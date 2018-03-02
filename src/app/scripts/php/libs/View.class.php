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

        private $BundleName = 'view';
        private $BundleLang = 'cs';
        private $Resources;
        private $Content;
        private $FullParser;
        private $Log;
        private $Title;
        private $CurrentTemplateContent;
        private $CurrentTemplatePointer;

        public function __construct() {
            global $webObject;
            parent :: setTagLibXml("xml/View.xml");

            if ($webObject->LanguageName != '') {
                $rb = new LocalizationBundle();
                if ($rb->testBundleExists($this->BundleName, $webObject->LanguageName)) {
                    $this->BundleLang = $webObject->LanguageName;
                }
            }
        }

        /* ======================= TAGS ========================================= */

        public function processView($path) {
            $this->Resources = array();
            $this->Content = array();
            $this->Log = '';
            $this->Title = '';
            $this->CurrentTemplateContent = array();
            $this->CurrentTemplatePointer = 0;

            try {
                $this->FullParser = new FullTagParser();
                $this->FullParser->setContent(ViewHelper :: getViewContent('~/' . $_REQUEST['WEB_PAGE_PATH']));
                $this->FullParser->startParsing();
            } catch (Exception $ex) {
                echo parent :: getError($ex->getMessage());
            }

            self :: flush($this->FullParser->getResult());
        }

        public function useTemplate($content, $src) {
            $return = '';
            $this->CurrentTemplateContent[$this->CurrentTemplatePointer] = $content;
            $this->CurrentTemplatePointer++;

            $parser = new FullTagParser();
            $parser->setContent(ViewHelper::getViewContent($src));
            $parser->startParsing();
            $return = $parser->getResult();

            return $return;
        }

        public function getContent() {
            $return = '';

            $this->CurrentTemplatePointer--;
            $parser = new FullTagParser();
            $parser->setContent($this->CurrentTemplateContent[$this->CurrentTemplatePointer]);
            $parser->startParsing();
            $return = $parser->getResult();

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

            $parser = new FullTagParser();
            $parser->setContent($content);
            $parser->startParsing();
            $return = $parser->getResult();

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
                foreach ($this->Resources['css'] as $res) {
                    $styles .= '<link rel="stylesheet" href="' . ViewHelper :: resolveUrl($res) . '" type="text/css" />';
                }
                $scripts = '';
                foreach ($this->Resources['js'] as $res) {
                    echo $res;
                    $scripts .= '<script type="text/javascript" src="' . ViewHelper :: resolveUrl($res) . '"></script>';
                }

                $content = ViewHelper :: resolveUrl($content);

                $diacont = "";
                if (array_key_exists('mem-stats', $_GET)) {
                    $diacont = $webObject->Diagnostics->printMemoryStats();
                }
                if (array_key_exists('duration-stats', $_GET)) {
                    $diacont .= $webObject->Diagnostics->printDuration();
                }
                if (array_key_exists('query-stats', $_GET)) {
                    $diacont .= ''
                    . '<div style="border: 2px solid #666666; margin: 10px; padding: 10px; background: #eeeeee;">'
                        . '<div style="color: red; font-weight: bold;">Database queries:</div>'
                        . '<div>' . parent::db()->getQueriesPerRequest() . '</div>'
                    . '</div>';
                }
                if(strlen($webObject->PageLog) != 0) {
                    $diacont .= ''
                    . '<div style="border: 2px solid #666666; margin: 10px; padding: 10px; background: #eeeeee;">'
                        . '<div style="color: red; font-weight: bold;">Page Log:</div>'
                        . '<div>' . $webObject->PageLog . '</div>'
                    . '</div>';
                }

                $return = '' .
                        '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' .
                        '<html xmlns="http://www.w3.org/1999/xhtml">' .
                        '<head>' .
                        '<meta http-equiv="content-type" content="text/html; charset=utf-8" />' .
                        '<meta name="description" content="' . $this->Title . '" />' .
                        '<meta name="robots" content="all, index, follow" />' .
                        '<meta name="author" content="Marek Fišera" />' .
                        '<title>' . $this->Title . '</title>' .
                        $styles .
                        '</head>' .
                        '<body>' . $content . $scripts . $diacont . '</body>' .
                        '</html>';
            }

            $return = preg_replace_callback('(<web:frame( title="([^"]*)")*( open="(true|false)")*>(((\s*)|(.*))*)</web:frame>)', array(
                        & $this,
                        'parsepostframes'
                            ), $return);
            self :: tryToComprimeContent($return);
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
                exit ();
            } else {
                echo $return;
                exit ();
            }
        }

    }

?>
