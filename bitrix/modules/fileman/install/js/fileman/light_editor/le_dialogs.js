window.LHEDailogs = {};

window.LHEDailogs['Anchor'] = function(pObj)
{
	var str = '<table width="100%">' +
	'<tr>' +
		'<td class="lhe-dialog-label lhe-label-imp">' + LHE_MESS.AnchorName + ':</td>' +
		'<td class="lhe-dialog-param"><input type="text" size="20" value="" id="lhed_anchor_name"></td>' +
	'</tr>' +
	'<tr valign="top">' +
		'<td class="lhe-dialog-but-cell" colSpan="2"><input type="button" id="lhe_dialog_save" value="' + LHE_MESS.DialogSave + '"> <input type="button" id="lhe_dialog_cancel" value="' + LHE_MESS.DialogCancel + '"></td>' +
	'</tr></table>';

	var OnClose = function() {pObj.Close();};
	var OnSave = function()
	{
		pObj.pLEditor.SelectRange(pObj.pLEditor.oPrevRange);
		var anchorName = pObj.pName.value.replace(/[^\w\d]/gi, '_');
		if(pObj.pSel)
		{
			if(anchorName.length > 0)
				pObj.pSel.id = anchorName;
			else
				pObj.pLEditor.executeCommand('Delete');
		}
		else if(anchorName.length > 0)
		{
			pObj.pLEditor.InsertHTML('<img id="' + anchorName + '" src="' + pObj.pLEditor.oneGifSrc + '" style="background-image: url(/bitrix/images/fileman/light_htmledit/lhe_iconkit.gif); background-position: -260px 0; height: 20px; width: 20px"  __bxtagname="anchor" />');
		}
		pObj.Close();
	};

	return {
		title: LHE_MESS.AnchorProps,
		innerHTML : str,
		width: '300px',
		OnLoad: function()
		{
			pObj.pName = document.getElementById("lhed_anchor_name");
			pObj.pName.focus();
			if(pObj.pSel)
				pObj.pName.value = pObj.pSel.getAttribute('id');
			else
				pObj.pName.value = "";

			document.getElementById("lhe_dialog_save").onclick = OnSave;
			document.getElementById("lhe_dialog_cancel").onclick = OnClose;
		}
	};
}

window.LHEDailogs['Link'] = function(pObj)
{
	var strHref = pObj.pLEditor.arConfig.bUseFileDialogs ? '<input type="text" size="26" value="" id="lhed_link_href"><input type="button" value="..." style="width: 20px;" onclick="window.LHED_Link_FDOpen();">' : '<input type="text" size="30" value="" id="lhed_link_href">';

	var str = '<table width="100%">' +
	'<tr>' +
		'<td class="lhe-dialog-label lhe-label-imp">' + LHE_MESS.LinkText + ':</td>' +
		'<td class="lhe-dialog-param"><input type="text" size="30" value="" id="lhed_link_text"></td>' +
	'</tr>' +
	'<tr>' +
		'<td class="lhe-dialog-label lhe-label-imp">' + LHE_MESS.LinkHref + ':</td>' +
		'<td class="lhe-dialog-param">' + strHref + '</td>' +
	'</tr>';

	if (!pObj.pLEditor.arConfig.bBBCode)
	{
		str +=
	'<tr>' +
		'<td class="lhe-dialog-label">' + LHE_MESS.LinkTitle + ':</td>' +
		'<td class="lhe-dialog-param"><input type="text" size="30" value="" id="lhed_link_title"></td>' +
	'</tr>' +
	'<tr>' +
		'<td class="lhe-dialog-label">' + LHE_MESS.LinkTarget + '</td>' +
		'<td class="lhe-dialog-param">' +
			'<select id="lhed_link_target">' +
				'<option value="">' + LHE_MESS.LinkTarget_def + '</option>' +
				'<option value="_blank">' + LHE_MESS.LinkTarget_blank + '</option>' +
				'<option value="_parent">' + LHE_MESS.LinkTarget_parent + '</option>' +
				'<option value="_self">' + LHE_MESS.LinkTarget_self + '</option>' +
				'<option value="_top">' + LHE_MESS.LinkTarget_top + '</option>' +
			'</select>' +
		'</td>' +
	'</tr>';
	}
	str +=
	'<tr valign="top">' +
		'<td class="lhe-dialog-but-cell" colSpan="2"><input type="button" id="lhe_dialog_save" value="' + LHE_MESS.DialogSave + '"> <input type="button" id="lhe_dialog_cancel" value="' + LHE_MESS.DialogCancel + '"></td>' +
	'</tr></table>';

	var OnClose = function() {pObj.Close();};
	var OnSave = function()
	{
		var
			link, text,
			href = pObj.pHref.value;

		if (href.length  < 1 || (pObj.pText && pObj.pText.value.length <=0)) // Need for showing error
			return;

		pObj.pLEditor.SelectRange(pObj.pLEditor.oPrevRange);
		if (pObj.pSel)
		{
			link = pObj.pSel;
		}
		else
		{
			link = false;
			var sRand = '#'+Math.random().toString().substring(5);
			var pDoc = pObj.pLEditor.pEditorDocument;

			pDoc.execCommand('CreateLink', false, sRand);
			if(pDoc.evaluate)
			{
				link = pDoc.evaluate("//a[@href='"+sRand+"']", pDoc.body, null, 9, null).singleNodeValue;
			}
			else
			{
				var arLinks = pDoc.getElementsByTagName('A');
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

		if (!link) // Create new link
		{
			pObj.pLEditor.InsertHTML('<a id="bx_lhe_' + sRand + '">#</a>');
			link = pDoc.getElementById('bx_lhe_' + sRand);
			link.removeAttribute("id");
			link.innerHTML = (pObj.pText !== false && pObj.pText.value.length > 0) ? pObj.pText.value : href;
		}

		if(link)
		{
			if (pObj.pText !== false)
				link.innerHTML = pObj.pText.value;
			SetAttr(link, 'href', href);
			SetAttr(link, '__bxhref', href);
			if (!pObj.pLEditor.bBBCode)
			{
				SetAttr(link, 'title', pObj.pTitle.value);
				SetAttr(link, 'target', pObj.pTarget.value);
			}
		}
		pObj.Close();
	};

	return {
		title: LHE_MESS.LinkProps,
		innerHTML : str,
		width: '420px',
		OnLoad: function()
		{
			if(!pObj.pSel)
			{
				var bogusImg = pObj.pLEditor.pEditorDocument.getElementById('bx_lhe_temp_bogus_node');
				if (bogusImg)
				{
					pObj.pSel = jsUtils.FindParentObject(bogusImg, 'A');
					bogusImg.parentNode.removeChild(bogusImg);
				}
			}

			pObj.bNew = !pObj.pSel || pObj.pSel.tagName.toUpperCase() != 'A';
			pObj.pText = document.getElementById("lhed_link_text");
			pObj.pHref = document.getElementById("lhed_link_href");
			pObj.pHref.focus();
			if (!pObj.pLEditor.bBBCode)
			{
				pObj.pTitle = document.getElementById("lhed_link_title");
				pObj.pTarget = document.getElementById("lhed_link_target");
			}

			// Select Link
			if (!pObj.bNew && !jsUtils.bIsIE)
				pObj.pLEditor.oPrevRange = pObj.pLEditor.SelectElement(pObj.pSel);
			
			var
				selectedText = false,
				oRange = pObj.pLEditor.oPrevRange;

			// Get selected text
			if (oRange.startContainer && oRange.endContainer) // DOM Model
			{
				if (oRange.startContainer == oRange.endContainer && (oRange.endContainer.nodeType == 3 || oRange.endContainer.nodeType == 1))
					selectedText = oRange.startContainer.textContent.substring(oRange.startOffset, oRange.endOffset) || '';
			}
			else // IE
			{
				if (oRange.text == oRange.htmlText)
					selectedText = oRange.text || '';
			}

			if (selectedText === false)
			{
				var textRow = jsUtils.FindParentObject(pObj.pText, 'TR');
				textRow.parentNode.removeChild(textRow);
				pObj.pText = false;
			}
			else
			{
				pObj.pText.value = selectedText || '';
			}

			if (!pObj.bNew)
			{
				if (pObj.pText !== false)
					pObj.pText.value = pObj.pSel.innerHTML;
				pObj.pHref.value = pObj.pSel.getAttribute('__bxhref');
				if (!pObj.pLEditor.bBBCode)
				{
					pObj.pTitle.value = pObj.pSel.getAttribute('title') || '';
					pObj.pTarget.value = pObj.pSel.getAttribute('target') || '';
				}
			}
			document.getElementById("lhe_dialog_save").onclick = OnSave;
			document.getElementById("lhe_dialog_cancel").onclick = OnClose;
		}
	};
}

window.LHEDailogs['Image'] = function(pObj)
{
	var sText = '', i, strSrc;

	if (pObj.pLEditor.arConfig.bUseMedialib)
		strSrc = '<input type="text" size="30" value="" id="lhed_img_src"><input class="lhe-br-but" type="button" value="..." onclick="window.LHED_Img_MLOpen();">';
	else if (pObj.pLEditor.arConfig.bUseFileDialogs)
		strSrc = '<input type="text" size="30" value="" id="lhed_img_src"><input class="lhe-br-but" type="button" value="..." onclick="window.LHED_Img_FDOpen();">';
	else
		strSrc = '<input type="text" size="33" value="" id="lhed_img_src">';

	for (i = 0; i < 200; i++){sText += 'text ';}

	var str = '<table width="100%">' +
	'<tr>' +
		'<td class="lhe-dialog-label lhe-label-imp">' + LHE_MESS.ImageSrc + ':</td>' +
		'<td class="lhe-dialog-param">' + strSrc + '</td>' +
	'</tr>';
	if (!pObj.pLEditor.arConfig.bBBCode)
	{
		str +=
	'<tr>' +
		'<td class="lhe-dialog-label">' + LHE_MESS.ImageTitle + ':</td>' +
		'<td class="lhe-dialog-param"><input type="text" size="33" value="" id="lhed_img_title"></td>' +
	'</tr>' +
	'<tr>' +
		'<td class="lhe-dialog-label">' + LHE_MESS.ImgAlign + ':</td>' +
		'<td class="lhe-dialog-param">' +
			'<select id="lhed_img_align">' +
				'<option value="">' + LHE_MESS.LinkTarget_def + '</option>' +
				'<option value="top">' + LHE_MESS.ImgAlignTop + '</option>' +
				'<option value="right">' + LHE_MESS.ImgAlignRight + '</option>' +
				'<option value="bottom">' + LHE_MESS.ImgAlignBottom + '</option>' +
				'<option value="left">' + LHE_MESS.ImgAlignLeft + '</option>' +
				'<option value="middle">' + LHE_MESS.ImgAlignMiddle + '</option>' +
			'</select>' +
		'</td>' +
	'</tr>' +
	'<tr>' +
		'<td colSpan="2" class="lhe-dialog-param"><span class="lhed-img-preview-label">' + LHE_MESS.ImageSizing + ':</span>' +
		'<div class="lhed-img-size-cont"><input type="text" size="4" value="" id="lhed_img_width"> x <input type="text" size="4" value="" id="lhed_img_height"> <input type="checkbox" id="lhed_img_save_prop" checked><label for="lhed_img_save_prop">' + LHE_MESS.ImageSaveProp + '</label></div></td>' +
	'</tr>';
	}
	str +=
	'<tr>' +
		'<td colSpan="2" class="lhe-dialog-param"><span class="lhed-img-preview-label">' + LHE_MESS.ImagePreview + ':</span>' +
			'<div class="lhed-img-preview-cont"><img id="lhed_img_preview" style="display:none" />' + sText + '</div>' +
		'</td>' +
	'</tr>' +
	'<tr valign="top">' +
		'<td class="lhe-dialog-but-cell" colSpan="2"><input type="button" id="lhe_dialog_save" value="' + LHE_MESS.DialogSave + '"> <input type="button" id="lhe_dialog_cancel" value="' + LHE_MESS.DialogCancel + '"></td>' +
	'</tr></table>';
	var OnClose = function() {pObj.Close();};
	var OnSave = function()
	{
		var src = pObj.pSrc.value, img;

		if (src.length  < 1) // Need for showing error
			return;

		pObj.pLEditor.SelectRange(pObj.pLEditor.oPrevRange);
		if (pObj.pSel)
		{
			img = pObj.pSel;
		}
		else
		{
			var tmpid = Math.random().toString().substring(4);
			pObj.pLEditor.InsertHTML('<img id="' + tmpid + '" src="" />');
			img = pObj.pLEditor.pEditorDocument.getElementById(tmpid);
			img.removeAttribute("id");
		}
		SetAttr(img, "src", src);
		SetAttr(img, "__bxsrc", src);
		if (!pObj.pLEditor.bBBCode)
		{
			if (pObj.bSetInStyles)
			{
				img.style.width = pObj.pWidth.value + 'px';
				img.style.height = pObj.pHeight.value + 'px';
				SetAttr(img, "width", '');
				SetAttr(img, "height", '');
			}
			else
			{
				SetAttr(img, "width", pObj.pWidth.value);
				SetAttr(img, "height", pObj.pHeight.value);
				img.style.width = '';
				img.style.height = '';
			}

			SetAttr(img, "align", pObj.pAlign.value);
			SetAttr(img, "title", pObj.pTitle.value);
		}
		pObj.Close();
	};

	var PreviewOnLoad = function()
	{
		var w = parseInt(this.style.width || this.getAttribute('width') || this.offsetWidth);
		var h = parseInt(this.style.height || this.getAttribute('hright') || this.offsetHeight);
		if (!w || !h)
			return;
		pObj.iRatio = w / h; // Remember proportion
		pObj.curWidth = pObj.pWidth.value = w;
		pObj.curHeight = pObj.pHeight.value = h;
	};

	var PreviewReload = function()
	{
		var newSrc = pObj.pSrc.value;
		if (!newSrc) return;
		if (pObj.prevSrc != newSrc)
		{
			pObj.prevSrc = pObj.pPreview.src = newSrc;
			pObj.pPreview.style.display = "";
			pObj.pPreview.removeAttribute("width");
			pObj.pPreview.removeAttribute("height");
		}

		if (pObj.curWidth && pObj.curHeight)
		{
			pObj.pPreview.style.width = pObj.curWidth + 'px';
			pObj.pPreview.style.height = pObj.curHeight + 'px';
		}

		if (!pObj.pLEditor.bBBCode)
		{
			SetAttr(pObj.pPreview, 'align', pObj.pAlign.value);
			SetAttr(pObj.pPreview, 'title', pObj.pTitle.value);
		}
	};

	if (pObj.pLEditor.arConfig.bUseMedialib || pObj.pLEditor.arConfig.bUseFileDialogs)
	{
		window.LHED_Img_SetUrl = function(filename, path, site)
		{
			var url, srcInput = document.getElementById("lhed_img_src");

			if (typeof filename == 'object') // Using medialibrary
			{
				url = filename.src;
				document.getElementById("lhed_img_title").value = filename.name;
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
		};
	}

	return {
		title: LHE_MESS.ImageProps,
		innerHTML : str,
		width: '430px',
		OnLoad: function()
		{
			pObj.bNew = !pObj.pSel || pObj.pSel.tagName.toUpperCase() != 'IMG';
			pObj.bSaveProp = true;
			pObj.iRatio = 1;

			pObj.pSrc = document.getElementById("lhed_img_src");
			pObj.pSrc.focus();
			pObj.pPreview = document.getElementById("lhed_img_preview");
			if (!pObj.pLEditor.bBBCode)
			{
				pObj.pTitle = document.getElementById("lhed_img_title");
				pObj.pAlign = document.getElementById("lhed_img_align");
				pObj.pWidth = document.getElementById("lhed_img_width");
				pObj.pHeight = document.getElementById("lhed_img_height");
				pObj.pSaveProp = document.getElementById("lhed_img_save_prop");
				pObj.bSetInStyles = false;
				pObj.pSaveProp.onclick = function()
				{
					pObj.bSaveProp = this.checked ? true : false;
					if (pObj.bSaveProp)
						pObj.pWidth.onchange();
				};
				pObj.pWidth.onchange = function()
				{
					var w = parseInt(this.value);
					if (isNaN(w)) return;
					pObj.curWidth = pObj.pWidth.value = w;
					if (pObj.bSaveProp)
					{
						var h = Math.round(w / pObj.iRatio);
						pObj.curHeight = pObj.pHeight.value = h;
					}
					PreviewReload();
				};
				pObj.pHeight.onchange = function()
				{
					var h = parseInt(this.value);
					if (isNaN(h)) return;
					pObj.curHeight = pObj.pHeight.value = h;
					if (pObj.bSaveProp)
					{
						var w = parseInt(h * pObj.iRatio);
						pObj.curWidth = pObj.pWidth.value = w;
					}
					PreviewReload();
				};
				pObj.pAlign.onchange = pObj.pTitle.onchange = PreviewReload;
			}
			pObj.pSrc.onchange = PreviewReload;
			pObj.pPreview.onload = PreviewOnLoad;

			if (!pObj.bNew) // Select Img
			{
				pObj.pSrc.value = pObj.pSel.getAttribute('__bxsrc');
				if (!pObj.pLEditor.bBBCode)
				{
					pObj.pPreview.onload = function(){pObj.pPreview.onload = PreviewOnLoad;};
					if (pObj.pSel.style.width || pObj.pSel.style.height)
						pObj.bSetInStyles = true;
					pObj.bSetInStyles = false;

					var w = parseInt(pObj.pSel.style.width || pObj.pSel.getAttribute('width') || pObj.pSel.offsetWidth);
					var h = parseInt(pObj.pSel.style.height || pObj.pSel.getAttribute('height') || pObj.pSel.offsetHeight);
					if (w && h)
					{
						pObj.iRatio = w / h; // Remember proportion
						pObj.curWidth = pObj.pWidth.value = w;
						pObj.curHeight = pObj.pHeight.value = h;
					}
					pObj.pTitle.value = pObj.pSel.getAttribute('title') || '';
					pObj.pAlign.value = pObj.pSel.getAttribute('align') || '';
				}
				PreviewReload();
			}


			document.getElementById("lhe_dialog_save").onclick = OnSave;
			document.getElementById("lhe_dialog_cancel").onclick = OnClose;
		}
	};
}

window.LHEDailogs['Video'] = function(pObj)
{
	var strPath = pObj.pLEditor.arConfig.bUseFileDialogs ? '<input type="text" size="30" value="" id="lhed_video_path"><input type="button" value="..." style="width: 20px;" onclick="window.LHED_VideoPath_FDOpen();">' : '<input type="text" size="33" value="" id="lhed_video_path">';
	var strPreview = pObj.pLEditor.arConfig.bUseFileDialogs ? '<input type="text" size="30" value="" id="lhed_video_prev_path"><input type="button" value="..." style="width: 20px;" onclick="window.LHED_VideoPreview_FDOpen();">' : '<input type="text" size="33" value="" id="lhed_video_prev_path">';

	var sText = '', i;
	for (i = 0; i < 200; i++){sText += 'text ';}

	var str = '<table width="100%">' +
	'<tr>' +
		'<td class="lhe-dialog-label lhe-label-imp">' + LHE_MESS.VideoPath + ':</td>' +
		'<td class="lhe-dialog-param">' + strPath + '</td>' +
	'</tr>';
	if (!pObj.pLEditor.arConfig.bBBCode)
	{
		str +=
	'<tr>' +
		'<td class="lhe-dialog-label">' + LHE_MESS.VideoPreviewPath + ':</td>' +
		'<td class="lhe-dialog-param">' + strPreview + '</td>' +
	'</tr>';
	}
	str +=
	'<tr>' +
		'<td class="lhe-dialog-label lhe-label-imp">' + LHE_MESS.ImageSizing + ':</td>' +
		'<td class="lhe-dialog-param">' +
		'<div class="lhed-img-size-cont"><input type="text" size="4" value="" id="lhed_video_width"> x <input type="text" size="4" value="" id="lhed_video_height"></div></td>' +
	'</tr>';
	if (!pObj.pLEditor.arConfig.bBBCode)
	{
		str +=
	'<tr>' +
		'<td class="lhe-dialog-label"></td>' +
		'<td class="lhe-dialog-param"><input type="checkbox" id="lhed_video_autoplay"><label for="lhed_video_autoplay">' + LHE_MESS.VideoAutoplay + '</label></td>' +
	'</tr>' +
	'<tr>' +
		'<td class="lhe-dialog-label">' + LHE_MESS.VideoVolume + ':</td>' +
		'<td class="lhe-dialog-param">' +
			'<select id="lhed_video_volume">' +
				'<option value="10">10</option><option value="20">20</option>' +
				'<option value="30">30</option><option value="40">40</option>' +
				'<option value="50">50</option><option value="60">60</option>' +
				'<option value="70">70</option><option value="80">80</option>' +
				'<option value="90" selected="selected">90</option><option value="100">100</option>' +
			'</select> %' +
		'</td>' +
	'</tr>';
	}

	str +=
	'<tr>' +
		'<td colSpan="2" class="lhe-dialog-param"><span class="lhed-vid-preview-label">' + LHE_MESS.ImagePreview + ':</span>' +
			'<div class="lhed-vid-preview-cont">' +
				'<div id="lhe_dialog_preview_flv" style="display: none;">WMV</div>' +
				'<div id="lhe_dialog_preview_wmv" style="display: none;"></div>' +
			'</div>' +
		'</td>' +
	'</tr>' +
	'<tr valign="top">' +
		'<td class="lhe-dialog-but-cell" colSpan="2"><input type="button" id="lhe_dialog_save" value="' + LHE_MESS.DialogSave + '"> <input type="button" id="lhe_dialog_cancel" value="' + LHE_MESS.DialogCancel + '"></td>' +
	'</tr></table>';
	var OnClose = function() {pObj.Close();};
	var OnSave = function()
	{
		var
			path = pObj.pPath.value,
			w = parseInt(pObj.pWidth.value) || 240,
			h = parseInt(pObj.pHeight.value) || 180,
			pVid, ext,
			arVidConf = pObj.pLEditor.arConfig.videoSettings;

		if (path.length  < 1) // Need for showing error
			return;

		pObj.pLEditor.SelectRange(pObj.pLEditor.oPrevRange);
		if (pObj.pSel)
		{
			pVid = pObj.pSel;
		}
		else
		{
			pObj.videoId = "bx_video_"+Math.round(Math.random() * 100000);
			pObj.pLEditor.InsertHTML('<img id="' + pObj.videoId + '" src="' + pObj.pLEditor.oneGifSrc + '" style="background: #E2DFDA center center no-repeat;" __bxtagname="video" />');
			//pObj.pLEditor.InsertHTML('<img id="' + pObj.videoId + '" src="' + pObj.pLEditor.oneGifSrc + '" __bxtagname="video" />');
			pVid = pObj.pLEditor.pEditorDocument.getElementById(pObj.videoId);
		}

		if (arVidConf.maxWidth && w && parseInt(w) > parseInt(arVidConf.maxWidth))
			w = arVidConf.maxWidth;
		if (arVidConf.maxHeight && h && parseInt(h) > parseInt(arVidConf.maxHeight))
			h = arVidConf.maxHeight;

		var oVideo = {width: w, height: h};
		if (path.indexOf('http://') != -1 || path.indexOf('.') != -1)
		{
			ext = (path.indexOf('.') != -1) ? path.substr(path.lastIndexOf('.') + 1).toLowerCase() : false;
			if (ext && (ext == 'wmv' || ext == 'wma')) // WMV
			{
				oVideo.file = path;
				if (!pObj.pLEditor.bBBCode)
				{
					oVideo.image = pObj.pPrevPath.value || '';
					oVideo.volume = pObj.pVolume.value;
					oVideo.autostart = pObj.pAutoplay.checked ? true : false;
				}
			}
			else
			{
				oVideo.flashvars= {file: path};
				if (!pObj.pLEditor.bBBCode)
				{
					oVideo.flashvars.image = pObj.pPrevPath.value || '';
					oVideo.flashvars.volume = pObj.pVolume.value;
					oVideo.flashvars.autostart = pObj.pAutoplay.checked ? true : false;
				}
			}

			pObj.pLEditor.arVideos[pObj.videoId] = oVideo;
			pVid.style.width = w + 'px';
			pVid.style.height = h + 'px';
			pVid.style.backgroundImage = (pObj.pPrevPath.value.length > 0) ? 'url(' + pObj.pPrevPath.value + ')' : 'url(/bitrix/images/fileman/light_htmledit/video.gif)';
		}
		else
		{
			pObj.pLEditor.arVideos[pObj.videoId] = null;
			pObj.pLEditor.InsertHTML('');
		}
		pObj.Close();
	};

	var PreviewReload = function()
	{
		setTimeout(function(){
		var path = pObj.pPath.value;
		var bHide = false;

		if (path.indexOf('http://') != -1 || path.indexOf('.') != -1)
		{
			var ext = (path.indexOf('.') != -1) ? path.substr(path.lastIndexOf('.') + 1).toLowerCase() : false;
			if (ext && (ext == 'wmv' || ext == 'wma')) // WMV
			{
				pObj.pPreviewDivWMV.style.display = 'block';
				pObj.pPreviewDivFLV.style.display = 'none';
				var f1 = function()
				{
					try{
						new jeroenwijering.Player(document.getElementById("lhe_dialog_preview_wmv"), "/bitrix/components/bitrix/player/wmvplayer/wmvplayer.xaml", {file: path, width:"320", height:"240", windowless: true});
						pObj.pPreviewDivWMV.style.display = 'block';
						pObj.pPreviewDivFLV.style.display = 'none';
					}catch(e){}
				};
				if (!window.jeroenwijering)
					jsUtils.loadJSFile(['/bitrix/components/bitrix/player/wmvplayer/silverlight.js', '/bitrix/components/bitrix/player/wmvplayer/wmvplayer.js'], f1);
				else
					f1();
			}
			else
			{
				try{
				var pEmb = jsUtils.CreateElement('EMBED', {src: "/bitrix/components/bitrix/player/mediaplayer/player.swf", type:"application/x-shockwave-flash", width:"320", height: "240", allowscriptaccess: "always", allowfullscreen: "true", flashvars:'file=' + path + '&skin=/bitrix/components/bitrix/player/mediaplayer/skins/bitrix.swf&bufferlength=10'});

				pObj.pPreviewDivFLV.removeChild(pObj.pPreviewDivFLV.firstChild);
				pObj.pPreviewDivFLV.appendChild(pEmb);

				pObj.pPreviewDivFLV.style.display = 'block';
				pObj.pPreviewDivWMV.style.display = 'none';
				}catch(e){}
			}
		}
		else
		{
			pObj.pPreviewDivFLV.innerHTML = '';
			pObj.pPreviewDivFLV.style.display = 'none';
			pObj.pPreviewDivWMV.style.display = 'none';
			return;
		}
		}, 100);
	};

	window['lhe_hide_preview_' + pObj.pLEditor.id] = function()
	{
		pObj.pPreviewDivFLV.style.display = 'none';
		pObj.pPreviewDivWMV.style.display = 'none';
	}

	window.LHED_Video_SetPath = function(filename, path, site)
	{
		var
			url = path + '/' + filename,
			inp = document.getElementById("lhed_video_path");
		inp.value = url;
		if(inp.onchange)
			inp.onchange();
		inp.focus();
		inp.select();
	};

	return {
		title: LHE_MESS.VideoProps,
		innerHTML : str,
		width: '500px',
		OnLoad: function()
		{
			pObj.pPreviewDivFLV = document.getElementById("lhe_dialog_preview_flv");
			pObj.pPreviewDivWMV = document.getElementById("lhe_dialog_preview_wmv");

			pObj.bNew = !pObj.pSel || !pObj.pSel.getAttribute || pObj.pSel.getAttribute('__bxtagname') != 'video';
			pObj.pPath = document.getElementById("lhed_video_path");
			pObj.pPath.focus();
			pObj.pPath.onchange = PreviewReload;
			pObj.pWidth = document.getElementById("lhed_video_width");
			pObj.pHeight = document.getElementById("lhed_video_height");

			if (!pObj.pLEditor.bBBCode)
			{
				pObj.pPrevPath = document.getElementById("lhed_video_prev_path");
				pObj.pVolume = document.getElementById("lhed_video_volume");
				pObj.pAutoplay = document.getElementById("lhed_video_autoplay");
			}

			document.getElementById("lhe_dialog_save").onclick = OnSave;
			document.getElementById("lhe_dialog_cancel").onclick = OnClose;

			if (!pObj.bNew)
			{
				pObj.videoId = pObj.pSel.getAttribute('id');
				pObj.arParams = pObj.pLEditor.arVideos[pObj.videoId];
				if (!pObj.arParams)
				{
					pObj.bNew = true;
					return;
				}

				var path, prPath, vol, w, h, autoplay;
				if (pObj.arParams.flashvars) //FLV
				{
					path = pObj.arParams.flashvars.file;
					w = pObj.arParams.width || '';
					h = pObj.arParams.height || '';
					prPath = pObj.arParams.flashvars.image || '';
					vol = pObj.arParams.flashvars.volume || '90';
					autoplay = pObj.arParams.flashvars.autostart || false;
				}
				else
				{
					path = pObj.arParams.file;
					w = pObj.arParams.width || '';
					h = pObj.arParams.height || '';
					prPath = pObj.arParams.image || '';
					vol = pObj.arParams.volume || '90';
					autoplay = pObj.arParams.autostart || false;
				}
				pObj.pPath.value = path;
				pObj.pWidth.value = w;
				pObj.pHeight.value = h;

				if (!pObj.pLEditor.bBBCode)
				{
					pObj.pPrevPath.value = prPath;
					pObj.pVolume.value = vol;
					pObj.pAutoplay.checked = autoplay ? true : false;
				}
				PreviewReload();
			}

			if (pObj.pLEditor.arConfig.bUseFileDialogs)
			{
				//var pFDButton = pObj.pPath.nextSibling;
				//pFDButton.onclick = function(){}
			}
		}
	};
}
