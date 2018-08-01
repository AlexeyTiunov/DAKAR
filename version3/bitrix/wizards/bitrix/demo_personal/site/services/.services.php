<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arServices = Array(
	"main" => Array(
		"NAME" => GetMessage("SERVICE_MAIN_SETTINGS"),
		"STAGES" => Array(
			"site_create.php", // Create site
			"files.php", // Copy bitrix files
			"template.php", // Install template
			"theme.php", // Install theme
		),
	),

	"iblock" => Array(
		"NAME" => GetMessage("SERVICE_IBLOCK_DEMO_DATA"),
		"STAGES" => Array(
			"types.php", //IBlock types
			"user_photogallery.php",
		),
	),

	"blog" => Array(
		"NAME" => GetMessage("SERVICE_BLOG"),
		"STAGES" => Array(
			"index.php", 
		),
	),
);
?>