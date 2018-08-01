$(function()   
{
    $('a#OfferAA').click( function()   

      {
            $('#dialog').dialog('close');
           $('#tableFindedItemsITG').remove(); 
            $('#brandSearchT').remove(); 
          
          paramssITG =     {
                                           icode:     $(this).find(':input[type="hidden"]').filter('[name="ItemCode"]').val(), 
                                            web:        'connect',
                                          
                                           };
                                           
                    urlQueryAjax = '/personal/suppload/search.php';
                  $.get(urlQueryAjax, paramssITG, function(data)
                   {

                    
                    $('div#content').html(data);
                       
                   } 
                   );
          
          
       
    }  );
        
         
    
});