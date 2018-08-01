<?php
##############################################
# Bitrix Site Manager                        #
# Copyright (c) 2002-2007 Bitrix             #
# http://www.bitrixsoft.com                  #
# mailto:admin@bitrixsoft.com                #
##############################################

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/prolog.php");
define("HELP_FILE", "utilities/dump.php");

if(!$USER->CanDoOperation('edit_php'))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

if(!defined("START_EXEC_TIME"))
	define("START_EXEC_TIME", getmicrotime());

if (!defined("BX_DIR_PERMISSIONS"))
	define("BX_DIR_PERMISSIONS", 0777);

if (!defined("BX_FILE_PERMISSIONS"))
	define("BX_FILE_PERMISSIONS", 0666);

if(!defined("DUMP_BASE_TIME_CONST"))
	define("DUMP_BASE_TIME_CONST", 0.4);

if(!defined("DUMP_FILE_TIME_CONST"))
	define("DUMP_FILE_TIME_CONST", 0.8);

@set_time_limit(0);

global $DB;

IncludeModuleLangFile(__FILE__);

/*
if ($_REQUEST['getSubMenu'])
{
	echo "menuItems={'items':".CAdminPopup::PhpToJavaScript(mnu_DirList($_REQUEST['dir']))."}";
	die();
}
*/

$bUseCompression = True;
if(!extension_loaded('zlib') || !function_exists("gzcompress"))
	$bUseCompression = False;

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/tar_gz.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/fileman/fileman.php");

if (function_exists('mb_internal_encoding'))
	mb_internal_encoding('ISO-8859-1');

$com_marker = "--";
$filr_id = "";
$sTableID = "tbl_dump";

$oSort = new CAdminSorting($sTableID, "timestamp", "desc");
$lAdmin = new CAdminList($sTableID, $oSort);

$arFilter = array(
	"EXTENSIONS"=>"sql,tar,gz"
);

$arFilterFields = array();

$lAdmin->InitFilter($arFilterFields);

$lAdmin->BeginPrologContent();

if(strlen($strMess)>0 && strlen($strSucc)>0)
{
	$mes = Array("MESSAGE"=>$strSucc, "DETAILS"=>$strMess, "TYPE"=>"OK", "HTML"=>true);
	$m = new CAdminMessage($mes);
	echo $m->Show();
}

$site = CSite::GetSiteByFullPath($_SERVER['DOCUMENT_ROOT']);
$path = BX_PERSONAL_ROOT."/backup";

if (($arID = $lAdmin->GroupAction()))
{
	if ($_REQUEST['action_target']=='selected')
	{
		$arID = array();

		if (!CSite::IsDistinctDocRoots() || strlen($site) > 0 || strlen($path) > 0)
		{
			$DOC_ROOT = CSite::GetSiteDocRoot($site);

			$path = Rel2Abs("/", rtrim($path, "/"));
			$arParsedPath = ParsePath(Array($site, $path));

			$abs_path = $DOC_ROOT.$path;

			GetDirList(Array($site, $path), $arDirs, $arFiles, $arFilter, Array($by => $order), "F");

			foreach ($arFiles as $File)
				$arID[] = $File["NAME"];
		}
	}

	foreach ($arID as $ID)
	{
		if (strlen($ID) <= 0)
			continue;

		$CurPerm = $APPLICATION->GetFileAccessPermission(array($site, $path."/".$ID));
		if ($CurPerm < "W")
			continue;

		switch ($_REQUEST['action'])
		{
			case "export":
				?>
				<script language="JavaScript">
					exportData('<?=$ID?>');
				</script>
				<?
			break;
			case "delete":
				@set_time_limit(0);

				$strWarning_tmp = CFileMan::DeleteEx(Array($site, CFileMan::NormalizePath($path."/".$ID)));

				if (strlen($strWarning_tmp) > 0)
					$lAdmin->AddGroupError($strWarning_tmp, $ID);
			break;
		}
	}
}

InitSorting();

$arDirs = array();
$arFiles = array();

GetDirList(Array($site, $path), $arDir, $arFiles, $arFilter, Array($by=>$order), "F");
$rsDirContent = new CDBResult;
$rsDirContent->InitFromArray($arFiles);
$rsDirContent->sSessInitAdd = $path;
$rsDirContent = new CAdminResult($rsDirContent, $sTableID);
$rsDirContent->NavStart(20);

// установка строки навигации
$lAdmin->NavText($rsDirContent->GetNavPrint(GetMessage("MAIN_DUMP_FILE_PAGES")));
$lAdmin->AddHeaders(array(
		array("id"=>"NAME", "content"=>GetMessage("MAIN_DUMP_FILE_NAME"), "sort"=>"name", "default"=>true),
		array("id"=>"SIZE","content"=>GetMessage("MAIN_DUMP_FILE_SIZE_FIELD"), "sort"=>"size", "default"=>true),
		array("id"=>"DATE", "content"=>GetMessage('MAIN_DUMP_FILE_TIMESTAMP'), "sort"=>"timestamp", "default"=>true)
));

while($Elem = $rsDirContent->NavNext(true, "f_"))
{
	$fname = $documentRoot.$path."/".$Elem["NAME"];

	$showFieldIcon = "";
	$showFieldText = "";
	$curFileType = CFileMan::GetFileTypeEx($Elem["NAME"]);
	$showFieldIcon = "<IMG SRC=\"/bitrix/images/fileman/types/".$curFileType.".gif\" WIDTH=\"16\" HEIGHT=\"16\" BORDER=0 ALT=\"\">";
	$showFieldText = $f_NAME;

	$showField = "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tr><td align=\"left\">".$showFieldIcon."</td><td align=\"left\" nowrap>&nbsp;".$showFieldText."</td></tr></table>";

	$row =& $lAdmin->AddRow($f_NAME, $Elem);

	$row->AddField("NAME", $showField, $editField);
	$row->AddField("SIZE", round(doubleval(sprintf("%u", $f_SIZE))/1048576, 2));
	$row->AddField("DATE", $f_DATE);

	$arActions = Array();

	if ($Elem["PERMISSION"] > "R")
	{
		$arActions[] = array(
			"ICON" => "export",
			"DEFAULT" => true,
			"TEXT" => GetMessage("MAIN_DUMP_ACTION_DOWNLOAD"),
			"ACTION" => "exportData('".$f_NAME."')"
		);
		$arActions[] = array(
			"ICON" => "restore",
			"TEXT" => GetMessage("MAIN_DUMP_RESTORE"),
			"ACTION" => "if(confirm('".GetMessage("MAIN_RIGHT_CONFIRM_EXECUTE")."')) restoreDump('".$f_NAME."')"
		);

		$arActions[] = array("SEPARATOR" => true);
		$arActions[] = array(
			"ICON" => "delete",
			"TEXT" => GetMessage("MAIN_DUMP_DELETE"),
			"ACTION" => "if(confirm('".GetMessage('MAIN_DUMP_ALERT_DELETE')."')) ".$lAdmin->ActionDoGroup($f_NAME, "delete", $addUrl."&site=".Urlencode($site)."&path=".UrlEncode($path)."&show_perms_for=".IntVal($show_perms_for))
		);
	}
	$row->AddActions($arActions);
}

// "подвал" списка
$lAdmin->AddFooter(
	array(
		array(
			"title" => GetMessage("MAIN_ADMIN_LIST_SELECTED"),
			"value" => $rsDirContent->SelectedRowsCount()
		),
		array(
			"counter" => true,
			"title" => GetMessage("MAIN_ADMIN_LIST_CHECKED"),
			"value" => "0"
		),
	)
);

$lAdmin->AddGroupActionTable(
	array(
		"delete" => GetMessage("MAIN_ADMIN_LIST_DELETE")
	)
);

$lAdmin->CheckListMode();

$ABS_PATH = $_SERVER["DOCUMENT_ROOT"];

function createTable($table_name, $drop = true)
{
	global $DB, $com_marker;
	$sql = "SHOW CREATE TABLE `".$table_name."`";

	$res = $DB->Query($sql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
	$row = $res->Fetch();

	$com = "\n\n";
	$com .= $com_marker. " --------------------------------------------------------" ."\n";
	$com .= $com_marker. " \n";
	$com .= $com_marker. " Table structure for table `".$table_name."`\n";
	$com .= $com_marker. " \n";
	$com .= "\n";

	return $com."\n\n\n".($drop ? "DROP TABLE IF EXISTS `".$table_name."`;\n".$row["Create Table"] : str_replace('CREATE TABLE','CREATE TABLE IF NOT EXISTS',$row["Create Table"])).';';
}

function getData($table, $file, $row_count, $max_execution_time, $stepped, $last_row = 0, $mem)
{
	global $DB, $com_marker;
	$dump = "";
	$step = "";

	$com = "\n" .$com_marker. " \n";
	$com .= $com_marker. " Dumping data for table  `".$table."`\n";
	$com .= $com_marker. " \n";
	$com .= "\n";

	fwrite($file, $com."\n");

	$sql = "SHOW COLUMNS FROM `$table`";
	$res = $DB->Query($sql);
	$num = Array();
	$i = 0;

	//Определяем тип поля
	while($row = $res->Fetch())
	{
		if(preg_match("/^(\w*int|year|float|double|decimal)/", $row["Type"]))
			$meta[$i] = 0;
		elseif(preg_match("/^(\w*binary)/", $row["Type"]))
		{
			$meta[$i] = 1;
		} else
			$meta[$i] = 2;
		$i++;
	}

	$sql = "SHOW TABLE STATUS LIKE '$table'";
	$res = $DB->Query($sql);
	$tbl_info = $res->Fetch();
	$step = 1+round($mem * 1048576*0.5 / ($tbl_info["Avg_row_length"] + 1));

	while(($last_row <= ($row_count-1)) && (((getmicrotime() - START_EXEC_TIME) < round($max_execution_time * DUMP_BASE_TIME_CONST))|| !$stepped))
	{
		$sql = "SELECT * FROM `$table` LIMIT $last_row, $step";
		$res = $DB->Query($sql);

		while($row = $res->Fetch())
		{
			$i = 0;
			foreach($row as $key => $val)
			{
				if (!isset($val) || is_null($val))
						$row[$key] = 'NULL';
				else
					switch($meta[$i])
					{
						case 0:
							$row[$key] = $val;
						break;
						case 1:
							if (empty($val) && $val != '0')
								$row[$key] = '\'\'';
							else
								$row[$key] = '0x' . bin2hex($val);
						break;
						case 2:
							$row[$key] = "'".$DB->ForSql($val)."'";
						break;
					}
				$i++;
			}
			fwrite($file, "INSERT INTO `".$table."` VALUES (".implode(",", $row).");\n");
		}
		$last_row += $step;
	}

	if($last_row >= ($row_count-1))
		return -1;
	else
		return $last_row;
}

function getArcName()
{
	global $bUseCompression;

	if(!file_exists($_SERVER['DOCUMENT_ROOT'].BX_PERSONAL_ROOT."/backup"))
		mkdir($_SERVER['DOCUMENT_ROOT'].BX_PERSONAL_ROOT."/backup", BX_DIR_PERMISSIONS);

	if(!file_exists($_SERVER['DOCUMENT_ROOT'].BX_PERSONAL_ROOT."/backup/index.php"))
	{
		$f = fopen($_SERVER['DOCUMENT_ROOT'].BX_PERSONAL_ROOT."/backup/index.php","w");
		fwrite($f,"<head><meta http-equiv=\"REFRESH\" content=\"0;URL=/bitrix/admin/index.php\"></head>");
		fclose($f);
	}

	if(is_dir($_SERVER['DOCUMENT_ROOT'].BX_PERSONAL_ROOT."/backup") && (is_writable($_SERVER['DOCUMENT_ROOT'].BX_PERSONAL_ROOT."/backup")))
	{
		$arc_name = $_SERVER['DOCUMENT_ROOT'].BX_PERSONAL_ROOT."/backup/".date("YmdHi_");
		$arc_name .= substr(md5(uniqid(rand(), true)), 0, 8);

		$ret["arc_name"] = $arc_name.".tar";
		if ($bUseCompression)
			$ret["arc_name"].=".gz";

		$ret["base_dump"] = $arc_name.".sql";
	}


	return $ret;
}

function ignorePath($path, $d_public, $d_kernel, $base)
{
	$ignore_path = array(
		BX_PERSONAL_ROOT."/cache",
		BX_PERSONAL_ROOT."/cache_image",
		BX_PERSONAL_ROOT."/managed_cache",
		BX_PERSONAL_ROOT."/stack_cache",
		BX_ROOT."/updates",
		BX_PERSONAL_ROOT."/backup",
		BX_ROOT."/backup",
	);

	$path_kernel = array(
		"/bitrix/admin",
		"/bitrix/components/bitrix",
		"/bitrix/help",
		"/bitrix/images",
		"/bitrix/js",
		"/bitrix/modules",
		"/bitrix/sounds",
		"/bitrix/themes/.default",
		"/bitrix/tools",
		"/bitrix/wizards/bitrix"
	);

	if($base && (substr($path, strlen($_SERVER["DOCUMENT_ROOT"]), strlen(BX_PERSONAL_ROOT."/backup")) == BX_PERSONAL_ROOT."/backup"))
		return false;

	foreach($ignore_path as $value)
		if($_SERVER["DOCUMENT_ROOT"].$value == $path)
			return true;

	// If we make kernel only dump - archive only $path_kernel, if public only - anything other
	if ($d_public xor $d_kernel)
	{
		foreach($path_kernel as $value)
		{
			if($_SERVER["DOCUMENT_ROOT"].$value == substr($path, 0, strlen($_SERVER["DOCUMENT_ROOT"].$value)))
				return $d_public; // Got it
			elseif ($d_kernel && $path == substr($_SERVER['DOCUMENT_ROOT'].$value, 0, strlen($path)))
				return false; // In case of /bitrix dir need to look deeper
		}
		return $d_kernel; // Making kernel dump and path not found in $path_kernel - return true = ignore
	}
}

function BaseDump($arc_name="", $tbl_num, $start_row, $max_execution_time = 0, $stepped, $stat, $index)
{
	global $DB;

	$ret = array();
	$last_row = $start_row;
	#$mem = (strlen(ini_get("memory_limit")) > 0) ? intVal(ini_get("memory_limit")) : 1;
	$mem = 32; // Minimum required value

	$sql = "SHOW TABLES;";
	$res = $DB->Query($sql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
	$ptab = Array();
	while($row = $res->Fetch())
	{
		$ar = each($row);
		$table = $ar[1];
		$ptab[] = $table;
	}

	$f = fopen($arc_name,"a");
	$i = $tbl_num;

	$dump = "";

	while($i <= (count($ptab) - 1) && (((getmicrotime() - START_EXEC_TIME) < round($max_execution_time * DUMP_BASE_TIME_CONST)) || !$stepped))
	{
		if (strlen($ptab[$i]))
		{
			if($last_row == -1)
			{
				$table = $ptab[$i];
				$drop = $stat || !preg_match("#^b_stat#i",$table); // если не переносим статистику, то только создаём структуру таблицы
				$dump = createTable($ptab[$i], $drop);
				fwrite($f, $dump."\n");
				$next = false;
				$ret["num"] = $i;
				$ret["st_row"] = 0;
				$last_row = 0;
			}

			$res = $DB->Query("SELECT count(*) as count FROM `$ptab[$i]`");
			$row_count = $res->Fetch();

			if($row_count["count"] > 0)
				if(!$stat && (substr($ptab[$i],0,6) == "b_stat") || !$index && (substr($ptab[$i],0,16) == "b_search_content"))
					$row_next = -1;
				else
					$row_next = getData($ptab[$i], $f, $row_count["count"], $max_execution_time, $stepped,  $last_row, $mem);
			else
				$row_next = -1;

			if($row_next == -1)
			{
				$ret["num"] = ++$i;
				$ret["st_row"] = -1;
				$last_row = -1;
			}
			else
			{
				$last_row = $row_next;
				$ret["num"] = $i;
				$ret["st_row"] = $last_row;
			}
		}
	}

	fclose($f);

	if(!($i <= (count($ptab) - 1)))
		$ret["end"] = true;

	return $ret;
}

function getTableSize($prefix)
{
	global $DB, $DBType;
	if (strtoupper($DBType)!='MYSQL')
		return 0;
	$size = 0;

	$sql = "SHOW TABLE STATUS LIKE '".$DB->ForSql($prefix)."%'";
	$res = $DB->Query($sql);

	while($row = $res->Fetch())
		$size += $row["Data_length"];

	return round($size/(1048576), 2);
}

function getPath($str_path, $level)
{
	$tmp_path = explode("/", $str_path);
	$res["path"] = "";
	$res["file"] = isset($tmp_path[1]) ? $tmp_path[1] : "";

	if($level > 0)
	{
		$i = 1;
		while($i <= $level)
		{
			$res["path"] .= "/".$res["file"];
			$res["file"] = isset($tmp_path[$i+1]) ? $tmp_path[$i+1] : "";
			$i++;
		}
	}

	return $res;
}

function addBase($base_name, $arc_name="", $max_exec_time, $pos, $stepped)
{
	global $ABS_PATH, $bUseCompression;
	$bNewArchive = true;

	$oArc = new CArchiver($arc_name, $bUseCompression, START_EXEC_TIME, round($max_exec_time * DUMP_FILE_TIME_CONST), $pos, $stepped);
	$oArc->_arErrors = array();

	if(file_exists($oArc->_strArchiveName) && is_file($oArc->_strArchiveName))
	{
		$bNewArchive = false;
	}
	if ($bNewArchive)
	{
		if(!$oArc->_openWrite())
			return false;
	}
	else
	{
		if (!$oArc->_openAppendFast())
			return false;
	}

	if($oArc != null)
	{

		if($pos == 0)
		{
			$res = $oArc->addFile($ABS_PATH.BX_PERSONAL_ROOT."/backup/", false, $ABS_PATH);
			$res = $oArc->addFile($ABS_PATH.BX_PERSONAL_ROOT."/backup/index.php", false, $ABS_PATH);
		}

		$res = $oArc->addFile($base_name, false, $ABS_PATH);

		$after_file = str_replace('.sql','_after_connect.sql',$base_name);
		if (file_exists($after_file))
			$res = $oArc->addFile($after_file, false, $ABS_PATH);

		$fret["pos"] = $oArc->getFilePos();

#		if($fret["pos"] == 0)
#			$oArc->_writeFooter();

		$oArc->_close();
		$fret["end"] = true;
	}
	return $fret;
}

function FileDump($str_paths, $arc_name="", $max_execution_time = 0, $max_file_size, $oArchiver=null, $level, $stepped, $d_public, $d_kernel, $base, $dump_file, $pos)
{
	global $ABS_PATH, $bUseCompression;

	$end_time = false;
	$tres = false;
	$bNewArchive = true;
	$fest = true;
	$oArc = null;
	$isSkip = false;
	$isSkip_big = false;
	$tres = "";

	$fres["cnt"] = 0;
	$fres["size"] = 0;

	if(!$oArchiver && $arc_name != "")
	{
		$oArc = new CArchiver($arc_name, $bUseCompression, START_EXEC_TIME, round($max_execution_time * DUMP_FILE_TIME_CONST), $pos, $stepped);
		$oArc->_arErrors = array();

		if(file_exists($oArc->_strArchiveName) && is_file($oArc->_strArchiveName))
		{
			$bNewArchive = false;
		}
		if ($bNewArchive)
		{
			if(!$oArc->_openWrite())
				return false;
		}
		else
		{
			if (!$oArc->_openAppendFast())
				return false;
		}
	}
	else
	{
		$oArc = $oArchiver;
		$fest = false;
	}

	do
	{
		$paths = getPath($str_paths, $level);
		$restart = false;

		if($oArc != null)
		{
			$abs_path = $ABS_PATH.$paths["path"];
			$handle = @opendir($abs_path);
			$fres["end"] = false;
			$fres["level"] = $level;
			$fres["fpath"] = (strlen($paths["path"]."/".$path["file"]) > 1) ? $paths["path"]."/".$path["file"] : "";

			if((getmicrotime() - START_EXEC_TIME) > round($max_execution_time * DUMP_FILE_TIME_CONST))
				$end_time = true;

			while ((false !== ($file = @readdir($handle)))  && (((getmicrotime() - START_EXEC_TIME) < round($max_execution_time * DUMP_FILE_TIME_CONST)) || !$stepped ))
			{
				if($file == "." || $file == "..")
					continue;

				if ($_REQUEST['skip_mask'] && SkipMask($abs_path."/".$file))
				{
//					die("=".$abs_path."/".$file."=");
					continue;
				}

 				if(!$isSkip && (strlen($paths["file"])) > 0 && ($file != $paths["file"]))
					continue;
				else
				{
					$isSkip = true;
				}

				if((strlen($paths["file"]) > 0) && ($file == $paths["file"]) && ($pos == 0))
					continue;

				if($_REQUEST['skip_symlinks']=='Y' && is_dir($abs_path."/".$file) && is_link($abs_path."/".$file))
					continue;

				if(is_dir($abs_path."/".$file) && ignorePath($abs_path."/".$file, $d_public, $d_kernel, $base))
					continue;
				
				if($abs_path == $_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/backup" && $abs_path."/".$file != $dump_file && $abs_path."/".$file != str_replace('.sql','_after_connect.sql',$dump_file) && $abs_path."/".$file != $abs_path."/index.php")
					continue;

				if((!$d_public && $d_kernel) && ((is_file($abs_path."/".$file) && ($ABS_PATH."/".$file == $abs_path."/".$file)) || ($abs_path == $_SERVER["DOCUMENT_ROOT"]."/bitrix" && is_file($abs_path."/".$file) && $file != "")))
					continue;

				if(is_dir($abs_path."/".$file))
				{
					$paths["file"] = $file;
					$t_path = $paths["path"];
					$paths["path"] = $paths["path"]."/".$file;

					$xres = $oArc->addFile($abs_path."/".$file, false, $ABS_PATH);
					if($xres)
						$tres = $xres;

					$res = FileDump($paths["path"], "", $max_execution_time, $max_file_size, $oArc, $level+1, $stepped, $d_public, $d_kernel, $base, $dump_file, 0);

					if($res["break"])
					{
						$fres["level"] = $res["level"];
						$fres["fpath"] = $res["fpath"];
						$fres["pos"] = $res["pos"];
						$fres["break"] = true;
						$level = $res["level"];
						$tres = $res["tres"];
						$fres["cnt"] += $res["cnt"];
						$fres["size"] += $res["size"];
						break;
					}
					else
					{
						if($res["end"])
						{
							$paths["file"] = $file;
							$paths["path"] = $t_path;

							$fres["level"] = $level;
							$fres["fpath"] = $paths["path"];
							$fres["fpath"] .= (strlen($paths["file"]) > 0) ? "/".$paths["file"] : "";

							$tres = $res["tres"];
							$fres["cnt"] += $res["cnt"];
							$fres["size"] += $res["size"];
						}
						else
						{
							$fres["level"] = $res["level"];
							$fres["fpath"] = $res["fpath"];
							$fres["break"] = true;
							$level = $res["level"];
							$fres["pos"] = $res["pos"];

							$tres = $res["tres"];
							$fres["cnt"] += $res["cnt"];
							$fres["size"] += $res["size"];
							break;
						}
					}
				}
				else // is file
				{
					if($abs_path."/".$file == $dump_file || $max_file_size == 0 || filesize($abs_path."/".$file) < $max_file_size*1024)
					{
						$xres = $oArc->addFile($abs_path."/".$file, false, $ABS_PATH);

						if($xres && ($oArc->getFilePos() == 0))
						{
							$fres["cnt"]++;
							$fres["size"] += filesize($abs_path."/".$file);
							$tres = $xres;
						}

						$fres["pos"] = $oArc->getFilePos();
						$pos = $fres["pos"];
						$paths["path"] = $paths["path"];
						$paths["file"] = $file;

						$fres["fpath"] = $paths["path"]."/".$paths["file"];
						$fres["level"] = $level;
						$fres["tres"] = $tres;
					}
				}
			}

			if(!$file && (($fres["pos"] == 0) || !$stepped))
				$fres["end"] = true;

			if(!$file && $fres["end"] && $level == 0)
			{
				$level = -1;
				$fres["level"] = $level;
			}

			closedir($handle);
		}

		if(((getmicrotime() - START_EXEC_TIME) < round($max_execution_time * DUMP_FILE_TIME_CONST)) && $stepped && $fres["end"] && $fest && ($fres["level"] > 0) && (strlen($fres["fpath"]) > 1))
		{
			$isSkip = false;
			unset($file);
			unset($handle);
			unset($paths);

			$str_paths = $fres["fpath"];
			$level = $fres["level"] - 1;
			$restart = true;
		}

	} while ($restart);

	if($fest)
	{

#		if(($fres["level"] == -1) && ($fres["end"]))
#			$oArc->_writeFooter();

		$oArc->_close();

		if($end_time)
		{
			$level = -1;
			$fres["level"] = $level;
			$fres["end"] = true;
		}
	}

	return $fres;
}

if($dumping == "Y" && check_bitrix_sessid())
{
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_js.php");

	if(strlen($Next) <= 0)
	{
		$max_file_size = intVal($max_file_size);
		$max_execution_time = intVal($max_execution_time);
		if (isset($_REQUEST['dump_max_exec_time_sleep']))
			COption::SetOptionString("main", "dump_max_exec_time_sleep", intval($_REQUEST['dump_max_exec_time_sleep']));

		COption::SetOptionString("main", "dump_max_file_size", $max_file_size);
		COption::SetOptionString("main", "dump_max_exec_time", $max_execution_time);
		COption::SetOptionString("main", "dump_file_stepped", $stepped);
		COption::SetOptionString("main", "dump_file_public", $d_pub);
		COption::SetOptionString("main", "dump_file_kernel", $d_ker);
		COption::SetOptionString("main", "dump_base_true", $dump_base);
		COption::SetOptionString("main", "dump_base_stat", $stat);
		COption::SetOptionString("main", "dump_base_index", $index);
		COption::SetOptionString("main", "skip_symlinks", $skip_symlinks);
		COption::SetOptionString("main", "skip_mask", $skip_mask);

		$skip_mask_array = array();
		if ($skip_mask && is_array($_REQUEST['arMask']))
		{
			$arMask = array_unique($_REQUEST['arMask']);
			foreach($arMask as $mask)
				if (trim($mask))
				{
					$mask = str_replace('\\','/',trim($mask));
					if (strpos($mask,'/')>0) // incorrect mask: no head slash + slash inside
						continue;
					$skip_mask_array[] = $mask;
				}
			COption::SetOptionString("main", "skip_mask_array", serialize($skip_mask_array));
		}

		$name = getArcName();

		$NS=Array();
		$NS["fpath"] = "";
		$NS["arc_name"] = $name["arc_name"];
		$NS["level"] = 0;
		$NS["ptab"] = null;
		$NS["num"] = 0;
		$NS["st_row"] = -1;
		$NS["b_end"] = false;
		$NS["backup_name"] = $name["base_dump"];
		$NS["add"] = false;
		$NS["del"] = false;
		$NS["end"] = false;
		$NS["pos"] = 0;
	}
	else
	{
//		$NS=unserialize($NS);
		$NS = $_SESSION['BX_DUMP_NS'];		
		$res = unserialize(COption::GetOptionString("main","skip_mask_array"));
		$skip_mask_array = is_array($res)?$res:array();
	}
	$res = array();
	$after_file = str_replace('.sql','_after_connect.sql',$NS['backup_name']);

	if(!$NS["end"])
	{
		if(!$NS["b_end"] && ($dump_base == "Y"))
		{
			$bres = BaseDump($NS["backup_name"], $NS["num"], $NS["st_row"], $max_execution_time, ($stepped == "Y"), ($stat == "Y"), ($index == "Y"));
			$NS["ptab"] = $bres["ptab"];
			$NS["num"] = $bres["num"];
			$NS["st_row"] = $bres["st_row"];
			$NS["b_end"] = $bres["end"];
			$res["end"] = false;

			if ($bres['end'])
			{
				$f = mysql_fetch_array(mysql_query('SHOW VARIABLES LIKE "character_set_results"'));
				if ($charset = $f[1])
				{
					$rs = fopen($after_file,'wb');
					fwrite($rs,"SET NAMES '$charset'");
					fclose($rs);
				}
			}
		}
		elseif($NS["b_end"] && !$NS["add"] && !($d_pub == "Y" || $d_ker == "Y"))
		{
			$fres = addBase($NS["backup_name"], $NS["arc_name"], $max_execution_time, $NS["pos"], ($stepped == "Y"));
			if($fres["end"] && $fres["pos"] == 0)
				$NS["add"] = $fres["end"];
			else
				$res["pos"] = $fres["pos"];
		}
		elseif($d_pub == "Y" || $d_ker == "Y")
			$res = FileDump($NS["fpath"], $NS["arc_name"], $max_execution_time, $max_file_size, null, $NS["level"], ($stepped == "Y"), ($d_pub == "Y"), ($d_ker == "Y"), $dump_base, $NS["backup_name"], $NS["pos"]);
		else
		{
			$res["end"] = true;
			$res["level"] = -1;
		}
	}

	if($NS["b_end"] && ($NS["add"] || $res["end"] ) && !$NS["del"] && ($dump_base == "Y"))
		$NS["del"] = unlink($NS["backup_name"]) && (!file_exists($after_file) || unlink($after_file));

	if($res["end"] && ($res["level"] != -1))
	{
		$res["end"] = false;
		if($res["level"] > 0)
			$res["level"]--;
	}

	$res["arc_name"] = $NS["arc_name"];
	$res["backup_name"] = $NS["backup_name"];
	$res["cnt"] += $NS["cnt"];
	$res["size"] += $NS["size"];

	$res["b_end"] = $NS["b_end"];
	$res["ptab"] = $NS["ptab"];
	$res["num"] = $NS["num"];
	$res["st_row"] = $NS["st_row"];
	$res["add"] = $NS["add"];
	$res["del"] = $NS["del"];

	$status_msg = "";

	if(!$res["end"])
	{

		if($dump_base == "Y")
			$status_msg = GetMessage("MAIN_DUMP_TABLE_FINISH")." <b>".($res["num"])."</b>";

		if(($d_pub == "Y" || $d_ker == "Y") && ($dump_base != "Y" || $res["b_end"]))
		{
			if(strlen($status_msg) > 0)
				$status_msg .= "<br>";

			$status_msg .= GetMessage("MAIN_DUMP_FILE_CNT")." <b>".$res["cnt"]."</b><br>".
			GetMessage("MAIN_DUMP_FILE_SIZE")." <b>".round($res["size"]/1024)."</b> ".GetMessage('MAIN_DUMP_FILE_MAX_SIZE_kb');
		}

		CAdminMessage::ShowMessage(array(
			"MESSAGE" => GetMessage("MAIN_DUMP_SITE_PROC"),
			"DETAILS" =>  $status_msg,
			"TYPE" => "OK",
			"HTML" => true));
			
		$_SESSION['BX_DUMP_NS'] = $res;
?>
		<input type="hidden" id="NS" name="NS" value="<?=md5(serialize($res))?>">
<?	}
	else
	{

		if($dump_base == "Y")
			$status_msg = GetMessage("MAIN_DUMP_TABLE_FINISH")." <b>".$res["num"]."</b>";

		if($d_pub == "Y" || $d_ker == "Y")
		{
			if(strlen($status_msg) > 0)
				$status_msg .= "<br>";

			$status_msg .= GetMessage("MAIN_DUMP_FILE_CNT")." <b>".$res["cnt"]."</b><br>".
			GetMessage("MAIN_DUMP_FILE_SIZE")." <b>".round($res["size"]/1024)."</b> ".GetMessage('MAIN_DUMP_FILE_MAX_SIZE_kb')."<br>".
			GetMessage("MAIN_DUMP_ARC_SIZE")." <b>".round(filesize($NS["arc_name"])/1024)."</b> ".GetMessage('MAIN_DUMP_FILE_MAX_SIZE_kb');
		}

		CAdminMessage::ShowMessage(array(
			"MESSAGE" => GetMessage("MAIN_DUMP_FILE_FINISH"),
			"DETAILS" => $status_msg ,
			"TYPE" => "OK",
			"HTML" => true));

?>
		<input type="hidden" id="NSTOP" name="NSTOP" value="Y">
		<?echo bitrix_sessid_post()?>
<script>
	<?=$lAdmin->ActionPost(htmlspecialchars($APPLICATION->GetCurPageParam("mode=frame", Array("mode", "PAGEN_1"))))?>
</script>

	<?
	}
	require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin_js.php");
}
else
{
	$APPLICATION->SetTitle(GetMessage("MAIN_DUMP_PAGE_TITLE"));

	require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");

	$aTabs = array(array("DIV"=>"tab1", "TAB"=>GetMessage("MAIN_DUMP_TAB"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("MAIN_DUMP_TAB_TITLE")),);
	$editTab = new CAdminTabControl("editTab", $aTabs);

	if (defined('ENCODE') && ENCODE=='Y')
	{
		preg_match("/^([0-9])/",PHP_VERSION,$regs);
		$ver = $regs[1];
		CAdminMessage::ShowMessage(array(
				"MESSAGE" => GetMessage('MAIN_DUMP_ENCODE'),
				"DETAILS" => str_replace("#VER#",$ver,GetMessage('MAIN_DUMP_ENCODE_DETAILS')),
				"TYPE" => "ERROR",
				"HTML" => true
			)
		);
	}
	?>

<div id="dump_result_div"></div>
<? 
echo BeginNote();
echo GetMessage("MAIN_DUMP_HEADER_MSG");
echo EndNote();

/*
$u = new CAdminPopup(
        "mnu_FILES",
        "mnu_FILES",
        mnu_DirList(),
        array("zIndex" => 2000)
);
$u->Show();
*/

CAdminFileDialog::ShowScript(Array
    (
	"event" => "__bx_select_dir",
	"arResultDest" => Array("FUNCTION_NAME" => "mnu_SelectValue"),
	"arPath" => Array('PATH'=>"/"),
	"select" => 'D',
	"operation" => 'O',
	"showUploadTab" => false,
	"showAddToMenuTab" => false,
	"allowAllFiles" => true,
	"SaveConfig" => true 
    )
);		
?>
<script language="JavaScript">
var numRows=0;
function AddTableRow()
{
	oTable = document.getElementById('skip_mask_table');
	numRows = oTable.rows.length;
	oRow = oTable.insertRow(-1);
	oCell = oRow.insertCell(0);
	oCell.innerHTML = '<input name="arMask[]" id="mnu_FILES_' + numRows  +'" size=30><input type="button" id="mnu_FILES_btn_' + numRows  + '" value="..." onclick="showMenu(this, '+ numRows  +')">';
}

var currentID;
/*
function showMenu(div, id)
{
	var pos = jsUtils.GetRealPos(div);
	currentID = id;
	mnu_FILES.PopupShow(pos)
}

function mnu_SelectValue(path)
{
	document.getElementById('mnu_FILES_' + currentID).value = path;
}
*/

function showMenu(div, id)
{
	currentID = id;
	__bx_select_dir();
}

function mnu_SelectValue(filename, path, site, title, menu)
{
	document.getElementById('mnu_FILES_' + currentID).value = filename;
}

function mnu_ShowSubItems(menu, dir)
{
	CHttpRequest.Action = function (result)
	{
		var menuItems;
		eval(result); // menuItems={'items':[]}

		if(menu)
		{
			menu.PopupHide();
			menu.SetItems(menuItems['items']);
			menu.BuildItems();
			menu.parentMenu.ShowSubmenu(menu.parentItem);
		}
	}
	CHttpRequest.Send('/bitrix/admin/dump.php?getSubMenu=Y&dir=' + dir)
}

function CheckActiveStart()
{
	var noFiles;
	noFiles = !document.fd1.dump_public.checked && !document.fd1.dump_kernel.checked;

	document.fd1.max_file_size.disabled = noFiles;
	document.fd1.skip_symlinks.disabled = noFiles;
	document.fd1.skip_mask.disabled = noFiles;

	var noMask;
	noMask = noFiles || !document.getElementById('skip_mask').checked;

	oTable = document.getElementById('skip_mask_table');
	numRows = oTable.rows.length;
	for(i=0;i<numRows;i++)
	{
		document.getElementById('mnu_FILES_'+i).disabled = noMask;
		document.getElementById('mnu_FILES_btn_'+i).disabled = noMask;
	}
	document.getElementById('more_button').disabled = noMask;
	
	document.getElementById('start_button').disabled = noFiles && !document.fd1.dump_base.checked;
}

var savedNS;
var stop;
function StartDump()
{
	stop=false;
	savedNS='start!';
	document.getElementById('dump_result_div').innerHTML='';
	document.getElementById('stop_button').disabled=false;
	document.getElementById('start_button').disabled=true;
	setTimeout('DoNext()', 1000);
}

function DoNext()
{
	if(document.getElementById('NS'))
		newNS=document.getElementById('NS').value;
	else
		newNS=null;

	if(document.getElementById('NSTOP'))
	{
		EndDump();
		return;
	}

	if(newNS != savedNS)
	{
		queryString='lang=<?echo htmlspecialchars(LANG)?>';
		queryString+='&dumping=Y';
		if(savedNS!='start!')
		{
			queryString+='&Next=Y';
			if(document.getElementById('NS'))
				queryString+='&NS='+document.getElementById('NS').value;
		}
		if(document.getElementById('stepped').checked)
		{
			queryString+='&stepped=Y';
			queryString+='&max_execution_time='+document.getElementById('max_execution_time').value;
			queryString+='&dump_max_exec_time_sleep='+document.getElementById('dump_max_exec_time_sleep').value;
		}

		if(document.getElementById('dump_public').checked)
			queryString +='&d_pub=Y';

		if(document.getElementById('dump_kernel').checked)
				queryString+='&d_ker=Y';

		if(document.getElementById('skip_symlinks').checked)
				queryString+='&skip_symlinks=Y';

		if(document.getElementById('skip_mask').checked)
		{
			queryString+='&skip_mask=Y';

			oTable = document.getElementById('skip_mask_table');
			numRows = oTable.rows.length;

			for(i=0;i<numRows;i++)
				queryString+='&arMask[]=' + document.getElementById('mnu_FILES_'+i).value;
		}

		if(document.getElementById('dump_public').checked || document.getElementById('dump_kernel').checked)
			queryString+='&max_file_size='+document.getElementById('max_file_size').value;


		if(document.getElementById('dump_base').checked)
		{
			queryString +='&dump_base=Y';

			if(!document.getElementById('dump_stat').checked)
				queryString +='&stat=Y';
			if(!document.getElementById('dump_index').checked)
				queryString +='&index=Y';
		}
		savedNS=newNS;
		CHttpRequest.Action = function(result)
		{
			CloseWaitWindow();
			document.getElementById('dump_result_div').innerHTML = result;
			if(!stop)
				setTimeout('DoNext()', 1000 * document.getElementById('dump_max_exec_time_sleep').value);
		}
		ShowWaitWindow();
		CHttpRequest.Send('dump.php?'+queryString+'&<?echo bitrix_sessid_get()?>');
	}
}
function StopDump()
{
	stop=true;
	document.getElementById('stop_button').disabled=true;
	document.getElementById('start_button').disabled=false;
}
function EndDump()
{
	stop=true;
	document.getElementById('stop_button').disabled=true;
	document.getElementById('start_button').disabled=false;
}
function exportData(val)
{
	window.open('dump_export.php?f_id='+val);
}
function restoreDump(val)
{
	window.open('exec_restore.php?f_id='+val+'&<?echo bitrix_sessid_get()?>');
}

</script>


	<form name="fd1" action="<?echo $APPLICATION->GetCurPage()?>?lang=<?=LANG?>" method="GET">
	<?
	$editTab->Begin();
	$editTab->BeginNextTab();
	?>
	<tr>
		<td  width="40%"><?echo GetMessage("MAIN_DUMP_FILE_STEPPED")?></td>
		<td  width="60%"><input type="checkbox" name="stepped" id="stepped" value="Y" OnClick="trs.disabled=!this.checked;document.fd1.max_execution_time.disabled=!this.checked;" <?if(COption::GetOptionString("main", "dump_file_stepped")=="Y") echo " checked"?>></td>
	</tr>
	<tr id="trs">
		<td><?echo GetMessage("MAIN_DUMP_FILE_STEP")?></td>
		<td>
			<? $cur = COption::GetOptionString("main", "dump_max_exec_time","30"); ?>
			<select name="max_execution_time" id="max_execution_time">
				<option <? if ($cur==30) echo 'selected'; ?> value=30>30</option>
				<option <? if ($cur==45) echo 'selected'; ?> value=45>45</option>
				<option <? if ($cur==60) echo 'selected'; ?> value=60>60</option>
			</select>
			<?echo GetMessage("MAIN_DUMP_FILE_STEP_sec");?>
			,
			<?echo GetMessage("MAIN_DUMP_FILE_STEP_SLEEP")?>
			<? $cur = COption::GetOptionString("main", "dump_max_exec_time_sleep","10"); ?>
			<input name="dump_max_exec_time_sleep" id="dump_max_exec_time_sleep" value="<?=$cur?>" size=2>
			<?echo GetMessage("MAIN_DUMP_FILE_STEP_sec");?>
		</td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>
	<tr class="heading">
		<td colspan="2"><?echo GetMessage("MAIN_DUMP_FILE_TITLE")?></td>
	</tr>
	<tr>
		<td><?echo GetMessage("MAIN_DUMP_FILE_PUBLIC")?></td>
		<td><input type="checkbox" name="dump_public" id="dump_public" value="Y" OnClick="CheckActiveStart()" <?if(COption::GetOptionString("main", "dump_file_public")=="Y") echo " checked"?>></td>
	</tr>
	<tr id="tr_full">
		<td><?echo GetMessage("MAIN_DUMP_FILE_KERNEL")?></td>
		<td><input type="checkbox" name="dump_kernel" id="dump_kernel" value="Y" OnClick="CheckActiveStart()" <?if(COption::GetOptionString("main", "dump_file_kernel")=="Y") echo " checked";?>></td>
	</tr>
	<?
		$bNoFiles = COption::GetOptionString("main", "dump_file_public")!="Y" && COption::GetOptionString("main", "dump_file_kernel")!="Y";
	?>
	<tr id="tr_fsize">
		<td><?echo GetMessage("MAIN_DUMP_FILE_MAX_SIZE")?></td>
		<td><input type="text" name="max_file_size" id="max_file_size" size="10" value="<?echo COption::GetOptionString("main", "dump_max_file_size","1048576")?>" <? if(COption::GetOptionString("main", "dump_file_public")!="Y" && COption::GetOptionString("main", "dump_file_kernel")!="Y") echo " disabled"?>>
		<?echo GetMessage("MAIN_DUMP_FILE_MAX_SIZE_kb")?></td>
	</tr>
	<tr>
		<td><?echo GetMessage("MAIN_DUMP_SKIP_SYMLINKS")?></td>
		<td><input type="checkbox" name="skip_symlinks" id="skip_symlinks" <?=$bNoFiles?'disabled':''?> value="Y" <?if(COption::GetOptionString("main", "skip_symlinks", "Y")=="Y") echo " checked";?>></td>
	</tr>
	<? $bMask = COption::GetOptionString("main", "skip_mask", "N")=="Y";?>
	<tr>
		<td><?echo GetMessage("MAIN_DUMP_MASK")?><span class="required"><sup>1</sup></span></td>
		<td><input type="checkbox" name="skip_mask" id="skip_mask" <?=$bNoFiles?'disabled':''?> value="Y" <?=$bMask?" checked":'';?> onclick="CheckActiveStart()">
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>
			<table id="skip_mask_table" cellspacing=0 cellpadding=0>
			<?
			$i=-1;

			$res = unserialize(COption::GetOptionString("main","skip_mask_array"));
			$skip_mask_array = is_array($res)?$res:array();

			foreach($skip_mask_array as $mask)
			{
				$i++;
				echo
				'<tr><td>
					<input name="arMask[]" id="mnu_FILES_'.$i.'" value="'.htmlspecialchars($mask).'" '.(!$bMask||$bNoFiles?'disabled':'').' size=30>'.
					'<input type="button" id="mnu_FILES_btn_'.$i.'" '.(!$bMask||$bNoFiles?'disabled':'').' value="..." onclick="showMenu(this, \''.$i.'\')">'.
				'</tr>';
			}
			$i++;
			?>
				<tr><td><input name="arMask[]" id="mnu_FILES_<?=$i?>" size=30 <?=!$bMask||$bNoFiles?'disabled':'';?>><input type="button" id="mnu_FILES_btn_<?=$i?>" value="..." onclick="showMenu(this, '<?=$i?>')" <?=!$bMask||$bNoFiles?'disabled':'';?>></tr>
			</table>
			<input type=button id="more_button" value="<?=GetMessage('MAIN_DUMP_MORE')?>" onclick="AddTableRow()" <?=(!$bMask||$bNoFiles?'disabled':'')?>>
		</td>
	</tr>
	<tr>
		<td colspan=2 align=center>
		<?
		echo BeginNote();
		echo GetMessage("MAIN_DUMP_DB_NOTE");
		echo EndNote();
		?>
		</td>
	</tr>
	<tr class="heading">
		<td colspan="2"><?echo GetMessage("MAIN_DUMP_BASE_TITLE")?></td>
	</tr>
	<?
	global $DBType;
	if (strtoupper($DBType)!='MYSQL') 
	{
		$strDisableNotMysql = 'disabled';
	?>
	<tr>
		<td colspan=2 align=center><? 
		echo BeginNote();
		echo GetMessage('MAIN_DUMP_MYSQL_ONLY');
		echo EndNote();
		?></td>
	</tr>
	<? 
	} else 
		$strDisableNotMysql = '';
	?>
	<tr>
		<td><?echo GetMessage("MAIN_DUMP_BASE_TRUE")?></td>
		<td><input type="checkbox" <?=$strDisableNotMysql?> name="dump_base" id="dump_base" value="Y" OnClick=" document.getElementById('start_button').disabled=(!this.checked&&!document.fd1.dump_public.checked&&!document.fd1.dump_kernel.checked); document.fd1.dump_stat.disabled=!this.checked;document.fd1.dump_index.disabled=!this.checked;" <?if((COption::GetOptionString("main", "dump_base_true")=="Y") && ($DB->type == "MYSQL")) echo " checked"?> <? if($DB->type != "MYSQL") echo " disabled"?>><?= " ( ".getTableSize("")." ".GetMessage("MAIN_DUMP_BASE_SIZE")." ) " ;?>
		</td>
	</tr>

	<tr>
		<td><?echo GetMessage("MAIN_DUMP_BASE_IGNORE")?></td>
		<td><input type="checkbox" <?=$strDisableNotMysql?> name="dump_stat" id="dump_stat" value="Y" <?if(COption::GetOptionString("main", "dump_base_stat")!="Y") echo " checked"?> <?if(COption::GetOptionString("main", "dump_base_true")!="Y") echo " disabled"?>> <? echo GetMessage("MAIN_DUMP_BASE_STAT")." ( ".getTableSize("b_stat")." ".GetMessage("MAIN_DUMP_BASE_SIZE")." )" ?>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="checkbox" <?=$strDisableNotMysql?> name="dump_index" id="dump_index" value="Y"<?if(COption::GetOptionString("main", "dump_base_index")!="Y") echo " checked"?>  <?if(COption::GetOptionString("main", "dump_base_true")!="Y") echo " disabled"?>> <? echo GetMessage("MAIN_DUMP_BASE_SINDEX")." ( ".getTableSize("b_search_content")." ".GetMessage("MAIN_DUMP_BASE_SIZE")." )" ?>
		</td>
	</tr>

	<?$editTab->Buttons();
	?>
	<input type="button" id="start_button" value="<?echo GetMessage("MAIN_DUMP_FILE_DUMP_BUTTON")?> " <?if((COption::GetOptionString("main", "dump_file_public")!="Y") && (COption::GetOptionString("main", "dump_base_true")!="Y") && (COption::GetOptionString("main", "dump_file_kernel")!="Y")) echo " disabled"?> OnClick="StartDump();">
	<input type="button" id="stop_button" value="<?echo GetMessage("MAIN_DUMP_FILE_STOP_BUTTON")?>" OnClick="StopDump();" disabled>

	<?
	$editTab->End();
	?>
	</form>

<?
$lAdmin->DisplayList();

echo BeginNote();
echo '<span class=required><sup>1</sup></span> '.GetMessage("MAIN_DUMP_FOOTER_MASK");
echo EndNote();

require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");
}

function mnu_DirList($v='')
{
	$path = realpath($_SERVER['DOCUMENT_ROOT'].$v);
	if (strlen($path) < strlen(realpath($_SERVER['DOCUMENT_ROOT']))) // Security check
		return;
	if (!is_dir($path))
		return;

	$mnu_loading = array(
		array(
			'TEXT'=>GetMessage('MAIN_DUMP_LOADING'),
			'ICON'=>'loading'
			)
		);
	$res = array();
	$dir = opendir($path);
	while(($file=readdir($dir))!==false)
	{
		if ($file=='.' || $file=='..' || !is_dir($path."/".$file))
			continue;
		$res[]=array(
			'TEXT'=>$file,
			'ONCLICK'=>"mnu_SelectValue('".$v."/".$file."')",
			'ICON'=>'fileman_menu_icon_sections',
			'MENU'=>has_subdirs($path."/".$file)?$mnu_loading:null,
			'ONMENUPOPUP'=>"mnu_ShowSubItems(menu, '".$v."/".$file."')"
			);
	}
	closedir($dir);
	return $res;
}

function has_subdirs($path)
{
	$dir = opendir($path);
	while(($file=readdir($dir))!==false)
		if ($file != '.' && $file != '..' && is_dir($path."/".$file))
		{
			closedir($dir);
			return true;
		}
	closedir($dir);
}

function SkipMask($abs_path)
{
	global $skip_mask_array;
	
	$path = substr($abs_path,strlen($_SERVER['DOCUMENT_ROOT']));

	$path = str_replace('\\','/',$path);
	
	$preg_mask_array = prepare_preg_escape($skip_mask_array);
	reset($skip_mask_array);
	foreach($skip_mask_array as $k=>$mask)
	{
		if (strpos($mask,'/')===0) // absolute path
		{
			if (strpos($mask,'*')===false) // no asterisk present
			{
				if (rtrim($path,'/') == $mask)
					return true;
			}
			elseif (preg_match('#^'.str_replace('*','[^/]*?',$preg_mask_array[$k]).'$#i',$path))
				return true;
		}
		elseif (strpos($mask, '/')===false)
		{
			if (strpos($mask,'*')===false)
			{
				if (substr($path,-strlen($mask)) == $mask)
					return true;
			}
			elseif (preg_match('#/[^/]*'.str_replace('*','[^/]*?',$preg_mask_array[$k]).'$#i',$path))
				return true;
		}
	}
}

function prepare_preg_escape($skip_mask_array)
{
	static $res;
	if (!isset($res))
		foreach($skip_mask_array as $a)
			$res[] = _preg_escape($a); 
	return $res;
}

function _preg_escape($str)
{
	$search = array('#','[',']','.','?','(',')','^','$','|','{','}');
	$replace = array('\#','\[','\]','\.','\?','\(','\)','\^','\$','\|','\{','\}');
	return str_replace($search, $replace, $str);
}
?>
