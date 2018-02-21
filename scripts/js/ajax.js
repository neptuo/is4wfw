Ajax = function(selector, parentPageId) {
    this.Selector = selector;

    if (typeof(parentPageId) != 'undefined') {
        this.ParentPageId = parentPageId;
    } else {
        this.ParentPageId = null;
    }

    window.addEventListener("popstate", this._OnPopState.bind(this));
}

Ajax.prototype = Object.create(Ajax.prototype);

Ajax.prototype.Initialize = function(root) {
    root.find("a").click(this._OnLinkClick.bind(this));
};

Ajax.prototype._StopEvent = function(e) {
    e.preventDefault();
};

Ajax.prototype._OnLinkClick = function(e) {
    var url = e.currentTarget.href;

    this.Load(url);
    this._UpdateHistory(url);
    this._StopEvent(e);
};

Ajax.prototype.Load = function(url) {
    $.ajax({
        url: url,
        type: 'GET',
        beforeSend: function (xhr) {
            xhr.setRequestHeader('X-Template', 'xml');
            if (this.ParentPageId != null) {
                xhr.setRequestHeader('X-ParentPageId', this.ParentPageId);
            }
        },
        success: this._OnLoadCompleted.bind(this),
        error: this._OnLoadCompleted.bind(this)
    });
};

Ajax.prototype._UpdateHistory = function(url) {
    if (window.history) {
        window.history.pushState(url, document.title, url);
    }
};

Ajax.prototype._OnLoadCompleted = function(responseText) {
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
    }

    var content = this._FindElement(response, "rssmm:content");
    if (content != null) {
        var html = content.innerHTML;
        html = html.replace(/\?__TEMPLATE=xml/g, '').replace(/\&__TEMPLATE=xml/g, '');

        var oldContent = $(this.Selector);
        var newContent = $(html).find(this.Selector);
        
        oldContent.replaceWith(newContent);
        this.Initialize(newContent); 
    }
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