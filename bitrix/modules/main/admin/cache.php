<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/prolog.php");
define("HELP_FILE", "settings/cache.php");
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/classes/general/cache_html.php");

if(!$USER->CanDoOperation('cache_control') && !$USER->CanDoOperation('view_other_settings'))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$isAdmin = $USER->CanDoOperation('cache_control');

IncludeModuleLangFile(__FILE__);

$errorMessage = "";
$okMessage = "";

if ($REQUEST_METHOD=="POST" && $clearcache=="Y" && strlen($cachetype)>0 && check_bitrix_sessid() && $isAdmin)
{
	if($cachetype=="menu")
		$GLOBALS["CACHE_MANAGER"]->CleanDir("menu");
	elseif($cachetype=="managed")
	{
		$GLOBALS["CACHE_MANAGER"]->CleanAll();
		$GLOBALS["stackCacheManager"]->CleanAll();
	}
	elseif($cachetype=="html")
	{
		CHTMLPagesCache::CleanAll();
	}
	else
	{
		BXClearCache($cachetype=="all");
		$GLOBALS["CACHE_MANAGER"]->CleanAll();
		$GLOBALS["stackCacheManager"]->CleanAll();
		CHTMLPagesCache::CleanAll();
	}
	$okMessage .= GetMessage("MAIN_OPTION_CACHE_OK").". ";
}

if ($REQUEST_METHOD=="POST" && ($cache_on=="Y" || $cache_on=="N") && check_bitrix_sessid() && $isAdmin)
{
	if(COption::GetOptionString("main", "component_cache_on", "Y")=="Y")
	{
		if ($cache_on=="N")
		{
			COption::SetOptionString("main", "component_cache_on", "N");
			$okMessage .= GetMessage("MAIN_OPTION_CACHE_SUCCESS").". ";
		}
	}
	else
	{
		if ($cache_on=="Y")
		{
			COption::SetOptionString("main", "component_cache_on", "Y");
			$okMessage .= GetMessage("MAIN_OPTION_CACHE_SUCCESS").". ";
		}
	}
}

if($REQUEST_METHOD=="POST" && ($managed_cache_on=="Y" || $managed_cache_on=="N") && check_bitrix_sessid() && $isAdmin)
{
	COption::SetOptionString("main", "component_managed_cache_on", $managed_cache_on);
	LocalRedirect($APPLICATION->GetCurPage()."?lang=".LANGUAGE_ID."&tabControl_active_tab=fedit4&res=managed_saved");
}
if($_REQUEST["res"] == "managed_saved")
	$okMessage .= GetMessage("main_cache_managed_saved");

if ($REQUEST_METHOD=="POST" && strlen($html_cache_siteb) && ($html_cache_on=="Y" || $html_cache_on=="N") && check_bitrix_sessid() && $isAdmin)
{
	if(CHTMLPagesCache::IsOn())
	{
		if ($html_cache_on == "N")
		{
			CHTMLPagesCache::SetEnabled(false);
			$okMessage .= GetMessage("MAIN_OPTION_HTML_CACHE_SUCCESS")." ";
		}
	}
	else
	{
		if ($html_cache_on == "Y")
		{
			CHTMLPagesCache::SetEnabled(true);
			$okMessage .= GetMessage("MAIN_OPTION_HTML_CACHE_SUCCESS")." ";
		}
	}
}

if ($REQUEST_METHOD=="POST" && strlen($html_cache_save_opt) && check_bitrix_sessid() && $isAdmin)
{
	$arHTMLCacheOptions = CHTMLPagesCache::GetOptions();
	$arHTMLCacheOptions["INCLUDE_MASK"] = $html_cache_include_mask;
	$arHTMLCacheOptions["EXCLUDE_MASK"] = $html_cache_exclude_mask;
	$arHTMLCacheOptions["FILE_QUOTA"] = $html_cache_quota;
	CHTMLPagesCache::SetOptions($arHTMLCacheOptions);
}

if ($REQUEST_METHOD=="POST" && strlen($html_cache_reset_opt) && check_bitrix_sessid() && $isAdmin)
{
	CHTMLPagesCache::SetOptions(array());
}

$APPLICATION->SetTitle(GetMessage("MCACHE_TITLE"));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
?>

<?
if(strlen($errorMessage)>0)
	echo CAdminMessage::ShowMessage(Array("DETAILS"=>$errorMessage, "TYPE"=>"ERROR", "MESSAGE"=>GetMessage("SAE_ERROR"), "HTML"=>true));
if(strlen($okMessage)>0)
	echo CAdminMessage::ShowNote($okMessage);
?>

<?
$aTabs = Array();
$aTabs = array(
	array("DIV" => "fedit1", "TAB" => GetMessage("MAIN_TAB_4"), "ICON" => "main_settings", "TITLE" => GetMessage("MAIN_OPTION_PUBL")),
	array("DIV" => "fedit4", "TAB" => GetMessage("main_cache_managed"), "ICON" => "main_settings", "TITLE" => GetMessage("main_cache_managed_sett")),
	array("DIV" => "fedit3", "TAB" => GetMessage("MAIN_TAB_2"), "ICON" => "main_settings", "TITLE" => GetMessage("MAIN_OPTION_HTML_CACHE")),
	array("DIV" => "fedit2", "TAB" => GetMessage("MAIN_TAB_3"), "ICON" => "main_settings", "TITLE" => GetMessage("MAIN_OPTION_CLEAR_CACHE")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

$tabControl->Begin();
?>
<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?lang=<?echo LANG?>">
<?=bitrix_sessid_post()?>
<?$tabControl->BeginNextTab();?>
<tr>
	<td valign="top" colspan="2" align="left">
		<?if(COption::GetOptionString("main", "component_cache_on", "Y")=="Y"):?>
			<span style="color:green;"><b><?echo GetMessage("MAIN_OPTION_CACHE_ON")?>.</b></span>
		<?else:?>
			<span style="color:red;"><b><?echo GetMessage("MAIN_OPTION_CACHE_OFF")?>.</b></span>
		<?endif?>
	</td>
</tr>
<tr>
	<td valign="top" colspan="2" align="left">
		<?if(COption::GetOptionString("main", "component_cache_on", "Y")=="Y"):?>
			<input type="hidden" name="cache_on" value="N">
			<input type="submit" name="cache_siteb" value="<?echo GetMessage("MAIN_OPTION_CACHE_BUTTON_OFF")?>"<?if(!$isAdmin) echo " disabled"?>>
		<?else:?>
			<input type="hidden" name="cache_on" value="Y">
			<input type="submit" name="cache_siteb" value="<?echo GetMessage("MAIN_OPTION_CACHE_BUTTON_ON")?>"<?if(!$isAdmin) echo " disabled"?>>
		<?endif?>
	</td>
</tr>
<tr>
	<td colspan="2">
		<?echo BeginNote();?><?echo GetMessage("cache_admin_note1")?>
		<?echo EndNote(); ?>
	</td>
</tr>
<?$tabControl->EndTab();?>
</form>

<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?lang=<?echo LANG?>&amp;tabControl_active_tab=fedit4">
<?=bitrix_sessid_post()?>
<?$tabControl->BeginNextTab();?>
<?
	$component_managed_cache = COption::GetOptionString("main", "component_managed_cache_on", "Y");
?>
<tr>
	<td valign="top" colspan="2" align="left">
		<?if($component_managed_cache <> "N" || defined("BX_COMP_MANAGED_CACHE")):?>
			<span style="color:green;"><b><?echo GetMessage("main_cache_managed_on")?></b></span>
		<?else:?>
			<span style="color:red;"><b><?echo GetMessage("main_cache_managed_off")?></b></span>
		<?endif?>
	</td>
</tr>
<tr>
	<td valign="top" colspan="2" align="left">
		<?if($component_managed_cache <> "N" || defined("BX_COMP_MANAGED_CACHE")):?>
			<input type="hidden" name="managed_cache_on" value="N">
			<input type="submit" name="" value="<?echo GetMessage("main_cache_managed_turn_off")?>"<?if(!$isAdmin || $component_managed_cache == "N") echo " disabled"?>>
			<?if($component_managed_cache == "N"):?><br><br><?echo GetMessage("main_cache_managed_const")?><?endif?>
		<?else:?>
			<input type="hidden" name="managed_cache_on" value="Y">
			<input type="submit" name="" value="<?echo GetMessage("main_cache_managed_turn_on")?>"<?if(!$isAdmin) echo " disabled"?>>
		<?endif?>
	</td>
</tr>
<tr>
	<td colspan="2">
		<?echo BeginNote();?>
		<?echo GetMessage("main_cache_managed_note")?>
		<?echo EndNote(); ?>
	</td>
</tr>
<?$tabControl->EndTab();?>
</form>

<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?lang=<?echo LANG?>&amp;tabControl_active_tab=fedit3">
<?=bitrix_sessid_post()?>
<?$tabControl->BeginNextTab();?>
<tr>
	<td valign="top" colspan="2" align="left">
		<?if(IsModuleInstalled('statistic') || IsModuleInstalled('advertising')):?>
			<div style="color:red;"><b><?echo GetMessage("MAIN_OPTION_HTML_CACHE_WARNING")?></b></div><br>
		<?endif?>
		<?
		$use_trans_sid = strtolower(ini_get("session.use_trans_sid")."");
		if( ($use_trans_sid === "1") || ($use_trans_sid === "on") ):?>
			<div style="color:red;"><b><?echo GetMessage("MAIN_OPTION_HTML_CACHE_WARNING_TRANSID")?></b></div><br>
		<?endif?>
		<?if(CHTMLPagesCache::IsOn()):?>
			<div style="color:green;"><b><?echo GetMessage("MAIN_OPTION_HTML_CACHE_ON")?>.</b></div><br>
			<input type="hidden" name="html_cache_on" value="N">
			<input type="submit" name="html_cache_siteb" value="<?echo GetMessage("MAIN_OPTION_HTML_CACHE_BUTTON_OFF")?>"<?if(!$isAdmin) echo " disabled"?>>
		<?else:?>
			<div style="color:red;"><b><?echo GetMessage("MAIN_OPTION_HTML_CACHE_OFF")?>.</b></div><br>
			<input type="hidden" name="html_cache_on" value="Y">
			<input type="submit" name="html_cache_siteb" value="<?echo GetMessage("MAIN_OPTION_HTML_CACHE_BUTTON_ON")?>"<?if(!$isAdmin) echo " disabled"?>>
		<?endif?>
	</td>
</tr>
<?if(CHTMLPagesCache::IsOn()):
		$arStatistic = CHTMLPagesCache::readStatistic();?>
<tr class="heading">
	<td colspan="2"><?echo GetMessage("MAIN_OPTION_HTML_CACHE_STAT");?></td>
	<?$arHTMLCacheOptions = CHTMLPagesCache::GetOptions();?>
</tr>
<tr>
	<td valign="top" colspan="2" align="center">
		<table border="0" cellpadding="0" cellspacing="0" class="internal">
		<tr class="heading">
			<td align="center"><?echo GetMessage("MAIN_OPTION_HTML_CACHE_STAT_HITS")?></td>
			<td align="center"><?echo GetMessage("MAIN_OPTION_HTML_CACHE_STAT_MISSES")?></td>
			<td align="center"><?echo GetMessage("MAIN_OPTION_HTML_CACHE_STAT_QUOTA")?></td>
			<td align="center"><?echo GetMessage("MAIN_OPTION_HTML_CACHE_STAT_POSTS")?></td>
		</tr>
		<tr>
			<td align="right"><?echo $arStatistic["HITS"]?></td>
			<td align="right"><?echo $arStatistic["MISSES"]?></td>
			<td align="right"><?echo $arStatistic["QUOTA"]?></td>
			<td align="right"><?echo $arStatistic["POSTS"]?></td>
		</tr>
		</table>
	</td>
</tr>
<?endif?>
<tr class="heading">
	<td colspan="2"><?echo GetMessage("MAIN_OPTION_HTML_CACHE_OPT");?></td>
	<?$arHTMLCacheOptions = CHTMLPagesCache::GetOptions();?>
</tr>
<tr>
	<td><?echo GetMessage("MAIN_OPTION_HTML_CACHE_INC_MASK");?>:</td>
	<td>
		<input type="text" size="45" name="html_cache_include_mask" value="<?echo htmlspecialchars($arHTMLCacheOptions["INCLUDE_MASK"])?>">
	</td>
</tr>
<tr>
	<td><?echo GetMessage("MAIN_OPTION_HTML_CACHE_EXC_MASK");?>:</td>
	<td>
		<input type="text" size="45" name="html_cache_exclude_mask" value="<?echo htmlspecialchars($arHTMLCacheOptions["EXCLUDE_MASK"])?>">
	</td>
</tr>
<tr>
	<td><?echo GetMessage("MAIN_OPTION_HTML_CACHE_QUOTA");?>:</td>
	<td>
		<input type="text" size="8" name="html_cache_quota" value="<?echo intval($arHTMLCacheOptions["FILE_QUOTA"])?>">
	</td>
</tr>
<tr>
	<td valign="top" colspan="2" align="left">
		<input type="submit" name="html_cache_save_opt" value="<?echo GetMessage("MAIN_OPTION_HTML_CACHE_SAVE");?>"<?if(!$isAdmin) echo " disabled"?>>
		<input type="submit" name="html_cache_reset_opt" value="<?echo GetMessage("MAIN_OPTION_HTML_CACHE_RESET");?>"<?if(!$isAdmin) echo " disabled"?>>
	</td>
</tr>
<tr>
	<td colspan="2">
		<?echo BeginNote();?><?echo GetMessage("cache_admin_note4")?>
		<?echo EndNote(); ?>
	</td>
</tr>
<?$tabControl->EndTab();?>
</form>

<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?lang=<?echo LANG?>">
<?=bitrix_sessid_post()?>
<?
$tabControl->BeginNextTab();
?>
<tr>
	<td colspan="2" valign="top" align="left">
		<input type="hidden" name="clearcache" value="Y">
		<input type="radio" name="cachetype" id="cachetype1" value="expired"<?if($cachetype!="all" && $cachetype!="menu" && $cachetype!="managed")echo " checked"?>> <label for="cachetype1"><?echo GetMessage("MAIN_OPTION_CLEAR_CACHE_OLD")?></label><br>
		<input type="radio" name="cachetype" id="cachetype2" value="all"<?if($cachetype=="all")echo " checked"?>> <label for="cachetype2"><?echo GetMessage("MAIN_OPTION_CLEAR_CACHE_ALL")?></label><br>
		<input type="radio" name="cachetype" id="cachetype3" value="menu"<?if($cachetype=="menu")echo " checked"?>> <label for="cachetype3"><?echo GetMessage("MAIN_OPTION_CLEAR_CACHE_MENU")?></label><br>
		<input type="radio" name="cachetype" id="cachetype4" value="managed"<?if($cachetype=="managed")echo " checked"?>> <label for="cachetype4"><?echo GetMessage("MAIN_OPTION_CLEAR_CACHE_MANAGED")?></label><br>
		<input type="radio" name="cachetype" id="cachetype5" value="html"<?if($cachetype=="static")echo " checked"?>> <label for="cachetype5"><?echo GetMessage("MAIN_OPTION_CLEAR_CACHE_STATIC")?></label><br>
	</td>
</tr>
<tr>
	<td valign="top" colspan="2" align="left"><input type="submit" name="clear" value="<?echo GetMessage("MAIN_OPTION_CLEAR_CACHE_CLEAR")?>"<?if(!$isAdmin) echo " disabled"?>></td>
</tr>
<tr>
	<td colspan="2">
		<?echo BeginNote();?>
		<?echo GetMessage("cache_admin_note2")?>
		<?echo EndNote(); ?>
	</td>
</tr>
<?$tabControl->End();?>
</form>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>