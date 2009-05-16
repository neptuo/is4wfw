function addEvent (obj, ev, func, b) {
  if(obj.addEventListener) {
    obj.addEventListener(ev, func, b);
  } else {
    obj.attachEvent("on" + ev, func);
  }
}

addEvent(window, "load", initConfirm, false);

function initConfirm(event) {
  confs = document.getElementsByTagName('input');
  var cofs = new Array();
  for(var i = 0; i < confs.length; i ++) {
    if(confs[i].className == "confirm") {
      cofs[cofs.length] = confs[i];
      confs[i].onclick = function(event) { return confirm("Opravdu?"); }
    }
  }
}