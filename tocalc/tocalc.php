<?
     require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");  
     global $DB;
    if (!function_exists(RecieveNeededBrandForCatalog))
    { 
     function RecieveNeededBrandForCatalog($DESCRIPTION_288type)
         {
              $optionString="";
              global $DB;
              $sql="SELECT IBLOCK_ELEMENT_ID AS ID,PROPERTY_288 AS SHORTNAME,PROPERTY_287 AS NAME FROM b_iblock_element_prop_s37 WHERE DESCRIPTION_288='{$DESCRIPTION_288type}'  ";
              $result=$DB->Query($sql);
              while ($neededBrandArray=$result->Fetch())
              {
                $brandName=strtoupper($neededBrandArray['NAME']);  
               $optionString.="<option value='{$neededBrandArray['ID']}'>{$brandName}</option>";    
              }
            return $optionString; 
         }
    }    
     function RecieveMileAgesOptions()
     {
          $optionString="";
          global $DB;
          $sql="SELECT * FROM b_autodoc_tscalc_mileage WHERE ACTIVE=1";
          $result=$DB->Query($sql); 
           while ($mileAgeOptionArray=$result->Fetch()) 
           {
             $optionString.="<option value='{$mileAgeOptionArray['ID']}'>{$mileAgeOptionArray['VALUE']}</option>";  
           } 
           return  $optionString; 
         
     }
     
     function RecieveServiceByBrandModel($brand, $model)
     {
        
        return ""; 
     }
?>
<?
$APPLICATION->SetPageProperty("keywords", "Калькулятор техобслуживание,Автосервис автодок,Ремонт Т, СТО Киев, СТО Toyota, СТО Mitsubishi,СТО Lexus"); 
$APPLICATION->SetPageProperty("description", "Калькулятор техобслуживание автосервиса Автодок Киев сервисное обслуживание и ремонт автомобилей  Mitsubishi,Toyota,Lexus");
$APPLICATION->SetTitle("Калькулятор техобслуживание Автосервис Автодок, Сеть СТО по ремонту автомобилей Mitsubishi,Toyota,Lexus.Киев");
 session_start(); 
 #var_dump($_SERVER['PHP_SELF']);
 if (isset($_POST['CLIENT']) &&  $_POST['CLIENT']['EMAIL']!="")
 {
      $action="/to_client_add.php";
      $_SESSION['CLIENT']=$_POST['CLIENT'];
} else
{
    $action="/ts_queue.php";
    
}

?>
 <style>
   select {
    width: 90%;
    margin: 5px;
    margin-bottom: 10px;
    padding: 5px;
}
 </style>
 <script>
  $(function(){
     $("#search_result").css("width","100%"); 
     $("#search_result").css("margin-left","0%");  
      
  })
 </script>
  <div class="container blog" style='min-height: 450px;' >
    <div id="full_calc">
      <div id='calc_option_common '> 
            <div id="type_of_auto">
          <h2>Выберите Авто</h2>
           <p>
           
           <select id="type_auto" lang="<?=$GLOBALS['LNG']?>">
            <option value='0'>Выберите бренд</option>
              <?
                echo RecieveNeededBrandForCatalog(1);
              ?>    
            
           </select>
          </p>
         </div>
          
          <div id="type_of_model_toyota">
          <h2>Выберите модель</h2>
           <p>
            <select id="type_model">
           
           </select>
          </p>
         </div> 
      </div> 
      <div id="type_modification">
          <h2>Выберите Тип</h2>
           <p>
            <select id="type_modeltype">
             <option value='0'></option>
                  <?
                 // echo RecieveMileAgesOptions();
                  ?>
            </select>
          </p>
         </div>
      <div id='calc_option_plan_ts'> 
        <!--  <div id="type_of_auto">
          <h2>Выберите бренд Авто</h2>
           <p>
           <select id="type_auto">
            <option value='0'>Выберите бренд</option>
              <?
               # echo RecieveNeededBrandForCatalog(1);
              ?>    
            
           </select>
          </p>
         </div>
          
          <div id="type_of_model_toyota">
          <h2>Выберите модель Авто</h2>
           <p>
            <select id="type_model">
           
           </select>
          </p>
         </div>    -->

          <div id="type_of_mileage">
          <h2>Выберите ваш пробег</h2>
           <p>
           <select id="type_mileage">
            <option value='0'></option>
              <?
              echo RecieveMileAgesOptions();
              ?>
           </select>
          </p>
         </div>
          <p id="tipus"></p>
          <div id="config_site">
          </div>
      
      </div>
       
       <div id='calc_option_ts' style="display: none;">
          
          <div id="type_of_mileage">
          <h2>Выберите работу</h2>
          <p>
              <select id="ts_services">
                
               </select>
         
               <input type='submit' name='' id='ad_ts_service' value='Добавить Работу'></input> 
            </p>          
         </div>
         
       </div> 
    </div>
      <div id="poloska">               </div>   

      <div id='ts_type'>
         <div>
           <a href='#' id='ts_plan' class='ts_type_icon' style='background: url(/images/ts_icons.png )no-repeat -228px 0;'>w</a>
           <p>Плановое TO</p>
         </div>  
          <div>
              <a  href='#' id='ts_ts' class='ts_type_icon' style='background: url(/images/ts_icons.png )no-repeat 7px 0;'>w</a>
              <p>Техническое<br>обслуживание</p>
          </div>
         <div>
              <a  id='ts_ts' class='ts_type_icon' style='background: url(/images/ts_icons.png )no-repeat -389px 0;'></a> 
               <h2>Итог:</h2><h1><span id='total_sum'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </span></h1>
         </div>
          
      </div> 
      <form name="" action="<?=$action?>" method="POST" enctype="multipart/form-data">  
       <div id='full_calc_result'>
         <div id='calc_result_plan_ts'>
         </div>
         <div id='calc_result_ts' >
               
         </div>
         <div style='margin:3%; float:right'>
          <input type='submit' value="Записаться на ТО"> </input>
         
          
          </div>
       </div>
       </form>

   </div>
  
<p align="center">Конечная сумма заказа может отличаться от рассчитанной, об изменениях Вас проинформирует менеджер. </p>

<div class="blankSeparator"></div>




<?
  require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");   
?>
