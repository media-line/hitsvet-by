<?php                            
/**
 *
 * Layout for the shopping cart
 *
 * @package    VirtueMart
 * @subpackage Cart
 * @author Max Milbers
 * @author Patrick Kohl
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 *
 */
//echo'<pre>';print_r($this->cart->products);echo'</pre>';

	$box = "
//<![CDATA[
	jQuery(document).ready(function($) {
		$('div#full-tos').hide();
		$('.quantity-plus').live('click', function(event) {
			event.preventDefault();
			var quantity_input = $(this).parent('form').find('.quantity-input');
			quantity_input.val(parseInt(quantity_input.val()) + 1);
			jQuery.ajax({
				type: 'post',				
				url: '/index.php?option=com_virtuemart&view=cart&task=updateJS',
				data: 	{	cart_virtuemart_product_id : jQuery(this).parent('form').find('input[name=\"cart_virtuemart_product_id\"]').val(),	
							quantity : jQuery(this).parent('form').find('input[name=\"quantity\"]').val()
						},
				dataType: 'html',
				beforeSend: function(){
				},
				success: function(msg){
					jQuery('#MT_Center_wr').html(msg);
					jQuery('#tosAccepted').hide();
				}
			});		
			
			
		});
		$('.quantity-minus').live('click', function(event) {
			event.preventDefault();
			var quantity_input = $(this).parent('form').find('.quantity-input');
			var quantity_input_val = parseInt(quantity_input.val());
			if(quantity_input_val != 1)	{
				quantity_input.val(quantity_input_val - 1);
				jQuery.ajax({
					type: 'post',				
					url: '/index.php?option=com_virtuemart&view=cart&task=updateJS',
					data: 	{	cart_virtuemart_product_id : jQuery(this).parent('form').find('input[name=\"cart_virtuemart_product_id\"]').val(),	
								quantity : jQuery(this).parent('form').find('input[name=\"quantity\"]').val()
							},
					dataType: 'html',
					beforeSend: function(){
					},
					success: function(msg){
						jQuery('#MT_Center_wr').html(msg);
						jQuery('#tosAccepted').hide();
					}
				});		
				
			}
		});
	});

//]]>
";
$document = JFactory::getDocument ();
$document->addScriptDeclaration ($box);
?>

<fieldset>
<table
	class="cart-summary"
	cellspacing="0"
	cellpadding="0"
	border="0"
	width="100%">
<tr>
	<th align="left">Товар</th>
	<th width="160"><?php echo JText::_ ('COM_VIRTUEMART_CART_QUANTITY') ?> / <?php echo JText::_ ('COM_VIRTUEMART_CART_ACTION') ?></th>
	<th width="120">Цена</th>	
	<th width="150">Сумма</th>
	<th width="50"></th>
</tr>



<?php
$i = 1;
// 		vmdebug('$this->cart->products',$this->cart->products);
foreach ($this->cart->products as $pkey => $prow) {
	?>
<tr valign="top" class="sectiontableentry<?php echo $i ?>">
	<td align="left">
		<?php if ($prow->virtuemart_media_id) { ?>
		<span class="cart-images">
						 <?php
			if (!empty($prow->image)) {
				echo $prow->image->displayMediaThumb ('', FALSE);
			}
			?>
						</span>
		<?php } ?>
		<h3><?php echo JHTML::link ($prow->url, $prow->product_name) . $prow->customfields; ?></h3>
		<span class="mf_name_cart"><?php echo $this->manufacturers_arr[$prow->virtuemart_manufacturer_id]->mf_name ?></span>

	</td>
	<td align="center"><?php
//				$step=$prow->min_order_level;
				if ($prow->step_order_level)
					$step=$prow->step_order_level;
				else
					$step=1;
				if($step==0)
					$step=1;
				$alert=JText::sprintf ('COM_VIRTUEMART_WRONG_AMOUNT_ADDED', $step);
				?>
                <script type="text/javascript">
				function check<?php echo $step?>(obj) {
 				// use the modulus operator '%' to see if there is a remainder
				remainder=obj.value % <?php echo $step?>;
				quantity=obj.value;
 				if (remainder  != 0) {
 					alert('<?php echo $alert?>!');
 					obj.value = quantity-remainder;
 					return false;
 				}
 				return true;
 				}
				
				</script>
		<form action="<?php echo JRoute::_ ('index.php'); ?>" method="post" class="inline">
			<input type="hidden" name="option" value="com_virtuemart"/>
				<?	//<input type="text" title="<?php echo  JText::_('COM_VIRTUEMART_CART_UPDATE') ?1>" class="inputbox" size="3" maxlength="4" name="quantity" value="<?php echo $prow->quantity ?1>" /> ?>
			<input type="button" class="quantity-controls quantity-minus"/>
			<input type="text"
				   onblur="check<?php echo $step?>(this);"
				   onclick="check<?php echo $step?>(this);"
				   onchange="check<?php echo $step?>(this);"
				   onsubmit="check<?php echo $step?>(this);"
				   title="<?php echo  JText::_('COM_VIRTUEMART_CART_UPDATE') ?>" class="quantity-input js-recalculate" size="3" maxlength="4" name="quantity" value="<?php echo $prow->quantity ?>" />
			<input type="button" class="quantity-controls quantity-plus"/>
			<input type="hidden" name="view" value="cart"/>
			<input type="hidden" name="task" value="update"/>
			<input type="hidden" name="cart_virtuemart_product_id" value="<?php echo $prow->cart_item_id  ?>"/>
			<input type="submit" class="vm2-add_quantity_cart" name="update" title="<?php echo  JText::_ ('COM_VIRTUEMART_CART_UPDATE') ?>" align="middle" value=" "/>
		</form>
		
	</td>
	<td align="center">
		<?php
		if (VmConfig::get ('checkout_show_origprice', 1) && $this->cart->pricesUnformatted[$pkey]['discountedPriceWithoutTax'] != $this->cart->pricesUnformatted[$pkey]['priceWithoutTax']) {
			echo '<span class="line-through">' . $this->currencyDisplay->createPriceDiv ('basePriceVariant', '', $this->cart->pricesUnformatted[$pkey], TRUE, FALSE) . '</span><br />';
		}
		if ($this->cart->pricesUnformatted[$pkey]['discountedPriceWithoutTax']) {
			echo $this->currencyDisplay->createPriceDiv ('discountedPriceWithoutTax', '', $this->cart->pricesUnformatted[$pkey], FALSE, FALSE);
		} else {
			echo $this->currencyDisplay->createPriceDiv ('basePriceVariant', '', $this->cart->pricesUnformatted[$pkey], FALSE, FALSE);
		}
		// 					echo $prow->salesPrice ;
		?>
	</td>
	

	<td align="center">
		<?php
		if (VmConfig::get ('checkout_show_origprice', 1) && !empty($this->cart->pricesUnformatted[$pkey]['basePriceWithTax']) && $this->cart->pricesUnformatted[$pkey]['basePriceWithTax'] != $this->cart->pricesUnformatted[$pkey]['salesPrice']) {
			echo '<span class="line-through">' . $this->currencyDisplay->createPriceDiv ('basePriceWithTax', '', $this->cart->pricesUnformatted[$pkey], TRUE, FALSE, $prow->quantity) . '</span><br />';
		}
		elseif (VmConfig::get ('checkout_show_origprice', 1) && empty($this->cart->pricesUnformatted[$pkey]['basePriceWithTax']) && $this->cart->pricesUnformatted[$pkey]['basePriceVariant'] != $this->cart->pricesUnformatted[$pkey]['salesPrice']) {
			echo '<span class="line-through">' . $this->currencyDisplay->createPriceDiv ('basePriceVariant', '', $this->cart->pricesUnformatted[$pkey], TRUE, FALSE, $prow->quantity) . '</span><br />';
		}
		echo $this->currencyDisplay->createPriceDiv ('salesPrice', '', $this->cart->pricesUnformatted[$pkey], FALSE, FALSE, $prow->quantity) ?></td>
	<td><a class="delFromCart" title="<?php echo JText::_ ('COM_VIRTUEMART_CART_DELETE') ?>" href="<?php echo JRoute::_ ('index.php?option=com_virtuemart&view=cart&task=delete&cart_virtuemart_product_id=' . $prow->cart_item_id) ?>" rel="nofollow">Удалить</a></td>
</tr>
	<?php
	$i = ($i==1) ? 2 : 1;
} ?>
<!--Begin of SubTotal, Tax, Shipment, Coupon Discount and Total listing -->
<? $colspan = 2; ?>

<?php

if ($this->checkout_task) {
$taskRoute = '&task=' . $this->checkout_task;
}
else {
$taskRoute = '';
}
if (VmConfig::get('oncheckout_opc', 1)) {
?>
<form method="post" id="checkoutForm" name="checkoutForm" action="<?php echo JRoute::_ ('index.php?option=com_virtuemart&view=cart' . $taskRoute, $this->useXHTML, $this->useSSL); ?>">
<?php } ?>
<?
$show_tr = 1;
if($show_tr == 1)	{
?>
<tr class="sectiontableentry2 totalSummRow">
	<td colspan="3" align="right"></td>
	
	<td align="center">Сумма: <?php echo $this->currencyDisplay->createPriceDiv ('billTotal', '', $this->cart->pricesUnformatted['billTotal'], FALSE); ?></td>
	<td></td>
</tr>
<?	}?>

<?php
if ($this->totalInPaymentCurrency) {
?>

<tr class="sectiontableentry2 totalInPaymentCurrency">
	<td colspan="4" align="right"><?php echo JText::_ ('COM_VIRTUEMART_CART_TOTAL_PAYMENT') ?>:</td>

	<?php if (VmConfig::get ('show_tax')) { ?>
	<td align="right"></td>
	<?php } ?>
	<td align="right"></td>
	<td align="right"><strong><?php echo $this->totalInPaymentCurrency;   ?></strong></td>
</tr>
	<?php
}
?>


</table>

</fieldset>
