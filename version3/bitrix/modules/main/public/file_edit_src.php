<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_js.php");

if (!($USER->CanDoOperation('fileman_admin_files') || $USER->CanDoOperation('fileman_edit_existent_files')))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

// lpa is not allowed!
if (!($USER->CanDoOperation('edit_php')))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

CUtil::JSPostUnescape();

$obJSPopup = new CJSPopup("lang=".urlencode($_GET["lang"])."&site=".urlencode($_GET["site"])."&back_url=".urlencode($_GET["back_url"])."&path=".urlencode($_GET["path"])."&name=".urlencode($_GET["name"]));

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/fileman/include.php");

IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/fileman/admin/fileman_html_edit.php");
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/public/file_edit.php");
IncludeModuleLangFile(__FILE__);

$strWarning = "";

while (($l=strlen($path))>0 && $path[$l-1]=="/")
	$path = substr($path, 0, $l-1);

$bVarsFromForm = false;		// флаг, указывающий, откуда брать контент из файла или из запостченой формы
if (strlen($filename) > 0 && ($mess = CFileMan::CheckFileName($filename)) !== true)
{
	$filename2 = $filename;
	$filename = '';
	$strWarning = $mess;
	$bVarsFromForm = true;
}

// если новый файл и задано новое имя
if(strlen($new)>0 && strlen($filename)>0)
	$path = $path."/".$filename; // присвоим нашему пути это новое имя

$path = Rel2Abs("/", $path);
$site = CFileMan::__CheckSite($site);
if(!$site)
	$site = CSite::GetSiteByFullPath($_SERVER["DOCUMENT_ROOT"].$path);

$DOC_ROOT = CSite::GetSiteDocRoot($site);
$abs_path = $DOC_ROOT.$path;
$arPath = Array($site, $path);

if (!$USER->CanDoFileOperation('fm_view_file', $arPath))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$site_template = false;
$rsSiteTemplates = CSite::GetTemplateList($site);
while($arSiteTemplate = $rsSiteTemplates->Fetch())
{
	if(strlen($arSiteTemplate["CONDITION"])<=0)
	{
		$site_template = $arSiteTemplate["TEMPLATE"];
		break;
	}
}

if((strlen($new) <= 0 || strlen($filename)<=0) && !file_exists($abs_path))
{
	$p = strrpos($path, "/");
	if($p!==false)
	{
		$new = "Y";
		$filename = substr($path, $p+1);
		$path = substr($path, 0, $p);
	}
}

$NEW_ROW_CNT = 1;

$arParsedPath = CFileMan::ParsePath(Array($site, $path), true, false, "", $logical == "Y");

$isScriptExt = in_array(CFileman::GetFileExtension($path), CFileMan::GetScriptFileExt());

$only_read = false;
$arPath = Array($site, $path);

if(
	(
		strlen($new) > 0 &&
		!(
			$USER->CanDoOperation('fileman_admin_files') &&
			$USER->CanDoFileOperation('fm_create_new_file',$arPath)
		)
	) 
	||
	(
		strlen($new) < 0 &&
		!(
			$USER->CanDoOperation('fileman_edit_existent_files') &&
			$USER->CanDoFileOperation('fm_edit_existent_file',$arPath) &&
			$USER->CanDoFileOperation('fm_view_file',$arPath)
		)
	)
)
{
	$strWarning = GetMessage("ACCESS_DENIED");
}
else
{
	if (
		strlen($new) < 0 &&
		$USER->CanDoFileOperation('fm_view_file',$arPath) &&
		!$USER->CanDoFileOperation('fm_edit_existent_file',$arPath)
	)
		$only_read = true;

	if(strlen($new) > 0 && strlen($filename) > 0 && file_exists($abs_path))		// если мы хотим создать новый файл, но уже такой есть - ругаемся
	{
		$strWarning = GetMessage("FILEMAN_FILEEDIT_FILE_EXISTS")." ";
		$bEdit = false;
		$bVarsFromForm = true;
		$path = Rel2Abs("/", $arParsedPath["PREV"]);
		$arParsedPath = CFileMan::ParsePath($path, true, false, "", $logical == "Y");
		$abs_path = $DOC_ROOT.$path;
	}
	elseif(!$USER->IsAdmin() && substr(CFileman::GetFileName($abs_path), 0, 1)==".")
	{
		$strWarning = GetMessage("FILEMAN_FILEEDIT_BAD_FNAME")." ";
		$bEdit = false;
		$bVarsFromForm = true;
		$path = Rel2Abs("/", $arParsedPath["PREV"]);
		$arParsedPath = CFileMan::ParsePath($path, true, false, "", $logical == "Y");
		$abs_path = $DOC_ROOT.$path;
	}
	elseif(strlen($new) > 0)
	{
		if (strlen($filename) < 0)
			$strWarning = GetMessage("FILEMAN_FILEEDIT_FILENAME_EMPTY")." ";
		$bEdit = false;
	}
	else
	{
		if(!is_file($abs_path))
			$strWarning = GetMessage("FILEMAN_FILEEDIT_FOLDER_EXISTS")." ";
		else
			$bEdit = true;
	}
}

if(strlen($strWarning)<=0)
{
	if($bEdit)
		$filesrc_tmp = $APPLICATION->GetFileContent($abs_path);
	else
	{
		$arTemplates = CFileman::GetFileTemplates(LANGUAGE_ID, array($site_template));
		if(strlen($template)>0)
		{
			for ($i=0; $i<count($arTemplates); $i++)
			{
				if($arTemplates[$i]["file"] == $template)
				{
					$filesrc_tmp = CFileman::GetTemplateContent($arTemplates[$i]["file"], LANGUAGE_ID, array($site_template));
					break;
				}
			}
		}
		else
			$filesrc_tmp = CFileman::GetTemplateContent($arTemplates[0]["file"], LANGUAGE_ID, array($site_template));
	}

	if($REQUEST_METHOD=="POST" && strlen($save)>0 && !$only_read)
	{
		if(!check_bitrix_sessid())
		{
			$strWarning = GetMessage("FILEMAN_SESSION_EXPIRED");
			$bVarsFromForm = true;
		}

		// lpa was denied earlier, so use file src as is
		$filesrc_for_save = $_POST['filesrc'];

		if(strlen($strWarning) <= 0)
		{
			if (!CFileMan::CheckOnAllowedComponents($filesrc_for_save))
			{
				$str_err = $APPLICATION->GetException();
				if($str_err && ($err = $str_err ->GetString()))
					$strWarning .= $err;
				$bVarsFromForm = true;
			}
		}

		if(strlen($strWarning)<=0)
		{
			if(!$APPLICATION->SaveFileContent($abs_path, $filesrc_for_save))
			{
				if($str_err = $APPLICATION->GetException())
				{
					if ($str_err && ($err = $str_err->GetString()))
						$strWarning = $err;

					$bVarsFromForm = true;
					//$path = Rel2Abs("/", $arParsedPath["PREV"]);
					//$arParsedPath = CFileMan::ParsePath($path, true, false, "", $logical == "Y");
					//$abs_path = $DOC_ROOT.$path;
				}

				if (empty($strWarning))
				{
					$strWarning = GetMessage("FILEMAN_FILE_SAVE_ERROR")." ";
				}
			}
			else
				$bEdit = true;

			if(strlen($strWarning)<=0)
			{
?>
<script type="text/javascript" bxrunfirst="true">
top.BX.showWait();
top.BX.reload('<?=CUtil::JSEscape($_REQUEST["back_url"])?>', true);
top.<?=$obJSPopup->jsPopup?>.Close();
</script>
<?
				die();
			}

			$filesrc_tmp = $filesrc_for_save;

			$path = Rel2Abs("/", $path);
			$arParsedPath = CFileMan::ParsePath($path, true, false, "", $logical == "Y");
			$abs_path = $DOC_ROOT.$path;
		}
		
		if (strlen($strWarning) > 0)
			$obJSPopup->ShowValidationError($strWarning);
	}
}

if(!$bVarsFromForm)
{
	if(!$bEdit && strlen($filename)<=0)
		$filename = "untitled.php";

	$filesrc = $filesrc_tmp;
}
else
	$filesrc = $_POST['filesrc'];


/*************************************************/


foreach ($arParsedPath["AR_PATH"] as $chainLevel)
{
	$adminChain->AddItem(
		array(
			"TEXT" => htmlspecialcharsex($chainLevel["TITLE"]),
			"LINK" => ((strlen($chainLevel["LINK"]) > 0) ? $chainLevel["LINK"] : ""),
		)
	);
}

$obJSPopup->ShowTitlebar(($bEdit ? GetMessage("FILEMAN_FILEEDIT_PAGE_TITLE")." \"".$arParsedPath["LAST"]."\"" : GetMessage("FILEMAN_NEWFILEEDIT_TITLE")).': '.htmlspecialcharsex($_GET['path']));

$editor = '/bitrix/admin/fileman_file_edit.php?path='.urlencode($path).'&amp;full_src=Y&amp;site='.$site.'&amp;lang='.LANGUAGE_ID.'&amp;back_url='.urlencode($_GET["back_url"]);
/*$obJSPopup->StartDescription();
echo '<p class="note"><a href="'.$editor.'" title="'.GetMessage("public_file_edit_edit_cp").'">'.htmlspecialchars($path).'</a></p>';
$obJSPopup->EndDescription();*/

$obJSPopup->StartContent(
	array(
		'style' => "height: 470px; padding: 0px; overflow: hidden; ",
		//'style' => "border: 0; padding: 5px; height: 505px; overflow: hidden; margin: 0px",
		//'class' => "bx-content-editor"
	)
);
?>

<?//CAdminMessage::ShowMessage($strWarning);?>

<input type="hidden" name="site" value="<?= htmlspecialchars($site) ?>">
<input type="hidden" name="path" value="<?= htmlspecialchars($path) ?>">
<input type="hidden" name="save" value="Y">
<input type="hidden" name="lang" value="<?echo LANG ?>">
<?=bitrix_sessid_post()?>
<?if(!$bEdit):?>
	<input type="hidden" name="new" value="y">
<?endif?>
<input type="hidden" name="save" value="Y">
<input type="hidden" name="template" value="<?echo htmlspecialchars($template)?>">
<input type="hidden" name="back_url" value="<?=htmlspecialchars($back_url)?>">

	<?if(!$bEdit):?>
		<?$arTemplates = CFileman::GetFileTemplates(LANGUAGE_ID, array($site_template));?>
		<tr>
			<td width="30%"><?echo GetMessage("FILEMAN_FILEEDIT_TEMPLATE")?></td>
			<td width="70%">
				<select name="template" onchange="window.location='/bitrix/admin/fileman_file_edit.php?lang=<?echo LANG?>&site=<?=Urlencode($site)?>&path=<?echo UrlEncode($path)?><? echo ($full_src=="Y" ? "&full_src=Y" : "")?>&new=y&template='+escape(this[this.selectedIndex].value)">
					<?for($i=0; $i<count($arTemplates); $i++):?>
					<option value="<?echo htmlspecialchars($arTemplates[$i]["file"])?>"<?if($template==$arTemplates[$i]["file"])echo " selected"?>><?echo htmlspecialchars($arTemplates[$i]["name"])?></option>
					<?endfor;?>
				</select>
			</td>
		</tr>
		<tr>
			<td valign="top"><?echo GetMessage("FILEMAN_FILEEDIT_NAME")?></td>
			<td>
				<?if (isset($filename2))
					$filename = $filename2;?>
				<input type="text" name="filename" id="__filename" style="float: left;" size="30" maxlength="255" value="<?echo htmlspecialchars($filename)?>">
			<table id='jserror_name' style="visibility:hidden"><tr><td valign="top">
			<IMG src="/bitrix/themes/.default/images/icon_warn.gif" title="<?=GetMessage("FILEMAN_NAME_ERROR");?>">
			</td><td class="jserror">
			<?=GetMessage("FILEMAN_NAME_ERROR");?>
			</td></tr></table>
			<script>
			var oInput = document.getElementById('__filename');
			var erTable = document.getElementById('jserror_name');
			oInput.onkeypress = function()
			{
				var _this = this;
				setTimeout(function()
					{
						var val = _this.value;
						var new_val = val.replace(/[^a-zA-Z0-9\s!\$&\(\)\[\]\{\}\-\.;=@\^_\~]/i, '');
						if (val !== new_val)
							erTable.style.visibility = 'visible';
						else
							erTable.style.visibility = 'hidden';
					}, 1
				);
			}
			</script>
			</td>
		</tr>
	<?else:?>
<?
	require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/interface/init_admin.php");
	//$arPos = array('filesrc_width' => '745', 'filesrc_height' => '455');
	/*if (class_exists('CUserOptions'))
	{
		$arPos = CUserOptions::GetOption(
			'jsPopup',
			'size_'.$APPLICATION->GetCurPage(),
			$arPos
		);
	}*/
?>
	<textarea id="filesrc" name="filesrc" style="height: 100%; width: 100%;"><?echo htmlspecialchars($filesrc)?></textarea>
	<script type="text/javascript">
var wnd = BX.WindowManager.Get();
wnd.PARTS.CONTENT.style.overflow = 'hidden';

var border = null, ta = null;
BX.addCustomEvent(wnd, 'onWindowResizeExt', function(data) {
	if (null == ta) ta = BX('filesrc');
	if (null == border) border = parseInt(BX.style(ta, 'border-left-width')) + parseInt(BX.style(ta, 'border-right-width'));
	if (isNaN(border)) border = 0;

	if (data.height) ta.style.height = (data.height - border) + 'px';
	if (data.width) ta.style.width = (data.width - border) + 'px';
});
wnd.adjustSize();
	</script>
	<?endif?>
<?
$obJSPopup->StartButtons();
?>
<input type="button" id="btn_popup_save" name="btn_popup_save" value="<?=GetMessage("JSPOPUP_SAVE_CAPTION")?>" onclick="<?=$obJSPopup->jsPopup?>.PostParameters()" title="<?=GetMessage("JSPOPUP_SAVE_CAPTION")?>" />
<?
$obJSPopup->ShowStandardButtons(array('cancel'));
$obJSPopup->EndButtons();

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin_js.php");
?>