<?
  require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');  
 # global $DBR;
  global $DB;
  $catalog_groups=" b_autodoc_items_catalog_groups";
  $catalog_groups_types="b_autodoc_items_catalog_groups_types";
  $brands="b_iblock_element_prop_s37";
  $carmodel="b_autodoc_carmodels";
  $catalog_items="b_autodoc_items_catalog_items";
  $catalog_items_structure = "b_autodoc_items_catalog_structure";
  $carmodel_types="b_autodoc_carmodels_types";
  #var_dump($_POST) ;
  function ConnectLocalDB()
  {
     
     
     $db=new CDatabase;
     $db->Connect("localhost:31006","dakar","bitrix","a251d851");
      
     return $db; 
  }
  function ConnectTecDocDB()
  {
    $dbr=new CDatabase;
    $dbr->Connect("localhost:31006","TecDoc","bitrix","a251d851");  
      
     return $dbr; 
  }
  function RecieveAllGroupsArray($table_name)
  {
      global $DB;
      $sql="SELECT * FROM {$table_name}";      
      $result=$DB->Query($sql);
      $allGroupArray=array();
      while  ($groupArray=$result->Fetch())
      {
         $allGroupArray[$groupArray['ID']]['ID']=$groupArray['ID'];
          
         $allGroupArray[$groupArray['ID']]['GROUP_NAME']=$groupArray['GROUP_NAME']; 
          
          
      }
      
      
     return  $allGroupArray;
      
      
  } 
  function ShowAllGroupArray($allGroupArray)
  {
      $selectOptionsString="";
      $selectOptionsString.="<select id='group_select' name='group_select' style='border-radius:5px; height:35px;'>";
      $selectOptionsString.="<option value='0'>выбрать группу</option>";
      foreach($allGroupArray as $id=>$item)
      {
        
         $selectOptionsString.="<option value='{$id}'>{$item['GROUP_NAME']}</option>";     
          
          
          
      } 
      $selectOptionsString.="</select>";  
      
     return  $selectOptionsString;
  }
  function AddGroup($group_name,$table_name)
  {
      global $DB;
      $sql="INSERT INTO {$table_name}(ID,GROUP_NAME) VALUES('','{$group_name}')";
      $DB->Query($sql); 
      
  }
  function AddGroupType($group_id,$type_name,$table_name)
  {
      global $DB;
      $sql="INSERT INTO {$table_name}(ID,GROUP_ID,TYPE_NAME) VALUES('',{$group_id},'{$type_name}')";
      $DB->Query($sql); 
      
  }
  
  function RecieveAllGroupTypesArray($table_name)
  {
       global $DB;
      $sql="SELECT * FROM {$table_name}";      
      $result=$DB->Query($sql);
      $allGroupTypeArray=array();
      while  ($groupTypeArray=$result->Fetch())
      {
         $allGroupTypeArray[$groupTypeArray['ID']]['ID']=$groupTypeArray['ID'];           
         $allGroupTypeArray[$groupTypeArray['ID']]['GROUP_ID']=$groupTypeArray['GROUP_ID']; 
         $allGroupTypeArray[$groupTypeArray['ID']]['TYPE_NAME']=$groupTypeArray['TYPE_NAME'];   
          
      }
      
      
     return  $allGroupTypeArray;
      
      
      
  } 
  
   function RecieveGroupTypeOptionsByGroupID($table_name,$groupID)
   {
        global $DB;
      $sql="SELECT * FROM {$table_name} WHERE GROUP_ID={$groupID}";      
      $result=$DB->Query($sql);
      $allGroupTypeArray=array();
      while  ($groupTypeArray=$result->Fetch())
      {
         $allGroupTypeArray[$groupTypeArray['ID']]['ID']=$groupTypeArray['ID'];           
         $allGroupTypeArray[$groupTypeArray['ID']]['GROUP_ID']=$groupTypeArray['GROUP_ID']; 
         $allGroupTypeArray[$groupTypeArray['ID']]['TYPE_NAME']=$groupTypeArray['TYPE_NAME'];   
          
      }
      
      
     return  $allGroupTypeArray;
      
       
   } 
  function ShowOptionsAllGroupTypesArray($allGroupTypesArray)
  {
      $selectOptionsString="";
      $selectOptionsString.="<select id='group_type_select' name='group_type_select' style='border-radius:5px; height:35px;'>";
      $selectOptionsString.="<option value='0'>выбрать тип</option>";
      foreach($allGroupTypesArray as $id=>$item)
      {
        
         $selectOptionsString.="<option value='{$id}'>{$item['TYPE_NAME']}</option>";     
          
          
          
      } 
      $selectOptionsString.="</select>";  
      
     return  $selectOptionsString;
       
      
  }
  function RecieveBrandsArray($table_name)
  {
      global $DB;
      $sql="SELECT IBLOCK_ELEMENT_ID, PROPERTY_286, PROPERTY_287, PROPERTY_288 FROM {$table_name} WHERE DESCRIPTION_287='1' ORDER BY PROPERTY_288 ASC ";      
      $result=$DB->Query($sql);
      $allBrands=array();
      while  ($brandArray=$result->Fetch())
      {
         $allBrands[$brandArray['IBLOCK_ELEMENT_ID']]['ID']=$brandArray['IBLOCK_ELEMENT_ID'];
         $allBrands[$brandArray['IBLOCK_ELEMENT_ID']]['BRAND_NAME']=$brandArray['PROPERTY_288']; 
         $allBrands[$brandArray['IBLOCK_ELEMENT_ID']]['TECDOC_ID']=intval($brandArray['PROPERTY_286']); 
         $allBrands[$brandArray['IBLOCK_ELEMENT_ID']]['COMMON_ID']=$brandArray['IBLOCK_ELEMENT_ID']."///".intval($brandArray['PROPERTY_286']);
                                                                
          
      }  
      
     return $allBrands;
  }
  
  function ShowBrandsArray($allBrands)
  {
    $selectOptionsString="";
      $selectOptionsString.="<select id='brand_select' name='brand_select' style='border-radius:5px; height:35px;' >";
      $selectOptionsString.="<option value='0'>выбрать бренд</option>";
      foreach($allBrands as $id=>$item)
      {
        
         $selectOptionsString.="<option value='{$item['COMMON_ID']}'>{$item['BRAND_NAME']}</option>";     
          
          
          
      } 
      $selectOptionsString.="</select>";  
      
     return  $selectOptionsString;   
      
      
  }
  
  function RecieveModelsByBrand($Brand_ID,$table_name)
  {
      global $DB;
     $sql="SELECT * FROM {$table_name} WHERE TECDOC_MODEL_ID IS NOT NULL AND BrandCode={$Brand_ID}";
     $result=$DB->Query($sql);
     $allModelsArray=array();
     
     while($modelArray=$result->Fetch())
     {
        $allModelsArray[$modelArray['ID']]['ID']=$modelArray['ID'];
        $allModelsArray[$modelArray['ID']]['TECDOC_MODEL_ID']=$modelArray['TECDOC_MODEL_ID'];
        $allModelsArray[$modelArray['ID']]['MODEL_NAME']=$modelArray['ModelName'];    
        $allModelsArray[$modelArray['ID']]['COMMON_ID']=$modelArray['ID']."#".$modelArray['TECDOC_MODEL_ID'];
         
         
         
     }  
      
    return  $allModelsArray; 
      
  }
  function ShowOptionModelsArray($allModelsArray)
  {
     $selectOptionsString="";
      $selectOptionsString.="<select id='tecdoc_model_id' name='model_id' style='border-radius:5px; height:35px;'>";
      $selectOptionsString.="<option value='0'>выбрать модель</option>";
      foreach($allModelsArray as $id=>$item)
      {
        
         $selectOptionsString.="<option value='{$item['COMMON_ID']}'>{$item['MODEL_NAME']}</option>";     
          
          
          
      } 
      $selectOptionsString.="</select>";  
      
     return  $selectOptionsString;
        
      
      
      
      
  }
  
  function RecieveModelTypesByModelID($modelID,$table_name)
  {
      global $DB;
     $sql="SELECT * FROM {$table_name} WHERE CARMODEL_ID={$modelID}";
     $result=$DB->Query($sql);
     $allModelTypesArray=array();
     
     while($modelTypeArray=$result->Fetch())
     {
        $allModelTypesArray[$modelTypeArray['ID']]['ID']=$modelTypeArray['ID'];
        $allModelTypesArray[$modelTypeArray['ID']]['TECDOC_TYPES_IDS']=$modelTypeArray['TECDOC_TYPE_IDS'];
        $allModelTypesArray[$modelTypeArray['ID']]['TYPE_NAME']=$modelTypeArray['TYPE_NAME'];    
        $allModelTypesArray[$modelTypeArray['ID']]['COMMON_TYPES_IDS']=$modelTypeArray['ID']."///".$modelTypeArray['TECDOC_TYPE_IDS']; 
     }     
         
     # var_dump($allModelTypesArray);
      return $allModelTypesArray;
      
  }
  
  function ShowOptionModelTypesArray($allModelTypesArray)
  {
     $selectOptionsString="";
      $selectOptionsString.="<select name='common_model_type_id' style='border-radius:5px; height:35px;'>";
      $selectOptionsString.="<option value='0'>выбрать т модели</option>";
      foreach($allModelTypesArray as $id=>$item)
      {
        
         $selectOptionsString.="<option value='{$item['COMMON_TYPES_IDS']}'>{$item['TYPE_NAME']}</option>";     
          
          
          
      } 
      $selectOptionsString.="</select>";  
      
     return  $selectOptionsString;
        
      
      
      
      
  }
  
  function MakeConditionForLINK_LA_TYP($typesIDStr)
  {  
     $typesIDArray=explode("#",$typesIDStr);
     
     if (count($typesIDArray)==0 )
     {
         return false;
     }  elseif (count($typesIDArray)>1)
     {
         $conditionStr="("; 
         $count=0;
         foreach ($typesIDArray as $typeid)
         {
             if ($count==0)
             {
                $conditionStr.="LAT_TYP_ID={$typeid}";  
             } else
             {
                   $conditionStr.=" OR LAT_TYP_ID={$typeid}";
             }
             
             
             $count++;
         }
         $conditionStr.=")";   
         return   $conditionStr; 
         
     }  elseif  (count($typesIDArray)==1)
     {            
        $conditionStr="";
        $conditionStr.="LAT_TYP_ID={$typesIDArray[0]}"; 
         return   $conditionStr;   
     }
      
    
  }
  
  
 function  GetBrandCodeByModelID($modelID,$table_name_m_id)
 {
   global $DB;   
   $sql="SELECT BrandCode FROM {$table_name_m_id} WHERE ID={$modelID} LIMIT 1";
  
    var_dump($sql) ; 
   $brandCode=$DB->Query($sql)->Fetch()['BrandCode'];
   
   if ($brandCode!="")
   {
       return  $brandCode  ;
   }
   
    return 0;
     
 }
 function GetTecDocBrandCodeByID($ID,$table_name_b_id)
 {
    global $DB;   
    $sql="SELECT PROPERTY_286 FROM {$table_name_b_id} WHERE IBLOCK_ELEMENT_ID={$ID} LIMIT 1"; 
    var_dump($sql) ;
    $brandCodeTecDoc=$DB->Query($sql)->Fetch()['PROPERTY_286'];
   
   if ($brandCodeTecDoc!="")
   {
       return  intval($brandCodeTecDoc);
   }
   
    return 0; 
     
 }
 
 function LoadDeleteDataFromTecDoc($action,$tecdoc_str_id,$group_id,$group_type_id,$model_id,$model_type_id,$tecdoc_model_type_id,$table_name_m_id,$table_name_m_t_id,$table_name_b_id,$table_name_catalog_items,$table_name_catalog_structure)
 {
     #$dbr=new CDatabase;
    # $dbr->Connect("localhost:31006","TecDoc","bitrix","a251d851");
     
     #$db=new CDatabase;
     #$db->Connect("localhost:31006","bitrix","bitrix","a251d851");
     $db=ConnectLocalDB();
     $dbr=ConnectTecDocDB();  
    # global $DBR;
     #global $DB;
     $tecdoc_brand_code=GetTecDocBrandCodeByID(GetBrandCodeByModelID($model_id,$table_name_m_id),$table_name_b_id);
     $brand_code=GetBrandCodeByModelID($model_id,$table_name_m_id);
        if ($brand_code==0)
        exit("ERROR_ERROR");  
        
        $LAT_TYP_ID_Condition=MakeConditionForLINK_LA_TYP($tecdoc_model_type_id);
        if ($LAT_TYP_ID_Condition===false)
        die("ERROR");
          
        $sqll_pattern="SELECT    LA_ART_ID ,ART_ARTICLE_NR ,SUP_BRAND ,tof_des_text.TEX_TEXTS
                FROM  tof_link_ga_str
                INNER JOIN tof_link_la_typ ON LAT_TYP_ID ={$model_type_id} AND LAT_GA_ID = LGS_GA_ID
                INNER JOIN tof_link_art ON LA_ID = LAT_LA_ID
                INNER JOIN tof_articles ON  LA_ART_ID=ART_ID
                INNER JOIN tof_designation ON tof_designation.DES_ID = tof_articles.ART_COMPLETE_DES_ID  AND tof_designation.DES_LNG_ID=16
                INNER JOIN tof_des_text ON DES_TEXTS.TEX_ID = tof_designation.DES_TEX_ID
                INNER JOIN tof_art_suppliers ON ART_SUP_ID=SUP_ID
                WHERE     LGS_STR_ID ={$tecdoc_str_id}";  
         # var_dump($sqll_pattern);      
          $sql="SELECT    LA_ART_ID ,ART_ARTICLE_NR ,SUP_BRAND ,tof_des_texts.TEX_TEXT AS CAPTION
                FROM  tof_link_ga_str
                INNER JOIN tof_link_la_typ ON {$LAT_TYP_ID_Condition} AND LAT_GA_ID = LGS_GA_ID
                INNER JOIN tof_link_art ON LA_ID = LAT_LA_ID
                INNER JOIN tof_articles ON  LA_ART_ID=ART_ID
                INNER JOIN tof_designation ON tof_designation.DES_ID = tof_articles.ART_COMPLETE_DES_ID  AND tof_designation.DES_LNG_ID=16
                INNER JOIN tof_des_texts ON tof_des_texts.TEX_ID = tof_designation.DES_TEX_ID
                INNER JOIN tof_art_suppliers ON ART_SUP_ID=SUP_ID
                WHERE     LGS_STR_ID ={$tecdoc_str_id}"; 
                         
          $result=$dbr->Query($sql);
          $notOriginalItemsArray=Array();
          while ($ItemArray=$result->Fetch())
          {
             $itemCode=preg_replace("/[^A-Za-z0-9]/i","",$ItemArray['ART_ARTICLE_NR']); 
             $notOriginalItemsArray[$itemCode]=$ItemArray['ART_ARTICLE_NR']; 
              
              
          }
          $OriginalItemsArray=Array();
          foreach ($notOriginalItemsArray as $itemCode=>$article)
          {
              $sql="SELECT S.ARL_SEARCH_NUMBER AS ItemCode , 
                    S.ARL_BRA_ID AS CrossBrandCode FROM `Crosses` as F inner join `Crosses` as S ON
                    F.`ARL_ART_ID`=S.`ARL_ART_ID` AND  F.`ARL_SEARCH_NUMBER`='{$itemCode}'
                    WHERE  S.ARL_KIND=3 AND S.`ARL_SEARCH_NUMBER`<>'{$itemCode}' AND S.ARL_BRA_ID={$tecdoc_brand_code}                    
                    GROUP BY S.ARL_SEARCH_NUMBER, S.ARL_BRA_ID   
                    " ;
               
              $result=$db->Query($sql);
              while ($ItemArray=$result->Fetch())
              {
                 $OriginalItemsArray[$ItemArray['ItemCode']]=$ItemArray['ItemCode']; 
              }
              
              
          }
          
          foreach ($OriginalItemsArray as $item_code)
          {
              if ($action===true)
              {
                  InsertItemToDbCatalogItem($group_id,$group_type_id,$brand_code,$item_code,$model_id,$model_type_id,$table_name_catalog_items);
                 # die("CHECK");
              }else
              {
                  DeleteItemFromDbCatalogItem($group_id,$group_type_id,$brand_code,$item_code,$model_id,$model_type_id,$table_name_catalog_items);
                  
              }
              
              
              
          }
     
          #var_dump($OriginalItemsArray);
          die();
     
     
 }
 
 
 function InsertToDbCatalogStructure($group_id,$group_type_id,$brand_code,$item_code,$model_id,$model_type_id,$table_name_catalog_structure)
 {
     global $DB; 
     $sql="INSERT INTO {$table_name_catalog_structure} (GROUP_ID,GROUP_TYPE_ID,BRAND_ID,MODEl_ID,MODEL_TYPE_ID) 
     VALUES({$group_id},{$group_type_id},{$brand_code},{$model_id},{$model_type_id})
     ON DUPLICATE KEY UPDATE GROUP_ID={$group_id} 
     ";
     $DB->Query($sql);
     
      return intval($DB->AffectedRowsCount);
     
 }
 function DeleteFromDbCatalogStructure($group_id,$group_type_id,$brand_code,$item_code,$model_id,$model_type_id,$table_name_catalog_structure)
 {
     global $DB; 
     $sql=" DELETE FROM {$table_name_catalog_structure} WHERE GROUP_ID={$group_id} AND GROUP_TYPE_ID={$group_type_id} BRAND_ID={$brand_code} 
          AND MODEl_ID={$model_id} AND MODEL_TYPE_ID={$model_type_id}";
     
     
      $DB->Query($sql);
     
      return intval($DB->AffectedRowsCount);  
      
 }
 function InsertItemToDbCatalogItem($group_id,$group_type_id,$brand_code,$item_code,$model_id,$model_type_id,$table_name_catalog_items)
 {
      global $DB;
       
       $sql="INSERT INTO {$table_name_catalog_items} (ID,BRAND_CODE,ITEM_CODE,GROUP_TYPE_ID,MODEl_ID,MODEL_TYPE_ID)
       VALUES('',{$brand_code},'{$item_code}',{$group_type_id},{$model_id},{$model_type_id})
       ON DUPLICATE KEY UPDATE BRAND_CODE={$brand_code}
       ";
      
      $DB->Query($sql);
     
      return $DB->AffectedRowsCount;
     
 }   
  
 function DeleteItemFromDbCatalogItem($group_id,$group_type_id,$brand_code,$item_code,$model_id,$model_type_id,$table_name_catalog_items)
 {
        global $DB;  
        $sql="DELETE FROM {$table_name_catalog_items} WHERE BRAND_CODE={$brand_code} AND ITEM_CODE='{$item_code}' 
        AND GROUP_TYPE_ID={$group_type_id} AND MODEl_ID={$model_id} AND MODEL_TYPE_ID={$model_type_id}";
        
        $DB->Query($sql);
        
        return $DB->AffectedRowsCount;     
     
 } 
 
 function RecieveModelsArrayFromTecDoc($regName,$tecdoc_brand_id)
 {
     $dbr=ConnectTecDocDB();
     $sql="SELECT MOD_ID, TEX_TEXT AS CAPTION, MOD_PCON_START, MOD_PCON_END
            FROM tof_models
            INNER JOIN tof_country_designations ON CDS_ID = MOD_CDS_ID
            INNER JOIN tof_des_texts ON TEX_ID = CDS_TEX_ID
            WHERE MOD_MFA_ID ={$tecdoc_brand_id}  AND  tof_des_texts.TEX_TEXT REGEXP '^.*{$regName}.*\$'
            AND CDS_LNG_ID =16
            ORDER BY CAPTION" ;
           # var_dump($sql);
     $result=$dbr->Query($sql);
     $findedModelsArray=array();
     while ($modelArray=$result->Fetch())
     {
         $findedModelsArray[$modelArray['MOD_ID']]['ID']= $modelArray['MOD_ID'];
         $findedModelsArray[$modelArray['MOD_ID']]['CAPTION']=$modelArray['CAPTION']; 
         $findedModelsArray[$modelArray['MOD_ID']]['DATE_BEGIN']= $modelArray['MOD_PCON_START'];
         $findedModelsArray[$modelArray['MOD_ID']]['DATE_END']= $modelArray['MOD_PCON_END'];
         
         
     }       
     
   return  $findedModelsArray; 
 }
 function  RecieveModelsTypesArrayFromTecDoc($tecdoc_brand_id,$tecdoc_model_id)
 {
     $dbr=ConnectTecDocDB();
     $sql="SELECT    TYP_ID ,tof_des_texts.TEX_TEXT  AS CAPTION, TYP_PCON_START ,TYP_PCON_END
           FROM               tof_types
           INNER JOIN tof_country_designations ON tof_country_designations.CDS_ID=TYP_CDS_ID AND tof_country_designations.CDS_LNG_ID=16
           INNER JOIN tof_des_texts ON tof_des_texts.TEX_ID=tof_country_designations.CDS_TEX_ID 
           WHERE    TYP_MOD_ID ={$tecdoc_model_id}";
     
    $result=$dbr->Query($sql); 
    $findedModelTypesArray=array();
    while ($modelTypeArray=$result->Fetch())
    {
      $findedModelTypesArray[$modelTypeArray['TYP_ID']]['ID']= $modelTypeArray['TYP_ID'];
      $findedModelTypesArray[$modelTypeArray['TYP_ID']]['CAPTION']=$modelTypeArray['CAPTION'];
      $findedModelTypesArray[$modelTypeArray['TYP_ID']]['DATE_BEGIN'] = $modelTypeArray['TYP_PCON_START'] ;
      $findedModelTypesArray[$modelTypeArray['TYP_ID']]['DATE_END'] =$modelTypeArray['TYP_PCON_END']; 
        
    }
     
    return $findedModelTypesArray; 
 }
 function  ShowModelsTypesArrayFromTecDoc($findedModelsTypesArray,$table_name_car_models_types)
 {
     
    $modelsTypesString='';    
     foreach ($findedModelsTypesArray as $tecdoc_model_type_id=>$item)
     {
         if (CheckIfModelTypesIsInDb($item['ID'],$table_name_car_models_types)>0)
         {
             $modelsTypesString.= "<input type='checkbox' checked='checked' class='tecdoc_finded_models_types' id='{$item['ID']}' >{$item['CAPTION']}-{$item['DATE_BEGIN']}-{$item['DATE_END']}</input><br>";
             
         } else
         {
           $modelsTypesString.= "<input type='checkbox'  class='tecdoc_finded_models_types' id='{$item['ID']}' ><a href='#' class='tecdoc_finded_models_types' id='{$item['ID']}'>{$item['CAPTION']}-{$item['DATE_BEGIN']}-{$item['DATE_END']}</a></input><br>";          
             
         }
        
         
     }
     return  $modelsTypesString;
 }
 function ShowModelsArrayFromTecDoc($findedModelsArray,$table_name_car_models)
 {
     $modelsString='';  
     foreach ($findedModelsArray as $tecdoc_model_id=>$item)
     {
        
       if (CheckIfModelIsInDb($item['ID'],$table_name_car_models)>0)
       {
       $modelsString.="<input type='checkbox' checked='checked' class='tecdoc_finded_models' id='{$item['ID']}'>{$item['CAPTION']}-{$item['DATE_BEGIN']}-{$item['DATE_END']}</input><br>";
       $modelsString.="";     
       } else
       {
        $modelsString.="<input type='checkbox' class='tecdoc_finded_models' id='{$item['ID']}'><a href='#' class='tecdoc_finded_models' id='{$item['ID']}'>{$item['CAPTION']}</a>-{$item['DATE_BEGIN']}-{$item['DATE_END']}</input><br>";
       $modelsString.="";   
       } 
         
         
         
     }
     
     
   return  $modelsString; 
 }
 function CheckIfModelIsInDb($tecdoc_model_id,$table_name_car_models)
 {
     $db=ConnectLocalDB(); 
      
      $sql="SELECT TECDOC_MODEL_ID FROM {$table_name_car_models} WHERE TECDOC_MODEL_ID='{$tecdoc_model_id}'";
      
      $result=$db->Query($sql);
      #var_dump($result->SelectedRowsCount());
      return intval($result->SelectedRowsCount()) ;
      
     
     
 }

 function CheckIfModelTypesIsInDb($tecdoc_model_type_id,$table_name_car_models_types)
 {
      $db=ConnectLocalDB(); 
      $sql="SELECT TECDOC_TYPE_IDS FROM {$table_name_car_models_types} WHERE TECDOC_TYPE_IDS={$tecdoc_model_type_id} OR  TECDOC_TYPE_IDS REGEXP '^{$tecdoc_model_type_id}#{1}.*\$' 
            OR TECDOC_TYPE_IDS REGEXP '^.*#{1}{$tecdoc_model_type_id}#{1}.*\$' OR TECDOC_TYPE_IDS REGEXP '^.*#{$tecdoc_model_type_id}\$'";   
     
     $result=$db->Query($sql);
     return intval($result->SelectedRowsCount()); 
     
 }
  function AddModelType($model_id,$tecdoc_model_type_id,$type_name,$table_name_model_types)
  {
     $db=ConnectLocalDB();
     $sql="INSERT INTO {$table_name_model_types} (ID,CARMODEl_ID,TECDOC_TYPE_IDS,TYPE_NAME) 
     VALUES('',{$model_id},{$tecdoc_model_type_id},'{$type_name}')
     "; 
     $result=$db->Query($sql);
     
     return intval($result->AffectedRowsCount()); 
      
      
  }
 ####################################    
  
  if (isset($_POST['add_group']) && $_POST['add_group']!="")
  {
    AddGroup($_POST['add_group'],$catalog_groups);  
    unset($_POST['add_group']);
      
  }
  
  
  if (isset($_POST['groupID']) && $_POST['groupID']!="")
  {
      
    echo ShowOptionsAllGroupTypesArray(RecieveGroupTypeOptionsByGroupID($catalog_groups_types,$_POST['groupID']));  
    exit(); 
      
  }
 
  if (isset($_POST['add_group_type']) && $_POST['add_group_type']!="" 
      && isset($_POST['group_select']) && $_POST['group_select']!="" 
      && isset($_POST['group_type_value']) && $_POST['group_type_value']!="")
       
  {
      
    AddGroupType($_POST['group_select'],$_POST['group_type_value'],$catalog_groups_types);
    unset($_POST['add_group_type']);
    unset($_POST['group_select']);
    unset($_POST['group_type_value']);
    header("Location:/catalog_service.php");    
      
  }
      // add to catalog item
   if (isset($_POST['item_code_add']) && $_POST['item_code_add']!="" 
      && isset($_POST['item_code']) && $_POST['item_code']!=""                                                               //item_code
      && isset($_POST['group_select']) && $_POST['group_select']!="" 
      && isset($_POST['group_type_select']) && $_POST['group_type_select']!=""
      && isset($_POST['brand_select']) &&  $_POST['brand_select']!=""
      && isset($_POST['model_id']) &&  $_POST['model_id']!="" 
      && isset($_POST['common_model_type_id']) &&  $_POST['common_model_type_id']!="" 
      )
      {
         //var_dump($_POST);
        $brand_code=explode("///",$_POST['brand_select'])[0];
        $item_code=preg_replace("/[^A-Za-z0-9]/","",$_POST['item_code']);  
        $model_id=explode("#",$_POST['model_id'])[0];         
        $model_types_id=explode("///",$_POST['common_model_type_id'])[0];
       
        
        InsertItemToDbCatalogItem($_POST['group_select'],$_POST['group_type_select'],$brand_code,$item_code,$model_id,$model_types_id,$catalog_items);    
           
          
      }
       // add to catalog structure
    if (isset($_POST['catalog_structure_add']) && $_POST['catalog_structure_add']!=""                                                                         
      && isset($_POST['group_select']) && $_POST['group_select']!="" 
      && isset($_POST['group_type_select']) && $_POST['group_type_select']!=""
      && isset($_POST['brand_select']) &&  $_POST['brand_select']!=""
      && isset($_POST['model_id']) &&  $_POST['model_id']!="" 
      && isset($_POST['common_model_type_id']) &&  $_POST['common_model_type_id']!="" 
      )
      {
          $brand_code=explode("///",$_POST['brand_select'])[0];
          $item_code=preg_replace("/[^A-Za-z0-9]/","",$_POST['item_code']);  
          $model_id=explode("#",$_POST['model_id'])[0];         
          $model_types_id=explode("///",$_POST['common_model_type_id'])[0];
          InsertToDbCatalogStructure($_POST['group_select'],$_POST['group_type_select'],$brand_code,"",$model_id,$model_types_id,$catalog_items_structure);    
          
      }   
   
   if (isset($_POST['modelID']) && $_POST['modelID']!="")
   {
      $model_id=explode("#",$_POST['modelID'])[0];
       $tecdoc_model_id=explode("#",$_POST['modelID'])[1]; 
      echo  ShowOptionModelTypesArray(RecieveModelTypesByModelID($model_id,$carmodel_types)); 
       
        exit();
   }
  
  
  if (isset($_POST['tecdoc_str_id_add']) && isset($_POST['tecdoc_str_id']) &&  $_POST['tecdoc_str_id']!="" )
  {
     # var_dump($_POST);
      if (isset($_POST['model_id']) && $_POST['model_id']!="" 
      && isset($_POST['common_model_type_id']) && $_POST['common_model_type_id']!=""
      && isset($_POST['group_select']) && $_POST['group_select']!=""
       && isset($_POST['group_type_select']) && $_POST['group_type_select']!=""  
      )
      {
         #LoadDataFromTecDoc($group_id,$group_type_id,$model_id,$model_type_id,$table_name_m_id,$table_name_m_t_id,$table_name_b_id)
         $types_id=explode("///",$_POST['common_model_type_id'])[0];
         $tecdoc_types_id=explode("///",$_POST['common_model_type_id'])[1]; 
          
         LoadDeleteDataFromTecDoc(true,$_POST['tecdoc_str_id'],$_POST['group_select'],$_POST['group_type_select'],$_POST['model_id'],$types_id,$tecdoc_types_id,$carmodel,$carmodel_types,$brands,$catalog_items,$catalog_items_structure); 
              
         
             
      } 
      
      
       
      
  }
  
    if (isset($_POST['tecdoc_str_id_delete']) && isset($_POST['tecdoc_str_id']) &&  $_POST['tecdoc_str_id']!="" )
    {
        if (isset($_POST['model_id']) && $_POST['model_id']!="" 
      && isset($_POST['common_model_type_id']) && $_POST['common_model_type_id']!=""
      && isset($_POST['group_select']) && $_POST['group_select']!=""
       && isset($_POST['group_type_select']) && $_POST['group_type_select']!=""  
      )
      {
         #LoadDataFromTecDoc($group_id,$group_type_id,$model_id,$model_type_id,$table_name_m_id,$table_name_m_t_id,$table_name_b_id)
         $types_id=explode("///",$_POST['common_model_type_id'])[0];
         $tecdoc_types_id=explode("///",$_POST['common_model_type_id'])[1]; 
          
         LoadDeleteDataFromTecDoc(false,$_POST['tecdoc_str_id'],$_POST['group_select'],$_POST['group_type_select'],$_POST['model_id'],$types_id,$tecdoc_types_id,$carmodel,$carmodel_types,$brands,$catalog_items,$catalog_items_structure); 
              
         
             
      } 
        
        
        
    } 
  
  
   if (isset($_POST['model_name_search']) && isset($_POST['model_brand_search']))
   {
       $brand_code=explode("///",$_POST['model_brand_search'])[0];
       $tecdoc_brand_code=explode("///",$_POST['model_brand_search'])[1];
        
      echo ShowModelsArrayFromTecDoc((RecieveModelsArrayFromTecDoc($_POST['model_name_search'],$tecdoc_brand_code)),$carmodel);
       
       
       
       
       exit();
   }
   if (isset($_POST['common_brand_id'])  && $_POST['common_brand_id']!=""
   && isset($_POST['find_exit_model_types'])
   )
   {
       $brand_code=explode("///",$_POST['common_brand_id'])[0];
       $tecdoc_brand_code=explode("///",$_POST['common_brand_id'])[1];
     echo  ShowOptionModelsArray(RecieveModelsByBrand($brand_code,$carmodel));
        exit();
   }
   if (isset($_POST['search_modelID']) && isset($_POST['search_brand_id']) && isset($_POST['find_tecdoc_model_types']))
   {
        
        $brand_code=explode("///",$_POST['search_brand_id'])[0];
        $tecdoc_brand_code=explode("///",$_POST['search_brand_id'])[1];
        $model_id=explode("#",$_POST['search_modelID'])[0];
        $tecdoc_model_id=explode("#",$_POST['search_modelID'])[1]; 
        
     echo ShowModelsTypesArrayFromTecDoc(RecieveModelsTypesArrayFromTecDoc($tecdoc_brand_code,$tecdoc_model_id),$carmodel_types); 
       
      exit(); 
   }
   
   if (isset($_POST['common_brand_id']) && $_POST['common_brand_id']!=""
    && isset($_POST['model_id']) && $_POST['model_id']!=""
    && isset($_POST['tecdoc_model_type_id']) && $_POST['tecdoc_model_type_id']!=""
    && isset ($_POST['add_model_type_id'])
    && isset ($_POST['model_type_name'] ) && $_POST['model_type_name']!=""
    )
    {
      
       $countRowAdd=0; 
       if (CheckIfModelTypesIsInDb($_POST['tecdoc_model_type_id'],$carmodel_types)==0)
       {
         $model_id=explode("#",$_POST['model_id'])[0];
         $brand_code=explode("///",$_POST['common_brand_id'])[0];
         
        $countRowAdd=AddModelType($model_id,$_POST['tecdoc_model_type_id'],$_POST['model_type_name'],$carmodel_types);  
        echo "Добавленна ".$countRowAdd." модель - {$_POST['model_type_name']}";   
        exit();      
       } 
      echo "Ошибка.";
      exit();
    }
   
   
   #########################################################  
   ?>
   <html>
     <head>

         <script type="text/javascript" src="/bitrix/js/itgScript/jquery.min.js"></script>
                 <script src="/js/catalog_service.js" ></script> 
     </head>
    <body>
    <div id='catalog_group_show' style="width:100%; height:250px; border-bottom:solid 1px black;">
           <form action="/catalog_service.php" method="POST" enctype="multipart/form-data"> 
             <div style="width:20%;height:50%;float:left;margin-left:5px;">  
             <?
              echo ShowAllGroupArray(RecieveAllGroupsArray($catalog_groups) ) 
             ?>   
             </div>
             <div id='group_type_div' style="width:10%;height:50%;float:left; margin-left:5px;">
                <select style='border-radius:5px; height:35px;'>
                     <option value='0'>выбрать группу</option>  
                </select>
                
             </div>  
              <div id='group_type_div' style="width:30%;height:50%;float:left; margin-left:5px;"> 
                    <input type='text' name='group_type_value' value='' style='border-radius:5px; height:35px;' ></input>
                    <input  type='submit' name='add_group_type' value='Добавить Тип'  style='border-radius:5px; height:35px;'> </input>
               </div> 
               <div id='group_type_div' style="width:30%;height:50%;float:left; margin-left:5px;">
                  <input type="text" name='tecdoc_str_id' value='' style='border-radius:5px; height:35px;'> </input>
                  <input  type='submit' name='tecdoc_str_id_add' value='Загрузить Данные Текдока'  style='border-radius:5px; height:35px;'> </input>
                  <p>Только Цифры - Номер категории по ТекДоку</p>
                  
                  <div name='tecdoc_model_id_div'>
                          <?
                          echo  ShowOptionModelsArray(RecieveModelsByBrand(33548590,$carmodel) )
                          
                          ?>
                  </div>
                  <div id='tecdoc_model_types_id_div' style='margin-top: 1%;'>
                       <select id='tecdoc_model_type_id' style='border-radius:5px; height:35px; margin-top: 1%;'>
                           <option value='0'>выбрать тип модели</option>
                       </select> 
                  </div>
                   <input  type='submit' name='catalog_structure_add' value='Добавить в структуру каталога'  style='border-radius:5px; height:35px; margin-top: 1%;'> </input>  
                  <input  type='submit' name='tecdoc_str_id_delete' value='Удалить Данные Текдока'  style='border-radius:5px; height:35px; margin-top: 1%;'> </input> 
                </div>
                
                <div id='group_type_div' style="width:40%;height:50%;float:left; margin-left:5px; border-radius:5px;">
                  
                  <?
                    echo ShowBrandsArray(RecieveBrandsArray($brands))  
                  ?> 
                  <input type="text" name='item_code' value='' style='border-radius:5px; height:35px;'> </input>   
                  <input  type='submit'  name='item_code_add' value='добавить артикул'  style='border-radius:5px; height:35px;'> </input>
                  <p>Добавить Артикул</p>
                </div>      
             </form>
             
             
     </div>     
  
   <?
  
   ?>
    <div id='catalog_group_add' style="margin-top:20px;width:100%; height:auto; float:left; position:relative; border-bottom: solid 1px black;">
      <form action="/catalog_service.php" method="POST" enctype="multipart/form-data"> 
        <input type='text' name='add_group' value='' style='border-radius:5px; height:35px;' ></input>
        <input  type='submit' value='Добавить Группу'  style='border-radius:5px; height:35px;'> </input>
      </form>
    </div> 
     <div id='catalog_model_types_add' style="margin-top:20px;width:100%; height:auto; float:left; position:relative; border-bottom: solid 1px black;">  
       <form action="/catalog_service.php" method="POST" enctype="multipart/form-data">   
          <div id="add_model_div" style="float:left; margin-right:1%;">
             <?
                        echo ShowBrandsArray(RecieveBrandsArray($brands))  
             ?> 
          </div>
         <input type='text' id='model_id_add' disabled="disabled"  name='search_model_name' value='' style='width:5%;border-radius:5px; height:35px;float:left; margin-right: 1%;' ></input>  
         <input type='text' id='model_name_add' name='search_model_name' value='' style='border-radius:5px; width:40%; height:35px;float:left;' ></input>
         <input  type='submit' id="add_model" name='add_model' value='Добавить Модель'  style='border-radius:5px; height:35px; margin-bottom: 1%; float:left;'> </input> 
      </form> 
     </div>
     <div id='model_types' style="margin-top:20px;width:100%; height:auto; float:left; position:relative; border-bottom: solid 1px black;"> 
         <div id="search_model_name_div" style="float:left; margin-right:1%;">
             <?
                        echo ShowBrandsArray(RecieveBrandsArray($brands))  
             ?> 
         </div>
         <input type='text' id='search_model_name' name='search_model_name' value='' style='border-radius:5px; height:35px;float:left;' ></input>
         <input  type='submit' id="search_model" name='search_model' value='Искать модель в базе ТекДок'  style='border-radius:5px; height:35px; margin-bottom: 1%; float:left;'> </input>
         <div id='model_search_result' style="float:left;border-bottom: solid 1px black; width:40%; margin-left: 1%;">
         </div>
       
     </div>
      <div id='model_types' style="margin-top:20px;width:100%; height:auto; float:left; position:relative; border-bottom: solid 1px black;">
          <div id="search_model_type_name_div" style="float:left; margin-right:1%;">
             <?
                        echo ShowBrandsArray(RecieveBrandsArray($brands))  
             ?> 
         </div>
          <div id="type_select_div" style="float:left; margin-right:1%;">
           
              
            
          </div>
          <div id="search_model_type_name_div" style="float:left; margin-right:1%;">
            <div id='model_type_add_p_info'> </div>
            <input type='text' id='tecdoc_model_type_id_add' disabled="disabled"  name='model_type_id_add' value='' style='float:left;width:15%;border-radius:5px; height:35px;float:left; margin-right: 1%;' ></input>
            <input type='text' id='tecdoc_model_type_name_add' name='search_model_name' value='' style='float:left;border-radius:5px; height:35px;float:left;' ></input>
            <input  type='submit' id="model_type_add" name='search_type_model' value='Добавить Тип Модели'  style='float:left;border-radius:5px; height:35px; margin-bottom: 1%; float:left;'> </input>  
            
          </div>
           <div id='model_type_search_result' style="float:left;border-bottom: solid 1px black; width:30%; margin-left: 1%;">  
              
            </div>
      </div>
   <?
  
  
  
  #$sql="SELECT * FROM tof_generic_articles LIMIT 1";
  
  #$result=$DBR->Query($sql)->Fetch();
  
  #var_dump($result); 
  
  
    
    
    
    
    
?> 
   </body>
   </html>
    