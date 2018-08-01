function BxInterfaceForm(name, aTabs)
{
	var _this = this;
	this.name = name;
	this.aTabs = aTabs;
	this.bExpandTabs = false;
	this.vars = {};
	
	this.SelectTab = function(tab_id)
	{
		var div = document.getElementById('inner_tab_'+tab_id);
		if(div.style.display != 'none')
			return;

		for (var i = 0, cnt = this.aTabs.length; i < cnt; i++)
		{
			var tab = document.getElementById('inner_tab_'+this.aTabs[i]);
			if(tab.style.display != 'none')
			{
				this.ShowTab(this.aTabs[i], false);
				tab.style.display = 'none';
				break;
			}
		}

		this.ShowTab(tab_id, true);
		div.style.display = 'block';

		document.getElementById(this.name+'_active_tab').value = tab_id;
	}

	this.ShowTab = function(tab_id, on)
	{
		var sel = (on? '-selected':'');
		document.getElementById('tab_cont_'+tab_id).className = 'bx-tab-container'+sel;
		document.getElementById('tab_left_'+tab_id).className = 'bx-tab-left'+sel;
		document.getElementById('tab_'+tab_id).className = 'bx-tab'+sel;
		document.getElementById('tab_right_'+tab_id).className = 'bx-tab-right'+sel;
	}

	this.HoverTab = function(tab_id, on)
	{
		var tab = document.getElementById('tab_'+tab_id);
		if(tab.className == 'bx-tab-selected')
			return;

		document.getElementById('tab_left_'+tab_id).className = (on? 'bx-tab-left-hover':'bx-tab-left');
		tab.className = (on? 'bx-tab-hover':'bx-tab');
		var tab_right = document.getElementById('tab_right_'+tab_id);
		tab_right.className = (on? 'bx-tab-right-hover':'bx-tab-right');
	}

	this.ShowDisabledTab = function(tab_id, disabled)
	{
		var tab = document.getElementById('tab_cont_'+tab_id);
		if(disabled)
		{
			tab.className = 'bx-tab-container-disabled';
			tab.onclick = null;
			tab.onmouseover = null;
			tab.onmouseout = null;
		}
		else
		{
			tab.className = 'bx-tab-container';
			tab.onclick = function(){_this.SelectTab(tab_id);};
			tab.onmouseover = function(){_this.HoverTab(tab_id, true);};
			tab.onmouseout = function(){_this.HoverTab(tab_id, false);};
		}
	}

	this.ToggleTabs = function()
	{
		this.bExpandTabs = !this.bExpandTabs;

		var a = document.getElementById('bxForm_'+this.name+'_expand_link');
		a.title = (this.bExpandTabs? this.vars.mess.collapseTabs : this.vars.mess.expandTabs);
		a.className = (this.bExpandTabs? a.className.replace(/\s*bx-down/ig, ' bx-up') : a.className.replace(/\s*bx-up/ig, ' bx-down'));

		for(var i in this.aTabs)
		{
			var tab_id = this.aTabs[i];
			this.ShowTab(tab_id, false);
			this.ShowDisabledTab(tab_id, this.bExpandTabs);
			var div = document.getElementById('inner_tab_'+tab_id);
			div.style.display = (this.bExpandTabs? 'block':'none');
		}
		if(!this.bExpandTabs)
		{
			this.ShowTab(this.aTabs[0], true);
			var div = document.getElementById('inner_tab_'+this.aTabs[0]);
			div.style.display = 'block';
		}
//		jsUserOptions.SaveOption('edit', this.unique_name, 'expand', (this.bExpandTabs? 'on': 'off'));
	}
}