<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/include.php");

$saleModulePermissions = $APPLICATION->GetGroupRight("sale");
if ($saleModulePermissions == "D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

IncludeModuleLangFile(__FILE__);

ClearVars();

$ID = IntVal($ID);
if ($ID <= 0)
	LocalRedirect("sale_order.php?lang=".LANG.GetFilterParams("filter_", false));

$customTabber = new CAdminTabEngine("OnAdminSaleOrderView", array("ID" => $ID));

$arTransactTypes = array(
		"ORDER_PAY" => GetMessage("SOD_PAYMENT"),
		"CC_CHARGE_OFF" => GetMessage("SOD_FROM_CARD"),
		"OUT_CHARGE_OFF" => GetMessage("SOD_INPUT"),
		"ORDER_UNPAY" => GetMessage("SOD_CANCEL_PAYMENT"),
		"ORDER_CANCEL_PART" => GetMessage("SOD_CANCEL_SEMIPAYMENT"),
		"MANUAL" => GetMessage("SOD_HAND"),
		"DEL_ACCOUNT" => GetMessage("SOD_DELETE"),
		"AFFILIATE" => GetMessage("SOD1_AFFILIATES_PAY"),
	);

$errorMessage = "";

$bUserCanViewOrder = CSaleOrder::CanUserViewOrder($ID, $GLOBALS["USER"]->GetUserGroupArray(), $GLOBALS["USER"]->GetID());
$bUserCanEditOrder = CSaleOrder::CanUserUpdateOrder($ID, $GLOBALS["USER"]->GetUserGroupArray());
$bUserCanCancelOrder = CSaleOrder::CanUserCancelOrder($ID, $GLOBALS["USER"]->GetUserGroupArray(), $GLOBALS["USER"]->GetID());
$bUserCanPayOrder = CSaleOrder::CanUserChangeOrderFlag($ID, "PERM_PAYMENT", $GLOBALS["USER"]->GetUserGroupArray());
$bUserCanDeliverOrder = CSaleOrder::CanUserChangeOrderFlag($ID, "PERM_DELIVERY", $GLOBALS["USER"]->GetUserGroupArray());
$bUserCanDeleteOrder = CSaleOrder::CanUserDeleteOrder($ID, $GLOBALS["USER"]->GetUserGroupArray(), $GLOBALS["USER"]->GetID());

if ($action == "change_status"
	&& $saleModulePermissions >= "U"
	&& $_SERVER["REQUEST_METHOD"] == "GET"
	&& check_bitrix_sessid()
	&& empty($dontsave))
{
	/*
	if (!$bUserCanEditOrder)
		$errorMessage .= GetMessage("SOD_NO_PERMS2DEL").". ";

	if (strlen($errorMessage) <= 0)
	{
		$STATUS_ID = Trim($STATUS_ID);
		if (strlen($STATUS_ID) <= 0)
			$errorMessage .= GetMessage("ERROR_NO_STATUS").". ";
	}
	*/
	if (CSaleOrder::IsLocked($ID, $lockedBY, $dateLock))
		$errorMessage .= str_replace("#DATE#", "$dateLock", str_replace("#ID#", "$lockedBY", GetMessage("SOE_ORDER_LOCKED"))).". ";

	$STATUS_ID = Trim($STATUS_ID);
	if (strlen($STATUS_ID) <= 0)
		$errorMessage .= GetMessage("ERROR_NO_STATUS").". ";

	if (strlen($errorMessage) <= 0)
	{
		if (!CSaleOrder::CanUserChangeOrderStatus($ID, $STATUS_ID, $GLOBALS["USER"]->GetUserGroupArray()))
			$errorMessage .= GetMessage("SOD_NO_PERMS2STATUS").". ";
	}

	if (strlen($errorMessage) <= 0)
	{
		if (!CSaleOrder::StatusOrder($ID, $STATUS_ID))
		{
			if ($ex = $APPLICATION->GetException())
				$errorMessage .= $ex->GetString();
			else
				$errorMessage .= GetMessage("ERROR_CHANGE_STATUS").". ";
		}
	}

	if (strlen($errorMessage) <= 0)
		LocalRedirect("sale_order_detail.php?ID=".$ID."&result=ok_status&lang=".LANG.GetFilterParams("filter_", false));
}
elseif ($action == "change_cancel"
	&& $saleModulePermissions >= "U"
	&& $_SERVER["REQUEST_METHOD"] == "GET"
	&& check_bitrix_sessid()
	&& empty($dontsave))
{
	if (CSaleOrder::IsLocked($ID, $lockedBY, $dateLock))
		$errorMessage .= str_replace("#DATE#", "$dateLock", str_replace("#ID#", "$lockedBY", GetMessage("SOE_ORDER_LOCKED"))).". ";

	if (!$bUserCanCancelOrder)
		$errorMessage .= GetMessage("SOD_NO_PERMS2CANCEL").". ";

	if (strlen($errorMessage) <= 0)
	{
		$CANCEL = Trim($CANCEL);
		$REASON_CANCELED = Trim($REASON_CANCELED);
		if ($CANCEL != "Y" && $CANCEL != "N")
			$errorMessage .= GetMessage("SOD_WRONG_CANCEL_FLAG").". ";
	}

	if (strlen($errorMessage) <= 0)
	{
		if (!CSaleOrder::CancelOrder($ID, $CANCEL, $REASON_CANCELED))
		{
			if ($ex = $APPLICATION->GetException())
				$errorMessage .= $ex->GetString();
			else
				$errorMessage .= GetMessage("ERROR_CANCEL_ORDER").". ";
		}
	}

	if (strlen($errorMessage) <= 0)
		LocalRedirect("sale_order_detail.php?ID=".$ID."&result=ok_cancel&lang=".LANG.GetFilterParams("filter_", false));
}
elseif ($action == "change_pay"
	&& $saleModulePermissions >= "U"
	&& $_SERVER["REQUEST_METHOD"] == "GET"
	&& check_bitrix_sessid()
	&& empty($dontsave))
{
	if (CSaleOrder::IsLocked($ID, $lockedBY, $dateLock))
		$errorMessage .= str_replace("#DATE#", "$dateLock", str_replace("#ID#", "$lockedBY", GetMessage("SOE_ORDER_LOCKED"))).". ";

	if (!$bUserCanPayOrder)
		$errorMessage .= GetMessage("SOD_NO_PERMS2PAYFLAG").". ";

	if (strlen($errorMessage) <= 0)
	{
		$PAYED = Trim($PAYED);
		if ($PAYED != "Y" && $PAYED != "N")
			$errorMessage .= GetMessage("SOD_WRONG_PAYFLAG").". ";
	}

	if (strlen($errorMessage) <= 0)
	{
		if ($PAYED == "Y")
		{
			$bPayOut = ((strlen($PAYOUT_BUTTON) > 0) ? True : False );
			$bPayIn = ((strlen($PAY_BUTTON) > 0) ? True : False );
			if ($bPayOut && $bPayIn || !$bPayOut && !$bPayIn)
				$errorMessage .= GetMessage("SOD_WRONG_PAYMETHOD").". ";
		}
	}

	if (strlen($errorMessage) <= 0)
	{
		$arAdditionalFields = array(
				"PAY_VOUCHER_NUM" => ((strlen($PAY_VOUCHER_NUM) > 0) ? $PAY_VOUCHER_NUM : False),
				"PAY_VOUCHER_DATE" => ((strlen($PAY_VOUCHER_DATE) > 0) ? $PAY_VOUCHER_DATE : False)
			);
		if (!CSaleOrder::PayOrder($ID, $PAYED, True, $bPayOut, 0, $arAdditionalFields))
		{
			if ($ex = $APPLICATION->GetException())
				$errorMessage .= $ex->GetString();
			else
				$errorMessage .= GetMessage("ERROR_PAY_ORDER").". ";
		}
	}

	if (strlen($errorMessage) <= 0)
		LocalRedirect("sale_order_detail.php?ID=".$ID."&result=ok_pay&lang=".LANG.GetFilterParams("filter_", false));
}
elseif ($action == "change_allow_delivery"
	&& $saleModulePermissions >= "U"
	&& $_SERVER["REQUEST_METHOD"] == "GET"
	&& check_bitrix_sessid()
	&& empty($dontsave))
{
	if (CSaleOrder::IsLocked($ID, $lockedBY, $dateLock))
		$errorMessage .= str_replace("#DATE#", "$dateLock", str_replace("#ID#", "$lockedBY", GetMessage("SOE_ORDER_LOCKED"))).". ";

	if (!$bUserCanDeliverOrder)
		$errorMessage .= GetMessage("SOD_NO_PERMS2DELIV").". ";

	if (strlen($errorMessage) <= 0)
	{
		$ALLOW_DELIVERY = Trim($ALLOW_DELIVERY);
		if ($ALLOW_DELIVERY != "Y" && $ALLOW_DELIVERY != "N")
			$errorMessage .= GetMessage("SOD_WRONG_DELIV_FLAG").". ";
	}

	if (strlen($errorMessage) <= 0)
	{
		$arAdditionalFields = array(
				"DELIVERY_DOC_NUM" => ((strlen($DELIVERY_DOC_NUM) > 0) ? $DELIVERY_DOC_NUM : False),
				"DELIVERY_DOC_DATE" => ((strlen($DELIVERY_DOC_DATE) > 0) ? $DELIVERY_DOC_DATE : False)
			);

		if (!CSaleOrder::DeliverOrder($ID, $ALLOW_DELIVERY, 0, $arAdditionalFields))
		{
			if ($ex = $APPLICATION->GetException())
				$errorMessage .= $ex->GetString();
			else
				$errorMessage .= GetMessage("ERROR_DELIVERY_ORDER").". ";
		}
	}

	if (strlen($errorMessage) <= 0)
		LocalRedirect("sale_order_detail.php?ID=".$ID."&result=ok_delivery&lang=".LANG.GetFilterParams("filter_", false));
}
elseif ($action == "change_comments"
	&& $saleModulePermissions >= "U"
	&& $_SERVER["REQUEST_METHOD"] == "POST"
	&& check_bitrix_sessid()
	&& empty($dontsave))
{
	if (CSaleOrder::IsLocked($ID, $lockedBY, $dateLock))
		$errorMessage .= str_replace("#DATE#", "$dateLock", str_replace("#ID#", "$lockedBY", GetMessage("SOE_ORDER_LOCKED"))).". ";

	if (!$bUserCanEditOrder)
		$errorMessage .= GetMessage("SOD_NO_PERMS2DEL").". ";

	if (strlen($errorMessage) <= 0)
	{
		if (!CSaleOrder::CommentsOrder($ID, $COMMENTS))
		{
			if ($ex = $APPLICATION->GetException())
				$errorMessage .= $ex->GetString();
			else
				$errorMessage .= GetMessage("ERROR_CHANGE_COMMENT").". ";
		}
	}

	if (strlen($errorMessage) <= 0)
		LocalRedirect("sale_order_detail.php?ID=".$ID."&result=ok_comment&lang=".LANG.GetFilterParams("filter_", false));
}
elseif ($action == "ps_update"
	&& $saleModulePermissions >= "U"
	&& $_SERVER["REQUEST_METHOD"] == "GET"
	&& check_bitrix_sessid()
	&& empty($dontsave))
{
	if (CSaleOrder::IsLocked($ID, $lockedBY, $dateLock))
		$errorMessage .= str_replace("#DATE#", "$dateLock", str_replace("#ID#", "$lockedBY", GetMessage("SOE_ORDER_LOCKED"))).". ";

	$arOrder = CSaleOrder::GetByID($ID);
	if (!$arOrder)
		$errorMessage .= GetMessage("ERROR_NO_ORDER")."<br>";

	if (strlen($errorMessage) <= 0)
	{
		$psResultFile = "";

		$arPaySys = CSalePaySystem::GetByID($arOrder["PAY_SYSTEM_ID"], $arOrder["PERSON_TYPE_ID"]);

		$psActionPath = $_SERVER["DOCUMENT_ROOT"].$arPaySys["PSA_ACTION_FILE"];
		$psActionPath = str_replace("\\", "/", $psActionPath);
		while (substr($psActionPath, strlen($psActionPath) - 1, 1) == "/")
			$psActionPath = substr($psActionPath, 0, strlen($psActionPath) - 1);

		if (file_exists($psActionPath) && is_dir($psActionPath))
		{
			if (file_exists($psActionPath."/result.php") && is_file($psActionPath."/result.php"))
				$psResultFile = $psActionPath."/result.php";
		}
		elseif (strlen($arPaySys["PSA_RESULT_FILE"]) > 0)
		{
			if (file_exists($_SERVER["DOCUMENT_ROOT"].$arPaySys["PSA_RESULT_FILE"])
				&& is_file($_SERVER["DOCUMENT_ROOT"].$arPaySys["PSA_RESULT_FILE"]))
				$psResultFile = $_SERVER["DOCUMENT_ROOT"].$arPaySys["PSA_RESULT_FILE"];
		}

		if (strlen($psResultFile) <= 0)
			$errorMessage .= GetMessage("SOD_NO_PS_SCRIPT").". ";
	}

	if (strlen($errorMessage) <= 0)
	{
		$ORDER_ID = $ID;
		CSalePaySystemAction::InitParamArrays($arOrder, $ID, $arPaySys["PSA_PARAMS"]);
		if (!include($psResultFile))
			$errorMessage .= GetMessage("ERROR_CONNECT_PAY_SYS").". ";
	}

	if (strlen($errorMessage) <= 0)
	{
		$ORDER_ID = IntVal($ORDER_ID);
		$arOrder = CSaleOrder::GetByID($ORDER_ID);
		if (!$arOrder)
			$errorMessage .= str_replace("#ID#", $ORDER_ID, GetMessage("SOD_NO_ORDER")).". ";
	}

	if (strlen($errorMessage) <= 0)
	{
		if ($arOrder["PS_STATUS"] == "Y" && $arOrder["PAYED"] == "N")
		{
			if ($arOrder["CURRENCY"] == $arOrder["PS_CURRENCY"]
				&& DoubleVal($arOrder["PRICE"]) == DoubleVal($arOrder["PS_SUM"]))
			{
				if (!CSaleOrder::PayOrder($arOrder["ID"], "Y", True, True))
				{
					if ($ex = $APPLICATION->GetException())
						$errorMessage .= $ex->GetString();
					else
						$errorMessage .= str_replace("#ID#", $ORDER_ID, GetMessage("SOD_CANT_PAY")).". ";
				}
			}
		}
	}

	if (strlen($errorMessage) <= 0)
		LocalRedirect("sale_order_detail.php?ID=".$ID."&result=ok_ps&lang=".LANG.GetFilterParams("filter_", false));
}
elseif (!empty($dontsave))
{
	CSaleOrder::UnLock($ID);
	LocalRedirect("sale_order.php?lang=".LANG.GetFilterParams("filter_", false));
}

$dbOrder = CSaleOrder::GetList(
	array("ID" => "DESC"),
	array("ID" => $ID),
	false,
	false,
	array("ID", "LID", "PERSON_TYPE_ID", "PAYED", "DATE_PAYED", "EMP_PAYED_ID", "CANCELED", "DATE_CANCELED", "EMP_CANCELED_ID", "REASON_CANCELED", "STATUS_ID", "DATE_STATUS", "PAY_VOUCHER_NUM", "PAY_VOUCHER_DATE", "EMP_STATUS_ID", "PRICE_DELIVERY", "ALLOW_DELIVERY", "DATE_ALLOW_DELIVERY", "EMP_ALLOW_DELIVERY_ID", "PRICE", "CURRENCY", "DISCOUNT_VALUE", "SUM_PAID", "USER_ID", "PAY_SYSTEM_ID", "DELIVERY_ID", "DATE_INSERT", "DATE_INSERT_FORMAT", "DATE_UPDATE", "USER_DESCRIPTION", "ADDITIONAL_INFO", "PS_STATUS", "PS_STATUS_CODE", "PS_STATUS_DESCRIPTION", "PS_STATUS_MESSAGE", "PS_SUM", "PS_CURRENCY", "PS_RESPONSE_DATE", "COMMENTS", "TAX_VALUE", "STAT_GID", "RECURRING_ID", "AFFILIATE_ID", "LOCK_STATUS", "USER_LOGIN", "USER_NAME", "USER_LAST_NAME", "USER_EMAIL", "DELIVERY_DOC_NUM", "DELIVERY_DOC_DATE")
);
if (!$dbOrder->ExtractFields("str_"))
	LocalRedirect("sale_order.php?lang=".LANG.GetFilterParams("filter_", false));


require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/prolog.php");

$APPLICATION->SetTitle(GetMessage("SALE_EDIT_RECORD", array("#ID#"=>$ID)));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

/*********************************************************************/
/********************  BODY  *****************************************/
/*********************************************************************/
?>

<?
$aMenu = array(
		array(
				"TEXT" => GetMessage("SOD_TO_LIST"),
				"LINK" => "/bitrix/admin/sale_order_detail.php?ID=".$ID."&dontsave=Y&lang=".LANGUAGE_ID.GetFilterParams("filter_"),
				"ICON"=>"btn_list",
			)
	);

$aMenu[] = array("SEPARATOR" => "Y");

if ($bUserCanEditOrder)
{
	$aMenu[] = array(
			"TEXT" => GetMessage("SOD_TO_EDIT"),
			"LINK" => "/bitrix/admin/sale_order_edit.php?ID=".$ID."&lang=".LANGUAGE_ID.GetFilterParams("filter_"),
			"ICON"=>"btn_edit",
		);
}

$aMenu[] = array(
		"TEXT" => GetMessage("SOD_TO_PRINT"),
		"LINK" => "/bitrix/admin/sale_order_print.php?ID=".$ID."&lang=".LANGUAGE_ID.GetFilterParams("filter_"),
		"ICON"=>"btn_print",
	);

if ($saleModulePermissions == "W" || $str_PAYED != "Y" && $bUserCanDeleteOrder)
{
	$aMenu[] = array(
			"TEXT" => GetMessage("SODN_CONFIRM_DEL"),
			"LINK" => "javascript:if(confirm('".GetMessage("SODN_CONFIRM_DEL_MESSAGE")."')) window.location='sale_order.php?ID=".$ID."&action=delete&lang=".LANG."&".bitrix_sessid_get().GetFilterParams("filter_")."'",
			"WARNING" => "Y",
			"ICON"=>"btn_delete",
		);
}

$context = new CAdminContextMenu($aMenu);
$context->Show();
?>

<?
CAdminMessage::ShowMessage($errorMessage);
if (strlen($result) > 0)
{
	$okMessage = "";

	if ($result == "ok_status")
		$okMessage = GetMessage("SOD_OK_STATUS");
	elseif ($result == "ok_cancel")
		$okMessage = GetMessage("SOD_OK_CANCEL");
	elseif ($result == "ok_pay")
		$okMessage = GetMessage("SOD_OK_PAY");
	elseif ($result == "ok_delivery")
		$okMessage = GetMessage("SOD_OK_DELIVERY");
	elseif ($result == "ok_comment")
		$okMessage = GetMessage("SOD_OK_COMMENT");
	elseif ($result == "ok_ps")
		$okMessage = GetMessage("SOD_OK_PS");

	CAdminMessage::ShowNote($okMessage);
}

if (!$bUserCanViewOrder)
{
	CAdminMessage::ShowMessage(str_replace("#ID#", $ID, GetMessage("SOD_NO_PERMS2VIEW")).". ");
}
else
{
	if (!CSaleOrder::IsLocked($ID, $lockedBY, $dateLock))
		CSaleOrder::Lock($ID);

	$customOrderView = COption::GetOptionString("sale", "path2custom_view_order", "");
	if (strlen($customOrderView) > 0
		&& file_exists($_SERVER["DOCUMENT_ROOT"].$customOrderView)
		&& is_file($_SERVER["DOCUMENT_ROOT"].$customOrderView))
	{
		include($_SERVER["DOCUMENT_ROOT"].$customOrderView);
	}
	else
	{

		$aTabs = array(
				array("DIV" => "edit1", "TAB" => GetMessage("SODN_TAB_ORDER"), "ICON" => "sale", "TITLE" => GetMessage("SODN_TAB_ORDER_DESCR")),
				array("DIV" => "edit2", "TAB" => GetMessage("SODN_TAB_BASKET"), "ICON" => "sale", "TITLE" => GetMessage("SODN_TAB_BASKET_DESCR")),
				array("DIV" => "edit3", "TAB" => GetMessage("SODN_TAB_TRANSACT"), "ICON" => "sale", "TITLE" => GetMessage("SODN_TAB_TRANSACT_DESCR"))
			);

		$tabControl = new CAdminTabControl("tabControl", $aTabs);
		$tabControl->AddTabs($customTabber);
		$tabControl->Begin();

		$tabControl->BeginNextTab();
		?>

			<tr class="heading">
				<td colspan="2" align="center">
					<?echo GetMessage("P_ORDER_ID")?> <?echo GetMessage("SOD_ORDER_ID_PREF")?> <?echo $ID ?> <?echo GetMessage("SOD_FROM")?> <?echo $str_DATE_INSERT ?>
				</td>
			</tr>
			<tr>
				<td width="40%"><?echo GetMessage("SOD_DATE_UPDATE")?></td>
				<td width="60%"><?echo $str_DATE_UPDATE ?></td>
			</tr>
			<tr>
				<td width="40%"><?echo GetMessage("P_ORDER_LANG")?>:</td>
				<td width="60%">
					<?
					echo '[<a href="site_edit.php?LID='.$str_LID.'&lang='.LANGUAGE_ID.'">'.$str_LID.'</a>] ';
					$dbSite = CLang::GetByID($str_LID);
					if ($arSite = $dbSite->GetNext())
						echo $arSite["NAME"];
					?>
				</td>
			</tr>
			<tr>
				<td width="40%" valign="top"><?echo GetMessage("SOD_CUR_STATUS")?></td>
				<form method="get" action="/bitrix/admin/sale_order_detail.php">
					<td width="60%" valign="top">
						<script language="JavaScript">
						<!--
						var statusBoxVisible = false;
						function ShowHideStatus()
						{
							var viewStatusDIV = document.getElementById("viewStatusDIV");
							var editStatusDIV = document.getElementById("editStatusDIV");
							if (statusBoxVisible)
							{
								viewStatusDIV.style["display"] = "block";
								editStatusDIV.style["display"] = "none";
								statusBoxVisible = false;
							}
							else
							{
								viewStatusDIV.style["display"] = "none";
								editStatusDIV.style["display"] = "block";
								statusBoxVisible = true;
							}
						}
						//-->
						</script>

						<?
						$arStatusList = False;
						$arFilter = array("LID" => LANG);
						$arGroupByTmp = false;
						if ($saleModulePermissions < "W")
						{
							$arFilter["GROUP_ID"] = $GLOBALS["USER"]->GetUserGroupArray();
							$arFilter["PERM_STATUS_FROM"] = "Y";
							$arFilter["ID"] = $str_STATUS_ID;
							$arGroupByTmp = array("ID", "NAME", "MAX" => "PERM_STATUS_FROM");
						}
						$dbStatusList = CSaleStatus::GetList(
								array(),
								$arFilter,
								$arGroupByTmp,
								false,
								array("ID", "NAME", "PERM_STATUS_FROM")
							);
						$arStatusList = $dbStatusList->GetNext();

						?>

						<div id="viewStatusDIV" style="display: block;">

							<?
							$arCurrentStatus = CSaleStatus::GetByID($str_STATUS_ID);

							echo "[";
							if ($saleModulePermissions >= "W")
								echo "<a href=\"/bitrix/admin/sale_status_edit.php?ID=".$str_STATUS_ID."&lang=".LANG."\">";
							echo $str_STATUS_ID;
							if ($saleModulePermissions >= "W")
								echo "</a>";

							echo "] ".htmlspecialcharsEx($arCurrentStatus["NAME"])." &nbsp;&nbsp;";
							echo $str_DATE_STATUS;
							if (IntVal($str_EMP_STATUS_ID) > 0)
							{
								if (!isset($LOCAL_PAYED_USER_CACHE[$str_EMP_STATUS_ID])
									|| !is_array($LOCAL_PAYED_USER_CACHE[$str_EMP_STATUS_ID]))
								{
									$dbUser = CUser::GetByID($str_EMP_STATUS_ID);
									if ($arUser = $dbUser->Fetch())
										$LOCAL_PAYED_USER_CACHE[$str_EMP_STATUS_ID] = htmlspecialcharsEx($arUser["NAME"].((strlen($arUser["NAME"])<=0 || strlen($arUser["LAST_NAME"])<=0) ? "" : " ").$arUser["LAST_NAME"]);
								}
								echo " &nbsp;&nbsp;[<a href=\"/bitrix/admin/user_edit.php?ID=".$str_EMP_STATUS_ID."&lang=".LANG."\">".$str_EMP_STATUS_ID."</a>] ";
								echo $LOCAL_PAYED_USER_CACHE[$str_EMP_STATUS_ID];
							}

							if ($arStatusList && ($str_LOCK_STATUS != "red"))
							{
								?>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<a href="javascript:ShowHideStatus();"><nobr><?echo GetMessage("SOD_CHANGE_STATUS")?></nobr></a>
								<?
							}
							?>

						</div>

						<div id="editStatusDIV" style="display: none;">
							<select name="STATUS_ID">
								<?
								if ($arStatusList)
								{
									$arFilter = array("LID" => LANG);
									$arGroupByTmp = false;
									if ($saleModulePermissions < "W")
									{
										$arFilter["GROUP_ID"] = $GLOBALS["USER"]->GetUserGroupArray();
										$arFilter["PERM_STATUS"] = "Y";
										//$arGroupByTmp = array("ID", "NAME", "MAX" => "PERM_STATUS");
									}
									$dbStatusListTmp = CSaleStatus::GetList(
											array("SORT" => "ASC"),
											$arFilter,
											$arGroupByTmp,
											false,
											array("ID", "NAME")
										);
									while($arStatusListTmp = $dbStatusListTmp->GetNext())
									{
										?><option value="<?echo $arStatusListTmp["ID"] ?>"<?if ($arStatusListTmp["ID"]==$str_STATUS_ID) echo " selected"?>>[<?echo $arStatusListTmp["ID"] ?>] <?echo $arStatusListTmp["NAME"] ?></option><?
									}
								}
								?>
							</select>
							<?= GetFilterHiddens("filter_"); ?>
							<?= bitrix_sessid_post(); ?>
							<input type="hidden" name="action" value="change_status">
							<input type="hidden" name="lang" value="<?= LANG ?>">
							<input type="hidden" name="ID" value="<?= $ID ?>">
							<input type="submit" value="<?echo GetMessage("SOD_CHANGE")?>">
							<br><br>
							<a href="javascript:ShowHideStatus();"><nobr><?echo GetMessage("SOD_VIEW")?></nobr></a>
						</div>
					</td>
				</form>
			</tr>
			<tr>
				<td width="40%"><?echo GetMessage("P_ORDER_PRICE")?>:</td>
				<td width="60%">
					<?
					echo "<b>".SaleFormatCurrency($str_PRICE, $str_CURRENCY)."</b>";
					if (DoubleVal($str_SUM_PAID) > 0)
						echo GetMessage("SOD_ALREADY_PAYED").SaleFormatCurrency($str_SUM_PAID, $str_CURRENCY)."</b>)";
					?>
				</td>
			</tr>
			<tr>
				<td width="40%"><?echo GetMessage("P_ORDER_AFFILIATE")?>:</td>
				<td width="60%">
					<?
					if (IntVal($str_AFFILIATE_ID) > 0)
					{
						echo '[<a href="sale_affiliate_edit.php?ID='.IntVal($str_AFFILIATE_ID).'&lang='.LANGUAGE_ID.'">'.IntVal($str_AFFILIATE_ID).'</a>] ';
						$dbAffiliate = CSaleAffiliate::GetList(
							array(),
							array("ID" => $str_AFFILIATE_ID),
							false,
							false,
							array("ID", "SITE_ID", "USER_ID", "USER_LOGIN", "USER_NAME", "USER_LAST_NAME")
						);
						if ($arAffiliate = $dbAffiliate->Fetch())
							echo $arAffiliate["USER_NAME"]." ".$arAffiliate["USER_LAST_NAME"]." (".$arAffiliate["USER_LOGIN"].")";
					}
					else
						echo "&nbsp;";
					?>
				</td>
			</tr>
			<tr>
				<td width="40%"><?echo GetMessage("P_ORDER_CANCELED")?>:</td>
				<form method="get" action="/bitrix/admin/sale_order_detail.php">
					<td width="60%">
						<script language="JavaScript">
						<!--
						var cancelBoxVisible = false;
						function ShowHideCancel()
						{
							var viewCancelDIV = document.getElementById("viewCancelDIV");
							var editCancelDIV = document.getElementById("editCancelDIV");
							if (cancelBoxVisible)
							{
								viewCancelDIV.style["display"] = "block";
								editCancelDIV.style["display"] = "none";
								cancelBoxVisible = false;
							}
							else
							{
								viewCancelDIV.style["display"] = "none";
								editCancelDIV.style["display"] = "block";
								cancelBoxVisible = true;
							}
						}
						//-->
						</script>

						<div id="viewCancelDIV" style="display: block;">
							<?
							echo (($str_CANCELED == "Y") ? GetMessage("SALE_YES") : GetMessage("SALE_NO"));
							echo " &nbsp;&nbsp;".$str_DATE_CANCELED;
							if (IntVal($str_EMP_CANCELED_ID) > 0)
							{
								if (!isset($LOCAL_PAYED_USER_CACHE[$str_EMP_CANCELED_ID])
									|| !is_array($LOCAL_PAYED_USER_CACHE[$str_EMP_CANCELED_ID]))
								{
									$dbUser = CUser::GetByID($str_EMP_CANCELED_ID);
									if ($arUser = $dbUser->Fetch())
										$LOCAL_PAYED_USER_CACHE[$str_EMP_CANCELED_ID] = htmlspecialcharsEx($arUser["NAME"].((strlen($arUser["NAME"])<=0 || strlen($arUser["LAST_NAME"])<=0) ? "" : " ").$arUser["LAST_NAME"]);
								}
								echo " &nbsp;&nbsp;[<a href=\"/bitrix/admin/user_edit.php?ID=".$str_EMP_CANCELED_ID."&lang=".LANG."\">".$str_EMP_CANCELED_ID."</a>] ";
								echo $LOCAL_PAYED_USER_CACHE[$str_EMP_CANCELED_ID];
							}
							if ($bUserCanCancelOrder && ($str_LOCK_STATUS != "red"))
							{
								?>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<a href="javascript:ShowHideCancel();"><nobr><?echo GetMessage("SOD_CHANGE_FLAG")?></nobr></a>
								<?
							}
							if ($str_CANCELED == "Y" && strlen($str_REASON_CANCELED) > 0)
								echo "<br>".$str_REASON_CANCELED;
							?>
						</div>

						<div id="editCancelDIV" style="display: none;">
							<?echo GetMessage("SOD_CHANGE_FLAG_FROM")?>
							<b><?= (($str_CANCELED == "Y") ? GetMessage("SOD_CANCELED") : GetMessage("SOD_NOT_CANCELED")); ?></b>
							<?echo GetMessage("SOD_CHANGE_TO")?>
							<b><?= (($str_CANCELED == "Y") ? GetMessage("SOD_NOT_CANCELED") : GetMessage("SOD_CANCELED")); ?></b>
							<br><br>
							<?echo GetMessage("SOD_CANCEL_REASON")?><br>
							<textarea name="REASON_CANCELED" rows="3" cols="40"><?= $str_REASON_CANCELED ?></textarea><br>
							<?= GetFilterHiddens("filter_"); ?>
							<?= bitrix_sessid_post(); ?>
							<input type="hidden" name="CANCEL" value="<?= (($str_CANCELED == "Y") ? "N" : "Y"); ?>">
							<input type="hidden" name="ID" value="<?= $ID ?>">
							<input type="hidden" name="action" value="change_cancel">
							<input type="hidden" name="lang" value="<?= LANG ?>">
							<br>
							<input type="submit" value="<?echo GetMessage("SOD_CHANGE")?>">
							<br><br>
							<a href="javascript:ShowHideCancel();"><nobr><?echo GetMessage("SOD_VIEW")?></nobr></a>
						</div>

					</td>
				</form>
			</tr>
			<tr>
				<td colspan="2">
					<img src="/bitrix/images/1.gif" width="1" height="8">
				</td>
			</tr>

			<tr class="heading">
				<td colspan="2"><?echo GetMessage("P_ORDER_USER_ACC")?></td>
			</tr>
			<tr>
				<td width="40%"><?echo GetMessage("P_ORDER_ACCOUNT") ?>:</td>
				<td width="60%">
					<?
					echo "[<a href=\"/bitrix/admin/user_edit.php?ID=".$str_USER_ID."&lang=".LANG."\">".$str_USER_ID."</a>] ";
					$dbUser = CUser::GetByID($str_USER_ID);
					if ($arUser = $dbUser->Fetch())
						echo htmlspecialcharsEx($arUser["NAME"].((strlen($arUser["NAME"])<=0 || strlen($arUser["LAST_NAME"])<=0) ? "" : " ").$arUser["LAST_NAME"]);
					?>
				</td>
			</tr>
			<tr>
				<td width="40%"><?= GetMessage("P_ORDER_USER_LOGIN") ?>:</td>
				<td width="60%"><?= htmlspecialcharsEx($arUser["LOGIN"]); ?></td>
			</tr>
			<tr>
				<td width="40%"><?= GetMessage("P_ORDER_USER_EMAIL") ?>:</td>
				<td width="60%">
					<a href="mailto:<?= htmlspecialcharsEx($arUser["EMAIL"]); ?>"><?= htmlspecialcharsEx($arUser["EMAIL"]); ?></a>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<img src="/bitrix/images/1.gif" width="1" height="8">
				</td>
			</tr>

			<tr class="heading">
				<td colspan="2"><?echo GetMessage("P_ORDER_USER")?></td>
			</tr>
			<tr>
				<td width="40%"><?echo GetMessage("P_ORDER_PERS_TYPE") ?>:</td>
				<td width="60%">
					<?
					echo "[";
					if ($saleModulePermissions >= "W")
						echo "<a href=\"/bitrix/admin/sale_person_type_edit.php?ID=".$str_PERSON_TYPE_ID."&lang=".LANG."\">";
					echo $str_PERSON_TYPE_ID;
					if ($saleModulePermissions >= "W")
						echo "</a>";
					echo "] ";
					$arPersonType = CSalePersonType::GetByID($str_PERSON_TYPE_ID);
					echo htmlspecialcharsEx($arPersonType["NAME"])." (".htmlspecialcharsEx($arPersonType["LID"]).")";
					?>
				</td>
			</tr>
			<?
			$dbOrderProps = CSaleOrderPropsValue::GetOrderProps($ID);
			$iGroup = -1;
			while ($arOrderProps = $dbOrderProps->Fetch())
			{
				if ($iGroup != IntVal($arOrderProps["PROPS_GROUP_ID"]))
				{
					?>
					<tr class="heading">
						<td colspan="2"><?echo $arOrderProps["GROUP_NAME"];?></td>
					</tr>
					<?
					$iGroup = IntVal($arOrderProps["PROPS_GROUP_ID"]);
				}

				?>
				<tr>
					<td width="40%">
						<?echo $arOrderProps["NAME"] ?>:
					</td>
					<td width="60%">
						<?
						if ($arOrderProps["TYPE"] == "CHECKBOX")
						{
							if ($arOrderProps["VALUE"] == "Y")
								echo GetMessage("SALE_YES");
							else
								echo GetMessage("SALE_NO");
						}
						elseif ($arOrderProps["TYPE"] == "TEXT" || $arOrderProps["TYPE"] == "TEXTAREA")
						{
							echo htmlspecialcharsEx($arOrderProps["VALUE"]);
						}
						elseif ($arOrderProps["TYPE"] == "SELECT" || $arOrderProps["TYPE"] == "RADIO")
						{
							$arVal = CSaleOrderPropsVariant::GetByValue($arOrderProps["ORDER_PROPS_ID"], $arOrderProps["VALUE"]);
							echo htmlspecialcharsEx($arVal["NAME"]);
						}
						elseif ($arOrderProps["TYPE"] == "MULTISELECT")
						{
							$curVal = split(",", $arOrderProps["VALUE"]);
							for ($i = 0; $i < count($curVal); $i++)
							{
								$arVal = CSaleOrderPropsVariant::GetByValue($arOrderProps["ORDER_PROPS_ID"], $curVal[$i]);
								if ($i > 0)
									echo ", ";
								echo htmlspecialcharsEx($arVal["NAME"]);
							}
						}
						elseif ($arOrderProps["TYPE"] == "LOCATION")
						{
							$arVal = CSaleLocation::GetByID($arOrderProps["VALUE"], LANG);
							echo htmlspecialcharsEx($arVal["COUNTRY_NAME"].((strlen($arVal["COUNTRY_NAME"])<=0 || strlen($arVal["CITY_NAME"])<=0) ? "" : " - ").$arVal["CITY_NAME"]);
						}
						else
						{
							echo htmlspecialcharsEx($arOrderProps["VALUE"]);
						}
						?>
					</td>
				</tr>
				<?
			}
			if ($iGroup >= 0 && strlen($str_USER_DESCRIPTION) > 0)
			{
				?>
				<tr>
					<td colspan="2">
						<img src="/bitrix/images/1.gif" width="1" height="8">
					</td>
				</tr>
				<?
			}
			if (strlen($str_USER_DESCRIPTION) > 0)
			{
				?>
				<tr>
					<td width="40%"><?echo GetMessage("P_ORDER_USER_COMMENT") ?>:</td>
					<td width="60%"><?echo $str_USER_DESCRIPTION ?></td>
				</tr>
				<?
			}
			?>
			<tr>
				<td colspan="2">
					<img src="/bitrix/images/1.gif" width="1" height="8">
				</td>
			</tr>

			<tr class="heading">
				<td colspan="2"><?echo GetMessage("P_ORDER_PAYMENT")?></td>
			</tr>
			<tr>
				<td width="40%"><?echo GetMessage("P_ORDER_PAY_SYSTEM") ?>:</td>
				<td width="60%">
					<?
					if (IntVal($str_PAY_SYSTEM_ID) > 0)
					{
						$arPaySys = CSalePaySystem::GetByID($str_PAY_SYSTEM_ID, $str_PERSON_TYPE_ID);
						if ($arPaySys)
						{
							echo '[';
							if ($saleModulePermissions >= "W")
								echo '<a href="sale_pay_system_edit.php?ID='.$arPaySys["ID"].'&lang='.LANGUAGE_ID.'">';
							echo $arPaySys["ID"];
							if ($saleModulePermissions >= "W")
								echo '</a>';
							echo '] '.htmlspecialcharsEx($arPaySys["NAME"]." (".$arPaySys["LID"].")");
						}
						else
							echo "<font color=\"#FF0000\">".GetMessage("SOD_PAY_SYS_DISC")."</font>";
					}
					else
						GetMessage("SOD_NONE");
					?>
				</td>
			</tr>
			<tr>
				<td width="40%"><?echo GetMessage("P_ORDER_PAYED") ?>:</td>
				<form method="get" action="/bitrix/admin/sale_order_detail.php" name="change_pay_form">
					<td width="60%">
						<script language="JavaScript">
						<!--
						var payBoxVisible = false;
						function ShowHidePay()
						{
							var viewPayDIV = document.getElementById("viewPayDIV");
							var editPayDIV = document.getElementById("editPayDIV");
							if (payBoxVisible)
							{
								viewPayDIV.style["display"] = "block";
								editPayDIV.style["display"] = "none";
								payBoxVisible = false;
							}
							else
							{
								viewPayDIV.style["display"] = "none";
								editPayDIV.style["display"] = "block";
								payBoxVisible = true;
							}
						}
						//-->
						</script>

						<div id="viewPayDIV" style="display: block;">
							<?
							echo (($str_PAYED == "Y") ? GetMessage("SALE_YES") : GetMessage("SALE_NO"));
							echo " &nbsp;&nbsp;".$str_DATE_PAYED;
							if (IntVal($str_EMP_PAYED_ID) > 0)
							{
								if (!isset($LOCAL_PAYED_USER_CACHE[$str_EMP_PAYED_ID])
									|| !is_array($LOCAL_PAYED_USER_CACHE[$str_EMP_PAYED_ID]))
								{
									$dbUser = CUser::GetByID($str_EMP_PAYED_ID);
									if ($arUser = $dbUser->Fetch())
										$LOCAL_PAYED_USER_CACHE[$str_EMP_PAYED_ID] = htmlspecialcharsEx($arUser["NAME"].((strlen($arUser["NAME"])<=0 || strlen($arUser["LAST_NAME"])<=0) ? "" : " ").$arUser["LAST_NAME"]);
								}
								echo " &nbsp;&nbsp;[<a href=\"/bitrix/admin/user_edit.php?ID=".$str_EMP_PAYED_ID."&lang=".LANG."\">".$str_EMP_PAYED_ID."</a>] ";
								echo $LOCAL_PAYED_USER_CACHE[$str_EMP_PAYED_ID];
							}
							if ($bUserCanPayOrder && ($str_LOCK_STATUS != "red"))
							{
								?>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<a href="javascript:ShowHidePay();"><nobr><?echo GetMessage("SOD_CHANGE_FLAG")?></nobr></a>
								<?
							}
							if (strlen($str_PAY_VOUCHER_NUM) > 0 || strlen($str_PAY_VOUCHER_DATE) > 0)
							{
								?>
								<br>
								<?= str_replace("#DATE#", $str_PAY_VOUCHER_DATE, str_replace("#NUM#", $str_PAY_VOUCHER_NUM, GetMessage("SOD_PAY_DOC"))) ?>
								<?
							}
							?>
						</div>

						<div id="editPayDIV" style="display: none;">
							<?echo GetMessage("SOD_CHANGE_FLAG_FROM")?>
							<b><?= (($str_PAYED == "Y") ? GetMessage("SOD_PAID") : GetMessage("SOD_NOT_PAID")); ?></b>
							<?echo GetMessage("SOD_CHANGE_TO")?>
							<b><?= (($str_PAYED == "Y") ? GetMessage("SOD_NOT_PAID") : GetMessage("SOD_PAID")); ?></b>
							<br><br>
							<table border="0" cellspacing="1" cellpadding="3">
							<tr>
								<td width="0%" nowrap>
									<?echo GetMessage("SOD_VOUCHER_NUM")?>
								</td>
								<td>
									<input type="text" name="PAY_VOUCHER_NUM" value="<?= $str_PAY_VOUCHER_NUM ?>" size="20" maxlength="20" class="typeinput">
								</td>
							</tr>
							<tr>
								<td width="0%" nowrap>
									<?echo GetMessage("SOD_VOUCHER_DATE")?><?= CSite::GetDateFormat("SHORT", LANG); ?>):
								</td>
								<td>
									<?= CalendarDate("PAY_VOUCHER_DATE", $str_PAY_VOUCHER_DATE, "change_pay_form", "20", "class=\"typeinput\""); ?>
								</td>
							</tr>
							</table>
							<br>
							<?= GetFilterHiddens("filter_"); ?>
							<?= bitrix_sessid_post(); ?>
							<input type="hidden" name="PAYED" value="<?= (($str_PAYED == "Y") ? "N" : "Y"); ?>">
							<input type="hidden" name="ID" value="<?= $ID ?>">
							<input type="hidden" name="action" value="change_pay">
							<input type="hidden" name="lang" value="<?= LANG ?>">
							<?
							if ($str_PAYED == "Y")
							{
								?>
								<input type="submit" value="<?echo GetMessage("SOD_UNPAY")?>">
								<?
							}
							else
							{
								?>
								<input type="submit" name="PAYOUT_BUTTON" value="<?echo GetMessage("SOD_PAY")?>">
								&nbsp;&nbsp;&nbsp;
								<input type="submit" name="PAY_BUTTON" value="<?echo GetMessage("SOD_PAY_ACCOUNT")?>">
								<?
							}
							?>
							<br><br>
							<a href="javascript:ShowHidePay();"><nobr><?echo GetMessage("SOD_VIEW")?></nobr></a>
						</div>

					</td>
				</form>
			</tr>
			<?
			if (strlen($str_PS_STATUS) > 0)
			{
				?>
				<tr>
					<td colspan="2">
						<img src="/bitrix/images/1.gif" width="1" height="8">
					</td>
				</tr>
				<tr>
					<td width="40%"><?echo GetMessage("P_ORDER_PS_STATUS") ?>:</td>
					<td width="60%">
						<?
						echo (($str_PS_STATUS=="Y") ? "OK" : "Error");

						if ($arPaySys["PSA_HAVE_RESULT"] == "Y" || strlen($arPaySys["PSA_RESULT_FILE"]) > 0)
						{
							?>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<a href="sale_order_detail.php?ID=<?= $ID ?>&action=ps_update&lang=<?= LANG ?><?echo GetFilterParams("filter_")?>&<?= bitrix_sessid_get() ?>"><?echo GetMessage("P_ORDER_PS_STATUS_UPDATE") ?> &gt;&gt;</a>
							<?
						}
						?>
					</td>
				</tr>
				<tr>
					<td width="40%"><?echo GetMessage("P_ORDER_PS_STATUS_CODE") ?>:</td>
					<td width="60%"><?echo $str_PS_STATUS_CODE ;?></td>
				</tr>
				<tr>
					<td width="40%"><?echo GetMessage("P_ORDER_PS_STATUS_DESCRIPTION") ?>:</td>
					<td width="60%"><?echo $str_PS_STATUS_DESCRIPTION ;?></td>
				</tr>
				<tr>
					<td width="40%"><?echo GetMessage("P_ORDER_PS_STATUS_MESSAGE") ?>:</td>
					<td width="60%"><?echo $str_PS_STATUS_MESSAGE ;?></td>
				</tr>
				<tr>
					<td width="40%"><?echo GetMessage("P_ORDER_PS_SUM") ?>:</td>
					<td width="60%"><?echo $str_PS_SUM ;?></td>
				</tr>
				<tr>
					<td width="40%"><?echo GetMessage("P_ORDER_PS_CURRENCY") ?>:</td>
					<td width="60%"><?echo $str_PS_CURRENCY ;?></td>
				</tr>
				<tr>
					<td width="40%"><?echo GetMessage("P_ORDER_PS_RESPONSE_DATE") ?>:</td>
					<td width="60%"><?echo $str_PS_RESPONSE_DATE; ?></td>
				</tr>
				<?
			}
			elseif ($arPaySys["PSA_HAVE_RESULT"] == "Y" || strlen($arPaySys["PSA_RESULT_FILE"]) > 0)
			{
				?>
				<tr>
					<td colspan="2">
						<img src="/bitrix/images/1.gif" width="1" height="8">
					</td>
				</tr>
				<tr>
					<td align="center" colspan="2">
						<a href="sale_order_detail.php?ID=<?= $ID ?>&action=ps_update&lang=<?= LANG ?><?= GetFilterParams("filter_") ?>&<?= bitrix_sessid_get() ?>"><?= GetMessage("P_ORDER_PS_STATUS_UPDATE") ?> &gt;&gt;</a>
					</td>
				</tr>
				<?
			}
			?>
			<tr>
				<td colspan="2">
					<img src="/bitrix/images/1.gif" width="1" height="8">
				</td>
			</tr>

			<tr class="heading">
				<td colspan="2"><?echo GetMessage("P_ORDER_DELIVERY")?></td>
			</tr>
			<tr>
				<td width="40%"><?echo GetMessage("P_ORDER_DELIVERY") ?>:</td>
				<td width="60%">
					<?
					if (strpos($str_DELIVERY_ID, ":") !== false)
					{
						$arId = explode(":", $str_DELIVERY_ID);

						$dbDelivery = CSaleDeliveryHandler::GetBySID($arId[0]);
						$arDelivery = $dbDelivery->Fetch();

						//echo "<pre>"; print_r($arDelivery); echo "</pre>";
						echo "[".$arDelivery["SID"]."] ".htmlspecialcharsEx($arDelivery["NAME"])." (".$str_LID.")";
						echo "<br />[".htmlspecialcharsEx($arId[1])."] ".htmlspecialcharsEx($arDelivery["PROFILES"][$arId[1]]["TITLE"]);

					}
					elseif (IntVal($str_DELIVERY_ID) > 0)
					{
						$arDelivery = CSaleDelivery::GetByID($str_DELIVERY_ID);
						echo "[".$arDelivery["ID"]."] ".$arDelivery["NAME"]." (".$arDelivery["LID"].")";
					}
					else
					{
						?><?echo GetMessage("SOD_NONE")?><?
					}
					?>
				</td>
			</tr>
			<tr>
				<td width="40%">
					<?echo GetMessage("P_ORDER_ALLOW_DELIVERY") ?>:
				</td>
				<form method="get" action="/bitrix/admin/sale_order_detail.php" name="change_delivery_form">
					<td width="60%">
						<script language="JavaScript">
						<!--
						var deliveryBoxVisible = false;
						function ShowHideDelivery()
						{
							var viewDeliveryDIV = document.getElementById("viewDeliveryDIV");
							var editDeliveryDIV = document.getElementById("editDeliveryDIV");
							if (deliveryBoxVisible)
							{
								viewDeliveryDIV.style["display"] = "block";
								editDeliveryDIV.style["display"] = "none";
								deliveryBoxVisible = false;
							}
							else
							{
								viewDeliveryDIV.style["display"] = "none";
								editDeliveryDIV.style["display"] = "block";
								deliveryBoxVisible = true;
							}
						}
						//-->
						</script>

						<div id="viewDeliveryDIV" style="display: block;">
							<?
							echo (($str_ALLOW_DELIVERY == "Y") ? GetMessage("SALE_YES") : GetMessage("SALE_NO"));
							echo " &nbsp;&nbsp;".$str_DATE_ALLOW_DELIVERY;
							if (IntVal($str_EMP_ALLOW_DELIVERY_ID) > 0)
							{
								if (!isset($LOCAL_PAYED_USER_CACHE[$str_EMP_ALLOW_DELIVERY_ID])
									|| !is_array($LOCAL_PAYED_USER_CACHE[$str_EMP_ALLOW_DELIVERY_ID]))
								{
									$dbUser = CUser::GetByID($str_EMP_ALLOW_DELIVERY_ID);
									if ($arUser = $dbUser->Fetch())
										$LOCAL_PAYED_USER_CACHE[$str_EMP_ALLOW_DELIVERY_ID] = htmlspecialcharsEx($arUser["NAME"].((strlen($arUser["NAME"])<=0 || strlen($arUser["LAST_NAME"])<=0) ? "" : " ").$arUser["LAST_NAME"]);
								}
								echo " &nbsp;&nbsp;[<a href=\"/bitrix/admin/user_edit.php?ID=".$str_EMP_ALLOW_DELIVERY_ID."&lang=".LANG."\">".$str_EMP_ALLOW_DELIVERY_ID."</a>] ";
								echo $LOCAL_PAYED_USER_CACHE[$str_EMP_ALLOW_DELIVERY_ID];
							}
							if ($bUserCanDeliverOrder && ($str_LOCK_STATUS != "red"))
							{
								?>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<a href="javascript:ShowHideDelivery();"><nobr><?echo GetMessage("SOD_CHANGE_FLAG")?></nobr></a>
								<?
							}
							if (strlen($str_DELIVERY_DOC_NUM) > 0 || strlen($str_DELIVERY_DOC_DATE) > 0)
							{
								?>
								<br>
								<?= str_replace("#DATE#", $str_DELIVERY_DOC_DATE, str_replace("#NUM#", $str_DELIVERY_DOC_NUM, GetMessage("SOD_DELIV_DOC"))) ?>
								<?
							}

							?>
						</div>

						<div id="editDeliveryDIV" style="display: none;">
							<?echo GetMessage("SOD_CHANGE_FLAG_FROM")?>
							<b><?= (($str_ALLOW_DELIVERY == "Y") ? GetMessage("SOD_ALLOWED") : GetMessage("SOD_NOT_ALLOWED")); ?></b>
							<?echo GetMessage("SOD_CHANGE_TO")?>
							<b><?= (($str_ALLOW_DELIVERY == "Y") ? GetMessage("SOD_NOT_ALLOWED") : GetMessage("SOD_ALLOWED")); ?></b>
							<br><br>
							<?= GetFilterHiddens("filter_"); ?>
							<?= bitrix_sessid_post(); ?>
							<input type="hidden" name="ALLOW_DELIVERY" value="<?= (($str_ALLOW_DELIVERY == "Y") ? "N" : "Y"); ?>">
							<input type="hidden" name="ID" value="<?= $ID ?>">
							<input type="hidden" name="action" value="change_allow_delivery">
							<input type="hidden" name="lang" value="<?= LANG ?>">
							<input type="submit" value="<?echo GetMessage("SOD_CHANGE_FLAG1")?>">
							<br><br>
							<table border="0" cellspacing="1" cellpadding="3">
							<tr>
								<td width="0%" nowrap>
									<?echo GetMessage("SOD_DEL_VOUCHER_NUM")?>
								</td>
								<td>
									<input type="text" name="DELIVERY_DOC_NUM" value="<?= $str_DELIVERY_DOC_NUM ?>" size="20" maxlength="20" class="typeinput">
								</td>
							</tr>
							<tr>
								<td width="0%" nowrap>
									<?echo GetMessage("SOD_DEL_VOUCHER_DATE")?><?= CSite::GetDateFormat("SHORT", LANG); ?>):
								</td>
								<td>
									<?= CalendarDate("DELIVERY_DOC_DATE", $str_DELIVERY_DOC_DATE, "change_delivery_form", "20", "class=\"typeinput\""); ?>
								</td>
							</tr>
							</table>

							<a href="javascript:ShowHideDelivery();"><nobr><?echo GetMessage("SOD_VIEW")?></nobr></a>
						</div>
					</td>
				</form>
			</tr>
			<tr>
				<td colspan="2">
					<img src="/bitrix/images/1.gif" width="1" height="8">
				</td>
			</tr>

			<?
			if (strlen($str_ADDITIONAL_INFO)>0)
			{
				?>
				<tr class="heading">
					<td colspan="2"><?echo GetMessage("P_ORDER_COMMENTS")?></td>
				</tr>
				<tr>
					<td width="40%"><?echo GetMessage("P_ORDER_COMMENTS") ?>:</td>
					<td width="60%"><?echo $str_ADDITIONAL_INFO; ?></td>
				</tr>
				<tr>
					<td colspan="2">
						<img src="/bitrix/images/1.gif" width="1" height="8">
					</td>
				</tr>
				<?
			}
			?>
			<tr class="heading">
				<td colspan="2"><?echo GetMessage("P_ORDER_COMMENTS")?></td>
			</tr>
			<tr>
				<td width="40%">
					<?echo GetMessage("P_ORDER_COMMENTS") ?>:<br><small><?echo GetMessage("SOD_HIDE_FROM_USER")?></small>
				</td>
				<form method="post" action="/bitrix/admin/sale_order_detail.php">
					<td width="60%">
						<script language="JavaScript">
						<!--
						var commentsBoxVisible = false;
						function ShowHideComments()
						{
							var viewCommentsDIV = document.getElementById("viewCommentsDIV");
							var editCommentsDIV = document.getElementById("editCommentsDIV");
							if (commentsBoxVisible)
							{
								viewCommentsDIV.style["display"] = "block";
								editCommentsDIV.style["display"] = "none";
								commentsBoxVisible = false;
							}
							else
							{
								viewCommentsDIV.style["display"] = "none";
								editCommentsDIV.style["display"] = "block";
								commentsBoxVisible = true;
							}
						}
						//-->
						</script>

						<div id="viewCommentsDIV" style="display: block;">
							<?
							echo $str_COMMENTS;
							if ($bUserCanEditOrder && ($str_LOCK_STATUS != "red"))
							{
								if (strlen($str_COMMENTS) > 0)
									echo "<br>";
								?>
								<a href="javascript:ShowHideComments();"><nobr><?echo GetMessage("SOD_ADD_COMMENT")?></nobr></a>
								<?
							}
							?>
						</div>

						<div id="editCommentsDIV" style="display: none;">
							<textarea name="COMMENTS" rows="3" cols="60"><?= $str_COMMENTS ?></textarea><br>
							<?= GetFilterHiddens("filter_"); ?>
							<?= bitrix_sessid_post(); ?>
							<input type="hidden" name="ID" value="<?= $ID ?>">
							<input type="hidden" name="action" value="change_comments">
							<input type="hidden" name="lang" value="<?= LANG ?>">
							<input type="submit" value="<?echo GetMessage("SOD_CHANGE")?>">
							<br><br>
							<a href="javascript:ShowHideComments();"><nobr><?echo GetMessage("SOD_VIEW")?></nobr></a>
						</div>
					</td>
				</form>
			</tr>
			<tr>
				<td colspan="2">
					<img src="/bitrix/images/1.gif" width="1" height="8">
				</td>
			</tr>

		<?
		$tabControl->EndTab();
		?>

		<?
		$tabControl->BeginNextTab();
		?>

			<tr>
				<td colspan="2">
					<?
					$summWeight = 0;
					$WEIGHT_UNIT = htmlspecialchars(COption::GetOptionString('sale', 'weight_unit'));

					$dbBasket = CSaleBasket::GetList(
							array("NAME" => "ASC"),
							array("ORDER_ID" => $ID),
							false,
							false,
							array("ID", "DETAIL_PAGE_URL", "NAME", "NOTES", "QUANTITY", "PRICE", "CURRENCY", "PRODUCT_XML_ID", "DISCOUNT_NAME", "DISCOUNT_VALUE", "DISCOUNT_COUPON", "WEIGHT")
						);
					?>
					<table cellpadding="3" cellspacing="1" border="0" width="100%" class="internal">
						<tr class="heading">
							<td><?echo GetMessage("SALE_F_NAME")?></td>
							<td><?echo GetMessage("SALE_F_XML_ID")?></td>
							<td><?echo GetMessage("SALE_F_PROPS")?></td>
							<td><?echo GetMessage("SALE_F_BASKET_DISCOUNT")?></td>
							<td><?echo GetMessage("SALE_F_PTYPE")?></td>
							<td><?echo GetMessage("SALE_F_WEIGHT")?></td>
							<td><?echo GetMessage("SALE_F_QUANTITY")?></td>
							<td><?echo GetMessage("SALE_F_PRICE")?></td>
						</tr>
						<?
						while ($arBasket = $dbBasket->Fetch())
						{
							$summWeight += $arBasket["WEIGHT"] * $arBasket["QUANTITY"];
							?>
							<tr>
								<td valign="top">
									<?
									if (strlen($arBasket["DETAIL_PAGE_URL"])>0)
										echo "<a href=\"".$arBasket["DETAIL_PAGE_URL"]."\">";
									echo htmlspecialcharsEx($arBasket["NAME"]);
									if (strlen($arBasket["DETAIL_PAGE_URL"])>0)
										echo "</a>";
									?>
								</td>
								<td valign="top">
									<?echo htmlspecialcharsEx($arBasket["PRODUCT_XML_ID"]);?>
								</td>
								<td valign="top">
									<?
									$dbBasketProps = CSaleBasket::GetPropsList(
											array("SORT" => "ASC"),
											array(
												"BASKET_ID" => $arBasket["ID"],
												"!CODE" => array("CATALOG.XML_ID", "PRODUCT.XML_ID")
											),
											false,
											false,
											array("ID", "BASKET_ID", "NAME", "VALUE", "CODE", "SORT")
									);
									while ($arBasketProps = $dbBasketProps->Fetch())
									{
										echo "<i>".htmlspecialcharsEx($arBasketProps["NAME"]).":</i> ";
										echo htmlspecialcharsEx($arBasketProps["VALUE"]);
										echo "<br>";
									}
									?>
								</td>
								<td valign="top">
									<?
									if(strlen($arBasket["DISCOUNT_NAME"])>0)
										echo GetMessage("SALE_F_BASKET_DISCOUNT_NAME").": ".htmlspecialcharsEx($arBasket["DISCOUNT_NAME"])."<br />";
									if(strlen($arBasket["DISCOUNT_VALUE"])>0)
										echo GetMessage("SALE_F_BASKET_DISCOUNT_VALUE").": ".htmlspecialcharsEx($arBasket["DISCOUNT_VALUE"])."<br />";
									if(strlen($arBasket["DISCOUNT_COUPON"])>0)
										echo GetMessage("SALE_F_BASKET_DISCOUNT_COUPON").": ".htmlspecialcharsEx($arBasket["DISCOUNT_COUPON"])."<br />";
									?>
								</td>
								<td valign="top">
									<?echo htmlspecialcharsEx($arBasket["NOTES"]) ?>
								</td>
								<td valign="top">
									<?echo $arBasket["WEIGHT"] ?> <?=$WEIGHT_UNIT?>
								</td>
								<td valign="top">
									<?echo $arBasket["QUANTITY"] ?>
								</td>
								<td align="right" valign="top">
									<?echo SaleFormatCurrency($arBasket["PRICE"], $arBasket["CURRENCY"]) ?>
								</td>
							</tr>
							<?
						}
						?>
						<tr>
							<td align="right">
								<b><?echo GetMessage("SALE_F_WEIGHT")?>:</b>
							</td>
							<td align="right" colspan="7">
								<?echo $summWeight ?> <?=$WEIGHT_UNIT?>
							</td>
						</tr>
						<tr>
							<td align="right">
								<b><?echo GetMessage("SALE_F_DISCOUNT")?>:</b>
							</td>
							<td align="right" colspan="7">
								<?echo SaleFormatCurrency($str_DISCOUNT_VALUE, $str_CURRENCY) ?>
							</td>
						</tr>
						<?
						$dbTaxList = CSaleOrderTax::GetList(
								array("APPLY_ORDER" => "ASC"),
								array("ORDER_ID" => $ID)
							);
						while ($arTaxList = $dbTaxList->Fetch())
						{
							?>
							<tr>
								<td align="right">
									<?
									echo htmlspecialcharsEx($arTaxList["TAX_NAME"]);
									if ($arTaxList["IS_IN_PRICE"]=="Y")
										echo " (".(($arTaxList["IS_PERCENT"]=="Y") ? "".DoubleVal($arTaxList["VALUE"])."%, " : "").GetMessage("SALE_TAX_INPRICE").")";
									elseif ($arTaxList["IS_PERCENT"]=="Y")
										echo " (".DoubleVal($arTaxList["VALUE"])."%)";
									?>:
								</td>
								<td align="right" colspan="7">
									<?echo SaleFormatCurrency($arTaxList["VALUE_MONEY"], $str_CURRENCY) ?>
								</td>
							</tr>
							<?
						}
						?>
						<tr>
							<td align="right">
								<b><?echo GetMessage("SALE_F_TAX")?>:</b>
							</td>
							<td align="right" colspan="7">
								<?echo SaleFormatCurrency($str_TAX_VALUE, $str_CURRENCY) ?>
							</td>
						</tr>
						<tr>
							<td align="right">
								<b><?echo GetMessage("SALE_F_DELIVERY")?>:</b>
							</td>
							<td align="right" colspan="7">
								<?echo SaleFormatCurrency($str_PRICE_DELIVERY, $str_CURRENCY) ?>
							</td>
						</tr>
						<tr>
							<td align="right">
								<b><?echo GetMessage("SALE_F_ITOG")?>:</b>
							</td>
							<td align="right" colspan="7">
								<?echo SaleFormatCurrency($str_PRICE, $str_CURRENCY) ?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<img src="/bitrix/images/1.gif" width="1" height="">
				</td>
			</tr>

		<?
		$tabControl->EndTab();
		?>

		<?
		$tabControl->BeginNextTab();
		?>

			<tr>
				<td colspan="2">
					<?
					$dbTransact = CSaleUserTransact::GetList(
							array("TRANSACT_DATE" => "ASC"),
							array("ORDER_ID" => $ID),
							false,
							false,
							array("ID", "USER_ID", "AMOUNT", "CURRENCY", "DEBIT", "ORDER_ID", "DESCRIPTION", "NOTES", "TIMESTAMP_X", "TRANSACT_DATE")
						);
					?>
					<table cellpadding="3" cellspacing="1" border="0" width="100%" class="internal">
						<tr class="heading">
							<td><?echo GetMessage("SOD_TRANS_DATE")?></td>
							<td><?echo GetMessage("SOD_TRANS_USER")?></td>
							<td><?echo GetMessage("SOD_TRANS_SUM")?></td>
							<td><?echo GetMessage("SOD_TRANS_DESCR")?></td>
							<td><?echo GetMessage("SOD_TRANS_COMMENT")?></td>
						</tr>
						<?
						$bNoTransact = True;
						while ($arTransact = $dbTransact->Fetch())
						{
							$bNoTransact = False;
							?>
							<tr>
								<td><?= $arTransact["TRANSACT_DATE"]; ?></td>
								<td>
									<?
									if (!isset($LOCAL_PAYED_USER_CACHE[$arTransact["USER_ID"]])
										|| !is_array($LOCAL_PAYED_USER_CACHE[$arTransact["USER_ID"]]))
									{
										$dbUser = CUser::GetByID($arTransact["USER_ID"]);
										if ($arUser = $dbUser->Fetch())
											$LOCAL_PAYED_USER_CACHE[$arTransact["USER_ID"]] = htmlspecialcharsEx($arUser["NAME"].((strlen($arUser["NAME"])<=0 || strlen($arUser["LAST_NAME"])<=0) ? "" : " ").$arUser["LAST_NAME"]);
									}
									echo "[<a href=\"/bitrix/admin/user_edit.php?ID=".$arTransact["USER_ID"]."&lang=".LANG."\">".$arTransact["USER_ID"]."</a>] ";
									echo $LOCAL_PAYED_USER_CACHE[$arTransact["USER_ID"]];
									?>
								</td>
								<td>
									<?
									echo (($arTransact["DEBIT"] == "Y") ? "+" : "-");
									echo SaleFormatCurrency($arTransact["AMOUNT"], $arTransact["CURRENCY"]);
									?>
								</td>
								<td>
									<?
									if (array_key_exists($arTransact["DESCRIPTION"], $arTransactTypes))
										echo htmlspecialcharsEx($arTransactTypes[$arTransact["DESCRIPTION"]]);
									else
										echo htmlspecialcharsEx($arTransact["DESCRIPTION"]);
									?>
								</td>
								<td align="right">
									<?echo htmlspecialcharsEx($arTransact["NOTES"]) ?>
								</td>
							</tr>
							<?
						}

						if ($bNoTransact)
						{
							?>
							<tr>
								<td colspan="5" align="center">
									<?echo GetMessage("SOD_NO_TRANS")?>
								</td>
							</tr>
							<?
						}
						?>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<img src="/bitrix/images/1.gif" width="1" height="8">
				</td>
			</tr>

		<?
		$tabControl->EndTab();
		?>

		<?
//		$tabControl->Buttons();
		?>

		<?
		$tabControl->End();
		?>

		<?
	}		// if (strlen($customOrderView) > 0 ...
}		// if (!$bUserCanViewOrder)
?>

<?require($DOCUMENT_ROOT."/bitrix/modules/main/include/epilog_admin.php");?>