<?php
/**
 *
 * Show the products in a category
 *
 * @package    VirtueMart
 * @subpackage
 * @author RolandD
 * @author Max Milbers
 * @todo add pagination
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: default.php 6556 2012-10-17 18:15:30Z kkmediaproduction $
 */

//vmdebug('$this->category',$this->category);
//vmdebug ('$this->category ' . $this->category->category_name);
// Check to ensure this file is included in Joomla!
defined ('_JEXEC') or die('Restricted access');
JHTML::_ ('behavior.modal');
/* javascript for list Slide
  Only here for the order list
  can be changed by the template maker
*/
$instock = JRequest::getVar('instock', 0) ? "&instock=1" : "";

$js = "


jQuery(document).ready(function () {
	jQuery('.orderlistcontainer').hover(
		function() { jQuery(this).find('.orderlist').stop().show()},
		function() { jQuery(this).find('.orderlist').stop().hide()}
	)
	
	
	var ClearFilterBtn = jQuery('#ClearFilterBtn').val();
	var FilterBtn = jQuery('#FilterBtn');
	var FilterUrl = '';
	
		FilterUrl = ClearFilterBtn;
		jQuery('.checkbox_item input').each(function() {
			if(jQuery(this).prop('checked'))	{
				FilterUrl = FilterUrl + jQuery(this).val();
			}
		});
		FilterUrl = FilterUrl.replace('&', '?');
		FilterBtn.attr('href',FilterUrl);
	
	
	jQuery('.checkbox_item input').live('change',function(e){
		//console.log(jQuery(this).val());
		FilterUrl = ClearFilterBtn;
		jQuery('.checkbox_item input').each(function() {
			if(jQuery(this).prop('checked'))	{
				FilterUrl = FilterUrl + jQuery(this).val();
			}
		});
		FilterUrl = FilterUrl.replace('&', '?');
		FilterBtn.attr('href',FilterUrl);
		
	});
	
	var width_1 = 0;
	jQuery('input[name=\"addtocart\"]').each(function(){
		if(jQuery(this).next('input').val() == 1)	{
			width_1 = jQuery(this).width() + 44;
			jQuery(this).val('В корзине');
			jQuery(this).width(width_1);
			jQuery(this).addClass('product_in_cart');
		}
		
	});
	
});
";

$document = JFactory::getDocument ();
$document->addScriptDeclaration ($js);

//echo'<pre>';print_r($this->document->base);echo'</pre>';


function createCustomSelects($db, $results, $customfields, $category_id)
{

	$var=JRequest::getvar('customfields', null);
	foreach($customfields as $custom_id => &$value){
		if($var && !empty($var[$custom_id])) 
			$value='&customfields['.$custom_id.']='.$var[ $custom_id];
		else unset($customfields[$custom_id]);
	}


	$value_ = JRequest::getvar('customfields', null);
	//echo'<pre>';print_r($value_);echo'</pre>';
	foreach($results as $custom_id => &$result){
		$ff=$customfields;
		unset($ff[$custom_id]); 
		$ff=implode('', $ff);

		$title=$result;
		$result='';

		$db->setQuery("SELECT DISTINCT(pcf.`custom_value`) AS value FROM `#__virtuemart_product_customfields` AS pcf
						INNER JOIN `#__virtuemart_product_categories` as pc USING(`virtuemart_product_id`)
						WHERE pc.`virtuemart_category_id` = $category_id AND virtuemart_custom_id = $custom_id");

/*						
		SELECT * FROM `m0wfo_virtuemart_product_customfields` as pcf
inner join `m0wfo_virtuemart_product_categories` as pc USING(`virtuemart_product_id`)
where `virtuemart_custom_id` = 6 AND pc.virtuemart_category_id = 2
*/



		$res=$db->loadObjectList();

		if(!is_array($res) || !count($res)) continue;

		//Массив значений
		//$a=array(0=>'Любой');
		$a=array();

		//Заносим данные из таблицы в массив значений
		foreach($res as $v) $a[$v->value]=$v->value; 

		//Сортируем 
		ksort($a);
		
		//echo'<pre>';print_r($a);echo'</pre>';

		//Получаем значение выбранного фильтра из запроса пользователя
		
		
		$value = (isset($value_[$custom_id])) ? ('&customfields['.$custom_id.']='.$value_[$custom_id]) : 'Любой';
		//echo'<pre>';print_r($value);echo'</pre>';
		
		//$state = array();
		//$state[] = JHTML::_('select.option', $value_ = (''), $text= JText::_( 'Любой' ));
		$checkboxes = '';
		$id_ = 1;
		foreach($a as $k => $v){
			if($k != '0')	{
				//$checked = (isset($value[$custom_id])) ? ' checked="checked"' : '';
				
				//if((isset($value_[$custom_id])) && ($value_[$custom_id]) == $v )	{
				$checked = '';
				if(isset($value_[$custom_id]))	{
					foreach($value_[$custom_id] as $val)	{
						//echo'<pre>';print_r($val.' | '.$v);echo'</pre>';
						if ($val == $v)	$checked = ' checked="checked"';
					}
				}
				
				$checkboxes .= '<div class="checkbox_item clearfix"><input type="checkbox" id="customfields_'.$custom_id.'_'.$id_.'" name="customfields['.$custom_id.'][]" value="'.(($k) ? '&customfields['.$custom_id.'][]='.$k : '').'"'.$checked.' /><label for="customfields_'.$custom_id.'_'.$id_.'">'.$v.'</label></div>';
				$id_++;
				//$state[] = JHTML::_('select.option', $value_ = (($k) ? '&customfields['.$custom_id.']='.$k : ''), $text= JText::_( $v ));
				// http://hitsvet.by/index.php/lyustry?customfields[4][]=%D0%9F%D0%BE%D0%B4%D0%B2%D0%B5%D1%81%D0%BD%D1%8B%D0%B5&customfields[4][]=%D0%9F%D0%BE%D1%82%D0%BE%D0%BB%D0%BE%D1%87%D0%BD%D1%8B%D0%B5&customfields[5][]=%D0%94%D0%B5%D1%82%D1%81%D0%BA%D0%B8%D0%B5&customfields[5][]=%D0%98%D0%B7%20%D1%80%D0%BE%D1%82%D0%B0%D0%BD%D0%B3%D0%B0
			}
		}
		$checkboxes = '<div class="filter_item"><div class="filter_item_title">'.$title.'</div>'.$checkboxes.'</div>';
							
		//$result = '<div class="orderlistcontainer"><div class="title">'.$title.'</div>'; 
		//$result .= JHTML::_('select.genericlist',  $state, $name = 'customfields['.$custom_id.']', $attribs = 'class="FilterSelect"', $key = 'value', $text = 'text', $selected = (($value) ? $value : 'Любой'), $idtag = false, $translate = false );
		//$result .= '</div>';
		$result .= $checkboxes;
	}
}


$format = JRequest::getVar('format','html');
if($format == 'html')	{
	$db=JFactory::getDbo();

	$db->setQuery('SELECT virtuemart_custom_id, custom_title FROM #__virtuemart_customs WHERE virtuemart_custom_id IN ('.$this->category->filter_ids.')'); 

	$custom_rows = $db->loadObjectList('virtuemart_custom_id');
	$filter_fields =	array();
	$customfields =	array();
	if(count($custom_rows) > 0)	{
		foreach($custom_rows as $row)	{
			$filter_fields[$row->virtuemart_custom_id] =	$row->custom_title;
			$customfields[$row->virtuemart_custom_id] =	0;	
		}
	}

	createCustomSelects(&$db, &$filter_fields, &$customfields, $this->category->virtuemart_category_id);

	//echo'<pre>';print_r($filter_fields);echo'</pre>';
	//echo'<pre>';print_r($this->category->virtuemart_category_id);echo'</pre>';
	?>


	<?
	if (empty($this->keyword) and !empty($this->category)) {
		?>

	<?php
	}

	/* Show child categories */

	if (VmConfig::get ('showCategory', 1) and empty($this->keyword)) {
		if (!empty($this->category->haschildren)) {

			// Category and Columns Counter
			$iCol = 1;
			$iCategory = 1;

			// Calculating Categories Per Row
			$categories_per_row = VmConfig::get ('categories_per_row', 3);
			$category_cellwidth = ' width' . floor (100 / $categories_per_row);

			// Separator
			$verticalseparator = " vertical-separator";
			?>

			<div class="category-view">

			<?php // Start the Output
			if (!empty($this->category->children)) {

				foreach ($this->category->children as $category) {

					// Show the horizontal seperator
					if ($iCol == 1 && $iCategory > $categories_per_row) {
						?>
						<div class="horizontal-separator"></div>
						<?php
					}

					// this is an indicator wether a row needs to be opened or not
					if ($iCol == 1) {
						?>
				<div class="row">
				<?php
					}

					// Show the vertical seperator
					if ($iCategory == $categories_per_row or $iCategory % $categories_per_row == 0) {
						$show_vertical_separator = ' ';
					} else {
						$show_vertical_separator = $verticalseparator;
					}

					// Category Link
					$caturl = JRoute::_ ('index.php?option=com_virtuemart&view=category&virtuemart_category_id=' . $category->virtuemart_category_id, FALSE);

					// Show Category
					?>
					<div class="category floatleft<?php echo $category_cellwidth . $show_vertical_separator ?>">
						<div class="spacer">
							<h2>
								<a href="<?php echo $caturl ?>" title="<?php echo $category->category_name ?>">
									<?php echo $category->category_name ?>
									<br/>
									<?php // if ($category->ids) {
									echo $category->images[0]->displayMediaThumb ("", FALSE);
									//} ?>
								</a>
							</h2>
						</div>
					</div>
					<?php
					$iCategory++;

					// Do we need to close the current row now?
					if ($iCol == $categories_per_row) {
						?>
						<div class="clear"></div>
			</div>
				<?php
						$iCol = 1;
					} else {
						$iCol++;
					}
				}
			}
			// Do we need a final closing row tag?
			if ($iCol != 1) {
				?>
				<div class="clear"></div>
			</div>
		<?php } ?>
		</div>

		<?php
		}
	}
	?>
	<div class="browse-view">
		<h1><span><?php echo $this->keyword ? 'Поиск по сайту' : $this->category->category_name; ?></span></h1>
	<?php

	if (!empty($this->keyword)) {
		?>
	<h3><?php echo $this->keyword; ?></h3>
		<?php
	} ?>
	
	<input type="hidden" id="instock_val" value="<?=$instock?>" />
	<input type="hidden" id="document_base" value="<?=$this->document->base?>" />
	<?php if ($this->search !== NULL) {

		$category_id  = JRequest::getInt ('virtuemart_category_id', 0); ?>
	<form action="<?php echo JRoute::_ ('index.php?option=com_virtuemart&view=category&limitstart=0', FALSE); ?>" method="get">

		<!--BEGIN Search Box -->
		<div class="virtuemart_search">
			<?php echo $this->searchcustom ?>
			<br/>
			<?php echo $this->searchcustomvalues ?>
			<input name="keyword" class="inputbox" type="text" size="20" value="<?php echo $this->keyword ?>"/>
			<input type="submit" value="Найти" class="orange_btn" onclick="this.form.keyword.focus();"/>
		</div>
		<input type="hidden" name="search" value="true"/>
		<input type="hidden" name="view" value="category"/>
		<input type="hidden" name="option" value="com_virtuemart"/>
		<input type="hidden" name="virtuemart_category_id" value="<?php echo $category_id; ?>"/>

	</form>
	<!-- End Search Box -->
		<?php } ?>
	<div class="l_part">
	<div class="l_part_wr">
		<h3>Подбор товара</h3>
		<div class="filter_item">
			<div class="checkbox_item clearfix">
				<input type="checkbox" id="instock" name="instock" value="&instock=1" <? echo(JRequest::getVar('instock', 0) ? 'checked="checked"' : '')?> ><label for="instock">В наличии</label>
			</div>
		</div>
		<?
			foreach($filter_fields as $i)	{
				echo $i;
			}
		?>
		<? $this_cal_url = JRoute::_('index.php?option=com_virtuemart&view=category&limitstart=0&virtuemart_category_id='.$this->category->virtuemart_category_id);	?>
		<input type="hidden" id="ClearFilterBtn" value="<?=$this_cal_url?>" />
		<a id="FilterBtn" class="orange_btn" href="<?=$this_cal_url?>">Подобрать товары</a>
	</div>
	</div>

	<div class="c_part">
	<div class="c_part_wr">
	<div class="c_part_WR">
	<?php // Show child categories
	if (!empty($this->products)) {
		include('products_list.php');
		?>
		


		<?php
	} elseif ($this->search !== NULL) {
		echo JText::_ ('COM_VIRTUEMART_NO_RESULT') . ($this->keyword ? ' : (' . $this->keyword . ')' : '');
	}
	?>
	</div>
	<!-- <div id="loadingbar_wr"><img  id="loadingbar" src="/images/system/uploadVK.gif" /></div> -->  <div class="category_description">
		<?php echo $this->category->category_description; ?>
	</div>
	</div>
	</div>
	</div>

<?
}	else	{
	include('products_list.php');
}

?>
