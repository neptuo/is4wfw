<?php

    require_once('System.class.php');
    require_once('FileAdmin.class.php');
    require_once(APP_SCRIPTS_PHP_PATH . "classes/ExtensionParser.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/manager/SystemProperty.class.php");

    /**
     *
     *  Base class for all tag libs.
     *  
     *  @author     Marek SMM
     *  @timestamp  2009-10-21
     *  @version    1.07
     *
     */
    class BaseTagLib {

        /**
         *
         *  Path to library xml definition.
         *
         */
        private $TagLibXml = "";
        /**
         *
         *  True, if no is used on page yet.     
         *
         */
        private $FirstFrame = true;
        /**
         *
         * 	Use caching for template content
         * 	REQUEST ...... for caching for single request
         *
         */
        private $CacheTemplatesContent = 'REQUEST';

        /**
         *
         *  return path to library xml definition.
         *  
         *  @return path to library xml definition
         *
         */
        public function getTagLibXml() {
            return $this->TagLIbXml;
        }

        /**
         *
         *  set path to library xml definition.
         *  
         *  @return none
         *
         */
        protected function setTagLibXml($xml) {
            $this->TagLIbXml = $xml;
        }

        /**
         *
         * 	Setup template content caching.
         * 	For possible values, see field definition
         *
         */
        protected function setCacheTemplatesContent($val) {
            $this->CacheTemplatesContent = $val;
        }

        /**
         *
         *  Returns web file extenstions.
         *  
         *  @return    web file extenstions          
         *
         */
        public function getFileEx() {
            return FileAdmin::$FileExtensions;
        }

        /**
         *
         *  Generates frame.
         *  
         *  @param    label     frame label
         *  @param    content   frame content
         *  @param    classes   extra classes for frame-cover
         *  @return   content in frame          
         *
         */
        public function getFrame($label, $content, $classes, $ignoreFirstFrame = false) {
            global $phpObject;
            global $dbObject;
            global $loginObject;

            if (strlen($content) == 0) {
                return '';
            }

            //$escapeChars = array("ě" => "e", "é" => "e", "ř" => "r", "ť" => "t", "ý" => "y", "ú" => "u", "ů" => "u", "í" => "i", "ó" => "o", "á" => "a", "š" => "s", "ď" => "d", "ž" => "z", "č" => "c", "ň" => "n");
            $name = 'Frame.' . strtolower(str_replace(' ', '', $label));
            $name = $phpObject->str_tr($name, ':');
            $name = $name[0];
            //$name = strtr($name, $escapeChars);
            $name = self::convertToUrlValid($name);

            $value = self::system()->getPropertyValue($name);
            $closed = false;
            if ($value == 'true') {
                $closed = true;
            }

            if ($_COOKIE[$name] == 'closed') {
                $closed = true;
            } elseif ($_COOKIE[$name] == 'opened') {
                $closed = false;
            }
            
            $defaultClosed = !$this->FirstFrame && !$ignoreFirstFrame;
            if(self::system()->getPropertyValue('Frames.leaveOpened') == 'true') {
                $defaultClosed = false;
            }

            $addAttrs;
            if ($_REQUEST['__TEMPLATE'] == 'xml') {
                $props = $dbObject->fetchAll('SELECT `left`, `top`, `width`, `height`, `maximized` FROM `window_properties` WHERE `frame_id` = "' . $name . '" AND `user_id` = ' . $loginObject->getUserId() . ';');
                if (count($props) == 1) {
                    $addAttrs = 'left="' . $props[0]['left'] . '" top="' . $props[0]['top'] . '" width="' . $props[0]['width'] . '" height="' . $props[0]['height'] . '" maximized="' . ($props[0]['maximized'] ? "true" : "false") . '"';
                }
            }

            $return = ''
            . '<div id="' . $name . '" class="frame frame-cover ' . $name . '' . ((strlen($classes)) ? ' ' . $classes : '') . (($defaultClosed || $closed) ? ' closed-frame' : '') . '"' . (($addAttrs != "") ? ' ' . $addAttrs : '') . '>'
                . '<div class="frame frame-head">'
                    . '<div class="frame-label">'
                        . $label
                    . '</div>'
                    . '<div class="frame-close">'
                        . '<a class="click-able click-able-roll" href="#"><span>^</span></a>'
                    . '</div>'
                    . '<div class="clear"></div>'
                . '</div>'
                . '<div class="frame frame-body">'
                    . $content
                . '</div>'
            . '</div>';
            if (!$ignoreFirstFrame) {
                $this->FirstFrame = false;
            }
            return $return;
        }

        public function getError($msg) {
            return strlen($msg) == 0 ? '' : '<h4 class="error">' . $msg . '</h4>';
        }

        public function getSuccess($msg) {
            return strlen($msg) == 0 ? '' : '<h4 class="success">' . $msg . '</h4>';
        }

        public function getWarning($msg) {
            return strlen($msg) == 0 ? '' : '<h4 class="warning">' . $msg . '</h4>';
        }

        public function getTemplateContent($templateId) {
            global $webObject;
            global $dbObject;
            global $loginObject;
            $templateContent = "";

            if ($this->CacheTemplatesContent == 'REQUEST' && self::request()->exists($templateId, 'templates')) {
                return self::request()->get($templateId, 'templates');
            }

            $rights = $dbObject->fetchAll('SELECT `value` FROM `template` LEFT JOIN `template_right` ON `template`.`id` = `template_right`.`tid` LEFT JOIN `group` ON `template_right`.`gid` = `group`.`gid` WHERE `template`.`id` = ' . $templateId . ' AND `template_right`.`type` = ' . WEB_R_READ . ' AND `group`.`value` >= ' . $loginObject->getGroupValue() . ';');
            if (count($rights) > 0 && $templateId > 0) {
                $template = $dbObject->fetchAll('SELECT `content` FROM `template` WHERE `id` = ' . $templateId . ';');
                $templateContent = $template[0]['content'];
            } else {
                $message = "Permission denied when reading template[templateId = " . $templateId . "]!";
                trigger_error($message, E_USER_WARNING);
                return;
            }

            if ($this->CacheTemplatesContent == 'REQUEST') {
                self::request()->set($templateId, $templateContent, 'templates');
            }

            return $templateContent;
        }

        public function autolib($prefix) {
            if (!self::php()->isRegistered($prefix)) {
                if (!self::php()->autoRegisterPrefix($prefix)) {
                    return null;
                }
            }
            
            $name = $prefix . 'Object';
            global ${$name};
            return ${$name};
        }

        public function php() {
            global $phpObject;
            return $phpObject;
        }

        public function web() {
            global $webObject;
            return $webObject;
        }

        public function db() {
            global $dbObject;
            return $dbObject;
        }

        public function login() {
            global $loginObject;
            return $loginObject;
        }

        public function system() {
            global $sysObject;
            return $sysObject;
        }

        public function request() {
            global $requestStorage;
            return $requestStorage;
        }

        public function session() {
            global $sessionStorage;
            return $sessionStorage;
        }

        public function query() {
            global $queryStorage;
            return $queryStorage;
        }

        public function convertToUrlValid($value, $allowSlash = true) {
            $value = str_replace(' - ', '-', $value);

            $escapeChars = array("ě" => "e", "é" => "e", "ř" => "r", "ť" => "t", "ý" => "y", "ú" => "u", "ů" => "u", "í" => "i", "ó" => "o", "á" => "a", "š" => "s", "ď" => "d", "ž" => "z", "č" => "c", "ň" => "n", "Ě" => "E", "É" => "E", "Ř" => "R", "Ť" => "T", "Ý" => "Y", "Ú" => "U", "Ů" => "U", "Í" => "I", "Ó" => "O", "Á" => "A", "Š" => "S", "Ď" => "D", "Ž" => "Z", "Č" => "C", "Ň" => "N", " " => '-', "\"" => '-', "'" => '-', "(" => '-', ")" => '-', "[" => '-', "]" => '-', "{" => '-', "}" => '-');
            
            if(strpos($value, "http") !== 0) {
                $escapeChars["."] = "-";
            }
            
            $value = strtr($value, $escapeChars);
            
            if(!$allowSlash) {
                $value = strtr($value, array("/" => "-"));
            }
            
            return $value;
        }

        protected function convertToValidUrl($value) {
            return self::convertToUrlValid($value);
        }

        protected function getUserProperty($name, $default = -1) {
            $value = self::system()->getPropertyValue($name);
            if ($value == -1) {
                return $default;
            } else {
                return $value;
            }
        }

        protected function escapeHtmlEntities($value) {
            $escapeChars = array("&" => "&amp;", '>' => '&gt;', '<' => '&lt;', '"' => '&quot;', "~" => "&#126;");
            $value = strtr($value, $escapeChars);
            return $value;
        }

        public function getGroupPerm($name, $groupId, $inherited, $default = '') {
            //echo 'Name: '.$name.', GroupID: '.$groupId.', Inherited: '.($inherited ? 'true' : 'false').', Default: "'.$default.'"<br />';
            if ($groupId != 0) {
                $perms = self::db()->fetchAll('select `name`, `value`, `type` from `group_perms` where `group_id` = ' . $groupId . ';');
                foreach ($perms as $perm) {
                    if ($name == $perm['name']) {
                        return $perm;
                    }
                }

                if ($inherited) {
                    $group = self::db()->fetchSingle('select `parent_gid` from `group` where `gid` = ' . $groupId . ';');
                    if ($group != array()) {
                        return self::getGroupPerm($name, $group['parent_gid'], true, $default);
                    }
                }
            }

            return array('value' => $default);
        }

        public function str_tr($s, $d, $c = 1000000) {
            if (strlen($d) == 1) {
                $res = array();
                $t = "";
                for ($i = 0; $i < strlen($s); $i++) {
                    if ($s[$i] == $d && ($i < (strlen($s) - 1) && $i > 0)) {
                        if ($c > 0) {
                            $res[] = $t;
                            $t = "";
                            $c--;
                        } else {
                            $t .= $s[$i];
                        }
                    } elseif ($s[$i] != $d) {
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
        
        public function getSystemProperty($name) {
            $property = new SystemProperty(self::db()->getDataAccess());
            return $property->getValue($name);
        }
        
        public function setSystemProperty($name, $value) {
            $property = new SystemProperty(self::db()->getDataAccess());
            $property->setValue($name, $value);
        }
        
        private $LocalizationBundle;
        private $BundleName;
        private $BundleLang = 'cs';
        
        public function loadLocalizationBundle($name) {
            $this->BundleName = $name;
            if ($this->web()->LanguageName != '') {
                $rb = new LocalizationBundle();
                if ($rb->testBundleExists($this->BundleName, self::web()->LanguageName)) {
                    $this->BundleLang = self::web()->LanguageName;
                }
            }
            
            $this->LocalizationBundle = new LocalizationBundle();
            $this->LocalizationBundle->loadBundle($this->BundleName, $this->BundleLang);
        }
        
        public function rb($key = false) {
            if($key == false) {
                return $this->LocalizationBundle;
            } else {
                return $this->LocalizationBundle->get($key);
            }
        }
        
        public function view($name, $data) {
            $parser = ExtensionParser::initialize($name, self::rb(), $data);
            return $parser->parse();
        }
        
        private $daos = array();
        
        public function dao($name) {
            if(!array_key_exists($name, $this->daos)) {
                require_once(APP_SCRIPTS_PHP_PATH . 'classes/dataaccess/' . $name . 'Dao.class.php');
                $classname = $name.'Dao';
                $dao = new $classname;
                $dao->setDataAccess(self::db()->getDataAccess());
                $this->daos[$name] = $dao;
            }
            
            return $this->daos[$name];
        }

        public function select() {
            return Select::factory(self::db()->getDataAccess());
        }
        
        public function log($message) {
            self::logVar($message);
        }
        
        public function logVar($variable) {
            $message = var_export($variable, true);
            $message = str_replace("<", "&lt;", $message);
            $message = str_replace(">", "&gt;", $message);
            $this->web()->PageLog .= "<pre>" . $message . "</pre>";
        }

        protected function addUrlParameter($url, $name, $value = '') {
            $pair = ($value != '') ? $name . '=' . $value : $name;
        
            $queryIndex = strpos($url, '?');
            if ($queryIndex == '') {
                $url .= '?' . $pair;
            } else {
                if (strpos($url, $name, $queryIndex) == '') {
                    $url .= '&' . $pair;
                } else {
                    $query = substr($url, $queryIndex + 1);
                    $query = explode('&', $query);
                    $url = substr($url, 0, $queryIndex);
                    $isFirst = true;
                    foreach ($query as $item) {
                        $keyvalue = explode('=', $item);
                        if ($keyvalue[0] == $name) {
                            $item = $pair;
                        }
        
                        if ($isFirst) {
                            $url .= '?' . $item;
                            $isFirst = false;
                        } else {
                            $url .= '&' . $item;
                        }
                    }
                }
            }

            return $url;
        }

        protected function removeUrlParameter($url, $name) {
            $queryIndex = strpos($url, '?');
            if ($queryIndex == '') {
                return $url;
            }

            if (strpos($url, $name, $queryIndex) == '') {
                return $url;
            }

            $query = substr($url, $queryIndex + 1);
            $query = explode('&', $query);
            $url = substr($url, 0, $queryIndex);
            $isFirst = true;
            foreach ($query as $item) {
                $keyvalue = explode('=', $item);
                if ($keyvalue[0] != $name) {
                    if ($isFirst) {
                        $url .= '?' . $item;
                        $isFirst = false;
                    } else {
                        $url .= '&' . $item;
                    }
                }
            }

            return $url;
        }
        
        protected function addUrlQueryString($url) {
            foreach ($_GET as $key => $value) {
                if($key != 'WEB_PAGE_PATH') {
                    $url = $this->addUrlParameter($url, $key, $value);
                }
            }

            return $url;
        }

        public function redirectUrlWithQueryString() {
            $actionUrl = $_SERVER['REQUEST_URI'];
            $actionUrl = $this->addUrlQueryString($actionUrl);
            return $actionUrl;
        }

        protected function removeQueryString($url) {
            $queryIndex = strpos($url, '?');
            return substr($url, 0, $queryIndex);
        }

        public function RedirectToUrl($url) {
            if ($url != '#') {
                header("Location: " . $url, true, 302);
                echo '<script type="text/javascript">window.location.href = "' . $url . '";</script>';
                echo '<a href="' . $url . '">Redirect to ' . $url . '</a>';
                exit;
            }
        }

        private $requestHeaders = null;

        public function requestHeaders() {
            if ($this->requestHeaders == null) {
                $this->requestHeaders = getallheaders();
            }

            return $this->requestHeaders;
        }

        public function removeDirectory($dir) {
            $files = array_diff(scandir($dir), array('.','..'));
            foreach ($files as $file) {
                $itemPath = $dir . "/" . $file;
                if (is_dir($itemPath)) {
                    self::removeDirectory($itemPath);
                } else {
                    unlink($itemPath); 
                }
            } 

            return rmdir($dir); 
        }

        public function zipExtract($zipFileName, $targetPath) {
            $zip = new ZipArchive();
            if ($zip->open($zipFileName) === TRUE) {
                $zip->extractTo($targetPath);
                $zip->close();

                return true;
            }

            return false;
        }
    }

?>
