<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/general/export.autodoc.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/components/itg/tmp.table/CitgTempTable.php");

if (!CModule::IncludeModule("sale"))
{
    ShowError(GetMessage("SALE_MODULE_NOT_INSTALL"));
    return;
}
if (!$USER->IsAuthorized())
{
    $APPLICATION->AuthForm(GetMessage("SALE_ACCESS_DENIED"));
}

$arParams["PATH_TO_DETAIL"] = Trim($arParams["PATH_TO_DETAIL"]);
if (strlen($arParams["PATH_TO_DETAIL"]) <= 0)
    $arParams["PATH_TO_DETAIL"] = htmlspecialchars($APPLICATION->GetCurPage()."?"."ID=#ID#");

$arParams["PATH_TO_COPY"] = Trim($arParams["PATH_TO_COPY"]);
if (strlen($arParams["PATH_TO_COPY"]) <= 0)
    $arParams["PATH_TO_COPY"] = htmlspecialchars($APPLICATION->GetCurPage()."?"."ID=#ID#");

$arParams["PATH_TO_CANCEL"] = Trim($arParams["PATH_TO_CANCEL"]);
if (strlen($arParams["PATH_TO_CANCEL"]) <= 0)
    $arParams["PATH_TO_CANCEL"] = htmlspecialchars($APPLICATION->GetCurPage()."?"."ID=#ID#");

$arParams["PATH_TO_BASKET"] = Trim($arParams["PATH_TO_BASKET"]);
if (strlen($arParams["PATH_TO_BASKET"]) <= 0)
    $arParams["PATH_TO_BASKET"] = "basket.php";

$arParams["PATH_TO_COPY"] .= (strpos($arParams["PATH_TO_COPY"], "?") === false ? "?" : "&amp;");
$arParams["PATH_TO_CANCEL"] .= (strpos($arParams["PATH_TO_CANCEL"], "?") === false ? "?" : "&amp;");

if($arParams["SET_TITLE"] == 'Y')
    $APPLICATION->SetTitle(GetMessage("SPOL_DEFAULT_TITLE"));
if($arParams["SAVE_IN_SESSION"] != "N")
    $arParams["SAVE_IN_SESSION"] = "Y";

$arParams["NAV_TEMPLATE"] = (strlen($arParams["NAV_TEMPLATE"])>0 ? $arParams["NAV_TEMPLATE"] : "");

$arParams["ORDERS_PER_PAGE"] = 100/*(intval($arParams["ORDERS_PER_PAGE"]) <= 0 ? 20 : intval($arParams["ORDERS_PER_PAGE"]))*/;

//Copy order
$ID = IntVal($arParams["ID"]);

//Save statuses for Filter form
$dbStatus = CSaleStatus::GetList(Array("SORT"=>"ASC"), array("LID"=>LANGUAGE_ID));
while ($arStatus = $dbStatus->GetNext())
    $arResult["INFO"]["STATUS"][$arStatus["ID"]] = $arStatus;

$dbPaySystem = CSalePaySystem::GetList(Array("SORT"=>"ASC"));
while ($arPaySystem = $dbPaySystem->GetNext())
    $arResult["INFO"]["PAY_SYSTEM"][$arPaySystem["ID"]] = $arPaySystem;

$dbDelivery = CSaleDelivery::GetList(Array("SORT"=>"ASC"));
while ($arDelivery = $dbDelivery->GetNext())
    $arResult["INFO"]["DELIVERY"][$arDelivery["ID"]] = $arDelivery;

$arResult["INFO"]["DELIVERY_HANDLERS"] = array();
$dbDelivery = CSaleDeliveryHandler::GetList(array(), array(array("SITE_ID" => SITE_ID)));
while ($arDeliveryHandler = $dbDelivery->GetNext())
{
    $arResult["INFO"]["DELIVERY_HANDLERS"][$arDeliveryHandler["SID"]] = $arDeliveryHandler;
}

$arResult["CURRENT_PAGE"] = $APPLICATION->GetCurPage();

//Preparing filter
$arFilter = Array();
$arFilter["USER_ID"] = IntVal($USER->GetID());

if(strlen($_REQUEST["del_filter"]) > 0)
{
    unset($_REQUEST["filter_id"]);
    unset($_REQUEST["filter_date_from"]);
    unset($_REQUEST["filter_date_to"]);
    unset($_REQUEST["filter_status"]);
    unset($_REQUEST["filter_payed"]);
    unset($_REQUEST["filter_canceled"]);
    $_REQUEST["filter_history"] = "Y";
    if($arParams["SAVE_IN_SESSION"] == "Y")
    {
        unset($_SESSION["spo_filter_id"]);
        unset($_SESSION["spo_filter_date_from"]);
        unset($_SESSION["spo_filter_date_to"]);
        unset($_SESSION["spo_filter_status"]);
        unset($_SESSION["spo_filter_payed"]);
        unset($_SESSION["spo_filter_canceled"]);
        $_SESSION["spo_filter_history"] = "Y";
    }
}

if($arParams["SAVE_IN_SESSION"] == "Y" && strlen($_REQUEST["filter"]) <= 0)
{
    if (IntVal($_SESSION["spo_filter_id"])>0)
        $_REQUEST["filter_id"] = $_SESSION["spo_filter_id"];
    if (strlen($_SESSION["spo_filter_date_from"])>0)
        $_REQUEST["filter_date_from"] = $_SESSION["spo_filter_date_from"];
    if (strlen($_SESSION["spo_filter_date_to"])>0)
        $_REQUEST["filter_date_to"] = $_SESSION["spo_filter_date_to"];
    if (strlen($_SESSION["spo_filter_status"])>0)
        $_REQUEST["filter_status"] = $_SESSION["spo_filter_status"];
    if (strlen($_SESSION["spo_filter_payed"])>0)
        $_REQUEST["filter_payed"] = $_SESSION["spo_filter_payed"];
    if (strlen($_SESSION["spo_filter_canceled"])>0)
        $_REQUEST["filter_canceled"] = $_SESSION["spo_filter_canceled"];
    if ($_SESSION["spo_filter_history"]=="Y")
        $_REQUEST["filter_history"] == "Y";
}


if (IntVal($_REQUEST["filter_id"])>0)
    $arFilter["ID"] = IntVal($_REQUEST["filter_id"]);
if (strlen($_REQUEST["filter_date_from"])>0)
    $arFilter["DATE_FROM"] = Trim($_REQUEST["filter_date_from"]);
if (strlen($_REQUEST["filter_date_to"])>0)
{
    $arFilter["DATE_TO"] = Trim($_REQUEST["filter_date_to"]);

    if ($arDate = ParseDateTime(Trim($_REQUEST["filter_date_to"]), CSite::GetDateFormat("FULL", SITE_ID)))
    {
        if (StrLen(Trim($_REQUEST["filter_date_to"])) < 11)
        {
            $arDate["HH"] = 23;
            $arDate["MI"] = 59;
            $arDate["SS"] = 59;
        }

        $arFilter["DATE_TO"] = date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL", SITE_ID)), mktime($arDate["HH"], $arDate["MI"], $arDate["SS"], $arDate["MM"], $arDate["DD"], $arDate["YYYY"]));
    }
}

if (strlen($_REQUEST["filter_status"])>0)
    $arFilter["STATUS_ID"] = Trim($_REQUEST["filter_status"]);
if (strlen($_REQUEST["filter_payed"])>0)
    $arFilter["PAYED"] = Trim($_REQUEST["filter_payed"]);
if (strlen($_REQUEST["filter_canceled"])>0)
    $arFilter["CANCELED"] = Trim($_REQUEST["filter_canceled"]);
if ($_REQUEST["filter_history"]!="Y")
{
//    $arFilter["!STATUS_ID"] = "F";
    $arFilter["CANCELED"] = "N";
}

$arFilter["LID"] = SITE_ID;
if($arParams["SAVE_IN_SESSION"] == "Y" && strlen($_REQUEST["filter"]) > 0)
{
    $_SESSION["spo_filter_id"] = $_REQUEST["filter_id"];
    $_SESSION["spo_filter_date_from"] = $_REQUEST["filter_date_from"];
    $_SESSION["spo_filter_date_to"] = $_REQUEST["filter_date_to"];
    $_SESSION["spo_filter_status"] = $_REQUEST["filter_status"];
    $_SESSION["spo_filter_payed"] = $_REQUEST["filter_payed"];
    $_SESSION["spo_filter_history"] = $_REQUEST["filter_history"];
}


$by = (strlen($_REQUEST["by"])>0 ? $_REQUEST["by"]: "ID");
$order = (strlen($_REQUEST["order"])>0 ? $_REQUEST["order"]: "DESC");
$orderBy = ($by && $order)? array($by,$order) : false;
/////////////////
    $dbOrder = CSaleOrder::GetList(Array($by => $order), $arFilter);
    
    while($arOrder = $dbOrder->Fetch())
    {
        $arFilterBasket["ORDER_ID"][]=$arOrder['ID'];
    }
    
    $dbPosition = CSaleBasket::GetList(
            array(
                    "ORDER_ID" => "ASC"
                ),
            $arFilterBasket
        );

    $arResult["ORDERS"] = Array();
    $createTmpTable = new CitgTempTable(array("URL_TO_DETAIL"=>"text DEFAULT NULL",
                                                "ORDER_ID"=>"int(20) unsigned DEFAULT NULL",
                                                "PRICE"=>"double(10,2) unsigned DEFAULT '0.00'",
                                                "ARTICLE"=>"varchar(50) DEFAULT NULL",
                                                "ARTICLECHANGE"=>"varchar(50) DEFAULT NULL",
                                                "BRAND"=>"varchar(50) DEFAULT NULL",
                                                "QUANTITY"=>"SMALLINT(50) unsigned DEFAULT NULL",
                                                "DATE_INSERT"=>"varchar(20) DEFAULT NULL",
                                                "NAME"=>"varchar(100) DEFAULT NULL",
                                                "ITEMSTATUS"=>"varchar(10) DEFAULT NULL",
                                                "CURRENCY"=>"varchar(10) DEFAULT NULL"
                                                )
                                        );
        while($arBasket = $dbPosition->Fetch())
        {
            $arBasket["URL_TO_DETAIL"] = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_DETAIL"], Array("ID" => $arBasket["ORDER_ID"]));
            $arBasket["ORDER_ID"] = $arBasket["ORDER_ID"];
            $arBasket["PRICE"] = $arBasket["PRICE"];
            $arBasket["ARTICLE"] = GetBasketItemProperty( $arBasket["ID"], "ItemCode" );
            $arBasket["ARTICLECHANGE"] = GetBasketItemProperty( $arBasket["ID"], "ItemCodeChange" );
            $arBasket["BRAND"] = GetBasketItemProperty( $arBasket["ID"], "Brand" );
            $arBasket["QUANTITY"] = DoubleVal($arBasket["QUANTITY"]);
            $arBasket["DATE_INSERT"] = $arBasket["DATE_INSERT"];
            $arBasket["NAME"] = htmlspecialcharsEx($arBasket["NAME"]);
            $arBasket["ITEMSTATUS"] = GetBasketItemProperty( $arBasket["ID"], "ItemStatus" );  //  DG
            $arBasket["CURRENCY"] = $arBasket["CURRENCY"];
            $arOBasket[] = $arBasket;
            $arResult["ORDERS"] = $arOBasket;
            $fieldToTmpTable[] = array($arBasket["URL_TO_DETAIL"],
                                        $arBasket["ORDER_ID"],
                                        $arBasket["PRICE"],
                                        $arBasket["ARTICLE"],
                                        $arBasket["ARTICLECHANGE"],
                                        $arBasket["BRAND"],
                                        $arBasket["QUANTITY"],
                                        $arBasket["DATE_INSERT"],
                                        $arBasket["NAME"],
                                        $arBasket["ITEMSTATUS"],
                                        $arBasket["CURRENCY"]
                                    );
        }
        $createTmpTable->Insert($fieldToTmpTable);
    if(empty($_REQUEST["itg_code"]) && empty($_REQUEST["itg_brand"]))
    {
        $dbPosition = $createTmpTable->GetList($orderBy);
        $dbPositionCh = $createTmpTable->GetList($orderBy); 
    }
    else 
    {
        if(!empty($_REQUEST["itg_code"]))
        {
         $arFiltrTmpTable["ARTICLE"] = str_replace(array("-",".",",",":","'","\""," "),"",$_REQUEST["itg_code"]);
         $arFiltrTmpTableCh["ARTICLECHANGE"] = str_replace(array("-",".",",",":","'","\""," "),"",$_REQUEST["itg_code"]);   
        }
        if(!empty($_REQUEST["itg_brand"])) $arFiltrTmpTable["BRAND"] = $_REQUEST["itg_brand"];
        $dbPositionCh = $createTmpTable->Filtr($arFiltrTmpTableCh,$orderBy);  
        $dbPosition = $createTmpTable->Filtr($arFiltrTmpTable,$orderBy);
        
        $dbExclusive = $createTmpTable->FiltrExclusive($arFiltrTmpTable, "BRAND");
    }

    while ($serchingItem = mysql_fetch_assoc($dbExclusive))
    {
        foreach ($serchingItem as $key=>$val)
        {
            $arResultTmp[$key] = $val;
        }
        $arResult["RESULT_SEARCH"][] = $arResultTmp;
    }
//echo "<pre>".print_r($arResult["RESULT_SEARCH"])."</pre>";
    $dbPosition->NavStart($arParams["ORDERS_PER_PAGE"]);
    $arResult["NAV_STRING"] = $dbPosition->GetPageNavString(GetMessage("SPOL_PAGES"), $arParams["NAV_TEMPLATE"]);
    $arResult["CURRENT_PAGE"] = $APPLICATION->GetCurPage();
    unset($arOBasket);
    $arResult["ORDERS"] = Array();
    while($arBasket = $dbPosition->GetNext())
    {
        $arOBasket[] = $arBasket;
       # $arResult["ORDERS"] = $arOBasket;
    }
     $dbPositionCh->NavStart($arParams["ORDERS_PER_PAGE"]);
    $arResult["NAV_STRING"] = $dbPositionCh->GetPageNavString(GetMessage("SPOL_PAGES"), $arParams["NAV_TEMPLATE"]);
    $arResult["CURRENT_PAGE"] = $APPLICATION->GetCurPage();
    #unset($arOBasket);
   # $arResult["ORDERS"] = Array();
    while($arBasket = $dbPositionCh->GetNext())
    {
        $arOBasket[] = $arBasket;
        #$arResult["ORDERS"] = $arOBasket;
    }
     $arResult["ORDERS"] = $arOBasket;
$this->IncludeComponentTemplate();
?>