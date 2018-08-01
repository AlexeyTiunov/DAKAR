<?
IncludeModuleLangFile(__FILE__);

class CIBlockPropertyMapInterface
{
	function GetUserTypeDescription()
	{
		return array();
	
	}

	function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
	{
		return '';
	}

	function GetAdminListViewHTML($arProperty, $value, $strHTMLControlName)
	{
		return $value['VALUE'];
	}
	
	function GetPublicViewHTML($arProperty, $value, $strHTMLControlName)
	{
		return '';
	}
	
	function ConvertFromDB($arProperty, $value)
	{
		$arResult = array('VALUE' => '');
	
		if (strlen($value['VALUE']) > 0)
		{
			$arCoords = explode(',', $value['VALUE'], 2);

			$lat = doubleval($arCoords[0]);
			$lng = doubleval($arCoords[1]);
			
			if ($lat && $lng)
				$arResult['VALUE'] = $lat.','.$lng;
		}

		return $arResult;
	}
	
	function ConvertToDB($arProperty, $value)
	{
		$arResult = array('VALUE' => '');
	
		if (strlen($value['VALUE']) > 0)
		{
			$arCoords = explode(',', $value['VALUE'], 2);

			$lat = doubleval($arCoords[0]);
			$lng = doubleval($arCoords[1]);
			
			if ($lat && $lng)
				$arResult['VALUE'] = $lat.','.$lng;
		}

		return $arResult;
	}
}

class CIBlockPropertyMapGoogle extends CIBlockPropertyMapInterface
{
	function GetUserTypeDescription()
	{
		return array(
			"PROPERTY_TYPE"		=>"S",
			"USER_TYPE"		=>"map_google",
			"DESCRIPTION"		=>GetMessage("IBLOCK_PROP_MAP_GOOGLE"),
			"GetPropertyFieldHtml"	=>array("CIBlockPropertyMapGoogle","GetPropertyFieldHtml"),
			"GetPublicViewHTML"	=>array("CIBlockPropertyMapGoogle","GetPublicViewHTML"),
			"ConvertToDB"		=>array("CIBlockPropertyMapGoogle","ConvertToDB"),
			"ConvertFromDB"		=>array("CIBlockPropertyMapGoogle","ConvertFromDB"),
		);
	}
	
	function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
	{
		global $APPLICATION;
		
		if ($strHTMLControlName["MODE"] != "FORM_FILL")
			return '<input type="text" name="'.htmlspecialchars($strHTMLControlName['VALUE']).'" value="'.htmlspecialchars($value['VALUE']).'" />';
		
		if (strlen($value['VALUE']) > 0)
		{
			list($POINT_LAT, $POINT_LON) = explode(',', $value['VALUE'], 2);
			$bHasValue = true;
		}
		else
		{
			$POINT_LAT = doubleval(GetMessage('IBLOCK_PROP_MAP_GOOGLE_INIT_LAT'));
			$POINT_LON = doubleval(GetMessage('IBLOCK_PROP_MAP_GOOGLE_INIT_LON'));
			$bHasValue = false;
		}
		
		$MAP_ID = 'map_google_'.$arProperty['CODE'].'_'.$arProperty['ID'];
		
		$MAP_KEY = '';
		$strMapKeys = COPtion::GetOptionString('fileman', 'map_google_keys');

		$strDomain = $_SERVER['HTTP_HOST'];
		$wwwPos = strpos($strDomian, 'www.');
		if ($wwwPos === 0)
			$strDomain = substr($strDomain, 4);

		if ($strMapKeys)
		{
			$arMapKeys = unserialize($strMapKeys);
			
			if (array_key_exists($strDomain, $arMapKeys))
				$MAP_KEY = $arMapKeys[$strDomain];
		}
		
		if (!$MAP_KEY)
		{
?>
<?
			echo BeginNote();
?>
<div id="key_input_control_<?echo $MAP_ID?>">
		<?echo str_replace('#DOMAIN#', $strDomain, GetMessage('IBLOCK_PROP_MAP_GOOGLE_NO_KEY_MESSAGE'))?><br /><br />
		<?echo GetMessage('IBLOCK_PROP_MAP_GOOGLE_NO_KEY')?><input type="text" name="map_google_key_<?echo $MAP_ID?>" id="map_google_key_<?echo $MAP_ID?>" /> <input type="button" value="<?echo htmlspecialchars(GetMessage('IBLOCK_PROP_MAP_GOOGLE_NO_KEY_BUTTON'))?>" onclick="setGoogleKey('<?echo $strDomain?>', 'map_google_key_<?echo $MAP_ID?>')" /> <input type="button" value="<?echo htmlspecialchars(GetMessage('IBLOCK_PROP_MAP_GOOGLE_SAVE_KEY_BUTTON'))?>" onclick="saveGoogleKey('<?echo $strDomain?>', 'map_google_key_<?echo $MAP_ID?>')" />
</div>
<div id="key_input_message_<?echo $MAP_ID?>" style="display: none;"><?echo GetMessage('IBLOCK_PROP_MAP_GOOGLE_NO_KEY_OKMESSAGE')?></div>
<?
			echo EndNote();
?>
<?
		}
		
		//$MAP_KEY = 'ABQIAAAAQXbn2N6rCIOqXZDIj5oJNhRY7Nls--OG-1THezihS7AYttQ9ZBRbYg2HGOy7qxzaC4Qkym0jucri9w';
		
?>
<div id="bx_map_hint_<?echo $MAP_ID?>" style="display: none;">
	<div id="bx_map_hint_value_<?echo $MAP_ID?>" style="display: <?echo $bHasValue ? 'block' : 'none'?>;">
<?
		echo GetMessage('IBLOCK_PROP_MAP_GOOGLE_INSTR_VALUE').'<br /><br />';
?>
		<a href="javascript:void(0);" onclick="findPoint_<?echo $MAP_ID?>()"><?echo GetMessage('IBLOCK_PROP_MAP_GOOGLE_GOTO_POINT')?></a> | <a href="javascript:void(0);" onclick="if (confirm('<?echo CUtil::JSEscape(GetMessage('IBLOCK_PROP_MAP_GOOGLE_REMOVE_POINT_CONFIRM'))?>')) removePoint_<?echo $MAP_ID?>()"><?echo GetMessage('IBLOCK_PROP_MAP_GOOGLE_REMOVE_POINT')?></a><br /><br />
	</div>
	<div id="bx_map_hint_novalue_<?echo $MAP_ID?>" style="display: <?echo $bHasValue ? 'none' : 'block'?>;">
<?
		echo GetMessage('IBLOCK_PROP_MAP_GOOGLE_INSTR').'<br /><br />';
?>
	</div>
</div>
<?			
		$APPLICATION->IncludeComponent(
			'bitrix:map.google.system',
			'',
			array(
				'KEY' => $MAP_KEY,
				'INIT_MAP_TYPE' => 'NORMAL',
				'INIT_MAP_LON' => $POINT_LON ? $POINT_LON : 37.64,
				'INIT_MAP_LAT' => $POINT_LAT ? $POINT_LAT : 55.76,
				'INIT_MAP_SCALE' => 10,
				'OPTIONS' => array('ENABLE_SCROLL_ZOOM', 'ENABLE_DRAGGING'),
				'CONTROLS' => array('LARGE_MAP_CONTROL', 'HTYPECONTROL', 'MINIMAP', 'SCALELINE'),
				'MAP_WIDTH' => '95%',
				'MAP_HEIGHT' => 400,
				'MAP_ID' => $MAP_ID,
				'DEV_MODE' => 'Y',
				'WAIT_FOR_EVENT' => $MAP_KEY ? '' : 'LoadMap_'.$MAP_ID
			),
			false, array('HIDE_ICONS' => 'Y')
		);
?>
<div id="bx_address_search_control_<?echo $MAP_ID?>" style="display: none;"><?echo GetMessage('IBLOCK_PROP_MAP_GOOGLE_SEARCH')?><input type="text" name="bx_address_<?echo $MAP_ID?>" id="bx_address_<?echo $MAP_ID?>" value="" style="width: 300px;" autocomplete="off" /></div>
<input type="hidden" id="value_<?echo $MAP_ID;?>" name="<?=htmlspecialchars($strHTMLControlName["VALUE"])?>" value="<?=htmlspecialcharsEx($value["VALUE"])?>" />
<script type="text/javascript">
window.jsAdminGoogleMess = {
	nothing_found: '<?echo CUtil::JSEscape(GetMessage('IBLOCK_PROP_MAP_GOOGLE_NOTHING_FOUND'))?>'
}
jsUtils.loadCSSFile('/bitrix/components/bitrix/map.google.view/settings/settings.css');

function BXWaitForMap_<?echo $MAP_ID?>()
{
	if (!window.GLOBAL_arMapObjects['<?echo $MAP_ID?>'])
		setTimeout(BXWaitForMap_<?echo $MAP_ID?>, 300);
	else
	{
		window.obPoint_<?echo $MAP_ID?> = null;
		GEvent.addListener(window.GLOBAL_arMapObjects['<?echo $MAP_ID?>'], 'dblclick', setPointValue_<?echo $MAP_ID?>);
		document.getElementById('bx_address_<?echo $MAP_ID?>').onkeypress = jsGoogleCESearch_<?echo $MAP_ID;?>.setTypingStarted;
<?
		if ($bHasValue):
?>
		setPointValue_<?echo $MAP_ID?>(null, new GLatLng(<?echo $POINT_LAT?>, <?echo $POINT_LON?>));
<?
		endif;
?>

		document.getElementById('bx_address_search_control_<?echo $MAP_ID?>').style.display = 'block';
		document.getElementById('bx_map_hint_<?echo $MAP_ID?>').style.display = 'block';
	}
}

<?
if ($MAP_KEY):
	if (defined('BX_PUBLIC_MODE') && BX_PUBLIC_MODE == 1):
?>
setTimeout(BXWaitForMap_<?echo $MAP_ID?>, 1000);
<?
	else:
?>
jsUtils.addEvent(window, 'load', BXWaitForMap_<?echo $MAP_ID?>);
<?
	endif;
else:
?>
function setGoogleKey(domain, input)
{
	LoadMap_<?echo $MAP_ID?>(document.getElementById(input).value);
	BXWaitForMap_<?echo $MAP_ID?>();
}

function saveGoogleKey(domain, input)
{
	var value = document.getElementById(input).value;
	
	CHttpRequest.Action = function(result)
	{
		CloseWaitWindow();
		if (result == 'OK')
		{
			document.getElementById('key_input_control_<?echo $MAP_ID?>').style.display = 'none';
			document.getElementById('key_input_message_<?echo $MAP_ID?>').style.display = 'block';
			if (!window.GMap2) 
				setGoogleKey(domain, input);
		}
		else
			alert('<?echo CUtil::JSEscape(GetMessage('IBLOCK_PROP_MAP_GOOGLE_NO_KEY_ERRORMESSAGE'))?>');
	}
	
	var data = 'key_type=google&domain=' + domain + '&key=' + value;
	ShowWaitWindow();
	CHttpRequest.Post('/bitrix/admin/settings.php?lang=<?echo LANGUAGE_ID?>&mid=fileman&save_map_key=Y', data);
}
<?
endif;
?>

function findPoint_<?echo $MAP_ID?>()
{
	if (null != window.obPoint_<?echo $MAP_ID?>)
		window.GLOBAL_arMapObjects['<?echo $MAP_ID?>'].panTo(window.obPoint_<?echo $MAP_ID?>.getLatLng());
}

function removePoint_<?echo $MAP_ID?>()
{
	window.GLOBAL_arMapObjects['<?echo $MAP_ID?>'].removeOverlay(window.obPoint_<?echo $MAP_ID?>);
	window.obPoint_<?echo $MAP_ID?> = null;
	
	document.getElementById('bx_map_hint_novalue_<?echo $MAP_ID?>').style.display = 'block';
	document.getElementById('bx_map_hint_value_<?echo $MAP_ID?>').style.display = 'none';
	
	updatePointPosition_<?echo $MAP_ID?>();
}

function setPointValue_<?echo $MAP_ID?>(obnull, obPoint)
{
	if (null == window.obPoint_<?echo $MAP_ID?>)
	{
		window.obPoint_<?echo $MAP_ID?> = new GMarker(obPoint, {draggable:true});
		window.GLOBAL_arMapObjects['<?echo $MAP_ID?>'].addOverlay(window.obPoint_<?echo $MAP_ID?>);
		GEvent.addListener(window.obPoint_<?echo $MAP_ID?>, "dragend", updatePointPosition_<?echo $MAP_ID?>);
	}
	else
	{
		window.obPoint_<?echo $MAP_ID?>.setLatLng(obPoint);
	}

	document.getElementById('bx_map_hint_novalue_<?echo $MAP_ID?>').style.display = 'none';
	document.getElementById('bx_map_hint_value_<?echo $MAP_ID?>').style.display = 'block';
	
	updatePointPosition_<?echo $MAP_ID?>(obPoint);
}

function updatePointPosition_<?echo $MAP_ID?>(obPoint)
{
	var obInput = document.getElementById('value_<?echo $MAP_ID?>');
	obInput.value = null == obPoint ? '' : obPoint.lat() + ',' + obPoint.lng();
}

var jsGoogleCESearch_<?echo $MAP_ID;?> = {
	bInited: false,

	map: null,
	geocoder: null,
	obInput: null,
	timerID: null,
	timerDelay: 1000,
	
	arSearchResults: [],
	
	obOut: null,
	
	__init: function(input)
	{
		if (jsGoogleCESearch_<?echo $MAP_ID;?>.bInited) return;
		
		jsGoogleCESearch_<?echo $MAP_ID;?>.map = window.GLOBAL_arMapObjects['<?echo $MAP_ID?>'];
		jsGoogleCESearch_<?echo $MAP_ID;?>.obInput = input;
		
		//input.form.onsubmit = function() {jsGoogleCESearch_<?echo $MAP_ID;?>.doSearch(); return false;}
		
		input.onfocus = jsGoogleCESearch_<?echo $MAP_ID;?>.showResults;
		input.onblur = jsGoogleCESearch_<?echo $MAP_ID;?>.hideResults;
		
		jsGoogleCESearch_<?echo $MAP_ID;?>.bInited = true;
	},
	
	setTypingStarted: function(e)
	{
		if (null == e)
			e = window.event;
			
		if (e.keyCode == 13)
		{
			jsGoogleCESearch_<?echo $MAP_ID;?>.doSearch();
			return false;
		}
		else
		{
			if (!jsGoogleCESearch_<?echo $MAP_ID;?>.bInited)
				jsGoogleCESearch_<?echo $MAP_ID;?>.__init(this);

			jsGoogleCESearch_<?echo $MAP_ID;?>.hideResults();
				
			if (null != jsGoogleCESearch_<?echo $MAP_ID;?>.timerID)
				clearTimeout(jsGoogleCESearch_<?echo $MAP_ID;?>.timerID);
		
			jsGoogleCESearch_<?echo $MAP_ID;?>.timerID = setTimeout(jsGoogleCESearch_<?echo $MAP_ID;?>.doSearch, jsGoogleCESearch_<?echo $MAP_ID;?>.timerDelay);
		}
	},
	
	doSearch: function()
	{
		var value = jsUtils.trim(jsGoogleCESearch_<?echo $MAP_ID;?>.obInput.value);
		if (value.length > 1)
		{
			if (null == jsGoogleCESearch_<?echo $MAP_ID;?>.geocoder)
				jsGoogleCESearch_<?echo $MAP_ID;?>.geocoder = new GClientGeocoder();
		
			jsGoogleCESearch_<?echo $MAP_ID;?>.geocoder.getLocations(value, jsGoogleCESearch_<?echo $MAP_ID;?>.__searchResultsLoad);
		}
	},
	
	handleError: function()
	{
		alert(jsGoogleCE.jsMess.mess_error);
	},
	
	__generateOutput: function()
	{
		var obPos = jsUtils.GetRealPos(jsGoogleCESearch_<?echo $MAP_ID;?>.obInput);
		
		jsGoogleCESearch_<?echo $MAP_ID;?>.obOut = document.body.appendChild(document.createElement('UL'));
		jsGoogleCESearch_<?echo $MAP_ID;?>.obOut.className = 'bx-google-address-search-results';
		jsGoogleCESearch_<?echo $MAP_ID;?>.obOut.style.top = (obPos.bottom + 2) + 'px';
		jsGoogleCESearch_<?echo $MAP_ID;?>.obOut.style.left = obPos.left + 'px';
	},

	__searchResultsLoad: function(obResult)
	{
		var _this = jsGoogleCESearch_<?echo $MAP_ID;?>;
		
		if (!obResult)
		{
			_this.handleError();
		}
		else
		{
			if (null == _this.obOut)
				_this.__generateOutput();
			
			_this.obOut.innerHTML = '';
			_this.clearSearchResults();
		
			if (obResult.Status.code == 200)
				for (var len = 0; obResult.Placemark[len]; len++) {}
			else
				var len = 0;
			
			if (len > 0) 
			{
				for (var i = 0; i < len; i++)
				{
					_this.arSearchResults[i] = new GLatLng(
						obResult.Placemark[i].Point.coordinates[1], 
						obResult.Placemark[i].Point.coordinates[0]
					);
					
					var obListElement = document.createElement('LI');
					
					if (i == 0)
						obListElement.className = 'bx-google-first';

					var obLink = document.createElement('A');
					obLink.href = "javascript:void(0)";
					var obText = obLink.appendChild(document.createElement('SPAN'));
					obText.appendChild(document.createTextNode(obResult.Placemark[i].address));
					
					obLink.BXSearchIndex = i;
					obLink.onclick = _this.__showSearchResult;
					
					obListElement.appendChild(obLink);
					_this.obOut.appendChild(obListElement);
				}
			} 
			else 
			{
				//var str = _this.jsMess.mess_search_empty;
				_this.obOut.innerHTML = '<li class="bx-google-notfound">' + window.jsAdminGoogleMess.nothing_found + '</li>';
			}
			
			_this.showResults();
		}
		
		//_this.map.redraw();
	},
	
	__showSearchResult: function()
	{
		if (null !== this.BXSearchIndex)
		{
			jsGoogleCESearch_<?echo $MAP_ID;?>.map.panTo(jsGoogleCESearch_<?echo $MAP_ID;?>.arSearchResults[this.BXSearchIndex]);
		}
	},
	
	showResults: function()
	{
		if (null != jsGoogleCESearch_<?echo $MAP_ID;?>.obOut)
			jsGoogleCESearch_<?echo $MAP_ID;?>.obOut.style.display = 'block';
	},

	hideResults: function()
	{
		if (null != jsGoogleCESearch_<?echo $MAP_ID;?>.obOut)
		{
			setTimeout("jsGoogleCESearch_<?echo $MAP_ID;?>.obOut.style.display = 'none'", 300);
		}
	},
	
	clearSearchResults: function()
	{
		for (var i = 0; i < jsGoogleCESearch_<?echo $MAP_ID;?>.arSearchResults.length; i++)
		{
			delete jsGoogleCESearch_<?echo $MAP_ID;?>.arSearchResults[i];
		}

		jsGoogleCESearch_<?echo $MAP_ID;?>.arSearchResults = [];
	},
	
	clear: function()
	{
		if (!jsGoogleCESearch_<?echo $MAP_ID;?>.bInited)
			return;
			
		jsGoogleCESearch_<?echo $MAP_ID;?>.bInited = false;
		if (null != jsGoogleCESearch_<?echo $MAP_ID;?>.obOut)
		{
			jsGoogleCESearch_<?echo $MAP_ID;?>.obOut.parentNode.removeChild(jsGoogleCESearch_<?echo $MAP_ID;?>.obOut);
			jsGoogleCESearch_<?echo $MAP_ID;?>.obOut = null;
		}
		
		jsGoogleCESearch_<?echo $MAP_ID;?>.arSearchResults = [];
		jsGoogleCESearch_<?echo $MAP_ID;?>.map = null;
		jsGoogleCESearch_<?echo $MAP_ID;?>.geocoder = null;
		jsGoogleCESearch_<?echo $MAP_ID;?>.obInput = null;
		jsGoogleCESearch_<?echo $MAP_ID;?>.timerID = null;
	}
}
</script>
<?
	}
	
	function GetPublicViewHTML($arProperty, $value, $strHTMLControlName)
	{
		$MAP_KEY = '';
		$strMapKeys = COPtion::GetOptionString('fileman', 'map_google_keys');

		$strDomain = $_SERVER['HTTP_HOST'];
		$wwwPos = strpos($strDomian, 'www.');
		if ($wwwPos === 0)
			$strDomain = substr($strDomain, 4);

		if ($strMapKeys)
		{
			$arMapKeys = unserialize($strMapKeys);
			
			if (array_key_exists($strDomain, $arMapKeys))
				$MAP_KEY = $arMapKeys[$strDomain];
		}
	
		$s = '';
		if(strlen($value["VALUE"])>0)
		{
			$value = parent::ConvertFromDB($arProperty, $value);
			$arCoords = explode(',', $value['VALUE']);
			ob_start();
			$GLOBALS['APPLICATION']->IncludeComponent(
				'bitrix:map.google.view',
				'',
				array(
					'KEY' => $MAP_KEY,
					'MAP_DATA' => serialize(array(
						'google_lat' => $arCoords[0],
						'google_lon' => $arCoords[1],
						'PLACEMARKS' => array(
							array(
								'LON' => $arCoords[1],
								'LAT' => $arCoords[0],
							),
						),
					)),
					'MAP_ID' => 'MAP_GOOGLE_VIEW_'.$arProperty['IBLOCK_ID'].'_'.$arProperty['ID'],
					'DEV_MODE' => 'Y',
				),
				false, array('HIDE_ICONS' => 'Y')
			);
			
			
			$s .= ob_get_contents();
			ob_end_clean();
		}
		
		return $s;
	}
}

class CIBlockPropertyMapYandex extends CIBlockPropertyMapInterface
{
	function GetUserTypeDescription()
	{
		return array(
			"PROPERTY_TYPE"		=>"S",
			"USER_TYPE"		=>"map_yandex",
			"DESCRIPTION"		=>GetMessage("IBLOCK_PROP_MAP_YANDEX"),
			"GetPropertyFieldHtml"	=>array("CIBlockPropertyMapYandex","GetPropertyFieldHtml"),
			"GetPublicViewHTML"	=>array("CIBlockPropertyMapYandex","GetPublicViewHTML"),
			"ConvertToDB"		=>array("CIBlockPropertyMapYandex","ConvertToDB"),
			"ConvertFromDB"		=>array("CIBlockPropertyMapYandex","ConvertFromDB"),
		);
	}
	
	function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
	{
		global $APPLICATION;

		if ($strHTMLControlName["MODE"] != "FORM_FILL")
			return '<input type="text" name="'.htmlspecialchars($strHTMLControlName['VALUE']).'" value="'.htmlspecialchars($value['VALUE']).'" />';
		
		if (strlen($value['VALUE']) > 0)
		{
			list($POINT_LAT, $POINT_LON) = explode(',', $value['VALUE'], 2);
			$bHasValue = true;
		}
		else
		{
			$POINT_LAT = doubleval(GetMessage('IBLOCK_PROP_MAP_YANDEX_INIT_LAT'));
			$POINT_LON = doubleval(GetMessage('IBLOCK_PROP_MAP_YANDEX_INIT_LON'));
			$bHasValue = false;
		}
		
		$MAP_ID = 'map_yandex_'.$arProperty['CODE'].'_'.$arProperty['ID'];
		
		$MAP_KEY = '';
		$strMapKeys = COPtion::GetOptionString('fileman', 'map_yandex_keys');

		$strDomain = $_SERVER['HTTP_HOST'];
		$wwwPos = strpos($strDomian, 'www.');
		if ($wwwPos === 0)
			$strDomain = substr($strDomain, 4);

		if ($strMapKeys)
		{
			$arMapKeys = unserialize($strMapKeys);
			
			if (array_key_exists($strDomain, $arMapKeys))
				$MAP_KEY = $arMapKeys[$strDomain];
		}
		
		if (!$MAP_KEY)
		{
?>
<?
			echo BeginNote();
?>
<div id="key_input_control_<?echo $MAP_ID?>">
		<?echo str_replace('#DOMAIN#', $strDomain, GetMessage('IBLOCK_PROP_MAP_YANDEX_NO_KEY_MESSAGE'))?><br /><br />
		<?echo GetMessage('IBLOCK_PROP_MAP_YANDEX_NO_KEY')?><input type="text" name="map_yandex_key_<?echo $MAP_ID?>" id="map_yandex_key_<?echo $MAP_ID?>" /> <input type="button" value="<?echo htmlspecialchars(GetMessage('IBLOCK_PROP_MAP_YANDEX_NO_KEY_BUTTON'))?>" onclick="setYandexKey('<?echo $strDomain?>', 'map_yandex_key_<?echo $MAP_ID?>')" /> <input type="button" value="<?echo htmlspecialchars(GetMessage('IBLOCK_PROP_MAP_YANDEX_SAVE_KEY_BUTTON'))?>" onclick="saveYandexKey('<?echo $strDomain?>', 'map_yandex_key_<?echo $MAP_ID?>')" />
</div>
<div id="key_input_message_<?echo $MAP_ID?>" style="display: none;"><?echo GetMessage('IBLOCK_PROP_MAP_YANDEX_NO_KEY_OKMESSAGE')?></div>
<?
			echo EndNote();
?>
<?
		}
		
?>
<div id="bx_map_hint_<?echo $MAP_ID?>" style="display: none;">
	<div id="bx_map_hint_value_<?echo $MAP_ID?>" style="display: <?echo $bHasValue ? 'block' : 'none'?>;">
<?
		echo GetMessage('IBLOCK_PROP_MAP_YANDEX_INSTR_VALUE').'<br /><br />';
?>
		<a href="javascript:void(0);" onclick="findPoint_<?echo $MAP_ID?>()"><?echo GetMessage('IBLOCK_PROP_MAP_YANDEX_GOTO_POINT')?></a> | <a href="javascript:void(0);" onclick="if (confirm('<?echo CUtil::JSEscape(GetMessage('IBLOCK_PROP_MAP_YANDEX_REMOVE_POINT_CONFIRM'))?>')) removePoint_<?echo $MAP_ID?>()"><?echo GetMessage('IBLOCK_PROP_MAP_YANDEX_REMOVE_POINT')?></a><br /><br />
	</div>
	<div id="bx_map_hint_novalue_<?echo $MAP_ID?>" style="display: <?echo $bHasValue ? 'none' : 'block'?>;">
<?
		echo GetMessage('IBLOCK_PROP_MAP_YANDEX_INSTR').'<br /><br />';
?>
	</div>
</div>
<?
		$APPLICATION->IncludeComponent(
			'bitrix:map.yandex.system',
			'',
			array(
				'KEY' => $MAP_KEY,
				'INIT_MAP_TYPE' => 'NORMAL',
				'INIT_MAP_LON' => $POINT_LON ? $POINT_LON : 37.64,
				'INIT_MAP_LAT' => $POINT_LAT ? $POINT_LAT : 55.76,
				'INIT_MAP_SCALE' => 10,
				'OPTIONS' => array('ENABLE_SCROLL_ZOOM', 'ENABLE_DRAGGING'),
				'CONTROLS' => array('TOOLBAR', 'ZOOM', 'TYPECONTROL', 'MINIMAP', 'SCALELINE'),
				'MAP_WIDTH' => '95%',
				'MAP_HEIGHT' => 400,
				'MAP_ID' => $MAP_ID,
				'DEV_MODE' => 'Y',
				'WAIT_FOR_EVENT' => $MAP_KEY ? '' : 'LoadMap_'.$MAP_ID,
				'ONMAPREADY' => 'BXWaitForMap_'.$MAP_ID
			),
			false, array('HIDE_ICONS' => 'Y')
		);
?>
<div id="bx_address_search_control_<?echo $MAP_ID?>" style="display: none;"><?echo GetMessage('IBLOCK_PROP_MAP_YANDEX_SEARCH')?><input type="text" name="bx_address_<?echo $MAP_ID?>" id="bx_address_<?echo $MAP_ID?>" value="" style="width: 300px;" autocomplete="off" /></div>
<input type="hidden" id="value_<?echo $MAP_ID;?>" name="<?=htmlspecialchars($strHTMLControlName["VALUE"])?>" value="<?=htmlspecialcharsEx($value["VALUE"])?>" />
<script type="text/javascript">
window.jsAdminYandexMess = {
	nothing_found: '<?echo CUtil::JSEscape(GetMessage('IBLOCK_PROP_MAP_YANDEX_NOTHING_FOUND'))?>'
}
jsUtils.loadCSSFile('/bitrix/components/bitrix/map.yandex.view/settings/settings.css');

function BXWaitForMap_<?echo $MAP_ID?>()
{
	window.obPoint_<?echo $MAP_ID?> = null;
	window.GLOBAL_arMapObjects['<?echo $MAP_ID?>'].bx_context.YMaps.Events.observe(window.GLOBAL_arMapObjects['<?echo $MAP_ID?>'], window.GLOBAL_arMapObjects['<?echo $MAP_ID?>'].Events.DblClick, setPointValue_<?echo $MAP_ID?>);
		
	document.getElementById('bx_address_<?echo $MAP_ID?>').onkeypress = jsYandexCESearch_<?echo $MAP_ID;?>.setTypingStarted;
<?
		if ($bHasValue):
?>
	setPointValue_<?echo $MAP_ID?>(new window.GLOBAL_arMapObjects['<?echo $MAP_ID?>'].bx_context.YMaps.GeoPoint(<?echo $POINT_LON?>, <?echo $POINT_LAT?>));
<?
		endif;
?>

	document.getElementById('bx_address_search_control_<?echo $MAP_ID?>').style.display = 'block';
	document.getElementById('bx_map_hint_<?echo $MAP_ID?>').style.display = 'block';
}

<?
if ($MAP_KEY): /*
	if (defined('BX_PUBLIC_MODE') && BX_PUBLIC_MODE == 1):
?>
setTimeout(BXWaitForMap_<?echo $MAP_ID?>, 1000);
<?
	else:
?>
jsUtils.addEvent(window, 'load', BXWaitForMap_<?echo $MAP_ID?>);
<?
	endif; */
else:
?>
function setYandexKey(domain, input)
{
	LoadMap_<?echo $MAP_ID?>(document.getElementById(input).value);
	//BXWaitForMap_<?echo $MAP_ID?>();
}

function saveYandexKey(domain, input)
{
	var value = document.getElementById(input).value;
	
	CHttpRequest.Action = function(result)
	{
		CloseWaitWindow();
		if (result == 'OK')
		{
			document.getElementById('key_input_control_<?echo $MAP_ID?>').style.display = 'none';
			document.getElementById('key_input_message_<?echo $MAP_ID?>').style.display = 'block';
			if (!window.GLOBAL_arMapObjects['<?echo $MAP_ID?>']) 
				setYandexKey(domain, input);
		}
		else
			alert('<?echo CUtil::JSEscape(GetMessage('IBLOCK_PROP_MAP_YANDEX_NO_KEY_ERRORMESSAGE'))?>');
	}
	
	var data = 'key_type=yandex&domain=' + domain + '&key=' + value;
	ShowWaitWindow();
	CHttpRequest.Post('/bitrix/admin/settings.php?lang=<?echo LANGUAGE_ID?>&mid=fileman&save_map_key=Y', data);
}
<?
endif;
?>

function findPoint_<?echo $MAP_ID?>()
{
	if (null != window.obPoint_<?echo $MAP_ID?>)
		window.GLOBAL_arMapObjects['<?echo $MAP_ID?>'].panTo(window.obPoint_<?echo $MAP_ID?>.getGeoPoint());
}

function removePoint_<?echo $MAP_ID?>()
{
	window.GLOBAL_arMapObjects['<?echo $MAP_ID?>'].removeOverlay(window.obPoint_<?echo $MAP_ID?>);
	window.obPoint_<?echo $MAP_ID?> = null;
	
	document.getElementById('bx_map_hint_novalue_<?echo $MAP_ID?>').style.display = 'block';
	document.getElementById('bx_map_hint_value_<?echo $MAP_ID?>').style.display = 'none';

	updatePointPosition_<?echo $MAP_ID?>();
}

// !!!
function setPointValue_<?echo $MAP_ID?>(obEvent)
{
	if (null != obEvent.getGeoPoint)
		var obPoint = obEvent.getGeoPoint();
	else
		var obPoint = obEvent;

	if (null == window.obPoint_<?echo $MAP_ID?>)
	{
		window.obPoint_<?echo $MAP_ID?> = new window.GLOBAL_arMapObjects['<?echo $MAP_ID?>'].bx_context.YMaps.Placemark(obPoint, {draggable:true});
		window.GLOBAL_arMapObjects['<?echo $MAP_ID?>'].addOverlay(window.obPoint_<?echo $MAP_ID?>);
		window.GLOBAL_arMapObjects['<?echo $MAP_ID?>'].bx_context.YMaps.Events.observe(window.obPoint_<?echo $MAP_ID?>, window.obPoint_<?echo $MAP_ID?>.Events.DragEnd, updatePointPosition_<?echo $MAP_ID?>);
	}
	else
	{
		window.obPoint_<?echo $MAP_ID?>.setGeoPoint(obPoint);
	}

	document.getElementById('bx_map_hint_novalue_<?echo $MAP_ID?>').style.display = 'none';
	document.getElementById('bx_map_hint_value_<?echo $MAP_ID?>').style.display = 'block';

	updatePointPosition_<?echo $MAP_ID?>(obPoint);
	window.GLOBAL_arMapObjects['<?echo $MAP_ID?>'].panTo(obPoint_<?echo $MAP_ID?>.getGeoPoint());
}

function updatePointPosition_<?echo $MAP_ID?>(obPoint)
{
	//var obPosition = obPoint.getGeoPoint();
	if (null != obPoint && null != obPoint.getGeoPoint)
		obPoint = obPoint.getGeoPoint();

	var obInput = document.getElementById('value_<?echo $MAP_ID?>');
	obInput.value = null == obPoint ? '' : obPoint.getLat() + ',' + obPoint.getLng();
}

var jsYandexCESearch_<?echo $MAP_ID;?> = {
	bInited: false,

	map: null,
	geocoder: null,
	obInput: null,
	timerID: null,
	timerDelay: 1000,
	
	arSearchResults: [],
	
	obOut: null,
	
	__init: function(input)
	{
		if (jsYandexCESearch_<?echo $MAP_ID;?>.bInited) return;
		
		jsYandexCESearch_<?echo $MAP_ID;?>.map = window.GLOBAL_arMapObjects['<?echo $MAP_ID?>'];
		jsYandexCESearch_<?echo $MAP_ID;?>.obInput = input;
		
		input.onfocus = jsYandexCESearch_<?echo $MAP_ID;?>.showResults;
		input.onblur = jsYandexCESearch_<?echo $MAP_ID;?>.hideResults;
		
		jsYandexCESearch_<?echo $MAP_ID;?>.bInited = true;
	},
	
	setTypingStarted: function(e)
	{
		if (null == e)
			e = window.event;
			
		if (e.keyCode == 13)
		{
			jsYandexCESearch_<?echo $MAP_ID;?>.doSearch();
			return false;
		}
		else
		{
			if (!jsYandexCESearch_<?echo $MAP_ID;?>.bInited)
				jsYandexCESearch_<?echo $MAP_ID;?>.__init(this);

			jsYandexCESearch_<?echo $MAP_ID;?>.hideResults();
				
			if (null != jsYandexCESearch_<?echo $MAP_ID;?>.timerID)
				clearTimeout(jsYandexCESearch_<?echo $MAP_ID;?>.timerID);
		
			jsYandexCESearch_<?echo $MAP_ID;?>.timerID = setTimeout(jsYandexCESearch_<?echo $MAP_ID;?>.doSearch, jsYandexCESearch_<?echo $MAP_ID;?>.timerDelay);
		}
	},
	
	doSearch: function()
	{
		var value = jsUtils.trim(jsYandexCESearch_<?echo $MAP_ID;?>.obInput.value);
		if (value.length > 1)
		{
			var geocoder = new window.GLOBAL_arMapObjects['<?echo $MAP_ID?>'].bx_context.YMaps.Geocoder(value);
		
			window.GLOBAL_arMapObjects['<?echo $MAP_ID?>'].bx_context.YMaps.Events.observe(
				geocoder, 
				geocoder.Events.Load, 
				jsYandexCESearch_<?echo $MAP_ID;?>.__searchResultsLoad
			);
			
			window.GLOBAL_arMapObjects['<?echo $MAP_ID?>'].bx_context.YMaps.Events.observe(
				geocoder, 
				geocoder.Events.Fault, 
				jsYandexCESearch_<?echo $MAP_ID;?>.handleError
			);
		}
	},
	
	handleError: function(error)
	{
		alert(this.jsMess.mess_error + ': ' + error.message);
	},
	
	__generateOutput: function()
	{
		var obPos = jsUtils.GetRealPos(jsYandexCESearch_<?echo $MAP_ID;?>.obInput);
		
		jsYandexCESearch_<?echo $MAP_ID;?>.obOut = document.body.appendChild(document.createElement('UL'));
		jsYandexCESearch_<?echo $MAP_ID;?>.obOut.className = 'bx-yandex-address-search-results';
		jsYandexCESearch_<?echo $MAP_ID;?>.obOut.style.top = (obPos.bottom + 2) + 'px';
		jsYandexCESearch_<?echo $MAP_ID;?>.obOut.style.left = obPos.left + 'px';
	},

	__searchResultsLoad: function(geocoder)
	{
		var _this = jsYandexCESearch_<?echo $MAP_ID;?>;
	
		if (null == _this.obOut)
			_this.__generateOutput();
			
		_this.obOut.innerHTML = '';
		_this.clearSearchResults();
		
		if (len = geocoder.length()) 
		{
			for (var i = 0; i < len; i++)
			{
				_this.arSearchResults[i] = geocoder.get(i);
				
				var obListElement = document.createElement('LI');
				
				if (i == 0)
					obListElement.className = 'bx-yandex-first';

				var obLink = document.createElement('A');
				obLink.href = "javascript:void(0)";
				var obText = obLink.appendChild(document.createElement('SPAN'));
				obText.appendChild(document.createTextNode(_this.arSearchResults[i].text));
				
				obLink.BXSearchIndex = i;
				obLink.onclick = _this.__showSearchResult;
				
				obListElement.appendChild(obLink);
				_this.obOut.appendChild(obListElement);
			}
		} 
		else 
		{
			//var str = _this.jsMess.mess_search_empty;
			_this.obOut.innerHTML = '<li class="bx-yandex-notfound">' + window.jsAdminYandexMess.nothing_found + '</li>';
		}
		
		_this.showResults();
		
		//_this.map.redraw();
	},
	
	__showSearchResult: function()
	{
		if (null !== this.BXSearchIndex)
		{
			jsYandexCESearch_<?echo $MAP_ID;?>.map.panTo(jsYandexCESearch_<?echo $MAP_ID;?>.arSearchResults[this.BXSearchIndex].getGeoPoint());
			jsYandexCESearch_<?echo $MAP_ID;?>.map.redraw();
		}
	},
	
	showResults: function()
	{
		if (null != jsYandexCESearch_<?echo $MAP_ID;?>.obOut)
			jsYandexCESearch_<?echo $MAP_ID;?>.obOut.style.display = 'block';
	},

	hideResults: function()
	{
		if (null != jsYandexCESearch_<?echo $MAP_ID;?>.obOut)
		{
			setTimeout("jsYandexCESearch_<?echo $MAP_ID;?>.obOut.style.display = 'none'", 300);
		}
	},
	
	clearSearchResults: function()
	{
		for (var i = 0; i < jsYandexCESearch_<?echo $MAP_ID;?>.arSearchResults.length; i++)
		{
			delete jsYandexCESearch_<?echo $MAP_ID;?>.arSearchResults[i];
		}

		jsYandexCESearch_<?echo $MAP_ID;?>.arSearchResults = [];
	},
	
	clear: function()
	{
		if (!jsYandexCESearch_<?echo $MAP_ID;?>.bInited)
			return;
			
		jsYandexCESearch_<?echo $MAP_ID;?>.bInited = false;
		if (null != jsYandexCESearch_<?echo $MAP_ID;?>.obOut)
		{
			jsYandexCESearch_<?echo $MAP_ID;?>.obOut.parentNode.removeChild(jsYandexCESearch_<?echo $MAP_ID;?>.obOut);
			jsYandexCESearch_<?echo $MAP_ID;?>.obOut = null;
		}
		
		jsYandexCESearch_<?echo $MAP_ID;?>.arSearchResults = [];
		jsYandexCESearch_<?echo $MAP_ID;?>.map = null;
		jsYandexCESearch_<?echo $MAP_ID;?>.geocoder = null;
		jsYandexCESearch_<?echo $MAP_ID;?>.obInput = null;
		jsYandexCESearch_<?echo $MAP_ID;?>.timerID = null;
	}
}

</script>
<?
	}
	
	function GetPublicViewHTML($arProperty, $value, $strHTMLControlName)
	{
		$MAP_KEY = '';
		$strMapKeys = COPtion::GetOptionString('fileman', 'map_yandex_keys');

		$strDomain = $_SERVER['HTTP_HOST'];
		$wwwPos = strpos($strDomian, 'www.');
		if ($wwwPos === 0)
			$strDomain = substr($strDomain, 4);

		if ($strMapKeys)
		{
			$arMapKeys = unserialize($strMapKeys);
			
			if (array_key_exists($strDomain, $arMapKeys))
				$MAP_KEY = $arMapKeys[$strDomain];
		}
	
		$s = '';
		if(strlen($value["VALUE"])>0)
		{
			$value = parent::ConvertFromDB($arProperty, $value);
			$arCoords = explode(',', $value['VALUE']);
			ob_start();
			$GLOBALS['APPLICATION']->IncludeComponent(
				'bitrix:map.yandex.view',
				'',
				array(
					'KEY' => $MAP_KEY,
					'MAP_DATA' => serialize(array(
						'yandex_lat' => $arCoords[0],
						'yandex_lon' => $arCoords[1],
						'PLACEMARKS' => array(
							array(
								'LON' => $arCoords[1],
								'LAT' => $arCoords[0],
							),
						),
					)),
					'MAP_ID' => 'MAP_YANDEX_VIEW_'.$arProperty['IBLOCK_ID'].'_'.$arProperty['ID'],
					'DEV_MODE' => 'Y',
				),
				false, array('HIDE_ICONS' => 'Y')
			);
			
			
			$s .= ob_get_contents();
			ob_end_clean();
		}
		
		return $s;
	}
}

//AddEventHandler("iblock", "OnIBlockPropertyBuildList", array("CIBlockPropertyFileMan", "GetUserTypeDescription"));
//RegisterModuleDependences('iblock', 'OnIBlockPropertyBuildList', 'fileman', 'CIBlockPropertyMapGoogle', 'GetUserTypeDescription');
//RegisterModuleDependences('iblock', 'OnIBlockPropertyBuildList', 'fileman', 'CIBlockPropertyMapYandex', 'GetUserTypeDescription');
?>