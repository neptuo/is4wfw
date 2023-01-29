<?php

    require_once('System.class.php');
    require_once('FileAdmin.class.php');
    require_once(APP_SCRIPTS_PHP_PATH . "classes/dataaccess/SqlBuilder.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/EditModel.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/ExtensionParser.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/TemplateParser.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/ParsedTemplate.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/EditModel.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/FilterModel.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/ListModel.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/SortModel.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/PagingModel.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/MissingEditModelException.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/Stack.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/ParameterException.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/manager/SystemProperty.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/TemplateCacheKeys.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/utils/ArrayUtils.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/utils/FileUtils.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/utils/HttpUtils.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/utils/StringUtils.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/utils/UrlUtils.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/utils/ZipUtils.class.php");

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
         *  True, if no is used on page yet.     
         *
         */
        private $FirstFrame = true;

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
            $name = StringUtils::explode($name, ':');
            $name = $name[0];
            //$name = strtr($name, $escapeChars);
            $name = UrlUtils::toValidUrl($name);

            $value = $this->system()->getPropertyValue($name);
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
            if($this->system()->getPropertyValue('Frames.leaveOpened') == 'true') {
                $defaultClosed = false;
            }

            $addAttrs = "";
            if ($_REQUEST['__TEMPLATE'] == 'xml') {
                $props = $dbObject->fetchAll('SELECT `left`, `top`, `width`, `height`, `maximized` FROM `window_properties` WHERE `frame_id` = "' . $name . '" AND `user_id` = ' . $loginObject->getUserId() . ';');
                if (count($props) == 1) {
                    $addAttrs = 'left="' . $props[0]['left'] . '" top="' . $props[0]['top'] . '" width="' . $props[0]['width'] . '" height="' . $props[0]['height'] . '" maximized="' . ($props[0]['maximized'] ? "true" : "false") . '"';
                }
            }

            $return = ''
            . '<div id="' . $name . '" class="shadow frame frame-cover ' . $name . '' . ((strlen($classes)) ? ' ' . $classes : '') . (($defaultClosed || $closed) ? ' closed-frame' : '') . '"' . (($addAttrs != "") ? ' ' . $addAttrs : '') . '>'
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

        public function getTemplateById($templateId) {
            global $dbObject;
            global $loginObject;
            $templateContent = "";

            $keys = TemplateCacheKeys::template($templateId);
            $template = $this->getParsedTemplate($keys);
            if ($template != null) {
                return $template;
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

            $template = $this->parseTemplate($keys, $templateContent);
            return $template;
        }

        public static function autolib($prefix) {
            if (!BaseTagLib::php()->isRegistered($prefix)) {
                if (!BaseTagLib::php()->autoRegisterPrefix($prefix)) {
                    return null;
                }
            }
            
            $name = $prefix . 'Object';
            global ${$name};
            return ${$name};
        }

        public static function php() {
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

        public function ui() {
            global $uiObject;
            if ($uiObject == NULL) {
                $this->php()->autoRegisterPrefix("ui");
                global $uiObject;
            }
            
            return $uiObject;
        }
        
        public static function js() {
            global $jsObject;
            if ($jsObject == NULL) {
                return BaseTagLib::autolib('js');
            }

            return $jsObject;
        }

        /**
         * @return DataAccess
         */
        public function dataAccess() {
            return $this->db()->getDataAccess();
        }

        public function sql() {
            return new SqlBuilder($this->dataAccess());
        }

        public function login() {
            global $loginObject;
            return $loginObject;
        }

        public static function system() {
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

        protected function getUserProperty($name, $default = -1) {
            $value = $this->system()->getPropertyValue($name);
            if ($value == -1) {
                return $default;
            } else {
                return $value;
            }
        }

        public function getGroupPerm($name, $groupId, $inherited, $default = '') {
            //echo 'Name: '.$name.', GroupID: '.$groupId.', Inherited: '.($inherited ? 'true' : 'false').', Default: "'.$default.'"<br />';
            if ($groupId != 0) {
                $perms = $this->db()->fetchAll('select `name`, `value`, `type` from `group_perms` where `group_id` = ' . $groupId . ';');
                foreach ($perms as $perm) {
                    if ($name == $perm['name']) {
                        return $perm;
                    }
                }

                if ($inherited) {
                    $group = $this->db()->fetchSingle('select `parent_gid` from `group` where `gid` = ' . $groupId . ';');
                    if ($group != array()) {
                        return $this->getGroupPerm($name, $group['parent_gid'], true, $default);
                    }
                }
            }

            return array('value' => $default);
        }
        
        public function getSystemProperty($name) {
            $property = new SystemProperty($this->db()->getDataAccess());
            return $property->getValue($name);
        }
        
        public function setSystemProperty($name, $value) {
            $property = new SystemProperty($this->db()->getDataAccess());
            $property->setValue($name, $value);
        }
        
        private $LocalizationBundle;
        private $BundleName;
        private $BundleLang = 'cs';
        private $BundleIsSystem = true;
        private $BundleModuleId = "";
        
        public function setLocalizationBundle($name, $system = true, $moduleId = "") {
            $this->BundleName = $name;
            $this->BundleIsSystem = $system;
            $this->BundleModuleId = $moduleId;
        }
        
        public function rb($key = false) {
            if ($this->LocalizationBundle == null) {
                $this->LocalizationBundle = LocalizationBundle::create($this->BundleName, $this->web()->LanguageName, $this->BundleIsSystem, $this->BundleModuleId);
                
                if ($this->LocalizationBundle == null) {
                    $this->LocalizationBundle = LocalizationBundle::create($this->BundleName, $this->BundleLang, $this->BundleIsSystem, $this->BundleModuleId);
                }
            }

            if ($key == false) {
                return $this->LocalizationBundle;
            } else {
                if (!$this->LocalizationBundle) {
                    return $key;
                }

                return $this->LocalizationBundle->get($key);
            }
        }

        public function view($name, $data) {
            $parser = ExtensionParser::initialize($name, $this->rb(), $data);
            return $parser->parse();
        }

        public function partialView($path) {
            $content = file_get_contents(APP_SCRIPTS_PATH . 'views/templates/'.$path.'.view.php');
            $keys = array_merge(["partialviews"], explode("/", $path), [sha1($content)]);
            $template = $this->getParsedTemplate($keys);
            if ($template == null) {
                $template = $this->parseTemplate($keys, $content);
            }

            return $template();
        }
        
        private $daos = array();
        
        /**
         * @return AbstractDao
         */
        public function dao($name) {
            if(!array_key_exists($name, $this->daos)) {
                require_once(APP_SCRIPTS_PHP_PATH . 'classes/dataaccess/' . $name . 'Dao.class.php');
                $classname = $name.'Dao';
                $dao = new $classname;
                $dao->setDataAccess($this->db()->getDataAccess());
                $this->daos[$name] = $dao;
            }
            
            return $this->daos[$name];
        }

        public function select() {
            return Select::factory($this->db()->getDataAccess());
        }
        
        public function log($message) {
            $this->logVar($message);
        }
        
        public function logVar($variable) {
            $message = var_export($variable, true);
            $message = str_replace("<", "&lt;", $message);
            $message = str_replace(">", "&gt;", $message);
            $this->web()->PageLog .= "<pre>" . $message . "</pre>";
        }

        public function redirectUrlWithQueryString() {
            $actionUrl = $this->getSelfUrl();
            $actionUrl = UrlUtils::addCurrentQueryString($actionUrl);
            return $actionUrl;
        }

        public function redirectToSelf() {
            $this->redirectToUrl($this->getSelfUrl());
        }

        public function getSelfUrl() {
            return $_SERVER['REQUEST_URI'];
        }

        public function redirectToUrl($url) {
            if ($url != '#') {
                header("Location: " . $url, true, 302);
                echo '<script type="text/javascript">window.location.href = "' . $url . '";</script>';
                echo '<a href="' . $url . '">Redirect to ' . $url . '</a>';
                $this->close();
            }
        }

        protected function close() {
            $this->php()->dispose();
            exit;
        }

        private $requestHeaders = null;

        public function requestHeaders() {
            if ($this->requestHeaders == null) {
                $this->requestHeaders = getallheaders();
            }

            return $this->requestHeaders;
        }

        public function requestHeader($name) {
            $headers = $this->requestHeaders();
            if (array_key_exists($name, $headers)) {
                return $headers[$name];
            }

            return null;
        }
        
        public function debugFrame($title, $content, $contentTag = null, $contentWhiteSpacePre = true) {
            if ($contentTag !== null && $contentTag !== '') {
                $content = '<' . $contentTag . ' style="' . ($contentWhiteSpacePre ? 'white-space: pre;' : '') . '">' . $content . '</' . $contentTag . '>';
            }

            return ''
            . '<div style="border: 2px solid #666666; margin: 10px; padding: 10px; background: #eeeeee;">'
                . '<div style="color: red; font-weight: bold;">' . $title . ':</div>'
                . '<div style="color: black;">' . $content . '</div>'
            . '</div>';
        }

        private static $editModel;

        public function getEditModel($thowIfNull = true) : ?EditModel {
            if (BaseTagLib::$editModel != null) {
                return BaseTagLib::$editModel;
            }
            
            if ($thowIfNull) {
                throw new MissingEditModelException();
            } else {
                return null;
            }
        }
        
        public function setEditModel($model) {
            BaseTagLib::$editModel = $model;
        }

        public function clearEditModel(?EditModel $prevModel = null) {
            BaseTagLib::$editModel = $prevModel;
        }
        
        private $stacks;

        private function getLocalModelStack($key, $createIfNotExists = false) {
            if ($this->stacks == null) {
                if ($createIfNotExists) {
                    $this->stacks = array();
                } else {
                    return null;
                }
            }

            $stack = $this->stacks[$key];
			if ($stack == null && $createIfNotExists) {
                $stack = new Stack();
                $this->stacks[$key] = $stack;
            }
            
            return $stack;
        }

        public function pushListModel($model) {
            $stack = $this->getLocalModelStack("listModels", true);
			$stack->push($model);
        }

        public function peekListModel($createIfNotExists = true): ?ListModel {
            $model = null;
            $stack = $this->getLocalModelStack("listModels", false);
            if ($stack != null) {
                $model = $stack->peek();
            }

            if ($model == null && $createIfNotExists) {
                $model = new ListModel();
            }

            return $model;
        }

        public function popListModel() {
            $stack = $this->getLocalModelStack("listModels", false);
			if ($stack == NULL) {
                return new ListModel();
			}

            return $stack->pop();
        }

        public function hasListModel() {
            $stack = $this->getLocalModelStack("listModels", false);
			if ($stack == NULL) {
                return false;
			}

            return $stack->peek() != null;
        }

        public function executeTemplateContent(array $keys, string $content, ?string $relativeClassPathPrefix = null) {
            if (is_callable($content)) {
                return $content();
            }

            $template = $this->getParsedTemplate($keys);
            if ($template == null) {
                $template = $this->parseTemplate($keys, $content, $relativeClassPathPrefix);
            }

            return $template();
        }

        public function parseTemplate(array $keys, string $content, ?string $relativeClassPathPrefix = null) {
            if ($content instanceof ParsedTemplate) {
                return $content;
            }

            $parser = $this->createParser($relativeClassPathPrefix);
            $parsedTemplate = $parser->parse($content, $keys);
            return $parsedTemplate;
        }

        public function getParsedTemplate(array $keys) {
            $parser = $this->createParser();
            $parsedTemplate = $parser->run($keys);
            return $parsedTemplate;
        }

        public function createParser(?string $relativeClassPathPrefix = null) {
            return new TemplateParser(
                $this->php()->getCurrentRegistrations(), 
                $this->php()->getDefaultGlobalAttributes(),
                $relativeClassPathPrefix
            );
        }

        public function deleteParsedTemplate(array $keys, bool $isRecursive = false) {
            $parser = $this->createParser();
            $parser->getCache()->delete($keys, $isRecursive);
        }

		protected function joinAttributes($params) {
			$attributes = "";
			foreach ($params as $key => $value) {
                $value = htmlspecialchars($value);
				$attributes = StringUtils::join($attributes, "$key=\"$value\"", " ");
			}

			if (!empty($attributes)) {
				$attributes = " $attributes";
			}

			return $attributes;
        }

		protected function appendClass($attributes, $class) {
			if (array_key_exists("class", $attributes)) {
				$attributes["class"] .= " $class";
			} else {
				$attributes["class"] = $class;
			}

			return $attributes;
		}

        protected function isFilterModel($filter) {
            return count($filter) == 1 && array_key_exists("", $filter) && ($filter[""] instanceof FilterModel);
        }

        protected function isSortModel($sort) {
            return count($sort) == 1 && array_key_exists("", $sort) && ($sort[""] instanceof SortModel);
        }

        protected function unsetKeys($array, $toRemove) {
            for ($i = 0; $i < count($toRemove); $i++) { 
                unset($array[$toRemove[$i]]);
            }
        }
    }

?>
