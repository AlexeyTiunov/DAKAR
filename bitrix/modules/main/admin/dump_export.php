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

LocalRedirect(BX_PERSONAL_ROOT."/backup/"._normalizePath($f_id));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin_after.php");
?>