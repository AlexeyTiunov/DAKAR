<?
 require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php'); 
 global $DB; 
 
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
 
 
 
 
 ################################################
 
 
 
  
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
              if (CheckItemCodeForAvailable($brandCode,$itemCode)===true   )
              {
                 //  $itemValue['IS_AVAILABLE']=true;
                   $catalogItemsForSearch[$brandCode][$itemCode]['IS_AVAILABLE']=true; 
              }
              
              
          }
          
          
      }    
          
     return  $catalogItemsForSearch; 
   }
   
   function CheckItemCodeForAvailable($brandCode,$itemCode)
   {
       global $DB; 
       $sql="SELECT Quantity FROM b_autodoc_prices_suppUA  WHERE BrandCode={$brandCode} AND ItemCode='{$itemCode}' LIMIT 1" ;
       
       $result=$DB->Query($sql);
       $checkQuantity=$result->Fetch();
       
       #echo "#################" ;
         # var_dump($checkQuantity) ;
        #echo "#################" ; 
       
       return intval($checkQuantity['Quantity'])>0;
   }
  
   $brandID=preg_replace("/[^0-9]/","",$_POST['BrandID']);   
   $modelID=preg_replace("/[^0-9]/","",$_POST['ModelID']);
   $modelTypeID=preg_replace("/[^0-9]/","",$_POST['modelTypeID']);
   $groupTypeID=preg_replace("/[^0-9]/","",$_POST['groupTypeID']); 
   
   
   
   if (!isset($_POST['BrandID']) || !isset($_POST['ModelID']) || !isset($_POST['modelTypeID']) || !isset($_POST['groupTypeID']))
  {
      die("ERRORRRR");
  }
  
  
  $sql="SELECT * FROM b_autodoc_items_catalog_items WHERE BRAND_CODE={$brandID} AND MODEL_ID={$modelID} AND MODEL_TYPE_ID={$modelTypeID} AND GROUP_TYPE_ID={$groupTypeID}";  
  
  $result=$DB->Query($sql);
  $catalogItemsForSearch=array();
  while($catalogItemsArray=$result->Fetch())
  {
    $catalogItemsForSearch[$catalogItemsArray['BRAND_CODE']][$catalogItemsArray['ITEM_CODE']]['ITEM_CODE']=$catalogItemsArray['ITEM_CODE'];  
    $catalogItemsForSearch[$catalogItemsArray['BRAND_CODE']][$catalogItemsArray['ITEM_CODE']]['PICTURE']="";
    $catalogItemsForSearch[$catalogItemsArray['BRAND_CODE']][$catalogItemsArray['ITEM_CODE']]['WEIGHT']="";
    $catalogItemsForSearch[$catalogItemsArray['BRAND_CODE']][$catalogItemsArray['ITEM_CODE']]['IS_AVAILABLE']=false;  
      
      
  }
  
  if (count($catalogItemsForSearch)==0)
  {
      die("");     
      
  }
  
  
  // var_dump(CheckCatalogItemsArrayForAvailable($catalogItemsForSearch) );
  
    $modelName=GetModelNameByID($modelID);
    $modelTypeName=GetModelTypeNameByID($modelTypeID);
    $groupTypeName=GetGroupTypeNameByID($groupTypeID);
    $itemArray=CheckCatalogItemsArrayForAvailable($catalogItemsForSearch);
    #var_dump($itemArray);
    
    include $_SERVER['DOCUMENT_ROOT']."/catalog/show_item_template.php"; 
    
    
?>