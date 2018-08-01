<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/autodoc_globals.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/autodoc/includes/autodoc_stats.php");
define("STOP_STATISTICS", true);  

global $USER;

if(isset($_GET["rcode"]))
{
    $_GET["quantity"] = intval($_GET["quantity"]);
    if(!$_GET["quantity"] || $_GET["quantity"] < 0)$_GET["quantity"] = 1;


    if(isset($_GET["r"]) && ( $_GET["r"]=="1"))$redirect = true;
    else $redirect = false;

    $_GET['icode'] = str_replace(array('-',' ','.',',','*'),'',$_GET['icode']);
    if (CModule::IncludeModule("sale")&& CModule::IncludeModule('iblock'))
    {
        CSaleBasket::Init();
        unset($arProps);
        if (!$_GET['caption']) $_GET['caption'] = '-';
        $productID = intval(preg_replace('/[^0-9]/', '', $_GET['icode'].$_GET['bcode']));
        $arFields = array(
                "PRODUCT_ID"             => $productID,
                "PRODUCT_PRICE_ID"        => 0,
                "PRICE"                 => $_GET['price'],
                "CURRENCY"                 => $_GET['currency'],
                "WEIGHT"                 => 0,
                "QUANTITY"                 => intval($_GET['quantity']),
                "LID"                     => LANG,
                "DELAY"                 => "N",
                "CAN_BUY"                 => "Y",
                "NAME"                     => $_GET['caption'],
                "MODULE"                 => "AUTODOC"
                );
        
        $arProps = array();
        $arProps[] = array("NAME" => "Валюта",                    "CODE" => "Currency",    "VALUE" => $_GET['currency']);
        $arProps[] = array("NAME" => "Артикул",                    "CODE" => "ItemCode",    "VALUE" => $_GET['icode']);
        $arProps[] = array("NAME" => "Код бренда",                "CODE" => "Brand",        "VALUE" => $_GET['bscode']);
        $arProps[] = array("NAME" => "Код региона",                "CODE" => "RegionCode",    "VALUE" => $_GET['rcode']);
        $arProps[] = array("NAME" => "Статус строки заказа",    "CODE" => "ItemStatus",    "VALUE" => "0");
                 
        $arFields["PROPS"] = $arProps;

            $arBasketItems = array();
                                    $checkItem=true;
                                    $checkRegion=true;
                                      $dbBasketItems = CSaleBasket::GetList(
                                           array(
                                                   "NAME" => "ASC",
                                                   "ID" => "ASC"
                                                   ),
                                           array(
                                               "FUSER_ID" => CSaleBasket::GetBasketUserID(),
                                                "LID" => "s1",
                                                "ORDER_ID" => "NULL"
                                                  ),
                                                  false,
                                                   false,
                                            array("ID","QUANTITY") 
                                                      ); 
                                                         
                                                    
                                          while ($arItems = $dbBasketItems->Fetch())
                                          {
                                              $checkItem=true;
                                              $checkRegion=true;
                                               $db_res = CSaleBasket::GetPropsList(
                                                   array(
                                                      "SORT" => "ASC",
                                                      "NAME" => "ASC"
                                                            ),
                                                   array("BASKET_ID" => $arItems['ID'])
                                                           );
                                               while ($ar_res = $db_res->Fetch())
                                               {
                                                   if ( $ar_res['CODE']=="ItemCode"  && $ar_res['VALUE']==$_GET['icode']  )
                                                    {   
                                                        $checkItem=false;
                                                    }
                                                    if ( $ar_res['CODE']=="RegionCode" &&  $ar_res['VALUE']==$_GET['rcode']  )  
                                                   {  
                                                      $checkRegion=false;   
                                                   }
                                                   if ($checkItem==false && $checkRegion==false )
                                                    {
                                                        
                                                    break;
                                                    }
                                               }
                                               
                                               if ($checkItem==false && $checkRegion==false )
                                               {
                                                   $arItemQuantity=$arItems['QUANTITY'];
                                                   $arItemId= $arItems['ID'];
                                                    break;
                                               }
                                          }
             
           
           if ($checkItem==false && $checkRegion==false ) 
           {    
               $arFieldsUpdates= array(
                        "QUANTITY" => $arItemQuantity +intval($_GET['quantity']) 
                          );
                CSaleBasket::Update($arItemId, $arFieldsUpdates);
           } 
            else
            {  
        
        
        
               $basketItemID = CSaleBasket::Add($arFields);
               

                $sql = "UPDATE b_sale_basket SET
                                        PRICE='{$_GET['price']}',
                                        CURRENCY='{$_GET['currency']}',
                                        PRODUCT_ID='{$productID}'
                                    WHERE 
                                        ID='{$basketItemID}'";
                    $tmpRes = $DB->Query($sql);
            }
        if($redirect) header("Location: /autodoc/mycart.php");
        else
        {
               //echo "Basket ID: ".$basketItemID;
            
            $arBasketItems = array();
            $dbBasketItems = CSaleBasket::GetList(
                                                    array(
                                                        "NAME" => "ASC",
                                                        "ID" => "ASC"
                                                    ),
                                                    array(
                                                        "FUSER_ID" => CSaleBasket::GetBasketUserID(),
                                                        "LID" => SITE_ID,
                                                        "ORDER_ID" => "NULL"
                                                    ),
                                                    false,
                                                    false,
                                                    array("ID")
                    );
            echo $dbBasketItems->SelectedRowsCount().'|<div id="search_name">Артикул <b>'.$_GET['icode'].'</b> добавлен в корзину</div>';
                                 
            $statObj = new CStats();
            $statObj->StoreLine($_GET['bcode'],$_GET['icode']);
        }
    }
}
?>