<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
// *****************************************************************************************
//Входные параметры
// $bVarsFromForm -
// arUserField USER_TYPE - тип свойства
// arUserField VALUE- значение пользовательского свойства
// *****************************************************************************************
$arParams["bVarsFromForm"] = $arParams["bVarsFromForm"] ? true:false;
$arResult["VALUE"] = false;
// *****************************************************************************************

if($arParams["arUserField"]["USER_TYPE"])
{
	if(!$arParams["bVarsFromForm"])
	{
		if($arParams["arUserField"]["ENTITY_VALUE_ID"]<1 && strlen($arParams["arUserField"]["SETTINGS"]["DEFAULT_VALUE"]["VALUE"])>0)
			$arResult["VALUE"] = $arParams["~arUserField"]["SETTINGS"]["DEFAULT_VALUE"];
		else
			$arResult["VALUE"] = $arParams["~arUserField"]["VALUE"];
	}
	else
	{
		if($arParams["arUserField"]["USER_TYPE"]["BASE_TYPE"]=="file")
			$arResult["VALUE"] = $GLOBALS[$arParams["arUserField"]["FIELD_NAME"]."_old_id"];
		else
			$arResult["VALUE"] = $_REQUEST[$arParams["arUserField"]["FIELD_NAME"]];
	}
	if (!is_array($arResult["VALUE"]))
		$arResult["VALUE"] = array($arResult["VALUE"]);
	if (empty($arResult["VALUE"]))
		$arResult["VALUE"] = array(null);

	foreach ($arResult["VALUE"] as $key => $res)
	{
		switch ($arParams["arUserField"]["USER_TYPE"]["BASE_TYPE"])
		{
			case "double":
				if (strlen($res)>0)
					$res = round(doubleval($res), $arParams["arUserField"]["SETTINGS"]["PRECISION"]);
				break;
			case "int":
				$res = intVal($res);
				break;
			default:
				$res = htmlspecialchars($res);
				break;
		}
		$arResult["VALUE"][$key] = $res;
	}

	$arParams["arUserField"]["~FIELD_NAME"] = $arParams["arUserField"]["FIELD_NAME"];
	if ($arParams["arUserField"]["MULTIPLE"]=="Y")
	{
		$arParams["arUserField"]["~FIELD_NAME"] = $arParams["arUserField"]["FIELD_NAME"];
		$arParams["arUserField"]["FIELD_NAME"] .= "[]";

		if (!empty($arResult["VALUE"]) && (!empty($arResult["VALUE"][count($arResult["VALUE"])-1])))
		{
			$arResult["VALUE"][] = null;
		}
	}

	//if ($arParams["arUserField"]["USER_TYPE"]["USER_TYPE_ID"] == "enumeration")
	if (is_callable(array($arParams["arUserField"]["USER_TYPE"]['CLASS_NAME'], 'getlist')))
	{
		$enum = array();

		if(
			($arParams["arUserField"]["MANDATORY"] != "Y")
			&& ($arParams["arUserField"]["SETTINGS"]["DISPLAY"] != "CHECKBOX")
		):
			$enum = array(null=>GetMessage("MAIN_NO"));
		endif;

		$rsEnum = call_user_func_array(
			array($arParams['arUserField']["USER_TYPE"]["CLASS_NAME"], "getlist"),
			array(
				$arParams['arUserField'],
			)
		);

		if(!$arParams["bVarsFromForm"] && ($arParams["arUserField"]["ENTITY_VALUE_ID"] <= 0))
			$arResult["VALUE"] = array();

		while($arEnum = $rsEnum->GetNext())
		{
			$enum[$arEnum["ID"]] = $arEnum["VALUE"];
			if(!$arParams["bVarsFromForm"] && ($arParams["arUserField"]["ENTITY_VALUE_ID"] <= 0))
			{
				if($arEnum["DEF"] == "Y")
					$arResult["VALUE"][] = $arEnum["ID"];
			}
		}
		$arParams["arUserField"]["USER_TYPE"]["FIELDS"] = $enum;
	}

	$arParams["form_name"] = !empty($arParams["form_name"]) ? $arParams["form_name"] : "form1";
	$this->IncludeComponentTemplate();
}?>