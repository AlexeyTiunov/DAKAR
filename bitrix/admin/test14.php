<?

error_reporting( E_ALL );

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Title");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/autodoc_globals.php");
CModule::IncludeModule('iblock');

  $arRegions = GetAllRegionsProperties();
echo "<pre>";
  print_r( $arRegions );
echo "</pre>";



echo "<br> rg 2 : ".GetPriceCoeff4Region(2);
echo "<br> rg 3 : ".GetPriceCoeff4Region(3);
/*
$arEventFields = array(
    "ID"                  => '12',
    "SUBJECT" => "This is Subject",
    "BODY_TYPE" => "text",
    "MESSAGE"             => "This is message",
    "EMAIL_TO"            => "dimtkg@gmail.com",
    "EMAIL_FROM"         => "dimtkg@gmail.com",
    "USER_ID" => "ID пользователя",
    "LOGIN" => "Логин",
    "EMAIL" => "dimtkg@gmail.com",
    "NAME" => "Имя",
    "LAST_NAME" => "Фамилия",
    "USER_IP" => "IP пользователя",
    "USER_HOST" => "Хост пользователя"
    );


			$event = new CEvent;
			echo "<br>ID:".$event->SendImmediate("NEW_USER", SITE_ID, $arEventFields);
*/
//echo "<br>ID:".CEvent::SendImmediate("SALE_NEW_ORDER", SITE_ID, $arEventFields);



$event = new CEvent;
/*$arFields = Array(
				"USER_ID"=>"1",
				"STATUS"=>"Status Line",
				"MESSAGE"=>"This is Message",
				"LOGIN"=>"- LOGIN -",
				"CHECKWORD"=>"CHKWRDFFGFG",
				"NAME"=>"NAME",
				"LAST_NAME"=>"LAST_NAME",
				"EMAIL"=>"dimtkg@gmail.com"
				);

$event->SendImmediate('USER_INFO', SITE_ID, $arFields);


$arFields = Array(
				"USER_ID"=>"1",
				"ORDER_ID"=>"OrderID12",
				"TEXT"=>"Описание заказа",
				"ORDER_DATE"=>"27.03.2011",
				"ORDER_STATUS"=>"Новый Статус заказа",
				"ORDER_DESCRIPTION"=>"Это описание статуса заказа",
				"SITE_NAME"=> "Автодок ",
				"EMAIL"=>"dimtkg@gmail.com"
				);
*/


$arFields = Array(
				"USER_ID"=>"1",
				"ORDER_ID"=>82,
				"TEXT"=>"",
				"ORDER_DATE"=>"11.02.2011",
				"ORDER_STATUS"=>"Заказ отложен",
				"ORDER_DESCRIPTION"=>"",
				"SITE_NAME"=> "Автодок ",
				"EMAIL"=>"dimtkg@gmail.com"
				);



$event->SendImmediate('ORDER_STATUS_CHANGED', 's1', $arFields);
//$event->Send('ORDER_STATUS_CHANGED', SITE_ID, $arFields,"N","26");
//CEvent::CheckEvents();

echo "<br>SITEID:".SITE_ID;
if((defined("DisableEventsCheck") && DisableEventsCheck===true) || (defined("BX_CRONTAB_SUPPORT") && BX_CRONTAB_SUPPORT===true && BX_CRONTAB!==true))
  echo "<br>Some problem";


?> Text here.... <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>