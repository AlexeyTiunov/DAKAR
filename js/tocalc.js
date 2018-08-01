$(function(){
   var totalsum; 
  $("#type_auto").change(function()
  {
    
      id=$(this).attr("id");
      params={};
      params.typeAutoId=$("#"+id+" option:selected").val();       
          
        $.ajax({
                 type:"POST",
                 url:"/tocalc/tocalc_findmodels.php",
                 dataType:"html", 
                 data: params,
                 cache:false,       
                 success:function(data)
                 {
                    
                     //alert(data); 
                     $("#type_model").html("");                     
                     $("#type_model").html("<option value='0'>Выберите модель</option>"+data);          
                     
                     
                     
                 },        
                 error: function(XMLHttpRequest, textStatus, errorThrown)
                   {
                       $("#PictureSlide").html("");
                       $("#PictureSlide").html(textStatus);  
                       
                   }   
        
        });
         
      
      
      
      
      
      
      
  }) 
  
  $("#type_model").change(function()
  {
      
    id=$(this).attr("id");
      params={};
      params.ModelId=$("#"+id+" option:selected").val();   
      
       $.ajax({
                 type:"POST",
                 url:"/tocalc/catalog_findcarmodelstypes.php",
                 dataType:"html", 
                 data: params,
                 cache:false,       
                 success:function(data)
                 {
                    $("#type_modeltype").html("");                     
                     $("#type_modeltype").html("<option value='0'>Выберите модель</option>"+data);   
                     
                 },        
                 error: function(XMLHttpRequest, textStatus, errorThrown)
                   {
                       $("#type_modeltype").html("");
                       $("#type_modeltype").html(textStatus);  
                       
                   }   
                 
                 
       })
      
      
      
      
      
      
      
      
      
      
  }); 
    
  $("select").change(function(){
      
       //alert("success")
    brandValue=$("#type_auto").val();   
    modelValue= $("#type_model").val();
    modelTypeValue= $("#type_modeltype").val();      
    mileAgeValue= $("#type_mileage").val(); 
     language=$("#type_auto").attr("lang");
    if (brandValue=='0' || modelValue=='0' || mileAgeValue=='0' || modelTypeValue=='0')
    {
        $("#calc_result_plan_ts").html("");
        return false;
    } 
     params={};
     params.BrandID=brandValue;
     params.ModelID=modelValue ;
     params.ModelTypeID=modelTypeValue ;
     params.MileAgeID=mileAgeValue;
     params.LNG=language; 
     
    $.ajax({
                 type:"POST",
                 url:"/tocalc/tocalc_calculate_price.php",
                 dataType:"html", 
                 data: params,
                 cache:false,       
                 success:function(data)
                 {
                   // alert(data); 
                    $("#calc_result_plan_ts").html("");
                    $("#calc_result_plan_ts").html(data);
                    $("span#total_sum").html("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
                     $("#main_service").trigger("click"); 
                     $("#main_service").prop("checked","checked");  
                     
                 },        
                 error: function(XMLHttpRequest, textStatus, errorThrown)
                   {
                       $("#full_calc_result").html("");
                       $("#full_calc_result").html("WW"+textStatus);  
                       
                   }   
        
        });
     // $(".service_check").trigger("click");   
         
  });
  $("select").change(function(){
     if ($(this).attr("id")=="ts_services")
     {
            return false;
        }  
     brandValue=$("#type_auto").val();   
     modelValue= $("#type_model").val(); 
     modelTypeValue= $("#type_modeltype").val(); 
     language=$("#type_auto").attr("lang");  
   
        if (brandValue=='0' || modelValue=='0' || modelTypeValue=='0')
        {
             $("#calc_result_plan_ts").html("");
            return false;
        }  
       params={};
       params.BrandID=brandValue;
       params.ModelID=modelValue ;
       params.LNG=language;
        
        $.ajax({
                 type:"POST",
                 url:"/tocalc/tocalc_findservices.php",
                 dataType:"html", 
                 data: params,
                 cache:false,       
                 success:function(data)
                 {
                     //alert(data);
                     $("#ts_services").html("");
                     $("#ts_services").html("<option value='0'>Выберите работу</option>"+data); 
                     
                 },        
                 error: function(XMLHttpRequest, textStatus, errorThrown)
                   {
                       $("#full_calc_result").html("");
                       $("#full_calc_result").html("ww"+textStatus);  
                       
                   }   
        
        });
      
      
      
      
      
  });
  $("#ad_ts_service").click(function(){
       
         brandValue=$("#type_auto").val();   
         modelValue= $("#type_model").val();
         modelTypeValue= $("#type_modeltype").val();
         serviceIDValue=$("#ts_services").val();    
         mileAgeValue='0' 
    if (brandValue=='0' || modelValue=='0' || serviceIDValue=='0' || modelTypeValue=='0')  
    {
        return false;
    } 
     params={};
     params.BrandID=brandValue;
     params.ModelID=modelValue ;
     params.ModelTypeID=modelTypeValue ;
     params.MileAgeID=mileAgeValue; 
    $.ajax({
                 type:"POST",
                 url:"/tocalc_calculate_price.php",
                 dataType:"html", 
                 data: params,
                 cache:false,       
                 success:function(data)
                 {
                   // alert(data); 
                   htmlData=$("#calc_result_ts").html();
                   // $("#calc_result_ts").html(htmlData+data);
                    $("#calc_result_ts").append(data);
                    $("span#total_sum").html("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
                    // $("#main_service").trigger("click"); 
                    // $("#main_service").prop("checked","checked"); 
                    totalsum=0;
                    $(".service_check").each(function(){
                    if ($(this).attr("checked")=="checked") 
                    {
                       totalsum+=($(this).attr("price")*1);   
                    } 
                    
            
            
                    }) 
                     $("span#total_sum").text(totalsum); 
                     
                 },        
                 error: function(XMLHttpRequest, textStatus, errorThrown)
                   {
                       $("#full_calc_result").html("");
                       $("#full_calc_result").html(textStatus);  
                       
                   }   
        
        });
     // $(".service_check").trigger("click");   
           
      
      
      
  });
   
  $(".service_check").live("click",function(){
      
    //alert("www");    
    // alert($(this).attr('price')); 
         totalsum=0;
        $(".service_check").each(function(){
          if ($(this).attr("checked")=="checked")  
           totalsum+=($(this).attr("price")*1);  
            
            
        }) 
        $("span#total_sum").text(totalsum);
      //  alert (totalsum);
  });

  $("#ts_ts").click(function(){
     
     $("#calc_option_plan_ts").css("display","none");
     $("#calc_result_plan_ts").css("display","none");
     $("#calc_option_ts").css("display","block");  
     $("#calc_result_ts").css("display","block");   
      
      
  });
   $("#ts_plan").click(function(){
     
     $("#calc_option_plan_ts").css("display","block");
     $("#calc_result_plan_ts").css("display","block");  
     $("#calc_option_ts").css("display","none");  
     $("#calc_result_ts").css("display","none");    
      
      
  });

})