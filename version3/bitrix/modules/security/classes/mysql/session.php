<?
global $SECURITY_SESSION_DBH;
$SECURITY_SESSION_DBH = false;
class CSecuritySessionDB
{
	function CurrentTimeFunction()
	{
		return "now()";
	}

	function SecondsAgo($sec)
	{
		return "DATE_ADD(now(), INTERVAL - ".intval($sec)." SECOND)";
	}

	function Query($strSql, $error_position)
	{
		global $SECURITY_SESSION_DBH;
		if(is_resource($SECURITY_SESSION_DBH))
		{
			$result = @mysql_query($strSql, $SECURITY_SESSION_DBH);
			if($result)
			{
				return $result;
			}
			else
			{
				$db_Error = mysql_error();
				AddMessage2Log($error_position." MySql Query Error: ".$strSql." [".$db_Error."]", "security");
			}
		}
		return false;
	}

	function QueryBind($strSql, $arBinds, $error_position)
	{
		foreach($arBinds as $key => $value)
			$strSql = str_replace(":".$key, "'".$value."'", $strSql);
		return CSecuritySessionDB::Query($strSql, $error_position);
	}

	function Fetch($result)
	{
		if($result)
			return mysql_fetch_array($result, MYSQL_ASSOC);
		else
			return false;
	}

	function Init()
	{
		global $DB, $SECURITY_SESSION_DBH;
		if(!is_resource($SECURITY_SESSION_DBH))
		{
			$DB->DoConnect();
			$SECURITY_SESSION_DBH = $DB->db_Conn;
		}
	}
}
?>