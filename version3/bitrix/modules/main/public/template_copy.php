<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_js.php");

$src_path = Rel2Abs("/", $_GET["src_path"]);
$src_line = intval($_GET["src_line"]);

if(!$USER->CanDoOperation('edit_php') && !$USER->CanDoFileOperation('fm_lpa', array($_GET["src_site"], $src_path)))
	die(GetMessage("ACCESS_DENIED"));

IncludeModuleLangFile(__FILE__);

CUtil::JSPostUnescape();

$obJSPopup = new CJSPopup('', 
	array(
		'TITLE' => GetMessage("template_copy_title"),
		'ARGS' => 'component_name='.urlencode($_GET["component_name"]).
				'&amp;component_template='.urlencode($_GET["component_template"]).
				'&amp;template_id='.urlencode($_GET["template_id"]).
				'&amp;lang='.urlencode(LANGUAGE_ID).
				'&amp;template_site_template='.urlencode($template_site_template).
				'&amp;src_path='.urlencode($_GET["src_path"]).
				'&amp;src_line='.intval($_GET["src_line"]).
				'&amp;src_site='.intval($_GET["src_site"]).
				'&amp;edit_file='.urlencode($_GET["edit_file"]).
				'&amp;back_path='.urlencode($_GET["back_path"]).
				'&amp;action=save'
	)
);

$strWarning = "";
$arTemplate = false;
$aComponent = false;


// try to read parameters from script file

/* Try to open script containing the component call */
if(!$src_path || $src_line <= 0)
	$strWarning .= GetMessage("comp_prop_err_param")."<br>";
else
{
	$abs_path = $_SERVER["DOCUMENT_ROOT"].$src_path;
	$filesrc = $APPLICATION->GetFileContent($abs_path);

	if(!$filesrc || $filesrc == "")
		$strWarning .= GetMessage("comp_prop_err_open")."<br>";
}

if($strWarning == "")
{
	/* parse source file for PHP code */
	$arComponents = PHPParser::ParseScript($filesrc);

	/* identify the component by line number */
	$arComponent = False;
	for ($i = 0, $cnt = count($arComponents); $i < $cnt; $i++)
	{
		$nLineFrom = substr_count(substr($filesrc, 0, $arComponents[$i]["START"]), "\n") + 1;
		$nLineTo = substr_count(substr($filesrc, 0, $arComponents[$i]["END"]), "\n") + 1;

		if ($nLineFrom <= $src_line && $nLineTo >= $src_line)
		{
			if ($arComponents[$i]["DATA"]["COMPONENT_NAME"] == $_GET["component_name"])
			{
				$arComponent = $arComponents[$i];
				break;
			}
		}
		if ($nLineTo > $src_line)
			break;
	}
}

if ($arComponent === false)
	$strWarning .= GetMessage("comp_prop_err_comp")."<br>";

if($strWarning == "")
{
	$arComponentDescription = CComponentUtil::GetComponentDescr($_GET["component_name"]);

	$arComponentParameters = CComponentUtil::GetComponentProps($_GET["component_name"], $arComponent["DATA"]["PARAMS"]);
	$arTemplateParameters = CComponentUtil::GetTemplateProps($_GET["component_name"], $_GET["component_template"], $_GET["template_id"], $arComponent["DATA"]["PARAMS"]);

	$arParameterGroups = array();
	if (isset($arComponentParameters["GROUPS"]) && is_array($arComponentParameters["GROUPS"]))
		$arParameterGroups = $arParameterGroups + $arComponentParameters["GROUPS"];
	if (isset($arTemplateParameters) && is_array($arTemplateParameters))
		$arParameterGroups = $arParameterGroups + array("TEMPLATE" => array("NAME" => GetMessage("comp_templ_template")));

	$arParameters = array();
	if (isset($arComponentParameters["PARAMETERS"]) && is_array($arComponentParameters["PARAMETERS"]))
		$arParameters = $arParameters + $arComponentParameters["PARAMETERS"];
	if (isset($arTemplateParameters) && is_array($arTemplateParameters))
		$arParameters = $arParameters + $arTemplateParameters;

	$templateSiteTemplate = "";
	$arTemplatesList = CComponentUtil::GetTemplatesList($_GET["component_name"], $_GET["template_id"]);
	for ($i = 0, $cnt = count($arTemplatesList); $i < $cnt; $i++)
	{
		if($arComponent["DATA"]["TEMPLATE_NAME"]<>"" && $arTemplatesList[$i]["NAME"] == $arComponent["DATA"]["TEMPLATE_NAME"]
			|| $arComponent["DATA"]["TEMPLATE_NAME"]=="" && $arTemplatesList[$i]["NAME"] == ".default")
		{
			$templateSiteTemplate = $arTemplatesList[$i]["TEMPLATE"];
			break;
		}
	}

	/* save parameters to file */
	if($_SERVER["REQUEST_METHOD"] == "POST" && $_REQUEST["action"] == "save" && $arComponent !== false && $arComponentDescription !== false && check_bitrix_sessid())
	{
		//check template name
		$sTemplateName = trim($_POST["TEMPLATE_NAME"]);
		if($sTemplateName == '' || !CBitrixComponentTemplate::CheckName($sTemplateName))
			$sTemplateName = '.default';

		if ($_POST["SITE_TEMPLATE"] != $_GET["template_id"] && $_POST["SITE_TEMPLATE"] != ".default")
			$_POST["USE_TEMPLATE"] = "N";

		if (CComponentUtil::CopyTemplate($arComponent["DATA"]["COMPONENT_NAME"], $arComponent["DATA"]["TEMPLATE_NAME"], ((StrLen($templateSiteTemplate) > 0) ? $templateSiteTemplate : False), $_POST["SITE_TEMPLATE"], $sTemplateName, False))
		{
			if ($_POST["USE_TEMPLATE"] == "Y")
			{
				$code = ($arComponent["DATA"]["VARIABLE"]?$arComponent["DATA"]["VARIABLE"]."=":"").
					"\$APPLICATION->IncludeComponent(\"".$arComponent["DATA"]["COMPONENT_NAME"]."\", ".
					"\"".$sTemplateName."\", ".
					"Array(\n\t".PHPParser::ReturnPHPStr2($arComponent["DATA"]["PARAMS"], $arParameters)."\n\t)".
					",\n\t".(strlen($arComponent["DATA"]["PARENT_COMP"]) > 0? $arComponent["DATA"]["PARENT_COMP"] : "false").
					(!empty($arComponent["DATA"]["FUNCTION_PARAMS"])? ",\n\t"."array(\n\t".PHPParser::ReturnPHPStr2($arComponent["DATA"]["FUNCTION_PARAMS"])."\n\t)" : "").
					"\n);";

				$filesrc_for_save = substr($filesrc, 0, $arComponent["START"]).$code.substr($filesrc, $arComponent["END"]);

				if(!$APPLICATION->SaveFileContent($abs_path, $filesrc_for_save))
					$strWarning .= GetMessage("comp_prop_err_save")."<br>";
			}

			if($strWarning == "")
			{
				$strJSText = 'window.location = window.location.href;';

				if ($_POST["EDIT_TEMPLATE"] == "Y")
				{
					$component = new CBitrixComponent();
					if ($component->InitComponent($arComponent["DATA"]["COMPONENT_NAME"], $_POST["TEMPLATE_NAME"]))
					{
						if ($component->InitComponentTemplate($_REQUEST["edit_file"], $_POST["SITE_TEMPLATE"]))
						{
							$template = & $component->GetTemplate();
							if (!is_null($template))
							{
								$strJSText = $APPLICATION->GetPopupLink(
									array(
										'URL' => '/bitrix/admin/public_file_edit_src.php?bxpublic=Y&lang='.LANGUAGE_ID.'&site='.SITE_ID.'&back_url='.urlencode($_REQUEST["back_path"]).'&path='.urlencode($template->GetFile()),
										"PARAMS" => Array("width"=>770, "height" => 570, "resize" => true),
									)
								);
							}
						}
					}
				}
?>
<script>
<?=$obJSPopup->jsPopup?>.Close();
//setTimeout(BX.showWait(), 30);

<?=$strJSText?>
</script>
<?
				die();
			}
		}
		else
		{
			if ($ex = $GLOBALS["APPLICATION"]->GetException())
				$strWarning .= $ex->GetString()."<br>";
			else
				$strWarning .= GetMessage("comp_templ_error_copy")."<br>";
		}
	}
}

$componentPath = CComponentEngine::MakeComponentPath($_GET["component_name"]);
if($arComponentDescription["ICON"] <> "" && is_file($_SERVER["DOCUMENT_ROOT"]."/bitrix/components".$componentPath.$arComponentDescription["ICON"]))
	$sIcon = "/bitrix/components".$componentPath.$arComponentDescription["ICON"];
else
	$sIcon = "/bitrix/images/fileman/htmledit/component.gif";

$sCurrentTemplateName = ($arComponent["DATA"]["TEMPLATE_NAME"] <> ""? htmlspecialchars($arComponent["DATA"]["TEMPLATE_NAME"]) : ".default");

$obJSPopup->ShowTitlebar();
$obJSPopup->StartDescription($sIcon);
?>
<?if($arComponentDescription["NAME"] <> ""):?>
<p title="<?echo GetMessage("comp_prop_name")?>"><b><?echo htmlspecialchars($arComponentDescription["NAME"])?></b></p>
<?endif;?>
<?if($arComponentDescription["DESCRIPTION"] <> ""):?>
<p title="<?echo GetMessage("comp_prop_desc")?>"><?echo htmlspecialchars($arComponentDescription["DESCRIPTION"])?></p>
<?endif;?>
<p class="note" title="<?echo GetMessage("comp_prop_path")?>"><a href="/bitrix/admin/fileman_admin.php?lang=<?echo LANGUAGE_ID?>&amp;path=<?echo urlencode("/bitrix/components".$componentPath)?>"><?echo htmlspecialchars($_GET["component_name"])?></a></p>
<?
if($_GET['system_template'] == 'Y')
	ShowNote(GetMessage("copy_comp_sys_templ"));

if($strWarning <> "")
{
	//ShowError($strWarning);
	$obJSPopup->ShowValidationError($strWarning);
	echo '<script>jsPopup.AdjustShadow()</script>';
}
?>

<?
$obJSPopup->StartContent();
?>
<input type="hidden" name="action" value="save" />
<script>
window.CheckSiteTemplate = function(el)
{
	var bList = (el.id == 'SITE_TEMPLATE_sel');
	if(el.form.USE_TEMPLATE)
	{
		el.form.USE_TEMPLATE.disabled = bList;
		el.form.USE_TEMPLATE.checked = !bList;
	}
	el.form.SITE_TEMPLATE[el.form.SITE_TEMPLATE.length-1].disabled = !bList;
}
</script>
<table cellspacing="0" class="bx-width100">
	<tr>
		<td class="bx-popup-label bx-width50"><?= GetMessage("comp_templ_cur_template") ?>:</td>
		<td><b><?=$sCurrentTemplateName?></b><?if($templateSiteTemplate==""):?> / <?echo GetMessage("comp_templ_system")?><?endif?></td>
	</tr>
<?
if($templateSiteTemplate<>""):
	$site_templates = CSiteTemplate::GetByID($templateSiteTemplate);
	if($site_template = $site_templates->Fetch())
		$sSiteTemplate = $site_template["NAME"];
?>
	<tr>
		<td class="bx-popup-label bx-width50"><?= GetMessage("comp_templ_cur_site_template")?>:</td>
		<td><b><?= htmlspecialchars($templateSiteTemplate)?></b><?if($sSiteTemplate <> "") echo " / ".htmlspecialchars($sSiteTemplate)?></td>
	</tr>
<?
endif;
?>
	<tr>
		<td class="bx-popup-label bx-width50"><?= GetMessage("comp_templ_new_tpl") ?>:</td>
		<td>
<?
$sParentComp = strtolower($arComponent["DATA"]["PARENT_COMP"]);
$bParentComp = ($sParentComp <> "" && $sParentComp !== "false" && $sParentComp !== "null");
if(!$bParentComp):
	//find next template name
	$def = (strlen($arComponent["DATA"]["TEMPLATE_NAME"]) > 0 && $arComponent["DATA"]["TEMPLATE_NAME"]<>".default"? rtrim($arComponent["DATA"]["TEMPLATE_NAME"], "0..9") : "template");
	if($def == '')
		$def = "template";
	$max = 0;
	foreach($arTemplatesList as $templ)
		if(strpos($templ["NAME"], $def) === 0 && ($v = intval(substr($templ["NAME"], strlen($def))))>$max)
			$max = $v;
?>
			<input type="text" name="TEMPLATE_NAME" value="<?echo (strlen($_REQUEST["TEMPLATE_NAME"]) > 0? htmlspecialchars($_REQUEST["TEMPLATE_NAME"]) : htmlspecialchars($def).($max+1)); ?>">
<?else:?>
			<?echo $sCurrentTemplateName?>
			<input type="hidden" name="TEMPLATE_NAME" value="<?echo $sCurrentTemplateName?>">
<?endif;?>
		</td>
	</tr>
	<tr>
		<td class="bx-popup-label bx-width50" valign="top"><?= GetMessage("comp_templ_new_template") ?>:</td>
		<td>
<input type="radio" name="SITE_TEMPLATE" value=".default" id="SITE_TEMPLATE_def"<?if($_REQUEST["SITE_TEMPLATE"] == "" || $_REQUEST["SITE_TEMPLATE"] == ".default") echo " checked"?> onclick="CheckSiteTemplate(this)"><label for="SITE_TEMPLATE_def"><?echo GetMessage("template_copy_def")?> / .default</label><br>
<?if($_GET["template_id"] <> "" && $_GET["template_id"] <> ".default"):?>
<input type="radio" name="SITE_TEMPLATE" value="<?echo htmlspecialchars($_GET["template_id"])?>" id="SITE_TEMPLATE_cur"<?if($_REQUEST["SITE_TEMPLATE"] == $_GET["template_id"]) echo " checked"?> onclick="CheckSiteTemplate(this)"><label for="SITE_TEMPLATE_cur"><?echo GetMessage("template_copy_cur")?> / <?echo htmlspecialchars($_GET["template_id"])?></label><br>
<?endif?>
<?
$bList = ($_REQUEST["SITE_TEMPLATE"] <> "" && $_REQUEST["SITE_TEMPLATE"] <> $_GET["template_id"] && $_REQUEST["SITE_TEMPLATE"] <> ".default")
?>
<input type="radio" name="SITE_TEMPLATE" value="" id="SITE_TEMPLATE_sel"<?if($bList) echo " checked"?> onclick="CheckSiteTemplate(this)"><label for="SITE_TEMPLATE_sel"><?echo GetMessage("template_copy_sel")?></label>
			<select name="SITE_TEMPLATE"<?if(!$bList) echo " disabled"?>>
				<?
				if ($handle = @opendir($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates"))
				{
					while (($file = readdir($handle)) !== false)
					{
						if ($file == "." || $file == ".." || $file == ".default" || $file == $_GET["template_id"])
							continue;

						if (is_dir($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".$file))
						{
							?><option value="<?= htmlspecialchars($file) ?>"<?if ((StrLen($_REQUEST["SITE_TEMPLATE"]) > 0 && $_REQUEST["SITE_TEMPLATE"] == $file) || (StrLen($_REQUEST["SITE_TEMPLATE"]) <= 0 && $file == $template_site_template)) echo " selected";?>><?= htmlspecialchars($file) ?></option><?
						}
					}
					@closedir($handle);
				}
				?>
			</select>
		</td>
	</tr>
<?if(!$bParentComp):?>
	<tr>
		<td class="bx-popup-label bx-width50"><?= GetMessage("comp_templ_use") ?>:</td>
		<td>
			<input type="checkbox" name="USE_TEMPLATE" value="Y"<?if (!($_REQUEST["action"] == "save" && $_REQUEST["USE_TEMPLATE"] <> "Y")) echo " checked";?><?if($bList) echo " disabled"?>>
		</td>
	</tr>
<?endif?>
	<tr>
		<td class="bx-popup-label bx-width50"><?= GetMessage("comp_templ_edit") ?>:</td>
		<td>
			<input type="checkbox" name="EDIT_TEMPLATE" value="Y"<?if (!($_REQUEST["action"] == "save" && $_REQUEST["EDIT_TEMPLATE"] <> "Y")) echo " checked";?>>
		</td>
	</tr>
</table>

<?
$obJSPopup->ShowStandardButtons();

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin_js.php");
?>