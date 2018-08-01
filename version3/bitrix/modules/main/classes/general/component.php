<?
class CBitrixComponent
{
	var $__name = "";
	var $__relativePath = "";
	var $__path = "";

	var $__templateName = "";
	var $__templatePage = "";
	var $__template = null;

	var $__component_epilog = false;

	var $arParams = array();
	var $arResult = array();
	var $arResultCacheKeys = false;

	var $__parent = null;

	var $__bInited = False;

	var $__arIncludeAreaIcons = array();

	var $__NavNum = false;

	var $__cache = null;
	var $__cacheID = "";
	var $__cachePath = "";

	var $__children_css = array();
	var $__children_epilogs = array();

	/***********  GET  ***************/
	function GetName()
	{
		if (!$this->__bInited)
			return null;

		return $this->__name;
	}

	function GetRelativePath()
	{
		if (!$this->__bInited)
			return null;

		return $this->__relativePath;
	}

	function GetPath()
	{
		if (!$this->__bInited)
			return null;

		return $this->__path;
	}

	function GetTemplateName()
	{
		if (!$this->__bInited)
			return null;

		return $this->__templateName;
	}

	function GetTemplatePage()
	{
		if (!$this->__bInited)
			return null;

		return $this->__templatePage;
	}

	function &GetTemplate()
	{
		$null = null;

		if (!$this->__bInited)
			return $null;

		if (!$this->__template)
			return $null;

		return $this->__template;
	}

	function &GetParent()
	{
		$null = null;

		if (!$this->__bInited)
			return $null;

		if (!$this->__parent)
			return $null;

		return $this->__parent;
	}

	function GetTemplateCachedData()
	{
		if (!$this->__bInited)
			return null;

		if (!$this->__template)
			return null;

		$templateCachedData = & $this->__template->GetCachedData();

		return $templateCachedData;
	}

	function SetTemplateCachedData($templateCachedData)
	{
		if (!$this->__bInited)
			return null;

		CBitrixComponentTemplate::ApplyCachedData($templateCachedData);
	}

	/***********  INIT  ***************/
	function InitComponent($componentName, $componentTemplate = False)
	{
		$this->__bInited = False;

		$componentName = Trim($componentName);
		if (StrLen($componentName) <= 0)
		{
			$this->__ShowError("Empty component name");
			return False;
		}

		$path2Comp = CComponentEngine::MakeComponentPath($componentName);
		if (StrLen($path2Comp) <= 0)
		{
			$this->__ShowError(str_replace("#NAME#", $componentName, "'#NAME#' is not a valid component name"));
			return False;
		}

		$componentPath = "/bitrix/components".$path2Comp;

		if (/*	!file_exists($_SERVER["DOCUMENT_ROOT"].$componentPath)
			|| !is_dir($_SERVER["DOCUMENT_ROOT"].$componentPath)
			||*/ !file_exists($_SERVER["DOCUMENT_ROOT"].$componentPath."/component.php")
			|| !is_file($_SERVER["DOCUMENT_ROOT"].$componentPath."/component.php"))
		{
			$this->__ShowError(str_replace("#NAME#", $componentName, "'#NAME#' is not a component"));
			return False;
		}

		$this->__name = $componentName;

		$this->__relativePath = $path2Comp;
		$this->__path = $componentPath;

		$this->arResult = array();
		$this->arParams = array();
		$this->__parent = null;
		$this->__arIncludeAreaIcons = array();
		$this->__cache = null;

		if ($componentTemplate !== False)
			$this->__templateName = $componentTemplate;

		$this->__bInited = True;

		//CPageOption::SetOptionString("main", "nav_page_in_session", "N");

		return True;
	}

	/***********  INCLUDE  ***************/
	function __IncludeComponent()
	{
		global $APPLICATION, $USER, $DB;

		if (!$this->__bInited)
			return null;

		$arParams = &$this->arParams;
		$arResult = &$this->arResult;

		$componentPath = $this->__path;
		$componentName = $this->__name;
		$componentTemplate = $this->__templateName;

		$parentComponentName = "";
		$parentComponentPath = "";
		$parentComponentTemplate = "";
		if ($this->__parent)
		{
			$parentComponentName = $this->__parent->__name;
			$parentComponentPath = $this->__parent->__path;
			$parentComponentTemplate = $this->__parent->__templateName;
		}

		return include($_SERVER["DOCUMENT_ROOT"].$this->__path."/component.php");
	}

	function __PrepareComponentParams(&$arParams)
	{
		$p=$arParams;//this avoids endless loop
		foreach($p as $k=>$v)
		{
			$arParams["~".$k] = $v;
			if (is_array($v))
				$arParams[$k] = htmlspecialcharsEx($v);
			elseif (is_object($v));
			elseif (preg_match("/[;&<>\"]/", $v))
				$arParams[$k] = htmlspecialcharsEx($v);
		}

		if ($arParams["CACHE_TYPE"] != "Y" && $arParams["CACHE_TYPE"] != "N")
			$arParams["CACHE_TYPE"] = "A";
	}

	function IncludeComponent($componentTemplate, $arParams, $parentComponent)
	{
		if (!$this->__bInited)
			return null;

		if ($componentTemplate !== False)
			$this->__templateName = $componentTemplate;

		$this->__PrepareComponentParams($arParams);
		$this->arParams = $arParams;

		if (is_object($parentComponent) && (StrToLower(get_class($parentComponent)) == "cbitrixcomponent"))
			$this->__parent = $parentComponent;

		$this->IncludeComponentLang();

		return $this->__IncludeComponent();
	}

	function IncludeComponentLang($relativePath = "", $lang = False)
	{
		if (!$this->__bInited)
			return null;

		if (StrLen($relativePath) <= 0)
			$relativePath = "component.php";

		if ($lang === False)
			$lang = LANGUAGE_ID;

		if ($lang != "en" && $lang != "ru")
			if (file_exists($_SERVER["DOCUMENT_ROOT"].$this->__path."/lang/en/".$relativePath))
				__IncludeLang($_SERVER["DOCUMENT_ROOT"].$this->__path."/lang/en/".$relativePath);

		if (file_exists($_SERVER["DOCUMENT_ROOT"].$this->__path."/lang/".$lang."/".$relativePath))
			__IncludeLang($_SERVER["DOCUMENT_ROOT"].$this->__path."/lang/".$lang."/".$relativePath);
	}

	function InitComponentTemplate($templatePage = "", $siteTemplate = false, $customTemplatePath = "")
	{
		if (!$this->__bInited)
			return null;

		$this->__templatePage = $templatePage;

		$this->__template = new CBitrixComponentTemplate();
		if ($this->__template->Init($this, $siteTemplate, $customTemplatePath))
			return True;
		else
			return False;
	}

	function ShowComponentTemplate()
	{
		if (!$this->__bInited)
			return null;

		if ($this->__template)
			$this->__template->IncludeTemplate($this->arResult);

		if(is_array($this->arResultCacheKeys))
		{
			$arNewResult = array();
			foreach($this->arResultCacheKeys as $key)
				if(array_key_exists($key, $this->arResult))
					$arNewResult[$key] = $this->arResult[$key];
			$this->arResult = $arNewResult;
		}

		$this->EndResultCache();
	}

	function IncludeComponentTemplate($templatePage = "", $customTemplatePath = "")
	{
		if (!$this->__bInited)
			return null;

		if ($this->InitComponentTemplate($templatePage, false, $customTemplatePath))
		{
			$this->ShowComponentTemplate();
			if($this->__component_epilog)
				$this->IncludeComponentEpilog($this->__component_epilog);
		}
		else
		{
			$this->AbortResultCache();
			$this->__ShowError(str_replace("#PAGE#", $templatePage, str_replace("#NAME#", $this->__templateName, "Can not find '#NAME#' template with page '#PAGE#'")));
		}
	}

	/***********  ICONS  ***************/
	function AddIncludeAreaIcon($arIcon)
	{
		if (!isset($this->__arIncludeAreaIcons) || !is_array($this->__arIncludeAreaIcons))
			$this->__arIncludeAreaIcons = array();

		$this->__arIncludeAreaIcons[] = $arIcon;
	}

	function AddIncludeAreaIcons($arIcons)
	{
		$this->__arIncludeAreaIcons = $arIcons;
	}

	function &GetIncludeAreaIcons()
	{
		return $this->__arIncludeAreaIcons;
	}

	function GetCacheID($additionalCacheID = False)
	{
		$cacheID = SITE_ID."|".$this->__name."|".$this->__templateName."|";

		foreach($this->arParams as $k=>$v)
			if(strncmp("~", $k, 1))
				$cacheID .= ",".$k."=".serialize($v);

		if ($additionalCacheID !== False)
			$cacheID .= "|".serialize($additionalCacheID);

		return $cacheID;
	}

	/***********  CACHE  ***************/
	function StartResultCache($cacheTime = False, $additionalCacheID = False, $cachePath = False)
	{
		global $APPLICATION, $CACHE_MANAGER;

		if (!$this->__bInited)
			return null;

		if ($this->arParams["CACHE_TYPE"] == "N" || ($this->arParams["CACHE_TYPE"] == "A" && COption::GetOptionString("main", "component_cache_on", "Y") == "N"))
			return True;

		if ($cacheTime === False)
			$cacheTime = IntVal($this->arParams["CACHE_TIME"]);

		$this->__cacheID = $this->GetCacheID($additionalCacheID);

		$this->__cachePath = $cachePath;
		if ($this->__cachePath === False)
			$this->__cachePath = $CACHE_MANAGER->GetCompCachePath($this->__relativePath);

		$this->__cache = new CPHPCache;
		if ($this->__cache->StartDataCache($cacheTime, $this->__cacheID, $this->__cachePath))
		{
			$this->__NavNum = $GLOBALS["NavNum"];

			if(defined("BX_COMP_MANAGED_CACHE"))
				$CACHE_MANAGER->StartTagCache($this->__cachePath);

			return True;
		}
		else
		{
			$arCache = $this->__cache->GetVars();
			$this->arResult = $arCache["arResult"];
			if (array_key_exists("templateCachedData", $arCache))
			{
				$templateCachedData = &$arCache["templateCachedData"];
				CBitrixComponentTemplate::ApplyCachedData($templateCachedData);

				if(array_key_exists("__NavNum", $templateCachedData))
					$GLOBALS["NavNum"] += $templateCachedData["__NavNum"];

				if(array_key_exists("__children_css", $templateCachedData))
				{
					foreach($templateCachedData["__children_css"] as $css_url)
					{
						$fname = $_SERVER["DOCUMENT_ROOT"].$css_url;
						if (file_exists($fname))
							$APPLICATION->SetAdditionalCSS($css_url.'?'.filemtime($fname));
					}
				}

				if(array_key_exists("__children_epilogs", $templateCachedData))
				{
					foreach($templateCachedData["__children_epilogs"] as $component_epilog)
						$this->IncludeComponentEpilog($component_epilog);
				}

				if(array_key_exists("component_epilog", $templateCachedData))
				{
					$this->IncludeComponentEpilog($templateCachedData["component_epilog"]);
				}
			}

			return False;
		}
	}

	function EndResultCache()
	{
		if (!$this->__bInited)
			return null;

		if (!$this->__cache)
			return null;

		$arCache = array(
			"arResult" => $this->arResult
		);

		if ($this->__template)
		{
			$arCache["templateCachedData"] = & $this->__template->GetCachedData();
			if($this->__component_epilog)
				$arCache["templateCachedData"]["component_epilog"] = $this->__component_epilog;
		}
		else
		{
			$arCache["templateCachedData"] = array();
		}

		global $NavNum;
		if(($this->__NavNum !== false) && ($this->__NavNum !== $NavNum))
		{
			$arCache["templateCachedData"]["__NavNum"] = $NavNum - $this->__NavNum;
		}

		if(count($this->__children_css))
		{
			$arCache["templateCachedData"]["__children_css"] = $this->__children_css;
			if($this->__parent)
				$this->__parent->__children_css += $this->__children_css;
		}

		if(count($this->__children_epilogs))
		{
			$arCache["templateCachedData"]["__children_epilogs"] = $this->__children_epilogs;
			if($this->__parent)
				$this->__parent->__children_epilogs += $this->__children_epilogs;
		}

		$this->__cache->EndDataCache($arCache);

		if(defined("BX_COMP_MANAGED_CACHE"))
			$GLOBALS["CACHE_MANAGER"]->EndTagCache();

		$this->__cache = null;
	}

	function AbortResultCache()
	{
		if (!$this->__bInited)
			return null;

		if (!$this->__cache)
			return null;

		$this->__cache->AbortDataCache();

		if(defined("BX_COMP_MANAGED_CACHE"))
			$GLOBALS["CACHE_MANAGER"]->AbortTagCache();

		$this->__cache = null;
	}

	function ClearResultCache($additionalCacheID = False, $cachePath = False)
	{
		global $APPLICATION;

		if (!$this->__bInited)
			return null;

		$this->__cacheID = SITE_ID."|".$this->__name."|".$this->__templateName."|";
		/*
		if (array_key_exists("REAL_FILE_PATH", $_SERVER) && StrLen($_SERVER["REAL_FILE_PATH"]) > 0)
			$this->__cacheID .= $_SERVER["REAL_FILE_PATH"];
		else
			$this->__cacheID .= $APPLICATION->GetCurPage();
		*/
		foreach($this->arParams as $k=>$v)
			if(strncmp("~", $k, 1))
				$this->__cacheID .= ",".$k."=".serialize($v);

		if ($additionalCacheID !== False)
			$this->__cacheID .= "|".serialize($additionalCacheID);

		$this->__cachePath = $cachePath;
		if ($this->__cachePath === False)
			$this->__cachePath = "/".SITE_ID.$this->__relativePath;

		CPHPCache::Clean($this->__cacheID, $this->__cachePath);
	}

	function SetResultCacheKeys($arResultCacheKeys)
	{
		if($this->arResultCacheKeys === false)
			$this->arResultCacheKeys = $arResultCacheKeys;
		else
			$this->arResultCacheKeys = array_merge($this->arResultCacheKeys, $arResultCacheKeys);
	}

	function SetTemplateEpilog($arEpilogInfo)
	{
		$this->__component_epilog = $arEpilogInfo;
		//Check if parent component exists and plug epilog it to it's "collection"
		if($this->__parent)
			$this->__parent->__children_epilogs[] = $this->__component_epilog;
	}

	function IncludeComponentEpilog($arEpilogInfo)
	{
		global $APPLICATION, $USER, $DB;
		extract($arEpilogInfo);

		if(file_exists($_SERVER["DOCUMENT_ROOT"].$epilogFile))
		{
			$arParams = $this->arParams;
			$arResult = $this->arResult;
			$componentPath = $this->GetPath();
			$component = &$this;
			include($_SERVER["DOCUMENT_ROOT"].$epilogFile);
		}
	}

	/***********  UTIL  ***************/
	function __ShowError($errorMessage, $errorCode = "")
	{
		if (StrLen($errorMessage) > 0)
			echo "<font color=\"#FF0000\">".$errorMessage.((StrLen($errorCode) > 0) ? " [".$errorCode."]" : "")."</font>";
	}
}
?>