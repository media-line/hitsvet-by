<?php
defined ('_JEXEC') or die('Restricted access');

if (!class_exists ('VmModel')) {
	require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'vmmodel.php');
}

// JTable::addIncludePath(JPATH_VM_ADMINISTRATOR.DS.'tables');
/**
 * Model for VirtueMart Products
 *
 * @package VirtueMart
 * @author RolandD
 * @todo Replace getOrderUp and getOrderDown with JTable move function. This requires the vm_product_category_xref table to replace the ordering with the ordering column
 */
class VirtueMartModelImportproducts extends VmModel {
	
	public function removeFiles($files,$file_path) {
		foreach($files as $file){
			unlink($file_path.$file);
		}
	}
	
	public function importProducts($xlsFile = '', $zipFile = '') {
		$vmimport_path = JPATH_SITE.DS.'tmp'.DS.'vmimport'.DS;
		//echo'<pre>';print_r($vmimport_path,0);echo'</pre>';
		
		
		$file_path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'assets'.DS;
		require_once($file_path.'PHPExcel.php');

		// Подключаем класс для вывода данных в формате excel
		require_once($file_path.'PHPExcel'.DS.'IOFactory.php');		

		$objPHPExcel = PHPExcel_IOFactory::load($vmimport_path.$xlsFile);
		$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
		 
		$db = JFactory::getDbo();
		$product_info = array();
		$user = &JFactory::getUser();
		
		$jnow = &JFactory::getDate();
		$now = $jnow->toMySQL();
		
		$lang = JFactory::getLanguage();
		
		if($zipFile != '')	{
			$this->extractZip($vmimport_path, $zipFile);
		}
		
		$price_array = array();
		//$in_stock_array();

		for ($i = 2; $i <= count($sheetData); $i++) {
			$sheetData[$i]['B'] = str_replace('.', ',', $sheetData[$i]['B']);
			//echo'<pre>';print_r($sheetData[$i]);echo'</pre>';
			$price_array[] = array	(
										'product_sku' => $sheetData[$i]['B'],
										'product_price' => (int)$sheetData[$i]['I'],
										'product_price_0' => (int)$sheetData[$i]['F'],
										'product_price_5' => (int)$sheetData[$i]['G'],
										'product_price_10' => (int)$sheetData[$i]['H'],
										'product_in_stock' => $sheetData[$i]['D'],
									);
								
			//echo'<pre>';print_r($sheetData[$i]['A'] .' | '. $sheetData[$i]['B']);echo'</pre>';
		}
		//echo'<pre>';print_r($price_array);echo'</pre>';die;
		$this->goUpdate($db, $price_array, $now);		
		
		$this->cleanDir(JPATH_SITE.DS.'tmp'.DS.'vmimport');
	}
	
	function goUpdate(&$db, $price_array, $now)
	{
		$product_sku_str = '';
		if(count($price_array))	{
		
			// обновляем количества позиций на складе.
			$values = "product_in_stock = -1";	// это соответствует статусу "Под заказ"
			$where = 1;
			$this->executeUpdate($db, "virtuemart_products", $values, $where);
		
			foreach($price_array as $i)	{
				$product_sku_str .= "'".$i['product_sku']."', ";
			}
		
			$product_sku_str = substr($product_sku_str, 0, -2);
			

			$query = $db->getQuery(true);		 
			$query->select(array('virtuemart_product_id','product_sku'));
			$query->from('#__virtuemart_products');
			$query->where("product_sku IN ($product_sku_str)");
			$db->setQuery($query);
			 
			$results = $db->loadObjectList('virtuemart_product_id');
			
			//echo'<pre>';print_r($product_sku_str,0);echo'</pre>';
			//echo'<pre>';print_r($results,0);echo'</pre>';
			//echo'<pre>';print_r($price_array,0);echo'</pre>';
			
			$fields = "`virtuemart_product_id`, `virtuemart_shoppergroup_id`,`product_price`, `override`, `product_override_price`, `product_tax_id`, `product_discount_id`, `product_currency`, `product_price_publish_up`, `product_price_publish_down`, `price_quantity_start`, `price_quantity_end`, `created_on`, `created_by`";
			$values = "";
			$values_0 = "";
			$values_5 = "";
			$values_10 = "";
			foreach($results as $result)	{
				foreach($price_array as $price_i)	{
					if($price_i['product_sku'] == $result->product_sku)	{
						//$values .= "(".$result->virtuemart_product_id.", 0, ".$price_i['product_price'].", 0, 0.00000, 0, 0, 194, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, '".$now."', 452),";
						
						$values .= "(".$result->virtuemart_product_id.", 1, ".$price_i['product_price'].", 0, 0.00000, 0, 0, 194, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, '".$now."', 452),";
						
						$values .= "(".$result->virtuemart_product_id.", 2, ".$price_i['product_price'].", 0, 0.00000, 0, 0, 194, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, '".$now."', 452),";
						
						
						//echo'<pre>';print_r($price_i['product_price_5']);echo'</pre>';
						//echo'<pre>';print_r($values);echo'</pre>';
						
						if($price_i['product_price_0'] != 0)	{
							$values_0 .= "(".$result->virtuemart_product_id.", 3, ".$price_i['product_price_0'].", 0, 0.00000, 0, 0, 194, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, '".$now."', 452),";							
						}
						
						if($price_i['product_price_5'] != 0)	{
							$values_5 .= "(".$result->virtuemart_product_id.", 4, ".$price_i['product_price_5'].", 0, 0.00000, 0, 0, 194, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, '".$now."', 452),";							
						}
						
						if($price_i['product_price_10'] != 0)	{
							$values_10 .= "(".$result->virtuemart_product_id.", 5, ".$price_i['product_price_10'].", 0, 0.00000, 0, 0, 194, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, '".$now."', 452),";
						}
						
						if($price_i['product_in_stock'] != '')	{
							$values1 = "`product_in_stock` = ".$price_i['product_in_stock'];	// 
							$where = "`virtuemart_product_id` = ".$result->virtuemart_product_id;
							$this->executeUpdate($db, "virtuemart_products", $values1, $where);
						}
						
						//break;
					}
				}
			}
			//echo'values5 = <pre>';print_r($values_5);echo'</pre>';
			//echo'values10 = <pre>';print_r($values_10);echo'</pre>';die;
			$values = substr($values,0,-1);
			$values_0 = substr($values_0,0,-1);
			$values_5 = substr($values_5,0,-1);
			$values_10 = substr($values_10,0,-1);
			
			//echo'<pre>values = ';print_r($values);echo'</pre>';die;
			
			//чистим таблицу перед обновлением
			$query = "TRUNCATE TABLE `#__virtuemart_product_prices`";
			$db->setQuery($query);
			$db->Query();
			//обновляем цены
			$this->executeInsert($db, "virtuemart_product_prices", $fields, $values);
			$this->executeInsert($db, "virtuemart_product_prices", $fields, $values_0);
			$this->executeInsert($db, "virtuemart_product_prices", $fields, $values_5);
			$this->executeInsert($db, "virtuemart_product_prices", $fields, $values_10);
			
			
			
			
		}
	}
		
	function executeInsert (&$db, $tbl_name, $fields, $values) {
		$query = "INSERT INTO `#__$tbl_name` ($fields) VALUES $values";
		$db->setQuery($query);
		$db->Query();
		
		$errMsg = $db->getErrorMsg ();
		$errs = $db->getErrors ();

		if (!empty($errMsg)) {
			$app = JFactory::getApplication ();
			$errNum = $db->getErrorNum ();
			$app->enqueueMessage ('SQL-Error: ' . $errNum . ' ' . $errMsg);
		}
		if ($errs) {
			$app = JFactory::getApplication ();
			foreach ($errs as $err) {
				$app->enqueueMessage ($err);
			}
		}
	}
	
		
	function executeUpdate (&$db, $tbl_name, $values, $where) {
		$query = "UPDATE `#__$tbl_name` SET $values WHERE $where";
		//echo'<pre>';print_r($query,0);echo'</pre>';
		$db->setQuery($query);
		$db->Query();
		
		$errMsg = $db->getErrorMsg ();
		$errs = $db->getErrors ();

		if (!empty($errMsg)) {
			$app = JFactory::getApplication ();
			$errNum = $db->getErrorNum ();
			$app->enqueueMessage ('SQL-Error: ' . $errNum . ' ' . $errMsg);
		}
		if ($errs) {
			$app = JFactory::getApplication ();
			foreach ($errs as $err) {
				$app->enqueueMessage ($err);
			}
		}	
	}
	
	function executeDelete (&$db, $tbl_name, $where) {
		$query = "DELETE FROM `#__$tbl_name` WHERE $where";
		$db->setQuery($query);
		$db->Query();
		
		$errMsg = $db->getErrorMsg ();
		$errs = $db->getErrors ();

		if (!empty($errMsg)) {
			$app = JFactory::getApplication ();
			$errNum = $db->getErrorNum ();
			$app->enqueueMessage ('SQL-Error: ' . $errNum . ' ' . $errMsg);
		}
		if ($errs) {
			$app = JFactory::getApplication ();
			foreach ($errs as $err) {
				$app->enqueueMessage ($err);
			}
		}
	}
	
	
	function extractZip ($path, $zipFile) {
		$zip = new ZipArchive;
		$res = $zip->open($path.$zipFile);
		if ($res === TRUE) {
		  $zip->extractTo($path);
		  $zip->close();
		  //echo 'ok';
		} else {
		  //echo 'failed';
		}	
	}
	
	private function isImage($file_extension=0)	{
		if($file_extension == 'jpg' || $file_extension == 'jpeg' || $file_extension == 'png' || $file_extension == 'gif'){
			$isImage = TRUE;
		} else {
			$isImage = FALSE;
		}
		return $isImage;
	}
	
	function cleanDir($dir) {
		$files = glob($dir."/*");
		$c = count($files);
		if (count($files) > 0) {
			foreach ($files as $file) {      
				if (file_exists($file)) {
				unlink($file);
				}   
			}
		}
	}
	
	function createSlug($product_name, &$lang) {
		$slug_ = $lang->transliterate($product_name);
		$slug_ = JApplication::stringURLSafe($slug_);
		return $slug_;
	}
	
}
