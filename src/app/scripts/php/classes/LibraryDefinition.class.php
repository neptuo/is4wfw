<?php

    require_once("TemplateAttributeCollection.class.php");
    require_once("LibraryLoader.class.php");

    class LibraryDefinition {
        private $prefix;
        private $className;
        private $xmlPath;
        private $xml;

        public function __construct($prefix, $xmlPath) {
            $this->prefix = $prefix;
            $this->xmlPath = $xmlPath;
            $this->xml = new SimpleXMLElement(file_get_contents($xmlPath));
        }
        
        public function getXmlPath() {
            return $this->xmlPath;
        }

        public function getXml() {
            return $this->xml;
        }

        public function triggerFail($message, $errorType = E_USER_WARNING) {
            trigger_error($message, $errorType);
            return false;
        }

        public function getClassName() {
            if (!$this->className) {
                $this->className = basename($this->xmlPath, ".xml");
                if ($this->xml->namespace) {
                    $this->className = $this->xml->namespace . "\\" . $this->className;
                }
            }

            return $this->className;
        }

        public function isDisposable() {
            return isset($this->xml->disposable);
        }

        // ------- TAG --------------------------------------------------------

        public function isTag($tagName) {
            foreach ($this->xml->tag as $tag) {
                if ($tag->name == $tagName) {
                    return true;
                }
            }

            return false;
        }

        public function isAnyTag($tagName) {
            if (isset($this->xml->anyTag)) {
                foreach ($this->xml->tag as $tag) {
                    if ($tag->name == $tagName) {
                        return false;
                    }
                }

                return true;
            }

            return false;
        }

        public function getFuncToTag(string $tagName) : string {
            foreach ($this->xml->tag as $tag) {
                if ($tag->name == $tagName) {
                    return (string)$tag->function;
                }
            }
            
            if (isset($this->xml->anyTag)) {
                return $this->xml->anyTag->function;
            }

            $this->triggerFail("Unnable to find tag [".$tagName."] in lib [".$this->prefix."]");
        }

        // ------- FULL TAG ---------------------------------------------------

        public function isFullTag($tagName) {
            foreach ($this->xml->fulltag as $tag) {
                if ($tag->name == $tagName) {
                    return true;
                }
            }

            return false;
        }

        public function isAnyFullTag($tagName) {
            if (isset($this->xml->anyFulltag)) {
                foreach ($this->xml->fulltag as $tag) {
                    if ($tag->name == $tagName) {
                        return false;
                    }
                }

                return true;
            }

            return false;
        }

        public function getFuncToFullTag(string $tagName) : ?string {
            foreach ($this->xml->fulltag as $tag) {
                if ($tag->name == $tagName) {
                    return (string)$tag->function;
                }
            }
            
            if (isset($this->xml->anyFulltag)) {
                return $this->xml->anyFulltag->function;
            }

            $this->triggerFail("Unnable to find tag [".$tagName."] in lib [".$this->prefix."]");
            return null;
        }

        // ------- PROPERTY ---------------------------------------------------

        public function isProperty($propName) {
            foreach ($this->xml->property as $prop) {
                if ($prop->name == $propName) {
                    return true;
                }
            }

            return false;
        }

        public function isAnyProperty() {
            return isset($this->xml->anyProperty);
        }

        public function getFuncToProperty(string $propName, string $use) : string {
            foreach ($this->xml->property as $prop) {
                if ($prop->name == $propName) {
                    if (strtolower($use) == 'set') {
                        return (string)$prop->setFunction;
                    } elseif (strtolower($use) == 'get') {
                        return (string)$prop->getFunction;
                    } else {
                        return false;                        
                    }
                }
            }

            return $this->triggerFail("Unnable to find property [".$propName."] in lib [".$this->prefix."]");
        }

        // ------- DECORATOR --------------------------------------------------

        public function findDecoratorsForAttributes(TemplateAttributeCollection $tagAttributes) {
            $decorators = [];
            $attributeNames = array_keys($tagAttributes->Decorators[$this->prefix]);

            foreach ($this->xml->decorator as $decorator) {
                foreach ($decorator->attribute as $attribute) {
                    if (in_array($attribute->name, $attributeNames)) {
                        if (!array_key_exists($attribute->name, $decorators)) {
                            $modifiesAttributes = isset($decorator->features->modifiesAttributes);
                            $conditionsExecution = isset($decorator->features->conditionsExecution);
                            $providesFullTagBody = isset($decorator->features->providesFullTagBody);

                            // If the decorator return 2 values, we wrap it in an array.
                            if ($modifiesAttributes || $conditionsExecution && $providesFullTagBody) {
                                $tagAttributes->HasAttributeModifyingDecorators = true;
                            }

                            if ($conditionsExecution) {
                                $tagAttributes->HasConditionalDecorators = true;
                            }

                            if ($providesFullTagBody) {
                                $tagAttributes->HasBodyProvidingDecorators = true;
                            }

                            $functionName = (string)$decorator->function;
                            if (!array_key_exists($functionName, $decorators)) {
                                $decorators[$functionName] = [
                                    "function" => $functionName,
                                    "attributes" => [(string)$attribute->name => $tagAttributes->Decorators[$this->prefix][(string)$attribute->name]],
                                    "modifiesAttributes" => $modifiesAttributes,
                                    "conditionsExecution" => $conditionsExecution,
                                    "providesFullTagBody" => $providesFullTagBody,
                                ];
                            } else {
                                $decorators[$functionName]["attributes"][(string)$attribute->name] = $tagAttributes->Decorators[$this->prefix][(string)$attribute->name];
                            }
                        } else {
                            $decorators[$decorator->function]["attributes"][(string)$attribute->name] = $tagAttributes->Decorators[$this->prefix][(string)$attribute->name];
                        }

                        
                        unset($attributeNames[array_search((string)$attribute->name, $attributeNames)]);
                    }
                }
            }

            if (count($attributeNames) > 0) {
                for ($i = 0; $i < count($attributeNames); $i++) { 
                    $attributeNames[$i] = $this->prefix . ":" . $attributeNames[$i];
                }

                return $this->triggerFail("Unnable to find decorator for attributes " . implode(", ", $attributeNames) . ".");
            }

            $tagAttributes->Decorators[$this->prefix] = $decorators;
            return true;
        }
    }

    class LibraryCollection {
        private $storage = [];

        public function add(string $prefix, string $xmlPath): LibraryDefinition {
            return $this->storage[$prefix] = new LibraryDefinition($prefix, $xmlPath);
        }

        public function remove(string $prefix) {
            unset($this->storage[$prefix]);
        }

        public function exists(string $prefix): bool {
            return array_key_exists($prefix, $this->storage);
        }

        public function find(string $prefix): ?LibraryDefinition {
            if ($this->exists($prefix)) {
                return $this->storage[$prefix];
            }

            return null;
        }

        public function get(string $prefix): LibraryDefinition {
            $lib = $this->find($prefix);
            if ($lib) {
                return $lib;
            }

            throw new Exception("Prefix '$prefix' not registered.");
        }

        public function getPrefixes(): array {
            return array_keys($this->storage);
        }
    }

    class AutoLibraryCollection extends LibraryCollection {
        private $isReadOnly = false;

        public function __construct($xmlPath, $lock = true) {
            $loader = new LibraryLoader();

            $xml = new SimpleXMLElement($xmlPath);
            foreach ($xml->reg as $reg) {
                $attrs = $reg->attributes();
                $this->add((string)$attrs['prefix'], $loader->getXmlPath((string)$attrs['class']));
            }

            $this->isReadOnly = $lock;
        }

        private function ensureWriteable() {
            if ($this->isReadOnly) {
                throw new Exception("AutoLibraryCollection is readonly.");
            }
        }

        public function add(string $prefix, string $xmlPath): LibraryDefinition {
            $this->ensureWriteable();
            return parent::add($prefix, $xmlPath);
        }
        
        public function remove(string $prefix) {
            $this->ensureWriteable();
            parent::remove($prefix);
        }
    }

?>