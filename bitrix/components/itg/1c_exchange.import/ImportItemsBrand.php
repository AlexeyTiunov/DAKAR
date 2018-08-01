<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/autodoc_globals.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/components/itg/1c_exchange.import/MainImport.php");

class ImportItemsBrand extends MainImport
{
	private $nodesCheckedAdd = false;
	private $triggerUpdateBrand = false;
	private $triggerForChange = false;
	private $arDomList = array();
	private $nodeIsChanged;
	private $analog;
	const OIL=1,RADIATOR=2;
	/*
	 * $params['node'] = 'Товар';
	 * $params['item'] = array('Масло','Радиаторы');
	 * $params['allUpdate'] = false;
	 * $params['file'] = import.xml;
	*/
	function __construct($params)
	{
		//echo "inside CONSTRUCTOR\n";
		//$this->params['UpdateConfirms'] = "Товары";
		parent::__construct($params);
	}
	/*function __destruct()
	{
		global $DB;
		$sql = "UPDATE `ITG_exchange_1c` SET `extMessage`='Data loaded...',`lastTime`=NOW(),`process`='success'";
		//$this->params['НомерОтправленного'] = $this->xpath->query("//НомерОтправленного")->item(0)->nodeValue;
		
		UpdateConfirms("Товары", $this->xpath->query("//НомерОтправленного")->item(0)->nodeValue);
	}*/
	protected function getDesiredNodes()
	{
		//echo "inside getDesiredNodes\n";
		$this->params['UpdateConfirms'] = "Товары";
		$this->params['numberSend'] = $this->xpath->query("//НомерОтправленного")->item(0)->nodeValue;
		foreach ($this->params['item'] as $key=>$path)
		{
			$this->pathDesired = "//Товар[ЗначенияСвойств/ЗначенияСвойства/Значение='{$path}']";
			$this->arDomList[$key] = $this->xpath->query($this->pathDesired);
		}
		$this->arDomList['Аналог'] = $this->xpath->query("//Товар[string-length(Аналоги/Аналог/Артикул)>2]");
		$this->nodesCheckedAdd = $this->xpath->query("//Бренды/Бренд");
		$this->nodeIsChanged = ($this->params['allPosition']) ? $this->xpath->query("//СодержитТолькоИзменения")->item(0)->nodeValue : "true";
	}
	protected  function updateMain()
	{
		//echo "inside updateMain\n";
		//echo var_dump($this->arDomList);
		foreach ($this->arDomList as $typeCHeckedNodes=>$list)
		{
			$this->nodesChecked = $list;
			foreach ($this->nodesChecked as $checked)
			{
				$nodes = $checked->childNodes;
				$this->queryToControllProcess .= ",(NULL,NOW(),'".serialize($nodes)."')";
				$this->analog = false;
				foreach ($nodes as $node)
				{
					$nodeValue = $node->nodeValue;
					$nodeName = $node->nodeName;
					
					$this->queryToControllProcess .= ",(NULL,NOW(),'{$nodeValue}:{$nodeName}')";
					$this->changeSameItemForImport($nodeName,$nodeValue);
				}
				if ($this->item['nodeTypeItem'] == self::OIL) $this->updateForImport();
				
				if ($this->item['nodeTypeItem'] == self::RADIATOR)
				{
					$this->triggerForChange = true;
					foreach ($this->item['Модель'] as $models)
					{
						$modelProps = $models->childNodes;
						foreach ($modelProps as $prop)
						{
							$propValue = $prop->nodeValue;
							$propName = $prop->nodeName;
							$this->changeSameItemForImport($propName,$propValue,$prop);
						}
						$this->updateForImport();
					}
					$this->triggerForChange = false;
				}
				
				if ($typeCHeckedNodes == "Аналог")
				{
					$this->triggerForChange = true;
					foreach ($this->item['Аналог'] as $analogs)
					{
						$analogProps = $analogs->childNodes;
						foreach ($analogProps as $prop)
						{
							$propValue = $prop->nodeValue;
							$propName = $prop->nodeName;
							$this->changeSameItemForImport($propName,$propValue,$typeCHeckedNodes);
						}
						$this->updateForImport();
					}
					$this->triggerForChange = false;
				}
				unset($this->item);
				//elseif($this->triggerUpdateBrand) $this->updateForImportBrand();
				
			}
		}
		/*if ($this->nodesCheckedAdd && !$this->triggerUpdateBrand)
		{
			$this->nodesChecked = $this->nodesCheckedAdd;
			$this->triggerUpdateBrand = true;
			$this->updateMain();
		}*/
		foreach ($this->nodesCheckedAdd as $checked)
		{
			$nodes = $checked->childNodes;
			foreach ($nodes as $node)
			{
				$nodeValue = $node->nodeValue;
				$nodeName = $node->nodeName;
				$this->triggerUpdateBrand = true;
				//$this->queryToControllProcess .= ",(NULL,NOW(),'{$nodeValue}:{$nodeName}')";
				$this->changeSameItemForImport($nodeName,$nodeValue);
			}
			$this->updateForImportBrand();
			unset($this->item);
		}
	}
	
	protected function changeSameItemForImport($nodeName,$nodeValue,$node = null)
	{
		parent::getProcessList();
		$arBrands = GetAllBrandsPropertiesFromFullName();
		$arBrands2 = GetAllBrandsProperties();
		if ($this->item['nodeTypeItem'] == self::RADIATOR && $this->triggerForChange && $node != "Аналог") $nodeName = $nodeName."Модель";
		if ($node == "Аналог" && $this->triggerForChange)
		{
			$nodeName = $nodeName."Аналог";
			$this->analog = true;
		}
		switch ($nodeName)
		{
			case "Бренд":
			case "БрендМодель":
			case "БрендАналог":
				$this->item[$nodeName] = ($arBrands[strtoupper($nodeValue)] != '')?$arBrands[strtoupper($nodeValue)]:$arBrands2[strtoupper($nodeValue)]; 
				break;
			case "Картинка":
				if (isset($this->params['path']))
				{
					$this->item['pathImage'] = $_SERVER["DOCUMENT_ROOT"]."/upload/".$this->params['path']."/".$nodeValue;
				}
				else 
				{
					$this->item['pathImage'] = $_SERVER["DOCUMENT_ROOT"]."/upload/1c_exchange/".$nodeValue;
				}
				break;
			case "Ид":
				$typeItemWord = $this->xpath->query("//Товар[Ид='{$nodeValue}']/ЗначенияСвойств/ЗначенияСвойства/Значение")->item(0)->nodeValue;
				$this->item['nodeTypeItem'] = $this->typeItems[strlen($typeItemWord)>3 ? $typeItemWord : ''];
				if ($this->triggerUpdateBrand)
				{
					$this->item['shablon'] = $this->xpath->query("//Бренды/Бренд[Ид='{$nodeValue}']/Шаблоны/Шаблон");
					//$this->item['shablonLength'] = $this->xpath->query("//Бренд[Ид='{$nodeValue}']/Шаблоны/Значение/ДлинаШаблона");
				}
				@$this->item['Модель'] = $this->xpath->query("//Товар[Ид='{$nodeValue}']/Модели/Модель");
				@$this->item['Аналог'] = $this->xpath->query("//Товар[Ид='{$nodeValue}']/Аналоги/Аналог");
				//echo var_dump($this->item['Модель']);
				break;
			case "НачалоВыпускаМодель":
			case "ОкончаниеВыпускаМодель":
				$this->item[$nodeName] = $nodeValue."-00-00";
				break;
			case "МодельАвтомобиляМодель":
				$this->item[$nodeName] = $nodeValue;
				$kod = intval($node->getAttribute('Код'));
				$this->item['КодМодель'] = $kod;
				break;
	    	default:
	    		$this->item[$nodeName] = addslashes(trim($nodeValue));
	    }
	}
	protected function updateForImport()
	{
		//echo "inside updateForImport\n";
		//echo var_dump($this->item);
		//echo $this->item['nodeTypeItem']." - it s nodeTypeItem for any elements\n";
		$namePicture = $this->getMainId();
		
		//echo "namePicture - ".$namePicture."\n";
		if ($namePicture == -1) return false;
	    
	    if ($this->item['nodeTypeItem'] == self::RADIATOR && !$this->analog) 
	    {
	    	//echo $this->item['nodeTypeItem']." - it s radiator\n";
	    	$this->updateForRadiators($namePicture);
	    }
	    if ($this->analog)
	    {
	    	//echo $this->item['nodeTypeItem']." - it s analog\n";
	    	$this->updateForAnalogs($namePicture);
	    }
	    $this->saveImage($namePicture);
	}
	private function updateForImportBrand()
	{
		//var_dump($this->item);
		if (strlen($this->item['Код']) == 2)
		{
			global $DB;
			
			$where = " WHERE `PROPERTY_71`='{$this->item['Код']}'";
			
		    $sqlUpdate = "UPDATE `b_iblock_element_prop_s14` 
		    					SET 
		    						`PROPERTY_71`='{$this->item['Код']}', 
		    						`PROPERTY_72`='{$this->item['Наименование']}'".$where;
	    	$DB->Query($sqlUpdate,true);//
	    	$idBrand = $this->getExistId("b_iblock_element_prop_s14", $where, "IBLOCK_ELEMENT_ID");
	
		    if ($idBrand == -1)
		    {
		    	$params = array(
						'TIMESTAMP_X'=>'NOW()',
						'MODIFIED_BY'=>"'1'",
						'DATE_CREATE'=>'NOW()',
						'CREATED_BY'=>"'1'",
						'IBLOCK_ID'=>"'14'",
						'ACTIVE'=>"'Y'",
						'SORT'=>"'500'",
						'NAME'=>"'{$this->item['Наименование']}'",
						'PREVIEW_TEXT'=>"'Бренд {$this->item['Наименование']}; Код {$this->item['Код']}'",
						'PREVIEW_TEXT_TYPE'=>"'text'",
						'SEARCHABLE_CONTENT'=>"'".strtoupper($this->item['Наименование'])."'",
						'WF_STATUS_ID'=>"'1'",
						'IN_SECTIONS'=>"'N'");
		    	$idBrand = $this->idFromMainIB($params);
		    	$sqlInsert = "INSERT INTO b_iblock_element_prop_s14 (
		    					IBLOCK_ELEMENT_ID,
		    					PROPERTY_71, 
		    					PROPERTY_72,
		    					PROPERTY_228) 
		    				VALUES(
		    					'{$idBrand}',
		    					'{$this->item['Код']}',
		    					'{$this->item['Наименование']}',
		    					'{$this->item['Ид']}');";
		    	$DB->Query($sqlInsert);
		    	//$idBrand = $DB->LastID();
		    }
		    
		    for ($i=0; $i < $this->item['shablon']->length; $i++)
		    {
		    	$snablon = $this->item['shablon']->item($i)->nodeValue;
		    	$snablonLength = intval($this->item['shablon']->item($i)->getAttribute('ДлинаШаблона'));
		    	$sqlSelect = "SELECT * FROM `b_autodoc_templates` WHERE `TEMPLATE`='{$snablon}' AND `LENGTH`='{$snablonLength}' AND `BCode`='{$idBrand}'";
		    	if(($DB->Query($sqlSelect,true)->SelectedRowsCount()) == 0)
		    	{
		    		$sqlInsert = "INSERT INTO `b_autodoc_templates` (
		    														`LENGTH`,
		    														`TEMPLATE`,
		    														`BCode`)
		    													VALUES(
		    														'{$snablonLength}',
		    														'{$snablon}',
		    														'{$idBrand}');";
		    		$DB->Query($sqlInsert);
		    	}
		    }
		    //$this->queryToControllProcess= ",(NULL,NOW(),'".serialize($info)."')";
		}
	}
	private function updateForRadiators($namePicture)
	{
		global $DB;
		$sqlUpdate = "UPDATE `b_autodoc_radiator`
		   						SET
		   							`ModelRange` = '{$this->item['МодельАвтомобиляМодель']}',
		   							`DateBegin` = '{$this->item['НачалоВыпускаМодель']}',
		   							`VendorCode` = '{$this->item['БрендМодель']}'";
		$sqlUpdate .= isset($this->item['ОкончаниеВыпускаМодель'])	?", `DateEnd` = '{$this->item['ОкончаниеВыпускаМодель']}'":"";
		$sqlUpdate .= isset($this->item['ОписаниеМодель'])			?", `AddInfo` = '{$this->item['ОписаниеМодель']}'":"";
		   
		$sqlUpdate .= " WHERE `Reffer1C` = '{$this->item['КодМодель']}' AND `autodocId` IN (SELECT `id` FROM `b_autodoc_items_m` WHERE `id`='{$namePicture}')";
		   
		$DB->Query($sqlUpdate);
		$where = " WHERE `Reffer1C` = '{$this->item['КодМодель']}' AND `autodocId` IN (SELECT `id` FROM `b_autodoc_items_m` WHERE `id`='{$namePicture}')";
		$idRadiator = $this->getExistId("b_autodoc_radiator", $where, "idRad");
		if ($idRadiator == -1)
		{
		  	$sqlInsert = "INSERT INTO `b_autodoc_radiator` (
		   													`idRad`,
		   													`autodocId`,
		   													`VendorCode`,
		   													`ModelRange`,
		   													`DateBegin`,
		   													`Reffer1C`";
		  	$sqlInsert .= isset($this->item['ОписаниеМодель'])			?",`AddInfo`":"";
		   	$sqlInsert .= isset($this->item['ОкончаниеВыпускаМодель'])	?",`DateEnd`)":")";
		   	
		   	$sqlInsert .=							"VALUES (
		   													NULL,
		   													'{$namePicture}',
		   													'{$this->item['БрендМодель']}',
		   													'{$this->item['МодельАвтомобиляМодель']}',
		   													'{$this->item['НачалоВыпускаМодель']}',
		   													'{$this->item['КодМодель']}'";
		   	$sqlInsert .= isset($this->item['ОписаниеМодель'])			?",'{$this->item['ОписаниеМодель']}'":"";
		   	$sqlInsert .= isset($this->item['ОкончаниеВыпускаМодель'])	?",'{$this->item['ОкончаниеВыпускаМодель']}')":")";
		   	$DB->Query($sqlInsert);
		}
		unset($this->item['ОкончаниеВыпускаМодель'],$this->item['ОписаниеМодель']);
	}
	private function updateForAnalogs($id)
	{
		global $DB;
		$sqlSelectMain = "SELECT `BrandCode`,`ItemCode` FROM `b_autodoc_items_m` WHERE `id`='{$id}'";
		$resBI = $DB->Query($sqlSelectMain);
		$arProduct = $resBI->Fetch();
		$this->item['АртикулАналог'] = str_replace(array(" ","-"), "", $this->item['АртикулАналог']);
		$sqlSelectAnalog = "SELECT * FROM `b_autodoc_analogs_m` WHERE 
																	`B1Code`='{$arProduct['BrandCode']}' 
																AND `I1Code`='{$arProduct['ItemCode']}'
																AND `B2Code`='{$this->item['БрендАналог']}'
																AND `I2Code`='{$this->item['АртикулАналог']}'";
		$resAnalogBI = intval($DB->Query($sqlSelectAnalog)->SelectedRowsCount());
		if ($resAnalogBI == 0 && $this->item['БрендАналог'] != '' && $this->item['АртикулАналог'] != '')
		{
			$in1c = ($this->item['ВведенВ1САналог'] == 'true') ? 1 : 0;
			$sqlInsert = "INSERT INTO `b_autodoc_analogs_m` VALUES(
																	NULL,
																	'{$arProduct['BrandCode']}',
																	'{$arProduct['ItemCode']}',
																	'{$this->item['БрендАналог']}',
																	'{$this->item['АртикулАналог']}',
																	'{$in1c}')";
			$DB->Query($sqlInsert);
		}
		elseif ($resAnalogBI > 0 && $this->item['БрендАналог'] != '' && $this->item['АртикулАналог'] != '' && $this->item['ВведенВ1САналог'] == 'true')
		{
			$sqlUpdateAnalog = "UPDATE `b_autodoc_analogs_m` SET `in1c`='1'  
															WHERE 
																		`B1Code`='{$arProduct['BrandCode']}' 
																	AND `I1Code`='{$arProduct['ItemCode']}'
																	AND `B2Code`='{$this->item['БрендАналог']}'
																	AND `I2Code`='{$this->item['АртикулАналог']}'";
			$DB->Query($sqlUpdateAnalog);
		}
		unset($this->item['БрендАналог'],$this->item['АртикулАналог']);
	}
	private function getMainId()
	{
		global $DB;
		
		$where = " WHERE ItemCode = '{$this->item['Артикул']}' AND BrandCode = '{$this->item['Бренд']}'";
		$sqlUpdate1c = "UPDATE `b_autodoc_items_1c_m` 
	    					SET 
	    						`Caption`='{$this->item['Наименование']}',
	    						`DATE_UPDATED` = Now()".$where;//достаточно одного запроса без SELECT, как для другой базы, так как поле времени обновляется всегда
	    $sqlUpdate = "UPDATE `b_autodoc_items_m` 
	    					SET 
	    						`Caption`='{$this->item['Наименование']}', 
	    						`1CRef` = '1',
	    						`type_item` = '{$this->item['nodeTypeItem']}', 
	    						`definition` = '{$this->item['Комментарий']}'".$where;
	    $countReturnedRow1C = intval($DB->Query($sqlUpdate1c,true)->AffectedRowsCount());
    	$DB->Query($sqlUpdate,true);
    			
	    $triggerInsert = true;
	    $namePicture = -1;
	    if ($countReturnedRow1C != 0)
	    {
	    	$namePicture = $this->getExistId("b_autodoc_items_1c_m", $where);
	    	$triggerInsert = false;
	    }
	    $namePicture = $this->getExistId("b_autodoc_items_m", $where);
	    if ($namePicture != -1)
	    {
	    	$triggerInsert = false;
	    }
	    
	    if ($triggerInsert && $this->item['Бренд'] > 0 && strlen($this->item['Артикул']) > 0 && strlen($this->item['Наименование']) > 0)
	    {
	    	$sqlInsert = "INSERT INTO b_autodoc_items_m (
	    					BrandCode, 
	    					ItemCode, 
	    					Caption,
	    					1CRef,
	    					type_item, 
	    					definition) 
	    				VALUES(
	    					'{$this->item['Бренд']}',
	    					'{$this->item['Артикул']}',
	    					'{$this->item['Наименование']}',
	    					'1',
	    					'{$this->item['nodeTypeItem']}',
	    					'{$this->item['Комментарий']}');";
	    	$DB->Query($sqlInsert);
	    	$namePicture = $DB->LastID();
	    }
	    return $namePicture;
	}
}
?>