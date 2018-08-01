/**********************************************************************/
/*********** Bitrix JS Core library ver 0.9.0 beta ********************/
/**********************************************************************/

(function(window){

var BX = function(node)
{
	if (BX.type.isNotEmptyString(node))
		return document.getElementById(node);
	else if (BX.type.isDomNode(node))
		return node;
	else if (BX.type.isFunction(node))
		return BX.ready(node);
	
	return null;
},

/* language messages */
MESS = {},

/* ready */
__readyHandler = null,
readyBound = false,
readyList = [],

/* list of registered proxy functions */
proxyId = 1,
proxyList = [],

/* List of denied event handlers */
deniedEvents = [],

/* list of registered event handlers */
eventsList = [],

/* list of registered custom events */
customEvents = {},

/* list of external garbage collectors */
garbageCollectors = [],

/* browser detection */
bOpera = navigator.userAgent.toLowerCase().indexOf('opera') != -1,
bSafari = navigator.userAgent.toLowerCase().indexOf('webkit') != -1,
bIE = document.attachEvent && !bOpera,

/* regexps */
r = {
	script: /<script([^>]*)>/i,
	script_src: /src=["\']([^"\']+)["\']/i,
	space: /\s+/,
	ltrim: /^[\s\r\n]+/g,
	rtrim: /[\s\r\n]+$/g,
	style_ie: /(\-([a-z]){1})/g
},

lastWait = null;

BX.ext = function(ob) {for (var i in ob) this[i] = ob[i];}

/* OO emulation utility */
BX.ext({
	extend: function(child, parent) 
	{
		var f = function() {};
		f.prototype = parent.prototype;
		child.prototype = new f();
		child.prototype.constructor = child;
		child.superclass = parent.prototype;
	}
});

// language utility
BX.ext({
	message: function(mess)
	{
		if (BX.type.isString(mess))
			return MESS[mess];
		else
		{
			for (var i in mess) 
			{
				MESS[i] = mess[i];
			}
		}
	},
	bitrix_sessid: function() {return MESS.bitrix_sessid;}
});

/* DOM manipulation */
BX.ext({
	create: function(tag, data, context)
	{
		context = context || document;

		if (null == data && typeof tag == 'object' && tag.constructor !== String)
		{
			data = tag; tag = tag.tag;
		}
		
		if (BX.browser.IsIE() && null != data && null != data.props && (data.props.name || data.props.id))
		{
			elem = context.createElement('<' + tag + (data.props.name ? ' name="' + data.props.name + '"' : '') + (data.props.id ? ' id="' + data.props.id + '"' : '') + '>');
		}
		else
		{
			elem = context.createElement(tag);
		}
		
		return data ? BX.adjust(elem, data) : elem;
	},
	
	adjust: function(elem, data)
	{
		var j;
		
		if (!elem.nodeType)
			return;
			
		if (elem.nodeType == 9)
			elem = elem.body;
		
		if (data.attrs)
		{
			for (j in data.attrs)
			{
				if (j == 'class' || j == 'className')
					elem.className = data.attrs[j];
				else if(data.attrs[j] == "")
					elem.removeAttribute(j);
				else
					elem.setAttribute(j, data.attrs[j]);
			}
		}
		
		if (data.style)
		{
			for (j in data.style)
				elem.style[j] = data.style[j];
		}
		
		if (data.props)
		{
			for (j in data.props)
				elem[j] = data.props[j];
		}
			
		if (data.events)
		{
			for (j in data.events)
				BX.bind(elem, j, data.events[j]);
		}
		
		if (data.children && data.children.length > 0)
		{
			for (j=0,len=data.children.length; j<len; j++)
			{
				if (BX.type.isNotEmptyString(data.children[j]))
					elem.innerHTML += data.children[j];
				else
					elem.appendChild(data.children[j]);
			}
		}
		else if (data.text)
		{
			elem.appendChild((elem.ownerDocument || document).createTextNode(data.text));
		}
		else if (data.html)
		{
			elem.innerHTML = data.html;
		}

		return elem;
	},
	
	remove: function(ob)
	{
		if (null != ob.parentNode)
			ob.parentNode.removeChild(ob);
		ob = null;
		return null;
	},

	cleanNode: function(node, bSuicide)
	{
		node = BX(node);
		bSuicide = !!bSuicide;
		
		if (node && node.childNodes)
		{
			while(node.childNodes.length > 0)
				node.removeChild(node.firstChild);
		}
		
		if (bSuicide)
		{
			node = BX.remove(node);
		}
		
		return node;
	},
	
	addClass: function(ob, value)
	{
		var classNames;
		
		if (ob = BX(ob))
		{
			if (!ob.className)
			{
				ob.className = value
			}
			else 
			{
				classNames = (value || "").split(r.space);
			
				var className = " " + ob.className + " ";
				for (var j = 0, cl = classNames.length; j < cl; j++) 
				{
					if (className.indexOf(" " + classNames[j] + " ") < 0) 
					{
						ob.className += " " + classNames[j];
					}
				}
			}
		}
		
		return ob;
	},
	
	removeClass: function(ob, value)
	{
		if (ob = BX(ob))
		{
			if (ob.className) 
			{
				if (BX.type.isString(value))
				{
					var classNames = value.split(r.space), className = " " + ob.className + " ";
					for (var j = 0, cl = classNames.length; j < cl; j++) 
					{
						className = className.replace(" " + classNames[j] + " ", " ");
					}
					
					ob.className = BX.util.trim(className);
				}
				else 
				{
					ob.className = "";
				}
			}
		}

		return ob;
	},
	
	toggleClass: function(ob, value)
	{
		if (BX.type.isArray(value))
		{
			var className = ' ' + ob.className + ' ';
			for (var j = 0, len = value.length; j < len; j++)
			{
				if (BX.hasClass(ob, value[j]))
				{
					className = (' ' + className + ' ').replace(' ' + value[j] + ' ', '');
					className += ' ' + value[j >= len-1 ? 0 : j+1];

					j--;
					break;
				}
			}
			
			if (j == len)
				ob.className += ' ' + value[0];
			else
				ob.className = className;
			
			ob.className = BX.util.trim(ob.className);
		}
		else if (BX.type.isNotEmptyString(value))
		{
			var className = ob.className;
			if (BX.hasClass(ob, value))
			{
				className = (' ' + className + ' ').replace(' ' + value + ' ', '');
			}
			else
			{
				className += ' ' + value;
			}
			
			ob.className = BX.util.trim(className);
		}
		
		return ob;
	},
	
	hasClass: function(el, className)
	{
		if (!el.className)
			return false;
		return ((" " + el.className + " ").indexOf(" " + className + " ")) >= 0;
	},
	
	_styleIEPropertyName: function(name)
	{
		if (name == 'float') name = 'cssFloat';
		else if (r.style_ie.test(name)) 
		{
			name = name.replace(r.style_ie, function () 
			{
				return arguments[2].toUpperCase();
			});
		}
		return name;
	},
	
	style: function(el, property, value)
	{
		if (!BX.type.isElementNode(el))
			return;
		
		if (null == value)
		{
			var res;
			if(el.currentStyle)
			{
				res = el.currentStyle[BX._styleIEPropertyName(property)];
			}
			else if(window.getComputedStyle)
				res = BX.GetContext(el).getComputedStyle(el, null).getPropertyValue(property);
			if(!res)
				res = '';
			return res;
		}
		else
		{
			el.style[BX._styleIEPropertyName(property)] = value;
			return el;
		}
	},
	
/*
	params: {
		tagName|tag : 'tagName',
		className|class : 'className',
		attribute : {attribute : value, attribute : value} | attribute | [attribute, attribute....],
		property : {prop: value, prop: value} | prop | [prop, prop]
	}
	
	all values can be RegExps or strings
*/
	_checkNode: function(obj, params)
	{
		params = params || {};
		if (!params.allowTextNodes && !BX.type.isElementNode(obj))
			return false;
		var i,j;
		for (i in params)
		{
			switch(i)
			{
				case 'tag':
				case 'tagName':
					if (BX.type.isString(params[i]))
					{
						if (obj.tagName.toUpperCase() != params[i].toUpperCase())
							return false;
					}
					else if (params[i] instanceof RegExp)
					{
						if (!params[i].test(obj.tagName))
							return false;
					}
				break;
				
				case 'class':
				case 'className':
					if (BX.type.isString(params[i]))
					{
						if (!BX.hasClass(obj, params[i]))
							return false;
					}
					else if (params[i] instanceof RegExp)
					{
						if (!params[i].test(obj.className))
							return false;
					}
				break;
				
				case 'attr':
				case 'attribute':
					if (BX.type.isString(params[i]))
					{
						if (!obj.getAttribute(params[i]))
							return false;
					}
					else if (BX.type.isArray(params[i]))
					{
						for (j = 0, len = params[i].length; j < len; j++)
						{
							if (params[i] && !obj.getAttribute(params[i]))
								return false;
						}
					}
					else
					{
						for (j in params[i])
						{
							if (BX.type.isString(params[i][j]))
							{
								if (obj.getAttribute(j) != params[i][j])
									return false;
							}
							else if (params[i][j] instanceof RegExp)
							{
								if (!params[i][j].test(obj.getAttribute(j)))
									return false;
							}
						}
					}
				break;
				
				case 'property':
					if (BX.type.isString(params[i]))
					{
						if (!obj[params[i]])
							return false;
					}
					else if (BX.type.isArray(params[i]))
					{
						for (j = 0, len = params[i].length; j < len; j++)
						{
							if (params[i] && !obj[params[i]])
								return false;
						}
					}
					else
					{
						for (j in params[i])
						{
							if (BX.type.isString(params[i][j]))
							{
								if (obj[j] != params[i][j])
									return false;
							}
							else if (params[i][j] instanceof RegExp)
							{
								if (!params[i][j].test(obj[j]))
									return false;
							}
						}
					}
				break;
			}
		}
		
		return true;
	},
	
	findChild: function(obj, params, recursive)
	{
		if(!obj || !obj.childNodes) return null;
		
		recursive = !!recursive;
		
		var n = obj.childNodes.length;
		
		for (var j=0; j<n; j++)
		{
			var child = obj.childNodes[j];
			
			if (BX._checkNode(child, params))
				return child;
			
			if(recursive == true)
			{
				var res = BX.findChild(child, params, true);
				if (res) 
					return res;
			}
		}
		return null;
	},
	
	findParent: function(obj, params)
	{
		if(!obj)
			return null;
		
		var o = obj;
		while(o.parentNode)
		{
			var parent = o.parentNode;
			
			if (BX._checkNode(parent, params))
				return parent;
			
			o = parent;
		}
		return null;
	},
	
	findNextSibling: function(obj, params)
	{
		if(!obj)
			return null;
		var o = obj;
		while(o.nextSibling)
		{
			var sibling = o.nextSibling;
			if (BX._checkNode(sibling, params))
				return sibling;
			o = sibling;
		}
		return null;
	},
	
	findPreviousSibling: function(obj, params)
	{
		if(!obj)
			return null;
			
		var o = obj;
		while(o.previousSibling)
		{
			var sibling = o.previousSibling;
			if(BX._checkNode(sibling, params))
				return sibling;
			o = sibling;
		}
		return null;
	},
	
	clone: function(obj, bCopyObj)
	{
		var _obj, i;
		if (bCopyObj !== false)
			bCopyObj = true;
		
		if (BX.type.isDomNode(obj))
		{
			_obj = obj.cloneNode(bCopyObj);
		}
		else if (typeof obj == 'object')
		{
			if (bCopyObj)
			{
				if (BX.type.isArray(obj))
				{
					_obj = [];
				}
				else
				{
					_obj =  {};
					if (obj.constructor)
					{
						_obj = new obj.constructor(obj);
					}
				}
			}
			
			for (i in obj)
			{
				if (typeof obj[i] == "object" && bCopyObj)
					_obj[i] = BX.clone(obj[i], bCopyObj);
				else
					_obj[i] = obj[i];
			}
		}
		else
		{
			_obj = obj;
		}
		
		return _obj;
	}
});

/* events */
BX.ext({
	bind: function(el, evname, func)
	{
		if(el.attachEvent) // IE
		{
			el.attachEvent("on" + evname, BX.proxy(func, el));
		}
		else 
		{
			if(el.addEventListener) // Gecko / W3C
				el.addEventListener(evname, func, false);
			else
				el["on" + evname] = func;
		}
		
		eventsList[eventsList.length] = {'element': el, 'event': evname, 'fn': func};
	},

	unbind: function(el, evname, func)
	{
		if(el.detachEvent) // IE
			el.detachEvent("on" + evname, BX.proxy(func, el));
		else if(el.removeEventListener) // Gecko / W3C
			el.removeEventListener(evname, func, false);
		else
			el["on" + evname] = null;
	},

	unbindAll: function(el)
	{
		for (var i=0,len=eventsList.length; i<len; i++)
		{
			if (eventsList[i] && (null==el || el==eventsList[i].element))
			{
				BX.unbind(eventsList[i].element, eventsList[i].event, eventsList[i].fn);
				eventsList[i] = null;
			}
		}
		
		if (null==el)
		{
			eventsList = [];
		}
	},
	
	proxy_context: null,
	
	delegate: function (func, thisObject) {
		return function() {
			BX.proxy_context = this;
			var res = func.apply(thisObject, arguments);
			BX.proxy_context = null;
			return res;
		}
	},
	
	proxy: function(func, thisObject) 
	{
		if (null == thisObject.__proxy_id)
		{
			proxyList[thisObject.__proxy_id = proxyList.length] = {};
		}
		
		if (null == func.__proxy_id)
			func.__proxy_id = proxyId++;

		if (null == proxyList[thisObject.__proxy_id][func.__proxy_id])
			proxyList[thisObject.__proxy_id][func.__proxy_id] = BX.delegate(func, thisObject);
		
		return proxyList[thisObject.__proxy_id][func.__proxy_id];
	},
	
	False: function() {return false;},
	
	DoNothing: function() {},
	
	// TODO: also check event handlers set via BX.bind()
	denyEvent: function(el, ev)
	{
		deniedEvents.push([el, ev, el['on' + ev]]);
		el['on' + ev] = BX.DoNothing;
	},
	
	allowEvent: function(el, ev)
	{
		for(var i=0, len=deniedEvents.length; i<len; i++)
		{
			if (deniedEvents[i][0] == el && deniedEvents[i][1] == ev)
			{
				el['on' + ev] = deniedEvents[i][2];
				BX.util.deleteFromArray(deniedEvents, i);
				return;
			}
		}
	},
	
	PreventDefault: function(e)
	{
		if(!e) e = window.event;
		if(e.stopPropagation)
		{
			e.preventDefault();
			e.stopPropagation();
		}
		else
		{
			e.cancelBubble = true;
			e.returnValue = false;
		}
		return false;
	}
});

/* custom events */
BX.ext({
	/*
		BX.addCustomEvent(eventObject, eventName, eventHandler) - set custom event handler for particular object
		BX.addCustomEvent(eventName, eventHandler) - set custom event handler for all objects
	*/
	addCustomEvent: function(eventObject, eventName, eventHandler)
	{
		/* shift parameters for short version */
		if (BX.type.isString(eventObject))
		{
			eventHandler = eventName;
			eventName = eventObject;
			eventObject = window;
		}
		
		eventName = eventName.toUpperCase();
	
		if (!customEvents[eventName])
			customEvents[eventName] = [];

		customEvents[eventName].push(
			{
				handler: eventHandler,
				obj: eventObject
			}
		);
	},

	removeCustomEvent: function(eventObject, eventName, eventHandler)
	{
		/* shift parameters for short version */
		if (BX.type.isString(eventObject))
		{
			eventHandler = eventName;
			eventName = eventObject;
			eventObject = window;
		}

		eventName = eventName.toUpperCase();
		
		if (!customEvents[eventName])
			return;

		var l = customEvents[eventName].length;
		if (l == 1)
		{
			delete customEvents[eventName];
			return;
		}

		for (var i = 0; i < l; i++)
		{
			if (!customEvents[eventName][i])
				continue;
			if (customEvents[eventName][i].handler == eventHandler && customEvents[eventName][i].obj == eventObject)
			{
				delete customEvents[eventName][i];
				return;
			}
		}
	},

	onCustomEvent: function(eventObject, eventName, arEventParams)
	{
		/* shift parameters for short version */
		if (BX.type.isString(eventObject))
		{
			eventHandler = eventName;
			eventName = eventObject;
			eventObject = window;
		}
		
		eventName = eventName.toUpperCase();
	
		if (!customEvents[eventName])
			return;

		if (!arEventParams)
			arEventParams = [];

		var h;
		for (var i = 0, l = customEvents[eventName].length; i < l; i++)
		{
			h = customEvents[eventName][i];
			if (!h || !h.handler)
				continue;

			if (h.obj == window || /*eventObject == window || */h.obj == eventObject) //- only global event handlers will be called
			{
				h.handler.apply(eventObject, arEventParams);
			}
		}
	}
});

/* ready */
BX.ext({
	isReady: false,
	
	ready: function(handler)
	{
		BX.bindReady();
		
		if (BX.isReady)
			handler.call(document);
		else if (readyList)
			readyList.push(handler);
	},
	
	runReady: function()
	{
		if (!BX.isReady) 
		{
			if (!document.body) 
			{
				return setTimeout(BX.runReady, 15);
			}

			BX.isReady = true;

			if (readyList && readyList.length > 0) 
			{
				var fn, i = 0;
				while (fn = readyList[i++]) 
				{
					fn.call(document);
				}
				
				readyList = null;
			}

			// TODO: check ready handlers binded some other way;
		}
	},
	
	bindReady: function ()
	{
		if (readyBound)
			return;
		
		readyBound = true;

		if (document.readyState === "complete") 
		{
			return BX.runReady();
		}

		if (document.addEventListener)
		{
			document.addEventListener("DOMContentLoaded", __readyHandler, false);
			window.addEventListener("load", BX.runReady, false);
		} 
		else if (document.attachEvent) // IE
		{
			document.attachEvent("onreadystatechange", __readyHandler);
			window.attachEvent("onload", BX.runReady);

			var toplevel = false;
			try {toplevel = (window.frameElement == null);} catch(e) {}

			if (document.documentElement.doScroll && toplevel) 
				doScrollCheck();
		}
	}
});

/* browser detection */
BX.ext({
	browser:{
		IsIE: function()
		{
			return bIE;
		},
		
		IsIE6: function()
		{
			return (/MSIE 6/i.test(navigator.userAgent));
		},

		IsOpera: function()
		{
			return bOpera;
		},

		IsSafari: function()
		{
			return bSafari;
		},
		
		IsDoctype: function(pDoc)
		{
			pDoc = pDoc || document;
			
			if (pDoc.compatMode)
				return (pDoc.compatMode == "CSS1Compat");

			if (pDoc.documentElement && pDoc.documentElement.clientHeight)
				return true;

			return false;
		}
	}
});

/* low-level fx funcitons*/
BX.ext({
	toggle: function(ob, values)
	{
		if (BX.type.isArray(values))
		{
			for (var i=0,len=values.length; i<len; i++)
			{
				if (ob == values[i])
				{
					ob = values[i==len-1 ? 0 : i+1]
					break;
				}
			}
			if (i==len)
				ob = values[0];
		}
		
		return ob;
	}
});

/* some useful util functions */
BX.ext({
	util:{
		array_merge: function(first, second) 
		{
			if (!BX.type.isArray(first)) first = [];
			if (!BX.type.isArray(second)) second = [];
			
			var i = first.length, j = 0;

			if (typeof second.length === "number") 
			{
				for (var l = second.length; j < l; j++) 
				{
					first[i++] = second[j];
				}
			} 
			else 
			{
				while (second[j] !== undefined) 
				{
					first[i++] = second[j++];
				}
			}

			first.length = i;

			return first;
		},
		
		array_unique: function(ar)
		{
			var i=0,j,len=ar.length;
			if(len<2) return ar;
			
			for (; i<len-1;i++)
			{
				for (j=i+1; j<len;j++)
				{
					if (ar[i]==ar[j])
					{
						ar.splice(j--,1); len--;
					}
				}
			}
			
			return ar;
		},
		
		in_array: function(needle, haystack)
		{
			for(var i=0; i<haystack.length; i++)
			{
				if(haystack[i] == needle)
					return true;
			}
			return false;
		},
		
		array_values: function(ar) 
		{
			var ar1 = [],j=0;
			for (var i=0,len=ar.length; i < len; i++)
			{
				if (null != ar[i]) ar1[j++]=ar[i];
			}
			return ar1;
		},
		
		trim: function(s)
		{
			if (BX.type.isString(s))
				return s.replace(r.ltrim, '').replace(r.rtrim, '');
			else
				return s;
		},

		urlencode: function(s){return encodeURIComponent(s);},
		
		// it may also be useful. via sVD.
		deleteFromArray: function(ar, ind) {return ar.slice(0, ind).concat(ar.slice(ind + 1));},
		insertIntoArray: function(ar, ind, el) {return ar.slice(0, ind).concat([el]).concat(ar.slice(ind));},
		
		htmlspecialchars: function(str)
		{
			if(!str.replace) return str;
			
			return str.replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
		},
		
		htmlspecialcharsback: function(str)
		{
			if(!str.replace) return str;

			return str.replace(/\&quot;/g, '"').replace(/&#39;/g, "'").replace(/\&lt;/g, '<').replace(/\&gt;/g, '>').replace(/\&amp;/g, '&');
		}
	},
	
	type: {
		isString: function(item) {
			return typeof (item) == "string" || item instanceof String;
		},
		isNotEmptyString: function(item) {
			return typeof (item) == "string" || item instanceof String ? item.length > 0 : false;
		},
		isBoolean: function(item) {
			return typeof (item) == "boolean" || item instanceof Boolean;
		},
		isNumber: function(item) {
		    return typeof (item) == "number" || item instanceof Number;
		},
		isFunction: function(item) {
		    return typeof (item) == "function" || item instanceof Function;
		},
		isElementNode: function(item) {
			return item && typeof (item) == "object" && "nodeType" in item && item.nodeType == 1; //document.body.ELEMENT_NODE;
		},
		isDomNode: function(item) {
			return item && typeof (item) == "object" && "nodeType" in item;
		},
		isArray: function(item) {
			return typeof (item) == 'array' || item instanceof Array;
		}
	},
	
	evalGlobal: function(data) 
	{
		if (data) 
		{
			var head = document.getElementsByTagName("head")[0] || document.documentElement,
				script = document.createElement("script");

			script.type = "text/javascript";

			if (!BX.browser.IsIE()) 
			{
				script.appendChild(document.createTextNode(data));
			} 
			else 
			{
				script.text = data;
			}

			head.insertBefore(script, head.firstChild);
			head.removeChild(script);
		}
	},
	
	processHTML: function(HTML, scriptsRunFirst)
	{
		var matchScript, scripts = [], data = HTML;

		while ((matchScript = data.match(r.script)) !== null)
		{
			var end = data.search('<\/script>', 'i');
			if (end == -1)
				break;

			var bRunFirst = scriptsRunFirst || (matchScript[1].indexOf('bxrunfirst') != '-1');

			var matchSrc;
			if ((matchSrc = matchScript[1].match(r.script_src)) !== null)
				scripts.push({"bRunFirst": bRunFirst, "isInternal": false, "JS": matchSrc[1]});
			else
			{
				var start = matchScript.index + matchScript[0].length;
				var js = data.substr(start, end-start);

				scripts.push({"bRunFirst": bRunFirst, "isInternal": true, "JS": js});
			}

			data = data.substr(0, matchScript.index) + data.substr(end+9);
		}
		
		return {'HTML': data, 'SCRIPT': scripts};
	},
	
	garbage: function(call, thisObject)
	{
		garbageCollectors.push({callback: call, context: thisObject});
	}
});


/* window pos functions */
BX.ext({
	GetDocElement: function (pDoc)
	{
		pDoc = pDoc || document;
		return (BX.browser.IsDoctype(pDoc) ? pDoc.documentElement : pDoc.body);
	},

	GetContext: function(node)
	{
		if (BX.type.isElementNode(node))
			return node.ownerDocument.parentWindow || node.ownerDocument.defaultView || window;
		else if (BX.type.isDomNode(node))
			return node.parentWindow || node.defaultView || window;
		else
			return window;
	},
	
	GetWindowInnerSize: function(pDoc)
	{
		var width, height;
		
		pDoc = pDoc || document;
		
		if (self.innerHeight) // all except Explorer
		{
			width = BX.GetContext(pDoc).innerWidth;
			height = BX.GetContext(pDoc).innerHeight;
		}
		else if (pDoc.documentElement && (pDoc.documentElement.clientHeight || pDoc.documentElement.clientWidth)) // Explorer 6 Strict Mode
		{
			width = pDoc.documentElement.clientWidth;
			height = pDoc.documentElement.clientHeight;
		}
		else if (pDoc.body) // other Explorers
		{
			width = pDoc.body.clientWidth;
			height = pDoc.body.clientHeight;
		}
		return {innerWidth : width, innerHeight : height};
	},

	GetWindowScrollPos: function(pDoc)
	{
		var left, top;
		
		pDoc = pDoc || document;
		
		if (self.pageYOffset) // all except Explorer
		{
			left = BX.GetContext(pDoc).pageXOffset;
			top = BX.GetContext(pDoc).pageYOffset;
		}
		else if (pDoc.documentElement && (pDoc.documentElement.scrollTop || pDoc.documentElement.scrollLeft)) // Explorer 6 Strict
		{
			left = pDoc.documentElement.scrollLeft;
			top = pDoc.documentElement.scrollTop;
		}
		else if (pDoc.body) // all other Explorers
		{
			left = pDoc.body.scrollLeft;
			top = pDoc.body.scrollTop;
		}
		return {scrollLeft : left, scrollTop : top};
	},

	GetWindowScrollSize: function(pDoc)
	{
		var width, height;
		if (!pDoc)
			pDoc = document;

		if ( (pDoc.compatMode && pDoc.compatMode == "CSS1Compat"))
		{
			width = pDoc.documentElement.scrollWidth;
			height = pDoc.documentElement.scrollHeight;
		}
		else
		{
			if (pDoc.body.scrollHeight > pDoc.body.offsetHeight)
				height = pDoc.body.scrollHeight;
			else
				height = pDoc.body.offsetHeight;

			if (pDoc.body.scrollWidth > pDoc.body.offsetWidth ||
				(pDoc.compatMode && pDoc.compatMode == "BackCompat") ||
				(pDoc.documentElement && !pDoc.documentElement.clientWidth)
			)
				width = pDoc.body.scrollWidth;
			else
				width = pDoc.body.offsetWidth;
		}
		return {scrollWidth : width, scrollHeight : height};
	},

	GetWindowSize: function(pDoc)
	{
		var innerSize = this.GetWindowInnerSize(pDoc);
		var scrollPos = this.GetWindowScrollPos(pDoc);
		var scrollSize = this.GetWindowScrollSize(pDoc);

		return  {
			innerWidth : innerSize.innerWidth, innerHeight : innerSize.innerHeight,
			scrollLeft : scrollPos.scrollLeft, scrollTop : scrollPos.scrollTop,
			scrollWidth : scrollSize.scrollWidth, scrollHeight : scrollSize.scrollHeight
		};
	},
	
	is_relative: function(el) 
	{
		var p = BX.style(el, 'position');
		return p == 'relative' || p == 'absolute';
	},
	
	pos: function(el, bRelative) 
	{
		var r = { top: 0, right: 0, bottom: 0, left: 0, width: 0, height: 0 };
		bRelative = !!bRelative
		if (!el)
			return r;
		if (typeof (el.getBoundingClientRect) != "undefined" && el.ownerDocument == document && !bRelative) 
		{
			var clientRect = el.getBoundingClientRect();
			var root = document.documentElement;
			var body = document.body;

			r.top = clientRect.top + root.scrollTop + body.scrollTop;
			r.left = clientRect.left + root.scrollLeft + body.scrollLeft;
			r.width = clientRect.right - clientRect.left;
			r.height = clientRect.bottom - clientRect.top;
			r.right = clientRect.right + root.scrollLeft + body.scrollLeft;
			r.bottom = clientRect.bottom + root.scrollTop + body.scrollTop;
		}
		else 
		{
			var x = 0, y = 0, w = el.offsetWidth, h = el.offsetHeight;
			var first = true;
			for (; el != null; el = el.offsetParent) 
			{
				if (bRelative && BX.is_relative(el))
					break;
				
				x += el.offsetLeft;
				y += el.offsetTop;
				if (first) 
				{
					first = false;
					continue;
				}
				var elBorderLeftWidth = 0, elBorderTopWidth = 0;

				if (BX.browser.IsIE()) 
				{
					elBorderLeftWidth = parseInt(BX.style(el, 'border-left-width'));
					elBorderTopWidth = parseInt(BX.style(el, 'border-top-width'));
				}
				else 
				{
					var elStyle;
					if (elStyle = el.currentStyle)
					{
						elBorderLeftWidth = parseInt(elStyle.getAttribute('borderLeftWidth'));
						elBorderTopWidth = parseInt(elStyle.getAttribute('borderTopWidth'));
					}
				}
				if (!isNaN(elBorderLeftWidth) && elBorderLeftWidth > 0)
					x += elBorderLeftWidth;
				if (!isNaN(elBorderTopWidth) && elBorderTopWidth > 0)
					y += elBorderTopWidth;
			}

			r.top = y;
			r.left = x;
			r.width = w;
			r.height = h;
			r.right = r.left + w;
			r.bottom = r.top + h;
		}
		
		return r;
	},
	
	align: function(pos, w, h)
	{
		var pDoc = document;
		if (BX.type.isElementNode(pos))
		{
			pDoc = pos.ownerDocument;
			pos = BX.pos(pos);
		}
	
		var x = pos["left"], y = pos["bottom"];

		var scroll = BX.GetWindowScrollPos(pDoc);
		var size = BX.GetWindowInnerSize(pDoc);

		if((size.innerWidth + scroll.scrollLeft) - (pos["left"] + w) < 0)
		{
			if(pos["right"] - w >= 0 )
				x = pos["right"] - w;
			else
				x = scroll.scrollLeft;
		}

		if((size.innerHeight + scroll.scrollTop) - (pos["bottom"] + h) < 0)
		{
			if(pos["top"] - h >= 0)
				y = pos["top"] - h;
			else
				y = scroll.scrollTop;
		}

		return {'left':x, 'top':y};
	}
});

/* non-xhr loadings */
BX.ext({
	showWait: function(node, msg)
	{
		node = BX(node) || document.body || document.documentElement;
		msg = msg || BX.message('JS_CORE_LOADING');

		var arContainerPos = BX.pos(node);
		var container_id = node.id || Math.random();
		
		if (!arContainerPos) return;

		var div_top = arContainerPos.top + 5;
		if (div_top < BX.GetDocElement().scrollTop) div_top = BX.GetDocElement().scrollTop + 5;
		
		var obMsg = node.bxmsg = document.body.appendChild(BX.create('DIV', {
			props: {
				id: 'wait_' + container_id,
				className: 'bx-core-waitwindow'
			},
			style: {
				top: div_top + 'px',
				left: (arContainerPos.right - 200) + 'px'
			},
			text: msg
		}));
		
		lastWait = obMsg;
		
		return obMsg;
	},

	closeWait: function(node, obMsg)
	{
		obMsg = obMsg || node && (node.bxmsg || BX('wait_' + node.id)) || lastWait;
		if (obMsg && obMsg.parentNode)
		{
			obMsg.parentNode.removeChild(obMsg);
			if (node) node.bxmsg = null;
			BX.cleanNode(obMsg, true);
		}
	},
	
	loadScript: function(script, callback, doc)
	{
		if (!BX.isReady)
		{
			var _args = arguments;
			BX.ready(function() {
				BX.loadScript.apply(this, _args);
			});
			return;
		}
		
		doc = doc || document;
		
		if (BX.type.isString(script))
			script = [script];
		var _callback = function()
		{
			if (!callback)
				return;
			else if (BX.type.isFunction(callback))
				return callback();
			else 
				return null;
		};
		var load_js = function(ind)
		{
			if (ind >= script.length)
				return _callback();
			var oScript = (doc.body || doc.documentElement).appendChild(doc.createElement('script'));
			oScript.src = script[ind];
			var bLoaded = false;
			oScript.onload = oScript.onreadystatechange = function()
			{
				if (!bLoaded && (!oScript.readyState || oScript.readyState == "loaded" || oScript.readyState == "complete"))
				{
					bLoaded = true;
					setTimeout(function (){load_js(++ind);}, 50);
				}
			};
		};
		
		load_js(0);
	},

	loadCSS: function(arCSS, doc, win)
	{
		if (!BX.isReady)
		{
			var _args = arguments;
			BX.ready(function() {
				BX.loadCSS.apply(this, _args);
			});
			return;
		}
	
		if (BX.type.isString(arCSS))
		{
			var bSingle = true;
			arCSS = [arCSS];
		}
		var i, l = arCSS.length, pLnk = [];
		
		if (l == 0)
			return;
		
		doc = doc || document;
		win = win ||window;
		
		if (!win.bxhead)
		{
			var heads = doc.getElementsByTagName('HEAD');
			win.bxhead = heads[0];
		}
		
		if (!win.bxhead)
			return;
			
		for (i = 0; i < l; i++)
		{
			var lnk = document.createElement('LINK');
			lnk.href = arCSS[i];
			lnk.rel = 'stylesheet';
			lnk.type = 'text/css';
			win.bxhead.appendChild(lnk);
			pLnk.push(lnk);
		}
		
		if (bSingle)
			return lnk;
		
		return pLnk;
	}
});

BX.ext({
	reload: function(back_url, bAddClearCache)
	{
		if (back_url === true)
		{
			bAddClearCache = true;
			back_url = null;
		}
	
		var new_href = back_url || top.location.href;
	
		var hashpos = new_href.indexOf('#'), hash = '';
		
		if (hashpos != -1)
		{
			hash = new_href.substr(hashpos);
			new_href = new_href.substr(0, hashpos);
		}

		if (bAddClearCache && new_href.indexOf('clear_cache=Y') < 0)
			new_href += (new_href.indexOf('?') == -1 ? '?' : '&') + 'clear_cache=Y';
			
		if (hash)
		{
			// hack for clearing cache in ajax mode components with history emulation
			if (bAddClearCache && (hash.substr(0, 5) == 'view/' || hash.substr(0, 6) == '#view/') && hash.indexOf('clear_cache%3DY') < 0) 
				hash += (hash.indexOf('%3F') == -1 ? '%3F' : '%26') + 'clear_cache%3DY'
		
			new_href = new_href.replace(/(\?|\&)_r=[\d]*/, '');
			new_href += (new_href.indexOf('?') == -1 ? '?' : '&') + '_r='+Math.round(Math.random()*10000) + hash;
		}
		
		top.location.href = new_href;
	},
	
	clearCache: function()
	{
		BX.showWait();
		BX.reload(true);
	}
});


BX.admin = {};

BX.admin.__borders = null;

BX.admin._CreateBorders = function()
{
	var color = 'orange'

	BX.admin.__borders = {};
	BX.admin.__borders.cont = document.body.appendChild(BX.create('DIV', {style: {
		display: 'none',
		height: '0px',
		width: '0px'
	}}));
	
	BX.admin.__borders.top = BX.admin.__borders.cont.appendChild(BX.create('DIV', {style: {
		position: 'absolute',
		height: '0px',
		borderTop: 'dashed 1px ' + color
	}}));
	BX.admin.__borders.right = BX.admin.__borders.cont.appendChild(BX.create('DIV', {style: {
		position: 'absolute',
		width: '10px',
		borderLeft: 'dashed 1px ' + color
	}}));
	BX.admin.__borders.bottom = BX.admin.__borders.cont.appendChild(BX.create('DIV', {style: {
		position: 'absolute',
		height: '0px',
		borderTop: 'dashed 1px ' + color
	}}));
	BX.admin.__borders.left = BX.admin.__borders.cont.appendChild(BX.create('DIV', {style: {
		position: 'absolute',
		width: '0px',
		borderLeft: 'dashed 1px ' + color
	}}));
}

BX.admin._AdjustBorders = function()
{
	var pos = BX.pos(this);
	var dx = 0;
	
	BX.adjust(BX.admin.__borders.top, {style: {
		'top': (pos.top - dx) + 'px',
		'left': (pos.left - dx) + 'px',
		'width': (pos.width + dx*2) + 'px'
	}});
	BX.adjust(BX.admin.__borders.right, {style: {
		'top': (pos.top - dx) + 'px',
		'left': (pos.right + dx) + 'px',
		'height': (pos.height + dx*2) + 'px'
	}});
	BX.adjust(BX.admin.__borders.bottom, {style: {
		'top': (pos.bottom + dx) + 'px',
		'left': (pos.left - dx) + 'px',
		'width': (pos.width + dx*2) + 'px'
	}});
	BX.adjust(BX.admin.__borders.left, {style: {
		'top': (pos.top - dx) + 'px',
		'left': (pos.left - dx) + 'px',
		'height': (pos.height + dx*2) + 'px'
	}});
}

BX.admin._ShowBorders = function(e)
{
	BX.admin.__borders.cont.style.display = 'block';
	BX.admin._AdjustBorders.apply(this);
	BX.PreventDefault(e);
}
BX.admin._HideBorders = function(e)
{
	BX.admin.__borders.cont.style.display = 'none';
	BX.PreventDefault(e);
}

BX.admin.setComponentBorder = function(comp)
{
	if (!BX.isReady)
	{
		return BX.ready(function() {BX.admin.setComponentBorder(comp)});
	}

	if (null == BX.admin.__borders)
	{
		BX.admin._CreateBorders();
	}
	
	comp = BX(comp);
	if (!comp) return;
	
	//var pos = BX.pos(comp);
	
	BX.bind(comp, 'mouseover', BX.admin._ShowBorders);
	BX.bind(comp, 'mouseout', BX.admin._HideBorders);

}

/* ready */
if (document.addEventListener) 
{
	__readyHandler = function() 
	{
		document.removeEventListener("DOMContentLoaded", __readyHandler, false);
		BX.runReady();
	};
} 
else if (document.attachEvent) 
{
	__readyHandler = function() 
	{
		if (document.readyState === "complete") 
		{
			document.detachEvent("onreadystatechange", __readyHandler);
			BX.runReady();
		}
	};
}

// hack for IE
function doScrollCheck() 
{
	if (BX.isReady) 
		return;

	try {document.documentElement.doScroll("left");} catch( error ) {setTimeout(doScrollCheck, 1); return;}

	BX.runReady();
}
/* \ready */

/* garbage collector */
function Trash()
{
	var i,len;

	for (i = 0, len = garbageCollectors.length; i<len; i++)
	{
		garbageCollectors[i].callback.apply(garbageCollectors[i].context || window);
		
		try {
			delete garbageCollectors[i];
			garbageCollectors[i] = null;
		} catch (e) {}
	}

	for (var i = 0, len = proxyList.length; i < len; i++)
	{
		try {
			delete proxyList[i];
			proxyList[i] = null;
		} catch (e) {}
	}

	try {BX.unbindAll();} catch(e) {}
	
	garbageCollectors = null;
	proxyList = null;
	eventsList = null;
	readyList = null;
	deniedEvents = null;
	customEvents = null;
	__readyHandler = null;
	
	//try {delete window.BX;} catch(e) {}
	
	// window.BX = null;
	// BX = null;
}

if(window.attachEvent) // IE
	window.attachEvent("onunload", Trash);
else if(window.addEventListener) // Gecko / W3C
	window.addEventListener('unload', Trash, false);
else
	window.onunload = Trash;
/* \garbage collector */

// set empty ready handler
BX(BX.DoNothing);

window.BX = BX;
})(window)