<?php

    require_once(APP_SCRIPTS_PHP_PATH . "classes/LocalizationBundle.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/ParsedTemplate.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/TemplateParserBase.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/TemplateAttributeCollection.class.php");

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
                // TODO: Remove as global attribute are deprecated.
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
                    $isFullTag = $attributes->HasBodyProvidingDecorators = $this->hasFullTagBodyProvider($attributes);
                }

                $functionName = null;
                if ($isFullTag) {
                    // TODO: To compile templates, we need to "soft evaluate" the and also generate code.
                    if ($object[0] == "php" && $object[1] == "using") {
                        $phpObject->register($attributes->Attributes["prefix"]["value"], $attributes->Attributes["class"]["value"]);
                    }

                    $template = $this->parseInternal($content, 'parse');
                    $content = "function () { return '". $template . "'; }";
                    
                    // TODO: To compile templates, we need to "soft evaluate" the and also generate code.
                    if ($object[0] == "php" && $object[1] == "using") {
                        $phpObject->unregister($attributes->Attributes["prefix"]["value"]);
                    }
                    
                    if ($phpObject->isFullTag($object[0], $object[1], $attributes)) {
                        $functionName = $phpObject->getFuncToFullTag($object[0], $object[1]);
                        if (!$phpObject->sortFullAttributes($object[0], $object[1], $attributes, $content)) {
                            return "";
                        }
                    } else if ($phpObject->isAnyFullTag($object[0], $object[1])) {
                        $functionName = $phpObject->getFuncToFullTag($object[0], $object[1]);
                        if (!$this->sortAnyTagAttributes($object[1], $attributes, $content)) {
                            return "";
                        }
                    }
                } else {
                    if ($phpObject->isTag($object[0], $object[1], $attributes)) {
                        $functionName = $phpObject->getFuncToTag($object[0], $object[1]);
                        if (!$phpObject->sortAttributes($object[0], $object[1], $attributes)) {
                            return "";
                        }
                    } else if ($phpObject->isAnyTag($object[0], $object[1])) {
                        $functionName = $phpObject->getFuncToTag($object[0], $object[1]);
                        if (!$this->sortAnyTagAttributes($object[1], $attributes)) {
                            return "";
                        }
                    }
                    
                    // TODO: To compile templates, we need to "soft evaluate" the and also generate code.
                    if ($object[0] == "php" && $object[1] == "register") {
                        $phpObject->register($attributes->Attributes["tagPrefix"]["value"], $attributes->Attributes["classPath"]["value"]);
                    } else if ($object[0] == "php" && $object[1] == "unregister") {
                        $phpObject->unregister($attributes->Attributes["tagPrefix"]["value"]);
                    }
                }

                if ($attributes->HasDecorators) {
                    if (!$this->parseDecorators($object[0], $object[1], $attributes)) {
                        return "";
                    }
                }

                if ($functionName != null) {
                    // We need to process php:register to know registered objects.
                    // TODO: To compile templates, we need to "soft evaluate" the and also generate code.
                    // if ($object[0] == 'php') {
                    //     eval('$return = ${$object[0]."Object"}->{$functionName}(' . $this->concatAttributesToString($attributes->Attributes) . ');');
                    //     return "";
                    // }
                    
                    return $this->generateFunctionOutput($object[0], $functionName, $attributes);
                }
            }
            
            return '<h4 class="error">Tag "' . $object[0] . ':' . $object[1] . '" is not registered!</h4>';
        }

        protected function parseDecorators(string $tagPrefix, string $tagName, TemplateAttributeCollection $tagAttributes) {
            global $phpObject;

            foreach ($tagAttributes->Decorators as $prefix => $attributes) {
                if ($phpObject->isRegistered($prefix)) {
                    if (!$phpObject->findDecoratorsForAttributes($prefix, $tagAttributes)) {
                        return false;
                    }
                } else {
                    return $phpObject->triggerUnregisteredPrefix($prefix);
                }
            }

            foreach ($tagAttributes->Decorators as $prefix => $decorators) {
                foreach ($decorators as $decorator) {
                    $attributes = new TemplateAttributeCollection();
                    $attributes->Attributes = $decorator["attributes"];

                    if (!$phpObject->sortDecoratorAttributes($prefix, $decorator["function"], $attributes, $tagPrefix, $tagName)) {
                        return false;
                    }

                    $call = $this->generateFunctionOutput($prefix, $decorator["function"], $attributes, false);
                    if (!$tagAttributes->HasAttributeModifyingDecorators && $decorator["providesFullTagBody"]) {
                        $tagAttributes->Attributes[DefaultPhp::$FullTagTemplateName] = array("value" => $call . "['" . DefaultPhp::$FullTagTemplateName . "']", "type" => "eval");
                    }
                }
            }

            return true;
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

                    $attributes = new TemplateAttributeCollection();
                    return $this->generateFunctionOutput($object[0], $functionName, $attributes, false);
                } else if($phpObject->isAnyProperty($object[0])) {
                    $functionName = 'getProperty';
                    
                    $attributes = new TemplateAttributeCollection();
                    $attributes->Attributes[] = array('value' => $object[1], 'type' => 'raw');
                    return $this->generateFunctionOutput($object[0], $functionName, $attributes, false);
                }
            }

            // #131 - Apostrophes are later stripped when binding to number attribute.
            // TODO: To compile templates, process these in a standard way.
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

        private function hasFullTagBodyProvider(TemplateAttributeCollection $attributes) {
            global $phpObject;

            foreach ($attributes->Decorators as $tagPrefix => $decorators) {
                if ($phpObject->isRegistered($tagPrefix)) {
                    if ($phpObject->hasFullTagBodyProvider($tagPrefix, array_keys($decorators))) {
                        return true;
                    }
                }
            }

            return false;
        }

        protected function sortAnyTagAttributes(string $tagName, TemplateAttributeCollection $attributes, string $content = null) {
            $params = array();
            foreach ($attributes->Attributes as $usedName => $usedValue) {
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

            $attributes->Attributes = $result;
            return true;
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
            $attributes = new TemplateAttributeCollection();

            preg_replace_callback($this->ATT_RE, array(&$this, 'parseatt'), $rawAttributes);

            foreach ($this->Attributes as $tmp) {
                $attributePrefix = null;
                [$attributeName, $attributeValue] = explode("=", $tmp);
                if (strlen($attributeName) > 0) {
                    $this->PropertyUse = 'get';
                    
                    $valueType = 'raw';
                    if (strlen($attributeValue) > 1) {
                        $attributeValue = substr($attributeValue, 1, strlen($attributeValue) - 2);
                        $evaluated = preg_replace_callback($this->ATT_PROPERTY_RE, array(&$this, 'parsecproperty'), $attributeValue);

                        if ($attributeValue != $evaluated) {
                            $attributeValue = $evaluated;
                            $valueType = 'eval';
                        }
                    } else {
                        $attributeValue = '';
                    }

                    // #61 - Any input double quotes will be escaped
                    // $attributeValue = str_replace("\"", "\\\"", $attributeValue);
                    $attributeValue = array('value' => $attributeValue, 'type' => $valueType);

                    $decorator = explode(":", $attributeName);
                    if (count($decorator) == 2) {
                        $attributePrefix = $decorator[0];
                        $attributeName = $decorator[1];

                        $attributes->Decorators[$attributePrefix][$attributeName] = $attributeValue;
                        $attributes->HasDecorators = true;
                    } else {
                        $attributes->Attributes[$attributeName] = $attributeValue;
                    }
                }
            }

            $globalResult = $this->tryProcessGlobalAttributes($attributes);
            if ($globalResult) {
                return false;
            }

            return $attributes;
        }

        // TODO: Remove as deprecated.
        // Returns <c>true</c> evaluation should be stopped; Otherwise <c>false</c>.
        protected function tryProcessGlobalAttributes(TemplateAttributeCollection $attributes) {
            foreach ($attributes->Attributes as $key => $att) {
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
                    unset($attributes->Attributes[$key]);
                } elseif ($key == 'security:requirePerm') {
                    global $loginObject;
                    $perm = $loginObject->getGroupPerm($att['value'], $loginObject->getMainGroupId(), false, 'false');
                    if ($perm['value'] != 'true') {
                        return true;
                    }
                    unset($attributes->Attributes[$key]);
                }
            }

            return false;
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

        protected function evalAttributesWithoutProcessingTag(TemplateAttributeCollection $attributes) {
            foreach ($attributes->Attributes as $key => $value) {
                if ($value["type"] == "eval") {
                    $eval = $value["value"] . ";";
                    eval($eval);
                }
            }
        }

        protected function generateFunctionOutput(string $tagPrefix, string $functionName, TemplateAttributeCollection $attributes, bool $isWrappedAsString = true) : string {
            $identifier = $this->generateRandomString();
            $identifier = 'template_' . $tagPrefix . '_' . $functionName . '_' . $identifier;
            $attributesString = $this->concatAttributesToString($attributes->Attributes);

            $targetObject = '$' . $tagPrefix . 'Object';
            $logObject = '$' . 'log' . 'Object';
            
            $this->Code->addMethod($identifier, "private");
            $this->Code->addTry();
            $this->Code->addLine("global $targetObject;");

            if ($attributes->HasDecorators && $attributes->HasAttributeModifyingDecorators) {
                foreach ($attributes->Decorators as $decorator) {
                    if ($decorator->ProvidesFullTagBody) {
                        $this->Code->addLine('$' . "parameters['" . DefaultPhp::$FullTagTemplateName . "'] = " . '$' . "this->" . $decorator->TemplateFunctionName . "()['" . DefaultPhp::$FullTagTemplateName . "'];");
                    }
                }
            }

            $this->Code->addLine("return " . $targetObject . "->" . $functionName . "(" . $attributesString . ");");
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
