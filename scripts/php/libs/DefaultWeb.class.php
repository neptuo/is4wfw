<?php

/**
 *
 *  Require base tag lib class.
 *
 */
require_once("BaseTagLib.class.php");
require_once("scripts/php/classes/RequestStorage.class.php");
require_once("scripts/php/classes/SessionStorage.class.php");
require_once("scripts/php/classes/QueryStorage.class.php");
require_once("scripts/php/classes/Diagnostics.class.php");
require_once("scripts/php/classes/FullTagParser.class.php");
require_once("scripts/php/classes/UniversalPermission.class.php");
require_once("scripts/php/classes/UrlResolver.class.php");
require_once("scripts/php/classes/UrlCache.class.php");
require_once("scripts/php/classes/ViewHelper.class.php");
require_once("scripts/php/classes/manager/WebForwardManager.class.php");

/**
 *
 *  Main web object.
 *  Take care of life-cycle of web application.
 *  Default object.
 *  
 *  @objectname webObject
 *  
 *  @author     Marek SMM
 *  @timestamp  2010-12-13
 *
 */
class DefaultWeb extends BaseTagLib {

    /**
     *
     *  Handle caching on/off.
     *     
     */
    private $IsCached = true;
    /**
     *
     *  Dir name for storing caches.
     *
     */
    private $CacheDir = "cache";
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
    private $PageScripts = "";
    private $PageStyles = "";
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
    //private $TAG_RE = '(<([a-zA-Z0-9]+:[a-zA-Z0-9]+) ((([a-zA-Z0-9]+)="([a-zA-Z0-9\.,\*`_;:/?-]+ *[a-zA-Z0-9\.,\*`_;:/?-]*)*" )*)\/>)';
    //private $TAG_RE = '(<([a-zA-Z0-9]+:[a-zA-Z0-9]+) ((([a-zA-Z0-9]+)="[^"]*" )*)\/>)';  ///2010-10-11
    protected $TAG_RE = '(<([a-zA-Z0-9]+:[a-zA-Z0-9]+)( )+((([a-zA-Z0-9-]+[:]?[a-zA-Z0-9-]*)="[^"]*"( )*)*)\/>)';
    private $PROP_RE = '(([a-zA-Z0-9]+:[a-zA-Z0-9]+))';
    private $PropertyUse = '';
    private $PropertyAttr = '';
    /**
     *
     *  Regular expression for parsing attribute.
     *
     */
    //private $ATT_RE = '(([a-zA-Z0-9]+)="([a-zA-Z0-9\.\*`_;:/?-]+ *[a-zA-Z0-9\.,\*`_;:/?-]*)*")';
    private $ATT_RE = '(([a-zA-Z0-9]+)="([^"]*)")';
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
    /**
     *
     * 	Keywords.
     *
     */
    private $keywords = '';

    /**
     *
     *  Initializes object.
     *
     */
    public function __construct() {
        self::setTagLibXml("xml/DefaultWeb.xml");

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

        //print_r(UniversalPermission::getPermissions(1, 1));
        //UniversalPermission::setPermissions('pes', 1, array(array('group' => 'admins', 'type' => WEB_R_READ), array('group' => 'web-admins', 'type' => WEB_R_WRITE)));
        //echo UniversalPermission::showPermissionsFormPart('pes', 1, array('admins'), WEB_R_READ);
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

    public function processRequestNG() {
        $this->UrlResolver = new UrlResolver();
        $this->UrlCache = new UrlCache();

        $found = false;
        $domainUrl = $_SERVER['HTTP_HOST'];
        $rootUrl = UrlResolver::parseScriptRoot($_SERVER['SCRIPT_NAME']);
        $virtualUrl = $_REQUEST['WEB_PAGE_PATH'];
        $fullUrl = UrlResolver::combinePath($domainUrl, UrlResolver::combinePath($rootUrl, $virtualUrl));
        $item = $this->UrlCache->read($fullUrl);
        
        if ($_SERVER['HTTPS'] == "on") {
            $this->Protocol = 'https';
        } else {
            $this->Protocol = 'http';
        }

        $this->FullUrl = $fullUrl;
        
        // Projit Forwardy s Always
        self::processForwards(self::findForward(array('Always')), UrlResolver::combinePath($this->Protocol, $fullUrl, '://'));
        

        if ($item != array()) {
            // Stranka jiz je v urlcache
            $this->UrlResolver->setPagesId(self::parsePagesId($item['pages_id']));
            $this->UrlResolver->selectProjectById($item['project_id'], $item['domain_url'], $item['root_url'], $item['virtual_url']);
            $this->UrlResolver->selectLanguage($item['language_id']);
            $this->UrlCache->updateLastCache($fullUrl);

            self::loadPageData();
            self::parseSingleUrlParts($domainUrl, $rootUrl, $virtualUrl);
            $found = true;
        } else {
            if ($this->UrlResolver->resolveUrl($domainUrl, $rootUrl, $virtualUrl)) {
                // Stranka existuje
                self::loadPageData();

                // Ulozit do urlcache
                $this->UrlCache->write($fullUrl, $this->UrlResolver->getWebProject(), self::pagesIdAsString('-'), $this->UrlResolver->getLanguage(), self::findCachetime());
                $found = true;
            } else {
                // Stranka neexistuje -> Projit Forwardy s 404 nebo All Errors
                self::processForwards(self::findForward(array('404', 'All Errors')), UrlResolver::combinePath($this->Protocol, $fullUrl, '://'));

                header("HTTP/1.1 404 Not Found");
                echo file_get_contents("error404.html");
                exit;
            }
        }

        if ($found) {
            self::doOldSetup();
            $this->PageContent = self::getContent();
            self::flush();
        }
    }

    public function substituteRequestFor($pageId, $langId) {
        //echo 'Substituting ...'.'<br />'.'<br />';

        $pageUrl = self::composeUrl($pageId, $langId);
        $url = parent::db()->fetchSingle('select `http`, `https`, `domain_url`, `root_url`, `virtual_url` from `web_url` join `page` on `web_url`.`project_id` = `page`.`wp` where `page`.`id` = '.$pageId.' order by `web_url`.`default` desc, `web_url`.`id`;');

        $pageUrl = substr($pageUrl, strlen($url['root_url']) + 1, strlen($pageUrl));
        $scriptUrl = UrlResolver::combinePath($url['root_url'], '/index.php');
        $domainUrl = $url['domain_url'];

        /*echo $_SERVER['HTTP_HOST'].'<br />';
        echo $_SERVER['SCRIPT_NAME'].'<br />';
        echo $_REQUEST['WEB_PAGE_PATH'].'<br />';
        echo $domainUrl.'<br />';
        echo $scriptUrl.'<br />';
        echo $pageUrl.'<br />';*/

        $_SERVER['HTTP_HOST'] = $domainUrl;
        $_SERVER['SCRIPT_NAME'] = $scriptUrl;
        $_REQUEST['WEB_PAGE_PATH'] = $pageUrl;

        if($this->Protocol == 'https' && $url['https'] == 1) {
            $_SERVER['https'] = 'on';
        }

        self::processRequestNG();
    }

    private function loadPageData() {
        $this->TempLoadedContent = self::sortPages(parent::db()->fetchAll("SELECT `id`, `name`, `href`, `in_title`, `keywords`, `title`, `tag_lib_start`, `tag_lib_end`, `head`, `content`, `info`.`timestamp`, `cachetime` FROM `content` LEFT JOIN `page` ON `content`.`page_id` = `page`.`id` LEFT JOIN `info` ON `content`.`page_id` = `info`.`page_id` AND `content`.`language_id` = `info`.`language_id` WHERE `info`.`is_visible` = 1 AND `info`.`language_id` = " . $this->UrlResolver->getLanguageId() . " AND `page`.`id` IN (" . self::pagesIdAsString() . ") AND `page`.`wp` = " . $this->UrlResolver->getWebProjectId() . ";"), $this->UrlResolver->getPagesId());
    }

    private function parsePagesId($item) {
        return parent::str_tr($item, '-');
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
        foreach ($this->TempLoadedContent as $page) {
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

        $reqDoms = parent::str_tr($domainUrl, '.');
        $prjDoms = parent::str_tr($webProject['alias']['domain_url'], '.');
        foreach ($prjDoms as $key => $part) {
            $this->UrlResolver->parseSingleUrlPart($part, $reqDoms[$key]);
        }

        $reqRoots = parent::str_tr($rootUrl, '/');
        $prjRoots = parent::str_tr($webProject['alias']['root_url'], '/');
        foreach ($prjRoots as $key => $part) {
            $this->UrlResolver->parseSingleUrlPart($part, $reqRoots[$key]);
        }

        $reqVirs = parent::str_tr($virtualUrl, '/');
        $prjVirs = self::prepareVirtualPathAsArray($webProject, $lang);
        self::parseAllPagesTagLib('tag_lib_start');
        foreach ($reqVirs as $key => $vir) {
            //echo 'URL: '.$vir.' : '.$prjVirs[$key].'<br />';
            $this->UrlResolver->parseSingleUrlPart($prjVirs[$key], $vir);
        }
        self::parseAllPagesTagLib('tag_lib_end');
    }

    private function prepareVirtualPathAsArray($webProject, $lang) {
        $pages = array();
        foreach ($this->TempLoadedContent as $page) {
            if ($page['href'] != '') {
                $pages = array_merge($pages, parent::str_tr($page['href'], '/'));
            }
        }
        if (strlen($lang['language']) > 0) {
            $pages = array_merge(array($lang['language']), $pages);
        }
        $virUrl = parent::str_tr($webProject['alias']['virtual_url'], '/');
        if(count($virUrl) == 1 && $virUrl[0] == '') {
            return $pages;
        }
        return array_merge($virUrl, $pages);
    }

    private function parseAllPagesTagLib($tl) {
        //print_r($this->TempLoadedContent);
        foreach ($this->TempLoadedContent as $page) {
            $this->UrlResolver->parseContentForCustomTags($page[$tl]);
        }
    }

    private function doOldSetup() {
        $webProject = $this->UrlResolver->getWebProject();
        $this->ProjectId = $webProject['id'];

        $language = $this->UrlResolver->getLanguage();
        $this->LanguageId = $language['id'];
        $this->LanguageName = $language['language'];

        $this->PagesId = $this->UrlResolver->getPagesId();

        if (array_key_exists('__START_ID', $_REQUEST) && in_array($_REQUEST['__START_ID'], $this->PagesId)) {
            for ($i = 0; $i < count($this->PagesId); $i++) {
                if ($this->PagesId[$i] == $_REQUEST['__START_ID']) {
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
            if(preg_match($rule, $fullUrl) > 0) {
                // Presmerovat
                if($forward->getType() == 'Substitute') {
                    self::substituteRequestFor($forward->getPageId(), $forward->getLangId());
                } elseif($forward->getType() == 'Forward') {
                    self::redirectTo($forward->getPageId(), $forward->getLangId());
                }
            }
        }
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
                $parsed_url = $phpObject->str_tr($project['url'], '/', 1);
                $project['url'] = $parsed_url[0];
                //$temp_url_rrc = preg_replace_callback($this->TAG_RE, array( &$this,'parsectag'), $project['url']);
                $temp_url = $phpObject->str_tr($project['url'], '.');
                $temp_req = $phpObject->str_tr($domainUrl, '.', 1);
                for ($j = 0; $j < count($temp_url); $j++) {
                    $this->PropertyAttr = $temp_req[0];
                    $this->PropertyUse = 'set';
                    $temp_url_rrc = preg_replace_callback($this->PROP_RE, array(&$this, 'parsecproperty'), $temp_url[$j]);
                    if ($temp_url_rrc == $temp_req[0]) {
                        $temp_req = $phpObject->str_tr($temp_req[1], '.', 1);
                    } else {
                        $ok = false;
                    }
                }

                $path = $phpObject->str_tr($this->Path, '/', 1);
                if ($ok && $parsed_url[1] != '') {
                    $temp_path = $phpObject->str_tr($parsed_url[1], '/');
                    for ($j = 0; $j < count($temp_path); $j++) {
                        $this->PropertyAttr = $path[0];
                        $this->PropertyUse = 'set';
                        $temp_path_rrc = preg_replace_callback($this->PROP_RE, array(&$this, 'parsecproperty'), $temp_path[$j]);
                        if ($temp_path_rrc == $path[0]) {
                            $prj_add_url .= '/' . $temp_path_rrc;
                            $path = $phpObject->str_tr($path[1], '/', 1);
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
                        header('Location: ' . $otherProtocol . '://' . $domainUrl . '/' . $this->Path);
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
                    $parsed_url = $phpObject->str_tr($project['url'], '/', 1);
                    $project['url'] = $parsed_url[0];
                    //$temp_url_rrc = preg_replace_callback($this->TAG_RE, array( &$this,'parsectag'), $project['url']);
                    echo $project['url'] . '<br />';
                    $temp_url = $phpObject->str_tr($project['url'], '.');
                    $temp_req = $phpObject->str_tr($domainUrl, '.', 1);
                    for ($j = 0; $j < count($temp_url); $j++) {
                        $this->PropertyAttr = $temp_req[0];
                        $this->PropertyUse = 'set';
                        $temp_url_rrc = preg_replace_callback($this->PROP_RE, array(&$this, 'parsecproperty'), $temp_url[$j]);
                        echo $temp_url_rrc . " == " . $temp_req[0] . "<br />";
                        if ($temp_url_rrc == $temp_req[0]) {
                            $ok = true;
                            $temp_req = $phpObject->str_tr($temp_req[1], '.', 1);
                        } else {
                            $ok = false;
                            break;
                        }
                    }
                    echo "<br /><br />";

                    echo $this->Path . '<br />';
                    $path = $phpObject->str_tr($this->Path, '/', 1);
                    if ($ok && $parsed_url[1] != '') {
                        $temp_path = $phpObject->str_tr($parsed_url[1], '/');
                        for ($j = 0; $j < count($temp_path); $j++) {
                            $this->PropertyAttr = $path[0];
                            $this->PropertyUse = 'set';
                            $temp_path_rrc = preg_replace_callback($this->PROP_RE, array(&$this, 'parsecproperty'), $temp_path[$j]);
                            echo $temp_path_rrc . " == " . $path[0] . '<br />';
                            if ($temp_path_rrc == $path[0]) {
                                $prj_add_url .= '/' . $temp_path_rrc;
                                $path = $phpObject->str_tr($path[1], '/', 1);
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
                            header('Location: ' . $otherProtocol . '://' . $domainUrl . '/' . $this->Path);
                            //echo '<a href="'.$otherProtocol.'://'.$domainUrl.'/'.$this->Path.'">Redirect to ...</a>';
                            //exit;
                        } else {
                            $ok = false;
                        }
                    }
                }
            }

            if ($ok == false) {
                header("HTTP/1.1 404 Not Found");
                echo '<h1 class="error">Error 404</h1><p class="error">Requested page doesn\'t exists.</p>';
                exit;
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

            self::parsePages($this->Path, 0);

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

            $path = $phpObject->str_tr($this->Path, '/', 1);
            if ($ok && $parsed_url[1] != '') {
                $temp_path = $phpObject->str_tr($parsed_url[1], '/');
                for ($j = 0; $j < count($temp_path); $j++) {
                    $this->PropertyAttr = $path[0];
                    $this->PropertyUse = 'set';
                    $temp_path_rrc = preg_replace_callback($this->PROP_RE, array(&$this, 'parsecproperty'), $temp_path[$j]);
                    $path = $phpObject->str_tr($path[1], '/', 1);
                }
            }

            // Old code ...
            $this->CacheInfo['id'] = $ucache[0]['id'];
            $this->CacheInfo['cachetime'] = $ucache[0]['cachetime'];
            $this->CacheInfo['lastcache'] = $ucache[0]['lastcache'];
            $this->CacheInfo['path'] = "cache/pages/page-" . $ucache[0]['page-ids'] . ".cache.html";
            if ($ucache[0]['cachetime'] != -1 && ($ucache[0]['cachetime'] == 0 || $ucache[0]['lastcache'] > time()) && is_file($this->CacheInfo['path'])) {
                self::tryToComprimeContent(file_get_contents($this->CacheInfo['path']));
            } else {
                $pages = $ucache[0]['page-ids'];
                $this->PagesId = $phpObject->str_tr($pages, '-');
            }
        }

        $str = '';
        for ($i = 0; $i < count($this->PagesId); $i++) {
            $str .= $this->PagesId[$i];
            if ($i < (count($this->PagesId) - 1)) {
                $str .= ', ';
            }
        }

        if (array_key_exists('__START_ID', $_REQUEST) && in_array($_REQUEST['__START_ID'], $this->PagesId)) {
            for ($i = 0; $i < count($this->PagesId); $i++) {
                if ($this->PagesId[$i] == $_REQUEST['__START_ID']) {
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

        $this->TempLoadedContent = self::sortPages($dbObject->fetchAll("SELECT `id`, `name`, `href`, `in_title`, `keywords`, `title`, `tag_lib_start`, `tag_lib_end`, `head`, `content`, `info`.`timestamp` FROM `content` LEFT JOIN `page` ON `content`.`page_id` = `page`.`id` LEFT JOIN `info` ON `content`.`page_id` = `info`.`page_id` AND `content`.`language_id` = `info`.`language_id` WHERE `info`.`is_visible` = 1 AND `info`.`language_id` = " . $this->LanguageId . " AND `page`.`id` IN (" . $str . ") AND `page`.`wp` = " . $this->ProjectId . ";"), $this->PagesId);
        $this->CurrentPageTimestamp = $this->TempLoadedContent[count($this->TempLoadedContent) - 1]['timestamp'];
        if (count($this->TempLoadedContent) == count($this->PagesId)) {
            foreach ($this->TempLoadedContent as $page) {
                preg_replace_callback($this->TAG_RE, array(&$this, 'parsectag'), $page['tag_lib_start']);
            }

            // Parse domain for setup dynamic properties
            $temp_req = $phpObject->str_tr($this->ProjectUrl, '.', 1);
            $prj_add_url = '';
            $parsed_url = $phpObject->str_tr($this->ProjectUrlDef, '/', 1);
            $project['url'] = $parsed_url[0];
            $temp_url = $phpObject->str_tr($project['url'], '.');
            for ($j = 0; $j < count($temp_url); $j++) {
                $this->PropertyAttr = $temp_req[0];
                $this->PropertyUse = 'set';
                $temp_url_rrc = preg_replace_callback($this->PROP_RE, array(&$this, 'parsecproperty'), $temp_url[$j]);
                if ($temp_url_rrc == $temp_req[0]) {
                    $temp_req = $phpObject->str_tr($temp_req[1], '.', 1);
                } else {
                    $ok = false;
                }
            }

            // Kvuli jazyku s neprazdnou url!!!
            if ($this->LanguageId != 1) {
                $this->Url = $phpObject->str_tr($this->Url, '/', 1);
                $this->Url = $this->Url[1];
            }

            // Parse url for setup dynamic properties
            $path = $phpObject->str_tr($this->Url, '/', 1);
            $temp_path = $phpObject->str_tr($this->UrlDef, '/');
            for ($j = 0; $j < count($temp_path); $j++) {
                $this->PropertyAttr = $path[0];
                $this->PropertyUse = 'set';
                $temp_path_rrc = preg_replace_callback($this->PROP_RE, array(&$this, 'parsecproperty'), $temp_path[$j]);
                $path = $phpObject->str_tr($path[1], '/', 1);
            }

            foreach ($this->TempLoadedContent as $page) {
                preg_replace_callback($this->TAG_RE, array(&$this, 'parsectag'), $page['tag_lib_end']);
            }

            $this->PageContent = self::getContent();
        } else {
            self::generateErrorPage($this->ProjectId, 404);
        }

        self::flush();
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

        $path = $phpObject->str_tr($path, '/', 1);
        $return = $dbObject->fetchAll("SELECT `info`.`page_id`, `info`.`href` , `info`.`cachetime`, `content`.`tag_lib_start`, `content`.`tag_lib_end` FROM `info` LEFT JOIN `page` ON `info`.`page_id` = `page`.`id` LEFT JOIN `content` ON `info`.`page_id` = `content`.`page_id` AND `info`.`language_id` = `content`.`language_id` WHERE `page`.`parent_id` = " . $parentId . " AND `info`.`language_id` = " . $this->LanguageId . " AND `page`.`wp` = " . $this->ProjectId . " ORDER BY `info`.`href` DESC;");
        //echo $path[0];

        $this->CurrentDynamicPath = $path[0];
        $this->ParsingPages = true;
        if (count($return) == 0 && ($path[0] != "" || $path[1] != "")) {
            if ($_REQUEST['temp-stop'] != 'stop') {
                //echo 'Generate err page!';
                $_REQUEST['temp-stop'] = 'stop';
                self::generateErrorPage($this->ProjectId, 404);
            } else {
                echo 'Bad!';
                exit;
            }
        } elseif (count($return) == 0 && $path[0] == "" && $path[1] == "") {
            if (count($this->PagesId) == 0) {
                self::generateErrorPage($this->ProjectId, 404);
            } else {
                return;
            }
        } else {
            $pathCache = $path;
            for ($i = 0; $i < count($return); $i++) {
                $ok = true;
                //$temp_path_rrc = preg_replace_callback($this->TAG_RE, array( &$this,'parsectag'), $return[$i]['href']);
                $temp_path = $phpObject->str_tr($return[$i]['href'], '/');
                for ($j = 0; $j < count($temp_path); $j++) {
                    //echo '<br />['.$i.']['.$j.']'.$temp_path[$j].' === '.$path[0];
                    //$temp_path_rrc = preg_replace_callback($this->TAG_RE, array( &$this,'parsectag'), $temp_path[$j]);
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
                        $path = $phpObject->str_tr($path[1], '/', 1);
                    } else {
                        $path = $pathCache;
                        $ok = false;
                        break;
                    }
                }
                //exit;
                // Dynamic rewrite ==== IMPORTANT ---------------
                //$tmp_path = preg_replace_callback($this->TAG_RE, array( &$this,'parsectag'), $return[$i]['href']);
                // ----------------------------------------------

                if ($ok/* $tmp_path == $path[0] */) {
                    $this->ParsingPages = true;
                    preg_replace_callback($this->TAG_RE, array(&$this, 'parsectag'), $return[$i]['tag_lib_start']);

                    $this->PagesId[] = $return[$i]['page_id'];
                    // Otestovat!!!!!!!!!
                    if ($this->CacheTime != -1 && ($return[$i]['cachetime'] < $this->CacheTime || $this->CacheTime == 0)) {
                        if ($return[$i]['cachetime'] != 0) {
                            $this->CacheTime = $return[$i]['cachetime'];
                        } elseif ($this->CacheTime == 10000000000) {
                            $this->CacheTime = $return[$i]['cachetime'];
                        }
                    }
                    self::parsePages($path[0] . '/' . $path[1], $return[$i]['page_id']);

                    preg_replace_callback($this->TAG_RE, array(&$this, 'parsectag'), $return[$i]['tag_lib_end']);

                    $this->ParsingPages = false;
                    return;
                }
            }
            for ($i = 0; $i < count($return); $i++) {
                $tmp_path = preg_replace_callback($this->TAG_RE, array(&$this, 'parsectag'), $return[$i]['href']);
                if ($tmp_path == "") {
                    $this->ParsingPages = true;
                    preg_replace_callback($this->TAG_RE, array(&$this, 'parsectag'), $return[$i]['tag_lib_start']);

                    $this->PagesId[] = $return[$i]['page_id'];
                    // Otestovat!!!!!!!!!
                    if ($this->CacheTime != -1 && ($return[$i]['cachetime'] < $this->CacheTime || $this->CacheTime == 0)) {
                        if ($return[$i]['cachetime'] != 0) {
                            $this->CacheTime = $return[$i]['cachetime'];
                        } elseif ($this->CacheTime == 10000000000) {
                            $this->CacheTime = $return[$i]['cachetime'];
                        }
                    }

                    self::parsePages(($tmp_path == $path[0]) ? $path[1] : $path[0] . '/' . $path[1], $return[$i]['page_id']);

                    preg_replace_callback($this->TAG_RE, array(&$this, 'parsectag'), $return[$i]['tag_lib_end']);

                    $this->ParsingPages = false;
                    return;
                }
            }

            self::generateErrorPage($this->ProjectId, 404);
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
            trigger_error("Passed value [allow] is not valid in cache [DefaultWeb]!", E_USER_WARNING);
            return;
        }
        if (!is_numeric($time)) {
            trigger_error("Passed value [time] is not valid in cache [DefaultWeb]!", E_USER_WARNING);
            return;
        }

        if ($this->IsCached && $this->ParsingPages == false) {
            $name = 'page';
            foreach ($this->PagesId as $pg) {
                $name .= '-' . $pg;
            }
            $name .= '.cache.html';
            $path = $_SERVER['DOCUMENT_ROOT'] . WEB_ROOT . $this->CacheDir . '/pages/';
            $cacheMTime = filemtime($path . $name);

            if (file_exists($path . $name) && is_readable($path . $name) && ($cacheMTime > (time() - $time))) {
                //echo $cacheMTime;
                echo file_get_contents($path . $name);
                exit;
            } else {
                $this->CacheFile = $name;
            }
        }
    }

    /**
     *
     *  Flushes page content to output.
     *
     *  @param  path  string containing path template file ( implied )
     *  @return none
     *
     */
    public function flush($path = null) {
        $_SESSION['last-request']['pages-id'] = $this->PagesId;

        foreach ($this->PagesId as $page) {
            $rights = parent::db()->fetchAll("SELECT `group`.`name` FROM `group` LEFT JOIN `page_right` ON `group`.`gid` = `page_right`.`gid` WHERE `page_right`.`pid` = " . $page . " AND `type` = " . WEB_R_READ . " AND (`group`.`gid` IN (" . parent::login()->getGroupsIdsAsString() . ") OR `group`.`parent_gid` IN (" . parent::login()->getGroupsIdsAsString() . "));");
            if (count($rights) == 0) {
                self::generateErrorPage($this->ProjectId, 403);
            }
        }

        $lang = parent::db()->fetchAll("SELECT `language` FROM `language` WHERE `id` = " . $this->LanguageId . ";");
        $lang = $lang[0]['language'];

        $keywords = file_get_contents("keywords.txt");

        // Diagnostics
        $diacont = "";
        if (array_key_exists('mem-stats', $_GET)) {
            $diacont = $this->Diagnostics->printMemoryStats();
        }
        if (array_key_exists('duration-stats', $_GET)) {
            $diacont .= $this->Diagnostics->printDuration();
        }
        if (array_key_exists('query-stats', $_GET)) {
            $diacont .= ''
                    . '<div style="border: 2px solid #666666; margin: 10px; padding: 10px; background: #eeeeee;">'
                    . '<div style="color: red; font-weight: bold;">Database queries:</div>'
                    . '<div>' . parent::db()->getQueriesPerRequest() . '</div>'
                    . '</div>';
        }

        if (strtolower($_REQUEST['__TEMPLATE']) == 'xml') {
            $return = ''
                    . '<rssmm:response>'
                    . ((strlen($this->PageLog) != 0) ? ''
                            . '<rssmm:log>'
                            . $this->PageLog
                            . '</rssmm:log>' :
                            '')
                    . '<rssmm:head>'
                    . '<rssmm:title>' . $this->PageTitle . '</rssmm:title>'
                    . '<rssmm:keywords>' . ((strlen($this->Keywords) > 0) ? $this->Keywords . ',' : '') . ((strlen($keywords) > 0) ? $keywords . ',' : '') . 'wfw,rssmm</rssmm:keywords>'
                    . '<rssmm:styles>' . $this->PageStyles . '</rssmm:styles>'
                    . '<rssmm:scripts>' . $this->PageScripts . '</rssmm:scripts>'
                    . '</rssmm:head>'
                    . '<rssmm:content>' . $this->PageContent . '</rssmm:content>'
                    . '</rssmm:response>';
        } else {
            $return = ''
                    . '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'
                    . '<html xmlns="http://www.w3.org/1999/xhtml">'
                    . '<head>'
                    . '<meta http-equiv="content-type" content="text/html; charset=utf-8" />'
                    . '<meta name="description" content="' . $this->PageTitle . '" />'
                    . '<meta name="keywords" content="' . ((strlen($this->Keywords) > 0) ? $this->Keywords . ',' : '') . ((strlen($keywords) > 0) ? $keywords . ',' : '') . 'wfw,rssmm" />'
                    . '<meta http-equiv="Content-language" content="' . $lang . '" />'
                    . '<meta name="robots" content="all, index, follow" />'
                    . '<meta name="author" content="Marek Fišera" />'
                    . '<title>' . $this->PageTitle . '</title>'
                    . $this->PageHead . $this->PageStyles . $this->PageScripts
                    . '</head>'
                    . '<body>' . $this->PageContent . $diacont . '</body>'
                    . '</html>';
        }
        $return = self::resolveWebRoot($return);

        if ($this->IsCached) {
            $webProject = $this->UrlResolver->getWebProject();
            $path = $_SERVER['DOCUMENT_ROOT'] . (UrlResolver::combinePath(WEB_ROOT, $webProject['alias']['root_url'])) . $this->CacheDir . '/pages/';
            $name = $this->CacheFile;
            file_put_contents($path . $name, $return);
        }

        // Rewrite anchors
        $return = preg_replace_callback('(&web:page=([0-9]+))', array(&$this, 'parseproperties'), $return);
        // Generate web:frames
        $return = preg_replace_callback('(<web:frame( title="([^"]*)")*( open="(true|false)")*>(((\s*)|(.*))*)</web:frame>)', array(&$this, 'parsepostframes'), $return);

        if ($this->CacheInfo['cachetime'] != -1) {
            parent::db()->execute('UPDATE `urlcache` SET `lastcache` = ' . (time() + $this->CacheInfo['cachetime']) . ' WHERE `id` = ' . $this->CacheInfo['id'] . ';');
            file_put_contents($this->CacheInfo['path'], $return);
        }

        self::tryToComprimeContent($return);
    }

    private function resolveWebRoot($content) {
        $webProject = $this->UrlResolver->getWebProject();
        $rootUrl = UrlResolver::combinePath(WEB_ROOT, $webProject['alias']['root_url']);
        $rootUrl = UrlResolver::combinePath($rootUrl, '/');
        $content = str_replace("~/", $rootUrl, $content);
        return $content;
    }

    private function parseproperties($values) {
        $path = self::composeUrl($values[1], $this->LanguageId);
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

        //file_put_contents('databasequeries.txt', parent::db()->getQueriesPerRequest());

        $return = $content;

        if ($this->ZipOutput && $encoding) {
            header('Content-Encoding: ' . $encoding);
            print("\x1f\x8b\x08\x00\x00\x00\x00\x00");
            $size = strlen($return);
            $return = gzcompress($return, 9);
            $return = substr($return, 0, $size);
            print($return);
            exit();
        } else {
            echo $return;
            exit();
        }
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
    public function composeUrl($pageId, $languageId = false, $absolutePath = false) {
        $languageId = ($languageId === false) ? $this->LanguageId : $languageId;
        $lastPageId = 0;
        $pageProjectId = 0;
        if (!is_numeric($pageId)) {
            return ViewHelper::resolveUrl($pageId);
        }
        while ($pageId != 0) {
            $return = parent::db()->fetchAll("SELECT `parent_id`, `href`, `wp` FROM `page` LEFT JOIN `info` ON `page`.`id` = `info`.`page_id` WHERE `page`.`id` = " . $pageId . " AND `info`.`language_id` = " . $languageId . ";");
            if (count($return) == 1) {
                if (strlen($return[0]['href']) != 0 && !preg_match($this->TAG_RE, $return[0]['href'])) {
                    $return[0]['href'] = preg_replace_callback($this->PROP_RE, array(&$this, 'parsecproperty'), $return[0]['href']);
                    $this->CurrentPath = "/" . $return[0]['href'] . $this->CurrentPath;
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

        $return = parent::db()->fetchAll("SELECT `language` FROM `language` WHERE `id` = " . $languageId . ";");
        if (count($return) == 1) {
            if (strlen($return[0]['language']) == 0) {
                $this->CurrentPath = substr($this->CurrentPath, 1, strlen($this->CurrentPath));
            }
            $tmpPath = WEB_ROOT . $return[0]['language'] . $this->CurrentPath;
            $this->CurrentPath = "";
        }

        if ($pageProjectId == $this->ProjectId) {
            // Dosestav url z dat v urlResolveru
            return self::addSpecialParams(self::composeUrlProjectPart($tmpPath, $this->UrlResolver->getWebProject(), $absolutePath));
        } else {
            // Najdi project url a dosestav url
            $project = array('alias' => parent::db()->fetchSingle('select `domain_url`, `root_url`, `virtual_url`, `http`, `https` from `web_url` where `project_id` = ' . $pageProjectId . ' and `enabled` = 1 order by `web_url`.`default` desc, `web_url`.`id`;'));
            if($project['alias'] != array()) {
                return self::addSpecialParams(self::composeUrlProjectPart($tmpPath, $project, true));
            } else {
                $message = parent::getError('Project doesn\' have url adress!!');
                trigger_error($message, E_USER_WARNING);
                return '#';
            }
        }
    }

    private function composeUrlProjectPart($pageUrl, $project, $absolute) {
        $pageUrl = UrlResolver::combinePath($project['alias']['virtual_url'], $pageUrl);
        $pageUrl = UrlResolver::combinePath($project['alias']['root_url'], $pageUrl);

        if ($absolute) {
            $pageUrl = UrlResolver::combinePath($project['alias']['domain_url'], $pageUrl);

            $other = ($this->Protocol == 'http') ? 'https' : 'http';
            $prot = ($project['alias'][$this->Protocol] == 1) ? $this->Protocol : $other;

            $pageUrl = UrlResolver::combinePath($prot, $pageUrl, '://');
        } else {
            //echo $pageUrl.'<br />';
            $pageUrl = UrlResolver::combinePath('/', $pageUrl);
            //echo $pageUrl.'<br />';
        }
        return $pageUrl;
    }

    public function addParamToUrl($url, $param, $value) {
        $pair = ($value != '') ? $param . '=' . $value : $param;
        if (strpos($url, '?') == '') {
            $url .= '?' . $pair;
        } else {
            if (strpos($url, $param, strpos($url, '?')) == '') {
                $url .= '&' . $pair;
            }
        }
        return $url;
    }

    private function addSpecialParams($url) {
        if (array_key_exists('mem-stats', $_GET)) {
            $url = self::addParamToUrl($url, 'mem-stats', '');
        }
        if (array_key_exists('duration-stats', $_GET)) {
            $url = self::addParamToUrl($url, 'duration-stats', '');
        }
        if (array_key_exists('query-stats', $_GET)) {
            $url = self::addParamToUrl($url, 'query-stats', '');
        }
        if (array_key_exists('auto-login-ignore', $_GET)) {
            $url = self::addParamToUrl($url, 'auto-login-ignore', '');
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
     *  Function parses custom tag, call right function & return content.
     *  
     *  @param  ctag  custom tag as string
     *  @return return of custom tag function     
     *
     */
    private function parsectag($ctag) {
        $object = explode(":", $ctag[1]);
        $attributes = array();
        $this->Attributes = array();

        preg_replace_callback($this->ATT_RE, array(&$this, 'parseatt'), $ctag[3]);

        foreach ($this->Attributes as $tmp) {
            $att = explode("=", $tmp);
            if (strlen($att[0]) > 0) {
                $this->PropertyAttr = '';
                $this->PropertyUse = 'get';
                $att[1] = preg_replace_callback($this->PROP_RE, array(&$this, 'parsecproperty'), $att[1]);
                $attributes[$att[0]] = str_replace("\"", "", $att[1]);
            }
        }

        global $phpObject;
        if ($phpObject->isRegistered($object[0]) && $phpObject->isTag($object[0], $object[1], $attributes)) {
            $attributes = $phpObject->sortAttributes($object[0], $object[1], $attributes);

            global ${$object[0] . "Object"};
            $func = $phpObject->getFuncToTag($object[0], $object[1]);
            if ($func && ($attributes !== false)) {
                $attstring = "";
                $i = 0;
                foreach ($attributes as $att) {
                    $attstring .= "'" . $att . "'";
                    if ($i < (count($attributes) - 1)) {
                        $attstring .= ", ";
                    }
                    $i++;
                }
                eval('$return =  ${$object[0]."Object"}->{$func}(' . $attstring . ');');
                return $return;
            }
        } else {
            echo "<h4 class=\"error\">This tag isn't registered! [" . $object[0] . "]</h4>";
            return "";
        }
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
        if ($phpObject->isRegistered($object[0]) && $phpObject->isProperty($object[0], $object[1])) {
            global ${$object[0] . "Object"};
            $func = $phpObject->getFuncToProperty($object[0], $object[1], $this->PropertyUse);
            eval('$return =  ${$object[0]."Object"}->{$func}("' . $this->PropertyAttr . '");');
            return $return;
        } else {
            //echo "<h4 class=\"error\">This tag isn't registered! [".$object[0]."]</h4>";
            return $cprop;
        }
    }

    /**
     *
     *  Generates page content and parse c tags.
     *  
     *  $return page content          
     *
     */
    public function getContent() {
        global $phpObject;
        global $dbObject;

        $path = $phpObject->str_tr($this->Path, '/', 1);
        $return = $this->TempLoadedContent[$this->PagesIdIndex];

        preg_replace_callback($this->TAG_RE, array(&$this, 'parsectag'), $return['tag_lib_start']);

        if (count($this->PagesId) > ($this->PagesIdIndex + 1)) {
            self::setChildPage($this->PagesId[$this->PagesIdIndex + 1]);
        } else {
            self::setChildPage(-1);
        }

        //$this->CurrentPageTimestamp = $this->TempLoadedContent[$this->PagesIdIndex]['timestamp'];
        $this->CurrentDynamicPath = $path[0];
        $tmp_path = preg_replace_callback($this->TAG_RE, array(&$this, 'parsectag'), $return['href']);

        $this->ParentId = $this->PagesId[$this->PagesIdIndex];
        $this->PagesIdIndex++;

        if ($return['in_title'] == 1) {
            // parse title
            if (strlen($return['title']) > 0) {
                $this->PropertyAttr = '';
                $this->PropertyUse = 'get';
                $this->PageTitle = preg_replace_callback($this->TAG_RE, array(&$this, 'parsectag'), $return['title']) . " - " . $this->PageTitle;
                //$this->PageTitle = preg_replace_callback($this->PROP_RE, array( &$this,'parsecproperty'), $return['title'])." - ".$this->PageTitle;
                //$this->PageTitle = $return['title']." - ".$this->PageTitle;
            }
        }
        //$this->PageHead .= $return['head'];
        if (strlen($return['keywords']) > 0) {
            $return['keywords'] = preg_replace_callback($this->TAG_RE, array(&$this, 'parsectag'), $return['keywords']);
        }
        $this->Keywords .= ( (strlen($return['keywords']) != 0) ? ((strlen($this->Keywords) != 0) ? ',' . $return['keywords'] : $return['keywords']) : '');

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

        $files = $dbObject->fetchAll("SELECT `page_file`.`id`, `page_file`.`name`, `page_file`.`type` FROM `page_file_inc` LEFT JOIN `page_file` ON `page_file_inc`.`file_id` = `page_file`.`id` WHERE `page_file_inc`.`page_id` = " . $this->ParentId . " AND `page_file_inc`.`language_id` = " . $this->LanguageId . " AND `for_all` = 1 OR `" . $browser . "` = 1;");
        foreach ($files as $file) {
            switch ($file['type']) {
                //case WEB_TYPE_CSS: $this->PageHead .= '<link rel="stylesheet" href="'.WEB_ROOT.'css/'.$file['id'].'-'.str_replace(' ', '-', strtolower($file['name'])).'" type="text/css" />'; break;
                //case WEB_TYPE_JS: $this->PageHead .= '<script type="text/javascript" src="'.WEB_ROOT.'js/'.$file['id'].'-'.str_replace(' ', '-', strtolower($file['name'])).'"></script>'; break;
                case WEB_TYPE_CSS: $this->PageStyles .= ( (strtolower($_REQUEST['__TEMPLATE']) == 'xml') ? '<rssmm:link-ref>~/file.php?fid=' . $file['id'] . '</rssmm:link-ref>' : '<link rel="stylesheet" href="~/file.php?fid=' . $file['id'] . '" type="text/css" />');
                    break;
                case WEB_TYPE_JS: $this->PageScripts .= ( (strtolower($_REQUEST['__TEMPLATE']) == 'xml') ? '<rssmm:script-ref>~/file.php?fid=' . $file['id'] . '</rssmm:script-ref>' : '<script type="text/javascript" src="~/file.php?fid=' . $file['id'] . '"></script>');
                    break;
            }
        }

        $this->Path = $path[1];

        $this->PageHead .= preg_replace_callback($this->TAG_RE, array(&$this, 'parsectag'), $return['head']);
        $pageContent = preg_replace_callback($this->TAG_RE, array(&$this, 'parsectag'), $return['content']);

        //$pageContent = preg_replace_callback('(&web:pathToId=([0-9]+))', array( &$this,'parseproperties'), $pageContent);
        //$return = preg_replace_callback('(&web:pathToId=([0-9]+))', array( &$this,'parseproperties'), $return);

        preg_replace_callback($this->TAG_RE, array(&$this, 'parsectag'), $return['tag_lib_end']);

        $this->PagesIdIndex--;

        if ($this->PagesIdIndex > 0) {
            self::setChildPage($this->PagesId[$this->PagesIdIndex]);
        } else {
            self::setChildPage(-1);
        }

        return $pageContent;
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
    public function getMenu($parentId = false, $inner = false, $classes = false, $rel = false, $template = false, $inn = false) {
        global $dbObject;
        global $loginObject;
        if ($inn == false) {
            $inn = 1;
        }
        if ($inner == false) {
            $inner = 1000;
        }
        $parentId = (!$parentId) ? 0 : $parentId;
        $return = $dbObject->fetchAll("SELECT `page`.`id`, `info`.`name`, `info`.`href` FROM `page` LEFT JOIN `info` ON `page`.`id` = `info`.`page_id` WHERE `page`.`parent_id` = " . $parentId . " AND `info`.`in_menu` = 1 AND `is_visible` = 1 AND `info`.`language_id` = " . $this->LanguageId . " AND `page`.`wp` = " . $this->ProjectId . " ORDER BY `info`.`page_pos`;");
        if (count($return) > 0) {
            $content .= '<div class="menu menu-' . $inn . ((strlen($classes) > 0) ? ' ' . $classes : '') . '"><ul class="ul-' . $inn . '">';
            $i = 1;
            foreach ($return as $lnk) {
                $rights = $dbObject->fetchAll("SELECT `value` FROM `page_right` LEFT JOIN `group` ON `page_right`.`gid` = `group`.`gid` WHERE `pid` = " . $lnk['id'] . " AND `type` = " . WEB_R_READ . " AND (`group`.`gid` IN (" . $loginObject->getGroupsIdsAsString() . ") OR `group`.`parent_gid` IN (" . $loginObject->getGroupsIdsAsString() . "));");
                if (count($rights) == 0) {
                    continue;
                }
                $href = self::composeUrl($lnk['id'], $this->LanguageId);
                $parent = (in_array($lnk['id'], $this->PagesId)) ? true : false;
                //$active = (($lnk['id'] == $this->PagesId[count($this->PagesId) - 1]) ? true : false);

                $active = false;
                if ($href == '/' . $_REQUEST['WEB_PAGE_PATH']) {
                    $active = true;
                    $parent = false;
                }


                //$class = ($this->ParentId == $lnk['id']) ? " class=\"active-item\"" : "";
                if ($inner > 1) {
                    $tmpContent = self::getMenu($lnk['id'], $inner, (($parent || $active) ? 'active-submenu' : ''), false, $inn + 1);
                }

                $content .= ''
                        . '<li class="menu-item li-' . $i . (($parent) ? ' active-parent' : '') . (($active) ? ' active-item' : '') . ' ' . ((strlen($tmpContent) != 0) ? 'parent' : 'single') . '">'
                        . '<div class="link' . (($parent) ? ' active-parent-link' : '') . (($active) ? ' active-link' : '') . '">'
                        . '<a href="' . $href . '"' . (($rel != false) ? ' rel="' . $rel . '"' : '') . '>'
                        . '<span>' . $lnk['name'] . '</span>'
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

    /**
     *
     *  Generates language links.
     *  C tag.     
     *  
     *  @param  homePage  show home page in links
     *  @return generate links to language versions
     *
     */
    public function getLanguages($homePage = false) {
        global $phpObject;
        global $dbObject;

        if ($homePage) {
            $return = $dbObject->fetchAll("SELECT `language`.`id`, `language`.`language` FROM `info` LEFT JOIN `language` ON `info`.`language_id` = `language`.`id` LEFT JOIN `page` ON `info`.`page_id` = `page`.`id` WHERE `language`.`language` != \"\" AND `page`.`parent_id` = 0 AND `info`.`href` = \"\" ORDER BY `language`.`id`;");

            if (count($return) > 0) {
                $ret = "<div class=\"languages\">";
                foreach ($return as $lang) {
                    $ret .= '<a class="lang-' . $lang['language'] . (($this->LanguageId == $lang['id']) ? ' active-language' : '') . '" href="' . WEB_ROOT . $lang['language'] . '">' . $lang['language'] . '</a> ';
                }
                $ret .= "</div>";
                return $ret;
            } else {
                return "";
            }
        } else {
            $langs = array();
            $return = $dbObject->fetchAll("SELECT `id`, `language` FROM `language` WHERE `language` != \"\";");
            foreach ($return as $ln) {
                $names[$ln['id']] = $ln['language'];
                $langs[$ln['id']] = WEB_ROOT . $ln['language'];
            }

            foreach ($this->PagesId as $pg) {
                $return = $dbObject->fetchAll("SELECT `info`.`href`, `info`.`language_id` FROM `info` WHERE `info`.`page_id` = " . $pg . ";");
                foreach ($return as $ln) {
                    if ($ln['href'] != '') {
                        $langs[$ln['language_id']] .= '/' . $ln['href'];
                    }
                }
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
        $return .= '<a href="' . WEB_ROOT . $root[0]['language'] . '">' . strtoupper($root[0]['language']) . '</a> ';
        $path = WEB_ROOT . $root[0]['language'];

        foreach ($this->PagesId as $pg) {
            $page = $dbObject->fetchAll("SELECT `name`, `href` FROM `info` WHERE `page_id` = " . $pg . " AND `language_id` = " . $this->LanguageId . " AND `href` != \"\";");
            if (count($page) == 1) {
                $path .= '/' . $page[0]['href'];
                $return .= $delimeter . ' <a href="' . $path . '">' . $page[0]['name'] . '</a> ';
            }
        }

        return '<div class="crumb-menu">' . $return . '</div>';
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
    public function makeAnchor($pageId, $text = false, $languageId = false, $class = "", $id = "", $target = "", $rel = "", $type = '') {
        global $dbObject;
        $languageId = (!$languageId) ? $this->LanguageId : $languageId;

        if (is_numeric($pageId)) {
            $sql_return = $dbObject->fetchAll("SELECT `href` FROM `page` LEFT JOIN `info` ON `page`.`id` = `info`.`page_id` WHERE `page`.`id` = " . $pageId . " AND `info`.`language_id` = " . $languageId . ";");
        }
        if (count($sql_return) == 1 || !is_numeric($pageId)) {
            $url = self::composeUrl($pageId, $languageId);
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
                    return preg_replace_callback($this->TAG_RE, array(&$this, 'parsectag'), $return[0]['content']);
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
        $languageId = (!$languageId) ? $this->LanguageId : $languageId;

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
            $template = $dbObject->fetchAll('SELECT `content` FROM `template` LEFT JOIN `template_right` ON `template`.`id` = `template_right`.`tid` LEFT JOIN `group` ON `template_right`.`gid` = `group`.`gid` WHERE `template`.`id` = ' . $templateId . ' AND (`group`.`gid` IN (' . $loginObject->getGroupsIdsAsString() . ') OR `group`.`parent_gid` IN (' . $loginObject->getGroupsIdsAsString() . '));');
            if (count($template) == 1) {
                require_once("scripts/php/classes/CustomTagParser.class.php");
                $Parser = new CustomTagParser();
                $Parser->setContent($template[0]['content']);
                $Parser->startParsing();
                $return = $Parser->getResult();
                return $return;
            } else {
                trigger_error('Permission denied when reading template id = ' . $templateId . '!', E_USER_WARNING);
            }
        }
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
            header("Location: " . $path);
            exit;
        } else {
            if (array_key_exists("path", $_REQUEST)) {
                // zkontrolovat odkaz, doplnit http ...
                header("Location: " . $_REQUEST['path']);
                exit;
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
     * 	@parem		langId		language id of page
     * 	@param		browser		browser name
     * 	@param		ip				client ip addresses separed by comma
     * 	@return		none
     *
     */
    public function redirectTo($pageId, $langId = false, $browser = false, $ip = false) {
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
            $ips = $phpObject->str_tr($ip, ',');
            if (in_array($_SERVER['REMOTE_ADDR'], $ips)) {
                $redirect = true;
            } else {
                $redirect = false;
            }
        }

        $i = 0;
        if (count($_GET) > 1 && strpos($key, $href) == -1) {
            foreach ($_GET as $key => $param) {
                if ($key != 'WEB_PAGE_PATH') {
                    if ($i == 0) {
                        $href .= '?' . $key . '=' . $param;
                    } else {
                        $href .= '&' . $key . '=' . $param;
                    }
                    $i++;
                }
            }
        }

        if ($redirect && $href != '#') {
            header("Location: " . $href);
            echo '<a href="' . $href . '">redirect to ...</a>';
            exit;
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
            $file[0]['content'] = str_replace("~/", WEB_ROOT, $file[0]['content']);

            header('Content-Type: ' . $fileType);
            header('Content-Length: ' . strlen($file[0]['content']));
            header('Content-Transfer-Encoding: binary');
            echo $file[0]['content'];
            exit;
        } else {
            trigger_error('Text File doesn\'t exists! [fileId = ' . $fileId . ']', E_USER_WARNING);
        }
    }

    /**
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
        $cdp = self::getCurrentDynamicPath();

        $id = $phpObject->str_tr($cdp, "-", 1);

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
            header('Location: ' . $href);
            exit;
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

    /**
     *
     * 	Returns current page title.
     *
     * 	@return		page title
     *
     */
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

    public function setProperty($prefix, $name, $value) {
        $this->PropertyAttr = $value;
        $this->PropertyUse = 'set';
        $name = preg_replace_callback($this->PROP_RE, array(&$this, 'parsecproperty'), $prefix . ':' . $name);
    }

    public function getProperty($name) {
        return $name;
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
    public function getWebFrame($content, $title, $open = false) {
        $parser = new FullTagParser();
        $parser->setContent($content);
        $parser->startParsing();
        $return = $parser->getResult();
        $return = parent::getFrame($title, $content, '', $open == 'true' ? true : false);

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
        $return = '';

        //parent::db()->fetchAll(parent::query()->get('selectProjects', array(0 => 5, 1 => 6), 'sport'), true, true, true);

        require_once("/scripts/php/classes/ui/BaseGrid.class.php");
        require_once("/scripts/php/classes/ui/BaseForm.class.php");

        $form = new BaseForm();
        $form->setFormAttrs('text-form', 'post', $_SERVER['REDIRECT_URL'], 'text-form-class-name');
        $form->addField('text', 'name', 'Name:', 'Your name ...', 'w160', 'w300');
        $form->addField('textarea', 'content', 'Content:', '', 'w160', 'w300');
        $form->addDropDown('type', 'Type:', array(array('key' => 0, 'value' => 'Comment'), array('key' => 1, 'value' => 'New topic'), array('key' => 2, 'value' => 'Note')), 2, 'w160', 'w200');
        $form->addSubmit('save', 'Save', 'save-button');

        if ($form->isSubmited()) {
            echo 'Submited ...<br />';
            if ($form->pressed('save')) {
                echo 'Pressed "Save" ... <br />';
                print_r($_POST);

                $types = array('Comment', 'New topic', 'Note');

                $grid = new BaseGrid();
                $grid->setHeader(array('name' => 'Name:', 'content' => 'Content:', 'type' => 'Type:'));
                $grid->addRow(array('name' => $form->getValue('name'), 'content' => $form->getValue('content'), 'type' => $types[$form->getValue('type')]));
                $return .= $grid->render();
            }
        }


        $return .= $form->render();

        return $return;
    }

    /**
     *
     * 	Try to find setuped error page or display default.
     * 		 
     * 	@param		projectId				project id
     * 	@param		errorCode				error code, 403, 404, ... , all		 		 
     *
     */
    private function generateErrorPage($projectId, $errorCode) {
        global $loginObject;
        global $dbObject;

        $info = $dbObject->fetchAll('SELECT `url`, `http`, `https`, `error_all_pid`, `error_404_pid`, `error_403_pid` FROM `web_project` LEFT JOIN `web_project_right` ON `web_project`.`id` = `web_project_right`.`wp` LEFT JOIN `group` ON `web_project_right`.`gid` = `group`.`gid` WHERE (`group`.`gid` IN (' . $loginObject->getGroupsIdsAsString() . ') OR `group`.`parent_gid` IN (' . $loginObject->getGroupsIdsAsString() . ')) AND `id` = ' . $projectId . ';');
        if (count($info) == 1) {
            switch ($errorCode) {
                case 404: $pid = $info[0]['error_404_pid'];
                    header("HTTP/1.1 404 Not Found");
                    break;
                case 403: $pid = $info[0]['error_403_pid'];
                    header("HTTP/1.1 403 Forbidden");
                    break;
                case 'all': $pid = $info[0]['error_all_pid'];
                    break;
            }
        }

        if ($pid != 0) {
            $wp = $dbObject->fetchAll('SELECT `url` FROM `web_project` LEFT JOIN `page` ON `web_project`.`id` = `page`.`wp` WHERE `page`.`id` = ' . $pid . ';');
            $url = '';
            $pageLang = parent::db()->fetchSingle('select `language_id` from `info` where `page_id` = ' . $pid . ' and `language_id` = ' . $this->LanguageId . ';');
            if ($pageLang != array()) {
                $url = self::composeUrl($pid, $this->LanguageId, 'no');
            } else {
                $pageLang = parent::db()->fetchSingle('select `language_id` from `info` where `page_id` = ' . $pid . ';');
                if ($pageLang != array()) {
                    $url = self::composeUrl($pid, $pageLang['language_id'], 'no');
                } else {
                    header("HTTP/1.1 404 Not Found");
                    echo '<h1 class="error">Error 404</h1><p class="error">Requested page doesn\'t exists.</p>';
                    exit;
                }
            }
            $_GET['WEB_PAGE_PATH'] = substr($url, 1, strlen($url));
            //$this->ServerName = substr($wp[0]['url'], 0, strpos($wp[0]['url'], '/'));
            if ($_SERVER['HTTPS'] == 'on' && $info[0]['https'] == 1) {
                $this->Https = 'on';
            } elseif ($info[0]['http'] == 1) {
                $this->Https = '';
            } else {
                if ($errorCode == 404) {
                    header("HTTP/1.1 404 Not Found");
                    echo '<h1 class="error">Error 404</h1><p class="error">Requested page doesn\'t exists.</p>';
                    exit;
                } elseif ($errorCode == 403) {
                    header("HTTP/1.1 403 Forbidden");
                    echo '<h1 class="error">Permission denied!</h1><p class="error">You can\'t read this page.</p>';
                    exit;
                } else {
                    echo '<h1 class="error">Error</h1><p class="error">Sorry, some error occurs.</p>';
                    exit;
                }
            }
            self::processRequest();
        } else {
            if ($errorCode == 404) {
                header("HTTP/1.1 404 Not Found");
                echo '<h1 class="error">Error 404</h1><p class="error">Requested page doesn\'t exist.</p>';
                exit;
            } elseif ($errorCode == 403) {
                header("HTTP/1.1 403 Forbidden");
                echo '<h1 class="error">Permission denied!</h1><p class="error">You can\'t read this page.</p>';
                exit;
            } else {
                echo '<h1 class="error">Error</h1><p class="error">Sorry, some error occurs.</p>';
                exit;
            }
        }
    }

    // PROPERTIES

    public function setChildPage($pageId) {
        $this->ChildPageId = $pageId;
        return $pageId;
    }

    public function getChildPage() {
        return $this->ChildPageId;
    }

    public function setCurrentPageName($name) {
        return $name;
    }

    public function getCurrentPageName() {
        return $this->TempLoadedContent[$this->PagesIdIndex - 1]['name'];
    }

    public function setCurrentTime($time) {
        return $time;
    }

    public function getCurrentTime() {
        return time();
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

}

?>