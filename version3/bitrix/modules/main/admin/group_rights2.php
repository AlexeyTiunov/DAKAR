<?
if (!$USER->IsAdmin())
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/".$module_id."/admin/task_description.php");

$md = CModule::CreateModuleObject($module_id);

$arGROUPS = array();
$arFilter = Array("ACTIVE"=>"Y");
if($md->SHOW_SUPER_ADMIN_GROUP_RIGHTS != "Y")
	$arFilter["ADMIN"] = "N";

$z = CGroup::GetList($v1="sort", $v2="asc", $arFilter);
while($zr = $z->Fetch())
{
	$ar = array();
	$ar["ID"] = intval($zr["ID"]);
	$ar["NAME"] = htmlspecialchars($zr["NAME"])." [<a title=\"".GetMessage("MAIN_USER_GROUP_TITLE")."\" href=\"/bitrix/admin/group_edit.php?ID=".intval($zr["ID"])."&amp;lang=".LANGUAGE_ID."\">".intval($zr["ID"])."</a>]";
	$arGROUPS[] = $ar;
}

if($REQUEST_METHOD=="POST" && strlen($Update)>0 && $USER->IsAdmin() && check_bitrix_sessid())
{
	// установка прав групп
	COption::SetOptionString($module_id, "GROUP_DEFAULT_TASK", $GROUP_DEFAULT_TASK, "Task for groups by default");
	$letter = ($l = CTask::GetLetter($GROUP_DEFAULT_TASK)) ? $l : 'D';
	COption::SetOptionString($module_id, "GROUP_DEFAULT_RIGHT", $letter, "Right for groups by default");

	reset($arGROUPS);
	$arTasksInModule = Array();
	while (list(, $value) = each($arGROUPS))
	{
		$tid = ${"TASKS_".$value["ID"]};
		if ($tid)
			$arTasksInModule[$value["ID"]] = Array('ID'=>$tid);

		$rt = ($tid) ? CTask::GetLetter($tid) : '';
		if (strlen($rt) > 0 && $rt != "NOT_REF")
			$APPLICATION->SetGroupRight($module_id, $value["ID"], $rt);
		else
			$APPLICATION->DelGroupRight($module_id, array($value["ID"]));
	}
	CGroup::SetTasksForModule($module_id,$arTasksInModule);
}

$GROUP_DEFAULT_TASK = COption::GetOptionString($module_id, "GROUP_DEFAULT_TASK", "");
if ($GROUP_DEFAULT_TASK == '')
{
	$GROUP_DEFAULT_RIGHT = COption::GetOptionString($module_id, "GROUP_DEFAULT_RIGHT", "D");
	$GROUP_DEFAULT_TASK = CTask::GetIdByLetter($GROUP_DEFAULT_RIGHT, $module_id, 'module');
	if ($GROUP_DEFAULT_TASK)
		COption::SetOptionString($module_id, "GROUP_DEFAULT_TASK", $GROUP_DEFAULT_TASK);
}
?>
<tr>
	<td nowrap valign="top" width="50%"><b><?=GetMessage("MAIN_BY_DEFAULT");?></b><br><br></td>
	<td valign="top" width="50%"><?
	$arTasksInModule = CTask::GetTasksInModules(true,$module_id,'module');
	$arTasks = $arTasksInModule[$module_id];
	echo SelectBoxFromArray("GROUP_DEFAULT_TASK", $arTasks, htmlspecialchars($GROUP_DEFAULT_TASK));
	?><?=bitrix_sessid_post()?></td>
</tr>
<?
$arTaskInModule = CGroup::GetTasksForModule($module_id);
reset($arGROUPS);
while(list(,$value)=each($arGROUPS)) :
	// if ($value["ID"]==1 && $md->SHOW_SUPER_ADMIN_GROUP_RIGHTS!="Y") continue;
?>
<tr>
	<td nowrap valign="top"><?=$value["NAME"].":"?><?
	if ($value["ID"]==1 && $md->SHOW_SUPER_ADMIN_GROUP_RIGHTS=="Y"):
		echo "<br><small>".GetMessage("MAIN_SUPER_ADMIN_RIGHTS_COMMENT")."</small>";
	endif;
	?></td>
	<td valign="top"><?
	$v = isset($arTaskInModule[$value["ID"]]['ID']) ? $arTaskInModule[$value["ID"]]['ID'] : false;
	echo SelectBoxFromArray("TASKS_".$value["ID"], $arTasks, $v, GetMessage("MAIN_DEFAULT"));
	?></td>
</tr>
<? endwhile; ?>
