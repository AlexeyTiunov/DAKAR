<?
/*
##############################################
# Bitrix: SiteManager                        #
# Copyright (c) 2002-2006 Bitrix             #
# http://www.bitrixsoft.com                  #
# mailto:admin@bitrixsoft.com                #
##############################################
*/
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/fileman/prolog.php");
if (!$USER->CanDoOperation('fileman_view_file_structure') || !$USER->CanDoOperation('fileman_edit_existent_files'))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/fileman/include.php");

function callback($buffer)
{
	return $buffer;
}

ob_start("callback");

if(CModule::IncludeModule("compression"))
	CCompress::Disable2048Spaces();

header("Content-type: text/xml");
echo '<'.'?xml version="1.0" encoding="'.LANG_CHARSET.'"?>';
echo "\r\n";

function bx_array_walk_recursive(&$array, $callback)
{
	foreach($array as $key=>$val)
	{
		if(is_array($array[$key]))
			bx_array_walk_recursive($array[$key], $callback);
		else
			$callback($array[$key], $key);
	}
}

function __unescapeuXXX(&$item, $key)
{
	global $APPLICATION;
	$item = $APPLICATION->UnJSEscape($item);
}

function __xmlspecialchars($str)
{
	return str_replace("'", "&apos;", htmlspecialchars($str));
}

bx_array_walk_recursive($_POST, '__unescapeuXXX');
?>
<params>
<?
$templateID = CFileMan::SecurePathVar($templateID);
switch($_REQUEST["op"])
{
	case "sitetemplateparams":
		$arResult = CFileman::GetAllTemplateParams($templateID, $site);
		$val = JSVal($arResult);
		?>
			<variable value="<?=__xmlspecialchars($val)?>" />
		<?
		break;
	case "getcomponents1":
		$arResult = CFileman::GetComponents1Params($templateID);
		$val = JSVal($arResult);
		?>
			<variable value="<?=__xmlspecialchars($val)?>" />
		<?
		break;
	case "componentconfig":
		$arValues = $_POST;
		if(!is_array($arValues))
			$arValues = Array();
		$arTemplates = CTemplates::GetByID($path, $arValues, $templateID);
		$arParameters = Array();
		if($arTemplates)
			$arParameters = $arTemplates["PARAMS"];
		?>
			<variable value="<?=htmlspecialchars(JSVal($arParameters))?>"/>
		<?
		break;
}
?>
</params>
<?
ob_end_flush();
define("ADMIN_AJAX_MODE", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin_after.php");
?>
