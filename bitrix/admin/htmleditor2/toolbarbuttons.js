var arButtons = [];
arButtons['separator'] = 'separator';
arButtons['Fullscreen']	= ['BXButton',
	{
		id : 'Fullscreen',
		iconkit : '_global_iconkit.gif',
		name : BX_MESS.Fullscreen,
		title : BX_MESS.Fullscreen,
		codeEditorMode : true,
		handler : function()
		{
			this.pMainObj.SetFullscreen(!this.pMainObj.bFullscreen);
			this.Check(this.pMainObj.bFullscreen);
		}
	}
];

arButtons['Settings'] = ['BXButton',
	{
		id : 'Settings',
		iconkit : '_global_iconkit.gif',
		name: BX_MESS.TBSettings,
		title: BX_MESS.TBSettings,
		codeEditorMode: true,
		handler: function()
		{
			this.bNotFocus = true;
			this.pMainObj.OpenEditorDialog("settings", false, 500, {bUseTabControl: true, lightMode: lightMode, PHPGetParams: '&light_mode=' + (lightMode ? 'Y' : 'N')});
		}
	}
];

arButtons['Cut'] = ['BXButton',
	{
		id : 'Cut',
		iconkit : '_global_iconkit.gif',
		name: BX_MESS.Cut,
		cmd: 'Cut',
		hideCondition: function() {return (navigator.appName=='Netscape');}
	}
];

arButtons['Copy'] = ['BXButton',
	{
		id : 'Copy',
		iconkit : '_global_iconkit.gif',
		name: BX_MESS.Copy,
		cmd: 'Copy',
		hideCondition: function() {return (navigator.appName=='Netscape');}
		//hideInFF : true
	}
];

arButtons['Paste'] =['BXButton',
	{
		id : 'Paste',
		iconkit : '_global_iconkit.gif',
		name: BX_MESS.Paste,
		cmd: 'Paste',
		hideCondition: function() {return (navigator.appName=='Netscape');}
		//hideInFF : true
	}
];

arButtons['pasteword'] = ['BXButton',
	{
		id : 'pasteword',
		iconkit : '_global_iconkit.gif',
		name : BX_MESS.PasteAsWord,
		handler : function ()
		{
			this.bNotFocus = true;
			this.pMainObj.OpenEditorDialog("pasteword");
		}
	}
];

arButtons['pastetext'] = ['BXButton',
	{
		id : 'pastetext',
		iconkit : '_global_iconkit.gif',
		name : BX_MESS.PasteAsText,
		handler : function ()
			{
				if(BX.browser.IsIE())
				{
					if (clipboardData.getData("Text"))
						this.pMainObj.PasteAsText(clipboardData.getData("Text"));
				}
				else
				{
					this.bNotFocus = true;
					this.pMainObj.OpenEditorDialog("pasteastext", false, 450);
				}
			}
	}
];

arButtons['SelectAll'] = ['BXButton',
	{
		id : 'SelectAll',
		iconkit : '_global_iconkit.gif',
		name: BX_MESS.SelectAllTitle,
		cmd: 'SelectAll'
	}
];

if(BX.browser.IsIE())
{
	arButtons['Undo'] = ['BXButton',
	{
		id : 'Undo',
		iconkit : '_global_iconkit.gif',
		name : 'Undo',
		title : BX_MESS.Undo,
		OnChangeContent : function(){this.Disable(!this.pMainObj.UndoStatus());},
		OnCreate : function(){this.pMainObj.AddEventHandler("OnChange", this.OnChangeContent, this);},
		handler : function(){this.pMainObj.Undo(1);}
	}
	];

	arButtons['Redo'] = ['BXButton',
	{
		id : 'Redo',
		iconkit : '_global_iconkit.gif',
		name: 'Redo',
		title: BX_MESS.Redo,
		OnChangeContent: function(){this.Disable(!this.pMainObj.RedoStatus());},
		OnCreate:	function(){this.pMainObj.AddEventHandler("OnChange", this.OnChangeContent, this);},
		handler: function(){this.pMainObj.Redo(1);}
	}
	];
}
else
{
	arButtons['Undo'] = ['BXButton',
	{
		id : 'Undo',
		iconkit : '_global_iconkit.gif',
		name : 'Undo',
		title : BX_MESS.Undo,
		cmd : 'Undo'
	}
	];

	arButtons['Redo'] = ['BXButton',
	{
		id : 'Redo',
		iconkit : '_global_iconkit.gif',
		name : 'Redo',
		title : BX_MESS.Redo,
		cmd : 'Redo'
	}
	];
}

arButtons['borders'] = ['BXButton',
	{
		name : BX_MESS.BordersTitle,
		id : 'borders',
		iconkit : '_global_iconkit.gif',
		handler : function ()
			{
				this.pMainObj.ShowTableBorder(!this.pMainObj.bTableBorder);
				this.Check(this.pMainObj.bTableBorder);
			},
		OnCreate : function ()
			{
				var _this = this;
				setTimeout(function(){_this.Check(_this.pMainObj.bTableBorder);}, 10);
				return true;
			}
	}
];

arButtons['table'] = ['BXButton',
	{
		id : 'table',
		iconkit : '_global_iconkit.gif',
		name : BX_MESS.TBInsTable,
		title : BX_MESS.TBInsTable,
		handler : function ()
		{
			this.bNotFocus = true;
			this.pMainObj.OpenEditorDialog("table", false, 450);
		}
	}
];

arButtons['anchor'] = ['BXButton',
	{
		id : 'anchor',
		iconkit : '_global_iconkit.gif',
		name: 'Anchor',
		title: BX_MESS.TBAnchor,
		handler: function ()
		{
			this.bNotFocus = true;
			var p = this.pMainObj.GetSelectionObject();
			if (!p || p.tagName != 'A')
				p = false;

			this.pMainObj.OpenEditorDialog("anchor", p, 340);
		}
	}
];

arButtons['CreateLink'] = ['BXButton',
	{
		id : 'CreateLink',
		iconkit : '_global_iconkit.gif',
		name : BX_MESS.TBLink,
		title : BX_MESS.TBLink,
		handler : function ()
		{
			this.bNotFocus = true;
			this.pMainObj.OpenEditorDialog("link", null, 520);
		}
	}
];

arButtons['deletelink'] = ['BXButton',
	{
		id : 'deletelink',
		iconkit : '_global_iconkit.gif',
		name : BX_MESS.TBDelLink,
		cmd : 'Unlink',
		handler : function()
		{
			var pElement = BXFindParentByTagName(this.pMainObj.GetSelectionObject(), 'A');
			if(pElement)
			{
				this.pMainObj.SelectElement(pElement);
				this.pMainObj.executeCommand('Unlink');
			}
		}
	}
];

arButtons['image'] = ['BXButton',
	{
		id : 'image',
		iconkit : '_global_iconkit.gif',
		name : BX_MESS.TBImg,
		handler : function ()
		{
			this.bNotFocus = true;
			var p = this.pMainObj.GetSelectionObject();
			if (!p || p.tagName != 'IMG')
				p = false;
			this.pMainObj.OpenEditorDialog("image", p, 500);
		}
	}
];

arButtons['SpecialChar'] = ['BXButton',
	{
		id : 'SpecialChar',
		iconkit : '_global_iconkit.gif',
		name : BX_MESS.SpecialCharTitle,
		handler : function ()
		{
			this.bNotFocus = true;
			this.pMainObj.OpenEditorDialog("specialchar", false, 610, {pMainObj:this.pMainObj});
		}
	}
];

arButtons['spellcheck'] = ['BXButton',
	{
		id : 'spellcheck',
		iconkit : '_global_iconkit.gif',
		name : 'spellCheck',
		title : BX_MESS.SpellTitle,
		handler : function ()
		{
			if (!window.spellcheck_js)
			{
				this.pMainObj.oBXWaitWindow.Show();
				BXLoadJSFiles(['spellcheck.js?v='+spellcheck_js_v], {func: this.pMainObj.OnSpellCheck, obj: this.pMainObj});
			}
			else
				this.pMainObj.OnSpellCheck();
		},
		hideCondition: function(pMainObj){return (pMainObj.bDotNet || false);}
	}
];

arButtons['Bold'] = ['BXButton',
	{
		id : 'Bold',
		iconkit : '_global_iconkit.gif',
		name : BX_MESS.TBBold,
		cmd : 'Bold'
	}
];

arButtons['Italic']	= ['BXButton',
	{
		id : 'Italic',
		iconkit : '_global_iconkit.gif',
		name : BX_MESS.TBItalic,
		cmd : 'Italic'
	}
];

arButtons['Underline'] = ['BXButton',
	{
		id : 'Underline',
		iconkit : '_global_iconkit.gif',
		name : BX_MESS.TBUnderline,
		cmd : 'Underline'
	}
];

arButtons['RemoveFormat'] = ['BXButton',
	{
		id : 'RemoveFormat',
		iconkit : '_global_iconkit.gif',
		name : BX_MESS.RemoveFormat,
		cmd : 'RemoveFormat'
	}
];

arButtons['Optimize'] = ['BXButton',
		{
			id : 'Optimize',
			iconkit : '_global_iconkit.gif',
			name : BX_MESS.Optimize,
			handler : function ()
			{
				this.pMainObj.SaveContent();
				this.pMainObj.OnEvent('ClearResourcesBeforeChangeView');
				this.pMainObj.SetEditorContent(this.pMainObj.OptimizeHTML(this.pMainObj.GetContent()));
				this.pMainObj.pEditorFrame.style.display = "none";
				var _this = this.pMainObj;
				setTimeout(function(){_this.pEditorFrame.style.display = "block";}, 50);
			}
		}
	];

// CELL
arButtons['insertcell_before'] = ['BXButton', {
	id : 'insertcell_before',
	iconkit : '_global_iconkit.gif',
	name: BX_MESS.TBInsCellBefore,
	handler: function () {this.pMainObj.TableOperation('cell', 'insert_before');}
}];
arButtons['insertcell_after'] = ['BXButton', {
	id : 'insertcell_after',
	iconkit : '_global_iconkit.gif',
	name: BX_MESS.TBInsCellAfter,
	handler: function () {this.pMainObj.TableOperation('cell', 'insert_after');}
}];
arButtons['deletecell'] = ['BXButton', {
	id : 'deletecell',
	iconkit : '_global_iconkit.gif',
	name: BX_MESS.TBDellCell,
	handler: function () {this.pMainObj.TableOperation('cell', 'delete');}
}];
arButtons['mergecells'] = ['BXButton', {
	id : 'mergecells',
	iconkit : '_global_iconkit.gif',
	name: BX_MESS.TBMergeCell,
	handler: function () {this.pMainObj.TableOperation('cell', 'merge');},
	disablecheck: function (oTable, pMainObj)
	{
		var arCells = pMainObj.getSelectedCells();
		if (arCells.length < 2)
			return true;
		return false;
	}
}];
arButtons['merge_right'] = ['BXButton', {
	id : 'merge_right',
	iconkit : '_global_iconkit.gif',
	name: BX_MESS.TBMergeRight,
	handler: function () {this.pMainObj.TableOperation('cell', 'mergeright');},
	disablecheck: function (oTable, pMainObj)
	{
		var arCells = pMainObj.getSelectedCells();
		if (arCells.length != 1 || !arCells[0].parentNode.cells[arCells[0].cellIndex + 1])
			return true;
		return false;
	}
}];
arButtons['merge_bottom'] = ['BXButton', {
	id : 'merge_bottom',
	iconkit : '_global_iconkit.gif',
	name: BX_MESS.TBMergeBottom,
	handler: function () {this.pMainObj.TableOperation('cell', 'mergebottom');},
	disablecheck: function (oTable, pMainObj)
	{
		var arCells = pMainObj.getSelectedCells();
		if (arCells.length != 1)
			return true;

		var oTR = arCells[0].parentNode;
		if (!oTR.parentNode.rows[oTR.rowIndex + 1])
			return true;
		return false;
	}
}];
arButtons['split_hor'] = ['BXButton', {
	id : 'split_hor',
	iconkit : '_global_iconkit.gif',
	name: BX_MESS.TBSplitCellHor,
	handler: function () {this.pMainObj.TableOperation('cell', 'splithorizontally');},
	disablecheck: function (oTable, pMainObj)
	{
		var arCells = pMainObj.getSelectedCells();
		if (arCells.length != 1)
			return true;
		return false;
	}
}];
arButtons['split_ver'] = ['BXButton', {
	id : 'split_ver',
	iconkit : '_global_iconkit.gif',
	name: BX_MESS.TBSplitCellVer,
	handler: function () {this.pMainObj.TableOperation('cell', 'splitvertically');},
	disablecheck: function (oTable, pMainObj)
	{
		var arCells = pMainObj.getSelectedCells();
		if (arCells.length != 1)
			return true;
		return false;
	}
}];
// ROW
arButtons['insertrow_before'] = ['BXButton', {
	id : 'insertrow_before',
	iconkit : '_global_iconkit.gif',
	name: BX_MESS.TBInsRowUpper,
	handler: function () {this.pMainObj.TableOperation('row', 'insertbefore');}
}];
arButtons['insertrow_after'] = ['BXButton', {
	id : 'insertrow_after',
	iconkit : '_global_iconkit.gif',
	name: BX_MESS.TBInsRowLower,
	handler: function () {this.pMainObj.TableOperation('row', 'insertafter');}
}];
arButtons['mergeallcellsinrow'] = ['BXButton', {
	id : 'mergeallcellsinrow',
	iconkit : '_global_iconkit.gif',
	name: BX_MESS.TBMergeRowCells,
	handler: function () {this.pMainObj.TableOperation('row', 'mergecells');}
}];
arButtons['deleterow'] = ['BXButton', {
	id : 'deleterow',
	iconkit : '_global_iconkit.gif',
	name: BX_MESS.TBDelRow,
	handler: function () {this.pMainObj.TableOperation('row', 'delete');}
}];
// COLUMN
arButtons['insertcolumn_before'] = ['BXButton', {
	id : 'insertcolumn_before',
	iconkit : '_global_iconkit.gif',
	name: BX_MESS.TBInsColLeft,
	handler: function () {this.pMainObj.TableOperation('column', 'insertleft');}
}];
arButtons['insertcolumn_after'] = ['BXButton', {
	id : 'insertcolumn_after',
	iconkit : '_global_iconkit.gif',
	name: BX_MESS.TBInsColRight,
	handler: function () {this.pMainObj.TableOperation('column', 'insertright');}
}];
arButtons['mergeallcellsincolumn'] = ['BXButton', {
	id : 'mergeallcellsincolumn',
	iconkit : '_global_iconkit.gif',
	name: BX_MESS.TBMergeColCells,
	handler: function () {this.pMainObj.TableOperation('column', 'mergecells');},
	disablecheck: function (oTable, pMainObj)
	{
		return false;
		var arCells = pMainObj.getSelectedCells();
		if (arCells.length != 1 || arCells[0].parentNode.rowIndex == arCells[0].parentNode.parentNode.rows.length - 1)
			return true;
		return false;
	}
}];
arButtons['deletecolumn'] = ['BXButton', {
	id : 'deletecolumn',
	iconkit : '_global_iconkit.gif',
	name: BX_MESS.TBDelCol,
	handler: function () {this.pMainObj.TableOperation('column', 'delete');}
}];

arButtons['deltable'] = ['BXButton',
	{
		id : 'deletetable',
		iconkit : '_global_iconkit.gif',
		name: BX_MESS.DeleteTable,
		title: BX_MESS.DeleteTable,
		handler: function ()
		{
			this.pMainObj.bSkipChanges = false;
			var pElement = BXFindParentByTagName(this.pMainObj.GetSelectionObject(), 'TABLE');
			if (pElement && pElement.nodeName.toUpperCase() == 'TABLE')
				pElement.parentNode.removeChild(pElement);
			this.pMainObj.OnChange("table", "deltable");
		}
	}];

arButtons['tableprop'] = ['BXButton',
	{
		id : 'tableprop',
		iconkit : '_global_iconkit.gif',
		name: BX_MESS.TBTableProp,
		title: BX_MESS.TBTableProp,
		handler: function ()
		{
			this.bNotFocus = true;
			var p = BXFindParentByTagName(this.pMainObj.GetSelectionObject(), 'TABLE');
			if (p) this.pMainObj.OpenEditorDialog("table", p, 450, {check_exists: true});
		}
	}];


arButtons['InsertHorizontalRule'] = ['BXButton',
	{
		id : 'InsertHorizontalRule',
		iconkit : '_global_iconkit.gif',
		name : BX_MESS.TBHr,
		handler: function()
		{
			if (BX.browser.IsIE())
				this.pMainObj.pMainObj.executeCommand('InsertHorizontalRule');
			else
				this.pMainObj.insertHTML('<IMG style="padding: 2px; width: 100%; height: 2px;" src="' + image_path + '/break_page.gif" __bxtagname="hr"/>');
		}
	}
];

arButtons['JustifyLeft'] = ['BXButton',
	{
		id : 'JustifyLeft',
		iconkit : '_global_iconkit.gif',
		name : BX_MESS.TBJLeft,
		cmd : 'JustifyLeft'
	}
];

arButtons['JustifyCenter'] = ['BXButton',
	{
		id : 'JustifyCenter',
		iconkit : '_global_iconkit.gif',
		name : BX_MESS.TBJCent,
		cmd : 'JustifyCenter'
	}
];

arButtons['JustifyRight'] = ['BXButton',
	{
		id : 'JustifyRight',
		iconkit : '_global_iconkit.gif',
		name : BX_MESS.TBJRig,
		cmd : 'JustifyRight'
	}
];

arButtons['JustifyFull'] = ['BXButton',
	{
		id : 'JustifyFull',
		iconkit : '_global_iconkit.gif',
		name : BX_MESS.TBJFull,
		cmd : 'JustifyFull'
	}
];

arButtons['InsertOrderedList'] = ['BXButton',
	{
		id : 'InsertOrderedList',
		iconkit : '_global_iconkit.gif',
		name : BX_MESS.TBOList,
		cmd : 'InsertOrderedList'
	}
];

arButtons['InsertUnorderedList'] = ['BXButton',
	{
		id : 'InsertUnorderedList',
		iconkit : '_global_iconkit.gif',
		name : BX_MESS.TBUnOList,
		cmd : 'InsertUnorderedList'
	}
];

arButtons['Outdent'] = ['BXButton',
	{
		id : 'Outdent',
		iconkit : '_global_iconkit.gif',
		name : BX_MESS.TBOutdent,
		cmd : 'Outdent'
	}
];

arButtons['Indent'] = ['BXButton',
	{
		id : 'Indent',
		iconkit : '_global_iconkit.gif',
		name : BX_MESS.TBIndent,
		cmd : 'Indent'
	}
];

arButtons['BackColor'] = ['BXEdColorPicker',
	{
		id : 'BackColor',
		title : BX_MESS.Background_Color,
		disableOnCodeView: true,
		OnChange : function (color)
		{
			if(BX.browser.IsIE())
			{
				this.pMainObj.executeCommand('BackColor', color);
			}
			else
			{
				try{
					this.pMainObj.pEditorDocument.execCommand("styleWithCSS", false, true);
					if (!color)
						this.pMainObj.executeCommand('removeFormat'); //BXClearMozDirtyInRange(this.pMainObj);
					else
						this.pMainObj.executeCommand('hilitecolor', color);

					this.pMainObj.pEditorDocument.execCommand("styleWithCSS", false, false);
				}catch(e){_alert('Error: toolbarbuttons.js: arButtons["BackColor"]');}
			}
		}
	}
];

arButtons['ForeColor'] = ['BXEdColorPicker',
	{
		id : 'ForeColor',
		title : BX_MESS.Foreground_Color,
		disableOnCodeView : true,
		OnChange : function (color)
		{
			if (!color && !BX.browser.IsIE())
				this.pMainObj.executeCommand('removeFormat');
			else
				this.pMainObj.executeCommand('ForeColor', color);
		}
	}
];


var __BXSrcBtn = function (mode, split_mode)
{
	this.Check(mode==this.t);
	this._OnChangeView(mode, split_mode);
}

arButtons['wysiwyg'] = ['BXButton',
	{
		id : 'wysiwyg',
		iconkit : '_global_iconkit.gif',
		codeEditorMode : true,
		name : BX_MESS.TBWysiwyg,
		t : 'html',
		OnChangeView : __BXSrcBtn,
		handler : function () {this.pMainObj.SetView('html');}
	}
];

arButtons['source'] = ['BXButton',
	{
		id : 'source',
		iconkit : '_global_iconkit.gif',
		codeEditorMode : true,
		name : BX_MESS.TBSrc,
		t : 'code',
		OnChangeView : __BXSrcBtn,
		handler : function () {this.pMainObj.SetView('code');}
	}
];

arButtons['split'] = ['BXButton',
	{
		id : 'split',
		iconkit : '_global_iconkit.gif',
		codeEditorMode : true,
		name : BX_MESS.TBSplitmode,
		t : 'split',
		OnChangeView : __BXSrcBtn,
		handler : function () {this.pMainObj.SetView('split');}
	}
];


arButtons['Wrap'] = ['BXButton',
	{
		id : 'Wrap',
		iconkit : '_global_iconkit.gif',
		name : BX_MESS.WrapTitle,
		codeEditorMode : true,
		hideInHtmlEditorMode : true,
		//hideInFF : true,
		hideCondition: function() {return (navigator.appName=='Netscape');},
		defaultState : true,
		handler : function ()
		{
			if (!this.pWnd.checked)
			{
				this.pMainObj.pSourceFrame.wrap = 'soft';
				this.Check(true);
			}
			else
			{
				this.pMainObj.pSourceFrame.wrap = 'OFF';
				this.Check(false);
			}
			this.pMainObj.pSourceFrame.focus();
		}
	}
];


arButtons['HeadingList']	=
	['BXList',
		{width: '150px', height: '150px', field_size: '75px', title: '(' + BX_MESS.Format + ')', disableOnCodeView: true,
			values:
			[
				{value: 'p', name: 'Normal'},
				{value: 'h1', name: 'Heading 1'},
				{value: 'h2', name: 'Heading 2'},
				{value: 'h3', name: 'Heading 3'},
				{value: 'h4', name: 'Heading 4'},
				{value: 'h5', name: 'Heading 5'},
				{value: 'h6', name: 'Heading 6'},
				{value: 'pre', name: 'Preformatted'}
			],
			OnSelectionChange: function (){
					var sel = 0;
					var frm = this.pMainObj.queryCommand('FormatBlock');
					if(frm)
					{
						var re = /[1-6]/;
						var r = frm.match(re);
						if(r>0)
							sel = r;
						else if(frm == 'pre')
							sel = 7;
					}

					this.Select(sel);
				},
			OnChange: function (selected){this.pMainObj.executeCommand('FormatBlock', (selected['value'].length>0?'<' + selected['value']+'>':'<p>'));},
			OnDrawItem: function (item)
			{
				if (!styleList_render_style)
					return item['name'];
				return (item['value'].length <= 0 ? item['name'] : '<'+item['value']+'>'+item['name']+'</'+item['value']+'>');
			}
		}
	];

arButtons['FontName']	=
	['BXList',
		{width: '160px', height: '120px', field_size: '75px', title: '('+BX_MESS.Font+')', disableOnCodeView: true,
			values:
			[
				{value: 'Times New Roman', name: 'Times New Roman'},
				{value: 'Courier', name: 'Courier'},
				{value: 'Arial', name: 'Arial'},
				{value: 'Tahoma', name: 'Tahoma'},
				{value: 'Verdana', name: 'Verdana'},
				{value: 'Georgia', name: 'Georgia'}
			],
			OnSelectionChange: function (){
					this.SelectByVal(this.pMainObj.queryCommand('FontName'));
				},
			OnChange: function (selected){this.pMainObj.executeCommand('FontName', selected['value']);},
			//text-overflow : ellipsis;
			OnDrawItem: function (item){return '<span style="white-space: nowrap; font-family:'+item['name']+';font-size: 10pt;">'+item['name']+'</span>';}
		}
	];

arButtons['FontSize']	=
	['BXList',
		{width: '160px', height: '175px', field_size: '75px', title: '('+BX_MESS.Size+')', disableOnCodeView: true,
			values:
			[
				{value: '1', name: 'xx-small'},
				{value: '2', name: 'x-small'},
				{value: '3', name: 'small'},
				{value: '4', name: 'medium'},
				{value: '5', name: 'large'},
				{value: '6', name: 'x-large'},
				{value: '7', name: 'xx-large'}
			],
			OnSelectionChange: function (){
					this.SelectByVal(this.pMainObj.queryCommand('FontSize'));
				},
			OnChange: function (selected){this.pMainObj.executeCommand('FontSize', selected['value']);},
			OnDrawItem: function (item){return '<font size="'+item['value']+'">'+item['name']+'</font>';}
		}
	];

arButtons['FontStyle'] = ['BXStyleList',
	{
		width: '200px',
		height: '200px',
		field_size: '130px',
		title: '(' + BX_MESS.Style + ')',
		disableOnCodeView: true,
		filter: ['DEFAULT'],
		prevType : false,
		deleteIfNoItems : true,
		OnChangeElement: function (arSelected)
		{
			if (this.pElement.tagName.toUpperCase() == 'BODY')
				this.pElement.innerHTML = '<span class="'+arSelected["value"]+'">'+this.pElement.innerHTML+'</span>';
			else
				SAttr(this.pElement, 'className', arSelected["value"]);
		},
		OnChangeText: function (arSelected)
		{
			if(arSelected["value"] != '')
			{
				this.pMainObj.WrapSelectionWith("span", {"class": arSelected["value"]});
			}
			else
			{
				var tag, pElement = this.pMainObj.GetSelectedNode();
				if(pElement)
				{
					while(pElement = pElement.parentNode)
					{
						if (pElement.nodeType != 1 || !pElement.className || !pElement.tagName)
							continue;

						tag = pElement.tagName.toUpperCase();
						if(tag == 'SPAN' || tag == 'FONT' || tag == 'P' || tag == 'A')
						{
							SAttr(pElement, 'className', '');

							if(pElement.attributes.length <= 0)
								BXCutNode(pElement);
							break;
						}
					}
				}
			}
		},
		OnSelectionChange: function ()
		{
			var pElement = this.pMainObj.GetSelectedNode();
			if(pElement && pElement.nodeType == 1)
			{
				if(this.prevType != pElement.tagName.toUpperCase())
				{
					this.prevType = pElement.tagName.toUpperCase();
					this.tag_name = pElement.tagName.toUpperCase();
					this.filter = [pElement.tagName.toUpperCase(), 'DEFAULT'];
					this.FillList();
				}

				this.pElement = pElement;
				this.OnChange = this.OnChangeElement;
				this.SelectByVal(this.pElement.className);
			}
			else
			{
				this.OnChange = this.OnChangeText;
				if(this.prevType != 'DEFAULT')
				{
					this.prevType = 'DEFAULT';
					this.filter = ['DEFAULT'];
					this.tag_name = '';
					this.FillList();
				}

				this.SelectByVal();
				if(pElement)
				{
					if(BX.browser.IsIE() && pElement.parentElement && (pElement = pElement.parentElement()))
						pElement = pElement.childNodes[0];

					while(pElement = pElement.parentNode)
					{
						if(pElement.nodeType == 1 && pElement.tagName.toUpperCase() == 'TABLE')
							break;
						if(pElement.nodeType == 1 && pElement.className)
						{
							if(pElement.tagName.toUpperCase() != 'SPAN' && pElement.tagName.toUpperCase() != 'FONT')
								break;
							this.SelectByVal(pElement.className);
							break;
						}
					}
				}
			}
		}
	}
];

arButtons['Template'] =
	['BXList',
		{width: '160px', height: '110px', field_size: '150px', title: '('+BX_MESS.Template+')', bSetGlobalStyles: true,
			values: arBXTemplates,
			OnCreate: function ()
			{
				this.pMainObj.pTemplateListbox = this;
			},
			OnInit: function ()
			{
				this.SelectByVal(this.pMainObj.templateID);
			},
			OnChange: function (selected)
			{
				this.pMainObj.LoadTemplateParams(selected['value']);
				if (this.pMainObjpComponent2Taskbar)
					checkComp2Template(this.pMainObj);
			}
		}
	];

// FLASH, BREAK, .....
arButtons['page_break'] = [
	'BXButton',
	{
		id : 'page_break',
		iconkit : '_global_iconkit.gif',
		name : BX_MESS.PAGE_BREAK,
		handler : function ()
		{
			this.pMainObj.insertHTML('<IMG src="' + image_path + '/break_page.gif" style="width:100%; height: 4px" __bxtagname="break_page"/>');
		}
	}
];

arButtons['break_tag'] = [
	'BXButton',
	{
		id : 'break_tag',
		iconkit : '_global_iconkit.gif',
		name : BX_MESS.BREAK_TAG,
		handler : function ()
		{
			this.pMainObj.insertHTML('<IMG src="' + image_path + '/break_tag.gif" __bxtagname="break"/>');
		}
	}
];

arButtons['insert_flash'] = [
	'BXButton',
	{
		id : 'insert_flash',
		iconkit : '_global_iconkit.gif',
		name : BX_MESS.FLASH,
		handler : function () {this.bNotFocus = true; this.pMainObj.OpenEditorDialog("flash", false, 500, {bUseTabControl: true, pMainObj: this.pMainObj});}
	}
];

arButtons['edit_flash'] = [
	'BXButton',
	{
		id : 'insert_flash',
		iconkit : '_global_iconkit.gif',
		name : BX_MESS.FLASH_MOV,
		handler : function ()
		{
			this.bNotFocus = true;
			this.pMainObj.OpenEditorDialog("flash", false, 500, {bUseTabControl: true, pMainObj: this.pMainObj});
		}
	}
];

arButtons['_taskbar_close'] = ['BXButton', {id : '_taskbar_close', iconkit : '_global_iconkit.gif', name :  BX_MESS.Close, title : BX_MESS.Close_toolbar, handler : function (arParams){arParams.pTaskbar.Close();}}];

arButtons['_taskbar_refresh'] = ['BXButton',{id : '_taskbar_refresh', iconkit : '_global_iconkit.gif', name : BX_MESS.RefreshData, title : BX_MESS.RefreshData, handler: function(arParams){arParams.pTaskbar.ClearCache();}}];

arButtons['_taskbar_hide'] = ['BXButton', {id : '_taskbar_hide', iconkit : '_global_iconkit.gif', name : BX_MESS.Hide, title : BX_MESS.Hide_toolbar, handler : function (arParams){arParams.pTaskbar.pTaskbarSet.Hide();}}];

arButtons['_settings'] = ['BXButton', {id : '_settings', iconkit : '_global_iconkit.gif', name : BX_MESS.Settings, title : BX_MESS.Settings_toolbar, handler :function (arParams) {this.pMainObj.OpenEditorDialog("settings", false, 500, {bUseTabControl: true, lightMode: lightMode, PHPGetParams: '&light_mode=' + (lightMode ? 'Y' : 'N')});}}];

var arCMButtons = [];
if (BX.browser.IsIE())
	arCMButtons["DEFAULT"] = [arButtons['Cut'], arButtons['Copy'], arButtons['Paste']];
else
	arCMButtons["DEFAULT"] = []; // FF - operations 'copy', 'cut' and 'paste' are unallowed

arCMButtons["A"] = [arButtons['CreateLink'], arButtons['deletelink']];
arCMButtons["IMG"] = [arButtons['image']];
arCMButtons["FLASH"] = [arButtons['edit_flash']];

arCMButtons["TABLE"] = [
	{
		isgroup : true,
		id : 'table_cell',
		name : BX_MESS.TBInsCell,
		elements : [
			arButtons['insertcell_before'],
			arButtons['insertcell_after'],
			arButtons['deletecell'],
			arButtons['mergecells'],
			arButtons['merge_right'],
			arButtons['merge_bottom'],
			arButtons['split_hor'],
			arButtons['split_ver']
		]
	},
	{
		isgroup : true,
		id : 'table_row',
		name : BX_MESS.TBInsRow,
		elements : [
			arButtons['insertrow_before'],
			arButtons['insertrow_after'],
			arButtons['deleterow'],
			arButtons['mergeallcellsinrow']
		]
	},
	{
		isgroup : true,
		id : 'table_column',
		name : BX_MESS.TBInsColumn,
		elements : [
			arButtons['insertcolumn_before'],
			arButtons['insertcolumn_after'],
			arButtons['deletecolumn'],
			arButtons['mergeallcellsincolumn']
		]
	},
	arButtons['deltable'],
	arButtons['tableprop']
];


arCMButtons["_TASKBAR_DEFAULT"] = [
	arButtons['_taskbar_hide'],
	arButtons['_settings'],
	arButtons['_taskbar_close']
];

arCMButtons["_TASKBAR_CACHED"] = [
	arButtons['_taskbar_hide'],
	arButtons['_taskbar_refresh'],
	arButtons['_settings'],
	arButtons['_taskbar_close']
];

if (!window.arToolbars)
	arToolbars = {};

// *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *
if (BX.browser.IsIE()) // For IE
	arToolbars['standart'] = [
		BX_MESS.TBSStandart,
		[
		arButtons['Fullscreen'], 'separator',
		arButtons['Settings'], arButtons['separator'],
		arButtons['Cut'], arButtons['Copy'], arButtons['Paste'], arButtons['pasteword'], arButtons['pastetext'], arButtons['SelectAll'], arButtons['separator'],
		arButtons['Undo'], arButtons['Redo'], arButtons['separator'],
		arButtons['borders'], 'separator',
		arButtons['table'], arButtons['anchor'], arButtons['CreateLink'], arButtons['deletelink'], arButtons['image'],  'separator',
		arButtons['SpecialChar'], /* arButtons['spellcheck'] */
		]
	];
else // FF - operations 'copy', 'cut' and 'paste' are unallowed
	arToolbars['standart'] = [
		BX_MESS.TBSStandart,
		[
		arButtons['Fullscreen'], 'separator',
		arButtons['Settings'], arButtons['separator'],
		arButtons['pasteword'], arButtons['pastetext'], arButtons['SelectAll'], arButtons['separator'],
		arButtons['Undo'], arButtons['Redo'], arButtons['separator'],
		arButtons['borders'], 'separator',
		arButtons['table'], arButtons['anchor'], arButtons['CreateLink'], arButtons['deletelink'], arButtons['image'], 'separator',
		arButtons['SpecialChar'], /* arButtons['spellcheck'] */
		]
	];
// *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *


arToolbars['style'] = [
	BX_MESS.TBSStyle,
		[arButtons['FontStyle'], arButtons['HeadingList'], arButtons['FontName'], arButtons['FontSize'], arButtons['separator'],
			arButtons['Bold'], arButtons['Italic'], arButtons['Underline'], 'separator',
			arButtons['RemoveFormat'], arButtons['Optimize']
		]
	];

arToolbars['formating'] = [
	BX_MESS.TBSFormat,
			[arButtons['InsertHorizontalRule'], arButtons['separator'],
				arButtons['JustifyLeft'], arButtons['JustifyCenter'], arButtons['JustifyRight'], arButtons['JustifyFull'], arButtons['separator'],
				arButtons['InsertOrderedList'], arButtons['InsertUnorderedList'],arButtons['separator'],
				arButtons['Outdent'], arButtons['Indent'], arButtons['separator'],
				arButtons['BackColor'], arButtons['ForeColor']
			]
	];

arToolbars['source'] = [
		BX_MESS.TBSEdit,
		[arButtons['wysiwyg'], arButtons['source'], arButtons['split'], arButtons['Wrap']]
	];

arToolbars['template'] = [
	BX_MESS.TBSTemplate,
	[arButtons['Template']]
	];

var arDefaultTBPositions = {
		standart: [0, 0, 0],
		template: [0, 0, 2],
		source: [1, 0, 0],
		style: [0, 1, 0],
		formating: [0, 1, 1]
	};


pPropertybarHandlers['table'] = function (bNew, pTaskbar, pElement)
{
	if(bNew)
	{
		pTaskbar.arElements = [];
		var tProp;
		var arBarHandlersCache = pTaskbar.pMainObj.arBarHandlersCache;
		if(arBarHandlersCache['table'])
		{
			var tProp = arBarHandlersCache['table'][0];
			pTaskbar.arElements = arBarHandlersCache['table'][1];
		}
		else
		{
			tProp = pTaskbar.pMainObj.pDocument.createElement("TABLE");
			tProp.className = "bxtaskbarprops";
			tProp.style.width = "100%";
			//tProp.style.height = "100%";
			tProp.cellSpacing = 0;
			tProp.cellPadding = 1;
			var row = tProp.insertRow(-1);

			var cell = row.insertCell(-1); cell.align = 'right';
			cell.appendChild(pTaskbar.pMainObj.CreateElement("SPAN", {'innerHTML':BX_MESS.TPropSize}));

			cell = row.insertCell(-1); cell.noWrap = true;
			pTaskbar.arElements['width'] = cell.appendChild(pTaskbar.pMainObj.CreateElement("INPUT", {'size':'5', 'title': BX_MESS.TPropW, 'type': 'text'}));
			cell.appendChild(pTaskbar.pMainObj.CreateElement("SPAN", {'innerHTML':'x'}));
			pTaskbar.arElements['height'] = cell.appendChild(pTaskbar.pMainObj.CreateElement("INPUT", {'size':'5', 'title': BX_MESS.TPropH, 'type': 'text'}));


			cell = row.insertCell(-1); cell.align = 'right';
			cell.appendChild(pTaskbar.pMainObj.CreateElement("SPAN", {'innerHTML':BX_MESS.TPropBord}));

			cell = row.insertCell(-1);
			pTaskbar.arElements['border'] = cell.appendChild(pTaskbar.pMainObj.CreateElement("INPUT", {'type': 'text', 'size': '5'}));
			////
			row = tProp.insertRow(-1);
			cell = row.insertCell(-1); cell.align = 'right';
			cell.appendChild(pTaskbar.pMainObj.CreateElement("SPAN", {'innerHTML':BX_MESS.TPropStyle}));
			cell = row.insertCell(-1);
			//1
			var pObjTemp = pTaskbar.pMainObj.CreateCustomElement('BXStyleList',
				{	width: '200px',
					height: '200px',
					field_size: '80px',
					title: '(' + BX_MESS.Style + ')',
					tag_name: 'TABLE',
					filter: ['TABLE', 'DEFAULT'],
					OnChangeElement: function (arSelected)
					{
						SAttr(this.pElement, 'className', arSelected["value"]);
					},
					OnChangeText: function (arSelected)
					{
						if(arSelected["value"]!='')
							this.pMainObj.WrapSelectionWith("span", {"class":arSelected["value"]});
						else
						{
							var pElement = this.pMainObj.GetSelectedNode();
							if(pElement)
							{
								while(pElement = pElement.parentNode)
								{
									if(pElement.nodeType == 1 && pElement.tagName.toUpperCase() == 'TABLE')
										break;
									if(pElement.nodeType == 1 && pElement.className)
									{
										if(pElement.tagName.toUpperCase() != 'SPAN' && pElement.tagName.toUpperCase() != 'FONT')
											break;
										SAttr(pElement, 'className', '');
										var attr = pElement.attributes;
										if(attr.length<=0)
											BX.cleanNode(pElement, true);
										break;
									}
								}
							}
						}
					},
					OnSelectionChange: function ()
					{
						var pElement = this.pMainObj.GetSelectedNode();
						if(pElement && pElement.nodeType == 1)
						{
							if(this.prevType != pElement.tagName.toUpperCase())
							{
								this.prevType = pElement.tagName.toUpperCase();
								this.tag_name = pElement.tagName.toUpperCase();
								this.filter = [pElement.tagName.toUpperCase(), 'DEFAULT'];
								this.FillList();
							}

							this.pElement = pElement;
							this.OnChange = this.OnChangeElement;
							this.SelectByVal(this.pElement.className);
						}
						else
						{
							this.OnChange = this.OnChangeText;
							if(this.prevType != 'DEFAULT')
							{
								this.prevType = 'DEFAULT';
								this.filter = ['DEFAULT'];
								this.tag_name = '';
								this.FillList();
							}

							this.SelectByVal();
							if(pElement)
							{
								if(BX.browser.IsIE() && pElement.parentElement && (pElement = pElement.parentElement()))
									pElement = pElement.childNodes[0];

								while(pElement = pElement.parentNode)
								{
									if(pElement.nodeType == 1 && pElement.tagName.toUpperCase() == 'TABLE')
										break;
									if(pElement.nodeType == 1 && pElement.className)
									{
										if(pElement.tagName.toUpperCase() != 'SPAN' && pElement.tagName.toUpperCase() != 'FONT')
											break;
										this.SelectByVal(pElement.className);
										break;
									}
								}
							}
						}
					}
				});
			pTaskbar.arElements['cssclass'] = pObjTemp;
			cell.appendChild(pObjTemp.pWnd);
			pObjTemp = null;

			cell = row.insertCell(-1); cell.align = 'right';
			cell.appendChild(pTaskbar.pMainObj.CreateElement("SPAN", {'innerHTML':BX_MESS.TPropBG}));
			pObjTemp = null;

			cell = row.insertCell(-1);
			pObjTemp = pTaskbar.pMainObj.CreateCustomElement('BXEdColorPicker', {with_input: true});
			pTaskbar.arElements['bgcolor'] = pObjTemp;
			cell.appendChild(pObjTemp.pWnd);
			pObjTemp = null;

			////
			row = tProp.insertRow(-1);
			cell = row.insertCell(-1); cell.align = 'right';
			cell.appendChild(pTaskbar.pMainObj.CreateElement("SPAN", {'innerHTML':'CellPadding: '}));

			cell = row.insertCell(-1);
			pTaskbar.arElements['cellpadding'] = cell.appendChild(pTaskbar.pMainObj.CreateElement("INPUT", {'type': 'text', 'size': '5'}));

			cell = row.insertCell(-1); cell.align = 'right';
			cell.appendChild(pTaskbar.pMainObj.CreateElement("SPAN", {'innerHTML':BX_MESS.TPropAlign}));

			cell = row.insertCell(-1);
			pObjTemp = pTaskbar.pMainObj.CreateCustomElement('BXTAlignPicker', {'type': 'table'});
			pTaskbar.arElements['talign'] = pObjTemp;
			cell.appendChild(pObjTemp.pWnd);

			row = tProp.insertRow(-1);
			cell = row.insertCell(-1); cell.align = 'right';
			cell.appendChild(pTaskbar.pMainObj.CreateElement("SPAN", {'innerHTML':'CellSpacing: '}));

			cell = row.insertCell(-1);
			pTaskbar.arElements['cellspacing'] = cell.appendChild(pTaskbar.pMainObj.CreateElement("INPUT", {'type': 'text', 'size': '5'}));
			cell = row.insertCell(-1);
			cell = row.insertCell(-1);

			arBarHandlersCache['table'] = [tProp, pTaskbar.arElements];
		}
		tProp.id = 'tProp_leak';
		pTaskbar.pCellProps.appendChild(tProp);
		cell = null;
		row = null;
		tProp = null;
	}

	pTaskbar.arElements['width'].value = GAttr(pElement, 'width');
	pTaskbar.arElements['height'].value = GAttr(pElement, 'height');
	pTaskbar.arElements['border'].value = GAttr(pElement, 'border');
	pTaskbar.arElements['cellpadding'].value = GAttr(pElement, 'cellPadding');
	pTaskbar.arElements['cellspacing'].value = GAttr(pElement, 'cellSpacing');
	pTaskbar.arElements['bgcolor'].SetValue(pElement.bgColor);
	pTaskbar.arElements['talign'].SetValue(pElement.align);
	pTaskbar.arElements['cssclass'].SelectByVal(pElement.className);

	var fChange = function (){
		SAttr(pElement, 'width', pTaskbar.arElements['width'].value);
		SAttr(pElement, 'height', pTaskbar.arElements['height'].value);
		SAttr(pElement, 'border', pTaskbar.arElements['border'].value);
		SAttr(pElement, 'cellPadding', pTaskbar.arElements['cellpadding'].value);
		SAttr(pElement, 'cellSpacing', pTaskbar.arElements['cellspacing'].value);
	};

	pTaskbar.arElements['height'].onchange = fChange;
	pTaskbar.arElements['width'].onchange = fChange;
	pTaskbar.arElements['border'].onchange = fChange;
	pTaskbar.arElements['cellpadding'].onchange = fChange;
	pTaskbar.arElements['cellspacing'].onchange = fChange;
	pTaskbar.arElements['bgcolor'].OnChange = function (color) {pElement.bgColor = color;};
	pTaskbar.arElements['talign'].OnChange = function (alH) {pElement.align = alH;};
	pTaskbar.arElements['cssclass'].OnChange = function (className) {pElement.className=className.value;};
}

pPropertybarHandlers['td'] = function (bNew, pTaskbar, pElement)
{
	if(bNew)
	{
		pTaskbar.arElements = [];

		var tProp;
		var arBarHandlersCache = pTaskbar.pMainObj.arBarHandlersCache;
		if(arBarHandlersCache['td'])
		{
			tProp = arBarHandlersCache['td'][0];
			pTaskbar.arElements = arBarHandlersCache['td'][1];
		}
		else
		{
			tProp = pTaskbar.pMainObj.pDocument.createElement("TABLE");
			tProp.className = "bxtaskbarprops";
			tProp.style.width = "100%";
			tProp.cellSpacing = 0;
			tProp.cellPadding = 1;
			var row = tProp.insertRow(-1);

			var cell = row.insertCell(-1); cell.align = 'right';
			oSpan = pTaskbar.pMainObj.CreateElement("SPAN", {'innerHTML':BX_MESS.TPropStyle});
			cell.appendChild(oSpan);
			oSpan = null;

			cell = row.insertCell(-1);

			//2
			var pObjTemp = pTaskbar.pMainObj.CreateCustomElement('BXStyleList',
				{	width: '200px',
					height: '200px',
					field_size: '80px',
					title: '(' + BX_MESS.Style + ')',
					tag_name: 'TD',
					filter: ['TD', 'DEFAULT'],
					OnChangeElement: function (arSelected) {SAttr(this.pElement, 'className', arSelected["value"]);},
					OnChangeText: function (arSelected)
					{
						if(arSelected["value"]!='')
							this.pMainObj.WrapSelectionWith("span", {"class":arSelected["value"]});
						else
						{
							var pElement = this.pMainObj.GetSelectedNode();
							if(pElement)
							{
								while(pElement = pElement.parentNode)
								{
									if(pElement.nodeType == 1 && pElement.tagName.toUpperCase() == 'TABLE')
										break;
									if(pElement.nodeType == 1 && pElement.className)
									{
										if(pElement.tagName.toUpperCase() != 'SPAN' && pElement.tagName.toUpperCase() != 'FONT')
											break;
										SAttr(pElement, 'className', '');
										var attr = pElement.attributes;
										if(attr.length<=0)
											BX.cleanNode(pElement, true);
										break;
									}
								}
							}
						}
					},
					OnSelectionChange: function ()
					{
						var pElement = this.pMainObj.GetSelectedNode();
						if(pElement && pElement.nodeType == 1)
						{
							if(this.prevType != pElement.tagName.toUpperCase())
							{
								this.prevType = pElement.tagName.toUpperCase();
								this.tag_name = pElement.tagName.toUpperCase();
								this.filter = [pElement.tagName.toUpperCase(), 'DEFAULT'];
								this.FillList();
							}

							this.pElement = pElement;
							this.OnChange = this.OnChangeElement;
							this.SelectByVal(this.pElement.className);
						}
						else
						{
							this.OnChange = this.OnChangeText;
							if(this.prevType != 'DEFAULT')
							{
								this.prevType = 'DEFAULT';
								this.filter = ['DEFAULT'];
								this.tag_name = '';
								this.FillList();
							}

							this.SelectByVal();
							if(pElement)
							{
								if(BX.browser.IsIE() && pElement.parentElement && (pElement = pElement.parentElement()))
									pElement = pElement.childNodes[0];

								while(pElement = pElement.parentNode)
								{
									if(pElement.nodeType == 1 && pElement.tagName.toUpperCase() == 'TABLE')
										break;
									if(pElement.nodeType == 1 && pElement.className)
									{
										if(pElement.tagName.toUpperCase() != 'SPAN' && pElement.tagName.toUpperCase() != 'FONT')
											break;
										this.SelectByVal(pElement.className);
										break;
									}
								}
							}
						}
					}
				});
			pTaskbar.arElements['cssclass'] = pObjTemp;
			cell.appendChild(pObjTemp.pWnd);

			cell = row.insertCell(-1); cell.align = 'right';
			var oSpan = pTaskbar.pMainObj.CreateElement("SPAN", {'innerHTML':BX_MESS.TPropSize});
			cell.appendChild(oSpan);
			oSpan = null;

			cell = row.insertCell(-1);
			var oInput = pTaskbar.pMainObj.CreateElement("INPUT", {'size':'5', 'title': BX_MESS.TPropW, 'type': 'text'});
			pTaskbar.arElements['width_val'] = cell.appendChild(oInput);
			oInput = null;
			var oSpan = pTaskbar.pMainObj.CreateElement("SPAN", {'innerHTML':'x'});
			cell.appendChild(oSpan);
			oSpan = null;
			var oInput = pTaskbar.pMainObj.CreateElement("INPUT", {'size':'5', 'title': BX_MESS.TPropH, 'type': 'text'});
			pTaskbar.arElements['height_val'] = cell.appendChild(oInput);
			oInput = null;

			row = tProp.insertRow(-1);
			cell = row.insertCell(-1); cell.align = 'right';
			var oSpan = pTaskbar.pMainObj.CreateElement("SPAN", {'innerHTML':BX_MESS.TPropAlign});
			cell.appendChild(oSpan);
			oSpan = null;

			cell = row.insertCell(-1);
			pObjTemp = pTaskbar.pMainObj.CreateCustomElement('BXTAlignPicker');
			pTaskbar.arElements['talign'] = pObjTemp;
			cell.appendChild(pObjTemp.pWnd);
			pObjTemp = null;

			cell = row.insertCell(-1); cell.align = 'right';
			var oSpan = pTaskbar.pMainObj.CreateElement("SPAN", {'innerHTML':BX_MESS.TPropBG});
			cell.appendChild(oSpan);
			oSpan = null;

			cell = row.insertCell(-1);
			pObjTemp = pTaskbar.pMainObj.CreateCustomElement('BXEdColorPicker', {'with_input': true});
			pTaskbar.arElements['bgcolor'] = pObjTemp;
			cell.appendChild(pObjTemp.pWnd);
			pObjTemp = null;

			row = tProp.insertRow(-1);
			cell = row.insertCell(-1); cell.align = 'right';
			var oSpan = pTaskbar.pMainObj.CreateElement("SPAN", {'innerHTML': BX_MESS.TPropNoWrap+':'});
			cell.appendChild(oSpan);
			oSpan = null;

			cell = row.insertCell(-1);
			var oInput = pTaskbar.pMainObj.CreateElement("INPUT", {'title': BX_MESS.TPropNoWrap, 'type': 'checkbox'});
			pTaskbar.arElements['nowrap'] = cell.appendChild(oInput);
			oInput = null;

			arBarHandlersCache['td'] = [tProp, pTaskbar.arElements];
		}
		tProp.id = 'tProp_td_leak';
		pTaskbar.pCellProps.appendChild(tProp);

		pObjTemp = null;
		cell = null;
		row = null
		tProp = null;
	}

	pTaskbar.arElements['width_val'].value = pElement.width;
	pTaskbar.arElements['height_val'].value = pElement.height;
	pTaskbar.arElements['nowrap'].checked = pElement.noWrap;
	pTaskbar.arElements['bgcolor'].SetValue(pElement.bgColor);
	pTaskbar.arElements['talign'].SetValue(pElement.align, pElement.vAlign);
	pTaskbar.arElements['cssclass'].SelectByVal(pElement.className);

	var fChange = function ()
	{
		pElement.width = pTaskbar.arElements['width_val'].value;
		pElement.height = pTaskbar.arElements['height_val'].value;
		pElement.noWrap = pTaskbar.arElements['nowrap'].checked;
	};

	pTaskbar.arElements['height_val'].onchange = fChange;
	pTaskbar.arElements['width_val'].onchange = fChange;
	pTaskbar.arElements['nowrap'].onclick = fChange;
	pTaskbar.arElements['bgcolor'].OnChange = function (color) {pElement.bgColor = color;};
	pTaskbar.arElements['talign'].OnChange = function (alH, alV) {pElement.align = alH; pElement.vAlign = alV;};
	pTaskbar.arElements['cssclass'].OnChange = function (className) {pElement.className = className.value;};
}

pPropertybarHandlers['a'] = function (bNew, pTaskbar, pElement)
{
	pTaskbar.pHtmlElement = pElement;
	var GAttrEx = function(pEl, atrName, atrNameEX){return _GAttrEx(pEl, atrName, atrNameEX,pTaskbar);}
	var SAttrEx = function(pEl,atrName,atrNameEX,val){return _SAttrEx(pEl,atrName,atrNameEX,val,pTaskbar);}

	if(bNew)
	{
		pTaskbar.arElements = [];

		var tProp;
		var arBarHandlersCache = pTaskbar.pMainObj.arBarHandlersCache;

		if(arBarHandlersCache['a'])
		{
			tProp = arBarHandlersCache['a'][0];
			pTaskbar.arElements = arBarHandlersCache['a'][1];
		}
		else
		{
			tProp = pTaskbar.pMainObj.CreateElement("TABLE");
			tProp.className = "bxtaskbarprops";
			tProp.style.width = "100%";
			tProp.cellSpacing = 0;
			tProp.cellPadding = 1;
			var row, cell;

			row = tProp.insertRow(-1); cell = row.insertCell(-1); cell.align = 'right';
			cell.appendChild(pTaskbar.pMainObj.CreateElement("SPAN", {'innerHTML':'URL: '}));

			cell = row.insertCell(-1);
			pTaskbar.arElements['href_val'] = cell.appendChild(pTaskbar.pMainObj.CreateElement("INPUT", {'size':'40', 'title': BX_MESS.TPropURL, 'type': 'text'}));

			cell = row.insertCell(-1); cell.align = 'right';
			cell.appendChild(pTaskbar.pMainObj.CreateElement("SPAN", {'innerHTML':'Title: '}));

			cell = row.insertCell(-1);
			pTaskbar.arElements['title'] = cell.appendChild(pTaskbar.pMainObj.CreateElement("INPUT", {'size':'30', 'title': BX_MESS.TPropTitle, 'type': 'text'}));

			row = tProp.insertRow(-1); cell = row.insertCell(-1); cell.align = 'right';
			cell.appendChild(pTaskbar.pMainObj.CreateElement("SPAN", {'innerHTML':BX_MESS.TPropTarget}));

			cell = row.insertCell(-1);
			var tTemp = cell.appendChild(pTaskbar.pMainObj.CreateElement("TABLE", {'cellPadding': '0', 'cellSpacing': '0'}));

			pTaskbar.arElements['target_list'] = pTaskbar.pMainObj.CreateCustomElement(
				"BXList",
				{width: '150px', height: '80px', field_size: '130px', bSetGlobalStyles: true, taskbar: pTaskbar,
				values:
				[
					{value: '', 'name': '&nbsp;'},
					{value: '_blank', 'name': BX_MESS.TPropTargetBlank},
					{value: '_parent', 'name': BX_MESS.TPropTargetParent},
					{value: '_self', 'name': BX_MESS.TPropTargetSelf},
					{value: '_top', 'name': BX_MESS.TPropTargetTop}
				],
				OnSelectionChange: function (){return; this.Select(sel);},
				OnChange: function (selected){
					this.taskbar.arElements['target'].disabled = (selected['value']!='');
					this.taskbar.arElements['target'].value = selected['value'];
					this.taskbar.pHtmlElement.target = selected['value'];
				}
			});
			tTemp.insertRow(-1).insertCell(-1).appendChild(pTaskbar.arElements['target_list'].pWnd);
			pTaskbar.arElements['target'] = tTemp.rows[0].insertCell(-1).appendChild(pTaskbar.pMainObj.CreateElement("INPUT", {'size':'13', 'title': BX_MESS.TPropTargetWin, 'type': 'text'}));

			cell = row.insertCell(-1); cell.align = 'right';
			cell.appendChild(pTaskbar.pMainObj.CreateElement("SPAN", {'innerHTML':BX_MESS.TPropStyle}));

			cell = row.insertCell(-1);
			//3
			var pObjTemp = pTaskbar.pMainObj.CreateCustomElement('BXStyleList',
				{	width: '200px',
					height: '200px',
					field_size: '120px',
					title: '(' + BX_MESS.Style + ')',
					tag_name: 'A',
					filter: ['A', 'DEFAULT'],
					OnChangeElement: function (arSelected) {SAttr(this.pElement, 'className', arSelected["value"]);},
					OnChangeText: function (arSelected)
					{
						if(arSelected["value"]!='')
							this.pMainObj.WrapSelectionWith("span", {"class":arSelected["value"]});
						else
						{
							var pElement = this.pMainObj.GetSelectedNode();
							if(pElement)
							{
								while(pElement = pElement.parentNode)
								{
									if(pElement.nodeType == 1 && pElement.tagName.toUpperCase() == 'TABLE')
										break;
									if(pElement.nodeType == 1 && pElement.className)
									{
										if(pElement.tagName.toUpperCase() != 'SPAN' && pElement.tagName.toUpperCase() != 'FONT')
											break;
										SAttr(pElement, 'className', '');
										var attr = pElement.attributes;
										if(attr.length<=0)
											BX.cleanNode(pElement, true);
										break;
									}
								}
							}
						}
					},
					OnSelectionChange: function ()
					{
						var pElement = this.pMainObj.GetSelectedNode();
						if(pElement && pElement.nodeType == 1)
						{
							if(this.prevType != pElement.tagName.toUpperCase())
							{
								this.prevType = pElement.tagName.toUpperCase();
								this.tag_name = pElement.tagName.toUpperCase();
								this.filter = [pElement.tagName.toUpperCase(), 'DEFAULT'];
								this.FillList();
							}

							this.pElement = pElement;
							this.OnChange = this.OnChangeElement;
							this.SelectByVal(this.pElement.className);
						}
						else
						{
							this.OnChange = this.OnChangeText;
							if(this.prevType != 'DEFAULT')
							{
								this.prevType = 'DEFAULT';
								this.filter = ['DEFAULT'];
								this.tag_name = '';
								this.FillList();
							}

							this.SelectByVal();
							if(pElement)
							{
								if(BX.browser.IsIE() && pElement.parentElement && (pElement = pElement.parentElement()))
									pElement = pElement.childNodes[0];

								while(pElement = pElement.parentNode)
								{
									if(pElement.nodeType == 1 && pElement.tagName.toUpperCase() == 'TABLE')
										break;
									if(pElement.nodeType == 1 && pElement.className)
									{
										if(pElement.tagName.toUpperCase() != 'SPAN' && pElement.tagName.toUpperCase() != 'FONT')
											break;
										this.SelectByVal(pElement.className);
										break;
									}
								}
							}
						}
					}
				});

			pTaskbar.arElements['cssclass'] = pObjTemp;
			cell.appendChild(pObjTemp.pWnd);

			arBarHandlersCache['a'] = [tProp, pTaskbar.arElements];
		}

		pTaskbar.pCellProps.appendChild(tProp);
	}

	pTaskbar.arElements['cssclass'].SelectByVal(pElement.className);
	pTaskbar.arElements['target_list'].SelectByVal(pElement.target);
	pTaskbar.arElements['href_val'].value = GAttrEx(pElement, '__bxhref', '__bx_ex_href');
	pTaskbar.arElements['title'].value = GAttrEx(pElement, 'title', '__bx_ex_title');
	pTaskbar.arElements['target'].value = pElement.target;
	pTaskbar.arElements['target'].disabled = !(pElement.target!='_blank' && pElement.target!='_top' && pElement.target!='_self' && pElement.target!='_parent');

	var fChange = function ()
	{
		SAttrEx(pElement, '__bxhref', '__bx_ex_href', pTaskbar.arElements['href_val'].value);
		SAttrEx(pElement, 'title', '__bx_ex_title', pTaskbar.arElements['title'].value);
		SAttr(pElement, 'target', pTaskbar.arElements['target'].value);
	};

	pTaskbar.arElements['href_val'].onchange = fChange;
	pTaskbar.arElements['title'].onchange = fChange;
	pTaskbar.arElements['target'].onchange = fChange;
	pTaskbar.arElements['cssclass'].OnChange = function (sel) {pElement.className=sel['value'];};
};

pPropertybarHandlers['anchor'] = function (bNew, pTaskbar, pElement)
{
	pTaskbar.pHtmlElement = pElement;
	if(bNew)
	{
		pTaskbar.arElements = [];
		var tProp;
		var arBarHandlersCache = pTaskbar.pMainObj.arBarHandlersCache;
		if(arBarHandlersCache['anchor'])
		{
			tProp = arBarHandlersCache['anchor'][0];
			pTaskbar.arElements = arBarHandlersCache['anchor'][1];
		}
		else
		{
			tProp = pTaskbar.pMainObj.CreateElement("TABLE");
			tProp.className = "bxtaskbarprops";
			tProp.style.width = "100%";
			tProp.cellSpacing = 0;
			tProp.cellPadding = 1;
			var row, cell;

			row = tProp.insertRow(-1); cell = row.insertCell(-1); cell.align = 'right'; cell.width="50%";
			cell.appendChild(pTaskbar.pMainObj.CreateElement("SPAN", {'innerHTML':BX_MESS.TPropName}));

			cell = row.insertCell(-1); cell.width="50%";
			pTaskbar.arElements['name'] = cell.appendChild(pTaskbar.pMainObj.CreateElement("INPUT", {'size':'40', 'title': BX_MESS.TPropAnchorName, 'type': 'text'}));

			arBarHandlersCache['anchor'] = [tProp, pTaskbar.arElements];
		}

		pTaskbar.pCellProps.appendChild(tProp);
	}

	var val = BXUnSerialize(pElement.getAttribute("__bxcontainer"));
	pTaskbar.arElements['name'].value = pTaskbar.pMainObj.pParser.GetSetAnchorName(val.html);
	//pTaskbar.arElements['name'].value = val["name"];

	var fChange = function ()
	{
		var
			val = BXUnSerialize(pElement.getAttribute("__bxcontainer")),
			html = pTaskbar.pMainObj.pParser.GetSetAnchorName(val.html, pTaskbar.arElements.name.value);

		pElement.setAttribute("__bxcontainer", BXSerialize({"html": html}));
	};

	pTaskbar.arElements['name'].onchange = fChange;
};

pPropertybarHandlers['img'] = function (bNew, pTaskbar, pElement)
{
	var GAttrEx = function(pEl, atrName, atrNameEX){return _GAttrEx(pEl, atrName, atrNameEX, pTaskbar);}
	var SAttrEx = function(pEl, atrName, atrNameEX, val){return _SAttrEx(pEl, atrName, atrNameEX, val, pTaskbar);}
	if(bNew)
	{
		pTaskbar.arElements = [];
		var tProp;
		var arBarHandlersCache = pTaskbar.pMainObj.arBarHandlersCache;
		var arBarHandlersCache = [];

		if(arBarHandlersCache['img'])
		{
			tProp = arBarHandlersCache['img'][0];
			pTaskbar.arElements = arBarHandlersCache['img'][1];
		}
		else
		{
			tProp = BXCreateElement('TABLE', {className : "bxtaskbarprops", cellSpacing: 0, cellPadding: 1}, {width: '100%'}, document);
			var row = tProp.insertRow(-1);
			var cell = row.insertCell(-1);cell.align = 'right';
			cell.innerHTML = '<span>'+BX_MESS.TPropImgPath+'</span>';
			cell = row.insertCell(-1);
			pTaskbar.arElements['src'] = cell.appendChild(pTaskbar.pMainObj.CreateElement("INPUT", {'size':'40', 'title': BX_MESS.TPropImgPath2, 'type': 'text'}));

			cell = row.insertCell(-1); cell.align = 'right';
			cell.innerHTML = '<span>'+BX_MESS.TPropImgHSpace+' </span>';
			cell = row.insertCell(-1);
			pTaskbar.arElements['hspace'] = cell.appendChild(pTaskbar.pMainObj.CreateElement("INPUT", {'size':'6', 'title': 'HSpace', 'type': 'text'}));

			row = tProp.insertRow(-1);
			cell = row.insertCell(-1); cell.align = 'right';
			cell.innerHTML = '<span>'+BX_MESS.TPropSize+' </span>';
			cell = row.insertCell(-1);
			pTaskbar.arElements['width'] = cell.appendChild(pTaskbar.pMainObj.CreateElement("INPUT", {'size':'5', 'title': BX_MESS.TPropW, 'type': 'text'}));
			cell.appendChild(pTaskbar.pMainObj.CreateElement("SPAN", {'innerHTML':'x'}));
			pTaskbar.arElements['height'] = cell.appendChild(pTaskbar.pMainObj.CreateElement("INPUT", {'size':'5', 'title': BX_MESS.TPropH, 'type': 'text'}));

			cell = row.insertCell(-1); cell.align = 'right';
			cell.innerHTML = '<span>'+BX_MESS.TPropImgVSpace+' </span>';
			cell = row.insertCell(-1);
			pTaskbar.arElements['vspace'] = cell.appendChild(pTaskbar.pMainObj.CreateElement("INPUT", {'size':'6', 'title': 'VSpace', 'type': 'text'}));

			row = tProp.insertRow(-1);
			cell = row.insertCell(-1); cell.align = 'right';
			cell.innerHTML = '<span>'+BX_MESS.TPropImgTitle+': </span>';
			cell = row.insertCell(-1);
			pTaskbar.arElements['title'] = cell.appendChild(pTaskbar.pMainObj.CreateElement("INPUT", {'size':'40', 'title': BX_MESS.TPropImgTitle, 'type': 'text'}));

			cell = row.insertCell(-1); cell.align = 'right';
			cell.innerHTML = '<span>'+BX_MESS.TPropStyle+' </span>';
			cell = row.insertCell(-1);
			var pObjTemp = pTaskbar.pMainObj.CreateCustomElement('BXStyleList',
				{	width: '200px',
					height: '200px',
					field_size: '120px',
					title: '(' + BX_MESS.Style + ')',
					tag_name: 'A',
					filter: ['A', 'DEFAULT'],
					OnChangeElement: function (arSelected)
					{
						SAttr(this.pElement, 'className', arSelected["value"]);
					},
					OnChangeText: function (arSelected)
					{
						if(arSelected["value"]!='')
							this.pMainObj.WrapSelectionWith("span", {"class":arSelected["value"]});
						else
						{
							var pElement = this.pMainObj.GetSelectedNode();
							if(pElement)
							{
								while(pElement = pElement.parentNode)
								{
									if(pElement.nodeType == 1 && pElement.tagName.toUpperCase() == 'TABLE')
										break;
									if(pElement.nodeType == 1 && pElement.className)
									{
										if(pElement.tagName.toUpperCase() != 'SPAN' && pElement.tagName.toUpperCase() != 'FONT')
											break;
										SAttr(pElement, 'className', '');
										var attr = pElement.attributes;
										if(attr.length<=0)
											BX.cleanNode(pElement, true);
										break;
									}
								}
							}
						}
					},
					OnSelectionChange: function ()
					{
						var pElement = this.pMainObj.GetSelectedNode();
						if(pElement && pElement.nodeType == 1)
						{
							if(this.prevType != pElement.tagName.toUpperCase())
							{
								this.prevType = pElement.tagName.toUpperCase();
								this.tag_name = pElement.tagName.toUpperCase();
								this.filter = [pElement.tagName.toUpperCase(), 'DEFAULT'];
								this.FillList();
							}

							this.pElement = pElement;
							this.OnChange = this.OnChangeElement;
							this.SelectByVal(this.pElement.className);
						}
						else
						{
							this.OnChange = this.OnChangeText;
							if(this.prevType != 'DEFAULT')
							{
								this.prevType = 'DEFAULT';
								this.filter = ['DEFAULT'];
								this.tag_name = '';
								this.FillList();
							}

							this.SelectByVal();
							if(pElement)
							{
								if(BX.browser.IsIE() && pElement.parentElement && (pElement = pElement.parentElement()))
									pElement = pElement.childNodes[0];

								while(pElement = pElement.parentNode)
								{
									if(pElement.nodeType == 1 && pElement.tagName.toUpperCase() == 'TABLE')
										break;
									if(pElement.nodeType == 1 && pElement.className)
									{
										if(pElement.tagName.toUpperCase() != 'SPAN' && pElement.tagName.toUpperCase() != 'FONT')
											break;
										this.SelectByVal(pElement.className);
										break;
									}
								}
							}
						}
					}
				});
			pTaskbar.arElements['cssclass'] = pObjTemp;
			cell.appendChild(pObjTemp.pWnd);

			row = tProp.insertRow(-1);
			cell = row.insertCell(-1); cell.align = 'right';
			cell.innerHTML = '<span>'+BX_MESS.TPropImgAlt+': </span>';
			cell = row.insertCell(-1);
			pTaskbar.arElements['alt'] = cell.appendChild(pTaskbar.pMainObj.CreateElement("INPUT", {'size':'40', 'title': BX_MESS.TPropImgAlt, 'type': 'text'}));

			cell = row.insertCell(-1); cell.align = 'right';
			cell.innerHTML = '<span>'+BX_MESS.TPropAlign+' </span>';
			cell = row.insertCell(-1);
			pObjTemp = pTaskbar.pMainObj.CreateCustomElement('BXTAlignPicker', {'type': 'image'});
			pTaskbar.arElements['talign'] = pObjTemp;
			cell.appendChild(pObjTemp.pWnd);

			row = tProp.insertRow(-1);
			cell = row.insertCell(-1); cell.align = 'right';
			cell.innerHTML = '<span>'+BX_MESS.TPropImgBorder+': </span>';
			cell = row.insertCell(-1);
			pTaskbar.arElements['border'] = cell.appendChild(pTaskbar.pMainObj.CreateElement("INPUT", {'size':'4', 'title': BX_MESS.TPropImgBorder, 'type': 'text'}));
			cell = row.insertCell(-1);
			cell = row.insertCell(-1);

			arBarHandlersCache['img'] = [tProp, pTaskbar.arElements];
		}
		pTaskbar.pCellProps.appendChild(tProp);
	}

	pTaskbar.arElements['src'].value = GAttrEx(pElement, '__bxsrc', '__bx_ex_src');
	pTaskbar.arElements['alt'].value = GAttrEx(pElement, 'alt', '__bx_ex_alt');
	pTaskbar.arElements['title'].value = GAttrEx(pElement, 'title', '__bx_ex_title');
	pTaskbar.arElements['cssclass'].SelectByVal(pElement.className);
	pTaskbar.arElements['talign'].SetValue(pElement.align);
	pTaskbar.arElements['hspace'].value = GAttr(pElement, 'hspace');
	pTaskbar.arElements['vspace'].value = GAttr(pElement, 'vspace');
	pTaskbar.arElements['border'].value = GAttr(pElement, 'border');

	var w = parseInt(pElement.style.width) || GAttr(pElement, 'width') || parseInt(pElement.offsetWidth);
	pTaskbar.arElements['width'].value = w;

	var h = parseInt(pElement.style.height) || GAttr(pElement, 'height') || parseInt(pElement.offsetHeight);
	pTaskbar.arElements['height'].value = h;

	var fChange = function ()
	{
		SAttrEx(pElement, '__bxsrc', '__bx_ex_src', pTaskbar.arElements['src'].value);
		SAttrEx(pElement, 'alt', '__bx_ex_alt', pTaskbar.arElements['alt'].value);
		SAttrEx(pElement, 'title', '__bx_ex_title', pTaskbar.arElements['title'].value);
		SAttr(pElement, 'hspace', pTaskbar.arElements['hspace'].value);
		SAttr(pElement, 'vspace', pTaskbar.arElements['vspace'].value);
		SAttr(pElement, 'border', pTaskbar.arElements['border'].value);

		var w = parseInt(pTaskbar.arElements.width.value);
		pElement.removeAttribute('width');
		if (parseInt(pElement.style.width) > 0)
			pElement.style.width = w + "px";
		else
			SAttr(pElement, 'width', w);

		var h = parseInt(pTaskbar.arElements.height.value);
		pElement.removeAttribute('height');
		if (parseInt(pElement.style.height) > 0)
			pElement.style.height = h + "px";
		else
			SAttr(pElement, 'height', h);
	}
	var arEls = ['src', 'alt', 'title', 'hspace', 'vspace', 'border', 'width', 'height'];
	for (var i = 0, l = arEls.length; i < l; i++)
		pTaskbar.arElements[arEls[i]].onchange = fChange;

	pTaskbar.arElements['cssclass'].onchange = function (sel) {pElement.className = sel['value'];};
	pTaskbar.arElements['talign'].OnChange = function (align) {pElement.align = align;};
	pTaskbar.pHtmlElement = pElement;
};

pPropertybarHandlers['php'] = function (bNew, pTaskbar, pElement)
{
	pTaskbar.pHtmlElement = pElement;
	if(bNew)
	{
		pTaskbar.arElements = [];
		var tProp = pTaskbar.pMainObj.CreateElement("TABLE");
		tProp.className = "bxtaskbarprops";
		tProp.style.width = "100%";
		tProp.style.height = "100%";

		tProp.cellSpacing = 0;
		tProp.cellPadding = 1;
		var row, cell;

		row = tProp.insertRow(-1);
		cell = row.insertCell(-1);
		cell.height = "100%";
		var TA = pTaskbar.pMainObj.CreateElement("TEXTAREA", {cols: '60', 'title': BX_MESS.TPropPHP}, {width: "100%", height: "100%"});
		if (IEplusDoctype)
			TA.rows = "20";
		cell.appendChild(TA);
		pTaskbar.arElements['code'] = cell.appendChild(TA);
		pTaskbar.pCellProps.appendChild(tProp);
	}

	pTaskbar.arElements['code'].value = BXUnSerialize(pElement.getAttribute("__bxcontainer")).code;

	var fChange = function () {pElement.setAttribute("__bxcontainer", BXSerialize({'code':pTaskbar.arElements['code'].value}));}

	pTaskbar.arElements['code'].onchange = fChange;
	TA = null;
	tProp = null;
	cell = null;
	row = null;
};

var __defBXTagPropertyHandler = function (bNew, pTaskbar, pElement)
{
	pTaskbar.pHtmlElement = pElement;
	if(bNew)
	{
		pTaskbar.arElements = [];

		var tProp = pTaskbar.pMainObj.CreateElement("TABLE");
		tProp.className = "bxtaskbarprops";
		tProp.style.width = "100%";
		tProp.style.height = "100%";
		tProp.cellSpacing = 0;
		tProp.cellPadding = 1;
		var row, cell;
		row = tProp.insertRow(-1);
		cell = row.insertCell(-1);
		pTaskbar.arElements['text'] = cell.appendChild(pTaskbar.pMainObj.CreateElement("TEXTAREA", {cols: '60'}, {width: "100%", height: "100%"}));
		if (IEplusDoctype)
			pTaskbar.arElements['text'].rows = "20";
		pTaskbar.pCellProps.appendChild(tProp);
		tProp = null;
		cell = null;
		row = null;
	}

	pTaskbar.arElements['text'].value = BXUnSerialize(pElement.getAttribute("__bxcontainer"));

	var fChange = function (){pElement.setAttribute("__bxcontainer", BXSerialize(pTaskbar.arElements['text'].value));};

	pTaskbar.arElements['text'].onchange = fChange;
};

pPropertybarHandlers['comments'] = __defBXTagPropertyHandler;
pPropertybarHandlers['script'] = __defBXTagPropertyHandler;

pPropertybarHandlers['default'] = function (bNew, pTaskbar, pElement)
{
	if(!bNew)
		return;

	pTaskbar.arElements = [];
	var tProp;
	var arBarHandlersCache = pTaskbar.pMainObj.arBarHandlersCache;
	if(arBarHandlersCache['default'])
	{
		tProp = arBarHandlersCache['default'][0];
		pTaskbar.arElements = arBarHandlersCache['default'][1];
	}
	else
	{
		tProp = pTaskbar.pMainObj.pDocument.createElement("TABLE");
		tProp.className = "bxtaskbarprops";
		tProp.style.width = "100%";
		tProp.cellSpacing = 0;
		tProp.cellPadding = 1;
		var row = tProp.insertRow(-1);

		var cell = row.insertCell(-1); cell.align = 'right';
		cell.appendChild(pTaskbar.pMainObj.CreateElement("SPAN", {'innerHTML':BX_MESS.TPropStyle}));

		cell = row.insertCell(-1);
		var pObjTemp = pTaskbar.pMainObj.CreateCustomElement('BXStyleList',
			{	'width': '200px',
				'height': '200px',
				'field_size': '80px',
				'title': '(' + BX_MESS.Style + ')',
				'filter':['DEFAULT'],
				'OnChangeElement': function (arSelected)
				{
					SAttr(this.pElement, 'className', arSelected["value"]);
				},
				'OnChangeText': function (arSelected)
				{
					if(arSelected["value"]!='')
						this.pMainObj.WrapSelectionWith("span", {"class":arSelected["value"]});
					else
					{
						var pElement = this.pMainObj.GetSelectedNode();
						if(pElement)
						{
							while(pElement = pElement.parentNode)
							{
								if(pElement.nodeType == 1 && pElement.tagName.toUpperCase() == 'TABLE')
									break;
								if(pElement.nodeType == 1 && pElement.className)
								{
									if(pElement.tagName.toUpperCase() != 'SPAN' && pElement.tagName.toUpperCase() != 'FONT')
										break;
									SAttr(pElement, 'className', '');
									var attr = pElement.attributes;
									if(attr.length<=0)
										BX.cleanNode(pElement, true);
									break;
								}
							}
						}
					}
				},
				OnSelectionChange: function ()
				{
					var pElement = this.pMainObj.GetSelectedNode();
					if(pElement && pElement.nodeType == 1)
					{
						if(this.prevType != pElement.tagName.toUpperCase())
						{
							this.prevType = pElement.tagName.toUpperCase();
							this.tag_name = pElement.tagName.toUpperCase();
							this.filter = [pElement.tagName.toUpperCase(), 'DEFAULT'];
							this.FillList();
						}

						this.pElement = pElement;
						this.OnChange = this.OnChangeElement;
						this.SelectByVal(this.pElement.className);
					}
					else
					{
						this.OnChange = this.OnChangeText;
						if(this.prevType != 'DEFAULT')
						{
							this.prevType = 'DEFAULT';
							this.filter = ['DEFAULT'];
							this.tag_name = '';
							this.FillList();
						}

						this.SelectByVal();
						if(pElement)
						{
							if(BX.browser.IsIE() && pElement.parentElement && (pElement = pElement.parentElement()))
								pElement = pElement.childNodes[0];

							while(pElement = pElement.parentNode)
							{
								if(pElement.nodeType == 1 && pElement.tagName.toUpperCase() == 'TABLE')
									break;
								if(pElement.nodeType == 1 && pElement.className)
								{
									if(pElement.tagName.toUpperCase() != 'SPAN' && pElement.tagName.toUpperCase() != 'FONT')
										break;
									this.SelectByVal(pElement.className);
									break;
								}
							}
						}
					}
				}
			});
		pTaskbar.arElements['cssclass'] = pObjTemp;
		cell.appendChild(pObjTemp.pWnd);

		cell = row.insertCell(-1); cell.align = 'right';
		cell.appendChild(pTaskbar.pMainObj.CreateElement("SPAN", {'innerHTML':BX_MESS.TPropBIU}));

		cell = row.insertCell(-1);
		pObjTemp = pTaskbar.pMainObj.CreateCustomElement(arButtons['Bold'][0], arButtons['Bold'][1]);
		cell.appendChild(pObjTemp.pWnd);
		pObjTemp = pTaskbar.pMainObj.CreateCustomElement(arButtons['Italic'][0], arButtons['Italic'][1]);
		cell.appendChild(pObjTemp.pWnd);
		pObjTemp = pTaskbar.pMainObj.CreateCustomElement(arButtons['Underline'][0], arButtons['Underline'][1]);
		cell.appendChild(pObjTemp.pWnd);

		////
		row = tProp.insertRow(-1);
		cell = row.insertCell(-1); cell.align = 'right';
		cell.appendChild(pTaskbar.pMainObj.CreateElement("SPAN", {'innerHTML': BX_MESS.Font+': '}));

		cell = row.insertCell(-1);
		pObjTemp = pTaskbar.pMainObj.CreateCustomElement(arButtons['FontName'][0], arButtons['FontName'][1])
		cell.appendChild(pObjTemp.pWnd);

		cell = row.insertCell(-1); cell.align = 'right';
		cell.appendChild(pTaskbar.pMainObj.CreateElement("SPAN", {'innerHTML':BX_MESS.Size + ': '}));

		cell = row.insertCell(-1);
		pObjTemp = pTaskbar.pMainObj.CreateCustomElement(arButtons['FontSize'][0], arButtons['FontSize'][1])
		cell.appendChild(pObjTemp.pWnd);

		////
		row = tProp.insertRow(-1);
		cell = row.insertCell(-1); cell.align = 'right';
		cell.appendChild(pTaskbar.pMainObj.CreateElement("SPAN", {'innerHTML': BX_MESS.TPropColor}));

		cell = row.insertCell(-1);
		pObjTemp = pTaskbar.pMainObj.CreateCustomElement(arButtons['ForeColor'][0], arButtons['ForeColor'][1]);
		cell.appendChild(pObjTemp.pWnd);

		cell = row.insertCell(-1); cell.align = 'right';
		cell.appendChild(pTaskbar.pMainObj.CreateElement("SPAN", {'innerHTML': BX_MESS.TPropBG}));

		cell = row.insertCell(-1);
		pObjTemp = pTaskbar.pMainObj.CreateCustomElement(arButtons['BackColor'][0], arButtons['BackColor'][1]);
		cell.appendChild(pObjTemp.pWnd);

		arBarHandlersCache['default'] = [tProp, pTaskbar.arElements];
	}
	pTaskbar.pCellProps.appendChild(tProp);
}

function flash_unparser(node, pMainObj)
{
	var id = node.arAttributes.id;
	var arParams = pMainObj.arFlashParams[id];
	if (!id || !arParams) return '';
	var res = '<EMBED type="application/x-shockwave-flash" pluginspage="http:/' + '/www.macromedia.com/go/getflashplayer" ';
	for (var i in arParams)
		if (arParams[i])
			res += i + '="' + arParams[i] + '" ';
	res += '></EMBED>';
	return res;
}

function hr_unparser(node, pMainObj)
{
	var id = node.arAttributes.id;
	return '<hr' + (pMainObj.arHrParams[id] || ' /') + '>';
}

function content_unparse(_node, pMainObj)
{
	if (_node.arAttributes["__bxtagname"] == 'break')
		return '<BREAK />';
	else if (_node.arAttributes["__bxtagname"] == 'break_page')
		return '<DIV STYLE="page-break-after: always"><SPAN STYLE="display: none">&nbsp;</SPAN></DIV>';
	else if (!BX.browser.IsIE() && _node.arAttributes["__bxtagname"] == 'hr')
	{
		return hr_unparser(_node, pMainObj);
		//return '<hr />';
	}
	else if (_node.arAttributes["__bxtagname"] == 'flash')
		return flash_unparser(_node, pMainObj);
	return false;
}
oBXEditorUtils.addUnParser(content_unparse);

function _DOMHandler(oDocument)
{
	var ar = oDocument.getElementsByTagName('DIV');
	var iLen = ar.length;
	for (var j=0;j<iLen;j++)
	{
		try
		{
			if (ar[j].style.pageBreakAfter == 'always')
			{
				var oImg = oDocument.createElement("IMG");
				oImg.src = image_path + "/break_page.gif";
				oImg.style.height = "4px";
				oImg.style.width = "100%";
				oImg.setAttribute("__bxtagname","break_page");
				ar[j].parentNode.insertBefore(oImg,ar[j]);
				ar[j].parentNode.removeChild(ar[j]);
			}
		}
		catch(e)
		{
			continue;
		}
	}
}
oBXEditorUtils.addDOMHandler(_DOMHandler);

function _ContentParser(_str, pMainObj)
{
	_str = _str.replace(/<break \/>/ig, '<IMG src="' + image_path + '/break_tag.gif" __bxtagname="break"/>');
	if (!BX.browser.IsIE()) // parse <hr> for FF
	{
		_str = _str.replace(/<hr([^>]*)>/ig, function(str, params)
			{
				var id = '';
				if (params)
				{
					id = "hr_"+Math.round(Math.random() * 1000000);
					pMainObj.arHrParams[id] = params;
				}
				return '<IMG style="padding: 2px; width: 100%; height: 2px;" src="' + image_path + '/break_page.gif" __bxtagname="hr" id="' + id + '"/>';
			}
		);
	}

	// Flash parsing
	var flash_parser = function(str, attr)
	{
		if (attr.indexOf('.swf') === false || attr.indexOf('flash') === false) // not a flash
			return str;

		attr = attr.replace(/[\r\n]+/ig, ' ');
		attr = attr.replace(/\s+/ig, ' ');
		attr = attr.trim();

		var
			arParams = {},
			w, h, style, id;

		attr = attr.replace(/([^\w]??)(\w+?)\s*=\s*("|\')([^\3]+?)\3/ig, function(s, b0, b1, b2, b3){arParams[b1] = b3; return b0;});
		w = (parseInt(arParams.width) || 50) + 'px';
		h = (parseInt(arParams.height) || 25) + 'px';
		style = 'border: 1px solid #B6B6B8; background-color: #E2DFDA; background-image: url(' + image_path + '/flash.gif); background-position: center center; background-repeat: no-repeat; width: ' + w + '; height: ' + h + ';';
		id = "swf_"+Math.round(Math.random() * 1000000);
		pMainObj.arFlashParams[id] = arParams;
		return '<IMG id="' + id + '" style="' + style + '" src="' + one_gif_src + '" __bxtagname="flash"/>';
	};
	_str = _str.replace(/<embed([^>]+?)(?:>(?:\s|\S)*?<\/embed)?(?:\/?)?>/ig, flash_parser);

	return _str;
}
oBXEditorUtils.addContentParser(_ContentParser);

function Flash_Reload(oPreviewCont, src, height, width)
{
	var flash_preview = BX("flash_preview_iframe");
	if (flash_preview)
		flash_preview.parentNode.removeChild(flash_preview);

	var pFrame = BXCreateElement("IFRAME", {id: "flash_preview_iframe"}, {}, document);
	pFrame.setAttribute("src", "javascript:''");
	pFrame.style.width = "97%";
	pFrame.style.height = "97%";
	pFrame = oPreviewCont.appendChild(pFrame);
	var width = width + "px";
	var height = height + "px";
	pFrame.setAttribute("src", flash_preview_path + "?path=" + BX.util.urlencode(src)+"&width="+width+"&height="+height);
}

pPropertybarHandlers['flash'] = function (bNew, pTaskbar, pElement)
{
	var id = pElement.id;
	var arParams = pTaskbar.pMainObj.arFlashParams[id];
	if (!id || !arParams || !arParams.src) return '';

	if(bNew)
	{
		pTaskbar.arElements = [];
		var tProp;
		var arBarHandlersCache = pTaskbar.pMainObj.arBarHandlersCache;
		var arBarHandlersCache = [];

		if(arBarHandlersCache['flash'])
		{
			var tProp = arBarHandlersCache['flash'][0];
			pTaskbar.arElements = arBarHandlersCache['flash'][1];
			pTaskbar.prCell = arBarHandlersCache['flash'][2];
		}
		else
		{
			tProp = BXCreateElement('TABLE', {cellSpacing: 0, cellPadding: 0, border: 1}, {width: '100%', height: '100%'}, document);
			var row = tProp.insertRow(-1);
			pTaskbar.prCell = row.insertCell(-1);
			pTaskbar.prCell.width = '300px';
			pTaskbar.prCell.className = 'bx_valign_top';

			var parCell = row.insertCell(-1);
			var _tProp = parCell.appendChild(BXCreateElement('TABLE', {className : "bxtaskbarprops", cellSpacing: 2, cellPadding: 1}, {width: '100%'}, document));

			var row = _tProp.insertRow(-1);
			cell = row.insertCell(-1); cell.align = 'right';
			cell.innerHTML = '<span>' + BX_MESS.PATH2SWF + ': </span>';
			var cell = row.insertCell(-1);
			pTaskbar.arElements.src = cell.appendChild(pTaskbar.pMainObj.CreateElement("INPUT", {size:'50', type: 'text'}));

			var row = _tProp.insertRow(-1);
			cell = row.insertCell(-1); cell.align = 'right';
			cell.innerHTML = '<span>' + BX_MESS.TPropW + ': </span>';
			var cell = row.insertCell(-1);
			pTaskbar.arElements.width = cell.appendChild(pTaskbar.pMainObj.CreateElement("INPUT", {size:'4', type: 'text'}));
			cell.appendChild(pTaskbar.pMainObj.CreateElement("SPAN", {innerHTML: '<span style="padding-left: 40px">' + BX_MESS.TPropH + ': </span>'}));
			pTaskbar.arElements.height = cell.appendChild(pTaskbar.pMainObj.CreateElement("INPUT", {size:'4', type: 'text'}));

			var row = _tProp.insertRow(-1);
			cell = row.insertCell(-1); cell.align = 'right';
			cell.innerHTML = '<span>' + BX_MESS.SWF_ID + ': </span>';
			var cell = row.insertCell(-1);
			pTaskbar.arElements.id = cell.appendChild(pTaskbar.pMainObj.CreateElement("INPUT", {size:'30', type: 'text'}));

			var row = _tProp.insertRow(-1);
			cell = row.insertCell(-1); cell.align = 'right';
			cell.innerHTML = '<span>' + BX_MESS.SWF_TITLE + ': </span>';
			var cell = row.insertCell(-1);
			pTaskbar.arElements.title = cell.appendChild(pTaskbar.pMainObj.CreateElement("INPUT", {size:'30', type: 'text'}));

			var row = _tProp.insertRow(-1);
			cell = row.insertCell(-1); cell.align = 'right';
			cell.innerHTML = '<span>' + BX_MESS.SWF_CLASSNAME + ': </span>';
			var cell = row.insertCell(-1);
			pTaskbar.arElements.className = cell.appendChild(pTaskbar.pMainObj.CreateElement("INPUT", {size:'30', type: 'text'}));

			var row = _tProp.insertRow(-1);
			cell = row.insertCell(-1); cell.align = 'right';
			cell.innerHTML = '<span>' + BX_MESS.TPropStyle + ': </span>';
			var cell = row.insertCell(-1);
			pTaskbar.arElements.style = cell.appendChild(pTaskbar.pMainObj.CreateElement("INPUT", {size:'30', type: 'text'}));

			var row = _tProp.insertRow(-1);
			cell = row.insertCell(-1); cell.align = 'right';
			cell.innerHTML = '<span>' + BX_MESS.SWF_QUALITY + ': </span>';
			var cell = row.insertCell(-1);
			cell.innerHTML = '<select style="width:100px">'+
							'<option value=""></option>'+
							'<option value="low">low</option>'+
							'<option value="medium">medium</option>'+
							'<option value="high">high</option>'+
							'<option value="autolow">autolow</option>'+
							'<option value="autohigh">autohigh</option>'+
							'<option value="best">best</option>'+
						'</select>';
			pTaskbar.arElements.quality = cell.firstChild;

			var row = _tProp.insertRow(-1);
			cell = row.insertCell(-1); cell.align = 'right';
			cell.innerHTML = '<span>' + BX_MESS.SWF_WMODE + ': </span>';
			var cell = row.insertCell(-1);
			cell.innerHTML = '<select style="width:100px">'+
							'<option value=""></option>'+
							'<option value="window">window</option>'+
							'<option value="opaque">opaque</option>'+
							'<option value="transparent">transparent</option>'+
						'</select>';
			pTaskbar.arElements.wmode = cell.firstChild;

			var row = _tProp.insertRow(-1);
			cell = row.insertCell(-1); cell.align = 'right';
			cell.innerHTML = '<span>' + BX_MESS.SWF_SCALE + ': </span>';
			var cell = row.insertCell(-1);
			cell.innerHTML = '<select style="width:100px">'+
							'<option value=""></option>'+
							'<option value="showall">showall</option>'+
							'<option value="noborder">noborder</option>'+
							'<option value="exactfit">exactfit</option>'+
						'</select>';
			pTaskbar.arElements.scale = cell.firstChild;

			var row = _tProp.insertRow(-1);
			cell = row.insertCell(-1); cell.align = 'right';
			cell.innerHTML = '<span>' + BX_MESS.SWF_SALIGN + ': </span>';
			var cell = row.insertCell(-1);
			cell.innerHTML = '<select style="width:100px">'+
							'<option value=""></option> '+
							'<option value="left">left</option> '+
							'<option value="top">top</option> '+
							'<option value="right">right</option> '+
							'<option value="bottom">bottom</option> '+
							'<option value="top left">top left</option>'+
							'<option value="top right">top right</option>'+
							'<option value="bottom left">bottom left</option>'+
							'<option value="bottom right">bottom right</option>'+
						'</select>';
			pTaskbar.arElements.salign = cell.firstChild;

			var row = _tProp.insertRow(-1);
			cell = row.insertCell(-1); cell.align = 'right';
			cell.innerHTML = '<span>' + BX_MESS.SWF_AUTOPLAY + ': </span>';
			var cell = row.insertCell(-1);
			pTaskbar.arElements.autoplay = cell.appendChild(pTaskbar.pMainObj.CreateElement("INPUT", {type: 'checkbox'}));

			var row = _tProp.insertRow(-1);
			cell = row.insertCell(-1); cell.align = 'right';
			cell.innerHTML = '<span>' + BX_MESS.SWF_LOOP + ': </span>';
			var cell = row.insertCell(-1);
			pTaskbar.arElements.loop = cell.appendChild(pTaskbar.pMainObj.CreateElement("INPUT", {type: 'checkbox'}));

			var row = _tProp.insertRow(-1);
			cell = row.insertCell(-1); cell.align = 'right';
			cell.innerHTML = '<span>' + BX_MESS.SWF_SHOW_MENU + ': </span>';
			var cell = row.insertCell(-1);
			pTaskbar.arElements.showmenu = cell.appendChild(pTaskbar.pMainObj.CreateElement("INPUT", {type: 'checkbox'}));

			arBarHandlersCache['flash'] = [tProp, pTaskbar.arElements, pTaskbar.prCell];
		}
		pTaskbar.pCellProps.appendChild(tProp);
	}

	var k, i, el;
	for (i in arParams)
	{
		k = (i.toLowerCase() == 'class') ? 'className' : i;
		if (!pTaskbar.arElements[k])
			continue

		if (pTaskbar.arElements[k].type.toLowerCase() == 'checkbox' && arParams[i])
			pTaskbar.arElements[k].checked = true;
		else
			pTaskbar.arElements[k].value = arParams[i];
	}
	pTaskbar.arElements.width.value = (parseInt(pElement.width) || parseInt(pElement.style.width)) + 'px';
	pTaskbar.arElements.height.value = (parseInt(pElement.height) || parseInt(pElement.style.height)) + 'px';

	Flash_Reload(pTaskbar.prCell, arParams.src, 250, 250);

	var fChange = function()
	{
		var i = this.id;
		var k = (i.toLowerCase() == 'classname') ? 'class' : i;

		if (pTaskbar.arElements[i].type.toLowerCase() == 'checkbox')
			arParams[k] = (pTaskbar.arElements[i].checked) || null;
		else
		{
			if (k == 'width' || k == 'height')
			{
				pElement.style.width = (parseInt(pTaskbar.arElements.width.value) || 50) + 'px';
				pElement.style.height = (parseInt(pTaskbar.arElements.height.value) || 25) + 'px';
			}
			else if(k == 'src' && pTaskbar.arElements.src.value)
			{
				Flash_Reload(pTaskbar.prCell, pTaskbar.arElements.src.value, 250, 250);
			}
			arParams[k] = pTaskbar.arElements[i].value || null;
		}
	};

	for (i in pTaskbar.arElements)
	{
		el = pTaskbar.arElements[i];
		el.id = i;
		if (el.type.toLowerCase() == 'checkbox')
			el.onclick = fChange;
		else
			el.onchange = fChange;
	}
};

if (!window.lightMode)
{
	oBXEditorUtils.appendButton("page_break", arButtons['page_break'], "standart");
	oBXEditorUtils.appendButton("break_tag", arButtons['break_tag'], "standart");
	oBXEditorUtils.appendButton("insert_flash", arButtons['insert_flash'], "standart");
}


// *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *   *
// Light mode Toolbars:
if (window.lightMode)
{
	var arGlobalToolbar = [
		'line_begin',

		arButtons['Fullscreen'],
		arButtons['Settings'],
		arButtons['Cut'], arButtons['Copy'], arButtons['Paste'], arButtons['pasteword'], arButtons['pastetext'], arButtons['SelectAll'],
		arButtons['Undo'], arButtons['Redo'],
		arButtons['borders'], 'separator',
		arButtons['table'], arButtons['anchor'], arButtons['CreateLink'], arButtons['deletelink'], arButtons['image'],
		arButtons['SpecialChar'], /* arButtons['spellcheck'], */
		arButtons['insert_flash'],

		arButtons['InsertHorizontalRule'], 'separator',
		arButtons['InsertOrderedList'], arButtons['InsertUnorderedList'], 'separator',
		arButtons['Outdent'], arButtons['Indent'],

		'line_end',
		'line_begin',

		arButtons['wysiwyg'],
		arButtons['source'],
		arButtons['split'],
		arButtons['Wrap'],

		arButtons['FontStyle'], arButtons['HeadingList'], arButtons['FontName'], arButtons['FontSize'], 'separator',
		arButtons['Bold'], arButtons['Italic'], arButtons['Underline'], 'separator',
		arButtons['JustifyLeft'], arButtons['JustifyCenter'], arButtons['JustifyRight'], arButtons['JustifyFull'], 'separator',
		arButtons['RemoveFormat'], arButtons['Optimize'], 'separator',
		arButtons['BackColor'], arButtons['ForeColor'],

		'line_end'
	];
}