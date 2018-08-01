function JCLightHTMLEditor(arConfig) {this.Init(arConfig);}

JCLightHTMLEditor.prototype.Init = function(arConfig)
{
	var _this = this;
	this.arConfig = arConfig;
	this.id = arConfig.id;
	this.bFloatingToolbar = arConfig.bFloatingToolbar;
	this.arConfig.width = this.arConfig.width ? parseInt(this.arConfig.width) + (this.arConfig.width.indexOf('%') == -1 ? "px" : '%') : "100%";
	this.arConfig.height = this.arConfig.height ? parseInt(this.arConfig.height) + (this.arConfig.height.indexOf('%') == -1 ? "px" : '%') : "100%";
	this.SetConstants();
	this.sEditorMode = 'html';
	this.toolbarLineCount = 1;
	this.CACHE = {};
	this.arVideos = {};
	// Set content from config;
	this.content = this.arConfig.content;
	this.oSpecialParsers = {};
	if (arConfig.parsers)
	{
		for (var p in arConfig.parsers)
		{
			if (arConfig.parsers[p])
				this.oSpecialParsers[p] = arConfig.parsers[p];
		}
	}

	this.bDialogOpened = false;

	// Sceleton
	this.pFrame = document.getElementById('bxlhe_frame_' + this.id);
	this.pFrame.style.width = this.arConfig.width;
	this.pFrame.style.height = this.arConfig.height;
	if (this.bFloatingToolbar)
	{
		this.pEditCont = this.pFrame;
		this.pFloatToolbar = document.getElementById('bxlhe_float_toolbar_' + this.id);
		var tbl = this.pFloatToolbar.firstChild;
		this.pFloatToolbarToggle = tbl.rows[0].cells[0];
		this.pButtonsCont = tbl.rows[0].cells[1];
	}
	else
	{
		this.pFrameTable = this.pFrame.firstChild;
		this.pButtonsCell = this.pFrameTable.rows[0].cells[0];
		this.pButtonsCont = this.pButtonsCell.firstChild;
		this.pEditCont = this.pFrameTable.rows[1].cells[0];

		if (this.arConfig.height.indexOf('%') == -1)
		{
			h = parseInt(this.arConfig.height) - this.toolbarLineCount * 25;
			if (h > 0)
				this.pEditCont.style.height = h + 'px';
		}
	}

	// iFrame
	this.iFrame = jsUtils.CreateElement("IFRAME", { id: 'LHE_iframe_' + this.id, className: 'lha-iframe' });
	this.iFrame.setAttribute("src", "javascript:void(0)");
	this.pEditCont.appendChild(this.iFrame);
	if (this.iFrame.contentDocument)
		this.pEditorDocument = this.iFrame.contentDocument;
	else
		this.pEditorDocument = this.iFrame.contentWindow.document;
	this.pEditorWindow = this.iFrame.contentWindow;

	// Textarea
	var ta = jsUtils.CreateElement("TEXTAREA", { className: 'lha-textarea', rows: 25, id: this.arConfig.inputId, name: this.arConfig.inputName });
	this.pSourceDiv = this.pEditCont.appendChild(jsUtils.CreateElement("DIV", { className: 'lha-source-div' }));
	this.pTextarea = this.pSourceDiv.appendChild(ta);

	this.oPos = jsUtils.GetRealPos(this.pEditCont);
	// Add buttons
	this.AddButtons();

	if (this.bFloatingToolbar)
		this.BuildFloatToolbar();

	this.SetEditorContent(this.content);
	this.oTransOverlay = new LHETransOverlay({zIndex: 995}, this);
	this.oContextMenu = new LHEContextMenu({zIndex: 1000}, this);

	jsUtils.onCustomEvent('LHE_OnInit', [this]);

	// Init events
	window['lhe_onclick__' + this.id] = function(e) {_this.OnClick(e);};
	window['lhe_mousedown__' + this.id] = function(e) {_this.OnMousedown(e);};
	window['lhe_contextmenu__' + this.id] = function(e) {_this.OnContextMenu(e);};
	window['lhe_keydown__' + this.id] = function(e) {_this.OnKeyDown(e);};

	jsUtils.addEvent(this.pEditorDocument, 'click', window['lhe_onclick__' + this.id]);
	jsUtils.addEvent(this.pEditorDocument, 'mousedown', window['lhe_mousedown__' + this.id]);
	jsUtils.addEvent(this.pEditorDocument, 'contextmenu', window['lhe_contextmenu__' + this.id]);
	if (!jsUtils.bIsIE && !jsUtils.IsOpera()) // Force Ctrl+B, Ctrl+U, Ctrl+I for FF
		jsUtils.addEvent(this.pEditorDocument, 'keydown', window['lhe_keydown__' + this.id]);

	if (this.arConfig.bSaveOnBlur || this.arConfig.bArisingToolbar)
	{
		window['lhe_doc_mousedown_' + this.id] = function(e) { _this.OnDocMousedown(e); }
		jsUtils.addEvent(document, "mousedown", window['lhe_doc_mousedown_' + this.id]);
	}
}

JCLightHTMLEditor.prototype.SetConstants = function()
{
	this.reBlockElements = /^(BR|TITLE|TABLE|SCRIPT|TR|TBODY|P|DIV|H1|H2|H3|H4|H5|H6|ADDRESS|PRE|OL|UL|LI)$/i;
	this.oneGifSrc = '/bitrix/images/1.gif';
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

JCLightHTMLEditor.prototype.OnMousedown = function(e)
{
	if (!e)
		e = window.event;
	if (jsUtils.IsOpera() && e.shiftKey)
	{
		this.OnContextMenu(e);
		jsUtils.PreventDefault(e);
	}
}

JCLightHTMLEditor.prototype.OnClick = function(e)
{
	if(!e)
		e = window.event;
	if (this.arConfig.bArisingToolbar)
		this.ShowFloatToolbar(true);
}

JCLightHTMLEditor.prototype.OnDblClick = function(e)
{
	return;
}

JCLightHTMLEditor.prototype.OnContextMenu = function(e, pElement)
{
	var
		_this = this,
		oFramePos,
		x, y;
	if (!e) e = this.pEditorWindow.event;

	if(e.pageX || e.pageY)
	{
		x = e.pageX - this.pEditorDocument.body.scrollLeft;
		y = e.pageY - this.pEditorDocument.body.scrollTop;
	}
	else if(e.clientX || e.clientY)
	{
		x = e.clientX;
		y = e.clientY;
	}

	oFramePos = this.CACHE['frame_pos'];
	if (!oFramePos)
		this.CACHE['frame_pos'] = oFramePos = jsUtils.GetRealPos(this.pEditCont);

	x += oFramePos.left;
	y += oFramePos.top;

	var targ;
	if (e.target)
		targ = e.target;
	else if (e.srcElement)
		targ = e.srcElement;
	if (targ.nodeType == 3) // defeat Safari bug
		targ = targ.parentNode;

	if (!targ || !targ.nodeName)
		return;
	var res = this.oContextMenu.Show({oPos: {left : x, top : y}, pElement: targ});

	return jsUtils.PreventDefault(e);
}

JCLightHTMLEditor.prototype.OnKeyDown = function(e)
{
	if(!e) e = window.event;

	if (e.ctrlKey && !e.shiftKey && !e.altKey)
	{
		if (!jsUtils.bIsIE && !jsUtils.IsOpera())
		{
			switch (e.which)
			{
				case 66 : // B
				case 98 : // b
					this.executeCommand('Bold');
					return jsUtils.PreventDefault(e);
				case 105 : // i
				case 73 : // I
					this.executeCommand('Italic');
					return jsUtils.PreventDefault(e);
				case 117 : // u
				case 85 : // U
					this.executeCommand('Underline');
					return jsUtils.PreventDefault(e);
			}
		}
	}
}

JCLightHTMLEditor.prototype.OnDocMousedown = function(e)
{
	if(!e) e = window.event;

	var
		windowSize = jsUtils.GetWindowSize(),
		mouseX = e.clientX + windowSize.scrollLeft,
		mouseY = e.clientY + windowSize.scrollTop;

	if (mouseX < this.oPos.left || mouseX > this.oPos.right || mouseY < this.oPos.top || mouseY > this.oPos.bottom)
	{
		if (this.arConfig.bSaveOnBlur)
			this.SaveContent();
		if (this.arConfig.bArisingToolbar && !this.bDialogOpened)
		{
			var targ = e.target || e.srcElement;
			if (targ.nodeType == 3) // defeat Safari bug
				targ = targ.parentNode;
			if (!targ) return;

			var o = {parentNode: targ};
			var cn, parent, id;
			while(o.parentNode)
			{
				parent = o.parentNode;
				id = parent.id;

				cn = parent.className || '';
				if(
					cn == 'bxlhe-float-toolbar' || // toolbar
					cn == 'lhe-button-cont' || // button
					cn == 'lhe-cm' || // context menu
					cn == 'lhe-colpick-cont' ||// colorpicker
					cn == 'lhe-list-val-cont' ||// list
					cn == 'lhe-dialog' ||// dialog
					cn == 'lhe-trans-overlay' || // transparent overlay
					cn == 'lhe-overlay' ||// overlay
					cn == 'bxlhe-frame' || // frame
					cn == 'bxlhe-toggle' // toolbar toogle
				)
				{
					return jsUtils.PreventDefault(e);
					return true;
				}
				o = parent;
			}
			this.ShowFloatToolbar(false);
		}
	}
	else
	{
		if (this.arConfig.bArisingToolbar)
			this.ShowFloatToolbar(true);
	}
}

JCLightHTMLEditor.prototype.SetView = function(sType)
{
	if (this.sEditorMode == sType)
		return;

	this.SaveContent();
	if (sType == 'code')
	{
		this.Set
		this.iFrame.style.display = "none";
		this.pSourceDiv.style.display = "block";
		this.SetCodeEditorContent(this.GetContent());
	}
	else
	{
		this.iFrame.style.display = "block";
		this.pSourceDiv.style.display = "none";
		this.SetEditorContent(this.GetContent());
	}
	this.sEditorMode = sType;
	//this.OnEvent("OnChangeView", [this.sEditorMode, this.sEditorSplitMode]);
}

JCLightHTMLEditor.prototype.SaveContent = function()
{
	var sContent = this.sEditorMode == 'code' ? this.GetCodeEditorContent() : this.GetEditorContent();
	//sContent = this.OptimizeHTML(sContent);
	this.SetContent(sContent);
}

JCLightHTMLEditor.prototype.SetContent = function(sContent)
{
	this.pTextarea.value = this.content = sContent;
};

JCLightHTMLEditor.prototype.GetContent = function()
{
	return this.content.toString();
};

JCLightHTMLEditor.prototype.SetEditorContent = function(sContent)
{
	var _this = this;
	sContent = this.ParseContent(sContent);

	if (this.pEditorDocument.designMode)
	{
		try{
			this.pEditorDocument.designMode='off';
		}catch(e){alert('SetEditorContent: designMode=\'off\'');}
	}

	this.pEditorDocument.open();
	this.pEditorDocument.write('<html><head></head><body>' + sContent + '</body></html>');
	this.pEditorDocument.close();

	if(jsUtils.bIsIE)
	{
		this.pEditorDocument.body.contentEditable = true;
	}
	else if (this.pEditorDocument.designMode)
	{
		this.pEditorDocument.designMode = "on";
		try{
		this.pEditorDocument.execCommand("styleWithCSS", false, false);
		this.pEditorDocument.execCommand("useCSS", false, true);
		}catch(e){}
	}
}

JCLightHTMLEditor.prototype.GetEditorContent = function()
{
	var sContent = this.UnParseContent();
	return sContent;
}

JCLightHTMLEditor.prototype.SetCodeEditorContent = function(sContent)
{
	this.pTextarea.value = sContent;
}

JCLightHTMLEditor.prototype.GetCodeEditorContent = function()
{
	return this.pTextarea.value;
}

JCLightHTMLEditor.prototype.OptimizeHTML = function (str)
{
	var
		iter = 0,
		bReplasing = true,
		arTags = ['b', 'em', 'font', 'h\\d', 'i', 'li', 'ol', 'p', 'small', 'span', 'strong', 'u', 'ul'],
		replaceEmptyTags = function(){i--; bReplasing = true; return ' ';},
		re, tagName, i, l;

	while(iter++ < 20 && bReplasing)
	{
		bReplasing = false;
		for (i = 0, l = arTags.length; i < l; i++)
		{
			tagName = arTags[i];
			re = new RegExp('<'+tagName+'[^>]*?>\\s*?</'+tagName+'>', 'ig');
			str = str.replace(re, replaceEmptyTags);

			re = new RegExp('<' + tagName + '\\s+?[^>]*?/>', 'ig');
			str = str.replace(re, replaceEmptyTags);

			// Replace <b>text1</b>    <b>text2</b> ===>>  <b>text1 text2</b>
			re = new RegExp('<((' + tagName + '+?)(?:\\s+?[^>]*?)?)>([\\s\\S]+?)<\\/\\2>\\s*?<\\1>([\\s\\S]+?)<\\/\\2>', 'ig');
			str = str.replace(re, function(str, b1, b2, b3, b4)
				{
					bReplasing = true;
					return '<' + b1 + '>' + b3 + ' ' + b4 + '</' + b2 + '>';
				}
			);
		}
	}
	return str;
};

JCLightHTMLEditor.prototype._RecursiveDomWalker = function(pNode)
{
	var oNode =
	{
		arAttributes : {},
		arNodes : [],
		type : null,
		text : ""
	};

	switch(pNode.nodeType)
	{
		case 9:
			oNode.type = 'document';
			break;
		case 1:
			if(pNode.tagName.length <= 0 || pNode.tagName.substring(0, 1) == "/")
				return;

			oNode.text = pNode.tagName.toLowerCase();
			if (oNode.text == 'script')
				break;

			oNode.type = 'element';
			var
				attr = pNode.attributes,
				j, l = attr.length;

			for(j = 0; j < l; j++)
			{
				if(attr[j].specified || (oNode.text=="input" && attr[j].nodeName.toLowerCase()=="value"))
				{
					var attrName = attr[j].nodeName.toLowerCase();
					if(attrName == '__bxid')
						continue;

					if(attrName=="style")
						oNode.arAttributes[attrName] = pNode.style.cssText;
					else if(attrName=="src" || attrName=="href"  || attrName=="width"  || attrName=="height")
						oNode.arAttributes[attrName] = pNode.getAttribute(attrName, 2);
					else
						oNode.arAttributes[attrName] = attr[j].nodeValue;
				}
			}
			break;
		case 3:
			oNode.type = 'text';
			var res = pNode.nodeValue;
			if(!(oNode.oParent && oNode.oParent.text && oNode.oParent.text.toLowerCase() == 'pre'))
			{
				res = res.replace(/\n+/g, ' ');
				res = res.replace(/ +/g, ' ');
			}

			oNode.text = res;
			break;
	}

	var arChilds = pNode.childNodes;
	var node, oBXChildNode;

	for(var i = 0; i < arChilds.length; i++)
	{
		node = arChilds[i];
		oNode.arNodes.push(this._RecursiveDomWalker(node));
	}

	return oNode;
}


JCLightHTMLEditor.prototype._RecursiveGetHTML = function(pNode)
{
	var res = "", ob, good_res;

	var attr = pNode.arAttributes["__bxtagname"];
	if (!pNode.arAttributes)
		return '';

	if (attr)
	{
		var parser = this.oSpecialParsers[attr];
		if (parser)
			return parser.UnParse(attr, pNode, this);
		else
			return '';
	}

	if (pNode.arAttributes["_moz_editor_bogus_node"])
		return '';

	bFormatted = true;

	if (pNode.text.toLowerCase() != 'body')
		res = this.GetNodeHTMLLeft(pNode);
	var bNewLine = false;
	var sIndent = '';

	if (bFormatted && pNode.type != 'text')
	{
		if (this.reBlockElements.test(pNode.text) && !(pNode.oParent && pNode.oParent.text && pNode.oParent.text.toLowerCase() == 'pre'))
		{
			for (var j = 0; j < pNode.iLevel - 3; j++)
				sIndent += "  ";
			bNewLine = true;
			res = "\r\n" + sIndent + res;
		}
	}

	for (var i = 0; i < pNode.arNodes.length; i++)
		res += this._RecursiveGetHTML(pNode.arNodes[i]);

	if (pNode.text.toLowerCase() != 'body')
		res += this.GetNodeHTMLRight(pNode);

	if (bNewLine)
		res += "\r\n" + (sIndent == '' ? '' : sIndent.substr(2));
	return res;
}


JCLightHTMLEditor.prototype.GetNodeHTMLLeft = function(pNode)
{
	if(pNode.type == 'text')
		return bxhtmlspecialchars(pNode.text);
	var atrVal, attrName, res;
	if(pNode.type == 'element')
	{
		res = "<" + pNode.text;
		for(attrName in pNode.arAttributes)
		{
			atrVal = pNode.arAttributes[attrName];
			if(attrName.substring(0,4).toLowerCase() == '_moz')
				continue;

			if(pNode.text.toUpperCase()=='BR' && attrName.toLowerCase() == 'type' && atrVal == '_moz')
				continue;
			if(attrName=='style' && atrVal.length<=0)
				continue;
			res += ' ' + attrName + '="' + (pNode.bDontUseSpecialchars ? atrVal : bxhtmlspecialchars(atrVal)) + '"';
		}
		if(pNode.arNodes.length <= 0 && !this.IsPairNode(pNode.text))
			return res+" />";
		return res+">";
	}
	return "";
};

JCLightHTMLEditor.prototype.GetNodeHTMLRight = function(pNode)
{
	if(pNode.type == 'element' && (pNode.arNodes.length>0 || this.IsPairNode(pNode.text)))
		return "</" + pNode.text + ">";
	return "";
};


JCLightHTMLEditor.prototype.IsPairNode = function(text)
{
	if(text.substr(0, 1) == 'h' || text == 'br' || text == 'img' || text == 'input')
		return false;
	return true;
};

JCLightHTMLEditor.prototype.executeCommand = function(commandName, sValue)
{
	this.SetFocus();
	//try{
	var res = this.pEditorWindow.document.execCommand(commandName, false, sValue);
	//}catch(e){};
	this.SetFocus();
	//this.OnEvent("OnSelectionChange");
	//this.OnChange("executeCommand", commandName);

	return res;
};

JCLightHTMLEditor.prototype.queryCommand = function(commandName)
{
	var sValue = '';
	if (!this.pEditorDocument.queryCommandEnabled || !this.pEditorDocument.queryCommandValue)
		return null;

	if(!this.pEditorDocument.queryCommandEnabled(commandName))
		return null;

	return this.pEditorDocument.queryCommandValue(commandName);
};

JCLightHTMLEditor.prototype.SetFocus = function ()
{
	if (this.sEditorMode != 'html')
		return;

	//try{
		if(this.pEditorWindow.focus)
			this.pEditorWindow.focus();
		else
			this.pEditorDocument.body.focus();
	//} catch(e){}
};

JCLightHTMLEditor.prototype.ParseContent = function(sContent) // Code->HTML
{
	jsUtils.onCustomEvent('LHE_OnParseContent', [this]);

	sContent = sContent.replace(/(<td[^>]*>)\s*(<\/td>)/ig, "$1<br _moz_editor_bogus_node='on'>$2");

	//add  tag  __bxhref to all links
	var add__bxhref = function(str, b1, b2, b3, b4, b5) { return b1 + b2 + b3 + b2 + b4 + ' __bxhref=' + b2 + b3 + b2 + b5; };
	sContent = sContent.replace(/(<a.*?\s{1}href\s*=\s*)("|')((?:.*?[^\\]{1})??)\2((?:.*?[^\?]{1})??)(>.+?<\/a>)/ig, add__bxhref);
	//add tag __bxsrc to all img
	var add__bxsrc = function(str, b1, b2, b3, b4) { return b1 + b2 + b3 + b2 + ' __bxsrc=' + b2 + b3 + b2 + b4; };
	sContent = sContent.replace(/(<img.*?\s{1}src\s*=\s*)("|')((?:.*?[^\\]{1})??)\2((?:.*?[^\?]{1})??>)/ig, add__bxsrc);
	// Fix area
	if (jsUtils.bIsIE)
	{
		sContent = sContent.replace(/<area([^>]*?>[^>]*?)<\/area>/ig, "<bxarea$1</bxarea>");
		sContent = sContent.replace(/<area([^>]*?>[^>]*?)>/ig, "<bxarea$1>");
	}

	for (var p in this.oSpecialParsers)
		sContent = this.oSpecialParsers[p].Parse(p, sContent, this);

	return sContent;
}

JCLightHTMLEditor.prototype.UnParseContent = function() // Code -> HTML
{
	jsUtils.onCustomEvent('LHE_OnUnParseContent', [this]);
	var pBody = this._RecursiveDomWalker(this.pEditorDocument.getElementsByTagName('BODY')[0]);
	var sContent = this._RecursiveGetHTML(pBody);

	//Removing temporary servise attributes: __bxhref, __bxsrc and other
	sContent = sContent.replace(/(<a[^>]*?)__bxhref[^=]*?=("|\')([^>]*?)\2([^>]*? href[^=]*?=)("|\')[^>]*?\5([^>]*?>.|\s+?<\/a>)/ig, "$1$4$5$3$5$6");
	sContent = sContent.replace(/(<a[^>]*?href[^=]*?=)("|\')[^>]*?\2([^>]*?)__bxhref[^=]*?=("|\')([^>]*?)\4([^>]*?>.|\s+?<\/a>)/ig,"$1$2$5$2$3$6");
	sContent = sContent.replace(/(<img[^>]*?)__bxsrc[^=]*?=("|\')([^>]*?)\2([^>]*?src[^=]*?=)("|\')[^>]*?\5([^>]*?>)/ig, "$1$4$5$3$5$6");
	sContent = sContent.replace(/(<img[^>]*?src[^=]*?=)("|\')[^>]*?\2([^>]*?)__bxsrc[^=]*?=("|\')([^>]*?)\4([^>]*?>)/ig, "$1$2$5$2$3$6");

	if (jsUtils.bIsIE)
	{
		sContent = sContent.replace(/<bxarea([^>]*?>[^>]*?)<\/bxarea>/ig, "<area$1</area>");
		sContent = sContent.replace(/<bxarea([^>]*?>[^>]*?)>/ig, "<area$1>");
	}

	var killspaces = function(str, b1, b2) {return b1+' '+b2.replace(/(.*?)\s{2,}(.*)/ig, killspaces);}
	sContent = sContent.replace(/(<a[^>]*?)\s{2,}(.*?>.+?<\/a>)/ig, killspaces);
	sContent = sContent.replace(/(<img[^>]*?)\s{2,}(.*?>)/ig, killspaces);

	//Replace entities
	//sContent = this.HTMLEntitiesReplace(sContent);
	return sContent;
}

JCLightHTMLEditor.prototype.ResizeFrame = function()
{
	if (this.bFloatingToolbar)
		return;

	var
		height = parseInt(this.pFrame.offsetHeight),
		buttonsHeight = this.toolbarLineCount * 25;
	this.pFrameTable.style.height = height + 'px';
	if (height - buttonsHeight - 5 > 0)
		this.pEditCont.style.height = (height - buttonsHeight - 5) + 'px';

	this.pButtonsCell.style.height = buttonsHeight + 'px';
}

JCLightHTMLEditor.prototype.BuildFloatToolbar = function()
{
	var pos = jsUtils.GetRealPos(this.pFrame);
	pos.top -= this.toolbarLineCount * 25 + 4;
	var _this= this;
	this.bFloatToolbarShowed = true;

	window['lhe_floating_tlb_stop_' + _this.id] = function()
	{
		if (_this.oTransOverlay && _this.oTransOverlay.bShowed)
			_this.oTransOverlay.Hide();
		jsUtils.removeEvent(document, "mouseup", window['lhe_floating_tlb_stop_' + _this.id]);
	};

	this.pFloatToolbarToggle.title = LHE_MESS.DragFloatingToolbar;
	this.pFloatToolbarToggle.ondrag = jsUtils.False;
	this.pFloatToolbarToggle.onselectstart = jsUtils.False;

	this.pFloatToolbarToggle.onmousedown = function(e)
	{
		_this.oTransOverlay.Show();
		jsUtils.addEvent(document, "mouseup", window['lhe_floating_tlb_stop_' + _this.id]);
		jsFloatDiv.StartDrag(e, _this.pFloatToolbar);
	};

	document.body.appendChild(this.pFloatToolbar); // Reappend float div to body (for IE)
	jsFloatDiv.Show(this.pFloatToolbar, pos.left, pos.top, 5, false, false);
	jsFloatDiv.AdjustShadow(this.pFloatToolbar);

	if (this.arConfig.bArisingToolbar)
		this.ShowFloatToolbar(false);
}

JCLightHTMLEditor.prototype.RelocateFloatToolbar = function()
{
	var pos = jsUtils.GetRealPos(this.pFrame);
	pos.top -= this.toolbarLineCount * 25 + 4;
	this.bFloatToolbarShowed = true;
	jsFloatDiv.Show(this.pFloatToolbar, pos.left, pos.top, 5, false, false);
	if (this.arConfig.bArisingToolbar)
		this.ShowFloatToolbar(false);
}

JCLightHTMLEditor.prototype.ShowFloatToolbar = function(bShow)
{
	if (this.bFloatToolbarShowed == bShow)
		return;

	if (bShow)
	{
		this.pFloatToolbar.style.display = 'block';
		jsFloatDiv.UnhideShadow(this.pFloatToolbar);
	}
	else
	{
		this.pFloatToolbar.style.display = 'none';
		jsFloatDiv.HideShadow(this.pFloatToolbar);
	}
	this.bFloatToolbarShowed = bShow;
}

JCLightHTMLEditor.prototype.ResizeFloatToolbar = function()
{
	this.pButtonsContWidth = (23 * this.buttonsCount + 9);
	this.pButtonsCont.style.width = this.pButtonsContWidth + 'px';
	if (this.bFloatingToolbar)
		this.pFloatToolbar.style.width = (this.pButtonsContWidth + 12) + 'px';
	else
		this.ResizeFrame();
}

JCLightHTMLEditor.prototype.AddButtons = function()
{
	var
		i, l, butId, grInd, arButtons,
		toolbarConfig = this.arConfig.toolbarConfig;
	this.buttonsCount = 0;

	if(!toolbarConfig)
		toolbarConfig = [
			//'Source',
			'Bold', 'Italic', 'Underline', 'RemoveFormat', //'InsertHR',
			//'Anchor',
			'CreateLink', 'DeleteLink', 'Image', //'SpecialChar',
			'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyFull',
			'InsertOrderedList', 'InsertUnorderedList', 'Outdent', 'Indent',
			//'=|=',
			'BackColor', 'ForeColor',
			'Video',
			'Table',
			'StyleList', 'HeaderList',
			'FontList', 'FontSizeList'
			//smiles:['SmileList']
		];


	this.ToolbarStartLine();

	for(i in toolbarConfig)
	{
		butId = toolbarConfig[i];
		if (typeof butId != 'string')
			continue;

		if (butId == '=|=')
			this.ToolbarNewtLine();
		else if (LHEButtons[butId])
			this.AddButton(LHEButtons[butId]);
	}

	this.ToolbarEndLine();
	if (!this.pButtonsContWidth)
		this.ResizeFloatToolbar();
}

JCLightHTMLEditor.prototype.AddButton = function(oBut)
{
	if (oBut.bBBHide && this.arConfig.bBBCode || (!this.arConfig.bBBCode && oBut.bBBShow))
		return;

	if (oBut.parser && oBut.parser.obj)
		this.oSpecialParsers[oBut.parser.name] = oBut.parser.obj;
	if (oBut.parsers)
	{
		for(var i = 0, cnt = oBut.parsers.length; i < cnt; i++)
			if (oBut.parsers[i] && oBut.parsers[i].obj)
				this.oSpecialParsers[oBut.parsers[i].name] = oBut.parsers[i].obj;
	}

	this.buttonsCount++;
	if (!oBut.type || !oBut.type == 'button')
	{
		var pButton = new window.LHEButton(oBut, this);
		this.pButtonsCont.appendChild(pButton.pCont);
	}
	else if (oBut.type == 'Colorpicker')
	{
		var pColorpicker = new window.LHEColorPicker(oBut, this);
		this.pButtonsCont.appendChild(pColorpicker.pCont);
	}
	else if (oBut.type == 'List')
	{
		var pList = new window.LHEList(oBut, this);
		this.pButtonsCont.appendChild(pList.pCont);
	}
}

JCLightHTMLEditor.prototype.ToolbarStartLine = function()
{
	var oDiv = jsUtils.CreateElement("DIV", {className: 'lhe-line-begin'});
	this.pButtonsCont.appendChild(oDiv);
}

JCLightHTMLEditor.prototype.ToolbarEndLine = function()
{
	var oDiv = jsUtils.CreateElement("DIV", {className: 'lhe-line-end'});
	this.pButtonsCont.appendChild(oDiv);
}

JCLightHTMLEditor.prototype.ToolbarNewtLine = function()
{
	this.toolbarLineCount++;
	this.ToolbarEndLine();
	this.ResizeFloatToolbar();
	this.ToolbarStartLine();
}

JCLightHTMLEditor.prototype.OpenDialog = function(arParams)
{
	var oDialog = new window.LHEDialog(arParams, this);
}

JCLightHTMLEditor.prototype.GetSelectionObject = function()
{
	var oSelection, oRange, root;
	if(this.pEditorDocument.selection) // IE
	{
		oSelection = this.pEditorDocument.selection;
		oRange = oSelection.createRange();

		if(oSelection.type=="Control")
			return oRange.commonParentElement();

		return oRange.parentElement();
	}
	else // FF
	{
		oSelection = this.pEditorWindow.getSelection();
		if(!oSelection)
			return false;

		var container, i, rangeCount = oSelection.rangeCount, obj;
		for(var i = 0; i < rangeCount; i++)
		{
			oRange = oSelection.getRangeAt(i);
			container = oRange.startContainer;
			if(container.nodeType != 3)
			{
				if(container.nodeType == 1 && container.childNodes.length <= 0)
					obj = container;
				else
					obj = container.childNodes[oRange.startOffset];
			}
			else
			{
				temp = oRange.commonAncestorContainer;
				while(temp && temp.nodeType == 3)
					temp = temp.parentNode;
				obj = temp;
			}
			root = (i == 0) ? obj : BXFindParentElement(root, obj);
		}
		return root;
	}
}

JCLightHTMLEditor.prototype.GetSelectionObjects = function ()
{
	var oSelection;
	if(this.pEditorDocument.selection) // IE
	{
		oSelection = this.pEditorDocument.selection;
		var s = oSelection.createRange();

		if(oSelection.type=="Control")
			return s.commonParentElement();

		return s.parentElement();
	}
	else // FF
	{
		oSelection = this.pEditorWindow.getSelection();
		if(!oSelection)
			return false;
		var oRange;
		var container, temp;
		var res = [];
		for(var i = 0; i < oSelection.rangeCount; i++)
		{
			oRange = oSelection.getRangeAt(i);
			container = oRange.startContainer;
			if(container.nodeType != 3)
			{
				if(container.nodeType == 1 && container.childNodes.length <= 0)
					res[res.length] = container;
				else
					res[res.length] = container.childNodes[oRange.startOffset];
			}
			else
			{
				temp = oRange.commonAncestorContainer;
				while(temp && temp.nodeType == 3)
					temp = temp.parentNode;
				res[res.length] = temp;
			}
		}
		if(res.length > 1)
			return res;
		return res[0];
	}
};

JCLightHTMLEditor.prototype.GetSelectionRange = function()
{
	//try{
	var
		oDoc = this.pEditorDocument,
		oWin = this.pEditorWindow,
		oRange,
		oSel = this.GetSelection(oDoc, oWin);

		if (oSel)
		{
			if (oDoc.createRange)
				oRange = oSel.getRangeAt(0);
			else
				oRange = oSel.createRange();
		}
		else
			oRange = false;

	return oRange;
	//catch(e){/*_alert('ERROR: BXGetSelectionRange');*/}
}

JCLightHTMLEditor.prototype.SelectRange = function(oRange)
{
	var
		oDoc = this.pEditorDocument,
		oWin = this.pEditorWindow;
	this.ClearSelection();
	if (oDoc.createRange) // FF
	{
		var oSel = oWin.getSelection();
		oSel.removeAllRanges();
		oSel.addRange(oRange);
	}
	else //IE
	{
		oRange.select();
	}
}

JCLightHTMLEditor.prototype.SelectElement = function (pElement)
{
	//try{
	var
		oRange,
		oDoc = this.pEditorDocument,
		oWin = this.pEditorWindow;

	if(oWin.getSelection)
	{
		var oSel = oWin.getSelection();
		oSel.selectAllChildren(pElement);
		oRange = oSel.getRangeAt(0);
	}
	else
	{
		oDoc.selection.empty();
		oRange = oDoc.selection.createRange();
		oRange.moveToElementText(pElement);
		oRange.select();
	}
	return oRange;
	//}catch(e){}
};

JCLightHTMLEditor.prototype.GetSelectedText = function(oRange)
{
	// Get selected text
	var selectedText = '';
	if (oRange.startContainer && oRange.endContainer) // DOM Model
	{
		if (oRange.startContainer == oRange.endContainer && (oRange.endContainer.nodeType == 3 || oRange.endContainer.nodeType == 1))
			selectedText = oRange.startContainer.textContent.substring(oRange.startOffset, oRange.endOffset);
	}
	else // IE
	{
		if (oRange.text == oRange.htmlText)
			selectedText = oRange.text;
	}
	return selectedText || '';
};

JCLightHTMLEditor.prototype.ClearSelection = function()
{
	var
		oDoc = this.pEditorDocument,
		oWin = this.pEditorWindow;

	if (oWin.getSelection)
		oWin.getSelection().removeAllRanges();
	else
		oDoc.selection.empty();
}


JCLightHTMLEditor.prototype.GetSelection = function(oDoc, oWin)
{
	if (!oDoc)
		oDoc = document;
	if (!oWin)
		oWin = window;

	var oSel = false;
	if (oWin.getSelection)
		oSel = oWin.getSelection();
	else if (oDoc.getSelection)
		oSel = oDoc.getSelection();
	else if (oDoc.selection)
		oSel = oDoc.selection;
	return oSel;
}

JCLightHTMLEditor.prototype.InsertHTML = function(sContent)
{
	this.SetFocus();
	//try{
		if(jsUtils.bIsIE)
		{
			var oRng = this.pEditorDocument.selection.createRange();
			oRng.pasteHTML(sContent);
			oRng.collapse(false);
			oRng.select();
		}
		else
		{
			this.pEditorWindow.document.execCommand('insertHTML', false, sContent);
		}
	//}catch(e){}
	//this.OnChange("insertHTML", "");
}

JCLightHTMLEditor.prototype.AppendCSS = function(styles)
{
	styles = jsUtils.trim(styles);
	if (styles.length <= 0)
		return false;

	var
		pDoc = this.pEditorDocument,
		pHeads = pDoc.getElementsByTagName("HEAD");

	if(pHeads.length != 1)
		return false;

	if(jsUtils.bIsIE)
	{
		setTimeout(function()
		{
			if (pDoc.styleSheets.length == 0)
				pHeads[0].appendChild(pDoc.createElement("STYLE"));
			pDoc.styleSheets[0].cssText += styles;
		}, 5);
	}
	else
	{
		var xStyle = pDoc.createElement("STYLE");
		pHeads[0].appendChild(xStyle);
		xStyle.appendChild(pDoc.createTextNode(styles));
	}
	return true;
}

bxhtmlspecialchars = function(s){return s};

function BXFindParentElement(pElement1, pElement2)
{
	var p, arr1 = [], arr2 = [];
	while((pElement1 = pElement1.parentNode)!=null)
		arr1[arr1.length] = pElement1;
	while((pElement2 = pElement2.parentNode)!=null)
		arr2[arr2.length] = pElement2;

	var min, diff1 = 0, diff2 = 0;
	if(arr1.length<arr2.length)
	{
		min = arr1.length;
		diff2 = arr2.length - min;
	}
	else
	{
		min = arr2.length;
		diff1 = arr1.length - min;
	}

	for(var i=0; i<min-1; i++)
	{
		if(BXElementEqual(arr1[i+diff1], arr2[i+diff2]))
			return arr1[i+diff1];
	}
	return arr1[0];
}

window.BXFindParentByTagName = function (pElement, tagName)
{
	tagName = tagName.toUpperCase();
	while(pElement && (pElement.nodeType!=1 || pElement.tagName.toUpperCase() != tagName))
		pElement = pElement.parentNode;
	return pElement;
}


function SetAttr(pEl, attr, val)
{
	if(attr=='className' && !jsUtils.bIsIE)
		attr = 'class';

	if(val.length <= 0)
		pEl.removeAttribute(attr);
	else
		pEl.setAttribute(attr, val);
}

