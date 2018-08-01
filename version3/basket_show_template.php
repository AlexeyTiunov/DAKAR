<?php 
#require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php"); 
#$APPLICATION->SetPageProperty("keywords", "Автосервис автодок, Корзина, СТО Киев, СТО Toyota, СТО Mitsubishi,СТО Lexus"); 
#$APPLICATION->SetPageProperty("description", "Автосервис Автодок Киев сервисное обслуживание и ремонт автомобилей  Mitsubishi,Toyota,Lexus,Калькулятор техобслуживание");
#$APPLICATION->SetTitle("Ваша Корзина");
?><div class="blankSeparator"></div>
<div class="container" style='width:100%;float:left; background: none; box-shadow:none;'>
  <div id="catalog_show_items" style="width: 85%;float: left;margin-left: 5%;margin-top: 10px;"> 
  
         <div class="part search_result" style="width: 100%;height: 130px;/*border:3px solid #CC5E5E; background:rgb(240, 230, 230);left: 20%;*/margin-bottom: 10px;">
         
         <div class="img_part" style="float:left;width:15%;height:90%; margin-left:5px;margin-top:5px ;"> 
                <img src="<?=$_GET['PICTURE_BASE64']?>" align="middle" width="100%" height="100%"/>  
            </div>
             
            <div class="part_brand" style="float: left;width: 15%;height: 100%;">
            
               <p align="center" style="font-size: 20px"> <?=$_GET['BRANDNAME']?></p>
                
               <p align="center" style="font-size: 20px"><?=$_GET['ITEMCODE']?></p> 
            </div>
                         
            <div class="part_name" style="float: left;width: 60%;height: 50%;"> 
            
                          <p style="margin-top: 10px;font-size: 20px;" align="center"><?=$_GET['CAPTION']?></p>   
            </div>
              
            
            <div class="part_quantity" style="float: left;width: 15%;height: 50%;">
             <p style="margin-bottom: 0px;margin-top: 0px">Заказано:</p>
                              <p style="margin-top: 10px;float: left;font-size: 20px"><?=$_GET['QUANTITY']?>шт.</p>
            </div>
            <div class="part_quantity" style="float: left;width: 15%;height: 50%;">
           <!-- <p style="margin-bottom: 0px;margin-top: 0px">Изменить:</p>
                                     <input type="text"style="width:85%;"align="center"></input> -->              
            </div>   
             <div class="part_prise" style="float:left ;width: 15%;height: 50%;">
              <p style="margin-bottom: 0px;margin-top: 0px "align="center">Цена:</p>
              <p style="margin-top: 10px;font-size: 20px;" align="center"><?=$_GET['PRICE']?></p>  
             </div>  
         <div class="total_prise" style="float:right; width: 15%;height: 50%;">
          <p style="margin-bottom: 0px;margin-top: 0px;"align="center">Сумма:</p>
            <p align="center" style="font-weight: 900;font-size: 24px;margin-top: 0px"><?=$_GET['SUM']?></p> </div>
         
                      
                     
                    </div>
         

           
             
         
         <input type="image" name='DELETE[<?=$_GET['ID']?>]' src="http://avtodok.com.ua/personal/order/images/otkaz.png" >
  </div>
  

  </div>  
</div>
<div class="blankSeparator"></div>
<?
#require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>