Ajax = function({ selector, parentPageId, modifyUrl, includeCredentials }) {
    this.Selector = selector;
    this.ModifyUrl = modifyUrl;
    this.IncludeCredentials = includeCredentials;
    this.LoadingHandlers = [];
    this.CompletedHandlers = [];
    this.FailedHandlers = [];

    if (typeof(parentPageId) != 'undefined') {
        this.ParentPageId = parentPageId;
    } else {
        this.ParentPageId = null;
    }

    if (this.ModifyUrl) {
        window.addEventListener("popstate", this._OnPopState.bind(this));
    }
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

Ajax.prototype._OnLinkClick = function(e) {
    var url = e.currentTarget.href;

    this.Load(url);
    this._UpdateHistory(url);
    this._StopEvent(e);
};

Ajax.prototype._OnButtonClick = function(e) {
    this._submitButton = {
        name: e.currentTarget.name,
        value: e.currentTarget.value
    };
};

Ajax.prototype._Fetch = function(url, method, data) {
    var fetchOptions = {
        method: method,
        headers: {}
    };

    if (data) {
        fetchOptions.body = data;
    }

    if (this.IncludeCredentials === true) {
        fetchOptions.credentials = "include";
    } else if (this.IncludeCredentials === false) {
        fetchOptions.credentials = "omit";
    }

    this._ObserveRequest(fetchOptions);

    fetch(url, fetchOptions)
        .then(response => { 
            if (!response.ok) {
                throw new Error("Network error");
            }

            url = response.url;
            return response.text(); 
        })
        .then(result => {
            this._OnLoadCompleted(result, url);
        })
        .catch(error => {
            this._RaiseEvent("failed", error);
        });
}

Ajax.prototype._OnFormSubmit = function(e) {
    var form = e.currentTarget;
    var url = form.action;
    var method = form.method;
    
    this._RaiseEvent("loading");

    var data = new FormData(form);

    if (this._submitButton != null) {
        data.set(this._submitButton.name, this._submitButton.value);
        this._submitButton = null;
    }

    this._Fetch(url, method, data);
    this._StopEvent(e);
};

Ajax.prototype.Load = function(url) {
    this._RaiseEvent("loading");
    this._Fetch(url, "GET");
};

Ajax.prototype._ObserveRequest = function(request) {
    request.headers["X-Template"] = "xml";
    if (this.ParentPageId != null) {
        request.headers["X-Parent-Page-Id"] = this.ParentPageId;
    }
};

Ajax.prototype._UpdateHistory = function(url, replace) {
    if (this.ModifyUrl && window.history) {
        if (replace === true) {
            window.history.replaceState(url, document.title, url);
        } else {
            window.history.pushState(url, document.title, url);
        }
    }
};

Ajax.prototype._OnLoadCompleted = function(responseText, responseUrl) {
    if (responseUrl != window.location.href) {
        this._UpdateHistory(responseUrl, true);
    }

    var response = document.createElement("document");
    response.innerHTML = responseText;

    var scriptInlines = null;

    var head = this._FindElement(response, "rssmm:head");
    if (head != null) {
        var title = this._FindElement(head, "rssmm:title");
        if (title != null) {
            this._UpdateHtmlTitle(title.innerHTML);
        }

        var styles = this._FindElement(head, "rssmm:styles");
        if (styles != null) {
            var styleReferences = styles.getElementsByTagName("rssmm:link-ref");
            for (var i = 0, count = styleReferences.length; i < count; i++) {
                var linkUrl = styleReferences[i].innerHTML;
                this._IncludeUrlStyle(linkUrl);
            }

            var styleInlines = styles.getElementsByTagName("rssmm:style");
            for (var i = 0, count = styleInlines.length; i < count; i++) {
                var style = styleInlines[i].innerHTML;
                this._IncludeInlineStyle(style);
            }
        }
        
        var scripts = this._FindElement(head, "rssmm:scripts");
        if (scripts != null) {
            var scriptReferences = scripts.getElementsByTagName("rssmm:script-ref");
            for (var i = 0, count = scriptReferences.length; i < count; i++) {
                var scriptUrl = scriptReferences[i].innerHTML;
                this._IncludeUrlScript(scriptUrl);
            }

            scriptInlines = scripts.getElementsByTagName("rssmm:script");
        }
    }

    var content = this._FindElement(response, "rssmm:content");
    if (content != null) {
        var html = content.innerHTML;
        html = html.replace(/\?__TEMPLATE=xml/g, '').replace(/\&__TEMPLATE=xml/g, '');

        this._UpdateHtmlContent(html);
    }

    if (scriptInlines != null) {
        for (var i = 0, count = scriptInlines.length; i < count; i++) {
            var script = scriptInlines[i].innerHTML;
            this._IncludeInlineScript(script);
        }
    }

    this._RaiseEvent('completed');
};

Ajax.prototype._IncludeUrlStyle = function(linkUrl) {
    if (!this._IsUrlStyleIncluded(linkUrl)) {
        var element = document.createElement("link");
        element.rel = "stylesheet";
        element.href = linkUrl;
        element.type = "text/css";
        document.head.appendChild(element);
    }
};

Ajax.prototype._IsUrlStyleIncluded = function(linkUrl) {
    return document.querySelector("link[href='" + linkUrl + "']") != null;
};

Ajax.prototype._IncludeInlineStyle = function(style) {
    var element = document.createElement("style");
    element.type = "text/css";
    element.innerHTML = style;
    document.head.appendChild(element);
};

Ajax.prototype._IncludeUrlScript = function(scriptUrl) {
    if (!this._IsUrlScriptIncluded(scriptUrl)) {
        var element = document.createElement("script");
        element.src = scriptUrl;
        element.type = "text/javascript";
        document.head.appendChild(element);
    }
};

Ajax.prototype._IsUrlScriptIncluded = function(scriptUrl) {
    return document.querySelector("script[src='" + scriptUrl + "']") != null;
};

Ajax.prototype._IncludeInlineScript = function(script) {
    eval(script);
};

Ajax.prototype._UpdateHtmlTitle = function(title) {
    document.title = title;
};

Ajax.prototype._UpdateHtmlContent = function(html) {
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