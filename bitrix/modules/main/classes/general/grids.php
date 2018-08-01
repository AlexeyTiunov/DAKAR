<?
class CGridOptions
{
	protected $grid_id;
	protected $options;
	protected $filter;
	
	function __construct($grid_id)
	{
		$this->grid_id = $grid_id;
		$this->options = array();
		$this->filter = array();

		$aOptions = CUserOptions::GetOption("main.interface.grid", $this->grid_id, array());
		if(is_array($aOptions["views"]) && array_key_exists($aOptions["current_view"], $aOptions["views"]))
			$this->options = $aOptions["views"][$aOptions["current_view"]];
		if($this->options["saved_filter"] <> '' && is_array($aOptions["filters"]) && array_key_exists($this->options["saved_filter"], $aOptions["filters"]))
			if(is_array($aOptions["filters"][$this->options["saved_filter"]]["fields"]))
				$this->filter = $aOptions["filters"][$this->options["saved_filter"]]["fields"];
	}
	
	function GetSorting($arParams=array())
	{
		if(!is_array($arParams["vars"]))
			$arParams["vars"] = array("by" => "by", "order" => "order");
		if(!is_array($arParams["sort"]))
			$arParams["sort"] = array();
			
		$arResult = array(
			"sort" => $arParams["sort"],
			"vars" => $arParams["vars"],
		);

		$uniq = md5($this->grid_id.":".$GLOBALS["APPLICATION"]->GetCurPage());

		$key = '';
		if(isset($_REQUEST[$arParams["vars"]["by"]]))
		{
			$_SESSION["SESS_SORT_BY"][$uniq] = $_REQUEST[$arParams["vars"]["by"]];
		}
		elseif(!isset($_SESSION["SESS_SORT_BY"][$uniq]))
		{
			if($this->options["sort_by"] <> '')
				$key = $this->options["sort_by"];
		}
		if(isset($_SESSION["SESS_SORT_BY"][$uniq]))
			$key = $_SESSION["SESS_SORT_BY"][$uniq];
			
		if($key <> '')
		{
			if(isset($_REQUEST[$arParams["vars"]["order"]]))
			{
				$_SESSION["SESS_SORT_ORDER"][$uniq] = $_REQUEST[$arParams["vars"]["order"]];
			}
			elseif(!isset($_SESSION["SESS_SORT_ORDER"][$uniq]))
			{
				if($this->options["sort_order"] <> '')
					$arResult["sort"] = array($key => $this->options["sort_order"]);
			}
			if(isset($_SESSION["SESS_SORT_ORDER"][$uniq]))
				$arResult["sort"] = array($key => $_SESSION["SESS_SORT_ORDER"][$uniq]);
		}

		return $arResult;
	}
	
	function GetNavParams($arParams=array())
	{
		$arResult = array(
			"nPageSize" => (isset($arParams["nPageSize"])? $arParams["nPageSize"] : 20),
		);
		
		if($this->options["page_size"] <> '')
			$arResult["nPageSize"] = $this->options["page_size"];

		return $arResult;
	}
	
	function GetVisibleColumns()
	{
		if($this->options["columns"] <> '')
			return explode(",", $this->options["columns"]);
		return array();
	}
	
	function GetFilter($arFilter)
	{
		$uniq = md5($this->grid_id.":".$GLOBALS["APPLICATION"]->GetCurPage());
		$aRes = array();
		foreach($arFilter as $field)
		{
			if($_REQUEST[$field["id"]."_list"] <> '' && $_REQUEST[$field["id"]] <> '')
				$aRes[$field["id"]."_list"] = $_REQUEST[$field["id"]."_list"];
			if(isset($_REQUEST[$field["id"]."_from"]))
			{
				if($_REQUEST[$field["id"]."_from"] <> '')
					$aRes[$field["id"]."_from"] = $_REQUEST[$field["id"]."_from"];
				else
					unset($_SESSION["GRID_FILTER"][$uniq][$field["id"]."_from"]);
			}
			if(isset($_REQUEST[$field["id"]."_to"]))
			{
				if($_REQUEST[$field["id"]."_to"] <> '')
					$aRes[$field["id"]."_to"] = $_REQUEST[$field["id"]."_to"];
				else
					unset($_SESSION["GRID_FILTER"][$uniq][$field["id"]."_to"]);
			}
			if($field["filtered"] == true)
			{
				$aRes[$field["id"]] = true;
			}
			elseif(isset($_REQUEST[$field["id"]]))
			{ 
				if(is_array($_REQUEST[$field["id"]]) && !empty($_REQUEST[$field["id"]]) && $_REQUEST[$field["id"]][0] <> '' || !is_array($_REQUEST[$field["id"]]) && $_REQUEST[$field["id"]] <> '')
					$aRes[$field["id"]] = $_REQUEST[$field["id"]];
				else
					unset($_SESSION["GRID_FILTER"][$uniq][$field["id"]]);
			}
		}
		if(!empty($aRes))
			$_SESSION["GRID_FILTER"][$uniq] = $aRes;
		elseif($_REQUEST["clear_filter"] <> '')
			$_SESSION["GRID_FILTER"][$uniq] = array();
		elseif(is_array($_SESSION["GRID_FILTER"][$uniq]))
			return $_SESSION["GRID_FILTER"][$uniq];
		elseif(!empty($this->filter))
		{
			foreach($arFilter as $field)
			{
				if($this->filter[$field["id"]."_list"] <> '' && $this->filter[$field["id"]] <> '')
					$aRes[$field["id"]."_list"] = $this->filter[$field["id"]."_list"];
				if($this->filter[$field["id"]."_from"] <> '')
					$aRes[$field["id"]."_from"] = $this->filter[$field["id"]."_from"];
				if($this->filter[$field["id"]."_to"] <> '')
					$aRes[$field["id"]."_to"] = $this->filter[$field["id"]."_to"];
				if(is_array($this->filter[$field["id"]]) && !empty($this->filter[$field["id"]]) && $this->filter[$field["id"]][0] <> '' || !is_array($this->filter[$field["id"]]) && $this->filter[$field["id"]] <> '')
					$aRes[$field["id"]] = $this->filter[$field["id"]];
			}
			if(!empty($aRes))
				$_SESSION["GRID_FILTER"][$uniq] = $aRes;
		}

		return $aRes;
	}
}
?>