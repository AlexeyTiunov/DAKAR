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
     
    
?>
   <div id='show_items_head'  style='width:100%;float:left'>
       <p><?=$modelName?> / <?=$modelTypeName?> / <?=$groupTypeName?></p>
   
   </div>
   
   <div id='show_items_list' style='width:100%;float:left;position:relative;'>
   
       <?
         foreach ($itemArray as $brandCode=>$item)
         { 
             
              foreach($item as $itemCode=>$value) 
              {
                  
                         
         
           ?>
   
                <div class="part" style="width: 80%;height: 42px;border:3px solid #CC5E5E; background: #fff;left: 20%;float: right;margin-bottom: 5px;">
                    <div class="img_part" style="float: left;width: 10%;height: 100%;">
                    <img src="/images/mitsubishi.png" align="middle" width="50" height="40">
                    </div>
                    <div class="part_brand" style="float: left;width: 14%;border-left:1px solid #cc5e5e;height: 100%;">
                    <p style="margin-top: 10px;" align="center">Mitsubishi</p>
                    </div>
                    <div class="part_number" style="float: left;width: 19%;border-left:1px solid #cc5e5e;height: 100%;">
                          <p style="margin-top: 10px;" align="center"><?=$itemCode?></p>
                    </div>
                    <div class="part_name" style="float: left;width: 24%;border-left:1px solid #cc5e5e;height: 100%;"> 
                          <p style="margin-top: 10px;" align="center">запчасть<?=$value['CAPTION']?></p>
                    </div>
                    <div class="part_quantity" style="float: left;width: 10%;border-left:1px solid #cc5e5e;height: 100%;">
                    <p style="margin-top: 10px" align="center">1</p>
                    </div>
                    <div class="part_prise" style="float: left;width: 15%;border-left:1px solid #cc5e5e;height: 100%;"> 
                          <p style="margin-top: 10px;" align="center"><?=$value['PRICE']?> <?=$value['CURRENCY']?>  </p>   
                    </div>
                         <div class="part_korz" style="float: right;width: 7%;border-left:1px solid #cc5e5e;height: 100%;"><img align="bottom" src="images/korzina.png" width="40" height="40"></div>
                    </div>
                  
     
            <?
              }
         }
     
      ?> 
   
   
   
   
   </div>








<?
    
    
    
    
?>