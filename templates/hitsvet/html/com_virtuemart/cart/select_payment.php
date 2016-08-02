
<?php
/**
 *
 * Layout for the payment selection
 *
 * @package	VirtueMart
 * @subpackage Cart
 * @author Max Milbers
 *
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: select_payment.php 5451 2012-02-15 22:40:08Z alatak $
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
$addClass="";

$doc = JFactory::getDocument();
$doc->addScript("http://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js");

if (VmConfig::get('oncheckout_show_steps', 1)) {
    echo '<div class="checkoutStep" id="checkoutStep3">' . JText::_('COM_VIRTUEMART_USER_FORM_CART_STEP3') . '</div>';
}

if ($this->layoutName!='default') {
	$headerLevel = 1;
	if($this->cart->getInCheckOut()){
		$buttonclass = 'button vm-button-correct';
	} else {
		$buttonclass = 'default';
	}
?>
	<form method="post" id="paymentForm ntcn1" name="choosePaymentRate" action="<?php echo JRoute::_('index.php'); ?>" class="form-validate <?php echo $addClass ?>">
<?php } else {
		$headerLevel = 3;
		$buttonclass = 'vm-button-correct';
	}


	echo "<h".$headerLevel.">".JText::_('COM_VIRTUEMART_CART_SELECT_PAYMENT')."</h".$headerLevel.">";

?>

<div class="buttonBar-right">

<button name="setpayment" class="<?php echo $buttonclass ?>" type="submit"><?php echo JText::_('COM_VIRTUEMART_SAVE'); ?></button>
     &nbsp;
   <?php   if ($this->layoutName!='default') { ?>
<button class="<?php echo $buttonclass ?>" type="reset" onClick="window.location.href='<?php echo JRoute::_('index.php?option=com_virtuemart&view=cart'); ?>'" ><?php echo JText::_('COM_VIRTUEMART_CANCEL'); ?></button>
	<?php  } ?>
    </div>

<?php
     if ($this->found_payment_method OR (VmConfig::get('oncheckout_opc', 1) )) {


    echo "<fieldset>";
		foreach ($this->paymentplugins_payments as $paymentplugin_payments) {
		    if (is_array($paymentplugin_payments)) {
			foreach ($paymentplugin_payments as $paymentplugin_payment) {
			    echo $paymentplugin_payment.'<br />';
			}
		    }
		}
    echo "</fieldset>";

    } else {
	 echo "<h1>".$this->payment_not_found_text."</h1>";
    }

if ($this->layoutName!='default') {
?>  <input type="hidden" name="option" value="com_virtuemart" />
    <input type="hidden" name="view" value="cart" />
    <input type="hidden" name="task" value="setpayment" />
    <input type="hidden" name="controller" value="cart" />
</form>
<?php
}
?>

<script>
    $.noConflict();
jQuery(document).ready(function () {
    jQuery('#payment_id_2').click(function() {
        jQuery('#myModal').modal();

        jQuery( '.vm-button-correct' ).css( 'display', 'none' );
    });

    jQuery('#payment_id_1').click(function() {
        jQuery('.vm-button-correct').css( 'display', 'block');
    })
});
</script>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body">
        Извините процессинговый центр временно недоступен, оплата картой невозможна. <br/><br/> Для связи с менеджером  и согласования способа оплаты и доставки заказа нажмите кнопку "Отправить письмо менеджеру". <br/><br/> Если же Вы хотите продоолжить оформление заказа, то Вам необходимо выбрать в качестве способа оплаты - оплату наличными.
      </div>
      <div class="modal-footer">
        <!--<button type="button" class="button" data-dismiss="modal">Отправить письмо менеджеру</button>-->
          <form action="" method="post" class="form-editpayment">
            <input type="submit" class="button" value="Отправить письмо менеджеру" name="submit">
          </form>
        <button type="button" class="button" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>

<?php
// если была нажата кнопка "Отправить"
if($_POST['submit']) {
				$subject = "письмо с сайта";
    $message = "есть заявка с сайта";
    // функция, которая отправляет наше письмо.
    $mail = mail('akravchenko@medidaline.by', $subject, $message);
						if ($mail == true) /*{
								echo 'Спасибо! Ваше письмо отправлено.';
						} else*/ {
								echo 'К сожалению письмо не отправлено. Попробуйте еще раз или свяжитесь с нами по телефону.';
						};
}
?>