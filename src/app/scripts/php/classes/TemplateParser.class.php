<?php

    require_once(APP_SCRIPTS_PHP_PATH . "classes/LocalizationBundle.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/ParsedTemplate.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/PropertyReference.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/TemplateAttributeCollection.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/TemplateCache.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/TemplateParserBase.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/TemplateParserException.class.php");

    class TemplateParser extends TemplateParserBase {

        protected $Code = null;
        protected $TemplateCache = null;
        protected $libraries;
        protected $libraryLoader;
        protected $defaultRegistrations;
        protected $defaultAttributes;
        protected $relativeClassPathPrefix;
        
        // Current custom tag attributes.
        protected $Attributes = array();

        // Regular expression for parsing custom tag.     
        protected $TAG_RE = '(<([a-zA-Z0-9-_]+:[a-zA-Z0-9-_]+)( )+((([a-zA-Z0-9-_]+[:]?[a-zA-Z0-9-_]*)="[^"]*"( )*)*)\/>)';

        // Regular expression for parsing attribute.
        protected $ATT_RE = '(([a-zA-Z0-9-_]+[:]?[a-zA-Z0-9-_]*)="([^"]*)")';

        // Regular expression for parsing property value. It requires exact match (no prefix or postfix text).
        protected $ATT_PROPERTY_RE = '(^([a-zA-Z0-9-_]+:[a-zA-Z0-9-_.]+)$)';

        // Regular expression for parsing full tag.     
        protected $FULL_TAG_RE = "#<([a-zA-Z0-9-_]+:[a-zA-Z0-9-_]+)((.*?)(?=\/>)\/>|([^>]*)>((?:[^<]|<(?!/?\\1[^>]*>)|(?R))+)</\\1>)#";

        public function __construct($defaultRegistrations, $defaultAttributes, $relativeClassPathPrefix = null) {
            $this->TemplateCache = new TemplateCache();
            $this->defaultRegistrations = $defaultRegistrations;
            $this->defaultAttributes = $defaultAttributes;
            $this->relativeClassPathPrefix = $relativeClassPathPrefix;
        }

        public function getCache() {
            return $this->TemplateCache;
        }

        public function parsePropertyExactly($value) {
            $this->libraries = new AutoLibraryCollection(file_get_contents(APP_SCRIPTS_PHP_PATH . 'autoregister.xml'), false);
            $this->libraryLoader = new LibraryLoader();

            $evaluated = preg_replace_callback($this->ATT_PROPERTY_RE, array(&$this, 'parsecproperty'), $value);
            $this->checkPregError("parsecproperty", $value);

            if ($evaluated == NULL) {
                return $value;
            }
            
            if ($evaluated != $value) {
                $prefix = $this->propertyFunctionsToGenerate[$evaluated]["prefix"];
                $get = $this->propertyFunctionsToGenerate[$evaluated]["get"];

                $eval = 'global $' . $prefix . 'Object; return $' . $prefix . 'Object->' . $get . '();';
                return eval($eval);
            }

            return $value;
        }

        public function run($keys) {
            if (!$this->TemplateCache->exists($keys)) {
                return null;
            }

            $this->TemplateCache->load($keys);
            $className = $this->getClassName($keys);

            $result = new $className();
            return $result;
        }

        /**
         *
         * 	Parse custom tags from Content and save result to Result
         *
         */
        public function parse($content, $keys) {
            $this->Code = new CodeWriter();
            $this->libraries = new AutoLibraryCollection(file_get_contents(APP_SCRIPTS_PHP_PATH . 'autoregister.xml'), false);
            $this->libraryLoader = new LibraryLoader($this->relativeClassPathPrefix);

            if ($this->defaultRegistrations) {
                foreach ($this->defaultRegistrations as $prefix => $xmlPath) {
                    $this->libraries->add($prefix, $xmlPath);
                }
            }

            return $this->parseInternal($content, 'compile', $keys);
        }

        private function getClassName(array $keys) {
            $className = "Template_" . implode("_", $keys);
            $className = str_replace("-", "", $className);
            $className = str_replace(".", "", $className);
            return $className;
        }

        private function parseInternal(string $content, string $mode, ?array $keys = null, bool $excapeText = true) {
            if ($mode == 'parse') {
                return $this->parseContentInternal($content, $excapeText);
            } else if($mode == 'compile') {
                $className = $this->getClassName($keys);

                $this->Code->addClass($className, "ParsedTemplate");
                
                $processed = $this->parseContentInternal($content, $excapeText);
                $this->Code->addMethod("evaluateInternal", "protected");
                $this->Code->addLine("return '". $processed . "';");
                $this->Code->closeBlock();
                $this->Code->closeBlock();

                $code = $this->Code->toString();
                $this->TemplateCache->set($keys, $code);
                
                return $this->run($keys);
            } else {
                throw new Exception("Invalid 'mode'.");
            }
        }

        private function parseContentInternal(string $content, bool $excapeText = true) {
            $this->startMeasure();
            
            $processed = "";
            if ($content != "") {
                if ($excapeText) {
                    $replaced = str_replace("'", "\'", $content);
                } else {
                    $replaced = $content;
                }

                $processed = preg_replace_callback($this->FULL_TAG_RE, array(&$this, 'parsefulltag'), $replaced);
                $this->checkPregError("parsefulltag", $replaced);
            }

            $this->stopMeasure($content);
            return $processed;
        }

        private function ensureAbsoluteClassPath($classPath) {
            if (StringUtils::startsWith($classPath, ".")) {
                if ($this->relativeClassPathPrefix) {
                    $classPath = $this->relativeClassPathPrefix . $classPath;
                } else {
                    throw new TemplateParserException("Missing classPath prefix for '$classPath'");
                }
            }

            return $classPath;
        }
        
        // Parses full tag
        // Output of this function can't contain ' (apostrophe), as the output is evaluated as PHP code wrapped in ' (apostrophe).
        private function parsefulltag($ctag) {
            $object = explode(":", $ctag[1]);

            $prevProperties = $this->propertyFunctionsToGenerate;
            $this->propertyFunctionsToGenerate = [];
            
            $isFullTag = count($ctag) != 4;
            $attributes = $this->tryParseAttributes($isFullTag ? $ctag[4] : $ctag[3]);
            
            $content = 0;
            if ($isFullTag) {
                $content = $ctag[5];
            }

            // Now we know the tag is syntactically valid and should be processed.
            if ($this->libraries->exists($object[0])) {
                $library = $this->libraries->get($object[0]);

                // Find which decorators are used.
                if ($attributes->HasDecorators) {
                    if (!$this->parseDecorators($attributes)) {
                        return "";
                    }
                }

                // Use decorators and content to determine whether the tags is self closing or full.
                if (!$isFullTag && $attributes->HasBodyProvidingDecorators) {
                    $isFullTag = true;
                }

                $functionName = null;

                $isRegisteredTag = $library->isTag($object[1]);
                $isRegisteredFullTag = $library->isFullTag($object[1]);
                if (!$isFullTag && !$isRegisteredTag && $isRegisteredFullTag) {
                    // Allow full tags to be used without body/template.
                    $isFullTag = true;
                    $content = "";
                }

                $hasBodyTemplate = false;
                $uniqueIdentifier = $this->generateRandomString();
                if ($isFullTag) {
                    if ($object[0] == "php" && $object[1] == "using") {
                        $attributes->Attributes["class"]["value"] = $this->ensureAbsoluteClassPath($attributes->Attributes["class"]["value"]);
                        $registrationClassPath = $attributes->Attributes["class"]["value"];
                        $registrationPrefix = $attributes->Attributes["prefix"]["value"];
                        $registrationXmlPath = $this->libraryLoader->getXmlPath($registrationClassPath);
                        $registrationLibrary = $this->libraries->add($registrationPrefix, $registrationXmlPath);
                        $this->ensureConstructorParameters($registrationLibrary, $attributes->Attributes, $registrationPrefix, $registrationClassPath, "param");
                    }

                    $template = $this->parseInternal($content, 'parse', null, false);
                    
                    if ($object[0] == "php" && $object[1] == "using") {
                        $this->libraries->remove($attributes->Attributes["prefix"]["value"]);
                    }
                    
                    $code = $this->Code;
                    if ($isRegisteredFullTag) {
                        $functionName = $library->getFuncToFullTag($object[1]);

                        $varConfig = $code->var("config");
                        $templateMethodName = 'template_' . $object[0] . '_' . $functionName . '_' . $uniqueIdentifier . '_body';
                        $code->addMethod($templateMethodName, "public", ["config"]);
                        $code->addLine("{$code->varThis()}->pushConfig($varConfig);");
                        $code->addLine("{$code->var("result")} = '". $template . "';");
                        $code->addLine("{$code->varThis()}->popConfig();");
                        $code->addLine("return {$code->var("result")};");
                        $code->closeBlock();
                        $content = "function(?ParsedTemplateConfig $varConfig = null) { return " . '$' . "this->$templateMethodName($varConfig); }";
                        $hasBodyTemplate = true;
                        
                        if (!$this->sortFullAttributes($object[0], $object[1], $attributes, $content, $uniqueIdentifier)) {
                            $this->propertyFunctionsToGenerate = $prevProperties;
                            return "";
                        }
                    } else if ($library->isAnyFullTag($object[1])) {
                        $functionName = $library->getFuncToFullTag($object[1]);
                        
                        $varConfig = $code->var("config");
                        $templateMethodName = 'template_' . $object[0] . '_' . $functionName . '_' . $uniqueIdentifier . '_body';
                        $code->addMethod($templateMethodName, "public", ["config"]);
                        $code->addLine("return '". $template . "';");
                        $code->closeBlock();
                        $content = "function(?ParsedTemplateConfig $varConfig = null) { return " . '$' . "this->$templateMethodName($varConfig); }";
                        $hasBodyTemplate = true;
                        
                        if (!$this->sortAnyTagAttributes($object[1], $attributes, $content)) {
                            $this->propertyFunctionsToGenerate = $prevProperties;
                            return "";
                        }
                    }
                } else {
                    if ($isRegisteredTag) {
                        $functionName = $library->getFuncToTag($object[1]);
                        if (!$this->sortAttributes($object[0], $object[1], $attributes, $uniqueIdentifier)) {
                            $this->propertyFunctionsToGenerate = $prevProperties;
                            return "";
                        }
                    } else if ($library->isAnyTag($object[1])) {
                        $functionName = $library->getFuncToTag($object[1]);
                        if (!$this->sortAnyTagAttributes($object[1], $attributes)) {
                            $this->propertyFunctionsToGenerate = $prevProperties;
                            return "";
                        }
                    }
                    
                    if ($object[0] == "php") {
                        if ($object[1] == "register") {
                            $attributes->Attributes["classPath"]["value"] = $this->ensureAbsoluteClassPath($attributes->Attributes["classPath"]["value"]);
                            $registrationClassPath = $attributes->Attributes["classPath"]["value"];
                            $registrationPrefix = $attributes->Attributes["tagPrefix"]["value"];
                            $registrationXmlPath = $this->libraryLoader->getXmlPath($registrationClassPath);
                            $registrationLibrary = $this->libraries->add($registrationPrefix, $registrationXmlPath);
                            $this->ensureConstructorParameters($registrationLibrary, $attributes->Attributes["param"]["value"], $registrationPrefix, $registrationClassPath);
                        } else if ($object[1] == "create" || $object[1] == "lazy") {
                            foreach ($attributes->Attributes["params"]["value"] as $registrationPrefix => &$registrationClassPath) {
                                if (strpos($registrationPrefix, "-") === false) {
                                    $registrationClassPath["value"] = $this->ensureAbsoluteClassPath($registrationClassPath["value"]);
                                    $registrationXmlPath = $this->libraryLoader->getXmlPath($registrationClassPath["value"]);
                                    $registrationLibrary = $this->libraries->add($registrationPrefix, $registrationXmlPath);
                                    $this->ensureConstructorParameters($registrationLibrary, $attributes->Attributes["params"]["value"], $registrationPrefix, $registrationClassPath["value"], $registrationPrefix);
                                }
                            }
                        } else if ($object[1] == "unregister") {
                            $this->libraries->remove($attributes->Attributes["tagPrefix"]["value"]);
                        } else if ($object[1] == "attribute") {
                            $this->defaultAttributes[$attributes->Attributes["prefix"]["value"]][$attributes->Attributes["tag"]["value"]][] = $attributes->Attributes["name"]["value"];
                        }
                    }
                }
                
                if ($attributes->HasDecorators) {
                    $this->generateDecorators($object[0], $object[1], $attributes);
                }
                
                if ($functionName != null) {
                    $this->generateAttributePropertyFunctions($attributes->Attributes);
                    $this->propertyFunctionsToGenerate = $prevProperties;

                    $indexOfNewLine = strpos($ctag[0], PHP_EOL);
                    if ($indexOfNewLine > 0) {
                        $sourceLine = substr($ctag[0], 0, $indexOfNewLine);
                    } else {
                        $sourceLine = $ctag[0];
                    }
                    $code = $this->Code->addLine("// " . $sourceLine);

                    return $this->generateFunctionOutput($uniqueIdentifier, $object[0], $object[1], $hasBodyTemplate, $functionName, $attributes);
                }
            }
            
            throw new TemplateParserException("Tag '$object[0]:$object[1]' is not registered!");
        }

        private function generateAttributePropertyFunctions($attributes) {
            foreach ($attributes as $attributeValue) {
                if ($attributeValue["content"] == "template" && array_key_exists($attributeValue["propertyIdentifier"], $this->propertyFunctionsToGenerate)) {
                    $property = $this->propertyFunctionsToGenerate[$attributeValue["propertyIdentifier"]];
                    $this->generatePropertyOutput($attributeValue["propertyIdentifier"], $property, $attributeValue);
                }

                if (is_array($attributeValue["value"])) {
                    $this->generateAttributePropertyFunctions($attributeValue["value"]);
                }
            }
        }

        protected function parseDecorators(TemplateAttributeCollection $tagAttributes) {
            foreach ($tagAttributes->Decorators as $prefix => $attributes) {
                $library = $this->libraries->get($prefix);
                if (!$library->findDecoratorsForAttributes($tagAttributes)) {
                    return false;
                }
            }

            return true;
        }

        protected function generateDecorators(string $tagPrefix, string $tagName, TemplateAttributeCollection $tagAttributes) {
            foreach ($tagAttributes->Decorators as $prefix => $decorators) {
                foreach ($decorators as $decorator) {
                    $identifier = $this->generateRandomString();

                    $attributes = new TemplateAttributeCollection();
                    $attributes->Attributes = $decorator["attributes"];

                    $this->sortDecoratorAttributes($prefix, $decorator["function"], $attributes, $tagPrefix, $tagName, $identifier);

                    $defaultReturnValue = "false";
                    if ($decorator["modifiesAttributes"]) {
                        $attributes->Attributes["tagPrefix"] = ["value" => "'$tagPrefix'", "type" => "eval"];
                        $attributes->Attributes["tagName"] = ["value" => "'$tagName'", "type" => "eval"];
                        $attributes->Attributes[PhpRuntime::$FullTagTemplateName] = ["value" => '$parameters', "type" => "eval"];
                        $attributes->FunctionParameters[] = "parameters";
                        $defaultReturnValue = '$parameters';
                    }

                    $this->generateAttributePropertyFunctions($attributes->Attributes);

                    $call = $this->generateFunctionOutput($identifier, $prefix, null, false, $decorator["function"], $attributes, false, $defaultReturnValue);
                    if (!$tagAttributes->HasAttributeModifyingDecorators && $decorator["providesFullTagBody"]) {
                        $tagAttributes->Attributes[PhpRuntime::$FullTagTemplateName] = array("value" => $call . "['" . PhpRuntime::$FullTagTemplateName . "']", "type" => "eval");
                    } else {
                        $tagAttributes->Decorators[$prefix][$decorator["function"]]["call"] = $call;
                    }
                }
            }
        }

        private $propertyFunctionsToGenerate = [];

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

            if ($this->libraries->exists($object[0])) {
                $library = $this->libraries->get($object[0]);
                if ($library->isProperty($object[1]) || $library->isAnyProperty()) {
                    $attributes = new TemplateAttributeCollection();
                    $identifier = $this->generateRandomString();
                    $property = [
                        "prefix" => $object[0],
                        "name" => $object[1],
                        "attributes" => $attributes,
                    ];

                    if ($library->isProperty($object[1])) {
                        $property["any"] = false;
                        $property["get"] = $library->getFuncToProperty($object[1], "get");
                        $property["set"] = $library->getFuncToProperty($object[1], "set");

                    } else if ($library->isAnyProperty()) {
                        $attributes->Attributes[] = array('value' => $object[1], 'type' => 'raw');

                        $property["any"] = true;
                        $property["get"] = "getProperty";
                        $property["set"] = "setProperty";
                    }

                    $this->propertyFunctionsToGenerate[$identifier] = $property;
                    // TODO We can't return an array here (because preg replace)
                    return $identifier;
                }
            }

            // #131 - Apostrophes are later stripped when binding to number attribute.
            return $cprop[0];
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

        private function ensureConstructorParameters(LibraryDefinition $library, $attributes, $prefix, $classPath, $extraAttributePrefix = null) {
            $xml = $library->getXml();
            if (!isset($xml->constructor)) {
                return;
            }

            foreach ($xml->constructor->attribute as $attribute) {
                if (isset($attribute->required)) {
                    $attributeName = (string)$attribute->name;
                    if ($extraAttributePrefix) {
                        $extraAttributePrefix .= "-";
                    }

                    if (!array_key_exists($extraAttributePrefix . $attributeName, $attributes)) {
                        throw new TemplateParserException("Missing required constructor parameter '$attributeName' on prefix registration '$prefix' for '$classPath'.");
                    }
                }
            }
        }

        protected function sortAnyTagAttributes(string $tagName, TemplateAttributeCollection $attributes, ?string $content = null) {
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
                $result[PhpRuntime::$FullTagTemplateName] = [
                    'value' => $content, 
                    'type' => 'eval'
                ];
            }

            $result[PhpRuntime::$ParamsName] = [
                "value" => $params, 
                "type" => "eval"
            ];

            $attributes->Attributes = $result;
            return true;
        }

        protected function tryEvaluateAttribute($name, $value, $isEmptyKeyMerged = false) {
            if (is_array($value)) {
                $emptyKeyValue = null;
                if ($isEmptyKeyMerged) {
                    $emptyKeyValue = $value[""];
                    unset($value[""]);
                }

                $result = "array(" . $this->concatAttributesToString($value, true) . ")";

                if ($isEmptyKeyMerged) {
                    $result = "array_merge(" . $this->serializeAttributeValue($name, $emptyKeyValue) . ", " . $result . ")";
                }

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
                [$attributeName, $attributeValue] = explode("=", $tmp, 2);
                if (strlen($attributeName) > 0) {
                    $valueType = 'raw';
                    $contentType = null;
                    $propertyIdentifier = null;
                    if (strlen($attributeValue) > 1) {
                        $attributeValue = substr($attributeValue, 1, strlen($attributeValue) - 2);
                        $isEscapedProperty = false;
                        if (StringUtils::startsWith($attributeValue, "\\")) {
                            $maybeProperty = substr($attributeValue, 1);

                            if (preg_match($this->ATT_PROPERTY_RE, $maybeProperty, $matches)) {
                                $attributeValue = $maybeProperty;
                                $isEscapedProperty = true;
                            }
                        }

                        if (!$isEscapedProperty) {
                            $evaluated = preg_replace_callback($this->ATT_PROPERTY_RE, array(&$this, 'parsecproperty'), $attributeValue);

                            if ($attributeValue != $evaluated) {
                                $attributeValue = '$this->' . 'property_' . $this->propertyFunctionsToGenerate[$evaluated]["prefix"] . '_' . $this->propertyFunctionsToGenerate[$evaluated]["get"] . '_' . $evaluated . "()";

                                $valueType = 'eval';
                                $contentType = 'template';
                                $propertyIdentifier = $evaluated;
                            }
                        }
                    } else {
                        $attributeValue = '';
                    }

                    // #61 - Any input double quotes will be escaped
                    // $attributeValue = str_replace("\"", "\\\"", $attributeValue);
                    $attributeValue = ['value' => $attributeValue, 'type' => $valueType];
                    if ($contentType != null) {
                        $attributeValue['content'] = $contentType;
                    }
                    if ($propertyIdentifier != null) {
                        $attributeValue['propertyIdentifier'] = $propertyIdentifier;
                    }

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

            return $attributes;
        }

        protected function serializeAttributeValue($name, $value) {
            if ($value['type'] == 'raw') {
                return "'" . $value['value'] . "'";
            } else if ($value['type'] == 'eval') {
                return $this->tryEvaluateAttribute($name, $value['value'], array_key_exists("mergeEmptyKey", $value) && $value["mergeEmptyKey"]);
            } else {
                echo '<pre>';
                print_r(debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT));
                echo '</pre>';
                die('Missing value type for attribute "' . $name . '".');
            }
        }

        protected function concatAttributesToString($attributes, $isItemNameIncluded = false, $isNewLineAfterAttribute = false) {
            $result = "";
            $i = 0;
            foreach ($attributes as $name => $value) {
                if ($isItemNameIncluded) {
                    $result .= "'" . $name . "' => ";
                }

                $result .= $this->serializeAttributeValue($name, $value);

                if ($i < (count($attributes) - 1)) {
                    $result .= ", ";

                    if ($isNewLineAfterAttribute) {
                        $result .= PHP_EOL;
                    }
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

        protected function generateFunctionOutput(string $identifier, string $tagPrefix, ?string $tagName, bool $hasBodyTemplate, string $functionName, TemplateAttributeCollection $attributes, bool $isWrappedAsString = true, $defaultReturnValue = "''") : string {
            $identifier = 'template_' . $tagPrefix . '_' . $functionName . '_' . $identifier;

            $targetObject = '$' . $tagPrefix . 'Object';
            
            $code = $this->Code;
            $code->addMethod($identifier, "private", $attributes->FunctionParameters);
            $code->addLine("$targetObject = {$code->var("this")}->autolib('$tagPrefix');");

            $attributesString = "";
            if ($attributes->HasAttributeModifyingDecorators) {
                $code->addLine('$'. "parameters = [");
                $code->addIndent();
                $attributesString = $this->concatAttributesToString($attributes->Attributes, true, true);
                $code->addLine($attributesString, true);
                $code->removeIndent();
                $code->addLine("];");

                $attributeNames = [];
                foreach (array_keys($attributes->Attributes) as $name) {
                    $attributeNames[] = '$parameters["' . $name . '"]';
                }
                $attributesString = implode(", ", $attributeNames);
            } else {
                $attributesString = $this->concatAttributesToString($attributes->Attributes);
            }

            if ($tagName != null) {
                $code->addLine("if ({$code->varThis()}->isTagProcessed('$tagPrefix', '$tagName')) {");
                $code->addIndent();
            }

            if ($attributes->HasDecorators) {
                foreach ($attributes->Decorators as $prefix => $decorators) {
                    foreach ($decorators as $decorator) {
                        if (strlen($decorator["call"]) == 0) {
                            continue;
                        }

                        $returnParametersName = '$parameters';
                        
                        if ($attributes->HasAttributeModifyingDecorators) {
                            if ($decorator["conditionsExecution"] && !$decorator["modifiesAttributes"] && !$decorator["providesFullTagBody"]) {
                                $code->addLine($returnParametersName . "['" . PhpRuntime::$DecoratorExecuteName . "'] = " . $decorator["call"] . "['" . PhpRuntime::$DecoratorExecuteName . "'];");
                                $code->addLine("if (" . $returnParametersName . "['" . PhpRuntime::$DecoratorExecuteName . "'] === true) {");
                                $code->addIndent();
                                continue;
                            }

                            if ($decorator["providesFullTagBody"] && !$decorator["modifiesAttributes"] && !$decorator["conditionsExecution"]) {
                                $code->addLine($returnParametersName. "['" . PhpRuntime::$FullTagTemplateName . "'] = " . $decorator["call"] . "['" . PhpRuntime::$FullTagTemplateName . "'];");
                                continue;
                            }
                            
                            if ($decorator["providesFullTagBody"] || $decorator["modifiesAttributes"] || $decorator["conditionsExecution"]) {
                                if ($decorator["providesFullTagBody"] && $decorator["conditionsExecution"] && !$decorator["modifiesAttributes"]) {
                                    $decorator["call"] = "array_merge(" . $returnParametersName . ", " . $decorator["call"] . ")";
                                }
                                
                                $code->addLine($returnParametersName . " = " . $decorator["call"] . ";");

                                if ($decorator["conditionsExecution"]) {
                                    $code->addLine("if (" . $returnParametersName . "['" . PhpRuntime::$DecoratorExecuteName . "'] === true) {");
                                    $code->addIndent();
                                }

                                continue;
                            }
                        }
                        
                        if ($decorator["conditionsExecution"]) {
                            $code->addLine("if (" . $decorator["call"] . "['" . PhpRuntime::$DecoratorExecuteName . "'] === true) {");
                            $code->addIndent();
                            continue;
                        }

                        $code->addLine($decorator["call"] . ";");
                    }
                }
            }

            $code->addLine("return " . $targetObject . "->" . $functionName . "(" . $attributesString . ");");

            if ($attributes->HasDecorators) {
                foreach ($attributes->Decorators as $prefix => $decorators) {
                    foreach ($decorators as $decorator) {
                        if ($decorator["conditionsExecution"]) {
                            $code->closeBlock();
                        }
                    }
                }
            }

            if ($tagName != null) {
                $code->closeBlock();
                if ($hasBodyTemplate || $attributes->HasTemplateAttribute()) {
                    $code->addLine("else {");
                    $code->addIndent();

                    // Call all properties in attributes.
                    foreach ($attributes->Attributes as $attribute) {
                        if ($attributes->IsTemplateAttribute($attribute)) {
                            if (is_array($attribute["value"])) {
                                foreach ($attribute["value"] as $value) {
                                    if ($attributes->IsTemplateAttribute($value)) {
                                        $code->addLine($value["value"] . ";");
                                    }
                                }
                            } else {
                                $code->addLine($attribute["value"] . ";");
                            }
                        }
                    }

                    // Call template/body function.
                    if ($hasBodyTemplate) {
                        if ($attributes->HasAttributeModifyingDecorators) {
                            $code->addLine($returnParametersName. "['" . PhpRuntime::$FullTagTemplateName . "']();");
                        } else {
                            $isContentProcessed = false;
                            if ($attributes->HasDecorators) {
                                foreach ($attributes->Decorators as $prefix => $decorators) {
                                    if ($decorator["providesFullTagBody"]) {
                                        $code->addLine($attributes->Attributes[PhpRuntime::$FullTagTemplateName]["value"] . "();");
                                        $isContentProcessed = true;
                                        break;
                                    }
                                }
                            }

                            if (!$isContentProcessed) {
                                $code->addLine('$'. "this->" . $identifier . "_body(null);");
                            }
                        }
                    }

                    $code->closeBlock();
                }
            }

            $code->closeBlock();

            $result = '$this->' . $identifier . "(" . implode(", ", array_map(function($parameter) { return '$' . $parameter; }, $attributes->FunctionParameters)) . ")";
            if ($isWrappedAsString) {
                $result = "' . " . $result . " . '";
            }

            return $result;
        }

        private function generatePropertyOutput($identifier, $property, $value) {
            $tagPrefix = $property["prefix"];
            $tagName = $property["name"];
            $functionName = $property["get"];
            $identifier = 'property_' . $tagPrefix . '_' . $functionName . '_' . $identifier;

            $targetObject = '$' . $tagPrefix . 'Object';
            $attributesString = $this->concatAttributesToString($property["attributes"]->Attributes);
            
            $code = $this->Code;

            $code->addMethod($identifier, "private", []);

            $code->addLine("if ({$code->varThis()}->isPropertyProcessed('$tagPrefix', '$tagName')) {");
            $code->addIndent();

            if (array_key_exists("preferPropertyReference", $value) && $value["preferPropertyReference"]) {
                $propertyName = $property["any"]
                    ? "'{$property["name"]}'"
                    : "null";

                $code->addLine("return {$code->varThis()}->getPropertyReference('$tagPrefix', '$tagName', function() { ");
                $code->addIndent();
                $code->addLine("$targetObject = {$code->varThis()}->autolib('$tagPrefix');");
                $code->addLine("return new PropertyReference($targetObject, '$functionName', '{$property["set"]}', {$propertyName});");
                $code->removeIndent();
                $code->addLine("});");
            } else {
                $code->addLine("$targetObject = {$code->varThis()}->autolib('$tagPrefix');");
                $code->addLine("return {$targetObject}->{$functionName}({$attributesString});");
            }

            $code->closeBlock();

            $code->addLine("return null;");
            $code->closeBlock();
        }

        private function sortAttributes(string $tagPrefix, string $tagName, TemplateAttributeCollection $attributes, string $uniqueIdentifier): bool {
            return $this->sortAttributesInternal("tag", $tagPrefix, $tagName, $attributes, $uniqueIdentifier);
        }

        private function sortFullAttributes(string $tagPrefix, string $tagName, TemplateAttributeCollection $attributes, string $content, string $uniqueIdentifier): bool {
            if (!$this->sortAttributesInternal("fulltag", $tagPrefix, $tagName, $attributes, $uniqueIdentifier)) {
                return false;
            }

            $attributes->Attributes = array_merge(array(PhpRuntime::$FullTagTemplateName => array('value' => $content, 'type' => 'eval')), $attributes->Attributes);
            return true;
        }

        private function sortDecoratorAttributes(string $decoratorPrefix, string $decoratorFunctionName, TemplateAttributeCollection $attributes, string $tagPrefix, string $tagName, string $uniqueIdentifier) {
            $xml = $this->libraries->get($decoratorPrefix)->getXml();
            foreach ($xml->decorator as $decorator) {
                if ($decorator->function == $decoratorFunctionName) {
                    return $this->sortAttributesForXmlElement($decorator, $attributes, "Decorator on '$tagPrefix:$tagName'", $uniqueIdentifier, null);
                }
            }
        }

        private function sortAttributesInternal(string $tagListName, string $tagPrefix, string $tagName, TemplateAttributeCollection $atts, string $uniqueIdentifier): bool {
            $xml = $this->libraries->get($tagPrefix)->getXml();
            foreach ($xml->{$tagListName} as $tag) {
                if ($tag->name == $tagName) {
                    return $this->sortAttributesForXmlElement($tag, $atts, $tagPrefix . ":" . $tagName, $uniqueIdentifier, function($att) use ($tagPrefix, $tagName) { return $this->getDefaultGlobalAttribute($tagPrefix, $tagName, $att); });
                }
            }
        }

        private function getDefaultGlobalAttribute(string $prefix, string $tag, SimpleXMLElement $attribute) {
            if (array_key_exists($prefix, $this->defaultAttributes)) {
                $prefixAttributes = $this->defaultAttributes[$prefix];
                if (array_key_exists($tag, $prefixAttributes) || ($isWildcard = array_key_exists("*", $prefixAttributes))) {
                    if ($isWildcard) {
                        // PhpRuntime counts on that tag will be '*', see `PhpRuntime->getDefaultAttributeValue`.
                        $tag = "*";
                    }

                    $tagAttributes = $prefixAttributes[$tag];
                    $attributeName = (string)$attribute->name;
                    if (in_array($attributeName, $tagAttributes)) {
                        return [
                            'value' => "{$this->Code->varThis()}->php()->getDefaultAttributeValue('$prefix', '$tag', '$attributeName')", 
                            'type' => "eval"
                        ];
                    }
                }
            }

            return false;
        }
        
        private function sortAttributesForXmlElement(SimpleXMLElement $tag, TemplateAttributeCollection $atts, string $nameForErrorReport, string $uniqueIdentifier, ?callable $defaultAttributeHandler) {
            $processedAtts = array();
            $return = [];

            if ($tag->identifiable) {
                $return[PhpRuntime::$IdentifiableName] = ['value' => $uniqueIdentifier, 'type' => "raw"];
            }

            for ($i = 0; $i < count($tag->attribute); $i ++) {
                $isProcessed = false;
                $att = $tag->attribute[$i];
                $attName = (string)$att->name;
                $hasDefault = isset($att->default);

                // Prefixed attributes.
                if (isset($att->prefix)) {
                    $attPrefix = "$attName-";
                    $attributeValue = array();
                    foreach ($atts->Attributes as $usedName => $usedValue) {
                        if (StringUtils::startsWith($usedName, $attPrefix)) {
                            $strippedName = substr($usedName, strlen($attPrefix));

                            $attribute = $atts->Attributes[$usedName];
                            $processValue = $this->processParsedAttributeValue($attribute, $att);
                            if ($processValue != null) {
                                $processedAtts[] = $usedName;
                                $attributeValue[$strippedName] = $processValue;
                                continue;
                            }
                        }
                    }

                    $return[$attName] = ['value' => $attributeValue, 'type' => "eval"];
                    $isProcessed = true;
                }
                
                // Used attributes source template.
                if (array_key_exists($attName, $atts->Attributes)) {
                    $attribute = $atts->Attributes[$attName];
                    $processValue = $this->processParsedAttributeValue($attribute, $att);
                    if ($processValue != null) {
                        $processedAtts[] = $attName;

                        if (isset($att->type) && ($att->type == "propertyReference" || $att->type["preferPropertyReference"])) {
                            $processValue["preferPropertyReference"] = true;
                        }

                        if (array_key_exists($attName, $return)) {
                            if (empty($return[$attName]["value"])) {
                                if ($att->prefix["default"] == "merge") {
                                    $return[$attName] = $processValue;
                                } else {
                                    $return[$attName]["value"][""] = $processValue;
                                }
                            } else {
                                $return[$attName]["mergeEmptyKey"] = $att->prefix["default"] == "merge" && $processValue["type"] == "eval";
                                $return[$attName]["value"][""] = $processValue;
                            }
                        } else {
                            $return[$attName] = $processValue;
                        }

                        $isProcessed = true;
                    }
                }

                // Global default attributes.
                if (!$isProcessed && $defaultAttributeHandler) {
                    $processValue = $defaultAttributeHandler($att);
                    if ($processValue != null) {
                        $return[$attName] = $processValue;
                        $isProcessed = true;
                    }
                }
                
                // Attribute is used in source template.
                if ($isProcessed) {
                    continue;
                }
                
                // Default values.
                if ($hasDefault) {
                    if ($att->default && $att->default["as"] == "unused") {
                        $attributeValue = "PhpRuntime::UnusedAttributeValue";
                    } else {
                        $defaultValue = (string)$att->default;
                        if ($att->type == 'string') {
                            $attributeValue = "'" . eval('return "' . $defaultValue . '";') . "'";
                        } else {
                            $attributeValue = eval('return '. $defaultValue . ';');
                        }
                    }
                    
                    $return[$attName] = array('value' => $attributeValue, 'type' => 'eval');
                } else if (isset($att->required) && !$atts->HasAttributeModifyingDecorators) {
                    throw new TemplateParserException("Missing required attribute '$att->name' on tag '$nameForErrorReport'.");
                } else {
                    $value = false;
                    if ($att->type == "string") {
                        $value = "";
                    } else if ($att->type == "number") { 
                        $value = 0;
                    }

                    $return[$attName] = array('value' => $value, 'type' => 'raw');
                }
            }
    
            // Any attributes
            if (isset($tag->anyAttribute)) {
                $params = array();
                foreach ($atts->Attributes as $usedName => $usedValue) {
                    if (!in_array($usedName, $processedAtts)) {
                        $processedAtts[] = $usedName;
                        $params[$usedName] = $usedValue;
                    }
                }

                $return[PhpRuntime::$ParamsName] = array('value' => $params, 'type' => 'eval');
            }

            if (count($processedAtts) == count($atts->Attributes)) {
                $atts->Attributes = $return;
                return true;
            } else {
                foreach ($atts->Attributes as $name => $value) {
                    if (!in_array($name, $processedAtts)) {
                        throw new TemplateParserException("Used undefined attribute! [$name] on [$nameForErrorReport]");
                    }
                }

                return false;
            }
        }

        private function processParsedAttributeValue(array $attribute, /* Attribute */ SimpleXMLElement $definition) {
            $attributeValue = $this->getConvertValue($attribute['value'], $definition);
            if ($attributeValue != null) {
                $attributeValueType = 'raw';
                if (((isset($definition->default) || isset($definition->type)) && $attributeValue['type'] == 'eval') || $attribute['type'] == 'eval') {
                    $attributeValueType = 'eval';
                }

                if (array_key_exists("content", $attributeValue) && $attributeValue["content"] == "template") {
                    if (isset($definition->type) && $definition->type["source"] == "constant") {
                        return null;
                    }
                }

                $attribute['value'] = $attributeValue['value'];
                $attribute['type'] = $attributeValueType;
                return $attribute;
            }

            return null;
        }
        
        protected function getConvertValue($val, $att) {
            $convert = isset($att->default);
            
            if (isset($att->type)) {
                if (StringUtils::startsWith($val, '$this->property_') && StringUtils::endsWith($val, '()')) {
                    return ['value' => $val, 'type' => 'eval', 'content' => 'template'];
                }

                switch ($att->type) {
                    case 'propertyReference':
                        // All properties catched few lines above, as they are not converted here.
                        return null;
                    case 'string':
                        return array('value' => $val, 'type' => 'raw');
                    case 'number':
                        $trimmed = trim($val, "'");
                        if (is_numeric($trimmed)) {
                            return array('value' => $trimmed, 'type' => 'eval');
                        } else {
                            return null;
                        }
                    case 'bool':
                        if ($val === 'true' || $val === '1') {
                            return array('value' => true, 'type' => 'eval');
                        } else if ($val === 'false' || $val === '0') {
                            return array('value' => false, 'type' => 'eval');
                        } else {
                            return null;
                        }
                }
            }

            if ($convert) {
                if ($val === 'true') {
                    return array('value' => true, 'type' => 'eval');
                }
                
                if ($val === 'false') {
                    return array('value' => false, 'type' => 'eval');
                }
            }
            
            return array('value' => $val, 'type' => 'raw');
        }
    }

?>
