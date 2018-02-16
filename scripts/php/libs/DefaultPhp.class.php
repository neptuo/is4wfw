<?php

    /**
     *
     *    Require base tag lib class.
     *
     */
    require_once("BaseTagLib.class.php");
    require_once("scripts/php/classes/FullTagParser.class.php");

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
            "php.libs.Error" => 1, 
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
            parent::setTagLibXml("xml/DefaultPhp.xml");
            
            // Init defalt objects (php, error, log)
            require_once(PHP_SCRIPTS."libs/Error.class.php");
            $GLOBALS['errorObject'] = new Error();
            require_once(PHP_SCRIPTS."libs/Log.class.php");
            $GLOBALS['logObject'] = new Log();
            require_once(PHP_SCRIPTS."libs/Database.class.php");
            $GLOBALS['dbObject'] = new Database();
            require_once(PHP_SCRIPTS."libs/Login.class.php");
            $GLOBALS['loginObject'] = new Login();
            require_once(PHP_SCRIPTS."libs/System.class.php");
            $GLOBALS['sysObject'] = new System();
            
            set_error_handler("Error::errorHandler");
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
                    require_once(SCRIPTS.$classPath.".class.php");
                    
                    if (self::isCountOfInstances($className, $classDir)) {
                        $GLOBALS[$tagPrefix."Object"] = new $className;
                        if(array_key_exists($classJPath, $this->_CLASSES)) {
                            $this->_CLASSES[$classJPath] ++;
                        } else {
                            $this->_CLASSES[$classJPath] = 1;
                        }
                        $this->_REGISTERED[$tagPrefix] = $classDir;
                    } else {
                        $str = "Too much instances of tag lib! [".$classJPath."]";
                        trigger_error($str , E_USER_WARNING);
                        echo "<h4 class=\"error\">".$str."</h4>";
                    }
                } else {
                    $str = "This class doesn't existecho '-'.$return.'-';! [".$classJPath."]";
                    trigger_error($str , E_USER_WARNING);
                    echo "<h4 class=\"error\">".$str."</h4>";
                }
            } else {
                $str = "This tag prefix also used! [".$tagPrefix."]";
                trigger_error($str , E_USER_WARNING);
                echo "<h4 class=\"error\">".$str."</h4>";
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
            $path = SCRIPTS.self::parseClassPath($classPath).".class.php";
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
         *    @return parsed string into array                                         
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
            $xml = self::getXml(PHP_SCRIPTS . 'autoregister.xml');
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
                if (is_file(SCRIPTS.$xmlPath)) {
                    $xml = self::getXml(SCRIPTS . $xmlPath);
                    
                    foreach ($xml->tag as $tag) {
                        if ($tag->tagname == $tagName) {
                            for ($i = 0; $i < count($tag->attribute); $i ++) {
                                $att = $tag->attribute[$i];
                                $req = true;
                                if (!array_key_exists((string)$att->attname, $atts) && strtolower($att->attreq) == "required") {
                                    $str = "Missing required attribute! [".$att->attname."]";
                                    trigger_error($str , E_USER_WARNING);
                                    echo "<h4 class=\"error\">".$str."</h4>";
                                    return false;
                                }
                            }
                        }
                    }
                    
                    return true;
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
            if(array_key_exists($tagPrefix, $this->_REGISTERED)) {
                global ${$tagPrefix."Object"};
                $xmlPath = str_replace(".", "/", $this->_REGISTERED[$tagPrefix])."/".${$tagPrefix."Object"}->getTagLibXml();
            } else if(array_key_exists($tagPrefix, $this->_DEFAULT)) {
                global ${$tagPrefix."Object"};
                $xmlPath = str_replace(".", "/", $this->_DEFAULT[$tagPrefix])."/".${$tagPrefix."Object"}->getTagLibXml();
            }
            
            if(isset($xmlPath)) {
                if(is_file(SCRIPTS.$xmlPath)) {
                    $xml = self::getXml(SCRIPTS.$xmlPath);
                    
                    foreach($xml->fulltag as $tag) {
                        if($tag->tagname == $tagName) {
                            for($i = 0; $i < count($tag->attribute); $i ++) {
                                $att = $tag->attribute[$i];
                                $req = true;
                                if(!array_key_exists((string)$att->attname, $atts) && strtolower($att->attreq) == "required") {
                                    $str = "Missing required attribute! [".$att->attname."]";
                                    trigger_error($str , E_USER_WARNING);
                                    echo "<h4 class=\"error\">".$str."</h4>";
                                    return false;
                                }
                            }
                        }
                    }
                    
                    return true;
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
                if (is_file(SCRIPTS.$xmlPath)) {
                    $xml = self::getXml(SCRIPTS . $xmlPath);
                    
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
                if (is_file(SCRIPTS.$xmlPath)) {
                    $xml = self::getXml(SCRIPTS . $xmlPath);
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
            $tmp = new $className();
            $xmlPath = str_replace(".", "/", $classDir)."/".$tmp->getTagLibXml();
            if (is_file(SCRIPTS.$xmlPath)) {
                $xml = self::getXml(SCRIPTS.$xmlPath);

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

        private function sortAttributesInternal($tagListName, $tagPrefix, $tagName, $atts) {
            $return = array();
            if (array_key_exists($tagPrefix, $this->_REGISTERED)) {
                global ${$tagPrefix."Object"};
                $xmlPath = str_replace(".", "/", $this->_REGISTERED[$tagPrefix])."/".${$tagPrefix."Object"}->getTagLibXml();
            } else if (array_key_exists($tagPrefix, $this->_DEFAULT)) {
                global ${$tagPrefix."Object"};
                $xmlPath = str_replace(".", "/", $this->_DEFAULT[$tagPrefix])."/".${$tagPrefix."Object"}->getTagLibXml();
            }
            
            if (isset($xmlPath)) {
                if (is_file(SCRIPTS.$xmlPath)) {
                    $xml = self::getXml(SCRIPTS . $xmlPath);
                    foreach ($xml->{$tagListName} as $tag) {
                        if ($tag->tagname == $tagName) {
                            for ($i = 0; $i < count($tag->attribute); $i ++) {
                                $att = $tag->attribute[$i];
                                if (array_key_exists((string)$att->attname, $atts)) {
                                    $return[(string)$att->attname] = self::getConvertValue($atts[(string)$att->attname], isset($att->attdef));
                                } elseif (isset($att->attdef)) {
                                    eval('$val = '. $att->attdef.';');
                                    $return[(string)$att->attname] = self::getConvertValue($val);
                                } else {
                                    $return[(string)$att->attname] = false;
                                }
                            }
                    
                            if (isset($tag->anyAttribute)) {
                                $params = array();
                                foreach ($atts as $usedName => $usedValue) {
                                    if (!array_key_exists($usedName, $return)) {
                                        $params[$usedName] = $usedValue;
                                    }
                                }
        
                                $return[DefaultPhp::$ParamsName] = $params;
                            }
                            break;
                        }
                    }

                    return $return;
                } else {
                    $str = "Xml library definition doesn.'t exists! [".$xmlPath."]";
                    trigger_error($str , E_USER_WARNING);
                    echo "<h4 class=\"error\">".$str."</h4>";
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
        public function sortAttributes($tagPrefix, $tagName, $atts) {
            return self::sortAttributesInternal("tag", $tagPrefix, $tagName, $atts);
        }
        
        protected function getConvertValue($val, $convert) {
            if(!$convert) {
                return $val;
            }
        
            if($val === 'true') {
                return true;
            }
            if($val === 'false') {
                return false;
            }
            return $val;
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
        public function sortFullAttributes($tagPrefix, $tagName, $atts, $content) {
            $return = array_merge(array('full:content' => $content), self::sortAttributesInternal("fulltag", $tagPrefix, $tagName, $atts));
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
                if (is_file(SCRIPTS.$xmlPath)) {
                    $xml = self::getXml(SCRIPTS . $xmlPath);
                    
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
                if (is_file(SCRIPTS.$xmlPath)) {
                    $xml = self::getXml(SCRIPTS . $xmlPath);
                    
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
                if (is_file(SCRIPTS.$xmlPath)) {
                    $xml = self::getXml(SCRIPTS . $xmlPath);
                    
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
            $webObject->cache($cache);
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
