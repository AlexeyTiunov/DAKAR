<?
global $DB;
$db_type = strtolower($DB->type);

CModule::AddAutoloadClasses(
	"perfmon",
	array(
		"CAllPerfomanceKeeper" => "classes/general/keeper.php",
		"CPerfomanceKeeper" => "classes/".$db_type."/keeper.php",
		"CAllPerfomanceHit" => "classes/general/hit.php",
		"CPerfomanceHit" => "classes/".$db_type."/hit.php",
		"CPerfomanceComponent" => "classes/general/component.php",
		"CAllPerfomanceSQL" => "classes/general/sql.php",
		"CPerfomanceSQL" => "classes/".$db_type."/sql.php",
		"CAllPerfomanceTable" => "classes/general/table.php",
		"CPerfomanceTable" => "classes/".$db_type."/table.php",
		"CPerfomanceTableList" => "classes/".$db_type."/table.php",
		"CAllPerfomanceError" => "classes/general/error.php",
		"CPerfomanceError" => "classes/".$db_type."/error.php",
		"CPerfomanceMeasure" => "classes/general/measure.php",
		"CPerfAccel" => "classes/general/measure.php",
	)
);

if(file_exists($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/php_interface/geshi/geshi.php"))
	require_once($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/php_interface/geshi/geshi.php");

function perfmon_SizeFormat($size)
{
	static $a = array("b", "Kb", "Mb", "Gb");
	$pos = 0;
	while($size >= 1024)
	{
		$size /= 1024;
		$pos++;
	}
	return round($size,2)." ".$a[$pos];
}

function perfmon_NumberFormat($num, $dec=2, $html=true)
{
	$str = number_format($num, $dec, ".", " ");
	if($html)
		return str_replace(" ", "&nbsp;", $str);
	else
		return $str;
}
?>