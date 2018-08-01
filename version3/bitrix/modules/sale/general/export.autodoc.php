<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/autodoc_globals.php");


       //  Возврашает выбранное свойство товара в корзине (заказе)
function GetBasketItemPropertyLIKE( $basketID, $propertyCode )
{
   global $DB;
   $res = '';

   $sql = "SELECT VALUE FROM b_sale_basket_props ";
   $sql .= " WHERE BASKET_ID='".$basketID."'";
   $sql .= " AND CODE LIKE '%".$propertyCode."%'";

   $rRes = $DB->Query($sql);
   if( $arRes = $rRes->Fetch())
     $res = $arRes["VALUE"] ;


   return $res;

}
function GetBasketItemProperty( $basketID, $propertyCode )
{
   global $DB;
   $res = '';

   $sql = "SELECT VALUE FROM b_sale_basket_props ";
   $sql .= " WHERE BASKET_ID='".$basketID."'";
   $sql .= " AND CODE='".$propertyCode."'";

   $rRes = $DB->Query($sql);
   if( $arRes = $rRes->Fetch())
     $res = $arRes["VALUE"] ;


   return $res;

}


       //  Изменяет выбранное свойство товара в корзине (заказе)


function SetBasketItemProperty( $basketID, $propertyCode, $propertyValue )
{
   global $DB;
   $res = '';

   $sql = "UPDATE b_sale_basket_props ";
   $sql .= " SET VALUE='".$propertyValue."'";
   $sql .= " WHERE BASKET_ID='".$basketID."'";
   $sql .= " AND CODE='".$propertyCode."'";

   $rRes = $DB->Query($sql);

}



      //  Возвращает регион  для текущего заказа
function GetOrderRegion( $orderID )
{
   $dbBasket = CSaleBasket::GetList(
						array("NAME" => "ASC"),
						array("ORDER_ID" => $orderID)
					);

   if ( $arBasket = $dbBasket->Fetch() )
     return   GetBasketItemProperty($arBasket["ID"],"RegionCode");
   else die("Error Creating XML 1");
}

      //  Возвращает валюту для текущего заказа
function GetOrderCurrency( $orderID )
{

   $dbBasket = CSaleBasket::GetList(
						array("NAME" => "ASC"),
						array("ORDER_ID" => $orderID)
					);

   if ( $arBasket = $dbBasket->Fetch() )
     return   $arBasket["CURRENCY"];
   else die("Error Creating XML 2");
}

       //  Возвращает массив с аналогами данного товара ( Caption, BrandCode, ItemCode )
       // Или false - если нет аналогов
function GetItemAnalogs($ItemCode, $Brand)
{

   global $DB;
   $arAnalogs = Array();
   $i = 0;


   $brandCode = GetBrandCodeByCHR($Brand);

   $sql = "SELECT B2Code, I2Code FROM b_autodoc_analogs_m ";
   $sql .= " WHERE I1Code='".$ItemCode."'";
   $sql .= " AND B1Code='".$brandCode."'";

   $rRes = $DB->Query($sql);

   while( $arRes = $rRes->Fetch())
     {
         $sql = "SELECT ItemCode, BrandCode, Caption FROM b_autodoc_items_m ";
         $sql .= " WHERE ItemCode='".$arRes["I2Code"]."'";
         $sql .= " AND BrandCode='".$arRes["B2Code"]."'";

         $rRes2 = $DB->Query($sql);
         $arRes2 = $rRes2->Fetch();

         $arAnalogs[$i]["BrandCode"] = GetCHRBrandCodeByID( $arRes2["BrandCode"] );
         $arAnalogs[$i]["ItemCode"] = $arRes2["ItemCode"];
         $arAnalogs[$i]["Caption"] = $arRes2["Caption"];

         $i++;
     }

   if($i > 0 )
     return $arAnalogs;
   else
     return false;
}

      //  Ищет и возвращает XML_ID товара из импортируемого заказа
      // с заданным артикулом и кодом бренда
      //  если не найдено - возвращает false

function GetOrderedProductXMLID($ItemCode,$Brand,$items)
{
	global $DB;
	$result = false;
	$Brand = strtoupper(trim($Brand));
   	$ItemCode = strtoupper(trim($ItemCode));
	foreach($items as $key =>$value)
    {
    	$queryToControllProcess = "INSERT INTO session_control_table VALUES (
																	NULL, 
																	NOW(),
																	'Изнутри функции items->value:".serialize($value)."'),
																	(NULL,
																	NOW(),
																	'ItemCode:{$ItemCode};Brand:{$Brand}')";
		
    	$DB->Query($queryToControllProcess);
    	$arBrandsID_fromShort = GetAllBrandsProperties();
    	
    	$value["BRAND"] = strtoupper(trim($value["BRAND"]));
    	$value["ARTICLE"] = strtoupper(trim($value["ARTICLE"]));
    	if(strlen($value["BRAND"])>2)//если бренд указан не правильно
    	{
    		$arBrandsID_fromFool = GetAllBrandsPropertiesFromFullName();
    		//$arBrandsID_fromShort = GetAllBrandsProperties();
    		//$Brand = $arBrandsID_fromShort[$Brand];//id - из корзины
    		$value["BRAND"] = $arBrandsID_fromFool[$value["BRAND"]];//id - из 1С
    		$queryToControllProcess = "INSERT INTO session_control_table VALUES (
																	NULL, 
																	NOW(),
																	'Изнутри функции 1 value[BRAND]:{$value['BRAND']}')";
    		$DB->Query($queryToControllProcess);
    	}
    	else 
    	{
    		$value["BRAND"] = $arBrandsID_fromShort[$value["BRAND"]];
    		$queryToControllProcess = "INSERT INTO session_control_table VALUES (
																	NULL, 
																	NOW(),
																	'Изнутри функции 2 value[BRAND]:{$value['BRAND']}')";
    		$DB->Query($queryToControllProcess);
    	}
    	$Brand = $arBrandsID_fromShort[$Brand];
    	$queryToControllProcess = "INSERT INTO session_control_table VALUES (
																	NULL, 
																	NOW(),
																	'Изнутри функции 3 Brand:{$Brand}')";
    	$DB->Query($queryToControllProcess);
		if(($value["ARTICLE"] == $ItemCode) && ($value["BRAND"] == $Brand))
		$result = $key;
	}
  return $result;
}

function OrderItemsToString( $orderID )
{
   $dbBasket = CSaleBasket::GetList(
						array("NAME" => "ASC"),
						array("ORDER_ID" => $orderID)
					);

   $arItems = Array();

   while ( $arBasket = $dbBasket->Fetch() )
     {
       $arItems[] = $arBasket;
     }

   $str = "";

   foreach( $arItems as $item  )
      {
      	 $itemStatus = GetItemStatusDescription(  GetBasketItemProperty( $item["ID"], "ItemStatus"));
         $brandCode = GetBasketItemProperty( $item["ID"], "Brand" );
         $itemCode = sprintf("%-15.15s",GetBasketItemProperty( $item["ID"], "ItemCode") );
         $price = sprintf("%10.2f",$item["PRICE"]);
         $name = sprintf("%-25.25s", $item["NAME"]);
         $qty = sprintf("%4u", $item["QUANTITY"]);
         $sum = sprintf( "%10.2f" , $item["QUANTITY"] * $item["PRICE"] );
         $cur = $item["CURRENCY"];
         $str .= $brandCode." : ".$itemCode."\t".$name."\t".$price." ".$cur."\t".$qty."\t".$sum." ".$cur;
         $str .= "\t".$itemStatus;

         $str .="\r\n";

      }


    return $str;
}


function ChangedItemsToString( $arChangedItems, $cur )
{

   $str = "";

   foreach( $arChangedItems as $item  )
      {
      	 $itemStatus = $item['STATUS'];
         $brandCode = $item['BCODE'];
         $itemCode = sprintf("%-15.15s",$item['ICODE'] );
         $price = sprintf("%10.2f",$item["PRICE"]);
         $name = sprintf("%-25.25s", $item["CAPTION"]);
         $qty = sprintf("%4u", $item["QTY"]);
         $sum = sprintf( "%10.2f" , $item["QTY"] * $item["PRICE"] );
         $cur = $cur;
         $str .= $brandCode." : ".$itemCode."\t".$name."\t".$price." ".$cur."\t".$qty."\t".$sum." ".$cur;
         $str .= "\t".$itemStatus."\t";// Изменения: ".$item['CHANGE_DESC'];

         $str .="\r\n";

      }
    return $str;
}
function ChangedItemsToStringBuy( $arChangedItems, $cur )
{

   $str = "";

   foreach( $arChangedItems as $item  )
      {
          
           if ($item['STATUSF']=='1' && ($item['QTY']*1)>($item['STATUSQUANTITY']*1))
           {             
          
           $qty= "(((  ".($item['STATUSQUANTITY']*1)." ИЗ ". ($item['QTY']*1)." ))) на сумму: ";
           $sum = sprintf( "%10.2f" , $item["STATUSQUANTITY"] * $item["PRICE"] );
           $itemStatus = $item['STATUS']." ВНИМАНИЕ ПО ДАННОЙ ПОЗИЦИИ ВЫКУПЛЕНО НЕ ВСЁ КОЛИЧЕСТВО ДЕТАЛЕЙ."; 
 
           }else
           { 
             $qty = sprintf("%4u", $item["STATUSQUANTITY"])." на сумму:"; 
             $sum = sprintf( "%10.2f" , $item["STATUSQUANTITY"] * $item["PRICE"] );
             $itemStatus = $item['STATUS'];         
           }
           if (($item['STATUSQUANTITY2']*1)>0)
           {
           $itemStatus2= $item['STATUS2']."-";
           $qty2= $item['STATUSQUANTITY2'];
           }else
           {
               $itemStatus2="";
               $qty2="";
           }
           
         $brandCode = $item['BCODE'];
         $itemCode = sprintf("%-15.15s",$item['ICODE'] );
         $price ="цена за одну штуку-".sprintf("%10.2f",$item["PRICE"]);
         $name = sprintf("%-25.25s", $item["CAPTION"]);
         if ($item['WAREHOUSEDATE']!="")
         {
             $warehousedatearray=explode("-",$item['WAREHOUSEDATE']);
             $warehousedate="Ожидается: ".$warehousedatearray[2]."-".$warehousedatearray[1]."-".$warehousedatearray[0];
         }
         else{
               $warehousedate="";
         }
        
         $cur = $cur;
         $str .= $brandCode." : ".$itemCode."\t".$name."\r\n".$price." ".$cur." \r\n".$itemStatus."-".$qty.$sum.$cur."\t" .$warehousedate ."\r\n".$itemStatus2.$qty2."\r\n";
        # $str .= "\t".$itemStatus."\t";// Изменения: ".$item['CHANGE_DESC'];

         $str .="-----------------------------------------------\r\n";

      }
    return $str;
}

function DeletedItemsToString( $arChangedItems, $cur )
{

   $str = "";

   foreach( $arChangedItems as $item  )
      {
      	 $itemStatus = $item['STATUS'];
         $brandCode = $item['BCODE'];
         $itemCode = sprintf("%-15.15s",$item['ICODE'] );
         $price = sprintf("%10.2f",$item["PRICE"]);
         $name = sprintf("%-25.25s", $item["CAPTION"]);
         $qty = sprintf("%4u", $item["QTY"]);
         $sum = sprintf( "%10.2f" , $item["QTY"] * $item["PRICE"] );
         $cur = $cur;
         $str .= $brandCode." : ".$itemCode."\t".$name."\t".$price." ".$cur."\t".$qty."\t".$sum." ".$cur;
		 $str .= "\t".$itemStatus."\t";
         $str .="\r\n";

      }
    return $str;
}

function InsertedItemsToString( $arChangedItems, $cur )
{
  return DeletedItemsToString($arChangedItems, $cur);
}
function replacementToString($from,$to,$currency)
{
	$str = "";
	foreach ($to as $i=>$arVal)
	{
         
        
      	 $itemStatus = $arVal['STATUS'];
         $brandCode = $arVal['BCODE'];
         $itemCode = sprintf("%-15.15s",$arVal['ICODE'] );
         $price = sprintf("%10.2f",$arVal["PRICE"]);
         $name = sprintf("%-25.25s", $arVal["CAPTION"]);
         $qty = sprintf("%4u", $arVal["QTY"]);
         $sum = sprintf( "%10.2f" , $arVal["QTY"] * $arVal["PRICE"] );
         $cur = $currency;
         $oldPosition = "Старый номер ".$from[$i]['ICODE'];
         $str .= $brandCode." : ".$itemCode."\t".$name."\t".$price." ".$cur."\t".$qty."\t".$sum." ".$cur."\t".$itemStatus."\t".$oldPosition;
		 
         $str .="\r\n";
	}
	return $str;
}
 function replacementToStringNewP($from,$to,$currency)
{
    $str = "";
    foreach ($to as $i=>$arVal)
    {
           if ($arVal['STATUSF']=='1' && ($arVal['QTY']*1)>($arVal['STATUSQUANTITY']*1))
           {             
          
           $qty= "(((  ".($arVal['STATUSQUANTITY']*1)." ИЗ ". ($arVal['QTY']*1)." ))) на сумму: ";
           $sum = sprintf( "%10.2f" , $arVal["STATUSQUANTITY"] * $arVal["PRICE"] );
           $itemStatus = $arVal['STATUS']." ВНИМАНИЕ ПО ДАННОЙ ПОЗИЦИИ ВЫКУПЛЕНО НЕ ВСЁ КОЛИЧЕСТВО ДЕТАЛЕЙ, И БЫЛА ПРОВЕДЕНА ЗАМЕНА."; 
           }else
           { 
             $qty = sprintf("%4u", $arVal["STATUSQUANTITY"])."на сумму:"; 
             $sum = sprintf( "%10.2f" , $arVal["STATUSQUANTITY"] * $arVal["PRICE"] );
             $itemStatus = $arVal['STATUS'];         
           }
           if (($arVal["STATUSQUANTITY2"]*1)>0)
           {
           $qty2=$arVal["STATUSQUANTITY2"];
           $itemStatus2 = $arVal['STATUS2']."-";
           }else
           {
               $itemStatus2="";
              $qty2=""; 
           }
         $brandCode = $arVal['BCODE'];
         $itemCode = sprintf("%-15.15s",$arVal['ICODE'] );
         $price ="цена за одну штуку". sprintf("%10.2f",$arVal["PRICE"]);
         $name = sprintf("%-25.25s", $arVal["CAPTION"]);
        # $qty = sprintf("%4u", $arVal["QTY"]);
        # $sum = sprintf( "%10.2f" , $arVal["QTY"] * $arVal["PRICE"] );
         $cur = $currency;
         $oldPosition = "Старый номер ".$from[$i]['ICODE'];
         $str .= $brandCode." : ".$itemCode."\t".$name."\r\n".$price." ".$cur."\r\n".$itemStatus."-".$qty."\t".$sum." ".$cur."\r\n".$itemStatus2.$qty2."\r\n".$oldPosition;
         
         $str .="\r\n";
    }
    return $str;
}

       //  Возвращает текстовое описание статуса товара по его коду
function GetItemStatusDescription( $itemStatus )
{
	 switch( $itemStatus)
  	       {
  	       	 case 0 :
  	       	    return "В работе";
  	            break;

  	       	 case 1 :
  	       	    return  "Выкуплен";
  	            break;

  	       	 case 2 :
  	       	    return  "Отказ";
  	            break;

  	       	 case 3 :
  	       	    return  "Склад";
  	            break;

  	       	 case 4 :
  	       	    return  "Отгружен";
  	            break;

                 case 5:
                    return "В пути";
                    break;
             case 6 :
                return  "Склад Донецк";
                  break;     

  	       }
  	  return "В обработке";
}


          //  Меняем цену и валюту только что добавленного товара в корзине
function UpdateBasketItem( $basketItemID, $price, $currency  )
{
	global $DB;
           $sql = "UPDATE b_sale_basket ";
           $sql .= " SET PRICE='".$price."'";
           $sql .= " WHERE ID='".$basketItemID."'";
           $tmpRes = $DB->Query($sql);

           $sql = "UPDATE b_sale_basket ";
           $sql .= " SET CURRENCY='".$currency."'";
           $sql .= " WHERE ID='".$basketItemID."'";
           $tmpRes = $DB->Query($sql);

                 //  меняем ID товара - чтобы можно было добавлять больше, чем 1 раз

          $sql = "UPDATE b_sale_basket ";
           $sql .= " SET PRODUCT_ID='".$basketItemID."'";
           $sql .= " WHERE ID='".$basketItemID."'";
           $tmpRes = $DB->Query($sql);

}

               //  Обновляем валюту для всех строк заказа в таблице
function UpdateCurrency4BasketItems( $order_id, $currency )
{
   global $DB;


   $sql = "UPDATE b_sale_basket ";
   $sql .= " SET CURRENCY='".$currency."'";
   $sql .= " WHERE ORDER_ID='".$order_id."'";
   $tmpRes = $DB->Query($sql);


}
        // Возвращает флаг, было ли отправлено сообщение по почте о создании заказа
function getOrderSentMailStatus( $order_id )
{
  global $DB;
  $sql = " SELECT IS_MAIL_SENT FROM b_sale_order";
  $sql .= " WHERE ID='".intval($order_id)."'";

  $tmpRes = $DB->Query($sql);
  if( $arRes = $tmpRes->Fetch() )
    return $arRes['IS_MAIL_SENT'];
  else
    return -1;
}

        // Устанавливает флаг, было ли отправлено сообщение по почте о создании заказа
function setOrderSentMailStatus( $order_id, $status )
{
  global $DB;
//  if( intval($tatus) > 0 ) $status = 1;
//    else $status = 0;

  $sql = " UPDATE b_sale_order ";
  $sql .= " SET IS_MAIL_SENT='".$status."'";
  $sql .= " WHERE ID='".intval($order_id)."'";
  $DB->Query($sql);


}

?>