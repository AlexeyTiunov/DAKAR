<?php
/**
 * 
 * Enter description here ...
 * @author gurkovsky
 *
 */
class Appearance_ITG
{
    function __construct()
    {
        
    }
    static public function preparePrice($price,$currency)
    {
        $cena = number_format($price, 2);
        switch ($currency) {
            case 'USD':
                return "$ {$cena}";
            case 'EUR':
                return "&euro; {$cena}";
            case 'UAH':
                return "{$cena} грн.";
        }
    }
    
    static public function getCountOfItems()
    {
        if (CModule::IncludeModule("sale")&& CModule::IncludeModule('iblock'))
        {
            CSaleBasket::Init();
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
            return  $dbBasketItems->SelectedRowsCount();
        }
        else 
        {
            return -1;
        }
    }
    
    static public function isFirstEnter()
    {
        if (stripos($_SERVER['HTTP_REFERER'],$_SERVER['PHP_SELF']) !== false) return false;
        return true;
    }
    public function PriceKoef($Currency , $UserCurrency)      
     { 
         if ($UserCurrency=="USD")  
         {
             if ($Currency=="USD") return 1;
             elseif ($Currency=="UAH") 
             {
                 $DBB = self::manualConnect();
                 $sql="SELECT RATE AS USDRATE FROM b_catalog_currency_rate  WHERE CURRENCY='USD' ORDER BY ID DESC LIMIT 1";
                 $result=$DBB->query($sql);
                 $rate=$result->fetch_assoc();
                  return 1/$rate['USDRATE'];
               
                 
             }
             elseif ($Currency=="EUR") 
             {
                 $DBB = self::manualConnect();  
                 $sql="SELECT RATE AS USDRATE FROM b_catalog_currency_rate  WHERE CURRENCY='USD' ORDER BY ID DESC LIMIT 1";
                 $result=$DBB->query($sql);
                 $rateusd=$result->fetch_assoc();
                 
                 $sql="SELECT RATE AS EURRATE FROM b_catalog_currency_rate  WHERE CURRENCY='EUR' ORDER BY ID DESC LIMIT 1";
                 $result=$DBB->query($sql);
                 $rateeur=$result->fetch_assoc();
                 
                 return  $rateeur['EURRATE']/ $rateusd['USDRATE'];
                 
             } 
             
         }
         elseif ($UserCurrency=="UAH")
         {
              if ($Currency=="UAH") return 1;
              elseif($Currency=="USD")
              {
                    $DBB = self::manualConnect();
                 $sql="SELECT RATE AS USDRATE FROM b_catalog_currency_rate  WHERE CURRENCY='USD' ORDER BY ID DESC LIMIT 1";
                 $result=$DBB->query($sql);
                 $rate=$result->fetch_assoc();
                  return $rate['USDRATE'];
                  
              } 
              elseif($Currency=="EUR")
              {
                  $DBB = self::manualConnect();
                 $sql="SELECT RATE AS EURRATE FROM b_catalog_currency_rate  WHERE CURRENCY='EUR' ORDER BY ID DESC LIMIT 1";
                 $result=$DBB->query($sql);
                 $rate=$result->fetch_assoc();
                  return $rate['EURRATE'];
                  
              }
             
             
             
         }
         
     } 
     public static function manualConnect()
    {
             $port=31006;
        $DB = new mysqli("localhost","bitrix","a251d851","bitrix",$port);
        $DB->set_charset("utf8");
              $DB->query("SET NAMES 'utf8'");
        return $DB;
    }
}
?>