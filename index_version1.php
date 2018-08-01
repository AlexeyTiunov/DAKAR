<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
  if (!$USER->IsAuthorized())
     {   
         
       
       #  $_SESSION['BACKULRSA']="/index.php";
        # $_SESSION['MASSAGE']="Пройдите авторизацию.";  
       #  header('Location:/SimpleAuth/');     
            
            
            
     }
?>  
             <div id='brand_logos' style='margin-bottom: 2%;'>
                  
             <div  class="Brand_Logo" id="nissan"> 
                <a target="blank" href="http://www.catcar.info/nissan/?lang=ru"><img id="nissan" src="/bitrix/templates/DAKAR/images/nissan.png"/></a> 
               <p>Nissan</p>
              </div>
              <div class="Brand_Logo" id="lexus"> 
                <a target="blank" href="http://www.catcar.info/toyota/?lang=ru"><img id="lexus" src="/bitrix/templates/DAKAR/images/lexus.png"/></a>
                 <p>Lexus</p>
              </div>
              <div  class="Brand_Logo" id="toyota"> 
              <a target="blank" href="http://www.catcar.info/toyota/?lang=ru"> <img id="toyota" src="/bitrix/templates/DAKAR/images/toyota.png"/> </a>
               <p> Toyota</p> 
              </div>
              <div class="Brand_Logo" id="akura"> 
               <a target="blank" href="http://www.catcar.info//?lang=en/?lang=en">  <img id="akura" src="/bitrix/templates/DAKAR/images/akura.png"/> </a>
                 <p>Acura</p>
              </div>
              <div class="Brand_Logo" id="mitsubishi"> 
               <a target="blank" href="http://www.catcar.info/mitsubishi/?lang=en">  <img id="mitsubishi" src="bitrix/templates/DAKAR/images/mitsubishi.png"/> </a>
                 <p>Mitsubishi</p>
              </div>
              <div class="Brand_Logo" id="subaru"> 
             <a target="blank" href="http://www.catcar.info/subaru/?lang=en/?lang=en">  <img id="subaru" src="bitrix/templates/DAKAR/images/subaru.png"/> </a>
                 <p>&nbsp&nbspSubaru</p>
  
              </div>
              <div class="Brand_Logo" id="mazda"> 
                <a target="blank" href="http://www.catcar.info/mazda/?lang=en"><img id="mazda" src="bitrix/templates/DAKAR/images/mazda.png"/> </a>
                 <p>Mazda</p>
              </div>
              <div class="Brand_Logo" id="honda"> 
               <a target="blank" href="http://www.catcar.info/mazda/?lang=en"> <img id="honda" src="bitrix/templates/DAKAR/images/honda.png"/></a> 
                <p>Honda</p>
              </div>
              <div class="Brand_Logo"  id="suzuki"> 
               <a target="blank" href="http://www.catcar.info/suzuki/?lang=en"> <img id="suzuki" src="bitrix/templates/DAKAR/images/suzuki.png"/> </a>
                 <p>Suzuki</p>
              </div>
              <div class="Brand_Logo" id="infiniti"> 
               <a target="blank" href="http://www.catcar.info/nissan/?lang=en"> <img id="infiniti" src="bitrix/templates/DAKAR/images/infiniti.png"/> </a>
                 <p>Infiniti</p>                
              </div>
             
             </div> 
             <!--<div align="center" id='moving_picture'>
                 <div id="slider" class="nivoSlider">

                  <img src='/images/1.png' alt=""/>
                  <img src='/images/3.png' alt=""/>  
                   <img src='/images/2.png' alt=""/>
                    
              </div>
            </div> 
             <script type="text/javascript">
                $(window).load(function() {  
                    $('#slider').nivoSlider({effect: 'random',directionNav:false,controlNav:false});
                    
                });
            </script> -->
 
              



<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>
