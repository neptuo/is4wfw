/**
 *
 *  @author  Marek Fišera marek.fisera@email.cz
 *  @date    2009/21/22
 *
 */
 
var AjaxUploadLoader = null;
var AjaxInitTopElement = null;
var AjaxLastLoadedUrl = null;
var EditAreaElements = null;
var AjaxCountDown = null;
var AjaxLogoutInput = null;
var AjaxWebConsole;
var AjaxHomeButton = null;
var AjaxRunButton = null;
var AjaxRunCover = null;
var AjaxRunInput = null;
var clockHours = null;
var clockMinutess = null;
var clockSeconds = null;

var AjaxUrlCachePageManagerEditOnlyPageId = 173;

var initClearCacheDone = false;
var initEditAreasDone = false;
 
function addEvent (obj, ev, func, b) {
  if(obj.addEventListener) {
    obj.addEventListener(ev, func, b);
  } else {
    obj.attachEvent("on" + ev, func);
  }
}

Event.domReady.add(init);

function init(event) {
	//initWebAjaxLog(event);
	//initClock(event);
	//initDockLeftIcons(event);
	//initDesktopRefresh(event);

	initEditors(event);
	initEditAreas(event);
	initEditAreasDone = false;
	initForms(document);
	initClosers(event);
	fileNameInit(event);
	initCountDown(event);
	initClearCache(event);
	initClearCacheDone = false;
	initRefreshButton(event);
	initConfirm(event);
	//initDataTables(event);
	
	initAutoSubmitForm();
	initWebprojectSelectButton(event);
	//initAjaxMenu(event).loadDefault();
	//initAjaxLinks(event);
	//initAjaxForms(event);
	
	//initUserLogin(event);
	//initKeystrokes(event);
	
	initClickableGrid();
	initCustomEntities();
	initTogglers();
	initDuplicators();
	
	var inputs = document.getElementsByTagName('input');
	for(var i = 0; i < inputs.length; i ++) {
		if(inputs[i].name == 'logout') {
			AjaxLogoutInput = inputs[i];
		}
	}

	if (AjaxCountDown != null) {
	    AjaxCountDown.onReachZero = ajaxAutoRedirect;
	}
	
	hideLoading();
	
	// roztazeni text-obsahu v gridech
	var foo = $('.foo');
	$('.foo').parent().width($(foo).parent().parent().width());
}

//addEvent(window, "load", initEditors, false);

var Editors = new Array();

function initEditors(event) {
  	var doc = document;
  var tas = doc.getElementsByTagName('textarea');

  var string = '';
  for(var no=1;no<2000;no++){
    if(string.length>0)string += '\n';
    string += no;
  }

  for(var i = 0; i < tas.length; ) {
    if(tas[i].className.indexOf('editor-textarea') != -1) {
    	var EdiConf = new Object();
    	EdiConf.textArea = tas[i];
    	if(tas[i].className.indexOf('editor-tiny') != -1) {
    		EdiConf.tiny = true;
    	} else {
				EdiConf.tiny = false;
			}
			if(tas[i].addEventListener) {
				EdiConf.wc = 65527;
  	  	EdiConf.rows = true;
	    	EdiConf.hide = true;
    		EdiConf.lnnm = true;
    		EdiConf.lineNumbers = string;
    		EdiConf.find = true;
    	} else {
    		EdiConf.wc = false;
    		EdiConf.rows = false;
	    	EdiConf.hide = true;
    		EdiConf.lnnm = false;
    		EdiConf.find = false;
    	}
    	if(tas[i].className.indexOf('editor-closed') != -1) {
    		EdiConf.closed = true;
    	} else {
				EdiConf.closed = false;
			}
      Editors[Editors.length] = new Editor(EdiConf);
    }
    if(i > 10) { break; }
    if(tas[i].addEventListener) {
			i += 2;
		} else {
			i ++;
		}
  }
}

function initEditAreas(event) {
	if(initEditAreasDone) return;
	//alert(AjaxInitTopElement ? AjaxInitTopElement : 'NULL!!');
	if(AjaxInitTopElement != null) {
		var doc = AjaxInitTopElement;
	} else {
		var doc = document;
	}
	//alert(doc);
	//var cover = doc.getElementById('editors');
	var cover = $(doc).find(".editors").get(0);
	
	if(cover == null) return;
	var tas = cover.getElementsByTagName('textarea');
	var addTab = false;
	var firstId = 0;
	var last = null;
	
	addEvent(window, 'unload', editorUnloadPage, false);
	
	for(var i = 0; i < tas.length; i ++) {
		if(tas[i].className.indexOf('edit-area') != -1) {
			var tabs = document.getElementById('editors-tab');
			var button = document.createElement('input');
			button.type = 'button';
			button.name = tas[i].id;
			var $label = $(cover).find('label[for='+tas[i].id+']');
			if ($label.length > 0) {
				button.value = $label.get(0).innerHTML.trim().replace(':', '');
			}
			//button.value = tas[i].id.replace(/-/g, ' ');
			addEvent(button, 'click', editorsTabClick, false);
			if (tabs != null) {
				tabs.appendChild(button);
				tabs.appendChild(document.createTextNode(' '));
			}
			editAreaLast = button;
		}
	}
	
	if(tabs != null) {
		var button = document.createElement('input');
		button.type = 'button';
		button.name = '';
		button.value = 'hide all';
		addEvent(button, 'click', editorsTabClick, false);
		tabs.appendChild(button);
	}
	
	var e = new Object();
	e.target = editAreaLast;
	editorsTabClick(e);
	
	initEditAreasDone = true;
}

function editorUnloadPage(event) {
	var c = new Cookies();
	for(var i = 0; i < EditAreaElements.length; i ++) {
		var sel = editAreaLoader.getSelectionRange(EditAreaElements[i].id);
		//alert("start: "+sel["start"]+"\nend: "+sel["end"]);
		// save position to cookies
		c.create(EditAreaElements[i].id + '-start', sel["start"]);
		c.create(EditAreaElements[i].id + '-end', sel["end"]);
		c.create(EditAreaElements[i].id + '-height', EditAreaElements[i].style.height);
	}
	//alert(document.cookie);
}

function editorsTabClick(event) {		
	var el = (event.srcElement) ? event.srcElement : event.target;
	var tas = document.getElementById('editors').getElementsByTagName('textarea');
	var c = new Cookies();
	
	//log("EA I: In tab click with, tas.length" + tas.length);
	for(var i = 0; i < tas.length; i ++) {
		var show = -1;
		//var height = 200;
		if(tas[i].id != el.name.replace(/ /g, '-')) {
			tas[i].parentNode.style.display = 'none';
		} else {
			show = i;
		}
		if(!EditAreaElements) {
			EditAreaElements = new Array();
			//alert(EditAreaElements);
		}
		
		if(show != -1) {
			var i = show;
			if(tas[i].rows > 0) {
				height = tas[i].rows * 10;
			}
			var th = c.read(tas[i].id + '-height');
			if(th != null) {
				tas[i].style.height = th;
			}
			tas[i].parentNode.style.display = '';
			if(tas[i].getAttribute('edit-area-init') != "true") {
				var type = 'html';
				if(tas[i].className.indexOf('css') != -1) {
					type = 'css';
				}
				//log("EA I: Before init ea ...");
				editAreaLoader.init({
					id: tas[i].id
					,start_highlight: false
					,allow_toggle: true
					,language: "en"
					,syntax: type	
					,toolbar: "search, go_to_line, |, undo, redo, |, select_font, |, change_smooth_selection, highlight, reset_highlight"
					,show_line_colors: true
					,font_size: "10"
					,font_family: "verdana, monospace"
					,allow_resize: "y"
					,replace_tab_by_spaces: 4
					,min_height: height
					,plugins: "keybinding"
				});
				tas[i].setAttribute('edit-area-init', "true");
				
				var selStart = c.read(tas[i].id + '-start');
				var selEnd = c.read(tas[i].id + '-end');
				//alert("selStart: " + selStart + ", selEnd: " + selEnd);
				if(selStart != null && selEnd != null) {
					window.setTimeout('editAreaSetSelection("' + tas[i].id + '", ' + selStart + ', ' + selEnd + ');', 1000);
					//window.setTimeout('editAreaSetSelection(' + tas[i].id + ', ' + selStart + ', ' + selEnd + ');', 1000);
				}
				EditAreaElements[EditAreaElements.length] = tas[i];
			}
		}
	}
}

function editAreaSetSelection(id, selStart, selEnd) {
	//alert('id: ' + id + ', selStart: ' + selStart + ', selEnd: ' + selEnd);
	editAreaLoader.setSelectionRange(id, selStart, selEnd);
}

//addEvent(window, "load", initClosers, false);

function initClosers(event) {
	if(AjaxInitTopElement != null) {
		var divs = AjaxInitTopElement.getElementsByTagName('div');
	} else {
		var divs = document.getElementsByTagName('div');
	}
	for(var i = 0; i < divs.length; i ++) {
		if(divs[i].className.indexOf('frame frame-cover') != -1) {
			new Closer(divs[i]);
		}
	}
}

function initClock(event) {
	//clockHours = document.getElementById('hours');
	//clockMinutes = document.getElementById('minutes');
	//clockSeconds = document.getElementById('seconds');
	
	//window.setInterval('clockNext()', 1000);
}

function initWebAjaxLog(event) {
	var cover = document.getElementById('web-ajax-log-cover');
	var icon = document.createElement('img');
	icon.src = '/images/cms/design/kterm.png';
	icon.width = 20;
	icon.height = 20;
	icon.title = "Web ajax log open / close";
	
	winListVar[winListVar.length] = new Window("Frame.webajaxlogjs", document.getElementById('cms-body'), winListVar.length, {closeable : false, maximizable : false, moveable : false, className : "web-ajax-log-window", onDock : false, reloadable : false});
	var win = winListVar[winListVar.length - 1];
	winHistory[winHistory.length] = win;
	win.open();
	win.minimize();
	win.setTitle('Web ajax log');
	win.frame.style.width = $(".dock-in").width() + "px";
	win.clientArea.style.width = ($(".dock-in").width() - 12) + "px";
	win.frame.style.left = "25px";
	win.frame.style.top = ($("#cms-body").height() - $(win.frame).height() + 6) + "px";
	win.frame.style.border = "#888888 1px solid";
	win.frame.style.padding = "0";
	win.clientArea.style.border = "none";
	AjaxWebConsole = win;
	
	$(icon).click(function (event) {
		if(win.isMinimized) {
			win.restore();
			win.makeActive();
		} else {
			if(winCtrl.active.id != win.id) {
				win.makeActive();
			} else {
				win.minimize();
			}
		}
	});
	
	var console = new Object();
	console.log = function(str) {
		if(str != null) {
			var content = "";
			var date = new Date();
			content += '<span class="web-ajax-log-date">' + date.getFullYear() + "-" + (date.getMonth() < 10 ? 0 + "" + date.getMonth() : date.getMonth()) + "-" + (date.getDay() < 10 ? 0 + "" + date.getDay() : date.getDay()) + " " + (date.getHours() < 10 ? 0 + "" + date.getHours() : date.getHours()) + ":" + (date.getMinutes() < 10 ? 0 + "" + date.getMinutes() : date.getMinutes()) + ":" + (date.getSeconds() < 10 ? 0 + "" + date.getSeconds() : date.getSeconds()) + "</span>";
			content += "<br />";
			content += str;
			content += "<hr />";
			win.addContent(content);
			win.clientArea.scrollTop = win.clientArea.scrollHeight;
		}
	}
	
	window.log = console.log;
	
	cover.appendChild(icon);
	
	win.restore();
	log('Web ajax log console loaded and ready to use.');
	win.minimize();
}

function initDockLeftIcons() {
	var cover = document.getElementById('dock-left');
	
	var home= document.createElement('img');
	AjaxHomeButton = home;
	home.src = '/images/cms/design/home.png';
	home.width = 20;
	home.height = 20;
	home.title = "Show desktop.";
	addEvent(home, "click", minimizeAllWindows, false);
	addEvent(home, 'click', onDesktopButtonClick, false);
	
	var inet = document.createElement('img');
	inet.src = '/images/cms/design/internet.png';
	inet.width = 20;
	inet.height = 20;
	inet.title = "Open web browser ... coming soon ;)";
	addEvent(inet, 'click', createBrowserWindow, false);
	
	var run = document.createElement('img');
	AjaxRunButton = run;
	run.src = '/images/cms/design/run.png';
	run.width = 20;
	run.height = 20;
	run.title = "Run page ...";
	run.tabIndex = 52;
	
	var runCover = document.createElement('div');
	runCover.className = 'run-cover';
	AjaxRunCover = runCover;
	var runInput = document.createElement('input');
	AjaxRunInput = runInput;
	runInput.type = 'text';
	runInput.className = 'run-input';
	runInput.tabIndex = 51;
	
	var runForm = document.createElement('form');
	runForm.method = 'post';
	runForm.action = '';
	
	runForm.appendChild(runInput);
	runCover.appendChild(runForm);
	
	var anchors = document.getElementById('cms-menus').getElementsByTagName('a');
	var links = new Array();
	for(var i = 0; i < anchors.length; i ++) {
		//links[links.length] = {name: $(anchors[i]).find('span').html(), to: anchors[i].href};
		var val = $(anchors[i]).find('span').html();
		if(val == null) {
			val = anchors[i].innerHTML;
		}
		links[links.length] = {name: val, to: anchors[i].href};
		log("AC: " + links[links.length - 1].name + " : " + links[links.length - 1].to);
	}
	
	log("RUN: links.length & links = " + links.length);
	$(runInput).autocomplete(links, {
		minChars: 0,
		width: 310,
		matchContains: "word",
		autoFill: false,
		formatItem: function(row, i, max) {
			return i + "/" + max + ": \"" + row.name + "\" [" + row.to + "]";
		},
		formatMatch: function(row, i, max) {
			return row.name + " " + row.to;
		},
		formatResult: function(row) {
			return row.to;
		}
	});
	
	addEvent(run, "click", onRunButtonClick, false);
	addEvent(runForm, 'submit', function(event) {
		onRunButtonClick();
		stopEvent(event);
		runInput.blur();
	}, false);
	addEvent(runInput, 'blur', function(event) {
		$(AjaxRunCover).removeClass("shown");
	}, false);
	
	/*var note = document.createElement('img');
	note.src = '/images/cms/design/notepad.png';
	note.width = 20;
	note.height = 20;
	note.title = "Open notepad ... coming soon ;)";*/
	
	cover.appendChild(home);
	cover.appendChild(inet);
	cover.appendChild(runCover);
	cover.appendChild(run);
	//cover.appendChild(note);
}

function onRunButtonClick(event) {
	if(AjaxRunCover.className.indexOf('shown') == -1) {
		$(AjaxRunCover).addClass("shown");
		AjaxRunInput.focus();
	} else {
		$(AjaxRunCover).removeClass("shown");
		var href = AjaxRunInput.value;
		AjaxLinksGlobal.loadPage(href);
		AjaxRunInput.value = "";
	}
}

function onDesktopButtonClick(event) {
	xmlHttp = new Rxmlhttp();
	xmlHttp.setAsync(true);
	xmlHttp.setMethod('get');
	showLoading();
	xmlHttp.onSuccess(function(xmlhttp) {
		var tmp = document.createElement('div');
		tmp.innerHTML = xmlhttp.responseText;
		$('#home-desktop').html($(tmp).find('.home-cover').html());
		hideLoading();
		AjaxInitTopElement = $('#home-desktop').get(0);
		initAjaxLinks(event);
	});
	xmlHttp.onError(function() {
		log("ERR: Some error occurs during refreshing desktop!");
		hideLoading();
	});
		
	xmlHttp.loadPage(window.location.href + "?__START_ID=5&__TEMPLATE=xml");
}

function createBrowserWindow(event) {
	var el = document.createElement('div');
	el.id = "Frame.webbrowser";
	el.setAttribute('width', 800);
	el.setAttribute('height', 500);
	el.setAttribute('maximized', "false");
	
	var win = createWindow(el);
	win.setTitle('Web Browser');
	win.setContent('<h3>Web browser is coming soon ;)</h3>');
	win.open();
}

function initDesktopRefresh(event) {
	var strong = $("#home-desktop strong").get(1);
	var icon = document.createElement('img');
	icon.src = "/images/cms/window/button-4.gif";
	icon.title = "Refresh desktop.";
	addEvent(icon, 'click', function() {
		xmlHttp = new Rxmlhttp();
		xmlHttp.setAsync(true);
		xmlHttp.setMethod('get');
		xmlHttp.onSuccess(function(xmlhttp) {
			var tmp = document.createElement('div');
			tmp.innerHTML = xmlhttp.responseText;
			var inner = $(tmp).find('.home-cover').get(0);
			strong.parentNode.innerHTML = inner.innerHTML;
			
			initDesktopRefresh(event);
		});
		xmlHttp.onError(function() {
			log("ERR: Some error occurs during refreshing desktop!");
		});
		
		xmlHttp.loadPage(window.location.href + "?__START_ID=5&__TEMPLATE=xml");
	}, false);
	strong.appendChild(icon);
}

function clockNext() {
	var time = new Date();
	clockHours.innerHTML = (time.getHours() < 10) ? "0" + time.getHours() : time.getHours();
	clockMinutes.innerHTML = (time.getMinutes() < 10) ? "0" + time.getMinutes() : time.getMinutes();
	clockSeconds.innerHTML = (time.getSeconds() < 10) ? "0" + time.getSeconds() : time.getSeconds();
	document.getElementById('clock').title = time.getDay() + "." + time.getMonth() + "." + time.getFullYear();
}

//addEvent(window, "load", fileNameInit);

function fileNameInit(event) {
	if(AjaxInitTopElement != null) {
		var inpts = AjaxInitTopElement.getElementsByTagName('input');
	} else {
		var inpts = document.getElementsByTagName('input');
	}
	var textInput = null;
	var fileInput = null;
	for(var i = 0; i < inpts.length; i ++) {
		if(inpts[i].name == "file-name") {
			textInput = inpts[i];
		}
		if(inpts[i].name == "file-rs") {
			fileInput = inpts[i];
		}
		if(textInput != null && fileInput != null) {
			new FileName(fileInput, textInput);
			textInput = null;
			fileInput = null;
		}
	}
}

//addEvent(window, "load", initCountDown);

function initCountDown(event) {
	var cdl = document.getElementById('logon-count-down');
    var x = document.getElementById('count-down-counter');
    if(x != null) {
	    var value = parseInt(x.innerHTML);
	    cdl.innerHTML = '';
	    if(!value || value < 0) {
		    value = 15;
	    }
	    AjaxCountDown = new CountDown('Login session <br />expires in: ', value * 60, cdl);
	    AjaxCountDown.start();
    }
}

function initCountDown2(event) {
	AjaxCountDown.restart();
}

function initRefreshButton(event) {
    var cdl = document.getElementById('logon-count-down');
    if (cdl != null) {
        var refresh = document.createElement('div');
        refresh.className = 'refresh-session';
        var form = document.createElement('form');
        form.action = '';
        form.name = 'refresh-session';
        form.method = 'post';
        var button = document.createElement('input');
        button.name = 'refresh-session';
        button.className = 'refresh-session-button';
        button.value = 'Refresh';
        button.type = 'submit';
        form.appendChild(button);
        refresh.appendChild(form);
        cdl.parentNode.insertBefore(refresh, cdl);

        function refreshSessionClicked(event) {

        }
        addEvent(button, 'click', refreshSessionClicked, false);

        var pform = new ProcessForm(form);
        pform.setStartPageId(5);
        pform.setResponseTemplate('xml');
        pform.setUseStartPageId(true);
        pform.setUpdateLocation(false);
        pform.onSubmitButton = function () {
            return true;
        };
        pform.beforeSubmit = function (event, form) {
            AjaxLastLoadedUrl = form.action;
            showLoading();
        };

        pform.onSuccess = function (xmlhttp, event) {
            initCountDown2(event);
            hideLoading();
        };
    }
}

function initClearCache(event) {
	if(initClearCacheDone) return;
	if(AjaxInitTopElement != null) {
		var doc = AjaxInitTopElement;
	} else {
		var doc = document;
	}
	var cover = $(doc).find("#clear-url-cache").get(0);
	if(cover != null) {
		var submit = document.getElementById('clear-url-cache-submit');
		var selectAll = document.createElement('input');
		selectAll.type = 'button';
		selectAll.name = 'clear-url-cache-select-all';
		selectAll.value = 'Select All';
		var unselectAll = document.createElement('input');
		unselectAll.type = 'button';
		unselectAll.name = 'clear-url-cache-unselect-all';
		unselectAll.value = 'Unselect All';
		
		inputs = cover.getElementsByTagName('input');
		checkboxes = new Array();
		for(var i = 0; i < inputs.length; i ++) {
			if(inputs[i].type == 'checkbox') {
				checkboxes[checkboxes.length] = inputs[i];
			}
		}
		
		function clickClearCacheSelectAll(event) {
			for(var i = 0; i < checkboxes.length; i ++) {
				checkboxes[i].checked = 'checked';
			}
		}
		
		function clickClearCacheUnselectAll(event) {
			for(var i = 0; i < checkboxes.length; i ++) {
				checkboxes[i].checked = '';
			}
		}
		
		var pids = $(cover).find(".url-cache-page label").get();
		if(false && pids && pids.length > 0) {
			for(var i = 0; i < pids.length; i ++) {
				var pages = pids[i].innerHTML.split('-');
				for(var j = 0; j < pages.length; j ++) {
					var a = document.createElement('a');
					a.href = '#';
					a.setAttribute('langid', pids[i].getAttribute('langid'));
					a.innerHTML = pages[j];
					addEvent(a, 'click', function(event) {
						xmlHttp = new Rxmlhttp();
						xmlHttp.setAsync(false);
						xmlHttp.setMethod('get');
						xmlHttp.onSuccess(function(xmlhttp) {
								var url = xmlhttp.responseText;
								var options = new Array();
								options[0] = {};
								options[1] = {};
								options[2] = {};
								options[3] = {};
								var el = event.srcElement ? event.srcElement : event.target;
								options[0]['key'] = "page-id";
								options[0]['val'] = el.innerHTML;
								options[1]['key'] = "parent-id";
								options[1]['val'] = el.innerHTML;
								options[2]['key'] = "page-lang-id";
								options[2]['val'] = el.getAttribute('langid');
								options[3]['key'] = "page-edit";
								options[3]['val'] = "Edit";
								postPageWithOptions(url + "?__START_ID=5&__TEMPLATE=xml", options);
						});
						xmlHttp.onError(function() {
							log("ERR: Some error occurs during saving window width, height!");
						});
						xmlHttp.loadPage("/url-composer.php?page-id=" + AjaxUrlCachePageManagerEditOnlyPageId + "&lang-id=1");
						stopEvent(event);
					}, false);
					pids[i].parentNode.appendChild(a);
					if(j != pages.length - 1) {
						pids[i].parentNode.appendChild(document.createTextNode('-'));
					}
				}
				pids[i].parentNode.removeChild(pids[i]);
			}
		}
		
		addEvent(selectAll, 'click', clickClearCacheSelectAll, false);
		addEvent(unselectAll, 'click', clickClearCacheUnselectAll, false);
		submit.appendChild(selectAll);
		submit.appendChild(document.createTextNode(' '));
		submit.appendChild(unselectAll);
		initClearCacheDone = true;
	}
}

function initAutoSubmitForm() {
	var forms = document.getElementsByTagName('form');
	for (var i = 0; i < forms.length; i ++) {
		var form = forms[i];

		if (form.className.indexOf('auto-submit') >= 0) {
			var inputs = form.getElementsByTagName('input');
			var buttons = form.getElementsByTagName('button');
			var textAreas = form.getElementsByTagName('textarea');
			var selects = form.getElementsByTagName('select');
			var submit = null

			for (let j = 0; j < inputs.length; j++) {
				const element = inputs[j];

				if (element.type == 'submit') {
					submit = element;
				}
			}

			if (submit == null) {
				continue;
			}

			var handler = submit.click.bind(submit);

			for (let j = 0; j < inputs.length; j++) {
				const element = inputs[j];
				if (element.type != 'submit' && element.type != 'button') {
					element.addEventListener('change', handler, false);
				} else {

					element.style.display = 'none';
				}
			}
			for (let j = 0; j < buttons.length; j++) {
				const element = buttons[j];
				element.style.display = 'none';
			}
			for (let j = 0; j < textAreas.length; j++) {
				const element = textAreas[j];
				element.addEventListener('change', handler, false);
			}
			for (let j = 0; j < selects.length; j++) {
				const element = selects[j];
				element.addEventListener('change', handler, false);
			}
		}
	}
}

function initWebprojectSelectButton(event) {
	var forms = document.getElementsByTagName('form');
	for(var i = 0; i < forms.length; i ++) {
	    if (forms[i].name == 'select-project' && forms[i].parentElement.className != 'gray-box-float') {
			var form = forms[i];
			var inputs = form.getElementsByTagName('input');
			inputs[0].style.display = 'none';
			var selects = form.getElementsByTagName('select');
			
			function changeSelectProject(event) {
				inputs[0].click();
			};
			
			addEvent(selects[0], 'change', changeSelectProject, false);
			
			break;
		}
	}
}

function initAjaxMenu(event) {
	var cmsMenus = document.getElementById('cms-menus');
	var links = new Links(cmsMenus);
  links.setStartPageId(5);
  links.setResponseTemplate('xml');
  links.setUseStartPageId(true);
  links.setUpdateLocation(false);
	
	links.beforeRequest = function(url) {
		AjaxLastLoadedUrl = url;
		showLoading();
		//document.body.style.cursor = 'wait';
	};
	
	links.onSuccess = function(xmlHttp) {
    processAjaxResult(xmlHttp);
  };
  
  return links;
}

function initAjaxLinks(event) {
	if(AjaxInitTopElement != null) {
		var cmsBody = AjaxInitTopElement;
	} else {
		var cmsBody = document.getElementById('cms-body');
	}
	var links = new Links(cmsBody);
  links.setStartPageId(5);
  links.setResponseTemplate('xml');
  links.setUseStartPageId(true);
  links.setUpdateLocation(false);
	
	links.beforeRequest = function(url) {
		AjaxLastLoadedUrl = url;
		showLoading();
		//document.body.style.cursor = 'wait';
	};
	
	links.onSuccess = function(xmlHttp) {
    processAjaxResult(xmlHttp);
  };
}

function initAjaxForms(event) {
	if(AjaxInitTopElement != null) {
		var cmsBody = AjaxInitTopElement;
	} else {
		var cmsBody = document.getElementById('cms-body');
	}
	var forms = new Array();
	var cmsForms = cmsBody.getElementsByTagName('form');
	
	for(var i = 0; i < cmsForms.length; i ++) {
		var form = new ProcessForm(cmsForms[i]);
  	form.setStartPageId(5);
  	form.setResponseTemplate('xml');
	  form.setUseStartPageId(true);
	  form.setUpdateLocation(false);
	  form.onSubmitButton = ajaxConfirmSubmit;
		form.beforeSubmit = formBeforeSubmit;
		form.beforeUpload = formBeforeUpload;
		form.onSuccess = processAjaxResult;
		form.onSuccessUpload = successUpload;
		form.setUseHashUrlIfFormActionIsPlain(true);
		forms[forms.length] = form;
	}
}

function ajaxConfirmSubmit(event, form) {
	var elm = ((event.srcElement) ? event.srcElement : event.target);
	if(elm.className.indexOf('confirm') != -1) {
		var title = 'this';
		if(elm && elm.title && elm.title.length != 0) {
			title = '\n\n\t"' + elm.title + '"\n\n';
		}
		return window.confirm(title);
	}
	
	try {
		var name = form.name;
		if(name == 'page-edit-detail' || name == 'edit-file' || name == 'template-edit-detail' || name == 'article-edit') {
			if(EditAreaElements) {
				for(var i = 0; i < EditAreaElements.length; i ++) {
					EditAreaElements[i].value = editAreaLoader.getValue(EditAreaElements[i].id);
				}
			}
		}
		EditAreaElements = null;
	} catch(e) {
	
	}
	
	return true;
}
	
function formBeforeSubmit(event, form) {
	AjaxLastLoadedUrl = form.action;
	showLoading();
	//document.body.style.cursor = 'wait';
}
	
function formBeforeSubmit2(event, form) {
	showLoading();
	//document.body.style.cursor = 'wait';
}
	
function formBeforeUpload(event, form) {
	var el = (event.target) ? event.target : event.srcElement;
	var loader = document.createElement('img');
	loader.src = '/images/loader.gif';
	if(AjaxUploadLoader != null) {
		AjaxUploadLoader.parentNode.removeChild(AjaxUploadLoader);
	}
	el.parentNode.appendChild(loader);
	AjaxUploadLoader = loader;
}

function processAjaxResult(xmlHttp) {
	var cmsMenus = document.getElementById('cms-menus');
	var cmsBody = document.getElementById('cms-body');
	var temp = document.createElement('div');

  temp.innerHTML = xmlHttp.responseText;
  if(navigator.appName == 'Microsoft Internet Explorer') {
    var body = temp.getElementsByTagName('content');
    var title = temp.getElementsByTagName('title');
    var log = temp.getElementsByTagName('log');
  } else {
    var body = temp.getElementsByTagName('rssmm:content');
    var title = temp.getElementsByTagName('rssmm:title');
    var log = temp.getElementsByTagName('rssmm:log');
  }
  body = body[0];
  title = title[0];
  
  //cmsBody.innerHTML = body.innerHTML;
  initJSWindows(body);
  //document.title = title.innerHTML;
  
  if(log.length != 0) {
		 window.log(log[0].innerHTML);
	}

  // pridat links & forms
//  initForms(document);
//	initEditors();
//	initEditAreas();
//	initClosers();
//	fileNameInit();
//	initCountDown2();
//	initClearCache();
	//initConfirm();
	
//  initAjaxForms();
//	initAjaxLinks();
	
	//document.body.style.cursor = 'Auto';
	hideLoading();
}

function successUpload() {
	var node = document.createTextNode(' Uploaded ...');
	AjaxUploadLoader.parentNode.appendChild(node);
	AjaxUploadLoader.parentNode.removeChild(AjaxUploadLoader);
	AjaxUploadLoader = node;
	
	var links = new Links(document, 5, 'xml', false);
  links.setUpdateLocation(false);
	
	links.beforeRequest = function() {
		showLoading();
		//document.body.style.cursor = 'wait';
	};
	
	links.onSuccess = function(xmlHttp) {
		var cmsMenus = document.getElementById('cms-menus');
		var cmsBody = document.getElementById('cms-body');
		var temp = document.createElement('div');
	
	  temp.innerHTML = xmlHttp.responseText;
	  if(navigator.appName == 'Microsoft Internet Explorer') {
	    var body = temp.getElementsByTagName('content');
	    var title = temp.getElementsByTagName('title');
	  } else {
	    var body = temp.getElementsByTagName('rssmm:content');
	    var title = temp.getElementsByTagName('rssmm:title');
	  }
	  body = body[0];
	  title = title[0];
	  
	  var divs = body.getElementsByTagName('div');
	  for(var i = 0; i < divs.length; i ++) {
			if(divs[i].className.indexOf('Frame.filelist') != -1) {
				//var filelist = document.getElementById('Frame.filelist');
				//filelist.innerHTML = divs[i].innerHTML;
				var win = findWindow('Frame.filelist');
				win.setContent(divs[i].childNodes[1].innerHTML);
				
				AjaxInitTopElement = win.clientArea;
			
	  		initAjaxForms();
				initAjaxLinks();
				fileNameInit();
				
				AjaxInitTopElement = null;
			} else if(divs[i].className.indexOf('Frame.newfile') != -1) {
				//var filelist = document.getElementById('Frame.editfile');
				var win = findWindow('Frame.newfile');
				win.setContent(divs[i].childNodes[1].innerHTML);
				
				AjaxInitTopElement = win.clientArea;
			
	  		initAjaxForms();
				initAjaxLinks();
				
				AjaxInitTopElement = null;
			}
		}
		
		hideLoading();
	}
	
	//links.onSuccess = processAjaxResult;
	if(AjaxLastLoadedUrl) {
		links.loadPage(AjaxLastLoadedUrl);
	}
}

function initDataTables() {
	if(AjaxInitTopElement) {
		var doc = AjaxInitTopElement;
	} else {
		var doc = document;
	}
	var tables = doc.getElementsByTagName('table');
	for(var i = 0; i < tables.length; i ++) {
		if(tables[i].className.indexOf('data-table') != -1) {
			$(tables[i]).dataTable({
				"bAutoWidth": false/*,
				"bStateSave": true*/
			});
		}
		//alert(i + ", " + tables[i]);
	}
}

function initClickableGrid() {
	var rows = $('.clickable').find('tr');
	for(var i = 0, s = rows.length; i < s; i++) {
		$(rows[i]).click(function(e) {
			var el = e.target ? e.target : e.srcElement;

			$tr = $(el).parents('tr');
			$buttonEdit = $tr.find('.button-edit');
			if ($buttonEdit.length != 0) {
				$button = $buttonEdit[0];
			} else {
				$target = $tr.find(".clickable-target");
				$buttons = $tr.find('input[type=image]');
				$button = $buttons.filter('[src$="edi.png"]');
				$checkbox = $tr.find('input[type=checkbox]');
				if ($target.length > 0) {
					$button = $target[0];
				} else if ($button.length > 0) {
					$button = $button[0];
				} else if ($buttons.length > 0) {
					$button = $buttons[0];
				} else {
					$button = $checkbox[0];
				}
			}

			if ($button != null) {
				$button.click();
			}
		});
	}
}

function showLoading() {
    var x = document.getElementById('loading');
    if (x != null) {
        x.style.display = 'block';
        //$(document).blockUI({ message: '<h1>Loading ...</h1>' });
        //$.blockUI({ message: '<h1>Loading ...</h1>' });

        if (AjaxCountDown.getCount() == 0) {
            ajaxAutoRedirect();
        }
    }
}

function hideLoading() {
    var x = document.getElementById('loading');
    if(x != null) {
        x.style.display = 'none';
    }
	//$.unblockUI({ message: '<h1>Loading ...</h1>' });
}

function ajaxAutoRedirect() {
	AjaxLogoutInput.click();
}

// Aktualizovat vypis adresare ;-)

function initCustomEntities() {
	$(".ce-creator-add").click(function(e) {
		var $form = $(e.currentTarget).closest("form");
		var $target = $form.find(".ce-columns");
		var $source = $form.find(".ce-column").last();
		
		var $new = $source.clone();
		$new.find("input").val("");

		$target.append($new);
	})
}

function initTogglers() {
	$toggles = $("[data-toggle]").hide();
	function switchGroup(group, value) {
		var value = group + "-" + value;
		$toggles.filter("[data-toggle^='" + group + "']").hide();
		$toggles.filter("[data-toggle='" + value + "']").show();
	}

	$("[data-toggler]").each(function (i, el) {
		var $el = $(el);
		var group = $el.attr("data-toggler");
		if (el.tagName == "SELECT") {
			$el.change(function () {
				switchGroup(group, $(this).val());
			});

			switchGroup(group, $el.val());
		}
	});
}

function initDuplicators() {
	$("[data-duplicator]").each(function(i, el) {
		var $el = $(el);
		var selector = $el.attr("data-duplicator");
		$el.click(function (e) {
			$duplicable = $(selector);
			$clone = $duplicable.clone();
			$clone.removeAttr("data-duplicable");
			$clone.appendTo($duplicable.parent());
			$clone.find('input[type=text], textarea').val('').first().focus();

			e.preventDefault();
		});
	});
}