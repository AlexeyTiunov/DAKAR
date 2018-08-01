<?php 
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
session_start();
global $USER;
# 
if ($USER->IsAuthorized())     
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


 if (!$USER->IsAuthorized())
     {   
         
       
         $_SESSION['BACKULRSA']="/ts_queue.php";
         $_SESSION['MASSAGE']="Пройдите авторизацию.";  
         header('Location:/SimpleAuth/');     
            
            
            
     }else
     {
       $user_login= $USER->GetLogin();
       $user_email=$USER->GetEmail();
       $user_firstname=$USER->GetFirstName();
       $user_lastname=$USER->GetLastName();
       
       if ($user_login==$user_firstname && $user_login==$user_lastname )
       {
           
       }else
       {
           $_POST['EMAIL']=$user_email;
           $_POST['LAST_NAME']=$user_lastname;
           $_POST['FIRST_NAME']=$user_firstname; 
             
       }
        
       
         
         
     }
     
$item_item_services=false;
if (isset($_POST['ITEM_ITEM_SERVICES']['SERVICES']) && isset($_POST['ITEM_ITEM_SERVICES']['ITEMS']) )
{
     $_SESSION['ITEM_ITEM_SERVICES']=$_POST['ITEM_ITEM_SERVICES']; 
     $action="/to_client_add.php";
     
}  else
{
    $action="/tocalc/tocalc.php";
}

#var_dump($_SESSION['ITEM_ITEM_SERVICES'])  
?>
  <div class="blankSeparator"></div>
   <style>
      #calendar input{
       
       display:inline;
          
          
          
      }
   </style>
   
   <div id='' style="margin: auto; width:50%;">
     <form name="Онлайн заявка" action="<?=$action?>" method="POST" enctype="multipart/form-data">
        <input type="hidden" name='REGISTER' value="YES">
        <table>
        <tbody>
          <tr>
             <td>
                <p style="font-size:11; color:#000000;
                font-family: verdana, serif;" align="left">Фамилия</p>
             </td>
             <td>
              <input type="text" name="CLIENT[LAST_NAME]" size="23" value="<?=$_POST['LAST_NAME']?>" maxlength="30" style="border: 1px black solid; font-size: 11px; width:300; color:black;">

             </td>

          </tr>
          <tr>
             <td>
                <p style="font-size:11; color:#000000;
                font-family: verdana, serif;" align="left">Имя</p>
             </td>
             <td>
              <input type="text" name="CLIENT[FIRST_NAME]" size="23" value="<?=$_POST['FIRST_NAME']?>" maxlength="30" style="border: 1px black solid; font-size: 11px; width:300; color:black;">

             </td>

          </tr>
          
          <tr>
             <td>
                 <p style="font-size:11; color:#000000;
                font-family: verdana, serif;" align="left">E-mail</p>
             </td>
             <td>
                <input type="text" name="CLIENT[EMAIL]" size="23" value="<?=$_POST['EMAIL']?>" maxlength="30" style="border: 1px black solid; font-size: 11px; width:300; color:black;">
             </td>
          </tr>
          <tr>
             <td>
                <p style="font-size:11; color:#000000;
                font-family: verdana, serif;" align="left">Госномер авто</p>
             </td>
             <td>
                 <input type="text" name="CLIENT[PLATE_NUMBER]" size="23" value="" maxlength="30" style="border: 1px black solid; font-size: 11px; width:300; color:black;">
             </td>
          </tr>
          <tr>
             <td>
                <p style="font-size:11; color:#000000;
                font-family: verdana, serif;" align="left">Желаемая дата визита </p>
             </td>
             <td id='calendar' style=''>
                 <?
                    $date=date("d.m.Y");
                    $APPLICATION->IncludeComponent(
                        "bitrix:main.calendar",
                        "",
                        Array(
                            "SHOW_INPUT" => "Y",
                            "FORM_NAME" => "",
                            "INPUT_NAME" => "CLIENT[USER_DATE]",                            
                            "INPUT_VALUE" => "{$date}",
                            "INPUT_VALUE_FINISH" => '01.06.2012',
                            "SHOW_TIME" => "N"
                        )
                    );
                 ?>

             </td>
           </tr>
            </tbody>                             
            </table>
             <script>  
              // $("td#calendar > input").attr("disabled","disabled");
             </script>
             <input type="submit" value="Записаться на ТО" style=" font-size: 20px; width:180; background-Color:#92caaf;margin-bottom: 2%; color:black;">





    </form>
     <p></p>

 </div>
  <div class="blankSeparator"></div>
<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>