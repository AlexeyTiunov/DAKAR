<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<!--
<!DOCTYPE html PUBLIC  "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
-->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?echo LANG_CHARSET;?>">
<!--<meta name='yandex-verification' content='4bb9380d20d31a5b' /> -->
<meta name='yandex-verification' content='59d9f041c36128f5' />
<link rel="SHORTCUT ICON" HREF="/logo3.png">
<?$APPLICATION->ShowMeta("keywords")?>
<?$APPLICATION->ShowMeta("description")?>
<title><?$APPLICATION->ShowTitle()?></title>
<?$APPLICATION->ShowCSS();?>
<?$APPLICATION->ShowHeadStrings()?>
<?$APPLICATION->ShowHeadScripts()?>

<?
function GetUser1CID( $ID )
{

  global $DB;
  $sql = "SELECT ID_1C FROM b_user WHERE ID='".$ID."'";

  $res = $DB->Query( $sql );

  if( $arRes = $res->Fetch() )
    return $arRes["ID_1C"];
  else
    return false;
} 
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
    
   return  $ua.$uaVers;
    
}      

echo "<script type=\"text/javascript\" src=\"/bitrix/js/itgScript/jquery.min.js\"></script>";  
if(stripos($_SERVER['REQUEST_URI'],'/personal/order/radiators/') !== false)
{
	echo "<script type=\"text/javascript\" src=\"/bitrix/js/itgScript/jquery.min.js\"></script>";
	echo "<script type=\"text/javascript\" src=\"/bitrix/components/itg/radiators/ajax.js\"></script>";
	echo "<script type=\"text/javascript\" src=\"/bitrix/components/itg/radiators/jquery-ui-1.8.14.custom.min.js\"></script>";
	echo "<link type=\"text/css\" href=\"/personal/order/radiators/jquery-ui-1.8.14.custom.css\" rel=\"stylesheet\" />";
	echo "<style type=\"text/css\">
			/*demo page css*/
			body{ font: 62.5%;}
			.demoHeaders { margin-top: 2em; }
			.popup {padding: .4em 1em .4em 14px;text-decoration: none;position: relative;}
			.popup span.ui-icon {margin: 0 5px 0 0;position: absolute;left: .2em;top: 50%;margin-top: -8px;}
			ul#icons {margin: 0; padding: 0;}
			ul#icons li {margin: 2px; position: relative; padding: 4px 0; cursor: pointer; float: left;  list-style: none;}
			ul#icons span.ui-icon {float: left; margin: 0 4px;}
		</style>";
}
if(stripos($_SERVER['REQUEST_URI'],'/personal/order/catalog/') !== false)
{
	global $itgCatalogPage;
	$itgCatalogPage = 'insert';
}
if(stripos($_SERVER['REQUEST_URI'],'/personal/order/radiators/') !== false)
{
	global $itgCatalogPage;
	$itgCatalogPage = 'radiators';
}
if(stripos($_SERVER['REQUEST_URI'],'/auth/Registration/') !== false)
{
	echo "<script type=\"text/javascript\" src=\"/bitrix/js/itgScript/jquery.min.js\"></script>";
	echo "<script type=\"text/javascript\" src=\"/auth/Registration/validateBeforeSend.js\"></script>";
}
if(stripos($_SERVER['REQUEST_URI'],'/autodoc/search') !== false)
{
	echo "<script type=\"text/javascript\" src=\"/bitrix/js/itgScript/jquery.min.js\"></script>";
	#echo "<script type=\"text/javascript\" src=\"/bitrix/components/itg/Search/ajax.js\"></script>";
}
if(stripos($_SERVER['REQUEST_URI'],'/personal/suppload') !== false)
{
	#echo "<script type=\"text/javascript\" src=\"/bitrix/js/itgScript/jquery.min.js\"></script>";
   # echo "<script type=\"text/javascript\" src=\"http://parts.avtodok.com.ua/personal/suppload/test.js\"></script>";
    #echo "<script type=\"text/javascript\" src=\"/bitrix/components/itg/Search/ajax1.js\"></script>";  
}
#echo "<script type=\"text/javascript\" src=\"/bitrix/js/itgScript/jquery.min.js\"></script>";    
echo "<script type=\"text/javascript\" src=\"/personal/suppload/test.js\"></script>"; 
echo "<script  type=\"text/javascript\" src=\"/bitrix/components/itg/Search/ajax1.js\"></script>";   
?>
<!--<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-25472309-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script> -->
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-44421695-1', 'avtodok.com.ua');
  ga('send', 'pageview');

</script>
<script>
$(function() 
   {
  clientwidth=($(window).height())-10;
$("#totalcomon").css("height",""+clientwidth+"px");
   });
</script>
<script type="text/javascript">
(function (d, w, c) {
    (w[c] = w[c] || []).push(function() {
        try {
            w.yaCounter22225360 = new Ya.Metrika({id:22225360,
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true});
        } catch(e) { }
    });

    var n = d.getElementsByTagName("script")[0],
        s = d.createElement("script"),
        f = function () { n.parentNode.insertBefore(s, n); };
    s.type = "text/javascript";
    s.async = true;
    s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";

    if (w.opera == "[object Opera]") {
        d.addEventListener("DOMContentLoaded", f, false);
    } else { f(); }
})(document, window, "yandex_metrika_callbacks");
</script>   
<script type="text/javascript">
 
function theRotator() {
    // Устанавливаем прозрачность всех картинок в 0
    $('div#rotator ul li').css({opacity: 0.0});
    $('div#rotator ul li').css("display","none");
    // Берем первую картинку и показываем ее (по пути включаем полную видимость)
    $('div#rotator ul li:first').css({opacity: 1.0});
    $('div#rotator ul li:first').css("display","block");
    // Вызываем функцию rotate для запуска слайдшоу, 5000 = смена картинок происходит раз в 5 секунд
    setInterval('rotate()',10000);
}
 
function rotate() {    
    // Берем первую картинку
    var current = ($('div#rotator ul li.show')?  $('div#rotator ul li.show') : $('div#rotator ul li:first'));
 
    // Берем следующую картинку, когда дойдем до последней начинаем с начала
    var next = ((current.next().length) ? ((current.next().hasClass('show')) ? $('div#rotator ul li:first') :current.next()) : $('div#rotator ul li:first'));    
 
    // Расскомментируйте, чтобы показвать картинки в случайном порядке
    // var sibs = current.siblings();
    // var rndNum = Math.floor(Math.random() * sibs.length );
    // var next = $( sibs[ rndNum ] );
 
    // Подключаем эффект растворения/затухания для показа картинок, css-класс show имеет больший z-index
    next.css({opacity: 0.0})
    next.css("display","block") 
    .addClass('show')
    .animate({opacity: 1.0}, 500);
 
    // Прячем текущую картинку
    current.animate({opacity: 0.0}, 500)
    current.css("display","none")
    .removeClass('show');
};
 
$(document).ready(function() {        
    // Запускаем слайдшоу
    theRotator();
});
 
</script>
</head>
<body> <?$APPLICATION->ShowPanel();?>
 
<div id="totalcomon">
  <a href="http://unitedcountry.com.ua"><img style="border: 0; position: absolute; right:15px; top: 0;" src="http://unitedcountry.com.ua/img/3" alt="Єдина Країна! Единая Страна!"></a>
<div id="comon"> 
<div id="header"> 		 
  <div id="logo1"> 		 		</div>
 
<!-- #logo1-->
 		 
  <div id="logo2"> 
   <? if ($USER->IsAuthorized())
  {
         global $USER;
         global $DB;
         $ONECID=GetUser1CID($USER->GetID());
         $sql="SELECT `BalanceOnDate`,`Caption` AS Caption, `MinPercent` AS MinPercent, `CreditSum` as CreditSum, `OrdersAllSumm`,`OrdersWorkSumm`,`CurrentDebt`,`CurrentDelayDebt` FROM `b_autodoc_agreements`
                                            WHERE
                                                   REPLACE (`Caption`,' ','')='ДОГОВОРНАЛИЧНЫЙДОЛЛАР' 
                                                AND `ClientCode`='{$ONECID}'
                                                OR REPLACE (`Caption`,' ','')='ДОГОВОРБЕЗНАЛГРН.' 
                                                AND `ClientCode`='{$ONECID}' ";
          $res = $DB->Query($sql);
          
         while( $UserInfo=$res->Fetch())
     {
          if($UserInfo['CurrentDebt']>0) 
          {
             $Debt=number_format($UserInfo['CurrentDebt'],2,',',' ') ;      
              $DebtName='Долг';
          } 
          else
          {
              if ($UserInfo['CurrentDebt']==0)
             {
                 $Debt=number_format($UserInfo['CurrentDebt'],2,',',' ') ;      
              $DebtName='Баланс';
              } else
              {
              
               $Debt=number_format($UserInfo['CurrentDebt']*-1,2,',',' ') ;      
              $DebtName='Баланс';
              }
          } 
           
          if ($UserInfo['CreditSum']==-1) 
          {
              $CreditSum="";
              #$freefunds="Неограниченно";
          } 
          else
          {
               $CreditSum="Отгрузка при 100% предоплате.";
              #$freefunds= ($CreditSum-$UserInfo['CurrentDebt']<0)? 0: $CreditSum-$UserInfo['CurrentDebt'];                           
          } 
          if (str_replace(' ','',$UserInfo['Caption'])=='ДОГОВОРБЕЗНАЛГРН.')
          { 
          $Currency='UAH' ;
          }
          elseif(str_replace(' ','',$UserInfo['Caption'])=='ДОГОВОРНАЛИЧНЫЙДОЛЛАР') {$Currency='USD' ;}
          
          $CalDebt= number_format($UserInfo['CurrentDebt']+ 
          ($UserInfo['OrdersAllSumm']*($UserInfo['MinPercent']/100)) ,2,',',' ') ; 
          
          $CurrentDelayDebt=number_format($UserInfo['CurrentDelayDebt'] ,2,',',' ') ; 
          
          
          #$freefunds= ($Debt+$CreditSum<0)? 0: $Debt+$CreditSum;                           
    /*echo"    Дополнительная Информация</br>  
             Пользователь: {$USER->GetFirstName()}
              <table>                 
                 <tr>
                  <td >{$DebtName}:</td> <td  style='width:100px;'> {$Debt} USD</td>
                </tr>
                 <tr>
                    <td >Сумма установленного  кредита:</td> <td style='width:100px;'>{$CreditSum} USD</td>
                 </tr> 
                 <tr>
                    <td>Свободных средств для покупки товара:</td> <td style='width:100px;'>{$freefunds} USD</td>
                 </tr>  
                  

                   
              </table>"; */
              echo $Currency."<br>";
              echo " {$DebtName}: {$Debt} {$Currency} <br> " ; 
              if  ($CreditSum!="") echo  $CreditSum."<br>" ;
              if ($CalDebt>0 && str_replace(' ','',$UserInfo['Caption'])!='ДОГОВОРБЕЗНАЛГРН.' ) echo "Требуется доплата: ". $CalDebt. " ".$Currency."<br>";    
              if ($CurrentDelayDebt>0 ) echo "Просроченная- <br>дебиторская задолженность: ".$CurrentDelayDebt." ".$Currency."<br>";                                                   
          
          echo"<br><br>" ;
          
     }       
  }?>            
                 </div>
   
<!-- #logo2-->

  <div id ="cinfo">
  
      <div id="cinfoin"> </div> 
      <div id="cinfoint"> office@parts.avtodok.com.ua<br>  (044)545-70-17<br> (097) 025-11-10<br> г.Киев<br> ул.Деревообрабатывающая,5<br> </div>                         
    
  
   </div>    
  <div id="kurs"> Курс валют <?$APPLICATION->IncludeComponent("bitrix:currency.rates", "template1", array(
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "86400",
	"arrCURRENCY_FROM" => array(
		0 => "USD",
		1 => "EUR",
	),
	"CURRENCY_BASE" => "UAH",
	"RATE_DAY" => "",
	"SHOW_CB" => "N"
	),
	false
);?> </div>
   
  <div id="logo3"> 		 		 </div> 
 
 		 
<!-- #search-->
 		 
  <div id="top_menu"> 
  <div id="top_menu_bar">			
  <?
  if (trim(strtolower(CheckBrowser($HTTP_USER_AGENT)))=="explorer6.0")
  {
     echo  "<p align=\"centert\"><h3>Вы используйте устаревший браузер.
     <br> Возможно не корректное отображение данных.</h3>
      
     </p> 
          
     ";
     exit;
  } else
  {
    # echo $HTTP_USER_AGENT; 
      #echo   CheckBrowser($HTTP_USER_AGENT);
  $APPLICATION->IncludeComponent("bitrix:menu", "horizontal_multilevel1", array(
	"ROOT_MENU_TYPE" => "top",
	"MENU_CACHE_TYPE" => "Y",
	"MENU_CACHE_TIME" => "3600",
	"MENU_CACHE_USE_GROUPS" => "Y",
	"MENU_CACHE_GET_VARS" => array(
	),
	"MAX_LEVEL" => "1",
	"CHILD_MENU_TYPE" => "left",
	"USE_EXT" => "Y",
	"DELAY" => "N",
	"ALLOW_MULTI_SELECT" => "N"
	),
	false,
	array(
	"ACTIVE_COMPONENT" => "Y"
	)
);
  }
?>  
        </div> 
       <div id="topmenuform"> <p align="center" style="margin-top:8px;"></p> 
         <? # <form name="datasearch" enctype="multipart/form-data" action="/autodoc/search.php?lang=s1" method="POST">  ?>
             <input style="border: solid 1px white; margin-left: 10px;"  id="forminput" type="text" name="ICODE" size="20" value="" />  
            <? #  <input  id="forminputcode"type="image" src="http://<?=$_SERVER['SERVER_NAME']/bitrix/templates/avtodok/images/button.gif"  name="button.gif" /> ?>  
           <? # </form>  ?>
           <a id="forminputcdA" href='#AcSearch'> <input  id="forminputcd"type="image" src="http://<?=$_SERVER['SERVER_NAME']?>/bitrix/templates/avtodok/images/button.gif"  name="button.gif" /></a>  
        </div>
       		

       </div>
 
<!-- #top_menu-->

           <div id ="hedpic"> 
          

               <a href="#" style="border:none;"  onclick="rotate()"> <img  style="border:none;" id="hedpicin" src="http://<?=$_SERVER['SERVER_NAME']?>/bitrix/templates/avtodok/images/HeadPicDiv.png"></a>
                <div style=" margin-left: 10px; position:absolute; margin-top: 60px; width:100%; display:;">
                   
                  <?
                   require($_SERVER['DOCUMENT_ROOT'].'/movedpic/main.php'); 
                  ?> 
                
                
                
                </div> 
                
                   
          </div>
          <!-- <div id ="underpic">   </div> --> 
 	 	</div> 
  
<!-- #header-->
 	 
<div id="middle"> 		 
  <div id="middle_up"> 
    <div id="empty"> </div>
	
<!-- #left_sb-->   
    <div id="left_sb"> 
  <!--  <div id="auth_up">АВТОРИЗАЦИЯ
      </div> <!-- #auth_up--> 
      <div id="auth">
       <img id="hedpicin" src="http://<?=$_SERVER['SERVER_NAME']?>/bitrix/templates/avtodok/images/AuthDivBack.png" / > 
       <p align="center" style="font-family: Cambria; font-style: Italic; color: #910303; font-size: 15pt; margin-top: 10px;" >Авторизация: </p>        
      <div id="authin">
        
      
       <?$APPLICATION->IncludeComponent("bitrix:system.auth.form", "template1", array(
    "REGISTER_URL" => "/personal/profile/index.php",
    "PROFILE_URL" => "/personal/profile/",
    "SHOW_ERRORS" => "N"
    ),
    false
);?> 
  </div>
</div> <!-- #auth--> 

      <div id="left_menu"> <?$APPLICATION->IncludeComponent("bitrix:menu", "vertical_multilevel1", array(
	"ROOT_MENU_TYPE" => "left",
	"MENU_CACHE_TYPE" => "Y",
	"MENU_CACHE_TIME" => "3600",
	"MENU_CACHE_USE_GROUPS" => "Y",
	"MENU_CACHE_GET_VARS" => array(
	),
	"MAX_LEVEL" => "1",
	"CHILD_MENU_TYPE" => "left",
	"USE_EXT" => "N",
	"DELAY" => "N",
	"ALLOW_MULTI_SELECT" => "N"
	),
	false,
	array(
	"ACTIVE_COMPONENT" => "Y"
	)
);?> </div>
     
<!-- #left_menu-->
<!--    <div id="empty1"> </div> -->

   <!--   <div id="auth_up">АВТОРИЗАЦИЯ
      </div> <!-- #auth_up-->
   <!--   <div id="auth"> 		 <?$APPLICATION->IncludeComponent("bitrix:system.auth.form", "template1", array(
	"REGISTER_URL" => "/personal/profile/index.php",
	"PROFILE_URL" => "/personal/profile/",
	"SHOW_ERRORS" => "N"
	),
	false
);?> 

</div> <!-- #auth--> 
    
 <div id="beforpayment"> </div> 
 <div id="payment">
  <p style="">
   </p>
  
  <a href="/personal/privatbank/liqpay.php"><img src="/personal/privatbank/logo.png" style="border:none;margin-top: 20px; margin-left:15%;width:70%"/></a><br>
  <a href="/personal/privatbank/privat24.php"><img    src="/personal/privatbank/logoprivat24.png" style="border:none; margin-left:15%;width:70%"/></a> 
   
 </div>
 <div id="left_menu_news_title" > </div> 
 <div id ="left_menu_news">
  
      <div id="left_sb_news">
      <!--<div style=" border-bottom:solid 2px #afb0b2; margin-top: -5px;  ;"> <p style=" height:20px; ; font-size:12pt;  /*font-family: Arial;*/  font-weight: ;  text-align:center;">Новости </p></div><br> -->
	   <?$APPLICATION->IncludeComponent("bitrix:news.list", "left_sb_news_list", array(
	"IBLOCK_TYPE" => "news",
	"IBLOCK_ID" => "3",
	"NEWS_COUNT" => "20",
	"SORT_BY1" => "ACTIVE_FROM",
	"SORT_ORDER1" => "DESC",
	"SORT_BY2" => "SORT",
	"SORT_ORDER2" => "ASC",
	"FILTER_NAME" => "",
	"FIELD_CODE" => array(
		0 => "",
		1 => "",
	),
	"PROPERTY_CODE" => array(
		0 => "",
		1 => "",
	),
	"CHECK_DATES" => "Y",
	"DETAIL_URL" => "",
	"AJAX_MODE" => "N",
	"AJAX_OPTION_SHADOW" => "Y",
	"AJAX_OPTION_JUMP" => "N",
	"AJAX_OPTION_STYLE" => "Y",
	"AJAX_OPTION_HISTORY" => "N",
	"CACHE_TYPE" => "Y",
	"CACHE_TIME" => "3600",
	"CACHE_FILTER" => "N",
	"CACHE_GROUPS" => "Y",
	"PREVIEW_TRUNCATE_LEN" => "",
	"ACTIVE_DATE_FORMAT" => "d.m.Y",
	"DISPLAY_PANEL" => "N",
	"SET_TITLE" => "Y",
	"SET_STATUS_404" => "Y",
	"INCLUDE_IBLOCK_INTO_CHAIN" => "Y",
	"ADD_SECTIONS_CHAIN" => "Y",
	"HIDE_LINK_WHEN_NO_DETAIL" => "N",
	"PARENT_SECTION" => "",
	"PARENT_SECTION_CODE" => "",
	"DISPLAY_TOP_PAGER" => "N",
	"DISPLAY_BOTTOM_PAGER" => "N",
	"PAGER_TITLE" => "Новости",
	"PAGER_SHOW_ALWAYS" => "N",
	"PAGER_TEMPLATE" => "",
	"PAGER_DESC_NUMBERING" => "N",
	"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
	"PAGER_SHOW_ALL" => "N",
	"DISPLAY_DATE" => "Y",
	"DISPLAY_NAME" => "Y",
	"DISPLAY_PICTURE" => "N",
	"DISPLAY_PREVIEW_TEXT" => "N",
	"AJAX_OPTION_ADDITIONAL" => ""
	),
	false
);?>
      </div> <!-- #left_sb_news-->
  </div>   <!-- #left_menu_news-->    
<?php 
	if (/*$USER->IsAuthorized() && ($USER->GetID() == 188 || $USER->IsAdmin())*/true)
	{
        ?>
       <div id="left_menu_news_pub" > </div>     
       <div id="left_menu_news">
       
        
        <div id="left_sb_news">
           
        <?
		#echo '<div id="left_sb_news">
			#<div style=" border-bottom:solid 2px #afb0b2; margin-top: -5px;  background-color: #e5e5e5;"><p style="color:#181818;font-weight: ;font-size:12pt; text-align:center;">Публикации</p></div><br>';
		$APPLICATION->IncludeComponent("bitrix:news.list", "left_sb_news_list1", array(
	"IBLOCK_TYPE" => "news",
	"IBLOCK_ID" => "30",
	"NEWS_COUNT" => "0",
	"SORT_BY1" => "ACTIVE_FROM",
	"SORT_ORDER1" => "DESC",
	"SORT_BY2" => "SORT",
	"SORT_ORDER2" => "ASC",
	"FILTER_NAME" => "",
	"FIELD_CODE" => array(
		0 => "",
		1 => "",
	),
	"PROPERTY_CODE" => array(
		0 => "",
		1 => "",
	),
	"CHECK_DATES" => "Y",
	"DETAIL_URL" => "",
	"AJAX_MODE" => "N",
	"AJAX_OPTION_SHADOW" => "Y",
	"AJAX_OPTION_JUMP" => "N",
	"AJAX_OPTION_STYLE" => "Y",
	"AJAX_OPTION_HISTORY" => "N",
	"CACHE_TYPE" => "N",
	"CACHE_TIME" => "3600",
	"CACHE_FILTER" => "N",
	"CACHE_GROUPS" => "Y",
	"PREVIEW_TRUNCATE_LEN" => "",
	"ACTIVE_DATE_FORMAT" => "d.m.Y",
	"DISPLAY_PANEL" => "N",
	"SET_TITLE" => "Y",
	"SET_STATUS_404" => "N",
	"INCLUDE_IBLOCK_INTO_CHAIN" => "Y",
	"ADD_SECTIONS_CHAIN" => "Y",
	"HIDE_LINK_WHEN_NO_DETAIL" => "N",
	"PARENT_SECTION" => "",
	"PARENT_SECTION_CODE" => "",
	"DISPLAY_TOP_PAGER" => "N",
	"DISPLAY_BOTTOM_PAGER" => "Y",
	"PAGER_TITLE" => "Публикации",
	"PAGER_SHOW_ALWAYS" => "Y",
	"PAGER_TEMPLATE" => "",
	"PAGER_DESC_NUMBERING" => "N",
	"PAGER_DESC_NUMBERING_CACHE_TIME" => "3600",
	"PAGER_SHOW_ALL" => "Y",
	"DISPLAY_DATE" => "Y",
	"DISPLAY_NAME" => "Y",
	"DISPLAY_PICTURE" => "Y",
	"DISPLAY_PREVIEW_TEXT" => "Y",
	"AJAX_OPTION_ADDITIONAL" => ""
	),
	false
);		
		echo "</div>";
		
	}
?>
    </div> <!-- #left_menu_newsP-->   
     <a href="http://www.koyoradracing.com/" target="_blank" rel="nofollow">
      <div id="left_sb_link">
        <img style="height:100%; width:100%;border:none;" src="http://<?=$_SERVER['SERVER_NAME']?>/bitrix/templates/avtodok/images/fp_racing.jpg"/>
      </div>  
       </a>
	</div>
<!-- #left_sb-->
<?php
/*require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/components/itg/catalog.product/itgProduct.php");
global $DB;
//выбираем нужные нам продукты для отображения
$bestProducts = $DB->Query("SELECT `id` FROM `b_autodoc_items_m` WHERE `id` IN (7240843,7240868,7241505,5544563,5544564,5545469)");
echo "<div class='itgBest' style='position:absolute; right:0px;'>";
while ($arId=$bestProducts->Fetch())
{
		echo "<div style = 'width:200px; background-color:#000;opacity:0.9;filter:alpha(opacity=90);padding:10px;'>";
		$product = new itgProduct($arId['id']);
		$props = $product->getProductProperties();
		echo "<img style = 'float:none; width:90px; margin:0px 10px 0px 2px;' src={$props['image']}>";

			echo "<div style = 'display:block; margin:0px 0px 0px 10px;'></div>";
				echo "<span style = 'font-style:italic;font-size:12px;'>Наименование:</span>";
				echo "<div style = 'margin-left:10px;color:#EDAF1F;font-size: 12px;'><a href='http://".$_SERVER["SERVER_NAME"]."/personal/order/catalog/index.php?itg_more_info={$arId['id']}'>{$props['Caption']}</a></div>";
			echo "</div>";
	echo "<br/>";
}
echo "</div>";
*/

?> 
   <a name="AcSearch"></a>
   <div id="beforecontent">
      <img id="hedpicin" src="http://<?=$_SERVER['SERVER_NAME']?>/bitrix/templates/avtodok/images/ContentBack.png" / > 
       <div id="content" style="-moz-border-radius-topright: 10px;"> 
