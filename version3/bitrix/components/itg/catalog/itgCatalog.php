<?php

class itgCatalog
{
	private $sqlResBrand;
	private $type;
	private $arType;
	private $numType;
	private $multiRes = array();
	private $namesTypeOfProduct = array("ЗАП.ЧАСТИ","МАСЛО","РАДИАТОРЫ");
	private $idTypeOfProduct = array("ЗАП.ЧАСТИ"=>0,"МАСЛО"=>1,"РАДИАТОРЫ"=>2);
	//const 
	
	function __construct($type = false)
	{
		global $DB;
		$this->type = $type;
		
		if($type != false) $wheres[$this->idTypeOfProduct[$type]] = " WHERE `type_item`='{$this->idTypeOfProduct[$type]}'";
		else 
		{
			$sqlType = "SELECT `type_item` FROM `b_autodoc_items_m` GROUP BY `type_item`";
			$typesRes = $DB->Query($sqlType);
			$this->numType = $typesRes->SelectedRowsCount();
			while ($res = $typesRes->Fetch())
			{
				if ($res['type_item'] == null) continue;
				$wheres[$res['type_item']] = " WHERE `type_item`={$res['type_item']}";
			}
		}
		foreach ($wheres as $key=>$where)
		{
			$sql = "SELECT `BrandCode`,COUNT(id) as countOfItems FROM `b_autodoc_items_m`".$where." GROUP BY `BrandCode`";
			$this->multiRes[$this->namesTypeOfProduct[$key]] = $DB->Query($sql);
		}
	}
	public function getTypeBrand()
	{
		return $this->multiRes;
	}
	public static function getListProduct($brand = false, $type = false)
	{
		global $DB;
		$namesTypeOfProduct = array("ЗАП.ЧАСТИ"=>0,"МАСЛО"=>1,"РАДИАТОРЫ"=>2);
		$wheres[] = " WHERE";
		if ($brand != false) $wheres[] = " `BrandCode`='{$brand}'";
		if ($type != false) $wheres[] = " `type_item`='{$namesTypeOfProduct[$type]}'";
		$numArr = count($wheres);
		switch ($numArr)
		{
			case 3:
				$where = $wheres[0].$wheres[1]." AND".$wheres[2];
				break;
			case 2:
				$where = $wheres[0].$wheres[1];
				break;
			default:
				$where = '';
		}
		$sql = "SELECT `id` FROM `b_autodoc_items_m`".$where;
		return $DB->Query($sql);
	}
}
?>