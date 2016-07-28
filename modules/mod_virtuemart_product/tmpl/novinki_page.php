<?php // no direct access
defined('_JEXEC') or die('Restricted access');
vmJsApi::jPrice();
//echo"<pre>";print_r($products);echo"</pre>";

?>
<div class="vmgroup">
	
	<?php if ($headerText) { ?>
		<div class="vmheader"><?php echo $headerText ?></div>
	<?php } ?>

	<div class="vmproduct">

			<div class="browse-view clearfix">
			<?php foreach ($products as $product) { ?>
					<div class="product floatleft">
						<div class="spacer">
						<div class="spacer_wr">
							<div class="center">
								<?
									 if (!empty($product->images[0]) )
									 $image = $product->images[0]->displayMediaThumb('class="featuredProductImage" border="0"',false) ;
									 else $image = '';
									 $url = JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id='.$product->virtuemart_product_id.'&virtuemart_category_id='.$product->virtuemart_category_id);
									 echo JHTML::_('link', $url, $image,array('title' => $product->product_name) );
								?>
							</div>

							<div>
								<h3><a href="<?=$url?>"><?php echo $product->product_name ?></a></h3>					
								<div class="mf_name"><?=$product->mf_name?></div>

								<div class="product-price">
								 <?php
								if ($show_price and  isset($product->prices)) {
								 // 		echo $currency->priceDisplay($product->prices['salesPrice']);
								 if (!empty($product->prices['salesPrice'] ) ) echo $currency->createPriceDiv('salesPrice','',$product->prices);
								 // 		if ($product->prices['salesPriceWithDiscount']>0) echo $currency->priceDisplay($product->prices['salesPriceWithDiscount']);
								 if (!empty($product->prices['salesPriceWithDiscount']) ) echo $currency->createPriceDiv('salesPriceWithDiscount','',$product->prices,true);
								 }
								 if ($show_addtocart) echo mod_virtuemart_product::addtocart($product);
								 ?>
								</div>
							</div>
						</div>
						</div>
					</div>	
			 
			 

				<?php } ?>
			</div>
			
		<?php if ($footerText) { ?>
			<div class="vmheader"><?php echo $footerText ?></div>
		<?php } ?>
	</div>
</div>