<?
  require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php'); 
  global $DB; 
  function ShowCatalogListArray($catalogListArray,$brandID,$modelID,$modelTypeID)
  {
       $catalogListString="";
       
       foreach ($catalogListArray as $groupID=>$groupType) 
       {
           $groupName=GetGroupNameByID($groupID);
           $catalogListString.="<a href='#' class='catalog_group_a' group_id='{$groupID}' brand_id='{$brandID}' model_type_id='{$modelTypeID}' model_id='{$modelID}' ><p class='catalog_group' group_id='{$groupID}' align='left'>{$groupName}</p></a>";   
           foreach($groupType as $typeName=>$value)
           {
              $catalogListString.="<a href='#' class='catalog_group_type' brand_id='{$brandID}' model_type_id='{$modelTypeID}' model_id='{$modelID}' group_type_id='{$value}'><p  align='center'>{$typeName}</p></a>"; 
               
           }
           
       } 
     return  $catalogListString;
      
  }
  function GetGroupNameByID($GroupID)
  {
      global $DB;
      $sql="SELECT GROUP_NAME FROM b_autodoc_items_catalog_groups WHERE ID={$GroupID} LIMIT 1";
      $result=$DB->Query($sql);
      
      return $result->Fetch()['GROUP_NAME'];
      
  }
  function GetGroupTypeNameByID($GroupID,$GroupTypeID)  
  {
      global $DB;
      $sql="SELECT TYPE_NAME FROM b_autodoc_items_catalog_groups_types WHERE ID={$GroupTypeID} AND GROUP_ID={$GroupID} LIMIT 1";
      $result=$DB->Query($sql);
      
      return $result->Fetch()['TYPE_NAME'];
      
  }
    if (!$USER->IsAuthorized())
     {   
         
       
        # $_SESSION['BACKULRSA']="/catalog.php";
        # $_SESSION['MASSAGE']="Пройдите авторизацию.";  
        # header('Location:/SimpleAuth/');     
            
            
            
     }
  if (!isset($_POST['BrandID']) || !isset($_POST['ModelID']) || !isset($_POST['modelTypeID']))
  {
      die("ERRORRRR");
  }  
  
   $brandID=preg_replace("/[^0-9]/","",$_POST['BrandID']);   
   $modelID=preg_replace("/[^0-9]/","",$_POST['ModelID']);
   $modelTypeID=preg_replace("/[^0-9]/","",$_POST['modelTypeID']);
  
  
   $sql="SELECT * FROM b_autodoc_items_catalog_structure WHERE BRAND_ID={$brandID} AND MODEL_ID={$modelID} AND MODEL_TYPE_ID={$modelTypeID} ";
   
   $result=$DB->Query($sql);
   $catalogListArray=array();
   while ($catalogListStructureArray=$result->Fetch())
   {
       #$groupName=GetGroupNameByID($catalogListStructureArray['GROUP_ID']);
       
       $groupTypeName= GetGroupTypeNameByID($catalogListStructureArray['GROUP_ID'],$catalogListStructureArray['GROUP_TYPE_ID']) ; 
       $catalogListArray[$catalogListStructureArray['GROUP_ID']][$groupTypeName]=$catalogListStructureArray['GROUP_TYPE_ID'];       
       
       
   }
   
   #var_dump($catalogListArray); 
  echo  ShowCatalogListArray($catalogListArray,$brandID,$modelID,$modelTypeID);   
    
?>