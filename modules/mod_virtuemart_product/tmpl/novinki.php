<?php // no direct access
defined('_JEXEC') or die('Restricted access');
vmJsApi::jPrice();
//echo"<pre>";print_r($products);echo"</pre>";


$document = &JFactory::getDocument();
/*
$document->addScript('http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js');
$document->addScript('http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js');
$document->addScript('/modules/mod_virtuemart_product/assets/js/jquery.easing.1.3.js');
$document->addScript('/modules/mod_virtuemart_product/assets/js/jquery.mousewheel.min.js');
$document->addScript('/modules/mod_virtuemart_product/assets/js/jquery.mCustomScrollbar.js');
*/
$document->addStyleSheet('/modules/mod_virtuemart_product/assets/css/jquery.mCustomScrollbar.css');

//echo'<pre>';print_r($module->title);echo'</pre>';
?>
<div class="vmgroup<?php echo $params->get( 'moduleclass_sfx' ) ?>">
<h3 class="like_h1"><span><?=$module->title?></span></h3>
<a class="allNovinki" href="<?=JRoute::_('index.php?Itemid=133')?>">Все новинки</a>
<?php if ($headerText) { ?>
	<div class="vmheader"><?php echo $headerText ?></div>
<?php } ?>

<div class="vmproduct<?php echo $params->get('moduleclass_sfx'); ?>">

<div id="mcs5_container">
	<div class="customScrollBox">
		<div class="horWrapper">
			<div class="container">
				<div class="content">
					<p>
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
					</p>
				</div>
			</div>
		</div>
		<div class="dragger_container">
			<div class="dragger">
			</div>
		</div>
	</div>
	<!-- кнопки прокрутки содержания -->
	<a class="scrollUpBtn" href="#"></a>
	<a class="scrollDownBtn" href="#"></a>
</div>
		
<?php if ($footerText) { ?>
	<div class="vmheader"><?php echo $footerText ?></div>
<?php } ?>
</div>
</div>
<script>
jQuery(window).load(function() {
	mCustomScrollbars();
	set_height();
});

function mCustomScrollbars(){
	/* 
	Параметры плагина CustomScrollbar: 
	1) Тип прокрутки (значение: "vertical" или "horizontal")
	2) Величина перемещения со сглаживанием (0 - сглаживание не используется) 
	3) Тип сглаживания перемещений 
	4) Дополнительное место снизу, только для вертикального типа прокрутки (минимальное значение: 1)
	5) Настройка высоты/ширины панели прокрутки (значение: "auto" или "fixed")
	6) Поддержка прокрутки колесиком мыши (значение: "yes" или "no")
	7) Прокрутка с помощью клавиш (значения: "yes" или "no")
	8) Скорость прокрутки (значение: 1-20, 1 соответствует самой медленной скорости)
	*/
	/*
	$("#mcs_container").mCustomScrollbar("vertical",400,"easeOutCirc",1.05,"auto","yes","yes",10); 
	$("#mcs2_container").mCustomScrollbar("vertical",0,"easeOutCirc",1.05,"auto","yes","no",0); 
	$("#mcs3_container").mCustomScrollbar("vertical",900,"easeOutCirc",1.05,"auto","no","no",0); 
	$("#mcs4_container").mCustomScrollbar("vertical",200,"easeOutCirc",1.25,"fixed","yes","no",0); 
	*/
	$("#mcs5_container").mCustomScrollbar("horizontal",500,"easeOutCirc",1,"fixed","yes","yes",20); 
}


/* Функция для обхода ошибки с 10000 px для jquery.animate */
/*
$.fx.prototype.cur = function(){
    if ( this.elem[this.prop] != null && (!this.elem.style || this.elem.style[this.prop] == null) ) {
      return this.elem[ this.prop ];
    }
    var r = parseFloat( jQuery.css( this.elem, this.prop ) );
    return typeof r == 'undefined' ? 0 : r;
}
*/
/* Функция для динамической загрузки содержания */
/*
function LoadNewContent(id,file){
	$("#"+id+" .customScrollBox .content").load(file,function(){
		mCustomScrollbars();
	});
}
*/


	function set_height()	{
		var max_height_item = 0;
		max_height_item = 0;
		$('.customScrollBox h3').each(function(){
			if($(this).height() > max_height_item)	{
				max_height_item = $(this).height();
			}
		});
		//console.log(max_height_item);
		$('.customScrollBox h3').css('height',max_height_item);
	}



</script>
