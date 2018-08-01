<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/autodoc_globals.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/autodoc/includes/autodoc_templaytor.php");
?>
<a name="tb"></a>
<a href="<?=$arResult["URL_TO_LIST"]?>"><?=GetMessage("SPOD_RECORDS_LIST")?></a>
<br /><br />
<?if(strlen($arResult["ERROR_MESSAGE"])<=0):?>
    <table class="sale_personal_order_detail data-table">
    <tr>
        <th colspan="2" align="center"><b><?=GetMessage("SPOD_ORDER_NO")?>&nbsp;<?=$arResult["ID"]?>&nbsp;<?=GetMessage("SPOD_FROM")?> <?=$arResult["DATE_INSERT"] ?></b></th>
    </tr>
    <tr>
        <td align="right" width="40%"><?echo GetMessage("SPOD_ORDER_STATUS")?></td>
        <td width="60%"><?=str_ireplace('Подтвержден','В работе',$arResult["STATUS"]["NAME"])?><?=GetMessage("SPOD_ORDER_FROM")?><?=$arResult["DATE_STATUS"]?>)</td>
    </tr>
    <tr>
        <td align="right" width="40%"><?=GetMessage("P_ORDER_PRICE")?>:</td>
        <td width="60%"><?
                echo "<b>".$arResult["PRICE_FORMATED"]."</b>";
                if (DoubleVal($arResult["SUM_PAID"]) > 0)
                    echo "(".GetMessage("SPOD_ALREADY_PAID")."&nbsp;<b>".$arResult["SUM_PAID_FORMATED"]."</b>)";
                ?></td>
    </tr>

    <tr>
        <td align="right" width="40%">Регион:</td>
        <td width="60%"><?

        CModule::IncludeModule('iblock');
        $arRegions = GetAllRegionsProperties();
        echo $arRegions[GetRegionCodeForOrder( $arResult["ID"] )]["ShortName"];
                ?></td>
    </tr>

    <?
    if(!empty($arResult["ORDER_PROPS"]))
    {
        foreach($arResult["ORDER_PROPS"] as $val)
        {
            if ($val["SHOW_GROUP_NAME"] == "Y")
            {
                ?>
                <tr>
                    <td colspan="2" align="center"><b><?=$val["GROUP_NAME"];?></b></td>
                </tr>
                <?
            }
            ?>
            <tr>
                <td width="40%" align="right"><?echo $val["NAME"] ?>:</td>
                <td width="60%"><?
                    if ($val["TYPE"] == "CHECKBOX")
                    {
                        if ($val["VALUE"] == "Y")
                            echo GetMessage("SALE_YES");
                        else
                            echo GetMessage("SALE_NO");
                    }
                    else
                        echo $val["VALUE"];
                    ?></td>
            </tr>
            <?
        }
    }
    if (strlen($arResult["USER_DESCRIPTION"])>0)
    {
        ?>
        <tr>
            <td align="right" colspan="2">
                <img src="/bitrix/images/1.gif" width="1" height="8">
            </td>
        </tr>
        <tr>
            <td align="right" width="40%"><?=GetMessage("P_ORDER_USER_COMMENT")?>:</td>
            <td width="60%"><?=$arResult["USER_DESCRIPTION"]?></td>
        </tr>
        <?
    }
    ?>
    <tr>
        <td align="right" colspan="2">
            <img src="/bitrix/images/1.gif" width="1" height="8">
        </td>
    </tr>


    <tr>
        <th colspan="2" align="center"><b><?=GetMessage("P_ORDER_BASKET")?></b></th>
    </tr>
    <tr>
        <td colspan="2">
            <table class="sale_personal_order_detail data-table">
                 <tr>
                    <th>Бренд, код, название</th>
                    <th>Статус строки<br>-------<br>СтатусСтроки2</th>
                    <th>Статус строки<br> Количество</th>
                    <th>№ заказа по<br>Вашей базе</th>
                    <th><?= GetMessage("SPOD_QUANTITY") ?></th>
                    <th><?= GetMessage("SPOD_PRICE") ?></th>
                </tr>
                <?
                $itemCodeChangeCheck=false;
                $itemCodeChangeTH="";  
                foreach($arResult["BASKET"] as $val)
                {


                            if(!empty($val["PROPS"]))
                              {
                                foreach($val["PROPS"] as $vv)
                                {

                                    switch($vv["CODE"])
                                      {
                                          case "Brand" :
                                            $brand = $vv["VALUE"];
                                            break;

                                          case "ItemCode" :
                                            $itemCode = $vv["VALUE"];
                                            break;
                                           case "ItemCodeChange"  :
                                            $itemCodeChange=$vv["VALUE"];
                                            $itemCodeChangeCheck=true;
                                            $itemCodeChangeTH="(Была Произведена Замена На)<br> ";
                                           break; 
                                          case "ItemStatusQuantity":
                                             $ItemStatusQuantity=$vv["VALUE"];
                                          break;
                                          
                                          case "ItemStatusQuantity2":
                                             $ItemStatusQuantity2=$vv["VALUE"];
                                          break;
                                          
                                          case "ItemStatus" :
                                               switch( $vv["VALUE"])
                                                 {
                                                      case 0 :
                                                         $itemStatus = "В работе";
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
                                                        case 5:
                                                        $itemStatus = "В пути";
                                                      break;

                                                 }
                                            break;
                                            case "ItemStatus2" :
                                               switch( $vv["VALUE"])
                                                 {
                                                      case 0 :
                                                         $itemStatus2 = "В работе";
                                                      break;

                                                      case 1 :
                                                         $itemStatus2 = "Выкуплен";
                                                      break;

                                                      case 2 :
                                                         $itemStatus2 = "Отказ";
                                                      break;

                                                      case 3 :
                                                         $itemStatus2 = "Склад";
                                                      break;

                                                      case 4 :
                                                         $itemStatus2 = "Отгружен";
                                                      break;
                                                        case 5:
                                                        $itemStatus2 = "В пути";
                                                      break;

                                                 }
                                            break;
                                      
                                      
                                      }
                                }


                             }
                           else
                             {
                               $brand = "";
                               $itemCode = "Не определено";
                               $itemStatus = "Не определено";
                             }





                    ?>
                    
                    <tr>
                        <td><?

                            $objTStr = new TemplatedString( $itemCode );

                           $brandID = GetBrandCodeByCHR( $brand );

                              $objTStr->SetTemplate( $brandID );
                           $itemCode = $objTStr->GetTemplated();


                            if (strlen($val["DETAIL_PAGE_URL"])>0)
                                echo "<a href=\"".$val["DETAIL_PAGE_URL"]."\">";
                                if ($itemCodeChangeCheck)
                                {
                                  echo $itemCodeChangeTH.
                                  $brand." : ".htmlspecialcharsEx($itemCode)."<br>".htmlspecialcharsEx($val["NAME"])."<br>----------------------------------<br>
                                  Старый Артикул: ".htmlspecialcharsEx($itemCodeChange)."
                                  ";   
                                }  else
                                {
                                echo $brand." : ".htmlspecialcharsEx($itemCode)."<br>".htmlspecialcharsEx($val["NAME"]);
                                }
                            if (strlen($val["DETAIL_PAGE_URL"])>0)
                                echo "</a>";
                            ?></td>
                            <?
                            if ($ItemStatusQuantity2=='0'|| (!isset($ItemStatusQuantity2)))
                            {
                               $itemStatus2="";
                               $ItemStatusQuantity2="";
                               $line=""; 
                            } else
                            {
                                $line="-------" ;
                            }
                            ?>
                        <td><?=$itemStatus?><br><?=$line?><br><?=$itemStatus2?></td>
                        <td><?=$ItemStatusQuantity?><br><?=$line?><br><?=$ItemStatusQuantity2?></td>
                        <td><?=$val["NOTES"]?></td>
                        <td><?=$val["QUANTITY"]?></td>
                        <td align="right"><?=$val["PRICE_FORMATED"]?></td>
                    </tr>
                    <?
                }
                ?>
                <tr>
                    <td align="right"><b><?=GetMessage("SPOD_ITOG")?>:</b></td>
                    <td align="right" colspan="4"><?=$arResult["PRICE_FORMATED"]?></td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<?else:?>
    <?=ShowError($arResult["ERROR_MESSAGE"]);?>
<?endif;?>
