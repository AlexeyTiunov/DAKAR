<?
  require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');   
 $GLOBALS['MASSAGE']=Array();  
 # global $DBR;
  global $DB;
  $catalog_groups=" b_autodoc_items_catalog_groups";
  $catalog_groups_types="b_autodoc_items_catalog_groups_types";
  $brands="b_iblock_element_prop_s37";
  $GLOBALS['BRAND_TABLE_NAME']=$brands;
  $carmodel="b_autodoc_carmodels";
  $catalog_items="b_autodoc_items_catalog_items";
  $catalog_items_structure = "b_autodoc_items_catalog_structure";
  $carmodel_types="b_autodoc_carmodels_types";
  $GLOBALS['BRAND_ID_LOCAL_COLUMN_NAME']="IBLOCK_ELEMENT_ID";
  $GLOBALS['TECDOC_BRAND_ID_LOCAL_COLUMN_NAME']="PROPERTY_286";
  
  $GLOBALS['BRAND_NAME_LOCAL_COLUMN_NAME']="PROPERTY_288"; 
  $GLOBALS['TECDOC_BRAND_NAME_LOCAL_COLUMN_NAME']=""; 
  
  $GLOBALS['ACTIVE_BRAND_COLUMN_NAME']="DESCRIPTION_287";
  
  
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
      $sql="SELECT {$GLOBALS['BRAND_ID_LOCAL_COLUMN_NAME']}, {$GLOBALS['TECDOC_BRAND_ID_LOCAL_COLUMN_NAME']}, {$GLOBALS['BRAND_NAME_LOCAL_COLUMN_NAME']} 
      FROM {$table_name} WHERE {$GLOBALS['ACTIVE_BRAND_COLUMN_NAME']}='1' ORDER  BY {$GLOBALS['BRAND_NAME_LOCAL_COLUMN_NAME']} ASC ";      
      $result=$DB->Query($sql);
      $allBrands=array();
      while  ($brandArray=$result->Fetch())
      {
         $allBrands[$brandArray['IBLOCK_ELEMENT_ID']]['ID']=$brandArray[$GLOBALS['TECDOC_BRAND_ID_LOCAL_COLUMN_NAME']];
         $allBrands[$brandArray['IBLOCK_ELEMENT_ID']]['BRAND_NAME']=$brandArray[$GLOBALS['BRAND_NAME_LOCAL_COLUMN_NAME']]; 
         $allBrands[$brandArray['IBLOCK_ELEMENT_ID']]['TECDOC_ID']=intval($brandArray[$GLOBALS['TECDOC_BRAND_ID_LOCAL_COLUMN_NAME']]); 
         $allBrands[$brandArray['IBLOCK_ELEMENT_ID']]['COMMON_ID']=$brandArray[$GLOBALS['BRAND_ID_LOCAL_COLUMN_NAME']]."///".intval($brandArray[$GLOBALS['TECDOC_BRAND_ID_LOCAL_COLUMN_NAME']]);
                                                                
          
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
       $db=ConnectLocalDB();
    # $sql="SELECT * FROM {$table_name} WHERE TECDOC_MODEL_ID IS NOT NULL AND BrandCode={$Brand_ID}";
    $sql="SELECT * FROM {$table_name} WHERE  BrandCode={$Brand_ID}";  
     $result=$db->Query($sql);
     $allModelsArray=array();
     
     while($modelArray=$result->Fetch())
     {
        $allModelsArray[$modelArray['ID']]['ID']=$modelArray['ID'];
        $allModelsArray[$modelArray['ID']]['TECDOC_MODEL_ID']=$modelArray['TECDOC_MODEL_ID'];
        $allModelsArray[$modelArray['ID']]['MODEL_NAME']=$modelArray['ModelName'];    
        $allModelsArray[$modelArray['ID']]['COMMON_ID']=$modelArray['ID']."///".$modelArray['TECDOC_MODEL_ID'];
         
         
         
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
      $selectOptionsString.="<select id='common_model_type_id' name='common_model_type_id' style='border-radius:5px; height:35px;'>";
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
    $sql="SELECT {$GLOBALS['TECDOC_BRAND_ID_LOCAL_COLUMN_NAME']} FROM {$table_name_b_id} WHERE {$GLOBALS['BRAND_ID_LOCAL_COLUMN_NAME']}={$ID} LIMIT 1"; 
    var_dump($sql) ;
    $brandCodeTecDoc=$DB->Query($sql)->Fetch()[$GLOBALS['TECDOC_BRAND_ID_LOCAL_COLUMN_NAME']];
   
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
        if ($brand_code==0 || $brand_code=="0" || $tecdoc_brand_code=="0" || $tecdoc_brand_code==0)
        exit("ERROR_ERROR_BRANDCODE");  
        
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
                
          $sql="SELECT    LA_ART_ID ,ART_ARTICLE_NR ,SUP_BRAND ,tof_des_texts.TEX_TEXT AS CAPTION
                FROM  tof_link_ga_str
                INNER JOIN tof_link_la_typ ON {$LAT_TYP_ID_Condition} AND LAT_GA_ID = LGS_GA_ID
                INNER JOIN tof_link_art ON LA_ID = LAT_LA_ID
                INNER JOIN tof_articles ON  LA_ART_ID=ART_ID
                INNER JOIN tof_designation ON tof_designation.DES_ID = tof_articles.ART_COMPLETE_DES_ID  AND tof_designation.DES_LNG_ID=16
                INNER JOIN tof_des_texts ON tof_des_texts.TEX_ID = tof_designation.DES_TEX_ID
                INNER JOIN tof_art_suppliers ON ART_SUP_ID=SUP_ID
                WHERE     LGS_STR_ID ={$tecdoc_str_id}"; 
         # var_dump($sql);               
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
                #var_dump($sql);
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
                  InsertAnalogsToDBByOriginalItemCodeFromTecDoc($group_id,$group_type_id,$tecdoc_brand_code,$item_code,$model_id,$model_type_id,$table_name_catalog_items);
                 # die("CHECK");
              }else
              {
                  DeleteItemFromDbCatalogItem($group_id,$group_type_id,$brand_code,$item_code,$model_id,$model_type_id,$table_name_catalog_items);
                  
              }
              
              
              
          }
         
              
     
         # var_dump($notOriginalItemsArray);
          #die();
     
     
 }
 function BrandSearch($BrandCode)
 {
    
     $db=ConnectLocalDB();
     if (strlen(trim($BrandCode))>2)
     {
         $sql="SELECT {$GLOBALS['BRAND_ID_LOCAL_COLUMN_NAME']} AS ID,{$GLOBALS['BRAND_NAME_LOCAL_COLUMN_NAME']} AS FULLNAME FROM {$GLOBALS['BRAND_TABLE_NAME']} WHERE {$GLOBALS['BRAND_NAME_LOCAL_COLUMN_NAME']}='".trim($BrandCode)."'";
           $result =$db->Query ($sql) ;
           $res=$result->Fetch();        
            if ($res['ID']>0)
            {
                return $res['ID'];
            }else
            {
                return false;
            }   
         
     }
    
     else
     {
         return false;
     }
     
 }
 
 function GetBrandNameFromTecDoc($tecdoc_brand_id)
 {
     $dbr=ConnectTecDocDB();
     $sql="SELECT BRA_MFC_CODE FROM tof_brands WHERE BRA_ID={$tecdoc_brand_id} LIMIT 1";
     $result=$dbr->Query($sql);
     
     return $result->Fetch()['BRA_MFC_CODE'];
     
 }
  function InsertAnalogsToDBByOriginalItemCodeFromTecDoc($group_id,$group_type_id,$tecDocBrand_id,$originalItemCode,$model_id,$model_type_id,$table_name_catalog_items) 
  #($originalItemCode,$tecDocBrand_id)
  {
     // $dbr=ConnectTecDocDB();
     $db=ConnectLocalDB();
      
      $sql="SELECT S.ARL_SEARCH_NUMBER AS CrossItemCode , 
            S.ARL_BRA_ID AS CrossBrandCode FROM `Crosses` as F 
            inner join `Crosses` as S ON F.`ARL_ART_ID`=S.`ARL_ART_ID` AND  F.`ARL_SEARCH_NUMBER`='{$originalItemCode}'  AND F.ARL_BRA_ID={$tecDocBrand_id} 
            WHERE  S.ARL_KIND<>3 AND S.`ARL_SEARCH_NUMBER`<>'{$originalItemCode}'                     
            GROUP BY S.ARL_SEARCH_NUMBER, S.ARL_BRA_ID";
      # var_dump($sql);     
       $result=$db->Query($sql);
       $notOriginalItemsArray=Array();
      while ($ItemArray=$result->Fetch())
      {
         $notOriginalItemsArray[$ItemArray['CrossBrandCode']][$ItemArray['CrossItemCode']]['ITEM_CODE']=$ItemArray['CrossItemCode'];
         $notOriginalItemsArray[$ItemArray['CrossBrandCode']][$ItemArray['CrossItemCode']]['BRAND_CODE']=$ItemArray['CrossBrandCode']; 
      }      
     
     foreach ($notOriginalItemsArray as $brandCode=>$values)
     {
         foreach ($values as $item_code=>$value)
         {
            $tecdoc_brand_name= GetBrandNameFromTecDoc($value['BRAND_CODE']);
           $brand_code=BrandSearch($tecdoc_brand_name);
          # var_dump($value['BRAND_CODE']);
           if ($brand_code!=false )
           { 
           # var_dump($sql);            
             InsertItemToDbCatalogItem($group_id,$group_type_id,$brand_code,$value['ITEM_CODE'],$model_id,$model_type_id,$table_name_catalog_items);  
           } else
           {
               $GLOBALS['MASSAGE'][]=$tecdoc_brand_name."///".$value['ITEM_CODE'];
           }
             
         }
         
     }      
      
      
  }
 
  
 function InsertToDbCatalogStructure($group_id,$group_type_id,$brand_code,$item_code,$model_id,$model_type_id,$table_name_catalog_structure)
 {
     global $DB; 
     $sql="INSERT INTO {$table_name_catalog_structure} (GROUP_ID,GROUP_TYPE_ID,BRAND_ID,MODEl_ID,MODEL_TYPE_ID) 
     VALUES({$group_id},{$group_type_id},{$brand_code},{$model_id},{$model_type_id})
     ON DUPLICATE KEY UPDATE GROUP_ID={$group_id} 
     ";
     echo $sql;
     $result=$DB->Query($sql);
     
      return intval($result->AffectedRowsCount());
     
 }
 function DeleteFromDbCatalogStructure($group_id,$group_type_id,$brand_code,$item_code,$model_id,$model_type_id,$table_name_catalog_structure)
 {
     global $DB; 
     $sql=" DELETE FROM {$table_name_catalog_structure} WHERE GROUP_ID={$group_id} AND GROUP_TYPE_ID={$group_type_id} AND  BRAND_ID={$brand_code} 
          AND MODEl_ID={$model_id} AND MODEL_TYPE_ID={$model_type_id}";
     
     
        $result=$DB->Query($sql);
     
      return intval($result->AffectedRowsCount());
      
 }
 function InsertItemToDbCatalogItem($group_id,$group_type_id,$brand_code,$item_code,$model_id,$model_type_id,$table_name_catalog_items)
 {
      global $DB;
       
       $sql="INSERT INTO {$table_name_catalog_items} (ID,BRAND_CODE,ITEM_CODE,GROUP_TYPE_ID,MODEl_ID,MODEL_TYPE_ID)
       VALUES('',{$brand_code},'{$item_code}',{$group_type_id},{$model_id},{$model_type_id})
       ON DUPLICATE KEY UPDATE BRAND_CODE={$brand_code}
       ";
       #var_dump($sql);
        $result=$DB->Query($sql);
     
      return intval($result->AffectedRowsCount());
     
 }   
  
 function DeleteItemFromDbCatalogItem($group_id,$group_type_id,$brand_code,$item_code,$model_id,$model_type_id,$table_name_catalog_items)
 {
        global $DB;  
        $sql="DELETE FROM {$table_name_catalog_items} WHERE BRAND_CODE={$brand_code} AND ITEM_CODE='{$item_code}' 
        AND GROUP_TYPE_ID={$group_type_id} AND MODEl_ID={$model_id} AND MODEL_TYPE_ID={$model_type_id}";
        
        $result=$DB->Query($sql);
     
      return intval($result->AffectedRowsCount());
     
 } 
 
 function RecieveModelsArrayFromTecDoc($regName,$tecdoc_brand_id)
 {
     $dbr=ConnectTecDocDB();
     $dbr->Query("SET NAMES utf8");
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
     $dbr->Query("SET NAMES utf8");
   $ConditionForTYP_MOD_ID =MakeConditionForTYP_MOD_ID($tecdoc_model_id); 
     $sql="SELECT    TYP_ID,tof_des_texts2.TEX_TEXT as BODY_TYPE ,tof_des_texts.TEX_TEXT  AS CAPTION, TYP_PCON_START ,TYP_PCON_END,TYP_HP_FROM
           FROM               tof_types
           INNER JOIN tof_country_designations ON tof_country_designations.CDS_ID=TYP_MMT_CDS_ID AND tof_country_designations.CDS_LNG_ID=16
           INNER JOIN tof_des_texts ON tof_des_texts.TEX_ID=tof_country_designations.CDS_TEX_ID
           
           LEFT JOIN tof_designation as tof_designation2  ON tof_designation2.DES_ID=TYP_KV_BODY_DES_ID AND tof_designation2.DES_LNG_ID=16
           LEFT JOIN tof_des_texts as tof_des_texts2 ON tof_des_texts2.TEX_ID=tof_designation2.DES_TEX_ID    
            
           WHERE {$ConditionForTYP_MOD_ID}";
     
    $result=$dbr->Query($sql); 
    $findedModelTypesArray=array();
    while ($modelTypeArray=$result->Fetch())
    {
      $findedModelTypesArray[$modelTypeArray['TYP_ID']]['ID']= $modelTypeArray['TYP_ID'];
      $findedModelTypesArray[$modelTypeArray['TYP_ID']]['CAPTION']=$modelTypeArray['CAPTION'];
      $findedModelTypesArray[$modelTypeArray['TYP_ID']]['DATE_BEGIN'] = $modelTypeArray['TYP_PCON_START'] ;
      $findedModelTypesArray[$modelTypeArray['TYP_ID']]['DATE_END'] =$modelTypeArray['TYP_PCON_END'];
      $findedModelTypesArray[$modelTypeArray['TYP_ID']]['BODY_TYPE']=$modelTypeArray['BODY_TYPE'];
      $findedModelTypesArray[$modelTypeArray['TYP_ID']]['ENGINE_POWER']=$modelTypeArray['TYP_HP_FROM']; 
        
    }
     
    return $findedModelTypesArray; 
 }
 function MakeConditionForTYP_MOD_ID($tecdoc_model_id)
 {
    $tecdoc_model_id_array=explode("#",$tecdoc_model_id);
    
    if (count($tecdoc_model_id_array)==0)
    {
       return ""; 
    }
     elseif (count($tecdoc_model_id_array)==1)
    {
        return "TYP_MOD_ID={$tecdoc_model_id_array[0]}" ;
    }  else
    {
       $count=0; 
       $TYP_MOD_IDConditionStr="";
       foreach ($tecdoc_model_id_array as $tecdoc_model_id)
       {
            if ($count==0)
            {
               $TYP_MOD_IDConditionStr.=" TYP_MOD_ID={$tecdoc_model_id}";
            }  else
            {
              $TYP_MOD_IDConditionStr.= " OR TYP_MOD_ID={$tecdoc_model_id} ";  
            }
           
            $count++; 
       } 
       return  $TYP_MOD_IDConditionStr;
    }
     
 }
 function  ShowModelsTypesArrayFromTecDoc($findedModelsTypesArray,$table_name_car_models_types)
 {
     
    $modelsTypesString='';    
     foreach ($findedModelsTypesArray as $tecdoc_model_type_id=>$item)
     {
         if (CheckIfModelTypesIsInDb($item['ID'],$table_name_car_models_types)>0)
         {
             $modelsTypesString.= "<input type='checkbox'  checked='checked' class='tecdoc_finded_models_types' id='{$item['ID']}' ><a title=''>{$item['CAPTION']}- ({$item['ENGINE_POWER']} л.с.)-{$item['DATE_BEGIN']}-{$item['DATE_END']}-{$item['BODY_TYPE']} </a> </input><br>";
             
         } else
         {
           $modelsTypesString.= "<input type='checkbox'  class='tecdoc_finded_models_types' id='{$item['ID']}' ><a href='#' class='tecdoc_finded_models_types' id='{$item['ID']}'>{$item['CAPTION']}-({$item['ENGINE_POWER']} л.с.)-{$item['DATE_BEGIN']}-{$item['DATE_END']}-{$item['BODY_TYPE']} </a></input><br>";          
             
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
      
      $sql="SELECT TECDOC_MODEL_ID FROM {$table_name_car_models} WHERE TECDOC_MODEL_ID='{$tecdoc_model_id}'
       OR  TECDOC_MODEL_ID REGEXP '^{$tecdoc_model_id}#{1}.*\$' 
            OR TECDOC_MODEL_ID REGEXP '^.*#{1}{$tecdoc_model_id}#{1}.*\$' OR TECDOC_MODEL_ID REGEXP '^.*#{$tecdoc_model_id}\$'";
      
      
      
      $result=$db->Query($sql);
      #var_dump($result->SelectedRowsCount());
      return intval($result->SelectedRowsCount()) ;
      
     
     
 }

 function CheckIfModelTypesIsInDb($tecdoc_model_type_id,$table_name_car_models_types)
 {
      $db=ConnectLocalDB(); 
      $sql="SELECT TECDOC_TYPE_IDS FROM {$table_name_car_models_types} WHERE TECDOC_TYPE_IDS='{$tecdoc_model_type_id}' OR  TECDOC_TYPE_IDS REGEXP '^{$tecdoc_model_type_id}#{1}.*\$' 
            OR TECDOC_TYPE_IDS REGEXP '^.*#{1}{$tecdoc_model_type_id}#{1}.*\$' OR TECDOC_TYPE_IDS REGEXP '^.*#{$tecdoc_model_type_id}\$'";   
     
     $result=$db->Query($sql);
     return intval($result->SelectedRowsCount()); 
     
 }  
  function AddModelType($model_id,$tecdoc_model_type_id,$type_name,$table_name_model_types)
  {
     $db=ConnectLocalDB();
     $sql="INSERT INTO {$table_name_model_types} (ID,CARMODEl_ID,TECDOC_TYPE_IDS,TYPE_NAME) 
     VALUES('',{$model_id},'{$tecdoc_model_type_id}','{$type_name}')
     "; 
    # var_dump($sql);
     $result=$db->Query($sql);
     
     return intval($result->AffectedRowsCount()); 
      
      
  }
  function AddModel($brand_id,$tecdoc_model_id,$model_name,$table_name_models)
  {
     $db=ConnectLocalDB();
     $sql="INSERT INTO {$table_name_models} (ID,TECDOC_MODEL_ID,BrandCode,ModelName) VALUES ('','{$tecdoc_model_id}',{$brand_id},'{$model_name}')";
     
     $result=$db->Query($sql);
     
     return intval($result->AffectedRowsCount()); 
     
      
      
      
  }
  
  function TieModelTypeFromTecDoc($model_type_id,$tecdoc_model_type_id,$type_name,$table_name_model_types)
  {
     $db=ConnectLocalDB(); 
     
     $sql="SELECT TECDOC_TYPE_IDS  FROM {$table_name_model_types} WHERE ID={$model_type_id} LIMIT 1";
     
     $common_tecdoc_model_id=$db->Query($sql)->Fetch()['TECDOC_TYPE_IDS'];
     
     if ($common_tecdoc_model_id=="") 
     {
        $common_tecdoc_model_id=$tecdoc_model_type_id; 
     } else
     {
         
       $common_tecdoc_model_id.="#".$tecdoc_model_type_id;  
     } 
     
     $sql="UPDATE {$table_name_model_types} SET TECDOC_TYPE_IDS='{$common_tecdoc_model_id}' WHERE  ID={$model_type_id}";
     
     $result=$db->Query($sql);
     
     return intval($result->AffectedRowsCount());
       
       
   }
   function TieModelFromTecDoc($model_id,$tecdoc_model_id,$type_name,$table_name_models)
   {
       $db=ConnectLocalDB();
       $sql="SELECT TECDOC_MODEl_ID FROM {$table_name_models} WHERE ID={$model_id}";
       
       $common_tecdoc_model_id=$db->Query($sql)->Fetch()['TECDOC_MODEl_ID'];
       
        if ($common_tecdoc_model_id=="") 
         {
            $common_tecdoc_model_id=$tecdoc_model_id; 
         } else
         {
             
           $common_tecdoc_model_id.="#".$tecdoc_model_id;  
         } 
       
       
       
       $sql="UPDATE {$table_name_models} SET TECDOC_MODEl_ID='{$common_tecdoc_model_id}' WHERE ID={$model_id}";
      
       $result=$db->Query($sql);
     
      return intval($result->AffectedRowsCount()); 
       
       
   }
   function GetGroupTypeIdByName($groupTypeName,$table_name_group_type_id)   
   {
       $db=ConnectLocalDB();
       $sql="SELECT ID  FROM {$table_name_group_type_id} WHERE TYPE_NAME='{$groupTypeName}'";
       #var_dump($sql) ;  
       $result=$db->Query($sql);
       $ID=$result->Fetch()['ID'];
       
       if ($ID=="" || $ID==null)
       {
           return false;
       }else
       {
           return  $ID;
       }
       
       
       
       
   }
   function GetModelIdByName($modelName,$table_name_model_id)
   {
       $db=ConnectLocalDB(); 
       $sql="SELECT ID FROM {$table_name_model_id} WHERE ModelName='{$modelName}'";
       #var_dump($sql) ;
       $result=$db->Query($sql);
       $ID=$result->Fetch()['ID'];
       if ($ID=="" || $ID==null)
       {
           return false;
       }else
       {
           return  $ID;
       } 
       
   }
   function  GetModelTypeIdByName($modelTypeName,$table_name_model_type_id)
   {
        $db=ConnectLocalDB(); 
       $sql="SELECT ID FROM {$table_name_model_type_id} WHERE TYPE_NAME='{$modelTypeName}'";
      # echo $sql ;  
       $result=$db->Query($sql);
       $ID=$result->Fetch()['ID'];
       if ($ID=="" || $ID==null)
       {
           return false;
       }else
       {
           return  $ID;
       } 
       
   }
    function  GetModelTypeIdByNameAndModelID($modelID,$modelTypeName,$table_name_model_type_id)
   {
        $db=ConnectLocalDB(); 
       $sql="SELECT ID FROM {$table_name_model_type_id} WHERE CARMODEL_ID={$modelID} AND TYPE_NAME='{$modelTypeName}'";
      # echo $sql ;  
       $result=$db->Query($sql);
       $ID=$result->Fetch()['ID'];
       if ($ID=="" || $ID==null)
       {
           return false;
       }else
       {
           return  $ID;
       } 
       
   }
   function MakeCSVFromExell($oldFile,$filename)
   {

   # if ($oldFile=="/var/www/priceld/ServiceKoreaMotorsKIA.xls") # ServiceKoreaMotorsKIA.
    
   # ini_set('memory_limit', '2048M');
    require_once $_SERVER["DOCUMENT_ROOT"]."/bitrix/components/itg/excel/Classes/PHPExcel/IOFactory.php";
    $objPHPExcel = PHPExcel_IOFactory::load($oldFile);  
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
    $newFile = $oldFile . '.csv';
    $objWriter->save($newFile);
    return $newFile;
   }
   function CSVFileDeal($filePathName,$table_name_catalog_items,$table_name_group_types,$table_name_models,$table_name_model_types)
   { 
      include $_SERVER['DOCUMENT_ROOT']."/catalog/catalog_structure_corrections.php";                 
      $count=0; 
      $ErrorCauseArray=Array();                  
      $handle = fopen($filePathName, "r"); 
     # var_dump($handle);                 
      while (($data=fgetcsv($handle))!== FALSE)
      {
         $num = count($data); 
          #var_dump($data);
          for ($c=0; $c<$num; $c++) 
         {  
           
           if ($c==0) 
           {     
               $BrandName=$data[$c];
              $brand_code=BrandSearch($data[$c]); 
              //var_dump($brand_Correction) ;              
               if ($brand_code==0) 
               {
                  $brand_code=BrandSearch($brand_Correction[$data[$c]]);  
                                  
               } 
               
              #var_dump($brand_code); 
              if ($brand_code==0)
              {
                 $data['ErrorCause']="BrandName"; 
                 break(1); 
              } else
              {
                  
              }
           }
           if ($c==1)
           {
               #var_dump($data[$c]);
               $item_code=preg_replace("/[^A-Za-z0-9]/i","",$data[$c]);
               if ($item_code=="")
               {
                   $data['ErrorCause']="ItemCode"; 
                    break(1); 
                   
               }  else
               {
                   
               }
               
               
               
           } 
           if ($c==2)
           {
              if (intval($data[$c])>0)
              {
                $group_type_id=intval($data[$c]);
              }elseif(strlen($data[$c])>1)
              {
                  
                $group_type_id=GetGroupTypeIdByName(trim($data[$c]),$table_name_group_types); 
                if  ($group_type_id===false)
                {
                   
                   $group_type_id=GetGroupTypeIdByName($model_type_name_Correction[trim($data[$c])],$table_name_group_types);
                }  
                  
              }else
              {
                  $group_type_id=false;
                    
              } 
               if  ($group_type_id===false)
               {
                  $data['ErrorCause']="group_type_id"; 
                  break(1); 
               }
              
               
               
           }
           if($c==3)
           {
             # if (intval($data[$c])>0) 
              if (intval($data[$c])>0 && preg_match("/^[0-9]*$/",$data[$c]))
              {
                  $model_id= intval($data[$c]);
                  
              }elseif(strlen($data[$c])>1)
              {
                  $model_id=GetModelIdByName(trim($data[$c]),$table_name_models);
                  if ($model_id===false)
                   {
                       $model_id=GetModelIdByName($model_name_Correction[trim($data[$c])],$table_name_models);  
                   }
                   # var_dump ($model_id);
              }else
              {  
                  $model_id=false;
                  
              }  
              if ($model_id===false)
              {
                  $data['ErrorCause']="model_id"; 
                  break(1); 
                  
                  
              } 
               
           }             
           if($c==4)
           {
              if (intval($data[$c])>0 && preg_match("/^[0-9]*$/",$data[$c])) 
              {
                  $model_type_id= intval(trim($data[$c]));
                 # var_dump ($model_type_id);
              }elseif(strlen($data[$c])>1)
              {
                  $model_type_name=preg_replace("/\,/",".",strtoupper(trim($data[$c])));
                  $model_type_id=GetModelTypeIdByNameAndModelID($model_id,$model_type_name,$table_name_model_types);
                 # $model_type_id=GetModelTypeIdByName($model_type_name,$table_name_model_types);
                  #var_dump ($model_type_id);
                 // $data['ErrorCauseM']=$model_type_name;
              }else
              {  
                  $model_type_id=false;
                  
              }  
              if ($model_type_id===false)
              {
                  $data['ErrorCause']="model_type_id"; 
                 
                  break(1); 
                  
                  
              } 
           
         } 
          
        # var_dump($data['ErrorCause']);  
        
       }
       if (isset($data['ErrorCause']))
       {
          $ErrorCauseArray[]=$data;  
       } else
       {        
        $count+=InsertItemToDbCatalogItem(0,$group_type_id,$brand_code,$item_code,$model_id,$model_type_id,$table_name_catalog_items); 
             
       }
   }
   
   fclose($handle);
   return Array("Count"=>$count,"ErrorCauseString"=>$ErrorCauseArray);
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
        $model_id=explode("///",$_POST['model_id'])[0];         
        $model_types_id=explode("///",$_POST['common_model_type_id'])[0];
       
        
        InsertItemToDbCatalogItem($_POST['group_select'],$_POST['group_type_select'],$brand_code,$item_code,$model_id,$model_types_id,$catalog_items);    
           
          
      }
       // add to catalog structure
    if (isset($_POST['catalog_structure_add']) && $_POST['catalog_structure_add']!="" )
    {                                                                        
      if ( isset($_POST['group_select']) && $_POST['group_select']!="" 
      && isset($_POST['group_type_select']) && $_POST['group_type_select']!=""
      && isset($_POST['brand_select']) &&  $_POST['brand_select']!=""
      && isset($_POST['model_id']) &&  $_POST['model_id']!="" 
      && isset($_POST['common_model_type_id']) &&  $_POST['common_model_type_id']!="" 
      )
      {
          $brand_code=explode("///",$_POST['brand_select'])[0];
          $item_code=preg_replace("/[^A-Za-z0-9]/","",$_POST['item_code']);  
          $model_id=explode("///",$_POST['model_id'])[0];         
          $model_types_id=explode("///",$_POST['common_model_type_id'])[0];
         $RowCounts=InsertToDbCatalogStructure($_POST['group_select'],$_POST['group_type_select'],$brand_code,"",$model_id,$model_types_id,$catalog_items_structure);    
          $GLOBALS['MASSAGE'][]="Добавленно {$RowCounts} позиция.";
      } else
      {
          echo "ERROR";
      }  
    }
     if (isset($_POST['catalog_structure_delete']) && $_POST['catalog_structure_delete']!="" )
    {                                                                        
      if ( isset($_POST['group_select']) && $_POST['group_select']!="" 
      && isset($_POST['group_type_select']) && $_POST['group_type_select']!=""
      && isset($_POST['brand_select']) &&  $_POST['brand_select']!=""
      && isset($_POST['model_id']) &&  $_POST['model_id']!="" 
      && isset($_POST['common_model_type_id']) &&  $_POST['common_model_type_id']!="" 
      )
      {
          $brand_code=explode("///",$_POST['brand_select'])[0];
          $item_code=preg_replace("/[^A-Za-z0-9]/","",$_POST['item_code']);  
          $model_id=explode("///",$_POST['model_id'])[0];         
          $model_types_id=explode("///",$_POST['common_model_type_id'])[0];
         $RowCounts=DeleteFromDbCatalogStructure($_POST['group_select'],$_POST['group_type_select'],$brand_code,"",$model_id,$model_types_id,$catalog_items_structure);    
           $GLOBALS['MASSAGE'][]="Удалена {$RowCounts} позиция.";
      } else
      {
          echo "ERROR";
      }  
    }
    
   if (isset($_POST['modelID']) && $_POST['modelID']!="")
   {
      $model_id=explode("///",$_POST['modelID'])[0];
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
         $model_id=explode("///",$_POST['model_id'])[0];
          
         LoadDeleteDataFromTecDoc(true,$_POST['tecdoc_str_id'],$_POST['group_select'],$_POST['group_type_select'],$model_id,$types_id,$tecdoc_types_id,$carmodel,$carmodel_types,$brands,$catalog_items,$catalog_items_structure); 
              
         
             
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
         $model_id=explode("///",$_POST['model_id'])[0];
          
         LoadDeleteDataFromTecDoc(false,$_POST['tecdoc_str_id'],$_POST['group_select'],$_POST['group_type_select'],$model_id,$types_id,$tecdoc_types_id,$carmodel,$carmodel_types,$brands,$catalog_items,$catalog_items_structure); 
              
         
             
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
        $model_id=explode("///",$_POST['search_modelID'])[0];
        $tecdoc_model_id=explode("///",$_POST['search_modelID'])[1]; 
        
     echo ShowModelsTypesArrayFromTecDoc(RecieveModelsTypesArrayFromTecDoc($tecdoc_brand_code,$tecdoc_model_id),$carmodel_types); 
       
      exit(); 
   }
   if (isset($_POST['search_modelID']) && isset($_POST['search_brand_id']) && isset($_POST['find_tecdoc_model_types_types']))
   {
        $brand_code=explode("///",$_POST['search_brand_id'])[0];
        $tecdoc_brand_code=explode("///",$_POST['search_brand_id'])[1];
        $model_id=explode("///",$_POST['search_modelID'])[0];
        $tecdoc_model_id=explode("#",$_POST['search_modelID'])[1]; 
        
        echo  ShowOptionModelTypesArray(RecieveModelTypesByModelID($model_id,$carmodel_types)); 
       
        exit(); 
       
   }
    if (isset($_POST['common_brand_id']) && $_POST['common_brand_id']!=""    
    && isset($_POST['tecdoc_model_id']) /*&& $_POST['tecdoc_model_type_id']!="" */
    && isset ($_POST['add_model_id'])
    && isset ($_POST['model_name'] ) && $_POST['model_name']!=""
    )
    {    
        $brand_code=explode("///",$_POST['common_brand_id'])[0];        
        $countRowAdd=AddModel($brand_code,$_POST['tecdoc_model_id'],$_POST['model_name'],$carmodel); 
        echo "Добавленна ".$countRowAdd." модель(ли) - {$_POST['model_name']}";  
        exit();  
    }
   
   
   if (isset($_POST['common_brand_id']) && $_POST['common_brand_id']!=""
    && isset($_POST['model_id']) && $_POST['model_id']!=""
    && isset($_POST['tecdoc_model_type_id']) /*&& $_POST['tecdoc_model_type_id']!="" */
    && isset ($_POST['add_model_type_id'])
    && isset ($_POST['model_type_name'] ) && $_POST['model_type_name']!=""
    )
    {
      # var_dump($_POST);
       $countRowAdd=0; 
       if (CheckIfModelTypesIsInDb($_POST['tecdoc_model_type_id'],$carmodel_types)==0)
       {
         $model_id=explode("///",$_POST['model_id'])[0];
         $brand_code=explode("///",$_POST['common_brand_id'])[0];
         
        $countRowAdd=AddModelType($model_id,$_POST['tecdoc_model_type_id'],$_POST['model_type_name'],$carmodel_types);  
        echo "Добавленна ".$countRowAdd." тип модели - {$_POST['model_type_name']}";   
        exit();      
       } 
       if ($_POST['tecdoc_model_type_id']=="")
       {
           $model_id=explode("///",$_POST['model_id'])[0];
         $brand_code=explode("///",$_POST['common_brand_id'])[0];
         
          $countRowAdd=AddModelType($model_id,$_POST['tecdoc_model_type_id'],$_POST['model_type_name'],$carmodel_types);  
         echo "Добавленна ".$countRowAdd." модель - {$_POST['model_type_name']}";   
         exit();      
           
           
       }
      echo "Ошибка.";
      exit();
    }
   
    if (isset($_POST['common_brand_id']) && $_POST['common_brand_id']!=""
    && isset($_POST['model_type_id']) && $_POST['model_type_id']!=""
    && isset($_POST['tecdoc_model_type_id']) && $_POST['tecdoc_model_type_id']!=""
    && isset ($_POST['tie_model_type_id'])
    
    )
    {
      $model_type_id=explode("///",$_POST['model_type_id'])[0];  
      $countRowUpdate=TieModelTypeFromTecDoc($model_type_id,$_POST['tecdoc_model_type_id'],"",$carmodel_types);  
        
        
        
       echo "Привязанна ".$countRowUpdate." тип(а) модели ";   
        exit();   
    }
    
    if (isset($_POST['common_brand_id']) && $_POST['common_brand_id']!=""
    && isset($_POST['model_id']) && $_POST['model_id']!=""
    && isset($_POST['tecdoc_model_id']) && $_POST['tecdoc_model_id']!=""
    && isset ($_POST['tie_model_id'])
    
    )
    {
         $model_id=explode("///",$_POST['model_id'])[0]; 
         $countRowUpdate=TieModelFromTecDoc($model_id,$_POST['tecdoc_model_id'],"",$carmodel);
         echo "Привязанна ".$countRowUpdate."  модель ";   
         exit();   
    }
    if (isset($_POST['file_download']))
    {
        if (file_exists($_FILES["file"]["tmp_name"]))   
        {
             
             if ($_FILES["file"]["type"]=="application/vnd.ms-excel" || $_FILES["file"]["type"]=="application/vnd.excel"||$_FILES["file"]["type"]=="text/comma-separated-values" || $_FILES["file"]["type"]=="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" ) 
             {
                 #echo  $_FILES["file"]["tmp_name"]; 
                $csvFile=MakeCSVFromExell($_FILES["file"]["tmp_name"],""); 
                #echo $csvFile; 
              #  CSVFileDeal($filePathName,$table_name_catalog_items)
              # Array("Count"=>$count,"ErrorCauseString"=>$ErrorCauseArray);   
                $infoArray=CSVFileDeal($csvFile,$catalog_items,$catalog_groups_types,$carmodel,$carmodel_types);    #$table_name_catalog_items,$table_name_group_types,$table_name_models,$table_name_model_types
                echo "Загруженно : ".$infoArray['Count']." позиций <br>"; 
                echo "НЕ Загруженно : ".count($infoArray['ErrorCauseString'])." позиций <br>"; 
                foreach ($infoArray['ErrorCauseString'] as $data)
                {
                    echo $data[0]."-".$data[1]."-".$data[2]."-".$data[3]."-".$data[4]."-".$data['ErrorCause']."<br>";
                }
                #var_dump($infoArray['ErrorCauseString']);
             } 
             
            
        }
    }
   #########################################################  
   ?>
        <html>
     <head>
         <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
         <script type="text/javascript" src="/bitrix/js/itgScript/jquery.min.js"></script>
                 <script src="/js/catalog_service.js" ></script> 
     </head>
    <body>
     <div id='massage_show' style="width:100%; height:auto; border-bottom:solid 1px black;">
        <?
         foreach ($GLOBALS['MASSAGE'] as $massage)
         {
             echo  $massage."<br>";
         }
        ?>
     </div>   
    <div id='catalog_group_show' style="width:100%; height:350px; border-bottom:solid 1px black;">
           <form action="/catalog_service.php" method="POST" enctype="multipart/form-data"> 
             <div style="width:20%;height:50%;float:left;margin-left:5px;">  
             <?
              echo ShowAllGroupArray(RecieveAllGroupsArray($catalog_groups) ) 
             ?>   
             </div>
             <div id='group_type_div' style="width:15%;height:50%;float:left; margin-left:5px;">
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
                  <input  type='submit' name='tecdoc_str_id_delete' value='Удалить Данные Текдока'  style='border-radius:5px; height:35px; margin-top: 1%; margin-bottom: 1%;;'> </input> 
                  <div name='tecdoc_model_id_div' id='tecdoc_model_id_div'>
                          <?
                        #  echo  ShowOptionModelsArray(RecieveModelsByBrand(33548590,$carmodel) )
                           
                          ?>
                          <select  style='border-radius:5px; height:35px; margin-top: 1%;'>
                           <option value='0'>выбрать модель</option>
                       </select> 
                  </div>
                  <div id='tecdoc_model_types_id_div' style='margin-top: 1%;'>
                       <select id='tecdoc_model_type_id' style='border-radius:5px; height:35px; margin-top: 1%;'>
                           <option value='0'>выбрать тип модели</option>
                       </select> 
                  </div>
                   <input  type='submit' name='catalog_structure_add' value='Добавить в структуру каталога'  style='border-radius:5px; height:35px; margin-top: 1%;float:left;'> </input>  
                  <input  type='submit' name='catalog_structure_delete' value='Удалить из структуры каталога'  style='border-radius:5px; height:35px; margin-top: 1%;float:left;'> </input>
                </div>
                
                <div id='group_type_div_brand' style="width:40%;height:50%;float:left; margin-left:5px; border-radius:5px;">
                  
                  <?
                    echo ShowBrandsArray(RecieveBrandsArray($brands))  
                  ?> 
                  <input type="text" name='item_code' value='' style='border-radius:5px; height:35px;'> </input>   
                  <input  type='submit'  name='item_code_add' value='добавить артикул'  style='border-radius:5px; height:35px;'> </input>
                  <p>Добавить Артикул</p>
                </div> 
                <div id='file_brand_div' style="height:50%;float:left; margin-left:5px; border-radius:5px;">
                    <input type='file'  name='file' > </input>
                    <input type='submit' name='file_download'></input>
                </div>           
             </form>
             
             
     </div>     
  
   <?
  
   ?>
    <div id='catalog_group_add' style=" margin-top:20px;width:100%; height:auto; float:left; position:relative; border-bottom: solid 1px black;">
      <form action="/catalog_service.php" method="POST" enctype="multipart/form-data"> 
        <input type='text' name='add_group' value='' style='border-radius:5px; height:35px;' ></input>
        <input  type='submit' value='Добавить Группу'  style='border-radius:5px; height:35px;'> </input>
      </form>
    </div> 
     <div id='catalog_model_types_add' style="margin-top:20px;width:100%; height:auto; float:left; position:relative; border-bottom: solid 1px black;">  
       <div id='model_add_p_info' style=""> </div>
      <!-- <form action="/catalog_service.php" method="POST" enctype="multipart/form-data">  --> 
          <div id="add_model_div" style="float:left; margin-right:1%;">
             <?
                        echo ShowBrandsArray(RecieveBrandsArray($brands))  
             ?> 
          </div>
         <input type='text' id='model_id_add' disabled="disabled"  name='search_model_name' value='' style='width:5%;border-radius:5px; height:35px;float:left; margin-right: 1%;' ></input>  
         <input type='text' id='model_name_add' name='search_model_name' value='' style='border-radius:5px; width:40%; height:35px;float:left;' ></input>
         <input  type='submit' id='add_model' name='add_model' value='Добавить Модель'  style='border-radius:5px; height:35px; margin-bottom: 1%; float:left;'> </input> 
         <div id='tie_model_div' style='float:left; margin-left: 1%;'>
         </div>
         
      <!--</form> -->
      
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
            <input type='text' id='tecdoc_model_type_id_add' disabled="disabled"  name='model_type_id_add' value='' style='float:left;width:30%;border-radius:5px; height:35px;float:left; margin-right: 1%;' ></input>
            <input type='text' id='tecdoc_model_type_name_add' name='search_model_name' value='' style='float:left;border-radius:5px; height:35px;float:left;' ></input>
            <input  type='submit' id="model_type_add" name='search_type_model' value='Добавить Тип Модели'  style='float:left;border-radius:5px; height:35px; margin-bottom: 1%; float:left;'> </input>  
            
          </div>
           <div id='model_type_search_result' style="float:left;border-bottom: solid 1px black; width:40%; margin-left: 1%;">  
              
            </div>  
            <div id="type_type_select_div" style="margin-right:1%; width:10%;">
           
              
                
            </div>
      </div>
      <div id='model_types_tie' style="margin-top:20px;width:100%; height:auto; float:left; position:relative; border-bottom: solid 1px black;">    
           
           
     
      </div>
      
   <?
  
  
  
  #$sql="SELECT * FROM tof_generic_articles LIMIT 1";
  
  #$result=$DBR->Query($sql)->Fetch();
  
  #var_dump($result); 
  
  
    
    
    
    
    
?> 
   </body>
   </html>
    