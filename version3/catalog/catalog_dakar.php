<?
 require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');  
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
 
 
 
 
    
?>
 <div class="widget">
    <h3 class="widget-title">Поиск по авто</h3>
    <div class="widget-body">
        <!--<input type='text' class='online_catalog_title' disabled='disabled'   value='Поиск по автомобилю'>  </input>   -->
        
         <!--<input class='online_catalog_filter' type='text'  value='MITSUBISHI'></input>-->
      <ul class="list-unstyled">
        <li class="hidden-xl">
             <select class='online_catalog_filter' id='type_auto' style=''>            
               <option value='0'>Выберите бренд</option>
                              <?
                                echo RecieveNeededBrandForCatalog(1);
                              ?>          
              </select>
         </li> 
       <!-- <a href='#'> <img class='online_catalog_filter_arrow_down' src='bitrix/templates/DAKAR/images/arrow_down.png'> </a>   -->
         <li class="hidden-xl"> 
              <select class='online_catalog_filter' id='type_model' style=''>  
                         <option value='0'>Выберите модель</option>   
              </select>   
          </li>
          <li class="hidden-xl"> 
              <select class='online_catalog_filter' id='type_modeltype' style=''>  
                              <option value='0'>Выберите тип модели</option>   
              </select>                 
           </li>
         <div id="catalog_list">
         </div>

     </div>   <!--.widget-body-->
  </div> <!--.widget -->    











<?
?>
