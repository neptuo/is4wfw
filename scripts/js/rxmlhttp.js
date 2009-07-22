function Rxmlhttp () {
	/**
	 * global properties 
	 */
	var Rxmlhttp = false;
	var content_type = 'application/x-www-form-urlencoded';
	var own = this;
	var method = "GET";
	var async = true;
	var action = null;
	var error = null;
	
	/* create xmlhttp object */
	try {
		Rxmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
	} catch (e1) {
		try {
			Rxmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		} catch (e2) {
			Rxmlhttp = false;
		}
	}
	
	if (!Rxmlhttp && typeof XMLHttpRequest != 'undefined') {
		Rxmlhttp = new XMLHttpRequest();
	}
	
	/**
	 * set request to synchronous / asynchronous
	 * @param bool newVal
	 * @return Rxmlhttp object
	 */
	this.setAsync = function(newVal){
		async = newVal;
		return this;
	}
	
	/**
	 * set request to GET / POST
	 * @param string newVal "GET"/"POST"
	 * @todo RAISE EXCEPTION WHEN INVALID VALUE
	 * @return Rxmlhttp object
	 */
	this.setMethod = function(newVal){
		method = newVal;
		return this;
	}
	
	/**
	 * Function to call on success request
	 * @param function newVal have to accept one argument - Rxmlhttp object
	 * @return Rxmlhttp object 
	 */
	this.onSuccess = function(newVal){
		action = newVal;
		return this;
	}
	
	/**
	 * Function to call on error in request
	 * @param function newVal have to accept one argument - Rxmlhttp object
	 * @return Rxmlhttp object 
	 */
	this.onError = function(newVal){
		error = newVal;
		return this;
	}
	
	/**
	 * private, sending headers
	 * @return Rxmlhttp object
	 */
	this.sendheaders = function(length){
		Rxmlhttp.setRequestHeader("If-Modified-Since", "Fri, Jan 01 1900 00:00:00 GMT");
		Rxmlhttp.setRequestHeader("Pragma", "no-cache");
		Rxmlhttp.setRequestHeader("Cache-Control", "no-cache");
		Rxmlhttp.setRequestHeader("Content-Type", content_type);
		Rxmlhttp.setRequestHeader("Content-Length", length);
		Rxmlhttp.setRequestHeader("Connection", "close");
		return this;
	}
	
	/**
	 * main function
	 * set request to serverPage and calls onsuccess function
	 * @param string serverPage
	 * @param string postParams
	 * @return Rxmlhttp object
	 */
	this.loadPage = function (serverPage, postParams) {
		Rxmlhttp.open(method, serverPage, async);		
		this.sendheaders(serverPage.length);
		
		if(async){
			Rxmlhttp.onreadystatechange = this.loadedAction;
		}
		else{
			document.body.style.cursor = "wait";
		}
		
		Rxmlhttp.send(postParams);
		
		if(!async){
			while(Rxmlhttp.readyState != 4){}
			this.loadedAction()
			document.body.style.cursor = "default";
		}
		return this;
	}
	
	/**
	 * what to do, when get response
	 * @TODO: other then success
	 * @return Rxmlhttp object
	 */
	this.loadedAction = function(){
		if (Rxmlhttp.readyState == 4){
			if (Rxmlhttp.status == 200) {
				if (Rxmlhttp.responseText && action != null) {
					action(Rxmlhttp);
				}
			} else {
				error(Rxmlhttp);
			}
		}
		return own;
	}
	
	/**
	 * serialize xml to string
	 * @return string (NOT Rxmlhttp object)
	 */
	this.serialize = function(node) {
	    if (typeof XMLSerializer != "undefined")
	        return (new XMLSerializer()).serializeToString(node) ;
	    else if (node.xml) return node.xml;
	    else throw "Rxmlhttp.serialize is not supported or can't serialize " + node;
	}
}
