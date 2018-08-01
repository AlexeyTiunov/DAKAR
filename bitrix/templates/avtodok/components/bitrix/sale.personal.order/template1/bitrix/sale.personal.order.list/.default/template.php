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

						?><option value="<?echo $val["ID"]?>"<?if($_REQUEST["filter_status"]==$val["ID"]) echo " selected"?>>[<?=$val["ID"]?>] <?=$val["NAME"]?></option><?

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
			<td><?php 
				$statusOrderStr = str_ireplace('Подтвержден','В работе',$arResult["INFO"]["STATUS"][$val["ORDER"]["STATUS_ID"]]["NAME"]);
				echo $statusOrderStr;
				?>
				<br /><?=$val["ORDER"]["DATE_STATUS"]?>
			</td>
			<td><?
				$bNeedComa = False;

				$arStatuses = Array();

				foreach($val["BASKET_ITEMS"] as $vval)
				{
					$arStatuses[] = $vval["ITEMSTATUS"];
				}


				$arStatuses = array_unique($arStatuses);

                $imagePath = "http://".$_SERVER["SERVER_NAME"]."/personal/order/images/";
                if ($statusOrderStr == 'Отложен')
                {
                	$itemStatus = "<img src='{$imagePath}otlozhen.png' title='Отложен' height='16' />";
                	echo $itemStatus;
                }
                elseif ($statusOrderStr == 'Рассматривается')
                {
                	$itemStatus = "<img src='{$imagePath}binokl.png' title='Рассматривается' height='16' />";
                	echo $itemStatus;
                }
				else 
				{
					foreach( $arStatuses as $v )
					{
	                     switch( $v)
	 
				  	       {
				  	       	 case 0 :/*В работе*/
				  	       	    $itemStatus = "<img src='{$imagePath}v_rabote.png' title='В работе' height='16' />";
				  	            break;
	
				  	       	 case 1 :/*Выкуплен*/
				  	       	    $itemStatus = "<img src='{$imagePath}vykuplen.png' title='Выкуплен' height='16' />";
				  	            break;
	
				  	       	 case 2 :/*Отказ*/
				  	       	    $itemStatus = "<img src='{$imagePath}otkaz.png' title='Отказ' height='16' />";
				  	            break;
	
				  	       	 case 3 :/*Склад*/
				  	       	    $itemStatus = "<img src='{$imagePath}sklad.png' title='Склад' height='16' />";
				  	            break;
	
				  	       	 case 4 :/*Отгружен*/
				  	       	    $itemStatus = "<img src='{$imagePath}otgruzhen.png' title='Отгружен' height='16' />";
				  	            break;
	                         case 5:/*В пути*/
	                            $itemStatus = "<img src='{$imagePath}v_puti.png' title='В пути' height='16' />";
	                            break;
				  	       }
	
	                      //echo "<Иконка для статуса товара : ".$itemStatus."><br>";
	                      echo $itemStatus."<br />";
					}
				}

			?></td>
			<td><?#= $arRegions[GetRegionCodeForOrder($val["ORDER"]["ID"])]["ShortName"]; ?>
            <? if ($arRegions[GetRegionCodeForOrder($val["ORDER"]["ID"])]["ShortName"]=='JPNA')
               {
                   echo 'Япония';
               } 
               elseif($arRegions[GetRegionCodeForOrder($val["ORDER"]["ID"])]["ShortName"]=='UAE')
               {
                   echo 'Эмираты'; 
               }
               elseif($arRegions[GetRegionCodeForOrder($val["ORDER"]["ID"])]["ShortName"]=='DE')
               {
                   echo 'Германия'; 
               }
               elseif($arRegions[GetRegionCodeForOrder($val["ORDER"]["ID"])]["ShortName"]=='USA')
               {
                   echo 'США'; 
               }
               elseif($arRegions[GetRegionCodeForOrder($val["ORDER"]["ID"])]["ShortName"]=='Склад')
               {
                   echo 'Склад'; 
               }
               else
               {
                   #echo $arRegions[GetRegionCodeForOrder($val["ORDER"]["ID"])]["ShortName"];
                  echo 'Украина';
               }
            ?>
			</td>

		</tr>
	<?endforeach;?>
</table>
<?if(strlen($arResult["NAV_STRING"]) > 0):?>
	<p><?=$arResult["NAV_STRING"]?></p>
<?endif?>