<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<!--
<!DOCTYPE html PUBLIC  "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
-->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?echo LANG_CHARSET;?>">
<?$APPLICATION->ShowMeta("keywords")?>
<?$APPLICATION->ShowMeta("description")?>
<title><?$APPLICATION->ShowTitle()?></title>
<?$APPLICATION->ShowCSS();?>
<?$APPLICATION->ShowHeadStrings()?>
<?$APPLICATION->ShowHeadScripts()?>
<?if(stripos($_SERVER['REQUEST_URI'],'/personal/order/radiators/') !== false)
{
	echo "<script type=\"text/javascript\" src=\"/bitrix/js/itgScript/jquery.min.js\"></script>";
	echo "<script type=\"text/javascript\" src=\"/bitrix/components/itg/radiators/ajax.js\"></script>";
	echo "<script type=\"text/javascript\" src=\"/bitrix/components/itg/radiators/jquery-ui-1.8.14.custom.min.js\"></script>";
	echo "<link type=\"text/css\" href=\"/personal/order/radiators/jquery-ui-1.8.14.custom.css\" rel=\"stylesheet\" />";
	echo "<style type=\"text/css\">
			/*demo page css*/
			body{ font: 62.5%;}
			.demoHeaders { margin-top: 2em; }
			.popup {padding: .4em 1em .4em 14px;text-decoration: none;position: relative;}
			.popup span.ui-icon {margin: 0 5px 0 0;position: absolute;left: .2em;top: 50%;margin-top: -8px;}
			ul#icons {margin: 0; padding: 0;}
			ul#icons li {margin: 2px; position: relative; padding: 4px 0; cursor: pointer; float: left;  list-style: none;}
			ul#icons span.ui-icon {float: left; margin: 0 4px;}
		</style>";
}
if(stripos($_SERVER['REQUEST_URI'],'/personal/order/catalog/') !== false)
{
	global $itgCatalogPage;
	$itgCatalogPage = 'insert';
}
if(stripos($_SERVER['REQUEST_URI'],'/personal/order/radiators/') !== false)
{
	global $itgCatalogPage;
	$itgCatalogPage = 'radiators';
}
if(stripos($_SERVER['REQUEST_URI'],'/auth/Registration/') !== false)
{
	echo "<script type=\"text/javascript\" src=\"/bitrix/js/itgScript/jquery.min.js\"></script>";
	echo "<script type=\"text/javascript\" src=\"/auth/Registration/validateBeforeSend.js\"></script>";
}
if(stripos($_SERVER['REQUEST_URI'],'/autodoc/search') !== false)
{
	echo "<script type=\"text/javascript\" src=\"/bitrix/js/itgScript/jquery.min.js\"></script>";
	echo "<script type=\"text/javascript\" src=\"/bitrix/components/itg/Search/ajax.js\"></script>";
}
if(stripos($_SERVER['REQUEST_URI'],'/personal/suppload') !== false)
{
	echo "<script type=\"text/javascript\" src=\"/bitrix/js/itgScript/jquery.min.js\"></script>";
}
?>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-25472309-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</head>
<body> <?$APPLICATION->ShowPanel();?> 
<div id="header"> 		 
  <div id="logo1"> 		 		</div>
 
<!-- #logo1-->
 		 
  <div id="logo2"> 		 		 </div>
 
<!-- #logo2-->

 
  <div id="kurs"> Курс валют <?$APPLICATION->IncludeComponent(
	"bitrix:currency.rates",
	"template1",
	Array(
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "86400",
		"arrCURRENCY_FROM" => array(0=>"USD",1=>"EUR",),
		"CURRENCY_BASE" => "UAH",
		"RATE_DAY" => "",
		"SHOW_CB" => "N"
	)
);?> </div>

  <div id="logo3"> 		 		 </div>
 		 
<!-- #search-->
 		 
  <div id="top_menu"> 			<?$APPLICATION->IncludeComponent("bitrix:menu", "horizontal_multilevel1", array(
	"ROOT_MENU_TYPE" => "top",
	"MENU_CACHE_TYPE" => "A",
	"MENU_CACHE_TIME" => "3600",
	"MENU_CACHE_USE_GROUPS" => "Y",
	"MENU_CACHE_GET_VARS" => array(
	),
	"MAX_LEVEL" => "1",
	"CHILD_MENU_TYPE" => "left",
	"USE_EXT" => "Y",
	"DELAY" => "N",
	"ALLOW_MULTI_SELECT" => "N"
	),
	false
);?> 		</div>
 
<!-- #top_menu-->
 	 	</div>
 
<!-- #header-->
 	 
<div id="middle"> 		 
  <div id="middle_up"> 
    <div id="empty"> </div>
	
<!-- #left_sb-->   
    <div id="left_sb"> 
      <div id="left_menu"> <?$APPLICATION->IncludeComponent("bitrix:menu", "vertical_multilevel1", array(
	"ROOT_MENU_TYPE" => "left",
	"MENU_CACHE_TYPE" => "N",
	"MENU_CACHE_TIME" => "3600",
	"MENU_CACHE_USE_GROUPS" => "Y",
	"MENU_CACHE_GET_VARS" => array(
	),
	"MAX_LEVEL" => "1",
	"CHILD_MENU_TYPE" => "left",
	"USE_EXT" => "N",
	"DELAY" => "N",
	"ALLOW_MULTI_SELECT" => "N"
	),
	false
);?> </div>
     
<!-- #left_menu-->
<!--    <div id="empty1"> </div> -->

      <div id="auth_up">АВТОРИЗАЦИЯ
      </div> <!-- #auth_up--> 
      <div id="auth"> 		 <?$APPLICATION->IncludeComponent("bitrix:system.auth.form", "template1", array(
	"REGISTER_URL" => "/personal/profile/index.php",
	"PROFILE_URL" => "/personal/profile/",
	"SHOW_ERRORS" => "N"
	),
	false
);?> 

</div> <!-- #auth-->
      <div id="left_sb_news">
<p style="font-weight: bold; text-align:center;">НОВОСТИ </p><br>
	   <?$APPLICATION->IncludeComponent("bitrix:news.list", "left_sb_news_list", array(
	"IBLOCK_TYPE" => "news",
	"IBLOCK_ID" => "3",
	"NEWS_COUNT" => "20",
	"SORT_BY1" => "ACTIVE_FROM",
	"SORT_ORDER1" => "DESC",
	"SORT_BY2" => "SORT",
	"SORT_ORDER2" => "ASC",
	"FILTER_NAME" => "",
	"FIELD_CODE" => array(
		0 => "",
		1 => "",
	),
	"PROPERTY_CODE" => array(
		0 => "",
		1 => "",
	),
	"CHECK_DATES" => "Y",
	"DETAIL_URL" => "",
	"AJAX_MODE" => "N",
	"AJAX_OPTION_SHADOW" => "Y",
	"AJAX_OPTION_JUMP" => "N",
	"AJAX_OPTION_STYLE" => "Y",
	"AJAX_OPTION_HISTORY" => "N",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "3600",
	"CACHE_FILTER" => "N",
	"CACHE_GROUPS" => "Y",
	"PREVIEW_TRUNCATE_LEN" => "",
	"ACTIVE_DATE_FORMAT" => "d.m.Y",
	"DISPLAY_PANEL" => "N",
	"SET_TITLE" => "Y",
	"SET_STATUS_404" => "N",
	"INCLUDE_IBLOCK_INTO_CHAIN" => "Y",
	"ADD_SECTIONS_CHAIN" => "Y",
	"HIDE_LINK_WHEN_NO_DETAIL" => "N",
	"PARENT_SECTION" => "",
	"PARENT_SECTION_CODE" => "",
	"DISPLAY_TOP_PAGER" => "N",
	"DISPLAY_BOTTOM_PAGER" => "N",
	"PAGER_TITLE" => "Новости",
	"PAGER_SHOW_ALWAYS" => "N",
	"PAGER_TEMPLATE" => "",
	"PAGER_DESC_NUMBERING" => "N",
	"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
	"PAGER_SHOW_ALL" => "N",
	"DISPLAY_DATE" => "Y",
	"DISPLAY_NAME" => "Y",
	"DISPLAY_PICTURE" => "N",
	"DISPLAY_PREVIEW_TEXT" => "N",
	"AJAX_OPTION_ADDITIONAL" => ""
	),
	false
);?>
      </div> <!-- #left_sb_news-->
<?php 
	if (/*$USER->IsAuthorized() && ($USER->GetID() == 188 || $USER->IsAdmin())*/true)
	{
		echo "<div id=\"left_sb_news\">
			<p style=\"font-weight: bold; text-align:center;\">ПУБЛИКАЦИИ</p><br>";
		$APPLICATION->IncludeComponent("bitrix:news.list", "left_sb_news_list1", Array(
	"IBLOCK_TYPE" => "news",	// Тип информационного блока (используется только для проверки)
	"IBLOCK_ID" => "30",	// Код информационного блока
	"NEWS_COUNT" => "10",	// Количество новостей на странице
	"SORT_BY1" => "ACTIVE_FROM",	// Поле для первой сортировки новостей
	"SORT_ORDER1" => "DESC",	// Направление для первой сортировки новостей
	"SORT_BY2" => "SORT",	// Поле для второй сортировки новостей
	"SORT_ORDER2" => "ASC",	// Направление для второй сортировки новостей
	"FILTER_NAME" => "",	// Фильтр
	"FIELD_CODE" => array(	// Поля
		0 => "",
		1 => "",
	),
	"PROPERTY_CODE" => array(	// Свойства
		0 => "",
		1 => "",
	),
	"CHECK_DATES" => "Y",	// Показывать только активные на данный момент элементы
	"DETAIL_URL" => "",	// URL страницы детального просмотра (по умолчанию - из настроек инфоблока)
	"AJAX_MODE" => "N",	// Включить режим AJAX
	"AJAX_OPTION_SHADOW" => "Y",	// Включить затенение
	"AJAX_OPTION_JUMP" => "N",	// Включить прокрутку к началу компонента
	"AJAX_OPTION_STYLE" => "Y",	// Включить подгрузку стилей
	"AJAX_OPTION_HISTORY" => "N",	// Включить эмуляцию навигации браузера
	"CACHE_TYPE" => "A",	// Тип кеширования
	"CACHE_TIME" => "3600",	// Время кеширования (сек.)
	"CACHE_FILTER" => "N",	// Кэшировать при установленном фильтре
	"CACHE_GROUPS" => "Y",	// Учитывать права доступа
	"PREVIEW_TRUNCATE_LEN" => "",	// Максимальная длина анонса для вывода (только для типа текст)
	"ACTIVE_DATE_FORMAT" => "d.m.Y",	// Формат показа даты
	"DISPLAY_PANEL" => "N",	// Добавлять в админ. панель кнопки для данного компонента
	"SET_TITLE" => "Y",	// Устанавливать заголовок страницы
	"SET_STATUS_404" => "N",	// Устанавливать статус 404, если не найдены элемент или раздел
	"INCLUDE_IBLOCK_INTO_CHAIN" => "Y",	// Включать инфоблок в цепочку навигации
	"ADD_SECTIONS_CHAIN" => "Y",	// Включать раздел в цепочку навигации
	"HIDE_LINK_WHEN_NO_DETAIL" => "N",	// Скрывать ссылку, если нет детального описания
	"PARENT_SECTION" => "",	// ID раздела
	"PARENT_SECTION_CODE" => "",	// Код раздела
	"DISPLAY_TOP_PAGER" => "N",	// Выводить над списком
	"DISPLAY_BOTTOM_PAGER" => "Y",	// Выводить под списком
	"PAGER_TITLE" => "Публикации",	// Название категорий
	"PAGER_SHOW_ALWAYS" => "Y",	// Выводить всегда
	"PAGER_TEMPLATE" => "",	// Название шаблона
	"PAGER_DESC_NUMBERING" => "N",	// Использовать обратную навигацию
	"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",	// Время кеширования страниц для обратной навигации
	"PAGER_SHOW_ALL" => "Y",	// Показывать ссылку "Все"
	"DISPLAY_DATE" => "Y",	// Выводить дату элемента
	"DISPLAY_NAME" => "Y",	// Выводить название элемента
	"DISPLAY_PICTURE" => "Y",	// Выводить изображение для анонса
	"DISPLAY_PREVIEW_TEXT" => "Y",	// Выводить текст анонса
	"AJAX_OPTION_ADDITIONAL" => "",	// Дополнительный идентификатор
	),
	false
);		
		echo "</div>";
		
	}
?>

<a href="http://www.koyoradracing.com/" target="_blank" rel="nofollow">
      <div id="left_sb_link">
      </div>  
</a> 
	</div>
<!-- #left_sb-->
<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/components/itg/catalog.product/itgProduct.php");
global $DB;
//выбираем нужные нам продукты для отображения
$bestProducts = $DB->Query("SELECT `id` FROM `b_autodoc_items_m` WHERE `id` IN (7240843,7240868,7241505,5544563,5544564,5545469)");
echo "<div class='itgBest' style='position:absolute; right:0px;'>";
while ($arId=$bestProducts->Fetch())
{
		echo "<div style = 'width:200px; background-color:#000;opacity:0.9;filter:alpha(opacity=90);padding:10px;'>";
		$product = new itgProduct($arId['id']);
		$props = $product->getProductProperties();
		echo "<img style = 'float:none; width:90px; margin:0px 10px 0px 2px;' src={$props['image']}>";

			echo "<div style = 'display:block; margin:0px 0px 0px 10px;'></div>";
				echo "<span style = 'font-style:italic;font-size:12px;'>Наименование:</span>";
				echo "<div style = 'margin-left:10px;color:#EDAF1F;font-size: 12px;'><a href='http://".$_SERVER["SERVER_NAME"]."/personal/order/catalog/index.php?itg_more_info={$arId['id']}'>{$props['Caption']}</a></div>";
			echo "</div>";
	echo "<br/>";
}
echo "</div>";
?>
    <div id="content"> 