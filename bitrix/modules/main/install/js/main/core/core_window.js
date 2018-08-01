/* windows manager */
BX.WindowManager = {
	_stack: [],
	_runtime_resize: {},
	_delta: 5,
	_delta_start: 1000,
	currently_loaded: null,
	
	register: function (w)
	{
		this.currently_loaded = null;
		var div = w.Get();
		
		if (null != (_current = this._stack[this._stack.length-1]))
		{
			div.style.zIndex = parseInt(_current.Get().style.zIndex) + this._delta;
		}
		else
		{
			div.style.zIndex = this._delta_start;
		}
		
		w.zIndex = parseInt(div.style.zIndex); // compatibility hack
		
		this._stack.push(w);
		
		if (this._stack.length < 2)
		{
			BX.bind(document, 'keyup', BX.proxy(this.__checkKeyPress, this));
		}
	},
	
	unregister: function (w)
	{
		var _current;
		if (this._stack.length > 0)
		{
			while ((_current = this.__pop_stack()) != w)
			{
				if (!_current)
				{
					_current = null;
					break;
				}
			}
			
			if (this._stack.length <= 0)
			{
				this.enableKeyCheck();
			}

			return _current;
		}
		else
		{
			return null;
		}
	},
	
	__pop_stack: function(clean)
	{
		if (this._stack.length > 0)
		{
			var _current = this._stack.pop();
			BX.onCustomEvent(_current, 'onWindowUnRegister', [clean === true])
			
			return _current;
		}
		else
			return null;
	},
	
	clean: function()
	{
		while (this.__pop_stack(true)){};
		this._stack = null;
		this.disableKeyCheck();
	},
	
	Get: function()
	{
		if (this.currently_loaded)
			return this.currently_loaded;
		else if (this._stack.length > 0)
			return this._stack[this._stack.length-1];
		else
			return null;
	},
	
	setStartZIndex: function(value)
	{
		this._delta_start = value;
	},
	
	restoreStartZIndex: function()
	{
		this._delta_start = 1000;
	},
	
	__get_check_url: function(url)
	{
		var pos = url.indexOf('?');
		return pos == -1 ? url : url.substring(0, pos);
	},
	
	saveWindowSize: function(url, params)
	{
		var check_url = this.__get_check_url(url);
		if (BX.userOptions)
		{
			BX.userOptions.save('BX.WindowManager', 'size_' + check_url, 'width', params.width);
			BX.userOptions.save('BX.WindowManager', 'size_' + check_url, 'height', params.height);
		}
		
		this._runtime_resize[check_url] = params;
	},
	
	getRuntimeWindowSize: function(url)
	{
		return this._runtime_resize[this.__get_check_url(url)];
	},
	
	disableKeyCheck: function()
	{
		BX.unbind(document, 'keyup', BX.proxy(this.__checkKeyPress, this));
	},
	
	enableKeyCheck: function()
	{
		BX.bind(document, 'keyup', BX.proxy(this.__checkKeyPress, this));
	},
	
	__checkKeyPress: function(e)
	{
		if (null == e)
			e = window.event;
		
		if (e.keyCode == 27)
		{
			var wnd = BX.WindowManager.Get();
			if (wnd) wnd.Close();
		}
	}
}

BX.garbage(BX.WindowManager.clean, BX.WindowManager);

/* base button class */
BX.CWindowButton = function(params)
{
	this.title = params.title; // html value attr
	this.hint = params.hint; // html title attr
	this.id = params.id; // html name and id attrs
	this.name = params.name // html name or value attrs when id and title 're absent
	
	this.action = params.action;
	this.onclick = params.onclick;
	
	this.btn = null;
}

BX.CWindowButton.prototype.disable = function(){if (this.btn) this.btn.disabled = true;};
BX.CWindowButton.prototype.enable = function(){if (this.btn) this.btn.disabled = false;};

BX.CWindowButton.prototype.Button = function(parentWindow)
{
	this.parentWindow = parentWindow;
	
	var btn = {
		props: {
			'type': 'button',
			'name': this.id ? this.id : this.name,
			'value': this.title ? this.title : this.name, 
			'id': this.id
		}
	};
	
	if (this.hint)
		btn.props.title = this.hint;
	
	if (this.action)
	{
		btn.events = {
			'click': BX.delegate(this.action, this)
		};
	}
	else if (this.onclick)
	{
		if (BX.browser.IsIE())
		{
			btn.events = {
				'click': BX.delegate(function() {eval(this.onclick)}, this)
			};
		}
		else
		{
			btn.attrs = {
				'onclick': this.onclick
			};
		}
	}
	
	this.btn = BX.create('INPUT', btn);
	
	return this.btn;
}

/* base window class */
BX.CWindow = function(div, type)
{
	this.DIV = div || document.createElement('DIV');
	
	this.SETTINGS = {
		resizable: false,
		min_height: 0,
		min_width: 0,
		draggable: false,
		drag_restrict: true,
		resize_restrict: true
	};
	
	this.ELEMENTS = {
		draggable: [],
		resizer: [],
		close: []
	};
	
	this.type = type == 'float' ? 'float' : 'dialog';
	
	BX.adjust(this.DIV, {
		props: {
			className: 'bx-core-window'
		},
		style: {
			'zIndex': 0,
			'position': 'absolute',
			'display': 'none',
			'top': '0px',
			'left': '0px',
			'height': '100px',
			'width': '100px'
		}
	});
	
	this.isOpen = false;
	
	BX.addCustomEvent(this, 'onWindowRegister', BX.delegate(this.onRegister, this));
	BX.addCustomEvent(this, 'onWindowUnRegister', BX.delegate(this.onUnRegister, this));
	
	BX.ready(BX.delegate(function() {
		document.body.appendChild(this.DIV);
	}, this));
}

BX.CWindow.prototype.Get = function () {return this.DIV};

BX.CWindow.prototype.Show = function(bNotRegister)
{
	this.DIV.style.display = 'block';
	
	if (!bNotRegister)
	{
		BX.WindowManager.register(this);
		BX.onCustomEvent(this, 'onWindowRegister');
	}
}

BX.CWindow.prototype.Hide = function()
{
	BX.WindowManager.unregister(this);
	this.DIV.style.display = 'none';
}

BX.CWindow.prototype.onRegister = function()
{
	this.isOpen = true;
}

BX.CWindow.prototype.onUnRegister = function(clean)
{
	this.isOpen = false;

	if (clean || this.PARAMS.content_url)
	{
		if (clean) {BX.onCustomEvent(this, 'onWindowClose', [this, true]);}
	
		if (this.DIV.parentNode)
			this.DIV.parentNode.removeChild(this.DIV);
	}
	else
	{
		this.DIV.style.display = 'none';
	}
}

BX.CWindow.prototype.CloseDialog = // compatibility
BX.CWindow.prototype.Close = function()
{
	BX.onCustomEvent(this, 'onWindowClose', [this]);

	if (this.bExpanded) this.__expand();
	BX.WindowManager.unregister(this);
}

BX.CWindow.prototype.SetResize = function(elem)
{
	elem.style.cursor = 'se-resize';
	BX.bind(elem, 'mousedown', BX.proxy(this.__startResize, this));
	
	this.ELEMENTS.resizer.push(elem);
	this.SETTINGS.resizable = true;
}

BX.CWindow.prototype.SetExpand = function(elem, event_name)
{
	event_name = event_name || 'click';
	BX.bind(elem, event_name, BX.proxy(this.__expand, this));
}

BX.CWindow.prototype.__expand_onresize = function()
{
	var windowSize = BX.GetWindowInnerSize();
	this.DIV.style.width = windowSize.innerWidth + "px";
	this.DIV.style.height = windowSize.innerHeight + "px";
	
	BX.onCustomEvent(this, 'onWindowResize');
}

BX.CWindow.prototype.__expand = function()
{
	var pDocElement = BX.GetDocElement();

	if (!this.bExpanded)
	{
		var wndScroll = BX.GetWindowScrollPos();
	
		this.__expand_settings = {
			resizable: this.SETTINGS.resizable,
			draggable: this.SETTINGS.draggable,
			width: this.DIV.style.width,
			height: this.DIV.style.height,
			left: this.DIV.style.left,
			top: this.DIV.style.top,
			scroll: wndScroll.scrollTop,
			overflow: BX.style(pDocElement, 'overflow')
		}
		
		this.SETTINGS.resizable = false;
		this.SETTINGS.draggable = false;
		
		pDocElement.style.overflow = 'hidden';

		var wndSize = BX.GetWindowInnerSize();

		pDocElement.scrollTop = 0;
		
		this.DIV.style.top = '0px';
		this.DIV.style.left = '0px';
		
		this.DIV.style.width = wndSize.innerWidth + 'px';
		this.DIV.style.height = wndSize.innerHeight + 'px';
		
		this.bExpanded = true;
		
		BX.onCustomEvent(this, 'onWindowExpand');
		BX.onCustomEvent(this, 'onWindowResize');
			
		BX.bind(window, 'resize', BX.proxy(this.__expand_onresize, this));
			
	}
	else
	{
		BX.unbind(window, 'resize', BX.proxy(this.__expand_onresize, this));
	
		this.SETTINGS.resizable = this.__expand_settings.resizable;
		this.SETTINGS.draggable = this.__expand_settings.draggable;
		
		pDocElement.style.overflow = this.__expand_settings.overflow;
		
		this.DIV.style.top = this.__expand_settings.top;
		this.DIV.style.left = this.__expand_settings.left;
		this.DIV.style.width = this.__expand_settings.width;
		this.DIV.style.height = this.__expand_settings.height;
		
		pDocElement.scrollTop = this.__expand_settings.scroll;
		
		this.bExpanded = false;
		
		BX.onCustomEvent(this, 'onWindowNarrow');
		BX.onCustomEvent(this, 'onWindowResize');
		
	}
}

BX.CWindow.prototype.Resize = function(x, y)
{
	var new_width = Math.max(x - this.pos.left + this.dx, this.SETTINGS.min_width);
	var new_height = Math.max(y - this.pos.top + this.dy, this.SETTINGS.min_height);
	
	if (this.SETTINGS.resize_restrict)
	{
		var scrollSize = BX.GetWindowScrollSize();

		if (this.pos.left + new_width > scrollSize.scrollWidth - this.dw)
			new_width = scrollSize.scrollWidth - this.pos.left - this.dw;
	}
	
	this.DIV.style.width = new_width + 'px';
	this.DIV.style.height = new_height + 'px';
	
	BX.onCustomEvent(this, 'onWindowResize');
}

BX.CWindow.prototype.__startResize = function(e)
{
	if (!this.SETTINGS.resizable)
		return false;

	if(!e) e = window.event;
	
	this.wndSize = BX.GetWindowScrollPos();
	this.wndSize.innerWidth = BX.GetWindowInnerSize().innerWidth;
	
	this.pos = BX.pos(this.DIV);
	
	this.x = e.clientX + this.wndSize.scrollLeft;
	this.y = e.clientY + this.wndSize.scrollTop;
	
	this.dx = this.pos.left + this.pos.width - this.x;
	this.dy = this.pos.top + this.pos.height - this.y;
	this.dw = this.pos.width - parseInt(this.DIV.style.width);
	
	BX.bind(document, "mousemove", BX.proxy(this.__moveResize, this));
	BX.bind(document, "mouseup", BX.proxy(this.__stopResize, this));
	
	if(document.body.setCapture)
		document.body.setCapture();

	document.onmousedown = BX.False;
	
	var b = document.body;
	b.ondrag = b.onselectstart = BX.False;
	b.style.MozUserSelect = this.DIV.style.MozUserSelect = 'none';
	b.style.cursor = 'se-resize';
	
	BX.onCustomEvent(this, 'onWindowResizeStart');
}

BX.CWindow.prototype.__moveResize = function(e)
{
	if(!e) e = window.event;

	var windowScroll = BX.GetWindowScrollPos();	
	
	var x = e.clientX + windowScroll.scrollLeft;
	var y = e.clientY + windowScroll.scrollTop;

	if(this.x == x && this.y == y)
		return;

	this.Resize(x, y);
	
	this.x = x;
	this.y = y;
}

BX.CWindow.prototype.__stopResize = function()
{
	if(document.body.releaseCapture)
		document.body.releaseCapture();

	BX.unbind(document, "mousemove", BX.proxy(this.__moveResize, this));
	BX.unbind(document, "mouseup", BX.proxy(this.__stopResize, this));

	document.onmousedown = null;
	
	var b = document.body;
	b.ondrag = b.onselectstart = null;
	b.style.MozUserSelect = this.DIV.style.MozUserSelect = '';
	b.style.cursor = '';
	
	BX.onCustomEvent(this, 'onWindowResizeFinished')
}

BX.CWindow.prototype.SetClose = function(elem)
{
	BX.bind(elem, 'click', BX.proxy(this.Close, this));
	this.ELEMENTS.close.push(elem);
}

BX.CWindow.prototype.SetDraggable = function(elem)
{
	BX.bind(elem, 'mousedown', BX.proxy(this.__startDrag, this));
	
	elem.style.cursor = 'move';
	
	this.ELEMENTS.draggable.push(elem);
	this.SETTINGS.draggable = true;
}

BX.CWindow.prototype.Move = function(x, y)
{
	var dxShadow = 1; // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

	var left = parseInt(this.DIV.style.left)+x;
	var top = parseInt(this.DIV.style.top)+y;
	
	if (this.SETTINGS.drag_restrict)
	{
		//Left side
		if (left < 0)
			left = 0;

		//Right side
		var scrollSize = BX.GetWindowScrollSize();
		var floatWidth = this.DIV.offsetWidth;

		if (left > (scrollSize.scrollWidth - floatWidth - dxShadow))
			left = scrollSize.scrollWidth - floatWidth - dxShadow;

		//Top side
		if (top < 0)
			top = 0;
	}

	this.DIV.style.left = left+'px';
	this.DIV.style.top = top+'px';

	//this.AdjustShadow(div);
}

BX.CWindow.prototype.__startDrag = function(e)
{
	if (!this.SETTINGS.draggable)
		return false;

	if(!e) e = window.event;
	
	this.x = e.clientX + document.body.scrollLeft;
	this.y = e.clientY + document.body.scrollTop;
	
	BX.bind(document, "mousemove", BX.proxy(this.__moveDrag, this));
	BX.bind(document, "mouseup", BX.proxy(this.__stopDrag, this));
	
	if(document.body.setCapture)
		document.body.setCapture();

	document.onmousedown = BX.False;
	
	var b = document.body;
	b.ondrag = b.onselectstart = BX.False;
	b.style.MozUserSelect = this.DIV.style.MozUserSelect = 'none';
	b.style.cursor = 'move';
	
	BX.onCustomEvent(this, 'onWindowDragStart');
}

BX.CWindow.prototype.__moveDrag = function(e)
{
	if(!e) e = window.event;

	var x = e.clientX + document.body.scrollLeft;
	var y = e.clientY + document.body.scrollTop;

	if(this.x == x && this.y == y)
		return;

	this.Move((x - this.x), (y - this.y));
	this.x = x;
	this.y = y;
}

BX.CWindow.prototype.__stopDrag = function(e)
{
	if(document.body.releaseCapture)
		document.body.releaseCapture();

	BX.unbind(document, "mousemove", BX.proxy(this.__moveDrag, this));
	BX.unbind(document, "mouseup", BX.proxy(this.__stopDrag, this));

	document.onmousedown = null;
	
	var b = document.body;
	b.ondrag = b.onselectstart = null;
	b.style.MozUserSelect = this.DIV.style.MozUserSelect = '';
	b.style.cursor = '';
	
	BX.onCustomEvent(this, 'onWindowDragFinished');
}

BX.CWindow.prototype.AllowClose = function()
{
	
}

BX.CWindow.prototype.ShowError = function(str)
{
	alert(str);
}

/* dialog window class extends window class */
BX.CWindowDialog = function() {
	arguments[1] = 'dialog';
	BX.CWindowDialog.superclass.constructor.apply(this, arguments);
	
	this.DIV.style.top = '10px';
	this.OVERLAY = null;
}
BX.extend(BX.CWindowDialog, BX.CWindow);

BX.CWindowDialog.prototype.__resizeOverlay = function() 
{
	var windowSize = BX.GetWindowScrollSize();
	this.OVERLAY.style.width = windowSize.scrollWidth + "px";
}

BX.CWindowDialog.prototype.Show = function()
{
	BX.CWindowDialog.superclass.Show.apply(this, arguments);

	if (null == this.OVERLAY)
	{
		var windowSize = BX.GetWindowScrollSize();
		this.OVERLAY = document.body.appendChild(BX.create("DIV", {
			style: {
				position: 'absolute',
				top: '0px',
				left: '0px',
				zIndex: parseInt(this.DIV.style.zIndex)-1,
				width: windowSize.scrollWidth + "px",
				height: windowSize.scrollHeight + "px"
			}
		}));
	}
	else
	{
		this.OVERLAY.style.display = 'block';
		this.OVERLAY.style.zIndex = parseInt(this.DIV.style.zIndex)-1;
	}
	
	BX.unbind(window, 'resize', BX.proxy(this.__resizeOverlay, this));
	BX.bind(window, 'resize', BX.proxy(this.__resizeOverlay, this));
}

BX.CWindowDialog.prototype.onUnRegister = function(clean)
{
	BX.CWindowDialog.superclass.onUnRegister.apply(this, arguments);
	
	if (this.clean)
	{
		if (this.OVERLAY.parentNode)
			this.OVERLAY.parentNode.removeChild(this.OVERLAY);
	}
	else
	{
		this.OVERLAY.style.display = 'none';
	}
	
	BX.unbind(window, 'resize', BX.proxy(this.__resizeOverlay, this));
}

/* standard bitrix dialog extends BX.CWindowDialog */
/*
	arParams = {
		(
			title: 'dialog title',
			head: 'head block html',
			content: 'dialog content',
			icon: 'head icon classname or filename',
			
			resize_id: 'some id to save resize information'// useless if resizable = false
		)
		or
		(
			content_url: url to content load
				loaded content scripts can use BX.WindowManager.Get() to get access to the current window object
		)
		
		height: window_height_in_pixels,
		width: window_width_in_pixels,
		
		draggable: true|false,
		resizable: true|false,
		
		min_height: min_window_height_in_pixels, // useless if resizable = false
		min_width: min_window_width_in_pixels, // useless if resizable = false
		
		buttons: [
			'html_code',
			BX.CDialog.btnSave, BX.CDialog.btnCancel, BX.CDialog.btnClose
		]
	}
*/
BX.CDialog = function(arParams)
{
	BX.CDialog.superclass.constructor.apply(this);
	
	this.PARAMS = arParams || {};
	
	for (var i in this.defaultParams)
	{
		if (typeof this.PARAMS[i] == 'undefined')
			this.PARAMS[i] = this.defaultParams[i];
	}
	
	this.PARAMS.width = this.PARAMS.width ? this.PARAMS.width : this.defaultParams['width'];
	this.PARAMS.height = this.PARAMS.height ? this.PARAMS.height : this.defaultParams['height'];
	
	if (this.PARAMS.resize_id || this.PARAMS.content_url)
	{
		var arSize = BX.WindowManager.getRuntimeWindowSize(this.PARAMS.resize_id || this.PARAMS.content_url);
		if (arSize)
		{
			this.PARAMS.width = arSize.width;
			this.PARAMS.height = arSize.height;
		}
	}
	
	BX.adjust(this.DIV, {
		style: {
			height: this.PARAMS.height + 'px',
			width: this.PARAMS.width + 'px'
		}
	});
	BX.addClass(this.DIV, 'bx-core-dialog');
	
	this.PARTS = {};
	
	this.PARTS.TITLEBAR_ICONS = this.DIV.appendChild(BX.create('DIV', {
		props: {
			className: 'bx-core-dialog-titlebar-icons'
		},
		children: (this.PARAMS.resizable ? [
			BX.create('DIV', {props: {className: 'bx-icon-close', title: BX.message('JS_CORE_WINDOW_CLOSE')}}),
			BX.create('DIV', {props: {className: 'bx-icon-expand', title: BX.message('JS_CORE_WINDOW_EXPAND')}})
		] : [
			BX.create('DIV', {props: {className: 'bx-icon-close', title: BX.message('JS_CORE_WINDOW_CLOSE')}})
		])
	}));
	
	this.SetClose(this.PARTS.TITLEBAR_ICONS.firstChild);
	
	if (this.PARAMS.resizable)
	{
		this.SetExpand(this.PARTS.TITLEBAR_ICONS.lastChild);
		BX.addCustomEvent(this, 'onWindowExpand', BX.proxy(this.__onexpand, this));
		BX.addCustomEvent(this, 'onWindowNarrow', BX.proxy(this.__onexpand, this));
	}
	
	
	this.PARTS.TITLEBAR = this.DIV.appendChild(BX.create('DIV', {
		props: {
			className: 'bx-core-dialog-titlebar'
		},
		children: [
			BX.create('DIV', {
				props: {
					className: 'bx-core-dialog-titlebar-text'
				},
				text: this.PARAMS.title,
				html: '&nbsp'
			})
		]
	}));

	
	// if (this.PARAMS.head || this.PARAMS.content_url)
	// {
		this.PARTS.HEAD = this.DIV.appendChild(BX.create('DIV', {
			props: {
				className: 'bx-core-dialog-head'
			},
			children: [
				BX.create('DIV', {
					props: {className: 'bx-core-dialog-head-content' + (this.PARAMS.icon ? ' ' + this.PARAMS.icon : '')},
					html: this.PARAMS.head || '&nbsp;'
				})
			]
		}));
		
		if (!this.PARAMS.head)
			this.PARTS.HEAD.style.display = 'none';
	// }
	
	if (true || this.PARAMS.buttons)
	{
		//this.PARAMS.buttons = [BX.CDialog.btnSave, BX.CDialog.btnCancel];
	
		this.PARTS.FOOT = this.DIV.appendChild(BX.create('DIV', {
			props: {className: 'bx-core-dialog-foot'},
			children: this.ShowButtons()
		}));
	}
	
	this.PARTS.CONTENT = this.DIV.appendChild(BX.create('DIV', {
		props: {className: 'bx-core-dialog-content'},
		style: {height: '0px'}
	}));
	
	// shut up! dont tell me what youre thinkin bout!
	if (BX.browser.IsIE())
	{
		this.PARTS.CONTENT_DATA = this.PARTS.CONTENT.appendChild(BX.create('TABLE', {attrs: {cellSpacing: '5', width: '100%'}, style: {'borderCollapse': 'collapse'}})).insertRow(-1).insertCell(-1);
		this.PARTS.CONTENT_DATA.style.verticalAlign = 'top';
		this.PARTS.CONTENT_DATA.className = 'bx-core-dialog-padding';
	}
	else
	{
		this.PARTS.CONTENT_DATA = this.PARTS.CONTENT.appendChild(BX.create('DIV', {style: {padding: '5px'}}));
	}
	
	if (this.PARAMS.content)
		this.PARTS.CONTENT_DATA.innerHTML = this.PARAMS.content;
	
	if (this.PARAMS.draggable)
		this.SetDraggable(this.PARTS.TITLEBAR.firstChild);
	
	this.SetExpand(this.PARTS.TITLEBAR.firstChild, 'dblclick');

	if (this.PARAMS.resizable)
	{
		this.PARTS.RESIZER = this.DIV.appendChild(BX.create('DIV', {
			props: {className: 'bx-core-resizer'}
		}));
		
		this.SetResize(this.PARTS.RESIZER);
		
		this.SETTINGS.min_width = this.PARAMS.min_width;
		this.SETTINGS.min_height = this.PARAMS.min_height;
	}
}
BX.extend(BX.CDialog, BX.CWindowDialog);

BX.CDialog.prototype.__onexpand = function()
{
	var ob = this.PARTS.TITLEBAR_ICONS.lastChild;
	ob.className = BX.toggle(ob.className, ['bx-icon-expand', 'bx-icon-narrow']);
	ob.title = BX.toggle(ob.title, [BX.message('JS_CORE_WINDOW_EXPAND'), BX.message('JS_CORE_WINDOW_NARROW')]);
	
	if (this.PARTS.RESIZER)
	{
		this.PARTS.RESIZER.style.display = this.bExpanded ? 'none' : 'block';
	}
}

BX.CDialog.prototype.defaultParams = {
	width: 700,
	height: 500,
	min_width: 250,
	min_height: 200,
	
	resizable: true,
	draggable: true,
	
	title: '',
	icon: ''
}

BX.CDialog.prototype.GetParameters = function(form_name)
{
	var form = this.PARTS.CONTENT_DATA.getElementsByTagName('FORM');
	if (form) form = form[0];
	
	if(!form)
		return "";

	var i, s = "";
	var n = form.elements.length;

	var delim = '';
	for(i=0; i<n; i++)
	{
		if (s != '') delim = '&';
		var el = form.elements[i];
		if (el.disabled)
			continue;

		switch(el.type.toLowerCase())
		{
			case 'text':
			case 'textarea':
			case 'password':
			case 'hidden':
				if (null == form_name && el.name.substr(el.name.length-4) == '_alt' && form.elements[el.name.substr(0, el.name.length-4)])
					break;
				s += delim + el.name + '=' + BX.util.urlencode(el.value);
				break;
			case 'radio':
				if(el.checked)
					s += delim + el.name + '=' + BX.util.urlencode(el.value);
				break;
			case 'checkbox':
				s += delim + el.name + '=' + BX.util.urlencode(el.checked ? 'Y':'N');
				break;
			case 'select-one':
				var val = "";
				if (null == form_name && form.elements[el.name + '_alt'] && el.selectedIndex == 0)
					val = form.elements[el.name+'_alt'].value;
				else
					val = el.value;
				s += delim + el.name + '=' + BX.util.urlencode(val);
				break;
			case 'select-multiple':
				var j, bAdded = false;
				var l = el.options.length;
				for (j=0; j<l; j++)
				{
					if (el.options[j].selected)
					{
						s += delim + el.name + '=' + BX.util.urlencode(el.options[j].value);
						bAdded = true;
					}
				}
				if (!bAdded)
					s += delim + el.name + '=';
				break;
			default:
				break;
		}
	}

	return s;
};

BX.CDialog.prototype.PostParameters = function(params)
{
	var url = this.PARAMS.content_url;
	if (null != params)
	{
		index = url.indexOf('?')
		if (index == -1)
			url += '?' + params;
		else
			url = url.substring(0, index) + '?' + params + "&" + url.substring(index+1);
	}
	
	BX.showWait();
	BX.ajax.post(url, this.GetParameters(), BX.delegate(function(result) {
		BX.closeWait();
		this.ClearButtons(); // buttons are appended during form reload, so we should clear footer
		this.SetContent(result);
		this.Show(true);
	}, this));
}

BX.CDialog.prototype.Submit = function(params)
{
	var FORM = this.GetForm();
	if (FORM)
	{
		if (!FORM.method) FORM.method = 'POST';
		if (!FORM.action) 
		{
			var url = this.PARAMS.content_url;
			if (null != params)
			{
				index = url.indexOf('?')
				if (index == -1)
					url += '?' + params;
				else
					url = url.substring(0, index) + '?' + params + "&" + url.substring(index+1);
			}

			FORM.action = url;
		}
		
		BX.ajax.submit(FORM);
	}
	else
	{
		alert('no form registered!');
	}
}

BX.CDialog.prototype.GetForm = function()
{
	if (null == this.__form)
	{
		var forms = this.PARTS.CONTENT_DATA.getElementsByTagName('FORM');
		this.__form = forms[0] ? forms[0] : null;
	}
	
	return this.__form;
}

BX.CDialog.prototype._checkButton = function(btn)
{
	var arCustomButtons = ['btnSave', 'btnCancel', 'btnClose'];
	
	for (var i = 0; i < arCustomButtons.length; i++)
	{
		if (this[arCustomButtons[i]] && (btn == this[arCustomButtons[i]]))
			return arCustomButtons[i];
	}
	
	return false;
}

BX.CDialog.prototype.ShowButtons = function()
{
	var result = [];
	if (this.PARAMS.buttons)
	{
		if (this.PARAMS.buttons.title) this.PARAMS.buttons = [this.PARAMS.buttons];
		
		for (var i=0, len=this.PARAMS.buttons.length; i<len; i++)
		{
			if (BX.type.isNotEmptyString(this.PARAMS.buttons[i]))
			{
				result.push(this.PARAMS.buttons[i]);
			}
			else if (this.PARAMS.buttons[i])
			{
				//if (!(this.PARAMS.buttons[i] instanceof BX.CWindowButton))
				if (null == this.PARAMS.buttons[i].Button)
				{
					var b = this._checkButton(this.PARAMS.buttons[i]); // hack to set links to real CWindowButton object in btnSave etc;
					this.PARAMS.buttons[i] = new BX.CWindowButton(this.PARAMS.buttons[i]);
					if (b) this[b] = this.PARAMS.buttons[i];
				}
					
				result.push(this.PARAMS.buttons[i].Button(this));
			}
		}
	}
	
	return result;
}

BX.CDialog.prototype.SetTitle = function(title)
{
	this.PARAMS.title = title;
	BX.cleanNode(this.PARTS.TITLEBAR.firstChild).appendChild(document.createTextNode(this.PARAMS.title));
}

BX.CDialog.prototype.SetHead = function(head)
{
	this.PARAMS.head = head;
	this.PARTS.HEAD.firstChild.innerHTML = head || "&nbsp;";
	
	this.PARTS.HEAD.style.display = this.PARAMS.head ? 'block' : 'none';
	this.adjustSize();
}

BX.CDialog.prototype.__adjustHeadToIcon = function()
{
	if (!this.PARTS.HEAD.firstChild.offsetHeight)
	{
		setTimeout(BX.delegate(this.__adjustHeadToIcon, this), 50);
	}
	else
	{
		if (this.icon_image && this.icon_image.height && this.icon_image.height > this.PARTS.HEAD.firstChild.offsetHeight - 5)
		{
			this.PARTS.HEAD.firstChild.style.height = this.icon_image.height + 5 + 'px';
			this.adjustSize();
		}
		
		this.icon_image.onload = null;
		this.icon_image = null;
	}
}

BX.CDialog.prototype.SetIcon = function(icon_class)
{
	if (this.PARAMS.icon != icon_class)
	{
		if (this.PARAMS.icon)
			BX.removeClass(this.PARTS.HEAD.firstChild, this.PARAMS.icon);
		
		this.PARAMS.icon = icon_class
		
		if (this.PARAMS.icon)
		{
			BX.addClass(this.PARTS.HEAD.firstChild, this.PARAMS.icon);
			
			var icon_file = (BX.style(this.PARTS.HEAD.firstChild, 'background-image') || BX.style(this.PARTS.HEAD.firstChild, 'backgroundImage')).replace('url("', '').replace('")', '');
			if (BX.type.isNotEmptyString(icon_file) && icon_file != 'none')
			{
				this.icon_image = new Image();
				this.icon_image.onload = BX.delegate(this.__adjustHeadToIcon, this);
				this.icon_image.src = icon_file;
			}
		}
	}
	this.adjustSize();
}

BX.CDialog.prototype.SetIconFile = function(icon_file)
{
	this.icon_image = new Image();
	this.icon_image.onload = BX.delegate(this.__adjustHeadToIcon, this);
	this.icon_image.src = icon_file;
	
	BX.adjust(this.PARTS.HEAD.firstChild, {style: {backgroundImage: 'url(' + icon_file + ')', backgroundPosition: '99% 5px'}});
	this.adjustSize();
}

/*
BUTTON: {
	title: 'title',
	'action': function executed in window object context
}
BX.CDialog.btnSave || BX.CDialog.btnCancel - standard buttons
*/

BX.CDialog.prototype.SetButtons = function(a)
{
	if (BX.type.isString(a))
	{
		if (a.length > 0)
			this.PARTS.FOOT.innerHTML += a;
	}
	else
	{
		this.PARAMS.buttons = a;
		BX.adjust(this.PARTS.FOOT, {children: this.ShowButtons()});
	}
	this.adjustSize();
}

BX.CDialog.prototype.ClearButtons = function()
{
	BX.cleanNode(this.PARTS.FOOT);
	this.adjustSize();
}

BX.CDialog.prototype.SetContent = function(html)
{
	this.PARAMS.content = html;
	this.PARTS.CONTENT_DATA.innerHTML = html || '&nbsp;';
}

BX.CDialog.prototype.SwapContent = function(cont)
{
	cont = BX(cont);
	
	BX.cleanNode(this.PARTS.CONTENT_DATA);
	cont.parentNode.removeChild(cont);
	this.PARTS.CONTENT_DATA.appendChild(cont);
	cont.style.display = 'block';
	this.SetContent(cont.innerHTML);
}

BX.CDialog.prototype.adjustSize = function()
{
	setTimeout(BX.delegate(this.__adjustSize, this), 10);
}

BX.CDialog.prototype.__adjustSize = function()
{
	var height = 
		parseInt(this.DIV.style.height) - 2 // strange bug with offsetHeight in IE while expanding
		- (this.PARTS.TITLEBAR.offsetHeight + 1) 
		- (this.PARTS.HEAD ? this.PARTS.HEAD.offsetHeight + 10 : 0) 
		- (this.PARTS.FOOT ? this.PARTS.FOOT.offsetHeight : 0);
		
	this.PARTS.CONTENT.style.height = height + 'px';
	
	// autotraing: calm down, max! calm down....
	if (BX.browser.IsIE())
	{
		var width_tmp = this.PARTS.CONTENT.style.width;
		this.PARTS.CONTENT.style.width = '0px';
		var width = this.DIV.offsetWidth-12;
		this.PARTS.CONTENT.style.width = width > 0 ? (width + 'px') : width_tmp;
	}

	// TODO: optimize it!
	BX.onCustomEvent(this, 'onWindowResizeExt', [{'height': height, 'width': this.PARTS.CONTENT.offsetWidth-(BX.browser.IsIE() ? -5 : 2)}]);
}

BX.CDialog.prototype.adjustSizeEx = function()
{
	setTimeout(BX.delegate(this.__adjustSizeEx, this), 10);
}

BX.CDialog.prototype.__adjustSizeEx = function()
{
	var arMargins = [10, parseInt(BX.style(this.PARTS.CONTENT, 'top')), parseInt(BX.style(this.PARTS.CONTENT.firstChild, 'margin-top')), parseInt(BX.style(this.PARTS.CONTENT.firstChild, 'margin-bottom'))];
	if (BX.browser.IsIE()) arMargins[0] += 5;
	var margins = 0;
	for (var i=0; i < arMargins.length; i++)
		if (!isNaN(arMargins[i]))
			margins += arMargins[i];

	var height = this.PARTS.CONTENT.firstChild.offsetHeight 
		+ margins
		+ this.PARTS.TITLEBAR.offsetHeight 
		+ (this.PARTS.FOOT ? this.PARTS.FOOT.offsetHeight : 0)
		+ (this.PARTS.HEAD ? this.PARTS.HEAD.offsetHeight : 0);
	
	this.DIV.style.height = height + 'px';
	this.adjustSize();
}


BX.CDialog.prototype.__onResizeFinished = function()
{
	BX.WindowManager.saveWindowSize(
		this.PARAMS.resize_id || this.PARAMS.content_url, {height: parseInt(this.DIV.style.height), width: parseInt(this.DIV.style.width)}
	);
}

BX.CDialog.prototype.Show = function(bNotRegister)
{
	if ((!this.PARAMS.content) && this.PARAMS.content_url && BX.ajax && !bNotRegister)
	{
		var wait = BX.showWait();
		BX.WindowManager.currently_loaded = this;
		
		var post_data = '', method = 'get';
		if (this.PARAMS.content_post)
		{
			post_data = this.PARAMS.content_post;
			method = 'post';
		}
		
		BX.ajax[method](this.PARAMS.content_url, post_data, BX.delegate(function(data) {
			BX.closeWait(null, wait);
			
			this.SetContent(data || '&nbsp;');
			this.Show();
		}, this));
	}
	else
	{
		BX.WindowManager.currently_loaded = null;
		BX.CDialog.superclass.Show.apply(this, arguments);

		this.adjustPos();

		this.OVERLAY.className = 'bx-core-dialog-overlay';

		this.__adjustSize();
		BX.addCustomEvent(this, 'onWindowResize', BX.proxy(this.__adjustSize, this))
		
		if (this.PARAMS.resizable && (this.PARAMS.content_url || this.PARAMS.resize_id))
			BX.addCustomEvent(this, 'onWindowResizeFinished', BX.delegate(this.__onResizeFinished, this));
	}
}

BX.CDialog.prototype.adjustPos = function()
{
	if (!this.bExpanded)
	{
		var windowSize = BX.GetWindowInnerSize();
		var windowScroll = BX.GetWindowScrollPos();
		
		BX.adjust(this.DIV, {
			style: {
				left: parseInt(windowScroll.scrollLeft + windowSize.innerWidth / 2 - parseInt(this.DIV.style.width) / 2) + 'px',
				top: Math.max(parseInt(windowScroll.scrollTop + windowSize.innerHeight / 2 - parseInt(this.DIV.style.height) / 2), 0) + 'px'
			}
		});
	}
}

BX.CDialog.prototype.GetContent = function () {return this.PARTS.CONTENT_DATA};

BX.CDialog.prototype.btnSave = BX.CDialog.btnSave = {
	title: BX.message('JS_CORE_WINDOW_SAVE'),
	id: 'savebtn',
	name: 'savebtn',
	action: function () {
		this.parentWindow.PostParameters();
	}
};

BX.CDialog.prototype.btnCancel = BX.CDialog.btnCancel = {
	title: BX.message('JS_CORE_WINDOW_CANCEL'),
	id: 'cancel',
	name: 'cancel',
	action: function () {
		this.parentWindow.Close();
	}
};

BX.CDialog.prototype.btnClose = BX.CDialog.btnClose = {
	title: BX.message('JS_CORE_WINDOW_CLOSE'),
	id: 'close',
	name: 'close',
	action: function () {
		this.parentWindow.Close();
	}
};

/* special child for admin forms loaded into public page */
BX.CAdminDialog = function(arParams)
{
	BX.CAdminDialog.superclass.constructor.apply(this, arguments);
	
	if (BX.browser.IsIE())
		BX.adjust(this.PARTS.CONTENT_DATA.parentNode.parentNode.parentNode, {attrs: {cellSpacing: 0, cellPadding: 0, border: 0}});
	else
		BX.adjust(this.PARTS.CONTENT_DATA, {style: {padding: '0px'}});
	
	this.PARTS.HEAD.className = 'bx-core-admin-dialog-head';
	this.PARTS.CONTENT.className = 'bx-core-admin-dialog-content';
}
BX.extend(BX.CAdminDialog, BX.CDialog);

BX.CAdminDialog.prototype.SetHead = function()
{
	BX.CAdminDialog.superclass.SetHead.apply(this, arguments);
	
	if (this.PARTS.HEAD.firstChild.firstChild && BX.type.isElementNode(this.PARTS.HEAD.firstChild.firstChild) && this.PARTS.HEAD.firstChild.firstChild.tagName.toUpperCase() == 'TABLE')
	{
		var arHeadCells = this.PARTS.HEAD.firstChild.firstChild.tBodies[0].rows[0].cells;
		var last_cell = arHeadCells[arHeadCells.length-1];
		
		this.SETTINGS.min_width = Math.max(this.PARTS.HEAD.firstChild.offsetWidth - last_cell.offsetWidth+10, this.SETTINGS.min_width); // 10 - vert scrollbar
	}
}

BX.CAdminDialog.prototype.__adjustSize = function()
{
	BX.CAdminDialog.superclass.__adjustSize.apply(this, arguments);

	if (BX.browser.IsIE() && this.DIV.offsetWidth > 3)
	{
		this.PARTS.CONTENT.style.width = '0px';
		var width = this.DIV.offsetWidth-3;
		this.PARTS.CONTENT.style.width = width + 'px';
	}
}

BX.CAdminDialog.prototype.btnSave = BX.CAdminDialog.btnSave = {
	title: BX.message('JS_CORE_WINDOW_SAVE'),
	id: 'savebtn',
	name: 'savebtn',
	action: function () {
		this.parentWindow.Submit();
	}
};

BX.CAdminDialog.btnCancel = BX.CAdminDialog.superclass.btnCancel;
BX.CAdminDialog.btnClose = BX.CAdminDialog.superclass.btnClose;

BX.CDebugDialog = function(arParams)
{
	BX.CDebugDialog.superclass.constructor.apply(this, arguments);
}
BX.extend(BX.CDebugDialog, BX.CDialog);

BX.CDebugDialog.prototype.Show = function()
{
	BX.CDebugDialog.superclass.Show.apply(this, arguments);
	
	if (BX.browser.IsIE())
	{
		BX.cleanNode(this.PARTS.CONTENT_DATA).style.padding = '0px !important';
		this.PARTS.CONTENT_BOTTOM = this.PARTS.CONTENT_DATA.appendChild(BX.create('DIV', {style: {
			width: '100%', overflow: 'auto'
		}, html: this.PARAMS.content}));

		BX.adjust(this.PARTS.CONTENT_DATA.parentNode.parentNode.parentNode, {
			style: {marginTop: this.PARTS.CONTENT_TOP ? this.PARTS.CONTENT_TOP.style.height : '0px'},
			attrs: {cellSpacing: '0px', cellPadding: '0px'}
		});
		
		BX.adjust(this.PARTS.CONTENT_DATA, {style: {
			padding: '0px', backgroundImage: 'none'
		}});
		
		this.__adjustSize();
	}
	else
	{
		if (this.PARTS.CONTENT_TOP)
			this.PARTS.CONTENT_DATA.style.marginTop = this.PARTS.CONTENT_TOP.style.height;
		
		this.PARTS.CONTENT_DATA.style.overflow = 'auto';
	}
}

BX.CDebugDialog.prototype.ShowDetails = function(div_id)
{
	var div = BX(div_id);
	if (div)
	{
		if (this.div_detail_current)
			this.div_detail_current.style.display = 'none';

		div.style.display = 'block';
		this.div_detail_current = div;
	}
}

BX.CDebugDialog.prototype.SetContent = function(html)
{
	var arHtml = html.split('#DIVIDER#');
	if (arHtml.length > 1)
	{
		this.PARAMS.content = arHtml[1];
		
		this.PARTS.CONTENT.style.overflow = 'hidden';
		this.PARTS.CONTENT_TOP = this.PARTS.CONTENT.appendChild(BX.create('DIV', {
			style: {
				position: 'absolute',
				top: '0px',
				left: '0px',
				width: '100%',
				height: '150px',
				overflow: 'auto',
				borderBottom: '1px solid #B8C1DD'
			},
			html: arHtml[0]
		}));
		
		BX.CDebugDialog.superclass.SetContent.apply(this, [arHtml[1]]);
	}
	else
	{
		BX.CDebugDialog.superclass.SetContent.apply(this, arguments);
	}
}

BX.CDebugDialog.prototype.__adjustSize = function()
{
	BX.CDebugDialog.superclass.__adjustSize.apply(this, arguments);
	
	if (this.PARTS.CONTENT_TOP)
	{
		var new_height = this.PARTS.CONTENT.offsetHeight - this.PARTS.CONTENT_TOP.offsetHeight;

		if (new_height > 0)
		{
			if (this.PARTS.CONTENT_BOTTOM)
				this.PARTS.CONTENT_BOTTOM.style.height = new_height + 'px';
			else
				new_height -= 10;

			this.PARTS.CONTENT_DATA.style.height = new_height + 'px';
			
		}
	}
}