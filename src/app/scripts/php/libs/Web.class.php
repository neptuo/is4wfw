<?php

    require_once("BaseTagLib.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/RoleHelper.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/RequestStorage.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/SessionStorage.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/QueryStorage.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/Diagnostics.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/UniversalPermission.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/UrlResolver.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/UrlCache.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/manager/WebForwardManager.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/manager/HttpClient.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/MissingEntrypointException.class.php");

    /**
     *
     *  Main web object.
     *  Take care of life-cycle of web application.
     *  Default object.
     *  
     *  @objectname webObject
     *  
     *  @author     Marek SMM
     *  @timestamp  2012-01-21
     *
     */
    class Web extends BaseTagLib {

        /**
         *
         *  Handle page output caching on/off.
         *     
         */
        private $IsCached = false;
        /**
         *
         * 	Path to cached file.
         *
         */
        private $CacheFile = "";
        /**
         *
         *  Handle page title.
         *
         */
        private $PageTitle = "";
        /**
         *
         *  Handle computed page head.
         *     
         */
        private $PageHead = "";
        private $PageHeadScripts = "";
        private $PageTailScripts = "";
        private $PageStyles = "";

        public function getPageHeadScripts() {
            return $this->PageHeadScripts;
        }

        public function getPageTailScripts() {
            return $this->PageTailScripts;
        }

        public function getPageStyles() {
            return $this->PageStyles;
        }
        
        private $ProjectContent;

        public $PageLog = "";
        /**
         *
         *  Handle computed page content.
         *
         */
        private $PageContent = "";
        /**
         *
         * 	Web project Id
         *
         */
        public $ProjectId = "";
        /**
         *
         * 	Http / Https
         *
         */
        public $Protocol = '';
        /**
         *
         *  Text file content when dynamic rewrite.
         *
         */
        private $TextFileId = 0;
        /**
         *
         *  Array to hold attributes values during callback.
         *
         */
        private $Attributes = array();
        /**
         *
         *  Holds requested path.
         *
         */
        private $Path = "";
        /**
         *
         *  Holds current path when dynamic rewriting.
         *
         */
        private $CurrentDynamicPath = "";
        /**
         *
         *  Holds language id.
         *
         */
        public $LanguageId = 0;
        public $LanguageName = '';
        /**
         *
         *  Holds parent page id to actually parsed page.
         *
         */
        private $ParentId = 0;
        /**
         *
         *  Holds current path while composeUrl.
         *
         */
        private $CurrentPath = "";
        private $TempLoadedContent = array();
        private $SelectedEntrypoint = null;
        private $CacheInfo = array();
        private $CacheTime = 10000000000;
        private $ServerName = '';
        private $Https = '';
        private $ProjectUrlDef = '';
        private $ProjectUrl = '';
        private $UrlDef = '';
        private $Url = '';
        /**
         *
         *  Regular expression for parsing c tag.     
         *
         */
        protected $TAG_RE = '(<([a-zA-Z0-9]+:[a-zA-Z0-9]+)( )+((([a-zA-Z0-9-]+[:]?[a-zA-Z0-9-]*)="[^"]*"( )*)*)\/>)';
        private $PROP_RE = '(([a-zA-Z0-9-_]+:[a-zA-Z0-9-_.]+))';
        private $PropertyUse = '';
        private $PropertyAttr = '';
        /**
         *
         *  Regular expression for parsing attribute.
         *
         */
        private $PagesId = array();
        private $PagesIdIndex = 0;
        private $ParsingPages = false;
        private $CurrentPageTimestamp = 0;
        private $ChildPageId = 0;
        public $Diagnostics;
        private $ZipOutput = true;
        private $UrlResolver;
        private $UrlCache;
        private $FullUrl;
        private $IsSubstituting = false;
        
        /**
         *
         * 	Keywords.
         *
         */
        private $keywords = '';

        private $Doctype = 'xhtml';
        private $ContentType = 'text/html';
        private $Template = null;
        private $StartPageId = null;
        private $ParentPageId = null;
        

        private $switchConditionWhenStack = array();
        private $switchConditionCaseStack = array();

        /**
         *
         *  Initializes object.
         *
         */
        public function __construct() {
            $this->PageTitle = $_SERVER['HTTP_HOST'];
            $this->ServerName = $_SERVER['SERVER_NAME'];
            $this->Https = $_SERVER['HTTPS'];

            global $requestStorage;
            $requestStorage = new RequestStorage();
            global $sessionStorage;
            $sessionStorage = new SessionStorage();
            global $queryStorage;
            $queryStorage = new QueryStorage();

            $this->Diagnostics = new Diagnostics();

            if (array_key_exists('__TEMPLATE', $_REQUEST)) {
                $this->Template = strtolower($_REQUEST['__TEMPLATE']);
            } else {
                $headers = $this->requestHeaders();
                if(array_key_exists('X-Template', $headers)) {
                    $this->Template = strtolower($headers['X-Template']);
                }
            }
            
            if (array_key_exists('__START_ID', $_REQUEST)) {
                $this->StartPageId = strtolower($_REQUEST['__START_ID']);
            } else {
                $headers = $this->requestHeaders();
                if (array_key_exists('X-Parent-Page-Id', $headers)) {
                    $this->ParentPageId = strtolower($headers['X-Parent-Page-Id']);
                }
            }
        }

        public function isXmlTemplate() {
            return $this->Template == "xml";
        }

        public function isZipOutput() {
            return $this->ZipOutput;
        }

        public function setZipOutput($val) {
            if ($val == false) {
                $this->ZipOutput = false;
            } else {
                $this->ZipOutput = true;
            }
        }

        public function getVirtualUrl() {
            return $_REQUEST['WEB_PAGE_PATH'];
        }

        public function processRequestNG() {
            $boundaryName = "processRequest";

            $error = $this->autolib("error");
            $error->boundary(
                function() { 
                    $this->processRequestExecute();
                }, 
                $boundaryName
            );
            if ($error->isFailed($boundaryName)[PhpRuntime::$DecoratorExecuteName]) {
                $detail = "";
                if ($this->getDebugMode()) {
                    $exceptions = $error->getExceptionsForBoundary($boundaryName);
                    foreach ($exceptions as $ex) {
                        $detail .= $this->autolib("log")->getDebugExceptionView($ex, [], false);
                    }
                }

                $this->generateErrorPage('500', $detail);
            }
        }

        // Call processRequestNG to ensure error boundary.
        private function processRequestExecute() {
            if (IS_DEVELOPMENT_MODE) {
                $this->debugMode(true);
            }

            if ($this->getDebugMode()) {
                if (array_key_exists('query-list', $_GET)) {
                    parent::db()->getDataAccess()->saveProfiles(true);
                    parent::db()->getDataAccess()->saveQueries(true);
                    parent::db()->getDataAccess()->saveMeasures(true);
                }

                if (array_key_exists('parser-stats', $_GET)) {
                    TemplateParser::saveMeasures(true);
                }
            }

            if ($this->runHook(WebHook::ProcessRequestBeforeCms, [])) {
                return;
            }

            $this->UrlResolver = new UrlResolver();
            $this->UrlCache = new UrlCache();

            $found = false;
            $domainUrl = $_SERVER['HTTP_HOST'];

            $rootUrl = INSTANCE_URL;
            $virtualUrl = $this->getVirtualUrl();
            $fullUrl = UrlResolver::combinePath($domainUrl, UrlResolver::combinePath($rootUrl, $virtualUrl));

            $item = $this->UrlCache->read($fullUrl);
            
            $this->ensureProtocol();

            $this->FullUrl = $fullUrl;
            
            // Projit Forwardy s Always
            $this->processForwards($this->findForward(array('Always')), UrlResolver::combinePath($this->Protocol, $fullUrl, '://'));

            if ($item != array()) {
                if ($item["language_id"] != 0) {
                    // Stranka jiz je v urlcache
                    $this->UrlResolver->setPagesId($this->parsePagesId($item['pages_id']));
                    $this->UrlResolver->selectLanguage($item['language_id']);
                } else {
                    if (!empty($item["pages_id"])) {
                        $this->SelectedEntrypoint = $item["pages_id"];
                    }
                }

                $this->UrlResolver->selectProjectById($item);
                $this->ProjectContent = $item["project_content"];

                if ($item['cachetime'] != -1) {
                    // Pouzijeme cache
                    $this->CacheFile = sha1($this->FullUrl).'.cache.html';
                    $cacheUsed = false;
                    if ($item['cachetime'] == 0 || $item['cachetime'] + $item['lastcache'] >= time()) {
                        if (file_exists(CACHE_PAGES_PATH . $this->CacheFile)) {
                            $cacheUsed = true;
                            $this->tryToComprimeContent(file_get_contents(CACHE_PAGES_PATH . $this->CacheFile));
                        }
                    } 

                    if (!$cacheUsed) {
                        $this->IsCached = true;
                        $this->CacheInfo = $item;
                        $this->UrlCache->updateLastCache($fullUrl);
                    }
                }

                $this->loadPageData();
                $found = $this->parseSingleUrlParts($domainUrl, $rootUrl, $virtualUrl);
            } else {
                //echo $domainUrl.', '.$rootUrl.', '.$virtualUrl.'<br />';
                if ($this->UrlResolver->resolveUrl($domainUrl, $rootUrl, $virtualUrl)) {
                    // Stranka existuje

                    $this->loadPageData();

                    $this->ProjectContent = $this->dataAccess()->fetchScalar($this->sql()->select("web_project", ["content"], ["id" => $this->UrlResolver->getWebProjectId()]));

                    // Ulozit do urlcache
                    $this->UrlCache->write($fullUrl, $this->UrlResolver->getWebProject(), $this->SelectedEntrypoint ?? $this->pagesIdAsString('-'), $this->UrlResolver->getLanguage(), $this->findCachetime());
                    $found = true;
                }
            }

            if ($found) {
                $this->doOldSetup();
                $this->PageContent = $this->getProjectContent();
                $this->flush();
            } else {
                // Stranka neexistuje -> Projit Forwardy s 404 nebo All Errors
                $this->generateErrorPage('404');
            }
        }

        private function ensureProtocol() {
            if (empty($this->Protocol)) {
                if ($_SERVER['HTTPS'] == "on") {
                    $this->Protocol = 'https';
                } else {
                    $this->Protocol = 'http';
                }
            }
        }

        public function substituteRequestFor($pageId, $langId) {
            $this->IsSubstituting = true;

            $pageUrl = $this->composeUrl($pageId, $langId, false);
            $url = parent::db()->fetchSingle('select `http`, `https`, `domain_url`, `root_url`, `virtual_url` from `web_url` join `page` on `web_url`.`project_id` = `page`.`wp` where `page`.`id` = '.$pageId.' order by `web_url`.`default` desc, `web_url`.`id`;');
            
            if (strpos($pageUrl, 'http://') != -1 || strpos($pageUrl, 'https://') != -1) {
                $pageUrl = substr($pageUrl, strpos($pageUrl, '/', 8), strlen($pageUrl));
            }

            $indexOfQuery = strpos($pageUrl, '?');
            if ($indexOfQuery !== false) {
                $pageUrl = substr($pageUrl, 0, $indexOfQuery);
            }

            $scriptUrl = UrlResolver::combinePath($url['root_url'], '/index.php');

            if ($url['domain_url'] != "*") {
                $_SERVER['HTTP_HOST'] = $url['domain_url'];
            }

            $_SERVER['SCRIPT_NAME'] = $scriptUrl;
            $_REQUEST['WEB_PAGE_PATH'] = $pageUrl;

            if ($this->Protocol == 'https' && $url['https'] == 1) {
                $_SERVER['https'] = 'on';
            }

            $this->processRequestNG();
        }

        private function loadPageData() {
            $wp = $this->UrlResolver->getWebProject();
            if (empty($wp["entrypoint"]) && !$wp["pageless"]) {
                $selectSql = ""
                    . "SELECT p.`id`, i.`name`, i.`href`, i.`in_title`, i.`keywords`, i.`title`, i.`timestamp`, i.`cachetime`, "
                    . "IFNULL(c.`tag_lib_start`, cd.`tag_lib_start`) AS `tag_lib_start`, IFNULL(c.`tag_lib_end`, cd.`tag_lib_end`) AS `tag_lib_end`, IFNULL(c.`head`, cd.`head`) AS `head`, IFNULL(c.`content`, cd.`content`) AS `content` "
                    . "FROM `page` p "
                        . "LEFT JOIN `info` i ON p.`id` = i.`page_id` "
                        . "LEFT JOIN `content` c ON p.`id` = c.`page_id` AND c.`language_id` = " . $this->UrlResolver->getLanguageId() . " " 
                        . "LEFT JOIN `content` cd ON p.`id` = cd.`page_id` AND cd.`language_id` = (SELECT `id` FROM `language` WHERE `language` = '') " 
                    . "WHERE i.`is_visible` = 1 "
                        . "AND i.`language_id` = " . $this->UrlResolver->getLanguageId() . " "
                        . "AND p.`id` IN (" . $this->pagesIdAsString() . ") "
                        . "AND p.`wp` = " . $this->UrlResolver->getWebProjectId() . ";";
                
                $this->TempLoadedContent = $this->sortPages(parent::db()->fetchAll($selectSql), $this->UrlResolver->getPagesId());
            } else if (!empty($wp["entrypoint"])) {
                $this->SelectedEntrypoint = $wp["entrypoint"];
            }
        }

        private function parsePagesId($item) {
            return StringUtils::explode($item, '-');
        }

        private function pagesIdAsString($delim = ', ') {
            $ret = '';
            foreach ($this->UrlResolver->getPagesId() as $page) {
                if ($ret != '') {
                    $ret .= $delim;
                }
                $ret .= $page;
            }
            return $ret;
        }

        private function findCachetime() {
            $cachetime = 10000000000;
            if (!empty($this->SelectedEntrypoint) || empty($this->TempLoadedContent)) {
                return -1;
            }

            foreach ($this->TempLoadedContent as $page) {
                //echo $cachetime . '<br />';
                if ($cachetime != -1 && ($page['cachetime'] < $cachetime || $cachetime == 0)) {
                    if ($page['cachetime'] != 0) {
                        $cachetime = $page['cachetime'];
                    } elseif ($cachetime == 10000000000) {
                        $cachetime = $page['cachetime'];
                    }
                }
            }
            return $cachetime;
        }

        private function parseSingleUrlParts($domainUrl, $rootUrl, $virtualUrl) {
            $webProject = $this->UrlResolver->getWebProject();
            $lang = $this->UrlResolver->getLanguage();

            $reqDoms = StringUtils::explode($domainUrl, '.');
            $prjDoms = StringUtils::explode($webProject['alias']['domain_url'], '.');
            foreach ($prjDoms as $key => $part) {
                $this->UrlResolver->parseSingleUrlPart($part, $reqDoms[$key]);
            }

            $reqRoots = StringUtils::explode($rootUrl, '/');
            $prjRoots = StringUtils::explode($webProject['alias']['root_url'], '/');
            foreach ($prjRoots as $key => $part) {
                $this->UrlResolver->parseSingleUrlPart($part, $reqRoots[$key]);
            }

            //echo $virtualUrl;
            $reqVirs = StringUtils::explode($virtualUrl, '/');
            $prjVirs = $this->prepareVirtualPathAsArray($webProject, $lang);
            $this->parseAllPagesTagLib('tag_lib_start');
            $lastIndex = 0;
            foreach ($prjVirs as $key => $prj) {
                $vir = $reqVirs[$key];
                $output = $this->UrlResolver->parseSingleUrlPart($prj, $vir);
                if ($output != $vir) {
                    // parent::log('URL: NotFound -> '.$vir.' : '.$prj.'<br />');
                    return false;
                }

                $lastIndex = $key;
            }
            $this->parseAllPagesTagLib('tag_lib_end');

            
            return true;
        }

        public function getAllPagesRelativePath() {
            $virtualUrl = $this->getVirtualUrl();
            $webProject = $this->UrlResolver->getWebProject();

            $reqVirs = StringUtils::explode($virtualUrl, '/');
            $prjVirs = $this->prepareVirtualPathAsArray($webProject, ["language" => ""]);
            
            $trailing = [];
            $lastIndex = count($prjVirs);
            if (count($reqVirs) > $lastIndex) {
                for ($i = $lastIndex; $i < count($reqVirs); $i++) { 
                    $trailing[] = $reqVirs[$i];
                }
            }

            return implode("/", $trailing);
        }

        private function prepareVirtualPathAsArray($webProject, $lang) {
            $pages = array();
            foreach ($this->TempLoadedContent as $page) {
                if ($page['href'] != '') {
                    $pages = array_merge($pages, StringUtils::explode($page['href'], '/'));
                }
            }
            if (strlen($lang['language']) > 0) {
                $pages = array_merge(array($lang['language']), $pages);
            }
            $virUrl = StringUtils::explode($webProject['alias']['virtual_url'], '/');
            if(count($virUrl) == 1 && $virUrl[0] == '') {
                return $pages;
            }
            return array_merge($virUrl, $pages);
        }

        private function parseAllPagesTagLib($tl) {
            $langId = $this->LanguageId;
            if ($langId == 0) {
                $langId = $this->UrlResolver->getLanguage()["id"];
            }

            foreach ($this->TempLoadedContent as $page) {
                $this->executeTemplateContent(TemplateCacheKeys::page($page["id"], $langId, $tl), $page[$tl]);
            }
        }

        private function doOldSetup() {
            $webProject = $this->UrlResolver->getWebProject();
            $this->ProjectId = $webProject['id'];

            $language = $this->UrlResolver->getLanguage();
            $this->LanguageId = $language['id'];
            $this->LanguageName = $language['language'];

            $this->PagesId = $this->UrlResolver->getPagesId();

            if ($this->StartPageId != null && in_array($this->StartPageId, $this->PagesId)) {
                for ($i = 0; $i < count($this->PagesId); $i++) {
                    if ($this->PagesId[$i] == $this->StartPageId) {
                        $this->PagesIdIndex = $i;
                        break;
                    }
                }
            } else if($this->ParentPageId != null && in_array($this->ParentPageId, $this->PagesId)) {
                $isNext = false;
                for ($i = 0; $i < count($this->PagesId); $i++) {
                    if ($this->PagesId[$i] == $this->ParentPageId) {
                        $isNext = true;
                    } else if($isNext) {
                        $this->PagesIdIndex = $i;
                        break;
                    }
                }
            }

            if (array_key_exists('SHOW_CHANGES_ONLY', $_REQUEST) && $_REQUEST['SHOW_CHANGES_ONLY'] == 'true') {
                for ($i = 0; $i < min(count($_SESSION['last-request']['pages-id']), count($this->PagesId)); $i++) {
                    if ($_SESSION['last-request']['pages-id'][$i] != $this->PagesId[$i]) {
                        $this->PagesIdIndex = $i;
                        break;
                    }
                }
                if ($this->PagesIdIndex == 0) {
                    $this->PagesIdIndex = $i - 1;
                }
            }
        }

        private function findForward($conditions) {
            $sqlconds = array('condition' => $conditions, 'enabled' => 1);
            $order = array('order', 'id');
            return WebForwardManager::getBy($sqlconds, $order);
        }

        private function processForwards($forwards, $fullUrl) {
            foreach($forwards as $forward) {
                $rule = '/'.  str_replace('/', '\/', $forward->getRule()).'/';
                $match = preg_match($rule, $fullUrl);
                // $this->log(array('rule' => $rule, 'url' => $fullUrl, 'match' => $match));
                if ($match > 0) {
                    // Presmerovat
                    if ($forward->getType() == 'Substitute' && !$this->IsSubstituting) {
                        $this->substituteRequestFor($forward->getPageId(), $forward->getLangId());
                        return true;
                    } elseif($forward->getType() == 'Forward') {
                        $this->redirectTo($forward->getPageId(), $forward->getLangId());
                    }
                }
            }

            return false;
        }

        /**
         *
         *  Loads page content.
         *  
         *  @param  path  requested path
         *  @return returns success, true if file exists          
         *
         * 	DEPRECATED !!!
         *
         */
        public function processRequest() {
            global $phpObject;
            global $dbObject;

            if (array_key_exists("WEB_PAGE_PATH", $_REQUEST)) {
                $this->Path = $_REQUEST['WEB_PAGE_PATH'];
            } else {
                $error = "Can't load this page!";
                echo "<h1 clas=\"error\">" . $error . "</h1>";
                trigger_error($error, E_USER_ERROR);
            }

            $domainUrl = $this->ServerName;
            $domainProtocol = $this->Https;
            $otherProtocol = '';

            if ($domainProtocol == "on") {
                $domainProtocol = "https";
                $otherProtocol = "http";
            } else {
                $domainProtocol = "http";
                $otherProtocol = "https";
            }
            $this->Protocol = $domainProtocol;

            // Test url cache ...
            $ucache = $dbObject->fetchAll('SELECT `id`, `project_url_def`, `project_url`, `url_def`, `url`, `page-ids`, `cachetime`, `lastcache`, `language_id`, `wp` FROM `urlcache` WHERE CONCAT(`project_url`, "/", `url`) = "' . $this->ServerName . '/' . $this->Path . '";', true, true, true);
            if (!WEB_USE_URLCACHE || count($ucache) == 0) {
                $dbProject = $dbObject->fetchAll('SELECT `id`, `http`, `https`, `url` FROM `web_project`;');
                $ok = false;
                $prj_add_url = '';
                foreach ($dbProject as $i => $project) {
                    $ok = true;
                    $prj_add_url = '';
                    $parsed_url = StringUtils::explode($project['url'], '/', 1);
                    $project['url'] = $parsed_url[0];
                    $temp_url = StringUtils::explode($project['url'], '.');
                    $temp_req = StringUtils::explode($domainUrl, '.', 1);
                    for ($j = 0; $j < count($temp_url); $j++) {
                        $this->PropertyAttr = $temp_req[0];
                        $this->PropertyUse = 'set';
                        $temp_url_rrc = preg_replace_callback($this->PROP_RE, array(&$this, 'parsecproperty'), $temp_url[$j]);
                        if ($temp_url_rrc == $temp_req[0]) {
                            $temp_req = StringUtils::explode($temp_req[1], '.', 1);
                        } else {
                            $ok = false;
                        }
                    }

                    $path = StringUtils::explode($this->Path, '/', 1);
                    if ($ok && $parsed_url[1] != '') {
                        $temp_path = StringUtils::explode($parsed_url[1], '/');
                        for ($j = 0; $j < count($temp_path); $j++) {
                            $this->PropertyAttr = $path[0];
                            $this->PropertyUse = 'set';
                            $temp_path_rrc = preg_replace_callback($this->PROP_RE, array(&$this, 'parsecproperty'), $temp_path[$j]);
                            if ($temp_path_rrc == $path[0]) {
                                $prj_add_url .= '/' . $temp_path_rrc;
                                $path = StringUtils::explode($path[1], '/', 1);
                            } else {
                                $ok = false;
                                break;
                            }
                        }
                    }

                    if ($ok) {
                        if ($dbProject[$i][$domainProtocol] == 1) {
                            $this->ProjectId = $dbProject[$i]['id'];
                            $this->ProjectUrlDef = $parsed_url[0] . '/' . $parsed_url[1];
                            $this->ProjectUrl = $this->ServerName . $prj_add_url;
                            break;
                        } elseif ($dbProject[$i][$otherProtocol] == 1) {
                            parent::redirectToUrl($otherProtocol . '://' . $domainUrl . '/' . $this->Path);
                        } else {
                            $ok = false;
                        }
                    }
                }

                if ($ok == false) {
                    $dbProject = $dbObject->fetchAll('SELECT `id`, `project_id`, `http`, `https`, `url` FROM `web_alias`;', true, true, true);
                    foreach ($dbProject as $i => $project) {
                        $ok = true;
                        $prj_add_url = '';
                        $parsed_url = StringUtils::explode($project['url'], '/', 1);
                        $project['url'] = $parsed_url[0];
                        echo $project['url'] . '<br />';
                        $temp_url = StringUtils::explode($project['url'], '.');
                        $temp_req = StringUtils::explode($domainUrl, '.', 1);
                        for ($j = 0; $j < count($temp_url); $j++) {
                            $this->PropertyAttr = $temp_req[0];
                            $this->PropertyUse = 'set';
                            $temp_url_rrc = preg_replace_callback($this->PROP_RE, array(&$this, 'parsecproperty'), $temp_url[$j]);
                            echo $temp_url_rrc . " == " . $temp_req[0] . "<br />";
                            if ($temp_url_rrc == $temp_req[0]) {
                                $ok = true;
                                $temp_req = StringUtils::explode($temp_req[1], '.', 1);
                            } else {
                                $ok = false;
                                break;
                            }
                        }
                        echo "<br /><br />";

                        echo $this->Path . '<br />';
                        $path = StringUtils::explode($this->Path, '/', 1);
                        if ($ok && $parsed_url[1] != '') {
                            $temp_path = StringUtils::explode($parsed_url[1], '/');
                            for ($j = 0; $j < count($temp_path); $j++) {
                                $this->PropertyAttr = $path[0];
                                $this->PropertyUse = 'set';
                                $temp_path_rrc = preg_replace_callback($this->PROP_RE, array(&$this, 'parsecproperty'), $temp_path[$j]);
                                echo $temp_path_rrc . " == " . $path[0] . '<br />';
                                if ($temp_path_rrc == $path[0]) {
                                    $prj_add_url .= '/' . $temp_path_rrc;
                                    $path = StringUtils::explode($path[1], '/', 1);
                                } else {
                                    $ok = false;
                                    break;
                                }
                            }
                        }
                        echo "<br /><br />";

                        if ($ok) {
                            if ($dbProject[$i][$domainProtocol] == 1) {
                                $this->ProjectId = $dbProject[$i]['project_id'];
                                $this->ProjectUrlDef = $parsed_url[0] . '/' . $parsed_url[1];
                                $this->ProjectUrl = $this->ServerName . $prj_add_url;
                                break;
                            } elseif ($dbProject[$i][$otherProtocol] == 1) {
                                parent::redirectToUrl($otherProtocol . '://' . $domainUrl . '/' . $this->Path);
                            } else {
                                $ok = false;
                            }
                        }
                    }
                }

                if ($ok == false) {
                    header("HTTP/1.1 404 Not Found");
                    echo '<h1 class="error">Error 404</h1><p class="error">Requested page doesn\'t exists.</p>';
                    parent::close();
                }

                if (strlen($path[1]) != 0) {
                    $this->Url = $path[0] . '/' . $path[1];
                } else {
                    $this->Url = $path[0];
                }

                // Parsovat tag_lib_start + end!!!!!!!!!!!!!
                $return = $dbObject->fetchAll("SELECT `id` FROM `language` WHERE `language` = \"" . $path[0] . "\";");
                if (count($return) == 1) {
                    $this->LanguageName = $path[0];
                    $this->LanguageId = $return[0]['id'];
                    $this->Path = $path[1];
                } else {
                    $return = $dbObject->fetchAll("SELECT `id` FROM `language` WHERE `language` = \"\";");
                    if (count($return) == 1) {
                        $this->LanguageId = $return[0]['id'];
                        if ($path[1] != '') {
                            $this->Path = $path[0] . "/" . $path[1];
                        } else {
                            $this->Path = $path[0];
                        }
                    } else {
                        $error = "Sorry, but this language version doesn't exists!";
                        echo "<h4 clas=\"error\">" . $error . "</h4>";
                        trigger_error($error, E_USER_ERROR);
                    }
                }

                $this->parsePages($this->Path, 0);

                $pcache = '';
                for ($i = 0; $i < count($this->PagesId); $i++) {
                    $pcache .= $this->PagesId[$i];
                    if ($i < (count($this->PagesId) - 1)) {
                        $pcache .= '-';
                    }
                }

                $dbObject->execute('INSERT INTO `urlcache` (`project_url_def`, `project_url`, `url_def`, `url`, `page-ids`, `language_id`, `wp`, `lastcache`, `cachetime`) VALUES ("' . $this->ProjectUrlDef . '", "' . $this->ProjectUrl . '", "' . $this->UrlDef . '", "' . $this->Url . '", "' . $pcache . '", ' . $this->LanguageId . ', ' . $this->ProjectId . ', 0, ' . $this->CacheTime . ');');
                $oldCacheFile = "cache/pages/page-" . $ucache[0]['page-ids'] . ".cache.html";
                if (is_file($oldCacheFile)) {
                    unlink($oldCacheFile);
                }
            } else {
                // Setup globals ....
                $this->ProjectId = $ucache[0]['wp'];
                $this->ProjectUrlDef = $ucache[0]['project_url_def'];
                $this->ProjectUrl = $ucache[0]['project_url'];
                $this->UrlDef = $ucache[0]['url_def'];
                $this->Url = $ucache[0]['url'];
                $this->LanguageId = $ucache[0]['language_id'];
                $name = $dbObject->fetchAll("select `language` from `language` where `id` = " . $this->LanguageId . ";");
                $this->LanguageName = $name[0]['language'];

                $path = StringUtils::explode($this->Path, '/', 1);
                if ($ok && $parsed_url[1] != '') {
                    $temp_path = StringUtils::explode($parsed_url[1], '/');
                    for ($j = 0; $j < count($temp_path); $j++) {
                        $this->PropertyAttr = $path[0];
                        $this->PropertyUse = 'set';
                        $temp_path_rrc = preg_replace_callback($this->PROP_RE, array(&$this, 'parsecproperty'), $temp_path[$j]);
                        $path = StringUtils::explode($path[1], '/', 1);
                    }
                }

                // Old code ...
                $this->CacheInfo['id'] = $ucache[0]['id'];
                $this->CacheInfo['cachetime'] = $ucache[0]['cachetime'];
                $this->CacheInfo['lastcache'] = $ucache[0]['lastcache'];
                $this->CacheInfo['path'] = "cache/pages/page-" . $ucache[0]['page-ids'] . ".cache.html";
                if ($ucache[0]['cachetime'] != -1 && ($ucache[0]['cachetime'] == 0 || $ucache[0]['lastcache'] > time()) && is_file($this->CacheInfo['path'])) {
                    $this->tryToComprimeContent(file_get_contents($this->CacheInfo['path']));
                } else {
                    $pages = $ucache[0]['page-ids'];
                    $this->PagesId = StringUtils::explode($pages, '-');
                }
            }

            $str = '';
            for ($i = 0; $i < count($this->PagesId); $i++) {
                $str .= $this->PagesId[$i];
                if ($i < (count($this->PagesId) - 1)) {
                    $str .= ', ';
                }
            }

            if ($this->StartPageId != null && in_array($this->StartPageId, $this->PagesId)) {
                for ($i = 0; $i < count($this->PagesId); $i++) {
                    if ($this->PagesId[$i] == $this->StartPageId) {
                        $this->PagesIdIndex = $i;
                        break;
                    }
                }
            } else if($this->ParentPageId != null && in_array($this->ParentPageId, $this->PagesId)) {
                $isNext = false;
                for ($i = 0; $i < count($this->PagesId); $i++) {
                    if ($this->PagesId[$i] == $this->ParentPageId) {
                        $isNext = true;
                    } else if($isNext) {
                        $this->PagesIdIndex = $i;
                        break;
                    }
                }
            }

            if (array_key_exists('SHOW_CHANGES_ONLY', $_REQUEST) && $_REQUEST['SHOW_CHANGES_ONLY'] == 'true') {
                for ($i = 0; $i < min(count($_SESSION['last-request']['pages-id']), count($this->PagesId)); $i++) {
                    if ($_SESSION['last-request']['pages-id'][$i] != $this->PagesId[$i]) {
                        $this->PagesIdIndex = $i;
                        break;
                    }
                }
                if ($this->PagesIdIndex == 0) {
                    $this->PagesIdIndex = $i - 1;
                }
            }

            $this->TempLoadedContent = $this->sortPages($dbObject->fetchAll("SELECT `id`, `name`, `href`, `in_title`, `keywords`, `title`, `tag_lib_start`, `tag_lib_end`, `head`, `content`, `info`.`timestamp` FROM `content` LEFT JOIN `page` ON `content`.`page_id` = `page`.`id` LEFT JOIN `info` ON `content`.`page_id` = `info`.`page_id` AND `content`.`language_id` = `info`.`language_id` WHERE `info`.`is_visible` = 1 AND `info`.`language_id` = " . $this->LanguageId . " AND `page`.`id` IN (" . $str . ") AND `page`.`wp` = " . $this->ProjectId . ";"), $this->PagesId);
            $this->CurrentPageTimestamp = $this->TempLoadedContent[count($this->TempLoadedContent) - 1]['timestamp'];
            if (count($this->TempLoadedContent) == count($this->PagesId)) {
                $this->parseAllPagesTagLib("tag_lib_start");

                // Parse domain for setup dynamic properties
                $temp_req = StringUtils::explode($this->ProjectUrl, '.', 1);
                $prj_add_url = '';
                $parsed_url = StringUtils::explode($this->ProjectUrlDef, '/', 1);
                $project['url'] = $parsed_url[0];
                $temp_url = StringUtils::explode($project['url'], '.');
                for ($j = 0; $j < count($temp_url); $j++) {
                    $this->PropertyAttr = $temp_req[0];
                    $this->PropertyUse = 'set';
                    $temp_url_rrc = preg_replace_callback($this->PROP_RE, array(&$this, 'parsecproperty'), $temp_url[$j]);
                    if ($temp_url_rrc == $temp_req[0]) {
                        $temp_req = StringUtils::explode($temp_req[1], '.', 1);
                    } else {
                        $ok = false;
                    }
                }

                // Kvuli jazyku s neprazdnou url!!!
                if ($this->LanguageId != 1) {
                    $this->Url = StringUtils::explode($this->Url, '/', 1);
                    $this->Url = $this->Url[1];
                }

                // Parse url for setup dynamic properties
                $path = StringUtils::explode($this->Url, '/', 1);
                $temp_path = StringUtils::explode($this->UrlDef, '/');
                for ($j = 0; $j < count($temp_path); $j++) {
                    $this->PropertyAttr = $path[0];
                    $this->PropertyUse = 'set';
                    $temp_path_rrc = preg_replace_callback($this->PROP_RE, array(&$this, 'parsecproperty'), $temp_path[$j]);
                    $path = StringUtils::explode($path[1], '/', 1);
                }

                $this->parseAllPagesTagLib("tag_lib_end");

                $this->PageContent = $this->getContent();
            } else {
                $this->generateErrorPage('404');
            }

            $this->flush();
        }

        /**
         *
         * 	Sorts pages from sql select return to order defined in order.
         *
         * 	@param		pages						pages data
         * 	@param		order						array to sort data by
         * 	@return		sort data
         *
         */
        public function sortPages($pages, $order) {
            $return = array();
            for ($i = 0; $i < count($order); $i++) {
                foreach ($pages as $pg) {
                    if ($pg['id'] == $order[$i]) {
                        $return[$i] = $pg;
                        break;
                    }
                }
            }

            return $return;
        }

        /**
         *
         * 	Parse path to array of page id's.
         * 	
         * 	@param		path			path
         * 	@paeam		parentId	parent page id
         * 	@return		none     
         *
         * 	DEPRECATED !!!
         *
         */
        private function parsePages($path, $parentId) {
            global $phpObject;
            global $dbObject;
            global $loginObject;

            $path = StringUtils::explode($path, '/', 1);
            $return = $dbObject->fetchAll("SELECT `info`.`page_id`, `info`.`href` , `info`.`cachetime`, `content`.`tag_lib_start`, `content`.`tag_lib_end` FROM `info` LEFT JOIN `page` ON `info`.`page_id` = `page`.`id` LEFT JOIN `content` ON `info`.`page_id` = `content`.`page_id` AND `info`.`language_id` = `content`.`language_id` WHERE `page`.`parent_id` = " . $parentId . " AND `info`.`language_id` = " . $this->LanguageId . " AND `page`.`wp` = " . $this->ProjectId . " ORDER BY `info`.`href` DESC;");
            //echo $path[0];

            $this->CurrentDynamicPath = $path[0];
            $this->ParsingPages = true;
            if (count($return) == 0 && ($path[0] != "" || $path[1] != "")) {
                if ($_REQUEST['temp-stop'] != 'stop') {
                    //echo 'Generate err page!';
                    $_REQUEST['temp-stop'] = 'stop';
                    $this->generateErrorPage('404');
                } else {
                    echo 'Bad!';
                    parent::close();
                }
            } elseif (count($return) == 0 && $path[0] == "" && $path[1] == "") {
                if (count($this->PagesId) == 0) {
                    $this->generateErrorPage('404');
                } else {
                    return;
                }
            } else {
                $pathCache = $path;
                for ($i = 0; $i < count($return); $i++) {
                    $ok = true;
                    $temp_path = StringUtils::explode($return[$i]['href'], '/');
                    for ($j = 0; $j < count($temp_path); $j++) {
                        $this->PropertyAttr = $path[0];
                        $this->PropertyUse = 'set';
                        $temp_path_rrc = preg_replace_callback($this->PROP_RE, array(&$this, 'parsecproperty'), $temp_path[$j]);
                        if ($temp_path_rrc == $path[0]) {
                            if (strlen($temp_path[$j]) != 0) {
                                if (strlen($this->UrlDef) == 0) {
                                    $this->UrlDef .= $temp_path[$j];
                                } else {
                                    $this->UrlDef .= '/' . $temp_path[$j];
                                }
                            }
                            //echo ' ( '.$path[1].' ) ';
                            $path = StringUtils::explode($path[1], '/', 1);
                        } else {
                            $path = $pathCache;
                            $ok = false;
                            break;
                        }
                    }

                    if ($ok/* $tmp_path == $path[0] */) {
                        $this->ParsingPages = true;
                        $this->executeTemplateContent(TemplateCacheKeys::page($return[$i]["page_id"], $this->LanguageId, "tag_lib_start"), $return[$i]['tag_lib_start']);

                        $this->PagesId[] = $return[$i]['page_id'];
                        // Otestovat!!!!!!!!!
                        if ($this->CacheTime != -1 && ($return[$i]['cachetime'] < $this->CacheTime || $this->CacheTime == 0)) {
                            if ($return[$i]['cachetime'] != 0) {
                                $this->CacheTime = $return[$i]['cachetime'];
                            } elseif ($this->CacheTime == 10000000000) {
                                $this->CacheTime = $return[$i]['cachetime'];
                            }
                        }
                        $this->parsePages($path[0] . '/' . $path[1], $return[$i]['page_id']);

                        $this->executeTemplateContent(TemplateCacheKeys::page($return[$i]["page_id"], $this->LanguageId, "tag_lib_end"), $return[$i]['tag_lib_end']);

                        $this->ParsingPages = false;
                        return;
                    }
                }
                for ($i = 0; $i < count($return); $i++) {
                    $tmp_path = $this->executeTemplateContent(TemplateCacheKeys::page($return[$i]["page_id"], $this->LanguageId, "href"), $return[$i]['href']);
                    if ($tmp_path == "") {
                        $this->ParsingPages = true;
                        $this->executeTemplateContent(TemplateCacheKeys::page($return[$i]["page_id"], $this->LanguageId, "tag_lib_start"), $return[$i]['tag_lib_start']);

                        $this->PagesId[] = $return[$i]['page_id'];
                        // Otestovat!!!!!!!!!
                        if ($this->CacheTime != -1 && ($return[$i]['cachetime'] < $this->CacheTime || $this->CacheTime == 0)) {
                            if ($return[$i]['cachetime'] != 0) {
                                $this->CacheTime = $return[$i]['cachetime'];
                            } elseif ($this->CacheTime == 10000000000) {
                                $this->CacheTime = $return[$i]['cachetime'];
                            }
                        }

                        $this->parsePages(($tmp_path == $path[0]) ? $path[1] : $path[0] . '/' . $path[1], $return[$i]['page_id']);

                        $this->executeTemplateContent(TemplateCacheKeys::page($return[$i]["page_id"], $this->LanguageId, "tag_lib_start"), $return[$i]['tag_lib_end']);

                        $this->ParsingPages = false;
                        return;
                    }
                }

                $this->generateErrorPage('404');
            }
        }

        /**
         *
         *  Setups IsCashed flag.
         *  
         *  @param  allow  value for caching ( true | false )
         *  @return none
         *
         */
        public function cache($allow, $time) {
            if ($allow == "true") {
                $this->IsCached = 1;
            } else if ($allow == "false") {
                $this->IsCached = 0;
            } else {
                trigger_error("Passed value [allow] is not valid in cache [Web]!", E_USER_WARNING);
                return;
            }
            if (!is_numeric($time)) {
                trigger_error("Passed value [time] is not valid in cache [Web]!", E_USER_WARNING);
                return;
            }

            if ($this->IsCached && $this->ParsingPages == false) {
                $name = 'page';
                foreach ($this->PagesId as $pg) {
                    $name .= '-' . $pg;
                }
                $name .= '.cache.html';
                $path = CACHE_PAGES_PATH . $name;
                $cacheMTime = filemtime($path);

                if (file_exists($path) && is_readable($path) && ($cacheMTime > (time() - $time))) {
                    //echo $cacheMTime;
                    echo file_get_contents($path);
                    parent::close();
                } else {
                    $this->CacheFile = $name;
                }
            }
        }

        private function loadPageFiles() {
            $allHeaders = getallheaders();
            $userBrowser = $allHeaders['User-Agent'];
            $browser = 'for_all';
            if (preg_match("(Firefox)", $userBrowser)) {
                $browser = 'for_firefox';
            } elseif (preg_match("(MSIE 8.0)", $userBrowser)) {
                $browser = 'for_msie8';
            } elseif (preg_match("(MSIE 7.0)", $userBrowser)) {
                $browser = 'for_msie7';
            } elseif (preg_match("(MSIE 6.0)", $userBrowser)) {
                $browser = 'for_msie6';
            } elseif (preg_match("(Opera)", $userBrowser)) {
                $browser = 'for_opera';
            } elseif (preg_match("(Safari)", $userBrowser)) {
                $browser = 'for_safari';
            }

            $pageIds = implode(', ', $this->PagesId);
            $files = parent::db()->fetchAll("SELECT pfi.`page_id`, pf.`id`, pf.`type`, pf.`placement` FROM `page_file_inc` pfi LEFT JOIN `page_file` pf ON pfi.`file_id` = pf.`id` WHERE pfi.`page_id` in (" . $pageIds . ") AND pfi.`language_id` = " . $this->LanguageId . " AND (pf.`for_all` = 1 OR pf.`" . $browser . "` = 1) ORDER BY pfi.`order`;");
            foreach ($this->PagesId as $pageId) {
                foreach ($files as $file) {
                    if ($file['page_id'] == $pageId) {
                        $fileUrl = '~/file.php?fid=' . $file['id'];
                        switch ($file['type']) {
                            case WEB_TYPE_CSS: 
                                $this->PageStyles .= (($this->Template == 'xml') ? '<rssmm:link-ref>' . $fileUrl . '</rssmm:link-ref>' : '<link rel="stylesheet" href="' . $fileUrl . '" type="text/css" />');
                                break;
                            case WEB_TYPE_JS: 
                                $value = (($this->Template == 'xml') ? '<rssmm:script-ref>' . $fileUrl . '</rssmm:script-ref>' : '<script type="text/javascript" src="' . $fileUrl . '"></script>');
                                if ($file['placement'] == 0) {
                                    $this->PageHeadScripts .= $value;
                                } else if ($file['placement'] == 1) {
                                    $this->PageTailScripts .= $value;
                                }
                                break;
                        }
                    }
                }
            }
        }

        public function formatScript($url) {
            return ($this->Template == 'xml') ? '<rssmm:script-ref>' . $url . '</rssmm:script-ref>' : '<script type="text/javascript" src="' . $url . '"></script>';
        }

        public function formatScriptInline($content) {
            return ($this->Template == 'xml') ? '<rssmm:script>' . $content . '</rssmm:script>' : '<script type="text/javascript">' . $content . '</script>';
        }

        public function formatStyle($url) {
            return ($this->Template == 'xml') ? '<rssmm:link-ref>' . $url . '</rssmm:link-ref>' : '<link rel="stylesheet" href="' . $url . '" type="text/css" />';
        }

        public function formatStyleInline($content) {
            return ($this->Template == 'xml') ? '<rssmm:style>' . $content . '</rssmm:style>' : '<style type="text/css">' . $content . '</style>';
        }

        public function addScript($html, $placement = "head") {
            if ($placement == "head") {
                $this->PageHeadScripts .= $html;
            } else if($placement == "tail") {
                $this->PageTailScripts .= $html;
            }
        }

        public function addStyle($html) {
            $this->PageStyles .= $html;
        }

        /**
         *
         *  Flushes page content to output.
         *
         *  @param  path  string containing path template file ( implied )
         *  @return none
         *
         */
        public function flush() {
            if (empty($this->SelectedEntrypoint) && !$this->UrlResolver->getWebProject()["pageless"]) {
                $_SESSION['last-request']['pages-id'] = $this->PagesId;

                if (!RoleHelper::canUser(Web::$PageRightDesc, $this->PagesId, WEB_R_READ)) {
                    $this->generateErrorPage('403');
                }

                $this->loadPageFiles();
            }

            $lang = $this->UrlResolver->getLanguage()['language'];
            $keywords = file_get_contents("keywords.txt");

            $this->flushContent($lang, $keywords);
        }

        public function flushContent($lang = null, $keywords = null, $webRootUrl = null) {
            $isLang = strlen($lang) > 0;

            // Diagnostics
            $diacont = "";
            if (!$this->IsCached && $this->getDebugMode()) {
                if (array_key_exists('mem-stats', $_GET)) {
                    $diacont = $this->Diagnostics->printMemoryStats();
                }
                if (array_key_exists('duration-stats', $_GET)) {
                    $diacont .= $this->Diagnostics->printDuration();
                }
                if (array_key_exists('query-stats', $_GET)) {
                    $diacont .= parent::debugFrame('Database queries', parent::db()->getQueriesPerRequest());
                }
                if (array_key_exists('query-list', $_GET)) {
                    $querycont = "";
                    $totalMeasure = 0;
                    $measures = parent::db()->getDataAccess()->getMeasures();
                    $profiles = parent::db()->getDataAccess()->getProfiles();
                    $worstKey = 0;
                    $worstMeasure = 0;
                    foreach (parent::db()->getDataAccess()->getQueries() as $key => $query) {
                        $totalMeasure += $measures[$key];
                        $measurePhp = round($measures[$key], 5);
                        $measureMysql = round($profiles[$key]["Duration"], 5);
                        if ($measurePhp > $worstMeasure) {
                            $worstMeasure = $measurePhp;
                            $worstKey = $key;
                        }

                        $header = "Query $key (php $measurePhp ms) (mysql $measureMysql ms)";
                        $querycont .= parent::debugFrame($header, $query, 'code', false);
                    }

                    $queryCount = count($measures);
                    $totalMeasure = round($totalMeasure, 5);
                    $diacont .= parent::debugFrame("Query stats", "Count: $queryCount<br />Total time: $totalMeasure ms<br />Worst: $worstKey ($worstMeasure ms)", 'code');
                    $diacont .= $querycont;
                }
                if (array_key_exists('parser-stats', $_GET)) {
                    $parsercont = "";
                    $totalMeasure = 0;
                    $measures = TemplateParser::getMeasures();
                    $worstKey = 0;
                    $worstMeasure = 0;
                    foreach ($measures as $key => $item) {
                        $totalMeasure += $item[0];
                        $measure = round($item[0], 5);
                        if ($measure > $worstMeasure) {
                            $worstMeasure = $measure;
                            $worstKey = $key;
                        }

                        $header = "Parsing $key ($measure ms)";
                        $parsercont .= parent::debugFrame($header, htmlentities($item[1]), 'code');
                    }

                    $parserCount = count($measures);
                    $totalMeasure = round($totalMeasure, 5);
                    $diacont .= parent::debugFrame("Parser stats", "Count: $parserCount<br />Total time: $totalMeasure ms<br />Worst: $worstKey ($worstMeasure ms)", 'code');
                    $diacont .= $parsercont;
                }
                if (array_key_exists('debug-environment', $_GET)) {
                    $diacont .= parent::debugFrame('Environment', $this->system()->debugEnvironment(), "pre");
                    $diacont .= parent::debugFrame('Server', $this->system()->debugServer(), "pre");
                }
                if (array_key_exists('debug-request', $_GET)) {
                    $diacont .= parent::debugFrame('Request', $this->system()->debugRequest(), "pre");
                    $diacont .= parent::debugFrame('Headers', $this->system()->debugHttpHeaders(), "pre");
                }
                if ($this->getDebugMode() && strlen($this->PageLog) != 0) {
                    $diacont .= parent::debugFrame('Page Log', $this->PageLog);
                }
            }

            $areHeadersSent = headers_sent();
            if (!$areHeadersSent) {
                header('Content-Type: ' . $this->ContentType . '; charset=utf-8');

                if ($isLang) {
                    header('Content-language: ' . $lang);
                }
            }

            if ($this->Template == 'xml') {
                $return = ''
                . '<rssmm:response>'
                    . (($this->getDebugMode() && strlen($this->PageLog) != 0) ? '<rssmm:log>' . $this->PageLog . '</rssmm:log>' : '')
                    . '<rssmm:head>'
                        . '<rssmm:title>' . $this->PageTitle . '</rssmm:title>'
                        . '<rssmm:keywords>' . ((strlen($this->Keywords) > 0) ? $this->Keywords . ',' : '') . ((strlen($keywords) > 0) ? $keywords . ',' : '') . 'wfw,rssmm,is4wfw,neptuo</rssmm:keywords>'
                        . '<rssmm:styles>' . $this->PageStyles . '</rssmm:styles>'
                        . '<rssmm:scripts>' . $this->PageHeadScripts . $this->PageTailScripts . '</rssmm:scripts>'
                    . '</rssmm:head>'
                    . '<rssmm:content>' . $this->PageContent . '</rssmm:content>'
                    . '<rssmm:log>' . $diacont . '</rssmm:log>'
                . '</rssmm:response>';
            } else if ($this->Template == 'none') {
                $return = $this->PageContent;
            } else {
                $isHtml = false;
                if ($this->Doctype == 'html5') {
                    $isHtml = true;
                    $doctype = ''
                    . '<!DOCTYPE html>'
                    . '<html' . ($isLang ? ' lang="' . $lang . '"' : '') . '>';
                } else if ($this->Doctype == 'svg') {
                    $doctype = ''
                    . '<?xml version="1.0" encoding="utf-8"?>'
                    . '<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">';
                } else {
                    $isHtml = true;
                    $doctype = ''
                    . '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'
                    . '<html xmlns="http://www.w3.org/1999/xhtml">';
                }

                if ($isHtml) {
                    $return = ''
                        . $doctype
                        . '<head>'
                            . (($areHeadersSent || $this->IsCached) ? '<meta http-equiv="content-type" content="' . $this->ContentType . '; charset=utf-8" />' : '')
                            . '<meta name="description" content="' . $this->PageTitle . '" />'
                            . '<meta name="keywords" content="' . ((strlen($this->Keywords) > 0) ? $this->Keywords . ',' : '') . ((strlen($keywords) > 0) ? $keywords . ',' : '') . 'wfw,rssmm,is4wfw,neptuo" />'
                            . (($areHeadersSent && $isLang) ? '<meta http-equiv="Content-language" content="' . $lang . '" />' : '')
                            . '<meta name="robots" content="all, index, follow" />'
                            . '<meta name="author" content="Marek Fišera" />'
                            . '<title>' . $this->PageTitle . '</title>'
                            . $this->PageHead . $this->PageStyles . $this->PageHeadScripts
                        . '</head>'
                        . '<body>' 
                            . $this->PageContent 
                            . $diacont 
                            . $this->PageTailScripts
                        . '</body>'
                    . '</html>';
                } else {
                    $return = $doctype . $this->PageContent;
                }
            }

            if ($webRootUrl == null) {
                $return = $this->resolveWebRoot($return);
            } else {
                $return = str_replace("~/", $webRootUrl, $return);
            }

            if ($this->IsCached) {
                file_put_contents(CACHE_PAGES_PATH . $this->CacheFile, $return);
            }

            // Rewrite anchors
            $return = preg_replace_callback('(&web:page=([0-9]+))', array(&$this, 'parseproperties'), $return);
            // Generate web:frames
            $return = preg_replace_callback('(<web:frame( title="([^"]*)")*( open="(true|false)")*>(((\s*)|(.*))*)</web:frame>)', array(&$this, 'parsepostframes'), $return);

            $this->tryToComprimeContent($return);
        }
        
        private function resolveWebRoot($content, bool $absolute = false, $useIs4wfwPort = true) {
            if ($this->UrlResolver != null) {
                $webProject = $this->UrlResolver->getWebProject();
                $rootUrl = UrlResolver::combinePath(INSTANCE_URL, $webProject['alias']['root_url']);
                $rootUrl = UrlResolver::combinePath($rootUrl, '/');

                if ($absolute) {
                    $domainUrl = $this->getHttpHost();
                    if ($useIs4wfwPort) {
                        $domainUrl .= $this->getDevelopmentPortWithSeparator();
                    }

                    $rootUrl = UrlResolver::combinePath($domainUrl, $rootUrl);
                    $rootUrl = UrlResolver::combinePath($this->Protocol, $rootUrl, '://');
                }

                $content = str_replace("~/", $rootUrl, $content);
            }

            return $content;
        }

        private function parseproperties($values) {
            $path = $this->composeUrl($values[1], $this->LanguageId);
            return $path;
        }

        private function parsepostframes($values) {
            $open = false;
            if ($values[5] == "true") {
                $open = true;
            }

            $title = $values[2];
            $content = $values[5];

            $path = parent::getFrame($title, $content, "", $open);
            return $path;
        }

        private function tryToComprimeContent($content) {
            $return = $content;

            if ($this->ZipOutput) {
                $acceptEnc = $_SERVER['HTTP_ACCEPT_ENCODING'];
                if (headers_sent ()) {
                    $encoding = false;
                } elseif (strpos($acceptEnc, 'x-gzip') !== false) {
                    $encoding = 'x-gzip';
                } elseif (strpos($acceptEnc, 'gzip') !== false) {
                    $encoding = 'gzip';
                } else {
                    $encoding = false;
                }

                if ($encoding) {
                    $return = gzcompress($return, 9);

                    header('Content-Encoding: ' . $encoding);
                    print("\x1f\x8b\x08\x00\x00\x00\x00\x00");
                    print($return);
                    parent::close();
                }
            }

            echo $return;
            parent::close();
        }

        private function resolveUrl($url, $absolutePath) {
            if ($this->UrlResolver == null) {
                $url = str_replace('~/', INSTANCE_URL, $url);
            } else {
                $project = $this->UrlResolver->getWebProject();
                $pageId = str_replace("~/", "/", $url);
                $url = $this->composeUrlProjectPart($pageId, $project, $absolutePath);
            }

            return $url;
        }

        /**
         *
         *  Compose url recursivly from passed pageId to root page[0].
         *  If pageId is url, this simply returns it :)
         *
         *  @param  pageId      	page id of required page path
         *  @param  languageId  	language id
         *  @param	absolutePath  use absolute path any time
         *  @return composed url
         *
         */
        public function composeUrl($pageId, $languageId = false, $absolutePath = false, $forceDefProp = false, $copyParameters = false) {
            $languageId = ($languageId === false) ? $this->LanguageId : $languageId;
            $lastPageId = 0;
            $pageProjectId = 0;
            if (!is_numeric($pageId)) {
                $url = $this->resolveUrl($pageId, $absolutePath);
                if ($copyParameters) {
                    $url = UrlUtils::addCurrentQueryString($url);
                }

                return $this->addSpecialParams($url);
            }
            
            $defaultQueryParameters = [];
            $currentValues = array();
            $props = parent::dao('PageProperty')->getPage($pageId);
            if (count($props) > 0) {
                $parser = $this->createParser();
                foreach ($props as $prop) {
                    if (strpos($prop['name'], ":") === false) {
                        if ($forceDefProp) {
                            $prop['value'] = $this->getProperty($prop['value']);
                            $defaultQueryParameters[$prop['name']] = $prop['value'];
                        }
                    } else {
                        $currentValue = $this->getProperty($prop['name']);
                        if (!$currentValue || $forceDefProp) {
                            $prop['value'] = $parser->parsePropertyExactly($prop['value']);
                            $this->setProperty($prop['name'], $prop['value']);
                            $currentValues[$prop['name']] = $currentValue;
                        }
                    }
                }
            }
            
            while ($pageId != 0) {
                $return = parent::db()->fetchAll("SELECT `parent_id`, `href`, `wp` FROM `page` LEFT JOIN `info` ON `page`.`id` = `info`.`page_id` WHERE `page`.`id` = " . $pageId . " AND `info`.`language_id` = " . $languageId . ";");
                if (count($return) == 1) {
                    if (strlen($return[0]['href']) != 0 && !preg_match($this->TAG_RE, $return[0]['href'])) {
                        $this->PropertyUse = 'get';
                        $return[0]['href'] = preg_replace_callback($this->PROP_RE, array(&$this, 'parsecproperty'), $return[0]['href']);
                        if (strpos($return[0]['href'], "http") === 0) {
                            //echo $return[0]['href'];
                            $url = $return[0]['href'] . $this->CurrentPath;
                            $this->CurrentPath = "";
                            foreach ($defaultQueryParameters as $key => $value) {
                                $url = UrlUtils::addParameter($url, $key, $value);
                            }

                            if ($copyParameters) {
                                $url = UrlUtils::addCurrentQueryString($url);
                            }

                            return $this->addSpecialParams($url);
                        }
                        
                        if (strlen($return[0]['href']) > 0) {
                            $this->CurrentPath = "/" . $return[0]['href'] . $this->CurrentPath;
                        }
                    }
                    $lastPageId = $pageId;
                    $pageProjectId = $return[0]['wp'];
                    $pageId = $return[0]['parent_id'];
                } else {
                    $message = "Error while composing url! [pageId = " . $pageId . " ; languageId = " . $languageId . "]";
                    //echo "<h4 class=\"error\">".$message."</h4>";
                    trigger_error($message, E_USER_WARNING);
                    return '#';
                }
            }
            
            foreach ($currentValues as $key => $item) {
                $this->setProperty($key, $item);
            }

            $return = parent::db()->fetchAll("SELECT `language` FROM `language` WHERE `id` = " . $languageId . ";");
            if (count($return) == 1) {
                if (strlen($return[0]['language']) == 0) {
                    $this->CurrentPath = substr($this->CurrentPath, 1, strlen($this->CurrentPath));
                }
                $tmpPath = INSTANCE_URL . $return[0]['language'] . $this->CurrentPath;
                $this->CurrentPath = "";
            }

            if ($pageProjectId == $this->ProjectId) {
                // Dosestav url z dat v urlResolveru
                $url = $this->composeUrlProjectPart($tmpPath, $this->UrlResolver->getWebProject(), $absolutePath);
                foreach ($defaultQueryParameters as $key => $value) {
                    $url = UrlUtils::addParameter($url, $key, $value);
                }
                
                if ($copyParameters) {
                    $url = UrlUtils::addCurrentQueryString($url);
                }

                return $this->addSpecialParams($url);
            } else {
                // Najdi project url a dosestav url
                $project = array('alias' => parent::db()->fetchSingle('select `domain_url`, `root_url`, `virtual_url`, `http`, `https` from `web_url` where `project_id` = ' . $pageProjectId . ' and `enabled` = 1 order by `web_url`.`default` desc, `web_url`.`id`;'));
                if ($project['alias'] != array()) {
                    $url = $this->composeUrlProjectPart($tmpPath, $project, true);

                    foreach ($defaultQueryParameters as $key => $value) {
                        $url = UrlUtils::addParameter($url, $key, $value);
                    }

                    if ($copyParameters) {
                        $url = UrlUtils::addCurrentQueryString($url);
                    }

                    return $this->addSpecialParams($url);
                } else {
                    $message = parent::getError("Project doesn't have an url adress!");
                    trigger_error($message, E_USER_WARNING);
                    return '#';
                }
            }
        }

        private function composeUrlProjectPart($pageUrl, $project, $absolute) {
            $pageUrl = UrlResolver::combinePath($project['alias']['virtual_url'], $pageUrl);
            $pageUrl = UrlResolver::combinePath($project['alias']['root_url'], $pageUrl);

            $isEmptyProject = empty($project);

            if ($isEmptyProject || $project['alias']['domain_url'] == "*") {
                $project['alias']['domain_url'] = $this->getHttpHost();
            }

            if ($isEmptyProject) {
                $project['alias'][$this->Protocol] = 1;
            }

            if ($absolute) {
                $domainUrl = $project['alias']['domain_url'] . $this->getDevelopmentPortWithSeparator();
                $pageUrl = UrlResolver::combinePath($domainUrl, $pageUrl);

                $other = ($this->Protocol == 'http') ? 'https' : 'http';
                $prot = ($project['alias'][$this->Protocol] == 1) ? $this->Protocol : $other;

                $pageUrl = UrlResolver::combinePath($prot, $pageUrl, '://');
            } else {
                $pageUrl = UrlResolver::combinePath('/', $pageUrl);
            }

            return $pageUrl;
        }

        public function getDevelopmentPortWithSeparator() {
            if ($_ENV["IS4WFW_PORT"]) {
                return ":" . $_ENV["IS4WFW_PORT"];
            }

            return "";
        }

        public function addSpecialParams($url) {
            if (array_key_exists('mem-stats', $_GET)) {
                $url = UrlUtils::addParameter($url, 'mem-stats', '');
            }
            if (array_key_exists('duration-stats', $_GET)) {
                $url = UrlUtils::addParameter($url, 'duration-stats', '');
            }
            if (array_key_exists('query-stats', $_GET)) {
                $url = UrlUtils::addParameter($url, 'query-stats', '');
            }
            if (array_key_exists('query-list', $_GET)) {
                $url = UrlUtils::addParameter($url, 'query-list', '');
            }
            if (array_key_exists('debug-environment', $_GET)) {
                $url = UrlUtils::addParameter($url, 'debug-environment', '');
            }
            if (array_key_exists('debug-request', $_GET)) {
                $url = UrlUtils::addParameter($url, 'debug-request', '');
            }
            if (array_key_exists('parser-stats', $_GET)) {
                $url = UrlUtils::addParameter($url, 'parser-stats', '');
            }
            if (array_key_exists('auto-login-ignore', $_GET)) {
                $url = UrlUtils::addParameter($url, 'auto-login-ignore', '');
            }
            return $url;
        }

        /**
         *
         *  Parse all attributes to array.
         *  
         *  @param  att string with attributes
         *  @return array of attributes
         *
         */
        private function parseatt($att) {
            $this->Attributes[] = $att[0];
        }

        /**
         *
         *  Function parses custom property, call right function & return content.
         *  
         *  @param  cprop  custom property as string
         *  @return return of custom property function     
         *
         */
        private function parsecproperty($cprop) {
            $object = explode(":", $cprop[1]);
            $attributes = array();
            $this->Attributes = array();

            global $phpObject;
            if ($phpObject->isRegistered($object[0])) {
                if ($phpObject->isProperty($object[0], $object[1])) {
                    global ${$object[0] . "Object"};
                    $func = $phpObject->getFuncToProperty($object[0], $object[1], $this->PropertyUse);
                    if($func) {
                        eval('$return =  ${$object[0]."Object"}->{$func}("' . $this->PropertyAttr . '");');
                        return $return;
                    } else {
                        return $cprop[0];
                    }
                } else if($phpObject->isAnyProperty($object[0])) {
                    global ${$object[0] . "Object"};

                    if ($this->PropertyUse === 'set') {
                        $func = 'setProperty';
                    } else {
                        $func = 'getProperty';
                    }
                    eval('$return =  ${$object[0]."Object"}->{$func}("' . $object[1] . '", "' . $this->PropertyAttr . '");');
                    return $return;
                }
            }

            return $cprop[0];
        }

        public function renderEntrypoint($moduleId, $entrypointId, $params) {
            $module = Module::findById($moduleId);
            if ($module != null) {
                foreach ($this->entrypoints as $item) {
                    if ($item["moduleId"] == $moduleId && $item["id"] == $entrypointId) {
                        return $item["handler"]($params);
                    }
                }
            }

            throw new MissingEntrypointException($moduleId, $entrypointId);
        }

        private function getProjectContent() {
            return $this->executeTemplateContent(TemplateCacheKeys::webProject($this->UrlResolver->getWebProjectId()), $this->ProjectContent);
        }

        /**
         *
         *  Generates page content and parse c tags.
         *  
         *  $return page content          
         *
         */
        public function getContent() {
            if (!empty($this->SelectedEntrypoint)) {
                try {
                    $entrypoint = explode(":", $this->SelectedEntrypoint);
                    $trailingUrl = $this->getAllPagesRelativePath();
                    return $this->renderEntrypoint($entrypoint[0], $entrypoint[1], ["relativeUrl" => $trailingUrl]);
                }
                catch (MissingEntrypointException $e) {
                    $this->generateErrorPage("404");
                }
            }

            $path = StringUtils::explode($this->Path, '/', 1);
            $page = $this->TempLoadedContent[$this->PagesIdIndex];
            if ($page == null) {
                trigger_error("Missing child page, used 'web:content' in leaf page.", E_USER_WARNING);
                return "";
            }

            $this->executeTemplateContent(TemplateCacheKeys::page($page["id"], $this->LanguageId, "tag_lib_start"), $page['tag_lib_start']);

            if (count($this->PagesId) > ($this->PagesIdIndex + 1)) {
                $this->setChildPage($this->PagesId[$this->PagesIdIndex + 1]);
            } else {
                $this->setChildPage(-1);
            }

            $this->CurrentDynamicPath = $path[0];
            
            $tmp_path = $this->executeTemplateContent(TemplateCacheKeys::page($page["id"], $this->LanguageId, "href"), $page['href']);

            $this->ParentId = $this->PagesId[$this->PagesIdIndex];
            $this->PagesIdIndex++;

            if ($page['in_title'] == 1) {
                // parse title
                if (strlen($page['title']) > 0) {
                    $this->PropertyAttr = '';
                    $this->PropertyUse = 'get';
                    $this->PageTitle = $this->executeTemplateContent(TemplateCacheKeys::page($page["id"], $this->LanguageId, "title"), $page['title']) . " - " . $this->PageTitle;
                }
            }
            if (strlen($page['keywords']) > 0) {
                $page['keywords'] = $this->executeTemplateContent(TemplateCacheKeys::page($page["id"], $this->LanguageId, "keywords"), $page['keywords']);
            }
            $this->Keywords .= ( (strlen($page['keywords']) != 0) ? ((strlen($this->Keywords) != 0) ? ',' . $page['keywords'] : $page['keywords']) : '');

            $this->Path = $path[1];

            $this->PageHead .= $this->executeTemplateContent(TemplateCacheKeys::page($page["id"], $this->LanguageId, "head"), $page['head']);
            $pageContent = $this->executeTemplateContent(TemplateCacheKeys::page($page["id"], $this->LanguageId, "content"), $page['content']);

            $this->executeTemplateContent(TemplateCacheKeys::page($page["id"], $this->LanguageId, "tag_lib_end"), $page['tag_lib_end']);

            $this->PagesIdIndex--;

            if ($this->PagesIdIndex > 0) {
                $this->setChildPage($this->PagesId[$this->PagesIdIndex]);
            } else {
                $this->setChildPage(-1);
            }

            return $pageContent;
        }

        public function setContent($pageContent) {
            $this->PageContent = $pageContent;
        }

        private function getMenuItems($parentId, $display) {
            return parent::db()->fetchAll("SELECT `page`.`id`, `info`.`$display`, `info`.`icon` FROM `page` LEFT JOIN `info` ON `page`.`id` = `info`.`page_id` WHERE `page`.`parent_id` = " . $parentId . " AND `info`.`in_menu` = 1 AND `is_visible` = 1 AND `info`.`language_id` = " . $this->LanguageId . " AND `page`.`wp` = " . $this->ProjectId . " ORDER BY `info`.`page_pos`;");
        }

        private function canUserReadPage($pageId) {
            global $dbObject;
            global $loginObject;

            $sql = "SELECT `value` FROM `page_right` LEFT JOIN `group` ON `page_right`.`gid` = `group`.`gid` WHERE `pid` = " . $pageId . " AND `type` = " . WEB_R_READ . " AND (`group`.`gid` IN (" . $loginObject->getGroupsIdsAsString() . ") OR `group`.`parent_gid` IN (" . $loginObject->getGroupsIdsAsString() . "));";
            $rights = $dbObject->fetchAll($sql);
            return (count($rights) > 0);
        }

        /**
         *
         *  Generates menu.
         *  
         *  @param  parentId  id parent level page
         *  @param  template  template for generated menu
         *  @param  inn       menu nesting     
         *  @return generated menu                    
         *
         */
        public function getMenu($parentId = false, $inner = false, $classes = false, $rel = false, $template = false, $copyParameters = false, $display = "name", $inn = false) {
            global $dbObject;
            global $loginObject;
            if ($inn == false) {
                $inn = 1;
            }
            if ($inner == false) {
                $inner = 1000;
            }
            $content = "";
            $parentId = (!$parentId) ? 0 : $parentId;
            $return = $this->getMenuItems($parentId, $display);
            if (count($return) > 0) {
                $content .= '<div class="menu menu-' . $inn . ((strlen($classes) > 0) ? ' ' . $classes : '') . '"><ul class="ul-' . $inn . '">';
                $i = 1;
                foreach ($return as $lnk) {
                    if (!$this->canUserReadPage($lnk['id'])) {
                        continue;
                    }
                    $href = $this->composeUrl($lnk['id'], $this->LanguageId, false, true, $copyParameters);
                    $parent = (in_array($lnk['id'], $this->PagesId)) ? true : false;

                    $active = false;
                    if ($href == '/' . $_REQUEST['WEB_PAGE_PATH']) {
                        $active = true;
                        $parent = false;
                    }

                    if ($inner > 1) {
                        $innerParentId = $lnk['id'];
                        $innerClasses = ($parent || $active) ? 'active-submenu' : '';
                        $tmpContent = $this->getMenu($innerParentId, $inner, $innerClasses, false, false, $copyParameters, $display, $inn + 1);
                    }

                    $lnkName = $lnk[$display];
                    if ($display == "title") {
                        $lnkName = $this->executeTemplateContent(TemplateCacheKeys::page($lnk["id"], $this->LanguageId, "title"), $lnk["title"]);
                    }

                    $content .= ''
                    . '<li class="menu-item li-' . $i . (($parent) ? ' active-parent' : '') . (($active) ? ' active-item' : '') . ' ' . ((strlen($tmpContent) != 0) ? 'parent' : 'single') . '">'
                        . '<div class="link' . (($parent) ? ' active-parent-link' : '') . (($active) ? ' active-link' : '') . '">'
                            . '<a href="' . $href . '"' . (($rel != false) ? ' rel="' . $rel . '"' : '') . '>'
                                . '<span>' . $lnkName . '</span>'
                            . '</a>'
                        . '</div>'
                        . $tmpContent
                    . '</li>';

                    $i++;
                }
                $inner--;
                $content .= "</ul></div>";

                return $content;
            }
        }

        public function getMenuWithTemplate($template, $parentId, $display = "name", $copyParameters = false) {
            $model = new ListModel();
            $this->pushListModel($model);

            $data = $this->getMenuItems($parentId, $display);
            $items = array();
            foreach ($data as $key => $item) {
                if (!$this->canUserReadPage($item['id'])) {
                    continue;
                }

                $text = $item[$display];
                if ($display == "title") {
                    $text = $this->executeTemplateContent(TemplateCacheKeys::page($item["id"], $this->LanguageId, "title"), $item["title"]);
                }
                
                $url = $this->composeUrl($item["id"], $this->LanguageId, false, true, $copyParameters);
                $active = in_array($item["id"], $this->PagesId);

                $items[] = array(
                    "display" => $text,
                    "url" => $url,
                    "active" => $active,
                    "icon" => $item["icon"]
                );
            }

            $model->render();
            $model->items($items);
            $result = $template();

            $this->popListModel();
            return $result;
        }

        public function getMenuData() {
            return $this->peekListModel();
        }

        public function getMenuItemDisplay() {
            return $this->peekListModel()->field("display");
        }

        public function getMenuItemUrl() {
            return $this->peekListModel()->field("url");
        }

        public function getMenuItemActive() {
            return $this->peekListModel()->field("active");
        }

        public function getMenuItemIcon() {
            return $this->peekListModel()->field("icon");
        }

        /**
         *
         *  Generates language links.
         *  C tag.     
         *  
         *  @param  homePage  show home page in links
         *  @return generate links to language versions
         *
         */
        public function getLanguages($homePage = false, $display = "language") {
            global $phpObject;
            global $dbObject;

            if ($homePage) {
                $return = $dbObject->fetchAll("SELECT `language`.`id`, `language`.`language`, `language`.`$display` FROM `info` LEFT JOIN `language` ON `info`.`language_id` = `language`.`id` LEFT JOIN `page` ON `info`.`page_id` = `page`.`id` WHERE `language`.`language` != \"\" AND `page`.`parent_id` = 0 AND `info`.`href` = \"\" ORDER BY `language`.`id`;");

                if (count($return) > 0) {
                    $ret = "<div class=\"languages\">";
                    foreach ($return as $lang) {
                        $ret .= '<a class="lang-' . $lang['language'] . (($this->LanguageId == $lang['id']) ? ' active-language' : '') . '" href="~/' . $lang['language'] . '">' . $lang[$display] . '</a> ';
                    }
                    $ret .= "</div>";
                    return $ret;
                } else {
                    return "";
                }
            } else {
                $langs = array();
                $return = $dbObject->fetchAll("SELECT `id`, `language`, `$display` FROM `language` WHERE `language` != \"\";");
                foreach ($return as $ln) {
                    $names[$ln['id']] = $ln[$display];
                    $langs[$ln['id']] = '~/' . $ln['language'];
                }

                $pageId = $this->PagesId[count($this->PagesId) - 1];
                $return = $dbObject->fetchAll("SELECT `info`.`language_id` FROM `info` WHERE `info`.`page_id` = " . $pageId . ";");
                foreach ($return as $ln) {
                    $langs[$ln['language_id']] = $this->composeUrl($pageId, $ln['language_id']);
                }

                $ret = "<div class=\"languages\">";
                foreach ($langs as $name => $ln) {
                    $ret .= '<a class="lang-' . $names[$name] . (($this->LanguageId == $name) ? ' active-language' : '') . '" href="' . $ln . '"><span>' . $names[$name] . '</span></a> ';
                }
                $ret .= "</div>";
                return $ret;
            }
        }

        /**
         *
         *  Generates crumb menu.
         *  C tag.
         *  
         *  @param    delimenter    delimenter between links
         *  @return   crumb menu
         *
         */
        public function getCrumbMenu($delimeter) {
            global $dbObject;
            $return = '';
            $path = '';
            $root = $dbObject->fetchAll("SELECT `language` FROM `language` WHERE `id` = " . $this->LanguageId . ";");
            $return .= '<a href="~/' .  $root[0]['language'] . '">' . strtoupper($root[0]['language']) . '</a> ';
            $path = '~/' . $root[0]['language'];

            foreach ($this->PagesId as $pg) {
                $page = $dbObject->fetchAll("SELECT `name`, `href` FROM `info` WHERE `page_id` = " . $pg . " AND `language_id` = " . $this->LanguageId . " AND `href` != \"\";");
                if (count($page) == 1) {
                    $path .= '/' . $page[0]['href'];
                    $return .= $delimeter . ' <a href="' . $path . '">' . $page[0]['name'] . '</a> ';
                }
            }

            return '<div class="crumb-menu">' . $return . '</div>';
        }

        public function getPageUrl($pageId, $languageId = false, $isAbsolute = false, $params = array(), $anything = array()) {
            $languageId = (!$languageId) ? $this->LanguageId : $languageId;
            $isAbsolute = (!$isAbsolute) ? false : true;

            if (is_numeric($pageId)) {
                $url = $this->composeUrl($pageId, $languageId, $isAbsolute);
            } else if (StringUtils::startsWith($pageId, "~/")) {
                $url = $this->resolveUrl($pageId, $isAbsolute);
            } else {
                $url = $pageId;
            }

            foreach ($params as $key => $value) {
                $url = UrlUtils::addParameter($url, $key, $value);
            }

            foreach ($anything as $key => $value) {
                if (strpos($key, 'param-') === 0) {
                    $key = substr($key, 6);
                }

                $url = UrlUtils::addParameter($url, $key, $value);
            }

            return $url;
        }

        private $providedPageUrl;

        public function providePageUrl($template, $pageId, $languageId, $isAbsolute = false, $params = array()) {
            $oldUrl = $this->providedPageUrl;

            try {
                $this->providedPageUrl = $this->getPageUrl($pageId, $languageId, $isAbsolute, $params);
                if (is_callable($template)) {
                    return $template();
                }
            } finally {
                $this->providedPageUrl = $oldUrl;
            }
        }

        /**
         *
         *  Creates anchor.
         *  C tag.     
         *  
         *  @param  pageId  id of other page in web page
         *  @param  text    inner html of anchor     
         *  @param  class   typical html class attribute
         *  @param  id      tycipal html id attribute
         *  @param  target  typical html target attribute
         *  @param  rel     typical html rel attribute
         *  @return html anchor               
         *
         */
        public function makeAnchor($pageId, $text = false, $languageId = false, $class = "", $activeClass = '', $id = "", $target = "", $rel = "", $type = '', $params = [], $attributes = []) {
            global $dbObject;
            $languageId = (!$languageId) ? $this->LanguageId : $languageId;

            if (strlen($activeClass) > 0 && $pageId == $this->getLastPageId() && $this->LanguageId == $languageId) {
                if(strlen($class) > 0) {
                    $class .= ' ' . $activeClass;
                } else {
                    $class = $activeClass;
                }
            }

            if (is_numeric($pageId)) {
                $sql_return = $dbObject->fetchAll("SELECT `href`, `is_visible` FROM `page` LEFT JOIN `info` ON `page`.`id` = `info`.`page_id` WHERE `page`.`id` = " . $pageId . " AND `info`.`language_id` = " . $languageId . ";");
            }

            if (count($sql_return) == 1 && $sql_return[0]['is_visible'] == 0) {
                return "";
            }

            if (count($sql_return) == 1 || !is_numeric($pageId)) {
                $url = $this->composeUrl($pageId, $languageId);
    
                foreach ($params as $key => $value) {
                    $url = UrlUtils::addParameter($url, $key, $value);
                }

                $return = $type == 'button' ? '<button' : '<a href="' . $url . '"';
                if ($class) {
                    $return .= " class=\"" . $class . "\"";
                }
                if ($id) {
                    $return .= " id=\"" . $id . "\"";
                }
                if ($target) {
                    $return .= " target=\"" . $target . "\"";
                }
                if ($rel) {
                    $return .= " rel=\"" . $rel . "\"";
                }

                $return .= " " . $this->joinAttributes($attributes);
                if ($text != false) {
                    $return .= $type == 'button' ? ' onclick="window.location.href=\'' . $url . '\';">' . $text . '</button>' : ">" . $text . "</a>";
                } else {
                    $return .= ">";
                }

                return $return;
            } else {
                $error = "Sorry. Requested page [" . $pageId . "] doesn't exists in this languageId [" . $languageId . "]!";
                trigger_error($error, E_USER_WARNING);
                return "<h4 class=\"error\">" . $error . "</h4>";
            }
        }

        public function getProvidedPageUrl() {
            return $this->providedPageUrl;
        }

        public function makeAnchorFullTag($template, $pageId, $languageId = false, $class = "", $activeClass = '', $id = "", $target = "", $rel = "", $type = '', $params = [], $attributes = []) {
            $text = $template();
            return $this->makeAnchor($pageId, $text, $languageId, $class, $activeClass, $id, $target, $rel, $type, $params, $attributes);
        }

        /**
         *
         *  Include content of another web page.
         *  C tag.   
         *  
         *  @param  pageId        id of other page in web page
         *  @param  language      can select language, if isn't set, it uses current language
         *  @param  notParseCTag  if false, i doesn't parse c tags in included page    
         *  @param	whenLogged		include only when true and user is logged
         * 	@param	whenNotLogged	include only when true and user is not logged
         *  @return content of requested page                      
         *
         */
        public function includePage($pageId, $languageId = false, $notParseCTag = false, $whenLogged = false, $whenNotLogged = false) {
            global $dbObject;
            global $loginObject;
            $languageId = (!$languageId) ? $this->LanguageId : $languageId;

            if (($whenLogged == true && $loginObject->isLogged() == true) || ($whenNotLogged == true && $loginObject->isLogged() == false) || ($whenLogged == false && $whenNotLogged == false)) {
                $return = $dbObject->fetchAll("SELECT `content` FROM `content` LEFT JOIN `page` ON `content`.`page_id` = `page`.`id` WHERE `page`.`id` = " . $pageId . " AND `content`.`language_id` = " . $languageId . ";");
                if (count($return) == 1) {
                    if (!$notParseCTag) {
                        return $this->executeTemplateContent(TemplateCacheKeys::page($pageId, $this->LanguageId, "content"), $return[0]['content']);
                    } else {
                        return $return[0]['content'];
                    }
                } else {
                    $error = "Sorry. Requested page [" . $pageId . "] doesn't exists in this languageId [" . $languageId . "]!";
                    trigger_error($error, E_USER_WARNING);
                    return "<h4 class=\"error\">" . $error . "</h4>";
                }
            }
        }

        public static $TemplateRightDesc = array(
            'template_right', 'tid', 'gid', 'type'
        );

        public static $PageRightDesc = array(
            'page_right', 'pid', 'gid', 'type'
        );

        /**
         *
         *  Include content of template.
         *  C tag.   
         *  
         *  @param  pageId        id of other page in web page    
         *  @param	whenLogged		include only when true and user is logged
         * 	@param	whenNotLogged	include only when true and user is not logged
         * 	@param	browser				include only in passed browser     
         *  @return content of requested template     
         *
         */
        public function includeTemplate($templateId, $whenLogged = false, $whenNotLogged = false, $browser = false) {
            global $dbObject;
            global $loginObject;

            $incForBrowser = true;
            if (strlen($browser) > 0) {
                $allHeaders = getallheaders();
                $userBrowser = $allHeaders['User-Agent'];

                // Takhle ne!!!
                if (strstr(strtolower($userBrowser), strtolower($browser)) != false) {
                    $incForBrowser = true;
                } else {
                    $incForBrowser = false;
                }
            }

            if (($whenLogged == true && $loginObject->isLogged() == true) || ($whenNotLogged == true && $loginObject->isLogged() == false) || ($whenLogged == false && $whenNotLogged == false && $browser == false) || $incForBrowser) {
                if (RoleHelper::isInRole(parent::login()->getGroupsIds(), RoleHelper::getRights(Web::$TemplateRightDesc, $templateId, WEB_R_READ))) {
                    $template = $dbObject->fetchAll('SELECT `content` FROM `template` WHERE `id` = ' . $dbObject->escape($templateId) . ';');
                    if (count($template) == 1) {
                        $return = $this->executeTemplateContent(TemplateCacheKeys::template($templateId), $template[0]['content']);
                        return $return;
                    }
                } else {
                    trigger_error('Permission denied when reading template id = ' . $templateId . '!', E_USER_WARNING);
                }
            }
        }

        public function includeUrl($url) {
            $url = $this->resolveWebRoot($url, true, false);

            $client = new HttpClient();
            $content = $client->get($url);
            return $content;
        }

        /**
         *
         *  Redirects to another page, its path is defined in $path or $_REQUEST['path'].
         *  C tag.          
         *  
         *  @param  path  new location path     
         *  @return none;          
         *
         */
        public function redirect($path = false) {
            if ($path) {
                // zkontrolovat odkaz, doplnit http ...
                parent::redirectToUrl($path);
                parent::close();
            } else {
                if (array_key_exists("path", $_REQUEST)) {
                    // zkontrolovat odkaz, doplnit http ...
                    parent::redirectToUrl($_REQUEST['path']);
                    parent::close();
                } else {
                    $error = "Missing argument path for redirect!";
                    trigger_error($error, E_USER_WARNING);
                    return "<h4 class=\"error\">" . $error . "</h4>";
                }
            }
        }

        /**
         *
         * 	redirects users using passed browser.
         * 	C tag.
         * 	
         * 	@param		pageId		page id to redirect
         * 	@param		langId		language id of page
         * 	@param		browser		browser name
         * 	@param		ip		    client ip addresses separed by comma
         * 	@return		none
         *
         */
        public function redirectTo($pageId, $langId = false, $browser = false, $ip = false, $copyParameters = false, $param = array()) {
            global $webObject;
            global $phpObject;

            if ($langId != false) {
                $href = $webObject->composeUrl($pageId, $langId, true);
            } else {
                $href = $webObject->composeUrl($pageId, false, true);
            }

            $redirect = true;
            if (strlen($browser) > 0) {
                $allHeaders = getallheaders();
                $userBrowser = $allHeaders['User-Agent'];

                // Takhle ne!!!
                if (strstr(strtolower($userBrowser), $browser) != false) {
                    $redirect = true;
                } else {
                    $redirect = false;
                }
            }
            if (strlen($ip) > 0) {
                $ips = StringUtils::explode($ip, ',');
                if (in_array($_SERVER['REMOTE_ADDR'], $ips)) {
                    $redirect = true;
                } else {
                    $redirect = false;
                }
            }

            if ($copyParameters) {
                $href = UrlUtils::addCurrentQueryString($href);
            }

            if (count($param) > 0) {
                foreach ($param as $key => $value) {
                    $href = UrlUtils::addParameter($href, $key, $value);
                }
            }

            if ($redirect && $href != '#') {
                parent::redirectToUrl($href);
            }
        }

        public function redirectToHttps() {
            $this->ensureProtocol();
            if ($this->Protocol != "https") {
                $url = "https://" . $this->getHttpHost() . $this->getDevelopmentPortWithSeparator() . $this->getRequestPath();
                $url = UrlUtils::addCurrentQueryString($url);
                $this->redirectToUrl($url);
            }
        }

        /**
         *
         *  Function returns text files in binary representation.
         *  C tag. 
         *  
         *  @param    type    text web file type
         *  @param    fileId  text file id                
         *  
         *  @return   text files in binary representation
         *
         */
        public function getTextFile($type, $fileId = false) {
            global $dbObject;

            if ($fileId == false) {
                if (array_key_exists('text-file-id', $_REQUEST)) {
                    $fileId = $_REQUEST['text-file-id'];
                } elseif ($this->TextFileId != 0) {
                    $fileId = $this->TextFileId;
                } else {
                    $message = 'Text File Id argument missing!';
                    echo '<h4 class="error">' . $message . '</h4>';
                    trigger_error($message, E_USER_ERROR);
                }
            }

            $file = $dbObject->fetchAll("SELECT `content` FROM `page_file` WHERE `id` = " . $fileId . ";");
            if (count($file) == 1) {
                $fileType = "text/plain";
                switch ($type) {
                    case "css": $fileType = "text/css";
                        break;
                    case "js": $fileType = "text/javascript";
                        break;
                }
                $file[0]['content'] = str_replace("~/", INSTANCE_URL, $file[0]['content']);

                header('Content-Type: ' . $fileType);
                header('Content-Length: ' . strlen($file[0]['content']));
                header('Content-Transfer-Encoding: binary');
                echo $file[0]['content'];
                parent::close();
            } else {
                trigger_error('Text File doesn\'t exists! [fileId = ' . $fileId . ']', E_USER_WARNING);
            }
        }

        /**
         *	DEPRECATED!
        *
        *  Dynamicly rewrite address.
        *  C tag.
        *  
        *  return    if text file exists, it returns CurrentDynamicPath     
        *
        */
        public function composeTextFileUrl() {
            global $phpObject;
            global $dbObject;
            $cdp = $this->getCurrentDynamicPath();

            $id = StringUtils::explode($cdp, "-", 1);

            $file = $dbObject->fetchAll("SELECT `id` FROM `page_file` WHERE `id` = " . $id[0] . ";");
            if (count($file)) {
                $this->TextFileId = $file[0]['id'];
                return $cdp;
            } else {
                return 'false.false';
            }
        }

        /**
         *
         *  Returns current path when parsing request.
         *  
         *  @return   current path when parsing request
         *
         */
        public function getCurrentDynamicPath() {
            return $this->CurrentDynamicPath;
        }

        /**
         *
         * 	redirects user 'his' lang version (if exists) or use default one.
         * 	
         * 	@param		default		default language version, if user one doesn't exists
         * 	@param		pageId		page if to redirect to
         * 	@return		none		 		 		      
         *
         */
        public function redirectToRightLangVersion($default, $pageId = false) {
            global $dbObject;

            $allHeaders = getallheaders();
            $userLang = substr($allHeaders['Accept-Language'], 0, 2);

            $ok = false;
            if ($pageId == false) {
                $page = $dbObject->fetchAll('SELECT `info`.`href` FROM `info` LEFT JOIN `page` ON `info`.`page_id` = `page`.`id` LEFT JOIN `language` ON `info`.`language_id` = `language`.`id` WHERE `page`.`parent_id` = 0 AND `language`.`language` = "' . strtolower($userLang) . '" ORDER BY `page`.`id` LIMIT 1;');
                if (count($page) > 0) {
                    $href = $userLang . ((strlen($page[0]['href']) > 0) ? '/' . $page[0]['href'] : '');
                    $ok = true;
                } else {
                    $page = $dbObject->fetchAll('SELECT `info`.`href` FROM `info` LEFT JOIN `page` ON `info`.`page_id` = `page`.`id` LEFT JOIN `language` ON `info`.`language_id` = `language`.`id` WHERE `page`.`parent_id` = 0 AND `language`.`language` = "' . strtolower($default) . '" ORDER BY `page`.`id` LIMIT 1;');
                    if (count($page) > 0) {
                        $href = $userLang . ((strlen($page[0]['href']) > 0) ? '/' . $page[0]['href'] : '');
                        $ok = true;
                    }
                }
            } else {
                $langId = $dbObject->fetchAll('SELECT `id` FROM `language` WHERE `language` = "' . $userLang . '";');
                if (count($langId) > 0) {
                    $href = $this->composeUrl($pageId, $langId[0]['id']);
                    $ok = true;
                } else {
                    $langId = $dbObject->fetchAll('SELECT `id` FROM `language` WHERE `language` = "' . $default . '";');
                    if (count($langId) > 0) {
                        $href = $this->composeUrl($pageId, $langId[0]['id']);
                        $ok = true;
                    }
                }
            }

            if ($ok) {
                parent::redirectToUrl($href);
                parent::close();
            }
        }

        /**
         *
         * 	Pair UID with property and set it in scope.
         * 	C tag.
         * 	
         * 	@param		property					property name to pair
         * 	@param		scope							scope to set property un		 		 		 		 
         *
         */
        public function makePair($property, $scope) {
            global $dbObject;
            global $loginObject;

            $props = $dbObject->fetchAll('SELECT `property_value` FROM `pair_uid_property` WHERE `uid` = ' . $loginObject->getUserId() . ' AND `property_name` = "' . $property . '";');
            if (count($props) != 0) {
                $value = $props[0]['property_value'];
                switch ($scope) {
                    case 'get': $_GET[$property] = $value;
                        break;
                    case 'post': $_POST[$property] = $value;
                        break;
                    case 'request': $_REQUEST[$property] = $value;
                        break;
                    case 'session': $_SESSION[$property] = $value;
                        break;
                }
            }

            return;
        }

        public function setPageTitle($pageTitle) {
            $this->PageTitle = $pageTitle;
            return $this->PageTitle;
        }

        public function getPageTitle() {
            return $this->PageTitle;
        }

        /**
         *
         * 	Returns http host name
         * 	
         * 	@return		http host		 		 
         *
         */
        public function getHttpHost() {
            return $_SERVER['HTTP_HOST'];
        }

        public function getProtocol() {
            return $this->Protocol;
        }

        public function getRequestPath() {
            return $_SERVER['REQUEST_URI'];
        }

        /**
         *
         * 	Return current project id.
         *
         * 	@return		project id
         *
         */
        public function getProjectId() {
            return $this->ProjectId;
        }

        /**
         *
         * 	Returns requested path.
         * 	
         * 	@return		requested path		 		 		 
         *
         */
        public function getCurrentRequestPath() {
            return $this->FullUrl;
        }

        /**
         *
         * 	Current time stamp.
         * 	C tag.
         *
         */
        public function showTimestamp() {
            return time();
        }

        /**
         *
         * 	Returns web framework version.
         * 	C tag.		 
         *
         */
        public function getVersion() {
            return WEB_VERSION;
        }

        /**
         *
         * 	Returns cms framework version.
         * 	C tag.
         *
         */
        public function getCmsVersion() {
            return CMS_VERSION;
        }

        public function getDatabaseVersion() {
            return $this->getSystemProperty("db_version");
        }

        /**
         *
         * 	Return last update of last page in requested url.
         * 	C tag.		 
         *
         */
        public function getLastPageUpdate() {
            return date('H:i:s d.m.Y', $this->CurrentPageTimestamp);
        }

        /**
         *
         * 	Returns years from start year.
         * 	C tag.
         *
         * 	@param		year						start year
         *
         */
        public function getYearsFrom($year) {
            $thisYear = date('Y');
            if ($thisYear == $year) {
                return $year;
            } elseif ($year < $thisYear) {
                return $year . ' - ' . $thisYear;
            }
        }

        /**
         *
         * 	Returns value of system property.
         * 	C tag.
         * 	
         * 	@param		name						system property name		 		 		 
         *
         */
        public function getSystemPropertyValue($name) {
            return parent::system()->getPropertyValue($name);
        }

        /**
         *
         * 	Returns current page language id.
         *
         */
        public function getLanguageId() {
            return $this->LanguageId;
        }

        public function setProperty($prop, $value, $value2 = "xyz.1-23") {
            if ($value2 != "xyz.1-23") {
                $prop = $prop . ':' . $value;
                $value = $value2;
            }
        
            $this->PropertyAttr = $value;
            $this->PropertyUse = 'set';
            preg_replace_callback($this->PROP_RE, array(&$this, 'parsecproperty'), $prop);
        }

        public function getProperty($prop, $isEvaluated = true, $encode = "none") {
            $value = $prop;
            if ($isEvaluated) {
                $this->PropertyUse = 'get';
                $value = preg_replace_callback($this->PROP_RE, array(&$this, 'parsecproperty'), $prop);
            }

            if (empty($encode)) {
                $encode = $this->outEncode;
            }

            if ($encode == "html") {
                $value = htmlspecialchars($value);
            }

            return $value;
        }

        public function getOut($template, $encode = "none") {
            $value = $template();

            if (empty($encode)) {
                $encode = $this->outEncode;
            }

            if ($encode == "html") {
                $value = htmlspecialchars($value);
            }

            return $value;
        }

        private $outEncode = "";

        public function outDefaults($encode) {
            $this->outEncode = $encode;
        }

        // min, max, scope + property pro jeho vypsani!!!!
        public function generateRandomNumber($min = false, $max = false) {
            $min = is_numeric($min) ? $min : 0;
            $max = (is_numeric($max) && $max > $min) ? $max : 99999999;
            $r = rand($min, $max);

            parent::session()->set('web-rand', $r);
            return;
        }

        /**
         *
         * 	C fulltag.
         *
         */
        public function getWebFrame($template, $title, $open = false) {
            $return = parent::getFrame($title, $template(), '', $open == 'true' ? true : false);
            return $return;
        }

        /**
         *
         * 	C tag.
         *  Show value if this->LanguageName == lang
         *
         */
        public function showStaticText($value, $lang) {
            if ($this->LanguageName == $lang) {
                return $value;
            }
            return "";
        }

        /**
         *
         * 	Plain C tag for testing things ...		 
         *
         */
        public function plainFunction() {
            // $return = '';

            // //parent::db()->fetchAll(parent::query()->get('selectProjects', array(0 => 5, 1 => 6), 'sport'), true, true, true);

            // require_once("/scripts/php/classes/ui/BaseGrid.class.php");
            // require_once("/scripts/php/classes/ui/BaseForm.class.php");

            // $form = new BaseForm();
            // $form->setFormAttrs('text-form', 'post', $_SERVER['REQUEST_URI'], 'text-form-class-name');
            // $form->addField('text', 'name', 'Name:', 'Your name ...', 'w160', 'w300');
            // $form->addField('textarea', 'content', 'Content:', '', 'w160', 'w300');
            // $form->addDropDown('type', 'Type:', array(array('key' => 0, 'value' => 'Comment'), array('key' => 1, 'value' => 'New topic'), array('key' => 2, 'value' => 'Note')), 2, 'w160', 'w200');
            // $form->addSubmit('save', 'Save', 'save-button');

            // if ($form->isSubmited()) {
                // echo 'Submited ...<br />';
                // if ($form->pressed('save')) {
                    // echo 'Pressed "Save" ... <br />';
                    // print_r($_POST);

                    // $types = array('Comment', 'New topic', 'Note');

                    // $grid = new BaseGrid();
                    // $grid->setHeader(array('name' => 'Name:', 'content' => 'Content:', 'type' => 'Type:'));
                    // $grid->addRow(array('name' => $form->getValue('name'), 'content' => $form->getValue('content'), 'type' => $types[$form->getValue('type')]));
                    // $return .= $grid->render();
                // }
            // }


            // $return .= $form->render();

            // return $return;
            
            //require_once('scripts/php/classes/RoleHelper.class.php');
            //require_once('scripts/php/libs/FileAdmin.class.php');
            //RoleHelper::refreshCache();
            //RoleHelper::setRights(FileAdmin::$DirectoryRightDesc, 19, array(3, 14), array(1), WEB_R_READ);
            echo '<br />plain';
        }

        /**
         *
         * 	Try to find setuped error page or display default.
         * 		 
         * 	@param		errorCode				error code, 403, 404, ... , all		 		 
         *
         */
        public function generateErrorPage($errorCode, $detail = null) {
            $domainUrl = $_SERVER['HTTP_HOST'];
            $rootUrl = INSTANCE_URL;
            $virtualUrl = $this->getVirtualUrl();
            $fullUrl = UrlResolver::combinePath($domainUrl, UrlResolver::combinePath($rootUrl, $virtualUrl));

            if ($this->runHook(WebHook::ErrorPageBeforeForward, ["code" => $errorCode])) {
                return;
            }

            if ($this->processForwards($this->findForward(array($errorCode, 'All Errors')), UrlResolver::combinePath($this->Protocol, $fullUrl, '://'))) {
                return;
            }

            if ($this->runHook(WebHook::ErrorPageAfterForward, ["code" => $errorCode])) {
                return;
            }

            $relativePath = 'error' . $errorCode . '.html';
            $path = USER_PATH . $relativePath;
            if (!file_exists($path)) {
                $path = APP_PATH . $relativePath;
            }
            
            if ($errorCode == 404) {
                header("HTTP/1.1 404 Not Found");
                if (file_exists($path)) {
                    echo file_get_contents($path);
                } else {
                    echo '<h1 class="error">Error 404</h1><p class="error">Requested page doesn\'t exist.</p>';
                }
            } elseif ($errorCode == 403) {
                header("HTTP/1.1 403 Forbidden");
                if (file_exists($path)) {
                    echo file_get_contents($path);
                } else {
                    echo '<h1 class="error">Permission denied!</h1><p class="error">You can\'t read this page.</p>';
                }
            } elseif ($errorCode == 500) {
                header("HTTP/1.1 500 Internal Server Error");
                if (file_exists($path)) {
                    $content = file_get_contents($path);
                    if ($detail !== null) {
                        $content = str_replace("<!-- Error -->", $detail, $content);
                    }
                    echo $content;
                } else {
                    echo '<h1 class="error">Error 500</h1><p class="error">Unexpected internal server error.</p>';
                }
            } else {
                if (file_exists($path)) {
                    echo file_get_contents($path);
                } else {
                    echo '<h1 class="error">Error</h1><p class="error">Sorry, some error occurs.</p>';
                }
            }

            parent::close();
        }
        
        public function getPageId() {
            return $this->TempLoadedContent[count($this->TempLoadedContent) - 1]['id'];
        }
        
        public function showWhenConditionIsSatified($template, $when, $is, $isInverted) {
            $return = "";

            $condition = false;
            if ($is === true) {
                $condition = $when === true || $when == "true" || ($when != 'false' && (strlen($when) != 0));
            } else if (is_array($when)) {
                $condition = in_array($is, $when);
            } else if (is_array($is)) {
                $condition = in_array($when, $is);
            } else {
                $condition = $when == $is;
            }

            if ($isInverted == true) {
                $condition = !$condition;
            }

            if ($condition) {
                $return = $template();
            }

            return $return;
        }

        public function setDoctype($doctype) {
            if ($doctype == 'html5' || $doctype == 'xhtml' || $doctype == 'svg') {
                $this->Doctype = $doctype;
            }
        }

        public function getDoctype() {
            return $this->Doctype;
        }

        public function setFlushOptions($template = false, $contentType = false, $isZipEnabled = true, $statusCode = 0) {
            if ($template == 'null') {
                $this->Template = null;
            } else if ($template == 'xml') {
                $this->Template = 'xml';
            } else if ($template == 'none') {
                $this->Template = 'none';
            }

            if ($contentType != false && $contentType != '') {
                $this->ContentType = $contentType;
            }

            if ($statusCode > 0) {
                http_response_code($statusCode);
            }

            $this->setZipOutput($isZipEnabled);
        }

        public function getFavicon($url, $contentType) {
            return '<link rel="icon" type="' . $contentType . '" href="' . $url . '" />';
        }

        public function switchCondition($template, $when) {
            $result = '';

            $index = array_push($this->switchConditionWhenStack, $when) - 1;
            $template();
            array_pop($this->switchConditionWhenStack);

            if (array_key_exists($index, $this->switchConditionCaseStack)) {
                $result = $this->switchConditionCaseStack[$index];
                unset($this->switchConditionCaseStack[$index]);
            }

            return $result;
        }

        public function switchConditionCase($template, $is = 'x.x-def', $in = 'x.x-def') {
            $index = count($this->switchConditionWhenStack);
            if ($index == 0) {
                return '';
            }

            $index--;

            if (array_key_exists($index, $this->switchConditionCaseStack)) {
                return '';
            }

            $when = $this->switchConditionWhenStack[$index];
            $condition = $is === 'x.x-def' && $in === 'x.x-def';
            if (!$condition && $is !== 'x.x-def' && $when == $is) {
                $condition = true;
            }

            if (!$condition && $in !== 'x.x-def') {
                if (is_string($in)) {
                    $in = explode(",", $in);
                }
                
                foreach ($in as $item) {
                    if ($when == $item) {
                        $condition = true;
                        break;
                    }
                }
            }

            if ($condition === true) {
                $this->switchConditionCaseStack[$index] = $template();
            }

            return '';
        }

        public function debugMode($isEnabled) {
            if ($isEnabled) {
                $_SESSION["debug-mode"] = true;
            } else {
                unset($_SESSION["debug-mode"]);
            }
        }

        public function getDebugMode() {
            return array_key_exists("debug-mode", $_SESSION) && $_SESSION["debug-mode"] == true;
        }

        public function lookless($template) {
            $template();
            return "";
        }

        /* ================== PROPERTIES ================================================== */

        public function setChildPage($pageId) {
            $this->ChildPageId = $pageId;
            return $pageId;
        }

        public function getChildPage() {
            return $this->ChildPageId;
        }

        public function getCurrentPage() {
            return $this->TempLoadedContent[$this->PagesIdIndex - 1]['id'];
        }

        public function getCurrentPageName() {
            return $this->TempLoadedContent[$this->PagesIdIndex - 1]['name'];
        }

        public function getCurrentPageTitle() {
            // parse title
            $page = $this->TempLoadedContent[$this->PagesIdIndex - 1];
            if (strlen($page['title']) > 0) {
                return $this->executeTemplateContent(TemplateCacheKeys::page($page["id"], $this->LanguageId, "title"), $page['title']);
            }
        }

        public function getLastPageName() {
            return $this->TempLoadedContent[count($this->TempLoadedContent) - 1]['name'];
        }

        public function getLastPageTitle() {
            // parse title
            $page = $this->TempLoadedContent[count($this->TempLoadedContent) - 1];
            if (strlen($page['title']) > 0) {
                return $this->executeTemplateContent(TemplateCacheKeys::page($page["id"], $this->LanguageId, "title"), $page['title']);
            }
        }

        public function getCurrentTime() {
            return time();
        }

        public function getTodayTimestamp() {
            $date = new DateTime();
            $date->setTimestamp(time());
            $date->setTime(0, 0);
            return $date->getTimestamp();
        }

        public function setRandomNumber($val) {
            parent::session()->set('web-rand', $val);
            return $val;
        }

        public function getRandomNumber() {
            if (parent::session()->exists('web-rand')) {
                return parent::session()->get('web-rand');
            } else {
                return parent::getError('Random number is not set!');
            }
        }

        public function getLanguageIdWhenParsing() {
            $languageId = $this->LanguageId;
            if($languageId == 0) {
                $languageId = parent::request()->get('language-id', 'web');
            }

            if(!is_null($languageId) && is_numeric($languageId) && $languageId > 0) {
                return $languageId;
            }

            return null;
        }

        private $IsInsideForm = false;
        
        public function getIsInsideForm() {
            return $this->IsInsideForm;
        }
        
        public function setIsInsideForm($value) {
            return $this->IsInsideForm = $value;
        }

        public function getLastPageId() {
            return $this->TempLoadedContent[count($this->TempLoadedContent) - 1]['id'];
        }

        public function appendToHead($template) {
            $this->PageHead .= $template();
        }

        private $cacheOutput = [];

        public function cacheOutput($template, $key1, $key2, $key3, $key4, $key5) {
            FileUtils::ensureDirectory(CACHE_OUTPUT_PATH);

            $key = sha1("$key1-$key2-$key3-$key4-$key5");

            $path = FileUtils::combinePath(CACHE_OUTPUT_PATH, "$key.output");
            if (array_key_exists($key, $this->cacheOutput)) {
                return $this->cacheOutput[$key];
            } else if (file_exists($path)) {
                $this->cacheOutput[$key] = $content = file_get_contents($path);
            } else {
                $this->cacheOutput[$key] = $content = $template();
                file_put_contents($path, $content);
            }

            return $content;
        }

        private $hooks = [];

        private function runHook($name, $params) {
            if (array_key_exists($name, $this->hooks)) {
                foreach ($this->hooks[$name] as $handler) {
                    if ($handler($params)) {
                        return true;
                    }
                }
            }

            return false;
        }

        public function addHook($name, $handler) {
            if (!array_key_exists($name, $this->hooks)) {
                $this->hooks[$name] = [];
            }

            $this->hooks[$name][] = $handler;
        }

        private $entrypoints = [];

        public function addEntrypoint(string $moduleId, string $id, string $displayName, callable $handler) {
            $this->entrypoints[] = [
                "moduleId" => $moduleId,
                "id" => $id,
                "displayName" => $displayName,
                "handler" => $handler
            ];
        }

        public function getEntrypointsInfo() {
            $data = [];
            foreach ($this->entrypoints as $entrypoint) {
                $module = Module::getById($entrypoint["moduleId"]);
                $data[] = [
                    "moduleId" => $entrypoint["moduleId"],
                    "moduleName" => $module->alias,
                    "id" => $entrypoint["id"],
                    "displayName" => $entrypoint["displayName"]
                ];
            }

            usort($data, function($a, $b) {
                $result = strcmp($a["moduleName"], $b["moduleName"]);
                if ($result === 0) {
                    $result = strcmp($a["displayName"], $b["displayName"]);
                }

                return $result;
            });

            return $data;
        }
    }

    class WebHook {

        // A hook executed before any CMS content processing.
        public const ProcessRequestBeforeCms = "processRequest-beforeCms";

        // A hook executed when generating error page, but after processing forwards.
        // Parameters contains status code (404, 403, etc).
        public const ErrorPageBeforeForward = "errorPage-beforeForward";
        
        // A hook executed when generating error page, but after processing forwards.
        // Parameters contains status code (404, 403, etc).
        public const ErrorPageAfterForward = "errorPage-afterForward";
    }

?>