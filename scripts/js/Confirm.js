/**
 *
 *  @author  Marek Fišera marek.fisera@email.cz
 *  @date    2009/06/18
 *
 */
function addEvent (obj, ev, func, b) {
  if(obj.addEventListener) {
    obj.addEventListener(ev, func, b);
  } else {
    obj.attachEvent("on" + ev, func);
  }
}

function stopEvent (event) {
  if(navigator.appName != "Microsoft Internet Explorer") {
    event.preventDefault();
    window.event.returnValue=false;
  } else {
	if(typeof event.preventDefault == 'function') {
		event.preventDefault();
	}
	if(typeof event.stopPropagation == 'function') {
		event.stopPropagation();
	}
    event.cancelBubble = true;
    event.returnValue = false;
    window.event.returnValue=false;
  }
}

//addEvent(window, "load", initConfirm, false);

function initConfirm(event) {
  confs = document.getElementsByTagName('input');
  var cofs = new Array();
  for(var i = 0; i < confs.length; i ++) {
    if(confs[i].className == "confirm") {
      cofs[cofs.length] = confs[i];
      function onClickConfirm(event) {
				var elm = ((event.srcElement) ? event.srcElement : event.target);
				var title = 'this';
				if(elm && elm.title && elm.title.length != 0) {
					title = '\n\n\t"' + elm.title + '"\n\n';
				}
				//stopEvent(event); 
				if(!confirm("Do you really want to do " + title + "?")) {
					stopEvent(event);
					return false;
				} else {
					return true;
				}
			}
			
			addEvent(confs[i], 'click', onClickConfirm, false);
    }
  }
}