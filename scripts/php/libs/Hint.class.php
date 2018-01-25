<?php

  /**
   *
   *  Require base tag lib class.
   *
   */
  require_once("BaseTagLib.class.php");
  require_once("scripts/php/classes/LocalizationBundle.class.php");
  require_once("scripts/php/classes/ui/BaseGrid.class.php");
  
	/**
	 * 
	 *  Class Hint.
	 *  Manual for custom tags         
	 *      
	 *  @author     Marek SMM
	 *  @timestamp  2012-01-10
	 * 
	 */  
    class Hint extends BaseTagLib {
  
    	private $BundleName = 'hint';
        private $BundleLang = 'cs';
  
    	public function __construct() {
        	global $webObject;
    
      		parent::setTagLibXml("xml/Hint.xml");
      
      		if ($webObject->LanguageName != '') {
                $rb = new LocalizationBundle();
                if($rb->testBundleExists($this->BundleName, $webObject->LanguageName)) {
                    $BundleLang = $webObject->LanguageName;
                }
            }
    	}
    
    /**
     *
     *    Generates hint for selected library.
     *    C tag.
     *    
     *    @param        classPath                    library class path
     *    @param        userFrames                use frames in outpout
     *    @param        showMsg                        show error messages in output                                         
     *
     *
     */                                    
    public function showHintForLib($classPath = false, $useFrames = false, $showMsg = false) {
        global $phpObject;
        $rb = new LocalizationBundle();
        $rb->loadBundle($this->BundleName, $this->BundleLang);
        $return = '';
        
        $cpArray = $phpObject->str_tr($classPath, '.');
        $xmlPath = SCRIPTS;
        for ($i = 0; $i < count($cpArray); $i ++) {
            if($i < count($cpArray) - 1) {
                $xmlPath .= $cpArray[$i].'/';
            } else {
                $xmlPath .= 'xml/'.$cpArray[$i].'.xml';
            }
        }
            
        if (is_file($xmlPath)) {
            $xml = new SimpleXMLElement(file_get_contents($xmlPath));
            
            $links = '<div class="gray-box"><div>'.$rb->get('lib.tags').':';
            foreach($xml->tag as $tag) {
                $links .= '<a href="#'.$tag->tagname.'">'.$tag->tagname.'</a> ';
            }
            $links .= '</div><div>'.$rb->get('lib.fulltags').':';
            foreach($xml->fulltag as $tag) {
                $links .= '<a href="#'.$tag->tagname.'">'.$tag->tagname.'</a> ';
            }
            $links .= '</div><div>'.$rb->get('lib.properties').':';
            foreach($xml->property as $prop) {
                $links .= '<a href="#'.$prop->propname.'">'.$prop->propname.'</a> ';
            }
            $links .= '</div></div>';
        
            $return .= ''
            .'<div class="hint-lib">'
                .'<div class="lib-head">'
                    .'<h1>'.$xml->name.' ( '.$xml->classpath.'.'.$xml->classname.' )</h1>'
                    .'<strong class="version">'.$rb->get('lib.version').': '.$xml->version.', '.$rb->get('lib.count-of-instances').': '.$xml->count.'</strong>'
                .'</div>'
                .'<div class="clear"></div>'
                .$links
                .'<div class="tag-h2">'
                    .'<h2>'.$rb->get('lib.tags').':</h2>'
                .'</div>';
            
            foreach ($xml->tag as $tag) {
                $attributes = '';
                for($i = 0; $i < count($tag->attribute); $i ++) {
                    $attributes .= ''
                    .'<tr>'
                        .'<td class="att-name">'.$tag->attribute[$i]->attname.'</td>'
                        .'<td class="att-req">'.$tag->attribute[$i]->attreq.'</td>'
                    .'</tr>';
                }
                
                if (isset($tag->params)) {
                    $attributes .= ''
                    .'<tr>'
                        .'<td colspan="2">'.$rb->get('lib.attparams').'</td>'
                    .'</tr>';
                }
                
                $return .= ''
                .'<div class="lib-tag">'
                    .'<div class="lib-tag-head">'
                        .'<h3 id="'.$tag->tagname.'">'.$tag->tagname.'</h3><p>' . str_replace(PHP_EOL, '<br />', trim($tag->comment)) . '</p>'
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
				.'<h2>'.$rb->get('lib.fulltags').':</h2>'
			.'</div>';
                    
            foreach ($xml->fulltag as $tag) {
                $attributes = '';
                for($i = 0; $i < count($tag->attribute); $i ++) {
                    $attributes .= ''
                    .'<tr>'
                        .'<td class="att-name">'.$tag->attribute[$i]->attname.'</td>'
                        .'<td class="att-req">'.$tag->attribute[$i]->attreq.'</td>'
                    .'</tr>';
                }

                if(isset($tag->params)) {
                    $attributes .= ''
                    .'<tr>'
                        .'<td colspan="2">'.$rb->get('lib.attparams').'</td>'
                    .'</tr>';
                }
                
                $return .= ''
                .'<div class="lib-tag">'
                    .'<div class="lib-tag-head">'
                        .'<h3 id="'.$tag->tagname.'">'.$tag->tagname.'</h3><p>' . str_replace(PHP_EOL, '<br />', trim($tag->comment)) . '</p>'
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
			.'</div>';
            
            foreach($xml->property as $prop) {
                $return .= ''
                .'<div class="lib-tag">'
                    .'<div class="lib-tag-head">'
                        .'<h3 id="'.$prop->propname.'">'.$prop->propname.'</h3><p>' . str_replace(PHP_EOL, '<br />', trim($prop->comment)) . '</p>'
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
            
    
		if ($useFrames == "false") {
			return $return;
		} else {
			return parent::getFrame($rb->get('lib.title').': '.$classPath, $return, "", true);
		}
	}
        
	/**
	 *
	 *    Generates form for select taglib
	 *
	 */                                            
	public function selectClassPath($useFrames = false, $showMsg = false) {
		$rb = new LocalizationBundle();
		$rb->loadBundle($this->BundleName, $this->BundleLang);
		$return = '';
		
		if ($_POST['select-class-path-submit'] == $rb->get('select-class-path.submit')) {
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
			.'<form name="select-class-path" method="post" action="'.$_SERVER['REQUEST_URI'].'">'
				.'<label for="select-class-path-select">'.$rb->get('select-class-path.label').':</label> '
				.'<select id="select-class-path-select" name="select-class-path-select">'
					.'<option value="php.libs.Article"'.($_SESSION['select-class-path'] == 'php.libs.Article' ? 'selected="selected"' : '').'>php.libs.Article</option>'
					.'<option value="php.libs.Counter"'.($_SESSION['select-class-path'] == 'php.libs.Counter' ? 'selected="selected"' : '').'>php.libs.Counter</option>'
					.'<option value="php.libs.CustomForm"'.($_SESSION['select-class-path'] == 'php.libs.CustomForm' ? 'selected="selected"' : '').'>php.libs.CustomForm</option>'
					.'<option value="php.libs.Database"'.($_SESSION['select-class-path'] == 'php.libs.Database' ? 'selected="selected"' : '').'>php.libs.Database</option>'
					.'<option value="php.libs.DefaultPhp"'.($_SESSION['select-class-path'] == 'php.libs.DefaultPhp' ? 'selected="selected"' : '').'>php.libs.DefaultPhp</option>'
					.'<option value="php.libs.DefaultWeb"'.($_SESSION['select-class-path'] == 'php.libs.DefaultWeb' ? 'selected="selected"' : '').'>php.libs.DefaultWeb</option>'
					.'<option value="php.libs.Error"'.($_SESSION['select-class-path'] == 'php.libs.Error' ? 'selected="selected"' : '').'>php.libs.Error</option>'
					.'<option value="php.libs.File"'.($_SESSION['select-class-path'] == 'php.libs.File' ? 'selected="selected"' : '').'>php.libs.File</option>'
					.'<option value="php.libs.FileAdmin"'.($_SESSION['select-class-path'] == 'php.libs.FileAdmin' ? 'selected="selected"' : '').'>php.libs.FileAdmin</option>'
					.'<option value="php.libs.Form"'.($_SESSION['select-class-path'] == 'php.libs.Form' ? 'selected="selected"' : '').'>php.libs.Form</option>'
					.'<option value="php.libs.Guestbook"'.($_SESSION['select-class-path'] == 'php.libs.Guestbook' ? 'selected="selected"' : '').'>php.libs.Guestbook</option>'
					.'<option value="php.libs.Hint"'.($_SESSION['select-class-path'] == 'php.libs.Hint' ? 'selected="selected"' : '').'>php.libs.Hint</option>'
					.'<option value="php.libs.hp.Hotproject"'.($_SESSION['select-class-path'] == 'php.libs.hp.Hotproject' ? 'selected="selected"' : '').'>php.libs.hp.Hotproject</option>'
					.'<option value="php.libs.Image"'.($_SESSION['select-class-path'] == 'php.libs.Image' ? 'selected="selected"' : '').'>php.libs.Image</option>'
					.'<option value="php.libs.Inquiry"'.($_SESSION['select-class-path'] == 'php.libs.Inquiry' ? 'selected="selected"' : '').'>php.libs.Inquiry</option>'
					.'<option value="php.libs.Js"'.($_SESSION['select-class-path'] == 'php.libs.Js' ? 'selected="selected"' : '').'>php.libs.Js</option>'
					.'<option value="php.libs.Log"'.($_SESSION['select-class-path'] == 'php.libs.Log' ? 'selected="selected"' : '').'>php.libs.Log</option>'
					.'<option value="php.libs.Login"'.($_SESSION['select-class-path'] == 'php.libs.Login' ? 'selected="selected"' : '').'>php.libs.Login</option>'
					.'<option value="php.libs.Menu"'.($_SESSION['select-class-path'] == 'php.libs.Menu' ? 'selected="selected"' : '').'>php.libs.Menu</option>'
					.'<option value="php.libs.Page"'.($_SESSION['select-class-path'] == 'php.libs.Page' ? 'selected="selected"' : '').'>php.libs.Page</option>'
					.'<option value="php.libs.PageNG"'.($_SESSION['select-class-path'] == 'php.libs.PageNG' ? 'selected="selected"' : '').'>php.libs.PageNG</option>'
					.'<option value="php.libs.Sport"'.($_SESSION['select-class-path'] == 'php.libs.Sport' ? 'selected="selected"' : '').'>php.libs.Sport</option>'
					.'<option value="php.libs.System"'.($_SESSION['select-class-path'] == 'php.libs.System' ? 'selected="selected"' : '').'>php.libs.System</option>'
					.'<option value="php.libs.User"'.($_SESSION['select-class-path'] == 'php.libs.User' ? 'selected="selected"' : '').'>php.libs.User</option>'
					.'<option value="php.libs.View"'.($_SESSION['select-class-path'] == 'php.libs.View' ? 'selected="selected"' : '').'>php.libs.View</option>'
					.'<option value="php.libs.WebProject"'.($_SESSION['select-class-path'] == 'php.libs.WebProject' ? 'selected="selected"' : '').'>php.libs.WebProject</option>'
				.'</select> '
				.'<input type="submit" name="select-class-path-submit" value="'.$rb->get('select-class-path.submit').'" />'
			.'</form>'
		.'</div>'
		.'<div class="gray-box">'
			.'<strong>There are some special GET parameters in the system, here is list of them: </strong>'
			.'<ul>'
				.'<li>auto-login-ignore - ignore auto-login parameteres for login:login</li>'
				.'<li>duration-stats - time required to generate response</li>'
				.'<li>mem-stats - wfw memory cosumption stats</li>'
				.'<li>query-stats - counts database queries per one request</li>'
			.'</ul>'
		.'</div>';
		
		if ($useFrames == "false") {
			return $return;
		} else {
			return parent::getFrame($rb->get('lib.title2').': '.$classPath, $return, "", true);
		}
	}
        
        
    public function getPropertyList($useFrames = false) {
        $rb = new LocalizationBundle();
        $rb->loadBundle($this->BundleName, $this->BundleLang);
        
        $return = ''
        .'<div class="gray-box">'
            .$rb->get('properties.headline').':'
        .'</div>'
        .'<div class="gray-box">'
            .'<ul>'
                .'<li>Admin.Language</li>'
                .'<li>Article.author</li>'
                .'<li>Article.editAreaHeadRows</li>'
                .'<li>Article.editors</li>'
                .'<li>Article.languageId</li>'
                .'<li>Article.pageSize</li>'
                .'<li>Frames.leaveOpened</li>'
                .'<li>Login.session</li>'
                .'<li>Page.editAreaContentRows</li>'
                .'<li>Page.editAreaHeadRows</li>'
                .'<li>Page.editAreaTLEndRows</li>'
                .'<li>Page.editAreaTLStartRows</li>'
                .'<li>Page.editAreaTextFileRows</li>'
                .'<li>Page.editors</li>'
                .'<li>System.cms.windowsstyle</li>'
                .'<li>TextFiles.showFilter</li>'
                .'<li>Templates.showFilter</li>'
                .'<li>WebProject.defaultProjectId</li>'
            .'</ul>'
        .'</div>';
        
        
        if ($useFrames == "false") {
            return $return;
        } else {
            return parent::getFrame($rb->get('properties.title').': '.$classPath, $return, "", true);
        }
    }

    public function getAutoRegistered($useFrames = false) {
        $rb = new LocalizationBundle();
        $rb->loadBundle($this->BundleName, $this->BundleLang);
		
		$grid = new BaseGrid();
		$grid->setHeader(array(
			'prefix' => $rb->get('autoregister.prefix'), 
			'class' => $rb->get('autoregister.class')
		));
		
		$xml = new SimpleXMLElement(file_get_contents(PHP_SCRIPTS . 'autoregister.xml'));
		foreach ($xml->reg as $reg) {
			$attrs = $reg->attributes();
			$grid->addRow(array(
				'prefix' => (string)$attrs['prefix'], 
				'class' => (string)$attrs['class']
			));
		}
		
		$return = ''
        . '<div class="gray-box">'
            . $rb->get('autoregister.headline')
		. '</div>'
        . '<div class="gray-box">'
            . $grid->render()
		. '</div>';

        if ($useFrames == "false") {
            return $return;
        } else {
            return parent::getFrame($rb->get('autoregister.title').': '.$classPath, $return, "", true);
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
