<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();
?>

<div class="bx-interface-form">

<form name="form_<?=$arParams["FORM_ID"]?>" id="form_<?=$arParams["FORM_ID"]?>" action="<?=POST_FORM_ACTION_URI?>" method="POST" enctype="multipart/form-data">

<?=bitrix_sessid_post();?>
<input type="hidden" id="<?=$arParams["FORM_ID"]?>_active_tab" name="<?=$arParams["FORM_ID"]?>_active_tab" value="<?=htmlspecialchars($arResult["SELECTED_TAB"])?>">

			<table cellspacing="0" class="bx-edit-tabs" width="100%">
				<tr>
					<td class="bx-tab-indent"><div class="empty"></div></td>
<?
$nTabs = count($arParams["TABS"]);
foreach($arParams["TABS"] as $tab):
	$bSelected = ($tab["id"] == $arResult["SELECTED_TAB"]);
?>
					<td title="<?=$tab["title"]?>" id="tab_cont_<?=$tab["id"]?>" class="bx-tab-container<?=($bSelected? "-selected":"")?>" onclick="bxForm_<?=$arParams["FORM_ID"]?>.SelectTab('<?=$tab["id"]?>');" onmouseover="if(bxForm_<?=$arParams["FORM_ID"]?>){bxForm_<?=$arParams["FORM_ID"]?>.HoverTab('<?=$tab["id"]?>', true);}" onmouseout="if(bxForm_<?=$arParams["FORM_ID"]?>){bxForm_<?=$arParams["FORM_ID"]?>.HoverTab('<?=$tab["id"]?>', false);}">
						<table cellspacing="0">
							<tr>
								<td class="bx-tab-left<?=($bSelected? "-selected":"")?>" id="tab_left_<?=$tab["id"]?>"><div class="empty"></div></td>
								<td class="bx-tab<?=($bSelected? "-selected":"")?>" id="tab_<?=$tab["id"]?>"><?=$tab["name"]?></td>
								<td class="bx-tab-right<?=($bSelected? "-selected":"")?>" id="tab_right_<?=$tab["id"]?>"><div class="empty"></div></td>
							</tr>
						</table>
					</td>
<?
endforeach;
?>
<?
if(count($arParams["TABS"]) > 1 && $arParams["CAN_EXPAND_TABS"] == true):
?>
					<td width="100%" align="right"><a href="javascript:void(0)" onclick="this.blur();bxForm_<?=$arParams["FORM_ID"]?>.ToggleTabs();" hidefocus="true" title="<?echo GetMessage("interface_form_show_all")?>" id="bxForm_<?=$arParams["FORM_ID"]?>_expand_link" class="bx-context-button bx-down"></a></td>
<?else:?>
					<td width="100%"><div class="empty"></div></td>
<?endif?>
				</tr>
			</table>
			<table cellspacing="0" class="bx-edit-tab">
				<tr>
					<td>
<?
$bWasRequired = false;
foreach($arParams["TABS"] as $tab):
?>
<div id="inner_tab_<?=$tab["id"]?>" class="bx-edit-tab-inner"<?if($tab["id"] <> $arResult["SELECTED_TAB"]) echo ' style="display:none;"'?>>
<div style="height: 100%;">
<?if($tab["title"] <> ''):?>
	<div class="bx-edit-tab-title">
	<table cellpadding="0" cellspacing="0" border="0" class="bx-edit-tab-title">
		<tr>
	<?
		if($tab["icon"] <> ""):
	?>
			<td class="bx-icon"><div class="<?=$tab["icon"]?>"></div></td>
	<?
		endif
	?>
			<td class="bx-form-title"><?=$tab["title"]?></td>
		</tr>
	</table>
	</div>
<?endif;?>

<div class="bx-edit-table">
<table cellpadding="0" cellspacing="0" border="0" class="bx-edit-table" id="<?=$tab["id"]?>_edit_table">
<?
$i = 0;
$cnt = count($tab["fields"]);
$prevType = '';
foreach($tab["fields"] as $field):
	$i++;
	if(!is_array($field))
		continue;

	$className = '';
	if($i == 1)
		$className .= ' bx-top';
	if($i == $cnt)
		$className .= ' bx-bottom';
	if($prevType == 'section')
		$className .= ' bx-after-heading';
?>
	<tr<?if($className <> ''):?> class="<?=$className?>"<?endif?>>
<?
if($field["type"] == 'section'):
?>
		<td colspan="2" class="bx-heading"><?=$field["name"]?></td>
<?
else:
	$val = (isset($field["value"])? $field["value"] : $arParams["~DATA"][$field["id"]]);

	//default attributes
	if(!is_array($field["params"]))
		$field["params"] = array();
	if($field["type"] == '' || $field["type"] == 'text')
	{
		if($field["params"]["size"] == '')
			$field["params"]["size"] = "30";
	}
	elseif($field["type"] == 'textarea')
	{
		if($field["params"]["cols"] == '')
			$field["params"]["cols"] = "40";
		if($field["params"]["rows"] == '')
			$field["params"]["rows"] = "3";
	}
	elseif($field["type"] == 'date')
	{
		if($field["params"]["size"] == '')
			$field["params"]["size"] = "10";
	}
	
	$params = '';
	if(is_array($field["params"]) && $field["type"] <> 'file')
	{
		foreach($field["params"] as $p=>$v)
			$params .= ' '.$p.'="'.$v.'"';
	}

	if($field["colspan"] <> true):
		if($field["required"])
			$bWasRequired = true;
?>
		<td class="bx-field-name<?if($field["type"] <> 'label') echo' bx-padding'?>"><?=($field["required"]? '<span class="required">*</span>':'')?><?=$field["name"]?>:</td>
<?
	endif
?>
		<td class="bx-field-value"<?=($field["colspan"]? ' colspan="2"':'')?>>
<?
	switch($field["type"]):
		case 'label':
		case 'custom':
			echo $val;
			break;
		case 'checkbox':
?>
<input type="hidden" name="<?=$field["id"]?>" value="N">
<input type="checkbox" name="<?=$field["id"]?>" value="Y"<?=($val == "Y"? ' checked':'')?><?=$params?>>
<?
			break;
		case 'textarea':
?>
<textarea name="<?=$field["id"]?>"<?=$params?>><?=$val?></textarea>
<?
			break;
		case 'list':
?>
<select name="<?=$field["id"]?>"<?=$params?>>
<?
			if(is_array($field["items"])):
				if(!is_array($val))
					$val = array($val);
				foreach($field["items"] as $k=>$v):
?>
	<option value="<?=htmlspecialchars($k)?>"<?=(in_array($k, $val)? ' selected':'')?>><?=htmlspecialchars($v)?></option>
<?
				endforeach;
?>
</select>
<?
			endif;
			break;
		case 'file':
			$arDefParams = array("iMaxW"=>150, "iMaxH"=>150, "sParams"=>"border=0", "strImageUrl"=>"", "bPopup"=>true, "sPopupTitle"=>false, "size"=>20);
			foreach($arDefParams as $k=>$v)
				if(!array_key_exists($k, $field["params"]))
					$field["params"][$k] = $v;
	
			echo CFile::InputFile($field["id"], $field["params"]["size"], $val);
			if($val <> '')
				echo '<br>'.CFile::ShowImage($val, $field["params"]["iMaxW"], $field["params"]["iMaxH"], $field["params"]["sParams"], $field["params"]["strImageUrl"], $field["params"]["bPopup"], $field["params"]["sPopupTitle"]);

			break;
		case 'date':
?>
<?$APPLICATION->IncludeComponent(
	"bitrix:main.calendar",
	"",
	array(
		"SHOW_INPUT"=>"Y",
		"INPUT_NAME"=>$field["id"],
		"INPUT_VALUE"=>$val,
		"INPUT_ADDITIONAL_ATTR"=>$params,
	),
	$component,
	array("HIDE_ICONS"=>true)
);?>
<?
			break;
		default:
?>
<input type="text" name="<?=$field["id"]?>" value="<?=$val?>"<?=$params?>>
<?
			break;
	endswitch;
?>
		</td>
<?endif?>
	</tr>
<?
	$prevType = $field["type"];
endforeach;
?>
</table>
</div>
</div>
</div>
<?
endforeach;
?>
					</td>
				</tr>
			</table>
<?
if(isset($arParams["BUTTONS"])):
?>
			<div class="bx-buttons">
<?if($arParams["~BUTTONS"]["standard_buttons"] !== false):?>
	<?if($arParams["BUTTONS"]["back_url"] <> ''):?>
	<input type="submit" name="save" value="<?echo GetMessage("interface_form_save")?>" title="<?echo GetMessage("interface_form_save_title")?>" />
	<?endif?>
	<input type="submit" name="apply" value="<?echo GetMessage("interface_form_apply")?>" title="<?echo GetMessage("interface_form_apply_title")?>" />
	<?if($arParams["BUTTONS"]["back_url"] <> ''):?>
	<input type="button" value="<?echo GetMessage("interface_form_cancel")?>" name="cancel" onclick="window.location='<?=htmlspecialchars(CUtil::addslashes($arParams["~BUTTONS"]["back_url"]))?>'" title="<?echo GetMessage("interface_form_cancel_title")?>" />
	<?endif?>
<?endif?>
<?=$arParams["~BUTTONS"]["custom_html"]?>
			</div>
<?endif?>
</form>

<?
$variables = array(
	"mess"=>array(
		"collapseTabs"=>GetMessage("interface_form_close_all"),
		"expandTabs"=>GetMessage("interface_form_show_all"),
	),
);
?>
<script type="text/javascript">
var bxForm_<?=$arParams["FORM_ID"]?> = new BxInterfaceForm('<?=$arParams["FORM_ID"]?>', <?=CUtil::PhpToJsObject($arResult["TAB_LIST"])?>);
bxForm_<?=$arParams["FORM_ID"]?>.vars = <?=CUtil::PhpToJsObject($variables)?>;
</script>

</div>

<?if($bWasRequired):?>
<div class="bx-form-notes"><span class="required">*</span><?echo GetMessage("interface_form_required")?></div>
<?endif?>
