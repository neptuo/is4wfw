/**
 *
 *  @author  Marek Fišera marek.fisera@email.cz
 *  @date    2009/07/19
 *
 */
function Links(root, startPageId, responseTemplate, disableInit) {
	if(root == null) {
		throw "Passed item isn't dom element!";
		return;
	}
	
	var Inited = false;
	var IsLoadingNow = false;
	var This = this;
	var Root = root;
	var Links = new Array();
	var XmlHttp = new Rxmlhttp();
	var AdditionalQuery = new String();
	var StartPageId = (startPageId != null) ? startPageId : -1;
	var UseStartPageId = true;
	var ResponseTemplate = (responseTemplate != null) ? responseTemplate : '';
	var DisableInit = (disableInit != null) ? disableInit : '';
	var UpdateLocation = true;
	var Hash = '';
	
	/**
	 *
	 *	Init process form.	 
	 *
	 */
	this.init = function() {
		if(!Inited) {
			if(DisableInit != true) {
				var links = root.getElementsByTagName('A');
				for(var i = 0; i < links.length; i ++) {
					if(links[i].target != '_blank') {
						This.addLink(links[i], 'click');
					}
				}
			}
			XmlHttp.setAsync(true);
			XmlHttp.setMethod('get');
			XmlHttp.onSuccess(This.onSuccessInner);
			XmlHttp.onError(This.onErrorInner);
			
			Inited = true;
		}
	}
	
	/**
	 *
	 *	Loads page that is in url after #.
	 *
	 */	 	 	 	
	this.loadDefault = function() {
		if(window.location.href.indexOf('#') != -1) {
			var url = window.location.protocol + '//' + window.location.host + window.location.href.substring(window.location.href.indexOf('#') + 1, window.location.href.length);
			This.loadPage(url);
		}
	}
	
	/**
	 *
	 *	Adds event to link.
	 *	@param		object				object (anchor) to add event to
	 *	@param		event					event name, without "on"
	 *
	 */	 	 	 	 	
	this.addLink = function(object, event) {
		if(object != null && object.tagName && object.tagName == "A") {
			Links[Links.length] = object;
			This.addEvent(object, event, This.onEvent, false);
		}
	}
	
	/**
	 *
	 *	Adds event to links.
	 *	@param		root					root element to find elements in to add event to
	 *	@param		event					event name, without "on"
	 *
	 */
	this.addLinks = function(root, event) {
		var links = root.getElementsByTagName('A');
			for(var i = 0; i < links.length; i ++) {
			if(!Links.inArray(links[i]) && links[i].target != '_blank') {
				This.addLink(links[i], 'click');
			}
		}
	}
	
	/**
	 *
	 *	Removes event form link.
	 *	@param		object				object (anchor) to add event to
	 *	@param		event					event name, without "on"
	 *
	 */	 	 	 	 	
	this.removeLink = function(object, event) {
		if(object != null && object.tagName && object.tagName == "A") {
			for(var i = 0; i < Links.length; i ++) {
				if(Links[i] == object) {
					This.removeEvent(Links[i], event, This.onEvent, false);
					Links.remove(i);
					break;
				}
			}
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
	 *	Called when event is fired.
	 *	@param		event					dom event object	 
	 *
	 */	 	 	 	
	this.onEvent = function(event) {
		if(!IsLoadingNow) {
			var url = '';
			
			element = ((event.srcElement) ? event.srcElement : event.target);
			if(element.tagName != 'A') {
				element = element.parentNode;
				if(element.tagName != 'A') {
					element = element.parentNode;
					if(element.tagName != 'A') {
						element = element.parentNode;
						if(element.tagName != 'A') {
							throw "Cannot find anchor element!";
						} else {
							url = element.href;
						}
					} else {
						url = element.href;
					}
				} else {
					url = element.href;
				}
			} else {
				url = element.href;
			}
			
			if(url && url.length && url.length > 0) {
				if(AdditionalQuery && AdditionalQuery.length && AdditionalQuery.length > 0) {
					if(url.indexOf('?') == -1) {
						url += '?' + AdditionalQuery;
					} else {
						url += '&' + AdditionalQuery;
					}
				}
				if(UseStartPageId == true) {
					This.loadPage(url, true);
				} else {
					This.loadPage(url, false);
				}
			}
		}
		
		This.stopEvent(event);
	}
	
	/**
	 *
	 *	Load specific page.
	 *	@param		url							server page url
	 *
	 */	 	 	 	
	this.loadPage = function(url) {
		var pos = window.location.href.indexOf('#');
		if(pos == -1) {
			pos = window.location.href.length;
		}
		var upos = url.indexOf('?__');
		if(upos == -1) {
			upos = url.length;
		}
		This.beforeRequest(url);
		
		
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
		
		Hash = '#' + url.substring(url.substring(9, url.length).indexOf('/') + 9, upos);
		if(UpdateLocation) {
			window.location.href = window.location.href.substring(0, pos) + Hash;
		}
		XmlHttp.loadPage(url);
	}
	
	this.getHash = function() {
		return Hash;
	}
	
	/**
	 *
	 *	Redefine this function before form submit.
	 *	@param		event					dom event object
	 *
	 */	 	 	 	
	this.beforeRequest = function(event) {
		alert('Submitting request ...');
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
	 *	Called on success.	 
	 *
	 *	@param		xmlHttp				Rxmlhttp object	 
	 */	 	 	
	this.onSuccessInner = function(xmlHttp) {
		This.onSuccess(xmlHttp);
		IsLoadingNow = false;
	}
	
	/**
	 *
	 *	Called on error.	 
	 *
	 *	@param		xmlHttp				Rxmlhttp object	 
	 */	 	 	
	this.onErrorInner = function(xmlHttp) {
		This.onError(xmlHttp);
		IsLoadingNow = false;
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

Array.prototype.inArray = function(el) {
	if(el == null) return false;
	for(var i = 0; i < this.length; i ++) {
		if(this[i] == el) {
			return true;
		}
	}
	return false;
};