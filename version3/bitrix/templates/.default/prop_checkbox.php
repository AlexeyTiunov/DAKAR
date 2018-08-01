<?php
class CIBlockPropertyCheckbox {
  function GetUserTypeDescription() {
    return array(
      'PROPERTY_TYPE' => 'S',
      'USER_TYPE' => 'Checkbox',
      'DESCRIPTION' => 'Флажок (Y/N)',

      'CheckFields' => array('CIBlockPropertyCheckbox', 'CheckFields'),
      'GetLength' => array('CIBlockPropertyCheckbox', 'GetLength'),
      'GetPropertyFieldHtml' => array('CIBlockPropertyCheckbox', 'GetEditField'),
      'GetAdminListViewHTML' => array('CIBlockPropertyCheckbox', 'GetFieldView'),
      'GetPublicViewHTML' => array('CIBlockPropertyCheckbox', 'GetFieldView'),
      'GetPublicEditHTML' => array('CIBlockPropertyCheckbox', 'GetEditField')
    );
  }

  function CheckFields($arProperty, $value) {
    return array();
  }

  function GetLength($arProperty, $value) {
    return strlen($value['VALUE']);
  }

  function GetEditField($arProperty, $value, $htmlElement) {
    return '<input type="hidden" name="' . $htmlElement['VALUE'] . '" value="N" />' . 
      '<input type="checkbox" name="' . $htmlElement['VALUE'] . '" value="Y"' .
     ($value['VALUE'] == 'Y' ? ' checked="checked"' : null) . ' />' . 
     ((isset($arProperty["WITH_DESCRIPTION"]) and $arProperty["WITH_DESCRIPTION"] == 'Y') ?
      '&nbsp;<input type="text" size="20" name="'.$htmlElement["DESCRIPTION"].'" value="'.htmlspecialchars($value["DESCRIPTION"]).'">' : '');
  }

  function GetFieldView($arProperty, $value, $htmlElement) {
    return $value['VALUE'] == 'Y' ? 'Да' : 'Нет';
  }
}