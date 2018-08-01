<?php
require_once "{$_SERVER["DOCUMENT_ROOT"]}/bitrix/php_interface/include/autodoc_globals.php";
require_once "{$_SERVER["DOCUMENT_ROOT"]}/bitrix/components/itg/1c_exchange.import/MainImport.php";

ini_set('memory_limit','512M');
@set_time_limit(0);
class ImportPayment extends MainImport
{
	private $arDomList = array();
	private $triggerForChange = false;
	private $docType;
	private $ignoreFieldsForeSelect = array('TIMESTAMP_X','DATE_CREATE','MODIFIED_BY','CREATED_BY','ACTIVE','SORT','PREVIEW_TEXT_TYPE','WF_STATUS_ID','IN_SECTIONS');
	private $allElementCash = array();
	
	const 	NAME_TABLE_PAY 		= "b_iblock_element_prop_s24", 
			TYPE_DOCUMENT_PAY	= "PROPERTY_190",
			NUM_DOCUMENT_PAY 	= "PROPERTY_192", 
			DATE_DOCUMENT_PAY 	= "PROPERTY_191",
			CLIENT_CODE_PAY 	= "PROPERTY_193",
			AGREEMENT_CODE_PAY	= "PROPERTY_194",
			CURRENCY_CODE_PAY	= "PROPERTY_195",
			SUMM_PAY			= "PROPERTY_196",
			ID1C_PAY			= "PROPERTY_236";
	
	const 	NAME_TABLE_SHIPMENT		= "b_iblock_element_prop_s25",
			NUM_DOCUMENT_SHIPMENT	= "PROPERTY_197",
			CLIENT_CODE_SHIPMENT	= "PROPERTY_199",
			AGREEMENT_CODE_SHIPMENT	= "PROPERTY_200",
			DATE_DOCUMENT_SHIPMENT	= "PROPERTY_201",
			CURRENCY_CODE_SHIPMENT	= "PROPERTY_202",
			SUMM_SHIPMENT			= "PROPERTY_203",
			TYPE_DOCUMENT_SHIPMENT	= "PROPERTY_205",
			STATUS_SHIPMENT			= "PROPERTY_231",
			ID1C_SHIPMENT			= "PROPERTY_237";
	
	const 	NAME_TABLE_ITEM 	= "b_iblock_element_prop_s26",
			ID_SHIPMENT_ITEM	= "PROPERTY_206",
			BRAND_ITEM			= "PROPERTY_207",
			ARTICLE_ITEM		= "PROPERTY_208",
			QUANTITY_ITEM		= "PROPERTY_210",
			SUM_ITEM			= "PROPERTY_211",
			PRICE_ITEM			= "PROPERTY_212",
			NAME_ITEM			= "PROPERTY_213",
			NUM_ORDER_ITEM		= "PROPERTY_230";
	
	function __construct($params)
	{
		parent::__construct($params);
		//if (isset($this->params['delete'])) $this->deleteDocument();
	}
	protected function getDesiredNodes()
	{
		$this->params['UpdateConfirms'] = "Оплата";
		$this->params['numberSend'] = $this->xpath->query("//НомерОтправленного")->item(0)->nodeValue;
		$this->arDomList["Оплата"] = $this->xpath->query("//Оплата");
		$this->arDomList["Отгрузка"] = $this->xpath->query("//Отгрузка");
	}
	/*function __destruct()
	{
		//$this->params['НомерОтправленного'] = $this->xpath->query("//НомерОтправленного")->item(0)->nodeValue;
		UpdateConfirms("Оплата", $this->xpath->query("//НомерОтправленного")->item(0)->nodeValue);
	}*/
	protected  function updateMain()
	{
		//echo "check ".var_dump($this->arDomList)."<br/>";
		foreach ($this->arDomList as $typeCHeckedNodes=>$list)
		{
			$this->nodesChecked = $list;
			//echo "check 2 ".var_dump($list)."<br/>";
			foreach ($this->nodesChecked as $checked)
			{
				$nodes = $checked->childNodes;
				foreach ($nodes as $node)
				{
					$nodeValue = $node->nodeValue;
					$nodeName = $node->nodeName;
					
					$this->changeSameItemForImport($nodeName,$nodeValue);
				}
				if ($typeCHeckedNodes == "Оплата")
				{
					$this->docType = "Оплата";
					$this->updateForImport();
				}
				
				if ($typeCHeckedNodes == "Отгрузка")
				{
					$this->docType = "Отгрузка";
					$this->triggerForChange = true;
					//echo "inside - 0  ".var_dump($this->item)."<br/>";
					foreach ($this->item['Товар'] as $shipment)
					{
						$shipmentProps = $shipment->childNodes;
						foreach ($shipmentProps as $prop)
						{
							$propValue = $prop->nodeValue;
							$propName = $prop->nodeName;
							$this->changeSameItemForImport($propName,$propValue,$typeCHeckedNodes);
						}
						//echo "inside - ".var_dump($this->item)."<br/>";
						$this->updateForImport();
					}
					$this->triggerForChange = false;
					//echo "check 2 ".var_dump($this->item)."<br/>";
				}
				unset($this->item);
			}
		}
	}
	protected function changeSameItemForImport($nodeName,$nodeValue,$node = null)
	{
		parent::getProcessList();
		if ($node == "Отгрузка" && $this->triggerForChange)
		{
			$nodeName = $nodeName."Отгрузка";
		}
		switch ($nodeName)
		{
			case "Валюта":
				($nodeValue == 'грн') ? $this->item[$nodeName] = "UAH" : $this->item[$nodeName] = trim($nodeValue);
				break;
			case "Дата":
				$this->item[$nodeName] = $nodeValue." 00:00:00";
				break;
			case "Ид":
				$this->item[$nodeName] = $nodeValue;
				@$this->item['Товар'] = $this->xpath->query("//Отгрузка[Ид=\"{$nodeValue}\"]/Товары/Товар");
				break;
			case "АртикулОтгрузка":
				$this->item[$nodeName] = str_replace('-','',trim($nodeValue));
				break;
			default:
				$this->item[$nodeName] = addslashes(trim($nodeValue));
		}
	}
	protected function updateForImport()
	{
		if ($this->currentYear($this->item['Дата']))
		{
			if ($this->docType == "Оплата")
			{
				$this->updateForPayment();
			}
			if ($this->docType == "Отгрузка")
			{
				$this->updateForShipment();
			}
		}
	}
	private function updateForPayment()
	{
		global $DB;
		//echo $this->item['Дата']."<br/>";
		
		//$where = " WHERE `".self::TYPE_DOCUMENT_PAY."`='{$this->item['ТипДокумента']}' 
		//					AND `".self::NUM_DOCUMENT_PAY."`='{$this->item['Номер']}' 
		//					AND `".self::DATE_DOCUMENT_PAY."`='{$this->item['Дата']}'";
		$where = " WHERE `".self::ID1C_PAY."`='{$this->item['Ид']}'";
		
		$idDocum = $this->getExistId(self::NAME_TABLE_PAY, $where,"IBLOCK_ELEMENT_ID");
		if ($idDocum == -1 && $this->item['Удалить'] == 'false')
		{
			$params = array(
							/*'ID'=>"'{$idPay}'",*/
							'TIMESTAMP_X'=>'NOW()',
							'MODIFIED_BY'=>"'1'",
							'DATE_CREATE'=>'NOW()',
							'CREATED_BY'=>"'1'",
							'IBLOCK_ID'=>"'24'",
							'ACTIVE'=>"'Y'",
							'SORT'=>"'500'",
							'NAME'=>"'Платеж {$this->item['Номер']}'",
							'PREVIEW_TEXT'=>"'{$this->item['ТипДокумента']} Дата документа {$this->item['Дата']}; Валюта {$this->item['Валюта']}; Сумма {$this->item['Сумма']}'",
							'PREVIEW_TEXT_TYPE'=>"'text'",
							'SEARCHABLE_CONTENT'=>"'{$this->item['ТипДокумента']} {$this->item['Номер']}'",
							'WF_STATUS_ID'=>"'1'",
							'IN_SECTIONS'=>"'N'");
			$idIB24 = $this->idFromMainIB($params);
			if ($idIB24)
			{
				$sqlInsert = "INSERT INTO `".self::NAME_TABLE_PAY."` (
																		`IBLOCK_ELEMENT_ID`,
																		`".self::TYPE_DOCUMENT_PAY."`,
																		`".self::DATE_DOCUMENT_PAY."`,
																		`".self::NUM_DOCUMENT_PAY."`,
																		`".self::CLIENT_CODE_PAY."`,
																		`".self::AGREEMENT_CODE_PAY."`,
																		`".self::CURRENCY_CODE_PAY."`,
																		`".self::SUMM_PAY."`,
																		`".self::ID1C_PAY."`)
															VALUES(
																		'{$idIB24}',
																		'{$this->item['ТипДокумента']}',
																		'{$this->item['Дата']}',
																		'{$this->item['Номер']}',
																		'{$this->item['Контрагент']}',
																		'{$this->item['ДоговорКод']}',
																		'{$this->item['Валюта']}',
																		'{$this->item['Сумма']}',
																		'{$this->item['Ид']}')";
				
				$DB->Query($sqlInsert);
				//$idPay = $DB->LastID();
				$this->updateDebt();
			}
		}
		elseif ($this->item['Удалить'] == 'false') 
		{
			$sqlUpdate = "UPDATE `".self::NAME_TABLE_PAY."` SET 
																`".self::DATE_DOCUMENT_PAY."`	='{$this->item['Дата']}',
																`".self::AGREEMENT_CODE_PAY."`	='{$this->item['ДоговорКод']}',
																`".self::SUMM_PAY."`			='{$this->item['Сумма']}'
															WHERE 
																`IBLOCK_ELEMENT_ID`='{$idDocum}'";
			$DB->Query($sqlUpdate);
			$idIB24 = $idDocum;
		}
		elseif ($this->item['Удалить'] == 'true') 
		{
			$sqlDelete = "DELETE FROM `".self::NAME_TABLE_PAY."` WHERE `IBLOCK_ELEMENT_ID`='{$idDocum}'";
			$this->deleteFromMainIB($idDocum);
			$DB->Query($sqlDelete);
		}
		$this->allElementCash['b_iblock_element_prop_s24'] = array($idIB24);
	}
	private function updateForShipment()
	{
		global $DB;

		$where = " WHERE 
							`".self::BRAND_ITEM."`='{$this->item['БрендОтгрузка']}'
						AND `".self::ARTICLE_ITEM."`=REPLACE(TRIM('{$this->item['АртикулОтгрузка']}'),'-','') 
						AND 
							`".self::ID_SHIPMENT_ITEM."` IN (SELECT `IBLOCK_ELEMENT_ID` FROM `".self::NAME_TABLE_SHIPMENT."`
																				WHERE `".self::ID1C_SHIPMENT."`='{$this->item['Ид']}')";
		//echo "запрос 1 - {$where}<br/>";
		$idIB26 = $this->getExistId(self::NAME_TABLE_ITEM, $where,"IBLOCK_ELEMENT_ID");
		if ($idIB26 == -1 && $this->item['Удалить'] == 'false')
		{
			$where = " WHERE `".self::ID1C_SHIPMENT."`='{$this->item['Ид']}'";
			//echo "запрос 2 - {$where}<br/>";
			$idIB25 = $this->getExistId(self::NAME_TABLE_SHIPMENT, $where,"IBLOCK_ELEMENT_ID");
			if ($idIB25 == -1)
			{
				$params = array(
								'TIMESTAMP_X'=>'NOW()',
								'MODIFIED_BY'=>"'1'",
								'DATE_CREATE'=>'NOW()',
								'CREATED_BY'=>"'1'",
								'IBLOCK_ID'=>"'25'",
								'ACTIVE'=>"'Y'",
								'SORT'=>"'500'",
								'NAME'=>"'Отгрузка {$this->item['Номер']}'",
								'PREVIEW_TEXT'=>"'{$this->item['ТипДокумента']} Дата документа {$this->item['Дата']}; Валюта {$this->item['Валюта']}; Сумма {$this->item['Сумма']}'",
								'PREVIEW_TEXT_TYPE'=>"'text'",
								'SEARCHABLE_CONTENT'=>"'{$this->item['ТипДокумента']} {$this->item['Номер']}'",
								'WF_STATUS_ID'=>"'1'",
								'IN_SECTIONS'=>"'N'");
				$idIB25 = $this->idFromMainIB($params);
				$sqlInsert = "INSERT INTO `".self::NAME_TABLE_SHIPMENT."` (
																		`IBLOCK_ELEMENT_ID`,
																		`".self::NUM_DOCUMENT_SHIPMENT."`,
																		`".self::CLIENT_CODE_SHIPMENT."`,
																		`".self::AGREEMENT_CODE_SHIPMENT."`,
																		`".self::DATE_DOCUMENT_SHIPMENT."`,
																		`".self::CURRENCY_CODE_SHIPMENT."`,
																		`".self::SUMM_SHIPMENT."`,
																		`".self::TYPE_DOCUMENT_SHIPMENT."`,
																		`".self::STATUS_SHIPMENT."`,
																		`".self::ID1C_SHIPMENT."`)
															VALUES(
																		'{$idIB25}',
																		'{$this->item['Номер']}',
																		'{$this->item['Контрагент']}',
																		'{$this->item['ДоговорКод']}',
																		'{$this->item['Дата']}',
																		'{$this->item['Валюта']}',
																		'{$this->item['Сумма']}',
																		'{$this->item['ТипДокумента']}',
																		'{$this->item['Статус']}',
																		'{$this->item['Ид']}')";
				
				$DB->Query($sqlInsert);
				$this->updateDebt();
			}
			else 
			{
				$sqlUpdate = "UPDATE `".self::NAME_TABLE_SHIPMENT."` SET 
																		`".self::AGREEMENT_CODE_SHIPMENT."`='{$this->item['ДоговорКод']}',
																		`".self::DATE_DOCUMENT_SHIPMENT."`='{$this->item['Дата']}',
																		`".self::CURRENCY_CODE_SHIPMENT."`='{$this->item['Валюта']}',
																		`".self::SUMM_SHIPMENT."`='{$this->item['Сумма']}',
																		`".self::TYPE_DOCUMENT_SHIPMENT."`='{$this->item['ТипДокумента']}',
																		`".self::STATUS_SHIPMENT."`='{$this->item['Статус']}'
																	WHERE 
																		`IBLOCK_ELEMENT_ID`='{$idIB25}'";
				$DB->Query($sqlUpdate);
			}
			$params = array(
					'TIMESTAMP_X'=>'NOW()',
					'MODIFIED_BY'=>"'1'",
					'DATE_CREATE'=>'NOW()',
					'CREATED_BY'=>"'1'",
					'IBLOCK_ID'=>"'26'",
					'ACTIVE'=>"'Y'",
					'SORT'=>"'500'",
					'NAME'=>"'Товар {$this->item['Номер']}'",
					'PREVIEW_TEXT'=>"'Количество {$this->item['КоличествоОтгрузка']}; Бренд {$this->item['БрендОтгрузка']}; Артикул {$this->item['АртикулОтгрузка']}'",
					'PREVIEW_TEXT_TYPE'=>"'text'",
					'SEARCHABLE_CONTENT'=>"'{$this->item['ТипДокумента']} {$this->item['Номер']}'",
					'WF_STATUS_ID'=>"'1'",
					'IN_SECTIONS'=>"'N'");
			$idIB26 = $this->idFromMainIB($params);
			$sqlInsert = "INSERT INTO `".self::NAME_TABLE_ITEM."` (
																	`IBLOCK_ELEMENT_ID`,
																	`".self::ID_SHIPMENT_ITEM."`,
																	`".self::BRAND_ITEM."`,
																	`".self::ARTICLE_ITEM."`,
																	`".self::QUANTITY_ITEM."`,
																	`".self::SUM_ITEM."`,
																	`".self::PRICE_ITEM."`,
																	`".self::NAME_ITEM."`,
																	`".self::NUM_ORDER_ITEM."`)
														VALUES(
																	'{$idIB26}',
																	'{$idIB25}',
																	'{$this->item['БрендОтгрузка']}',
																	'{$this->item['АртикулОтгрузка']}',
																	'{$this->item['КоличествоОтгрузка']}',
																	'{$this->item['СуммаОтгрузка']}',
																	'{$this->item['ЦенаОтгрузка']}',
																	'{$this->item['НаименованиеОтгрузка']}',
																	'{$this->item['ЗаказПокупателяОтгрузка']}')";
			
			$DB->Query($sqlInsert);
			//$idItem = $DB->LastID();
			$this->allElementCash['b_iblock_element_prop_s25'][] = $idIB25;
			$this->allElementCash['b_iblock_element_prop_s26'][] = $idIB26;
		}
		elseif ($this->item['Удалить'] == 'false') 
		{
			$where = " WHERE `".self::ID1C_SHIPMENT."`='{$this->item['Ид']}'";
			$idIB25 = $this->getExistId(self::NAME_TABLE_SHIPMENT, $where,"IBLOCK_ELEMENT_ID");
			$sqlUpdate = "UPDATE `".self::NAME_TABLE_ITEM."` SET 
																`".self::QUANTITY_ITEM."`	='{$this->item['КоличествоОтгрузка']}',
																`".self::SUM_ITEM."`		='{$this->item['СуммаОтгрузка']}',
																`".self::PRICE_ITEM."`		='{$this->item['ЦенаОтгрузка']}'
															WHERE
																`IBLOCK_ELEMENT_ID`			='{$idIB26}'";
			$DB->Query($sqlUpdate);
			$sqlUpdate = "UPDATE `".self::NAME_TABLE_SHIPMENT."` SET 
																	`".self::AGREEMENT_CODE_SHIPMENT."`='{$this->item['ДоговорКод']}',
																	`".self::DATE_DOCUMENT_SHIPMENT."`='{$this->item['Дата']}',
																	`".self::CURRENCY_CODE_SHIPMENT."`='{$this->item['Валюта']}',
																	`".self::SUMM_SHIPMENT."`='{$this->item['Сумма']}',
																	`".self::TYPE_DOCUMENT_SHIPMENT."`='{$this->item['ТипДокумента']}',
																	`".self::STATUS_SHIPMENT."`='{$this->item['Статус']}'
																WHERE 
																	`IBLOCK_ELEMENT_ID`='{$idIB25}'";
			$DB->Query($sqlUpdate);
			$this->allElementCash['b_iblock_element_prop_s25'][] = $idIB25;
			$this->allElementCash['b_iblock_element_prop_s26'][] = $idIB26;
		}
		elseif ($this->item['Удалить'] == 'true')
		{
			$whereItems = " WHERE `".self::ID_SHIPMENT_ITEM."` IN (SELECT `IBLOCK_ELEMENT_ID` FROM `".self::NAME_TABLE_SHIPMENT."`
																				WHERE `".self::ID1C_SHIPMENT."`='{$this->item['Ид']}')";
			$whereShipment = "WHERE `".self::ID1C_SHIPMENT."`='{$this->item['Ид']}'";
			$sqlDeleteItems = "DELETE FROM `".self::NAME_TABLE_ITEM."` ".$whereItems;
			$sqlDeleteShipment = "DELETE FROM `".self::NAME_TABLE_SHIPMENT."` ".$whereShipment;
			$sqlMainDeleteItems = "SELECT `IBLOCK_ELEMENT_ID` FROM `".self::NAME_TABLE_ITEM."` ".$whereItems;
			$sqlMainDeleteShipment = "SELECT `IBLOCK_ELEMENT_ID` FROM `".self::NAME_TABLE_SHIPMENT."` ".$whereShipment;
			$this->deleteFromMainIB($sqlMainDeleteItems);
			$this->deleteFromMainIB($sqlMainDeleteShipment);
			$DB->Query($sqlDeleteItems);
			$DB->Query($sqlDeleteShipment);
		}
	}
	private function currentYear($date)
	{
		$currDate = new DateTime('2010-01');
		$currYear = $currDate->format('Ym');
		$docDate = new DateTime($date);
		$docYear = $docDate->format('Ym');
		if (intval($docYear) < intval($currYear)) return false;
		else return true;
	}
	private function updateDebt()//обновляем задолженность
	{
		global $DB;
		$sqlUpdate = "UPDATE `b_autodoc_agreements` SET 
														`CurrentDebt`=".floatval($this->item['ПросроченнаяЗадолженность'])."
													WHERE
														`ClientCode`='{$this->item['Контрагент']}' AND `Code`='{$this->item['ДоговорКод']}'";
		$DB->Query($sqlUpdate,true);
	}
	private function deleteDocument()
	{
		global $DB;
		foreach ($this->allElementCash as $sqlTable=>$docs)
		{
			$i = 0;
			$sqlDelete[$sqlTable] = "DELETE FROM `{$sqlTable}` WHERE ";
			foreach ($docs as $doc)
			{
				if ($i != 0) $sqlDelete[$sqlTable] .= " AND ";
				$sqlDelete[$sqlTable] .= "`IBLOCK_ELEMENT_ID`!='".$doc."'";
				$i++;
			}
		}
		foreach ($sqlDelete as $sql)
		{
			$DB->Query($sql);
		}
	}
}
?>