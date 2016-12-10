<?php
defined('_JEXEC') or die('Restricted access');

//Load the Plugin language file out of the administration
$lang = & JFactory::getLanguage();
$lang->load('plg_hikashop_HikaBackPriceChart', JPATH_ADMINISTRATOR);
require_once JPATH_PLUGINS .'/hikashop/HikaProPriceChart/jdf.php';

// You need to extend from the hikashopPaymentPlugin class which already define lots of functions in order to simplify your work
Class plgHikashopHikaBackPriceChart extends JPlugin
{

function onHikashopAfterDisplayView(&$view) {
	JHtml::script('https://github.com/chartjs/Chart.js/releases/download/v2.4.0/Chart.bundle.min.js'); 
	JHtml::script(JPATH_PLUGINS.'/hikashop/HikaProPriceChart/jquery.slModal.js'); 
	JHtml::stylesheet('https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css');
		JHtml::stylesheet(JPATH_PLUGINS.'/hikashop/HikaProPriceChart/jquery.slModal.css');
	if (JRequest::getVar('option')==='com_hikashop' AND JRequest::getVar('ctrl')==='product' AND JRequest::getVar('task')==='show') {
		if($view->getName() == 'product') {
			if($view->getLayout() == 'show_quantity') { 
				$productId = hikashop_getCID('product_id');
				if ($this::getٰVariantId($productId) != false)	{	// start if
						foreach($this::getٰVariantId($productId) as $variant){
							$variantData[] = $this::prepareDateChart($variant,'hours','variant');
						}

		
						for($i = 0 ; $i < count($variantData) ; $i++){
							$newDate[] = $variantData[$i][0];
							$newPrice[] = $variantData[$i][1];
							$newName[] =  $variantData[$i][2];
						}
						for($k= 0 ; $k < count($newDate) ; $k++){   // convert to array
							$toArrayDate[] = explode(',',$newDate[$k]); 
							$toArrayPrice[] = explode(',',$newPrice[$k]);
						}
						
						for($l=0 ; $l < count($toArrayDate) ; $l++){
							for($n= 0 ;$n < count($toArrayDate[$l]);$n++){
								$mergData[$l][$toArrayDate[$l][$n]] = $toArrayPrice[$l][$n]; 
							}
						}
						for($q = 0 ; $q <count ($mergData);$q++){ // for sorting
							arsort($mergData[$q]);
						}
						

						for($p=0 ; $p <count($toArrayDate) ; $p++){
							for($pp = 0 ;$pp < count($toArrayDate[$p]); $pp++){
								$jDate[] = jdate("o/m/j",$toArrayDate[$p][$pp],'','','en');
								$gDate[] = JFactory::getDate($toArrayDate[$p][$pp])->format('Y/m/d');
							}
						}
						
						$allJDate = array_unique(explode(',',implode(',',$jDate))); // یکپارچه سازی تاریخ ها و حذف تاریخ های تکراری
						sort($allJDate);
						$allGDate = array_unique(explode(',',implode(',',$gDate))); // یکپارچه سازی تاریخ ها و حذف تاریخ های تکراری
						sort($allGDate);
						$nallDate = implode(',',$allJDate); // یکپارچه سازی تاریخ ها و حذف تاریخ های تکراری
						$countMergData = count($mergData); // 4
						$countGData = count($allGDate); // 2
					
						for($vv=0;$vv<$countMergData; $vv++){
							foreach($mergData[$vv] as $key => $md){
								$getDArray[$vv][] = JFactory::getDate($key)->format('Y/m/d');
								$getVArray[$vv][] = $md;
							}
						}
						for($v=0;$v<$countMergData; $v++){
							if (count($getDArray[$v]) > 1){
								$setArray[] = array_unique($getDArray[$v]);
							}
							else {
								$setArray[] = $getDArray[$v];
							}
						}

						for($vvv=0;$vvv<count($setArray); $vvv++){	
							foreach($setArray[$vvv] as $key => $value) {
								$mergNData[$vvv][$value] = $getVArray[$vvv][$key];
								ksort($mergNData[$vvv]);
							}
						}
						
						//=============================================================
						for($v=0;$v<count($mergNData); $v++){
							if (count($mergNData[$v]) > 1){
								foreach($mergNData[$v] as $key => $md){
									$dateArray[] = $key;
								}
								
								if (array_diff($allGDate,$dateArray) != null){
									$addElement[$v] =array_values(array_diff($allGDate,$dateArray));
									unset($dateArray);
								}
								else {
									unset($dateArray);
								}
								
							}
							else {
								foreach($mergNData[$v] as $key => $md){
									$addElement[$v] =array_values(array_diff($allGDate,explode(" ",$key)));
								}
							}
						}
						
					//=============================================================		
					for ($bb = 0 ; $bb < count($addElement) ; $bb++){ // 1
						if (count ($mergNData[$bb]) <= 2){
							$Min = min(array_keys($mergNData[$bb])); 
							$Max = max(array_keys($mergNData[$bb])); 
							for($ff=0;$ff<count($addElement[$bb]); $ff++){ 
							
								if ($addElement[$bb][$ff] >= $Min && $addElement[$bb][$ff] <= $Max){
									$finalDate[$bb][$addElement[$bb][$ff]] = $mergNData[$bb][$Min];
								}
								else if ($addElement[$bb][$ff] >= $Max) {
									$finalDate[$bb][$addElement[$bb][$ff]] = $mergNData[$bb][$Max];
								}
								else if ($addElement[$bb][$ff] <= $Min) {
									$finalDate[$bb][$addElement[$bb][$ff]] = $mergNData[$bb][$Min];
								}
							}
						}
						else {
							for($ff=0;$ff<count($addElement[$bb]); $ff++){ // 1
								$mnData = array_keys($mergNData[$bb]);
								foreach ($mnData as $index => $value){ // 4
									if ($addElement[$bb][$ff] >= $value ){
										$finalDate[$bb][$addElement[$bb][$ff]] = $mergNData[$bb][$value];
									}
									else if($addElement[$bb][$ff] <= $value && $addElement[$bb][$ff] >= $mergNData[$bb][$mnData[$index-1]]){ 
										if ($mergNData[$bb][$mnData[$index-1]] != null)
											$finalDate[$bb][$addElement[$bb][$ff]] = $mergNData[$bb][$mnData[$index-1]];
										else
											$finalDate[$bb][$addElement[$bb][$ff]] = $mergNData[$bb][$mnData[$index]];
									}
								}
							}
						}
						
					}
				
					//====================================== merge array
					for ($cc = 0 ; $cc < count($mergNData) ; $cc++){
						if (array_key_exists($cc ,$finalDate ))
							$finalFinal[$cc] = array_merge_recursive($mergNData[$cc],$finalDate[$cc]);
						else 
							$finalFinal[$cc] = $mergNData[$cc];
					}
					//======================================

					//==================================== final merge
					for ($qq = 0 ; $qq < count($mergNData) ; $qq++){
						if (count($mergNData[$qq]) == 1){
							foreach($finalFinal[$qq] as $key => $value){
								$finalFinalFinal[$qq][$key] = $value;
							}
							ksort	($finalFinalFinal[$qq]);
						}
						else {
							foreach ($finalFinal[$qq] as $key => $value){
								$finalFinalFinal[$qq][$key] = $value;
							}
							ksort	($finalFinalFinal[$qq]);
						}
					} 
							//======================================

					//====================================== merge name to array
					for ($mm = 0 ; $mm < count($finalFinalFinal) ; $mm++){
						for($kk=0 ;$kk < count($finalFinalFinal[$mm]);$kk++){	
							$output[$mm]['label'] = "'".implode("','",array_keys ($finalFinalFinal[$mm]))."'";
							$output[$mm]['data'] = implode(',',array_values ($finalFinalFinal[$mm]));
							$output[$mm]['name'] = $newName[$mm];	
						}
					}
				
					$newAllGDate = "'".implode("','",$allGDate)."'";
					$return =  true;
				} // end if 
				else {
					if ($this::getProductPrice($productId) != null){
						foreach($this::getProductPrice($productId) as $key => $value){
							$mergData['gdate'][] = JFactory::getDate($value->date)->format('Y/m/d');
							$mergData['jdate'][] = jdate("o/m/j",$this::convert_date_to_unix($value->date),'','','en');
							$mergData['name'] = $value->product_name;
							$NmergData[JFactory::getDate($value->date)->format('Y/m/d')][] = round( $value->product_price,7);
						}

						$AllGDate = array_unique(explode(',',implode(',',$mergData['jdate'])));
						$newAllGDate = "'".implode("','",$AllGDate)."'";

						foreach ($NmergData as $key => $value){
							for($j=0 ;$j< count($value); $j++){
								$data[jdate("o/m/j",$this::convert_date_to_unix($key),'','','en')]=$value[$j];  
							}
						}
						$output[0]['name'] = $mergData['name'] ;
						$output[0]['data'] = implode(",",array_values($data));
						$return =  true;
					}
					else {
						$return =  false;
					}
				}	
				if($return == true) {
					$document = JFactory::getDocument();
					$document->addScriptDeclaration('');
					$out= 		'<span data-toggle="modal" data-target="#myModal">نمودار تغییرات قیمت</span>
					<!-- Modal -->
					<div id="myModal" class="modal fade" role="dialog">
					<div class="modal-dialog">
					<!-- Modal content-->
					<div class="modal-content">
					<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">نمودار تغییرات قیمت </h4>
					</div>
					<div class="modal-body">
					<canvas id="myChart" width="1109" height="300" style="display: block; width: 100%; height: 300px;"></canvas>
					</div>
					<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">بستن</button>
					</div>
					</div>
					</div>
					</div>';

					$out.= "
					<script>
					var ctx = document.getElementById('myChart');
					var myChart = new Chart(ctx, {
					type: 'bar',
					data: {
					labels: [".$newAllGDate."],
					datasets: [";
					for ($rr=0  ; $rr < count($output); $rr++){
					$out.= "     {
							type: 'line',
							fill:false,
							label: '".$output[$rr]['name']."',
							data: [".$output[$rr]['data']."],
							borderColor: 'rgba(".rand(0,255).",".rand(0,255).",".rand(0,255).",1)',
						},";
						
					}   
					$out.= "]
					}
					});
					</script>";
					echo $out;
				}	
				else {
					echo 'هیچ نموداری وجود ندارد';
				}
	
		}
	}
			
	}	
}
//============================================================================================
//============================================================================================
    function __construct(&$subject, $config){
		parent::__construct($subject, $config);
		$this->product = hikashop_get('class.product');
	}
	function onAfterProductCreate(&$element) {
		$this::storeDB($element);
	}

	function onAfterProductUpdate(&$element) {
		$this::storeDB($element);
	}

	function storeDB ($element) {
		$product_id =  $this->product->get($element->product_id)->product_id;
		$product_parent_id =  $this->product->get($element->product_id)->product_parent_id;
		$product_type =  $this->product->get($element->product_id)->product_type;
		$product_price =  $this->product->get($element->product_id)->product_msrp;
		$characteristic =  array_key_exists('characteristic',$this->product->get($element->product_id));

		if ($product_type == 'variant' && !$characteristic) {
			$product_code =  $this->product->get($element->product_id)->product_code;
			$productName = $this::getChildName ($product_code,'');
			$this::storeDate ($product_parent_id,$product_id,$productName,$product_price,$product_type); // store DB
		}
		else if ($product_type == 'main'){
			 $productName =  $this->product->get($element->product_id)->product_name;
			 $this::storeDate ('',$product_id,$productName,$product_price,$product_type); // store DB
		}
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
	//------------------ storeVariant
	function storeDate ($parentId,$productId,$productName,$productPrice,$productType) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		if (isset($parentId) && $productType == 'variant'){
			$columns = array('parent_id','variant_id','variant_name','variant_price');
			$values =  array($db->q($parentId),$db->q($productId),$db->q($productName),$db->q($productPrice));
			$query->insert($db->qn('#__trangel_hikashop_variant'));
		}
			else if ($productType == 'main') {
			$columns = array('product_id','product_name','product_price');
			$values =  array($db->q($productId),$db->q($productName),$db->q($productPrice));
			$query->insert($db->qn('#__trangel_hikashop_product'));
		}
			$query->columns($db->qn($columns));
			$query->values(implode(',',$values)); 
			$db->setQuery((string)$query);
			$db->execute();
	}
	//============================================================================================
	//============================================================================================

	
	function getProductPrice($productId){
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->qn('#__trangel_hikashop_product'));
		$query->where($db->qn('product_id') . ' = ' . $db->q($productId));
		$db->setQuery((string)$query);
		$result = $db->loadObjectlist();
		return $result;
	}
	
	function prepareDateChart ($data,$dateType,$type) {
		foreach ($data as $d){
			if ($dateType == 'time'){
				$date[] = jdate("H:i_y/m/j",$this::convert_date_to_unix($d->date));
			}
			else{ 
				$date[] = jdate("o/m/j",$this::convert_date_to_unix($d->date),'','','en');
				//$date[] =$this::convert_date_to_unix($d->date);
			}
			if ($type == 'product'){
				$price[] = round($d->product_price,7);
				$name = $d->product_name;
			}
			else{ 
				$price[] = round($d->variant_price,7);
				$name = $d->variant_name;
			}
		}
		
		$nDate = implode(",",$date);
		$nPrice = implode(",",$price);
		return array($nDate,$nPrice,$name);
	}
	
	function convert_date_to_unix($date_time) {
		$user = JFactory::getUser();
		$timeZone = $user->getParam('timezone', 'UTC');
		$myDate = JDate::getInstance($date_time, $timeZone); 
		return $myDate->toUnix();
	}
	
	function getٰVariantId($productId){
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('variant_id');
		$query->from($db->qn('#__trangel_hikashop_variant'));
		$query->where($db->qn('parent_id') . ' = ' . $db->q($productId));
		$query->group($db->qn('variant_id'));
		//$query->setLimit(10);
		$db->setQuery((string)$query);
		$result = $db->loadObjectlist();
		if ($result){
			foreach ($result as $d){
				$variantData[] = $this::getVariantPrice($d->variant_id);
			}
			if ($variantData)
				return $variantData;
		}
		else {
			return false;
		}
	}
	
	function getVariantPrice($variantId) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->qn('#__trangel_hikashop_variant'));
		$query->where($db->qn('variant_id') . ' = ' . $db->q($variantId));
		//$query->setLimit(10);
		$db->setQuery((string)$query);
		$result = $db->loadObjectlist();
		return $result;
	}
	
 }
?>
