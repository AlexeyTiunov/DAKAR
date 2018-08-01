<?php 
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/autodoc_globals.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/include.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/autodoc/includes/autodoc_templaytor.php");
global $SummITG,$CurrencyCodeITG;
?>
<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="catalog-section">
<?if($arParams["DISPLAY_TOP_PAGER"]):?>
    <p><?=$arResult["NAV_STRING"]?></p>
<?endif?>
<div id='table_upload' >
<table class="data-table" cellspacing="0" cellpadding="0" border="0" width="100%">
    <thead>
    <tr>
        <!-- <th><?=GetMessage("CATALOG_TITLE")?></th>
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
        <?endif?> -->
        <th>№</th>
        <th>Бренд</th>
        <th>Артикул</th>
        <th>Наименование</th>
        <th>Количество</th>
        <th>Цена</th>
        <th>Сумма</th>
    </tr>
    </thead>
    <?php $i=0; foreach($arResult["ITEMS"] as $arElement):?>
    <tr>
        <td>
            <!--<span href="<?=$arElement["DETAIL_PAGE_URL"]?>"><?=$arElement["NAME"]?></span>
            <?if(count($arElement["SECTION"]["PATH"])>0):?>
                <br />
                <?foreach($arElement["SECTION"]["PATH"] as $arPath):?>
                    / <span href="<?=$arPath["SECTION_PAGE_URL"]?>"><?=$arPath["NAME"]?></span>
                <?endforeach?>
            <?endif?>-->
            <?=++$i;?>
        </td>
        <?foreach($arElement["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>
        <!--<td>
            <?if(is_array($arProperty["DISPLAY_VALUE"]))
                echo implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);
            elseif($arProperty["DISPLAY_VALUE"] === false)
                echo "&nbsp;";
            else
                echo $arProperty["DISPLAY_VALUE"];?>

        </td>-->
        <?endforeach?>
        <?php 
        $brands = GetAllBrandsNameFromID();
        $brands2 = GetAllBrandsProperties();
        $objTStr = new TemplatedString($arElement['DISPLAY_PROPERTIES']['ICode']['DISPLAY_VALUE']);
        $objTStr->SetTemplate($brands2[$arElement['DISPLAY_PROPERTIES']['BCode']['DISPLAY_VALUE']]);
        $objTStr->SetColor("#000000");
        $objTStr->SetSelection($arElement['DISPLAY_PROPERTIES']['ICode']['DISPLAY_VALUE']);
        echo "<td>&nbsp;".$brands[GetBrandCodeByCHR($arElement['DISPLAY_PROPERTIES']['BCode']['DISPLAY_VALUE'])]."&nbsp;</td>";
        echo "<td>&nbsp;{$objTStr->GetTemplated()}&nbsp;</td>";
        echo "<td>&nbsp;{$arElement['DISPLAY_PROPERTIES']['Caption']['DISPLAY_VALUE']}&nbsp;</td>";
        echo "<td align='right'>&nbsp;{$arElement['DISPLAY_PROPERTIES']['Quantity']['DISPLAY_VALUE']}&nbsp;</td>";
        echo "<td align='right'>&nbsp;".number_format($arElement['DISPLAY_PROPERTIES']['Price']['DISPLAY_VALUE'],2)."&nbsp;</td>";
        echo "<td align='right'>&nbsp;".number_format($arElement['DISPLAY_PROPERTIES']['Summ']['DISPLAY_VALUE'],2)."&nbsp;</td>";
        ?>
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
<br/>
<table  width="200" style="color: black;background: #E0E0E0; border: border:solid  white 1px ;
    border-right:solid 2px #afb0b2;
    border-bottom:solid 2px #afb0b2 ; float:right; padding-left:3px;padding-right:3px;">
    <tr style="border-color: inherit;">
        <td align='right' style="border: 1px solid white;">&nbsp;<b>Сумма</b>&nbsp;</td>
        <td align='right' style="border: 1px solid white;">&nbsp;<b><?php
        echo SaleFormatCurrency($SummITG, $CurrencyCodeITG);
        ?></b>&nbsp;</td>
    </tr>
</table>
</div>
