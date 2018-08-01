<?
  require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php'); 
  global $DB; 
  function getItemPositionForService($serviceID,$BrandID,$ModelID,$ModelTypeID,$language)
  {   
    global $DB;
    $sql=" SELECT * FROM b_autodoc_tscalc_item_price WHERE SERVICE_ID={$serviceID} AND MODEL_ID={$ModelID} AND MODEL_TYPE_ID={$ModelTypeID} AND BRAND_ID={$BrandID}";
    #echo $sql;
    $result=$DB->Query($sql);
    $itemPositionProperties= array();
    $captionColumnName=($language=="")?"ITEM_CAPTION":"ITEM_CAPTION_{$language}";
    while ($itemPositionArray=$result->Fetch())
    {
       $itemPositionProperties[$itemPositionArray['BRAND_ID']][$itemPositionArray['ITEM_CODE']]['ITEMCODE']=$itemPositionArray['ITEM_CODE']; 
       $itemPositionProperties[$itemPositionArray['BRAND_ID']][$itemPositionArray['ITEM_CODE']]['BRAND_ID']=$itemPositionArray['BRAND_ID']; 
       $itemPositionProperties[$itemPositionArray['BRAND_ID']][$itemPositionArray['ITEM_CODE']]['CAPTION']=$itemPositionArray[$captionColumnName];
       $itemPositionProperties[$itemPositionArray['BRAND_ID']][$itemPositionArray['ITEM_CODE']]['QUANTITY']=$itemPositionArray['QUANTITY'];
       $itemPositionProperties[$itemPositionArray['BRAND_ID']][$itemPositionArray['ITEM_CODE']]['PRICE']=$itemPositionArray['PRICE'];
       $itemPositionProperties[$itemPositionArray['BRAND_ID']][$itemPositionArray['ITEM_CODE']]['CURRENCY']=$itemPositionArray['CURRENCY'];

        
    }   
       
    return $itemPositionProperties;  
  } 
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
   function ShowServiceItemsPropertyArray($ItemsPropertyArray)
   {
        $htmlPropertyString="<table style='width:100%;'>";
        foreach ($ItemsPropertyArray as $BrandPosition)
        {
            $color_style="#eeeeee";
            foreach($BrandPosition as $key=>$ItemCodePosition)
            {     
             if ($color_style=="#eeeeee") $color_style="white";
             else $color_style="#eeeeee"; 
             
              if (explode('.',$ItemCodePosition['QUANTITY'])[1]=="00")
              $quantity=intval(explode('.',$ItemCodePosition['QUANTITY'])[0]);
              else  $quantity=floatval($ItemCodePosition['QUANTITY']);
              if (explode('.',$ItemCodePosition['PRICE'])[1]=="00")
                 $price=intval(explode('.',$ItemCodePosition['PRICE'])[0]);
              else  $price=floatval($ItemCodePosition['PRICE']);
              if ($ItemCodePosition['CURRENCY']=="UAH")
              {
                $currency="грн.";  
              }               
              elseif  ($ItemCodePosition['CURRENCY']=="USD") 
              {
                  $koef=PriceKoef($ItemCodePosition['CURRENCY'],UAH);
                  $price=$price*$koef;
                  $currency="грн.";
              }
              else  $currency=$ItemCodePosition['CURRENCY']; 
              
              $sum=$price*$quantity;
              
             $htmlPropertyString.="<tr style='height:50px;background:{$color_style};'>"; 
                      $htmlPropertyString.="<td style='width:15%;'>";  
                     
                                          
                     $htmlPropertyString.="</td>";
                      
                      $htmlPropertyString.="<td style='width:10%;'>";                        
                                   $htmlPropertyString.="<input type='checkbox' class='service_check' checked  price='{$sum}'></input>"; 
                                   $htmlPropertyString.="<input type='hidden' name='ITEM_ITEM_SERVICES[ITEMS][{$ItemCodePosition['BRAND_ID']}][{$ItemCodePosition['ITEMCODE']}][ITEMCODE]' value='{$ItemCodePosition['ITEMCODE']}'>";
                                   $htmlPropertyString.="<input type='hidden' name='ITEM_ITEM_SERVICES[ITEMS][{$ItemCodePosition['BRAND_ID']}][{$ItemCodePosition['ITEMCODE']}][BRANDCODE]' value='{$ItemCodePosition['BRAND_ID']}'>";
                                   $htmlPropertyString.="<input type='hidden' name='ITEM_ITEM_SERVICES[ITEMS][{$ItemCodePosition['BRAND_ID']}][{$ItemCodePosition['ITEMCODE']}][PRICE]' value='{$ItemCodePosition['PRICE']}'>"; 
                                   $htmlPropertyString.="<input type='hidden' name='ITEM_ITEM_SERVICES[ITEMS][{$ItemCodePosition['BRAND_ID']}][{$ItemCodePosition['ITEMCODE']}][CURRENCY]' value='{$ItemCodePosition['CURRENCY']}'>"; 
                                   $htmlPropertyString.="<input type='hidden' name='ITEM_ITEM_SERVICES[ITEMS][{$ItemCodePosition['BRAND_ID']}][{$ItemCodePosition['ITEMCODE']}][CAPTION]' value='{$ItemCodePosition['CAPTION']}'>"; 
                                   $htmlPropertyString.="<input type='hidden' name='ITEM_ITEM_SERVICES[ITEMS][{$ItemCodePosition['BRAND_ID']}][{$ItemCodePosition['ITEMCODE']}][QUANTITY]' value='{$ItemCodePosition['QUANTITY']}'>";                                      
                      $htmlPropertyString.="</td>";
                      $htmlPropertyString.="<td style='width:45%;font-style:Italic;font-weight:bold;'>";  
                                          $htmlPropertyString.= $ItemCodePosition['CAPTION'] ; 
                     
                      $htmlPropertyString.="</td>";
                      $htmlPropertyString.="<td style='width:30%;'>";
                                          
                                                             
                                          $htmlPropertyString.= "<p style='color:#D30000;font-size:12pt; margin-top:0px; margin-bottom:0px;'>{$quantity} x {$price} {$currency}</p>" ;   
                      $htmlPropertyString.="</td>";
            
             
             $htmlPropertyString.="</tr>"; 
            } 
        }
        $htmlPropertyString.="</table>"; 
        return  $htmlPropertyString;
   }
   function ShowSevicePropertyArray($servicePropertyArray)
   {
        $htmlPropertyString="<table style='width:100%;'>";
        foreach ($servicePropertyArray as $propertyPosition)
        {
           $htmlPropertyString.="<tr style='background:#CDCDCD; height:30px;'>";
             $htmlPropertyString.="<td style='width:10%;height:30px;'>"; 
                $htmlPropertyString.="<input type='checkbox' class='service_check' checked  id='main_service' price='{$propertyPosition['PRICE']}'></input>";
                $htmlPropertyString.="<input type='hidden' name='ITEM_ITEM_SERVICES[SERVICES][{$propertyPosition['BRAND_ID']}][{$propertyPosition['ID']}][ITEMCODE]' value='{$propertyPosition['ID']}'>"; 
                $htmlPropertyString.="<input type='hidden' name='ITEM_ITEM_SERVICES[SERVICES][{$propertyPosition['BRAND_ID']}][{$propertyPosition['ID']}][BRANDCODE]' value='{$propertyPosition['BRAND_ID']}'>";
                $htmlPropertyString.="<input type='hidden' name='ITEM_ITEM_SERVICES[SERVICES][{$propertyPosition['BRAND_ID']}][{$propertyPosition['ID']}][PRICE]' value='{$propertyPosition['PRICE']}'>";
                $htmlPropertyString.="<input type='hidden' name='ITEM_ITEM_SERVICES[SERVICES][{$propertyPosition['BRAND_ID']}][{$propertyPosition['ID']}][CAPTION]' value='{$propertyPosition['CAPTION']}'>";
                $htmlPropertyString.="<input type='hidden' name='ITEM_ITEM_SERVICES[SERVICES][{$propertyPosition['BRAND_ID']}][{$propertyPosition['ID']}][isService]' value='TRUE'>"; 
                $htmlPropertyString.="<input type='hidden' name='ITEM_ITEM_SERVICES[SERVICES][{$propertyPosition['BRAND_ID']}][{$propertyPosition['ID']}][QUANTITY]' value='1'>";
                $htmlPropertyString.="<input type='hidden' name='ITEM_ITEM_SERVICES[SERVICES][{$propertyPosition['BRAND_ID']}][{$propertyPosition['ID']}][isItem]' value='FALSE'>";                
             $htmlPropertyString.="</td>";  
             
             $htmlPropertyString.="<td style='width:50%;font-weight:800;font-size:20px;height:30px;'>"; 
                $htmlPropertyString.="<p style='font-size:12pt; margin-top:0px; margin-bottom:0px;'>{$propertyPosition['CAPTION']}</p>";                 
             $htmlPropertyString.="</td>";
             
             $htmlPropertyString.="<td style='width:5%;height:30px;'>"; 
                                 
             $htmlPropertyString.="</td>";
             
              
             #$htmlPropertyString.="<td style='width:5%;height:30px;'>"; 
                     #  $htmlPropertyString.="<p style='color:#D30000;font-size:12pt; margin-top:0px; margin-bottom:0px;'>{$propertyPosition['QUANTITY']}X</p>";     
                                 
            # $htmlPropertyString.="</td>";
             
             $htmlPropertyString.="<td style='width:20%;height:30px;'>";
             
                
                $htmlPropertyString.="<input type='text' style='width:100%;display:table;margin-bottom:0px;padding:0;border:solid 0px;width:50%;background:#CDCDCD;color:#D30000;font-size:17px; font-weight:800;' 
                                      disabled='disabled' value=' {$propertyPosition['PRICE']} '></input>";                 
             $htmlPropertyString.="</td>";
             $htmlPropertyString.="<td style='width:5%;height:28px;'>"; 
                                    $htmlPropertyString.="<p style='color:#D30000;font-size:12pt; margin-top:0px; margin-bottom:0px;'>грн.</p>"; 
             $htmlPropertyString.="</td>";  
           
           $htmlPropertyString.="</tr>"; 
           
           $htmlPropertyString.="<tr>";
                 $htmlPropertyString.="<td colspan='5' style=''>";
                        $htmlPropertyString.="<div style='margin-left:3%;width:97%'>"; 
                            $htmlPropertyString.= ShowServiceItemsPropertyArray($propertyPosition['ITEMS']); 
                        $htmlPropertyString.="</div>";  
                 $htmlPropertyString.="</td>"; 
           $htmlPropertyString.="</tr>";   
            
        }
       $htmlPropertyString.="</table>";   
    return  $htmlPropertyString;  
   }
   function manualConnect()
    {
             $port=31006;
        $DB = new mysqli("localhost","bitrix","a251d851","bitrix",$port);
        $DB->set_charset("utf8");
              $DB->query("SET NAMES 'utf8'");
        return $DB;
    }
   function PriceKoef($Currency , $UserCurrency)      // $Currency-from      $UserCurrency - to currency
     { 
         if ($UserCurrency=="USD")  
         {
             if ($Currency=="USD") return 1;
             elseif ($Currency=="UAH") 
             {
                 $DBB = manualConnect();
                 $sql="SELECT RATE AS USDRATE FROM b_catalog_currency_rate  WHERE CURRENCY='USD' ORDER BY ID DESC LIMIT 1";
                 $result=$DBB->query($sql);
                 $rate=$result->fetch_assoc();
                  return 1/$rate['USDRATE'];
               
                 
             }
             elseif ($Currency=="EUR") 
             {
                 $DBB = self::manualConnect();  
                 $sql="SELECT RATE AS USDRATE FROM b_catalog_currency_rate  WHERE CURRENCY='USD' ORDER BY ID DESC LIMIT 1";
                 $result=$DBB->query($sql);
                 $rateusd=$result->fetch_assoc();
                 
                 $sql="SELECT RATE AS EURRATE FROM b_catalog_currency_rate  WHERE CURRENCY='EUR' ORDER BY ID DESC LIMIT 1";
                 $result=$DBB->query($sql);
                 $rateeur=$result->fetch_assoc();
                 
                 return  $rateeur['EURRATE']/ $rateusd['USDRATE'];
                 
             } 
             
         }
         elseif ($UserCurrency=="UAH")
         {
              if ($Currency=="UAH") return 1;
              elseif($Currency=="USD")
              {
                    $DBB = manualConnect();
                 $sql="SELECT RATE AS USDRATE FROM b_catalog_currency_rate  WHERE CURRENCY='USD' ORDER BY ID DESC LIMIT 1";
                 $result=$DBB->query($sql);
                 $rate=$result->fetch_assoc();
                  return $rate['USDRATE'];
                  
              } 
              elseif($Currency=="EUR")
              {
                  $DBB = manualConnect();
                 $sql="SELECT RATE AS EURRATE FROM b_catalog_currency_rate  WHERE CURRENCY='EUR' ORDER BY ID DESC LIMIT 1";
                 $result=$DBB->query($sql);
                 $rate=$result->fetch_assoc();
                  return $rate['EURRATE'];
                  
              }
             
             
             
         }
         
     } 
  if (!isset($_POST['BrandID']) || !isset($_POST['ModelID']))
  {
      die("ERRORRRR");
  }  
 
  $brandID=preg_replace("/[^0-9]/","",$_POST['BrandID']);   
  $modelID=preg_replace("/[^0-9]/","",$_POST['ModelID']);
  $modelTypeID=preg_replace("/[^0-9]/","",$_POST['ModelTypeID']);
  $mileAgeID=preg_replace("/[^0-9]/","",$_POST['MileAgeID']);
  if (isset($_POST['LNG']))
  {
   $language=$_POST['LNG'];   
  } else
  {
    $language="";   
  }
  
  if (isset($_POST['ServiceID']))
  {
      $serviceID= preg_replace("/[^0-9]/","",$_POST['ServiceID']);  
      $sql="SELECT * FROM b_autodoc_tscalc_services_price WHERE BRAND_ID={$brandID} AND MODEL_ID={$modelID} AND MODEL_TYPE_ID={$modelTypeID} AND MILEAGE_ID={$mileAgeID} AND SERVICE_ID={$serviceID}";  
  } else
  {
     $sql="SELECT * FROM b_autodoc_tscalc_services_price WHERE BRAND_ID={$brandID} AND MODEL_ID={$modelID} AND MODEL_TYPE_ID={$modelTypeID} AND MILEAGE_ID={$mileAgeID}";  
  }
  
  
  #echo $sql; 
  $servcesPropertyArray=array();
  $result= $DB->Query($sql);
  while ($servicesArray=$result->Fetch())
  {
       
     $servcesPropertyArray[$servicesArray['SERVICE_ID']]['ID']=$servicesArray['SERVICE_ID'];
     $servcesPropertyArray[$servicesArray['SERVICE_ID']]['CAPTION']=getServiceNameByID($servicesArray['SERVICE_ID'],$language);
     $servcesPropertyArray[$servicesArray['SERVICE_ID']]['BRAND_ID']=$servicesArray['BRAND_ID'];
     $servcesPropertyArray[$servicesArray['SERVICE_ID']]['MODEL_ID']=$servicesArray['MODEL_ID'];
     $servcesPropertyArray[$servicesArray['SERVICE_ID']]['MILEAGE_ID']=$servicesArray['MILEAGE_ID'];
     $servcesPropertyArray[$servicesArray['SERVICE_ID']]['PRICE']=floatval($servicesArray['PRICE']);      
     $servcesPropertyArray[$servicesArray['SERVICE_ID']]['ITEMS']=getItemPositionForService($servicesArray['SERVICE_ID'],$servicesArray['BRAND_ID'],$modelID,$modelTypeID,$language) ;   
      
  }
  echo ShowSevicePropertyArray($servcesPropertyArray) ; 
  #var_dump($servcesPropertyArray); 
    
?>