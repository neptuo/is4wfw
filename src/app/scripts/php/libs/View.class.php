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

        private $Resources;
        private $Content;
        private $Log;
        private $Title;
        private $CurrentTemplateContent;
        private $CurrentTemplatePointer;
        private $Header = "";

        public function __construct() {
            $this->setLocalizationBundle("view");
        }

        private function getCurrentVirtualUrlWithoutExtension() {
            return '~/' . $_REQUEST['WEB_PAGE_PATH'];
        }

        public function getCurrentVirtualUrl() {
            return $this->getCurrentVirtualUrlWithoutExtension() . ".view";
        }

        private function getVirtualPathKeys($virtualPath) {
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
            } catch (Exception $ex) {
                echo parent::getError($ex->getMessage());
            }

            $this->flush($result);
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
            $this->Header .= $template();
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
            if (!in_array($src, $this->Resources[$type])) {
                $this->Resources[$type][] = $src;
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

        /* ============================= FUNCTIONS =========================================== */

        private function flush($content) {
            global $webObject;
            
            if (strtolower($_REQUEST['__TEMPLATE']) == 'xml') {
                $styles = '';
                foreach ($this->Resources['css'] as $res) {
                    $styles .= '<rssmm:link-ref>' . ViewHelper :: resolveUrl($res) . '</rssmm:link-ref>';
                }
                $scripts = '';
                foreach ($this->Resources['js'] as $res) {
                    $scripts .= '<rssmm:script-ref>' . ViewHelper :: resolveUrl($res) . '</rssmm:script-ref>';
                }

                $return = '' .
                        '<rssmm:response>' .
                        ((strlen($this->Log) != 0) ? '' .
                                '<rssmm:log>' .
                                $this->Log .
                                '</rssmm:log>' : '') .
                        '<rssmm:head>' .
                        '<rssmm:title>' . $this->Title . '</rssmm:title>' .
                        '<rssmm:styles>' . $styles . '</rssmm:styles>' .
                        '<rssmm:scripts>' . $scripts . '</rssmm:scripts>' .
                        '</rssmm:head>' .
                        '<rssmm:content>' . $content . '</rssmm:content>' .
                        '</rssmm:response>';
            } else {
                $styles = '';
                $styles .= ViewHelper::resolveUrl(parent::web()->getPageStyles());
                foreach ($this->Resources['css'] as $res) {
                    $styles .= '<link rel="stylesheet" href="' . ViewHelper :: resolveUrl($res) . '" type="text/css" />';
                }

                $scripts = '';
                $scripts .= ViewHelper::resolveUrl(parent::web()->getPageHeadScripts());
                $scripts .= ViewHelper::resolveUrl(parent::web()->getPageTailScripts());
                foreach ($this->Resources['js'] as $res) {
                    echo $res;
                    $scripts .= '<script type="text/javascript" src="' . ViewHelper :: resolveUrl($res) . '"></script>';
                }


                $content = ViewHelper::resolveUrl($content);

                $diacont = "";
                if (array_key_exists('mem-stats', $_GET)) {
                    $diacont = $webObject->Diagnostics->printMemoryStats();
                }
                if (array_key_exists('duration-stats', $_GET)) {
                    $diacont .= $webObject->Diagnostics->printDuration();
                }
                if (array_key_exists('query-stats', $_GET)) {
                    $diacont .= parent::debugFrame('Database queries', parent::db()->getQueriesPerRequest());
                }
                if (array_key_exists('query-list', $_GET)) {
                    foreach (parent::db()->getDataAccess()->getQueries() as $key => $query) {
                        $diacont .= parent::debugFrame('Query ' . $key, $query, 'code');
                    }
                }
                if (strlen($this->PageLog) != 0) {
                    $diacont .= parent::debugFrame('Page Log', $webObject->PageLog);
                }

                $return = '' .
                '<!DOCTYPE html>' .
                '<html>' .
                    '<head>' .
                        '<meta http-equiv="content-type" content="text/html; charset=utf-8" />' .
                        '<meta name="description" content="' . $this->Title . '" />' .
                        '<meta name="robots" content="all, index, follow" />' .
                        '<meta name="author" content="Marek Fišera" />' .
                        '<title>' . $this->Title . '</title>' .
                        $this->Header .
                        $styles .
                    '</head>' .
                    '<body>' . $content . $scripts . $diacont . '</body>' .
                '</html>';
            }

            $return = preg_replace_callback(
                '(<web:frame( title="([^"]*)")*( open="(true|false)")*>(((\s*)|(.*))*)</web:frame>)', 
                array(&$this, 'parsepostframes'), 
                $return
            );
            $this->tryToComprimeContent($return);
        }

        private function tryToComprimeContent($content) {
            $acceptEnc = $_SERVER['HTTP_ACCEPT_ENCODING'];
            if (headers_sent ()) {
                $encoding = false;
            } elseif (strpos($acceptEnc, 'x-gzip') !== false) {
                $encoding = 'x-gzip';
            } elseif (strpos($acceptEnc, 'gzip') !== false) {
                $encoding = 'gzip';
            } else {
                $encoding = false;
            }

            $return = $content;

            if ($encoding) {
                header('Content-Encoding: ' . $encoding);
                print ("\x1f\x8b\x08\x00\x00\x00\x00\x00");
                $size = strlen($return);
                $return = gzcompress($return, 9);
                $return = substr($return, 0, $size);
                print ($return);
                parent::close();
            } else {
                echo $return;
                parent::close();
            }
        }

    }

?>
