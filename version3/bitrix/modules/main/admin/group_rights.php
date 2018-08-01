<?
$MODULE_RIGHT = $APPLICATION->GetGroupRight($module_id);

$md = CModule::CreateModuleObject($module_id);

$arFilter = Array("ACTIVE"=>"Y");
if($md->SHOW_SUPER_ADMIN_GROUP_RIGHTS != "Y")
	$arFilter["ADMIN"] = "N";

$arGROUPS = array();
$z = CGroup::GetList($v1="sort", $v2="asc", $arFilter);
while($zr = $z->Fetch())
{
	$ar = array();
	$ar["ID"] = intval($zr["ID"]);
	$ar["NAME"] = htmlspecialchars($zr["NAME"])." [<a title=\"".GetMessage("MAIN_USER_GROUP_TITLE")."\" href=\"/bitrix/admin/group_edit.php?ID=".intval($zr["ID"])."&amp;lang=".LANGUAGE_ID."\">".intval($zr["ID"])."</a>]";
	$arGROUPS[] = $ar;
}

if ($MODULE_RIGHT!="D") :

if($REQUEST_METHOD=="POST" && strlen($Update)>0 && $MODULE_RIGHT=="W" && check_bitrix_sessid())
{
	// set per module rights
	COption::SetOptionString($module_id, "GROUP_DEFAULT_RIGHT", $GROUP_DEFAULT_RIGHT, "Right for groups by default");
	reset($arGROUPS);
	while(list(,$value)=each($arGROUPS))
	{
		$rt = ${"RIGHTS_".$value["ID"]};
		if (strlen($rt)>0 && $rt!="NOT_REF")
		{
			$APPLICATION->SetGroupRight($module_id, $value["ID"], $rt);
		}
		else
		{
			$APPLICATION->DelGroupRight($module_id, array($value["ID"]));
		}
	}
}
$GROUP_DEFAULT_RIGHT = COption::GetOptionString($module_id, "GROUP_DEFAULT_RIGHT", "D");
?>
	<tr>
		<td nowrap valign="top" width="50%"><b><?=GetMessage("MAIN_BY_DEFAULT");?></b><br><br></td>
		<td valign="top" width="50%"><?
		if (method_exists($md, "GetModuleRightList"))
			$ar = call_user_func(array($md, "GetModuleRightList"));
		else
			$ar = $APPLICATION->GetDefaultRightList();
		echo SelectBoxFromArray("GROUP_DEFAULT_RIGHT", $ar, htmlspecialchars($GROUP_DEFAULT_RIGHT));
		?><?=bitrix_sessid_post()?></td>
	</tr>
	<?
	reset($arGROUPS);
	while(list(,$value)=each($arGROUPS)) :
		//if ($value["ID"]==1 && $md->SHOW_SUPER_ADMIN_GROUP_RIGHTS!="Y") continue;
	?>
	<tr>
		<td nowrap valign="top"><?=$value["NAME"].":"?><?
		if ($value["ID"]==1 && $md->SHOW_SUPER_ADMIN_GROUP_RIGHTS=="Y"):
			echo "<br><small>".GetMessage("MAIN_SUPER_ADMIN_RIGHTS_COMMENT")."</small>";
		endif;
		?></td>
		<td valign="top"><?
		$v = $APPLICATION->GetGroupRight($module_id, array($value["ID"]), "N", "N");
		echo SelectBoxFromArray("RIGHTS_".$value["ID"], $ar, htmlspecialchars($v), GetMessage("MAIN_DEFAULT"));
		?></td>
	</tr>
	<? endwhile; ?>
<?endif;?>
