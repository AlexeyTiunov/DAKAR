<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

$APPLICATION->AddHeadScript('/bitrix/js/main/utils.js');

$aGlobalOptions = CUserOptions::GetOption("main.interface", "global", array());
if($aGlobalOptions["theme"] <> '')
	$APPLICATION->SetAdditionalCSS($templateFolder.'/themes/'.$aGlobalOptions["theme"].'/style.css');
?>