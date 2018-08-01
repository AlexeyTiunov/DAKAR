<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;

$arDefaultUrlTemplates404 = array(
	"faq" => "",
	"element" => "#SECTION_ID#/#ELEMENT_ID#/",
	"section" => "#SECTION_ID#/",
);

$arDefaultVariableAliases404 = Array(
	"faq"=>array(),
	"section"=>array("SECTION_ID" => "SECTION_ID"),
	"element"=>array("SECTION_ID" => "SECTION_ID", "ELEMENT_ID" => "ELEMENT_ID"),
);

$arComponentVariables = Array(
	"SECTION_ID",
	"ELEMENT_ID",
	"q",
);

$arDefaultVariableAliases = Array(
	"SECTION_ID"=>"SECTION_ID",
	"ELEMENT_ID"=>"ELEMENT_ID",
	"q"=>"q",
);

if($arParams["SEF_MODE"] == "Y")
{
	$arUrlTemplates = CComponentEngine::MakeComponentUrlTemplates($arDefaultUrlTemplates404, $arParams["SEF_URL_TEMPLATES"]);
	$arVariableAliases = CComponentEngine::MakeComponentVariableAliases($arDefaultVariableAliases404, $arParams["VARIABLE_ALIASES"]);

	$componentPage = CComponentEngine::ParseComponentPath(
		$arParams["SEF_FOLDER"],
		$arUrlTemplates,
		$arVariables
	);

	if(!$componentPage)
		$componentPage = "faq";

	CComponentEngine::InitComponentVariables($componentPage, $arComponentVariables, $arVariableAliases, $arVariables);
	$arResult = array(
			"FOLDER" => $arParams["SEF_FOLDER"],
			"URL_TEMPLATES" => $arUrlTemplates,
			"VARIABLES" => $arVariables,
			"ALIASES" => $arVariableAliases
		);
}
else
{
	$arVariableAliases = CComponentEngine::MakeComponentVariableAliases($arDefaultVariableAliases, $arParams["VARIABLE_ALIASES"]);
	CComponentEngine::InitComponentVariables(false, $arComponentVariables, $arVariableAliases, $arVariables);

	$componentPage = "";

	if(isset($arVariables["ELEMENT_ID"]) && intval($arVariables["ELEMENT_ID"]) > 0 && isset($arVariables["SECTION_ID"]) && intval($arVariables["SECTION_ID"]) > 0)
		$componentPage = "element";
	elseif(isset($arVariables["SECTION_ID"]) && intval($arVariables["SECTION_ID"]) > 0)
		$componentPage = "section";
	else
		$componentPage = "faq";

	$arResult = array(
			"FOLDER" => "",
			"URL_TEMPLATES" => Array(
				"news" => htmlspecialchars($APPLICATION->GetCurPage()),
				"section" => htmlspecialchars($APPLICATION->GetCurPage()."?".$arVariableAliases["SECTION_ID"]."=#SECTION_ID#"),
				"detail" => htmlspecialchars($APPLICATION->GetCurPage()."?".$arVariableAliases["ELEMENT_ID"]."=#ELEMENT_ID#"),
			),
			"VARIABLES" => $arVariables,
			"ALIASES" => $arVariableAliases
		);
}

$this->IncludeComponentTemplate($componentPage);
?>