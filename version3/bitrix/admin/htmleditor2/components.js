var components_js = true;

// ######################################
// #######    BXComponentsTaskbar      ##############
// ######################################
function BXComponentsTaskbar()
{
	ar_BXComponentsTaskbarS.push(this);

	var obj = this;
	var oTaskbar = this;
	BXComponentsTaskbar.prototype.OnTaskbarCreate = function ()
	{
		var obj = this;
		this.icon_class = 'tb_icon_components1';
		this.iconDiv.className = 'tb_icon ' + this.icon_class;
		oTaskbar.pDataCell = oTaskbar.CreateScrollableArea(oTaskbar.pDataCell);
		var table = oTaskbar.pMainObj.CreateElement("TABLE", {cellSpacing: '0', cellPadding: '0', className: "bxtaskbarcomp"});

		table.style.height = "100%";
		this.pDataCell.appendChild(table);
		this.pWnd.style.width = "200px";
		this.pWnd.style.width = "100%";
		this.pCellList = table.insertRow(-1).insertCell(-1);
		this.pCellList.style.padding = "10px 0px 10px 10px";
		this.pModulesList = this.pMainObj.CreateCustomElement('BXList',
			{
				'width': '150',
				'height': '150',
				'field_size': '150',
				'bSetGlobalStyles': true,
				'values': [],
				'OnChange': function (selected)
				{
					if(selected)
						obj.__ShowComponentList(selected["value"]);
				}
			}
		);
		this.pCellList.appendChild(this.pModulesList.pWnd);

		this.pCellComp = table.insertRow(-1).insertCell(-1);
		this.pCellComp.style.height = "100%";
		this.pCellComp.className = "bx_valign_top";

		this.pMainObj.pComponent1Taskbar = this;
		this.FetchArray();
		
		emptyRow = null;
		table = null;
	};
	
	
	BXComponentsTaskbar.prototype.FetchArray = function (clear_cache)
	{
		if (clear_cache === true || !window.arComp1Elements)
		{
			var templateID = this.pMainObj.templateID;
			var
				_this = this,
				q = new JCHttpRequest();

			q.Action = function(result)
			{
				setTimeout(function()
				{
					if (window.arComp1Elements)
						_this.BuildList();
				}, 100);
			};
			q.Send(editor_action_path + '&action=getcomponents1&lang=' + BXLang + '&site=' + BXSite + '&templateID=' + templateID);
		}
		else
		{
			this.BuildList();
		}
	};
	

	BXComponentsTaskbar.prototype.BuildList = function ()
	{
		var arList = [];
		var arFolders = window.arComp1Elements["FOLDERS"];

		for(var i=0; i<arFolders.length; i++)
			arList.push({'value': arFolders[i]["ID"], 'name': arFolders[i]["NAME"]});

		this.pModulesList.SetValues(arList);
		this.pModulesList.Select(0);
		this.pModulesList.FireChangeEvent();

		arList = null;
		arFolders = null;
	};
	
		
	BXComponentsTaskbar.prototype.__ShowComponentList = function (sFolder)
	{
		oBXEditorUtils.BXRemoveAllChild(this.pCellComp);
		if (this.rootElementsCont)
			oBXEditorUtils.BXRemoveAllChild(this.rootElementsCont);
			
		var arComponents = [];
		var _arComponents = [];
		var tempArGrElement = [];
		var tempArElement = [];
		var arAllComponents = window.arComp1Elements["COMPONENTS"];
		var el_num, gr_num = 0;

		for(var i=0; i<arAllComponents.length; i++)
		{
			if(arAllComponents[i]["FOLDER"]==sFolder)
			{
				if(arAllComponents[i]["SEPARATOR"])
				{
					tempArGrElement = [];
					tempArGrElement['name'] = sFolder+'_'+gr_num;
					tempArGrElement['title'] = (arAllComponents[i]["NAME"]) ? arAllComponents[i]["NAME"] : BX_MESS.CompTBTitle;

					tempArGrElement['tagname'] = '';
					tempArGrElement['icon'] = '';
					tempArGrElement['isGroup'] = true;
					tempArGrElement['params'] = [];
					tempArGrElement['childElements'] = [];
					_arComponents.push(tempArGrElement);
					gr_num++;
					el_num = 0;
				}
				else
				{
					tempArElement = [];
					tempArElement['name'] = sFolder+'_'+el_num;
					tempArElement['title'] = (arAllComponents[i]["NAME"]) ? arAllComponents[i]["NAME"] : 'No Name';
					tempArElement['tagname'] = 'component';
					tempArElement['icon'] = arAllComponents[i]["ICON"];
					tempArElement['isGroup'] = false;
					tempArElement['params'] = {"SCRIPT_NAME": arAllComponents[i]["PATH"], "PARAMS": {}};
					tempArElement['childElements'] = [];

					try
					{
						_arComponents[_arComponents.length-1]['childElements'].push(tempArElement);
					}
					catch(e)
					{
						tempArGrElement = [];
						tempArGrElement['name'] = sFolder+'_'+gr_num;
						tempArGrElement['title'] = BX_MESS.CompTBTitle;

						tempArGrElement['tagname'] = '';
						tempArGrElement['icon'] = '';
						tempArGrElement['isGroup'] = true;
						tempArGrElement['params'] = [];
						tempArGrElement['childElements'] = [];
						_arComponents.push(tempArGrElement);
						gr_num++;
						el_num = 0;

						_arComponents[_arComponents.length-1]['childElements'].push(tempArElement);
					}
					el_num++;
				}
			}
		}
		oTaskbar.DisplayElementList(_arComponents, this.pCellComp);

		var elPHP = [];
		elPHP['name'] = 'el_php';
		elPHP['title'] = BX_MESS.PHP_CODE;
		elPHP['tagname'] = 'php';
		elPHP['icon'] = '/bitrix/images/fileman/htmledit2/php.gif';
		elPHP['isGroup'] = false;
		elPHP['params'] = {code:"<?\n?>"};
		elPHP['childElements'] = [];		
		
		this.AddElement(elPHP,this.pCellComp,"",-1);
	};
}

oBXEditorUtils.addTaskBar('BXComponentsTaskbar', 2, BX_MESS.CompTBTitle+" 1.0", {bWithoutPHP : false});


function BXCheckForComponent(_str)
{
	if ((arRes = __CheckForComponent(_str)) && (arTemplate = obj.pMainObj.FindComponentByPath(arRes["SCRIPT_NAME"])))
	{
		if (!SETTINGS[obj.pMainObj.name].arTaskbarSettings)
			SETTINGS[obj.pMainObj.name].arTaskbarSettings = arTaskbarSettings;
	
		_str = '<img src="' + arTemplate['ICON'] + '" border="0" __bxtagname="component" __bxcontainer="' + bxhtmlspecialchars(BXSerialize(arRes)) + '" />';
	}

	return _str;
}


function __CheckForComponent(str)
{
	str = oBXEditorUtils.PHPParser.trimPHPTags(str);
	var _oFunc = oBXEditorUtils.PHPParser.parseFunction(str);
	if (!_oFunc)
		return false;
	if (_oFunc.name.toUpperCase()=='$APPLICATION->INCLUDEFILE')
	{
		var arParams = oBXEditorUtils.PHPParser.parseParameters(_oFunc.params);
		var oRes = {};
		oRes.SCRIPT_NAME = arParams[0];
		oRes.PARAMS = (arParams[1]) ? arParams[1] : {};
		
		if (arParams[2])
			oRes.ADD_PARAMS = arParams[2];

		return oRes;
	}
	else
		return false;
}

// ----------- Adding COMPONENT 1.0 Parser ----------//
oBXEditorUtils.addPHPParser(BXCheckForComponent,0);

var BXShowComponentPanel = function (bNew, pTaskbar, pElement)
{
	while(pTaskbar.pCellProps.childNodes.length>0)
		pTaskbar.pCellProps.removeChild(pTaskbar.pCellProps.childNodes[0]);
		
	pTaskbar.pHtmlElement = pElement;
	var arSettings = BXUnSerialize(pElement.getAttribute("__bxcontainer"));

	var fChange = function (e)
	{
		var arAllFields = [];
		function addel(arEls)
		{
			var el;
			for(var i=0; i<arEls.length; i++)
			{
				if(!arEls[i]["__exp"] || arEls[i]["__exp"]!="Y") continue;
				el = arEls[i];
				if(el["name"].substr(el["name"].length-2, 2) == '[]')
				{
					if(arAllFields[el["name"].substr(0, el["name"].length-2)])
						arAllFields[el["name"].substr(0, el["name"].length-2)].push(el);
					else
						arAllFields[el["name"].substr(0, el["name"].length-2)] = Array(el);
				}
				else
					arAllFields[el["name"]] = el;
			}
		}

		arSettings["PARAMS"] = {};
		var propID, i, j, val;
		addel(pTaskbar.pCellProps.getElementsByTagName("select"));
		addel(pTaskbar.pCellProps.getElementsByTagName("input"));
		addel(pTaskbar.pCellProps.getElementsByTagName("textarea"));

		for(i=0; i<pTaskbar.arElements.length; i++)
		{
			propID = pTaskbar.arElements[i];
			val = arAllFields[propID];

			if(arAllFields[propID+'_alt'] && val.selectedIndex == 0)
				val = arAllFields[propID+'_alt'];

			if(!val) continue;
			if(val.tagName) // one element
			{
				if(val.tagName.toUpperCase() == "SELECT")
				{
					for(j=0; j<val.length; j++)
					{
						if(val[j].selected && val[j].value!='')
							arSettings["PARAMS"][propID] = val[j].value;
					}
				}
				else
					arSettings["PARAMS"][propID] = val.value;
			}
			else
			{
				arSettings["PARAMS"][propID] = [];
				for(k=0; k<val.length; k++)
				{
					if(val[k].tagName.toUpperCase() == "SELECT")
					{
						for(j=0; j<val[k].length; j++)
						{
							if(val[k][j].selected && val[k][j].value!='')
								arSettings["PARAMS"][propID].push(val[k][j].value);
						}
					}
					else
						arSettings["PARAMS"][propID].push(val[k].value);
				}
			}
		}
		if(pElement)
			pElement.setAttribute("__bxcontainer", BXSerialize(arSettings));
	} // end of fChange

	pTaskbar.arElements = [];

	var templateID = pTaskbar.pMainObj.templateID;

	var tProp = pTaskbar.pMainObj.CreateElement("TABLE");
	tProp.className = "bxtaskbarprops";
	tProp.style.width = "100%";
	tProp.cellSpacing = 0;
	tProp.cellPadding = 1;
	var row, cell, arPropertyParams, bSel, arValues, res, pSelect, arUsedValues, bFound, key, oOption, val, xCell, opt_val, bBr, i, k, alt;

	var
		_this = this,
		q = new JCHttpRequest();

	q.Action = function(result) {setTimeout(function()
	{
		var arParams = window.bx_componentconfig || {};

		for(var propertyID in arParams)
		{
			pTaskbar.arElements.push(propertyID);
			res = '';
			arUsedValues = [];
			arPropertyParams = arParams[propertyID];
			if(!arSettings["PARAMS"][propertyID] && arPropertyParams["DEFAULT"])
				arValues = arPropertyParams["DEFAULT"];
			else if(arSettings["PARAMS"][propertyID])
				arValues = arSettings["PARAMS"][propertyID];
			else
				arValues = '';

			if(!arPropertyParams["MULTIPLE"] || arPropertyParams["MULTIPLE"]!="Y")
				arPropertyParams["MULTIPLE"] = "N";
			if(!arPropertyParams["TYPE"])
				arPropertyParams["TYPE"] = "STRING";
			if(!arPropertyParams["CNT"])
				arPropertyParams["CNT"] = 0;
			if(!arPropertyParams["SIZE"])
				arPropertyParams["SIZE"] = 0;
			if(!arPropertyParams['ADDITIONAL_VALUES'])
				arPropertyParams['ADDITIONAL_VALUES'] = 'N';
			if(!arPropertyParams['ROWS'])
				arPropertyParams['ROWS'] = 0;
			if(!arPropertyParams["COLS"] || arPropertyParams["COLS"]<1)
				arPropertyParams["COLS"] = '30';

			if(arPropertyParams["MULTIPLE"] && arPropertyParams["MULTIPLE"]=='Y' && typeof(arValues)!='object')
			{
				if(!arValues)
					arValues = [];
			}
			else if(arPropertyParams["TYPE"]&& arPropertyParams["TYPE"]=="LIST" && typeof(arValues)!='object')
				arValues = Array(arValues);

			if(arPropertyParams["MULTIPLE"] && arPropertyParams["MULTIPLE"]=='Y')
			{
				arPropertyParams["CNT"] = parseInt(arPropertyParams["CNT"]);
				if(arPropertyParams["CNT"]<1)
					arPropertyParams["CNT"] = 1;
			}

			row = tProp.insertRow(-1);
			row.className = "bxtaskbarpropscomp";
			cell = row.insertCell(-1);
			cell.width = "50%";
			cell.align = "right";
			cell.vAlign = "top";
			var oSpan = pTaskbar.pMainObj.CreateElement("SPAN", {'innerHTML': arPropertyParams['NAME']+':'});
			cell.appendChild(oSpan);
			oSpan = null;
			cell = row.insertCell(-1);
			cell.width = "50%";

			arPropertyParams["TYPE"] = arPropertyParams["TYPE"].toUpperCase();
			switch(arPropertyParams["TYPE"])
			{
			case "LIST":
				arPropertyParams["SIZE"] = (arPropertyParams["MULTIPLE"]=='Y' && (parseInt(arPropertyParams["SIZE"])<=1 || isNaN(parseInt(arPropertyParams["SIZE"]))) ? '3' : arPropertyParams["SIZE"]);
				if(parseInt(arPropertyParams["SIZE"])<=0 || isNaN(parseInt(arPropertyParams["SIZE"])))
					arPropertyParams["SIZE"] = 1;

				pSelect = pTaskbar.pMainObj.CreateElement("SELECT", {'size': arPropertyParams["SIZE"], 'name': propertyID+(arPropertyParams["MULTIPLE"]=='Y'?'[]':''), '__exp': 'Y', 'onchange': fChange, "multiple":(arPropertyParams["MULTIPLE"]=="Y")});
				cell.appendChild(pSelect);

				if(!arPropertyParams["VALUES"])
					arPropertyParams["VALUES"] = [];

				bFound = false;
				for(opt_val in arPropertyParams["VALUES"])
				{
					bSel = false;
					oOption = new Option(arPropertyParams["VALUES"][opt_val], opt_val, false, false);
					pSelect.options.add(oOption);
					if(pSelect.options.length<=1)
						setTimeout(__BXSetOptionSelected(oOption, false), 1);
					
					var _arValues = [];
					if (typeof arValues == 'string')
					{
						if (arValues.substr(0, 2) == '={')
							arValues = arValues.substr(2, arValues.length-3);
							
						if (arValues.substr(0, 6).toLowerCase()=='array(')
							_arValues = _BXStr2Arr(arValues);
					}
					
					key = BXSearchInd(arValues, opt_val);
					
					if(key>=0)
					{
						bFound = true;
						arUsedValues[key]=true;
						bSel = true;
						setTimeout(__BXSetOptionSelected(oOption, true), 1);
					}
					else if(_arValues[opt_val])
					{
						bFound = true;
						arUsedValues[opt_val]=true;
						bSel = true;
						setTimeout(__BXSetOptionSelected(oOption, true), 1);
						delete _arValues[opt_val];							
					}
				}


				if(arPropertyParams['ADDITIONAL_VALUES']!='N')
				{	
					oOption = document.createElement("OPTION");
					oOption.value = '';
					oOption.selected = !bFound;
					oOption.text = (arPropertyParams['MULTIPLE']=='Y'?BX_MESS.TPropCompNS:BX_MESS.TPropCompOth)+' ->';
					pSelect.options.add(oOption, 0);
					oOption = null;
				}

				if(arPropertyParams['ADDITIONAL_VALUES']!='N')
				{
					if(arPropertyParams['MULTIPLE']=='Y')
					{
						if (typeof(arValues)=='string')
							arValues = _arValues;
					
						for(k in arValues)
						{
							if(arUsedValues[k])
								continue;
							cell.appendChild(pTaskbar.pMainObj.CreateElement("BR"));
							if(arPropertyParams['ROWS']>1)
							{
								var oTextarea = pTaskbar.pMainObj.CreateElement("TEXTAREA", {'cols': (isNaN(arPropertyParams['COLS'])?'20':arPropertyParams['COLS']), 'value': arValues[k], 'name': propertyID+'[]', '__exp': 'Y', 'onchange': fChange});
								cell.appendChild(oTextarea);
								oTextarea = null;
							}
							else
							{
								var oInput = pTaskbar.pMainObj.CreateElement("INPUT", {'type': 'text', 'size': (isNaN(arPropertyParams['COLS'])?'20':arPropertyParams['COLS']), 'value': arValues[k], 'name': propertyID+'[]', '__exp': 'Y', 'onchange': fChange});
								cell.appendChild(oInput);
								oInput = null;
							}
						}

						for(k=0; k<arPropertyParams["CNT"]; k++)
						{
							cell.appendChild(pTaskbar.pMainObj.CreateElement("BR"));
							if(arPropertyParams['ROWS']>1)
							{
								var oTextarea = pTaskbar.pMainObj.CreateElement("TEXTAREA", {'cols': (isNaN(arPropertyParams['COLS'])?'20':arPropertyParams['COLS']), 'value': '', 'name': propertyID+'[]', '__exp': 'Y', 'onchange': fChange});
								cell.appendChild(oTextarea);
								oTextarea = null;
							}
							else
							{
								var oInput = pTaskbar.pMainObj.CreateElement("INPUT", {'type': 'text', 'size': (isNaN(arPropertyParams['COLS'])?'20':arPropertyParams['COLS']), 'value': '', 'name': propertyID+'[]', '__exp': 'Y', 'onchange': fChange});
								cell.appendChild(oInput);
								oInput = null;
							}
						}
						
						var oInput = pTaskbar.pMainObj.CreateElement("INPUT", {'type': 'button', 'value': '+', 'pMainObj': pTaskbar.pMainObj,  'arPropertyParams': arPropertyParams});
						xCell = cell.appendChild(oInput);
						oInput = null;
						var oBR = pTaskbar.pMainObj.CreateElement("BR");
						cell.appendChild(oBR);
						oBR = null;
						xCell.propertyID = propertyID;
						xCell.fChange = fChange;
						xCell.onclick = function ()
						{
							this.parentNode.insertBefore(this.pMainObj.CreateElement("BR"), this);
							if(this.arPropertyParams['ROWS'] && this.arPropertyParams['ROWS']>1)
							{
								var oTextarea = this.pMainObj.CreateElement("TEXTAREA", {'cols': (!this.arPropertyParams['COLS'] || isNaN(this.arPropertyParams['COLS'])?'20':this.arPropertyParams['COLS']), 'value': '', 'name': this.propertyID+'[]', '__exp': 'Y', 'onchange': this.fChange});
								this.parentNode.insertBefore(oTextarea, this);
								oTextarea = null;
							}
							else
							{
								var oInput = this.pMainObj.CreateElement("INPUT", {'type': 'text', 'size': (!this.arPropertyParams['COLS'] || isNaN(this.arPropertyParams['COLS'])?'20':this.arPropertyParams['COLS']), 'value': '', 'name': this.propertyID+'[]', '__exp': 'Y', 'onchange': this.fChange});
								this.parentNode.insertBefore(oInput, this);
								oInput = null;
							}
						}
					}
					else
					{
						val = '';
						for(k=0; k<arValues.length; k++)
						{
							if(arUsedValues[k])
								continue;
							val = arValues[k];
							break;
						}

						if(arPropertyParams['ROWS'] && arPropertyParams['ROWS']>1)
							alt = cell.appendChild(pTaskbar.pMainObj.CreateElement("TEXTAREA", {'cols': (!arPropertyParams['COLS'] || isNaN(arPropertyParams['COLS'])?'20':arPropertyParams['COLS']), 'value': val, 'disabled': bFound, 'name': propertyID+'_alt', '__exp': 'Y', 'onchange': fChange}));
						else
							alt = cell.appendChild(pTaskbar.pMainObj.CreateElement("INPUT", {'type': 'text', 'size': (!arPropertyParams['COLS'] || isNaN(arPropertyParams['COLS'])?'20':arPropertyParams['COLS']), 'value': val, 'disabled': bFound, 'name': propertyID+'_alt', '__exp': 'Y', 'onchange': fChange}));

						pSelect.pAlt = alt;
						pSelect.onchange = function (e){this.pAlt.disabled = (this.selectedIndex!=0); fChange(e);};
					}
				}

				break;
			default:
				if(arPropertyParams["MULTIPLE"]=='Y')
				{
					bBr = false;
					for(val in arValues)
					{
						if(bBr)
							cell.appendChild(pTaskbar.pMainObj.CreateElement("BR"));
						else
							bBr = true;

						if(arPropertyParams['ROWS']>1)
							cell.appendChild(pTaskbar.pMainObj.CreateElement("TEXTAREA", {'cols': (isNaN(arPropertyParams['COLS'])?'20':arPropertyParams['COLS']), 'value': val, 'name': propertyID+'[]', '__exp': 'Y', 'onchange': fChange}));
						else
							cell.appendChild(pTaskbar.pMainObj.CreateElement("INPUT", {'type': 'text', 'size': (isNaN(arPropertyParams['COLS'])?'20':arPropertyParams['COLS']), 'value': val, 'name': propertyID+'[]', '__exp': 'Y', 'onchange': fChange}));
					}

					for(k=0; k<arPropertyParams["CNT"]; k++)
					{
						if(bBr)
							cell.appendChild(pTaskbar.pMainObj.CreateElement("BR"));
						else
							bBr = true;

						if(arPropertyParams['ROWS']>1)
							cell.appendChild(pTaskbar.pMainObj.CreateElement("TEXTAREA", {'cols': (isNaN(arPropertyParams['COLS'])?'20':arPropertyParams['COLS']), 'value': '', 'name': propertyID+'[]', '__exp': 'Y', 'onchange': fChange}));
						else
							cell.appendChild(pTaskbar.pMainObj.CreateElement("INPUT", {'type': 'text', 'size': (isNaN(arPropertyParams['COLS'])?'20':arPropertyParams['COLS']), 'value': '', 'name': propertyID+'[]', '__exp': 'Y', 'onchange': fChange}));
					}

					xCell = cell.appendChild(pTaskbar.pMainObj.CreateElement("INPUT", {'type': 'button', 'value': '+', 'pMainObj': pTaskbar.pMainObj,  'arPropertyParams': arPropertyParams}));
					xCell.propertyID = propertyID;
					xCell.fChange = fChange;
					xCell.onclick = function ()
					{
						this.parentNode.insertBefore(this.pMainObj.CreateElement("BR"), this);
						if(this.arPropertyParams['ROWS'] && this.arPropertyParams['ROWS']>1)
							this.parentNode.insertBefore(this.pMainObj.CreateElement("TEXTAREA", {'cols': (!this.arPropertyParams['COLS'] || isNaN(this.arPropertyParams['COLS'])?'20':this.arPropertyParams['COLS']), 'value': '', 'name': this.propertyID+'[]', '__exp': 'Y', 'onchange': this.fChange}), this);
						else
							this.parentNode.insertBefore(this.pMainObj.CreateElement("INPUT", {'type': 'text', 'size': (!this.arPropertyParams['COLS'] || isNaN(this.arPropertyParams['COLS'])?'20':this.arPropertyParams['COLS']), 'value': '', 'name': this.propertyID+'[]', '__exp': 'Y', 'onchange': this.fChange}), this);
					}
					cell.appendChild(pTaskbar.pMainObj.CreateElement("BR"));
				}
				else
				{
					val = arValues;

					if(arPropertyParams['ROWS'] && arPropertyParams['ROWS']>1)
						cell.appendChild(pTaskbar.pMainObj.CreateElement("TEXTAREA", {'cols': (!arPropertyParams['COLS'] || isNaN(arPropertyParams['COLS'])?'20':arPropertyParams['COLS']), 'value': val, 'name': propertyID, '__exp': 'Y', 'onchange': fChange}));
					else
						cell.appendChild(pTaskbar.pMainObj.CreateElement("INPUT", {'type': 'text', 'size': (!arPropertyParams['COLS'] || isNaN(arPropertyParams['COLS'])?'20':arPropertyParams['COLS']), 'value': val, 'name': propertyID, '__exp': 'Y', 'onchange': fChange}));
				}
				break;
			}

			if(arPropertyParams["REFRESH"] && arPropertyParams["REFRESH"]=="Y")
			{
				xCell = cell.appendChild(pTaskbar.pMainObj.CreateElement("INPUT", {'type': 'button', 'value': 'ok', 'pMainObj': pTaskbar.pMainObj,  'arPropertyParams': arPropertyParams}));
				xCell.onclick = function (){BXShowComponentPanel(bNew, pTaskbar, pElement);};
			}
		}

		var arTemplate;
		if(tProp.rows.length>0 && (arTemplate = pTaskbar.pMainObj.FindComponentByPath(arSettings["SCRIPT_NAME"])))
		{
			cell = tProp.rows[0].cells[1];
			cell.noWrap = true;
			cell.insertBefore(pTaskbar.pMainObj.CreateElement("IMG", {'src': '/bitrix/images/fileman/htmledit2/info.gif', 'title': arTemplate['FULL_PATH'], 'align': 'right', 'width': '16', 'height':'16'}), cell.childNodes[0]);
		}

		pTaskbar.pCellProps.appendChild(tProp);
		
		tProp = null;
		row = null;
		cell = null;
		arPropertyParams = null;
		pSelect = null;
		oOption = null;
		
		
	}, 100);};

	var postData = oBXEditorUtils.ConvertArray2Post(arSettings["PARAMS"], 'params');
	q.Post(editor_action_path + '&action=component1config&lang=' + BXLang + '&site=' + BXSite + '&templateID=' + templateID + '&path='+arSettings["SCRIPT_NAME"], postData);
};

pPropertybarHandlers['component'] = BXShowComponentPanel;