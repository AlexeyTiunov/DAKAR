<?php
require_once "{$_SERVER["DOCUMENT_ROOT"]}/bitrix/php_interface/include/autodoc_globals.php";
require_once "{$_SERVER["DOCUMENT_ROOT"]}/bitrix/components/itg/1c_exchange.import/MainImport.php";

class ImportAgreement extends MainImport
{
    function __construct($params)
    {
        parent::__construct($params);
    }
    /*function __destruct()
    {
        //$this->params['НомерОтправленного'] = $this->xpath->query("//НомерОтправленного")->item(0)->nodeValue;
        UpdateConfirms("Договора", $this->xpath->query("//НомерОтправленного")->item(0)->nodeValue);
    }*/
    protected function getDesiredNodes()
    {
        $this->params['UpdateConfirms'] = "Договора";
        $this->params['numberSend'] = $this->xpath->query("//НомерОтправленного")->item(0)->nodeValue;
        $this->nodesChecked = $this->xpath->query("//Договор");
        //echo "this->nodesChecked <pre>";
        //echo print_r($this->nodesChecked);
        //echo "</pre>";
    }
    protected function changeSameItemForImport($nodeName,$nodeValue,$node = null)
    {
        parent::getProcessList();
        ////echo "{$nodeName}:{$nodeValue}<br/>";
        switch ($nodeName)
        {
            case "Валюта":
                ($nodeValue == 'грн') ? $this->item[$nodeName] = "UAH" : $this->item[$nodeName] = trim($nodeValue);
                break;
            /*case "КодКонтрагента":
                $this->item[$nodeName] = intval($nodeValue);
                break;*/
            default:
                $this->item[$nodeName] = addslashes(trim($nodeValue));
                break;
        }
    }
    protected function updateForImport()
    {
        global $DB;
        //echo "user - {$this->item['КодКонтрагента']}<br/>";
        //$user = CUser::GetByID(GetUserIDByID_1C($this->item['КодКонтрагента']));
        ////echo "user - {$user}<br/>";
        $user = GetUserIDByID_1C($this->item['КодКонтрагента']);
        if ($user)
        {
            $where = " WHERE `Code`='{$this->item['Код']}' AND `ClientCode`='{$this->item['КодКонтрагента']}'";
            $idAgreem = $this->getExistId('b_autodoc_agreements', $where, "ID");
            if ($idAgreem == -1)
            {
                $sqlInsert = "INSERT INTO `b_autodoc_agreements` (
                                                                                    `ID`,
                                                                                    `Code`,
                                                                                    `ClientCode`,
                                                                                    `CurrencyCode`,
                                                                                    `Caption`";
                $sqlInsert .= isset($this->item['ПроцентПредоплаты'])             ? ",`MinPercent`"                 : "";
                $sqlInsert .= isset($this->item['СуммаКредита'])                 ? ",`CreditSum`"                 : "";
                $sqlInsert .= isset($this->item['Цена'])                         ? ",`PriceColCode`"             : "";
                $sqlInsert .= isset($this->item['ДатаНачалаВзаиморассчетов'])     ? ",`DateBeginningReference`"     : "";
                $sqlInsert .= isset($this->item['СуммаНачВзаиморассчетов'])     ? ",`BalanceOnDate`)"             : ")";
                $sqlInsert .= "                                                VALUES (
                                                                                    NULL,
                                                                                    '{$this->item['Код']}',
                                                                                    '{$this->item['КодКонтрагента']}',
                                                                                    '{$this->item['Валюта']}',
                                                                                    '{$this->item['Наименование']}'";
                $sqlInsert .= isset($this->item['ПроцентПредоплаты'])             ? ",'{$this->item['ПроцентПредоплаты']}'"             : "";
                $sqlInsert .= isset($this->item['СуммаКредита'])                 ? ",'{$this->item['СуммаКредита']}'"                 : "";
                $sqlInsert .= isset($this->item['Цена'])                         ? ",'{$this->item['Цена']}'"                         : "";
                $sqlInsert .= isset($this->item['ДатаНачалаВзаиморассчетов'])     ? ",'{$this->item['ДатаНачалаВзаиморассчетов']}'"     : "";
                $sqlInsert .= isset($this->item['СуммаНачВзаиморассчетов'])     ? ",'{$this->item['СуммаНачВзаиморассчетов']}')"     : ")";
                //echo $sqlInsert."<br/>";
                $DB->Query($sqlInsert);
            }
            else 
            {
                $sqUpdate = "UPDATE `b_autodoc_agreements` SET
                                                                                `Code`                    ='{$this->item['Код']}',
                                                                                `ClientCode`            ='{$this->item['КодКонтрагента']}',
                                                                                `CurrencyCode`            ='{$this->item['Валюта']}',
                                                                                `Caption`                ='{$this->item['Наименование']}'";
                $sqUpdate .= isset($this->item['ПроцентПредоплаты'])         ? ",`MinPercent`            ='{$this->item['ПроцентПредоплаты']}'"             : "";
                $sqUpdate .= isset($this->item['СуммаКредита'])             ? ",`CreditSum`                ='{$this->item['СуммаКредита']}'"                 : "";
                $sqUpdate .= isset($this->item['Цена'])                     ? ",`PriceColCode`            ='{$this->item['Цена']}'"                         : "";
                $sqUpdate .= isset($this->item['ДатаНачалаВзаиморассчетов'])? ",`DateBeginningReference`='{$this->item['ДатаНачалаВзаиморассчетов']}'"     : "";
                $sqUpdate .= isset($this->item['СуммаНачВзаиморассчетов'])     ? ",`BalanceOnDate`            ='{$this->item['СуммаНачВзаиморассчетов']}'"     : "";
                //echo $sqUpdate."<br/>";
                $DB->Query($sqUpdate.$where);
            }
        }
    }
}
?>