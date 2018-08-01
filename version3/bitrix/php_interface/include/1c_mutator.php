<?php

// Создадим функцию, которая будет обновлять нестандартное свойство
// "Оригинальный номер", значения которого идут как атрибуты элемента
// Товар в CommerceML файле, и делать товар активным
// Кроме того запишем "Оригинальный номер" без пробелов в поле описания
// товара для предварительного просмотра
function catalog_product_mutator_1c(&$arLoadProduct, &$xProductNode, $bInsert)
 {
    global $arProperties;
    $sItemCode = trim($xProductNode->GetAttribute("Артикул"));
    $arLoadProduct["PROPERTY_VALUES"][$arProperties["PROPERTY_136"]][] =  "111".$sItemCode;
    return $arLoadProduct;
    }
?>