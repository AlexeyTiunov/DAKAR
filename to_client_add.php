<?
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php"); 
    global $USER;
    
    global $DB ;
    session_start(); 
      
     function GetUserIDByLogin ($Login) 
     {
        global $DB;
        
        $sql="SELECT ID FROM b_user WHERE LOGIN='{$Login}' LIMIT 1" ;
        
          $ID=$DB->Query($sql)->Fetch()['ID']; 
          if ($ID==null || $ID=="")
          {
             return 0; 
          }else
          {
            return $ID; 
          }
        
         
     }
      
      function PriceKoef($Currency , $UserCurrency)      // $Currency-from      $UserCurrency - to currency
     { 
         global $DB;
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
                 $DBB = manualConnect();  
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
     
     function manualConnect()
    {
             $port=31006;
        $DB = new mysqli("localhost","bitrix","a251d851","bitrix",$port);
        $DB->set_charset("utf8");
              $DB->query("SET NAMES 'utf8'");
        return $DB;
    } 
    
    function UserInfoUpdate($ts_client_array)
    {
       global $USER;  
       $user_login= $USER->GetLogin();
       $user_email=$USER->GetEmail();
       $user_firstname=$USER->GetFirstName();
       $user_lastname=$USER->GetLastName(); 
       $fields=Array();
       
       $toUpdate=false;
       if ($user_email!=$ts_client_array['EMAIL'])
       {
          if (preg_match("/.*@.*/i",$ts_client_array['EMAIL'])) 
          {  
               $fields['EMAIL']=$ts_client_array['EMAIL'];  
              $toUpdate=true;
          }
           
          
           
       }
       if ($user_firstname!=$ts_client_array['FIRST_NAME'])
       {
          if ($ts_client_array['FIRST_NAME']!="")
          {   
              $fields['NAME']=$ts_client_array['FIRST_NAME'];
              $toUpdate=true; 
          } 
                     
           
       }
       if($user_lastname!=$ts_client_array['LAST_NAME'])
       {
          if ($ts_client_array['LAST_NAME']!="")
          {
              $fields['LAST_NAME']=$ts_client_array['LAST_NAME'];
              $toUpdate=true; 
          }
           
       } 
      if ($toUpdate)
      {
         
        $USER->Update($USER->GetID(),$fields);  
          
          
      } 
       
        
    }
?>


 <?
 
     if (isset($_POST['REGISTER']))
     {
        $login=$POST['USER_LOGIN'];
        $name=$POST['USER_NAME'];
        $last_name=$_POST['USER_LAST_NAME'] ;
        $password=$_POST['USER_PASSWORD'];
        $email=$_POST['USER_EMAIL']; 
           
          
        
        
        COption::SetOptionString("main","new_user_registration_email_confirmation","N");
        COption::SetOptionString("main","captcha_registration","N");
         
          $arResult=$USER->Register($login,$name,$last_name,$password,$password,$email,false,"","");
            
        COption::SetOptionString("main","new_user_registration_email_confirmation","Y");
        COption::SetOptionString("main","captcha_registration","Y");
        
         $USER->Authorize($USER->GetID(),true);  
         
      }
    
  
    if (!$USER->IsAuthorized())     
    {
       $cookie_login = ${COption::GetOptionString("main", "cookie_name", "BITRIX_SM")."_LOGIN"};
       $cookie_md5pass = ${COption::GetOptionString("main", "cookie_name", "BITRIX_SM")."_UIDH"};
       if ($cookie_login!="" && $cookie_md5pass!="")
       {
           $USER->LoginByHash($cookie_login, $cookie_md5pass);   
       } else
       {
          if ($cookie_login!="")
          {
              
            $USER->Authorize(GetUserIDByLogin ($cookie_login),true);  
              
          } 
           
           
       }          
        
        
        
    }
    
      if (isset($_SESSION['CLIENT']))
       {
           
          $ts_client=$_SESSION['CLIENT']; 
          UserInfoUpdate($ts_client); 
           
       }elseif (isset($_POST['CLIENT']))
       {
           
          $ts_client=$_POST['CLIENT'];              
          UserInfoUpdate($ts_client); 
       } else
       {
          $ts_client=false; 
       }    
       #var_dump($ts_client) ;
       if (isset($_SESSION['ITEM_ITEM_SERVICES']))
       {
           $ts_servicesArray=$_SESSION['ITEM_ITEM_SERVICES']['SERVICES'];
           $ts_itemsArray=$_SESSION['ITEM_ITEM_SERVICES']['ITEMS'];
           
       } elseif(isset($_POST['ITEM_ITEM_SERVICES']))
       {
          $ts_servicesArray=$_POST['ITEM_ITEM_SERVICES']['SERVICES'];
          $ts_itemsArray=$_POST['ITEM_ITEM_SERVICES']['ITEMS'];
           
       }else
       {
           $ts_servicesArray=false;
           $ts_itemsArray=false;
           header('Location:/tocalc.php');  
       } 
    
      
    
     if (!$USER->IsAuthorized())
     {   
         $_SESSION['CLIENT']=$ts_client;
         $_SESSION['ITEM_ITEM_SERVICES']['SERVICES']=$ts_servicesArray;
         $_SESSION['ITEM_ITEM_SERVICES']['ITEMS']=$ts_itemsArray;    
       
         $_SESSION['BACKULRSA']="/to_client_add.php";  
         header('Location:/SimpleAuth/');     
            
            
            
     }else
     {
          /**
          *   $_SESSION['ITEM_ITEM_SERVICES']['SERVICES']['BRANDCODE']
          *   $_SESSION['ITEM_ITEM_SERVICES']['SERVICES']['ITEMCODE']
          *   $_SESSION['ITEM_ITEM_SERVICES']['SERVICES']['CAPTION']
          *   $_SESSION['ITEM_ITEM_SERVICES']['SERVICES'] ['isService']=true
          *   $_SESSION['ITEM_ITEM_SERVICES']['SERVICES'] ['isItem']=false 
          */
           if (isset($ts_client['PLATE_NUMBER']))           
            $_GET['PLATE_NUMBER']=$ts_client['PLATE_NUMBER'];
           else
           $_GET['PLATE_NUMBER']="";
           
           if (isset($ts_client['USER_DATE']))           
            $_GET['USER_DATE']=$ts_client['USER_DATE'];
           else
            $_GET['USER_DATE']="";
          
           $sum=0;
           foreach  ($ts_servicesArray as $brandID=>$item)
           {
                foreach ($item as $itemcode=>$value)
                {
                    
                     $_GET['ITEMCODE']=$value['ITEMCODE'];
                     $_GET['BRANDCODE']=$value['BRANDCODE'];
                     $_GET['isService']=1;
                     $_GET['isItem']=0;                
                     $_GET['PRICE']=$value['PRICE'];
                     $_GET['QUANTITY']=$value['QUANTITY'];
                     $_GET['CAPTION']=$value['CAPTION']; 
                     $_GET['CURRENCY']="UAH";                     
                     $sum+=$_GET['PRICE']*$_GET['QUANTITY'];
                     include $_SERVER["DOCUMENT_ROOT"]."/add_to_basket.php";  
                    
                }
               
               
               
           }
           foreach  ($ts_itemsArray as $brandID=>$item)
           {
                foreach ($item as $itemcode=>$value)
                {
                    
                     $_GET['ITEMCODE']=$value['ITEMCODE'];
                     $_GET['BRANDCODE']=$value['BRANDCODE'];
                     $_GET['isService']=0;
                     $_GET['isItem']=1;                
                     $_GET['PRICE']=$value['PRICE'];
                     $_GET['QUANTITY']=$value['QUANTITY'];
                     $_GET['CAPTION']=$value['CAPTION']; 
                     $_GET['CURRENCY']="UAH";
                     $sum+=$_GET['PRICE']*$_GET['QUANTITY'];
                     include $_SERVER["DOCUMENT_ROOT"]."/add_to_basket.php";  
                    
                }
               
               
               
           }
          ################################################################
          
               $basketUserID = CSaleBasket::GetBasketUserID();
                         $dbBasketItems = CSaleBasket::GetList(
                            array(
                                    "ID" => "ASC"
                                ),
                            array(
                                    "FUSER_ID" => $basketUserID,
                                    "LID" => SITE_ID,
                                    "ORDER_ID" => "NULL"
                                ),
                            false,
                            false,
                            array("ID", "PRODUCT_ID","NAME", "QUANTITY","CURRENCY",
                                  "CAN_BUY", "PRICE", "NOTES")
                       );
                       
                       $_GET['TOTAL_SUM']=0.00;
                      while ($arItem=$dbBasketItems->Fetch())
                      {
                        
                         if ($arItem['CURRENCY']==$_GET['CURRENCY']) 
                         {
                            $_GET['TOTAL_SUM']+=($arItem['PRICE']*$arItem['QUANTITY']);  
                         } else
                         {
                             $_GET['TOTAL_SUM']+= ($arItem['PRICE']*$arItem['QUANTITY'])*PriceKoef($arItem['CURRENCY'] , $_GET['CURRENCY']);
                             
                         }
                         
                        
                          
                      }
          
          
          
          ################################################################ 
         
          $arFields = array(
                           "LID" => SITE_ID,
                           "PERSON_TYPE_ID" => 1,
                           "PAYED" => "N",
                           "CANCELED" => "N",
                           "STATUS_ID" => "N",
                           "PRICE" => $_GET['TOTAL_SUM'],
                           "CURRENCY" => $_GET['CURRENCY'],
                           "USER_ID" => IntVal($USER->GetID()),
                           #"PAY_SYSTEM_ID" => ,
                           "ALLOW_DELIVERY"=> N,
                           "TAX_VALUE" => 0.0,
                           "USER_DESCRIPTION" => "",
                           "REGION_CODE"  => 1
                            );



                     $ORDER_ID = CSaleOrder::Add($arFields);
                     CSaleBasket::OrderBasket($ORDER_ID, $_SESSION["SALE_USER_ID"], SITE_ID);
         
          #var_dump($cookie_login); 
          #var_dump($cookie_md5pass);
          if ($ORDER_ID>0)
          {
              $_GET['ORDER_ID']=$ORDER_ID;
             include "send_info.php"; 
             if ($GLOBALS['LNG']=='UKR') header("Location:/UKR/order_check.php");
             else header("Location:/order_check.php")
             ; 
             # echo  $ORDER_ID;
          } else
          {
              echo "ERROR";
              
          }
         unset($_POST['ITEM_ITEM_SERVICES']);
         unset($_SESSION['ITEM_ITEM_SERVICES']) ;
         unset($_SESSION['CLIENT']);
         unset($_POST['CLIENT']); 
         
     } 
    
    
 ?> 




<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>