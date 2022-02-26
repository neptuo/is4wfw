<?php

    require_once("BaseTagLib.class.php");
    require_once("Log.class.php");
    require_once("Database.class.php");
    require_once("Login.class.php");
    require_once("System.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/LibraryDefinition.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/LibraryLoader.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/TemplateAttributeCollection.class.php");

    /**
     *
     *    PhpRuntime class is for registrating & unregistrating tag-libs.
     *    Default object.            
     *    
     *    @objectname phpObject
     *    
     *    @author         Marek SMM
     *    @timestamp    2012-01-17
     *
     */                
    class PhpRuntime extends BaseTagLib {
        
        public static $ParamsName = 'params';
        public static $IdentifiableName = 'identifiable';
        public static $FullTagTemplateName = 'full:content';
        public static $DecoratorExecuteName = 'decorator:execute';
    
        private $defaultRegistrations = [
            "php" => "php.libs.PhpRuntime", 
            "web" => "php.libs.Web", 
            "log" => "php.libs.Log", 
            "db" => "php.libs.Database", 
            "login" => "php.libs.Login", 
            "sys" => "php.libs.System"
        ];

        public function getDefaultRegistrations() {
            return $this->defaultRegistrations;
        }

        public function getCurrentRegistrations() {
            $result = [];
            foreach ($this->libraries->getPrefixes() as $prefix) {
                $result[$prefix] = $this->libraries->get($prefix)->getXmlPath();
            }

            foreach ($this->autoRegister as $prefix => $definition) {
                $xmlPath = $this->libraryLoader->getXmlPath($definition["class"]);
                $result[$prefix] = $xmlPath;
            }

            return $result;
        }

        private $disposables = [];
        
        /**
         *
         *    Array with count of instacence of each tag lib.
         *
         */                                     
        private $instanceCounter = [
            "php.libs.PhpRuntime" => 1, 
            "php.libs.Web" => 1, 
            "php.libs.Log" => 1, 
            "php.libs.Database" => 1,
            "php.libs.Login" => 1, 
            "php.libs.System" => 1
        ];

        private $libraries;
        private $libraryLoader;
        private $autoRegister = [];

        private $defaultGlobalAttributes = [];
        private $defaultGlobalAttributeNames = [];
                                                            
        /**
         *
         *    Creates other default objects.
         *
         */                                                
        public function __construct() {
            $GLOBALS['logObject'] = new Log();
            $GLOBALS['dbObject'] = new Database();
            $GLOBALS['loginObject'] = new Login();
            $GLOBALS['sysObject'] = new System();

            $this->libraryLoader = new LibraryLoader();
            $this->libraries = new LibraryCollection();
            foreach ($this->defaultRegistrations as $prefix => $classPath) {
                $this->libraries->add($prefix, $this->libraryLoader->getXmlPath($classPath));
            }
        }
    
        private $XmlStorage = array();

        private function getXml($path) {
            if (!array_key_exists($path, $this->XmlStorage)) {
                $this->XmlStorage[$path] = new SimpleXMLElement(file_get_contents($path));
            }

            return $this->XmlStorage[$path];
        }

        private function addInstanceToCounter($classPath) {
            if (array_key_exists($classPath, $this->instanceCounter)) {
                $this->instanceCounter[$classPath] ++;
            } else {
                $this->instanceCounter[$classPath] = 1;
            }
        }

        /**
         *
         *    Registers tag library.
         *
         */                                                
        public function register($tagPrefix, $classPath, $params = []) {
            if (!$this->libraries->exists($tagPrefix)) {
                $codePath = $this->libraryLoader->getCodePath($classPath);
                if (is_file($codePath)) {
                    require_once($codePath);
                    
                    $xmlPath = $this->libraryLoader->getXmlPath($classPath);
                    $library = $this->libraries->add($tagPrefix, $xmlPath);
                    if ($this->isCountOfInstances($classPath, $library)) {
                        $className = $library->getClassName($classPath);
                        $GLOBALS[$tagPrefix . "Object"] = new $className($tagPrefix, $params);
                        $this->addInstanceToCounter($classPath);

                        if ($library->isDisposable()) {
                            $this->disposables[] = $tagPrefix;
                        }
                    } else {
                        $this->libraries->remove($tagPrefix);
                        return $this->getError('Too much instances of tag lib! [' . $classPath . ']');
                    }
                } else {
                    return $this->getError('This class does not exist "' . $codePath . '".');
                }
            } else {
                return $this->getError('This tag prefix already used! [' . $tagPrefix . ']');
            }
            
            return "";
        }

        public function create($attributes) {
            foreach ($this->groupAttributesByPrefix($attributes) as $prefix => $values) {
                $classPath = $values[""];
                if (empty($classPath)) {
                    throw new Exception("Missing class to instantiate for prefix '$prefix'.");
                }

                unset($values[""]);

                $this->register($prefix, $classPath, $values);
            }
        }

        protected function groupAttributesByPrefix($attributes) {
            $result = array();
            foreach ($attributes as $key => $value) {
                $keyParts = explode("-", $key, 2);
                if (count($keyParts) == 2) {
                    $result[$keyParts[0]][$keyParts[1]] = $value;
                } else {
                    $result[$keyParts[0]][""] = $value;
                }
            }

            return $result;
        }
    
        /**
         *
         *    Unregisters tag library.
         *
         */ 
        public function unregister($tagPrefix) {
            if ($this->libraries->exists($tagPrefix) && !array_key_exists($tagPrefix, $this->defaultRegistrations)) {
                $object = ${$tagPrefix."Object"};
                if (array_key_exists($tagPrefix, $this->disposables)) {
                    $object->dispose();
                    unset($this->disposables[$tagPrefix]);
                }

                $this->libraries->remove($tagPrefix);
                $this->instanceCounter[$tagPrefix] --;
                unset($object);
            }
            
            return "";
        }
        
        /**
         *
         *    Check if passed tagPrefix is checked.
         *    
         *    @param    tagPrefix string for check
         *    @return true if tagPrefix is registered, false other wise                             
         *
         */                                     
        public function isRegistered($tagPrefix) {
            if ($this->libraries->exists($tagPrefix)) {
                return true;
            } else {
                return $this->autoRegisterPrefix($tagPrefix);
            }
        }
        
        public function autoRegisterPrefix($prefix) {
            if (array_key_exists($prefix, $this->autoRegister)) {
                ["class" => $classPath, "params" => $params] = $this->autoRegister[$prefix];
                $this->register($prefix, $classPath, $params);
                return true;
            }

            $xml = $this->getXml(APP_SCRIPTS_PHP_PATH . 'autoregister.xml');
            foreach ($xml->reg as $reg) {
                $attrs = $reg->attributes();
                if ($attrs['prefix'] == $prefix) {
                    $params = [];

                    foreach ($attrs as $name => $value) {
                        if (StringUtils::startsWith($name, "param-")) {
                            $key = substr($name, 6);
                            $params[$key] = (string)$value;
                        }
                    }

                    $this->register($prefix, (string)$attrs['class'], $params);
                    return true;
                }
            }
            
            return false;
        }

        public function lazyTag($attributes) {
            foreach ($this->groupAttributesByPrefix($attributes) as $prefix => $values) {
                $classPath = $values[""];
                if (empty($classPath)) {
                    throw new Exception("Missing class to instantiate for prefix '$prefix'.");
                }

                unset($values[""]);

                $this->lazy($prefix, $classPath, $values);
            }
        }

        public function lazy(string $prefix, string $classPath, array $params = []) {
            $this->autoRegister[$prefix] = ["class" => $classPath, "params" => $params];
        }
        
        /**
         *
         *    Check if passed values are valid property of library.
         *    
         *    @param    tagPrefix library object name
         *    @param    propName name of required property
         *    @return true is passed values are valid tagPrefix and property name, false other wise
         *
         */                                     
        public function isProperty($tagPrefix, $propName) {
            return $this->libraries->get($tagPrefix)->isProperty($propName);
        }

        // Vrací true, pokud daná knihovna umožňuje <anyProperty />.
        public function isAnyProperty($tagPrefix) {
            return $this->libraries->get($tagPrefix)->isAnyProperty();
        }
                                           
        private function isCountOfInstances($classPath, LibraryDefinition $library) {
            $count = 0;
            if (array_key_exists($classPath, $this->instanceCounter)) {
                $count = $this->instanceCounter[$classPath];
            }
            
            $xml = $library->getXml();
            if (!isset($xml->count) || (string)$xml->count == "*") {
                return true;
            } else if ((int)$xml->count > $count) {
                return true;
            } else {
                return false;
            }
        }
        
        /**
         *
         *    Return function name to passed tag name.
         *    
         *    @return function name                    
         *
         */                                     
        public function getFuncToProperty(string $tagPrefix, string $propName, string $use) : string {
            return $this->libraries->get($tagPrefix)->getFuncToProperty($propName, $use);
        }
        
        public function usingObject($content, $prefix, $class, $params = []) {
            $return = '';
            $this->register($prefix, $class, $params);

            $return = $content();
            
            $this->unregister($prefix);
            return $return;
        }

        public function dispose() {
            foreach ($this->disposables as $tagPrefix) {
                global ${$tagPrefix."Object"};
                ${$tagPrefix."Object"}->dispose();
            }
        }

        public function triggerUnregisteredPrefix($tagPrefix) {
            return $this->triggerFail("Tag prefix isn't registered! [".$tagPrefix."]");
        }

        public function triggerFail($message, $errorType = E_USER_WARNING) {
            trigger_error($message, $errorType);
            echo $this->getError($message);
            return false;
        }
        
        public function cache($cache) {

        }

        public function getNull() {
            return null;
        }

        public function getTrue() {
            return true;
        }

        public function getFalse() {
            return false;
        }

        public function getDefaultGlobalAttributeNames() {
            return $this->defaultGlobalAttributes;
        }

        public function setDefaultGlobalAttribute($prefix, $tag, $name, $value) {
            $this->defaultGlobalAttributes[$prefix][$tag][$name] = $value;
            $this->defaultGlobalAttributes[$prefix][$tag][] = $name;
        }

        public function getDefaultAttributeValue($prefix, $tag, $name) {
            // Tag can also be a '*'. Caused by TemplateParser.
            return $this->defaultGlobalAttributes[$prefix][$tag][$name];
        }
    }

?>
