<?php

    require_once("BaseTagLib.class.php");
    require_once("ErrorHandler.class.php");
    require_once("Log.class.php");
    require_once("Database.class.php");
    require_once("Login.class.php");
    require_once("System.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/LibraryDefinition.class.php");
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
        public static $FullTagTemplateName = 'full:content';
        public static $DecoratorExecuteName = 'decorator:execute';
    
        private $defaultRegistrations = [
            "php" => "php.libs.PhpRuntime", 
            "web" => "php.libs.Web", 
            "error" => "php.libs.ErrorHandler", 
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
            "php.libs.ErrorHandler" => 1, 
            "php.libs.Log" => 1, 
            "php.libs.Database" => 1,
            "php.libs.Login" => 1, 
            "php.libs.System" => 1
        ];

        private $libraries;
                                                            
        /**
         *
         *    Creates other default objects.
         *
         */                                                
        public function __construct() {
            $GLOBALS['errorObject'] = new ErrorHandler();
            $GLOBALS['logObject'] = new Log();
            $GLOBALS['dbObject'] = new Database();
            $GLOBALS['loginObject'] = new Login();
            $GLOBALS['sysObject'] = new System();

            $this->libraries = new LibraryCollection();
            foreach ($this->defaultRegistrations as $prefix => $classPath) {
                $this->libraries->add($prefix, APP_SCRIPTS_PATH . $this->parseClassPath($classPath) . ".xml");
            }
            
            set_error_handler("ErrorHandler::errorHandler");
        }
    
        private $XmlStorage = array();

        private function getXml($path) {
            if (!array_key_exists($path, $this->XmlStorage)) {
                $this->XmlStorage[$path] = new SimpleXMLElement(file_get_contents($path));
            }

            return $this->XmlStorage[$path];
        }

        /**
         *
         *    Registers tag library.
         *
         */                                                
        public function register($tagPrefix, $classPath, $params = []) {
            $classJPath = $classPath;
            if (!$this->libraries->exists($tagPrefix)) {
                if ($this->checkIfClassExists($tagPrefix, $classPath)) {
                    $classArray = StringUtils::explode($classPath, '.');
                    $classDir = "";
                    for ($i = 0; $i < count($classArray) - 1; $i ++) {
                        $classDir .= $classArray[$i];
                        if($i < (count($classArray) - 2)) {
                            $classDir .= ".";
                        }
                    }

                    $className = $classArray[count($classArray) - 1];
                    $classPath = $this->parseClassPath($classPath);

                    require_once(APP_SCRIPTS_PATH . $classPath . ".class.php");
                    
                    if ($this->isCountOfInstances($className, $classDir, $classPath)) {
                        $GLOBALS[$tagPrefix . "Object"] = new $className($tagPrefix, $params);
                        if(array_key_exists($classJPath, $this->instanceCounter)) {
                            $this->instanceCounter[$classJPath] ++;
                        } else {
                            $this->instanceCounter[$classJPath] = 1;
                        }

                        $library = $this->libraries->add($tagPrefix, APP_SCRIPTS_PATH . $classPath . ".xml");
                        if ($library->isDisposable()) {
                            $this->disposables[] = $tagPrefix;
                        }
                    } else {
                        return $this->getError('Too much instances of tag lib! [' . $classJPath . ']');
                    }
                } else {
                    return $this->getError('This class does not exist "' . $classPath . '".');
                }
            } else {
                return $this->getError('This tag prefix already used! [' . $tagPrefix . ']');
            }
            
            return "";
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
         *    Check if passed string is valid class path.
         *    
         *    @param    classPath path to required class
         *    @return true of class existes, false other wise
         *
         */                                     
        private function checkIfClassExists($tagPrefix, $classPath) {
            $path = APP_SCRIPTS_PATH . $this->parseClassPath($classPath) . ".class.php";
            if (is_file($path)) {
                return true;
            } else {
                return false;
            }
        }
        
        /**
         *
         *    Parse string to array separated by '/'
         *    
         *    @return array with directory names                    
         *
         */                                     
        private function parseClassPath($classPath) {
            $classPath = str_replace(".", "/", $classPath);
            return $classPath;
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
            $xml = $this->getXml(APP_SCRIPTS_PHP_PATH . 'autoregister.xml');
            foreach ($xml->reg as $reg) {
                $attrs = $reg->attributes();
                if ($attrs['prefix'] == $prefix) {
                    $this->register($prefix, (string)$attrs['class']);
                    return true;
                }
            }
            
            return false;
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
        
        /**
         *
         *    Compare actual count and max count for passed class.
         *    Return true if max count is "*" or actual count is lower than max,
         *    false otherwise.
         *    
         *    @param    className name of class
         *    @param    dir where tu find class
         *    
         *    @return true if max count is "*" or actual count is lower than max, false otherwise.                                                                                
         *
         */                                     
        private function isCountOfInstances($className, $classDir, $classPath) {
            $count = 0;

            if (array_key_exists($classDir.".".$className, $this->instanceCounter)) {
                $count = $this->instanceCounter[$classDir.".".$className];
            }
            
            $xmlPath = $classPath . ".xml";
            if (is_file(APP_SCRIPTS_PATH . $xmlPath)) {
                $xml = $this->getXml(APP_SCRIPTS_PATH . $xmlPath);

                if (!isset($xml->count) || (string)$xml->count == "*") {
                    return true;
                } else if ((int)$xml->count > $count) {
                    return true;
                } else {
                    return false;
                }
            } else {
                $str = "Xml library definition doesn't exists! [".$xmlPath."]";
                trigger_error($str , E_USER_WARNING);
                echo "<h4 class=\"error\">".$str."</h4>";
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
    }

?>
