<?php
/**
*
* Review controller
*
* @package	VirtueMart
* @subpackage
* @author Max Milberes
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: ratings.php 6219 2012-07-04 16:10:42Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the controller framework
jimport('joomla.application.component.controller');

if (!class_exists ('VmController')){
	require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'vmcontroller.php');
}


/**
 * Review Controller
 *
 * @package    VirtueMart
 * @author Max Milbers
 */
class VirtuemartControllerImportproducts extends VmController {

	/**
	 * Method to display the view
	 *
	 * @access	public
	 */
	function __construct() {
		parent::__construct();
	}
	
	function import()	{
		$mainframe = Jfactory::getApplication();
		//echo 'task import';
		
		$zipFile = JRequest::getVar('zipFile', '', 'post');
		$xlsFile = JRequest::getVar('xlsFile', '', 'post');
		
		if($xlsFile == '')	{
			$msg = 'Отсутствует файл с данными.';
			$msgtype = 'error';
		}	else	{
			//echo '789798789879';
			$model = VmModel::getModel();
			$model->importProducts($xlsFile, $zipFile);
			$msg = 'Обновление цен успешно завершено.';
			$msgtype = '';
		}
		$mainframe->redirect('index.php?option=com_virtuemart&view=product', $msg, $msgtype);
	}	
}
// pure php no closing tag
