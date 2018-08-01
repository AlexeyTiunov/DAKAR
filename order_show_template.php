 <div class="container" style='width:100%;float:left;margin-left: 5%;margin-top:2%; background:none;box-shadow:none;'>
  <div class="blankSeparator"></div>
  
<?
    function MakeNavigationString($pages_count,$count_for_page,$active_page_number)
    {
        $previuos=($active_page_number-1<1)?"":"<a href='/order_check.php?pageNumber=".($active_page_number-1)."'> < </a>";
        
        $next=($active_page_number+1>$pages_count)?"":"<a href='/order_check.php?pageNumber=".($active_page_number+1)."'> > </a>";
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
         $nav_string.="<a  style='{$style}'href='/order_check.php?pageNumber={$i}'>{$i}</a>";  
        }
         $nav_string.=$next;
        $nav_string.="</div>"; 
        return $nav_string;
    }
    $count_for_pages=4;
    $orderListCount=count($orderList);
    $pages_count=ceil($orderListCount/$count_for_pages);
    
     
    
   if(!isset($_GET['pageNumber']))   $_GET['pageNumber']=1;
   
   $startCheckNumber=($_GET['pageNumber']-1)*$count_for_pages;    #>
   $stopCheckNumber=($_GET['pageNumber']==0)? $count_for_pages:($_GET['pageNumber'])*$count_for_pages ;   #<=
   
   
    
  
   
   echo MakeNavigationString($pages_count,$count_for_pages,$_GET['pageNumber']);
   for ($i=0;$i<$orderListCount;$i++) 
    #var_dump($orderList);  
   # foreach ($orderList as $orderItem)
    {
      if ($i+1>$startCheckNumber && $i+1<=$stopCheckNumber)
      { }
      else 
      {
          #var_dump("error");
          continue;
      }
       $orderItem=$orderList[$i]; 
       $order=$orderItem['ORDER'];
        $orderBasket=$orderItem['BASKET_ITEMS'];
        
       
        ?>
             <table class="order_list search_result" id='main_table_<?=$orderItem['ORDER']["ID"]?>' orderID='<?=$orderItem['ORDER']["ID"]?>' align="center" style="font-family: Blogger_Sans; margin-top:10px;width:100%; border-bottom:Solid 1px black;" >
                 <tr bgcolor="#c01717" align="center" >
                     <th >Номер заказа</th>
                     <th >Дата / Время</th>
                     <th style="float: right;margin-right: 5%;">Cумма заказа</th> 
                 </tr> 
                 <tr align="center"style="border-top:1px solid #a52a2a;font-weight:900;">
                       <td align="center">№<?=$orderItem['ORDER']["ID"]?></td>
                       <td><?=$orderItem['ORDER']["DATE_INSERT"]?></td>
                       <td style="float: right;margin-right: 5%;"><?=$orderItem['ORDER']["FORMATED_PRICE"]?></td>
                 </tr>
             </table>
             <table class="detail_order" id='detail_order_<?=$orderItem['ORDER']["ID"]?>' align="center" style="margin-bottom: 10px;width:80%; font-family: Blogger_Sans; display:none;">
                  <!--<tr bgcolor="#c01717" align="center"> -->     
                  <tr  align="center">
                    <th>Артикул</th>
                    <th>Название</th>
                    <th>Количество</th>
                    <th>Цена</th>
                  </tr>
           
           
        <?
        
        foreach ($orderItem['BASKET_ITEMS'] as $key=>$basketItem)
        {
            
         ?>
               
               <tr align="center" style="border-top:1px solid #a52a2a;">
                   <td> <?=$basketItem['ARTICLE']?> </td>
                   <td><?=$basketItem['NAME']?></td>
                   <td><?=$basketItem['QUANTITY']?></td>
                   <td><?=$basketItem['PRICE_FORMATED']?></td>
               </tr>
              
           
            
         <?  
            
        }
        ?>
        
         </table> 
        
        <?
        
    }
    echo MakeNavigationString($pages_count,$count_for_pages,$_GET['pageNumber']); 
    
    
    
    
?>
 <script>
          $(function(){
              
            $("table.order_list").click(function(){
               //alert("ww"); 
               orderID=$(this).attr("orderID");
               //alert(orderID);
               detail_order_id="detail_order_"+ orderID;
               
               if ($("#"+detail_order_id).css("display")=="none")
               {
                  $("#"+detail_order_id).show(300) ;
               }  else
               {
                  $("#"+detail_order_id).hide(300); 
               }
              return;  
            })  
              
              
              
              
          })
         </script>
<div class="blankSeparator"></div>
</div>
<div class="blankSeparator"></div>
