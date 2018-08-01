<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/autodoc_globals.php");
class itgImportFromXML
{
	private $domxml;
	private $xpath;
	private $queryToControllProcess;
	private $pathDesired;
	//private $pathDesiredAdd;//дополнительный путь для файла import.xml для брендов
	private $nodesChecked;
	private $nodesCheckedAdd = false;//дополнительнвыборка узлов для файла import.xml для брендов
	private $triggerUpdateBrand = false;
	private $nodeIsChanged;
	private $item = array();
	private $params = array();
	private $typeItems = array(''=>0,'Масло'=>1,'Радиаторы'=>2);
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
		$this->domxml->load($_SERVER["DOCUMENT_ROOT"]."/upload/1c_exchange/{$this->params['file']}");
		$this->xpath = new DOMXPath($this->domxml);
		
		
		$this->getDesiredNodes();
		$this->updateMain();
		$this->dumpProcess();
	}
	private function getDesiredNodes()//получаем выборку необходимых нам узлов в зависимости от названия файла
	{
		switch ($this->params['file']) 
		{
			case 'import.xml':
				$numName = count($this->params['item']);
				$this->pathDesired = "//Товар";//оздаем путь для выборки узлов
				if (isset($this->params['item'])
					&& $numName == 1) $this->pathDesired .= "[ЗначенияСвойств/ЗначенияСвойства/Значение='{$this->params['item'][0]}']";
				else if ($numName > 1)
				{
					$i = 0;
					$this->pathDesired .= "[ЗначенияСвойств/ЗначенияСвойства/(";
					foreach ($this->params['item'] as $item)
					{
						$i++;
						$this->pathDesired .= "Значение='{$item}'";
						if ($i < $numName) $this->pathDesired .= " or ";
					}
					$this->pathDesired .= ")]";
				}
				//$this->pathDesiredAdd = "//Бренд";
				$this->nodesCheckedAdd = $this->xpath->query("//Бренд");
				$this->nodeIsChanged = ($this->params['allPosition']) ? $this->xpath->query("//СодержитТолькоИзменения")->item(0)->nodeValue : "true";//триггер показывающий необходимо ли анализировать документ
				$this->nodesChecked = $this->xpath->query($this->pathDesired);//выбираем только ужный нам продукт
				$this->queryToControllProcess .= ",(NULL,NOW(),'".serialize($this->nodesChecked)."')";
				$this->queryToControllProcess .= ",(NULL,NOW(),'{$this->nodeIsChanged}')";
				break;
			case 'regions.xml':
				$this->pathDesired = "//Регион";
				$this->nodesChecked = $this->xpath->query($this->pathDesired);
				break;
		}
	}
	private  function updateMain()
	{
		foreach ($this->nodesChecked as $checked)//проходим по выбранным узлам
		{
			$nodes = $checked->childNodes;

			$this->queryToControllProcess .= ",(NULL,NOW(),'".serialize($nodes)."')";
			//$DB->Query($queryToControllProcess);
			foreach ($nodes as $node)//цикл по дочерним узлам выбранного узла
			{
				$nodeValue = $node->nodeValue;
				$nodeName = $node->nodeName;

				$this->queryToControllProcess .= ",(NULL,NOW(),'{$nodeValue}:{$nodeName}')";
				switch ($this->params['file'])//в зависимости от файла делаем корректировки значений узлов для дальнейшей обработки
				{
					case 'import.xml':
						$this->changeSameItemForImport($nodeName,$nodeValue);;
						break;
					case 'regions.xml':
						break;
				}
			}
			switch ($this->params['file']) //в зависимости от файла подключаем функцию для вставки значений в БД
			{
				case 'import.xml':
					if (!$this->triggerUpdateBrand) $this->updateForImport();
					//else $this->updateForImportBrand();
					break;
				case 'regions.xml':
					$this->updateForRegions();
					break;
			}
		}
		/*if ($this->nodesCheckedAdd && !$this->triggerUpdateBrand)//вызов рекурсивно текущую функцию для обработки брендов
		{
			$this->nodesChecked = $this->nodesCheckedAdd;
			$this->triggerUpdateBrand = true;
			$this->updateMain();
		}*/
	}
	private function updateForRegions() 
	{
		//в разработке
	}
	private function changeSameItemForImport($nodeName,$nodeValue)//изменяем значения некоторых узлов для вставки в БД
	{
		$arBrands = GetAllBrandsPropertiesFromFullName();
		$arBrands2 = GetAllBrandsProperties();
		switch ($nodeName)
		{
			case "Бренд":
				$this->item[$nodeName] = ($arBrands[strtoupper($nodeValue)] != '')?$arBrands[strtoupper($nodeValue)]:$arBrands2[strtoupper($nodeValue)]; 
				break;
			case "Картинка":
				$this->item['pathImage'] = $_SERVER["DOCUMENT_ROOT"]."/upload/1c_exchange/".$nodeValue;
				break;
			case "Ид":
				$j = $this->xpath->query("//Товар[Ид='{$nodeValue}']/ЗначенияСвойств/ЗначенияСвойства/Значение")->item(0)->nodeValue;
				$this->item['nodeTypeItem'] = $this->typeItems[$j];
				if ($this->triggerUpdateBrand)
				{
					$this->item['shablon'] = $this->xpath->query("//Бренд[Ид='{$nodeValue}']/Шаблоны/Значение/Шаблон");//список узлов с представлением шаблоа
					$this->item['shablonLength'] = $this->xpath->query("//Бренд[Ид='{$nodeValue}']/Шаблоны/Значение/ДлинаШаблона");//список узлов с количеством символов в шаблоне
				}
				break;
	    	default:
	    		$this->item[$nodeName] = $nodeValue;
	    }
	}
	private function updateForImport()//полученные значения вставляем в нужную нам БД записсываем картинки
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
	    $countReturnedRow['Update1c'] = intval($DB->Query($sqlUpdate1c,true)->AffectedRowsCount());
    	$countReturnedRow['Update'] = intval($DB->Query($sqlUpdate,true)->AffectedRowsCount());
    			
	    $triggerInsert = true;
	    			
	    if ($countReturnedRow['Update1c'] != 0)
	    {
	    	$namePicture = $this->getExistId("b_autodoc_items_1c_m", $where);
	    	$triggerInsert = false;
	    }
	    if ($countReturnedRow['Update'] != 0)
	    {
	    	$namePicture = $this->getExistId("b_autodoc_items_m", $where);
	    	$triggerInsert = false;
	    }
	    if (($namePicture = $this->getExistId("b_autodoc_items_m", $where)) != 0)//дополнительный запрос, так как если строки идентичны запрос на обновление не затрагивает строки
	    {
	    	$triggerInsert = false;
	    }
	    if ($triggerInsert)
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
	    echo "артикул : {$this->item['Артикул']} | триггер для вставки : {$triggerInsert}| название картинки : {$namePicture}\n";
	    $this->saveImage($namePicture);
	    
	    $this->queryToControllProcess .= ",(NULL,NOW(),'".serialize($info)."')";
	}
	private function updateForImportBrand()
	{
		global $DB;
		
		$where = " WHERE PROPERTY_72 = '{$this->item['Код']}'";//задаем условие сравнения
		//вначале пытаемся обновить таблицу инфоблока
	    $sqlUpdate = "UPDATE `b_iblock_element_prop_s14` 
	    					SET 
	    						`PROPERTY_71`='{$this->item['Код']}', 
	    						`PROPERTY_72` = '{$this->item['Наименование']}'".$where;
    	$countReturnedRow['Update'] = intval($DB->Query($sqlUpdate,true)->AffectedRowsCount());//смотрим сколько строк затронуло
    	
    	
    			
	    $triggerInsert = true;
	    			
	    if ($countReturnedRow['Update'] == 0)//если строки небыли затронуты проверяем есть ли такой бренд через SELECT
	    {
	    	$sqlSelect = "SELECT `IBLOCK_ELEMENT_ID` as id FROM `b_iblock_element_prop_s14` ".$where;
	    	$res = $DB->Query($sqlSelect,true);
	    	$numrow = intval($res->SelectedRowsCount());
	    	if($numrow != 0)
	    	{
	    		$triggerInsert = false;
	    		$resTmp = $res->Fetch();
	    		$idBrand = $resTmp['id'];
	    	}
	    }

	    if ($triggerInsert)
	    {
	    	$sqlInsert = "INSERT INTO b_iblock_element_prop_s14 (
	    					PROPERTY_71, 
	    					PROPERTY_72) 
	    				VALUES(
	    					'{$this->item['Код']}',
	    					'{$this->item['Наименование']});";
	    	$DB->Query($sqlInsert);
	    	$idBrand = $DB->LastID();
	    }
	    //записываем полученные шаблоны
	    for ($i=0; $i < $this->item['shablon']->length; $i++)
	    {
	    	$snablon = $this->item['shablon']->item($i)->nodeValue;
	    	$snablonLength = $this->item['shablonLength']->item($i)->nodeValue;
	    	$sqlSelect = "SELECT * FROM `b_autodoc_templates` WHERE `TEMPLATE`='{$snablon}' AND `LENGTH`={$snablonLength} AND `BCode`={$idBrand}";
	    	if($DB->Query($sqlSelect,true)->SelectedRowsCount() == 0)
	    	{
	    		$sqlInsert = "INSERT INTO `b_autodoc_templates` (
	    														`LENGTH`,
	    														`TEMPLATE`,
	    														`BCode`)
	    													VALUES(
	    														'{$snablonLength}',
	    														'{$snablon}',
	    														'{$idBrand}');";
	    	}
	    }
	    $this->queryToControllProcess= ",(NULL,NOW(),'".serialize($info)."')";
	}
	private function getExistId($dataBase,$where)//возвращаем ID выбранного узла, в случае неудачи возвращаем 0
	{
		global $DB;
		$sqlTmp = "SELECT `id` FROM `{$dataBase}` ".$where;
	    
	    $resTmp = $DB->Query($sqlTmp,true);
	    $countReturnedRow = intval($resTmp->SelectedRowsCount());
	    $arTmp['id'] = 0;
	    $reterned = $countReturnedRow == 0 ? $arTmp['id'] : $arTmp = $resTmp->Fetch();
	    return $arTmp['id'];
	}
	private function saveImage($namePicture)
	{
		$from = imageCreateFromJPEG($this->item['pathImage']);
	    $info = @getimagesize($this->item['pathImage']);
	    $x = $info[0];
	    $y = $info[1];
	    		
	    $to = imageCreateTrueColor($x, $y);
	    imageCopy($to,$from,0,0,0,0,$x,$y);
	    imageJPEG($to,$_SERVER["DOCUMENT_ROOT"]."/autodoc/imagesItems/{$namePicture}.jpg");
	    imageDestroy($to);
	    imageDestroy($from);
	}
	private function dumpProcess()//записываем в спец БД результаты работы скрипта
	{
		global $DB;
		//echo $this->queryToControllProcess;
		$DB->Query($this->queryToControllProcess);
	}
}
?>