<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$module_id = "catalog";
//$CAT_RIGHT = $APPLICATION->GetGroupRight($module_id);
if ($USER->CanDoOperation('catalog_read')) :

include_once($GLOBALS["DOCUMENT_ROOT"]."/bitrix/modules/catalog/include.php");

if ($ex = $APPLICATION->GetException())
{
	require($DOCUMENT_ROOT."/bitrix/modules/main/include/prolog_admin_after.php");

	$strError = $ex->GetString();
	ShowError($strError);

	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
	die();
}

IncludeModuleLangFile(__FILE__);

if(CModule::IncludeModule("iblock")):

	$arIBlockType = array(
		"-" => GetMessage("CAT_1C_CREATE"),
	);
	$rsIBlockType = CIBlockType::GetList(array("sort"=>"asc"), array("ACTIVE"=>"Y"));
	while ($arr=$rsIBlockType->Fetch())
	{
		if($ar=CIBlockType::GetByIDLang($arr["ID"], LANGUAGE_ID))
		{
			$arIBlockType[$arr["ID"]] = "[".$arr["ID"]."] ".$ar["NAME"];
		}
	}

	$rsSite = CSite::GetList($by="sort", $order="asc", $arFilter=array("ACTIVE" => "Y"));
	$arSites = array(
		"-" => GetMessage("CAT_1C_CURRENT"),
	);
	while ($arSite = $rsSite->GetNext())
	{
		$arSites[$arSite["LID"]] = $arSite["NAME"];
	}

	$arUGroupsEx = Array();
	$dbUGroups = CGroup::GetList($by = "c_sort", $order = "asc");
	while($arUGroups = $dbUGroups -> Fetch())
	{
		$arUGroupsEx[$arUGroups["ID"]] = $arUGroups["NAME"];
	}

	$arAction = array(
		"N" => GetMessage("CAT_1C_NONE"),
		"A" => GetMessage("CAT_1C_DEACTIVATE"),
		"D" => GetMessage("CAT_1C_DELETE"),
	);

	$arAllOptions = array(
		array("1C_IBLOCK_TYPE", GetMessage("CAT_1C_IBLOCK_TYPE"), "-", Array("list", $arIBlockType)),
		array("1C_SITE_LIST", GetMessage("CAT_1C_SITE_LIST"), "-", Array("list", $arSites)),
		array("1C_INTERVAL", GetMessage("CAT_1C_INTERVAL"), "30", Array("text", 20)),
		array("1C_GROUP_PERMISSIONS", GetMessage("CAT_1C_GROUP_PERMISSIONS"), "-", Array("mlist", 5, $arUGroupsEx)),
		array("1C_ELEMENT_ACTION", GetMessage("CAT_1C_ELEMENT_ACTION"), "D", Array("list", $arAction)),
		array("1C_SECTION_ACTION", GetMessage("CAT_1C_SECTION_ACTION"), "D", Array("list", $arAction)),
		array("1C_FILE_SIZE_LIMIT", GetMessage("CAT_1C_FILE_SIZE_LIMIT"), 200*1024, Array("text", 20)),
		array("1C_USE_CRC", GetMessage("CAT_1C_USE_CRC"), "Y", Array("checkbox")),
		array("1C_USE_ZIP", GetMessage("CAT_1C_USE_ZIP"), "Y", Array("checkbox")),
		array("1C_GENERATE_PREVIEW", GetMessage("CAT_1C_GENERATE_PREVIEW"), "Y", Array("checkbox")),
		array("1C_PREVIEW_WIDTH", GetMessage("CAT_1C_PREVIEW_WIDTH"), 100, Array("text", 20)),
		array("1C_PREVIEW_HEIGHT", GetMessage("CAT_1C_PREVIEW_HEIGHT"), 100, Array("text", 20)),
		array("1C_DETAIL_RESIZE", GetMessage("CAT_1C_DETAIL_RESIZE"), "Y", Array("checkbox")),
		array("1C_DETAIL_WIDTH", GetMessage("CAT_1C_DETAIL_WIDTH"), 300, Array("text", 20)),
		array("1C_DETAIL_HEIGHT", GetMessage("CAT_1C_DETAIL_HEIGHT"), 300, Array("text", 20)),
		array("1C_USE_OFFERS", GetMessage("CAT_1C_USE_OFFERS"), "N", Array("checkbox")),
		array("1C_USE_IBLOCK_TYPE_ID", GetMessage("CAT_1C_USE_IBLOCK_TYPE_ID"), "N", Array("checkbox")),
	);

	if($REQUEST_METHOD=="POST" && strlen($Update)>0 && /*$CAT_RIGHT>="W"*/ $USER->CanDoOperation('edit_php'))
	{
		for ($i=0; $i<count($arAllOptions); $i++)
		{
			$name = $arAllOptions[$i][0];
			$val = $_REQUEST[$name];
			if($arAllOptions[$i][3][0]=="checkbox" && $val!="Y")
				$val = "N";
			if($arAllOptions[$i][3][0]=="mlist")
				$val = implode(",", $val);
			COption::SetOptionString("catalog", $name, $val, $arAllOptions[$i][1]);
		}
		return;
	}

	foreach($arAllOptions as $Option):
		$val = COption::GetOptionString("catalog", $Option[0], $Option[2]);
		$type = $Option[3];
		?>
		<tr>
			<td valign="top" width="60%"><?	if($type[0]=="checkbox")
							echo "<label for=\"".htmlspecialchars($Option[0])."\">".$Option[1]."</label>";
						else
							echo $Option[1];?>:</td>
			<td valign="middle" width="40%">
					<?if($type[0]=="checkbox"):?>
						<input type="checkbox" name="<?echo htmlspecialchars($Option[0])?>" id="<?echo htmlspecialchars($Option[0])?>" value="Y"<?if($val=="Y")echo" checked";?>>
					<?elseif($type[0]=="text"):?>
						<input type="text" size="<?echo $type[1]?>" maxlength="255" value="<?echo htmlspecialchars($val)?>" name="<?echo htmlspecialchars($Option[0])?>">
					<?elseif($type[0]=="textarea"):?>
						<textarea rows="<?echo $type[1]?>" cols="<?echo $type[2]?>" name="<?echo htmlspecialchars($Option[0])?>"><?echo htmlspecialchars($val)?></textarea>
					<?elseif($type[0]=="list"):?>
						<select name="<?echo htmlspecialchars($Option[0])?>">
						<?foreach($type[1] as $key=>$value):?>
							<option value="<?echo htmlspecialchars($key)?>" <?if($val==$key) echo "selected"?>><?echo htmlspecialchars($value)?></option>
						<?endforeach?>
						</select>
					<?elseif($type[0]=="mlist"):
						$val = explode(",", $val)?>
						<select multiple name="<?echo htmlspecialchars($Option[0])?>[]" size="<?echo $type[1]?>">
						<?foreach($type[2] as $key=>$value):?>
							<option value="<?echo htmlspecialchars($key)?>" <?if(in_array($key, $val)) echo "selected"?>><?echo htmlspecialchars($value)?></option>
						<?endforeach?>
						</select>
					<?endif?>
			</td>
		</tr>
	<?endforeach;

else:
	CAdminMessage::ShowMessage(GetMessage("CAT_NO_IBLOCK_MOD"));
endif;

endif;
?>