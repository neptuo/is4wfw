// JavaScript Document
	
function Editor(conf) {
  var Own = this;
  var conf = conf;
  var TextArea = conf.textArea;
  var Controls = new Object();
  var FindPosition = -1;
  var Lines = 0;
  var Tiny = false;
  var TinyFirst = false;
  var RandomId = Math.random(100, 1000) + "" + Math.random(100, 1000);
  var LastSearchedValue = '';
  
  Controls.Form = document.createElement('form');
  Controls.Form.name = "editor-controls-form";
  Controls.Form.method = "post";
  Controls.Form.action = "";
  
  Controls.Cover = document.createElement('div');
  Controls.Cover.className = "editor-panel";
  
  if(conf.tiny != false) {
  	Controls.TinyCover = document.createElement('div');
  	Controls.TinyCover.className = 'editor-tiny-panel';
  	
	  Controls.Tiny = document.createElement('input');
	  Controls.Tiny.type = "button";
	  Controls.Tiny.name = "tiny";
  	Controls.Tiny.value = "Tiny Open / Close";
	  Controls.Tiny.title = "Open / Close Tiny editor";
	  
	  Controls.TinyCover.appendChild(Controls.Tiny);
  	Controls.Form.appendChild(Controls.TinyCover);
  }
  
  if(conf.hide != false) {
  	Controls.ShowHideCover = document.createElement('div');
  	Controls.ShowHideCover.className = "editor-showhide-panel";
  	
	  Controls.ShowHide = document.createElement('input');
  	Controls.ShowHide.type = "button";
	  Controls.ShowHide.name = "show-hide";
	  Controls.ShowHide.value = "Editor Show / Hide";
	  Controls.ShowHide.title = "Show/Hide text field";
	  
	  Controls.ShowHideCover.appendChild(Controls.ShowHide);
  	Controls.Form.appendChild(Controls.ShowHideCover);
  }
  
  if(conf.find != false) {
  	Controls.FindCover = document.createElement('div');
	  Controls.FindCover.className = "editor-find-panel";
  
  	Controls.FindText = document.createElement('span');
  	Controls.FindText.innerHTML = "Find: ";
  
  	Controls.RowsCover = document.createElement('div');
  	Controls.RowsCover.className = "editor-find-panel";
  	
	  Controls.Find = document.createElement('input');
  	Controls.Find.type = "text";
  	Controls.Find.name = "find";
  	
  	Controls.Next = document.createElement('input');
  	Controls.Next.type = "button";
	  Controls.Next.name = "next";
  	Controls.Next.value = "Next";
	  Controls.Next.title = "Find in text.";
		
		Controls.FindCover.appendChild(Controls.FindText);
  	Controls.FindCover.appendChild(Controls.Find);
  	Controls.FindCover.appendChild(Controls.Next);
  	Controls.Form.appendChild(Controls.FindCover);
  }
  
  if(conf.rows != false) {
  	Controls.RowsText = document.createElement('span');
  	Controls.RowsText.innerHTML = "Rows: ";
  	
  	Controls.RowAdd = document.createElement('input');
  	Controls.RowAdd.type = "button";
  	Controls.RowAdd.name = "row-add";
  	Controls.RowAdd.value = "+";
  	Controls.RowAdd.title = "Add row";
  
	  Controls.RowRem = document.createElement('input');
  	Controls.RowRem.type = "button";
  	Controls.RowRem.name = "row-rem";
  	Controls.RowRem.value = "-";
  	Controls.RowRem.title = "Remove row";
  	
	  Controls.RowsCover.appendChild(Controls.RowsText);
  	Controls.RowsCover.appendChild(Controls.RowAdd);
  	Controls.RowsCover.appendChild(Controls.RowRem);
  	Controls.Form.appendChild(Controls.RowsCover);
  }
  
  Controls.Cover.appendChild(Controls.Form);
  Controls.Form.Clear = document.createElement('div');
  Controls.Form.Clear.className = "clear";
  Controls.Cover.appendChild(Controls.Form.Clear);
  
  TextArea.parentNode.parentNode.insertBefore(Controls.Cover, TextArea.parentNode);
	
	if(conf.lnnm != false) {
  	Controls.LineNumbersCover = document.createElement('div');
	  Controls.LineNumbers = document.createElement('textarea');
  }
  
  this.init = function() {
  	if(conf.tiny != false) {
			this.addEvent(Controls.Tiny, "click", Own.openCloseTiny, false);
		}
		
		if(conf.find != false) {
			this.addEvent(Controls.Find, "focus", Own.findFocus, false);
    	this.addEvent(Controls.Next, "click", Own.nextClick, false);
    }
    
    if(conf.rows != false) {
  	  this.addEvent(Controls.RowAdd, "click", Own.rowAddClick, false);
	    this.addEvent(Controls.RowRem, "click", Own.rowRemClick, false);
    }
    
    if(conf.hide != false) {
    	this.addEvent(Controls.ShowHide, "click", Own.showHideClick, false);
    }
    
    this.addEvent(TextArea, "keypress", Own.textAreaKeyPress, false);
    
    this.addEvent(Controls.Form, "submit", Own.formSubmit, false);

    if(conf.closed != false) {
      Own.showHideClick(null);
    }
  }
  
  this.disinit = function() {
    this.removeEvent(Controls.Find, "focus", Own.findFocus, false);
    this.removeEvent(Controls.Next, "click", Own.nextClick, false);
    
    this.removeEvent(Controls.Rowremove, "click", Own.rowAddClick, false);
    this.removeEvent(Controls.RowRem, "click", Own.rowRemClick, false);
    
    this.removeEvent(Controls.ShowHide, "click", Own.showHideClick, false);
    
    this.removeEvent(TextArea, "keypress", Own.textAreaKeyPress, false);
    
    this.removeEvent(Controls.Form, "submit", Own.formSubmit, false);

    if(TextArea.className.indexOf('editor-closed') != -1) {
      Own.showHideClick(null);
    }
	}
	
	this.openCloseTiny = function (event) {
		if(Tiny == false) {
			TextArea.id = RandomId;
  		if(TinyFirst == false) {
				initTiny(RandomId);
			}
			Own.disable();
  		tinyMCE.execCommand('mceAddControl', true, RandomId);
  		Tiny = true;
		} else {
			Own.enable();
  		tinyMCE.execCommand('mceRemoveControl', true, RandomId);
  		Tiny = false;
		}
	}
  
  this.findFocus = function (event) {
    FindPosition = TextArea.selectionStart;
  }
  
  this.nextClick = function (event) {
    if(Controls.Find.value.length > 0) {
    	if(LastSearchedValue != Controls.Find.value) {
				FindPosition = -1;
			}
      FindPosition = TextArea.value.indexOf(Controls.Find.value, FindPosition + 1);
      if(FindPosition == -1) {
        FindPosition = -1;
        //Own.nextClick(event);
        alert(" '"+ Controls.Find.value + "' not found!");
      } else {
        TextArea.setSelectionRange(FindPosition, (FindPosition + Controls.Find.value.length));
        TextArea.focus();
      }
    }
    LastSearchedValue = Controls.Find.value;
  }
  
  this.rowAddClick = function (event) {
    TextArea.rows += 2;
    Controls.LineNumbers.rows = TextArea.rows + 1;
    
    if(Tiny) {
			Own.openCloseTiny();
			Own.openCloseTiny();
		}
  }
  
  this.rowRemClick = function (event) {
    if(TextArea.rows > 2) {
      TextArea.rows -= 2;
    	Controls.LineNumbers.rows = TextArea.rows + 1;
    
    	if(Tiny) {
				Own.openCloseTiny();
				Own.openCloseTiny();
			}
    }
  }
  
  this.showHideClick = function (event) {
  	if(!Tiny) {
	    if(TextArea.style.display == "") {
  	    TextArea.style.display = "none";
    	  if(conf.lnnm != false) {
      		Controls.LineNumbersCover.style.display = "none";
	      }
  	  } else {
    	  TextArea.style.display = "";
      	if(conf.lnnm != false) {
      		Controls.LineNumbersCover.style.display = "";
      	}
    	}
    }
  }
  
  this.textAreaKeyPress = function (event) {
  	if(event.keyCode == 9 && !event.ctrlKey) {
  		Own.insertText(TextArea, '  ');
  		TextArea.focus();
  		Own.stopEvent(event);
  	} else if(event.charCode == 109 && event.ctrlKey) {
			var line = window.prompt('Set line number');
			var lineHeight = TextArea.clientHeight / TextArea.rows;
			var jump = (line - 3) * lineHeight;
			TextArea.scrollTop = jump;
			
			var c = 0;
			var pos = -2;
			var pos2 = 0;
			while((pos = TextArea.value.indexOf('\n', pos + 1)) != -1 && c < line - 1) {
				c ++;
				pos2 = pos;
			}
			TextArea.setSelectionRange(pos2 + 1, pos2 + 1);
		}
  }
  
  this.formSubmit = function (event) {
  	Own.stopEvent(event);
  }
  
  this.insertText = function (textBox, strNewText, pos){
		var top = TextArea.scrollTop;
  	var tb = textBox;
  	var first = tb.value.slice(0, tb.selectionStart);
  	var second = tb.value.slice(tb.selectionEnd);
  	var sta = tb.selectionStart + strNewText.length;
  	tb.value = first + strNewText + second;
  	if(pos != null) {
    	sta = sta - strNewText.length + pos;
		}
  	if(tb.setSelectionRange) {
			tb.setSelectionRange(sta,sta);
		}
  	tb.focus();
		TextArea.scrollTop = top;
  }
  
  this.disable = function() {
  	if(conf.lnnm != false) {
			TextArea.parentNode.parentNode.removeChild(Controls.LineNumbersCover);
		}
		TextArea.parentNode.style.margin = '0';
	}
	
	this.enable = function() {
		TextArea.parentNode.style.marginLeft = '40px';
		if(conf.lnnm != false) {
			TextArea.parentNode.parentNode.insertBefore(Controls.LineNumbersCover, TextArea.parentNode);
		}
	}

  this.addEvent = function (obj, ev, func, b) {
    if(obj.addEventListener) {
      obj.addEventListener(ev, func, b);
    } else {
      obj.attachEvent("on" + ev, func);
    }
  }
  
  this.removeEvent = function(obj, ev, func, b) {
    if(obj.removeEventListener) {
      obj.removeEventListener(ev, func, b);
    } else {
      obj.deattachEvent("on" + ev, func);
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

  this.createTextAreaWithLines = function(string) {
    Controls.LineNumbersCover.className = 'line-numbers-cover';
    
    Controls.LineNumbers.className      = 'line-numbers';
    Controls.LineNumbers.setAttribute('readonly', 'readonly');
  	Controls.LineNumbers.rows           = TextArea.rows + 1;
    Controls.LineNumbers.innerHTML      = string;
    
    Controls.LineNumbersCover.appendChild(Controls.LineNumbers);
    TextArea.parentNode.parentNode.insertBefore(Controls.LineNumbersCover, TextArea.parentNode);
    
    Own.setLine();
    TextArea.focus();
 
 		Own.addEvent(TextArea, "keydown", Own.setLine, false);
 		Own.addEvent(TextArea, "mousedown", Own.setLine, false);
 		Own.addEvent(TextArea, "onscroll", Own.setLine, false);
 		Own.addEvent(TextArea, "blur", Own.setLine, false);
 		Own.addEvent(TextArea, "focus", Own.setLine, false);
 		Own.addEvent(TextArea, "nouseover", Own.setLine, false);
 		Own.addEvent(TextArea, "mouseup", Own.setLine, false);
 		
    TextArea.onscroll     = function() { Own.setLine(); }
  }
           
  this.setLine = function(){
    Controls.LineNumbers.scrollTop   = TextArea.scrollTop;
  }
	
  this.init();
  if(conf.lnnm) {
  	this.createTextAreaWithLines(conf.lineNumbers);
  }
}
