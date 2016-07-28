<?php
/**
 *
 * REALEX  payment plugin
 *
 * @version $Id: REALEX.php 7217 2013-09-18 13:42:54Z alatak $
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
defined('_JEXEC') or die();

$customerData = $viewData['customerData'];

JHTML::_('behavior.tooltip');
JHTML::script('vmcreditcard.js', 'components/com_virtuemart/assets/js/', false);
VmConfig::loadJLang('com_virtuemart', true);
vmJsApi::jCreditCard();
vmJsApi::jQuery();
vmJsApi::chosenDropDowns();
$doc = JFactory::getDocument();
//$doc->addScript(JURI::root(true) . '/plugins/vmpayment/realex/realex/assets/js/site.js');
$doc->addStyleSheet(JURI::root(true) . '/plugins/vmpayment/realex/realex/assets/css/realex.css');

?>
<div class="realex remote_cc_form">

	<h3 class="order_amount"><?php echo $viewData['order_amount']; ?></h3>

	<div class="cc_form_payment_name">
		<?php
		echo $viewData['payment_name'];
		?>
	</div>

	<form method="post" action="<?php echo $viewData['submit_url'] ?>">

		<?php if (!empty($viewData['creditcardsDropDown'])) { ?>
			<div class="creditcardsDropDown">
				<?php
				echo $viewData['creditcardsDropDown'];
				?>
			</div>
		<?php
		}
		?>

		<div class="vmpayment_cardinfo">
			<div class="vmpayment_cardinfo_text">
				<?php if (empty($viewData['creditcardsDropDown'])) {
					echo JText::_('VMPAYMENT_REALEX_CC_COMPLETE_FORM');

				} else {
					echo JText::_('VMPAYMENT_REALEX_CC_ADD_NEW');
				}
				?>
			</div>


			<div class="vmpayment_creditcardtype">
				<span class="vmpayment_label"><label for="creditcardtype"><?php echo JText::_('VMPAYMENT_REALEX_CC_CCTYPE'); ?></label></span>


				<?php
				foreach ($viewData['creditcards'] as $creditCard) {
					$options[] = JHTML::_('select.option', $creditCard, JText::_('VMPAYMENT_REALEX_CC_' . strtoupper($creditCard)));
				}
				$attribs = 'class="inputbox vm-chzn-select" style= "width: 250px;" rel="' . $viewData['virtuemart_paymentmethod_id'] . '"';
				echo JHTML::_('select.genericlist', $options, 'cc_type', $attribs, 'value', 'text', $customerData->getVar('cc_type'));
				?>
			</div>
			<div class="vmpayment_cc_type">
				<span class="vmpayment_label"><label for="cc_type"><?php echo JText::_('VMPAYMENT_REALEX_CC_CCNUM'); ?></label></span>

				<input type="text" size="30" class="inputbox" id="cc_number"
				       name="cc_number" value="<?php echo wordwrap($customerData->getVar('cc_number'), 4, " "); ?>"
				       autocomplete="off" onchange="ccError=razCCerror(<?php echo $viewData['virtuemart_paymentmethod_id']; ?>);
					CheckCreditCardNumber(this . value, <?php echo $viewData['virtuemart_paymentmethod_id']; ?>);
					if (!ccError) {
					this.value='';}"/>

				<div id="cc_cardnumber_errormsg"></div>

			</div>
			<div class="vmpayment_cc_cvv">

				<span class="vmpayment_label"><label for="cc_cvv"><?php echo JText::_('VMPAYMENT_REALEX_CC_CVV2') ?></label></span>

				<input type="text" class="inputbox cc_cvv" id="cc_cvv" name="cc_cvv" maxlength="4" size="5" value="<?php echo $customerData->getVar('cc_cvv'); ?>" autocomplete="off"/>
                    <span class="hasTip" title="<?php echo JText::_('VMPAYMENT_REALEX_CC_WHATISCVV') ?>::<?php echo JText::sprintf("VMPAYMENT_REALEX_CC_WHATISCVV_TOOLTIP", $this->_displayCVVImages($viewData['method'])) ?> ">
                        <?php echo JText::_('VMPAYMENT_REALEX_CC_WHATISCVV'); ?>
                    </span>
			</div>
			<div class="vmpayment_cc_date">
				<span class="vmpayment_label"><label for="creditcardtype"><?php echo JText::_('VMPAYMENT_REALEX_CC_EXPDATE'); ?></label></span>

				<?php
				echo shopfunctions::listMonths('cc_expire_month', $customerData->getVar('cc_expire_month'), "class=\"inputbox vm-chzn-select\" style=\"width: 100px;\"", 'm');

				echo shopfunctions::listYears('cc_expire_year', $customerData->getVar('cc_expire_year'), null, null, "class=\"inputbox vm-chzn-select\" style=\"width: 100px;\"  onchange=\"var month = document.getElementById('cc_expire_month_'" . $viewData['virtuemart_paymentmethod_id'] . "); if(!CreditCardisExpiryDate(month.value,this.value, '" . $viewData['virtuemart_paymentmethod_id'] . "')){this.value='';month.value='';}\" ","m");
				?>
				<div id="cc_expiredate_errormsg"></div>

			</div>
			<div class="vmpayment_cc_name">

				<span class="vmpayment_label"><label for="cc_name"><?php echo JText::_('VMPAYMENT_REALEX_CC_CCNAME'); ?></label></span>

				<input type="text" size="30" class="inputbox" id="cc_name"
				       name="cc_name" value="<?php echo $customerData->getVar('cc_name'); ?>"
				       autocomplete="off" onchange="ccError=razCCerror(<?php echo $viewData['virtuemart_paymentmethod_id']; ?>);
					CheckCreditCardNumber(this . value, <?php echo $viewData['virtuemart_paymentmethod_id']; ?>);
					if (!ccError) {
					this.value='';}"/>

				<div id="cc_cardname_errormsg"></div>
			</div>
			<?php if ($viewData['dccinfo']) { ?>
				<div class="dccinfo">
					<div class="dcc_offer_title">
						<?php echo vmText::_('VMPAYMENT_REALEX_DCC_PAY_OWN_CURRENCY'); ?>
					</div>

					<div class="dcc_offer" id="dcc_offer_section">
						<div id="dcc_offer_text">
							<?php echo vmText::sprintf('VMPAYMENT_REALEX_DCC_PAY_OWN_CURRENCY_TIP', $this->getCardHolderAmount($viewData['dccinfo']->merchantamount), $viewData['dccinfo']->merchantcurrency, $this->getCardHolderAmount($viewData['dccinfo']->cardholderamount), $viewData['dccinfo']->cardholdercurrency); ?>
						</div>
						<div class="dcc_offer_exchange_rate">
							<?php echo vmText::sprintf('VMPAYMENT_REALEX_DCC_PAY_OWN_CURRENCY_EXCHANGE_RATE', 1, $viewData['dccinfo']->merchantcurrency, $viewData['dccinfo']->cardholderrate, $viewData['dccinfo']->cardholdercurrency); ?>
						</div>


					</div>
					<div class="dcc_choices">
						<div class="dcc_choice">
							<input class="dcc_offer_btn vm-button" name="dcc_choice" id="dcc_choice_1" type="radio" value="1">
							<label for="dcc_choice_1">
								<?php echo vmText::sprintf('VMPAYMENT_REALEX_DCC_PAY_OWN_CURRENCY_YES', $this->getCardHolderAmount($viewData['dccinfo']->cardholderamount), $viewData['dccinfo']->cardholdercurrency); ?>
							</label>
						</div>
						<div class="dcc_choice">
							<input class="dcc_offer_btn vm-button" name="dcc_choice" id="dcc_choice_0" type="radio" value="0" checked="checked">
							<label for="dcc_choice_0">
								<?php echo vmText::sprintf('VMPAYMENT_REALEX_DCC_PAY_OWN_CURRENCY_NO', $this->getCardHolderAmount($viewData['dccinfo']->merchantamount), $viewData['dccinfo']->merchantcurrency); ?>
							</label>
						</div>
					</div>
					<div class="dcc_card_payment_button details-button">
						<div class=" dcc_offer_legal ">
							<?php echo vmText::sprintf('VMPAYMENT_REALEX_DCC_PAY_OWN_CURRENCY_LEGAL', $viewData['dccinfo']->exchangeratesourcetimestamp, sprintf("%d", $viewData['dccinfo']->marginratepercentage), $viewData['dccinfo']->commissionpercentage); ?>
						</div>
					</div>
				</div>
			<?php } ?>
			<?php if ($viewData['offer_save_card']) {
				if ($customerData->getVar('save_card')) {
					$checked = 'checked="checked"';
				} else {
					$checked = '';
				}
				?>

				<div class="offer_save_card">
					<label for="save_card">
						<input id="save_card" name="save_card" type="checkbox" value="1" <?php echo $checked ?>><span class="save_card"> <?php echo vmText::_('VMPAYMENT_REALEX_SAVE_CARD_DETAILS') ?></span>
					</label>

					<div id="save_card_tip"><?php echo vmText::_('VMPAYMENT_REALEX_SAVE_CARD_DETAILS_TIP') ?></div>
				</div>
			<?php
			}
			?>


		</div>
		<div class="dcc_card_payment_button details-button">
		<span class="addtocart-button">

		<input type="submit" class="dcc_offer_btn addtocart-button" value="<?php echo $viewData['card_payment_button'] ?>"/>
		<input type="hidden" name="option" value="com_virtuemart"/>
		<input type="hidden" name="view" value="pluginresponse"/>
		<input type="hidden" name="task" value="pluginnotification"/>
		<input type="hidden" name="notificationTask" value="<?php echo $viewData['notificationTask']; ?>"/>
		<input type="hidden" name="order_number" value="<?php echo $viewData['order_number']; ?>"/>
		<input type="hidden" name="pm" value="<?php echo $viewData['virtuemart_paymentmethod_id']; ?>"/>
			</span>
	</form>
</div>
</div>