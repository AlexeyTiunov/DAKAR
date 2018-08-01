<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

if(!is_array($arParams["TABS"]))
	$arParams["TABS"] = array();

if($arParams["CAN_EXPAND_TABS"] <> 'N' && $arParams["CAN_EXPAND_TABS"] !== false)
	$arParams["CAN_EXPAND_TABS"] = true;

$arResult["TAB_LIST"] = array();
foreach($arParams["TABS"] as $tab)
	$arResult["TAB_LIST"][] = $tab["id"];

$hidden = $arParams["FORM_ID"]."_active_tab";
if(isset($_REQUEST[$hidden]) && in_array($_REQUEST[$hidden], $arResult["TAB_LIST"]))
	$arResult["SELECTED_TAB"] = $_REQUEST[$hidden];
else
	$arResult["SELECTED_TAB"] = ($arParams["TABS"][0]["id"]);

//*********************
// Self-explaining
//*********************

$this->IncludeComponentTemplate();
?>