<?
##############################################
# Bitrix Site Manager                        #
# Copyright (c) 2002-2007 Bitrix             #
# http://www.bitrixsoft.com                  #
# mailto:admin@bitrixsoft.com                #
##############################################

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

if(!$USER->CanDoOperation('edit_php'))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

IncludeModuleLangFile(__FILE__);

function _normalizePath($strPath)
{
	$strResult = "";
	if (strlen($strPath)>0)
	{
		$strPath = str_replace("\\", "/", $strPath);
		$arPath = explode('/', $strPath);
		for ($i = count($arPath)-1; $i>=0; $i--)
		{
			if ($arPath[$i] == ".")
				;
			elseif ($arPath[$i] == "..")
				$i--;
			elseif (($arPath[$i] == '') && ($i!=(count($arPath)-1)) && ($i!=0))
				;
			else
				$strResult = $arPath[$i].($i!=(count($arPath)-1) ? '/'.$strResult : '');
		}
	}
	return $strResult;
}

$Warning_a = $Warning_b = false;
if(check_bitrix_sessid())
{
	$Warning_a = copy($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/backup/"._normalizePath($f_id), $_SERVER["DOCUMENT_ROOT"]."/"._normalizePath($f_id));
	
	$from = $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/restore.php";
	$to = $_SERVER["DOCUMENT_ROOT"]."/restore.php";

	$Warning_b = ($file = fopen($from, "rb"));
	$contents = fread($file, filesize($from));
	fclose($file);
	
	$contents = str_replace("%DEFAULT_LANG_ID%", LANG, $contents);

	$Warning_b &= ($file = fopen($to, "wb"));
	$Warning_b &= fputs($file, $contents);
	fclose($file);

	if($Warning_a && $Warning_b)
		LocalRedirect("/restore.php?lang=".LANG);
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$msg = !$Warning_a ? GetMessage("MAIN_EXEC_RESTORE_TEXT").htmlspecialchars(_normalizePath($f_id)) : "";
$msg .= (strlen($msg) > 0) ? "<br>" : "";
$msg .= !$Warning_b ? GetMessage("MAIN_EXEC_RESTORE_TEXT")."restore.php" : "";
$msg .= (strlen($msg) > 0) ? "<br>" : "";
$msg .= GetMessage("MAIN_EXEC_RESTORE_IMPOTANT");

CAdminMessage::ShowMessage(array(
	"MESSAGE" => GetMessage("MAIN_EXEC_RESTORE_MSG"),
	"DETAILS" => $msg,
	"HTML" => true));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>
