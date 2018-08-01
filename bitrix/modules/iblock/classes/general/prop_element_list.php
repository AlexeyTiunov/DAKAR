<?
class CIBlockPropertyElementList
{
	//PARAMETERS:
	//$arProperty - b_iblock_property.*
	//$value - array("VALUE","DESCRIPTION") -- here comes HTML form value
	//strHTMLControlName - array("VALUE","DESCRIPTION")
	//return:
	//safe html
	function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
	{
		static $cache = array();
		$IBLOCK_ID = $arProperty["LINK_IBLOCK_ID"];
		if(!array_key_exists($IBLOCK_ID, $cache))
		{
			$arSelect = array(
				"ID",
				"NAME",
			);
			$arFilter = array (
				"IBLOCK_ID"=> $arProperty["LINK_IBLOCK_ID"],
				//"ACTIVE" => "Y",
				"CHECK_PERMISSIONS" => "Y",
			);
			$arOrder = array(
				"NAME" => "ASC",
				"ID" => "ASC",
			);
			$cache[$IBLOCK_ID] = array();
			$rsItems = CIBlockElement::GetList($arOrder, $arFilter, false, false, $arSelect);
			while($arItem = $rsItems->GetNext())
				$cache[$IBLOCK_ID][] = $arItem;

		}
		$html = '<select name="'.$strHTMLControlName["VALUE"].'">
		<option value=""> </option>';
		foreach($cache[$IBLOCK_ID] as $arItem)
		{
			$html .= '<option value="'.$arItem["ID"].'"';
			if($value["VALUE"] == $arItem["~ID"])
				$html .= ' selected';
			$html .= '>'.$arItem["NAME"].'</option>';
		}
		$html .= '</select>';
		return  $html;
	}
}
?>
