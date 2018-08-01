<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/classes/general/xml.php');


$APPLICATION->SetAdditionalCSS('/bitrix/gadgets/bitrix/weather/styles.css');

if($arGadgetParams["CITY"]!='')
	$url = 'region='.substr($arGadgetParams["CITY"], 1).'&ts='.mktime();
else
	$url = 'ts='.mktime();

$cache = new CPageCache();
if($arGadgetParams["CACHE_TIME"]>0 && !$cache->StartDataCache($arGadgetParams["CACHE_TIME"], 'c'.$arGadgetParams["CITY"], "gdweather"))
	return;

$ob = new CHTTP();
$ob->http_timeout = 10;
$ob->Query(
	"GET",
	"export.yandex.ru",
	80,
	"/bar/reginfo.xml?".$url,
	false,
	"",
	"N"
	);


$errno = $ob->errno;
$errstr = $ob->errstr;

$res = $ob->result;

$res = str_replace("\xE2\x88\x92", "-", $res);

$xml = new CDataXML();
$xml->LoadString($APPLICATION->ConvertCharset($res, 'UTF-8', SITE_CHARSET));
$node = $xml->SelectNodes('/info/region/title');
?>
<h3><?=$node->content?></h3>

<?
$node = $xml->SelectNodes('/info/weather/day/day_part/temperature');
$t = Intval($node->content);
?>
<table width="90%">
<tr>
<td nowrap="yes" width="20%"><span class="t<?=intval($t/10)?>"><?=$node->content?></span></td>
<td width="20%"><?$node = $xml->SelectNodes('/info/weather/day/day_part/image');?><img src="<?=$node->content?>" class="gdwico"></td>
<td width="60%" nowrap>
<?$node = $xml->SelectNodes('/info/weather/day/day_part/weather_type');?>
<span class="gdweather"><?=$node->content?></span><br>
<span class="gdwinfo">
<?$node = $xml->SelectNodes('/info/weather/day/day_part/wind_direction');?>
Ветер: <?=$node->content?>, <?$node = $xml->SelectNodes('/info/weather/day/day_part/wind_speed');?><?=$node->content?> м/сек. <br>
<?$node = $xml->SelectNodes('/info/weather/day/day_part/pressure');?>
Давление: <?=$node->content?> мм.рт.ст.<br>
<?$node = $xml->SelectNodes('/info/weather/day/day_part/dampness');?>
Влажность: <?=$node->content?>%<br>

<?$node = $xml->SelectNodes('/info/weather/day/sun_rise');?>
Восход: <?=$node->content?><br>
<?$node = $xml->SelectNodes('/info/weather/day/sunset');?>
Заход: <?=$node->content?>

</span>
</td>
</tr>

<?$node = $xml->SelectNodes('/info/weather/tonight/temperature');?>
<?if($node):?>
<tr>
<td>Ночью:</td>
<td colspan="2"><?=$node->content?>°C</td>
</tr>
<?endif?>

<?$node = $xml->SelectNodes('/info/weather/tomorrow/temperature');?>
<?if($node):?>
<tr>
<td>Завтра:</td>
<td colspan="2"><?=$node->content?>°C</td>
</tr>
<?endif?>
</table>
<?if($arGadgetParams["SHOW_URL"]=="Y"):?>
<br />
<?$node = $xml->SelectNodes('/info/weather/url');?>
<a href="<?=htmlspecialchars($node->content)?>">Подробнее</a> <a href="<?=htmlspecialchars($node->content)?>"><img width="7" height="7" border="0" src="/bitrix/components/bitrix/desktop/images/arrows.gif" /></a>
<br />
<?endif?>

<?$cache->EndDataCache();?>
