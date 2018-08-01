<?php
require_once $_SERVER["DOCUMENT_ROOT"]."/bitrix/components/itg/Search/Search_ITG2.php";

class ShowStat_ITG
{
	private $params = array();
	private $DB;
	private $sql = array();
	private $items = array();
	function __construct($params)
	{
		$this->params = $params;
		$this->params['DATE_BEGIN'] = self::prepareDateFromCalendar($this->params['DATE_BEGIN']);
		$this->params['DATE_END'] = self::prepareDateFromCalendar($this->params['DATE_END']);
		#$this->prePrint($this->params);
		$this->DB = Search_ITG::manualConnect();
		$this->prepareSql();
		if ($this->params['RegOnly'] === 'true') $this->sql['main'] = $this->sql['selectFromDateRegOnly'];
		else $this->sql['main'] = $this->sql['selectFromDateAll'];
		$this->getArray();
	}
	static public function prepareDateFromCalendar($date)
	{
		$dateAr = explode(".", $date);
		$datePrepare = new DateTime();
		$datePrepare->setDate($dateAr[2], $dateAr[1], $dateAr[0]);
		return $datePrepare->format('Y-m-d H:i:s');
	}
	public function getArrItems()
	{
		return $this->items;
	}
	private function getArray()
	{
		$DB = $this->DB;
		
		$res = $DB->query($this->sql['main']);
		while ($arItem = $res->fetch_assoc())
		{
			$arItem['BrandName'] = $this->params['arBrands'][$arItem['BrandCode']]['FullName'];
			$this->items[] = $arItem;
		}
	}
	private function prepareSql()
	{
		$this->sql['selectFromDateRegOnly'] = "SELECT `DateTime`, `BrandCode`, `ItemCode`, `ClientCode1C`, count(`ID`) AS Amount, CONCAT(`BrandCode`,`ItemCode`) AS Code FROM `b_autodoc_stats` 
												WHERE 
														`DateTime`>='{$this->params['DATE_BEGIN']}' 
													AND `DateTime`<='{$this->params['DATE_END']}' 
													AND `ClientCode1C` NOT IN('09999','01473','01497','') 
													AND `ItemCode`!='' 
												GROUP BY Code ORDER BY Amount DESC";
		$this->sql['selectFromDateAll'] = "SELECT `DateTime`, `BrandCode`, `ItemCode`, `ClientCode1C`, count(`ID`) AS Amount, CONCAT(`BrandCode`,`ItemCode`) AS Code FROM `b_autodoc_stats` 
												WHERE 
														`DateTime`>='{$this->params['DATE_BEGIN']}' 
													AND `DateTime`<='{$this->params['DATE_END']}' 
													AND `ClientCode1C` NOT IN('09999','01473','01497') 
													AND `ItemCode`!='' 
												GROUP BY Code ORDER BY Amount DESC";
		#$this->prePrint($this->sql);
	}
	private function prePrint($var)
	{
		echo "<pre>";
		print_r($var);
		echo "</pre>";
	}
}
?>