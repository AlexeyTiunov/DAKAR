<?
/*
##############################################
# Bitrix Site Manager                        #
# Copyright (c) 2002-2007 Bitrix             #
# http://www.bitrixsoft.com                  #
# mailto:admin@bitrixsoft.com                #
##############################################
*/
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
IncludeModuleLangFile(__FILE__);

function Panel_Sort($a, $b)
{
	if($a["MAIN_SORT"] == $b["MAIN_SORT"])
	{
		if($a["SORT"] == $b["SORT"])
			return 0;
		return ($a["SORT"] < $b["SORT"]? -1 : 1);
	}
	return ($a["MAIN_SORT"] < $b["MAIN_SORT"]? -1 : 1);
}


function GetStandardButtons()
{
	global $USER, $APPLICATION, $DB;

	//Check permissions functions
	function IsCanCreatePage($currentDirPath, $documentRoot, $filemanExists)
	{
		if (!is_dir($documentRoot.$currentDirPath) || !$GLOBALS["USER"]->CanDoFileOperation("fm_create_new_file", Array(SITE_ID, $currentDirPath)))
			return false;

		if ($filemanExists)
			return $GLOBALS["USER"]->CanDoOperation("fileman_admin_files");

		return true;
	}

	function IsCanCreateSection($currentDirPath, $documentRoot, $filemanExists)
	{
		if (!is_dir($documentRoot.$currentDirPath) ||
			!$GLOBALS["USER"]->CanDoFileOperation("fm_create_new_folder", Array(SITE_ID, $currentDirPath)) ||
			!$GLOBALS["USER"]->CanDoFileOperation("fm_create_new_file", Array(SITE_ID, $currentDirPath)))
			return false;

		if ($filemanExists)
			return ($GLOBALS["USER"]->CanDoOperation("fileman_admin_folders") && $GLOBALS["USER"]->CanDoOperation("fileman_admin_files"));

		return true;
	}

	function IsCanEditPage($currentFilePath, $documentRoot, $filemanExists)
	{
		if (!is_file($documentRoot.$currentFilePath) || !$GLOBALS["USER"]->CanDoFileOperation("fm_edit_existent_file",Array(SITE_ID, $currentFilePath)))
			return false;

		if ($filemanExists)
		{
			if (in_array(GetFileExtension($currentFilePath), GetScriptFileExt()) && !$GLOBALS["USER"]->CanDoFileOperation('fm_lpa', Array(SITE_ID, $currentFilePath)) && !$GLOBALS["USER"]->CanDoOperation('edit_php'))
				return false;

			return ($GLOBALS["USER"]->CanDoOperation("fileman_admin_files") && $GLOBALS["USER"]->CanDoOperation("fileman_edit_existent_files"));
		}

		return true;
	}

	function IsCanEditSection($currentDirPath, $filemanExists)
	{
		if (!$GLOBALS["USER"]->CanDoFileOperation("fm_edit_existent_folder", Array(SITE_ID, $currentDirPath)))
			return false;

		if ($filemanExists)
			return ($GLOBALS["USER"]->CanDoOperation("fileman_edit_existent_folders") && $GLOBALS["USER"]->CanDoOperation("fileman_admin_folders"));

		return true;
	}

	function IsCanEditPermission($currentFilePath, $documentRoot, $filemanExists)
	{
		if (!file_exists($documentRoot.$currentFilePath) ||
			!$GLOBALS["USER"]->CanDoFileOperation("fm_edit_existent_folder",Array(SITE_ID, $currentFilePath)) ||
			!$GLOBALS["USER"]->CanDoFileOperation("fm_edit_permission",Array(SITE_ID, $currentFilePath)))
				return false;

		if ($filemanExists)
			return ($GLOBALS["USER"]->CanDoOperation("fileman_edit_existent_folders") && $GLOBALS["USER"]->CanDoOperation("fileman_admin_folders"));

		return true;
	}

	function IsCanDeletePage($currentFilePath, $documentRoot, $filemanExists)
	{
		if (!is_file($documentRoot.$currentFilePath) || !$GLOBALS["USER"]->CanDoFileOperation("fm_delete_file",Array(SITE_ID, $currentFilePath)))
			return false;

		if ($filemanExists)
			return ($GLOBALS["USER"]->CanDoOperation("fileman_admin_files"));

		return true;
	}

	//updates button
	if($USER->CanDoOperation('install_updates'))
	{
		$update_res = UpdateTools::GetUpdateResult();

		if($update_res["result"] == true || $update_res['error'] <> '')
		{
			$APPLICATION->AddPanelButton(Array(
				"HREF"=> "/bitrix/admin/sysupdate.php?lang=".LANGUAGE_ID,
				"ICON"=>"icon-update",
				"ALT"=>($update_res["result"] == true? GetMessage("top_panel_updates_hint") : GetMessage("top_panel_update_hint2")),
				"TEXT"=>GetMessage("top_panel_update_btn"),
				"MAIN_SORT"=>"50",
				"SORT"=>10,
				"TOOLTIP"=>$update_res['tooltip'],
				"TOOLTIP_ACTION"=>"jsUserOptions.SaveOption('update', 'options', 'tooltip', 'off');",
			));
		}
	}

	if (isset($_SERVER["REAL_FILE_PATH"]) && $_SERVER["REAL_FILE_PATH"] != "")
	{
		$currentDirPath = dirname($_SERVER["REAL_FILE_PATH"]);
		$currentFilePath = $_SERVER["REAL_FILE_PATH"];
	}
	else
	{
		$currentDirPath = $APPLICATION->GetCurDir();
		$currentFilePath = $APPLICATION->GetCurPage(true);
	}

	$encCurrentDirPath = urlencode($currentDirPath);
	$encCurrentFilePath = urlencode($currentFilePath);
	$encRequestUri = urlencode($_SERVER["REQUEST_URI"]);
	$encSiteTemplateId = urlencode(SITE_TEMPLATE_ID);

	$documentRoot = CSite::GetSiteDocRoot(SITE_ID);
	$filemanExists = IsModuleInstalled("fileman");
	$showMode = $APPLICATION->GetPublicShowMode();

	//create button
	$defaultUrl = "";
	$arMenu = Array();
	$bCanCanCreatePage = IsCanCreatePage($currentDirPath, $documentRoot, $filemanExists);
	$bCanCreateSection = IsCanCreateSection($currentDirPath, $documentRoot, $filemanExists);

	if($bCanCanCreatePage || $bCanCreateSection)
	{
		$defaultUrl = $APPLICATION->GetPopupLink(
			Array(
				"URL"=>"/bitrix/admin/public_file_new.php?lang=".LANGUAGE_ID."&site=".SITE_ID."&templateID=".$encSiteTemplateId.
					"&path=".$encCurrentDirPath."&back_url=".$encRequestUri,
				"PARAMS"=> Array("min_width"=>450, "min_height" => 250)
			)
		);
	}
	//Create page menu item
	if($bCanCanCreatePage)
	{
		$arMenu[] = Array(
			"TEXT"=>GetMessage("top_panel_create_page"),
			"TITLE"=>GetMessage("top_panel_create_page_title"),
			"ICON"=>"panel-new-file",
			"ACTION"=> $defaultUrl,
			"DEFAULT"=>true,
		);
	}

	//Create section menu item
	if($bCanCreateSection)
	{
		$arMenu[] = Array(
			"TEXT"=>GetMessage("top_panel_create_folder"),
			"TITLE"=>GetMessage("top_panel_create_folder_title"),
			"ICON"=>"panel-new-folder",
			"ACTION"=> $APPLICATION->GetPopupLink(Array(
				"URL"=>"/bitrix/admin/public_file_new.php?lang=".LANGUAGE_ID."&site=".SITE_ID."&templateID=".$encSiteTemplateId.
							"&newFolder=Y&path=".$encCurrentDirPath."&back_url=".$encRequestUri,
				"PARAMS"=>Array("min_width"=>450, "min_height" => 250)))
		);
	}

	if($bCanCanCreatePage || $bCanCreateSection)
	{
		require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/admin_tools.php");

		//create page from new template
		$arActPageTemplates = CPageTemplate::GetList(array(SITE_TEMPLATE_ID));

		//create page from old template
		$arPageTemplates = GetFileTemplates(SITE_ID, array(SITE_TEMPLATE_ID));

		//templates menu for pages
		$arSubmenu = array();
		if(!empty($arActPageTemplates))
		{
			foreach($arActPageTemplates as $pageTemplate)
			{
				if($pageTemplate['type'] == '' || $pageTemplate['type'] == 'page')
				{
					$arSubmenu[] = array(
						"TEXT"=>"<b>".$pageTemplate['name']."</b>".($pageTemplate['description'] <> ''? "<br>".$pageTemplate['description']:""),
						"TITLE"=>GetMessage("top_panel_template")." ".$pageTemplate['file'],
						"ICON"=>($pageTemplate['icon'] == ''? "panel-new-file-template":""),
						"IMAGE"=>($pageTemplate['icon'] <> ''? $pageTemplate['icon']:""),
						"ACTION"=> str_replace("public_file_new.php?", "public_file_new.php?wiz_template=".urlencode($pageTemplate['file'])."&", $defaultUrl),
					);
				}
			}
		}

		if(!empty($arPageTemplates) && (!empty($arSubmenu) || count($arPageTemplates)>1))
		{
			if(!empty($arSubmenu))
				$arSubmenu[] = array("SEPARATOR"=>true);

			foreach($arPageTemplates as $pageTemplate)
				$arSubmenu[] = array(
					"TEXT"=>$pageTemplate['name'],
					"TITLE"=>GetMessage("top_panel_template")." ".$pageTemplate['file'],
					"ICON"=>"panel-new-file-template",
					"ACTION"=> str_replace("public_file_new.php?", "public_file_new.php?page_template=".urlencode($pageTemplate['file'])."&", $defaultUrl),
				);

		}

		//templates menu for sections
		$arSectSubmenu = array();
		if(!empty($arActPageTemplates))
		{
			foreach($arActPageTemplates as $pageTemplate)
			{
				if($pageTemplate['type'] == '' || $pageTemplate['type'] == 'section')
				{
					$arSectSubmenu[] = array(
						"TEXT"=>"<b>".$pageTemplate['name']."</b>".($pageTemplate['description'] <> ''? "<br>".$pageTemplate['description']:""),
						"TITLE"=>GetMessage("top_panel_template")." ".$pageTemplate['file'],
						"ICON"=>($pageTemplate['icon'] == ''? "panel-new-file-template":""),
						"IMAGE"=>($pageTemplate['icon'] <> ''? $pageTemplate['icon']:""),
						"ACTION"=> str_replace("public_file_new.php?", "public_file_new.php?newFolder=Y&wiz_template=".urlencode($pageTemplate['file'])."&", $defaultUrl),
					);
				}
			}
		}

		if(!empty($arPageTemplates) && (!empty($arSectSubmenu) || count($arPageTemplates)>1))
		{
			if(!empty($arSectSubmenu))
				$arSectSubmenu[] = array("SEPARATOR"=>true);

			foreach($arPageTemplates as $pageTemplate)
				$arSectSubmenu[] = array(
					"TEXT"=>$pageTemplate['name'],
					"TITLE"=>GetMessage("top_panel_template")." ".$pageTemplate['file'],
					"ICON"=>"panel-new-file-template",
					"ACTION"=> str_replace("public_file_new.php?", "public_file_new.php?newFolder=Y&page_template=".urlencode($pageTemplate['file'])."&", $defaultUrl),
				);

		}

		if(!empty($arSubmenu) || !empty($arSectSubmenu))
			$arMenu[] = array("SEPARATOR"=>true);

		//page from template
		if($bCanCanCreatePage && !empty($arSubmenu))
		{
			$arMenu[] = array(
				"TEXT"=>GetMessage("top_panel_create_from_template"),
				"TITLE"=>GetMessage("top_panel_create_from_template_title"),
				"ICON"=>"panel-new-file-template",
				"MENU"=>$arSubmenu,
			);
		}

		//section from template
		if($bCanCreateSection && !empty($arSectSubmenu))
		{
			$arMenu[] = array(
				"TEXT"=>GetMessage("top_panel_create_folder_template"),
				"TITLE"=>GetMessage("top_panel_create_folder_template_title"),
				"ICON"=>"panel-new-folder-template",
				"MENU"=>$arSectSubmenu,
			);
		}
	}

	if (!empty($arMenu))
	{
		$APPLICATION->AddPanelButton(Array(
			"HREF"=> ($defaultUrl == "" ? "" : "javascript:".$defaultUrl),
			"ID"=>"create",
			"ICON"=>"icon-create",
			"ALT"=>GetMessage("top_panel_create_title"),
			"TEXT"=>GetMessage("top_panel_create"),
			"MAIN_SORT"=>"100",
			"SORT"=>10,
			"MENU"=> $arMenu,
		));
	}

	$defaultUrl = "";
	$arMenu = Array();
	if (IsCanEditPage($currentFilePath, $documentRoot, $filemanExists))
	{
		$defaultUrl = $APPLICATION->GetPopupLink(Array(
			"URL"=> "/bitrix/admin/public_file_edit.php?lang=".LANGUAGE_ID."&bxpublic=Y&path=".$encCurrentFilePath."&site=".SITE_ID."&back_url=".$encRequestUri."&templateID=".$encSiteTemplateId,
			"PARAMS"=>array("width"=>780, "height"=>570, "resizable"=>false))
		);

		$arMenu[] = Array(
			"TEXT"=>GetMessage("top_panel_edit_page"),
			"TITLE"=>GetMessage("top_panel_edit_page_title"),
			"ICON"=>"panel-edit-visual",
			"ACTION"=> $defaultUrl,
			"DEFAULT"=>true,
		);


		$arMenu[] = Array(
			"TEXT"=>GetMessage("top_panel_edit_page_html"),
			"TITLE"=>GetMessage("top_panel_edit_page_html_title"),
			"ICON"=>"panel-edit-text",
			"ACTION"=>$APPLICATION->GetPopupLink(Array(
				"URL"=>"/bitrix/admin/public_file_edit.php?lang=".LANGUAGE_ID."&bxpublic=Y&noeditor=Y&path=".$encCurrentFilePath."&site=".SITE_ID."&back_url=".$encRequestUri,
				"PARAMS"=>array("width"=>780, "height"=>570, "resize"=>true))
			),
		);

		if ($showMode == "configure" && ($USER->CanDoOperation("fm_lpa") || $USER->CanDoOperation("edit_php")))
		{
			$arMenu[] = Array(
				"TEXT"=>GetMessage("top_panel_edit_page_php"),
				"TITLE"=>GetMessage("top_panel_edit_page_php_title"),
				"ICON"=>"panel-edit-php",
				"ACTION"=>$APPLICATION->GetPopupLink(Array(
					"URL" => "/bitrix/admin/public_file_edit_src.php?lang=".LANGUAGE_ID."&bxpublic=Y&path=".$encCurrentFilePath."&site=".SITE_ID."&back_url=".$encRequestUri,
					"PARAMS" => Array("width"=>770, "height" => 570, "resize" => true))
				),
			);
		}

		$arMenu[] = Array("SEPARATOR" => true);

		$arMenu[] = Array(
			"TEXT"=>GetMessage("top_panel_page_prop"),
			"TITLE"=>GetMessage("top_panel_page_prop_title"),
			"ICON"=>"panel-file-props",
			"ACTION"=> $APPLICATION->GetPopupLink(Array(
				"URL"=>"/bitrix/admin/public_file_property.php?lang=".LANGUAGE_ID."&site=".SITE_ID."&path=".$encCurrentFilePath."&back_url=".$encRequestUri,
				"PARAMS" => Array("min_width"=>450, "min_height" => 250))
			),
		);
	}

	if (IsCanEditSection($currentDirPath, $filemanExists))
	{
		$arMenu[] = Array(
			"TEXT"=>GetMessage("top_panel_folder_prop"),
			"TITLE"=>GetMessage("top_panel_folder_prop_title"),
			"ICON"=>"panel-folder-props",
			"ACTION"=>$APPLICATION->GetPopupLink(array(
				"URL"=>"/bitrix/admin/public_folder_edit.php?lang=".LANGUAGE_ID."&site=".SITE_ID."&path=".urlencode($APPLICATION->GetCurDir())."&back_url=".$encRequestUri)),
		);
	}

	if (!empty($arMenu))
	{
		$APPLICATION->AddPanelButton(array(
			"HREF"=>($defaultUrl == "" ? "" : "javascript:".$defaultUrl),
			"ID"=>"edit",
			"ICON"=>"icon-edit",
			"ALT"=>GetMessage("top_panel_edit_title"),
			"TEXT"=>GetMessage("top_panel_edit"),
			"MAIN_SORT"=>"100",
			"SORT"=>20,
			"MENU"=> $arMenu,
		));
	}

	if($USER->CanDoOperation('fileman_view_file_structure') && $USER->CanDoFileOperation('fm_edit_existent_folder', array(SITE_ID, "/")))
	{
		$defaultUrl = $APPLICATION->GetPopupLink(array(
			"URL" => "/bitrix/admin/public_structure.php?lang=".LANGUAGE_ID."&site=".SITE_ID."&path=".$encCurrentFilePath."&templateID=".$encSiteTemplateId,
			"PARAMS" => Array("width"=>350, "height"=>600, "resize" => true)));
		$arMenu = Array();
		if($showMode <> 'view' && $filemanExists)
		{
			$arMenu[] = array(
				"TEXT" => GetMessage("main_top_panel_struct"),
				"TITLE"=> GetMessage("main_top_panel_struct_title"),
				"ACTION" => $defaultUrl,
				"DEFAULT" => true,
			);
			$arMenu[] = array('SEPARATOR'=>true);
			$arMenu[] = array(
				"TEXT" => GetMessage("main_top_panel_struct_panel"),
				"TITLE"=> GetMessage("main_top_panel_struct_panel_title"),
				"ACTION"	=> "jsUtils.Redirect(arguments, '".CUtil::JSEscape("/bitrix/admin/fileman_admin.php?lang=".LANGUAGE_ID."&site=".SITE_ID."&path=".urlencode($APPLICATION->GetCurDir()))."')",
			);
		}
		$APPLICATION->AddPanelButton(Array(
			"HREF"=>"javascript:".$defaultUrl,
			"ID"=>"structure",
			"ICON"=>"icon-structure",
			"ALT"=>GetMessage("main_top_panel_struct_title"),
			"TEXT"=>GetMessage("main_top_panel_structure"),
			"MAIN_SORT"=>"100",
			"SORT"=>30,
			"MENU"=> $arMenu,
		));
	}

	$defaultUrl = "";
	$arMenu = Array();
	if (IsCanEditPermission($currentFilePath, $documentRoot, $filemanExists))
	{
		//access button
		$defaultUrl = $APPLICATION->GetPopupLink(Array(
			"URL"=>"/bitrix/admin/public_access_edit.php?lang=".LANGUAGE_ID."&site=".SITE_ID."&path=".$encCurrentFilePath."&back_url=".$encRequestUri,
			"PARAMS" => Array("min_width"=>450, "min_height" => 250)
		));

		$arMenu[] = Array(
			"TEXT"=>GetMessage("top_panel_access_page"),
			"TITLE"=>GetMessage("top_panel_access_page_title"),
			"ICON"=>"panel-file-access",
			"ACTION"=>$defaultUrl,
			"DEFAULT"=>true,
		);
	}

	if (IsCanEditPermission($currentDirPath, $documentRoot, $filemanExists))
	{
		$arMenu[] = Array(
			"TEXT"=>GetMessage("top_panel_access_folder"),
			"TITLE"=>GetMessage("top_panel_access_folder_title"),
			"ICON"=>"panel-folder-access",
			"ACTION"=>$APPLICATION->GetPopupLink(Array(
				"URL"=>"/bitrix/admin/public_access_edit.php?lang=".LANGUAGE_ID."&site=".SITE_ID."&path=".$encCurrentDirPath."&back_url=".$encRequestUri,
				"PARAMS" => Array("min_width"=>450, "min_height" => 250))
			),
		);
	}

	if (!empty($arMenu))
	{
		$APPLICATION->AddPanelButton(array(
			"HREF"=> ($defaultUrl == "" ? "" : "javascript:".$defaultUrl),
			"ICON"=>"icon-access",
			"ALT"=>GetMessage("top_panel_access_title"),
			"TEXT"=>GetMessage("top_panel_access"),
			"MAIN_SORT"=>"100",
			"SORT"=>40,
			"MENU"=>$arMenu,
			"MODE"=>array("edit", "configure"),
		));
	}

	//delete button
	if (IsCanDeletePage($currentFilePath, $documentRoot, $filemanExists))
	{
		$APPLICATION->AddPanelButton(Array(
			"HREF"=>"javascript:".$APPLICATION->GetPopupLink(array(
				"URL" => "/bitrix/admin/public_file_delete.php?lang=".LANGUAGE_ID."&site=".SITE_ID."&path=".$encCurrentFilePath,
				"PARAMS" => Array("min_width"=>250, "min_height" => 180, 'height' => 180, 'width' => 350))),
			"ID"=>"delete",
			"ICON"=>"icon-delete",
			"ALT"=>GetMessage("top_panel_del_page"),
			"TEXT"=>GetMessage("top_panel_del"),
			"MAIN_SORT"=>"100",
			"SORT"=>50,
			"MODE"=>array("edit", "configure"),
		));
	}

	//Template edit
	if ($USER->CanDoOperation("edit_php") || $USER->CanDoOperation("view_other_settings") || $USER->CanDoOperation("lpa_template_edit"))
	{
		$arMenu = array();
		$bUseSubmenu = false;

		$defaultUrl = '';

		$filePath = BX_ROOT."/templates/".SITE_TEMPLATE_ID."/styles.css";

		if (file_exists($_SERVER['DOCUMENT_ROOT'].$filePath))
		{
			$arMenu[] = array(
					"TEXT"	=> GetMessage("top_panel_templ_site_css"),
					"TITLE"	=> GetMessage("top_panel_templ_site_css_title"),
					"ICON"	=> "panel-edit-text",
					"ACTION"=> $APPLICATION->GetPopupLink(
						array(
							"URL" => "/bitrix/admin/public_file_edit_src.php?bxpublic=Y&lang=".LANGUAGE_ID."&path=".urlencode($filePath)."&site=".SITE_ID."&back_url=".$encRequestUri,
							"PARAMS" => array(
								"width" => 770,
								'height' => 570,
								'resize' => true,
							)
						)
					),
				);
			$bUseSubmenu = true;
		}

		$filePath = BX_ROOT."/templates/".SITE_TEMPLATE_ID."/template_styles.css";

		if (file_exists($_SERVER['DOCUMENT_ROOT'].$filePath))
		{
			$arMenu[] = array(
					"TEXT"   => GetMessage("top_panel_templ_templ_css"),
					"TITLE"  => GetMessage("top_panel_templ_templ_css_title"),
					"ICON"   => "panel-edit-text",
					"ACTION" => $APPLICATION->GetPopupLink(
						array(
							"URL" => "/bitrix/admin/public_file_edit_src.php?bxpublic=Y&lang=".LANGUAGE_ID."&path=".urlencode($filePath)."&site=".SITE_ID."&back_url=".$encRequestUri,
							"PARAMS" => array(
								"width" => 770,
								'height' => 570,
								'resize' => true,
							)
						)
					),
				);
			$bUseSubmenu = true;
		}

		$arSubMenu = array(
			array(
				"TEXT"		=>GetMessage("top_panel_templ_edit"),
				"TITLE"		=>GetMessage("top_panel_templ_edit_title"),
				"ICON"		=>"icon-edit",
				"ACTION"	=> "jsUtils.Redirect(arguments, '/bitrix/admin/template_edit.php?lang=".LANGUAGE_ID."&ID=".$encSiteTemplateId."')",
				"DEFAULT"	=>!$bUseSubmenu,
			),

			array(
				"TEXT"		=> GetMessage("top_panel_templ_site"),
				"TITLE"		=> GetMessage("top_panel_templ_site_title"),
				"ICON"		=> "icon-edit",
				"ACTION"	=> "jsUtils.Redirect(arguments, '/bitrix/admin/site_edit.php?lang=".LANGUAGE_ID."&LID=".SITE_ID."')",
				"DEFAULT"	=> false,
			),
		);

		if ($bUseSubmenu)
		{
			$arMenu[] = array('SEPARATOR' => "Y");

			$arMenu[] = array(
				"TEXT" => GetMessage("top_panel_cp"),
				"MENU" => $arSubMenu,
			);
		}
		else
		{
			$arMenu = $arSubMenu;
			$defaultUrl = $arSubMenu[0]['ACTION'];
		}


		$APPLICATION->AddPanelButton(Array(
			"HREF" => $defaultUrl,
			"ICON" => "icon-template",
			"ALT" => GetMessage("top_panel_templ_title"),
			"TEXT" => GetMessage("top_panel_templ"),
			"MAIN_SORT" => "800",
			"SORT" => 50,
			"MODE" => Array("configure"),
			"MENU" => $arMenu
		));
	}

	//cache button
	if ($USER->CanDoOperation("cache_control"))
	{
		//recreate cache on the current page
		$arMenu = Array(
			array(
				"TEXT"=>GetMessage("top_panel_cache_page"),
				"TITLE"=>GetMessage("top_panel_cache_page_title"),
				"ICON"=>"panel-page-cache",
				//"ACTION"=>"jsUtils.Redirect(arguments, '".CUtil::addslashes($APPLICATION->GetCurPageParam("clear_cache=Y", array("clear_cache")))."');",
				"ACTION" => "BX.clearCache()",
				"DEFAULT"=>true,
			),
		);
		if (!empty($APPLICATION->aCachedComponents))
		{
			$arMenu[] = array(
				"TEXT"=>GetMessage("top_panel_cache_comp"),
				"TITLE"=>GetMessage("top_panel_cache_comp_title"),
				"ICON"=>"panel-comp-cache",
				"ACTION"=>"jsComponentUtils.ClearCache('component_name=".CUtil::addslashes(implode(",", $APPLICATION->aCachedComponents))."&site_id=".SITE_ID."');",
			);
		}
		$arMenu[] = array("SEPARATOR"=>true);
		$arMenu[] = array(
			"TEXT"=>GetMessage("top_panel_cache_not"),
			"TITLE"=>GetMessage("top_panel_cache_not_title"),
			"ICON"=>($_SESSION["SESS_CLEAR_CACHE"] == "Y"? "checked":""),
			"ACTION"=>"jsUtils.Redirect(arguments, '".CUtil::addslashes($APPLICATION->GetCurPageParam("clear_cache_session=".($_SESSION["SESS_CLEAR_CACHE"] == "Y"? "N" : "Y"), array("clear_cache_session")))."');",
		);

		$APPLICATION->AddPanelButton(array(
			"HREF" => "javascript:BX.clearCache()",
			//"HREF"=>htmlspecialchars($APPLICATION->GetCurPageParam("clear_cache=Y", array("clear_cache"))),
			"ICON"=>"icon-cache",
			"TEXT"=>GetMessage("top_panel_cache"),
			"ALT"=>GetMessage("top_panel_clear_cache"),
			"MAIN_SORT"=>"900",
			"SORT"=>10,
			"MENU"=>$arMenu,
		));
	}

	//statistics buttons
	if ($USER->CanDoOperation("edit_php"))
	{
		//show debug information
		$cmd = ($_SESSION["SESS_SHOW_INCLUDE_TIME_EXEC"]=="Y" && $_SESSION["SESS_SHOW_TIME_EXEC"]=="Y" && $DB->ShowSqlStat? "N" : "Y");
		$url = $APPLICATION->GetCurPageParam("show_page_exec_time=".$cmd."&show_include_exec_time=".$cmd."&show_sql_stat=".$cmd, array("show_page_exec_time", "show_include_exec_time", "show_sql_stat"));
		$arMenu = array(
			array(
				"TEXT"=>GetMessage("top_panel_debug_summ"),
				"TITLE"=>GetMessage("top_panel_debug_summ_title"),
				"ICON"=>($cmd == "N"? "checked":""),
				"ACTION"=>"jsUtils.Redirect(arguments, '".CUtil::addslashes($url)."');",
				"DEFAULT"=>true,
			),
			array("SEPARATOR"=>true),
			array(
				"TEXT"=>GetMessage("top_panel_debug_sql"),
				"TITLE"=>GetMessage("top_panel_debug_sql_title"),
				"ICON"=>($DB->ShowSqlStat? "checked":""),
				"ACTION"=>"jsUtils.Redirect(arguments, '".CUtil::addslashes($APPLICATION->GetCurPageParam("show_sql_stat=".($DB->ShowSqlStat? "N" : "Y"), array("show_sql_stat")))."');",
			),
			array(
				"TEXT"=>GetMessage("top_panel_debug_incl"),
				"TITLE"=>GetMessage("top_panel_debug_incl_title"),
				"ICON"=>($_SESSION["SESS_SHOW_INCLUDE_TIME_EXEC"]=="Y"? "checked":""),
				"ACTION"=>"jsUtils.Redirect(arguments, '".CUtil::addslashes($APPLICATION->GetCurPageParam("show_include_exec_time=".($_SESSION["SESS_SHOW_INCLUDE_TIME_EXEC"]=="Y"? "N" : "Y"), array("show_include_exec_time")))."');",
			),
			array(
				"TEXT"=>GetMessage("top_panel_debug_time"),
				"TITLE"=>GetMessage("top_panel_debug_time_title"),
				"ICON"=>($_SESSION["SESS_SHOW_TIME_EXEC"]=="Y"? "checked":""),
				"ACTION"=>"jsUtils.Redirect(arguments, '".CUtil::addslashes($APPLICATION->GetCurPageParam("show_page_exec_time=".($_SESSION["SESS_SHOW_TIME_EXEC"]=="Y"? "N" : "Y"), array("show_page_exec_time")))."');",
			),
		);
		if(IsModuleInstalled("compression"))
		{
			$bShowCompressed = ($_SESSION["SESS_COMPRESS"] == "Y" && strtoupper($_GET["compress"])<>"N" || strtoupper($_GET["compress"])=="Y");
			$arMenu[] = array("SEPARATOR"=>true);
			$arMenu[] = array(
				"TEXT"=>GetMessage("top_panel_debug_compr"),
				"TITLE"=>GetMessage("top_panel_debug_compr_title"),
				"ICON"=>($bShowCompressed? "checked":""),
				"ACTION"=>"jsUtils.Redirect(arguments, '".CUtil::addslashes($APPLICATION->GetCurPageParam("compress=".($bShowCompressed? "N" : "Y"), array("compress")))."');",
			);
		}

		$APPLICATION->AddPanelButton(array(
			"HREF"=>$url,
			"ICON"=>"icon-debug",
			"TEXT"=>GetMessage("top_panel_debug"),
			"ALT"=>GetMessage("top_panel_show_debug"),
			"MAIN_SORT"=>"900",
			"SORT"=>20,
			"MODE"=>"configure",
			"MENU"=>$arMenu,
		));
	}

}

function GetPanelHtml()
{
	global $USER, $APPLICATION, $DB;

	if ($APPLICATION->ShowPanel === false || (!$USER->IsAuthorized() && $APPLICATION->ShowPanel !== true))
		return "";

	$core_scripts = CUtil::InitJSCore(array('window', 'ajax'), true);

	GetStandardButtons();

	//other modules buttons
	$db_events = GetModuleEvents("main", "OnPanelCreate");
	while($arEvent = $db_events->Fetch())
		ExecuteModuleEventEx($arEvent);

	$arPanelButtons = &$APPLICATION->arPanelButtons;
	usort($arPanelButtons, "Panel_Sort");

	/* Now we can display buttons */
	$bShowPanel = false;
	foreach($arPanelButtons as $key=>$arValue)
	{
		if(trim($arValue["HREF"]) <> "" || is_array($arValue["MENU"]) && !empty($arValue["MENU"]))
		{
			$bShowPanel = true;
			break;
		}
	}

	if ($bShowPanel === false && $APPLICATION->ShowPanel !== true)
		return "";

	require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/interface/init_admin.php");

	$bCanProfile = $USER->CanDoOperation('view_own_profile') || $USER->CanDoOperation('edit_own_profile');
	$url = '/bitrix/admin/user_edit.php?lang='.LANGUAGE_ID.'&ID='.$USER->GetID();
	$APPLICATION->AddPanelButton(array(
		"HREF"=>($bCanProfile? $url:""),
		"ICON"=>"icon-user",
		"TEXT"=>htmlspecialchars($USER->GetFullName()).' ('.htmlspecialchars($USER->GetLogin()).')',
		"ALT"=>($bCanProfile? GetMessage("top_panel_profile_title"):GetMessage("top_panel_curr_user")),
		"MAIN_SORT"=>"1000",
		"SORT"=>20,
	));
	$url = $APPLICATION->GetCurPage().'?logout=yes'.htmlspecialchars(($s=DeleteParam(array("logout"))) == ""? "":"&".$s);
	$APPLICATION->AddPanelButton(array(
		"HREF"=>$url,
		"ICON"=>"icon-key",
		"TEXT"=>GetMessage("top_panel_logout"),
		"ALT"=>GetMessage("TOP_LOG_OFF"),
		"MAIN_SORT"=>"1000",
		"SORT"=>30,
	));

	$APPLICATION->PanelShowed = true;

	if ($_GET["back_url_admin"] <> "")
		$_SESSION["BACK_URL_ADMIN"] = $_GET["back_url_admin"];

	if (isset($_GET["bitrix_include_areas"]) && $_GET["bitrix_include_areas"] <> "")
		$APPLICATION->SetShowIncludeAreas($_GET["bitrix_include_areas"]=="Y");
	if (isset($_GET["bitrix_show_mode"]) && $_GET["bitrix_show_mode"] <> "")
		$APPLICATION->SetPublicShowMode($_GET["bitrix_show_mode"]);

	$showMode = $APPLICATION->GetPublicShowMode();

	$params = DeleteParam(array("bitrix_include_areas", "bitrix_show_mode", "back_url_admin"));
	$href = $APPLICATION->GetCurPage();
	$hrefParams = ($params<>""? "&amp;".htmlspecialchars($params):"");

	$aUserOpt = CUserOptions::GetOption("admin_panel", "settings");

	$result = $core_scripts.$GLOBALS["adminPage"]->ShowScript().'
<script type="text/javascript" src="/bitrix/js/main/public_tools.js'.'?'.filemtime($_SERVER["DOCUMENT_ROOT"].'/bitrix/js/main/public_tools.js').'"></script>
<link rel="stylesheet" type="text/css" href="'.ADMIN_THEMES_PATH.'/'.ADMIN_THEME_ID.'/pubstyles.css'.'?'.filemtime($_SERVER["DOCUMENT_ROOT"].ADMIN_THEMES_PATH.'/'.ADMIN_THEME_ID.'/pubstyles.css').'" />
<link rel="stylesheet" type="text/css" href="'.ADMIN_THEMES_PATH.'/'.ADMIN_THEME_ID.'/jspopup.css'.'?'.filemtime($_SERVER["DOCUMENT_ROOT"].ADMIN_THEMES_PATH.'/'.ADMIN_THEME_ID.'/jspopup.css').'" />

<div style="display:none; overflow:hidden;" id="bx_top_panel_back"></div>
<div class="bx-top-panel" id="bx_top_panel_container">
<div id="bx_top_panel_splitter" style="display:'.($aUserOpt["collapsed"] == "on"? 'none':'block').'">
<div class="bx-panel-empty"></div>
<table cellspacing="0" cellpadding="0" class="bx-panel-container" dir="ltr">
	<tr>
		<td class="bx-button-cell"><div class="bx-start-button" onclick="jsStartMenu.ShowStartMenu(this, \''.CUtil::JSEscape($href.($params<>""? "?".htmlspecialchars($params):"")).'\');" onmouseover="this.className+=\' start-over\'" onmouseout="this.className=this.className.replace(/\s*start-over/i, \'\')" title="'.GetMessage("top_panel_start").'"></div></td>
		<td class="bx-tabs-cell">
';
	$aTabs = array(
		array(
			"ICON"=>"bx-tab-icon-view",
			"URL"=>($showMode <> "view"? $href."?bitrix_include_areas=N&amp;bitrix_show_mode=view".$hrefParams:""),
			"TEXT"=>GetMessage("top_panel_tab_view"),
			"TITLE"=>GetMessage("pub_top_panel_view_title")
		),
		array(
			"ICON"=>"bx-tab-icon-edit",
			"URL"=>($showMode <> "edit"? $href.'?bitrix_include_areas=Y&amp;bitrix_show_mode=edit'.$hrefParams:""),
			"TEXT"=>GetMessage("top_panel_tab_edit"),
			"TITLE"=>GetMessage("top_panel_tab_edit_title")
		),
		array(
			"ICON"=>"bx-tab-icon-configure",
			"URL"=>($showMode <> "configure"? $href.'?bitrix_include_areas=Y&amp;bitrix_show_mode=configure'.$hrefParams:""),
			"TEXT"=>GetMessage("top_panel_tab_configure"),
			"TITLE"=>GetMessage("top_panel_tab_configure_title")
		),
		array(
			"ICON"=>"bx-tab-icon-admin",
			"URL"=>($_SESSION["BACK_URL_ADMIN"] <> ""?
				htmlspecialchars($_SESSION["BACK_URL_ADMIN"]).(strpos($_SESSION["BACK_URL_ADMIN"], "?") !== false? "&amp;":"?") :
				'/bitrix/admin/index.php?lang='.LANGUAGE_ID.'&amp;').'back_url_pub='.urlencode($href.($params<>""? "?".$params:"")),
			"TEXT"=>GetMessage("pub_top_panel_adm"),
			"TITLE"=>GetMessage("top_panel_tab_cp")
		),
	);

	$selectedIndex = array_search($showMode, array('view', 'edit', 'configure'));
	$cnt = count($aTabs);
	foreach($aTabs as $i=>$tab)
	{
		if($tab["URL"] <> "")
			$result .= '<a href="'.$tab["URL"].'" title="'.$tab["TITLE"].'">';

		$result .='
<div class="bx-panel-tab'.($i == $selectedIndex? ' bx-panel-tab-active':'').($i == $cnt-1? ' bx-panel-tab-admin':'').'">
	<div class="bx-tab-left'.($i == 0? ' bx-tab-left-first':'').($i == $selectedIndex? ' bx-tab-left-active':'').($i == $selectedIndex+1? ' bx-tab-left-prev-active':'').'"><div class="bx-tab-icon '.$tab["ICON"].'"></div></div>
	<div class="bx-tab'.($i == $selectedIndex? ' bx-tab-active':'').'"><div class="bx-panel-caption">'.$tab["TEXT"].'</div></div>
	<div class="bx-tab-right'.($i == $selectedIndex? ' bx-tab-right-active':'').($i == $selectedIndex-1? ' bx-tab-right-next-active':'').'"></div>
	<br />
	<div class="bx-tab-bottom'.($i == $selectedIndex? ' bx-tab-bottom-active':'').($i == $selectedIndex+1? ' bx-tab-bottom-prev-active':'').($i == 0? ' bx-tab-bottom-first'.($i == $selectedIndex? '-active':''):'').'"></div>
</div>';
			if($tab["URL"] <> "")
				$result .= '</a>';
		}

		$result .= '
	<a id="admin_panel_fix_link" class="fix-link fix-on" href="javascript:jsPanel.FixPanel();" title="'.GetMessage("pub_top_panel_fix_title").'"></a>
	<br clear="all" />

<table cellpadding="0" cellspacing="0" border="0" style="width:100% !important;">
	<tr>
		<td style="vertical-align:top !important;"><div class="bx-panel-left"></div></td>
		<td>
<div class="bx-panel-buttons">
';

	$main_sort = "";
	foreach($arPanelButtons as $key=>$arValue)
	{
		if(array_key_exists("MODE", $arValue))
		{
			if(is_array($arValue["MODE"]))
			{
				if(!in_array($showMode, $arValue["MODE"]))
					continue;
			}
			elseif($arValue["MODE"] != $showMode)
				continue;
		}

		//very old behaviour
		if(is_set($arValue, "SRC_0"))
			$arValue["SRC"] = $arValue["SRC_0"];

		if($main_sort!=$arValue["MAIN_SORT"] && $main_sort<>"")
			$result .= '<div class="bx-pnseparator"></div>';

		$main_sort = $arValue["MAIN_SORT"];

		$tooltip = ($arValue["TOOLTIP"] <> ''? '<div class="panel-tooltip"><a class="panel-tooltip-close" title="'.GetMessage("top_panel_updates_close").'" href="javascript:void(0)" onclick="'.$arValue["TOOLTIP_ACTION"].'jsUtils.FindParentObject(this, \'div\').style.display = \'none\';"></a>'.$arValue["TOOLTIP"].'</div>' : '');

		$result .= '<div class="bx-pnbutton">'.$tooltip;

		$result .= '
		<table cellspacing="0" class="bx-pnbutton" onmouseover="this.className+=\' bx-pnbutton-over\'" onmouseout="this.className=this.className.replace(/\s*bx-pnbutton-over/i, \'\')" id="bx_panel_button_'.$key.'">
			<tr>
				<td class="bx-left"><div class="empty"></div></td>
				<td class="bx-center">';
		$bMenu = (is_array($arValue["MENU"]) && !empty($arValue["MENU"]));
		if($bMenu)
		{
			if($arValue["RESORT_MENU"] == true)
				usort($arValue["MENU"], create_function('$a, $b', 'if($a["SORT"] == $b["SORT"]) return 0; return ($a["SORT"] < $b["SORT"])? -1 : 1;'));

			$menu = new CAdminPopup("bx_panel_menu_".$key, "bx_panel_menu_".$key, $arValue["MENU"], array('zIndex'=>1100));
			$result .= $menu->Show(true);
			$sMenuLink = "bx_panel_menu_".$key.".ShowMenu(document.getElementById('bx_panel_button_".$key."'), null, jsPanel.IsFixed(), {top:0, bottom:0, left:0, right:0});";
		}
		$result .= '<div '.$arValue["FIELD"];
		if($arValue["HREF"]<>"")
		{
			if(strtolower(substr($arValue["HREF"], 0, 11)) == 'javascript:')
				$url = substr($arValue["HREF"], 11);
			else
				$url = 'jsUtils.Redirect(arguments, \''.CUtil::JSEscape($arValue["HREF"]).'\')';
			$result .= ' onclick="'.$url.'"';
		}
		elseif($bMenu)
		{
			$result .= ' onclick="'.$sMenuLink.'"';
		}
		$result .= ' class="bx-button'.($arValue["ICON"]<>"" || $arValue["SRC"]<>""? ' bx-pnicon '.$arValue["ICON"]:'').'"'.
					($arValue["SRC"]<>""? ' style="background-image:url('.$arValue["SRC"].'); background-position:0px 1px; padding-left:24px;"':'').' title="'.$arValue["ALT"].'"><div class="bx-button-text">'.$arValue["TEXT"].'</div></div></td>
				<td class="bx-right'.($bMenu? ($arValue["HREF"]<>""? ' bx-arrow-separate':' bx-arrow'):'').'"';
		if($bMenu)
		{
			if($arValue["HREF"]<>"")
				$result .= ' onclick="'."var o=document.getElementById('bx_panel_button_".$key."'); bx_panel_menu_".$key.".ShowMenu(this, null, jsPanel.IsFixed(), {top:0, bottom:0, left:-(o.offsetWidth-this.offsetWidth), right:0});".'"';
			else
				$result .=' onclick="'.$sMenuLink.'"';
		}
		$result .= '><div class="empty"></div></td>
			</tr>
		</table>
		';

		$result .= '</div>';
	}

	$maxQuota = COption::GetOptionInt("main", "disk_space", 0)*1024*1024;
	if($maxQuota > 0)
	{
		$quota = new CDiskQuota();
		$free = $quota->GetDiskQuota();
		$result .= '
		<div class="bx-pnseparator"></div>
		<div class="bx-pnbutton">
			<div class="free-space-bar">
			<table class="free-space-bar" cellspacing="1" title="'.GetMessage("top_panel_quota", array("#FREE#"=>round($free/(1024*1024)), "#QUOTA#"=>round($maxQuota/(1024*1024)))).'">
				<tr>
					<td class="bx-panel-used" style="width:'.(100 - round($free/$maxQuota*100)).'% !important;"><div class="empty"></div></td>
					<td class="bx-panel-free" style="width:'.round($free/$maxQuota*100).'% !important;"><div class="empty"></div></td>
				</tr>
			</table>
			</div>
		</div>';
	}

		$result .= '
	<br clear="all" />
</div>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</div>
<table cellspacing="0" class="splitter">
	<tr>
		<td><a href="javascript:void(0);" onclick="jsPanel.DisplayPanel(this);" class="splitterknob'.($aUserOpt["collapsed"] == "on"? ' splitterknobdown':'').'" title="'.($aUserOpt["collapsed"] == "on"? GetMessage("top_panel_show"):GetMessage("top_panel_hide")).'"></a></td>
	</tr>
</table>
</div>';
	if($aUserOpt["fix"] == "on")
		$result .= '<script type="text/javascript">jsPanel.FixOn();</script>';

	//start menu preload
	$aUserOptGlobal = CUserOptions::GetOption("global", "settings");
	if($aUserOptGlobal["start_menu_preload"] == 'Y')
		$result .= '<script type="text/javascript">jsUtils.addEvent(window, "load", function(){jsStartMenu.PreloadMenu(\''.CUtil::JSEscape($href.($params<>""? "?".$params:"")).'\');});</script>';

	//show script to play sound
	$result .= $GLOBALS["adminPage"]->ShowSound();

	return $result;
}
?>