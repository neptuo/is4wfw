<?php

    require_once("BaseTagLib.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/UrlResolver.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/ViewHelper.class.php");

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

        private $includedScripts = [];
        private $includedInlineScripts = [];
        private $includedStyles = [];
        private $includedInlineStyles = [];

        public function formatScript($path, $checkExistence = false) {
            if (in_array($path, $this->includedScripts)) {
                return null;
            }

            $this->includedScripts[] = $path;

            if ($checkExistence && !file_exists(str_replace("~/", APP_SCRIPTS_PATH, $path))) {
                return null;
            }

            $minPath = str_replace(".js", ".min.js", $path);
            if (file_exists(str_replace("~/", APP_SCRIPTS_PATH, $minPath))) {
                $path = $minPath;
            }
            
            $path = UrlUtils::addParameter($path, "version", WEB_VERSION);
            return parent::web()->formatScript($path);
        }
        
        public function formatStyle($path) {
            if (in_array($path, $this->includedStyles)) {
                return null;
            }
            
            $this->includedStyles[] = $path;
            $path = UrlUtils::addParameter($path, "version", WEB_VERSION);
            return parent::web()->formatStyle($path);
        }

        public function addScript($virtualPath, $placement = "head", $checkExistence = false) {
            $script = $this->formatScript($virtualPath, $checkExistence);
			if ($script != null) {
                parent::web()->addScript($script, $placement);
			}
        }

        public function addScriptInline($template, string $placement = "head", $key = null) {
            if ($key != null && in_array($key, $this->includedInlineScripts)) {
                return;
            } else {
                $this->includedInlineScripts[] = $key;
            }

            if (is_string($template)) {
                $content = $template;
            } else {
                $content = $template();
            }
            $script = parent::web()->formatScriptInline($content);
            parent::web()->addScript($script, $placement);
        }

		public function addStyle($virtualPath) {
			$style = $this->formatStyle($virtualPath);
			if ($style != null) {
				parent::web()->addStyle($style);
			}
		}

        public function addStyleInline(callable $template, $key = null) {
            if ($key != null && in_array($key, $this->includedInlineStyles)) {
                return;
            } else {
                $this->includedInlineStyles[] = $key;
            }

            $content = $template();
            $style = parent::web()->formatStyleInline($content);
            parent::web()->addStyle($style);
        }

        private $isjQueryAdded = false;

        public function addjQuery($version = "3.5.1") {
            if ($this->isjQueryAdded) {
                return;
            }

            $this->isjQueryAdded = true;
            if ($version == "1.4.2") {
                $this->addScript("~/js/jquery/jquery.js");
            } else if ($version == "3.5.1") {
                $this->addScript("~/js/jquery/jquery-3.5.1.min.js");
            }
        }

        /**
         *
         * 	Returns all javascripts and stylesheets required by cms.
         *
         * 	@param		useWindows				if true, includes scripts for windows
         *
         */
        public function getCmsResources($useWindows = false) {
            $return = '';

            if ($useWindows) {
                $return .= ''
                    . $this->formatStyle('~/css/editor.css')
                    . $this->formatStyle('~/css/edit-area.css')
                    . $this->formatStyle('~/css/window.css')
                    . $this->formatStyle('~/css/jquery-autocomplete.css')
                    . $this->formatStyle('~/css/jquery-wysiwyg.css')
                    . $this->formatStyle('~/css/demo_table.css')
                    . $this->formatScript('~/edit_area/edit_area_full.js')
                    . $this->formatScript('~/js/jquery/jquery.js')
                    . $this->formatScript('~/js/jquery/jquery-autocomplete-pack.js')
                    . $this->formatScript('~/js/jquery/jquery-blockui.js')
                    . $this->formatScript('~/js/jquery/jquery-dataTables-min.js')
                    . $this->formatScript('~/js/jquery/jquery-wysiwyg.js')
                    . $this->formatScript('~/js/cookies.js')
                    . $this->formatScript('~/js/functions.js')
                    . $this->formatScript('~/js/window.js')
                    . $this->formatScript('~/js/domready.js')
                    . $this->formatScript('~/js/rxmlhttp.js')
                    . $this->formatScript('~/js/links.js')
                    . $this->formatScript('~/js/processform.js')
                    . $this->formatScript('~/js/Closer.js')
                    . $this->formatScript('~/js/Confirm.js')
                    . $this->formatScript('~/js/Editor.js')
                    . $this->formatScript('~/js/FileName.js')
                    . $this->formatScript('~/js/CountDown.js')
                    . $this->formatScript('~/js/formFieldEffect.js')
                    . $this->formatScript('~/js/init.js')
                    . $this->formatScript('~/tiny-mce/tinymce.min.js')
                    . $this->formatScript('~/js/initTiny.js');
            } else {
                $return .= ''
                    . $this->formatStyle('~/css/cms_nowindows.css')
                    . $this->formatStyle('~/css/editor.css')
                    . $this->formatStyle('~/css/edit-area.css')
                    . $this->formatStyle('~/css/jquery-wysiwyg.css')
                    . $this->formatScript('~/edit_area/edit_area_full.js')
                    . $this->formatScript('~/js/jquery/jquery.js')
                    . $this->formatScript('~/js/jquery/jquery-wysiwyg.js')
                    . $this->formatScript('~/js/cookies.js')
                    . $this->formatScript('~/js/functions.js')
                    . $this->formatScript('~/js/domready.js')
                    . $this->formatScript('~/js/rxmlhttp.js')
                    . $this->formatScript('~/js/links.js')
                    . $this->formatScript('~/js/processform.js')
                    . $this->formatScript('~/js/Closer.js')
                    . $this->formatScript('~/js/Confirm.js')
                    . $this->formatScript('~/js/Editor.js')
                    . $this->formatScript('~/js/FileName.js')
                    . $this->formatScript('~/js/CountDown.js')
                    . $this->formatScript('~/js/formFieldEffect.js')
                    . $this->formatScript('~/js/init_nowindows.js')
                    . $this->formatScript('~/tiny-mce/tinymce.min.js')
                    . $this->formatScript('~/js/initTiny.js');
            }
            if (strpos($_SERVER['REQUEST_URI'], ".view") == -1) {
                $return = str_replace("~/", INSTANCE_URL, $return);
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

            $return .= $this->formatScript('~/js/domready.js');
            $return .= $this->formatScript('~/js/rxmlhttp.js');
            $return .= $this->formatScript('~/js/links.js');

            $content = file_get_contents("js/web/ajaxWebInit.js");
            $content = str_replace("{web-content}", $webContentRootElId, $content);
            $content = str_replace("{root-page}", $rootPageId, $content);
            $content = str_replace("{ajax-message}", $ajaxMessage, $content);

            $return .= '<script type="text/javascript">' . $content . '</script>';

            return $return;
        }

        public function addResourcesToPage($names, $type, $as = false) {
            global $phpObject;
            $return = '';

            if ($type != "js" && $type != "css") {
                $type = "js";
            }

            $namesAsArray = StringUtils::explode($names, ',');
            foreach ($namesAsArray as $name) {
                $path = $type == "js" ? "js/" . trim($name) . ".js" : "css/" . trim($name) . ".css";
                $filePath = "scripts/" . $path;
                if (file_exists($filePath)) {
                    $virtualPath = '~/' . $path;
                    if ($as == 'inline') {
                        $return .= ($type == "js") ? '<script type="text/javascript">' . "\n" . file_get_contents($path) . "\n" . '</script>' : '<style type="text/css">' . "\n" . file_get_contents($path) . "\n" . '</style>';
                    } else {
                        $return .= ($type == "js") ? $this->formatScript($virtualPath) : $this->formatStyle($virtualPath);
                    }
                }
            }

            return $return;
        }

        public function tinyMce($ids, $language = '', $jQuery = true, $isEditable = true) {
            if ($jQuery) {
                $this->addjQuery("1.4.2");
            }

            $this->addScript('~/tiny-mce/tinymce.min.js');
            $this->addScript('~/js/initTiny.js');

            $script = "";
            $ids = StringUtils::explode($ids, ',');
            if (count($ids) > 0) {
                foreach ($ids as $id) {
                    $arguments = '"' . $id . '"';
                    if ($language != '') {
                        $arguments .= ', "' . $language . '"';
                    } else {
                        $arguments .= ', null';
                    }

                    $isEditable = $isEditable == true ? "true" : "false";
                    $arguments .= ", $isEditable";

                    $script .= 'initTiny(' . $arguments . ');';
                }
            }

            $this->addScriptInline("(function() { $script })();", "tail");
        }

        public function select2($selector, $tags = false) {
            $this->addjQuery();
            $this->addScript('~/js/select2/select2.min.js');
            $this->addStyle('~/css/select2/select2.min.css');

            $options = "{";
            if ($tags) {
                $options .= "tags: true,";
            }
            $options .= "}";

            $this->addScriptInline("(function() { $('$selector').select2($options); })();", "tail");
        }

        public function bootstrapDatePicker($selector, $format = "", $language = "", $autoclose = false, $orientation = "") {
            $this->addScript("~/js/bootstrap-datepicker/bootstrap-datepicker.min.js");
            $this->addScript("~/js/bootstrap-datepicker/locales/bootstrap-datepicker.$language.min.js", "head", true);
            $this->addStyle('~/css/bootstrap-datepicker/bootstrap-datepicker.min.css');
            
            $options = [];
            if ($format != "") {
                $options[] = "format: '$format'";
            }
            if ($language != "") {
                $options[] = "language: '$language'";
            }
            if ($autoclose) {
                $options[] = "autoclose: true";
            }
            if ($orientation) {
                $options[] = "orientation: '$orientation'";
            }
            
            $optionsString = "";
            if (count($options) > 0) {
                $optionsString = "{" . implode(", ", $options) . "}";
            }

            $this->addScriptInline("(function() { $('$selector').datepicker($optionsString); })();", "tail");
        }

        public function ajax($selector, $parentPageId = false, $onLoading = false, $onCompleted = false, $onFailed = false, $varName = false, $jQuery = true, $modifyUrl = true) {
            if (parent::web()->isXmlTemplate()) {
                return;
            }

            if (!is_numeric($parentPageId)) {
                $parentPageId = 'null';
            }

            $init = '';
            if ($onLoading) {
                $init .= 'ajax.AddEventListener("loading", ' . $onLoading . '); ';
            }
            if ($onCompleted) {
                $init .= 'ajax.AddEventListener("completed", ' . $onCompleted . '); ';
            }
            if ($onFailed) {
                $init .= 'ajax.AddEventListener("failed", ' . $onFailed . '); ';
            }
            if ($varName) {
                $init .= 'window["' . $varName . '"] = ajax; ';
            }

            $params = [];
            $params[] = 'selector: "' . $selector . '"';
            $params[] = 'parentPageId: ' . $parentPageId;
            $params[] = 'modifyUrl: ' . ($modifyUrl ? "true" : "false");

            $init .= 'ajax.Initialize($(document.body)); ';
            
            if ($jQuery) {
                $this->addjQuery("1.4.2");
            }

            $this->addScript('~/js/ajax.js');
            $this->addScriptInline('(function() { var ajax = new Ajax({ ' . implode(', ', $params) . ' }); ' . $init . '})();', "tail");
        }

        public function dataDuplicators($jQuery = true) {
            if ($jQuery) {
                $this->addjQuery("1.4.2");
            }

            $script = '
$("[data-duplicator]").each(function(i, el) {
    var $el = $(el);
    var selector = $el.attr("data-duplicator");
    $el.click(function (e) {
        $duplicable = $(selector);
        $clone = $duplicable.clone();
        $clone.removeAttr("data-duplicable");
        $clone.appendTo($duplicable.parent());
        $clone.find("input[type=text], textarea").val("").first().focus();

        e.preventDefault();
    });
});';

            $this->addScriptInline("(function() { $script })();", "tail");
        }
    }

?>
