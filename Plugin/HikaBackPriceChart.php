<?php
defined('_JEXEC') or die('Restricted access');

//Load the Plugin language file out of the administration
$lang = & JFactory::getLanguage();
$lang->load('plg_hikashop_HikaBackPriceChart', JPATH_ADMINISTRATOR);


// You need to extend from the hikashopPaymentPlugin class which already define lots of functions in order to simplify your work
class plgHikashopHikaBackPriceChart extends JPlugin
{
	
function onHikashopAfterDisplayView(&$view) {
		if (JRequest::getVar('option')==='com_hikashop' AND JRequest::getVar('ctrl')==='product' AND JRequest::getVar('task')==='show') {

		if($view->getName() == 'product') {

			if($view->getLayout() == 'show_quantity') {
				$priceparams = $this->params->get('price');

				if (!empty($this->params->get('months'))) {
					$monthsparams = $this->params->get('months');
				}else {
					$monthsparams = "'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'آذر', 'فروریدن'";
				}
				
				if (!empty($priceparams)) {
					$chrtlinePriceHikashop = $view->escape($view->element->$priceparams);
				} 
				JHtml::script('https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.2.2/Chart.bundle.js'); 
				JHtml::script('http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js'); 
				JHtml::script('./jquery.slModal.js'); 
				JHtml::script(JURI::root().'plugins/hikashop/HikaBackPriceChart/jquery.slModal.js'); 
				JHtml::stylesheet(JURI::root().'plugins/hikashop/HikaBackPriceChart/jquery.slModal.css'); 
				JHtml::stylesheet('https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css');

    	
				    	$document = JFactory::getDocument();
					$document->addScriptDeclaration('
					 	$().slModal() 
					');

					echo '<a data-modal="searchBox" class="pure-button button-success"><i class="fa fa-bar-chart-o" aria-hidden="true"></i> '.JText::_('PLG_HIKABACKPRICECHART_HIKASHOP_PRICE_CHART').' </a>';
					
					echo '<div id="searchBox" class="slModal ">';
					          	echo '<fieldset>';
					            echo '<canvas id="Chartonpage" class="span12 chartsjs"></canvas>';
					        	echo '</fieldset>';   	
					echo '</div>';

					// echo '<canvas id="Chartonpage" width="100%" height="40%" style="display: block; width: 100%; height: 40%;"></canvas>';
				echo "<script> 

					var options = {
					    responsive: true,
					    maintainAspectRatio: true,
					};
					var ctx = document.getElementById('Chartonpage');

						var data = {
						    labels: [$monthsparams],
						    datasets: [
						        {
						            label: '".JText::_('PLG_HIKABACKPRICECHART_HIKASHOP_PRICE_CHART')."',
						            fill: false,
						            lineTension: 0.1,
						            backgroundColor: 'rgba(75,192,192,0.4)',
						            borderColor: 'rgba(75,192,192,1)',
						            borderCapStyle: 'butt',
						            borderDash: [],
						            borderDashOffset: 0.0,
						            borderJoinStyle: 'miter',
						            pointBorderColor: 'rgba(75,192,192,1)',
						            pointBackgroundColor: '#fff',
						            pointBorderWidth: 1,
						            pointHoverRadius: 5,
						            pointHoverBackgroundColor: 'rgba(75,192,192,1)',
						            pointHoverBorderColor: 'rgba(220,220,220,1)',
						            pointHoverBorderWidth: 2,
						            pointRadius: 1,
						            pointHitRadius: 10,
						            data: [$chrtlinePriceHikashop],
						            spanGaps: false,
						        }
						    ]
						};



					var myLineChart = new Chart(ctx, {
					    type: 'line',
					    data: data,
					    options: options
					});

				</script>";
			}
		}
	}
     }
     
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
 }
?>
