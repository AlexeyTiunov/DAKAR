<?php
class Customer
    {
        public $UserName;
        public $Password;
        public $SubCustomerId;
        public $CustomerId;
    }

class Search_ITG
{
    private $exact = false;
    private $exactInAbout = false;
    private $containItems = false; 
    private $staticItems = '';
    private $numRows = 0;
    private $tmpRes;
    private $items = array();
    private $brands = array();
    private $dbConn;
    private $url = array();
    private $params;
    private $sql;
    private $sqlString;
    function __construct($params)
    {
        $DB = self::manualConnect();
        $this->dbConn = $DB;
        $this->params = $params;
        $this->params['warehouse'] = false;
        $this->params['exactOnly'] = isset($params['exactOnly'])?true:false;
        #echo "<pre>";
        #print_r($this->params['region']);
        #echo "</pre>";
        $this->prepareSql();
        $this->getExactCompare();
        if ($this->numRows == 0 && $this->params['bcode'] == "" && !$this->params['exactOnly'])
        {
            #echo "<br />2:condition";
            $this->getCompetitor();
            #$this->numRows
            if ($this->numRows == 0)
            {
                $i = 0;
                foreach ($this->params['arBrands'] as $key=>$brand)
                {
                    #echo "SELECT ITG_presence_1c_F({$key},'{$this->params['icode']}') AS brand";
                    #$this->tmpRes = $DB->query("SELECT IFNULL((SELECT ITG_presence_F({$key},'{$this->params['icode']}')),(SELECT ITG_presence_1c_F({$key},'{$this->params['icode']}'))) AS brand");//запрос на приблизительное совпадение
                    $this->tmpRes = $DB->query("SELECT ITG_presence_Union({$key},'{$this->params['icode']}') AS brand");//
                    $brandSql = $this->tmpRes->fetch_assoc();
                    if ($brandSql['brand'])
                    {
                        $this->brands[$key] = $brand['FullName'];
                        $this->url[$key] = "?ICODE=".$this->params['icode']."&BCODE=".$key."&REGION=".$_REQUEST["REGION"];
                        $statBrand = $key;
                        #echo $this->url[$brand]."<br />";
                        $this->numRows = ++$i;
                    }
                }
                if ($i == 1)
                {
                    $this->params['bcode'] = $statBrand;
                    $this->prepareSql();
                    $this->getExactCompare();
                }
            }
        }
        elseif ($this->numRows > 0)
        {
            #echo "<br />3:condition";
            #$this->exact = true;
            $this->getArray();
            #$this->prePrint($this->items);
            if (!$this->params['warehouse'] && $this->exact)//
            {
                #
                $this->getCompetitor();
                #$this->prePrint($this->items);
                $this->exact = true;//
            }
        }
    }
    public static function manualConnect()
    {
             $port=31006;
        $DB = new mysqli("localhost","bitrix","a251d851","bitrix",$port);
        $DB->set_charset("utf8");
              $DB->query("SET NAMES 'utf8'");
        return $DB;
    }
    public function getSQLres()
    {
        return $this->tmpRes;
    }
    public function getNumRows()
    {
        return $this->numRows;
    }
    public function getBrans()
    {
        return $this->brands;
    }
    public function getArrItems()
    {
        return $this->items;
    }
    public function getUrl()
    {
        if (count($this->url) == 0 && count($this->brands) > 1)
        {
            foreach ($this->brands as $key=>$brand)
            {
                $this->url[$key] = "?ICODE=".$this->params['icode']."&BCODE=".$key."&REGION=".$_REQUEST["REGION"];
            }
        }
        return $this->url;
    }
    public function getExact()
    {
        return $this->exact;
    }
    public function getExactInAbout()
    {
        return $this->exactInAbout;
    }
    public function containItems()
    {
        return $this->containItems;
    }
    public function inOwnWarehouse()
    {
        return $this->params['warehouse'];
    }
     public function returnSqlString()
     {
                   return $this->sqlString;
     }
    private function getArray()
    {
        #echo "<br />inside getArray()";
        $DB = $this->dbConn;
        $brands = array();
        //
        $trigger = (count($this->brands)==0)?true:false;
              
        while ($arItem = $this->tmpRes->fetch_assoc())
        {
            $arItem['DeliveryDays'] = $this->params['region'][$arItem['RegionCode']]['DeliveryDays'];
            $arItem['RegionShortName'] = $this->params['region'][$arItem['RegionCode']]['ShortName'];
            $arItem['Currency'] = $this->params['region'][$arItem['RegionCode']]['chrCurrencyCode'];
            $brandsFlip = /*array_flip(*/$this->params['arBrands']/*)*/;
            $arItem['BrandName'] = $brandsFlip[$arItem['BrandCode']]['FullName'];
            #echo trim($arItem['RegionShortName'])."<br />";
            if ($this->staticItems == '')//
            {
                $this->staticItems = $arItem['ItemCode'].$arItem['BrandCode'];
                $this->exactInAbout = true;
            }
            elseif ($this->staticItems != $arItem['ItemCode'].$arItem['BrandCode'])//
            {
                $this->exactInAbout = false;
            }
            if ($arItem['isActive'] == -1)//
            {
                #$this->params['SuppCode'] = $arItem['RegionCode'];
                #$this->prepareSql();
                #$sqlRes = $DB->query($this->sql['getSuppliersCode']);
                #$short = $sqlRes->fetch_assoc();
                #$arItem['RegionShortName'] = $short['ShortSupp'];
                #$arItem['DeliveryDays'] = $short['DeliveryDay'];

                $arItem['Currency'] = $arItem['CurrencyRegion'];
                #$arItem['RegionCode'] = intval($short['RegionCode']);
                
                $arItem['RegionShortName'] = $this->params['regionID'][$arItem['RegionCode']]['ShortName'];
                $arItem['DeliveryDays'] = $this->params['regionID'][$arItem['RegionCode']]['DeliveryDays'];
                #$arItem['Currency'] = $this->params['regionID'][$arItem['RegionCode']]['chrCurrencyCode'];
                $arItem['RegionCode'] = $this->params['regionID'][$arItem['RegionCode']]['Code'];
                if (!$arItem['RegionShortName']) $arItem['RegionShortName'] = '-';
            }
            if ($arItem['RegionShortName'] == 'СКЛАД' ||$arItem['RegionShortName'] == 'ДОНЕЦК'  ) $this->params['warehouse'] = true;
            $this->items[] = $arItem;
            if ($trigger || $this->params['addBrand']) $this->brands[$arItem['BrandCode']] = $arItem['BrandName'];
        }
           
        #echo "<pre>";
        #print_r($this->items);
        #echo "</pre>";
        
        #echo "<pre>";
        #print_r($this->brands);
        #echo "</pre>";
        #if ($this->params['warehouse']) echo "есть на складе";
        #else echo "нет на складе";
    }
    private function getExactCompare()
    {
        #echo "<br/>inside getExactCompare<br/>";
        $DB = $this->dbConn;
        $this->sqlString=$this->sql['exact'];
        $this->tmpRes = $DB->query($this->sql['exact']);
        $this->numRows += $this->tmpRes->num_rows;
        $this->exact = true;
        $this->containItems = (boolean) $this->tmpRes->num_rows;
        if($this->tmpRes->num_rows == 0 && $this->params['bcode'] != "" && !$this->params['exactOnly'])
        {
            $this->exact = false;
            $this->containItems = true;
            #echo "<br /> condition 5";
            #echo "<br />{$this->sql['about']}";
            $this->tmpRes = $DB->query($this->sql['about']);//
            $this->numRows += $this->tmpRes->num_rows;
            $this->containItems = (boolean) $this->tmpRes->num_rows;
            if ($this->numRows > 0)
            {
                $this->getArray();
            }
        }
    }
    private function getCompetitor()
    {
        $DB = $this->dbConn;
                $DB-> query("SET NAMES 'utf8'");
        $sqlPartColumnRegion = $DB->query($this->sql['partColumnRegion']);//
        $partColumnRegion  = $sqlPartColumnRegion->fetch_assoc();
        #echo $partColumnRegion['RegionColumn']."<br />";
        $this->params['propertyColumn'] = "PROPERTY_".$partColumnRegion['RegionColumn'];//
        #$this->params['descriptionColumn'] = "DESCRIPTION_".$partColumnRegion['RegionColumn'];
        #echo $this->sql['priceColumn1c']."<br />";
        #$sqlPriceColumn1c = $DB->query($this->sql['priceColumn1c']);
        #$priceColumn1c = $sqlPriceColumn1c->fetch_assoc();
        #$this->params['PriceColumn_1C'] = $priceColumn1c['PriceColumn_1C'];
        
        $this->prepareSql();//
        #echo "<br />".$this->sql['suppliers']."<br />";
        $this->tmpRes = $DB->query($this->sql['suppliers']);//
        $this->numRows += $this->tmpRes->num_rows;
        $this->containItems = (boolean) $this->numRows;
        if ($this->numRows > 0)
        {
            $this->params['addBrand'] = true;
            $this->getArray();
        }
    }
    private function prepareSql()
    {
        #echo "<br/>inside prepare<br/>";
        if (!$this->params['user']) $this->params['user'] = -17;
        if (!$this->params['sort'] || $this->params['sort'] == 'PRICE') $this->params['sort'] = " Price, ";
        else $this->params['sort'] = " RegionCode, ";
        if (!$this->params['bcode']) $this->params['bcode'] = "";
        else $this->params['bcode'] = " `BrandCode`={$this->params['bcode']} AND ";
        $this->params['limit'] = ($this->params['page'] == 0)
                                            ?
                                            " 0,{$this->params['numPage']}"
                                            :
                                            " ".(intval($this->params['page']-1)*intval($this->params['numPage'])).",{$this->params['numPage']}";

        //global $DB;
        /*$this->params['fieldsExtern'] = "
                                    `id`, 
                                    `BrandCode`, 
                                    `ItemCode`, 
                                    `Caption`, 
                                    `Weight`, 
                                    `RegionCode`, 
                                    `PriceCode`, 
                                    `Price`*(SELECT ITG_region_koef(ITG.RegionCode,'{$this->params['user']}')) AS Price, 
                                    (`Price`*
                                        (SELECT ITG_region_koef(ITG.RegionCode,'{$this->params['user']}')))*
                                            (SELECT ITG_currency_rate(ITG.RegionCode,'{$this->params['currency']}')) AS UserPrice,
                                    `isActive`
                                    ";
        $this->params['fieldsOwn'] = "
                                    `id`, 
                                    `BrandCode`, 
                                    `ItemCode`, 
                                    `Caption`, 
                                    `Weight`, 
                                    `RegionCode`, 
                                    `PriceCode`, 
                                    `Price`,
                                    `Price`*
                                            (SELECT ITG_currency_rate(ITG.RegionCode,'{$this->params['currency']}')) AS UserPrice, 
                                    `isActive`
                                    ";*/
        $this->params['fieldsUnion'] = " 
                                    `BrandCode`, 
                                    `ItemCode`, 
                                    `Caption`,
                                    (SELECT IF (`RegionCode`=1 OR `RegionCode`=1000 ,`Quantity`,10 )) AS Quantity, 
                                    `Weight`, 
                                    `RegionCode`, 
                                    `PriceCode`, 
                                    `Price`*(SELECT IF(`RegionCode`=1 OR `RegionCode`=1000,1,(SELECT ITG_region_koef(`RegionCode`,'{$this->params['user']}')))) AS Price,
                                    (`Price`*
                                        (SELECT IF(`RegionCode`=1 OR `RegionCode`=1000 ,1,(SELECT ITG_region_koef(`RegionCode`,'{$this->params['user']}')))))*
                                            (SELECT ITG_currency_rate(`RegionCode`,'{$this->params['currency']}')) AS UserPrice,
                                    `isActive`
                                    ";
        /*$this->sql['exact'] = "(SELECT ".$this->params['fieldsOwn']." FROM `ITG_price_own` AS ITG
                                        WHERE ".$this->params['bcode']." 
                                            `PriceCode`=(SELECT ITG_price_koef('{$this->params['user']}'))
                                            AND
                                            `ItemCode`='{$this->params['icode']}')
                    UNION
                        (SELECT ".$this->params['fieldsExtern']." FROM `ITG_price_extern` AS ITG 
                                        WHERE ".$this->params['bcode']." `ItemCode`='{$this->params['icode']}')
                            ORDER BY ItemCode".$this->params['sort']." LIMIT ".$this->params['limit'];
        $this->sql['about'] = "(SELECT ".$this->params['fieldsOwn']." FROM `ITG_price_own` AS ITG 
                                        WHERE ".$this->params['bcode']." 
                                            `PriceCode`=(SELECT ITG_price_koef('{$this->params['user']}')) 
                                            AND 
                                            `ItemCode` LIKE '%{$this->params['icode']}%')
                    UNION
                        (SELECT ".$this->params['fieldsExtern']." FROM `ITG_price_extern` AS ITG 
                                        WHERE ".$this->params['bcode']." `ItemCode` LIKE '%{$this->params['icode']}%')
                            ORDER BY ItemCode".$this->params['sort']." LIMIT ".$this->params['limit'];*/
        $this->sql['exact'] = "SELECT ".$this->params['fieldsUnion']." FROM `ITG_price_union` AS ITG
                                        WHERE ".$this->params['bcode']." 
                                            (`RegionCode`!=1 OR (`RegionCode`=1 AND `PriceCode`=(SELECT ITG_price_koef('{$this->params['user']}')))) AND (`RegionCode`!=1000 OR(`RegionCode`=1000 AND `PriceCode`=(SELECT ITG_price_koef('{$this->params['user']}'))))
                                            AND
                                            `ItemCode`='{$this->params['icode']}'
                            ORDER BY ".$this->params['sort']." ItemCode LIMIT ".$this->params['limit'];
        $this->sql['about'] = "SELECT ".$this->params['fieldsUnion']." FROM `ITG_price_union` AS ITG
                                        WHERE ".$this->params['bcode']." 
                                           (`RegionCode`!=1 OR (`RegionCode`=1 AND `PriceCode`=(SELECT ITG_price_koef('{$this->params['user']}')))) AND (`RegionCode`!=1000 OR (`RegionCode`=1000 AND `PriceCode`=(SELECT ITG_price_koef('{$this->params['user']}'))) ) 
                                            AND
                                            `ItemCode` LIKE '%{$this->params['icode']}%'
                            ORDER BY ".$this->params['sort']." ItemCode LIMIT ".$this->params['limit'];
        #$this->sql['priceColumn1c'] = "SELECT ITG_price_koef('{$this->params['user']}') as PriceColumn_1C";
        $this->sql['partColumnRegion'] = "SELECT `RegionColumn` FROM  `b_autodoc_wh_price_types_m` 
                WHERE `Code`=(SELECT ITG_price_koef('{$this->params['user']}'))";

#(SELECT PriceColumn_1C FROM 

#b_user WHERE ID_1C='{$this->params['user']}');
        
        $this->sql['suppliers'] = "SELECT 
                                            CONCAT(`SuppCode`,`BrandCode`,`ItemCode`) as id,
                                            `BrandCode`,
                                            `ItemCode`,
                                            `Caption`,
                                            (SELECT IF(`Quantity`=0,1,`Quantity`)) AS Quantity,
                                            (SELECT PercentSupplied FROM `b_iblock_element_prop_s17` WHERE `IBLOCK_ELEMENT_ID`=SUPP.SuppCode   ) AS PercentSupp,
                                            (SELECT PROPERTY_94 FROM `b_iblock_element_prop_s17` WHERE `IBLOCK_ELEMENT_ID`=SUPP.SuppCode  ) AS RegionFullName,
                                            NULL as Weight,
                                            `SuppCode` as RegionCode,
                                            `Currency` as CurrencyRegion,
                                            `Price`*(SELECT ".$this->params['propertyColumn']." FROM `b_iblock_element_prop_s17` 
                                                                WHERE `IBLOCK_ELEMENT_ID`=SUPP.SuppCode) as Price,
                                            (`Price`*(SELECT ".$this->params['propertyColumn']." FROM `b_iblock_element_prop_s17` 
                                                                WHERE `IBLOCK_ELEMENT_ID`=SUPP.SuppCode))*
                                                                    (SELECT ITG_currency_rate_ua(SUPP.Currency,'{$this->params['currency']}')) AS UserPrice,
                                            -1 as isActive
                                        FROM  b_autodoc_prices_suppUA AS SUPP
                                            WHERE ";
        $this->sql['suppliers'] .= isset($_GET["BCODE"])?    "`BrandCode`={$_GET["BCODE"]} AND ":" ";
        $this->sql['suppliers'] .=                                         "`ItemCode`='{$this->params['icode']}'";
        #$this->sql['getSuppliersCode'] = "SELECT `PROPERTY_93` AS ShortSupp,`PROPERTY_95` AS DeliveryDay, `PROPERTY_189` AS Currency, `PROPERTY_92` AS RegionCode  FROM b_iblock_element_prop_s17 WHERE IBLOCK_ELEMENT_ID={$this->params['SuppCode']}";
        #echo "<br />".$this->sql['exact']."<br />";
        #echo "<br />".$this->sql['about']."<br />";
        #$this->prePrint($this->sql);
    }
    private function prePrint($var)
    {
        echo "<pre>";
        print_r($var);
        echo "</pre>";
    }
    
     public function GetInfoFromMegaP()
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
         $params = array("Customer"=>$customer,"DetailNum"=>$this->params['icode'],"ShowSubsts"=>false);
         $ArrayInfo=array(
                'weight'=> 'N',
                'quantity'=>'>10', 
                'persentsupped'=>'N'   
                  )   ;
         #$resLogin = $client->Login($params);
        try
     {    
             
             $resLogin=$client->SearchPart($params) ;
             
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
                        'persentsupped'=>((string)$info->PercentSupped)*1    
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
                 'persentsupped'=> ((string)$resLogin->SearchPartResult->FindByNumber->PercentSupped)*1 
                  )   ;
              }
                $ArrayInfo['weight']= (((string)$resLogin->SearchPartResult->FindByNumber->WeightGr)*1 )/1000;
                $ArrayInfo['persentsupped']=((string)$resLogin->SearchPartResult->FindByNumber->PercentSupped)*1 ;            }
            else
            {
               $ArrayInfo=array(
                'weight'=> 'N' ,
                'quantity'=>'>10',
                'persentsupped'=>'N'   
                  )   ; 
            }
     }  
     catch (Exception $e) 
     {
              $ArrayInfo=array(
                'weight'=> 'N',
                'quantity'=>'>10' ,
                'persentsupped'=>'N'      
                  )   ;
     }
         #$res=iconv('utf8','cp1251',(string)$resLogin->SearchPartResult->FindByNumber[0]->PartNameRus);
         #echo $res;
         #var_dump($resLogin);
         return $ArrayInfo;
     }
    
}
?>
