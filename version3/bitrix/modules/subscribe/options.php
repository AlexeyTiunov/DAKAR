<?
$module_id = "subscribe";
$POST_RIGHT = $APPLICATION->GetGroupRight($module_id);
if($POST_RIGHT>="R") :

IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/options.php");
IncludeModuleLangFile(__FILE__);

$arAllOptions = array(
	array("allow_anonymous", GetMessage("opt_anonym"), array("checkbox", "Y")),
	array("show_auth_links", GetMessage("opt_links"), array("checkbox", "Y")),
	array("subscribe_section", GetMessage("opt_sect"), array("text", 35)),
	array("posting_use_editor", GetMessage("opt_vis_edit"), array("checkbox", "Y")),
	array("posting_interval", GetMessage("opt_interval"), array("text", 5)),
	array("max_bcc_count", GetMessage("opt_max_bcc_count"), array("text", 5)),
	array("default_from", GetMessage("opt_def_from"), array("text", 35)),
	array("default_to", GetMessage("opt_def_to"), array("text", 35)),
	array("posting_charset", GetMessage("opt_encoding"), array("text-list", 3, 20)),
	array("allow_8bit_chars", GetMessage("opt_allow_8bit"), array("checkbox", "Y")),
	array("mail_additional_parameters", GetMessage("opt_mail_additional_parameters"), array("text", 35)),
	array("attach_images", GetMessage("opt_attach"), array("checkbox", "Y")),
	array("subscribe_confirm_period", GetMessage("opt_delete"), array("text", 5)),
	array("subscribe_auto_method", GetMessage("opt_method"), array("selectbox", array("agent"=>GetMessage("opt_method_agent"), "cron"=>GetMessage("opt_method_cron")))),
	array("subscribe_max_emails_per_hit", GetMessage("opt_max_per_hit"), array("text", 5)),
	array("subscribe_template_method", GetMessage("opt_template_method"), array("selectbox", array("agent"=>GetMessage("opt_method_agent"), "cron"=>GetMessage("opt_method_cron")))),
	array("subscribe_template_interval", GetMessage("opt_template_interval"), array("text", 10))
);
$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("MAIN_TAB_SET"), "ICON" => "subscribe_settings", "TITLE" => GetMessage("MAIN_TAB_TITLE_SET")),
	array("DIV" => "edit2", "TAB" => GetMessage("MAIN_TAB_RIGHTS"), "ICON" => "subscribe_settings", "TITLE" => GetMessage("MAIN_TAB_TITLE_RIGHTS")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

if($REQUEST_METHOD=="POST" && strlen($Update.$Apply.$RestoreDefaults)>0 && $POST_RIGHT=="W" && check_bitrix_sessid())
{
	if(strlen($RestoreDefaults)>0)
	{
		COption::RemoveOption("subscribe");
		$z = CGroup::GetList($v1="id",$v2="asc", array("ACTIVE" => "Y", "ADMIN" => "N"));
		while($zr = $z->Fetch())
			$APPLICATION->DelGroupRight($module_id, array($zr["ID"]));
	}
	else
	{
		foreach($arAllOptions as $arOption)
		{
			$name = $arOption[0];
			if($arOption[2][0]=="text-list")
			{
				$val = "";
				for($j=0; $j<count($$name); $j++)
				{
					if(strlen(trim(${$name}[$j])) > 0)
						$val .= ($val <> ""? ",":"").trim(${$name}[$j]);
				}
			}
			else
				$val=$$name;
			if($arOption[2][0] == "checkbox" && $val <> "Y")
				$val="N";
			if($name != "mail_additional_parameters" || $USER->IsAdmin())
				COption::SetOptionString($module_id, $name, $val);
		}
	}
	CAgent::RemoveAgent("CPostingTemplate::Execute();", "subscribe");
	if(COption::GetOptionString("subscribe", "subscribe_template_method")!=="cron")
		CAgent::AddAgent("CPostingTemplate::Execute();", "subscribe", "N", COption::GetOptionString("subscribe", "subscribe_template_interval"));

	$Update = $Update.$Apply;
	ob_start();
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");
	ob_end_clean();

	if(strlen($_REQUEST["back_url_settings"]) > 0)
	{
		if((strlen($Apply) > 0) || (strlen($RestoreDefaults) > 0))
			LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($module_id)."&lang=".urlencode(LANGUAGE_ID)."&back_url_settings=".urlencode($_REQUEST["back_url_settings"])."&".$tabControl->ActiveTabParam());
		else
			LocalRedirect($_REQUEST["back_url_settings"]);
	}
	else
	{
		LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($module_id)."&lang=".urlencode(LANGUAGE_ID)."&".$tabControl->ActiveTabParam());
	}
}

?>
<form method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=urlencode($module_id)?>&amp;lang=<?=LANGUAGE_ID?>">
<?
$tabControl->Begin();
$tabControl->BeginNextTab();

	foreach($arAllOptions as $Option):
	$type = $Option[2];
	$val = COption::GetOptionString($module_id, $Option[0]);
	?>
	<tr>
		<td valign="top" width="50%"><?
			if($type[0]=="checkbox")
				echo "<label for=\"".htmlspecialchars($Option[0])."\">".$Option[1]."</label>";
			else
				echo $Option[1];
		?></td>
		<td valign="middle" width="50%"><?
			if($type[0]=="checkbox"):
				?><input type="checkbox" name="<?echo htmlspecialchars($Option[0])?>" id="<?echo htmlspecialchars($Option[0])?>" value="Y"<?if($val=="Y")echo" checked";?>><?
			elseif($type[0]=="text"):
				?><input type="text" size="<?echo $type[1]?>" maxlength="255" value="<?echo htmlspecialchars($val)?>" name="<?echo htmlspecialchars($Option[0])?>"><?
			elseif($type[0]=="textarea"):
				?><textarea rows="<?echo $type[1]?>" cols="<?echo $type[2]?>" name="<?echo htmlspecialchars($Option[0])?>"><?echo htmlspecialchars($val)?></textarea><?
			elseif($type[0]=="text-list"):
				$aVal = explode(",", $val);
				for($j=0; $j<count($aVal); $j++):
					?><input type="text" size="<?echo $type[2]?>" value="<?echo htmlspecialchars($aVal[$j])?>" name="<?echo htmlspecialchars($Option[0])."[]"?>"><br><?
				endfor;
				for($j=0; $j<$type[1]; $j++):
					?><input type="text" size="<?echo $type[2]?>" value="" name="<?echo htmlspecialchars($Option[0])."[]"?>"><br><?
				endfor;
			elseif($type[0]=="selectbox"):
				$arr = $type[1];
				$arr_keys = array_keys($arr);
				?><select name="<?echo htmlspecialchars($Option[0])?>"><?
					for($j=0; $j<count($arr_keys); $j++):
						?><option value="<?echo $arr_keys[$j]?>"<?if($val==$arr_keys[$j])echo" selected"?>><?echo htmlspecialchars($arr[$arr_keys[$j]])?></option><?
					endfor;
					?></select><?
			endif;
		?></td>
	</tr>
	<?endforeach?>
<?$tabControl->BeginNextTab();?>
<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");?>
<?$tabControl->Buttons();?>
	<input <?if ($POST_RIGHT<"W") echo "disabled" ?> type="submit" name="Update" value="<?=GetMessage("MAIN_SAVE")?>" title="<?=GetMessage("MAIN_OPT_SAVE_TITLE")?>">
	<input <?if ($POST_RIGHT<"W") echo "disabled" ?> type="submit" name="Apply" value="<?=GetMessage("MAIN_OPT_APPLY")?>" title="<?=GetMessage("MAIN_OPT_APPLY_TITLE")?>">
	<?if(strlen($_REQUEST["back_url_settings"])>0):?>
		<input <?if ($POST_RIGHT<"W") echo "disabled" ?> type="button" name="Cancel" value="<?=GetMessage("MAIN_OPT_CANCEL")?>" title="<?=GetMessage("MAIN_OPT_CANCEL_TITLE")?>" onclick="window.location='<?echo htmlspecialchars(CUtil::addslashes($_REQUEST["back_url_settings"]))?>'">
		<input type="hidden" name="back_url_settings" value="<?=htmlspecialchars($_REQUEST["back_url_settings"])?>">
	<?endif?>
	<input <?if ($POST_RIGHT<"W") echo "disabled" ?> type="submit" name="RestoreDefaults" title="<?echo GetMessage("MAIN_HINT_RESTORE_DEFAULTS")?>" OnClick="return confirm('<?echo AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>')" value="<?echo GetMessage("MAIN_RESTORE_DEFAULTS")?>">
	<?=bitrix_sessid_post();?>
<?$tabControl->End();?>
</form>
<?endif;?>
