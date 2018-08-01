<?
class CAllCurrencyLang
{
	function Add($arFields)
	{
		global $DB;

		$arInsert = $DB->PrepareInsert("b_catalog_currency_lang", $arFields);

		$strSql =
			"INSERT INTO b_catalog_currency_lang(".$arInsert[0].") ".
			"VALUES(".$arInsert[1].")";
		$DB->Query($strSql);

		unset($GLOBALS["MAIN_CURRENCY_LIST_CACHE"]);
		return true;
	}

	function Update($currency, $lang, $arFields)
	{
		global $DB;

		unset($GLOBALS["MAIN_CURRENCY_CACHE"][$currency]["LANG"][$lang]);

		$strUpdate = $DB->PrepareUpdate("b_catalog_currency_lang", $arFields);
		$strSql = "UPDATE b_catalog_currency_lang SET ".$strUpdate." WHERE CURRENCY = '".$DB->ForSql($currency, 3)."' AND LID='".$DB->ForSql($lang, 2)."'";
		$DB->Query($strSql);

		if (isset($GLOBALS["MAIN_CURRENCY_CACHE"][$currency]))
		{
			$GLOBALS["MAIN_CURRENCY_CACHE"][$currency]["LANG"][$arFields["LID"]] = $arFields;
		}

		unset($GLOBALS["MAIN_CURRENCY_LIST_CACHE"]);
		return true;
	}

	function Delete($currency, $lang)
	{
		global $DB;

		unset($GLOBALS["MAIN_CURRENCY_CACHE"][$currency]["LANG"][$lang]);

		$strSql = "DELETE FROM b_catalog_currency_lang ".
			"WHERE CURRENCY = '".$DB->ForSql($currency, 3)."' ".
			"	AND LID = '".$DB->ForSql($lang, 2)."' ";
		$DB->Query($strSql);

		unset($GLOBALS["MAIN_CURRENCY_LIST_CACHE"]);
		return true;
	}

	function GetByID($currency, $lang)
	{
		global $DB;

		$strSql = 
			"SELECT * ".
			"FROM b_catalog_currency_lang ".
			"WHERE CURRENCY = '".$DB->ForSql($currency, 3)."' ".
			"	AND LID = '".$DB->ForSql($lang, 2)."' ";
		$db_res = $DB->Query($strSql);

		if ($res = $db_res->Fetch())
		{
			return $res;
		}
		return false;
	}

	function GetCurrencyFormat($currency, $lang = LANG)
	{
		global $DB;
		if (!isset($GLOBALS["MAIN_CURRENCY_CACHE"][$currency]["LANG"][$lang]["FORMAT_STRING"]))
		{
			$res = CCurrencyLang::GetByID($currency, $lang);
			$GLOBALS["MAIN_CURRENCY_CACHE"][$currency]["LANG"][$lang] = $res;
		}
		return $GLOBALS["MAIN_CURRENCY_CACHE"][$currency]["LANG"][$lang];
	}

	function GetList(&$by, &$order, $currency = "")
	{
		global $DB;

		$strSql =
			"SELECT CURL.CURRENCY, CURL.LID, CURL.FORMAT_STRING, CURL.FULL_NAME, CURL.DEC_POINT, CURL.THOUSANDS_SEP, CURL.DECIMALS ".
			"FROM b_catalog_currency_lang CURL ";

		if (strlen($currency)>0)
		{
			$strSql .= "WHERE CURL.CURRENCY = '".$DB->ForSql($currency, 3)."' ";
		}

		if (strtolower($by) == "currency") $strSqlOrder = " ORDER BY CUR.CURRENCY ";
		elseif (strtolower($by) == "name") $strSqlOrder = " ORDER BY CURL.FULL_NAME ";
		else
		{
			$strSqlOrder = " ORDER BY CUR.LID "; 
			$by = "lang";
		}

		if ($order=="desc") 
			$strSqlOrder .= " desc "; 
		else
			$order = "asc"; 

		$strSql .= $strSqlOrder;
		$res = $DB->Query($strSql);

		return $res;
	}
}





class CAllCurrency
{
	function GetCurrency($currency)
	{
		if (!isset($GLOBALS["MAIN_CURRENCY_CACHE"][$currency]))
		{
			$res = CCurrency::GetByID($currency);
			$res = $res->Fetch();
			$GLOBALS["MAIN_CURRENCY_CACHE"][$res["CURRENCY"]] = $res;
		}

		return $GLOBALS["MAIN_CURRENCY_CACHE"][$currency]["CURRENCY"];
	}

	function Add($arFields)
	{
		global $DB;

		$db_result = $DB->Query("SELECT 'x' FROM b_catalog_currency WHERE CURRENCY = '".$DB->ForSql($arFields["CURRENCY"], 3)."'");
		if ($db_result->Fetch())
			return false;
		else
		{
			$arInsert = $DB->PrepareInsert("b_catalog_currency", $arFields);

			$strSql =
				"INSERT INTO b_catalog_currency(".$arInsert[0].", DATE_UPDATE) ".
				"VALUES(".$arInsert[1].", ".$DB->GetNowFunction().")";
			$DB->Query($strSql);
		}

		unset($GLOBALS["MAIN_CURRENCY_LIST_CACHE"]);
		return $arFields["CURRENCY"];
	}

	function Update($currency, $arFields)
	{
		global $DB;
		UnSet($GLOBALS["MAIN_CURRENCY_CACHE"][$currency]);
		UnSet($GLOBALS["MAIN_BASE_CURRENCY"]);
		$bCanUpdate = False;
		if ($currency==$arFields["CURRENCY"])
		{
			$strUpdate = $DB->PrepareUpdate("b_catalog_currency", $arFields);
			$strSql = "UPDATE b_catalog_currency SET ".$strUpdate.", DATE_UPDATE = ".$DB->GetNowFunction()." WHERE CURRENCY = '".$DB->ForSql($currency, 3)."' ";
			$DB->Query($strSql);
			unset($GLOBALS["MAIN_CURRENCY_LIST_CACHE"]);
			return $arFields["CURRENCY"];
		}
		else
		{
			die("RYH76T85RF45");
			if (CCurrency::Delete($currency))
			{
				return CCurrency::Add($arFields);
			}
			else
			{
				return False;
			}
		}
	}

	function Delete($currency)
	{
		global $DB;

		//проверка - оставил ли тут кто-нибудь обработчик на OnBeforeDelete
		$bCanDelete = true;
		$db_events = GetModuleEvents("catalog", "OnBeforeCurrencyDelete");
		while($arEvent = $db_events->Fetch())
			if(ExecuteModuleEvent($arEvent, $currency)===false)
				return false;

		//проверка - оставил ли тут какой-нибудь модуль обработчик на OnDelete
		$events = GetModuleEvents("catalog", "OnCurrencyDelete");
		while($arEvent = $events->Fetch())
			ExecuteModuleEvent($arEvent, $currency);

		$DB->Query("DELETE FROM b_catalog_currency_lang WHERE CURRENCY = '".$DB->ForSQL($currency, 3)."'", true);
		$DB->Query("DELETE FROM b_catalog_currency_rate WHERE CURRENCY = '".$DB->ForSQL($currency, 3)."'", true);

		unset($GLOBALS["MAIN_CURRENCY_LIST_CACHE"]);
		return $DB->Query("DELETE FROM b_catalog_currency WHERE CURRENCY = '".$DB->ForSQL($currency, 3)."'", true);
	}


	function GetByID($currency)
	{
		global $DB;

		$strSql =
			"SELECT CUR.* ".
			"FROM b_catalog_currency CUR ".
			"WHERE CUR.CURRENCY = '".$DB->ForSQL($currency, 3)."'";
		$db_res = $DB->Query($strSql);

		if ($res = $db_res->Fetch())
		{
			return $res;
		}
		return False;
	}


	function GetBaseCurrency()
	{
		global $DB;

		if (!isset($GLOBALS["MAIN_BASE_CURRENCY"]) || strlen($GLOBALS["MAIN_BASE_CURRENCY"])<=0)
		{
			$strSql = "SELECT CURRENCY FROM b_catalog_currency WHERE AMOUNT = 1 ";
			$db_res = $DB->Query($strSql);
			if ($res = $db_res->Fetch())
				$GLOBALS["MAIN_BASE_CURRENCY"] = $res["CURRENCY"];
		}
		if (isset($GLOBALS["MAIN_BASE_CURRENCY"]) && strlen($GLOBALS["MAIN_BASE_CURRENCY"])>0)
		{
			return $GLOBALS["MAIN_BASE_CURRENCY"];
		}
		return "";
	}


	function SelectBox($sFieldName, $sValue, $sDefaultValue = "", $bFullName = True, $JavaFunc = "", $sAddParams = "")
	{
		if (!isset($GLOBALS["MAIN_CURRENCY_LIST_CACHE"]) || !is_array($GLOBALS["MAIN_CURRENCY_LIST_CACHE"]) || count($GLOBALS["MAIN_CURRENCY_LIST_CACHE"])<1)
		{
			unset($GLOBALS["MAIN_CURRENCY_LIST_CACHE"]);
			$l = CCurrency::GetList(($by="sort"), ($order="asc"));
			while ($l_res = $l->Fetch())
			{
				$GLOBALS["MAIN_CURRENCY_LIST_CACHE"][] = $l_res;
			}
		}
		$s = '<select name="'.$sFieldName.'"';
		if (strlen($sAddParams)>0) $s .= ' '.$sAddParams.'';
		if (strlen($JavaFunc)>0) $s .= ' OnChange="'.$JavaFunc.'"';
		$s .= '>'."\n";
		$found = false;
		for ($i=0; $i<count($GLOBALS["MAIN_CURRENCY_LIST_CACHE"]); $i++)
		{
			$found = ($GLOBALS["MAIN_CURRENCY_LIST_CACHE"][$i]["CURRENCY"] == $sValue);
			$s1 .= '<option value="'.$GLOBALS["MAIN_CURRENCY_LIST_CACHE"][$i]["CURRENCY"].'"'.($found ? ' selected':'').'>'.htmlspecialchars($GLOBALS["MAIN_CURRENCY_LIST_CACHE"][$i]["CURRENCY"]).(($bFullName)?(' ('.htmlspecialchars($GLOBALS["MAIN_CURRENCY_LIST_CACHE"][$i]["FULL_NAME"]).')'):"").'</option>'."\n";
		}
		if (strlen($sDefaultValue)>0) 
			$s .= "<option value='' ".($found ? "" : "selected").">".htmlspecialchars($sDefaultValue)."</option>";
		return $s.$s1.'</select>';
	}
}




class CAllCurrencyRates
{
	function Add($arFields)
	{
		global $DB;

		$db_result = $DB->Query("SELECT 'x' ".
			"FROM b_catalog_currency_rate ".
			"WHERE CURRENCY = '".$DB->ForSql($arFields["CURRENCY"], 3)."' ".
			"	AND DATE_RATE = ".$DB->CharToDateFunction($DB->ForSql($arFields["DATE_RATE"]), "SHORT")." ");
		if ($db_result->Fetch())
			return false;
		else
		{
			$arInsert = $DB->PrepareInsert("b_catalog_currency_rate", $arFields);

			$strSql =
				"INSERT INTO b_catalog_currency_rate(".$arInsert[0].") ".
				"VALUES(".$arInsert[1].")";
			$DB->Query($strSql);
		}
		return true;
	}

	function Update($ID, $arFields)
	{
		global $DB;
		$ID = IntVal($ID);

		$db_result = $DB->Query("SELECT 'x' ".
			"FROM b_catalog_currency_rate ".
			"WHERE CURRENCY = '".$DB->ForSql($arFields["CURRENCY"], 3)."' ".
			"	AND DATE_RATE = ".$DB->CharToDateFunction($DB->ForSql($arFields["DATE_RATE"]), "SHORT")." ".
			"	AND ID<>".$ID." ");
		if ($db_result->Fetch())
		{
			return false;
		}
		else
		{
			$strUpdate = $DB->PrepareUpdate("b_catalog_currency_rate", $arFields);
			$strSql = "UPDATE b_catalog_currency_rate SET ".$strUpdate." WHERE ID = ".$ID." ";
			$DB->Query($strSql);
		}
		return true;
	}

	function Delete($ID)
	{
		global $DB;

		$ID = IntVal($ID);
		$strSql = "DELETE FROM b_catalog_currency_rate WHERE ID = ".$ID." ";
		$DB->Query($strSql);

		return true;
	}

	function GetByID($ID)
	{
		global $DB;

		$ID = IntVal($ID);
		$strSql = 
			"SELECT * ".
			"FROM b_catalog_currency_rate ".
			"WHERE ID = ".$ID." ";
		$db_res = $DB->Query($strSql);

		if ($res = $db_res->Fetch())
		{
			return $res;
		}
		return false;
	}


	function GetList(&$by, &$order, $arFilter=Array())
	{
		global $DB;
		$arSqlSearch = Array();

		if(!is_array($arFilter)) 
			$filter_keys = Array();
		else
			$filter_keys = array_keys($arFilter);

		for($i=0; $i<count($filter_keys); $i++)
		{
			$val = $DB->ForSql($arFilter[$filter_keys[$i]]);
			if (strlen($val)<=0) continue;

			$key = $filter_keys[$i];
			if ($key[0]=="!")
			{
				$key = substr($key, 1);
				$bInvert = true;
			}
			else
				$bInvert = false;

			switch(strtoupper($key))
			{
			case "CURRENCY":
				$arSqlSearch[] = "C.CURRENCY = '".$val."'";
				break;
			case "DATE_RATE":
				$arSqlSearch[] = "(C.DATE_RATE ".($bInvert?"<":">=").$DB->CharToDateFunction($DB->ForSql($val), "SHORT").($bInvert?"":" OR C.DATE_RATE IS NULL").")";
				break;
			}
		}

		$strSqlSearch = "";
		for($i=0; $i<count($arSqlSearch); $i++)
		{
			if($i>0)
				$strSqlSearch .= " AND ";
			else
				$strSqlSearch = " WHERE ";

			$strSqlSearch .= " (".$arSqlSearch[$i].") ";
		}

		$strSql =
			"SELECT C.ID, C.CURRENCY, C.RATE_CNT, C.RATE, ".$DB->DateToCharFunction("C.DATE_RATE", "SHORT")." as DATE_RATE ".
			"FROM b_catalog_currency_rate C ".
			$strSqlSearch;

		if (strtolower($by) == "curr") $strSqlOrder = " ORDER BY C.CURRENCY ";
		elseif (strtolower($by) == "rate") $strSqlOrder = " ORDER BY C.RATE ";
		else
		{
			$strSqlOrder = " ORDER BY C.DATE_RATE "; 
			$by = "date";
		}

		if (strtolower($order)=="desc") 
			$strSqlOrder .= " desc "; 
		else
			$order = "asc"; 

		$strSql .= $strSqlOrder;
		$res = $DB->Query($strSql);

		return $res;
	}

}

?>