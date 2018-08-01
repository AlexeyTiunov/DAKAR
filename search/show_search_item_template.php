<?
    /** 
     Пример Массива 
    $catalogItemsForSearch[$catalogItemsArray['BRAND_CODE']][$catalogItemsArray['ITEM_CODE']]['ITEM_CODE']=$catalogItemsArray['ITEM_CODE'];  
    $catalogItemsForSearch[$catalogItemsArray['BRAND_CODE']][$catalogItemsArray['ITEM_CODE']]['PICTURE']="";
    $catalogItemsForSearch[$catalogItemsArray['BRAND_CODE']][$catalogItemsArray['ITEM_CODE']]['WEIGHT']="";
                                                                                              ['CAPTION'] 
    $catalogItemsForSearch[$catalogItemsArray['BRAND_CODE']][$catalogItemsArray['ITEM_CODE']]['IS_AVAILABLE']=false;   
    */
    #error_reporting(E_ALL);
  # var_dump("12");  
    
    
?>



<? 
    #$modelName=""; 
    #$modelTypeName="";
    #$groupTypeName="";
    #$itemArray=Array();   
    #error_reporting(E_ALL);
    /*function MakeNavigationString($pages_count,$count_for_page,$active_page_number,$sort,$dsort)
    {
        $previuos=($active_page_number-1<1)?"":"<a href='/search/search_item.php?pageNumber=".($active_page_number-1)."&FULL_PAGE &SORT={sort}&DSORT={$dsort}'> < </a>";
        
        $next=($active_page_number+1>$pages_count)?"":"<a href='/search/search_item.php?pageNumber=".($active_page_number+1)."&FULL_PAGE&SORT={sort}&DSORT={$dsort}'> > </a>";
        #var_dump($pages_count);
        $nav_string="<div id='nav'>";
        $nav_string.=$previuos;
        for ($i=1;$i<=$pages_count;$i++)
        { 
          if ($i==$active_page_number)
          {
              $style="color:#c01717";
          } 
          else
          {  
              $style="";              
          }           
         $nav_string.="<a  style='{$style}'href='/search/search_item.php?pageNumber={$i}&FULL_PAGE&SORT={$sort}&DSORT={$dsort}'>{$i}</a>";  
        }
         $nav_string.=$next;
        $nav_string.="</div>"; 
        return $nav_string;
    }
    function MakeSortArrows($active_page_number,$condition_sort)
    {
      $sortArrowsString="";
      $sortArrowsString.="<p style='margin-top: 10px;font-size: 20px;' align='center'>";
      $sortArrowsString.="<a href='/search/search_item.php?pageNumber=".($active_page_number)."&FULL_PAGE&SORT={$condition_sort}&DSORT=ACS'> < </a>";
      $sortArrowsString.="<a href='/search/search_item.php?pageNumber=".($active_page_number)."&FULL_PAGE&SORT={$condition_sort}&DSORT=DECS'> > </a>";   
      $sortArrowsString.="</p>";  
      
      return $sortArrowsString;
    }   */
     if(!isset($_GET['pageNumber']))   $_GET['pageNumber']=1;
?>
  <div id='show_items_head' class='col-sm-5'  >
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
      
  <style> 
     .search_table{ 
       font-family:Blogger_Sans; 
     /* box-shadow:  -2px -2px 2px 0.6px rgba(0,0,0,1), 
       2px 2px 2px 0.6px rgba(0,0,0,1),-2px 2px 2px 0.6px rgba(0,0,0,1),2px -2px 2px 0.6px rgba(0,0,0,1); */
     /*  box-shadow:   
       2px 1px 3px 0.6px rgba(0,0,0,1);  
      border-radius:4px;  */ 
      width:100%;
      margin-top: 10px;
      /*border-spacing: 2px; */
      border-collapse:separate;
     /* 
      border-color: grey; */
      color:white;
     }
     .search_table td {
       /*  display:table-cell;
         box-shadow: inset -1px -1px 2px 0.6px rgba(0,0,0,1), inset 1px 1px 2px 0px rgba(0,0,0,1);
           
         box-shadow: 
      1px 1px 2px 0.6px rgba(0,0,0,1);  border-radius:5px;*/ 
      width:14%;
     }
     .search_table th {
       width:14%;
       border-radius:5px;
     }
     
   .search_table  p {
         color: #1A1A1A;
    line-height: 17px;
    font-size: 90%;
    font-family:Blogger_Sans;
  /*font-family:Times New Roman;*/
    font-weight:bold;
    margin-left: 5%;
  
     }
      
  </style>    
  <!--<div class='container'>
  <div class='content'>
  <div class='row'>  
  <div class='col-sm-9 main-content pull-right'> --> 
   <div  class='col-sm-9 main-content pull-right' id='show_items_list' style='/*background-color: rgb(240, 230, 230);*/'>   
       <?  
           $tr_color="#c01717";  
       $count_for_pages=5;  
       $countitemsArray=count($itemsArray);
       $pages_count=ceil($countitemsArray/$count_for_pages);
       
       # if(!isset($_GET['pageNumber']))   $_GET['pageNumber']=1;
   
        $startCheckNumber=($_GET['pageNumber']-1)*$count_for_pages;    #>
        $stopCheckNumber=($_GET['pageNumber']==0)? $count_for_pages:($_GET['pageNumber'])*$count_for_pages ;   #<=
     #  echo MakeNavigationString($pages_count,$count_for_pages,$_GET['pageNumber'],$_GET['SORT'],$_GET['DSORT']);
       
           if (isset($_POST['ANALOGS']))
           echo MakeNavigationStringAjax($itemCode,$pages_count,$count_for_pages,$_GET['pageNumber'],$_GET['SORT'],$_GET['DSORT'],"navlink_analogs");  
           else
            echo MakeNavigationStringAjax($itemCode,$pages_count,$count_for_pages,$_GET['pageNumber'],$_GET['SORT'],$_GET['DSORT']);
        ?> 

      <table  class='shop_table beta-shopping-cart-table' cellspacing="0"  style="/*border-collapse:separate*/"  >  
        <tr style="background-color:#c01717;">
           <th class="product-brand" style="" > <p  align="center">Бренд</p> </th>
           <th><p  align="center">Артикул</p>            </th> 
           <th style=""><p  align="center">Наименование</p>             </th> 
           <th><p  align="center">Кол.</p>             
               <?
                echo  MakeSortArrows($_GET['pageNumber'],"QUANTITY");
                ?>
           </th> 
           <th> <p  align="center">Цена</p>           
                <?
                echo  MakeSortArrows($_GET['pageNumber'],"PRICE");
                ?>
            </th>
            <th><p  align="center">Регион</p></th>
           <th> <p  align="center">Заказать</p>            </th>
        </tr> 
       </table> 
       <?
       
       
        
      for ($i=0;$i<$countitemsArray;$i++)       
     # foreach($itemsArray as $itemArray) 
      {
          if ($i+1>$startCheckNumber && $i+1<=$stopCheckNumber)
          { }
          else 
          {
              #var_dump("error");
              continue;
          } 
          $value= $itemsArray[$i];
        # $itemArray=$itemsArray[$i];
        # foreach ($itemArray as $brandCode=>$item)
        #{ 
             
              #foreach($item as $itemCode=>$value) 
              #{
                 
                  
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
                 /* if ($tr_color=="#c01717")
                  {
                     $tr_color ="#808080";
                   
                  }else
                  {
                    $tr_color="#c01717";   
                  } */
                   $tr_color="#a9a9a9";
                 # $tr_color="rgba(169, 169, 169, 0.28);"
                # $tr_color=" rgba(169, 169, 169, 0.78)";
                 $tr_color="rgba(138, 137, 137, 0.78)";
                 ?>  <table  class='shop_table beta-shopping-cart-table'   > 
                       <tr sstyle="/*background-color:<?#=$tr_color?>;*/">
                          <td class="product-brand" ><p align="center" ><?=$value['BRAND_NAME']?></p></td>
                          <td><p align="center" ><?=$value['ITEM_CODE']?></p> </td>
                          <td ><p  align="center" ><?=$value['CAPTION']?></p></td>
                          <td><p align="center"><?=$value['QUANTITY']?>шт.</p></td>
                          <td><p align="center"> <?=$value['PRICE_SALE']?> <?=ShowCurrency($value['CURRENCY'])?>  </p></td>  
                          <td><p align="center" >(<?=$value['SUPPLIER_NAME']?>)</p>  </td>
                          <td align="center"> <img align="center" style="margin-left:0%;margin-top:0%;" src="/images/korzina.png" width="40" height="40" class='add_to_basket_from_catalog'
                                       price='<?=$value['PRICE_SALE']?>'
                                       currency='<?=$value['CURRENCY']?>'
                                       itemcode='<?=$value['ITEM_CODE']?>' 
                                       brandcode='<?=$value['BRAND_CODE']?>' 
                                       quantity='<?=$value['QUANTITY']?>' 
                                       caption='<?=$value['CAPTION']?>'
                                       isservice='0' 
                                       isitem='1'>
                                       <input type="text"style="width:15%;"align="center" id='<?=$value['ITEM_CODE']."_".$value['BRAND_CODE']?>_quantity' value='1'></input>
                          </td>
                       
         
           
                      <td style="display: none;">
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
                      </td>   
                    </tr>  
                </table> 
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
           # }
           #######################################################################################################3
        # }
      }
      
      ?> 
   
   
   
    <!-- </table>  -->
    <br>
     <?
         if (isset($_POST['ANALOGS']))
           echo MakeNavigationStringAjax($itemCode,$pages_count,$count_for_pages,$_GET['pageNumber'],$_GET['SORT'],$_GET['DSORT'],"navlink_analogs");  
         else
           echo MakeNavigationStringAjax($itemCode,$pages_count,$count_for_pages,$_GET['pageNumber'],$_GET['SORT'],$_GET['DSORT']);
     ?>  
   </div>
  <!--</div >    </div >  </div >  </div> -->
  
   <div id='show_info_local' style="display:none">
       <p style="color:white;">Добавлена</p>
       
       <input type='button' id="show_info_ok" value='OK'></input>
   </div>
   







<?
    
    
    
    
?>