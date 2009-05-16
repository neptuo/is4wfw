// JavaScript Document

function Closer(frameCover) {
  var Own = this;
  var FrameCover = frameCover;
  var Anchor;
  var Head;
  var Content;
  
  this.init = function() {
    var elms = frameCover.getElementsByTagName('*');
    for(var i = 0; i < elms.length; i ++) {
      elm = elms[i];
      if(elm.className.indexOf("frame-head") != -1) {
        Head = elm;
      } else if(elm.className.indexOf("frame-body") != -1) {
        Content = elm;
      } else if(elm.className.indexOf("click-able-roll") != -1) {
        Anchor = elm;
      }
    }
    
    Own.addEvent(Head, "click", Own.headClick, false);
  }
  
  this.headClick = function(event) {
    if(Content.style.display == "none") {
      if(FrameCover.className.indexOf(" closed-frame") != -1) {       
        FrameCover.className = FrameCover.className.substring(0,FrameCover.className.indexOf(" closed-frame"));
      }
      Content.style.display = "block";
    } else {
      FrameCover.className += " closed-frame";
      Content.style.display = "none";
    }
    Own.stopEvent(event);
  }

  this.addEvent = function (obj, ev, func, b) {
		if(obj.addEventListener) {
			obj.addEventListener(ev, func, b);
		} else {
			obj.attachEvent("on" + ev, func);
		}
	}
	
	this.stopEvent = function (event) {
    if(navigator.appName != "Microsoft Internet Explorer") {
      event.stopPropagation();
      event.preventDefault();
    } else {
      event.cancelBubble = true;
      event.returnValue = false;
    }
  }
  
  this.init();
}
