<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
global $DB;
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/general/export.autodoc.php");

if(!is_array($arParams["GROUP_PERMISSIONS"]))
    $arParams["GROUP_PERMISSIONS"] = array(1);
if(strlen($arParams["SITE_LIST"])<=0)
    $arParams["SITE_LIST"] = "";

    $arParams["USE_ZIP"] = $arParams["USE_ZIP"]!="N";
$arParams["EXPORT_PAYED_ORDERS"] = (($arParams["EXPORT_PAYED_ORDERS"]=="Y")?true:false);
$arParams["EXPORT_ALLOW_DELIVERY_ORDERS"] = (($arParams["EXPORT_ALLOW_DELIVERY_ORDERS"]=="Y")?true:false);
$arParams["REPLACE_CURRENCY"] = htmlspecialcharsEx($arParams["REPLACE_CURRENCY"]);

@set_time_limit(0);
ini_set('memory_limit','512M');
$status = "";
//$triggerForInsertItems = true;
$bUSER_HAVE_ACCESS = false;
if(isset($GLOBALS["USER"]) && is_object($GLOBALS["USER"]))
{
    $arUserGroupArray = $GLOBALS["USER"]->GetUserGroupArray();
    foreach($arParams["GROUP_PERMISSIONS"] as $PERM)
    {
        if(in_array($PERM, $arUserGroupArray))
        {
            $bUSER_HAVE_ACCESS = true;
            break;
        }
    }
}

$bDesignMode = $GLOBALS["APPLICATION"]->GetShowIncludeAreas() && is_object($GLOBALS["USER"]) && $GLOBALS["USER"]->IsAdmin();
if(!$bDesignMode)
{
    $APPLICATION->RestartBuffer();
    header("Pragma: no-cache");
}

ob_start();

$curPage = substr($APPLICATION -> GetCurPage(), 0, 22);
if($_GET["mode"] == "checkauth" && $USER->IsAuthorized())
{
    echo "success\n";
    echo session_name()."\n";
    echo session_id() ."\n";

    COption::SetOptionString("sale", "export_session_name_".$curPage, session_name());
    COption::SetOptionString("sale", "export_session_id_".$curPage, session_id());
}
elseif(!$USER->IsAuthorized())
{
    echo "failure\n",GetMessage("CC_BSC1_ERROR_AUTHORIZE");
}
elseif(!$bUSER_HAVE_ACCESS)
{
    echo "failure\n",GetMessage("CC_BSC1_PERMISSION_DENIED");
}
elseif(!(CModule::IncludeModule('sale') && CModule::IncludeModule('catalog')))
{
    echo "failure\n",GetMessage("CC_BSC1_ERROR_MODULE");
}
else
{
    if($_GET["mode"] == "query")
    {
        $arFilter = Array();
        if($arParams["EXPORT_PAYED_ORDERS"])
            $arFilter["PAYED"] = "Y";
        if($arParams["EXPORT_ALLOW_DELIVERY_ORDERS"])
            $arFilter["ALLOW_DELIVERY"] = "Y";
        if(strlen($arParams["EXPORT_FINAL_ORDERS"])>0)
        {
            $bNextExport = false;
            $arStatusToExport = Array();
            $dbStatus = CSaleStatus::GetList(Array("SORT" => "ASC"), Array("LID" => LANGUAGE_ID));
            while ($arStatus = $dbStatus->Fetch())
            {
                if($arStatus["ID"] == $arParams["EXPORT_FINAL_ORDERS"])
                    $bNextExport = true;
                if($bNextExport)
                    $arStatusToExport[] = $arStatus["ID"];
            }

            $arFilter["STATUS_ID"] = $arStatusToExport;
        }
        if(strlen($arParams["SITE_LIST"])>0)
            $arFilter["LID"] = $arParams["SITE_LIST"];
        if(strlen(COption::GetOptionString("sale", "last_export_time_committed_".$curPage, ""))>0)
//DG            $arFilter[">=DATE_UPDATE"] = ConvertTimeStamp(COption::GetOptionString("sale", "last_export_time_committed_".$curPage, ""), "FULL");
            $arFilter[">=DATE_INSERT"] = ConvertTimeStamp(COption::GetOptionString("sale", "last_export_time_committed_".$curPage, ""), "FULL");

        COption::SetOptionString("sale", "last_export_time_".$curPage, time());


        CSaleExport::ExportOrders2Xml($arFilter, false, $arParams["REPLACE_CURRENCY"]);
    }
    elseif($_GET["mode"]=="success")
    {
         if($_COOKIE[COption::GetOptionString("sale", "export_session_name_".$curPage, "")] == COption::GetOptionString("sale", "export_session_id_".$curPage, ""))
         {
            COption::SetOptionString("sale", "last_export_time_committed_".$curPage, COption::GetOptionString("sale", "last_export_time_".$curPage, ""));
             echo "success\n";
        }
        else
            echo "error\n";
    }
        elseif($_GET["mode"]=="init")
    {
        $DIR_NAME = "/".COption::GetOptionString("main", "upload_dir")."/1c_exchange/";
        DeleteDirFilesEx($DIR_NAME);
         CheckDirPath($_SERVER["DOCUMENT_ROOT"].$DIR_NAME."/");
        if(!is_dir($_SERVER["DOCUMENT_ROOT"].$DIR_NAME))
        {
            echo "failure\n",GetMessage("CC_BSC1_ERROR_INIT");
        }
        else
        {
            $ht_name = $_SERVER["DOCUMENT_ROOT"].$DIR_NAME."/.htaccess";
            if(!file_exists($ht_name))
            {
                $fp = fopen($ht_name, "w");
                if($fp)
                {
                    fwrite($fp, "Deny from All");
                    fclose($fp);
                    @chmod($ht_name, BX_FILE_PERMISSIONS);
                }
            }

            $_SESSION["BX_CML2_EXPORT"]["zip"] = $arParams["USE_ZIP"] && function_exists("zip_open");
            echo "zip=".($_SESSION["BX_CML2_EXPORT"]["zip"]? "yes": "no")."\n";
            echo "file_limit=0\n";
        }
    }
    elseif($_GET["mode"] == "file")
    {
        $DIR_NAME = "/".COption::GetOptionString("main", "upload_dir")."/1c_exchange/";
        $ABS_FILE_NAME = false;
        $WORK_DIR_NAME = false;

        if(isset($_GET["filename"]) && (strlen($_GET["filename"])>0))
        {
            $filename = trim(str_replace("\\", "/", trim($_GET["filename"])), "/");
            $FILE_NAME = rel2abs($_SERVER["DOCUMENT_ROOT"].$DIR_NAME, "/".$filename);
            if((strlen($FILE_NAME) > 1) && ($FILE_NAME === "/".$filename))
            {
                $ABS_FILE_NAME = $_SERVER["DOCUMENT_ROOT"].$DIR_NAME.$FILE_NAME;
                $WORK_DIR_NAME = substr($ABS_FILE_NAME, 0, strrpos($ABS_FILE_NAME, "/")+1);
            }
        }

        if($ABS_FILE_NAME)
        {
            if(function_exists("file_get_contents"))
                $DATA = file_get_contents("php://input");
            elseif(isset($GLOBALS["HTTP_RAW_POST_DATA"]))
                $DATA = &$GLOBALS["HTTP_RAW_POST_DATA"];
                else
                    $DATA = false;
            if($DATA !== false)
            {
                CheckDirPath($ABS_FILE_NAME);
                if($fp = fopen($ABS_FILE_NAME, "ab"))
                {
                    $result = fwrite($fp, $DATA);
                    if($result === (function_exists("mb_strlen")? mb_strlen($DATA, 'latin1'): strlen($DATA)))
                    {
                        if($_SESSION["BX_CML2_EXPORT"]["zip"])
                            $_SESSION["BX_CML2_EXPORT"]["zip"] = $ABS_FILE_NAME;
                    }
                    else
                    {
                        echo "failure\n",GetMessage("CC_BSC1_ERROR_FILE_WRITE", array("#FILE_NAME#"=>$FILE_NAME));
                    }
                    fclose($fp);
                }
                else
                {
                    echo "failure\n",GetMessage("CC_BSC1_ERROR_FILE_OPEN", array("#FILE_NAME#"=>$FILE_NAME));
                }
            }
            else
            {
                echo "failure\n",GetMessage("CC_BSC1_ERROR_HTTP_READ");
            }

            if(strlen($_SESSION["BX_CML2_EXPORT"]["zip"])>0)
            {
                $file_name = $_SESSION["BX_CML2_EXPORT"]["zip"];

                if(function_exists("zip_open"))
                {
                    $dir_name = substr($file_name, 0, strrpos($file_name, "/")+1);
                    if(strlen($dir_name) <= strlen($_SERVER["DOCUMENT_ROOT"]))
                        return false;

                    $hZip = zip_open($file_name);
                    if($hZip)
                    {
                        while($entry = zip_read($hZip))
                        {
                            $entry_name = zip_entry_name($entry);
                            //Check for directory
                            if(zip_entry_filesize($entry))
                            {
                                $ABS_FILE_NAME = $dir_name.$entry_name;
                                $file_name = $dir_name.$entry_name;
                                CheckDirPath($file_name);
                                $fout = fopen($file_name, "wb");
                                if($fout)
                                {
                                    while($data = zip_entry_read($entry, 102400))
                                    {
                                        $result = fwrite($fout, $data);
                                        if($result !== (function_exists("mb_strlen")? mb_strlen($data, 'latin1'): strlen($data)))
                                            return false;
                                    }
                                }
                            }
                            zip_entry_close($entry);
                        }
                        zip_close($hZip);
                    }
                }
                else
                    echo "error\n".GetMessage("CC_BSC1_UNZIP_ERROR");
            }
            require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/xml.php");
            $sResult = "";
            $new_file_name = $ABS_FILE_NAME;

            $f = fopen($new_file_name, "rb");
            $sResult = fread($f, filesize($new_file_name));
            fclose($f);
            if(strlen($sResult)>0)
            {
                if(toUpper(LANG_CHARSET) != "UTF-8")
                    $sResult = $APPLICATION->ConvertCharset($sResult, "UTF-8", LANG_CHARSET);

                $objXML = new CDataXML();
                $objXML->LoadString($sResult);
                $arResult = $objXML->GetArray();

                $SumFormat = ".";
                $QuantityFormat = ".";
                foreach ($arResult[GetMessage("CC_BSC1_COM_INFO")]["@"] as $key => $val)
                {
                    if($key == GetMessage("SALE_EXPORT_FORM_SUMM"))
                    {
                        $arFormat = explode(";", $val);
                        foreach($arFormat as $val)
                        {
                            if(strpos($val, GetMessage("SALE_EXPORT_FORM_CRD")) !== false)
                                $SumFormat = trim(substr($val, strpos($val, "=")+1));
                        }
                    }
                    elseif($key == GetMessage("SALE_EXPORT_FORM_QUANT"))
                    {
                        $arFormat = explode(";", $val);
                        foreach($arFormat as $val)
                        {
                            if(strpos($val, GetMessage("SALE_EXPORT_FORM_CRD")) !== false)
                                $QuantityFormat = trim(substr($val, strpos($val, "=")+1));
                        }
                    }
                }

                        //  Записываем номер выгрузки заказа из 1С и формируем файл confirm.xml
                $numSent = "";
                $numSent = $arResult["КоммерческаяИнформация"]["#"]["НомерОтправленного"]["0"]["#"];
                UpdateConfirms( "Заказы", $numSent );


                foreach($arResult[GetMessage("CC_BSC1_COM_INFO")]["#"][GetMessage("CC_BSC1_DOCUMENT")] as $value)
                {
                    if($value["#"][GetMessage("CC_BSC1_OPERATION")][0]["#"] == GetMessage("CC_BSC1_ORDER"))
                    {
                        $orderId = IntVal($value["#"][GetMessage("CC_BSC1_NUMBER")][0]["#"]);
                        $arOrder[$orderId] = Array();
                        $arItem = Array();
                        $arOrder[$orderId]["AMOUNT"] = $value["#"][GetMessage("CC_BSC1_SUMM")][0]["#"];
                        $arOrder[$orderId]["AMOUNT"] = str_replace($SumFormat, ".", $arOrder[$orderId]["AMOUNT"]);
                        
                        $arOrder[$orderId]["CURRENCY"] = $value["#"][GetMessage("CC_BSC1_CURRENCY")][0]["#"];//валюта не передавалась
                        $arOrder[$orderId]["COMMENT"] = $value["#"][GetMessage("CC_BSC1_COMMENT")][0]["#"];
                        $arOrder[$orderId]["STATUS"] =  $value["#"][GetMessage("CC_BSC1_STATUS")][0]["#"];

                        foreach($value["#"][GetMessage("CC_BSC1_REK_VALUES")][0]["#"][GetMessage("CC_BSC1_REK_VALUE")] as $val)
                            $arOrder[$orderId]["TRAITS"][$val["#"][GetMessage("CC_BSC1_NAME")][0]["#"]] = $val["#"][GetMessage("CC_BSC1_VALUE")][0]["#"];

                        $taxValue = 0;
                        $taxValueTmp = 0;
                        $taxName = "";
                        if(is_array($value["#"][GetMessage("CC_BSC1_ITEMS")][0]["#"][GetMessage("CC_BSC1_ITEM")]))
                        {
                            foreach($value["#"][GetMessage("CC_BSC1_ITEMS")][0]["#"][GetMessage("CC_BSC1_ITEM")] as $val)
                            {
                                $val = $val["#"];
                                $productID = $val[GetMessage("CC_BSC1_ID")][0]["#"];
                                $bGood = false;
                                $price = str_replace($SumFormat, ".", $val[GetMessage("CC_BSC1_PRICE_PER_UNIT")][0]["#"]);
                                $quantity = str_replace($QuantityFormat, ".", $val[GetMessage("CC_BSC1_QUANTITY")][0]["#"]);
                                $arItem[$productID] = Array(
                                        "NAME" => $val[GetMessage("CC_BSC1_NAME")][0]["#"],
                                        "ARTICLE" => $val[GetMessage("CC_BSC1_ARTICLE")][0]["#"],
                                        "BRAND" => $val[GetMessage("CC_BSC1_BRAND")][0]["#"],
                                        "USERORDERCODE" => $val[GetMessage("CC_BSC1_USERORDERCODE")][0]["#"],
                                        "PRICE" => $price,
                                        "QUANTITY" => $quantity,
                                        "ITEMSTATUS" => $val[GetMessage("CC_BSC1_ITEMSTATUS")][0]["#"]
                                    );

                                if(is_array($val[GetMessage("CC_BSC1_PROPS_ITEMS")][0]["#"][GetMessage("CC_BSC1_PROP_ITEM")]))
                                {
                                    foreach($val[GetMessage("CC_BSC1_PROPS_ITEMS")][0]["#"][GetMessage("CC_BSC1_PROP_ITEM")] as $val1)
                                        $arItem[$productID]["ATTRIBUTES"][$val1["#"][GetMessage("CC_BSC1_NAME")][0]["#"]] = $val1["#"][GetMessage("CC_BSC1_VALUE")][0]["#"];
                                }

                                if(is_array($val[GetMessage("CC_BSC1_REK_VALUES")][0]["#"][GetMessage("CC_BSC1_REK_VALUE")]))
                                {
                                    foreach($val[GetMessage("CC_BSC1_REK_VALUES")][0]["#"][GetMessage("CC_BSC1_REK_VALUE")] as $val1)
                                    {
                                        if($val1["#"][GetMessage("CC_BSC1_NAME")][0]["#"] == GetMessage("CC_BSC1_ITEM_TYPE"))
                                            $arItem[$productID]["TYPE"] = $val1["#"][GetMessage("CC_BSC1_VALUE")][0]["#"];
                                    }
                                }

                                if(strlen($value["#"][GetMessage("CC_BSC1_TAXES")][0]["#"][GetMessage("CC_BSC1_TAX")][0]["#"][GetMessage("CC_BSC1_NAME")][0]["#"])>0)
                                {
                                    $taxValueTmp = $val[GetMessage("CC_BSC1_TAXES")][0]["#"][GetMessage("CC_BSC1_TAX")][0]["#"][GetMessage("CC_BSC1_TAX_VALUE")][0]["#"];
                                    $arItem[$productID]["VAT_RATE"] = $taxValueTmp/100;

                                    if(IntVal($taxValueTmp) > IntVal($taxValue))
                                    {
                                        $taxName = $val[GetMessage("CC_BSC1_TAXES")][0]["#"][GetMessage("CC_BSC1_TAX")][0]["#"][GetMessage("CC_BSC1_NAME")][0]["#"];
                                        $taxValue = $taxValueTmp;
                                    }
                                }
                            }
                        }
                        if(IntVal($taxValue)>0)
                        {
                            $price = str_replace($SumFormat, ".", $value["#"][GetMessage("CC_BSC1_TAXES")][0]["#"][GetMessage("CC_BSC1_TAX")][0]["#"][GetMessage("CC_BSC1_SUMM")][0]["#"]);
                            $arOrder[$orderId]["TAX"] = Array(
                                    "NAME" => $taxName,
                                    "VALUE" =>$taxValue,
                                    "IS_IN_PRICE" => ($value["#"][GetMessage("CC_BSC1_TAXES")][0]["#"][GetMessage("CC_BSC1_TAX")][0]["#"][GetMessage("CC_BSC1_IN_PRICE")][0]["#"]=="true"?"Y":"N"),
                                    "VALUE_MONEY" => $price,
                                );

                        }

                        $arOrder[$orderId]["items"] = $arItem;
                    }
                }

                if(count($arOrder)<=0)
                {
                //dg    echo "failure\n",GetMessage("CC_BSC1_NO_ORDERS_IN_IMPORT");
                }
                else
                {
                    $strError = "";
                    /************************************************************************************************************/
                    //$queryToControllProcess = "INSERT INTO session_control_table VALUES (
                    //                                            NULL, 
                    //                                            NOW(),
                    //                                            'перебор ордеров:".serialize($arOrder)."')";
                    //$DB->Query($queryToControllProcess);
                    /************************************************************************************************************/
                    foreach($arOrder as $k => $v)//$k - ID - ордера, $v - позиции ордера
                    {
                        /************************************************************************************************************/
                        $queryToControllProcess = "INSERT INTO session_control_table VALUES (
                                                                    NULL, 
                                                                    NOW(),
                                                                    'Значение пришедшее с 1С: ".serialize($v)."')";
                        $DB->Query($queryToControllProcess);
                        /************************************************************************************************************/
                           //  храним все изменения в заказе тут
                        unset($arChangedStatus,$arChangedItems,$arDeletedItems,$arInsertedItems,$arToDelete);
                       $arChangedStatus = Array();
                       $arChangedItems = Array();
                       $arDeletedItems = Array();
                       $arInsertedItems = Array();
                       $arToDelete = array();

                        if($orderInfo = CSaleOrder::GetByID($k))//$orderInfo - содержит параметры заказа с сайта
                        {
                            /************************************************************************************************************/
                            $queryToControllProcess = "INSERT INTO session_control_table VALUES (
                                                                        NULL, 
                                                                        NOW(),
                                                                        'Информация по текущей корзине orderInfo :".serialize($orderInfo)."')";//этот массив содержит свойства строки
                            $DB->Query($queryToControllProcess);
                            /************************************************************************************************************/
                                // DG: Обновляем статус заказа

                               switch($v["STATUS"])
                                 {
                                   case "0" :
                                     $status = "N";
                                     break;
                                   case "1" :
                                     $status = "A";
                                     break;
                                   case "2" :
                                     $status = "B";
                                     break;
                                   case "3" :
                                     $status = "C";
                                     break;

                                   default : $status = 'N';
                                 }

                              //  Проверяем, изменился ли статус заказа
                            if( $status != $orderInfo['STATUS_ID'] )
                              {
                                $arChangedStatus['ORDER_CHANGED'] = true;

                                $tmp = CSaleStatus::GetByID( $orderInfo['STATUS_ID'], 'ru' );
                                $arChangedStatus['ORDER_OLD_STATUS'] = $tmp['DESCRIPTION'];

                                $tmp = CSaleStatus::GetByID( $status, 'ru' );
                                $arChangedStatus['ORDER_NEW_STATUS'] = $tmp['DESCRIPTION'];

                              }
                            else
                              $arChangedStatus['ORDER_CHANGED'] = false;



                            CSaleOrder::StatusOrder($k,$status);//изменяем статус заказа на сайте согласно пришедшей информации с 1С

                            if($orderInfo["PAYED"] != "Y" && $orderInfo["ALLOW_DELIVERY"] != "Y" && $orderInfo["STATUS_ID"] != "F")//если ордер не оплачен не разрешена доставка...
                            {

                                $dbOrderTax = CSaleOrderTax::GetList(
                                    array(),
                                    array("ORDER_ID" => $k),
                                    false,
                                    false,
                                    array("ID", "TAX_NAME", "VALUE", "VALUE_MONEY", "CODE", "IS_IN_PRICE")
                                );
                                $bTaxFound = false;
                                if($arOrderTax = $dbOrderTax->Fetch())
                                {
                                    $bTaxFound = true;
                                    if(IntVal($arOrderTax["VALUE"]) != IntVal($v["TAX"]["VALUE"]) || ($arOrderTax["IS_IN_PRICE"] != $v["TAX"]["IS_IN_PRICE"]))
                                    {
                                        if(IntVal($v["TAX"]["VALUE"])>0)
                                        {
                                            $arFields = Array(
                                                    "TAX_NAME" => $v["TAX"]["NAME"],
                                                    "ORDER_ID" => $k,
                                                    "VALUE" => $v["TAX"]["VALUE"],
                                                    "IS_PERCENT" => "Y",
                                                    "IS_IN_PRICE" => $v["TAX"]["IS_IN_PRICE"],
                                                    "VALUE_MONEY" => $v["TAX"]["VALUE_MONEY"],
                                                    "CODE" => "VAT1C",
                                                    "APPLY_ORDER" => "100"
                                                );
                                            CSaleOrderTax::Update($arOrderTax["ID"], $arFields);
                                            CSaleOrder::Update($k, Array("TAX_VALUE" => $v["TAX"]["VALUE_MONEY"]));
                                        }
                                        else
                                        {
                                            CSaleOrderTax::Delete($arOrderTax["ID"]);
                                            CSaleOrder::Update($k, Array("TAX_VALUE" => 0));
                                        }
                                    }
                                }



                                if(!$bTaxFound)
                                {
                                    if(IntVal($v["TAX"]["VALUE"])>0)
                                    {
                                        $arFields = Array(
                                                "TAX_NAME" => $v["TAX"]["NAME"],
                                                "ORDER_ID" => $k,
                                                "VALUE" => $v["TAX"]["VALUE"],
                                                "IS_PERCENT" => "Y",
                                                "IS_IN_PRICE" => $v["TAX"]["IS_IN_PRICE"],
                                                "VALUE_MONEY" => $v["TAX"]["VALUE_MONEY"]
                                            );
                                        CSaleOrderTax::Update($k, $arFields);
                                        CSaleOrder::Update($k, Array("TAX_VALUE" => $v["TAX"]["VALUE_MONEY"]));
                                    }
                                }
                                //выбираем товары по данному заказу с сайта
                                $dbBasket = CSaleBasket::GetList(
                                        array("NAME" => "ASC"),
                                        array("ORDER_ID" => $k)/*,
                                        false,
                                        false,
                                        array("ID","FUSER_ID","ORDER_ID","PRODUCT_ID","PRICE","CURRENCY","NAME","ORDER_PRICE")//описываем какие поля нужны*/
                                    );
                                $basketSum = 0;
                                /************************************************************************************************************/
                                //$queryToControllProcess = "INSERT INTO session_control_table VALUES (
                                //                                            NULL, 
                                //                                            NOW(),
                                //                                            '№ заказа:".$k."')";
                                //$queryToControllProcess .= ",(
                                //                                            NULL, 
                                //                                            NOW(),
                                //                                            '№ заказа:".serialize($dbBasket)."')";
                                //$DB->Query($queryToControllProcess);
                                /************************************************************************************************************/
                                unset($fUser);
                                while ($arBasket = $dbBasket->Fetch())//$arBasket - содержит ассоциативный массив строки заказа с сайта
                                {
                                    unset($arFields);
                                    $arFields = Array();
                                    //записываем свойства текущей корзины
                                    /************************************************************************************************************/
                                    $queryToControllProcess = "INSERT INTO session_control_table VALUES (
                                                                                NULL, 
                                                                                NOW(),
                                                                                'Массив строки заказа:".serialize($arBasket)."')";
                                    $DB->Query($queryToControllProcess);
                                    /************************************************************************************************************/
                                    $curBrandCode     = GetBasketItemProperty($arBasket["ID"],"Brand");        //Буквенный код бренда
                                    $curArticle     = GetBasketItemProperty($arBasket["ID"],"ItemCode");    //артикул без пробелов
                                    $curItemStatus     = GetBasketItemProperty($arBasket["ID"],"ItemStatus");    //цифровой статус строки
                                    //$v["items"] ARTICLE - содержит код,BRAND - содержит буквенный код
                                    /************************************************************************************************************/
                                    //$queryToControllProcess = "INSERT INTO session_control_table VALUES (
                                    //                                            NULL, 
                                    //                                            NOW(),
                                    //                                            'Данные приходящие с 1С № заказа : {$k}')";
                                    //$queryToControllProcess .= ",(
                                    //                                            NULL, 
                                    //                                            NOW(),
                                    //                                            'Данные сайта : curBrandCode = {$curBrandCode}, curArticle = {$curArticle}, curItemStatus = {$curItemStatus}')";
                                    //$queryToControllProcess .= ",(
                                    //                                            NULL, 
                                    //                                            NOW(),
                                    //                                            'v[items]:".serialize($v["items"])."')";
                                    //$DB->Query($queryToControllProcess);
                                    /************************************************************************************************************/
                                           //                                    if(!empty($v["items"][$arBasket["PRODUCT_XML_ID"]]))
                                   if($productXMLID = GetOrderedProductXMLID($curArticle, $curBrandCode, $v["items"]))//проверяем есть ли позиция с сайта в массиве $v["items"]
                                     {
                                        $strChanged = '';
                                          if($arBasket["QUANTITY"] != $v["items"][$productXMLID]["QUANTITY"])//смотрим совпадают ли количества
                                        {
                                            $arFields["QUANTITY"] = $v["items"][$productXMLID]["QUANTITY"];
                                            //$strChanged .= ' Количество |';
                                        }
                                        if($arBasket["PRICE"] != $v["items"][$productXMLID]["PRICE"])//совпадают ли цены
                                        {
                                            $arFields["PRICE"] = $v["items"][$productXMLID]["PRICE"];
                                            //$strChanged .= ' Цена |';
                                        }
                                        $arFields["NAME"] = $v["items"][$productXMLID]["NAME"];
                                          //  обновляем строку заказа (цены и количество)
                                        //если произошли И ДАЖЕ ЕСЛИ НЕТ ... изменения со строками изменяем состояние корзины
                                         $arFields["CURRENCY"] = $v["CURRENCY"];
                                         //CSaleBasket::Update($arBasket["ID"], $arFields);
                                         $sqlUpdateBasket = "UPDATE `b_sale_basket` 
                                                                             SET
                                                                                 `PRICE`            ='{$v['items'][$productXMLID]['PRICE']}',
                                                                                 `CURRENCY`        ='{$v['CURRENCY']}',
                                                                                 `DATE_UPDATE`    =NOW(),
                                                                                 `QUANTITY`        ='{$v['items'][$productXMLID]['QUANTITY']}',
                                                                                 `NAME`            ='{$v['items'][$productXMLID]['NAME']}'
                                                                             WHERE
                                                                                 `ID`            ='{$arBasket['ID']}'";
                                         $DB->Query($sqlUpdateBasket);
                                         unset($arProps);
                                        $arProps = array();
                                           $arProps[] = array("NAME" => "Валюта",                "CODE" => "Currency",        "VALUE" => $v["CURRENCY"]);
                                           $arProps[] = array("NAME" => "Артикул",                "CODE" => "ItemCode",        "VALUE" => $v['items'][$productXMLID]['ARTICLE']);
                                          $arProps[] = array("NAME" => "Код бренда",            "CODE" => "Brand",            "VALUE" => $v['items'][$productXMLID]['BRAND']);
                                          $arProps[] = array("NAME" => "Код региона",            "CODE" => "RegionCode",        "VALUE" => $orderInfo['REGION_CODE']);
                                          foreach ($arProps as $prop)
                                          {
                                            $sqlSaleBasketProps = "UPDATE `b_sale_basket_props` 
                                                                                        SET
                                                                                            `VALUE`    ='{$prop['VALUE']}',
                                                                                            `CODE`    ='{$prop['CODE']}'
                                                                                        WHERE
                                                                                            `NAME`    ='{$prop['NAME']}'
                                                                                        AND
                                                                                            `BASKET_ID`='{$arBasket['ID']}'";
                                            $DB->Query($sqlSaleBasketProps);
                                          }
                                         //CSaleOrder::Update($k, array("CURRENCY" => $v["CURRENCY"]));
                                                      ////  Меняем цену и валюту только что обновленного товара в корзине

                                            //   меняем статус текущей строки заказа
                                        if($curItemStatus != $v["items"][$productXMLID]["ITEMSTATUS"])//если прошли изменения статуса строки заказа, корректируем
                                        {
                                             SetBasketItemProperty($arBasket["ID"], "ItemStatus", $v["items"][$productXMLID]["ITEMSTATUS"]);
                                             $strChanged .= ' Статус|';
                                        }
                                        /************************************************************************************************************/
                                        //$queryToControllProcess = "INSERT INTO session_control_table VALUES (
                                        //                                            NULL, 
                                        //                                            NOW(),
                                        //                                            'GetOrderedProductXMLID IF: {$productXMLID}')";
                                        //$queryToControllProcess .= ",(
                                        //                                            NULL, 
                                        //                                            NOW(),
                                        //                                            'strChanged = {$strChanged}')";
                                        //$DB->Query($queryToControllProcess);
                                        /************************************************************************************************************/
                                        $v["items"][$productXMLID]["CHECKED"] = "Y";
                                        $triggerForDisableItems = false;//триггер отслеживающий изменились ли текущие строки на отказ
                                        if($strChanged !='')//эта чать кода выполняется
                                        {//если произошли изменения с существующими в корзине строками запивыаем изменившиеся строки в отдельный массив
                                            $arTmp = Array();
                                            $arTmp['BCODE']         = $curBrandCode;
                                            $arTmp['ICODE']         = $curArticle;
                                            $arTmp['PRICE']         = $v["items"][$productXMLID]["PRICE"];
                                            $arTmp['QTY']             = $v["items"][$productXMLID]["QUANTITY"];
                                            $arTmp['CAPTION']        = $v["items"][$productXMLID]["NAME"];
                                            $arTmp['CHANGE_DESC']     = $strChanged;
                                            $arTmp['STATUS']         = GetItemStatusDescription($v["items"][$productXMLID]["ITEMSTATUS"]);
                                            $arChangedItems[]         = $arTmp;
                                            $arToCheck[$arBasket["ID"]] = $arTmp;
                                            //фиксируем изменение строки на отказ если статус заказа изменен на закрыт для того чтобы послать в этом случае уведомление
                                            if ($v["items"][$productXMLID]["ITEMSTATUS"] == 2 && $curItemStatus != $v["items"][$productXMLID]["ITEMSTATUS"])
                                            {
                                                $triggerForDisableItems = true;
                                                $arTmp2 = Array();
                                                $arTmp2['BCODE']         = $curBrandCode;
                                                $arTmp2['ICODE']         = $curArticle;
                                                $arTmp2['PRICE']         = $v["items"][$productXMLID]["PRICE"];
                                                $arTmp2['QTY']             = $v["items"][$productXMLID]["QUANTITY"];
                                                $arTmp2['CAPTION']        = $v["items"][$productXMLID]["NAME"];
                                                $arTmp2['CHANGE_DESC']     = $strChanged;
                                                $arTmp2['STATUS']         = GetItemStatusDescription($v["items"][$productXMLID]["ITEMSTATUS"]);
                                                $arDisableItems[]         = $arTmp2;
                                            }
                                        }
                                    }
                                    else//если указанного товара нету в заказе с 1С его необходимо удалить
                                    {
                                        /************************************************************************************************************/
                                        //$queryToControllProcess = "INSERT INTO session_control_table VALUES (
                                        //                                            NULL, 
                                        //                                            NOW(),
                                        //                                            'GetOrderedProductXMLID ELSE:".serialize($productXMLID)."')";
                                        //$DB->Query($queryToControllProcess);
                                        /************************************************************************************************************/
                                        
                                       $arTmp = Array();
                                       $arTmp['BCODE']         = $curBrandCode;
                                       $arTmp['ICODE']         = $curArticle;
                                       $arTmp['PRICE']         = $arBasket["PRICE"];
                                       $arTmp['QTY']         = $arBasket["QUANTITY"];
                                       $arTmp['CAPTION']     = $arBasket['NAME'];
                                       $arTmp['STATUS']     = GetItemStatusDescription($v["items"][$productXMLID]["ITEMSTATUS"]);

                                       //$arDeletedItems[] = $arTmp;
                                       $arToDelete[$arBasket["ID"]] = $arTmp;
                                          //CSaleBasket::Delete($arBasket["ID"]);//удаляется товар в корзине
                                    }
                                    if(!isset($fUser)) $fUser = $arBasket["FUSER_ID"];
                                }//окончание цикла while ($arBasket = $dbBasket->Fetch())
                                foreach ($arToCheck as $basketId=>$basketPositions)
                                {
                                    unset($arToDelete[$basketId]);
                                }
                                foreach ($arToDelete as $basketId=>$basketPositions)
                                {
                                    CSaleBasket::Delete($basketId);
                                }

                                 $counter = 0;

                                foreach($v["items"] as $itemID => $arItem)
                                {
                                    /************************************************************************************************************/
                                    //$queryToControllProcess = "INSERT INTO session_control_table VALUES (
                                    //                                            NULL, 
                                    //                                            NOW(),
                                    //                                            'itemID:".serialize($itemID)."')";//этот массив содержит свойства строки
                                    //$queryToControllProcess .= ",(
                                    //                                            NULL, 
                                    //                                            NOW(),
                                    //                                            'arItem:".serialize($arItem)."')";//этот массив ничего не содержит
                                    //$DB->Query($queryToControllProcess);
                                    /************************************************************************************************************/
                                            //  для товаров, не найденных в заказе насайте
                                    if($arItem["CHECKED"] != "Y")//признак того что данный товар отсутствует пока в корзине пользователя
                                    {
                                        /************************************************************************************************************/
                                        $queryToControllProcess = "INSERT INTO session_control_table VALUES (
                                                                                    NULL, 
                                                                                    NOW(),
                                                                                    'добавляем товар:".serialize($arItem)."')";//этот массив содержит свойства строки
                                        $DB->Query($queryToControllProcess);
                                        /************************************************************************************************************/
                                        //попробуем найти товар в общей базе
                                        //зададим условия поиска
                                        $arBrandsID_fromShort = GetAllBrandsProperties();
                                        $arItem['BRAND'] = trim($arItem['BRAND']);//даляем лишние пробелы
                                        $arItem['ARTICLE'] = trim($arItem['ARTICLE']);
                                        $arBrandsID_fromFool = GetAllBrandsPropertiesFromFullName();
                                        //
                                        $brandCode1C = strlen($arItem['BRAND']) > 2 ? $arBrandsID_fromFool[$arItem["BRAND"]] : $arBrandsID_fromShort[$arItem["BRAND"]];
                                        if ($brandCode1C == '' || $arItem['ARTICLE'] == '') continue;//если пустые значения артикула или кода пропускаем товар
                                        $where = " WHERE ItemCode = '{$arItem['ARTICLE']}' AND BrandCode = '{$brandCode1C}'";
                                        
                                        $sqlExist = "SELECT `id` FROM `b_autodoc_items_m`".$where." UNION SELECT `id` FROM `b_autodoc_items_1c_m`".$where;
                                        $resExist = $DB->Query($sqlExist,true);
                                        $resExistRow = intval($resExist->SelectedRowsCount());
                                        if ($resExistRow == 0)//обавляем товар если он отсутствует в таблицах
                                        {
                                            $sqlExistInsert = "INSERT INTO b_autodoc_items_m (
                                                                    `id`,
                                                                    `BrandCode`, 
                                                                    `ItemCode`, 
                                                                    `Caption`,
                                                                    `1CRef`) 
                                                                VALUES(
                                                                    NULL,
                                                                    '{$brandCode1C}',
                                                                    '{$arItem['ARTICLE']}',
                                                                    '{$arItem['NAME']}',
                                                                    '1');";
                                            $DB->Query($sqlExistInsert);
                                            $product_ID = $DB->LastID();
                                        }
                                        else 
                                        {
                                            $resTmp = $resExist->Fetch();
                                            $product_ID = $resTmp['id'];
                                        }
                                        
                                        //делаем прямые запросы к БД, так как обычные функции не работают
                                        $sqlSaleBasket = "INSERT INTO `b_sale_basket` (
                                                                                    ID,
                                                                                    FUSER_ID,
                                                                                    ORDER_ID,
                                                                                    PRODUCT_ID,
                                                                                    PRODUCT_PRICE_ID,
                                                                                    PRICE,
                                                                                    CURRENCY,
                                                                                    DATE_INSERT,
                                                                                    DATE_UPDATE,
                                                                                    WEIGHT,
                                                                                    QUANTITY,
                                                                                    LID,
                                                                                    DELAY,
                                                                                    NAME,
                                                                                    CAN_BUY,
                                                                                    MODULE)
                                                                            VALUES (
                                                                                    NULL,
                                                                                    '{$fUser}',
                                                                                    '{$k}',
                                                                                    '{$product_ID}',
                                                                                    '{$product_ID}',
                                                                                    '{$arItem['PRICE']}',
                                                                                    '{$v['CURRENCY']}',
                                                                                    NOW(),
                                                                                    NOW(),
                                                                                    0,
                                                                                    '{$arItem['QUANTITY']}',
                                                                                    '{$orderInfo['LID']}',
                                                                                    'N',
                                                                                    '{$arItem['NAME']}',
                                                                                    'Y',
                                                                                    'AUTODOC')";
                                        $DB->Query($sqlSaleBasket);
                                        $idInsertingPosition = $DB->LastID();
                                        unset($arProps);
                                        $arProps = array();
                                           $arProps[] = array("NAME" => "Валюта",                "CODE" => "Currency",        "VALUE" => $v["CURRENCY"]);
                                           $arProps[] = array("NAME" => "Артикул",                "CODE" => "ItemCode",        "VALUE" => $arItem["ARTICLE"]);
                                          $arProps[] = array("NAME" => "Код бренда",            "CODE" => "Brand",            "VALUE" => $arItem["BRAND"]);
                                          $arProps[] = array("NAME" => "Код региона",            "CODE" => "RegionCode",        "VALUE" => $orderInfo["REGION_CODE"]);
                                          $arProps[] = array("NAME" => "Статус строки заказа","CODE" => "ItemStatus",        "VALUE" => $arItem["ITEMSTATUS"]);
                                          foreach ($arProps as $prop)
                                          {
                                            $sqlSaleBasketProps = "INSERT INTO `b_sale_basket_props` (
                                                                                                    ID,
                                                                                                    BASKET_ID,
                                                                                                    NAME,
                                                                                                    VALUE,
                                                                                                    CODE,
                                                                                                    SORT)
                                                                                            VALUES (
                                                                                                    NULL,
                                                                                                    '{$idInsertingPosition}',
                                                                                                    '{$prop['NAME']}',
                                                                                                    '{$prop['VALUE']}',
                                                                                                    '{$prop['CODE']}',
                                                                                                    '100')";
                                            $DB->Query($sqlSaleBasketProps);
                                          }
                                          //CSaleOrder::Update($k, array("CURRENCY" => $v["CURRENCY"]));
                                         $arTmp = Array();
                                        $arTmp['BCODE'] = $arItem["BRAND"];
                                        $arTmp['ICODE'] = $arItem["ARTICLE"];
                                        $arTmp['PRICE'] = $arItem["PRICE"];
                                        $arTmp['CAPTION'] = $arItem["NAME"];
                                        $arTmp['QTY'] = $arItem["QUANTITY"];
                                        $arTmp['STATUS'] = GetItemStatusDescription($v["items"][$productXMLID]["ITEMSTATUS"]);
                                        $arInsertedItems[] = $arTmp;
                                    }
                                }
                                //меняем общую сумму и валюту ордера если она не совпадает с пришедшей с 1С
                                if($v["AMOUNT"]         != $orderInfo["PRICE"] 
                                    || $v["CURRENCY"]     != $orderInfo["CURRENCY"]) CSaleOrder::Update($k, array("PRICE"     => $v["AMOUNT"],
                                                                                                                "CURRENCY"     => $v["CURRENCY"]));
                            }//окончание условия if($orderInfo["PAYED"] != "Y" && $orderInfo["ALLOW_DELIVERY"] != "Y" && $orderInfo["STATUS_ID"] != "F")
                            else
                            {
                                //$strError .= "\n".GetMessage("CC_BSC1_FINAL_NOT_EDIT", Array("#ID#" => $k));
                            }

                            $arAditFields = Array();
                            if($v["TRAITS"][GetMessage("CC_BSC1_CANCELED")] == "true")
                            {
                                if($orderInfo["CANCELED"] == "N")
                                    CSaleOrder::CancelOrder($k, "Y", $v["COMMENT"]);
                            }
                            else
                            {
                                if(strlen($v["TRAITS"][GetMessage("CC_BSC1_1C_PAYED_DATE")])>1)
                                {
                                    if($orderInfo["PAYED"]=="N")
                                        CSaleOrder::PayOrder($k, "Y");
                                    $arAditFields["PAY_VOUCHER_DATE"] = CDatabase::FormatDate($v["TRAITS"][GetMessage("CC_BSC1_1C_PAYED_DATE")], "YYYY-MM-DD HH:MI:SS", CLang::GetDateFormat("FULL", LANG));
                                    if(strlen($v["TRAITS"][GetMessage("CC_BSC1_1C_PAYED_NUM")])>0)
                                        $arAditFields["PAY_VOUCHER_NUM"] = $v["TRAITS"][GetMessage("CC_BSC1_1C_PAYED_NUM")];
                                }
                                if(strlen($v["TRAITS"][GetMessage("CC_BSC1_1C_DELIVERY_DATE")])>1)
                                {
                                    if($orderInfo["ALLOW_DELIVERY"]=="N")
                                        CSaleOrder::DeliverOrder($k, "Y");
                                    $arAditFields["DATE_ALLOW_DELIVERY"] = CDatabase::FormatDate($v["TRAITS"][GetMessage("CC_BSC1_1C_DELIVERY_DATE")], "YYYY-MM-DD HH:MI:SS", CLang::GetDateFormat("FULL", LANG));
                                    if(strlen($arParams["FINAL_STATUS_ON_DELIVERY"])>0 && $orderInfo["STATUS_ID"] != "F" && $orderInfo["STATUS_ID"] != $arParams["FINAL_STATUS_ON_DELIVERY"])
                                        CSaleOrder::StatusOrder($k, $arParams["FINAL_STATUS_ON_DELIVERY"]);
                                }
                            }

                            if(count($arAditFields)>0)
                                CSaleOrder::Update($k, $arAditFields);


                            //UpdateCurrency4BasketItems( $orderId, $v["CURRENCY"] );

                            //  dg : Отправляем сообщение по электронной почте о изменениях в заказе




                           $strChanged = '';
                           $strChangedXX = '';
                           $triggerForInsertItems = true;
                           $triggerForInsertItemsXX = true;
                           $deletePosition = count($arToDelete);//было $arDeletedItems
                           $insertPosition = count($arInsertedItems);
                                   //  Если изменился статус заказа - пишем об этом в письме
                           if($arChangedStatus['ORDER_CHANGED'])
                             {
                               //$strChanged .= "\nИзменился статус заказа:\n";
                               //$strChanged .= ' - Старый статус :'.$arChangedStatus['ORDER_OLD_STATUS']."\n";
                               //если статус отложен тогда не нужно выводить строку "- Новый статус"
                               $strChanged .= $v["STATUS"] != 3 ? $arChangedStatus['ORDER_NEW_STATUS']."\n" : " \n";
                             }
                             //если заказ закрыт по причине отказа некоторых позиций оставляем $triggerForDisableItems без изменений
                            if ($v["STATUS"] == 3 && $arChangedStatus['ORDER_CHANGED'] && $triggerForDisableItems)
                            {
                                 $triggerForInsertItems = false;
                            }
                            else 
                            {
                                $triggerForDisableItems = false;
                            }

                                       //  Если есть измененные товары ( изменилась цена, количество или стат4ус )
                            if( count($arChangedItems) > 0 )
                              {
                                   $strChanged .= "\nИзменены позиции\n";
                                   $strChanged .= "-------------------------------------------------------------\n";
                                 //$strChanged .= "\n";
                                 //если если заказ закрыт по причине отказа некоторых позиций передаем на обработку позиции которые получили статус отказ иначе обычные измененные позиции
                                 $strChangedBody = ChangedItemsToString($triggerForDisableItems ? $arDisableItems : $arChangedItems, $v["CURRENCY"]);
                                   $strChanged .= $strChangedBody;
                                 $triggerForInsertItems = false;
                              }

                                       //  Если есть удаленные товары
                            /*if($deletePosition > 0)
                              {
                                   $strChangedXX .= "\nПозиции\n";
                                   $strChangedXX .= "-------------------------------------------------------------\n";

                                   $strChangedXX .= DeletedItemsToString( $arDeletedItems, $v["CURRENCY"] );
                                 $triggerForInsertItemsXX = false;
                              }*/

                                       //  Если есть добавленные товары
                            /*if($insertPosition > 0)
                              {
                                   $strChangedXX .= "\nЗаменены на\n";
                                   $strChangedXX .= "-------------------------------------------------------------\n";

                                   $strChangedXX .= InsertedItemsToString( $arInsertedItems, $v["CURRENCY"] );
                                 $triggerForInsertItemsXX = false;
                              }*/
                              //сообщение об изменении позиций
                              if ($insertPosition == $deletePosition && $deletePosition > 0)
                              {
                                  $strChangedXX = replacementToString($arToDelete,$arInsertedItems,$v["CURRENCY"]);//1 - было $arDeletedItems
                                  $triggerForInsertItemsXX = false;
                              }


                            //при изменении статуса не выводить списка строк заказа
                              switch( $status )
                                 {
                                   case "N" :
                                     $status = "Заказ рассматривается";
                                     $triggerForInsertItems = false;
                                     break;
                                   case "A" :
                                     $status = "Заказ подтвержден";
                                     $triggerForInsertItems = false;
                                     break;
                                   case "B" :
                                     $status = "Заказ отложен";
                                     $triggerForInsertItems = false;
                                     break;
                                   case "C" :
                                     $status = "Заказ закрыт";
                                     break;

                                   default : $status = '';
                                 }

                                 
                            $filter = Array( "ID" => $orderInfo["USER_ID"] );
                            $rsUsers = CUser::GetList(($by="id"), ($order="desc"), $filter);
                            $arUser = $rsUsers->Fetch();
                            $strItemsHeader = "Состав заказа\n-------------------------------------------------------------\n";
                            $strItemsBody = OrderItemsToString($k);
                            $strItemsReal = $triggerForInsertItems ? $strItemsHeader.$strItemsBody : "";
                            $userEmail = $arUser["EMAIL"];

                            $arEmlFields = Array(
                                "USER_ID" => $orderInfo["USER_ID"],
                                "ORDER_ID" => $k,
                                "TEXT" => $v["COMMENT"],
                                "ORDER_DATE" => $orderInfo["DATE_INSERT_FORMAT"],
                                "ORDER_STATUS" => $status,
                                "ORDER_DESCRIPTION" => $orderInfo["COMMENTS"],
                                "SITE_NAME"=> "Автодок-Партс",
                                "ITEMS" => $strItemsReal,
                                "CHANGES" => $strChanged,
                                "EMAIL"=> $userEmail
                            );
                            
                            $strItemsReal = $triggerForInsertItemsXX ? $strItemsHeader.$strItemsBody : "";
                            $arEmlFieldsXX = Array(
                                "USER_ID" => $orderInfo["USER_ID"],
                                "ORDER_ID" => $k,
                                "TEXT" => $v["COMMENT"],
                                "ORDER_DATE" => $orderInfo["DATE_INSERT_FORMAT"],
                                "ORDER_STATUS" => $status,
                                "ORDER_DESCRIPTION" => $orderInfo["COMMENTS"],
                                "SITE_NAME"=> "Автодок-Партс",
                                "ITEMS" => $strItemsReal,
                                "CHANGES" => $strChangedXX,
                                "EMAIL"=> /*$userEmail*/'osipov@itekgold.com'
                            );

                            //my_logger( $arEmlFields );





                            $event = new CEvent;
                                //уведомление о том что заказ принят
                            if(getOrderSentMailStatus($k) == 0)
                              {
                                   if($status != "Заказ закрыт")                 $event->SendImmediate( "NEW_ORDER_RECEIVED", "s1", $arEmlFields);
                                   if($status != "Заказ закрыт")                 setOrderSentMailStatus( $k, 1 );  //  mail sent successfully
                              }
                              //отправка при изменении статуса заказа
                            if($arChangedStatus['ORDER_CHANGED'] 
                                    && $status != "Заказ закрыт" 
                                    && $status != "Заказ подтвержден"
                                    && $status != "Заказ отложен"
                                    && $deletePosition == $insertPosition
                                    && $deletePosition == 0)                 $event->SendImmediate( "ORDER_STATUS_CHANGED", "s1", $arEmlFields);
                                //отправка при изменении статуса строки
                            elseif ($strChanged != '' 
                                    && $status != "Заказ закрыт"
                                    && $status != "Заказ подтвержден"
                                    && $status != "Заказ отложен"
                                    && $deletePosition == $insertPosition
                                    && $deletePosition == 0)                 $event->SendImmediate( "ORDER_STATUS_CHANGED", "s1", $arEmlFields);
                                //отправка сообщения если заказ отложен
                            elseif ($strChanged != '' 
                                    && $status != "Заказ закрыт"
                                    && $status != "Заказ подтвержден"
                                    && $status == "Заказ отложен"
                                    && $deletePosition == $insertPosition
                                    && $deletePosition == 0)                {$event->SendImmediate( "SALE_STATUS_CHANGED_B", "s1", $arEmlFields);setOrderSentMailStatus($k,1); }
                            elseif ($triggerForDisableItems)                $event->SendImmediate( "ORDER_STATUS_CHANGED", "s1", $arEmlFields);
                            //условие отпавки сообщений при замене позиций
                            if ($strChangedXX != '' 
                                    && $deletePosition == $insertPosition)     $event->SendImmediate( "ORDER_STATUS_CHANGED", "s1", $arEmlFieldsXX);

                        }//окончание условия if($orderInfo = CSaleOrder::GetByID($k))
                        else
                            $strError .= "\n".GetMessage("CC_BSC1_ORDER_NOT_FOUND", Array("#ID#" => $k));
                    }


                    if(strlen($strError)>0)
                        echo $strError;
                    else
                      echo "success\n";
                }
            }
            else
                echo "failure\n".GetMessage("CC_BSC1_EMPTY_CML");
        }
    }
    else
    {
        echo "failure\n",GetMessage("CC_BSC1_ERROR_UNKNOWN_COMMAND");
    }
}

$contents = ob_get_contents();
ob_end_clean();

if(!$bDesignMode)
{
    if(toUpper(LANG_CHARSET) != "WINDOWS-1251")//WINDOWS-1251
        $contents = $APPLICATION->ConvertCharset($contents, LANG_CHARSET, "windows-1251");
    $str = (function_exists("mb_strlen")? mb_strlen($contents, 'latin1'): strlen($contents));
    if($_GET["mode"] == "query")
    {
        header("Content-Type: application/xml; charset=windows-1251");//
        header("Content-Length: ".$str);
    }
    else
    {
        header("Content-Type: text/html; charset=windows-1251");//
    }

    echo $contents;
    die();
}
else
{
    $this->IncludeComponentLang(".parameters.php");
    $arStatuses = Array("" => GetMessage("CP_BCI1_NO"));
    CModule::IncludeModule("sale");
    $dbStatus = CSaleStatus::GetList(Array("SORT" => "ASC"), Array("LID" => LANGUAGE_ID));
    while ($arStatus = $dbStatus->GetNext())
    {
        $arStatuses[$arStatus["ID"]] = "[".$arStatus["ID"]."] ".$arStatus["NAME"];
    }

    ?><table class="data-table">
    <tr><td><?echo GetMessage("CP_BCI1_SITE_LIST")?></td><td><?echo $arParams["SITE_LIST"]?></td></tr>
    <tr><td><?echo GetMessage("CP_BCI1_EXPORT_PAYED_ORDERS")?></td><td><?echo $arParams["EXPORT_PAYED_ORDERS"]? GetMessage("MAIN_YES"): GetMessage("MAIN_NO")?></td></tr>
    <tr><td><?echo GetMessage("CP_BCI1_EXPORT_ALLOW_DELIVERY_ORDERS")?></td><td><?echo $arParams["EXPORT_ALLOW_DELIVERY_ORDERS"]? GetMessage("MAIN_YES"): GetMessage("MAIN_NO")?></td></tr>
    <tr><td><?echo GetMessage("CP_BCI1_EXPORT_FINAL_ORDERS")?></td><td><?echo $arStatuses[$arParams["EXPORT_FINAL_ORDERS"]]?></td></tr>
    <tr><td><?echo GetMessage("CP_BCI1_FINAL_STATUS_ON_DELIVERY")?></td><td><?echo $arStatuses[$arParams["FINAL_STATUS_ON_DELIVERY"]]?></td></tr>
    <tr><td><?echo GetMessage("CP_BCI1_REPLACE_CURRENCY")?></td><td><?echo $arParams["REPLACE_CURRENCY"]?></td></tr>
    <tr><td><?echo GetMessage("CP_BCI1_USE_ZIP")?></td><td><?echo $arParams["USE_ZIP"]? GetMessage("MAIN_YES"): GetMessage("MAIN_NO")?></td></tr>
    </table>
    <?
}
?>