// JavaScript Document

function FileName(inputFile, inputText) {
  if(inputFile.type != "file") {
    alert("Passed object isn't input[type=file]!");
    return;
  }
  
  var Own = this;
  var InputFile = inputFile;
  var InputText = inputText;
  
  this.init = function () {
    Own.addEvent(InputFile, "change", Own.inputFileOnChange, false);
  }
  
  this.inputFileOnChange = function (event) {
    if(InputFile.value.indexOf('\\') != -1 ) {
      var pos2 = -1;
      do {
        pos = pos2;
        pos2 = InputFile.value.indexOf('\\', pos + 1);
      } while(pos2 != -1);
      InputText.value = InputFile.value.substring(pos + 1, InputFile.value.indexOf('.', InputFile.value.length - 4));
    } else {
      InputText.value = InputFile.value.substring(0, InputFile.value.indexOf('.', InputFile.value.length - 4));
    }
  }

  this.addEvent = function (obj, ev, func, b) {
		if(obj.addEventListener) {
			obj.addEventListener(ev, func, b);
		} else {
			obj.attachEvent("on" + ev, func);
		}
	}
	
	this.stopEvent = function (event) {
    if(navigator.appName != "Microsoft Internet Explorer") {
      event.stopPropagation();
      event.preventDefault();
    } else {
      event.cancelBubble = true;
      event.returnValue = false;
    }
  }
  
  Own.init();
}
