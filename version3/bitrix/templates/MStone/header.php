 <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?echo LANG_CHARSET;?>">
<?$APPLICATION->ShowMeta("keywords")?>
<?$APPLICATION->ShowMeta("description")?>
<title><?$APPLICATION->ShowTitle()?></title>
<?$APPLICATION->ShowCSS();?>
<?$APPLICATION->ShowHeadStrings()?>
<?$APPLICATION->ShowHeadScripts()?>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script> 
<script>
        $(function() 
           {
         clientheight=($(window).height());
         clientwidth=($(window).width()-500);       
         $("#common").css ("height",""+clientheight+"px");
         $("body").css ("width",""+clientwidth+"px");      
               
          
           
          $("#auth").click(function(){
           
            if ($("#AUTH").css("display")=="none")
            {
             $("#AUTH").css("display","block");    
            }  else
            {
                $("#AUTH").css("display","none");    
            }
            
              
          }) 
           
           });
  </script>  
  <script>
  $(window).resize(function(){
      clientheight=($(window).height());
         clientwidth=($(window).width()-500);       
         $("#common").css ("height",""+clientheight+"px");
         $("body").css ("width",""+clientwidth+"px");       
      
      
  }) ;
  </script>       

</head>
<body> <?#$APPLICATION->ShowPanel();?>
<div id="common">
<div id="AUTH">
      <?
      $APPLICATION->IncludeComponent("bitrix:system.auth.form", "", array(
    "REGISTER_URL" => "/personal/profile/index.php",
    "PROFILE_URL" => "/personal/profile/",
    "SHOW_ERRORS" => "N"
    ),
    false
);
     ?>
     </div>
<div id="main">
 <div id="head">
   <div id="logo">  </div>
   <div id="title"> 
   <a href="#" id="auth" style="text-decoration: none;"><p class="bigtext" align="center">АВЕНТИН</p> </a>
   <p class="smalltext" align="center">гранитные памятники</p>
   
   </div>
 </div>
 <nav style="margin: auto;">
            <ul>
                <li><a href="/index.php">Главная</a></li>
                <li><a href="/Galery.php?StoneType=4&StoneKind=1">Галерея</a></li>
                <li><a href="/PriceList.php">Прайс-Лист</a></li>
               
                <li><a href="/contacts.php">Акции</a></li> 
                <li><a href="/contacts.php">Контакты</a></li> 
            </ul>
        </nav> 
        
        
   
  