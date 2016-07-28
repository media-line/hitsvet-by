<?php
/**
*
* Category controller
*
* @package	VirtueMart
* @subpackage Category
* @author RickG, jseros
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: category.php 6071 2012-06-06 15:33:04Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the controller framework
jimport('joomla.application.component.controller');

if(!class_exists('VmController'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmcontroller.php');

/**
 * Category Controller
 *
 * @package    VirtueMart
 * @subpackage Category
 * @author jseros, Max Milbers
 */
class VirtuemartControllerCategory extends VmController {

	public function __construct() {
		parent::__construct();

	}

	/**
	 * We want to allow html so we need to overwrite some request data
	 *
	 * @author Max Milbers
	 */
	function save($data = 0){

		//ACL
		if (!JFactory::getUser()->authorise('vm.category.edit', 'com_virtuemart')) {
			JFactory::getApplication()->redirect( 'index.php?option=com_virtuemart', JText::_('JERROR_ALERTNOAUTHOR'), 'error');
		}
		
		$data = JRequest::get('post');

		$data['category_name'] = JRequest::getVar('category_name','','post','STRING',JREQUEST_ALLOWHTML);
		$data['category_description'] = JRequest::getVar('category_description','','post','STRING',JREQUEST_ALLOWHTML);
		//-----------------------------------------------------------------
		$this->updateChildCatsProds($data['virtuemart_category_id'], $data['filter_ids']);
		//-----------------------------------------------------------------

		parent::save($data);
	}


	/**
	* Save the category order
	*
	* @author jseros
	*/
	public function orderUp()
	{
		//ACL
		if (!JFactory::getUser()->authorise('vm.category.edit', 'com_virtuemart')) {
			JFactory::getApplication()->redirect( 'index.php?option=com_virtuemart', JText::_('JERROR_ALERTNOAUTHOR'), 'error');
		}

		// Check token
		JRequest::checkToken() or jexit( 'Invalid Token' );

		//capturing virtuemart_category_id
		$id = 0;
		$cid	= JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);

		if (isset($cid[0]) && $cid[0]) {
			$id = $cid[0];
		} else {
			$this->setRedirect( 'index.php?option=com_virtuemart&view=category', JText::_('COM_VIRTUEMART_NO_ITEMS_SELECTED') );
			return false;
		}

		//getting the model
		$model = VmModel::getModel('category');

		if ($model->orderCategory($id, -1)) {
			$msg = JText::_('COM_VIRTUEMART_ITEM_MOVED_UP');
		} else {
			$msg = $model->getError();
		}

		$this->setRedirect( 'index.php?option=com_virtuemart&view=category', $msg );
	}


	/**
	* Save the category order
	*
	* @author jseros
	*/
	public function orderDown()
	{
		//ACL
		if (!JFactory::getUser()->authorise('vm.category.edit', 'com_virtuemart')) {
			JFactory::getApplication()->redirect( 'index.php?option=com_virtuemart', JText::_('JERROR_ALERTNOAUTHOR'), 'error');
		}
		
		// Check token
		JRequest::checkToken() or jexit( 'Invalid Token' );

		//capturing virtuemart_category_id
		$id = 0;
		$cid	= JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);

		if (isset($cid[0]) && $cid[0]) {
			$id = $cid[0];
		} else {
			$this->setRedirect( 'index.php?option=com_virtuemart&view=category', JText::_('COM_VIRTUEMART_NO_ITEMS_SELECTED') );
			return false;
		}

		//getting the model
		$model = VmModel::getModel('category');

		if ($model->orderCategory($id, 1)) {
			$msg = JText::_('COM_VIRTUEMART_ITEM_MOVED_DOWN');
		} else {
			$msg = $model->getError();
		}

		$this->setRedirect( 'index.php?option=com_virtuemart&view=category', $msg );
	}


	/**
	* Save the categories order
	*/
	public function saveOrder()
	{
		//ACL
		if (!JFactory::getUser()->authorise('vm.category.edit', 'com_virtuemart')) {
			JFactory::getApplication()->redirect( 'index.php?option=com_virtuemart', JText::_('JERROR_ALERTNOAUTHOR'), 'error');
		}
		
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$cid	= JRequest::getVar( 'cid', array(), 'post', 'array' );	//is sanitized
		JArrayHelper::toInteger($cid);

		$model = VmModel::getModel('category');

		$order	= JRequest::getVar('order', array(), 'post', 'array');
		JArrayHelper::toInteger($order);

		if ($model->setOrder($cid,$order)) {
			$msg = JText::_('COM_VIRTUEMART_NEW_ORDERING_SAVED');
		} else {
			$msg = $model->getError();
		}
		$this->setRedirect('index.php?option=com_virtuemart&view=category', $msg );
	}
	
	//-----------------------------------------------------------------
	public function updateChildCatsProds($category_id, $filter_ids)
	{
			$cats = $category_id;
			$db_ =& JFactory::getDBO();
			$q = 'SELECT * FROM `#__virtuemart_category_categories` WHERE category_parent_id = '.$category_id;
			$db_->setQuery($q);

			$rows1 = $db_->loadObjectList();
			//echo'<pre>';print_r($rows1,0);echo'</pre>';

			foreach ($rows1 as $row1){
				$cats .= ','.$row1->category_child_id;
				$q = 'SELECT * FROM `#__virtuemart_category_categories` WHERE category_parent_id = '.$row1->category_child_id;
				$db_->setQuery($q);
				$rows2 = $db_->loadObjectList();
				//echo'<pre>';print_r($rows2,0);echo'</pre>';
				foreach ($rows2 as $row2){
					$cats .= ','.$row2->category_child_id;
					$q = 'SELECT * FROM `#__virtuemart_category_categories` WHERE category_parent_id = '.$row2->category_child_id;
					$db_->setQuery($q);
					$rows3 = $db_->loadObjectList();
					//echo'<pre>';print_r($rows3,0);echo'</pre>';
					foreach ($rows3 as $row3){
						$cats .= ','.$row3->category_child_id;
						$q = 'SELECT * FROM `#__virtuemart_category_categories` WHERE category_parent_id = '.$row3->category_child_id;
						$db_->setQuery($q);
						$rows4 = $db_->loadObjectList();
						//echo'<pre>';print_r($rows3,0);echo'</pre>';
						foreach ($rows4 as $row4){
							$cats .= ','.$row4->category_child_id;
							$q = 'SELECT * FROM `#__virtuemart_category_categories` WHERE category_parent_id = '.$row4->category_child_id;
							$db_->setQuery($q);
							$rows5 = $db_->loadObjectList();
							//echo'<pre>';print_r($rows5,0);echo'</pre>';
							foreach ($rows5 as $row5){
								$cats .= ','.$row5->category_child_id;
								$q = 'SELECT * FROM `#__virtuemart_category_categories` WHERE category_parent_id = '.$row5->category_child_id;
								$db_->setQuery($q);
								$rows6 = $db_->loadObjectList();
								//echo'<pre>';print_r($rows4,0);echo'</pre>';
								foreach ($rows6 as $row6){
									$cats .= ','.$row6->category_child_id;
								}
							}
						}
					}
				}
			}
			
			//echo'<pre>';print_r($cats,0);echo'</pre>';
			
			$q = "UPDATE `#__virtuemart_categories` SET `filter_ids` = '$filter_ids' WHERE `virtuemart_category_id` IN ($cats)";
			$db_->setQuery($q);
			$db_->Query();

	}
	//-----------------------------------------------------------------

}
