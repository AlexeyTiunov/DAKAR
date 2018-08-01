<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if ($arResult["FORM_TYPE"] == "login"):?>
<?
if ($arResult['SHOW_ERRORS'] == 'Y' && $arResult['ERROR'])
{
    ShowMessage($arResult['ERROR_MESSAGE']);
    
}
?>
<? if($arResult['NEW_USER_REGISTRATION'] == 'Y' && ($arResult['USE_OPENID'] == 'Y' || $arResult['USE_LIVEID'] == 'Y')){?>
<script type="text/javascript">

function SAFChangeAuthForm(v)
{
    document.getElementById('at_frm_bitrix').style.display = (v == 'bitrix') ? 'block' : 'none';
    <? if ($arResult['USE_OPENID'] == 'Y') { ?>document.getElementById('at_frm_openid').style.display = (v == 'openid') ? 'block' : 'none';<?}?>
    <? if ($arResult['USE_LIVEID'] == 'Y') { ?>document.getElementById('at_frm_liveid').style.display = (v == 'liveid') ? 'block' : 'none';<?}?>
}

</script>
<table border="0" cellpadding="0" cellspacing="0">
<form id="choosemethod">
<tr>
    <td><input type="radio" id="auth_type_frm_bitrix" name="BX_AUTH_TYPE" value="bitrix" onc lick="SAFChangeAuthForm(this.value)" checked></td>
    <td><label for="auth_type_frm_bitrix"><?=GetMessage('AUTH_A_INTERNAL')?></label></td>
</tr>
<? if ($arResult['USE_OPENID'] == 'Y') { ?>
<tr>
    <td><input type="radio" id="auth_type_frm_openid" name="BX_AUTH_TYPE" value="openid" onc lick="SAFChangeAuthForm(this.value)"></td>
    <td><label for="auth_type_frm_openid"><?=GetMessage('AUTH_A_OPENID')?></label></td>
</tr>
<? } ?>
<? if ($arResult['USE_LIVEID'] == 'Y') { ?>
<tr>
    <td><input type="radio" id="auth_type_frm_liveid" name="BX_AUTH_TYPE" value="liveid" onc lick="SAFChangeAuthForm(this.value)"></td>
    <td><label for="auth_type_frm_liveid"><?=GetMessage('AUTH_A_LIVEID')?></label></td>
</tr>
<? } ?>
</form>
</table>
<?}?>

<div id="at_frm_bitrix">
<form method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>">
    <?
    if (strlen($arResult["BACKURL"]) > 0)
    {
    ?>
        <input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
    <?
    }
    ?>
    <?
    foreach ($arResult["POST"] as $key => $value)
    {
    ?>
    <input type="hidden" name="<?=$key?>" value="<?=$value?>" />
    <?
    }
    ?>
    <input type="hidden" name="AUTH_FORM" value="Y" />
    <input type="hidden" name="TYPE" value="AUTH" />

    <div id="auth_table">        
    <table width="95%">
            <tr>
                <td colspan="2" align="center">
                <div style="color:black;"><p align="left"><?=GetMessage("AUTH_LOGIN")?>:</p></div>
                <div id="inputtext" ><input type="text" name="USER_LOGIN" maxlength="50" value="<?=$arResult["USER_LOGIN"]?>" size="25" /></div></td>
            </tr>
            <tr>
                <td colspan="2" align="center">
               <div style="color:black;"><p align="left"> <?=GetMessage("AUTH_PASSWORD")?>:</p></div>
                <div id="inputtext"><input type="password" name="USER_PASSWORD" maxlength="50" size="25" /></div></td>
            </tr>
        <?
        if ($arResult["STORE_PASSWORD"] == "Y")
        {
        ?>
            <tr>
                <td align="center" valign="top"><input type="checkbox" id="USER_REMEMBER_frm" name="USER_REMEMBER" value="Y" />
                <label for="USER_REMEMBER_frm"><?=GetMessage("AUTH_REMEMBER_ME")?></label></td>
            </tr>
        <?
        }
        ?>
        <?
        if ($arResult["CAPTCHA_CODE"])
        {
        ?>
            <tr>
                <td colspan="2">
                <?echo GetMessage("AUTH_CAPTCHA_PROMT")?>:<br />
                <input type="hidden" name="captcha_sid" value="<?echo $arResult["CAPTCHA_CODE"]?>" />
                <img src="/bitrix/tools/captcha.php?captcha_sid=<?echo $arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" /><br /><br />
                <input type="text" name="captcha_word" maxlength="50" value="" /></td>
            </tr>
        <?
        }
        ?>
            <tr>
                <td colspan="2" align="center"><input type="image" src="/bitrix/templates/avtodok/images/AuthButtonLog.png"  name="Login" value="<?=GetMessage("AUTH_LOGIN_BUTTON")?>" /></td>
            </tr>

        <?
        if($arResult["NEW_USER_REGISTRATION"] == "Y")
        {
        ?>
        <div id="auth_reg">
            <tr>

                <td colspan="2"><noindex><a href="<?=$arResult["AUTH_REGISTER_URL"]?>" rel="nofollow"><?=GetMessage("AUTH_REGISTER")?></a></noindex><br /></td>

            </tr>
        </div>
        <?
        }
        ?>

            <tr>
        <div id="auth_reg_forgot">
                <td align="left" ><noindex><a  style="color:white; font-size: 9pt;"href="<?=$arResult["AUTH_FORGOT_PASSWORD_URL"]?>" rel="nofollow"><?=GetMessage("AUTH_FORGOT_PASSWORD_2")?></a></noindex></td>
        </div>
            
                <td  align="center">
                <noindex><b><a href="/regester.php" rel="nofollow" style="color:white; font-size: 9pt">Регистрация</a></b></noindex>
                </td>
            </tr>
    </table>
    </div>
</form>
</div>
<? if($arResult['NEW_USER_REGISTRATION'] == 'Y' && $arResult['USE_OPENID'] == 'Y'){?>
<div id="at_frm_openid" st yle="display: none">
<fo rm method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>">
    <table width="95%">
            <tr>
                <td colspan="2">
                <?=GetMessage("AUTH_OPENID")?>:<br />
                <input type="text" name="OPENID_IDENTITY" maxlength="50" value="<?=$arResult["USER_LOGIN"]?>" size="17" /></td>
            </tr>
            <tr>
                <td colspan="2"><input type="submit" name="Login" value="<?=GetMessage("AUTH_LOGIN_BUTTON")?>" /></td>
            </tr>

    </table>
</form>
</div>
<?}?>

<? if($arResult['NEW_USER_REGISTRATION'] == 'Y' && $arResult['USE_LIVEID'] == 'Y'){?>
<div id="at_frm_liveid" st yle="display: none"><noindex>
<a href="<?=$arResult['LIVEID_LOGIN_LINK']?>" rel="nofollow"><?=GetMessage('AUTH_LIVEID_LOGIN')?></a>
</noindex></div>
<?}?>

<?else:?>

<form action="<?=$arResult["AUTH_URL"]?>">
    <table width="95%">
        <tr>
            <td align="center">
                <?=$arResult["USER_NAME"]?><br />
                [<?=$arResult["USER_LOGIN"]?>]<br />
                <a style="color:white;" href="<?=$arResult["PROFILE_URL"]?>" title="<?=GetMessage("AUTH_PROFILE")?>"><?=GetMessage("AUTH_PROFILE")?></a><br />
            </td>
        </tr>
        <tr>
            <td align="center">
            <?foreach ($arResult["GET"] as $key => $value):?>
                <input type="hidden" name="<?=$key?>" value="<?=$value?>" />
            <?endforeach?>
            <input type="hidden" name="logout" value="yes" />
            <input type="image" src="/bitrix/templates/avtodok/components/bitrix/system.auth.form/template1/images/AuthButtonLogOut.png" name="logout_butt" value="<?=GetMessage("AUTH_LOGOUT_BUTTON")?>" />   
            </td>
        </tr>
    </table>
</form>
<?endif?>