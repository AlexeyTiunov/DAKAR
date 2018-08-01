<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult)):?>
<ul id="vertical-multilevel-menu">

<?
global $itgCatalogPage;
global $DB,$USER;
if($USER->GetID() == 463 || $USER->GetID() == 1)
{
	echo "<li><a href='/search/stat/' class='root-item-selected'>СТАТИСТИКА ЗАПРОСОВ</a></li>";
	echo "<li><a href='/service/' class='root-item-selected'>СЕРВИСНОЕ</a></li>";
}
if($itgCatalogPage == 'insert')
{
	//unset($arResult);
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/components/itg/catalog/itgCatalog.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/autodoc_globals.php");
	$catalogDB = new itgCatalog("МАСЛО");
	$catalogs = $catalogDB->getTypeBrand();
	$arBrands = GetAllBrandsNameFromID();
	foreach ($catalogs as $nameCatalog=>$queryDB)
	{
		$namesOfCatalog[] = $nameCatalog;
		echo "<li><a href='/personal/order/catalog/index.php' class='root-item-selected'>$nameCatalog</a></li>";
		echo "<div id='InnerMenu'>";  #background-color:#2f2f30;padding:3px 0px 3px 0px;
		while ($brend = $queryDB->Fetch())
		{
			$arrBrend['code'] = $brend['BrandCode'];
			$arrBrend['numbers'] = $brend['countOfItems'];
			echo "<span><a id='itgBrendGroup'
							href='/personal/order/catalog/index.php?itg_brend_code=".
							$arrBrend['code']."&itg_name_catalog=".$nameCatalog."'  style = 'z-index:1000;'>  
							{$arBrands[$arrBrend['code']]}({$arrBrend['numbers']})</a></span>";
		}
		echo "</div>";
	}
}
if($itgCatalogPage == 'radiators')
{
    echo "<li><a href='http://parts.avtodok.com.ua/personal/order/radiators/index.php' class='root-item-selected'>РАДИАТОРЫ</a></li>";
    echo "<div id='InnerMenu'>";
			echo "<span><a id='itgBrendGroup'
							href='/personal/order/radiators/products.php' style = 'z-index:1000;'>  Продукция KOYORAD</a></span>";
			echo "<span><a id='itgBrendGroup'
							href='/personal/order/radiators/interactive.php' style = 'z-index:1000;'>  Каталог радиаторов</a></span>";
	echo "</div>";
}
$previousLevel = 0;
foreach($arResult as $arItem):?>

	<?if ($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel):?>
		<?=str_repeat("</ul></li>", ($previousLevel - $arItem["DEPTH_LEVEL"]));?>
	<?endif?>

	<?if ($arItem["IS_PARENT"]):?>

		<?if ($arItem["DEPTH_LEVEL"] == 1):?>
			<li><a href="<?=$arItem["LINK"]?>" class="<?if ($arItem["SELECTED"] && $itgCatalogPage != 'insert' && $itgCatalogPage != 'radiators'):?>root-item-selected<?else:?>root-item<?endif?>"><?=$arItem["TEXT"]?></a>
				<ul class="root-item">
		<?else:?>
			<li><a href="<?=$arItem["LINK"]?>" class="parent<?if ($arItem["SELECTED"]):?> item-selected<?endif?>"><?=$arItem["TEXT"]?></a>
				<ul>
		<?endif?>

	<?else:?>

		<?if ($arItem["PERMISSION"] > "D"):?>

			<?if ($arItem["DEPTH_LEVEL"] == 1):?>
				<li><a href="<?=$arItem["LINK"]?>" class="<?if ($arItem["SELECTED"] && $itgCatalogPage != 'insert' && $itgCatalogPage != 'radiators'):?>root-item-selected<?else:?>root-item<?endif?>"><?=$arItem["TEXT"]?></a></li>
			<?else:?>
				<li><a href="<?=$arItem["LINK"]?>" <?if ($arItem["SELECTED"]):?> class="item-selected"<?endif?>><?=$arItem["TEXT"]?></a></li>
			<?endif?>

		<?else:?>

			<?if ($arItem["DEPTH_LEVEL"] == 1):?>
				<li><a href="" class="<?if ($arItem["SELECTED"] && $itgCatalogPage != 'insert' && $itgCatalogPage != 'radiators'):?>root-item-selected<?else:?>root-item<?endif?>" title="<?=GetMessage("MENU_ITEM_ACCESS_DENIED")?>"><?=$arItem["TEXT"]?></a></li>
			<?else:?>
				<li><a href="" class="denied" title="<?=GetMessage("MENU_ITEM_ACCESS_DENIED")?>"><?=$arItem["TEXT"]?></a></li>
			<?endif?>

		<?endif?>

	<?endif?>

	<?if ($arItem["TEXT"] == 'КАТАЛОГИ ON-LINE' && $arItem["SELECTED"]):?>
		<div id='InnerMenu'>
			<!--<span><a id='itgBrendGroup'	href='/personal/order/radiators/interactive.php' style = 'z-index:1000;'>  KOYORAD</a></span>
			<span><a id='itgBrendGroup' href='http://catalog.avtodok.com.ua/audivw/' style = 'z-index:1000;'>  AUDI</a></span>
			<span><a id='itgBrendGroup' href='http://catalog.avtodok.com.ua/audivw/' style = 'z-index:1000;'>  SEAT</a></span>
			<span><a id='itgBrendGroup' href='http://catalog.avtodok.com.ua/audivw/' style = 'z-index:1000;'>  SKODA</a></span>
			<span><a id='itgBrendGroup' href='http://catalog.avtodok.com.ua/audivw/' style = 'z-index:1000;'>  VW</a></span>
			<span><a id='itgBrendGroup' href='http://catalog.avtodok.com.ua/bmw/' style = 'z-index:1000;'>  BMW</a></span>
			<span><a id='itgBrendGroup' href='http://catalog.avtodok.com.ua/chrysler/' style = 'z-index:1000;'>  CHRYSLER</a></span>
			<span><a id='itgBrendGroup' href='http://catalog.avtodok.com.ua/ford/' style = 'z-index:1000;'>  FORD</a></span>
			<span><a id='itgBrendGroup' href='http://catalog.avtodok.com.ua/honda/' style = 'z-index:1000;'>  HONDA</a></span>
			<span><a id='itgBrendGroup' href='http://catalog.avtodok.com.ua/hyundai/' style = 'z-index:1000;'>  HYUNDAI</a></span>
			<span><a id='itgBrendGroup' href='http://catalog.avtodok.com.ua/isuzu/' style = 'z-index:1000;'>  ISUZU</a></span>
			<span><a id='itgBrendGroup' href='http://catalog.avtodok.com.ua/kia/' style = 'z-index:1000;'>  KIA</a></span>
			<span><a id='itgBrendGroup' href='http://catalog.avtodok.com.ua/mazda/' style = 'z-index:1000;'>  MAZDA</a></span>
			<span><a id='itgBrendGroup' href='http://catalog.avtodok.com.ua/mercedes/' style = 'z-index:1000;'>  MERCEDES</a></span>
			<span><a id='itgBrendGroup' href='http://catalog.avtodok.com.ua/mitsubishi/' style = 'z-index:1000;'>  MITSUBISHI</a></span>
			<span><a id='itgBrendGroup' href='http://catalog.avtodok.com.ua/nissan/' style = 'z-index:1000;'>  NISSAN</a></span>
			<span><a id='itgBrendGroup' href='http://catalog.avtodok.com.ua/subaru/' style = 'z-index:1000;'>  SUBARU</a></span>
			<span><a id='itgBrendGroup' href='http://catalog.avtodok.com.ua/suzuki/' style = 'z-index:1000;'>  SUZUKI</a></span> -->
			<span><a id='itgBrendGroup' href='http://catalog.avtodok.com.ua/toyota/' style = 'z-index:1000;'>  TOYOTA</a></span>
           <!-- <span><a id='itgBrendGroup' href='http://catalog.avtodok.com.ua/totalcatalog/' style = 'z-index:1000;'>  ОБЩИЙ КАТАЛОГ ЗАПЧАСТЕЙ</a></span>
            <span><a id='itgBrendGroup' href='http://catalog.avtodok.com.ua/usa_oem/' style = 'z-index:1000;'>  ОРИГИНАЛ США</a></span>
            <span><a id='itgBrendGroup' href='http://catalog.avtodok.com.ua/usa_noem/' style = 'z-index:1000;'>  НЕОРИГИНАЛ США</a></span> -->
		</div>
	<?endif?>
	<?php 
	//$isCabinet = ($arItem["SELECTED"])? true:false;
	$isCabinet = false;
     $isCabinet = (stripos($_SERVER['REQUEST_URI'],'/personal/conditions/') !== false)? true :$isCabinet; 
    $isCabinet = (stripos($_SERVER['REQUEST_URI'],'/autodoc/fulldoc.php') !== false)? true :$isCabinet;
    $isCabinet = (stripos($_SERVER['REQUEST_URI'],'/autodoc/partsreadytodeliver.php') !== false)? true :$isCabinet;
    $isCabinet = (stripos($_SERVER['REQUEST_URI'],'/personal/privatbank/') !== false)? true :$isCabinet;
	$isCabinet = (stripos($_SERVER['REQUEST_URI'],'/autodoc/settlements.php') !== false)? true :$isCabinet;
	$isCabinet = (stripos($_SERVER['REQUEST_URI'],'/personal/order/balance/') !== false)? true :$isCabinet;
	$isCabinet = (stripos($_SERVER['REQUEST_URI'],'/personal/order/history_positions/') !== false)? true :$isCabinet;
	$isCabinet = (stripos($_SERVER['REQUEST_URI'],'/personal/order/') !== false 
					&& stripos($_SERVER['REQUEST_URI'],'/personal/order/catalog/') === false
					&& stripos($_SERVER['REQUEST_URI'],'/personal/order/radiators/') === false)? true :$isCabinet;
	$isCabinet = (stripos($_SERVER['REQUEST_URI'],'/personal/cabinet/') !== false)? true :$isCabinet;

	?>
	<?if ($arItem["TEXT"] == 'ЛИЧНЫЙ КАБИНЕТ' && $isCabinet):?>
		<div id='InnerMenu'>
			<span><a id='itgBrendGroup' href='/autodoc/settlements.php' style = 'z-index:1000;'>  Взаиморасчеты</a></span>
			<span><a id='itgBrendGroup' href='/personal/order/balance/' style = 'z-index:1000;'>  Баланс</a></span>
			<span><a id='itgBrendGroup' href='/personal/order/' style = 'z-index:1000;'> Заказы</a></span>
			<?#<span><a id='itgBrendGroup' href='/personal/order/history_positions/' style = 'z-index:1000;'>  История позиций</a></span>?>
<span><a id='itgBrendGroup' href='/autodoc/fulldoc.php' style = 'z-index:1000;'>  История позиций </a></span>
<span><a id='itgBrendGroup' href='/personal/cabinet/Payment' style = 'z-index:1000;'>  Уведомить об оплате заказа</a></span>
 <span><a id='itgBrendGroup' href='/personal/privatbank/' style = 'z-index:1000;'> Заплатить (Приват24,LiqPay) </a></span> 
 <span><a id='itgBrendGroup' style="color:yellow;" title="Список товара, который готовы к отправке или самовывозу." href='/autodoc/partsreadytodeliver.php' style = 'z-index:1000;'>Товар, готовый к отгрузке</a></span> 
 <!--<span><a id='itgBrendGroup' href='/personal/conditions/' style = 'z-index:1000;'> Условия работы </a></span>-->  
		</div>
	<?endif?>
	<?$previousLevel = $arItem["DEPTH_LEVEL"];?>

<?endforeach?>

<?if ($previousLevel > 1)://close last item tags?>
	<?=str_repeat("</ul></li>", ($previousLevel-1) );?>
<?endif?>

</ul>
<?endif?>