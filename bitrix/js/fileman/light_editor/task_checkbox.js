LHEButtons['TaskCheckbox'] = {
	id: 'TaskCheckbox',
	name: LHE_TC.butTitle,
	bBBHide: true,
	handler: function(pBut)
	{
		var oRange = pBut.pLEditor.GetSelectionRange();
		var selectedText = '', pUl = false, pLi = false;

		// Get selected text
		if (oRange.startContainer) // DOM Model
		{
			var oSel = pBut.pLEditor.GetSelectionObject();
			if (oSel && !oSel.collapsed)
				selectedText = true;
		}
		else // IE
		{
			selectedText = oRange.text || oRange.htmlText;
		}

		if (selectedText)
		{
			var p = pBut.pLEditor.GetSelectionObject();
			pLi = (p && p.nodeName.toUpperCase() == 'LI') ? p : jsUtils.FindParentObject(p, 'LI');
			pUl = jsUtils.FindParentObject(p, 'UL');
		}
		else
		{
			pBut.pLEditor.InsertHTML('<span id="bx_lhe_temp_bogus_node_0" style="visibility: hidden;">#</span>');
		}

		setTimeout(function()
		{
			var pNode = pBut.pLEditor.pEditorDocument.getElementById('bx_lhe_temp_bogus_node_0');
			if (pNode)
			{
				pLi = jsUtils.FindParentObject(pNode, "LI");
				pUl = jsUtils.FindParentObject(pNode, "UL");
				pNode.parentNode.removeChild(pNode);
			}

			if (pUl)
			{
				//Open dialog, add new point
				pBut.pLEditor.OpenDialog({id: 'TaskCheckbox', pUl: pUl, pLi: pLi});
			}
			else
			{
				if (selectedText)
				{
					var
						arUL = pBut.pLEditor.pEditorDocument.body.getElementsByTagName('UL'),
						i0, l0 = arUL.length, i1, l1;

					for (i0 = 0; i0 < l0; i0++)
						arUL[i0].setAttribute('__bx_tmp_old_ul', true);

					pBut.pLEditor.executeCommand("InsertUnorderedList");

					arUL = pBut.pLEditor.pEditorDocument.body.getElementsByTagName('UL');
					l1 = arUL.length;

					for (i1 = 0; i1 < l1; i1++)
					{
						if (!arUL[i1].getAttribute('__bx_tmp_old_ul'))
						{
							arUL[i1].className = 'bx-subtasklist';
							arUL[i1].setAttribute('__bxtagname', 'subtasklist');

							var
								arLi = arUL[i1].getElementsByTagName('LI'),
								i, l = arLi.length;

							if (arLi && l > 0)
							{
								for(i = 0; i < l; i++)
								{
									if (arLi[i])
										arLi[i].setAttribute('__bxtagname', 'subtask');
								}
							}
						}
						else
						{
							arUL[i1].removeAttribute('__bx_tmp_old_ul');
						}
					}
				}
				else
				{
					pBut.pLEditor.OpenDialog({id: 'TaskCheckbox'});
				}
			}
		},
		50
		);
	},
	parsers:
	[
		{
			name: "subtasklist",
			obj: {
				Parse: function(sName, sContent, pLEditor)
				{
					if (window.subTaskStyles && window.subTaskStyles.length > 0)
						setTimeout(function(){pLEditor.AppendCSS(window.subTaskStyles);}, 300);

					var newStr;
					var taskParse = function(str, b1, b2, b3, b4, b5, b6)
					{
						var
							id = b3,
							bChecked = b1.toLowerCase().indexOf('checked') != -1 || b4.toLowerCase().indexOf('checked') != -1,
							html = b6,
							cn = bChecked ? 'class="checked"' : '';

						newStr += '<li __bxtagname="subtask" id="' + id + '" ' + cn + '>' + html + '</li>';
						return str;
					};

					var taskContParse = function(str, b1, b2)
					{
						newStr = '';
						if (str.toLowerCase().indexOf('bx_subtask_') > 0)
						{
							b2.replace(/<input([\s\S]*?)id\s*=\s*("|\')(bx_subtask_\d*?)\2([\s\S]*?)>[\s\S]*?<label[\s\S]*?for\s*=\s*("|\')\3\5[\s\S]*?>([\s\S]*?)<\/label>/ig, taskParse);
							return newStr == '' ? '' : '<ul class="bx-subtasklist"  __bxtagname="subtasklist">' + newStr + '</ul>';
						}
						return str;
					};

					sContent = sContent.replace(/<div[\s\S]*?class\s*=\s*("|\')bx-subtask-list\1[\s\S]*?>([\s\S]*?)<\/div>/ig, taskContParse);
					return sContent;
				},
				UnParse: function(sName, pNode, pLEditor)
				{
					if (sName != 'subtasklist' || !pNode.arNodes)
						return '';

					var html = '', i, l = pNode.arNodes.length, id, label, j, l1;
					for (i = 0; i < l; i++)
					{
						el = pNode.arNodes[i];
						if (el.type == 'element' && el.arAttributes['__bxtagname'] == 'subtask' && el.text.toLowerCase() == 'li')
						{
							bChecked = (el.arAttributes['class'] == 'checked') ? ' checked' : '';
							pLEditor.subTaskIdCounter++;
							id = 'bx_subtask_' + pLEditor.subTaskIdCounter;

							el.arAttributes['__bxtagname'] = null;
							label = '';
							for (j = 0, l1 = el.arNodes.length; j < l1; j++)
								label += pLEditor._RecursiveGetHTML(el.arNodes[j]);

							html += '<input type="checkbox" ' + bChecked + ' name="' + id + '" id="' + id + '" /><label for="' + id + '">' + label + '</label><br />' + "\n";
						}
					}
					if (html.length > 0)
					{
						html = '<div class="bx-subtask-list">' + html + '</div>';
					}
					return html;
				}
			}
		},
		{
			name: "subtask",
			obj: {
				Parse: function(sName, sContent, pLEditor) {return sContent;},
				UnParse: function(sName, pNode, pLEditor)
				{
					if (jsUtils.bIsIE && sName == 'subtask' && pNode.text.toLowerCase() != 'li')
					{
						pNode.arAttributes["__bxtagname"] = null;
						delete pNode.arAttributes["__bxtagname"];
						return pLEditor._RecursiveGetHTML(pNode);
					}
					return '';
				}
			}
		}
	]
};

window.LHEDailogs['TaskCheckbox'] = function(pObj)
{
	var str = '<table width="100%">' +
	'<tr>' +
		'<td class="lhe-dialog-label lhe-label-imp"><label for="lhed_subtask_name">' + LHE_TC.subTaskLabel + ':</label></td>' +
		'<td class="lhe-dialog-param"><input type="text" size="30" value="" id="lhed_subtask_name"></td>' +
	'</tr>' +
	'<tr valign="top">' +
		'<td class="lhe-dialog-but-cell" colSpan="2"><input type="button" id="lhe_dialog_save" value="' + LHE_MESS.DialogSave + '"> <input type="button" id="lhe_dialog_cancel" value="' + LHE_MESS.DialogCancel + '"></td>' +
	'</tr></table>';

	var OnClose = function(){pObj.Close();};
	var OnSave = function()
	{
		var subTask = pObj.pName.value;
		if (pObj.arParams.pUl && pObj.arParams.pUl.getAttribute('__bxtagname') == 'subtasklist')
		{
			var newLi = jsUtils.CreateElement("LI", {}, {}, pObj.pLEditor.pEditorDocument);
			newLi.setAttribute('__bxtagname', 'subtask');
			newLi.innerHTML = subTask;

			if (pObj.arParams.pLi && pObj.arParams.pLi.nextSibling)
				pObj.arParams.pUl.insertBefore(newLi, pObj.arParams.pLi.nextSibling);
			else
				pObj.arParams.pUl.appendChild(newLi);
		}
		else
		{
			var
				arUL0 = pObj.pLEditor.pEditorDocument.body.getElementsByTagName('UL'),
				i0, l0 = arUL0.length, i1, l1;

			for (i0 = 0; i0 < l0; i0++)
				arUL0[i0].setAttribute('__bx_tmp_old_ul', true);

			pObj.pLEditor.SelectRange(pObj.pLEditor.oPrevRange);
			//pObj.pLEditor.InsertHTML(' <p>' + subTask + '<img id="bx_lhe_temp_bogus_node"  _moz_editor_bogus_node="on" src="/bitrix/images/1.gif" /></p>');
			pObj.pLEditor.InsertHTML(' <p>' + subTask + '</p>');

			pObj.pLEditor.executeCommand('InsertUnorderedList');

			var arUL = pObj.pLEditor.pEditorDocument.body.getElementsByTagName('UL');
			l1 = arUL.length;

			for (i1 = 0; i1 < l1; i1++)
			{
				if (!arUL[i1].getAttribute('__bx_tmp_old_ul'))
				{
					arUL[i1].className = 'bx-subtasklist';
					arUL[i1].setAttribute('__bxtagname', 'subtasklist');

					var
						arLi = arUL[i1].getElementsByTagName('LI'),
						i, l = arLi.length;

					if (arLi && l > 0)
					{
						for(i = 0; i < l; i++)
						{
							if (arLi[i])
								arLi[i].setAttribute('__bxtagname', 'subtask');
						}
					}
					break;
				}
				else
				{
					arUL[i1].removeAttribute('__bx_tmp_old_ul');
				}
			}

			setTimeout(function()
			{
				return;
				var pBN = pObj.pLEditor.pEditorDocument.getElementById('bx_lhe_temp_bogus_node');
				if (pBN)
				{
					var pUl = jsUtils.FindParentObject(pBN, "UL");
					if (pUl)
					{
						pUl.className = 'bx-subtasklist';
						pUl.setAttribute('__bxtagname', 'subtasklist');

						var
							arLi = pUl.getElementsByTagName('LI'),
							i, l = arLi.length;

						if (arLi && l > 0)
						{
							for(i = 0; i < l; i++)
							{
								if (arLi[i])
									arLi[i].setAttribute('__bxtagname', 'subtask');
							}
						}
					}
					pBN.parentNode.removeChild(pBN);
				}
			}, 100);
		}
		pObj.Close();
	};

	return {
		title: LHE_TC.butTitle,
		innerHTML : str,
		width: '400px',
		OnLoad: function()
		{
			pObj.pName = document.getElementById("lhed_subtask_name");
			pObj.pName.focus();
			pObj.pName.onkeydown = function(e)
			{
				if (!e)
					e = window.event;
				if(e.keyCode == 13)
				{
					OnSave();
					return jsUtils.PreventDefault(e);
				}
			};

			document.getElementById("lhe_dialog_save").onclick = OnSave;
			document.getElementById("lhe_dialog_cancel").onclick = OnClose;
		}
	};
}

window.LHETaskOnInit = function(P, evParams)
{
	var
		oLHE = evParams[0],
		id = oLHE.id;

	if (id != 'LHETaskId')
		return;

	oLHE.TaskInitEvents();
	jsUtils.addCustomEvent('LHE_OnParseContent', oLHE.TaskOnParse_, {}, oLHE);
	jsUtils.addCustomEvent('LHE_OnUnParseContent', oLHE.TaskOnUnParse_, {}, oLHE);
};

JCLightHTMLEditor.prototype.TaskMouseDown_ = function(e)
{
	if (!e)
		e = window.event;

	var targ;
	if (e.target)
		targ = e.target;
	else if (e.srcElement)
		targ = e.srcElement;
	if (targ.nodeType == 3) // defeat Safari bug
		targ = targ.parentNode;

	if (targ)
	{
		var pLi = (targ && targ.nodeName.toUpperCase() == 'LI') ? targ : jsUtils.FindParentObject(targ, 'LI');
		if (pLi && (pLi.getAttribute('__bxtagname') == 'subtask' || pLi.parentNode.getAttribute('__bxtagname') == 'subtasklist'))
			pLi.className = pLi.className != 'checked' ? 'checked' : '';
	}
}

JCLightHTMLEditor.prototype.TaskInitEvents = function()
{
	// Init events
	var _this = this;
	window['lhe_task_mousedown_' + this.id] = function(e) {_this.TaskMouseDown_(e);};
	jsUtils.addEvent(this.pEditorDocument, 'mousedown', window['lhe_task_mousedown_' + this.id]);
}

JCLightHTMLEditor.prototype.TaskOnParse_ = function()
{
	var _this = this;
	setTimeout(function(){_this.TaskInitEvents();}, 100);
}

JCLightHTMLEditor.prototype.TaskOnUnParse_ = function()
{
	this.subTaskIdCounter = 0;
}

jsUtils.addCustomEvent('LHE_OnInit', LHETaskOnInit);