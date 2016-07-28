<?php
defined('_JEXEC') or die();

/**
 *
 * @package	VirtueMart
 * @subpackage Plugins  - Elements
 * @author Valérie Isaksen
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2011 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: getlanguage.php 7301 2013-10-29 17:45:07Z alatak $
 */
/*
 * This class is used by VirtueMart Payment or Shipment Plugins
 * which uses JParameter
 * So It should be an extension of JElement
 * Those plugins cannot be configured througth the Plugin Manager anyway.
 */
class JElementGetlanguage extends JElement {

    /**
     * Element name
     * @access	protected
     * @var		string
     */
    var $_name = 'getlanguage';

    function fetchElement($name, $value, &$node, $control_name) {

	    $languages = JLanguage::getKnownLanguages();
	    $fields = array();
	    $i = 0;
	    foreach ($languages as $language) {
		    $fields[$i]['value'] = strtolower($language['tag']);
		    $fields [$i]['text'] = $language['name'].' ('.$fields[$i]['value'] .')';
		    $i++;
	    }
        //$class = 'multiple="true" size="10"  ';
	    $class='';
        return JHTML::_('select.genericlist', $fields, $control_name . '[' . $name . ']', $class, 'value', 'text', $value, $control_name . $name);
    }

}