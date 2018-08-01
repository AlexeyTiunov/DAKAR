if (!window.LHEButtons)
	LHEButtons = {};

LHEButtons['Source'] = {
	id : 'Source',
	codeEditorMode : true,
	name : LHE_MESS.Source,
	handler : function(pBut)
	{
		var bHtml = pBut.pLEditor.sEditorMode == 'html';
		pBut.pLEditor.SetView(bHtml ? 'code' : 'html');
		pBut.Check(bHtml);
	}
};

// BASE
LHEButtons['Anchor'] = {
	id: 'Anchor',
	name: LHE_MESS.Anchor,
	bBBHide: true,
	handler: function(pBut)
	{
		var p = pBut.pLEditor.GetSelectionObject();
		if (!p || !p.getAttribute || p.getAttribute("__bxtagname") != "anchor")
			p = false;
		pBut.pLEditor.OpenDialog({ id: 'Anchor', obj: p });
	},
	parser:
	{
		name: "anchor",
		obj: {
			Parse: function(sName, sContent, pLEditor)
			{
				return sContent.replace(
					/<a[^>]*?name[^=]*?=([^>]+?)(?:>\s*?<\/a)?(?:\/?)?>/ig,
					function(sContent, name)
					{
						if (sContent.toLowerCase().indexOf("href") > 0)
							return sContent;
						name = name.replace(/[^\w\d_]/gi, '');
						return '<img id="' + name + '" src="/bitrix/images/1.gif" style="background-image: url(/bitrix/images/fileman/light_htmledit/lhe_iconkit.gif); background-position: -260px 0; height: 20px; width: 20px"  __bxtagname="' + sName + '" />';
					}
				);

			},
			UnParse: function(sName, pNode, pLEditor)
			{
				return '<a name="' + pNode.arAttributes['id'] + '"></a>';
			} 
		}
	}
};

LHEButtons['CreateLink'] = {
	id : 'CreateLink',
	name : LHE_MESS.CreateLink,
	name_edit : LHE_MESS.EditLink,
	handler : function (pBut)
	{
		var p = (pBut.arSelectedElement && pBut.arSelectedElement['A']) ? pBut.arSelectedElement['A'] : pBut.pLEditor.GetSelectionObject();
		if (!p || p.tagName != 'A')
			p = false;

		pBut.pLEditor.OpenDialog({id : 'Link', obj: p, bCM: !!pBut.menu});
	}
};

LHEButtons['DeleteLink'] = {
	id : 'DeleteLink',
	name : LHE_MESS.DeleteLink,
	cmd : 'Unlink',
	handler : function(pBut)
	{
		var p = (pBut.arSelectedElement && pBut.arSelectedElement['A']) ? pBut.arSelectedElement['A'] : pBut.pLEditor.GetSelectionObject();
		if(p && p.tagName != 'A')
			p = jsUtils.FindParentObject(pBut.pLEditor.GetSelectionObject(), 'A');

		if (jsUtils.bIsIE && !p)
		{
			var oRange = pBut.pLEditor.GetSelectionRange();
			if (pBut.pLEditor.GetSelectedText(oRange) == '')
			{
				pBut.pLEditor.InsertHTML('<img id="bx_lhe_temp_bogus_node" src="/bitrix/images/1.gif" _moz_editor_bogus_node="on" style="border: 0px !important;"/>');
				var bogusImg = pBut.pLEditor.pEditorDocument.getElementById('bx_lhe_temp_bogus_node');
				if (bogusImg)
				{
					p = jsUtils.FindParentObject(bogusImg, 'A');
					bogusImg.parentNode.removeChild(bogusImg);
				}
			}
		}

		if (p)
		{
			if (!jsUtils.bIsIE)
				pBut.pLEditor.SelectElement(p);
			pBut.pLEditor.executeCommand('Unlink');
		}
	}
};

LHEButtons['Image'] = {
	id : 'Image',
	name : LHE_MESS.Image,
	name_edit : LHE_MESS.EditImage,
	handler : function (pBut)
	{
		var p = (pBut.arSelectedElement && pBut.arSelectedElement['IMG']) ? pBut.arSelectedElement['IMG'] : pBut.pLEditor.GetSelectionObject();
		if (!p || p.tagName != 'IMG')
			p = false;
		pBut.pLEditor.OpenDialog({id : 'Image', obj: p});
	}
};

//LHEButtons['SpecialChar'] = {
//	id : 'SpecialChar',
//	name : LHE_MESS.SpecialChar,
//	handler : function (pBut) {pBut.pLEditor.OpenDialog({id : 'SpecialChar'});}
//};

LHEButtons['Bold'] = {id : 'Bold', name : LHE_MESS.Bold, cmd : 'Bold'};
LHEButtons['Italic']	= {id : 'Italic', name : LHE_MESS.Italic, cmd : 'Italic'};
LHEButtons['Underline'] = {id : 'Underline', name : LHE_MESS.Underline, cmd : 'Underline'};
LHEButtons['RemoveFormat'] = {id : 'RemoveFormat', name : LHE_MESS.RemoveFormat, cmd : 'RemoveFormat'};

LHEButtons['JustifyLeft'] = {id : 'JustifyLeft', name : LHE_MESS.JustifyLeft, cmd : 'JustifyLeft'};
LHEButtons['JustifyCenter'] = {id : 'JustifyCenter', name : LHE_MESS.JustifyCenter, cmd : 'JustifyCenter'};
LHEButtons['JustifyRight'] = {id : 'JustifyRight', name : LHE_MESS.JustifyRight, cmd : 'JustifyRight'};
LHEButtons['JustifyFull'] = {id : 'JustifyFull', name : LHE_MESS.JustifyFull, cmd : 'JustifyFull'};

LHEButtons['InsertOrderedList'] = {id : 'InsertOrderedList', name : LHE_MESS.OrderedList, cmd : 'InsertOrderedList', bBBHide: true};
LHEButtons['InsertUnorderedList'] = {id : 'InsertUnorderedList', name : LHE_MESS.UnorderedList, cmd : 'InsertUnorderedList'};
LHEButtons['Outdent'] = {id : 'Outdent', name : LHE_MESS.Outdent, cmd : 'Outdent', bBBHide: true};
LHEButtons['Indent'] = {id : 'Indent', name : LHE_MESS.Indent, cmd : 'Indent', bBBHide: true};

LHEButtons['Video'] = {
	id: 'Video',
	name: LHE_MESS.InsertVideo,
	name_edit: LHE_MESS.EditVideo,
	handler: function(pBut)
	{
		var p = pBut.pLEditor.GetSelectionObject();
		if (!p || !p.getAttribute || p.getAttribute("__bxtagname") != "video")
			p = false;
		pBut.pLEditor.OpenDialog({ id: 'Video', obj: p });
	},
	parser:
	{
		name: "video",
		obj:
		{
			Parse: function(sName, sContent, pLEditor)
			{
				// **** Parse WMV ****
				// b1, b3 - quotes
				// b2 - id of the div
				// b4 - javascript config
				var ReplaceWMV = function(str, b1, b2, b3, b4)
				{
					var 
						id = b2,
						JSConfig, w, h, prPath;

					try {eval('JSConfig = ' + b4); } catch (e) { JSConfig = false; }
					if (!id || !JSConfig)
						return '';

					pLEditor.arVideos[id] = JSConfig;
					w = (parseInt(JSConfig.width) || 50) + 'px';
					h = (parseInt(JSConfig.height) || 25) + 'px';
					prPath = JSConfig.image || '/bitrix/images/fileman/light_htmledit/video.gif';

					return '<img id="' + id + '" src="' + pLEditor.oneGifSrc + '" style="background: #E2DFDA url(' + prPath + ') center center no-repeat; width: ' + w + '; height: ' + h + ';"  __bxtagname="' + sName + '" title="' + LHE_MESS.Video + ': ' + JSConfig.file + '"/>';
				}
				sContent = sContent.replace(/<script.*?silverlight\.js.*?<\/script>\s*?<script.*?wmvplayer\.js.*?<\/script>\s*?<div.*?id\s*?=\s*?("|\')(.*?)\1.*?<\/div>\s*?<script.*?jeroenwijering\.Player\(document\.getElementById\(("|\')\2\3.*?wmvplayer\.xaml.*?({.*?})\).*?<\/script>/ig, ReplaceWMV);

				// **** Parse FLV ****
				var ReplaceFLV = function(str, attr)
				{
					attr = attr.replace(/[\r\n]+/ig, ' '); attr = attr.replace(/\s+/ig, ' '); attr = jsUtils.trim(attr);
					var 
						arParams = {},
						arFlashvars = {},
						w, h, id, prPath;

					attr.replace(/([^\w]??)(\w+?)\s*=\s*("|\')([^\3]+?)\3/ig, function(s, b0, b1, b2, b3)
					{
						b1 = b1.toLowerCase();
						if (b1 == 'src' || b1 == 'type' || b1 == 'allowscriptaccess' || b1 == 'allowfullscreen' || b1 == 'pluginspage' || b1 == 'wmode')
							return '';
						arParams[b1] = b3; return b0;
					});
					id = arParams.id;

					if (!id || !arParams.flashvars)
						return str;

					arParams.flashvars += '&';
					arParams.flashvars.replace(/(\w+?)=((?:\s|\S)*?)&/ig, function(s, name, val) { arFlashvars[name] = val; return ''; });
					w = (parseInt(arParams.width) || 50) + 'px';
					h = (parseInt(arParams.height) || 25) + 'px';
					arParams.flashvars = arFlashvars;
					pLEditor.arVideos[id] = arParams;
					prPath = arFlashvars.image || '/bitrix/images/fileman/light_htmledit/video.gif';

					return '<img id="' + id + '" src="' + pLEditor.oneGifSrc + '" style="background: #E2DFDA url(' + prPath + ') center center no-repeat; width: ' + w + '; height: ' + h + ';"  __bxtagname="' + sName + '" title="' + LHE_MESS.Video + ': ' + arParams.flashvars.file + '"/>';
				}

				sContent = sContent.replace(/<embed((?:\s|\S)*?player\/mediaplayer\/player\.swf(?:\s|\S)*?)(?:>\s*?<\/embed)?(?:\/?)?>/ig, ReplaceFLV);

				return sContent;
			},
			UnParse: function(sName, pNode, pLEditor)
			{
				var videoId = pNode.arAttributes["id"];
				if (!videoId || !pLEditor.arVideos[videoId])
					return '';

				var 
				arParams = pLEditor.arVideos[videoId],
				i, str;

				//arParams
				var arVidConf = pLEditor.arConfig.videoSettings;
				if (arVidConf.maxWidth && arParams.width && parseInt(arParams.width) > parseInt(arVidConf.maxWidth))
					arParams.width = arVidConf.maxWidth;
				if (arVidConf.maxHeight && arParams.height && parseInt(arParams.height) > parseInt(arVidConf.maxHeight))
					arParams.height = arVidConf.maxHeight;

				if (arParams['flashvars']) // FLV
				{
					str = '<embed src="/bitrix/components/bitrix/player/mediaplayer/player.swf" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" pluginspage="http:/' + '/www.macromedia.com/go/getflashplayer" ';
					str += 'id="' + videoId + '" ';
					if (arVidConf.WMode)
						str += 'WMode="' + arVidConf.WMode + '" ';

					for (i in arParams)
					{
						if (i == 'flashvars')
						{
							if (arVidConf.bufferLength)
								arParams[i].bufferlength = arVidConf.bufferLength;
							if (arVidConf.skin)
								arParams[i].skin = arVidConf.skin;
							if (arVidConf.logo)
								arParams[i].logo = arVidConf.logo;
							str += 'flashvars="';
							for (k in arParams[i])
								str += k + '=' + arParams[i][k] + '&';
							str = str.substring(0, str.length - 1) + '" ';
						}
						else
						{
							str += i + '="' + arParams[i] + '" ';
						}
					}
					str += '></embed>';
				}
				else // WMV
				{
					str = '<script type="text/javascript" src="/bitrix/components/bitrix/player/wmvplayer/silverlight.js" /></script>' +
				'<script type="text/javascript" src="/bitrix/components/bitrix/player/wmvplayer/wmvplayer.js"></script>' +
				'<div id="' + videoId + '">WMV Player</div>' +
				'<script type="text/javascript">new jeroenwijering.Player(document.getElementById("' + videoId + '"), "/bitrix/components/bitrix/player/wmvplayer/wmvplayer.xaml", {';

					if (arVidConf.bufferLength)
						arParams.bufferlength = arVidConf.bufferLength;
					if (arVidConf.logo)
						arParams.logo = arVidConf.logo;
					if (arVidConf.windowless)
						arParams.windowless = arVidConf.windowless ? true : false;

					for (i in arParams)
						str += i + ': "' + arParams[i] + '", ';
					str = str.substring(0, str.length - 2);

					str += '});</script>';
				}
				return str;
			}
		}
	}
};

LHEButtons['SmileList'] = {
	id : 'SmileList',
	name : LHE_MESS.SmileList,
	bBBShow: true,
	type: 'List',
	handler: function() {},
	OnCreate: function(pList)
	{
		var
			arSmiles = pList.pLEditor.arConfig.arSmiles,
			pImg, pSmile, i, oSmile, k;

		pList.pValuesCont.style.width = '100px';
		pList.oSmiles = {};

		for (i in arSmiles)
		{
			oSmile = arSmiles[i];
			if (typeof oSmile != 'object' || !oSmile.path || !oSmile.code)
				continue;

			k = 'smile_' + Math.random().toString().substring(4) + '_' + pList.pLEditor.id;
			pSmile = jsUtils.CreateElement("DIV", {className: 'lhe-smile-cont', title: oSmile.name, id: k});
			pImg = pSmile.appendChild(jsUtils.CreateElement("IMG", {src: oSmile.path, className: 'lhe-smile'}));
			pImg.onerror = function(){var d = this.parentNode; d.parentNode.removeChild(d);};
			pList.oSmiles[k] = oSmile;

			pSmile.onclick = function(){pList.oBut.SetSmile(this.id, pList);};
			pSmile.onmouseover = function(){this.className = 'lhe-smile-cont lhe-smile-cont-over';};
			pSmile.onmouseout = function(){this.className = 'lhe-smile-cont';};

			pList.pValuesCont.appendChild(pSmile);
		}
	},
	SetSmile: function(k, pList)
	{
		pList.pLEditor.SelectRange(pList.pLEditor.oPrevRange);
		var oSmile = pList.oSmiles[k];
		pList.pLEditor.InsertHTML('<img __bxtagname="smile" id="' + k + '" src="' + oSmile.path + '" title="' + oSmile.name + '"/>');
		pList.Close();
	}
};


LHEButtons['HeaderList'] = {
	id : 'HeaderList',
	name : LHE_MESS.HeaderList,
	bBBHide: true,
	type: 'List',
	handler: function() {},
	OnCreate: function(pList)
	{
		var
			pIt, pItem, i, oItem;

		pList.arItems = [
			{value: 'p', name: LHE_MESS.Normal},
			{value: 'h1', name: LHE_MESS.Heading + ' 1'},
			{value: 'h2', name: LHE_MESS.Heading + ' 2'},
			{value: 'h3', name: LHE_MESS.Heading + ' 3'},
			{value: 'h4', name: LHE_MESS.Heading + ' 4'},
			{value: 'h5', name: LHE_MESS.Heading + ' 5'},
			{value: 'h6', name: LHE_MESS.Heading + ' 6'},
			{value: 'pre', name: LHE_MESS.Preformatted}
		];

		var innerCont = jsUtils.CreateElement("DIV", {className: 'lhe-header-innercont'});

		for (i = 0; i < pList.arItems.length; i++)
		{
			oItem = pList.arItems[i];
			if (typeof oItem != 'object' || !oItem.name)
				continue;

			pItem = jsUtils.CreateElement("DIV", {className: 'lhe-header-cont', title: oItem.name, id: 'lhe_header__' + i});
			pIt = pItem.appendChild(jsUtils.CreateElement(oItem.value.toUpperCase()));
			pIt.innerHTML = oItem.name;
			oItem.pWnd = pItem;

			pItem.onclick = function(){pList.oBut.Select(pList.arItems[this.id.substring('lhe_header__'.length)], pList);};
			pItem.onmouseover = function(){this.className = 'lhe-header-cont lhe-header-cont-over';};
			pItem.onmouseout = function(){this.className = 'lhe-header-cont';};

			innerCont.appendChild(pItem);
		}
		pList.pValuesCont.appendChild(innerCont);
	},
	OnOpen: function(pList)
	{
		var
			frm = pList.pLEditor.queryCommand('FormatBlock'),
			i, v;

		if (pList.pSelectedItemId >= 0)
			pList.SelectItem(false);

		if (!frm)
			frm = 'p';
		for (i = 0; i < pList.arItems.length; i++)
		{
			v = pList.arItems[i];
			if (v.value == frm)
			{
				pList.pSelectedItemId = i;
				pList.SelectItem(true);
			}
		}
	},
	Select: function(oItem, pList)
	{
		pList.pLEditor.SelectRange(pList.pLEditor.oPrevRange);
		pList.pLEditor.executeCommand('FormatBlock', '<' + oItem.value + '>');
		pList.Close();
	}
};

LHEButtons['FontList'] = {
	id : 'FontList',
	name : LHE_MESS.FontList,
	//bBBHide: true,
	type: 'List',
	handler: function() {},
	OnCreate: function(pList)
	{
		var
			pIt, pItem, i, oItem, font;

		pList.arItems = [];
		for (i in pList.pLEditor.arConfig.arFonts)
		{
			font = pList.pLEditor.arConfig.arFonts[i];
			if (typeof font == 'string')
				pList.arItems.push({value: font, name: font});
		}

		for (i = 0; i < pList.arItems.length; i++)
		{
			oItem = pList.arItems[i];
			if (typeof oItem != 'object' || !oItem.name)
				continue;

			pItem = jsUtils.CreateElement("DIV", {className: 'lhe-list-item-cont', title: oItem.name, id: 'lhe_font__' + i});
			pIt = pItem.appendChild(jsUtils.CreateElement('SPAN', {className: 'lhe-list-font-span'}, {fontFamily: oItem.value}));
			pIt.innerHTML = oItem.name;
			oItem.pWnd = pItem;

			pItem.onclick = function(){pList.oBut.Select(pList.arItems[this.id.substring('lhe_font__'.length)], pList);};
			pItem.onmouseover = function(){this.className = 'lhe-list-item-cont lhe-list-item-cont-over';};
			pItem.onmouseout = function(){this.className = 'lhe-list-item-cont';};

			pList.pValuesCont.appendChild(pItem);
		}
	},
	OnOpen: function(pList)
	{
		var
			frm = pList.pLEditor.queryCommand('FontName'),
			i, v;
		if (pList.pSelectedItemId >= 0)
			pList.SelectItem(false);

		if (!frm)
			frm = 'p';
		for (i = 0; i < pList.arItems.length; i++)
		{
			v = pList.arItems[i];
			if (v.value.toLowerCase() == frm.toLowerCase())
			{
				pList.pSelectedItemId = i;
				pList.SelectItem(true);
			}
		}
	},
	Select: function(oItem, pList)
	{
		pList.pLEditor.SelectRange(pList.pLEditor.oPrevRange);
		pList.pLEditor.executeCommand('FontName', oItem.value);
		pList.Close();
	}
};

LHEButtons['FontSizeList'] = {
	id : 'FontSizeList',
	name : LHE_MESS.FontSizeList,
	type: 'List',
	handler: function() {},
	OnCreate: function(pList)
	{
		var
			pIt, pItem, i, oItem, fontSize;

		pList.arItems = [];
		for (i in pList.pLEditor.arConfig.arFontSizes)
		{
			fontSize = pList.pLEditor.arConfig.arFontSizes[i];
			if (typeof fontSize == 'string')
				pList.arItems.push({value: parseInt(i), name: fontSize});
		}

		for (i = 0; i < pList.arItems.length; i++)
		{
			oItem = pList.arItems[i];
			if (typeof oItem != 'object' || !oItem.name)
				continue;

			pItem = jsUtils.CreateElement("DIV", {className: 'lhe-list-item-cont', title: oItem.name, id: 'lhe_font_size__' + i});
			pIt = pItem.appendChild(jsUtils.CreateElement('SPAN', {className: 'lhe-list-font-span'}, {fontSize: oItem.name}));

			pIt.innerHTML = oItem.name;
			oItem.pWnd = pItem;

			pItem.onclick = function(){pList.oBut.Select(pList.arItems[this.id.substring('lhe_font_size__'.length)], pList);};
			pItem.onmouseover = function(){this.className = 'lhe-list-item-cont lhe-list-item-cont-over';};
			pItem.onmouseout = function(){this.className = 'lhe-list-item-cont';};

			pList.pValuesCont.appendChild(pItem);
		}
	},
	OnOpen: function(pList)
	{
		var
			frm = pList.pLEditor.queryCommand('FontSize'),
			i, v;
		if (pList.pSelectedItemId >= 0)
			pList.SelectItem(false);

		if (!frm)
			frm = 'p';
		frm = frm.toString().toLowerCase();
		for (i = 0; i < pList.arItems.length; i++)
		{
			v = pList.arItems[i];
			if (v.value.toString().toLowerCase() == frm)
			{
				pList.pSelectedItemId = i;
				pList.SelectItem(true);
			}
		}
	},
	Select: function(oItem, pList)
	{
		pList.pLEditor.SelectRange(pList.pLEditor.oPrevRange);
		pList.pLEditor.executeCommand('FontSize', oItem.value);
		pList.Close();
	}
};

LHEButtons['BackColor'] = {
	id : 'BackColor',
	name : LHE_MESS.BackColor,
	type: 'Colorpicker',
	OnSelect: function(color, pCol)
	{
		if(jsUtils.bIsIE)
		{
			pCol.pLEditor.executeCommand('BackColor', color || '');
		}
		else
		{	
			//try{
				pCol.pLEditor.pEditorDocument.execCommand("styleWithCSS", false, true);
				if (!color)
					pCol.pLEditor.executeCommand('removeFormat');
				else
					pCol.pLEditor.executeCommand('hilitecolor', color);

				pCol.pLEditor.pEditorDocument.execCommand("styleWithCSS", false, false);
			//}catch(e){}
		}
	}
};

LHEButtons['ForeColor'] = {
	id : 'ForeColor',
	name : LHE_MESS.ForeColor,
	type: 'Colorpicker',
	OnSelect: function(color, pCol)
	{
		if (!color && !jsUtils.bIsIE)
			pCol.pLEditor.executeCommand('removeFormat');
		else
			pCol.pLEditor.executeCommand('ForeColor', color || '');
	}
};

/* CONTEXT MENU*/
var LHEContMenu = {};
LHEContMenu["A"] = [LHEButtons['CreateLink'], LHEButtons['DeleteLink']];
LHEContMenu["IMG"] = [LHEButtons['Image']];
LHEContMenu["VIDEO"] = [LHEButtons['Video']];