
if (typeof Event == 'undefined') Event = new Object();

Event.domReady = {
  add: function(fn) {
    if (Event.domReady.loaded) return fn();

    var observers = Event.domReady.observers;
    if (!observers) observers = Event.domReady.observers = [];

    observers[observers.length] = fn;
    if (Event.domReady.callback) return;
    Event.domReady.callback = function() {
      if (Event.domReady.loaded) return;
      
      Event.domReady.loaded = true;
      if (Event.domReady.timer) {
        clearInterval(Event.domReady.timer);
        Event.domReady.timer = null;
      }
      
      var observers = Event.domReady.observers;
      for (var i = 0, length = observers.length; i < length; i++) {
        var fn = observers[i];
        observers[i] = null;
        fn();
      }
      Event.domReady.callback = Event.domReady.observers = null;
    };

    var ie = !!(window.attachEvent && !window.opera);
    var webkit = navigator.userAgent.indexOf('AppleWebKit/') > -1;
    
    if (document.readyState && webkit) {
      Event.domReady.timer = setInterval(function() {
        var state = document.readyState;
        if (state == 'loaded' || state == 'complete') {
          Event.domReady.callback();
        }
      }, 50);
      
    } else if (document.readyState && ie) {
      
      var src = (window.location.protocol == 'https:') ? '://0' : 'javascript:void(0)';
      document.write(
        '<script type="text/javascript" defer="defer" src="' + src + '" ' + 
        'onreadystatechange="if (this.readyState == \'complete\') Event.domReady.callback();"' + 
        '><\/script>');
    } else {
      
      if (window.addEventListener) {
        document.addEventListener("DOMContentLoaded", Event.domReady.callback, false);
        window.addEventListener("load", Event.domReady.callback, false);
      } else if (window.attachEvent) {
        window.attachEvent('onload', Event.domReady.callback);
      } else {
        var fn = window.onload;
        window.onload = function() {
          Event.domReady.callback();
          if (fn) fn();
        }
      }
    }
  }
}
