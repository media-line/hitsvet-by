<?php
#####################################################################################################
#
#					Module pour la plateforme de paiement Systempay
#						Version : 1.3c (révision 46701)
#									########################
#					Développé pour VirtueMart
#						Version : 2.0.8
#						Compatibilité plateforme : V2
#									########################
#					Développé par Lyra Network
#						http://www.lyra-network.com/
#						29/04/2013
#						Contact : supportvad@lyra-network.com
#
#####################################################################################################

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

/**
 * Renders a label element
 */

class JElementReferringurl extends JElement
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'referringurl';

	function fetchElement($name, $value, &$node, $control_name)
	{

			$value= JURI::root().'plugins/vmpayment/realex/jump.php';

		$class = ( $node->attributes('class') ? 'class="'.$node->attributes('class').'"' : 'class="text_area"' );
		return '<label for="'.$name.'"'.$class.'>'.$value.'</label>';
	}
}