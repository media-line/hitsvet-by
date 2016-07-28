<?php
/**
 *
 * Paypal payment plugin
 *
 * @author Jeremy Magne
 * @author Valérie Isaksen
 * @version $Id: paypal.php 7217 2013-09-18 13:42:54Z alatak $
 * @package VirtueMart
 * @subpackage payment
 * Copyright (C) 2004-2013 Virtuemart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
 *
 * http://virtuemart.net
 */


defined( '_JEXEC' ) or die( 'Restricted access' );

//PayPal error codes:
//https://developer.paypal.com/webapps/developer/docs/classic/api/errorcodes/
//API Reference
//https://developer.paypal.com/webapps/developer/docs/classic/api/

class PaypalHelperPayPalApi extends PaypalHelperPaypal {
	
	var $api_login_id = '';
	var $api_signature = '';
	var $api_password = '';
	
	function __construct($method,$paypalPlugin) {
		parent::__construct($method,$paypalPlugin);

		//Set the credentials
		if ($this->_method->sandbox ) {
			$this->api_login_id = $this->_method->sandbox_api_login_id;
			$this->api_signature = $this->_method->sandbox_api_signature;
			$this->api_password = $this->_method->sandbox_api_password;
		} else {
			$this->api_login_id = $this->_method->api_login_id;
			$this->api_signature = $this->_method->api_signature;
			$this->api_password = $this->_method->api_password;
		}

		if (empty($this->api_login_id) || empty($this->api_signature) || empty($this->api_password)) {
            $text=JText::sprintf('VMPAYMENT_PAYPAL_CREDENTIALS_NOT_SET', $this->_method->payment_name, $this->_method->virtuemart_paymentmethod_id);
            vmError($text,$text);
		}
	}

	function initPostVariables($paypalMethod) {
		$post_variables = Array();
		$post_variables['METHOD'] 		= $paypalMethod;
		//$post_variables['version']	 	= "106.0"; //https://developer.paypal.com/webapps/developer/docs/classic/release-notes/
		$post_variables['version'] = "104.0";
		$post_variables['USER']			= $this->api_login_id;
		$post_variables['PWD'] 			= $this->api_password;
		$post_variables['SIGNATURE'] 	= $this->api_signature;
		$post_variables['BUTTONSOURCE'] = self::BNCODE;;
		$post_variables['CURRENCYCODE']	= $this->currency_code_3;

		if (is_array($this->order) && is_object($this->order['details']['BT'])) {
			$post_variables['INVNUM']	= $this->order['details']['BT']->order_number;
		} else if (is_object($this->order)) {
			$post_variables['INVNUM']	= $this->order->order_number;
		}
		$post_variables['IPADDRESS']	= ($_SERVER['REMOTE_ADDR']=='::1') ? '127.0.0.1' : $_SERVER['REMOTE_ADDR'];

		return $post_variables;
	}
	
	function addBillTo(&$post_variables) {

		$addressBT = $this->order['details']['BT'];

		//Bill To
		$post_variables['FIRSTNAME']	=  isset($addressBT->first_name) ? $this->truncate($addressBT->first_name, 50) : '';
		$post_variables['LASTNAME']		=  isset($addressBT->last_name) ? $this->truncate($addressBT->last_name, 50) : '';
		$post_variables['STREET']		=  isset($addressBT->address_1) ? $this->truncate($addressBT->address_1, 60) : '';
		$post_variables['CITY']			=  isset($addressBT->city) ? $this->truncate($addressBT->city, 40) : '';
		$post_variables['ZIP']			=  isset($addressBT->zip) ? $this->truncate($addressBT->zip, 40) : '';
		$post_variables['STATE']		=  isset($addressBT->virtuemart_state_id) ?  ShopFunctions::getStateByID($addressBT->virtuemart_state_id, 'state_2_code') : '';
		$post_variables['COUNTRYCODE']	=  ShopFunctions::getCountryByID($addressBT->virtuemart_country_id, 'country_2_code');
	}
	
	function addShipTo(&$post_variables) {

		$addressST = ((isset($this->order['details']['ST'])) ? $this->order['details']['ST'] : $this->order['details']['BT']);

		//Ship To
		$post_variables['SHIPTONAME']	= isset($addressST->first_name) ? $this->truncate($addressST->first_name, 50) : '';
		$post_variables['SHIPTOSTREET']	= isset($addressST->first_name) ? $this->truncate($addressST->address_1, 60) : '';
		$post_variables['SHIPTOCITY']	= isset($addressST->city) ? $this->truncate($addressST->city, 40) : '';
		$post_variables['SHIPTOZIP']	= isset($addressST->zip) ? $this->truncate($addressST->zip, 40) : '';
		$post_variables['SHIPTOSTATE']	= isset($addressST->virtuemart_state_id) ?  ShopFunctions::getStateByID($addressST->virtuemart_state_id, 'state_2_code')  : '';
		$post_variables['SHIPTOCOUNTRYCODE'] = ShopFunctions::getCountryByID($addressST->virtuemart_country_id, 'country_2_code');
	}
	
	function addCreditCard(&$post_variables) {

		$post_variables['ACCT']			= $this->customerData->getVar('cc_number');
		$post_variables['CVV2']			= $this->customerData->getVar('cc_cvv');
		$post_variables['CREDITCARDTYPE']	= $this->customerData->getVar('cc_type');
		$post_variables['EXPDATE']		= $this->_getFormattedDate($this->customerData->getVar('cc_expire_month'), $this->customerData->getVar('cc_expire_year'));
	}
	
	public function ManageCheckout() {
		switch ($this->_method->payment_type) {
			case '_xclick':
				return $this->DoPayment();
			case '_xclick-subscriptions':
				return $this->CreateRecurringPaymentsProfile();
			case '_xclick-payment-plan':
				return $this->CreatePaymentPlanProfile();
		}
	}

	public function ManageCancelOrder($payment) {
		$this->RefundTransaction($payment);
		/*
		switch ($this->_method->payment_type) {
			case '_xclick':
				return $this->RefundTransaction($payment);
			case '_xclick-subscriptions':
			case '_xclick-payment-plan':
				return $this->ManageRecurringPaymentsProfileStatus($payment);
		}
		*/
	}
	
	function DoPayment() {

		$post_variables = $this->initPostVariables('DoDirectPayment');
		$this->addBillTo($post_variables);
		$this->addShipTo($post_variables);
		$this->addCreditCard($post_variables);

		$post_variables['PAYMENTACTION'] 	= $this->_method->payment_action;
		$post_variables['AMT']				= $this->total;

		$this->sendRequest($post_variables);
		if ($this->handleResponse()) {
			if ($this->_method->payment_action == 'Authorization') {
				$this->response['PAYMENTSTATUS'] = 'Pending';
				$this->response['PENDINGREASON'] = 'authorization';
			} else {
				$this->response['PAYMENTSTATUS'] = 'Completed';
				$this->response['PENDINGREASON'] = 'None';
			}
			$this->response['paypal_response_txn_type'] = 'DoDirectPayment';
			return true;
		} else {
			return false;
		}
	}

	public function CreateRecurringPaymentsProfile() {
		//https://developer.paypal.com/webapps/developer/docs/classic/direct-payment/ht_dp-recurringPaymentProfile-curl-etc/
		//https://developer.paypal.com/webapps/developer/docs/classic/api/merchant/CreateRecurringPaymentsProfile_API_Operation_NVP/

		$post_variables = $this->initPostVariables('CreateRecurringPaymentsProfile');
		$this->addBillTo($post_variables);
		$this->addShipTo($post_variables);
		$this->addCreditCard($post_variables);
		
		//$post_variables['SUBSCRIBERNAME']	= isset($addressBT->first_name) ? $this->truncate($addressBT->first_name, 50) : '';
		$post_variables['PROFILEREFERENCE']		= $this->order['details']['BT']->order_number;
		$post_variables['DESC']					= $this->getRecurringProfileDesc();
		
		$startDate = JFactory::getDate();
		$post_variables['PROFILESTARTDATE']		= $startDate->toISO8601();
		$post_variables['AUTOBILLOUTAMT']		= 'AddToNextBilling';
		

		$post_variables['BILLINGFREQUENCY']		= $this->getDurationValue($this->_method->subscription_duration);
		$post_variables['BILLINGPERIOD']		= $this->getDurationUnit($this->_method->subscription_duration);
		$post_variables['TOTALBILLINGCYCLES']	= $this->_method->subscription_term;


		if ($this->cart->pricesUnformatted['salesPricePayment'] && $this->cart->pricesUnformatted['salesPricePayment'] > 0) {
			$post_variables['INITAMT']				= $this->cart->pricesUnformatted['salesPricePayment'];
			$post_variables['FAILEDINITAMTACTION']	= 'CancelOnFailure';
			$post_variables['AMT']					= $this->total - $this->cart->pricesUnformatted['salesPricePayment'];
		} else {
			$post_variables['AMT']					= $this->total;
		}

		if ($this->_method->subcription_trials) {
			$post_variables['TRIALBILLINGFREQUENCY']= $this->getDurationValue($this->_method->trial1_duration);
			$post_variables['TRIALBILLINGPERIOD']	= $this->getDurationUnit($this->_method->trial1_duration);
			$post_variables['TRIALTOTALBILLINGCYCLES']= $this->_method->subcription_trials;
			$post_variables['TRIALAMT']				= ($this->_method->trial1_price) ? $this->_method->trial1_price : 0;
		}
		
		$this->sendRequest($post_variables);
		return $this->handleResponse();
	}

	public function CreatePaymentPlanProfile() {
		//Payment plans are not implemented in the API. 
		//A workaround is to create a subscription profile and divide the total amount by the term.

		$post_variables = $this->initPostVariables('CreateRecurringPaymentsProfile');
		$this->addBillTo($post_variables);
		$this->addShipTo($post_variables);
		$this->addCreditCard($post_variables);
		
		//$post_variables['SUBSCRIBERNAME']	= isset($addressBT->first_name) ? $this->truncate($addressBT->first_name, 50) : '';
		$post_variables['PROFILEREFERENCE']		= $this->order['details']['BT']->order_number;
		$post_variables['DESC']					= $this->order['details']['BT']->order_number.': '.$this->getPaymentPlanDesc();
		
		
		if ($this->cart->pricesUnformatted['salesPricePayment'] && $this->cart->pricesUnformatted['salesPricePayment'] > 0) {
			$initAmount = $this->cart->pricesUnformatted['salesPricePayment'];
		} else {
			$initAmount = 0;
		}
		$occurenceAmount = round(($this->total-$initAmount) / $this->_method->payment_plan_term,2);
		if ($this->_method->payment_plan_defer == 2) {
			$initAmount += $occurenceAmount;
			$occurencesCount = $this->_method->payment_plan_term - 1;
		} else {
			$occurencesCount = $this->_method->payment_plan_term;
		}
		
		if ($this->_method->payment_plan_defer && $this->_method->payment_plan_defer_strtotime) {
			$startDate = JFactory::getDate($this->_method->payment_plan_defer_strtotime);
		} else {
			$startDate = JFactory::getDate();
		}
		$post_variables['PROFILESTARTDATE']		= $startDate->toISO8601();
		$post_variables['AUTOBILLOUTAMT']		= 'AddToNextBilling';
		
		$post_variables['BILLINGFREQUENCY']		= $this->getDurationValue($this->_method->payment_plan_duration);
		$post_variables['BILLINGPERIOD']		= $this->getDurationUnit($this->_method->payment_plan_duration);
		$post_variables['TOTALBILLINGCYCLES']	= $occurenceAmount;

		if ($this->cart->pricesUnformatted['salesPricePayment'] && $this->cart->pricesUnformatted['salesPricePayment'] > 0) {
			$post_variables['INITAMT']				= $initAmount;
			$post_variables['FAILEDINITAMTACTION']	= 'CancelOnFailure';
		}
		$post_variables['AMT']					= $occurenceAmount;

		$this->sendRequest($post_variables);
		return $this->handleResponse();
	}

	function GetRecurringPaymentsProfileDetails($profileId) {
		
		$post_variables = $this->initPostVariables('GetRecurringPaymentsProfileDetails');
		$post_variables['PROFILEID']	= $profileId;

		$this->sendRequest($post_variables);
		return $this->handleResponse();
	}
	
	function ManageRecurringPaymentsProfileStatus($payment) {
		
		$paypal_data = json_decode($payment->paypal_fullresponse);
		$post_variables = $this->initPostVariables('ManageRecurringPaymentsProfileStatus');
		$post_variables['PROFILEID']	= $paypal_data->PROFILEID;
		$post_variables['ACTION']		= 'Cancel';

		$this->sendRequest($post_variables);
		$this->handleResponse();
		
		return $this->GetRecurringPaymentsProfileDetails($paypal_data->PROFILEID);
	}
	
	function DoCapture($payment) {
		
		$paypal_data = json_decode($payment->paypal_fullresponse);
		//Only capture payment if it still pending
		if (strcasecmp($paypal_data->PAYMENTSTATUS ,'Pending') !=0 && strcasecmp($paypal_data->PENDINGREASON, 'authorization') !=0) {
			return false;
		}
		
		$post_variables = $this->initPostVariables('DoCapture');
		
		//Do we need to reauthorize ?
		$reauth = $this->doReauthorize($paypal_data->txn_id,$paypal_data);
		if ($reauth === false) {
			$post_variables['AuthorizationID']	= $paypal_data->TRANSACTIONID;
		} else {
			$post_variables['AuthorizationID']	= $reauth;
		}

		$post_variables['PAYMENTACTION'] 	= 'DoCapture';
		$post_variables['AMT']	= $this->total;
		$post_variables['COMPLETETYPE']	= 'Complete';

		$this->sendRequest($post_variables);
		//print_a($post_variables);
		//print_a($this->response);
		$success = $this->handleResponse();
		if (!$success) {
			$this->doVoid($payment);
		}
		return $success;
	}
	
	function doReauthorize($AuthorizationID,$paypal_data) {
		return false;
		$post_variables = $this->initPostVariables('DoReauthorization');
		$post_variables['AuthorizationID']	= $AuthorizationID;
		$post_variables['PAYMENTACTION'] 	= 'DoReauthorization';
		$post_variables['AMT']	= $this->total;

		$this->sendRequest($post_variables);
		if ($this->handleResponse()) {
			return $this->response['AUTHORIZATIONID'];
		} else {
			return false;
		}
	}

	function RefundTransaction($payment) {
		
		$paypal_data = json_decode($payment->paypal_fullresponse);
		if ($paypal_data->PAYMENTSTATUS == 'Completed') {
			$post_variables = $this->initPostVariables('RefundTransaction');
			$post_variables['REFUNDTYPE'] 	= 'Full';
		} else if ($paypal_data->PAYMENTSTATUS == 'Pending' && $paypal_data->PENDINGREASON == 'authorization') {
			$post_variables = $this->initPostVariables('DoVoid');
		} else {
			return false;
		}
		
		$post_variables['AuthorizationID']	= $paypal_data->TRANSACTIONID;

		$this->sendRequest($post_variables);
		return $this->handleResponse();
	}
	function doVoid($payment) {
		$paypal_data = json_decode($payment->paypal_fullresponse);
		$post_variables = $this->initPostVariables('DoVoid');
		$post_variables['AuthorizationID']	= $paypal_data->TRANSACTIONID;
		$this->sendRequest($post_variables);
		return $this->handleResponse();
	}



	function validate($enqueueMessage=true) {
		
		if (!class_exists('Creditcard')) {
			require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'creditcard.php');
		}
		$html = '';
		$cc_valid = true;
		$errormessages = array();
		
		$cc_type = $this->customerData->getVar('cc_type');
		$cc_number = $this->customerData->getVar('cc_number');
		$cc_cvv = $this->customerData->getVar('cc_cvv');
		$cc_expire_month = $this->customerData->getVar('cc_expire_month');
		$cc_expire_year = $this->customerData->getVar('cc_expire_year');

		if (!Creditcard::validate_credit_card_number($cc_type, $cc_number)) {
			$errormessages[] = 'VMPAYMENT_PAYPAL_CC_CARD_NUMBER_INVALID';
			$cc_valid = false;
		}
		if ($this->_method->cvv_required or $cc_type=='Maestro') {
			$required=true;
		} else {
			$required=false;
		}
		if (!Creditcard::validate_credit_card_cvv($cc_type, $cc_cvv, $required)) {
			$errormessages[] = 'VMPAYMENT_PAYPAL_CC_CARD_CVV_INVALID';
			$cc_valid = false;
		}
		if (!Creditcard::validate_credit_card_date($cc_type, $cc_expire_month, $cc_expire_year)) {
			$errormessages[] = 'VMPAYMENT_PAYPAL_CC_CARD_DATE_INVALID';
			$cc_valid = false;
		}
		if (!$cc_valid) {
			foreach ($errormessages as $msg) {
				$html .= Jtext::_($msg) . "<br/>";
			}
		}
		if (!$cc_valid && $enqueueMessage) {
			$app = & JFactory::getApplication();
			$app->enqueueMessage($html,'error');
		}
		$displayInfoMsg="";
		if (!$cc_valid) {
			$displayInfoMsg=false;
			return false;
		} else {
			return parent::validate($displayInfoMsg);
		}
	}
	
	function displayExtraPluginInfo() {
		$extraInfo = '';
		//if ($this->customerData->getVar('cc_number') && $this->validate()) {
		if ($this->customerData->getVar('cc_number') ) {
			$cc_number = "**** **** **** " . substr($this->customerData->getVar('cc_number'), -4);
			$creditCardInfos = '<br /><span class="vmpayment_cardinfo">' . JText::_('VMPAYMENT_PAYPAL_CC_CCTYPE') . $this->customerData->getVar('cc_type') . '<br />';
			$creditCardInfos .=JText::_('VMPAYMENT_PAYPAL_CC_CCNUM') . $cc_number . '<br />';
			$creditCardInfos .= JText::_('VMPAYMENT_PAYPAL_CC_CVV2') . '****' . '<br />';
			$creditCardInfos .= JText::_('VMPAYMENT_PAYPAL_CC_EXDATE') . $this->customerData->getVar('cc_expire_month') . '/' . $this->customerData->getVar('cc_expire_year');
			$creditCardInfos .="</span>";
			$extraInfo .= $creditCardInfos;
		} else {
			$extraInfo .= '<br/><a href="'.JRoute::_('index.php?option=com_virtuemart&view=cart&task=editpayment&Itemid=' . JRequest::getInt('Itemid'), false).'">'.JText::_('VMPAYMENT_PAYPAL_CC_ENTER_INFO').'</a>';
		}
		$extraInfo .= parent::getExtraPluginInfo();
		return $extraInfo;
	}

	protected function getDurationUnit($duration) {
		$parts = explode('-',$duration);
		switch ($parts[1]) {
			case 'D': return 'Day';
			case 'W': return 'Week';
			case 'M': return 'Month';
			case 'Y': return 'Year';
		}
	}

	function getOrderBEFields() {
		$showOrderBEFields = array(
			'method' => 'method',
			'ACK' => 'ACK',
			'TXN_ID' => 'TRANSACTIONID',
			'PROFILEID' => 'PROFILEID',
			'MC_GROSS' => 'PAYMENTINFO_0_AMT',
			'MC_FEE' => 'PAYMENTINFO_0_FEEAMT',
			'TAXAMT' => 'PAYMENTINFO_0_TAXAMT',
			'MC_CURRENCY' => 'PAYMENTINFO_0_CURRENCYCODE',
			'PAYMENT_STATUS' => 'PAYMENTSTATUS',
			'REFUND_STATUS' => 'REFUNDSTATUS',
			'PENDING_REASON' => 'PENDINGREASON',
			'REASONCODE' => 'PAYMENTINFO_0_REASONCODE',
			'ERRORCODE' => 'PAYMENTINFO_0_ERRORCODE',
			'PROTECTION_ELIGIBILITY' => 'PAYMENTINFO_0_PROTECTIONELIGIBILITY',
			'CORRELATIONID' => 'CORRELATIONID',


		);
		return $showOrderBEFields;
	}

}
