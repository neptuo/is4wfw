<?php

    require_once("BaseTagLib.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/LocalizationBundle.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/FullTagParser.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/ViewHelper.class.php");

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
            self::setTagLibXml("View.xml");
            self::setLocalizationBundle("view");
        }

        private function getCurrentVirtualUrlWithoutExtension() {
            return '~/' . $_REQUEST['WEB_PAGE_PATH'];
        }

        public function getCurrentVirtualUrl() {
            return self::getCurrentVirtualUrlWithoutExtension() . ".view";
        }

        /* ======================= TAGS ========================================= */

        public function processView($path) {
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
                $result = parent::parseContent(ViewHelper::getViewContent(self::getCurrentVirtualUrlWithoutExtension()));
            } catch (Exception $ex) {
                echo parent :: getError($ex->getMessage());
            }

            self :: flush($result);
        }

        public function useTemplate($content, $src) {
            $return = '';
            $this->CurrentTemplateContent[$this->CurrentTemplatePointer] = $content;
            $this->CurrentTemplatePointer++;
            
            $return = parent::parseContent(ViewHelper::getViewContent($src));
            return $return;
        }

        public function head($content) {
            $this->Header .= parent::parseContent($content);
        }

        public function getContent() {
            $return = '';

            $this->CurrentTemplatePointer--;
            
            $return = parent::parseContent($this->CurrentTemplateContent[$this->CurrentTemplatePointer]);
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

        public function showPanel($content, $id = false, $class = false) {
            $return = '';

            $return = parent::parseContent($content);

            $att = '';
            if ($id != '') {
                $att .= ' id="' . $id . '"';
            }
            if ($class != '') {
                $att .= ' class="' . $class . '"';
            }
            $return = '<div' . $att . '>' . $return . '</div>';

            return $return;
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
