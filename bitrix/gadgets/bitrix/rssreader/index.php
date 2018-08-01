<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$rnd = rand();

$arGadgetParams["CNT"] = IntVal($arGadgetParams["CNT"]);

if($arGadgetParams["CNT"]>50)
	$arGadgetParams["CNT"] = 0;

$APPLICATION->SetAdditionalCSS('/bitrix/gadgets/bitrix/rssreader/styles.css');

include_once(dirname(__FILE__).'/include.php');

$cache = new CPageCache();
if($arGadgetParams["CACHE_TIME"]>0 && !$cache->StartDataCache($arGadgetParams["CACHE_TIME"], 'c'.$arGadgetParams["RSS_URL"].'-'.$arGadgetParams["CNT"], "gdrss"))
	return;
?>
<?
if($arGadgetParams["RSS_URL"]=="")
{
	?>
	<div class="gdrsserror">
		<?echo GetMessage("GD_RSS_READER_NEW_RSS")?>
	</div>
	<?
	$cache->EndDataCache();
	return;
}

$rss = gdGetRss($arGadgetParams["RSS_URL"]);
if($rss):
?>
<script>
function ShowHide<?=$rnd?>(id)
{
	var d = document.getElementById(id);
	if(d.style.display == 'none')
		d.style.display = 'block';
	else
		d.style.display = 'none';
}
</script>
<div class="gdrsstitle">
<?if($arGadgetParams["SHOW_URL"]=="Y"):?>
	<a href="<?=$rss->link?>"><?=$rss->title?></a>
<?else:?>
	<?=$rss->title?>
<?endif?>
</div>
<div class="gdrssitems">
<?
$cnt = 0;
foreach($rss->items as $item):
	$cnt++;
	if($arGadgetParams["CNT"]>0 && $arGadgetParams["CNT"]<$cnt)
		break;
	?>
<div class="gdrssitem">
	<div class="gdrssitemtitle">&raquo; <a href="javascript:void(0)" onclick="ShowHide<?=$rnd?>('z<?=$cnt.md5($item["TITLE"])?><?=$rnd?>')"><?=$item["TITLE"]?></a></div>
	<div class="gdrssitemdetail" id="z<?=$cnt.md5($item["TITLE"])?><?=$rnd?>" style="display:none">
		<div class="gdrssitemdate"><?=$item["PUBDATE"]?></div>
		<div class="gdrssitemdesc"><?=$item["DESCRIPTION"]?> <?if($arGadgetParams["SHOW_URL"]=="Y"):?><a href="<?=$item["LINK"]?>"><?echo GetMessage("GD_RSS_READER_RSS_MORE")?></a></div><?endif?>
	</div>
</div>
<?endforeach;?>
</div>
<?else:?>
<div class="gdrsserror">
<?echo GetMessage("GD_RSS_READER_RSS_ERROR")?>
</div>
<?endif?>
<?$cache->EndDataCache();?>
