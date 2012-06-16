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

    public function __construct() {
        parent::setTagLibXml("xml/Js.xml");
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
                    . '<link rel="stylesheet" href="~/css/editor.css?version='.WEB_VERSION.'" type="text/css" />'
                    . '<link rel="stylesheet" href="~/css/edit-area.css?version='.WEB_VERSION.'" type="text/css" />'
                    . '<link rel="stylesheet" href="~/css/window.css?version='.WEB_VERSION.'" type="text/css" />'
                    . '<link rel="stylesheet" href="~/css/jquery-autocomplete.css?version='.WEB_VERSION.'" type="text/css" />'
                    . '<link rel="stylesheet" href="~/css/jquery-wysiwyg.css?version='.WEB_VERSION.'" type="text/css" />'
                    . '<link rel="stylesheet" href="~/css/demo_table.css?version='.WEB_VERSION.'" type="text/css" />'
                    . '<script type="text/javascript" src="~/edit_area/edit_area_full.js?version='.WEB_VERSION.'"></script>'
                    . '<script type="text/javascript" src="~/js/jquery/jquery.js?version='.WEB_VERSION.'"></script>'
                    . '<script type="text/javascript" src="~/js/jquery/jquery-autocomplete-pack.js?version='.WEB_VERSION.'"></script>'
                    . '<script type="text/javascript" src="~/js/jquery/jquery-blockui.js?version='.WEB_VERSION.'"></script>'
                    . '<script type="text/javascript" src="~/js/jquery/jquery-dataTables-min.js?version='.WEB_VERSION.'"></script>'
                    . '<script type="text/javascript" src="~/js/jquery/jquery-wysiwyg.js?version='.WEB_VERSION.'"></script>'
                    . '<script type="text/javascript" src="~/js/cookies.js?version='.WEB_VERSION.'"></script>'
                    . '<script type="text/javascript" src="~/js/functions.js?version='.WEB_VERSION.'"></script>'
                    . '<script type="text/javascript" src="~/js/window.js?version='.WEB_VERSION.'"></script>'
                    . '<script type="text/javascript" src="~/js/domready.js?version='.WEB_VERSION.'"></script>'
                    . '<script type="text/javascript" src="~/js/rxmlhttp.js?version='.WEB_VERSION.'"></script>'
                    . '<script type="text/javascript" src="~/js/links.js?version='.WEB_VERSION.'"></script>'
                    . '<script type="text/javascript" src="~/js/processform.js?version='.WEB_VERSION.'"></script>'
                    . '<script type="text/javascript" src="~/js/Closer.js?version='.WEB_VERSION.'"></script>'
                    . '<script type="text/javascript" src="~/js/Confirm.js?version='.WEB_VERSION.'"></script>'
                    . '<script type="text/javascript" src="~/js/Editor.js?version='.WEB_VERSION.'"></script>'
                    . '<script type="text/javascript" src="~/js/FileName.js?version='.WEB_VERSION.'"></script>'
                    . '<script type="text/javascript" src="~/js/CountDown.js?version='.WEB_VERSION.'"></script>'
                    . '<script type="text/javascript" src="~/js/formFieldEffect.js?version='.WEB_VERSION.'"></script>'
                    . '<script type="text/javascript" src="~/js/init.js?version='.WEB_VERSION.'"></script>'
                    . '<script type="text/javascript" src="~/tiny-mce/tiny_mce.js?version='.WEB_VERSION.'"></script>'
                    . '<script type="text/javascript" src="~/scripts/js/initTiny.js?version='.WEB_VERSION.'"></script>';
        } else {
            $return .= ''
                    . '<link rel="stylesheet" href="~/css/cms_nowindows.css?version='.WEB_VERSION.'" type="text/css" />'
                    . '<link rel="stylesheet" href="~/css/editor.css?version='.WEB_VERSION.'" type="text/css" />'
                    . '<link rel="stylesheet" href="~/css/edit-area.css?version='.WEB_VERSION.'" type="text/css" />'
                    . '<link rel="stylesheet" href="~/css/jquery-wysiwyg.css?version='.WEB_VERSION.'" type="text/css" />'
                    . '<script type="text/javascript" src="~/edit_area/edit_area_full.js?version='.WEB_VERSION.'"></script>'
                    . '<script type="text/javascript" src="~/js/jquery/jquery.js?version='.WEB_VERSION.'"></script>'
                    . '<script type="text/javascript" src="~/js/jquery/jquery-wysiwyg.js?version='.WEB_VERSION.'"></script>'
                    . '<script type="text/javascript" src="~/js/cookies.js?version='.WEB_VERSION.'"></script>'
                    . '<script type="text/javascript" src="~/js/functions.js?version='.WEB_VERSION.'"></script>'
                    . '<script type="text/javascript" src="~/js/domready.js?version='.WEB_VERSION.'"></script>'
                    . '<script type="text/javascript" src="~/js/rxmlhttp.js?version='.WEB_VERSION.'"></script>'
                    . '<script type="text/javascript" src="~/js/links.js?version='.WEB_VERSION.'"></script>'
                    . '<script type="text/javascript" src="~/js/processform.js?version='.WEB_VERSION.'"></script>'
                    . '<script type="text/javascript" src="~/js/Closer.js?version='.WEB_VERSION.'"></script>'
                    . '<script type="text/javascript" src="~/js/Confirm.js?version='.WEB_VERSION.'"></script>'
                    . '<script type="text/javascript" src="~/js/Editor.js?version='.WEB_VERSION.'"></script>'
                    . '<script type="text/javascript" src="~/js/FileName.js?version='.WEB_VERSION.'"></script>'
                    . '<script type="text/javascript" src="~/js/CountDown.js?version='.WEB_VERSION.'"></script>'
                    . '<script type="text/javascript" src="~/js/formFieldEffect.js?version='.WEB_VERSION.'"></script>'
                    . '<script type="text/javascript" src="~/js/init_nowindows.js?version='.WEB_VERSION.'"></script>'
                    . '<script type="text/javascript" src="~/tiny-mce/tiny_mce.js?version='.WEB_VERSION.'"></script>'
                    . '<script type="text/javascript" src="~/scripts/js/initTiny.js?version='.WEB_VERSION.'"></script>';
        }
        if(strpos($_SERVER['REDIRECT_URL'], ".view") == -1) {
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

        $return .= '<script type="text/javascript" src="~/js/domready.js?version='.WEB_VERSION.'"></script>';
        $return .= '<script type="text/javascript" src="~/js/rxmlhttp.js?version='.WEB_VERSION.'"></script>';
        $return .= '<script type="text/javascript" src="~/js/links.js?version='.WEB_VERSION.'"></script>';

        $content = file_get_contents("scripts/js/web/ajaxWebInit.js?version='.WEB_VERSION.'");
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
            if (file_exists("scripts/" . $path)) {
                if ($as == 'inline') {
                    $return .= ( $type == "js") ? '<script type="text/javascript">' . "\n" . file_get_contents("scripts/" . $path) . "\n" . '</script>' : '<style type="text/css">' . "\n" . file_get_contents("scripts/" . $path) . "\n" . '</style>';
                } else {
                    $return .= ( $type == "js") ? '<script type="text/javascript" src="~/' . $path . '"></script>' : '<link rel="stylesheet" href="~/' . $path . '" type="text/css" />';
                }
            }
        }

        return $return;
    }

}

?>
