<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<a name="top"></a>
<ul>
<?
foreach ($arResult['ITEMS'] as $key=>$val):
?>
	<li class="point-faq"><a href="#<?=$val["ID"]?>"><?=$val['NAME']?></a></li>
<?
endforeach;
?>
</ul>

<?
foreach ($arResult['ITEMS'] as $key=>$val):
	if ($key > 0):
?>
<div class="hr"></div>
<?
	endif;
?>
<a name="<?=$val["ID"]?>"></a>
<?
	//add edit element button
	if(isset($val['EDIT_BUTTON']))
		echo $val['EDIT_BUTTON'];
?>
<h3><?=$val['NAME']?></h3>
<p>
	<?=$val['PREVIEW_TEXT']?>
	<?=$val['DETAIL_TEXT']?>
</p><p>
	<a href="#top"><?=GetMessage("SUPPORT_FAQ_GO_UP")?></a>
</p>
<?endforeach;?>