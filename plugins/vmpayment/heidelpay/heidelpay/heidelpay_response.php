<?php

/**
 * Heidelpay response page for Heidelpay plugin
 * @author Heidelberger Paymenrt GmbH <Jens Richter> 
 * @version 13.07
 * @package VirtueMart
 * @subpackage payment
 * @copyright Copyright (C) Heidelberger Payment GmbH
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 */

include('../../../../configuration.php');
$config = new JConfig();

foreach ($_POST as $key => $value) {
	$key = preg_replace('/_x$/', '', trim($key));
	$_POST[$key] = $value;
	
}
foreach ($_GET as $key => $value) {
	$key = preg_replace('/_x$/', '', trim($key));
	$_GET[$key] = $value;
}



if ( $_SERVER['SERVER_PORT'] == "443" ) {
	$Protocol = "https://";
} else {
	$Protocol = "http://";
}

$PATH = preg_replace('@plugins\/vmpayment\/heidelpay\/heidelpay\/heidelpay_response\.php@','', $_SERVER['SCRIPT_NAME']);
$URL = $_SERVER['HTTP_HOST'] . $PATH ; 


$redirectURL	 = $Protocol.$URL.'index.php?option=com_virtuemart&view=pluginresponse&task=pluginresponsereceived&on='.$_GET['on'].'&pm='.$_GET['pm'].'&Itemid='.$_GET['Itemid'];
$cancelURL	 = $Protocol.$URL.'index.php?option=com_virtuemart&view=pluginresponse&task=pluginUserPaymentCancel&on='.$_GET['on'].'&pm='.$_GET['pm'].'&Itemid='.$_GET['Itemid'];

function updateHeidelpay($orderID, $connect) {
	$comment="";
	if ( preg_match('/^[A-Za-z0-9 -]+$/', $orderID , $str)) {
		$link = mysql_connect($connect->host, $connect->user , $connect->password);
		mysql_select_db($connect->db);	
		$result = mysql_query("SELECT virtuemart_order_id FROM ".$connect->dbprefix."virtuemart_orders"." WHERE  order_number = '".mysql_real_escape_string($orderID)."';");
		$row = mysql_fetch_object($result);
		$paymentCode = explode('.' , $_POST['PAYMENT_CODE']);
		if ($_POST['PROCESSING_RESULT'] == "NOK") {
				$comment = $_POST['PROCESSING_RETURN'];
		} elseif ($paymentCode[0] == "PP" or $paymentCode[0] == "IV") {
			if (strtoupper ($_POST['CRITERION_LANG']) == 'DE') {
					$comment = '<b>Bitte &uuml;berweisen Sie uns den Betrag von '.$_POST['CLEARING_CURRENCY'].' '.$_POST['PRESENTATION_AMOUNT'].' auf folgendes Konto:</b>
					<br /><br/>
					Land : '.$_POST['CONNECTOR_ACCOUNT_COUNTRY'].'<br />
					Kontoinhaber : '.$_POST['CONNECTOR_ACCOUNT_HOLDER'].'<br />
					Konto-Nr. : '.$_POST['CONNECTOR_ACCOUNT_NUMBER'].'<br />
					Bankleitzahl:  '.$_POST['CONNECTOR_ACCOUNT_BANK'].'<br />
					IBAN: '.$_POST['CONNECTOR_ACCOUNT_IBAN'].'<br />
					BIC: '.$_POST['CONNECTOR_ACCOUNT_BIC'].'<br />
					<br />
					<b>Geben sie bitte im Verwendungszweck UNBEDINGT die Identifikationsnummer<br />
					'.$_POST['IDENTIFICATION_SHORTID'].'<br />
					und NICHTS ANDERES an.</b><br />';
				} else {
					$comment = '<b>Please transfer the amount of '.$_POST['CLEARING_CURRENCY'].' '.$_POST['PRESENTATION_AMOUNT'].' to the following account:</b>
					<br /><br/>
					Country: '.$_POST['CONNECTOR_ACCOUNT_COUNTRY'].'<br />
					Account holder: '.$_POST['CONNECTOR_ACCOUNT_HOLDER'].'<br />
					Account No.: '.$_POST['CONNECTOR_ACCOUNT_NUMBER'].'<br />
					Bank Code:  '.$_POST['CONNECTOR_ACCOUNT_BANK'].'<br />
					IBAN: '.$_POST['CONNECTOR_ACCOUNT_IBAN'].'<br />
					BIC: '.$_POST['CONNECTOR_ACCOUNT_BIC'].'<br />
					<br />
					<b>When you transfer the money you HAVE TO use the identification number<br />
					'.$_POST['IDENTIFICATION_SHORTID'].'<br />
					as the descriptor and nothing else. Otherwise we cannot match your transaction!</b><br />';
			}
		
				if($_POST['ACCOUNT_BRAND'] == 'BARPAY')
						
						{
										
										
							$comment = '(-'.$_POST['CRITERION_BARPAY_PAYCODE_URL'].'-)
									</b><br />
									</b><br />
									Drucken Sie den Barcode aus oder speichern Sie diesen auf Ihrem mobilen Endger�t. 
									Gehen Sie nun zu einer Kasse der 18.000 Akzeptanzstellen in Deutschland und bezahlen 
									Sie ganz einfach in bar. In dem Augenblick, wenn der Rechnungsbetrag beglichen wird, 
									erh�lt der Online-H�ndler die Information �ber den Zahlungseingang.Die bestellte Ware 
									oder Dienstleistung geht umgehend in den Versand
								';
						}	
						
		}
		if (!empty($row->virtuemart_order_id)) {
			$sql = "INSERT ".$connect->dbprefix."virtuemart_payment_plg_heidelpay SET " .
					"virtuemart_order_id			= \"".mysql_real_escape_string($row->virtuemart_order_id). "\"," .
					"order_number					= \"".mysql_real_escape_string($_GET['on']). "\"," .
					"virtuemart_paymentmethod_id	= \"".mysql_real_escape_string($_GET['pm']). "\"," .
					"unique_id 						= \"".mysql_real_escape_string($_POST['IDENTIFICATION_UNIQUEID']). "\"," .
					"short_id						= \"".mysql_real_escape_string($_POST['IDENTIFICATION_SHORTID']). "\"," .
					"payment_code					= \"".mysql_real_escape_string($_POST['PROCESSING_REASON_CODE']). "\"," .
					"comment						= \"".mysql_real_escape_string($comment). "\"," .
					"payment_methode				= \"".mysql_real_escape_string($paymentCode[0]). "\"," .
					"payment_type					= \"".mysql_real_escape_string($paymentCode[1]). "\"," .
					"transaction_mode				= \"".mysql_real_escape_string($_POST['TRANSACTION_MODE']). "\"," .
					"payment_name					= \"".mysql_real_escape_string($_POST['CRITERION_PAYMENT_NAME']). "\"," .
					"processing_result				= \"".mysql_real_escape_string($_POST['PROCESSING_RESULT']). "\"," .
					"secret_hash					= \"".mysql_real_escape_string($_POST['CRITERION_SECRET']). "\"," .
					"response_ip					= \"".mysql_real_escape_string($_SERVER['REMOTE_ADDR']). "\";" ;
			$dbEerror = mysql_query($sql);
		}
	}
}


$returnvalue=$_POST['PROCESSING_RESULT'];
if (!empty($returnvalue)){
	if (strstr($returnvalue,"ACK")) {
		print $redirectURL;
		updateHeidelpay($_POST['IDENTIFICATION_TRANSACTIONID'], $config);
	} else if ($_POST['FRONTEND_REQUEST_CANCELLED'] == 'true'){
		print $cancelURL ;
	} else {
		updateHeidelpay($_POST['IDENTIFICATION_TRANSACTIONID'], $config);
		print $redirectURL;
	}
} else {
	echo 'FAIL';
}

?>


