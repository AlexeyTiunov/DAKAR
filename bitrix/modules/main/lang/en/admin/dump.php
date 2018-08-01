<?
$MESS ['MAIN_DUMP_FILE_CNT'] = "Files compressed:";
$MESS ['MAIN_DUMP_FILE_SIZE'] = "Files size:";
$MESS ['MAIN_DUMP_FILE_FINISH'] = "Backup completed";
$MESS ['MAIN_DUMP_FILE_MAX_SIZE'] = "Do not include files which size exceeds (0 - no limit): ";
$MESS ['MAIN_DUMP_FILE_STEPPED'] = "Back up gradually:";
$MESS ['MAIN_DUMP_FILE_STEP'] = "Step:";
$MESS ['MAIN_DUMP_FILE_STEP_SLEEP'] = "interval:";
$MESS ['MAIN_DUMP_FILE_STEP_sec'] = "sec";
$MESS ['MAIN_DUMP_FILE_MAX_SIZE_kb'] = "kB";
$MESS ['MAIN_DUMP_FILE_DUMP_BUTTON'] = "Back up";
$MESS ['MAIN_DUMP_FILE_STOP_BUTTON'] = "Stop";
$MESS ['MAIN_DUMP_FILE_KERNEL'] = "Back up kernel files:";
$MESS ['MAIN_DUMP_FILE_NAME'] = "Filename";
$MESS ['MAIN_DUMP_FILE_SIZE_FIELD'] = "File size, Mb";
$MESS ['MAIN_DUMP_FILE_TIMESTAMP'] = "Modified";
$MESS ['MAIN_DUMP_FILE_PUBLIC'] = "Back up public files:";
$MESS ['MAIN_DUMP_FILE_TITLE'] = "Files";
$MESS ['MAIN_DUMP_BASE_STAT'] = "statistics";
$MESS ['MAIN_DUMP_BASE_SINDEX'] = "search index";
$MESS ['MAIN_DUMP_BASE_IGNORE'] = "Exclude from archive:";
$MESS ['MAIN_DUMP_BASE_TRUE'] = "Back up database:";
$MESS ['MAIN_DUMP_BASE_TITLE'] = "Database";
$MESS ['MAIN_DUMP_BASE_SIZE'] = "Mb";
$MESS ['MAIN_DUMP_PAGE_TITLE'] = "Backup";
$MESS ['MAIN_DUMP_TAB'] = "Backup";
$MESS ['MAIN_DUMP_TAB_TITLE'] = "Backup settings";
$MESS ['MAIN_DUMP_SITE_PROC'] = "Compressing...";
$MESS ['MAIN_DUMP_ARC_SIZE'] = "Archive size:";
$MESS ['MAIN_DUMP_TABLE_FINISH'] = "Tables processed:";
$MESS ['MAIN_DUMP_ACTION_DOWNLOAD'] = "Download";
$MESS ['MAIN_DUMP_DELETE'] = "Delete";
$MESS ['MAIN_DUMP_ACTIONS'] = "Action";
$MESS ['MAIN_DUMP_ALERT_DELETE'] = "Are you sure you want to delete file?";
$MESS ['MAIN_DUMP_FILE_PAGES'] = "Backup copies";
$MESS ['MAIN_DUMP_FOOTER_MSG'] = "To transfer archive to the other place and for restoring system from the archive use special restoring script: ";
$MESS ['MAIN_DUMP_GET_SCRIPT'] = "Download";
$MESS ['MAIN_RIGHT_CONFIRM_EXECUTE'] = "Attention! Unpacking the backup copy on the working site can corrupt the site! Continue?";
$MESS ['MAIN_DUMP_RESTORE'] = "Unpack";
$MESS ['MAIN_DUMP_ENCODE'] = "Attention! You are using encoded product version.";
$MESS ['MAIN_DUMP_ENCODE_DETAILS'] = "When you move the site(s) to another server, it must have PHP version
<b>#VER#.x.x</b> and <b>Zend Optimizer 3.3</b> or higher installed.<br>If you have a valid license key, download source codes using the <a href=\"/bitrix/admin/sysupdate.php\">update system</a>.";
$MESS ['MAIN_DUMP_MYSQL_ONLY'] = "The backup feature supports MySQL databases only.<br>Please use external tools to create the database copy.";
$MESS ['MAIN_DUMP_HEADER_MSG'] = "To move the site back-up archive to another server, copy the restore script <a href='/bitrix/admin/restore_export.php'>restore.php</a> and the archive file to the root directory of the new server. Then, type in your browser: <b>&lt;site name&gt;/restore.php</b>.";
$MESS ['MAIN_DUMP_SKIP_SYMLINKS'] = "Skip Symbolic Links to Directories:";
$MESS ['MAIN_DUMP_MASK'] = "Exclude Files and Folders (mask):";
$MESS ['MAIN_DUMP_MORE'] = "More...";
$MESS ['MAIN_DUMP_LOADING'] = "Loading...";
$MESS ['MAIN_DUMP_FOOTER_MASK'] = "The following rules apply to exclusion masks:
 <p>
 <li>the mask can contain asterisks &quot;*&quot; that match any or none characters in the file or folder name;</li>
 <li>if a path starts with a slash or a backslash (&quot;/&quot; or &quot;\\&quot;), the path is relative to the site root;</li>
 <li>otherwise, the mask applies to each file and folder;</li>
 <p>Examples of templates:</p>
 <li>/content/photo - excludes the folder/content/photo;</li>
 <li>*.zip - excludes ZIP files (the ones with the &quot;zip&quot; extension);</li>
 <li>.access.php - excludes all files &quot;.access.php&quot;;</li>
 <li>/files/download/*.zip - excludes ZIP files in /files/download;</li>
 <li>/files/d*/*.ht* - excludes files with extensions starting with &quot;ht&quot; in directories starting with &quot;/files/d&quot;.</li>";
$MESS ['MAIN_DUMP_DB_NOTE'] = "Note that the update procedure changes the system kernel files and can alter the database structure. If the system kernel version does not match the database version, your site can fail to run.";
?>
