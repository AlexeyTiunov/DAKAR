<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="fields integer" id="main_<?=$arParams["arUserField"]["FIELD_NAME"]?>"><?
$index = 0;
$fIndex = time();
foreach ($arResult["VALUE"] as $res):
	$name = $arParams["arUserField"]["FIELD_NAME"];
	if ($arParams["arUserField"]["MULTIPLE"] == "Y")
		$name = $arParams["arUserField"]["~FIELD_NAME"]."[".$index."]";
		
?><div class="fields datetime">
<input type="text" name="<?=$name?>" value="<?=$res?>"<?
	if (intVal($arParams["arUserField"]["SETTINGS"]["SIZE"]) > 0):
		?> size="<?=$arParams["arUserField"]["SETTINGS"]["SIZE"]?>"<?
	endif;
	if ($arParams["arUserField"]["EDIT_IN_LIST"]!="Y"):
		?> readonly="readonly"<?
	endif;
?> class="fields datetime"><?
	$GLOBALS['APPLICATION']->IncludeComponent(
		"bitrix:main.calendar",
		"",
		array(
			"SHOW_INPUT" => "N",
			"FORM_NAME" => $arParams["form_name"],
			"INPUT_NAME" => $name),
		$component,
		array("HIDE_ICONS" => "Y"));
?></div><?
$index++;
endforeach;
?></div><?


if ($arParams["arUserField"]["MULTIPLE"] == "Y" && $arParams["SHOW_BUTTON"] != "N"):
?><input type="button" value="<?=GetMessage("USER_TYPE_PROP_ADD")?>"<?
	if ($arParams["arUserField"]["EDIT_IN_LIST"]!="Y"):
		?> readonly="readonly"<?
	else:
		?> onClick="addStr<?=$fIndex?>();"<?
	endif;
?>><?
if ($arParams["arUserField"]["EDIT_IN_LIST"] =="Y"):?>

<div id="hidden_<?=$fIndex?>" style="display:none;">
	<div class="fields datetime">
		<input type="text" name="#FIELD_NAME#" value="<?=$res?>"<?
	if (intVal($arParams["arUserField"]["SETTINGS"]["SIZE"]) > 0):
		?> size="<?=$arParams["arUserField"]["SETTINGS"]["SIZE"]?>"<?
	endif;
?> class="fields datetime"><?
	$GLOBALS['APPLICATION']->IncludeComponent(
		"bitrix:main.calendar",
		"",
		array(
			"SHOW_INPUT" => "N",
			"FORM_NAME" => $arParams["form_name"],
			"INPUT_NAME" => $name),
		$component,
		array("HIDE_ICONS" => "Y"));
?></div>
</div>
<script language="JavaScript">
var index<?=$fIndex?> = '<?=$index?>';
var search = new Array(/\#FIELD_NAME\#/gi);
var replaceText = '<?=$arParams["arUserField"]["~FIELD_NAME"]?>';
var patternText = '';
if (document.getElementById('hidden_<?=$fIndex?>'))
	patternText = document.getElementById('hidden_<?=$fIndex?>').innerHTML;
	
function addStr<?=$fIndex?>()
{
	var element = document.getElementById('main_<?=$arParams["arUserField"]["FIELD_NAME"]?>');
	var text = patternText;
	if (element && text)
	{
		text = text.replace(/[#]FIELD_NAME[#]/g, replaceText+'['+index<?=$fIndex?>+']');
		text = text.replace(/[\%]23FIELD_NAME[\%]23/g, escape(replaceText+'['+index<?=$fIndex?>+']'));
		element.innerHTML += text
		index<?=$fIndex?>++;
	}
	return;
}
</script>
<?endif;
endif;?>