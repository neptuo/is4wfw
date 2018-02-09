<?php

/**
 *
 *  Require base tag lib class.
 *
 */
require_once("BaseTagLib.class.php");
require_once("scripts/php/classes/UrlResolver.class.php");
require_once("scripts/php/classes/ViewHelper.class.php");

/**
 * 
 *  Class Js.
 * 	System javascripts	     
 *      
 *  @author     Marek SMM
 *  @timestamp  2010-06-02
 * 
 */
class Js extends BaseTagLib {

    private $includedScripts = array();
    private $includedStyles = array();

    public function __construct() {
        parent::setTagLibXml("xml/Js.xml");

        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    }

    public function formatScript($path) {
        if (in_array($path, $this->includedScripts)) {
            return null;
        }
        
        $this->includedScripts[] = $path;
        return '<script type="text/javascript" src="' . $path . '?version=' . WEB_VERSION . '"></script>';
    }

    public function formatStyle($path) {
        if (in_array($path, $this->includedStyles)) {
            return null;
        }
        
        $this->includedStyles[] = $path;
        return '<link rel="stylesheet" href="' . $path . '?version=' . WEB_VERSION . '" type="text/css" />';
    }

    /**
     *
     * 	Returns all javascripts and stylesheets required by cms.
     *
     * 	@param		useWindows				if true, includes scripts for windows
     *
     */
    public function getCmsResources($useWindows) {
        $return = '';

        if ($useWindows) {
            $return .= ''
                . self::formatStyle('~/css/editor.css')
                . self::formatStyle('~/css/edit-area.css')
                . self::formatStyle('~/css/window.css')
                . self::formatStyle('~/css/jquery-autocomplete.css')
                . self::formatStyle('~/css/jquery-wysiwyg.css')
                . self::formatStyle('~/css/demo_table.css')
                . self::formatScript('~/edit_area/edit_area_full.js')
                . self::formatScript('~/js/jquery/jquery.js')
                . self::formatScript('~/js/jquery/jquery-autocomplete-pack.js')
                . self::formatScript('~/js/jquery/jquery-blockui.js')
                . self::formatScript('~/js/jquery/jquery-dataTables-min.js')
                . self::formatScript('~/js/jquery/jquery-wysiwyg.js')
                . self::formatScript('~/js/cookies.js')
                . self::formatScript('~/js/functions.js')
                . self::formatScript('~/js/window.js')
                . self::formatScript('~/js/domready.js')
                . self::formatScript('~/js/rxmlhttp.js')
                . self::formatScript('~/js/links.js')
                . self::formatScript('~/js/processform.js')
                . self::formatScript('~/js/Closer.js')
                . self::formatScript('~/js/Confirm.js')
                . self::formatScript('~/js/Editor.js')
                . self::formatScript('~/js/FileName.js')
                . self::formatScript('~/js/CountDown.js')
                . self::formatScript('~/js/formFieldEffect.js')
                . self::formatScript('~/js/init.js')
                . self::formatScript('~/tiny-mce/tinymce.min.js')
                . self::formatScript('~/scripts/js/initTiny.js');
        } else {
            $return .= ''
                . self::formatStyle('~/css/cms_nowindows.css')
                . self::formatStyle('~/css/editor.css')
                . self::formatStyle('~/css/edit-area.css')
                . self::formatStyle('~/css/jquery-wysiwyg.css')
                . self::formatScript('~/edit_area/edit_area_full.js')
                . self::formatScript('~/js/jquery/jquery.js')
                . self::formatScript('~/js/jquery/jquery-wysiwyg.js')
                . self::formatScript('~/js/cookies.js')
                . self::formatScript('~/js/functions.js')
                . self::formatScript('~/js/domready.js')
                . self::formatScript('~/js/rxmlhttp.js')
                . self::formatScript('~/js/links.js')
                . self::formatScript('~/js/processform.js')
                . self::formatScript('~/js/Closer.js')
                . self::formatScript('~/js/Confirm.js')
                . self::formatScript('~/js/Editor.js')
                . self::formatScript('~/js/FileName.js')
                . self::formatScript('~/js/CountDown.js')
                . self::formatScript('~/js/formFieldEffect.js')
                . self::formatScript('~/js/init_nowindows.js')
                . self::formatScript('~/tiny-mce/tinymce.min.js')
                . self::formatScript('~/scripts/js/initTiny.js');
        }
        if (strpos($_SERVER['REQUEST_URI'], ".view") == -1) {
            $return = str_replace("~/", UrlResolver::combinePath(WEB_ROOT, UrlResolver::combinePath(UrlResolver::parseScriptRoot($_SERVER['SCRIPT_NAME'], 'file.php'), WEB_ROOT)), $return);
        } else {
            $return = ViewHelper::resolveUrl($return);
        }

        return $return;
    }

    public function getAjaxWeb($webContentRootElId, $rootPageId, $ajaxMessage = false) {
        $return = '';

        if (trim($ajaxMessage) == '') {
            $ajaxMessage = 'Loading ...';
        }

        $return .= self::formatScript('~/js/domready.js');
        $return .= self::formatScript('~/js/rxmlhttp.js');
        $return .= self::formatScript('~/js/links.js');

        $content = file_get_contents("scripts/js/web/ajaxWebInit.js");
        $content = str_replace("{web-content}", $webContentRootElId, $content);
        $content = str_replace("{root-page}", $rootPageId, $content);
        $content = str_replace("{ajax-message}", $ajaxMessage, $content);

        $return .= '<script type="text/javascript">' . $content . '</script>';

        return $return;
    }

    public function addResourcesToPage($names, $type, $as = false) {
        global $phpObject;
        $return .= '';

        if ($type != "js" && $type != "css") {
            $type = "js";
        }

        $namesAsArray = $phpObject->str_tr($names, ',');
        foreach ($namesAsArray as $name) {
            $path = $type == "js" ? "js/" . trim($name) . ".js" : "css/" . trim($name) . ".css";
            if (file_exists()) {
                $path = "scripts/" . $path;
                $virtualPath = '~/' . $path;
                if ($as == 'inline') {
                    $return .= ($type == "js") ? '<script type="text/javascript">' . "\n" . file_get_contents($path) . "\n" . '</script>' : '<style type="text/css">' . "\n" . file_get_contents($path) . "\n" . '</style>';
                } else {
                    $return .= ($type == "js") ? self::formatScript($path) : self::formatStyle($path);
                }
            }
        }

        return $return;
    }

    public function tinyMce($ids, $language = '') {
        $return = ''
        . self::formatScript('~/js/jquery/jquery.js')
        . self::formatScript('~/tiny-mce/tinymce.min.js')
        . self::formatScript('~/scripts/js/initTiny.js');

        $ids = parent::php()->str_tr($ids, ',');
        if (count($ids) > 0) {
            $return .= '<script type="text/javascript"> $(function() { ';

            foreach ($ids as $id) {
                $arguments = '"' . $id . '"';
                if ($language != '') {
                    $arguments .= ', "' . $language . '"';
                }

                $return .= 'initTiny(' . $arguments . ');';
            }

            $return .= ' });</script>';
        }

        return $return;
    }
}

?>
