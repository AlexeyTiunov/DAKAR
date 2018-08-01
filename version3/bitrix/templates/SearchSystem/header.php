<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<script type="text/javascript" src="/bitrix/js/itgScript/jquery.min.js"></script>
<script type="text/javascript" src="/SCF/SearchF.js"></script>   
<?$APPLICATION->ShowMeta("keywords")?>
<?$APPLICATION->ShowMeta("description")?>
<title><?$APPLICATION->ShowTitle()?></title>
<?$APPLICATION->ShowCSS();?>
<?$APPLICATION->ShowHeadStrings()?>
<?$APPLICATION->ShowHeadScripts()?>
<?
    #global $USER;
?>
<script>
$(function() 
   {
  clientwidth=($(window).height())-25;
$("body").css("height",""+clientwidth+"px");
   });
</script>
<?
 if($_SERVER['REQUEST_URI']=='/services/LoadPriceS.php')
 {      
?>
  <script>
  $("html").css("overflow","auto")
  </script>
 <?
 }
?>
</head>

<body>
<?
#echo $_SERVER['REQUEST_URI']; /services/LoadPriceS.php
 if($_SERVER['REQUEST_URI']!='/services/LoadPriceS.php') 
{
$APPLICATION->ShowPanel();
}
?>   
<div>
