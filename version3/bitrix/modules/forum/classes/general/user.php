<?
IncludeModuleLangFile(__FILE__); 
/**********************************************************************/
/************** FORUM USER ********************************************/
/**********************************************************************/
class CAllForumUser
{
	//---------------> User insert, update, delete
	function IsLocked($USER_ID)
	{
		global $DB, $CACHE_MANAGER, $aForumPermissions;
		$USER_ID = intVal($USER_ID);
		if ($USER_ID <= 0)
			return false;
		$cache_id = "b_forum_user_locked";

		if (!array_key_exists("LOCKED_USERS", $GLOBALS["FORUM_CACHE"]))
		{
			if (CACHED_b_forum_user !== false && $CACHE_MANAGER->Read(CACHED_b_forum_user, $cache_id, "b_forum_user"))
			{
				$GLOBALS["FORUM_CACHE"]["LOCKED_USERS"] = $CACHE_MANAGER->Get($cache_id);
			}
			else
			{
				$arRes = array();
				$strSql = "SELECT ID, USER_ID FROM b_forum_user WHERE ALLOW_POST != 'Y' ORDER BY ID ASC";
				$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
				if ($db_res && $res = $db_res->Fetch())
				{
					do 
					{
						$arRes[intVal($res["USER_ID"])] = $res;
					} while ($res = $db_res->Fetch());
				}
				
				$GLOBALS["FORUM_CACHE"]["LOCKED_USERS"] = $arRes;
				if (CACHED_b_forum_user !== false)
					$CACHE_MANAGER->Set($cache_id, $GLOBALS["FORUM_CACHE"]["LOCKED_USERS"]);
			}
		}
		return array_key_exists($USER_ID, $GLOBALS["FORUM_CACHE"]["LOCKED_USERS"]);
	}

	function CanUserAddUser($arUserGroups)
	{
		return True;
	}

	function CanUserUpdateUser($ID, $arUserGroups, $CurrentUserID = 0)
	{
		$ID = intVal($ID);
		$CurrentUserID = intVal($CurrentUserID);
		if (in_array(1, $arUserGroups)) return True;
		$arUser = CForumUser::GetByID($ID);
		if ($arUser && intVal($arUser["USER_ID"]) == $CurrentUserID) return True;
		return False;
	}

	function CanUserDeleteUser($ID, $arUserGroups)
	{
		$ID = intVal($ID);
		if (in_array(1, $arUserGroups)) return True;
		return False;
	}

	function CheckFields($ACTION, &$arFields, $ID=false)
	{
		$aMsg = array();
		// Checking user for updating or adding	
		// USER_ID as value
		if ((is_set($arFields, "USER_ID") || $ACTION=="ADD") && intVal($arFields["USER_ID"]) <= 0)
		{
			$aMsg[] = array(
				"id" => 'EMPTY_USER_ID', 
				"text" => GetMessage("F_GL_ERR_EMPTY_USER_ID"));
		}
		elseif (is_set($arFields, "USER_ID"))
		{
			$db_res = CUser::GetByID($arFields["USER_ID"]);
			if (!$db_res->Fetch())
			{
				$aMsg[] = array(
					"id" => 'USER_IS_NOT_EXIST', 
					"text" => GetMessage("F_GL_ERR_USER_NOT_EXIST", array("#UID#" => htmlspecialchars($arFields["USER_ID"]))));
			}
			
			$res = CForumUser::GetByUSER_ID(intVal($arFields["USER_ID"]));
			
			if ($ACTION == "ADD" && intVal($res["ID"]) > 0)
			{
				$aMsg[] = array(
					"id" => 'USER_IS_EXIST', 
					"text" => GetMessage("F_GL_ERR_USER_IS_EXIST", array("#UID#" => htmlspecialchars($arFields["USER_ID"]))));
			}
			elseif ($ACTION == "UPDATE")
			{
				unset($arFields["USER_ID"]);
			}
		}
		// last visit
		if (is_set($arFields, "LAST_VISIT"))
		{
			$arFields["LAST_VISIT"] = trim($arFields["LAST_VISIT"]);
			if (strLen($arFields["LAST_VISIT"]) > 0)
			{
				if ($arFields["LAST_VISIT"] != $GLOBALS["DB"]->GetNowFunction() && !$GLOBALS["DB"]->IsDate($arFields["LAST_VISIT"], false, SITE_ID, "FULL"))
					$aMsg[] = array(
						"id" => 'LAST_VISIT', 
						"text" => GetMessage("F_GL_ERR_LAST_VISIT"));
			}
			else
			{
				unset($arFields["LAST_VISIT"]);
			}
		}
		// date registration
		if (is_set($arFields, "DATE_REG"))
		{
			$arFields["DATE_REG"] = trim($arFields["DATE_REG"]);
			if (strLen($arFields["DATE_REG"]) > 0)
			{
				if ($arFields["DATE_REG"] != $GLOBALS["DB"]->GetNowFunction() && !$GLOBALS["DB"]->IsDate($arFields["DATE_REG"], false, SITE_ID, "SHORT"))
				{
					$aMsg[] = array(
						"id" => 'DATE_REG', 
						"text" => GetMessage("F_GL_ERR_DATE_REG"));
				}
			}
			else
			{
				unset($arFields["DATE_REG"]);
			}
		}
		// avatar
		if (is_set($arFields, "AVATAR") && strLen($arFields["AVATAR"]["name"]) <= 0 && strLen($arFields["AVATAR"]["del"]) <= 0)
		{
			unset($arFields["AVATAR"]);
		}
		if (is_set($arFields, "AVATAR"))
		{
			$max_size = COption::GetOptionInt("forum", "avatar_max_size", 10000);
			$max_width = COption::GetOptionInt("forum", "avatar_max_width", 90);
			$max_height = COption::GetOptionInt("forum", "avatar_max_height", 90);
			$res = CFile::CheckImageFile($arFields["AVATAR"], $max_size, $max_width, $max_height);
			if (strLen($res) > 0)
			{
				$aMsg[] = array(
					"id" => 'AVATAR', 
					"text" => $res);
			}
		}
		
		if (!empty($aMsg))
		{
			$e = new CAdminException(array_reverse($aMsg));
			$GLOBALS["APPLICATION"]->ThrowException($e);
			return false;
		}
		
		// show name
		if (is_set($arFields, "SHOW_NAME") || $ACTION == "ADD")
		{
			if (empty($arFields["SHOW_NAME"]))
				$arFields["SHOW_NAME"] = COption::GetOptionString("forum", "USER_SHOW_NAME", "Y") == "Y" ? "Y" : "N";
			$arFields["SHOW_NAME"] = ($arFields["SHOW_NAME"] == "N" ? "N" : "Y");
		}
		// allow post
		if (is_set($arFields, "ALLOW_POST") || $ACTION=="ADD")
		{
			$arFields["ALLOW_POST"] = ($arFields["ALLOW_POST"] == "N" ? "N" : "Y");
		}
		return True;
	}

	function Add($arFields, $strUploadDir = false)
	{
		global $DB;
		$arBinds = Array();
		$strUploadDir = ($strUploadDir === false ? "forum/avatar" : $strUploadDir);
		
		if (!CForumUser::CheckFields("ADD", $arFields))
			return false;
/***************** Event onBeforeUserAdd ***************************/
		$events = GetModuleEvents("forum", "onBeforeUserAdd");
		while ($arEvent = $events->Fetch())
			ExecuteModuleEventEx($arEvent, array(&$arFields));
/***************** /Event ******************************************/
		if (empty($arFields))
			return false;
/***************** Cleaning cache **********************************/
		if (is_set($arFields, "ALLOW_POST") && $arFields["ALLOW_POST"] != "Y")
		{
			unset($GLOBALS["FORUM_CACHE"]["LOCKED_USERS"]);
			if (CACHED_b_forum_user !== false)
			$GLOBALS["CACHE_MANAGER"]->CleanDir("b_forum_user");
		}
/***************** Cleaning cache/**********************************/
		if (!is_set($arFields, "LAST_VISIT"))
			$arFields["~LAST_VISIT"] = $DB->GetNowFunction();
		if (!is_set($arFields, "DATE_REG"))
			$arFields["~DATE_REG"] = $DB->GetNowFunction();
		if (is_set($arFields, "INTERESTS"))
			$arBinds["INTERESTS"] = $arFields["INTERESTS"];

		CFile::SaveForDB($arFields, "AVATAR", $strUploadDir);

		$ID = $DB->Add("b_forum_user", $arFields, $arBinds);
/***************** Event onAfterUserAdd ****************************/
		$events = GetModuleEvents("forum", "onAfterUserAdd");
		while ($arEvent = $events->Fetch())
			ExecuteModuleEventEx($arEvent, array($ID, $arFields));
/***************** /Event ******************************************/
		return $ID;
	}

	function Update($ID, $arFields, $strUploadDir = false, $UpdateByUserId = false)
	{
		global $DB;
		$ID = intVal($ID);
		if ($ID <= 0):
			return false;
		endif;
		$strUploadDir = ($strUploadDir === false ? "forum/avatar" : $strUploadDir);
		$arFields1 = array();
		
		foreach ($arFields as $key => $value)
		{
			if (substr($key, 0, 1)=="=")
			{
				$arFields1[substr($key, 1)] = $value;
				unset($arFields[$key]);
			}
		}
		
		if (!CForumUser::CheckFields("UPDATE", $arFields))
			return false;
		CFile::SaveForDB($arFields, "AVATAR", $strUploadDir);
/***************** Event onBeforeUserUpdate ************************/
		$events = GetModuleEvents("forum", "onBeforeUserUpdate");
		while ($arEvent = $events->Fetch())
			ExecuteModuleEventEx($arEvent, array(&$ID, &$arFields));
/***************** /Event ******************************************/
		if (empty($arFields) && empty($arFields1))
			return false;
/***************** Cleaning cache **********************************/
		if (is_set($arFields, "ALLOW_POST"))
		{
			unset($GLOBALS["FORUM_CACHE"]["LOCKED_USERS"]);
			if (CACHED_b_forum_user !== false)
				$GLOBALS["CACHE_MANAGER"]->CleanDir("b_forum_user");
		}
/***************** Cleaning cache/**********************************/
		$strUpdate = $DB->PrepareUpdate("b_forum_user", $arFields);

		foreach ($arFields1 as $key => $value)
		{
			if (strLen($strUpdate)>0) $strUpdate .= ", ";
			$strUpdate .= $key."=".$value." ";
		}
		if (!$UpdateByUserId)
			$strSql = "UPDATE b_forum_user SET ".$strUpdate." WHERE ID = ".$ID;
		else 
			$strSql = "UPDATE b_forum_user SET ".$strUpdate." WHERE USER_ID = ".$ID;
		$arBinds = Array();

		if (is_set($arFields, "INTERESTS"))
			$arBinds["INTERESTS"] = $arFields["INTERESTS"];
		$DB->QueryBind($strSql, $arBinds);
/***************** Event onAfterUserUpdate *************************/
		$events = GetModuleEvents("forum", "onAfterUserUpdate");
		while ($arEvent = $events->Fetch())
			ExecuteModuleEventEx($arEvent, array($ID, $arFields));
/***************** /Event ******************************************/
		unset($GLOBALS["FORUM_CACHE"]["USER"]);
		unset($GLOBALS["FORUM_CACHE"]["USER_ID"]);
		
		return $ID;
	}

	function Delete($ID)
	{
		global $DB;
		$ID = intVal($ID);
		if ($ID <= 0):
			return false;
		endif;
/***************** Event onBeforeUserDelete ************************/
		$events = GetModuleEvents("forum", "onBeforeUserDelete");
		while ($arEvent = $events->Fetch())
			ExecuteModuleEventEx($arEvent, array(&$ID));
/***************** /Event ******************************************/
		$strSql = "SELECT F.ID FROM b_forum_user FU, b_file F WHERE FU.ID = ".$ID." AND FU.AVATAR = F.ID ";
		$z = $DB->Query($strSql, false, "FILE: ".__FILE__." LINE:".__LINE__);
		while ($zr = $z->Fetch())
			CFile::Delete($zr["ID"]);

		$arForumUser = CForumUser::GetByID($ID);
		$res = $DB->Query("DELETE FROM b_forum_user WHERE ID = ".$ID, True);
/***************** Event onAfterUserDelete *************************/
		$events = GetModuleEvents("forum", "onAfterUserDelete");
		while ($arEvent = $events->Fetch())
			ExecuteModuleEventEx($arEvent, array($ID));
/***************** /Event ******************************************/
		unset($GLOBALS["FORUM_CACHE"]["USER"][$ID]);
		unset($GLOBALS["FORUM_CACHE"]["USER_ID"][$arForumUser["USER_ID"]]);
		return $res;
	}

	function CountUsers($bActive = False, $arFilter = array())
	{
		global $DB;
		$arFilter = (is_array($arFilter) ? $arFilter : array());
		$arSqlSearch = array();
		$strSqlSearch = "";
		if ($bActive)
			$arSqlSearch[] = "NUM_POSTS > 0";
		foreach ($arFilter as $key => $val)
		{
			$key_res = CForumNew::GetFilterOperation($key);
			$key = strtoupper($key_res["FIELD"]);
			$strNegative = $key_res["NEGATIVE"];
			$strOperation = $key_res["OPERATION"];

			switch ($key)
			{
				case "ACTIVE":
					if (strlen($val)<=0)
						$arSqlSearch[] = ($strNegative=="Y"?"NOT":"")."(U.".$key." IS NULL OR ".($DB->type == "MSSQL" ? "LEN" : "LENGTH")."(U.".$key.")<=0)";
					else
						$arSqlSearch[] = ($strNegative=="Y"?" U.".$key." IS NULL OR NOT (":"")."U.".$key." ".$strOperation." '".$DB->ForSql($val)."'".
							($strNegative=="Y"?")":"");
					break;
			}
		}
		if (count($arSqlSearch) > 0)
			$strSqlSearch = " WHERE (".implode(") AND (", $arSqlSearch).") ";

		$strSql = "SELECT COUNT(FU.ID) AS CNT FROM b_forum_user FU INNER JOIN b_user U ON (U.ID = FU.USER_ID)".$strSqlSearch;
		$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		if ($ar_res = $db_res->Fetch())
			return $ar_res["CNT"];

		return 0;
	}

	function GetByID($ID)
	{
		global $DB;

		$ID = intVal($ID);
		if (isset($GLOBALS["FORUM_CACHE"]["USER"][$ID]) && is_array($GLOBALS["FORUM_CACHE"]["USER"][$ID]) && is_set($GLOBALS["FORUM_CACHE"]["USER"][$ID], "ID"))
		{
			return $GLOBALS["FORUM_CACHE"]["USER"][$ID];
		}
		else
		{
			$strSql = 
				"SELECT FU.ID, FU.USER_ID, FU.SHOW_NAME, FU.DESCRIPTION, FU.IP_ADDRESS, 
					FU.REAL_IP_ADDRESS, FU.AVATAR, FU.NUM_POSTS, FU.POINTS as NUM_POINTS, FU.INTERESTS, 
					FU.HIDE_FROM_ONLINE, FU.SUBSC_GROUP_MESSAGE, FU.SUBSC_GET_MY_MESSAGE, 
					FU.LAST_POST, FU.ALLOW_POST, FU.SIGNATURE, FU.RANK_ID, FU.POINTS, 
					".$DB->DateToCharFunction("FU.DATE_REG", "SHORT")." as DATE_REG, 
					".$DB->DateToCharFunction("FU.LAST_VISIT", "FULL")." as LAST_VISIT 
				FROM b_forum_user FU 
				WHERE FU.ID = ".$ID;
			$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			if ($res = $db_res->Fetch())
			{
				$GLOBALS["FORUM_CACHE"]["USER"][$ID] = $res;
				return $res;
			}
		}
		return False;
	}

	function GetByLogin($Name)
	{
		global $DB;
		$Name = $DB->ForSql(trim($Name));
		if (
			isset($GLOBALS["FORUM_CACHE"]["USER_NAME"]) && 
			is_set($GLOBALS["FORUM_CACHE"]["USER_NAME"], $Name) && 
			is_array($GLOBALS["FORUM_CACHE"]["USER_NAME"][$Name]) && 
			is_set($GLOBALS["FORUM_CACHE"]["USER_NAME"][$Name], "ID"))
		{
			return $GLOBALS["FORUM_CACHE"]["USER_NAME"][$Name];
		}
		else
		{
			$strSql = 
				"SELECT ID AS USER_ID 
				FROM b_user
				WHERE LOGIN='".$Name."'";
			$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			$res = $db_res->Fetch();
			if (!empty($res["USER_ID"]))
			{
				$strSql = 
					"SELECT FU.ID, FU.USER_ID, FU.SHOW_NAME, FU.DESCRIPTION, FU.IP_ADDRESS, 
						FU.REAL_IP_ADDRESS, FU.AVATAR, FU.NUM_POSTS, FU.POINTS as NUM_POINTS, 
						FU.INTERESTS, FU.HIDE_FROM_ONLINE, FU.SUBSC_GROUP_MESSAGE, FU.SUBSC_GET_MY_MESSAGE, 
						FU.LAST_POST, FU.ALLOW_POST, FU.SIGNATURE, FU.RANK_ID, FU.POINTS, 
						".$DB->DateToCharFunction("FU.DATE_REG", "SHORT")." as DATE_REG, 
						".$DB->DateToCharFunction("FU.LAST_VISIT", "FULL")." as LAST_VISIT 
					FROM b_forum_user FU 
					WHERE FU.USER_ID = ".$res["USER_ID"];
				$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
				if ($res = $db_res->Fetch())
				{
					$GLOBALS["FORUM_CACHE"]["USER"][$ID] = $res;
					$GLOBALS["FORUM_CACHE"]["USER_NAME"][$Name] = $res;
					return $res;
				}
			}
		}
		
		return False;
	}

	function GetByIDEx($ID)
	{
		global $DB;

		$ID = intVal($ID);
		$strSql = 
			"SELECT FU.ID, FU.USER_ID, FU.SHOW_NAME, FU.DESCRIPTION, FU.IP_ADDRESS, ".
			"	FU.REAL_IP_ADDRESS, FU.AVATAR, FU.NUM_POSTS, FU.POINTS as NUM_POINTS, FU.INTERESTS, ".
			"	FU.LAST_POST, FU.ALLOW_POST, FU.SIGNATURE, FU.RANK_ID, ".
			"	U.EMAIL, U.NAME, U.LAST_NAME, U.LOGIN, U.PERSONAL_BIRTHDATE, ".
			"	".$DB->DateToCharFunction("FU.DATE_REG", "SHORT")." as DATE_REG, ".
			"	".$DB->DateToCharFunction("FU.LAST_VISIT", "FULL")." as LAST_VISIT, ".
			"	U.PERSONAL_ICQ, U.PERSONAL_WWW, U.PERSONAL_PROFESSION, ".
			"	U.PERSONAL_CITY, U.PERSONAL_COUNTRY, U.PERSONAL_PHOTO, ".
			"	U.PERSONAL_GENDER, FU.POINTS, FU.HIDE_FROM_ONLINE, FU.SUBSC_GROUP_MESSAGE, FU.SUBSC_GET_MY_MESSAGE, ".
			"	".$DB->DateToCharFunction("U.PERSONAL_BIRTHDAY", "SHORT")." as PERSONAL_BIRTHDAY ".
			"FROM b_user U, b_forum_user FU ".
			"WHERE FU.USER_ID = U.ID ".
			"	AND FU.ID = ".$ID." ";
		$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		if ($res = $db_res->Fetch())
		{
			return $res;
		}
		return False;
	}

	function GetByUSER_ID($USER_ID)
	{
		global $DB;

		$USER_ID = intVal($USER_ID);
		if (isset($GLOBALS["FORUM_CACHE"]["USER_ID"][$USER_ID]) && is_array($GLOBALS["FORUM_CACHE"]["USER_ID"][$USER_ID]) && is_set($GLOBALS["FORUM_CACHE"]["USER_ID"][$USER_ID], "ID"))
		{
			return $GLOBALS["FORUM_CACHE"]["USER_ID"][$USER_ID];
		}
		else
		{
			$strSql = 
				"SELECT FU.ID, FU.USER_ID, FU.SHOW_NAME, FU.DESCRIPTION, FU.IP_ADDRESS, 
					FU.REAL_IP_ADDRESS, FU.AVATAR, FU.NUM_POSTS, FU.POINTS as NUM_POINTS, 
					FU.INTERESTS, FU.HIDE_FROM_ONLINE, FU.SUBSC_GROUP_MESSAGE, FU.SUBSC_GET_MY_MESSAGE, 
					FU.LAST_POST, FU.ALLOW_POST, FU.SIGNATURE, FU.RANK_ID, FU.POINTS, 
					".$DB->DateToCharFunction("FU.DATE_REG", "SHORT")." as DATE_REG, 
					".$DB->DateToCharFunction("FU.LAST_VISIT", "FULL")." as LAST_VISIT 
				FROM b_forum_user FU 
				WHERE FU.USER_ID = ".$USER_ID;
			$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

			if ($db_res && $res = $db_res->Fetch())
			{
				$GLOBALS["FORUM_CACHE"]["USER_ID"][$USER_ID] = $res;
				return $res;
			}
		}
		return False;
	}

	function GetByUSER_IDEx($USER_ID)
	{
		global $DB;

		$USER_ID = intVal($USER_ID);
		$strSql = 
			"SELECT F_USER.*, FU.ID, FU.USER_ID, FU.SHOW_NAME, FU.DESCRIPTION, FU.IP_ADDRESS, 
				FU.REAL_IP_ADDRESS, FU.AVATAR, FU.NUM_POSTS, FU.POINTS as NUM_POINTS, 
				FU.INTERESTS, FU.HIDE_FROM_ONLINE, FU.SUBSC_GROUP_MESSAGE, FU.SUBSC_GET_MY_MESSAGE, 
				FU.LAST_POST, FU.ALLOW_POST, FU.SIGNATURE, FU.RANK_ID, FU.POINTS, 
				".$DB->DateToCharFunction("FU.DATE_REG", "SHORT")." as DATE_REG, 
				".$DB->DateToCharFunction("FU.LAST_VISIT", "FULL")." as LAST_VISIT
				FROM b_forum_user FU 
				LEFT JOIN (
					SELECT FM.AUTHOR_ID, MAX(FM.ID) AS LAST_MESSAGE_ID, COUNT(FM.ID) AS CNT
					FROM b_forum_message FM
					WHERE (FM.AUTHOR_ID = ".$USER_ID." AND FM.APPROVED = 'Y')
					GROUP BY FM.AUTHOR_ID
				) F_USER ON (F_USER.AUTHOR_ID = FU.USER_ID)
			WHERE (FU.USER_ID = ".$USER_ID.")";
		$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		if ($db_res && $res = $db_res->Fetch())
		{
			return $res;
		}
		return False;
	}


	function GetUserRank($USER_ID, $strLang = false)
	{
		$USER_ID = intVal($USER_ID);
		if ($USER_ID<=0) return false;

		if ($strLang===false)
		{
			$arUser = CForumUser::GetByUSER_ID($USER_ID);
			if ($arUser)
			{
				$db_res = CForumPoints::GetList(array("MIN_POINTS"=>"DESC"), array("<=MIN_POINTS"=>$arUser["POINTS"]));
				if ($ar_res = $db_res->Fetch())
					return $ar_res;
			}
		}
		else
		{
			if (strLen($strLang)!=2) 
				return false;

			$arUser = CForumUser::GetByUSER_ID($USER_ID);
			if ($arUser)
			{
				$db_res = CForumPoints::GetListEx(array("MIN_POINTS"=>"DESC"), array("<=MIN_POINTS"=>$arUser["POINTS"], "LID" => $strLang));
				if ($ar_res = $db_res->Fetch())
				{
					return $ar_res;
				}
			}
		}

		return false;
	}
	
	//---------------> User visited
	function SetUserForumLastVisit($USER_ID, $FORUM_ID = 0, $LAST_VISIT = false)
	{
		global $DB;
		$USER_ID = intVal($USER_ID);
		$FORUM_ID = intVal($FORUM_ID);
		if (is_int($LAST_VISIT)):
			$LAST_VISIT = $DB->CharToDateFunction(date(CDatabase::DateFormatToPHP(CLang::GetDateFormat("FULL")), $LAST_VISIT), "FULL");
		elseif (is_string($LAST_VISIT)):
			$LAST_VISIT = $DB->CharToDateFunction(trim($LAST_VISIT), "FULL");
		else:
			$LAST_VISIT = false;
		endif;

		if (!$LAST_VISIT):
			$Fields = array("LAST_VISIT" => $DB->GetNowFunction());
			$rows = $DB->Update("b_forum_user_forum", $Fields, "WHERE (FORUM_ID=".$FORUM_ID." AND USER_ID=".$USER_ID.")", "File: ".__FILE__."<br>Line: ".__LINE__);
			
			if (intVal($rows) <= 0):
				$Fields["USER_ID"] = $USER_ID;
				$Fields["FORUM_ID"] = $FORUM_ID;
				$DB->Insert("b_forum_user_forum", $Fields, "File: ".__FILE__."<br>Line: ".__LINE__);
			elseif ($FORUM_ID <= 0):
				$DB->Query("DELETE FROM b_forum_user_forum WHERE (FORUM_ID > 0 AND USER_ID=".$USER_ID.")", false, "File: ".__FILE__."<br>Line: ".__LINE__);
				$DB->Query("DELETE FROM b_forum_user_topic WHERE (USER_ID=".$USER_ID.")", false, "File: ".__FILE__."<br>Line: ".__LINE__);
			else:
				$DB->Query("DELETE FROM b_forum_user_topic WHERE (FORUM_ID=".$FORUM_ID." AND USER_ID=".$USER_ID.")", false, "File: ".__FILE__."<br>Line: ".__LINE__);
			endif;
		else: 
			$Fields = array("LAST_VISIT" => $LAST_VISIT);
			$rows = $DB->Update("b_forum_user_forum", $Fields, 
				"WHERE (FORUM_ID=".$FORUM_ID." AND USER_ID=".$USER_ID.")", "File: ".__FILE__."<br>Line: ".__LINE__);

			if (intVal($rows) <= 0):
				$Fields = array("LAST_VISIT" => $LAST_VISIT, "FORUM_ID" => $FORUM_ID, "USER_ID" => $USER_ID);
				$DB->Insert("b_forum_user_forum", $Fields, "File: ".__FILE__."<br>Line: ".__LINE__);
			elseif ($FORUM_ID <= 0):
				$DB->Query("DELETE FROM b_forum_user_forum WHERE (FORUM_ID > 0 AND USER_ID=".$USER_ID." AND LAST_VISIT <= ".$LAST_VISIT.")", 
					false, "File: ".__FILE__."<br>Line: ".__LINE__);
				$DB->Query("DELETE FROM b_forum_user_topic WHERE (USER_ID=".$USER_ID." AND LAST_VISIT <= ".$LAST_VISIT.")", 
					false, "File: ".__FILE__."<br>Line: ".__LINE__);
			else:
				$DB->Query("DELETE FROM b_forum_user_topic WHERE (FORUM_ID=".$FORUM_ID." AND USER_ID=".$USER_ID." AND LAST_VISIT <= ".$LAST_VISIT.")", 
					false, "File: ".__FILE__."<br>Line: ".__LINE__);
			endif;
		endif;
		return true;
	}
	
	function GetListUserForumLastVisit($arOrder = Array("LAST_VISIT"=>"DESC"), $arFilter = Array())
	{
		global $DB;
		$arSqlSearch = Array();
		$arSqlOrder = Array();
		$strSqlSearch = "";
		$strSqlOrder = "";
		$arFilter = (is_array($arFilter) ? $arFilter : array());

		foreach ($arFilter as $key => $val)
		{
			$key_res = CForumNew::GetFilterOperation($key);
			$key = strToUpper($key_res["FIELD"]);
			$strNegative = $key_res["NEGATIVE"];
			$strOperation = $key_res["OPERATION"];

			switch ($key)
			{
				case "ID":
				case "USER_ID":
				case "FORUM_ID":
					if (intVal($val)<=0)
						$arSqlSearch[] = ($strNegative=="Y"?"NOT":"")."(FUF.".$key." IS NULL OR FUF.".$key."<=0)";
					else
						$arSqlSearch[] = ($strNegative=="Y"?" FUF.".$key." IS NULL OR NOT ":"")."(FUF.".$key." ".$strOperation." ".intVal($val)." )";
					break;
			}
		}
		for ($i=0; $i<count($arSqlSearch); $i++)
			$strSqlSearch .= " AND (".$arSqlSearch[$i].") ";
		foreach ($arOrder as $by=>$order)
		{
			$by = strtoupper($by); $order = strtoupper($order);
			if ($order!="ASC") $order = "DESC";

			if ($by == "USER_ID") $arSqlOrder[] = " FUF.USER_ID ".$order." ";
			elseif ($by == "FORUM_ID") $arSqlOrder[] = " FUF.FORUM_ID ".$order." ";
			elseif ($by == "LAST_VISIT") $arSqlOrder[] = " FUF.LAST_VISIT ".$order." ";
			else
			{
				$arSqlOrder[] = " FU.ID ".$order." ";
				$by = "ID";
			}
		}
		DelDuplicateSort($arSqlOrder); 
		if (count($arSqlOrder) > 0)
			$strSqlOrder = " ORDER BY ".implode(", ", $arSqlOrder);
			
		$strSql = "
			SELECT FUF.ID, FUF.FORUM_ID,  FUF.USER_ID, ".$DB->DateToCharFunction("FUF.LAST_VISIT", "FULL")." as LAST_VISIT 
			FROM b_forum_user_forum FUF
				INNER JOIN b_user U ON (U.ID = FUF.USER_ID)
			WHERE 1=1 ".$strSqlSearch."
			".$strSqlOrder;
		$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		return $db_res;
	}
	//---------------> User visited
	
	//---------------> User utils
	function GetUserPoints($USER_ID, $arAddParams = array())
	{
		global $DB;
		$USER_ID = intVal($USER_ID);
		if ($USER_ID <= 0) return 0;
		$arAddParams = (is_array($arAddParams) ? $arAddParams : array($arAddParams));
		$arAddParams["INCREMENT"] = intVal($arAddParams["INCREMENT"]);
		$arAddParams["DECREMENT"] = intVal($arAddParams["DECREMENT"]);
		$arAddParams["NUM_POSTS"] = (is_set($arAddParams, "NUM_POSTS") ? $arAddParams["NUM_POSTS"] : false);
		$arAddParams["RETURN_FETCH"] = ($arAddParams["RETURN_FETCH"] == "Y" ? "Y" : "N");
		$strSql = "
			SELECT
			  (".
				($arAddParams["NUM_POSTS"] ? $arAddParams["NUM_POSTS"] : "FU.NUM_POSTS").
			  	($arAddParams["INCREMENT"] > 0 ? "+".$arAddParams["INCREMENT"] : "").
			  	($arAddParams["DECREMENT"] > 0 ? "-".$arAddParams["DECREMENT"] : "").
			  	") AS NUM_POSTS, FP2P.MIN_NUM_POSTS, FP2P.POINTS_PER_POST, SUM(FUP.POINTS) AS POINTS_FROM_USER
			FROM
			  b_forum_user FU
			  LEFT JOIN b_forum_points2post FP2P ON (FP2P.MIN_NUM_POSTS <= ".
			  	($arAddParams["NUM_POSTS"] ? $arAddParams["NUM_POSTS"] : "FU.NUM_POSTS").
			  	($arAddParams["INCREMENT"] > 0 ? "+".$arAddParams["INCREMENT"] : "").
			  	($arAddParams["DECREMENT"] > 0 ? "-".$arAddParams["DECREMENT"] : "").")
			  LEFT JOIN b_forum_user_points FUP ON (FUP.TO_USER_ID = FU.USER_ID)
			WHERE
			  FU.user_id = ".$USER_ID."
			GROUP BY
				".($arAddParams["NUM_POSTS"] ? "" : "FU.NUM_POSTS, ")."FP2P.MIN_NUM_POSTS, FP2P.POINTS_PER_POST
			ORDER BY
			  FP2P.MIN_NUM_POSTS DESC";
		
		$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		if ($arAddParams["RETURN_FETCH"] == "Y"):
			return $db_res;
		elseif ($db_res && ($res = $db_res->Fetch())):
			$result = floor(doubleVal($res["POINTS_PER_POST"])*intVal($res["NUM_POSTS"]) + intVal($res["POINTS_FROM_USER"]));
			return $result;
		endif;
		return false;
	}
	
	function CountUserPoints($USER_ID = 0, $iCnt = false)
	{
		$USER_ID = intVal($USER_ID);
		$iNumUserPosts = intVal($iCnt);
		$iNumUserPoints = 0;
		$fPointsPerPost = 0.0;
		if ($USER_ID <= 0) return 0;
		
		if ($iCnt === false):
			$iNumUserPoints = CForumUser::GetUserPoints($USER_ID);
		endif;
		
		if ($iNumUserPoints === false || $iCnt != false):
			$iNumUserPosts = CForumMessage::GetList(array(), array("AUTHOR_ID" => $USER_ID, "APPROVED" => "Y"), true);
			$db_res = CForumPoints2Post::GetList(array("MIN_NUM_POSTS" => "DESC"), array("<=MIN_NUM_POSTS" => $iNumUserPosts));
			if ($ar_res = $db_res->Fetch())
				$fPointsPerPost = DoubleVal($ar_res["POINTS_PER_POST"]);
			$iNumUserPoints = floor($fPointsPerPost*$iNumUserPosts);
			$iCnt = CForumUserPoints::CountSumPoints($USER_ID);
			$iNumUserPoints += $iCnt;
		endif;
		return $iNumUserPoints;
	}

	function SetStat($USER_ID = 0, $arParams = array())
	{
		$USER_ID = intVal($USER_ID);
		if ($USER_ID <= 0) 
			return 0;

		$bNeedCreateUser = false;
		$arUser = array();
		$arUserFields = Array();

		$arParams = (is_array($arParams) ? $arParams : array());
		
		$arMessage = (is_array($arParams["MESSAGE"]) ? $arParams["MESSAGE"] : array());
		$arMessage = ($arMessage["AUTHOR_ID"] != $USER_ID ? array() : $arMessage);
		
		if (!empty($arMessage))
		{
			$arParams["ACTION"] = ($arParams["ACTION"] == "DECREMENT" || $arParams["ACTION"] == "UPDATE" ? $arParams["ACTION"] : "INCREMENT");
			if ($arParams["ACTION"] == "UPDATE"):
				$arParams["ACTION"] = ($arMessage["APPROVED"] == "Y" ? "INCREMENT" : "DECREMENT");
				$arMessage["APPROVED"] = "Y";
			endif;
			
			$arParams["POSTS"] = intVal($arParams["POSTS"] > 0 ? $arParams["POSTS"] : 1);
			$arUser = CForumUser::GetByUSER_ID($USER_ID);
		}

		if (empty($arMessage)):
			// full recount;
		elseif ($arMessage["APPROVED"] != "Y"):
			return true;
		elseif (empty($arUser)):
			$bNeedCreateUser = true;
			// full recount;
		elseif ($arParams["ACTION"] == "DECREMENT" && $arMessage["ID"] >= $arUser["LAST_POST"]):
			// full recount;
		elseif ($arParams["ACTION"] == "DECREMENT"):
			$arUserFields = array(
				"=NUM_POSTS" => "NUM_POSTS-".$arParams["POSTS"], 
				"POINTS" => intVal(CForumUser::GetUserPoints($USER_ID, array("DECREMENT" => $arParams["POSTS"]))));
		elseif ($arParams["ACTION"] == "INCREMENT" && $arMessage["ID"] < $arUser["LAST_POST"]):
			$arUserFields = array(
				"=NUM_POSTS" => "NUM_POSTS+".$arParams["POSTS"], 
				"POINTS" => intVal(CForumUser::GetUserPoints($USER_ID, array("INCREMENT" => $arParams["POSTS"]))));
		elseif ($arParams["ACTION"] == "INCREMENT"): 
			$arUserFields["IP_ADDRESS"] = $arMessage["AUTHOR_IP"];
			$arUserFields["REAL_IP_ADDRESS"] = $arMessage["AUTHOR_REAL_IP"];
			$arUserFields["LAST_POST"] = intVal($arMessage["ID"]);
			$arUserFields["LAST_POST_DATE"] = $arMessage["POST_DATE"];
			$arUserFields["=NUM_POSTS"] = "NUM_POSTS+".$arParams["POSTS"];
			$arUserFields["POINTS"] = intVal(CForumUser::GetUserPoints($USER_ID, array("INCREMENT" => $arParams["POSTS"])));
		endif;

		if (empty($arUserFields))
		{
			$arUserFields = Array(
				"LAST_POST" => false,
				"LAST_POST_DATE" => false);
			if ($bNeedCreateUser == false)
				$arUser = CForumUser::GetByUSER_IDEx($USER_ID);
			if (empty($arUser) || $bNeedCreateUser == true):
				$bNeedCreateUser = true;
				$arUser = CForumMessage::GetList(array(), array("AUTHOR_ID" => $USER_ID, "APPROVED" => "Y"), "cnt_and_last_mid");
				$arUser = (is_array($arUser) ? $arUser : array());
			endif;
			$arMessage = CForumMessage::GetByID($arUser["LAST_MESSAGE_ID"], array("FILTER" => "N"));
			if ($arMessage):
				$arUserFields["IP_ADDRESS"] = $arMessage["AUTHOR_IP"];
				$arUserFields["REAL_IP_ADDRESS"] = $arMessage["AUTHOR_REAL_IP"];
				$arUserFields["LAST_POST"] = intVal($arMessage["ID"]);
				$arUserFields["LAST_POST_DATE"] = $arMessage["POST_DATE"];
			endif;
			$arUserFields["NUM_POSTS"] = intVal($arUser["CNT"]);
			$arUserFields["POINTS"] = intVal(CForumUser::GetUserPoints($USER_ID, array("NUM_POSTS" => $arUserFields["NUM_POSTS"])));
		}

		if ($bNeedCreateUser):
			$arUserFields["USER_ID"] = $USER_ID;
			$arUser = CForumUser::Add($arUserFields);
		else:
			CForumUser::Update($USER_ID, $arUserFields, false, true);
		endif;
		
		return $USER_ID;
	}
	//---------------> User actions
	function OnUserDelete($user_id)
	{
		global $DB;
		$user_id = intVal($user_id);
		if ($user_id>0)
		{
			$DB->Query("UPDATE b_forum SET LAST_POSTER_ID = NULL WHERE LAST_POSTER_ID = ".$user_id."");
			$DB->Query("UPDATE b_forum_topic SET LAST_POSTER_ID = NULL WHERE LAST_POSTER_ID = ".$user_id."");
			$DB->Query("UPDATE b_forum_topic SET USER_START_ID = NULL WHERE USER_START_ID = ".$user_id."");
			$DB->Query("UPDATE b_forum_message SET AUTHOR_ID = NULL WHERE AUTHOR_ID = ".$user_id."");
			$DB->Query("DELETE FROM b_forum_subscribe WHERE USER_ID = ".$user_id."");
			$DB->Query("DELETE FROM b_forum_stat WHERE USER_ID = ".$user_id."");

			$strSql = "
				SELECT 
					F.ID
				FROM 
					b_forum_user FU, 
					b_file F
				WHERE 
					FU.USER_ID = $user_id
				and FU.AVATAR = F.ID 
				";
			$z = $DB->Query($strSql, false, "FILE: ".__FILE__." LINE:".__LINE__);
			while ($zr = $z->Fetch()) CFile::Delete($zr["ID"]);

			$DB->Query("DELETE FROM b_forum_user WHERE USER_ID = ".$user_id."");
			
			if(CModule::IncludeModule("socialnetwork"))
			{
				$dbRes = $DB->Query("select ID from b_forum_topic where OWNER_ID=".$user_id);
				while($arRes = $dbRes->Fetch())
				{
					$DB->Query("DELETE FROM b_forum_message WHERE TOPIC_ID = ".$arRes["ID"]);
					$DB->Query("DELETE FROM b_forum_topic WHERE ID = ".$arRes["ID"]);
				}
				
			}
		}
		return true;
	}
	// >-- Using for private message
	function SearchUser($template)
	{
		global $DB;
		$template = $DB->ForSql(str_replace("*", "%", $template));
		
		$strSql = 
			"SELECT U.ID, U.NAME, U.LAST_NAME, U.LOGIN, F.SHOW_NAME ".
			"FROM b_forum_user F LEFT JOIN b_user U ON(F.USER_ID = U.ID)".
			"WHERE ((F.SHOW_NAME='Y')AND(U.NAME LIKE '".$template."' OR U.LAST_NAME LIKE '".$template."')) OR(( U.LOGIN LIKE '".$template."')AND(F.SHOW_NAME='N'))";		
		$dbRes = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		return $dbRes;
	}

	function UserAddInfo($arOrder = array(), $arFilter = Array(), $mode = false, $iNum = 0, $check_permission = true)
	{
		global $DB, $USER;
		
		$arSqlFrom = array();
		$arSqlOrder = array();
		$arSqlSearch = array();
		$strSqlFrom = "";
		$strSqlOrder = "";
		$strSqlSearch = "";
		$arFilter = (is_array($arFilter) ? $arFilter : array());
		if ((!$USER->IsAdmin()) && $check_permission)
		{
			$arFilter["LID"] = SITE_ID;
			$arFilter["PERMISSION"] = true;
		}
		
		foreach ($arFilter as $key => $val)
		{
			$key_res = CForumNew::GetFilterOperation($key);
			$key = strtoupper($key_res["FIELD"]);
			$strNegative = $key_res["NEGATIVE"];
			$strOperation = $key_res["OPERATION"];
			switch ($key)
			{
				case "ID":
				case "AUTHOR_ID":
				case "FORUM_ID":
				case "TOPIC_ID":
					if ($strOperation == 'IN'):
						$res = (is_array($val) ? $val : explode(",", $val));
						$val = array();
						foreach ($res as $v)
							$val[] = intVal($v);
						$val = implode(",", $val);
					else:
						$val = intVal($val);
					endif;
					if ($val <= 0)
						$arSqlSearch[] = ($strNegative=="Y"?"NOT":"")."(FM.".$key." IS NULL OR FM.".$key."<=0)";
					else
						$arSqlSearch[] = ($strNegative=="Y"?" FM.".$key." IS NULL OR NOT ":"")."FM.".$key." ".$strOperation." (".$DB->ForSql($val).")";
					break;
				case "APPROVED":
					if (strLen($val)<=0)
						$arSqlSearch[] = ($strNegative=="Y"?"NOT":"")."(FM.".$key." IS NULL OR ".($DB->type == "MSSQL" ? "LEN" : "LENGTH")."(FM.".$key.")<=0)";
					else
						$arSqlSearch[] = ($strNegative=="Y"?" FM.".$key." IS NULL OR NOT ":"")."FM.".$key." ".$strOperation." '".$DB->ForSql($val)."'";
					break;
				case "DATE":
				case "POST_DATE":
					if (strLen($val)<=0)
						$arSqlSearch[] = ($strNegative=="Y"?"NOT":"")."FM.".$key." IS NULL";
					else
						$arSqlSearch[] = ($strNegative=="Y"?" FM.".$key." IS NULL OR NOT ":"")."FM.".$key." ".$strOperation." ".$DB->CharToDateFunction($DB->ForSql($val), "SHORT");
					break;
				case "LID":
					$arSqlFrom["FS2"] = "LEFT JOIN b_forum2site FS2 ON (FS2.FORUM_ID = FM.FORUM_ID)";
					$arSqlSearch[] = ($strNegative=="Y"?" NOT ":"")."(FS2.SITE_ID ".$strOperation." '".$DB->ForSql($val)."')";
					break;
				case "ACTIVE":
					$arSqlFrom["F"] = "INNER JOIN b_forum F ON (F.ID = FM.FORUM_ID)";
					if (strLen($val)<=0)
						$arSqlSearch[] = ($strNegative=="Y"?"NOT":"")."(F.".$key." IS NULL OR ".($DB->type == "MSSQL" ? "LEN" : "LENGTH")."(F.".$key.")<=0)";
					else
						$arSqlSearch[] = ($strNegative=="Y"?" F.".$key." IS NULL OR NOT ":"")."F.".$key." ".$strOperation." '".$DB->ForSql($val)."'";
					break;
				case "USER_START_ID":
					if (!is_array($val))
						$val = array($val);
					$tmp = array();
					foreach ($val as $k=>$v)
						$tmp[] = intVal(trim($v));
					$val = implode(",", $tmp);
					$arSqlFrom["FT"] = "INNER JOIN b_forum_topic FT ON (FT.ID = FM.TOPIC_ID)";
					if (strLen($val)<=0)
						$arSqlSearch[] = ($strNegative=="Y"?"NOT":"")."FT.".$key." IS NULL OR FT.".$key."<=0";
					else
						$arSqlSearch[] = ($strNegative=="Y"?" FT.".$key." IS NULL OR NOT ":"")."FT.".$key." ".$strOperation." (".$DB->ForSql($val).")";
					break;
				case "PERMISSION":
					$arSqlFrom["FP"] = "
						INNER JOIN (
							SELECT FP.FORUM_ID, MAX(FP.PERMISSION) AS PERMISSION 
							FROM b_forum_perms FP 
							WHERE FP.GROUP_ID IN (".$DB->ForSql(implode(",", $USER->GetUserGroupArray())).") AND FP.PERMISSION > 'A'
							GROUP BY FP.FORUM_ID) FPP ON (FPP.FORUM_ID = FM.FORUM_ID) ";
					$arSqlSearch[] = "(FPP.PERMISSION > 'A' AND (FM.APPROVED='Y' OR FPP.PERMISSION >= 'Q'))"; 
					break;
				case "TOPIC_TITLE":
				case "POST_MESSAGE":
					if ($key == "TOPIC_TITLE")
					{
						$key = "FT.TITLE";
						$arSqlFrom["FT"] = "INNER JOIN b_forum_topic FT ON (FT.ID = FM.TOPIC_ID)";
					}
					else 
						$key = "FM.POST_MESSAGE";
					if ($strOperation == "LIKE")
						$val = "%".$val."%";

					if (strLen($val)<=0)
						$arSqlSearch[] = ($strNegative=="Y"?"NOT":"")."(".$key." IS NULL OR ".($DB->type == "MSSQL" ? "LEN" : "LENGTH")."(".$key.")<=0)";
					else
						$arSqlSearch[] = ($strNegative=="Y"?" ".$key." IS NULL OR NOT ":"")."(".$key." ".$strOperation." '".$DB->ForSQL($val)."')";
					break;
			}
		}
		ksort($arSqlFrom);
		if (count($arSqlFrom) > 0)
			$strSqlFrom = " ".implode(" ", $arSqlFrom);
			
		if (count($arSqlSearch) > 0)
			$strSqlSearch = " AND (".implode(") AND (", $arSqlSearch).")";
			
		foreach ($arOrder as $key=>$val)
		{
			$key = strtoupper($key); $val = (strtoupper($val) != "ASC" ? "DESC" : "ASC"); 
			switch ($key)
			{
				case "FIRST_POST":
				case "LAST_POST":
					$arSqlOrder["LAST_POST"] = "FMM.".$key." ".$val;
				break;
				case "FORUM_ID":
				case "TOPIC_ID":
					$arSqlOrder["ID"] = " FT.".$key." ".$val;
				break;
			}
		}
		if (count($arSqlOrder)>0)
			$strSqlOrder = "ORDER BY ".implode(", ", $arSqlOrder);
		else 
			$strSqlOrder = "ORDER BY FMM.FIRST_POST DESC";
			
		// *****************************************************
		$strSql = "
		SELECT FMM.*, FT.TITLE, FT.DESCRIPTION, FT.VIEWS, FT.LAST_POSTER_ID, 
			".$DB->DateToCharFunction("FT.START_DATE", "FULL")." as START_DATE, 
			FT.USER_START_NAME,	FT.USER_START_ID, FT.POSTS, FT.LAST_POSTER_NAME, 
			FT.LAST_MESSAGE_ID, FS.IMAGE, '' as IMAGE_DESCR,
			FT.APPROVED, FT.STATE, FT.FORUM_ID, FT.ICON_ID, FT.SORT, FT.HTML 
		FROM
		(
			SELECT FM.TOPIC_ID, COUNT(FM.ID) AS COUNT_MESSAGE, MIN(FM.ID) AS FIRST_POST, MAX(FM.ID) AS LAST_POST
			FROM b_forum_message FM 
			".$strSqlFrom."
			WHERE 1=1 
			".$strSqlSearch." 
			GROUP BY FM.TOPIC_ID
		) FMM
		LEFT JOIN b_forum_topic FT ON (FT.ID = FMM.TOPIC_ID)
		LEFT JOIN b_forum_smile FS ON (FT.ICON_ID = FS.ID)
		".$strSqlOrder;
		$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		$db_res = new _CTopicDBResult($db_res);
		return $db_res;
	}
	// <-- Using for private message
	
	function OnSocNetGroupDelete($group_id)
	{
		global $DB;
		$group_id = intVal($group_id);
		if ($group_id>0)
		{			
			if(CModule::IncludeModule("socialnetwork"))
			{
				$dbRes = $DB->Query("select ID from b_forum_topic where SOCNET_GROUP_ID=".$group_id);
				while($arRes = $dbRes->Fetch())
				{
					$DB->Query("DELETE FROM b_forum_message WHERE TOPIC_ID = ".$arRes["ID"]);
					$DB->Query("DELETE FROM b_forum_topic WHERE ID = ".$arRes["ID"]);
				}
				
			}
		}
		return true;
	}
}


/**********************************************************************/
/************** SUBSCRIBE *********************************************/
/**********************************************************************/
class CAllForumSubscribe
{
	//---------------> User insert, update, delete
	function CanUserAddSubscribe($FID, $arUserGroups)
	{
		if (CForumNew::GetUserPermission($FID, $arUserGroups)>="E") return True;
		return False;
	}

	function CanUserUpdateSubscribe($ID, $arUserGroups, $CurrentUserID = 0)
	{
		$ID = intVal($ID);
		$CurrentUserID = intVal($CurrentUserID);
		if (in_array(1, $arUserGroups)) return True;

		$arSubscr = CForumSubscribe::GetByID($ID);
		if ($arSubscr && intVal($arSubscr["USER_ID"]) == $CurrentUserID) return True;
		return False;
	}

	function CanUserDeleteSubscribe($ID, $arUserGroups, $CurrentUserID = 0)
	{
		$ID = intVal($ID);
		$CurrentUserID = intVal($CurrentUserID);
		if (in_array(1, $arUserGroups)) return True;

		$arSubscr = CForumSubscribe::GetByID($ID);
		if ($arSubscr && intVal($arSubscr["USER_ID"]) == $CurrentUserID) return True;
		return False;
	}

	function CheckFields($ACTION, &$arFields)
	{
		if ((is_set($arFields, "USER_ID") || $ACTION=="ADD") && intVal($arFields["USER_ID"])<=0) return false;
		if ((is_set($arFields, "FORUM_ID") || $ACTION=="ADD") && intVal($arFields["FORUM_ID"])<=0) return false;
		if ((is_set($arFields, "SITE_ID") || $ACTION=="ADD") && strLen($arFields["SITE_ID"])<=0) return false;

		if ((is_set($arFields, "TOPIC_ID") || $ACTION=="ADD") && intVal($arFields["TOPIC_ID"])<=0) $arFields["TOPIC_ID"] = false;
		if ((is_set($arFields, "NEW_TOPIC_ONLY") || $ACTION=="ADD") && ($arFields["NEW_TOPIC_ONLY"]!="Y")) $arFields["NEW_TOPIC_ONLY"] = "N";

		if ($arFields["TOPIC_ID"]!==false) $arFields["NEW_TOPIC_ONLY"] = "N";
		if ($ACTION=="ADD")
		{
			$arFilter = array("USER_ID"=>intVal($arFields["USER_ID"]), "FORUM_ID"=>intVal($arFields["FORUM_ID"]), "TOPIC_ID"=>intVal($arFields["TOPIC_ID"]));
			if($arFields["SOCNET_GROUP_ID"])
				$arFilter["SOCNET_GROUP_ID"] = $arFields["SOCNET_GROUP_ID"];
			$db_res = CForumSubscribe::GetList(array(), $arFilter);
			if ($res = $db_res->Fetch())
			{
				return false;
			}
		}

		return True;
	}
	
	function Add($arFields)
	{
		global $DB;

		if (!CForumSubscribe::CheckFields("ADD", $arFields))
			return false;
			
		$Fields = array(
			"USER_ID" => intVal($arFields["USER_ID"]),
			"FORUM_ID" => intVal($arFields["FORUM_ID"]),
			"START_DATE" => $DB->GetNowFunction(),
			"NEW_TOPIC_ONLY" => "'".$DB->ForSQL($arFields["NEW_TOPIC_ONLY"], 1)."'",
			"SITE_ID" => "'".$DB->ForSQL($arFields["SITE_ID"], 2)."'",
			);

		if(intval($arFields["SOCNET_GROUP_ID"])>0)
			$Fields["SOCNET_GROUP_ID"] = intval($arFields["SOCNET_GROUP_ID"]);

		if (intVal($arFields["TOPIC_ID"]) > 0)
			$Fields["TOPIC_ID"] = intVal($arFields["TOPIC_ID"]);
			
		return $DB->Insert("b_forum_subscribe", $Fields, "File: ".__FILE__."<br>Line: ".__LINE__);
	}
	
	function Update($ID, $arFields)
	{
		global $DB;
		$ID = intVal($ID);

		if (!CForumSubscribe::CheckFields("UPDATE", $arFields))
			return false;

		$strUpdate = $DB->PrepareUpdate("b_forum_subscribe", $arFields);
		$strSql = "UPDATE b_forum_subscribe SET ".$strUpdate." WHERE ID = ".$ID;
		$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		return $ID;
	}

	function Delete($ID)
	{
		global $DB;
		$ID = intVal($ID);
		return $DB->Query("DELETE FROM b_forum_subscribe WHERE ID = ".$ID, True);
	}
	
	function DeleteUSERSubscribe($USER_ID)
	{
		global $DB;
		$USER_ID = intVal($USER_ID);
		return $DB->Query("DELETE FROM b_forum_subscribe WHERE USER_ID = ".$USER_ID, false, "File: ".__FILE__."<br>Line: ".__LINE__);
	}

	function UpdateLastSend($MID, $sIDs)
	{
		global $DB;
		$MID = intVal($MID);
		$arID = explode(",", $sIDs);
		if ($MID <= 0 || empty($sIDs) || (count($arID) == 1 && $arID[0] == 0)) 
			return false;

		$DB->Query("UPDATE b_forum_subscribe SET LAST_SEND = ".$MID." WHERE ID IN (".$sIDs.")");
	}

	function GetList($arOrder = array("ID"=>"ASC"), $arFilter = array())
	{
		global $DB;
		$arSqlSearch = Array();
		$arFilter = (is_array($arFilter) ? $arFilter : array());

		foreach ($arFilter as $key => $val)
		{
			$key_res = CForumNew::GetFilterOperation($key);
			$key = strtoupper($key_res["FIELD"]);
			$strNegative = $key_res["NEGATIVE"];
			$strOperation = $key_res["OPERATION"];

			switch ($key)
			{
				case "ID":
				case "USER_ID":
				case "FORUM_ID":
				case "TOPIC_ID":
				case "LAST_SEND":
					if (intVal($val)<=0)
						$arSqlSearch[] = ($strNegative=="Y"?"NOT":"")."(FP.".$key." IS NULL OR FP.".$key."<=0)";
					else
						$arSqlSearch[] = ($strNegative=="Y"?" FP.".$key." IS NULL OR NOT ":"")."(FP.".$key." ".$strOperation." ".intVal($val)." )";
					break;
				case "TOPIC_ID_OR_NULL":
					$arSqlSearch[] = "(FP.TOPIC_ID = ".intVal($val)." OR FP.TOPIC_ID = 0 OR FP.TOPIC_ID IS NULL)";
					break;
				case "NEW_TOPIC_ONLY":
					if (strLen($val)<=0)
						$arSqlSearch[] = ($strNegative=="Y"?"NOT":"")."(FP.NEW_TOPIC_ONLY IS NULL)";
					else
						$arSqlSearch[] = ($strNegative=="Y"?" FP.NEW_TOPIC_ONLY IS NULL OR NOT ":"")."(FP.NEW_TOPIC_ONLY ".$strOperation." '".$DB->ForSql($val)."' )";
					break;
				case "SOCNET_GROUP_ID":
					if($val>0)
						$arSqlSearch[] = "FP.SOCNET_GROUP_ID=".intval($val);
					else
						$arSqlSearch[] = "FP.SOCNET_GROUP_ID IS NULL";
					break;
				case "LAST_SEND_OR_NULL":
					$arSqlSearch[] = "(FP.LAST_SEND IS NULL OR FP.LAST_SEND = 0 OR FP.LAST_SEND < ".intVal($val).")";
					break;
			}
		}

		$strSqlSearch = "";
		for ($i=0; $i<count($arSqlSearch); $i++)
		{
			$strSqlSearch .= " AND (".$arSqlSearch[$i].") ";
		}

		$strSql = 
			"SELECT FP.ID, FP.USER_ID, FP.FORUM_ID, FP.TOPIC_ID, FP.LAST_SEND, FP.NEW_TOPIC_ONLY, FP.SITE_ID, ".
			"	".$DB->DateToCharFunction("FP.START_DATE", "FULL")." as START_DATE ".
			"FROM b_forum_subscribe FP ".
			"WHERE 1 = 1 ".
			"	".$strSqlSearch." ";

		$arSqlOrder = Array();
		foreach ($arOrder as $by=>$order)
		{
			$by = strtoupper($by);
			$order = strtoupper($order);
			if ($order!="ASC") $order = "DESC";

			if ($by == "FORUM_ID") $arSqlOrder[] = " FP.FORUM_ID ".$order." ";
			elseif ($by == "USER_ID") $arSqlOrder[] = " FP.USER_ID ".$order." ";
			elseif ($by == "TOPIC_ID") $arSqlOrder[] = " FP.TOPIC_ID ".$order." ";
			elseif ($by == "NEW_TOPIC_ONLY") $arSqlOrder[] = " FP.NEW_TOPIC_ONLY ".$order." ";
			elseif ($by == "START_DATE") $arSqlOrder[] = " FP.START_DATE ".$order." ";
			else
			{
				$arSqlOrder[] = " FP.ID ".$order." ";
				$by = "ID";
			}
		}

		$strSqlOrder = "";
		DelDuplicateSort($arSqlOrder); for ($i=0; $i<count($arSqlOrder); $i++)
		{
			if ($i==0)
				$strSqlOrder = " ORDER BY ";
			else
				$strSqlOrder .= ", ";

			$strSqlOrder .= $arSqlOrder[$i];
		}
		$strSql .= $strSqlOrder;
		$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		return $db_res;
	}
	
	function GetListEx($arOrder = array("ID"=>"ASC"), $arFilter = array())
	{
		global $DB;
		$arSqlSearch = array();
		$arSqlFrom = array();
		$arSqlGroup = array();
		$arSqlSelect = array();
		$arSqlOrder = array();
		$strSqlSelect = "";
		$strSqlSearch = "";
		$strSqlFrom = "";
		$strSqlGroup = "";
		$strSqlOrder = "";
		$arSqlSelectConst = array(
			"FS.ID" =>"FS.ID", 
			"FS.USER_ID" => "FS.USER_ID",
			"FS.FORUM_ID" => "FS.FORUM_ID",
			"FS.TOPIC_ID" => "FS.TOPIC_ID",
			"FS.LAST_SEND" => "FS.LAST_SEND",
			"FS.NEW_TOPIC_ONLY" => "FS.NEW_TOPIC_ONLY",
			"FS.SITE_ID" => "FS.SITE_ID",
			"START_DATE" => $DB->DateToCharFunction("FS.START_DATE", "FULL"), 
			"U.EMAIL" => "U.EMAIL",
			"U.LOGIN" => "U.LOGIN",
			"U.NAME" => "U.NAME",
			"U.LAST_NAME" =>"U.LAST_NAME",
			"FT.TITLE" => "FT.TITLE",
			"FORUM_NAME" => "F.NAME"
		);
		$arFilter = (is_array($arFilter) ? $arFilter : array());

		foreach ($arFilter as $key => $val)
		{
			$key_res = CForumNew::GetFilterOperation($key);
			$key = strtoupper($key_res["FIELD"]);
			$strNegative = $key_res["NEGATIVE"];
			$strOperation = $key_res["OPERATION"];

			switch ($key)
			{
				case "ID":
				case "USER_ID":
				case "FORUM_ID":
				case "TOPIC_ID":
				case "LAST_SEND":
					if (intVal($val)<=0)
						$arSqlSearch[] = ($strNegative=="Y"?"NOT":"")."(FS.".$key." IS NULL OR FS.".$key."<=0)";
					else
						$arSqlSearch[] = ($strNegative=="Y"?" FS.".$key." IS NULL OR NOT ":"")."(FS.".$key." ".$strOperation." ".intVal($val)." )";
					break;
				case "TOPIC_ID_OR_NULL":
					$arSqlSearch[] = "(FS.TOPIC_ID = ".intVal($val)." OR FS.TOPIC_ID = 0 OR FS.TOPIC_ID IS NULL)";
					break;
				case "NEW_TOPIC_ONLY":
					if (strLen($val)<=0)
						$arSqlSearch[] = ($strNegative=="Y"?"NOT":"")."(FS.".$key." IS NULL)";
					else
						$arSqlSearch[] = ($strNegative=="Y"?" FS.".$key." IS NULL OR NOT ":"")."(FS.".$key." ".$strOperation." '".$DB->ForSql($val)."' )";
					break;
				case "START_DATE":
					if(strLen($val)<=0)
						$arSqlSearch[] = ($strNegative=="Y"?"NOT":"")."(FS.".$key." IS NULL)";
					else
						$arSqlSearch[] = ($strNegative=="Y"?" FS.".$key." IS NULL OR NOT ":"")."(FS.".$key." ".$strOperation." ".$DB->CharToDateFunction($DB->ForSql($val), "SHORT").")";
					break;
				case "LAST_SEND_OR_NULL":
					$arSqlSearch[] = "(FS.LAST_SEND IS NULL OR FS.LAST_SEND = 0 OR FS.LAST_SEND < ".intVal($val).")";
					break;
				case "ACTIVE":
					if (strLen($val)<=0)
						$arSqlSearch[] = ($strNegative=="Y"?"NOT":"")."(U.".$key." IS NULL)";
					else
						$arSqlSearch[] = ($strNegative=="Y"?" U.".$key." IS NULL OR NOT ":"")."(U.".$key." ".$strOperation." '".$DB->ForSql($val)."' )";
					break;
				case "FORUM":
				case "TOPIC":
					$key = ($key == "FORUM"	? "F.NAME" : "FT.TITLE");
					$arSqlSearch[] = GetFilterQuery($key, $val);
					break;
				case "SOCNET_GROUP_ID":
					if($val>0)
						$arSqlSearch[] = "FS.SOCNET_GROUP_ID=".intval($val);
					else
						$arSqlSearch[] = "FS.SOCNET_GROUP_ID IS NULL";
					break;
				case "PERMISSION":
					if($arFilter["SOCNET_GROUP_ID"]>0)
					{
						$arSqlSearch[] = "EXISTS(SELECT 'x' 
							FROM b_sonet_features SF
								INNER JOIN b_sonet_features2perms SFP ON SFP.FEATURE_ID = SF.ID AND SFP.OPERATION_ID = 'view' 
							WHERE SF.ENTITY_TYPE = 'G' 
								AND SF.ENTITY_ID = FS.SOCNET_GROUP_ID
								AND SF.FEATURE = 'forum'
								AND SFP.ROLE = 'N' OR EXISTS(SELECT 'x' FROM b_sonet_user2group UG WHERE UG.USER_ID = FS.USER_ID AND ".$DB->IsNull("SFP.ROLE", "'K'")." >= UG.ROLE AND UG.GROUP_ID = FS.SOCNET_GROUP_ID)
						) ";
					}
					elseif (strLen($val)>0)
					{
						$arSqlSearch[] = "(
							(FP.PERMISSION >= '".$DB->ForSql($val)."') OR
							(FP1.PERMISSION >= '".$DB->ForSql($val)."') OR 
							((FP.ID IS NULL) AND (UG.GROUP_ID = 1)))";
						$arSqlSelect[] = "FU.SUBSC_GROUP_MESSAGE, FU.SUBSC_GET_MY_MESSAGE";
						$arSqlFrom[] = " 
							LEFT JOIN b_forum_user FU ON (U.ID = FU.USER_ID) 
							LEFT JOIN b_user_group UG ON (U.ID = UG.USER_ID) 
							LEFT JOIN b_forum_perms FP ON (FP.FORUM_ID = FS.FORUM_ID AND FP.GROUP_ID=UG.GROUP_ID)
							LEFT JOIN b_forum_perms FP1 ON (FP1.FORUM_ID = FS.FORUM_ID AND FP1.GROUP_ID=2)";
						$arSqlGroup = array_values($arSqlSelectConst);
						$arSqlGroup[] = "FU.SUBSC_GROUP_MESSAGE, FU.SUBSC_GET_MY_MESSAGE";
					}
					break;
			}
		}

		if (count($arSqlSelect) > 0)
			$strSqlSelect .= ", ".implode(", ", $arSqlSelect);
			
		if (count($arSqlSearch) > 0)
			$strSqlSearch .= " AND (".implode(") 
			AND 
			(", $arSqlSearch).") ";

		if (count($arSqlFrom)>0)
			$strSqlFrom .= " ".implode(" ", $arSqlFrom)." ";

		if (count($arSqlGroup)>0)
			$strSqlGroup .= " GROUP BY ".implode(", ", $arSqlGroup)." ";
			
		foreach ($arOrder as $by=>$order)
		{
			$by = strtoupper($by);
			$order = strtoupper($order);
			if ($order!="ASC") $order = "DESC";

			if ($by == "FORUM_ID") $arSqlOrder[] = " FS.FORUM_ID ".$order." ";
			elseif ($by == "USER_ID") $arSqlOrder[] = " FS.USER_ID ".$order." ";
			elseif ($by == "FORUM_NAME") $arSqlOrder[] = " F.NAME ".$order." ";
			elseif ($by == "TOPIC_ID") $arSqlOrder[] = " FS.TOPIC_ID ".$order." ";
			elseif ($by == "TITLE") $arSqlOrder[] = " FT.TITLE ".$order." ";
			elseif ($by == "START_DATE") $arSqlOrder[] = " FS.START_DATE ".$order." ";
			elseif ($by == "NEW_TOPIC_ONLY") $arSqlOrder[] = " FS.NEW_TOPIC_ONLY ".$order." ";
			elseif ($by == "LAST_SEND") $arSqlOrder[] = " FS.LAST_SEND ".$order." ";
			else
			{
				$arSqlOrder[] = " FS.ID ".$order." ";
				$by = "ID";
			}
		}
		DelDuplicateSort($arSqlOrder); 
		if (count($arSqlOrder)>0)
			$strSqlOrder = " ORDER BY ".implode(", ", $arSqlOrder);
			
		$strSql = "
			SELECT FS.ID, FS.USER_ID, FS.FORUM_ID, FS.TOPIC_ID, FS.LAST_SEND, FS.NEW_TOPIC_ONLY, FS.SITE_ID, 
				".$DB->DateToCharFunction("FS.START_DATE", "FULL")." as START_DATE, 
				U.EMAIL, U.LOGIN, U.NAME, U.LAST_NAME, FT.TITLE, F.NAME AS FORUM_NAME".$strSqlSelect."
			 FROM b_forum_subscribe FS 
				INNER JOIN b_user U ON (FS.USER_ID = U.ID) 
				LEFT JOIN b_forum_topic FT ON (FS.TOPIC_ID = FT.ID) 
				LEFT JOIN b_forum F ON (FS.FORUM_ID = F.ID) 
				".$strSqlFrom." 
			WHERE 1 = 1 
				".$strSqlSearch." 
			".$strSqlGroup."
			".$strSqlOrder;

		$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		return $db_res;
	}

	function GetByID($ID)
	{
		global $DB;
		$ID = intVal($ID);

		$strSql = 
			"SELECT FP.ID, FP.USER_ID, FP.FORUM_ID, FP.TOPIC_ID, FP.LAST_SEND, FP.NEW_TOPIC_ONLY, FP.SITE_ID, ".
			"	".$DB->DateToCharFunction("FP.START_DATE", "FULL")." as START_DATE ".
			"FROM b_forum_subscribe FP ".
			"WHERE FP.ID = ".$ID."";
		$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		if ($res = $db_res->Fetch())
		{
			return $res;
		}
		return False;
	}
}

/**********************************************************************/
/************** RANK **************************************************/
/**********************************************************************/
class CAllForumRank
{
	//---------------> User insert, update, delete
	function CanUserAddRank($arUserGroups)
	{
		if (in_array(1, $arUserGroups)) return True;
		return False;
	}

	function CanUserUpdateRank($ID, $arUserGroups)
	{
		if (in_array(1, $arUserGroups)) return True;
		return False;
	}

	function CanUserDeleteRank($ID, $arUserGroups)
	{
		if (in_array(1, $arUserGroups)) return True;
		return False;
	}

	function CheckFields($ACTION, &$arFields)
	{
		if (is_set($arFields, "LANG") || $ACTION=="ADD")
		{
			for ($i = 0; $i<count($arFields["LANG"]); $i++)
			{
				if (!is_set($arFields["LANG"][$i], "LID") || strLen($arFields["LANG"][$i]["LID"])<=0) return false;
				if (!is_set($arFields["LANG"][$i], "NAME") || strLen($arFields["LANG"][$i]["NAME"])<=0) return false;
			}

			$db_lang = CLang::GetList(($b="sort"), ($o="asc"));
			while ($arLang = $db_lang->Fetch())
			{
				$bFound = False;
				for ($i = 0; $i<count($arFields["LANG"]); $i++)
				{
					if ($arFields["LANG"][$i]["LID"]==$arLang["LID"])
						$bFound = True;
				}
				if (!$bFound) return false;
			}
		}

		return True;
	}

	// Tekuwie statusy posetitelej srazu ne pereschityvayutsya. Tol'ko postepenno v processe raboty.
	function Update($ID, $arFields)
	{
		global $DB;
		$ID = intVal($ID);
		if ($ID <= 0) 
			return False;

		if (!CForumRank::CheckFields("UPDATE", $arFields))
			return false;

		$strUpdate = $DB->PrepareUpdate("b_forum_rank", $arFields);
		$strSql = "UPDATE b_forum_rank SET ".$strUpdate." WHERE ID = ".$ID;
		$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		if (is_set($arFields, "LANG"))
		{
			$DB->Query("DELETE FROM b_forum_rank_lang WHERE RANK_ID = ".$ID, false, "File: ".__FILE__."<br>Line: ".__LINE__);

			foreach ($arFields["LANG"] as $i => $val)
			{
				$arInsert = $DB->PrepareInsert("b_forum_rank_lang", $arFields["LANG"][$i]);
				$strSql = "INSERT INTO b_forum_rank_lang(RANK_ID, ".$arInsert[0].") VALUES(".$ID.", ".$arInsert[1].")";
				$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			}
		}
		return $ID;
	}
	
	function Delete($ID)
	{
		global $DB;
		$ID = intVal($ID);

		$arUsers = array();
		$db_res = CForumUser::GetList(array(), array("RANK_ID"=>$ID));
		while ($ar_res = $db_res->Fetch())
		{
			$arUsers[] = $ar_res["USER_ID"];
		}

		$DB->Query("DELETE FROM b_forum_rank_lang WHERE RANK_ID = ".$ID, True);
		$DB->Query("DELETE FROM b_forum_rank WHERE ID = ".$ID, True);

		for ($i = 0; $i < count($arUsers); $i++)
		{
			CForumUser::SetStat(intVal($arUsers[$i]));
		}

		return true;
	}

	function GetList($arOrder = array("MIN_NUM_POSTS"=>"ASC"), $arFilter = array())
	{
		global $DB;
		$arSqlSearch = array();
		$arSqlOrder = array();
		$strSqlSearch = "";
		$strSqlOrder = "";
		$arFilter = (is_array($arFilter) ? $arFilter : array());

		foreach ($arFilter as $key => $val)
		{
			$key_res = CForumNew::GetFilterOperation($key);
			$key = strtoupper($key_res["FIELD"]);
			$strNegative = $key_res["NEGATIVE"];
			$strOperation = $key_res["OPERATION"];

			switch ($key)
			{
				case "ID":
				case "MIN_NUM_POSTS":
					if (intVal($val)<=0)
						$arSqlSearch[] = ($strNegative=="Y"?"NOT":"")."(FR.".$key." IS NULL OR FR.".$key."<=0)";
					else
						$arSqlSearch[] = ($strNegative=="Y"?" FR.".$key." IS NULL OR NOT ":"")."(FR.".$key." ".$strOperation." ".intVal($val)." )";
					break;
			}
		}

		if (count($arSqlSearch) > 0)
			$strSqlSearch = " AND (".implode(") AND (", $arSqlSearch).") ";

		foreach ($arOrder as $by=>$order)
		{
			$by = strtoupper($by); $order = strtoupper($order);
			if ($order!="ASC") $order = "DESC";

			if ($by == "ID") $arSqlOrder[] = " FR.ID ".$order." ";
			else
			{
				$arSqlOrder[] = " FR.MIN_NUM_POSTS ".$order." ";
				$by = "MIN_NUM_POSTS";
			}
		}
		DelDuplicateSort($arSqlOrder); 
		if (count($arSqlOrder) > 0)
			$strSqlOrder = " ORDER BY ".implode(", ", $arSqlOrder);
			
		$strSql = 
			"SELECT FR.ID, FR.MIN_NUM_POSTS 
			FROM b_forum_rank FR 
			WHERE 1 = 1 
			".$strSqlSearch." 
			".$strSqlOrder;

		$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		return $db_res;
	}

	function GetListEx($arOrder = array("MIN_NUM_POSTS"=>"ASC"), $arFilter = array())
	{
		global $DB;
		$arSqlSearch = array();
		$arSqlOrder = array();
		$strSqlSearch = "";
		$strSqlOrder = "";
		$arFilter = (is_array($arFilter) ? $arFilter : array());

		foreach ($arFilter as $key => $val)
		{
			$key_res = CForumNew::GetFilterOperation($key);
			$key = strtoupper($key_res["FIELD"]);
			$strNegative = $key_res["NEGATIVE"];
			$strOperation = $key_res["OPERATION"];

			switch ($key)
			{
				case "ID":
				case "MIN_NUM_POSTS":
					if (intVal($val)<=0)
						$arSqlSearch[] = ($strNegative=="Y"?"NOT":"")."(FR.".$key." IS NULL OR FR.".$key."<=0)";
					else
						$arSqlSearch[] = ($strNegative=="Y"?" FR.".$key." IS NULL OR NOT ":"")."(FR.".$key." ".$strOperation." ".intVal($val)." )";
					break;
				case "LID":
					if (strLen($val)<=0)
						$arSqlSearch[] = ($strNegative=="Y"?"NOT":"")."(FRL.LID IS NULL OR ".($DB->type == "MSSQL" ? "LEN" : "LENGTH")."(FRL.LID)<=0)";
					else
						$arSqlSearch[] = ($strNegative=="Y"?" FRL.LID IS NULL OR NOT ":"")."(FRL.LID ".$strOperation." '".$DB->ForSql($val)."' )";
					break;
			}
		}
		if (count($arSqlSearch) > 0)
			$strSqlSearch = " AND (".imlode(" ) AND (", $arSqlSearch).") ";

		foreach ($arOrder as $by=>$order)
		{
			$by = strtoupper($by);	$order = strtoupper($order);
			if ($order!="ASC") $order = "DESC";

			if ($by == "ID") $arSqlOrder[] = " FR.ID ".$order." ";
			elseif ($by == "LID") $arSqlOrder[] = " FRL.LID ".$order." ";
			elseif ($by == "NAME") $arSqlOrder[] = " FRL.NAME ".$order." ";
			else
			{
				$arSqlOrder[] = " FR.MIN_NUM_POSTS ".$order." ";
				$by = "MIN_NUM_POSTS";
			}
		}
		DelDuplicateSort($arSqlOrder); 
		if (count($arSqlOrder) > 0)
			$strSqlOrder = " ORDER BY ".implode(", ", $arSqlOrder);

		$strSql = "
			SELECT FR.ID, FR.MIN_NUM_POSTS, FRL.LID, FRL.NAME 
			FROM b_forum_rank FR 
				LEFT JOIN b_forum_rank_lang FRL ON FR.ID = FRL.RANK_ID 
			WHERE 1 = 1 
			".$strSqlSearch."
			".$strSqlOrder;

		$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		return $db_res;
	}

	function GetByID($ID)
	{
		global $DB;

		$ID = intVal($ID);
		$strSql = 
			"SELECT FR.ID, FR.MIN_NUM_POSTS ".
			"FROM b_forum_rank FR ".
			"WHERE FR.ID = ".$ID."";
		$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		if ($res = $db_res->Fetch())
		{
			return $res;
		}
		return False;
	}

	function GetByIDEx($ID, $strLang)
	{
		global $DB;

		$ID = intVal($ID);
		$strSql = 
			"SELECT FR.ID, FRL.LID, FRL.NAME, FR.MIN_NUM_POSTS ".
			"FROM b_forum_rank FR ".
			"	LEFT JOIN b_forum_rank_lang FRL ON (FR.ID = FRL.RANK_ID AND FRL.LID = '".$DB->ForSql($strLang)."') ".
			"WHERE FR.ID = ".$ID."";
		$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		if ($res = $db_res->Fetch())
		{
			return $res;
		}
		return False;
	}

	function GetLangByID($RANK_ID, $strLang)
	{
		global $DB;

		$RANK_ID = intVal($RANK_ID);
		$strSql = 
			"SELECT FRL.ID, FRL.RANK_ID, FRL.LID, FRL.NAME ".
			"FROM b_forum_rank_lang FRL ".
			"WHERE FRL.RANK_ID = ".$RANK_ID." ".
			"	AND FRL.LID = '".$DB->ForSql($strLang)."' ";
		$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		if ($res = $db_res->Fetch())
		{
			return $res;
		}
		return False;
	}
	
	
}

class CALLForumStat
{
	function RegisterUSER_OLD($arFields = array())
	{
		global $DB, $USER;
		$tmp = "";
		if ($_SESSION["FORUM"]["SHOW_NAME"] == "Y" && strLen(trim($_SESSION["SESS_AUTH"]["NAME"])) > 0)
			$tmp = $_SESSION["SESS_AUTH"]["NAME"];
		else 
			$tmp = $_SESSION["SESS_AUTH"]["LOGIN"];
			
			
		$session_id = "'".$DB->ForSQL(session_id(), 255)."'";
		$Fields = array(
			"FS.USER_ID" => intVal($USER->GetID()), 
			"FS.IP_ADDRESS" => "'".$DB->ForSql($_SERVER["REMOTE_ADDR"],15)."'",
			"FS.SHOW_NAME" => "'".$DB->ForSQL($tmp, 255)."'",
			"FS.LAST_VISIT" => $DB->GetNowFunction(),
			"FS.FORUM_ID" => intVal($arFields["FORUM_ID"]),
			"FS.TOPIC_ID" => intVal($arFields["TOPIC_ID"])
			);
		$FieldsForInsert = array(
			"USER_ID" => $Fields["FS.USER_ID"], 
			"IP_ADDRESS" => $Fields["FS.IP_ADDRESS"],
			"SHOW_NAME" => $Fields["FS.SHOW_NAME"],
			"LAST_VISIT" => $Fields["FS.LAST_VISIT"],
			"FORUM_ID" => $Fields["FS.FORUM_ID"],
			"TOPIC_ID" => $Fields["FS.TOPIC_ID"],
			"PHPSESSID" => $session_id
			);
			
			
		if (intVal($USER->GetID()) > 0)
		{
			$FieldsForUpdate = $Fields;
			$FieldsForUpdate["FU.LAST_VISIT"] = $DB->GetNowFunction();
			$rows = $DB->Update(
				"b_forum_user FU, b_forum_stat FS", 
				$FieldsForUpdate, 
				"WHERE (FU.USER_ID=".$Fields["FS.USER_ID"].") AND (FS.PHPSESSID=".$session_id.")", 
				"File: ".__FILE__."<br>Line: ".__LINE__,
				false);
				
			if (intVal($rows) < 2)
			{
				if (intVal($rows)<=0)
				{
					$rows = $DB->Update(
						"b_forum_user", 
						array("USER_ID" => $Fields["FS.USER_ID"]), 
						"WHERE (USER_ID=".$Fields["FS.USER_ID"].")", 
						"File: ".__FILE__."<br>Line: ".__LINE__,
						false);
					if (intVal($rows) <= 0)
					{
						$ID = CForumUser::Add(array("USER_ID" => $Fields["FS.USER_ID"]));
					}
					
					$rows = $DB->Update(
						"b_forum_stat", 
						array(
							"USER_ID" => $Fields["FS.USER_ID"], 
							"IP_ADDRESS" => $Fields["FS.IP_ADDRESS"],
							"SHOW_NAME" => $Fields["FS.SHOW_NAME"],
							"LAST_VISIT" => $Fields["FS.LAST_VISIT"],
							"FORUM_ID" => $Fields["FS.FORUM_ID"],
							"TOPIC_ID" => $Fields["FS.TOPIC_ID"],
							), 
						"WHERE (PHPSESSID=".$session_id.")", 
						"File: ".__FILE__."<br>Line: ".__LINE__,
						false);
					if (intVal($rows) <= 0)
					{
						$DB->Insert("b_forum_stat", $FieldsForInsert, "File: ".__FILE__."<br>Line: ".__LINE__);
					}
				}
			}
		}
		else 
		{
			$rows = $DB->Update(
				"b_forum_stat", 
				array(
					"USER_ID" => $Fields["FS.USER_ID"], 
					"IP_ADDRESS" => $Fields["FS.IP_ADDRESS"],
					"SHOW_NAME" => $Fields["FS.SHOW_NAME"],
					"LAST_VISIT" => $Fields["FS.LAST_VISIT"],
					"FORUM_ID" => $Fields["FS.FORUM_ID"],
					"TOPIC_ID" => $Fields["FS.TOPIC_ID"],
					), 
				"WHERE (PHPSESSID=".$session_id.")", "File: ".__FILE__."<br>Line: ".__LINE__);		
				
			if (intVal($rows)<=0)
			{
				$DB->Insert("b_forum_stat", $FieldsForInsert, "File: ".__FILE__."<br>Line: ".__LINE__);
			}	
		}
		return true;
	}
	
	function RegisterUSER($arFields = array())
	{
		global $DB, $USER;
		$tmp = "";
		if ($_SESSION["FORUM"]["SHOW_NAME"] == "Y" && strLen(trim($_SESSION["SESS_AUTH"]["NAME"])) > 0)
			$tmp = $_SESSION["SESS_AUTH"]["NAME"];
		else 
			$tmp = $_SESSION["SESS_AUTH"]["LOGIN"];
		$session_id = "'".$DB->ForSQL(session_id(), 255)."'";
		$Fields = array(
			"USER_ID" => intVal($USER->GetID()), 
			"IP_ADDRESS" => "'".$DB->ForSql($_SERVER["REMOTE_ADDR"], 15)."'",
			"SHOW_NAME" => "'".$DB->ForSQL($tmp, 255)."'",
			"LAST_VISIT" => $DB->GetNowFunction(),
			"SITE_ID" => "'".$DB->ForSQL($arFields["SITE_ID"], 2)."'", 
			"FORUM_ID" => intVal($arFields["FORUM_ID"]),
			"TOPIC_ID" => intVal($arFields["TOPIC_ID"]));
		$rows = $DB->Update("b_forum_stat", $Fields, "WHERE PHPSESSID=".$session_id."", "File: ".__FILE__."<br>Line: ".__LINE__);
		if (intVal($rows)<=0)
		{
			$Fields = array(
				"USER_ID" => intVal($USER->GetID()), 
				"IP_ADDRESS" => "'".$DB->ForSql($_SERVER["REMOTE_ADDR"], 15)."'",
				"SHOW_NAME" => "'".$DB->ForSQL($tmp, 255)."'",
				"PHPSESSID" => "'".$DB->ForSQL(session_id(), 255)."'", 
				"LAST_VISIT" => $DB->GetNowFunction(),
				"SITE_ID" => "'".$DB->ForSQL($arFields["SITE_ID"], 2)."'", 
				"FORUM_ID" => intVal($arFields["FORUM_ID"]),
				"TOPIC_ID" => intVal($arFields["TOPIC_ID"]));
			return $DB->Insert("b_forum_stat", $Fields, "File: ".__FILE__."<br>Line: ".__LINE__);
		}
		else
			return true;
	}
	
	function Add($arFields)
	{
		global $DB, $USER;
		$Fields = array(
			"USER_ID" => $USER->GetID(), 
			"IP_ADDRESS" => "'".$DB->ForSql($_SERVER["REMOTE_ADDR"],15)."'",
			"PHPSESSID" => "'".$DB->ForSQL(session_id(), 255)."'", 
			"LAST_VISIT" => "'".$DB->GetNowFunction()."'",
			"FORUM_ID" => intVal($arFields["FORUM_ID"]),
			"TOPIC_ID" => intVal($arFields["TOPIC_ID"]));

		return $DB->Insert("b_forum_stat", $Fields, "File: ".__FILE__."<br>Line: ".__LINE__);
	}
	
	function GetListEx($arOrder = Array("ID"=>"ASC"), $arFilter = Array())
	{
		global $DB;
		$arSqlSearch = array();
		$arSqlSelect = array();
		$arSqlFrom = array();
		$arSqlGroup = array();
		$arSqlOrder = array();
		$arSql = array(); 
		$strSqlSearch = "";
		$strSqlSelect = "";
		$strSqlFrom = "";
		$strSqlGroup = "";
		$strSqlOrder = "";
		$strSql = "";
		
		$arSqlSelectConst = array(
			"FSTAT.USER_ID" => "FSTAT.USER_ID", 
			"FSTAT.IPADDRES" => "FSTAT.IPADDRES", 
			"FSTAT.PHPSESSID" => "FSTAT.PHPSESSID", 
			"LAST_VISIT" => $DB->DateToCharFunction("FSTAT.LAST_VISIT", "FULL"), 
			"FSTAT.FORUM_ID" => "FSTAT.FORUM_ID",
			"FSTAT.TOPIC_ID" => "FSTAT.TOPIC_ID"
		);
		$arSqlSelect = $arSqlSelectConst;
		$arFilter = (is_array($arFilter) ? $arFilter : array());

		foreach ($arFilter as $key => $val)
		{
			$key_res = CForumNew::GetFilterOperation($key);
			$key = strtoupper($key_res["FIELD"]);
			$strNegative = $key_res["NEGATIVE"];
			$strOperation = $key_res["OPERATION"];

			switch ($key)
			{
				case "TOPIC_ID":
				case "FORUM_ID":
				case "USER_ID":
					if (intVal($val)<=0)
						$arSqlSearch[] = ($strNegative=="Y"?"NOT":"")."(FSTAT.".$key." IS NULL OR FSTAT.".$key."<=0)";
					else
						$arSqlSearch[] = ($strNegative=="Y"?" FSTAT.".$key." IS NULL OR NOT ":"")."(FSTAT.".$key." ".$strOperation." ".intVal($val)." )";
					break;
				case "LAST_VISIT":
					if(strLen($val)<=0)
						$arSqlSearch[] = ($strNegative=="Y"?"NOT":"")."(FSTAT.".$key." IS NULL)";
					else
						$arSqlSearch[] = ($strNegative=="Y"?" FSTAT.".$key." IS NULL OR NOT ":"")."(FSTAT.".$key." ".$strOperation." ".$DB->CharToDateFunction($DB->ForSql($val), "FULL").")";
					break;
				case "HIDE_FROM_ONLINE":
					$arSqlFrom["FU"] = "LEFT JOIN b_forum_user FU ON FSTAT.USER_ID=FU.USER_ID";
					if (strLen($val)<=0)
						$arSqlSearch[] = ($strNegative=="Y"?"NOT":"")."(FU.".$key." IS NULL OR ".($DB->type == "MSSQL" ? "LEN" : "LENGTH")."(FU.".$key.")<=0)";
					else
						$arSqlSearch[] = ($strNegative=="Y"?" FU.".$key." IS NULL OR NOT ":"")."(FU.".$key." ".$strOperation." '".$DB->ForSql($val)."' )";
					break;
				break;
				case "COUNT_GUEST":
					$arSqlSelect = array(
						"FSTAT.USER_ID" => "FSTAT.USER_ID", 
						"FSTAT.SHOW_NAME" => "FSTAT.SHOW_NAME", 
						"COUNT_USER" => "COUNT(FSTAT.PHPSESSID) AS COUNT_USER", 
					);
					$arSqlGroup["FSTAT.USER_ID"] = "FSTAT.USER_ID";
					$arSqlGroup["FSTAT.SHOW_NAME"] = "FSTAT.SHOW_NAME";
					break;
			}
		}
		if (count($arSqlSearch) > 0)
			$strSqlSearch = " AND (".implode(") AND (", $arSqlSearch).") ";
		if (count($arSqlSelect) > 0)
			$strSqlSelect = implode(", ", $arSqlSelect);
		if (count($arSqlFrom) > 0)
			$strSqlFrom = implode("	", $arSqlFrom);
		if (count($arSqlGroup) > 0)
			$strSqlGroup = " GROUP BY ".implode(", ", $arSqlGroup);


		foreach ($arOrder as $by=>$order)
		{
			$by = strtoupper($by); $order = strtoupper($order);
			$order = $order!="ASC" ? $order = "DESC" : "ASC";

			if ($by == "USER_ID") $arSqlOrder[] = " FSTAT.USER_ID ".$order." ";
		}

		DelDuplicateSort($arSqlOrder); 
		if (count($arSqlOrder) > 0)
			$strSqlOrder = " ORDER BY ".implode(", ", $arSqlOrder);

		$strSql = " SELECT ".$strSqlSelect."
			FROM b_forum_stat FSTAT
			".$strSqlFrom."
			WHERE 1=1 
			".$strSqlSearch."
			".$strSqlGroup."
			".$strSqlOrder;
			
		$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		return $db_res;
	}
	
	function CleanUp($period = 48) // time in hours
	{
		global $DB;
		$period = intVal($period)*3600;
		$date = $DB->CharToDateFunction($DB->ForSql(Date(CDatabase::DateFormatToPHP(CLang::GetDateFormat("FULL", LANGUAGE_ID)), time()-$period)), "FULL") ;
		$strSQL = "DELETE FROM b_forum_stat 
					WHERE (LAST_VISIT
					< ".$date.")";
		$DB->Query($strSQL, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		return "CForumStat::CleanUp();";
	}
}

?>