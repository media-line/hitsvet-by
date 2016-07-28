<?php
/**
 * @version $Id: getsofort.php 7200 2013-09-16 15:00:06Z alatak $
 *
 * @author Valérie Isaksen
 * @package VirtueMart
 * @copyright Copyright (c) 2004 - 2012 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */
defined ('JPATH_BASE') or die();

/**
 * Renders a label element
 */

class JElementGetSofort extends JElement {

	/**
	 * Element name
	 *
	 * @access    protected
	 * @var        string
	 */
	var $_name = 'getSofort';

	function fetchElement ($name, $value, &$node, $control_name) {

		$jlang = JFactory::getLanguage ();
		$lang = $jlang->getTag ();
		$langArray = explode ("-", $lang);
		$lang = strtolower ($langArray[1]);
		$getSofortLang='eng-DE';
		 if ($lang=='de') {
			 $getSofortLang = "ger-DE";
	}

		// MOre information
	$getSofortLInk="https://www.sofort.com/".$getSofortLang."/merchant/products/";
		//$getSofortLInk="https://www.sofort.com/payment/users/register/688";
		$html = '<a href="#" id="sofortmoreinfo_link" ">' . JText::_ ('VMPAYMENT_SOFORT_READMORE') . '</a>';
		$html .= '<div id="sofortmoreinfo_show_hide" >';

		$js = '
		jQuery(document).ready(function( $ ) {
			$("#sofortmoreinfo_show_hide").hide();
			jQuery("#sofortmoreinfo_link").click( function() {
				 if ( $("#sofortmoreinfo_show_hide").is(":visible") ) {
				  $("#sofortmoreinfo_show_hide").hide("slow");
			        $("#sofortmoreinfo_link").html("' . addslashes (JText::_ ('VMPAYMENT_SOFORT_READMORE')) . '");
				} else {
				 $("#sofortmoreinfo_show_hide").show("slow");
			       $("#sofortmoreinfo_link").html("' . addslashes (JText::_ ('VMPAYMENT_SOFORT_HIDE')) . '");
			    }
		    });
		});
';

		$doc = JFactory::getDocument ();
		$doc->addScriptDeclaration ($js);

		$html .= '<iframe src="' . $getSofortLInk . '" scrolling="yes" style="x-overflow: none;" frameborder="0" height="500px" width="850px"></iframe>';
		$html .= '</div>';
// Get Sofort

		// MOre information
		$getSofortLInk="https://www.sofort.com/payment/users/register/688";
		$html .= '<div><a target="_blank" href="'.$getSofortLInk.'" id="getsogort_link" ">' . JText::_ ('VMPAYMENT_SOFORT_REGISTERNOW') . '</a>';
		$html .= '</div>';

		return $html;
	}



}