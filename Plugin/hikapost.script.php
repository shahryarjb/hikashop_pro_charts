<?php
 // no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.plugin.plugin'); 

class plgHikashopHikaProPriceChartInstallerScript
{

	function postflight($type, $parent) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('product_id');
		$query->from($db->qn('#__hikashop_product'));
		$query->where($db->qn('product_parent_id') . ' =  0');
		$db->setQuery((string)$query);
		$parent_id = $db->loadObjectlist();
		$query->clear();
		$query = $db->getQuery(true);
		$query->select('product_id,product_code,product_msrp,product_modified');
		$query->from($db->qn('#__hikashop_product'));
		foreach($parent_id as $key => $pId){
			$query->where($db->qn('product_parent_id') . ' = '. $db->q($pId->product_id));
			$db->setQuery((string)$query);
			$child_id[$pId->product_id] = $db->loadObjectlist();
		}
		
		foreach($child_id as $key => $value){
			if (count($child_id[$key]) < 1){
				$pInfo =$this::getProductInfo($key);
				$this::storeDate(0,$pInfo['product_id'],$pInfo['product_name'],$pInfo['product_msrp'],'main',$pInfo['product_modified']);
			}
			else {
				foreach($child_id[$key] as $c => $child){
					$productName = $this::getChildName($child->product_code,'');
					$this::storeDate($key,$child->product_id,$productName,$child->product_msrp,'variant',$child->product_modified);
				}
			}
		}	
	}
		
	function getProductInfo ($pId){
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('product_id,product_name,product_msrp,product_modified');
		$query->from($db->qn('#__hikashop_product'));
		$query->where($db->qn('product_id') . ' = '. $db->q($pId));
		$db->setQuery((string)$query);
		$result = $db->loadAssoc();
		return $result;
	}
	
	function getChildName ($name,$pId) {
		$ids = explode('_',str_replace('product_','',$name));
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('characteristic_value');
		$query->from($db->qn('#__hikashop_characteristic'));
		if (isset($pId) && $pId != null)
			$query->where($db->qn('characteristic_id') . ' = ' . $db->q($ids[1]) . ' AND ' . $db->qn('characteristic_parent_id') . ' = ' . $db->q($pId));
		else
			$query->where($db->qn('characteristic_id') . ' = ' . $db->q($ids[1]));
		$db->setQuery((string)$query);
		$result = $db->loadResult();
		return $result;
	}

	function storeDate ($parentId,$productId,$productName,$productPrice,$productType,$pTime) {
		$productTime = JFactory::getDate($pTime)->format('Y-m-d H:i:s');
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		if (isset($parentId) && $productType == 'variant'){
			$columns = array('parent_id','variant_id','variant_name','variant_price','date');
			$values =  array($db->q($parentId),$db->q($productId),$db->q($productName),$db->q($productPrice),$db->q($productTime));
			$query->insert($db->qn('#__trangel_hikashop_variant'));
		}
		else if ($productType == 'main') {
			$columns = array('product_id','product_name','product_price','date');
			$values =  array($db->q($productId),$db->q($productName),$db->q($productPrice),$db->q($productTime));
			$query->insert($db->qn('#__trangel_hikashop_product'));
		}
		$query->columns($db->qn($columns));
		$query->values(implode(',',$values)); 
		$db->setQuery((string)$query);
		$db->execute();
	}

} 

?>
