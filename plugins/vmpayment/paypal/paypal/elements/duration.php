<?php
/**
 *
 * Paypal  payment plugin
 *
 * @author Jeremy Magne
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


defined ('_JEXEC') or die();

class JElementDuration extends JElement {

	/**
	 * Element name
	 *
	 * @access    protected
	 * @var        string
	 */
	var $_name = 'Duration';

	function fetchElement ($name, $value, &$node, $control_name) {
		
		$class = ($node->attributes('class') ? $node->attributes('class') : '');
		$field_id = uniqid('duration');
		$duration_value_id = $field_id.'_value';
		$duration_unit_id = $field_id.'_unit';
		
		if ($value) {
			$duration_parts = explode('-',$value);
			$duration_value = $duration_parts[0];
			$duration_unit = $duration_parts[1];
		}

		$doc = JFactory::getDocument();

		$js = "
		jQuery().ready(function($) {
			$('#".$duration_value_id."').change(function() {
				$('#".$control_name . $name."').val($('#".$duration_value_id."').val()+'-'+$('#".$duration_unit_id."').val());
			});
			$('#".$duration_unit_id."').change(function() {
				$('#".$control_name . $name."').val($('#".$duration_value_id."').val()+'-'+$('#".$duration_unit_id."').val());
			});
		});";
		$doc->addScriptDeclaration($js );

		$options = array();
		$options[] = JHTML::_('select.option', 'D', JText::_('VMPAYMENT_PAYPAL_DURATION_D'));
		$options[] = JHTML::_('select.option', 'W', JText::_('VMPAYMENT_PAYPAL_DURATION_W'));
		$options[] = JHTML::_('select.option', 'M', JText::_('VMPAYMENT_PAYPAL_DURATION_M'));
		$options[] = JHTML::_('select.option', 'Y', JText::_('VMPAYMENT_PAYPAL_DURATION_Y'));

		$html = '<input type="text" style="width:25px;position:relative;font-size:14px;margin-right:10px;" name="'.$duration_value_id.'" id="'.$duration_value_id.'" value="'.$duration_value.'" >';
		$html .= JHTML::_ ('select.genericlist', $options, $duration_unit_id, '', 'value', 'text', $duration_unit, $duration_unit_id);
		$html .= '<input type="hidden" name="'.$control_name.'['.$name.']'.'" id="'.$control_name . $name.'" value="'.$value.'" class="'.$class.'" >';
		
		return $html;		
	}

}