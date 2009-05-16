<?php

  /**
   *
   *  Base class for all tag libs.
   *  
   *  @author     Marek SMM
   *  @timestamp  2009-01-24
   *  @version    1.06
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
                   WEB_TYPE_CPP => "cpp", WEB_TYPE_H => "h", WEB_TYPE_JAVA => "java");
      
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
      $return = ''
      .'<div class="frame frame-cover'.((strlen($classes)) ? ' '.$classes : '').((!$this->FirstFrame && !$ignoreFirstFrame) ? ' closed-frame' : '').'">'
        .'<div class="frame frame-head">'
          .'<div class="frame-label">'
            .$label
          .'</div>'
          .'<div class="frame-close">'
            .'<a class="click-able click-able-roll" href="#"><span>^</span></a>'
          .'</div>'
          .'<div class="clear"></div>'
        .'</div>'
        .'<div class="frame frame-body"'.((!$this->FirstFrame && !$ignoreFirstFrame) ? ' style="display: none;"' : '').'>'
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
