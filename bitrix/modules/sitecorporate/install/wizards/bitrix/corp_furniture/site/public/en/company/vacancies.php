<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Careers");
?><p>Our Furniture Company is always looking for intelligent, imaginative and self-motivated people for all levels of our company. </p>
<?$APPLICATION->IncludeComponent("bitrix:furniture.vacancies", ".default", array(
	"IBLOCK_TYPE" => "vacancies",
	"IBLOCK_ID" => "#VACANCIES_IBLOCK_ID#",
	"AJAX_MODE" => "N",
	"AJAX_OPTION_SHADOW" => "Y",
	"AJAX_OPTION_JUMP" => "N",
	"AJAX_OPTION_STYLE" => "Y",
	"AJAX_OPTION_HISTORY" => "N",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "3600",
	"CACHE_GROUPS" => "Y",
	"AJAX_OPTION_ADDITIONAL" => ""
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>