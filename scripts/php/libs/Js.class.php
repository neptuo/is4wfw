<?php

  /**
   *
   *  Require base tag lib class.
   *
   */
  require_once("BaseTagLib.class.php");
  
  /**
   * 
   *  Class Js.
   * 	System javascripts	     
   *      
   *  @author     Marek SMM
   *  @timestamp  2010-04-15
   * 
   */  
  class Js extends BaseTagLib {
  
    public function __construct() {
      parent::setTagLibXml("xml/Js.xml");
    }
    
    /**
     *
     *	Returns all javascripts and stylesheets required by cms.
     *	
     *	@param		useWindows				if true, includes scripts for windows		      
     *
     */		 		 		     
    public function getCmsResources($useWindows) {
    	$return = '';
    	
    	if($useWindows) {
    		$return .= ''
    			.'<link rel="stylesheet" href="~/css/editor.css" type="text/css" />'
					.'<link rel="stylesheet" href="~/css/edit-area.css" type="text/css" />'
					.'<link rel="stylesheet" href="~/css/window.css" type="text/css" />'
					.'<link rel="stylesheet" href="~/css/jquery-autocomplete.css" type="text/css" />'
					.'<link rel="stylesheet" href="~/css/jquery-wysiwyg.css" type="text/css" />'
					.'<link rel="stylesheet" href="~/css/demo_table.css" type="text/css" />'
					.'<script type="text/javascript" src="~/edit_area/edit_area_full.js"></script>'
					.'<script type="text/javascript" src="~/js/jquery/jquery.js"></script>'
					.'<script type="text/javascript" src="~/js/jquery/jquery-autocomplete-pack.js"></script>'
					.'<script type="text/javascript" src="~/js/jquery/jquery-blockui.js"></script>'
					.'<script type="text/javascript" src="~/js/jquery/jquery-dataTables-min.js"></script>'
					.'<script type="text/javascript" src="~/js/jquery/jquery-wysiwyg.js"></script>'
					.'<script type="text/javascript" src="~/js/functions.js"></script>'
					.'<script type="text/javascript" src="~/js/window.js"></script>'
					.'<script type="text/javascript" src="~/js/domready.js"></script>'
					.'<script type="text/javascript" src="~/js/rxmlhttp.js"></script>'
					.'<script type="text/javascript" src="~/js/links.js"></script>'
					.'<script type="text/javascript" src="~/js/processform.js"></script>'
					.'<script type="text/javascript" src="~/js/Closer.js"></script>'
					.'<script type="text/javascript" src="~/js/Confirm.js"></script>'
					.'<script type="text/javascript" src="~/js/Editor.js"></script>'
					.'<script type="text/javascript" src="~/js/FileName.js"></script>'
					.'<script type="text/javascript" src="~/js/CountDown.js"></script>'
					.'<script type="text/javascript" src="~/js/formFieldEffect.js"></script>'
					.'<script type="text/javascript" src="~/js/init.js"></script>'
					.'<script type="text/javascript" src="~/tiny-mce/tiny_mce.js"></script>'
					.'<script type="text/javascript" src="~/scripts/js/initTiny.js"></script>';
    	} else {
    		$return .= ''
    			.'<link rel="stylesheet" href="~/css/cms_nowindows.css" type="text/css" />'
					.'<link rel="stylesheet" href="~/css/editor.css" type="text/css" />'
					.'<link rel="stylesheet" href="~/css/edit-area.css" type="text/css" />'
					.'<link rel="stylesheet" href="~/css/jquery-wysiwyg.css" type="text/css" />'
					.'<script type="text/javascript" src="~/edit_area/edit_area_full.js"></script>'
					.'<script type="text/javascript" src="~/js/jquery/jquery.js"></script>'
					.'<script type="text/javascript" src="~/js/jquery/jquery-wysiwyg.js"></script>'
					.'<script type="text/javascript" src="~/js/functions.js"></script>'
					.'<script type="text/javascript" src="~/js/domready.js"></script>'
					.'<script type="text/javascript" src="~/js/rxmlhttp.js"></script>'
					.'<script type="text/javascript" src="~/js/links.js"></script>'
					.'<script type="text/javascript" src="~/js/processform.js"></script>'
					.'<script type="text/javascript" src="~/js/Closer.js"></script>'
					.'<script type="text/javascript" src="~/js/Confirm.js"></script>'
					.'<script type="text/javascript" src="~/js/Editor.js"></script>'
					.'<script type="text/javascript" src="~/js/FileName.js"></script>'
					.'<script type="text/javascript" src="~/js/CountDown.js"></script>'
					.'<script type="text/javascript" src="~/js/formFieldEffect.js"></script>'
					.'<script type="text/javascript" src="~/js/init_nowindows.js"></script>'
					.'<script type="text/javascript" src="~/tiny-mce/tiny_mce.js"></script>'
					.'<script type="text/javascript" src="~/scripts/js/initTiny.js"></script>';
    	}
    	$return = str_replace("~/", WEB_ROOT, $return); 
    	
    	return $return;
    }
  }

?>
