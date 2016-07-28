<?php
defined ('_JEXEC') or die();
/**
 *
 * @package    VirtueMart
 * @subpackage Plugins  - Elements
 * @author Valérie Isaksen
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2011 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id:$
 */
/*
 * This class is used by VirtueMart Payment or Shipment Plugins
 * which uses JParameter
 * So It should be an extension of JElement
 * Those plugins cannot be configured througth the Plugin Manager anyway.
 */
class JElementgetcertificate extends JElement {

	/**
	 * Element name
	 *
	 * @access    protected
	 * @var        string
	 */
	var $_name = 'getcertificate';


		function fetchElement ($name, $value, &$node, $control_name) {

			jimport ('joomla.filesystem.folder');
			jimport ('joomla.filesystem.file');
			$lang = JFactory::getLanguage ();
			$lang->load ('com_virtuemart', JPATH_ADMINISTRATOR);
			// path to images directory
			$folder = $node->attributes ('directory');
			$safePath = VmConfig::get ('forSale_path', '');
			if (!JFolder::exists ($safePath)) {
				return JText::sprintf ('COM_VIRTUEMART_FOLDER_NOT_EXIST', $safePath);
			}
			$certificatePath=$safePath.$folder;
			if (!JFolder::exists ($certificatePath)) {
				if (!JFolder::create ($certificatePath)) {
					return JText::sprintf ('COM_VIRTUEMART_FOLDER_COULD_NOT_CREATE', $certificatePath);
				}
			}
			$path = str_replace ('/', DS, $certificatePath);
			$filter = $node->attributes ('filter');
			$exclude = array($node->attributes ('exclude'), '.svn', 'CVS', '.DS_Store', '__MACOSX', 'index.html');
			$pattern = implode ( "|", $exclude);
			$stripExt = $node->attributes ('stripext');


			$files = JFolder::files ($path, $filter, FALSE, FALSE, $exclude);

			$options = array();



			if (is_array ($files)) {
				foreach ($files as $file) {
					if ($exclude) {
						if (preg_match (chr (1) . $pattern . chr (1), $file)) {
							continue;
						}
					}
					if ($stripExt) {
						$file = JFile::stripExt ($file);
					}
					$options[] = JHTML::_ ('select.option', $file, $file);
				}
			}
			$class = ($node->attributes('class') ? 'class="' . $node->attributes('class') . '"' : '');
			$class .= ' size="5" data-placeholder="'.JText::_('COM_VIRTUEMART_DRDOWN_SELECT_SOME_OPTIONS').'"';
			return JHTML::_ ('select.genericlist', $options, '' . $control_name . '[' . $name . ']', $class, 'value', 'text', $value, $control_name . $name);
		}



}