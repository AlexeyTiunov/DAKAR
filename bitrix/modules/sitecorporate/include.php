<?
IncludeModuleLangFile(__FILE__);
class CSiteCorporate
{
	function ShowPanel()
	{
		if ($GLOBALS["USER"]->IsAdmin() && COption::GetOptionString("main", "wizard_solution", "", SITE_ID) == "corp_services")
		{
			$GLOBALS["APPLICATION"]->SetAdditionalCSS("/bitrix/wizards/bitrix/corp_services/css/panel.css"); 
			$GLOBALS["APPLICATION"]->AddPanelButton(array(
				"HREF" => "/bitrix/admin/wizard_install.php?lang=ru&wizardName=bitrix:corp_services&wizardSiteID=".SITE_ID."&".bitrix_sessid_get(),
				"ID" => "corp_services_wizard",
				"ICON" => "icon-wizard",
				"ALT" => GetMessage("SCOM_BUTTON_DESCRIPTION"),
				"TEXT" => GetMessage("SCOM_BUTTON_NAME"),
				"MAIN_SORT" => 325,
				"SORT" => 20,
				"MENU" => array(),
			));
		}
	}
}
?>