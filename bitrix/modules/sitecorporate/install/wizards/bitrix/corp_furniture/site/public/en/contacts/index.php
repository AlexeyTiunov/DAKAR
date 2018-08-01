<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Contact Us");
?>
<p>Have a question you can't find the answer to?</p>

<p>Our furniture experts are here to help.</p>

<h2>Contact us by phone</h2>
<p>Please don't hesitate to contact us on the telephone number below.</p>
<p>X.XXX.XXX.XXXX</p>

<h2>Contact us by email</h2>

<ul> 
  <li><a href="mailto:info@example.com">info@example.com</a> &mdash; For order status on an unshipped order</li>
  <li><a href="mailto:sales@example.com">sales@example.com</a> &mdash; For product questions or to place an order</li>
</ul>

<h2>Visit our showroom at Castlemilk, Glasgow</h2>

<?
$APPLICATION->AddHeadString('<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false&language='.LANGUAGE_ID.'"></script>');
CUtil::InitJSCore();
?>
<script type="text/javascript">
BX.ready(function () 
{
	var point = new google.maps.LatLng(55.807922, -4.232088);
	var options = {
		zoom: 15,
		center: point,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	}
	var map = new google.maps.Map(BX("map"), options);
	
	var marker = new google.maps.Marker({
		position: point, 
		map: map, 
		title:"Our showroom at Castlemilk, Glasgow"
	});   
});
</script>
<div id="map" style="width:600px; height:600px"></div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>