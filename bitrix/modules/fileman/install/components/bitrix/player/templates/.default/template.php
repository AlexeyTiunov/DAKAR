<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if ($arResult["PLAYER_TYPE"] == "flv"): // Attach Flash Player?>
<div id="<?=$arResult["ID"]?>_div">
<object
	classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
	codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0"
	width="<?=$arResult['WIDTH']?>"
	height="<?=$arResult['HEIGHT']?>"
	id="<?=$arResult["ID"]?>">
	<param name="movie" value="/bitrix/components/bitrix/player/mediaplayer/player.swf">
	<param name="quality" value="high">
	<param name="wmode" value="<?=$arResult['WMODE']?>">
	<param name="flashvars" value="<?=$arResult['FLASH_VARS']?>">
	<param name="allowscriptaccess" value="always">
	<param name="allowfullscreen" value="true">
	<embed
		id="<?=$arResult["ID"]?>_embed"
		name="<?=$arResult["ID"]?>_embed"
		src="/bitrix/components/bitrix/player/mediaplayer/player.swf"
		type="application/x-shockwave-flash"
		width="<?=$arResult['WIDTH']?>"
		height="<?=$arResult['HEIGHT']?>"
		allowscriptaccess="always"
		allowfullscreen="true"
		menu="<?=$arResult['MENU']?>"
		wmode="<?=$arResult['WMODE']?>"
		flashvars="<?=$arResult['FLASH_VARS']?>"
	/>
</object>
</div>
<script>
BX.loadScript('/bitrix/components/bitrix/player/mediaplayer/flvscript.js', function(){setTimeout(function(){
	if (window.showFLVPlayer)
		window.showFLVPlayer("<?=$arResult['ID']?>", "<?=GetMessage('INSTALL_FLASH_PLAYER')?>");
}, 100);});
</script><noscript><?=GetMessage('ENABLE_JAVASCRIPT')?></noscript>
<?elseif ($arResult["PLAYER_TYPE"] == "wmv"): // Attach WMV Player?>
<div id="<?=$arResult["ID"]?>"></div>
<script>
var arFiles = [
	'/bitrix/components/bitrix/player/wmvplayer/silverlight.js',
	'/bitrix/components/bitrix/player/wmvplayer/wmvplayer.js'
];
<?if ($arResult["USE_JS_PLAYLIST"]):?>
	var JSMESS = {
		ClickToPLay : "<?= GetMessage('JS_CLICKTOPLAY')?>",
		Link : "<?= GetMessage('JS_LINK')?>",
		PlayListError: "<?= GetMessage('JS_PLAYLISTERROR')?>"
	};
	BX.loadCSS('/bitrix/components/bitrix/player/templates/.default/wmvplaylist.css');
	arFiles.push('/bitrix/components/bitrix/player/templates/.default/wmvscript_playlist.js');
<?else:?>
	arFiles.push('/bitrix/components/bitrix/player/wmvplayer/wmvscript.js');
<?endif;?>

BX.loadScript(arFiles, function(){setTimeout(function(){
	if (window.showWMVPlayer)
		window.showWMVPlayer("<?=$arResult["ID"]?>", <?=$arResult['WMV_CONFIG']?>, <?=($arResult['PLAYLIST_CONFIG'] ? $arResult['PLAYLIST_CONFIG'] : '{}')?>);
}, 100);});

</script><noscript><?=GetMessage('ENABLE_JAVASCRIPT')?></noscript>
<?endif;?>