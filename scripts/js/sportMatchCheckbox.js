function addEvent (obj, ev, func, b) {
  if(obj.addEventListener) {
    obj.addEventListener(ev, func, b);
  } else {
    obj.attachEvent("on" + ev, func);
  }
};

function onNotPlayedChange(event) {
	for(var i = 0; i < sportMatchFields.length; i++) {
		sportMatchFields[i].disabled = sportMatchCheckbox.checked;
	}
};

var sportMatchFields = new Array();
sportMatchFields[0] = document.getElementById('match-edit-hscore');
sportMatchFields[1] = document.getElementById('match-edit-hshoots');
sportMatchFields[2] = document.getElementById('match-edit-hpenalty');
sportMatchFields[3] = document.getElementById('match-edit-hextratime');
sportMatchFields[4] = document.getElementById('match-edit-ascore');
sportMatchFields[5] = document.getElementById('match-edit-ashoots');
sportMatchFields[6] = document.getElementById('match-edit-apenalty');
sportMatchFields[7] = document.getElementById('match-edit-aextratime');
var sportMatchCheckbox = document.getElementById('match-edit-notplayed');

addEvent(sportMatchCheckbox, 'change', onNotPlayedChange, false);
onNotPlayedChange(null);