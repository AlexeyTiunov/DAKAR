/*$(function(){
	
	$("#itg_code").keyup(function(event)
			{
		var searh = this.value;
		var tmpTable = $("#nameTmpTable").val();
		//$("#codeVariant").text(tmpTable);
		$.get("/bitrix/components/itg/tmp.table/CitgTempTable.php",{search:searh, field:'ARTICLE',table:tmpTable},function(data)
				{

			$("#codeVariant").html(data);
			$("#codeVariant").css({"border": "1px solid #F00"});
				});
			});
});*/