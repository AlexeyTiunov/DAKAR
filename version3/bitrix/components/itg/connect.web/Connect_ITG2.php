<?php
require_once $_SERVER["DOCUMENT_ROOT"]."/bitrix/components/itg/Search/Search_ITG2.php";

class Connect_ITG
{
	public static  $log = array(
							'tehnomir'	=>array('pass'		=>'251110',
												'log'		=>'Zolotov',
												'url'		=>'http://tehnomir.com.ua/ws/soap.wsdl',
												'encoding'	=>'cp1251',
												'region'	=>'UW1'),
							'autopalma'	=>array('pass'		=>'3cgz8k',
												'log'		=>'Zolotov',
												'url'		=>'http://autopalma.com.ua/wsdl/server.php?wsdl',
												'encoding'	=>'cp1251',
												'region'	=>'UW2'));
	private $params = array();
	private $client;
	private $result;
	private $sql = array();
	
	function __construct($params)
	{
		$this->params = $params;
		foreach (self::$log as $name=>$propsClient)
		{
			$this->params['client'] = $name;
			$this->client = @new SoapClient(self::$log[$this->params['client']]['url'], array('encoding'=>self::$log[$this->params['client']]['encoding']));
			$this->authorization();
			$this->prepareResult();
		}
	}
	public function getResult()
	{
		//$this->prepareResult();
		return $this->resultToReturn;
	}
	private function authorization()
	{
		$log = self::$log[$this->params['client']]['log'];
		$pass = self::$log[$this->params['client']]['pass'];
		$partnumber = $this->params['article'];
		switch ($this->params['client'])
		{
			case 'tehnomir':

				$brand = ($this->params['brand'] != '')?$this->params['brand']:'';
				$result = $this->client->GetPrice($partnumber, $brand, $log, $pass);
				break;
			case 'autopalma':
				$userParam = array('login'=>$log,'passwd'=>$pass); 
				$result = $this->client->getPartsPrice($partnumber,$userParam); 
				break;
		}
		$this->result = $result;
		echo "-------------------<br /><pre>";
		print_r($this->result);
		echo "</pre>";
	}
	private function prepareResult()
	{
		$products = array();
		$DB = Search_ITG::manualConnect();
		#echo "<pre>";
		#print_r($DB);
		#echo "</pre>";
		echo "<pre>";
		print_r($this->result);
		echo "</pre>";
		if (@count($this->result) == 0) return array();
		foreach ($this->result as $key=>$product)
		{
			//if ((isset($product['DeliveryTime']) && intval($product['DeliveryTime']) != 1) || (isset($product['Quantity']) && intval($product['Quantity']) == 0)) continue;
			//if (isset($product['Dostavka']) && (intval($product['Dostavka']) != 2 || (intval($product['Qty1']) == 0 && intval($product['Qty2']) == 0 && intval($product['Qty3'] == 0)))) continue;
			switch ($this->params['client'])
			{
				case 'tehnomir':
					//echo intval($product['DeliveryTime'])."<br />";
					//echo intval($product['Quantity'])."<br />";
					#if (intval($product['DeliveryTime']) != 1 || intval($product['Quantity']) == 0) continue 2;
					//echo "-------------------<br /><pre>";
					//print_r($product);
					//echo "</pre>";
					$product['CAPTION'] = iconv('cp1251','utf8',$product['Name']);
					$product['ICODE'] = $product['Number'];
					$product['DELIVERY'] = $product['DeliveryTime'];
					//$product['REGION'] = $this->log[$this->params['client']]['region'];
					break;
				case 'autopalma':
					//echo intval($product['Dostavka'])."<br />";
					//echo intval($product['Qty1'])."<br />";
					#if (intval($product['Dostavka']) != 2 || (intval($product['Qty1']) == 0 && intval($product['Qty2']) == 0 && intval($product['Qty3'] == 0))) continue;
					//echo "-------------------<br /><pre>";
					//print_r($product);
					//echo "</pre>";
					$product['CAPTION'] = $product['Name2'];
					$product['ICODE'] = $product['Name'];
					$product['DELIVERY'] = $product['Dostavka'];
					//$product['REGION'] = $this->log[$this->params['client']]['region'];
					
					break;
			}
			$product['REGION'] = self::$log[$this->params['client']]['region'];
			$this->prepareSql();
			#echo $this->sql['partColumnRegion'];
			$sqlQuery = $DB->query($this->sql['partColumnRegion']);
			$sqlRes = $sqlQuery->fetch_assoc();
			$this->params['propertyColumn'] = "PROPERTY_".$sqlRes['RegionColumn'];//формируем коэффициенты для пересчета цен
			$this->prepareSql();
			#echo $this->sql['suppliers'];
			$sqlQuery = $DB->query($this->sql['suppliers']);
			$sqlRes = $sqlQuery->fetch_assoc();
			$product['PRICEREGION'] = $sqlRes['koefPrice']*$product['Price'];
			$product['CURRREGION'] = $sqlRes['CurrencyCode'];
			$this->params['CURRREGION'] = $product['CURRREGION'];
			$this->prepareSql();
			#echo $this->sql['rate'];
			$sqlQuery = $DB->query($this->sql['rate']);
			$sqlRes = $sqlQuery->fetch_assoc();
			$product['PRICEREGIONINCURRENCY'] = $product['PRICEREGION']*$sqlRes['Rate'];
			$products[] = $product;
		}
		//unset($this->result);
		$this->resultToReturn[] = $products;
		echo "<pre>";
		print_r($this->resultToReturn);
		echo "</pre>";
	}
	private function prepareSql()
	{
		$this->sql['partColumnRegion'] = "SELECT `RegionColumn` FROM  `b_autodoc_wh_price_types_m` 
												WHERE `Code`=(SELECT IF((SELECT PriceColumn_1C FROM b_user WHERE ID_1C='{$this->params['user']}' LIMIT 1)=0,10,(SELECT PriceColumn_1C FROM b_user WHERE ID_1C='{$this->params['user']}' LIMIT 1))) LIMIT 1";
		
		$this->sql['suppliers'] = "SELECT ".$this->params['propertyColumn']." AS koefPrice, `PROPERTY_189` AS CurrencyCode FROM `b_iblock_element_prop_s17` 
																WHERE `PROPERTY_93`='".self::$log[$this->params['client']]['region']."'";
		$this->sql['rate'] = "SELECT ITG_currency_rate_ua('{$this->params['CURRREGION']}','{$this->params['currency']}') AS Rate";
	}
}
?>