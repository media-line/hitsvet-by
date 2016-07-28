<?php
/**
 *
 * Realex payment plugin
 *
 * @author Valérie Isaksen
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


class RealexHelperRealexRedirect extends RealexHelperRealex {


	function __construct ($method, $plugin) {
		parent::__construct($method, $plugin);


	}

	public function confirmedOrder (&$postRequest) {
		$selectedCCParams = array();
		if (!$this->doRealvault($selectedCCParams)) {
			$response = $this->sendPostRequest();
			$postRequest = true;
		} else {
			$response = $this->realvaultReceiptIn($selectedCCParams);
		}
		return $response;
	}


	function doRealVault (&$selectedCCParams) {
		$redirect_cc_selected = $this->customerData->getVar('redirect_cc_selected');
		$selectedCCParams = $this->getSelectedCCParams($redirect_cc_selected, $this->cart->virtuemart_paymentmethod_id);
		$doRealVault = false;
		if ($this->_method->realvault and !empty($selectedCCParams)) {
			if (!$selectedCCParams->addNew) {
				$doRealVault = true;
			}
		}
		$this->debugLog((int)$doRealVault, 'Realex doRealVault:', 'debug');
		return $doRealVault;
	}

	function sendPostRequest () {
		$post_variables = $this->getPostVariables();

		$jump_url = $this->getJumpUrl();


		// add spin image
		$html = '<html><head><title>Redirection</title></head><body><div style="margin: auto; text-align: center;">';
		if ($this->_method->debug) {
			$html .= '<form action="' . $jump_url . '" method="post" name="vm_realex_form" target="realex">';
		} else {
			$html .= '<form action="' . $jump_url . '" method="post" name="vm_realex_form" accept-charset="UTF-8">';
		}
		$html .= '<input type="hidden" name="charset" value="utf-8">';

		foreach ($post_variables as $name => $value) {
			$html .= '<input type="hidden" name="' . $name . '" value="' . $value . '" />';
		}

		if ($this->_method->debug) {

			$html .= '<div style="background-color:red;color:white;padding:10px;">
						<input type="submit"  value="The method is in debug mode. Click here to be redirected to Realex" />
						</div>';
			$this->debugLog($post_variables, 'sendPostRequest:', 'debug');

		} else {

			$html .= '<input type="submit"  value="' . JText::_('VMPAYMENT_REALEX_REDIRECT_MESSAGE') . '" />
					<script type="text/javascript">';
			$html .= '		document.vm_realex_form.submit();';
			$html .= '	</script>';
		}
		$html .= '</form></div>';
		$html .= '</body></html>';

		return $html;
	}

	function getPostVariables () {

		$BT = $this->order['details']['BT'];
		$ST = ((isset($this->order['details']['ST'])) ? $this->order['details']['ST'] : $this->order['details']['BT']);


		// prepare postdata
		$post_variables = array();
		$post_variables['MERCHANT_ID'] = $this->_method->merchant_id;
		$post_variables['ACCOUNT'] = $this->_method->subaccount;
		$post_variables['ORDER_ID'] = $BT->order_number;
		$post_variables['AMOUNT'] = $this->getTotalInPaymentCurrency();
		$post_variables['CURRENCY'] = $this->getPaymentCurrency();
		$post_variables['LANG'] = $this->getPaymentLang();
		$post_variables['TIMESTAMP'] = $this->getTimestamp();
		$post_variables['DCC_ENABLE'] = $this->_method->dcc;

		$post_variables['COMMENT1'] = $this->setComment1();
		//$post_variables['COMMENT2'] = $this->setComment2();

		$post_variables['MERCHANT_RESPONSE_URL'] = JURI::root() . 'index.php?option=com_virtuemart&format=raw&view=pluginresponse&task=pluginnotification&notificationTask=handleRedirect&tmpl=component';
		$post_variables['AUTO_SETTLE_FLAG'] = $this->_method->settlement;
		if ($BT->virtuemart_user_id != 0) {
			$post_variables['VAR_REF'] = $BT->customer_number;
			$post_variables['PAYER_EXIST'] = 0;
			$post_variables['CARD_STORAGE_ENABLE'] = $this->_method->realvault;
			if ($this->_method->realvault) {
				if ($this->customerData->getVar('redirect_cc_selected') == -1) {
					$post_variables['PAYER_EXIST'] = 1;
					$post_variables['PAYER_REF'] = $this->getExistingPayerRef();;
					$post_variables['PMT_REF'] = '';
				} else {
					$post_variables['PAYER_REF'] = '';
					$post_variables['PAYER_EXIST'] = 0;
					$post_variables['PMT_REF'] = '';

				}
				$post_variables['OFFER_SAVE_CARD'] = $this->_method->offer_save_card;

			} else {
				$post_variables['OFFER_SAVE_CARD'] = 0;
			}
		}
		if ($this->_method->card_payment_button) {
			$post_variables['CARD_PAYMENT_BUTTON'] = $this->getCardPaymentButton($this->_method->card_payment_button);
		}

		if ($this->_method->offer_save_card  and $BT->virtuemart_user_id != 0) {
			$post_variables['SHA1HASH'] = $this->getSha1Hash($this->_method->shared_secret, $post_variables['TIMESTAMP'], $post_variables['MERCHANT_ID'], $post_variables['ORDER_ID'], $post_variables['AMOUNT'], $post_variables['CURRENCY'], $post_variables['PAYER_REF'], $post_variables['PMT_REF']);
		} else {
			$post_variables['SHA1HASH'] = $this->getSha1Hash($this->_method->shared_secret, $post_variables['TIMESTAMP'], $post_variables['MERCHANT_ID'], $post_variables['ORDER_ID'], $post_variables['AMOUNT'], $post_variables['CURRENCY']);
		}

		// use_tss? if uk
		if ($this->_method->tss) {
			$post_variables['RETURN_TSS'] = 1; // Transaction Suitability Score
			// <digits from postcode>|<digits from address>
			$post_variables['BILLING_CODE'] = $this->stripnonnumeric($BT->zip);
			$post_variables['BILLING_CO'] = ShopFunctions::getCountryByID($BT->virtuemart_country_id, 'country_2_code');

			$post_variables['SHIPPING_CODE'] = $this->stripnonnumeric($ST->zip);
			$post_variables['SHIPPING_CO'] = ShopFunctions::getCountryByID($ST->virtuemart_country_id, 'country_2_code');

		}
		return $post_variables;

	}

	function  getExistingPayerRef () {
		$storedCCs = $this->getStoredCCsByPaymentMethod(JFactory::getUser()->id, $this->_method->virtuemart_paymentmethod_id);
		if (is_array($storedCCs)) {
			return $storedCCs[0]->realex_saved_payer_ref;
		}
		return NULL;

	}

	function  getExistingPmtRef () {
		$storedCCs = $this->getStoredCCsByPaymentMethod(JFactory::getUser()->id, $this->_method->virtuemart_paymentmethod_id);
		if (is_array($storedCCs)) {
			return $storedCCs[0]->realex_saved_payer_ref;
		}
		return NULL;

	}


	/**
	 * @param bool $enqueueMessage
	 * @return bool
	 */

	function validateConfirmedOrder ($enqueueMessage = true) {

		if ($this->_method->realvault) {
			if ($storedCCs = $this->getStoredCCsByPaymentMethod(JFactory::getUser()->id, $this->_method->virtuemart_paymentmethod_id)) {
				$redirect_cc_selected = $this->customerData->getVar('redirect_cc_selected');
				if ($this->customerData->getVar('selected_method')  and empty($redirect_cc_selected)) {
					vmInfo('VMPAYMENT_REALEX_PLEASE_SELECT_OPTION');
					return false;
				}
			}
		}
		return true;

	}

	/**
	 * @param bool $enqueueMessage
	 * @return bool
	 */
	public function validate ($enqueueMessage = true) {
		if ($this->_method->realvault) {
			if ($storedCCs = $this->getStoredCCsByPaymentMethod(JFactory::getUser()->id, $this->_method->virtuemart_paymentmethod_id)) {
				$redirect_cc_selected = $this->customerData->getVar('redirect_cc_selected');
				if ($this->customerData->getVar('selected_method') AND empty($redirect_cc_selected)) {
					vmInfo('VMPAYMENT_REALEX_PLEASE_SELECT_OPTION');
					return false;
				}
			}
		}
		return true;
	}

	/**
	 * @param bool $enqueueMessage
	 * @return bool
	 */
	public function validateSelectCheckPayment ($enqueueMessage = true) {
		return $this->validate();
	}

	/**
	 * @return bool
	 */
	function validateCheckoutCheckDataPayment () {
		return $this->validate();
	}

	function getExtraPluginInfo () {
		$extraPluginInfo = array();
		$redirect_cc_selected = $this->customerData->getVar('redirect_cc_selected');
		if ($redirect_cc_selected != -1) {
			$selected_cc = $this->getSelectedCCParams($redirect_cc_selected);
			if (!empty($selected_cc)) {
				$extraPluginInfo['cc_type'] = $selected_cc->realex_saved_pmt_type;
				$extraPluginInfo['cc_number'] = $selected_cc->realex_saved_pmt_digits;
				$extraPluginInfo['cc_name'] = $selected_cc->realex_saved_pmt_name;

				$extraPluginInfo['cc_expire_month'] = "";
				$extraPluginInfo['cc_expire_year'] = "";
			}
		} else {
			$extraPluginInfo['cc_number'] = vmText::_('VMPAYMENT_REALEX_USE_ANOTHER_CC');
		}


		return $extraPluginInfo;
	}

	/**
	 * Validate the response hash from Realex.
	 * timestamp.merchantid.orderid.amount.curr.payerref.pmtref
	 */
	function validateResponseHash ($post) {
		$post=$_POST;
		if (is_array($post)) {
			if ($post['RESULT'] == self::RESPONSE_CODE_SUCCESS) {
				$hash = $this->getSha1Hash($this->_method->shared_secret, $post['TIMESTAMP'], $this->_method->merchant_id, $post['ORDER_ID'], $post['RESULT'], $post['MESSAGE'], isset($post['PASREF']) ? $post['PASREF'] : "", isset($post['AUTHCODE']) ? $post['AUTHCODE'] : "");
			} else {
				$hash = $this->getSha1Hash($this->_method->shared_secret, $post['TIMESTAMP'], $this->_method->merchant_id, $post['ORDER_ID'], $post['RESULT'], $post['MESSAGE'], isset($post['PASREF']) ? $post['PASREF'] : "");

			}
			if ($hash != $post['SHA1HASH']) {
				$this->displayError(vmText::sprintf('VMPAYMENT_REALEX_ERROR_WRONG_HASH', $hash,$post['SHA1HASH'] ));
				echo vmText::sprintf('VMPAYMENT_REALEX_ERROR_WRONG_HASH', $hash, $post['SHA1HASH']);
				print_r($_POST);
				return FALSE;
			}
		} else {
			return parent::validateResponseHash($post);
		}


		return true;
	}

	function setComment1 () {
		$amountValue = vmPSPlugin::getAmountInCurrency($this->order['details']['BT']->order_total, $this->order['details']['BT']->order_currency);
		$shop_name = $this->getVendorInfo('vendor_store_name');
		return vmText::sprintf('VMPAYMENT_REALEX_COMMENT1', $amountValue['display'], $this->order['details']['BT']->order_number, $shop_name);
	}

	function setComment2 () {
		$shop_name = $this->getVendorInfo('vendor_store_name');
		return vmText::sprintf('VMPAYMENT_REALEX_NOTIFY_RETURN_URL', $shop_name);
	}

	/**
	 * JumpUrl is a prefined URL that must be configurated in Realex
	 * @return string
	 */
	function getJumpUrl () {
		return JURI::root(false) . 'plugins/vmpayment/realex/jump.php';

	}
}