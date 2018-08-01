<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
/*
Authorization form (for prolog)
Params:
	REGISTER_URL => path to page with authorization script (component?)
	PROFILE_URL => path to page with profile component
*/

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

$arParams["REGISTER_URL"] = strlen($arParams["REGISTER_URL"]) > 0 ? $arParams["REGISTER_URL"] : $APPLICATION->GetCurPageParam("", array_merge($arParamsToDelete, array("logout_butt")));

$bRegisterURLque = strpos($arParams["REGISTER_URL"], "?") !== false;
$bProfileURLque = strpos($arParams["PROFILE_URL"], "?") !== false;

$arResult['ERROR'] = false;
$arResult['SHOW_ERRORS'] = (array_key_exists('SHOW_ERRORS', $arParams) && $arParams['SHOW_ERRORS'] == 'Y') ? 'Y' : 'N';

if (!$USER->IsAuthorized())
{
	$arResult["FORM_TYPE"] = "login";

	$arResult["STORE_PASSWORD"] = COption::GetOptionString("main", "store_password", "Y") == "Y" ? "Y" : "N";

	$arResult["NEW_USER_REGISTRATION"] = COption::GetOptionString("main", "new_user_registration", "N") == "Y" ? "Y" : "N";
	$arResult['USE_OPENID'] = COption::GetOptionString('main', 'auth_openid', 'N') == 'Y' ? 'Y' : 'N';
	$arResult['USE_LIVEID'] = COption::GetOptionString('main', 'auth_liveid', 'N') == 'Y' ? 'Y' : 'N';

	if ($arResult['USE_OPENID'] == 'Y' && ($arResult['OPENID_STEP'] = COpenIDClient::GetOpenIDAuthStep()) > 0)
	{
		$obOpenID = new COpenIDClient();

		if ($arResult['OPENID_STEP'] == 2)
		{
			if (!$obOpenID->Authorize())
			{
				$arResult['ERROR'] = true;
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
				$arResult['ERROR'] = true;
			}
		}
	}

	if(defined("AUTH_404"))
	{
		$arResult["AUTH_URL"] = POST_FORM_ACTION_URI;
	}
	else
	{
		$arResult["AUTH_URL"] = $APPLICATION->GetCurPageParam("login=yes", array_merge($arParamsToDelete, array("logout_butt", "backurl")), $get_index_page=false);
	}

	$arResult["BACKURL"] = $APPLICATION->GetCurPageParam("", array_merge($arParamsToDelete, array("logout_butt")), $get_index_page=false);

	$arResult["AUTH_REGISTER_URL"] = $arParams["REGISTER_URL"] . ($bRegisterURLque ? "&" : "?") . "register=yes&backurl=" .
		urlencode($APPLICATION->GetCurPageParam("", array_merge($arParamsToDelete, array("logout_butt", "backurl")), $get_index_page=false));


	$arResult["AUTH_FORGOT_PASSWORD_URL"] = $arParams["REGISTER_URL"] . ($bRegisterURLque ? "&" : "?") . "forgot_password=yes&backurl=".urlencode($APPLICATION->GetCurPageParam("", array_merge($arParamsToDelete, array("logout_butt", "backurl")), $get_index_page=false));

	foreach ($arResult as $key => $value)
	{
		if (!is_array($value)) $arResult[$key] = htmlspecialchars($value);
	}

	$arResult["POST"] = array();
	foreach ($_POST as $vname=>$vvalue)
	{
		if ($vname=="USER_LOGIN" || is_array($vvalue) || $vname=="backurl" || $vname == 'OPENID_IDENTITY') continue;
		$arResult["POST"][htmlspecialchars($vname)] = htmlspecialchars($vvalue);
	}

	if(defined("HTML_PAGES_FILE") && !defined("ERROR_404"))
		$arResult["USER_LOGIN"] = "";
	else
		$arResult["USER_LOGIN"] = htmlspecialchars($_COOKIE[COption::GetOptionString("main", "cookie_name", "BITRIX_SM")."_LOGIN"]);

	if ($arResult['USE_LIVEID'] == 'Y')
	{
		$wll = new WindowsLiveLogin();

		$wll->setAppId(COption::GetOptionString('main', 'liveid_appid'));
		$wll->setSecret(COption::GetOptionString('main', 'liveid_secret'));

		$arResult['LIVEID_APPID'] = $wll->getAppId();
		$arResult['LIVEID_LOGIN_LINK'] = $wll->getLoginUrl();

		$_SESSION['BX_LIVEID_LAST_PAGE'] = $APPLICATION->GetCurPageParam('', array('logout'));
	}

	if ($APPLICATION->arAuthResult)
	{
		$arResult['ERROR'] = true;
		$arResult['ERROR_MESSAGE'] = $APPLICATION->arAuthResult;
	}
	elseif ($arResult['ERROR'])
	{
		$ex = $APPLICATION->GetException();
		if ($ex)
			$arResult['ERROR_MESSAGE'] = $ex->GetString();
	}

	if($APPLICATION->NeedCAPTHAForLogin($arResult["USER_LOGIN"]))
	{
		$arResult["CAPTCHA_CODE"] = $APPLICATION->CaptchaGetCode();
	}
	else
	{
		$arResult["CAPTCHA_CODE"] = false;
	}
}
else
{
	$arResult["FORM_TYPE"] = "logout";

	if(defined("AUTH_404"))
	{
		$arResult["AUTH_URL"] = POST_FORM_ACTION_URI;
	}
	else
	{
		$arResult["AUTH_URL"] = $APPLICATION->GetCurPageParam("", $arParamsToDelete, $get_index_page=false);
	}

	$arResult["AUTH_URL"] = $APPLICATION->GetCurPageParam("", $arParamsToDelete, $get_index_page=false);

	$arResult["BACKURL"] = $APPLICATION->GetCurPageParam("", $arParamsToDelete, $get_index_page=false);

	$arResult["PROFILE_URL"] = $arParams["PROFILE_URL"] . ($bProfileURLque ? "&" : "?") . "backurl=".urlencode($APPLICATION->GetCurPageParam("", array(
			"login",
		    "logout",
		    "register",
		    "forgot_password",
		    "change_password",
			"logout_butt",
			"backurl",
		), $get_index_page=false));

	$arResult["USER_NAME"] = htmlspecialcharsEx($USER->GetFullName());
	$arResult["USER_LOGIN"] = htmlspecialcharsEx($USER->GetLogin());

	foreach ($arResult as $key => $value)
	{
		if (!is_array($value)) $arResult[$key] = htmlspecialchars($value);
	}

	$arResult["GET"] = array();
	foreach ($_GET as $vname=>$vvalue)
	{
		if (!is_array($vvalue) && $vname!="backurl" && $vname != "login" && $vname != "")
		{
			$arResult["GET"][htmlspecialchars($vname)] = htmlspecialchars($vvalue);
		}
	}
}

$this->IncludeComponentTemplate();
?>