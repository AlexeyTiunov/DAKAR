<?
 require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php'); 
 global $DB;
 global $USER;
 $groupArray=$USER->GetUserGroupArray();
foreach ($groupArray as $id=>$i)
{
   
   if ($i==1 || $i==7)
    #if ($i==1) 
    {  #echo'www';
      $groupCheck=true; 
      break;   
    } else
    {
          $groupCheck=false ;
    }
} 
 $brands="b_iblock_element_prop_s37"; 
 $GLOBALS['BRAND_TABLE_NAME']=$brands;   
 $GLOBALS['BRAND_ID_LOCAL_COLUMN_NAME']="IBLOCK_ELEMENT_ID";
  #$GLOBALS['TECDOC_BRAND_ID_LOCAL_COLUMN_NAME']="DESCRIPTION_71";
  $GLOBALS['TECDOC_BRAND_ID_LOCAL_COLUMN_NAME']="PROPERTY_286"; 
    
 # $GLOBALS['BRAND_NAME_LOCAL_COLUMN_NAME']="PROPERTY_72"; 
 # $GLOBALS['TECDOC_BRAND_NAME_LOCAL_COLUMN_NAME']=""; 
  
 # $GLOBALS['ACTIVE_BRAND_COLUMN_NAME']="DESCRIPTION_72"; 
  
  $GLOBALS['BRAND_NAME_LOCAL_COLUMN_NAME']="PROPERTY_288"; 
  $GLOBALS['TECDOC_BRAND_NAME_LOCAL_COLUMN_NAME']=""; 
  
  $GLOBALS['ACTIVE_BRAND_COLUMN_NAME']="DESCRIPTION_287";
 #################################################
    function GetGroupNameByID($GroupID)
  {
      global $DB;
      $sql="SELECT GROUP_NAME FROM b_autodoc_items_catalog_groups WHERE ID={$GroupID} LIMIT 1";
      $result=$DB->Query($sql);
      
      return $result->Fetch()['GROUP_NAME'];
      
  }
  function GetGroupTypeNameByID($GroupTypeID)  
  {
      global $DB;
      $sql="SELECT TYPE_NAME FROM b_autodoc_items_catalog_groups_types WHERE ID={$GroupTypeID}  LIMIT 1";
      $result=$DB->Query($sql);
      
      return $result->Fetch()['TYPE_NAME'];
      
  }
  function GetModelNameByID($modelID)
  {
       global $DB;
      $sql="SELECT ModelName FROM  b_autodoc_carmodels  WHERE ID={$modelID}  LIMIT 1";
      $result=$DB->Query($sql);
      
      return $result->Fetch()['ModelName'];
      
  }
   function GetModelTypeNameByID($modelTypeName)
   {
       global $DB;
      $sql="SELECT TYPE_NAME FROM  b_autodoc_carmodels_types  WHERE ID={$modelTypeName}  LIMIT 1";
      $result=$DB->Query($sql);
      
      return $result->Fetch()['TYPE_NAME'];
       
   }  
 
  
   function CheckCatalogItemsArrayForAvailable($catalogItemsForSearch)
   {
       if (count($catalogItemsForSearch)==0)
          {
              return false;     
              
          }
      foreach ($catalogItemsForSearch as $brandCode=>$ItemProperties )
      {
          foreach ($ItemProperties as $itemCode=>$itemValue)
          {
              if (CheckItemCodeForAvailable($brandCode,$itemCode,1)===true   )
              {
                 //  $itemValue['IS_AVAILABLE']=true;
                   $catalogItemsForSearch[$brandCode][$itemCode]['IS_AVAILABLE']=true; 
                   $catalogItemsForSearch[$brandCode][$itemCode]['PRICE']=GetPriceForAvailableItem($brandCode,$itemCode,1);
                   $catalogItemsForSearch[$brandCode][$itemCode]['CURRENCY']=GetCurrencyForAvailableItem($brandCode,$itemCode,1);
                   $catalogItemsForSearch[$brandCode][$itemCode]['QUANTITY']=GetQuantityForAvailableItem($brandCode,$itemCode,1);  
              }
              
              
          }
          
          
      }    
          
     return  $catalogItemsForSearch; 
   }
   
   function CheckItemCodeForAvailable($brandCode,$itemCode,$suppcode)
   {
       global $DB; 
       $sql="SELECT Quantity FROM b_autodoc_prices_suppUA  WHERE BrandCode={$brandCode} AND ItemCode='{$itemCode}' AND SuppCode={$suppcode} LIMIT 1" ;
       
       $result=$DB->Query($sql);
       $checkQuantity=$result->Fetch();
       
       #echo "#################" ;
         # var_dump($checkQuantity) ;
        #echo "#################" ; 
       
       return intval($checkQuantity['Quantity'])>0;
   }
   function GetQuantityForAvailableItem($brandCode,$itemCode,$suppcode)
   {
       global $DB; 
       $sql="SELECT Quantity FROM b_autodoc_prices_suppUA  WHERE BrandCode={$brandCode} AND ItemCode='{$itemCode}' AND SuppCode={$suppcode}  LIMIT 1" ;
       
       $result=$DB->Query($sql);
       $Quantity=$result->Fetch()['Quantity'];
       
       return $Quantity;
       
   }
   function GetPriceForAvailableItem($brandCode,$itemCode,$suppcode)
   {
      global $DB; 
      $sql="SELECT Price FROM b_autodoc_prices_suppUA  WHERE BrandCode={$brandCode} AND ItemCode='{$itemCode}' AND SuppCode={$suppcode} LIMIT 1";
      
      $result=$DB->Query($sql);
      $Price=$result->Fetch()['Price'];
      
      if ($Price=="")
      {
          return 0;
      }
      return  $Price;  
       
   }
   function GetCurrencyForAvailableItem($brandCode,$itemCode,$suppcode)
   {
      global $DB; 
      $sql="SELECT Currency FROM b_autodoc_prices_suppUA  WHERE BrandCode={$brandCode} AND ItemCode='{$itemCode}' AND SuppCode={$suppcode} LIMIT 1";
      
      $result=$DB->Query($sql);
      $Currency=$result->Fetch()['Currency'];
      
      if ($Currency=="")
      {
          return "USD";
      }
      if ($Currency=="USD") 
       {
           return "USD";
       } 
       if ($Currency=="UAH") 
       {
           
         return "UAH";  
           
       }  
      return  $Currency;  
       
   } 
   function ShowCurrency($currency)
   {
       if ($currency=="")
      {
          return "USD";
      }
      if ($currency=="USD") 
       {
           return "$";
       } 
       if ($currency=="UAH") 
       {
           
         return "грн.";  
           
       }  
      return  $Currency;  
       
   }
   function GetCaptionGetCurrencyForAvailableItem($brandCode,$itemCode,$suppcode)
   {
         global $DB;
        $sql="SELECT Caption FROM b_autodoc_prices_suppUA  WHERE BrandCode={$brandCode} AND ItemCode='{$itemCode}' AND SuppCode={$suppcode} LIMIT 1";
      
      $result=$DB->Query($sql);
      $Caption=$result->Fetch()['Caption']; 
       return $Caption;
   }
    
    function GetBrandNameByCode($brand_code)
    {
          global $DB;
        $sql="SELECT {$GLOBALS['BRAND_ID_LOCAL_COLUMN_NAME']} AS ID,{$GLOBALS['BRAND_NAME_LOCAL_COLUMN_NAME']} AS FULLNAME FROM {$GLOBALS['BRAND_TABLE_NAME']} WHERE {$GLOBALS['BRAND_ID_LOCAL_COLUMN_NAME']}='".trim($brand_code)."'"; 
        
           $result =$DB->Query ($sql) ;
           $brand_name=$result->Fetch()['FULLNAME'];        
       
        return $brand_name;
    }
    function GetImageForAvailableItem($dbValue)
    {
        if ($dbValue==null || $dbValue=="")
        {
            return "src='/images/favicon.png'";
            
        }else
        {
           
            return "src='data:image/png;base64,{$dbValue}'";
        }
        
        
        
    }
   
    #################FUNCTION FOR TEMPLATE###############################      
    
     function ShowItemImage($base64Image)
     { 
         global $USER;
     
        
       
         
         
         
     }
    
    
    ################################################      
  
   $brandID=preg_replace("/[^0-9]/","",$_POST['BrandID']);   
   $modelID=preg_replace("/[^0-9]/","",$_POST['ModelID']);
   $modelTypeID=preg_replace("/[^0-9]/","",$_POST['modelTypeID']);
   $groupTypeID=preg_replace("/[^0-9]/","",$_POST['groupTypeID']); 
   
   
   
   if (!isset($_POST['BrandID']) || !isset($_POST['ModelID']) || !isset($_POST['modelTypeID']) || !isset($_POST['groupTypeID']))
  {
      die("ERRORRRR");
  }
  
  
 # $sql="SELECT * FROM b_autodoc_items_catalog_items WHERE BRAND_CODE={$brandID} AND MODEL_ID={$modelID} AND MODEL_TYPE_ID={$modelTypeID} AND GROUP_TYPE_ID={$groupTypeID}";  
   $sql="SELECT * FROM b_autodoc_items_catalog_items WHERE                           MODEL_ID={$modelID} AND MODEL_TYPE_ID={$modelTypeID} AND GROUP_TYPE_ID={$groupTypeID}";  
  
  $result=$DB->Query($sql);
  $catalogItemsForSearch=array();
  while($catalogItemsArray=$result->Fetch())
  {
    $catalogItemsForSearch[$catalogItemsArray['BRAND_CODE']][$catalogItemsArray['ITEM_CODE']]['ITEM_CODE']=$catalogItemsArray['ITEM_CODE']; 
    $catalogItemsForSearch[$catalogItemsArray['BRAND_CODE']][$catalogItemsArray['ITEM_CODE']]['BRAND_CODE']=$catalogItemsArray['BRAND_CODE'];
    $catalogItemsForSearch[$catalogItemsArray['BRAND_CODE']][$catalogItemsArray['ITEM_CODE']]['BRAND_NAME']=GetBrandNameByCode($catalogItemsArray['BRAND_CODE']); 
    $catalogItemsForSearch[$catalogItemsArray['BRAND_CODE']][$catalogItemsArray['ITEM_CODE']]['PRICE']=0.00;
    $catalogItemsForSearch[$catalogItemsArray['BRAND_CODE']][$catalogItemsArray['ITEM_CODE']]['CURRENCY']="USD";     
    $catalogItemsForSearch[$catalogItemsArray['BRAND_CODE']][$catalogItemsArray['ITEM_CODE']]['PICTURE']=GetImageForAvailableItem($catalogItemsArray['Base64']);
    $catalogItemsForSearch[$catalogItemsArray['BRAND_CODE']][$catalogItemsArray['ITEM_CODE']]['WEIGHT']="";
    $catalogItemsForSearch[$catalogItemsArray['BRAND_CODE']][$catalogItemsArray['ITEM_CODE']]['IS_AVAILABLE']=false;
    $catalogItemsForSearch[$catalogItemsArray['BRAND_CODE']][$catalogItemsArray['ITEM_CODE']]['QUANTITY']="";
    $catalogItemsForSearch[$catalogItemsArray['BRAND_CODE']][$catalogItemsArray['ITEM_CODE']]['CAPTION']=($catalogItemsArray['Caption']=="" || $catalogItemsArray['Caption']==null)?GetCaptionGetCurrencyForAvailableItem($catalogItemsArray['BRAND_CODE'],$catalogItemsArray['ITEM_CODE'],1):$catalogItemsArray['Caption'] ; 
      
      
  }
  
  if (count($catalogItemsForSearch)==0)
  {
      die("");     
      
  }
  
  
  //var_dump(CheckCatalogItemsArrayForAvailable($catalogItemsForSearch) );
  
    $modelName=GetModelNameByID($modelID);
    $modelTypeName=GetModelTypeNameByID($modelTypeID);
    $groupTypeName=GetGroupTypeNameByID($groupTypeID);
    $itemArray=CheckCatalogItemsArrayForAvailable($catalogItemsForSearch);
    
    include "show_item_template.php"; 
    
    
?>