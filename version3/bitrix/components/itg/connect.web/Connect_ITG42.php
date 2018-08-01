<?php
require_once $_SERVER["DOCUMENT_ROOT"]."/bitrix/components/itg/Search/Search_ITG2.php";

class Connect_ITG
{
    public static  $log = array(
                            'tehnomir'    =>array('pass'        =>'251110',
                                                'log'        =>'Zolotov',
                                                'url'        =>'http://tehnomir.com.ua/ws/soap.wsdl',
                                                'encoding'    =>'cp1251',
                                                'region'    =>'UW1'),
                            'autopalma'    =>array('pass'        =>'3cgz8k',
                                                'log'        =>'Zolotov',
                                                'url'        =>'http://twinauto.net/wsdl/server.php?wsdl',
                                                'encoding'    =>'cp1251',
                                                'region'    =>'UW2'),
                             'automir'   =>array( 'pass'     =>'336169',
                                                  'log'     =>'DOK',
                                                  'url'     =>'http://avto-mir.od.ua/wsdl/server.php?wsdl',
                                                  'encoding' =>'cp1251',
                                                  'region'   =>'UW3'),
                              'JapanAvtoparts'=>array(   'pass'=>'251110' ,
                                                         'log'=>'DOK' ,
                                                         'url'=>'',
                                                         'encoding'    =>'cp1251',
                                                         'region'    => 'JP1' 
                              )                                            
                    
  
 
  
  );
    private $params = array();
    private $client;
    private $result;
         private $clientmir;
    private $sql = array();
    
    function __construct($params)
    {
        $this->params = $params;
        foreach (self::$log as $name=>$propsClient)
        {
            if ($name=='JapanAvtoparts')
            {
                $this->params['client'] = $name;
                  #$this->authorization(); 
            #$this->prepareResult();
                
            } 
             elseif ($name=='automir')
             {
                 
             }  
            elseif($name=='autopalma')                     
            {
                error_reporting(0);
                set_time_limit(15);
                $this->params['client'] = $name;
           $this->client = @new SoapClient(self::$log[$this->params['client']]['url'], array('encoding'=>self::$log[$this->params['client']]['encoding']));


           $this->authorization(); 
            $this->prepareResult();                       
                
            }
            else           
            {
            $this->params['client'] = $name;
            $this->client = @new SoapClient(self::$log[$this->params['client']]['url'], array('encoding'=>self::$log[$this->params['client']]['encoding']));

            
            $this->authorization(); 
            $this->prepareResult();
            }
        }
                      
    }
    public function getResult()
    {
        return $this->resultToReturn;
    }
    private function authorization()
    {
        $log = self::$log[$this->params['client']]['log'];
        $pass = self::$log[$this->params['client']]['pass'];
        $partnumber = $this->params['article'];
                
        switch ($this->params['client'])
        {          case 'automir':
 // $this->clientmir = @new SoapClient("http://avto-mir.od.ua/wsdl/server.php?wsdl", array('encoding'=>'cp1251'));
                          $Login = $log;
                          $Passwd = $pass;
                          $OemCode = $partnumber ;
                           $MakeId =0;
                          $UserParam = array('login'=>$Login,'passwd'=>$Passwd);
                          try
                          {
                         # $result =  $this->client->getPartsPrice($OemCode,$MakeId,$UserParam); 
                          } catch (Exception $e) 
                        {
                            #echo 'Выброшено исключение: ',  $e->getMessage(), "\n";
                       }
                            break;  
            case 'tehnomir':

                $brand = ($this->params['brand'] != '')?$this->params['brand']:'';
                try
                {
                $result = $this->client->GetPrice($partnumber, $brand, $log, $pass); 
                }
                 catch (Exception $e) 
                        {
                          #  echo 'Выброшено исключение: ',  $e->getMessage(), "\n";
                       }
                break;
                    case 'autopalma':
                $userParam = array('login'=>$log,'passwd'=>$pass);
                try
                {
                #$result = $this->client->getPartsPrice($partnumber,$userParam);
                
                $result = $this->client->getPartsPrice($partnumber,$log,$pass,'korea','notfull');  
                }
                catch (Exception $e) 
                        {
                           #echo 'Выброшено исключение: ',  $e->getMessage(), "\n";
                       }
                break;
            case'JapanAvtoparts':
                error_reporting(0); 
                 $fp = fsockopen ("jap.com.ua", 80, $errno, $errstr, 30); 
                 if (!$fp) {
                 # echo "$errstr ($errno)<br>\n";
                  } 
                 else {
                   fputs ($fp, "GET /export/get_detail.php?act=GetPrice&usr_login=DOK&usr_passwd=251110&Number={$partnumber} HTTP/1.0\r\nHost:jap.com.ua \r\n\r\n Connection: Close\r\n\r\n");
                   
                   # }
                    $dom = new domDocument;
                    while (!feof($fp)) {
                       #echo fgets ($fp,525);
                    $str=iconv  ('cp1251','utf8',fgets ($fp,525));
                    $dom->loadXML($str);
                    if (!$dom) {
                      echo 'Ошибка преобразования документа';
         
                       } else
                          {
                        $s = simplexml_import_dom($dom);
                         }
                     # print_r( $s );
                      #echo $str;
                     #echo iconv  ('utf8','cp1251', $s->Detail[0]->Name);

                      }
                      #echo  $s->Detail[0]->Brand;                      
                      
                      $res = array('BrandJ'=>(string)$s->Detail[0]->Brand,
                                  'Number'=>(string)$s->Detail[0]->Number,
                                  'Name'=>(string)$s->Detail[0]->Name,
                                  'Price'=>(string)$s->Detail[0]->Price,
                                  'QuantityJ'=>(string)$s->Detail[0]->Quantity,
                                  'Currency'=>(string)$s->Detail[0]->Currency
                             ); 
                       # print_r( $res );
                       $res1[]=$res;   
                     # $result=$res1;
                 }
                 break; 
        
        }
                      
                      
                      
        $this->result = $result;
        #echo "-------------------<br /><pre>";
        #print_r($this->result);
        #echo "</pre>";
    }
    private function prepareResult()
    {
        $products = array();
        $DB = Search_ITG::manualConnect();
        #$DB=$_SESSION['DBB'] ;
        if (count($this->result) == 0) return array();
                # echo "<pre>";
        #print_r($this->result);
        #echo "</pre>";
        foreach ($this->result as $key=>$product)
        {
            //if ((isset($product['DeliveryTime']) && intval($product['DeliveryTime']) != 1) || (isset($product['Quantity']) && intval($product['Quantity']) == 0)) continue;
           //if (isset($product['Quantity']) && intval($product['Quantity']) == 0) continue; 
              //if ((isset($product["SupplierCode"]) && iconv('cp1251','utf8',$product["SupplierCode"])!="usaf") ||(isset($product["SupplierCode"]) && iconv('cp1251','utf8',$product["SupplierCode"])!="klod") )continue;  
               //if ($product["SupplierCode"]!="USAF" || $product["SupplierCode"]!="KLOD") continue;
            //if ((isset($product['Quantity']) && intval($product['Quantity']) == 0)||(isset($product["SupplierCode"]) && iconv  ('utf8','cp1251',$product["SupplierCode"])!="KLOD" && iconv('utf8','cp1251',$product["SupplierCode"])!="USAF") )
                 //continue;
if ((isset($product['Quantity']) && intval($product['Quantity']) == 1000)||(isset($product["SupplierCode"])  && $product["SupplierCode"]!="KLOD"  && $product["SupplierCode"]!="STOK" ) ) continue;     //TechnoMir
                                                                                                                                              
           
           #if (isset($product['Dostavka']) && (intval($product['Dostavka']) != 2 || (intval($product['Qty1']) == 0 && intval($product['Qty2']) == 0 && intval($product['Qty3'] == 0)))) continue;
            if ((isset($product['Sklad']) && $product['Sklad']!="KOREA:TWIN")/*|| (isset($producr['Kvo'])&& intval($producr['Kvo'])=='0' ) */ ) continue;        //Avtopalma 
            if (isset($product['Sklad']) && $product['Sklad']=="KOREA:TWIN" && $product['Price']==0 )   continue;        //Avtopalma  
           # if (isset($product['Kvo'])&& $product['Kvo']=='0')  continue;    //Avtopalma
            if (  ( isset($product['Postavwik'])&& $product['Postavwik'] != 'Ukraine - 0d.')  || (isset($product['Available']) && intval($product['Available'])==0) ) continue; //Avtomir  
             if (isset($product['QuantityJ']) && intval($product['QuantityJ']) == 0 ) continue;     //JapanAvtoparts
            switch ($this->params['client'])
            {
                case 'JapanAvtoparts':
             #  $product['CAPTION'] = iconv ('cp1251','utf8',$product['Name']);
                 $product['CAPTION'] = $product['Name'];
                 #product['CAPTION'] =  $product['Name'];
                $product['ICODE']=$product['Number'] ;
                $product['DELIVERY']=2;  
                $product['REGIONR'] = 'УКРАИНА';
                $product['Brand']=$product['BrandJ'];
                $product['QuantityS'] =intval($product['QuantityJ']);
                  $this->prepareSql();
            #echo $this->sql['partColumnRegion'];
            $sqlQuery = $DB->query($this->sql['partColumnRegion']);
            $sqlRes = $sqlQuery->fetch_assoc();
               $product['RC']=$sqlRes['RegionColumn'];//
            $this->params['propertyColumn'] = "PROPERTY_".$sqlRes['RegionColumn'];//формируем коэффициенты для пересчета цен
            $this->prepareSql();
            #echo $this->sql['suppliers'];
            $sqlQuery = $DB->query($this->sql['suppliers']);
            $sqlRes = $sqlQuery->fetch_assoc();
            $product['PercentSupp']=$sqlRes['PercentSupplied'] ;  
            $product['KP']  =$sqlRes['koefPrice'];
            $product['PRICEREGION'] = $sqlRes['koefPrice']*intval($product['Price']) ;
            $product['CURRREGION'] = $sqlRes['CurrencyCode'];
            $this->params['CURRREGION'] = $product['CURRREGION'];
            $this->prepareSql();
            #echo $this->sql['rate'];
            $sqlQuery = $DB->query($this->sql['rate']);
            $sqlRes = $sqlQuery->fetch_assoc();
            $product['PRICEREGIONINCURRENCY'] = $product['PRICEREGION']*$sqlRes['Rate'];
            $product['CS']="#eed5d5" ; 
            $product['REGION'] = self::$log[$this->params['client']]['region'];  
                
             break;   
                 
                 case 'automir':
                  $product['CAPTION'] = iconv ('cp1251','utf8',$product['PartNameEng']);
                     #echo "<pre>";
        #print_r($product['CAPTION']);
        #echo "</pre>";
                  $product['ICODE']=$product['DetailNum'] ;
                  $product['DELIVERY']=2;
                  $product['REGIONR'] = 'УКРАИНА';
                  $product['Brand']=$product['MakeName'];
                  $product['QuantityS']= intval($product['Available']);
                  $product['Weight'] =$producr['WeightGr']  /1000;
                   $this->prepareSql();
            #echo $this->sql['partColumnRegion'];
            $sqlQuery = $DB->query($this->sql['partColumnRegion']);
            $sqlRes = $sqlQuery->fetch_assoc();
            $this->params['propertyColumn'] = "PROPERTY_".$sqlRes['RegionColumn'];//формируем коэффициенты для пересчета цен
            $this->prepareSql();
            #echo $this->sql['suppliers'];
            $sqlQuery = $DB->query($this->sql['suppliers']);
            $sqlRes = $sqlQuery->fetch_assoc();
            $product['PercentSupp']=$sqlRes['PercentSupplied'] ;  
            $product['PRICEREGION'] = $sqlRes['koefPrice']*$product['Price'];
            $product['CURRREGION'] = $sqlRes['CurrencyCode'];
            $this->params['CURRREGION'] = $product['CURRREGION'];
            $this->prepareSql();
            #echo $this->sql['rate'];
            $sqlQuery = $DB->query($this->sql['rate']);
            $sqlRes = $sqlQuery->fetch_assoc();
            $product['PRICEREGIONINCURRENCY'] = $product['PRICEREGION']*$sqlRes['Rate'];
            $product['CS']="#eed5d5" ; 
            $product['REGION'] = self::$log[$this->params['client']]['region'];     
                 break;
                
                case 'tehnomir':
                    //echo intval($product['DeliveryTime'])."<br />";
                    //echo intval($product['Quantity'])."<br />";
                    #if (intval($product['DeliveryTime']) != 1 || intval($product['Quantity']) == 0) continue 2;
                    #echo "-------------------<br /><pre>";
                    #print_r($product['Name']);
                    #echo "</pre>";
                    $this->InfoFromTehnomirToDBase($product,$DB);
                    if  ($product['Brand']== "MERCEDES BENZ")$product['Brand']="MERCEDES-BENZ" ;
                    
                    $product['CAPTION'] = iconv('cp1251','utf8',$product['Name']);
                     #$product['CAPTION'] =$product['Name']; 
                    # echo "-------------------<br /><pre>";
                      #print_r($product['Name']);
                      #echo "</pre>";
                    $product['QuantityS'] =((intval($product['Quantity'])==0 &&$product['SupplierCode']=='USAF')||(intval($product['Quantity'])==0 &&$product['SupplierCode']=='USAM') )?'>10':$product['Quantity']; 
                    $product['ICODE'] = $product['Number'];
                    $product['DELIVERY'] = ($product['SupplierCode']=='USAF' or $product['SupplierCode']=='USAM' )? '14':$product['DeliveryTime'] + 1;/*$product['DeliveryTime'] + 1*/;
                    $product['REGIONR']=($product['SupplierCode']=='USAF' or $product['SupplierCode']=='USAM' ) ? 'USA'  :'tehnomir' ;
                   // $product['PRICEREGIONINCURRENCY']=$product['Price']-($product['Price']*0.1);
            $this->prepareSql();
            #echo $this->sql['partColumnRegion'];
            $sqlQuery = $DB->query($this->sql['partColumnRegion']);
            $sqlRes = $sqlQuery->fetch_assoc();
            $this->params['propertyColumn'] = "PROPERTY_".$sqlRes['RegionColumn'];//формируем коэффициенты для пересчета цен
            $this->prepareSql();
            #echo $this->sql['suppliers'];
            $sqlQuery = $DB->query($this->sql['suppliers']);
            $sqlRes = $sqlQuery->fetch_assoc();
            $sqlQueryR = $DB->query($this->sql['koef']);
             $sqlResR = $sqlQueryR->fetch_assoc();
            $product['PRICEREGIONP'] = ($product['SupplierCode']=='USAF' or $product['SupplierCode']=='USAM') ?$product['Price']-($product['Price']*0.03): /*$sqlRes['koefPrice']**/$product;
             $product['PRICEREGION']= ($product['SupplierCode']=='USAF' or $product['SupplierCode']=='USAM')?$product['PRICEREGIONP']*$sqlResR['COEF'] : $sqlRes['koefPrice']*$product['Price'];
            $product['CURRREGION'] = $sqlRes['CurrencyCode'];
            $product['PercentSupp']=$sqlRes['PercentSupplied'] ;
            $this->params['CURRREGION'] = $product['CURRREGION'];
            $this->prepareSql();
            #echo $this->sql['rate'];
            $sqlQuery = $DB->query($this->sql['rate']);
            $sqlRes = $sqlQuery->fetch_assoc();
            $product['PRICEREGIONINCURRENCY'] =($product['SupplierCode']=='USAF' or $product['SupplierCode']=='USAM') ?$product['PRICEREGION'] : $product['PRICEREGION']*$sqlRes['Rate'];
             $product['CS']=($product['SupplierCode']!='USAF' and $product['SupplierCode']!='USAM')?'#eed5d5':'#e3e3e3' ;      
             $product['REGION'] =($product['SupplierCode']=='USAF' or $product['SupplierCode']=='USAM') ?'USA': self::$log[$this->params['client']]['region'];       
                    
                    //$product['REGION'] = $this->log[$this->params['client']]['region'];
                    break;
             case 'autopalma':
                    //echo intval($product['Dostavka'])."<br />";
                    //echo intval($product['Qty1'])."<br />";
                    #if (intval($product['Dostavka']) != 2 || (intval($product['Qty1']) == 0 && intval($product['Qty2']) == 0 && intval($product['Qty3'] == 0))) continue;
                    //echo "-------------------<br /><pre>";
                    //print_r($product);
                    //echo "</pre>";
                   /* 
                    $product['CAPTION'] = $product['Name2'];
                    $product['ICODE'] = $product['Name'];
                    $product['DELIVERY'] = $product['Dostavka'];
                    $product['REGIONR'] = 'УКРАИНА';  */
                   if  (strtolower($product['Brand'])=="hyundai/kia")
                    {
                       $product['Brand']="HYUNDAI"; 
                    }elseif (preg_replace("/[^A-Za-z0-9]*/i","",$product['Brand'])=="GeneralMotors") 
                    {
                      $product['Brand']="GM" ; 
                    }
                    $this->prepareSql();
                    $sqlQuery = $DB->query($this->sql['NAMESEARCH']);
                    $sqlRes = $sqlQuery->fetch_assoc();
                    $product['CAPTION']=(!isset ($sqlRes['Caption']))?"":$sqlRes['Caption']; 
                    #$product['CAPTION'] = iconv('cp1251','utf8',$product['Description']);
                    $product['ICODE'] = $product['Oem'];
                    $product['DELIVERY'] = 90;
                    $product['REGIONR'] = 'KOREA';  
                    $product['QuantityS']=$product['Kvo'];
            $this->prepareSql();
            #echo $this->sql['partColumnRegion'];
            $sqlQuery = $DB->query($this->sql['partColumnRegion']);
            $sqlRes = $sqlQuery->fetch_assoc();
            $this->params['propertyColumn'] = "PROPERTY_".$sqlRes['RegionColumn'];//формируем коэффициенты для пересчета цен
            $this->prepareSql();
            #echo $this->sql['suppliers'];
            $sqlQuery = $DB->query($this->sql['suppliers']);
            $sqlRes = $sqlQuery->fetch_assoc();
            $product['PercentSupp']=$sqlRes['PercentSupplied'] ;
            $this->prepareSql();
            $sqlQueryKoef = $DB->query($this->sql['koefkor']);
            $sqlResKoef = $sqlQueryKoef->fetch_assoc();  
            $product['PRICEREGION'] =($product['Price']*1.5)*$sqlResKoef['COEFKOR'] /* $sqlRes['koefPrice']*$product['Price']*/;
            $product['CURRREGION'] = $sqlRes['CurrencyCode'];
            $this->params['CURRREGION'] = $product['CURRREGION']; 
            $this->prepareSql();
            #echo $this->sql['rate'];
            $sqlQuery = $DB->query($this->sql['rate']);
            $sqlRes = $sqlQuery->fetch_assoc();
            $product['PRICEREGIONINCURRENCY'] = $product['PRICEREGION']*$sqlRes['Rate'];
            $product['CS']="#e3e3e3" ;
            #$product['REGION'] = self::$log[$this->params['client']]['region'];
             $product['REGION']="KOR";     
                    break;
                   
         }                         
            //$product['REGION'] = self::$log[$this->params['client']]['region'];         
            $products[] = $product;
                         
    }
        //unset($this->result);
        $this->resultToReturn[] = $products;
                # echo "-------------------<br /><pre>";
        #print_r($products);
        #echo "</pre>";

    }
    private function prepareSql()
    {
                                                                                                                                        $this->sql['partColumnRegion'] = "SELECT `RegionColumn` FROM  `b_autodoc_wh_price_types_m` 
                                                WHERE `Code`=(SELECT IF((SELECT PriceColumn_1C FROM b_user WHERE ID_1C='{$this->params['user']}' LIMIT 1)=0,10,(SELECT PriceColumn_1C FROM b_user WHERE ID_1C='{$this->params['user']}' LIMIT 1))) LIMIT 1";
        
        $this->sql['suppliers'] = "SELECT ".$this->params['propertyColumn']." AS koefPrice, `PROPERTY_189` AS CurrencyCode ,`PercentSupplied`  AS PercentSupplied  FROM `b_iblock_element_prop_s17` 
                                                                WHERE `PROPERTY_93`='".self::$log[$this->params['client']]['region']."'";
        $this->sql['rate'] = "SELECT ITG_currency_rate_ua('{$this->params['CURRREGION']}','{$this->params['currency']}') AS Rate";
        
        $this->sql['koef']="SELECT  ITG_region_koef('4','{$this->params['user']}') AS COEF ";
         $this->sql['koefkor']="SELECT  ITG_region_koef('996','{$this->params['user']}') AS COEFKOR ";   
        $this->sql['NAMESEARCH']="SELECT Caption AS Caption FROM  b_autodoc_items_m WHERE ItemCode='{$this->params['article']}' LIMIT 1" ;
    }
     private function InfoFromTehnomirToDBase($ArrayInfo,$DB)
    {   $sql="SELECT * FROM b_autodoc_items_m WHERE ItemCode='{$this->params['article']}' AND Weight IS NULL LIMIT 1";
        $ResultObject=$DB->Query($sql);
        $ResultArray= $ResultObject->fetch_assoc();
        #echo "-------------------<br /><pre>";
              #print_r($ResultArray);
               #echo "</pre>";
        if ($ResultArray['ItemCode']!="")
        {
            #echo "-------------------<br /><pre>";
              #print_r($products);
               #echo "</pre>";
        
               if (isset($ArrayInfo['Weight']) && $ArrayInfo['Weight']!='')
               {
                  $sql=" UPDATE  b_autodoc_items_m SET
                    Weight=".$ArrayInfo['Weight']."
                    WHERE ItemCode='{$this->params['article']}'";
                    $DB->Query($sql);
                   
               } 
        }
        
    }
}
?>
