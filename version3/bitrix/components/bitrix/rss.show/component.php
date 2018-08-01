<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
{
	ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
	return;
}

/*************************************************************************
	Processing of received parameters
*************************************************************************/
if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 3600;

$arParams["SITE"] = trim($arParams["SITE"]);
$arParams["PORT"] = intval($arParams["PORT"]);
$arParams["PATH"] = trim($arParams["PATH"]);
$arParams["QUERY_STR"] = trim($arParams["QUERY_STR"]);
$arParams["OUT_CHANNEL"] = $arParams["OUT_CHANNEL"]=="Y";
$arParams["NUM_NEWS"] = intval($arParams["NUM_NEWS"]);

/*************************************************************************
	Start caching
*************************************************************************/
if($this->StartResultCache())
{
	$arResult = CIBlockRSS::GetNewsEx($arParams["SITE"], $arParams["PORT"], $arParams["PATH"], $arParams["QUERY_STR"], $arParams["OUT_CHANNEL"]);
	$arResult = CIBlockRSS::FormatArray($arResult, $arParams["OUT_CHANNEL"]);
	if($arParams["NUM_NEWS"]>0)
		while(count($arResult["item"])>$arParams["NUM_NEWS"])
			array_pop($arResult["item"]);
	
	$this->IncludeComponentTemplate();
}
?>
