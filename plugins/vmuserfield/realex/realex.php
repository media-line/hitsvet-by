<?php

if (!defined('_JEXEC')) {
	die('Direct Access to ' . basename(__FILE__) . ' is not allowed.');
}

/**
 * @author Valerie Isaksen, Patrick Kohl
 * @package    VirtueMart
 * @subpackage Plugins
 * @copyright Copyright (C) 2012 iStraxx - All rights reserved.
 * @license license.txt Proprietary License. This code belongs to iStraxx UG
 * You are not allowed to distribute or sell this code. You bought only a license to use it for ONE virtuemart installation.
 * You are allowed to modify this code for your installation.
 */

if (!class_exists('vmUserfieldPlugin')) {
	require(JPATH_VM_PLUGINS . DS . 'vmuserfieldtypeplugin.php');
}
define('USERFIELD_REALEX', 1);
class plgVmUserfieldRealex extends vmUserfieldPlugin {

	var $varsToPush = array();

	const REALEX_FOLDERNAME = "realex";
	function __construct (& $subject, $config) {

		parent::__construct($subject, $config);

		$this->_loggable = TRUE;
		$this->tableFields = array_keys($this->getTableSQLFields());
		$this->_tablepkey = 'id';
		$this->_tableId = 'id';
		$this->setConfigParameterable('params', $this->varsToPush);
		$this->_userFieldName = 'realex';
	}

	/**
	 * @return string
	 */
	public function getVmPluginCreateTableSQL () {

		$db = JFactory::getDBO();
		$query = 'SHOW TABLES LIKE "%' . str_replace('#__', '', $this->_tablename) . '"';
		$db->setQuery($query);
		$result = $db->loadResult();
		$app = JFactory::getApplication();
		$tablesFields = 0;
		if ($result) {
			$SQLfields = $this->getTableSQLFields();
			$loggablefields = $this->getTableSQLLoggablefields();
			$tablesFields = array_merge($SQLfields, $loggablefields);
			$update[$this->_tablename] = array($tablesFields, array(), array());
			vmdebug(get_class($this) . ':: VirtueMart2 update ' . $this->_tablename);
			if (!class_exists('GenericTableUpdater')) {
				require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'tableupdater.php');
			}
			$updater = new GenericTableUpdater();
			$updater->updateMyVmTables($update);
			//	return FALSE;   //TODO enable this, when using vm version higher than 2.0.8F
		} else {
			return $this->createTableSQL('Userfield Realex Realvault Table', $tablesFields);
		}

	}

	/**
	 * @return array
	 */
	function getTableSQLFields () {

		$SQLfields = array(
			'id'                          => 'int(11) UNSIGNED NOT NULL AUTO_INCREMENT',
			'virtuemart_user_id'          => 'int(1) UNSIGNED',
			'virtuemart_paymentmethod_id' => 'mediumint(1) UNSIGNED',
			'realex_saved_payer_ref'      => 'char(50)',
			'realex_saved_pmt_type'       => 'varchar(20)',
			'realex_saved_pmt_ref'        => 'char(50)',
			'realex_saved_pmt_digits'     => 'varchar(128)',
			'realex_saved_pmt_name'       => 'char(255)',
		);
		return $SQLfields;
	}

	function plgVmDeclarePluginParamsUserfield ($type, $name, $id, &$data) {

		return $this->declarePluginParams($type, $name, $id, $data);
	}

	/**
	 * Create the table for this plugin if it does not yet exist.
	 * This functions checks if the called plugin is active one.
	 * When yes it is calling the standard method to create the tables
	 *
	 * @author Valérie Isaksen
	 *
	 */
	function plgVmOnStoreInstallPluginTable ($jplugin_name) {

		$return= $this->onStoreInstallPluginTable($jplugin_name);
		self::_createRealexFolder();

		$filename =self::_getRealexSafepath () .DS.'key.php';
		if (JFile::exists($filename)) {
			return $return;
		}

		$token = JUtility::getHash(JUserHelper::genRandomPassword());
		$salt = JUserHelper::getSalt('crypt-md5');
		$hashedToken = md5($token . $salt)  ;
		$key = base64_encode($hashedToken);
		$filecontents = "<?php  defined('USERFIELD_REALEX') or die();
	define('USERFIELD_REALEX_KEY', '".$key."');
	?>";
		$result = JFile::write($filename, $filecontents);

		if (!$result) {
			VmInfo($result);
		} else {
			// vmInfo('File ' . $filename . ' <strong style="color:red;">key stored in file</strong>');
			VmInfo(Jtext::sprintf('VMUSERFIELD_REALEX_KEY_STORED', $filename));

		}
		return $return ;
	}



	/**
	 * This method is fired when showing the order details in the frontend.
	 * It displays the shipment-specific data.
	 *
	 * @param integer $order_number The order Number
	 * @return mixed Null for shipments that aren't active, text (HTML) otherwise
	 * @author Valérie Isaksen
	 * @author Max Milbers
	 */

	public function plgVmOnUserfieldDisplay ($_prefix, $field, $userId, &$return) {

		if ('plugin' . $this->_name != $field->type) {
			return;
		}

		$return['fields'][$field->name]['formcode'] .= $this->onShowUserDisplayUserfield($userId, $field->name);

	}

	public function plgVmOnPaymentDisplay ($fieldtype, $userId, &$storedCreditCards) {

		if ('plugin' . $this->_name != $fieldtype) {
			return;
		}

		$storedCreditCards = $this->getStoredCreditCards($userId);


	}

	public function plgVmPrepareUserfieldDataSave ($fieldType, $fieldName, $post, &$value, $params) {

		if ('plugin' . $this->_name != $fieldType) {
			return;
		}

		$card_ids = JRequest::getVar('card_id', array(), 'post', 'array');
		if (!empty($card_ids)) {
			return $this->deleteStoredCards($card_ids);
		}


// we come from payment
		if (isset($params['realex_saved_payer_ref'])) {
			$params['realex_saved_pmt_digits'] = self::encrypt($params['realex_saved_pmt_digits']);
			$this->storePluginInternalData($params);
		}

	}

	/**
	 * Delete a stored card
	 * To remove a card from the RealVault system
	 */
	function deleteStoredCards ($card_ids) {
		foreach ($card_ids as $card_id) {

			JLoader::import('joomla.plugin.helper');
			JPluginHelper::importPlugin('vmpayment');
			$app = JFactory::getApplication();

			$storedCC = $this->getStoredCCInfo($card_id);
			$success = false;
			$app->triggerEvent('plgVmOnRealexDeletedStoredCard', array('realex', $storedCC, &$success));
			if ($success) {
				$db = JFactory::getDBO();
				$query = 'DELETE FROM `' . $this->_tablename . '` WHERE `id`=' . $card_id;
				$db->setQuery($query);
				$db->query();
				vmInfo('VMUSERFIELD_REALEX_CARD_DELETED');

			} else {
				$vendorId = 1;
				$vendor_link = JRoute::_('index.php?option=com_virtuemart&view=vendor&layout=contact&virtuemart_vendor_id=' . $vendorId);
				vmInfo(vmText::sprintf('VMUSERFIELD_REALEX_CARD_NOT_DELETED', $vendor_link));

			}

		}
	}

	function onShowUserDisplayUserfield ($userId, $fieldName) {
		if ($userId == 0) {
			return;
		}
		$display_fields = array('realex_saved_pmt_type', 'realex_saved_pmt_digits', 'realex_saved_pmt_name');


		$storedCreditCards = $this->getStoredCreditCards($userId);
		if (empty ($storedCreditCards)) {
			return vmText::_('VMUSERFIELD_REALEX_NO_CARD_SAVED');
		}


		if (!class_exists('VmHTML')) {
			require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'html.php');
		}
		if (JFactory::getApplication()->isSite()) {
			$html = $this->renderByLayout("creditcardlist", array("storedCreditCards" => $storedCreditCards));
		} else {
			$html = '<table  class="adminlist" width="50%">' . "\n";
			$i = 1;
			foreach ($storedCreditCards as $storedCreditCard) {
				$class = 'class="row' . $i . '"';
				$html .= '<tr class="row1"><td>' . JText::_('COM_VIRTUEMART_DATE') . '</td><td align="left">' . $storedCreditCard->created_on . '</td></tr>';
				$checked = JHTML::_('grid.id', $i, $storedCreditCard->id, null, 'card_id');
				$view=vmRequest::getCmd('view');
				if ($view =='user') {
					$html .= "<tr>\n<td>" . $checked . "</td>\n <td align='left'>" . 'DELETE' . "</td>\n</tr>\n";
				}

				foreach ($display_fields as $display_field) {
					$complete_key = strtoupper('VMUSERFIELD_' . $display_field);

					$value = $storedCreditCard->$display_field;
					$key_text = JText::_($complete_key);
					$value = JText::_($value);
					if (!empty($value)) {
						$html .= "<tr>\n<td>" . $key_text . "</td>\n <td align='left'>" . $value . "</td>\n</tr>\n";
					}
				}

			}

			$html .= '</table>' . "\n";

		}
		return $html;
	}

	private function getStoredCreditCards ($userId) {
		if (!($storedCreditCards = $this->_getInternalData($userId))) {
			return '';
		}
		foreach ($storedCreditCards as $storedCreditCard) {
			$storedCreditCard->realex_saved_pmt_digits = self::decrypt($storedCreditCard->realex_saved_pmt_digits);
		}
		return $storedCreditCards;

	}

	/**
	 * @param        $virtuemart_order_id
	 * @param string $order_number
	 * @return mixed|string
	 */
	function _getInternalData ($userId) {

		$db = JFactory::getDBO();
		$q = 'SELECT * FROM `' . $this->_tablename . '` WHERE ';
		$q .= ' `virtuemart_user_id` = "' . $userId . '"';
		$q .= ' ORDER BY `modified_on` DESC ';

		$db->setQuery($q);
		return $db->loadObjectList();

	}

	/**
	 * @param        $virtuemart_order_id
	 * @param string $order_number
	 * @return mixed|string
	 */
	function getStoredCCInfo ($card_id) {

		$db = JFactory::getDBO();
		$q = 'SELECT * FROM `' . $this->_tablename . '` ';
		$q .= ' WHERE `id`=' . $card_id;

		$db->setQuery($q);
		return $db->loadAssoc();

	}

	function  plgVmOnBeforeUserfieldSave ($plgName, &$data, &$tableClass) {

		if ($this->_name != $plgName) {
			return;
		}
		$vars = array();
		foreach ($this->varsToPush as $key => $var) {
			$vars[$key] = array($data['params'][$key], $var[1]);
		}
		$tableClass->setParameterable('params', $vars);
	}


	static function encrypt ($string) {

		$key = self::getKey ();
		return base64_encode (mcrypt_encrypt (MCRYPT_RIJNDAEL_256, md5 ($key), $string, MCRYPT_MODE_CBC, md5 (md5 ($key))));
	}

	static function decrypt ($string) {

		$key = self::getKey ();
		return rtrim (mcrypt_decrypt (MCRYPT_RIJNDAEL_256, md5 ($key), base64_decode ($string), MCRYPT_MODE_CBC, md5 (md5 ($key))), "\0");
	}

	static function getKey () {

		$filename = self::_getRealexSafepath () . DS . 'key.php';
		if (file_exists ($filename)) {
			include_once $filename;
		}

		return base64_decode (USERFIELD_REALEX_KEY);

	}
	static function _getRealexSafepath () {

		$safePath = VmConfig::get ('forSale_path', '');
		if (empty($safePath)) {
			return NULL;
		}
		$realexSafePath = $safePath . self::REALEX_FOLDERNAME;
		return $realexSafePath;
	}
	static function _createRealexFolder () {

		$folderName = self::_getRealexSafepath ();

		$exists = JFolder::exists ($folderName);
		if ($exists) {
			return TRUE;
		}
		$created = JFolder::create ($folderName);
		if ($created) {
			return TRUE;
		}

		$uri = JFactory::getURI ();
		$link = $uri->root () . 'administrator/index.php?option=com_virtuemart&view=config';
		VmError (JText::sprintf ('VMUSERFIELD_REALEX_CANNOT_STORE_CONFIG', $folderName, '<a href="' . $link . '">' . $link . '</a>', JText::_ ('COM_VIRTUEMART_ADMIN_CFG_MEDIA_FORSALE_PATH')));
		return FALSE;
	}

}

// No closing tag
