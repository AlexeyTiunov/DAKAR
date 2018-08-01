<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arResult['VALUE'] = array_filter($arResult["VALUE"]);

$bWasSelect = false;

if($arParams['arUserField']["SETTINGS"]["DISPLAY"]!="CHECKBOX"):
	?><select name="<?=$arParams["arUserField"]["FIELD_NAME"]?>" size="<?=$arParams["arUserField"]["SETTINGS"]["LIST_HEIGHT"]
	?>" <?
	if ($arParams["arUserField"]["MULTIPLE"]=="Y"):
	?> multiple="multiple"<?
	endif;
	?>><?
endif;
foreach ($arParams["arUserField"]["USER_TYPE"]["FIELDS"] as $key => $val)
{

	$bSelected = in_array($key, $arResult["VALUE"]) && (
		(!$bWasSelect) ||
		($arParams["arUserField"]["MULTIPLE"] == "Y")
	);
	$bWasSelect = $bWasSelect || $bSelected;

	if($arParams['arUserField']["SETTINGS"]["DISPLAY"]=="CHECKBOX")
	{
		?><label><input type="radio" value="<?echo $key?>" name="<?echo $arParams["arUserField"]["FIELD_NAME"]?>"<?echo ($bSelected? " checked" : "")?><?
		if ($arParams["arUserField"]["MULTIPLE"]=="Y"):
		?> multiple="multiple"<?
		endif;
		?>><?=$val?></label><br /><?
	}
	else
	{
		?><option value="<?echo $key?>"<?echo ($bSelected? " selected" : "")?> title="<?echo trim($val, " .")?>"><?echo $val?></option><?
	}
}
if($arParams['arUserField']["SETTINGS"]["DISPLAY"]!="CHECKBOX"):
?></select><?
endif;?>