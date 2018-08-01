<?php
/*CREATE TABLE `ws_sessions` ( 
  `session_id` varchar(255) binary NOT NULL default '', 
  `session_expires` int(10) unsigned NOT NULL default '0', 
  `session_data` text, 
  PRIMARY KEY  (`session_id`) 
) TYPE=InnoDB; */
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/components/itg/tmp.table");
class session { 
    // session-lifetime 
    var $lifeTime; 
    // mysql-handle 
    var $dbHandle; 
    function open($savePath, $sessName) { 
       // get session-lifetime 
       $this->lifeTime = get_cfg_var("session.gc_maxlifetime"); 
       // open database-connection 
       $dbHandle = @mysql_connect("server","user","password"); 
       $dbSel = @mysql_select_db("database",$dbHandle); 
       // return success 
       if(!$dbHandle || !$dbSel) 
           return false; 
       $this->dbHandle = $dbHandle; 
       return true; 
    } 
    function close() { 
        $this->gc(ini_get('session.gc_maxlifetime')); 
        // close database-connection 
        return @mysql_close($this->dbHandle); 
    } 
    function read($sessID) { 
        // fetch session-data 
        $res = mysql_query("SELECT session_data AS d FROM ws_sessions 
                            WHERE session_id = '$sessID' 
                            AND session_expires > ".time(),$this->dbHandle); 
        // return data or an empty string at failure 
        if($row = mysql_fetch_assoc($res)) 
            return $row['d']; 
        return ""; 
    } 
    function write($sessID,$sessData) { 
        // new session-expire-time 
        $newExp = time() + $this->lifeTime; 
        // is a session with this id in the database? 
        $res = mysql_query("SELECT * FROM ws_sessions 
                            WHERE session_id = '$sessID'",$this->dbHandle); 
        // if yes, 
        if(mysql_num_rows($res)) { 
            // ...update session-data 
            mysql_query("UPDATE ws_sessions 
                         SET session_expires = '$newExp', 
                         session_data = '$sessData' 
                         WHERE session_id = '$sessID'",$this->dbHandle); 
            // if something happened, return true 
            if(mysql_affected_rows($this->dbHandle)) 
                return true; 
        } 
        // if no session-data was found, 
        else { 
            // create a new row 
            mysql_query("INSERT INTO ws_sessions ( 
                         session_id, 
                         session_expires, 
                         session_data) 
                         VALUES( 
                         '$sessID', 
                         '$newExp', 
                         '$sessData')",$this->dbHandle); 
            // if row was created, return true 
            if(mysql_affected_rows($this->dbHandle)) 
                return true; 
        } 
        // an unknown error occured 
        return false; 
    } 
    function destroy($sessID) { 
        // delete session-data 
        mysql_query("DELETE FROM ws_sessions WHERE session_id = '$sessID'",$this->dbHandle); 
        // if session was deleted, return true, 
        if(mysql_affected_rows($this->dbHandle)) 
        {
        	CitgTempTable::Drop('itgTempTable'.$sessID);
            return true; 
        }
        // ...else return false 
        return false; 
    } 
    function gc($sessMaxLifeTime) { 
    	
        // delete old sessions 
        $result = mysql_query("SELECT session_id FROM ws_sessions WHERE session_expires < ".time(),$this->dbHandle);
        while ($row = mysql_fetch_row($result))
        {
        	CitgTempTable::Drop('itgTempTable'.$row[0]);
        }
        mysql_query("DELETE FROM ws_sessions WHERE session_expires < ".time(),$this->dbHandle); 
        // return affected rows 
        return mysql_affected_rows($this->dbHandle); 
    } 
} 
$session = new session(); 
session_set_save_handler(array(&$session,"open"), 
                         array(&$session,"close"), 
                         array(&$session,"read"), 
                         array(&$session,"write"), 
                         array(&$session,"destroy"), 
                         array(&$session,"gc")); 
session_start(); 