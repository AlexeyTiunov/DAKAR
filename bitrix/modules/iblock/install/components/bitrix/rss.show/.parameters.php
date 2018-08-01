<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;

$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS" => array(
		"SITE" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("T_IBLOCK_DESC_RSS_SITE"),
			"TYPE" => "STRING",
			"DEFAULT" => 'www.1c-bitrix.ru',
		),
		"PORT" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("T_IBLOCK_DESC_RSS_PORT"),
			"TYPE" => "STRING",
			"DEFAULT" => '80',
		),
		"PATH" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("T_IBLOCK_DESC_RSS_PATH"),
			"TYPE" => "STRING",
			"DEFAULT" => '/bitrix/rss.php',
		),
		"QUERY_STR" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("T_IBLOCK_DESC_RSS_QUERY_STR"),
			"TYPE" => "STRING",
			"DEFAULT" => 'ID=news_sm&LANG=ru&TYPE=news&LIMIT=5',
		),
		"OUT_CHANNEL" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("T_IBLOCK_DESC_RSS_OUT_CHANNEL"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"NUM_NEWS" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("T_IBLOCK_DESC_RSS_NUM_NEWS"),
			"TYPE" => "STRING",
			"DEFAULT" => '10',
		),
		"CACHE_TIME"  =>  Array("DEFAULT"=>3600),
	),
);
?>
