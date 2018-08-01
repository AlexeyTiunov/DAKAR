<?
$module_id = "fileman";
$dicsRelPath = '/bitrix/modules/fileman/dictionaries';
$gzDicsRelPath = BX_PERSONAL_ROOT.'/tmp/dics';
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/fileman/include.php");
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/admin/task_description.php");

CUtil::InitJSCore();

// save keys for Yandex maps - without rights check
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_REQUEST['save_map_key']))
{
	$APPLICATION->RestartBuffer();

	$key_type = $_REQUEST['key_type'];
	$key = $_REQUEST['key'];
	$domain = $_REQUEST['domain'];

	if ($key_type && $key && $domain && ($key_type == 'yandex'))
	{
		$strMapKeys = COPtion::GetOptionString('fileman', 'map_'.$key_type.'_keys', '');
		$arMapKeys = array();
		if ($strMapKeys != '')
		{
			$arMapKeys = unserialize($strMapKeys);
		}

		$arMapKeys[$domain] = $key;

		if (COption::SetOptionString('fileman', 'map_'.$key_type.'_keys', serialize($arMapKeys)))
			echo 'OK';
	}

	die();
}

if (!$USER->CanDoOperation('fileman_view_all_settings'))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

function isValidLang($lang)
{
	$rsLang = CLanguage::GetList($by="sort", $order="desc");
	$is_valid_lang = false;
	while ($arLang = $rsLang->Fetch())
	{
		if ($lang==$arLang["LID"])
		{
			$is_valid_lang = true;
			break;
		}
	}
	return $is_valid_lang;
}

if ($REQUEST_METHOD=="GET" && $USER->CanDoOperation('fileman_edit_all_settings') && strlen($RestoreDefaults)>0 && check_bitrix_sessid())
{
	COption::RemoveOption("fileman");
	$z = CGroup::GetList($v1="id",$v2="asc", array("ACTIVE" => "Y", "ADMIN" => "N"));
	while($zr = $z->Fetch())
		$APPLICATION->DelGroupRight($module_id, array($zr["ID"]));
}


global $MESS;
IncludeModuleLangFile(__FILE__);

//Default file extensions;
$script_files_default = "php,php3,php4,php5,php6,phtml,pl,asp,aspx,cgi,exe,ico,shtm,shtml";

if($REQUEST_METHOD=="POST" && strlen($Update)>0 && $USER->CanDoOperation('fileman_edit_all_settings') && check_bitrix_sessid())
{
	if($default_edit!="html" && $default_edit!="php")
		$default_edit="text";
	COption::SetOptionString($module_id, "default_edit", $default_edit);

	if($htmleditor_fullscreen!="Y")
		$htmleditor_fullscreen = "N";
	COption::SetOptionString($module_id, "htmleditor_fullscreen", $htmleditor_fullscreen);

	COption::SetOptionString($module_id, "show_untitled_styles", $show_untitled_styles);
	COption::SetOptionString($module_id, "render_styles_in_classlist", $render_styles_in_classlist);
	COption::SetOptionString($module_id, "allow_render_components", $allow_render_components == 'Y' ? 'Y' : 'N');
	COption::SetOptionString($module_id, "use_medialib", $use_medialib == 'Y' ? 'Y' : 'N');

	/* **********  Medialib ************/
	$cur_ml_width = COption::GetOptionInt($module_id, "ml_thumb_width", 140);
	$cur_ml_height = COption::GetOptionInt($module_id, "ml_thumb_height", 105);

	$ml_width = intval($medialib_thumb_width, 10);
	if ($ml_width <=0)
		$ml_width = 140;

	$ml_height = intval($medialib_thumb_height, 10);
	if ($ml_height <=0)
		$ml_height = 105;

	if (abs($cur_ml_width - $ml_width) > 10 || abs($cur_ml_height - $ml_height) > 10)
		CMedialib::DeleteThumbnails();

	COption::SetOptionInt($module_id, "ml_thumb_width", $ml_width);
	COption::SetOptionInt($module_id, "ml_thumb_height", $ml_height);

	$arMLExt = explode(',', $medialib_ext);
	$arMLExt_ = array();
	for ($i = 0, $l = count($arMLExt); $i < $l; $i++)
	{
		$ext = strtolower(trim($arMLExt[$i], ' .'));
		if (strlen($ext) > 0)
			$arMLExt_[] = $ext;
	}
	$medialib_ext = implode(',', $arMLExt_);
	COption::SetOptionString($module_id, "ml_media_extentions", $medialib_ext);

	/* Max size*/
	$ml_max_width = intval($medialib_max_width);
	if ($ml_max_width <=0)
		$ml_max_width = 1024;

	$ml_max_height = intval($medialib_max_height);
	if ($ml_max_height <=0)
		$ml_max_height = 1024;

	COption::SetOptionInt($module_id, "ml_max_width", $ml_max_width);
	COption::SetOptionInt($module_id, "ml_max_height", $ml_max_height);

	/* MEDIALIB TYPES*/
	$arMLTypes = array();
	$arMLDelTypes = array();
	$strAvExt = $medialib_ext;

	foreach ($_POST['ML_TYPE'] as $key => $type)
	{
		if ($type["DEL"] == "Y")
		{
			$arMLDelTypes[] = $key;
		}
		elseif(trim($type["EXT"]) != "" && trim($type["NAME"]) != "" && trim($type["CODE"]) != "")
		{
			$arMLTypes[] = array(
				'NEW' => $type["NEW"] == "Y",
				'ID' => $key,
				'NAME' => $type["NAME"],
				'CODE' => $type["CODE"],
				'EXT' => $type["EXT"],
				//'ICON' => $type["ICON"],
				'DESCRIPTION' => $type["DESC"],
				'SYSTEM' => $type["SYS"] == "Y" ? "Y" : "N"
			);

			$strAvExt .= ','.$type["EXT"];
		}
	}

	CMedialib::DelTypes($arMLDelTypes);
	CMedialib::SetTypes($arMLTypes);

	$arExt_ = explode(',', $strAvExt);
	$arAvExt = array();
	for ($i = 0, $l = count($arExt_); $i < $l; $i++)
	{
		$ext = strtolower(trim($arExt_[$i], ' .'));
		if (strlen($ext) > 0 && !in_array($ext, $arAvExt))
			$arAvExt[] = $ext;
	}
	$strAvExt = implode(',', $arAvExt);
	COption::SetOptionString($module_id, "ml_media_available_ext", $strAvExt);

	/* MEDIALIB END*/

	// Using medialib (or file dialog) by default in HTML-editor and other...
	COption::SetOptionString($module_id, "ml_use_default", $medialib_use_default == 'Y');

	if(is_dir($_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/FCKeditor'))
	{
		if($ext_html_editor!="not_pages" && $ext_html_editor!="not_php" && $ext_html_editor!="not_admin" && $ext_html_editor!="always")
		$ext_html_editor="";
		COption::SetOptionString($module_id, "ext_html_editor", $ext_html_editor);
	}

	//File extensions
	if ($USER->CanDoOperation('edit_php'))
	{
		COption::SetOptionString($module_id, "~script_files", $script_files);
		COption::SetOptionString($module_id, "~allowed_components", $allowed_components);
	}

	// LCA - limit component access
	COption::SetOptionString($module_id, "use_lca", ($use_lca == 'Y' ? 'Y' : 'N'));

	//Entities
	if (count($ar_entities) <= 0)
		$str_ar_entities = 'none';
	else
		$str_ar_entities = implode(',',$ar_entities);
	COption::SetOptionString($module_id, "ar_entities", $str_ar_entities);

	$siteList_ID = unserialize($mSiteList);

	if(isset($dif_settings))
	{
		COption::SetOptionString($module_id, "different_set", "Y");

		$j = 0;
		while($j < count($siteList_ID))
		{
			COption::SetOptionInt($module_id, "num_menu_param", ${"num_".$siteList_ID[$j]["ID"]."_menu_param"}, false, $siteList_ID[$j]["ID"]);

			$menutypes = "";
			$armt = Array();
			for($i=0; $i<${"menutypes_".$siteList_ID[$j]["ID"]."_count"}; $i++)
			{
				if(strlen(${"menutypes_".$siteList_ID[$j]["ID"]."_".$i."_type"})>0)
					$armt[${"menutypes_".$siteList_ID[$j]["ID"]."_".$i."_type"}] = ${"menutypes_".$siteList_ID[$j]["ID"]."_".$i."_name"};
			}

			if(strlen(${"menutypes_".$siteList_ID[$j]["ID"]."_new_type"})>0 && $USER->CanDoOperation('fileman_edit_menu_types'))
				$armt[${"menutypes_".$siteList_ID[$j]["ID"]."_new_type"}] = ${"menutypes_".$siteList_ID[$j]["ID"]."_new_name"};

			SetMenuTypes($armt, $siteList_ID[$j]["ID"]);

			$arPT = Array();
			for($i=0; $i<${"propstypes_".$siteList_ID[$j]["ID"]."_count"}; $i++)
			{
				if(strlen(${"propstypes_".$siteList_ID[$j]["ID"]."_".$i."_type"})>0)
					$arPT[${"propstypes_".$siteList_ID[$j]["ID"]."_".$i."_type"}] = ${"propstypes_".$siteList_ID[$j]["ID"]."_".$i."_name"};
			}
			if(strlen(${"propstypes_".$siteList_ID[$j]["ID"]."_new_type"})>0)
				$arPT[${"propstypes_".$siteList_ID[$j]["ID"]."_new_type"}] = ${"propstypes_".$siteList_ID[$j]["ID"]."_new_name"};

			CFileMan::SetPropstypes($arPT, false, $siteList_ID[$j]["ID"]);
			$j++;
		}
	}
	else
	{
		COption::SetOptionString($module_id, "different_set", "N");
		COption::SetOptionInt($module_id, "num_menu_param", $num_menu_param);

		$armt = Array();
		$menutypes = "";
		for($i=0; $i<$menutypes_count; $i++)
		{
			if(strlen(${"menutypes_".$i."_type"})>0)
				$armt[${"menutypes_".$i."_type"}] = ${"menutypes_".$i."_name"};
		}
		if(strlen($menutypes_new_type)>0 && $USER->CanDoOperation('fileman_edit_menu_types'))
			$armt[$menutypes_new_type] = $menutypes_new_name;

		SetMenuTypes($armt, '');

		$propstypes = "";
		$arPT = Array();
		for($i=0; $i<$propstypes_count; $i++)
		{
			if(strlen(${"propstypes_".$i."_type"})>0)
				$arPT[${"propstypes_".$i."_type"}] = ${"propstypes_".$i."_name"};
		}
		if(strlen($propstypes_new_type)>0)
			$arPT[$propstypes_new_type] = $propstypes_new_name;

		CFileMan::SetPropstypes($arPT);

		$j = 0;
		while($j < count($siteList_ID))
		{
			COption::RemoveOption($module_id, "menutypes", $siteList_ID[$j]["ID"]);
			COption::RemoveOption($module_id, "propstypes", $siteList_ID[$j]["ID"]);
			COption::RemoveOption($module_id, "num_menu_param", $siteList_ID[$j]["ID"]);
			$j++;
		}

	}

	// Search 
	$search_max_open_file_size = intVal($_POST['search_max_open_file_size']);
	if ($search_max_open_file_size <= 0)
		$search_max_open_file_size = 1024;
	COption::SetOptionString($module_id, "search_max_open_file_size", $search_max_open_file_size);

	$search_max_res_count = intVal($_POST['search_max_res_count']);
	if ($search_max_res_count <= 0)
		$search_max_res_count = '';
	COption::SetOptionString($module_id, "search_max_res_count", $search_max_res_count);

	$search_time_step = intVal($_POST['search_time_step']);
	if ($search_time_step <= 0)
		$search_time_step = 5;
	COption::SetOptionString($module_id, "search_time_step", $search_time_step);

	$search_mask = $_POST['search_mask'];
	if ($search_mask == "")
		$search_mask = "*.php";
	COption::SetOptionString($module_id, "search_mask", $search_mask);

	COption::SetOptionString($module_id, "show_inc_icons", (isset($_POST['show_inc_icons']) ? 'Y' : 'N'));
	COption::SetOptionString($module_id, "spell_check_first_client", (isset($_POST['spell_check_first_client']) ? 'Y' : 'N'));

	COption::SetOptionString($module_id, "hide_physical_struc", (isset($_POST['hide_physical_struc'])));

	if (isset($_POST['use_pspell']))
		COption::SetOptionString($module_id, "use_pspell", "Y");
	else
		COption::SetOptionString($module_id, "use_pspell", "N");


	if (isset($_POST['user_dics_path']) && $_POST['user_dics_path']!='')
		COption::SetOptionString($module_id, "user_dics_path", $_POST['user_dics_path']);
	else
		COption::SetOptionString($module_id, "user_dics_path","/bitrix/modules/fileman/u_dics");


	if (isset($_POST['use_separeted_dics']))
		COption::SetOptionString($module_id, "use_separeted_dics", "Y");
	else
		COption::SetOptionString($module_id, "use_separeted_dics", "N");

	COption::SetOptionString($module_id, "use_custom_spell", "N");


	//Handle dictionary loading
	if (isset($_POST['dic_lang']) && isset($_FILES['dic_aff']) && isset($_FILES['dic_base']) && $_FILES['dic_aff']['name'] != '' && 	$_FILES['dic_base']['name'] != '')
	{
		$dic_lang = $_POST['dic_lang'];
		if (isValidLang($dic_lang))
		{
			$lang_dir = $_SERVER['DOCUMENT_ROOT'].$dicsRelPath.'/'.$dic_lang;
			$dics_dir = $_SERVER['DOCUMENT_ROOT'].$dicsRelPath.'/'.$dic_lang.'/dics';

			if (!file_exists($lang_dir))
				mkdir($lang_dir, BX_DIR_PERMISSIONS);

				$source=$_FILES['dic_base']['tmp_name'];
				$target = $lang_dir.'/'.$dic_lang.'.dic';
				if (file_exists($target))
					unlink ($target);
				move_uploaded_file($source, $target);

				$source=$_FILES['dic_aff']['tmp_name'];
				$target = $lang_dir.'/'.$dic_lang.'.aff';
				if (file_exists($target))
					unlink ($target);
				move_uploaded_file($source, $target);

			if (!file_exists($dics_dir))
				mkdir($dics_dir, BX_DIR_PERMISSIONS);
			COption::SetOptionString($module_id, $dic_lang."_dic_indexed", "N");
		}
	}

	//Handle dictionary removing
	if (isset($_POST['del_dic']))
	{
		$lang_dir = $_SERVER['DOCUMENT_ROOT'].$dicsRelPath.'/'.$_POST['del_dic'];
		if (file_exists($lang_dir) && is_dir($lang_dir))
		{
			$dicDir = dir($lang_dir);
			while (false !== ($entry = $dicDir->read()))
			{
				$entry_path = $dicDir->path.'/'.$entry;
				if (is_dir($entry_path) && $entry=='dics')
				{
					//Removing files from 'dics' directory
					$dicsDir = dir($entry_path);
					while (false !== ($dic = $dicsDir->read()))
					{
						$dic_path = $dicsDir->path.'/'.$dic;
						if (is_file($dic_path))
							unlink ($dic_path);
					}
					$dicsDir->close();
					//removing 'dics' directory
					rmdir($entry_path);
				}
				elseif (is_file($entry_path))
				{
					unlink ($entry_path);
				}
			}
			$dicDir->close();
			rmdir($lang_dir);
		}
	}

	//Handle dictionary indexing
	if (isset($_POST['index_dic']))
	{
		$lang_dir = $_SERVER['DOCUMENT_ROOT'].$dicsRelPath.'/'.$_POST['index_dic'];
		if (file_exists($lang_dir) && is_dir($lang_dir))
		{
			$dicsDir = dir($lang_dir.'/dics');
			while (false !== ($dic = $dicsDir->read()))
			{
				$dic_path = $dicsDir->path.'/'.$dic;
				if (is_file($dic_path))
					unlink ($dic_path);
			}
			$dicsDir->close();

			require($_SERVER['DOCUMENT_ROOT'].BX_ROOT.'/modules/fileman/admin/spell_createDictionary.php');

			$CD = new createDictionary();
			$lang = $_POST['index_dic'];
			$CD->init($lang,$lang_dir);
			if ($CD->create())
				COption::SetOptionString($module_id, $dic_lang."_dic_indexed", "Y");
		}
	}

	$arMapTypes = array('yandex');
	foreach ($arMapTypes as $map_type)
	{
		$arKeys = array();
		if (isset($_POST['map_keys_'.$map_type]))
		{
			$arKeys = $_POST['map_keys_'.$map_type];
			foreach ($arKeys as $domain => $key)
			{
				$key = trim($key);
				if (strlen($key) <= 0)
					unset($arKeys[$domain]);
				else
					$arKeys[$domain] = $key;
			}
		}

		if ($_POST['map_domain_'.$map_type] && $_POST['map_key_'.$map_type])
			$arKeys[trim($_POST['map_domain_'.$map_type])] = $_POST[trim('map_key_'.$map_type)];

		$value = (count($arKeys) <= 0) ? '' : serialize($arKeys);
		COption::SetOptionString('fileman', 'map_'.$map_type.'_keys', $value);
	}
}


if ($REQUEST_METHOD=="GET" && isset($_GET['load_dic']) && $USER->CanDoOperation('fileman_edit_all_settings'))
{
	if (isValidLang($_GET['load_dic']))
	{
		$l_id = $_GET['load_dic'];
		require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/tar_gz.php");
		$indexedDicPath = $_SERVER['DOCUMENT_ROOT'].$gzDicsRelPath.'/'.$l_id.'.tar.gz';
		$oArchiver = new CArchiver($indexedDicPath);

		if ($oArchiver->extractFiles($_SERVER['DOCUMENT_ROOT'].$dicsRelPath.'/'.$l_id))
			COption::SetOptionString($module_id, $l_id."_dic_indexed", "Y");
	}
}



$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("MAIN_TAB_SET"), "ICON" => "fileman_settings", "TITLE" => GetMessage("MAIN_TAB_TITLE_SET")),
	array("DIV" => "edit2", "TAB" => GetMessage("MAIN_TAB_VISUAL_EDITOR"), "ICON" => "fileman_settings", "TITLE" => GetMessage("MAIN_TAB_TITLE_VIS_ED_SET")),
	array("DIV" => "edit5", "TAB" => GetMessage("MAIN_TAB_MEDIALIB"), "ICON" => "fileman_settings", "TITLE" => GetMessage("FILEMAN_SEC_MEDIALIB")),
	array("DIV" => "edit4", "TAB" => GetMessage("MAIN_TAB_MAP_KEYS"), "ICON" => "fileman_settings", "TITLE" => GetMessage("MAIN_TAB_TITLE_MAP_KEYS")),
	array("DIV" => "edit3", "TAB" => GetMessage("MAIN_TAB_RIGHTS"), "ICON" => "fileman_settings", "TITLE" => GetMessage("MAIN_TAB_TITLE_RIGHTS")),
	);

	$siteList = array();
	$rsSites = CSite::GetList($by="sort", $order="asc", Array());
	$i = 0;
	while($arRes = $rsSites->Fetch())
	{
		$siteList[$i]["ID"] = $arRes["ID"];
		$siteList[$i]["NAME"] = $arRes["NAME"];
		$i++;
	}
	$siteCount = $i;

	unset($rsSites);
	unset($arRes);

	$tabControl = new CAdmintabControl("tabControl", $aTabs);
	$tabControl->Begin();

?>


<script>
	function SelectSite(id)
	{
		<?for($i = 0; $i < $siteCount; $i++):?>
		BX('<?= htmlspecialchars($siteList[$i]["ID"]);?>_Propery').style.display='none';
		<?endfor;?>
		BX(id+'_Propery').style.display='';
	}

	function hideSite()
	{
		<?for($i = 0; $i < $siteCount; $i++):?>
		BX('<?= htmlspecialchars($siteList[$i]["ID"]);?>_Propery').style.display='none';
		<?endfor;?>
	}

	function showCustomSpellSettings(id)
	{
		var checker = BX(id);
		var customSpellSettings = BX('customSpellSettings');
		if (checker.checked)
		{
			customSpellSettings.style.display = "block";
		}
		else
		{
			customSpellSettings.style.display = "none";
		}
	}
</script>

<form method="POST" enctype="multipart/form-data" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialchars($mid)?>&lang=<?echo LANG?>">
<?=bitrix_sessid_post()?>
<?$tabControl->BeginNextTab();?>
<tr>
	<td valign="top" width="40%"><?echo GetMessage("FILEMAN_OPTION_DEF_EDITOR")?></td>
	<td valign="top" width="60%">

	<select name="default_edit">
		<option value="text"><?echo GetMessage("FILEMAN_OPTION_EDITOR_TEXT")?></option>
		<option value="php"<?if(COption::GetOptionString($module_id, "default_edit", "text")=="php")echo " selected"?>><?echo GetMessage("FILEMAN_OPTION_EDITOR_PHP")?></option>
		<option value="html"<?if(COption::GetOptionString($module_id, "default_edit", "text")=="html")echo " selected"?>><?echo GetMessage("FILEMAN_OPTION_EDITOR_HTML")?></option>
	</select>
	</td>
</tr>
	<? if (COption::GetOptionString('main', 'distributive6', 'N') != 'Y'):?>
	<tr>
		<td valign="top"><label for="show_inc_icons"><?echo GetMessage("FILEMAN_OPTION_MENU_SHOW_INC")?></label></td>
		<td><input type="checkbox" name="show_inc_icons" id="show_inc_icons" size="5" value="Y" <?if(COption::GetOptionString($module_id, "show_inc_icons", "Y")=="Y")echo " checked"?>></td>
	</tr>
	<?endif;?>
	<? if ($USER->CanDoOperation('edit_php')):?>
	<tr>
		<td>
			<?echo GetMessage("FILEMAN_OPTION_SCRIPT_FILES")?>:
		</td>
		<td>
			<input type="text" name="script_files" id="script_files" size="40" value="<?echo COption::GetOptionString($module_id, "~script_files", $script_files_default);?>">
		</td>
	</tr>
	<tr>
		<td valign="top">
			<?echo GetMessage("FILEMAN_OPTION_ALLOWED_COMPONENTS")?>:
		</td>
		<td>
			<textarea cols="30" rows="4" name="allowed_components"><?echo COption::GetOptionString($module_id, "~allowed_components", '');?></textarea>
		</td>
	</tr>
	<?endif;?>
	<tr>
	<td valign="top" width="40%"><label for="hide_physical_struc"><?echo GetMessage("FILEMAN_HIDE_PHYSICAL_STRUC")?>:</label></td>
	<td valign="top" width="60%">
	<input type="checkbox" name="hide_physical_struc" id="hide_physical_struc" <? if(COption::GetOptionString($module_id, "hide_physical_struc", false) == true) echo " checked";?>>
	</td>
</tr>
	<tr>
		<td colspan=2>&nbsp;</td>
	</tr>
	<tr class="heading">
		<td colspan=2><? echo GetMessage("FILEMAN_OPTION_SPECIAL_SETTINGS")?></td>
	</tr>
	<tr>
		<td><? echo GetMessage("FILEMAN_OPTION_DIFFERENT_SET")?></td>
		<td><input type="checkbox" name="dif_settings" id="dif_settings_id" onClick="if(this.checked) {  BX('comPropery').style.display='none'; BX('site_select_id').disabled=false; SelectSite(BX('site_select_id').value);} else { BX('site_select_id').disabled=true; BX('comPropery').style.display=''; hideSite();}" <? if(COption::GetOptionString($module_id, "different_set", "N") == "Y") echo " checked";?>></td>
	</tr>
	<tr>
		<td><?echo GetMessage("FILEMAN_OPTION_FOR_SYTE")?></td>
		<td>
			<select name="site_select" id="site_select_id" onChange="SelectSite(this.value)" <? if(COption::GetOptionString($module_id, "different_set", "N") != "Y") echo " disabled"; ?>>
			<?
				for($i = 0; $i < $siteCount; $i++)
					echo "<option value=\"".htmlspecialchars($siteList[$i]["ID"])."\">".htmlspecialchars($siteList[$i]["NAME"])."</option>";
			?>
			</select>
		</td>
	</tr>
	<tr id="comPropery" <? if(COption::GetOptionString($module_id, "different_set", "N") == "Y") echo " style=\"display: none;\""; ?>>
		<td colspan="2">
		<table cellspacing="4"  cellpadding="0" width="100%">
		<tr>
			<td valign="top" width="40%" class="field-name" style="{padding: 4px;}"><?echo GetMessage("FILEMAN_OPTION_MENU_TYPES")?></td>
			<td valign="top" width="60%">
			<table cellpadding="0" cellspacing="2" border="0" width="100%">
			<tr class="heading">
				<td align="center" width="40%"><b><?echo GetMessage("FILEMAN_OPTION_MENU_TYPE")?></b></td>
				<td align="center" width="60%"><b><?echo GetMessage("FILEMAN_OPTION_MENU_NAME")?></b></td>
			</tr>
			<?
			$armt = GetMenuTypes('', "left=".GetMessage("FILEMAN_OPTION_LEFT_MENU_NAME").",top=".GetMessage("FILEMAN_OPTION_TOP_MENU_NAME"));


			$i = 0;
			foreach($armt as $key => $title):
				if ($USER->CanDoOperation('fileman_edit_menu_types')):
				?>
					<tr>
						<td><input type="text" name="menutypes_<?echo $i?>_type" value="<?echo htmlspecialchars($key)?>" style="width:100%"></td>
						<td><input type="text" name="menutypes_<?echo $i?>_name" value="<?echo htmlspecialchars($title)?>" style="width:100%"></td>
					</tr>
				<?else:?>
				<tr>
					<td style="padding-left: 5px">
					<?echo htmlspecialchars($key)?>
					<input type="hidden" name="menutypes_<?echo $i?>_type" value="<?echo htmlspecialchars($key)?>">
					</td>
					<td  style="padding-left: 5px">
					<?echo htmlspecialchars($title)?>
					<input type="hidden" name="menutypes_<?echo $i?>_name" value="<?echo htmlspecialchars($title)?>">
					</td>
				</tr>
				<?
				endif;
				$i++;
			endforeach;
			?>
			<input type="hidden" name="menutypes_count" value="<?echo $i?>">
			<?if ($USER->CanDoOperation('fileman_edit_menu_types')):?>
			<tr>
				<td><input type="text" name="menutypes_new_type" value="" style="width:100%"></td>
				<td><input type="text" name="menutypes_new_name" value="" style="width:100%"></td>
			</tr>
			<?endif;?>
			</table>
			</td>
		</tr>
		<tr>
			<td valign="top" class="field-name" style="{padding: 4px;}"><?echo GetMessage("FILEMAN_OPTION_MENU_PARAMS")?></td>
			<td><input type="text" name="num_menu_param" size="5" value="<?echo COption::GetOptionInt($module_id, "num_menu_param", 1, "")?>"></td>
		</tr>
		<tr>
			<td valign="top" class="field-name" style="{padding: 4px;}"><?echo GetMessage("FILEMAN_OPTION_PROPS_TYPES")?></td>
			<td valign="top">
			<table cellpadding="0" cellspacing="2" border="0" width="100%">
			<tr class="heading">
				<td align="center" width="40%"><b><?echo GetMessage("FILEMAN_OPTION_PROPS_TYPE")?></b></td>
				<td align="center" width="60%"><b><?echo GetMessage("FILEMAN_OPTION_PROPS_NAME")?></b></td>
			</tr>
			<?

			$i = 0;
			foreach (CFileMan::GetPropstypes('') as $key => $val)
			{
				?>
			<tr>
				<td><input type="text" name="propstypes_<?echo $i?>_type" value="<?echo htmlspecialchars($key)?>" style="width:100%"></td>
				<td><input type="text" name="propstypes_<?echo $i?>_name" value="<?echo htmlspecialchars($val)?>" style="width:100%"></td>
			</tr>
			<?
				$i++;
			}
			?>
			<input type="hidden" name="propstypes_count" value="<?echo $i+1;?>">
			<tr>
				<td><input type="text" name="propstypes_new_type" value="" style="width:100%"></td>
				<td><input type="text" name="propstypes_new_name" value="" style="width:100%"></td>
			</tr>
			</table>
			</td>
			</tr>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	<input type="hidden" name="mSiteList" value="<?=htmlspecialchars(serialize($siteList))?>">
	<?
	for($j = 0; $j < $siteCount; $j++)
	{
	?>
	<tr id="<?= htmlspecialchars($siteList[$j]["ID"])?>_Propery" style="<? if(((COption::GetOptionString($module_id, "different_set", "N") == "Y") && ($j != 0)) || (COption::GetOptionString($module_id, "different_set", "N") == "N")) echo "display: none;"?>">
		<td colspan="2">
		<table cellspacing="4" cellpadding="0" width="100%">
		<tr>
			<td valign="top" width="40%" class="field-name" style="{padding: 4px;}"><?echo GetMessage("FILEMAN_OPTION_MENU_TYPES")?></td>
			<td valign="top" width="60%">
			<table cellpadding="0" cellspacing="2" border="0" width="100%">
			<tr class="heading">
				<td align="center" width="40%"><b><?echo GetMessage("FILEMAN_OPTION_MENU_TYPE")?></b></td>
				<td align="center" width="60%"><b><?echo GetMessage("FILEMAN_OPTION_MENU_NAME")?></b></td>
			</tr>
			<?
			$armt = GetMenuTypes($siteList[$j]["ID"], "left=".GetMessage("FILEMAN_OPTION_LEFT_MENU_NAME").",top=".GetMessage("FILEMAN_OPTION_TOP_MENU_NAME"));

			$i = 0;
			foreach($armt as $key => $title):
				if ($USER->CanDoOperation('fileman_edit_menu_types')):
				?>
			<tr>
				<td><input type="text" name="menutypes_<?= htmlspecialchars($siteList[$j]["ID"])?>_<?echo $i?>_type" value="<?echo htmlspecialchars($key)?>" style="width:100%"></td>
				<td><input type="text" name="menutypes_<?= htmlspecialchars($siteList[$j]["ID"])?>_<?echo $i?>_name" value="<?echo htmlspecialchars($title)?>" style="width:100%"></td>
			</tr>
				<?else:?>
			<tr>
				<td>
				<?echo htmlspecialchars($key)?>
				<input type="hidden" name="menutypes_<?= htmlspecialchars($siteList[$j]["ID"])?>_<?echo $i?>_type" value="<?echo htmlspecialchars($key)?>">
				</td>
				<td>
				<?echo htmlspecialchars($title)?>
				<input type="hidden" name="menutypes_<?= htmlspecialchars($siteList[$j]["ID"])?>_<?echo $i?>_name" value="<?echo htmlspecialchars($title)?>">
				</td>
			</tr>
				<?
				endif;
				$i++;
			endforeach;
			?>
			<input type="hidden" name="menutypes_<?= htmlspecialchars($siteList[$j]["ID"])?>_count" value="<?echo $i?>">
			<?if($USER->CanDoOperation('fileman_edit_menu_types')):?>
			<tr>
				<td><input type="text" name="menutypes_<?= htmlspecialchars($siteList[$j]["ID"])?>_new_type" value="" style="width:100%"></td>
				<td><input type="text" name="menutypes_<?= htmlspecialchars($siteList[$j]["ID"])?>_new_name" value="" style="width:100%"></td>
			</tr>
			<?endif;?>
			</table>
			</td>
		</tr>
		<tr>
			<td valign="top" class="field-name" style="{padding: 4px;}"><?echo GetMessage("FILEMAN_OPTION_MENU_PARAMS")?></td>
			<td><input type="text" name="num_<?= htmlspecialchars($siteList[$j]["ID"])?>_menu_param" size="5" value="<?echo COption::GetOptionInt($module_id, "num_menu_param", 1, $siteList[$j]["ID"])?>"></td>
		</tr>
		<tr>
			<td valign="top" class="field-name" style="{padding: 4px;}"><?echo GetMessage("FILEMAN_OPTION_PROPS_TYPES")?></td>
			<td valign="top">
			<table cellpadding="0" cellspacing="2" border="0" width="100%">
			<tr class="heading">
				<td align="center" width="40%"><b><?echo GetMessage("FILEMAN_OPTION_PROPS_TYPE")?></b></td>
				<td align="center" width="60%"><b><?echo GetMessage("FILEMAN_OPTION_PROPS_NAME")?></b></td>
			</tr>
			<?

			$i = 0;
			foreach (CFileMan::GetPropstypes($siteList[$j]["ID"]) as $key => $val)
			{?>
			<tr>
				<td><input type="text" name="propstypes_<?= htmlspecialchars($siteList[$j]["ID"])?>_<?echo $i?>_type" value="<?echo htmlspecialchars($key)?>" style="width:100%"></td>
				<td><input type="text" name="propstypes_<?= htmlspecialchars($siteList[$j]["ID"])?>_<?echo $i?>_name" value="<?echo htmlspecialchars($val)?>" style="width:100%"></td>
			</tr>
			<?
				$i++;
			}
			?>
			<input type="hidden" name="propstypes_<?= htmlspecialchars($siteList[$j]["ID"])?>_count" value="<?echo $i+1?>">
			<tr>
				<td><input type="text" name="propstypes_<?= htmlspecialchars($siteList[$j]["ID"])?>_new_type" value="" style="width:100%"></td>
				<td><input type="text" name="propstypes_<?= htmlspecialchars($siteList[$j]["ID"])?>_new_name" value="" style="width:100%"></td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
	</td>
	</tr>

	<? } ?>
	<tr class="heading">
		<td colspan="2"><?= GetMessage("FILEMAN_SEARCH_TITLE")?></td>
	</tr>
	<tr>
		<td><label for="search_max_open_file_size"><?= GetMessage("FILEMAN_SEARCH_MAX_FILE_SIZE")?>:</label></td>
		<td><input type="text" name="search_max_open_file_size" id="search_max_open_file_size" value="<?= COption::GetOptionString($module_id, "search_max_open_file_size", "1024")?>"><?= GetMessage("FILEMAN_SEARCH_KB")?></td>
	</tr>
	<tr>
		<td><label for="search_max_res_count"><?= GetMessage("FILEMAN_SEARCH_MAX_RES_CNT")?>:</label></td>
		<?
		$val = COption::GetOptionString($module_id, "search_max_res_count", "");
		$def_val = ' - '.GetMessage('FILEMAN_SEARCH_NO_LIMITS').' -';
		?>
		<td><input type="text" name="search_max_res_count" id="search_max_res_count" value="<? echo $val != "" ? $val : $def_val; ?>" <? if ($val == ""): ?>class="def-val"<?endif;?> onfocus="if (this.value == '<?= $def_val?>' || this.value == ''){this.value = ''; BX.removeClass(this, 'def-val');}" onblur="if (this.value == ''){this.value = '<?= $def_val?>'; BX.addClass(this, 'def-val');}" />
		</td>
	</tr>
	<tr>
		<td><label for="search_time_step"><?= GetMessage("FILEMAN_SEARCH_TIME_STEP")?>:</label></td>
		<td><input type="text" name="search_time_step" id="search_time_step" value="<?= COption::GetOptionString($module_id, "search_time_step", "5"); ?>"></td>
	</tr>
	<tr>
		<td><label for="search_mask"><?= GetMessage("FILEMAN_SEARCH_MASK_DEF")?>:</label></td>
		<td><input type="text" name="search_mask" id="search_mask" value="<?= COption::GetOptionString($module_id, "search_mask", "*.php"); ?>"></td>
	</tr>
<?$tabControl->BeginNextTab();?>
<tr>
	<td valign="top"><label for="show_untitled_styles"><?echo GetMessage("FILEMAN_OPTION_USE_ONLY_DEFINED_STYLES")?></label></td>
	<td><input type="checkbox" name="show_untitled_styles" id="show_untitled_styles" value="Y" <?if(COption::GetOptionString($module_id, "show_untitled_styles", "N")=="Y")echo " checked"?>></td>
</tr>
<tr>
	<td valign="top"><label for="render_styles_in_classlist"><?echo GetMessage("FILEMAN_OPTION_RENDER_CLASSLIST_STYLE")?>:</label></td>
	<td><input type="checkbox" name="render_styles_in_classlist" id="render_styles_in_classlist" value="Y" <?if(COption::GetOptionString($module_id, "render_styles_in_classlist", "N") == "Y") echo " checked"?>></td>
</tr>
<tr>
	<td valign="top"><label for="htmleditor_fullscreen"><?echo GetMessage("FILEMAN_OPT_FULLSCREEN")?></label></td>
	<td><input type="checkbox" name="htmleditor_fullscreen" id="htmleditor_fullscreen" value="Y" <?if(COption::GetOptionString($module_id, "htmleditor_fullscreen", "N")=="Y")echo " checked"?>></td>
</tr>
<tr>
	<td valign="top"><label for="allow_render_components"><?echo GetMessage("FILEMAN_OPT_ALLOW_RENDER_COMPONENTS")?>:</label></td>
	<td><input type="checkbox" name="allow_render_components" id="allow_render_components" value="Y" <?if(COption::GetOptionString($module_id, "allow_render_components", "N") == "Y") echo " checked"?>></td>
</tr>

<?if(is_dir($_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/FCKeditor')):?>
<tr>
	<td valign="top"><?echo GetMessage("FILEMAN_OPTION_USE_FCK")?></td>
	<td valign="top">

	<select name="ext_html_editor" onchange="BX('htmleditor_fullscreen').disabled = (this.value=='always')">
		<option value=""><?echo GetMessage("FILEMAN_OPTION_USE_FCK_NOT")?></option>
		<option value="not_pages"<?if(COption::GetOptionString($module_id, "ext_html_editor", "")=="not_pages")echo " selected"?>><?echo GetMessage("FILEMAN_OPTION_USE_FCK_NOT_PAGES")?></option>
		<option value="not_php"<?if(COption::GetOptionString($module_id, "ext_html_editor", "")=="not_php")echo " selected"?>><?echo GetMessage("FILEMAN_OPTION_USE_FCK_NOT_PHP")?></option>
		<option value="not_admin"<?if(COption::GetOptionString($module_id, "ext_html_editor", "")=="not_admin")echo " selected"?>><?echo GetMessage("FILEMAN_OPTION_USE_FCK_NOT_ADMIN")?></option>
		<option value="always"<?if(COption::GetOptionString($module_id, "ext_html_editor", "")=="always")echo " selected"?>><?echo GetMessage("FILEMAN_OPTION_USE_FCK_ALWAYS")?></option>
	</select>

	</td>
</tr>
<?endif;?>
	<tr class="heading">
		<td colspan="2"><? echo GetMessage("FILEMAN_EDITOR_CONVERT_SETTINGS");?></td>
	</tr>
		<td width="50%" valign="top"><label for='use_lca'><?echo GetMessage("FILEMAN_USE_LCA");?>:</td>
		<td  valign="top">
			<input type="checkbox" name="use_lca" id='use_lca' value="Y" <? if (COption::GetOptionString($module_id, "use_lca", 'N') == 'Y') echo 'checked';?>>
		</td>
	<tr>
		<td width="50%" valign="top"><?echo GetMessage("FILEMAN_ENTITIES_GROUPS");?>:</td>
		<td  valign="top">
			<?
			$opt = COption::GetOptionString($module_id, "ar_entities", 'umlya,greek,other');
			if ($opt == 'none')
				$ar_entities = array();
			else
				$ar_entities = explode(',', $opt);
			?>
			<table border="0" style="width:100%">
			<tr>
				<td><input type="checkbox" name="ar_entities[]" id='ent_umlya' value="umlya" <? if(in_array('umlya',$ar_entities)) echo 'checked';?>></td>
				<td><label for='ent_umlya'><?echo GetMessage("FILEMAN_ENTITIES_UMLYA");?></label></td>
			</tr>
			<tr>
				<td><input type="checkbox" name="ar_entities[]" id="ent_greek" value="greek" <? if(in_array('greek',$ar_entities)) echo 'checked';?>></td>
				<td><label for='ent_greek'><?echo GetMessage("FILEMAN_ENTITIES_GREEK");?></label></td>
			</tr>
			<tr>
				<td><input type="checkbox" name="ar_entities[]" value="other" id="ent_other" <? if(in_array('other',$ar_entities)) echo 'checked';?>></td>
				<td><label for='ent_other'><?echo GetMessage("FILEMAN_ENTITIES_OTHER");?></label></td>
			</tr>
			</table>
		</td>
	</tr>
	<tr class="heading">
		<td colspan="2"><? echo GetMessage("FILEMAN_OPTION_SPELL_SET");?></td>
	</tr>
	<tr>
		<td width="50%"><label for="spell_check_first_client"><?echo GetMessage("FILEMAN_OPTION_FIRST_SPELL_CLIENT");?></label></td>
		<td>
			<input type="checkbox" name="spell_check_first_client" id="spell_check_first_client" value="Y" <?echo (COption::GetOptionString($module_id, "spell_check_first_client", "Y")=="Y") ? 'checked' : '';?>>
		</td>
	</tr>

	<?
	if (function_exists('pspell_config_create')):
		$use_pspell_checked = (COption::GetOptionString($module_id, "use_pspell", "Y")=="Y") ? "checked" : "";

	?>
	<tr>
		<td valign="top"><label for="use_pspell"><?echo GetMessage("FILEMAN_OPTION_USE_PSPELL");?></label><br>
						 <a title="<?echo GetMessage("FILEMAN_OPTION_ADDISH_DICS_TITLE");?>" href="http://aspell.sourceforge.net/" target="blank"><?echo GetMessage("FILEMAN_OPTION_ADDISH_DICS");?></a><br>
		</td>
		<td>
			<input type="checkbox" name="use_pspell" id="use_pspell" value="Y" <?echo $use_pspell_checked;?>>
		</td>
	</tr>
	<tr>
		<td><? echo GetMessage("FILEMAN_OPTION_USER_DIC_DIR");?></td>
		<td>
			<input type="text" name="user_dics_path" style="width: 100%" value="<? echo COption::GetOptionString($module_id, "user_dics_path", "/bitrix/modules/fileman/u_dics")?>">
		</td>
	</tr>
	<tr>
		<td><label for="use_separeted_dics"><?echo GetMessage("FILEMAN_OPTION_USE_SEP_DICS");?></label></td>
		<td>
			<input type="checkbox" name="use_separeted_dics" id="use_separeted_dics" value="Y" <?echo (COption::GetOptionString($module_id, "use_separeted_dics", "Y")=="Y") ? "checked" : "";?>>
		</td>
	</tr>
	<?else:
			COption::SetOptionString($module_id, "use_pspell", "N");
	?>
	<tr>
		<td valign="top"><?echo GetMessage("FILEMAN_OPTION_USE_PSPELL");?><br>
			 <a title="<?echo GetMessage("FILEMAN_OPTION_INSTALL_PSPELL_TITLE");?>" href="http://php.net/manual/en/ref.pspell.php" target="blank"><?echo GetMessage("FILEMAN_OPTION_INSTALL_PSPELL");?></a><br>
			 <a title="<?echo GetMessage("FILEMAN_OPTION_ADDISH_DICS_TITLE");?>" href="http://aspell.sourceforge.net/" target="blank"><?echo GetMessage("FILEMAN_OPTION_ADDISH_DICS");?></a><br>
		</td>
		<td valign="top">
			<?echo GetMessage("FILEMAN_OPTION_NOT_INSTALLED");?>
		</td>
	</tr>
	<?endif;?>

<?
/* MEDIALIB TAB*/
$tabControl->BeginNextTab();

function _MLGetTypeHTML($type = array())
{
	$name = "ML_TYPE[".$type["id"]."]";
	ob_start();
?>
<div class="bx-ml-type"  id="type_cont_<?= $type["id"]?>">
<div class="bx-ml-type-label">
	<?if ($type["b_new"]):?>
		<input type="hidden" name="<?= $name."[NEW]"?>" value="Y" />
	<?endif;?>

	<? /* <input id="type_real_name_inp_<?= $type["id"]?>" type="hidden" name="<?= $name."[NAME]"?>" value="<?= $type["name"]?>" />  */?>
	<input type="hidden" name="<?= $name."[SYS]"?>" value="<?= $type["system"] ? "Y" : "N"?>" />

	<? if($type["system"]):?>
		<div><?= htmlspecialcharsex($type["name"])?></div>
	<? else:?>
		<div id="type_name_<?= $type["id"]?>" class="bx-ml-editable"><?= htmlspecialcharsex($type["name"])?></div>
		<? /*<input id="type_name_inp_<?= $type["id"]?>" type="text" size="30" value="<?= $type["name"]?>" style="display: none;" /> */ ?>
	<?endif;?>

	<? if($type["code"] != "image" || !$type["system"]):?>
		<a  id="type_del_<?= $type["id"]?>" class="bx-ml-type-del" href="javascript:void(0);"><?= GetMessage("FILEMAN_OPTION_DELETE")?></a>
	<?endif;?>
</div>

<? if($type["code"] != "image" || !$type["system"]):?>
	<div class="bx-ml-type-label-deleted">
	<input id="type_empty_<?= $type["id"]?>" type="hidden"  value="<?= $type['empty'] ? 'Y' : 'N'?>" />
	<input id="type_del_inp_<?= $type["id"]?>" type="hidden"  name="<?= $name."[DEL]"?>" value="N" />
	<div id="type_del_name_<?= $type["id"]?>"><?= htmlspecialcharsex($type["name"])?></div>
	<a  id="type_restore_<?= $type["id"]?>" class="bx-ml-type-restore" href="javascript:void(0);"><?= GetMessage("FILEMAN_ML_TYPE_RESTORE")?></a>
</div>
<?endif;?>

<div class="bx-ml-type-params">
	<table border="0">
		<tr><td class="bx-ml-td-left">
			<? if(!$type["system"]):?><span class="required">*</span><?endif;?><label for="type_name_inp_<?= $type["id"]?>"><?= GetMessage('FILEMAN_OPTION_PROPS_NAME')?>:</label>
		</td><td>
			<? if($type["system"]):?>
				<span class="bx-sys-value"><?= htmlspecialcharsex($type["name"])?></span>
				<input type="hidden" id="type_name_inp_<?= $type["id"]?>" value="<?= $type["name"]?>" />
			<? else:?>
				<input type="text"  name="<?= $name."[NAME]"?>" id="type_name_inp_<?= $type["id"]?>" value="<?= $type["name"]?>" size="40" />
			<?endif;?>
		</td></tr>

		<tr><td class="bx-ml-td-left">
			<input type="hidden" name="<?= $name."[CODE]"?>" value="<?= $type["code"]?>" />
			<? if(!$type["system"]):?><span class="required">*</span><?endif;?><label for="type_code_inp_<?= $type["id"]?>"><?= GetMessage('FILEMAN_ML_ADD_TYPE_CODE')?><? if(!$type["system"]):?><span class="required"><sup>1</sup></span><?endif;?>:</label>
		</td><td>
			<? if($type["system"]):?>
				<span class="bx-sys-value"><?= htmlspecialcharsex($type["code"])?></span>
			<? else:?>
				<input type="text"  name="<?= $name."[CODE]"?>" id="type_code_inp_<?= $type["id"]?>" value="<?= $type["code"]?>" size="40" />
			<?endif;?>
		</td></tr>

		<tr><td class="bx-ml-td-left">
			<span class="required">*</span><label for="type_ext_inp_<?= $type["id"]?>"><?= GetMessage('FILEMAN_ML_ADD_TYPE_EXT')?><span class="required"><sup>1</sup></span>:</label>
		</td><td>
			<input type="text" name="<?= $name."[EXT]"?>" id="type_ext_inp_<?= $type["id"]?>" value="<?= $type["ext"]?>" size="40" />
		</td></tr>
		<tr><td class="bx-ml-td-left">
			<label for="type_desc_inp_<?= $type["id"]?>"><?= GetMessage('FILEMAN_ML_ADD_TYPE_DESC')?>:</label>
		</td><td style="height: 50px;">
			<? if($type["system"]):?>
				<input name="<?= $name."[DESC]"?>" type="hidden" value="<?= htmlspecialchars($type["desc"])?>" />
				<span class="bx-sys-value bx-desc-view"><?= $type["desc"]?></span>
			<? else:?>
				<textarea name="<?= $name."[DESC]"?>" id="type_desc_inp_<?= $type["id"]?>" style="width: 260px;" rows="2" cols="30"><?= htmlspecialchars($type["desc"])?></textarea>
			<?endif;?>
		</td></tr>
	</table>
</div>
</div>
<?
	$s = ob_get_contents();
	ob_end_clean();
	return $s;
}
?>

	<tr>
		<?
		$useML = (COption::GetOptionString($module_id, "use_medialib", "Y") == "Y");
		$displ = $useML ? '' : 'style="display:none;"';
		?>
		<td>
		<label for="use_medialib"><?= GetMessage("FILEMAN_OPTION_USE_MEDIALIB")?>:</label></td>
		<td><input type="checkbox" name="use_medialib" id="use_medialib" value="Y" <?if($useML) echo " checked";?> onclick="BX('edit5_edit_table').className = 'edit-table' + (this.checked ? '' : ' edit-table-ml-hidden');">
		<img src="/bitrix/images/1.gif" style="width: 110px; height: 1px;" />
		</td>
	</tr>
	<tr <?= $displ?> class="bx-ml-hidden-row">
		<td><label for="medialib_thumb_width"><?= GetMessage("FILEMAN_MEDIALIB_THUMB_SIZE")?>:</label></td>
		<td><input type="text" name="medialib_thumb_width" id="medialib_thumb_width" value="<?= COption::GetOptionInt($module_id, "ml_thumb_width", 140)?>" size="6"/> x <input type="text" name="medialib_thumb_height" value="<?= COption::GetOptionInt($module_id, "ml_thumb_height", 105)?>" size="6"/></td>
	</tr>
	<tr <?= $displ?> class="bx-ml-hidden-row">
		<td><label for="medialib_ext"><?= GetMessage("FILEMAN_MEDIA_EXT")?>:</label></td>
		<td><input type="text" value="<?= COption::GetOptionString($module_id, "ml_media_extentions", CMedialib::GetDefaultMediaExtentions())?>" size="40" id="medialib_ext" name="medialib_ext"/></td>
	</tr>
	<tr <?= $displ?> class="bx-ml-hidden-row">
		<td><label for="medialib_max_width"><?= GetMessage("FILEMAN_MEDIALIB_MAX_SIZE")?>:</label></td>
		<td><input type="text" name="medialib_max_width" id="medialib_max_width" value="<?= COption::GetOptionInt($module_id, "ml_max_width", 1024)?>" size="6"/> x <input type="text" name="medialib_max_height" value="<?= COption::GetOptionInt($module_id, "ml_max_height", 1024)?>" size="6"/></td>
	</tr>
	<tr <?= $displ?> class="bx-ml-hidden-row">
		<td><label for="medialib_use_default"><?= GetMessage("FILEMAN_MEDIA_USE_DEF")?>:</label></td>
		<td><input type="checkbox" value="Y" <?if(COption::GetOptionString($module_id, "ml_use_default", true)) echo " checked";?> id="medialib_use_default" name="medialib_use_default"/></td>
	</tr>

<?
CMedialib::Init();
$arMLTypes = CMedialib::GetTypes(array(), true);
$maxCount = 0;
?>
	<tr class="heading bx-ml-hidden-row" <?= $displ?>>
		<td colspan=2><?= GetMessage("FILEMAN_SEC_MEDIALIB_TYPES")?></td>
	</tr>
	<tr <?= $displ?> class="bx-ml-hidden-row"><td colspan="2" align="center">

		<table id="bxml_type_tbl">
		<?for ($i = 0, $l = count($arMLTypes); $i < $l; $i++):?>
			<tr><td>
			<?= _MLGetTypeHTML($arMLTypes[$i]);?>
			<?
			if ($maxCount <= $arMLTypes[$i]['id'])
				$maxCount = $arMLTypes[$i]['id'] + 1;
			?>
			</td></tr>
		<?endfor;?>
			<tr><td align="right">
				<input onclick="addType();" type="button" value="<?= GetMessage("FILEMAN_ML_ADD_TYPE")?> >>" title="<?= GetMessage("FILEMAN_ML_ADD_TYPE_TITLE")?>" />
			</td></tr>
			<tr><td align="left">
				<?= BeginNote();?>
				<span class="required">*</span><?= GetMessage("REQUIRED_FIELDS")?><br>
				<span class="required"><sup>1</sup></span><?= GetMessage("FILEMAN_ONLY_LATIN")?><br>
				<?= EndNote();?>
			</td></tr>
		</table>

<script>
window.onload = function(){setTimeout(function()
{
	window.oMLSet = {
		pTypeTbl: BX("bxml_type_tbl"),
		curCount: <?= $maxCount?>
	};

	<?for ($i = 0, $l = count($arMLTypes); $i < $l; $i++):?>
		InitEventForType('<?= $arMLTypes[$i]['id']?>');
	<?endfor;?>
},
50);};

function addType()
{
	var id = window.oMLSet.curCount++;
	var newCell = window.oMLSet.pTypeTbl.insertRow(window.oMLSet.pTypeTbl.rows.length - 1).insertCell(-1);
	var typeHtml = '<?= CUtil::JSEscape(_MLGetTypeHTML(array("id" => "tmp_ml_type_id", "name" => "", "code" => "", "ext" => "", "icon" => "", "desc" => "", "b_new" => true, "empty" => true)));?>';

	// Replace id, and increase "curCount"
	typeHtml = typeHtml.replace(/tmp_ml_type_id/ig, id);

	var code = [], start, end, i, cnt;
	while((start = typeHtml.indexOf('<' + 'script>')) != -1)
	{
		var end = typeHtml.indexOf('</' + 'script>', start);
		if(end == -1)
			break;
		code[code.length] = typeHtml.substr(start + 8, end - start - 8);
		typeHtml = typeHtml.substr(0, start) + typeHtml.substr(end + 9);
	}

	for(var i = 0, cnt = code.length; i < cnt; i++)
		if(code[i] != '')
			jsUtils.EvalGlobal(code[i]);
	newCell.innerHTML = typeHtml;

	setTimeout(function(){InitEventForType(id);}, 50);
}

function InitEventForType(id)
{
	var oType =
	{
		pCont: BX('type_cont_' + id),
		pName: BX('type_name_' + id),
		pNameInp: BX('type_name_inp_' + id),
		pDelLink: BX('type_del_' + id),
		pRestoreLink: BX('type_restore_' + id),
		pDelInput: BX('type_del_inp_' + id),
		pEmpty: BX('type_empty_' + id)
	};

	if (oType.pName && oType.pNameInp)
	{
		oType.pNameInp.onkeyup = function()
		{
			while (oType.pName.firstChild)
				oType.pName.removeChild(oType.pName.firstChild);

			oType.pName.appendChild(document.createTextNode(oType.pNameInp.value));
		};

		if (oType.pNameInp.value == "")
		{
			oType.pNameInp.value = "<?= GetMessage("FILEMAN_ML_ADD_TYPE_NEW")?>";
			oType.pName.innerHTML = "<?= GetMessage("FILEMAN_ML_ADD_TYPE_NEW")?>";
			oType.pNameInp.focus();
			oType.pNameInp.select();
		}
	}

	if (oType.pDelLink)
	{
		oType.pDelLink.onclick = function()
		{
			if (oType.pEmpty.value == "N")
				return alert("<?= GetMessage("FILEMAN_ML_TYPE_CANT_DEL")?>");

			oType.pCont.className = "bx-ml-type bx-ml-type-deleted";

			if (!oType.pDelName)
				oType.pDelName = BX("type_del_name_" + id);

			while (oType.pDelName.firstChild)
				oType.pDelName.removeChild(oType.pDelName.firstChild);

			oType.pDelName.appendChild(document.createTextNode(oType.pNameInp.value));
			oType.pDelInput.value = "Y";
		};
	}

	if (oType.pRestoreLink)
	{
		oType.pRestoreLink.onclick = function()
		{
			oType.pCont.className = "bx-ml-type";
			oType.pDelInput.value = "N";
		};
	}
}

</script>
	</td></tr>

<?
// MAP
$tabControl->BeginNextTab();

$arMaps = array('yandex');
foreach ($arMaps as $map_type)
{
?>
	<tr class="heading">
		<td colspan="2">
			<?echo GetMessage("FILEMAN_OPTION_".ToUpper($map_type));?>
		</td>
	</tr>
<?
	$strMapKeys = COPtion::GetOptionString('fileman', 'map_'.$map_type.'_keys', '');

	$arKeys = array();
	if ($strMapKeys != '')
	{
		$arKeys = unserialize($strMapKeys);
	}

	if (is_array($arKeys) && count($arKeys) > 0)
	{
		foreach ($arKeys as $domain => $key)
		{
?>
			<tr>
				<td width="40%"><?echo htmlspecialchars($domain)?>: </td>
				<td width="60%"><input type="text" size="50" name="map_keys_<?echo $map_type?>[<?echo htmlspecialchars($domain)?>]" value="<?echo htmlspecialchars($key)?>" /></td>
			</tr>
<?
		}
	}
	else
	{
?>
	<tr>
		<td align="center" colspan="2"><?echo BeginNote(),GetMessage('FILEMAN_OPTION_KEYS_NOKEYS'),EndNote();?></td>
	</tr>
<?
	}
?>
	<tr><td colspan="2"><table width="50%" align="center">
		<tr class="heading"><td width="30%"><?echo GetMessage('FILEMAN_OPTION_KEYS_DOMAIN')?></td><td width="70%"><?echo GetMessage('FILEMAN_OPTION_KEYS_KEY')?></td></tr>
		<tr>
			<td><input type="text" style="width: 100%;" name="map_domain_<?echo $map_type?>" /></td>
			<td><input type="text" style="width: 100%;" name="map_key_<?echo $map_type?>" value="" /></td>
		</tr>
	</table></td></tr>
<?
}
?>
<?$tabControl->BeginNextTab();?>
<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights2.php");?>
<?$tabControl->Buttons();?>
<script>
	function RestoreDefaults()
	{
		if(confirm('<?echo AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>'))
			window.location = "<?echo $APPLICATION->GetCurPage()?>?RestoreDefaults=Y&lang=<?echo LANG?>&mid=<?echo urlencode($mid)?>&<?=bitrix_sessid_get()?>";
	}
</script>
<input type="submit" <?if (!$USER->CanDoOperation('fileman_edit_all_settings')) echo "disabled" ?> name="Update" value="<?echo GetMessage("FILEMAN_OPTION_SAVE")?>">
<input type="reset" name="reset" onClick="BX('site_select_id').disabled=<? if(COption::GetOptionString($module_id, "different_set", "N") != "Y") echo "true"; else echo "false"; ?>; SelectSite('<?echo htmlspecialchars($siteList[0]["ID"])?>');" value="<?echo GetMessage("FILEMAN_OPTION_RESET")?>">
<input type="hidden" name="Update" value="Y">
<input <?if (!$USER->CanDoOperation('fileman_edit_all_settings')) echo "disabled" ?> type="button" title="<?echo GetMessage("MAIN_HINT_RESTORE_DEFAULTS")?>" OnClick="RestoreDefaults();" value="<?echo GetMessage("MAIN_RESTORE_DEFAULTS")?>">
<?$tabControl->End();?>
</form>
