<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!defined("BX_GADGET_DEFAULT"))
{
	define("BX_GADGET_DEFAULT", true);
	?>
	<script>
	var updateURL = '<?=htmlspecialcharsback($arResult['UPD_URL'])?>';
	var langGDError1 = '<?=GetMessage("CMDESKTOP_TDEF_ERR1")?>';
	var langGDError2 = '<?=GetMessage("CMDESKTOP_TDEF_ERR2")?>';
	var langGDConfirm1 = '<?=GetMessage("CMDESKTOP_TDEF_CONF")?>';
	var langGDConfirmUser = '<?=GetMessage("CMDESKTOP_TDEF_CONF_USER")?>';
	var langGDConfirmGroup = '<?=GetMessage("CMDESKTOP_TDEF_CONF_GROUP")?>';
	var langGDCancel = "<?echo GetMessage("CMDESKTOP_TDEF_CANCEL")?>";
	</script>
	<?if($arResult["PERMISSION"]>"R"):?>
	<script type="text/javascript" src="/bitrix/components/bitrix/desktop/script.js?v=<?=filemtime($_SERVER['DOCUMENT_ROOT'].'/bitrix/components/bitrix/desktop/script.js');?>"></script>
	<?endif?>
	<div id="antiselect" style="height:100%; width:100%; left: 0; top: 0; position: absolute; -moz-user-select: none !important; display: none; background-color:#FFFFFF; -moz-opacity: 0.01;"></div>
	<?
}
?>
<?if($arResult["PERMISSION"]>"R"):?>
<?
$allGD = Array();
foreach($arResult['ALL_GADGETS'] as $gd)
{
	$allGD[] = Array(
		'ID' => $gd["ID"],
		'TEXT' =>
			'<div style="text-align: left;">'.($gd['ICON1']?'<img src="'.($gd['ICON']).'" align="left">':'').
			'<b>'.(htmlspecialchars($gd['NAME'])).'</b><br>'.(htmlspecialchars($gd['DESCRIPTION'])).'</div>',
		);
}
?>
<script>
arGDGroups = <?=CUtil::PhpToJSObject($arResult["GROUPS"])?>;
new BXGadget('<?=$arResult["ID"]?>', <?=CUtil::PhpToJSObject($allGD)?>);
</script>

<div class="bx-gd-buttons">
	<div class="bx-gd-button bx-gd-add" onclick="getGadgetHolder('<?=AddSlashes($arResult["ID"])?>').ShowAddGDMenu(this);">
		<span class="bx-gd-l"></span>
		<span class="bx-gd-c"><span class="bx-gd-text"><a href="javascript:void(0)"><?echo GetMessage("CMDESKTOP_TDEF_ADD")?></a></span></span>
		<span class="bx-gd-r"></span>
	</div>

<?if($arResult["PERMISSION"]>"W"):?>
	<?
	if ($arParams["MODE"] == "SU")
		$mode = "'SU'";
	elseif ($arParams["MODE"] == "SG")
		$mode = "'SG'";
	else
		$mode = "";
	?>
	<div class="bx-gd-button bx-gd-setdef" onclick="getGadgetHolder('<?=AddSlashes($arResult["ID"])?>').SetForAll(<?=$mode?>);">
		<span class="bx-gd-l"></span>
		<span class="bx-gd-c"><span class="bx-gd-text"><a href="javascript:void(0)"><?echo GetMessage("CMDESKTOP_TDEF_SET")?></a></span></span>
		<span class="bx-gd-r"></span>
	</div>
<?endif?>

	<div class="bx-gd-button bx-gd-clear" onclick="getGadgetHolder('<?=AddSlashes($arResult["ID"])?>').ClearUserSettings();">
		<span class="bx-gd-l"></span>
		<span class="bx-gd-c"><span class="bx-gd-text"><a href="javascript:void(0)"><?echo GetMessage("CMDESKTOP_TDEF_CLEAR")?></a></span></span>
		<span class="bx-gd-r"></span>
	</div>
</div>
<br>&nbsp;
<?endif;?>

<form action="<?=POST_FORM_ACTION_URI?>" method="POST" id="GDHolderForm_<?=$arResult["ID"]?>">
<input type="hidden" name="holderid" value="<?=$arResult["ID"]?>">
<input type="hidden" name="gid" value="0">
<input type="hidden" name="action" value="">
</form>

<table class="gadgetholder" cellspacing="0" cellpadding="0" width="100%" id="GDHolder_<?=$arResult["ID"]?>">
  <tbody>
    <tr>
    <?for($i=0; $i<$arResult["COLS"]; $i++):?>
    	<?if($i==0):?>
    	<td class="gd-page-column<?=$i?>" valign="top" width="<?=$arResult["COLUMN_WIDTH"][$i]?>" id="s0">
    	<?elseif($i==$arResult["COLS"]-1):?>
	 	  <td width="10">
	        <div style="WIDTH: 10px"></div>
	        <br />
	      </td>
	      <td class="gd-page-column<?=$i?>" valign="top" width="<?=$arResult["COLUMN_WIDTH"][$i]?>" id="s2">
    	<?else:?>
	 	  <td width="10">
	        <div style="WIDTH: 10px"></div>
	        <br />
	      </td>
	      <td class="gd-page-column<?=$i?>" valign="top"  width="<?=$arResult["COLUMN_WIDTH"][$i]?>" id="s1">
 		<?endif?>
		<?foreach($arResult["GADGETS"][$i] as $arGadget):?>
			<table id="t<?=$arGadget["ID"]?>" class="data-table-gadget<?=($arGadget["HIDED"]?' gdhided':'')?>"><tr><td><div class="gdparent">
				<div class="gdcorn"></div>
				<?if($arResult["PERMISSION"]>"R" && $GLOBALS["APPLICATION"]->GetPublicShowMode()=='view'):?>
					<div class="gdheader" style="cursor:move;" onmousedown="return getGadgetHolder('<?=AddSlashes($arResult["ID"])?>').DragStart('<?=$arGadget["ID"]?>', event)">
						<a class="gdremove" href="javascript:void(0)" onclick="return getGadgetHolder('<?=AddSlashes($arResult["ID"])?>').Delete('<?=$arGadget["ID"]?>');"></a>
						<a class="gdhide" href="javascript:void(0)" onclick="return getGadgetHolder('<?=AddSlashes($arResult["ID"])?>').Hide('<?=$arGadget["ID"]?>', this);"></a>
						<a class="gdsettings<?=($arGadget["NOPARAMS"]?' gdnoparams':'')?>" href="javascript:void(0)" onclick="return getGadgetHolder('<?=AddSlashes($arResult["ID"])?>').ShowSettings('<?=$arGadget["ID"]?>');"></a>
				<?else:?>
					<div class="gdheader">
				<?endif?>
						<?=$arGadget["TITLE"]?>
					</div>
				<div class="gdoptions" style="display:none" id="dset<?=$arGadget["ID"]?>"></div>
				<div class="gdcontent" id="dgd<?=$arGadget["ID"]?>">
					<?=$arGadget["CONTENT"]?>
				</div>
				<div style="position:relative;"><div class="gdbcorn"></div></div>
			</div></td></tr></table>
			<div style="display:none; border:1px #404040 dashed; margin-bottom:8px;" id="d<?=$arGadget["ID"]?>"></div>
 		<?endforeach;?>
 	  </td>
    <?endfor;?>
    </tr>
  </tbody>
</table>
