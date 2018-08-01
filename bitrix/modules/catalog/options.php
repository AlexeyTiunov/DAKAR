<?
$module_id = "catalog";
//$CAT_RIGHT = $APPLICATION->GetGroupRight($module_id);
//if ($CAT_RIGHT >= "R") :

if ($USER->CanDoOperation('catalog_read') || $USER->CanDoOperation('catalog_settings')) :

$bReadOnly = !$USER->CanDoOperation('catalog_settings');

global $MESS;
include(GetLangFileName($GLOBALS["DOCUMENT_ROOT"]."/bitrix/modules/main/lang/", "/options.php"));
include(GetLangFileName($GLOBALS["DOCUMENT_ROOT"]."/bitrix/modules/catalog/lang/", "/options.php"));

include_once($GLOBALS["DOCUMENT_ROOT"]."/bitrix/modules/catalog/include.php");

if ($ex = $APPLICATION->GetException())
{
	require($DOCUMENT_ROOT."/bitrix/modules/main/include/prolog_admin_after.php");
	
	$strError = $ex->GetString();
	ShowError($strError);
	
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
	die();
}

if ($REQUEST_METHOD=="GET" && strlen($RestoreDefaults)>0 && !$bReadOnly /*$CAT_RIGHT=="W"*/ && check_bitrix_sessid())
{
	if (!$USER->IsAdmin())
		$strValTmp = COption::GetOptionString("catalog", "avail_content_groups");

	COption::RemoveOption("catalog");
	$z = CGroup::GetList($v1="id",$v2="asc", array("ACTIVE" => "Y", "ADMIN" => "N"));
	while($zr = $z->Fetch())
		$APPLICATION->DelGroupRight($module_id, array($zr["ID"]));

	if (!$USER->IsAdmin())
		COption::SetOptionString("catalog", "avail_content_groups", $strValTmp);
}

$arAllOptions =
	Array(
		Array("export_default_path", GetMessage("CAT_EXPORT_DEFAULT_PATH"), "/bitrix/catalog_export/", Array("text", 30)),
		Array("default_catalog_1c", GetMessage("CAT_DEF_IBLOCK"), "", Array("text", 30)),
		Array("deactivate_1c_no_price", GetMessage("CAT_DEACT_NOPRICE"), "N", Array("checkbox")),
		Array("yandex_xml_period", GetMessage("CAT_YANDEX_XML_PERIOD"), "24", Array("text", 5)),
		);

$strWarning = "";
$strOK = "";
if ($REQUEST_METHOD=="POST" && strlen($Update)>0 && !$bReadOnly /*$CAT_RIGHT=="W"*/ && check_bitrix_sessid())
{
	for ($i=0; $i<count($arAllOptions); $i++)
	{
		$name = $arAllOptions[$i][0];
		$val = $$name;
		if ($arAllOptions[$i][3][0]=="checkbox" && $val!="Y")
			$val = "N";
		if ($name == 'export_default_path' && substr($val, -1) != '/') $val .= '/';
		COption::SetOptionString("catalog", $name, $val, $arAllOptions[$i][1]);
	}

	if ($default_outfile_action!="D" && $default_outfile_action!="H" && $default_outfile_action!="F")
	{
		$default_outfile_action = "D";
	}
	COption::SetOptionString("catalog", "default_outfile_action", $default_outfile_action, "");

	$strAllowedProductFields = "";
	for ($i = 0; $i < count($allowed_product_fields); $i++)
	{
		$allowed_product_fields[$i] = Trim($allowed_product_fields[$i]);
		if (strlen($allowed_product_fields[$i])>0)
		{
			if (strlen($strAllowedProductFields)>0) $strAllowedProductFields .= ",";
			$strAllowedProductFields .= $allowed_product_fields[$i];
		}
	}
	COption::SetOptionString("catalog", "allowed_product_fields", $strAllowedProductFields);

	$strAllowedPriceFields = "";
	for ($i = 0; $i < count($allowed_price_fields); $i++)
	{
		$allowed_price_fields[$i] = Trim($allowed_price_fields[$i]);
		if (strlen($allowed_price_fields[$i]) > 0)
		{
			if (strlen($strAllowedPriceFields) > 0)
				$strAllowedPriceFields .= ",";
			$strAllowedPriceFields .= $allowed_price_fields[$i];
		}
	}
	COption::SetOptionString("catalog", "allowed_price_fields", $strAllowedPriceFields);

	COption::SetOptionString("catalog", "num_catalog_levels", IntVal($num_catalog_levels));

	$strAllowedGroupFields = "";
	for ($i = 0; $i < count($allowed_group_fields); $i++)
	{
		$allowed_group_fields[$i] = Trim($allowed_group_fields[$i]);
		if (strlen($allowed_group_fields[$i])>0)
		{
			if (strlen($strAllowedGroupFields)>0) $strAllowedGroupFields .= ",";
			$strAllowedGroupFields .= $allowed_group_fields[$i];
		}
	}
	COption::SetOptionString("catalog", "allowed_group_fields", $strAllowedGroupFields);

	$strAllowedCurrencies = "";
	for ($i = 0; $i < count($allowed_currencies); $i++)
	{
		$allowed_currencies[$i] = Trim($allowed_currencies[$i]);
		if (strlen($allowed_currencies[$i])>0)
		{
			if (strlen($strAllowedCurrencies)>0) $strAllowedCurrencies .= ",";
			$strAllowedCurrencies .= $allowed_currencies[$i];
		}
	}
	COption::SetOptionString("catalog", "allowed_currencies", $strAllowedCurrencies);

	if ($USER->IsAdmin())
	{
		$arOldAvailContentGroups = array();
		$oldAvailContentGroups = COption::GetOptionString("catalog", "avail_content_groups");
		if (strlen($oldAvailContentGroups) > 0)
			$arOldAvailContentGroups = explode(",", $oldAvailContentGroups);

		$availContentGroups = "";
		if (isset($AVAIL_CONTENT_GROUPS) && is_array($AVAIL_CONTENT_GROUPS))
		{
			for ($i = 0; $i < count($AVAIL_CONTENT_GROUPS); $i++)
			{
				$AVAIL_CONTENT_GROUPS[$i] = IntVal($AVAIL_CONTENT_GROUPS[$i]);
				if ($AVAIL_CONTENT_GROUPS[$i] > 0)
				{
					if (strlen($availContentGroups) > 0)
						$availContentGroups .= ",";

					$availContentGroups .= $AVAIL_CONTENT_GROUPS[$i];

					if (in_array($AVAIL_CONTENT_GROUPS[$i], $arOldAvailContentGroups))
					{
						$ind = array_search($AVAIL_CONTENT_GROUPS[$i], $arOldAvailContentGroups);
						unset($arOldAvailContentGroups[$ind]);
					}
				}
			}
		}

		foreach ($arOldAvailContentGroups as $key => $value)
			CCatalogProductGroups::DeleteByGroup($value);

		COption::SetOptionString("catalog", "avail_content_groups", $availContentGroups);
	}

	$db_res = CIBlock::GetList(Array("iblock_type"=>"asc", "name"=>"asc"));
	$bNeedAgent = False;
	while ($res = $db_res->Fetch())
	{
		$is_cat = ((${"IS_CATALOG_".$res["ID"]}!="Y") ? "N" : "Y" );
		$is_cont = ((${"IS_CONTENT_".$res["ID"]}!="Y") ? "N" : "Y" );
		$yan_exp = ((${"YANDEX_EXPORT_".$res["ID"]}!="Y") ? "N" : "Y" );
		$cat_vat = intval(${"VAT_ID_".$res["ID"]});
		
		$ar_res1 = CCatalog::GetByID($res["ID"]);

		if (($is_cat=="Y" || $is_cont=="Y") && $ar_res1)
		{
			CCatalog::Update($res["ID"], array("YANDEX_EXPORT" => $yan_exp, "SUBSCRIPTION" => $is_cont, "VAT_ID" => $cat_vat));
			if ($yan_exp=="Y") $bNeedAgent = True;
		}
		elseif ($is_cat!="Y" && $is_cont!="Y" && $ar_res1)
		{
			if (CCatalog::Delete($res["ID"])===False)
			{
				$strWarning .= GetMessage("CAT_DEL_CATALOG1")." \"[".$ar_res1["IBLOCK_TYPE_ID"]."] ".$ar_res1["NAME"]." (".$ar_res1["LID"].")\" ".GetMessage("CAT_DEL_CATALOG2").".<br>";
			}
		}
		elseif ($is_cat=="Y" || $is_cont=="Y")
		{
			CCatalog::Add(array("IBLOCK_ID" => $res["ID"], "YANDEX_EXPORT" => $yan_exp, "SUBSCRIPTION" => $is_cont, "VAT_ID" => $cat_vat));
			if ($yan_exp=="Y") $bNeedAgent = True;
		}
	}

	CAgent::RemoveAgent("CCatalog::PreGenerateXML(\"yandex\");", "catalog");
	if ($bNeedAgent)
	{
		CAgent::AddAgent("CCatalog::PreGenerateXML(\"yandex\");", "catalog", "N", IntVal(COption::GetOptionString("catalog", "yandex_xml_period", "24"))*60*60, "", "Y");
	}
}

if ($REQUEST_METHOD=="POST" && strlen($pregenerate_discounts)>0 && !$bReadOnly /*$CAT_RIGHT=="W"*/)
{
	if (file_exists($_SERVER["DOCUMENT_ROOT"].CATALOG_DISCOUNT_FILE) && is_file($_SERVER["DOCUMENT_ROOT"].CATALOG_DISCOUNT_FILE))
		@unlink($_SERVER["DOCUMENT_ROOT"].CATALOG_DISCOUNT_FILE);
	if (file_exists($_SERVER["DOCUMENT_ROOT"].CATALOG_DISCOUNT_CPN_FILE) && is_file($_SERVER["DOCUMENT_ROOT"].CATALOG_DISCOUNT_CPN_FILE))
		@unlink($_SERVER["DOCUMENT_ROOT"].CATALOG_DISCOUNT_CPN_FILE);

	clearstatcache();

	if (file_exists($_SERVER["DOCUMENT_ROOT"].CATALOG_DISCOUNT_FILE) || file_exists($_SERVER["DOCUMENT_ROOT"].CATALOG_DISCOUNT_CPN_FILE))
	{
		$strWarning .= GetMessage("COP_CANT_DELETE").". ";
	}
	else
	{
		$dbDiscounts = CCatalogDiscount::GetList(
			array(),
			array(
				"ACTIVE" => "Y",
				"+<=ACTIVE_FROM" => Date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL"))),
				"+>=ACTIVE_TO" => Date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL"))),
			),
			false,
			false,
			array("ID")
		);
		while ($arDiscounts = $dbDiscounts->Fetch())
			CCatalogDiscount::GenerateDataFile($arDiscounts["ID"]);

		$strOK .= GetMessage("COP_REGEN_SUCCESS").". ";
	}
}

if(strlen($strWarning)>0)
	CAdminMessage::ShowMessage($strWarning);

if(strlen($strOK)>0)
	CAdminMessage::ShowNote($strOK);

$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("CO_TAB_1"), "ICON" => "catalog_settings", "TITLE" => GetMessage("CO_TAB_1_TITLE")),
	array("DIV" => "edit2", "TAB" => GetMessage("CO_TAB_2"), "ICON" => "catalog_settings", "TITLE" => GetMessage("CO_TAB_2_TITLE"))
);

if ($USER->IsAdmin())
{
	$aTabs[] = array("DIV" => "edit3", "TAB" => GetMessage("CO_TAB_3"), "ICON" => "catalog_settings", "TITLE" => GetMessage("CO_SALE_GROUPS"));
	$aTabs[] = array("DIV" => "edit4", "TAB" => GetMessage("MAIN_TAB_RIGHTS"), "ICON" => "catalog_settings", "TITLE" => GetMessage("MAIN_TAB_TITLE_RIGHTS"));
}

$tabControl = new CAdminTabControl("tabControl", $aTabs);
?>
<?
$tabControl->Begin();
?><form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialchars($mid)?>&lang=<?=LANGUAGE_ID?>" name="ara">
<?echo bitrix_sessid_post()?><?
$tabControl->BeginNextTab();

	for($i=0; $i<count($arAllOptions); $i++):
		$Option = $arAllOptions[$i];
		$val = COption::GetOptionString("catalog", $Option[0], $Option[2]);
		$type = $Option[3];
		?>
		<tr>
			<td valign="top"><?	if($type[0]=="checkbox")
							echo "<label for=\"".htmlspecialchars($Option[0])."\">".$Option[1]."</label>";
						else
							echo $Option[1];?></td>
			<td valign="middle">
					<?if($type[0]=="checkbox"):?>
						<input type="checkbox" name="<?echo htmlspecialchars($Option[0])?>" id="<?echo htmlspecialchars($Option[0])?>" value="Y"<?if($val=="Y")echo" checked";?>>
					<?elseif($type[0]=="text"):?>
						<input type="text" size="<?echo $type[1]?>" maxlength="255" value="<?echo htmlspecialchars($val)?>" name="<?echo htmlspecialchars($Option[0])?>">
					<?elseif($type[0]=="textarea"):?>
						<textarea rows="<?echo $type[1]?>" cols="<?echo $type[2]?>" name="<?echo htmlspecialchars($Option[0])?>"><?echo htmlspecialchars($val)?></textarea>
					<?endif?>
			</td>
		</tr>
	<?endfor;?>
	<tr>
		<td valign="top"><?=GetMessage("CAT_DEF_OUTFILE")?></td>
		<td valign="middle">
				<?$default_outfile_action = COption::GetOptionString("catalog", "default_outfile_action", "D");?>
				<select name="default_outfile_action">
					<option value="D" <?if ($default_outfile_action=="D" || strlen($default_outfile_action)<=0) echo "selected" ?>><?echo GetMessage("CAT_DEF_OUTFILE_D") ?></option>
					<option value="H" <?if ($default_outfile_action=="H") echo "selected" ?>><?=GetMessage("CAT_DEF_OUTFILE_H")?></option>
					<option value="F" <?if ($default_outfile_action=="F") echo "selected" ?>><?=GetMessage("CAT_DEF_OUTFILE_F")?></option>
				</select>
		</td>
	</tr>
	<tr class="heading">
		<td colspan="2" valign="top" align="center"><?echo GetMessage("CO_PAR_IE_CSV") ?></td>
	</tr>
	<tr>
		<td valign="top"><?echo GetMessage("CO_PAR_DPP_CSV") ?></td>
		<td valign="top">
			<?
			$strVal = COption::GetOptionString("catalog", "allowed_product_fields", $defCatalogAvailProdFields.",".$defCatalogAvailPriceFields);
			$arVal = explode(",", $strVal);
			$arCatalogAvailProdFields_tmp = array_merge($arCatalogAvailProdFields, $arCatalogAvailPriceFields);
			?>
			<select name="allowed_product_fields[]" multiple size="5">
				<?for ($i = 0; $i < count($arCatalogAvailProdFields_tmp); $i++):?>
					<option value="<?echo $arCatalogAvailProdFields_tmp[$i]["value"] ?>"<?if (in_array($arCatalogAvailProdFields_tmp[$i]["value"], $arVal)) echo " selected";?>><?echo $arCatalogAvailProdFields_tmp[$i]["name"]; ?></option>
				<?endfor;?>
			</select>
		</td>
	</tr>
	<tr>
		<td valign="top">
			<?= GetMessage("CO_AVAIL_PRICE_FIELDS") ?>
		</td>
		<td valign="top">
			<?
			$strVal = COption::GetOptionString("catalog", "allowed_price_fields", $defCatalogAvailValueFields);
			$arVal = explode(",", $strVal);
			?>
			<select name="allowed_price_fields[]" multiple size="5">
				<?for ($i = 0; $i < count($arCatalogAvailValueFields); $i++):?>
					<option value="<?echo $arCatalogAvailValueFields[$i]["value"] ?>"<?if (in_array($arCatalogAvailValueFields[$i]["value"], $arVal)) echo " selected";?>><?echo $arCatalogAvailValueFields[$i]["name"]; ?></option>
				<?endfor;?>
			</select>
		</td>
	</tr>
	<tr>
		<td valign="top"><?echo GetMessage("CAT_NUM_CATALOG_LEVELS");?></td>
		<td valign="top"><?
			$strVal = COption::GetOptionString("catalog", "num_catalog_levels", "3");
			?>
			<input type="text" size="5" maxlength="5" value="<?echo htmlspecialchars($strVal)?>" name="num_catalog_levels"></td>
	</tr>
	<tr>
		<td valign="top"><?echo GetMessage("CO_PAR_DPG_CSV") ?></td>
		<td valign="top"><?
			$strVal = COption::GetOptionString("catalog", "allowed_group_fields", $defCatalogAvailGroupFields);
			$arVal = explode(",", $strVal);
			?>
			<select name="allowed_group_fields[]" multiple size="5">
				<?for ($i = 0; $i < count($arCatalogAvailGroupFields); $i++):?>
					<option value="<?echo $arCatalogAvailGroupFields[$i]["value"] ?>"<?if (in_array($arCatalogAvailGroupFields[$i]["value"], $arVal)) echo " selected";?>><?echo $arCatalogAvailGroupFields[$i]["name"]; ?></option>
				<?endfor;?>
			</select></td>
	</tr>
	<tr>
		<td valign="top"><?echo GetMessage("CO_PAR_DV1_CSV")?></td>
		<td valign="top">
			<?
			$strVal = COption::GetOptionString("catalog", "allowed_currencies", $defCatalogAvailCurrencies);
			$arVal = explode(",", $strVal);

			$lcur = CCurrency::GetList(($by1="sort"), ($order1="asc"));
			?>
			<select name="allowed_currencies[]" multiple size="3">
				<?while ($lcur_res = $lcur->Fetch()):?>
					<option value="<?echo $lcur_res["CURRENCY"] ?>"<?if (in_array($lcur_res["CURRENCY"], $arVal)) echo " selected";?>><?echo $lcur_res["CURRENCY"] ?></option>
				<?endwhile;?>
			</select>
		</td>
	</tr>

<?$tabControl->BeginNextTab();?>
<tr><td>
<script type="text/javascript">
function ib_checkFldActivity(id, flag)
{
	if (flag == 0)
		if (!document.forms.ara['IS_CATALOG_' + id].checked)
			document.forms.ara['IS_CONTENT_' + id].checked = false;

	if (flag == 1)
		if (document.forms.ara['IS_CONTENT_' + id].checked)
			document.forms.ara['IS_CATALOG_' + id].checked = true;

	var bActive = document.forms.ara['IS_CATALOG_' + id].checked;
	document.forms.ara['YANDEX_EXPORT_' + id].disabled = !bActive;
	document.forms.ara['VAT_ID_' + id].disabled = !bActive;
}
</script>
<table width="100%" cellspacing="0" cellpadding="0" border="0" class="internal">
	<tr class="heading">
		<td valign="top"><?=GetMessage("CAT_IBLOCK_SELECT_NAME")?></td>
		<td valign="top"><?=GetMessage("CAT_IBLOCK_SELECT_CAT")?></td>
		<td valign="top"><?=GetMessage("CO_SALE_CONTENT") ?></td>
		<td valign="top"><?=GetMessage("CAT_IBLOCK_SELECT_YAND")?></td>
		<td valign="top"><?=GetMessage("CAT_IBLOCK_SELECT_VAT")?></td>
	</tr>
	<?
	$arVATRef = CatalogGetVATArray(array(), true);
	
	$db_res = CIBlock::GetList(Array("iblock_type"=>"asc", "name"=>"asc"));
	while ($res = $db_res->GetNext())
	{
		$ar_res1 = CCatalog::GetByID($res["ID"]);
		
		
		?>
		<tr>
			<td><?="[<a title='".GetMessage("CO_IB_TYPE_ALT")."' href='iblock_admin.php?type=".$res["IBLOCK_TYPE_ID"]."&lang=".LANGUAGE_ID."'>".$res["IBLOCK_TYPE_ID"]."</a>] <a title='".GetMessage("CO_IB_ELEM_ALT")."' href='iblock_element_admin.php?type=".$res["IBLOCK_TYPE_ID"]."&IBLOCK_ID=".$res["ID"]."&lang=".LANGUAGE_ID."&filter_section=-1&&filter=Y&set_filter=Y'>".$res["NAME"]."</a> (<a href='site_edit.php?LID=".$res["LID"]."&lang=".LANGUAGE_ID."' title='".GetMessage("CO_SITE_ALT")."'>".$res["LID"]."</a>)"?></td>
			<td align="center"><input type="checkbox" name="IS_CATALOG_<?echo $res["ID"] ?>" onclick="ib_checkFldActivity('<?=$res['ID']?>', 0)" <?if ($ar_res1) echo "checked"?> value="Y" /></td>
			<td align="center"><input type="checkbox" name="IS_CONTENT_<?echo $res["ID"] ?>" onclick="ib_checkFldActivity('<?=$res['ID']?>', 1)" <?if ($ar_res1["SUBSCRIPTION"]=="Y") echo "checked"?> value="Y" /></td>
			<td align="center"><input type="checkbox" name="YANDEX_EXPORT_<?echo $res["ID"] ?>" <?if (!$ar_res1) echo "disabled=\"disabled\""?> <?if ($ar_res1["YANDEX_EXPORT"]=="Y") echo "checked"?> value="Y" /></td>
			<td align="center"><?=SelectBoxFromArray('VAT_ID_'.$res['ID'], $arVATRef, $ar_res1['VAT_ID'], '', (!$ar_res1 ? "disabled=\"disabled\"" : ''))?></td>
		</tr>
		<?
	}
	?>
</table>
</td></tr>
<?
if ($USER->IsAdmin())
{
$tabControl->BeginNextTab();

		$strVal = COption::GetOptionString("catalog", "avail_content_groups");
		$arVal = explode(",", $strVal);

		$dbUserGroups = CGroup::GetList(($b="c_sort"), ($o="asc"), array("ANONYMOUS" => "N"));
		while ($arUserGroups = $dbUserGroups->Fetch())
		{
			$arUserGroups["ID"] = IntVal($arUserGroups["ID"]);
			if ($arUserGroups["ID"] == 2)
				continue;
			?>
			<tr>
				<td width="50%"><label for="user_group_<?=$arUserGroups["ID"]?>"><?= htmlspecialcharsEx($arUserGroups["NAME"])?></label> [<a href="group_edit.php?ID=<?=$arUserGroups["ID"]?>&lang=<?=LANGUAGE_ID?>" title="<?=GetMessage("CO_USER_GROUP_ALT")?>"><?=$arUserGroups["ID"]?></a>]:</td>
				<td width="50%"><input type="checkbox" id="user_group_<?=$arUserGroups["ID"]?>" name="AVAIL_CONTENT_GROUPS[]"<?if (in_array($arUserGroups["ID"], $arVal)) echo " checked"?> value="<?= $arUserGroups["ID"] ?>"></td>
			</tr>
			<?
		}
?>
<?$tabControl->BeginNextTab();?>
<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights2.php");?>
<?
}
?>
<?$tabControl->Buttons();?>
<script language="JavaScript">
function RestoreDefaults()
{
	if (confirm('<?echo AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>'))
		window.location = "<?echo $APPLICATION->GetCurPage()?>?RestoreDefaults=Y&lang=<?echo LANG?>&mid=<?echo urlencode($mid)?>&<?echo bitrix_sessid_get()?>";
}
</script>
<input type="submit" <?if ($bReadOnly /*$CAT_RIGHT<"W"*/) echo "disabled" ?> name="Update" value="<?echo GetMessage("MAIN_SAVE")?>">
<input type="hidden" name="Update" value="Y">
<input type="reset" name="reset" value="<?echo GetMessage("MAIN_RESET")?>">
<input type="button" <?if ($bReadOnly /*CAT_RIGHT<"W"*/) echo "disabled" ?> title="<?echo GetMessage("MAIN_HINT_RESTORE_DEFAULTS")?>" OnClick="RestoreDefaults();" value="<?echo GetMessage("MAIN_RESTORE_DEFAULTS")?>">
<?$tabControl->End();?>
</form>



<h2><?= GetMessage("COP_SYS_ROU") ?></h2>
<?
$aTabs = Array();
$aTabs = array(
	array("DIV" => "fedit1", "TAB" => GetMessage("COP_TAB2_TAB"), "ICON" => "catalog_settings", "TITLE" => GetMessage("COP_TAB2_TAB_TITLE")),
);
$tabControl = new CAdminTabControl("tabControl2", $aTabs);

$tabControl->Begin();
$tabControl->BeginNextTab();
	?>
	<tr>
		<td align="left" colspan="2">
			<form name="preg_form" method="POST" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialchars($mid)?>&lang=<?=LANGUAGE_ID?>">
			<?echo bitrix_sessid_post()?>
			<input type="button" <?if ($bReadOnly /*$CAT_RIGHT < "W"*/) echo "disabled" ?> name="preg_but" value="<?= GetMessage("COP_PREG_BUT") ?>" OnClick="javascript: PregenerateDiscounts();">
			<input type="hidden" name="pregenerate_discounts" value="Y">
			<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
			<SCRIPT LANGUAGE="JavaScript">
			<!--
			function PregenerateDiscounts()
			{
				if (confirm('<?= GetMessage("COP_PREG_CONFIRM") ?>'))
					document.preg_form.submit();
			}
			//-->
			</SCRIPT>
			</form>
		</td>
	</tr>
	<?
$tabControl->End();
?>
<?endif;?>
