//Colors of borders and backgrounds for diferent button states
var borderColorNormal = "#e4e2dc";
var borderColorOver = "#4B4B6F";
var borderColorSet = "#4B4B6F";
var borderColorSetOver = "#4B4B6F";

var bgroundColorOver = "#FFC678";
var bgroundColorSet = "#FFC678";
var bgroundColorSetOver = "#FFA658";

// BXButton - class
function BXButton()
{
	ar_BXButtonS.push(this);
	this._prevDisabledState = false;
}

BXButton.prototype = {
_Create: function ()
{
	if(this.OnCreate && this.OnCreate()==false)
		return false;

	var pElement, i, j, obj = this;
	this.className = 'BXButton';

	if (this.id && this.iconkit)
	{
		this.pWnd = this.CreateElement("IMG", {src: one_gif_src, alt: (this.title ? this.title : this.name), title: (this.title?this.title:this.name), width: '20', height: '20', id: "bx_btn_"+obj.id});
		this.pWnd.className = 'bxedtbutton';
		this.pWnd.style.backgroundImage = "url(" + image_path + "/" + this.iconkit + ")";
	}
	else
	{
		this.pWnd = this.CreateElement("IMG", {'src' : this.src, 'alt' : (this.title ? this.title : this.name), 'title': (this.title ? this.title : this.name), 'width' : '20', 'height' : '20'});
		this.pWnd.className = 'bxedtbutton';
	}


	if (this.show_name)
	{
		var _icon = this.pWnd;
		this.pWnd = this.CreateElement("TABLE", {title: (this.title ? this.title: this.name), height: '20', id: "bx_btnex_"+obj.id, cellPadding: 0, cellSpacing: 0});
		this.pWnd.className = 'bxedtbuttonex';
		this.pWnd.checked = false;
		this.pWnd.disabled = false;
		var r = this.pWnd.insertRow(-1);
		var c = r.insertCell(-1);
		c.className = 'tdbutex';
		c.appendChild(_icon);
		var c = r.insertCell(-1);
		c.className = 'tdbutex tdbutex_txt';
		c.style.paddingRight = '4px';
		c.innerHTML = this.name;
	}

	this.pWnd.style.borderColor  = borderColorNormal;
	this.pWnd.style.borderWidth = "1px";
	this.pWnd.style.borderStyle = "solid";

	if(!this.no_actions || this.no_actions != true) // for context menu
	{
		this.pWnd.onmouseover = function(e)
		{
			if(!this.disabled)
			{
				this.style.borderColor = borderColorOver;
				this.style.border = "#4B4B6F 1px solid";
				if(this.checked)
				{
					this.style.backgroundColor = bgroundColorSetOver;
				}
				else
				{
					this.style.backgroundColor = bgroundColorOver;
					if (this.nodeName == 'TABLE')
						this.className = 'bxedtbuttonexover';
				}
			}
		};

		this.pWnd.onmouseout = function(e)
		{
			if(!this.disabled)
			{
				if(this.checked)
				{
					this.style.borderColor = borderColorSet;
					this.style.backgroundColor = bgroundColorSet;
				}
				else
				{
					this.style.backgroundColor ="";
					this.style.borderColor = borderColorNormal;
					if (this.nodeName == 'TABLE')
						this.className = 'bxedtbuttonex';
				}
			}
		};
		if (this.defaultState)
			this.Check(true);

		addCustomElementEvent(this.pWnd, 'click', this.OnClick, this);
		this.pMainObj.AddEventHandler("OnSelectionChange", this._OnSelectionChange, this);
		this.pMainObj.AddEventHandler("OnChangeView", this.OnChangeView, this);
	}
},

_OnChangeView: function (mode, split_mode)
{
	mode = (mode == 'split' ? split_mode : mode);
	if(mode == 'code' && !this.codeEditorMode || (mode=='html' && this.hideInHtmlEditorMode))
	{
		this._prevDisabledState = this.pWnd.disabled;
		this.Disable(true);
	}
	else if(mode == 'code' && this.codeEditorMode || (this.hideInHtmlEditorMode && mode != 'html'))
		this.Disable(false);
	else if(!this.codeEditorMode)
		this.Disable(this._prevDisabledState);
},

OnChangeView: function (mode, split_mode)
{
	this._OnChangeView(mode, split_mode);
},

Disable: function (bFlag)
{
	if(bFlag == this.pWnd.disabled)
		return false;
	this.pWnd.disabled = bFlag;
	if(bFlag)
	{
		if (this.id && this.iconkit)
		{
			this.pWnd.className = 'bxedtbuttondisabled';
			this.pWnd.style.backgroundImage = "url(" + image_path + "/" + this.iconkit + ")";
		}
		else
		{
			this.pWnd.className = 'bxedtbuttondisabled';
		}
		this.pWnd.style.filter = 'gray() alpha(opacity=30)';
	}
	else
	{
		this.pWnd.style.filter = '';
		this.pWnd.className = 'bxedtbutton';
		if(this.pWnd.checked)
		{
			this.pWnd.style.borderColor = borderColorSet;
			this.pWnd.style.backgroundColor = bgroundColorSet;
		}
		else
		{
			this.pWnd.style.backgroundColor ="";
			this.pWnd.style.borderColor = borderColorNormal;
		}
	}
},

Check: function (bFlag)
{
	if(bFlag == this.pWnd.checked)
		return false;
	this.pWnd.checked = bFlag;
	if(!this.pWnd.disabled)
	{
		if(this.pWnd.checked)
		{
			this.pWnd.style.borderColor = borderColorSet;
			this.pWnd.style.backgroundColor = bgroundColorSet;
		}
		else
		{
			this.pWnd.style.backgroundColor ="";
			this.pWnd.style.borderColor = borderColorNormal;
		}
	}
},

OnMouseOver: function (e)
{
	if(!this.disabled)
	{
		this.style.borderColor = borderColorOver;
		this.style.border = "#4B4B6F 1px solid";
		if(this.checked)
			this.style.backgroundColor = bgroundColorSetOver;
		else
			this.style.backgroundColor = bgroundColorOver;
	}
},

OnMouseOut: function (e)
{
	if(!this.disabled)
	{
		if(this.checked)
		{
			this.style.borderColor = borderColorSet;
			this.style.backgroundColor = bgroundColorSet;
		}
		else
		{
			this.style.backgroundColor ="";
			this.style.borderColor = borderColorNormal;
		}
	}
},

OnClick: function (e)
{
	if(this.pWnd.disabled) return false;
	this.pMainObj.SetFocus();
	var res = false;
	if(this.handler)
		if(this.handler() !== false)
			res = true;

	if(!res)
		res = this.pMainObj.executeCommand(this.cmd);

	if(!this.bNotFocus)
		this.pMainObj.SetFocus();

	return res;
},

_OnSelectionChange: function()
{
	if(this.OnSelectionChange)
		this.OnSelectionChange();
	else if(this.cmd)
	{
		var res;

		if(this.cmd=='Unlink' && !BXFindParentByTagName(this.pMainObj.GetSelectionObject(), 'A'))
			res = 'DISABLED';
		else
			res = this.pMainObj.queryCommandState(this.cmd);

		if(res == 'DISABLED')
			this.Disable(true);
		else if(res == 'CHECKED')
		{
			this.Disable(false);
			this.Check(true);
		}
		else
		{
			this.Disable(false);
			this.Check(false);
		}
	}
}
};

function BXButtonSeparator()
{
	ar_BXButtonS.push(this);
}

BXButtonSeparator.prototype._Create = function ()
{
	var pElement, i, j;
	this.className = 'BXButtonSeparator';
	this.pWnd = this.CreateElement("DIV", {className: 'bxseparator'});

	this.OnToolbarChangeDirection = function(bVertical)
	{
		if(bVertical)
		{
			this.pWnd.style.backgroundPosition = "-60px -78px";
			this.pWnd.style.width = "20px";
			this.pWnd.style.height = "2px";
		}
		else
		{
			this.pWnd.style.backgroundPosition = "-58px -60px";
			this.pWnd.style.width = "2px";
			this.pWnd.style.height = "24px";
		}
	};
}


// BXList - class
function BXList()
{
	ar_BXListS.push(this);
	this.className = 'BXList';
	this.iSelectedIndex = -1;
	this.disabled = false;
}

BXList.prototype = {
_Create: function ()
{
	if(this.OnCreate && this.OnCreate()==false)
		return false;

	if(this.OnSelectionChange)
		this.pMainObj.AddEventHandler("OnSelectionChange", this.OnSelectionChange, this);

	if(this.disableOnCodeView)
		this.pMainObj.AddEventHandler("OnChangeView", this.OnChangeView, this);

	this._PreCreate();
	this.SetValues(this.values);

	if(this.OnInit && this.OnInit()==false)
		return false;

	return true;
},

_OnChangeView: function (mode, split_mode)
{
	mode = (mode=='split'?split_mode:mode);
	this.Disable(mode=='code');
},

OnChangeView: function (mode, split_mode)
{
	this._OnChangeView(mode, split_mode);
},

Disable: function(flag)
{
	if(this.disabled==flag)
		return false;
	this.disabled=flag;
	if(flag)
	{
		this.pWnd.className = 'bxlistdisabled';
	}
	else
	{
		this.pWnd.className = 'bxlist';
	}
},

SetValues: function (values)
{
	this.values = values;
	while(this.pDropDownList.childNodes.length>0)
		this.pDropDownList.removeChild(this.pDropDownList.childNodes[0]);

	var r, c, t1, r1, c1;
	for(var i = 0, l = this.values.length; i < l; i++)
	{
		r = this.pDropDownList.insertRow(-1);
		c = r.insertCell(-1);

		t1 = BXPopupWindow.CreateElement("TABLE", {border: '0', cellSpacing: '0', width: '100%', cellPadding: '1', className: 'bxedlistitem'});
		r1 = t1.insertRow(-1);
		c1 = r1.insertCell(-1);
		c1.style.height = "16px";
		c1.style.cursor = "default";
		c1.noWrap = true;
		this.values[i].index = i;
		c1.title = this.values[i].name;
		c1.value = this.values[i];

		c1.style.border = '1px solid #FFFFFF';
		c1.onmouseover = function (e){this.style.border = '1px solid #4B4B6F';};
		c1.onmouseout = function (e){this.style.border = '1px solid #FFFFFF';};
		c1.obj = this;
		c1.onclick = function ()
		{
			BXPopupWindow.Hide();
			this.obj._OnChange(this.value);
			this.obj.FireChangeEvent();
		};
		c1.innerHTML = (this.OnDrawItem) ? this.OnDrawItem(this.values[i]) : this.values[i].name;

		ar_EVENTS.push([c1,"mouseover"]);
		ar_EVENTS.push([c1,"mouseout"]);
		ar_EVENTS.push([c1,"click"]);
		t1.unselectable = "on";
		c.appendChild(t1);
	}

	r1 = c1 = t1 = r = c = null;
},

FireChangeEvent: function()
{
	if(this.OnChange)
		this.OnChange(this.arSelected);
},

_OnChange: function (selected)
{
	this.Select(selected["index"]);
},

SetValue: function(val)
{
	if(!this.pTitle)
		return;

	this.pTitle.innerHTML = val || this.title || '';
},

OnMouseOver: function (e)
{
	if(this.disabled) return false;
	this.pWnd.className = 'bxlist bxlistover';
},

OnMouseOut: function (e)
{
	if(this.disabled) return false;
	this.pWnd.className = 'bxlist';
},

_PreCreate: function ()
{
	obj = this;
	this.pWnd = this.pMainObj.CreateElement("DIV", {'className': 'bxlist', 'border': '0'});
	var pTable = this.pWnd.appendChild(this.pMainObj.CreateElement("TABLE", {'cellPadding': 0, 'cellSpacing': 0, 'border': 0}));
	if (this.field_size)
	{
		this.pWnd.style.width = this.field_size;
		pTable.style.width = this.field_size;
	}

	var row = pTable.insertRow(-1), cell = row.insertCell(-1);
	this.pTitle = this.pMainObj.CreateElement("DIV", {'className': 'bxlisttitle', 'border': '0'});
	this.pTitle.innerHTML = (this.title?this.title:'');
	this.pTitle.unselectable = "on";
	cell.appendChild(this.pTitle);
	this.pTitleCell = cell;

	cell = row.insertCell(-1);
	cell.className = 'bxlistbutton';
	cell.innerHTML = '&nbsp;';
	cell.unselectable = "on";

	addCustomElementEvent(this.pWnd, 'mouseover', this.OnMouseOver, this);
	addCustomElementEvent(this.pWnd, 'mouseout', this.OnMouseOut, this);
	addCustomElementEvent(this.pWnd, 'click', this.OnClick, this);

	if (!BXPopupWindow.bCreated)
		BXPopupWindow.Create();

	this.pPopupNode = BXPopupWindow.CreateElement("DIV", {'border': "0"});
	this.pPopupNode.style.border = "1px solid #A0A0A0";
	this.pPopupNode.style.overflow = "auto";
	this.pPopupNode.style.width = (this.width?this.width:"150px");
	this.pPopupNode.style.overflowX = "hidden";
	this.pPopupNode.style.height = (this.height?this.height:"200px");
	this.pPopupNode.style.overflowY = "auto";
	this.pPopupNode.style.textOverflow = "ellipsis";

	this.pDropDownList = BXPopupWindow.CreateElement("TABLE", {'border': '0', 'width': '100%', 'cellSpacing': '0', 'cellPadding': '0', 'unselectable': 'on'});

	this.pPopupNode.appendChild(this.pDropDownList);

	row = null;
	cell = null;
	pTable = null;
},

OnClick: function (e)
{
	if(this.disabled) return false;

	var arPos = GetRealPos(this.pWnd);
	if(this.bSetGlobalStyles)
		BXPopupWindow.SetCurStyles();
	else
		this.pMainObj.oStyles.SetToDocument(BXPopupWindow.GetDocument());
	BXPopupWindow.Show([arPos["left"], arPos["right"]], [arPos["top"], arPos["bottom"]], this.pPopupNode);
},

Select: function(v)
{
	if(this.iSelectedIndex == v || v >= this.values.length)
		return;
	var sel = this.values[v];
	this.iSelectedIndex = v;
	this.arSelected = sel;
	this.SetValue(sel["name"]);
},

SelectByVal: function(val)
{
	if(val)
	{
		for(var i=0; i < this.values.length; i++)
		{
			if(this.values[i].value == val)
			{
				this.Select(i);
				return;
			}
		}
	}

	this.SetValue(this.title || '');
	this.iSelectedIndex = -1;
},

OnToolbarChangeDirection: function (bVertical)
{
	if(bVertical)
	{
		this.pWnd.style.width = "18px";
		this.pTitleCell.style.visibility = "hidden";
	}
	else
	{
		this.pWnd.style.width = this.field_size;
		this.pTitleCell.style.visibility = "inherit";
	}

	this.pWnd.className = 'bxlist';
}
};

// BXStyleList - class
function BXStyleList(){}

BXStyleList.prototype = new BXList;

BXStyleList.prototype._Create = function ()
{
	this.className = 'BXStyleList';
	this._PreCreate();

	if(this.OnSelectionChange)
		this.pMainObj.AddEventHandler("OnSelectionChange", this.OnSelectionChange, this);

	this.pMainObj.AddEventHandler("OnTemplateChanged", this.FillList, this);

	if(this.disableOnCodeView)
		this.pMainObj.AddEventHandler("OnChangeView", this.OnChangeView, this);

	this.FillList();
};

BXStyleList.prototype.FillList = function()
{
	var i, j, arStyles, l;

	if(!this.filter)
		this._SetFilter();

	while(this.pDropDownList.rows.length>0)
		this.pDropDownList.deleteRow(0);

	this.values = [];
	if(!this.tag_name)
		this.tag_name = '';

	//"clear style" item
	this.__CreateRow('', BX_MESS.DeleteStyleOpt, {'index': this.values.length, 'value': '', 'name': BX_MESS.DeleteStyleOptTitle});

	var style_title, counter = 0, arStyleTitle;
	// other styles
	for(i = 0, l = this.filter.length; i < l;  i++)
	{
		arStyles = this.pMainObj.oStyles.GetStyles(this.filter[i]);
		for(j=0; j<arStyles.length; j++)
		{
			if(arStyles[j].className.length<=0)
				continue;
			arStyleTitle = this.pMainObj.arTemplateParams["STYLES_TITLE"];

			if(this.pMainObj.arTemplateParams && arStyleTitle && arStyleTitle[arStyles[j].className])
				style_title = arStyleTitle[arStyles[j].className];
			else if(!this.pMainObj.arConfig["bUseOnlyDefinedStyles"])
			 	style_title = arStyles[j].className;
			else
			 	continue;

			this.__CreateRow(arStyles[j].className, style_title, {'index': this.values.length, 'value': arStyles[j].className, 'name': style_title});
			counter++;
		}
	}
	if (this.deleteIfNoItems)
		this.pWnd.style.display = (counter == 0) ? "none" : "block";
};

BXStyleList.prototype.__CreateRow = function(className, Name, value)
{
	var r1, c1, t1, r, c;

	r = this.pDropDownList.insertRow(-1);
	c = r.insertCell(-1);

	t1 = BXPopupWindow.CreateElement("TABLE", {'border': '0', 'cellSpacing': '0', 'width': '100%', 'cellPadding': '1'});
	r1 = t1.insertRow(-1);
	c1 = r1.insertCell(-1);
	c1.style.height = "16px";
	c1.style.cursor = "default";
	c1.innerHTML = Name;

	if (styleList_render_style)
	{
		switch(this.tag_name.toUpperCase())
		{
			case "TD":
				c1.className = className;
				break;
			case "TABLE":
				t1.className = className;
				break;
			case "TR":
				r1.className = className;
				break;
			default:
				c1.innerHTML = '<span class="'+className+'">'+Name+'</span>';
		}
	}

	c1.style.border = '1px solid #CCCCCC';
	c1.val = className;
	c1.onmouseover = function (e){this.style.border = '1px solid #000000';};
	c1.onmouseout = function (e){this.style.border = '1px solid #CCCCCC';};
	c1.onclick = function (e){this.obj._OnChange(this.value); this.obj.FireChangeEvent(); BXPopupWindow.Hide(); this.style.border = '1px solid #CCCCCC'; if(this.value.value=='')this.obj.SelectByVal();};
	ar_EVENTS.push([c1,"mouseover"]);
	ar_EVENTS.push([c1,"mouseout"]);
	ar_EVENTS.push([c1,"click"]);
	c1.title = Name;
	t1.unselectable = "on";
	c.appendChild(t1);
	c1.value = value;
	c1.obj = this;
	this.values.push(value);

	r1=null;
	c1=null;
	r=null;
	c=null;
	t1=null;
};

BXStyleList.prototype.OnChange = function(arSelected)
{
	//alert('OnChange');
	this.pMainObj.WrapSelectionWith("span", {"class":arSelected["value"]});
};

BXStyleList.prototype._SetFilter = function()
{
	this.filter = ["DEFAULT"];
};

BXStyleList.prototype.OnClick = function (e)
{
	if(this.disabled)
		return;
	var arPos = GetRealPos(this.pWnd);
	this.pMainObj.oStyles.SetToDocument(BXPopupWindow.GetDocument());
	BXPopupWindow.Show([arPos["left"], arPos["right"]], [arPos["top"], arPos["bottom"]], this.pPopupNode);
};

function BXTransOverlay(arParams)
{
	this.id = 'lhe_trans_overlay';
	this.zIndex = arParams.zIndex || 100;
}

BXTransOverlay.prototype = {
Create: function ()
{
	this.bCreated = true;
	this.bShowed = false;
	var windowSize = BX.GetWindowScrollSize();
	this.pWnd = document.body.appendChild(BX.create("DIV", {props: {id: this.id, className: "bxed-trans-overlay"}, style: {zIndex: this.zIndex, width: windowSize.scrollWidth + "px", height: windowSize.scrollHeight + "px"}}));

	this.pWnd.ondrag = BX.False;
	this.pWnd.onselectstart = BX.False;
},

Show: function(arParams)
{
	if (!this.bCreated)
		this.Create();
	this.bShowed = true;

	var windowSize = BX.GetWindowScrollSize();

	this.pWnd.style.display = 'block';
	this.pWnd.style.width = windowSize.scrollWidth + "px";
	this.pWnd.style.height = windowSize.scrollHeight + "px";

	if (!arParams)
		arParams = {};

	if (arParams.zIndex)
		this.pWnd.style.zIndex = arParams.zIndex;

	BX.bind(window, "resize", BX.proxy(this.Resize, this));
	return this.pWnd;
},

Hide: function ()
{
	if (!this.bShowed)
		return;
	this.bShowed = false;
	this.pWnd.style.display = 'none';
	BX.unbind(window, "resize", BX.proxy(this.Resize, this));
	this.pWnd.onclick = null;
},

Resize: function ()
{
	if (this.bCreated)
		this.pWnd.style.width = BX.GetWindowScrollSize().scrollWidth + "px";
}
};

function BXEdColorPicker()
{
	this.disabled = false;
	this.bCreated = false;
	this.bOpened = false;
	this.zIndex = 2005;

	this.arColors = [
		'#FF0000', '#FFFF00', '#00FF00', '#00FFFF', '#0000FF', '#FF00FF', '#FFFFFF', '#EBEBEB', '#E1E1E1', '#D7D7D7', '#CCCCCC', '#C2C2C2', '#B7B7B7', '#ACACAC', '#A0A0A0', '#959595',
		'#EE1D24', '#FFF100', '#00A650', '#00AEEF', '#2F3192', '#ED008C', '#898989', '#7D7D7D', '#707070', '#626262', '#555', '#464646', '#363636', '#262626', '#111', '#000000',
		'#F7977A', '#FBAD82', '#FDC68C', '#FFF799', '#C6DF9C', '#A4D49D', '#81CA9D', '#7BCDC9', '#6CCFF7', '#7CA6D8', '#8293CA', '#8881BE', '#A286BD', '#BC8CBF', '#F49BC1', '#F5999D',
		'#F16C4D', '#F68E54', '#FBAF5A', '#FFF467', '#ACD372', '#7DC473', '#39B778', '#16BCB4', '#00BFF3', '#438CCB', '#5573B7', '#5E5CA7', '#855FA8', '#A763A9', '#EF6EA8', '#F16D7E',
		'#EE1D24', '#F16522', '#F7941D', '#FFF100', '#8FC63D', '#37B44A', '#00A650', '#00A99E', '#00AEEF', '#0072BC', '#0054A5', '#2F3192', '#652C91', '#91278F', '#ED008C', '#EE105A',
		'#9D0A0F', '#A1410D', '#A36209', '#ABA000', '#588528', '#197B30', '#007236', '#00736A', '#0076A4', '#004A80', '#003370', '#1D1363', '#450E61', '#62055F', '#9E005C', '#9D0039',
		'#790000', '#7B3000', '#7C4900', '#827A00', '#3E6617', '#045F20', '#005824', '#005951', '#005B7E', '#003562', '#002056', '#0C004B', '#30004A', '#4B0048', '#7A0045', '#7A0026'
];
}

BXEdColorPicker.prototype = {
	_Create: function ()
	{
		var _this = this;
		this.pWnd = BX.create("TABLE", {props: {className: 'bx-ed-colorpicker'}});
		var row = this.pWnd.insertRow(-1);
		cell = row.insertCell(-1);

		if(this.OnSelectionChange)
			this.pMainObj.AddEventHandler("OnSelectionChange", this.OnSelectionChange, this);

		if(this.disableOnCodeView)
			this.pMainObj.AddEventHandler("OnChangeView", this.OnChangeView, this);

		if(this.with_input)
		{
			this.pInput = cell.appendChild(BX.create("INPUT", {props: {size: 7}, events: {change: function (){_this._OnChange(this.value);}}}));
			cell = row.insertCell(-1);
		}

		if (!this.id)
			this.id = 'BackColor';

		this.pIcon = cell.appendChild(BX.create("IMG", {props: {id: 'bx_btn_' + this.id, title: this.title, src: one_gif_src, className: "bxedtbutton"}, style:  {border: '1px solid '+borderColorNormal, backgroundImage: "url(" + image_path + "/_global_iconkit.gif)"}}));

		this.pIcon.onclick = function(e){_this.OnClick(e, this)};
		this.pIcon.onmouseover = function(e){_this.OnMouseOver(e, this)};
		this.pIcon.onmouseout = function(e){_this.OnMouseOut(e, this)};
	},

	Create: function ()
	{
		var _this = this;
		this.pColCont = document.body.appendChild(BX.create("DIV", {props: {className: "bx-colpick-cont"}, style: {zIndex: this.zIndex}}));

		var
			row, cell, colorCell,
			tbl = BX.create("TABLE", {props: {className: 'bx-colpic-tbl'}}),
			i, l = this.arColors.length;

		row = tbl.insertRow(-1);
		cell = row.insertCell(-1);
		cell.colSpan = 8;

		var defBut = cell.appendChild(BX.create("SPAN", {props: {className: 'bx-colpic-def-but'}, text: BX_MESS.CPickDef}));
		colorCell = BX.adjust(row.insertCell(-1), {props: {colSpan: 8, className: 'bx-color-inp-cell'}, style: {backgroundColor: this.arColors[38]}});

		defBut.onmouseover = function()
		{
			this.className = 'bx-colpic-def-but bx-colpic-def-but-over';
			colorCell.style.backgroundColor = 'transparent';
		};
		defBut.onmouseout = function(){this.className = 'bx-colpic-def-but';};
		defBut.onclick = function(e){_this.Select(false);}

		for(i = 0; i < l; i++)
		{
			if (Math.round(i / 16) == i / 16) // new row
				row = tbl.insertRow(-1);

			cell = BX.adjust(row.insertCell(-1), {props: {className: 'bx-col-cell', id: 'bx_color_' + i}, html: '<img src="' + one_gif_src + '" />', style: {backgroundColor: this.arColors[i]}});

			cell.onmouseover = function (e)
			{
				this.className = 'bx-col-cell bx-col-cell-over';
				colorCell.style.backgroundColor = _this.arColors[this.id.substring('bx_color_'.length)];
			};
			cell.onmouseout = function (e){this.className = 'bx-col-cell';};
			cell.onclick = function(e){_this.Select(_this.arColors[this.id.substring('bx_color_'.length)]);};
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
			pOverlay = this.pMainObj.oTransOverlay.Show(),
			pos = BX.align(BX.pos(this.pIcon), 325, 155),
			_this = this;

		BX.bind(window, "keypress", BX.proxy(this.OnKeyPress, this));
		oPrevRange = BXGetSelectionRange(this.pMainObj.pEditorDocument, this.pMainObj.pEditorWindow);
		pOverlay.onclick = function(){_this.Close()};

		this.pColCont.style.display = 'block';
		this.pColCont.style.top = pos.top + 'px';
		this.pColCont.style.left = pos.left + 'px';
		this.bOpened = true;
	},

	Close: function ()
	{
		this.pColCont.style.display = 'none';
		this.pMainObj.oTransOverlay.Hide();
		BX.unbind(window, "keypress", BX.proxy(this.OnKeyPress, this));
		this.bOpened = false;
	},

	OnMouseOver: function (e)
	{
		if(!this.disabled)
		{
			this.pIcon.style.borderColor = borderColorOver;
			this.pIcon.style.border = "#4B4B6F 1px solid";
			this.pIcon.style.backgroundColor = bgroundColorOver;
		}
	},

	OnMouseOut: function (e)
	{
		if(!this.disabled)
		{
			this.pIcon.style.backgroundColor = "";
			this.pIcon.style.borderColor = borderColorNormal;
		}
	},

	OnKeyPress: function(e)
	{
		if(!e) e = window.event
		if(e.keyCode == 27)
			this.Close();
	},

	Select: function (color)
	{
		if (!color)
			color = '';

		if(this.pInput)
			this.pInput.value = color;

		BXSelectRange(oPrevRange, this.pMainObj.pEditorDocument, this.pMainObj.pEditorWindow);
		if(this.OnChange)
			this.OnChange(color);

		this.Close();
	},

	OnChangeView: function (mode, split_mode)
	{
		mode = (mode == 'split' ? split_mode : mode);
		this.Disable(mode == 'code');
	},

	Disable: function(bFlag)
	{
		if(bFlag == this.disabled)
			return false;

		this.disabled = this.pIcon.disabled = bFlag;
		if(bFlag)
		{
			this.pIcon.className = 'bxedtbuttondisabled';
			this.pWnd.style.filter = 'gray() alpha(opacity=30)';
		}
		else
		{
			this.pIcon.className = 'bxedtbutton';
			this.pIcon.style.backgroundColor ="";
			this.pIcon.style.borderColor = borderColorNormal;
			this.pWnd.style.filter = '';
		}
	},

	SetValue: function(val)
	{
		if(this.pInput)
			this.pInput.value = val;
	}
};

// BXTAlignPicker - class
function BXTAlignPicker()
{
	this.arIcon = ["tl", "tc", "tr", "cl", "cc", "cr", "bl", "bc", "br"];
	this.arIconH = ["left", "center", "right"];
	this.arIconV = ["top", "middle", "bottom"];
	this.arIconName = [
		BX_MESS.TAlign1, BX_MESS.TAlign2, BX_MESS.TAlign3,
		BX_MESS.TAlign4, BX_MESS.TAlign5, BX_MESS.TAlign6,
		BX_MESS.TAlign7, BX_MESS.TAlign8, BX_MESS.TAlign9];

	BXTAlignPicker.prototype._Create = function ()
	{
		var pElement, i, j, obj = this;
		this.className = 'BXTAlignPicker';
		this.pWnd = this.pMainObj.CreateElement("TABLE", {cellPadding: 0, cellSpacing: 0, border: 0});
		var row = this.pWnd.insertRow(-1), cell = row.insertCell(-1);

		this.pIcon = this.pMainObj.CreateElement("DIV", {id: 'bx_btn_align_tl', className: '', title: this.title}, {width: '20px', height: '20px', border: '1px solid '+borderColorNormal, backgroundImage: "url(" + global_iconkit_path + ")"});
		cell.appendChild(this.pIcon);
		addCustomElementEvent(this.pIcon, 'mouseover', this.OnMouseOver, this);
		addCustomElementEvent(this.pIcon, 'mouseout', this.OnMouseOut, this);
		addCustomElementEvent(this.pIcon, 'click', this.OnClick, this);

		if (!BXPopupWindow.bCreated)
			BXPopupWindow.Create();

		this.pPopupNode = BXPopupWindow.CreateElement("DIV", {'border': "0"});
		this.pPopupNode.style.border = "1px solid #A0A0A0";
		//this.onclick = function (e){BXPopupWindow.Hide();};
		//ar_EVENTS.push([this,"click"]);
		var t = BXPopupWindow.CreateElement("TABLE", {cellSpacing:1, className: 'bxedtalignpicker'});
		t.onclick = function (e){BXPopupWindow.Hide();};
		ar_EVENTS.push([t,"click"]);
		var r = t.insertRow(-1);
		var c = r.insertCell(-1);
		c.innerHTML = '<nobr>'+BX_MESS.TAlignDef+'</nobr>';
		c.className = 'bxedtbutton';
		c.style.border = '1px solid ' + borderColorNormal;
		c.noWrap = true;
		c.onmouseover = function (e)
		{
			this.className = 'bxedtbuttonover';
			this.style.borderColor = borderColorOver;
		};
		c.onmouseout = function (e)
		{
			this.className = 'bxedtbutton';
			this.style.borderColor = borderColorNormal;
		};
		c.onclick = function (e){obj._OnChange('', ''); BXPopupWindow.Hide();};
		ar_EVENTS.push([c,"mouseover"]);
		ar_EVENTS.push([c,"mouseout"]);
		ar_EVENTS.push([c,"click"]);

		r = t.insertRow(-1);
		c = r.insertCell(-1);
		c.style.height = "100%";
		if(!this.type)
			this.type = "default";

		var r1, c1, t1 = BXPopupWindow.CreateElement("TABLE", {'border': '0', 'cellSpacing': '3', 'cellPadding': '0'});
		for(i = 0; i < 3; i++)
		{
			r1 = t1.insertRow(-1);
			if(this.type == 'table')
				i = 1;
			for(j = 0; j < 3; j++)
			{
				c1 = r1.insertCell(-1);
				if(this.type == 'image' && i!=1 && j!=1)
				{
					c1 = c1.appendChild(BXPopupWindow.CreateElement("DIV", {className: 'bxedtbutton'}, {border: '1px solid '+borderColorNormal, backgroundImage: "url(" + global_iconkit_path + ")"}));
				}
				else
				{
					c1 = c1.appendChild(BXPopupWindow.CreateElement("DIV", {id: 'bx_btn_align_'+this.arIcon[i * 3 + j], className: 'bxedtbutton', title: this.arIconName[i * 3 + j]}, {border: '1px solid '+borderColorNormal, backgroundImage: "url(" + global_iconkit_path + ")"}));

					if(this.type == 'image')
					{
						if(j==1)
							c1.val = this.arIconV[i];
						else
							c1.val = this.arIconH[j];
						c1.onclick = function (e){obj._OnChangeI(this.val); BXPopupWindow.Hide(); this.className = 'bxedtbutton';};
					}
					else
					{
						c1.valH = this.arIconH[j];
						c1.valV = this.arIconV[i];
						c1.onclick = function (e){obj._OnChange(this.valH, this.valV); BXPopupWindow.Hide(); this.className = 'bxedtbutton';};
					}
					c1.onmouseover = function (e){
						this.style.borderColor = borderColorOver;
						this.style.border = "#4B4B6F 1px solid";
						this.style.backgroundColor = bgroundColorOver;
					};
					c1.onmouseout = function (e){
						this.style.backgroundColor ="";
						this.style.borderColor = borderColorNormal;
					};
					ar_EVENTS.push([c1,"mouseover"]);
					ar_EVENTS.push([c1,"mouseout"]);
					ar_EVENTS.push([c1,"click"]);
				}
			}
			if(this.type == 'table')
				break;
		}

		c.appendChild(t1);
		this.pPopupNode.appendChild(t);

		c = r = t = c1 = r1 = t1 = row = cell = null;
	}

	BXTAlignPicker.prototype._OnChange = function (valH, valV)
	{
		if(this.OnChange)
			this.OnChange(valH, valV);

		this.SetValue(valH, valV);
	}

	BXTAlignPicker.prototype._OnChangeI = function (val)
	{
		if(this.OnChange)
			this.OnChange(val);

		this.SetValueI(val);
	}

	BXTAlignPicker.prototype.SetValue = function(valH, valV)
	{
		if(this.type == 'image')
			return this.SetValueI(valH);

		for(var j = 0; j < 3; j++)
			if(this.arIconH[j] == valH)
				break;

		for(var i = 0; i < 3; i++)
			if(this.arIconV[i] == valV)
				break;

		if(i > 2) i = 1;
		if(j > 2) j=0;

		this.pIcon.id = "bx_btn_align_"+this.arIcon[i * 3 + j];
		this.pIcon.title = this.arIconName[i * 3 + j];
		return i * 3 + j;
	}

	BXTAlignPicker.prototype.SetValueI = function(val)
	{
		var i, j = 0;
		for(i = 0; i < 3; i++)
			if(this.arIconV[i] == val)
			{
				j = 1;
				break;
			}
		if(j != 1)
			for(j = 0; j < 3; j++)
				if(this.arIconH[j] == val)
				{
					i = 1;
					break;
				}

		if(i > 2) i=1;
		if(j > 2) j=0;

		this.pIcon.id = "bx_btn_align_"+this.arIcon[i * 3 + j];
		this.pIcon.title = this.arIconName[i * 3 + j];
		return i * 3 + j;
	}

	BXTAlignPicker.prototype.OnMouseOver = function (e)
	{
		this.pIcon.style.borderColor = borderColorOver;
		this.pIcon.style.border = "#4B4B6F 1px solid";
		this.pIcon.style.backgroundColor = bgroundColorOver;
	}

	BXTAlignPicker.prototype.OnMouseOut = function (e)
	{
		this.pIcon.style.backgroundColor ="";
		this.pIcon.style.borderColor = borderColorNormal;
	}

	BXTAlignPicker.prototype.OnClick = function (e)
	{
		var arPos = GetRealPos(this.pIcon);
		BXPopupWindow.SetCurStyles();
		BXPopupWindow.Show([arPos["left"], arPos["right"]], [arPos["top"], arPos["bottom"]], this.pPopupNode);
	}
}

function BXCombo(pMainObj, id, pHandler)
{
	this.className = "BXCombo";
	this.id = id;
	this.items = [];
	this.pHandler = pHandler;
	this.pMainObj = pMainObj;

	BXCombo.prototype.AddRow = function(id, value, title, handler)
	{
		this.items[id] = new Object;
		this.items[id].id = id;
		this.items[id].value = value;
		if(handler)
			this.items[id].handler = handler;
		if(title)
			this.items[id].title = title;
		else
			this.items[id].title = value;
	}

	BXCombo.prototype.Show = function()
	{
		var obj = this;
		this.dx = this.params[1];
		this.dy = this.params[2];
		this.title_size = this.params[3];
		this.title_name = this.params[4];

		this.pWnd = this.pMainObj.pDocument.createElement("TABLE");
		this.pWnd.cellPadding = 0;
		this.pWnd.cellSpacing = 0;
		this.pWnd.className = 'bxcombo';
		this.pWnd.unselectable = "on";
		var obwnd = this.pWnd;
		this.pWnd.onmouseover = function (e){obj.pWnd.className = 'bxcomboover'; return false;};
		this.pWnd.onmouseout = function (e){obj.pWnd.className = 'bxcombo';return false;};
		ar_EVENTS.push([this.pWnd,"mouseover"]);
		ar_EVENTS.push([this.pWnd,"mouseout"]);
		var r  = this.pWnd.insertRow(0);
		var c = r.insertCell(0);
		this.title = c.appendChild(this.pMainObj.pDocument.createElement("DIV"));
		this.title.className = 'bxcombotitle';
		this.title.style.width = this.title_size;
		this.title.innerHTML = this.title_name;
		this.title.unselectable = "on";

		var c2 = r.insertCell(1);
		c2.appendChild(this.pMainObj.CreateElement("DIV", {}, {width: '11px', height: '17px', backgroundImage: 'url(' + global_iconkit_path + ')', backgroundPosition: "-64px -63px"}));

		this.pWnd.onclick = function e(){obj.Drop(true)};
		ar_EVENTS.push([this.pWnd,"click"]);
		this.pDropDown = new BXPopup(this.pMainObj, this.pWnd, this.pMainObj.pDocument);
		//ar_OBJECTS.push(this.pDropDown);

		var itemstable = this.pDropDown.pDocument.createElement("TABLE");
		itemstable.style.width = "100%";
		for(var it_id in this.items)
		{
			var item = itemstable.insertRow(-1).insertCell(0);
			item.unselectable = "on";
			item.style.border = "1px #CCCCCC solid";
			item.style.cursor = "default";
			item.onmousemove = function (e)
			{
				this.style.border = "1px #0000FF solid";
			}
			item.onmouseout = function (e)
			{
				this.style.border = "1px #CCCCCC solid";
			}
			item.onclick = function (e)
			{
				this.style.border = "1px #CCCCCC solid";
				obj.title.innerText = this.title;
				obj.Drop(false);
				var id = it_id;
				obj.selectedItem = this.id;
				if(item.handler)
					item.handler();
				else
					obj.OnSelect();
			}
			ar_EVENTS.push([item,"mouseover"]);
			ar_EVENTS.push([item,"mouseout"]);
			ar_EVENTS.push([item,"click"]);
			item.innerHTML = this.items[it_id].value;
			item.title = this.items[it_id].title;
			item.id = this.items[it_id].id;
			item.handler = this.items[it_id].handler;
		}
		this.pDropDown.AddContent(itemstable);

		c2 = null;
		c1 = null;
		r = null;
		item = null;
		itemstable = null;
	}

	BXCombo.prototype.Drop = function(bDrop)
	{
		if(bDrop)
		{
			var pos = GetRealPos(this.pWnd);
			this.pDropDown.Show(pos["left"], pos["bottom"], this.dx, this.dy)
		}
		else
			this.pDropDown.Hide();
		this.pMainObj.SetFocus();
	}

	BXCombo.prototype.OnToolbarChangeDirection = function (bVertical)
	{
		if(bVertical)
			this.title.style.width = "0px";
		else
			this.title.style.width = this.title_size;
		this.pWnd.className = 'bxcombo';
	}
}

function BXTBSelect()
{
	//alert('BXTBSelect');
}

//BXCombo.prototype = new BXToolbarItem;
BXTBSelect.prototype = new BXCombo;
//ar_OBJECTS.push(BXTBSelect.prototype);
//BXTBSelect.prototype = new BXCombo;

function FontStyleListSelect()
{
	var obj = this;
	obj.className = 'FontStyleListSelect';
	obj.items = [];
}
FontStyleListSelect.prototype = new BXTBSelect;
//ar_OBJECTS.push(FontStyleListSelect.prototype);

function FontSizeListSelect()
{
	var obj = this;
	obj.className = 'FontSizeListSelect';
	obj.items = [];
	obj.__Show = obj.Show;
	obj.Show = function ()
	{
		for(var ix=0; ix<obj.params[5].length; ix++)
			obj.AddRow(ix, '<span style="font-size:' + obj.params[5][ix] + ';">' + obj.params[5][ix] + '</span>', obj.params[5][ix]);
		obj.__Show();
	}

	FontSizeListSelect.prototype.OnSelect = function ()
	{
		var item_id = this.selectedItem;
		this.pMainObj.WrapSelectionWith('span', {'style': 'font-size:'+this.items[item_id].title+';'});
		return true;
	}
}

function BXDialog() {}
BXDialog.prototype = {
	_Create: function()
	{
		var _this = this;
		this.bDenyClose = false;
		this.pMainObj._DisplaySourceFrame(true);
		var floatDiv = BX("BX_editor_dialog");
		if(floatDiv)
			this.Close(floatDiv);

		if(!this.params || typeof(this.params) != "object")
			this.params = {};

		this.params.pMainObj = this.pMainObj;
		pObj = window.pObj = this;

		oPrevRange = BXGetSelectionRange(this.pMainObj.pEditorDocument, this.pMainObj.pEditorWindow);
		if(BX("BX_editor_dialog"))
			this.Close();

		var ShowResult = function(result, bFastMode)
		{
			CloseWaitWindow();
			var arDConfig = {
				title : _this.name,
				width: _this.width,
				height: 300,
				resizable: false
			};

			if (bFastMode)
			{
				if (result.title)
					arDConfig.title = result.title;

				if (result.width)
					arDConfig.width = result.width;
				if (result.height)
					arDConfig.height = result.height;

				if (result.resizable)
				{
					arDConfig.resizable = true;
					arDConfig.min_width = result.min_width;
					arDConfig.min_height = result.min_height;
					arDConfig.resize_id = result.resize_id;
				}
			}

			window.oBXEditorDialog = new BX.CDialog(arDConfig);

			if (bFastMode)
			{
				window.oBXEditorDialog.Show();
				window.oBXEditorDialog.SetContent(result.innerHTML);

				if (result.OnLoad && typeof result.OnLoad == 'function')
					result.OnLoad();
			}

		}
		ShowWaitWindow();

		var potRes = this.GetFastDialog();
		if (potRes !== false)
			return ShowResult(potRes, true);

		var
			addUrl = (this.params.PHPGetParams ? this.params.PHPGetParams : '') + '&mode=public&sessid=' + bxsessid + (this.not_use_default ? '&not_use_default=Y' : ''),
			handler = this.handler ? '/bitrix/admin/' + this.handler : editor_dialog_path
			url = handler + '?lang=' + BXLang + '&bxpublic=Y&site=' + BXSite + '&name=' + this.name + addUrl;

		if (_this.params.bUseTabControl)
		{
			window.oBXEditorDialog = new BX.CAdminDialog({
				title : _this.name,
				content_url: url,
				width: _this.width,
				resizable: false
			});
			window.oBXEditorDialog.bUseTabControl = true;
			window.oBXEditorDialog.Show();
		}
		else
		{
			BX.ajax.post(url, {}, ShowResult);
		}
	},

	Close: function(){},

	GetFastDialog: function()
	{
		return window.arEditorFastDialogs[this.name] ? window.arEditorFastDialogs[this.name](this) : false;
	}
}

BXHTMLEditor.prototype.OpenEditorDialog = function(dialogName, obj, width, arParams, notUseDefaultButtons)
{
	this.CreateCustomElement("BXDialog", {width: parseInt(width) || 500, name: dialogName, params: arParams || {}, not_use_default: notUseDefaultButtons});
}