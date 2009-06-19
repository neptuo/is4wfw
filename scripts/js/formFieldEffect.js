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

function initForms(root) {
	if(root.getElementsByTagName) {
		var inputs = root.getElementsByTagName('input');
		for(var i = 0; i < inputs.length; i ++) {
			addEvent(inputs[i], 'focus', formFieldOnFocus, false);
			addEvent(inputs[i], 'blur', formFieldOnBlur, false);
		}
		var inputs = root.getElementsByTagName('select');
		for(var i = 0; i < inputs.length; i ++) {
			addEvent(inputs[i], 'focus', formFieldOnFocus, false);
			addEvent(inputs[i], 'blur', formFieldOnBlur, false);
		}
		var inputs = root.getElementsByTagName('textarea');
		for(var i = 0; i < inputs.length; i ++) {
			addEvent(inputs[i], 'focus', formFieldOnFocus, false);
			addEvent(inputs[i], 'blur', formFieldOnBlur, false);
		}
	}
}

function formFieldOnFocus(event) {
	var input = ((event.srcElement) ? event.srcElement : event.target);
	if(input) {
		if(input.className.indexOf('editor-textarea') != -1) {
			input = input.parentNode.parentNode;
		}
		input.className += (input.className.length > 0) ? ' input-focused' : 'input-focused';
	}
}

function formFieldOnBlur(event) {
	var input = ((event.srcElement) ? event.srcElement : event.target);
	if(input) {
		if(input.className.indexOf('editor-textarea') != -1) {
			input = input.parentNode.parentNode;
		}
		input.className += (input.className.length > 0) ? ' input-focused' : 'input-focused';
		input.className = input.className.substring(0, input.className.indexOf('input-focused'));
		if(input.className.length == 1) {
			input.className = '';
		}
	}
}