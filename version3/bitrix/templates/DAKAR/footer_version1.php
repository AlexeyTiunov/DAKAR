     </div>
  
  </div>
  <div class="container" style="margin-top:10px;"  >
  <div class="container" id='container_ns'  style="margin-top:0px;" >   
    <div align="center"  id='moving_picture' >
              <div id="slider"  class="nivoSlider" style="box-shadow: 4px 4px 8px #2B2A1A;">

                  <img src='/images/1.jpg' style='width:100%;height:100%;' alt="" />
                  <img src='/images/2.jpg' style='width:100%;height:100%;' alt="" />
                 <img src='/images/3.png' style='width:100%;height:100%;' alt="" />
                  <img src='/images/4.jpg' style='width:100%;height:100%;' alt="" />
                  <img src='/images/5.jpg' style='width:100%;height:100%;' alt="" />
                  <img src='/images/6.jpg' style='width:100%;height:100%;' alt="" />
                  <img src='/images/7.jpg' style='width:100%;height:100%;' alt="" /> 
                  
                    
              </div>
            </div> 
             <script type="text/javascript">
                $(window).load(function() {  
                    $('#slider').nivoSlider({effect: 'random',directionNav:false,controlNav:false});
                    
                });
            </script> 
    </div> 
    </div> 
    <script>
     $(function(){
         
      original_width=1200; 
      original_height=350;  
     
     current_width=$("#container_ns").parent().width();     
     coef=(current_width*100/original_width)/100;
   // alert(original_height*coef);
     $("#container_ns img").css("height",Number(original_height*coef)+"px");
    
         
     })
    </script>                
   <div class="container_footer">
 <!--  <div id="poloska" style="background-color: #5D5D5D;">               </div>  -->
   <div id="footer">
                  <img src="/bitrix/templates/DAKAR/images/mail_ru_icon.png"  style='float: right; margin-right :10%; margin-top:2%; width:50px; height:50px;  ' >
                  <img src="/bitrix/templates/DAKAR/images/skype_icon.png"  style='float: right; margin-right :1%; margin-top:2%; width:50px; height:50px;  ' > 
                  <img src="/bitrix/templates/DAKAR/images/viber_icon.png"  style='float: right; margin-right :1%; margin-top:2%; width:50px; height:50px;  ' >
                  <div id='contacts' style="margin-left:10%; float:left">
                    <h1 style="color: white;text-shadow: none; font-size: 18pt; font-weight:400;      font-family: Blogger_Sans;">Время работы: <span>Пн.-Пт. 9.00-19.00</span><br>   </h1>
              
                 </div>
                  <div id='contacts' style="margin-left:10%;float:left">
                    <h1 style="color: white;text-shadow: none;     font-size: 18pt; font-weight:400;     font-family: Blogger_Sans;">Отдел Запчастей: <span>(098)622-23-80</span><br> 
                                                                             Отдел Запчастей: <span>(095)877-88-01</span><br>
                                                                             СТО: <span>(067)925-19-24</span><br>                                                                             
                      
                      e-mail:<span> dakar@dakar.in.ua </span></h1> 
              
               </div>
             </div>
        </div>
  </body>
 </html>
