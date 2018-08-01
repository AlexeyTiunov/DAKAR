<?php
  #require("/var/www/www/bitrix/components/itg/Search/Search_ITG4.php");
  error_reporting(1);
  set_time_limit(0);
    function AuthConnect()
    {
        $hostname ="genparts.com.ua/customer/account/loginPost/";
        $headers=array(
                'User-Agent' => "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/535.11 (KHTML, like Gecko) Chrome/17.0.963.47 Safari/535.11 MRCHROME",
                'Accept-Language' => "ru,en-us;q=0.7,en;q=0.3",
                'Accept-Encoding' => "gzip,deflate",
                'Accept-Charset' => "windows-1251,utf-8;q=0.7,*;q=0.7",
                'Keep-Alive' => '300',
                'Connection' => 'keep-alive', 
                'Accept'=>"text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8"            
                
            );
            #form_key=Bn0QU8sPC027N66u &login%5Busername%5D=shurkis%40ukr.net&login%5Bpassword%5D=JXDXLZOH&send=
         $ch = curl_init('http://'.$hostname.'/');
         #$ch = curl_init();
          #curl_setopt($ch,CURLOPT_URL,"http://".$hostname);
         curl_setopt($ch, CURLOPT_TIMEOUT, 600);
         curl_setopt ($ch, CURLOPT_HEADER, 1);
         curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
         curl_setopt ($ch, CURLOPT_REFERER, 'http://genparts.com.ua/customer/account/login/');
         curl_setopt ($ch, CURLOPT_POST, 1);
         #curl_setopt ($ch, CURLOPT_POSTFIELDS, 'form_key=Bn0QU8sPC027N66u&login[username]=shurkis@ukr.net&login[password]=JXDXLZOH');
         curl_setopt ($ch, CURLOPT_POSTFIELDS, 'form_key=Bn0QU8sPC027N66u&login[username]=service@parts.avtodok.com.ua&login[password]=251110');
         curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 ); 
         curl_exec ($ch);
         $result = curl_multi_getcontent ($ch);     
        # echo $result;
        # curl_close ($ch); 
      
        $ArrayResult=explode("frontend=",$result);
        #var_dump($ArrayResult);
        $ArrayCookieResult=explode(';',$ArrayResult[1]);
        #var_dump($ArrayCookieResult);
        $Cookie="frontend=".$ArrayCookieResult[0];
        #echo $Cookie;
        
       #--------------------------------
        
        #-----------------------------------------------------------------
        echo "Auth";
        return $Cookie;
    
    }
    function GetInfoOld($Cookie,$ItemCode)
    {
       $ArrayResult['ItemCode']=$ItemCode;      
       $hostname="genparts.com.ua/catalogsearch/result/?q={$ItemCode}";
       $ch = curl_init();
       $headers=array(
                        'Host'=>'http://b2b.genparts.com.ua/',
                        'User-Agent' => "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/535.11 (KHTML, like Gecko) Chrome/17.0.963.47 Safari/535.11 MRCHROME",  
                        'Accept-Language' => "ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4",
                        'Accept-Encoding' => "gzip,deflate,sdch",
                        'Accept-Charset' => "windows-1251,utf-8;q=0.7,*;q=0.3",          
                        'Connection' => 'keep-alive',            
                        'X-Requested-With'=> 'XMLHttpRequest',
                        'Accept'=>'*/*'
                        
                    );
         curl_setopt($ch,CURLOPT_URL,"http://".$hostname);
        curl_setopt($ch, CURLOPT_TIMEOUT, 600);
        curl_setopt ($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        #curl_setopt ($ch,CURLOPT_COOKIESESSION,true);
        curl_setopt ($ch,CURLOPT_COOKIE,$Cookie); 
        # curl_setopt ($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.3) Gecko/2008092417 Firefox/3.0.3');
        curl_setopt ($ch, CURLOPT_REFERER,"http://".$hostname);  
        curl_setopt($ch,CURLOPT_NOBODY,false);     
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        #curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
        $result=curl_exec ($ch); 
        
        # $ArrayString=preg_split("/\n/",$result);
       #  foreach ($ArrayString as $string)
        # {
         #    echo DeleteHTMLTegsFromString($string); 
       #  }
       $i_result_pattern="/(.*)(<div class=\"product-info\">)(.*?)(\<div class=\"actions\"\>)(.*)/ms" ;
      # $i_result=preg_replace($i_result_pattern,"$1-1--------$2-2----------$3-3-----------$4-4----------$5-5----------",$result);
      $i_result=preg_replace($i_result_pattern,"$3",$result); 
       
     # var_dump($i_result);
     # exit();
      $caption_pattern="/(.*)(<h2.*?>)(<a.*?>)(.*?)(<\/a>)(<\/h2>)(.*)/ms";
      $caption=preg_replace($caption_pattern,"$4",$i_result);
      $caption=preg_replace("/[^\s\w\W\D\d]/","",$caption);
      #var_dump(mb_detect_encoding($caption_));
      #$caption=iconv("windows-1251","UTF-8",$caption);
       
     # var_dump($caption);
      
      $price_pattern="/(.*?)(<span class=\"price\">)(.*?)(Гр\.)(<\/span>)(.*)/ms";
      $price=preg_replace($price_pattern,"$3",$i_result);
      $price=str_replace(",",".",$price);      
      $price=floatval(preg_replace("/[^0-9\,\.]/","",$price));
      var_dump($price);
       
      return array(
        'price'=>$price,
        'caption'=>$caption
      );  
                   
                     
    }
     function GetInfo($Cookie,$ItemCode)
    {
       $ArrayResult['ItemCode']=$ItemCode;     
      $hostname= "genparts.com.ua/admin_genpartssearchaartikul/index/index/article/{$ItemCode}/vendorcode/1/sortby/price"; 
       //"genparts.com.ua/catalogsearch/result/?q= {$ItemCode} ";
       $ch = curl_init();
       $headers=array(
                        'Host'=>'http://b2b.genparts.com.ua/',
                        'User-Agent' => "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/535.11 (KHTML, like Gecko) Chrome/17.0.963.47 Safari/535.11 MRCHROME",  
                        'Accept-Language' => "ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4",
                        'Accept-Encoding' => "gzip,deflate,sdch",
                        'Accept-Charset' => "windows-1251,utf-8;q=0.7,*;q=0.3",          
                        'Connection' => 'keep-alive',            
                        'X-Requested-With'=> 'XMLHttpRequest',
                        'Accept'=>'*/*'
                        
                    );
         curl_setopt($ch,CURLOPT_URL,"http://".$hostname);
        curl_setopt($ch, CURLOPT_TIMEOUT, 600);
        curl_setopt ($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        #curl_setopt ($ch,CURLOPT_COOKIESESSION,true);
        curl_setopt ($ch,CURLOPT_COOKIE,$Cookie); 
        # curl_setopt ($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.3) Gecko/2008092417 Firefox/3.0.3');
        curl_setopt ($ch, CURLOPT_REFERER,"http://".$hostname);  
        curl_setopt($ch,CURLOPT_NOBODY,false);     
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        #curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
        $result=curl_exec ($ch); 
        
       $i_result_pattern="/(.*)(<div id=\"wplg-search-by-artikul-results-data\".*?>)(.*?)(<\/table>)(.*)/ms";
       $i_result=preg_replace($i_result_pattern,"$3",$result);       
       $h3_parten="/(.*?)(<h3>)(.*?)(<\/h3>)(.*)/ms";
       $i_h3=preg_replace($h3_parten,"$3",$i_result);
        # var_dump($i_result);
       
       if (!preg_match("/^Результаты поиска по запросу:.*$/",$i_h3))
       {
        return array(
        'price'=>"",
        'caption'=>"<div>ERROR</div>");
       } 
        $table_body_patten="/(.*)(<tbody.*?>)(.*?)(<\/tbody>)(.*)/ms";
        $table_body=preg_replace($table_body_patten,"$3",$i_result);
       # var_dump($table_body);
        
        $span_class_patten="/(.*)(<span class=\"price\">)(.*?)(<\/span>)(.*)/ms";
        $price=preg_replace("/\,/",".",preg_replace("/[^0-9\,]/","",preg_replace($span_class_patten,"$3",$table_body)));
        
        #var_dump(floatval($price)); 
        
        
        $caption_patten="/(.*)(<a.*?>)(.*?)(<\/a>)(.*)/ms";
        $caption=preg_replace($caption_patten,"$3",$i_result);
          var_dump($price); 
         var_dump($caption); 
          return array(
        'price'=>floatval($price),
        'caption'=>$caption
      );  
        
     /* 
       $i_result_pattern="/(.*)(<div class=\"product-info\">)(.*?)(\<div class=\"actions\"\>)(.*)/ms" ;
      
      $i_result=preg_replace($i_result_pattern,"$3",$result); 
       
    
      $caption_pattern="/(.*)(<h2.*?>)(<a.*?>)(.*?)(<\/a>)(<\/h2>)(.*)/ms";
      $caption=preg_replace($caption_pattern,"$4",$i_result);
      $caption=preg_replace("/[^\s\w\W\D\d]/","",$caption);
      
      
      $price_pattern="/(.*?)(<span class=\"price\">)(.*?)(Гр\.)(<\/span>)(.*)/ms";
      $price=preg_replace($price_pattern,"$3",$i_result);
      $price=str_replace(",",".",$price);      
      $price=floatval(preg_replace("/[^0-9\,\.]/","",$price));
      var_dump($price);
       
      return array(
        'price'=>$price,
        'caption'=>$caption
      );  
                   
           */          
    }
    
   
    function DeleteHTMLTegsFromString($string)
    {
           $string= preg_replace("/<.*?>/","",$string);
           return $string;
           
    }
    function WriteToBase($DB,$itemCode,$caption,$quantity,$price)
    {
            #$DB=Search_ITG::manualConnect();
            $LastUpdate=date("Y-m-d H:i:s");  
                   $sql="INSERT INTO b_autodoc_prices_supp_ROV (BrandCode,ItemCode,SuppCode,Caption,Quantity,Price,Currency,LastUpdate)
                    VALUES (916,'{$itemCode}',367216,'{$caption}',{$quantity},{$price},'UAH','{$LastUpdate}')
                    ON DUPLICATE KEY UPDATE 
                    Price={$price},
                    LastUpdate='{$LastUpdate}',
                    Caption='{$caption}',
                    Currency='UAH'                        
                        ";
                       # echo $sql;
              $DB->Query($sql); 
                     if ($DB->errno>0)
                     {
                         $action=false;
                     } elseif ($DB->affected_rows>0)
                     {
                          $action=true;
                     }  else
                     {
                           $action=true; 
                     }                  
          return $action;        
        
    }
     function manualConnect()
    {
        $port=31006;
        $DB = new mysqli("localhost","bitrix","a251d851","bitrix",$port);
        $DB->set_charset("utf8");
              $DB->query("SET NAMES 'utf8'");
        return $DB;
    } 
    
    $a=1;
    if ($a==1)
    {
        
    
     header("Content-Type: text/html; charset=UTF-8");   
     echo "Auth start\n";     
     $Cookie= AuthConnect();  
     echo "\n".$Cookie;
     $DB=manualConnect(); 
     #$LastUpdate=date("Y-m-d H:i:s");   
    # $sql="SELECT ItemCode FROM `b_autodoc_prices_suppUA` WHERE `BrandCode`=916 AND SuppCode=39934778";    #39934778  #367216
     $sql="SELECT * FROM `b_autodoc_items_m`WHERE `BrandCode`=916"; 
     #$sql="SELECT ItemCode FROM `b_autodoc_prices_suppUA` WHERE `BrandCode`=916 AND SuppCode=367216";
      $resultDB=$DB->Query($sql);
      $counter=1;
      $errorcounter=0;
      $totalcounter=1;
      while ($ArrayResult=$resultDB->fetch_assoc())
      {
        /*  if ($totalcounter<360420)
          {
              echo  "".$totalcounter." \n";
              $totalcounter+=1;
              continue;
              
          }*/
          if ($errorcounter==100 || $counter==1000)
          {
              echo "Auth start\n";     
             $Cookie= AuthConnect();  
             echo "\n".$Cookie;
           //  $DB=manualConnect(); 
             $errorcounter=0;
             $counter=1;
              
          }
          
          echo "\n".$ArrayResult['ItemCode']."--\n";
          $infoArray=GetInfo($Cookie,$ArrayResult['ItemCode']);
          
         
         
        
          
          
          $action=WriteToBase($DB,$ArrayResult['ItemCode'],$infoArray['caption'],1,$infoArray['price']);
          if ($action==true)
          {
              echo "DONE-{$counter}  \n";
              $counter+=1;
              $totalcounter+=1;
          }else
          {
               echo "ERROR-{$errorcounter} \n"; 
               $errorcounter+=1;
               $totalcounter+=1;
          }
          echo  "".$totalcounter." \n";
          #echo $action;
          #echo $counter;
          
          #exit;
           
      }
    }  
     
    # echo  header("Content-Type: text/html; charset=UTF-8");   
   #  GetInfo($Cookie,"5253606190");  
       
       
  
  
  
  
  
  
    $a=0;
  if ($a==1)
  {   
   #-------------------------------- 
   echo "Auth start\n";
   $Cookie= AuthConnect(); 
   echo "\n".$Cookie;
   $DB=Search_ITG::manualConnect();
   #$sql="SELECT * FROM `b_autodoc_items_m`WHERE `BrandCode` =916 AND `ItemCode` REGEXP '^044.*'";
    $sql="SELECT * FROM `b_autodoc_items_m`WHERE `BrandCode`=916";
  # $sql="SELECT * FROM `b_autodoc_items_m`WHERE `BrandCode` =916 AND `ItemCode` REGEXP '^521596A914.*'"; 
 #  $sql="SELECT ItemCode FROM `b_autodoc_prices_suppUA` WHERE `BrandCode`=916 AND SuppCode=367216";
    $resultDB=$DB->Query($sql);
    #$importfile=fopen("/var/www/www/priceld/Rovenko49.csv",'w');
    $ch = curl_init();
    #$ch = curl_init(); 
    while ($ArrayResult=$resultDB->fetch_assoc())
  {
      echo $ArrayResult['ItemCode']."\n";
                $hostname = "b2b.genparts.com.ua/wbtob/products/searching/index.php?vendorcode={$ArrayResult['ItemCode']}&_=1412319522242";
               #$hostname='b2b.genparts.com.ua/wbtob/logged/?vendorecode=0446560230&_=1412319522239';   

                 $headers=array(
                        'Host'=>'http://b2b.genparts.com.ua/',
                        'User-Agent' => "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/535.11 (KHTML, like Gecko) Chrome/17.0.963.47 Safari/535.11 MRCHROME",  
                        'Accept-Language' => "ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4",
                        'Accept-Encoding' => "gzip,deflate,sdch",
                        'Accept-Charset' => "windows-1251,utf-8;q=0.7,*;q=0.3",          
                        'Connection' => 'keep-alive',            
                        'X-Requested-With'=> 'XMLHttpRequest',
                        'Accept'=>'*/*'
                        
                    );
               #$ch = curl_init();
                #curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY) ; 
               # curl_setopt($ch, CURLOPT_USERPWD, "shurkis@ukr.net:JXDXLZOH");
                curl_setopt($ch,CURLOPT_URL,"http://".$hostname);
                curl_setopt($ch, CURLOPT_TIMEOUT, 600);
                curl_setopt ($ch, CURLOPT_HEADER, 1);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                #curl_setopt ($ch,CURLOPT_COOKIESESSION,true);
                curl_setopt ($ch,CURLOPT_COOKIE,$Cookie); 
               # curl_setopt ($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.3) Gecko/2008092417 Firefox/3.0.3');
                curl_setopt ($ch, CURLOPT_REFERER, 'http://b2b.genparts.com.ua/wbtob/');
                curl_setopt($ch,CURLOPT_NOBODY,false);     
                curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
                #curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
                $result=curl_exec ($ch);
                 #$result = curl_multi_getcontent ($ch);     
                 #echo $result;
                 #curl_close ($ch); 
                 
               $ArrayString=preg_split("/\n/",$result);
               #var_dump($ArrayString);
               $CountArrayString=count($ArrayString);
               $ArrayStringReady=Array();
               for ($i=0;$i<$CountArrayString-1;$i++)  
               {
                   $ArrayStringReady=Array();
                   $stroka=trim($ArrayString[$i]);
                   if ($storka=="HTTP/1.1 302 Moved Temporarily")
                   {
                       $Cookie= AuthConnect();
                       echo  $ArrayResult['ItemCode']."///Unloaded\n";
                       break(1);
                   }
                   elseif ($stroka=="<td>TOYOTA</td>")       
                   {
                       $StrokaBrand=DeleteHTMLTegsFromString (trim($ArrayString[$i]));
                       $StrokaItemCode=DeleteHTMLTegsFromString(trim($ArrayString[$i+1]));
                       $StrokaPrice=trim(preg_replace("/[^0-9,\,]/",'',DeleteHTMLTegsFromString(trim($ArrayString[$i+3]))));
                       $StrokaCaption=DeleteHTMLTegsFromString(trim($ArrayString[$i+2]));
                       $QuantityArray=explode("yes.png",$ArrayString[$i+4]); 
                       #var_dump($QuantityArray);
                       $StrokaQuantity=(count($QuantityArray)>1)?"1":"0";
                       $i++;
                      # $ArrayStringReady[$StrokaItemCode]['Price']=$StrokaPrice;
                       #$ArrayStringReady[$StrokaItemCode]['Caption']=$StrokaCaption; 
                       $ArrayStringReady[]=$StrokaBrand;
                       $ArrayStringReady[]=$StrokaItemCode;
                       $ArrayStringReady[]=$StrokaCaption;
                       $ArrayStringReady[]=$StrokaQuantity;
                       $ArrayStringReady[]=$StrokaPrice;
                       $StrokaPricePoint=str_replace(",",".",$StrokaPrice);
                       $LastUpdate=date("Y-m-d H:i:s");

                      # $ArrayStringReady[]="\n";
                       #var_dump($ArrayStringReady); 
                        #echo $ArrayResult['ItemCode']."//// Load\n"; 
                        $sql="INSERT INTO b_autodoc_prices_suppUA (BrandCode,ItemCode,SuppCode,Caption,Quantity,Price,Currency,LastUpdate)
                        VALUES (916,'{$StrokaItemCode}',367216,'{$StrokaCaption}',{$StrokaQuantity},{$StrokaPricePoint},'UAH','{$LastUpdate}')
                        ON DUPLICATE KEY UPDATE 
                        Price={$StrokaPricePoint},
                        LastUpdate='{$LastUpdate}',
                        Currency='UAH'                        
                        ";
                        if ($StrokaQuantity=="1")
                        {
                            #echo $sql."\n";
                           
                            $DB->Query($sql);
                            echo $ArrayResult['ItemCode']."//// Load\n";
                        } 
                       #fputcsv($importfile, $ArrayStringReady,',','"'); 
                       
                   }else
                   {
                      # echo"Blank\n";
                   }
                   
               }
            }
            curl_close ($ch);   
  }       
            
 
?>