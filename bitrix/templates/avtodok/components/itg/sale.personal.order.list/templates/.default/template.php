<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$trigger_status = array("В работе","Выкуплен","Отказ","Склад","Отгружен","В пути");$arRegions = GetAllRegionsProperties();?>
<form method="GET" action="<?= $arResult["CURRENT_PAGE"] ?>" name="bfilter">
<table class="sale-personal-order-list-filter data-table" style="width:300px;">
    <tr>
        <th colspan="2"><?=GetMessage("SPOL_T_HISTORY_POSITION")?></th>
    </tr>
    <tr>
        <td><?=GetMessage("SPOL_T_CODE_ITIEM")?>:<br />
        <input type="text" name="itg_code" id="itg_code" value="<?=htmlspecialchars($_REQUEST["itg_code"])?>" size="35">
        <input type="hidden" name="nameTmpTable" id="nameTmpTable" value="<?='itgTempTable'.session_id()?>">
        <div id="codeVariant"></div>
        </td>
        <!-- <td><?=GetMessage("SPOL_T_VENDOR")?>:<br /><input type="text" name="itg_brand" value="<?=htmlspecialchars($_REQUEST["itg_brand"])?>" size="20"></td> -->
    </tr>
    <tr>
        <th colspan="2">
            <input type="submit" name="filter" value="Поиск">&nbsp;&nbsp;
            <!--<input type="submit" name="del_filter" value="<?=GetMessage("SPOL_T_F_DEL")?>">-->
        </th>
    </tr>
</table>
</form>
<br />
<?if(!empty($arResult["RESULT_SEARCH"])):?>
	<table class="sale-personal-order-list data-table" style="width:600px;">
	    <tr>
	        <th style="width:100px;"><?=GetMessage("SPOL_T_CODE_ITIEM")?> </th>
	        <th style="width:100px;"><?=GetMessage("SPOL_T_VENDOR")?></th>
	        <th style="width:200px;"><?=GetMessage("SPOL_T_NAME_POSITION")?></th>
	    </tr>
        <?foreach($arResult["RESULT_SEARCH"] as $item):?>
            <tr>
                <td style="width:100px;"><?=$item["ARTICLE"]?></td>
                <td style="width:100px;"><?=$item["BRAND"]?></td>
                <td style="width:200px;"><?=$item["NAME"]?></td>
            </tr>
        <?endforeach;?>
	</table>
	
	<?if(strlen($arResult["NAV_STRING"]) > 0):?>
	    <p><?=str_replace('Заказы','Позиции',$arResult["NAV_STRING"])?></p>
	<?endif?>
	
	<br>
	<table class="sale-personal-order-list data-table">
	    <tr>
	        <th><?=GetMessage("SPOL_T_NUM_DOCUMENT")?> <?=SortingEx("ORDER_ID")?></th>
	        <th>Дата <?=SortingEx("DATE_INSERT")?></th>
	        <th><?=GetMessage("SPOL_T_REGION")?> </th>
	        <th>Количество <?=SortingEx("QUANTITY")?></th>
	        <th><?=GetMessage("SPOL_T_PRICE")?> <?=SortingEx("PRICE")?></th>
	        <th><?=GetMessage("SPOL_T_STATUS_POSITION")?> <?=SortingEx("ITEMSTATUS")?></th>
	    </tr>
	    <? /*echo "<pre>".print_r($arResult["ORDERS"])."</pre>";*/?>
	    <?foreach($arResult["ORDERS"] as $item):?>
	        <? /*echo "<pre>".print_r($arResult["ORDERS"])."</pre>";*/?>
	        <tr>
	            <td><b><a title="<?=GetMessage("SPOL_T_DETAIL_DESCR")?>" href="<?=$item["URL_TO_DETAIL"]?>"><?=$item["ORDER_ID"]?></b></a> </td>
	            <td><?=$item["DATE_INSERT"]?></td>
	            <td><?=$arRegions[GetRegionCodeForOrder($item["ORDER_ID"])]["ShortName"]?></td>
	            <td><?=$item["QUANTITY"].GetMessage("STPOL_SHT")?></td>
	            <td><?=str_replace('0000','',$item["PRICE"])." ".$item["CURRENCY"]?></td>
	            <td><?=$trigger_status[$item["ITEMSTATUS"]];?></td>
	        </tr>
	        
	    <?endforeach;?>
	</table>
	<?if(strlen($arResult["NAV_STRING"]) > 0):?>
	    <p><?=str_replace('Заказы','Позиции',$arResult["NAV_STRING"])?></p>
	<?endif?>
<?endif?>
<?if(empty($arResult["RESULT_SEARCH"]) && isset($_REQUEST["filter"])):?>
<table class="sale-personal-order-list data-table" style="width:400px;">
	    <tr>
	        <th>Результаты поиска:</th>
	    </tr>
	    <tr>
	        <td>код <b><?=htmlspecialchars($_REQUEST["itg_code"])?></b> в Ваших заказах не найден</td>
	    </tr>
	</table>
<?endif?>