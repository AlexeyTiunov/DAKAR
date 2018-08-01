<?
IncludeModuleLangFile(__FILE__);
class CSitePersonal
{
	function ShowPanel()
	{
		if ($GLOBALS["USER"]->IsAdmin() && COption::GetOptionString("main", "wizard_solution", "", SITE_ID) == "personal")
		{
			$GLOBALS["APPLICATION"]->SetAdditionalCSS("/bitrix/wizards/bitrix/demo_personal/css/panel.css"); 
			$GLOBALS["APPLICATION"]->AddPanelButton(array(
				"HREF" => "/bitrix/admin/wizard_install.php?lang=ru&wizardName=bitrix:demo_personal&wizardSiteID=".SITE_ID."&".bitrix_sessid_get(),
				"ID" => "demo_personal_wizard",
				"ICON" => "icon-wizard",
				"ALT" => GetMessage("SPER_BUTTON_DESCRIPTION"),
				"TEXT" => GetMessage("SPER_BUTTON_NAME"),
				"MAIN_SORT" => 335,
				"SORT" => 30,
				"MENU" => array(),
			));
		}
	}
}
?>