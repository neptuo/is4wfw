<?php

    require_once(APP_SCRIPTS_PHP_PATH . "classes/LocalizationBundle.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/ParsedTemplate.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/TemplateParserBase.class.php");

    class TemplateParser extends TemplateParserBase {

        protected $Code = null;
        
        // List of custom tags to parse [[prefix => name]]
        protected $TagsToParse = array();

        // Current custom tag attributes.
        protected $Attributes = array();

        // Regular expression for parsing custom tag.     
        protected $TAG_RE = '(<([a-zA-Z0-9-_]+:[a-zA-Z0-9-_]+)( )+((([a-zA-Z0-9-_]+[:]?[a-zA-Z0-9-_]*)="[^"]*"( )*)*)\/>)';

        // Regular expression for parsing attribute.
        protected $ATT_RE = '(([a-zA-Z0-9-_]+[:]?[a-zA-Z0-9-_]*)="([^"]*)")';

        // Regular expression for parsing property value. It requires exact match (no prefix or postfix text).
        protected $ATT_PROPERTY_RE = '(^([a-zA-Z0-9-_]+:[a-zA-Z0-9-_.]+)$)';
        protected $PropertyUse = '';

        // Regular expression for parsing full tag.     
        protected $FULL_TAG_RE = "#<([a-zA-Z0-9-_]+:[a-zA-Z0-9-_]+)((.*?)(?=\/>)\/>|([^>]*)>((?:[^<]|<(?!/?\\1[^>]*>)|(?R))+)</\\1>)#";

        public function setTagsToParse($tags) {
            $this->TagsToParse = $tags;
        }
        
        public function parsePropertyExactly($value) {
            $this->PropertyUse = 'get';
            
            $result = preg_replace_callback($this->ATT_PROPERTY_RE, array(&$this, 'parsecproperty'), $value);
            $this->checkPregError("parsecproperty", $value);

            if ($result == NULL) {
                return $value;
            }
            
            $result = eval("return ". $result . ";");
            return $result;
        }

        /**
         *
         * 	Parse custom tags from Content and save result to Result
         *
         */
        public function parse($content) {
            $this->Code = new CodeWriter();
            return $this->parseInternal($content, 'compile');
        }

        protected function isSkippedTag($ctag) {
            if ($this->TagsToParse != array()) {
                $skip = true;
                foreach ($this->TagsToParse as $tag) {
                    if($ctag[1] == $tag) {
                        $skip = false;
                        break;
                    }
                }
                
                if ($skip) {
                    return $ctag[0];
                }
            }
            
            return false;
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
        
        // Parses full tag
        // Output of this function can't contain ' (apostrophe), as the output is evaluated as PHP code wrapped in ' (apostrophe).
        private function parsefulltag($ctag) {
            global $phpObject;

            $object = explode(":", $ctag[1]);
            
            $skipped = $this->isSkippedTag($ctag);
            
            $isFullTag = count($ctag) != 4;
            $attributes = $this->tryParseAttributes($isFullTag ? $ctag[4] : $ctag[3]);
            if ($attributes === FALSE) {
                return '';
            }
            
            $content = 0;
            if ($isFullTag) {
                $content = $ctag[5];
            }

            if ($skipped) {
                $this->evalAttributesWithoutProcessingTag($attributes);

                if ($isFullTag) {
                    $parser = new TemplateParser();
                    $parser->setTagsToParse($this->TagsToParse);
                    $parser->parse($content);
                }
                return '';
            }

            // Now we know the tag is syntactically valid and should be processed.
            if ($phpObject->isRegistered($object[0])) {
                // Use decorators and content to determine whether the tags is self closing or full.
                if (!$isFullTag) {
                    $isFullTag = $this->hasFullTagBodyProvider($attributes);
                }

                if ($isFullTag) {
                    $template = $this->parseInternal($content, 'parse');
                    $content = "function () { return '". $template . "'; }";

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
                } else {
                    $functionName = false;

                    if ($phpObject->isTag($object[0], $object[1], $attributes)) {
                        $attributes = $phpObject->sortAttributes($object[0], $object[1], $attributes);
                        $functionName = $phpObject->getFuncToTag($object[0], $object[1]);
                    } else if ($phpObject->isAnyTag($object[0], $object[1])) {
                        $functionName = $phpObject->getFuncToTag($object[0], $object[1]);
                        $attributes = $this->sortAnyTagAttributes($object[1], $attributes);
                    }

                    if ($functionName && ($attributes !== false)) {
                        // We need to process php:register to know registered objects.
                        if ($object[0] == 'php') {
                            eval('$return =  ${$object[0]."Object"}->{$functionName}(' . $this->concatAttributesToString($attributes) . ');');
                        }
                        
                        return $this->generateFunctionOutput($object[0], $functionName, $attributes);
                    }
                }
            }
            
            return '<h4 class="error">This tag "' . $object[1] . '" is not registered! [' . $object[0] . ']</h4>';
        }

        /**
         *
         *  Function parses custom property, call right function & return content.
         *  
         *  @param  cprop  custom property as string
         *  @return return of custom property function     
         *
         */
        protected function parsecproperty($cprop) {
            $object = explode(":", $cprop[1]);
            $this->Attributes = array();

            global $phpObject;
            if ($phpObject->isRegistered($object[0])) {
                if ($phpObject->isProperty($object[0], $object[1])) {
                    $functionName = $phpObject->getFuncToProperty($object[0], $object[1], $this->PropertyUse);

                    return $this->generateFunctionOutput($object[0], $functionName, array(), false);
                } else if($phpObject->isAnyProperty($object[0])) {
                    $functionName = 'getProperty';
                    return $this->generateFunctionOutput($object[0], $functionName, array(array('value' => $object[1], 'type' => 'raw')), false);
                }
            }

            // #131 - Apostrophes are later stripped when binding to number attribute.
            if ($object[0] == 'query' && strlen($object[1]) > 0){
                return "'" . addcslashes($_GET[$object[1]], "'") . "'";
            } elseif($object[0] == 'post' && strlen($object[1]) > 0){
                return "'" . addcslashes($_POST[$object[1]], "'") . "'";
            } else {
                return "'" . addcslashes($cprop[0], "'") . "'";
            }
        }
        
        /**
         *
         *  Parse all attributes to array.
         *
         *  @param  att string with attributes
         *  @return array of attributes
         *
         */
        protected function parseatt($att) {
            $this->Attributes[] = $att[0];
        }

        private function hasFullTagBodyProvider($attributes) {
            global $phpObject;

            $decorators = [];
            foreach ($attributes as $name => $value) {
                $object = explode(":", $name);
                if (count($object) == 2) {
                    $decorators[$object[0]][] = $object[1];
                }
            }

            foreach ($decorators as $tagPrefix => $attributes) {
                if ($phpObject->isRegistered($tagPrefix)) {
                    if ($phpObject->hasFullTagBodyProvider($tagPrefix, $attributes)) {
                        return true;
                    }
                }
            }

            return false;
        }

        protected function sortAnyTagAttributes($tagName, $attributes, $content = null) {
            $params = array();
            foreach ($attributes as $usedName => $usedValue) {
                $params[$usedName] = $usedValue;
            }

            $result = [];
            $result["tagName"] = [
                "value" => $tagName,
                "type" => "raw"
            ];

            if ($content != null) {
                $result[DefaultPhp::$FullTagTemplateName] = [
                    'value' => $content, 
                    'type' => 'raw'
                ];
            }

            $result[DefaultPhp::$ParamsName] = [
                "value" => $params, 
                "type" => "eval"
            ];

            return $result;
        }

        protected function tryEvaluateAttribute($value) {
            if (is_array($value)) {
                $result = "array(" . $this->concatAttributesToString($value, true) . ")";
                return $result;
            } else if($value === false) {
                return 'false';
            } else if($value === true) {
                return 'true';
            }

            return $value;
        }

        protected function tryParseAttributes($rawAttributes) {
            $this->Attributes = array();
            $attributes = array();

            preg_replace_callback($this->ATT_RE, array(&$this, 'parseatt'), $rawAttributes);

            foreach ($this->Attributes as $tmp) {
                $att = explode("=", $tmp);
                if (strlen($att[0]) > 0) {
                    $this->PropertyUse = 'get';
                    
                    $valueType = 'raw';
                    if (strlen($att[1]) > 1) {
                        $att[1] = substr($att[1], 1, strlen($att[1]) - 2);
                        $evaluated = preg_replace_callback($this->ATT_PROPERTY_RE, array(&$this, 'parsecproperty'), $att[1]);

                        if ($att[1] != $evaluated) {
                            $att[1] = $evaluated;
                            $valueType = 'eval';
                        }
                    } else {
                        $att[1] = '';
                    }

                    // #61 - Any input double quotes will be escaped
                    // $att[1] = str_replace("\"", "\\\"", $att[1]);
                    $attributes[$att[0]] = array('value' => $att[1], 'type' => $valueType);
                }
            }

            $globalResult = $this->tryProcessGlobalAttributes($attributes);
            if ($globalResult === TRUE) {
                return FALSE;
            } else {
                $attributes = $globalResult;
            }

            return $attributes;
        }

        // Returns <c>true</c> evaluation should be stopped; Otherwise <c>false</c>.
        protected function tryProcessGlobalAttributes($attributes) {
            foreach ($attributes as $key => $att) {
                if ($key == 'security:requireGroup') {
                    global $loginObject;
                    $ok = false;
                    foreach ($loginObject->getGroups() as $group) {
                        if ($group['name'] == $att['value']) {
                            $ok = true;
                            break;
                        }
                    }
                    if (!$ok) {
                        return true;
                    }
                    unset($attributes[$key]);
                } elseif ($key == 'security:requirePerm') {
                    global $loginObject;
                    $perm = $loginObject->getGroupPerm($att['value'], $loginObject->getMainGroupId(), false, 'false');
                    if ($perm['value'] != 'true') {
                        return true;
                    }
                    unset($attributes[$key]);
                }
            }

            return $attributes;
        }

        protected function concatAttributesToString($attributes, $isItemNameIncluded = false) {
            $result = "";
            $i = 0;
            foreach ($attributes as $name => $value) {
                if ($isItemNameIncluded) {
                    $result .= "'" . $name . "' => ";
                }

                if ($value['type'] == 'raw') {
                    $result .= "'" . $value['value'] . "'";
                } else if($value['type'] == 'eval') {
                    $result .= $this->tryEvaluateAttribute($value['value']);
                } else {
                    echo '<pre>';
                    print_r(debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT));
                    echo '</pre>';
                    die('Missing value type for attribute "' . $name . '".');
                }

                if ($i < (count($attributes) - 1)) {
                    $result .= ", ";
                }
                $i++;
            }

            return $result;
        }

        protected function evalAttributesWithoutProcessingTag($attributes) {
            foreach ($attributes as $key => $value) {
                if ($value["type"] == "eval") {
                    $eval = $value["value"] . ";";
                    eval($eval);
                }
            }
        }

        protected function generateFunctionOutput($tagPrefix, $functionName, $attributes, $isWrappedAsString = true) {
            $identifier = $this->generateRandomString();

            if (is_array($attributes)) {
                $attributes = $this->concatAttributesToString($attributes);
            }

            $identifier = 'template_' . $tagPrefix . '_' . $functionName . '_' . $identifier;

            $targetObject = '$' . $tagPrefix . 'Object';
            $logObject = '$' . 'log' . 'Object';
            
            $this->Code->addMethod($identifier, "private");
            $this->Code->addTry();
            $this->Code->addLine("global $targetObject;");
            $this->Code->addLine("return " . $targetObject . "->" . $functionName . "(" . $attributes . ");");
            $this->Code->addCatch(["Exception", "e"]);
            $this->Code->addLine("global $logObject;");
            $this->Code->addLine($logObject . "->exception(" . '$e' . ");");
            $this->Code->addLine("return '';");
            $this->Code->closeBlock();
            $this->Code->closeBlock();

            $result = '$this->' . $identifier . "()";
            if ($isWrappedAsString) {
                $result = "' . " . $result . " . '";
            }

            return $result;
        }
    }

?>
