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

    $("#tecdoc_model_id").change(function(){
        
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

    $("a.tecdoc_finded_models").live('click',function(){
        
       text=$(this).text(); 
       id=$(this).attr('id');
       
       $("#model_id_add").val(id);
       $("#model_name_add").val(text); 
       
      selected_brand_id= $("#search_model_name_div #brand_select option:selected" ).val(); 
     // alert(selected_brand_id);   
      $("#add_model_div #brand_select option[value='"+selected_brand_id+"']" ).attr("selected", "selected");  
        
        
        
        
        
        
        
    })   //type_select_div

     $("a.tecdoc_finded_models_types").live('click',function(){
         
        text=$(this).text(); 
       id=$(this).attr('id'); 
         
        $("#tecdoc_model_type_id_add").val(id);
       $("#tecdoc_model_type_name_add").val(text);   
         
         
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
    
    
    
    $("#model_type_add").live('click',function(){
        
      brand_id=$("#search_model_type_name_div #brand_select").val();
      model_id=$("#type_select_div #tecdoc_model_id").val();  
      tecdoc_model_type_id =$("#tecdoc_model_type_id_add").val();
      model_type_name=$("#tecdoc_model_type_name_add").val();
     
     alert(brand_id+model_id+tecdoc_model_type_id+model_type_name)
         
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
         
        
        
        
        
        
    })

})