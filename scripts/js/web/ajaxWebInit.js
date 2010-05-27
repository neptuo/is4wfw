/**
 *
 *  @author  Marek Fišera marek.fisera@email.cz
 *  @date    2009/06/18
 *
 */
var ajaxRootElement = null;
var ajaxLoading = null;
var ajaxBody = null;
var ajaxCoverElement = null;
var ajaxIsLoadingNow = false;

function addEvent (obj, ev, func, b) {
  if(obj.addEventListener) {
    obj.addEventListener(ev, func, b);
  } else {
    obj.attachEvent("on" + ev, func);
  }
}

function stopEvent(event) {
  event.cancelBubble = true;
  event.returnValue = false;
  if(navigator.appName != "Microsoft Internet Explorer") {
    event.preventDefault();
  }
}

Event.domReady.add(initAjax);
//addEvent(window, 'load', initAjax, false);

function initAjax(event) {
  initLocation(event);
  initDynamicLinks(document);
}

function initLocation(event) {
  if(window.location.href.indexOf('#') != -1) {
    var href = window.location.href.substring(window.location.href.indexOf('#') + 1, window.location.href.length);
    if(!ajaxIsLoadingNow) {
      if(ajaxCoverElement == null) {
        ajaxCoverElement = document.createElement('div');
        ajaxCoverElement.style.position = "absolute";
        ajaxCoverElement.style.left = "0px";
        ajaxCoverElement.style.top = "0px";
        ajaxCoverElement.style.width = "100%";
        ajaxCoverElement.style.height = "100%";
      }
      document.body.appendChild(ajaxCoverElement);
      if(ajaxLoading == null) {
        ajaxLoading = document.createElement('div');
        ajaxLoading.className = "ajax-loading";
        ajaxRootElement = document.getElementById('{web-content}');
        if(ajaxRootElement != null) {
          ajaxRootElement.parentNode.insertBefore(ajaxLoading, ajaxRootElement);
        } else {
          document.body.appendChild(ajaxLoading);
        }
      }
      ajaxLoading.innerHTML = "Loading ...";
      ajaxLoading.style.display = '';
      ajaxIsLoadingNow = true;
      var xmlhttp = new Rxmlhttp();
      xmlhttp.setAsync(true);
      xmlhttp.setMethod("GET");
      xmlhttp.onSuccess(processRequest);
      xmlhttp.loadPage(href + "?__START_ID={root-page}");
    }
    //stopEvent(event);
  }
}

function initDynamicLinks(root) {
  if(root != null) {
    var lis = root.getElementsByTagName('div');
    for(var i = 0; i < lis.length; i ++) {
      if(lis[i].className.indexOf('link') != -1) {
        if(lis[i].childNodes[0] != null && lis[i].childNodes[0].tagName == "A") {
          addEvent(lis[i].childNodes[0], 'click', menuLinkClick, false);
        }
      }
    }
    var as = root.getElementsByTagName('a');
    for(var i = 0; i < as.length; i ++) {
      if(as[i].rel == "dynamic-link") {
        addEvent(as[i], 'click', menuLinkClick, false);
      }
    }
  }
}

function menuLinkClick(event) {
  var anchor = (event.srcElement) ? event.srcElement : event.target;
  if(anchor.tagName == "A" || (anchor.parentNode != null && anchor.parentNode.tagName == "A")) {
    if(!ajaxIsLoadingNow) {
      if(anchor.tagName != "A") {
        anchor = anchor.parentNode;
      }
      if(ajaxCoverElement == null) {
        ajaxCoverElement = document.createElement('div');
        ajaxCoverElement.style.position = "absolute";
        ajaxCoverElement.style.left = "0px";
        ajaxCoverElement.style.top = "0px";
        ajaxCoverElement.style.width = "100%";
        ajaxCoverElement.style.height = "100%";
      }
      document.body.appendChild(ajaxCoverElement);
      if(ajaxLoading == null) {
        ajaxLoading = document.createElement('div');
        ajaxLoading.className = "ajax-loading";
        ajaxRootElement = document.getElementById('{web-content}');
        if(ajaxRootElement != null) {
          ajaxRootElement.parentNode.insertBefore(ajaxLoading, ajaxRootElement);
        } else {
          document.body.appendChild(ajaxLoading);
        }
      }
      var href = anchor.href.substring(anchor.href.indexOf('/', 7), anchor.href.length);
      window.location.href = window.location.href.substring(0, window.location.href.indexOf('#')) + "#" + href;
      ajaxLoading.innerHTML = "Loading ...";
      ajaxLoading.style.display = '';
      ajaxIsLoadingNow = true;
      var xmlhttp = new Rxmlhttp();
      xmlhttp.setAsync(true);
      xmlhttp.setMethod("GET");
      xmlhttp.onSuccess(processRequest);
      xmlhttp.loadPage(anchor.href + "?__START_ID={root-page}");
    }
    stopEvent(event);
  }
}

function processRequest(xmlhttp) {
  var temp = document.createElement('div');
  temp.innerHTML = xmlhttp.responseText.replace('<body', '<div ').replace('</body', '</div').replace('<title', '<dd ').replace('</title', '</dd');
  ajaxTitle = temp.getElementsByTagName('dd');
  ajaxTitle = ajaxTitle[0];
  if(ajaxTitle != null) {
    document.title = ajaxTitle.innerHTML;
  }
  ajaxBody = temp.getElementsByTagName('div');
  ajaxBody = ajaxBody[0];
  ajaxRootElement = document.getElementById('{web-content}');
  if(ajaxRootElement == null) {
    ajaxLoading.innerHTML = "ERROR LOADING .. Press F5!";
  } else {
    ajaxRootElement.innerHTML = ajaxBody.innerHTML;
    initDynamicLinks(ajaxRootElement);
    ajaxLoading.innerHTML = "";
    ajaxLoading.style.display = 'none';
    if(ajaxCoverElement != null) {
      document.body.removeChild(ajaxCoverElement);
    }
    ajaxIsLoadingNow = false;
  }
}