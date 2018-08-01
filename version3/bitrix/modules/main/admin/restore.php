<?
error_reporting(E_ALL & ~E_NOTICE);
if(str_replace(array('\\','/'),'',dirname(__FILE__)) != str_replace(array('\\','/'),'',$_SERVER['DOCUMENT_ROOT']))
	die('Must be started from DOCUMENT_ROOT');

if(isset($_SERVER["BX_PERSONAL_ROOT"]) && $_SERVER["BX_PERSONAL_ROOT"] <> "")
	define("BX_PERSONAL_ROOT", $_SERVER["BX_PERSONAL_ROOT"]);
else
	define("BX_PERSONAL_ROOT", "/bitrix");

if (!defined("BX_DIR_PERMISSIONS"))
	define("BX_DIR_PERMISSIONS", 0755);

if (!defined("BX_FILE_PERMISSIONS"))
	define("BX_FILE_PERMISSIONS", 0644);

if(!defined("START_EXEC_TIME"))
	define("START_EXEC_TIME", getmicrotime());

if (function_exists('mb_internal_encoding'))
	mb_internal_encoding('ISO-8859-1');


# http://bugs.php.net/bug.php?id=48886 - We have 2Gb file limit on Linux

#@set_time_limit(0);
ob_start();

$lang = $_REQUEST['lang']?($_REQUEST['lang']=='ru'?'ru':'en'):(preg_match('#ru#i',$_SERVER['HTTP_ACCEPT_LANGUAGE'])?'ru':'en');
if ($lang=='ru' && !headers_sent())
	header("Content-type:text/html; charset=windows-1251");

$mArr_ru =  array(
			"WINDOW_TITLE" => "Восстановление архива",
			"BACK" => "Назад",
			"BEGIN" => "
			<p>
			<ul>
			<li>Перейдите в административную панель своего сайта на страницу <b>Настройки &gt; Инструменты &gt; Резервное копирование</b>
			<li>Создайте полную резервную копию, которая будет включать <b>публичную часть</b>, <b>ядро</b> и <b>базу данных</b>
			</ul>
			<b>Документация:</b> <a href='http://dev.1c-bitrix.ru/api_help/main/going_remote.php' target='_blank'>http://dev.1c-bitrix.ru/api_help/main/going_remote.php</a>
			</p>
			",
			"ARC_DOWN" => "Скачать архив с дальнего сайта",
			"DB_SETTINGS" => "Данные для подключения к базе данных:",
			"DB_DEF" => "по умолчанию для выделенного сервера или виртуальной машины",
			"DB_ENV" => "восстановление в &quot;Битрикс: Веб-окружение&quot;",
			"DB_OTHER" => "установить значения вручную",
			"ARC_DOWN_SITE" => "адрес сайта (www.site.ru):",
			"ARC_DOWN_NAME" => "имя архива (20090225_443e1.tar.gz):",
			"OR" => "ИЛИ",
			"ARC_DOWN_URL" => "прямой URL архива (http://site.ru/20090225_443e1.tar.gz):",
			"NO_FILES" => "нет архивов",
			"TITLE0" => "Шаг 1: Подготовка архива",
			"TITLE1" => "Шаг 2: Распаковка архива",
			"TITLE_PROCESS1" => "Шаг 2: Выполняется распаковка архива",
			"TITLE_PROCESS2" => "Шаг 3: Выполняется восстановление базы данных",
			"TITLE2" => "Шаг 3: Восстановление базы данных",
			"SELECT_LANG" => "Выберите язык",
			"ARC_SKIP" => "Архив уже распакован",
			"ARC_SKIP_DESC" => "переход к восстановлению базы данных",
			"ARC_NAME" => "Архив загружен в корневую папку сервера",
			"ARC_LOCAL" => "Загрузить с локального диска",
			"MAX_TIME" => "Шаг выполнения (сек.)",
			"ERR_NO_ARC" => "Не выбран архив для распаковки!",
			"BUT_TEXT1" => "Далее",
			"DUMP_NAME" => "Файл резервной копии базы:",
			"USER_NAME" => "Имя пользователя базы данных",
			"USER_PASS" => "Пароль",
			"BASE_NAME" => "Имя базы данных",
			"BASE_HOST" => "Адрес сервера базы данных",
			"BASE_RESTORE" => "Восстановить",
			"ERR_NO_DUMP" => "Не выбран архив базы данных для восстановления!",
			"ERR_EXTRACT" => "Ошибка:",
			"ERR_UPLOAD" => "Не удалось загрузить файл на сервер",
			"ERR_DUMP_RESTORE" => "Ошибка восстановления базы данных:",
			"ERR_DB_CONNECT" => "Ошибка соединения с базой данных:",
			"ERR_CREATE_DB" => "Ошибка создания базы",
			"FINISH" => "Операция выполнена успешно",
			"FINISH_MSG" => "Операция восстановления системы завершена успешно!",
			"EXTRACT_FINISH_TITLE" => "Распаковка архива",
			"EXTRACT_FINISH_MSG" => "Распаковка архива завершена успешно!",
			"BASE_CREATE_DB" => "Создать базу данных",
			"EXTRACT_FINISH_DELL" => "Обязательно удалите скрипт restore.php и файл резервной копии из корневой директории сайта.",
			"EXTRACT_FULL_FINISH_DELL" => "Обязательно удалите скрипт restore.php, файл резервной копии из корневой директории сайта, а также дамп базы.",
			"BUT_DELL" => "Удалить",
			"FINISH_ERR_DELL" => "Не удалось удалить все временные файлы! Обязательно удалите их вручную.",
			"FINISH_ERR_DELL_TITLE" => "Ошибка удаления файлов!",
			"NO_READ_PERMS" => "Нет прав на чтение корневой папки сайта",
			);

$mArr_en = array(
			"WINDOW_TITLE" => "Restoring",
			"BACK" => "Back",
			"BEGIN" => "
			<p>
			<ul>
			<li>Step 1. Open Control Panel section of your old site and select <b>Settings &gt; Tools &gt; Backup</b>
			<li>Create full archive which contains <b>public site files</b>, <b>kernel files</b> and <b>database dump</b>
			</ul>
			<b>Documentation:</b> <a href='http://www.bitrixsoft.com/support/training/course/lesson.php?COURSE_ID=12&ID=441' target='_blank'>learning course</a>
			</p>
			",
			"ARC_DOWN" => "Download from remote server",
			"DB_SETTINGS" => "Database settings:",
			"DB_DEF" => "default values for Dedicated Server or Virtual Machine",
			"DB_ENV" => "restoring in Bitrix Environment",
			"DB_OTHER" => "custom database settings",
			"ARC_DOWN_SITE" => "Server URL (www.site.com):",
			"ARC_DOWN_NAME" => "Archive name (20090225_443e1.tar.gz):",
			"OR" => "OR",
			"ARC_DOWN_URL" => "Archive URL (http://www.site.com/20090225_443e1.tar.gz):",
			"NO_FILES" => "no archives found",
			"TITLE0" => "Step 1: Archive Creation",
			"TITLE1" => "Step 2: Archive Extracting",
			"TITLE_PROCESS1" => "Step 2: Extracting an archive...",
			"TITLE_PROCESS2" => "Step 3: Restoring database...",
			"TITLE2" => "Step 3: Database restore",
			"SELECT_LANG" => "Choose the language",
			"ARC_SKIP" => "Archive is already extracted",
			"ARC_SKIP_DESC" => "Starting database restore",
			"ARC_NAME" => "Archive is stored in document root folder",
			"ARC_LOCAL" => "Upload from local disk",
			"MAX_TIME" => "Step (sec.)",
			"ERR_NO_ARC" => "Archive for extracting is not specified!",
			"BUT_TEXT1" => "Continue",
			"DUMP_NAME" => "Database dump file:",
			"USER_NAME" => "Database User Name",
			"USER_PASS" => "Password",
			"BASE_NAME" => "Database Name",
			"BASE_HOST" => "Database Host",
			"BASE_RESTORE" => "Restore",
			"ERR_NO_DUMP" => "Database dump file is not specified!",
			"ERR_EXTRACT" => "Error:",
			"ERR_UPLOAD" => "Unable to upload file",
			"ERR_DUMP_RESTORE" => "Error restoring the database:",
			"ERR_DB_CONNECT" => "Error connecting the database:",
			"ERR_CREATE_DB" => "Error creating the database",
			"FINISH" => "Successfully completed",
			"FINISH_MSG" => "Restoring of the system was completed successfully!",
			"EXTRACT_FINISH_TITLE" => "Archive extracting",
			"EXTRACT_FINISH_MSG" => "Archive extracting was completed successfully!",
			"BASE_CREATE_DB" => "Create database",
			"EXTRACT_FINISH_DELL" => "Warning! You should delete restore.php script and backup copy file from the root folder of your site!",
			"EXTRACT_FULL_FINISH_DELL" => "Warning! You should delete restore.php script, backup copy file and database dump from the root folder of your site!",
			"BUT_DELL" => "Delete",
			"FINISH_ERR_DELL" => "Failed to delete temporary files! You should delete them manually",
			"FINISH_ERR_DELL_TITLE" => "Error deleting the files!",
			"NO_READ_PERMS" => "No permissions for reading Web Server root",
			);

	$MESS = array();
	if ($lang=="ru")
	{
		$MESS["LOADER_SUBTITLE1"] = "Загрузка архива";
		$MESS["STATUS"] = "% выполнено...";
		$MESS["LOADER_MENU_UNPACK"] = "Распаковка файла";
		$MESS["LOADER_TITLE_LIST"] = "Выбор файла";
		$MESS["LOADER_TITLE_LOAD"] = "Загрузка файла на сайт";
		$MESS["LOADER_TITLE_UNPACK"] = "Распаковка файла";
		$MESS["LOADER_TITLE_LOG"] = "Отчет по загрузке";
		$MESS["LOADER_NEW_LOAD"] = "Загрузить";
		$MESS["LOADER_BACK_2LIST"] = "Вернуться в список файлов";
		$MESS["LOADER_LOG_ERRORS"] = "Загрузка архива не удалась";
		$MESS["LOADER_NO_LOG"] = "Log-файл не найден";
		$MESS["LOADER_KB"] = "кб";
		$MESS["LOADER_LOAD_QUERY_SERVER"] = "Запрашиваю сервер...";
		$MESS["LOADER_LOAD_QUERY_DISTR"] = "Запрашиваю файл #DISTR#";
		$MESS["LOADER_LOAD_CONN2HOST"] = "Открываю соединение к #HOST#...";
		$MESS["LOADER_LOAD_NO_CONN2HOST"] = "Не могу соединиться с #HOST#:";
		$MESS["LOADER_LOAD_QUERY_FILE"] = "Запрашиваю файл...";
		$MESS["LOADER_LOAD_WAIT"] = "Ожидаю ответ...";
		$MESS["LOADER_LOAD_SERVER_ANSWER"] = "Ошибка загрузки. Сервер ответил: #ANS#";
		$MESS["LOADER_LOAD_SERVER_ANSWER1"] = "Ошибка загрузки. У вас нет прав на доступ к этому файлу. Сервер ответил: #ANS#";
		$MESS["LOADER_LOAD_NEED_RELOAD"] = "Ошибка загрузки. Докачка файла невозможна.";
		$MESS["LOADER_LOAD_NO_WRITE2FILE"] = "Не могу открыть файл #FILE# на запись";
		$MESS["LOADER_LOAD_LOAD_DISTR"] = "Загружаю файл #DISTR#";
		$MESS["LOADER_LOAD_ERR_SIZE"] = "Ошибка размера файла";
		$MESS["LOADER_LOAD_ERR_RENAME"] = "Не могу переименовать файл #FILE1# в файл #FILE2#";
		$MESS["LOADER_LOAD_CANT_OPEN_WRITE"] = "Не могу открыть файл #FILE# на запись";
		$MESS["LOADER_LOAD_CANT_OPEN_READ"] = "Не могу открыть файл #FILE# на чтение";
		$MESS["LOADER_LOAD_LOADING"] = "Загружаю файл... дождитесь окончания загрузки...";
		$MESS["LOADER_LOAD_FILE_SAVED"] = "Файл сохранен: #FILE# [#SIZE# байт]";
		$MESS["LOADER_UNPACK_ACTION"] = "Распаковываю файл... дождитесь окончания распаковки...";
		$MESS["LOADER_UNPACK_UNKNOWN"] = "Неизвестная ошибка. Повторите процесс еще раз или обратитесь в службу технической поддержки";
		$MESS["LOADER_UNPACK_SUCCESS"] = "Файл успешно распакован";
		$MESS["LOADER_UNPACK_ERRORS"] = "Файл распакован с ошибками";
		$MESS["LOADER_KEY_DEMO"] = "Демонстрационная версия";
		$MESS["LOADER_KEY_COMM"] = "Коммерческая версия";
	}
	else
	{
		$MESS["LOADER_SUBTITLE1"] = "Loading";
		$MESS["STATUS"] = "% done...";
		$MESS["LOADER_MENU_LIST"] = "Select package";
		$MESS["LOADER_MENU_UNPACK"] = "Unpack file";
		$MESS["LOADER_TITLE_LIST"] = "Select file";
		$MESS["LOADER_TITLE_LOAD"] = "Uploading file to the site";
		$MESS["LOADER_TITLE_UNPACK"] = "Unpack file";
		$MESS["LOADER_TITLE_LOG"] = "Upload report";
		$MESS["LOADER_NEW_ED"] = "package edition";
		$MESS["LOADER_NEW_AUTO"] = "automatically start unpacking after loading";
		$MESS["LOADER_NEW_STEPS"] = "load gradually with interval:";
		$MESS["LOADER_NEW_STEPS0"] = "unlimited";
		$MESS["LOADER_NEW_LOAD"] = "Download";
		$MESS["LOADER_BACK_2LIST"] = "Back to packages list";
		$MESS["LOADER_LOG_ERRORS"] = "Error occured";
		$MESS["LOADER_NO_LOG"] = "Log file not found";
		$MESS["LOADER_KB"] = "kb";
		$MESS["LOADER_LOAD_QUERY_SERVER"] = "Connecting server...";
		$MESS["LOADER_LOAD_QUERY_DISTR"] = "Requesting package #DISTR#";
		$MESS["LOADER_LOAD_CONN2HOST"] = "Establishing connection to #HOST#...";
		$MESS["LOADER_LOAD_NO_CONN2HOST"] = "Cannot connect to #HOST#:";
		$MESS["LOADER_LOAD_QUERY_FILE"] = "Requesting file...";
		$MESS["LOADER_LOAD_WAIT"] = "Waiting for response...";
		$MESS["LOADER_LOAD_SERVER_ANSWER"] = "Error while downloading. Server reply was: #ANS#";
		$MESS["LOADER_LOAD_SERVER_ANSWER1"] = "Error while downloading. Your can not download this package. Server reply was: #ANS#";
		$MESS["LOADER_LOAD_NEED_RELOAD"] = "Error while downloading. Cannot resume download.";
		$MESS["LOADER_LOAD_NO_WRITE2FILE"] = "Cannot open file #FILE# for writing";
		$MESS["LOADER_LOAD_LOAD_DISTR"] = "Downloading package #DISTR#";
		$MESS["LOADER_LOAD_ERR_SIZE"] = "File size error";
		$MESS["LOADER_LOAD_ERR_RENAME"] = "Cannot rename file #FILE1# to #FILE2#";
		$MESS["LOADER_LOAD_CANT_OPEN_WRITE"] = "Cannot open file #FILE# for writing";
		$MESS["LOADER_LOAD_CANT_OPEN_READ"] = "Cannot open file #FILE# for reading";
		$MESS["LOADER_LOAD_LOADING"] = "Download in progress. Please wait...";
		$MESS["LOADER_LOAD_FILE_SAVED"] = "File saved: #FILE# [#SIZE# bytes]";
		$MESS["LOADER_UNPACK_ACTION"] = "Unpacking the package. Please wait...";
		$MESS["LOADER_UNPACK_UNKNOWN"] = "Unknown error occured. Please try again or consult the technical support service";
		$MESS["LOADER_UNPACK_SUCCESS"] = "The file successfully unpacked";
		$MESS["LOADER_UNPACK_ERRORS"] = "Errors occured while unpacking the file";
		$MESS["LOADER_KEY_DEMO"] = "Demo version";
		$MESS["LOADER_KEY_COMM"] = "Commercial version";
	}

if (!$_REQUEST['ajax_mode'])
{
?>
<html>
<head>
	<title><?= getMsg("WINDOW_TITLE", $lang)?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
	<style type="text/css">
		.tablebody1 {background-color:#f9fafd; padding:5px;font-family:Verdana,Arial, Helvetica, sans-serif;color:#616263;font-size:10px;}
		.tabletitle1 {background-color:#ffffff; font-family:Arial;color:#000000;font-size:17px;font-weight: bold;}
		.tableborder1 {background-color:#aeb8d7; padding:0;}
		.tableborder2 {background-color:#cccccc; padding:0;}
		.selectitem {width: 200;}
	</style>
</head>

<body>
<table width=100% height=100%><tr><td align=center valign=middle>


<form name="restore" id="restore" action="restore.php" enctype="multipart/form-data" method="POST">
<input type="hidden" name="Step" id="Step_id" value="">
<input type="hidden" name="lang" value="<?=$lang?>">
<script language="JavaScript">
	function reloadPage(val, lang, delay)
	{
		document.getElementById('Step_id').value = val;
		document.getElementById('restore').action='restore.php?lang=<?=$lang?>';
		if (null!=delay)
			window.setTimeout("document.getElementById('restore').submit()",1000);
		else
			document.getElementById('restore').submit();
	}
</script>
<?
}

if ($_REQUEST['source']=='dump')
	$bSkipExtract = true;

$Step = IntVal($_REQUEST["Step"]);

if ($Step == 2 && !$bSkipExtract)
{
	if ($_REQUEST['source']=='download')
	{
		$url = $_REQUEST['arc_down_url'];
		if (!$url)
			$url = $_REQUEST['arc_down_site'].'/bitrix/backup/'.$_REQUEST['arc_down_name'];
		if ($_REQUEST['ajax_mode'])
		{
			if (!preg_match('#http://#',$url))
				$url = 'http://'.$url;
			$name = basename($url);
			if (!preg_match("#\.tar\.gz$#",$name))
				$name = 'archive.tar.gz';
			$strLog = '';
			$status = '';

			$res = LoadFile($url, $name, 2);
			if (!$res)
			{
				echo nl2br($strLog);
				echo '<p align=right><input type=button onClick="document.location=\'?Step=1\'" value="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.getMsg('BACK').'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"></p>';
				die();
			}
			elseif ($res==2) // частичная закачка
			{
				echo $status;
				?><script>window.setTimeout('document.location="restore.php?ajax_mode=Y&lang=<?=$lang?>&Step=2&source=download&arc_down_url=<?=urlencode($url)?>"',1000);</script><?
				die();
			}
			else
			{
				?><script>parent.location='restore.php?lang=<?=$lang?>&Step=2&arc_name=<?=urlencode($name)?>';</script><?
			}
		}
		else
		{
			SetCurrentProgress(0);
			echo showMsg(LoaderGetMessage('LOADER_SUBTITLE1'),$status);
			?>
			</form>
			<iframe width=1 height=1 style="display:none" onload="document.getElementById('msg').innerHTML=this.contentDocument.body.innerHTML" src="restore.php?ajax_mode=Y&lang=<?=$lang?>&Step=2&source=download&arc_down_url=<?=urlencode($url)?>"></iframe>
			</td></tr></table></body></html>
			<?
			die();
		}
	}
	elseif($_REQUEST['source']=='upload')
	{
		$tmp = $_FILES['archive'];
		$arc_name = $_REQUEST['arc_name'] = 'archive.tar.gz';
		if (!move_uploaded_file($tmp['tmp_name'],$_SERVER['DOCUMENT_ROOT'].'/'.$arc_name) || !filesize($_SERVER['DOCUMENT_ROOT'].'/'.$arc_name))
		{
			echo showMsg(getMsg('ERR_EXTRACT'),getMsg('ERR_UPLOAD'));
			$Step = 1;
		}
	}
}
elseif($Step == 3)
{
	$max_exec_time = 10;
	$d_pos = (double) $_REQUEST["d_pos"];
	if ($d_pos < 0)
		$d_pos = 0;

	if ($_REQUEST['db_settings']=='default' || $_REQUEST['db_settings'] == 'env')
	{
		$_REQUEST['db_host'] = 'localhost'.($_REQUEST['db_settings'] == 'env' ? ':31006' : '');
		$_REQUEST['db_name'] = 'bitrix';
		$_REQUEST['db_user'] = 'root';
		$_REQUEST['db_pass'] = '';
		$_REQUEST['create_db'] = 'Y';
	}

	$oDB = new CDBRestore($_REQUEST["db_host"], $_REQUEST["db_name"], $_REQUEST["db_user"], $_REQUEST["db_pass"], $_REQUEST["dump_name"], START_EXEC_TIME, $max_exec_time, $d_pos);

	if(!$oDB->Connect())
	{
		echo showMsg(getMsg("ERR_DB_CONNECT", $lang), $oDB->getError());
		$Step = 2;
		$bSkipExtract = true;
	}
}









if(!$Step)
{
	?>
		<table width="600"  border="0" cellspacing="0" cellpadding="0" align=center>
		<tr><td colspan="5" class="tabletitle1" align="Left" nowrap="nowrap" valign="center"><?= getMsg("TITLE0", $lang)?></td></tr>
		<tr>
			<td colspan="5" class="tableborder2" height="1"></td>
		</tr>
		<tr>
			<td colspan="5" align="center">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="5" class="tableborder1" height="1"></td>
		</tr>
		<tr>
			<td class="tableborder1" width="1"></td>
			<td class="tablebody1" colspan=3 height="5">&nbsp;</td>
			<td class="tableborder1" width="1"></td>
		</tr>
		<tr>
			<td width="1" class="tableborder1"></td>
			<td class="tablebody1" width="19">&nbsp;</td>
			<td class="tablebody1">
			<?=getMsg('BEGIN')?>
			<br>
			<?
				$img = 'images/dump'.($lang=='ru'?'_ru':'').'.png';
				if (file_exists($img))
					echo '<img src="'.$img.'">';
			?>
			</td>
			<td class="tablebody1" width="19">&nbsp;</td>
			<td class="tableborder1" width="1"></td>
		<tr>
			<td class="tableborder1" width="1"></td>
			<td colspan="3" class="tablebody1" height="3">&nbsp;</td>
			<td class="tableborder1" width="1"></td>
		</tr>
		<tr>
			<td width="1" class="tableborder1"></td>
			<td class="tablebody1" width="19">&nbsp;</td>
			<td class="tablebody1" align="right"><input type="button" class="selectitem" value="<?= getMsg("BUT_TEXT1", $lang)?>" onClick="reloadPage(1,'<?=$lang?>')"></td>
			<td class="tablebody1" width="19">&nbsp;</td>
			<td class="tableborder1" width="1"></td>
		</tr>
		<tr>
			<td class="tableborder1" width="1"></td>
			<td colspan="3" class="tablebody1" height="3">&nbsp;</td>
			<td class="tableborder1" width="1"></td>
		</tr>

		<tr>
			<td colspan="5" class="tableborder1" height="1"></td>
		</tr>
		</table>
	<?
}
elseif($Step == 1)
{
	?>
	<script>
		function div_show(i)
		{
			document.getElementById('div1').style.display='none';
			document.getElementById('div2').style.display='none';
			document.getElementById('div3').style.display='none';
			document.getElementById('div4').style.display='none';
			document.getElementById('div'+i).style.display='block';
		}
	</script>
	<style type="text/css">
		.div-tool
		{
			border:1px solid #CCCCCC;
			padding:10px;
		}
	</style>
		<table width="600"  border="0" cellspacing="0" cellpadding="0" align=center>
		<tr><td colspan="5" class="tabletitle1" align="Left" nowrap="nowrap" valign="center"><?= getMsg("TITLE1", $lang)?></td></tr>
		<tr>
			<td colspan="5" class="tableborder2" height="1"></td>
		</tr>
		<tr>
			<td colspan="5" align="center">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="5" class="tableborder1" height="1"></td>
		</tr>
		<tr>
			<td class="tableborder1" width="1"></td>
			<td class="tablebody1" colspan=3 height="5">&nbsp;</td>
			<td class="tableborder1" width="1"></td>
		</tr>
		<tr>
			<td width="1" class="tableborder1"></td>
			<td class="tablebody1" width="19">&nbsp;</td>
			<td class="tablebody1">
				<input type=radio name=source value=download id=val1 onclick="div_show(1)"><label for=val1><?= getMsg("ARC_DOWN", $lang)?></label>
				<div id=div1 class="div-tool" style="display:none" align="right">
			<?=getMsg("ARC_DOWN_SITE")?> <input name=arc_down_site size=20><br>
			<?=getMsg("ARC_DOWN_NAME")?> <input name=arc_down_name size=20><br>
									<?=getMsg("OR")?><br>
			<?=getMsg("ARC_DOWN_URL")?> <input name=arc_down_url size=40><br>
				</div>
			</td>
			<td class="tablebody1" width="19">&nbsp;</td>
			<td class="tableborder1" width="1"></td>
		</tr>
			<td width="1" class="tableborder1"></td>
			<td class="tablebody1" width="19">&nbsp;</td>
			<td class="tablebody1" align="left">
				<input type=radio name=source value=upload id=val2 onclick="div_show(2)"><label for=val2><?= getMsg("ARC_LOCAL", $lang)?></label>
				<div id=div2 class="div-tool" style="display:none">
					<input type=file name=archive size=40>
				</div>
			</td>
			<td class="tablebody1" width="19">&nbsp;</td>
			<td class="tableborder1" width="1"></td>
		</tr>
		<tr>
			<td width="1" class="tableborder1"></td>
			<td class="tablebody1" width="19">&nbsp;</td>
			<td class="tablebody1">
				<input type=radio name=source value=local id=val3 onclick="div_show(3)"><label for=val3><?= getMsg("ARC_NAME", $lang)?></label>
				<div id=div3 class="div-tool" style="display:none">
				<?
					$option = getArcList();

					echo ((strlen($option) > 0) ? '<select class="selectitem" name="arc_name">'.$option.'</select>' : '<span style="color:#999999">'.getMsg('NO_FILES', $lang).'</span>');
				?>
				</div>
				<?
				if ($option === false)
					echo '<div style="color:red">'.getMsg('NO_READ_PERMS', $lang).'</div>';
				?>
			</td>
			<td class="tablebody1" width="19">&nbsp;</td>
			<td class="tableborder1" width="1"></td>
		</tr>
		<tr>
			<td width="1" class="tableborder1"></td>
			<td class="tablebody1" width="19">&nbsp;</td>
			<td class="tablebody1">
				<input type=radio name=source value=dump id=val4 onclick="div_show(4)"><label for=val4><?= getMsg("ARC_SKIP", $lang)?></label>
				<div id=div4 class="div-tool" style="display:none;color:#999999"><?=getMsg('ARC_SKIP_DESC')?><div>

			</td>
			<td class="tablebody1" width="19">&nbsp;</td>
			<td class="tableborder1" width="1"></td>
		</tr>
		<tr>
			<td class="tableborder1" width="1"></td>
			<td colspan="3" class="tablebody1" height="3">&nbsp;</td>
			<td class="tableborder1" width="1"></td>
		</tr>
		<tr>
			<td width="1" class="tableborder1"></td>
			<td class="tablebody1" width="19">&nbsp;</td>
			<td class="tablebody1" align="right"><input type="button" class="selectitem" id="start_button" value="<?= getMsg("BUT_TEXT1", $lang)?>" onClick="reloadPage(2,'<?=$lang?>')"></td>
			<td class="tablebody1" width="19">&nbsp;</td>
			<td class="tableborder1" width="1"></td>
		</tr>
		<tr>
			<td class="tableborder1" width="1"></td>
			<td colspan="3" class="tablebody1" height="3">&nbsp;</td>
			<td class="tableborder1" width="1"></td>
		</tr>

		<tr>
			<td colspan="5" class="tableborder1" height="1"></td>
		</tr>
		</table>
	<?
}
elseif($Step == 2)
{
	if (!$arc_name)
		$arc_name = htmlspecialchars($_REQUEST["arc_name"]);
	$max_exec_time = 30;
	$pos = (double) $_REQUEST["pos"];
	if ($pos < 0)
		$pos = 0;

	$oArc = new CArchiver($_SERVER["DOCUMENT_ROOT"]."/".$arc_name, true, START_EXEC_TIME, $max_exec_time, $pos, true);

	if(!$bSkipExtract && !$oArc->extractFiles($_SERVER["DOCUMENT_ROOT"]."/") && $oArc->end_time)
	{
		$pos = $oArc->getFilePos();
		SetCurrentProgress($pos,filesize($_SERVER["DOCUMENT_ROOT"]."/".$arc_name)/0.3);
		echo showMsg(getMsg('TITLE_PROCESS1'),$status);
		?>

		<input type="hidden" name="time" id="time_id" value="<?= $max_exec_time?>">
		<input type="hidden" name="pos" id="pos_id" value="<?= $pos?>">
		<input type="hidden" name="arc_name" id="arc_name_id" value="<?= $arc_name?>">
		<script>reloadPage(2, '<?= $lang?>', 1);</script>
		<?
	}
	else
	{
		if(count($oArc->GetErrors()) > 0)
		{
			$earr = array();
			$earr = $oArc->GetErrors();
			$e_str = "";
			foreach($earr as $val)
			{

				$e_str .= $val[1]."<br>";
			}

			echo showMsg(getMsg("ERR_EXTRACT", $lang), $e_str);
		}
		else
		{
			$arDName = getDumpList();
			$arc_name = $_REQUEST["arc_name"];

			if(count($arDName))
			{
			?>
				<script>
					function s_display(val)
					{
						document.getElementById('settings').style.display = (val ? 'block' : 'none');
					}
				</script>
				<table width="600"  border="0" cellspacing="0" cellpadding="0" align=center>
				<tr>
					<td colspan="6" class="tabletitle1" align="Left" nowrap="nowrap" valign="center"><?= getMsg("TITLE2", $lang)?></td>
				</tr>
				<tr>
					<td colspan="6" class="tableborder2" height="1"></td>
				</tr>
				<tr>
					<td colspan="6" class="" align="center" nowrap="nowrap" valign="center">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="6" class="tableborder1" height="1"></td>
				</tr>
				<tr>
					<td class="tableborder1" width="1"></td>
					<td colspan="4" class="tablebody1" height="5">&nbsp;</td>
					<td class="tableborder1" width="1"></td>
				</tr>
				<tr>
					<td width="1" class="tableborder1"></td>
					<td class="tablebody1" width="19">&nbsp;</td>
					<td class="tablebody1" align="right" nowrap="nowrap"><?= getMsg("DUMP_NAME", $lang)?></td>
					<td class="tablebody1" width="240">
					<?
						if (count($arDName)>1)
							echo '<select class="selectitem" name="dump_name"><option>'.(implode('</option><option>',$arDName)).'</option></select>';
						else
							echo htmlspecialchars($arDName[0]).'<input type=hidden name=dump_name value="'.htmlspecialchars($arDName[0]).'">';
					?>
					</td>
					<td class="tablebody1" width="19">&nbsp;</td>
					<td class="tableborder1" width="1"></td>
				</tr>
				<tr>
					<td width="1" class="tableborder1"></td>
					<td class="tablebody1" width="19">&nbsp;</td>
					<td class="tablebody1" align="right" nowrap="nowrap"><?= getMsg("DB_SETTINGS", $lang)?></td>
					<td class="tablebody1" width="240">&nbsp;</td>
					<td class="tablebody1" width="19">&nbsp;</td>
					<td class="tableborder1" width="1"></td>
				</tr>
				<tr>
					<td width="1" class="tableborder1"></td>
					<td class="tablebody1" width="19">&nbsp;</td>
					<td class="tablebody1" colspan=2>
						<p><input type=radio name=db_settings value=default id=default onClick="s_display(0)" <?=$_REQUEST['db_settings']=='default'?'checked':''?>><label for=default><?=getMsg("DB_DEF")?></label></p>
						<p><input type=radio name=db_settings value=env id=env onClick="s_display(0)" <?=$_REQUEST['db_settings']=='env'?'checked':''?>><label for=env><?=getMsg("DB_ENV")?></label></p>
						<p><input type=radio name=db_settings value=custom id=custom onClick="s_display(1)" <?=$_REQUEST['db_settings']=='custom'?'checked':''?>><label for=custom><?=getMsg("DB_OTHER")?></label></p>
						<div style="border:1px solid #aeb8d7;padding:5px;<?=$_REQUEST['db_settings']=='custom'?'':'display:none'?>" id=settings>
						<table width=100% cellspacing=0 cellpadding=2 border=0>
						<tr><td class="tablebody1" align=right><?= getMsg("BASE_HOST", $lang)?></td><td><input type="text" class="selectitem" name="db_host" id="db_host_id" value="<?echo (strlen($_REQUEST["db_host"])>0) ? htmlspecialchars($_REQUEST['db_host']) : "localhost"?>"></td></tr>
						<tr><td class="tablebody1" align=right><?= getMsg("USER_NAME", $lang)?></td><td><input type="text" class="selectitem" name="db_user" id="db_user_id" value="<?echo (strlen($_REQUEST["db_user"])>0) ? htmlspecialchars($_REQUEST["db_user"]) : ""?>"></td></tr>
						<tr><td class="tablebody1" align=right><?= getMsg("USER_PASS", $lang)?></td><td><input type="password" class="selectitem" name="db_pass" id="db_pass_id" value="<?echo (strlen($_REQUEST["db_pass"])>0) ? htmlspecialchars($_REQUEST["db_pass"]) : ""?>"></td></tr>
						<tr><td class="tablebody1" align=right><?= getMsg("BASE_NAME", $lang)?></td><td><input type="text" class="selectitem" name="db_name" id="db_name_id" value="<?echo (strlen($_REQUEST["db_name"])>0) ? htmlspecialchars($_REQUEST["db_name"]) : ""?>"></td></tr>
						<tr><td class="tablebody1" align=right><?= getMsg("BASE_CREATE_DB", $lang)?></td><td><input type="checkbox" name="create_db" id="create_db_id" value="Y" <? if($_REQUEST["create_db"]=="Y") echo "checked";?>></td></tr>
						</table>
						</div>
					</td>
					<td class="tablebody1" width="19">&nbsp;</td>
					<td class="tableborder1" width="1"></td>
				</tr>
				<tr>
					<td width="1" class="tableborder1"></td>
					<td class="tablebody1" width="19">&nbsp;</td>
					<td class="tablebody1" align="right" nowrap="nowrap">&nbsp;</td>
					<td class="tablebody1" width="240"><input type="button" class="selectitem" id="start_button" value="<?= getMsg("BASE_RESTORE", $lang)?>" onClick="if(document.restore.dump_name.value=='') alert('<?= getMsg("ERR_NO_DUMP", $lang)?>'); else return reloadPage(3, '<?= $lang?>');"></td>
					<td class="tablebody1" width="19">&nbsp;</td>
					<td class="tableborder1" width="1"></td>
				</tr>
				<tr>
					<td class="tableborder1" width="1"></td>
					<td colspan="4" class="tablebody1" height="5">&nbsp;</td>
					<td class="tableborder1" width="1"></td>
				</tr>

				<tr>
					<td colspan="6" class="tableborder1" height="1"></td>
				</tr>

				</table>

				<input type="hidden" name="time" id="time_id" value="<?= intVal($max_exec_time)?>">
				<input type="hidden" name="arc_name" id="arc_name_id" value="<?= $arc_name?>">
				<?
			}
			else
				echo showMsg(getMsg("EXTRACT_FINISH_TITLE", $lang), getMsg("EXTRACT_FINISH_MSG", $lang), 1);
		}
	}
	$oArc->_close();
}
elseif($Step == 3)
{
	$max_exec_time = 10;
	$d_pos = (double) $_REQUEST["d_pos"];
	if ($d_pos < 0)
		$d_pos = 0;

	if ($d_pos==0) // start
	{
		$dbconn = $_SERVER['DOCUMENT_ROOT']."/bitrix/php_interface/dbconn.php";
		if(file_exists($dbconn))
		{
			$arReplace = array(
				'DBHost' => 'db_host',
				'DBLogin' => 'db_user',
				'DBPassword' => 'db_pass',
				'DBName' => 'db_name'
			);
			include($dbconn);
			$arFile = file($dbconn);
			$bCron = false;
			foreach($arFile as $line)
			{
				if (preg_match("#^[ \t]*".'\$'."(DB[a-zA-Z]+)#",$line,$regs))
				{
					$key = $regs[1];
					$new_val = $_REQUEST[$arReplace[$key]];
					if (isset($new_val) && $$key != $new_val)
					{
						$strFile.='#'.$line.
						'$'.$key.' = "'.addslashes($new_val).'";'."\n\n";
					}
					else
						$strFile.=$line;
				}
				else
					$strFile.=$line;

				if (preg_match('#BX_CRONTAB_SUPPORT#',$line)) // почта уже на кроне
					$bCron = true;
			}

			if (defined('VM_BITRIX') && !$bCron)
				$strFile = '<'.'?define("BX_CRONTAB_SUPPORT", true);?>'.$strFile;

			$f = fopen($dbconn,"wb");
			fputs($f,$strFile);
			fclose($f);
		}
	}

	if($oDB->restore() && !$oDB->is_end())
	{
		$d_pos = $oDB->getPos();
		$oDB->close();
		$arc_name = $_REQUEST["arc_name"];
		?>
			<input type="hidden" name="time" id="time_id" value="<?= $max_exec_time?>">
			<input type="hidden" name="arc_name" id="arc_name_id" value="<?= $arc_name?>">
			<input type="hidden" name="d_pos" id="d_pos_id" value="<?= $d_pos?>">
			<input type="hidden" name="db_user" id="db_user_id" value="<?= $_REQUEST["db_user"]?>">
			<input type="hidden" name="db_pass" id="db_pass_id" value="<?= strlen($_REQUEST["db_pass"]) > 0 ? htmlspecialchars($_REQUEST["db_pass"]) : ""?>">
			<input type="hidden" name="db_name" id="db_name_id" value="<?= $_REQUEST["db_name"]?>">
			<input type="hidden" name="db_host" id="db_host_id" value="<?= $_REQUEST["db_host"]?>">
			<input type="hidden" name="dump_name" id="dump_name_id" value="<?= $_REQUEST["dump_name"]?>">
		<?
		SetCurrentProgress($d_pos,filesize($_SERVER['DOCUMENT_ROOT'].'/bitrix/backup/'.$_REQUEST['dump_name']));
		echo showMsg(getMsg('TITLE_PROCESS2'),$status);
		?>
		<script>
			reloadPage(3, '<?= $lang?>', 1);
		</script>
	<?

	}
	else
	{
		if($oDB->getError() != "")
			echo showMsg(getMsg("ERR_DUMP_RESTORE", $lang), $oDB->getError());
		else
			echo showMsg(getMsg("FINISH", $lang), getMsg("FINISH_MSG", $lang), 2);
	}
}
elseif($Step == 4)
{
	$Warn_a = !$_REQUEST['arc_name'] || unlink($_SERVER["DOCUMENT_ROOT"]."/".$_REQUEST["arc_name"]);
	$Warn_b = unlink($_SERVER["DOCUMENT_ROOT"]."/restore.php");
	@unlink($_SERVER['DOCUMENT_ROOT'].'/bitrix8setup.php');

	if(!$Warn_a || !$Warn_b)
		echo showMsg(getMsg("FINISH_ERR_DELL_TITLE", $lang), getMsg("FINISH_ERR_DELL", $lang));
	else
	{
		echo showMsg(getMsg("FINISH", $lang), getMsg("FINISH_MSG", $lang));
		echo '<script>window.setTimeout(function(){document.location="/";},3000);</script>';
	}

}
elseif($Step == 5)
{
	$Warn_a = unlink($_SERVER["DOCUMENT_ROOT"]."/".@$_REQUEST["arc_name"]);
	$Warn_b = unlink($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/backup/".@$_REQUEST["dump_name"]);
	$Warn_c = unlink($_SERVER["DOCUMENT_ROOT"]."/restore.php");
	@unlink($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/backup/".str_replace('.sql','_after_connect.sql',@$_REQUEST["dump_name"]));
	@unlink($_SERVER['DOCUMENT_ROOT'].'/bitrix8setup.php');

	if(!$Warn_a || !$Warn_b || !$Warn_c)
		echo showMsg(getMsg("FINISH_ERR_DELL_TITLE", $lang), getMsg("FINISH_ERR_DELL", $lang));
	else
	{
		echo showMsg(getMsg("FINISH", $lang), getMsg("FINISH_MSG", $lang));
		echo '<script>window.setTimeout(function(){document.location="/";},3000);</script>';
	}
}

?>
</form>
</td></tr></table>
</body>
</html>

<?
class CArchiver
{
	var $_strArchiveName = "";
	var $_bCompress = false;
	var $_strSeparator = " ";
	var $_dFile = 0;
	var $_arErrors = array();
	var $max_execution_time = 0;
	var $start = 0;

	var $end = false;
	var $end_time = false;
	var $file_pos = 0;

	function CArchiver($strArchiveName, $bCompress = false, $start, $max_execution_time, $pos)
	{
		$this->_bCompress = false;
		$this->max_execution_time = $max_execution_time;
		$this->start = $start;
		$this->file_pos = $pos;
		if (!$bCompress)
		{
			if (@file_exists($strArchiveName))
			{
				if ($fp = @fopen($strArchiveName, "rb"))
				{
					$data = fread($fp, 2);
					fclose($fp);
					if ($data == "\37\213")
					{
						$this->_bCompress = True;
					}
				}
			}
			else
			{
				if (substr($strArchiveName, -2) == 'gz')
				{
					$this->_bCompress = True;
				}
			}
		}
		else
		{
			$this->_bCompress = True;
		}

		$this->_strArchiveName = $strArchiveName;
		$this->_arErrors = array();
	}

	function GetErrors()
	{
		return $this->_arErrors;
	}

	function extractFiles($strPath)
	{
		$this->_arErrors = array();
		$v_result = true;

		if ($v_result = $this->_openRead())
		{
			if($this->file_pos > 0)
			{
				if ($this->_bCompress)
					@gzseek($this->_dFile, $this->file_pos-512);
				else
					@fseek($this->_dFile, $this->file_pos-512);
			}
			$v_result = $this->_extractList($strPath, '');
		}
		return $v_result;
	}

	function _extractList($p_path, $p_remove_path)
	{
		$v_result = true;
		$v_nb = 0;
		$v_extract_all = true;
		$v_listing = false;

		$p_path = str_replace("\\", "/", $p_path);

		if ($p_path == '' || (substr($p_path, 0, 1) != '/' && substr($p_path, 0, 3) != "../" && !strpos($p_path, ':')))
			$p_path = "./".$p_path;

		$p_remove_path = str_replace("\\", "/", $p_remove_path);

		if (($p_remove_path != '') && (substr($p_remove_path, -1) != '/'))
			$p_remove_path .= '/';

		$p_remove_path_size = strlen($p_remove_path);

		clearstatcache();

		while (strlen($v_binary_data = $this->_readBlock()) != 0)
		{
			if((getmicrotime() - $this->start) < round($this->max_execution_time * 0.8))
			{
				$v_extract_file = FALSE;

				if (!$this->_readHeader($v_binary_data, $v_header))
					return false;

				if ($v_header['filename'] == '')
					continue;

				// ----- Look for long filename
				if ($v_header['typeflag'] == 'L')
					if (!$this->_readLongHeader($v_header))
						return false;

				if (($p_remove_path != '') && (substr($v_header['filename'], 0, $p_remove_path_size) == $p_remove_path))
					$v_header['filename'] = substr($v_header['filename'], $p_remove_path_size);

				if (($p_path != './') && ($p_path != '/'))
				{
					while (substr($p_path, -1) == '/')
						$p_path = substr($p_path, 0, strlen($p_path)-1);

					if (substr($v_header['filename'], 0, 1) == '/')
						$v_header['filename'] = $p_path.$v_header['filename'];
					else
						$v_header['filename'] = $p_path.'/'.$v_header['filename'];
				}

				if (file_exists($v_header['filename']))
				{
					if ((is_dir($v_header['filename'])) && ($v_header['typeflag'] == ''))
					{
						$this->_arErrors[] = array("DIR_EXISTS", "File '".$v_header['filename']."' already exists as a directory");
						return false;
					}
					if ((is_file($v_header['filename'])) && ($v_header['typeflag'] == "5"))
					{
						$this->_arErrors[] = array("FILE_EXISTS", "Directory '".$v_header['filename']."' already exists as a file");
						return false;
					}
					if (!is_writeable($v_header['filename']))
					{
						$this->_arErrors[] = array("FILE_PERMS", "File '".$v_header['filename']."' already exists and is write protected");
						return false;
					}
				}
				elseif (($v_result = $this->_dirCheck(($v_header['typeflag'] == "5" ? $v_header['filename'] : dirname($v_header['filename'])))) != 1)
				{
					$this->_arErrors[] = array("NO_DIR", "Unable to create path for '".$v_header['filename']."'");
					return false;
				}

				if ($v_header['typeflag'] == "5")
				{
					if (!file_exists($v_header['filename']))
					{
						if (!@mkdir($v_header['filename'], BX_DIR_PERMISSIONS))
						{
							$this->_arErrors[] = array("ERR_CREATE_DIR", "Unable to create directory '".$v_header['filename']."'");
							return false;
						}
					}
				}
				else
				{
					$bSkip = false;
					if ($v_header['filename']==$_SERVER['DOCUMENT_ROOT'].'/.htaccess' && file_exists($v_header['filename']))
					{ // skip /.htaccess
						$bSkip = true;
						$n = floor($v_header['size']/512);
						for ($i = 0; $i < $n; $i++)
							$v_content = $this->_readBlock();
						if (($v_header['size'] % 512) != 0)
							$v_content = $this->_readBlock();
					}
					elseif (($v_dest_file = @fopen($v_header['filename'], "wb")) == 0)
					{
						$this->_arErrors[] = array("ERR_CREATE_FILE", "Error while opening '".$v_header['filename']."' in write binary mode");
						return false;
					}
					else
					{
						$n = floor($v_header['size']/512);
						for ($i = 0; $i < $n; $i++)
						{
							$v_content = $this->_readBlock();
							fwrite($v_dest_file, $v_content, 512);
						}
						if (($v_header['size'] % 512) != 0)
						{
							$v_content = $this->_readBlock();
							fwrite($v_dest_file, $v_content, ($v_header['size'] % 512));
						}

						@fclose($v_dest_file);
						chmod($v_header['filename'], BX_FILE_PERMISSIONS);
						touch($v_header['filename'], $v_header['mtime']);
						// To be completed
						//chmod($v_header[filename], DecOct($v_header[mode]));
					}

					clearstatcache();

					if (!$bSkip && filesize($v_header['filename']) != $v_header['size'])
					{
						$this->_arErrors[] = array("ERR_SIZE_CHECK", "Extracted file '".$v_header['filename']."' have incorrect file size '".filesize($v_filename)."' (".$v_header['size']." expected). Archive may be corrupted");
						return false;
					}
				}

				if (($v_file_dir = dirname($v_header['filename'])) == $v_header['filename'])
					$v_file_dir = '';

				if ((substr($v_header['filename'], 0, 1) == '/') && ($v_file_dir == ''))
					$v_file_dir = '/';
			}
			else
			{
				$this->end_time = true;
				return false;
			}
		}
		return true;
	}

	function _readBlock()
	{
		$v_block = "";
		if (is_resource($this->_dFile))
		{
			if ($this->_bCompress)
				$v_block = @gzread($this->_dFile, 512);
			else
				$v_block = @fread($this->_dFile, 512);
		}
		return $v_block;
	}

	function _readHeader($v_binary_data, &$v_header)
	{
		if (strlen($v_binary_data)==0)
		{
			$v_header['filename'] = '';
			return true;
		}

		if (strlen($v_binary_data) != 512)
		{
			$v_header['filename'] = '';
			$this->_arErrors[] = array("INV_BLOCK_SIZE", "Invalid block size : ".strlen($v_binary_data)."");
			return false;
		}

		$v_checksum = 0;
		for ($i = 0; $i < 148; $i++)
			$v_checksum+=ord(substr($v_binary_data, $i, 1));
		for ($i = 148; $i < 156; $i++)
			$v_checksum += ord(' ');
		for ($i = 156; $i < 512; $i++)
			$v_checksum+=ord(substr($v_binary_data, $i, 1));

		$v_data = unpack("a100filename/a8mode/a8uid/a8gid/a12size/a12mtime/a8checksum/a1typeflag/a100link/a6magic/a2version/a32uname/a32gname/a8devmajor/a8devminor", $v_binary_data);

		$v_header['checksum'] = OctDec(trim($v_data['checksum']));
		if ($v_header['checksum'] != $v_checksum)
		{
			$v_header['filename'] = '';

			if (($v_checksum == 256) && ($v_header['checksum'] == 0))
				return true;

			$this->_arErrors[] = array("INV_BLOCK_CHECK", "Invalid checksum for file '".$v_data['filename']."' : ".$v_checksum." calculated, ".$v_header['checksum']." expected");
			return false;
		}

		// ----- Extract the properties
		$v_header['filename'] = trim($v_data['filename']);
		$v_header['mode'] = OctDec(trim($v_data['mode']));
		$v_header['uid'] = OctDec(trim($v_data['uid']));
		$v_header['gid'] = OctDec(trim($v_data['gid']));
		$v_header['size'] = OctDec(trim($v_data['size']));
		$v_header['mtime'] = OctDec(trim($v_data['mtime']));
		if (($v_header['typeflag'] = $v_data['typeflag']) == "5")
			$v_header['size'] = 0;

		return true;
	}

	function _readLongHeader(&$v_header)
	{
		$v_filename = '';
		$n = floor($v_header['size']/512);
		for ($i = 0; $i < $n; $i++)
		{
			$v_content = $this->_readBlock();
			$v_filename .= $v_content;
   		}
		if (($tail = $v_header['size'] % 512) != 0)
		{
			$v_content = $this->_readBlock();
			$v_filename .= substr($v_content, 0, $tail);
		}

		$v_binary_data = $this->_readBlock();

		if (!$this->_readHeader($v_binary_data, $v_header))
			return false;

		$v_header['filename'] = $v_filename;

		return true;
	}

	function &_parseFileParams(&$vFileList)
	{
		if (isset($vFileList) && is_array($vFileList))
			return $vFileList;
		elseif (isset($vFileList) && strlen($vFileList)>0)
			return explode($this->_strSeparator, $vFileList);
		else
			return array();
	}

	function _openRead()
	{
		if (filesize($this->_strArchiveName)==0)
			return false;

		if ($this->_bCompress)
			$this->_dFile = @gzopen($this->_strArchiveName, "rb");
		else
			$this->_dFile = @fopen($this->_strArchiveName, "rb");

		if (!$this->_dFile)
		{
			$this->_arErrors[] = array("ERR_OPEN", "Unable to open '".$this->_strArchiveName."' in read mode");
			return false;
		}

		return true;
	}

	function _close()
	{
		if (is_resource($this->_dFile))
		{
			if ($this->_bCompress)
				@gzclose($this->_dFile);
			else
				@fclose($this->_dFile);

			$this->_dFile = 0;
		}

		return true;
	}

	function _normalizePath($strPath)
	{
		$strResult = "";

		if (strlen($strPath)>0)
		{
			$strPath = str_replace("\\", "/", $strPath);
			$arPath = explode('/', $strPath);

			for ($i = count($arPath)-1; $i>=0; $i--)
			{
				if ($arPath[$i] == ".")
					;
				elseif ($arPath[$i] == "..")
					$i--;
				elseif (($arPath[$i] == '') && ($i!=(count($arPath)-1)) && ($i!=0))
					;
				else
					$strResult = $arPath[$i].($i!=(count($arPath)-1) ? '/'.$strResult : '');
			}
		}
		return $strResult;
	}

	function _dirCheck($p_dir)
	{
		if ((@is_dir($p_dir)) || ($p_dir == ''))
			return true;

		$p_parent_dir = dirname($p_dir);

		if (($p_parent_dir != $p_dir) &&
			($p_parent_dir != '') &&
			(!$this->_dirCheck($p_parent_dir)))
			return false;

		if (!@mkdir($p_dir, BX_DIR_PERMISSIONS))
		{
			$this->_arErrors[] = array("CANT_CREATE_PATH", "Unable to create directory '".$p_dir."'");
			return false;
		}

		return true;
	}

	function endTime()
	{
		return $this->end_time;
	}

	function getFilePos()
	{
		if (is_resource($this->_dFile))
		{
			if ($this->_bCompress)
				return @gztell($this->_dFile);
			else
				return @ftell($this->_dFile);
		}
	}
}

class CDBRestore
{
	var $type = "";
	var $DBHost ="";
	var $DBName = "";
	var $DBLogin = "";
	var $DBPassword = "";
	var $DBdump = "";
	var $db_Conn = "";
	var $db_Error = "";
	var $f_end = false;
	var $start;
	var $d_pos;
	var $_dFile;

	function CDBRestore($DBHost, $DBName, $DBLogin, $DBPassword, $DBdump, $start, $max_exec_time, $d_pos)
	{
		$this->DBHost = $DBHost;
		$this->DBLogin = $DBLogin;
		$this->DBPassword = $DBPassword;
		$this->DBName = $DBName;
		$this->DBdump = $_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/backup/".$DBdump;
		$this->start = $start;
		$this->max_exec_time = $max_exec_time;
		$this->d_pos = $d_pos;
	}

	//Соединяется с базой данных
	function Connect()
	{

		$this->type="MYSQL";
		if (!defined("DBPersistent")) define("DBPersistent",false);
		if (DBPersistent)
		{
			$this->db_Conn = @mysql_pconnect($this->DBHost, $this->DBLogin, $this->DBPassword);
		}
		else
		{
			$this->db_Conn = @mysql_connect($this->DBHost, $this->DBLogin, $this->DBPassword);
		}
		if(!($this->db_Conn))
		{
			if (DBPersistent) $s = "mysql_pconnect"; else $s = "mysql_connect";
			if(($str_err = mysql_error()) != "")
				$this->db_Error .= "<br><font color=#ff0000>Error! ".$s."('-', '-', '-')</font><br>".$str_err."<br>";
			return false;
		}

		$after_file = str_replace('.sql','_after_connect.sql',$this->DBdump);
		if (file_exists($after_file))
		{
			$rs = fopen($after_file,'rb');
			$str = fread($rs,filesize($after_file));
			fclose($rs);
			$arSql = explode(';',$str);
			foreach($arSql as $sql)
				mysql_query($sql);
		}


		if (@$_REQUEST["create_db"]=="Y")
		{
			if(!@mysql_query("CREATE DATABASE ".@$_REQUEST["db_name"], $this->db_Conn))
			{
				$this->db_Error = getMsg("ERR_CREATE_DB", $lang).' '.mysql_error();
				return false;
			}
		}

		if(!mysql_select_db($this->DBName, $this->db_Conn))
		{
			if(($str_err = mysql_error($this->db_Conn)) != "")
				$this->db_Error = "<br><font color=#ff0000>Error! mysql_select_db($this->DBName)</font><br>".$str_err."<br>";
			return false;
		}

		return true;
	}

	function readSql()
	{
		$cache ="";

		while(!feof($this->_dFile) && (substr($cache, (strlen($cache)-2), 1) != ";"))
			$cache .= fgets($this->_dFile);

		if(!feof($this->_dFile))
			return $cache;
		else
		{
			$this->f_end = true;
			return false;
		}
	}

	function restore()
	{
		$this->_dFile = @fopen($this->DBdump, 'r');

		if($this->d_pos > 0)
			@fseek($this->_dFile, $this->d_pos);

		$sql = "";

		while(($sql = $this->readSql()) && (getmicrotime() - $this->start) < round($this->max_exec_time * 0.6))
		{
			if (defined('VM_BITRIX')) // избавимся от MyISAM
			{
				if (preg_match('#^CREATE TABLE#i',$sql))
				{
					$sql = preg_replace('#ENGINE=MyISAM#i','',$sql);
					$sql = preg_replace('#TYPE=MyISAM#i','',$sql);
				}
			}

			$result = @mysql_query($sql, $this->db_Conn);

			if(!$result)
			{
				$this->db_Error .= mysql_error();
				return false;
			}
			$sql = "";
		}

		if($sql != "")
		{
			$result = @mysql_query($sql, $this->db_Conn);

			if(!$result)
			{
				$this->db_Error .= mysql_error();
				return false;
			}
			$sql = "";
		}

		return true;
	}

	function getError()
	{
		return $this->db_Error;
	}

	function getPos()
	{
		if (is_resource($this->_dFile))
		{
			return @ftell($this->_dFile);
		}
	}

	function close()
	{
		unset($this->_dFile);
		return true;
	}

	function is_end()
	{
		return $this->f_end;
	}
}


function getmicrotime()
{
	list($usec, $sec) = explode(" ", microtime());
	return ((float)$usec + (float)$sec);
}

function getDumpList()
{
	$arDump = array();
	$handle = @opendir($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/backup");
	while (false !== ($file = @readdir($handle)))
	{
		if($file == "." || $file == "..")
			continue;

		if(is_dir($_SERVER["DOCUMENT_ROOT"]."/".$file))
			continue;

		if (strpos($file,'_after_connect.sql'))
			continue;

		if(substr($file, strlen($file) - 3, 3) == "sql")
			$arDump[] = $file;
	}

	return $arDump;
}

function getMsg($str_index, $str_lang='')
{
	global $mArr_ru, $mArr_en, $lang;
	if (!$str_lang)
		$str_lang=$lang;
	if($str_lang == "ru")
		return $mArr_ru[$str_index];
	else
		return $mArr_en[$str_index];
}

function getArcList()
{
	$arc = "";

	$handle = @opendir($_SERVER["DOCUMENT_ROOT"]);
	if (!$handle)
		return false;

	while (false !== ($file = @readdir($handle)))
	{
		if($file == "." || $file == "..")
			continue;

		if(is_dir($_SERVER["DOCUMENT_ROOT"]."/".$file))
			continue;

		if(substr($file, strlen($file) - 6, 6) == "tar.gz" || substr($file, strlen($file) - 3, 3) == "tar")
			$arc .= "<option value=\"$file\"> ".$file;
	}

	return $arc;
}

function showMsg($title, $msg, $type = 0)
{
	global $lang;

	$del_pos = "";

	if($type == 1)
	{
		$del_pos = "<tr>
			<td class=\"tableborder1\" width=\"1\"></td>
			<td colspan=\"4\" class=\"tablebody1\" height=\"5\">&nbsp;</td>
			<td class=\"tableborder1\" width=\"1\"></td>
		</tr>
		<tr>
			<td class=\"tableborder1\" width=\"1\"></td>
			<td class=\"tablebody1\" width=\"19\">&nbsp;</td>
			<td class=\"tablebody1\" colspan=\"2\" algin=\"center\" valign=\"center\">".getMsg("EXTRACT_FINISH_DELL", $lang)."</td>
			<td class=\"tablebody1\" width=\"19\">&nbsp;</td>
			<td class=\"tableborder1\" width=\"1\"></td>
		</tr>
		<tr>
			<td width=\"1\" class=\"tableborder1\"></td>
			<td class=\"tablebody1\" width=\"19\">&nbsp;</td>
			<td class=\"tablebody1\">&nbsp;</td>
			<td class=\"tablebody1\" width=\"240\"><input type=\"button\" class=\"selectitem\" id=\"del_button\" value=\"".getMsg("BUT_DELL", $lang)."\" onClick=\"reloadPage(4,'$lang');\"></td>
			<td class=\"tablebody1\" width=\"19\">&nbsp;</td>
			<td class=\"tableborder1\" width=\"1\"></td>
		</tr>";
	}
	elseif($type == 2)
	{
		$del_pos = "<tr>
			<td class=\"tableborder1\" width=\"1\"></td>
			<td colspan=\"4\" class=\"tablebody1\" height=\"5\">&nbsp;</td>
			<td class=\"tableborder1\" width=\"1\"></td>
		</tr>
		<tr>
			<td class=\"tableborder1\" width=\"1\"></td>
			<td class=\"tablebody1\" width=\"19\">&nbsp;</td>
			<td class=\"tablebody1\" colspan=\"2\" algin=\"center\" valign=\"center\">".getMsg("EXTRACT_FULL_FINISH_DELL", $lang)."</td>
			<td class=\"tablebody1\" width=\"19\">&nbsp;</td>
			<td class=\"tableborder1\" width=\"1\"></td>
		</tr>
		<tr>
			<td width=\"1\" class=\"tableborder1\"></td>
			<td class=\"tablebody1\" width=\"19\">&nbsp;</td>
			<td class=\"tablebody1\">&nbsp;</td>
			<td class=\"tablebody1\" width=\"240\"><input type=\"button\" class=\"selectitem\" id=\"del_button\" value=\"".getMsg("BUT_DELL", $lang)."\" onClick=\"reloadPage(5,'$lang');\"></td>
			<td class=\"tablebody1\" width=\"19\">&nbsp;</td>
			<td class=\"tableborder1\" width=\"1\"></td>
		</tr>";
	}


	$res = "<table width=\"600\"  border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=center>
		<tr><td colspan=\"6\" class=\"tabletitle1\" align=\"Left\" nowrap=\"nowrap\" valign=\"center\">$title</td></tr>
		<tr>
			<td colspan=\"6\" class=\"tableborder2\" height=\"1\"></td>
		</tr>
		<tr>
			<td colspan=\"6\" align=\"center\" nowrap=\"nowrap\" valign=\"center\">&nbsp;</td>
		</tr>
		<tr>
			<td colspan=\"6\" class=\"tableborder1\" height=\"1\"></td>
		</tr>
		<tr>
			<td class=\"tableborder1\" width=\"1\"></td>
			<td colspan=\"4\" class=\"tablebody1\" height=\"5\">&nbsp;</td>
			<td class=\"tableborder1\" width=\"1\"></td>
		</tr>
		<tr>
			<td class=\"tableborder1\" width=\"1\"></td>
			<td class=\"tablebody1\" width=\"19\">&nbsp;</td>
			<td class=\"tablebody1\" colspan=\"2\" algin=\"center\" valign=\"center\" id=msg>$msg</td>
			<td class=\"tablebody1\" width=\"19\">&nbsp;</td>
			<td class=\"tableborder1\" width=\"1\"></td>
		</tr>
		$del_pos
		<tr>
			<td width=\"1\" class=\"tableborder1\"></td>
			<td class=\"tablebody1\" width=\"19\">&nbsp;</td>
			<td class=\"tablebody1\">&nbsp;</td>
			<td class=\"tablebody1\" width=\"240\">&nbsp;</td>
			<td class=\"tablebody1\" width=\"19\">&nbsp;</td>
			<td class=\"tableborder1\" width=\"1\"></td>
		</tr>
		<tr>
			<td class=\"tableborder1\" width=\"1\"></td>
			<td colspan=\"4\" class=\"tablebody1\" height=\"5\">&nbsp;</td>
			<td class=\"tableborder1\" width=\"1\"></td>
		</tr>
		<tr>
			<td colspan=\"6\" class=\"tableborder1\" height=\"1\"></td>
		</tr>
		</table>
		<input type=\"hidden\" name=\"arc_name\" id=\"arc_name_id\" value=\"".@$_REQUEST["arc_name"]."\">
		<input type=\"hidden\" name=\"dump_name\" id=\"dump_name_id\" value=\"".@$_REQUEST["dump_name"]."\">";

	return $res.'<br>';
}

function LoadFile($strRequestedUrl, $strFilename, $iTimeOut = 10)
{
	global $proxyaddr, $proxyport, $strUserAgent, $strRequestedSize;

	$iTimeOut = IntVal($iTimeOut);
	if ($iTimeOut>0)
		$start_time = getmicrotime();

	$strRealUrl = $strRequestedUrl;
	$iStartSize = 0;
	$iRealSize = 0;

	$bCanContinueDownload = False;

	// ИНИЦИАЛИЗИРУЕМ, ЕСЛИ ДОКАЧКА
	$strRealUrl_tmp = "";
	$iRealSize_tmp = 0;
	if (file_exists($strFilename.".tmp") && file_exists($strFilename.".log") && filesize($strFilename.".log")>0)
	{
		$fh = fopen($strFilename.".log", "rb");
		$file_contents_tmp = fread($fh, filesize($strFilename.".log"));
		fclose($fh);

		list($strRealUrl_tmp, $iRealSize_tmp) = explode("\n", $file_contents_tmp);
		$strRealUrl_tmp = Trim($strRealUrl_tmp);
		$iRealSize_tmp = doubleval(Trim($iRealSize_tmp));
	}
	if ($iRealSize_tmp<=0 || strlen($strRealUrl_tmp)<=0)
	{
		$strRealUrl_tmp = "";
		$iRealSize_tmp = 0;

		if (file_exists($strFilename.".tmp"))
			@unlink($strFilename.".tmp");

		if (file_exists($strFilename.".log"))
			@unlink($strFilename.".log");
	}
	else
	{
		$strRealUrl = $strRealUrl_tmp;
		$iRealSize = $iRealSize_tmp;
		$iStartSize = filesize($strFilename.".tmp");
	}
	// КОНЕЦ: ИНИЦИАЛИЗИРУЕМ, ЕСЛИ ДОКАЧКА

	SetCurrentStatus(LoaderGetMessage("LOADER_LOAD_QUERY_SERVER"));

	// ИЩЕМ ФАЙЛ И ЗАПРАШИВАЕМ ИНФО
	do
	{
		SetCurrentStatus(str_replace("#DISTR#", $strRealUrl, LoaderGetMessage("LOADER_LOAD_QUERY_DISTR")));

		$lasturl = $strRealUrl;
		$redirection = "";

		$parsedurl = @parse_url($strRealUrl);
		$useproxy = (($proxyaddr != "") && ($proxyport != ""));

		if (!$useproxy)
		{
			$host = $parsedurl["host"];
			$port = $parsedurl["port"];
			$hostname = $host;
		}
		else
		{
			$host = $proxyaddr;
			$port = $proxyport;
			$hostname = $parsedurl["host"];
		}

		$port = $port ? $port : "80";

		SetCurrentStatus(str_replace("#HOST#", $host, LoaderGetMessage("LOADER_LOAD_CONN2HOST")));
		$sockethandle = @fsockopen($host, $port, $error_id, $error_msg, 30);
		if (!$sockethandle)
		{
			SetCurrentStatus(str_replace("#HOST#", $host, LoaderGetMessage("LOADER_LOAD_NO_CONN2HOST"))." [".$error_id."] ".$error_msg);
			return false;
		}
		else
		{
			if (!$parsedurl["path"])
				$parsedurl["path"] = "/";

			SetCurrentStatus(LoaderGetMessage("LOADER_LOAD_QUERY_FILE"));
			$request = "";
			if (!$useproxy)
			{
				$request .= "HEAD ".$parsedurl["path"].($parsedurl["query"] ? '?'.$parsedurl["query"] : '')." HTTP/1.0\r\n";
				$request .= "Host: $hostname\r\n";
			}
			else
			{
				$request .= "HEAD ".$strRealUrl." HTTP/1.0\r\n";
				$request .= "Host: $hostname\r\n";
			}

			if ($strUserAgent != "")
				$request .= "User-Agent: $strUserAgent\r\n";

			$request .= "\r\n";

			fwrite($sockethandle, $request);

			$result = "";
			SetCurrentStatus(LoaderGetMessage("LOADER_LOAD_WAIT"));

			$replyheader = "";
			while (($result = fgets($sockethandle, 4096)) && $result!="\r\n")
			{
				$replyheader .= $result;
			}
			fclose($sockethandle);

			$ar_replyheader = explode("\r\n", $replyheader);

			$replyproto = "";
			$replyversion = "";
			$replycode = 0;
			$replymsg = "";
			if (preg_match("#([A-Z]{4})/([0-9.]{3}) ([0-9]{3})#", $ar_replyheader[0], $regs))
			{
				$replyproto = $regs[1];
				$replyversion = $regs[2];
				$replycode = IntVal($regs[3]);
				$replymsg = substr($ar_replyheader[0], strpos($ar_replyheader[0], $replycode) + strlen($replycode) + 1, strlen($ar_replyheader[0]) - strpos($ar_replyheader[0], $replycode) + 1);
			}

			if ($replycode!=200 && $replycode!=302)
			{
				if ($replycode==403)
					SetCurrentStatus(str_replace("#ANS#", $replycode." - ".$replymsg, LoaderGetMessage("LOADER_LOAD_SERVER_ANSWER1")));
				else
					SetCurrentStatus(str_replace("#ANS#", $replycode." - ".$replymsg, LoaderGetMessage("LOADER_LOAD_SERVER_ANSWER")));
				return false;
			}

			$strLocationUrl = "";
			$iNewRealSize = 0;
			$strAcceptRanges = "";
			for ($i = 1; $i < count($ar_replyheader); $i++)
			{
				if (strpos($ar_replyheader[$i], "Location") !== false)
					$strLocationUrl = trim(substr($ar_replyheader[$i], strpos($ar_replyheader[$i], ":") + 1, strlen($ar_replyheader[$i]) - strpos($ar_replyheader[$i], ":") + 1));
				elseif (strpos($ar_replyheader[$i], "Content-Length") !== false)
					$iNewRealSize = IntVal(Trim(substr($ar_replyheader[$i], strpos($ar_replyheader[$i], ":") + 1, strlen($ar_replyheader[$i]) - strpos($ar_replyheader[$i], ":") + 1)));
				elseif (strpos($ar_replyheader[$i], "Accept-Ranges") !== false)
					$strAcceptRanges = Trim(substr($ar_replyheader[$i], strpos($ar_replyheader[$i], ":") + 1, strlen($ar_replyheader[$i]) - strpos($ar_replyheader[$i], ":") + 1));
			}

			if (strlen($strLocationUrl)>0)
			{
				$redirection = $strLocationUrl;
				$redirected = true;
				if ((strpos($redirection, "http://")===false))
					$strRealUrl = dirname($lasturl)."/".$redirection;
				else
					$strRealUrl = $redirection;
			}

			if (strlen($strLocationUrl)<=0)
				break;
		}
	}
	while (true);
	// КОНЕЦ: ИЩЕМ ФАЙЛ И ЗАПРАШИВАЕМ ИНФО

	$bCanContinueDownload = ($strAcceptRanges == "bytes");

	// ЕСЛИ НЕЛЬЗЯ ДОКАЧИВАТЬ
	if (!$bCanContinueDownload
		|| ($iRealSize>0 && $iNewRealSize != $iRealSize))
	{
	//	SetCurrentStatus(LoaderGetMessage("LOADER_LOAD_NEED_RELOAD"));
	//	$iStartSize = 0;
		die(LoaderGetMessage("LOADER_LOAD_NEED_RELOAD"));
	}
	// КОНЕЦ: ЕСЛИ НЕЛЬЗЯ ДОКАЧИВАТЬ

	// ЕСЛИ МОЖНО ДОКАЧИВАТЬ
	if ($bCanContinueDownload)
	{
		$fh = fopen($strFilename.".log", "wb");
		if (!$fh)
		{
			SetCurrentStatus(str_replace("#FILE#", $strFilename.".log", LoaderGetMessage("LOADER_LOAD_NO_WRITE2FILE")));
			return false;
		}
		fwrite($fh, $strRealUrl."\n");
		fwrite($fh, $iNewRealSize."\n");
		fclose($fh);
	}
	// КОНЕЦ: ЕСЛИ МОЖНО ДОКАЧИВАТЬ

	SetCurrentStatus(str_replace("#DISTR#", $strRealUrl, LoaderGetMessage("LOADER_LOAD_LOAD_DISTR")));
	$strRequestedSize = $iNewRealSize;

	// КАЧАЕМ ФАЙЛ
	$parsedurl = parse_url($strRealUrl);
	$useproxy = (($proxyaddr != "") && ($proxyport != ""));

	if (!$useproxy)
	{
		$host = $parsedurl["host"];
		$port = $parsedurl["port"];
		$hostname = $host;
	}
	else
	{
		$host = $proxyaddr;
		$port = $proxyport;
		$hostname = $parsedurl["host"];
	}

	$port = $port ? $port : "80";

	SetCurrentStatus(str_replace("#HOST#", $host, LoaderGetMessage("LOADER_LOAD_CONN2HOST")));
	$sockethandle = @fsockopen($host, $port, $error_id, $error_msg, 30);
	if (!$sockethandle)
	{
		SetCurrentStatus(str_replace("#HOST#", $host, LoaderGetMessage("LOADER_LOAD_NO_CONN2HOST"))." [".$error_id."] ".$error_msg);
		return false;
	}
	else
	{
		if (!$parsedurl["path"])
			$parsedurl["path"] = "/";

		SetCurrentStatus(LoaderGetMessage("LOADER_LOAD_QUERY_FILE"));

		$request = "";
		if (!$useproxy)
		{
			$request .= "GET ".$parsedurl["path"].($parsedurl["query"] ? '?'.$parsedurl["query"] : '')." HTTP/1.0\r\n";
			$request .= "Host: $hostname\r\n";
		}
		else
		{
			$request .= "GET ".$strRealUrl." HTTP/1.0\r\n";
			$request .= "Host: $hostname\r\n";
		}

		if ($strUserAgent != "")
			$request .= "User-Agent: $strUserAgent\r\n";

		if ($bCanContinueDownload && $iStartSize>0)
			$request .= "Range: bytes=".$iStartSize."-\r\n";

		$request .= "\r\n";

		fwrite($sockethandle, $request);

		$result = "";
		SetCurrentStatus(LoaderGetMessage("LOADER_LOAD_WAIT"));

		$replyheader = "";
		while (($result = fgets($sockethandle, 4096)) && $result!="\r\n")
			$replyheader .= $result;

		$ar_replyheader = explode("\r\n", $replyheader);

		$replyproto = "";
		$replyversion = "";
		$replycode = 0;
		$replymsg = "";
		if (preg_match("#([A-Z]{4})/([0-9.]{3}) ([0-9]{3})#", $ar_replyheader[0], $regs))
		{
			$replyproto = $regs[1];
			$replyversion = $regs[2];
			$replycode = IntVal($regs[3]);
			$replymsg = substr($ar_replyheader[0], strpos($ar_replyheader[0], $replycode) + strlen($replycode) + 1, strlen($ar_replyheader[0]) - strpos($ar_replyheader[0], $replycode) + 1);
		}

		if ($replycode!=200 && $replycode!=302 && $replycode!=206)
		{
			SetCurrentStatus(str_replace("#ANS#", $replycode." - ".$replymsg, LoaderGetMessage("LOADER_LOAD_SERVER_ANSWER")));
			return false;
		}

		$strContentRange = "";
		$iContentLength = 0;
		$strAcceptRanges = "";
		for ($i = 1; $i < count($ar_replyheader); $i++)
		{
			if (strpos($ar_replyheader[$i], "Content-Range") !== false)
				$strContentRange = trim(substr($ar_replyheader[$i], strpos($ar_replyheader[$i], ":") + 1, strlen($ar_replyheader[$i]) - strpos($ar_replyheader[$i], ":") + 1));
			elseif (strpos($ar_replyheader[$i], "Content-Length") !== false)
				$iContentLength = doubleval(Trim(substr($ar_replyheader[$i], strpos($ar_replyheader[$i], ":") + 1, strlen($ar_replyheader[$i]) - strpos($ar_replyheader[$i], ":") + 1)));
			elseif (strpos($ar_replyheader[$i], "Accept-Ranges") !== false)
				$strAcceptRanges = Trim(substr($ar_replyheader[$i], strpos($ar_replyheader[$i], ":") + 1, strlen($ar_replyheader[$i]) - strpos($ar_replyheader[$i], ":") + 1));
		}

		$bReloadFile = True;
		if (strlen($strContentRange)>0)
		{
			if (preg_match("# *bytes +([0-9]*) *- *([0-9]*) */ *([0-9]*)#i", $strContentRange, $regs))
			{
				$iStartBytes_tmp = doubleval($regs[1]);
				$iEndBytes_tmp = doubleval($regs[2]);
				$iSizeBytes_tmp = doubleval($regs[3]);

				if ($iStartBytes_tmp==$iStartSize
					&& $iEndBytes_tmp==($iNewRealSize-1)
					&& $iSizeBytes_tmp==$iNewRealSize)
				{
					$bReloadFile = False;
				}
			}
		}

		if ($bReloadFile)
		{
			@unlink($strFilename.".tmp");
			$iStartSize = 0;
		}

		if (($iContentLength+$iStartSize)!=$iNewRealSize)
		{
			SetCurrentStatus(LoaderGetMessage("LOADER_LOAD_ERR_SIZE"));
			return false;
		}

		$fh = fopen($strFilename.".tmp", "ab");
		if (!$fh)
		{
			SetCurrentStatus(str_replace("#FILE#", $strFilename.".tmp", LoaderGetMessage("LOADER_LOAD_CANT_OPEN_WRITE")));
			return false;
		}

		$bFinished = True;
		$downloadsize = (double) $iStartSize;
		SetCurrentStatus(LoaderGetMessage("LOADER_LOAD_LOADING"));
		while (!feof($sockethandle))
		{
			if ($iTimeOut>0 && (getmicrotime()-$start_time)>$iTimeOut)
			{
				$bFinished = False;
				break;
			}

			$result = fread($sockethandle, 40960);
			$downloadsize += strlen($result);
			if ($result=="")
				break;

			fwrite($fh, $result);
		}
		SetCurrentProgress($downloadsize,$iNewRealSize);

		fclose($fh);
		fclose($sockethandle);

		if ($bFinished)
		{
			@unlink($strFilename);
			if (!@rename($strFilename.".tmp", $strFilename))
			{
				SetCurrentStatus(str_replace("#FILE2#", $strFilename, str_replace("#FILE1#", $strFilename.".tmp", LoaderGetMessage("LOADER_LOAD_ERR_RENAME"))));
				return false;
			}
			@unlink($strFilename.".tmp");
		}
		else
			return 2;

		SetCurrentStatus(str_replace("#SIZE#", $downloadsize, str_replace("#FILE#", $strFilename, LoaderGetMessage("LOADER_LOAD_FILE_SAVED"))));
		@unlink($strFilename.".log");
		return 1;
	}
	// КОНЕЦ: КАЧАЕМ ФАЙЛ
}

function SetCurrentStatus($str)
{
	global $strLog;
	$strLog .= $str."\n";
}

function LoaderGetMessage($name)
{
	global $MESS;
	return $MESS[$name];
}

function SetCurrentProgress($cur,$total=0)
{
	global $status;
	if (!$total)
	{
		$total=100;
		$cur=0;
	}
	$val = intval($cur/$total*100);
	if ($val > 99)
		$val = 99;

	$status = '<table width=100% cellspacing=0 cellpadding=0 border=0 style="border:1px solid #aeb8d7">
	<tr>
		<td style="width:'.$val.'%;height:10px" bgcolor="#aeb8d7"></td>
		<td style="width:'.(100-$val).'%"></td>
	</tr>
	</table>
	<div align=center>'.$val.'%</div>';
}

?>
