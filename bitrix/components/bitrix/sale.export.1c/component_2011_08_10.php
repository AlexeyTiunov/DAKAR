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
                    require_once "{$_SERVER['DOCUMENT_ROOT']}/bitrix/components/bitrix/sale.export.1c/UpdateBasketITG.php";
                    foreach($arOrder as $orderNum => $orderPosition)//$k - ID - ордера, $v - позиции ордера
                    {                
                        $updateBasket = new UpdateBasketITG($orderNum,$orderPosition);
                        $orderChanges = $updateBasket->getChangesOrder();
                        $insertPosition = $updateBasket->getInsertPosition();
                        $deletePosition = $updateBasket->getDeletePosition();
                        $statusPosition = $updateBasket->getStatusChangedPosition();
                        
                        $filter = array("ID"=>$updateBasket->user);
                        $rsUsers = CUser::GetList(($by="id"), ($order="desc"), $filter);
                        $arUser = $rsUsers->Fetch();
                        $userEmail = $arUser["EMAIL"];
                        unset($arFields);
 		                $arFields = array(
			                            "USER_ID" => $updateBasket->user,
			                            "ORDER_ID" => $orderNum,
			                            "TEXT" => "",
			                            "ORDER_DATE" => $updateBasket->dateInsert,
			                            "ORDER_STATUS" => $updateBasket->status,
			                            "ORDER_DESCRIPTION" => "",
			                            "SITE_NAME"=> "Автодок-Партс",
			                            "ITEMS" => "",
			                            "CHANGES" => "",
			                            "EMAIL"=> $userEmail
			                	        );
			            
			            $changesHeader = "\nИзменены позиции\n";
			            $changesHeader .= "-------------------------------------------------------------\n";
                        $event = new CEvent;
                        if (getOrderSentMailStatus($orderNum) == 0)//если не произошли изменения касающиеся всего ордера и посылается сообщение в первый раз
                        {
                        	if (count($orderChanges) > 0 || UpdateBasketITG::$statusOrderName[$orderChanges["STATUS"]["NEW"]] != "Заказ закрыт")
                        	{
                        		if (UpdateBasketITG::$statusOrderName[$orderChanges["STATUS"]["NEW"]] != "Заказ отложен")
                        		{//изменился статус заказа
	                        		$event->SendImmediate( "NEW_ORDER_RECEIVED", "s1", $arFields);
	                        		setOrderSentMailStatus($orderNum,1);
                        		}
                        		else 
                        		{//если товар отложен и посылается сразу об этом сообщение
                        			$event->SendImmediate( "SALE_STATUS_CHANGED_B", "s1", $arFields);
                        			setOrderSentMailStatus($orderNum,1);
                        		}
                        	}
                        }
                        else //если сообщение послыается повторно
                        {//если статус не изменен и не закрыт //если статус изменен и не закрыт
                        	if ((count($orderChanges) == 0 && $updateBasket->status != "Заказ закрыт")
                        		|| (count($orderChanges) > 0 && UpdateBasketITG::$statusOrderName[$orderChanges["STATUS"]["NEW"]] != "Заказ закрыт"))
                        	{
                        		if (UpdateBasketITG::$statusOrderName[$orderChanges["STATUS"]["NEW"]] == "Заказ отложен")
                        		{
                        			$event->SendImmediate("SALE_STATUS_CHANGED_B", "s1", $arFields);
                        		}
                        		elseif (count($statusPosition) > 0)//изменились статусы строк
                        		{
                        			$arFields["CHANGES"] = $changesHeader.ChangedItemsToString($statusPosition,$updateBasket->currency);
                        			$event->SendImmediate("ORDER_STATUS_CHANGED", "s1", $arFields);
                        		}
                        		elseif (count($insertPosition)>0 && count($deletePosition) == count($insertPosition)>0)//произошла замена
                        		{
			                        $arFields["CHANGES"] = $changesHeader.replacementToString($deletePosition,$insertPosition,$updateBasket->currency);
                        			$event->SendImmediate("ORDER_STATUS_CHANGED", "s1", $arFields);
                        		}
                        	}
                        	elseif (UpdateBasketITG::$statusOrderName[$orderChanges["STATUS"]["NEW"]] == "Заказ закрыт" && count($statusPosition) > 0)
                        	{//если заказ закрыт по причине отказа некоторых позиций
                        	    unset($refusal);
                        		$refusal = array();
                        		foreach ($statusPosition as $position)
                        		{
                        			if ($position["STATUS"] == "Отказ") $refusal[] = $position;
                        		}
                        		if (count($refusal)>0)
                        		{
                        			$arFields["CHANGES"] = $changesHeader.ChangedItemsToString($refusal,$updateBasket->currency);
                        			$event->SendImmediate("ORDER_STATUS_CHANGED", "s1", $arFields);
                        		}
                        	}
                        }
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