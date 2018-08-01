<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;

//prepare params
$arParams['IBLOCK_ID'] = intval($arParams['IBLOCK_ID']);
if($arParams['IBLOCK_ID']<=0)
	return;

$arParams["DETAIL_URL"] = trim($arParams["DETAIL_URL"]);

if(isset($arParams["IBLOCK_TYPE"]) && $arParams["IBLOCK_TYPE"]!='')
	$arFilter['IBLOCK_TYPE'] = $arParams["IBLOCK_TYPE"];

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 3600;

//SELECT
$arSelect = Array(
	"ID",
	"NAME",
	"IBLOCK_SECTION_ID",
	"PREVIEW_TEXT_TYPE",
	"PREVIEW_TEXT",
	"DETAIL_TEXT_TYPE",
	"DETAIL_TEXT",
);
//WHERE
$arFilter = Array(
	'IBLOCK_ID' => $arParams["IBLOCK_ID"],
	'ACTIVE' => 'Y',
	'IBLOCK_ACTIVE' => 'Y',
);
//ORDER BY
$arOrder = Array(
	'SORT' => 'ASC',
	'ID' => 'DESC',
);

$arAddCacheParams = array(
	"MODE" => $_REQUEST['bitrix_show_mode']?$_REQUEST['bitrix_show_mode']:'view',
	"SESS_MODE" => $_SESSION['SESS_PUBLIC_SHOW_MODE']?$_SESSION['SESS_PUBLIC_SHOW_MODE']:'view',
);

//**work body**//
if($this->StartResultCache(false, array(($arParams["CACHE_GROUPS"]==="N"? false: $USER->GetGroups()), $arFilter, $arAddCacheParams)))
{
	$arResult['ITEMS'] = Array();
	$arItems = CIBlockElement::GetList($arOrder, $arFilter, false, false, $arSelect);
	while($arResItems = $arItems->Fetch())
		$arResult['ITEMS'][] = $arResItems;

	if(count($arResult['ITEMS'])<=0)
	{
		$this->AbortResultCache();
		@define("ERROR_404", "Y");
		return;
	}
	//include template
	$this->IncludeComponentTemplate();
}
?>