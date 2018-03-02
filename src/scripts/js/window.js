//<![CDATA[
//*****************************************************************************
// Do not remove this notice.
//
// Copyright 2001 by Mike Hall.
// See http://www.brainjar.com for terms of use.
//*****************************************************************************
// Determine browser and version.
function Browser() {
  var ua, s, i;
  var This = this;
  this.isIE    = false;  // Internet Explorer
  this.isNS    = false;  // Netscape
  this.version = null;
  ua = navigator.userAgent;
  s = "MSIE";
  if ((i = ua.indexOf(s)) >= 0) {
    this.isIE = true;
    this.version = parseFloat(ua.substr(i + s.length));
    return;
  }
  s = "Netscape6/";
  if ((i = ua.indexOf(s)) >= 0) {
    this.isNS = true;
    this.version = parseFloat(ua.substr(i + s.length));
    return;
  }
  // Treat any other "Gecko" browser as NS 6.1.
  s = "Gecko";
  if ((i = ua.indexOf(s)) >= 0) {
    this.isNS = true;
    this.version = 6.1;
    return;
  }
}
var browser = new Browser();
//=============================================================================
// Window Object
//=============================================================================
function Window(id, parent, index, props) {

	if(props != null) {
		this.props = props;
	} else {
		this.props = new Object();
		this.props.minimizable = true;
		this.props.maximizable = true;
		this.props.closeable = true;
		this.props.onDock = true;
		this.props.reloadable = true;
		this.className = "";
		props = this.props;
	}
	var This = this;
  var i, mapList, mapName;
  this.index = index;
  this.url = "";
  this.id = id;
  // Get window components.
  this.frame = document.createElement('div');
  this.frame.className = "window";
  this.frame.style.width="400px";
  
  if(this.props.className && this.props.className != "") {
		this.frame.className += " " + this.props.className;
	}
  
  this.titleBar = document.createElement('div');
  this.titleBar.className = "titleBar";
  this.frame.appendChild(this.titleBar);
  this.titleBarText = document.createElement('div');
  this.titleBarText.className = "titleBarText";
  this.titleBar.appendChild(this.titleBarText);
  this.titleBarButtons = document.createElement('img');
  this.titleBarButtons.className = "titleBarButtons";
  this.titleBarButtons.src = "/images/cms/window/buttons.gif";
  this.titleBarButtons.width = "50";
  this.titleBarButtons.height = "14";
  this.titleBarButtons.usemap = "#window" + index;
  //this.titleBar.appendChild(this.titleBarButtons);
  this.titleBarMap = document.createElement('map');
  this.titleBarMap.id = "#window" + index;
  this.titleBarMap.name = "#window" + index;
  //this.titleBar.appendChild(this.titleBarMap);
  
  this.titleBarArea1 = document.createElement('img');
  this.titleBarArea1.src = "/images/cms/window/button-1.gif";
  //this.titleBarArea2.shape = "rect";
  //this.titleBarArea2.coords = "16,0,31,13";
  //this.titleBarArea2.href = "";
  this.titleBarArea1.title = "Minimize";
  this.titleBarArea1.onclick = function() {
		This.minimize();
		return false;
	}
	
  this.titleBarArea2 = document.createElement('img');
  this.titleBarArea2.src = "/images/cms/window/button-2.gif";
  //this.titleBarArea2.shape = "rect";
  //this.titleBarArea2.coords = "16,0,31,13";
  //this.titleBarArea2.href = "";
  this.titleBarArea2.title = "Maximize";
  this.titleBarArea2.onclick = function() {
		This.maximize();
		return false;
	}
	
  this.titleBarArea3 = document.createElement('img');
  this.titleBarArea3.src = "/images/cms/window/button-3.gif";
  this.titleBarArea3.style.marginLeft = "2px";
  //this.titleBarArea3.shape = "rect";
  //this.titleBarArea3.coords = "34,0,49,13";
  //this.titleBarArea3.href = "";
  this.titleBarArea3.title = "Close";
  this.titleBarArea3.onclick = function() {
		This.close();
		return false;
	}
	
  this.titleBarArea4 = document.createElement('img');
  this.titleBarArea4.src = "/images/cms/window/button-4.gif";
  this.titleBarArea4.style.marginRight = "2px";
  this.titleBarArea4.title = "Refresh";
  this.titleBarArea4.onclick = function() {
		AjaxLinksGlobal.loadPage(This.url);
		return false;
	}
  
  this.clientArea = document.createElement('div');
  this.clientArea.className = "clientArea";
  this.clientArea.style.height = "300px";
  this.clientArea.innerHTML = "<hr />";
  this.clientArea.innerHTML = "";
  this.frame.appendChild(this.clientArea);
  
	if(props.reloadable != false) {
		this.titleBar.appendChild(this.titleBarArea4);
	}
  if(props.minimizable != false) {
		this.titleBar.appendChild(this.titleBarArea1);
	}
	if(props.maximizable != false) {
		this.titleBar.appendChild(this.titleBarArea2);
	}
	if(props.closeable != false) {
		this.titleBar.appendChild(this.titleBarArea3);
	}
  
  parent.appendChild(this.frame);
  
  
  this.frame.style.left = (10 + (20 * (index % 5))) + "px";
  this.frame.style.top = (10 + (20 * (index % 5))) + "px";
  
  // Save colors.
  this.activeFrameBackgroundColor  = this.frame.style.backgroundColor;
  this.activeFrameBorderColor      = this.frame.style.borderColor;
  this.activeTitleBarColor         = this.titleBar.style.backgroundColor;
  this.activeTitleTextColor        = this.titleBar.style.color;
  this.activeClientAreaBorderColor = this.clientArea.style.borderColor;
  if (browser.isIE)
    this.activeClientAreaScrollbarColor = this.clientArea.style.scrollbarBaseColor;
  // Save images.
  this.activeButtonsImage   = this.titleBarButtons.src;
  this.inactiveButtonsImage = this.titleBarButtons.longDesc;
  // Set flags.
  this.isOpen      = false;
  this.isMinimized = false;
  this.isMaximized = false;
  this.isDragable = true;
  // Set methods.
  this.open       = winOpen;
  this.close      = winClose;
  this.minimize   = winMinimize;
  this.maximize   = winMaximize;
  this.restore    = winRestore;
  this.makeActive = winMakeActive;
  
  this.dockCreate = dockCreate;
  this.dockClick = function (event) {
		if(This.isMinimized) {
			This.restore();
			This.makeActive();
		} else {
			log("WIN: active -> " + winCtrl.active.id + " != " + This.id + " => " + (winCtrl.active.id != This.id) + " :: " + This.isMinimized);
			if(winCtrl.active.id != This.id) {
	  		log("WIN: Hello ;)");
				This.makeActive();
			} else {
	  		log("WIN: Bad :(");
				This.minimize();
			}
		}
	};
	this.dockCloseFunc = function (event) {
		This.close();
	};
  this.dockDelete = dockDelete;
  // Set up event handling.
  this.frame.parentWindow = this;
  this.frame.onmousemove  = winResizeCursorSet;
  this.frame.onmouseout   = winResizeCursorRestore;
  this.frame.onmousedown  = winResizeDragStart;
  this.titleBar.parentWindow = this;
  if(props.moveable != false) {
	  this.titleBar.onmousedown  = winMoveDragStart;
  }
  this.clientArea.parentWindow = this;
  this.clientArea.onclick      = winClientAreaClick;
  for (i = 0; i < this.titleBarMap.childNodes.length; i++)
    if (this.titleBarMap.childNodes[i].tagName == "AREA")
      this.titleBarMap.childNodes[i].parentWindow = this;
  // Calculate the minimum width and height values for resizing
  // and fix any initial display problems.
  var initLt, initWd, w, dw;
  // Save the inital frame width and position, then reposition
  // the window.
  initLt = this.frame.style.left;
  initWd = parseInt(this.frame.style.width);
  this.frame.style.left = -this.titleBarText.offsetWidth + "px";
  // For IE, start calculating the value to use when setting
  // the client area width based on the frame width.
  if (browser.isIE) {
    this.titleBarText.style.display = "none";
    w = this.clientArea.offsetWidth;
    this.widthDiff = this.frame.offsetWidth - w;
    this.clientArea.style.width = w + "px";
    dw = this.clientArea.offsetWidth - w;
    w -= dw;     
    this.widthDiff += dw;
    this.titleBarText.style.display = "";
  }
  // Find the difference between the frame's style and offset
  // widths. For IE, adjust the client area/frame width
  // difference accordingly.
  w = this.frame.offsetWidth;
  this.frame.style.width = w + "px";
  dw = this.frame.offsetWidth - w;
  w -= dw;     
  this.frame.style.width = w + "px";
  if (browser.isIE)
    this.widthDiff -= dw;
  // Find the minimum width for resize.
  this.isOpen = true;  // Flag as open so minimize call will work.
  this.minimize();
  // Get the minimum width.
  if (browser.isNS && browser.version >= 1.2)
    // For later versions of Gecko.
    this.minimumWidth = this.frame.offsetWidth;
  else
    // For all others.
    this.minimumWidth = this.frame.offsetWidth - dw;
  // Find the frame width at which or below the title bar text will
  // need to be clipped.
  this.titleBarText.style.width = "";
  this.clipTextMinimumWidth = this.frame.offsetWidth - dw;
  // Set the minimum height.
  this.minimumHeight = 1;
  // Restore window. For IE, set client area width.
  this.restore();
  this.isOpen = false;  // Reset flag.
  initWd = Math.max(initWd, this.minimumWidth);
  this.frame.style.width = initWd + "px";
  if (browser.isIE)
    this.clientArea.style.width = (initWd - this.widthDiff) + "px";
  // Clip the title bar text if needed.
  if (this.clipTextMinimumWidth >= this.minimumWidth)
    this.titleBarText.style.width = (winCtrl.minimizedTextWidth + initWd - this.minimumWidth) + "px";
  // Restore the window to its original position.
  this.frame.style.left = initLt;
  
  this.setTitle = function(cont) {
		This.titleBarText.innerHTML = cont;
		if(This.props.onDock != false) {
			This.dockText.innerHTML = cont;
		}
	}
  
  this.setContent = function(cont) {
		This.clientArea.innerHTML = cont;
	}
  
  this.addContent = function(cont) {
		This.clientArea.innerHTML += cont;
	}
	
	this.onClose = function(id) {
	
	}
	
	this.onResizeEnd = function(id) {
		
	}
	
	this.onMoveEnd = function(id) {
		
	}
	
	this.onMaximized = function(id) {
		
	}
	
	this.onRestoreFromMaximized = function(id) {
		
	}
	
	this.onMinimized = function(id) {
		
	}
	
	this.onInactivate = function() {
		if(This.dockCover) {
			This.dockCover.style.background = "#eeeeee";
		}
	}
	
	this.onActivate = function() {
		if(This.dockCover) {
			This.dockCover.style.background = "#dddddd";
		}
	}
}
//=============================================================================
// Window Methods
//=============================================================================
function winOpen() {
  if (this.isOpen)
    return;
  // Restore the window and make it visible.
  this.makeActive();
  this.isOpen = true;
  if (this.isMinimized)
    this.restore();
  this.frame.style.visibility = "visible";
  
  if(this.props.onDock != false) {
	  this.dockCreate();
  }
}
function winClose() {
  // Hide the window.
  this.frame.style.visibility = "hidden";
  desktopElement.removeChild(this.frame);
  //this.frame = null;
  this.isOpen = false;
  if(this.props.onDock != false) {
	  this.dockDelete();
  }
  
  if(winHistory.length > 1) {
  	var i = winHistory.length - 1;
	  while(i >= 0) {
			if(!winHistory[i].isMinimized && winHistory[i].isOpen) {
				winHistory[i].makeActive();
				break;
			}
			i --;
		}
	}
  
  this.onClose(this.id);
}
function winMinimize() {
  if (!this.isOpen || this.isMinimized)
    return;
  this.makeActive();
  // Save current frame and title bar text widths.
  //this.restoreFrameWidth = this.frame.style.width;
  //this.restoreTextWidth = this.titleBarText.style.width;
  // Disable client area display.
  //this.clientArea.style.display = "none";
  // Minimize frame and title bar \text widths.
  //if (this.minimumWidth)
    //this.frame.style.width = this.minimumWidth + "px";
  this.frame.style.visibility = 'hidden';
  //else
  //  this.frame.style.width = "";
  //this.titleBarText.style.width = winCtrl.minimizedTextWidth + "px";
  this.isMinimized = true;
  var ok = true;
  var i = this.index;
  /*if(winListVar.length > 1) {
	  while(ok) {
	  	i = (++i) % winListVar.length;
			if(!winListVar[i].isMinimized && winListVar[i].isOpen && winListVar[i].dock) {
				winListVar[i].makeActive();
				ok = false;
			}
			if(i == this.index - 1 || i == this.index) {
				ok = false;
			}
		}
	}*/
	
  if(winHistory.length > 1) {
  	var i = winHistory.length - 1;
	  while(i >= 0) {
			if(!winHistory[i].isMinimized && winHistory[i].isOpen) {
				winHistory[i].makeActive();
				break;
			}
			i --;
		}
	}
	
	if(this.onMinimized) {
		this.onMinimized(this.id);
	}
}

function winRestore() {
  if (!this.isOpen || !this.isMinimized)
    return;
  //this.makeActive();
  // Enable client area display.
  //this.clientArea.style.display = "";
  // Restore frame and title bar text widths.
  //this.frame.style.width = this.restoreFrameWidth;
  //this.titleBarText.style.width = this.restoreTextWidth;
  this.frame.style.visibility = 'visible';
  this.isMinimized = false;
}
function winMaximize() {
	if (!this.isOpen)
    return;
  this.makeActive();
  if(!this.isMaximized) {
	  this.restoreFrameLeft = this.frame.style.left;
	  this.restoreFrameTop = this.frame.style.top;
	  this.restoreFrameWidth = this.frame.style.width;
	  this.restoreFrameHeight = this.frame.style.height;
	  this.restoreClientAreaHeight = this.clientArea.style.height;
  	this.restoreTextWidth = this.titleBarText.style.width;
  	
	  this.frame.style.left = 0;
  	this.frame.style.top = 0;
	  this.frame.style.width = ($("#cms-body").width() - winFrameWidthOffset) + "px";
  	this.frame.style.height = ($("#cms-body").height() - winFrameHeightOffset) + "px";
  	this.clientArea.style.height = ($("#cms-body").height() - winClientAreaHeightOffset) + "px"
	  this.isMaximized = true;
	  this.isDragable = false;
		//This.onMaximized(This.id);
		if(this.onMaximized) {
			this.onMaximized(this.id);
		}
  } else {
	  this.frame.style.left = this.restoreFrameLeft;
	  this.frame.style.top = this.restoreFrameTop;
	  this.frame.style.width = this.restoreFrameWidth;
	  this.frame.style.height = this.restoreFrameHeight;
  	this.titleBarText.style.width = this.restoreTextWidth;
	  this.clientArea.style.height = this.restoreClientAreaHeight;
	  this.isMaximized = false;
	  this.isDragable = true;
	  //This.onRestoreFromMaximized(This.id);
		if(this.onRestoreFromMaximized) {
			this.onRestoreFromMaximized(this.id);
		} 
	}
}
function winMakeActive() {
  if (winCtrl.active == this)
    return;
  // Inactivate the currently active window.
  if (winCtrl.active) {
  	if(winCtrl.active.onInactivate) {
	  	winCtrl.active.onInactivate();
  	}
    winCtrl.active.frame.style.backgroundColor    = winCtrl.inactiveFrameBackgroundColor;
    winCtrl.active.frame.style.borderColor        = winCtrl.inactiveFrameBorderColor;
    winCtrl.active.titleBar.style.backgroundColor = winCtrl.inactiveTitleBarColor;
    winCtrl.active.titleBar.style.color           = winCtrl.inactiveTitleTextColor;
    winCtrl.active.clientArea.style.borderColor   = winCtrl.inactiveClientAreaBorderColor;
    if (browser.isIE)
      winCtrl.active.clientArea.style.scrollbarBaseColor = winCtrl.inactiveClientAreaScrollbarColor;
    if (browser.isNS && browser.version < 6.1)
      winCtrl.active.clientArea.style.overflow = "hidden";
    if (winCtrl.active.inactiveButtonsImage)
      winCtrl.active.titleBarButtons.src = winCtrl.active.inactiveButtonsImage;
  }
  // Activate this window.
  if(this.onActivate) {
	  this.onActivate();
  }
  this.frame.style.backgroundColor    = this.activeFrameBackgroundColor;
  this.frame.style.borderColor        = this.activeFrameBorderColor;
  this.titleBar.style.backgroundColor = this.activeTitleBarColor;
  this.titleBar.style.color           = this.activeTitleTextColor;
  this.clientArea.style.borderColor   = this.activeClientAreaBorderColor;
  if (browser.isIE)
    this.clientArea.style.scrollbarBaseColor = this.activeClientAreaScrollbarColor;
  if (browser.isNS && browser.version < 6.1)
    this.clientArea.style.overflow = "auto";
  if (this.inactiveButtonsImage)
    this.titleBarButtons.src = this.activeButtonsImage;
  this.frame.style.zIndex = ++winCtrl.maxzIndex;
  winHistory[winHistory.length] = this;
  winCtrl.active = this;
}
//=============================================================================
// Event handlers.
//=============================================================================
function winClientAreaClick(event) {
  // Make this window the active one.
  this.parentWindow.makeActive();
}
//-----------------------------------------------------------------------------
// Window dragging.
//-----------------------------------------------------------------------------
function winMoveDragStart(event) {
	if(!winCtrl.active.isDragable) 
		return;
  var target;
  var x, y;
  if (browser.isIE)
    target = window.event.srcElement.tagName;
  if (browser.isNS)
    target = event.target.tagName;
  if (target == "AREA")
    return;
  this.parentWindow.makeActive();
  // Get cursor offset from window frame.
  if (browser.isIE) {
    x = window.event.x;
    y = window.event.y;
  }
  if (browser.isNS) {
    x = event.pageX;
    y = event.pageY;
  }
  winCtrl.xOffset = winCtrl.active.frame.offsetLeft - x;
  winCtrl.yOffset = winCtrl.active.frame.offsetTop  - y;
  // Set document to capture mousemove and mouseup events.
  if (browser.isIE) {
    document.onmousemove = winMoveDragGo;
    document.onmouseup   = winMoveDragStop;
  }
  if (browser.isNS) {
    document.addEventListener("mousemove", winMoveDragGo,   true);
    document.addEventListener("mouseup",   winMoveDragStop, true);
    event.preventDefault();
  }
  winCtrl.inMoveDrag = true;
}
function winMoveDragGo(event) {
	if(!winCtrl.active.isDragable) 
		return;
  var x, y;
  if (!winCtrl.inMoveDrag)
    return;
  // Get cursor position.
  if (browser.isIE) {
    x = window.event.x;
    y = window.event.y;
    window.event.cancelBubble = true;
    window.event.returnValue = false;
  }
  if (browser.isNS) {
    x = event.pageX;
    y = event.pageY;
    event.preventDefault();
  }
  // Move window frame based on offset from cursor.
  if(x > 0) {
  	winCtrl.active.frame.style.left = (x + winCtrl.xOffset) + "px";
  }
  if((y - ($("#cms-head").height() + 9)) > 0) {
	  winCtrl.active.frame.style.top  = (y + winCtrl.yOffset) + "px";
  } else {
		winCtrl.active.frame.style.top  = (($("#cms-head").height() + 9) + winCtrl.yOffset) + "px";
	}
}
function winMoveDragStop(event) {
	if(!winCtrl.active.isDragable) 
		return;
  winCtrl.inMoveDrag = false;
  // Remove mousemove and mouseup event captures on document.
  if (browser.isIE) {
    document.onmousemove = null;
    document.onmouseup   = null;
  }
  if (browser.isNS) {
    document.removeEventListener("mousemove", winMoveDragGo,   true);
    document.removeEventListener("mouseup",   winMoveDragStop, true);
  }
  
  if(winCtrl.active.onMoveEnd) {
		winCtrl.active.onMoveEnd(winCtrl.active.id);
	}
}
//-----------------------------------------------------------------------------
// Window resizing.
//-----------------------------------------------------------------------------
function winResizeCursorSet(event) {
  var target;
  var xOff, yOff;
  if (this.parentWindow.isMinimized || winCtrl.inResizeDrag)
    return;
  // If not on window frame, restore cursor and exit.
  if (browser.isIE)
    target = window.event.srcElement;
  if (browser.isNS)
    target = event.target;
  if (target != this.parentWindow.frame)
    return;
  // Find resize direction.
  if (browser.isIE) {
    xOff = window.event.offsetX;
    yOff = window.event.offsetY;
  }
  if (browser.isNS) {
    xOff = event.layerX;
    yOff = event.layerY;
  }
  winCtrl.resizeDirection = ""
  if (yOff <= winCtrl.resizeCornerSize)
    winCtrl.resizeDirection += "n";
  else if (yOff >= this.parentWindow.frame.offsetHeight - winCtrl.resizeCornerSize)
    winCtrl.resizeDirection += "s";
  if (xOff <= winCtrl.resizeCornerSize)
    winCtrl.resizeDirection += "w";
  else if (xOff >= this.parentWindow.frame.offsetWidth - winCtrl.resizeCornerSize)
    winCtrl.resizeDirection += "e";
  // If not on window edge, restore cursor and exit.
  if (winCtrl.resizeDirection == "") {
    this.onmouseout(event);
    return;
  }
  // Change cursor.
  if (browser.isIE)
    document.body.style.cursor = winCtrl.resizeDirection + "-resize";
  if (browser.isNS)
    this.parentWindow.frame.style.cursor = winCtrl.resizeDirection + "-resize";
}
function winResizeCursorRestore(event) {
  if (winCtrl.inResizeDrag)
    return;
  // Restore cursor.
  if (browser.isIE)
    document.body.style.cursor = "";
  if (browser.isNS)
    this.parentWindow.frame.style.cursor = "";
}
function winResizeDragStart(event) {
  var target;
  // Make sure the event is on the window frame.
  if (browser.isIE)
    target = window.event.srcElement;
  if (browser.isNS)
    target = event.target;
  if (target != this.parentWindow.frame)
    return;
  this.parentWindow.makeActive();
  if (this.parentWindow.isMinimized)
    return;
  // Save cursor position.
  if (browser.isIE) {
    winCtrl.xPosition = window.event.x;
    winCtrl.yPosition = window.event.y;
  }
  if (browser.isNS) {
    winCtrl.xPosition = event.pageX;
    winCtrl.yPosition = event.pageY;
  }
  // Save window frame position and current window size.
  winCtrl.oldLeft   = parseInt(this.parentWindow.frame.style.left,  10);
  winCtrl.oldTop    = parseInt(this.parentWindow.frame.style.top,   10);
  winCtrl.oldWidth  = parseInt(this.parentWindow.frame.style.width, 10);
  winCtrl.oldHeight = parseInt(this.parentWindow.clientArea.style.height, 10);
  // Set document to capture mousemove and mouseup events.
  if (browser.isIE) {
    document.onmousemove = winResizeDragGo;
    document.onmouseup   = winResizeDragStop;
  }
  if (browser.isNS) {
    document.addEventListener("mousemove", winResizeDragGo,   true);
    document.addEventListener("mouseup"  , winResizeDragStop, true);
    event.preventDefault();
  }
  winCtrl.inResizeDrag = true;
}
function winResizeDragGo(event) {
 var north, south, east, west;
 var dx, dy;
 var w, h;
  if (!winCtrl.inResizeDrag)
    return;
  // Set direction flags based on original resize direction.
  north = false;
  south = false;
  east  = false;
  west  = false;
  if (winCtrl.resizeDirection.charAt(0) == "n")
    north = true;
  if (winCtrl.resizeDirection.charAt(0) == "s")
    south = true;
  if (winCtrl.resizeDirection.charAt(0) == "e" || winCtrl.resizeDirection.charAt(1) == "e")
    east = true;
  if (winCtrl.resizeDirection.charAt(0) == "w" || winCtrl.resizeDirection.charAt(1) == "w")
    west = true;
  // Find change in cursor position.
  if (browser.isIE) {
  	if(window.event.y < ($("#cms-head").height() + 9)) {
			window.event.y = ($("#cms-head").height() + 9);
		}
  
    dx = window.event.x - winCtrl.xPosition;
    dy = window.event.y - winCtrl.yPosition;
  }
  if (browser.isNS) {
  	//log("WIN: pageY = " + event.pageY + ", height = " + ($("#cms-head").height() + 9));
    var y = (event.pageY < $("#cms-head").height()) ? $("#cms-head").height() : event.pageY;
  
    dx = event.pageX - winCtrl.xPosition;
    dy = y - winCtrl.yPosition;
  }
  // If resizing north or west, reverse corresponding amount.
  if (west)
    dx = -dx;
  if (north)
    dy = -dy;
  // Check new size.
  w = winCtrl.oldWidth  + dx;
  h = winCtrl.oldHeight + dy;
  if (w <= winCtrl.active.minimumWidth) {
    w = winCtrl.active.minimumWidth;
    dx = w - winCtrl.oldWidth;
  }
  if (h <= winCtrl.active.minimumHeight) {
    h = winCtrl.active.minimumHeight;
    dy = h - winCtrl.oldHeight;
  }
  // Resize the window. For IE, keep client area and frame widths in synch.
  if (east || west) {
    winCtrl.active.frame.style.width = w + "px";
    if (browser.isIE)
      winCtrl.active.clientArea.style.width = (w - winCtrl.active.widthDiff) + "px";
  }
  if (north || south)
    winCtrl.active.clientArea.style.height = h + "px";
  // Clip the title bar text, if necessary.
  if (east || west) {
    if (w < winCtrl.active.clipTextMinimumWidth)
      winCtrl.active.titleBarText.style.width = (winCtrl.minimizedTextWidth + w - winCtrl.active.minimumWidth) + "px";
    else
      winCtrl.active.titleBarText.style.width = "";
  }
  // For a north or west resize, move the window.
  if (west)
    winCtrl.active.frame.style.left = (winCtrl.oldLeft - dx) + "px";
  if (north)
    winCtrl.active.frame.style.top  = (winCtrl.oldTop  - dy) + "px";
  if (browser.isIE) {
    window.event.cancelBubble = true;
    window.event.returnValue = false;
  }
  if (browser.isNS)
    event.preventDefault();
}
function winResizeDragStop(event) {
  winCtrl.inResizeDrag = false;
  // Remove mousemove and mouseup event captures on document.
  if (browser.isIE) {
    document.onmousemove = null;
    document.onmouseup   = null;
  }
  if (browser.isNS) {
    document.removeEventListener("mousemove", winResizeDragGo,   true);
    document.removeEventListener("mouseup"  , winResizeDragStop, true);
  }
  
  if(winCtrl.active.onResizeEnd) {
		winCtrl.active.onResizeEnd(winCtrl.active.id);
	}
}
//=============================================================================
// Dock bar
//=============================================================================
function dockCreate() {
	this.dockCover = document.createElement('div');
	this.dockCover.className = 'dock-item';
	this.dockText = document.createElement('span');
	this.dockText.className = 'dock-text';
	this.dockText.innerHTML = $(this.titleBar).find(".titleBarText").html();
	this.dockClose = document.createElement('img');
	this.dockClose.src = "/images/cms/window/button-3b.gif";
	this.dockClose.className = 'dock-close-x';
	this.dockClose.title = 'Close window';
	this.dockIcon = document.createElement('img');
	
	addEvent(this.dockCover, 'click', this.dockClick, false);
	addEvent(this.dockClose, "click", this.dockCloseFunc, false);
	
	var as = $('.cms-menu a').get();
	for(var i = 0; i < as.length; i ++) {
		if(as[i].href == this.url) {
			this.dockIcon.src = $(as[i]).css('background-image').replace(/^url|[\(\)]/g, '');
			break;
		}
	}
	
	this.dockCover.appendChild(this.dockIcon);
	this.dockCover.appendChild(this.dockText);
	this.dockCover.appendChild(this.dockClose);
	document.getElementById(dockElementId).appendChild(this.dockCover);
	//this.dockClose();
}

function dockDelete() {
	document.getElementById(dockElementId).removeChild(this.dockCover);
}

//=============================================================================
// Utility functions.
//=============================================================================
function winFindByClassName(el, className) {
  var i, tmp;
  if (el.className == className)
    return el;
  // Search for a descendant element assigned the given class.
  for (i = 0; i < el.childNodes.length; i++) {
    tmp = winFindByClassName(el.childNodes[i], className);
    if (tmp != null)
      return tmp;
  }
  return null;
}
//]]>