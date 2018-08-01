<?
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php"); 
    global $USER; 
    session_start();
    $_SESSION['MASSAGE_MOBILE']="Введите номер вашего мобильного в формате 380501234567.";
    $_SESSION['MASSAGE_SMSCODE']="Введите номер из смс.";
    //var_dump($_SESSION['BACKULRSA']);
    function DecipherCheckWord($checkWord)
    {
        
        
        
        return  $checkWord;
    }     
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
     function SendSMSCode($code,$phone)
     {
        require_once($_SERVER["DOCUMENT_ROOT"].'/Alphasmsclient.class.php'); 
        $sms = new SMSclient('380635168220', 'T26071980', '9333968faf8a40850dd8ebfc20aaa33565602a27');
        $SmsText=$code; 
        $id = $sms->sendSMS('Alex', $phone, $SmsText);  
        var_dump($sms->hasErrors() ); 
     }
    if( isset($_POST['CHECK']) && isset($_POST['LOGIN']))      
    {
       
       $userCheckWord=$_POST['CHECKWORDS'];
       $checkWord=DecipherCheckWord($_SESSION['CHECKWORD']);
       
       $loginID=GetUserIDByLogin($_POST['LOGIN']); 
       if ($loginID>0 && $checkWord==$userCheckWord )
       {  
             $USER->Authorize($loginID,true);
             echo $USER->GetID(); 
            # $cookie_login = ${COption::GetOptionString("main", "cookie_name", "BITRIX_SM")."_LOGIN"};
            # $cookie_md5pass = ${COption::GetOptionString("main", "cookie_name", "BITRIX_SM")."_UIDH"};
              $USER->LoginByHash($cookie_login, $cookie_md5pass);
            if (isset($_SESSION['BACKULRSA']))
            {
                 header("Location:{$_SESSION['BACKULRSA']}");
                
            }
            else
            {
                header("Location:/index.php");
            }  
            
            
           
       } 
       elseif ($loginID==0 && $checkWord==$userCheckWord )
       {
            
            $login=$_POST['LOGIN'];
            $name=$_POST['LOGIN'];
            $last_name=$_POST['LOGIN'] ;
            $password="Aa123456!";
            $email=$_POST['LOGIN'].'@'.$_POST['LOGIN'].'com.ua'; 
               
              
            
            
            COption::SetOptionString("main","new_user_registration_email_confirmation","N");
            COption::SetOptionString("main","captcha_registration","N");
             
              $arResult=$USER->Register($login,$name,$last_name,$password,$password,$email,false,"","");
                
            COption::SetOptionString("main","new_user_registration_email_confirmation","Y");
            COption::SetOptionString("main","captcha_registration","Y");
            
             $USER->Authorize($USER->GetID(),true); 
             if (isset($_SESSION['BACKULRSA']))
            {
                 header("Location:{$_SESSION['BACKULRSA']}");
                
            }else
            {
                header("Location:/index.php");
            }   
           
       }elseif($checkWord!=$userCheckWord ) 
       {  
           $_SESSION['MASSAGE_ERROR']="Не верный код из смс"; 
            header("Location:/SimpleAuth/index.php");            
       }
        
          
        
    }
    elseif( isset($_POST['SEND_SMS']) && isset($_POST['LOGIN'])) 
    {
        $rand=rand(1000,9999);
        var_dump($rand);
        SendSMSCode($rand,$_POST['LOGIN']);
        $_SESSION['CHECKWORD']=$rand;           
        ?>         <div align="center" style="margin: 5%;"> 
          <form action="/SimpleAuth/" method='POST' enctype="multipart/form-data">
              <p style="font-size:24px;"><?=$_SESSION['MASSAGE_ERROR']?></p>
              <p style="font-size:24px;"><?=$_SESSION['MASSAGE_SMSCODE']?></p>  
              <input type='text' name='LOGIN' value="<?=$_POST['LOGIN']?>">  </input>
              <input type='text' name='CHECKWORDS' value="">  </input> 
              <input type='hidden' name='CHECK' value="">  </input>   
            <input style="width: 10%;height: 32px;" type='submit' value='OK'>  
           </form>
           </div>
        <?
        
        
    }    
    elseif ($_POST['REGISTER'])
    {
        
        
        
        
        
    }else
    {
         
         ?>
         <div align="center" style="margin: 5%;">   
                 <form action="/SimpleAuth/" method='POST' enctype="multipart/form-data">
              <p style="font-size:26px;color:red ;"><?=$_SESSION['MASSAGE_ERROR']?></p> 
              <p style="font-size:24px;"><?=$_SESSION['MASSAGE']?></p>
              <p style="font-size:24px;"><?=$_SESSION['MASSAGE_MOBILE']?></p> 
              <input type='text' name='LOGIN' value="<?=$_POST['LOGIN']?>">  </input>
              <input type='hidden' name='SEND_SMS' value="">  </input> 
            <input style="width: 10%;height: 32px;" type='submit' value='OK'>  
           </form>
         </div>
         <?
        
         unset($_SESSION['MASSAGE_ERROR']);
        
        
    }
?>
   
    




<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>