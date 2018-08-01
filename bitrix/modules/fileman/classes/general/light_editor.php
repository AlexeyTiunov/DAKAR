<?
IncludeModuleLangFile(__FILE__);
class CLightHTMLEditor // LHE
{
	var $ownerType;

	function Init(&$arParams)
	{
		global $USER;
		$basePath = '/bitrix/js/fileman/light_editor/';
		$this->Id = isset($arParams['id']) ? $arParams['id'] : 'bxlhe'.substr(uniqid(mt_rand(), true), 0, 4);
		$this->cssPath = $this->GetActualPath($basePath.'light_editor.css');
		$this->arJSPath = array(
			$this->GetActualPath($basePath.'le_dialogs.js'),
			$this->GetActualPath($basePath.'le_controls.js'),
			$this->GetActualPath($basePath.'le_toolbarbuttons.js'),
			$this->GetActualPath($basePath.'le_core.js')
		);

		$arJS = Array();
		$arCSS = Array();
		$events = GetModuleEvents("fileman", "OnBeforeLightEditorScriptsGet");
		while($arEvent = $events->Fetch())
		{
			$tmp = ExecuteModuleEventEx($arEvent, $this->Id, $arParams);
			if (!is_array($tmp))
				continue;

			if (is_array($tmp['JS']))
			{
				for($i = 0, $c = count($tmp['JS']); $i < $c; $i++)
				{
					if(file_exists($_SERVER['DOCUMENT_ROOT'].$tmp['JS'][$i]))
						$this->arJSPath[] = $this->GetActualPath($tmp['JS'][$i]);
				}
			}
		}

		$this->bAutorized = is_object($USER) && $USER->IsAuthorized();
		$this->bUseFileDialogs = $arParams['bUseFileDialogs'] !== false && $this->bAutorized;
		$this->bUseMedialib = $arParams['bUseMedialib'] !== false && COption::GetOptionString('fileman', "use_medialib", "Y") == "Y" && CMedialib::CanDoOperation('medialib_view_collection', 0);

		$this->bBBCode = $arParams['BBCode'] === true;
		$this->bInitByJS = $arParams['bInitByJS'] === true;
		$this->bSaveOnBlur = $arParams['bSaveOnBlur'] !== false;
		$this->bFloatingToolbar = $arParams['bFloatingToolbar'] === true;
		$this->bArisingToolbar = $this->bFloatingToolbar && $arParams['bArisingToolbar'];
		$this->content = $arParams['content'];
		$this->inputName = isset($arParams['inputName']) ? $arParams['inputName'] : 'lha_content';
		$this->inputId = isset($arParams['inputId']) ? $arParams['inputId'] : 'lha_content_id';
		$this->videoSettings = is_array($arParams['videoSettings']) ? $arParams['videoSettings'] : array(
				'maxWidth' => 640,
				'maxHeight' => 480,
				'WMode' => 'transparent',
				'windowless' => true,
				'bufferLength' => 20,
				'skin' => '/bitrix/components/bitrix/player/mediaplayer/skins/bitrix.swf',
				'logo' => ''
			);

		//$this->allowedTags = isset($arParams['allowedTags']) ? $arParams['allowedTags'] : array('A', 'ABBR', 'ACRONYM', 'ADDRESS', 'AREA', 'B', 'STRONG', 'BASE', 'BLOCKQUOTE', 'BR', 'CAPTION', 'CITE', 'CODE', 'DEL', 'DIV', 'EM', 'FONT', 'H1', 'H2', 'H3', 'H4', 'H5', 'H6', 'HR', 'I', 'IMG', 'OL', 'LI', 'UL', 'MAP', 'META', 'P', 'PRE', 'Q', 'S', 'STRIKE', 'SPAN', 'SUB', 'SUP', 'TABLE', 'TBODY', 'THEAD', 'TFOOT', 'TR', 'TH', 'TD', 'U');

		if (!is_array($arParams['arFonts']) || count($arParams['arFonts']) <= 0)
			$arParams['arFonts'] = array('Times New Roman', 'Courier', 'Arial', 'Tahoma', 'Verdana', 'Georgia');

		if (!is_array($arParams['arFontSizes']) || count($arParams['arFontSizes']) <= 0)
			$arParams['arFontSizes'] = array('1' => 'xx-small', '2' => 'x-small', '3' => 'small', '4' => 'medium', '5' => 'large', '6' => 'x-large', '7' => 'xx-large');

		// Tables
		//$this->arJSPath[] = $this->GetActualPath($basePath.'le_table.js');

		$this->jsObjName = (isset($arParams['jsObjName']) && strlen($arParams['jsObjName']) > 0) ? $arParams['jsObjName'] : 'LightHTMLEditor'.$this->Id;

		$this->JSConfig = array(
			'id' => $this->Id,
			'content' => $this->content,
			'bBBCode' => $this->bBBCode,
			'bUseFileDialogs' => $this->bUseFileDialogs,
			'bUseMedialib' => $this->bUseMedialib,
			'arSmiles' => $arParams['arSmiles'],
			'arFonts' => $arParams['arFonts'],
			'arFontSizes' => $arParams['arFontSizes'],
			'bFloatingToolbar' => $this->bFloatingToolbar,
			'bArisingToolbar' => $this->bArisingToolbar,
			'inputName' => $this->inputName,
			'inputId' => $this->inputId,
			'videoSettings' => $this->videoSettings,
			'bSaveOnBlur' => $this->bSaveOnBlur
		);

		if (isset($arParams['width']) && intVal($arParams['width']) > 0)
			$this->JSConfig['width'] = $arParams['width'];
		if (isset($arParams['height']) && intVal($arParams['height']) > 0)
			$this->JSConfig['height'] = $arParams['height'];
		if (isset($arParams['toolbarConfig']))
			$this->JSConfig['toolbarConfig'] = $arParams['toolbarConfig'];
	}

	function GetActualPath($path)
	{
		return $path.'?v='.@filemtime($_SERVER['DOCUMENT_ROOT'].$path);
	}

	function Show($arParams)
	{
		$this->Init($arParams);
		$this->BuildSceleton();
		$this->InitLangMess();
		$this->InitScripts();
		if ($this->bUseFileDialogs)
			$this->InitFileDialogs();

		if ($this->bUseMedialib)
			$this->InitMedialibDialogs();
	}

	function BuildSceleton()
	{
		if ($this->bFloatingToolbar):?>
		<div class="bxlhe-frame" id="bxlhe_frame_<?=$this->Id?>"></div>
		<div class="bxlhe-float-toolbar" id="bxlhe_float_toolbar_<?=$this->Id?>"><table class="bxlhe-float-tlb-table">
			<tr><td class="bxlhe-toggle">&nbsp;</td><td></td></tr>
		</table></div>
		<?else:?>
		<div class="bxlhe-frame" id="bxlhe_frame_<?=$this->Id?>"><table class="bxlhe-frame-table">
				<tr><td><div class="lhe-stat-toolbar-cont"></div></td></tr>
				<tr><td style="overflow: hidden;"></td></tr>
		</table></div>
		<?endif;
	}

	function InitScripts()
	{
		?>
		<script>
		function winOnLoad_<?=$this->Id?>()
		{
			//return;
			if (!window.phpVars) // Hack for anonyms
				window.phpVars = {ADMIN_THEME_ID:'.default'};

			if (!window.jsUtils) // Load JS Utils
			{
				var oSript = document.body.appendChild(document.createElement('script'));
				oSript.src = '/bitrix/js/main/utils.js';
				var bLoaded = false;
				oSript.onload = oSript.onreadystatechange = function()
				{
					if (!bLoaded && (!oSript.readyState || oSript.readyState == "loaded" || oSript.readyState == "complete") )
					{
						bLoaded = true;
						window._jsUtilsInterval<?=$this->Id?> = setInterval(LoadLHE_<?=$this->Id?>, 1000); // 10
						oSript.onload = oSript.onreadystatechange = null;
					}
				};
			}
			else
			{
				LoadLHE_<?=$this->Id?>();
			}
		}

		<?if(!$this->bInitByJS):?>
		if(window.attachEvent) // IE
			window.attachEvent("onload", winOnLoad_<?=$this->Id?>);
		else if(window.addEventListener) // Gecko / W3C
			window.addEventListener("load", winOnLoad_<?=$this->Id?>, false);
		<?endif;?>

		function LoadLHE_<?=$this->Id?>() // Load JS and CSS via jsUtils
		{
			if (!window.jsUtils)
				return;

			jsUtils.bIsIE = jsUtils.IsIE();
			jsUtils.IsIE = function(){return jsUtils.bIsIE;};
			if (window._jsUtilsInterval<?=$this->Id?>)
			{
				clearInterval(window._jsUtilsInterval<?=$this->Id?>);
				window._jsUtilsInterval<?=$this->Id?> = null;
			}

			if (!window.BXLHEStyles) // Load css
				window.BXLHEStyles = jsUtils.loadCSSFile(['<?=$this->cssPath?>']);

			var arScripts = [<?for ($i = 0, $l = count($this->arJSPath); $i < $l; $i++){echo '\''.$this->arJSPath[$i].'\''.($i == $l - 1 ? '' : ',');}?>];

			if (!window.PopupMenu)
				arScripts.push('<?=$this->GetActualPath('/bitrix/js/main/popup_menu.js')?>');
			if (!window.JCLightHTMLEditor)
				return jsUtils.loadJSFile(arScripts, bxInitLHE_<?=$this->Id?>);
			else
				bxInitLHE_<?=$this->Id?>();
		}

		function bxInitLHE_<?=$this->Id?>() // Create new Light Editor object
		{
			if (window._jsUtilsInterval<?=$this->Id?>) // probing interval clearing
			{
				clearInterval(window._jsUtilsInterval<?=$this->Id?>);
				window._jsUtilsInterval<?=$this->Id?> = null;
			}

			if (!window.JCLightHTMLEditor)
				return setTimeout(bxInitLHE_<?=$this->Id?>, 1000); //9
			window.<?=$this->jsObjName?> = new window.JCLightHTMLEditor(<?=CUtil::PhpToJSObject($this->JSConfig)?>);
		}
		</script><?
	}

	function InitLangMess()
	{
		$langPath = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/fileman/lang/'.LANGUAGE_ID.'/classes/general/light_editor_js.php';
		if(file_exists($langPath))
			include($langPath);
		else
			$langPath = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/fileman/lang/en/classes/general/light_editor_js.php';

		?><script>LHE_MESS = window.LHE_MESS = <?=CUtil::PhpToJSObject($MESS)?>;</script><?
	}

	function InitFileDialogs()
	{
		// Link
		CAdminFileDialog::ShowScript(Array(
			"event" => "LHED_Link_FDOpen",
			"arResultDest" => Array("ELEMENT_ID" => "lhed_link_href"),
			"arPath" => Array("SITE" => SITE_ID),
			"select" => 'F',
			"operation" => 'O',
			"showUploadTab" => true,
			"showAddToMenuTab" => false,
			"fileFilter" => 'php, html',
			"allowAllFiles" => true,
			"SaveConfig" => true
		));

		// Image
		CAdminFileDialog::ShowScript(Array
		(
			"event" => "LHED_Img_FDOpen",
			"arResultDest" => Array("FUNCTION_NAME" => "LHED_Img_SetUrl"),
			"arPath" => Array("SITE" => SITE_ID),
			"select" => 'F',
			"operation" => 'O',
			"showUploadTab" => true,
			"showAddToMenuTab" => false,
			"fileFilter" => 'image',
			"allowAllFiles" => true,
			"SaveConfig" => true
		));

		// video path
		CAdminFileDialog::ShowScript(Array
		(
			"event" => "LHED_VideoPath_FDOpen",
			"arResultDest" => Array("FUNCTION_NAME" => "LHED_Video_SetPath"),
			"arPath" => Array("SITE" => SITE_ID),
			"select" => 'F',
			"operation" => 'O',
			"showUploadTab" => true,
			"showAddToMenuTab" => false,
			"fileFilter" => 'wmv,wma,flv,vp6,mp3,mp4,aac,jpg,jpeg,gif,png',
			"allowAllFiles" => true,
			"SaveConfig" => true
		));

		// video preview
		CAdminFileDialog::ShowScript(Array
		(
			"event" => "LHED_VideoPreview_FDOpen",
			"arResultDest" => Array("ELEMENT_ID" => "lhed_video_prev_path"),
			"arPath" => Array("SITE" => SITE_ID),
			"select" => 'F',
			"operation" => 'O',
			"showUploadTab" => true,
			"showAddToMenuTab" => false,
			"fileFilter" => 'image',
			"allowAllFiles" => true,
			"SaveConfig" => true
		));
	}

	function InitMedialibDialogs()
	{
		CMedialib::ShowDialogScript(array(
			"event" => "LHED_Img_MLOpen",
			"arResultDest" => Array("FUNCTION_NAME" => "LHED_Img_SetUrl")
		));
	}
}
?>