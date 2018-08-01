$(function(){

   $("#group_select").change(function(){
     
     
      id=$(this).attr("id");
      params={};
      params.groupID=$("#"+id+" option:selected").val();
      $.ajax({
                 type:"POST",
                 url:"/catalog_service.php",
                 dataType:"html", 
                 data: params,
                 cache:false,       
                 success:function(data)
                 {
                    
                    // alert(data); 
                     $("#group_type_div").html("");                     
                     $("#group_type_div").html(data);          
                     
                     
                     
                 },        
                 error: function(XMLHttpRequest, textStatus, errorThrown)
                   {
                       $("#PictureSlide").html("");
                       $("#PictureSlide").html(textStatus);  
                       
                   }   
        
        }); 
       
       
       
       
   }); 

    $("#tecdoc_model_id").live('change',function(){
        
      id=$(this).attr("id");
      params={};
      params.modelID=$("#"+id+" option:selected").val();
      $.ajax({
                 type:"POST",
                 url:"/catalog_service.php",
                 dataType:"html", 
                 data: params,
                 cache:false,       
                 success:function(data)
                 {
                    
                    // alert(data); 
                     $("#tecdoc_model_types_id_div").html("");                     
                     $("#tecdoc_model_types_id_div").html(data);          
                     
                     
                     
                 },        
                 error: function(XMLHttpRequest, textStatus, errorThrown)
                   {
                       $("#PictureSlide").html("");
                       $("#PictureSlide").html(textStatus);  
                       
                   }   
        
        }); 
       
        
        
        
        
    })

    $('#search_model').click(function(){
        
       name_str=$("#search_model_name").val();
       brand_code=$("#search_model_name_div  #brand_select").val();
      // alert(brand_code+name_str);
       
       if (name_str==undefined || brand_code==0 || brand_code=="0")
       {
           return false;
       } 
       $("#model_search_result").html("Идет Поиск"); 
      params={};
      params.model_name_search=name_str;
      params.model_brand_search=brand_code; 
      $.ajax({
                 type:"POST",
                 url:"/catalog_service.php",
                 dataType:"html", 
                 data: params,
                 cache:false,       
                 success:function(data)
                 {
                    
                    // alert(data); 
                     $("#model_search_result").html("");                     
                     $("#model_search_result").html(data);          
                     
                     
                     
                 },        
                 error: function(XMLHttpRequest, textStatus, errorThrown)
                   {
                       $("#model_search_result").html("");
                       $("#model_search_result").html(textStatus);  
                       
                   }   
        
        });   
        
        
        
        
        
        
        
    })
    
    $('#search_model').click(function(){ 
         
      common_brand_id=$("#search_model_name_div  #brand_select").val();   
      params={};
      params.common_brand_id=common_brand_id;
      params.find_exit_model_types="Y" ;
      
     // alert(params.common_brand_id);
      $.ajax({
                 type:"POST",
                 url:"/catalog_service.php",
                 dataType:"html", 
                 data: params,
                 cache:false,       
                 success:function(data)
                 {
                    
                    // alert(data); 
                     $("#tie_model_div").html("");                     
                     $("#tie_model_div").html(data+"<br><input  type='submit' id='model_tie' name='model_tie' value='Привязать Модель'  style='border-radius:5px; height:35px; margin-bottom: 1%; float:left;'> </input>");          
                     
                     
                     
                 },        
                 error: function(XMLHttpRequest, textStatus, errorThrown)
                   {
                       $("#tie_model_div").html("");
                       $("#tie_model_div").html(textStatus);  
                       
                   }   
        
        });   
          
        selected_brand_id= $("#search_model_name_div #brand_select option:selected" ).val(); 
     // alert(selected_brand_id);   
      $("#add_model_div #brand_select option[value='"+selected_brand_id+"']" ).attr("selected", "selected");  
         
         
         
         
     })
    // changed 04.05.2016   
    $("#add_model").click(function(){
        
      brand_id=$("#add_model_div #brand_select").val();
      tecdoc_model_id=$("#model_id_add").val();  
      model_name=$("#model_name_add").val();
      
     // alert(brand_id+tecdoc_model_id+model_name);
      if  (model_name=="")
      {
           alert(brand_id+tecdoc_model_id+model_name); 
          return;
      } 
      
        params={};
      params.common_brand_id=brand_id;     
      params.tecdoc_model_id=tecdoc_model_id;
      params.model_name=model_name;
      params.add_model_id="Y"
      $.ajax({
                 type:"POST",
                 url:"/catalog_service.php",
                 dataType:"html", 
                 data: params,
                 cache:false,       
                 success:function(data)
                 {
                    
                     alert(data); 
                     $("#model_add_p_info").html("");                     
                     $("#model_add_p_info").html(data);          
                     alert(data);
                     
                     
                 },        
                 error: function(XMLHttpRequest, textStatus, errorThrown)
                   {
                       $("#model_add_p_info").html("");
                       $("#model_add_p_info").html(textStatus);  
                       
                   }   
        
        }); 
         
        
        
        
    }) 
   /* $("a.tecdoc_finded_models").live('click',function()
    {
        
       text=$(this).text(); 
       id=$(this).attr('id');
       
       $("#model_id_add").val(id);
       $("#model_name_add").val(text); 
       
      selected_brand_id= $("#search_model_name_div #brand_select option:selected" ).val(); 
        
      $("#add_model_div #brand_select option[value='"+selected_brand_id+"']" ).attr("selected", "selected");  
        
        
        
        
        
        
        
    }) */  
    $("input.tecdoc_finded_models").live('click',function()
    {
        
       text=$(this).text(); 
       id=$(this).attr('id');
       
       //$("#model_id_add").val(id);
       //$("#model_name_add").val(text); 
       
        if ($(this).attr("checked")=="checked") 
       {
           if ($("#model_id_add").val()=="" || $("#model_id_add").val==null)
           {
              $("#model_id_add").val(id);  
               
           } else
           {
               previousValue=$("#model_id_add").val(); 
              $("#model_id_add").val(previousValue+"#"+id);
               
           }
           
           
           
       } else
       {
            nowValue=$("#model_id_add").val();          
          newValue=nowValue.replace(""+id,"");       
          $("#model_id_add").val(newValue);    
          
          nowValue=$("#model_id_add").val();
          pattern=/\#\#/;
          newValue=nowValue.replace(pattern,"#");
          $("#model_id_add").val(newValue);
          
          nowValue=$("#model_id_add").val();
          pattern=/^(.*)(\#)$/;
          newValue=nowValue.replace(pattern,"$1");
          $("#model_id_add").val(newValue);
          
          
          
         //alert(newValue);
           
       }
       
       
       
      selected_brand_id= $("#search_model_name_div #brand_select option:selected" ).val(); 
     // alert(selected_brand_id);   
      $("#add_model_div #brand_select option[value='"+selected_brand_id+"']" ).attr("selected", "selected");  
        
        
        
        
        
        
        
    })   
    
      
    /* $("a.tecdoc_finded_models_types").live('click',function(){
         
        text=$(this).text(); 
        id=$(this).attr('id'); 
       
       if ($("#tecdoc_model_type_id_add").val()=="" || $("#tecdoc_model_type_id_add").val==null)
       {
         $("#tecdoc_model_type_id_add").val(id);  
       } else
       {
          previousValue=$("#tecdoc_model_type_id_add").val(); 
          $("#tecdoc_model_type_id_add").val(previousValue+"#"+id); 
       } 
       //$("#tecdoc_model_type_id_add").val(id);
       $("#tecdoc_model_type_name_add").val(text);   
         
         
     }) */
      $("input.tecdoc_finded_models_types").live('click',function(){
         
        text=$(this).text(); 
        id=$(this).attr('id'); 
       
       if ($(this).attr("checked")=="checked") 
       {
       
           if ($("#tecdoc_model_type_id_add").val()=="" || $("#tecdoc_model_type_id_add").val==null)
           {
             $("#tecdoc_model_type_id_add").val(id);  
           } else
           {
              previousValue=$("#tecdoc_model_type_id_add").val(); 
              $("#tecdoc_model_type_id_add").val(previousValue+"#"+id); 
           } 
           //$("#tecdoc_model_type_id_add").val(id);
           $("#tecdoc_model_type_name_add").val(text);   
       }else
       {
          nowValue=$("#tecdoc_model_type_id_add").val();          
          newValue=nowValue.replace(""+id,"");       
          $("#tecdoc_model_type_id_add").val(newValue);    
          
          nowValue=$("#tecdoc_model_type_id_add").val();
          pattern=/\#\#/;
          newValue=nowValue.replace(pattern,"#");
          $("#tecdoc_model_type_id_add").val(newValue);
          
          nowValue=$("#tecdoc_model_type_id_add").val();
          pattern=/^(.*)(\#)$/;
          newValue=nowValue.replace(pattern,"$1");
          $("#tecdoc_model_type_id_add").val(newValue);
          
          
          
          alert(newValue);
         // $("#tecdoc_model_type_id_add").val(newValue); 
       }  
         
     })
    
    $("#search_model_type_name_div #brand_select").change(function(){
        
      id=$(this).attr("id");
      params={};
      params.common_brand_id=$("#search_model_type_name_div #"+id+" option:selected").val();
      params.find_exit_model_types="Y" ;
      
     // alert(params.common_brand_id);
      $.ajax({
                 type:"POST",
                 url:"/catalog_service.php",
                 dataType:"html", 
                 data: params,
                 cache:false,       
                 success:function(data)
                 {
                    
                    // alert(data); 
                     $("#type_select_div").html("");                     
                     $("#type_select_div").html(data);          
                     
                     
                     
                 },        
                 error: function(XMLHttpRequest, textStatus, errorThrown)
                   {
                       $("#type_select_div").html("");
                       $("#type_select_div").html(textStatus);  
                       
                   }   
        
        });   
        
        
        
        
        
        
    })
    //
     $("#group_type_div_brand #brand_select").change(function(){
        
      id=$(this).attr("id");
      params={};
      params.common_brand_id=$("#group_type_div_brand #"+id+" option:selected").val();
      params.find_exit_model_types="Y" ;
      
     // alert(params.common_brand_id);
      $.ajax({
                 type:"POST",
                 url:"/catalog_service.php",
                 dataType:"html", 
                 data: params,
                 cache:false,       
                 success:function(data)
                 {
                    
                    // alert(data); 
                     $("#tecdoc_model_id_div").html("");                     
                     $("#tecdoc_model_id_div").html(data);          
                     
                     
                     
                 },        
                 error: function(XMLHttpRequest, textStatus, errorThrown)
                   {
                       $("#tecdoc_model_i").html("");
                       $("#tecdoc_model_i").html(textStatus);  
                       
                   }   
        
        });   
        
        
        
        
        
        
    })
    
    
    
    
    $("#type_select_div #tecdoc_model_id").live('change',function(){
     
       id=$(this).attr("id");
       brand_id=$("#search_model_type_name_div #brand_select").val();
       params={};
       params.search_modelID=$("#type_select_div #"+id+" option:selected").val();
       params.search_brand_id=brand_id;
       params.find_tecdoc_model_types="Y" ; 
       $("#model_type_search_result").html("Идет Поиск"); 
      $.ajax({
                 type:"POST",
                 url:"/catalog_service.php",
                 dataType:"html", 
                 data: params,
                 cache:false,       
                 success:function(data)
                 {
                    
                    // alert(data); 
                     $("#model_type_search_result").html("");                     
                     $("#model_type_search_result").html(data);          
                     
                     
                     
                 },        
                 error: function(XMLHttpRequest, textStatus, errorThrown)
                   {
                       $("#model_type_search_result").html("");
                       $("#model_type_search_result").html(textStatus);  
                       
                   }   
        
        }); 
       
           
        
        
        
        
        
        
        
    })
    
    $("#type_select_div #tecdoc_model_id").live('change',function(){
        
         id=$(this).attr("id");
       brand_id=$("#search_model_type_name_div #brand_select").val();
       params={};
       params.search_modelID=$("#type_select_div #"+id+" option:selected").val();
       params.search_brand_id=brand_id;
       params.find_tecdoc_model_types_types="Y" ; 
       $("#model_type_search_result").html("Идет Поиск"); 
      $.ajax({
                 type:"POST",
                 url:"/catalog_service.php",
                 dataType:"html", 
                 data: params,
                 cache:false,       
                 success:function(data)
                 {
                    
                    // alert(data); 
                     $("#type_type_select_div").html("");
                     $("#type_type_select_div").html(data+"<input  type='submit' id='model_type_tie' name='model_type_tie' value='Привязать к модели'  style='float:left;border-radius:5px; height:35px; margin-bottom: 1%; float:left;'> </input> ");                     
                    // $("#type_type_select_div").html(data);          
                     
                     
                     
                 },        
                 error: function(XMLHttpRequest, textStatus, errorThrown)
                   {
                       $("#type_type_select_div").html("");
                       $("#type_type_select_div").html(textStatus);  
                       
                   }   
        
        }); 
       
            
        
        
        
        
        
    });
    
    
    $("#model_type_add").live('click',function(){
        
      brand_id=$("#search_model_type_name_div #brand_select").val();
      model_id=$("#type_select_div #tecdoc_model_id").val();  
      tecdoc_model_type_id =$("#tecdoc_model_type_id_add").val();
      model_type_name=$("#tecdoc_model_type_name_add").val();
     
    // alert(brand_id+model_id+tecdoc_model_type_id+model_type_name)
         
      params={};
      params.common_brand_id=brand_id;
      params.model_id=model_id;
      params.tecdoc_model_type_id=tecdoc_model_type_id;
      params.model_type_name=model_type_name;
      params.add_model_type_id="Y"
      $.ajax({
                 type:"POST",
                 url:"/catalog_service.php",
                 dataType:"html", 
                 data: params,
                 cache:false,       
                 success:function(data)
                 {
                    
                    // alert(data); 
                     $("#model_type_add_p_info").html("");                     
                     $("#model_type_add_p_info").html(data);          
                     
                     
                     
                 },        
                 error: function(XMLHttpRequest, textStatus, errorThrown)
                   {
                       $("#model_type_add_p_info").html("");
                       $("#model_type_add_p_info").html(textStatus);  
                       
                   }   
        
        }); 
         
       $("#type_select_div #tecdoc_model_id").trigger('change'); 
       $("#tecdoc_model_type_name_add").val(""); 
       $("#tecdoc_model_type_id_add").val(""); 
        
        
    })
    $("#model_type_tie").live('click',function(){
        
          brand_id=$("#search_model_type_name_div #brand_select").val();
          model_type_id=$("#type_type_select_div #common_model_type_id").val();  
          tecdoc_model_type_id =$("#tecdoc_model_type_id_add").val();
         // model_type_name=$("#tecdoc_model_type_name_add").val();
         
        // alert(model_type_id);
             
          params={};
          params.common_brand_id=brand_id;
          params.model_type_id=model_type_id;
          params.tecdoc_model_type_id=tecdoc_model_type_id;
         // params.model_type_name=model_type_name;
          params.tie_model_type_id="Y"
          $.ajax({
                     type:"POST",
                     url:"/catalog_service.php",
                     dataType:"html", 
                     data: params,
                     cache:false,       
                     success:function(data)
                     {
                        
                        // alert(data); 
                         $("#model_type_add_p_info").html("");                     
                         $("#model_type_add_p_info").html(data);          
                         
                         
                         
                     },        
                     error: function(XMLHttpRequest, textStatus, errorThrown)
                       {
                           $("#model_type_add_p_info").html("");
                           $("#model_type_add_p_info").html(textStatus);  
                           
                       }   
            
            }); 
         
        
        
        
        
        
    })
    
    $("#model_tie").live('click',function(){
        
      model_id=$("#tie_model_div #tecdoc_model_id").val();
      brand_id=$("#add_model_div #brand_select").val(); 
      tecdoc_model_id =$("#model_id_add").val();      
      
      
       params={};
       params.common_brand_id=brand_id;
       params.model_id=model_id;
       params.tecdoc_model_id=tecdoc_model_id;       
       params.tie_model_id="Y"
       
        $.ajax({
                     type:"POST",
                     url:"/catalog_service.php",
                     dataType:"html", 
                     data: params,
                     cache:false,       
                     success:function(data)
                     {
                        
                        // alert(data); 
                         $("#model_add_p_info").html("");                     
                         $("#model_add_p_info").html(data);          
                         
                         
                         
                     },        
                     error: function(XMLHttpRequest, textStatus, errorThrown)
                       {
                           $("#model_add_p_info").html("");
                           $("#model_add_p_info").html(textStatus);  
                           
                       }   
            
            }); 
          
        
        
    })

})