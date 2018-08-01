<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!-->
<html lang="ru">
<!--<![endif]-->
   <?
     function CheckBrowser($HTTPUSERAGENT) 
        {
            if (strpos($HTTPUSERAGENT,"Opera") !==false)
           {
             $ua="Opera";
             $uaVers = substr($HTTPUSERAGENT,strpos($HTTPUSERAGENT,"Opera")+6,4);
           }
        elseif (strpos($HTTPUSERAGENT,"Gecko") !==false)
           {
             $ua="Netscape";
             $uaVers = substr($HTTPUSERAGENT,strpos($HTTPUSERAGENT,"Mozilla")+8,3);
           }
        elseif (strpos($HTTPUSERAGENT,"Windows") !==false)
           {
             $ua="Explorer";
             $uaVers = substr($HTTPUSERAGENT,strpos($HTTPUSERAGENT,"MSIE")+5,3);
           }
        else
           {
             $ua=$HTTPUSERAGENT;
             $uaVers=""; 
           }
            
           return  $ua."#".$uaVers;
            
        }      
    
    
   ?>
    <head>
        
        <?$APPLICATION->ShowMeta("keywords")?>
        <?$APPLICATION->ShowMeta("description")?>
        <title><?$APPLICATION->ShowTitle()?></title>
        <?$APPLICATION->ShowCSS();?>
        <?
          $useragentArray=explode("#",trim((strtolower(CheckBrowser($HTTP_USER_AGENT)))));           
         if ($useragentArray[0]=="explorer" && intval($useragentArray[1])<0 )
          {
             echo  "<p align=\"centert\"><h3>Вы используйте устаревший браузер.
             <br> Возможно не корректное отображение данных.</h3>
              
             </p> 
                  
             ";
             exit;
          }
        ?>
        
      <meta charset="utf-8">
         <link href="style_1.css" rel="stylesheet" type="text/css">
         <title>DAKAR service</title>
         <script src="/js/jquery-1.8.0.min.js" type="text/javascript"></script>
         <script src="/js/search.js" type="text/javascript"></script> 

        <!-- <script type="text/javascript" src="/Nivo-Slider-master/scripts/jquery-1.9.0.min.js"></script>-->           
         <script type="text/javascript" src="/Nivo-Slider-master/jquery.nivo.slider.js"></script>
         <?
           if(stripos($_SERVER['REQUEST_URI'],'/tocalc/tocalc.php') !== false)
              {
             
              echo "<script src='/js/tocalc.js' ></script>";
             
              } elseif ((stripos($_SERVER['REQUEST_URI'],'/catalog/catalog_dakar.php') !== false) )
              {
                  echo  "<script type='text/javascript' src='/js/catalog_dakar.js'></script>";
              }else
              {
                  echo  "<script type='text/javascript' src='/js/catalog_dakar.js'></script>";
              }
          
          
         ?>
            <link rel="stylesheet" href="/Nivo-Slider-master/themes/default/default.css" type="text/css" media="screen" />
            <link rel="stylesheet" href="/Nivo-Slider-master/themes/light/light.css" type="text/css" media="screen" />
            <link rel="stylesheet" href="/Nivo-Slider-master/themes/dark/dark.css" type="text/css" media="screen" />
            <link rel="stylesheet" href="/Nivo-Slider-master/themes/bar/bar.css" type="text/css" media="screen" />
            <link rel="stylesheet" href="/Nivo-Slider-master/nivo-slider.css" type="text/css" media="screen" />
     <script>
      $(function(){
       feexed_width=1311;        
       window_width=$(window).width();
      // alert(window_width);
       if (feexed_width<window_width)
       {  
          koef=feexed_width/window_width;
          need_width=Number(feexed_width*koef);
          need_width_percent=need_width*100/window_width;
          margin_left_percent=(100-need_width_percent)/2;
          $("body").css("width",""+Number(need_width_percent)+"%");
          $("body").css("margin",""+Number(margin_left_percent)+"%");
          $("body").css("margin-top","0px");
         // alert(margin_left_percent); 
       } 
       
      });
    </script>
 <!--<script src="/js/jquery-1.8.0.min.js" type="text/javascript"></script>-->
  
         
    </head>
       <body>
       <?#$APPLICATION->ShowPanel();?>
        <?
            if (!function_exists(GetUserIDByLogin )) 
       {  
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
       }
             global $USER;
             
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
                      
                    $USER->Authorize(GetUserIDByLogin($cookie_login),true);  
                      
                  } 
                   
                   
               }          
                
                
                
            }
            if  ($USER->IsAuthorized() )
            {
              
             
                CModule::IncludeModule("sale");
                CModule::IncludeModule('iblock');
                $basketUserID = CSaleBasket::GetBasketUserID();   
             
                 
             # if (CModule::IncludeModule("sale")&& CModule::IncludeModule('iblock') )   
               #$basketUserID = CSaleBasket::GetBasketUserID();
               #var_dump ($basketUserID);
                ?>
                 <script>
                   $(document).ready(function(){
                       
                       
                       params={};
                      // params.BASKET_USER_ID=<?#=$basketUserID?>;
                        params.BASKET_USER_ID_COUNT="Y";
                       $.ajax({
                                 type:"POST",
                                 url:"/basket_check.php",
                                 dataType:"html", 
                                 data: params,
                                 cache:false,       
                                 success:function(data)
                                 {
                                    $("#basket_count").html("<img src='/images/bskt.png' width='50px'/> ("+data+")"); 
                                    //alert(data); 
                                 },        
                                 error: function(XMLHttpRequest, textStatus, errorThrown)
                                   {
                                       $("#basket_count").html("");
                                       $("#basket_count").html(textStatus);  
                                       
                                   }   
            
                              })

                   });
                 
                 
                 </script>               
            <?            
                
            }
        
        
            ?> 
      <div id='show_info_back_ground' style="overflow:none; position: absolute;left:0;display:none;width:100%;height:100%;background-color:rgba(0,0,0,0.6);z-index: 998;">
        <div id='show_info' style="display:none;position: absolute;width:30%;height:auto;left:35%;top:5%;z-index:999;"> 
  
       </div>
    </div>
        <div id="main">   
        <div class="main">    
               <div id="poloska">               </div>
               <div id="logotip"> 
                  <img id="dakar" src="<?=$_SERVER["SERVER_ROOT"]?>/bitrix/templates/DAKAR/images/logo.png"/> 
               </div> 
               <div id='contacts'>
                 <h1  align="center" style="font-size:20px; font-family: Blogger_Sans; /*text-shadow: 0.1px 0.1px 1px #1C1C1B*/;">Отдел Запчастей: <span>(098)622-23-80</span> <br> &nbsp СТО: <span>(067)925-19-24 </span>
                     &nbsp<br> e-mail: <span>dakar@dakar.in.ua </span> 
                 
                  </h1>  
                   <!--  <h1  align="center">Отдел Запчастей: <span>(098)622-23-80</span> </h1>
                     <h1  align="center">Отдел СТО: <span>(067)925-19-24 </span> </h1> -->
               </div>
              <div id='lupa'> 
              <a href="basket_check.php?BASKET_SHOW=Y" style="margin-top: 95px;margin-right: 10px; float: right;">
                <span id='basket_count'> <img src="/images/bskt.png" width="50px">(0)</span></a>
                  
                 
              </div>
              <div id="poisk">
              <img src="<?=$_SERVER["SERVER_ROOT"]?>/bitrix/templates/DAKAR/images/lupa.png" style='float:left;width:8%; height:100%; margin-left:-8%; '/>
              <input style=" outline-color: white; border: solid 1px white; margin:1%; width:45%;height: 65%;float:left;" id="search_value" type="text" name="ITEM_CODE" size="10" value="">               </input><div id="poisk1">
               <a href="#"><p align="center" id='search_button' style="margin-top:0%;font-family:Blogger_Sans;font-weight:medium; font-size:18pt; color:#F2F1F1;">Поиск</p></a>
              </div>
              </div>
              <div id="poloska1">
              </div> 
           <div id="menu" style="width:100%;float:left;">
              <div id="glavnaia" style=' box-shadow: 2px 5px 10px #1C1C1B;'> 
                  <img src="/bitrix/templates/DAKAR/images/dom.png" style='width:35%; height:90%; margin-left:35%;margin-top:2%;'>
              </div>
              <div id ="glavnaia2" class='menu' style='position:relative;'>
                  <a href="/index.php"><p align='center'> Главная  </p> </a>
                  <ul id ="glavnaia2_ul" style='z-index:999;margin-top:0px; display:none; position:absolute; width:100%; padding:0; margin-left:-1px;'>
                  <li ><a style="color:black;"  href="/order_check.php"><p>Галерея</p></a></li>
                 
                 </ul>
              </div>
               <div id ="glavnaia3" class='menu' style='position:relative;'>
                <a href="/oplata.php"><p align='center'> Оплата/Доставка </p></a>
              </div>
              <div id ="glavnaia4" class='menu' style='position:relative;'>
                <a href="/service.php"><p align='center'> Услуги СТО  </p></a>
                 <ul id ="glavnaia4_ul" style='z-index:999;margin-top:0px; display:none; position:absolute; width:100%; padding:0; margin-left:-1px;'>
                  <li ><a style="color:black;"  href="/tocalc/tocalc.php"><p>Калькулятор ТО</p></a></li>
                 
                 </ul>
              </div>
               <div id ="glavnaia5" class='menu' style='position:relative;'>
                 <a href="/contact.php"><p align='center'> Контакты  </p></a>
               <!--  <ul id ="glavnaia5_ul" style='z-index:999;margin-top:0px; display:none; position:absolute; width:100%; padding:0; margin-left:-1px;'>
                  <li ><a style="color:black;"  href="/order_check.php"><p>СТО</p></a></li>
                 
                 </ul>  -->
              </div>
               <div id ="glavnaia6" class='menu' style='position:relative;'>
                 <a href="/order_check.php"><p align='center'> Личный Кабинет </p></a>
                 <ul id ="glavnaia6_ul" style='z-index:999;margin-top:0px; display:none; position:absolute;left:0px; width:100%; padding:0; margin-left:-1px;'>
                  <li ><a style="color:black;"  href="/order_check.php"><p>Заказы</p></a></li>
                  <li ><a style="color:black;"  href="/basket_check.php?BASKET_SHOW=Y"><p>Корзина</p></a></li>
                 </ul>
              </div>
             </div> 
              <script>
                  $(".menu").mouseover(function(){
                     id=$(this).attr("id");
                     $("div#"+id).css("z-index","9999");  
                     $("div#"+id).css("background","#C01717");
                      $("div#"+id).css("box-shadow","2px 5px 10px #C01717");  
                     $("div#"+id+" ul#"+id+"_ul").show(500); 
                  })
                  $(".menu").mouseleave(function(){
                      $("div#"+id).css("background","#2D2E2E");
                      $("div#"+id).css("box-shadow","2px 5px 10px #1C1C1B"); 
                     $("div#"+id+" ul#"+id+"_ul").hide(500); 
                      
                      
                  })
                  $(".menu li").mouseover(function(){
                      
                      $(this).css("background","#C01717");
                      
                      
                  })
                  $(".menu li").mouseleave(function(){
                      
                     $(this).css("background","#2D2E2E");  
                      
                      
                  })
                 
              </script>
              </div>  
       <div id="poloska">               </div> 
       </div>
       
              
       <div class="container" id='' style='min-height: 350px; background: linear-gradient(to top,#F5F5F5,#6F6F6E ) no-repeat;' >           
         
        
          <?
            if(stripos($_SERVER['REQUEST_URI'],'/tocalc/tocalc.php') === false)
            {
             ?>
                <div id='online_catalog'> 
             <?   
              include $_SERVER["DOCUMENT_ROOT"]."/catalog/catalog_dakar.php" ;
             ?>
               </div>         
             <?
            }
          ?>
        
       <div  id='search_result' style="float:left; margin-left:1%; width:70%;">   
              
