<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
IncludeModuleLangFile(__FILE__);

if($_GET["back_url_pub"] <> "" && !is_array($_GET["back_url_pub"]))
	$_SESSION["BACK_URL_PUB"] = $_GET["back_url_pub"];

$params = DeleteParam(array("logout", "back_url_pub"));

$arPanelButtons = array();
$arLangs = CLanguage::GetLangSwitcherArray();

if($USER->IsAuthorized())
{
	//Favorites
	if(is_callable(array($USER,'CanDoOperation')) && ($USER->CanDoOperation('edit_own_profile') || $USER->CanDoOperation('edit_other_settings') || $USER->CanDoOperation('view_other_settings')))
	{
		$aFav = array();
		if(is_callable(array('CUtil', 'addslashes')))
		{
			$aFav = array(
				array(
					"TEXT"=>GetMessage("top_panel_add_fav"),
					"TITLE"=>GetMessage("MAIN_ADD_PAGE_TO_FAVORITES"),
					"ACTION"=>"jsUtils.Redirect(arguments, '".CUtil::addslashes(BX_ROOT."/admin/favorite_edit.php?lang=".LANG."&amp;name=".urlencode($APPLICATION->GetTitle()))."&amp;addurl='+encodeURIComponent(document.getElementById('navchain-link').getAttribute('href', 2)));"
				),
				array(
					"TEXT"=>GetMessage("top_panel_org_fav"),
					"TITLE"=>GetMessage("top_panel_org_fav_title"),
					"ACTION"=>"jsUtils.Redirect(arguments, '".CUtil::addslashes(BX_ROOT."/admin/favorite_list.php?lang=".LANG)."');"
				),
			);
			if(is_callable(array('CFavorites','Delete')))
			{
				$db_fav = CFavorites::GetList(array("COMMON"=>"ASC", "SORT"=>"ASC", "NAME"=>"ASC"), array("MENU_FOR_USER"=>$USER->GetID(), "LANGUAGE_ID"=>LANGUAGE_ID));
				$prevCommon = "";
				while($db_fav_arr = $db_fav->Fetch())
				{
					if($db_fav_arr["COMMON"] == "Y" && $db_fav_arr["MODULE_ID"] <> "" && $APPLICATION->GetGroupRight($db_fav_arr["MODULE_ID"]) < "R")
						continue;
					if($db_fav_arr["COMMON"] <> $prevCommon)
					{
						$aFav[] = array("SEPARATOR"=>true);
						$prevCommon = $db_fav_arr["COMMON"];
					}

					$sTitle = $db_fav_arr["COMMENTS"];
					$sTitle = (strlen($sTitle)>100? substr($sTitle, 0, 100)."..." : $sTitle);
					$sTitle = str_replace("\r\n", "\n", $sTitle);
					$sTitle = str_replace("\r", "\n", $sTitle);
					$sTitle = str_replace("\n", " ", $sTitle);

					$aFav[] = array(
						"TEXT"=>htmlspecialchars($db_fav_arr["NAME"]),
						"TITLE"=>htmlspecialchars($sTitle),
						"ICON"=>"favorites",
						"ACTION"=>"jsUtils.Redirect(arguments, '".CUtil::addslashes($db_fav_arr["URL"])."');",
					);
				}
			}
		}
		$arPanelButtons[] = array(
			"TEXT"=>GetMessage("top_panel_favorites"),
			"TITLE"=>GetMessage("top_panel_fav"),
			"ICON"=>"top_panel_favorites",
			"MENU"=>$aFav,
		);
	}

	$bCanViewSettings = (is_callable(array($USER,'CanDoOperation')) && ($USER->CanDoOperation('view_other_settings') || $USER->CanDoOperation('edit_other_settings')));
	if($bCanViewSettings)
	{
		//Settings
		$arPanelButtons[] = array(
			"TEXT"=>GetMessage("top_panel_settings"),
			"TITLE"=>GetMessage("button_settings"),
			"LINK"=>BX_ROOT."/admin/settings.php?lang=".LANG."&amp;mid=".(defined("ADMIN_MODULE_NAME")? ADMIN_MODULE_NAME:"main").($APPLICATION->GetCurPage() <> BX_ROOT."/admin/settings.php"? "&amp;back_url_settings=".urlencode($_SERVER["REQUEST_URI"]):""),
			"ICON"=>"top_panel_settings",
		);
	}

	//Help
	$page = (defined("HELP_FILE") && strpos(HELP_FILE, '/') === false? HELP_FILE : basename($APPLICATION->GetCurPage()));
	$module = (defined("ADMIN_MODULE_NAME")? ADMIN_MODULE_NAME:"main");
	$aActiveSection = $adminMenu->ActiveSection();
	if(LANGUAGE_ID == "ru")
		$help_link = "http://dev.1c-bitrix.ru/".(IsModuleInstalled('intranet')?"intranet":"user")."_help/".$aActiveSection["help_section"]."/".(defined("HELP_FILE") && strpos(HELP_FILE, '/') !== false?  HELP_FILE : $module."/".$page);
	else
		$help_link = "http://www.bitrixsoft.com/help/index.html?page=".urlencode("source/".$module."/help/en/".$page.".html");

	$arPanelButtons[] = array(
		"TEXT"=>GetMessage("top_panel_help"),
		"TITLE"=>GetMessage("MAIN_HELP"),
		"LINK"=>$help_link,
		"ICON"=>"top_panel_help",
		"LINK_PARAM"=>'target="bxHelpWindow"',
	);

	if($USER->CanDoOperation('install_updates'))
	{
		$update_res = UpdateTools::GetUpdateResult();
	
		//update
		$arPanelButtons[] = array(
			"SEPARATOR"=>true,
		);
		$arPanelButtons[] = array(
			"TITLE"=>($update_res["result"] == true? GetMessage("top_panel_updates_hint") : GetMessage("top_panel_update")),
			"LINK"=>"/bitrix/admin/sysupdate.php?lang=".LANGUAGE_ID,
			"ICON"=>($update_res["result"] == true? "top_panel_update_act" : "top_panel_update"),
			"TOOLTIP"=>$update_res['tooltip'],
			"TOOLTIP_ACTION"=>"jsUserOptions.SaveOption('update', 'options', 'tooltip', 'off');",
		);
	}

	$arPanelButtons[] = array(
		"SEPARATOR"=>true,
	);
}

foreach($arLangs as $adminLang)
{
	$arPanelButtons[] = array(
		"TEXT"=>$adminLang["LID"],
		"TITLE"=>GetMessage("top_panel_lang")." ".$adminLang["NAME"],
		"LINK"=>$adminLang["PATH"],
		"SELECTED"=>$adminLang["SELECTED"],
	);
}

if($USER->IsAuthorized())
{
	$arPanelButtons[] = array(
		"SEPARATOR"=>true,
	);
	$arPanelButtons[] = array(
		"TEXT"=>'[<a href="/bitrix/admin/user_edit.php?lang='.LANG.'&amp;ID='.$USER->GetID().'">'.$USER->GetID().'</a>] ('.$USER->GetLogin().') '.htmlspecialchars($USER->GetFullName()),
		"TITLE"=>GetMessage("top_panel_user"),
	);
	$arPanelButtons[] = array(
		"SEPARATOR"=>true,
	);
	$arPanelButtons[] = array(
		"TEXT"=>GetMessage("top_panel_logout"),
		"TITLE"=>GetMessage("TOP_LOG_OFF"),
		"LINK"=>$APPLICATION->GetCurPage()."?logout=yes".htmlspecialchars($params == ""? "":"&".$params),
		"ICON"=>"top_panel_logout",
	);
}

$sPubUrl = ($_SESSION["BACK_URL_PUB"] <> ""?
	htmlspecialchars($_SESSION["BACK_URL_PUB"]).(strpos($_SESSION["BACK_URL_PUB"], "?") !== false? "&amp;":"?") : '/?').
	'back_url_admin='.urlencode($APPLICATION->GetCurPage().($params<>""? "?".$params:""));
?>

<?
$bShowTabs = (version_compare(SM_VERSION, "5.1.3") >= 0);
if($bShowTabs):

$res ='
<div style="display:none; overflow:hidden;" id="bx_top_panel_back"></div>
<div class="top-panel" id="bx_top_panel_container">
<div class="empty top"></div>
<table cellspacing="0" cellpadding="0" class="panel-container">
	<tr valign="top">
		<td width="0%" class="start-button"><div class="start-button" onclick="jsStartMenu.ShowStartMenu(this);" onmouseover="this.className+=\' start-over\'" onmouseout="this.className=this.className.replace(/\s*start-over/i, \'\')" title="'.GetMessage("top_panel_start_btn").'"></div></td>
		<td width="100%">
';
			$aTabs = array(
				array(
					"ICON"=>"tab-icon-view",
					"URL"=>$sPubUrl."&amp;bitrix_include_areas=N&amp;bitrix_show_mode=view",
					"TEXT"=>GetMessage("top_panel_view"),
					"TITLE"=>GetMessage("adm_top_panel_view_title")
				),
				array(
					"ICON"=>"tab-icon-edit",
					"URL"=>$sPubUrl."&amp;bitrix_include_areas=Y&amp;bitrix_show_mode=edit",
					"TEXT"=>GetMessage("top_panel_edit"),
					"TITLE"=>GetMessage("top_panel_edit_title")
				),
				array(
					"ICON"=>"tab-icon-configure",
					"URL"=>$sPubUrl."&amp;bitrix_include_areas=Y&amp;bitrix_show_mode=configure",
					"TEXT"=>GetMessage("top_panel_sett"),
					"TITLE"=>GetMessage("top_panel_sett_title")
				),
				array(
					"ICON"=>"tab-icon-admin",
					"URL"=>BX_ROOT."/admin/index.php?lang=".LANGUAGE_ID,
					"TEXT"=>GetMessage("adm_top_panel_adm"),
					"TITLE"=>GetMessage("TOP_ADMIN_ENTER")
				),
			);

			$cnt = count($aTabs);
			foreach($aTabs as $i=>$tab)
			{
				if($tab["URL"] <> "")
					$res .= '<a href="'.$tab["URL"].'" title="'.$tab["TITLE"].'">';

				$res .='
<div class="panel-tab'.($i == $cnt-1? ' panel-tab-admin':'').'">
	<div class="tab-left'.($i == 0? ' tab-left-first':'').'"><div class="tab-icon '.$tab["ICON"].'"></div></div>
	<div class="tab"><div class="caption">'.$tab["TEXT"].'</div></div>
	<div class="tab-right'.($i == $cnt-2? ' tab-right-next-active':'').'"></div>
	<br />
	<div class="tab-bottom'.($i == $cnt-1? ' tab-bottom-active':'').($i == 0? ' tab-bottom-first':'').'"></div>
</div>';
				if($tab["URL"] <> "")
					$res .= '</a>';
			}

			$res .= '
	<a id="admin_panel_fix_link" class="fix-link fix-on" href="javascript:jsPanel.FixPanel();" title="'.GetMessage("top_panel_fix_on").'"></a>
	<br clear="all" />
';

echo $res;

endif; //$bShowTabs
?>
	<div class="toppanel">
	<table border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td class="left"></td>
<?
$bWasPopup = false;
foreach($arPanelButtons as $item)
{
	if($item["SEPARATOR"] == true)
	{
		echo '<td><div class="separator"></div></td>';
	}
	else
	{
		$tooltip = ($item["TOOLTIP"] <> ''? '<div class="panel-tooltip"><a class="panel-tooltip-close" title="'.GetMessage("top_panel_updates_close").'" href="javascript:void(0)" onclick="'.$item["TOOLTIP_ACTION"].'jsUtils.FindParentObject(this, \'div\').style.display = \'none\';"></a>'.$item["TOOLTIP"].'</div>' : '');
		if($item["LINK"] <> "")
		{
			echo '<td>'.$tooltip.'<div><a href="'.$item["LINK"].'" hidefocus="true" title="'.$item["TITLE"].'" '.$item["LINK_PARAM"].' class="context-button'.($item["SELECTED"] == true? ' pressed':'').(!empty($item["ICON"])? ' icon'.($item["TEXT"] == ""? ' icon-only':'').'" id="'.$item["ICON"].'"':'"').'>'.$item["TEXT"].'</a></div></td>';
		}
		elseif(is_array($item["MENU"]) && !empty($item["MENU"]))
		{
			$bWasPopup = true;
			$sMenuUrl = "this.blur(); topPanelPopup.ShowMenu(this, ".htmlspecialchars(CAdminPopup::PhpToJavaScript($item["MENU"], $dummy)).", jsPanel.IsFixed());";
			echo '<td>'.$tooltip.'<div><a href="javascript:void(0)" hidefocus="true" onclick="'.$sMenuUrl.'" title="'.$item["TITLE"].'" class="context-button'.($item["SELECTED"] == true? ' pressed':'').(!empty($item["ICON"])? ' icon'.($item["TEXT"] == ""? ' icon-only':'').'" id="'.$item["ICON"].'"':'"').'">'.$item["TEXT"].'<img src="/bitrix/themes/'.ADMIN_THEME_ID.'/images/panel/arr_down.gif" class="arrow" alt=""></a></div></td>';
		}
		else
		{
			echo '<td>'.$tooltip.'<div title="'.$item["TITLE"].'" class="context-text'.(!empty($item["ICON"])? ' icon" id="'.$item["ICON"].'"':'"').'>'.$item["TEXT"].'</div></td>';
		}
	}
}
if(!defined("BX_AUTH_FORM") && $USER->IsAuthorized())
{
	$maxQuota = COption::GetOptionInt("main", "disk_space", 0)*1024*1024;
	if($maxQuota > 0)
	{
		$quota = new CDiskQuota();
		$free = $quota->GetDiskQuota();
		echo '
		<td><div class="separator"></div></td>
		<td>
			<table class="free-space-bar" cellspacing="1" title="'.GetMessage("top_panel_quota", array("#FREE#"=>round($free/(1024*1024)), "#QUOTA#"=>round($maxQuota/(1024*1024)))).'">
				<tr>
					<td class="used" style="width:'.(100 - round($free/$maxQuota*100)).'%;"><div class="empty"></div></td>
					<td class="free" style="width:'.round($free/$maxQuota*100).'%;"><div class="empty"></div></td>
				</tr>
			</table>
		</td>';
	}
}


if(!defined("BX_AUTH_FORM") && $USER->IsAuthorized() && CModule::IncludeModule("bizproc"))
{
	$arFilter = array("USER_ID" => $USER->GetID());
	$dbResultList = CBPTaskService::GetList(
			array($by => $order),
			$arFilter,
			false,
			false,
			array("ID")
		);

	if($dbResultList->Fetch())
	{
		echo '<td><div class="separator"></div></td>
		<td><a href="/bitrix/admin/bizproc_task_list.php?lang='.LANGUAGE_ID.'" hidefocus="true" title="'.GetMessage("top_panel_bizproc_title").'" class="context-button icon" id="top_panel_bizproc">'.GetMessage('top_panel_bizproc_tasks').'</a></td>';
	}
}
?>
		</tr>
	</table>
	</div>

<?if($bShowTabs):?>
		</td>
	</tr>
</table>
</div>
<?
$aUserOpt = CUserOptions::GetOption("admin_panel", "settings");
if($aUserOpt["fix"] == "on"):
?>
<script type="text/javascript">
jsPanel.FixOn();
</script>
<?endif;?>
<?endif?>

<?
if($USER->IsAuthorized())
{
	//start menu preload
	$aUserOptGlobal = CUserOptions::GetOption("global", "settings");
	if($aUserOptGlobal["start_menu_preload"] == 'Y')
		echo '<script type="text/javascript">jsUtils.addEvent(window, "load", function(){jsStartMenu.PreloadMenu();});</script>';
}

echo $GLOBALS["adminPage"]->ShowSound();
?>

<?
if($bWasPopup)
{
	$menu = new CAdminPopup("topPanelPopup", "topPanelPopup");
	$menu->Show();
}
?>