<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!-->
<html lang="ru">
<!--<![endif]-->
    <?#$APPLICATION->ShowPanel = true; $APPLICATION->ShowPanel();?>
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
          <meta name="viewport" content="width=device-width, initial-scale=1.0">  
          <link href="style_1.css" rel="stylesheet" type="text/css">
          <title>Dakar &mdash; service</title>    
          <script src="/js/jquery-1.8.0.min.js" type="text/javascript"></script>
          <script src="/js/search.js?V=3" type="text/javascript"></script> 

          <!-- <script type="text/javascript" src="/Nivo-Slider-master/scripts/jquery-1.9.0.min.js"></script>-->           
          <script type="text/javascript" src="/Nivo-Slider-master/jquery.nivo.slider.js"></script>
         <?
           if(stripos($_SERVER['REQUEST_URI'],'/tocalc/tocalc.php') !== false)
              {
             
              echo "<script src='/js/tocalc.js' ></script>";
             
              } elseif ((stripos($_SERVER['REQUEST_URI'],'/catalog/catalog_dakar.php') !== false) )
              {
                  echo  "<script type='text/javascript' src='/js/catalog_dakar.js?V_2'></script>";
              }else
              {
                  echo  "<script type='text/javascript' src='/js/catalog_dakar.js?V_2'></script>";
              }
          
          
         ?>
            <link rel="stylesheet" href="/Nivo-Slider-master/themes/default/default.css" type="text/css" media="screen" />
            <link rel="stylesheet" href="/Nivo-Slider-master/themes/light/light.css" type="text/css" media="screen" />
            <link rel="stylesheet" href="/Nivo-Slider-master/themes/dark/dark.css" type="text/css" media="screen" />
            <link rel="stylesheet" href="/Nivo-Slider-master/themes/bar/bar.css" type="text/css" media="screen" />
            <link rel="stylesheet" href="/Nivo-Slider-master/nivo-slider.css" type="text/css" media="screen" />
          <!--   from version 3 --> 
            <link href='http://fonts.googleapis.com/css?family=Dosis:300,400' rel='stylesheet' type='text/css'>
            <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300' rel='stylesheet' type='text/css'>
            <link rel="stylesheet" href="http://netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css">
            <link rel="stylesheet" href="/assets/dest/css/font-awesome.min.css">
            <link rel="stylesheet" href="/assets/dest/rs-plugin/css/settings.css">
            <link rel="stylesheet" href="/assets/dest/rs-plugin/css/responsive.css">
            <link rel="stylesheet" href="/assets/dest/vendors/colorbox/example3/colorbox.css">
            <link rel="stylesheet" title="style" href="/assets/dest/css/style.css">
            <link rel="stylesheet" href="/assets/dest/css/animate.css"> 
          <!--   from version 3 end -->   
            
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
          <?
           if(!class_exists('CUserOptions'))
         include_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/classes/".$GLOBALS['DBType']."/favorites.php");
          
          
          $APPLICATION->ShowPanel();
        var_dump($GLOBALS['DBType']);
          ?> 
       <body>
      
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
                                   // $("#basket_count").html("<img src='/images/bskt.png' width='50px'/> ("+data+")"); 
                                   $("#basket_count").html(""+data+"");
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
            <div id='show_info_back_ground' style="overflow:none; position: absolute;left:0;display:none;width:100%;height:100%;background-color:rgba(0,0,0,0.6);z-index: 998;"> </div>
            <div id='show_info' style="display:none;position: absolute;width:30%;height:auto;left:35%;top:5%;z-index:999;">        </div>      
                 <div id="header">
        <div class="header-bottom">
            <div class="container">
                <a class="visible-xs beta-menu-toggle pull-right" href="#">
                <i class="fa fa-bars"></i></a>

                <div class="visible-xs clearfix"></div>
                <nav class="main-menu">
                    <ul class="l-inline ov">
                        <li><a href="/index.php">Главная</a></li>
                        <li><a href="/about.php">О нас</a></li>
                        <li><a href="delivery_payment.php">Доставка и оплата</a></li>
                        <li><a href="/catalog.php">Каталог з/ч</a></li>
                        <li><a href="/tocalc/tocalc.php">Калькулятор ТО</a></li>
                        <li><a href="/auto_service.php">Автосервис</a></li>
                        <li><a href="/index.php">Отзывы</a></li>
                        <li><a href="/contacts.php">Контакты</a></li>
                        
                        
                    </ul>
                    <div class="clearfix"></div>
                </nav>
            </div> <!-- .container -->
        </div> <!-- .header-bottom -->


        
        <!-- .header-top -->
        <div class="header-body">
            <div class="container beta-relative">
                <div class="pull-left">
                    <a href="/index.php" id="logo"><img src="/assets/dest/images/logo.png" alt="" width="200em"></a>
                    <span class="slogan">Магазин - (098) 622 23 80                     Автосервис - (068) 841 25 13
                    </span>
                </div>
                <div class="pull-right">
                    <script type="text/javascript"> 
                    var css_file=document.createElement("link"); 
                    css_file.setAttribute("rel","stylesheet"); 
                    css_file.setAttribute("type","text/css"); 
                    css_file.setAttribute("href","//s.bookcdn.com//css/cl/bw-cl-120x45.css"); 
                    document.getElementsByTagName("head")[0].appendChild(css_file); </script>
                     <div id="tw_6_613442938">
                       <div style="width:130px; height:45px; margin: 0 auto;">
                         <!-- <a href="//nochi.com/time/kiev-18881">Киев</a><br/>   -->
                       </div>
                     </div>
                     <script type="text/javascript"> function setWidgetData_613442938(data)
                     { 
                         if(typeof(data) != 'undefined' && data.results.length > 0) 
                         { 
                             for(var i = 0; i < data.results.length; ++i) 
                              { 
                                  var objMainBlock = ''; 
                                  var params = data.results[i]; 
                                  objMainBlock = document.getElementById('tw_'+params.widget_type+'_'+params.widget_id); 
                                 // if(objMainBlock !== null)  
                                  //objMainBlock.innerHTML = params.php_code; 
                              
                              } 
                         }
                     } 
                     var clock_timer_613442938 = -1; 
                     </script>  
                     <script type="text/javascript" charset="UTF-8" src="http://nochi.com/?page=get_time_info&ver=2&domid=589&type=6&id=613442938&scode=124&city_id=18881&wlangid=20&mode=0&details=0&background=ffffff&color=333333&add_background=a0a1a1&add_color=e74d3c&head_color=333333&border=3&transparent=0"></script>
    <!-- clock widget end -->
                </div>
                    <div class="pull-right beta-components space-left ov">
                    <!-- Gismeteo informer START -->
                    
                    <link rel="stylesheet" type="text/css" href="https://s1.gismeteo.ua/static/css/informer2/gs_informerClient.min.css">
                    <div id="gsInformerID-7Tq30WM4I1l1GR" class="gsInformer" style="width:180px;height:131px">
                      <div class="gsIContent">
                       <div id="cityLink">
                         <a href="https://www.gismeteo.ua/weather-kyiv-4944/" target="_blank">Погода в Киеве</a>
                       </div>
                       <div class="gsLinks">
                         <table>
                           <tr>
                             <td>
                               <div class="leftCol">
                                 <a href="https://www.gismeteo.ua" target="_blank">
                                   <img alt="Gismeteo" title="Gismeteo" src="https://s1.gismeteo.ua/static/images/informer2/logo-mini2.png" align="absmiddle" border="0" />
                                   <span>Gismeteo</span>
                                 </a>
                               </div>
                               <div class="rightCol">
                                 <a href="https://www.gismeteo.ua/weather-kyiv-4944/" target="_blank">Прогноз</a>
                               </div>
                               </td>
                            </tr>
                          </table>
                        </div>
                      </div>
                    </div>
                    <script src="https://www.gismeteo.ua/ajax/getInformer/?hash=7Tq30WM4I1l1GR" type="text/javascript"></script>
                    <!-- Gismeteo informer END -->

                    
                </div>
                <div class="clearfix"></div>
            </div> <!-- .container -->
        </div> <!-- .header-body -->



        <div class="header-button">
            <div class="container">
                <div class="pull-left auto-width-left">
                    <div class="beta-comp" >
                        <a href="#" class="beta-btn primary beta-btn-medium" id="vin">Запрос по VIN-коду <i class="fa fa-chevron-right"></i></a>
                    </div>
                </div>
                <div class="pull-left auto-width-left">
                    
                        <div  id="searchform">
                            <input type="text" value="" name="s" id="search_value" placeholder="Введите номер запчасти" />
                            <button class="fa fa-search" type="submit" id="search_button"></button>
                        </div>
                    
                </div>
                <div class="pull-right auto-width-righ">
                    <div class="cart">
                            <div class="beta-selec"><a href="/order_check.php"><i class="fa fa-user"></i>Кабинет </div>
                    </div>
                </div>
                <div class="pull-right auto-width-right">
                    <div id="basket">
                        <div class="cart">
                            <div class="beta-select">
                            <style>
                           
                            </style>
                            <a href='/basket_check.php?BASKET_SHOW=Y' id='basketC'>&#xf07a</a>
                            <i class="f fa-chevron-dow"></i>
                            </div>                                                     
                            
                        </div> <!-- .cart -->
                    </div> 
                </div>
                <div class="clearfix"></div>
            </div> <!-- .container -->
        </div> 




    </div> <!-- #header -->
     <div class="space50">&nbsp;</div>        
    <div class="col-sm-3 aside">    
          <?
            if(stripos($_SERVER['REQUEST_URI'],'/tocalc/tocalc.php') === false
                && stripos($_SERVER['REQUEST_URI'],'/about.php') === false
                && stripos($_SERVER['REQUEST_URI'],'/auto_service.php') === false
                && stripos($_SERVER['REQUEST_URI'],'/our_work.php') === false              
             && stripos($_SERVER['REQUEST_URI'],'/contacts.php') === false 
            
               )
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
          
   <script>
      // var search_block=".content #row";
      
     
   </script>        
        
   </div> <!-- .col-sm-3 aside -->    
   <div class="container">  
     <div class="content">  
       <div class="row" id='row'> 