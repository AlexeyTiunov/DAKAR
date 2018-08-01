<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="catalog-section">
<?if($arParams["DISPLAY_TOP_PAGER"]):?>
	<p><?=$arResult["NAV_STRING"]?></p>
<?endif?>
<div id='table_upload' >
<table class="data-table" cellspacing="0" cellpadding="0" border="0" width="100%">
	<thead>
	<tr>
		<th><?=GetMessage("CATALOG_TITLE")?></th>
		<?if(count($arResult["ITEMS"]) > 0):
			foreach($arResult["ITEMS"][0]["DISPLAY_PROPERTIES"] as $arProperty):?>
				<th><?=$arProperty["NAME"]?></th>
			<?endforeach;
		endif;?>
		<?foreach($arResult["PRICES"] as $code=>$arPrice):?>
			<th><?=$arPrice["TITLE"]?></th>
		<?endforeach?>
		<?if(count($arResult["PRICES"]) > 0):?>
			<th>&nbsp;</th>
		<?endif?>
	</tr>
	</thead>
	<?foreach($arResult["ITEMS"] as $arElement):?>
	<tr>
		<td>
			<span href="<?=$arElement["DETAIL_PAGE_URL"]?>"><?=$arElement["NAME"]?></span>
			<?if(count($arElement["SECTION"]["PATH"])>0):?>
				<br />
				<?foreach($arElement["SECTION"]["PATH"] as $arPath):?>
					/ <span href="<?=$arPath["SECTION_PAGE_URL"]?>"><?=$arPath["NAME"]?></span>
				<?endforeach?>
			<?endif?>
		</td>
		<?foreach($arElement["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>
		<td>
			<?if(is_array($arProperty["DISPLAY_VALUE"]))
				echo implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);
			elseif($arProperty["DISPLAY_VALUE"] === false)
				echo "&nbsp;";
			else
				echo $arProperty["DISPLAY_VALUE"];?>
		</td>
		<?endforeach?>
		<?foreach($arResult["PRICES"] as $code=>$arPrice):?>
		<td>
			<?if($arPrice = $arElement["PRICES"][$code]):?>
				<?if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>
					<s><?=$arPrice["PRINT_VALUE"]?></s><br /><span class="catalog-price"><?=$arPrice["PRINT_DISCOUNT_VALUE"]?></span>
				<?else:?>
					<span class="catalog-price"><?=$arPrice["PRINT_VALUE"]?></span>
				<?endif?>
			<?else:?>
				&nbsp;
			<?endif;?>
		</td>
		<?endforeach;?>
		<?if(count($arResult["PRICES"]) > 0):?>
		<td>
			<?if($arElement["CAN_BUY"]):?>
				<noindex>
				<span href="<?echo $arElement["BUY_URL"]?>" rel="nofollow"><?echo GetMessage("CATALOG_BUY")?></span>
				&nbsp;<span href="<?echo $arElement["ADD_URL"]?>" rel="nofollow"><?echo GetMessage("CATALOG_ADD")?></span>
				</noindex>
			<?elseif((count($arResult["PRICES"]) > 0) || is_array($arElement["PRICE_MATRIX"])):?>
				<?=GetMessage("CATALOG_NOT_AVAILABLE")?>
			<?endif?>&nbsp;
		</td>
		<?endif;?>
	</tr>
	<?endforeach;?>
</table>
</div> <!--#table_upload-->
<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<p><?=$arResult["NAV_STRING"]?></p>
<?endif?>
</div>
