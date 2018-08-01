function LHEButton(oBut, pLEditor)
{
	if (!oBut.name)
		oBut.name = oBut.id;
	if (!oBut.title)
		oBut.title = oBut.name;
	this.disabled = false;

	this.pLEditor = pLEditor;
	this.oBut = oBut;
	this.Create();
}

LHEButton.prototype =
{
Create: function ()
{
	var _this = this;
	this.pWnd = jsUtils.CreateElement("IMG", {src: this.pLEditor.oneGifSrc, title: this.oBut.title, className: "lhe-button lhe-button-normal", id: "lhe_btn_" + this.oBut.id.toLowerCase()});

	this.pCont = jsUtils.CreateElement("DIV", {className: 'lhe-button-cont'});
	this.pCont.appendChild(this.pWnd);

	this.pWnd.onmouseover = function(e){_this.OnMouseOver(e, this)};
	this.pWnd.onmouseout = function(e){_this.OnMouseOut(e, this)};
	this.pWnd.onclick = function(e){_this.OnClick(e, this)};
},

OnMouseOver: function (e, pEl)
{
	if(this.disabled)
		return;
	pEl.className = 'lhe-button lhe-button-over';
},

OnMouseOut: function (e, pEl)
{
	if(this.disabled)
		return;

	if(this.checked)
		pEl.className = 'lhe-button lhe-button-checked';
	else
		pEl.className = 'lhe-button lhe-button-normal';
},

OnClick: function (e, pEl)
{
	if(this.disabled)
		return false;

	var res = false;
	if(this.oBut.handler)
		res = this.oBut.handler(this) !== false;

	if(!res && this.oBut.cmd)
		res = this.pLEditor.executeCommand(this.oBut.cmd);

	return res;
},

Check: function (bFlag)
{
	if(bFlag == this.checked || this.disabled)
		return;

	this.checked = bFlag;
	if(this.checked)
		this.pWnd.className = 'lhe-button lhe-button-checked';
	else
		this.pWnd.className = 'lhe-button lhe-button-normal';
}
}

/* Dialog */
function LHEDialog(arParams, pLEditor)
{
	this.pSel = arParams.obj || false;
	this.pLEditor = pLEditor;
	this.id = arParams.id;
	this.arParams = arParams;
	this.Create();
};

LHEDialog.prototype = {
Create: function()
{
	this.pLEditor.bDialogOpened = true;
	var id = "lhe_dialog_" + this.pLEditor.id;
	var floatDiv = document.getElementById(id);
	var _this = this;
	if(floatDiv)
		this.Close(floatDiv);

	if(!this.params || typeof(this.params) != "object")
		this.params = {};

	this.pLEditor.oPrevRange = this.pLEditor.GetSelectionRange();
	if (jsUtils.bIsIE && !this.arParams.bCM)
	{
		if (this.pLEditor.GetSelectedText(this.pLEditor.oPrevRange) == '')
		{
			this.pLEditor.InsertHTML('<img id="bx_lhe_temp_bogus_node" src="/bitrix/images/1.gif" _moz_editor_bogus_node="on" style="border: 0px !important;"/>');
			this.pLEditor.oPrevRange = this.pLEditor.GetSelectionRange();
		}
	}

	window.pLHEDialog = this;
	this.overlay_id = "lhe_overlay";
	this.zIndex = 1000;

	var div = document.body.appendChild(jsUtils.CreateElement("DIV", {id: id, className: 'lhe-dialog'}, {left: '-1000px', top: '-1000px', zIndex: this.zIndex}));
	div.innerHTML =
		'<div class="title">'+
		'<table>'+
		'	<tr>'+
		'		<td width="100%" class="title-text" onmousedown="jsFloatDiv.StartDrag(arguments[0], document.getElementById(\'' + id + '\'));" id="lhe_editor_dialog_title">'+'Title'+'</td>'+
		'		<td width="0%"><a class="close" href="javascript:pLHEDialog.Close();" onclick="pLHEDialog.Close(); return false;" title="' + LHE_MESS.DialogClose + '"></a></td></tr>'+
		'</table>'+
		'</div>'+
		'<div class="content" id="lhe_editor_dialog_content"></div>';

	this.floatDiv = div;
	this.pContent =  document.getElementById('lhe_editor_dialog_content');
	this.pTitle = document.getElementById('lhe_editor_dialog_title');

	if (window.jsPopup)
		jsPopup.DenyClose();

	jsUtils.addEvent(document, "keypress", pLHEDialog.OnKeyPress);

	window['lhe_resize_overlay_' + this.pLEditor.id] = function(){_this.ResizeOverlay();};
	this.CreateOverlay();
	var oDialog = window.LHEDailogs[this.id](this);
	if (!oDialog)
		return;

	this.pContent.innerHTML = oDialog.innerHTML;
	this.pTitle.innerHTML = oDialog.title;
	div.style.width = oDialog.width || '450px';
	if (oDialog.OnLoad)
		oDialog.OnLoad();

	var
		w = jsUtils.GetWindowInnerSize(),
		s = jsUtils.GetWindowScrollPos(),
		left = parseInt(s.scrollLeft + w.innerWidth / 2 - div.offsetWidth / 2),
		top = parseInt(s.scrollTop + w.innerHeight / 2 - div.offsetHeight / 2) - 50;

	jsFloatDiv.Show(div, left, top, 5, false, false);
	jsFloatDiv.AdjustShadow(div);
},

OnKeyPress: function(e)
{
	if(!e) e = window.event
	if(e.keyCode == 27)
	{
		jsUtils.removeEvent(document, "keypress", pLHEDialog.OnKeyPress);
		pLHEDialog.Close();
	}
},

Close: function(floatDiv)
{
	this.RemoveOverlay();
	if (!floatDiv)
		floatDiv = this.floatDiv;
	if (!floatDiv || !floatDiv.parentNode)
		return;

	this.pLEditor.bDialogOpened = false;
	jsFloatDiv.Close(floatDiv);
	floatDiv.parentNode.removeChild(floatDiv);
	if (window.jsPopup)
		jsPopup.AllowClose();
},

CreateOverlay: function()
{
	var windowSize = jsUtils.GetWindowScrollSize();
	this.overlay = document.body.appendChild(jsUtils.CreateElement("DIV", {id: this.overlay_id, className: "lhe-overlay"}, {zIndex: this.zIndex - 5, width: windowSize.scrollWidth + "px", height: windowSize.scrollHeight + "px"}));
	this.overlay.ondrag = jsUtils.False;
	this.overlay.onselectstart = jsUtils.False;
	jsUtils.addEvent(window, "resize", window['lhe_resize_overlay_' + this.pLEditor.id]);
},

RemoveOverlay: function()
{
	if (this.overlay && this.overlay.parentNode)
		this.overlay.parentNode.removeChild(this.overlay);
	jsUtils.removeEvent(window, "resize", window['lhe_resize_overlay_' + this.pLEditor.id]);
},

ResizeOverlay: function()
{
	if (this.overlay)
		this.overlay.style.width = jsUtils.GetWindowScrollSize().scrollWidth + "px";
}
}

/* List */
function LHEList(oBut, pLEditor)
{
	if (!oBut.name)
		oBut.name = oBut.id;
	if (!oBut.title)
		oBut.title = oBut.name;
	this.disabled = false;
	this.zIndex = 1000;
	this.fid = oBut.id.toLowerCase() + '_' + pLEditor.id;

	this.pLEditor = pLEditor;
	this.oBut = oBut;
	this.Create();
	this.bRunOnOpen = false;


	if (oBut.OnCreate && typeof oBut.OnCreate == 'function')
		this.bRunOnOpen = true;
}

LHEList.prototype.Create = function ()
{
	var _this = this;
	this.pWnd = jsUtils.CreateElement("IMG", {src: this.pLEditor.oneGifSrc, title: this.oBut.title, className: "lhe-button lhe-button-normal", id: "lhe_btn_" + this.oBut.id.toLowerCase()});

	this.pWnd.onmouseover = function(e){_this.OnMouseOver(e, this)};
	this.pWnd.onmouseout = function(e){_this.OnMouseOut(e, this)};
	this.pWnd.onclick = function(e){_this.OnClick(e, this)};

	this.pCont = jsUtils.CreateElement("DIV", {className: 'lhe-button-cont'});
	this.pCont.appendChild(this.pWnd);

	window['lhe_list_keypress_' + this.fid] = function(e){_this.OnKeyPress(e);};

	this.pValuesCont = document.body.appendChild(jsUtils.CreateElement("DIV", {className: "lhe-list-val-cont"}, {zIndex: this.zIndex}));
}

LHEList.prototype.OnMouseOver = function (e, pEl)
{
	if(this.disabled)
		return;
	pEl.className = 'lhe-button lhe-button-over';
}

LHEList.prototype.OnMouseOut = function (e, pEl)
{
	if(this.disabled)
		return;

	if(this.checked)
		pEl.className = 'lhe-button lhe-button-checked';
	else
		pEl.className = 'lhe-button lhe-button-normal';
}

LHEList.prototype.OnKeyPress = function(e)
{
	if(!e) e = window.event
	if(e.keyCode == 27)
		this.Close();
};

LHEList.prototype.OnClick = function (e, pEl)
{
	if(this.disabled)
		return false;

	if (this.bOpened)
		return this.Close();

	this.Open();
}

LHEList.prototype.Close = function ()
{
	this.pValuesCont.style.display = 'none';
	this.pLEditor.oTransOverlay.Hide();
	jsUtils.removeEvent(window, "keypress", window['lhe_list_keypress_' + this.fid]);

	this.bOpened = false;
}

LHEList.prototype.Open = function ()
{
	if (this.bRunOnOpen)
	{
		this.oBut.OnCreate(this);
		this.bRunOnOpen = false;
	}

	this.pValuesCont.style.display = 'block';
	var
		pOverlay = this.pLEditor.oTransOverlay.Show(),
		pos = jsUtils.AlignToPos(jsUtils.GetRealPos(this.pWnd), parseInt(this.pValuesCont.offsetWidth) || 150, parseInt(this.pValuesCont.offsetHeight) || 200),
		_this = this;

	jsUtils.addEvent(window, "keypress", window['lhe_list_keypress_' + this.fid]);
	pOverlay.onclick = function(){_this.Close()};

	this.pLEditor.oPrevRange = this.pLEditor.GetSelectionRange();
	if (this.oBut.OnOpen && typeof this.oBut.OnOpen == 'function')
		this.oBut.OnOpen(this);

	this.pValuesCont.style.top = pos.top + 'px';
	this.pValuesCont.style.left = pos.left + 'px';
	this.bOpened = true;
}

LHEList.prototype.SelectItem = function(bSelect)
{
	var pItem = this.arItems[this.pSelectedItemId || 0].pWnd;
	if (bSelect)
	{
		pItem.style.border = '1px solid #4B4B6F';
		pItem.style.backgroundColor = '#FFC678';
	}
	else
	{
		pItem.style.border = '';
		pItem.style.backgroundColor = '';
	}
}


function LHETransOverlay(arParams, pLEditor)
{
	this.pLEditor = pLEditor;
	this.id = 'lhe_trans_overlay';
	this.zIndex = arParams.zIndex || 100;
}

LHETransOverlay.prototype.Create = function ()
{
	this.bCreated = true;
	this.bShowed = false;
	var windowSize = jsUtils.GetWindowScrollSize();
	this.pWnd = document.body.appendChild(jsUtils.CreateElement("DIV", {id: this.id, className: "lhe-trans-overlay"}, {zIndex: this.zIndex, width: windowSize.scrollWidth + "px", height: windowSize.scrollHeight + "px"}));

	var _this = this;
	window['lhe_resize_trans_overlay_' + this.pLEditor.id] = function(){_this.Resize();};
	this.pWnd.ondrag = jsUtils.False;
	this.pWnd.onselectstart = jsUtils.False;
}

LHETransOverlay.prototype.Show = function(arParams)
{
	if (!this.bCreated)
		this.Create();
	this.bShowed = true;

	var windowSize = jsUtils.GetWindowScrollSize();

	this.pWnd.style.display = 'block';
	this.pWnd.style.width = windowSize.scrollWidth + "px";
	this.pWnd.style.height = windowSize.scrollHeight + "px";

	if (!arParams)
		arParams = {};

	if (arParams.zIndex)
		this.pWnd.style.zIndex = arParams.zIndex;

	jsUtils.addEvent(window, "resize", window['lhe_resize_trans_overlay_' + this.pLEditor.id]);
	return this.pWnd;
}

LHETransOverlay.prototype.Hide = function ()
{
	if (!this.bShowed)
		return;
	this.bShowed = false;
	this.pWnd.style.display = 'none';
	jsUtils.removeEvent(window, "resize", window['lhe_resize_trans_overlay_' + this.pLEditor.id]);
	this.pWnd.onclick = null;
}

LHETransOverlay.prototype.Resize = function ()
{
	if (this.bCreated)
		this.pWnd.style.width = jsUtils.GetWindowScrollSize().scrollWidth + "px";
};

function LHEColorPicker(oPar, pLEditor)
{
	if (!oPar.name)
		oPar.name = oPar.id;
	if (!oPar.title)
		oPar.title = oPar.name;
	this.disabled = false;
	this.bCreated = false;
	this.bOpened = false;
	this.zIndex = 1000;
	this.fid = oPar.id.toLowerCase() + '_' + pLEditor.id;

	this.pLEditor = pLEditor;
	this.oPar = oPar;
	this.BeforeCreate();
}

LHEColorPicker.prototype = {
	BeforeCreate: function()
	{
		var _this = this;
		this.pWnd = jsUtils.CreateElement("IMG", {src: this.pLEditor.oneGifSrc, title: this.oPar.title, className: "lhe-button lhe-button-normal", id: "lhe_btn_" + this.oPar.id.toLowerCase()});

		this.pWnd.onmouseover = function(e){_this.OnMouseOver(e, this)};
		this.pWnd.onmouseout = function(e){_this.OnMouseOut(e, this)};
		this.pWnd.onclick = function(e){_this.OnClick(e, this)};
		this.pCont = jsUtils.CreateElement("DIV", {className: 'lhe-button-cont'});
		this.pCont.appendChild(this.pWnd);
	},

	Create: function ()
	{
		var _this = this;
		window['lhe_colpic_keypress_' + this.fid] = function(e){_this.OnKeyPress(e);};
		this.pColCont = document.body.appendChild(jsUtils.CreateElement("DIV", {className: "lhe-colpick-cont"}, {zIndex: this.zIndex}));

		var
			arColors = this.pLEditor.arColors,
			row, cell, colorCell,
			tbl = jsUtils.CreateElement("TABLE", {className: 'lha-colpic-tbl'}),
			i, l = arColors.length;

		row = tbl.insertRow(-1);
		cell = row.insertCell(-1);
		cell.colSpan = 8;
		var defBut = cell.appendChild(jsUtils.CreateElement("SPAN", {className: 'lha-colpic-def-but'}));
		defBut.innerHTML = LHE_MESS.DefaultColor;
		defBut.onmouseover = function()
		{
			this.className = 'lha-colpic-def-but lha-colpic-def-but-over';
			colorCell.style.backgroundColor = 'transparent';
		};
		defBut.onmouseout = function(){this.className = 'lha-colpic-def-but';};
		defBut.onclick = function(e){_this.Select(false);}

		colorCell = row.insertCell(-1);
		colorCell.colSpan = 8;
		colorCell.className = 'lha-color-inp-cell';
		colorCell.style.backgroundColor = arColors[38];

		for(i = 0; i < l; i++)
		{
			if (Math.round(i / 16) == i / 16) // new row
				row = tbl.insertRow(-1);

			cell = row.insertCell(-1);
			cell.innerHTML = '&nbsp;';
			cell.className = 'lha-col-cell';
			cell.style.backgroundColor = arColors[i];
			cell.id = 'lhe_color_id__' + i;

			cell.onmouseover = function (e)
			{
				this.className = 'lha-col-cell lha-col-cell-over';
				colorCell.style.backgroundColor = arColors[this.id.substring('lhe_color_id__'.length)];
			};
			cell.onmouseout = function (e){this.className = 'lha-col-cell';};
			cell.onclick = function (e)
			{
				var k = this.id.substring('lhe_color_id__'.length);
				_this.Select(arColors[k]);
			};
		}

		this.pColCont.appendChild(tbl);
		this.bCreated = true;
	},

	OnClick: function (e, pEl)
	{
		if(this.disabled)
			return false;

		if (!this.bCreated)
			this.Create();

		if (this.bOpened)
			return this.Close();

		this.Open();
	},

	Open: function ()
	{
		var
			pOverlay = this.pLEditor.oTransOverlay.Show(),
			pos = jsUtils.AlignToPos(jsUtils.GetRealPos(this.pWnd), 325, 155),
			_this = this;

		this.pLEditor.oPrevRange = this.pLEditor.GetSelectionRange();
		jsUtils.addEvent(window, "keypress", window['lhe_colpic_keypress_' + this.fid]);
		pOverlay.onclick = function(){_this.Close()};

		this.pColCont.style.display = 'block';
		this.pColCont.style.top = pos.top + 'px';
		this.pColCont.style.left = pos.left + 'px';
		this.bOpened = true;
	},

	Close: function ()
	{
		this.pColCont.style.display = 'none';
		this.pLEditor.oTransOverlay.Hide();
		jsUtils.removeEvent(window, "keypress", window['lhe_colpic_keypress_' + this.fid]);

		this.bOpened = false;
	},

	OnMouseOver: function (e, pEl)
	{
		if(this.disabled)
			return;
		pEl.className = 'lhe-button lhe-button-over';
	},

	OnMouseOut: function (e, pEl)
	{
		if(this.disabled)
			return;
		pEl.className = 'lhe-button lhe-button-normal';
	},

	OnKeyPress: function(e)
	{
		if(!e) e = window.event
		if(e.keyCode == 27)
			this.Close();
	},

	Select: function (color)
	{
		this.pLEditor.SelectRange(this.pLEditor.oPrevRange);
		if (this.oPar.OnSelect && typeof this.oPar.OnSelect == 'function')
			this.oPar.OnSelect(color, this);
		this.Close();
	}
};

// CONTEXT MENU FOR EDITING AREA
function LHEContextMenu(arParams, pLEditor)
{
	this.zIndex = arParams.zIndex;
	this.pLEditor = pLEditor;
	this.Create();
}

LHEContextMenu.prototype = {
Create: function()
{
	this.pref = 'LHE_CM_' + this.pLEditor.id.toUpperCase()+'_';
	this.oDiv = document.body.appendChild(jsUtils.CreateElement('DIV', {className: 'lhe-cm', id: this.pref + '_cont'}, {zIndex: this.zIndex}));
	this.oDiv.innerHTML = '<table><tr><td class="lhepopup"><table id="' + this.pref + '_cont_items"><tr><td></td></tr></table></td></tr></table>';

	// Part of logic of JCFloatDiv.Show()   Prevent bogus rerendering window in IE... And SpeedUp first context menu calling
	document.body.appendChild(jsUtils.CreateElement('IFRAME',{id: this.pref + '_frame', src: "javascript:void(0)"}, {position: 'absolute', zIndex: this.zIndex - 5, left: '-1000px', top: '-1000px', visibility: 'hidden'}));
	this.menu = new PopupMenu(this.pref + '_cont');
},

Show: function(arParams)
{
	if (!arParams.pElement || !this.FetchAndBuildItems(arParams.pElement))
		return;

	try{this.pLEditor.SelectElement(arParams.pElement);}catch(e){}
	this.pLEditor.oPrevRange = this.pLEditor.GetSelectionRange();
	this.oDiv.style.width = parseInt(this.oDiv.firstChild.offsetWidth) + 'px';

	var
		_this = this,
		w = parseInt(this.oDiv.offsetWidth),
		h = parseInt(this.oDiv.offsetHeight),
		pOverlay = this.pLEditor.oTransOverlay.Show();
	pOverlay.onclick = function(){_this.Close()};
	window['lhe_cm_keypress_' + this.pLEditor.id] = function(e){_this.OnKeyPress(e);};
	jsUtils.addEvent(window, "keypress", window['lhe_cm_keypress_' + this.pLEditor.id]);

	arParams.oPos.right = arParams.oPos.left + w;
	arParams.oPos.bottom = arParams.oPos.top;

	this.menu.PopupShow(arParams.oPos);
},

Close: function()
{
	this.menu.PopupHide();
	this.pLEditor.oTransOverlay.Hide();
	jsUtils.removeEvent(window, "keypress", window['lhe_cm_keypress_' + this.pLEditor.id]);
},

FetchAndBuildItems: function(pElement)
{
	var pElementTemp,
	i, k,
	arMenuItems = [],
	arUsed = {},
	strPath, strPath1,
	__bxtagname = false;
	this.arSelectedElement = {};

	//Adding elements
	while(pElement && (pElementTemp = pElement.parentNode) != null)
	{
		if(pElementTemp.nodeType == 1 && pElement.tagName && (strPath = pElement.tagName.toUpperCase()) && strPath != 'TBODY' && !arUsed[strPath])
		{
			strPath1 = strPath;
			if (pElement.getAttribute && (__bxtagname = pElement.getAttribute('__bxtagname')))
				strPath1 = __bxtagname.toUpperCase();

			arUsed[strPath] = pElement;
			if(LHEContMenu[strPath1])
			{
				this.arSelectedElement[strPath1] = pElement;
				if (arMenuItems.length > 0)
					arMenuItems.push('separator');
				for(i = 0, k = LHEContMenu[strPath1].length; i < k; i++)
					arMenuItems.push(LHEContMenu[strPath1][i]);
			}
		}
		else
		{
			pElement = pElementTemp;
			continue;
		}
	}

	if (arMenuItems.length == 0)
		return false;

	//Cleaning menu
	var contTbl = document.getElementById(this.pref + '_cont_items');
	while(contTbl.rows.length>0)
		contTbl.deleteRow(0);
	return this.BuildItems(arMenuItems, contTbl);
},

BuildItems: function(arMenuItems, contTbl, parentName)
{
	var n = arMenuItems.length;
	var _this = this;
	var arSubMenu = {};
	this.subgroup_parent_id = '';
	this.current_opened_id = '';

	var _hide = function()
	{
		var cs = document.getElementById("__curent_submenu");
		if (!cs)
			return;
		_over(cs);
		_this.current_opened_id = '';
		_this.subgroup_parent_id = '';
		cs.style.display = "none";
		cs.id = "";
	};

	var _over = function(cs)
	{
		if (!cs)
			return;
		var t = cs.parentNode.nextSibling;
		t.parentNode.className = '';
	};

	var _refresh = function() {setTimeout(function() {_this.current_opened_id = '';_this.subgroup_parent_id = '';}, 400);}
	var i, row, cell, el_params, _atr, _innerHTML, oItem;

	//Creation menu elements
	for(var i = 0; i < n; i++)
	{
		oItem = arMenuItems[i];
		row = contTbl.insertRow(-1);
		cell = row.insertCell(-1);
		if(oItem == 'separator')
		{
			cell.innerHTML = '<div class="popupseparator"></div>';
		}
		else
		{
			if (oItem.isgroup)
			{
				var c = (jsUtils.bIsIE) ? 'arrow_ie' : 'arrow';
				cell.innerHTML =
				'<div id="_oSubMenuDiv_' + oItem.id + '" style="position: relative;"></div>'+
				'<table cellpadding="0" cellspacing="0" class="popupitem" id="'+oItem.id+'">'+
				'	<tr>'+
				'		<td class="gutter"></td>'+
				'		<td class="item">' + oItem.name + '</td>' +
				'		<td class="'+c+'"></td>'+
				'	</tr>'+
				'</table>';
				var oTable = cell.childNodes[1];
				var _LOCAL_CACHE = {};
				arSubMenu[oItem.id] = oItem.elements;

				oTable.onmouseover = function(e)
				{
					var pTbl = this;
					pTbl.className = 'popupitem popupitemover';
					_over(document.getElementById("__curent_submenu"));
					setTimeout(function()
					{
						//pTbl.parentNode.className = 'popup_open_cell';
						if (_this.current_opened_id && _this.current_opened_id == _this.subgroup_parent_id)
						{
							_refresh();
							return;
						}
						if (pTbl.className == 'popupitem')
							return;
						_hide();
						_this.current_opened_id = pTbl.id;

						var _oSubMenuDiv = document.getElementById("_oSubMenuDiv_" + pTbl.id);
						var left = parseInt(oTable.offsetWidth) + 1 + 'px';
						var oSubMenuDiv = jsUtils.CreateElement('DIV', {'className' : 'popupmenu'}, {position: 'absolute', zIndex: 1500, left: left, top: '-1px'});

						_oSubMenuDiv.appendChild(oSubMenuDiv);
						oSubMenuDiv.onmouseover = function(){pTbl.parentNode.className = 'popup_open_cell';};

						var contTbl = oSubMenuDiv.appendChild(jsUtils.CreateElement('TABLE', {cellPadding:0, cellSpacing:0}));
						_this.BuildItems(arSubMenu[pTbl.id], contTbl, pTbl.id);

						oSubMenuDiv.style.display = "block";
						oSubMenuDiv.id = "__curent_submenu";
					}, 400);
				};
				oTable.onmouseout = function(e){this.className = 'popupitem';};
				continue;
			}

			_innerHTML =
				'<table class="popupitem" id="lhe_cm__' + oItem.id + '"><tr>' +
				'	<td class="gutter"><div class="lhe-button" id="lhe_btn_' + oItem.id.toLowerCase()+'"></div></td>' +
				'	<td class="item">' + (oItem.name_edit || oItem.name) + '</td>' +
				'</tr></table>';
			cell.innerHTML = _innerHTML;

			var oTable = cell.firstChild;
			oTable.onmouseover = function(e){this.className='popupitem popupitemover';}
			oTable.onmouseout = function(e){this.className = 'popupitem';};
			oTable.onclick = function(e){_this.OnClick(this);};
		}
	}

	this.oDiv.style.width = contTbl.parentNode.offsetWidth;
	return true;
},

OnClick: function(pEl)
{
	var oItem = LHEButtons[pEl.id.substring('lhe_cm__'.length)];
	if(!oItem || oItem.disabled)
		return false;

	this.selectedElement = this.pLEditor.SelectRange(this.pLEditor.oPrevRange);
	var res = false;

	if(oItem.handler)
		res = oItem.handler(this) !== false;

	if(!res && oItem.cmd)
		this.pLEditor.executeCommand(oItem.cmd);
	this.Close();
},

OnKeyPress: function(e)
{
	if(!e) e = window.event
	if(e.keyCode == 27)
		this.Close();
}
}
