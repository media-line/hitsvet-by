<?php
/**
 *
 * Realex payment plugin
 *
 * @author Valerie Isaksen
 * @version $Id$
 * @package VirtueMart
 * @subpackage payment
 * Copyright (C) 2004-2014 Virtuemart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
 *
 * http://virtuemart.net
 */


defined('_JEXEC') or die('Restricted access');

class RealexHelperCustomerData {
	const REALEX_FOLDERNAME = "realex";
	const REALEX_SESSION = "RealexCustomerData";
	private $_cc_name = '';
	private $_cc_type = '';
	private $_cc_number = '';
	private $_cc_cvv = '';
	private $_cc_expire_month = '';
	private $_cc_expire_year = '';
	private $_cc_valid = false;
	private $_selected_method = '';
	private $_redirect_cc_selected = '';
	private $_save_card = '';


	public function load () {

		//$this->_clear();
		/* TODO
		$store = 'none';
		$options['expire']= 60* 60;
		$session = JFactory::getSession($store,$options);
		*/
		$session = JFactory::getSession();
		$sessionData = $session->get(self::REALEX_SESSION, 0, 'vm');

		if (!empty($sessionData)) {
			$data = unserialize($sessionData);
			$this->_redirect_cc_selected = $data->redirect_cc_selected;
			$this->_save_card = $data->save_card;
			$this->_selected_method = $data->selected_method;
			$this->_cc_type = $data->cc_type;
			$this->_cc_name = $data->cc_name;
			$this->_cc_number = $this->decrypt($data->cc_number);
			$this->_cc_cvv = $data->cc_cvv;
			$this->_cc_expire_month = $data->cc_expire_month;
			$this->_cc_expire_year = $data->cc_expire_year;
			$this->_cc_valid = $data->cc_valid;
		}

	}

	public function loadPost () {

		$this->_selected_method =vmRequest::getInt('virtuemart_paymentmethod_id', 0);
		$this->_save_card =vmRequest::getInt('save_card', 0);
		$redirect_cc_selected =vmRequest::getInt('redirect_cc_selected_'.$this->_selected_method, 0);
		if ($redirect_cc_selected) {
			$this->_redirect_cc_selected = $redirect_cc_selected;
		}
		$cctype = vmRequest::getString('cc_type', '');
		if ($cctype) {
			$this->_cc_type = $cctype;
		}
		$cc_name = vmRequest::getVar('cc_name', '');
		if ($cc_name) {
			$this->_cc_name = $cc_name;
		}

		$cc_number = vmRequest::getString('cc_number', '');
		if ($cc_number) {
			$this->_cc_number = $cc_number;
		}

		$cc_cvv = vmRequest::getInt('cc_cvv', '');
		if ($cc_cvv) {
			$this->_cc_cvv = $cc_cvv;
		}

		$cc_expire_month = vmRequest::getInt('cc_expire_month', '');
		if ($cc_expire_month) {
			$this->_cc_expire_month = $cc_expire_month;
		}

		$cc_expire_year = vmRequest::getInt('cc_expire_year', '');
		if ($cc_expire_year) {
			$this->_cc_expire_year = $cc_expire_year;
		}
		$this->save();

	}

	public function save () {
		$session = JFactory::getSession();
		$sessionData = new stdClass();
		$sessionData->selected_method = $this->_selected_method;
		$sessionData->redirect_cc_selected = $this->_redirect_cc_selected;
		$sessionData->save_card = $this->_save_card;
		// card information
		$sessionData->cc_type = $this->_cc_type;
		$sessionData->cc_name = $this->_cc_name;
		$sessionData->cc_number = $this->encrypt($this->_cc_number);
		$sessionData->cc_cvv = $this->_cc_cvv;
		$sessionData->cc_expire_month = $this->_cc_expire_month;
		$sessionData->cc_expire_year = $this->_cc_expire_year;
		$sessionData->cc_valid = $this->_cc_valid;
		$session->set(self::REALEX_SESSION, serialize($sessionData), 'vm');
	}

	public function getVar($var) {
		$this->load();
		return $this->{'_' . $var};
	}

	public function setVar($var, $val) {
		$this->{'_' . $var} = $val;
	}

	public function clear () {
		$session = JFactory::getSession();
		$session->clear(self::REALEX_SESSION, 'vm');
	}

	static function encrypt ($string) {

		$key = self::getKey();
		$crypt = new JCrypt(new JCryptCipherSimple, $key);
		return $crypt->encrypt($string);
	}

	static function decrypt ($string) {

		$key = self::getKey();
		$crypt = new JCrypt(new JCryptCipherSimple, $key);
		return $crypt->decrypt($string);
	}

	static function getKey () {
		jimport('joomla.utilities.simplecrypt');

		$privateKey = JApplication::getHash(JFactory::getUser()->id);
		$key = new JCryptKey('simple', $privateKey, $privateKey);
		return $key;

	}
}
