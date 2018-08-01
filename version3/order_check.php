<?php 
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/general/export.autodoc.php");  
global $DB;
global $USER;
?>

<?

  if (!$USER->IsAuthorized())
     {   
         
       
         $_SESSION['BACKULRSA']="/order_check.php";
         $_SESSION['MASSAGE']="Пройдите авторизацию.";  
         header('Location:/SimpleAuth/');     
            
            
            
     }
    
?>

<?  
    $arFilter=Array( "USER_ID"=>IntVal($USER->GetID()),                               
                     ">=DATE_INSERT"=>date($DB->DateFormatToPHP(CSite::GetDateFormat("SHORT")) ,mktime(0,0,0,1,1,date('Y')))   
                             );
    $arFilter["LID"] = SITE_ID;
    #  var_dump($DB->DateFormatToPHP(CSite::GetDateFormat("SHORT")));  string(5) "d.m.Y" 
    # var_dump (CSite::GetDateFormat("SHORT"));      string(10) "DD.MM.YYYY"  
 
    $dbOrder = CSaleOrder::GetList(Array("ID"=>"DESC"), $arFilter ); 
    $orderList=Array();
    while($arOrder = $dbOrder->GetNext())
    {
       $arOrder["FORMATED_PRICE"] = SaleFormatCurrency($arOrder["PRICE"], $arOrder["CURRENCY"]);  
        $dbBasket = CSaleBasket::GetList
            (
                  array(
                       "NAME" => "ASC",
                       "ID" => "ASC"
                       ),
                  array("ORDER_ID"=>$arOrder["ID"])       
        
            );
            $arOBasket=Array();
          while ($arBasket = $dbBasket->Fetch()) 
          {
            $arBasket["ARTICLE"] = GetBasketItemProperty( $arBasket["ID"], "ItemCode" );
            $arBasket["BRAND"] = GetBasketItemProperty( $arBasket["ID"], "Brand" );   
            $arBasket["NAME"] = htmlspecialcharsEx($arBasket["NAME"]);
            $arBasket["NOTES"] = htmlspecialcharsEx($arBasket["NOTES"]);
            $arBasket["QUANTITY"] = DoubleVal($arBasket["QUANTITY"]);   
            $arBasket["PRICE_FORMATED"]=SaleFormatCurrency( $arBasket["PRICE"], $arBasket["CURRENCY"]); 
            $arBasket["SUM"]=DoubleVal($arBasket["PRISE"]*$arBasket["QUANTITY"]);
            $arBasket["SUM_FORMATED"]=SaleFormatCurrency(DoubleVal($arBasket["PRICE"]*$arBasket["QUANTITY"]),$arBasket["CURRENCY"]);  
           
             $arOBasket[] = $arBasket;    
          }
      
      $orderList[]=Array(
         "ORDER"=>$arOrder,
         "BASKET_ITEMS"=>$arOBasket
      ) ; 
      
        
        
        
        
    } 
    #var_dump($orderList);
    include "order_show_template.php";
 
?>



<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>