<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php'); 
  global $DB;
  global $USER;
 # error_reporting(E_ALL);  
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
    function GetBase64ImageString($modelTypeID,$modelID)
    {
        global $DB;
        
        $sql="SELECT PICTURE_BASE64 FROM  b_autodoc_carmodels_types  WHERE ID={$modelTypeID} AND CARMODEL_ID={$modelID} LIMIT 1";
        
        $result=$DB->Query($sql);
        
        return $result->Fetch()['PICTURE_BASE64'];   
        
        
        
    }
    
    function GetModelTypeDescription($modelTypeID)
    {
        
        global $DB;
        
        $sql="SELECT DESCRIPTION FROM  b_autodoc_carmodels_types  WHERE ID={$modelTypeID} LIMIT 1";
        
        $result=$DB->Query($sql);
        
        return $result->Fetch()['DESCRIPTION'];        
        
        
    }
  
   function ShowModelTypeIDInfo($modelTypeID,$modelID)
   {
       $imgSrc=GetImageForAvailableItem(GetBase64ImageString($modelTypeID,$modelID)) ;
       $modelTypeDescription=GetModelTypeDescription($modelTypeID);
       
       $modelTypeDescriptionHTML=""; 
       $modelTypeDescriptionHTML.="<div>  </div> ";   
       
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
  
   if (!isset($_POST['BrandID']) || !isset($_POST['ModelID']) || !isset($_POST['modelTypeID']))
  {
      die("ERRORRRR");
  }  
  
  $brandID=preg_replace("/[^0-9]/","",$_POST['BrandID']);   
  $modelID=preg_replace("/[^0-9]/","",$_POST['ModelID']);
  $modelTypeID=preg_replace("/[^0-9]/","",$_POST['modelTypeID']);  
  
  $imgSrc=GetImageForAvailableItem(GetBase64ImageString($modelTypeID,$modelID)) ;
  $modelTypeDescription=GetModelTypeDescription($modelTypeID);
  $modelName=GetModelNameByID($modelID);
  $modelTypeName=GetModelTypeNameByID($modelTypeID);
    
  include $_SERVER['DOCUMENT_ROOT']."/catalog/model_info_template.php";  
    
    
?>