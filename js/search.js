var data_back=null;
//var search_block="";
var search_analog_block="";
var search_block_main=".content";
var search_item_item=""; 
$(function(){

   $("#search_button_1").click(function()
   {
       
      
       var search_value=$("#search_value").val();
       //search_value=$("#search_value").attr('value');
      
       //alert(search_value);
       if  (search_value=='underfined')
       {
           return false;
       }
       params={};
       params.ITEM_CODE=search_value;
       
       $.ajax({
                 type:"POST",
                 url:"/search/search_item.php",
                 dataType:"html", 
                 data: params,
                 cache:false,       
                 success:function(data)
                 {
                 //  alert(data);
                     $(search_block).html("");    //catalog_show_items
                     $(search_block).html(data);   
                    // $(search_block).css("margin-top","7%");
                      
                 },        
                 error: function(XMLHttpRequest, textStatus, errorThrown)
                   {
                       
                       
                   }   
                 
                 
       })
       
       
       
   })

   $("#search_button").click(function()
   {
       console.log(search_block_main);
      $(search_block_main).html("");
      $(search_block_main).append("<div class='roww' id='item_search_item'></div>");
      $(search_block_main).append("<div class='roww' id='item_analog_search'></div>");  
      search_item_item="#item_search_item";
      search_analog_block="#item_analog_search";
       var search_value=$("#search_value").val();
       
       SearchItemCode(search_value,"",search_item_item);
        
       SearchItemCode(search_value,"",search_analog_block);
       
   })
    $('body').on('click', '.navlink', function(event) 
    {
        event.preventDefault();
       SerarchItemCodeWithPageNumber($(this),search_item_item); 
    } 
   
   );
    $('body').on('click', '.navlink_analogs', function(event) 
    {
        event.preventDefault();
       SerarchItemCodeWithPageNumber($(this),search_analog_block); 
    } 
   
   );

    var SearchItemCode=function (ItemCode,GetParam,divtofill)
    {
        var search_value=ItemCode;
       //search_value=$("#search_value").attr('value');
      
       //alert(search_value);
       if  (search_value=='underfined')
       {
           return false;
       }
       params={};
       params.ITEM_CODE=search_value;
       if (divtofill=="#item_analog_search")
       {
          params.ANALOGS="1"; 
       }
       
       
       $.ajax({
                 type:"POST",
                 url: "/search/search_item.php"+GetParam , 
                 dataType:"html", 
                 data: params,
                 cache:false,                
                 success:function(data)
                 {
                    //alert(divtofill);
                    $(divtofill).html("");    //catalog_show_items
                    $(divtofill).html(data);   
                      
                 },        
                 error: function(XMLHttpRequest, textStatus, errorThrown)
                   {
                       
                    data_back=false;  
                   }   
                 
                 
       }) 
       
      
    }

    var SerarchItemCodeWithPageNumber= function(element,divtofill)
    {
        page_number=element.attr("pageNumber");
        itemCode=element.attr("itemCode"); 
        
        SearchItemCode(itemCode,"?pageNumber="+page_number,divtofill);
        
    }




})