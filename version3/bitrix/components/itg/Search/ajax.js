$(function()
{

	var paramTostr = '';
	var triggerToSend = false;
	var sameTime = false;
	//for (x in paramsITG)
	//{
	//	paramTostr += (paramsITG[x] + " ");
	//}
	//alert(paramTostr);
	if (window.paramsITG)
	{
		var urlQueryAjax = '';
		if(location.pathname == '/autodoc/search_14112011.php') urlQueryAjax = "/bitrix/components/itg/Search/ajaxRequest2.php";
		else urlQueryAjax = "/bitrix/components/itg/Search/ajaxRequest.php";
		$.get(urlQueryAjax, paramsITG, function(data)
		{
			//alert(data);
			$('tr.searchAnalogITG').before(data);
			var htmltxt = "<div align='center' id='itemSearchITG'>";
			htmltxt += "<table id='tableFindedItemsITG'>";
			htmltxt += "<tr>";
			htmltxt += "<th style='width:30px;'>№</th>";
			htmltxt += "<th style='width:80px;'>Бренд</th>";
			htmltxt += "<th style='width:100px;'>Код позиции</th>";
			htmltxt += "<th style='width:90px;'>Наименование</th>";
			htmltxt += "<th style='width:30px;'>Срок <br>пост-<br>авки<br>(Дни)</th>";
            htmltxt += "<th style='width:30px;'>Кол-во</th>";  
			htmltxt += "<th style='width:80px;'>Регион</th>";
            htmltxt += "<th style='width:80px;'>%</th>"; 
			htmltxt += "<th style='width:50px;'>Вес</th>";
			htmltxt += "<th style='width:80px;'>Цена</th>";
			htmltxt += "<th style='width:100px;'>Цена <br>"+paramsITG.currency+"</th>";
			if(paramsITG.auth) htmltxt += "<th style='width:120px;border:solid 1px #8b8b8b;color:white;'>Действие</th></tr>";
			if(data)htmltxt += data;
			else htmltxt = "По Вашему запросу ничего не найдено...";
			$('#notFind').html(htmltxt);
			
		});
	}
	$('body').keyup(function(event) 
	{
		//alert(event.keyCode);
		if (event.keyCode == '13' && triggerToSend) 
		{
			showCicle();
			return false;
		}
		if(event.keyCode == '13' && !triggerToSend)
		{
			showCicle();
			triggerToSend = true;
		}
	});
	$('form[name="datasearch"]').one(
	'submit', function(e)
	{
		if(!triggerToSend)
		{
			showCicle();
		}
		else
		{
			return false;
		}
		
	});
	$('div#brandSearchITG a').click(function()
	{
		if(!triggerToSend)
		{
			showCicle();
			triggerToSend = true;
		}
		else
		{
			return false;
		}
	}
	);
	/*$('td > a.itg-basket-style').live(
	{
		click : function()
		{
			//var txt = $(this).parent().parent().find(':input[type="hidden"]').filter('[name="BrandCode"]').val();//
			//alert(txt);
			var ParamBasket = {
					bcode: 		$(this).parent().parent().find(':input[type="hidden"]').filter('[name="BrandCode"]').val(),
					bscode: 	$(this).parent().parent().find(':input[type="hidden"]').filter('[name="BrandShortCode"]').val(),
					icode: 		$(this).parent().parent().find(':input[type="hidden"]').filter('[name="ItemCode"]').val(),
					caption: 	$(this).parent().parent().find(':input[type="hidden"]').filter('[name="Caption"]').val(),
					rcode: 		$(this).parent().parent().find(':input[type="hidden"]').filter('[name="RegionCode"]').val(),
					delivery: 	$(this).parent().parent().find(':input[type="hidden"]').filter('[name="DeliveryDays"]').val(),
					currency: 	$(this).parent().parent().find(':input[type="hidden"]').filter('[name="CurrencyCode"]').val(),
					price: 		$(this).parent().parent().find(':input[type="hidden"]').filter('[name="Price"]').val(),
					quantity:	$(this).parent().parent().find(':input[type="text"]').filter('[name="Quantity"]').val()//BrandShortCode
			};
			var paramTostr = '';
			//for (x in ParamBasket)
			//{
			//	paramTostr += (ParamBasket[x] + " ");
			//}
			//alert(paramTostr);
			$.get("/bitrix/components/itg/Basket/Add.php", ParamBasket, function(data)
			{
				//alert(data);
				var arData = data.split('|');
				$('#addition_status').html(arData[1]);
				$("ul#vertical-multilevel-menu li a[href='/autodoc/mycart.php']").html("КОРЗИНА ( "+arData[0]+" ) ");
				
			});
			$(this).next('span').html("<img src='/images/ok2.gif'>");
			return false;
		}
		//mouseover: function(){$(this).find('img').replaceWith("<img style='width:30px;float:right;' title='Добавить в корзину' src=\"http://parts.avtodok.com.ua/bitrix/components/itg/Search/basketActive.png\" />")},
		//mouseout: function(){$(this).find('img').replaceWith("<img style='width:30px;float:right;' title='Добавить в корзину' src=\"http://parts.avtodok.com.ua/bitrix/components/itg/Search/basket.png\" />")}
	}); */
	$('span > a.itg-basket-style').click(
	function()
	{
		var ParamsAnalog = {
				bcode: 		$(':input[type="hidden"]').filter('[name="BrandCode"]').val(),
				icode: 		$(':input[type="hidden"]').filter('[name="ItemCode"]').val()
		};
		//var paramTostr = '';
		//for (x in ParamsAnalog)
		//{
		//	paramTostr += (ParamsAnalog[x] + " ");
		//}
		//alert(paramTostr);
		$('table#tableFindedItemsITG tr:last td').html('Ведется поиск...');
		$.get("/bitrix/components/itg/Search/ajaxRequestAnalog.php", ParamsAnalog, function(data)
		{
			//alert(data);
			//var arData = data.split('|');
			if (data)
			{
				$('table#tableFindedItemsITG tr:last td#analogsp').html('Аналоги:');
				$('table#tableFindedItemsITG tr:last').after(data);
			}
			else
			{
				$('table#tableFindedItemsITG tr:last td#analogsp').html('Аналоги не найдены...');
			}
		});
		return false;
	});
	
	function showCicle()
	{
		if(!triggerToSend)
		{
			var x = ''+$(window).width()/2+'px';
			var y = ''+$(window).height()/2+'px';
			$('form').hide('slow');
			$('#animatedIcon').show().css({'position':'absolute','top':y,'left':x});
			$('#header').wrap("<div style='opacity:0.7;background-color:black;z-index:10000;' />");
			$('#middle').wrap("<div style='opacity:0.7;background-color:black;z-index:10000;' />");
		}
		else
		{
			var x = ''+(($(window).width()/2) - 250)+'px';
			var y = ''+(($(window).height()/2) - 175)+'px';
			$('#animatedIcon2').show('slow').css({'position':'absolute','top':y,'left':x});
		}
		
	}
	if(window.basketCount)
	{
		$("ul#vertical-multilevel-menu li a[href='/autodoc/mycart.php']").html("КОРЗИНА ( "+basketCount+" ) ");
	}
});