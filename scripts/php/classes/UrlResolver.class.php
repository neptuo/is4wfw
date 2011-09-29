<?php

/**
 *
 *  Require base tag lib class.
 *
 */
require_once("scripts/php/libs/BaseTagLib.class.php");
require_once("scripts/php/classes/CustomTagParser.class.php");

/**
 *
 * 	UrlResolver
 *
 */
class UrlResolver extends BaseTagLib {

    private $PROP_RE = '(([a-zA-Z0-9]+:[a-zA-Z0-9]+))';
    private $PropertyUse = '';
    private $PropertyAttr = '';
    private $Parser;
    private $WebProject = array();
    private $Language = array();
    private $PagesId = array();

    public function __construct() {
        $this->Parser = new CustomTagParser();
    }

    public function resolveUrl($domainUrl, $rootUrl, $pageUrl) {
        //echo $url;
        $domainProtocol = $_SERVER['HTTPS'];
        $otherProtocol = '';

        if ($domainProtocol == "on") {
            $domainProtocol = "https";
            $otherProtocol = "http";
        } else {
            $domainProtocol = "http";
            $otherProtocol = "https";
        }

        //echo $domainProtocol.' :: '.$domainUrl.' :: '.$rootUrl.' :: '.$pageUrl.'<br />';

        $urls = parent::db()->fetchAll('select `id`, `project_id`, `domain_url`, `root_url`, `virtual_url`, `http`, `https` from `web_url` where `enabled` = 1;');
        $selected = array();
        foreach ($urls as $url) {
            if ($url[$domainProtocol] != 1) {
                continue;
            }
            $projDoms = parent::str_tr($url['domain_url'], '.');
            $reqDoms = parent::str_tr($domainUrl, '.');
            if (count($projDoms) != count($reqDoms)) {
                continue;
            } else {
                $ok = true;
                foreach ($projDoms as $key => $dom) {
                    $fdom = self::parseSingleUrlPart($dom, $reqDoms[$key]);
                    //$this->PropertyAttr = $reqDoms[$key];
                    //$fdom = preg_replace_callback($this->PROP_RE, array( &$this,'parsecproperty'), $dom);
                    if ($fdom != $reqDoms[$key]) {
                        $ok = false;
                        break;
                    }
                }
                if ($ok) {
                    // Domeny sedi, pridame do selected.
                    $selected[] = $url;
                } else {
                    continue;
                }
            }
        }
        //print_r($selected);
        $selected2 = array();
        foreach ($selected as $url) {
            if ($url['root_url'] == '' && $rootUrl == '') {
                $selected2[] = $url;
            } elseif ($rootUrl != '') {
                $projRoots = parent::str_tr($url['root_url'], '/');
                $reqRoots = parent::str_tr($rootUrl, '/');
                if (count($projRoots) != count($reqRoots)) {
                    continue;
                } else {
                    $ok = true;
                    foreach ($projRoots as $key => $root) {
                        $froot = self::parseSingleUrlPart($root, $reqRoots[$key]);
                        //$this->PropertyAttr = $reqRoots[$key];
                        //$froot = preg_replace_callback($this->PROP_RE, array( &$this,'parsecproperty'), $root);
                        if ($froot != $reqRoots[$key]) {
                            $ok = false;
                            break;
                        }
                    }
                    if ($ok) {
                        $selected2[] = $url;
                    } else {
                        continue;
                    }
                }
            }
        }
        //print_r($selected2);
        $pageUrls = parent::str_tr($pageUrl, '/');
        foreach ($selected2 as $url) {
            if ($url['virtual_url'] == '') {
                // prejit na parsovani url stranek
                //print_r($pageUrls);
                if (self::parsePageUrl($pageUrls, $url['project_id'])) {
                    // mame viteze
                    self::selectProject($url);
                    return true;
                } else {
                    continue;
                }
            } else {
                $virUrls = parent::str_tr($url['virtual_url'], '/');
                if (count($virUrls) > count($pageUrls)) {
                    continue;
                } else {
                    $ok = true;
                    $key = 0;
                    foreach ($virUrls as $key => $vir) {
                        $fvir = self::parseSingleUrlPart($vir, $pageUrls[$key]);
                        //$this->PropertyAttr = $pageUrls[$key];
                        //$fvir = preg_replace_callback($this->PROP_RE, array( &$this,'parsecproperty'), $vir);
                        if ($fvir != $pageUrls[$key]) {
                            $ok = false;
                            break;
                        }
                    }
                    if ($ok) {
                        // prejit na parsovani url stranek
                        $key++;
                        //print_r(self::subarray($pageUrls, $key));
                        if (self::parsePageUrl(self::subarray($pageUrls, $key), $url['project_id'])) {
                            // mame viteze
                            self::selectProject($url);
                            return true;
                        } else {
                            continue;
                        }
                    } else {
                        continue;
                    }
                }
            }
        }
        return false;
    }

    private function parsePageUrl($pageUrls, $projectId) {
        // Vyber jazyk, pokud neni prazdny
        $langs = array();
        if (!parent::request()->exists('language-id', 'web')) {
            $langs = parent::db()->fetchAll('select `id`, `language` from `language`;');
        } else {
            $langs = parent::db()->fetchAll('select `id`, `language` from `language` where `id` = ' . parent::request()->get('language-id', 'web') . ';');
        }
        $langs = parent::db()->fetchAll('select `id`, `language` from `language`;');
        foreach ($langs as $lang) {
            if ($lang['language'] == $pageUrls[0] || $lang['language'] == '') {
                // Nalezen jazyk, zkusit najit stranky, jinak se vratit, a projit i dalsi jazyky
                $found = true;
                $curPageUrls = $lang['language'] == '' ? $pageUrls : self::subarray($pageUrls, 1);
                $parentId = 0;
                if (self::parsePageUrlWithLang($curPageUrls, $projectId, $parentId, $lang['id'])) {
                    $this->Language = $lang;
                    return true;
                }
            }
        }
        return false;
    }

    private function parsePageUrlWithLang($pageUrls, $projectId, $parentId, $langId) {
        //echo 'UrlsCount: '.count($pageUrls).'<br />';
        $pages = parent::db()->fetchAll('select `id`, `href`, `tag_lib_start`, `tag_lib_end` from `info` left join `page` on `info`.`page_id` = `page`.`id` left join `content` on `info`.`page_id` = `content`.`page_id` and `info`.`language_id` = `content`.`language_id` where `parent_id` = ' . $parentId . ' and `info`.`language_id` = ' . $langId . ' and `wp` = ' . $projectId . ';');
        if (count($pageUrls) == 0 && count($pages) == 0) {
            return true;
        }
        foreach ($pages as $page) {
            // !!!!
            self::parseContentForCustomTags($page['tag_lib_start']);
            //$this->Parser->setContent($page['tag_lib_start']);
            //$this->Parser->startParsing();

            $found = true;
            $key = 0;
            if ($page['href'] != '') {
                $hrefs = parent::str_tr($page['href'], '/');
                foreach ($hrefs as $key => $href) {
                    // !!!! Jeste je potreba parsovat custom tagy s tlstart a tlend !!!!
                    $fhref = self::parseSingleUrlPart($href, $pageUrls[$key]);
                    //$this->PropertyAttr = $pageUrls[$key];
                    //$fhref = preg_replace_callback($this->PROP_RE, array( &$this,'parsecproperty'), $href);
                    //echo '"'.$fhref.'" == "'.$pageUrls[$key].'"<br />';
                    if ($fhref != $pageUrls[$key]) {
                        self::parseContentForCustomTags($page['tag_lib_end']);
                        $found = false;
                        //break;
                    }
                }
            } else {
                if($pageUrls[0] != '') {
                    $key--;
                }
            }
            if ($found) {
                //echo 'Page: '.$page['id'].'<br />';
                $this->PagesId[] = $page['id'];
                //print_r($this->PagesId);
                // rekurze
                if (self::parsePageUrlWithLang(self::subarray($pageUrls, $key + 1), $projectId, $page['id'], $langId)) {
                    self::parseContentForCustomTags($page['tag_lib_end']);
                    return true;
                } else {
                    //echo 'PagesCount: '.count($this->PagesId).'<br />';
                    $this->PagesId = self::subarray($this->PagesId, 0, count($this->PagesId) - 1);
                }
            }

            // !!!!
            self::parseContentForCustomTags($page['tag_lib_end']);
            //$this->Parser->setContent($page['tag_lib_end']);
            //$this->Parser->startParsing();
        }
        return false;
    }

    public function parseSingleUrlPart($part, $value) {
        $this->PropertyUse = 'set';
        $this->PropertyAttr = $value;
        return preg_replace_callback($this->PROP_RE, array(&$this, 'parsecproperty'), $part);
    }

    public function parseContentForCustomTags($content) {
        $this->Parser->setContent($content);
        $this->Parser->startParsing();
    }

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
			if(is_array($cprop)) {
				return $cprop[0];
			} else {
				return $cprop;
			}
        }
    }

    public function selectProject($url) {
        $this->WebProject = parent::db()->fetchSingle('select `id`, `name`, `error_all_pid`, `error_404_pid`, `error_403_pid` from `web_project` where `id` = ' . $url['project_id'] . ';');
        $this->WebProject['alias'] = $url;
    }

    public function selectProjectById($id, $domainUrl, $rootUrl, $virtualUrl) {
        $this->WebProject = parent::db()->fetchSingle('select `id`, `name`, `error_all_pid`, `error_404_pid`, `error_403_pid` from `web_project` where `id` = ' . $id . ';');
        $this->WebProject['alias'] = parent::db()->fetchSingle('select `id`, `project_id`, `domain_url`, `root_url`, `virtual_url`, `http`, `https`, `default` from `web_url` where `domain_url` = "' . $domainUrl . '" and `root_url` = "' . $rootUrl . '" and `virtual_url` = "' . $virtualUrl . '";');
    }

    public function selectLanguage($id) {
        $this->Language = parent::db()->fetchSingle('select `id`, `language` from `language` where `id` = ' . $id . ';');
    }

    public function subarray($origin, $fromIndex, $toIndex = -1) {
        $ret = array();
        if ($toIndex == -1) {
            $toIndex = count($origin);
        }
        for ($i = $fromIndex; $i < $toIndex; $i++) {
            $ret[] = $origin[$i];
        }
        return $ret;
    }

    public function getWebProject() {
        return $this->WebProject;
    }

    public function getWebProjectId() {
        return $this->WebProject['id'];
    }

    public function setWebProject($webProject) {
        $this->WebProject = $webProject;
    }

    public function getLanguage() {
        return $this->Language;
    }

    public function setLanguage($language) {
        $this->Language = $language;
    }

    public function getLanguageId() {
        return $this->Language['id'];
    }

    public function getPagesId() {
        return $this->PagesId;
    }

    public function setPagesId($pagesId) {
        $this->PagesId = $pagesId;
    }

    public static function parseScriptRoot($url, $scriptName = 'index.php') {
        $url = substr($url, 1, strpos($url, '/' . $scriptName));
        if (strlen($url) > 0) {
            return substr($url, 0, strlen($url) - 1);
        }
        return $url;
    }

    public static function combinePath($path1, $path2, $delim = '/') {
        if($path1 == $delim) {
            if($path2{0} == $delim) {
                return $path2;
            } else {
                return $delim.$path2;
            }
        }
        if ($path1 != '' && $path2 != '' && $path1{strlen($path1) - 1} != $delim && $path2{0} != $delim) {
            return $path1 . $delim . $path2;
        } elseif ($path1 == $delim && $path2 == $delim) {
            return $path1;
        } elseif ($path1{strlen($path1) - 1} == $delim && $path2{0} == $delim) {
            return substr(0, $path1{strlen($path1) - 1}) . $path2;
        } else {
            return $path1 . $path2;
        }
    }

}

?>