<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="search-form">
История позиций:
<form action="<?=$arResult["FORM_ACTION"]?>">
	<table border="0" cellspacing="0" cellpadding="2" align="center">
		<tr>
			<th align="right" width="100">Артикул:</th>
			<th align="right" width="100">Бренд:</th>
		</tr>
		<tr>
			<td align="center"><?if($arParams["USE_SUGGEST"] === "Y"):?><?$APPLICATION->IncludeComponent(
				"bitrix:search.suggest.input",
				"",
				array(
					"NAME" => "ARTICLE",
					"VALUE" => "",
					"INPUT_SIZE" => 15,
					"DROPDOWN_SIZE" => 10,
				),
				$component, array("HIDE_ICONS" => "Y")
			);?><?else:?><input type="text" name="ARTICLE" value="" size="15" maxlength="50" /><?endif;?></td>
			<td align="center"><?if($arParams["USE_SUGGEST"] === "Y"):?><?$APPLICATION->IncludeComponent(
				"bitrix:search.suggest.input",
				"",
				array(
					"NAME" => "BRAND",
					"VALUE" => "",
					"INPUT_SIZE" => 15,
					"DROPDOWN_SIZE" => 10,
				),
				$component, array("HIDE_ICONS" => "Y")
			);?><?else:?><input type="text" name="BRAND" value="" size="15" maxlength="50" /><?endif;?></td>
		</tr>
		<tr>
			<td align="right"></td>
			<td align="right"><input name="s" type="submit" value="<?=GetMessage("BSF_T_SEARCH_BUTTON");?>" /></td>
		</tr>
	</table>
</form>
</div>