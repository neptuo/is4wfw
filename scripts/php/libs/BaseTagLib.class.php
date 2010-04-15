<?php

  /**
   *
   *  Base class for all tag libs.
   *  
   *  @author     Marek SMM
   *  @timestamp  2009-10-21
   *  @version    1.07
   *
   */           
  class BaseTagLib {
    /**
     *
     *  Path to library xml definition.
     *
     */                   
    private $TagLibXml = "";
    
    /**
     *
     *  True, if no is used on page yet.     
     *
     */                   
    private $FirstFrame = true;
    
    /**
     *
     *  return path to library xml definition.
     *  
     *  @return path to library xml definition
     *
     */                   
    public function getTagLibXml() {
      return $this->TagLIbXml;
    }
    
    /**
     *
     *  set path to library xml definition.
     *  
     *  @return none
     *
     */                   
    protected function setTagLibXml($xml) {
      $this->TagLIbXml = $xml;
    }
    
    /**
     *
     *  Returns web file extenstions.
     *  
     *  @return    web file extenstions          
     *
     */                   
    public function getFileEx() {
      return array(WEB_TYPE_CSS => "css", WEB_TYPE_JS => "js", WEB_TYPE_JPG => "jpg", WEB_TYPE_GIF => "gif", 
                            WEB_TYPE_PNG => "png", WEB_TYPE_PDF => "pdf", WEB_TYPE_RAR => "rar", WEB_TYPE_ZIP => "zip", 
                            WEB_TYPE_TXT => "txt", WEB_TYPE_XML => "xml", WEB_TYPE_XSL => "xsl", WEB_TYPE_DTD => "dtd",
                            WEB_TYPE_HTML => "html", WEB_TYPE_PHP => "php", WEB_TYPE_SQL => "sql", WEB_TYPE_C => "c",
                            WEB_TYPE_CPP => "cpp", WEB_TYPE_H => "h", WEB_TYPE_JAVA => "java", WEB_TYPE_SWF => "swf",
														WEB_TYPE_MP3 => "mp3", WEB_TYPE_PSD => "psd", WEB_TYPE_DOC => "doc", WEB_TYPE_PPT => "ppt",
														WEB_TYPE_XLS => "xls", WEB_TYPE_MPEG => "mpeg", WEB_TYPE_MOV => "mov",
														WEB_TYPE_BMP => "bmp", WEB_TYPE_AVI => "avi", WEB_TYPE_ICO => "ico");
      
    }
    
    /**
     *
     *  Generates frame.
     *  
     *  @param    label     frame label
     *  @param    content   frame content
     *  @param    classes   extra classes for frame-cover
     *  @return   content in frame          
     *
     */                   
    public function getFrame($label, $content, $classes, $ignoreFirstFrame = false) {
    	global $phpObject;
    	global $dbObject;
    	global $loginObject;
    	include_once('System.class.php');
    
	    $escapeChars = array("ě" => "e", "é" => "e", "ř" => "r", "ť" => "t", "ý" => "y", "ú" => "u", "ů" => "u", "í" => "i", "ó" => "o", "á" => "a", "š" => "s", "ď" => "d", "ž" => "z", "č" => "c", "ň" => "n");
    	$name = 'Frame.'.strtolower(str_replace(' ', '', $label));
    	$name = $phpObject->str_tr($name, ':');
    	$name = $name[0];
    	$name = strtr($name, $escapeChars); 
    	
    	$system = new System();
    	$value = $system->getPropertyValue($name);
    	$closed = false;
    	if($value == 'true') {
				$closed = true;
			}
			
			$addAttrs;
			if($_REQUEST['__TEMPLATE'] == 'xml') {
				$props = $dbObject->fetchAll('SELECT `left`, `top`, `width`, `height`, `maximized` FROM `window_properties` WHERE `frame_id` = "'.$name.'" AND `user_id` = '.$loginObject->getUserId().';');
				if(count($props) == 1) {
					$addAttrs = 'left="'.$props[0]['left'].'" top="'.$props[0]['top'].'" width="'.$props[0]['width'].'" height="'.$props[0]['height'].'" maximized="'.($props[0]['maximized'] ? "true" : "false").'"';
				}
			}
    
      $return = ''
      .'<div id="'.$name.'" class="frame frame-cover '.$name.''.((strlen($classes)) ? ' '.$classes : '').(((!$this->FirstFrame && !$ignoreFirstFrame) || $closed) ? ' closed-frame' : '').'"'.(($addAttrs != "") ? ' '.$addAttrs : '').'>'
        .'<div class="frame frame-head">'
          .'<div class="frame-label">'
            .$label
          .'</div>'
          .'<div class="frame-close">'
            .'<a class="click-able click-able-roll" href="#"><span>^</span></a>'
          .'</div>'
          .'<div class="clear"></div>'
        .'</div>'
        .'<div class="frame frame-body">'
          .$content
        .'</div>'
      .'</div>';
      if(!$ignoreFirstFrame) {
        $this->FirstFrame = false;
      }
      return $return;
    }
  }

?>
