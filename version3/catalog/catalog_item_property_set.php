<?
  require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php'); 
  $GLOBALS['CATALOG_ITEMS_TABLES']="b_autodoc_items_catalog_items";
  $GLOBALS['CATALOG_ITEMS_TABLES_BASE64PICTURE_COLUMN_NAME']="Base64"; 
  function SetItemValueInDB($table_name,$column_name,$value,$condition_itemcode,$condition_brandcode)
  {
    global $DB;
    
    $sql="UPDATE {$table_name} SET `{$column_name}`='{$value}' WHERE ITEM_CODE='{$condition_itemcode}' AND  BRAND_CODE={$condition_brandcode}" ;
      
    $result=$DB->Query($sql);
    
     return intval($result->AffectedRowsCount());  
      
  }
  
  function GetIgtemValueFromDB($table_name,$column_name,$value,$condition_itemcode,$condition_brandcode)
  {
      global $DB;
      $sql="SELECT {$column_name} FROM {$table_name}   WHERE ITEM_CODE='{$condition_itemcode}' AND  BRAND_CODE={$condition_brandcode} LIMIT 1" ; 
      $result=$DB->Query($sql); 
      
     return  $result->Fetch()[$column_name];
      
  }
 # error_reporting(E_ALL);
  
  
  
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
    
    
    SetItemValueInDB($GLOBALS['CATALOG_ITEMS_TABLES'],$GLOBALS['CATALOG_ITEMS_TABLES_BASE64PICTURE_COLUMN_NAME'],
    $base64encodedata,$_POST['ITEM_CODE'],$_POST['BRAND_CODE']);
    echo $base64encodedata ;
    
  }  
  
  if (isset ($_POST['RECIEVE_IMAGE_BASE64']))
  {
    if ($_POST['ITEM_CODE'] && $_POST['BRAND_CODE'])  
    {
     echo  GetIgtemValueFromDB ($GLOBALS['CATALOG_ITEMS_TABLES'],$GLOBALS['CATALOG_ITEMS_TABLES_BASE64PICTURE_COLUMN_NAME'],
     $base64encodedata,$_POST['ITEM_CODE'],$_POST['BRAND_CODE']);
        
        
        
    } 
      
      
      
  }  
    
?>