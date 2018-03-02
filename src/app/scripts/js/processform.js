/**
 *
 *  @author  Marek Fišera marek.fisera@email.cz
 *  @date    2009/06/24
 *
 */
function ProcessForm(form, startPageId, responseTemplate) {
	if(form == null || form.tagName != 'FORM') {
		throw "Passed item isn't form element!";
		return;
	}

	var Inited = false;
	var This = this;
	var Form = form;
	var SubmitButtons = new Array();
	var QueryString = new String();
	var XmlHttp = new Rxmlhttp();
	var IsLoadingNow = false;
	
	var AdditionalQuery = new String();
	var StartPageId = (startPageId != null) ? startPageId : -1;
	var UseStartPageId = true;
	var ResponseTemplate = (responseTemplate != null) ? responseTemplate : '';
	var UpdateLocation = true;
	
	var UseHashUrlIfFormActionIsPlain = false;
	var FileUploadform = false;
	
	/**
	 *
	 *	Init process form.	 
	 *
	 */	 	 	
	this.init = function() {
		if(!Inited) {
			This.addEvent(Form, 'submit', function(event) { This.stopEvent(event) }, false);
			var submits = Form.getElementsByTagName('input');
			for(var i = 0; i < submits.length; i++) {
				//console.log("New input: " + submits[i].name + ", " + submits[i].type);
				if(submits[i].type == "submit" || submits[i].type == "image") {
					This.addSubmitButton(submits[i], 'click');
				}
				if(submits[i].type == 'file') {
					FileUploadform = true;
				}
			}
			XmlHttp.setAsync(true);
			if(Form.method.length == 0) {
				XmlHttp.setMethod('post');
			} else {
				XmlHttp.setMethod(Form.method);
			}
			XmlHttp.onSuccess(This.onSuccessInner);
			XmlHttp.onError(This.onErrorInner);
			Inited = true;
		}
	}
	
	/**
	 *
	 *	Sets addtional query.
	 *	@param		str						query string	 
	 *	 
	 */	 	 	
	this.setQuery = function(str) {
		AdditionalQuery = str;
	}
	
	/**
	 *
	 *	Sets use start page id parameter.
	 *	
	 *	@param		use						use start page id parameter	 	 
	 *
	 */	 	 	 	
	this.setUseStartPageId = function(use) {
		if(use == true) {
			UseStartPageId = true;
		} else {
			UseStartPageId = false;
		}
	}
	
	/**
	 *
	 *	Set start page id for requested paged.
	 *	
	 *	@param		pageId				start page id	 	 
	 *	 
	 */	 	 	
	this.setStartPageId = function(pageId) {
		if(pageId != null) {
			StartPageId = pageId;
		}
	}
	
	/**
	 *
	 *	Set response template for requested paged.
	 *	
	 *	@param		responseTemplate		 	 response template, possible value xml.
	 *	 
	 */	 	 	
	this.setResponseTemplate = function(responseTemplate) {
		if(responseTemplate != null) {
			ResponseTemplate = responseTemplate;
		}
	}
	
	/**
	 *
	 *	Set update location.
	 *	
	 *  @param		updateLocation					new value	 	 
	 *
	 */	 	 	 	 	
	this.setUpdateLocation = function(updateLocation) {
		if(updateLocation == false) {
			UpdateLocation = false;
		} else if(updateLocation == true) {
			UpdateLocation = true;
		}
	}
	
	/**
	 *
	 *	Set using hash instead of plain form action.
	 *
	 *	@param		value										new value.
	 *
	 */	 	 	 	 	 	
	this.setUseHashUrlIfFormActionIsPlain = function(value) {
		if(value != null) {
			UseHashUrlIfFormActionIsPlain = value;
		}
	}
	
	/**
	 *
	 *	Adds submit button.
	 *	@param		button		dom element
	 *	@param		event			event to submit on, without "on"
	 *
	 */	 	 	 	
	this.addSubmitButton = function(button, event) {
		if(button != null) {
			SubmitButtons[SubmitButtons.length] = button;
			This.addEvent(button, event, This.onSubmitButtonInner, false);
		} else {
			throw "Passed item is null!";
			return;
		}
	}
	
	/**
	 *
	 *	Removes submit button.
	 *	@param		button		dom element
	 *	@param		event			event to submit on, without "on"
	 *
	 */	 	 	 	
	this.removeSubmitButton = function(button, event) {
		if(button != null) {
			for(var i = 0; i < SubmitButtons.length; i ++) {
				if(SubmitButtons[i] == button) {
					This.removeEvent(SubmitButtons[i], event, This.onSubmitButton, false);
					SubmitButtons.remove(i);
					break;
				}
			}
		} else {
			throw "Passed item is null!";
			return;
		}
	}
	
	/**
	 *
	 *	Prepares query string before form submit.
	 *
	 */	 	 	 	
	this.createQueryString = function() {
		var queryString = '';
		var inputs = Form.getElementsByTagName('input');
		for(var i = 0; i < inputs.length; i ++) {
			if(inputs[i].type == 'radio' || inputs[i].type == 'checkbox') {
				if(inputs[i].checked == true) {
					if(queryString.length != 0) {
						queryString += '&';
					}
					if(inputs[i].value.length != 0) {
						if(inputs[i].name.length != 0) { 
							inputs[i].name = inputs[i].name.replace(/&/g, '%26');
							inputs[i].value = inputs[i].value.replace(/&/g, '%26');
							inputs[i].value = inputs[i].value.replace(/\+/g, '%2B');
							queryString += inputs[i].name + '=' + encodeURI(inputs[i].value);
						}
					} else {
						if(inputs[i].name.length != 0) {
							inputs[i].name = inputs[i].name.replace(/&/g, '%26');
							inputs[i].name = inputs[i].name.replace(/\+/g, '%2B');
							queryString += inputs[i].name + '=on';
						}
					}
				}
			} else if(inputs[i].type != 'submit' && inputs[i].type != 'button' && inputs[i].type != 'file' && inputs[i].type != 'reset') {
				if(queryString.length != 0) {
					queryString += '&';
				}
				if(inputs[i].name.length != 0) {
					inputs[i].name = inputs[i].name.replace(/&/g, '%26');
					inputs[i].value = inputs[i].value.replace(/&/g, '%26');
					inputs[i].value = inputs[i].value.replace(/\+/g, '%2B');
					//queryString += inputs[i].name + '=' + encodeURI(inputs[i].value);
					queryString += inputs[i].name + '=' + inputs[i].value;
				}
			}
		}
		var textareas = Form.getElementsByTagName('textarea');
		for(var i = 0; i < textareas.length; i ++) {
			if(textareas[i].name.length != 0) {
				if(queryString.length != 0) {
					queryString += '&';
				}
				textareas[i].name = textareas[i].name.replace(/&/g, '%26');
				textareas[i].value = textareas[i].value.replace(/&/g, '%26');
				textareas[i].value = textareas[i].value.replace(/\+/g, '%2B');
				//queryString += textareas[i].name + '=' + encodeURI(textareas[i].value);
				queryString += textareas[i].name + '=' + textareas[i].value;
			}
		}
		var options = Form.getElementsByTagName('option');
		for(var i = 0; i < options.length; i ++) {
			if(options[i].selected == true) {
				var value = ((options[i].value.length == 0) ? options[i].innerHTML : options[i].value);
				if(options[i].parentNode.tagName == 'SELECT') {
					if(queryString.length != 0) {
						queryString += '&';
					}
					options[i].parentNode.name = options[i].parentNode.name.replace(/&/g, '%26');
					options[i].parentNode.name = options[i].parentNode.name.replace(/\+/g, '%2B');
					value = value.replace(/&/g, '%26');
					value = value.replace(/\+/g, '%2B');
					//queryString += options[i].parentNode.name + '=' + encodeURI(value);
					queryString += options[i].parentNode.name + '=' + value;
				} else if(options[i].parentNode.parentNode.tagName == 'SELECT') {
					if(queryString.length != 0) {
						queryString += '&';
					}
					parentNode.parentNode.name = parentNode.parentNode.name.replace(/&/g, '%26');
					parentNode.parentNode.name = parentNode.parentNode.name.replace(/\+/g, '%2B');
					value = value.replace(/&/g, '%26');
					value = value.replace(/\+/g, '%2B');
					//queryString += options[i].parentNode.parentNode.name + '=' + encodeURI(value);
					queryString += options[i].parentNode.parentNode.name + '=' + value;
				} else if(options[i].parentNode.parentNode.parentNode.tagName == 'SELECT') {
					if(queryString.length != 0) {
						queryString += '&';
					}
					options[i].parentNode.parentNode.parentNode.name = options[i].parentNode.parentNode.parentNode.name.replace(/&/g, '%26');
					options[i].parentNode.parentNode.parentNode.name = options[i].parentNode.parentNode.parentNode.name.replace(/\+/g, '%2B');
					value = value.replace(/&/g, '%26');
					value = value.replace(/\+/g, '%2B');
					//queryString += options[i].parentNode.parentNode.parentNode.name + '=' + encodeURI(value);
					queryString += options[i].parentNode.parentNode.parentNode.name + '=' + value;
				}
			}
		}
		This.QueryString = queryString;
	}
	
	/**
	 *
	 *	Called on every submit button click.
	 *
	 *	@param		event					event object
	 *	 	 
	 */	 	 	 	
	this.onSubmitButtonInner = function(event) {
		if(!IsLoadingNow && This.onSubmitButton(event, Form)) {
			var button = ((event.srcElement) ? event.srcElement : event.target);
			This.disableAllSubmitButtons();
			This.createQueryString();
			if(button != null && button.tagName == 'INPUT' && (button.type == 'submit' || button.type == 'button')) {
				if(This.QueryString.length != 0) {
					This.QueryString += '&';
				}
				This.QueryString += button.name + '=' + button.value;
			}
		
			//if(navigator.appName == "Microsoft Internet Explorer") {
			This.onSubmit(event);
			//}
		}
	}
	
	/**
	 *
	 *	Redefine this function called on submit button click.
	 *	
	 *	@param		event					event object
	 *	@param		form					html element
	 *	@return		true, continue to submit the form, false, break the submit 	 
	 *
	 */
	this.onSubmitButton = function(event, form) {
		return window.confirm('Do U really want to submit the form');
	}
	
	/**
	 *
	 *	Redefine this function called before form submit.
	 *	
	 *	@param		event					event object
	 *	@param		form					html element
	 *
	 */	 	 	 	
	this.beforeSubmit = function(event, form) {
		alert('Submitting form ...');
	}
	
	/**
	 *
	 *	Redefine this function called before ajax upload.
	 *
	 *	@param		event					event object
	 *	@param		form					html element
	 *
	 */	 	 	 	
	this.beforeUpload = function(event, form) {
		alert('Uploading form ...');
	}
	
	/**
	 *
	 *	Redefine this function called on request success.
	 *	
	 *	@param		xmlHttp				Rxmlhttp object	 	 
	 *
	 */
	this.onSuccess = function(xmlHttp) {
		alert('Request successfully completed!');
	}
	
	/**
	 *
	 *	Redefine this function called on upload success.	 	 
	 *
	 */
	this.onSuccessUpload = function() {
		alert('Upload successfully completed!');
	}
	
	/**
	 *
	 *	Redefine this function called on request error.
	 *	
	 *	@param		xmlHttp				Rxmlhttp object	 	 
	 *
	 */
	this.onError = function(xmlHttp) {
		alert('Some error occured in request!');
	}
	
	/**
	 *
	 *	Called on form submit.
	 *	
	 *	@param		event					event object	 	 
	 *
	 */	 	 	 	
	this.onSubmit = function(event) {
		if(!IsLoadingNow) {
			if(FileUploadform == true) {
				var iframe = document.createElement('iframe');
				iframe.name = 'ajaxFileUploadIFrame';
				iframe.id = 'ajaxFileUploadIFrame';
				iframe.style.display = 'none';
				document.body.appendChild(iframe);
				
				Form.target = 'ajaxFileUploadIFrame';
				if(Form.action.indexOf('?') != -1) {
					Form.action += "&ajaxUpload=true";
				} else {
					Form.action += "?ajaxUpload=true";
				}
				Form.submit();
				
				addEvent(iframe, 'load', This.onSuccessUploadInner, false);

				This.beforeUpload(event, Form);
				
				return true;
			}
		
			var url = '';
			if(Form.action == '') {
				if(UseHashUrlIfFormActionIsPlain && window.location.href.indexOf('#') != -1) {
					url = window.location.protocol + '//' + window.location.host + window.location.href.substring(window.location.href.indexOf('#') + 1, window.location.href.length);
				} else {
					url = window.location.href;
				}
			} else {
				url = Form.action;
			}
			var pos = window.location.href.indexOf('#');
			if(pos == -1) {
				pos = window.location.href.length;
			}
			var upos = url.indexOf('?__');
			if(upos == -1) {
				upos = url.length;
			}
			
			This.beforeSubmit(event, Form);
			
			if(UseStartPageId && StartPageId && StartPageId != -1) {
				if(url.indexOf('?') == -1) {
					url += '?__START_ID=' + StartPageId;
				} else {
					url += '&__START_ID=' + StartPageId;
				}
			}
			if(ResponseTemplate != '') {
				if(url.indexOf('?') == -1) {
					url += '?__TEMPLATE=' + ResponseTemplate;
				} else {
					url += '&__TEMPLATE=' + ResponseTemplate;
				}
			}
			
			if(url[0] != '/') {
				Hash = '#' + url.substring(url.substring(9, url.length).indexOf('/') + 9, upos);
			} else {
				Hash = '#' + url.substring(0, url.indexOf('?'));
			}
			if(UpdateLocation) {
				window.location.href = window.location.href.substring(0, pos) + Hash;
			}
			//XmlHttp.sendheaders(This.QueryString.length);
			XmlHttp.loadPage(url, This.QueryString);
			
			IsLoadingNow = true;
		}	
		This.stopEvent(event);
	}
	
	/**
	 *
	 *	Called on success.	 
	 *
	 *	@param		xmlHttp				Rxmlhttp object	 
	 */	 	 	
	this.onSuccessInner = function(xmlHttp) {
		This.onSuccess(xmlHttp, Form);
		IsLoadingNow = false;
		This.enableAllSubmitButtons();
	}
	
	/**
	 *
	 *	Called on upload success.	 	 
	 *
	 */
	this.onSuccessUploadInner = function() {
		This.onSuccessUpload(Form);
		This.enableAllSubmitButtons();
	}
	
	/**
	 *
	 *	Called on error.	 
	 *
	 *	@param		xmlHttp				Rxmlhttp object	 
	 */	 	 	
	this.onErrorInner = function(xmlHttp) {
		This.onError(xmlHttp, Form);
		IsLoadingNow = false;
		This.enableAllSubmitButtons();
	}
	
	/**
	 *
	 *	Disable all submit buttons in form
	 *
	 */	 	 	 	
	this.disableAllSubmitButtons = function() {
		for(var i = 0; i < SubmitButtons.length; i ++) {
			SubmitButtons[i].disabled = "disabled";
		}
	}
	
	/**
	 *
	 *	Enable all submit buttons in form
	 *
	 */	 	 	 	
	this.enableAllSubmitButtons = function() {
		for(var i = 0; i < SubmitButtons.length; i ++) {
			SubmitButtons[i].disabled = "";
		}
	}
	
	/**
	 *
	 *	Adds event to element
	 *	@param		obj						dom object to add event to
	 *	@param		ev						event name, without "on"
	 *	@param		func					function to call
	 *	@param		b							boolean -> bubble	 	 	 	 
	 *	 
	 */
	this.addEvent = function (obj, ev, func, b) {
		if(ev != null && ev.length > 1) {
			if(ev.substring(0,2) == 'on') {
				ev = ev.substring(2, ev.length);
			} 
		} else {
			ev = 'click';
		}
    if(obj.addEventListener) {
      obj.addEventListener(ev, func, b);
    } else {
      obj.attachEvent("on" + ev, func);
    }
  }
	
	/**
	 *
	 *	Removes event form element
	 *	@param		obj						dom object to add event to
	 *	@param		ev						event name, without "on"
	 *	@param		func					function to call
	 *	@param		b							boolean -> bubble	 	 	 	 
	 *	 
	 */
  this.removeEvent = function(obj, ev, func, b) {
  	if(ev != null && ev.length > 1) {
			if(ev.substring(0,2) == 'on') {
				ev = ev.substring(2, ev.length);
			} 
		} else {
			ev = 'click';
		}
    if(obj.removeEventListener) {
      obj.removeEventListener(ev, func, b);
    } else {
      obj.deattachEvent("on" + ev, func);
    }
	}
	
	/**
	 *
	 *	Stops event.
	 *	
	 *	@param		event					event to stop
	 *
	 */	 	 	 	
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

Array.prototype.remove = function(from, to) {
  var rest = this.slice((to || from) + 1 || this.length);
  this.length = from < 0 ? this.length + from : from;
  return this.push.apply(this, rest);
};