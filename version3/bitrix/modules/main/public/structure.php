<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_js.php");

if(CModule::IncludeModule("compression"))
	CCompress::Disable2048Spaces();

IncludeModuleLangFile(__FILE__);

$obJSPopup = new CJSPopup(GetMessage("pub_struct_title"));

if(!$USER->CanDoOperation('fileman_view_file_structure') || !$USER->CanDoFileOperation('fm_edit_existent_folder', array(SITE_ID, "/")))
	$obJSPopup->ShowError(GetMessage("ACCESS_DENIED"));

function __struct_file_sort($a, $b)
{
	if($a["type"] == "D" && $b["type"] == "F")
		return -1;
	elseif($a["type"] == "F" && $b["type"] == "D")
		return 1;
	else
	{
		$name1 = ($a["name"] <> ''? $a["name"] : $a["file"]);
		$name2 = ($b["name"] <> ''? $b["name"] : $b["file"]);
		return strcmp(strtoupper($name1), strtoupper($name2));
	}
}

function __struct_get_file_info($abs_path, $file)
{
	static $arTextExt = array("php", "htm", "html");

	$arFile = array("file"=>$file, "name"=>"");
	if(is_dir($abs_path."/".$file))
	{
		$arFile["type"] = "D";
		if(file_exists($abs_path."/".$file."/.section.php"))
		{
			$sSectionName = "";
			$arDirProperties = array();
			include($abs_path."/".$file."/.section.php");
			$arFile["name"] = $sSectionName;
			$arFile["properties"] = $arDirProperties;
		}
	}
	else
	{
		$arFile["type"] = "F";
		if(in_array(GetFileExtension($file), $arTextExt))
		{
			$sContent = $GLOBALS["APPLICATION"]->GetFileContent($abs_path."/".$file);
			$arContent = ParseFileContent($sContent);
			$arFile["name"] = $arContent["TITLE"];
			$arFile["properties"] = $arContent["PROPERTIES"];
		}
	}
	if($GLOBALS['arOptions']['show_file_info'] == true)
	{
		$arFile["time"] = filemtime($abs_path."/".$file);
		$arFile["size"] = filesize($abs_path."/".$file);
	}
	return $arFile;
}

function __struct_show_files($arFiles, $doc_root, $path, $open_path, $dirsonly=false)
{
	global $USER;
	$res = '';
	$hintScript = '';
	$scrDest = '';
	$scrSrc = '';
	foreach($arFiles as $arFile)
	{
		if($arFile["name"] == '' && $arFile["file"] <> "/" && $GLOBALS['arOptions']['show_all_files'] != true)
			continue;

		$full_path = rtrim($path, "/")."/".trim($arFile["file"], "/");
		$encPath = urlencode($full_path);
		$name = ($arFile["name"] <> ''? htmlspecialcharsback($arFile["name"]):$arFile["file"]);

		$md5 = md5($full_path);
		if($dirsonly)
			$md5 = "_dirs".$md5;
		$itemID = 'item'.$md5;
		$item = '';
		if($arFile["type"] == 'D')
		{
			$arPath = array($_GET['site'], $full_path);
			$arPerm = array(
				"create_file" => $USER->CanDoFileOperation("fm_create_new_file", $arPath),
				"create_folder" => $USER->CanDoFileOperation("fm_create_new_folder", $arPath),
				"edit_folder" => $USER->CanDoFileOperation("fm_edit_existent_folder", $arPath),
				"edit_perm" => $USER->CanDoFileOperation("fm_edit_permission", $arPath),
				"del_folder" => $USER->CanDoFileOperation("fm_delete_folder", $arPath),
			);
			
			$bOpenSubdir = ($open_path <> "" && (strpos($open_path."/", $full_path."/") === 0 || $arFile["file"] == "/"));
			$dirID = 'dir'.$md5;
			$item = '<div id="sign'.$md5.'" class="'.($bOpenSubdir? 'bx-struct-minus':'bx-struct-plus').'" onclick="structGetSubDir(this, \''.$dirID.'\', \''.$encPath.'\', '.($dirsonly? 'true':'false').')"></div>
				<div class="bx-struct-dir" id="icon'.$md5.'"></div>
				<div id="'.$itemID.'" __bx_path="'.$encPath.'" __bx_type="D" class="bx-struct-name"'.
				' onmouseover="structNameOver(this)" onmouseout="structNameOut(this)" onclick="structShowDirMenu(this, '.($dirsonly? 'true':'false').', '.CUtil::PhpToJSObject($arPerm).')"'.
				' ondblclick="structGetSubdirAction(\'sign'.$md5.'\')">'.htmlspecialcharsEx($name).'</div>
				<div style="clear:both;"></div>
				<div id="'.$dirID.'" class="bx-struct-sub" style="display:'.($bOpenSubdir? 'block':'none').'">'.
				($bOpenSubdir? __struct_get_files($doc_root, $full_path, $open_path, $dirsonly):'').'</div>';

			$scrDest .= ($scrDest <>''? ', ':'')."'".$itemID."'";
			if($arFile["file"] <> '/')
				$scrSrc .= ($scrSrc <>''? ', ':'')."'".$itemID."', 'icon".$md5."'";
		}
		elseif($dirsonly == false)
		{
			$arPath = array($_GET['site'], $full_path);
			$arPerm = array(
				"edit_file" => $USER->CanDoFileOperation("fm_edit_existent_file", $arPath),
				"edit_perm" => $USER->CanDoFileOperation("fm_edit_permission", $arPath),
				"del_file" => $USER->CanDoFileOperation("fm_delete_file", $arPath),
			);

			if($GLOBALS['bFileman'] == true && $GLOBALS['arOptions']['show_all_files'] == true)
				$type = CFileMan::GetFileTypeEx($arFile["file"]);
			else
				$type = "";

			$item = '<div style="float:left"></div><div class="bx-struct-file'.($type <> ''? ' bx-struct-type-'.$type : '').'" id="icon'.$md5.'"></div>
				<div id="'.$itemID.'" __bx_path="'.$encPath.'" __bx_type="F" class="bx-struct-name" onmouseover="structNameOver(this)" onmouseout="structNameOut(this)" onclick="structShowFileMenu(this, '.CUtil::PhpToJSObject($arPerm).')" ondblclick="structEditFileAction(this)">'.htmlspecialcharsEx($name).'</div>
				<div style="clear:both;"></div>';

			$scrSrc .= ($scrSrc <>''? ', ':'')."'".$itemID."', 'icon".$md5."'";
		}
		if($item <> '')
			$res .= '<div class="bx-struct-item">'.$item.'</div>';

		if($GLOBALS['arOptions']['show_file_info'] == true)
		{
			$sHint = '<table cellspacing="0" border="0">'.
				'<tr><td colspan="2"><b>'.($arFile["type"] == 'D'? GetMessage("pub_struct_folder"):GetMessage("pub_struct_file")).'</b></td></tr>'.
				'<tr><td class="bx-grey">'.GetMessage("pub_struct_name").'</td><td>'.htmlspecialcharsEx($arFile["file"]).'</td></tr>'.
				($arFile["type"] == 'F'? '<tr><td class="bx-grey">'.GetMessage("pub_struct_size")."</td><td>".number_format($arFile["size"], 0, ".", ",")." ".GetMessage("pub_struct_byte").'</td></tr>':'').
				'<tr><td class="bx-grey">'.GetMessage("pub_struct_modified").'</td><td>'.htmlspecialcharsEx(ConvertTimeStamp($arFile["time"], 'FULL', $_GET['site'])).'</td></tr>';
			if(is_array($arFile["properties"]))
				foreach($arFile["properties"] as $prop_name => $prop_val)
					$sHint .= '<tr valign="top"><td class="bx-grey">'.htmlspecialcharsEx($prop_name).':</td><td>'.htmlspecialcharsEx($prop_val).'</td></tr>';
			$sHint .= '</table>';
			
			$hintScript .= 'window.structHint'.$itemID.' = new BXHint(\''.CUtil::JSEscape($sHint).'\', document.getElementById(\''.$itemID.'\')); ';
		}
	}
	if($hintScript <> '')
		$res .= '<script>'.$hintScript.'</script>';

	if($GLOBALS['bFileman'] == true)
		$res .= '<script>structRegisterDD(['.$scrSrc.'], ['.$scrDest.']);</script>';

	return $res;
}

function __struct_get_files($doc_root, $path="", $open_path="", $dirsonly=false)
{
	if(!$GLOBALS['USER']->CanDoFileOperation('fm_view_listing', array($_GET['site'], $path)))
		return '';

	$arFiles = array();
	$abs_path = $doc_root."/".$path;
	$handle = opendir($abs_path);
	if($handle)
	{
		while(($file = readdir($handle)) !== false)
		{
			if($file == "." || $file == "..")
				continue;

			if(is_file($abs_path."/".$file))
			{
				if($file == '.section.php' || $file == '.access.php')
					continue;
				if(preg_match('/^\.(.*)?\.menu\.(php|html|php3|php4|php5|php6|phtml)$/', $file))
					continue;
			}
			$arFile = __struct_get_file_info($abs_path, $file);
			$arFiles[] = $arFile;
		}
		closedir($handle);
	
		usort($arFiles, "__struct_file_sort");
	}

	return __struct_show_files($arFiles, $doc_root, $path, $open_path, $dirsonly);
}

$bFileman = CModule::IncludeModule('fileman');

$strWarning = "";
$DOC_ROOT = CSite::GetSiteDocRoot($_GET["site"]);

$arOptions = CUserOptions::GetOption("public_structure", "options", array());
if(!isset($arOptions['show_file_info']))
	$arOptions['show_file_info'] = true;

// **********************************************
//ajax requests
if($_GET['ajax'] == 'Y')
{
	if($_GET['action'] == 'delfolder' && check_bitrix_sessid() && $bFileman)
	{
		$normPath = CFileMan::NormalizePath($_GET["path"]);
		if($normPath <> "")
			$strWarning = CFileMan::DeleteEx(array($_GET["site"], $normPath));
	}
	elseif(($_GET['action'] == 'copy' || $_GET['action'] == 'move') && check_bitrix_sessid() && $bFileman)
	{
		$normFrom = CFileMan::NormalizePath($_GET["from"]);
		$name = "";
		if(($pos = strrpos($normFrom, "/")) !== false)
			$name = substr($normFrom, $pos+1);
		$normTo = CFileMan::NormalizePath($_GET["to"]."/".$name);
		if($normFrom <> "" && $normTo <> "")
			$strWarning = CFileMan::CopyEx(array($_GET["site"], $normFrom), array($_GET["site"], $normTo), ($_GET['action'] == "move"? true : false));
	}
		
	if(isset($_GET['show_all_files']))
		$arOptions['show_all_files'] = ($_GET['show_all_files'] == 'Y');
	if(isset($_GET['show_file_info']))
		$arOptions['show_file_info'] = ($_GET['show_file_info'] == 'Y');
	if(isset($_GET['show_all_files']) || isset($_GET['show_file_info']))
		CUserOptions::SetOption("public_structure", "options", $arOptions);

	if($_GET['load_path'] <> '')
	{
		echo __struct_get_files($DOC_ROOT, $_GET['load_path'], "", ($_GET['dirsonly']=='Y'));
	}
	elseif($_GET['reload'] == 'Y')
	{
		//display first level tree
		$arRoot = __struct_get_file_info($DOC_ROOT, "/");
		echo __struct_show_files(array($arRoot), $DOC_ROOT, "", $_GET["path"], ($_GET['dirsonly']=='Y'));
	}

	if($strWarning <> "")
	{
		$obJSPopup->ShowValidationError($strWarning);
		echo '<script>jsPopup.AdjustShadow()</script>';
	}
}
?>
<script>window.structOptions = <?=CUtil::PhpToJSObject($arOptions)?>;</script>
<?
if($_GET['ajax'] == 'Y')
{
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin_js.php");
	die();
}
// **********************************************

$encPath = urlencode($_GET["path"]);
$encLang = urlencode($_GET["lang"]);
$encSite = urlencode($_GET["site"]);
$encTemplateID = urlencode($_GET["templateID"]);
?>
<script src="/bitrix/js/main/dd.js<?echo '?'.filemtime($_SERVER["DOCUMENT_ROOT"].'/bitrix/js/main/dd.js')?>" type="text/javascript"></script>
<script>
window.structRegisterDD = function(arSrc, arDest)
{
	for(var i=0, n=arSrc.length; i<n; i++)
	{
		var obEl = document.getElementById(arSrc[i]);
		obEl.onbxdragstart = Struct_DragStart;
		obEl.onbxdragstop = Struct_DragStop;
		obEl.onbxdrag = Struct_Drag;
		jsDD.registerObject(obEl);
	}
	for(var i=0, n=arDest.length; i<n; i++)
	{
		var obEl = document.getElementById(arDest[i]);
		obEl.onbxdestdraghover = Struct_DragHover;
		obEl.onbxdestdraghout = Struct_DragOut;
		obEl.onbxdestdragfinish = Struct_DragFinish;
		jsDD.registerDest(obEl);
	}
}

window.structGetSubDir = function(el, div_id, path, dirsonly)
{
	var div = document.getElementById(div_id);
	if(!div)
		return;
	if(div.innerHTML == '')
	{
		div.innerHTML = '<?=CUtil::JSEscape(GetMessage("pub_struct_loading"))?>';
		CHttpRequest.Action = function(result)
		{
			result = jsUtils.trim(result);
			div.innerHTML = result;
			if(result == '')
			{
				el.onclick = null;
				el.className = 'bx-struct-dot';
				div.style.display = 'none';
			}
		};
		CHttpRequest.Send('/bitrix/admin/public_structure.php?ajax=Y&<?="lang=".$encLang."&site=".$encSite?>&load_path='+path+(dirsonly? '&dirsonly=Y':''));
	}
	el.className = (el.className == 'bx-struct-plus'? 'bx-struct-minus':'bx-struct-plus');
	div.style.display = (div.style.display == 'none'? 'block':'none');
}

window.structGetSubdirAction = function(id)
{
	var el = document.getElementById(id);
	if(el)
	{
		setTimeout(function(){if(window.structMenu)	window.structMenu.PopupHide();}, 50);
		el.onclick();
	}
}

window.structReload = function(path, params)
{
	CHttpRequest.Action = function(result)
	{
		jsDD.Reset();
		var container = document.getElementById('structure_content');
		if(container)
			container.innerHTML = result;

		CloseWaitWindow();
		structReloadDirs(path);
	};
	setTimeout(ShowWaitWindow, 50);
	CHttpRequest.Send('/bitrix/admin/public_structure.php?ajax=Y&reload=Y&<?="lang=".$encLang."&site=".$encSite?>&path='+path+(params? '&'+params:''));
}

window.structReloadDirs = function(path)
{
	var container = document.getElementById('bx_struct_dirs_container');
	if(!container)
		return;
	CHttpRequest.Action = function(result)
	{
		container.innerHTML = result;
		CloseWaitWindow();
	};
	setTimeout(ShowWaitWindow, 50);
	CHttpRequest.Send('/bitrix/admin/public_structure.php?ajax=Y&reload=Y&<?="lang=".$encLang."&site=".$encSite?>&dirsonly=Y&path='+path);
}

window.structNameOver = function(el)
{
	el.className += ' bx-struct-name-over';
}

window.structNameOut = function(el)
{
	el.className = el.className.replace(/\s*bx-struct-name-over/ig, "");
}

window.jsPopup_subdialog = new JCPopup({'suffix':'subdialog', 'zIndex':parseInt(<?=$obJSPopup->jsPopup?>.zIndex)+20});
window.structShowSubDialog = function()
{
	setTimeout(function(){<?=$obJSPopup->jsPopup?>.bDenyEscKey = true}, 200);
	jsUtils.addCustomEvent('OnBeforeCloseDialog', function(){setTimeout(function(){<?=$obJSPopup->jsPopup?>.bDenyEscKey = false;}, 50);});
}

window.structAddFile = function(path, isFolder)
{
	structShowSubDialog();
<?
	$url = $APPLICATION->GetPopupLink(array(
		"URL"=>"/bitrix/admin/public_file_new.php?subdialog=Y&lang=".$encLang."&site=".$encSite."&templateID=".$encTemplateID."&path=#PATH#", 
		"PARAMS"=> Array("min_width"=>450, "min_height" => 250)), "subdialog");
	$url = str_replace("#PATH#", "'+path+(isFolder==true? '&newFolder=Y':'')+'", $url);
?>
	<?=$url?>;
}

window.structAccessDialog = function(path)
{
	structShowSubDialog();
<?
	$url = $APPLICATION->GetPopupLink(Array(
		"URL"=>"/bitrix/admin/public_access_edit.php?subdialog=Y&lang=".$encLang."&site=".$encSite."&path=#PATH#",
		"PARAMS" => Array("min_width"=>450, "min_height" => 250)), "subdialog");
	$url = str_replace("#PATH#", "'+path+'", $url);
?>
	<?=$url?>;
}

window.structEditFolder = function(path)
{
	structShowSubDialog();
<?
	$url = $APPLICATION->GetPopupLink(array(
		"URL"=>"/bitrix/admin/public_folder_edit.php?subdialog=Y&lang=".$encLang."&site=".$encSite."&path=#PATH#",
		"PARAMS" => Array("min_width"=>450, "min_height" => 250)), "subdialog");
	$url = str_replace("#PATH#", "'+path+'", $url);
?>
	<?=$url?>;
}

jsPopup_editor = new JCPopup({'suffix':'editor', 'zIndex':parseInt(<?=$obJSPopup->jsPopup?>.zIndex)+20});
window.structEditFile = function(path)
{
//	structShowSubDialog();
//	jsPopup_editor.ShowDialog('/bitrix/admin/public_file_edit.php?bxpublic=Y&subdialog=Y&lang=<?=$encLang?>&site=<?=$encSite?>&templateID=<?=$encTemplateID?>&path='+path, {width: 780, height: 570, resize: false});
<?
	$url = $APPLICATION->GetPopupLink(Array(
		"URL"=>"/bitrix/admin/public_file_edit.php?bxpublic=Y&subdialog=Y&lang=".$encLang."&path=#PATH#&site=".$encSite, 
		"PARAMS"=>array("width"=>780, "height"=>570, "resize"=>true)), "editor");
	$url = str_replace("#PATH#", "'+path+'", $url);
?>
	<?=$url?>;

}

window.structEditFileHtml = function(path)
{
	//structShowSubDialog();
<?
	$url = $APPLICATION->GetPopupLink(Array(
		"URL"=>"/bitrix/admin/public_file_edit.php?bxpublic=Y&subdialog=Y&lang=".$encLang."&noeditor=Y&path=#PATH#&site=".$encSite, 
		"PARAMS"=>array("width"=>780, "height"=>570, "resize"=>true)), "editor");
	$url = str_replace("#PATH#", "'+path+'", $url);
?>
	<?=$url?>;
}

window.structFileProp = function(path)
{
	structShowSubDialog();
<?
	$url = $APPLICATION->GetPopupLink(Array(
		"URL"=>"/bitrix/admin/public_file_property.php?subdialog=Y&lang=".$encLang."&site=".$encSite."&path=#PATH#",
		"PARAMS" => Array("min_width"=>450, "min_height" => 250)), "subdialog");
	$url = str_replace("#PATH#", "'+path+'", $url);
?>
	<?=$url?>;
}

window.structDelFile = function(path)
{
	structShowSubDialog();
<?
	$url = $APPLICATION->GetPopupLink(array(
		"URL" => "/bitrix/admin/public_file_delete.php?subdialog=Y&lang=".$encLang."&site=".$encSite."&path=#PATH#",
		"PARAMS" => Array("min_width"=>250, "min_height" => 150, 'height' => 150, 'width' => 350)), "subdialog");
	$url = str_replace("#PATH#", "'+path+'", $url);
?>
	<?=$url?>;
}

window.structDelFolder = function(path)
{
	if(confirm('<?=CUtil::JSEscape(GetMessage("pub_struct_folder_del_confirm"))?>'))
		structReload(path, 'action=delfolder&<?="lang=".$encLang."&site=".$encSite."&".bitrix_sessid_get()?>&path='+path);
}

window.structShowDirMenu = function(el, dirsonly, arPerm)
{
	var path = el.getAttribute('__bx_path');
	var items = [
		{'ICONCLASS': 'panel-new-file', 'TEXT': '<?=CUtil::JSEscape(GetMessage("pub_struct_add_page"))?>', 'ONCLICK': 'structAddFile(\''+path+'\')', 'TITLE': '<?=CUtil::JSEscape(GetMessage("pub_struct_add_page_title"))?>', 'DISABLED':!arPerm.create_file},
		{'ICONCLASS': 'panel-new-folder', 'TEXT': '<?=CUtil::JSEscape(GetMessage("pub_struct_add_sect"))?>', 'ONCLICK': 'structAddFile(\''+path+'\', true)', 'TITLE': '<?=CUtil::JSEscape(GetMessage("pub_struct_add_sect_title"))?>', 'DISABLED':!arPerm.create_folder},
		{'SEPARATOR':true},
		{'ICONCLASS': 'panel-folder-props', 'TEXT': '<?=CUtil::JSEscape(GetMessage("pub_struct_folder_prop"))?>', 'ONCLICK': 'structEditFolder(\''+path+'\')', 'TITLE': '<?=CUtil::JSEscape(GetMessage("pub_struct_folder_prop_title"))?>', 'DISABLED':!arPerm.edit_folder},
		{'ICONCLASS': 'panel-folder-access', 'TEXT': '<?=CUtil::JSEscape(GetMessage("pub_struct_folder_access"))?>', 'ONCLICK': 'structAccessDialog(\''+path+'\')', 'TITLE': '<?=CUtil::JSEscape(GetMessage("pub_struct_folder_access_title"))?>', 'DISABLED':!arPerm.edit_perm}
	];
<?if($bFileman):?>
	if(unescape(path) != '/')
	{
		items[items.length] = {'SEPARATOR':true};
		items[items.length] = {'ICONCLASS': 'panel-folder-delete', 'TEXT': '<?=CUtil::JSEscape(GetMessage("pub_struct_folder_del"))?>', 'ONCLICK': 'structDelFolder(\''+path+'\')', 'TITLE': '<?=CUtil::JSEscape(GetMessage("pub_struct_folder_del_title"))?>', 'DISABLED':!arPerm.del_folder};
	}
	items[items.length] = {'SEPARATOR':true};
	items[items.length] = {'TEXT': '<?=CUtil::JSEscape(GetMessage("pub_struct_cp"))?>', 'ONCLICK': 'jsUtils.Redirect(arguments, \'/bitrix/admin/fileman_admin.php?lang=<?=$encLang?>&site=<?=$encSite?>&path='+path+'\')', 'TITLE': '<?=CUtil::JSEscape(GetMessage("pub_struct_cp_title"))?>'};
<?endif;?>
	
	window.structShowMenu(el, items, dirsonly);
}

window.structShowFileMenu = function(el, arPerm)
{
	var path = el.getAttribute('__bx_path');
	var ext = '';
	var pos = path.lastIndexOf('.');
	if(pos > -1)
		ext = path.substr(pos+1);

	var bText = false;
	var items = [];
	if(ext == 'php' || ext == 'htm' || ext == 'html')
	{
		items[items.length] = {'ICONCLASS': 'panel-edit-visual', 'TEXT': '<?=CUtil::JSEscape(GetMessage("pub_struct_file_edit"))?>', 'ONCLICK': 'structEditFile(\''+path+'\')', 'TITLE': '<?=CUtil::JSEscape(GetMessage("pub_struct_file_edit_title"))?>', 'DEFAULT':true, 'DISABLED':!arPerm.edit_file};
		items[items.length] = {'ICONCLASS': 'panel-edit-text', 'TEXT': '<?=CUtil::JSEscape(GetMessage("pub_struct_file_edit_html"))?>', 'ONCLICK': 'structEditFileHtml(\''+path+'\')', 'TITLE': '<?=CUtil::JSEscape(GetMessage("pub_struct_file_edit_html_title"))?>', 'DISABLED':!arPerm.edit_file};
		bText = true;
	}
	if(ext == 'php')
	{
		items[items.length] = {'SEPARATOR':true};
		items[items.length] = {'ICONCLASS': 'panel-file-props', 'TEXT': '<?=CUtil::JSEscape(GetMessage("pub_struct_file_prop"))?>', 'ONCLICK': 'structFileProp(\''+path+'\')', 'TITLE': '<?=CUtil::JSEscape(GetMessage("pub_struct_file_prop_title"))?>', 'DISABLED':!arPerm.edit_file};
		items[items.length] = {'ICONCLASS': 'panel-file-access', 'TEXT': '<?=CUtil::JSEscape(GetMessage("pub_struct_file_access"))?>', 'ONCLICK': 'structAccessDialog(\''+path+'\')', 'TITLE': '<?=CUtil::JSEscape(GetMessage("pub_struct_file_access_title"))?>', 'DISABLED':!arPerm.edit_perm};
	}
	if(items.length > 0)
		items[items.length] = {'SEPARATOR':true};
	items[items.length] = {'ICONCLASS': 'panel-file-delete', 'TEXT': (bText? '<?=CUtil::JSEscape(GetMessage("pub_struct_file_del"))?>':'<?=CUtil::JSEscape(GetMessage("pub_struct_file_del_title"))?>'), 'ONCLICK': 'structDelFile(\''+path+'\')', 'TITLE': '<?=CUtil::JSEscape(GetMessage("pub_struct_file_del_title1"))?>', 'DISABLED':!arPerm.del_file};
	
	window.structShowMenu(el, items);
}

window.structEditFileAction = function(el)
{
	var path = el.getAttribute('__bx_path');
	var pos = path.lastIndexOf('.');
	if(pos > -1)
	{
		var ext = path.substr(pos+1);
		if(ext == 'php' || ext == 'htm' || ext == 'html')
			structEditFile(path);
	}
}

window.structShowMenu = function(el, items, dirsonly)
{
	if(!window.structMenu)
	{
		window.structMenu = new PopupMenu('structure_menu');
		window.structMenu.Create(parseInt(<?=$obJSPopup->jsPopup?>.zIndex)+15);
	}

	if(window['structHint'+el.id])
		window['structHint'+el.id].Freeze();
	<?=$obJSPopup->jsPopup?>.bDenyEscKey = true;
	jsUtils.addCustomEvent('OnBeforeCloseDialog', window.structMenu.PopupHide, [], window.structMenu);
	
	//var dY = document.getElementById((dirsonly? 'bx_struct_dirs_content':'bx_popup_content')).scrollTop;
	var dY = 0; /*(
		dirsonly 
			? BX('bx_struct_dirs_content') 
			: BX.findParent(<?=$obJSPopup->jsPopup?>.GetContent(), {tag: 'DIV'})  // hack ;-(
		).scrollTop;*/
	
	var dPos = {'left':0, 'right':0, 'top':-dY+1, 'bottom':-dY+1};
		
	window.structMenu.ShowMenu(el, items, false, dPos, function(){
		setTimeout(function(){<?=$obJSPopup->jsPopup?>.bDenyEscKey = false}, 50);
		if(window['structHint'+el.id])
			window['structHint'+el.id].UnFreeze();
	});
}

window.structShowSettingsMenu = function(el)
{
	if(!window.structSettingsMenu)
		window.structSettingsMenu = new PopupMenu('structure_menu', parseInt(<?=$obJSPopup->jsPopup?>.zIndex)+10);

	var items = [
		{'ICONCLASS': (window.structOptions['show_all_files'] == true? 'checked':''), 
			'TEXT': '<?=CUtil::JSEscape(GetMessage("pub_struct_show_all"))?>', 
			'ONCLICK': 'structReload(\'<?=$encPath?>\', \'show_all_files='+(window.structOptions['show_all_files'] == true? 'N':'Y')+'\')', 
			'TITLE': '<?=CUtil::JSEscape(GetMessage("pub_struct_show_all_title"))?>'},
		{'ICONCLASS': (window.structOptions['show_file_info'] == true? 'checked':''), 
			'TEXT': '<?=CUtil::JSEscape(GetMessage("pub_struct_show_info"))?>', 
			'ONCLICK': 'structReload(\'<?=$encPath?>\', \'show_file_info='+(window.structOptions['show_file_info'] == true? 'N':'Y')+'\')', 
			'TITLE': '<?=CUtil::JSEscape(GetMessage("pub_struct_show_info_title"))?>'}
	];

	window.structSettingsMenu.SetItems(items);
	window.structSettingsMenu.BuildItems();

	<?=$obJSPopup->jsPopup?>.bDenyEscKey = true;
	jsUtils.addCustomEvent('OnBeforeCloseDialog', window.structSettingsMenu.PopupHide, [], window.structSettingsMenu);
	
	window.structSettingsMenu.ShowMenu(el, false, false, false, function(){setTimeout(function(){<?=$obJSPopup->jsPopup?>.bDenyEscKey = false}, 50)});
}

window.structOpenDirs = function(el)
{
	if(document.getElementById('bx_struct_dirs'))
		return;
	var strDiv = <?=$obJSPopup->jsPopup?>.Get();
	var div = jsFloatDiv.Create({
		'id':'bx_struct_dirs', 
		'className':'bx-popup-form', 
		'zIndex':parseInt(<?=$obJSPopup->jsPopup?>.zIndex)+10,
		'width':250, 'height':strDiv.offsetHeight
	});

	BX.showWait(strDiv);
	BX.ajax.get(
		'/bitrix/admin/public_structure.php?ajax=Y&reload=Y&<?="lang=".$encLang."&site=".$encSite."&path=".$encPath?>&dirsonly=Y', 
		function(result)
		{
			var container = document.getElementById('bx_struct_dirs');
			if(container)
			{
				container.innerHTML = 
					'<div class="bx-popup-title" id="bx_popup_title_dirs"><table cellspacing="0" class="bx-width100">'+
					'<tr>'+
					'	<td class="bx-width100 bx-title-text" onmousedown="jsFloatDiv.StartDrag(arguments[0], document.getElementById(\'bx_struct_dirs\'));">'+'<?=CUtil::JSEscape(GetMessage("pub_struct_sections"))?>'+'</td>'+
					'	<td class="bx-width0"><a class="bx-popup-close" href="javascript:void(0)" onclick="structCloseDirs()" title="'+'<?=CUtil::JSEscape(GetMessage("pub_struct_close"))?>'+'"></a></td>'+
					'</tr>'+
					'</table></div>'+
					'<div class="bx-popup-content" id="bx_struct_dirs_content"><div class="bx-popup-content-container" id="bx_struct_dirs_container">'+result+'</div></div>';

				var pos = jsUtils.GetRealPos(strDiv);
				var cont = document.getElementById('bx_struct_dirs_content');
				cont.style.height = pos["bottom"]-pos["top"]-31+'px';
				cont.style.width = 250-12+'px';
			
				jsDD.registerContainer(cont);

				div.style.zIndex = parseInt(<?=$obJSPopup->jsPopup?>.zIndex)+2;
				jsFloatDiv.Show(div, pos["left"]-250-1, pos["top"], 0, true);
				BX.closeWait(strDiv);
			}
		}
	);
	window.structUpdateTop = function() {div.style.top = strDiv.style.top;}
	BX.addCustomEvent(<?=$obJSPopup->jsPopup?>, 'onWindowClose', structCloseDirs);
	BX.addCustomEvent(<?=$obJSPopup->jsPopup?>, 'onWindowExpand', window.structUpdateTop);
	BX.addCustomEvent(<?=$obJSPopup->jsPopup?>, 'onWindowNarrow', window.structUpdateTop);
}

window.structCloseDirs = function()
{
	var div = document.getElementById('bx_struct_dirs');
	if(div)
	{
		jsFloatDiv.Close(div);
		div.parentNode.removeChild(div);
	}
	BX.removeCustomEvent(<?=$obJSPopup->jsPopup?>, 'onWindowClose', structCloseDirs);
	
	if (window.structUpdateTop)
	{
		BX.removeCustomEvent(<?=$obJSPopup->jsPopup?>, 'onWindowExpand', window.structUpdateTop);
		BX.removeCustomEvent(<?=$obJSPopup->jsPopup?>, 'onWindowNarrow', window.structUpdateTop);
		window.structUpdateTop = null;
	}
}

/* DD handlers */

window.Struct_DragStart = function()
{
	var div = document.body.appendChild(document.createElement("DIV"));
	div.style.position = 'absolute';
	div.style.zIndex = parseInt(<?=$obJSPopup->jsPopup?>.zIndex)+30;
	div.className = 'bx-struct-drag';
	this.__dragCopyDiv = div;

	var drag_div = this;
	if(!drag_div.getAttribute('__bx_path'))
		drag_div = jsUtils.FindNextSibling(drag_div, "div");

	div.innerHTML = drag_div.innerHTML;
	drag_div.className = 'bx-struct-name bx-struct-name-drag';

	window.structContainers = [BX.findParent(<?=$obJSPopup->jsPopup?>.GetContent(), {tag: 'DIV'}), document.getElementById('bx_struct_dirs_content')];
	window.structContainerPos = [];
	for(var i=0; i<window.structContainers.length; i++)
		if(window.structContainers[i])
			window.structContainerPos[i] = jsUtils.GetRealPos(window.structContainers[i]);

	var hint = window['structHint'+drag_div.id];
	if(hint)
		hint.Freeze();

	return true;
}

window.Struct_Drag = function(x, y)
{
	var div = this.__dragCopyDiv;
	div.style.left = x+'px';
	div.style.top = y+'px';

	for(var i=0; i<window.structContainers.length; i++)
	{
		if(window.structContainers[i] && x >= window.structContainerPos[i]["left"] && x <= window.structContainerPos[i]["right"])
		{
			if(y > window.structContainerPos[i]["bottom"])
				window.structContainers[i].scrollTop += 20;
			if(y < window.structContainerPos[i]["top"])
				window.structContainers[i].scrollTop -= 20;
		}
	}

	return true;
}

window.Struct_DragStop = function()
{
	this.__dragCopyDiv.parentNode.removeChild(this.__dragCopyDiv);
	this.__dragCopyDiv = null;

	var drag_div = this;
	if(!drag_div.getAttribute('__bx_path'))
		drag_div = jsUtils.FindNextSibling(drag_div, "div");

	drag_div.className = 'bx-struct-name';

	var hint = window['structHint'+drag_div.id];
	if(hint)
		hint.UnFreeze();
	
	return true;
}

window.Struct_DragHover = function(obDrag, x, y)
{
	this.className += ' bx-struct-dragover';
	return true;
}

window.Struct_DragOut = function(obDrag, x, y)
{
	this.className = this.className.replace(/\s*bx-struct-dragover/ig, "");
	return true;
}

window.Struct_DragFinish = function(obDrag, x, y, e)
{
	this.className = this.className.replace(/\s*bx-struct-dragover/ig, "");

	if(!obDrag.getAttribute('__bx_path'))
		obDrag = jsUtils.FindNextSibling(obDrag, "div");

	//can't move to itself
	if(this == obDrag)
		return true;
	//can't move to parent folder
	var enc_from = obDrag.getAttribute('__bx_path');
	var enc_to = this.getAttribute('__bx_path');
	var from = unescape(enc_from);
	var to = unescape(enc_to);
	if(to.charAt(to.length-1) != '/')
		to += '/';
	if(to == from.substring(0, from.lastIndexOf('/')+1))
		return true;
	//can't move folder to its subfolder
	if(to.indexOf(from+'/') == 0)
		return true;

	var mess;
	var bFolder = (obDrag.getAttribute('__bx_type') == 'D');
	if(e.ctrlKey)
		mess = (bFolder? '<?=CUtil::JSEscape(GetMessage("pub_struct_folder_confirm_copy"))?>' : '<?=CUtil::JSEscape(GetMessage("pub_struct_file_confirm_copy"))?>');
	else
		mess = (bFolder? '<?=CUtil::JSEscape(GetMessage("pub_struct_folder_confirm_move"))?>' : '<?=CUtil::JSEscape(GetMessage("pub_struct_file_confirm_move"))?>');

	mess = mess.replace(/#FROM#/g, from);
	mess = mess.replace(/#TO#/g, unescape(enc_to));
	if(confirm(mess))
		structReload(enc_to, 'action='+(e.ctrlKey? 'copy':'move')+'&from='+enc_from+'&to='+enc_to+'&<?=bitrix_sessid_get()?>');

	return true;
}

jsDD.Reset();
jsDD.registerContainer(BX.findParent(<?=$obJSPopup->jsPopup?>.GetContent(), {tag: 'DIV'}));

</script>

<?
$obJSPopup->ShowTitlebar();
$obJSPopup->StartDescription('bx-structure');
?>
<p><b><?echo GetMessage("pub_struct_desc_title")?></b></p>
<div class="bx-struct-settings" onclick="structShowSettingsMenu(this)" onmouseover="this.className+=' bx-struct-settings-over'" onmouseout="this.className=this.className.replace(/\s*bx-struct-settings-over/ig, '')" title="<?echo GetMessage("pub_struct_settings_title")?>"><?echo GetMessage("pub_struct_settings")?></div>
<div class="bx-struct-settings bx-struct-button" onclick="structOpenDirs(this)" onmouseover="this.className+=' bx-struct-settings-over'" onmouseout="this.className=this.className.replace(/\s*bx-struct-settings-over/ig, '')" title="<?echo GetMessage("pub_struct_folders_title")?>"><?echo GetMessage("pub_struct_folders_button")?></div>
<br />
<br style="clear:both;" />
<?
$obJSPopup->StartContent();
?>
<div id="structure_content">
<?
//display first level tree
$arRoot = __struct_get_file_info($DOC_ROOT, "/");
echo __struct_show_files(array($arRoot), $DOC_ROOT, "", $_GET["path"]);
?>
</div>
<?
$obJSPopup->ShowStandardButtons(array("close"));
?>
<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin_js.php");
?>