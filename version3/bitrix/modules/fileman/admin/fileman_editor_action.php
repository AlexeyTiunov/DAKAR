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

if(CModule::IncludeModule("compression"))
	CCompress::Disable2048Spaces();

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : false;

if (!check_bitrix_sessid())
	die('<!--BX_EDITOR_DUBLICATE_ACTION_REQUEST'.bitrix_sessid().'-->');	

if ($action == 'sitetemplateparams')
{
	$templateID = $_GET['templateID'];
	?>
	<script>
	window.bx_template_params = <?= CUtil::PhpToJSObject(CFileman::GetAllTemplateParams($templateID, $site))?>;
	</script>
	<?
}
elseif ($action == 'getcomponents1')
{
	$templateID = $_GET['templateID'];
	?>
	<script>
	window.arComp1Elements = <?= CUtil::PhpToJSObject(CFileman::GetComponents1Params($templateID))?>;
	</script>
	<?
}
elseif ($action == 'component1config')
{
	$arValues = is_array($_POST['params']) ? $_POST['params'] : Array();
	$templateID = $_GET['templateID'];

	$arTemplates = CTemplates::GetByID($path, $arValues, $templateID);
	$arParameters = Array();
	if($arTemplates)
		$arParameters = $arTemplates["PARAMS"];
	?>
	<script>
	window.bx_componentconfig = <?= CUtil::PhpToJSObject($arParameters)?>;
	</script>
	<?
	break;
}

define("ADMIN_AJAX_MODE", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin_after.php");
?>
