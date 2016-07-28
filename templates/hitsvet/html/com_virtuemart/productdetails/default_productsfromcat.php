<?php
defined('_JEXEC') or die('Restricted access');

$iBrowseCol = 1;
$iBrowseProduct = 1;

// Calculating Products Per Row
$BrowseProducts_per_row = 4;
$Browsecellwidth = ' width' . floor (100 / $BrowseProducts_per_row);

// Separator
$verticalseparator = " vertical-separator";

$BrowseTotalProducts = count($this->products_from_cat);


?>
<div class="products_from_cat browse-view">
	<h2><span>Из этой же серии</span></h2>
    <?php
	//echo'<pre>';print_r($this->products_from_cat);echo'</pre>';
	foreach ($this->products_from_cat as $product) {	?>

		<?php

		// this is an indicator wether a row needs to be opened or not
		if ($iBrowseCol == 1) {
			?>
	<div class="row">
	<?php
		}

		// Show the vertical seperator
		if ($iBrowseProduct == $BrowseProducts_per_row or $iBrowseProduct % $BrowseProducts_per_row == 0) {
			$show_vertical_separator = ' ';
		} else {
			$show_vertical_separator = $verticalseparator;
		}

		// Show Products
		?>
		<div class="product floatleft<?php echo $Browsecellwidth . $show_vertical_separator ?>">
			<div class="spacer">
			<div class="spacer_wr">
				<div class="center">
				    <a title="<?php echo $product->product_name ?>" href="<?php echo $product->link; ?>">
						<?php echo $product->images[0]->displayMediaThumb('class="browseProductImage"', false);?>
					 </a>
				</div>

				<div class="">

					<h3><?php echo JHTML::link ($product->link, $product->product_name); ?></h3>
					
					<div class="mf_name"><?=$product->mf_name?></div>


					<div class="product-price marginbottom12" id="productPrice<?php echo $product->virtuemart_product_id ?>">
						<?php
						if ($this->show_prices == '1') {
							if ($product->prices['salesPrice']<=0 and VmConfig::get ('askprice', 1) and  !$product->images[0]->file_is_downloadable) {
								echo JText::_ ('COM_VIRTUEMART_PRODUCT_ASKPRICE');
							}
							//todo add config settings
							if ($this->showBasePrice) {
								echo $this->currency->createPriceDiv ('basePrice', 'COM_VIRTUEMART_PRODUCT_BASEPRICE', $product->prices);
								echo $this->currency->createPriceDiv ('basePriceVariant', 'COM_VIRTUEMART_PRODUCT_BASEPRICE_VARIANT', $product->prices);
							}
							echo $this->currency->createPriceDiv ('variantModification', 'COM_VIRTUEMART_PRODUCT_VARIANT_MOD', $product->prices);
							if (round($product->prices['basePriceWithTax'],$this->currency->_priceConfig['salesPrice'][1]) != $product->prices['salesPrice']) {
								echo '<div class="price-crossed" >' . $this->currency->createPriceDiv ('basePriceWithTax', 'COM_VIRTUEMART_PRODUCT_BASEPRICE_WITHTAX', $product->prices) . "</div>";
							}
							if (round($product->prices['salesPriceWithDiscount'],$this->currency->_priceConfig['salesPrice'][1]) != $product->prices['salesPrice']) {
								echo $this->currency->createPriceDiv ('salesPriceWithDiscount', 'COM_VIRTUEMART_PRODUCT_SALESPRICE_WITH_DISCOUNT', $product->prices);
							}
							//echo $this->currency->createPriceDiv ('salesPrice', 'COM_VIRTUEMART_PRODUCT_SALESPRICE', $product->prices);
							echo $this->currency->createPriceDiv ('salesPrice', '', $product->prices);
							if ($product->prices['discountedPriceWithoutTax'] != $product->prices['priceWithoutTax']) {
								echo $this->currency->createPriceDiv ('discountedPriceWithoutTax', 'COM_VIRTUEMART_PRODUCT_SALESPRICE_WITHOUT_TAX', $product->prices);
							} else {
								echo $this->currency->createPriceDiv ('priceWithoutTax', 'COM_VIRTUEMART_PRODUCT_SALESPRICE_WITHOUT_TAX', $product->prices);
							}
							echo $this->currency->createPriceDiv ('discountAmount', 'COM_VIRTUEMART_PRODUCT_DISCOUNT_AMOUNT', $product->prices);
							echo $this->currency->createPriceDiv ('taxAmount', 'COM_VIRTUEMART_PRODUCT_TAX_AMOUNT', $product->prices);
							$unitPriceDescription = JText::sprintf ('COM_VIRTUEMART_PRODUCT_UNITPRICE', $product->product_unit);
							echo $this->currency->createPriceDiv ('unitPrice', $unitPriceDescription, $product->prices);
						} ?>
						<div class="addtocart-area">
							<!--<form method="post" class="product js-recalculate" action="<?php /*echo JRoute::_ ('index.php'); */?>">
								<div class="addtocart-bar">
									<?/*//<input type="text" class="quantity-input js-recalculate" name="quantity[]" value="<?=$product->min_order_level ? $product->min_order_level : '1'?1>"/>	*/?>
									<input type="hidden" class="quantity-input" name="quantity[]" value="<?/*=$product->min_order_level ? $product->min_order_level : '1'*/?>"/>
									<span class="addtocart-button">
										<?php /*echo shopFunctionsF::getAddToCartButton ($product->orderable); */?>
									</span>
								</div>
								<input type="hidden" class="pname" value="<?php /*echo htmlentities($product->product_name, ENT_QUOTES, 'utf-8') */?>"/>
								<input type="hidden" name="option" value="com_virtuemart"/>
								<input type="hidden" name="view" value="cart"/>
								<noscript><input type="hidden" name="task" value="add"/></noscript>
								<input type="hidden" name="virtuemart_product_id[]" value="<?php /*echo $product->virtuemart_product_id */?>"/>
							</form>-->
							<!--<span class="addtocart-button">
								<input class="addtocart-button callme_viewform" type="submit" title="Узнать цену" value="Узнать цену" rel="nofollow" name="addtocart">
							</span>-->
						</div>
					</div>
				</div>
			</div>
			</div>
			<!-- end of spacer -->
		</div> <!-- end of product -->
		<?php

		// Do we need to close the current row now?
		if ($iBrowseCol == $BrowseProducts_per_row || $iBrowseProduct == $BrowseTotalProducts) {
			?>
			<div class="clear"></div>
   </div> <!-- end of row -->
			<?php
			$iBrowseCol = 1;
		} else {
			$iBrowseCol++;
		}

		$iBrowseProduct++;
	} // end of foreach ( $this->products as $product )
	?>
</div>