<?php
/**
 *
 * Show the product details page
 *
 * @package	VirtueMart
 * @subpackage
 * @author Max Milbers, Valerie Isaksen

 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: default_customfields.php 5699 2012-03-22 08:26:48Z ondrejspilka $
 */

// Check to ensure this file is included in Joomla!
defined ( '_JEXEC' ) or die ( 'Restricted access' );
?>
<div class="product-fields clearfix">
	<div class="product-field product-field-type-S">
		<span class="product-fields-title">Артикул:</span>
		<span class="product-field-display"><?=$this->product->product_sku?></span>
	</div>
	<?
	/*
	<div class="product-field product-field-type-S">
		<span class="product-fields-title">Тип светильника:</span>
		<span class="product-field-display"><?=$this->product->category_name?></span>
	</div>
	<div class="product-field product-field-type-S">
		<span class="product-fields-title">Высота (см):</span>
		<span class="product-field-display"><?=(int)$this->product->product_height?></span>
	</div>
	<div class="product-field product-field-type-S">
		<span class="product-fields-title">Габариты (см):</span>
		<span class="product-field-display"><?=(int)$this->product->product_width.'*'.(int)$this->product->product_length?></span>
	</div>
	*/
	?>
	    <?php
	    $custom_title = null;
	    foreach ($this->product->customfieldsSorted[$this->position] as $field) {
	    	if ( $field->is_hidden ) //OSP http://forum.virtuemart.net/index.php?topic=99320.0
	    		continue;
			if ($field->display) {
	    ?><div class="product-field product-field-type-<?php echo $field->field_type ?>">
		    <?php if ($field->custom_title != $custom_title && $field->show_title) { ?>
			    <span class="product-fields-title" ><?php echo JText::_($field->custom_title); ?>:</span>
			    <?php
			    if ($field->custom_tip)
				echo JHTML::tooltip($field->custom_tip, JText::_($field->custom_title), 'tooltip.png');
			}
			?>
	    	    <span class="product-field-display"><?php echo $field->custom_value //echo $field->display ?></span>
	    	    <?if($field->custom_field_desc)	{?>
					<span class="product-field-desc"><?php echo JText::_($field->custom_field_desc) ?></span>
				<?	}?>
	    	</div>
		    <?php
		    $custom_title = $field->custom_title;
			}
	    }
	    ?>
</div>
