//=============================================================================
// Globals
//=============================================================================
var winListVar = null;
var winHistory = new Array();
var winFrameWidthOffset = 8;
var winFrameHeightOffset = 7;
var winClientAreaHeightOffset = 44;
var dockElementId = 'dock';
var desktopElement = null;

var UserLogin = "";

window.log = function(str) {}


//=============================================================================
// Initialization code.
//=============================================================================
var winList = new Array();
var winCtrl = new Object();

var winListVar = winList;

function winInit() {
  var elList;
  // Initialize window control object.
  winCtrl.maxzIndex                        =   0;
  winCtrl.resizeCornerSize                 =  16;
  winCtrl.minimizedTextWidth               = 100;
  winCtrl.inactiveFrameBackgroundColor     = "#c0c0c0";
  winCtrl.inactiveFrameBorderColor         = "#f0f0f0 #505050 #404040 #e0e0e0";
  winCtrl.inactiveTitleBarColor            = "#808080";
  winCtrl.inactiveTitleTextColor           = "#c0c0c0";
  winCtrl.inactiveClientAreaBorderColor    = "#404040 #e0e0e0 #f0f0f0 #505050";
  winCtrl.inactiveClientAreaScrollbarColor = "";
  winCtrl.inMoveDrag                       = false;
  winCtrl.inResizeDrag                     = false;
  
  desktopElement = document.getElementById('cms-body');
}

$(function() {
	$("#cms-body").height($(window).height() - $(".dock-bar").height() - $("#cms-head").height());
	
	/*addEvent(document.getElementById('cms-body'), 'click', function(event) {
		winMakeActive();
	}, true);*/
	
	/*$(document).ready(function() {
		$('#example').dataTable();
	});*/
	
	addEvent(window, 'resize', function(event) {
		$("#cms-body").height($(window).height() - $(".dock-bar").height() - $("#cms-head").height());
	}, true);
	
	winInit();
});


//=============================================================================
// Functions.
//=============================================================================
function addEvent (obj, ev, func, b) {
  if(obj.addEventListener) {
    obj.addEventListener(ev, func, b);
  } else {
    obj.attachEvent("on" + ev, func);
  }
}

function removeEvent(obj, ev, func, b) {
  if(obj.removeEventListener) {
    obj.removeEventListener(ev, func, b);
  } else {
    obj.deattachEvent("on" + ev, func);
  }
}

function stopEvent(event) {
  if(navigator.appName != "Microsoft Internet Explorer") {
    event.stopPropagation();
    event.preventDefault();
  } else {
    event.cancelBubble = true;
    event.returnValue = false;
  }
}

function initJSWindows(el) {
	var frames = $(el).find('.frame-cover').get();
	var first = null;
	for(var i = 0; i < frames.length; i ++) {
		win = findWindow(frames[i].id);
		if(win != null) {
			win.setContent(frames[i].childNodes[1].innerHTML);
			win.setTitle(frames[i].childNodes[0].childNodes[0].innerHTML);
		} else {
			win = createWindow(frames[i]);
			win.setContent(frames[i].childNodes[1].innerHTML);
			win.setTitle(frames[i].childNodes[0].childNodes[0].innerHTML);
			win.open();
		}
		//alert($(win.clientArea).find('#editors').get(0));
		AjaxInitTopElement = win.clientArea;
		// pridat links & forms
	  //initAjaxMenu();
		initClearCache();
	  initForms(document);
		initEditors();
		initEditAreas();
		initClosers();
		fileNameInit();
		initCountDown2();
		initDataTables();
		//initConfirm();
		
	  initAjaxForms();
		initAjaxLinks();
		
		AjaxInitTopElement = null;
		if(i == 0) {
			first = win;
		}
	}
	initClearCacheDone = false;
	initEditAreasDone = false;
	
	if(first) {
		first.makeActive();
	}
}

function findWindow(id) {
	for(var i = 0; i < winListVar.length; i ++) {
		if(winListVar[i] && winListVar[i].id == id) {
			return winListVar[i];
		}
	}
	return null;
}

function createWindow(el) {
	var body = desktopElement;
	winListVar[winListVar.length] = new Window(el.id, body, winListVar.length);
	win = winListVar[winListVar.length - 1];
	win.url = AjaxLastLoadedUrl;
	win.open();
	win.onClose = closeWindow;
	win.onResizeEnd = afterResizeWindow;
	win.onMoveEnd = afterMoveWindow;
	win.onMaximized = afterMaximizedWindow;
	win.onMinimized = afterMinimizedWindow;
	win.onRestoreFromMaximized = afterRestoreFromMaximized;
	if(el.getAttribute("left") && el.getAttribute("left") != 0) {
		win.frame.style.left = el.getAttribute("left") + "px";
	}
	if(el.getAttribute("top") && el.getAttribute("top") != 0) {
		win.frame.style.top = el.getAttribute("top") + "px";
	}
	if(el.getAttribute("width")) {
		win.frame.style.width = el.getAttribute("width") + "px";
	}
	if(el.getAttribute("height")) {
		win.clientArea.style.height = el.getAttribute("height") + "px";
	}
	if(el.getAttribute("maximized")) {
		if(el.getAttribute("maximized") == "true") {
			win.maximize();
		}
	}
	winHistory[winHistory.length] = win;
	//log("Win.url: " + win.url);
	return win;
}

function minimizeAllWindows() {
	for(var i = 0; i < winListVar.length; i ++) {
		if(winListVar[i].frame && !winListVar[i].isMinimized) {
			winListVar[i].minimize();
		}
	}
}

function closeWindow(id) {
	var win = findWindow(id);

	for(var i = 0; i < winListVar.length; i ++) {
		if(winListVar[i] != null) {
			//console.log(winListVar[i].id + " == " + id + "? " + (winListVar[i].id == id));
			if(winListVar[i].id == id) {
				winListVar[i] = new Object();
				winListVar[i].id = 0;
			}
		}
	}
}

function afterResizeWindow(id) {
	var frame = findWindow(id);
	var prop = new Object();
	prop.userLogin = UserLogin;
	prop.frameId = id;
	prop.width = frame.frame.style.width.substr(0, frame.frame.style.width.length - 2);
	prop.height = frame.clientArea.style.height.substr(0, frame.clientArea.style.height.length - 2);
	
	sendNewWindoProperties(prop);
}

function afterMoveWindow(id) {
	var frame = findWindow(id);
	var prop = new Object();
	prop.userLogin = UserLogin;
	prop.frameId = id;
	prop.left = frame.frame.style.left.substr(0, frame.frame.style.left.length - 2);
	prop.top = frame.frame.style.top.substr(0, frame.frame.style.top.length - 2);
	
	sendNewWindoProperties(prop);
}

function afterMaximizedWindow(id) {
	var frame = findWindow(id);
	var prop = new Object();
	prop.userLogin = UserLogin;
	prop.frameId = id;
	prop.maximized = "true";
	
	sendNewWindoProperties(prop);
}

function afterRestoreFromMaximized(id) {
	var frame = findWindow(id);
	var prop = new Object();
	prop.userLogin = UserLogin;
	prop.frameId = id;
	prop.maximized = "false";
	
	sendNewWindoProperties(prop);
}

function afterMinimizedWindow(id) {
	
}

function sendNewWindoProperties(prop) {
	if((prop.userId != null || prop.userLogin != null) && prop.frameId != null) {
		xmlHttp = new Rxmlhttp();
		xmlHttp.setAsync(true);
		xmlHttp.setMethod('get');
		xmlHttp.onSuccess(function() {
			
		});
		xmlHttp.onError(function() {
			log("ERR: Some error occurs during saving window width, height!");
		});
		
		query = "";
		query = "?request=set";
		
		if(prop.userId != null) {
			query += "&userId=" + prop.userId;
		} else {
			query += "&user-login=" + prop.userLogin;
		}
		
		query += "&frame-id=" + prop.frameId;
		
		if(prop.width != null) {
			query += "&frame-width=" + prop.width;
		}
		if(prop.height != null) {
			query += "&frame-height=" + prop.height;
		}
		if(prop.left != null) {
			query += "&frame-left=" + prop.left;
		}
		if(prop.top != null) {
			query += "&frame-top=" + prop.top;
		}
		if(prop.maximized != null) {
			query += "&frame-maximized=" + prop.maximized;
		}
		
		xmlHttp.loadPage("/window-property.php" + query);
	}
}

function initUserLogin(event) {
	var loginEl = $(".user-info .user-login").get(0);
	UserLogin = loginEl.innerHTML.substr(0, loginEl.innerHTML.indexOf('@'));
}

function postPageWithOptions(url, options) {
	var q = "";
	for(var i = 0; i < options.length; i ++) {
		q += options[i]['key'] + "=" + options[i]['val'];
		if(i != options.length - 1) {
			q += "&";
		}
	}
	
	xmlHttp = new Rxmlhttp();
	xmlHttp.setAsync(true);
	xmlHttp.setMethod('post');
	xmlHttp.onSuccess(processAjaxResult);
	xmlHttp.onError(function() {
		log("ERR: Some error occurs during loading edit page!");
	});
	
	xmlHttp.loadPage(url, q);
}

function initKeystrokes(event) {
	addEvent(window, 'keydown', function(e) {
		var el = e.target ? e.target : e.srcElement;
		var onInputOrTextareaOrSelect = (el.tagName == 'INPUT' || el.tagName == 'TEXTAREA' || el.tagName == 'SELECT') ? true : false;
	
		if(onInputOrTextareaOrSelect && e.keyCode == 27) {
			el.blur();
		}
	
		if(e.shiftKey && e.keyCode == 123) {
			if(AjaxWebConsole.isMinimized) {
				AjaxWebConsole.restore();
				AjaxWebConsole.makeActive();
			} else {
				AjaxWebConsole.minimize();
			}
			stopEvent(e);
		} else if(e.ctrlKey && (e.keyCode == 65 || e.keyCode == 79)) {
			stopEvent(e);
		} else if(e.shiftKey && e.keyCode == 88 && !onInputOrTextareaOrSelect) {
			if(winCtrl.active && winCtrl.active.isOpen) {
				winCtrl.active.close();
			}
			stopEvent(e);
		} else if(e.shiftKey && e.keyCode == 90 && !onInputOrTextareaOrSelect) {
			if(winCtrl.active && winCtrl.active.isOpen) {
				if(winCtrl.active.isMinimized) {
					winCtrl.active.restore();
				} else {
					winCtrl.active.minimize();
				}
			}
			stopEvent(e);
		} else if(e.shiftKey && e.keyCode == 68 && !onInputOrTextareaOrSelect) {
			minimizeAllWindows();
			onDesktopButtonClick(e);
			stopEvent(e);
		} else if(e.shiftKey && e.keyCode == 79 && !onInputOrTextareaOrSelect) {
			onRunButtonClick(e);
			stopEvent(e);
		}
		log('KEY: ' + e.keyCode + ", " + e.shiftKey + ", " + e.ctrlKey + ", " + e.metaKey + ", " + el + ", " + el.tagName);
	}, false);
}