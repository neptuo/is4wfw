<?php

    require_once(APP_SCRIPTS_PHP_PATH . "classes/CustomTagParser.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/LocalizationBundle.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/ParsedTemplate.class.php");

    class FullTagParser extends CustomTagParser {

        /**
         *
         *  Regular expression for parsing full tag.     
         *
         */
        protected $FULL_TAG_RE = "#<([a-zA-Z0-9-_]+:[a-zA-Z0-9-_]+)((.*?)(?=\/>)\/>|([^>]*)>((?:[^<]|<(?!/?\\1[^>]*>)|(?R))+)</\\1>)#";

        
        /**
         *
         * 	Parses full tag
         * 
         *  Output of this function can't contain ' (apostrophe), as the output is evaluated as PHP code wrapped in ' (apostrophe).
         *
         */
        private function parsefulltag($ctag) {
            global $phpObject;

            // Self closing tag.
            if (count($ctag) == 4) {
                return parent::parsectag($ctag);
            }

            $object = explode(":", $ctag[1]);
            $content = $ctag[5];

            $skipped = $this->isSkippedTag($ctag);

            $attributes = $this->tryProcessAttributes($ctag[4]);
            if ($attributes === FALSE) {
                return '';
            }

            if ($skipped) {
                $this->evalAttributesWithoutProcessingTag($attributes);

                // Parse $ctag[5].
                $parser = new FullTagParser();
                $parser->setTagsToParse($this->TagsToParse);
                $parser->parse($content);
                return '';
            }

            $template = $this->parseInternal($content, 'parse');
            $content = "function () { return '". $template . "'; }";

            if ($phpObject->isRegistered($object[0])) {
                if ($phpObject->isFullTag($object[0], $object[1], $attributes)) {
                    $attributes = $phpObject->sortFullAttributes($object[0], $object[1], $attributes, $content);
                    if ($attributes === false) {
                        return "";
                    }
                    
                    $functionName = $phpObject->getFuncToFullTag($object[0], $object[1]);
                } else if ($phpObject->isAnyFullTag($object[0], $object[1])) {
                    $functionName = $phpObject->getFuncToFullTag($object[0], $object[1]);
                    $attributes = $this->sortAnyTagAttributes($object[1], $attributes, $content);
                }

                if ($functionName) {
                    return $this->generateFunctionOutput($object[0], $functionName, $attributes);
                }
            }
            
            return '<h4 class="error">This tag "' . $object[1] . '" is not registered! [' . $object[0] . ']</h4>';
        }

        /**
         *
         * 	Parse custom tags from Content and save result to Result
         *
         */
        public function parse($content) {
            parent::setUseCaching(false);

            $this->Code = new CodeWriter();
            return $this->parseInternal($content, 'compile');
        }

        private function parseInternal($content, $mode) {
            if ($mode == 'parse') {
                return $this->parseContentInternal($content);
            } else if($mode == 'compile') {
                $className = "Template_" . $this->generateRandomString();

                $this->Code->addClass($className, "ParsedTemplate");
                
                $processed = $this->parseContentInternal($content);
                $this->Code->addMethod("evaluate");
                $this->Code->addLine("return '". $processed . "';");
                $this->Code->closeBlock();
                $this->Code->closeBlock();

                $code = $this->Code->toString();
                eval($code);
                
                $result = new $className();
                return $result;
            } else {
                throw new Exception("Invalid 'mode'.");
            }
        }

        private function parseContentInternal($content) {
            $this->startMeasure();
            
            $processed = "";
            if ($content != "") {
                $replaced = str_replace("'", "\\'", $content);
                $processed = preg_replace_callback($this->FULL_TAG_RE, array(&$this, 'parsefulltag'), $replaced);
            }

            $this->stopMeasure($content);
            return $processed;
        }
    }

?>
