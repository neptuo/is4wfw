<?php

  /**
   *
   *  Require base tag lib class.
   *
   */
  require_once("BaseTagLib.class.php");
  
  /**
   * 
   *  Class Hint.
   * 	Manul for custom tags	     
   *      
   *  @author     Marek SMM
   *  @timestamp  2009-07-25
   * 
   */  
  class Hint extends BaseTagLib {
  
  	private $BundleName = 'hint';
  	
  	private $BundleLang = 'cs';
  
    public function __construct() {
    	global $webObject;
    
      parent::setTagLibXml("xml/Hint.xml");
      
      if($webObject->LanguageName != '') {
				$rb = new ResourceBundle();
				if($rb->testBundleExists($this->BundleName, $webObject->LanguageName)) {
					$BundleLang = $webObject->LanguageName;
				}
			}
      
      require_once("scripts/php/classes/ResourceBundle.class.php");
    }
    
    /**
     *
     *	Generates hint for selected library.
     *	C tag.
     *	
     *	@param		classPath					library class path
     *	@param		userFrames				use frames in outpout
     *	@param		showMsg						show error messages in output		 		 		 		      
     *
     *
     */	 		 		 		     
    public function showHintForLib($classPath = false, $useFrames = false, $showMsg = false) {
    	global $phpObject;
    	$rb = new ResourceBundle();
			$rb->loadBundle($this->BundleName, $this->BundleLang);
			$return = '';
			
			$cpArray = $phpObject->str_tr($classPath, '.');
			$xmlPath = SCRIPTS;
			for($i = 0; $i < count($cpArray); $i ++) {
				if($i < count($cpArray) - 1) {
					$xmlPath .= $cpArray[$i].'/';
				} else {
					$xmlPath .= 'xml/'.$cpArray[$i].'.xml';
				}
			}
			
			if(is_file($xmlPath)) {
        $xml = new SimpleXMLElement(file_get_contents($xmlPath));
        
        $return .= ''
        .'<div class="hint-lib">'
        	.'<div class="lib-head">'
						.'<h1>'.$xml->name.' ( '.$xml->classpath.'.'.$xml->classname.' )</h1>'
	    	    .'<strong class="version">'.$rb->get('lib.version').': '.$xml->version.', '.$rb->get('lib.count-of-instances').': '.$xml->count.'</strong>'
        	.'</div>'
        	.'<div class="clear"></div>'
        	.'<div class="tag-h2">'
        		.'<h2>'.$rb->get('lib.tags').':</h2>'
        	.'</div>'
				.'';
        
        foreach($xml->tag as $tag) {
    	    $attributes = '';
  	      for($i = 0; $i < count($tag->attribute); $i ++) {
	        $attributes .= ''
        	  .'<tr>'
      	  	  .'<td class="att-name">'.$tag->attribute[$i]->attname.'</td>'
    	      	.'<td class="att-req">'.$tag->attribute[$i]->attreq.'</td>'
  	      	.'</tr>';
	        }
        	$return .= ''
        	.'<div class="lib-tag">'
        		.'<div class="lib-tag-head">'
        			.'<h3>'.$tag->tagname.'</h3><p>'.$tag->comment.'</p>'
        			.'<div class="clear"></div>'
        		.'</div>'
        		.'<div class="lib-tag-attrs">'
        			.((strlen($attributes) > 0) ? ''
        			.'<table>'
        				.'<tr>'
	        				.'<th class="att-name">'.$rb->get('lib.attname').'</th>'
  	      				.'<th class="att-req">'.$rb->get('lib.attreq').'</th>'
        				.'</tr>'
        				.$attributes
          		.'</table>'
          		: '<p>'.$rb->get('lib.noattrs').'</p>')
          	.'</div>'
          .'</div>';
        }
        $return .= ''	
					.'<div class="tag-h2">'
        		.'<h2>'.$rb->get('lib.properties').':</h2>'
        	.'</div>'
				.'';
        
        foreach($xml->property as $prop) {
        	$return .= ''
        	.'<div class="lib-tag">'
        		.'<div class="lib-tag-head">'
        			.'<h3>'.$prop->propname.'</h3><p>'.$prop->comment.'</p>'
        			.'<div class="clear"></div>'
        		.'</div>'
          .'</div>';
        }
        $return .= '</div>';
      } else {
        $str = "Xml library definition doesn't exists! [".$xmlPath."]";
        if($showMsg != 'false') {
					$return .= $str;
				}
      }
			
    
			if($useFrames == "false") {
				return $return;
			} else {
				return parent::getFrame($rb->get('lib.title').': '.$classPath, $return, "", true);
			}
		}
		
		/**
		 *
		 *
		 *
		 *
		 */		 		 		 		 		
		public function selectClassPath($useFrames = false, $showMsg = false) {
    	$rb = new ResourceBundle();
			$rb->loadBundle($this->BundleName, $this->BundleLang);
			$return = '';
			
			if($_POST['select-class-path-submit'] == $rb->get('select-class-path.submit')) {
				$_SESSION['select-class-path'] = $_POST['select-class-path-select'];
				if($showMsg != 'false') {
					$return .= '<h4 class="success">'.$rb->get('select-class-path.success').'</h4>';
				}
			} else {
				if($_SESSION['select-class-path'] == '') {
					$_SESSION['select-class-path'] = 'php.libs.Article';
				}
			}
			
			$return .= ''
			.'<div class="select-class-path">'
				.'<form name="select-class-path" method="post" action="">'
					.'<label for="select-class-path-select">'.$rb->get('select-class-path.label').':</label> '
					.'<select id="select-class-path-select" name="select-class-path-select">'
						.'<option value="php.libs.Article"'.($_SESSION['select-class-path'] == 'php.libs.Article' ? 'selected="selected"' : '').'>php.libs.Article</option>'
						.'<option value="php.libs.Counter"'.($_SESSION['select-class-path'] == 'php.libs.Counter' ? 'selected="selected"' : '').'>php.libs.Counter</option>'
						.'<option value="php.libs.Database"'.($_SESSION['select-class-path'] == 'php.libs.Database' ? 'selected="selected"' : '').'>php.libs.Database</option>'
						.'<option value="php.libs.DefaultPhp"'.($_SESSION['select-class-path'] == 'php.libs.DefaultPhp' ? 'selected="selected"' : '').'>php.libs.DefaultPhp</option>'
						.'<option value="php.libs.DefaultWeb"'.($_SESSION['select-class-path'] == 'php.libs.DefaultWeb' ? 'selected="selected"' : '').'>php.libs.DefaultWeb</option>'
						.'<option value="php.libs.Error"'.($_SESSION['select-class-path'] == 'php.libs.Error' ? 'selected="selected"' : '').'>php.libs.Error</option>'
						.'<option value="php.libs.File"'.($_SESSION['select-class-path'] == 'php.libs.File' ? 'selected="selected"' : '').'>php.libs.File</option>'
						.'<option value="php.libs.Form"'.($_SESSION['select-class-path'] == 'php.libs.Form' ? 'selected="selected"' : '').'>php.libs.Form</option>'
						.'<option value="php.libs.Guestbook"'.($_SESSION['select-class-path'] == 'php.libs.Guestbook' ? 'selected="selected"' : '').'>php.libs.Guestbook</option>'
						.'<option value="php.libs.Hint"'.($_SESSION['select-class-path'] == 'php.libs.Hint' ? 'selected="selected"' : '').'>php.libs.Hint</option>'
						.'<option value="php.libs.hp.Hotproject"'.($_SESSION['select-class-path'] == 'php.libs.hp.Hotproject' ? 'selected="selected"' : '').'>php.libs.hp.Hotproject</option>'
						.'<option value="php.libs.Log"'.($_SESSION['select-class-path'] == 'php.libs.Log' ? 'selected="selected"' : '').'>php.libs.Log</option>'
						.'<option value="php.libs.Login"'.($_SESSION['select-class-path'] == 'php.libs.Login' ? 'selected="selected"' : '').'>php.libs.Login</option>'
						.'<option value="php.libs.Page"'.($_SESSION['select-class-path'] == 'php.libs.Page' ? 'selected="selected"' : '').'>php.libs.Page</option>'
						.'<option value="php.libs.Sport"'.($_SESSION['select-class-path'] == 'php.libs.Sport' ? 'selected="selected"' : '').'>php.libs.Sport</option>'
						.'<option value="php.libs.user"'.($_SESSION['select-class-path'] == 'php.libs.User' ? 'selected="selected"' : '').'>php.libs.User</option>'
						.'<option value="php.libs.WebProject"'.($_SESSION['select-class-path'] == 'php.libs.WebProject' ? 'selected="selected"' : '').'>php.libs.WebProject</option>'
					.'</select>'
					.'<input type="submit" name="select-class-path-submit" value="'.$rb->get('select-class-path.submit').'" />'
				.'</form>'
			.'</div>';
			
			if($useFrames == "false") {
				return $return;
			} else {
				return parent::getFrame($rb->get('lib.title').': '.$classPath, $return, "", true);
			}
		}
		
		public function setPropClassPath($classPath) {
			$_SESSION['select-class-path'] = $classPath;
			return $classPath;
		}
		
		public function getPropClassPath() {
			return $_SESSION['select-class-path'];
		}
  }

?>
