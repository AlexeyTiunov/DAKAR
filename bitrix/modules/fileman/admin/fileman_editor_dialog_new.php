<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/fileman/prolog.php");

//if (!$USER->CanDoOperation('fileman_view_file_structure'))
if (!check_bitrix_sessid())
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/fileman/include.php");
IncludeModuleLangFile(__FILE__);

define("FROMDIALOGS", true);
?>
<form id="form_editor_dialog" name="form_editor_dialog" onsubmit="return false;">
<script>
function __OnLoad()
{
	try
	{
		OnLoad();
		var bFocus = false;
		var arInp = pObj.floatDiv.getElementsByTagName('INPUT');
		for (var i = 0, l = arInp.length; i < l; i++)
		{
			if(arInp[i].type.toUpperCase() == 'TEXT')
			{
				arInp[i].focus();
				bFocus = true;
				break;
			}
		}

		if (!bFocus)
		{
			var arInp = pObj.floatDiv.getElementsByTagName('TEXTAREA');
			for (var i = 0, l = arInp.length; i < l; i++)
			{
				arInp[i].focus();
				bFocus = true;
				break;
			}
		}
	}
	catch (e){}
	jsFloatDiv.AdjustShadow(document.getElementById('BX_editor_dialog'));
}

var iNoOnSelectionChange = 1;
var iNoOnChange = 2;

function __OnSave()
{
	var r = 0;
	if(OnSave)
		r = OnSave();

	if((r & 'NoOnSelectionChange') != 0)
		pObj.pMainObj.OnEvent("OnSelectionChange", ["always"]);

	pObj.Close();
}
</script>

<script type="text/javascript" src="/bitrix/admin/fileman_js.php?lang=<?=LANGUAGE_ID?>"></script>
<input type="hidden" name="logical" value="<?=htmlspecialchars($logical)?>">
<table height="100%" width="100%" border = "0" style="height: 100%;"><tr><td style="vertical-align: top !important;">
<?if($name=="settings" || $name=="flash" || $name=="media" || $name=="edit_hbf"):
//*********************************************************************************
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
<div class="edit-form" style="margin: 0px;">
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="edit-form">
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
				<?echo$this->name;?>.SelectTab('<?echo$tab["DIV"];?>');
			}
			tab_cont__.onmouseover = function()
			{
				<?echo$this->name;?>.HoverTab('<?echo$tab["DIV"];?>',true);
			}
			tab_cont__.onmouseout = function()
			{
				<?echo$this->name;?>.HoverTab('<?echo$tab["DIV"];?>',false);
			}
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
//*********************************************************************************
endif;
?>

<?if($name == "anchor"):?>

<script>
var pElement = null;
function OnLoad()
{
	pElement = pObj.pMainObj.GetSelectionObject();
	oDialogTitle.innerHTML = '<?=GetMessage("FILEMAN_ED_LINK_TITLE")?>';
	var el = document.getElementById("anchor_value");
	if(pElement && pElement.getAttribute("__bxtagname")=="anchor")
	{
		var val = BXUnSerialize(pElement.getAttribute("__bxcontainer"));
		el.value = pObj.pMainObj.pParser.GetSetAnchorName(val.html);
	}
	else
	{
		el.value = "";
	}

	el.focus();
}

function OnSave()
{
	BXSelectRange(oPrevRange, pObj.pMainObj.pEditorDocument, pObj.pMainObj.pEditorWindow);
	pElement = pObj.pMainObj.GetSelectionObject();
	pObj.pMainObj.bSkipChanges = true;
	var anchor_value = document.getElementById("anchor_value");
	if(pElement && pElement.getAttribute && pElement.getAttribute("__bxtagname")=="anchor")
	{
		if(anchor_value.value.length <= 0)
			pObj.pMainObj.executeCommand('Delete');
		else
		{
			var
				val = BXUnSerialize(pElement.getAttribute("__bxcontainer")),
				html = pObj.pMainObj.pParser.GetSetAnchorName(val.html, anchor_value.value);
			pElement.setAttribute("__bxcontainer", BXSerialize({"html": html}));
		}
	}
	else if(anchor_value.value.length > 0)
	{
		var
			tmp_id = Math.random().toString().substring(2),
			html = '<a name="' + anchor_value.value + '"></a>';

		pObj.pMainObj.insertHTML('<img id="'+tmp_id+'" src="/bitrix/images/1.gif" style="background-image: url(/bitrix/images/fileman/htmledit2/_global_iconkit.gif); background-position: -260px 0; height: 20px; width: 20px"  __bxtagname="anchor" __bxcontainer="' + bxhtmlspecialchars(BXSerialize({html : html}))+'" />');
		var pComponent = pObj.pMainObj.pEditorDocument.getElementById(tmp_id);
		pComponent.removeAttribute('id');
		if(pObj.pMainObj.pEditorWindow.getSelection)
			pObj.pMainObj.pEditorWindow.getSelection().selectAllChildren(pComponent);
	}
	pObj.pMainObj.bSkipChanges = false;
	pObj.pMainObj.OnChange("anchor");
}
</script>
<div style="padding: 5px;">
<?echo GetMessage("FILEMAN_ED_ANCHOR_NAME")?>
<input type="text" size="30" value="" id="anchor_value" style="width: 180px;" />
</div>

<?elseif($name == "link"):?>

<script>
var pElement = null;
function OnLoad()
{
	_Ch();
	pElement = BXFindParentByTagName(pObj.pMainObj.GetSelectionObject(), 'A');

	var
		arStFilter = ['A', 'DEFAULT'], i, j,
		elStyles = document.getElementById("classname"),
		arStyles;

	elStyles.options.add(new Option("", "", false, false));
	for(i=0; i<arStFilter.length; i++)
	{
		arStyles = pObj.pMainObj.oStyles.GetStyles(arStFilter[i]);
		for(j = 0; j < arStyles.length; j++)
		{
			if(arStyles[j].className.length<=0)
				continue;
			oOption = new Option(arStyles[j].className, arStyles[j].className, false, false);
			elStyles.options.add(oOption);
		}
	}

	var
		i, arAnchs = [], anc, ancName,
		arImgs = pObj.pMainObj.pEditorDocument.getElementsByTagName('IMG');

	for(i = 0; i < arImgs.length; i++)
	{
		if(arImgs[i].getAttribute("__bxtagname") && arImgs[i].getAttribute("__bxtagname") == "anchor")
		{
			anc = BXUnSerialize(arImgs[i].getAttribute("__bxcontainer"));
			ancName = pObj.pMainObj.pParser.GetSetAnchorName(anc.html);
			if (ancName.length > 0)
				arAnchs.push(ancName);
		}
	}
	
	el = document.getElementById('url3');
	for(i = 0; i < arAnchs.length; i++)
		el.options.add(new Option(arAnchs[i], '#'+arAnchs[i], false, false));

	var tip = 1;
	if(pElement)
		oDialogTitle.innerHTML = '<?=GetMessage("FILEMAN_ED_LE_TITLE")?>';
	else
		oDialogTitle.innerHTML = '<?=GetMessage("FILEMAN_ED_LN_TITLE")?>';

	if(pElement)
	{
		if(pElement.tagName.toLowerCase() == 'a')
		{
			oPrevRange = pObj.pMainObj.SelectElement(pElement);
			var href = pElement.getAttribute("href", 2), el, tip;
			if(href.substring(0, 7).toLowerCase() == 'mailto:')
			{
				tip = 4;
				document.getElementById("url4").value = href.substring(7);
			}
			else if(href.substr(0, 1) == '#')
			{
				tip = 3;
				el = document.getElementById("url3");
				var bF = false;
				for(i=0; i<el.options.length; i++)
				{
					if(el.options[i].value == href)
					{
						el.selectedIndex = i;
						bF = true;
						break;
					}
				}

				 if(!bF)
				 {
				 	tip = 1;
					document.getElementById("url1").value = href;
				 }
			}
			else if(href.substr(0, 20) == '/bitrix/redirect.php')
			{
				tip = 2;
				document.getElementById("fixstat").checked = true;
				var sParams = href.substring(20);
				__ExtrParam = function (p, s)
				{
					var pos = s.indexOf(p+'=');
					if(pos<0)
						return '';
					var pos2 = s.indexOf('&', pos+p.length+1);
					if(pos2<0)
						s = s.substring(pos+p.length+1);
					else
						s = s.substr(pos+p.length+1, pos2 - pos - 1 - p.length);
					return unescape(s);
				};

				document.getElementById("event1").value = __ExtrParam('event1', sParams);
				document.getElementById("event2").value = __ExtrParam('event2', sParams);
				document.getElementById("event3").value = __ExtrParam('event3', sParams);
				var url2 = __ExtrParam('goto', sParams);
				if(url2.substr(0, 7)=='http://')
				{
					document.getElementById("url2").value = url2.substring(7);
					document.getElementById("url_type").selectedIndex = 0;
				}
				else if(url2.substr(0, 6)=='ftp://')
				{
					document.getElementById("url2").value = url2.substring(6);
					document.getElementById("url_type").selectedIndex = 1;
				}
				else if(url2.substr(0, 8)=='https://')
				{
					document.getElementById("url2").value = url2.substring(8);
					document.getElementById("url_type").selectedIndex = 2;
				}
				else
				{
					document.getElementById("url2").value = url2;
					document.getElementById("url_type").selectedIndex = 3;
				}
			}
			else if(href.substring(0, 7) == 'http://')
			{
				tip = 2;
				document.getElementById("url2").value = href.substring(7);
				document.getElementById("url_type").selectedIndex = 0;
			}
			else if(href.substring(0, 6) == 'ftp://')
			{
				tip = 2;
				document.getElementById("url2").value = href.substring(6);
				document.getElementById("url_type").selectedIndex = 1;
			}
			else if(href.substring(0, 8) == 'https://')
			{
				tip = 2;
				document.getElementById("url2").value = href.substring(8);
				document.getElementById("url_type").selectedIndex = 2;
			}
			else
			{
				document.getElementById("url1").value = href;
			}
			if(pElement.className)
			{
				el = document.getElementById("classname");
				for(i=0; i<el.length; i++)
				{
					if(el[i].value==pElement.className)
					{
						el.selectedIndex = i;
						break;
					}
				}
			}
			if(pElement.target)
			{
				el = document.getElementById("bx_target");
				var el2 = document.getElementById("bx_targ_list");
				switch(pElement.target.toLowerCase())
				{
				case '_blank':
					el2.selectedIndex = 1;
					break;
				case '_parent':
					el2.selectedIndex = 2;
					break;
				case '_self':
					el2.selectedIndex = 3;
					break;
				case '_top':
					el2.selectedIndex = 4;
					break;
				}
				_ChTargL();
				el.value = pElement.target;
			}

			document.getElementById("__bx_id").value = pElement.id;
			document.getElementById("BXEditorDialog_title").value = pElement.title;

			el = document.getElementById('bx_link_type');
			el.selectedIndex = tip-1;
		}
	}
	_Ch();

	if(el = document.getElementById('url'+tip))
		el.focus();
}

function OnSave()
{
	var href='', target='';

	switch(document.getElementById('bx_link_type').selectedIndex)
	{
	case 0:
		href = document.getElementById('url1').value;
		break;
	case 1:
		href = document.getElementById('url2').value;
		if(document.getElementById("url_type").value.length>0)
			href = document.getElementById("url_type").value + href;
		if(document.getElementById("fixstat").checked)
			href = '/bitrix/redirect.php?event1='+escape(document.getElementById("event1").value)+'&event2='+escape(document.getElementById("event2").value)+'&event3='+escape(document.getElementById("event3").value)+'&goto='+escape(href);
		break;
	case 2:
		href = document.getElementById('url3').value;
		break;
	case 3:
		if(document.getElementById('url4').value.length>0)
			href = 'mailto:'+document.getElementById('url4').value;
		break;
	}
	BXSelectRange(oPrevRange, pObj.pMainObj.pEditorDocument, pObj.pMainObj.pEditorWindow);
	pObj.pMainObj.bSkipChanges = true;

	if(href.length>0)
	{
		if (window.pElement)
		{
			var link = pElement;
		}
		else
		{
			var link = false;
			var sRand = '#'+Math.random().toString().substring(2);
			pObj.pMainObj.pEditorDocument.execCommand('CreateLink', false, sRand);
			if(document.evaluate)
			{
				link = pObj.pMainObj.pEditorDocument.evaluate("//a[@href='"+sRand+"']", pObj.pMainObj.pEditorDocument.body, null, 9, null).singleNodeValue;
			}
			else
			{
				var arLinks = pObj.pMainObj.pEditorDocument.getElementsByTagName('A');
				for(var i = 0; i < arLinks.length; i++)
				{
					if(arLinks[i].getAttribute('href', 2) == sRand)
					{
						link = arLinks[i];
						break;
					}
				}
			}
		}
		if(link)
		{
			SAttr(link, 'href', href);
			SAttr(link, '__bxhref', href);
			SAttr(link, 'target', document.getElementById("bx_target").value);
			SAttr(link, 'id', document.getElementById("__bx_id").value);
			SAttr(link, 'title', document.getElementById("BXEditorDialog_title").value);
			SAttr(link, 'className',  document.getElementById("classname").value);
		}
	}
	pObj.pMainObj.bSkipChanges = false;
	pObj.pMainObj.OnChange("link");
}

var pT = null;
function _Ch()
{
	var t = document.getElementById('bx_link_type');
	if(pT)
		pT.style.display = 'none';
	pT = document.getElementById('bx_' + t.value);
	pT.style.display = "block";
	var tr = document.getElementById('trg');

	if(t.value=='t1' || t.value=='t2')
	{
		tr.style.display = GetDisplStr(1);
		tr.parentNode.cells[1].style.display = GetDisplStr(1);
	}
	else
	{
		tr.style.display = GetDisplStr(0);
		tr.parentNode.cells[1].style.display = GetDisplStr(0);
	}

	_ChFix();
}

function _ChTargL()
{
	var t = document.getElementById('bx_targ_list');
	var o = document.getElementById('bx_target');
	if(t.value.length>0)
	{
		o.disabled = true;
		o.value = t.value;
	}
	else
	{
		o.value = '';
		o.disabled = false;
	}
}

function _ChFix()
{
	var el = document.getElementById("fixstat");
	document.getElementById("event1").disabled = (!el.checked);
	document.getElementById("event2").disabled = (!el.checked);
	document.getElementById("event3").disabled = (!el.checked);
	document.getElementById("events").disabled = (!el.checked);
}

function SetUrl(url) {document.getElementById("url1").value = url;}
</script>
<?echo GetMessage("FILEMAN_ED_LINK_TYPE")?>
<select id='bx_link_type'>
	<option value='t1'><?echo GetMessage("FILEMAN_ED_LINK_TYPE1")?></option>
	<option value='t2'><?echo GetMessage("FILEMAN_ED_LINK_TYPE2")?></option>
	<option value='t3'><?echo GetMessage("FILEMAN_ED_LINK_TYPE3")?></option>
	<option value='t4'><?echo GetMessage("FILEMAN_ED_LINK_TYPE4")?></option>
</select>

<hr size="1">

<?
CAdminFileDialog::ShowScript(Array
	(
		"event" => "OpenFileBrowserWindFile",
		"arResultDest" => Array("FORM_NAME" => "form_editor_dialog", "FORM_ELEMENT_NAME" => "url1"),
		"arPath" => Array("SITE" => $_GET["site"]),
		"select" => 'F',// F - file only, D - folder only, DF - files & dirs
		"operation" => 'O',// O - open, S - save
		"showUploadTab" => true,
		"showAddToMenuTab" => false,
		"fileFilter" => 'php, html',
		"allowAllFiles" => true,
		"SaveConfig" => true
	)
);
?>

<table width="100%" id="bx_t1" style="display:none;" border="0">
	<tr>
		<td width="50%" align="right"><?echo GetMessage("FILEMAN_ED_LINK_DOC")?></td>
		<td width="250"><input type="text" size="25" value="" id="url1"><input type="button" id="OpenFileBrowserWindFile_button" value="..."></td>
	</tr>
</table>

<table width="100%"  id="bx_t2" style="display:none;" border="0">
	<tr>
		<td align="right" width="50%">URL:</td>
		<td width="250" >
			<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td>
						<select id='url_type'>
							<option value="http://">http://</option>
							<option value="ftp://">ftp://</option>
							<option value="https://">https://</option>
							<option value=""></option>
						</select>
					</td>
					<td>
						<input type="text" size="20" value="" id="url2">
					</td>
				</tr>
			</table>
		</td>
</tr>
<tr>
	<td align="right" valign="top"><?echo GetMessage("FILEMAN_ED_LINK_STAT")?></td>
	<td>
		<input type="checkbox" id="fixstat" value=""><br>
		<table cellpadding="0" cellspacing="0" id="events">
			<tr>
				<td valign="top">
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				</td>
				<td>
					<table cellpadding="0" cellspacing="0">
						<tr><td>Event1:</td><td><input type="event1" id="event1" size="10" value=""></td></tr>
						<tr><td>Event2:</td><td><input type="event2" id="event2" size="10" value=""></td></tr>
						<tr><td>Event3:</td><td><input type="event3" id="event3" size="10" value=""></td></tr>
					</table>
				</td>
			</tr>
		</table>
	</td>
</tr>
</table>

<table width="100%"  id="bx_t3" style="display:none;" border="0">
	<tr>
		<td align="right" width="211px">
			<?echo GetMessage("FILEMAN_ED_LINK_ACH")?>
		</td>
		<td>
			<select id="url3"></select>
		</td>
	</tr>
</table>

<table width="100%" id="bx_t4" style="display:none;" border="0">
<tr>
	<td align="right" width="211px">EMail:</td>
	<td>
		<input type="text" size="25" value="" id="url4">
	</td>
</tr>
</table>


<table width="100%" border="0">
	<tr >
		<td id='trg' style="display:none;" align="right"><?echo GetMessage("FILEMAN_ED_LINK_WIN")?></td>
		<td width="50%">
			<table cellpadding="0" cellspacing="0">
				<tr>
					<td>
						<select onchange="_ChTargL()" id='bx_targ_list'>
							<option value=""></option>
							<option value="_blank"><?echo GetMessage("FILEMAN_ED_LINK_WIN_BLANK")?></option>
							<option value="_parent"><?echo GetMessage("FILEMAN_ED_LINK_WIN_PARENT")?></option>
							<option value="_self"><?echo GetMessage("FILEMAN_ED_LINK_WIN_SELF")?></option>
							<option value="_top"><?echo GetMessage("FILEMAN_ED_LINK_WIN_TOP")?></option>
						</select>
					</td>
					<td><input type="text" size="7" id="bx_target" value=""></td>
				</tr>
			</table>
		</td>
	</tr>

	<tr>
		<td align="right"><?echo GetMessage("FILEMAN_ED_LINK_ATITLE")?></td>
		<td><input type="text" size="30" value="" id="BXEditorDialog_title"></td>
	</tr>
	<tr ><td align="right"><?echo GetMessage("FILEMAN_ED_STYLE")?></td><td>
		<select id='classname'>
		</select>
	</td></tr>
	<tr><td align="right">ID:</td><td><input type="text" size="30" value="" id="__bx_id"></td></tr>
</table>
<script>
document.getElementById("OpenFileBrowserWindFile_button").onclick = OpenFileBrowserWindFile;
document.getElementById("fixstat").onclick = _ChFix;
document.getElementById("bx_link_type").onchange = _Ch;
document.getElementById("bx_targ_list").onchange = _ChTargL;
</script>

<?elseif($name == "image"):?>
<script>
var pElement = null;
var prevsrc = '';

function _CHSize()
{
	var el = document.getElementById("bx_img_preview");
	SAttr(el, "width", document.getElementById("width").value);
	SAttr(el, "height", document.getElementById("height").value);
}

function _Reload(bFirst)
{
	var el = document.getElementById("bx_img_preview");
	if(prevsrc != document.getElementById("src").value)
	{
		el.style.display="";
		el.removeAttribute("width");
		el.removeAttribute("height");
		prevsrc=document.getElementById("src").value;
		el.src=document.getElementById("src").value;
	}

	el.alt=document.getElementById("alt").value;
	el.title=document.getElementById("img_title").value;
	el.border=document.getElementById("border").value;
	el.align=document.getElementById("align").value;
	el.hspace=document.getElementById("hspace").value;
	el.vspace=document.getElementById("vspace").value;
}

function _LPreview()
{
	var w = this.getAttribute('width');
	if (!w)
		w = parseInt(this.offsetWidth);

	var h = this.getAttribute('height');
	if (!h)
		h = parseInt(this.offsetHeight);

	document.getElementById("width").value = w;
	document.getElementById("height").value = h;
}

function OnLoad()
{
	pElement = pObj.pMainObj.GetSelectionObject();
	var preview = document.getElementById("bx_img_preview");
	preview.onload = _LPreview;

	if(!pElement || pElement.nodeName.toUpperCase() != 'IMG' || pElement.getAttribute("__bxtagname"))
	{
		pElement = null;
		oDialogTitle.innerHTML = '<?=GetMessage("FILEMAN_ED_NEW_IMG")?>';
	}
	else
	{
		oDialogTitle.innerHTML = '<?=GetMessage("FILEMAN_ED_EDIT_IMG")?>';
		document.getElementById("width").value = GAttr(pElement, "width");
		document.getElementById("height").value = GAttr(pElement, "height");
		document.getElementById("src").value = GAttr(pElement, "src");
		document.getElementById("img_title").value = GAttr(pElement, "title");
		document.getElementById("alt").value = GAttr(pElement, "alt");
		document.getElementById("border").value = GAttr(pElement, "border");
		document.getElementById("align").value = GAttr(pElement, "align");
		document.getElementById("hspace").value = GAttr(pElement, "hspace");
		document.getElementById("vspace").value = GAttr(pElement, "vspace");

		prevsrc = GAttr(pElement, "src");
		preview.style.display="";
		preview.src = prevsrc;
		preview.alt=document.getElementById("alt").value;
		preview.border=document.getElementById("border").value;
		preview.align=document.getElementById("align").value;
		preview.hspace=document.getElementById("hspace").value;
		preview.vspace=document.getElementById("vspace").value;
	}
	document.getElementById("src").onchange = _Reload;
}

function OnSave()
{
	pObj.pMainObj.bSkipChanges = true;
	var _src = document.getElementById("src").value;
	if (!_src)
		return;
	if(!pElement)
	{
		var tmpid = Math.random().toString().substring(2);
		var str = '<img id="'+tmpid+'" __bxsrc="'+bxhtmlspecialchars(_src)+'" />';
		BXSelectRange(oPrevRange,pObj.pMainObj.pEditorDocument,pObj.pMainObj.pEditorWindow);
		pObj.pMainObj.insertHTML(str);
		pElement = pObj.pMainObj.pEditorDocument.getElementById(tmpid);
		pElement.removeAttribute("id");
	}

	SAttr(pElement, "width", document.getElementById("width").value);
	SAttr(pElement, "height", document.getElementById("height").value);
	SAttr(pElement, "hspace", document.getElementById("hspace").value);
	SAttr(pElement, "vspace", document.getElementById("vspace").value);
	SAttr(pElement, "border", document.getElementById("border").value);
	SAttr(pElement, "align", document.getElementById("align").value);
	SAttr(pElement, "src", _src);
	SAttr(pElement, "__bxsrc", _src);
	SAttr(pElement, "alt", document.getElementById("alt").value);
	SAttr(pElement, "title", document.getElementById("img_title").value);
	pObj.pMainObj.bSkipChanges = false;
	pObj.pMainObj.OnChange("image");
}

function SetUrl(filename, path, site)
{
	var url, srcInput = document.getElementById("src");

	if (typeof filename == 'object') // Using medialibrary
	{
		url = filename.src;
		document.getElementById("img_title").value = filename.description || filename.name;
		document.getElementById("alt").value = filename.name;
	}
	else // Using file dialog
	{
		url = (path == '/' ? '' : path) + '/'+filename;
	}

	srcInput.value = url;
	if(srcInput.onchange)
		srcInput.onchange();
	srcInput.focus();
	srcInput.select();
}
</script>

<?
CAdminFileDialog::ShowScript(Array
	(
		"event" => "OpenFileBrowserWindImage",
		"arResultDest" => Array("FUNCTION_NAME" => "SetUrl"),
		"arPath" => Array("SITE" => $_GET["site"], "PATH" =>(strlen($str_FILENAME)>0 ? GetDirPath($str_FILENAME) : '')),
		"select" => 'F',// F - file only, D - folder only
		"operation" => 'O',// O - open, S - save
		"showUploadTab" => true,
		"showAddToMenuTab" => false,
		"fileFilter" => 'image',//'' - don't shjow select, 'image' - only images; "ext1,ext2" - Only files with ext1 and ext2 extentions;
		"allowAllFiles" => true,
		"SaveConfig" => true
	)
);
?>
<table width="100%" id="bx_t1" border="0">
	<tr>
		<td align="right" width="50%"><?echo GetMessage("FILEMAN_ED_IMG_PATH")?></td>
		<td width="50%">
			<table cellpadding="0" cellspacing="0">
				<tr>
					<td><input type="text" size="25" value="" id="src" name="src"></td>
					<td>
						<?
						CMedialib::ShowBrowseButton(
							array(
								'value' => '...',
								'event' => 'OpenFileBrowserWindImage',
								'id' => 'OpenFileBrowserWindImage_button',
								'MedialibConfig' => array(
									"arResultDest" => Array("FUNCTION_NAME" => "SetUrl"),
									"types" => array('image')
								)
							)
						);
						?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td width="50%" align="right"><?echo GetMessage("FILEMAN_ED_IMG_TITLE")?></td>
		<td width="50%"><input type="text" size="30" value="" id="img_title"></td>
	</tr>
	<tr>
		<td width="50%" align="right"><?echo GetMessage("FILEMAN_ED_IMG_ALT")?></td>
		<td width="50%"><input type="text" size="30" value="" id="alt"></td>
	</tr>
	<tr>
		<td width="50%" align="right">&nbsp;</td>
		<td width="50%">&nbsp;</td>
	</tr>
	<tr>
		<td width="50%" align="right" valign="top">
			<table width="100%">
				<tr><td align="right"><?echo GetMessage("FILEMAN_ED_IMG_W")?></td><td><input type="text" size="3" id="width"></td></tr>
				<tr><td align="right"><?echo GetMessage("FILEMAN_ED_IMG_H")?></td><td><input type="text" size="3" id="height"></td></tr>
				<tr><td align="right"><?echo GetMessage("FILEMAN_ED_IMG_HSp")?></td><td><input type="text" id="hspace" size="3"></td></tr>
				<tr><td align="right"><?echo GetMessage("FILEMAN_ED_IMG_HVp")?></td><td><input type="text" id="vspace" size="3"></td></tr>
				<tr><td align="right"><?echo GetMessage("FILEMAN_ED_IMG_BORD")?></td><td><input type="text" id="border" size="3"></td></tr>
				<tr><td align="right"><?echo GetMessage("FILEMAN_ED_IMG_AL")?></td><td>
					<select id="align">
						<option value=""></option>
						<!--
						<option value="absbottom">absbottom</option>
						<option value="absmiddle">absmiddle</option>
						<option value="baseline">baseline</option>
						-->
						<option value="bottom">bottom</option>
						<option value="left">left</option>
						<option value="middle">middle</option>
						<option value="right">right</option>
						<!--<option value="texttop">texttop</option>-->
						<option value="top">top</option>
					</select>
				</td></tr>
			</table>
		</td>
		<td width="50%"><?echo GetMessage("FILEMAN_ED_IMG_PREV")?>
		<div style="height:140px; width:180px; overflow: hidden; border: 1px #999999 solid; overflow-y: scroll; overflow-x: auto; color: #999999; background-color: #FFFFFF; padding: 3px">
			<img id="bx_img_preview" style="display:none"/>
			text text text text text text text text text text text text text text text text
			text text text text text text text text text text text text text text text text
			text text text text text text text text text text text text text text text text
			text text text text text text text text text text text text text text text text
			text text text text text text text text text text text text text text text text
			text text text text text text text text text text text text text text text text
			text text text text text text text text text text text text text text text text
			text text text text text text text text text text text text text text text text
			text text text text text text text text text text text text text text text text
			text text text text text text text text text text text text text text text text
		</div>
		</td>
	</tr>
</table>
<script>
//attaching events
document.getElementById("src").onchange = _Reload;
if (document.getElementById("OpenFileBrowserWindImage_button"))
	document.getElementById("OpenFileBrowserWindImage_button").onclick = OpenFileBrowserWindImage;

document.getElementById("width").onchange = _CHSize;
document.getElementById("width").onchange = _CHSize;
document.getElementById("height").onchange = _CHSize;
document.getElementById("hspace").onchange = _Reload;
document.getElementById("vspace").onchange = _Reload;
document.getElementById("border").onchange = _Reload;
document.getElementById("align").onchange = _Reload;
</script>

<?elseif($name == "table"):?>
<script>
var pElement = null;
function OnLoad()
{
	if(pObj.params.check_exists)
	{
		oDialogTitle.innerHTML = '<?=GetMessage("FILEMAN_ED_TABLE_PROP")?>';
		pElement = BXFindParentByTagName(pObj.pMainObj.GetSelectionObject(), 'TABLE');
	}
	else
	{
		oDialogTitle.innerHTML = '<?=GetMessage("FILEMAN_ED_NEW_TABLE")?>';
	}

	var arStFilter = ['TABLE', 'DEFAULT'], i;
	var elStyles = document.getElementById("classname");
	var oOption = new Option("", "", false, false);
	elStyles.options.add(oOption);
	var arStyles;
	for(i=0; i<arStFilter.length; i++)
	{
		arStyles = pObj.pMainObj.oStyles.GetStyles(arStFilter[i]);
		for(var j=0; j<arStyles.length; j++)
		{
			if(arStyles[j].className.length<=0)
				continue;
			oOption = new Option(arStyles[j].className, arStyles[j].className, false, false);
			elStyles.options.add(oOption);
		}
	}

	if(pElement)
	{
		document.getElementById("rows").value=pElement.rows.length;
		document.getElementById("rows").disabled = true;
		document.getElementById("cols").value=pElement.rows[0].cells.length;
		document.getElementById("cols").disabled = true;
		document.getElementById("cellpadding").value = GAttr(pElement, "cellPadding");
		document.getElementById("cellspacing").value = GAttr(pElement, "cellSpacing");
		document.getElementById("border").value = GAttr(pElement, "border");
		document.getElementById("align").value = GAttr(pElement, "align");
		document.getElementById("classname").value = GAttr(pElement, "className");
		var v = GAttr(pElement, "width");

		if(v.substr(-1, 1) == "%")
		{
			document.getElementById("width").value = v.substr(0, v.length-1);
			document.getElementById("width_unit").value = "%";
		}
		else
		{
			if(v.substr(-2, 2) == "px")
				v = v.substr(0, v.length-2);

		 	document.getElementById("width").value = v
		}

		v = GAttr(pElement, "height");
		if(v.substr(-1, 1) == "%")
		{
			document.getElementById("height").value = v.substr(0, v.length-1);
			document.getElementById("height_unit").value = "%";
		}
		else
		{
			if(v.substr(-1, 2) == "px")
				v = v.substr(0, v.length-2);

			document.getElementById("height").value = v
		}
	}
	else
	{
		document.getElementById("rows").value="2";
		document.getElementById("cols").value="3";
		document.getElementById("cellpadding").value="1";
		document.getElementById("cellspacing").value="1";
		document.getElementById("border").value="0";
	}
}

function OnSave()
{
	pObj.pMainObj.bSkipChanges = true;
	if(!pElement)
	{
		var tmpid = Math.random().toString().substring(2);
		var str = '<table id="'+tmpid+'"/>';
		BXSelectRange(oPrevRange, pObj.pMainObj.pEditorDocument,pObj.pMainObj.pEditorWindow);
		pObj.pMainObj.insertHTML(str);

		pElement = pObj.pMainObj.pEditorDocument.getElementById(tmpid);
		pElement.removeAttribute("id");

		var i, j, row, cell;
		for(i=0; i<document.getElementById("rows").value; i++)
		{
			row = pElement.insertRow(-1);
			for(j=0; j<document.getElementById("cols").value; j++)
			{
				cell = row.insertCell(-1);
				cell.innerHTML = '<br _moz_editor_bogus_node="on">';
				//cell.innerHTML = '&nbsp;';
			}
		}
	}
	else
	{
		if(pObj.pMainObj.bTableBorder)
			pObj.pMainObj.__ShowTableBorder(pElement, false);
	}

	SAttr(pElement, "width", (document.getElementById("width").value.length>0?document.getElementById("width").value+''+(document.getElementById("width_unit").value=='%'?'%':''):''));
	SAttr(pElement, "height", (document.getElementById("height").value.length>0?document.getElementById("height").value+''+(document.getElementById("height_unit").value=='%'?'%':''):''));
	SAttr(pElement, "border", document.getElementById("border").value);
	SAttr(pElement, "cellPadding", document.getElementById("cellpadding").value);
	SAttr(pElement, "cellSpacing", document.getElementById("cellspacing").value);
	SAttr(pElement, "align", document.getElementById("align").value);
	SAttr(pElement, 'className', document.getElementById("classname").value);

	pObj.pMainObj.OnChange("table");

	if(pObj.pMainObj.bTableBorder)
		pObj.pMainObj.__ShowTableBorder(pElement, true);
}

</script>

<table width="100%" id="bx_t1" border="0">
	<tr>
		<td align="right"><?echo GetMessage("FILEMAN_ED_TBL_R")?></td>
		<td><input type="text" size="3" id="rows"></td>
		<td>&nbsp;</td>
		<td align="right"><?echo GetMessage("FILEMAN_ED_TBL_W")?></td>
		<td nowrap><input type="text" size="3" id="width"><select id="width_unit"><option value="px"><?echo GetMessage("FILEMAN_ED_TBL_WPX")?></option><option value="%"><?echo GetMessage("FILEMAN_ED_TBL_WPR")?></option></select></td>
	</tr>
	<tr>
		<td align="right"><?echo GetMessage("FILEMAN_ED_TBL_COL")?></td>
		<td><input type="text" size="3" id="cols"></td>
		<td>&nbsp;</td>
		<td align="right"><?echo GetMessage("FILEMAN_ED_TBL_H")?></td>
		<td nowrap><input type="text" size="3" id="height"><select id="height_unit"><option value="px"><?echo GetMessage("FILEMAN_ED_TBL_WPX")?></option><option value="%"><?echo GetMessage("FILEMAN_ED_TBL_WPR")?></option></td>
	</tr>
	<tr>
		<td colspan="5">&nbsp;</td>
	</tr>
	<tr>
		<td align="right" nowrap><?echo GetMessage("FILEMAN_ED_IMG_BORD")?></td>
		<td><input type="text" id="border" size="3"></td>
		<td>&nbsp;</td>
		<td align="right" nowrap>Cell padding:</td>
		<td><input type="text" id="cellpadding" size="3"></td>
	</tr>
	<tr>
		<td align="right"><?echo GetMessage("FILEMAN_ED_TBL_AL")?></td>
		<td>
			<select id="align">
				<option value=""></option>
				<option value="left">left</option>
				<option value="center">center</option>
				<option value="right">right</option>
			</select>
		</td>
		<td>&nbsp;</td>
		<td align="right" nowrap>Cell spacing:</td>
		<td><input type="text" id="cellspacing" size="3"></td>
	</tr>
	<tr>
		<td align="right"><?echo GetMessage("FILEMAN_ED_STYLE")?></td>
		<td colspan="4"><select id="classname"></select></td>
	</tr>
</table>

<?elseif($name == "pasteastext"):?>
<script>
function OnLoad()
{
	oDialogTitle.innerHTML = '<?=GetMessage("FILEMAN_ED_PASTE_TEXT")?>';
	document.getElementById("BXInsertAsText").focus();
}

function OnSave()
{
	BXSelectRange(oPrevRange,pObj.pMainObj.pEditorDocument,pObj.pMainObj.pEditorWindow);
	pObj.pMainObj.PasteAsText(document.getElementById("BXInsertAsText").value);
}
</script>

<table width="100%" id="bx_t1" border="0">
	<tr>
		<td><?echo GetMessage("FILEMAN_ED_FF")?> "<?echo GetMessage("FILEMAN_ED_SAVE")?>":</td>
	</tr>
	<tr><td>
		<textarea id="BXInsertAsText" style="width:100%; height:200px"></textarea>
	</td></tr>
</table>

<?elseif($name == "pasteword"):?>
<script>
var pFrame = null;
function OnLoad()
{
	oDialogTitle.innerHTML = '<?=GetMessage("FILEMAN_ED_PASTE_WORD")?>';
	pFrame = document.getElementById("BXPasteAsWordNode_text");

	if(pFrame.contentDocument)
		pFrame.pDocument = pFrame.contentDocument;
	else
		pFrame.pDocument = pFrame.contentWindow.document;
	pFrame.pWindow = pFrame.contentWindow;

	pFrame.pDocument.open();
	pFrame.pDocument.write('<html><head><style>BODY{margin:0px; padding:0px; border:0px;}</style></head><body></body></html>');
	pFrame.pDocument.close();

	if(pFrame.pDocument.addEventListener)
		pFrame.pDocument.addEventListener('keydown', dialog_OnKeyDown, false);
	else if (pFrame.pDocument.attachEvent)
		pFrame.pDocument.body.attachEvent('onpaste', dialog_OnPaste);

	if(jsUtils.IsIE())
	{
		document.getElementById("BXPasteAsWordNode_ff").style.display = 'none';
		pFrame.pDocument.body.contentEditable = true;
		pFrame.pDocument.body.innerHTML = pObj.pMainObj.GetClipboardHTML();
		dialog_OnPaste();
	}
	else
		pFrame.pDocument.designMode='on';

	setTimeout(function()
	{
		var 
			wnd = pFrame.contentWindow,
			doc = pFrame.contentDocument || pFrame.contentWindow.document;
		if(wnd.focus)
			wnd.focus();
		else
			doc.body.focus();
	},
	10);
}

function dialog_OnKeyDown(e)
{
	if (e.ctrlKey && !e.shiftKey && !e.altKey)
	{
		if (!jsUtils.IsIE())
		{
			switch (e.which)
			{
				case 86: // "V" and "v"
				case 118:
					dialog_OnPaste(e);
					break ;
			}
		}
	}
	dialog_cleanAndShow();
}

function dialog_OnPaste(e)
{
	this.pOnChangeTimer = setTimeout(dialog_cleanAndShow, 10);
}

function dialog_cleanAndShow()
{
	var
		removeFonts = document.getElementById('BXPasteAsWordNode_removeFonts').checked,
		removeStyles = document.getElementById('BXPasteAsWordNode_removeStyles').checked,
		removeIndents = document.getElementById('BXPasteAsWordNode_removeIndents').checked;
		removeSpaces = document.getElementById('BXPasteAsWordNode_removeSpaces').checked;
	dialog_showClenedHtml(pObj.pMainObj.CleanWordText(pFrame.pDocument.body.innerHTML, [removeFonts, removeStyles, removeIndents, removeSpaces]));
}

function dialog_showClenedHtml(html)
{
	taSourse = document.getElementById('BXPasteAsWordNode_sourse');
	taSourse.value = html;
}

function OnSave()
{
	var removeFonts = document.getElementById('BXPasteAsWordNode_removeFonts').checked;
	var removeStyles = document.getElementById('BXPasteAsWordNode_removeStyles').checked;
	var removeIndents = document.getElementById('BXPasteAsWordNode_removeIndents').checked;
	var removeSpaces = document.getElementById('BXPasteAsWordNode_removeSpaces').checked;
	BXSelectRange(oPrevRange,pObj.pMainObj.pEditorDocument,pObj.pMainObj.pEditorWindow);
	pObj.pMainObj.PasteWord(pFrame.pDocument.body.innerHTML,[removeFonts, removeStyles, removeIndents, removeSpaces]);
}
</script>

<table width="100%" id="BXPasteAsWordNode_t1" border="0">
	<tr id="BXPasteAsWordNode_ff">
		<td><?echo GetMessage("FILEMAN_ED_FF")?> "<?echo GetMessage("FILEMAN_ED_SAVE")?>":</td>
	</tr>
	<tr>
		<td><iframe id="BXPasteAsWordNode_text" src="javascript:''" style="width:100%; height:150px; border:1px solid #CCCCCC;"></iframe></td>
	</tr>
	<tr>
		<td><?echo GetMessage("FILEMAN_ED_HTML_AFTER_CLEANING")?></td>
	</tr>
	<tr>
		<td><textarea id="BXPasteAsWordNode_sourse" style="width:100%; height:100px; border:1px solid #CCCCCC;" readonly="true"></textarea></td>
	</tr>
	<tr>
		<td>
			<input id="BXPasteAsWordNode_removeFonts" type="checkbox" checked="checked"><label for="BXPasteAsWordNode_removeFonts"><?echo GetMessage("FILEMAN_ED_REMOVE_FONTS")?></label><br>
			<input id="BXPasteAsWordNode_removeStyles" type="checkbox" checked="checked"> <label for="BXPasteAsWordNode_removeStyles"><?echo GetMessage("FILEMAN_ED_REMOVE_STYLES")?></label><br>
			<input id="BXPasteAsWordNode_removeIndents" type="checkbox" checked="checked"> <label for="BXPasteAsWordNode_removeIndents"><?echo GetMessage("FILEMAN_ED_REMOVE_INDENTS")?></label><br>
			<input id="BXPasteAsWordNode_removeSpaces" type="checkbox" checked="checked"> <label for="BXPasteAsWordNode_removeSpaces"><?echo GetMessage("FILEMAN_ED_REMOVE_SPACES")?></label>
		</td>
	</tr>
</table>

<script>
//attaching events
document.getElementById("BXPasteAsWordNode_removeFonts").onclick = 
document.getElementById("BXPasteAsWordNode_removeStyles").onclick = 
document.getElementById("BXPasteAsWordNode_removeIndents").onclick = 
document.getElementById("BXPasteAsWordNode_removeSpaces").onclick = 
dialog_cleanAndShow;
</script>

<?elseif($name == "asksave"):?>

<script>
function OnLoad()
{
	oDialogTitle.innerHTML = '<?=GetMessage("FILEMAN_ED_EDITOR")?>';
	document.getElementById("asksave_b1").focus();
	document.getElementById("asksave_b1").onclick = function(){OnSave('save')};
	document.getElementById("asksave_b2").onclick = function(){OnSave('exit')};
	document.getElementById("asksave_b3").onclick = OnSave;

	document.getElementById("buttonsSec").style.height = (jsUtils.IsIE()) ? 25 : 45;
}

function OnSave(t)
{
	if(t=='save')
	{
		pObj.pMainObj.isSubmited = true;
		if(pObj.params.savetype == 'saveas')
		{
			if (!pObj.params.popupMode)
				pObj.params.window.__bx_fd_save_as();
		}
		else
		{
			if (!pObj.params.popupMode)
			{
				pObj.pMainObj.SaveContent(true);
				pObj.pMainObj.pForm.submit();
			}
			else
			{
				BXFormSubmit();
			}
		}
	}
	else if(t=='exit')
	{
		pObj.pMainObj.isSubmited = true;
		if (!pObj.params.popupMode)
		{
			if(pObj.pMainObj.arConfig["sBackUrl"])
				pObj.params.window.location = pObj.pMainObj.arConfig["sBackUrl"];
		}
		else
		{
			top.jsPopup.CloseDialog();
		}
	}
	pObj.Close()
}
</script>

<table height="100%" width="100%" id="bx_t1" border="0">
	<tr>
		<td colspan="3">
			<table height="100%" width="100%" id="bx_t1" border="0" style="font-size:14px;">
			<tr>
			<td></td>
			<td><?=GetMessage("FILEMAN_DIALOG_EXIT_ACHTUNG")?></td>
			</tr>
			</table>
		</td>
	</tr>
	<tr id="buttonsSec" valign="top">
		<td align="center"><input style="font-size:12px; height: 25px; width: 180px;" type="button" id="asksave_b1" value="<?echo GetMessage("FILEMAN_DIALOG_SAVE_BUT")?>"></td>
		<td align="center"><input style="font-size:12px; height: 25px; width: 180px;" type="button" id="asksave_b2" value="<?echo GetMessage("FILEMAN_DIALOG_EXIT_BUT")?>"></td>
		<td align="center"><input style="font-size:12px; height: 25px; width: 180px;" type="button" id="asksave_b3" value="<?echo GetMessage("FILEMAN_DIALOG_EDIT_BUT")?>"></td>
	</tr>
</table>

<?elseif($name == "pageprops"):?>

<script>
var finput = false;
function OnLoad()
{
	oDialogTitle.innerHTML = '<?=GetMessage("FILEMAN_ED_EDITOR_PAGE_PROP")?>';
	var eddoc = pObj.params.document;
	document.getElementById('BX_dialog_title').value = eddoc.getElementById('title').value;
	document.getElementById("BX_more_prop_but").onclick = function(e) {AppendRow('', '');};

	<?if(CModule::IncludeModule("search")):?>
	var tag_property = "<? echo htmlspecialchars(COption::GetOptionString("search", "page_tag_property"));?>";
	<?else:?>
	var tag_property = "";
	<?endif;?>

	var code, val, name, cnt = parseInt(eddoc.getElementById("maxind").value)+1;
	for(var i=0; i<cnt; i++)
	{
		code = eddoc.getElementById("CODE_"+i);
		val = eddoc.getElementById("VALUE_"+i);
		name = eddoc.getElementById("NAME_"+i);
		if (tag_property == code.value)
			AppendTagPropertyRow(code.value, (val?val.value:null), (name?name.value:null));
		else
			AppendRow(code.value, (val?val.value:null), (name?name.value:null));
	}

	if(finput)
		finput.focus();
}

function AppendRow(code, value, name)
{
	var tbl = document.getElementById('pageprops_t1');

	var cnt = parseInt(document.getElementById("BX_dialog_maxind").value)+1;
	var r = tbl.insertRow(tbl.rows.length-1);
	var c = r.insertCell(-1);
	c.align="right";
	if(name)
		c.innerHTML = '<input type="hidden" id="BX_dialog_CODE_'+cnt+'" name="BX_dialog_CODE_'+cnt+'" value="'+bxhtmlspecialchars(code)+'">'+bxhtmlspecialchars(name)+':';
	else
	{
		c.innerHTML = '<input type="text" id="BX_dialog_CODE_'+cnt+'" name="BX_dialog_CODE_'+cnt+'" value="'+bxhtmlspecialchars(code)+'" size="30">:';
		if(!finput)
			finput = document.getElementById('BX_dialog_CODE_'+cnt);
	}

	c = r.insertCell(-1);
	c.innerHTML = '<input type="text" name="BX_dialog_VALUE_'+cnt+'" id="BX_dialog_VALUE_'+cnt+'" value="'+bxhtmlspecialchars(value)+'" size="55">';

	if(!finput)
		finput = document.getElementById('BX_dialog_VALUE_'+cnt);

	document.getElementById("BX_dialog_maxind").value = cnt;
}

function AppendTagPropertyRow(code, value, name)
{

	var tbl = document.getElementById('pageprops_t1');

	var cnt = parseInt(document.getElementById("BX_dialog_maxind").value)+1;
	var r = tbl.insertRow(tbl.rows.length-1);
	var c = r.insertCell(-1);
	c.align="right";
	if(name)
		c.innerHTML = '<input type="hidden" id="BX_dialog_CODE_'+cnt+'" name="BX_dialog_CODE_'+cnt+'" value="'+bxhtmlspecialchars(code)+'">'+bxhtmlspecialchars(name)+':';
	else
	{
		c.innerHTML = '<input type="text" id="BX_dialog_CODE_'+cnt+'" name="BX_dialog_CODE_'+cnt+'" value="'+bxhtmlspecialchars(code)+'" size="30">:';
		if(!finput)
			finput = document.getElementById('BX_dialog_CODE_'+cnt);
	}

	c = r.insertCell(-1);
	id = 'BX_dialog_VALUE_'+cnt;
	name = 'BX_dialog_VALUE_'+cnt;
	c.innerHTML =  '<input name="'+name+'" id="'+id+'" type="text" autocomplete="off" value="'+value+'" onfocus="window.oObject[this.id] = new JsTc(this, []);"  size="50"/><input type="checkbox" id="ck_'+id+'" name="ck_'+name+'" <? echo (CUserOptions::GetOption("search_tags", "order", "CNT") == "NAME" ? "checked": "");?> title="<?=GetMessage("SEARCH_TAGS_SORTING_TIP")?>">';

	if(!finput)
		finput = document.getElementById('BX_dialog_VALUE_'+cnt);

	document.getElementById("BX_dialog_maxind").value = cnt;
}

function OnSave()
{
	var eddoc = pObj.params.document;

	var edcnt = parseInt(eddoc.getElementById("maxind").value);
	var cnt = parseInt(document.getElementById("BX_dialog_maxind").value);

	for(var i=0; i<=edcnt; i++)
	{
		if(eddoc.getElementById("CODE_"+i).value != document.getElementById("BX_dialog_CODE_"+i).value)
			eddoc.getElementById("CODE_"+i).value = document.getElementById("BX_dialog_CODE_"+i).value;
		if(eddoc.getElementById("VALUE_"+i).value != document.getElementById("BX_dialog_VALUE_"+i).value)
			eddoc.getElementById("VALUE_"+i).value = document.getElementById("BX_dialog_VALUE_"+i).value;
	}

	for(i = edcnt+1; i<=cnt; i++)
	{
		pObj.params.window._MoreRProps(document.getElementById("BX_dialog_CODE_"+i).value, document.getElementById("BX_dialog_VALUE_"+i).value);
	}

	eddoc.getElementById("maxind").value = cnt;
	eddoc.getElementById('title').value = document.getElementById('BX_dialog_title').value;

	pObj.pMainObj.bNotSaved = true;

	return iNoOnSelectionChange;
}
</script>
<div style="width:100%; height:220px; overflow-y:scroll;">
<table width="100%" id="pageprops_t1" border="0">
	<tr>
		<td width="40%" align="right"><b><?echo GetMessage("FILEMAN_DIALOG_TITLE")?></b></td>
		<td width="60%"><input type="text" id="BX_dialog_title" value="" size="30"></td>
	</tr>
	<tr>
		<td align="right"></td>
		<td><input id="BX_more_prop_but" type="button" value="<?echo GetMessage("FILEMAN_DIALOG_MORE_PROP")?>"></td>
	</tr>
</table>
</div>
<input type="hidden" value="-1" id="BX_dialog_maxind">

<?elseif($name == "spellcheck"):?>

<script>
var pElement = null;
function OnLoad()
{
	oDialogTitle.innerHTML = '<?=GetMessage("FILEMAN_ED_SPELLCHECKING")?>';
	pElement = pObj.pMainObj.GetSelectionObject();
	var BXLang = pObj.params.BXLang;
	var usePspell = pObj.params.usePspell;
	var useCustomSpell = pObj.params.useCustomSpell;
	oBXSpellChecker = new BXSpellChecker(pObj.pMainObj, BXLang, usePspell, useCustomSpell);
	oBXSpellChecker.parseDocument();
	oBXSpellChecker.spellCheck();
}

window.closeDialog = function()
{
	BXClearSelection(pObj.pMainObj.pEditorDocument,pObj.pMainObj.pEditorWindow);
	pObj.Close();
}

</script>
	<div id="BX_dialog_waitWin" style="display: block; text-align: center; vertical-align: middle;">
		<table border="0" width="100%" height="100%" style="vertical-align: middle">
			<tr><td height="60"></td></tr>
			<tr>
				<td align="center" valign="top">
					<img style="vertical-align: middle;" src="/bitrix/themes/.default/images/wait.gif" />
					<span style="vertical-align: middle;"><?echo GetMessage("FILEMAN_ED_WAIT_LOADING")?></span>
				</td>
			</tr>
		</table>
	</div>
	<div id="BX_dialog_okMessWin" style="display: none;">
		<table border="0" width="100%" height="100%">
			<tr>
				<td align="center">
					<span style="vertical-align: middle;"><?echo GetMessage("FILEMAN_ED_SPELL_FINISHED")?></span>
					<br><br>
					<input id="BX_dialog_butClose" type="button" value="<?echo GetMessage("FILEMAN_ED_CLOSE")?>" style="width:150">
				</td>
			</tr>
		</table>
	</div>
	<div id="BX_dialog_spellResultWin" style="display: none">
	<table width="380" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr><td colspan="4" height="5"></td></tr>
		<tr>
			<td width="224" valign="top"><input id="BX_dialog_wordBox" type="text" style="width:100%;"></td>
			<td width="8"></td>
			<td width="140" valign="top"><input id="BX_dialog_butSkip" type="button" value="<?echo GetMessage("FILEMAN_ED_SKIP")?>" style="width:100%;"></td>
			<td width="8"></td>
		</tr>
		<tr><td colspan="4" height="7"></td></tr>
		<tr>
			<td rowspan="9" valign="top"><select id="BX_dialog_suggestionsBox" size="8" style="width:100%;"></select></td>
			<td></td>
			<td><input id="BX_dialog_butSkipAll" type="button" value="<?echo GetMessage("FILEMAN_ED_SKIP_ALL")?>" style="width:100%;"></td>
			<td></td>
		</tr>
		<tr height="5"><td colspan="2" height="5"></td></tr>
		<tr>
			<td></td>
			<td><input id="BX_dialog_butReplace" type="button" value="<?echo GetMessage("FILEMAN_ED_REPLACE")?>" style="width:100%;"></td>
			<td></td>
		</tr>
		<tr height="5"><td colspan="2" height="5"></td></tr>
		<tr>
			<td></td>
			<td><input id="BX_dialog_butReplaceAll" type="button" value="<?echo GetMessage("FILEMAN_ED_REPLACE_ALL")?>" style="width:100%;"></td>
			<td></td>
		</tr>
		<tr height="5"><td colspan="2" height="5"></td></tr>
		<tr>
			<td></td>
			<td><input id="BX_dialog_butAdd" type="button" value="<?echo GetMessage("FILEMAN_ED_ADD")?>" style="width:100%;"></td>
			<td></td>
		</tr>
		<tr height="5"><td colspan="2" height="5"></td></tr>
		<tr>
			<td></td>
			<td><input id="BX_dialog_butClose" type="button" value="<?echo GetMessage("FILEMAN_ED_CLOSE")?>" style="width:100%;" onClick="pObj.Close();"></td>
			<td></td>
		</tr>
	</table>
	</div>

<?elseif($name == "specialchar"):?>

<script>
function OnLoad()
{
	oDialogTitle.innerHTML = '<?=GetMessage("FILEMAN_ED_EDITOR_SPES_CHAR")?>';
	var cancelBut = document.getElementById("cancelBut");
	cancelBut.style.display = "none";
	var saveBut = document.getElementById("saveBut");
	saveBut.style.display = "none";

	arEntities_dialog = ['&iexcl;','&cent;','&pound;','&curren;','&yen;','&brvbar;','&sect;','&uml;','&copy;','&ordf;','&laquo;','&not;','&reg;','&macr;','&deg;','&plusmn;','&sup2;','&sup3;','&acute;','&micro;','&para;','&middot;','&cedil;','&sup1;','&ordm;','&raquo;','&frac14;','&frac12;','&frac34;','&iquest;','&Agrave;','&Aacute;','&Acirc;','&Atilde;','&Auml;','&Aring;','&AElig;','&Ccedil;','&Egrave;','&Eacute;','&Ecirc;','&Euml;','&Igrave;','&Iacute;','&Icirc;','&Iuml;','&ETH;','&Ntilde;','&Ograve;','&Oacute;','&Ocirc;','&Otilde;','&Ouml;','&times;','&Oslash;','&Ugrave;','&Uacute;','&Ucirc;','&Uuml;','&Yacute;','&THORN;','&szlig;','&agrave;','&aacute;','&acirc;','&atilde;','&auml;','&aring;','&aelig;','&ccedil;','&egrave;','&eacute;','&ecirc;','&euml;','&igrave;','&iacute;','&icirc;','&iuml;','&eth;','&ntilde;','&ograve;','&oacute;','&ocirc;','&otilde;','&ouml;','&divide;','&oslash;','&ugrave;','&uacute;','&ucirc;','&uuml;','&yacute;','&thorn;','&yuml;','&OElig;','&oelig;','&Scaron;','&scaron;','&Yuml;','&circ;','&tilde;','&ndash;','&mdash;','&lsquo;','&rsquo;','&sbquo;','&ldquo;','&rdquo;','&bdquo;','&dagger;','&Dagger;','&permil;','&lsaquo;','&rsaquo;','&euro;','&Alpha;','&Beta;','&Gamma;','&Delta;','&Epsilon;','&Zeta;','&Eta;','&Theta;','&Iota;','&Kappa;','&Lambda;','&Mu;','&Nu;','&Xi;','&Omicron;','&Pi;','&Rho;','&Sigma;','&Tau;','&Upsilon;','&Phi;','&Chi;','&Psi;','&Omega;','&alpha;','&beta;','&gamma;','&delta;','&epsilon;','&zeta;','&eta;','&theta;','&iota;','&kappa;','&lambda;','&mu;','&nu;','&xi;','&omicron;','&pi;','&rho;','&sigmaf;','&sigma;','&tau;','&upsilon;','&phi;','&chi;','&psi;','&omega;','&bull;','&hellip;','&prime;','&Prime;','&oline;','&frasl;','&trade;','&larr;','&uarr;','&rarr;','&darr;','&harr;','&part;','&sum;','&minus;','&radic;','&infin;','&int;','&asymp;','&ne;','&equiv;','&le;','&ge;','&loz;','&spades;','&clubs;','&hearts;'];

	if(!jsUtils.IsIE())
	{
		arEntities_dialog = arEntities_dialog.concat('&thetasym;','&upsih;','&piv;','&weierp;','&image;','&real;','&alefsym;','&crarr;','&lArr;','&uArr;','&rArr;','&dArr;','&hArr;','&forall;','&exist;','&empty;','&nabla;','&isin;','&notin;','&ni;','&prod;','&lowast;','&prop;','&ang;','&and;','&or;','&cap;','&cup;','&there4;','&sim;','&cong;','&sub;','&sup;','&nsub;','&sube;','&supe;','&oplus;','&otimes;','&perp;','&sdot;','&lceil;','&rceil;','&lfloor;','&rfloor;','&lang;','&rang;','&diams;');
	}

	drawTable();
}

function drawTable()
{
	var charCont = document.getElementById("charCont");
	var chTable = document.createElement("TABLE");
	var tBody = document.createElement("TBODY");
	chTable.appendChild(tBody);
	charCont.appendChild(chTable);

	var r,c,lEn = arEntities_dialog.length;
	var elEntity = document.createElement("span");
	var elEmpty = document.createElement("span");

	for(var i=0; i<lEn; i++)
	{
		if (i%19 == 0)
		{
			r = document.createElement("TR");
			tBody.appendChild(r);
		}
		elEntity.innerHTML = arEntities_dialog[i];
		c = document.createElement("TD");
		c.id = 'e_'+i;
		c.innerHTML = elEntity.innerHTML;
		setCellstyle(c,'normal');
		setCellEvents(c);
		r.appendChild(c);
	}
}

function setCellstyle(cellObj,mode){
	switch (mode)
	{
		case 'normal':
			cellObj.style.width = "17px";
			cellObj.style.height = "17px";
			cellObj.style.fontSize = "12px";
			cellObj.style.textAlign = "center";
			cellObj.style.verticalAlign = "middle";
			cellObj.style.border = "1px solid #ffffff";
			cellObj.style.backgroundColor = "#FFFFFF";
			break;
		case 'over':
			cellObj.style.width = "17px";
			cellObj.style.height = "17px";
			cellObj.style.fontSize = "12px";
			cellObj.style.textAlign = "center";
			cellObj.style.verticalAlign = "middle";
			cellObj.style.border = "#4B4B6F 1px solid";
			cellObj.style.backgroundColor = "#BFC6B8";
			break;
	}
}

function setCellEvents(cellObj){
	cellObj.onmouseover = function(){
		setCellstyle(this,'over');
		prevChar(this);
	}
	cellObj.onmouseout = function(){
		setCellstyle(this,'normal');
	}
	cellObj.onclick = function(){
		var entInd = cellObj.id.substring(2);
		BXSelectRange(oPrevRange,pObj.pMainObj.pEditorDocument,pObj.pMainObj.pEditorWindow);
		pObj.pMainObj.insertHTML(arEntities_dialog[entInd]);
		pObj.Close();
	}
}

function prevChar(cellObj)
{
	var charPrev = document.getElementById('charPrev');
	charPrev.innerHTML = cellObj.innerHTML;
	charPrev.style.fontSize = "80px";
	charPrev.style.textAlign = "center";
	charPrev.style.verticalAlign = "middle";

	var charPrev = document.getElementById('entityName');
	var entInd = cellObj.id.substring(2);
	charPrev.innerHTML = arEntities_dialog[entInd].substr(1,arEntities_dialog[entInd].length-2);
}
</script>
	<div style="height: 260px;"></div>
	<div id="charCont" style="width: 455px; position: absolute; top: 25px; left: 5px"></div>
	<div id="charPrev" style="background-color: #FFFFFF; width: 120px; height: 120px; position: absolute; top: 25px; left: 465px"></div>
	<div id="entityName" style="font-size: 14; text-align: center; background-color: #FFFFFF; width: 120px; height: 20px; position: absolute; top: 130px; left: 465px"></div>
	<div id="saveBut_div" style="width: 120px; height: 20px; position: absolute; top: 258px; left: 465px;">
	<input type="button" value="<?echo GetMessage("FILEMAN_ED_CANC")?>" onclick="pObj.Close();"></div>
<?elseif($name == "settings"):?>
<script>
/*  ----------------------------------- SETTINGS --------------------------------------------*/
function OnLoad()
{
	oDialogTitle.innerHTML = '<?=GetMessage("FILEMAN_ED_SETTINGS")?>';
	if (!pObj.params.lightMode)
	{
		// ************************ TAB #1: Toolbar settings ***********************************
		var oDiv = document.getElementById("__bx_set_1_toolbar");
		oDiv.style.height = '190px';
		oDiv.innerHTML = '';
		window.temp_arToolbarSettings = copyObj(SETTINGS[pObj.pMainObj.name].arToolbarSettings);
		_displayToolbarList(oDiv);
		oDiv = null;
	}
	// ************************ TAB #2: Taskbar settings ***********************************
	oDiv = document.getElementById("__bx_set_2_taskbar");
	oDiv.style.height = '190px';
	oDiv.innerHTML = '';
	window.temp_arTaskbarSettings = copyObj(SETTINGS[pObj.pMainObj.name].arTaskbarSettings);
	_displayTaskbarList(oDiv);

	// ************************ TAB #3: Additional Properties ***********************************
	oDiv = document.getElementById("__bx_set_3_add_props");
	oDiv.style.height = '190px';
	oDiv.innerHTML = '';
	_displayAdditionalProps(oDiv);

	document.getElementById("restoreDefault").onclick = function(e){restoreSettings()};
}

function _displayToolbarList(oCont)
{
	var oTable = document.createElement("TABLE");
	oTable.width = "100%";
	_displayTitle(oTable,'<?=GetMessage("FILEMAN_ED_TLBR_DISP")?>');
	var _show;
	pObj.arToolbarCheckboxes = [];
	for(var sToolBarId in arToolbars)
	{
		if (arToolbars[sToolBarId] && typeof arToolbars[sToolBarId] == 'object')
			_displayToolbarRow(oTable, sToolBarId, SETTINGS[pObj.pMainObj.name].arToolbarSettings[sToolBarId].show);
	}

	_displayTitle(oTable,'<?=GetMessage("FILEMAN_ED_DISP_SET")?>');
	oTr = oTable.insertRow(-1);
	oTr.className = "bxpropertysell";
	oTd = oTr.insertCell(-1);
	oTd.innerHTML = '<?=GetMessage("FILEMAN_ED_REM_TLBR")?>';
	oTd.align = "right";
	oTd = oTr.insertCell(-1);
	pCheckbox = pObj.pMainObj.CreateElement("INPUT", {type:'checkbox', id: '__bx_rs_tlbrs'});
	oTd.appendChild(pCheckbox);

	oBXEditorUtils.setCheckbox(pCheckbox, pObj.pMainObj.RS_toolbars);
	pCheckbox.onclick = function(){enableCheckboxes(pObj.arToolbarCheckboxes, this.checked);};
	oTd.align = "left";
	oCont.appendChild(oTable);
}

function _displayToolbarRow(oTb,toolbarId,_show)
{
	var oTr = oTb.insertRow(-1);
	oTr.className = "bxpropertysell";
	var oTd = oTr.insertCell(-1);
	oTd.innerHTML = arToolbars[toolbarId][0];
	oTd.align = "right";
	oTd.width = "60%";

	oTd = oTr.insertCell(-1);
	pCheckbox = pObj.pMainObj.CreateElement("INPUT", {type:'checkbox', id: '__bx_'+toolbarId, '__bxid' : toolbarId});
	oTd.appendChild(pCheckbox);
	oBXEditorUtils.setCheckbox(pCheckbox, _show);
	if (toolbarId != "standart")
		pObj.arToolbarCheckboxes.push(pCheckbox);

	if (toolbarId=="standart" || !pObj.pMainObj.RS_toolbars)
		pCheckbox.disabled = "disabled";

	pCheckbox.onchange = function(e) {window.temp_arToolbarSettings[this.getAttribute("__bxid")].show = this.checked;}
	oTd.align = "left"
	oTd.width = "40%";
}


function _displayTaskbarList(oCont)
{
	var oTable = document.createElement("TABLE");
	oTable.width = "100%";
	_displayTitle(oTable,'<?=GetMessage("FILEMAN_ED_TSKBR_DISP")?>');
	var _show;
	pObj.arTaskbarCheckboxes = [];

	for(var k in ar_BXTaskbarS)
	{
		if (ar_BXTaskbarS[k] && ar_BXTaskbarS[k].pMainObj && ar_BXTaskbarS[k].pMainObj.name == pObj.pMainObj.name)
			_displayTaskbarRow(oTable, ar_BXTaskbarS[k], SETTINGS[pObj.pMainObj.name].arTaskbarSettings[ar_BXTaskbarS[k].name]);
	}

	//########    COMPONENTS 1.0  ########
	if(pObj.pMainObj.allowedTaskbars['BXComponentsTaskbar'])
	{
		BXComponentsTaskbar_need_preload = false;
		if (!window.BXComponentsTaskbar)
		{
			BXComponentsTaskbar_need_preload = true;
			var settings = SETTINGS[pObj.pMainObj.name].arTaskbarSettings['BXComponentsTaskbar'];
			if (!settings.show)
				_displayTaskbarRow(oTable,{name:'BXComponentsTaskbar',title:BX_MESS.CompTBTitle+" 1.0"},settings);
		}
	}

	//########    COMPONENTS 2.0  ########
	if(pObj.pMainObj.allowedTaskbars['BXComponents2Taskbar'])
	{
		BXComponents2Taskbar_need_preload = false;
		if (!window.BXComponents2Taskbar)
		{
			BXComponents2Taskbar_need_preload = true;
			var settings = SETTINGS[pObj.pMainObj.name].arTaskbarSettings['BXComponents2Taskbar'];
			if (!settings.show)
				_displayTaskbarRow(oTable,{name:'BXComponents2Taskbar',title:BX_MESS.CompTBTitle+" 2.0"},settings);
		}
	}

	//###########    SNIPPETS  ###########
	if(pObj.pMainObj.allowedTaskbars['BXSnippetsTaskbar'])
	{
		BXSnippetsTaskbar_need_preload = false;
		if (!ar_BXTaskbarS["BXSnippetsTaskbar_"+pObj.pMainObj.name])
		{
			//BXSnippetsTaskbar
			BXSnippetsTaskbar_need_preload = true;
			//var settings = SETTINGS[pObj.pMainObj.name].arTaskbarSettings['BXSnippetsTaskbar'] || {};
			var settings = SETTINGS[pObj.pMainObj.name].arTaskbarSettings['BXSnippetsTaskbar'];
			if (!settings.show)
				_displayTaskbarRow(oTable,{name:'BXSnippetsTaskbar',title:BX_MESS.SnippetsTB}, settings);
		}
	}
	_displayTitle(oTable,'<?=GetMessage("FILEMAN_ED_DISP_SET")?>');
	oTr = oTable.insertRow(-1);
	oTr.className = "bxpropertysell";
	oTd = oTr.insertCell(-1);
	oTd.innerHTML = '<?=GetMessage("FILEMAN_ED_REM_TSKBR")?>';
	oTd.align = "right";
	oTd = oTr.insertCell(-1);
	pCheckbox = pObj.pMainObj.CreateElement("INPUT", {type: 'checkbox', id: '__bx_rs_tskbrs'});
	oTd.appendChild(pCheckbox);
	oBXEditorUtils.setCheckbox(pCheckbox,pObj.pMainObj.RS_taskbars);
	pCheckbox.onclick = function(){enableCheckboxes(pObj.arTaskbarCheckboxes, this.checked);};

	oTd.align = "left";
	oCont.appendChild(oTable);
}


function _displayTaskbarRow(oTb, oTaskbar, arSettings)
{
	_show = arSettings.show;
	var taskbarId = oTaskbar.name;
	var oTr = oTb.insertRow(-1);
	oTr.className = "bxpropertysell";
	var oTd = oTr.insertCell(-1);
	oTd.innerHTML = oTaskbar.title;
	oTd.align = "right";
	oTd.width = "60%";
	oTd = oTr.insertCell(-1);

	pCheckbox = pObj.pMainObj.CreateElement("INPUT", {type: 'checkbox', id: '__bx_' + taskbarId, __bxid : taskbarId});
	oTd.appendChild(pCheckbox);
	pObj.arTaskbarCheckboxes.push(pCheckbox);
	oBXEditorUtils.setCheckbox(pCheckbox,_show);
	pCheckbox.onchange = function(e) {window.temp_arTaskbarSettings[this.getAttribute("__bxid")].show = this.checked;}
	if (!pObj.pMainObj.RS_taskbars)
		pCheckbox.disabled = "disabled";

	oTd.align = "left"
	oTd.width = "40%";
}


function _displayTitle(oTb, sTitle)
{
	var oTr = oTb.insertRow(-1);
	oTr.className = "heading_dialog";
	var oTd = oTr.insertCell(-1);
	oTd.colSpan = 2;
	oTd.innerHTML = sTitle;
}


function _displayAdditionalProps(oCont)
{
	var oTable = pObj.pMainObj.CreateElement('TABLE', {width: '100%'});
	var oTd = oTable.insertRow(-1).insertCell(-1);
	oTd.style.height = '10px';

	var oTr = oTable.insertRow(-1);
	oTr.className = "bxpropertysell";
	oTd = oTr.insertCell(-1);
	oTd.innerHTML = '<label for="__bx_show_tooltips"><?=GetMessage("FILEMAN_ED_SHOW_TOOLTIPS")?></label>';
	oTd.align = "right";
	oTd.width = "70%";
	oTd = oTr.insertCell(-1);
	var pCheckbox = oTd.appendChild(pObj.pMainObj.CreateElement("INPUT", {type: 'checkbox', id: '__bx_show_tooltips', name: '__bx_show_tooltips'}));
	oBXEditorUtils.setCheckbox(pCheckbox, pObj.pMainObj.showTooltips4Components);
	oTd.align = "left";
	oTd.width = "30%";

	var oTr = oTable.insertRow(-1);
	oTr.className = "bxpropertysell";
	oTd = oTr.insertCell(-1);
	oTd.innerHTML = '<label for="__bx_visual_effects"><?=GetMessage("FILEMAN_ED_VIS_EFFECTS")?></label>';
	oTd.align = "right";
	oTd = oTr.insertCell(-1);
	var pCheckbox = oTd.appendChild(pObj.pMainObj.CreateElement("INPUT", {type: 'checkbox', id: '__bx_visual_effects', name: '__bx_visual_effects'}));
	oBXEditorUtils.setCheckbox(pCheckbox, pObj.pMainObj.visualEffects);
	oTd.align = "left";

	if (pObj.pMainObj.arConfig.allowRenderComp2)
	{
		oTr = oTable.insertRow(-1);
		oTr.className = "bxpropertysell";
		oTd = oTr.insertCell(-1);
		oTd.innerHTML = '<label for="__bx_render_comp2"><?=GetMessage("FILEMAN_ED_RENDER_COMPONENTS2")?></label>';
		oTd.align = "right";
		oTd = oTr.insertCell(-1);
		pCheckbox = oTd.appendChild(pObj.pMainObj.CreateElement("INPUT", {type: 'checkbox', id: '__bx_render_comp2', name: '__bx_render_comp2'}));
		oTd.align = "left";
		oBXEditorUtils.setCheckbox(pCheckbox, pObj.pMainObj.bRenderComponents);
	}

	oCont.appendChild(oTable);
}


function restoreSettings()
{
	BXUnsetConfiguration(pObj.pMainObj);
	var RSPreloader = new BXPreloader(
		[{func: BXGetConfiguration, params: ['get_all', pObj.pMainObj]}],
		{
			func: function()
			{
				if (!lightMode)
					BXRefreshToolbars(pObj.pMainObj);
				BXRefreshTaskbars(pObj.pMainObj);
				pObj.Close();
			}
		}
	);
	RSPreloader.LoadStep();
}

function enableCheckboxes(arCh, bEnable)
{
	for (var i = 0, l = arCh.length; i < l; i++)
		arCh[i].disabled = !bEnable;
}

function OnSave()
{
	if (!document.getElementById("__bx_rs_tskbrs").checked)
		temp_arTaskbarSettings = arTaskbarSettings_default;

	//var showTooltips = (document.getElementById("__bx_show_tooltips").checked) ? true : false;
	var showTooltips = (document.getElementById("__bx_show_tooltips").checked);
	if (showTooltips != pObj.pMainObj.showTooltips4Components)
	{
		pObj.pMainObj.showTooltips4Components = showTooltips;
		BXSetConfiguration(pObj.pMainObj,"tooltips","GET");
	}

	var visEff = (document.getElementById("__bx_visual_effects").checked);
	if (visEff != pObj.pMainObj.visualEffects)
	{
		pObj.pMainObj.visualEffects = visEff;
		BXSetConfiguration(pObj.pMainObj, "visual_effects", "GET");
	}

	if (pObj.pMainObj.arConfig.allowRenderComp2)
	{
		var bRendComp2 = (document.getElementById("__bx_render_comp2").checked);
		if (bRendComp2 != pObj.pMainObj.bRenderComponents)
		{
			pObj.pMainObj.bRenderComponents = bRendComp2;
			pObj.pMainObj.SetEditorContent(pObj.pMainObj.GetContent());
			if (!pObj.pMainObj.pComponent2Taskbar.C2Parser.bInited)
				pObj.pMainObj.pComponent2Taskbar.C2Parser.InitRenderingSystem();
			else
				pObj.pMainObj.pComponent2Taskbar.C2Parser.COnChangeView();
			BXSetConfiguration(pObj.pMainObj, "render_components", "GET");
		}
	}

	if (!lightMode)
	{
		if (!document.getElementById("__bx_rs_tlbrs").checked)
			temp_arToolbarSettings = arToolbarSettings_default;

		if (!compareObj(SETTINGS[pObj.pMainObj.name].arToolbarSettings,window.temp_arToolbarSettings) ||
			(document.getElementById("__bx_rs_tlbrs").checked != pObj.pMainObj.RS_toolbars))
		{
			pObj.pMainObj.RS_toolbars = document.getElementById("__bx_rs_tlbrs").checked;
			SETTINGS[pObj.pMainObj.name].arToolbarSettings = temp_arToolbarSettings;
			var postData = oBXEditorUtils.ConvertArray2Post(temp_arToolbarSettings,'tlbrset');
			BXSetConfiguration(pObj.pMainObj,"toolbars","POST",postData);
			BXRefreshToolbars(pObj.pMainObj);
		}
	}

	if (!compareObj(SETTINGS[pObj.pMainObj.name].arTaskbarSettings, window.temp_arTaskbarSettings) ||
		(document.getElementById("__bx_rs_tskbrs").checked != pObj.pMainObj.RS_taskbars))
	{
		pObj.pMainObj.RS_taskbars = document.getElementById("__bx_rs_tskbrs").checked;
		SETTINGS[pObj.pMainObj.name].arTaskbarSettings = temp_arTaskbarSettings;

		//########    COMPONENTS 1.0  ########
		try
		{
			if (BXComponentsTaskbar_need_preload && SETTINGS[pObj.pMainObj.name].arTaskbarSettings['BXComponentsTaskbar'].show)
			{
				var oSript = document.body.appendChild(document.createElement('script'));
				oSript.src = "/bitrix/admin/htmleditor2/components.js";
			}
		}
		catch(e){/*_alert('ERROR: OnSave >> Load components.js');*/}

		//###########    SNIPPETS  ###########
		try
		{
			if (BXSnippetsTaskbar_need_preload && SETTINGS[pObj.pMainObj.name].arTaskbarSettings['BXSnippetsTaskbar'].show)
			{
				var oSript = document.body.appendChild(document.createElement('script'));
				oSript.src = "/bitrix/admin/htmleditor2/snippets.js";
			}
		}
		catch(e){/*_alert('ERROR: OnSave >> Load snippets.js');*/}

		//########    COMPONENTS 2.0  ########
		try{
			if (BXComponents2Taskbar_need_preload && SETTINGS[pObj.pMainObj.name].arTaskbarSettings['BXComponents2Taskbar'].show)
			{
				var oSript = document.body.appendChild(document.createElement('script'));
				oSript.src = "/bitrix/admin/htmleditor2/components2.js";
				pObj.pMainObj.LoadComponents2({func: recreateTaskbars, params: [pObj.pMainObj]})
			}
			else
			{
				recreateTaskbars(pObj.pMainObj);
			}
		}catch(e){recreateTaskbars(pObj.pMainObj); }

		var postData = oBXEditorUtils.ConvertArray2Post(temp_arTaskbarSettings, 'tskbrset');
		BXSetConfiguration(pObj.pMainObj, "taskbars", "POST", postData);
	}
}

function recreateTaskbars(pMainObj)
{
	setTimeout(function () {
			BXCreateTaskbars(pMainObj, false);
			BXRefreshTaskbars(pMainObj);
		}
	, 50);
}
</script>
<?
	$aTabs_dialog = array();
	if (!isset($_GET['light_mode']) || $_GET['light_mode'] != 'Y')
		$aTabs_dialog[] = array("DIV" => "__bx_set_1_toolbar", "TAB" => GetMessage("FILEMAN_ED_TOOLBARS"), "ICON" => "", "TITLE" => GetMessage("FILEMAN_ED_TOOLBARS_SETTINGS"));

	$aTabs_dialog[] = array("DIV" => "__bx_set_2_taskbar", "TAB" => GetMessage("FILEMAN_ED_TASKBARS"), "ICON" => "", "TITLE" => GetMessage("FILEMAN_ED_TASKBARS_SETTINGS"));
	$aTabs_dialog[] = array("DIV" => "__bx_set_3_add_props", "TAB" => GetMessage("FILEMAN_ED_ADDITIONAL_PROPS"), "ICON" => "", "TITLE" => GetMessage("FILEMAN_ED_ADDITIONAL_PROPS"));

	$tabControl_dialog = new CAdmintabControl_dialog("tabControl_dialog", $aTabs_dialog, false);
	$tabControl_dialog->Begin();
?>


<?$tabControl_dialog->BeginNextTab();?>
<div id="__bx_set_1_toolbar">&nbsp;</div>
<?$tabControl_dialog->BeginNextTab();?>
<div id="__bx_set_2_taskbar">&nbsp;</div>
<?$tabControl_dialog->BeginNextTab();?>
<div id="__bx_set_3_add_props">&nbsp;</div>
<?$tabControl_dialog->End();?>

<?elseif($name == "flash"):?>
<script>
// F L A S H
var prevsrc = "";
function OnLoad()
{
	// ************************ TAB #1: Base params *************************************
	var oDiv = document.getElementById("__bx_base_params");
	oDiv.style.padding = "5px";
	oDiv.innerHTML = '<table width="100%" border="0" height="260">'+
					'<tr>'+
						'<td align="right" width="40%">' + BX_MESS.PATH2SWF + ':</td>'+
						'<td width="60%" colspan="3">'+
							'<input type="text" size="30" value="" id="flash_src" name="src">'+
							'<input type="button" value="..." id="OpenFileBrowserWindFlash_button">'+
						'</td>'+
					'</tr>'+
					'<tr>'+
						'<td align="right">' + BX_MESS.TPropW + ':</td>'+
						'<td width="60px"><input type="text" size="3" id="flash_width"></td>'+
						'<td width="80px"align="right">' + BX_MESS.TPropH + ':</td>'+
						'<td width="130px"><input type="text" size="3" id="flash_height"></td>'+
					'</tr>'+
					'<tr>'+
						'<td align="right" valign="top"><?=GetMessage("FILEMAN_ED_IMG_PREV")?></td>'+
						'<td colspan="3">'+
							'<div id="flash_preview_cont" style="height:200px; width:95%; overflow: hidden; border: 1px #999999 solid; overflow-y: auto; overflow-x: auto;">'+
							'</div>'+
						'</td>'+
					'</tr>'+
				'</table>';

	//Attaching Events
	document.getElementById("OpenFileBrowserWindFlash_button").onclick = OpenFileBrowserWindFlash;
	var oPreviewCont = document.getElementById("flash_preview_cont");
	document.getElementById("flash_src").onchange = function(){Flash_Reload(oPreviewCont, document.getElementById("flash_src").value, 150, 150)};

	// ************************ TAB #2: Additional params ***********************************
	var oDiv = document.getElementById("__bx_additional_params");
	oDiv.style.padding = "5px";
	oDiv.innerHTML = '<table width="100%" border="0" height="260">'+
				'<tr>'+
					'<td align="right" width="40%" colspan="2">' + BX_MESS.SWF_ID + ':</td>'+
					'<td width="60%" colspan="2">'+
						'<input type="text" size="30" value="" id="_flash_id">'+
					'</td>'+
				'</tr>'+
				'<tr>'+
					'<td align="right" colspan="2">' + BX_MESS.SWF_TITLE + ':</td>'+
					'<td colspan="2">'+
						'<input type="text" size="30" value="" id="_flash_title">'+
					'</td>'+
				'</tr>'+
				'<tr>'+
					'<td align="right" colspan="2">' + BX_MESS.SWF_CLASSNAME + ':</td>'+
					'<td colspan="2">'+
						'<input type="text" size="30" value="" id="_flash_classname">'+
					'</td>'+
				'</tr>'+
				'<tr>'+
					'<td align="right" colspan="2">' + BX_MESS.TPropStyle + '</td>'+
					'<td colspan="2">'+
						'<input type="text" size="30" value="" id="_flash_style">'+
					'</td>'+
				'</tr>'+
				'<tr>'+
					'<td align="right" colspan="2">' + BX_MESS.SWF_QUALITY + ':</td>'+
					'<td colspan="2">'+
						'<select id="_flash_quality" style="width:100px">'+
							'<option value=""></option>'+
							'<option value="low">low</option>'+
							'<option value="medium">medium</option>'+
							'<option value="high">high</option>'+
							'<option value="autolow">autolow</option>'+
							'<option value="autohigh">autohigh</option>'+
							'<option value="best">best</option>'+
						'</select>'+
					'</td>'+
				'</tr>'+
				'<tr>'+
					'<td align="right" colspan="2">' + BX_MESS.SWF_WMODE + ':</td>'+
					'<td colspan="2">'+
						'<select id="_flash_wmode" style="width:100px">'+
							'<option value=""></option>'+
							'<option value="window">window</option>'+
							'<option value="opaque">opaque</option>'+
							'<option value="transparent">transparent</option>'+
						'</select>'+
					'</td>'+
				'</tr>'+
				'<tr>'+
					'<td align="right" colspan="2">' + BX_MESS.SWF_SCALE + ':</td>'+
					'<td colspan="2">'+
						'<select id="_flash_scale"style="width:100px">'+
							'<option value=""></option>'+
							'<option value="showall">showall</option>'+
							'<option value="noborder">noborder</option>'+
							'<option value="exactfit">exactfit</option>'+
						'</select>'+
					'</td>'+
				'</tr>'+
				'<tr>'+
					'<td align="right" colspan="2">' + BX_MESS.SWF_SALIGN + ':</td>'+
					'<td colspan="2">'+
						'<select id="_flash_salign" style="width:100px">'+
							'<option value=""></option> '+
							'<option value="left">left</option> '+
							'<option value="top">top</option> '+
							'<option value="right">right</option> '+
							'<option value="bottom">bottom</option> '+
							'<option value="top left">top left</option>'+
							'<option value="top right">top right</option>'+
							'<option value="bottom left">bottom left</option>'+
							'<option value="bottom right">bottom right</option>'+
						'</select>'+
					'</td>'+
				'</tr>'+
				'<tr>'+
					'<td align="right" colspan="2">' + BX_MESS.SWF_AUTOPLAY + ':</td>'+
					'<td colspan="2">'+
						'<input type="checkbox" value="" id="_flash_autoplay">'+
					'</td>'+
				'</tr>'+
				'<tr>'+
					'<td align="right" colspan="2">' + BX_MESS.SWF_LOOP + ':</td>'+
					'<td colspan="2">'+
						'<input type="checkbox" value="" id="_flash_loop">'+
					'</td>'+
				'</tr>'+
				'<tr>'+
					'<td align="right" colspan="2">' + BX_MESS.SWF_SHOW_MENU + ':</td>'+
					'<td colspan="2">'+
						'<input type="checkbox" value="" id="_flash_showmenu">'+
					'</td>'+
				'</tr>'+
			'</table>';

	// ************************ TAB #3: HTML Code *************************************
	var oDiv = document.getElementById("__bx_code");
	oDiv.style.padding = "5px";
	oDiv.innerHTML = '<table width="100%" border="0" height="260">'+
					'<tr>'+
						'<td align="left" width="100%"><?=GetMessage("FILEMAN_ED_SWF_HTML_CODE")?>:<br />'+
							'<textarea id="bx_flash_html_code" cols="49" rows="12"></textarea>'+
						'</td>'+
					'</tr>'+
				'</table>';

	var applyParams = function(arParams)
	{
		var re, _p, i, l;
		for(var i in pObj.bx_swf_arParams)
		{
			_p = pObj.bx_swf_arParams[i].p;
			if (!_p) continue;

			if (_p.type.toLowerCase() == 'checkbox')
				_p.checked = (arParams[i]);
			else
				_p.value = arParams[i] || '';
		}
	};

	pObj.bx_swf_source = document.getElementById("bx_flash_html_code");
	pObj.bx_swf_source.onblur = function()
	{
		var s = this.value;
		if (s.length <= 0)
			return;
		var flash_parser = function(str, attr)
		{
			if (attr.indexOf('.swf') === false || attr.indexOf('flash') === false) // not a flash
				return;

			attr = attr.replace(/[\r\n]+/ig, ' ');
			attr = attr.replace(/\s+/ig, ' ');
			attr = attr.trim();

			var _params = ['src', 'width', 'height', 'id', 'title', 'class', 'style', 'quality', 'wmode', 'scale', 'salign', 'autoplay', 'loop', 'showmenu' ];
			var arParams = {};
			var re, _p, i, l;
			for (i = 0, l = _params.length; i < l; i++)
			{
				_p = _params[i];
				re = new RegExp(_p+'\\s*=\\s*("|\')([^\\1]+?)\\1', "ig");
				attr = attr.replace(re, function(s, b1, value){arParams[_p] = value;});
			}
			applyParams(arParams);
		};
		s = s.replace(/<embed([^>]*?)>[^>]*?<\/embed>/ig, flash_parser);
		Flash_Reload(oPreviewCont, document.getElementById("flash_src").value, 150, 150);
	};

	pObj.bx_swf_arParams = {
		src : {p : document.getElementById("flash_src")},
		width : {p : document.getElementById("flash_width")},
		height : {p : document.getElementById("flash_height")},
		id : {p : document.getElementById("_flash_id")},
		title : {p : document.getElementById("_flash_title")},
		classname : {p : document.getElementById("_flash_classname")},
		style : {p : document.getElementById("_flash_style")},
		quality : {p : document.getElementById("_flash_quality")},
		wmode : {p : document.getElementById("_flash_wmode")},
		scale : {p : document.getElementById("_flash_scale")},
		salign : {p : document.getElementById("_flash_salign")},
		autoplay : {p : document.getElementById("_flash_autoplay")},
		loop : {p : document.getElementById("_flash_loop")},
		showmenu : {p : document.getElementById("_flash_showmenu")}
	};


	pElement = pObj.pMainObj.GetSelectionObject();
	if(pElement && pElement.getAttribute && pElement.getAttribute("__bxtagname") == "flash") // Edit flash
	{
		pObj.bEdit = true;
		var id  = pElement.id;
		var id  = pElement.id;
		pObj.bx_swf_source.disabled = true;
		oDialogTitle.innerHTML = BX_MESS.FLASH_MOV;
		applyParams(pObj.pMainObj.arFlashParams[id]);
		Flash_Reload(oPreviewCont, document.getElementById("flash_src").value, 150, 150);
	}
	else // insert flash
	{
		pObj.bEdit = false;
		oDialogTitle.innerHTML = '<?=GetMessage("FILEMAN_ED_FLASH")?>';
	}
}

function SetUrl(filename, path, site)
{
	var url = (path == '/' ? '' : path) + '/'+filename;
	document.getElementById("flash_src").value = url;
	if(document.getElementById("flash_src").onchange)
		document.getElementById("flash_src").onchange();
}

function OnSave()
{
	if (!pObj.bx_swf_arParams.src.p.value)
		return;
	pObj.pMainObj.bSkipChanges = true;
	BXSelectRange(oPrevRange,pObj.pMainObj.pEditorDocument,pObj.pMainObj.pEditorWindow);
	var html;
	if (pObj.bEdit)
	{
		var id = pElement.id;
		var ar = pObj.pMainObj.arFlashParams[id];
		for(var i in pObj.bx_swf_arParams)
		{
			_p = pObj.bx_swf_arParams[i].p;
			if (!_p) continue;
			if (_p.type.toLowerCase() == 'checkbox' && _p.checked)
				ar[i] = true;
			else if(_p.type.toLowerCase() != 'checkbox' && _p.value.length > 0)
				ar[i] = _p.value;
		}

		pElement.style.width = (parseInt(ar.width) || 50) + 'px';
		pElement.style.height = (parseInt(ar.height) || 25) + 'px';
		pObj.pMainObj.bSkipChanges = false;
		return;
	}


	if (pObj.bx_swf_source.value.length > 0)
	{
		html = pObj.bx_swf_source.value;
	}
	else
	{
		html = '<EMBED ';
		for(var i in pObj.bx_swf_arParams)
		{
			_p = pObj.bx_swf_arParams[i].p;
			if (!_p) continue;

			if (_p.type.toLowerCase() == 'checkbox' && _p.checked)
				html += i + '="true" ';
			else if(_p.type.toLowerCase() != 'checkbox' && _p.value.length > 0)
				html += i + '="' + _p.value + '" ';
		}
		html += 'type = "application/x-shockwave-flash" '+
		'pluginspage = "http://www.macromedia.com/go/getflashplayer" '+
		'></EMBED>';
	}

	var html = pObj.pMainObj.pParser.SystemParse(html);
	pObj.pMainObj.insertHTML(html);
	pObj.pMainObj.bSkipChanges = false;
}

</script>

<?
CAdminFileDialog::ShowScript(Array
	(
		"event" => "OpenFileBrowserWindFlash",
		"arResultDest" => Array("FUNCTION_NAME" => "SetUrl"),
		"arPath" => Array("SITE" => $_GET["site"], "PATH" =>(strlen($str_FILENAME)>0 ? GetDirPath($str_FILENAME) : '')),
		"select" => 'F',// F - file only, D - folder only,
		"operation" => 'O',// O - open, S - save
		"showUploadTab" => true,
		"showAddToMenuTab" => false,
		"fileFilter" => 'swf',//'' - don't shjow select, 'image' - only images; "ext1,ext2" - Only files with ext1 and ext2 extentions;
		"allowAllFiles" => true,
		"SaveConfig" => true
	)
);


$aTabs_dialog = array(
array("DIV" => "__bx_base_params", "TAB" => GetMessage("FILEMAN_ED_BASE_PARAMS"), "ICON" => "", "TITLE" => GetMessage("FILEMAN_ED_BASE_PARAMS")),
array("DIV" => "__bx_additional_params", "TAB" => GetMessage("FILEMAN_ED_ADD_PARAMS"), "ICON" => "", "TITLE" => GetMessage("FILEMAN_ED_ADD_PARAMS")),
array("DIV" => "__bx_code", "TAB" => GetMessage("FILEMAN_ED_HTML_CODE"), "ICON" => "", "TITLE" => GetMessage("FILEMAN_ED_SWF_HTML_CODE")),
);
$tabControl_dialog = new CAdmintabControl_dialog("tabControl_dialog", $aTabs_dialog, false);

$tabControl_dialog->Begin();?>
<?$tabControl_dialog->BeginNextTab();?>
<div id="__bx_base_params"></div>
<?$tabControl_dialog->BeginNextTab();?>
<div id="__bx_additional_params"></div>
<?$tabControl_dialog->BeginNextTab();?>
<div id="__bx_code"></div>
<?$tabControl_dialog->End();?>

<?elseif($name == "edit_hbf"):?>
<script>
function OnLoad()
{
	oDialogTitle.innerHTML = '<?=GetMessage("FILEMAN_ED_EDIT_HBF")?>';
	// ************************ TAB #1: HEAD *************************************
	var oDiv = document.getElementById("__bx_head");
	oDiv.style.padding = "5px";
	var newCell = titleTable = oDiv.getElementsByTagName("TABLE")[0].rows[1].insertCell(1);
	newCell.style.paddingRight = (jsUtils.IsIE() ? "12px" : "2px");
	var _insertDefaultImg = pObj.pMainObj.CreateElement("DIV", {title: '<?=GetMessage("FILEMAN_ED_RESTORE")?>', className: "iconkit_c", onclick: insertDefault_head}, {backgroundPosition: "-162px -43px", width: "16px", height: "16px"});
	newCell.appendChild(_insertDefaultImg);

	var oTA = pObj.pMainObj.CreateElement("TEXTAREA", {id: "__bx_head_ta"}, {width: "100%", height: "280px"});
	oTA.value = pObj.pMainObj._head + pObj.pMainObj._body;
	oDiv.appendChild(oTA);

	// ************************ TAB #3: Footer ***********************************
	var oDiv = document.getElementById("__bx_footer");
	oDiv.style.padding = "5px";

	var newCell = titleTable = oDiv.getElementsByTagName("TABLE")[0].rows[1].insertCell(1);
	newCell.style.paddingRight = (jsUtils.IsIE() ? "12px" : "2px");
	var _insertDefaultImg = pObj.pMainObj.CreateElement("DIV", {title: '<?=GetMessage("FILEMAN_ED_INSERT_DEF")?>', className: "iconkit_c", onclick : insertDefault_footer}, {backgroundPosition: "-162px -43px", width: "16px", height: "16px"});
	newCell.appendChild(_insertDefaultImg);

	var oTA = pObj.pMainObj.CreateElement("TEXTAREA", {id: "__bx_footer_ta"}, {width: "100%", height: "280px"});
	oTA.value = pObj.pMainObj._footer;
	oDiv.appendChild(oTA);
}

function OnSave()
{
	document.getElementById("__bx_head_ta").value.replace(/(^[\s\S]*?)(<body.*?>)/i, "");
	pObj.pMainObj._head = RegExp.$1;
	pObj.pMainObj._body = RegExp.$2;

	pObj.pMainObj._footer = document.getElementById("__bx_footer_ta").value;
	pObj.pMainObj.updateBody();
}

function insertDefault_head()
{
	if (!confirm("<?=GetMessage("FILEMAN_ED_CONFIRM_HEAD")?>"))
		return;

	var oTA = document.getElementById("__bx_head_ta");
	var s60 = String.fromCharCode(60);
	var s62 = String.fromCharCode(62);
	oTA.value = s60 + '?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?'+s62+'<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">'+"\n"+
	'<html>'+"\n"+
	'<head>'+"\n"+
	'<meta http-equiv="Content-Type" content="text/html; charset='+s60+'?echo LANG_CHARSET;?'+s62+'">'+"\n"+
	s60+'?$APPLICATION->ShowMeta("keywords")?'+s62+"\n"+
	s60+'?$APPLICATION->ShowMeta("description")?'+s62+"\n"+
	'<title>'+s60+'?$APPLICATION->ShowTitle()?'+s62+'</title>'+"\n"+
	s60+'?$APPLICATION->ShowCSS();?'+s62+"\n"+
	s60+'?$APPLICATION->ShowHeadStrings()?'+s62+"\n"+
	s60+'?$APPLICATION->ShowHeadScripts()?'+s62+"\n"+
	"</head>\n"+
	'<body>';
}

function insertDefault_footer()
{
	if (!confirm("<?=GetMessage("FILEMAN_ED_CONFIRM_FOOTER")?>"))
		return;
	var oTA = document.getElementById("__bx_footer_ta");
	oTA.value = "</body>\n</html>";
}
</script>
<?
$aTabs_dialog = array(
array("DIV" => "__bx_head", "TAB" => GetMessage("FILEMAN_ED_TOP_AREA"), "ICON" => "", "TITLE" => GetMessage("FILEMAN_ED_EDIT_HEAD")),
array("DIV" => "__bx_footer", "TAB" => GetMessage("FILEMAN_ED_BOTTOM_AREA"), "ICON" => "", "TITLE" => GetMessage("FILEMAN_ED_EDIT_FOOTER")),
);
$tabControl_dialog = new CAdmintabControl_dialog("tabControl_dialog", $aTabs_dialog, false);

$tabControl_dialog->Begin();?>
<?$tabControl_dialog->BeginNextTab();?>
<div id="__bx_head"></div>
<?$tabControl_dialog->BeginNextTab();?>
<div id="__bx_footer"></div>
<?$tabControl_dialog->End();?>
<?endif;?>

</td></tr>
<?if(!isset($not_use_default) || $not_use_default!='Y'):?>
	<tr id="buttonsSec">
	<td align="center" valign="top">
	<div>
		<input id="saveBut" type="button" value="<?echo GetMessage("FILEMAN_ED_SAVE")?>">
		<input id="cancelBut" type="button" value="<?echo GetMessage("FILEMAN_ED_CANC")?>" onclick="pObj.Close();">
		<?if($name=="settings"):?>
			<input id="restoreDefault" type="button" value="<?echo GetMessage('FILEMAN_ED_RESTORE');?>">
		<?endif;?>
	</div>
	</td>
	</tr>
<?endif?>
</table>
<script>
<?if(!isset($not_use_default) || $not_use_default!='Y'):?>
	document.getElementById("buttonsSec").style.height = (jsUtils.IsIE()) ? 25 : 45;
	document.getElementById('saveBut').onclick = __OnSave;
<?endif?>
__OnLoad();
</script>
</form>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");?>
