<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/************************************************************************************************************/
/*  Include Areas Component
/* Params:
/*	AREA_FILE_SHOW => {page | sect} - area to include. Default value - 'page'
/*	AREA_FILE_SUFFIX => string - suffix of file to seek. Default value - 'inc'
/*	AREA_FILE_RECURSIVE => {Y | N} - whether to search area file in parent directories. Used only when AREA_FILE_SHOW='sect'. Default value - 'Y'
/*	EDIT_MODE => {php | html | text} - default edit mode for an area. Default value - 'html'
/*	EDIT_TEMPLATE => string - default template to add new area. Default value - page_inc.php / sect_inc.php
/*
/************************************************************************************************************/

//$arParams["EDIT_MODE"] = in_array($arParams["EDIT_MODE"], array("php", "html", "text")) ? $arParams["EDIT_MODE"] : "html";
$arParams["EDIT_TEMPLATE"] = strlen($arParams["EDIT_TEMPLATE"]) > 0 ? $arParams["EDIT_TEMPLATE"] : $arParams["AREA_FILE_SHOW"]."_inc.php";

// check params values
$bHasPath = ($arParams["AREA_FILE_SHOW"] == 'file');
$sRealFilePath = $_SERVER["REAL_FILE_PATH"];

if (!$bHasPath)
{
	$arParams["AREA_FILE_SHOW"] = $arParams["AREA_FILE_SHOW"] == "sect" ? "sect" : "page";
	$arParams["AREA_FILE_SUFFIX"] = strlen($arParams["AREA_FILE_SUFFIX"]) > 0 ? $arParams["AREA_FILE_SUFFIX"] : "inc";
	$arParams["AREA_FILE_RECURSIVE"] = $arParams["AREA_FILE_RECURSIVE"] == "N" ? "N" : "Y";


	// check file for including
	if ($arParams["AREA_FILE_SHOW"] == "page")
	{
		// if page in SEF mode check real path
		if (strlen($sRealFilePath) > 0)
		{
			$slash_pos = strrpos($sRealFilePath, "/");
			$sFilePath = substr($sRealFilePath, 0, $slash_pos+1);
			$sFileName = substr($sRealFilePath, $slash_pos+1);
			$sFileName = substr($sFileName, 0, strlen($sFileName)-4)."_".$arParams["AREA_FILE_SUFFIX"].".php";
		}
		// otherwise use current
		else
		{
			$sFilePath = $APPLICATION->GetCurDir();
			$sFileName = substr($APPLICATION->GetCurPage(true), 0, strlen($APPLICATION->GetCurPage(true))-4)."_".$arParams["AREA_FILE_SUFFIX"].".php";
			$sFileName = substr($sFileName, strlen($sFilePath));
		}
		
		$sFilePathTMP = $sFilePath;
		$bFileFound = file_exists($_SERVER['DOCUMENT_ROOT'].$sFilePath.$sFileName);
	}
	else
	{
		// if page is in SEF mode - check real path
		if (strlen($sRealFilePath) > 0)
		{
			$slash_pos = strrpos($sRealFilePath, "/");
			$sFilePath = substr($sRealFilePath, 0, $slash_pos+1);
		}
		// otherwise use current
		else
		{
			$sFilePath = $APPLICATION->GetCurDir();
		}
		
		$sFilePathTMP = $sFilePath;
		$sFileName = "sect_".$arParams["AREA_FILE_SUFFIX"].".php";

		$bFileFound = file_exists($_SERVER['DOCUMENT_ROOT'].$sFilePath.$sFileName); 

		// if file not found and is set recursive check - start it
		if (!$bFileFound && $arParams["AREA_FILE_RECURSIVE"] == "Y" && $sFilePath != "/")
		{
			$finish = false;
			
			do
			{
				// back one level
				if (substr($sFilePath, -1) == "/") $sFilePath = substr($sFilePath, 0, -1);
				$slash_pos = strrpos($sFilePath, "/");
				$sFilePath = substr($sFilePath, 0, $slash_pos+1);
				
				$bFileFound = file_exists($_SERVER['DOCUMENT_ROOT'].$sFilePath.$sFileName);
				
				// if we are on the root - finish
				$finish = $sFilePath == "/";
			}
			while (!$finish && !$bFileFound);
		}
	}
}
else
{
	if (substr($arParams['PATH'], 0, 1) != '/')
	{
		// if page in SEF mode check real path
		if (strlen($sRealFilePath) > 0)
		{
			$slash_pos = strrpos($sRealFilePath, "/");
			$sFilePath = substr($sRealFilePath, 0, $slash_pos+1);
		}
		// otherwise use current
		else
		{
			$sFilePath = $APPLICATION->GetCurDir();
		}

		$arParams['PATH'] = Rel2Abs($sFilePath, $arParams['PATH']);
	}
	
	$slash_pos = strrpos($arParams['PATH'], "/");
	$sFilePath = substr($arParams['PATH'], 0, $slash_pos+1);
	$sFileName = substr($arParams['PATH'], $slash_pos+1);

	$bFileFound = file_exists($_SERVER['DOCUMENT_ROOT'].$sFilePath.$sFileName);
	
	$sFilePathTMP = $sFilePath;
}

//$editor = "/bitrix/admin/fileman_".($arParams["EDIT_MODE"] == "html" ? "html" : "file")."_edit.php?&site=".SITE_ID;
$editor = '&site='.SITE_ID.'&back_url='.urlencode($_SERVER['REQUEST_URI']);

$sPerm1 = $APPLICATION->GetFileAccessPermission($sFilePath.$sFileName);
$sPerm2 = $APPLICATION->GetFileAccessPermission($sFilePathTMP.$sFileName);

if ($APPLICATION->GetShowIncludeAreas() && ($sPerm1 >= "W" || $sPerm2 >="W"))
{
	$showMode = $APPLICATION->GetPublicShowMode();

	if ($bFileFound)
	{
		if ($sPerm1 >= 'W')
		{
			$arIcons = array(
				array(
					"URL" => 'javascript:'.$APPLICATION->GetPopupLink(
						array(
							'URL' => "/bitrix/admin/public_file_edit.php?bxpublic=Y&lang=".LANGUAGE_ID."&from=main.include&template=".urlencode($arParams["EDIT_TEMPLATE"])."&path=".urlencode($sFilePath.$sFileName).$editor,
							"PARAMS" => array(
								'width' => 770,
								'height' => 570,
								'resize' => false
							)
						)
					),
					"DEFAULT" => $APPLICATION->GetPublicShowMode() != 'configure',
					'ICON' => 'include-edit',
					'TITLE' => GetMessage("MAIN_INCLUDE_AREA_EDIT_".$arParams["AREA_FILE_SHOW"]),
				),
			);
			
			if ($showMode == 'configure')
			{
				$arIcons[] = array(
					"URL" => 'javascript:'.$APPLICATION->GetPopupLink(
						array(
							'URL' => "/bitrix/admin/public_file_edit_src.php?bxpublic=Y&lang=".LANGUAGE_ID."&from=main.include&template=".urlencode($arParams["EDIT_TEMPLATE"])."&path=".urlencode($sFilePath.$sFileName).$editor,
							"PARAMS" => array(
								'width' => 770,
								'height' => 570,
								'resize' => true
							)
						)
					),
					'ICON' => 'include-edit',
					'TITLE' => GetMessage("MAIN_INCLUDE_AREA_EDIT_".$arParams["AREA_FILE_SHOW"].'_NOEDITOR'),
				);
			}
		}

		if ($sFilePath != $sFilePathTMP && $sPerm2 >= 'W')
		{
			$arIcons[] = array(
					"URL" => 'javascript:'.$APPLICATION->GetPopupLink(
						array(
							'URL' => "/bitrix/admin/public_file_edit.php?bxpublic=Y&lang=".LANGUAGE_ID."&from=main.include&new=Y&path=".urlencode($sFilePathTMP.$sFileName)."&new=Y&template=".urlencode($arParams["EDIT_TEMPLATE"]).$editor,
							"PARAMS" => array(
								'width' => 770,
								'height' => 570,
								'resize' => false
							)
						)
					),
				"DEFAULT" => $APPLICATION->GetPublicShowMode() != 'configure',
				//'URL' => "javascript:jsPopup.ShowDialog('/bitrix/admin/public_file_edit.php', 'from=main.include&new=Y&path=".urlencode($sFilePathTMP.$sFileName)."&new=Y&template=".urlencode($arParams["EDIT_TEMPLATE"])."&mode=".$arParams["EDIT_MODE"].$editor."', 800, 570, jsEditorLoader)",
				'ICON' => 'include-add',
				'TITLE' => GetMessage("MAIN_INCLUDE_AREA_ADD_".$arParams["AREA_FILE_SHOW"]),
			);
			
			if ($showMode == 'configure')
			{
				$arIcons[] = array(
					"URL" => 'javascript:'.$APPLICATION->GetPopupLink(
							array(
								'URL' => "/bitrix/admin/public_file_edit_src.php?bxpublic=Y&lang=".LANGUAGE_ID."&from=main.include&new=Y&path=".urlencode($sFilePathTMP.$sFileName)."&new=Y&template=".urlencode($arParams["EDIT_TEMPLATE"]).$editor,
								"PARAMS" => array(
									'width' => 770,
									'height' => 570,
									'resize' => true
								)
							)
						),
					//'URL' => "javascript:jsPopup.ShowDialog('/bitrix/admin/public_file_edit.php', 'from=main.include&new=Y&noeditor=Y&path=".urlencode($sFilePathTMP.$sFileName)."&new=Y&template=".urlencode($arParams["EDIT_TEMPLATE"])."&mode=".$arParams["EDIT_MODE"].$editor."', 800, 570)",
					'ICON' => 'include-add',
					'TITLE' => GetMessage("MAIN_INCLUDE_AREA_ADD_".$arParams["AREA_FILE_SHOW"].'_NOEDITOR'),
				);
			}
		}
	}
	elseif ($sPerm2 >= 'W')
	{
		$arIcons = array(
			array(
				"URL" => 'javascript:'.$APPLICATION->GetPopupLink(
					array(
						'URL' => "/bitrix/admin/public_file_edit.php?bxpublic=Y&lang=".LANGUAGE_ID."&from=main.include&path=".urlencode($sFilePathTMP.$sFileName)."&new=Y&template=".urlencode($arParams["EDIT_TEMPLATE"]).$editor,
						"PARAMS" => array(
							'width' => 770,
							'height' => 570,
							'resize' => false
						)
					)
				),
				"DEFAULT" => $APPLICATION->GetPublicShowMode() != 'configure',
				//'URL' => "javascript:jsPopup.ShowDialog('/bitrix/admin/public_file_edit.php', 'from=main.include&path=".urlencode($sFilePathTMP.$sFileName)."&new=Y&template=".urlencode($arParams["EDIT_TEMPLATE"])."&mode=".$arParams["EDIT_MODE"].$editor."', 800, 570, jsEditorLoader)",
				'ICON' => 'include-add',
				'TITLE' => GetMessage("MAIN_INCLUDE_AREA_ADD_".$arParams["AREA_FILE_SHOW"]),
			),
		);
	
		if ($showMode == 'configure')
		{
			$arIcons[] = array(
				"URL" => 'javascript:'.$APPLICATION->GetPopupLink(
					array(
						'URL' => "/bitrix/admin/public_file_edit_src.php?bxpublic=Y&lang=".LANGUAGE_ID."&from=main.include&path=".urlencode($sFilePathTMP.$sFileName)."&new=Y&template=".urlencode($arParams["EDIT_TEMPLATE"]).$editor,
						"PARAMS" => array(
							'width' => 770,
							'height' => 570,
							'resize' => true
						)
					)
				),
				//'URL' => "javascript:jsPopup.ShowDialog('/bitrix/admin/public_file_edit.php', 'from=main.include&noeditor=Y&path=".urlencode($sFilePathTMP.$sFileName)."&new=Y&template=".urlencode($arParams["EDIT_TEMPLATE"])."&mode=".$arParams["EDIT_MODE"].$editor."', 800, 570)",
				'ICON' => 'include-add',
				'TITLE' => GetMessage("MAIN_INCLUDE_AREA_ADD_".$arParams["AREA_FILE_SHOW"].'_NOEDITOR'),
			);
		}
	}
	
	if (is_array($arIcons) && count($arIcons) > 0)
	{
		$APPLICATION->SetAdditionalCSS($this->GetPath()."/icons.css");
		$this->AddIncludeAreaIcons($arIcons);
	}
}

if ($bFileFound) 
{
	$arResult["FILE"] = $_SERVER["DOCUMENT_ROOT"].$sFilePath.$sFileName;
	$this->IncludeComponentTemplate();
}
?>