<?
function stemming_init($sLang="ru")
{
	static $arStemFunc=array();

	if(!isset($arStemFunc[$sLang]))
	{
		$stemming_function_suf = $sLang;

		if(!function_exists("stemming_".$sLang))
		{
			$strFileName=$_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/php_interface/".$sLang."/search/stemming.php";
			if(file_exists($strFileName))
				@include($strFileName);
			if(!function_exists("stemming_".$sLang))
			{
				$strFileName=$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/search/tools/".$sLang."/stemming.php";
				if(file_exists($strFileName))
					@include($strFileName);
				if(!function_exists("stemming_".$sLang))
				{
					$stemming_function_suf = "default";
				}
			}
		}

		$stemming_stop_function = "stemming_stop_".$sLang;
		if(!function_exists($stemming_stop_function))
			$stemming_stop_function = "stemming_stop_default";

		$stemming_upper_function = "stemming_upper_".$sLang;
		if(!function_exists($stemming_upper_function))
			$stemming_upper_function = "stemming_upper_default";

		$letters = stemming_letter_default();
		$stemming_letter_function = "stemming_letter_".$sLang;
		if(function_exists($stemming_letter_function))
			$letters .= $stemming_letter_function();
		$letters .= COption::GetOptionString("search", "letters");

		$arStemFunc[$sLang] = array(
			"stem" => "stemming_".$stemming_function_suf,
			"stop" => $stemming_stop_function,
			"upper" => $stemming_upper_function,
			"letters" => $letters,
			"pcre_letters" => str_replace(
				array("\\"  , "-"  , "^"  , "]"  ),
				array("\\\\", "\\-", "\\^", "\\]"),
				$letters
			),
		);
	}

	return $arStemFunc[$sLang];
}

function stemming_upper($sText, $sLang="ru")
{
	$arStemFunc = stemming_init($sLang);
	$upper_function = $arStemFunc["upper"];
	return $upper_function($sText);
}

function stemming($sText, $sLang="ru")
{
	static $WORD_CACHE=array();
	static $STOP_CACHE=array();

	$arStemFunc = stemming_init($sLang);
	$stem_function = $arStemFunc["stem"];
	$stop_function = $arStemFunc["stop"];

	$word_cache = &$WORD_CACHE[$sLang];
	$stop_cache = &$STOP_CACHE[$sLang];
	//uppercase and remove punctuation marks

	$stems = array();

	$tok = " ";
	$sText = preg_replace("/[^".$arStemFunc["pcre_letters"]."]+/".BX_UTF_PCRE_MODIFIER, $tok, ToUpper($sText));

	$word = strtok($sText, $tok);
	while($word !== false)
	{
		$word = substr($word, 0, 50);
		if(isset($word_cache[$word]))
			$stem = $word_cache[$word];
		else
			$stem = $word_cache[$word] = $stem_function($word);
		if(!isset($stop_cache[$stem]))
			$stop_cache[$stem] = $stop_function($stem);
		if($stop_cache[$stem])
			$stems[$stem]++;
		$word = strtok($tok);
	}

	return $stems;
}

function stemming_upper_default($sText)
{
	return ToUpper($sText);
}

function stemming_default($sText)
{
	return $sText;
}
function stemming_stop_default($sWord)
{
	if(strlen($sWord) < 2)
		return false;
	else
		return true;
}
function stemming_letter_default()
{
	return "qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM0123456789";
}
?>
