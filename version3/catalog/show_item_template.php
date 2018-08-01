<?
    /** 
     Пример Массива 
    $catalogItemsForSearch[$catalogItemsArray['BRAND_CODE']][$catalogItemsArray['ITEM_CODE']]['ITEM_CODE']=$catalogItemsArray['ITEM_CODE'];  
    $catalogItemsForSearch[$catalogItemsArray['BRAND_CODE']][$catalogItemsArray['ITEM_CODE']]['PICTURE']="";
    $catalogItemsForSearch[$catalogItemsArray['BRAND_CODE']][$catalogItemsArray['ITEM_CODE']]['WEIGHT']="";
                                                                                              ['CAPTION'] 
    $catalogItemsForSearch[$catalogItemsArray['BRAND_CODE']][$catalogItemsArray['ITEM_CODE']]['IS_AVAILABLE']=false;   
    */
    
    
    
?>



<? 
    #$modelName=""; 
    #$modelTypeName="";
    #$groupTypeName="";
    #$itemArray=Array();   
    #error_reporting(E_ALL);
    
?>
  <div id='show_items_head' class='col-sm-5' style='/*width:100%;float:left;*/'>
   <?
   if ($modelName!="")
   {        
   ?>  
       <p align="center" style="font-size: 18px;color: #C01717;"><?=$modelName?> / <?=$modelTypeName?> / <?=$groupTypeName?></p>    
   <?
   }else
   {
   ?>
      
   <?
   }
   ?>
  </div>  
   <div id='show_items_list' class='col-sm-9 main-content pull-right' style='/*width:100%;float:left;background-color: rgb(240, 230, 230);*/'>
   
       <?
      foreach($itemsArray as $itemArray) 
      {
         foreach ($itemArray as $brandCode=>$item)
         { 
             
              foreach($item as $itemCode=>$value) 
              {
                  
                #if ($value['IS_AVAILABLE']!=true )continue;         
              #############################################################################
               
    /**
    * 
   <?=$value['ITEM_CODE'] ?>    
    $value['BRAND_NAME']
    $value ['PRICE']
    <?=ShowCurrency($value['CURRENCY'])?>    
    $value ['PICTURE'];
    $value ['WEIGHT'];
    $value['QUANTITY'];
    $value['IS_AVAILABLE'];   
    */
           ?>
   
         <div class="part search_result" style="width:100%;height:130px;/*border:3px solid #CC5E5E; background:rgb(240, 230, 230),margin-bottom: 10px;*/;float:left; margin-top:1% ;">
            <div class="img_part" style="float:left;width:15%;height:90%; margin-left:5px;margin-top:5px ;">
               <img <?=$value['PICTURE']?> id='<?=$value['ITEM_CODE']."_".$value['BRAND_CODE']?>_img'  style='width:100%;height:100%;'/>
            </div>
             
            <div class="part_brand" style="float: left;width: 15%;height: 100%;">
            
               <p align="center" style="font-size: 20px"><?=$value['BRAND_NAME']?></p> 
                
               <p align="center" style="font-size: 20px"><?=$value['ITEM_CODE']?></p>
            </div>
                         
            <div class="part_name" style="float: left;width: 60%;height: 50%;"> 
            
                          <p style="margin-top: 10px;font-size: 20px;" align="center"><?=$value['CAPTION']?></p>
            </div>
              <div class="part_korz" 
             style="float: right;width: 7%;height: 100%;">
             <?
               # Дима. Менять атрибуты img, которые нужны для заказа запчастей, нельзя !!!!
             ?>
                <img align="bottom" style="margin-left:15%;margin-top: 50%;" src="images/korzina.png" width="40" height="40" class='add_to_basket_from_catalog'
                           price='<?=$value['PRICE_SALE']?>'
                           currency='<?=$value['CURRENCY']?>'
                           itemcode='<?=$value['ITEM_CODE']?>' 
                           brandcode='<?=$value['BRAND_CODE']?>' 
                           quantity='<?=$value['QUANTITY']?>' 
                           caption='<?=$value['CAPTION']?>'
                           isservice='0' 
                           isitem='1'>
                           <input type="text"style="width:85%;"align="center" id='<?=$value['ITEM_CODE']."_".$value['BRAND_CODE']?>_quantity' value='1'></input>
                </div>
            <div class="part_quantity" style="float: left;width: 30%;height: 50%;">
               <p style="margin-top: 11px;float: right;">(<?#=$value['SUPPLIER_NAME']?>)</p> 
               <p style="margin-top: 5px;float: right;font-size: 20px"><?=$value['QUANTITY']?>шт.</p>
               
            </div>
             <div class="part_prise" style="float: right;width: 25%;height: 50%;">
              
              <p style="margin-top: 10px;font-size: 20px;" align="center"> <?=$value['PRICE_SALE']?> <?=ShowCurrency($value['CURRENCY'])?>  </p>  
             </div>  
          
          <!--  <div class="part_number"><p align="center"><?#=$value['ITEM_CODE']?></p></div> -->
          <!--  <div class="part_quantity"><p align="center"><?#=$value['QUANTITY']?></p></div> -->
          <!-- <div class="part_prise"><p align="center"> <?#=$value['PRICE']?> <?#=ShowCurrency($value['CURRENCY'])?>  </p>  </div> -->
        </div>     
            <?
                  if($USER->IsAuthorized() && $groupCheck==true && $GLOBALS['SEARCH_TYPE']=="catalog") 
                  {
                     ?>
                      <div class='catalog_item_service' style='width:100%; height:50px; margin-bottom: 10px;float:left;'>
                                                
                        <input type='image' title='Загрузить или изменить фото запчасти' src='/images/jpg_icon.jpg' class='catalog_change_item_image' value=''></input>  
                        <input type='file'  class='catalog_change_item_image_file' name='file' value='' style='display:none;'
                          itemcode='<?=$value['ITEM_CODE']?>'
                          brandcode='<?=$value['BRAND_CODE']?>'
                        
                        ></input>  
                        <!-- <img src='/images/jpg_icon.jpg'  title='Загрузить или изменить фото запчасти' class='catalog_change_item_image' style='height:70%;width:5%;padding: 0; background: ;'
                         itemcode='<?#=$value['ITEM_CODE']?>'
                         brandcode='<?#=$value['BRAND_CODE']?>'
                       
                       > -->
                      </div>
                     
                     <?                    
                      
                      
                  }    
            }
           #######################################################################################################3
         }
      }
      ?> 
   
   
   
   
   </div>
   <div id='show_info_local' style="display:none">
       <p style="color:white;">Добавлена</p>
       
       <input type='button' id="show_info_ok" value='OK'></input>
   </div>
  







<?
    
    
    
    
?>