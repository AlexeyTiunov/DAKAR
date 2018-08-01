<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php'); 
 global $DB;

 if (isset($_POST['typeAutoId']) )
 {
      $typeOfAuto=preg_replace("/[^0-9]*/i","",$_POST['typeAutoId']);
      
      $sql="SELECT ID AS ID,ModelName,DateBegin FROM b_autodoc_carmodels WHERE BrandCode={$typeOfAuto} GROUP BY ID,ModelName,DateBegin";
     
     $result=$DB->Query($sql);
     $stringForOptionModel="";
     while($modelArray=$result->Fetch())
     {
        $modelName=strtoupper($modelArray['ModelName']);
        $yearBegin=explode("-",$modelArray['DateBegin'])[0]; 
        $stringForOptionModel.="<option value='{$modelArray['ID']}'>{$modelName}</option>" ; 
         
     }
     
      echo   $stringForOptionModel;
 }
    
    
    
    
    
    
    
?>