<?
$MESS ['MAIN_DUMP_FILE_CNT'] = "Файлов сжато:";
$MESS ['MAIN_DUMP_FILE_SIZE'] = "Размер файлов:";
$MESS ['MAIN_DUMP_FILE_FINISH'] = "Создание резервной копии завершено";
$MESS ['MAIN_DUMP_FILE_MAX_SIZE'] = "Исключить из архива файлы размером более (0 - без ограничения):";
$MESS ['MAIN_DUMP_FILE_STEPPED'] = "Архивировать по шагам:";
$MESS ['MAIN_DUMP_FILE_STEP'] = "Шаг:";
$MESS ['MAIN_DUMP_FILE_STEP_SLEEP'] = "интервал:";
$MESS ['MAIN_DUMP_FILE_STEP_sec'] = "секунд";
$MESS ['MAIN_DUMP_FILE_MAX_SIZE_kb'] = "кб ";
$MESS ['MAIN_DUMP_FILE_DUMP_BUTTON'] = "Архивировать";
$MESS ['MAIN_DUMP_FILE_STOP_BUTTON'] = "Остановить";
$MESS ['MAIN_DUMP_FILE_KERNEL'] = "Архивировать ядро:";
$MESS ['MAIN_DUMP_FILE_NAME'] = "Имя";
$MESS ['MAIN_DUMP_FILE_SIZE_FIELD'] = "Размер файла, МБ";
$MESS ['MAIN_DUMP_FILE_TIMESTAMP'] = "Изменен";
$MESS ['MAIN_DUMP_FILE_PUBLIC'] = "Архивировать публичную часть:";
$MESS ['MAIN_DUMP_FILE_TITLE'] = "Файлы";
$MESS ['MAIN_DUMP_BASE_STAT'] = "статистику";
$MESS ['MAIN_DUMP_BASE_SINDEX'] = "поисковый индекс";
$MESS ['MAIN_DUMP_BASE_IGNORE'] = "Исключить из архива:";
$MESS ['MAIN_DUMP_BASE_TRUE'] = "Архивировать базу данных:";
$MESS ['MAIN_DUMP_BASE_TITLE'] = "База Данных";
$MESS ['MAIN_DUMP_BASE_SIZE'] = "МБ";
$MESS ['MAIN_DUMP_PAGE_TITLE'] = "Резервное копирование";
$MESS ['MAIN_DUMP_TAB'] = "Копирование";
$MESS ['MAIN_DUMP_TAB_TITLE'] = "Параметры резервного копирования";
$MESS ['MAIN_DUMP_SITE_PROC'] = "Сжатие...";
$MESS ['MAIN_DUMP_ARC_SIZE'] = "Размер архива:";
$MESS ['MAIN_DUMP_TABLE_FINISH'] = "Обработано таблиц:";
$MESS ['MAIN_DUMP_ACTION_DOWNLOAD'] = "Скачать";
$MESS ['MAIN_DUMP_DELETE'] = "Удалить";
$MESS ['MAIN_DUMP_ACTIONS'] = "Действие";
$MESS ['MAIN_DUMP_ALERT_DELETE'] = "Вы уверены, что хотите удалить файл?";
$MESS ['MAIN_DUMP_FILE_PAGES'] = "Резервные копии";
$MESS ['MAIN_DUMP_FOOTER_MSG'] = "Для переноса архива на другую систему или для восстановления системы из архива воспользуйтесь скриптом для восстановления:";
$MESS ['MAIN_DUMP_GET_SCRIPT'] = "Скачать";
$MESS ['MAIN_RIGHT_CONFIRM_EXECUTE'] = "Внимание! Распаковка резервной копии на действующем сайте может привести к повреждению сайта! Продолжить?";
$MESS ['MAIN_DUMP_RESTORE'] = "Распаковать";
$MESS ['MAIN_DUMP_ENCODE'] = "Внимание! Вы используете закодированную версию продукта";
$MESS ['MAIN_DUMP_ENCODE_DETAILS'] = "При переносе на другой хостинг там должен быть установлен PHP версии <b>#VER#.x.x</b> и <b>Zend Optimizer 3.3</b> или выше.<br>Если у вас есть действующий коммерческий ключ, скачайте исходные коды через <a href=\"/bitrix/admin/sysupdate.php\">систему обновлений</a>.";
$MESS ['MAIN_DUMP_MYSQL_ONLY'] = "Система резервного копирования работает только с базой данных MySQL.<br> Пожалуйста, используйте внешние инструменты для создания архива базы данных.";
$MESS ['MAIN_DUMP_HEADER_MSG'] = "Для переноса архива сайта на другой хостинг поместите в корневой папке нового сайта скрипт для восстановления <a href='/bitrix/admin/restore_export.php'>restore.php</a> и сам архив, затем наберите в строке браузера &quot;&lt;имя сайта&gt;/restore.php&quot; и следуйте инструкциям по распаковке.<br>Подробная инструкция доступна в <a href='http://dev.1c-bitrix.ru/api_help/main/going_remote.php' target=_blank>разделе справки</a>.";
$MESS ['MAIN_DUMP_SKIP_SYMLINKS'] = "Пропускать символические ссылки на директории:";
$MESS ['MAIN_DUMP_MASK'] = "Исключить из архива файлы и директории по маске:";
$MESS ['MAIN_DUMP_MORE'] = "Ещё...";
$MESS ['MAIN_DUMP_LOADING'] = "Загрузка...";
$MESS ['MAIN_DUMP_FOOTER_MASK'] = "Для маски исключения действуют следующие правила:
	<p>
	<li>шаблон маски может содержать символы &quot;*&quot;, которые соответствуют любому количеству любых символов в имени файла или папки;</li>
	<li>если в начале стоит косая черта (&quot;/&quot; или &quot;\\&quot;), путь считается от корня сайта;</li>
	<li>в противном случае шаблон применяется к каждому файлу или папке;</li>
	<p>Примеры шаблонов:</p>
	<li>/content/photo - исключить целиком папку /content/photo;</li>
	<li>*.zip - исключить файлы с расширением &quot;zip&quot;;</li>
	<li>.access.php - исключить все файлы &quot;.access.php&quot;;</li>
	<li>/files/download/*.zip - исключить файлы с расширением &quot;zip&quot; в директории /files/download;</li>
	<li>/files/d*/*.ht* - исключить файлы из директорий, начинающихся на &quot;/files/d&quot;  с расширениями, начинающимися на &quot;ht&quot;.</li>
	";
$MESS ['MAIN_DUMP_DB_NOTE'] = "Обратите внимание, что при обновлении продукта меняются  файлы ядра и структура базы данных: если версия ядра не будет соответствовать версии базы данных, сайт может оказаться неработоспособным.";
?>
