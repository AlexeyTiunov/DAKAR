<?php
/**
 * 
 */
require_once "{$_SERVER["DOCUMENT_ROOT"]}/bitrix/php_interface/include/autodoc_globals.php";
require_once "{$_SERVER["DOCUMENT_ROOT"]}/bitrix/components/itg/1c_exchange.import/MainImport.php";

class ImportRegions extends MainImport
{
    function __construct($params)
    {
        parent::__construct($params);
    }
    /*function __destruct()
    {
        //$this->params['НомерОтправленного'] = $this->xpath->query("//НомерОтправленного")->item(0)->nodeValue;
        UpdateConfirms("Регионы", $this->xpath->query("//НомерОтправленного")->item(0)->nodeValue);
    }*/
    protected function getDesiredNodes()
    {
        $this->params['UpdateConfirms'] = "Регионы";
        $this->params['numberSend'] = $this->xpath->query("//НомерОтправленного")->item(0)->nodeValue;
        $this->nodesChecked = $this->xpath->query("//Регион");
    }
    protected function changeSameItemForImport($nodeName, $nodeValue, $node = null)    
    {
        /*if ($nodeName == 'Наценка')
        {
            $this->item[$node->getAttribute('Наименование')][0] = intval($nodeValue);
            $this->item[$node->getAttribute('Наименование')][1] = intval($node->getAttribute('Код'));
        }
        else 
        {
            $this->item[$nodeName] = addslashes(trim($nodeValue));
        }*/
        parent::getProcessList();
        switch ($nodeName)
        {
            case 'Наценка':
                $this->item[$node->getAttribute('Наименование')][0] = floatval(str_replace(',', '.',$nodeValue));
                $this->item[$node->getAttribute('Наименование')][1] = intval($node->getAttribute('Код'));
                break;
            case 'БуквенныйКодВалютыРегиона':
                if (trim($nodeValue) == 'грн.') $this->item[$nodeName] = 'UAH';
                break;
            default:
                $this->item[$nodeName] = addslashes(trim($nodeValue));
        }
    }
    protected function updateForImport()
    {
        global $DB;
        $where = " WHERE CAST(`PROPERTY_92` AS SIGNED)='".intval($this->item['Код'])."'";
        $idRegion = $this->getExistId("b_iblock_element_prop_s17", $where, "IBLOCK_ELEMENT_ID");
        if ($idRegion != -1)
        {
            $sqlUpdate = "UPDATE `b_iblock_element_prop_s17` 
                                SET `PROPERTY_93`='{$this->item['КороткоеНазваниеРегиона']}',
                                    `PROPERTY_94`='{$this->item['Наименование']}',
                                    `PROPERTY_95`='{$this->item['СрокДоставкиВДнях']}'";
            $sqlUpdate .= isset($this->item['БуквенныйКодВалютыРегиона'])    ? ",`PROPERTY_189`='{$this->item['БуквенныйКодВалютыРегиона']}'" : "";
            $sqlUpdate .= isset($this->item['БрендПоУмолчанию'])            ? ",`PROPERTY_245`='{$this->item['БрендПоУмолчанию']}'" : "";
            $sqlUpdate .= isset($this->item['КолонкаБренда'])                ? ",`PROPERTY_232`='{$this->item['КолонкаБренда']}'" : "";
            $sqlUpdate .= isset($this->item['КолонкаАртикула'])                ? ",`PROPERTY_233`='{$this->item['КолонкаАртикула']}'" : "";
            $sqlUpdate .= isset($this->item['КолонкаНаименования'])            ? ",`PROPERTY_243`='{$this->item['КолонкаНаименования']}'" : "";
            $sqlUpdate .= isset($this->item['КолонкаВалюты'])                ? ",`PROPERTY_234`='{$this->item['КолонкаВалюты']}'" : "";
            $sqlUpdate .= isset($this->item['КолонкаЦены'])                    ? ",`PROPERTY_235`='{$this->item['КолонкаЦены']}'" : "";
            $sqlUpdate .= isset($this->item['Спеццена'][0])                    ? ",`PROPERTY_238`='{$this->item['Спеццена'][0]}'" : "";
            $sqlUpdate .= isset($this->item['Спеццена'][1])                    ? ",`DESCRIPTION_238`='{$this->item['Спеццена'][1]}'" : "";
            $sqlUpdate .= isset($this->item['Оптовая'][0])                    ? ",`PROPERTY_239`='{$this->item['Оптовая'][0]}'" : "";
            $sqlUpdate .= isset($this->item['Оптовая'][1])                    ? ",`DESCRIPTION_239`='{$this->item['Оптовая'][1]}'" : "";
            $sqlUpdate .= isset($this->item['Мелкооптовая'][0])                ? ",`PROPERTY_240`='{$this->item['Мелкооптовая'][0]}'" : "";
            $sqlUpdate .= isset($this->item['Мелкооптовая'][1])                ? ",`DESCRIPTION_240`='{$this->item['Мелкооптовая'][1]}'" : "";
            $sqlUpdate .= isset($this->item['Розничная'][0])                ? ",`PROPERTY_241`='{$this->item['Розничная'][0]}'" : "";
            $sqlUpdate .= isset($this->item['Розничная'][1])                ? ",`DESCRIPTION_241`='{$this->item['Розничная'][1]}'" : "";
            $sqlUpdate .= isset($this->item['СТО'][0])                        ? ",`PROPERTY_242`='{$this->item['СТО'][0]}'" : "";
            $sqlUpdate .= isset($this->item['СТО'][1])                        ? ",`DESCRIPTION_242`='{$this->item['СТО'][1]}'" : "";

            $sqlUpdate .= $where;
            $DB->Query($sqlUpdate);
        }
        else
        {
            $params = array(
                    'TIMESTAMP_X'=>'NOW()',
                    'MODIFIED_BY'=>"'1'",
                    'DATE_CREATE'=>'NOW()',
                    'CREATED_BY'=>"'1'",
                    'IBLOCK_ID'=>"'17'",
                    'ACTIVE'=>"'Y'",
                    'SORT'=>"'500'",
                    'NAME'=>"'{$this->item['Наименование']}'",
                    'PREVIEW_TEXT'=>"'Склад {$this->item['Наименование']}; Срок доставки в днях {$this->item['СрокДоставкиВДнях']}'",
                    'PREVIEW_TEXT_TYPE'=>"'text'",
                    'SEARCHABLE_CONTENT'=>"'{$this->item['Наименование']}'",
                    'WF_STATUS_ID'=>"'1'",
                    'IN_SECTIONS'=>"'N'");
            $idRegion = $this->idFromMainIB($params);
            $sqlInsert = "INSERT INTO b_iblock_element_prop_s17 (
                            IBLOCK_ELEMENT_ID,
                            PROPERTY_92, 
                            PROPERTY_93,
                            PROPERTY_94,
                            PROPERTY_95,
                            PROPERTY_189";
            $sqlInsert .= isset($this->item['БрендПоУмолчанию'])            ? ",`PROPERTY_245`" : "";
            $sqlInsert .= isset($this->item['КолонкаБренда'])                ? ",`PROPERTY_232`" : "";
            $sqlInsert .= isset($this->item['КолонкаАртикула'])                ? ",`PROPERTY_233`" : "";
            $sqlInsert .= isset($this->item['КолонкаНаименования'])            ? ",`PROPERTY_243`" : "";
            $sqlInsert .= isset($this->item['КолонкаВалюты'])                ? ",`PROPERTY_234`" : "";
            $sqlInsert .= isset($this->item['КолонкаЦены'])                    ? ",`PROPERTY_235`" : "";
            $sqlInsert .= isset($this->item['Спеццена'][0])                    ? ",`PROPERTY_238`" : "";
            $sqlInsert .= isset($this->item['Спеццена'][1])                    ? ",`DESCRIPTION_238`" : "";
            $sqlInsert .= isset($this->item['Оптовая'][0])                    ? ",`PROPERTY_239`" : "";
            $sqlInsert .= isset($this->item['Оптовая'][1])                    ? ",`DESCRIPTION_239`" : "";
            $sqlInsert .= isset($this->item['Мелкооптовая'][0])                ? ",`PROPERTY_240`" : "";
            $sqlInsert .= isset($this->item['Мелкооптовая'][1])                ? ",`DESCRIPTION_240`" : "";
            $sqlInsert .= isset($this->item['Розничная'][0])                ? ",`PROPERTY_241`" : "";
            $sqlInsert .= isset($this->item['Розничная'][1])                ? ",`DESCRIPTION_241`" : "";
            $sqlInsert .= isset($this->item['СТО'][0])                        ? ",`PROPERTY_242`" : "";
            $sqlInsert .= isset($this->item['СТО'][1])                        ? ",`DESCRIPTION_242`" : "";
            $sqlInsert .= ") 
                        VALUES(
                            '{$idRegion}',
                            '{$this->item['Код']}',
                            '{$this->item['КороткоеНазваниеРегиона']}',
                            '{$this->item['Наименование']}',
                            '{$this->item['СрокДоставкиВДнях']}',
                            '{$this->item['БуквенныйКодВалютыРегиона']}'";
            
            $sqlInsert .= isset($this->item['БрендПоУмолчанию'])            ? ",'{$this->item['БрендПоУмолчанию']}'" : "";
            $sqlInsert .= isset($this->item['КолонкаБренда'])                ? ",'{$this->item['КолонкаБренда']}'" : "";
            $sqlInsert .= isset($this->item['КолонкаАртикула'])                ? ",'{$this->item['КолонкаАртикула']}'" : "";
            $sqlInsert .= isset($this->item['КолонкаНаименования'])            ? ",'{$this->item['КолонкаНаименования']}'" : "";
            $sqlInsert .= isset($this->item['КолонкаВалюты'])                ? ",'{$this->item['КолонкаВалюты']}'" : "";
            $sqlInsert .= isset($this->item['КолонкаЦены'])                    ? ",'{$this->item['КолонкаЦены']}'" : "";
            $sqlInsert .= isset($this->item['Спеццена'][0])                    ? ",'{$this->item['Спеццена'][0]}'" : "";
            $sqlInsert .= isset($this->item['Спеццена'][1])                    ? ",'{$this->item['Спеццена'][1]}'" : "";
            $sqlInsert .= isset($this->item['Оптовая'][0])                    ? ",'{$this->item['Оптовая'][0]}'" : "";
            $sqlInsert .= isset($this->item['Оптовая'][1])                    ? ",'{$this->item['Оптовая'][1]}'" : "";
            $sqlInsert .= isset($this->item['Мелкооптовая'][0])                ? ",'{$this->item['Мелкооптовая'][0]}'" : "";
            $sqlInsert .= isset($this->item['Мелкооптовая'][1])                ? ",'{$this->item['Мелкооптовая'][1]}'" : "";
            $sqlInsert .= isset($this->item['Розничная'][0])                ? ",'{$this->item['Розничная'][0]}'" : "";
            $sqlInsert .= isset($this->item['Розничная'][1])                ? ",'{$this->item['Розничная'][1]}'" : "";
            $sqlInsert .= isset($this->item['СТО'][0])                        ? ",'{$this->item['СТО'][0]}'" : "";
            $sqlInsert .= isset($this->item['СТО'][1])                        ? ",'{$this->item['СТО'][1]}'" : "";
            $sqlInsert .=    ");";
            $DB->Query($sqlInsert);
        }
        unset($this->item);
    }
}
?>