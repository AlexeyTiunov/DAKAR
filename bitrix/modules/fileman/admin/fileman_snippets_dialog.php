<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/fileman/prolog.php");

if (!$USER->CanDoOperation('fileman_edit_existent_files'))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/fileman/include.php");
IncludeModuleLangFile(__FILE__);

define("FROMDIALOGS", true);
?>
<form id="form1" name="form1" onsubmit="return false;">
<script>
function __OnLoad()
{
	try
	{
		OnLoad();
		pObj.floatDiv.focus();

		var arInp = pObj.floatDiv.getElementsByTagName('INPUT');
		for (var i = 0, l = arInp.length; i < l; i++)
			if(arInp[i].type.toUpperCase() == 'TEXT')
				arInp[i].onclick = function(e){this.focus();}

		var arInp = pObj.floatDiv.getElementsByTagName('TEXTAREA');
		for (var i = 0, l = arInp.length; i < l; i++)
			arInp[i].onclick = function(e){this.focus();}
	}
	catch (e){}

	arBXSnippetsTaskbars = [];
	for (var k in ar_BXTaskbarS)
	{
		if (k.substr(0, 'BXSnippetsTaskbar'.length) == 'BXSnippetsTaskbar')
			arBXSnippetsTaskbars.push(ar_BXTaskbarS[k]);
	}
}

var iNoOnSelectionChange = 1;
var iNoOnChange = 2;
</script>

<script type="text/javascript" src="/bitrix/admin/fileman_js.php?lang=<?=LANGUAGE_ID?>"></script>
<input type="hidden" name="logical" value="<?=htmlspecialchars($logical)?>">
<table height="100%" width="100%" border = "0"><tr><td valign="top">

<?
//*********************************************************************************
/*Tab Control*/
class CAdminTabControl_dialog extends CAdminTabControl
{
	function CAdminTabControl_dialog($name, $tabs, $bCanExpand=true)
	{
		$this->tabs = $tabs;
		$this->name = $name;
		$this->unique_name = $name."_".md5($GLOBALS["APPLICATION"]->GetCurPage());
		$this->bCanExpand = $bCanExpand;
		if(isset($_REQUEST[$this->name."_active_tab"]))
			$this->selectedTab = $_REQUEST[$this->name."_active_tab"];
		else
			$this->selectedTab = $tabs[0]["DIV"];
	}

	function Begin()
	{
		?>
<div class="edit-form">
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="edit-form-tbl">
	<tr class="top">
		<td class="left"><div class="empty"></div></td>
		<td><div class="empty"></div></td>
		<td class="right"><div class="empty"></div></td>
	</tr>
	<tr>
		<td class="left"><div class="empty"></div></td>
		<td class="content">
			<table cellspacing="0" class="edit-tabs" width="100%">
				<tr>
					<td class="tab-indent"><div class="empty"></div></td>
					<?$nTabs = count($this->tabs);
		$i = 0;
		foreach($this->tabs as $tab)
		{
			$bSelected = ($tab["DIV"] == $this->selectedTab);
			?>
					<td title="<?echo$tab["TITLE"];?>" id="tab_cont_<?echo$tab["DIV"];?>" class="tab-container<?echo($bSelected? "-selected":"");?>">
						<table cellspacing="0">
							<tr>
								<td class="tab-left<?echo($bSelected? "-selected":"");?>" id="tab_left_<?echo$tab["DIV"];?>"><div class="empty"></div></td>
								<td class="tab<?echo($bSelected? "-selected":"");?>" id="tab_<?echo$tab["DIV"];?>"  style="font-size:90%;"><?echo$tab["TAB"];?></td>
								<td class="tab-right<?echo($i == ($nTabs - 1)? "-last":"").($bSelected? "-selected":"");?>" id="tab_right_<?echo$tab["DIV"];?>"><div class="empty"></div></td>
							</tr>
						</table>
					</td>
			<script>
			var tab_cont__ = document.getElementById('tab_cont_<?echo$tab["DIV"];?>');
			tab_cont__.onclick = function()
			{
				try{<?=$tab["DIV"];?>_onclick();}catch(e){};
				<?echo$this->name;?>.SelectTab('<?=$tab["DIV"];?>');
			}
			tab_cont__.onmouseover = function(){<?echo$this->name;?>.HoverTab('<?echo$tab["DIV"];?>',true);}
			tab_cont__.onmouseout = function(){<?echo$this->name;?>.HoverTab('<?echo$tab["DIV"];?>',false);}
			</script>
			<?
			$i++;
		}
		if($nTabs > 1 && $this->bCanExpand):?>
			<td width="100%" align="right"><a href="javascript:void(0)" onclick="this.blur();'.$this->name.'.ToggleTabs();" hidefocus="true" title="'.GetMessage("admin_lib_expand_tabs").'" id="'.$this->name.'_expand_link" class="context-button down"></a></td>
		<?else:?>
			<td width="100%"><div class="empty"></div></td>
		<?endif;?>
			</tr>
			</table>
			<table cellspacing="0" class="edit-tab">
				<tr>
					<td>
<?
	}
}
?>

<?if($name=="snippets"):?>
<script>
/*  #################    S N I P P E T S  ##################  */
var prevsrc = "";
function OnLoad()
{
	var st = document.getElementById('__snippet_template');
	st.options[1].value = st.options[1].innerHTML = pObj.pMainObj.templateID;

	document.getElementById('saveBut').onclick = function(e){__OnSave();}
	var createTabDiv = function(id,temp_id)
	{
		var oDiv = document.getElementById(id);
		oDiv.style.width = "420px";
		oDiv.style.height = "270px";
		oDiv.style.padding = "5px";
		var tempDiv = document.getElementById(temp_id);
		oDiv.innerHTML = tempDiv.innerHTML;
		tempDiv.parentNode.removeChild(tempDiv);
	};

	// ************************ TAB #1: Base params *************************************
	createTabDiv("__bx_sn_base_params","__bx_temp_sn_base_params");
	// ************************ TAB #2: Location*************************************
	createTabDiv("__bx_sn_location","__bx_temp_sn_location");
	// ************************ TAB #3: Additional params *************************************
	createTabDiv("__bx_sn_additional_params","__bx_temp_sn_additional_params");


	window.arSnGroups = {};
	window.rootDefaultName = {};
	if (pObj.params.mode == 'add')
	{
		oDialogTitle.innerHTML = '<?=GetMessage("FILEMAN_ED_ADD_SNIPPET")?>';
		document.getElementById("__snippet_template").onchange = fillLocation;
		fillLocation();
		var chkbox = document.getElementById("__create_new_subfolder");
		chkbox.onclick = function(e)
		{
			if (this.checked)
				displayRow('_new_group_row',true);
			else
				displayRow('_new_group_row',false);
		}

		document.getElementById("__snippet_template").onchange = fillLocation;
	}
	else if (pObj.params.mode == 'edit')
	{
		oDialogTitle.innerHTML = '<?=GetMessage("FILEMAN_ED_EDIT_SNIPPET")?>';
		var oEl = pObj.params.oEl;
		var title = document.getElementById("__snippet_title");
		title.value = oEl.title;
		var code = document.getElementById("__snippet_code");
		code.value = oEl.code;
		var description = document.getElementById("__snippet_description");
		description.value = oEl.description;

		var _pref = '&nbsp;<span style="color:#525355">';
		var _suf = '</span>';
		var template = document.getElementById("__snippet_template");
		template.parentNode.style.height = '30px';
		template.parentNode.innerHTML = _pref+oEl.template+_suf;

		var name = document.getElementById("__snippet_name");
		name.parentNode.style.height = '30px';
		name.parentNode.innerHTML = _pref+oEl.name+_suf;

		var group_sel = document.getElementById("__snippet_group");
		group_sel.parentNode.style.height = '30px';
		group_sel.parentNode.vAlign = 'middle';
		group_sel.parentNode.previousSibling.vAlign = 'middle';
		var _path = oEl.path.replace(/,/g,'/');
		group_sel.parentNode.innerHTML = _pref+'snippets'+(_path == '' ? '' : '/'+_path)+_suf;

		displayRow('_new_group_chck_row',false);

		// ***** IMAGE *****
		if (oEl.thumb != '')
		{
			displayRow('__bx_snd_exist_image_tr',true);
			var old_img_tr = document.getElementById("__bx_snd_exist_image_tr");
			var img_path = 'snippets/images/'+(_path == '' ? '' : _path+'/')+oEl.thumb;
			old_img_tr.cells[1].innerHTML = _pref+img_path+_suf;
			displayRow('__bx_snd_new_image_chbox_tr',true);
			displayRow('__bx_snd_new_image_tr',false);
			document.getElementById("__bx_snd_new_image_tr").cells[0].innerHTML = '<?=GetMessage("FILEMAN_ED_SN_NEW_IMG")?>:';

			document.getElementById("__new_image_chbox").onclick = function()
			{
				if (this.checked)
					displayRow('__bx_snd_new_image_tr',true);
				else
					displayRow('__bx_snd_new_image_tr',false);
			}
		}
	}
}

function SetUrl(filename,path,site)
{
	var url = path+'/'+filename;
	document.getElementById("thumb_src").value = url;
	if(document.getElementById("thumb_src").onchange)
		document.getElementById("thumb_src").onchange();
}

function fillLocation()
{
	var template = document.getElementById("__snippet_template").value;

	if (window.arSnGroups[template])
		_fillLocation(template);
	else
		Get_arSnGroups(template);
}

function _fillLocation(template)
{
	var _arGroups = window.arSnGroups[template];
	var file_name = document.getElementById("__snippet_name");
	file_name.value = window.rootDefaultName[template];
	var group_sel = document.getElementById("__snippet_group");
	group_sel.options.length = 0;
	group_sel.onchange = function()
	{
		var chbox = document.getElementById("__create_new_subfolder");

		if (this.value == '..')
		{
			file_name.value = window.rootDefaultName[template];
			var _level = -1;
		}
		else
		{
			file_name.value = _arGroups[this.value].default_name;
			var _level = _arGroups[this.value].level;
		}

		if (_level >= 1)
		{
			chbox.checked = false;
			chbox.disabled = 'disabled';
			chbox.onclick();
		}
		else
		{
			chbox.disabled = '';
		}
	}

	var _addOption = function(key,name,level,select)
	{
		var oOpt = document.createElement('OPTION');
		var strPref = '';
		oOpt.value = key;
		for (var _i=-1; _i < level; _i++)
			strPref += '&nbsp;&nbsp;.&nbsp;&nbsp;';

		if (select)
			oOpt.selected = "selected";
		oOpt.innerHTML = strPref+name;
		group_sel.appendChild(oOpt);
		oOpt = null;
	};

	_addOption('..','snippets',-1,true);
	for (var key in _arGroups)
		_addOption(key,_arGroups[key].name,_arGroups[key].level,false);
	return;

	var url = path+'/'+filename;
	document.getElementById("thumb_src").value = url;
	if(document.getElementById("thumb_src").onchange)
		document.getElementById("thumb_src").onchange();
}

function displayRow(rowId,bDisplay)
{
	var row = document.getElementById(rowId);
	if (bDisplay)
	{
		try{row.style.display = 'table-row';}
		catch(e){row.style.display = 'block';}
	}
	else
	{
		row.style.display = 'none';
	}
}

function Get_arSnGroups(template)
{
	var _r = new JCHttpRequest();
	_r.Action = function(result)
	{
		try
		{
			setTimeout(function ()
				{
					_fillLocation(template);
					//_alert(">>\n"+result);
				}, 5
			);
		}
		catch(e)
		{
			_alert('error: loadGroups');
		}
	}
	window.arSnGroups[template] = {};
	window.rootDefaultName[template] = '';
	_r.Send(manage_snippets_path + '&templateID='+template+'&target=getgroups');
}

function __OnSave()
{
	var title = document.getElementById("__snippet_title").value;
	var code = document.getElementById("__snippet_code").value;
	if (title == "")
	{
		alert("<?=GetMessage("FILEMAN_ED_WRONG_PARAM_TITLE")?>");
		return;
	}
	if (code == "")
	{
		alert("<?=GetMessage("FILEMAN_ED_WRONG_PARAM_CODE")?>");
		return;
	}

	if (pObj.params.mode == 'add')
	{
		var name = document.getElementById("__snippet_name").value;
		name = name.replace(/[^a-z0-9\s!\$\(\)\[\]\{\}\-\.;=@\^_\~]/gi, "");

		var template = document.getElementById("__snippet_template").value;
		if (template == "")
			template = ".default";

		var new_group = '';
		if (document.getElementById("__create_new_subfolder").checked)
			new_group = document.getElementById("__new_subfolder_name").value.replace(/\\/ig, '/');

		new_group = new_group.replace(/[^a-z0-9\s!\$\(\)\[\]\{\}\-\.;=@\^_\~]/gi, "");

		checkFileName(___OnSave, name, template, new_group);
	}
	else if (pObj.params.mode == 'edit')
	{
		editSnippet(title, code);
	}
}

function ___OnSave(ok, fileName, templateId, new_group)
{
	if (!ok && !confirm("<?=GetMessage("FILEMAN_ED_FILE_EXISTS")?>"))
		return;

	saveSnippet(fileName, templateId, new_group);
}

function checkFileName(callback, fileName, templateId, new_group)
{
	if (new_group.length > 0)
	{
		var _arGroups = window.arSnGroups[templateId];
		if (new_group.substr(0,1) == '/')
			new_group = new_group.substr(1);

		if (new_group.substr(new_group.length - 1, 1) == '/')
			new_group = new_group.substr(0, new_group.length - 1);

		var ar_d = new_group.split('/');
		if (ar_d.length > 2)
		{
			alert("<?=GetMessage("FILEMAN_ED_WRONG_PARAM_SUBGROUP2")?>");
			return;
		}
		if (_arGroups[ar_d[0]] || _arGroups[new_group])
		{
			alert("<?=GetMessage("FILEMAN_ED_WRONG_PARAM_SUBGROUP")?>");
			return;
		}
	}
	callback(true, fileName, templateId, new_group);
}


function saveSnippet(fileName, templateId, new_group)
{
	var title = document.getElementById("__snippet_title").value;
	var code = document.getElementById("__snippet_code").value;

	var location = document.getElementById("__snippet_group").value;
	if (location == '..')
		location = '';

	var thumb = document.getElementById("thumb_src").value;
	var description = document.getElementById("__snippet_description").value;

	var postData = "title="+escape(title)+"&code="+encodeURIComponent(code)+"&name="+escape(fileName)+"&description="+escape(description)+"&location="+escape(location)+"&new_group="+escape(new_group)+"&thumb="+escape(thumb);


	var _ss = new JCHttpRequest();
	window.__bx_res_sn_filename = null;
	_ss.Action = function(result)
	{
		setTimeout(function()
		{
			if (window.__bx_res_sn_filename)
				fileName = window.__bx_res_sn_filename;

			var _path = location+((location != '' && new_group != '') ? '/' : '')+new_group;
			var createGroup = function(name, path)
			{
				name = bxhtmlspecialchars(name);
				for (var i = 0, l = arBXSnippetsTaskbars.length; i < l; i++)
					arBXSnippetsTaskbars[i].AddElement({name : name, tagname : '', isGroup : true, childElements : [], icon : '', path : path, code : ''}, arBXSnippetsTaskbars[i].pCellSnipp, path);
			}

			reappend_rot_el = false;
			if(location != '')
			{
				var ar_groups = location.split('/');
				var len = ar_groups.length;
				var _loc = '';
				for (var _j = 0; _j<len; _j++)
				{
					_loc += ar_groups[_j];
					if (!pObj.params.BXSnippetsTaskbar.GetGroup(pObj.params.BXSnippetsTaskbar.pCellSnipp,_loc))
					{
						createGroup(ar_groups[_j], (_j>0 ? ar_groups[_j-1] : ''));
						reappend_rot_el = true;
					}
					_loc += ',';
				}
			}

			if (new_group != '')
			{
				var ar_groups = new_group.split('/');
				var len = ar_groups.length;

				if (len>2)
					return;
				else if(len>0)
					reappend_rot_el = true;

				for (var _j = 0; _j<len; _j++)
					createGroup(ar_groups[_j],(_j>0 ? ar_groups[_j-1] : location));
			}

			if (thumb != '')
				thumb = fileName+thumb.substr(thumb.lastIndexOf('.'));

			var c = "sn_"+Math.round(Math.random()*1000000);
			var __arEl =
			{
				name: fileName + '.snp',
				title: title,
				tagname:'snippet',
				description: description,
				template:templateId,
				thumb:thumb,
				isGroup:false,
				icon:'/bitrix/images/fileman/htmledit2/snippet.gif',
				path: _path.replace(/\//ig, ","),
				code:code,
				params:{c:c}
			};

			var key = (__arEl.path == '' ? '' : __arEl.path.replace(/,/ig, '/')+'/')+__arEl.name;
			arSnippets[key] = __arEl;

			var _ar;
			for (var el in GLOBAL_pMainObj)
			{
				_ar = GLOBAL_pMainObj[el].arSnippetsCodes;
				if (_ar) _ar[c] = key;
			}

			for (var i = 0, l = arBXSnippetsTaskbars.length; i < l; i++)
			{
				arBXSnippetsTaskbars[i].AddElement(__arEl, arBXSnippetsTaskbars[i].pCellSnipp, __arEl.path);
				arBXSnippetsTaskbars[i].AddSnippet_button();
			}
			pObj.Close();
		}, 100);
	};

	try{
		_ss.Post(manage_snippets_path + '&templateID='+templateId+'&target=add', postData);
	}catch(e){_alert("ERROR: !!!: saveSnippet");}
}

function editSnippet(title, code)
{
	var oEl = pObj.params.oEl;
	var description = document.getElementById("__snippet_description").value;
	var elNode = pObj.params.elNode;
	var thumb = '';
	var pSessid = document.getElementById("sessid");
	var post_data = '';

	if (document.getElementById("__new_image_chbox").checked);
		thumb = document.getElementById("thumb_src").value;

	if (title != oEl.title)
	{
		oEl.title = title;
		post_data += "title="+escape(oEl.title);
		// Change title in elements list
		var _id = elNode.parentNode.id;
		var titleCell = elNode.parentNode.parentNode.cells[1];
		titleCell.innerHTML = bxhtmlspecialchars(oEl.title);
	}
	if (code != oEl.code)
	{
		oEl.code = code;
		if (post_data != '')
			post_data += '&';
		post_data += "code="+jsUtils.urlencode(oEl.code);
	}
	if (description != oEl.description)
	{
		oEl.description = description;
		if (post_data != '')
			post_data += '&';
		post_data += "description="+escape(oEl.description);
	}

	if (thumb != oEl.thumb)
	{
		if (post_data != '')
			post_data += '&';
		post_data += "thumb="+escape(thumb);

		if (thumb != '' && thumb.lastIndexOf('.') > 0)
			oEl.thumb = oEl.name.substr(0, oEl.name.lastIndexOf('.')) + thumb.substr(thumb.lastIndexOf('.')).toLowerCase();
		else
			oEl.thumb = '';
	}

	if (post_data == '')
		return pObj.Close();

	post_data += "&name="+escape(oEl.name)+"&path="+escape(oEl.path.replace(/,/g,'/'))+"&templateID="+escape(oEl.template);

	var _es = new JCHttpRequest();
	_es.Action = function(result){setTimeout(function(){elNode.onclick();}, 500);}

	try{
		_es.Post(manage_snippets_path + '&target=edit', post_data);
	}catch(e){_alert("ERROR: !!!: editSnippet");pObj.Close();}

	oBXEditorUtils.BXRemoveAllChild(pObj.params.prop_taskbar);
	pObj.Close();
}

function OnSave(){}

</script>

<?
CAdminFileDialog::ShowScript(Array
	(
		"event" => "OpenFileDialog_thumb",
		"arResultDest" => Array("FUNCTION_NAME" => "SetUrl"),
		"arPath" => Array(),
		"select" => 'F',
		"operation" => 'O',
		"showUploadTab" => true,
		"showAddToMenuTab" => false,
		"fileFilter" => 'image',
		"allowAllFiles" => true,
		"SaveConfig" => true
	)
);

$aTabs_dialog = array(
array("DIV" => "__bx_sn_base_params", "TAB"=>GetMessage("FILEMAN_ED_BASE_PARAMS"), "ICON" => "", "TITLE" =>'' ),
array("DIV" => "__bx_sn_location", "TAB"=>GetMessage("FILEMAN_ED_LOCATION"), "ICON" => "", "TITLE"=>''),
array("DIV" => "__bx_sn_additional_params", "TAB"=>GetMessage("FILEMAN_ED_ADD_PARAMS"), "ICON" => "", "TITLE"=>''),
);
$tabControl_dialog = new CAdmintabControl_dialog("tabControl_dialog", $aTabs_dialog, false);

$tabControl_dialog->Begin();
$tabControl_dialog->BeginNextTab();?>
<div id="__bx_sn_base_params"></div>
<?$tabControl_dialog->BeginNextTab();?>
<div id="__bx_sn_location"></div>
<?$tabControl_dialog->BeginNextTab();?>
<div id="__bx_sn_additional_params"></div>
<?$tabControl_dialog->End();?>


<div id="__bx_temp_sn_base_params" style="display:none">
<table class="add_snippet" border="0" cellpadding="2" style="height:100%; width:99%">
	<tr>
		<td>
			<?=GetMessage("FILEMAN_ED_TITLE")?>:<span class="required">*</span><br />
			<input id="__snippet_title" type="text">
		</td>
	</tr>
	<tr height="100%">
		<td>
			<?=GetMessage("FILEMAN_ED_CODE")?>:<span class="required">*</span><br />
			<textarea id="__snippet_code" rows="10" style="width: 410px;"></textarea>
		</td>
	</tr>
</table>
</div>

<div id="__bx_temp_sn_location" style="display:none">
<table class="add_snippet" border="0" cellpadding="2" style="height:100%; width:99%">
	<tr>
		<td width="40%" align="right"><?=GetMessage("FILEMAN_ED_TEMPLATE")?>:</td>
		<td width="60%">
			<select id="__snippet_template">
				<option value=".default">.default</option>
				<option value="111">222</option>
			</select>
		</td>
	</tr>
	<tr>
		<td align="right"><?=GetMessage("FILEMAN_ED_NAME")?>:</td>
		<td><input id="__snippet_name" style="width:135px" type="text">.snp</td>
	</tr>
	<tr>
		<td align="right" valign="middle"><?=GetMessage("FILEMAN_ED_FILE_LOCATION")?>:</td>
		<td valign="top">
			<select id="__snippet_group" size="6" style="width: 160px;"></select>
		</td>
	</tr>
	<tr id='_new_group_chck_row'>
		<td align="right"><label for="__create_new_subfolder"><?=GetMessage("FILEMAN_ED_CREATE_SUBGROUP")?>:</label></td>
		<td align="left"><input style="width:18px" id="__create_new_subfolder" type="checkbox"></td>
	</tr>
	<tr id='_new_group_row' style="display:none;">
		<td align="right"><?=GetMessage("FILEMAN_ED_SUBGROUP_NAME")?>:</td>
		<td><input style="width:160px" id="__new_subfolder_name" type="text"></td>
	</tr>
	<tr style="height:100%;"><td colspan="2"></td></tr>
</table>
</div>

<div id="__bx_temp_sn_additional_params" style="display:none">
<table class="add_snippet" border="0" cellpadding="2" style="height:100%; width:99%">
	<tr style="height:0%; display:none" id="__bx_snd_exist_image_tr">
		<td width="30%"align="right"><?=GetMessage("FILEMAN_ED_SN_IMAGE")?>:</td>
		<td width="70%"></td>
	</tr>
	<tr style="height:0%; display:none" id="__bx_snd_new_image_chbox_tr">
		<td width="30%"align="right"><label for='__new_image_chbox'><?=GetMessage("FILEMAN_ED_SN_DEL_IMG")?>:</label></td>
		<td width="70%"><input style="width:18px" id="__new_image_chbox" type="checkbox"></input></td>
	</tr>
	<tr style="height:20%"  id="__bx_snd_new_image_tr">
		<td width="30%"align="right"><?=GetMessage("FILEMAN_ED_SN_IMAGE")?>:</td>
		<td width="70%">
		<input type="text" size="25" value="" id="thumb_src" style="width: 85%"><input id="OpenFileDialog_button" type="button" value="..." onclick="OpenFileDialog_thumb()" style="width: 10%">
		</td>
	</tr>
	<tr style="height:80%">
		<td align="right" style="vertical-align: top !important;"><?=GetMessage("FILEMAN_ED_DESCRIPTION")?>:</td>
		<td style="vertical-align: top !important;"><textarea id="__snippet_description" rows="10"></textarea></td>
	</tr>
</table>
</div>
<?endif;?>

</td></tr>
<?if($not_use_default!='Y'):?>
	<tr id="buttonsSec">
	<td align="center" valign="top">
	<div class="buttonCont">
		<input id="saveBut" type="button" value="<?echo GetMessage("FILEMAN_ED_SAVE")?>">
		<input id="cancelBut" type="button" value="<?echo GetMessage("FILEMAN_ED_CANC")?>" onclick="pObj.Close();">
		<?if($name=="settings"):?>
			<input id="restoreDefault" type="button" value="<?echo GetMessage('FILEMAN_ED_RESTORE');?>" title="<?echo GetMessage('FILEMAN_ED_RESTORE');?>">
		<?endif;?>
	</div>
	</td>
	</tr>
<?endif?>
</table>
<script>
<?if($not_use_default!='Y'):?>
	document.getElementById("buttonsSec").style.height = (jsUtils.IsIE()) ? 25 : 45;
<?endif?>
__OnLoad();
</script>
</form>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");?>
