var B_open = 0;
var I_open = 0;
var U_open = 0;
var QUOTE_open = 0;
var CODE_open = 0;

var text_enter_url      = "<?echo GetMessage("FORUM_TEXT_ENTER_URL");?>";
var text_enter_url_name = "<?echo GetMessage("FORUM_TEXT_ENTER_URL_NAME");?>";
var text_enter_image    = "<?echo GetMessage("FORUM_TEXT_ENTER_IMAGE");?>";
var list_prompt         = "<?echo GetMessage("FORUM_LIST_PROMPT");?>";
var error_no_url   = "<?echo GetMessage("FORUM_ERROR_NO_URL");?>";
var error_no_title = "<?echo GetMessage("FORUM_ERROR_NO_TITLE");?>";

var help_bold        = "<?echo GetMessage("FORUM_HELP_BOLD");?>";
var help_italic      = "<?echo GetMessage("FORUM_HELP_ITALIC");?>";
var help_under       = "<?echo GetMessage("FORUM_HELP_UNDER");?>";
var help_font        = "<?echo GetMessage("FORUM_HELP_FONT");?>";
var help_color       = "<?echo GetMessage("FORUM_HELP_COLOR");?>";
var help_close       = "<?echo GetMessage("FORUM_HELP_CLOSE");?>";
var help_url         = "<?echo GetMessage("FORUM_HELP_URL");?>";
var help_img         = "<?echo GetMessage("FORUM_HELP_IMG");?>";
var help_quote       = "<?echo GetMessage("FORUM_HELP_QUOTE");?>";
var help_list        = "<?echo GetMessage("FORUM_HELP_LIST");?>";
var help_code        = "<?echo GetMessage("FORUM_HELP_CODE");?>";
var help_click_close = "<?echo GetMessage("FORUM_HELP_CLICK_CLOSE");?>";
var help_translit    = "<?echo GetMessage("FORUM_HELP_TRANSLIT");?>";

var bbtags = new Array();

var myAgent   = navigator.userAgent.toLowerCase();
var myVersion = parseInt(navigator.appVersion);

var is_ie  = ((myAgent.indexOf("msie") != -1)  && (myAgent.indexOf("opera") == -1));
var is_nav = ((myAgent.indexOf('mozilla')!=-1) && (myAgent.indexOf('spoofer')==-1)
					&& (myAgent.indexOf('compatible') == -1) && (myAgent.indexOf('opera')==-1)
					&& (myAgent.indexOf('webtv') ==-1)       && (myAgent.indexOf('hotjava')==-1));

var is_win = ((myAgent.indexOf("win")!=-1) || (myAgent.indexOf("16bit")!=-1));
var is_mac = (myAgent.indexOf("mac")!=-1);

var smallEngLettersReg = new Array(/e'/g, /ch/g, /sh/g, /yo/g, /jo/g, /zh/g, /yu/g, /ju/g, /ya/g, /ja/g, /a/g, /b/g, /v/g, /g/g, /d/g, /e/g, /z/g, /i/g, /j/g, /k/g, /l/g, /m/g, /n/g, /o/g, /p/g, /r/g, /s/g, /t/g, /u/g, /f/g, /h/g, /c/g, /w/g, /~/g, /y/g, /'/g);
var capitEngLettersReg = new Array( /E'/g, /CH/g, /SH/g, /YO/g, /JO/g, /ZH/g, /YU/g, /JU/g, /YA/g, /JA/g, /A/g, /B/g, /V/g, /G/g, /D/g, /E/g, /Z/g, /I/g, /J/g, /K/g, /L/g, /M/g, /N/g, /O/g, /P/g, /R/g, /S/g, /T/g, /U/g, /F/g, /H/g, /C/g, /W/g, /~/g, /Y/g, /'/g);
var smallRusLetters = new Array("ý", "÷", "ø", "¸", "¸","æ", "þ", "þ", "ÿ", "ÿ", "à", "á", "â", "ã", "ä", "å", "ç", "è", "é", "ê", "ë", "ì", "í", "î", "ï", "ð", "ñ", "ò", "ó", "ô", "õ", "ö", "ù", "ú", "û", "ü" );
var capitRusLetters = new Array( "Ý", "×", "Ø", "¨", "¨", "Æ", "Þ", "Þ", "\ß", "\ß", "À", "Á", "Â", "Ã", "Ä", "Å", "Ç", "È", "É", "Ê", "Ë", "Ì", "Í", "Î", "Ï", "Ð", "Ñ", "Ò", "Ó", "Ô", "Õ", "Ö", "Ù", "Ú", "Û", "Ü");


function quoteMessage()
{
	var selection;

	if (window.getSelection)
	{
		selection = window.getSelection();
		selection = selection.replace(/\r\n\r\n/gi, "_newstringhere_");
		selection = selection.replace(/\r\n/gi, " ");
		selection = selection.replace(/  /gi, "");
		selection = selection.replace(/_newstringhere_/gi, "\r\n\r\n");
	}
	else if (document.getSelection)
	{
		selection = document.getSelection();
		selection = selection.replace(/\r\n\r\n/gi, "_newstringhere_");
		selection = selection.replace(/\r\n/gi, " ");
		selection = selection.replace(/  /gi, "");
		selection = selection.replace(/_newstringhere_/gi, "\r\n\r\n");
	}
	else
	{
		selection = document.selection.createRange().text;
	}

	if (selection!="")
	{
		document.REPLIER.POST_MESSAGE.value += "[quote]"+selection+"[/quote]\n";
	}
}

function quoteMessageEx(theAuthor)
{
	var selection;
	if (document.getSelection)
	{
		selection = document.getSelection();
		selection = selection.replace(/\r\n\r\n/gi, "_newstringhere_");
		selection = selection.replace(/\r\n/gi, " ");
		selection = selection.replace(/  /gi, "");
		selection = selection.replace(/_newstringhere_/gi, "\r\n\r\n");
	}
	else
	{
		selection = document.selection.createRange().text;
	}
	if (selection!="")
	{
		document.REPLIER.POST_MESSAGE.value += "[quote]"+theAuthor+" <?echo GetMessage("JQOUTE_AUTHOR_WRITES");?>:\n"+selection+"[/quote]\n";
	}
}

function emoticon(theSmilie)
{
	doInsert(" " + theSmilie + " ", "", false);
//	theSmilie = ' '+theSmilie+' ';
//	if (document.REPLIER.POST_MESSAGE.caretPos && document.REPLIER.POST_MESSAGE.createTextRange)
//	{
//		var caretPos = document.REPLIER.POST_MESSAGE.caretPos;
//		caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? theSmilie : theSmilie;
//	}
//	else
//	{
//		document.REPLIER.POST_MESSAGE.value += theSmilie;
//	}
//
//	document.REPLIER.POST_MESSAGE.focus();
}

function storeCaret(textEl)
{
	if (textEl.createTextRange) textEl.caretPos = document.selection.createRange().duplicate();
}


function reply2author(name)
{
	document.REPLIER.POST_MESSAGE.value += "[b]"+name+"[/b]"+" \n";
}

function ValidateForm(form)
{
	MessageMax = 64000;
	MessageLength = document.REPLIER.POST_MESSAGE.value.length;
	errors = "";

	if (form.MESSAGE_TYPE.value != "REPLY")
	{
		if (document.REPLIER.TITLE)
		{
			if (document.REPLIER.TITLE.value.length < 2)
			{
				errors += "<?echo GetMessage("JERROR_NO_TOPIC_NAME");?>";
			}
		}
	}

	if (MessageLength < 2)
	{
		errors += "<?echo GetMessage("JERROR_NO_MESSAGE");?>";
    }

	if (MessageMax != 0)
	{
		if (MessageLength > MessageMax)
		{
			errors += "<?echo GetMessage("JERROR_MAX_LEN1");?>"+MessageMax+"<?echo GetMessage("JERROR_MAX_LEN2");?>"+MessageLength;
		}
	}

	if (errors != "")
	{
		alert(errors);
		return false;
	}
	else
	{
		document.REPLIER.submit.disabled = true;
		return true;
	}
}

function show_hints(msg)
{
	document.REPLIER.helpbox.value = eval( "help_" + msg );
}

// Insert simple tags: B, I, U, CODE, QUOTE
function simpletag(thetag)
{
	var tagOpen = eval(thetag + "_open");

	if (tagOpen == 0)
	{
		if (doInsert("[" + thetag + "]", "[/" + thetag + "]", true))
		{
			eval(thetag + "_open = 1");
			// Change the button status
			eval("document.REPLIER." + thetag + ".value += '*'");

			pushstack(bbtags, thetag);
			cstat();
			show_hints('click_close');
		}
	}
	else
	{
		// Find the last occurance of the opened tag
		lastindex = 0;

		for (i = 0 ; i < bbtags.length; i++ )
		{
			if ( bbtags[i] == thetag )
			{
				lastindex = i;
			}
		}

		// Close all tags opened up to that tag was opened
		while (bbtags[lastindex])
		{
			tagRemove = popstack(bbtags);
			doInsert("[/" + tagRemove + "]", "", false);

			// Change the button status
			eval("document.REPLIER." + tagRemove + ".value = ' " + tagRemove + " '");
			eval(tagRemove + "_open = 0");
		}

		cstat();
	}
}

// Insert font tag
function alterfont(theval, thetag)
{
	if (theval == 0)
		return;

	if (doInsert("[" + thetag + "=" + theval + "]", "[/" + thetag + "]", true))
		pushstack(bbtags, thetag);

	document.REPLIER.ffont.selectedIndex  = 0;
	document.REPLIER.fcolor.selectedIndex = 0;
}

// Insert url tag
function tag_url()
{
	var FoundErrors = '';
	var enterURL   = prompt(text_enter_url, "http://");
	var enterTITLE = prompt(text_enter_url_name, "My Webpage");

	if (!enterURL)
	{
		FoundErrors += " " + error_no_url;
	}
	if (!enterTITLE)
	{
		FoundErrors += " " + error_no_title;
	}

	if (FoundErrors)
	{
		alert("Error! " + FoundErrors);
		return;
	}

	doInsert("[URL="+enterURL+"]"+enterTITLE+"[/URL]", "", false);
}

// Insert image tag
function tag_image()
{
	var FoundErrors = '';
	var enterURL = prompt(text_enter_image, "http://");

	if (!enterURL)
	{
		FoundErrors += " " + error_no_url;
	}

	if (FoundErrors)
	{
		alert("Error! "+FoundErrors);
		return;
	}

	doInsert("[IMG]"+enterURL+"[/IMG]", "", false);
}

// Insert list tag
function tag_list()
{
	var listvalue = "init";
	var thelist = "[LIST]\n";

	while ( (listvalue != "") && (listvalue != null) ) 
	{
		listvalue = prompt(list_prompt, "");
		if ( (listvalue != "") && (listvalue != null) ) 
		{
			thelist = thelist+"[*]"+listvalue+"\n";
		}
	}

	doInsert(thelist + "[/LIST]\n", "", false);
}


// Main insert tag
// ibTag: opening tag
// ibClsTag: closing tag, used if we have selected text
// isSingle: true if we do not close the tag right now
// return value: true if the tag needs to be closed later
function doInsert(ibTag, ibClsTag, isSingle)
{
	var isClose = false;
	var obj_ta = document.REPLIER.POST_MESSAGE;

	if ( (myVersion >= 4) && is_ie && is_win)
	{
		// this doesn't work for NS, but it works for IE 4+ and compatible browsers
		if (obj_ta.isTextEdit)
		{
			obj_ta.focus();
			var sel = document.selection;
			var rng = sel.createRange();
			rng.colapse;
			if ((sel.type == "Text" || sel.type == "None") && rng != null)
			{
				if (ibClsTag != "" && rng.text.length > 0)
					ibTag += rng.text + ibClsTag;
				else if(isSingle)
					isClose = true;

				rng.text = ibTag;
			}
		}
		else
		{
			if (isSingle)
				isClose = true;

			obj_ta.value += ibTag;
		}
	}
	else
	{
		if (isSingle)
			isClose = true;

		obj_ta.value += ibTag;
	}

	obj_ta.focus();

	return isClose;
}	

// Close all tags
function closeall()
{
	if (bbtags[0]) 
	{
		while (bbtags[0]) 
		{
			tagRemove = popstack(bbtags);
			document.REPLIER.POST_MESSAGE.value += "[/" + tagRemove + "]";

			if ( (tagRemove != 'FONT') && (tagRemove != 'SIZE') && (tagRemove != 'COLOR') )
			{
				eval("document.REPLIER." + tagRemove + ".value = ' " + tagRemove + " '");
				eval(tagRemove + "_open = 0");
			}
		}
	}

	document.REPLIER.tagcount.value = 0;
	bbtags = new Array();
	document.REPLIER.POST_MESSAGE.focus();
}

// Stack functions
function pushstack(thearray, newval)
{
	arraysize = stacksize(thearray);
	thearray[arraysize] = newval;
}

function popstack(thearray)
{
	arraysize = stacksize(thearray);
	theval = thearray[arraysize - 1];
	delete thearray[arraysize - 1];
	return theval;
}

function stacksize(thearray)
{
	for (i = 0 ; i < thearray.length; i++ )
	{
		if ( (thearray[i] == "") || (thearray[i] == null) || (thearray == 'undefined') ) 
		{
			return i;
		}
	}

	return thearray.length;
}

// Show statistic
function cstat()
{
	var c = stacksize(bbtags);

	if ( (c < 1) || (c == null) )
	{
		c = 0;
	}

	if ( ! bbtags[0] )
	{
		c = 0;
	}

	document.REPLIER.tagcount.value = c;
}


function translit()
{
	var textar = document.REPLIER.POST_MESSAGE.value;
	if (textar)
	{
		for (i=0; i<smallEngLettersReg.length; i++)
		{
			textar = textar.replace(smallEngLettersReg[i], smallRusLetters[i]);
		}
		for (var i=0; i<capitEngLettersReg.length; i++)
		{
			textar = textar.replace(capitEngLettersReg[i], capitRusLetters[i]);
		} 
		document.REPLIER.POST_MESSAGE.value = textar;
	}
}
