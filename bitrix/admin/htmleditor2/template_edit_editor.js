var templatePreparseHeaders = function()
{
	var TA = BX('CONTENT');
	if (TA)
		GLOBAL_pMainObj['CONTENT'].pParser.GetHBF(TA.value, false);
}
jsUtils.addCustomEvent('EditorLoadFinish_CONTENT', templatePreparseHeaders)

window.fullEditMode = true;

var edit_hbf = [ //hbf - head, body, footer
	'BXButton', 
	{
		id : 'edit_hbf',
		iconkit : '_global_iconkit.gif',
		name : TE_MESS.FILEMAN_EDIT_HBF,
		handler : function ()
		{
			this.bNotFocus = true;
			this.pMainObj.OpenEditorDialog("edit_hbf", false, 700, {bUseTabControl: true});
		}
	}
];

var insert_wa = [
	'BXButton', 
	{
		id : 'insert_wa',
		iconkit : '_global_iconkit.gif',
		name : TE_MESS.FILEMAN_INSERT_WA,
		handler : function () {this.pMainObj.insertHTML('<IMG src="/bitrix/images/fileman/htmledit2/work_area.gif"  __bxtagname="work_area"/>');}
	}
];

var preview_tmpl = [
	'BXButton', 
	{
		id : 'preview_tmpl',
		iconkit : '_global_iconkit.gif',
		name : TE_MESS.FILEMAN_PREVIEW_TEMPLATE,
		title : TE_MESS.FILEMAN_PREVIEW_TEMPLATE_TITLE,
		handler : function () {preview_template(__ID);}
	}
];

arToolbars['style'] = [
	BX_MESS.TBSStyle,
		[arButtons['FontStyle'], arButtons['HeadingList'], arButtons['FontName'], arButtons['FontSize'], arButtons['separator'],
			arButtons['Bold'], arButtons['Italic'], arButtons['Underline'], 'separator',
			arButtons['RemoveFormat']
		]
];

var edit_template = oBXEditorUtils.createToolbar("edit_template", TE_MESS.FILEMAN_TOOLBAR_TITLE, [edit_hbf, insert_wa, preview_tmpl]);
oBXEditorUtils.addToolbar(edit_template);

function ReplaceSiteTemplatePath(str)
{
	var replace = function(str, b1, b2, b3_src, b4)
	{
		if (str.indexOf('__bxtagname') != -1 || b3_src.indexOf('SITE_TEMPLATE_PATH') == -1)
			return str;
		
		b3_src = b3_src.replace(/<\?=\s*SITE_TEMPLATE_PATH;?\s*\?>/i, SITE_TEMPLATE_PATH);
		b3_src = b3_src.replace(/<\?\s*echo\s*SITE_TEMPLATE_PATH;?\s*\?>/i, SITE_TEMPLATE_PATH);
		
		b1 = b1.replace(/__bxsrc\s*=\s*("|')((?:(?:\s|\S)*?[^\\]{1}){1})\1/i, '__bxsrc="' + b3_src + '"');
		b4 = b4.replace(/__bxsrc\s*=\s*("|')((?:(?:\s|\S)*?[^\\]{1}){1})\1/i, '__bxsrc="' + b3_src + '"');
		return b1 + b2 + b3_src + b2 + b4;
	};
	str = str.replace(/(<img(?:\s|\S)*?\s*src\s*=\s*)("|')((?:(?:\s|\S)*?[^\\]{1}){1})\2((?:(?:\s|\S)*?[^\?]{1})??>)/ig, replace);
	return str;
}

function TemplateEditContentParser(str)
{
	str = str.replace(/#WORK_AREA#/ig, '<IMG src="/bitrix/images/fileman/htmledit2/work_area.gif" __bxtagname="work_area"/>');
	str = ReplaceSiteTemplatePath(str);
	return str;
}
oBXEditorUtils.addContentParser(TemplateEditContentParser);


function TemplateEditNodeUnParser(node, pMainObj)
{
	if (node.type != 'element' || node.text != 'img')
		return;
	
	var src = node.arAttributes.__bxsrc;
	var len = SITE_TEMPLATE_PATH.length;
	
	if (src.substr(0, len) != SITE_TEMPLATE_PATH)
		return;

	var new_src = '<?=SITE_TEMPLATE_PATH?>' + src.substr(len);
	delete node.arAttributes.__bxsrc;
	node.arAttributes.src = new_src;
	node.bDontUseSpecialchars = true;
}
oBXEditorUtils.addNodeUnParser(TemplateEditNodeUnParser);

function TemplateEditUnParser(node)
{
	if (node.arAttributes["__bxtagname"] == 'work_area')
		return '#WORK_AREA#';
	return false;
}
oBXEditorUtils.addUnParser(TemplateEditUnParser);