<?php

    require_once("BaseTagLib.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/LocalizationBundle.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/ViewHelper.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/ViewMissingException.class.php");

    /**
     * 
     *  Class View.	     
     *      
     *  @author     Marek SMM
     *  @timestamp  2010-08-06
     * 
     */
    class View extends BaseTagLib {

        private $CurrentTemplateContent;
        private $CurrentTemplatePointer = 0;

        public function __construct() {
            $this->setLocalizationBundle("view");
        }

        private function getCurrentVirtualUrlWithoutExtension() {
            return '~/' . $_REQUEST['VIEW_PAGE_PATH'];
        }

        public function getCurrentVirtualUrl() {
            return $this->getCurrentVirtualUrlWithoutExtension() . ".view";
        }

        public function getVirtualPathKeys($virtualPath) {
            $withoutView = (strlen($virtualPath) - 5);
            if (strpos($virtualPath, ".view") == $withoutView) {
                $virtualPath = substr($virtualPath, 0, $withoutView);
            }

            $virtualKeys = str_replace("~", "view", $virtualPath);
            $keys = explode("/", $virtualKeys);
            $keys[count($keys) - 1] .= "." . ViewHelper::getViewContentIdentifier($virtualPath);
            return $keys;
        }

        /* ======================= TAGS ========================================= */

        public function processView() {
            if (array_key_exists('query-list', $_GET)) {
                parent::db()->getDataAccess()->saveQueries(true);
            }

            $this->Resources = array();
            $this->Content = array();
            $this->Log = '';
            $this->Title = '';
            $this->CurrentTemplateContent = array();
            $this->CurrentTemplatePointer = 0;

            $result = "";
            try {
                $virtualPath = $this->getCurrentVirtualUrlWithoutExtension();
                $keys = $this->getVirtualPathKeys($virtualPath);
                $template = $this->getParsedTemplate($keys);
                if ($template == null) {
                    $template = $this->parseTemplate($keys, ViewHelper::getViewContent($virtualPath));
                }

                $result = $template();
            } catch (Exception $e) {
                if (IS_DEVELOPMENT_MODE) {
                    return "<pre>" . $e->getMessage() . PHP_EOL . $e->getTraceAsString() . "</pre>";
                } else {
                    return parent::getError($e->getMessage());
                }
            }

            return $result;
        }

        public function useTemplate($template, $src) {
            $return = '';
            $this->CurrentTemplateContent[$this->CurrentTemplatePointer] = $template;
            $this->CurrentTemplatePointer++;

            $keys = $this->getVirtualPathKeys($src);
            $template = $this->getParsedTemplate($keys);
            if ($template == null) {
                $template = $this->parseTemplate($keys, ViewHelper::getViewContent($src));
            }
            
            $return = $template();
            return $return;
        }

        public function head($template) {
            $this->web()->appendToHead($template);
        }

        public function getContent() {
            $return = '';

            $this->CurrentTemplatePointer--;
            
            $return = $this->CurrentTemplateContent[$this->CurrentTemplatePointer]();
            return $return;
        }

        public function addHeader($name, $value) {
            return '';
        }

        public function addResource($type, $src) {
            switch ($type) {
                case "js":
                    $this->js()->addScript($src);
                    break;

                case "css":
                    $this->js()->addStyle($src);
                    break;
            }
        }

        public function setTitle($title) {
            $this->Title = $title;
        }

        public function showPanel($template, $id = false, $class = false, $attributes = []) {
            $attributes = $this->appendClass($attributes, $class);
            if (!empty($id)) {
                $attributes["id"] = $id;
            }

            $return = '<div' . $this->joinAttributes($attributes) . '>' . $template() . '</div>';

            return $return;
        }

		public function provideBodyByPath($src) {
			$keys = $this->getVirtualPathKeys($src);
            $template = $this->getParsedTemplate($keys);
            if ($template == null) {
                $template = $this->parseTemplate($keys, ViewHelper::getViewContent($src));
            }

			$parameters = [PhpRuntime::$FullTagTemplateName => $template];
			return $parameters;
		}
    }

?>
