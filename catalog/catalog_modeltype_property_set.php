<?
 require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
 $GLOBALS['CATALOG_MODELTYPES_TABLES']="b_autodoc_carmodels_types";
  $GLOBALS['CATALOG_MODELTYPES_TABLES_BASE64PICTURE_COLUMN_NAME']="PICTURE_BASE64";
  $GLOBALS['CATALOG_MODELTYPES_TABLES_DESCRIPTION_COLUMN_NAME']="DESCRIPTION";
 function SetItemValueInDB($table_name,$column_name,$value,$condition_id)
  {
    global $DB;
    
    $sql="UPDATE {$table_name} SET `{$column_name}`='{$value}' WHERE ID='{$condition_id}'" ;
   // var_dump($sql);  
    $result=$DB->Query($sql);
    
     return intval($result->AffectedRowsCount());  
      
  }  
  
  
  if (isset($_POST['LOAD_IMAGE']))
  {
    if (!file_exists($_FILES[0]["tmp_name"]))
    {  
        echo  ini_get('upload_tmp_dir');
       die("ERROR");      
       
    } else
    {
        # echo  ini_get('upload_tmp_dir');
        # var_dump($_FILES);
    }
   // copy($_FILES["file"]["tmp_name"],$_SERVER["DOCUMENT_ROOT"]."/".($_FILES['file']['name'])); 
    $FilePicture=fopen($_FILES[0]["tmp_name"],'r');
    $filedata=fread($FilePicture,50000000);
    
    fclose($FilePicture);
    $base64encodedata=base64_encode($filedata);
    #var_dump($base64encodedata);
    
    
    SetItemValueInDB($GLOBALS['CATALOG_MODELTYPES_TABLES'],$GLOBALS['CATALOG_MODELTYPES_TABLES_BASE64PICTURE_COLUMN_NAME'],
    $base64encodedata,$_POST['MODEL_TYPE_ID']);
    echo $base64encodedata ;
    
  }    
   elseif (isset($_POST['DESCRIPTION_UPDATE']))
   {
       $count=SetItemValueInDB($GLOBALS['CATALOG_MODELTYPES_TABLES'],$GLOBALS['CATALOG_MODELTYPES_TABLES_DESCRIPTION_COLUMN_NAME'],
    $_POST['DESCRIPTION'],$_POST['MODEL_TYPE_ID']); 
      if ($count>0)
      {
          echo $_POST['DESCRIPTION']."////Обновленно////"; 
      }     
       
   }
    
?>