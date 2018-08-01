<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
   session_start();  
   global $USER;
   /**
     $_GET['ITEMCODE']
     $_GET['BRANDCODE']
     $_GET['isService']
     $_GET['isItem']  
     $_GET['PRICE']
     $_GET['QUANTITY']
     $_GET['CAPTION']
   */
 # error_reporting(E_ALL);
   if(isset($_GET['CHECK_AUTH']))
   {
       if (!$USER->IsAuthorized())
       {
           die("0");
       }
       
   }
    
 
  if (!CModule::IncludeModule("sale"))
   {
       CModule::IncludeModule("sale"); 
   }    
  if (! CModule::IncludeModule('iblock'))
    {
        CModule::IncludeModule("iblock");  
    }     
   
    CSaleBasket::Init(); 
    
    
    $productID = intval(preg_replace('/[^0-9]/', '', $_GET['ITEMCODE'].$_GET['BRANDCODE'])); 
    
    $arFields = array(
                "PRODUCT_ID"             => $productID,
                "PRODUCT_PRICE_ID"        => 0,
                "PRICE"                 => $_GET['PRICE'],
                "CURRENCY"                 => $_GET['CURRENCY'],
                "WEIGHT"                 => 0,
                "QUANTITY"                 => intval($_GET['QUANTITY']),
                "LID"                     => LANG,
                "DELAY"                 => "N",
                "CAN_BUY"                 => "Y",
                "NAME"                     => $_GET['CAPTION'],
                "MODULE"                 => "AUTODOC"
                );
                
    $arProps = array();
        $arProps[] = array("NAME" => "Валюта",                     "CODE" => "Currency",    "VALUE" => $_GET['CURRENCY']);
        $arProps[] = array("NAME" => "Артикул",                    "CODE" => "ItemCode",    "VALUE" => $_GET['ITEMCODE']);
        $arProps[] = array("NAME" => "Код бренда",                 "CODE" => "Brand",        "VALUE" => $_GET['BRANDCODE']);
        $arProps[] = array("NAME" => "Услуга",                      "CODE" => "isService",    "VALUE" => $_GET['isService']);
        $arProps[] = array("NAME" => "Товар",                      "CODE" => "isService",    "VALUE" => $_GET['isItem']); 
        if (isset($_GET['PLATE_NUMBER']) &&$_GET['PLATE_NUMBER']!="" )
        {
            $arProps[] = array("NAME" => "ГосНомер",                     "CODE" => "PlateNumber",    "VALUE" => $_GET['PLATE_NUMBER']);    
            
        }
        
         if (isset($_GET['USER_DATE']) && $_GET['USER_DATE']!="" )
        {
            $arProps[] = array("NAME" => "ДатаКлиента",                     "CODE" => "UserDate",    "VALUE" =>$_GET['USER_DATE']);    
            
        } 
        
        
       # $arProps[] = array("NAME" => "Статус строки заказа",    "CODE" => "ItemStatus",    "VALUE" => "0");     
    $arFields["PROPS"] = $arProps;   
       
       
    #var_dump($arFields);   
       
    $basketItemID = CSaleBasket::Add($arFields);  
    $sql = "UPDATE b_sale_basket SET
                                        PRICE='{$_GET['PRICE']}',
                                        CURRENCY='{$_GET['CURRENCY']}',
                                        PRODUCT_ID='{$productID}'
                                    WHERE 
                                        ID='{$basketItemID}'";
    $tmpRes = $DB->Query($sql);     
    #var_dump($basketItemID); 
   
    if ($_GET['RECIEVE_INFO']) 
    {
       echo   $basketItemID;
    }
    
   
    
    
    
    
?>