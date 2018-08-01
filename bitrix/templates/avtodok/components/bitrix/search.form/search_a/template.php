<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="search-form">
<form action="<?=$arResult["FORM_ACTION"]?>">
<?if($arParams["USE_SUGGEST"] === "Y"):?><?$APPLICATION->IncludeComponent(
				"bitrix:search.suggest.input",
				"",
				array(
					"NAME" => "q",
					"VALUE" => "",
					"INPUT_SIZE" => 15,
					"DROPDOWN_SIZE" => 10,
				),
				$component, array("HIDE_ICONS" => "Y")
			);?>
<?else:?>
<input type="text" name="q" value="" id="search_field"  />
<?endif;?>
<input name="s" type="image" src="<?=SITE_TEMPLATE_PATH?>/images/poisk.jpg" id="search_button" type="submit" value="<?=GetMessage("BSF_T_SEARCH_BUTTON");?>" />
</form>
</div>