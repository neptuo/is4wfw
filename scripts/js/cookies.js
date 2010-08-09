function Cookies() {
	this.create = function(name,value,days) {
		if (days) {
			var date = new Date();
			date.setTime(date.getTime()+(days*24*60*60*1000));
			var expires = "; expires="+date.toGMTString();
		}
		else var expires = "";
		//alert('name: ' + name + ', value: ' + value + ', cookie: ' + document.cookie);
		document.cookie = name+"="+value+expires+"; path=/";
	};

	this.read = function(name) {
		var nameEQ = name + "=";
		var ca = document.cookie.split(';');
		for(var i=0;i < ca.length;i++) {
			var c = ca[i];
			while (c.charAt(0)==' ') c = c.substring(1,c.length);
			if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
		}
		return null;
	};

	this.erase = function(name) {
		createCookie(name,"",-1);
	};
};