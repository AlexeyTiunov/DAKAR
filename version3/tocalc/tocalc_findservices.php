<?
    require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php'); 
    global $DB;
    function getServiceNameByID($serviceID,$language)
       {
           if ($language!="")
           {
              $SERVICE_NAME="SERVICE_NAME_{$language}" ;
           } else
           {
              $SERVICE_NAME="SERVICE_NAME"; 
           }
           global $DB;
           $sql="SELECT {$SERVICE_NAME} FROM b_autodoc_tscalc_services WHERE ID={$serviceID} LIMIT 1";
           $result= $DB->Query($sql);
           $serviceProperties=$result->Fetch();
           
           return $serviceProperties[$SERVICE_NAME];
           
       }
     function ShowServicesArraySelectOptions($ServiceArray)
     {
          $optionString="";
          foreach ($ServiceArray as $service)
          {
            $optionString.="<option value='{$service['ID']}'>{$service['NAME']}</option>";   
              
          }
        return $optionString; 
     }  
   if (!isset($_POST['BrandID']) || !isset($_POST['ModelID']))
      {
          die("ERRORRRR");
      }  
     
      $brandID=preg_replace("/[^0-9]/","",$_POST['BrandID']);   
      $modelID=preg_replace("/[^0-9]/","",$_POST['ModelID']);
      $mileAgeID=0; 
       if (isset($_POST['LNG']))
      {
       $language=$_POST['LNG'];   
      } else
      {
        $language="";   
      }
      
      $sql="SELECT * FROM b_autodoc_tscalc_services_price WHERE BRAND_ID={$brandID} AND MODEL_ID={$modelID} AND MILEAGE_ID={$mileAgeID}"; 
      #echo $sql; 
      $servcesPropertyArray=array();
      $result= $DB->Query($sql);
    
       while ($servicesArray=$result->Fetch())
      {
           
         $servcesPropertyArray[$servicesArray['SERVICE_ID']]['ID']=$servicesArray['SERVICE_ID'];
         $servcesPropertyArray[$servicesArray['SERVICE_ID']]['NAME']=getServiceNameByID($servicesArray['SERVICE_ID'],$language);
         $servcesPropertyArray[$servicesArray['SERVICE_ID']]['BRAND_ID']=$servicesArray['BRAND_ID'];
         $servcesPropertyArray[$servicesArray['SERVICE_ID']]['MODEL_ID']=$servicesArray['MODEL_ID'];
         $servcesPropertyArray[$servicesArray['SERVICE_ID']]['MILEAGE_ID']=$servicesArray['MILEAGE_ID'];
         $servcesPropertyArray[$servicesArray['SERVICE_ID']]['PRICE']=floatval($servicesArray['PRICE']);
        # $servcesPropertyArray[$servicesArray['SERVICE_ID']]['ITEMS']=getItemPositionForService($servicesArray['SERVICE_ID'],$servicesArray['BRAND_ID']) ;   
          
      }
    
      echo ShowServicesArraySelectOptions($servcesPropertyArray);
    
    
    
    
?>