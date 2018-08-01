<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>
<?
ShowMessage($arParams["~AUTH_RESULT"]);
ShowMessage($arResult['ERROR_MESSAGE']);
?>
<? if($arResult['NEW_USER_REGISTRATION'] == 'Y' && ($arResult['USE_OPENID'] == 'Y' || $arResult['USE_LIVEID'] == 'Y')){?>
<script type="text/javascript">

function SAAChangeAuthForm(v)
{
	document.getElementById('at_bitrix').style.display = (v == 'bitrix') ? 'block' : 'none';
	<? if ($arResult['USE_OPENID'] == 'Y') { ?>document.getElementById('at_openid').style.display = (v == 'openid') ? 'block' : 'none';<?}?>
	<? if ($arResult['USE_LIVEID'] == 'Y') { ?>document.getElementById('at_liveid').style.display = (v == 'liveid') ? 'block' : 'none';<?}?>
}

</script>
<table border="0" cellpadding="0" cellspacing="0">
<form id="choosemethod">
<tr>
	<td><input type="radio" id="auth_type_bitrix" name="BX_AUTH_TYPE" value="bitrix" onclick="SAAChangeAuthForm(this.value)" checked></td>
	<td><label for="auth_type_bitrix"><?=GetMessage('AUTH_A_INTERNAL')?></label></td>
</tr>
<? if ($arResult['USE_OPENID'] == 'Y') { ?>
<tr>
	<td><input type="radio" id="auth_type_openid" name="BX_AUTH_TYPE" value="openid" onclick="SAAChangeAuthForm(this.value)"></td>
	<td><label for="auth_type_openid"><?=GetMessage('AUTH_A_OPENID')?></label></td>
</tr>
<?}?>
<? if ($arResult['USE_LIVEID'] == 'Y') { ?>
<tr>
	<td><input type="radio" id="auth_type_liveid" name="BX_AUTH_TYPE" value="liveid" onclick="SAAChangeAuthForm(this.value)"></td>
	<td><label for="auth_type_liveid"><?=GetMessage('AUTH_A_LIVEID')?></label></td>
</tr>
<? } ?>
</form>
</table>
<?}?>
<div id="at_bitrix">
<form name="form_auth" method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>">

	<input type="hidden" name="AUTH_FORM" value="Y" />
	<input type="hidden" name="TYPE" value="AUTH" />
	<?if (strlen($arResult["BACKURL"]) > 0):?>
	<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
	<?endif?>
	<?
	foreach ($arResult["POST"] as $key => $value)
	{
	?>
	<input type="hidden" name="<?=$key?>" value="<?=$value?>" />
	<?
	}
	?>
<p><?=GetMessage("AUTH_PLEASE_AUTH")?></p>
<table class="data-table bx-auth-table">
	<thead>
		<tr>
			<td colspan="2"><b><?=GetMessage("AUTH_AUTH")?></b></td>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><?=GetMessage("AUTH_LOGIN")?></td>
			<td><input type="text" name="USER_LOGIN" maxlength="50" value="<?=$arResult["LAST_LOGIN"]?>" /></td>
		</tr>
		<tr>
			<td><?=GetMessage("AUTH_PASSWORD")?></td>
			<td><input type="password" name="USER_PASSWORD" maxlength="50" /></td>
		</tr>
		<?if($arResult["CAPTCHA_CODE"]):?>
			<tr>
				<td>&nbsp;</td>
				<td><input type="hidden" name="captcha_sid" value="<?echo $arResult["CAPTCHA_CODE"]?>" />
				<img src="/bitrix/tools/captcha.php?captcha_sid=<?echo $arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" /></td>
			</tr>
			<tr>
				<td><?echo GetMessage("AUTH_CAPTCHA_PROMT")?>:</td>
				<td><input type="text" name="captcha_word" maxlength="50" value="" /></td>
			</tr>
		<?endif;?>
	</tbody>
	<tfoot>
	<?
	if ($arResult["STORE_PASSWORD"] == "Y")
	{
	?>
		<tr>
			<td colspan="2"><input type="checkbox" id="USER_REMEMBER" name="USER_REMEMBER" value="Y" /><label for="USER_REMEMBER">&nbsp;<?=GetMessage("AUTH_REMEMBER_ME")?></label></td>
		</tr>
	<?
	}
	?>
		<tr>
			<td colspan="2" class="authorize-submit-cell"><input type="submit" name="Login" value="<?=GetMessage("AUTH_AUTHORIZE")?>" /></td>
		</tr>
	</tfoot>
</table>
<?
if ($arParams["NOT_SHOW_LINKS"] != "Y")
{
?><noindex>
	<?
	if($arResult["NEW_USER_REGISTRATION"] == "Y" && $arParams["AUTHORIZE_REGISTRATION"] != "Y")
	{
	?>
<p>
<a href="<?=$arResult["AUTH_REGISTER_URL"]?>" rel="nofollow"><b><?=GetMessage("AUTH_REGISTER")?></b></a><br />
<?=GetMessage("AUTH_FIRST_ONE")?> <a href="<?=$arResult["AUTH_REGISTER_URL"]?>" rel="nofollow"><?=GetMessage("AUTH_REG_FORM")?></a>
</p>
	<?
	}
	?>
<p>
<a href="<?=$arResult["AUTH_FORGOT_PASSWORD_URL"]?>" rel="nofollow"><b><?=GetMessage("AUTH_FORGOT_PASSWORD_2")?></b></a><br />
<?=GetMessage("AUTH_GO")?> <a href="<?=$arResult["AUTH_FORGOT_PASSWORD_URL"]?>" rel="nofollow"><?=GetMessage("AUTH_GO_AUTH_FORM")?></a><br />
<?=GetMessage("AUTH_MESS_1")?> <a href="<?=$arResult["AUTH_CHANGE_PASSWORD_URL"]?>" rel="nofollow"><?=GetMessage("AUTH_CHANGE_FORM")?></a>
</p>
</noindex><?
}
?>
</form>
<script type="text/javascript">
<?
if (strlen($arResult["LAST_LOGIN"])>0)
{
?>
try{document.form_auth.USER_PASSWORD.focus();}catch(e){}
<?
}
else
{
?>
try{document.form_auth.USER_LOGIN.focus();}catch(e){}
<?
}
?>
</script>
</div>
<? if($arResult['NEW_USER_REGISTRATION'] == 'Y' && $arResult['USE_OPENID'] == 'Y'){?>
<div id="at_openid" style="display: none">
<form method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>">
<table class="data-table bx-auth-table">
		<tr>
			<td><?=GetMessage("AUTH_OPENID")?></td>
			<td><input type="text" name="OPENID_IDENTITY" maxlength="100" value="<?=$arResult["USER_LOGIN"]?>" /></td>
		</tr>
		<tr>
			<td colspan="2"><input type="submit" name="Login" value="<?=GetMessage("AUTH_AUTHORIZE")?>" /></td>
		</tr>
</table>
</form>
</div>
<?}?>
<? if($arResult['NEW_USER_REGISTRATION'] == 'Y' && $arResult['USE_LIVEID'] == 'Y'){?>
<div id="at_liveid" style="display: none"><noindex>
<a href="<?=$arResult['LIVEID_LOGIN_LINK']?>" rel="nofollow"><?=GetMessage('AUTH_LIVEID_LOGIN')?></a>
</noindex></div>
<?}?>
