<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/fileman/prolog.php");
if (!$USER->CanDoOperation('fileman_edit_existent_files') || !check_bitrix_sessid())
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/fileman/include.php");

if(CModule::IncludeModule("compression"))
	CCompress::Disable2048Spaces();

if (isset($_GET['target']) && $_GET['target']=='toolbars' &&  isset($_POST['tlbrset']) && isset($_GET['edname']))
{
	$edname = $_GET['edname'];
	if (isset($_GET['rs_tlbrs']))
		CUserOptions::SetOption("fileman", "rs_toolbar_".$edname, ($_GET['rs_tlbrs']=="N" ? "N" : "Y"));
	
	$tlbrset = $_POST['tlbrset'];
	$resultString = "";
	$_string = '';
	foreach($tlbrset as $tlbrname => $tlbr)
	{
		$resultString .= $tlbrname.":";
		$resultString .= $tlbr['show'].",";
		$resultString .= $tlbr['docked'].",";
		$resultString .= "[";
		foreach($tlbr['position'] as $tlbrpos)
			$resultString .= (substr($tlbrpos,-2)=="px" ? substr($tlbrpos,0,-2) : $tlbrpos).";";
		$resultString .= "]";
		$resultString .= "||";
	}
	$resultString = substr($resultString,0,-2);

	CUserOptions::SetOption("fileman", "toolbar_settings_".$edname, _addslashes($resultString));
}

if (isset($_GET['target'], $_GET['tooltips'], $_GET['edname']) && $_GET['target']=='tooltips' )
	CUserOptions::SetOption("fileman", "show_tooltips".$_GET['edname'], ($_GET['tooltips'] == "N" ? "N" : "Y"));

if (isset($_GET['target'], $_GET['visual_effects'], $_GET['edname']) && $_GET['target']=='visual_effects')
	CUserOptions::SetOption("fileman", "visual_effects".$_GET['edname'], ($_GET['visual_effects'] == "N" ? "N" : "Y"));

if (isset($_GET['target'], $_GET['render_components']) && $_GET['target'] == 'render_components')
	CUserOptions::SetOption("fileman", "render_components", $_GET['render_components'] == "Y");

if (isset($_GET['target']) && $_GET['target']=='taskbars' &&  isset($_POST['tskbrset']) && isset($_GET['edname']))
{
	$edname = $_GET['edname'];
	if (isset($_GET['rs_tskbrs']))
		CUserOptions::SetOption("fileman", "rs_taskbar_".$edname, ($_GET['rs_tskbrs']=="N" ? "N" : "Y"));

	$tskbrset = $_POST['tskbrset'];
	$resultString = "";
	$_string = '';
	foreach($tskbrset as $tskbrname => $tskbr)
	{
		$resultString .= $tskbrname.":";
		$resultString .= $tskbr['show'].",";
		$resultString .= $tskbr['docked'].",";
		$resultString .= "[";
		foreach($tskbr['position'] as $tskbrpos)
			$resultString .= (substr($tskbrpos,-2)=="px" ? substr($tskbrpos,0,-2) : $tskbrpos).";";
		$resultString .= "]";
		if ($tskbr['auto']!=undefined)
			$resultString .= ','.$tskbr['auto'];
		$resultString .= "||";
	}
	$resultString = substr($resultString,0,-2);
	CUserOptions::SetOption("fileman", "taskbar_settings_".$edname, _addslashes($resultString));
}


if (isset($_GET['target']) && $_GET['target']=='taskbarsets' &&  isset($_POST['tskbrsetset']) && isset($_GET['edname']))
{
	$edname = $_GET['edname'];
	if (isset($_GET['rs_tskbrs']))
		CUserOptions::SetOption("fileman", "rs_taskbar_".$edname, ($_GET['rs_tskbrs']=="N" ? "N" : "Y"));

	$tskbrsetset = $_POST['tskbrsetset'];
	$resultString = "";
	$_string = '';
	foreach($tskbrsetset as $tskbrsetNum => $tskbrset)
	{
		$resultString .= $tskbrsetNum.":";
		$resultString .= $tskbrset['show'].",";
		$resultString .= $tskbrset['width'].",";
		$resultString .= $tskbrset['height'];
		$resultString .= "||";
	}
	$resultString = substr($resultString,0,-2);

	CUserOptions::SetOption("fileman", "taskbarset_settings_".$edname, _addslashes($resultString));
}


if (isset($_GET['target'], $_GET['edname']) && $_GET['target']=='get_all')
{
	$edname = $_GET['edname'];
	//Get toolbar settings
	$toolbar_settings = stripslashes(CUserOptions::GetOption("fileman", "toolbar_settings_".$edname));
	$rs_tlbrs = stripslashes(CUserOptions::GetOption("fileman", "rs_toolbar_".$edname));
		
	if ($toolbar_settings)
		getToolbarSettings($toolbar_settings,$rs_tlbrs);
	
	//Get taskbar settings
	$taskbar_settings = stripslashes(CUserOptions::GetOption("fileman", "taskbar_settings_".$edname));
	$rs_tskbrs = stripslashes(CUserOptions::GetOption("fileman", "rs_taskbar_".$edname));
		
	if ($taskbar_settings)
		getTaskbarSettings($taskbar_settings, $rs_tskbrs);
	
	//Get taskbarset settings
	$taskbarset = stripslashes(CUserOptions::GetOption("fileman", "taskbarset_settings_".$edname));
		
	if ($taskbarset)
		getTaskbarsetSettings($taskbarset);
		
	$show_tooltips = stripslashes(CUserOptions::GetOption("fileman", "show_tooltips".$edname, "Y"));
	$visualEffects = stripslashes(CUserOptions::GetOption("fileman", "visual_effects".$edname, "Y"));
	
	displayJSAddSett($show_tooltips, $visualEffects);
}

if (isset($_GET['target'], $_GET['edname']) && $_GET['target'] == 'unset')
{
	$edname = $_GET['edname'];
	CUserOptions::DeleteOption("fileman", "toolbar_settings_".$edname);
	CUserOptions::DeleteOption("fileman", "rs_toolbar_".$edname);
	CUserOptions::DeleteOption("fileman", "taskbar_settings_".$edname);
	CUserOptions::DeleteOption("fileman", "rs_taskbar_".$edname);
	CUserOptions::DeleteOption("fileman", "taskbarset_settings_".$edname);
	CUserOptions::DeleteOption("fileman", "show_tooltips".$edname);
	CUserOptions::DeleteOption("fileman", "visual_effects".$edname);
}


function displayJSAddSett($tooltips, $visualEffects)
{?>
<script>
window.__show_tooltips = <?echo ($tooltips == "N" ? "false" : "true");?>;
window.__visual_effects = <?echo ($visualEffects == "N" ? "false" : "true");?>;
</script>
<?}

function displayJSToolbar($tlbrname,$show,$docked,$arPos)
{
	?>
	<script>
	var _ar = [];
	_ar.show = <?echo($show == 'true' ? 'true' : 'false');?>;
	_ar.docked = <?echo($docked=='true' ? 'true' : 'false');?>;
	<?if ($docked=='true'):?>
		_ar.position = [<?echo$arPos[0];?>,<?echo$arPos[1];?>,<?echo$arPos[2];?>];		
	<?else:?>
		_ar.position = {
			x : '<?echo(substr($arPos[0],-2)=="px" ? substr($arPos[0],0,-2) : $arPos[0]);?>',
			y : '<?echo(substr($arPos[1],-2)=="px" ? substr($arPos[1],0,-2) : $arPos[1]);?>'
		};
	<?endif;?>
	window.arToolbarSettings["<?echo$tlbrname;?>"] = _ar;
	</script>
	<?
}

function displayJSTaskbar($tskbrname,$show,$docked,$arPos,$auto)
{
	?>
	<script>
	var _ar = [];
	_ar.show = <?echo(($show=='true' && $auto!='true') ? 'true' : 'false');?>;
	_ar.docked = <?echo($docked=='true' ? 'true' : 'false');?>;
	<?if ($docked=='true'):?>
		_ar.position = [<?=$arPos[0];?>,<?=$arPos[1];?>,<?=$arPos[2];?>];		
	<?else:?>
		_ar.position = {
			x : '<?echo substr($arPos[0],-2)=="px" ? substr($arPos[0],0,-2) : $arPos[0];?>',
			y : '<?echo substr($arPos[1],-2)=="px" ? substr($arPos[1],0,-2) : $arPos[1];?>'
		};
	<?endif;?>
	<?if ($auto!=''):?>
		_ar.auto = <?echo($auto=='true' ? 'true' : 'false');?>;
	<?endif;?>
	window.arTaskbarSettings["<?=$tskbrname;?>"] = _ar;
	</script>
	<?
}


function displayJSTaskbarset($num,$show,$width,$height)
{
	?>
	<script>
	var _ar = {
		show : <?echo($show=='true' ? 'true' : 'false');?>,
		width : '<?echo $width;?>',
		height : '<?echo $height;?>'
	};
	window.arTBSetsSettings["<?echo $num;?>"] = _ar;
	</script>
	<?
}

function _addslashes($str)
{
	$pos = strpos(strtolower($str), "</script");
	if ($pos!==FALSE)
		$str = str_replace("</script","&lt;/script",$str);

	$pos2 = strpos(strtolower($str), "\n");
	if ($pos2!==FALSE)
	{
		$str = str_replace("\r","",$str);
		$str = str_replace("\n","\\n",$str);
	}
	return CUtil::addslashes($str);
}


function getToolbarSettings($settings,$rs_tlbrs)
{
	?>
	<script>
	window.arToolbarSettings = [];
	window.RS_toolbars = <?echo($rs_tlbrs=="N" ? "false" : "true");?>;
	</script>
	<?
	$res = explode("||", $settings);
	$len = count($res);

	for ($i=0; $i<$len; $i++)
	{
		$tmp = explode(":", $res[$i]);
		$tlbrname = $tmp[0];
		$tmp2 = explode(",", $tmp[1]);
		$show = $tmp2[0];
		$docked = $tmp2[1];
		$position = explode(";",substr($tmp2[2],1,-1));
		displayJSToolbar($tlbrname,$show,$docked,$position);
	}
}

function getTaskbarSettings($settings,$rs_tskbrs)
{
	?>
	<script>
	window.arTaskbarSettings = [];
	window.RS_taskbars = <?echo($rs_tskbrs=="N" ? "false" : "true");?>;
	</script>
	<?
	$res = explode("||", $settings);
	$len = count($res);

	for ($i=0; $i<$len; $i++)
	{
		$tmp = explode(":", $res[$i]);
		$tskbrname = $tmp[0];
		$tmp2 = explode(",", $tmp[1]);
		$show = $tmp2[0];
		$docked = $tmp2[1];
		$position = explode(";",substr($tmp2[2],1,-1));
		$auto = ($tmp2[3]) ? $tmp2[3] : '';
		displayJSTaskbar($tskbrname,$show,$docked,$position,$auto);
	}
}

function getTaskbarsetSettings($settings)
{
	?>
	<script>
	window.arTBSetsSettings = [];
	</script>
	<?
	$res = explode("||", $settings);
	$len = count($res);

	for ($i=0; $i<$len; $i++)
	{
		$tmp = explode(":", $res[$i]);
		$tskbrsetNum = $tmp[0];
		$tmp2 = explode(",", $tmp[1]);
		$show = $tmp2[0];
		$width = $tmp2[1];
		$height = $tmp2[2];
		displayJSTaskbarset($tskbrsetNum,$show,$width,$height);
	}
}
?>