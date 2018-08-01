<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<form method="GET" action="<?= $arResult["CURRENT_PAGE"] ?>" name="bfilter">
<table class="sale-personal-order-list-filter data-table">
	<tr>
		<th colspan="2"><?echo GetMessage("SPOL_T_F_FILTER")?></th>
	</tr>
	<tr>
		<td><?=GetMessage("SPOL_T_F_ID");?>:</td>
		<td><input type="text" name="filter_id" value="<?=htmlspecialchars($_REQUEST["filter_id"])?>" size="10"></td>
	</tr>
	<tr>
		<td><?=GetMessage("SPOL_T_F_DATE");?>:</td>
		<td><?$APPLICATION->IncludeComponent(
	"bitrix:main.calendar",
	"",
	Array(
		"SHOW_INPUT" => "Y",
		"FORM_NAME" => "bfilter",
		"INPUT_NAME" => "filter_date_from",
		"INPUT_NAME_FINISH" => "filter_date_to",
		"INPUT_VALUE" => $_REQUEST["filter_date_from"],
		"INPUT_VALUE_FINISH" => $_REQUEST["filter_date_to"],
		"SHOW_TIME" => "N"
	)
);


CModule::IncludeModule('iblock');
$arRegions = GetAllRegionsProperties();


?></td>
	</tr>
	<tr>
		<td><?=GetMessage("SPOL_T_F_STATUS")?>:</td>
		<td><select name="filter_status">
				<option value=""><?=GetMessage("SPOL_T_F_ALL")?></option>
				<?
				foreach($arResult["INFO"]["STATUS"] as $val)
				{
					if ($val["ID"]!="F")
					{
						?><option value="<?echo $val["ID"]?>"<?if($_REQUEST["filter_status"]==$val["ID"]) echo " selected"?>>[<?=$val["ID"]?>] <?=$val["NAME"]?></option><?
					}
				}
				?>
		</select></td>
	</tr>
	<tr>
		<th colspan = "2">
			<input type="submit" name="filter" value="<?=GetMessage("SPOL_T_F_SUBMIT")?>">&nbsp;&nbsp;
			<input type="submit" name="del_filter" value="<?=GetMessage("SPOL_T_F_DEL")?>">
		</th>
	</tr>
</table>
</form>
<br />
<?if(strlen($arResult["NAV_STRING"]) > 0):?>
	<p><?=$arResult["NAV_STRING"]?></p>
<?endif?>
<table class="sale-personal-order-list data-table">
	<tr>
		<th><?=GetMessage("SPOL_T_ID")?><br /><?=SortingEx("ID")?></th>
		<th><?=GetMessage("SPOL_T_PRICE")?><br /><?=SortingEx("PRICE")?></th>
		<th><?=GetMessage("SPOL_T_STATUS")?><br /><?=SortingEx("STATUS_ID")?></th>
		<th><?=GetMessage("SPOL_T_BASKET")?><br /></th>
		<th>Регион</th>
	</tr>
	<?foreach($arResult["ORDERS"] as $val):?>
		<tr>
			<td><b><a title="<?=GetMessage("SPOL_T_DETAIL_DESCR")?>" href="<?=$val["ORDER"]["URL_TO_DETAIL"]?>"><?=$val["ORDER"]["ID"]?></a></b><br /><?=GetMessage("SPOL_T_FROM")?> <?=$val["ORDER"]["DATE_INSERT_FORMAT"]?></td>
			<td><?=$val["ORDER"]["FORMATED_PRICE"]?></td>
			<td><?=$arResult["INFO"]["STATUS"][$val["ORDER"]["STATUS_ID"]]["NAME"]?><br /><?=$val["ORDER"]["DATE_STATUS"]?></td>
			<td><?
				$bNeedComa = False;

				$arStatuses = Array();

				foreach($val["BASKET_ITEMS"] as $vval)
				{
					$arStatuses[] = $vval["ITEMSTATUS"];
				}


				$arStatuses = array_unique( $arStatuses );



				foreach( $arStatuses as $v )
				{
                     switch( $v)
			  	       {
			  	       	 case 0 :
			  	       	    $itemStatus = "Пустой";
			  	            break;

			  	       	 case 1 :
			  	       	    $itemStatus = "Выкуплен";
			  	            break;

			  	       	 case 2 :
			  	       	    $itemStatus = "Отказ";
			  	            break;

			  	       	 case 3 :
			  	       	    $itemStatus = "Склад";
			  	            break;

			  	       	 case 4 :
			  	       	    $itemStatus = "Отгружен";
			  	            break;

			  	       }

                      echo "<Иконка для статуса товара : ".$itemStatus."><br>";

				}

			?></td>
			<td><?= $arRegions[GetRegionCodeForOrder($val["ORDER"]["ID"])]["ShortName"]; ?>
			</td>

		</tr>
	<?endforeach;?>
</table>
<?if(strlen($arResult["NAV_STRING"]) > 0):?>
	<p><?=$arResult["NAV_STRING"]?></p>
<?endif?>