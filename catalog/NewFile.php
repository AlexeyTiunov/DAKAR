<div class="part" style="width: 100%;height: 100px;border:3px solid #CC5E5E; background:rgb(240, 230, 230);left: 20%;margin-bottom: 10px;">
            <div class="img_part" style="float: left;width: 12%;height: 100%;">
               <img <?=$value['PICTURE']?> id='<?=$value['ITEM_CODE']."_".$value['BRAND_CODE']?>_img'  align="middle" width="100%" height="100%"/>
            </div>
             
            <div class="part_brand" style="float: left;width: 12%;border-left:1px solid #cc5e5e;height: 100%;">
            <p>&nbsp</p>
               <p style="margin-top: 10px;" align="center"><?=$value['BRAND_NAME']?></p> 
              
            </div>
             <div class="part_number" style="float: left;width: 16%;border-left:1px solid #cc5e5e;height: 100%;">
             <p>&nbsp</p>
               <p style="margin-top: 10px;" align="center"><?=$value['ITEM_CODE']?></p>
             </div>
            
            <div class="part_name" style="float: left;width: 28%;border-left:1px solid #cc5e5e;height: 100%;"> 
            <p>&nbsp</p>
                          <p style="margin-top: 10px;" align="center"><?=$value['CAPTION']?></p>
            </div>
            <div class="part_quantity" style="float: left;width: 8%;border-left:1px solid #cc5e5e;height: 100%;">
               
               <p style="margin-top: 10px" align="center"><?=$value['QUANTITY']?></p>
               <p style="margin-top: 10px;" align="center">(<?=$value['SUPPLIER_NAME']?>)</p> 
            </div>
             <div class="part_prise" style="float: left;width: 15%;border-left:1px solid #cc5e5e;height: 100%;">
              <p>&nbsp</p>
              <p style="margin-top: 10px;" align="center"> <?=$value['PRICE_SALE']?> <?=ShowCurrency($value['CURRENCY'])?>  </p>  
             </div>  
            <div class="part_korz" 
             style="float: right;width: 7%;border-left:1px solid #cc5e5e;height: 100%;">
             <?
               # Дима. Менять атрибуты img, которые нужны для заказа запчастей, нельзя !!!!
             ?>
                <img align="bottom" style="margin-left:15%;" src="images/korzina.png" width="40" height="40" class='add_to_basket_from_catalog'
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