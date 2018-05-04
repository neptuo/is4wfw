Ajax = function(selector, parentPageId) {
    this.Selector = selector;
    this.LoadingHandlers = [];
    this.CompletedHandlers = [];
    this.FailedHandlers = [];

    if (typeof(parentPageId) != 'undefined') {
        this.ParentPageId = parentPageId;
    } else {
        this.ParentPageId = null;
    }

    window.addEventListener("popstate", this._OnPopState.bind(this));
}

Ajax.prototype = Object.create(Ajax.prototype);

Ajax.prototype.AddEventListener = function(eventName, handler) {
    if (eventName == 'loading') {
        this.LoadingHandlers.push(handler);
    } else if(eventName == 'completed') {
        this.CompletedHandlers.push(handler);
    } else if(eventName == 'failed') {
        this.FailedHandlers.push(handler);
    } else {
        throw new Error('Not supported event name "' + eventName + '".');
    }
};

Ajax.prototype.Initialize = function(root) {
    root.find("a").not("[target=_blank]").not("[data-ajax=false]").click(this._OnLinkClick.bind(this));
    root.find("form").not("[target=_blank]").not("[data-ajax=false]").submit(this._OnFormSubmit.bind(this));
};

Ajax.prototype._StopEvent = function(e) {
    e.preventDefault();
};

Ajax.prototype._RaiseEvent = function(eventName) {
    var handlers = null;
    if (eventName == 'loading') {
        handlers = this.LoadingHandlers;
    } else if(eventName == 'completed') {
        handlers = this.CompletedHandlers;
    } else if(eventName == 'failed') {
        handlers = this.FailedHandlers;
    } else {
        throw new Error('Not supported event name "' + eventName + '".');
    }

    if (handlers != null) {
        for (var i = 0, j = handlers.length; i < j; i++) {
            var handler = handlers[i];
            if (typeof(handler) == 'function') {
                handler(this);
            }
        }
    }
}

Ajax.prototype._OnLinkClick = function(e) {
    var url = e.currentTarget.href;

    this.Load(url);
    this._UpdateHistory(url);
    this._StopEvent(e);
};

Ajax.prototype._OnFormSubmit = function(e) {
    var form = e.currentTarget;
    var url = form.action;
    var method = form.method;
    
    this._RaiseEvent('loading');

    var data = new FormData(form);
    var request = new XMLHttpRequest();
    request.addEventListener("readystatechange", this._OnFormReadyStateChanged.bind(this));
    request.open(method, url);
    this._ObserveRequest(request);
    request.send(data);
    
    this._StopEvent(e);
};

Ajax.prototype._OnFormReadyStateChanged = function(e) {
    var request = e.currentTarget;
    if (request.readyState == 4){
        this._OnLoadCompleted(request, "success");
    }
};

Ajax.prototype.Load = function(url) {
    this._RaiseEvent('loading');
    $.ajax({
        url: url,
        type: 'GET',
        beforeSend: this._ObserveRequest.bind(this),
        complete: this._OnLoadCompleted.bind(this)
    });
};

Ajax.prototype._ObserveRequest = function(request) {
    request.setRequestHeader('X-Template', 'xml');
    if (this.ParentPageId != null) {
        request.setRequestHeader('X-Parent-Page-Id', this.ParentPageId);
    }
};

Ajax.prototype._UpdateHistory = function(url, replace) {
    if (window.history) {
        if (replace === true) {
            window.history.replaceState(url, document.title, url);
        } else {
            window.history.pushState(url, document.title, url);
        }
    }
};

Ajax.prototype._OnLoadCompleted = function(xhr, statusText) {
    if (statusText == "error" || statusText == "timeout" || statusText == "abort" || statusText == "parsererror") {
        this._RaiseEvent('failed');
        return;
    }

    var url = xhr.responseURL;
    if (url != window.location.href) {
        this._UpdateHistory(url, true);
    }

    var responseText = xhr.responseText;

    var response = document.createElement("document");
    response.innerHTML = responseText;

    var head = this._FindElement(response, "rssmm:head");
    if (head != null) {
        var title = this._FindElement(head, "rssmm:title");
        if (title != null) {
            document.title = title.innerHTML;
        }

        var styles = this._FindElement(head, "rssmm:styles");
        if (styles != null) {
            styles = styles.getElementsByTagName("rssmm:link-ref");
            for (var i = 0, count = styles.length; i < count; i++) {
                var linkUrl = styles[i].innerHTML;
                if (document.querySelector("link[href='" + linkUrl + "']") == null) {
                    var link = document.createElement("link");
                    link.rel = "stylesheet";
                    link.href = linkUrl;
                    link.type = "text/css";
                    document.head.appendChild(link);
                }
            }
        }
        
        var scripts = this._FindElement(head, "rssmm:scripts");
        if (scripts != null) {
            scripts = scripts.getElementsByTagName("rssmm:script-ref");
            for (var i = 0, count = scripts.length; i < count; i++) {
                var linkUrl = scripts[i].innerHTML;
                if (document.querySelector("script[src='" + linkUrl + "']") == null) {
                    var link = document.createElement("script");
                    link.src = linkUrl;
                    link.type = "text/javascript";
                    document.head.appendChild(link);
                }
            }
        }
    }

    var content = this._FindElement(response, "rssmm:content");
    if (content != null) {
        var html = content.innerHTML;
        html = html.replace(/\?__TEMPLATE=xml/g, '').replace(/\&__TEMPLATE=xml/g, '');

        var oldContent = $(this.Selector);
        var newContent = $(html).find(this.Selector);
        if (newContent.length == 1) {
            oldContent.replaceWith(newContent);
            this.Initialize(newContent);
        } else {
            oldContent.html(html)
            this.Initialize(oldContent); 
        }   
    }

    this._RaiseEvent('completed');
};

Ajax.prototype._FindElement = function(container, name) {
    var elements = container.getElementsByTagName(name);
    if (elements.length > 0) {
        return elements[0];
    }

    return null;
};

Ajax.prototype._OnPopState = function(e) {
    var url = e.state;
    if (url == null) {
        url = window.location.href;
    }

    this.Load(url);
    this._StopEvent(e);
};