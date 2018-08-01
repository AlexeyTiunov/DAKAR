<?php
require_once $_SERVER["DOCUMENT_ROOT"]."/bitrix/components/itg/1c_exchange.import/MainImport.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/autodoc_globals.php";
/**
 * 
 * Enter description here ...
 * @author gurkovsky
 *
 */
class ImportOffers extends MainImport
{
	private $typeNode;
	private $triggerTruncate = false;
	//private $currency = array('USD'=>,'EUR'=>,'UAH'=>,'грн.'=>);
	function __construct($params)
	{
		parent::__construct($params);
		//$this->params['arBrand'] = GetAllBrandsPropertiesFromFullName();
	}
	/*function __destruct()
	{
		//$this->params['НомерОтправленного'] = $this->xpath->query("//НомерОтправленного")->item(0)->nodeValue;
		UpdateConfirms("Цены", $this->xpath->query("//НомерОтправленного")->item(0)->nodeValue);
	}*/
	protected function getDesiredNodes()
	{
		$this->params['UpdateConfirms'] = "Цены";
		$this->params['numberSend'] = $this->xpath->query("//НомерОтправленного")->item(0)->nodeValue;
		$this->nodesChecked['type'] = $this->xpath->query("//ТипЦены");
		$this->nodesChecked['offers'] = $this->xpath->query("//Предложение[number(Количество)>0]");
		$triggerTruncate = $this->xpath->query("//СодержитТолькоИзменения")->item(0)->nodeValue;
		$this->triggerTruncate = ($triggerTruncate == 'true')?true:false;//триггер полного обмена
		//echo " triggerTruncate: ".var_dump($triggerTruncate);
	}
	protected  function updateMain()
	{
		//echo "\n inside updateMain\n";
		//var_dump($this->nodesChecked);
		//var_dump($this->params['arBrand']);
		foreach ($this->nodesChecked as $key=>$check)
		{
			//echo "\n inside cicle #1\n";
			$this->typeNode = $key;
			foreach ($check as $checked)
			{
				//echo "\n inside cicle #2\n";
				$nodes = $checked->childNodes;
	
				$this->queryToControllProcess .= ",(NULL,NOW(),'".serialize($nodes)."')";
				//$DB->Query($queryToControllProcess);
				foreach ($nodes as $node)
				{
					//echo "\n inside cicle #3\n";
					$nodeValue = $node->nodeValue;
					$nodeName = $node->nodeName;
	
					$this->queryToControllProcess .= ",(NULL,NOW(),'{$nodeValue}:{$nodeName}')";
					$this->changeSameItemForImport($nodeName,$nodeValue,$node);

				}
				if ($this->typeNode == 'offers')
				{
					foreach ($this->item['ТоварыЦены'] as $keyPrice=>$offers)
					{
						$nodes = $offers->childNodes;
						foreach ($nodes as $node)
						{
							//echo "\n inside cicle #4\n";
							$nodeValue = $node->nodeValue;
							$nodeName = $node->nodeName;
							$this->changeSameItemForImport($nodeName,$nodeValue,$node);
						}
						//echo "\n if this is offers\n";
						$this->updateForImport();
					}
				}
				else 
				{
					//echo "\n if this is prices!\n";
					$this->updateForImport();
				}
				//var_dump($this->item);
				unset($this->item);
			}
		}
	}
	protected function changeSameItemForImport($nodeName, $nodeValue, $node = null)
	{
		parent::getProcessList();
		$arBrands = GetAllBrandsPropertiesFromFullName();
		switch ($nodeName)
		{
			case 'Ид':
				@$this->item['ТоварыЦены'] = $this->xpath->query("//Предложение[Ид='{$nodeValue}']/Цены/Цена");
				break;
			case 'ИдТипаЦены':
			case 'Код':
				$this->item[$nodeName] = intval($nodeValue);
				break;
			case 'ЦенаЗаЕдиницу':
				$this->item[$nodeName] = floatval($nodeValue);
				break;
			case 'Бренд':
				$this->item[$nodeName] = intval($arBrands[strtoupper($nodeValue)]);
				//$this->item['brand2'] = strtoupper($nodeValue);
				break;
			default:
				$this->item[$nodeName] = addslashes(trim($nodeValue));
				break;
		}
	}
	protected function updateForImport()
	{
		global $DB;
		if ($this->item['Бренд'])
		{
			if ($this->typeNode == 'offers')
			{
				if (!$this->triggerTruncate)
				{
					$sql = "UPDATE `b_autodoc_items_1c_m` SET `Presence`=0";
					$DB->Query($sql);
					$sql = "DELETE FROM `ITG_price_union` WHERE `RegionCode`=1";
					$DB->Query($sql);
					$this->triggerTruncate = true;
				}
				$sql = "UPDATE `b_autodoc_wh_prices_m` 
													SET 
														`Price`='{$this->item['ЦенаЗаЕдиницу']}',
														`DATE_UPDATED`=NOW()
													WHERE
														`BrandCode`='{$this->item['Бренд']}'
														AND
														`ItemCode`='{$this->item['Артикул']}'
														AND
														`PriceCode`='{$this->item['ИдТипаЦены']}';";
				$DB->Query($sql);
				$sql = "SELECT * FROM `b_autodoc_wh_prices_m` 
													WHERE
														`BrandCode`='{$this->item['Бренд']}'
														AND
														`ItemCode`='{$this->item['Артикул']}'
														AND
														`PriceCode`='{$this->item['ИдТипаЦены']}';";
				$res = $DB->Query($sql);
				$count = $res->SelectedRowsCount();
				
				$sql = "INSERT INTO `b_autodoc_wh_prices_m` 
														(`BrandCode`,`ItemCode`,`PriceCode`,`Price`,`DATE_UPDATED`) 
													VALUES 
														('{$this->item['Бренд']}','{$this->item['Артикул']}','{$this->item['ИдТипаЦены']}','{$this->item['ЦенаЗаЕдиницу']}',NOW())";
				if ($count == 0) $DB->Query($sql);
				$sql = "UPDATE `b_autodoc_items_1c_m` 
													SET 
														`Presence`=1,
														`Caption`='{$this->item['Наименование']}',
														`DATE_UPDATED`=NOW()
													WHERE
														`BrandCode`='{$this->item['Бренд']}'
														AND
														`ItemCode`='{$this->item['Артикул']}';";
				$DB->Query($sql);
				$sql = "SELECT * FROM `b_autodoc_items_1c_m` 
													WHERE
														`BrandCode`='{$this->item['Бренд']}'
														AND
														`ItemCode`='{$this->item['Артикул']}';";
				$res = $DB->Query($sql);
				$count = $res->SelectedRowsCount();
				
				$sql = "INSERT INTO `b_autodoc_items_1c_m` 
														(`BrandCode`,`ItemCode`,`Caption`,`Presence`,`DATE_UPDATED`) 
													VALUES 
														('{$this->item['Бренд']}','{$this->item['Артикул']}','{$this->item['Наименование']}',1,NOW());";
				if ($count == 0) $DB->Query($sql);
				$sql = "INSERT IGNORE INTO `ITG_price_union` 
														(`BrandCode`, `ItemCode`, `Caption`, `RegionCode`, `PriceCode`, `Price`) 
													VALUES 
														('{$this->item['Бренд']}','{$this->item['Артикул']}','{$this->item['Наименование']}',1,'{$this->item['ИдТипаЦены']}','{$this->item['ЦенаЗаЕдиницу']}')";
				$DB->Query($sql);
			}
			else
			{
				$sql = "UPDATE `b_autodoc_wh_price_types_m` 
													SET 
														`Caption`='{$this->item['Наименование']}',
														`CurrencyCode`='{$this->item['Валюта']}',
														`DATE_UPDATED`=NOW() 
													WHERE `Code`='{$this->item['Код']}';";
				$res = $DB->Query($sql);
				$count = $res->AffectedRowsCount();
				$sql = "INSERT INTO `b_autodoc_wh_price_types_m` (Caption,Code,CurrencyCode) 
															VALUES('{$this->item['Наименование']}','{$this->item['Код']}','{$this->item['Валюта']}')";
				if ($count == 0) $DB->Query($sql);
			}
		}
	}
}
?>