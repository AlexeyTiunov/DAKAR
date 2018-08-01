<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php'); 
 global $DB;
 function RecieveModelTypes($modelID) 
    {
        $optionString="";
        global $DB;
        $sql="SELECT * FROM b_autodoc_carmodels_types WHERE CARMODEL_ID={$modelID} "; 
        $result=$DB->Query($sql);
        while ($carModelTypesArray=$result->Fetch())
        {
             $modelTypeName=strtoupper($carModelTypesArray['TYPE_NAME']);
            $optionString.="<option value='{$carModelTypesArray['ID']}'>{$modelTypeName}</option>";   
        }
        
        return $optionString;
    }
    
  if (isset($_POST['ModelId']) )  
  {
      $modelID=preg_replace("/[^0-9]*/i","",$_POST['ModelId']);
      
      echo   RecieveModelTypes($modelID);
      
      
      
  }  
    
 
 
 ?>