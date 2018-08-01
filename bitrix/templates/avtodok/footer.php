         
         </div>   <!-- #content--> 
       </div>   <!-- #BеforContent-->
      <!-- </div>   --#UNKNOWN-->
   </div>
   <!-- #middle_up--> 

 
  <div id="middle_down"> 
  <img id="middle_down_pic" src="http://<?=$_SERVER['SERVER_NAME']?>/bitrix/templates/avtodok/images/sporder.jpg" / > 
      <p id="offer">Предложение:</p>
      <div id="middle_down_in">  
         <?php
     # require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
      require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/components/itg/catalog.product/itgProduct.php");
      global $DB;
       //выбираем нужные нам продукты для отображения
           $bestProducts = $DB->Query("SELECT `id` FROM `b_autodoc_items_m` WHERE `id` IN (389141,7240843,7240868,7241505,5544563,5544564,5545469,12537618)");
          #echo "<div class='itgBest' style='position:absolute; right:0px;'>";
         while ($arId=$bestProducts->Fetch())
     {
        echo "<div id='sporder' >";
        $product = new itgProduct($arId['id']);
        $props = $product->getProductProperties();
       

           // echo "<div style = 'display:block; margin:0px 0px 0px 10px;'></div>";
                echo "<span style = 'font-style:italic;font-size:14px;'></span>";
                echo "<div style = 'margin-left:10px;color:#323232;font-size: 12px;height:40px;'>
                <a  style='color:#8a0101; font-style:italic;'href='http://".$_SERVER["SERVER_NAME"]."/personal/order/catalog/index.php?itg_more_info={$arId['id']}'>{$props['Caption']}</a>
                </div>";
         
        echo "<img  src={$props['image']}>"; 
        echo "<div style='margin-top:70px;'>";
       # <a style=' font-size:12px;color:#8a0101; font-style:italic; font-weight:bold;' href='http://parts.avtodok.com.ua/autodoc/search.php?ICODE=".$props['ItemCode']."&BCODE=".$props['BrandCode']."&REGION=0&EMODE=EXACT&CURRENCY=USD&CMB_SORT=PRICE&NUM_PAG=25'>
       # Купить
       # </a> <br>
        echo " <a id=\"OfferA\" style=' font-size:12px;color:#8a0101; font-style:italic; font-weight:bold;' href='#AcSearch'>
          <input type=\"hidden\" name=\"ItemCode\" value=\"".$props['ItemCode']."\">

         
         Купить</a>
        </div>";  
        echo "</div>"; 
       #echo "<br/>";
      }
#echo "</div>";
?>
      </div>  
<!-- #middle_down-->
 </div>

<!-- #middle-->
<div id="footer"> 
  
 
<div id="yandm"  >
       <!-- Yandex.Metrika informer -->
<a href="http://metrika.yandex.ru/stat/?id=22225360&amp;from=informer"
target="_blank" rel="nofollow"><img src="//bs.yandex.ru/informer/22225360/3_0_FFFFFFFF_FFFFFFFF_0_pageviews"
 alt="Яндекс.Метрика" title="Яндекс.Метрика: данные за сегодня (просмотры, визиты и уникальные посетители)" onclick="try{Ya.Metrika.informer({i:this,id:22225360,lang:'ru'});return false}catch(e){}"/></a>
<!-- /Yandex.Metrika informer -->



<!--<script type="text/javascript">
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
<noscript><div><img src="//mc.yandex.ru/watch/22225360" style="position:absolute; left:-9999px;" alt="" /></div></noscript> -->
<!-- /Yandex.Metrika counter -->
<!-- /Yandex.Metrika counter -->
  </div>
  
  <div id="metam">
  
<!--Логотип статистики-->
<a href='http://www.stat24.meta.ua' target='_blank'>
<img alt='МЕТА - Украина. Рейтинг сайтов' border='0' src='http://stat24.meta.ua/img/counter/9.gif' /></a>
<script type='text/javascript'>
var st24Date=(new Date()).getTime();
var st24Src='http://ua5.hit.stat24.com/_'+st24Date;
st24Src+='/script.js?id=';
st24Src+='bJrgvZxF.xoj6IIRyHCuf5ci.q6s7G7UHDGnIMzd0Cz..7/l=11';
var st24Tg='<'+'scr'+'ipt type="text/javascript" src="';
document.writeln(st24Tg+st24Src+'"></'+'scr'+'ipt>');
</script>

  </div>
  <div id="metam"> 
                   <!--LiveInternet counter--><script type="text/javascript"><!--
                    document.write("<a href='http://www.liveinternet.ru/click' "+
                    "target=_blank><img src='//counter.yadro.ru/hit?t11.3;r"+
                    escape(document.referrer)+((typeof(screen)=="undefined")?"":
                    ";s"+screen.width+"*"+screen.height+"*"+(screen.colorDepth?
                    screen.colorDepth:screen.pixelDepth))+";u"+escape(document.URL)+
                    ";"+Math.random()+
                    "' alt='' title='LiveInternet: показано число просмотров за 24"+
                    " часа, посетителей за 24 часа и за сегодня' "+
                    "border='0' width='88' height='31'><\/a>")
                    //--></script><!--/LiveInternet-->

  </div>

   <div id="footer_text"> © Copyright parts.avtodok    All Rights Reserved<br>г. Киев ул Деревообрабатывающая,5 <br> 
    <a  href="/map.php"style="font-size: 10px; font-family:Cambria; ">Карта сайта</a>   
      </div>
</div>
<div id="after_footer_text">
</div>
<!-- #footer -->
   </div>  <!--Comon-->
   </div>   <!--TotalComon-->  
 </body>
</html>