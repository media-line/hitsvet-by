<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage	ratings
* @author
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: view.html.php 6219 2012-07-04 16:10:42Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the view framework
if(!class_exists('VmView'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmview.php');

class VirtuemartViewImportproducts extends VmView {

	function display($tpl = null) {

		$mainframe = Jfactory::getApplication();
		$document = &JFactory::getDocument();		
		$option = JRequest::getWord('option');
		$main_part = JUri::base().'components'.DS.'com_virtuemart'.DS.'assets'.DS.'fancyupload'.DS;
		/*
		
		$document->addScript('http://ajax.googleapis.com/ajax/libs/mootools/1.2.2/mootools.js');
		$document->addScript($main_part.'source'.DS.'Fx.ProgressBar.js');
		$document->addScript($main_part.'source'.DS.'Swiff.Uploader.js');
		$document->addScript($main_part.'source'.DS.'FancyUpload3.Attach.js');
		*/
		$document->addStyleSheet($main_part.'assets'.DS.'css'.DS.'style.css');
		

		//Load helpers


		$this->loadHelper('html');
		$model = VmModel::getModel();
		$this->SetViewTitle('IMPORT_PRODUCTS');
		$this->addStandardDefaultViewCommands(false, true);
		
		$file_path2 = 'components'.DS.'com_virtuemart'.DS.'files'.DS;
		$file_path = JPATH_SITE.DS.$file_path2;
		
		$file_list = glob($file_path."*.xls");
		//$file_list_png = glob("./components/com_vmimports/files/*.xls");
		
		//$file_list = array_merge($file_list_png);
		//echo'<pre>';print_r($file_list_png,0);echo'</pre>';
		//$file_list = $file_list_png;
		$file_list = array_reverse($file_list);
		//echo'<pre>';print_r($file_list,0);echo'</pre>';
		//$url = JUri::base().$file_path2;
		$url = DS.$file_path2;
		
		$vmimport_path_ = JPATH_SITE.DS.'tmp'.DS.'vmimport';
		if(!file_exists($vmimport_path_)) mkdir($vmimport_path_,0777,true); //если каталог для пользователя не создан - создаем его.
		
		
		$this->assignRef('main_part',$main_part);
		$this->assignRef('url',$url);
		
		
		parent::display($tpl);
		
	}

}
// pure php no closing tag
