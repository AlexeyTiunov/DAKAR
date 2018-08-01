<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$player_type = $arParams['PLAYER_TYPE'];
$fp = $arParams['PATH'];

if (strlen($fp) > 0 && strpos($fp, '.') !== false)
$ext = (strlen($fp) > 0 && strpos($fp, '.') !== false) ? strtolower(GetFileExtension($fp)) : '';

if ($player_type == 'auto')
	$player_type = (in_array($ext, array('wmv', 'wma'))) ? 'wmv' : 'flv';

if ($ext == 'swf' && $arParams['ALLOW_SWF'] != 'Y')
	return CComponentUtil::__ShowError(GetMessage("SWF_DENIED"));

CUtil::InitJSCore(array('ajax'));

if (!function_exists(escapeFlashvar))
{
	function escapeFlashvar($str)
	{
		$str = str_replace('?', '%3F', $str);
		$str = str_replace('=', '%3D', $str);
		$str = str_replace('&', '%26', $str);
		return $str;
	}

	function isYes($str)
	{
		if (strtoupper($str) == 'Y')
			return 'true';
		return 'false';
	}

	function addFlashvar(&$str, $key, $value, $default = false)
	{
		if (!isset($value) || $value == '' || $value == $default)
			return;
		if (strlen($str) > 0)
			$str .= '&';
		$str .= $key.'='.escapeFlashvar($value);
	}

	function addWMVJSConfig(&$str, $key, $value, $default = false)
	{
		if (!isset($value) || $value == '' || $value === $default)
			return;
		if ($str != '{')
			$str .= ',';
		$str .= $key.': \''.$value.'\'';
	}

	function findCorrectFile($path, &$strWarn, $warning = false)
	{
		if (strpos($path, '://') !== false)
			return $path;
		$DOC_ROOT = $_SERVER["DOCUMENT_ROOT"];
		$path = Rel2Abs("/", $path);
		$path_ = $path;

		if (!file_exists($DOC_ROOT.$path))
		{
			if ($warning)
				$strWarn .= $warning."<br />";
			$path = $path_;
		}
		return $path;
	}
}

$warning = '';
$arResult["WIDTH"] = intval($arParams['WIDTH']);
if ($arResult["WIDTH"] <= 0)
	$arResult["WIDTH"] = 400;

$arResult["HEIGHT"] = intval($arParams['HEIGHT']);
if ($arResult["HEIGHT"] <= 0)
	$arResult["HEIGHT"] = 300;

// Only for render in editor
if ($arParams['BX_EDITOR_RENDER_MODE'] == 'Y')
{
?>
<div style="width: <?= $arResult["WIDTH"]?>px; height:<?= $arResult["HEIGHT"]?>px; background: #000; color: #fff; font-weight: bold; padding: 10px;"><?= GetMessage("ABOUT_TEXT")?></div>
<?
	return;
}

if (strlen($arParams['STREAMER']) > 0)
	$path = $arParams['PATH'];
else
	$path = findCorrectFile($arParams['PATH'], $warning, GetMessage("INCORRECT_FILE"));

$preview = (strlen($arParams['PREVIEW'])) ? findCorrectFile($arParams['PREVIEW'], $w = '') : '';
$logo = (strlen($arParams['LOGO']) > 0) ? findCorrectFile($arParams['LOGO'], $w = '') : '';

if (intval($arParams['VOLUME']) > 100)
	$arParams['VOLUME'] = 100;
if (intval($arParams['VOLUME']) < 0)
	$arParams['VOLUME'] = 0;
if (isset($arParams['PLAYER_ID']) && strlen($arParams['PLAYER_ID']) > 0)
	$arResult["ID"] = $arParams['PLAYER_ID'];
else
	$arResult["ID"] = "bx_".$player_type."_player_".rand();

if ($player_type == 'flv') // FLASH PLAYER
{
	$fv = '';
	addFlashvar($fv, 'file', $path, null);
	addFlashvar($fv, 'image', $preview, '');

	// Logo
	if ($logo != '' && $arParams["LOGO_POSITION"] != "none")
	{
		$logoLink = trim($arParams["LOGO_LINK"]) != "" ? $arParams["LOGO_LINK"] : GetMessage("ABOUT_LINK");
		addFlashvar($fv, 'logo.position', $arParams["LOGO_POSITION"]);
		addFlashvar($fv, 'logo.file', $logo);
		addFlashvar($fv, 'logo.link', $logoLink);
	}
	else
	{
		addFlashvar($fv, 'logo.hide', 'true');
	}

	// Skining
	$skin = rtrim($arParams['SKIN_PATH'], "/")."/".$arParams['SKIN'];
	$skinExt = strtolower(GetFileExtension($arParams['SKIN']));
	if ($arParams['SKIN'] != '' && $arParams['SKIN'] != 'default' && file_exists($_SERVER["DOCUMENT_ROOT"].$skin) && ($skinExt == 'swf' || $skinExt == 'zip'))
		addFlashvar($fv, 'skin', $skin);

	addFlashvar($fv, 'controlbar', $arParams['CONTROLBAR'], 'bottom');
	addFlashvar($fv, 'playlist', $arParams['PLAYLIST'], 'none');
	addFlashvar($fv, 'playlistsize', $arParams['PLAYLIST_SIZE'], '180');
	addFlashvar($fv, 'autostart', isYes($arParams['AUTOSTART']), 'false');
	addFlashvar($fv, 'repeat', $arParams['REPEAT'], 'none');
	addFlashvar($fv, 'volume', $arParams['VOLUME'], 90);
	addFlashvar($fv, 'mute', isYes($arParams['MUTE']), 'false');
	addFlashvar($fv, 'shuffle', isYes($arParams['SHUFFLE']), 'false');
	addFlashvar($fv, 'item', $arParams['START_ITEM'], '0');
	addFlashvar($fv, 'bufferlength', $arParams['BUFFER_LENGTH'], '1');

	// File info
	addFlashvar($fv, 'title', $arParams['FILE_TITLE']);
	addFlashvar($fv, 'duration', $arParams['FILE_DURATION']);
	addFlashvar($fv, 'author', $arParams['FILE_AUTHOR']);
	addFlashvar($fv, 'date', $arParams['FILE_DATE']);
	addFlashvar($fv, 'description', $arParams['FILE_DESCRIPTION']);

	// Append plugins
	if (is_array($arParams['PLUGINS']) && count($arParams['PLUGINS']) > 0)
		addFlashvar($fv, 'plugins', trim(implode(',', $arParams['PLUGINS']), ', '));

	// Append plugins vars
	for ($i = 0, $l = count($arParams['PLUGINS']); $i < $l; $i++)
	{
		if (strlen($arParams['PLUGINS'][$i]) <= 0)
			continue;

		$arFlashVars = explode("\n", trim($arParams["PLUGINS_".strtoupper($arParams['PLUGINS'][$i])]));
		for ($j = 0, $n = count($arFlashVars); $j < $n; $j++)
		{
			$var_ = explode("=", trim($arFlashVars[$j]));
			if (count($var_) < 2 || strlen($var_[0]) <= 0 || strlen($var_[1]) <= 0)
				continue;
			addFlashvar($fv, $var_[0], $var_[1]);
		}
	}

	// Append additional flashvars
	$arFlashVars = explode("\n", trim($arParams["ADDITIONAL_FLASHVARS"]));
	for ($j = 0, $n = count($arFlashVars); $j < $n; $j++)
	{
		$var_ = explode("=", trim($arFlashVars[$j]));
		if (count($var_) < 2 || strlen($var_[0]) <= 0 || strlen($var_[1]) <= 0)
			continue;
		addFlashvar($fv, $var_[0], $var_[1]);
	}

	addFlashvar($fv, 'dock', 'true');

	if (strpos($path, "youtube.") !== false)
		$arParams['PROVIDER'] = "youtube";

	addFlashvar($fv, 'provider', $arParams['PROVIDER']);

	if (strlen($arParams['STREAMER']) > 0)
		addFlashvar($fv, 'streamer', $arParams['STREAMER']);

	addFlashvar($fv, 'abouttext', GetMessage('ABOUT_TEXT'), '');
	addFlashvar($fv, 'aboutlink', GetMessage('ABOUT_LINK'), '');
	if ($arParams['CONTENT_TYPE'])
		addFlashvar($fv, 'type', $arParams['CONTENT_TYPE'], '');

	$arResult["FLASH_VARS"] = $fv;

	$arResult["WMODE"] = $arParams['WMODE'];
	$arResult["MENU"] = $arParams['HIDE_MENU'] == 'Y' ? 'false' : 'true';
}
else // WMV PLAYER
{
	$conf = "{";
	addWMVJSConfig($conf, 'file', $path, '');
	addWMVJSConfig($conf, 'image', $preview, '');

	addWMVJSConfig($conf, 'width', $arResult["WIDTH"]);
	addWMVJSConfig($conf, 'height', $arResult["HEIGHT"]);
	addWMVJSConfig($conf, 'backcolor', $arParams["CONTROLS_BGCOLOR"], 'FFFFFF');
	addWMVJSConfig($conf, 'frontcolor', $arParams["CONTROLS_COLOR"], '000000');
	addWMVJSConfig($conf, 'lightcolor', $arParams["CONTROLS_OVER_COLOR"], '000000');
	addWMVJSConfig($conf, 'screencolor', $arParams["SCREEN_COLOR"], '000000');

	addWMVJSConfig($conf, 'shownavigation', isYes($arParams["SHOW_CONTROLS"]), 'true');
	addWMVJSConfig($conf, 'showdigits', isYes($arParams["SHOW_DIGITS"]), 'true');

	addWMVJSConfig($conf, 'autostart', isYes($arParams["AUTOSTART"]), 'false');
	addWMVJSConfig($conf, 'repeat', $arParams["REPEAT"] != "none", 'false');
	addWMVJSConfig($conf, 'volume', $arParams['VOLUME'], 80);
	addWMVJSConfig($conf, 'bufferlength', $arParams['BUFFER_LENGTH'], 3);
	addWMVJSConfig($conf, 'link', $arParams['DOWNLOAD_LINK'], '');
	addWMVJSConfig($conf, 'linktarget', $arParams['DOWNLOAD_LINK_TARGET'], '_self');

	addWMVJSConfig($conf, 'title', $arParams['FILE_TITLE']);
	addWMVJSConfig($conf, 'duration', $arParams['FILE_DURATION']);
	addWMVJSConfig($conf, 'author', $arParams['FILE_AUTHOR']);
	addWMVJSConfig($conf, 'date', $arParams['FILE_DATE']);
	addWMVJSConfig($conf, 'description', $arParams['FILE_DESCRIPTION']);

	// Append additional js vars
	$arWMVVars = explode("\n", trim($arParams["ADDITIONAL_WMVVARS"]));
	for ($j = 0, $n = count($arWMVVars); $j < $n; $j++)
	{
		$var_ = explode("=", trim($arWMVVars[$j]));
		if (count($var_) == 2 && strlen($var_[0]) > 0 && strlen($var_[1]) > 0)
			addWMVJSConfig($conf, $var_[0], $var_[1]);
	}
	if ($arParams["WMODE_WMV"] == 'windowless')
		addWMVJSConfig($conf, 'windowless', 'true', '');
	$conf .= "}";

	$arResult["WMV_CONFIG"] = $conf;
	if ($arParams["SHOW_CONTROLS"] == 'Y')
		$arResult["HEIGHT"] += 20;

	$arResult["USE_JS_PLAYLIST"] = (($arParams["USE_PLAYLIST"] == 'Y'));
	$playlist_conf = false;
	if ($arResult["USE_JS_PLAYLIST"])
	{
		$playlist_conf = '{';
		addWMVJSConfig($playlist_conf, 'format', $arParams['PLAYLIST_TYPE'], 'xspf');
		addWMVJSConfig($playlist_conf, 'size', $arParams['PLAYLIST_SIZE'], '180');
		addWMVJSConfig($playlist_conf, 'image_height', $arParams['PLAYLIST_PREVIEW_HEIGHT']);
		addWMVJSConfig($playlist_conf, 'image_width', $arParams['PLAYLIST_PREVIEW_WIDTH']);
		addWMVJSConfig($playlist_conf, 'position', $arParams['PLAYLIST'] == 'right' ? 'right' : 'bottom', 'right');
		addWMVJSConfig($playlist_conf, 'path', $path, '');
		$playlist_conf .= "}";
	}
	$arResult["PLAYLIST_CONFIG"] = $playlist_conf;
}
$arResult["PLAYER_TYPE"] = $player_type;

if($arParams["USE_PLAYLIST"] == 'Y')
{
	$playlistExists = file_exists($_SERVER["DOCUMENT_ROOT"].$path);
	if (!$playlistExists)
		$warning = GetMessage('INCORRECT_PLAYLIST');

	//Icons
	$bShowIcon = ($USER->IsAuthorized() && ($APPLICATION->GetPublicShowMode() == 'configure' || $APPLICATION->GetPublicShowMode() == 'edit'));
	if ($bShowIcon && strlen($path) > 0)
	{
		$playlist_edit_url = $APPLICATION->GetPopupLink(
			array(
				"URL"=> "/bitrix/components/bitrix/player/player_playlist_edit.php?lang=".LANGUAGE_ID.
					"&site=".SITE_ID."&back_url=".urlencode($_SERVER["REQUEST_URI"]).
					"&path=".urlencode($path)."&contID=".urlencode($arResult["ID"]),
				"PARAMS" => array(
					'width' => '850',
					'height' => '400'
				)
			)
		);
		if (!$playlistExists)
			$warning .= '<br><a href="javascript:'.$playlist_edit_url.'">'.GetMessage("PLAYER_PLAYLIST_ADD").'</a>';
		$arIcons = Array(Array(
			"URL" => 'javascript:'.$playlist_edit_url,
			"ICON" => "playlist-edit",
			"TITLE" => ($playlistExists ? GetMessage("PLAYER_PLAYLIST_EDIT") : GetMessage("PLAYER_PLAYLIST_ADD")),
			"DEFAULT" => $APPLICATION->GetPublicShowMode() == 'edit',
		));
		echo '<script>if (JCPopup) {window.jsPopup_playlist = new JCPopup({suffix: "playlist", zIndex: 3000});}</script>'; // create instance of JCPopup: jsPopup_playlist
		$this->AddIncludeAreaIcons($arIcons);
	}
}

if (strlen($warning) > 0)
	return CComponentUtil::__ShowError($warning);

$this->IncludeComponentTemplate();
?>