<?
    if (!isset($_POST['AUTH_FORM']))
    {
     require('/media/Vol/www/bitrix/modules/main/include/prolog_before.php');
        $APPLICATION->IncludeComponent("bitrix:system.auth.form", "template1", array(
         "REGISTER_URL" => "/personal/profile/index.php",
         "PROFILE_URL" => "/personal/profile/",
         "SHOW_ERRORS" => "N"
         ),
    false
           );
    } 
    else 
    {
   require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php"); 
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/components/itg/Search/searchnew.php");  
   require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
    }
?>