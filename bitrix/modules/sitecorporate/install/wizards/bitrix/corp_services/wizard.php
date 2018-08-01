<?
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/install/wizard_sol/wizard.php");

class SelectSiteStep extends CSelectSiteWizardStep
{
	function InitStep()
	{
		parent::InitStep();

		$wizard =& $this->GetWizard();
		$wizard->solutionName = "corp_services";
	}
}


class SelectTemplateStep extends CSelectTemplateWizardStep
{
}

class SelectThemeStep extends CSelectThemeWizardStep
{

}

class SiteSettingsStep extends CSiteSettingsWizardStep
{
	function InitStep()
	{
		$wizard =& $this->GetWizard();
		$wizard->solutionName = "corp_services";
		parent::InitStep();

		$templateID = $wizard->GetVar("templateID");
		$themeID = $wizard->GetVar($templateID."_themeID");

		$siteLogo = $this->GetFileContentImgSrc(WIZARD_SITE_PATH."include/company_name.php", "/bitrix/wizards/bitrix/corp_services/site/templates/corp_services/themes/".$themeID."/lang/".LANGUAGE_ID."/logo.gif");
		$siteBanner = $this->GetFileContentImgSrc(WIZARD_SITE_PATH."include/banner.php", "/bitrix/wizards/bitrix/corp_services/site/templates/corp_services/images/banner.png");
		
		$wizard->SetDefaultVars(
			Array(
				"siteLogo" => $siteLogo,
				"siteBanner" => $siteBanner, 
				"siteBannerText" => $this->GetFileContent(WIZARD_SITE_PATH."include/banner_text.php", GetMessage("WIZ_BANNER_TEXT_DEFAULT")),
				"siteSlogan" => $this->GetFileContent(WIZARD_SITE_PATH."include/company_slogan.php", GetMessage("WIZ_COMPANY_SLOGAN_DEF")),
				"siteCopy" => $this->GetFileContent(WIZARD_SITE_PATH."include/copyright.php", GetMessage("WIZ_COMPANY_COPY_DEF")),
			)
		);
	}

	function ShowStep()
	{
		$wizard =& $this->GetWizard();
				
		$siteLogo = $wizard->GetVar("siteLogo", true);

		$this->content .= '<table width="100%" cellspacing="0" cellpadding="0">';
		$this->content .= '<tr><td>';
		$this->content .= '<label for="site-logo">'.GetMessage("WIZ_COMPANY_LOGO").'</label><br />';
		$this->content .= CFile::ShowImage($siteLogo, 190, 70, "border=0 vspace=15");
		$this->content .= "<br />".$this->ShowFileField("siteLogo", Array("show_file_info" => "N", "id" => "site-logo"));
		$this->content .= '</tr></td>';

		$this->content .= '<tr><td><br /><br /><br /></td></tr>';
		
		$siteBanner = $wizard->GetVar("siteBanner", true);

		$this->content .= '<tr><td>';
		$this->content .= '<label for="site-banner">'.GetMessage("WIZ_SITE_BANNER").'</label><br />';
		$this->content .= CFile::ShowImage($siteBanner, 580, 175, "border=0 vspace=15");
		$this->content .= "<br />".$this->ShowFileField("siteBanner", Array("show_file_info" => "N", "id" => "site-banner"));
		$this->content .= '</tr></td>';

		$this->content .= '<tr><td><br /></td></tr>';
		
		$this->content .= '<tr><td>';
		$this->content .= '<label for="site-text">'.GetMessage("WIZ_BANNER_TEXT").'</label><br />';
		$this->content .= $this->ShowInputField("textarea", "siteBannerText", Array("id" => "site-text", "style" => "width:100%", "rows"=>"4"));
		$this->content .= '<img src="/bitrix/wizards/bitrix/corp_services/images/'.LANGUAGE_ID.'/banner_processed.png">';
		$this->content .= '</tr></td>';

		$this->content .= '<tr><td><br /></td></tr>';


		
		$this->content .= '<tr><td>';
		$this->content .= '<label for="site-slogan">'.GetMessage("WIZ_COMPANY_SLOGAN").'</label><br />';
		$this->content .= $this->ShowInputField("textarea", "siteSlogan", Array("id" => "site-slogan", "style" => "width:100%", "rows"=>"3"));
		$this->content .= '</tr></td>';

		$this->content .= '<tr><td><br /></td></tr>';

		
		$this->content .= '<tr><td>';
		$this->content .= '<label for="site-copy">'.GetMessage("WIZ_COMPANY_COPY").'</label><br />';
		$this->content .= $this->ShowInputField("textarea", "siteCopy", Array("id" => "site-copy", "style" => "width:100%", "rows"=>"3"));
		$this->content .= '</tr></td>';

		$this->content .= '<tr><td><br /></td></tr>';


		
		$this->content .= '<tr><td style="padding-bottom:3px;">';
		$this->content .= $this->ShowCheckboxField("installDemoData", "Y", 
			(array("id" => "install-demo-data") + ($_SERVER["PHP_SELF"] == "/index.php" && empty($siteLogo) ? array() : array("checked" => false))));
		$this->content .= '<label for="install-demo-data">'.GetMessage("wiz_structure_data").'</label><br />';
		$this->content .= '</td></tr>';
		
		$this->content .= '<tr><td>&nbsp;</td></tr>';
		
		$this->content .= '</table>';

		$formName = $wizard->GetFormName();
		$installCaption = $this->GetNextCaption();
		$nextCaption = GetMessage("NEXT_BUTTON");
	}

	function OnPostForm()
	{
		$wizard =& $this->GetWizard();
		$res = $this->SaveFile("siteLogo", Array("extensions" => "gif,jpg,jpeg,png", "max_height" => 70, "max_width" => 190, "make_preview" => "Y"));
		$res = $this->SaveFile("siteBanner", Array("extensions" => "gif,jpg,jpeg,png", "max_height" => 400, "max_width" => 600, "make_preview" => "Y"));
//		COption::SetOptionString("main", "wizard_site_logo", $res, "", $wizard->GetVar("siteID")); 
	}
}

class DataInstallStep extends CDataInstallWizardStep
{
	function CorrectServices(&$arServices)
	{
		$wizard =& $this->GetWizard();
		if($wizard->GetVar("installDemoData") != "Y")
		{
		}
	}
}

class FinishStep extends CFinishWizardStep
{
}
?>