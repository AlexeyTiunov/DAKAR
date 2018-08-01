<?
global $DB;
$db_type = strtolower($DB->type);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/perfmon/classes/".$db_type."/keeper.php");
?>