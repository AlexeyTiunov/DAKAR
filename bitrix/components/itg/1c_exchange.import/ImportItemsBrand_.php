<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/autodoc_globals.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/components/itg/1c_exchange.import/MainImport.php");

class ImportItemsBrand extends MainImport
{
	private $nodesCheckedAdd = false;//дополнительнвыборка узлов для файла import.xml для брендов
	private $triggerUpdateBrand = false;
	private $triggerForChange = false;
	private $arDomList = array();
	private $nodeIsChanged;//триггер для рекурсии
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
		global $DB;
		$this->queryToControllProcess = "INSERT INTO session_control_table VALUES (
																NULL, 
																NOW(),'условие1')";

		foreach ($params as $key=>$param)
		{
			$this->params[$key] = $param;
		}
	    $this->domxml = new DOMDocument;
		$this->domxml->substituteEntities = true;
		$this->domxml->preserveWhiteSpace = false;
		$this->domxml->load($_SERVER["DOCUMENT_ROOT"]."/upload/1c_exchange_MANUAL/{$this->params['file']}");
		$this->xpath = new DOMXPath($this->domxml);
		
		
		$this->getDesiredNodes();
		$this->updateMain();
		$this->dumpProcess();
	}
	protected function getDesiredNodes()//получаем выборку необходимых нам узлов в зависимости от названия файла
	{
		//echo "inside getDesiredNodes\n";
		foreach ($this->params['item'] as $key=>$path)
		{
			$this->pathDesired = "//Товар[ЗначенияСвойств/ЗначенияСвойства/Значение='{$path}']";
			$this->arDomList[$key] = $this->xpath->query($this->pathDesired);
		}
		$this->arDomList['Аналог'] = $this->xpath->query("//Товар[string-length(Аналоги/Аналог/Артикул)>2]");
		$this->nodesCheckedAdd = $this->xpath->query("//Бренды/Бренд");
		$this->nodeIsChanged = ($this->params['allPosition']) ? $this->xpath->query("//СодержитТолькоИзменения")->item(0)->nodeValue : "true";//триггер показывающий необходимо ли анализировать документ
	}
	protected  function updateMain()
	{
		//echo "inside updateMain\n";
		//echo var_dump($this->arDomList);
		foreach ($this->arDomList as $typeCHeckedNodes=>$list)
		{
			$this->nodesChecked = $list;
			foreach ($this->nodesChecked as $checked)//проходим по выбранным узлам
			{
				$nodes = $checked->childNodes;
				$this->queryToControllProcess .= ",(NULL,NOW(),'".serialize($nodes)."')";
				$this->analog = false;
				foreach ($nodes as $node)//цикл по дочерним узлам выбранного узла
				{
					$nodeValue = $node->nodeValue;
					$nodeName = $node->nodeName;
					
					$this->queryToControllProcess .= ",(NULL,NOW(),'{$nodeValue}:{$nodeName}')";
					$this->changeSameItemForImport($nodeName,$nodeValue);
				}
				if ($this->item['nodeTypeItem'] == self::OIL) $this->updateForImport();//если товар - масло, идем обновляться здесь!!!
				//обрабатывать масло надо первым делом так как признак масла затирается на признак аналог
				if ($this->item['nodeTypeItem'] == self::RADIATOR)//радиатор
				{
					$this->triggerForChange = true;
					foreach ($this->item['Модель'] as $models)//$this->item['Модель'] - выборка узлов сделанная с помощью метода changeSameItemForImport()
					{
						$modelProps = $models->childNodes;
						foreach ($modelProps as $prop)
						{
							$propValue = $prop->nodeValue;
							$propName = $prop->nodeName;
							$this->changeSameItemForImport($propName,$propValue,$prop);//на текущем этапе мы будем иметь свойства модели с префиксом Модель и свойства радиатора
						}
						$this->updateForImport();//если товар радиатор идем обновляться здесь!!!
					}
					$this->triggerForChange = false;
				}
				
				if ($typeCHeckedNodes == "Аналог")
				{
					$this->triggerForChange = true;
					foreach ($this->item['Аналог'] as $analogs)//$this->item['Аналог'] - выборка узлов сделанная с помощью метода changeSameItemForImport()
					{
						$analogProps = $analogs->childNodes;
						foreach ($analogProps as $prop)
						{
							$propValue = $prop->nodeValue;
							$propName = $prop->nodeName;
							$this->changeSameItemForImport($propName,$propValue,$typeCHeckedNodes);//на текущем этапе мы будем иметь свойства модели с префиксом Модель и свойства радиатора
						}
						$this->updateForImport();//если товар аналог идем обновляться здесь!!!
					}
					$this->triggerForChange = false;
				}
				unset($this->item);//очищаем массив свойств товара после обработки дабы не влиять на последующий цикл
				//elseif($this->triggerUpdateBrand) $this->updateForImportBrand();//если данные по товарам импортированы, делаем импорт брендов
				
			}
		}
		/*if ($this->nodesCheckedAdd && !$this->triggerUpdateBrand)//вызов рекурсивно текущую функцию для обработки брендов
		{
			$this->nodesChecked = $this->nodesCheckedAdd;
			$this->triggerUpdateBrand = true;
			$this->updateMain();
		}*/
		foreach ($this->nodesCheckedAdd as $checked)
		{
			$nodes = $checked->childNodes;
			$this->triggerUpdateBrand = true;
			foreach ($nodes as $node)//цикл по дочерним узлам выбранного узла
			{
				$nodeValue = $node->nodeValue;
				$nodeName = $node->nodeName;
				
				//$this->queryToControllProcess .= ",(NULL,NOW(),'{$nodeValue}:{$nodeName}')";
				$this->changeSameItemForImport($nodeName,$nodeValue);
			}
			$this->updateForImportBrand();
			//var_dump($this->item);
			unset($this->item);
		}
	}
	
	protected function changeSameItemForImport($nodeName,$nodeValue,$node = null)//изменяем значения некоторых узлов для вставки в БД
	{
		$arBrands = GetAllBrandsPropertiesFromFullName();
		$arBrands2 = GetAllBrandsProperties();
		if ($this->item['nodeTypeItem'] == self::RADIATOR && $this->triggerForChange && $node != "Аналог") $nodeName = $nodeName."Модель";//добавляем префикс чтобы не затереть пересекающие свойства
		if ($node == "Аналог" && $this->triggerForChange)//добавляем префикс чтобы не затереть пересекающие свойства
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
				$this->item['pathImage'] = $_SERVER["DOCUMENT_ROOT"]."/upload/1c_exchange/".$nodeValue;
				break;
			case "Ид":
				$typeItemWord = $this->xpath->query("//Товар[Ид='{$nodeValue}']/ЗначенияСвойств/ЗначенияСвойства/Значение")->item(0)->nodeValue;
				$this->item['nodeTypeItem'] = $this->typeItems[strlen($typeItemWord)>3 ? $typeItemWord : ''];
				if ($this->triggerUpdateBrand)
				{
					$this->item['shablon'] = $this->xpath->query("//Бренды/Бренд[Ид='{$nodeValue}']/Шаблоны/Шаблон");//список узлов с представлением шаблоа
					//$this->item['shablonLength'] = $this->xpath->query("//Бренд[Ид='{$nodeValue}']/Шаблоны/Значение/ДлинаШаблона");//список узлов с количеством символов в шаблоне
				}
				@$this->item['Модель'] = $this->xpath->query("//Товар[Ид='{$nodeValue}']/Модели/Модель");//содержит массив узлов моделей
				@$this->item['Аналог'] = $this->xpath->query("//Товар[Ид='{$nodeValue}']/Аналоги/Аналог");//содержит массив узлов аналогов
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
	    		$this->item[$nodeName] = addslashes(trim($nodeValue));//используем спец функцию для возможности вставки в БД если есть кавычки или слеши
	    }
	}
	protected function updateForImport()//полученные значения вставляем в нужную нам БД записсываем картинки
	{
		//echo "inside updateForImport\n";
		//echo var_dump($this->item);
		//echo $this->item['nodeTypeItem']." - it s nodeTypeItem for any elements\n";
		$namePicture = $this->getMainId();
		//если данные не корректны то выходим из метода
		//echo "namePicture - ".$namePicture."\n";
		if ($namePicture == -1) return false;
	    //блок обновления радиаторов
	    if ($this->item['nodeTypeItem'] == self::RADIATOR && !$this->analog) //если это радиатор
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
			
			$where = " WHERE `PROPERTY_71`='{$this->item['Код']}'";//задаем условие сравнения
			//вначале пытаемся обновить таблицу инфоблока
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
		    	$idBrand = $this->idFromMainIB($params);//вставка бренда в главную таблицу инфоблоков
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
		    //записываем полученные шаблоны
		    //echo $this->item['shablon']->length."<br/>";
		    for ($i=0; $i < $this->item['shablon']->length; $i++)
		    {
		    	$snablon = $this->item['shablon']->item($i)->nodeValue;
		    	$snablonLength = intval($this->item['shablon']->item($i)->getAttribute('ДлинаШаблона'));
		    	$sqlSelect = "SELECT * FROM `b_autodoc_templates` WHERE `TEMPLATE`='{$snablon}' AND `LENGTH`='{$snablonLength}' AND `BCode`='{$idBrand}'";
		    	echo $sqlSelect."<br/>";
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
		$idRadiator = $this->getExistId("b_autodoc_radiator", $where, "idRad");//1 - DB, 2 - definition, 3 - name of primary key
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
		unset($this->item['ОкончаниеВыпускаМодель'],$this->item['ОписаниеМодель']);//удаляем данные чтобы они не оставались при следующей записи
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
		{//добавляем непустые аналоги
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
		{//обновляем данные если есть пометка из 1С
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
	    $namePicture = $this->getExistId("b_autodoc_items_m", $where);//дополнительный запрос, так как если строки идентичны запрос на обновление не затрагивает строки
	    if ($namePicture != -1)
	    {
	    	$triggerInsert = false;
	    }
	    //вставляем данные в таблицу только если все поля не путсые
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