<?php

  /**
   *
   *  Require base tag lib class.
   *
   */
  require_once("BaseTagLib.class.php");
  require_once("scripts/php/classes/ResourceBundle.class.php");
  require_once("scripts/php/classes/FullTagParser.class.php");
  require_once("scripts/php/classes/ViewHelper.class.php");
  
  /**
   * 
   *  Class View.	     
   *      
   *  @author     Marek SMM
   *  @timestamp  2010-08-06
   * 
   */  
  class Menu extends BaseTagLib {
  
  	private $BundleName = 'view';
  	private $BundleLang = 'cs';
  
    public function __construct() {
    	
      parent::setTagLibXml("xml/Menu.xml");
      
      if($webObject->LanguageName != '') {
				$rb = new ResourceBundle();
				if($rb->testBundleExists($this->BundleName, $webObject->LanguageName)) {
					$this->BundleLang = $webObject->LanguageName;
				}
			}
    }
    
    /* ======================= TAGS ========================================= */
    
    public function showXmlMenu($path) {
    	$return = '';
    	
    	$xml = new SimpleXMLElement(file_get_contents(ViewHelper::resolveViewRoot($path)));
    	$i = 0;
    	
    	$return .= '<div class="menu"><ul class="ul-1">';
    	
    	foreach($xml->item as $item) {
  		  $i++;
    		$attrs = $item->attributes();
    		if(isset($attrs['requireGroup'])) {
      		global $loginObject;
	    	  $ok = false;
  		    foreach($loginObject->getGroups() as $group) {
  		    	if($group['name'] == $attrs['requireGroup']) {
  		    		$ok = true;
  		    		break;
  		  		}
  		  	}
  		  	if(!$ok) {
  		  		continue;
  		  	}
	  	  }
    		
    		$name = $attrs['name'];
    		$url = ViewHelper::resolveUrl($attrs['url']);
    		if($url == '/'.$_REQUEST['WEB_PAGE_PATH']) {
					$active = true;
				} else {
					$active = false;
				}
    	
    		$return .= ''
				.'<li class="menu-item li-'.$i.(($active) ? ' active-item' : '').'">'
					.'<div class="link'.(($parent) ? ' active-parent-link' : '').(($active) ? ' active-link' : '').'">'
						.'<a href="'.$url.'"'.((isset($attrs['rel'])) ? ' rel="'.$attrs['rel'].'"' : '').'>'
							.'<span>'.$name.'</span>'
						.'</a>'
					.'</div>'
				.'</li>';
    	}
    	
    	$return .= '</ul></div>';
    	
    	return $return;	
    }
    
    /* ============================= FUNCTIONS =========================================== */
    
    
    
  }

?>
