<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Контакты");
?>
<p>Обратитесь к нашим специалистам и получите профессиональную консультацию по вопросам создания и покупки мебели (от дизайна, разработки технического задания до доставки мебели к Вам домой).</p>

<p>Вы можете обратиться к нам по телефону, по электронной почте или договориться о встрече в нашем офисе. Будем рады помочь вам и ответить на все ваши вопросы. </p>

<h2>Телефоны</h2>

<ul> 
	<li>Телефон/факс:
		<ul> 
			<li><b>(495) 212-85-06</b></li>
		</ul>
	</li>
 
	<li>Телефоны:
		<ul> 
			<li><b>(495) 212-85-07</b></li>
			<li><b>(495) 212-85-08</b></li>
		</ul>
	</li>
</ul>

<h2>Email</h2>

<ul> 
  <li><a href="mailto:info@example.ru">info@example.ru</a> &mdash; общие вопросы</li>
  <li><a href="mailto:sales@example.ru">sales@example.ru</a> &mdash; приобретение продукции</li>
  <li><a href="mailto:marketing@example.ru">marketing@example.ru</a> &mdash; маркетинг/мероприятия/PR</li>
</ul>

<h2>Офис в Москве</h2>

<?
$APPLICATION->AddHeadString('<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false&language='.LANGUAGE_ID.'"></script>');
CUtil::InitJSCore();
?>
<script type="text/javascript">
BX.ready(function () 
{
	var point = new google.maps.LatLng(55.75318806139659, 37.60178098678589);
	var options = {
		zoom: 15,
		center: point,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	}
	var map = new google.maps.Map(BX("map"), options);
	
	var marker = new google.maps.Marker({
		position: point, 
		map: map, 
		title:"Наш московский офис"
	});   
});
</script>
<div id="map" style="width:600px; height:600px"></div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>