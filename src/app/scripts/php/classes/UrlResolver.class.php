<?php

    require_once(APP_SCRIPTS_PHP_PATH . "libs/BaseTagLib.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/TemplateParser.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/TemplateCacheKeys.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/utils/StringUtils.class.php");

    /**
     *
     * 	UrlResolver
     *
     */
    class UrlResolver extends BaseTagLib {

        private $PROP_RE = '(([a-zA-Z0-9-_]+:[a-zA-Z0-9-_.]+))';
        private $PropertyUse = '';
        private $PropertyAttr = '';
        private $WebProject = array();
        private $Language = array();
        private $PagesId = array();

        public function resolveUrl($domainUrl, $rootUrl, $pageUrl) {
            //echo $url;
            $domainProtocol = $_SERVER['HTTPS'];

            if ($domainProtocol == "on") {
                $domainProtocol = "https";
            } else {
                $domainProtocol = "http";
            }

            $urls = parent::db()->fetchAll('select wu.`id`, wu.`project_id`, wu.`domain_url`, wu.`root_url`, wu.`virtual_url`, wu.`http`, wu.`https`, wp.`entrypoint`, wp.`pageless` from `web_url` wu join `web_project` wp on wu.`project_id` = wp.`id` where `enabled` = 1;');

            // Domains
            $selected = array();
            foreach ($urls as $url) {
                if ($url[$domainProtocol] != 1) {
                    continue;
                }

                // Support for wildcard domains.
                if ($url['domain_url'] == "*") {
                    $url['domain_url'] = $domainUrl;
                    $selected[] = $url;
                    continue;
                }

                $projDoms = StringUtils::explode($url['domain_url'], '.');
                $reqDoms = StringUtils::explode($domainUrl, '.');
                if (count($projDoms) != count($reqDoms)) {
                    continue;
                } else {
                    $ok = true;
                    foreach ($projDoms as $key => $dom) {
                        $fdom = $this->parseSingleUrlPart($dom, $reqDoms[$key]);
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
            
            // Root path
            $selected2 = array();
            foreach ($selected as $url) {
                if ($url['root_url'] == '' && $rootUrl == '') {
                    $selected2[] = $url;
                } elseif ($rootUrl != '') {
                    $projRoots = StringUtils::explode($url['root_url'], '/');
                    $reqRoots = StringUtils::explode($rootUrl, '/');
                    if (count($projRoots) != count($reqRoots)) {
                        continue;
                    } else {
                        $ok = true;
                        foreach ($projRoots as $key => $root) {
                            $froot = $this->parseSingleUrlPart($root, $reqRoots[$key]);
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
            
            // Virtual url
            $pageUrls = StringUtils::explode($pageUrl, '/');
            foreach ($selected2 as $url) {
                if ($url['virtual_url'] == '') {
                    // prejit na parsovani url stranek
                    if (empty($url['entrypoint']) || $url['pageless']) {
                        if ($this->parsePageUrl($pageUrls, $url['project_id'])) {
                            // mame viteze
                            $this->selectProject($url);
                            return true;
                        } else {
                            continue;
                        }
                    }
                    
                    // First matched project with entrypoint is the winner.
                    $this->selectProject($url);
                    return true;
                } else {
                    $virUrls = StringUtils::explode($url['virtual_url'], '/');
                    if (count($virUrls) > count($pageUrls)) {
                        continue;
                    } else {
                        $ok = true;
                        $key = 0;
                        foreach ($virUrls as $key => $vir) {
                            $fvir = $this->parseSingleUrlPart($vir, $pageUrls[$key]);
                            if ($fvir != $pageUrls[$key]) {
                                $ok = false;
                                break;
                            }
                        }

                        if ($ok) {
                            if (empty($url['entrypoint']) && !$url['pageless']) {
                                // prejit na parsovani url stranek
                                $key++;
                                if ($this->parsePageUrl($this->subarray($pageUrls, $key), $url['project_id'])) {
                                    // mame viteze
                                    $this->selectProject($url);
                                    return true;
                                } else {
                                    continue;
                                }
                            } else {
                                // First matched project with entrypoint is the winner.
                                $this->selectProject($url);
                                return true;
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
            $langs = parent::db()->fetchAll('select `id`, `language` from `language`;');

            $oldLangId = parent::request()->get('language-id', 'web');
            foreach ($langs as $lang) {
                if ($lang['language'] == $pageUrls[0] || $lang['language'] == '') {
                    // Nalezen jazyk, zkusit najit stranky, jinak se vratit, a projit i dalsi jazyky
                    $found = true;
                    $curPageUrls = $lang['language'] == '' ? $pageUrls : $this->subarray($pageUrls, 1);
                    $parentId = 0;
                    parent::request()->set('language-id', $lang['id'], 'web');
                    if ($this->parsePageUrlWithLang($curPageUrls, $projectId, $parentId, $lang['id'])) {
                        $this->setLanguage($lang);
                        return true;
                    }
                }
            }

            parent::request()->set('language-id', $oldLangId, 'web');
            return false;
        }

        private function parsePageUrlWithLang($pageUrls, $projectId, $parentId, $langId) {
            //echo 'UrlsCount: '.count($pageUrls).'<br />';
            $pages = parent::db()->fetchAll('select `id`, `href`, `tag_lib_start`, `tag_lib_end` from `info` left join `page` on `info`.`page_id` = `page`.`id` left join `content` on `info`.`page_id` = `content`.`page_id` and `info`.`language_id` = `content`.`language_id` where `parent_id` = ' . $parentId . ' and `info`.`language_id` = ' . $langId . ' and `wp` = ' . $projectId . ';');
            if (count($pageUrls) == 0 && count($pages) == 0) {
                return true;
            }
            foreach ($pages as $page) {
                $this->parseContentForCustomTags(TemplateCacheKeys::page($page["id"], $langId, "tag_lib_start"), $page['tag_lib_start']);

                $found = true;
                $key = 0;
                if ($page['href'] != '') {
                    $hrefs = StringUtils::explode($page['href'], '/');
                    foreach ($hrefs as $key => $href) {
                        $fhref = $this->parseSingleUrlPart($href, $pageUrls[$key]);
                        if ($fhref != $pageUrls[$key]) {
                            $this->parseContentForCustomTags(TemplateCacheKeys::page($page["id"], $langId, "tag_lib_end"), $page['tag_lib_end']);
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
                    if ($this->parsePageUrlWithLang($this->subarray($pageUrls, $key + 1), $projectId, $page['id'], $langId)) {
                        $this->parseContentForCustomTags(TemplateCacheKeys::page($page["id"], $langId, "tag_lib_end"), $page['tag_lib_end']);
                        return true;
                    } else {
                        //echo 'PagesCount: '.count($this->PagesId).'<br />';
                        $this->PagesId = $this->subarray($this->PagesId, 0, count($this->PagesId) - 1);
                    }
                }

                $this->parseContentForCustomTags(TemplateCacheKeys::page($page["id"], $langId, "tag_lib_end"), $page['tag_lib_end']);
            }
            return false;
        }

        public function parseSingleUrlPart($part, $value) {
            $this->PropertyUse = 'set';
            $this->PropertyAttr = $value;
            return preg_replace_callback($this->PROP_RE, array(&$this, 'parsecproperty'), $part);
        }

        private function parseContentForCustomTags(array $keys, string $content) {
            $this->executeTemplateContent($keys, $content);
        }

        private function parsecproperty($cprop) {
            $object = explode(":", $cprop[1]);
            $attributes = array();
            $this->Attributes = array();

            global $phpObject;
            if ($phpObject->isRegistered($object[0])) {
                if ($phpObject->isProperty($object[0], $object[1])) {
                    global ${$object[0] . "Object"};
                    $func = $phpObject->getFuncToProperty($object[0], $object[1], $this->PropertyUse);
                    eval('$return =  ${$object[0]."Object"}->{$func}("' . $this->PropertyAttr . '");');
                    return $return;
                } else if($phpObject->isAnyProperty($object[0])) {
                    global ${$object[0] . "Object"};
                    $func = $this->PropertyUse == 'set' ? 'setProperty' : 'getProperty';
                    eval('$return =  ${$object[0]."Object"}->{$func}("' . $object[1] . '", "' . $this->PropertyAttr . '");');
                    return $return;
                }
            }
            
            if (is_array($cprop)) {
                return $cprop[0];
            } else {
                return $cprop;
            }
        }

        private function loadProjectById($projectId) {
            $this->WebProject['id'] = $projectId;
        }

        public function selectProject($url) {
            $this->loadProjectById($url['project_id']);
            $this->WebProject['alias'] = $url;
            $this->WebProject['entrypoint'] = $url["entrypoint"];
            $this->WebProject['pageless'] = $url["pageless"];
        }

        public function selectProjectById($cacheItem) {
            $this->loadProjectById($cacheItem['project_id']);
            $this->WebProject['alias'] = array(
                'project_id' => $cacheItem['project_id'],
                'domain_url' => $cacheItem['domain_url'],
                'root_url' => $cacheItem['root_url'],
                'virtual_url' => $cacheItem['virtual_url'],
                'http' => $cacheItem['http'],
                'https' => $cacheItem['https'],
            );
            
            $this->WebProject['pageless'] = false;
            if ($cacheItem["language_id"] == 0) {
                if (empty($cacheItem["pages_id"])) {
                    $this->WebProject['pageless'] = true;
                } else {
                    $this->WebProject['entrypoint'] = $cacheItem["pages_id"];
                }
            }
        }

        public function selectLanguage($id) {
            $language = parent::db()->fetchSingle('select `id`, `language` from `language` where `id` = ' . $id . ';');
            $this->setLanguage($language);
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
            parent::request()->set('language-id', $this->Language['id'], 'web');
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
            // Rewrites: /a/x/server.php => a/x/
            $url = substr($url, 1, strpos($url, '/' . $scriptName));
            if (strlen($url) > 0) {
                // Rewrites: a/x/ => a/x
                return substr($url, 0, strlen($url) - 1);
            }
            return $url;
        }

        public static function combinePath($path1, $path2, $delim = '/') {
            if ($path1 == $delim) {
                if($path2[0] == $delim) {
                    return $path2;
                } else {
                    return $delim.$path2;
                }
            }
            if ($path1 != '' && $path2 != '' && $path1[strlen($path1) - 1] != $delim && $path2[0] != $delim) {
                return $path1 . $delim . $path2;
            } elseif ($path1 == $delim && $path2 == $delim) {
                return $path1;
            } elseif ($path1[strlen($path1) - 1] == $delim && $path2[0] == $delim) {
                return substr(0, $path1[strlen($path1) - 1]) . $path2;
            } else {
                return $path1 . $path2;
            }
        }

    }

?>