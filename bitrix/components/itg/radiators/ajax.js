$(function()
{
    var brand = '';
    var model = '';
    var dateb = '';
    $('#BrandCode').change(function()
            {
        //alert(this.value);
        brand = this.value;
        model = $('#ModelRange').val();
        dateb = $('#DateBegin').val();
        radtype=$('#RadType').val();
            });
    $('#ModelRange').change(function()
            {
        //alert(this.value);
        model = this.value;
        brand = $('#BrandCode').val();
        dateb = $('#DateBegin').val();
        radtype=$('#RadType').val();
            });
    $('#DateBegin').change(function()
            {
        //alert(this.value);
        dateb = this.value;
        model = $('#ModelRange').val();
        brand = $('#BrandCode').val();
        radtype=$('#RadType').val();
            });
     $('#RadType').change(function()
            {
        //alert(this.value);
        radtype = this.value;
        model = $('#ModelRange').val();
        brand = $('#BrandCode').val();
        dateb = $('#DateBegin').val();
            });         
    $('select').change(function(){requestFunctopn()});
    $('#resFilter').click(function()
            {
        $('#BrandCode').val('');
        $('#ModelRange').val('');
        $('#DateBegin').val('');
        brand = '';
        model = '';
        dateb = '';
        radtype= ''; 
        requestFunctopn();
            });
    function requestFunctopn()
    {
    var params = {};
    if(brand != '') params.brand = brand;
    if(model != '') params.model = model;
    if(dateb != '') params.dateb = dateb;
    if(radtype != '') params.radtype = radtype;
    //alert(params + model);
    $.get("/bitrix/components/itg/radiators/ajaxRequest.php", params, function(data)
        {
    //alert("Data Loaded: "+data);
    getArrData = data.split("#");
    for(i=0; i<getArrData.length; i++)
        {
        if(getArrData[i].search("BrandCode") != -1)
            {
            brandToInsert = getArrData[i].replace("BrandCode","");
            //if (brandToInsert.lenght > 30)
            $('#BrandCode').html(brandToInsert);
            }
        if(getArrData[i].search("ModelRange") != -1)
            {
            modelToInsert = getArrData[i].replace("ModelRange","");
            //if (modelToInsert.lenght > 30)
            $('#ModelRange').html(modelToInsert);
            }
        if(getArrData[i].search("DateBegin") != -1)
            {
            dateToInsert = getArrData[i].replace("DateBegin","");
            //if (dateToInsert.lenght > 30)
            $('#DateBegin').html(dateToInsert);
            }
        if(getArrData[i].search("ListProduct") != -1)
            {
                listToInsert="<script type=\"text/javascript\" src=\"/bitrix/components/itg/radiators/buyR.js\"></script>";
            listToInsert += getArrData[i].replace("ListProduct","");
            $('#ListProduct').html(listToInsert);
            }
        }
        });
    };
    // Dialog    
    var correctPosition = true;
    $('#dialog').dialog({
        autoOpen: false,
        width: 700,
        modal: true,
        draggable: false,
        position: 'center'
        /*buttons: {
            "Приобрести": function() { 
                $(this).dialog("close"); 
            }, 
            "Вернуться": function() { 
                $(this).dialog("close"); 
            } 
        }*/
    });

    // Dialog Link
    $('.popup td:not(:has(a))').live('click', function(){
        //alert($(this).children().eq(7).text());
        //alert($(this).attr('id')); //id радиатора
        //alert($(this).parent().children().eq(7).text());
        var idRad = $(this).parent().attr('id');
        var ItemCode= $(this).attr('id');
        $.ajaxSetup({cache: false}); 
       var urll="/bitrix/components/itg/radiators/ajaxRequest.php";
       
       window.open('/bitrix/components/itg/radiators/ajaxRequestInfo.php?ItemCode='+ItemCode+'',idRad,'width=620,height=600,left=30%,top=10%');
        
     /*   $.ajax({
          type: "GET", 
          url: urll,
          datatype: "html",
          cache: false,
          data: { item: idRad },
         success: function(data){
         $("#dialog").html(data+"<script type=\"text/javascript\" src=\"/bitrix/components/itg/radiators/buyR.js\"></script>");},
         error:  function() {$("#dialog").html("error")}
         }); */ 

       /* $.get("/bitrix/components/itg/radiators/ajaxRequest.php?"+"&noCache=" + (new Date().getTime()) + Math.random(), {item: idRad}, function(data)
                    {
            $('#dialog').html(data);
                    });  */
     /*   $('#dialog').dialog('open');
        var position = $('#dialog').dialog("option","position");
    
        if (correctPosition)
            {
                $('#dialog').dialog( "option", "position", [350,5]);
                correctPosition = false;
            }
        else $('#dialog').dialog( "option", "position", 'center');
        return false;  */ 
    });

    
    //hover states on the static widgets
    $('.popup, ul#icons li').hover(
        function() { $(this).addClass('ui-state-hover'); }, 
        function() { $(this).removeClass('ui-state-hover'); }
    );
});