Ajax = function(selector, parentPageId, options) {
    this.Selector = selector;
    this.BackendOrigin = null;
    this.LoadingHandlers = [];
    this.CompletedHandlers = [];
    this.FailedHandlers = [];

    if (typeof(parentPageId) != 'undefined') {
        this.ParentPageId = parentPageId;
    } else {
        this.ParentPageId = null;
    }
    
    if (options) {
        if (options.backendOrigin) {
            this.BackendOrigin = options.backendOrigin;
        }
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
    root.find("button,input[type=submit]").not("[data-ajax=false]").not("[type=button]").not("[type=reset]").click(this._OnButtonClick.bind(this));
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

Ajax.prototype._ReplaceBrowserOriginWithBackendOrigin = function(url) {
    if (this.BackendOrigin != null) {
        url = url.replace(window.location.origin, this.BackendOrigin);
    }

    return url;
}

Ajax.prototype._ReplaceBackendOriginWithBrowserOrigin = function(url) {
    if (this.BackendOrigin != null) {
        url = url.replace(this.BackendOrigin, window.location.origin);
    }

    return url;
}

Ajax.prototype._OnLinkClick = function(e) {
    var url = e.currentTarget.href;
    var loadUrl = this._ReplaceBrowserOriginWithBackendOrigin(url);

    this.Load(loadUrl);
    this._UpdateHistory(url);
    this._StopEvent(e);
};

Ajax.prototype._OnButtonClick = function(e) {
    this._submitButton = {
        name: e.currentTarget.name,
        value: e.currentTarget.value
    };
};

Ajax.prototype._OnFormSubmit = function(e) {
    var form = e.currentTarget;
    var url = this._ReplaceBrowserOriginWithBackendOrigin(form.action);
    var method = form.method;
    
    this._RaiseEvent('loading');

    var data = new FormData(form);

    if (this._submitButton != null) {
        data.set(this._submitButton.name, this._submitButton.value);
        this._submitButton = null;
    }

    var request = new XMLHttpRequest();
    request.withCredentials = true;
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

    if (this.BackendOrigin != null && url.indexOf("http") != 0) {
        url = this.BackendOrigin + url;
    }

    $.ajax({
        url: url,
        xhrFields: {withCredentials: true},
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

    if (xhr.responseURL) {
        var url = this._ReplaceBackendOriginWithBrowserOrigin(xhr.responseURL);
        if (url != window.location.href) {
            this._UpdateHistory(url, true);
        }
    }

    var responseText = xhr.responseText;

    var response = document.createElement("document");
    response.innerHTML = responseText;

    var scriptInlines = null;
    var styleInlines = null;

    var head = this._FindElement(response, "rssmm:head");
    if (head != null) {
        var title = this._FindElement(head, "rssmm:title");
        if (title != null) {
            document.title = title.innerHTML;
        }

        var styles = this._FindElement(head, "rssmm:styles");
        if (styles != null) {
            var styleReferences = styles.getElementsByTagName("rssmm:link-ref");
            for (var i = 0, count = styleReferences.length; i < count; i++) {
                var linkUrl = styleReferences[i].innerHTML;
                if (this.BackendOrigin != null) {
                    linkUrl = this.BackendOrigin + linkUrl;
                }

                if (document.querySelector("link[href='" + linkUrl + "']") == null) {
                    var link = document.createElement("link");
                    link.rel = "stylesheet";
                    link.href = linkUrl;
                    link.type = "text/css";
                    document.head.appendChild(link);
                }
            }

            var styleInlines = styles.getElementsByTagName("rssmm:style");
            for (var i = 0, count = styleInlines.length; i < count; i++) {
                var style = styleInlines[i].innerHTML;
                var link = document.createElement("style");
                link.type = "text/css";
                link.innerHTML = style;
                document.head.appendChild(link);
            }
        }
        
        var scripts = this._FindElement(head, "rssmm:scripts");
        if (scripts != null) {
            var scriptReferences = scripts.getElementsByTagName("rssmm:script-ref");
            for (var i = 0, count = scriptReferences.length; i < count; i++) {
                var linkUrl = scriptReferences[i].innerHTML;
                if (this.BackendOrigin != null) {
                    linkUrl = this.BackendOrigin + linkUrl;
                }

                if (document.querySelector("script[src='" + linkUrl + "']") == null) {
                    var link = document.createElement("script");
                    link.src = linkUrl;
                    link.type = "text/javascript";
                    document.head.appendChild(link);
                }
            }

            scriptInlines = scripts.getElementsByTagName("rssmm:script");
        }
    }

    var content = this._FindElement(response, "rssmm:content");
    if (content != null) {
        var html = content.innerHTML;
        html = html.replace(/\?__TEMPLATE=xml/g, '').replace(/\&__TEMPLATE=xml/g, '');

        var oldContent = $(this.Selector);
        
        var newHtml = $(html);
        var newContent = newHtml.filter(this.Selector);
        if (newContent.length == 0) {
            newContent = newHtml.find(this.Selector);
        }

        if (newContent.length == 1) {
            oldContent.replaceWith(newContent);
            this.Initialize(newContent);
            newContent.find("[autofocus]").focus();
        } else {
            oldContent.html(html)
            this.Initialize(oldContent); 
            oldContent.find("[autofocus]").focus();
        }
    }

    if (scriptInlines != null) {
        for (var i = 0, count = scriptInlines.length; i < count; i++) {
            var script = scriptInlines[i].innerHTML;
            eval(script);
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

    url = this._ReplaceBrowserOriginWithBackendOrigin(url);

    this.Load(url);
    this._StopEvent(e);
};