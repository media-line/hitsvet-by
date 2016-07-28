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


class  RealexHelperRealex {
	var $_method;
	var $cart;
	var $order;
	var $vendor;
	var $customerData;
	var $context;
	var $total;
	var $post_variables;
	var $post_string;
	var $requestData;
	var $response;
	var $currency_code_3;
	var $currency_display;
	var $plugin;

	const REQUEST_TYPE_AUTH = "auth";
	const REQUEST_TYPE_RECEIPT_IN = 'receipt-in';
	const REQUEST_TYPE_REBATE = "rebate";
	const REQUEST_TYPE_SETTLE = "settle";
	const REQUEST_TYPE_VOID = "void";
	const REQUEST_TYPE_3DS_VERIFYENROLLED = "3ds-verifyenrolled";
	const REQUEST_TYPE_3DS_VERIFYSIG = "3ds-verifysig";
	const REQUEST_TYPE_DCCRATE = "dccrate";
	const REQUEST_TYPE_REALVAULT_DCCRATE = "realvault-dccrate";
	const REQUEST_TYPE_CARD_CANCEL_CARD = "card-cancel-card";
	const REQUEST_TYPE_CARD_UPDATE_CARD = "card-update-card";
	const REQUEST_TYPE_PAYER_NEW = "payer-new";
	const REQUEST_TYPE_CARD_NEW = "card-new";

	const RESPONSE_DCC_CHOICE_YES = "Yes";
	const DCCRATE_RESULT_SUCCESS = '00';


	const RESPONSE_CODE_SUCCESS = '00';
	const RESPONSE_CODE_DECLINED = '101';
	const RESPONSE_CODE_REFERRAL_B = '102';
	const RESPONSE_CODE_REFERRAL_A = '103';
	const RESPONSE_CODE_INVALID_ORDER_ID = '501'; // This order ID has already been used - please use another one
	const RESPONSE_CODE_PAYER_REF_NOTEXIST = '501'; // This Payer Ref payerref does not exist
	const RESPONSE_CODE_INVALID_PAYER_REF_USED = '501'; // This Payer Ref payerref has already been used - please use another one
	const PAYER_SETUP_SUCCESS = "00";


	function __construct ($method, $plugin) {
		if ($method->dcc) {
			$method->settlement = 1;
			$method->rebate_password = "";
		}
		if (!$method->realvault) {
			$method->offer_save_card=0;
		}
		$this->_method = $method;
		$this->plugin = $plugin;
		$session = JFactory::getSession();
		$this->context = $session->getId();
	}

	public function setTotalInPaymentCurrency ($total) {

		if (!class_exists('CurrencyDisplay')) {
			require(JPATH_VM_ADMINISTRATOR . '/helpers/currencydisplay.php');
		}
		$this->total = vmPSPlugin::getAmountValueInCurrency($total, $this->_method->payment_currency) * 100;

		$cd = CurrencyDisplay::getInstance($this->cart->pricesCurrency);
	}

	public function getTotalInPaymentCurrency () {

		return $this->total;

	}

	public function setPaymentCurrency () {
		vmPSPlugin::getPaymentCurrency($this->_method);
		$this->currency_code_3 = shopFunctions::getCurrencyByID($this->_method->payment_currency, 'currency_code_3');
	}

	public function getPaymentCurrency () {
		return $this->currency_code_3;
	}

	public function setContext ($context) {
		$this->context = $context;
	}

	public function getContext () {
		return $this->context;
	}

	public function setCart ($cart) {
		$this->cart = $cart;
		if (!isset($this->cart->pricesUnformatted)) {
			$this->cart->getCartPrices();
		}
	}

	public function setOrder ($order) {
		$this->order = $order;
	}


	public function stripnonnumeric ($testString) {
		return preg_replace("/[^0-9]/", "", $testString);
	}

	function _getRealexUrl () {
		if ($this->_method->shop_mode == 'sandbox') {
			return 'https://realcontrol.sandbox.realexpayments.com';
		} else {
			return ' https://realcontrol.realexpayments.com';
		}

	}


	/*********************/
	/* Log and Reporting */
	/*********************/
	public function debug ($subject, $title = '', $echo = true) {

		$debug = '<div style="display:block; margin-bottom:5px; border:1px solid red; padding:5px; text-align:left; font-size:10px;white-space:nowrap; overflow:scroll;">';
		$debug .= ($title) ? '<br /><strong>' . $title . ':</strong><br />' : '';
		//$debug .= '<pre>';
		if (is_array($subject)) {
			$debug .= str_replace("=>", "&#8658;", str_replace("Array", "<font color=\"red\"><b>Array</b></font>", nl2br(str_replace(" ", " &nbsp; ", print_r($subject, true)))));
		} else {
			$debug .= str_replace("=>", "&#8658;", str_replace("Array", "<font color=\"red\"><b>Array</b></font>", (str_replace(" ", " &nbsp; ", print_r($subject, true)))));

		}

		//$debug .= '</pre>';
		$debug .= '</div>';
		if ($echo) {
			echo $debug;
		} else {
			return $debug;
		}
	}

	function highlight ($string) {
		return '<span style="color:red;font-weight:bold">' . $string . '</span>';
	}

	public function debugLog ($message, $title = '', $type = 'message', $echo = false, $doVmDebug = false) {

		//Nerver log the full credit card number nor the CVV code.
		if (is_array($message)) {
			if (array_key_exists('REALEX_SAVED_PMT_DIGITS', $message)) {
				$message['REALEX_SAVED_PMT_DIGITS'] = "**** **** **** " . substr($message['REALEX_SAVED_PMT_DIGITS'], -4);
			}
			if (array_key_exists('CVV2', $message)) {
				$message['CVV2'] = str_repeat('*', strlen($message['CVV2']));
			}
			if (array_key_exists('signature', $message)) {
				$message['signature'] = '**MASKED**';
			}
			if (array_key_exists('api_password', $message)) {
				$message['api_password'] = '**MASKED**';
			}
		}

		if ($this->_method->debug) {
			$this->debug($message, $title, true);
		}

		if ($echo) {
			echo $message . '<br/>';
		}


		$this->plugin->debugLog($message, $title, $type, $doVmDebug);
	}

	function getPaymentLang () {
		$available_languages = array(
			'en-GB' => 'en',
			'es-ES' => 'es',
		);
		$default_language = 'en';

		$language = JFactory::getLanguage();

		if (array_key_exists($language->getTag(), $available_languages)) {
			return $available_languages[$language->getTag()];
		} else {
			return $default_language;
		}
	}

	function getCardPaymentButton ($card_payment_button) {
		$lang = JFactory::getLanguage();
		if ($lang->hasKey($card_payment_button)) {
			return vmText::_($card_payment_button);
		} else {
			return $card_payment_button;
		}
	}

	/**
	 * @param $response
	 */

	function manageResponseRequest ($response) {
		if ($response == NULL) {
			$this->plugin->redirectToCart(vmText::_('VMPAYMENT_REALEX_ERROR_TRY_AGAIN'));
		}
		if (!$this->validateResponseHash($response)) {
			$this->plugin->redirectToCart(vmText::_('VMPAYMENT_REALEX_ERROR_TRY_AGAIN'));
		}
		$this->plugin->_storeRealexInternalData($response, $this->_method->virtuemart_paymentmethod_id, $this->order['details']['BT']->virtuemart_order_id, $this->order['details']['BT']->order_number, $this->request_type);

		$xml_response = simplexml_load_string($response);

		$success = $this->isResponseSuccess($xml_response);

		if (!$success) {
			$error = (string)$xml_response->message . " (" . (string)$xml_response->result . ")";
			$this->displayError($error);
			$this->plugin->redirectToCart(vmText::_('VMPAYMENT_REALEX_ERROR_TRY_AGAIN'));
		}
	}

	/**
	 * @param $response
	 */
	function manageResponseRequestReceiptIn ($response) {
		$this->manageResponseRequest($response);
	}

	public function validateConfirmedOrder ($enqueueMessage = true) {
		return true;
	}
	public function validateSelectCheckPayment ($enqueueMessage = true) {
		return true;
	}
	/**
	 * @param bool $enqueueMessage
	 * @return bool
	 */
	function validateCheckoutCheckDataPayment ( ) {
		return true;
	}
	function getOrderBEFields () {
		$showOrderBEFields = array(

			'RESULT'            => 'RESULT',
			'PASREF'            => 'PASREF',
			'AUTHCODE'          => 'AUTHCODE',
			'MESSAGE'           => 'MESSAGE',
			'TSS'               => 'TSS',
			'CAVV'              => 'CAVV',
			'CVNRESULT'         => 'CVNRESULT',
			'AVSPOSTCODERESULT' => 'AVSPOSTCODERESULT',
			'DCCCHOICE'         => 'DCCCHOICE',
			'REALWALLET_CHOSEN' => 'REALWALLET_CHOSEN',

		);


		return $showOrderBEFields;
	}


	function getOrderBEFieldsDcc () {
		$showOrderBEFields = array(

			'DCCRATE'                        => 'DCCRATE',
			'DCCMERCHANTAMOUNT'              => 'DCCMERCHANTAMOUNT',
			'DCCMERCHANTCURRENCY'            => 'DCCMERCHANTCURRENCY',
			'DCCCARDHOLDERAMOUNT'            => 'DCCCARDHOLDERAMOUNT',
			'DCCCARDHOLDERCURRENCY'          => 'DCCCARDHOLDERCURRENCY',
			'DCCMARGINRATEPERCENTAGE'        => 'DCCMARGINRATEPERCENTAGE',
			'DCCEXCHANGERATESOURCENAME'      => 'DCCEXCHANGERATESOURCENAME',
			'DCCCOMMISSIONPERCENTAGE'        => 'DCCCOMMISSIONPERCENTAGE',
			'DCCEXCHANGERATESOURCETIMESTAMP' => 'DCCEXCHANGERATESOURCETIMESTAMP',

		);


		return $showOrderBEFields;
	}

	function getOrderBEFields3DS () {
		$showOrderBEFields = array(

			'status' => 'status',

		);


		return $showOrderBEFields;
	}

	function onShowOrderBEPayment ($data, $format) {

		$showOrderBEFields = $this->getOrderBEFields();
		$prefix = 'REALEX_RESPONSE_';

		$html = '';


		foreach ($showOrderBEFields as $key => $showOrderBEField) {
			if ($format == "xml") {
				$showOrderBEField = strtolower($showOrderBEField);
			}
			if (isset($data->$showOrderBEField) and !empty($data->$showOrderBEField)) {
				$key = $prefix . $key;
				$html .= $this->plugin->getHtmlRowBE($key, $data->$showOrderBEField);
			}

		}

		$plugin = $this->plugin;
		/*
		if (isset($data->DCCCHOICE) and $data->DCCCHOICE == $plugin::DCCCHOICE_YES) {
			$showOrderBEFields = $this->getOrderBEFieldsDcc();
			$prefix = 'REALEX_RESPONSE_';
			foreach ($showOrderBEFields as $key => $showOrderBEField) {

				if (isset($data->$showOrderBEField)) {
					$key = $prefix . $key;
					$html .= $this->plugin->getHtmlRowBE($key, $data->$showOrderBEField);
				}

			}
		}
		*/
		if (isset($data->threedsecure)) {
			$showOrderBEFields = $this->getOrderBEFields3DS();
			$prefix = 'REALEX_RESPONSE_THREEDSECURE_';
			foreach ($showOrderBEFields as $key => $showOrderBEField) {
				if (isset($data->threedsecure->$showOrderBEField)) {
					$key = $prefix . $key;
					$html .= $this->plugin->getHtmlRowBE($key, $data->threedsecure->$showOrderBEField);
				}

			}
		}
		return $html;
	}
	/**
	 * @param $response
	 * @param $order
	 * @return null|string
	 */
	 function getResponseHTML ($payments) {

		$payment_name = $this->plugin->renderPluginName($this->_method, 'order');
		$dcc_info = "";
		$payer_info = "";
		$auth_info = "";
		if ($payments) {
			foreach ($payments as $payment) {
				if (isset($payment->realex_fullresponse) and !empty($payment->realex_fullresponse)) {
					if ($payment->realex_fullresponse_format == 'xml') {
						$xml_response = simplexml_load_string($payment->realex_fullresponse);
						if ($payment->realex_request_type_response == $this::REQUEST_TYPE_AUTH OR $payment->realex_request_type_response == $this::REQUEST_TYPE_RECEIPT_IN) {
							$success = $this->isResponseSuccess($xml_response);

							if ($success) {
								if (isset($xml_response->dccinfo) AND isset($xml_response->dccinfo->cardholderrate)) {
									if ($xml_response->dccinfo->cardholderrate != 1.0) {
										$dcc_info = vmText::sprintf('VMPAYMENT_REALEX_DCC_PAY_OWN_CURRENCY_CHARGED', $this->plugin->getCardHolderAmount($xml_response->dccinfo->merchantamount), $xml_response->dccinfo->merchantcurrency, $this->plugin->getCardHolderAmount($xml_response->dccinfo->cardholderamount), $xml_response->dccinfo->cardholdercurrency);
									} else {
										$dcc_info = vmText::sprintf('VMPAYMENT_REALEX_DCC_PAY_MERCHANT_CURRENCY', $this->plugin->getCardHolderAmount($xml_response->dccinfo->merchantamount), $xml_response->dccinfo->merchantcurrency);
									}
								}
								$amountValue = vmPSPlugin::getAmountInCurrency($this->order['details']['BT']->order_total, $this->order['details']['BT']->order_currency);

								$auth_info = vmText::sprintf('VMPAYMENT_REALEX_PAYMENT_STATUS_CONFIRMED', $amountValue['display'], $this->order['details']['BT']->order_number);
								$pasref = $payment->realex_response_pasref;
							} else {
								$auth_info = JText::sprintf('VMPAYMENT_REALEX_PAYMENT_STATUS_CANCELLED', $this->order['details']['BT']->order_number);
							}

						} elseif ($payment->realex_request_type_response == $this::REQUEST_TYPE_CARD_NEW) {
							$success = $this->isResponseSuccess($xml_response);
							if ($success) {
								$payer_info = vmText::_('VMPAYMENT_REALEX_CARD_STORAGE_SUCCESS');
							} else {
								$payer_info = vmText::_('VMPAYMENT_REALEX_CARD_STORAGE_FAILED');
							}
						}

					} else {
						if ($payment->realex_fullresponse_format == 'json') {
							$realex_data = json_decode($payment->realex_fullresponse);
							$result = $payment->realex_response_result;

							$success = ($result == $this::RESPONSE_CODE_SUCCESS);
							if ($success) {
								$amountValue = vmPSPlugin::getAmountInCurrency($this->order['details']['BT']->order_total, $this->order['details']['BT']->order_currency) ;
								$auth_info = vmText::sprintf('VMPAYMENT_REALEX_PAYMENT_STATUS_CONFIRMED', $amountValue['display'], $this->order['details']['BT']->order_number);
								if (isset($realex_data->DCCCHOICE) and $realex_data->DCCCHOICE == $this::RESPONSE_DCC_CHOICE_YES) {
									$dcc_info = vmText::sprintf('VMPAYMENT_REALEX_DCC_PAY_OWN_CURRENCY_CHARGED', $this->plugin->getCardHolderAmount($realex_data['DCCMERCHANTAMOUNT']), $realex_data['DCCMERCHANTCURRENCY'], $this->plugin->getCardHolderAmount($realex_data['DCCCARDHOLDERAMOUNT']), $realex_data['DCCCARDHOLDERCURRENCY']);
								}
								if (isset($realex_data->REALWALLET_CHOSEN) and  $realex_data->REALWALLET_CHOSEN == 1) {
									$payer_info = vmText::_('VMPAYMENT_REALEX_CARD_STORAGE_SUCCESS');
								}
								$pasref = $realex_data->PASREF;


							} else {
								$auth_info = JText::sprintf('VMPAYMENT_REALEX_PAYMENT_STATUS_CANCELLED',  $this->order['details']['BT']->order_number);
							}
						}
					}
				}
			}
		}
		VmConfig::loadJLang('com_virtuemart_orders', TRUE);


		$html = $this->plugin->renderByLayout('response', array(
		                                                       "success"      => $success,
		                                                       "payment_name" => $payment_name,
		                                                       "dcc_info"     => $dcc_info,
		                                                       "auth_info"    => $auth_info,
		                                                       "payer_info"   => $payer_info,
		                                                       "pasref"       => $pasref,
		                                                       "order_number" => $this->order['details']['BT']->order_number,
		                                                       "order_pass" => $this->order['details']['BT']->order_pass,
		                                                  ));
		return $html;


	}
	public function loadCustomerData () {
		if (!class_exists('RealexHelperCustomerData')) {
			require(JPATH_SITE . '/plugins/vmpayment/realex/realex/helpers/customerdata.php');
		}
		$this->getCustomerData();
		$this->customerData->loadPost();
	}
	public function getCustomerData () {
		if (!class_exists('RealexHelperCustomerData')) {
			require(JPATH_SITE . '/plugins/vmpayment/realex/realex/helpers/customerdata.php');
		}
		$this->customerData = new RealexHelperCustomerData();
		$this->customerData->load();
	}
	static function getRealexCreditCards () {
		return array(
			'AMEX',
			'DINERS',
			'LASER',
			'MC',
			'VISA',
			'SWITCH',
		);

	}

	function getVendorInfo ($field) {

		$virtuemart_vendor_id = 1;
		$vendorModel = VmModel::getModel('vendor');
		$vendor = $vendorModel->getVendor($virtuemart_vendor_id);

		return $vendor->$field;

	}

	// todo
	/**
	 * the reference to use for the payment method saved. If this field is not present an alphanumeric reference will be automatically generated.
	 * @return string
	 */
	function getPmtRef () {
		return '';
	}

	// todo
	/**
	 * This field contains the payer reference used for this cardholder. If this field is empty or missing and PAYER_EXIST = 0, then a PAYER_REF will be automatically generated.
	 * To add another card to an existing payer the PAYER_REF field should be set to their existing payer reference.
	 * This field is mandatory if the CARD_STORAGE_ENABLE is set to 1 and the PAYER_EXIST flag is set to 1. If PAYER_EXIST = 1 and CARD_STORAGE_ENABLE = 1, a 5xx error will be returned if the field is empty or missing:
	 * 5xx “Mandatory field missing. PAYER_REF not present in request”     * @return string
	 */
	function getPayerExist () {
		return 0;
	}

	function   getPayerRef ($payer_exist) {

	}

	function getSelectedCC ($storedCCs, $selected) {
		if (empty($storedCCs)) {
			return NULL;
		}
		foreach ($storedCCs as $storedCC) {
			if ($storedCC->id == $selected) {
				return $storedCC;
			}
		}

	}


	function getSelectedCCParams ($selected_cc_id) {


		if ($selected_cc_id == -1) {
			$selectedCCParams = new stdClass();
			$selectedCCParams->addNew = true;
			return $selectedCCParams;
		}
		$storedCCs = $this->getStoredCCs(JFactory::getUser()->id);

		$selectedCC = $this->getSelectedCC($storedCCs, $selected_cc_id);
		if ($selectedCC == NULL) {
			return NULL;
		}
		$selectedCCParams = $selectedCC;
		$selectedCCParams->addNew = false;
		return $selectedCCParams;
	}

	static $_storedCCsCache;

	function getStoredCCs ($virtuemart_user_id) {

		if (!empty(self::$_storedCCsCache)) {
			if (isset(self::$_storedCCsCache[$virtuemart_user_id])) {
				$storedCCs = self::$_storedCCsCache[$virtuemart_user_id]['storedCC'];
				return $storedCCs;
			}
		}


		JLoader::import('joomla.plugin.helper');
		JPluginHelper::importPlugin('vmuserfield');
		$app = JFactory::getApplication();

		$storedCCs = "";
		$app->triggerEvent('plgVmOnPaymentDisplay', array('pluginrealex', $virtuemart_user_id, &$storedCCs));

		if (empty(self::$_storedCCsCache)) {
			self::$_storedCCsCache = array();
		}
		if (empty(self::$_storedCCsCache[$virtuemart_user_id])) {
			self::$_storedCCsCache[$virtuemart_user_id] = array();
		}
		self::$_storedCCsCache[$virtuemart_user_id]['storedCC'] = $storedCCs;


		return $storedCCs;

	}

	function getStoredCCsByPaymentMethod ($virtuemart_user_id, $virtuemart_paymentmethod_id) {
		$storedCCsByPaymentMethod = array();
		$storeCCs = $this->getStoredCCs($virtuemart_user_id);
		if ($storeCCs) {
			foreach ($storeCCs as $storeCC) {
				if ($storeCC->virtuemart_paymentmethod_id == $virtuemart_paymentmethod_id) {
					$storedCCsByPaymentMethod[] = $storeCC;
				}
			}
		}

		return $storedCCsByPaymentMethod;
	}

	/**
	 * @param $virtuemart_paymentmethod_id
	 * @param $virtuemart_user_id
	 * @param $selected_cc
	 * @return mixed|null
	 */
	function getCCDropDown ($virtuemart_paymentmethod_id, $virtuemart_user_id, $selected_cc, $use_another_cc = true) {

		$storeCCs = $this->getStoredCCsByPaymentMethod($virtuemart_user_id, $virtuemart_paymentmethod_id);
		if (empty($storeCCs)) {
			return null;
		}
		$attrs = 'class="inputbox vm-chzn-select"';
		$idA = $id = 'redirect_cc_selected_' . $virtuemart_paymentmethod_id;
		$options[] = array('value' => '', 'text' => vmText::_('VMPAYMENT_REALEX_PLEASE_SELECT'));
		if ($use_another_cc) {
			$options[] = JHTML::_('select.option', -1, vmText::_('VMPAYMENT_REALEX_USE_ANOTHER_CC'));
		}

		foreach ($storeCCs as $storeCC) {
			$cc_type = vmText::_('VMPAYMENT_REALEX_CC_' . $storeCC->realex_saved_pmt_type);
			$name = $cc_type . ' ' . $storeCC->realex_saved_pmt_digits . ' (' . $storeCC->realex_saved_pmt_name . ')';
			$options[] = JHTML::_('select.option', $storeCC->id, $name);
		}

		return JHTML::_('select.genericlist', $options, $idA, 'class="inputbox vm-chzn-select" style="width: 250px;"', 'value', 'text', $selected_cc);
	}

	function getOfferSaveCard ($checked, $paymentmethod_id) {
		$param = '';
		if ($checked) {
			$param = "checked";
		}
		$id = 'realex_offersavecard_' . $paymentmethod_id;
		$html = vmText::_('VMPAYMENT_REALEX_OFFERSAVECARD');
		$html .= "<br />";
		$html .= '<input type="checkbox" name="' . $id . '" id="' . $id . '" value="1" ' . $param . '/>';
		$html .= ' <label for="' . $id . '">' . vmText::_('VMPAYMENT_REALEX_OFFERSAVECARD_YES') . '</label>';
		return $html;
	}

	function doRealVault (&$selectedCCParams) {
		if ($this->_method->realvault AND $this->_method->integration == 'remote') {
			if (!($this->_method->offer_save_card) OR
				($this->_method->offer_save_card AND $this->customerData->getVar('save_card') )
			) {
				return true;
			}
			return false;
		}
		return false;
	}

	/**
	 * @return string
	 */
	function getRemoteURL () {
		return "https://epage.payandshop.com/epage-remote-plugins.cgi";

	}

	/**
	 * @return string
	 */

	function getPaymentButton () {
		if (empty($this->_method->card_payment_button)) {
			$card_payment_button = vmText::_('VMPAYMENT_REALEX_DCC_PAY_NOW');
		} else {
			$card_payment_button = $this->_method->card_payment_button;
		}
		return $card_payment_button;
	}

	/**
	 * @param      $selectedCCParams
	 * @param bool $ask_dcc
	 * @param bool $set_dcc
	 * @return bool|mixed
	 */
	public function requestReceiptIn ($selectedCCParams, $ask_dcc = true, $set_dcc = false) {
		$xm = new stdClass();

		$timestamp = $this->getTimestamp();

		$xml_request = $this->setHeader($timestamp, self::REQUEST_TYPE_RECEIPT_IN);
		if (!empty($selectedCCParams->realex_saved_cvn)) {
			$xml_request .= '<paymentdata>
					<cvn>
						<number>' . $selectedCCParams->realex_saved_cvn . '</number>
					</cvn>
				</paymentdata>
				';
		}
		if ($this->_method->dcc) {
			$xml_request .= '<autosettle flag="1" />
			';
		} else {
			$xml_request .= '<autosettle flag="' . $this->_method->settlement . '" />
			';
		}
		if (isset($xm->eci) && !empty($xm->eci)) {
			$xml_request .= '<mpi>';
			if (isset($xm->cavv) && !empty($xm->cavv) && isset($xm->xid) && !empty($xm->xid)) {
				$xml_request .= '<cavv>' . $xm->cavv . '</cavv>
				<xid>' . $xm->xid . '</xid>';
			}
			if (isset($xm->eci) && !empty($xm->eci)) {
				$xml_request .= '<eci>' . $xm->eci . '</eci></mpi>';
			}
		}
		$xml_request .= '<payerref>' . $selectedCCParams->realex_saved_payer_ref . '</payerref>
		<paymentmethod>' . $selectedCCParams->realex_saved_pmt_ref . '</paymentmethod>';
		if ($set_dcc) {
			$xml_request .= '
			<dccinfo>
				<ccp>' . $xm->dcc . '</ccp>
				<type>1</type>
				<rate>' . $xm->cardholderrate . '</rate>
				<ratetype>S</ratetype>
				<amount currency="' . $xm->cardholdercurrency . '">' . $xm->cardholderamount . '</amount>
			</dccinfo>';
		}
		$xml_request .= '<md5hash />
		';
		$hash = $this->getSha1Hash($this->_method->shared_secret, $timestamp,  $this->_method->merchant_id,$this->order['details']['BT']->order_number, $this->getTotalInPaymentCurrency(), $this->getPaymentCurrency(), $selectedCCParams->realex_saved_payer_ref);
		$xml_request .= $this->setSha1($hash);
		$xml_request .= $this->setComments();
		$xml_request .= $this->setTSSinfo();
		$xml_request .= '</request>';


		$response = $this->getXmlResponse($xml_request);

		return $response;

	}

	/**
	 * Hash Value Syntax: Timestamp.merchantID.payerref.pmtref
	 * @param $storedCC : the stored Credit card to delete
	 * @return bool
	 */

	function deleteStoredCard ($storedCC) {
		$timestamp = $this->getTimestamp();
		$xml_request = '
				<request timestamp="' . $timestamp . '" type="' . self::REQUEST_TYPE_CARD_CANCEL_CARD . '">
					<merchantid>' . $this->_method->merchant_id . '</merchantid>
					<card>
						<ref>' . $storedCC['realex_saved_pmt_ref'] . '</ref>
						<payerref>' . $storedCC['realex_saved_payer_ref'] . '</payerref>
					</card>';
		$sha1_request = $this->getSha1Hash($this->_method->shared_secret, $timestamp, $this->_method->merchant_id, $storedCC['realex_saved_payer_ref'], $storedCC['realex_saved_pmt_ref']);
		$xml_request .= $this->setSha1($sha1_request);
		$xml_request .= '</request>';

		$response = $this->getXmlResponse($xml_request);
		$xml_response = simplexml_load_string($response);
		$result = (string)$xml_response->result;

		$merchantid = (string)$xml_response->merchantid;
		$sha1hash = (string)$xml_response->sha1hash;
		$sha1_temp_response = sha1($timestamp . '.' . $merchantid . '.' . $storedCC['realex_saved_payer_ref'] . '.' . $storedCC['realex_saved_pmt_ref']);
		$sha1_response = sha1($sha1_temp_response . '.' . $this->_method->shared_secret);
		// 501 : Card Ref and Payer combination does not exist: ignore those cases
		if (($result == '00' || $result == '501') && $sha1_response == $sha1_request) {
			return true;
		} else {
			$this->displayError((string)$xml_response->message);
			return false;
		}


	}

	/**
	 * set comment in request
	 * @return string
	 */
	function setComments () {
		$xml_request = '<comments>
				<comment id="1"></comment>
				<comment id="2" />
			</comments>
			';
		return $xml_request;
	}

	/**
	 * set TSS info in request
	 * @return string
	 */
	function setTSSinfo () {
		$BT = $this->order['details']['BT'];
		$ST = ((isset($this->order['details']['ST'])) ? $this->order['details']['ST'] : $this->order['details']['BT']);

		$xml_request = '<tssinfo>
		                <address type="billing">
		                <code>' . $this->stripnonnumeric($BT->zip) . '</code>
						 <country>' . ShopFunctions::getCountryByID($BT->virtuemart_country_id, 'country_2_code') . '</country>
						 </address>
						<address type="shipping">
					     <code>' . $this->stripnonnumeric($ST->zip) . '</code>
						 <country>' . ShopFunctions::getCountryByID($ST->virtuemart_country_id, 'country_2_code') . '</country>
						 </address>
						 <custnum></custnum>
						 <varref></varref>
						 <prodid></prodid>
						 </tssinfo>
						 ';

		return $xml_request;
	}

	/**
	 * @return bool|mixed
	 */
	function setNewPayer (&$newPayerRef) {
		$timestamp = $this->getTimestamp();

		$xml_request = $this->setHeader($timestamp, self::REQUEST_TYPE_PAYER_NEW, false);

		$BT = $this->order['details']['BT'];
		$newPayerRef = $this->getUniqueId($BT->customer_number);
		$xml_request .= '<payer type="Business" ref="' . $newPayerRef . '">
				<firstname>' . $BT->first_name . '</firstname>
				<surname>' . $BT->last_name . '</surname>
				';
		if (!empty($BT->company)) {
			$xml_request .= '<company>' . $BT->company . '</company>
		';
		}

		$xml_request .= '<address>
				<line1>' . $BT->address_1 . '</line1>
				<line2 >' . $BT->address_2 . '</line2>
				<line3 />
				<city>' . $BT->city . '</city>
				<county>' . ShopFunctions::getCountryByID($BT->virtuemart_country_id, 'country_2_code') . '</county>
				<postcode>' . $this->stripnonnumeric($BT->zip) . '</postcode>
				<country code="' . ShopFunctions::getCountryByID($BT->virtuemart_country_id, 'country_2_code') . '"> ' . ShopFunctions::getCountryByID($BT->virtuemart_country_id, 'country_name') . ' </country>
				</address>
				<phonenumbers>
				<home />
				<work>' . $BT->phone_1 . '</work>
				<fax />
				<mobile>' . $BT->phone_2 . '</mobile>
				</phonenumbers>
				<email>' . $BT->email . '</email>
				<comments>
				<comment id="1" />
				<comment id="2" />
				</comments>
				</payer>
				';
		$sha1 = $this->getSha1Hash($this->_method->shared_secret, $timestamp, $this->_method->merchant_id, $this->order['details']['BT']->order_number, '', '', $newPayerRef);
		$xml_request .= $this->setSha1($sha1);
		$xml_request .= '</request>';
		$response = $this->getXmlResponse($xml_request);
		return $response;


	}

	/**
	 * @return bool|mixed
	 */
	function setNewPayment ($newPayerRef, &$newPaymentRef) {
		$timestamp = $this->getTimestamp();
		$xml_request = $this->setHeader($timestamp, self::REQUEST_TYPE_CARD_NEW, false);
		$newPaymentRef = $this->getUniqueId($this->order['details']['BT']->order_number);
		$xml_request .= '<card>
		 <ref>' . $newPaymentRef . '</ref>
		<payerref>' . $newPayerRef . '</payerref>
		<number>' . $this->customerData->getVar('cc_number') . '</number>
		<expdate>' . $this->getFormattedExpiryDateForRequest() . '</expdate>
		<chname>' . $this->customerData->getVar('cc_name') . '</chname>
		<type>' . $this->customerData->getVar('cc_type') . '</type>
		<issueno />
		</card>
		';
		$sha1 = $this->getSha1Hash($this->_method->shared_secret, $timestamp, $this->_method->merchant_id, $this->order['details']['BT']->order_number, '', '', $newPayerRef, $this->customerData->getVar('cc_name'), $this->customerData->getVar('cc_number'));
		$xml_request .= $this->setSha1($sha1);
		$xml_request .= '</request>';
		$response = $this->getXmlResponse($xml_request);
		return $response;

	}

	/**
	 * @param $value
	 * @return string
	 */
	function getUniqueId ($value) {
		return $value . '-' . time();
	}

	/**
	 * @return string
	 */
	function getFormattedExpiryDateForRequest () {
		return $this->customerData->getVar('cc_expire_month') . substr($this->customerData->getVar('cc_expire_year'), -2);
	}

	/**
	 * set header request
	 * @param $timestamp
	 * @param $type
	 * @return string
	 */
	function setHeader ($timestamp, $type, $include_amount = true) {
		$this->request_type = $type;
		$xml_request = '<request timestamp="' . $timestamp . '" type="' . $type . '">
						<merchantid>' . $this->_method->merchant_id . '</merchantid>
						 <account>' . $this->_method->subaccount . '</account>
						 <orderid>' . $this->order['details']['BT']->order_number . '</orderid>
						 ';
		if ($include_amount) {
			$xml_request .= '<amount currency="' . $this->getPaymentCurrency() . '">' . $this->getTotalInPaymentCurrency() . '</amount>
			';
		}
		return $xml_request;
	}

	/**
	 * set sha1 in request
	 * @param $sha1hash
	 * @return string
	 */
	function setSha1 ($sha1hash) {
		$xml_request = '<sha1hash>' . $sha1hash . '</sha1hash>
		';
		return $xml_request;
	}

	function rebateTransaction ($payments) {

		$payment = $this->getTransactionData($payments);
		if ($payment === NULL) {
			return NULL;
		}
		$timestamp = $this->getTimestamp();
		$xml_request = $this->setHeader($timestamp, self::REQUEST_TYPE_REBATE);
		$xml_request .= '<pasref>' . $payment->realex_response_pasref . '</pasref>
				<authcode>' . $payment->realex_response_authcode . '</authcode>
				';

		$refundhash = sha1($this->_method->rebate_password);
		$xml_request .= '<refundhash>' . $refundhash . '</refundhash>
				 ';

		$xml_request .= $this->setComments();

		// NOTE: There is no cardnumber included in the rebate so the cardnumber field can be left blank in the hash  .

		$sha1 = $this->getSha1Hash($this->_method->shared_secret, $timestamp, $this->_method->merchant_id, $this->order['details']['BT']->order_number, $this->getTotalInPaymentCurrency(), $this->getPaymentCurrency(), "");
		$xml_request .= $this->setSha1($sha1);
		$xml_request .= '<md5hash></md5hash>
		';
		$xml_request .= '</request>';
		$response = $this->getXmlResponse($xml_request);

		return $response;
	}

	function settleTransaction ($payments) {
		$payment = $this->getTransactionData($payments);
		if ($payment === NULL) {
			return NULL;
		}
		$timestamp = $this->getTimestamp();
		$xml_request = $this->setHeader($timestamp, self::REQUEST_TYPE_SETTLE);
		$xml_request .= '
				<pasref>' . $payment->realex_response_pasref . '</pasref>
				<authcode>' . $payment->realex_response_authcode . '</authcode>
				';


		$xml_request .= $this->setComments();
		$sha1 = $this->getSha1Hash($this->_method->shared_secret, $timestamp, $this->_method->merchant_id, $this->order['details']['BT']->order_number, $this->getTotalInPaymentCurrency(), $this->getPaymentCurrency(), "");
		$xml_request .= $this->setSha1($sha1);
		$xml_request .= '<md5hash></md5hash>
		';
		$xml_request .= '</request>';
		$response = $this->getXmlResponse($xml_request);

		return $response;
	}

	function voidTransaction ($payments) {
		$payment = $this->getTransactionData($payments);
		if ($payment === NULL) {
			return NULL;
		}
		$timestamp = $this->getTimestamp();
		$xml_request = $this->setHeader($timestamp, self::REQUEST_TYPE_VOID);
		$xml_request .= '
				<pasref>' . $payment->realex_response_pasref . '</pasref>
				<authcode>' . $payment->realex_response_authcode . '</authcode>
				';

		$xml_request .= $this->setComments();
// timestamp.merchantid.orderid...

		$sha1 = $this->getSha1Hash($this->_method->shared_secret, $timestamp, $this->_method->merchant_id, $this->order['details']['BT']->order_number, $this->getTotalInPaymentCurrency(), $this->getPaymentCurrency(), "");
		$xml_request .= $this->setSha1($sha1);
		$xml_request .= '<md5hash></md5hash>
		';
		$xml_request .= '</request>';
		$response = $this->getXmlResponse($xml_request);

		return $response;
	}

	function getTransactionData ($payments, $request_type = self::REQUEST_TYPE_AUTH) {
		foreach ($payments as $payment) {
			if ($payment->realex_request_type_response == $request_type) {
				return $payment;
			}
		}
		return NULL;
	}
	function  isResponseSuccess ($xml_response) {
		$result = (string)$xml_response->result;
		$success = ($result == self::RESPONSE_CODE_SUCCESS);
		return $success;
	}

	/**
	 * get HASH for Realex
	 * @param      $secret
	 * @param      $args
	 * @return string
	 */
	public function getSha1Hash ($secret, $args = null) {
		if (empty($secret)) {
			vmError('no secret value for getSha1Hash', 'no secret value for getSha1Hash');
		}
		$args = func_get_args();
		array_shift($args);
		$tmp = $args;
		$this->debugLog($tmp, 'getSha1Hash', 'debug');
	$this->debugLog($secret, 'getSha1Hash', 'debug');
		$temp2 = implode('.', $args);
		$this->debugLog($temp2, 'getSha1Hash', 'debug');
		$hash = sha1(implode('.', $args));
		$temp3 = "{$hash}.{$secret}";
		$this->debugLog($temp3, 'getSha1Hash', 'debug');
		$hash = sha1("{$hash}.{$secret}");
		$this->debugLog($hash, 'getSha1Hash', 'debug');
		return $hash;
	}

	/**
	 * Validate the response hash from Realex.
	 * is always timestamp.merchantid.orderid.result.message.pasref.authcode
	 * @param      $xml_response simplexml_load_string
	 */
	protected function validateResponseHash ($response) {
		$xml_response = simplexml_load_string($response);
		if ($xml_response->result != '00') {
			return true;
		}
		$hash = $this->getSha1Hash($this->_method->shared_secret, (int)$xml_response->attributes()->timestamp, $this->_method->merchant_id, (string)$xml_response->orderid, (string)$xml_response->result, (string)$xml_response->message, (string)$xml_response->pasref, (string)$xml_response->authcode);

		if ($hash != $xml_response->sha1hash) {
			$this->displayError(vmText::sprintf('VMPAYMENT_REALEX_ERROR_WRONG_HASH', $hash, $xml_response->sha1hash));
			return false;
		}

		return true;
	}

	/**
	 * @return string
	 */
	function getTimestamp () {
		return strftime("%Y%m%d%H%M%S");
	}

	/**
	 * @param $xml_request
	 * @return bool|mixed
	 */
	function getXmlResponse ($xml_request) {
		$this->xml_request = $xml_request;

			$request = simplexml_load_string($xml_request);
		if (isset($request->card)) {
			$card_number=$request->card->number;
			$cc_length=strlen($card_number);
			$request->card->number=str_repeat("*", $cc_length);
		}

		//$this->debugLog(print_r($request, true), 'debug');
		$this->debugLog('<textarea style="margin: 0px; width: 100%; height: 250px;">' . $request->asXML() . '</textarea>', 'Request', 'debug');

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->getRemoteURL());
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_request);
		$response = curl_exec($ch);
		curl_close($ch);

		$this->debugLog('<textarea style="margin: 0px; width: 100%; height: 250px;">' . $response . '</textarea>', 'response :', 'debug');

		if (empty($response)) {
			$this->displayError(vmText::_('VMPAYMENT_REALEX_EMPTY_RESPONSE'));
			return FALSE;
		}
		if (!$this->validateResponseHash($response)) {
			return FALSE;
		}

		return $response;
	}

	/**
	 * @param $message
	 */

	function displayError ($admin, $public = '') {
		if ($admin == NULL) {
			$admin = "an error occurred";
		}

		if (empty($public) AND $this->_method->debug) {
			$public = $admin;
		}
		vmError((string)$admin, (string)$public);
	}
}