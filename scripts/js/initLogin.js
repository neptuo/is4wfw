/**
 *
 *  @author  Marek Fišera marek.fisera@email.cz
 *  @date    2009/06/19
 *
 */
var SaveNameCheckBox = null;

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
	setRememberName();
	tryToLoadName();
	setActiveField();
}

function setActiveField() {
	var inputs = document.getElementsByTagName('input');
	for(var i = 0; i < inputs.length; i ++) {
		if((inputs[i].type == 'text' || inputs[i].type == 'password') && inputs[i].value.length == 0) {
			inputs[i].focus();
			break;
		}
	}
}

function setRememberName() {
	var submit = null;
	var inputs = document.getElementsByTagName('input');
	for(var i = 0; i < inputs.length; i ++) {
		if(inputs[i].type == 'submit' && inputs[i].value == 'Log in') {
			submit = inputs[i];
		}
	}
	
	if(submit) {
		var checkBox = document.createElement('input');
		checkBox.type = 'checkbox';
		checkBox.id = 'id-10010-checkbox';
		var label = document.createElement('label')
		label.setAttribute('for', 'id-10010-checkbox');
		label.innerHTML = 'Remember my name.';
		
		submit.parentNode.appendChild(checkBox);
		submit.parentNode.appendChild(label);
		
		SaveNameCheckBox = checkBox;
	}
	
	addEvent(submit.parentNode.parentNode, 'submit', tryToSaveName, false);
}

function tryToLoadName() {
	var userName = '';
	cookies = document.cookie.split(";");
	for(i in cookies) {
	  cookie = cookies[i].split("=");
  	if (cookie[0] == " loginUserName") { 
			userName = cookie[1];
		}
	}
	if(userName != '') {
		document.getElementById('username').value = userName;
		SaveNameCheckBox.checked = true;
	}
}

function tryToSaveName(event) {
	if(SaveNameCheckBox.checked == true) {
		var date = new Date();
  	date.setTime((date.getTime() + 1000 * 60 * 60 * 24 * 3));
		document.cookie = "loginUserName=" + document.getElementById('username').value + "; expires=" + date.toGMTString();
	}
}