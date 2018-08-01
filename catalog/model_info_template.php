<?
# error_reporting(E_ALL);   
?>
<div id='show_items_head' class='col-sm-5' >
   <?
   if ($modelName!="")
   {        
   ?>  
       <p align="center" style="font-size: 18px;color: #C01717; font-family: Blogger_Sans;"><?=$modelName?> / <?=$modelTypeName?> </p>    
   <?
   }else
   {
   ?>
      
   <?
   }
   ?>
  </div>  
 <div class='model_type_img col-sm-3 main-content pull-right' id='model_type_<?=$modelTypeID?>' style='/*float:left; width:50%; height:50%; margin:5px;*/'>
   
   <img <?=$imgSrc?> id='<?=$modelTypeID?>_img' style='width:100%;height:100%;' />
    
</div>

<div class='model_type_description col-sm-5' style='/*float:left; width:45%; height:30%; margin-left: 2%;*/'>
   <?
      if($USER->IsAuthorized() && $groupCheck==true) 
      {
        ?>
         <textarea  maxlength="1500" id='update_description_text' type='text' style='float:left; width:100%; height:100%; background: #808080;font-family: Blogger_Sans;'> <?=$modelTypeDescription?></textarea >
         <input type='button' id='update_description' value='Обновить Описание' modelID='<?=$modelID?>' modelTypeID='<?=$modelTypeID?>'></input> 
        <?  
    
      }else
      {
         ?>
         <p style='font-family: Blogger_Sans;'> <?=$modelTypeDescription?></p>
         <?  
      }
     ?>
</div>

     <?
      if($USER->IsAuthorized() && $groupCheck==true) 
      {
     ?>
         <div class='catalog_item_service' style='width:100%; height:50px; margin-bottom: 10px; float:left;'>
                                                
                        <input type='image' title='Загрузить или изменить фото запчасти' src='/images/jpg_icon.jpg' class='catalog_change_model_type_image' value=''></input>  
                        <input type='file'  class='catalog_change_model_type_image_file' name='file' value='' style='display:none;'
                          modelID='<?=$modelID?>'
                          modelTypeID='<?=$modelTypeID?>'
                        
                        ></input>  
                        <!-- <img src='/images/jpg_icon.jpg'  title='Загрузить или изменить фото запчасти' class='catalog_change_item_image' style='height:70%;width:5%;padding: 0; background: ;'
                         itemcode='<?#=$value['ITEM_CODE']?>'
                         brandcode='<?#=$value['BRAND_CODE']?>'
                       
                       > -->
        </div>
                     
     
     
     <?
      }
     ?>