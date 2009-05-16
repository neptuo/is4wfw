// JavaScript Document

function CountDown(label, sec, parentEl) {
	var Id = 0;
	if(!CountDown.Instances) {
		CountDown.Instances = new Array();
		CountDown.Instances[0] = this;
	} else {
		Id = CountDown.Instances.length;
		CountDown.Instances[Id] = this;
	}
	var This = this;
	
	var Interval = null;
	
	if(sec < 0) { alert('You must set more than 0 seconds to count down!'); return; }
	var Seconds = sec;
	
	var HTML = new Array();
	HTML.Parent = parentEl;
	HTML.Cover = document.createElement('div');
	HTML.Cover.className = 'count-down-cover';
	HTML.Label = document.createElement('span');
	HTML.Label.className = 'count-down-label';
	HTML.Label.innerHTML = label;
	HTML.Counter = document.createElement('span');
	HTML.Counter.className = 'count-down-counter';
	HTML.Counter.innerHTML = Seconds + 's';
	
	HTML.Cover.appendChild(HTML.Label);
	HTML.Cover.appendChild(HTML.Counter);
	
	HTML.Parent.appendChild(HTML.Cover);
	
	this.count = function() {
		Seconds --;
		HTML.Counter.innerHTML = Seconds + 's';
		if(Seconds == 0) {
			window.clearInterval(Interval);
		}
	}
	
	this.start = function() {
		Interval = window.setInterval ('CountDown.Instances['+Id+'].count()', 1000);
	}
}