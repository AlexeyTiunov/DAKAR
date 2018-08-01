<?
require ("/media/Vol/www/bitrix/components/itg/Search/Search_ITG4.php"); 
  error_reporting(2047);
  function manualConnect()
    {
             $port=31006;
        $DB = new mysqli("localhost","bitrix","a251d851","bitrix",$port);
        $DB->set_charset("utf8");
              $DB->query("SET NAMES 'utf8'");
        return $DB;
    }
      function GetInfoFromMegaP($ItemCode)
     {
         $host = "http://emexonline.com:3000/";
         $username = "qdok";
         $password = "458TYU";
     
     
         $client = new SoapClient($host."MaximaWS/service.wsdl");
         $customer = new Customer();
         $customer->UserName = $username;
         $customer->Password = $password;
         $customerAr['UserName']  == $username;
         $customerAr['Password']  == $password;
         $params = array("Customer"=>$customer,"DetailNum"=>$ItemCode,"ShowSubsts"=>false);
         $ArrayInfo=array(
                'weight'=> 'N',
                'quantity'=>'>10', 
                'persentsupped'=>'N',
                'PartNameRus'=>''   
                  )   ;
         #$resLogin = $client->Login($params);
        try
      {    
             
             $resLogin=$client->SearchPart($params) ;
           print_r($resLogin);
            if (is_array($resLogin->SearchPartResult->FindByNumber))
            { 
                
               if (count($resLogin->SearchPartResult->FindByNumber[0])>0)
              {
                  foreach ($resLogin->SearchPartResult->FindByNumber as $info)
                  {
                      if (((string)$info->PriceLogo)=='EMIR')
                      {
                       $ArrayInfo=array(
                       'weight'=> (((string)$info->WeightGr)*1 )/1000,
                       'quantity'=> ((((string)$info->Available)*1)==0 )?'>10': ((string)$info->Available)*1,
                        'persentsupped'=>((string)$info->PercentSupped)*1,
                        'PartNameRus'=>(string)$info->PartNameRus    
                        )   ;
                        break;
                      }
                      $ArrayInfo['weight']=(((string)$info->WeightGr)*1 )/1000;
                      $ArrayInfo['persentsupped']=((string)$info->PercentSupped)*1 ; 
                       
                  }
             
             
              }
            }
            elseif(is_object($resLogin->SearchPartResult->FindByNumber) )
            {
              if(((string)$resLogin->SearchPartResult->PriceLogo)=='EMIR')
              {
                 $ArrayInfo=array(
                'weight'=> (((string)$resLogin->SearchPartResult->FindByNumber->WeightGr)*1 )/1000,
                'quantity'=> ((((string)$resLogin->SearchPartResult->FindByNumber->Available)*1)==0)?'>10': ((string)$resLogin->SearchPartResult->FindByNumber->Available)*1  ,
                 'persentsupped'=> ((string)$resLogin->SearchPartResult->FindByNumber->PercentSupped)*1,
                 'PartNameRus'=>(string)$info->PartNameRus 
                  )   ;
              }
                $ArrayInfo['weight']= (((string)$resLogin->SearchPartResult->FindByNumber->WeightGr)*1 )/1000;
                $ArrayInfo['persentsupped']=((string)$resLogin->SearchPartResult->FindByNumber->PercentSupped)*1 ;           
            }
            else
            {
               $ArrayInfo=array(
                'weight'=> 'N' ,
                'quantity'=>'>10',
                'persentsupped'=>'N',
                'PartNameRus'=>''    
                  )   ; 
            }
     }  
     catch (Exception $e) 
     {
              $ArrayInfo=array(
                'weight'=> 'N',
                'quantity'=>'>10' ,
                'persentsupped'=>'N',
                'PartNameRus'=>''       
                  )   ;
     }
         #$res=iconv('utf8','cp1251',(string)$resLogin->SearchPartResult->FindByNumber[0]->PartNameRus);
         #echo $res;
         #var_dump($resLogin);
         return $ArrayInfo;
     }
     function GetInfoFromVivat($ItemCode)
     {
         $ArrayInfo=array(
                'weight'=> 'N',
                'quantity'=>'>10', 
                'persentsupped'=>'N',
                'PartNameRus'=>'',
                'Brand'=>'',
                'Caption'=>'',
                'ItemCode'=>'',
                'AnalogArray'=>array()   
                  )   ;
         $fp = fsockopen ("srv.vivat-uae.net", 80, $errno, $errstr, 30);   
         if (!$fp) {
                    echo "$errstr ($errno)<br>\n";
                    } 
              else 
              {
                    fputs ($fp, "GET /gettoken?login=tiunovalexey&pwd=1d2e5h1c HTTP/1.0\r\nHost:srv.vivat-uae.net\r\n\r\n Connection: Close\r\n\r\n");
                    $dom = new domDocument;
                      while (!feof($fp)) 
                    {
                    #echo fgets ($fp,525);
                    #$str=iconv('cp1251','utf8',fgets ($fp,525));
                     $str=fgets ($fp,525); 
                     
                     $totalstr.=$str ;
                     } 
                      $arraystr=explode("<result xmlns:i=\"http://www.w3.org/2001/XMLSchema-instance\">",$totalstr);
                      $totalstr="";
                     $dom->loadXML("<result xmlns:i=\"http://www.w3.org/2001/XMLSchema-instance\">".$arraystr[1]);
                     

                        if (!$dom) 
                        {
                            echo 'Ошибка преобразования документа';
                             
                       } else
                      {
                       $s = simplexml_import_dom($dom);
                      }
                      print_r( $s );                     
                      fclose ($fp);
              }
              if (is_object($s))
              {
                $statusid=(string)$s->status->id;
                $token=$s->data->row->token; 
                  
              }else
              {
                  $statusid='1';
                  $token="";
              } 
         
          #-------------------------------------------------------------------------------------------------------------------
         
            if ($statusid=="0")
            {
              $fp = fsockopen ("srv.vivat-uae.net", 80, $errno, $errstr, 30); 
                    if (!$fp) 
                    {
                     #echo "$errstr ($errno)<br>\n";
                     
                    } 
                else 
                {
                    fputs ($fp, "GET /getprices?token={$token}&partno={$ItemCode}&plevelid=749&ptypeid=1&routeid=1&tarifid=15&depotid=-1&currid=1&withsubsts=1 HTTP/1.0\r\nHost:srv.vivat-uae.net\r\n\r\n Connection: Close\r\n\r\n");
                    $dom = new domDocument;
                    while (!feof($fp)) 
                    {
                        #echo fgets ($fp,525);
                        #$str=iconv('cp1251','utf8',fgets ($fp,525));
                         $str=fgets ($fp,525); 
                         
                         $totalstr.=$str ;
                     }
                       
                         $arraystr=explode("<result xmlns:i=\"http://www.w3.org/2001/XMLSchema-instance\">",$totalstr);
                        # echo   "<result xmlns:i=\"http://www.w3.org/2001/XMLSchema-instance\">".$arraystr[1];
                         $dom->loadXML("<result xmlns:i=\"http://www.w3.org/2001/XMLSchema-instance\">".$arraystr[1]);
                     

                        if (!$dom) 
                        {
                            echo 'Ошибка преобразования документа';
                             
                        } else
                        {
                          $s = simplexml_import_dom($dom);
                        }
                         
                      #                   
                      fclose ($fp);
                      
                      if (is_object($s))
                      {   var_dump( $s);
                           #print_r( $s->data->row );
                           #print_r ($s->data->row[2]->{'qty'});
                           #$arraytocheck=$s->data->{"row"};
                           #if (is_object($arraytocheck))
                           # print_r($arraytocheck) ;
                           if (is_object($s->data->row))
                           {    $i=0;
                               # $arrayanalogs["BrandName"] =(string)$s->data->row[0]->brand;
                                #$arrayanalogs["ItemCode"] =(string)$s->data->row[0]->partno;
                               # print_r($arrayanalogs);
                                foreach ($s->data->row as $rowobject)
                                { 
                                     
                                     if ((string) $rowobject->partno !=$ItemCode ) 
                                     {
                                         $arrayanalogs[]=$rowobject;
                                         
                                     } else
                                     {
                                       $arrayanalogs["BrandName"] =(string)$rowobject->brand;   
                                     } 
                                         #print_r( $arrayanalogs );
                                     if  (/*is_string($rowobject->qty) &&*/ (((string)$rowobject->qty)*1.00)>0 && (string) $rowobject->partno==$ItemCode)
                                     {   #var_dump( $rowobject );     
                                        $arrayrow[$i]["Quantity"]=((string) $rowobject->qty)*1.00;
                                        $arrayrow[$i]["Caption"]=(string) $rowobject->descrrus;
                                        $arrayrow[$i]["Brand"]=(string) $rowobject->brand;
                                        $arrayrow[$i]["ItemCode"]=(string) $rowobject->partno;
                                        if ( /*is_string($rowobject->{'weight'})  &&*/ (((string) $rowobject->weight)*1.00)>0   )
                                        {  
                                         $arrayrow[$i]["Weight"]= ((string) $rowobject->weight)*1.00 ;
                                        }else
                                        {
                                         $arrayrow[$i]["Weight"]='N';   
                                        }  
                                     }else
                                     {
                                         
                                          continue;
                                     }
                                      
                                   $i++ ;
                                }
                               # print_r( $arrayanalogs );
                                if (is_array($arrayanalogs))
                                {   
                                    if   ($arrayanalogs["BrandName"]!=""  )
                                    {
                                        $ii=0;
                                        foreach($arrayanalogs as $arritem=>$key)
                                        {   
                                            if ($arritem=="BrandName") {continue; }
                                            $AnalogArray[$ii]["BrandName1"] =$arrayanalogs["BrandName"];
                                            $AnalogArray[$ii]["ItemCode1"]=$ItemCode;
                                            $AnalogArray[$ii]["BrandName2"] =(string)$key->brand;
                                            $AnalogArray[$ii]["ItemCode2"]=(string)$key->partno;
                                            
                                         
                                         $ii++;   
                                        }
                                    }
                                }
                                print_r($AnalogArray);  
                                if (!is_array($arrayrow))
                                {
                                   
                                    #print_r($arrayrow); 
                                      $ArrayInfo["AnalogArray"]=$AnalogArray; 
                                     return $ArrayInfo;   
                                }
                                
                                $quantitycheck=0;
                                $weightcheck=0;
                                foreach ($arrayrow as $row=>$item)
                                {
                                #print_r ($item);
                                    if ($item['Quantity']>$quantitycheck)
                                    {
                                        $quantitycheck=$item['Quantity'];
                                    }
                                    
                                }
                                foreach ($arrayrow as $row=>$item)
                                {
                                    if ($item['Weight']!='N' && $item['Weight']>0 )
                                    {
                                        $weightcheck=$item['Weight'];
                                        break;
                                    }
                                }    
                               # print_r($arrayrow);
                                $ArrayInfo["weight"]=( $weightcheck!=0)?$weightcheck: $arrayrow[0]['Weight'];
                                $ArrayInfo["quantity"]= $quantitycheck;
                                $ArrayInfo['persentsupped']=100;
                                $ArrayInfo['Caption']=$arrayrow[0]['Caption'];
                                $ArrayInfo['Brand']= $arrayrow[0]['Brand'];
                                $ArrayInfo['ItemCode']= $arrayrow[0]['ItemCode'];
                                $ArrayInfo["AnalogArray"]=$AnalogArray; 
                                print_r($ArrayInfo);
                                return  $ArrayInfo;
                                
                           }
                          
                      } else 
                      {
                          return  $ArrayInfo;
                      }
                      
                } 
            }else 
              {
                  return  $ArrayInfo;
              }
                    
                       
     }
     
     
     
     function CheckCaption()
     {
       $sql="SELECT Caption FROM b_autodoc_items_m WHERE ItemCode='{$_GET['ItemCode']}'"  ;
       $DB=Search_ITG::manualConnect(); 
       $result=$DB->query($sql);
       if ($result->num_rows ==0) return false;
       $ArrayResult=$result->fetch_assoc();
       $ArrayStr=str_split($ArrayResult['Caption']);
       $StrLenth=Count($ArrayStr);
       $Count=0;
       foreach($ArrayStr as $Symbol)
       {
            if ( ord($Symbol)>127 || (ord($Symbol)>32 && ord($Symbol)<65  ))  
            {
               $Count++; 
            }
            
           
           
       }
       
       if ($Count>($StrLenth/3)) return true;
       else return false;
       
         
     }
     function CheckAndWriteAnalogs ($AnalogArray)
        {  print_r($AnalogArray);
     #1379215  -последний номер     1379215
       if (count($AnalogArray)>0)
       {   
            $DB=Search_ITG::manualConnect();
            foreach($AnalogArray as $item)
            {                 
               if ($item['ItemCode1']=='' || $item['ItemCode2']=='')   continue;
                $sql="SELECT  
               ( SELECT IF ( (SELECT IBLOCK_ELEMENT_ID FROM b_iblock_element_prop_s14 WHERE PROPERTY_72='{$item['BrandName1']}') IS NULL ,0, (SELECT IBLOCK_ELEMENT_ID FROM b_iblock_element_prop_s14 WHERE PROPERTY_72='{$item['BrandName1']}') )) AS BrandCode1,
               ( SELECT IF ( (SELECT IBLOCK_ELEMENT_ID FROM b_iblock_element_prop_s14 WHERE PROPERTY_72='{$item['BrandName2']}') IS NULL ,0, (SELECT IBLOCK_ELEMENT_ID FROM b_iblock_element_prop_s14 WHERE PROPERTY_72='{$item['BrandName2']}') )) AS BrandCode2 , 
               (SELECT  id FROM  b_autodoc_analogs_m  WHERE B1Code=BrandCode1 AND B2Code=BrandCode2 AND I1Code='{$item['ItemCode1']}' AND I2Code='{$item['ItemCode2']}' LIMIT 1)  AS ID,
               (SELECT  id FROM  b_autodoc_analogs_m  WHERE B1Code=BrandCode2 AND B2Code=BrandCode1 AND I1Code='{$item['ItemCode2']}' AND I2Code='{$item['ItemCode1']}' LIMIT 1)  AS ID2
               ";
               $result=$DB->query($sql); 
               $ArrayResult=$result->fetch_assoc();
               echo $sql; 
               #print_r($ArrayResult);  
               if ($ArrayResult['BrandCode1']!=0 && $ArrayResult['BrandCode2']!=0 && $ArrayResult['ID']=='' )
               {
                   
                   $sql="INSERT INTO b_autodoc_analogs_m (B1Code,I1Code,B2Code,I2Code) VALUES
                   ({$ArrayResult['BrandCode1']},'{$item['ItemCode1']}',{$ArrayResult['BrandCode2']},'{$item['ItemCode2']}')  ";
                   $result=$DB->query($sql);
                   echo $sql;
                   
                    
                   #var_dump($result); 
                   
               } elseif  ($ArrayResult['BrandCode1']!=0 && $ArrayResult['BrandCode2']!=0 && $ArrayResult['ID2']=='' ) 
               {
                   $sql="INSERT INTO b_autodoc_analogs_m (B1Code,I1Code,B2Code,I2Code) VALUES
                   ({$ArrayResult['BrandCode2']},'{$item['ItemCode2']}',{$ArrayResult['BrandCode1']},'{$item['ItemCode1']}')  ";
                   echo $sql;
                   $result=$DB->query($sql);
                   
               }
                
                
            }   
       } 
     }
     
     
     $check=1;
    if ($check==1)
    {
        $ArrayInfo=GetInfoFromVivat($_GET['ItemCode']) ; 
         $DB=manualConnect();
         if ($ArrayInfo["quantity"] >0)
         { 
             $sql="UPDATE `ITG_price_union` SET Quantity={$ArrayInfo["quantity"]} WHERE ItemCode='{$ArrayInfo["ItemCode"]}' AND RegionCode=2" ;
             $result=$DB->query($sql);
            #var_dump($DB);
           # echo $ArrayInfo["quantity"];  
         }
         if ($ArrayInfo["weight"]>0 && $ArrayInfo["weight"]!='N' )
         {
             $sql="UPDATE `b_autodoc_items_m` SET Weight={$ArrayInfo["weight"]} WHERE ItemCode='{$ArrayInfo["ItemCode"]}'" ;
             $result=$DB->query($sql);
             if ($DB->affected_rows<1)
             {
                 $sql="INSERT INTO ";
             } 
            # var_dump($DB);  
         }
         if ($ArrayInfo["Caption"]!="")
         {
              if (!CheckCaption() ) 
              {
               $sql="UPDATE `b_autodoc_items_m` SET Caption='{$ArrayInfo["Caption"]}' WHERE ItemCode='{$ArrayInfo["ItemCode"]}'" ;
               $result=$DB->query($sql);   
              }
         }
          #print_r($ArrayInfo);
         if (count($ArrayInfo["AnalogArray"])>0)
         {
              CheckAndWriteAnalogs($ArrayInfo["AnalogArray"]);
         }
    } 
    elseif ($check==0)
    { 
              #$_GET['ItemCode']  ='8156135301';
    $ArrayInfo=GetInfoFromVivat($_GET['ItemCode']) ; 
    $weight=($ArrayInfo['weight']=='N' || $ArrayInfo['weight']==0 )?'NULL':$ArrayInfo['weight']; 
    $supped=($ArrayInfo['persentsupped']=='N')?'NULL':$ArrayInfo['persentsupped'];
    if ($ArrayInfo['PartNameRus']=='')
    {
     
    $sql=" UPDATE  b_autodoc_items_m SET
    Weight=".$weight.", supped=".$supped."
    WHERE ItemCode='{$_GET['ItemCode']}'
    "; 
    }
    else
     {
        if (!CheckCaption() )
        {
            $sql=" UPDATE  b_autodoc_items_m SET
            Weight=".$weight.", supped=".$supped.",Caption='{$ArrayInfo['PartNameRus']}'
            WHERE ItemCode='{$_GET['ItemCode']}'
           ";             
        } else
        {
            $sql=" UPDATE  b_autodoc_items_m SET
            Weight=".$weight.", supped=".$supped."
            WHERE ItemCode='{$_GET['ItemCode']}'
           "; 
            
        }
      }
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    /*$sql="SELECT  b_autodoc_items_m WHERE ItemCode='{$_GET['ItemCode']}' ";
    #$DB=Search_ITG::manualConnect();
    
    try
    { 
    $DB=manualConnect(); 
    $result=$DB->query($sql);
    if ($DB->errno==0)
    {
    var_dump( $DB);
    }
    }catch (Exception $e)
    {
        echo 'Выброшено исключение: ',  $e->getMessage(), "\n";
    }
     print_r($result);
     print_r($ArrayInfo);
   
   
     #echo $sql;
     #echo $ $ArrayInfo['PartNameRus']; */
     
     
     
     
     
     
     
     
     
     
?>