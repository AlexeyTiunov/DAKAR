<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/*
Component: bitrix:system.authorize
Parameters:
	AUTH_RESULT - Authorization result message
	NOT_SHOW_LINKS - Whether to show links to register page && password restoration (Y/N)
*/

$arParams["NOT_SHOW_LINKS"] = ($arParams["NOT_SHOW_LINKS"] == "Y" ? "Y" : "N");

$arResult = array();

$arParamsToDelete = array(
	"login",
	"logout",
	"register",
	"forgot_password",
	"change_password",
	"confirm_registration",
	"confirm_code",
	"confirm_user_id",
);

if(defined("AUTH_404"))
{
	$arResult["AUTH_URL"] = POST_FORM_ACTION_URI;
}
else
{
	$arResult["AUTH_URL"] = $APPLICATION->GetCurPageParam("login=yes",$arParamsToDelete);
}

$arResult["BACKURL"] = $APPLICATION->GetCurPageParam("",$arParamsToDelete);


$arResult["AUTH_REGISTER_URL"] = $APPLICATION->GetCurPageParam("register=yes",$arParamsToDelete);
$arResult["AUTH_FORGOT_PASSWORD_URL"] = $APPLICATION->GetCurPageParam("forgot_password=yes",$arParamsToDelete);
$arResult["AUTH_CHANGE_PASSWORD_URL"] = $APPLICATION->GetCurPageParam("change_password=yes",$arParamsToDelete);

foreach ($arResult as $key => $value)
{
	if (!is_array($value)) $arResult[$key] = htmlspecialchars($value);
}

$arResult["POST"] = array();
foreach ($_POST as $vname=>$vvalue)
{
	if ($vname=="USER_LOGIN") continue;
	if (is_array($vvalue)) continue;
	$arResult["POST"][htmlspecialchars($vname)] = htmlspecialchars($vvalue);
}

$arResult["LAST_LOGIN"] = htmlspecialchars($_COOKIE[COption::GetOptionString("main", "cookie_name", "BITRIX_SM")."_LOGIN"]);
$arResult["STORE_PASSWORD"] = COption::GetOptionString("main", "store_password", "Y") == "Y" ? "Y" : "N";

$arResult["NEW_USER_REGISTRATION"] = COption::GetOptionString("main", "new_user_registration", "N") == "Y" ? "Y" : "N";
$arResult['USE_OPENID'] = COption::GetOptionString('main', 'auth_openid', 'N') == 'Y' ? 'Y' : 'N';
$arResult['USE_LIVEID'] = COption::GetOptionString('main', 'auth_liveid', 'N') == 'Y' ? 'Y' : 'N';

if (!$USER->IsAuthorized() && $arResult['USE_OPENID'] == 'Y' && ($arResult['OPENID_STEP'] = COpenIDClient::GetOpenIDAuthStep()) > 0)
{
	$obOpenID = new COpenIDClient();

	if ($arResult['OPENID_STEP'] == 2)
	{
		if (!$obOpenID->Authorize())
		{
			$ex = $APPLICATION->GetException();
			if ($ex)
				$arResult['ERROR_MESSAGE'] = $ex->GetString();
		}
	}
	elseif ($arResult['OPENID_STEP'] == 1)
	{
		if ($url = $obOpenID->GetRedirectUrl($_POST['OPENID_IDENTITY']))
		{
			LocalRedirect($url);
		}
		else
		{
			$ex = $APPLICATION->GetException();
			if ($ex)
				$arResult['ERROR_MESSAGE'] = $ex->GetString();
		}
	}
}

if ($arResult['USE_LIVEID'] == 'Y')
{
	$wll = new WindowsLiveLogin();

	$wll->setAppId(COption::GetOptionString('main', 'liveid_appid'));
	$wll->setSecret(COption::GetOptionString('main', 'liveid_secret'));

	$arResult['LIVEID_APPID'] = $wll->getAppId();
	$arResult['LIVEID_LOGIN_LINK'] = $wll->getLoginUrl();

	if (!$USER->IsAuthorized())
	{
		$_SESSION['BX_LIVEID_LAST_PAGE'] = $APPLICATION->GetCurPageParam('', array('logout'));
	}
}

if($APPLICATION->NeedCAPTHAForLogin($arResult["LAST_LOGIN"]))
{
	$arResult["CAPTCHA_CODE"] = $APPLICATION->CaptchaGetCode();
}
else
{
	$arResult["CAPTCHA_CODE"] = false;
}

$this->IncludeComponentTemplate();
?>