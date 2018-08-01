<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/fileman/prolog.php");

if (!check_bitrix_sessid())
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/fileman/include.php");
IncludeModuleLangFile(__FILE__);

define("FROMDIALOGS", true);
?>
<script>
var iNoOnSelectionChange = 1;
var iNoOnChange = 2;
</script>

<?if($name == "anchor"):?>
<script>
var pElement = null;
function OnLoad()
{
	pElement = pObj.pMainObj.GetSelectionObject();
	window.oBXEditorDialog.SetTitle('<?=GetMessage("FILEMAN_ED_LINK_TITLE")?>');

	var el = BX("anchor_value");
	if(pElement && pElement.getAttribute("__bxtagname") == "anchor")
	{
		var val = BXUnSerialize(pElement.getAttribute("__bxcontainer"));
		el.value = pObj.pMainObj.pParser.GetSetAnchorName(val.html);
	}
	else
	{
		el.value = "";
	}
	el.focus();
	window.oBXEditorDialog.adjustSizeEx();
}

function OnSave()
{
	BXSelectRange(oPrevRange, pObj.pMainObj.pEditorDocument, pObj.pMainObj.pEditorWindow);
	pElement = pObj.pMainObj.GetSelectionObject();
	pObj.pMainObj.bSkipChanges = true;
	var anchor_value = BX("anchor_value");
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
<?ob_start();?>
<div style="padding: 5px; margin-bottom: 10px;">
<?= GetMessage("FILEMAN_ED_ANCHOR_NAME")?>&nbsp;<input type="text" size="25" value="" id="anchor_value" />
</div>
<?$dialogHTML = ob_get_contents(); ob_end_flush();?>

<?elseif($name == "link"):?>
<script>
var pElement = null;
var curLinkType = 't1';
function OnLoad()
{
	var bWasSelectedElement = false;

	pElement = pObj.pMainObj.GetSelectionObject();
	if (pElement && pElement.nodeName && pElement.nodeName.toUpperCase() != 'A')
	{
		var nodeName = pElement.nodeName.toUpperCase();
		if (nodeName == 'IMG')
			bWasSelectedElement = true;

		pElement = BXFindParentByTagName(pElement, 'A');
	}

	// Set title
	window.oBXEditorDialog.SetTitle(pElement ? '<?=GetMessage("FILEMAN_ED_LE_TITLE")?>' : '<?=GetMessage("FILEMAN_ED_LN_TITLE")?>');

	if (BX("OpenFileBrowserWindLink_button"))
		BX("OpenFileBrowserWindLink_button").onclick = OpenFileBrowserWindFile;

	// Set styles
	var
		arStFilter = ['A', 'DEFAULT'], i, j,
		elStyles = BX("bx_classname"),
		arStyles;

	for(i = 0; i < arStFilter.length; i++)
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

	// Fetch anchors
	var
		pAnchorSelect = BX('bx_url_3'),
		i, l, anc, ancName,
		arImgs = pObj.pMainObj.pEditorDocument.getElementsByTagName('IMG');

	for(i = 0, l = arImgs.length; i < l; i++)
	{
		if(arImgs[i].getAttribute("__bxtagname") && arImgs[i].getAttribute("__bxtagname") == "anchor")
		{
			anc = BXUnSerialize(arImgs[i].getAttribute("__bxcontainer"));
			ancName = pObj.pMainObj.pParser.GetSetAnchorName(anc.html);

			if (ancName.length > 0)
				pAnchorSelect.options.add(new Option(ancName, '#'+ancName, false, false));
		}
	}

	if (pAnchorSelect.options.length <= 0)
	{
		pAnchorSelect.options.add(new Option('<?= GetMessage("FILEMAN_ED_NOANCHORS")?>', '', true, true));
		pAnchorSelect.disabled = true;
	}

	var tip = 1;
	var selectedText = false;
	if(pElement) /* Link selected*/
	{
		oPrevRange = pObj.pMainObj.SelectElement(pElement);
		if (pElement.childNodes && pElement.childNodes.length == 1 && pElement.childNodes[0].nodeType == 3)
			selectedText = pElement.innerHTML;

		var href = pElement.getAttribute("href", 2), el;
		if(href.substring(0, 7).toLowerCase() == 'mailto:') // email
		{
			tip = 4;
			BX("bx_url_4").value = href.substring('mailto:'.length);
		}
		else if(href.substr(0, 1) == '#') // anchor
		{
			BX("bx_url_3").value = href;
			if(BX("bx_url_3").value == href)
			{
				tip = 3;
			}
			else
			{
				tip = 1;
				BX("bx_url_1").value = href;
			}
		}
		else if (href.indexOf("://") !== -1 || href.substr(0, 'www.'.length) == 'www.')
		{
			tip = 2;

			// Fix link in statistic
			if(href.substr(0, '/bitrix/redirect.php'.length) == '/bitrix/redirect.php')
			{
				BX("bx_fixstat").checked = true;
				var sParams = href.substring('/bitrix/redirect.php'.length);

				var __ExtrParam = function (p, s)
				{
					var pos = s.indexOf(p + '=');
					if(pos < 0)
						return '';
					var pos2 = s.indexOf('&', pos + p.length+1);
					if(pos2 < 0)
						s = s.substring(pos + p.length + 1);
					else
						s = s.substr(pos+p.length+1, pos2 - pos - 1 - p.length);
					return unescape(s);
				};

				BX("event1").value = __ExtrParam('event1', sParams);
				BX("event2").value = __ExtrParam('event2', sParams);
				BX("event3").value = __ExtrParam('event3', sParams);

				href = __ExtrParam('goto', sParams);
			}

			if (href.substr(0, 'www.'.length) == 'www.')
				href = "http://" + href;

			var sProt = href.substr(0, href.indexOf("://") + 3);

			BX("bx_url_type").value = sProt;
			if (BX("bx_url_type").value != sProt)
				BX("bx_url_type").value = '';

			BX("bx_url_2").value = href.substring(href.indexOf("://") + 3);
		}
		else // link to page on server
		{
			BX("bx_url_1").value = href;
		}

		if(pElement.className)
		{
			var pClassSel = BX("bx_classname");
			pClassSel.value = pElement.className;
			if (pClassSel.value != pElement.className) // Add class to select if it's not exsist here
				pClassSel.options.add(new Option(pElement.className, pElement.className, true, true));
		}

		BX("bx_targ_list").value = pElement.target.toLowerCase() || '';
		BX("__bx_id").value = pElement.id;
		BX("BXEditorDialog_title").value = pElement.title;

		var rel = pElement.getAttribute('rel');
		if (pElement.getAttribute('__bxnoindex') || rel == 'nofollow')
		{
			BX("bx_noindex").checked = true;
			BX("bx_link_rel").disabled = true;
		}

		if (rel)
			BX("bx_link_rel").value = rel;
	}
	else if (!bWasSelectedElement)/* NO selected link*/
	{
		// Get selected text
		if (oPrevRange.startContainer && oPrevRange.endContainer) // DOM Model
		{
			if (oPrevRange.startContainer == oPrevRange.endContainer && (oPrevRange.endContainer.nodeType == 3 || oPrevRange.endContainer.nodeType == 1))
			{
				selectedText = oPrevRange.startContainer.textContent.substring(oPrevRange.startOffset, oPrevRange.endOffset) || '';
			}
		}
		else // IE
		{
			if (oPrevRange.text == oPrevRange.htmlText)
				selectedText = oPrevRange.text || '';
		}
	}

	if (selectedText === false)
		BX('bx_link_text_tr').style.display = "none";
	else
		BX('bx_link_text').value = selectedText || '';

	BX('bx_link_type').value = 't' + tip;
	ChangeLinkType();
}

function OnSave()
{
	var href='', target='', bText = BX('bx_link_text_tr').style.display != "none";

	switch(BX('bx_link_type').value)
	{
		case 't1':
			href = BX('bx_url_1').value;
			break;
		case 't2':
			href = BX('bx_url_2').value;

			if (BX("bx_url_type").value && href.indexOf('://') == -1)
				href = BX("bx_url_type").value + href;

			if(BX("bx_fixstat").checked)
				href = '/bitrix/redirect.php?event1=' + escape(BX("event1").value) + '&event2=' + escape(BX("event2").value) + '&event3=' + escape(BX("event3").value) + '&goto=' + escape(href);
			break;
		case 't3':
			href = BX('bx_url_3').value;
			break;
		case 't4':
			if(BX('bx_url_4').value)
				href = 'mailto:' + BX('bx_url_4').value;
			break;
	}

	BXSelectRange(oPrevRange, pObj.pMainObj.pEditorDocument, pObj.pMainObj.pEditorWindow);
	pObj.pMainObj.bSkipChanges = true;

	if(href.length > 0)
	{
		var arlinks = [], link;

		if (window.pElement)
		{
			arlinks[0] = pElement;
		}
		else
		{
			var sRand = '#'+Math.random().toString().substring(5);
			if (bText) // Simple case
			{
				pObj.pMainObj.insertHTML('<a id="bx_lhe_' + sRand + '">#</a>');
				arlinks[0] = pObj.pMainObj.pEditorDocument.getElementById('bx_lhe_' + sRand);
				arlinks[0].removeAttribute("id");
			}
			else
			{
				pObj.pMainObj.pEditorDocument.execCommand('CreateLink', false, sRand);
				var arLinks_ = pObj.pMainObj.pEditorDocument.getElementsByTagName('A');
				for(var i = 0; i < arLinks_.length; i++)
					if(arLinks_[i].getAttribute('href', 2) == sRand)
						arlinks.push(arLinks_[i]);
			}
		}

		for (var i = 0, l = arlinks.length; i < l; i++)
		{
			link = arlinks[i];

			SAttr(link, 'href', href);
			SAttr(link, '__bxhref', href);
			SAttr(link, 'title', BX("BXEditorDialog_title").value);
			SAttr(link, 'className', BX("bx_classname").value);
			SAttr(link, 'target', BX("bx_targ_list").value);
			SAttr(link, 'id', BX("__bx_id").value);
			SAttr(link, '__bxnoindex', BX("bx_noindex").checked ? "Y" : "");
			SAttr(link, 'rel', BX("bx_link_rel").value);

			// Add text
			if (bText)
				link.innerHTML = BX.util.htmlspecialchars(BX('bx_link_text').value || href);
		}
	}

	pObj.pMainObj.bSkipChanges = false;
	pObj.pMainObj.OnChange("link");
}

function showAddSect()
{
	var pCont = BX('bx_link_dialog_tbl').parentNode;
	var bShow = pCont.className.indexOf('bx-link-simple') == -1;

	if (bShow)
	{
		BX.addClass(pCont, 'bx-link-simple');
	}
	else
	{
		BX.removeClass(pCont, 'bx-link-simple');
	}
	window.oBXEditorDialog.adjustSizeEx();
}

function ChangeLinkType()
{
	var
		pUrl,
		pTbl = BX('bx_link_dialog_tbl'),
		val = BX('bx_link_type').value;

	if (curLinkType == 't1' && val == 't2')
	{
		var url1 = BX('bx_url_1').value;
		if (url1 != '' && url1.indexOf('://') != -1)
		{
			BX('bx_url_2').value = url1.substr(url1.indexOf('://') + 3);
			BX('bx_url_type').value = url1.substr(0, url1.indexOf('://') + 3);
		}
	}
	curLinkType = val;

	if(pUrl = BX('bx_url_' + val.substr(1)))
		setTimeout(function(){pUrl.focus();}, 300);

	pTbl.className = ("bx-link-dialog-tbl bx--t1 bx--t2 bx--t3 bx--t4 bx-only-" + val).replace(' bx--' + val, '');
	window.oBXEditorDialog.adjustSizeEx();
	// TODO: remember last choise of link-type and additional sect position
}

function ChangeFixStat()
{
	var bFix = BX("bx_fixstat").checked;
	BX("bx_fixstat_div").style.display = bFix ? 'block' : 'none';
	BX("event1").disabled = BX("event2").disabled = BX("event3").disabled = !bFix;
	window.oBXEditorDialog.adjustSizeEx();
}

function SetUrl(filename, path, site)
{
	var url, pInput = BX("bx_url_1"), pText = BX("bx_link_text"), pTitle = BX("BXEditorDialog_title");
	if (typeof filename == 'object') // Using medialibrary
	{
		url = filename.src;
		//if (pText.value == "" && filename.name)
			pText.value = filename.name;
		//if (pTitle.value == "")
			pTitle.value = filename.description || filename.name;
	}
	else // Using file dialog
	{
		url = (path == '/' ? '' : path) + '/' + filename;
	}

	pInput.value = url;
	pInput.focus();
	pInput.select();
}
</script>

<?ob_start();?>
<table class="bx-link-dialog-tbl bx--t1 bx--t2 bx--t3 bx--t4" id="bx_link_dialog_tbl">
	<tr class="bx-link-type">
		<td class="bx-par-title"><label for="bx_link_type"><?= GetMessage("FILEMAN_ED_LINK_TYPE")?></label></td>
		<td class="bx-par-val">
			<select id='bx_link_type' onchange="ChangeLinkType();">
				<option value='t1'><?= GetMessage("FILEMAN_ED_LINK_TYPE1")?></option>
				<option value='t2'><?= GetMessage("FILEMAN_ED_LINK_TYPE2")?></option>
				<option value='t3'><?= GetMessage("FILEMAN_ED_LINK_TYPE3")?></option>
				<option value='t4'><?= GetMessage("FILEMAN_ED_LINK_TYPE4")?></option>
			</select>
		</td>
	</tr>

	<tr><td colSpan="2" class="bx-link-sep"></td></tr>

	<tr id="bx_link_text_tr">
		<td class="bx-par-title"><label for="bx_link_text"><?= GetMessage("FILEMAN_LINK_TEXT")?>:</label></td>
		<td class="bx-par-val"><input type="text" size="30" value="" id="bx_link_text" /></td>
	</tr>

	<tr class="bx-link-t1">
		<td class="bx-par-title"><label for="bx_url_1"><?= GetMessage("FILEMAN_ED_LINK_DOC")?>:</label></td>
		<td class="bx-par-val">
			<input type="text" size="30" value="" id="bx_url_1" style="float: left;">
			<?
			CMedialib::ShowBrowseButton(
				array(
					'value' => '...',
					'event' => 'OpenFileBrowserWindFile',
					'id' => 'OpenFileBrowserWindLink_button',
					'MedialibConfig' => array("arResultDest" => Array("FUNCTION_NAME" => "SetUrl")),
					'useMLDefault' => false
				)
			);
			?>
		</td>
	</tr>

	<!-- Link to external site -->
	<tr class="bx-link-t2">
		<td class="bx-par-title"><label for="bx_url_2"><?= GetMessage("FILEMAN_ED_LINK_DOC")?>:</label></td>
		<td class="bx-par-val">
			<select id='bx_url_type'>
				<option value="http://">http://</option>
				<option value="ftp://">ftp://</option>
				<option value="https://">https://</option>
				<option value=""></option>
			</select>
			<input type="text" size="25" value="" id="bx_url_2">
		</td>
	</tr>

	<tr class="bx-link-t2">
		<td style="text-align: right; vertical-align: top;"><input type="checkbox" id="bx_fixstat" value="" onclick="ChangeFixStat();"></td>
		<td>
			<label for="bx_fixstat" style="display: block; margin-top: 3px;"><?= GetMessage("FILEMAN_ED_LINK_STAT")?></label>
			<div id="bx_fixstat_div" style="margin: 8px 5px; display: none;">
				<label for="event1">Event1:</label> <input type="event1" id="event1" size="10" value=""><br/>
				<label for="event2">Event2:</label> <input type="event2" id="event2" size="10" value=""><br/>
				<label for="event3">Event3:</label> <input type="event3" id="event3" size="10" value=""><br/>
			</div>
		</td>
	</tr>

	<!-- anchor -->
	<tr class="bx-link-t3">
		<td class="bx-par-title"><label for="bx_url_3"><?= GetMessage("FILEMAN_ED_LINK_ACH")?></label></td>
		<td class="bx-par-val">
			<select id="bx_url_3"></select>
		</td>
	</tr>

	<!-- email -->
	<tr class="bx-link-t4">
		<td class="bx-par-title"><label for="bx_url_4">EMail:</label></td>
		<td class="bx-par-val">
			<input type="text" size="30" value="" id="bx_url_4">
		</td>
	</tr>

	<tr class="bx-header"><td colSpan="2"><a  class="bx-adv-link" onclick="showAddSect(); return false;" href="javascript: void(0);"><?= GetMessage("FILEMAN_ED_ADDITIONAL")?> <span>(<?= GetMessage("FILEMAN_ED_HIDE")?>)</span></a></td></tr>

	<tr id="bx_target_row" class="bx-adv bx-hide-in-t3 bx-hide-in-t4">
		<td class="bx-par-title"><label for="bx_targ_list"><?= GetMessage("FILEMAN_ED_LINK_WIN")?>:</label></td>
		<td class="bx-par-val">
			<select id='bx_targ_list'>
				<option value=""> - <?= GetMessage("FILEMAN_NO_VAL")?> -</option>
				<option value="_blank"><?= GetMessage("FILEMAN_ED_LINK_WIN_BLANK")?></option>
				<option value="_parent"><?= GetMessage("FILEMAN_ED_LINK_WIN_PARENT")?></option>
				<option value="_self"><?= GetMessage("FILEMAN_ED_LINK_WIN_SELF")?></option>
				<option value="_top"><?= GetMessage("FILEMAN_ED_LINK_WIN_TOP")?></option>
			</select>
		</td>
	</tr>
	<tr class="bx-adv bx-hide-in-t3 bx-hide-in-t4">
		<td class="bx-par-title"><input type="checkbox" value="Y" id="bx_noindex" onclick="var rel = BX('bx_link_rel'); if (this.checked){rel.value='nofollow'; rel.disabled=true;}else{rel.disabled=false;rel.value='';}" /></td>
		<td class="bx-par-val"><label for="bx_noindex"><?= GetMessage("FILEMAN_ED_LINK_NOINDEX")?></label></td>
	</tr>
	<tr class="bx-adv">
		<td class="bx-par-title"><label for="BXEditorDialog_title"><?= GetMessage("FILEMAN_ED_LINK_ATITLE")?></label></td>
		<td class="bx-par-val">
			<input type="text" size="30" value="" id="BXEditorDialog_title">
		</td>
	</tr>
	<tr class="bx-adv">
		<td class="bx-par-title"><label for="bx_classname"><?= GetMessage("FILEMAN_ED_STYLE")?>:</label></td>
		<td class="bx-par-val">
			<select id='bx_classname'><option value=""> - <?= GetMessage("FILEMAN_NO_VAL")?> -</option></select>
		</td>
	</tr>
	<tr class="bx-adv">
		<td class="bx-par-title"><label for="__bx_id">ID:</label></td>
		<td class="bx-par-val"><input type="text" size="30" value="" id="__bx_id" /></td>
	</tr>
	<tr class="bx-adv">
		<td class="bx-par-title"><label for="bx_link_rel"><?= GetMessage("FILEMAN_REL")?>:</label></td>
		<td class="bx-par-val"><input type="text" size="30" value="" id="bx_link_rel" /></td>
	</tr>
</table>
<?$dialogHTML = ob_get_contents(); ob_end_flush();?>

<?
CAdminFileDialog::ShowScript(Array
	(
		"event" => "OpenFileBrowserWindFile",
		//"arResultDest" => Array("ELEMENT_ID" => "bx_url_1"),
		"arResultDest" => Array("FUNCTION_NAME" => "SetUrl"),
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

<?elseif($name == "image"):?>
<script>
var pElement = null;
function OnLoad()
{
	pElement = pObj.pMainObj.GetSelectionObject();
	var
		preview = BX("bx_img_preview"),
		pWidth = BX("bx_width"),
		pHeight = BX("bx_height");

	preview.onload = PreviewOnLoad;
	if(!pElement || pElement.nodeName.toUpperCase() != 'IMG' || pElement.getAttribute("__bxtagname"))
	{
		pElement = null;
		window.oBXEditorDialog.SetTitle('<?=GetMessage("FILEMAN_ED_NEW_IMG")?>');
	}
	else
	{
		var w = parseInt(pElement.style.width || pElement.getAttribute('width') || pElement.offsetWidth);
		var h = parseInt(pElement.style.height || pElement.getAttribute('height') || pElement.offsetHeight);
		if (w && h)
		{
			pObj.iRatio = w / h; // Remember proportion
			pObj.curWidth = pWidth.value = w;
			pObj.curHeight = pHeight.value = h;
		}

		window.oBXEditorDialog.SetTitle('<?=GetMessage("FILEMAN_ED_EDIT_IMG")?>');
		BX("bx_src").value = GAttr(pElement, "src");
		BX("bx_img_title").value = GAttr(pElement, "title");
		BX("bx_alt").value = GAttr(pElement, "alt");
		BX("bx_border").value = GAttr(pElement, "border");
		BX("bx_align").value = GAttr(pElement, "align");
		BX("bx_hspace").value = GAttr(pElement, "hspace");
		BX("bx_vspace").value = GAttr(pElement, "vspace");

		preview.style.display = "";
		pObj.prevsrc = preview.src = GAttr(pElement, "src");
		preview.alt=BX("bx_alt").value;
		preview.border=BX("bx_border").value;
		preview.align=BX("bx_align").value;
		preview.hspace=BX("bx_hspace").value;
		preview.vspace=BX("bx_vspace").value;

		preview.onload = function(){PreviewReload(); preview.onload = PreviewOnLoad;};
	}

	if (BX("OpenFileBrowserWindImage_button"))
		BX("OpenFileBrowserWindImage_button").onclick = OpenFileBrowserWindImage;

	BX("bx_src").onchange = BX("bx_hspace").onchange =
	BX("bx_vspace").onchange = BX("bx_border").onchange =
	BX("bx_align").onchange = PreviewReload;

	var pSaveProp = BX("save_props");
	pSaveProp.onclick = function()
	{
		if (this.checked)
			pWidth.onchange();
	};

	pWidth.onchange = function()
	{
		var w = parseInt(this.value);
		if (isNaN(w))
			return;
		pObj.curWidth = pWidth.value = w;
		if (pSaveProp.checked)
		{
			var h = Math.round(w / pObj.iRatio);
			pObj.curHeight = pHeight.value = h;
		}
		PreviewReload();
	};

	pHeight.onchange = function()
	{
		var h = parseInt(this.value);
		if (isNaN(h))
			return;
		pObj.curHeight = pHeight.value = h;
		if (pSaveProp.checked)
		{
			var w = parseInt(h * pObj.iRatio);
			pObj.curWidth = pWidth.value = w;
		}
		PreviewReload();
	};

	window.oBXEditorDialog.adjustSizeEx();
}

function OnSave()
{
	pObj.pMainObj.bSkipChanges = true;
	var _src = BX("bx_src").value;
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

	SAttr(pElement, "width", BX("bx_width").value);
	SAttr(pElement, "height", BX("bx_height").value);
	SAttr(pElement, "hspace", BX("bx_hspace").value);
	SAttr(pElement, "vspace", BX("bx_vspace").value);
	SAttr(pElement, "border", BX("bx_border").value);
	SAttr(pElement, "align", BX("bx_align").value);
	SAttr(pElement, "src", _src);
	SAttr(pElement, "__bxsrc", _src);
	SAttr(pElement, "alt", BX("bx_alt").value);
	SAttr(pElement, "title", BX("bx_img_title").value);
	pObj.pMainObj.bSkipChanges = false;
	pObj.pMainObj.OnChange("image");
}

function PreviewOnLoad()
{
	var w = parseInt(this.style.width || this.getAttribute('width') || this.offsetWidth);
	var h = parseInt(this.style.height || this.getAttribute('hright') || this.offsetHeight);
	if (!w || !h)
		return;
	pObj.iRatio = w / h; // Remember proportion
	pObj.curWidth = BX("bx_width").value = w;
	pObj.curHeight = BX("bx_height").value = h;
};

function PreviewReload(bFirst)
{
	var el = BX("bx_img_preview");
	if(pObj.prevsrc != BX("bx_src").value)
	{
		el.style.display="";
		el.removeAttribute("width");
		el.removeAttribute("height");
		pObj.prevsrc = BX("bx_src").value;
		el.src=BX("bx_src").value;
	}

	if (pObj.curWidth && pObj.curHeight)
	{
		el.style.width = pObj.curWidth + 'px';
		el.style.height = pObj.curHeight + 'px';
	}

	el.alt = BX("bx_alt").value;
	el.title = BX("bx_img_title").value;
	el.border = BX("bx_border").value;
	el.align = BX("bx_align").value;
	el.hspace = BX("bx_hspace").value;
	el.vspace = BX("bx_vspace").value;
}

function SetUrl(filename, path, site)
{
	var url, srcInput = BX("bx_src");

	if (typeof filename == 'object') // Using medialibrary
	{
		url = filename.src;
		BX("bx_img_title").value = filename.description || filename.name;
		BX("bx_alt").value = filename.name;
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

<?ob_start();?>
<table class="bx-image-dialog-tbl">
	<tr>
		<td class="bx-par-title"><label for="bx_src"><?= GetMessage("FILEMAN_ED_IMG_PATH")?></label></td>
		<td class="bx-par-val">
			<input type="text" size="25" value="" id="bx_src" style="float: left;" />
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
	<tr>
		<td class="bx-par-title"><label for="bx_img_title"><?= GetMessage("FILEMAN_ED_IMG_TITLE")?></label></td>
		<td class="bx-par-val"><input type="text" size="30" value="" id="bx_img_title" /></td>
	</tr>
	<tr>
		<td class="bx-par-title"><label for="bx_width"><?= GetMessage("FILEMAN_SIZES")?>:</label></td>
		<td class="bx-par-val">
		<input type="text" size="4" id="bx_width" /> x <input type="text" size="4" id="bx_height" />
		<input type="checkbox" value="Y" checked="checked" id="save_props" /> <label for="save_props"><?= GetMessage("FILEMAN_SAVE_PROPORTIONS")?></label>
		</td>
	</tr>
	<tr>
		<td valign="top">
			<table class="bx-img-side">
				<tr>
					<td><label for="bx_hspace"><?= GetMessage("FILEMAN_ED_IMG_ALT")?></label>
					<br />
					<input type="text" size="20" value="" id="bx_alt" />
					</td>
				</tr>
				<tr>
					<td><label for="bx_align"><?= GetMessage("FILEMAN_ED_IMG_AL")?></label>
					<br />
					<select id="bx_align">
						<option value=""> - <?= GetMessage("FILEMAN_NO_VAL")?> -</option>
						<option value="top"><?= GetMessage("FILEMAN_ALIGN_TOP")?></option>
						<option value="bottom"><?= GetMessage("FILEMAN_ALIGN_BOTTOM")?></option>
						<option value="left"><?= GetMessage("FILEMAN_ALIGN_LEFT")?></option>
						<option value="middle"><?= GetMessage("FILEMAN_ALIGN_MIDDLE")?></option>
						<option value="right"><?= GetMessage("FILEMAN_ALIGN_RIGHT")?></option>
					</select>
					</td>
				</tr>
				<tr>
					<td><label for="bx_hspace"><?= GetMessage("FILEMAN_ED_IMG_HSp")?></label>
					<br />
					<input type="text" id="bx_hspace" size="10">px</td>
				</tr>
				<tr>
					<td><label for="bx_vspace"><?= GetMessage("FILEMAN_ED_IMG_HVp")?></label>
					<br />
					<input type="text" id="bx_vspace" size="10">px</td>
				</tr>
				<tr>
					<td><label for="bx_border"><?= GetMessage("FILEMAN_ED_IMG_BORD")?></label>
					<br />
					<input type="text" id="bx_border" size="10" value="0">px</td>
				</tr>
			</table>
		</td>
		<td valign="top" style="padding-top: 2px;"><?= GetMessage("FILEMAN_ED_IMG_PREV")?>
		<div class="bx-preview"><img id="bx_img_preview" style="display:none"/><?= str_repeat('text ', 200)?></div>
		</td>
	</tr>
</table>
<?$dialogHTML = ob_get_contents(); ob_end_flush();?>

<?elseif($name == "table"):?>
<script>
var pElement = null;
function OnLoad()
{
	if(pObj.params.check_exists)
	{
		window.oBXEditorDialog.SetTitle('<?=GetMessage("FILEMAN_ED_TABLE_PROP")?>');
		pElement = BXFindParentByTagName(pObj.pMainObj.GetSelectionObject(), 'TABLE');
	}
	else
	{
		window.oBXEditorDialog.SetTitle('<?=GetMessage("FILEMAN_ED_NEW_TABLE")?>');
	}

	var
		arStFilter = ['TABLE', 'DEFAULT'], i, arStyles, j,
		elStyles = BX("bx_classname");

	for(i = 0; i < arStFilter.length; i++)
	{
		arStyles = pObj.pMainObj.oStyles.GetStyles(arStFilter[i]);
		for(j = 0; j < arStyles.length; j++)
		{
			if(arStyles[j].className != "")
				elStyles.options.add(new Option(arStyles[j].className, arStyles[j].className, false, false));
		}
	}

	if(pElement)
	{
		BX("rows").value=pElement.rows.length;
		BX("rows").disabled = true;
		BX("cols").value=pElement.rows[0].cells.length;
		BX("cols").disabled = true;
		BX("cellpadding").value = GAttr(pElement, "cellPadding");
		BX("cellspacing").value = GAttr(pElement, "cellSpacing");
		BX("bx_border").value = GAttr(pElement, "border");
		BX("bx_align").value = GAttr(pElement, "align");
		BX("bx_classname").value = GAttr(pElement, "className");
		var v = GAttr(pElement, "width");

		if(v.substr(-1, 1) == "%")
		{
			BX("bx_width").value = v.substr(0, v.length-1);
			BX("width_unit").value = "%";
		}
		else
		{
			if(v.substr(-2, 2) == "px")
				v = v.substr(0, v.length-2);

		 	BX("bx_width").value = v
		}

		v = GAttr(pElement, "height");
		if(v.substr(-1, 1) == "%")
		{
			BX("bx_height").value = v.substr(0, v.length-1);
			BX("height_unit").value = "%";
		}
		else
		{
			if(v.substr(-1, 2) == "px")
				v = v.substr(0, v.length-2);

			BX("bx_height").value = v
		}
	}
	else
	{
		BX("rows").value="2";
		BX("cols").value="3";
		BX("cellpadding").value="1";
		BX("cellspacing").value="1";
		BX("bx_border").value="0";
	}

	window.oBXEditorDialog.adjustSizeEx();
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
		for(i=0; i<BX("rows").value; i++)
		{
			row = pElement.insertRow(-1);
			for(j=0; j<BX("cols").value; j++)
			{
				cell = row.insertCell(-1);
				cell.innerHTML = '<br _moz_editor_bogus_node="on">';
			}
		}
	}
	else
	{
		if(pObj.pMainObj.bTableBorder)
			pObj.pMainObj.__ShowTableBorder(pElement, false);
	}

	SAttr(pElement, "width", (BX("bx_width").value.length>0?BX("bx_width").value+''+(BX("width_unit").value=='%'?'%':''):''));
	SAttr(pElement, "height", (BX("bx_height").value.length>0?BX("bx_height").value+''+(BX("height_unit").value=='%'?'%':''):''));
	SAttr(pElement, "border", BX("bx_border").value);
	SAttr(pElement, "cellPadding", BX("cellpadding").value);
	SAttr(pElement, "cellSpacing", BX("cellspacing").value);
	SAttr(pElement, "align", BX("bx_align").value);
	SAttr(pElement, 'className', BX("bx_classname").value);

	pObj.pMainObj.OnChange("table");

	if(pObj.pMainObj.bTableBorder)
		pObj.pMainObj.__ShowTableBorder(pElement, true);
}

</script>
<?ob_start();?>
<table class="bx-dialog-table">
	<tr>
		<td align="right"><label for="rows"><?= GetMessage("FILEMAN_ED_TBL_R")?></label></td>
		<td><input type="text" size="3" id="rows"></td>
		<td>&nbsp;</td>
		<td align="right"><label for="bx_width"><?= GetMessage("FILEMAN_ED_TBL_W")?></label></td>
		<td nowrap><input type="text" size="3" id="bx_width"><select id="width_unit"><option value="px">px</option><option value="%">%</option></select></td>
	</tr>
	<tr>
		<td align="right"><label for="cols"><?= GetMessage("FILEMAN_ED_TBL_COL")?></label></td>
		<td><input type="text" size="3" id="cols"></td>
		<td>&nbsp;</td>
		<td align="right"><label for="bx_height"><?= GetMessage("FILEMAN_ED_TBL_H")?></label></td>
		<td nowrap><input type="text" size="3" id="bx_height"><select id="height_unit"><option value="px">px</option><option value="%">%</option></td>
	</tr>
	<tr>
		<td colspan="5">&nbsp;</td>
	</tr>
	<tr>
		<td align="right" nowrap><label for="bx_border"><?= GetMessage("FILEMAN_ED_IMG_BORD")?></label></td>
		<td><input type="text" id="bx_border" size="3"></td>
		<td>&nbsp;</td>
		<td align="right" nowrap><label for="cellpadding">Cell padding:</label></td>
		<td><input type="text" id="cellpadding" size="3"></td>
	</tr>
	<tr>
		<td align="right"><label for="bx_align"><?= GetMessage("FILEMAN_ED_TBL_AL")?></label></td>
		<td>
			<select id="bx_align">
				<option value=""></option>
				<option value="left"><?= GetMessage("FILEMAN_ALIGN_LEFT")?></option>
				<option value="center"><?= GetMessage("FILEMAN_ALIGN_MIDDLE")?></option>
				<option value="right"><?= GetMessage("FILEMAN_ALIGN_RIGHT")?></option>
			</select>
		</td>
		<td>&nbsp;</td>
		<td align="right" nowrap><label for="cellspacing">Cell spacing:</label></td>
		<td><input type="text" id="cellspacing" size="3"></td>
	</tr>
	<tr>
		<td align="right"><label for="bx_classname"><?= GetMessage("FILEMAN_ED_STYLE")?>:</label></td>
		<td colspan="4"><select id='bx_classname'><option value=""> - <?= GetMessage("FILEMAN_NO_VAL")?> -</option></select></td>
	</tr>
</table>
<?$dialogHTML = ob_get_contents(); ob_end_flush();?>

<?elseif($name == "pasteastext"):?>
<script>
function OnLoad()
{
	window.oBXEditorDialog.SetTitle('<?=GetMessage("FILEMAN_ED_PASTE_TEXT")?>');
	BX("BXInsertAsText").focus();

	window.oBXEditorDialog.adjustSizeEx();
}

function OnSave()
{
	BXSelectRange(oPrevRange, pObj.pMainObj.pEditorDocument,pObj.pMainObj.pEditorWindow);
	pObj.pMainObj.PasteAsText(BX("BXInsertAsText").value);
}
</script>
<?ob_start();?>
<table style="width: 100%; margin-bottom:8px;">
	<tr>
		<td><?= GetMessage("FILEMAN_ED_FF")?> "<?= GetMessage("FILEMAN_ED_SAVE")?>":</td>
	</tr>
	<tr><td>
		<textarea id="BXInsertAsText" style="width:100%; height:200px;"></textarea>
	</td></tr>
</table>
<?$dialogHTML = ob_get_contents(); ob_end_flush();?>

<?elseif($name == "pasteword"):?>
<script>
var pFrame = null;
function OnLoad()
{
	window.oBXEditorDialog.SetTitle('<?=GetMessage("FILEMAN_ED_PASTE_WORD")?>');
	pFrame = BX("BXPasteAsWordNode_text");

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
		BX("BXPasteAsWordNode_ff").style.display = 'none';
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

	//attaching events
	BX("BXPasteAsWordNode_removeFonts").onclick =
	BX("BXPasteAsWordNode_removeStyles").onclick =
	BX("BXPasteAsWordNode_removeIndents").onclick =
	BX("BXPasteAsWordNode_removeSpaces").onclick =
	dialog_cleanAndShow;

	window.oBXEditorDialog.adjustSizeEx();
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
		removeFonts = BX('BXPasteAsWordNode_removeFonts').checked,
		removeStyles = BX('BXPasteAsWordNode_removeStyles').checked,
		removeIndents = BX('BXPasteAsWordNode_removeIndents').checked;
		removeSpaces = BX('BXPasteAsWordNode_removeSpaces').checked;
	dialog_showClenedHtml(pObj.pMainObj.CleanWordText(pFrame.pDocument.body.innerHTML, [removeFonts, removeStyles, removeIndents, removeSpaces]));
}

function dialog_showClenedHtml(html)
{
	taSourse = BX('BXPasteAsWordNode_sourse');
	taSourse.value = html;
}

function OnSave()
{
	var removeFonts = BX('BXPasteAsWordNode_removeFonts').checked;
	var removeStyles = BX('BXPasteAsWordNode_removeStyles').checked;
	var removeIndents = BX('BXPasteAsWordNode_removeIndents').checked;
	var removeSpaces = BX('BXPasteAsWordNode_removeSpaces').checked;
	BXSelectRange(oPrevRange,pObj.pMainObj.pEditorDocument,pObj.pMainObj.pEditorWindow);
	pObj.pMainObj.PasteWord(pFrame.pDocument.body.innerHTML,[removeFonts, removeStyles, removeIndents, removeSpaces]);
}
</script>
<?ob_start();?>
<table class="bx-dialog-pasteword">
	<tr id="BXPasteAsWordNode_ff">
		<td><?= GetMessage("FILEMAN_ED_FF")?> "<?= GetMessage("FILEMAN_ED_SAVE")?>":</td>
	</tr>
	<tr>
		<td><iframe id="BXPasteAsWordNode_text" src="javascript:void(0)" style="width:100%; height:150px; border:1px solid #CCCCCC;"></iframe></td>
	</tr>
	<tr>
		<td><?= GetMessage("FILEMAN_ED_HTML_AFTER_CLEANING")?></td>
	</tr>
	<tr>
		<td><textarea id="BXPasteAsWordNode_sourse" style="width:100%; height:100px; border:1px solid #CCCCCC;" readonly="true"></textarea></td>
	</tr>
	<tr>
		<td>
			<input id="BXPasteAsWordNode_removeFonts" type="checkbox" checked="checked"><label for="BXPasteAsWordNode_removeFonts"><?= GetMessage("FILEMAN_ED_REMOVE_FONTS")?></label><br>
			<input id="BXPasteAsWordNode_removeStyles" type="checkbox" checked="checked"> <label for="BXPasteAsWordNode_removeStyles"><?= GetMessage("FILEMAN_ED_REMOVE_STYLES")?></label><br>
			<input id="BXPasteAsWordNode_removeIndents" type="checkbox" checked="checked"> <label for="BXPasteAsWordNode_removeIndents"><?= GetMessage("FILEMAN_ED_REMOVE_INDENTS")?></label><br>
			<input id="BXPasteAsWordNode_removeSpaces" type="checkbox" checked="checked"> <label for="BXPasteAsWordNode_removeSpaces"><?= GetMessage("FILEMAN_ED_REMOVE_SPACES")?></label>
		</td>
	</tr>
</table>
<?$dialogHTML = ob_get_contents(); ob_end_flush();?>

<?elseif($name == "pageprops"):?>

<script>
var finput = false;
function OnLoad()
{
	window.oBXEditorDialog.SetTitle('<?=GetMessage("FILEMAN_ED_EDITOR_PAGE_PROP")?>');
	BX.addClass(window.oBXEditorDialog.PARTS.CONTENT, "bxed-dialog-props");

	var eddoc = pObj.params.document;
	BX('BX_dialog_title').value = BX('title').value;
	BX("BX_more_prop_but").onclick = function(e) {AppendRow('', '');};

	<?if(CModule::IncludeModule("search")):?>
	var tag_property = "<? echo htmlspecialchars(COption::GetOptionString("search", "page_tag_property"));?>";
	<?else:?>
	var tag_property = "";
	<?endif;?>

	var code, val, name, cnt = parseInt(BX("maxind").value)+1;
	for(var i=0; i<cnt; i++)
	{
		code = BX("CODE_"+i);
		val = BX("VALUE_"+i);
		name = BX("NAME_"+i);
		if (tag_property == code.value)
			AppendTagPropertyRow(code.value, (val?val.value:null), (name?name.value:null));
		else
			AppendRow(code.value, (val?val.value:null), (name?name.value:null));
	}

	if(finput)
		finput.focus();

	window.oBXEditorDialog.adjustSizeEx();
}

function AppendRow(code, value, name)
{
	var tbl = BX('pageprops_t1');

	var cnt = parseInt(BX("BX_dialog_maxind").value)+1;
	var r = tbl.insertRow(tbl.rows.length-1);
	var c = r.insertCell(-1);
	c.align="right";
	if(name)
		c.innerHTML = '<input type="hidden" id="BX_dialog_CODE_'+cnt+'" name="BX_dialog_CODE_'+cnt+'" value="'+bxhtmlspecialchars(code)+'">'+bxhtmlspecialchars(name)+':';
	else
	{
		c.innerHTML = '<input type="text" id="BX_dialog_CODE_'+cnt+'" name="BX_dialog_CODE_'+cnt+'" value="'+bxhtmlspecialchars(code)+'" size="30">:';
		if(!finput)
			finput = BX('BX_dialog_CODE_'+cnt);
	}

	c = r.insertCell(-1);
	c.innerHTML = '<input type="text" name="BX_dialog_VALUE_'+cnt+'" id="BX_dialog_VALUE_'+cnt+'" value="'+bxhtmlspecialchars(value)+'" size="55">';

	if(!finput)
		finput = BX('BX_dialog_VALUE_'+cnt);

	BX("BX_dialog_maxind").value = cnt;
}

function AppendTagPropertyRow(code, value, name)
{
	var tbl = BX('pageprops_t1');

	var cnt = parseInt(BX("BX_dialog_maxind").value)+1;
	var r = tbl.insertRow(tbl.rows.length-1);
	var c = r.insertCell(-1);
	c.className = "bx-par-title";

	if(name)
	{
		c.innerHTML = '<input type="hidden" id="BX_dialog_CODE_'+cnt+'" name="BX_dialog_CODE_'+cnt+'" value="'+bxhtmlspecialchars(code)+'">'+bxhtmlspecialchars(name)+':';
	}
	else
	{
		c.innerHTML = '<input type="text" id="BX_dialog_CODE_'+cnt+'" name="BX_dialog_CODE_'+cnt+'" value="'+bxhtmlspecialchars(code)+'" size="30">:';
		if(!finput)
			finput = BX('BX_dialog_CODE_'+cnt);
	}

	c = r.insertCell(-1);
	c.className = "bx-par-val";
	id = 'BX_dialog_VALUE_' + cnt;
	name = 'BX_dialog_VALUE_' + cnt;
	c.innerHTML =  '<input name="'+name+'" id="'+id+'" type="text" autocomplete="off" value="'+value+'" onfocus="window.oObject[this.id] = new JsTc(this, []);"  size="50"/><input type="checkbox" id="ck_'+id+'" name="ck_'+name+'" <? echo (CUserOptions::GetOption("search_tags", "order", "CNT") == "NAME" ? "checked": "");?> title="<?=GetMessage("SEARCH_TAGS_SORTING_TIP")?>">';

	if(!finput)
		finput = BX('BX_dialog_VALUE_' + cnt);

	BX("BX_dialog_maxind").value = cnt;
}

function OnSave()
{
	var eddoc = pObj.params.document;
	var edcnt = parseInt(eddoc.getElementById("maxind").value);
	var cnt = parseInt(BX("BX_dialog_maxind").value);

	for(var i=0; i<=edcnt; i++)
	{
		if(eddoc.getElementById("CODE_"+i).value != BX("BX_dialog_CODE_"+i).value)
			eddoc.getElementById("CODE_"+i).value = BX("BX_dialog_CODE_"+i).value;
		if(eddoc.getElementById("VALUE_"+i).value != BX("BX_dialog_VALUE_"+i).value)
			eddoc.getElementById("VALUE_"+i).value = BX("BX_dialog_VALUE_"+i).value;
	}

	for(i = edcnt+1; i<=cnt; i++)
	{
		pObj.params.window._MoreRProps(BX("BX_dialog_CODE_"+i).value, BX("BX_dialog_VALUE_"+i).value);
	}

	eddoc.getElementById("maxind").value = cnt;
	eddoc.getElementById('title').value = BX('BX_dialog_title').value;

	pObj.pMainObj.bNotSaved = true;

	return iNoOnSelectionChange;
}
</script>
<?ob_start();?>
<table id="pageprops_t1" class="bx-par-tbl">
	<tr>
		<td class="bx-par-title"><label for="BX_dialog_title"><b><?= GetMessage("FILEMAN_DIALOG_TITLE")?></b></label></td>
		<td class="bx-par-val"><input type="text" id="BX_dialog_title" value="" size="30"></td>
	</tr>
	<tr>
		<td></td>
		<td class="bx-par-val"><input id="BX_more_prop_but" type="button" value="<?= GetMessage("FILEMAN_DIALOG_MORE_PROP")?>"></td>
	</tr>
</table>
<input type="hidden" value="-1" id="BX_dialog_maxind">

<?$dialogHTML = ob_get_contents(); ob_end_flush();?>

<?elseif($name == "spellcheck"):?>

<script>
var pElement = null;
function OnLoad()
{
	window.oBXEditorDialog.SetTitle('<?=GetMessage("FILEMAN_ED_SPELLCHECKING")?>');
	pElement = pObj.pMainObj.GetSelectionObject();
	var BXLang = pObj.params.BXLang;
	var usePspell = pObj.params.usePspell;
	var useCustomSpell = pObj.params.useCustomSpell;
	oBXSpellChecker = new BXSpellChecker(pObj.pMainObj, BXLang, usePspell, useCustomSpell);
	oBXSpellChecker.parseDocument();
	oBXSpellChecker.spellCheck();

	window.oBXEditorDialog.adjustSizeEx();
}

</script>
<?ob_start();?>
<div style="margin-bottom: 10px;">
<div id="BX_dialog_waitWin" style="display: block; text-align: center; vertical-align: middle;">
	<table border="0" width="100%" height="100%" style="vertical-align: middle">
		<tr><td height="60"></td></tr>
		<tr>
			<td align="center" valign="top">
				<img style="vertical-align: middle;" src="/bitrix/themes/.default/images/wait.gif" />
				<span style="vertical-align: middle;"><?= GetMessage("FILEMAN_ED_WAIT_LOADING")?></span>
			</td>
		</tr>
	</table>
</div>
<div id="BX_dialog_okMessWin" style="display: none;">
	<table border="0" width="100%" height="100%">
		<tr>
			<td align="center">
				<span style="vertical-align: middle;"><?= GetMessage("FILEMAN_ED_SPELL_FINISHED")?></span>
				<br><br>
				<input id="BX_dialog_butClose" type="button" value="<?= GetMessage("FILEMAN_ED_CLOSE")?>" style="width:150">
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
		<td width="140" valign="top"><input id="BX_dialog_butSkip" type="button" value="<?= GetMessage("FILEMAN_ED_SKIP")?>" style="width:100%;"></td>
		<td width="8"></td>
	</tr>
	<tr><td colspan="4" height="7"></td></tr>
	<tr>
		<td rowspan="9" valign="top"><select id="BX_dialog_suggestionsBox" size="8" style="width:100%;"></select></td>
		<td></td>
		<td><input id="BX_dialog_butSkipAll" type="button" value="<?= GetMessage("FILEMAN_ED_SKIP_ALL")?>" style="width:100%;"></td>
		<td></td>
	</tr>
	<tr height="5"><td colspan="2" height="5"></td></tr>
	<tr>
		<td></td>
		<td><input id="BX_dialog_butReplace" type="button" value="<?= GetMessage("FILEMAN_ED_REPLACE")?>" style="width:100%;"></td>
		<td></td>
	</tr>
	<tr height="5"><td colspan="2" height="5"></td></tr>
	<tr>
		<td></td>
		<td><input id="BX_dialog_butReplaceAll" type="button" value="<?= GetMessage("FILEMAN_ED_REPLACE_ALL")?>" style="width:100%;"></td>
		<td></td>
	</tr>
	<tr height="5"><td colspan="2" height="5"></td></tr>
	<tr>
		<td></td>
		<td><input id="BX_dialog_butAdd" type="button" value="<?= GetMessage("FILEMAN_ED_ADD")?>" style="width:100%;"></td>
		<td></td>
	</tr>
	<tr height="5"><td colspan="2" height="5"></td></tr>
	<tr>
		<td></td>
		<td><input id="BX_dialog_butClose" type="button" value="<?= GetMessage("FILEMAN_ED_CLOSE")?>" style="width:100%;" onClick="pObj.Close();"></td>
		<td></td>
	</tr>
</table>
</div>
</div>
<?$dialogHTML = ob_get_contents(); ob_end_flush();?>

<?elseif($name == "specialchar"):?>

<script>
function OnLoad()
{
	window.oBXEditorDialog.SetTitle('<?=GetMessage("FILEMAN_ED_EDITOR_SPES_CHAR")?>');

	arEntities_dialog = ['&iexcl;','&cent;','&pound;','&curren;','&yen;','&brvbar;','&sect;','&uml;','&copy;','&ordf;','&laquo;','&not;','&reg;','&macr;','&deg;','&plusmn;','&sup2;','&sup3;','&acute;','&micro;','&para;','&middot;','&cedil;','&sup1;','&ordm;','&raquo;','&frac14;','&frac12;','&frac34;','&iquest;','&Agrave;','&Aacute;','&Acirc;','&Atilde;','&Auml;','&Aring;','&AElig;','&Ccedil;','&Egrave;','&Eacute;','&Ecirc;','&Euml;','&Igrave;','&Iacute;','&Icirc;','&Iuml;','&ETH;','&Ntilde;','&Ograve;','&Oacute;','&Ocirc;','&Otilde;','&Ouml;','&times;','&Oslash;','&Ugrave;','&Uacute;','&Ucirc;','&Uuml;','&Yacute;','&THORN;','&szlig;','&agrave;','&aacute;','&acirc;','&atilde;','&auml;','&aring;','&aelig;','&ccedil;','&egrave;','&eacute;','&ecirc;','&euml;','&igrave;','&iacute;','&icirc;','&iuml;','&eth;','&ntilde;','&ograve;','&oacute;','&ocirc;','&otilde;','&ouml;','&divide;','&oslash;','&ugrave;','&uacute;','&ucirc;','&uuml;','&yacute;','&thorn;','&yuml;','&OElig;','&oelig;','&Scaron;','&scaron;','&Yuml;','&circ;','&tilde;','&ndash;','&mdash;','&lsquo;','&rsquo;','&sbquo;','&ldquo;','&rdquo;','&bdquo;','&dagger;','&Dagger;','&permil;','&lsaquo;','&rsaquo;','&euro;','&Alpha;','&Beta;','&Gamma;','&Delta;','&Epsilon;','&Zeta;','&Eta;','&Theta;','&Iota;','&Kappa;','&Lambda;','&Mu;','&Nu;','&Xi;','&Omicron;','&Pi;','&Rho;','&Sigma;','&Tau;','&Upsilon;','&Phi;','&Chi;','&Psi;','&Omega;','&alpha;','&beta;','&gamma;','&delta;','&epsilon;','&zeta;','&eta;','&theta;','&iota;','&kappa;','&lambda;','&mu;','&nu;','&xi;','&omicron;','&pi;','&rho;','&sigmaf;','&sigma;','&tau;','&upsilon;','&phi;','&chi;','&psi;','&omega;','&bull;','&hellip;','&prime;','&Prime;','&oline;','&frasl;','&trade;','&larr;','&uarr;','&rarr;','&darr;','&harr;','&part;','&sum;','&minus;','&radic;','&infin;','&int;','&asymp;','&ne;','&equiv;','&le;','&ge;','&loz;','&spades;','&clubs;','&hearts;'];

	if(!BX.browser.IsIE())
	{
		arEntities_dialog = arEntities_dialog.concat('&thetasym;','&upsih;','&piv;','&weierp;','&image;','&real;','&alefsym;','&crarr;','&lArr;','&uArr;','&rArr;','&dArr;','&hArr;','&forall;','&exist;','&empty;','&nabla;','&isin;','&notin;','&ni;','&prod;','&lowast;','&prop;','&ang;','&and;','&or;','&cap;','&cup;','&there4;','&sim;','&cong;','&sub;','&sup;','&nsub;','&sube;','&supe;','&oplus;','&otimes;','&perp;','&sdot;','&lceil;','&rceil;','&lfloor;','&rfloor;','&lang;','&rang;','&diams;');
	}

	var
		charCont = BX("charCont"),
		charPreview = BX('charPrev'),
		charEntName = BX('entityName'),
		chTable = charCont.appendChild(BX.create("TABLE")),
		i, r, c, lEn = arEntities_dialog.length,
		elEntity = document.createElement("span");

	for(i = 0; i < lEn; i++)
	{
		if (i%19 == 0)
			r = chTable.insertRow(-1);

		elEntity.innerHTML = arEntities_dialog[i];
		c = BX.adjust(r.insertCell(-1), {
			props: {id: 'e_' + i},
			html: elEntity.innerHTML,
			events: {
				mouseover: function(e){
					var entInd = this.id.substring(2);
					BX.addClass(this, 'bx-over');
					charPreview.innerHTML = this.innerHTML;
					charEntName.innerHTML = arEntities_dialog[entInd].substr(1, arEntities_dialog[entInd].length - 2);
				},
				mouseout: function(e){BX.removeClass(this, 'bx-over');},
				click: function(e){
					var entInd = this.id.substring(2);
					BXSelectRange(oPrevRange,pObj.pMainObj.pEditorDocument,pObj.pMainObj.pEditorWindow);
					pObj.pMainObj.insertHTML(arEntities_dialog[entInd]);
					window.oBXEditorDialog.Close();
				}
			}
		});
	}

	window.oBXEditorDialog.SetButtons([window.oBXEditorDialog.btnCancel]);
	window.oBXEditorDialog.adjustSizeEx();
}
</script>

<?ob_start();?>
<div style="height: 285px;">
	<div id="charCont" class="bx-d-char-cont"></div>
	<div id="charPrev" class="bx-d-prev-char"></div>
	<div id="entityName" class="bx-d-ent-name"></div>
</div>
<?$dialogHTML = ob_get_contents(); ob_end_flush();?>

<?elseif($name == "settings"):?>

<script>
function OnLoad()
{
	window.oBXEditorDialog.SetTitle('<?=GetMessage("FILEMAN_ED_SETTINGS")?>');

	if (!pObj.params.lightMode)
	{
		// TAB #1: Toolbar settings
		window.temp_arToolbarSettings = copyObj(SETTINGS[pObj.pMainObj.name].arToolbarSettings);
		_displayToolbarList(BX("__bx_set_1_toolbar"));
	}

	// TAB #2: Taskbar settings
	window.temp_arTaskbarSettings = copyObj(SETTINGS[pObj.pMainObj.name].arTaskbarSettings);
	_displayTaskbarList(BX("__bx_set_2_taskbar"));

	// TAB #3: Additional Properties
	_displayAdditionalProps(BX("__bx_set_3_add_props"));

	window.oBXEditorDialog.SetButtons([
		new BX.CWindowButton(
		{
			title: '<?= GetMessage("FILEMAN_ED_SAVE")?>',
			id: 'save',
			name: 'save',
			action: function()
			{
				var r;
				if(window.OnSave && typeof window.OnSave == 'function')
					r = window.OnSave();

				window.oBXEditorDialog.Close();
			}
		}),
		new BX.CWindowButton(
		{
			title: '<?= GetMessage("FILEMAN_ED_RESTORE")?>',
			id: 'restore',
			name: 'restore',
			action: function()
			{
				restoreSettings();
				window.oBXEditorDialog.Close();
			}
		}),
		window.oBXEditorDialog.btnClose
	]);

	window.oBXEditorDialog.adjustSizeEx();
}

function _displayToolbarList(oCont)
{
	var oTable = oCont.appendChild(BX.create("TABLE", {style: {width: "100%"}}));
	_displayTitle(oTable, '<?=GetMessage("FILEMAN_ED_TLBR_DISP")?>');
	pObj.arToolbarCheckboxes = [];

	for(var sToolBarId in arToolbars)
		if (arToolbars[sToolBarId] && typeof arToolbars[sToolBarId] == 'object')
			_displayToolbarRow(oTable, sToolBarId, SETTINGS[pObj.pMainObj.name].arToolbarSettings[sToolBarId].show);

	_displayTitle(oTable, '<?=GetMessage("FILEMAN_ED_DISP_SET")?>');
	var pCh = _displayRow(oTable, '<?=GetMessage("FILEMAN_ED_REM_TLBR")?>', '__bx_rs_tlbrs');
	oBXEditorUtils.setCheckbox(pCh, pObj.pMainObj.RS_toolbars);
	pCh.onclick = function(){enableCheckboxes(pObj.arToolbarCheckboxes, this.checked);};
}

function _displayToolbarRow(oTb, toolbarId, _show)
{
	var pCh = _displayRow(oTb, arToolbars[toolbarId][0], '__bx_' + toolbarId);
	SAttr(pCh, "__bxid", toolbarId);
	oBXEditorUtils.setCheckbox(pCh, _show);
	if (toolbarId != "standart")
		pObj.arToolbarCheckboxes.push(pCh);

	if (toolbarId == "standart" || !pObj.pMainObj.RS_toolbars)
		pCh.disabled = "disabled";
	pCh.onchange = function(e) {window.temp_arToolbarSettings[this.getAttribute("__bxid")].show = this.checked;}
}

function _displayTaskbarList(oCont)
{
	var oTable = oCont.appendChild(BX.create("TABLE", {style: {width: "100%"}}));
	_displayTitle(oTable,'<?=GetMessage("FILEMAN_ED_TSKBR_DISP")?>');
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
			var settings = SETTINGS[pObj.pMainObj.name].arTaskbarSettings['BXSnippetsTaskbar'];
			if (!settings.show)
				_displayTaskbarRow(oTable,{name:'BXSnippetsTaskbar',title:BX_MESS.SnippetsTB}, settings);
		}
	}
	_displayTitle(oTable,'<?=GetMessage("FILEMAN_ED_DISP_SET")?>');
	var pCh = _displayRow(oTable, '<?=GetMessage("FILEMAN_ED_REM_TSKBR")?>', '__bx_rs_tskbrs');

	oBXEditorUtils.setCheckbox(pCh, pObj.pMainObj.RS_taskbars);
	pCh.onclick = function(){enableCheckboxes(pObj.arTaskbarCheckboxes, this.checked);};

	oCont.appendChild(oTable);
}

function _displayTaskbarRow(pTb, oTaskbar, arSettings)
{
	var taskbarId = oTaskbar.name;
	var pCh = _displayRow(pTb, oTaskbar.title, '__bx_' + taskbarId);

	SAttr(pCh, "__bxid", taskbarId);
	oBXEditorUtils.setCheckbox(pCh, arSettings.show);
	pObj.arTaskbarCheckboxes.push(pCh);
	pCh.onchange = function(e) {window.temp_arTaskbarSettings[this.getAttribute("__bxid")].show = this.checked;}
	if (!pObj.pMainObj.RS_taskbars)
		pCh.disabled = "disabled";
}

function _displayRow(pTb, label, id)
{
	var pTr = pTb.insertRow(-1);
	var pTd = BX.adjust(pTr.insertCell(-1), {props: {className: "bx-par-title"}});

	BX.adjust(pTr.insertCell(-1), {props: {className: "bx-par-val"}, html: '<label for="' + id + '">' + label + '</label>'});
	return pTd.appendChild(BX.create("INPUT", {props: {type: 'checkbox', id: id}}));
}

function _displayTitle(pTb, sTitle)
{
	var pTr = pTb.insertRow(-1);
	pTr.className = "heading_dialog";
	BX.adjust(pTr.insertCell(-1), {props: {colSpan: 2}, text: sTitle});
}

function _displayAdditionalProps(oCont)
{
	var oTable = oCont.appendChild(pObj.pMainObj.CreateElement('TABLE', {width: '100%'}));
	_displayTitle(oTable,'<?=GetMessage("FILEMAN_ED_ADDITIONAL_PROPS")?>');

	oBXEditorUtils.setCheckbox(_displayRow(oTable, '<?=GetMessage("FILEMAN_ED_SHOW_TOOLTIPS")?>', '__bx_show_tooltips'), pObj.pMainObj.showTooltips4Components);

	oBXEditorUtils.setCheckbox(_displayRow(oTable, '<?=GetMessage("FILEMAN_ED_VIS_EFFECTS")?>', '__bx_visual_effects'), pObj.pMainObj.visualEffects);

	if (pObj.pMainObj.arConfig.allowRenderComp2)
		oBXEditorUtils.setCheckbox(_displayRow(oTable, '<?=GetMessage("FILEMAN_ED_RENDER_COMPONENTS2")?>', '__bx_render_comp2'), pObj.pMainObj.bRenderComponents);
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
	var Settings = SETTINGS[pObj.pMainObj.name];

	if (!lightMode)
	{
		if (!BX("__bx_rs_tlbrs").checked)
			temp_arToolbarSettings = arToolbarSettings_default;

		if (!compareObj(Settings.arToolbarSettings,window.temp_arToolbarSettings) ||
			(BX("__bx_rs_tlbrs").checked != pObj.pMainObj.RS_toolbars))
		{
			pObj.pMainObj.RS_toolbars = BX("__bx_rs_tlbrs").checked;
			Settings.arToolbarSettings = temp_arToolbarSettings;
			var postData = oBXEditorUtils.ConvertArray2Post(temp_arToolbarSettings,'tlbrset');
			BXSetConfiguration(pObj.pMainObj,"toolbars","POST",postData);
			BXRefreshToolbars(pObj.pMainObj);
		}
	}

	if (!BX("__bx_rs_tskbrs").checked)
		temp_arTaskbarSettings = arTaskbarSettings_default;

	var showTooltips = !!BX("__bx_show_tooltips").checked;
	if (showTooltips != pObj.pMainObj.showTooltips4Components)
	{
		pObj.pMainObj.showTooltips4Components = showTooltips;
		BXSetConfiguration(pObj.pMainObj, "tooltips", "GET");
	}

	var visEff = !!BX("__bx_visual_effects").checked;
	if (visEff != pObj.pMainObj.visualEffects)
	{
		pObj.pMainObj.visualEffects = visEff;
		BXSetConfiguration(pObj.pMainObj, "visual_effects", "GET");
	}

	if (pObj.pMainObj.arConfig.allowRenderComp2)
	{
		var bRendComp2 = !!BX("__bx_render_comp2").checked;
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

	if (!compareObj(Settings.arTaskbarSettings, window.temp_arTaskbarSettings) ||
		(BX("__bx_rs_tskbrs").checked != pObj.pMainObj.RS_taskbars))
	{
		pObj.pMainObj.RS_taskbars = BX("__bx_rs_tskbrs").checked;
		Settings.arTaskbarSettings = temp_arTaskbarSettings;

		//########    COMPONENTS 1.0  ########
		try
		{
			if (BXComponentsTaskbar_need_preload && Settings.arTaskbarSettings['BXComponentsTaskbar'].show)
			{
				var oSript = document.body.appendChild(document.createElement('script'));
				oSript.src = "/bitrix/admin/htmleditor2/components.js";
			}
		}
		catch(e){/*_alert('ERROR: OnSave >> Load components.js');*/}

		//###########    SNIPPETS  ###########
		//try{
			if(Settings.arTaskbarSettings['BXSnippetsTaskbar'].show)
			{
				if (BXSnippetsTaskbar_need_preload)
					BX.loadScript("/bitrix/admin/htmleditor2/snippets.js");
			}
			else
			{
				var oTaskbar = ar_BXTaskbarS["BXSnippetsTaskbar_" + pObj.pMainObj.name];
				oTaskbar.Close();
			}
		//}catch(e){}

		//########    COMPONENTS 2.0  ########
		try{
			if (BXComponents2Taskbar_need_preload && Settings.arTaskbarSettings['BXComponents2Taskbar'].show)
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
	$arTabs = array();
	if (!isset($_GET['light_mode']) || $_GET['light_mode'] != 'Y')
		$arTabs[] = array("DIV" => "__bx_set_1_toolbar", "TAB" => GetMessage("FILEMAN_ED_TOOLBARS"), "ICON" => "", "TITLE" => GetMessage("FILEMAN_ED_TOOLBARS_SETTINGS"));

	$arTabs[] = array("DIV" => "__bx_set_2_taskbar", "TAB" => GetMessage("FILEMAN_ED_TASKBARS"), "ICON" => "", "TITLE" => GetMessage("FILEMAN_ED_TASKBARS_SETTINGS"));
	$arTabs[] = array("DIV" => "__bx_set_3_add_props", "TAB" => GetMessage("FILEMAN_ED_ADDITIONAL_PROPS"), "ICON" => "", "TITLE" => GetMessage("FILEMAN_ED_ADDITIONAL_PROPS"));

	$tabControlDialog = new CAdmintabControl("tabControlDialog", $arTabs, false);
	$tabControlDialog->Begin();
?>

<?$tabControlDialog->BeginNextTab();?>
<tr><td></td></tr>
<?$tabControlDialog->BeginNextTab();?>
<tr><td></td></tr>
<?$tabControlDialog->BeginNextTab();?>
<tr><td></td></tr>
<?$tabControlDialog->End();?>

<?elseif($name == "flash"):?>
<script>
// F L A S H
function OnLoad()
{
	// ************************ TAB #1: Base params *************************************
	var oDiv = BX("__bx_base_params");
	oDiv.style.padding = "5px";
	oDiv.innerHTML = '<table width="100%" border="0" height="260">'+
					'<tr>'+
						'<td align="right" width="40%">' + BX_MESS.PATH2SWF + ':</td>'+
						'<td width="60%" colspan="3">'+
							'<input type="text" size="30" value="" id="flash_src" name="bx_src">'+
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
	BX("OpenFileBrowserWindFlash_button").onclick = OpenFileBrowserWindFlash;
	var oPreviewCont = BX("flash_preview_cont");
	BX("flash_src").onchange = function(){Flash_Reload(oPreviewCont, BX("flash_src").value, 150, 150)};

	// ************************ TAB #2: Additional params ***********************************
	var oDiv = BX("__bx_additional_params");
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
	var oDiv = BX("__bx_code");
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

	pObj.bx_swf_source = BX("bx_flash_html_code");
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
		Flash_Reload(oPreviewCont, BX("flash_src").value, 150, 150);
	};

	pObj.bx_swf_arParams = {
		src : {p : BX("flash_src")},
		width : {p : BX("flash_width")},
		height : {p : BX("flash_height")},
		id : {p : BX("_flash_id")},
		title : {p : BX("_flash_title")},
		classname : {p : BX("_flash_classname")},
		style : {p : BX("_flash_style")},
		quality : {p : BX("_flash_quality")},
		wmode : {p : BX("_flash_wmode")},
		scale : {p : BX("_flash_scale")},
		salign : {p : BX("_flash_salign")},
		autoplay : {p : BX("_flash_autoplay")},
		loop : {p : BX("_flash_loop")},
		showmenu : {p : BX("_flash_showmenu")}
	};


	pElement = pObj.pMainObj.GetSelectionObject();
	if(pElement && pElement.getAttribute && pElement.getAttribute("__bxtagname") == "flash") // Edit flash
	{
		pObj.bEdit = true;
		var id  = pElement.id;
		pObj.bx_swf_source.disabled = true;
		window.oBXEditorDialog.SetTitle(BX_MESS.FLASH_MOV);
		applyParams(pObj.pMainObj.arFlashParams[id]);
		Flash_Reload(oPreviewCont, BX("flash_src").value, 150, 150);
	}
	else // insert flash
	{
		pObj.bEdit = false;
		window.oBXEditorDialog.SetTitle('<?=GetMessage("FILEMAN_ED_FLASH")?>');
	}

	window.oBXEditorDialog.adjustSizeEx();
}

function SetUrl(filename, path, site)
{
	var url = (path == '/' ? '' : path) + '/'+filename;
	BX("flash_src").value = url;
	if(BX("flash_src").onchange)
		BX("flash_src").onchange();
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

$tabControlDialog = new CAdminTabControl("tabControlDialog", array(
	array("DIV" => "__bx_base_params", "TAB" => GetMessage("FILEMAN_ED_BASE_PARAMS"), "ICON" => "", "TITLE" => GetMessage("FILEMAN_ED_BASE_PARAMS")),
	array("DIV" => "__bx_additional_params", "TAB" => GetMessage("FILEMAN_ED_ADD_PARAMS"), "ICON" => "", "TITLE" => GetMessage("FILEMAN_ED_ADD_PARAMS")),
	array("DIV" => "__bx_code", "TAB" => GetMessage("FILEMAN_ED_HTML_CODE"), "ICON" => "", "TITLE" => GetMessage("FILEMAN_ED_SWF_HTML_CODE"))
), false);

$tabControlDialog->Begin();?>

<?$tabControlDialog->BeginNextTab();?>
<div id="__bx_base_params"></div>
<?$tabControlDialog->BeginNextTab();?>
<div id="__bx_additional_params"></div>
<?$tabControlDialog->BeginNextTab();?>
<div id="__bx_code"></div>
<?$tabControlDialog->End();?>

<?elseif($name == "snippets"):?>
<script>
function OnLoad()
{
	window.oBXEditorDialog.SetTitle(pObj.params.mode == 'add' ? '<?=GetMessage("FILEMAN_ED_ADD_SNIPPET")?>' : '<?=GetMessage("FILEMAN_ED_EDIT_SNIPPET")?>');

	window.arBXSnippetsTaskbars = [];
	for (var k in ar_BXTaskbarS)
	{
		if (k.substr(0, 'BXSnippetsTaskbar'.length) == 'BXSnippetsTaskbar')
			window.arBXSnippetsTaskbars.push(ar_BXTaskbarS[k]);
	}

	BX("__bx_sn_base_params").appendChild(BX("__bx_temp_sn_base_params"));
	BX("__bx_sn_location").appendChild(BX("__bx_temp_sn_location"));
	BX("__bx_sn_additional_params").appendChild(BX("__bx_temp_sn_additional_params"));
	
	var st = BX('__snippet_template');
	st.options[1].value = st.options[1].innerHTML = pObj.pMainObj.templateID;

	window.arSnGroups = {};
	window.rootDefaultName = {};
	var pTemplate = BX("__snippet_template");

	if (pObj.params.mode == 'add')
	{
		BX("__snippet_template").onchange = fillLocation;
		fillLocation();
		BX("__create_new_subfolder").onclick = function(e)
		{
			if (this.checked)
				displayRow('_new_group_row',true);
			else
				displayRow('_new_group_row',false);
		}
	}
	else if (pObj.params.mode == 'edit')
	{
		var oEl = pObj.params.oEl;
		BX("__snippet_title").value = oEl.title;
		BX("__snippet_code").value = oEl.code;
		BX("__snippet_description").value = oEl.description;

		var 
			_pref = '&nbsp;<span style="color:#525355">',
			_suf = '</span>';

		pTemplate.parentNode.style.height = '30px';
		pTemplate.parentNode.innerHTML = _pref + oEl.template + _suf;

		var name = BX("__snippet_name");
		name.parentNode.style.height = '30px';
		name.parentNode.innerHTML = _pref + oEl.name + _suf;

		var group_sel = BX("__snippet_group");
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
			var old_img_tr = BX("__bx_snd_exist_image_tr");
			var img_path = 'snippets/images/'+(_path == '' ? '' : _path+'/')+oEl.thumb;
			old_img_tr.cells[1].innerHTML = _pref+img_path+_suf;
			displayRow('__bx_snd_new_image_chbox_tr',true);
			displayRow('__bx_snd_new_image_tr',false);
			BX("__bx_snd_new_image_tr").cells[0].innerHTML = '<?=GetMessage("FILEMAN_ED_SN_NEW_IMG")?>:';

			BX("__new_image_chbox").onclick = function()
			{
				if (this.checked)
					displayRow('__bx_snd_new_image_tr',true);
				else
					displayRow('__bx_snd_new_image_tr',false);
			}
		}
	}

	window.oBXEditorDialog.adjustSizeEx();
}

function SetUrl(filename, path, site)
{
	var url = path+'/'+filename;
	BX("thumb_src").value = url;
	if(BX("thumb_src").onchange)
		BX("thumb_src").onchange();
}

function fillLocation()
{
	var template = BX("__snippet_template").value;

	if (window.arSnGroups[template])
		return _fillLocation(template);

	var _r = new JCHttpRequest();
	_r.Action = function(result)
	{
		try
		{
			setTimeout(function ()
				{
					_fillLocation(template);
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

function _fillLocation(template)
{
	var _arGroups = window.arSnGroups[template];
	var file_name = BX("__snippet_name");
	file_name.value = window.rootDefaultName[template];
	var group_sel = BX("__snippet_group");
	group_sel.options.length = 0;
	group_sel.onchange = function()
	{
		var chbox = BX("__create_new_subfolder");

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
	BX("thumb_src").value = url;
	if(BX("thumb_src").onchange)
		BX("thumb_src").onchange();
}

function displayRow(rowId,bDisplay)
{
	var row = BX(rowId);
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

function OnSave()
{
	var title = BX("__snippet_title").value;
	var code = BX("__snippet_code").value;

	if (title == "")
	{
		alert("<?=GetMessage("FILEMAN_ED_WRONG_PARAM_TITLE")?>");
		return false;
	}
	if (code == "")
	{
		alert("<?=GetMessage("FILEMAN_ED_WRONG_PARAM_CODE")?>");
		return false;
	}

	if (pObj.params.mode == 'add')
	{
		var name = BX("__snippet_name").value;
		name = name.replace(/[^a-z0-9\s!\$\(\)\[\]\{\}\-\.;=@\^_\~]/gi, "");

		var template = BX("__snippet_template").value;
		if (template == "")
			template = ".default";

		var new_group = '';
		if (BX("__create_new_subfolder").checked)
			new_group = BX("__new_subfolder_name").value.replace(/\\/ig, '/');

		new_group = new_group.replace(/[^a-z0-9\s!\$\(\)\[\]\{\}\-\.;=@\^_\~]/gi, "");

		checkAndSave(name, template, new_group);
	}
	else if (pObj.params.mode == 'edit')
	{
		editSnippet(title, code);
	}
}

function checkAndSave(fileName, templateId, new_group)
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

	//if (confirm("<?=GetMessage("FILEMAN_ED_FILE_EXISTS")?>"))
	saveSnippet(fileName, templateId, new_group);
}

function saveSnippet(fileName, templateId, new_group)
{
	var 
		title = BX("__snippet_title").value,
		code = BX("__snippet_code").value,
		thumb = BX("thumb_src").value,
		description = BX("__snippet_description").value,
		location = BX("__snippet_group").value;

	if (location == '..')
		location = '';

	var postData = "title="+escape(title)+"&code="+encodeURIComponent(code)+"&name="+escape(fileName)+"&description="+escape(description)+"&location="+escape(location)+"&new_group="+escape(new_group)+"&thumb="+escape(thumb);

	var _ss = new JCHttpRequest();
	window.__bx_res_sn_filename = null;
	_ss.Action = function(result) {setTimeout(function()
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
			thumb = fileName + thumb.substr(thumb.lastIndexOf('.'));

		var c = "sn_" + Math.round(Math.random()*1000000);
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
	}, 100);};

	_ss.Post(manage_snippets_path + '&templateID=' + templateId + '&target=add', postData);
}

function editSnippet(title, code)
{
	var oEl = pObj.params.oEl;
	var description = BX("__snippet_description").value;
	var elNode = pObj.params.elNode;
	var thumb = '';
	var pSessid = BX("sessid");
	var post_data = '';

	if (BX("__new_image_chbox").checked);
		thumb = BX("thumb_src").value;

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
		return;

	post_data += "&name="+escape(oEl.name)+"&path="+escape(oEl.path.replace(/,/g,'/'))+"&templateID="+escape(oEl.template);

	var _es = new JCHttpRequest();
	_es.Action = function(result){setTimeout(function(){elNode.onclick();}, 500);}
	_es.Post(manage_snippets_path + '&target=edit', post_data);

	oBXEditorUtils.BXRemoveAllChild(pObj.params.prop_taskbar);
}
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

$tabControlDialog = new CAdmintabControl("tabControlDialog", array(
	array("DIV" => "__bx_sn_base_params", "TAB"=>GetMessage("FILEMAN_ED_BASE_PARAMS"), "ICON" => "", "TITLE" =>'' ),
	array("DIV" => "__bx_sn_location", "TAB"=>GetMessage("FILEMAN_ED_LOCATION"), "ICON" => "", "TITLE"=>''),
	array("DIV" => "__bx_sn_additional_params", "TAB"=>GetMessage("FILEMAN_ED_ADD_PARAMS"), "ICON" => "", "TITLE"=>''),
), false);
$tabControlDialog->Begin();
?>

<?$tabControlDialog->BeginNextTab();?>
<tr><td></td></tr>
<?$tabControlDialog->BeginNextTab();?>
<tr><td></td></tr>
<?$tabControlDialog->BeginNextTab();?>
<tr><td></td></tr>
<?$tabControlDialog->End();?>

<table id="__bx_temp_sn_base_params" class="add_snippet">
	<tr>
		<td>
			<?=GetMessage("FILEMAN_ED_TITLE")?>:<span class="required">*</span><br />
			<input id="__snippet_title" type="text">
		</td>
	</tr>
	<tr height="100%">
		<td>
			<?=GetMessage("FILEMAN_ED_CODE")?>:<span class="required">*</span><br />
			<textarea id="__snippet_code" rows="10"></textarea>
		</td>
	</tr>
</table>

<table id="__bx_temp_sn_location" class="add_snippet">
	<tr>
		<td width="40%" align="right"><?=GetMessage("FILEMAN_ED_TEMPLATE")?>:</td>
		<td width="60%">
			<select id="__snippet_template" style="width: 160px;">
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

<table id="__bx_temp_sn_additional_params" class="add_snippet">
	<tr style="height:0%; display:none" id="__bx_snd_exist_image_tr">
		<td width="30%"align="right"><?=GetMessage("FILEMAN_ED_SN_IMAGE")?>:</td>
		<td width="70%"></td>
	</tr>
	<tr style="height:0%; display:none" id="__bx_snd_new_image_chbox_tr">
		<td width="30%"align="right"><label for='__new_image_chbox'><?=GetMessage("FILEMAN_ED_SN_DEL_IMG")?>:</label></td>
		<td width="70%"><input style="width:18px" id="__new_image_chbox" type="checkbox"></input></td>
	</tr>
	<tr id="__bx_snd_new_image_tr">
		<td width="30%"align="right"><?=GetMessage("FILEMAN_ED_SN_IMAGE")?>:</td>
		<td width="70%">
		<input type="text" size="25" value="" id="thumb_src" style="width: 85%"><input id="OpenFileDialog_button" type="button" value="..." onclick="OpenFileDialog_thumb()" style="width: 10%">
		</td>
	</tr>
	<tr>
		<td colSpan="2">
			<?=GetMessage("FILEMAN_ED_DESCRIPTION")?>:<br />
			<textarea id="__snippet_description" rows="10"></textarea>
		</td>
	</tr>
</table>

<?elseif($name == "edit_hbf"):?>
<script>
function OnLoad()
{
	window.oBXEditorDialog.SetTitle('<?=GetMessage("FILEMAN_ED_EDIT_HBF")?>');
	// ************************ TAB #1: HEAD *************************************
	var oDiv = BX("__bx_head");
	oDiv.style.padding = "5px";
	var newCell = titleTable = oDiv.getElementsByTagName("TABLE")[0].rows[1].insertCell(1);
	newCell.style.paddingRight = (BX.browser.IsIE() ? "12px" : "2px");
	var _insertDefaultImg = pObj.pMainObj.CreateElement("DIV", {title: '<?=GetMessage("FILEMAN_ED_RESTORE")?>', className: "iconkit_c", onclick: insertDefault_head}, {backgroundPosition: "-162px -43px", width: "16px", height: "16px"});
	newCell.appendChild(_insertDefaultImg);

	var oTA = pObj.pMainObj.CreateElement("TEXTAREA", {id: "__bx_head_ta"}, {width: "99%", height: "280px"});
	oTA.value = pObj.pMainObj._head + pObj.pMainObj._body;
	oDiv.appendChild(oTA);

	// ************************ TAB #3: Footer ***********************************
	var oDiv = BX("__bx_footer");
	oDiv.style.padding = "5px";

	var newCell = titleTable = oDiv.getElementsByTagName("TABLE")[0].rows[1].insertCell(1);
	newCell.style.paddingRight = (BX.browser.IsIE() ? "12px" : "2px");
	var _insertDefaultImg = pObj.pMainObj.CreateElement("DIV", {title: '<?=GetMessage("FILEMAN_ED_INSERT_DEF")?>', className: "iconkit_c", onclick : insertDefault_footer}, {backgroundPosition: "-162px -43px", width: "16px", height: "16px"});
	newCell.appendChild(_insertDefaultImg);

	var oTA = pObj.pMainObj.CreateElement("TEXTAREA", {id: "__bx_footer_ta"}, {width: "99%", height: "280px"});
	oTA.value = pObj.pMainObj._footer;
	oDiv.appendChild(oTA);

	window.oBXEditorDialog.adjustSizeEx();
}

function OnSave()
{
	BX("__bx_head_ta").value.replace(/(^[\s\S]*?)(<body.*?>)/i, "");
	pObj.pMainObj._head = RegExp.$1;
	pObj.pMainObj._body = RegExp.$2;

	pObj.pMainObj._footer = BX("__bx_footer_ta").value;
	pObj.pMainObj.updateBody();
}

function insertDefault_head()
{
	if (!confirm("<?=GetMessage("FILEMAN_ED_CONFIRM_HEAD")?>"))
		return;

	var oTA = BX("__bx_head_ta");
	var s60 = String.fromCharCode(60);
	var s62 = String.fromCharCode(62);
	oTA.value = s60 + '?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?' + s62 + '<' + '!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">'+"\n"+
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
	var oTA = BX("__bx_footer_ta");
	oTA.value = "</body>\n</html>";
}
</script>
<?
$aTabs_dialog = array(
array("DIV" => "__bx_head", "TAB" => GetMessage("FILEMAN_ED_TOP_AREA"), "ICON" => "", "TITLE" => GetMessage("FILEMAN_ED_EDIT_HEAD")),
array("DIV" => "__bx_footer", "TAB" => GetMessage("FILEMAN_ED_BOTTOM_AREA"), "ICON" => "", "TITLE" => GetMessage("FILEMAN_ED_EDIT_FOOTER")),
);
$tabControlDialog = new CAdminTabControl("tabControlDialog", $aTabs_dialog, false);

$tabControlDialog->Begin();?>
<?$tabControlDialog->BeginNextTab();?>
<div id="__bx_head"></div>
<?$tabControlDialog->BeginNextTab();?>
<div id="__bx_footer"></div>
<?$tabControlDialog->End();?>
<?endif;?>

<script>
	if (!window.oBXEditorDialog.bUseTabControl)
	{
		window.oBXEditorDialog.Show();
		window.oBXEditorDialog.SetContent('<?= CUtil::JSEscape($dialogHTML)?>');
		OnLoad();
	}
	else
	{
		CloseWaitWindow();
		OnLoad();
	}
	BX.addClass(window.oBXEditorDialog.PARTS.CONTENT, "bxed-dialog");

	BX.addCustomEvent(window.oBXEditorDialog, 'onWindowUnRegister', function()
	{
		if (window.oBXEditorDialog && window.oBXEditorDialog.DIV && window.oBXEditorDialog.DIV.parentNode)
			window.oBXEditorDialog.DIV.parentNode.removeChild(window.oBXEditorDialog.DIV);
	});

	// Set default buttons
	if (!window.oBXEditorDialog.PARAMS.buttons || !window.oBXEditorDialog.PARAMS.buttons.length)
	{
		window.oBXEditorDialog.SetButtons([
			new BX.CWindowButton(
			{
				title: '<?= GetMessage("FILEMAN_ED_SAVE")?>',
				id: 'save',
				name: 'save',
				action: function()
				{
					var r;
					if(window.OnSave && typeof window.OnSave == 'function')
						r = window.OnSave();
					//if((r & 'NoOnSelectionChange') != 0)
					//	pObj.pMainObj.OnEvent("OnSelectionChange", ["always"]);
					if (r !== false)
						window.oBXEditorDialog.Close();
				}
			}),
			window.oBXEditorDialog.btnClose
		]);
	}
</script>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");?>
