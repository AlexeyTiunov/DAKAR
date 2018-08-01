<?php

class Search_ITG
{
	private $exact = false;
	private $numRows = 0;
	private $tmpRes;
	private $items = array();
	private $brands = array();
	private $dbConn;
	private $url;
	private $searchCode;
	function __construct($user,$currency,$icode,$bcode,$page,$numPage,$sort,$mode='exact',$arBrands=array())
	{
		$DB = new mysqli("localhost","bitrix","a251d851","bitrix","31006");
    	$DB->set_charset("utf8");
    	$this->dbConn = $DB;
		if (!$user) $user = -17;
		if (!$bcode) $bcode = 0;
		if (!$page) $page = 0;
		if (!$sort) $sort = 'PRICE';
		$this->searchCode = $icode;

		//global $DB;
		$sqlGAF['selectFirst'] = <<<TOXA
		(SELECT 
		PE.id as ID,
		PE.ItemCode as ItemCode,
		PE.BrandCode as BrandCode,
		BR.PROPERTY_72 as BrandName,
		PE.RegionCode as RegionCode,
		CE.Caption as Caption,
		CE.Weight as Weight,
		PE.Price*
				(SELECT K FROM b_autodoc_clients_k  WHERE ID_1C in('00001','{$user}') AND RegionCode=PE.RegionCode LIMIT 1) 
				as Price,
		IBR.PROPERTY_95 as DeliveryDays,
		IBR.PROPERTY_93 as RegionShortName,
		IBR.PROPERTY_189 as Currency,
		PE.isActive as isActive,
		(PE.Price*
				(SELECT K FROM b_autodoc_clients_k  WHERE ID_1C in('00001','{$user}') AND RegionCode=PE.RegionCode LIMIT 1))*
                (SELECT CASE(CONCAT(IBR.PROPERTY_189,'{$currency}')) 
											WHEN 'USDUAH' THEN (SELECT RATE FROM b_catalog_currency_rate WHERE CURRENCY='USD' HAVING MAX(DATE_RATE) LIMIT 1)
											WHEN 'USDUSD' THEN (SELECT RATE FROM b_catalog_currency_rate WHERE CURRENCY='UAH' HAVING MAX(DATE_RATE) LIMIT 1)
											WHEN 'USDEUR' THEN ((SELECT RATE FROM b_catalog_currency_rate WHERE CURRENCY='USD' HAVING MAX(DATE_RATE) LIMIT 1)/
																(SELECT RATE FROM b_catalog_currency_rate WHERE CURRENCY='EUR' HAVING MAX(DATE_RATE) LIMIT 1))
											WHEN 'EURUSD' THEN ((SELECT RATE FROM b_catalog_currency_rate WHERE CURRENCY='EUR' HAVING MAX(DATE_RATE) LIMIT 1)/
																(SELECT RATE FROM b_catalog_currency_rate WHERE CURRENCY='USD' HAVING MAX(DATE_RATE) LIMIT 1))
											WHEN 'EUREUR' THEN (SELECT RATE FROM b_catalog_currency_rate WHERE CURRENCY='UAH' HAVING MAX(DATE_RATE) LIMIT 1)
											WHEN 'EURUAH' THEN (SELECT RATE FROM b_catalog_currency_rate WHERE CURRENCY='EUR' HAVING MAX(DATE_RATE) LIMIT 1)
											WHEN 'UAHUSD' THEN 1/(SELECT RATE FROM b_catalog_currency_rate WHERE CURRENCY='USD' HAVING MAX(DATE_RATE) LIMIT 1)
											WHEN 'UAHEUR' THEN 1/(SELECT RATE FROM b_catalog_currency_rate WHERE CURRENCY='EUR' HAVING MAX(DATE_RATE) LIMIT 1)
											WHEN 'UAHUAH' THEN (SELECT RATE FROM b_catalog_currency_rate WHERE CURRENCY='UAH' HAVING MAX(DATE_RATE) LIMIT 1)
									END)
				as UserPrice
TOXA;
		$sqlGAF['selectFirstBR'] = <<<TOXA
		(SELECT 
		PE.id as ID,
		PE.ItemCode as ItemCode,
		PE.BrandCode as BrandCode,
		PE.BrandCode as BrandName
TOXA;
		$sqlGAF['fromFirstBR'] = <<<TOXA
		FROM
			b_autodoc_prices_m as PE
TOXA;
		$sqlGAF['whereFirstBR'] = ($bcode == 0)?" WHERE PE.ItemCode REGEXP '{$icode}')":" WHERE PE.BrandCode='{$bcode}' AND PE.ItemCode REGEXP '{$icode}')";
		if ($bcode != 0)
		{
			$sqlFromFirst[] = <<<TOXA
			FROM
				b_autodoc_items_m as CE 
				LEFT JOIN b_autodoc_prices_m as PE ON(CE.BrandCode={$bcode} AND PE.BrandCode={$bcode} AND CE.ItemCode=PE.ItemCode)
				LEFT JOIN b_iblock_element_prop_s14 as BR ON(BR.IBLOCK_ELEMENT_ID=CE.BrandCode),
				b_iblock_element_prop_s17 as IBR
TOXA;
		}
		else 
		{
			foreach ($arBrands as $brCode=>$brName)
			{
			$sqlFromFirst[] = <<<TOXA
			FROM
				b_autodoc_items_m as CE 
				LEFT JOIN b_autodoc_prices_m as PE ON(CE.BrandCode={$brCode} AND PE.BrandCode={$brCode} AND CE.ItemCode=PE.ItemCode)
				LEFT JOIN b_iblock_element_prop_s14 as BR ON(BR.IBLOCK_ELEMENT_ID=CE.BrandCode),
				b_iblock_element_prop_s17 as IBR
TOXA;
;
			}
		}
		$sqlFromFirstExact = <<<TOXA
			FROM
				b_autodoc_items_m as CE 
				LEFT JOIN b_autodoc_prices_m as PE ON(CE.BrandCode=PE.BrandCode AND CE.ItemCode=PE.ItemCode)
				LEFT JOIN b_iblock_element_prop_s14 as BR ON(BR.IBLOCK_ELEMENT_ID=CE.BrandCode),
				b_iblock_element_prop_s17 as IBR
TOXA;
		$sqlGAF['whereFirst'] = <<<TOXA
			WHERE
					CE.BrandCode=BR.IBLOCK_ELEMENT_ID
				AND
					CAST(IBR.PROPERTY_92 AS SIGNED)=PE.RegionCode AND PE.ItemCode REGEXP '{$icode}')
TOXA;
		$sqlGAF['whereFirstExact'] = <<<TOXA
			WHERE
					CE.BrandCode=BR.IBLOCK_ELEMENT_ID
				AND
					CAST(IBR.PROPERTY_92 AS SIGNED)=PE.RegionCode AND PE.ItemCode='{$icode}')
TOXA;

		$sqlGAF['selectSecond'] = <<<TOXA
UNION
	(SELECT
		P1C.id as ID,
		P1C.ItemCode as ItemCode,
		P1C.BrandCode as BrandCode,
		BR.PROPERTY_72 as BrandName,
		1 as RegionCode,
		C1C.Caption as Caption,
		C1C.Weight as Weight,
		P1C.Price as Price,
		IBR.PROPERTY_95 as DeliveryDays,
		IBR.PROPERTY_93 as RegionShortName,
		IBR.PROPERTY_189 as Currency,
		C1C.Presence as isActive,
		P1C.Price*
                  (SELECT CASE(CONCAT(IBR.PROPERTY_189,'{$currency}')) 
											WHEN 'USDUAH' THEN (SELECT RATE FROM b_catalog_currency_rate WHERE CURRENCY='USD' HAVING MAX(DATE_RATE) LIMIT 1)
											WHEN 'USDUSD' THEN (SELECT RATE FROM b_catalog_currency_rate WHERE CURRENCY='UAH' HAVING MAX(DATE_RATE) LIMIT 1)
											WHEN 'USDEUR' THEN ((SELECT RATE FROM b_catalog_currency_rate WHERE CURRENCY='USD' HAVING MAX(DATE_RATE) LIMIT 1)/
																(SELECT RATE FROM b_catalog_currency_rate WHERE CURRENCY='EUR' HAVING MAX(DATE_RATE) LIMIT 1))
											WHEN 'EURUSD' THEN ((SELECT RATE FROM b_catalog_currency_rate WHERE CURRENCY='EUR' HAVING MAX(DATE_RATE) LIMIT 1)/
																(SELECT RATE FROM b_catalog_currency_rate WHERE CURRENCY='USD' HAVING MAX(DATE_RATE) LIMIT 1))
											WHEN 'EUREUR' THEN (SELECT RATE FROM b_catalog_currency_rate WHERE CURRENCY='UAH' HAVING MAX(DATE_RATE) LIMIT 1)
											WHEN 'EURUAH' THEN (SELECT RATE FROM b_catalog_currency_rate WHERE CURRENCY='EUR' HAVING MAX(DATE_RATE) LIMIT 1)
											WHEN 'UAHUSD' THEN 1/(SELECT RATE FROM b_catalog_currency_rate WHERE CURRENCY='USD' HAVING MAX(DATE_RATE) LIMIT 1)
											WHEN 'UAHEUR' THEN 1/(SELECT RATE FROM b_catalog_currency_rate WHERE CURRENCY='EUR' HAVING MAX(DATE_RATE) LIMIT 1)
											WHEN 'UAHUAH' THEN (SELECT RATE FROM b_catalog_currency_rate WHERE CURRENCY='UAH' HAVING MAX(DATE_RATE) LIMIT 1)
							END)
				as UserPrice
TOXA;
		$sqlGAF['selectSecondBR'] = <<<TOXA
UNION
	(SELECT
		C1C.id as ID,
		C1C.ItemCode as ItemCode,
		C1C.BrandCode as BrandCode,
		C1C.BrandCode as BrandName
TOXA;
		$sqlGAF['fromSecondBR'] = <<<TOXA
		FROM
			b_autodoc_items_1c_m as C1C
TOXA;
		$sqlGAF['whereSecondBR'] = ($bcode == 0)? " WHERE C1C.Presence=1 AND C1C.ItemCode REGEXP '{$icode}')" : " WHERE C1C.Presence=1 AND C1C.BrandCode='{$bcode}' AND C1C.ItemCode REGEXP '{$icode}')";
		if ($bcode != 0)
		{
			$sqlFromSecond[] = <<<TOXA
			FROM
				b_autodoc_items_1c_m as C1C 
				   LEFT JOIN b_autodoc_wh_prices_m as P1C ON (P1C.BrandCode={$bcode} AND C1C.BrandCode={$bcode} AND P1C.ItemCode=C1C.ItemCode)
				   LEFT JOIN b_iblock_element_prop_s14 as BR ON (BR.IBLOCK_ELEMENT_ID=P1C.BrandCode),
				b_iblock_element_prop_s17 as IBR		
TOXA;
		}
		else 
		{
			foreach ($arBrands as $brCode=>$brName)
			{
			$sqlFromSecond[] = <<<TOXA
			FROM
				b_autodoc_items_1c_m as C1C 
				   LEFT JOIN b_autodoc_wh_prices_m as P1C ON (P1C.BrandCode={$brCode} AND C1C.BrandCode={$brCode} AND P1C.ItemCode=C1C.ItemCode)
				   LEFT JOIN b_iblock_element_prop_s14 as BR ON (BR.IBLOCK_ELEMENT_ID=P1C.BrandCode),
				b_iblock_element_prop_s17 as IBR		
TOXA;
			}
		}
		$sqlFromSecondExact = <<<TOXA
			FROM
				b_autodoc_items_1c_m as C1C 
				   LEFT JOIN b_autodoc_wh_prices_m as P1C ON (P1C.BrandCode=C1C.BrandCode AND P1C.ItemCode=C1C.ItemCode)
				   LEFT JOIN b_iblock_element_prop_s14 as BR ON (BR.IBLOCK_ELEMENT_ID=C1C.BrandCode),
				b_iblock_element_prop_s17 as IBR		
TOXA;
		$sqlGAF['whereSecond'] = <<<TOXA
	WHERE
			C1C.Presence=1
		AND
			CAST(IBR.PROPERTY_92 AS SIGNED)=1
		AND
			P1C.PriceCode=(SELECT IF((SELECT PriceColumn_1C FROM b_user WHERE ID_1C='{$user}' LIMIT 1)<>0,(SELECT PriceColumn_1C FROM b_user WHERE ID_1C='{$user}' LIMIT 1),10)) 
            AND P1C.ItemCode REGEXP '{$icode}')   
TOXA;
		$sqlGAF['whereSecondExact'] = <<<TOXA
	WHERE
			C1C.Presence=1
		AND
			CAST(IBR.PROPERTY_92 AS SIGNED)=1
		AND
			P1C.PriceCode=(SELECT IF((SELECT PriceColumn_1C FROM b_user WHERE ID_1C='{$user}' LIMIT 1)<>0,(SELECT PriceColumn_1C FROM b_user WHERE ID_1C='{$user}' LIMIT 1),10)) 
            AND P1C.ItemCode='{$icode}')   
TOXA;
		$sqlGAF['sort'] = ($sort == "REGION")?" ORDER BY ItemCode, RegionShortName " : " ORDER BY ItemCode, Price ";
		$sqlGAF['limit'] = ($page == 0)? "LIMIT 0,{$numPage}":"LIMIT ".(intval($page-1)*intval($numPage)).",{$numPage}";
		$sqlQuery = array();
		if (count($sqlFromFirst)>0)
		{
			foreach ($sqlFromFirst as $key=>$from)
			{
				$sqlQuery[] = $sqlGAF['selectFirst'].$from.$sqlGAF['whereFirst'].$sqlGAF['selectSecond'].$sqlFromSecond[$key].$sqlGAF['whereSecond'].$sqlGAF['sort'].$sqlGAF['limit'];
				
			}
		}
		if ($bcode != 0)
		{
			$sqlFromFirstExact = $sqlFromFirst[0];
			$sqlFromSecondExact = $sqlFromSecond[0];
		}
		$sqlQueryExact = $sqlGAF['selectFirst'].$sqlFromFirstExact.$sqlGAF['whereFirstExact'].$sqlGAF['selectSecond'].$sqlFromSecondExact.$sqlGAF['whereSecondExact'].$sqlGAF['sort'].$sqlGAF['limit'];
		$sqlQueryBR = $sqlGAF['selectFirstBR'].$sqlGAF['fromFirstBR'].$sqlGAF['whereFirstBR'].$sqlGAF['selectSecondBR'].$sqlGAF['fromSecondBR'].$sqlGAF['whereSecondBR']." ORDER BY ItemCode ".$sqlGAF['limit'];
		//echo "<br />exact <br /><pre>".$sqlQueryExact."</pre>";
		//echo "<br />not exact <br />-----------------------------------------";
		//echo "<pre>".print_r($sqlQueryBR)."</pre>";
		$this->tmpRes = $DB->query($sqlQueryExact);
		$this->numRows = $this->tmpRes->num_rows;
		if(($this->numRows == 0 /*&& $mode != 'exact'*/ && $bcode == 0) || ($mode == 'brandAjax' && $this->numRows == 0))
		{
			/*foreach ($sqlQuery as $query)
			{
				$this->tmpRes = $DB->query($query);//запрос на приблизительное совпадение
				$this->numRows = $this->tmpRes->num_rows;
				#echo "<br />";
				#echo "количество: {$this->numRows}";
				#echo "<br />";
				if ($this->numRows > 0)
				{
					$this->getArray();
				}
			}*/
			//echo "<br />1:condition";
			$this->tmpRes = $DB->query($sqlQueryBR);//запрос на приблизительное совпадение
			$this->numRows = $this->tmpRes->num_rows;
			if ($this->numRows > 0)
			{
				$this->getArray();
			}
		}
		elseif ($this->numRows == 0 && $bcode != 0)
		{
			//echo "<br />2:condition";
			//echo $sqlQuery[0];
			$this->tmpRes = $DB->query($sqlQuery[0]);//запрос на приблизительное совпадение
			$this->numRows = $this->tmpRes->num_rows;
			if ($this->numRows > 0)
			{
				$this->getArray();
			}
		}
		else
		{
			//echo "<br />3:condition";
			$this->exact = true;
			$this->getArray();
		}
	}
	public function getSQLres()
	{
		return $this->tmpRes;
	}
	public function getNumRows()
	{
		return $this->numRows;
	}
	public function getBrans()
	{
		return $this->brands;
	}
	public function getArrItems()
	{
		return $this->items;
	}
	public function getUrl()
	{
		return $this->url;
	}
	public function getExact()
	{
		return $this->exact;
	}
	private function getArray()
	{
		$DB = $this->dbConn;
		$brands = array();
		while ($arItem = $this->tmpRes->fetch_assoc())
		{
			$this->items[] = $arItem;
			@$this->brands[$arItem['BrandName']] = $arItem['BrandName'];
			@$this->url[$arItem['BrandName']] = "?ICODE=".$this->searchCode."&BCODE=".$arItem["BrandCode"]."&REGION=".$_REQUEST["REGION"];
		}
		#echo "<pre>";
		#print_r($this->items);
		#echo "</pre>";
		#echo "<br />****************************************************************************************";
		#echo "<pre>";
		#print_r($this->brands);
		#echo "</pre>";
	}
}
?>