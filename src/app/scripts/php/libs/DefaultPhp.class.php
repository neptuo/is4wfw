<?php

    require_once("BaseTagLib.class.php");
    require_once("ErrorHandler.class.php");
    require_once("Log.class.php");
    require_once("Database.class.php");
    require_once("Login.class.php");
    require_once("System.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/FullTagParser.class.php");

    /**
     *
     *    DefaultPhpClass is for registrating & unregistrating tag-libs.
     *    Default object.            
     *    
     *    @objectname phpObject
     *    
     *    @author         Marek SMM
     *    @timestamp    2012-01-17
     *
     */                
    class DefaultPhp extends BaseTagLib {
        
        public static $ParamsName = 'params';
        public static $FullTagTemplateName = 'full:content';
    
        /**
         *
         *    List of default registered tag libs.
         *
         */                                     
        private $_DEFAULT = array(
            "php" => "php.libs", 
            "web" => "php.libs", 
            "error" => "php.libs", 
            "log" => "php.libs", 
            "db" => "php.libs", 
            "login" => "php.libs", 
            "sys" => "php.libs"
        );
    
        /**
         *
         *    Array with registered tag prefixes.
         *
         */                                     
        private $_REGISTERED = array();
        
        /**
         *
         *    Array with count of instacence of each tag lib.
         *
         */                                     
        private $_CLASSES = array(
            "php.libs.DefaultPhp" => 1, 
            "php.libs.DefaultWeb" => 1, 
            "php.libs.ErrorHandler" => 1, 
            "php.libs.Log" => 1, 
            "php.libs.Database" => 1,
            "php.libs.Login" => 1, 
            "php.libs.System" => 1
        );
                                                            
        private $_AUTOXML = null;
        
        /**
         *
         *    Creates other default objects.
         *
         */                                                
        public function __construct() {
            parent::setTagLibXml("DefaultPhp.xml");
            
            // Init defalt objects (php, error, log)
            $GLOBALS['errorObject'] = new ErrorHandler();
            $GLOBALS['logObject'] = new Log();
            $GLOBALS['dbObject'] = new Database();
            $GLOBALS['loginObject'] = new Login();
            $GLOBALS['sysObject'] = new System();
            
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
         *    Registrate tag library.
         *    
         *    @param $attlist array with required parameters (tagPrefix & classPath)                    
         *
         */                                                
        public function register($tagPrefix, $classPath) {
            $classJPath = $classPath;
            //echo $tagPrefix.', '.$classPath.'<br />';
            //$tagPrefix = $attlist['tagPrefix'];
            //$classPath = $attlist['classPath'];
            // KONTROLOVAT POCET INSTANCI PODLE <count> V XML!!!
            if (!array_key_exists($tagPrefix, $this->_REGISTERED) && !array_key_exists($tagPrefix, $this->_DEFAULT)) {
                if (self::checkIfClassExists($tagPrefix, $classPath)) {
                    $classArray = self::str_tr($classPath, '.');
                    $classDir = "";
                    for ($i = 0; $i < count($classArray) - 1; $i ++) {
                        $classDir .= $classArray[$i];
                        if($i < (count($classArray) - 2)) {
                            $classDir .= ".";
                        }
                    }

                    $className = $classArray[count($classArray) - 1];
                    $classPath = self::parseClassPath($classPath);

                    require_once(APP_SCRIPTS_PATH . $classPath . ".class.php");
                    
                    if (self::isCountOfInstances($className, $classDir)) {
                        $GLOBALS[$tagPrefix."Object"] = new $className($tagPrefix);
                        if(array_key_exists($classJPath, $this->_CLASSES)) {
                            $this->_CLASSES[$classJPath] ++;
                        } else {
                            $this->_CLASSES[$classJPath] = 1;
                        }
                        $this->_REGISTERED[$tagPrefix] = $classDir;
                    } else {
                        return '<h4 class="error">Too much instances of tag lib! [' . $classJPath . ']</h4>';
                    }
                } else {
                    return '<h4 class="error">This class does not exist "' . $classPath . '".</h4>';
                }
            } else {
                return '<h4 class="error">This tag prefix already used! [' . $tagPrefix . ']</h4>';
            }
            
            return "";
        }
    
        /**
         *
         *    Unregistrate tag library.
         *    
         *    @param $attlist array with required parameters (tagPrefix)                    
         *
         */ 
        public function unregister($tagPrefix) {
            //$tagPrefix = $attlist['tagPrefix'];
            
            if (array_key_exists($tagPrefix, $this->_REGISTERED) && $tagPrefix != "php") {
                foreach($this->_REGISTERED as $name => $tmp) {
                    if($name == $tagPrefix) {
                        unset($this->_REGISTERED[$name]);
                        unset(${$name."Object"});
                        $this->_CLASSES[$name] --;
                        break;
                    }
                }
            } else {
                //$str = "This tag prefix doesn't exist! [".$tagPrefix."]";
                //trigger_error($str , E_USER_WARNING);
                //echo "<h4 class=\"error\">".$str."</h4>";
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
            $path = APP_SCRIPTS_PATH . self::parseClassPath($classPath).".class.php";
            if(is_file($path)) {
                //$cont = file_get_contents($path);
                //if(eregi("class *"))
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
         *    Cut string into array by d.
         *    
         *    @param    s string
         *    @param    d delimeter
         *    @param    c count         
         *    @return array parsed string into array                                         
         *
         */
        public function str_tr($s, $d, $c = 1000000){
            if(strlen($d) == 1) {
                $res = array();
                $t = "";
                for($i = 0; $i < strlen($s); $i ++) {
                    if($s[$i] == $d && ($i < (strlen($s) - 1) && $i > 0)) {
                        if($c > 0) {
                            $res[] = $t;
                            $t = "";
                            $c --;
                        } else {
                            $t .= $s[$i];
                        }
                    } elseif($s[$i] != $d) {
                        $t .= $s[$i];
                    }
                }
                $res[] = $t;
                $t = "";
                return $res;
            } else {
                return $s;
            }
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
            if (array_key_exists($tagPrefix, $this->_REGISTERED) || array_key_exists($tagPrefix, $this->_DEFAULT)) {
                return true;
            } else {
                return self::autoRegisterPrefix($tagPrefix);
            }
        }
        
        public function autoRegisterPrefix($prefix) {
            $xml = self::getXml(APP_SCRIPTS_PHP_PATH . 'autoregister.xml');
            foreach ($xml->reg as $reg) {
                $attrs = $reg->attributes();
                if ($attrs['prefix'] == $prefix) {
                    self::register($prefix, (string)$attrs['class']);
                    return true;
                }
            }
            
            return false;
        }
        
        /**
         *
         *    Check if passed values are valid tag of library.
         *    
         *    @param    tagPrefix library object name
         *    @param    tagName name of required tag
         *    @param    atts    passed tag attributes
         *    @return true is passed values are valid tagPrefix and tag name, false other wise
         *
         */                                     
        public function isTag($tagPrefix, $tagName, $atts) {
            if (array_key_exists($tagPrefix, $this->_REGISTERED)) {
                global ${$tagPrefix."Object"};
                $xmlPath = str_replace(".", "/", $this->_REGISTERED[$tagPrefix])."/".${$tagPrefix."Object"}->getTagLibXml();
            } else if (array_key_exists($tagPrefix, $this->_DEFAULT)) {
                global ${$tagPrefix."Object"};
                $xmlPath = str_replace(".", "/", $this->_DEFAULT[$tagPrefix])."/".${$tagPrefix."Object"}->getTagLibXml();
            }
            
            if (isset($xmlPath)) {
                if (is_file(APP_SCRIPTS_PATH . $xmlPath)) {
                    $xml = self::getXml(APP_SCRIPTS_PATH . $xmlPath);
                    
                    foreach ($xml->tag as $tag) {
                        if ($tag->tagname == $tagName) {
                            return true;
                        }
                    }
                    
                    return false;
                } else {
                    $str = "Xml library definition doesn't exists! [".$xmlPath."]";
                    trigger_error($str , E_USER_WARNING);
                    echo "<h4 class=\"error\">".$str."</h4>";
                    return false;
                }
            } else {
                return false;
            }
            
            return true;
        }
        
        /**
         *
         *    Check if passed values are valid fulltag of library.
         *    
         *    @param    tagPrefix library object name
         *    @param    tagName name of required tag
         *    @param    atts    passed tag attributes
         *    @return true is passed values are valid tagPrefix and tag name, false other wise
         *
         */                                     
        public function isFullTag($tagPrefix, $tagName, $atts) {
            if (array_key_exists($tagPrefix, $this->_REGISTERED)) {
                global ${$tagPrefix."Object"};
                $xmlPath = str_replace(".", "/", $this->_REGISTERED[$tagPrefix])."/".${$tagPrefix."Object"}->getTagLibXml();
            } else if (array_key_exists($tagPrefix, $this->_DEFAULT)) {
                global ${$tagPrefix."Object"};
                $xmlPath = str_replace(".", "/", $this->_DEFAULT[$tagPrefix])."/".${$tagPrefix."Object"}->getTagLibXml();
            }
            
            if (isset($xmlPath)) {
                if (is_file(APP_SCRIPTS_PATH . $xmlPath)) {
                    $xml = self::getXml(APP_SCRIPTS_PATH . $xmlPath);
                    
                    foreach ($xml->fulltag as $tag) {
                        if ($tag->tagname == $tagName) {
                            return true;
                        }
                    }
                    
                    return false;
                } else {
                    $str = "Xml library definition doesn't exists! [".$xmlPath."]";
                    trigger_error($str , E_USER_WARNING);
                    echo "<h4 class=\"error\">".$str."</h4>";
                    return false;
                }
            } else {
                return false;
            }
            
            return true;
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
            if (array_key_exists($tagPrefix, $this->_REGISTERED)) {
                global ${$tagPrefix."Object"};
                $xmlPath = str_replace(".", "/", $this->_REGISTERED[$tagPrefix])."/".${$tagPrefix."Object"}->getTagLibXml();
            } else if (array_key_exists($tagPrefix, $this->_DEFAULT)) {
                global ${$tagPrefix."Object"};
                $xmlPath = str_replace(".", "/", $this->_DEFAULT[$tagPrefix])."/".${$tagPrefix."Object"}->getTagLibXml();
            }
            
            if (isset($xmlPath)) {
                if (is_file(APP_SCRIPTS_PATH . $xmlPath)) {
                    $xml = self::getXml(APP_SCRIPTS_PATH . $xmlPath);
                    
                    foreach ($xml->property as $prop) {
                        if ($prop->propname == $propName) {
                            return true;
                        }
                    }

                    return false;
                } else {
                    $str = "Xml library definition doesn't exists! [".$xmlPath."]";
                    trigger_error($str , E_USER_WARNING);
                    //echo "<h4 class=\"error\">".$str."</h4>";
                    return false;
                }
            } else {
                return false;
            }
            
            return true;
        }

        // Vrací true, pokud daná knihovna umožňuje <anyProperty />.
        public function isAnyProperty($tagPrefix) {
            if (array_key_exists($tagPrefix, $this->_REGISTERED)) {
                global ${$tagPrefix."Object"};
                $xmlPath = str_replace(".", "/", $this->_REGISTERED[$tagPrefix])."/".${$tagPrefix."Object"}->getTagLibXml();
            } else if (array_key_exists($tagPrefix, $this->_DEFAULT)) {
                global ${$tagPrefix."Object"};
                $xmlPath = str_replace(".", "/", $this->_DEFAULT[$tagPrefix])."/".${$tagPrefix."Object"}->getTagLibXml();
            }
            
            if (isset($xmlPath)) {
                if (is_file(APP_SCRIPTS_PATH . $xmlPath)) {
                    $xml = self::getXml(APP_SCRIPTS_PATH . $xmlPath);
                    return isset($xml->anyProperty);
                } else {
                    $str = "Xml library definition doesn't exists! [".$xmlPath."]";
                    trigger_error($str , E_USER_WARNING);
                    //echo "<h4 class=\"error\">".$str."</h4>";
                    return false;
                }
            } else {
                return false;
            }
            
            return true;
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
        private function isCountOfInstances($className, $classDir) {
            $count = 0;

            if (array_key_exists($classDir.".".$className, $this->_CLASSES)) {
                $count = $this->_CLASSES[$classDir.".".$className];
            }
            
            //echo ' '.$className.'<br />';
            $tmp = new $className("");
            $xmlPath = str_replace(".", "/", $classDir)."/".$tmp->getTagLibXml();
            if (is_file(APP_SCRIPTS_PATH . $xmlPath)) {
                $xml = self::getXml(APP_SCRIPTS_PATH . $xmlPath);

                if ((string)$xml->count == "*") {
                    return true;
                } else if ((int)$xml->count > $count) {
                    return true;
                } else {
                    return false;
                }
            } else {
                $str = "Xml library definition doesn.'t exists! [".$xmlPath."]";
                trigger_error($str , E_USER_WARNING);
                echo "<h4 class=\"error\">".$str."</h4>";
                return false;
            }
        }

        private function processParsedAttributeValue($attribute, $definition, $hasDefault) {
            $attributeValue = self::getConvertValue($attribute['value'], $definition);
            if ($attributeValue != null) {
                $attributeValueType = 'raw';
                if (($hasDefault && $attributeValue['type'] == 'eval') || $attribute['type'] == 'eval') {
                    $attributeValueType = 'eval';
                }

                return array('value' => $attributeValue['value'], 'type' => $attributeValueType);
            }

            return null;
        }

        private function sortAttributesInternal($tagListName, $tagPrefix, $tagName, $atts) {
            $return = array();
            if (array_key_exists($tagPrefix, $this->_REGISTERED)) {
                global ${$tagPrefix."Object"};
                $xmlPath = str_replace(".", "/", $this->_REGISTERED[$tagPrefix]) . "/" . ${$tagPrefix."Object"}->getTagLibXml();
            } else if (array_key_exists($tagPrefix, $this->_DEFAULT)) {
                global ${$tagPrefix."Object"};
                $xmlPath = str_replace(".", "/", $this->_DEFAULT[$tagPrefix]) ."/" . ${$tagPrefix."Object"}->getTagLibXml();
            }
            
            $processedAtts = array();
            if (isset($xmlPath)) {
                if (is_file(APP_SCRIPTS_PATH . $xmlPath)) {
                    $xml = self::getXml(APP_SCRIPTS_PATH . $xmlPath);
                    foreach ($xml->{$tagListName} as $tag) {
                        if ($tag->tagname == $tagName) {
                            for ($i = 0; $i < count($tag->attribute); $i ++) {
                                $isProcessed = false;
                                $att = $tag->attribute[$i];
                                $attName = (string)$att->attname;
                                $hasDefault = isset($att->attdef);
                                if ($att->prefix == true) {
                                    $attPrefix = "$attName-";
                                    $attributeValue = array();
                                    foreach ($atts as $usedName => $usedValue) {
                                        if (parent::startsWith($usedName, $attPrefix)) {
                                            $strippedName = substr($usedName, strlen($attPrefix));

                                            $attribute = $atts[$usedName];
                                            $processValue = self::processParsedAttributeValue($attribute, $att, $hasDefault);
                                            if ($processValue != null) {
                                                $processedAtts[] = $usedName;
                                                $attributeValue[$strippedName] = $processValue;
                                                continue;
                                            }
                                        }
                                    }

                                    $return[$attName] = array('value' => $attributeValue, 'type' => "eval");
                                    $isProcessed = true;
                                }
                                
                                if (array_key_exists($attName, $atts)) {
                                    $attribute = $atts[$attName];
                                    $processValue = self::processParsedAttributeValue($attribute, $att, $hasDefault);
                                    if ($processValue != null) {
                                        $processedAtts[] = $attName;
                                        if (array_key_exists($attName, $return)) {
                                            $return[$attName]["value"][""] = $processValue;
                                        } else {
                                            $return[$attName] = $processValue;
                                        }

                                        $isProcessed = true;
                                    }
                                }

                                if ($isProcessed) {
                                    continue;
                                }
                                
                                if ($hasDefault) {
                                    if ($att->atttype == 'string') {
                                        $attributeValue = "'" . eval('return "'. $att->attdef.'";') . "'";
                                    } else {
                                        $attributeValue = eval('return '. $att->attdef.';');
                                    }
                                    
                                    $return[$attName] = array('value' => $attributeValue, 'type' => 'eval');
                                } else if (strtolower($att->attreq) == "required") {
                                    $str = "Missing required attribute '$att->attname' on tag '$tagPrefix:$tagName'.";
                                    trigger_error($str , E_USER_WARNING);
                                    echo parent::getError($str);
                                    return false;
                                } else {
                                    $return[$attName] = array('value' => false, 'type' => 'raw');
                                }
                            }
                    
                            if (isset($tag->anyAttribute)) {
                                $params = array();
                                foreach ($atts as $usedName => $usedValue) {
                                    if (!in_array($usedName, $processedAtts)) {
                                        $processedAtts[] = $usedName;
                                        $params[$usedName] = $usedValue;
                                    }
                                }
        
                                $return[DefaultPhp::$ParamsName] = array('value' => $params, 'type' => 'eval');
                            }
                            break;
                        }
                    }

                    if (count($processedAtts) == count($atts)) {
                        return $return;
                    } else {
                        foreach ($atts as $name => $value) {
                            if (!in_array($name, $processedAtts)) {
                                $str = "Used undefined attribute! [$name] on [$tagPrefix:$tagName]";
                                trigger_error($str , E_USER_WARNING);
                                echo parent::getError($str);
                            }
                        }

                        return false;
                    }

                } else {
                    $str = "Xml library definition doesn't exists! [".$xmlPath."]";
                    trigger_error($str , E_USER_WARNING);
                    echo parent::getError($str);
                    return false;
                }
                
            } else {
                return false;
            }
        }
        
        /**
         *
         *    Sort attributes to right sequence.
         *    
         *    @param    tagPrefix library object name
         *    @param    tagName name of required tag
         *    @param    atts    passed tag attributes
         *    @return sorted attributes
         *
         */                                                
        public function sortAttributes($tagPrefix, $tagName, $attributes) {
            return self::sortAttributesInternal("tag", $tagPrefix, $tagName, $attributes);
        }
        
        protected function getConvertValue($val, $att) {
            $convert = isset($att->attdef);
            
            if (isset($att->atttype)) {
                if (self::startsWith($val, 'template_') && self::endsWith($val, '()')) {
                    return array('value' => $val, 'type' => 'eval');
                }

                switch ($att->atttype) {
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
        
        /**
         *
         *    Sort attributes to right sequence.
         *    
         *    @param    tagPrefix library object name
         *    @param    tagName name of required tag
         *    @param    atts    passed tag attributes
         *    @return sorted attributes
         *
         */                                                
        public function sortFullAttributes($tagPrefix, $tagName, $attributes, $content) {
            $sorted = self::sortAttributesInternal("fulltag", $tagPrefix, $tagName, $attributes);
            if ($sorted === false) {
                return false;
            }

            $return = array_merge(array(DefaultPhp::$FullTagTemplateName => array('value' => $content, 'type' => 'raw')), $sorted);
            return $return;
        }
        
        /**
         *
         *    Return function name to passed tag name.
         *    
         *    @return function name                    
         *
         */                                     
        public function getFuncToTag($tagPrefix, $tagName) {
            if (array_key_exists($tagPrefix, $this->_REGISTERED)) {
                global ${$tagPrefix."Object"};
                $xmlPath = str_replace(".", "/", $this->_REGISTERED[$tagPrefix])."/".${$tagPrefix."Object"}->getTagLibXml();
            } else if (array_key_exists($tagPrefix, $this->_DEFAULT)) {
                global ${$tagPrefix."Object"};
                $xmlPath = str_replace(".", "/", $this->_DEFAULT[$tagPrefix])."/".${$tagPrefix."Object"}->getTagLibXml();
            }
            
            if (isset($xmlPath)) {
                if (is_file(APP_SCRIPTS_PATH . $xmlPath)) {
                    $xml = self::getXml(APP_SCRIPTS_PATH . $xmlPath);
                    
                    foreach ($xml->tag as $tag) {
                        if ($tag->tagname == $tagName) {
                            return (string)$tag->function;
                        }
                    }
                    
                    $str = "Unnable to find tag [".$tagName."] in lib [".$tagPrefix."]";
                    trigger_error($str , E_USER_WARNING);
                    echo "<h4 class=\"error\">".$str."</h4>";
                    return false;
                } else {
                    $str = "Xml library definition doesn.'t exists! [".$xmlPath."]";
                    trigger_error($str , E_USER_WARNING);
                    echo "<h4 class=\"error\">".$str."</h4>";
                    return false;
                }
                
            } else {
                $str = "Tag prefix isn't registered! [".$tagPrefix."]";
                trigger_error($str , E_USER_WARNING);
                echo "<h4 class=\"error\">".$str."</h4>";
                return false;
            }
        }
        
        /**
         *
         *    Return function name to passed fulltag name.
         *    
         *    @return function name                    
         *
         */                                     
        public function getFuncToFullTag($tagPrefix, $tagName) {
            if (array_key_exists($tagPrefix, $this->_REGISTERED)) {
                global ${$tagPrefix."Object"};
                $xmlPath = str_replace(".", "/", $this->_REGISTERED[$tagPrefix])."/".${$tagPrefix."Object"}->getTagLibXml();
            } else if (array_key_exists($tagPrefix, $this->_DEFAULT)) {
                global ${$tagPrefix."Object"};
                $xmlPath = str_replace(".", "/", $this->_DEFAULT[$tagPrefix])."/".${$tagPrefix."Object"}->getTagLibXml();
            }
            
            if (isset($xmlPath)) {
                if (is_file(APP_SCRIPTS_PATH . $xmlPath)) {
                    $xml = self::getXml(APP_SCRIPTS_PATH . $xmlPath);
                    
                    foreach ($xml->fulltag as $tag) {
                        if ($tag->tagname == $tagName) {
                            return (string)$tag->function;
                        }
                    }
                    
                    $str = "Unnable to find tag [".$tagName."] in lib [".$tagPrefix."]";
                    trigger_error($str , E_USER_WARNING);
                    echo "<h4 class=\"error\">".$str."</h4>";
                    return false;
                } else {
                    $str = "Xml library definition doesn.'t exists! [".$xmlPath."]";
                    trigger_error($str , E_USER_WARNING);
                    echo "<h4 class=\"error\">".$str."</h4>";
                    return false;
                }
                
            } else {
                $str = "Tag prefix isn't registered! [".$tagPrefix."]";
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
        public function getFuncToProperty($tagPrefix, $propName, $use) {
            if (array_key_exists($tagPrefix, $this->_REGISTERED)) {
                global ${$tagPrefix."Object"};
                $xmlPath = str_replace(".", "/", $this->_REGISTERED[$tagPrefix])."/".${$tagPrefix."Object"}->getTagLibXml();
            } else if (array_key_exists($tagPrefix, $this->_DEFAULT)) {
                global ${$tagPrefix."Object"};
                $xmlPath = str_replace(".", "/", $this->_DEFAULT[$tagPrefix])."/".${$tagPrefix."Object"}->getTagLibXml();
            }
            
            if (isset($xmlPath)) {
                if (is_file(APP_SCRIPTS_PATH . $xmlPath)) {
                    $xml = self::getXml(APP_SCRIPTS_PATH . $xmlPath);
                    
                    foreach ($xml->property as $prop) {
                        if ($prop->propname == $propName) {
                            if (strtolower($use) == 'set') {
                                return (string)$prop->setfunction;
                            } elseif (strtolower($use) == 'get') {
                                    return (string)$prop->getfunction;
                            } else {
                                //$str = "Bad use!";
                                //trigger_error($str , E_USER_WARNING);
                                //echo "<h4 class=\"error\">".$str."</h4>";
                                return false;                        
                            }
                        }
                    }
                    
                    $str = "Unnable to find tag [".$tagName."] in lib [".$tagPrefix."]";
                    trigger_error($str , E_USER_WARNING);
                    echo "<h4 class=\"error\">".$str."</h4>";
                    return false;
                } else {
                    $str = "Xml library definition doesn.'t exists! [".$xmlPath."]";
                    trigger_error($str , E_USER_WARNING);
                    echo "<h4 class=\"error\">".$str."</h4>";
                    return false;
                }
                
            } else {
                $str = "Tag prefix isn't registered! [".$tagPrefix."]";
                trigger_error($str , E_USER_WARNING);
                echo "<h4 class=\"error\">".$str."</h4>";
                return false;
            }
        }
        
        /**
         *
         *    Set caching in webObject :D
         *
         *    @param    cache bool value for caching         
         *         
         */
        public function cache($cache) {
            global $webObject;
            $webObject->cache($cache, 60);
        }
        
        public function usingObject($content, $prefix, $class) {
            $return = '';
            self::register($prefix, $class);

            $parser = new FullTagParser();
            $parser->setContent($content);
            $parser->startParsing();
            $return = $parser->getResult();
            
            self::unregister($prefix);
            return $return;
        }
                                                
    }

?>
