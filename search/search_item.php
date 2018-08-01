<?   session_start();
    
  if (isset($_GET['FULL_PAGE']))
  {
     require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php"); 
     ?>
       <script>
        $(function(){
        
          $("#search_result").css("margin-top","7%");
          $("#search_result").css("box-shadow","none") ;
          $("#search_result").css("border-radius","0px"); 
          $("#search_result").css("background","none");
          $("#search_result").css("width","75%");
        
        })
        </script>
     <?
  }else
  {
     require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');   error_reporting(E_ALL);  
     ?>
       <script>
        $(function(){
        
          //$("#search_result").css("margin-top","7%");
          $("#search_result").css("box-shadow","none") ;
          $("#search_result").css("border-radius","0px"); 
          $("#search_result").css("background","none");
          $("#search_result").css("width","75%");
        
        })
        </script>
     <?   
  }
 
  require_once($_SERVER['DOCUMENT_ROOT'].'search/web_service_connect.php');
  global $DB;
  global $USER; 
  $GLOBALS['SEARCH_ITEM_TABLE']="b_autodoc_prices_suppUA" ;
   $GLOBALS['SEARCH_ANALOG_TABLE']="b_dakar_analogs_m";
  $GLOBALS['ITEM_CODE_COLUMN_NAME']="ItemCode";
  $GLOBALS['BRAND_CODE_COLUMN_NAME']="BrandCode";
  $GLOBALS['ITEM_CODE_ANALOG1_COLUMN_NAME']="I1Code";
  $GLOBALS['BRAND_CODE_ANALOG1_COLUMN_NAME']="B1Code";
  $GLOBALS['ITEM_CODE_ANALOG2_COLUMN_NAME']="I2Code";
  $GLOBALS['BRAND_CODE_ANALOG2_COLUMN_NAME']="B2Code";
  
  $GLOBALS['COMMON_PRICE_PERCENT_ADD']=30;  
  #session_start(); 
  
 $groupArray=$USER->GetUserGroupArray();
foreach ($groupArray as $id=>$i)
{
   
   if ($i==1 || $i==7)
    #if ($i==1) 
    {  #echo'www';
      $groupCheck=true; 
      break;   
    } else
    {
          $groupCheck=false ;
    }
}    
  $GLOBALS['SEARCH_TYPE']="search"; 
  $brands="b_iblock_element_prop_s37"; 
 $GLOBALS['BRAND_TABLE_NAME']=$brands;   
 $GLOBALS['BRAND_ID_LOCAL_COLUMN_NAME']="IBLOCK_ELEMENT_ID";
  #$GLOBALS['TECDOC_BRAND_ID_LOCAL_COLUMN_NAME']="DESCRIPTION_71";
  $GLOBALS['TECDOC_BRAND_ID_LOCAL_COLUMN_NAME']="PROPERTY_286"; 
    
 # $GLOBALS['BRAND_NAME_LOCAL_COLUMN_NAME']="PROPERTY_72"; 
 # $GLOBALS['TECDOC_BRAND_NAME_LOCAL_COLUMN_NAME']=""; 
  
 # $GLOBALS['ACTIVE_BRAND_COLUMN_NAME']="DESCRIPTION_72"; 
  
  $GLOBALS['BRAND_NAME_LOCAL_COLUMN_NAME']="PROPERTY_288"; 
  $GLOBALS['TECDOC_BRAND_NAME_LOCAL_COLUMN_NAME']=""; 
  
  $GLOBALS['ACTIVE_BRAND_COLUMN_NAME']="DESCRIPTION_287";
  $GLOBALS['INCLUDE_ITEM_TEMPLATE']=$_SERVER['DOCUMENT_ROOT']."/search/show_search_item_template.php"; 
  
?>

 <?
    function MakeNavigationString($pages_count,$count_for_page,$active_page_number,$sort,$dsort)
    {
        $previuos=($active_page_number-1<1)?"":"<a href='/search/search_item.php?pageNumber=".($active_page_number-1)."&FULL_PAGE &SORT={sort}&DSORT={$dsort}'> < </a>";
        
        $next=($active_page_number+1>$pages_count)?"":"<a href='/search/search_item.php?pageNumber=".($active_page_number+1)."&FULL_PAGE&SORT={sort}&DSORT={$dsort}'> > </a>";
        #var_dump($pages_count);
        $nav_string="<div id='nav'>";
        $nav_string.=$previuos;
        for ($i=1;$i<=$pages_count;$i++)
        { 
          if ($i==$active_page_number)
          {
              $style="color:#c01717";
          } 
          else
          {  
              $style="";              
          }           
         $nav_string.="<a  style='{$style}'href='/search/search_item.php?pageNumber={$i}&FULL_PAGE&SORT={$sort}&DSORT={$dsort}'>{$i}</a>";  
        }
         $nav_string.=$next;
        $nav_string.="</div>"; 
        return $nav_string;
    }
     function MakeNavigationStringAjax($itemCode,$pages_count,$count_for_page,$active_page_number,$sort,$dsort,$class="navlink")
    {
        $previuos=($active_page_number-1<1)?"":"<a href='#' class='{$class}' itemCode='{$itemCode}' pageNumber='".($active_page_number-1)."' FULL_PAGE=''  SORT='{sort}' DSORT='{$dsort}'> < </a>";
        
        $next=($active_page_number+1>$pages_count)?"":"<a href='#' class='{$class}' itemCode='{$itemCode}' pageNumber='".($active_page_number+1)."' FULL_PAGE=''  SORT='{sort}' DSORT='{$dsort}'> > </a>";
        #var_dump($pages_count);
        $nav_string="<div id='nav'>";
        $nav_string.=$previuos;
        for ($i=1;$i<=$pages_count;$i++)
        { 
          if ($i==$active_page_number)
          {
              $style="color:#c01717";
          } 
          else
          {  
              $style="";              
          }           
         $nav_string.="<a  style='{$style}' href='#' class='{$class}' itemCode='{$itemCode}' pageNumber='{$i}' FULL_PAGE=''  SORT='{$sort}' DSORT='{$dsort}'>{$i}</a>";  
        }
         $nav_string.=$next;
        $nav_string.="</div>"; 
        return $nav_string;
    }
    
    function MakeSortArrows($active_page_number,$condition_sort)
    {
      $sortArrowsString="";
      $sortArrowsString.="<p style='margin-top: 10px;font-size: 20px;' align='center'>";
      $sortArrowsString.="<a href='/search/search_item.php?pageNumber=".($active_page_number)."&FULL_PAGE&SORT={$condition_sort}&DSORT=ACS'> < </a>";
      $sortArrowsString.="<a href='/search/search_item.php?pageNumber=".($active_page_number)."&FULL_PAGE&SORT={$condition_sort}&DSORT=DECS'> > </a>";   
      $sortArrowsString.="</p>";  
      
      return $sortArrowsString;
    }
    function ShowCurrency($currency)
   {
       if ($currency=="")
      {
          return "USD";
      }
      if ($currency=="USD") 
       {
           return "$";
       } 
       if ($currency=="UAH") 
       {
           
         return "грн.";  
           
       }  
      return  $Currency;  
       
   }
   function SearchItemByItemCode($itemCode)
   {
      global $DB;
      $sql="SELECT * FROM {$GLOBALS['SEARCH_ITEM_TABLE']} WHERE {$GLOBALS['ITEM_CODE_COLUMN_NAME']}='{$itemCode}'";
      
      $result=$DB->Query($sql);
      $itemsArray=array();
      while ($itemPositionArray=$result->Fetch())
      {
                $supplierInfo= GetSupplierInfoByID($itemPositionArray['SuppCode']);
                $itemArray=Array();
                $itemArray[$itemPositionArray['BrandCode']][$itemPositionArray['ItemCode']]["BRAND_CODE"]=$itemPositionArray['BrandCode'];
                $itemArray[$itemPositionArray['BrandCode']][$itemPositionArray['ItemCode']]["BRAND_NAME"]=GetBrandNameByCode($itemPositionArray['BrandCode']); 
                $itemArray[$itemPositionArray['BrandCode']][$itemPositionArray['ItemCode']]["ITEM_CODE"] =$itemPositionArray['ItemCode'];
                $itemArray[$itemPositionArray['BrandCode']][$itemPositionArray['ItemCode']]["QUANTITY"]  = $itemPositionArray['Quantity'];
                $itemArray[$itemPositionArray['BrandCode']][$itemPositionArray['ItemCode']]['CAPTION']   = $itemPositionArray['Caption'];
                $itemArray[$itemPositionArray['BrandCode']][$itemPositionArray['ItemCode']]["PRICE"]     = $itemPositionArray['Price'];
                $itemArray[$itemPositionArray['BrandCode']][$itemPositionArray['ItemCode']]["PRICE_SALE"]     = AddPercentsToPrice($itemPositionArray['Price'],$supplierInfo);
                $itemArray[$itemPositionArray['BrandCode']][$itemPositionArray['ItemCode']]["PICTURE"]   =GetImageForAvailableItem(GetBase64ImageString($itemPositionArray['ItemCode'],$itemPositionArray['BrandCode'])); 
                $itemArray[$itemPositionArray['BrandCode']][$itemPositionArray['ItemCode']]['CURRENCY']  =$itemPositionArray['Currency'];
                $itemArray[$itemPositionArray['BrandCode']][$itemPositionArray['ItemCode']]['SUPPLIER_CODE']  =$itemPositionArray['SuppCode'];
                $itemArray[$itemPositionArray['BrandCode']][$itemPositionArray['ItemCode']]['SUPPLIER_NAME']  =$supplierInfo['NAME'];
                $itemsArray[]=$itemArray;
        }                                                                                                    
      return   $itemsArray;
   }
   function SearchAnalogsByItemCode($itemCode)
   {
         global $DB;
      $sql="SELECT * FROM {$GLOBALS['SEARCH_ANALOG_TABLE']} WHERE {$GLOBALS['ITEM_CODE_ANALOG1_COLUMN_NAME']}='{$itemCode}'";
     # var_dump($sql);
      $result=$DB->Query($sql);
      $AnalogsArray=array();
      while ($itemPositionArray=$result->Fetch())
      {   #var_dump($itemPositionArray[$GLOBALS['ITEM_CODE_ANALOG2_COLUMN_NAME']]);
          #$itemAnalogsArray= SearchItemByItemCodeForSort($itemPositionArray[$GLOBALS['ITEM_CODE_ANALOG2_COLUMN_NAME']]);
          $itemAnalogsArray=SearchItemByItemCodeBrandCodeForSort($itemPositionArray[$GLOBALS['ITEM_CODE_ANALOG2_COLUMN_NAME']],$itemPositionArray[$GLOBALS['BRAND_CODE_ANALOG2_COLUMN_NAME']]);
          #var_dump($itemAnalogsArray);
          #var_dump($itemAnalogsArray);
          foreach ($itemAnalogsArray as $itemAnalogs)
          {
             $AnalogsArray[]= $itemAnalogs;
          }
          
          
      } 
     return  $AnalogsArray; 
   }
   function SearchAnalogsByItemCodeBrandCode($itemCode,$brandCode)
   {
         global $DB;
      $sql="SELECT * FROM {$GLOBALS['SEARCH_ANALOG_TABLE']} WHERE {$GLOBALS['ITEM_CODE_ANALOG1_COLUMN_NAME']}='{$itemCode}'
            AND {$GLOBALS['BRAND_CODE_ANALOG1_COLUMN_NAME']}='{$brandCode}'
      
      ";
     # var_dump($sql);
      $result=$DB->Query($sql);
      $AnalogsArray=array();
      while ($itemPositionArray=$result->Fetch())
      {   #var_dump($itemPositionArray[$GLOBALS['ITEM_CODE_ANALOG2_COLUMN_NAME']]);
          $itemAnalogsArray= SearchItemByItemCodeBrandCode($itemPositionArray[$GLOBALS['ITEM_CODE_ANALOG2_COLUMN_NAME']],$itemPositionArray[$GLOBALS['BRAND_CODE_ANALOG2_COLUMN_NAME']]);
          #var_dump($itemAnalogsArray);
          foreach ($itemAnalogsArray as $itemAnalogs)
          {
             $AnalogsArray[]= $itemAnalogs;
          }
          
          
      } 
     return  $AnalogsArray; 
   }
   
   function SearchItemByItemCodeForSort($itemCode)
   {
      global $DB;
      $sql="SELECT * FROM {$GLOBALS['SEARCH_ITEM_TABLE']} WHERE {$GLOBALS['ITEM_CODE_COLUMN_NAME']}='{$itemCode}'";
      #var_dump($sql);  
      $result=$DB->Query($sql);
      $itemsArray=array();
      while ($itemPositionArray=$result->Fetch())
      {
                $supplierInfo= GetSupplierInfoByID($itemPositionArray['SuppCode']);
                $itemArray=Array();
                $itemArray["BRAND_CODE"]=$itemPositionArray['BrandCode'];
                $itemArray["BRAND_NAME"]=GetBrandNameByCode($itemPositionArray['BrandCode']); 
                $itemArray["ITEM_CODE"] =$itemPositionArray['ItemCode'];
                $itemArray["QUANTITY"]  = $itemPositionArray['Quantity'];
                $itemArray['CAPTION']   = $itemPositionArray['Caption'];
                $itemArray["PRICE"]     = $itemPositionArray['Price'];
                $itemArray["PRICE_SALE"]     = AddPercentsToPrice($itemPositionArray['Price'],$supplierInfo);
                $itemArray["PICTURE"]   =GetImageForAvailableItem(GetBase64ImageString($itemPositionArray['ItemCode'],$itemPositionArray['BrandCode'])); 
                $itemArray['CURRENCY']  =$itemPositionArray['Currency'];
                $itemArray['SUPPLIER_CODE']  =$itemPositionArray['SuppCode'];
                $itemArray['SUPPLIER_NAME']  =$supplierInfo['COMMON_NAME'];
                $itemsArray[]=$itemArray;
        }                                                                                                    
      return   $itemsArray;
   }
   
   
   function SearchItemByItemCodeBrandCode($itemCode,$BrandCode)
   {
       global $DB;
      $sql="SELECT * FROM {$GLOBALS['SEARCH_ITEM_TABLE']} WHERE {$GLOBALS['ITEM_CODE_COLUMN_NAME']}='{$itemCode}' AND {$GLOBALS['BRAND_CODE_COLUMN_NAME']}={$BrandCode} ";
      
      $result=$DB->Query($sql);
      $itemsArray=array();
      while ($itemPositionArray=$result->Fetch())
      {
                $supplierInfo= GetSupplierInfoByID($itemPositionArray['SuppCode']);
                $itemArray=Array();
                $itemArray[$itemPositionArray['BrandCode']][$itemPositionArray['ItemCode']]["BRAND_CODE"]=$itemPositionArray['BrandCode'];
                $itemArray[$itemPositionArray['BrandCode']][$itemPositionArray['ItemCode']]["BRAND_NAME"]=GetBrandNameByCode($itemPositionArray['BrandCode']); 
                $itemArray[$itemPositionArray['BrandCode']][$itemPositionArray['ItemCode']]["ITEM_CODE"] =$itemPositionArray['ItemCode'];
                $itemArray[$itemPositionArray['BrandCode']][$itemPositionArray['ItemCode']]["QUANTITY"]  = $itemPositionArray['Quantity'];
                $itemArray[$itemPositionArray['BrandCode']][$itemPositionArray['ItemCode']]['CAPTION']   = $itemPositionArray['Caption'];
                $itemArray[$itemPositionArray['BrandCode']][$itemPositionArray['ItemCode']]["PRICE"]     = $itemPositionArray['Price'];
                $itemArray[$itemPositionArray['BrandCode']][$itemPositionArray['ItemCode']]["PRICE_SALE"]     = AddPercentsToPrice($itemPositionArray['Price'],$supplierInfo);
                $itemArray[$itemPositionArray['BrandCode']][$itemPositionArray['ItemCode']]["PICTURE"]   =GetImageForAvailableItem(GetBase64ImageString($itemPositionArray['ItemCode'],$itemPositionArray['BrandCode'])); 
                $itemArray[$itemPositionArray['BrandCode']][$itemPositionArray['ItemCode']]['CURRENCY']  =$itemPositionArray['Currency'];
                $itemArray[$itemPositionArray['BrandCode']][$itemPositionArray['ItemCode']]['SUPPLIER_CODE']  =$itemPositionArray['SuppCode'];
                $itemArray[$itemPositionArray['BrandCode']][$itemPositionArray['ItemCode']]['SUPPLIER_NAME']  =$supplierInfo['NAME'];
                $itemsArray[]=$itemArray;
        }                                                                                                    
      return   $itemsArray;
       
       
       
       
       
       
   }
   
   function SearchItemByItemCodeBrandCodeForSort($itemCode,$BrandCode)
   {
       global $DB;
      $sql="SELECT * FROM {$GLOBALS['SEARCH_ITEM_TABLE']} WHERE {$GLOBALS['ITEM_CODE_COLUMN_NAME']}='{$itemCode}' AND {$GLOBALS['BRAND_CODE_COLUMN_NAME']}={$BrandCode} ";
      
      $result=$DB->Query($sql);
      $itemsArray=array();
      while ($itemPositionArray=$result->Fetch())
      {
                $supplierInfo= GetSupplierInfoByID($itemPositionArray['SuppCode']);
                $itemArray=Array();
                $itemArray["BRAND_CODE"]=$itemPositionArray['BrandCode'];
                $itemArray["BRAND_NAME"]=GetBrandNameByCode($itemPositionArray['BrandCode']); 
                $itemArray["ITEM_CODE"] =$itemPositionArray['ItemCode'];
                $itemArray["QUANTITY"]  = $itemPositionArray['Quantity'];
                $itemArray['CAPTION']   = $itemPositionArray['Caption'];
                $itemArray["PRICE"]     = $itemPositionArray['Price'];
                $itemArray["PRICE_SALE"]     = AddPercentsToPrice($itemPositionArray['Price'],$supplierInfo);
                $itemArray["PICTURE"]   =GetImageForAvailableItem(GetBase64ImageString($itemPositionArray['ItemCode'],$itemPositionArray['BrandCode'])); 
                $itemArray['CURRENCY']  =$itemPositionArray['Currency'];
                $itemArray['SUPPLIER_CODE']  =$itemPositionArray['SuppCode'];
                $itemArray['SUPPLIER_NAME']  =$supplierInfo['COMMON_NAME'];
                $itemsArray[]=$itemArray;
        }                                                                                                    
      return   $itemsArray;
       
       
       
       
       
       
   }
   function SortSearchArrayACS(&$itemsArray,$sort_condition)
   {
      $itemsArrayCount=count($itemsArray); 
      for ($i=0;$i<$itemsArrayCount;$i++)
      {
          for ($ii=0;$ii<$itemsArrayCount;$ii++)
          {
              if (floatval($itemsArray[$i][$sort_condition])<floatval($itemsArray[$ii][$sort_condition]))
              {
                  $firstArray=$itemsArray[$i];
                  $secondArray=$itemsArray[$ii];
                  $itemsArray[$i]=$secondArray;
                  $itemsArray[$ii]=$firstArray;
                  
              }
              
          }
          
          
          
          
      } 
       
       
   }
   
    function SortSearchArrayDECS(&$itemsArray,$sort_condition)
   {
      $itemsArrayCount=count($itemsArray); 
      for ($i=0;$i<$itemsArrayCount;$i++)
      {
          for ($ii=0;$ii<$itemsArrayCount;$ii++)
          {
              if (floatval($itemsArray[$i][$sort_condition])>floatval($itemsArray[$ii][$sort_condition]))
              {
                  $firstArray=$itemsArray[$i];
                  $secondArray=$itemsArray[$ii];
                  $itemsArray[$i]=$secondArray;
                  $itemsArray[$ii]=$firstArray;
                  
              }
              
          }
          
          
          
          
      } 
       
       
   }  
    function AddPercentsToPrice($Price,$supplierInfoArray)
    {
        
      return  round(floatval($Price)*(1+($GLOBALS['COMMON_PRICE_PERCENT_ADD']/100)),2,PHP_ROUND_HALF_UP );
    }
    function GetSupplierInfoByID($ID)
    {
        global $DB;
       $supplierInfoArray=array(
                          'NAME'=>"",
                          'COMMON_NAME'=>''
       
       );
       $sql="SELECT * FROM b_iblock_element_prop_s17 WHERE PROPERTY_92={$ID} LIMIT 1";
       $result=$DB->Query($sql);
       $suppPositionArray=$result->Fetch();
       
       $supplierInfoArray['NAME']=$suppPositionArray['PROPERTY_94'];
       $supplierInfoArray['COMMON_NAME']=$suppPositionArray['DESCRIPTION_93'];
        
      return $supplierInfoArray;  
    }
    function GetBrandNameByCode($brand_code)
    {
          global $DB;
        $sql="SELECT {$GLOBALS['BRAND_ID_LOCAL_COLUMN_NAME']} AS ID,{$GLOBALS['BRAND_NAME_LOCAL_COLUMN_NAME']} AS FULLNAME FROM {$GLOBALS['BRAND_TABLE_NAME']} WHERE {$GLOBALS['BRAND_ID_LOCAL_COLUMN_NAME']}='".trim($brand_code)."'"; 
        
           $result =$DB->Query ($sql) ;
           $brand_name=$result->Fetch()['FULLNAME'];        
       
        return $brand_name;
    } 
    function GetBase64ImageString($itemCode,$brandCode)
    {
        global $DB;
        
        $sql="SELECT Base64 FROM b_autodoc_items_catalog_items WHERE BRAND_CODE={$brandCode} AND ITEM_CODE='{$itemCode}' LIMIT 1";
        
        $result=$DB->Query($sql);
        
        return $result->Fetch()['Base64'];   
        
        
        
    }
     function GetImageForAvailableItem($dbValue)
    {
        if ($dbValue==null || $dbValue=="")
        {
            return "src='/images/favicon.png'";
            
        }else
        {
           
            return "src='data:image/png;base64,{$dbValue}'";
        }
        
        
        
    }
    #echo "check3";
    #var_dump($_POST);
    if (isset($_SESSION['ITEM_CODE']) && !isset($_POST['ITEM_CODE']))
    {
       $_POST['ITEM_CODE']= $_SESSION['ITEM_CODE'];
    } elseif (isset($_POST['ITEM_CODE']) )
    {
       $_SESSION['ITEM_CODE']=$_POST['ITEM_CODE']; 
    }
    
    
    if (isset($_POST['ITEM_CODE']) && isset($_POST['BRAND_CODE']))
    {
       # echo "check2";
       $itemCode=preg_replace("/[^A-Za-z0-9]/","",$_POST['ITEM_CODE']);
       $brandCode=preg_replace("/[^0-9]/","",$_POST['BRAND_CODE']);
              
       $itemArray=SearchItemByItemCodeBrandCodeForSort($itemCode,$BrandCode);
       
       $modelName="";
       $modelTypeName="";
       $groupTypeName="";   
    
       
       include $GLOBALS['INCLUDE_ITEM_TEMPLATE']; 
       
        
    }
    elseif (isset($_POST['ITEM_CODE']) && !isset($_POST['BRAND_CODE']))
    {
        #echo "check1";
       $itemCode=preg_replace("/[^A-Za-z0-9]/","",$_POST['ITEM_CODE']);
      
       if (isset($_POST['ANALOGS']))
       {
             $itemsArray=SearchAnalogsByItemCode($itemCode);
       }else
       {
         $itemsArray=SearchItemByItemCodeForSort($itemCode); 
         $webServiceQuery= new Connect_Web_Service(array('article'=>$itemCode,'brand'=>''),$itemsArray);       
         $itemsArray= $webServiceQuery->getResult();  
       }       
       
     
     # var_dump($itemsAnalogArray); 
       
       
       if (isset($_GET['SORT']) && isset($_GET['DSORT']))
       {
            if  ($_GET['DSORT']=="DECS")  SortSearchArrayDECS($itemsArray,$_GET['SORT']); 
            elseif ($_GET['DSORT']=="ACS")  SortSearchArrayACS($itemsArray,$_GET['SORT']);
            else    SortSearchArrayACS($itemsArray,"PRICE"); 
           
       } else
       {
         SortSearchArrayACS($itemsArray,"PRICE");  
       }
       
      # SortSearchArrayDECS($itemsArray,"PRICE");
      # var_dump($itemArray);
       $modelName="";
       $modelTypeName="";
       $groupTypeName="";   
    
       include $GLOBALS['INCLUDE_ITEM_TEMPLATE'];        
     //  $itemsArray=$itemsAnalogArray;
     //  include $GLOBALS['INCLUDE_ITEM_TEMPLATE'];
      
       
       
    }
    elseif  (isset($_POST['BRAND_CODE']))
    {
        
    }
    
    
    
    if (isset($_GET['FULL_PAGE']))
  {
     require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php"); 
  }else
  {
        
  }
 ?>


