<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
  if (!$USER->IsAuthorized())
     {   
         
       
       #  $_SESSION['BACKULRSA']="/index.php";
        # $_SESSION['MASSAGE']="Пройдите авторизацию.";  
       #  header('Location:/SimpleAuth/');     
            
            
            
     }
?>  
   <!-- <div class="container"> 
        <div id="content">
            <div class="row">  -->
              <!--  <a href="http://koyorad.com.ua/search.php">www</a>     -->
                <div class="col-sm-9 main-content pull-right">
                    <div class="beta-banner">
                        <div class="fullwidthbanner-container-home">
                    <div class="fullwidthbanner">
                    <div class="bannercontainer" >
                    <div class="banner" >
                        <ul>
                            <!-- THE FIRST SLIDE -->
                        <li data-transition="boxfade" data-slotamount="10">
                            <img src="assets/dest/images/1.png" alt="image"/>

                            <div class="caption randomrotate regular_button" data-x="825" data-y="550" data-speed="700" data-start="3000" data-easing="easeOutBack">Детали</div>
                            
                            
                        </li>
                        
                        <li data-transition="boxfade" data-slotamount="10">
                            <img src="assets/dest/images/2.png" alt="background">


                            <div class="caption randomrotate regular_button" data-x="600" data-y="380" data-speed="700" data-start="3200" data-easing="easeOutBack"><a href="service.php">Перейти</a></div>
                        </li>

                        <li data-transition="boxfade" data-slotamount="10">
                            <img src="assets/dest/images/3.png" alt="image"/>

                            <div class="caption randomrotate regular_button" data-x="825" data-y="550" data-speed="700" data-start="3000" data-easing="easeOutBack">Детали</div>
                            
                            
                        </li>
                        <li data-transition="boxfade" data-slotamount="10">
                            <img src="assets/dest/images/4.png" alt="image"/>

                          
                            <div class="caption randomrotate regular_button" data-x="600" data-y="380" data-speed="700" data-start="3200" data-easing="easeOutBack"><a href="catalog.php">Перейти</a></div>
                            
                        </li>
 
                        </ul>
                </div></div>
                        <div class="tp-bannertimer"></div>
                    </div>
                </div>
                <!--slider-->
                    </div> <!-- .beta-banner -->
                    
                    <div class="space10">&nbsp;</div>
                    <div class="dg">
                        <div class="col-6">
                            <div class="beta-banner">
                                <img src="assets/dest/images/banners/banner2.jpg" alt="">
                                <h2 
                                    class="beta-banner-layer text-right"
                                    data-animo='{
                                        "duration" : 1000,
                                        "delay" : 100,
                                        "easing" : "easeOutSine",
                                        "template" : {
                                            "opacity" : [0, 1],
                                            "top" : [20, 20, "px"],
                                            "right" : [-300, 25, "px"]
                                        }
                                    }'
                                >Фильтр масляный</h2>
                                <p 
                                    class="beta-banner-layer text-right"
                                    data-animo='{
                                        "duration" : 1000,
                                        "delay" : 400,
                                        "easing" : "easeOutSine",
                                        "template" : {
                                            "opacity" : [0, 1],
                                            "top" : [65, 65, "px"],
                                            "right" : [-300, 25, "px"]
                                        }
                                    }'
                                >супер пупер  <br /> просто клас.</p>
                                <a 
                                    class="beta-banner-layer beta-btn text-right" 
                                    href="product.php"
                                    data-animo='{
                                        "duration" : 1000,
                                        "delay" : 300,
                                        "easing" : "easeOutSine",
                                        "template" : {
                                            "opacity" : [0, 1],
                                            "bottom" : [20, 20, "px"],
                                            "right" : [-300, 25, "px"]
                                        }
                                    }'
                                >Купить</a>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="beta-banner">
                                <img src="assets/dest/images/banners/banner3.jpg" alt="">
                                <h2 
                                    class="beta-banner-layer text-right"
                                    data-animo='{
                                        "duration" : 1000,
                                        "delay" : 100,
                                        "easing" : "easeOutSine",
                                        "template" : {
                                            "opacity" : [0, 1],
                                            "top" : [20, 20, "px"],
                                            "right" : [-300, 25, "px"]
                                        }
                                    }'
                                >Калькулятор ТО</h2>
                                <p 
                                    class="beta-banner-layer text-right"
                                    data-animo='{
                                        "duration" : 1000,
                                        "delay" : 400,
                                        "easing" : "easeOutSine",
                                        "template" : {
                                            "opacity" : [0, 1],
                                            "top" : [65, 65, "px"],
                                            "right" : [-300, 25, "px"]
                                        }
                                    }'
                                >Расчёт стоимости<br /> тех. обслужывания Вашего авто</p>
                                <a 
                                    class="beta-banner-layer beta-btn text-right" 
                                    href="calc.php"
                                    data-animo='{
                                        "duration" : 1000,
                                        "delay" : 300,
                                        "easing" : "easeOutSine",
                                        "template" : {
                                            "opacity" : [0, 1],
                                            "bottom" : [20, 20, "px"],
                                            "right" : [-300, 25, "px"]
                                        }
                                    }'
                                >Перейти</a>
                            </div>
                        </div>
                    </div>

                    <div class="space10">&nbsp;</div>
                    <div class="beta-promobox">
                        <h2 class="pull-left "><span class="white">Акция</span> -5%, при покупке от 100$</h2>
                        <a class="beta-btn pull-right mt5" href="custom_callout_box.php">Детали</a>
                        <div class="clearfix"></div>
                    </div> <!-- .beta-promobox -->
                    
                    <div class="space50">&nbsp;</div>
                    <div class="beta-products-list hidden-xs">
                        <h4>Популярное</h4>
                        <div class="beta-products-details">
                            <p class="pull-left">438 запчастей <a href="#">Смотреть всё</a></p>
                            <p class="pull-right">
                                <span class="sort-by">Сортировка </span>
                                <select name="sortproducts" class="beta-select-primary">
                                    <option value="desc">Цене</option>
                                    <option value="popular">Скорости доставки</option>
                                    </select>
                            </p>
                            <div class="clearfix"></div>
                        </div>

                        <div class="row">
                            <div class="col-sm-4 ">
                                <div class="single-item">
                                    <div class="single-item-header">
                                        <a href="#"><img src="assets/dest/images/products/1.jpg" alt=""></a>
                                    </div>
                                    <div class="single-item-body">
                                        <p class="single-item-title">Запчасть</p>
                                        <p class="single-item-price">
                                            <span>$34.55</span>
                                        </p>
                                    </div>
                                    <div class="single-item-caption">
                                        <a class="add-to-cart pull-left" href="#"><i class="fa fa-shopping-cart"></i></a>
                                        <a class="beta-btn primary" href="product.php">Детали<i class="fa fa-chevron-right"></i></a>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4 ">
                                <div class="single-item">
                                    <div class="ribbon-wrapper"><div class="ribbon sale">Скидки</div></div>

                                    <div class="single-item-header">
                                        <a href="#"><img src="assets/dest/images/products/2.jpg" alt=""></a>
                                    </div>
                                    <div class="single-item-body">
                                        <p class="single-item-title">Запчасть</p>
                                        <p class="single-item-price">
                                            <span class="flash-del">$34.55</span>
                                            <span class="flash-sale">$33.55</span>
                                        </p>
                                    </div>
                                    <div class="single-item-caption">
                                        <a class="add-to-cart pull-left" href="#"><i class="fa fa-shopping-cart"></i></a>
                                        <a class="beta-btn primary" href="product.php">Детали<i class="fa fa-chevron-right"></i></a>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4 ">
                                <div class="single-item">
                                    <div class="single-item-header">
                                        <a href="#"><img src="assets/dest/images/products/3.jpg" alt=""></a>
                                    </div>
                                    <div class="single-item-body">
                                        <p class="single-item-title">Запчасть</p>
                                        <p class="single-item-price">
                                            <span>$34.55</span>
                                        </p>
                                    </div>
                                    <div class="single-item-caption">
                                        <a class="add-to-cart pull-left" href="shopping_cart.php"><i class="fa fa-shopping-cart"></i></a>
                                        <a class="beta-btn primary" href="product.php">Детали <i class="fa fa-chevron-right"></i></a>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> <!-- .beta-products-list -->

                    <div class="space50">&nbsp;</div>
                    
                    <div class="dg">
                        <div class="col-6 ">
                            <div class="beta-banner beta-banner-a">
                                <img src="assets/dest/images/banners/4/banner4.jpg" alt="">
                                <img 
                                    class="beta-banner-layer" 
                                    src="assets/dest/images/banners/4/banner4.jpg" 
                                    alt="">
                                <h6>Евакуатор</h6>
                                <p class="beta-banner-layer text-right">Детали </p>
                            </div>
                        </div>
                        <div class="col-6 ">
                            <div class="beta-banner beta-banner-a">
                                <a target="blank" href="http://www.catcar.info/toyota/?lang=ru">
                                <img src="assets/dest/images/banners/4/banner5.png"  alt="">
                                
                                <h6>Каталог Toyota</h6>
                                
                            </div>
                        </div>
                    </div>

                    
            </div>
    <!--    </div> --> <!-- #content -->
        <!--</div>--><!-- .container -->
    <!--</div> --> <!-- .container -->        
 
     <!-- include js files -->
    <script src="/assets/dest/js/jquery.js"></script>
    <script src="/assets/dest/vendors/jqueryui/jquery-ui-1.10.4.custom.min.js"></script> 
    <script src="http://netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script> 
    <script src="/assets/dest/vendors/bxslider/jquery.bxslider.min.js"></script>
    <script src="/assets/dest/vendors/colorbox/jquery.colorbox-min.js"></script>
    <script src="/assets/dest/rs-plugin/js/jquery.themepunch.tools.min.js"></script>
    <script src="/assets/dest/rs-plugin/js/jquery.themepunch.revolution.min.js"></script>
    <script src="/assets/dest/js/waypoints.min.js"></script>
    <script src="/assets/dest/js/wow.min.js"></script>
    <script src="/assets/dest/vendors/animo/Animo.js"></script>
    <script src="/assets/dest/vendors/dug/dug.js"></script>
    <script src="/assets/dest/js/scripts.min_d.js"></script>
    <script src="assets/dest/js/jquery.countTo.js"></script> 
    
    
    <!--customjs-->
    <script type="text/javascript">

                var tpj=jQuery;
                tpj.noConflict();

                tpj(document).ready(function() {

                if (tpj.fn.cssOriginal!=undefined)
                    tpj.fn.css = tpj.fn.cssOriginal;

                    tpj('.banner').revolution(
                        {
                            delay:9000,
                            startheight:700,
                            startwidth:1200,


                            hideThumbs:200,

                            thumbWidth:100,                            // Thumb With and Height and Amount (only if navigation Tyope set to thumb !)
                            thumbHeight:50,
                            thumbAmount:5,

                            navigationType:"bullet",                // bullet, thumb, none
                            navigationArrows:"nexttobullets",                // nexttobullets, solo (old name verticalcentered), none

                            navigationStyle:"navbar",                // round,square,navbar,round-old,square-old,navbar-old, or any from the list in the docu (choose between 50+ different item), custom


                            navigationHAlign:"center",                // Vertical Align top,center,bottom
                            navigationVAlign:"bottom",                    // Horizontal Align left,center,right
                            navigationHOffset:0,
                            navigationVOffset:20,

                            soloArrowLeftHalign:"left",
                            soloArrowLeftValign:"center",
                            soloArrowLeftHOffset:20,
                            soloArrowLeftVOffset:0,

                            soloArrowRightHalign:"right",
                            soloArrowRightValign:"center",
                            soloArrowRightHOffset:20,
                            soloArrowRightVOffset:0,

                            touchenabled:"on",                        // Enable Swipe Function : on/off
                            onHoverStop:"on",                        // Stop Banner Timet at Hover on Slide on/off

                            stopAtSlide:-1,                            // Stop Timer if Slide "x" has been Reached. If stopAfterLoops set to 0, then it stops already in the first Loop at slide X which defined. -1 means do not stop at any slide. stopAfterLoops has no sinn in this case.
                            stopAfterLoops:-1,                        // Stop Timer if All slides has been played "x" times. IT will stop at THe slide which is defined via stopAtSlide:x, if set to -1 slide never stop automatic

                            hideCaptionAtLimit:0,                    // It Defines if a caption should be shown under a Screen Resolution ( Basod on The Width of Browser)
                            hideAllCaptionAtLilmit:0,                // Hide all The Captions if Width of Browser is less then this value
                            hideSliderAtLimit:0,                    // Hide the whole slider, and stop also functions if Width of Browser is less than this value

                            shadow:1,                                //0 = no Shadow, 1,2,3 = 3 Different Art of Shadows  (No Shadow in Fullwidth Version !)
                            fullWidth:"off"                            // Turns On or Off the Fullwidth Image Centering in FullWidth Modus


                        });



                    });

            </script>
    
    <script>
     jQuery(document).ready(function($) {
                'use strict';
                try {        
        if ($(".animated")[0]) {
            $('.animated').css('opacity', '0');
            }
            $('.triggerAnimation').waypoint(function() {
            var animation = $(this).attr('data-animate');
            $(this).css('opacity', '');
            $(this).addClass("animated " + animation);

            },
                {
                    offset: '80%',
                    triggerOnce: true
                }
            );
    } catch(err) {

        }
        
var wow = new WOW(
  {
    boxClass:     'wow',      // animated element css class (default is wow)
    animateClass: 'animated', // animation css class (default is animated)
    offset:       150,          // distance to the element when triggering the animation (default is 0)
    mobile:       false        // trigger animations on mobile devices (true is default)
  }
);
wow.init();    

$(function() {
        // this will get the full URL at the address bar
        var url = window.location.href;

        // passes on every "a" tag
        $(".main-menu a").each(function() {
            // checks if its the same on the address bar
            if (url == (this.href)) {
                $(this).closest("li").addClass("active");
                $(this).parents('li').addClass('parent-active');
            }
        });
    }); 
    
    });
    
    
    
    
    
    
    

    </script>
              



<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>
