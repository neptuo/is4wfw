/**
 *
 *  @author  Marek Fišera marek.fisera@email.cz
 *  @date    2009/10/04
 *
 */
function addEvent (obj, ev, func, b) {
  if(obj.addEventListener) {
    obj.addEventListener(ev, func, b);
  } else {
    obj.attachEvent("on" + ev, func);
  }
}

Event.domReady.add(init);

function init(event) {
	initForms(document);
	initEditors(event);
	initClosers(event);
	fileNameInit(event);
	initCountDown(event);
	initClearCache(event);
}

//addEvent(window, "load", initEditors, false);

var Editors = new Array();

function initEditors(event) {
  var tas = document.getElementsByTagName('textarea');

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

//addEvent(window, "load", initClosers, false);

function initClosers(event) {
  var divs = document.getElementsByTagName('div');
  for(var i = 0; i < divs.length; i ++) {
    if(divs[i].className.indexOf('frame frame-cover') != -1) {
      new Closer(divs[i]);
    }
  }
}

//addEvent(window, "load", fileNameInit);

function fileNameInit(event) {
	var inpts = document.getElementsByTagName('input');
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
	cdl.innerHTML = '';
	var cd = new CountDown('Login session <br />expires in: ', 15 * 60, cdl);
	cd.start();
}

function initClearCache(event) {
	var cover = document.getElementById('clear-url-cache');
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
		
		addEvent(selectAll, 'click', clickClearCacheSelectAll, false);
		addEvent(unselectAll, 'click', clickClearCacheUnselectAll, false);
		submit.appendChild(selectAll);
		submit.appendChild(unselectAll);
	}
}