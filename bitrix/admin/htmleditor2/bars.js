function BXTaskbarSet(pColumn, pMainObj, iNum)
{
	if (!SETTINGS[pMainObj.name].arTBSetsSettings)
		SETTINGS[pMainObj.name].arTBSetsSettings = arTBSetsSettings_default;

	if (iNum == 3) // bottom tasbarset (properties )
		SETTINGS[pMainObj.name].arTBSetsSettings[3].height = arTBSetsSettings_default[3].height;
	var arTBSetsSet = SETTINGS[pMainObj.name].arTBSetsSettings;
	ar_BXTaskbarSetS.push(this);
	var obj = this;
	var bVertical = (iNum == 1 || iNum==2);
	this.className = 'BXTaskbarSet';
	pColumn.unselectable = "on";
	this.pParentWnd = pColumn;
	this.pMainObj = pMainObj;
	this.bVertical = bVertical;
	this.pParentWnd.className = 'bxedtaskbarset';
	this.iNum = iNum;
	this.bShowing = arTBSetsSet[iNum].show;
	if(bVertical)
	{
		pColumn.style.verticalAlign = "top";
		SETTINGS[pMainObj.name].arTBSetsSettings[iNum].height = '100%';
	}
	this.pWnd = pColumn.appendChild(this.pMainObj.CreateElement("TABLE", {unselectable: 'on', cellSpacing: '0', cellPadding: '0', width: '100%', height: '100%'}));
	this.sActiveTaskbar = '';
	var r = this.pWnd.insertRow(-1);
	switch(this.iNum)
	{
		case 0: //top
			this.pMainCell = r.insertCell(-1);
			this.pMoveColumn = this.pWnd.insertRow(-1).insertCell(-1);
			break;
		case 1: //left
			this.pMainCell = r.insertCell(-1);
			this.pMoveColumn = r.insertCell(-1);
			break;
		case 2: //right
			this.pMoveColumn = r.insertCell(-1);
			this.pMainCell = r.insertCell(-1);
			break;
		case 3: //bottom
			this.pMoveColumn = r.insertCell(-1);
			this.pMainCell = this.pWnd.insertRow(-1).insertCell(-1);
			break;
	}
	r = null;

	this.pTaskbarsTable = this.pMainCell.appendChild(this.pMainObj.CreateElement("TABLE", {"unselectable": "on", "cellPadding": "0", "cellSpacing": "0", "border": "0"}, {"height": "100%", "width": "100%"}));
	this.pDataColumn = this.pTaskbarsTable.insertRow(-1).insertCell(-1);
	this.pBottomColumn = this.pTaskbarsTable.insertRow(-1).insertCell(-1);
	this.arTaskbars = [];

	this.pMainCell.style.height = "100%";
	this.pMainCell.style.width = "100%";
	this.pDataColumn.style.height = "100%";
	this.pDataColumn.style.width = "100%";

	if (!bVertical)
	{
		this.pMoveColumn.style.cursor = "N-resize";
		this.pMoveColumn.style.backgroundImage = "url(" + image_path + "/slider_bg_h.png)";
		this.pMoveColumn.style.height = "6px";
		this.pMoveColumn.style.width = "100%";
	}
	else
	{
		this.pMoveColumn.style.cursor = "W-resize";
		this.pMoveColumn.appendChild(this.pMainObj.CreateElement("IMG", {src: one_gif_src}, {width: '7px'}));
		this.pMoveColumn.style.backgroundImage = "url(" + image_path + "/slider_bg_v.png)";
	}
	this.pMoveColumn.style.display = "none";
	this.pMoveColumn.ondragstart = function (e){return false;};
	this.pMoveColumn.onmousedown = function(e){obj.MouseDown(e); return false;};
	if (!CACHE_DISPATCHER['TranspToggle'])
		CACHE_DISPATCHER['TranspToggle'] = document.body.appendChild(BXCreateElement('IMG', {src: one_gif_src}, {display: 'none'}, document));
}


BXTaskbarSet.prototype.MouseDown = function (e)
{
	switch(this.iNum)
	{
		case 0:
		case 3:
			var val = parseInt(this.pWnd.offsetHeight);
			var d = (BX.browser.IsIE()) ? 50 : 155;
			var maxVal = val + parseInt(BX(this.pMainObj.name+'_cEditor').offsetHeight) - d;
			//var minVal = 160;
			var minVal = 120;
			var bVertical = true;
			var width = this.pMoveColumn.offsetWidth;
			var height = false;
			break;
		case 1:
		case 2:
			var val = parseInt(this.pWnd.offsetWidth);
			var maxVal = val + parseInt(BX(this.pMainObj.name+'_cEditor').offsetWidth) - 155;
			var minVal = 210;
			var bVertical = false;
			var width = false;
			var height = this.pMoveColumn.offsetHeight;
			break;
	}

	this.pMainObj.ClearPosCache();

	showTranspToggle({
		e: e,
		bVertical: bVertical,
		pMainObj: this.pMainObj,
		pos: GetRealPos(this.pMoveColumn),
		width: width,
		height: height,
		callbackFunc: this.Resize,
		callbackObj: this,
		value: val,
		maxValue: maxVal,
		minValue: minVal
	});
}

BXTaskbarSet.prototype.Resize = function (value)
{
	value = parseInt(value);
	var arTBSetsSet = SETTINGS[this.pMainObj.name].arTBSetsSettings;
	switch (this.iNum)
	{
		case 0:
		case 3:
			if (IEplusDoctype)
			{
				this.pMainObj.IEplusDoctypePatchSizes(value);
				value = value - 35;
			}
			this.pWnd.style.height = value + 'px';
			arTBSetsSet[this.iNum].width = this.pWnd.style.width;
			break;
		case 1:
		case 2:
			this.pWnd.style.width = value + 'px';
			arTBSetsSet[this.iNum].height = this.pWnd.style.height;
			break;
	}
}


BXTaskbarSet.prototype.SaveConfiguration = function ()
{
	if (!this.pMainObj.RS_taskbars)
		return;
	var arTBSetsSet = SETTINGS[this.pMainObj.name].arTBSetsSettings;

	var postData = oBXEditorUtils.ConvertArray2Post(arTBSetsSet, 'tskbrsetset');
	BXSetConfiguration(this.pMainObj, "taskbarsets", "POST", postData);
}

BXTaskbarSet.prototype.Show = function ()
{
	this.bShowing = true;
	var _this = this;
	var btt = this.pMainObj.oBXTaskTabs;
	if (this.pMainObj.visualEffects && btt)
		window.oBXVM.Show({sPos: btt.GetVPos(), ePos: this.GetVPos(), callback: function(){_this.Display(true); _this.pMainObj.IEplusDoctypePatchSizes();}});
	else
		_this.Display(true);
}

BXTaskbarSet.prototype.Hide = function ()
{
	this.bShowing = false;
	this.Display(false);

	var btt = this.pMainObj.oBXTaskTabs;
	if (btt)
	{
		if (this.pMainObj.visualEffects)
			window.oBXVM.Show({sPos: this.GetVPos(), ePos: btt.GetVPos()});

		btt.Refresh();
	}
}

BXTaskbarSet.prototype.Display = function(bDisplay)
{
	var dispStr = bDisplay ? '' : 'none';

	this.pWnd.style.display = dispStr;
	this.pWnd.parentNode.style.display = dispStr;
	if (!this.bVertical)
		this.pWnd.parentNode.parentNode.style.display = dispStr;
}

BXTaskbarSet.prototype.ShowToggle = function(e)
{
	if(this.bShowing)
		this.Hide();
	else
		this.Show();

	SETTINGS[this.pMainObj.name].arTBSetsSettings[this.iNum].show = this.bShowing;
	this.SaveConfiguration();

	if (e.stopPropagandation)
		e.stopPropagandation();
	else
		e.cancelBubble = true;
}


BXTaskbarSet.prototype.AddTaskbar = function (pTaskbar, bDontRefresh)
{
	var arTBSetsSet = SETTINGS[this.pMainObj.name].arTBSetsSettings;
	pTaskbar.bDocked = true;
	pTaskbar.pWnd.style.position = "relative";
	pTaskbar.pWnd.style.zIndex = "200";
	pTaskbar.pWnd.style.left = null;
	pTaskbar.pWnd.style.top = null;
	pTaskbar.pWnd.style.display = GetDisplStr(1);
	this.Display(true);
	pTaskbar.oldWidth = pTaskbar.pWnd.style.width;
	pTaskbar.pWnd.style.width = "100%";
	pTaskbar.pWnd.style.height = "100%"
	pTaskbar.oldHeight = pTaskbar.pWnd.style.height;
	pTaskbar.pTaskbarSet = this;
	pTaskbar.parentCell = this.pWnd;
	this.pWnd.style.height = '100%';
	this.arTaskbars[this.arTaskbars.length] = pTaskbar;
	this.pMoveColumn.style.display = (BX.browser.IsIE() ? "block" : "table-cell");
	this.pWnd.style.width = arTBSetsSet[this.iNum].width;
	this.pWnd.style.height = arTBSetsSet[this.iNum].height;
	if (this.bVertical)
		this.pWnd.parentNode.style.height = "100%";

	this.DrawTabs();

	var tbs = this;
	if (this.bShowing)
		setTimeout(function(){try{tbs.ActivateTaskbar(tbs.arTaskbars[0].id);tbs.Show();}catch(e){}}, 3);
	else
		this.Hide();
	pTaskbar = null;
}

BXTaskbarSet.prototype.GetVPos = function (pTaskbar, bDontRefresh)
{
	var arVPos = [];
	var iNum = this.iNum;
	var edPos = GetRealPos(this.pMainObj.pWnd);
	if (this.bVertical)
	{
		arVPos[iNum] = {
			t: parseInt(edPos.top) + 60,
			l: parseInt(edPos.right) - 200,
			w: 200,
			h: parseInt(this.pMainObj.pWnd.offsetHeight) - 150
		};
	}
	else
	{
		arVPos[iNum] = {
			t: parseInt(edPos.bottom) - 200,
			l: parseInt(edPos.left),
			w: parseInt(this.pMainObj.pWnd.offsetWidth),
			h: 200
		};
	}
	return arVPos[iNum];
};


BXTaskbarSet.prototype.DelTaskbar = function (pTaskbar, bDontRefresh)
{
	pTaskbar.pWnd.parentNode.removeChild(pTaskbar.pWnd);
	pTaskbar.pTaskbarSet = null;

	for(var i = 0; i < this.arTaskbars.length; i++)
	{
		if(pTaskbar.id == this.arTaskbars[i].id)
		{
			this.arTaskbars = this.arTaskbars.slice(0, i).concat(this.arTaskbars.slice(i + 1));
			this.DrawTabs();
			if(this.arTaskbars.length>0)
				this.ActivateTaskbar(this.arTaskbars[0].id);
		}
	}
	var btt = this.pMainObj.oBXTaskTabs;
	btt.Draw();
	btt.Refresh();

	if(this.arTaskbars.length == 0)
		this.Display(false);
}

BXTaskbarSet.prototype.DrawTabs = function ()
{
	if(this.arTaskbars.length == 0)
	{
		this.pMoveColumn.style.display = "none";
		this.Display(false);
	}
	else
	{
		this.pMoveColumn.style.display = (BX.browser.IsIE()?"block":"table-cell");
		this.Display(true);
	}

	if(this.arTaskbars.length <= 1)
	{
		this.pBottomColumn.style.display = 'none';
		return;
	}

	//If more than one taskbars for one taskbarsets
	this.pBottomColumn.style.display = (BX.browser.IsIE() ? "block" : "table-cell");

	oBXEditorUtils.BXRemoveAllChild(this.pBottomColumn);

	var pIconTable = this.pMainObj.CreateElement("TABLE", {"width": "100%", "unselectable": "on", "cellSpacing" : 0, "cellPadding" : 0});
	var r = pIconTable.insertRow(0);
	var c = r.insertCell(-1);
	c.style.width = "9px";
	c.appendChild(this.pMainObj.CreateElement("DIV", {className: 'tabs_common bx_btn_tabs_0a'}, {}));

	this.sActiveTaskbar = this.arTaskbars[0].id;
	var _this = this;
	var tabIsAct, cn;
	for(var k = 0, l = this.arTaskbars.length; k < l; k++)
	{
		tabIsAct = true;
		if (k != 0)
		{
			c = r.insertCell(-1);
			c.style.width = "9px";
			cn = (k==1) ? 'bx_btn_tabs_ad' : 'bx_btn_tabs_dd';
			c.appendChild(this.pMainObj.CreateElement("DIV", {className: 'tabs_common ' + cn}, {}));
			tabIsAct = false;
		}

		c = r.insertCell(-1);
		c.style.width = "0%";
		c.style.backgroundImage = (tabIsAct) ? 'url(' + image_path + '/taskbar_tabs/a-bg.gif)' : 'url(' + image_path + '/taskbar_tabs/d-bg.gif)';
		c.tid = this.arTaskbars[k].id;
		c.innerHTML = '<span unselectable="on" style="overflow:hidden; cursor:default; ">'+BXReplaceSpaceByNbsp(this.arTaskbars[k].title)+'</span>';
		c.onclick = function (e)
		{
			_this.ActivateTaskbar(this.tid);
		};
	}

	c = r.insertCell(-1);
	c.style.width = "9px";
	c.appendChild(this.pMainObj.CreateElement("DIV", {className: 'tabs_common bx_btn_tabs_d0'}, {}));

	c = r.insertCell(-1);
	c.width = "100%";
	c.unselectable = "on";
	c.className = 'bxedtaskbaricontable';
	c.style.backgroundImage = 'url(' + image_path + '/taskbar_tabs/0-bg.gif)';
	c.innerHTML = '&nbsp;';
	this.pBottomColumn.appendChild(pIconTable);
	this.pBottomColumn.style.paddingBottom = "5px";

	c = null;
	r = null;
	pIconTable = null;
}


BXTaskbarSet.prototype.ActivateTaskbar = function (id)
{
	while(this.pDataColumn.childNodes.length>0)
		this.pDataColumn.removeChild(this.pDataColumn.childNodes[0]);

	for(var i = 0, l = this.arTaskbars.length; i < l; i++)
	{
		if(this.arTaskbars[i].id == id)
		{
			this.pDataColumn.appendChild(this.arTaskbars[i].pWnd);
			this.arTaskbars[i].pWnd.style.display = GetDisplStr(1);
			this.sActiveTaskbar = id;
			break;
		}
	}

	if(this.pBottomColumn.childNodes[0])
	{
		var tsb_cells = this.pBottomColumn.childNodes[0].rows[0].cells;
		for(i=0; i<tsb_cells.length-1; i++)
		{
			if(true)
			{
				if (i==0)
				{
					if (tsb_cells[1].tid==id)
						tsb_cells[i].firstChild.className = 'tabs_common bx_btn_tabs_0a';
					else
						tsb_cells[i].firstChild.className = 'tabs_common bx_btn_tabs_0d';
					continue;
				}
				else if (i==tsb_cells.length-2)
				{
					if (tsb_cells[tsb_cells.length-3].tid==id)
						tsb_cells[i].firstChild.className = 'tabs_common bx_btn_tabs_a0';
					else
						tsb_cells[i].firstChild.className = 'tabs_common bx_btn_tabs_d0';
				}
				else if((i+1)%2==0)
				{
					//TaskbarTasb cells
					if (tsb_cells[i].tid==id)
					{
						tsb_cells[i].className = 'bxedtaskbaricontableact';
						tsb_cells[i].style.backgroundImage = 'url(' + image_path + '/taskbar_tabs/a-bg.gif)';
					}
					else
					{
						tsb_cells[i].className = 'bxedtaskbaricontable';
						tsb_cells[i].style.backgroundImage = 'url(' + image_path + '/taskbar_tabs/d-bg.gif)';
					}
				}
				else
				{
					//switching between tabs
					if (tsb_cells[i-1].tid==id)
						tsb_cells[i].firstChild.className = 'tabs_common bx_btn_tabs_ad';
					else if (tsb_cells[i+1].tid==id)
						tsb_cells[i].firstChild.className = 'tabs_common bx_btn_tabs_da';
					else
						tsb_cells[i].firstChild.className = 'tabs_common bx_btn_tabs_dd';
				}
			}
		}
		tsb_cells = null;
	}

	var btt = this.pMainObj.oBXTaskTabs;
	if (btt)
		btt.Refresh();
}


function BXTaskbar(name)
{
	this.name = name;
}

BXTaskbar.prototype.Create = function(pMainObj, title, dx, dy)
{
	ar_BXTaskbarS[this.name+"_"+pMainObj.name] = this;
	this.pMainObj = pMainObj;
	this.className = 'BXTaskbar';
	this.pref = this.pMainObj.name.toUpperCase()+'_BXTaskBar_';
	this.id = Math.random();
	this.bVertical = false;
	this.title = title;
	this.thirdlevel = false;
	var _this = this;
	this.fullyLoaded = true;

	if (!SETTINGS[this.pMainObj.name].arTaskbarSettings[this.name])
		SETTINGS[this.pMainObj.name].arTaskbarSettings[this.name] = arTaskbarSettings_default[this.name];

	var tableTaskbar = pMainObj.CreateElement("TABLE", {unselectable: "on", cellPadding:"0", cellSpacing:"0", ondragstart: 'return false;'}, {position: "absolute", zIndex: "1000"});
	tableTaskbar.style.width = (dx != null ? dx : "100%");
	tableTaskbar.style.height = (dy != null ? dy : "200px");
	tableTaskbar.pObj = this;
	this.pMainObj.pWnd.vAlign = "top";

	var rowTitle = tableTaskbar.insertRow(0);
	var cellTitle = rowTitle.insertCell(0);
	cellTitle.noWrap = "nowrap";
	cellTitle.className = "bxedtaskbartitle";
	cellTitle.unselectable = "on";
	cellTitle.style.height = "0%";
	cellTitle.setAttribute("__bxtagname", "_taskbar_default");
	cellTitle.oncontextmenu = function(e) {_this.pMainObj.OnContextMenu(e, this, true, {pTaskbar: _this});};
	this.cellTitle = cellTitle;

	var hdrow = cellTitle.appendChild(pMainObj.CreateElement("TABLE", {"cellPadding":"0", "cellSpacing":"0", "width":"100%", "className":"bxedtaskbartitletext"})).insertRow(-1);

	var c = hdrow.insertCell(-1);
	c.style.width = "1%";
	c.style.paddingLeft = "2px";
	c.className = 'def';
	this.iconDiv = document.createElement("DIV");
	c.appendChild(this.iconDiv);

	c = hdrow.insertCell(-1);
	c.className = "head_text";
	c.noWrap = true;
	c.unselectable="on";
	c.innerHTML = title;

	c = hdrow.insertCell(-1);
	c.style.width = "25px";
	c.align = "right";
	c.title = BX_MESS.Actions;
	c.className = "head_button";
	c.innerHTML = '<div style="background-position: -60px -81px; background-image:url(' + global_iconkit_path + '); width: 22px; height: 18px; float: left; margin: 1px;"></div>';
	var cmBut = c.childNodes[0];
	cmBut.onmouseover = function(e)
	{
		this.style.margin =  "0px";
		this.style.border =  "#4B4B6F 1px solid";
		this.style.backgroundColor = "#FFC678";
	};
	cmBut.onmouseout = function(e)
	{
		this.style.margin =  "1px";
		this.style.borderStyle = "none";;
		this.style.backgroundColor = "transparent";
	};
	cmBut.onclick = function(e)
	{
		var _bxtgn = cellTitle.getAttribute("__bxtagname");
		if (!_bxtgn)
			return;
		var pos = GetRealPos(this);
		pos.left += 22;
		pos.top += 20;
		oBXContextMenu.Show(2500, 0, pos, false, {pTaskbar: _this, bxtagname: _bxtgn}, _this.pMainObj, true);
	};

	c = hdrow.insertCell(-1);
	c.style.width = "20px";
	c.className = "head_button";
	c.title = BX_MESS.Hide;
	c.innerHTML = '<div style="background-position: -2px -102px; background-image:url(' + global_iconkit_path + '); width: 18px; height: 18px; margin: 1px;"></div>';
	c.onclick = function(e)
	{
		_this.pTaskbarSet.Hide();
		SETTINGS[_this.pMainObj.name].arTBSetsSettings[_this.pTaskbarSet.iNum].show = false;
		_this.pTaskbarSet.SaveConfiguration();
	};

	this.pTitleRow = rowTitle;
	var row2 = tableTaskbar.insertRow(1);
	var cellrow2 = row2.insertCell(0);
	cellrow2.className = "bxedtaskbar";
	cellrow2.vAlign = "top";
	cellrow2.unselectable = "on";
	cellrow2.style.height = "100%";
	this.pDataCell = cellrow2;
	this.pWnd = this.pMainObj.pWnd.appendChild(tableTaskbar);

	this.rootElementsCont = document.createElement('DIV');
	this.rootElementsCont.id = 'root_el_cont_'+this.name;
	this.rootElementsCont.style.width = "100%";
	this.rootElementsCont.style.padding = "10px 1px 1px 10px";

	if(this.OnTaskbarCreate)
		this.OnTaskbarCreate();

	c = null;
	cellrow2 = null;
	row2 = null;
	hdrow = null;
	rowTitle = null;
	tableTaskbar = null;
}

BXTaskbar.prototype.SetActive = function ()
{
	if(this.pTaskbarSet)
		this.pTaskbarSet.ActivateTaskbar(this.id);
}

BXTaskbar.prototype.Close = function()
{
	if(this.pTaskbarSet)
		this.pTaskbarSet.DelTaskbar(this);

	this.SaveConfiguration();
}

BXTaskbar.prototype.SaveConfiguration = function()
{
	if (!this.pMainObj.RS_taskbars)
		return;

	var arTskbrSet = SETTINGS[this.pMainObj.name].arTaskbarSettings;
	arTskbrSet[this.name].show = (this.pTaskbarSet) ? true : false;
	var postData = oBXEditorUtils.ConvertArray2Post(arTskbrSet,'tskbrset');
	BXSetConfiguration(this.pMainObj, "taskbars", "POST", postData);
}

BXTaskbar.prototype.SetContent = function (sContent)
{
	this.pDataCell.innerHTML = sContent;
}

BXTaskbar.prototype.CreateScrollableArea = function (pParent)
{
	var res = this.pMainObj.pDocument.createElement("DIV");
	res.style.position = "relative";
	res.style.left = "0px";
	res.style.right = "0px";
	res.style.width = "100%";
	res.style.height = "100%";
	pParent = pParent.appendChild(res);
	res = null;

	res = this.pMainObj.pDocument.createElement("DIV");
	res.style.position = "absolute";
	res.style.left = "0px";
	res.style.right = "0px";
	res.style.width = "100%";
	res.style.height = "100%";

	if(!BX.browser.IsIE())
		res.style.overflow = "-moz-scrollbars-vertical";

	//res.style.overflowY = "scroll";
	res.style.overflowY = "auto";
	res.style.overflowX = "hidden";

	res.style.scrollbar3dLightColor = "#C0C0C0";
	res.style.scrollbarArrowColor = "#252525";
	res.style.scrollbarBaseColor = "#C0C0C0";
	res.style.scrollbarDarkShadowColor = "#252525";
	res.style.scrollbarFaceColor = "#D4D4D4";
	res.style.scrollbarHighlightColor = "#EFEFEF";
	res.style.scrollbarShadowColor = "#EFEFEF";
	res.style.scrollbarTrackColor = "#DFDFDF";


	pParent = pParent.appendChild(res);
	res = null;

	return pParent;
}


BXTaskbar.prototype.DisplayElementList = function (arElements, oCont)
{
	this.RemoveElementList(oCont);
	var hi, hlen = arElements.length;
	for (hi = 0; hi < hlen; hi++)
		this.DisplayElement(arElements[hi], oCont);
}


BXTaskbar.prototype.DisplayElement = function (arElement, oCont, orderInd, sPath)
{
	if (orderInd == undefined)
		orderInd = -1;

	if (arElement['isGroup'])
		this.DisplayGroupElement(arElement, oCont, orderInd, sPath);
	else
	{
		if (arElement['thirdlevel'])
		{
			if(this.thirdlevel.name && this.thirdlevel.name != arElement['thirdlevel'])
				this.Display3rdLevelSep(oCont,this.thirdlevel.sPath);
			this.DisplaySingleElement(arElement,oCont,orderInd,sPath);
			this.thirdlevel = {
					name  : arElement['thirdlevel'],
					sPath : sPath
				};
		}
		else
		{
			if(this.thirdlevel.name)
			{
				this.Display3rdLevelSep(oCont,this.thirdlevel.sPath);
				this.thirdlevel = [];
			}
			this.DisplaySingleElement(arElement,oCont,orderInd,sPath);
		}
	}

	if (this.rootElementsCont.parentNode)
		this.rootElementsCont.parentNode.removeChild(this.rootElementsCont);
	oCont.appendChild(this.rootElementsCont);
}


BXTaskbar.prototype.DisplaySingleElement = function (oElement, oCont, orderInd, sPath)
{
	if (sPath==undefined)
		sPath='';

	var _oTable = BX.create('TABLE', {props: {className: 'bxgroupblock1', title: BX_MESS.InsertTitle}});
	_oTable.setAttribute('__bxgroup1', '__' + oElement.name);
	var rowTitle = _oTable.insertRow(-1); //Group header
	//Left cell - icon
	var c = BX.adjust(rowTitle.insertCell(-1), {props: {className: 'iconcell1', unselectable: "on"}});

	var _this = this;
	//*** ICON ***
	ic = this.pMainObj.CreateElement('IMG', {src: oElement.icon || (image_path + '/component.gif')});
	ic.ondragstart = function(){if(window.event) window.event.cancelBubble = true;};
	ic.setAttribute("__bxtagname", oElement.tagname);
	ic.setAttribute("__bxcontainer", BXSerialize(oElement.params));

	if (_this.OnElementClick)
		ic.onclick = function(e){_this.OnElementClick(this, oElement);};

	_oTable.ondblclick = function()
	{
		var _tbl = this;
		var tmpid = 'bx_temp_comp_id';
		_this.pMainObj.insertHTML('<img id="' + tmpid + '" src="' + one_gif_src + '"/>');
		setTimeout(function(){
			var ic = BX.findChild(_tbl, {tagName: 'IMG'}, true);
			if (ic)
			{
				var pEl = _this.pMainObj.pEditorDocument.getElementById(tmpid);
				var newIc = ic.cloneNode(false);
				pEl.parentNode.insertBefore(newIc, pEl);
				pEl.parentNode.removeChild(pEl);
				_this.OnElementDragEnd(newIc);
			}
		}, 100);
	};

	if(BX.browser.IsIE())
	{
		ic.onmousedown = function (e)
		{
			this.id = _this.pMainObj.nLastDragNDropElement = 'tsb_' + Math.random();
		};

		ic.ondragend = function (e)
		{
			_this.pMainObj.nLastDragNDropElementFire = false;
			if (_this.OnElementDragEnd != undefined)
				_this.pMainObj.nLastDragNDropElementFire = _this.OnElementDragEnd;
			_this.pMainObj.OnDragDrop();
		};
	}
	else
	{
		ic.onmousedown = function (e)
		{
			_this.pMainObj.SetFocus();
			this.id = _this.pMainObj.nLastDragNDropElement = 'tsb_' + Math.random();
			_this.pMainObj.nLastDragNDropElementFire = false;
			if (_this.OnElementDragEnd != undefined)
				_this.pMainObj.nLastDragNDropElementFire = _this.OnElementDragEnd;
		};

		ic.ondragend = function (e) // For Firefox 3.5 and later
		{
			_this.pMainObj.nLastDragNDropElementFire = false;
			if (_this.OnElementDragEnd != undefined)
				_this.pMainObj.nLastDragNDropElementFire = _this.OnElementDragEnd;
			_this.pMainObj.OnDragDrop();
		};
	}
	c.appendChild(ic);
	c.id = 'element_' + oElement.name;
	ic = null;

	//*** TITLE ***
	c = rowTitle.insertCell(-1);
	c.style.paddingLeft = '5px';
	c.className = 'titlecell1';
	c.appendChild(document.createTextNode(oElement.title || oElement.name));

	if (sPath=='')
	{
		this.rootElementsCont.appendChild(_oTable);
	}
	else
	{
		var oGroup = this.GetGroup(oCont, sPath);
		var childCell = oGroup.rows[1].cells[0];
		childCell.appendChild(_oTable);
	}
}

BXTaskbar.prototype.Display3rdLevelSep = function (oCont,sPath)
{
	var _oSeparator = document.createElement('TABLE');
	_oSeparator.style.width = BX.browser.IsIE() ? '80%' : '100%';
	_oSeparator.style.height = "1px";
	var _oSepTR = _oSeparator.insertRow(-1);
	var _oSepTD = _oSepTR.insertCell(-1);
	_oSepTD.style.backgroundImage = 'url(' + image_path + '/new_taskbars/point.gif)';

	if (sPath=='')
		oCont.appendChild(_oSeparator);
	else
	{
		var oGroup = this.GetGroup(oCont,sPath);
		var childCell = oGroup.rows[1].cells[0];

		childCell.appendChild(_oSeparator);
	}
	_oSepTD = null;
	_oSepTR = null;
	_oSeparator = null;
}

BXTaskbar.prototype.DisplayGroupElement = function (arElement, oCont, orderInd, sPath)
{
	// create group
	var _this = this;
	if (sPath == undefined)
		sPath = '';

	if (sPath=='')
	{
		//Hight level group
		var _oTable = document.createElement('TABLE');
		oCont.appendChild(_oTable);
		_oTable.cellPadding = 0;
		_oTable.cellSpacing = 0;
		_oTable.width = '100%';
		_oTable.className = 'bxgroupblock0';
		_oTable.setAttribute('__bxgroup0', '__'+arElement.name);

		var rowTitle = _oTable.insertRow(-1); //Group header
		c = rowTitle.insertCell(-1); // Plus/Minus cell
		c.className = 'pluscell0';
		c.unselectable = "on";
		c.style.width = '20px';
		c.style.backgroundImage = 'url(' + image_path + '/new_taskbars/part_l.gif)';
		c.appendChild(this.pMainObj.CreateElement("DIV", {className: 'tskbr_common bx_btn_tabs_plus_small', id: this.pref + 'Group_plus_'+arElement.name}));

		c = rowTitle.insertCell(-1); //Central cell - title
		c.className = 'titlecell0';
		c.unselectable = "on";
		c.innerHTML = BXReplaceSpaceByNbsp((arElement.title) ? arElement.title : arElement.name);
		c.style.backgroundImage = 'url(' + image_path + '/new_taskbars/part_l.gif)';

		var rowData = _oTable.insertRow(-1); // Cell with child elements
		rowData.id = this.pref + 'Group_'+arElement.name;
		rowData.style.display = GetDisplStr(0);
		c = rowData.insertCell(-1);
		c.className = 'datacell0';
		c.colSpan = "2";

		arElement.hidden = true;
		rowTitle._el = arElement;
		rowTitle.onclick = function()
		{
			if (_this.PreBuildList && !_this.fullyLoaded)
			{
				var __this = this;
				_this.pMainObj.oBXWaitWindow.Show();
				setTimeout(function()
				{
					_this.PreBuildList();
					_this.HideGroup(__this._el, !__this._el.hidden, 0);
					_this.pMainObj.oBXWaitWindow.Hide();
				}, 1);
			}
			else
				_this.HideGroup(this._el, !this._el.hidden, 0);
		};

		var len = arElement.childElements.length;
		if (len<=0)
			return;

		for (var i=0; i<len; i++)
			this.DisplayElement(arElement.childElements[i],oCont,-1,arElement.name);
	}
	else
	{
		//1st level subgroup
		if (sPath.indexOf(',')!=-1)
			return;

		try
		{
			var oGroup = this.GetGroup(oCont,sPath);
			var childCell = oGroup.rows[1].cells[0];

			var _oTable = document.createElement('TABLE');
			_oTable.cellPadding = 0;
			_oTable.cellSpacing = 0;
			_oTable.width = '100%';
			_oTable.className = 'bxgroupblock1';

			_oTable.setAttribute('__bxgroup1','__'+arElement.name);

			var rowTitle = _oTable.insertRow(-1); //group title
			var c = rowTitle.insertCell(-1); //plus
			c.unselectable = "on";
			c.style.width = '0%';
			c.className = 'pluscell1';
			c.appendChild(this.pMainObj.CreateElement("IMG", {src: one_gif_src, className: 'tskbr_common bx_btn_tabs_plus_big', id: this.pref + 'Plus_1_icon_'+arElement.name}));

			var c = rowTitle.insertCell(-1); //icon
			c.unselectable = "on";
			c.style.width = '0%';
			c.className = 'iconfoldercell1';

			c.appendChild(this.pMainObj.CreateElement("DIV", {className: 'tskbr_common bx_btn_tabs_folder_c', id: this.pref + 'Folder_1_icon_'+arElement.name}));

			c = rowTitle.insertCell(-1); // title
			c.unselectable = "on";
			c.className = 'titlecell1';
			c.innerHTML = (arElement.title) ? arElement.title : arElement.name;

			var rowData = _oTable.insertRow(-1); //Cell with child elements
			rowData.style.display = GetDisplStr(0);
			rowData.id = this.pref + 'Group_1_'+arElement.name;
			c = rowData.insertCell(-1);
			c.className = 'datacell1';
			c.colSpan = "3";

			rowTitle._el = arElement;
			rowTitle.onclick = function(){_this.HideGroup(this._el,!this._el.hidden,1)};

			childCell.appendChild(_oTable);

			arElement.hidden = true;

			var len = arElement.childElements.length;
			if (len<=0)
				return;
			for (var i=0;i<len;i++)
				this.DisplayElement(arElement.childElements[i],oCont,-1,arElement.name);
		}
		catch(e)
		{
			return false;
		}
	}
	rowTitle = null;
	rowData = null;
	rowBottom = null;
	c = null;
	r = null;
}


//sPath - path  in tree
BXTaskbar.prototype.AddElement = function(oElement, oCont, sPath, orderInd)
{
	if (sPath==undefined)
		sPath = "";
	if (orderInd==undefined)
		orderInd = -1;
	this.DisplayElement(oElement, oCont, orderInd, sPath);
}


BXTaskbar.prototype.RemoveElementList = function (oCont)
{
	oBXEditorUtils.BXRemoveAllChild(oCont);
	return true;
}

BXTaskbar.prototype.RemoveElement = function(elName, oCont, sPath)
{
	if (sPath == "")
	{
		var child, __bxgroup;
		for (var i = 0; i < oCont.childNodes.length; i++)
		{
			child = oCont.childNodes[i];
			__bxgroup = child.getAttribute('__bxgroup');
			if (__bxgroup == '__' + elName)
				oCont.removeChild(child);
		}
	}
	else
	{
		var arPath = sPath.split(',');
		var _len = arPath.length;

		if (_len==0 || _len>1)
			return false;

		for (var iCh = 0;iCh<oCont.childNodes.length;iCh++)
		{
			try
			{
				var grName = oCont.childNodes[iCh].getAttribute('__bxgroup'), row;
				if(grName == '__'+arPath[0])
				{
					_oCont = BX(this.pref + 'Group_'+arPath[0]);
					for (var j=0;j<_oCont.rows.length;j++)
					{
						row = _oCont.rows[j];
						if (row.cells[0].id=='element_'+elName)
							row.parentNode.removeChild(row);
					}
					break;
				}
			}
			catch(e)
			{
				continue;
			}
		}
	}
	return true;
}


BXTaskbar.prototype.HideGroup = function (arElement, bHide, ilevel)
{
	if (ilevel==undefined)
		ilevel = 0;

	if (ilevel==0)
	{
		var im_plus = BX(this.pref + 'Group_plus_'+arElement.name);
		var elementsGroup = BX(this.pref + 'Group_'+arElement.name);
		if(!bHide)
		{
			arElement.hidden = false;
			elementsGroup.style.display = GetDisplStr(1);
			im_plus.className = 'tskbr_common bx_btn_tabs_minus_small';
		}
		else
		{
			arElement.hidden = true;
			elementsGroup.style.display = GetDisplStr(0);
			im_plus.className = 'tskbr_common bx_btn_tabs_plus_small';
		}
	}
	else if(ilevel==1)
	{
		var plusIcon = BX(this.pref + 'Plus_1_icon_'+arElement.name);
		var groupIcon = BX(this.pref + 'Folder_1_icon_'+arElement.name);
		var elementsGroup1 = BX(this.pref + 'Group_1_'+arElement.name);
		if(!bHide)
		{
			arElement.hidden = false;
			elementsGroup1.style.display = GetDisplStr(1);
			plusIcon.className = 'tskbr_common bx_btn_tabs_minus_big';
			groupIcon.className = "tskbr_common bx_btn_tabs_folder_o";
		}
		else
		{
			arElement.hidden = true;
			elementsGroup1.style.display = GetDisplStr(0);
			plusIcon.className = 'tskbr_common bx_btn_tabs_plus_big';
			groupIcon.className = "tskbr_common bx_btn_tabs_folder_c";
		}
	}
}


BXTaskbar.prototype.GetGroup = function(oCont, sPath)
{
	var arPath = sPath.split(',');
	var len = arPath.length, grName, grName2, newCont;
	if (len<=2)
	{
		for (var iCh = 0; iCh < oCont.childNodes.length; iCh++)
		{
			try
			{
				grName = oCont.childNodes[iCh].getAttribute('__bxgroup0');
				if(grName == '__'+arPath[0])
				{
					if (len==1)
						return oCont.childNodes[iCh];
					else
					{
						newCont = oCont.childNodes[iCh].rows[1].cells[0];
						for (var iCh2 = 0; iCh2<newCont.childNodes.length; iCh2++)
						{
							grName2 = newCont.childNodes[iCh2].getAttribute('__bxgroup1');
							if(grName2 == '__'+arPath[1])
								return newCont.childNodes[iCh2];
						}
					}

				}
			}
			catch(e)
			{
				continue;
			}
		}

	}
	return false;
}


BXTaskbar.prototype.insertHTML = function(_html)
{
	this.pMainObj.insertHTML(_html);
}


// ########      BXPropertiesTaskbar      ###############
function BXPropertiesTaskbar()
{
	ar_BXPropertiesTaskbarS.push(this);
	var obj = this;
	obj.bDefault = false;
	obj.emptyInnerHTML = "<br /><span style='padding-left: 15px;'>" + BX_MESS.SelectAnyElement + "</span>";

	BXPropertiesTaskbar.prototype.OnTaskbarCreate = function ()
	{
		this.pMainObj.oPropertiesTaskbar = this;
		this.icon_class = 'tb_icon_properties';
		this.iconDiv.className = 'tb_icon ' + this.icon_class;
		var table = this.pMainObj.pDocument.createElement("TABLE");
		table.style.width = "100%";
		table.style.height = "100%";
		table.cellSpacing = 0;
		table.cellPadding = 0;
		this.pCellPath = table.insertRow(-1).insertCell(-1);
		this.pCellPath.className = "bxproptagspath";
		this.pCellProps = table.insertRow(-1).insertCell(-1);
		this.pCellProps.style.height = "100%";
		this.pCellProps.vAlign = "top";
		this.pDataCell.appendChild(table);

		if (IEplusDoctype)
		{
			this.pCellPath.style.height = "10%";
			this.pCellProps.style.height = "90%";
		}
		else
		{
			this.pCellPath.style.height = "0%";
			this.pCellProps.style.height = "100%";
		}

		this.pCellProps = this.CreateScrollableArea(this.pCellProps);
		this.pCellProps.className = "bxtaskbarprops";
		this.pCellProps.innerHTML = obj.emptyInnerHTML;
		this.pMainObj.AddEventHandler("OnSelectionChange", obj.OnSelectionChange);

		table = null;
	}

	BXPropertiesTaskbar.prototype.OnSelectionChange = function (sReloadControl, pElement)
	{
		try{ // In split mode in IE fast view mode changing occurs Permission denied ERROR
		if (!SETTINGS[obj.pMainObj.name].arTaskbarSettings['BXPropertiesTaskbar'].show)
			return;

		var oSelected, pElementTemp, strPath = '';
		if (pElement)
			oSelected = pElement;
		else
			pElement = oSelected = obj.pMainObj.GetSelectionObject();

		if (pElement && pElement.ownerDocument != obj.pMainObj.pEditorDocument)
		{
			try{
				var pBody = obj.pMainObj.pEditorDocument.body;
				if (pBody.lastChild && !pBody.lastChild.getAttribute('__bxtagname'))
					pElement = pBody.lastChild;
				else
					pElement = pBody.appendChild(obj.pMainObj.pEditorDocument.createElement('BR'));
				obj.pMainObj.SelectElement(pElement);
			}catch(e){}
		}

		if(sReloadControl == "always" || !obj.oOldSelected || !BXElementEqual(oSelected, obj.oOldSelected))
		{
			obj.oOldSelected = oSelected;
			while(obj.pCellPath.childNodes.length > 0)
				obj.pCellPath.removeChild(obj.pCellPath.childNodes[0]);

			var tPath = obj.pMainObj.pDocument.createElement("TABLE");
			tPath.className = "bxproptagspathinl";
			tPath.cellSpacing = 0;
			tPath.cellPadding = 1;
			var
				rPath = tPath.insertRow(-1),
				cPath, pBut, oRange,
				cActiveTag = null,
				fPropertyPanel = null,
				fPropertyPanelElement = null;

			if(obj.pMainObj.pEditorDocument.body.createTextRange)
				oRange = obj.pMainObj.pEditorDocument.body.createTextRange();

			while(pElement && (pElementTemp = pElement.parentNode) != null)
			{
				if(pElementTemp.nodeType !=1 || !pElement.tagName)
				{
					pElement = pElementTemp;
					continue;
				}

				strPath = pElement.tagName.toLowerCase();
				if(pElement.getAttribute("__bxtagname"))
				{
					strPath = pElement.getAttribute("__bxtagname").toLowerCase();
					fPropertyPanel = false;
					tPath.deleteRow(rPath);
					rPath = tPath.insertRow(-1);
				}

				if(strPath == 'tbody')
				{
					pElement = pElementTemp;
					continue;
				}

				cPath = rPath.insertCell(0);
				if(!fPropertyPanel && pPropertybarHandlers[strPath])
				{
					fPropertyPanel = pPropertybarHandlers[strPath];
					fPropertyPanelElement = pElement;
					cActiveTag = cPath;
				}

				cPath.innerHTML = '&lt;' + strPath + '&gt;';
				cPath.pElement = pElement;
				cPath.oRange = oRange;
				cPath.pMainObj = obj.pMainObj;
				cPath.onclick = function ()
				{
					if(this.oRange && this.oRange.moveToElementText)
					{
						this.oRange.moveToElementText(this.pElement);
						this.oRange.select();
					}
					else
					{
						this.pMainObj.pEditorWindow.getSelection().selectAllChildren(this.pElement);
					}
					this.pMainObj.OnEvent("OnSelectionChange");
				};

				pElement = pElementTemp;
			}

			var bDefault = false;
			obj.pCellPath.appendChild(tPath);
			if(!fPropertyPanel)
			{
				fPropertyPanel = pPropertybarHandlers['default'];
				fPropertyPanelElement = oSelected;
				bDefault = true;
			}

			if(cActiveTag)
				cActiveTag.className = 'bxactive-tag';

			if(fPropertyPanelElement && fPropertyPanelElement.tagName && (!(obj.oOldPropertyPanelElement && BXElementEqual(fPropertyPanelElement, obj.oOldPropertyPanelElement)) || sReloadControl == "always"))
			{
				var sRealTag = fPropertyPanelElement.tagName.toLowerCase();
				if(fPropertyPanelElement.getAttribute("__bxtagname"))
					sRealTag = fPropertyPanelElement.getAttribute("__bxtagname").toLowerCase();
				obj.oOldPropertyPanelElement = fPropertyPanelElement;

				var bNew = false;
				if((sReloadControl == "always") || (bDefault && obj.bDefault != bDefault) || (!bDefault && (!obj.sOldTag || obj.sOldTag != sRealTag)))
				{
					obj.pMainObj.OnChange("OnPropertyChange", "");
					bNew = true;
					oBXEditorUtils.BXRemoveAllChild(obj.pCellProps);
				}

				obj.sOldTag = sRealTag;

				if(fPropertyPanel)
					fPropertyPanel(bNew, obj, fPropertyPanelElement);

				obj.pMainObj.OnEvent("OnPropertybarChanged");
				ar_PROP_ELEMENTS.push(obj);
				obj.bDefault = bDefault;
			}
			else
			{
				obj.pCellProps.innerHTML = obj.emptyInnerHTML;
				obj.oOldPropertyPanelElement = {};
				obj.pMainObj.OnEvent("OnPropertybarChanged");
			}
			tPath = rPath = cPath = pBut = null;
		}
		pElement = null;
		oSelected = null;
		}catch(e){}
		return true;
	}
}
oBXEditorUtils.addTaskBar('BXPropertiesTaskbar', 3 , BX_MESS.CompTBProp, []);

function BXCreateTaskbars(pMainObj, bDrawTabs)
{
	var oTB;
	var sortorder = function(obj1, obj2){return obj1.sort - obj2.sort;}
	var _sort = function(arr, func)
	{
		var l = arr.length, tmp, flag = false;
		var i = 0;
		while (i < l - 1)
		{
			if (func(arr[i], arr[i + 1]) > 0)
			{
				tmp = arr[i + 1];
				arr[i + 1] = arr[i];
				arr[i] = tmp;
				i--;
			}
			i++;
		}
	}

	_sort(arBXTaskbars, sortorder);
	var aroTBSet;
	if (!SETTINGS[pMainObj.name].arTaskbarSettings || !pMainObj.RS_taskbars)
		SETTINGS[pMainObj.name].arTaskbarSettings = arTaskbarSettings_default;

	var arTaskbarSettings = SETTINGS[pMainObj.name].arTaskbarSettings;
	var _old_visualEffects = pMainObj.visualEffects;
	pMainObj.visualEffects = false;
	for(var i = 0, l = arBXTaskbars.length; i < l; i++)
	{
		oTB = arBXTaskbars[i];
		if (ar_BXTaskbarS[oTB.name + "_" + pMainObj.name] && (ar_BXTaskbarS[oTB.name+"_"+pMainObj.name].pMainObj.name == pMainObj.name))
			continue;

		aroTBSet = arTaskbarSettings[oTB.name];
		if (!arTaskbarSettings[oTB.name] || !pMainObj.allowedTaskbars[oTB.name])
			continue;

		if ((oTB.arParams['bWithoutPHP'] === false && !pMainObj.arConfig["bWithoutPHP"]) || oTB.arParams['bWithoutPHP'] !== false)
		{
			var pTaskbar = new window[oTB.name]();
			pTaskbar.Create(pMainObj, oTB.title);
			pMainObj.arTaskbarSet[oTB.pos].AddTaskbar(pTaskbar, true);
			pTaskbar.pWnd.style.display = GetDisplStr(0);
		}
	}

	var tbs, l, i;

	for(i = 0, l = pMainObj.arTaskbarSet.length; i < l; i++)
	{
		tbs = pMainObj.arTaskbarSet[i];
		l1 = tbs.arTaskbars.length;
		if (!tbs.arTaskbars[0])
			continue;
		if (l1 > 1)
			setTimeout(function(){try{tbs.ActivateTaskbar(tbs.arTaskbars[0].id);}catch(e){}}, 3);
		else if (l1 == 1)
			tbs.sActiveTaskbar = tbs.arTaskbars[0].id;
	}

	if (!pMainObj.oBXTaskTabs)
		pMainObj.oBXTaskTabs = new BXTaskTabs(pMainObj);
	if (bDrawTabs)
		pMainObj.oBXTaskTabs.Draw();

	BXRefreshTaskbars(pMainObj);

	setTimeout(function(){pMainObj.visualEffects = _old_visualEffects; pMainObj.oBXTaskTabs.Refresh();}, 500);
}

function BXRefreshTaskbars(pMainObj)
{
	var arTskbrSet = SETTINGS[pMainObj.name].arTaskbarSettings;
	var tId, tTitle, BXTaskbar;
	for (var k in ar_BXTaskbarS)
	{
		BXTaskbar = ar_BXTaskbarS[k];

		if (typeof BXTaskbar.name != 'object' || !BXTaskbar.pMainObj || !BXTaskbar.name)
			continue;
		tId = BXTaskbar.name;

		if (BXTaskbar.pMainObj.name != pMainObj.name || !arTskbrSet || !arTskbrSet[tId])
			continue;

		if (arTskbrSet[tId].show && !BXTaskbar.pTaskbarSet && arTskbrSet[tId].docked)
			pMainObj.arTaskbarSet[arTskbrSet[tId].position[0]].AddTaskbar(BXTaskbar, true);
		else if (!arTskbrSet[tId].show && BXTaskbar.pTaskbarSet && arTskbrSet[tId].docked)
			BXTaskbar.pTaskbarSet.DelTaskbar(BXTaskbar, true);
	}
	pMainObj.oBXTaskTabs.Draw();
}


function BXTaskTabs(pMainObj)
{
	this.pMainObj = pMainObj;
	this.cellCreated = false;
};

BXTaskTabs.prototype.DrawTabCell = function()
{
	var pTT = this.pMainObj.pTaskTabs;
	pTT.className = "tasktabcell";
	pTT.style.borderTop = "1px solid #808080";
	this.tabsRow = pTT.appendChild(this.pMainObj.CreateElement("TABLE", {unselectable : "on", cellSpacing : 0, cellPadding : 0})).insertRow(-1);
	this.cellCreated = true;
};

BXTaskTabs.prototype.Draw = function()
{
	var tbs, i, j, l, k, tb, c, r, t;
	var _this = this;
	this.arTabs = [];

	if (this.tabsRow)
		while(this.tabsRow.cells.length > 0)
			this.tabsRow.deleteCell(0);

	var ac = [];
	for(var i = 0, l = this.pMainObj.arTaskbarSet.length; i < l; i++)
	{
		tbs = this.pMainObj.arTaskbarSet[i];
		for(j = 0, k = tbs.arTaskbars.length; j < k; j++)
		{
			if (!this.cellCreated)
				this.DrawTabCell();

			tb = tbs.arTaskbars[j];
			t = this.pMainObj.CreateElement("TABLE", {unselectable: "on", cellSpacing : 0, cellPadding : 0, title: tb.title});
			this.arTabs.push({table: t, tb: tb, tbs: tbs});

			if (t._bx_pushed =  (tbs.sActiveTaskbar == tb.id))
				t.className = "tasktab tasktab_pushed";
			else
				t.className = "tasktab tasktab_def";

			t._bx_tid = tb.id;
			t._bx_tsbinum = tbs.iNum;
			t.onclick = function() {_this.OnClick(this, _this);};
			t.onmouseover =  this.OnMouseOver;
			t.onmouseout = this.OnMouseOut;
			r = t.insertRow(-1);

			c = r.insertCell(-1);
			c.className="tasktab_left";
			//preventselect
			c = r.insertCell(-1);
			c.className = "def tasktab_center";
			c.style.width = "20px";
			var iconDiv = BXCreateElement("DIV", {className: 'tb_icon ' + tb.icon_class}, {}, document);
			c.appendChild(iconDiv);

			c = r.insertCell(-1);

			c.unselectable = "on";

			c.className = "tasktab_center";
			c.innerHTML = c.title = tb.title;

			c = r.insertCell(-1);
			c.className="tasktab_right";

			this.tabsRow.insertCell(-1).appendChild(t);
		}
	}

	this.pMainObj.IEplusDoctypePatchSizes();
};

BXTaskTabs.prototype.Refresh = function()
{
	for(var i = 0, l = this.arTabs.length; i < l; i++)
	{
		var tab = this.arTabs[i];
		var bAct = (tab.tbs.bShowing && tab.tb.id == tab.tbs.sActiveTaskbar);

		if (tab.table._bx_pushed == bAct)
			continue;

		tab.table._bx_pushed = bAct;
		if(bAct)
			tab.table.className = 'tasktab tasktab_pushed';
		else
			tab.table.className = 'tasktab tasktab_def';
	}
	this.pMainObj.IEplusDoctypePatchSizes();
};


BXTaskTabs.prototype.OnClick = function(pObj, _this)
{
	var tbs = _this.pMainObj.arTaskbarSet[pObj._bx_tsbinum];
	var bShow;
	if (pObj._bx_pushed)
	{
		bShow = false;
		pObj.className = "tasktab tasktab_def";
		tbs.Hide();
	}
	else
	{
		bShow = true;
		pObj.className = "tasktab tasktab_pushed";
		if (!tbs.bShowing)
			tbs.Show();
		tbs.ActivateTaskbar(pObj._bx_tid);
	}
	pObj._bx_pushed = bShow;
	SETTINGS[tbs.pMainObj.name].arTBSetsSettings[tbs.iNum].show = bShow;
	tbs.SaveConfiguration();
};

BXTaskTabs.prototype.OnMouseOver = function()
{
	if (this._bx_pushed)
		this.className = "tasktab tasktab_pover";
	else
		this.className = "tasktab tasktab_over";
};

BXTaskTabs.prototype.OnMouseOut = function()
{
	if (this._bx_pushed)
		this.className = "tasktab tasktab_pushed";
	else
		this.className = "tasktab tasktab_def";
};


BXTaskTabs.prototype.GetVPos = function()
{
	var edPos = GetRealPos(this.pMainObj.pWnd);
	vPos = {
		t: parseInt(edPos.bottom) - 25,
		l: parseInt(edPos.left) + 50,
		w: 300,
		h: 25
	};
	return vPos;
};


function BXVisualMinimize(par)
{
	this.oDiv = document.body.appendChild(document.createElement('DIV'));
	this.oDiv.className = 'visual_minimize';
	this.oDiv.style.display = 'none';
}

BXVisualMinimize.prototype.Show = function(par)
{
	par.num = 7;
	par.time = 3;
	var dt = Math.round((par.ePos.t - par.sPos.t) / par.num);
	var dl = Math.round((par.ePos.l - par.sPos.l) / par.num);
	var dw = Math.round((par.ePos.w - par.sPos.w) / par.num);
	var dh = Math.round((par.ePos.h - par.sPos.h) / par.num);
	this.oDiv.style.display = 'block';

	var i = 0, s;
	var _this = this;
	var show = function()
	{
		i++;
		//if (i > par.num)
		if (i > par.num - 1)
		{
			clearInterval(intId);
			_this.oDiv.style.display = 'none';
			if (par.callback)
				par.callback();
			return;
		}

		_this.oDiv.style.top = (par.sPos.t + dt * i) + 'px';
		_this.oDiv.style.left = (par.sPos.l + dl * i) + 'px';
		_this.oDiv.style.width = Math.abs(par.sPos.w + dw * i) + 'px';
		_this.oDiv.style.height = Math.abs(par.sPos.h + dh * i) + 'px';
	};

	var intId = setInterval(show, par.time);
};

function showTranspToggle(arParams)
{
	if (!arParams) return;
	var pMainObj = arParams.pMainObj;
	var e = arParams.e;

	// For main document
	var TranspToggleMove = function(e)
	{
		e = getRealMousePos(e, pMainObj);
		if (arParams.bVertical)
			TranspToggle.style.top = adjustValue(arParams.pos.top, dY + e.realY);
		else
			TranspToggle.style.left = adjustValue(arParams.pos.left, dX + e.realX);
	};

	// For editor document
	var TranspToggleMoveF = function(e)
	{
		e = getRealMousePos(e, pMainObj, true);
		if (arParams.bVertical)
			TranspToggle.style.top = adjustValue(arParams.pos.top, dY + e.realY);
		else
			TranspToggle.style.left = adjustValue(arParams.pos.left, dX + e.realX);
	};

	var adjustValue = function(value, new_value)
	{
		var _cursor = cursor;

		if ((new_value < value) && (value - new_value > maxDiff))
		{
			new_value = value - maxDiff;
			_cursor = "not-allowed";
		}
		else if((new_value > value) && (new_value - value > minDiff))
		{
			new_value = value + minDiff;
			_cursor = "not-allowed";
		}

		if (curCursor != _cursor)
		{
			curCursor = _cursor;
			pBXEventDispatcher.SetCursor(curCursor);
		}

		return new_value + 'px';
	};

	// MouseUp handler
	var TranspToggleMouseUp = function()
	{
		// Clean event handlers
		removeAdvEvent(document, "mousemove", TranspToggleMove, true);
		removeAdvEvent(document, "mouseup", TranspToggleMouseUp, true);
		removeAdvEvent(pMainObj.pEditorDocument, "mousemove", TranspToggleMoveF, true);
		removeAdvEvent(pMainObj.pEditorDocument, "mouseup", TranspToggleMouseUp, true);
		if (BX.browser.IsIE())
		{
			removeAdvEvent(pMainObj.pEditorDocument, "selectstart", preventselect, true);
			removeAdvEvent(document, "selectstart", preventselect, true);
		}

		// Remove toggle
		TranspToggle.style.display = 'none';
		pBXEventDispatcher.SetCursor("default");

		if (arParams.bVertical)
			var value = arParams.value - (parseInt(TranspToggle.style.top) - arParams.pos.top);
		else
			var value = arParams.value - (parseInt(TranspToggle.style.left) - arParams.pos.left);

		if (arParams.callbackObj)
			arParams.callbackFunc.apply(arParams.callbackObj, [value]);
		else
			arParams.callbackFunc(value);
	};

	var maxDiff = arParams.maxValue - arParams.value;
	var minDiff = arParams.value - arParams.minValue;
	e = getRealMousePos(e, pMainObj);

	if (arParams.bVertical)
	{
		var w = parseInt(arParams.width) + "px";
		var h = parseInt(arParams.height ? arParams.height : 6)  + "px";
		var dY = e.realY - parseInt(arParams.pos.top) - 6;
		var cursor = "row-resize";
		var className = "transp_tog_h";
	}
	else
	{
		var h = parseInt(arParams.height) + "px";
		var w = parseInt((arParams.width) ? arParams.width : 6) + "px";;
		var dX = e.realX - parseInt(arParams.pos.left) - 6;
		var cursor = "col-resize";
		var className = "transp_tog_v";
	}
	var curCursor = cursor;
	pBXEventDispatcher.SetCursor(cursor);

	// Create toggle
	var TranspToggle = CACHE_DISPATCHER['TranspToggle'];
	TranspToggle.className = className;
	TranspToggle.style.display = 'block';
	TranspToggle.style.width = w;
	TranspToggle.style.height = h;
	TranspToggle.style.top = arParams.pos.top + "px";
	TranspToggle.style.left = arParams.pos.left + "px";

	addAdvEvent(document, "mousemove", TranspToggleMove, true);
	addAdvEvent(document, "mouseup", TranspToggleMouseUp, true);
	addAdvEvent(pMainObj.pEditorDocument, "mousemove", TranspToggleMoveF, true);
	addAdvEvent(pMainObj.pEditorDocument, "mouseup", TranspToggleMouseUp, true);

	if (BX.browser.IsIE())
	{
		addAdvEvent(pMainObj.pEditorDocument, "selectstart", preventselect, true);
		addAdvEvent(document, "selectstart", preventselect, true);
	}

	if (e.stopPropagandation)
		e.stopPropagandation();
	else
		e.cancelBubble = true;
}


// # # # # # # # # # # # # # # # #    ONE BIG TOOLBAR # # # # # # # # # # # # # # # #
// For lightMode == true
function BXGlobalToolbar(pMainObj)
{
	this.pMainObj = pMainObj;
	this.oCont = BX(this.pMainObj.name+'_toolBarSet0');
	this.oCont.parentNode.style.display = "";
	this.oCont.className = "bxedtoolbarset";
	this.oCont.unselectable = "on";
	var oDiv = document.createElement("DIV");
	oDiv.style.width = '100%';
	this.oCont.appendChild(oDiv);
}

BXGlobalToolbar.prototype.AddButton = function(pButton)
{
	var oDiv = document.createElement("DIV");
	oDiv.className = 'global_toolbar_button';
	oDiv.appendChild(pButton.pWnd);
	this.oCont.firstChild.appendChild(oDiv);
}

BXGlobalToolbar.prototype.LineBegin = function()
{
	var oDiv = document.createElement("DIV");
	oDiv.className = 'global_toolbar_line_begin';
	this.oCont.firstChild.appendChild(oDiv);
}

BXGlobalToolbar.prototype.LineEnd = function()
{
	var oDiv = document.createElement("DIV");
	oDiv.className = 'global_toolbar_line_end';
	this.oCont.firstChild.appendChild(oDiv);
	var br = document.createElement("BR");
	br.style.fontSize = "12px";
	this.oCont.firstChild.appendChild(br);
	var br = document.createElement("BR");
	br.style.fontSize = "12px";
	this.oCont.firstChild.appendChild(br);
}