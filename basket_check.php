<?  error_reporting(E_ALL);
 require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php'); 
 require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/general/export.autodoc.php"); 
  $brands="b_iblock_element_prop_s37"; 
  $GLOBALS['BRAND_TABLE_NAME']=$brands;   
  $GLOBALS['BRAND_ID_LOCAL_COLUMN_NAME']="IBLOCK_ELEMENT_ID";
  #$GLOBALS['TECDOC_BRAND_ID_LOCAL_COLUMN_NAME']="DESCRIPTION_71";
  $GLOBALS['TECDOC_BRAND_ID_LOCAL_COLUMN_NAME']="PROPERTY_286"; 
  
  #$GLOBALS['BRAND_NAME_LOCAL_COLUMN_NAME']="PROPERTY_72";
  $GLOBALS['BRAND_NAME_LOCAL_COLUMN_NAME']="PROPERTY_288";  
  $GLOBALS['TECDOC_BRAND_NAME_LOCAL_COLUMN_NAME']=""; 
  
  #$GLOBALS['ACTIVE_BRAND_COLUMN_NAME']="DESCRIPTION_72";
  $GLOBALS['ACTIVE_BRAND_COLUMN_NAME']="DESCRIPTION_287";
 
 if (!function_exists(GetPictureBase64ByItemCode))
 {
    function GetPictureBase64ByItemCode($itemCode,$brandCode)
    {
        global $DB;
        if ($brandCode=='') $brandCode=0;
      $sql="SELECT Base64 FROM b_autodoc_items_catalog_items WHERE ITEM_CODE='{$itemCode}' AND BRAND_CODE={$brandCode} LIMIT 1";
      
      $result=$DB->Query($sql);
      
      $base64pic=$result->Fetch()['Base64'];
      
      if ($base64pic!="")
      {
        return "data:image/jpg;base64,".$base64pic;   
      }else
      {
        return "/images/favicon.png";   
      }  
       
        
        
    } 
     
     
     
 } 
 if (!function_exists(GetBrandNameByCode)) 
 {  
    function GetBrandNameByCode($brand_code)
    {
          global $DB;
        $sql="SELECT {$GLOBALS['BRAND_ID_LOCAL_COLUMN_NAME']} AS ID,{$GLOBALS['BRAND_NAME_LOCAL_COLUMN_NAME']} AS FULLNAME FROM {$GLOBALS['BRAND_TABLE_NAME']} WHERE {$GLOBALS['BRAND_ID_LOCAL_COLUMN_NAME']}='".trim($brand_code)."'"; 
        
           $result =$DB->Query ($sql) ;
           $brand_name=$result->Fetch()['FULLNAME'];        
       
        return $brand_name;
    }
  }
?>

<?
  if (isset($_POST['BASKET_USER_ID_COUNT']))
  {
     require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');       
     if (CModule::IncludeModule("sale")&& CModule::IncludeModule('iblock') ) 
     {  
       CSaleBasket::Init();   
       $dbBasketItems = CSaleBasket::GetList(
                array(
                        "ID" => "ASC"
                    ),
                array(
                        "FUSER_ID" => CSaleBasket::GetBasketUserID(),
                        "LID" => SITE_ID,
                        "ORDER_ID" => "NULL"
                    ),
                false,
                false,
                array("ID", "PRODUCT_ID","NAME", "QUANTITY",
                      "CAN_BUY", "PRICE", "NOTES")
            );
       
      
      echo ($dbBasketItems->SelectedRowsCount()>0)?$dbBasketItems->SelectedRowsCount():"0";
     }  
  } elseif($_GET['BASKET_SHOW'])
  {
      require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php"); 
      #$APPLICATION->SetPageProperty("keywords", "Автосервис автодок, Корзина, СТО Киев, СТО Toyota, СТО Mitsubishi,СТО Lexus"); 
    #  $APPLICATION->SetPageProperty("description", "Автосервис Автодок Киев сервисное обслуживание и ремонт автомобилей  Mitsubishi,Toyota,Lexus,Калькулятор техобслуживание");
     # $APPLICATION->SetTitle("Ваша Корзина");
             #GetBasketItemProperty($arBasket["ID"], "IsReturnable" )
       session_start();       
       global $USER;
             
             if (CModule::IncludeModule("sale")&& CModule::IncludeModule('iblock') ) 
             {  
                 
                # CSaleBasket::Init();
                
                if (isset($_POST['DELETE'])) 
                {
                      foreach ($_POST['DELETE'] as $id=>$value)
                      {
                          CSaleBasket::Delete(intval($id));
                                             
                          
                          
                      }
                                    
                 }elseif (isset($_POST['ORDER_BASKET']))
                 {
                         $basketUserID = CSaleBasket::GetBasketUserID();
                         $dbBasketItems = CSaleBasket::GetList(
                            array(
                                    "ID" => "ASC"
                                ),
                            array(
                                    "FUSER_ID" => $basketUserID,
                                    "LID" => SITE_ID,
                                    "ORDER_ID" => "NULL"
                                ),
                            false,
                            false,
                            array("ID", "PRODUCT_ID","NAME", "QUANTITY","CURRENCY",
                                  "CAN_BUY", "PRICE", "NOTES")
                       );
                       
                       $_GET['TOTAL_SUM']=0.00;
                      while ($arItem=$dbBasketItems->Fetch())
                      {
                        /* $_GET['ID']=$arItem['ID'];
                         $_GET['ITEMCODE']=GetBasketItemProperty($arItem["ID"], "ItemCode");
                         $_GET['BRANDCODE']=GetBasketItemProperty($arItem["ID"], "Brand");
                         $_GET['CAPTION']=$arItem['NAME'];
                         $_GET['SUM']= number_format($arItem['PRICE']*$arItem['QUANTITY'],2,',',' ');
                         $_GET['QUANTITY']=number_format($arItem['QUANTITY'],2,',',' ');
                         $_GET['PRICE']=number_format($arItem['PRICE'],2,',',' ');  
                         $_GET['CURRENCY']=$arItem['CURRENCY'];
                         $_GET['PICTURE_BASE64']=GetPictureBase64ByItemCode($_GET['ITEMCODE'],$_GET['BRANDCODE']) ; */
                         $_GET['TOTAL_SUM']+=($arItem['PRICE']*$arItem['QUANTITY']); 
                         $_GET['CURRENCY']=$arItem['CURRENCY'];
                         # include $_SERVER['DOCUMENT_ROOT']."/basket_show_template.php";  
                       # var_dump($_GET) ; 
                      }
                     
                     
                     
                     
                      $arFields = array(
                           "LID" => SITE_ID,
                           "PERSON_TYPE_ID" => 1,
                           "PAYED" => "N",
                           "CANCELED" => "N",
                           "STATUS_ID" => "N",
                           "PRICE" => $_GET['TOTAL_SUM'],
                           "CURRENCY" => $_GET['CURRENCY'],
                           "USER_ID" => IntVal($USER->GetID()),
                           #"PAY_SYSTEM_ID" => ,
                           "ALLOW_DELIVERY"=> N,
                           "TAX_VALUE" => 0.0,
                           "USER_DESCRIPTION" => "",
                           "REGION_CODE"  => 1
                            );
                     $ORDER_ID = CSaleOrder::Add($arFields);
                     CSaleBasket::OrderBasket($ORDER_ID, $_SESSION["SALE_USER_ID"], SITE_ID);
                     if ($ORDER_ID>0)
                      {
                         # echo  $ORDER_ID;
                         header("Location:/order_check.php"); 
                      } else
                      {
                          echo "ERROR";
                          
                      }
                      exit();           
                 } 
                    
                
                

                 $basketUserID = CSaleBasket::GetBasketUserID();
                 $dbBasketItems = CSaleBasket::GetList(
                    array(
                            "ID" => "ASC"
                        ),
                    array(
                            "FUSER_ID" => $basketUserID,
                            "LID" => SITE_ID,
                            "ORDER_ID" => "NULL"
                        ),
                    false,
                    false,
                    array("ID", "PRODUCT_ID","NAME", "QUANTITY","CURRENCY",
                          "CAN_BUY", "PRICE", "NOTES")
               );
              ?>
                   <form name="" action="/basket_check.php?BASKET_SHOW=Y" method="POST" enctype="multipart/form-data">  
                
                  
                 
              <?
               $_GET['TOTAL_SUM']=0.00;
              while ($arItem=$dbBasketItems->Fetch())
              {
                 $_GET['ID']=$arItem['ID'];
                 $_GET['ITEMCODE']=GetBasketItemProperty($arItem["ID"], "ItemCode");
                 $_GET['BRANDCODE']=GetBasketItemProperty($arItem["ID"], "Brand");
                 $_GET['BRANDNAME']=GetBrandNameByCode($_GET['BRANDCODE']);
                 $_GET['CAPTION']=$arItem['NAME'];
                 $_GET['SUM']= number_format($arItem['PRICE']*$arItem['QUANTITY'],2,',',' ');
                 $_GET['QUANTITY']=number_format($arItem['QUANTITY'],2,',',' ');
                 $_GET['PRICE']=number_format($arItem['PRICE'],2,',',' ');  
                 $_GET['CURRENCY']=$arItem['CURRENCY'];
                 $_GET['PICTURE_BASE64']=GetPictureBase64ByItemCode($_GET['ITEMCODE'],$_GET['BRANDCODE']) ; 
                 $_GET['TOTAL_SUM']+=($arItem['PRICE']*$arItem['QUANTITY']); 
                  include $_SERVER['DOCUMENT_ROOT']."/basket_show_template.php";  
               # var_dump($_GET) ; 
              }
          
          
          }
          ?><div class="container" style='width:70%;float:left;margin-left: 25%;background: none; box-shadow:none;'>
                  <p style="font-size: 26px;margin-left: 18%" >Итог:&nbsp<?=number_format($_GET['TOTAL_SUM'],2,',',' ');?> </p>
                  <input type='submit' name='ORDER_BASKET' value='Оформить Заказ' style="background-color: #c01717;margin-left: 14%;margin-bottom: 2%;font-size: 24px; border:2px solid black;">  </input>
                 </form>          </div>      
               <div class="blankSeparator"></div>  
          <?
          require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
  } 
    
?>
