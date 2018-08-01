<?
  require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php'); 
   global $DB;
    error_reporting(0);
  $catalog_groups=" b_autodoc_items_catalog_groups";
  $catalog_groups_types="b_autodoc_items_catalog_groups_types";
  $brands="b_iblock_element_prop_s37";
  $GLOBALS['BRAND_TABLE_NAME']=$brands;
  $carmodel="b_autodoc_carmodels";
  $catalog_items="b_autodoc_items_catalog_items";
  $catalog_items_structure = "b_autodoc_items_catalog_structure";
  $carmodel_types="b_autodoc_carmodels_types";
  $GLOBALS['BRAND_ID_LOCAL_COLUMN_NAME']="IBLOCK_ELEMENT_ID";
  $GLOBALS['TECDOC_BRAND_ID_LOCAL_COLUMN_NAME']="PROPERTY_286";
  
  $GLOBALS['BRAND_NAME_LOCAL_COLUMN_NAME']="PROPERTY_288"; 
  $GLOBALS['TECDOC_BRAND_NAME_LOCAL_COLUMN_NAME']=""; 
  
  $GLOBALS['ACTIVE_BRAND_COLUMN_NAME']="DESCRIPTION_287";
  class Connect_Web_Service 
  {
      public static  $log = array(
                            'tehnomir'    =>array('pass'        =>'kma924',
                                                'log'        =>'kobets',
                                                'url'        =>'http://tehnomir.com.ua/ws/soap.wsdl',
                                                'encoding'    =>'utf-8',
                                                'region'    =>'UW1')
                                                );
      private $params = array();
      private $client;
      private $result;
      private $clientmir;
      private $sql = array(); 
      private $itemArray;
      private $itemsArray;
      
      function __construct($params,&$itemsArray)
      {
        $this->params = $params;
        $this->itemsArray=$itemsArray;
        foreach (self::$log as $name=>$propsClient)
        {
            if($name=='Rovenko')
            { 
                
            } else
            {
             $this->params['client'] = $name;
             $this->client = @new SoapClient(self::$log[$this->params['client']]['url'], array('encoding'=>self::$log[$this->params['client']]['encoding']));

            
              $this->authorization(); 
              $this->prepareResultForSort();
            }
        } 
           
           
           
       }  
       public function getResult()
       {
        return $this->itemsArray;
       } 
      private function authorization()
      {
        $log = self::$log[$this->params['client']]['log'];
        $pass = self::$log[$this->params['client']]['pass'];
        $partnumber = $this->params['article'];
        switch ($this->params['client'])
        {
          case 'tehnomir':

                $brand = ($this->params['brand'] != '')?$this->params['brand']:'';
                try
                {  
                #set_time_limit(1);
                $result = $this->client->GetPrice($partnumber, $brand, $log, $pass); 
                }
                 catch (Exception $e) 
                        {
                          #  echo 'Выброшено исключение: ',  $e->getMessage(), "\n";
                       }
               # var_dump($result);        
          break; 
            
            
        }         
        $this->result = $result;
        #var_dump($this->result);
     }  
       private function prepareResult()
      {
        $products = array();
      #  $DB = Search_ITG::manualConnect();
        if (count($this->result) == 0) return array();
        foreach ($this->result as $key=>$product)
        {
            $itemPositionArray=array(); 
           #if ((isset($product['Quantity']) && intval($product['Quantity']) == 1000)||(isset($product["SupplierCode"])  && $product["SupplierCode"]!="KLOD"  && $product["SupplierCode"]!="STOK" ) ) continue;     //TechnoMir
            if (isset($product['Quantity']) && intval($product['Quantity']) == 0) continue;
            $this->itemArray=Array();
            switch ($this->params['client'])
            {
                case 'tehnomir':
                     #var_dump($product['Brand']);
                    $brandCode=$this->GetBrandCodeIDByName($product['Brand']);
                    if ($brandCode===false)  continue;
                     $this->itemArray[$brandCode][$product['Number']]['SUPPLIER_CODE'] =6;                 
                     $this->itemArray[$brandCode][$product['Number']]['BRAND_CODE']=$brandCode;
                     $this->itemArray[$brandCode][$product['Number']]['BRAND_NAME']=$product['Brand'];
                     $this->itemArray[$brandCode][$product['Number']]['ITEM_CODE']=$product['Number'];
                     $this->itemArray[$brandCode][$product['Number']]["QUANTITY"]= $product['Quantity'];
                     $this->itemArray[$brandCode][$product['Number']]['CAPTION']=$product['Name'];
                     $this->itemArray[$brandCode][$product['Number']]["PRICE"]=$product['Price'];
                     $this->itemArray[$brandCode][$product['Number']] ["PRICE_SALE"]  = self::AddPercentsToPrice($product['Price'],SELF::GetSupplierInfoByID($this->itemArray[$brandCode][$product['Number']]['SUPPLIER_CODE']));
                     $this->itemArray[$brandCode][$product['Number']]['CURRENCY']=$product['Currency'];
                     $this->itemArray[$brandCode][$product['Number']]["PICTURE"]="src='/images/favicon.png'";
                     
                     $this->itemArray[$brandCode][$product['Number']]['SUPPLIER_NAME']  =self::GetSupplierInfoByID($this->itemArray[$brandCode][$product['Number']]['SUPPLIER_CODE'])['NAME']; 
                     $this->itemsArray[]=$this->itemArray;
                break;
                
                #$products[] = $product;
            }
                                                                                                                                               
            
         # $this->resultToReturn[] = $products;   
        }
        
        
    } 
      private function prepareResultForSort()
      {
        $products = array();
      #  $DB = Search_ITG::manualConnect();
        if (count($this->result) == 0) return array();
        foreach ($this->result as $key=>$product)
        {
            $itemPositionArray=array(); 
           #if ((isset($product['Quantity']) && intval($product['Quantity']) == 1000)||(isset($product["SupplierCode"])  && $product["SupplierCode"]!="KLOD"  && $product["SupplierCode"]!="STOK" ) ) continue;     //TechnoMir
            if (isset($product['Quantity']) && intval($product['Quantity']) == 0) continue;
            $this->itemArray=Array();
            switch ($this->params['client'])
            {
                case 'tehnomir':
                     #var_dump($product['Brand']);
                    $brandCode=$this->GetBrandCodeIDByName($product['Brand']);
                    if ($brandCode===false)  continue;
                     $this->itemArray['SUPPLIER_CODE'] =6;                 
                     $this->itemArray['BRAND_CODE']=$brandCode;
                     $this->itemArray['BRAND_NAME']=$product['Brand'];
                     $this->itemArray['ITEM_CODE']=$product['Number'];
                     $this->itemArray["QUANTITY"]= $product['Quantity'];
                     $this->itemArray['CAPTION']=$product['Name'];
                     $this->itemArray["PRICE"]=$product['Price'];
                     $this->itemArray["PRICE_SALE"]  = self::AddPercentsToPrice($product['Price'],SELF::GetSupplierInfoByID($this->itemArray['SUPPLIER_CODE']));
                     $this->itemArray['CURRENCY']=$product['Currency'];
                     $this->itemArray["PICTURE"]="src='/images/favicon.png'";
                     
                     $this->itemArray['SUPPLIER_NAME']  =self::GetSupplierInfoByID($this->itemArray['SUPPLIER_CODE'])['COMMON_NAME']; 
                     $this->itemsArray[]=$this->itemArray;
                break;
                
                #$products[] = $product;
            }
                                                                                                                                               
            
         # $this->resultToReturn[] = $products;   
        }
        
        
    }
    private function GetBrandCodeIDByName($brand_name)
    {
      global $DB;
     
     if (strlen(trim($brand_name))>2)
     {
         $sql="SELECT {$GLOBALS['BRAND_ID_LOCAL_COLUMN_NAME']} AS ID,{$GLOBALS['BRAND_NAME_LOCAL_COLUMN_NAME']} AS FULLNAME FROM {$GLOBALS['BRAND_TABLE_NAME']} WHERE {$GLOBALS['BRAND_NAME_LOCAL_COLUMN_NAME']}='".trim($brand_name)."'";
           $result =$DB->Query ($sql) ;
           $res=$result->Fetch();        
            if ($res['ID']>0)
            {
                return $res['ID'];
            }else
            {
                return false;
            }   
         
     }
    
     else
     {
         return false;
     }  
        
        
    } 
   static private function GetSupplierInfoByID($ID)
    {
        global $DB;
       $supplierInfoArray=array(
                          'NAME'=>"",
                           'COMMON_NAME'=>""
       
       );
       $sql="SELECT * FROM b_iblock_element_prop_s17 WHERE PROPERTY_92={$ID} LIMIT 1";
       $result=$DB->Query($sql);
       $suppPositionArray=$result->Fetch();
       
       $supplierInfoArray['NAME']=$suppPositionArray['PROPERTY_94'];
       $supplierInfoArray['COMMON_NAME']=$suppPositionArray['DESCRIPTION_93'];  
      return $supplierInfoArray;  
    }
   static private function AddPercentsToPrice($Price,$supplierInfoArray)
    {
        
      return  round(floatval($Price)*(1+($GLOBALS['COMMON_PRICE_PERCENT_ADD']/100)),2,PHP_ROUND_HALF_UP );
    } 
  }  
    
    
    
?>