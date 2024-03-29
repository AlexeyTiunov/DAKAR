<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

/*
$catalogModulePermissions = $APPLICATION->GetGroupRight("catalog");
if ($catalogModulePermissions=="D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
*/

if (!($USER->CanDoOperation('catalog_read') || $USER->CanDoOperation('catalog_price')))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$bReadOnly = !$USER->CanDoOperation('catalog_price');

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/catalog/include.php");

if ($ex = $APPLICATION->GetException())
{
	require($DOCUMENT_ROOT."/bitrix/modules/main/include/prolog_admin_after.php");
	
	$strError = $ex->GetString();
	ShowError($strError);
	
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
	die();
}

IncludeModuleLangFile(__FILE__);

ClearVars();

$errorMessage = "";
$bVarsFromForm = false;

$ID = IntVal($ID);

if ($REQUEST_METHOD=="POST" && strlen($Update) > 0 && !$bReadOnly /*$catalogModulePermissions>="W"*/ && check_bitrix_sessid())
{
	$arFields = array(
		"NAME" => $NAME,
		"PERCENTAGE" => $PERCENTAGE,
		"RECALCULATE" => (($ID > 0) ? $RECALCULATE : "N")
	);

	if ($ID > 0)
	{
		if (!CExtra::Update($ID, $arFields))
		{
			if ($ex = $APPLICATION->GetException())
				$errorMessage .= $ex->GetString().". ";
			else
				$errorMessage .= GetMessage("CEEN_ERROR_SAVING_EXTRA").". ";
		}
	}
	else
	{
		$ID = CExtra::Add($arFields);
		$ID = IntVal($ID);
		if ($ID <= 0)
		{
			if ($ex = $APPLICATION->GetException())
				$errorMessage .= $ex->GetString().". ";
			else
				$errorMessage .= GetMessage("CEEN_ERROR_SAVING_EXTRA").". ";
		}
	}

	if (strlen($errorMessage) <= 0)
	{
		if (strlen($apply) <= 0)
			LocalRedirect("/bitrix/admin/cat_extra.php?lang=".LANG/*.GetFilterParams("filter_", false)*/);
		else
			LocalRedirect("/bitrix/admin/cat_extra_edit.php?lang=".LANG."&ID=".$ID /*.GetFilterParams("filter_", false)*/);
	}
	else
	{
		$bVarsFromForm = true;
	}
}

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/catalog/prolog.php");

if ($ID > 0)
	$APPLICATION->SetTitle(GetMessage("CEEN_UPDATING"));
else
	$APPLICATION->SetTitle(GetMessage("CEEN_ADDING"));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

if ($ID > 0)
{
	$arExtra = CExtra::GetByID($ID);
	if (!$arExtra)
	{
		//if ($catalogModulePermissions < "W")
		if ($bReadOnly)
			$errorMessage .= GetMessage("CEEN_NO_PERMS2ADD").". ";
		$ID = 0;
	}
	else
	{
		$str_NAME = htmlspecialchars($arExtra["NAME"]);
		$str_PERCENTAGE = htmlspecialchars($arExtra["PERCENTAGE"]);
		$str_RECALCULATE = "N";
	}
}

if ($bVarsFromForm)
	$DB->InitTableVarsForEdit("b_catalog_extra", "", "str_");
?>

<?
$aMenu = array(
	array(
		"TEXT" => GetMessage("CEEN_2FLIST"),
		"ICON" => "btn_list",
		"LINK" => "/bitrix/admin/cat_extra.php?lang=".LANG /*.GetFilterParams("filter_", false)*/
	)
);

if ($ID > 0 && !$bReadOnly /*$catalogModulePermissions >= "W"*/)
{
	$aMenu[] = array("SEPARATOR" => "Y");

	$aMenu[] = array(
			"TEXT" => GetMessage("CEEN_NEW_DISCOUNT"),
			"ICON" => "btn_new",
			"LINK" => "/bitrix/admin/cat_extra_edit.php?lang=".LANG /*.GetFilterParams("filter_", false)*/
		);

	$aMenu[] = array(
			"TEXT" => GetMessage("CEEN_DELETE_DISCOUNT"), 
			"ICON" => "btn_delete",
			"LINK" => "javascript:if(confirm('".GetMessage("CEEN_DELETE_DISCOUNT_CONFIRM")."')) window.location='/bitrix/admin/cat_extra.php?ID=".$ID."&action=delete&lang=".LANG."&".bitrix_sessid_get()."#tb';",
			"WARNING" => "Y"
		);
}
$context = new CAdminContextMenu($aMenu);
$context->Show();
?>

<?CAdminMessage::ShowMessage($errorMessage);?>

<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?" name="form1">
<?echo GetFilterHiddens("filter_");?>
<input type="hidden" name="Update" value="Y">
<input type="hidden" name="lang" value="<?echo LANG ?>">
<input type="hidden" name="ID" value="<?echo $ID ?>">
<?=bitrix_sessid_post()?>

<?
$aTabs = array(
		array("DIV" => "edit1", "TAB" => GetMessage("CEEN_TAB_DISCOUNT"), "ICON" => "catalog", "TITLE" => GetMessage("CEEN_TAB_DISCOUNT_DESCR"))
	);

$tabControl = new CAdminTabControl("tabControl", $aTabs);
$tabControl->Begin();
?>

<?
$tabControl->BeginNextTab();
?>

	<?if ($ID > 0):?>
		<tr>
			<td width="40%">ID:</td>
			<td width="60%"><?=$ID?></td>
		</tr>
	<?endif;?>
	<tr>
		<td width="40%"><span class="required">*</span><?echo GetMessage("CEEN_NAME")?>:</td>
		<td width="60%">
			<input type="text" name="NAME" size="50" value="<?= $str_NAME ?>">
		</td>
	</tr>
	<tr>
		<td><?echo GetMessage("CEEN_PERCENTAGE")?>:</td>
		<td>
			<input type="text" name="PERCENTAGE" size="10" maxlength="20" value="<?= roundEx($str_PERCENTAGE, CATALOG_VALUE_PRECISION) ?>" />%
		</td>
	</tr>
	<?
	if ($ID > 0)
	{
		?>
		<tr>
			<td><?echo GetMessage("CEEN_RECALC")?>:</td>
			<td>
				<input type="checkbox" name="RECALCULATE" value="Y"<?if ($str_RECALCULATE == "Y") echo " checked"?>>
			</td>
		</tr>
		<?
	}
	?>

<?
$tabControl->EndTab();
?>

<?
$tabControl->Buttons(
		array(
				//"disabled" => ($catalogModulePermissions < "W"),
				"disabled" => $bReadOnly,
				"back_url" => "/bitrix/admin/cat_extra.php?lang=".LANG /*.GetFilterParams("filter_", false)*/
			)
	);
?>

<?
$tabControl->End();
?>

</form>

<?echo BeginNote();?>
<span class="required">*</span> <?echo GetMessage("REQUIRED_FIELDS")?>
<?echo EndNote(); ?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>