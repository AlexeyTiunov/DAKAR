<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$GLOBALS['APPLICATION']->AddHeadString('<script type="text/javascript" src="/bitrix/components/bitrix/player/wmvplayer/silverlight.js?v='.filemtime($_SERVER['DOCUMENT_ROOT'].'/bitrix/components/bitrix/player/wmvplayer/silverlight.js').'"></script>', true);
$GLOBALS['APPLICATION']->AddHeadString('<script type="text/javascript" src="/bitrix/components/bitrix/player/wmvplayer/wmvplayer.js?v='.filemtime($_SERVER['DOCUMENT_ROOT'].'/bitrix/components/bitrix/player/wmvplayer/wmvplayer.js').'"></script>', true);
$GLOBALS['APPLICATION']->AddHeadString('<script type="text/javascript" src="/bitrix/components/bitrix/player/mediaplayer/flvscript.js?v='.filemtime($_SERVER['DOCUMENT_ROOT'].'/bitrix/components/bitrix/player/mediaplayer/flvscript.js').'"></script>', true);
?>
<?if ($arResult["PLAYER_TYPE"] == "flv"): // Attach Flash Player?>
<div id="myIDflv_<?=intval($arParams['ADDITIONAL_PARAMS']['NUM'])?>" style="display:none; height: <?=$arResult['HEIGHT']+intval($arParams['ADDITIONAL_PARAMS']['HEIGHT_CORRECT']['FLV']);?>px">
	<object 
	    id="FLVplayer_<?=intval($arParams['ADDITIONAL_PARAMS']['NUM'])?>" width="<?=$arResult['WIDTH']?>" height="<?=$arResult['HEIGHT']+intval($arParams['ADDITIONAL_PARAMS']['HEIGHT_CORRECT']['FLV'])?>"        
	    classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab">
	    <param name="movie" value="/bitrix/components/bitrix/player/mediaplayer/player.swf" />
	    <param name="allowScriptAccess" value="always" />
	    <param name="FlashVars" value="<?=$arResult['FLASH_VARS']?>" />
		<embed
			name="FLVplayer_<?=intval($arParams['ADDITIONAL_PARAMS']['NUM'])?>"
			src="/bitrix/components/bitrix/player/mediaplayer/player.swf"
			width="<?=$arResult['WIDTH']?>"
			height="<?=$arResult['HEIGHT']+intval($arParams['ADDITIONAL_PARAMS']['HEIGHT_CORRECT']['FLV'])?>"
			allowscriptaccess="always"
			allowfullscreen="true"
			menu="<?=$arResult['MENU']?>"
			wmode="<?=$arResult['WMODE']?>"
			flashvars="<?=$arResult['FLASH_VARS']?>"
		/>
</object>
</div>
<div id="myIDwmv_<?=intval($arParams['ADDITIONAL_PARAMS']['NUM'])?>" style="display:none; height: <?=$arResult['HEIGHT']+intval($arParams['ADDITIONAL_PARAMS']['HEIGHT_CORRECT']['FLV']);?>px"></div>
<script>
showFLVPlayer('myIDflv_<?=intval($arParams['ADDITIONAL_PARAMS']['NUM'])?>', "<?=GetMessage('INSTALL_FLASH_PLAYER')?>");
</script><noscript><?=GetMessage('ENABLE_JAVASCRIPT')?></noscript>
<?elseif ($arResult["PLAYER_TYPE"] == "wmv"): // Attach WMV Player?>
<div id="myIDflv_<?=intval($arParams['ADDITIONAL_PARAMS']['NUM'])?>" style="display:none; height: <?=$arResult['HEIGHT']+intval($arParams['ADDITIONAL_PARAMS']['HEIGHT_CORRECT']['FLV'] - $arParams['ADDITIONAL_PARAMS']['HEIGHT_CORRECT']['WMV']);?>px">
	<object 
	    id="FLVplayer_<?=intval($arParams['ADDITIONAL_PARAMS']['NUM'])?>" width="<?=$arResult['WIDTH']?>" height="<?=$arResult['HEIGHT']?>"        
	    classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab">
	    <param name="movie" value="/bitrix/components/bitrix/player/mediaplayer/player.swf" />
	    <param name="allowScriptAccess" value="always" />
	    <param name="FlashVars" value="logo=<?=$arParams['ADDITIONAL_PARAMS']['LOGO']?>&skin=<?=$arParams["SKIN_PATH"]?>/bitrix.swf&fullscreen=true&<?=$arResult['FLASH_VARS']?>" />
		<embed
			name="FLVplayer_<?=intval($arParams['ADDITIONAL_PARAMS']['NUM'])?>"
			src="/bitrix/components/bitrix/player/mediaplayer/player.swf"
			width="<?=$arResult['WIDTH']?>"
			height="<?=$arResult['HEIGHT']+intval($arParams['ADDITIONAL_PARAMS']['HEIGHT_CORRECT']['FLV']-$arParams['ADDITIONAL_PARAMS']['HEIGHT_CORRECT']['WMV'])?>"
			allowscriptaccess="always"
			allowfullscreen="true"
			menu="<?=$arResult['MENU']?>"
			wmode="<?=$arResult['WMODE']?>"
			flashvars="logo=<?=$arParams['ADDITIONAL_PARAMS']['LOGO']?>&skin=<?=$arParams["SKIN_PATH"]?>/bitrix.swf&fullscreen=true&<?=$arResult['FLASH_VARS']?>"
		/>
</object>
</div>
<div id="myIDwmv_<?=intval($arParams['ADDITIONAL_PARAMS']['NUM'])?>" style="height: <?=$arResult['HEIGHT'];?>px; padding-top:<?=intval($arParams['ADDITIONAL_PARAMS']['HEIGHT_CORRECT']['FLV']-$arParams['ADDITIONAL_PARAMS']['HEIGHT_CORRECT']['WMV'])?>px"></div>
<?endif;?>