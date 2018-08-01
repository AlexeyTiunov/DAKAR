<?php
#require ("/media/Vol/www/bitrix/components/itg/Search/Search_ITG4.php");

class IBPropertyAdvanced_ITG
{
	const 	ID_IB_REGION = 17,
			ID_IB_BRAND = 14;
	private $params = array();
	private $sql = array();
	private $DB;
	private $result = array();
	function __construct($params)
	{
		$this->params = $params;
		#$this->DB = Search_ITG::manualConnect();
                 $this->DB = new mysqli("localhost","bitrix","a251d851","bitrix");
                  #$this->DB= $DBB;
                  $this->DB->set_charset("utf8");
		switch ($this->params['IB'])
		{
			case self::ID_IB_BRAND:
				$this->getHashBrand();
				break;
			case self::ID_IB_REGION:
				$this->getHashRegion();
				break;
		}
	}
	public function getArray()
	{
		return $this->result;
	}
	private function getHashBrand()
	{
		$this->prepareSql();
		$query = $this->DB->query($this->sql[self::ID_IB_BRAND]);
		while (($brands = $query->fetch_assoc()))
		{
			$this->result['id'][$brands['id']] 		= array('ShortName'=>$brands['ShortName'],'FullName'=>$brands['FullName']);
			$this->result['ShortName'][$brands['ShortName']] = array('id'=>$brands['id'],'FullName'=>$brands['FullName']);
			$this->result['FullName'][$brands['FullName']]	= array('ShortName'=>$brands['ShortName'],'id'=>$brands['id']);
		}
	}
	private function getHashRegion()
	{
		$this->prepareSql();
		$query = $this->DB->query($this->sql[self::ID_IB_REGION]);
		while (($regions = $query->fetch_assoc()))
		{
			$this->result['id'][$regions['id']] = array('Code'=>$regions['Code'],'ShortName'=>$regions['ShortName'],'FullName'=>$regions['FullName'],'DeliveryDays'=>$regions['DeliveryDays'],'chrCurrencyCode'=>$regions['chrCurrencyCode']);
			$this->result['Code'][$regions['Code']] = array('id'=>$regions['id'],'ShortName'=>$regions['ShortName'],'FullName'=>$regions['FullName'],'DeliveryDays'=>$regions['DeliveryDays'],'chrCurrencyCode'=>$regions['chrCurrencyCode']);
			$this->result['ShortName'][$regions['ShortName']] = array('id'=>$regions['id'],'Code'=>$regions['Code'],'FullName'=>$regions['FullName'],'DeliveryDays'=>$regions['DeliveryDays'],'chrCurrencyCode'=>$regions['chrCurrencyCode']);
			$this->result['FullName'][$regions['FullName']] = array('id'=>$regions['id'],'ShortName'=>$regions['ShortName'],'Code'=>$regions['Code'],'DeliveryDays'=>$regions['DeliveryDays'],'chrCurrencyCode'=>$regions['chrCurrencyCode']);
		}
	}
	private function prepareSql()
	{
		$tableBrand = "b_iblock_element_prop_s".self::ID_IB_BRAND;
		$tableRegion = "b_iblock_element_prop_s".self::ID_IB_REGION;
		$this->sql[self::ID_IB_BRAND] = "SELECT 
												IBLOCK_ELEMENT_ID as id,
												UCASE(PROPERTY_71) as ShortName,
												UCASE(PROPERTY_72) as FullName 
											FROM {$tableBrand}";
		$this->sql[self::ID_IB_REGION] = "SELECT 
												IBLOCK_ELEMENT_ID as id,
												CAST(PROPERTY_92 AS SIGNED) as Code,
												UCASE(PROPERTY_93) as ShortName,
												UCASE(PROPERTY_94) as FullName,
												UCASE(PROPERTY_95) as DeliveryDays,
												UCASE(PROPERTY_189) as chrCurrencyCode 
											FROM {$tableRegion}";
	}
}
?>
