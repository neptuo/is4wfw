<?php

    require_once("BaseTagLib.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/LocalizationBundle.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/Order.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/manager/EmbeddedResourceManager.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/manager/WebForwardManager.class.php");
    require_once("System.class.php");


    /**
     * 
     *  Class updating web pages.     
     *      
     *  @author     Marek SMM
     *  @timestamp  2012-01-21
     * 
     */
    class Page extends BaseTagLib {
        public static $PageRightDesc = array(
            'page_right', 'pid', 'gid', 'type'
        );

        private $MessageFromEdit = '';

        public function __construct() {
            $this->setLocalizationBundle('page');
        }

        public function showEditPage($paramPageId = false, $paramLangId = false) {
            global $dbObject;
            global $loginObject;
            $rb = $this->rb();
            $return = '';
            
            if ($paramPageId != 0 && $paramLangId != 0) {
                $_POST['page-edit'] = $rb->get('page.action.edit');
                $_POST['page-id']  = $paramPageId;
                $dataItem = parent::db()->fetchSingle('select `parent_id` from `page` where `id` = '.$paramPageId.';');
                $_POST['parent-id'] = $dataItem['parent_id'];
                $_POST['page-lang-id'] = $paramLangId;
            }

            $projects = $dbObject->fetchAll('SELECT `web_project`.`id` FROM `web_project` LEFT JOIN `web_project_right` ON `web_project`.`id` = `web_project_right`.`wp` LEFT JOIN `group` ON `web_project_right`.`gid` = `group`.`gid` WHERE `web_project_right`.`type` = ' . WEB_R_WRITE . ' AND `group`.`value` >= ' . $loginObject->getGroupValue() . ';');
            if (count($projects) != 0) {
                if (array_key_exists('selected-project', $_SESSION)) {
                    $projectId = $_SESSION['selected-project'];
                    $test = $dbObject->fetchAll('SELECT `web_project`.`id` FROM `web_project` LEFT JOIN `web_project_right` ON `web_project`.`id` = `web_project_right`.`wp` LEFT JOIN `group` ON `web_project_right`.`gid` = `group`.`gid` WHERE `web_project`.`id` = ' . $projectId . ' AND `web_project_right`.`type` = ' . WEB_R_WRITE . ' AND `group`.`value` >= ' . $loginObject->getGroupValue() . ';');
                    if (count($test) == 0) {
                        $projectId = $projects[0]['id'];
                    }
                } else {
                    $projectId = $projects[0]['id'];
                }
            } else {
                if (array_key_exists('selected-project', $_SESSION)) {
                    $projectId = $_SESSION['selected-project'];
                } else {
                    // dont care.
                }
            }

            if ($_POST['edit-save'] == $rb->get('page.action.save') || $_POST['edit-save'] == $rb->get('page.action.saveandclose')) {
                $pageId = $_POST['page-id'];
                $parentId = $_POST['parent-id'];
                $languageId = $_POST['language'];
                $name = $_POST['edit-name'];
                $href = UrlUtils::toValidUrl($_POST['edit-href'], true, true);
                $inTitle = ($_POST['edit-in-title'] == "on") ? 1 : 0;
                $visible = ($_POST['edit-visible'] == "on") ? 1 : 0;
                $menu = ($_POST['edit-menu'] == "on") ? 1 : 0;
                $isContentless = ($_POST['edit-contentless'] == "on") ? 1 : 0;
                $head = str_replace('&amp;web:page', '&web:page', $_POST['edit-head']);
                $content = str_replace('&amp;web:page', '&web:page', $_POST['edit-content']);
                $tlStart = str_replace('&amp;web:page', '&web:page', $_POST['edit-tl-start']);
                $tlEnd = str_replace('&amp;web:page', '&web:page', $_POST['edit-tl-end']);
                $head =    $dbObject->escape(str_replace('&#126', '~', $head));
                $content = $dbObject->escape(str_replace('&#126', '~', $content));
                $tlStart = $dbObject->escape(str_replace('&#126', '~', $tlStart));
                $tlEnd =   $dbObject->escape(str_replace('&#126', '~', $tlEnd));
                $type = $_POST['type'];
                $keywords = $dbObject->escape(str_replace('&#126', '~', $_POST['edit-keywords']));
                $icon = $dbObject->escape(str_replace('&#126', '~', $_POST['edit-icon']));
                $title = $dbObject->escape(str_replace('&#126', '~', $_POST['edit-title']));
                $clearUrlCache = $_POST['edit-clearurlcache'];
                $cacheTime = $_POST['edit-cachetime'];
                $errors = array();

                $forSaveNewPageId = 0;
                $pageRightW = $_POST['right-edit-groups-w'];
                $pageRightR = $_POST['right-edit-groups-r'];
                $pageRightD = $_POST['right-edit-groups-d'];
                $pageRightA = $_POST['right-edit-groups-a'];

                if (strlen($name) < 2) {
                    $errors[] = $rb->get('page.error.nametooshort');
                }

                if ($type != "page-edit") {
                    $tmpPages = $dbObject->fetchAll("SELECT `id` FROM `info` LEFT JOIN `page` ON `info`.`page_id` = `page`.`id` WHERE `info`.`href` = \"" . $href . "\" AND `page`.`parent_id` = " . $parentId . " AND `info`.`language_id` = " . $languageId . " AND `page`.`wp` = " . $projectId . ";");
                    if (count($tmpPages) != 0) {
                        $errors[] = $rb->get('page.error.urlused');
                    }
                }

                if (count($errors) == 0) {
                    if ($type == "page-edit") {
                        $dbObject->execute("UPDATE `info` SET `name` = \"" . $name . "\", `href` = \"" . $href . "\", `in_title` = \"" . $inTitle . "\", `in_menu` = " . $menu . ", `is_visible` = " . $visible . ", `keywords` = \"" . $keywords . "\", `icon` = \"" . $icon . "\", `title` = \"" . $title . "\", `timestamp` = " . time() . ", `cachetime` = " . $cacheTime . " WHERE `page_id` = " . $pageId . " AND `language_id` = " . $languageId . ";");
                        
                        $contentFilter = array("page_id" => $pageId, "language_id" => $languageId);
                        if ($isContentless) {
                            $deleteSql = parent::sql()->delete("content", $contentFilter);
                            parent::dataAccess()->execute($deleteSql);
                        } else {
                            $this->deleteParsedTemplate(TemplateCacheKeys::pageCleanUp($pageId, $languageId), true);

                            $hasContent = parent::dataAccess()->fetchSingle(parent::sql()->count("content", $contentFilter))["count"] != 0;
                            $contentValues = array("tag_lib_start" => $tlStart, "tag_lib_end" => $tlEnd, "head" => $head, "content" => $content);
                            if ($hasContent) {
                                $dbObject->execute("UPDATE `content` SET `tag_lib_start` = \"" . $tlStart . "\", `tag_lib_end` = \"" . $tlEnd . "\", `head` = \"" . $head . "\", `content` = \"" . $content . "\" WHERE `page_id` = " . $pageId . " AND `language_id` = " . $languageId . ";");
                            } else {
                                $contentValues = array_merge($contentValues, $contentFilter);
                                $insertSql = parent::sql()->insert("content", $contentValues);
                                parent::dataAccess()->execute($insertSql);
                            }
                        }

                        $allUserGroups = $dbObject->fetchAll('SELECT `gid`, `name` FROM `group` WHERE (`group`.`gid` IN (' . $loginObject->getGroupsIdsAsString() . ') OR `group`.`parent_gid` IN (' . $loginObject->getGroupsIdsAsString() . ')) ORDER BY `value`;');

                        if (count($pageRightR) != 0) {
                            $dbR = $dbObject->fetchAll("SELECT `gid` FROM `page_right` WHERE `page_right`.`pid` = " . $pageId . " AND `type` = " . WEB_R_READ . ";");
                            foreach ($dbR as $right) {
                                if (!in_array($right['gid'], $pageRightR) && $this->inAsocArray($right['gid'], $allUserGroups, 'gid')) {
                                    $dbObject->execute("DELETE FROM `page_right` WHERE `pid` = " . $pageId . " AND `type` = " . WEB_R_READ . " and `gid` = " . $right['gid'] . ";");
                                }
                            }

                            foreach ($pageRightR as $right) {
                                $row = $dbObject->fetchAll("SELECT `gid` FROM `page_right` WHERE `pid` = " . $pageId . " AND `type` = " . WEB_R_READ . " AND `gid` = " . $right . ";");
                                if (count($row) == 0) {
                                    $dbObject->execute("INSERT INTO `page_right`(`pid`, `gid`, `type`) VALUES (" . $pageId . ", " . $right . ", " . WEB_R_READ . ")");
                                }
                            }
                        }

                        if (count($pageRightW) != 0) {
                            $dbR = $dbObject->fetchAll("SELECT `gid` FROM `page_right` WHERE `page_right`.`pid` = " . $pageId . " AND `type` = " . WEB_R_WRITE . ";");
                            foreach ($dbR as $right) {
                                if (!in_array($right['gid'], $pageRightW) && $this->inAsocArray($right['gid'], $allUserGroups, 'gid')) {
                                    $dbObject->execute("DELETE FROM `page_right` WHERE `pid` = " . $pageId . " AND `type` = " . WEB_R_WRITE . " and `gid` = " . $right['gid'] . ";");
                                }
                            }

                            foreach ($pageRightW as $right) {
                                $row = $dbObject->fetchAll("SELECT `gid` FROM `page_right` WHERE `pid` = " . $pageId . " AND `type` = " . WEB_R_WRITE . " AND `gid` = " . $right . ";");
                                if (count($row) == 0) {
                                    $dbObject->execute("INSERT INTO `page_right`(`pid`, `gid`, `type`) VALUES (" . $pageId . ", " . $right . ", " . WEB_R_WRITE . ")");
                                }
                            }
                        }

                        if (count($pageRightD) != 0) {
                            $dbR = $dbObject->fetchAll("SELECT `gid` FROM `page_right` WHERE `page_right`.`pid` = " . $pageId . " AND `type` = " . WEB_R_DELETE . ";");
                            foreach ($dbR as $right) {
                                if (!in_array($right['gid'], $pageRightD) && $this->inAsocArray($right['gid'], $allUserGroups, 'gid')) {
                                    $dbObject->execute("DELETE FROM `page_right` WHERE `pid` = " . $pageId . " AND `type` = " . WEB_R_DELETE . " and `gid` = " . $right['gid'] . ";;");
                                }
                            }

                            foreach ($pageRightD as $right) {
                                $row = $dbObject->fetchAll("SELECT `gid` FROM `page_right` WHERE `pid` = " . $pageId . " AND `type` = " . WEB_R_DELETE . " AND `gid` = " . $right . ";");
                                if (count($row) == 0) {
                                    $dbObject->execute("INSERT INTO `page_right`(`pid`, `gid`, `type`) VALUES (" . $pageId . ", " . $right . ", " . WEB_R_DELETE . ")");
                                }
                            }
                        }

                        if (count($pageRightA) != 0) {
                            $dbR = $dbObject->fetchAll("SELECT `gid` FROM `page_right` WHERE `page_right`.`pid` = " . $pageId . " AND `type` = " . WEB_R_ADDCHILD . ";");
                            foreach ($dbR as $right) {
                                if (!in_array($right['gid'], $pageRightA) && $this->inAsocArray($right['gid'], $allUserGroups, 'gid')) {
                                    $dbObject->execute("DELETE FROM `page_right` WHERE `pid` = " . $pageId . " AND `type` = " . WEB_R_ADDCHILD . " and `gid` = " . $right['gid'] . ";;");
                                }
                            }

                            foreach ($pageRightA as $right) {
                                $row = $dbObject->fetchAll("SELECT `gid` FROM `page_right` WHERE `pid` = " . $pageId . " AND `type` = " . WEB_R_ADDCHILD . " AND `gid` = " . $right . ";");
                                if (count($row) == 0) {
                                    $dbObject->execute("INSERT INTO `page_right`(`pid`, `gid`, `type`) VALUES (" . $pageId . ", " . $right . ", " . WEB_R_ADDCHILD . ")");
                                }
                            }
                        }

                        //$return .= parent::getFrame('Success Message', '<h4 class="success">Page successfully updated!</h4>', '', true);
                    } else if ($type == "add-new-page") {
                        $sql_return = $dbObject->fetchAll("SELECT MAX(`id`) AS `id` FROM `page`");

                        $pageId = $sql_return[0]['id'] + 1;
                        $forSaveNewPageId = $pageId;
                        $_POST['page-id'] = $pageId;
                        $languageId = $_POST['language'];

                        $dbObject->execute("INSERT INTO `page`(`id`, `parent_id`, `wp`) VALUES(" . $pageId . ", " . $parentId . ", " . $projectId . ");");
                        $dbObject->execute("INSERT INTO `content`(`page_id`, `tag_lib_start` , `tag_lib_end`, `head`, `content`, `language_id`) VALUES(" . $pageId . ", \"" . $tlStart . "\", \"" . $tlEnd . "\", \"" . $head . "\", \"" . $content . "\", " . $languageId . ");");
                        $dbObject->execute("INSERT INTO `info`(`page_id`, `language_id`, `name`, `href`, `in_title`, `in_menu`, `page_pos`, `is_visible`, `keywords`, `icon`, `title`, `timestamp`, `cachetime`) VALUES(" . $pageId . ", " . $languageId . ", \"" . $name . "\", \"" . $href . "\", " . $inTitle . ", " . $menu . ", " . $pageId . ", " . $visible . ", \"" . $keywords . "\", \"" . $icon . "\", \"" . $title . "\", " . time() . ", " . $cacheTime . ");");

                        if (count($pageRightR) != 0) {
                            foreach ($pageRightR as $right) {
                                $dbObject->execute("INSERT INTO `page_right`(`pid`, `gid`, `type`) VALUES (" . $pageId . ", " . $right . ", " . WEB_R_READ . ")");
                            }
                            $rights = $dbObject->fetchAll('SELECT `gid` FROM `web_project_right` WHERE `wp` = ' . $projectId . ' AND `type` = ' . WEB_R_READ . ' and `gid` not in (' . 'SELECT `gid` FROM `group` WHERE (`group`.`gid` IN (' . $loginObject->getGroupsIdsAsString() . ') OR `group`.`parent_gid` IN (' . $loginObject->getGroupsIdsAsString() . ')) ORDER BY `value`' . ');');
                            foreach ($rights as $right) {
                                $dbObject->execute('INSERT INTO `page_right`(`pid`, `gid`, `type`) VALUES (' . $pageId . ', ' . $right['gid'] . ', ' . WEB_R_READ . ');');
                            }
                        } else {
                            //$rights = $dbObject->fetchAll('SELECT `gid` FROM `page_right` WHERE `pid` = '.$parentId.' AND `type` = '.WEB_R_READ.';');
                            $rights = $dbObject->fetchAll("SELECT `gid` FROM `web_project_right` WHERE `wp` = " . $projectId . " AND `type` = " . WEB_R_READ . ";");
                            foreach ($rights as $right) {
                                $dbObject->execute('INSERT INTO `page_right`(`pid`, `gid`, `type`) VALUES (' . $pageId . ', ' . $right['gid'] . ', ' . WEB_R_READ . ');');
                            }
                        }

                        if (count($pageRightW) != 0) {
                            foreach ($pageRightW as $right) {
                                $dbObject->execute("INSERT INTO `page_right`(`pid`, `gid`, `type`) VALUES (" . $pageId . ", " . $right . ", " . WEB_R_WRITE . ")");
                            }
                            $rights = $dbObject->fetchAll('SELECT `gid` FROM `web_project_right` WHERE `wp` = ' . $projectId . ' AND `type` = ' . WEB_R_WRITE . ' and `gid` not in (' . 'SELECT `gid` FROM `group` WHERE (`group`.`gid` IN (' . $loginObject->getGroupsIdsAsString() . ') OR `group`.`parent_gid` IN (' . $loginObject->getGroupsIdsAsString() . ')) ORDER BY `value`' . ');');
                            foreach ($rights as $right) {
                                $dbObject->execute('INSERT INTO `page_right`(`pid`, `gid`, `type`) VALUES (' . $pageId . ', ' . $right['gid'] . ', ' . WEB_R_WRITE . ');');
                            }
                        } else {
                            //$rights = $dbObject->fetchAll('SELECT `gid` FROM `page_right` WHERE `pid` = '.$parentId.' AND `type` = '.WEB_R_WRITE.';');
                            $rights = $dbObject->fetchAll("SELECT `gid` FROM `web_project_right` WHERE `wp` = " . $projectId . " AND `type` = " . WEB_R_WRITE . ";");
                            foreach ($rights as $right) {
                                $dbObject->execute('INSERT INTO `page_right`(`pid`, `gid`, `type`) VALUES (' . $pageId . ', ' . $right['gid'] . ', ' . WEB_R_WRITE . ');');
                            }
                        }

                        if (count($pageRightD) != 0) {
                            foreach ($pageRightD as $right) {
                                $dbObject->execute("INSERT INTO `page_right`(`pid`, `gid`, `type`) VALUES (" . $pageId . ", " . $right . ", " . WEB_R_DELETE . ")");
                            }
                            $rights = $dbObject->fetchAll('SELECT `gid` FROM `web_project_right` WHERE `wp` = ' . $projectId . ' AND `type` = ' . WEB_R_DELETE . ' and `gid` not in (' . 'SELECT `gid` FROM `group` WHERE (`group`.`gid` IN (' . $loginObject->getGroupsIdsAsString() . ') OR `group`.`parent_gid` IN (' . $loginObject->getGroupsIdsAsString() . ')) ORDER BY `value`' . ');');
                            foreach ($rights as $right) {
                                $dbObject->execute('INSERT INTO `page_right`(`pid`, `gid`, `type`) VALUES (' . $pageId . ', ' . $right['gid'] . ', ' . WEB_R_DELETE . ');');
                            }
                        } else {
                            //$rights = $dbObject->fetchAll('SELECT `gid` FROM `page_right` WHERE `pid` = '.$parentId.' AND `type` = '.WEB_R_DELETE.';');
                            $rights = $dbObject->fetchAll("SELECT `gid` FROM `web_project_right` WHERE `wp` = " . $projectId . " AND `type` = " . WEB_R_DELETE . ";");
                            foreach ($rights as $right) {
                                $dbObject->execute('INSERT INTO `page_right`(`pid`, `gid`, `type`) VALUES (' . $pageId . ', ' . $right['gid'] . ', ' . WEB_R_DELETE . ');');
                            }
                        }

                        if (count($pageRightA) != 0) {
                            foreach ($pageRightA as $right) {
                                $dbObject->execute("INSERT INTO `page_right`(`pid`, `gid`, `type`) VALUES (" . $pageId . ", " . $right . ", " . WEB_R_ADDCHILD . ")");
                            }
                            $rights = $dbObject->fetchAll('SELECT `gid` FROM `web_project_right` WHERE `wp` = ' . $projectId . ' AND `type` = ' . WEB_R_WRITE . ' and `gid` not in (' . 'SELECT `gid` FROM `group` WHERE (`group`.`gid` IN (' . $loginObject->getGroupsIdsAsString() . ') OR `group`.`parent_gid` IN (' . $loginObject->getGroupsIdsAsString() . ')) ORDER BY `value`' . ');');
                            foreach ($rights as $right) {
                                $dbObject->execute('INSERT INTO `page_right`(`pid`, `gid`, `type`) VALUES (' . $pageId . ', ' . $right['gid'] . ', ' . WEB_R_ADDCHILD . ');');
                            }
                        } else {
                            //$rights = $dbObject->fetchAll('SELECT `gid` FROM `page_right` WHERE `pid` = '.$parentId.' AND `type` = '.WEB_R_ADDCHILD.';');
                            $rights = $dbObject->fetchAll("SELECT `gid` FROM `web_project_right` WHERE `wp` = " . $projectId . " AND `type` = " . WEB_R_WRITE . ";");
                            foreach ($rights as $right) {
                                $dbObject->execute('INSERT INTO `page_right`(`pid`, `gid`, `type`) VALUES (' . $pageId . ', ' . $right['gid'] . ', ' . WEB_R_ADDCHILD . ');');
                            }
                        }

                        $this->MessageFromEdit = '<h4 class="success">' . $rb->get('page.success.added') . '</h4>';
                    } else if ($type == "page-add-lang-ver") {
                        if (!$isContentless) {
                            $dbObject->execute("INSERT INTO `content`(`page_id`, `tag_lib_start` , `tag_lib_end`, `head`, `content`, `language_id`) VALUES(" . $pageId . ", \"" . $tlStart . "\", \"" . $tlEnd . "\", \"" . $head . "\", \"" . $content . "\", " . $languageId . ");");
                        }

                        $dbObject->execute("INSERT INTO `info`(`page_id`, `language_id`, `name`, `href`, `in_title`, `in_menu`, `page_pos`, `is_visible`, `keywords`, `icon`, `title`, `timestamp`, `cachetime`) VALUES(" . $pageId . ", " . $languageId . ", \"" . $name . "\", \"" . $href . "\", " . $inTitle . ", " . $menu . ", " . $pageId . ", " . $visible . ", \"" . $keywords . "\", \"" . $icon . "\", \"" . $title . "\", " . time() . ", " . $cacheTime . ");");

                        $this->MessageFromEdit = '<h4 class="success">' . $rb->get('page.success.langadded') . '</h4>';
                    } else if ($type == "page-add-sub") {
                        $sql_return = $dbObject->fetchAll("SELECT MAX(`id`) AS `id` FROM `page`");

                        $pageId = $sql_return[0]['id'] + 1;
                        $_POST['page-id'] = $pageId;

                        $dbObject->execute("INSERT INTO `page`(`id`, `parent_id`, `wp`) VALUES(" . $pageId . ", " . $parentId . ", " . $projectId . ");");
                        $dbObject->execute("INSERT INTO `content`(`page_id`, `tag_lib_start` , `tag_lib_end`, `head`, `content`, `language_id`) VALUES(" . $pageId . ", \"" . $tlStart . "\", \"" . $tlEnd . "\", \"" . $head . "\", \"" . $content . "\", " . $languageId . ");");
                        $dbObject->execute("INSERT INTO `info`(`page_id`, `language_id`, `name`, `href`, `in_title`, `in_menu`, `page_pos`, `is_visible`, `keywords`, `icon`, `title`, `timestamp`, `cachetime`) VALUES(" . $pageId . ", " . $languageId . ", \"" . $name . "\", \"" . $href . "\", " . $inTitle . ", " . $menu . ", " . $pageId . ", " . $visible . ", \"" . $keywords . "\", \"" . $icon . "\", \"" . $title . "\", " . time() . ", " . $cacheTime . ");");

                        if (count($pageRightR) != 0) {
                            foreach ($pageRightR as $right) {
                                $dbObject->execute("INSERT INTO `page_right`(`pid`, `gid`, `type`) VALUES (" . $pageId . ", " . $right . ", " . WEB_R_READ . ")");
                            }
                            $rights = $dbObject->fetchAll('SELECT `gid` FROM `page_right` WHERE `pid` = ' . $parentId . ' AND `type` = ' . WEB_R_READ . ' and `gid` not in (' . 'SELECT `gid` FROM `group` WHERE (`group`.`gid` IN (' . $loginObject->getGroupsIdsAsString() . ') OR `group`.`parent_gid` IN (' . $loginObject->getGroupsIdsAsString() . ')) ORDER BY `value`' . ');;');
                            foreach ($rights as $right) {
                                $dbObject->execute('INSERT INTO `page_right`(`pid`, `gid`, `type`) VALUES (' . $pageId . ', ' . $right['gid'] . ', ' . WEB_R_READ . ');');
                            }
                        } else {
                            $rights = $dbObject->fetchAll('SELECT `gid` FROM `page_right` WHERE `pid` = ' . $parentId . ' AND `type` = ' . WEB_R_READ . ';');
                            foreach ($rights as $right) {
                                $dbObject->execute('INSERT INTO `page_right`(`pid`, `gid`, `type`) VALUES (' . $pageId . ', ' . $right['gid'] . ', ' . WEB_R_READ . ');');
                            }
                        }

                        if (count($pageRightW) != 0) {
                            foreach ($pageRightW as $right) {
                                $dbObject->execute("INSERT INTO `page_right`(`pid`, `gid`, `type`) VALUES (" . $pageId . ", " . $right . ", " . WEB_R_WRITE . ")");
                            }
                            $rights = $dbObject->fetchAll('SELECT `gid` FROM `page_right` WHERE `pid` = ' . $parentId . ' AND `type` = ' . WEB_R_ADDCHILD . ' and `gid` not in (' . 'SELECT `gid` FROM `group` WHERE (`group`.`gid` IN (' . $loginObject->getGroupsIdsAsString() . ') OR `group`.`parent_gid` IN (' . $loginObject->getGroupsIdsAsString() . ')) ORDER BY `value`' . ');;');
                            foreach ($rights as $right) {
                                $dbObject->execute('INSERT INTO `page_right`(`pid`, `gid`, `type`) VALUES (' . $pageId . ', ' . $right['gid'] . ', ' . WEB_R_WRITE . ');');
                            }
                        } else {
                            $rights = $dbObject->fetchAll('SELECT `gid` FROM `page_right` WHERE `pid` = ' . $parentId . ' AND `type` = ' . WEB_R_ADDCHILD . ';');
                            foreach ($rights as $right) {
                                $dbObject->execute('INSERT INTO `page_right`(`pid`, `gid`, `type`) VALUES (' . $pageId . ', ' . $right['gid'] . ', ' . WEB_R_WRITE . ');');
                            }
                        }

                        if (count($pageRightD) != 0) {
                            foreach ($pageRightD as $right) {
                                $dbObject->execute("INSERT INTO `page_right`(`pid`, `gid`, `type`) VALUES (" . $pageId . ", " . $right . ", " . WEB_R_DELETE . ")");
                            }
                            $rights = $dbObject->fetchAll('SELECT `gid` FROM `page_right` WHERE `pid` = ' . $parentId . ' AND `type` = ' . WEB_R_ADDCHILD . ' and `gid` not in (' . 'SELECT `gid` FROM `group` WHERE (`group`.`gid` IN (' . $loginObject->getGroupsIdsAsString() . ') OR `group`.`parent_gid` IN (' . $loginObject->getGroupsIdsAsString() . ')) ORDER BY `value`' . ');;');
                            foreach ($rights as $right) {
                                $dbObject->execute('INSERT INTO `page_right`(`pid`, `gid`, `type`) VALUES (' . $pageId . ', ' . $right['gid'] . ', ' . WEB_R_DELETE . ');');
                            }
                        } else {
                            $rights = $dbObject->fetchAll('SELECT `gid` FROM `page_right` WHERE `pid` = ' . $parentId . ' AND `type` = ' . WEB_R_ADDCHILD . ';');
                            foreach ($rights as $right) {
                                $dbObject->execute('INSERT INTO `page_right`(`pid`, `gid`, `type`) VALUES (' . $pageId . ', ' . $right['gid'] . ', ' . WEB_R_DELETE . ');');
                            }
                        }

                        if (count($pageRightA) != 0) {
                            foreach ($pageRightA as $right) {
                                $dbObject->execute("INSERT INTO `page_right`(`pid`, `gid`, `type`) VALUES (" . $pageId . ", " . $right . ", " . WEB_R_ADDCHILD . ")");
                            }
                            $rights = $dbObject->fetchAll('SELECT `gid` FROM `page_right` WHERE `pid` = ' . $parentId . ' AND `type` = ' . WEB_R_ADDCHILD . ' and `gid` not in (' . 'SELECT `gid` FROM `group` WHERE (`group`.`gid` IN (' . $loginObject->getGroupsIdsAsString() . ') OR `group`.`parent_gid` IN (' . $loginObject->getGroupsIdsAsString() . ')) ORDER BY `value`' . ');;');
                            foreach ($rights as $right) {
                                $dbObject->execute('INSERT INTO `page_right`(`pid`, `gid`, `type`) VALUES (' . $pageId . ', ' . $right['gid'] . ', ' . WEB_R_ADDCHILD . ');');
                            }
                        } else {
                            $rights = $dbObject->fetchAll('SELECT `gid` FROM `page_right` WHERE `pid` = ' . $parentId . ' AND `type` = ' . WEB_R_ADDCHILD . ';');
                            foreach ($rights as $right) {
                                $dbObject->execute('INSERT INTO `page_right`(`pid`, `gid`, `type`) VALUES (' . $pageId . ', ' . $right['gid'] . ', ' . WEB_R_ADDCHILD . ');');
                            }
                        }

                        $this->MessageFromEdit = '<h4 class="success">' . $rb->get('page.success.added') . '</h4>';
                    }

                    if ($clearUrlCache) {
                        $dbObject->execute("DELETE FROM `urlcache` WHERE `pages_id` LIKE \"%-" . $pageId . "-%\" AND `language_id` = " . $languageId . ";");
                        $dbObject->execute("DELETE FROM `urlcache` WHERE `pages_id` LIKE \"" . $pageId . "-%\" AND `language_id` = " . $languageId . ";");
                        $dbObject->execute("DELETE FROM `urlcache` WHERE `pages_id` LIKE \"%-" . $pageId . "\" AND `language_id` = " . $languageId . ";");
                        $dbObject->execute("DELETE FROM `urlcache` WHERE `pages_id` = \"" . $pageId . "\" AND `language_id` = " . $languageId . ";");
                    }

                    if ($_POST['edit-save'] == $rb->get('page.action.save')) {
                        if ($_POST['type'] == 'add-new-page') {
                            $_POST['page-id'] = $forSaveNewPageId;
                        }
                        $_POST['page-edit'] = $rb->get('page.action.edit');
                        $_POST['page-lang-id'] = $_POST['language'];
                    }
                } else {
                    //$errorList = '<ul class="error-list">';
                    foreach ($errors as $error) {
                        $errorList .= '<h4 class="error">' . $error . '</h4>';
                    }
                    //$errorList .= '</ul>';
                    $return .= parent::getFrame($rb->get('page.error.listlabel'), $errorList, "", true);

                    $errorOccurs = "true";

                    if ($_POST['type'] == 'add-new-page') {
                        $_POST['add-new-page'] = $rb->get('page.action.addpage');
                    } else if ($_POST['type'] == 'page-add-sub') {
                        $_POST['page-add-sub'] = $rb->get('page.action.addsubpage');
                    } else if ($_POST['type'] == 'page-add-lang-ver') {
                        $_POST['page-add-lang-ver'] = $rb->get('page.action.addlang');
                        ;
                    } else if ($_POST['type'] == 'page-edit') {
                        $_POST['page-edit'] = "Edit";
                    }
                }
            }

            if ($_POST['page-edit'] == $rb->get('page.action.edit') || $_POST['add-new-page'] == $rb->get('page.action.addpage') || $_POST['page-add-sub'] == $rb->get('page.action.addsubpage') || $_POST['page-add-lang-ver'] == $rb->get('page.action.addlang') || $errorOccurs == "true") {
                $usedLangs = array();
                $pageId = $_POST['page-id'] | 0;
                $parentId = $_POST['parent-id'];
                $langId = $_POST['page-lang-id'];
                $langsCount = true;

                $rights = array();
                if ($_POST['add-new-page'] == $rb->get('page.action.addpage') || $_POST['page-add-sub'] == $rb->get('page.action.addsubpage')) {
                    $rights = $dbObject->fetchAll('SELECT `group`.`name` FROM `group` LEFT JOIN `page_right` ON `group`.`gid` = `page_right`.`gid` WHERE `page_right`.`pid` = ' . $pageId . ' AND `page_right`.`type` = ' . WEB_R_ADDCHILD . ' AND (`group`.`gid` IN (' . $loginObject->getGroupsIdsAsString() . ') OR `group`.`parent_gid` IN (' . $loginObject->getGroupsIdsAsString() . '));');
                } else {
                    $rights = $dbObject->fetchAll('SELECT `group`.`name` FROM `group` LEFT JOIN `page_right` ON `group`.`gid` = `page_right`.`gid` WHERE `page_right`.`pid` = ' . $pageId . ' AND `page_right`.`type` = ' . WEB_R_WRITE . ' AND (`group`.`gid` IN (' . $loginObject->getGroupsIdsAsString() . ') OR `group`.`parent_gid` IN (' . $loginObject->getGroupsIdsAsString() . '));');
                }

                $ok = true;
                if (count($rights) == 0) {
                    $ok = false;
                }

                if ($ok) {
                    $right_pid = $pageId;
                    if ($_POST['page-edit'] == $rb->get('page.action.edit')) {
                        $type = "page-edit";
                    } else if ($_POST['add-new-page'] == $rb->get('page.action.addpage')) {
                        $type = "add-new-page";
                        $right_pid = $parentId;
                        $frameTitle = $rb->get('page.title.addpage');
                    } else if ($_POST['page-add-sub'] == $rb->get('page.action.addsubpage')) {
                        $type = "page-add-sub";
                        $frameTitle = $rb->get('page.title.addsubpage');
                    } else if ($_POST['page-add-lang-ver'] == $rb->get('page.action.addlang')) {
                        $type = "page-add-lang-ver";
                        $parentId = $pageId;
                        $usedLangs = $dbObject->fetchAll("SELECT `language_id` FROM `info` WHERE `page_id` = " . $pageId . ";");
                        $frameTitle = $rb->get('page.title.addlang');
                    } else {
                        $type = "undefined";
                    }

                    if ($_POST['page-edit'] == $rb->get('page.action.edit')) {
                        // $sql_return = $dbObject->fetchAll("SELECT `content`.`tag_lib_start`, `content`.`tag_lib_end`, `content`.`head`, `content`.`content`, `info`.`name`, `info`.`href`, `info`.`in_title`, `info`.`in_menu`, `info`.`is_visible`, `info`.`keywords`, `info`.`title`, `info`.`cachetime` FROM `content` LEFT JOIN `info` ON `content`.`page_id` = `info`.`page_id` AND `info`.`language_id` = `content`.`language_id` WHERE `info`.`page_id` = " . $pageId . " AND `info`.`language_id` = " . $langId . ";");
                        $sql_return = array();
                        $sql_info = $dbObject->fetchAll("SELECT `info`.`name`, `info`.`href`, `info`.`in_title`, `info`.`in_menu`, `info`.`is_visible`, `info`.`keywords`, `info`.`icon`, `info`.`title`, `info`.`cachetime` FROM `info` WHERE `info`.`page_id` = " . $pageId . " AND `info`.`language_id` = " . $langId . ";");
                        $sql_content = $dbObject->fetchAll("SELECT `content`.`tag_lib_start`, `content`.`tag_lib_end`, `content`.`head`, `content`.`content` FROM `content` WHERE `content`.`page_id` = " . $pageId . " AND `content`.`language_id` = " . $langId . ";");
                        if ($sql_content != array()) {
                            $sql_info[0]["tag_lib_start"] = $sql_content[0]["tag_lib_start"];
                            $sql_info[0]["tag_lib_end"] = $sql_content[0]["tag_lib_end"];
                            $sql_info[0]["head"] = $sql_content[0]["head"];
                            $sql_info[0]["content"] = $sql_content[0]["content"];
                            $sql_info[0]["contentless"] = 0;
                        } else {
                            $sql_info[0]["contentless"] = 1;
                        }

                        $sql_return = $sql_info;

                        $frameTitle = $rb->get('page.action.editation') . ' :: ' . $sql_return[0]['name'] . ' ( ' . $pageId . ' )';
                    } else {
                        $sql_return = array();
                        $sql_return[0]['in_title'] = 1;
                        $sql_return[0]['is_visible'] = 1;
                        $sql_return[0]['cachetime'] = -1;
                        $sql_return[0]['title'] = "<web:getProperty name=\"web:currentPageName\" />";
                    }

                    if ($errorOccurs == "true") {
                        $head = str_replace('\"', '"', $head);
                        $content = str_replace('\"', '"', $content);
                        $tlStart = str_replace('\"', '"', $tlStart);
                        $tlEnd = str_replace('\"', '"', $tlEnd);
                        $title = str_replace('\"', '"', $title);
                        $keywords = str_replace('\"', '"', $keywords);
                        $icon = str_replace('\"', '"', $icon);

                        $sql_return[0]['name'] = $name;
                        $sql_return[0]['href'] = $href;
                        $sql_return[0]['in_title'] = $inTitle;
                        $sql_return[0]['in_menu'] = $menu;
                        $sql_return[0]['is_visible'] = $visible;
                        $sql_return[0]['head'] = $head;
                        $sql_return[0]['content'] = $content;
                        $sql_return[0]['tag_lib_start'] = $tlStart;
                        $sql_return[0]['tag_lib_end'] = $tlEnd;
                        $sql_return[0]['cachetime'] = $cacheTime;
                        $sql_return[0]['keywords'] = $keywords;
                        $sql_return[0]['icon'] = $icon;
                        $sql_return[0]['title'] = $title;
                        $langId = $languageId;
                    }

                    $groupsR = array();
                    $groupsW = array();
                    $groupsD = array();
                    $groupsA = array();
                    if ($type == 'add-new-page') {
                        $groupsR = $dbObject->fetchAll("SELECT `gid` FROM `web_project_right` WHERE `wp` = " . $projectId . " AND `type` = " . WEB_R_READ . ";");
                        $groupsW = $dbObject->fetchAll("SELECT `gid` FROM `web_project_right` WHERE `wp` = " . $projectId . " AND `type` = " . WEB_R_WRITE . ";");
                        $groupsD = $dbObject->fetchAll("SELECT `gid` FROM `web_project_right` WHERE `wp` = " . $projectId . " AND `type` = " . WEB_R_DELETE . ";");
                        $groupsA = $dbObject->fetchAll("SELECT `gid` FROM `web_project_right` WHERE `wp` = " . $projectId . " AND `type` = " . WEB_R_WRITE . ";");
                    } elseif ($type == 'page-add-sub') {
                        $groupsR = $dbObject->fetchAll("SELECT `gid` FROM `page_right` WHERE `pid` = " . $right_pid . " AND `type` = " . WEB_R_READ . ";");
                        $groupsA = $dbObject->fetchAll("SELECT `gid` FROM `page_right` WHERE `pid` = " . $right_pid . " AND `type` = " . WEB_R_ADDCHILD . ";");
                        $groupsW = $groupsA;
                        $groupsD = $groupsA;
                    } else {
                        $groupsR = $dbObject->fetchAll("SELECT `gid` FROM `page_right` WHERE `pid` = " . $right_pid . " AND `type` = " . WEB_R_READ . ";");
                        $groupsW = $dbObject->fetchAll("SELECT `gid` FROM `page_right` WHERE `pid` = " . $right_pid . " AND `type` = " . WEB_R_WRITE . ";");
                        $groupsD = $dbObject->fetchAll("SELECT `gid` FROM `page_right` WHERE `pid` = " . $right_pid . " AND `type` = " . WEB_R_DELETE . ";");
                        $groupsA = $dbObject->fetchAll("SELECT `gid` FROM `page_right` WHERE `pid` = " . $right_pid . " AND `type` = " . WEB_R_ADDCHILD . ";");
                    }

                    $show = array('read' => true, 'write' => true, 'delete' => false);
                    $allGroups = $dbObject->fetchAll('SELECT `gid`, `name` FROM `group` WHERE (`group`.`gid` IN (' . $loginObject->getGroupsIdsAsString() . ') OR `group`.`parent_gid` IN (' . $loginObject->getGroupsIdsAsString() . ')) ORDER BY `value`;');
                    $groupSelectR = '<select id="right-edit-groups-r" name="right-edit-groups-r[]" multiple="multiple" size="5">';
                    $groupSelectW = '<select id="right-edit-groups-w" name="right-edit-groups-w[]" multiple="multiple" size="5">';
                    $groupSelectD = '<select id="right-edit-groups-d" name="right-edit-groups-d[]" multiple="multiple" size="5">';
                    $groupSelectA = '<select id="right-edit-groups-a" name="right-edit-groups-a[]" multiple="multiple" size="5">';

                    foreach ($allGroups as $group) {
                        $selectedR = false;
                        $selectedW = false;
                        $selectedD = false;
                        $selectedA = false;
                        foreach ($groupsR as $gp) {
                            if ($gp['gid'] == $group['gid']) {
                                $selectedR = true;
                                $show['read'] = true;
                            }
                        }

                        foreach ($groupsW as $gp) {
                            if ($gp['gid'] == $group['gid']) {
                                $selectedW = true;
                                $show['write'] = true;
                            }
                        }

                        foreach ($groupsD as $gp) {
                            if ($gp['gid'] == $group['gid']) {
                                $selectedD = true;
                                $show['delete'] = true;
                            }
                        }

                        foreach ($groupsA as $gp) {
                            if ($gp['gid'] == $group['gid']) {
                                $selectedA = true;
                                $show['addchild'] = true;
                            }
                        }

                        $groupSelectR .= '<option' . (($selectedR) ? ' selected="selected"' : '') . ' value="' . $group['gid'] . '">' . $group['name'] . '</option>';
                        $groupSelectW .= '<option' . (($selectedW) ? ' selected="selected"' : '') . ' value="' . $group['gid'] . '">' . $group['name'] . '</option>';
                        $groupSelectD .= '<option' . (($selectedD) ? ' selected="selected"' : '') . ' value="' . $group['gid'] . '">' . $group['name'] . '</option>';
                        $groupSelectA .= '<option' . (($selectedA) ? ' selected="selected"' : '') . ' value="' . $group['gid'] . '">' . $group['name'] . '</option>';
                    }

                    $groupSelectR .= '</select>';
                    $groupSelectW .= '</select>';
                    $groupSelectD .= '</select>';
                    $groupSelectA .= '</select>';

                    $return .= '';
                    if ($this->MessageFromEdit != '') {
                        $returnTmp .= $this->MessageFromEdit;
                        $this->MessageFromEdit = '';
                    }

                    if (($type != "undefined" && (count($sql_return) == 1) || $type != "Edit")) {
                        $sql_return[0]['tag_lib_start'] = StringUtils::escapeHtmlEntities($sql_return[0]['tag_lib_start']);
                        $sql_return[0]['tag_lib_end'] = StringUtils::escapeHtmlEntities($sql_return[0]['tag_lib_end']);
                        $sql_return[0]['head'] = StringUtils::escapeHtmlEntities($sql_return[0]['head']);
                        $sql_return[0]['content'] = StringUtils::escapeHtmlEntities($sql_return[0]['content']);
                        $sql_return[0]['title'] = StringUtils::escapeHtmlEntities($sql_return[0]['title']);
                        $sql_return[0]['keywords'] = StringUtils::escapeHtmlEntities($sql_return[0]['keywords']);

                        $returnTmp .= ''
                        . '<form name="page-edit-detail" method="post" action="' . $_SERVER['REQUEST_URI'] . '">'
                            . '<div class="edit edit-page-info">'
                                . '<div class="edit edit-prop">'
                                    . '<div class="gray-box">'
                                        . '<label for="edit-name" class="w90">' . $rb->get('page.field.namelabel') . ':</label> '
                                        . '<input type="text" name="edit-name" id="edit-name" value="' . $sql_return[0]['name'] . '" class="w450" />'
                                    . '</div>'
                                    . '<div class="gray-box">'
                                        . '<label for="edit-href" class="w90">' . $rb->get('page.field.urllabel') . ':</label> '
                                        . '<input type="text" name="edit-href" id="edit-href" value="' . $sql_return[0]['href'] . '" class="w450" />'
                                    . '</div>'
                                    . '<div class="gray-box">'
                                        . '<label for="edit-keywords" class="w90">' . $rb->get('page.field.keywordslabel') . ':</label> '
                                        . '<input id="edit-keywords" type="text" name="edit-keywords" value="' . $sql_return[0]['keywords'] . '" class="w450" />'
                                    . '</div>'
                                    . '<div class="gray-box">'
                                        . '<label for="edit-icon" class="w90">' . $rb->get('page.field.iconlabel') . ':</label> '
                                        . '<input id="edit-icon" type="text" name="edit-icon" value="' . $sql_return[0]['icon'] . '" class="w450" />'
                                    . '</div>'
                                    . '<div class="gray-box">'
                                        . '<label for="edit-title" class="w90">' . $rb->get('page.field.titlelabel') . ':</label> '
                                        . '<input id="edit-title" type="text" name="edit-title" value="' . $sql_return[0]['title'] . '" class="w450" />'
                                    . '</div>'
                                    . '<div class="edit edit-in-title h50">'
                                        . '<label for="edit-in-title">' . $rb->get('page.field.intitlelabel') . ':</label> '
                                        . '<input type="checkbox" name="edit-in-title" id="edit-in-title"' . (($sql_return[0]['in_title'] == 1) ? 'checked="checked"' : '') . ' />'
                                    . '</div>'
                                    . '<div class="edit edit-menu h50">'
                                        . '<label for="edit-menu">' . $rb->get('page.field.inmenulabel') . ':</label> '
                                        . '<input type="checkbox" name="edit-menu" id="edit-menu"' . (($sql_return[0]['in_menu'] == 1) ? 'checked="checked"' : '') . ' />'
                                    . '</div>'
                                    . '<div class="edit edit-visible h50">'
                                        . '<label for="edit-visible">' . $rb->get('page.field.isvisiblelabel') . ':</label> '
                                        . '<input type="checkbox" name="edit-visible" id="edit-visible"' . (($sql_return[0]['is_visible'] == 1) ? 'checked="checked"' : '') . ' />'
                                    . '</div>'
                                    . '<div class="edit edit-clear-cache h50">'
                                        . '<label for="edit-clearurlcache">' . $rb->get('page.field.clearcachelabel') . ':</label> '
                                        . '<input type="checkbox" name="edit-clearurlcache" id="edit-clearurlcache" />'
                                    . '</div>'
                                    . '<div class="edit edit-cache-time h50">'
                                        . '<label for="edit-cachetime">' . $rb->get('page.field.cachetimelabel') . ':</label>'
                                        . '<select id="edit-cachetime" name="edit-cachetime">'
                                            . '<option value="-1"' . (($sql_return[0]['cachetime'] == -1) ? 'selected="selected"' : '') . '>Don\'t use</option>'
                                            . '<option value="60"' . (($sql_return[0]['cachetime'] == 60) ? 'selected="selected"' : '') . '>1 minute</option>'
                                            . '<option value="3600"' . (($sql_return[0]['cachetime'] == 3600) ? 'selected="selected"' : '') . '>1 hour</option>'
                                            . '<option value="86400"' . (($sql_return[0]['cachetime'] == 86400) ? 'selected="selected"' : '') . '>1 day</option>'
                                            . '<option value="172800"' . (($sql_return[0]['cachetime'] == 172800) ? 'selected="selected"' : '') . '>2 days</option>'
                                            . '<option value="259200"' . (($sql_return[0]['cachetime'] == 259200) ? 'selected="selected"' : '') . '>3 days</option>'
                                            . '<option value="604800"' . (($sql_return[0]['cachetime'] == 604800) ? 'selected="selected"' : '') . '>1 week</option>'
                                            . '<option value="0"' . (($sql_return[0]['cachetime'] == 0) ? 'selected="selected"' : '') . '>Unlimited</option>'
                                        . '</select>'
                                    . '</div>';

                        if ($type == 'add-new-page' || $type == 'page-add-lang-ver') {
                            $returnTmp .= ''
                                    . '<div class="edit edit-language">'
                                    . '<label for="select-language">' . $rb->get('page.field.languagelabel') . ': </label>'
                                    . '<select id="select-language" name="language">';

                            $parentPage = $dbObject->fetchAll('SELECT `parent_id` FROM `page` WHERE `id` = ' . $pageId . ';');
                            if ($type == "page-add-lang-ver" && $parentPage[0]['parent_id']) {
                                $langs = $dbObject->fetchAll("SELECT `language`.`id`, `language`.`language` FROM `language` LEFT JOIN `info` ON `language`.`id` = `info`.`language_id` WHERE `info`.`page_id` = " . $parentPage[0]['parent_id'] . " ORDER BY `language`.`language`;");
                            } else {
                                $langs = $dbObject->fetchAll("SELECT `language`.`id`, `language`.`language` FROM `language` ORDER BY `language`.`language`;");
                            }

                            $iOk = 0;
                            foreach ($langs as $lang) {
                                $ok = true;
                                foreach ($usedLangs as $usedLang) {
                                    if (in_array($lang['id'], $usedLang)) {
                                        $ok = false;
                                    }
                                }
                                if ($ok) {
                                    $returnTmp .= '<option value="' . $lang['id'] . '">' . $lang['language'] . '</option>';
                                    $iOk++;
                                }
                            }

                            if ($iOk == 0) {
                                $langsCount = false;
                            }

                            $returnTmp .= '</select></div>';
                        }

                        $name = 'Page.editors';
                        $propertyEditors = parent::system()->getPropertyValue($name);
                        $editAreaContentRows = parent::system()->getPropertyValue('Page.editAreaContentRows');
                        $editAreaHeadRows = parent::system()->getPropertyValue('Page.editAreaHeadRows');
                        $editAreaTLStartRows = parent::system()->getPropertyValue('Page.editAreaTLStartRows');
                        $editAreaTLEndRows = parent::system()->getPropertyValue('Page.editAreaTLEndRows');

                        $returnTmp .= ''
                        . '</div>'
                        . (($this->getGroupPermCached('Page.ManageRights')) ? ''
                                . '<div class="gray-box-float" style="min-width:650px;">'
                                . '<details>'
                                . '<summary>' . $rb->get('page.field.permissions') . '</summary>'
                                . (($show['read']) ? ''
                                        . '<div class="float-left mr-1">'
                                        . '<label for="right-edit-groups-r" class="d-block">' . $rb->get('page.field.rreadlabel') . ':</label>'
                                        . $groupSelectR
                                        . '</div>' : '')
                                . (($show['write']) ? ''
                                        . '<div class="float-left mr-1">'
                                        . '<label for="right-edit-groups-w" class="d-block">' . $rb->get('page.field.rwritelabel') . ':</label>'
                                        . $groupSelectW
                                        . '</div>' : '')
                                . (($show['delete']) ? ''
                                        . '<div class="float-left mr-1">'
                                        . '<label for="right-edit-groups-d" class="d-block">' . $rb->get('page.field.rdeletelabel') . ':</label>'
                                        . $groupSelectD
                                        . '</div>' : '')
                                . (($show['addchild']) ? ''
                                        . '<div class="float-left mr-1">'
                                        . '<label for="right-edit-groups-a" class="d-block">' . $rb->get('page.field.raddchildlabel') . ':</label>'
                                        . $groupSelectA
                                        . '</div>' : '')
                                . '<div class="clear"></div>'
                                . '</details>'
                                . '</div>' : '')
                        . '</div>'
                        . '<div class="clear"></div>';

                        if ($type == "page-add-lang-ver" || $type == "page-edit") {
                            $defaultLanguageId = parent::dataAccess()->fetchSingle(parent::sql()->select("language", array("id"), array("language" => "")))["id"];
                            $hasDefaultContent = parent::dataAccess()->fetchSingle(parent::sql()->count("content", array("language_id" => $defaultLanguageId, "page_id" => $pageId)))["count"] != 0;
                            if ($hasDefaultContent) {
                                $returnTmp .= ''
                                . '<div class="gray-box">'
                                    . '<label>'
                                    . '<input type="checkbox" name="edit-contentless"' . ($sql_return[0]["contentless"] == 1 ? ' checked="checked"' : '') . '> ' 
                                    . $rb->get("page.field.contentless") 
                                    . '</label>'
                                . '</div>';
                            }
                        }

                        if ($propertyEditors == 'edit_area') {
                            $returnTmp .= ''
                            . '<div id="editors" class="editors edit-area-editors">'
                                . '<div id="editors-tab" class="editors-tab"></div>'
                                . (($this->getGroupPermCached('Page.TagLibs')) ? ''
                                    . '<div id="cover-page-edit-tag-lib-start">'
                                        . '<label for="page-edit-tag-lib-start">' . $rb->get('page.field.tlstartlabel') . ':</label>'
                                        . '<textarea id="page-edit-tag-lib-start" class="edit-area html" name="edit-tl-start" rows="' . ($editAreaTLStartRows > 0 ? $editAreaTLStartRows : 20) . '">' . $sql_return[0]['tag_lib_start'] . '</textarea>'
                                    . '</div>'
                                    . '<div id="cover-page-edit-tag-lib-end">'
                                        . '<label for="page-edit-tag-lib-end">' . $rb->get('page.field.tlendlabel') . ':</label>'
                                        . '<textarea id="page-edit-tag-lib-end" class="edit-area html" name="edit-tl-end" rows="' . ($editAreaTLEndRows > 0 ? $editAreaTLEndRows : 20) . '">' . $sql_return[0]['tag_lib_end'] . '</textarea>'
                                    . '</div>' : '')
                                . (($this->getGroupPermCached('Page.Head')) ? ''
                                    . '<div id="cover-page-edit-head">'
                                        . '<label for="page-edit-head">' . $rb->get('page.field.headlabel') . ':</label>'
                                        . '<textarea id="page-edit-head" class="edit-area html" name="edit-head" rows="' . ($editAreaHeadRows > 0 ? $editAreaHeadRows : 20) . '">' . $sql_return[0]['head'] . '</textarea>'
                                    . '</div>' : '')
                                . '<div id="cover-page-edit-content">'
                                    . '<label for="page-edit-content">' . $rb->get('page.field.contentlabel') . ':</label>'
                                    . '<textarea id="page-edit-content" class="edit-area html" name="edit-content" rows="' . ($editAreaContentRows > 0 ? $editAreaContentRows : 20) . '">' . $sql_return[0]['content'] . '</textarea>'
                                . '</div>'
                            . '</div>';
                        } elseif ($propertyEditors == 'tiny') {
                            $returnTmp .= ''
                            . (($this->getGroupPermCached('Page.TagLibs')) ? ''
                                . '<div class="edit edit-tag-lib">'
                                    . '<div class="edit edit-tl-start">'
                                        . '<label for="edit-tl-start">' . $rb->get('page.field.tlstartlabel') . ':</label>'
                                        . '<div class="editor-cover">'
                                            . '<div class="textarea-cover">'
                                                . '<textarea name="edit-tl-start" class="editor-textarea editor-closed" wrap="off" rows="' . ($editAreaTLStartRows > 0 ? $editAreaTLStartRows : 20) . '">' . $sql_return[0]['tag_lib_start'] . '</textarea>'
                                            . '</div>'
                                            . '<div class="clear"></div>'
                                        . '</div>'
                                    . '</div>'
                                    . '<div class="edit edit-tl-end">'
                                        . '<label for="edit-tl-end">' . $rb->get('page.field.tlendlabel') . ':</label>'
                                        . '<div class="editor-cover">'
                                            . '<div class="textarea-cover">'
                                                . '<textarea name="edit-tl-end" class="editor-textarea editor-closed" wrap="off" rows="' . ($editAreaTLEndRows > 0 ? $editAreaTLEndRows : 20) . '">' . $sql_return[0]['tag_lib_end'] . '</textarea>'
                                            . '</div>'
                                            . '<div class="clear"></div>'
                                        . '</div>'
                                    . '</div>'
                                . '</div>' : '')
                            . '<div class="edit edit-content">'
                                . (($this->getGroupPermCached('Page.Head')) ? ''
                                    . '<div class="edit edit-head">'
                                        . '<label for="edit-head">' . $rb->get('page.field.headlabel') . ':</label>'
                                        . '<div class="editor-cover">'
                                            . '<div class="textarea-cover">'
                                                . '<textarea name="edit-head" class="editor-textarea editor-closed" wrap="off" rows="' . ($editAreaHeadRows > 0 ? $editAreaHeadRows : 20) . '">' . $sql_return[0]['head'] . '</textarea>'
                                            . '</div>'
                                            . '<div class="clear"></div>'
                                        . '</div>'
                                    . '</div>' : '')
                                . '<div class="edit edit-content">'
                                    . '<label for="page-content">' . $rb->get('page.field.contentlabel') . ':</label>'
                                    . '<div class="editor-cover">'
                                        . '<div class="tiny-cover">'
                                            . '<textarea name="edit-content" class="" id="page-content" wrap="off" rows="' . ($editAreaContentRows > 0 ? $editAreaContentRows : 20) . '">' . str_replace('~', '&#126', $sql_return[0]['content']) . '</textarea>'
                                        . '</div>'
                                        . '<div class="clear"></div>'
                                    . '</div>'
                                . '</div>'
                            . '</div>';

                            $js = parent::autolib('js');
                            $return .= $js->tinyMce("page-content", $this->web()->LanguageName);
                        } else {
                            $returnTmp .= ''
                            . (($this->getGroupPermCached('Page.TagLibs')) ? ''
                                . '<div class="edit edit-tag-lib">'
                                    . '<div class="edit edit-tl-start">'
                                        . '<label for="edit-tl-start">' . $rb->get('page.field.tlstartlabel') . ':</label>'
                                        . '<div class="editor-cover">'
                                            . '<div class="textarea-cover">'
                                                . '<textarea name="edit-tl-start" class="editor-textarea editor-closed" wrap="off" rows="4">' . $sql_return[0]['tag_lib_start'] . '</textarea>'
                                            . '</div>'
                                            . '<div class="clear"></div>'
                                        . '</div>'
                                    . '</div>'
                                    . '<div class="edit edit-tl-end">'
                                        . '<label for="edit-tl-end">' . $rb->get('page.field.tlendlabel') . ':</label>'
                                        . '<div class="editor-cover">'
                                            . '<div class="textarea-cover">'
                                                . '<textarea name="edit-tl-end" class="editor-textarea editor-closed" wrap="off" rows="4">' . $sql_return[0]['tag_lib_end'] . '</textarea>'
                                            . '</div>'
                                            . '<div class="clear"></div>'
                                        . '</div>'
                                    . '</div>'
                                . '</div>' : '')
                            . '<div class="edit edit-content">'
                            . (($this->getGroupPermCached('Page.Head')) ? ''
                                    . '<div class="edit edit-head">'
                                        . '<label for="edit-head">' . $rb->get('page.field.headlabel') . ':</label>'
                                        . '<div class="editor-cover">'
                                            . '<div class="textarea-cover">'
                                                . '<textarea name="edit-head" class="editor-textarea editor-closed" wrap="off" rows="4">' . $sql_return[0]['head'] . '</textarea>'
                                            . '</div>'
                                            . '<div class="clear"></div>'
                                        . '</div>'
                                    . '</div>' : '')
                                . '<div class="edit edit-content">'
                                    . '<label for="edit-content">' . $rb->get('page.field.contentlabel') . ':</label>'
                                    . '<div class="editor-cover">'
                                        . '<div class="textarea-cover">'
                                            . '<textarea name="edit-content" class="editor-textarea editor-tiny" wrap="off" rows="15">' . $sql_return[0]['content'] . '</textarea>'
                                        . '</div>'
                                        . '<div class="clear"></div>'
                                    . '</div>'
                                . '</div>'
                            . '</div>';
                        }

                        $returnTmp .= ''
                        . '<div class="edit edit-submit">'
                            . '<input type="hidden" name="parent-id" value="' . $parentId . '" />'
                            . '<input type="hidden" name="page-id" value="' . $pageId . '" />';

                        if ($type != "add-new-page" && $type != "page-add-lang-ver") {
                            $returnTmp .= '<input type="hidden" name="language" value="' . $langId . '" />';
                        }

                        $returnTmp .= '<input type="hidden" name="type" value="' . $type . '" />'
                                . '<input type="submit" name="edit-save" value="' . $rb->get('page.action.save') . '" /> '
                                . '<input type="submit" name="edit-save" value="' . $rb->get('page.action.saveandclose') . '" /> '
                                . '<input type="submit" name="edit-close" value="' . $rb->get('page.action.close') . '" /> '
                            . '</div>'
                        . ' </form>';
                    } else {
                        $returnTmp .= '<h4 class="warning">' . $rb->get('page.warning.nopage') . '</h4>';
                    }

                    if ($langsCount) {
                        $return .= parent::getFrame($frameTitle, $returnTmp, "page-editpage");
                    } else {
                        //$return .= parent::getFrame($frameTitle, '<h4 class="error">You can\'t add more language versions at this moment! Please, first, add language version to parent page or if this is root page, create more language versions in web application!</h4>', "");
                        $return .= parent::getFrame($frameTitle, '<h4 class="error">' . $rb->get('page.error.alllangversionsused') . '</h4>', "");
                    }
                } else {
                    $this->MessageFromEdit .= '<h4 class="error">' . $rb->get('page.error.permissiondenied') . '</h4>';
                }
            }

            return $return;
        }

        /**
         *
         *  Generates table with informations about web pages.
         *  C tag.     
         *  
         *  @param  editable  if true, it shows also form editing
         *  @return formed list of web pages               
         *
         */
        public function showPages($editable = false) {
            global $dbObject;
            global $loginObject;
            global $webObject;
            $rb = $this->rb();
            $return = '';

            $projects = $dbObject->fetchAll('SELECT `web_project`.`id` FROM `web_project` LEFT JOIN `web_project_right` ON `web_project`.`id` = `web_project_right`.`wp` LEFT JOIN `group` ON `web_project_right`.`gid` = `group`.`gid` WHERE `web_project_right`.`type` = ' . WEB_R_WRITE . ' AND `group`.`value` >= ' . $loginObject->getGroupValue() . ';');
            if (count($projects) != 0) {
                if (array_key_exists('selected-project', $_SESSION)) {
                    $projectId = $_SESSION['selected-project'];
                    $test = $dbObject->fetchAll('SELECT `web_project`.`id` FROM `web_project` LEFT JOIN `web_project_right` ON `web_project`.`id` = `web_project_right`.`wp` LEFT JOIN `group` ON `web_project_right`.`gid` = `group`.`gid` WHERE `web_project`.`id` = ' . $projectId . ' AND `web_project_right`.`type` = ' . WEB_R_WRITE . ' AND `group`.`value` >= ' . $loginObject->getGroupValue() . ';');
                    if (count($test) == 0) {
                        $projectId = $projects[0]['id'];
                    }
                } else {
                    $projectId = $projects[0]['id'];
                }
            } else {
                if (array_key_exists('selected-project', $_SESSION)) {
                    $projectId = $_SESSION['selected-project'];
                } else {
                    return parent::getFrame($rb->get('pagelist.title'), '<h4 class="warning">' . $rb->get('pagelist.warning.nopages') . '</h4>', "page-pagelist", true);
                }
            }

            // save block ------------------------

            if ($_POST['delete'] == $rb->get('pagelist.action.delete')) {
                $pageId = $_POST['page-id'];
                $languageId = $_POST['page-lang-id'];

                /* $rights = $dbObject->fetchAll("SELECT `group`.`name` FROM `group` LEFT JOIN `page_right` ON `group`.`gid` = `page_right`.`gid` WHERE `page_right`.`pid` = ".$pageId." AND `page_right`.`type` = ".WEB_R_DELETE.";");
                $ok = true;
                if(count($rights) > 0) {
                $ok = false;
                foreach($rights as $right) {
                foreach($loginObject->getGroups() as $u_gp) {
                if($right['name'] == $u_gp['name']) {
                $ok = true;
                }
                }
                }
                } */

                $rights = $dbObject->fetchAll('SELECT `group`.`name` FROM `group` LEFT JOIN `page_right` ON `group`.`gid` = `page_right`.`gid` WHERE `page_right`.`pid` = ' . $pageId . ' AND `page_right`.`type` = ' . WEB_R_DELETE . ' AND (`group`.`gid` IN (' . $loginObject->getGroupsIdsAsString() . ') OR `group`.`parent_gid` IN (' . $loginObject->getGroupsIdsAsString() . '));');

                $ok = true;
                if (count($rights) == 0) {
                    $ok = false;
                }

                if ($ok) {
                    if ($languageId != "") {
                        $dbObject->execute("DELETE FROM `info` WHERE `page_id` = " . $pageId . " AND `language_id` = " . $languageId . ";");
                        $dbObject->execute("DELETE FROM `content` WHERE `page_id` = " . $pageId . " AND `language_id` = " . $languageId . ";");

                        if (count($dbObject->fetchAll("SELECT `name` FROM `info` WHERE `page_id` = " . $pageId . ";")) == 0) {
                            $dbObject->execute("DELETE FROM `page` WHERE `id` = " . $pageId . ";");
                            $dbObject->execute("DELETE FROM `page_right` WHERE `pid` = " . $pageId . ";");
                            $dbObject->execute("DELETE FROM `page_file_inc` WHERE `page_id` = " . $pageId . ";");
                        }

                        $dbObject->execute("DELETE FROM `urlcache` WHERE `pages_id` LIKE \"%-" . $pageId . "-%\" AND `language_id` = " . $languageId . ";");
                        $dbObject->execute("DELETE FROM `urlcache` WHERE `pages_id` LIKE \"" . $pageId . "-%\" AND `language_id` = " . $languageId . ";");
                        $dbObject->execute("DELETE FROM `urlcache` WHERE `pages_id` LIKE \"%-" . $pageId . "\" AND `language_id` = " . $languageId . ";");
                        $dbObject->execute("DELETE FROM `urlcache` WHERE `pages_id` = \"" . $pageId . "\" AND `language_id` = " . $languageId . ";");

                        $return .= parent::getFrame("Success Message", '<h4 class="success">Laguage version deleted!</h4>', "", true);
                    } else {
                        $dbObject->execute("DELETE FROM `info` WHERE `page_id` = " . $pageId . ";");
                        $dbObject->execute("DELETE FROM `content` WHERE `page_id` = " . $pageId . ";");
                        $dbObject->execute("DELETE FROM `page` WHERE `id` = " . $pageId . ";");
                        $dbObject->execute("DELETE FROM `page_right` WHERE `pid` = " . $pageId . ";");
                        $dbObject->execute("DELETE FROM `page_file_inc` WHERE `page_id` = " . $pageId . ";");

                        $dbObject->execute("DELETE FROM `urlcache` WHERE `pages_id` LIKE \"%-" . $pageId . "-%\";");
                        $dbObject->execute("DELETE FROM `urlcache` WHERE `pages_id` LIKE \"" . $pageId . "-%\";");
                        $dbObject->execute("DELETE FROM `urlcache` WHERE `pages_id` LIKE \"%-" . $pageId . "\";");
                        $dbObject->execute("DELETE FROM `urlcache` WHERE `pages_id` = \"" . $pageId . "\";");

                        $this->MessageFromEdit .= '<h4 class="success">' . $rb->get('pagelist.success.deleted') . '!</h4>';
                    }
                } else {
                    $this->MessageFromEdit .= '<h4 class="error">' . $rb->get('page.error.permissiondenied') . '</h4>';
                }
            }

            if ($_POST['move-up'] == $rb->get('pagelist.action.up')) {
                $pageId = $_POST['page-id'];
                $pagePos = 0;

                $pages = $dbObject->fetchAll("SELECT `page`.`id`, `info`.`page_pos` FROM `page` LEFT JOIN `info` ON `page`.`id` = `info`.`page_id` WHERE `parent_id` = (SELECT `parent_id` FROM `page` WHERE `id` = " . $pageId . ") ORDER BY `page_pos`;");
                $prevSibling = -1;
                $prevSibPos = 0;
                $i = 0;
                if (count($pages) > 1) {
                    while ($prevSibling == -1 || $i == count($pages)) {
                        if ($pages[$i]['id'] == $pageId && $i > 0) {
                            $prevSibling = $pages[$i - 1]['id'];
                            $prevSibPos = $pages[$i - 1]['page_pos'];
                            $pagePos = $pages[$i]['page_pos'];
                        }
                        $i++;
                    }
                }

                if ($prevSibling != -1) {
                    $dbObject->execute("UPDATE `info` SET `page_pos` = " . $prevSibPos . " WHERE `page_id` = " . $pageId . ";");
                    $dbObject->execute("UPDATE `info` SET `page_pos` = " . $pagePos . " WHERE `page_id` = " . $prevSibling . ";");
                } else {
                    $this->MessageFromEdit .= '<h4 class="error">Position can\'t be updated!</h4>';
                }
            } elseif ($_POST['move-down'] == $rb->get('pagelist.action.down')) {
                $pageId = $_POST['page-id'];
                $pagePos = 0;

                $pages = $dbObject->fetchAll("SELECT DISTINCT `page`.`id`, `info`.`page_pos` FROM `page` LEFT JOIN `info` ON `page`.`id` = `info`.`page_id` WHERE `parent_id` = (SELECT `parent_id` FROM `page` WHERE `id` = " . $pageId . ") ORDER BY `page_pos`;");
                $prevSibling = -1;
                $prevSibPos = 0;
                $i = 0;
                if (count($pages > 1)) {
                    while ($prevSibling == -1 || $i == count($pages)) {
                        if ($pages[$i]['id'] == $pageId && $i < count($pages)) {
                            $prevSibling = $pages[$i + 1]['id'];
                            $prevSibPos = $pages[$i + 1]['page_pos'];
                            $pagePos = $pages[$i]['page_pos'];
                        }
                        $i++;
                    }
                }

                if ($prevSibling != -1) {
                    $dbObject->execute("UPDATE `info` SET `page_pos` = " . $prevSibPos . " WHERE `page_id` = " . $pageId . ";");
                    $dbObject->execute("UPDATE `info` SET `page_pos` = " . $pagePos . " WHERE `page_id` = " . $prevSibling . ";");
                } else {
                    $this->MessageFromEdit .= '<h4 class="error">Position can\'t be updated!</h4>';
                }
            } elseif ($_POST['move-branch'] == $rb->get('pagelist.action.move') || $_POST['copy-branch'] == $rb->get('pagelist.action.copy')) {
                $pageId = $_POST['page-id'];
                // test na prava!!!!!!!!!!!!!!!!!!!

                $returnMove = '';

                $projects = $dbObject->fetchAll('SELECT DISTINCT `web_project`.`id`, `web_project`.`name` FROM `web_project` LEFT JOIN `web_project_right` ON `web_project`.`id` = `web_project_right`.`wp` LEFT JOIN `group` ON `web_project_right`.`gid` = `group`.`gid` WHERE `web_project_right`.`type` = ' . WEB_R_WRITE . ' AND (`group`.`gid` IN (' . $loginObject->getGroupsIdsAsString() . ') OR `group`.`parent_gid` IN (' . $loginObject->getGroupsIdsAsString() . ')) ORDER BY `id`;');

                $strProjects = '';
                foreach ($projects as $project) {
                    $strProjects .= '<option class="webproject" value="wp' . $project['id'] . '">' . $project['name'] . '</option>';
                    $pages = $dbObject->fetchAll('SELECT DISTINCT `page`.`id`, `info`.`name` FROM `page` LEFT JOIN `info` ON `page`.`id` = `info`.`page_id` LEFT JOIN `page_right` ON `page`.`id` = `page_right`.`pid` LEFT JOIN `group` ON `page_right`.`gid` = `group`.`gid` WHERE `page_right`.`type` = ' . WEB_R_WRITE . ' AND (`group`.`gid` IN (' . $loginObject->getGroupsIdsAsString() . ') OR `group`.`parent_gid` IN (' . $loginObject->getGroupsIdsAsString() . ')) AND `page`.`wp` = ' . $project['id'] . ' ORDER BY `info`.`page_id`;');
                    $i = 0;
                    foreach ($pages as $page) {
                        $strProjects .= '<option value="' . $page['id'] . '">- ' . (($page['id'] < 10) ? '0' . $page['id'] : $page['id'] ) . ' - ' . $page['name'] . '</option>';
                        $i++;
                    }
                }

                $returnMove .= ''
                        . '<div class="move-copy-branch">'
                        . '<form name="move-copy-branch" method="post" action="' . $_SERVER['REQUEST_URI'] . '">'
                        . '<label for="select-parent">' . $rb->get('pagelist.field.movecopyparent') . ':</label> '
                        . '<select class="select-webproject" name="select-parent" id="select-parent">'
                        . $strProjects
                        . '</select> '
                        . '<input type="hidden" name="page-id" value="' . $pageId . '" />'
                        . (($_POST['move-branch'] == $rb->get('pagelist.action.move')) ? ''
                                . '<input type="submit" name="move-branch-to" value="' . $rb->get('pagelist.action.moveto') . '" />' : ''
                                . '<input type="submit" name="copy-branch-to" value="' . $rb->get('pagelist.action.copyto') . '" />'
                        )
                        . '</form>'
                        . '</div>';

                $return .= parent::getFrame((($_POST['move-branch'] == $rb->get('pagelist.action.move')) ? $rb->get('pagelist.action.moveto') : $rb->get('pagelist.action.copyto')), $returnMove, '', true);
            } elseif ($_POST['move-branch-to'] == $rb->get('pagelist.action.moveto')) {
                $selectParent = $_POST['select-parent'];
                $pageId = $_POST['page-id'];
                // test na prava zapisu stranky !!!!!!!!!!!!!!!!!!!
                $ok = true;
                $pages = $dbObject->fetchAll('SELECT `page`.`wp`, `page_right`.`gid` FROM `page` LEFT JOIN `info` ON `page`.`id` = `info`.`page_id` LEFT JOIN `page_right` ON `page`.`id` = `page_right`.`pid` LEFT JOIN `group` ON `page_right`.`gid` = `group`.`gid` WHERE `page_right`.`type` = ' . WEB_R_WRITE . ' AND (`group`.`gid` IN (' . $loginObject->getGroupsIdsAsString() . ') OR `group`.`parent_gid` IN (' . $loginObject->getGroupsIdsAsString() . ')) AND `page`.`id` = ' . $pageId . ';');
                if (count($pages) != 0) {
                    if (substr($selectParent, 0, 2) == "wp") {
                        $projectID = substr($selectParent, 2, strlen($selectParent));
                        $parentId = 0;
                    } else {
                        $parentId = $selectParent;
                        $projectID = $dbObject->fetchAll('SELECT `wp` FROM `page` WHERE `id` = ' . $parentId . ';');
                        if (count($projectID) != 0) {
                            $projectID = $projectID[0]['wp'];
                        } else {
                            $ok = false;
                        }
                    }
                    if ($ok) {
                        // test zapisu do projektu
                        $projects = $dbObject->fetchAll('SELECT `web_project_right`.`gid` FROM `web_project_right` LEFT JOIN `group` ON `web_project_right`.`gid` = `group`.`gid` WHERE `web_project_right`.`type` = ' . WEB_R_WRITE . ' AND (`group`.`gid` IN (' . $loginObject->getGroupsIdsAsString() . ') OR `group`.`parent_gid` IN (' . $loginObject->getGroupsIdsAsString() . ')) AND `web_project_right`.`wp` = ' . $projectID . ';');
                        if (count($projects) != 0) {
                            if ($pages[0]['wp'] == $projectID) {
                                $dbObject->execute('UPDATE `page` SET `parent_id` = ' . $parentId . ' WHERE `id` = ' . $pageId . ';');
                            } else {
                                $dbObject->execute('UPDATE `page` SET `parent_id` = ' . $parentId . ', `wp` = ' . $projectID . ' WHERE `id` = ' . $pageId . ';');
                                // Zavolat fci pro rekurzivni prespsani projektu u podstranek.
                                $this->rewriteProjectIdRecursivly($pageId, $projectID);
                            }
                        } else {
                            $return .= '<h4 class="error">' . $rb->get('page.error.permissiondenied') . '</h4>';
                        }
                    } else {
                        $return .= '<h4 class="error">' . $rb->get('pagelist.error.someerror') . '!</h4>';
                    }
                } else {
                    $return .= '<h4 class="error">' . $rb->get('page.error.permissiondenied') . '</h4>';
                }
            } elseif ($_POST['copy-branch-to'] == $rb->get('pagelist.action.copyto')) {
                // Code for copying pages!! ;)

                $selectParent = $_POST['select-parent'];
                $pageId = $_POST['page-id'];
                // test na prava zapisu stranky !!!!!!!!!!!!!!!!!!!
                $ok = true;
                $pages = $dbObject->fetchAll('SELECT `page`.`wp`, `info`.`href`, `page_right`.`gid` FROM `page` LEFT JOIN `info` ON `page`.`id` = `info`.`page_id` LEFT JOIN `page_right` ON `page`.`id` = `page_right`.`pid` LEFT JOIN `group` ON `page_right`.`gid` = `group`.`gid` WHERE `page_right`.`type` = ' . WEB_R_WRITE . ' AND (`group`.`gid` IN (' . $loginObject->getGroupsIdsAsString() . ') OR `group`.`parent_gid` IN (' . $loginObject->getGroupsIdsAsString() . ')) AND `page`.`id` = ' . $pageId . ';');
                $pagesUrl = '';
                if (count($pages) != 0) {
                    $first = true;
                    foreach ($pages as $page) {
                        if ($first) {
                            $pagesUrl .= '"' . $page['href'] . '"';
                            $first = false;
                        } else {
                            $pagesUrl .= ', "' . $page['href'] . '"';
                        }
                    }
                    if (substr($selectParent, 0, 2) == "wp") {
                        $projectID = substr($selectParent, 2, strlen($selectParent));
                        $parentId = 0;
                    } else {
                        $parentId = $selectParent;
                        $projectID = $dbObject->fetchAll('SELECT `wp` FROM `page` WHERE `id` = ' . $parentId . ';');
                        if (count($projectID) != 0) {
                            $projectID = $projectID[0]['wp'];
                        } else {
                            $ok = false;
                        }
                    }

                    if ($ok) {
                        // test zapisu do projektu
                        $projects = $dbObject->fetchAll('SELECT `web_project_right`.`gid` FROM `web_project_right` LEFT JOIN `group` ON `web_project_right`.`gid` = `group`.`gid` WHERE `web_project_right`.`type` = ' . WEB_R_WRITE . ' AND (`group`.`gid` IN (' . $loginObject->getGroupsIdsAsString() . ') OR `group`.`parent_gid` IN (' . $loginObject->getGroupsIdsAsString() . ')) AND `web_project_right`.`wp` = ' . $projectID . ';');
                        if (count($projects) != 0) {
                            if ($pages[0]['wp'] == $projectID) {
                                // Testovat parentId stranka, testovat url v dane parent vetvi, kopirovat i vazby na TF
                                $urls = $dbObject->fetchAll('SELECT `href` FROM `info` LEFT JOIN `page` ON `info`.`page_id` = `page`.`id`  WHERE `page`.`parent_id` = ' . $parentId . ' AND `info`.`href` IN (' . $pagesUrl . ') AND `page`.`wp` = ' . $projectID . ';');
                                if (count($urls) == 0) {
                                    // neni treba menit url, je jedinecna v dane sekci
                                    // rekurzivne zkopirovat vsechny stranky atd.
                                    $pages = $dbObject->fetchAll('SELECT `page`.`id`, `info`.`language_id`, `info`.`name`, `info`.`in_title`, `info`.`href`, `info`.`in_menu`, `info`.`is_visible`, `info`.`keywords`, `info`.`cachetime`, `content`.`tag_lib_start`, `content`.`tag_lib_end`, `content`.`head`, `content`.`content` FROM `page` LEFT JOIN `info` ON `page`.`id` = `info`.`page_id` LEFT JOIN `content` ON `page`.`id` = `content`.`page_id` WHERE `page`.`id` = ' . $pageId . ' ORDER BY `page`.`id`;');
                                    $lastId = 0;
                                    $newId = 0;
                                    foreach ($pages as $page) {
                                        if ($lastId != $page['id']) {
                                            $newId = $dbObject->fetchAll('SELECT MAX(`id`) AS `id` FROM `page`;');
                                            $newId = $newId[0]['id'] + 1;
                                            $dbObject->execute('INSERT INTO `page`(`id`, `parent_id`, `wp`) VALUES (' . $newId . ', ' . $parentId . ', ' . $projectId . ');');
                                            $rights = $dbObject->fetchAll('SELECT `gid`, `type` FROM `page_right` WHERE `pid` = ' . $page['id'] . ';');
                                            foreach ($rights as $right) {
                                                $dbObject->execute('INSERT INTO `page_right`(`pid`, `gid`, `type`) VALUES (' . $newId . ', ' . $right['gid'] . ', ' . $right['type'] . ');');
                                            }
                                            $this->copyPagesRecursivly($page['id'], $newId, $projectID, true);
                                        }
                                        $page['tag_lib_start'] = addslashes($page['tag_lib_start']);
                                        $page['tag_lib_end'] = addslashes($page['tag_lib_end']);
                                        $page['head'] = addslashes($page['head']);
                                        $page['content'] = addslashes($page['content']);
                                        $page['name'] = addslashes($page['name']);
                                        $page['href'] = addslashes($page['href']);
                                        $page['keywords'] = addslashes($page['keywords']);
                                        $dbObject->execute('INSERT INTO `info`(`page_id`, `language_id`, `name`, `in_title`, `href`, `in_menu`, `page_pos`, `is_visible`, `keywords`, `timestamp`, `cachetime`) VALUES (' . $newId . ', ' . $page['language_id'] . ', "' . $page['name'] . '", ' . $page['in_title'] . ', "' . $page['href'] . '", ' . $page['in_menu'] . ', ' . $newId . ', ' . $page['is_visible'] . ', "' . $page['keywords'] . '", ' . time() . ', ' . $page['cachetime'] . ');');
                                        $dbObject->execute('INSERT INTO `content`(`page_id`, `language_id`, `tag_lib_start`, `tag_lib_end`, `head`, `content`) VALUES (' . $newId . ', ' . $page['language_id'] . ', "' . $page['tag_lib_start'] . '", "' . $page['tag_lib_end'] . '", "' . $page['head'] . '", "' . $page['content'] . '");');
                                        $lastId = $page['id'];
                                    }
                                    $return .= '<h4 class="success">' . $rb->get('pagelist.success.copied') . '</h4>';
                                } else {
                                    // zmenit url na nahodnou a vypsat ji.
                                    // rekurzivne zkopirovat vsechny stranky atd.
                                    $pages = $dbObject->fetchAll('SELECT `page`.`id`, `info`.`language_id`, `info`.`name`, `info`.`in_title`, `info`.`href`, `info`.`in_menu`, `info`.`is_visible`, `info`.`keywords`, `info`.`cachetime`, `content`.`tag_lib_start`, `content`.`tag_lib_end`, `content`.`head`, `content`.`content` FROM `page` LEFT JOIN `info` ON `page`.`id` = `info`.`page_id` LEFT JOIN `content` ON `page`.`id` = `content`.`page_id` WHERE `page`.`id` = ' . $pageId . ' ORDER BY `page`.`id`;');
                                    $lastId = 0;
                                    $newId = 0;
                                    $randUrl = 'random-url-' . rand(100, 1000) . rand(100, 1000);
                                    foreach ($pages as $page) {
                                        if ($lastId != $page['id']) {
                                            $newId = $dbObject->fetchAll('SELECT MAX(`id`) AS `id` FROM `page`;');
                                            $newId = $newId[0]['id'] + 1;
                                            $dbObject->execute('INSERT INTO `page`(`id`, `parent_id`, `wp`) VALUES (' . $newId . ', ' . $parentId . ', ' . $projectId . ');');
                                            $rights = $dbObject->fetchAll('SELECT `gid`, `type` FROM `page_right` WHERE `pid` = ' . $page['id'] . ';');
                                            foreach ($rights as $right) {
                                                $dbObject->execute('INSERT INTO `page_right`(`pid`, `gid`, `type`) VALUES (' . $newId . ', ' . $right['gid'] . ', ' . $right['type'] . ');');
                                            }
                                            $this->copyPagesRecursivly($page['id'], $newId, $projectID, true);
                                        }
                                        $page['tag_lib_start'] = addslashes($page['tag_lib_start']);
                                        $page['tag_lib_end'] = addslashes($page['tag_lib_end']);
                                        $page['head'] = addslashes($page['head']);
                                        $page['content'] = addslashes($page['content']);
                                        $page['name'] = addslashes($page['name']);
                                        $page['href'] = addslashes($page['href']);
                                        $page['keywords'] = addslashes($page['keywords']);
                                        $dbObject->execute('INSERT INTO `info`(`page_id`, `language_id`, `name`, `in_title`, `href`, `in_menu`, `page_pos`, `is_visible`, `keywords`, `timestamp`, `cachetime`) VALUES (' . $newId . ', ' . $page['language_id'] . ', "' . $page['name'] . '", ' . $page['in_title'] . ', "' . $randUrl . '", ' . $page['in_menu'] . ', ' . $newId . ', ' . $page['is_visible'] . ', "' . $page['keywords'] . '", ' . time() . ', ' . $page['cachetime'] . ');');
                                        $dbObject->execute('INSERT INTO `content`(`page_id`, `language_id`, `tag_lib_start`, `tag_lib_end`, `head`, `content`) VALUES (' . $newId . ', ' . $page['language_id'] . ', "' . $page['tag_lib_start'] . '", "' . $page['tag_lib_end'] . '", "' . $page['head'] . '", "' . $page['content'] . '");');
                                        $lastId = $page['id'];
                                    }
                                    $return .= '<h4 class="success">' . $rb->get('pagelist.success.copied') . '</h4><h4 class="warning">' . $rb->get('pagelist.warning.urlchanged') . ' "' . $randUrl . '"</h4>';
                                }
                            } else {
                                // Testovat url v dane parent vetvi, nekopirovat vazby na TF
                                $urls = $dbObject->fetchAll('SELECT `href` FROM `info` LEFT JOIN `page` ON `info`.`page_id` = `page`.`id`  WHERE `page`.`parent_id` = ' . $parentId . ' AND `info`.`href` IN (' . $pagesUrl . ') AND `page`.`wp` = ' . $projectID . ';');
                                if (count($urls) == 0) {
                                    // neni treba menit url, je jedinecna v dane sekci
                                    // rekurzivne zkopirovat vsechny stranky atd.
                                    $pages = $dbObject->fetchAll('SELECT `page`.`id`, `info`.`language_id`, `info`.`name`, `info`.`in_title`, `info`.`href`, `info`.`in_menu`, `info`.`is_visible`, `info`.`keywords`, `info`.`cachetime`, `content`.`tag_lib_start`, `content`.`tag_lib_end`, `content`.`head`, `content`.`content` FROM `page` LEFT JOIN `info` ON `page`.`id` = `info`.`page_id` LEFT JOIN `content` ON `page`.`id` = `content`.`page_id` WHERE `page`.`id` = ' . $pageId . ' ORDER BY `page`.`id`;');
                                    $lastId = 0;
                                    $newId = 0;
                                    foreach ($pages as $page) {
                                        if ($lastId != $page['id']) {
                                            $newId = $dbObject->fetchAll('SELECT MAX(`id`) AS `id` FROM `page`;');
                                            $newId = $newId[0]['id'] + 1;
                                            $dbObject->execute('INSERT INTO `page`(`id`, `parent_id`, `wp`) VALUES (' . $newId . ', ' . $parentId . ', ' . $projectID . ');');
                                            $rights = $dbObject->fetchAll('SELECT `gid`, `type` FROM `page_right` WHERE `pid` = ' . $page['id'] . ';');
                                            foreach ($rights as $right) {
                                                $dbObject->execute('INSERT INTO `page_right`(`pid`, `gid`, `type`) VALUES (' . $newId . ', ' . $right['gid'] . ', ' . $right['type'] . ');');
                                            }
                                            $this->copyPagesRecursivly($page['id'], $newId, $projectID, true);
                                        }
                                        $page['tag_lib_start'] = addslashes($page['tag_lib_start']);
                                        $page['tag_lib_end'] = addslashes($page['tag_lib_end']);
                                        $page['head'] = addslashes($page['head']);
                                        $page['content'] = addslashes($page['content']);
                                        $page['name'] = addslashes($page['name']);
                                        $page['href'] = addslashes($page['href']);
                                        $page['keywords'] = addslashes($page['keywords']);
                                        $dbObject->execute('INSERT INTO `info`(`page_id`, `language_id`, `name`, `in_title`, `href`, `in_menu`, `page_pos`, `is_visible`, `keywords`, `timestamp`, `cachetime`) VALUES (' . $newId . ', ' . $page['language_id'] . ', "' . $page['name'] . '", ' . $page['in_title'] . ', "' . $page['href'] . '", ' . $page['in_menu'] . ', ' . $newId . ', ' . $page['is_visible'] . ', "' . $page['keywords'] . '", ' . time() . ', ' . $page['cachetime'] . ');');
                                        $dbObject->execute('INSERT INTO `content`(`page_id`, `language_id`, `tag_lib_start`, `tag_lib_end`, `head`, `content`) VALUES (' . $newId . ', ' . $page['language_id'] . ', "' . $page['tag_lib_start'] . '", "' . $page['tag_lib_end'] . '", "' . $page['head'] . '", "' . $page['content'] . '");');
                                        $lastId = $page['id'];
                                    }
                                    $return .= '<h4 class="success">' . $prb->get('pagelist.success.copied') . '</h4>';
                                } else {
                                    // zmenit url na nahodnou a vypsat ji.
                                    // rekurzivne zkopirovat vsechny stranky atd.
                                    $pages = $dbObject->fetchAll('SELECT `page`.`id`, `info`.`language_id`, `info`.`name`, `info`.`in_title`, `info`.`href`, `info`.`in_menu`, `info`.`is_visible`, `info`.`keywords`, `info`.`cachetime`, `content`.`tag_lib_start`, `content`.`tag_lib_end`, `content`.`head`, `content`.`content` FROM `page` LEFT JOIN `info` ON `page`.`id` = `info`.`page_id` LEFT JOIN `content` ON `page`.`id` = `content`.`page_id` WHERE `page`.`id` = ' . $pageId . ' ORDER BY `page`.`id`;');
                                    $lastId = 0;
                                    $newId = 0;
                                    $randUrl = 'random-url-' . rand(100, 1000) . rand(100, 1000);
                                    foreach ($pages as $page) {
                                        if ($lastId != $page['id']) {
                                            $newId = $dbObject->fetchAll('SELECT MAX(`id`) AS `id` FROM `page`;');
                                            $newId = $newId[0]['id'] + 1;
                                            $dbObject->execute('INSERT INTO `page`(`id`, `parent_id`, `wp`) VALUES (' . $newId . ', ' . $parentId . ', ' . $projectID . ');');
                                            $rights = $dbObject->fetchAll('SELECT `gid`, `type` FROM `page_right` WHERE `pid` = ' . $page['id'] . ';');
                                            foreach ($rights as $right) {
                                                $dbObject->execute('INSERT INTO `page_right`(`pid`, `gid`, `type`) VALUES (' . $newId . ', ' . $right['gid'] . ', ' . $right['type'] . ');');
                                            }
                                            $this->copyPagesRecursivly($page['id'], $newId, $projectID, true);
                                        }
                                        $page['tag_lib_start'] = addslashes($page['tag_lib_start']);
                                        $page['tag_lib_end'] = addslashes($page['tag_lib_end']);
                                        $page['head'] = addslashes($page['head']);
                                        $page['content'] = addslashes($page['content']);
                                        $page['name'] = addslashes($page['name']);
                                        $page['href'] = addslashes($page['href']);
                                        $page['keywords'] = addslashes($page['keywords']);
                                        $dbObject->execute('INSERT INTO `info`(`page_id`, `language_id`, `name`, `in_title`, `href`, `in_menu`, `page_pos`, `is_visible`, `keywords`, `timestamp`, `cachetime`) VALUES (' . $newId . ', ' . $page['language_id'] . ', "' . $page['name'] . '", ' . $page['in_title'] . ', "' . $randUrl . '", ' . $page['in_menu'] . ', ' . $newId . ', ' . $page['is_visible'] . ', "' . $page['keywords'] . '", ' . time() . ', ' . $page['cachetime'] . ');');
                                        $dbObject->execute('INSERT INTO `content`(`page_id`, `language_id`, `tag_lib_start`, `tag_lib_end`, `head`, `content`) VALUES (' . $newId . ', ' . $page['language_id'] . ', "' . $page['tag_lib_start'] . '", "' . $page['tag_lib_end'] . '", "' . $page['head'] . '", "' . $page['content'] . '");');
                                        $lastId = $page['id'];
                                    }
                                    $return .= '<h4 class="success">' . $prb->get('pagelist.success.copied') . '</h4><h4 class="warning">' . $rb->get('pagelist.warning.urlchanged') . ' "' . $randUrl . '"</h4>';
                                }
                            }
                        } else {
                            $return .= '<h4 class="error">' . $rb->get('page.error.permissiondenied') . '</h4>';
                        }
                    } else {
                        $return .= '<h4 class="error">' . $rb->get('pagelist.error.someerror') . '!</h4>';
                    }
                } else {
                    $return .= '<h4 class="error">' . $rb->get('page.error.permissiondenied') . '</h4>';
                }
            }

            // edit block --------------

            $returnTmp = '';

            if ($_POST['remove-files'] == $rb->get('pagelist.action.removeselected')) {
                $pageId = $_POST['page-id'];
                $langId = $_POST['page-lang-id'];
                $files = $_POST['files'];

                foreach ($files as $file => $val) {
                    if ($val = "on") {
                        //$dbObject->execute("INSERT INTO `page_file_inc`(`file_id`, `page_id`, `language_id`) VALUES (".$file.", ".$pageId.", ".$langId.");");
                        $dbObject->execute("DELETE FROM `page_file_inc` WHERE `file_id` = " . $file . " AND `page_id` = " . $pageId . " AND `language_id` = " . $langId . ";");
                    }
                }

                /* $fileId = $_POST['file-id'];
                $pageId = $_POST['page-id'];
                $langId = $_POST['page-lang-id'];

                $dbObject->execute("DELETE FROM `page_file_inc` WHERE `file_id` = ".$fileId." AND `page_id` = ".$pageId." AND `language_id` = ".$langId.";");
                */
                //$returnTmp = '<h4 class="success">'.$rb->get('pagelist.success.removed').'</h4>';
                $_POST['added-files'] = $rb->get('pagelist.action.addedfiles');
            } elseif ($_POST['add-files'] == $rb->get('pagelist.action.addselected')) {
                //print_r($_POST);
                $pageId = $_POST['page-id'];
                $langId = $_POST['page-lang-id'];
                $files = $_POST['files'];

                foreach ($files as $file => $val) {
                    if ($val = "on") {
                        $dbObject->execute("INSERT INTO `page_file_inc`(`file_id`, `page_id`, `language_id`, `order`) VALUES (" . $file . ", " . $pageId . ", " . $langId . ", " . $file . ");");
                    }
                }
                //$return .= parent::getFrame("Success Message", '<h4 class="success">Files successfully inserted!</h4>', "", true);
                //$returnTmp = '<h4 class="success">'.$rb->get('pagelist.success.added').'</h4>';
                $_POST['added-files'] = $rb->get('pagelist.action.addedfiles');
            }

            if (Order::isPost('text-file')) {
                $pageId = $_POST['page-id'];
                $langId = $_POST['page-lang-id'];
                $files = $dbObject->fetchAll("SELECT `file_id`, `page_id`, `language_id`, `order`, `name`, `content`, `type` FROM `page_file` LEFT JOIN `page_file_inc` ON `page_file`.`id` = `page_file_inc`.`file_id` WHERE `page_file_inc`.`page_id` = " . $pageId . " AND `page_file_inc`.`language_id` = " . $langId . " ORDER BY `page_file_inc`.`order`;");
                if (Order::tryUpdate($files, 'text-file', 'page_file_inc', array('file_id', 'page_id', 'language_id'), 'order')) {
                    // TODO: Add success message.
                }
                $_POST['added-files'] = $rb->get('pagelist.action.addedfiles');
            }

            if ($_POST['added-files'] == $rb->get('pagelist.action.addedfiles')) {
                $pageId = $_POST['page-id'];
                $langId = $_POST['page-lang-id'];
                $filesEx = array(WEB_TYPE_CSS => "Css", WEB_TYPE_JS => "Js");

                $files = $dbObject->fetchAll("SELECT `id`, `name`, `content`, `type` FROM `page_file` LEFT JOIN `page_file_inc` ON `page_file`.`id` = `page_file_inc`.`file_id` WHERE `page_file_inc`.`page_id` = " . $pageId . " AND `page_file_inc`.`language_id` = " . $langId . " ORDER BY `page_file_inc`.`order`;");

                if (count($files) != 0) {
                    $returnTmp .= ''
                    . '<form name="files-to-remove" method="post" action="' . $_SERVER['REQUEST_URI'] . '">'
                        . '<table class="standart clickable">'
                            . '<tr class="file-tr">'
                                . '<th colspan="5" class="file-head-th">' . $rb->get('pagelist.field.addedfiles') . ':</th>'
                            . '</tr>';

                    $additional = array('page-id' => $pageId, 'page-lang-id' => $langId);
                    $i = 1;
                    foreach ($files as $file) {
                        $returnTmp .= ''
                        . '<tr class="file-tr ' . ((($i % 2) == 0) ? 'even' : 'idle') . '">'
                            . '<td>'
                                . '<input id="remove-text-files-files-' . $file['id'] . '" type="checkbox" class="clickable-target" name="files[' . $file['id'] . ']" />'
                            . '</td>'
                            . '<td class="file-id">'
                                . $file['id']
                            . '</td>'
                            . '<td class="file-name">'
                            . '<label for="remove-text-files-files-' . $file['id'] . '">' . $file['name'] . '</label>'
                            . '</td>'
                            . '<td class="file-type">'
                                . '<label for="remove-text-files-files-' . $file['id'] . '">'
                                    . $filesEx[$file['type']]
                                . '</label>'
                            . '</td>'
                            . '<td>'
                                . Order::upForm($_SERVER['REQUEST_URI'], 'text-file', $file['id'], $rb->get('pagelist.action.addedfiles.moveup'), $additional)
                                . Order::downForm($_SERVER['REQUEST_URI'], 'text-file', $file['id'], $rb->get('pagelist.action.addedfiles.movedown'), $additional)
                            . '</td>'
                        . '</tr>';
                        $i++;
                    }

                    $returnTmp .= ''
                        . '</table>'
                        . '<div class="add-rem-text-files-submit">'
                            . '<input type="hidden" name="page-id" value="' . $pageId . '" />'
                            . '<input type="hidden" name="page-lang-id" value="' . $langId . '" />'
                            . '<input type="submit" name="remove-files" value="' . $rb->get('pagelist.action.removeselected') . '" />'
                        . '</div>'
                    . '</form>' 
                    . '<div class="break"></div>';
                    $return1 = $returnTmp;
                } else {
                    $return1 = '<h4 class="warning">' . $rb->get('pagelist.warning.nofilesadded') . '</h4>';
                }


                $files = $dbObject->fetchAll("SELECT DISTINCT `id`, `name`, `content`, `type` FROM `page_file` LEFT JOIN `page_file_inc` ON `page_file`.`id` = `page_file_inc`.`file_id` WHERE `id` NOT IN (SELECT `file_id` FROM `page_file_inc` WHERE `page_id` = " . $pageId . " AND `language_id` = " . $langId . ") AND `wp` = " . $_SESSION['selected-project'] . " ORDER BY `id`;");
                if (count($files) != 0) {
                    $returnTmp = ''
                            . '<form name="files-to-add" method="post" action="' . $_SERVER['REQUEST_URI'] . '">'
                            . '<table class="standart clickable">'
                            . '<tr class="file-tr">'
                            . '<th colspan="4" class="file-head-th">' . $rb->get('pagelist.field.filestoadd') . '</th>'
                            . '</tr>';
                    $i = 1;
                    foreach ($files as $file) {
                        $returnTmp .= ''
                        . '<tr class="file-tr ' . ((($i % 2) == 0) ? 'even' : 'idle') . '">'
                            . '<td>'
                                . '<input id="add-text-files-files-' . $file['id'] . '" type="checkbox" name="files[' . $file['id'] . ']" />'
                            . '</td>'
                            . '<td class="file-id">'
                                . $file['id']
                            . '</td>'
                            . '<td class="file-name">'
                                . '<label for="add-text-files-files-' . $file['id'] . '">' . $file['name'] . '</label>'
                            . '</td>'
                            . '<td class="file-type">'
                                . '<label for="add-text-files-files-' . $file['id'] . '">' . $filesEx[$file['type']] . '</label>'
                            . '</td>'
                        . '</tr>';
                        $i++;
                    }
                    $returnTmp .= '</table>'
                            . '<div class="add-rem-text-files-submit">'
                            . '<input type="hidden" name="page-id" value="' . $pageId . '" />'
                            . '<input type="hidden" name="page-lang-id" value="' . $langId . '" />'
                            . '<input type="submit" name="add-files" value="' . $rb->get('pagelist.action.addselected') . '" />'
                            . '</div>'
                            . '</form>';
                    //$return2 = parent::getFrame('Files to add', $returnTmp, '');
                    $return2 = $returnTmp;
                } else {
                    $return2 = '<h4 class="warning">' . $rb->get('pagelist.warning.nofilestoadd') . '</h4>';
                }
                $return .= parent::getFrame($rb->get('pagelist.textfilestitle'), $return1 . $return2, 'page-textfiles');
            }

            if ($_POST['select-lang'] == "Select") {
                $_SESSION['language'] = $_POST['language'];
                $langQuery = " `language`.`id` = " . $_SESSION['language'];
            }

            if (isset($_SESSION['language'])) {
                $langQuery = " `language`.`id` = " . $_SESSION['language'];
            } else {
                $_SESSION['language'] = 1;
                $langQuery = " `language`.`id` = " . $_SESSION['language'];
            }

            $langs = $dbObject->fetchAll("SELECT `id`, `language` FROM `language` ORDER BY `language`;");

            $returnTmp = ''
                    . '<div class="page-list">';

            $returnTmp .= '<div class="pages-list-in">';
            $returnTmp .= $this->generatePageList(0, $editable, 0, $projectId);
            $returnTmp .= '</div></div>';
            //$return .= parent::getFrame($rb->get('pagelist.title'), $returnTmp, 'page-pagelist');
            if ($_SESSION['selected-project'] != '') {
                $returnTmp = ''
                        . '<div class="add-page">'
                        . '<ul>'
                        . '<li>'
                        . $rb->get('page.newpagecaption')
                        . '<form name="add-page" method="post" action="' . $_SERVER['REQUEST_URI'] . '">'
                        . '<input type="hidden" name="parent-id" value="0" />'
                        . '<input type="hidden" name="add-new-page" value="' . $rb->get('page.action.addpage') . '" />'
                        . '<input type="image" src="~/images/page_add.png" name="add-new-page" value="' . $rb->get('page.action.addpage') . '" />'
                        . '</form>'
                        . '</li>'
                        . '</ul>'
                        . '</div>'
                        . '<hr />'
                        . $returnTmp;
                //$return .= parent::getFrame($rb->get('pagelist.newtitle'), $returnTmp, 'page-newlist');
            }

            if ($this->MessageFromEdit != '') {
                $returnTmp = $this->MessageFromEdit . $returnTmp;
                $this->MessageFromEdit = '';
            }

            $return .= parent::getFrame($rb->get('pagelist.title'), $returnTmp, 'page-pagelist');

            return $return;
        }

        /**
         *
         *  Generates page list.
         *  
         *  @param    parentId  parent page
         *  @param    editable  if true, it shows forms for editing
         *  @param    inn       number of inner recursion
         *  @param		projectId	web project id     
         *  @return   page list          
         *
         */
        private function generatePageList($parentId, $editable, $inn, $projectId) {
            global $dbObject;
            global $webObject;
            $rb = $this->rb();

            $sql_return = $dbObject->fetchAll("SELECT `page`.`parent_id`, `page`.`id` FROM `page` LEFT JOIN `info` ON `page`.`id` = `info`.`page_id` WHERE `page`.`parent_id` = " . $parentId . " AND `page`.`wp` = " . $projectId . " GROUP BY `page`.`id` ORDER BY `info`.`page_pos`;");
            if (count($sql_return) == 0 && $parentId == 0) {
                return '<h4 class="warning">' . $rb->get('pagelist.warning.nopages') . '</h4>';
            }
            if (count($sql_return) > 0)
                $return .= '<ul class="inn-' . $inn . '">';
            $count = 0;
            foreach ($sql_return as $tmp) {
                if (count($dbObject->fetchAll("SELECT `id` FROM `page` WHERE `parent_id` = " . $tmp['id'] . " AND `wp` = " . $projectId . ";")) == 0) {
                    $parent = false;
                } else {
                    $parent = true;
                }
                if ($count == (count($sql_return) - 1)) {
                    $last = " last";
                } else {
                    $last = "";
                }
                $pg_info = $dbObject->fetchAll("SELECT `info`.`name`, `info`.`is_visible`, `language`, `language`.`id` AS `lang_id` FROM `info` LEFT JOIN `language` ON `info`.`language_id` = `language`.`id` WHERE `info`.`page_id` = " . $tmp['id'] . ";");
                if (count($pg_info) > 0) {
                    $count++;
                    $innText = '<span class="page-id-col" title="Page Id">(' . $tmp['id'] . ')</span> <span class="page-name' . ($pg_info[0]['is_visible'] == 0 ? ' gray-color' : '') . '" title="Page Name">' . $pg_info[0]['name'] . '</span> : ' . '<span class="page-languages">( ';
                    foreach ($pg_info as $inf) {
                        $parentLang = $dbObject->fetchAll('SELECT `page_id` FROM `info` LEFT JOIN `page` ON `info`.`page_id` = `page`.`id` WHERE `language_id` = ' . $inf['lang_id'] . ' AND `page`.`parent_id` = ' . $tmp['id'] . ';');
                        if (count($parentLang) > 0) {
                            $thisParent = true;
                        } else {
                            $thisParent = false;
                        }

                        //echo 'PageId: '.$tmp['id'].', Parent: '.$parent.', ThisParent: '.$thisParent.'<br />';

                        $innText .= ''
                                . '<div class="page-language-version' . ($pg_info['is_visible'] == 0 ? ' gray-color' : '') . '"> { '
                                . '<span class="page-language">'
                                . '<a target="_blank" href="' . $webObject->composeUrl($tmp['id'], $inf['lang_id']) . '">' . ((strlen($inf['language']) != 0) ? $inf['language'] : "-") . '</a>'
                                . '</span>'
                                . (($this->getGroupPermCached('Page.EditDetail')) ? ''
                                        . '<form name="page1" method="post" action="' . $_SERVER['REQUEST_URI'] . '" class="form-page1">'
                                        . '<input type="hidden" name="page-id" value="' . $tmp['id'] . '" /> '
                                        . '<input type="hidden" name="parent-id" value="' . $tmp['id'] . '" /> '
                                        . '<input type="hidden" name="page-lang-id" value="' . $inf['lang_id'] . '" /> '
                                        . '<input type="hidden" name="page-edit" value="' . $rb->get('page.action.edit') . '" /> '
                                        . '<input type="image" title="' . $rb->get('pagelist.field.edit') . '" src="~/images/page_edi.png" name="page-edit" value="' . $rb->get('page.action.edit') . '" /> '
                                        . '</form>' : '')
                                . (($this->getGroupPermCached('Page.AddNew')) ? ''
                                        . '<form name="page2" method="post" action="' . $_SERVER['REQUEST_URI'] . '" class="form-page2">'
                                        . '<input type="hidden" name="page-id" value="' . $tmp['id'] . '" /> '
                                        . '<input type="hidden" name="parent-id" value="' . $tmp['id'] . '" /> '
                                        . '<input type="hidden" name="page-lang-id" value="' . $inf['lang_id'] . '" /> '
                                        . '<input type="hidden" name="page-add-sub" value="' . $rb->get('page.action.addsubpage') . '" /> '
                                        . '<input type="image" title="' . $rb->get('page.action.addsubpage') . '" src="~/images/page_add.png" name="page-add-sub" value="' . $rb->get('page.action.addsubpage') . '" /> '
                                        . '</form>' : '')
                                . (($this->getGroupPermCached('Page.ManageFiles')) ? ''
                                        . '<form name="page3" method="post" action="' . $_SERVER['REQUEST_URI'] . '" class="form-page3">'
                                            . '<input type="hidden" name="page-id" value="' . $tmp['id'] . '" /> '
                                            . '<input type="hidden" name="parent-id" value="' . $tmp['id'] . '" /> '
                                            . '<input type="hidden" name="page-lang-id" value="' . $inf['lang_id'] . '" /> '
                                            . '<input type="hidden" name="added-files" value="' . $rb->get('pagelist.field.addedfiles') . '" /> '
                                            . '<input type="image" title="' . $rb->get('pagelist.field.addedfiles') . '" src="~/images/file_bws.png" name="added-files" value="' . $rb->get('pagelist.action.addedfiles') . '" /> '
                                        . '</form>' : '')
                                . (($this->getGroupPermCached('Page.ManageProperties')) ? ''
                                        . '<form name="page3" method="post" action="' . $_SERVER['REQUEST_URI'] . '" class="form-page5">'
                                            . '<input type="hidden" name="page-id" value="' . $tmp['id'] . '" /> '
                                            . '<input type="hidden" name="manage-properties" value="' . $rb->get('pagelist.field.manageprops') . '" /> '
                                            . '<input type="image" title="' . $rb->get('pagelist.field.manageprops') . '" src="~/images/page_pro.png" name="manage-properties" value="' . $rb->get('pagelist.action.manageprops') . '" /> '
                                        . '</form>' : '')
                                . (($this->getGroupPermCached('Page.Delete') && (!$parent || !$thisParent)) ? ''
                                        . '<form name="page4" method="post" action="' . $_SERVER['REQUEST_URI'] . '" class="form-page4">'
                                        . '<input type="hidden" name="page-id" value="' . $tmp['id'] . '" /> '
                                        . '<input type="hidden" name="parent-id" value="' . $tmp['id'] . '" /> '
                                        . '<input type="hidden" name="page-lang-id" value="' . $inf['lang_id'] . '" /> '
                                        . '<input type="hidden" name="delete" value="' . $rb->get('pagelist.action.delete') . '" /> '
                                        . '<input class="confirm" type="image" title="' . $rb->get('pagelist.field.delete2') . ', id(' . $tmp['id'] . ')" src="~/images/lang_del.png" name="delete" value="' . $rb->get('pagelist.action.delete') . '" />'
                                        . '</form>' : '')
                                . ' } </div>';
                    }
                    $innText .= ''
                            . '[ '
                            . (($this->getGroupPermCached('Page.MoveTree')) ? ''
                                    . '<form name="page-move1" method="post" action="' . $_SERVER['REQUEST_URI'] . '" class="page-move1">'
                                    . '<input type="hidden" name="page-id" value="' . $tmp['id'] . '" />'
                                    . '<input type="hidden" name="move-branch" value="' . $rb->get('pagelist.action.move') . '" />'
                                    . '<input type="image" src="~/images/page_mov.png" title="' . $rb->get('pagelist.field.move') . '" name="move-branch" value="' . $rb->get('pagelist.action.move') . '" />'
                                    . '</form> ' : '')
                            . (($this->getGroupPermCached('Page.CopyTree')) ? ''
                                    . '<form name="page-move2" method="post" action="' . $_SERVER['REQUEST_URI'] . '" class="page-move2">'
                                    . '<input type="hidden" name="page-id" value="' . $tmp['id'] . '" />'
                                    . '<input type="hidden" name="copy-branch" value="' . $rb->get('pagelist.action.copy') . '" />'
                                    . '<input type="image" src="~/images/page_cop.png" title="' . $rb->get('pagelist.field.copy') . '" name="copy-branch" value="' . $rb->get('pagelist.action.copy') . '" />'
                                    . '</form> ' : '')
                            . (($this->getGroupPermCached('Page.MoveUpDown')) ? ''
                                    . '<form name="page-move3" method="post" action="' . $_SERVER['REQUEST_URI'] . '" class="page-move3">'
                                    . '<input type="hidden" name="page-id" value="' . $tmp['id'] . '" /> '
                                    . '<input type="hidden" name="move-up" value="' . $rb->get('pagelist.action.up') . '" /> '
                                    . '<input type="image" src="~/images/arro_up.png" title="' . $rb->get('pagelist.field.up') . '" name="move-up" value="' . $rb->get('pagelist.action.up') . '" />'
                                    . '</form>' : '')
                            . (($this->getGroupPermCached('Page.MoveUpDown')) ? ''
                                    . '<form name="page-move4" method="post" action="' . $_SERVER['REQUEST_URI'] . '" class="page-move4">'
                                    . '<input type="hidden" name="page-id" value="' . $tmp['id'] . '" /> '
                                    . '<input type="hidden" name="move-down" value="' . $rb->get('pagelist.action.down') . '" /> '
                                    . '<input type="image" src="~/images/arro_do.png" title="' . $rb->get('pagelist.field.down') . '" name="move-down" value="' . $rb->get('pagelist.action.down') . '" />'
                                    . '</form>' : '')
                            . '] '
                            . (($this->getGroupPermCached('Page.AddLang')) ? ''
                                    . '<form name="page-add-lang1" method="post" action="' . $_SERVER['REQUEST_URI'] . '" class="page-add-lang1">'
                                    . '<input type="hidden" name="page-id" value="' . $tmp['id'] . '" /> '
                                    . '<input type="hidden" name="page-add-lang-ver" value="' . $rb->get('page.action.addlang') . '" /> '
                                    . '<input type="image" title="' . $rb->get('pagelist.field.addlang') . '" src="~/images/lang_add.png" name="page-add-lang-ver" value="' . $rb->get('page.action.addlang') . '" /> '
                                    . '</form>' : '')
                            . (($this->getGroupPermCached('Page.Delete') && count($dbObject->fetchAll("SELECT `id` FROM `page` WHERE `parent_id` = " . $tmp['id'] . ";")) == 0) ? ''
                                    . '<form name="page-add-lang2" method="post" action="' . $_SERVER['REQUEST_URI'] . '" class="page-add-lang2">'
                                    . '<input type="hidden" name="page-id" value="' . $tmp['id'] . '" /> '
                                    . '<input type="hidden" name="delete" value="' . $rb->get('pagelist.action.delete') . '" /> '
                                    . '<input class="confirm" type="image" title="' . $rb->get('pagelist.field.delete') . ', id(' . $tmp['id'] . ')" src="~/images/page_del.png" name="delete" value="' . $rb->get('pagelist.action.delete') . '" />'
                                    . '</form>' : '')
                            . ' )</span>';
                    $return .= ''
                            . '<li class="page page-item-' . $count . ' inn-' . $inn . (($parent) ? ' parent' : ' single') . $last . '">'
                            . (($editable) ? '<div><div><span class="page page-id">' . $innText . '</span></div></div>' : '')
                            . $this->generatePageList($tmp['id'], $editable, $inn + 1, $projectId)
                            . '</li>';
                }
            }
            if (count($sql_return) > 0)
                $return .= '</ul>';

            return $return;
        }

        private function inAsocArray($val, $array, $key) {
            //print_r($array);
            foreach ($array as $item) {
                //echo $item[$key].' == '.$val.'<br />';
                if ($item[$key] == $val) {
                    return true;
                }
            }
            return false;
        }

        private function getGroupPermCached($name, $default = 'true') {
            if (parent::request()->exists($name, 'page-gperm')) {
                return parent::request()->get($name, 'page-gperm');
            } else {
                $value = parent::getGroupPerm($name, parent::login()->getMainGroupId(), true, $default);
                $value = ($value['value'] == 'true' ? true : false);
                parent::request()->set($name, $value, 'page-gperm');
                return $value;
            }
        }

        /**
         *
         * 	Recursivly rewrites project id every page under pageId.
         *
         * 	@param	pageId			root page id
         * 	@param	projectId		web project id
         * 	@return	none
         *
         */
        private function rewriteProjectIdRecursivly($pageId, $projectId) {
            global $dbObject;

            $pages = $dbObject->fetchAll('SELECT `id` FROM `page` WHERE `parent_id` = ' . $pageId . ';');
            foreach ($pages as $page) {
                $dbObject->execute('UPDATE `page` SET `wp` = ' . $projectId . ' WHERE `id` = ' . $page['id'] . ';');
                $this->rewriteProjectIdRecursivly($page['id'], $projectId);
            }
        }

        // Jeste doresit kopirovat pageFileInc
        private function copyPagesRecursivly($parentId, $newParentId, $projectId, $pageFileInc = false) {
            global $dbObject;

            if ($pageFileInc) {
                $pages = $dbObject->fetchAll('SELECT `page`.`id`, `info`.`language_id`, `info`.`name`, `info`.`in_title`, `info`.`href`, `info`.`in_menu`, `info`.`is_visible`, `info`.`keywords`, `info`.`cachetime`, `content`.`tag_lib_start`, `content`.`tag_lib_end`, `content`.`head`, `content`.`content` FROM `page` LEFT JOIN `info` ON `page`.`id` = `info`.`page_id` LEFT JOIN `content` ON `page`.`id` = `content`.`page_id` WHERE `page`.`parent_id` = ' . $parentId . ' ORDER BY `page`.`id`;');
                $lastId = 0;
                $newId = 0;
                foreach ($pages as $page) {
                    if ($lastId != $page['id']) {
                        $newId = $dbObject->fetchAll('SELECT MAX(`id`) AS `id` FROM `page`;');
                        $newId = $newId[0]['id'] + 1;
                        $dbObject->execute('INSERT INTO `page`(`id`, `parent_id`, `wp`) VALUES (' . $newId . ', ' . $newParentId . ', ' . $projectId . ');');
                        $rights = $dbObject->fetchAll('SELECT `gid`, `type` FROM `page_right` WHERE `pid` = ' . $page['id'] . ';');
                        foreach ($rights as $right) {
                            $dbObject->execute('INSERT INTO `page_right`(`pid`, `gid`, `type`) VALUES (' . $newId . ', ' . $right['gid'] . ', ' . $right['type'] . ');');
                        }
                        $this->copyPagesRecursivly($page['id'], $newId, $projectId, $pageFileInc);
                    }
                    $page['tag_lib_start'] = addslashes($page['tag_lib_start']);
                    $page['tag_lib_end'] = addslashes($page['tag_lib_end']);
                    $page['head'] = addslashes($page['head']);
                    $page['content'] = addslashes($page['content']);
                    $page['name'] = addslashes($page['name']);
                    $page['href'] = addslashes($page['href']);
                    $page['keywords'] = addslashes($page['keywords']);
                    $dbObject->execute('INSERT INTO `info`(`page_id`, `language_id`, `name`, `in_title`, `href`, `in_menu`, `page_pos`, `is_visible`, `keywords`, `timestamp`, `cachetime`) VALUES (' . $newId . ', ' . $page['language_id'] . ', "' . $page['name'] . '", ' . $page['in_title'] . ', "' . $page['href'] . '", ' . $page['in_menu'] . ', ' . $newId . ', ' . $page['is_visible'] . ', "' . $page['keywords'] . '", ' . time() . ', ' . $page['cachetime'] . ');');
                    $dbObject->execute('INSERT INTO `content`(`page_id`, `language_id`, `tag_lib_start`, `tag_lib_end`, `head`, `content`) VALUES (' . $newId . ', ' . $page['language_id'] . ', "' . $page['tag_lib_start'] . '", "' . $page['tag_lib_end'] . '", "' . $page['head'] . '", "' . $page['content'] . '");');

                    $lastId = $page['id'];
                }
            } else {

            }
        }

        public function showEditPageFile() {
            global $dbObject;
            global $loginObject;
            $rb = $this->rb();
            $return = "";
            $filesEx = array(WEB_TYPE_CSS => "Css", WEB_TYPE_JS => "Js");

            $projects = $dbObject->fetchAll('SELECT `web_project`.`id` FROM `web_project` LEFT JOIN `web_project_right` ON `web_project`.`id` = `web_project_right`.`wp` LEFT JOIN `group` ON `web_project_right`.`gid` = `group`.`gid` WHERE `web_project_right`.`type` = ' . WEB_R_WRITE . ' AND (`group`.`gid` IN (' . $loginObject->getGroupsIdsAsString() . ') OR `group`.`parent_gid` IN (' . $loginObject->getGroupsIdsAsString() . '));');
            if (count($projects) != 0) {
                if (array_key_exists('selected-project', $_SESSION)) {
                    $projectId = $_SESSION['selected-project'];
                } else {
                    $projectId = $projects[0]['id'];
                }
            } else {
                if (array_key_exists('selected-project', $_SESSION)) {
                    $projectId = $_SESSION['selected-project'];
                } else {
                    // dont care.
                }
            }

            if ($_POST['add-file'] == $rb->get('tf.new')) {
                $fileTypesOpt = "";
                foreach ($filesEx as $key => $ext) {
                    $fileTypesOpt .= '<option value="' . $key . '">' . $ext . '</option>';
                }

                $placementOptions = "";
                $placementOptions .= '<option selected="selected" value="head">' . parent::rb()->get("tf.placement.head") . '</option>';
                $placementOptions .= '<option value="tail">' . parent::rb()->get("tf.placement.tail") . '</option>';

                $browsers['All'] = 1;
                $browsers['IE6'] = 0;
                $browsers['IE7'] = 0;
                $browsers['IE8'] = 0;
                $browsers['Firefox'] = 0;
                $browsers['Opera'] = 0;
                $browsers['Safari'] = 0;

                $return .= $this->getFileUpdateForm(-1, '', '', $browsers, $fileTypesOpt, $placementOptions);
            }

            if ($_POST['save'] == $rb->get('tf.save') || $_POST['save'] == $rb->get('tf.saveandclose')) {
                if (array_key_exists("file-id", $_POST)) {
                    $fileId = $_POST['file-id'];
                }

                $getBrowserValue = function($key) {
                    return ($_POST[$key] == "on") ? 1 : 0;
                };

                $data = [
                    "name" => $_POST['file-name'],
                    "content" => str_replace('&#126', '~', $_POST['file-content']),
                    "for_all" => $getBrowserValue('browser-all'),
                    "for_msie6" => $getBrowserValue('browser-ie6'),
                    "for_msie7" => $getBrowserValue('browser-ie7'),
                    "for_msie8" => $getBrowserValue('browser-ie8'),
                    "for_firefox" => $getBrowserValue('browser-firefox'),
                    "for_opera" => $getBrowserValue('browser-opera'),
                    "for_safari" => $getBrowserValue('browser-safari'),
                    "type" => $_POST['file-type'],
                    "placement" => $_POST['file-placement'] == 'tail' ? 1 : 0,
                    "wp" => $projectId
                ];

                $newFile = false;

                if (isset($fileId)) {
                    $sql = parent::sql()->update("page_file", $data, ["id" => $fileId]);
                    parent::dataAccess()->execute($sql);
                } else {
                    $sql = parent::sql()->insert("page_file", $data);
                    parent::dataAccess()->execute($sql);
                    $newFile = true;
                }

                if ($_POST['save'] == $rb->get('tf.save')) {
                    $_POST['edit-file'] = $rb->get('tf.edit');
                    if ($newFile) {
                        $newFileId = parent::dataAccess()->getLastId();
                        $_POST['file-id'] = $newFileId;
                    }
                }
            }

            if ($_POST['edit-file'] == $rb->get('tf.edit')) {
                $fileId = $_POST['file-id'];

                $file = $dbObject->fetchSingle("SELECT `name`, `content`, `placement`, `for_all`, `for_msie6`, `for_msie7`, `for_msie8`, `for_firefox`, `for_opera`, `for_safari`, `type` FROM `page_file` WHERE `id` = " . $fileId . ";");
                if (!empty($file)) {
                    $fileTypesOpt = "";
                    foreach ($filesEx as $key => $ext) {
                        $fileTypesOpt .= '<option ' . (($key == $file['type']) ? 'selected="selected"' : '') . 'value="' . $key . '">' . $ext . '</option>';
                    }

                    $placementOptions = "";
                    $placementOptions .= '<option ' . ((0 == $file['placement']) ? 'selected="selected"' : '') . 'value="head">' . parent::rb()->get("tf.placement.head") . '</option>';
                    $placementOptions .= '<option ' . ((1 == $file['placement']) ? 'selected="selected"' : '') . 'value="tail">' . parent::rb()->get("tf.placement.tail") . '</option>';

                    $browsers['All'] = $file['for_all'];
                    $browsers['IE6'] = $file['for_msie6'];
                    $browsers['IE7'] = $file['for_msie7'];
                    $browsers['IE8'] = $file['for_msie8'];
                    $browsers['Firefox'] = $file['for_firefox'];
                    $browsers['Opera'] = $file['for_opera'];
                    $browsers['Safari'] = $file['for_safari'];

                    $return .= $this->getFileUpdateForm($fileId, $file['name'], $file['content'], $browsers, $fileTypesOpt, $placementOptions);
                } else {
                    $return .= parent::getFrame($rb->get('tf.title'), parent::getError($rb->get('tf.notselected')), '', true);
                }
            }

            return $return;
        }

        //C-tag
        public function managePageProperties($useFrames) {
            $pageId = $_POST['page-id'];
            
            if ($_POST['manageprops-delete'] == parent::rb('button.deleteselected')) {
                foreach($_POST['manageprops-delete-item'] as $i => $item) {
                    parent::dao('PageProperty')->delete($i);
                }
            
                $_POST['manage-properties'] = parent::rb('pagelist.action.manageprops');
            }
            
            if ($_POST['manageprops-save'] == parent::rb('button.save')) {
                //Update
                foreach($_POST['manageprops-name'] as $i => $name) {
                    $item = array('name' => $_POST['manageprops-name'][$i], 'value' => $_POST['manageprops-value'][$i], 'page_id' => $pageId, 'id' => $i);
                    parent::dao('PageProperty')->update($item);
                }
                
                //New
                $new = array('name' => $_POST['manageprops-name-new'], 'value' => $_POST['manageprops-value-new'], 'page_id' => $pageId);
                if(strlen($new['name']) > 0) {
                    parent::dao('PageProperty')->insert($new);
                }
                $_POST['manage-properties'] = parent::rb('pagelist.action.manageprops');
            }
        
            if($_POST['manage-properties'] == parent::rb('pagelist.action.manageprops')) {
                $dataModel = array('items' => parent::dao('PageProperty')->getPage($pageId), 'page_id' => $pageId);
            
                if($useFrames) {
                    return parent::getFrame(parent::rb('title.manageprops'), $return.parent::view('page-manageprops', $dataModel), true);
                } else {
                    return $return.parent::view('page-manageprops', $dataModel);
                }
            }
        }
        
        /**
         *
         *  Generates table with informations about page files.
         *  C tag.     
         *  
         *  @param  editable  if true, it shows also form editing
         *  @return formed list of page files
         *
         */
        public function showPageFiles($editable = false) {
            global $dbObject;
            global $loginObject;
            $rb = $this->rb();
            $return = "";
            $editable = (strtolower($editable) == "true") ? true : false;
            $filesEx = array(WEB_TYPE_CSS => "Css", WEB_TYPE_JS => "Js");

            $projects = $dbObject->fetchAll('SELECT `web_project`.`id` FROM `web_project` LEFT JOIN `web_project_right` ON `web_project`.`id` = `web_project_right`.`wp` LEFT JOIN `group` ON `web_project_right`.`gid` = `group`.`gid` WHERE `web_project_right`.`type` = ' . WEB_R_WRITE . ' AND (`group`.`gid` IN (' . $loginObject->getGroupsIdsAsString() . ') OR `group`.`parent_gid` IN (' . $loginObject->getGroupsIdsAsString() . '));');
            if (count($projects) != 0) {
                if (array_key_exists('selected-project', $_SESSION)) {
                    $projectId = $_SESSION['selected-project'];
                } else {
                    $projectId = $projects[0]['id'];
                }
            } else {
                if (array_key_exists('selected-project', $_SESSION)) {
                    $projectId = $_SESSION['selected-project'];
                } else {
                    return parent::getFrame($rb->get('tf.title'), parent::getWarning($rb->get('tf.emptylist')), "", true);
                }
            }

            // text file form block ---------------------

            if ($_POST['delete-file'] == $rb->get('tf.delete')) {
                $fileId = $_POST['file-id'];
                $dbObject->execute("DELETE FROM `page_file_inc` WHERE `file_id` = " . $fileId . ";");
                $dbObject->execute("DELETE FROM `wp_wysiwyg_file` WHERE `tf_id` = " . $fileId . ";");
                $dbObject->execute("DELETE FROM `page_file` WHERE `id` = " . $fileId . ";");
            }

            if (parent::getUserProperty('TextFiles.showFilter', 'true') == 'true') {
                $searchForm = $this->getTextFileSearchForm() . '<hr />';
            }

            $n = 1;
            $returnTmp = "";
            $files = $dbObject->fetchAll("SELECT `id`, `name`, `content`, `type` FROM `page_file` WHERE `wp` = " . $projectId . " " . $this->getTextFileSearchPartSql() . " ORDER BY `id`;");
            if (count($files) != 0) {
                $returnTmp .= ''
                        . '<table class="page-file-list data-table clickable standart">'
                        . '<thead>'
                        . '<tr class="file-tr">'
                        . '<th class="tf-id">' . $rb->get('tf.id') . ':</th>'
                        . '<th class="tf-name">' . $rb->get('tf.name') . ':</th>'
                        . '<th class="tf-type">' . $rb->get('tf.type') . ':</th>'
                        . '<th class="tf-content">' . $rb->get('tf.content') . ':</th>'
                        . '<th class="tf-action"></th>'
                        . '</tr>'
                        . '</thead>'
                        . '<tbody>';
                foreach ($files as $file) {
                    $returnTmp .= ''
                    . '<tr class="file-tr ' . (($n % 2) ? 'idle' : 'even') . '">'
                        . '<td class="file-id">'
                            . $file['id']
                        . '</td>'
                        . '<td class="file-name">'
                            . $file['name']
                        . '</td>'
                        . '<td class="file-type">'
                            . $filesEx[$file['type']]
                        . '</td>'
                        . '<td class="file-content">'
                            . '<div class="file-content-in"><div class="foo">' . substr($file['content'], 0, 130) . '</div></div>'
                        . '</td>'
                        . '<td>'
                            . (($editable) ? ''
                                    . '<form name="process-file1" method="post" action="' . $_SERVER['REQUEST_URI'] . '">'
                                    . '<input type="hidden" name="file-id" value="' . $file['id'] . '" />'
                                    . '<input type="hidden" name="edit-file" value="' . $rb->get('tf.edit') . '" />'
                                    . '<input type="image" src="~/images/page_edi.png" name="edit-file" value="' . $rb->get('tf.edit') . '" title="' . $rb->get('tf.edittitle') . ', id=' . $file['id'] . '" /> '
                                    . '</form>'
                                    . '<form name="process-file2" method="post" action="' . $_SERVER['REQUEST_URI'] . '">'
                                    . '<input type="hidden" name="file-id" value="' . $file['id'] . '" />'
                                    . '<input type="hidden" name="delete-file" value="' . $rb->get('tf.delete') . '" />'
                                    . '<input class="confirm" type="image" src="~/images/page_del.png" name="delete-file" value="' . $rb->get('tf.delete') . '" title="' . $rb->get('tf.deletetitle') . ', id(' . $file['id'] . ')" />'
                                    . '</form>' : '')
                        . '</td>'
                    . '</tr>';
                    $n++;
                }
                $returnTmp .= '</tbody></table>';
            } else {
                $returnTmp .= parent::getWarning($rb->get('tf.emptylist'));
            }

            if ($_SESSION['selected-project'] != '') {
                $returnTmp .= ''
                        . '<hr />'
                        . '<div class="gray-box">'
                        . '<form name="add-file" method="post" action="' . $_SERVER['REQUEST_URI'] . '">'
                        . '<input type="submit" name="add-file" value="' . $rb->get('tf.new') . '" title="' . $rb->get('tf.newtitle') . '" />'
                        . '</form>'
                        . '</div>';
            }

            
            $returnTmp = $searchForm . $returnTmp;

            $return .= parent::getFrame($rb->get('tf.title'), $returnTmp, '');
            return $return;
        }

        /**
         *
         * 	Generates search form for text files
         *
         */
        private function getTextFileSearchForm() {
            $rb = $this->rb();

            if ($_POST['text-file-search-submit'] == $rb->get('tf.search')) {
                parent::session()->set('name', $_POST['text-file-search-name'], 'tf-search');
                parent::session()->set('content', $_POST['text-file-search-content'], 'tf-search');
                parent::session()->set('type', $_POST['text-file-search-type'], 'tf-search');
            } elseif ($_POST['text-file-search-clear'] == $rb->get('tf.clear')) {
                parent::session()->clear('tf-search');
            }

            $return = ''
            . '<form name="text-file-search" method="post" action="' . $_SERVER['REQUEST_URI'] . '">'
                . '<div class="gray-box">'
                    . '<label class="w80" for="text-file-search-name">' . $rb->get('tf.name') . ':</label>'
                    . '<input class="w300" nametype="text" name="text-file-search-name" id="text-file-search-name" value="' . parent::session()->get('name', 'tf-search') . '" />'
                . '</div>'
                . '<div class="gray-box">'
                    . '<label class="w80" for="text-file-search-content">' . $rb->get('tf.content') . ':</label>'
                    . '<input class="w500" type="text" name="text-file-search-content" id="text-file-search-content" value="' . parent::session()->get('content', 'tf-search') . '" />'
                . '</div>'
                . '<div class="gray-box">'
                    . '<label class="w80" for="text-file-search-type">' . $rb->get('tf.type') . ':</label>'
                    . '<select name="text-file-search-type" id="text-file-search-type">'
                        . '<option value=""' . (parent::session()->get('type', 'tf-search') == "" ? " selected='selected'" : "") . '>---</option>'
                        . '<option value="' . WEB_TYPE_CSS . '"' . (parent::session()->get('type', 'tf-search') == WEB_TYPE_CSS ? " selected='selected'" : "") . '>CSS</option>'
                        . '<option value="' . WEB_TYPE_JS . '"' . (parent::session()->get('type', 'tf-search') == WEB_TYPE_JS ? " selected='selected'" : "") . '>JS</option>'
                    . '</select>'
                . '</div>'
                . '<div class="gray-box">'
                    . '<input type="submit" name="text-file-search-submit" value="' . $rb->get('tf.search') . '" /> '
                    . '<input type="submit" name="text-file-search-clear" value="' . $rb->get('tf.clear') . '" /> '
                . '</div>'
            . '</form>';

            return $return;
        }

        /**
         *
         * 	Generates part sql for text file search form
         *
         */
        private function getTextFileSearchPartSql() {
            $return = '';
            $name = parent::session()->get('name', 'tf-search');
            $content = parent::session()->get('content', 'tf-search');
            $type = parent::session()->get('type', 'tf-search');
            if (strlen($name) != 0) {
                if ($return != '') {
                    $return .= 'and ';
                }
                $return .= '`name` like "%' . $name . '%"';
            }
            if (strlen($content) != 0) {
                if ($return != '') {
                    $return .= 'and ';
                }
                $return .= '`content` like "%' . $content . '%"';
            }
            if (!empty($type)) {
                if ($return != '') {
                    $return .= 'and ';
                }
                $return .= '`type` = ' . parent::dataAccess()->escape($type);
            }

            if (!empty($return))
                return 'and ' . $return;

            return $return;
        }

        /**
         *
         *  Generates form for updating text files.
         *
         */
        private function getFileUpdateForm($fileId, $fileName, $fileContent, $browsers, $fileTypes, $placementOptions) {
            $rb = $this->rb();
            $htmlBrowsers = '';
            foreach ($browsers as $browser => $value) {
                $htmlBrowsers .= ''
                . '<div class="text-file-browser">'
                    . '<label for="browser-' . strtolower($browser) . '">' . $browser . '</label> '
                    . '<input type="checkbox" name="browser-' . strtolower($browser) . '" id="browser-' . strtolower($browser) . '"' . (($value == 1) ? ' checked="checked"' : '') . ' />'
                . '</div>';
            }

            $returnTmp = ''
            . '<form name="edit-file" method="post" action="' . $_SERVER['REQUEST_URI'] . '">'
                . '<div class="edit-file-name">'
                    . '<div class="text-file-prop">'
                        . '<div class="text-file-name gray-box">'
                            . '<label for="file-name">' . $rb->get('tf.name') . ':</label> '
                            . '<input type="text" name="file-name" value="' . $fileName . '" class="w300" /> '
                        . '</div>'
                        . '<div class="text-file-type">'
                            . '<label for="file-type">' . $rb->get('tf.type') . ':</label> '
                            . '<select name="file-type"> '
                                . $fileTypes
                            . '</select> '
                        . '</div>'
                        . '<div class="text-file-type">'
                            . '<label for="file-placement">' . $rb->get('tf.placement') . ':</label> '
                            . '<select name="file-placement"> '
                                . $placementOptions
                            . '</select> '
                        . '</div>'
                        . '<div class="text-file-browsers">'
                            . $htmlBrowsers
                        . '</div>'
                        . '<div class="clear"></div>'
                    . '</div>'
                    . '<div class="text-file-content">';

            $name = 'Page.editors';
            $propertyEditors = parent::system()->getPropertyValue($name);
            $editAreaTextFileRows = parent::system()->getPropertyValue('Page.editAreaTextFileRows');

            if ($propertyEditors == "edit_area") {
                $returnTmp .= ''
                        . '<div id="editors" class="editors edit-area-editors">'
                        . '<div id="cover-page-file-content">'
                        . '<label for="file-content">' . $rb->get('tf.name') . ':</label>'
                        . '<textarea id="file-content" class="edit-area html" name="file-content" rows="' . ($editAreaTextFileRows > 0 ? $editAreaTextFileRows : 30) . '" wrap="off">' . str_replace('~', '&#126', $fileContent) . '</textarea>'
                        . '</div>'
                        . '</div>';
            } else {
                $returnTmp .= ''
                        . '<label for="file-content">' . $rb->get('tf.name') . ':</label> '
                        . '<div class="editor-cover">'
                        . '<div class="textarea-cover">'
                        . '<textarea name="file-content" class="editor-textarea" rows="15" wrap="off">' . str_replace('~', '&#126', $fileContent) . '</textarea> '
                        . '</div>'
                        . '<div class="clear"></div>'
                        . '</div>';
            }
            $returnTmp .= ''
                    . '</div>'
                    . '<div class="text-file-submit">'
                    . (($fileId != -1) ? '<input type="hidden" name="file-id" value="' . $fileId . '" />' : '')
                    . '<input type="submit" name="save" value="' . $rb->get('tf.save') . '" title="' . $rb->get('tf.savetitle') . '" /> '
                    . '<input type="submit" name="save" value="' . $rb->get('tf.saveandclose') . '" title="' . $rb->get('tf.saveandclosetitle') . '" /> '
                    . '<input type="submit" name="close" value="' . $rb->get('tf.close') . '" title="' . $rb->get('tf.closetitle') . '" /> '
                    . '</div>'
                    . '</div>'
                    . '</form>';

            return parent::getFrame($rb->get('tf.editframetitle') . ' :: ' . ((strlen($fileName) != 0) ? $fileName . ' ( ' . $fileId . ' )' : $rb->get('tf.new')), $returnTmp, '');
        }

        /**
         *
         * 	Clears url cache.
         * 	C tag.     
         * 	
         * 	@return		form		      
         *
         */
        public function clearUrlCache() {
            global $dbObject;
            $return = $msg = '';

            if ($_POST['clear-url-cache'] == "Do \'Clear Url Cache\'") {
                $dbObject->execute("TRUNCATE TABLE `urlcache`");
                $msg = '<h4 class="success">Url cache cleared!</h4>';
            }

            $returnForm = ''
                    . ((strlen($msg) > 0) ? $msg : '' )
                    . '<div class="clear-url-cache">'
                    . '<form name="clear-url-cache" method="post" action="' . $_SERVER['REQUEST_URI'] . '">'
                    . '<input type="submit" name="clear-url-cache" value="Do \'Clear Url Cache\'" />'
                    . '</form>'
                    . '</div>';

            return $return . parent::getFrame("Clear Url Cache", $returnForm, "", true);
        }

        /**
         *
         * 	Edit & clear url cache.
         * 	C tag.     
         * 	
         * 	@return		form		      
         *
         */
        public function manageUrlCache() {
            global $dbObject;
            $return = $msg = '';
            $projectId = 0;
            $pageId = '';
            $partOfUrl = '';
            $urlCache = array();
            $urlCacheReturn = '';
            $sent = false;

            if ($_POST['clear-url-cache'] == "Do 'Clear Url Cache'") {
                $dbObject->execute("TRUNCATE TABLE `urlcache`");
                $msg = '<h4 class="success">Url cache cleared!</h4>';
            }

            if ($_POST['delete-from-url-cache'] == 'Delete selected') {
                $delete = $_POST['url-cache-delete-checkbox'];
                foreach ($delete as $del) {
                    $dbObject->execute('DELETE FROM `urlcache` WHERE `id` = ' . $del . ';');
                }
                $msg = '<h4 class="success">Selected items have been deleted!</h4>';
                $_POST['show-url-cache'] = 'Search';
                $sent = true;
            }

            if ($_POST['show-url-cache'] == 'Search') {
                $pageId = $_POST['page-id-url-cache'];
                $projectId = $_POST['project-id-url-cache'];
                $partOfUrl = $_POST['part-of-url-url-cache'];
                if (strlen($pageId) != 0 && strlen($partOfUrl) != 0 && $projectId != 0) {
                    $urlCache = $dbObject->fetchAll('SELECT `urlcache`.`id`, `urlcache`.`url`, `urlcache`.`pages_id`, `language`.`language`, `language_id` as `lang-id`, `urlcache`.`cachetime`, `urlcache`.`lastcache`, `web_project`.`name` FROM `urlcache` LEFT JOIN `language` ON `urlcache`.`language_id` = `language`.`id` LEFT JOIN `web_project` ON `urlcache`.`project_id` = `web_project`.`id` WHERE (`urlcache`.`pages_id` LIKE "' . $pageId . '-%" OR `urlcache`.`pages_id` LIKE "%-' . $pageId . '-%" OR `urlcache`.`pages_id` LIKE "%-' . $pageId . '") AND (`urlcache`.`url` LIKE "%' . $partOfUrl . '%") AND `web_project`.`id` = ' . $projectId . ' ORDER BY `urlcache`.`id`;');
                } elseif (strlen($pageId) != 0 && strlen($partOfUrl) != 0) {
                    $urlCache = $dbObject->fetchAll('SELECT `urlcache`.`id`, `urlcache`.`url`, `urlcache`.`pages_id`, `language`.`language`, `language_id` as `lang-id`, `urlcache`.`cachetime`, `urlcache`.`lastcache`, `web_project`.`name` FROM `urlcache` LEFT JOIN `language` ON `urlcache`.`language_id` = `language`.`id` LEFT JOIN `web_project` ON `urlcache`.`project_id` = `web_project`.`id` WHERE (`urlcache`.`pages_id` LIKE "' . $pageId . '-%" OR `urlcache`.`pages_id` LIKE "%-' . $pageId . '-%" OR `urlcache`.`pages_id` LIKE "%-' . $pageId . '") AND (`urlcache`.`url` LIKE "%' . $partOfUrl . '%") ORDER BY `urlcache`.`id`;');
                } elseif (strlen($partOfUrl) != 0 && $projectId != 0) {
                    $urlCache = $dbObject->fetchAll('SELECT `urlcache`.`id`, `urlcache`.`url`, `urlcache`.`pages_id`, `language`.`language`, `language_id` as `lang-id`, `urlcache`.`cachetime`, `urlcache`.`lastcache`, `web_project`.`name` FROM `urlcache` LEFT JOIN `language` ON `urlcache`.`language_id` = `language`.`id` LEFT JOIN `web_project` ON `urlcache`.`project_id` = `web_project`.`id` WHERE (`urlcache`.`url` LIKE "%' . $partOfUrl . '%") AND `web_project`.`id` = ' . $projectId . ' ORDER BY `urlcache`.`id`;');
                } elseif (strlen($partOfUrl) != 0) {
                    $urlCache = $dbObject->fetchAll('SELECT `urlcache`.`id`, `urlcache`.`url`, `urlcache`.`pages_id`, `language`.`language`, `language_id` as `lang-id`, `urlcache`.`cachetime`, `urlcache`.`lastcache`, `web_project`.`name` FROM `urlcache` LEFT JOIN `language` ON `urlcache`.`language_id` = `language`.`id` LEFT JOIN `web_project` ON `urlcache`.`project_id` = `web_project`.`id` WHERE (`urlcache`.`url` LIKE "%' . $partOfUrl . '%") ORDER BY `urlcache`.`id`;');
                } elseif (strlen($pageId) != 0) {
                    $urlCache = $dbObject->fetchAll('SELECT `urlcache`.`id`, `urlcache`.`url`, `urlcache`.`pages_id`, `language`.`language`, `language_id` as `lang-id`, `urlcache`.`cachetime`, `urlcache`.`lastcache`, `web_project`.`name` FROM `urlcache` LEFT JOIN `language` ON `urlcache`.`language_id` = `language`.`id` LEFT JOIN `web_project` ON `urlcache`.`project_id` = `web_project`.`id` WHERE (`urlcache`.`pages_id` LIKE "' . $pageId . '-%" OR `urlcache`.`pages_id` LIKE "%-' . $pageId . '-%" OR `urlcache`.`pages_id` LIKE "%-' . $pageId . '") ORDER BY `urlcache`.`id`;');
                } elseif ($projectId != 0) {
                    $urlCache = $dbObject->fetchAll('SELECT `urlcache`.`id`, `urlcache`.`url`, `urlcache`.`pages_id`, `language`.`language`, `language_id` as `lang-id`, `urlcache`.`cachetime`, `urlcache`.`lastcache`, `web_project`.`name` FROM `urlcache` LEFT JOIN `language` ON `urlcache`.`language_id` = `language`.`id` LEFT JOIN `web_project` ON `urlcache`.`project_id` = `web_project`.`id` WHERE `web_project`.`id` = ' . $projectId . ' ORDER BY `urlcache`.`id`;');
                } else {
                    $urlCache = $dbObject->fetchAll('SELECT `urlcache`.`id`, `urlcache`.`url`, `urlcache`.`pages_id`, `language`.`language`, `language_id` as `lang-id`, `urlcache`.`cachetime`, `urlcache`.`lastcache`, `web_project`.`name` FROM `urlcache` LEFT JOIN `language` ON `urlcache`.`language_id` = `language`.`id` LEFT JOIN `web_project` ON `urlcache`.`project_id` = `web_project`.`id` ORDER BY `urlcache`.`id`;');
                }

                if (count($urlCache) > 0) {
                    $urlCacheReturn .= ''
                            . '<hr />'
                            . '<table class="standart data-table standart clickable">'
                            . '<thead>'
                            . '<tr>'
                            . '<th class="url-cache-id">Id:</th>'
                            . '<th class="url-cache-link">Link:</th>'
                            . '<th class="url-cache-name">Name:</th>'
                            . '<th class="url-cache-url">Url:</th>'
                            . '<th class="url-cache-page">Page Ids:</th>'
                            . '<th class="url-cache-lang">Lang:</th>'
                            . '<th class="url-cache-cachetime">Cachetime:</th>'
                            . '<th class="url-cache-lastcache">Lastcache:</th>'
                            . '<th class="url-cache-edit">Edit:</th>'
                            . '</tr>'
                            . '</thead>'
                            . '<tbody>';

                    $i = 1;
                    foreach ($urlCache as $url) {
                        $cacheTime = '';
                        if ($url['cachetime'] == -1) {
                            $cacheTime = 'Don\'t use';
                        } elseif ($url['cachetime'] == 60) {
                            $cacheTime = '1 minute';
                        } elseif ($url['cachetime'] == 3600) {
                            $cacheTime = '1 hour';
                        } elseif ($url['cachetime'] == 86400) {
                            $cacheTime = '1 day';
                        } elseif ($url['cachetime'] == 172800) { 
                            $cacheTime = '2 days';
                        } elseif ($url['cachetime'] == 259200) { 
                            $cacheTime = '3 days';
                        } elseif ($url['cachetime'] == 604800) { 
                            $cacheTime = '1 week';
                        } elseif ($url['cachetime'] == 0) {
                            $cacheTime = 'Unlimited';
                        }

                        if ($_ENV["IS4WFW_PORT"]) {
                            $urlFix = explode("/", $url['url'], 2);
                            $url['url'] = $urlFix[0] . ":" . $_ENV["IS4WFW_PORT"] . "/" . $urlFix[1];
                        }

                        $urlCacheReturn .= ''
                        . '<tr class="' . ((($i % 2) == 0) ? 'even' : 'idle') . '">'
                            . '<td class="url-cache-id"><label for="url-cache-delete-checkbox-' . $url['id'] . '">' . $url['id'] . '</label></td>'
                            . '<td class="url-cache-id"><a target="_blank" href="http://' . $url['url'] . '">view</a></td>'
                            . '<td class="url-cache-name"><label for="url-cache-delete-checkbox-' . $url['id'] . '">' . $url['name'] . '</label></td>'
                            . '<td class="url-cache-wp-url"><label for="url-cache-delete-checkbox-' . $url['id'] . '">' . $url['url'] . '</label></td>'
                            . '<td class="url-cache-page"><label for="url-cache-delete-checkbox-' . $url['id'] . '" langid="' . $url['lang-id'] . '">' . $url['pages_id'] . '</label></td>'
                            . '<td class="url-cache-lang"><label for="url-cache-delete-checkbox-' . $url['id'] . '">' . $url['language'] . ' (' . $url['lang-id'] . ')</label></td>'
                            . '<td class="url-cache-cachetime"><label for="url-cache-delete-checkbox-' . $url['id'] . '">' . $cacheTime . '</label></td>'
                            . '<td class="url-cache-lastcache"><label for="url-cache-delete-checkbox-' . $url['id'] . '">' . (($url['lastcache'] > 0) ? date('H:i:s d.m.Y', $url['lastcache']) : '-') . '</label></td>'
                            . '<td class="url-cache-edit">'
                                . '<input id="url-cache-delete-checkbox-' . $url['id'] . '" type="checkbox" name="url-cache-delete-checkbox[]" value="' . $url['id'] . '" />'
                            . '</td>'
                        . '</tr>';
                        
                        $i++;
                    }

                    $urlCacheReturn .= '</tbody></table>';
                }
                $sent = true;
            }

            $projectsDb = $dbObject->fetchAll('SELECT `id`, `name` FROM `web_project` ORDER BY `name`;');
            $projects = '';
            foreach ($projectsDb as $prj) {
                $projects .= '<option value="' . $prj['id'] . '"' . (($projectId == $prj['id']) ? 'selected="selected"' : '') . '>' . $prj['name'] . '</option>';
            }

            $returnForm = ''
                    . ((strlen($msg) > 0) ? $msg : '' )
                    . '<div id="clear-url-cache" class="clear-url-cache">'
                    . '<form name="clear-url-cache" method="post" action="' . $_SERVER['REQUEST_URI'] . '">'
                    . '<div class="part-of-url">'
                    . '<label for="part-of-url-url-cache">Part of url:</label> '
                    . '<input id="part-of-url-url-cache" type="text" name="part-of-url-url-cache" value="' . $partOfUrl . '" />'
                    . '</div>'
                    . '<div class="project-id">'
                    . '<label for="project-id-url-cache">Project:</label> '
                    . '<select id="project-id-url-cache" type="text" name="project-id-url-cache">'
                    . '<option value="0">All</option>'
                    . $projects
                    . '</select>'
                    . '</div>'
                    . '<div class="page-id">'
                    . '<label for="page-id-url-cache">Page id:</label> '
                    . '<input id="page-id-url-cache" type="text" name="page-id-url-cache" value="' . $pageId . '" />'
                    . '</div>'
                    . '<div class="clear"></div>'
                    . '<div id="clear-url-cache-submit" class="submit">'
                    . '<input type="submit" name="show-url-cache" value="Search" />'
                    . ((strlen($urlCacheReturn) > 0) ? ''
                            . '<input class="confirm" type="submit" name="delete-from-url-cache" value="Delete selected" title="Delete selected items from urlcache" />' : '')
                    . '<input class="confirm" type="submit" name="clear-url-cache" value="Do \'Clear Url Cache\'" title="Clear whole url cache" />'
                    . (($sent) ? ''
                            . '<input type="submit" name="cancel-url-cache" value="Cancel" />' : '')
                    . '</div>'
                    . $urlCacheReturn
                    . '</form>'
                    . '</div>';

            return $return . parent::getFrame("Manage Url Cache", $returnForm, "", true);
        }

        /**
         *
         * 	Setups keywords of whole web app.
         * 	C tag.
         *
         * 	return form
         *
         */
        public function updateKeywords() {
            $rb = $this->rb();

            $return = $msg = '';

            if ($_POST['save-keywords'] == $rb->get('keywords.save')) {
                file_put_contents("keywords.txt", $_POST['keywords']);
                $msg = '<h4 class="success">' . $rb->get('keywords.saved') . '</h4>';
            }

            $keywords = file_get_contents("keywords.txt");
            $returnForm = ''
                . ((strlen($msg) > 0) ? $msg : '' )
                . '<form name="update-keywords" method="post" action="' . $_SERVER['REQUEST_URI'] . '">'
                    . '<div class="gray-box">'
                        . '<label for="keywords">' . $rb->get('keywords.note') . '</label> '
                        . '<input type="text" name="keywords" value="' . $keywords . '" class="wmax" /> '
                    . '</div>'
                        . '<div class="gray-box">'
                        . '<input type="submit" name="save-keywords" value="' . $rb->get('keywords.save') . '" >'
                    . '</div>'
                . '</form>';

            return parent::getFrame($rb->get('keywords.title'), $returnForm, "", true);
        }

        /**
         *
         * 	Setups robots.txt of whole web app.
         * 	C tag.
         *
         * 	return form
         *
         */
        public function updateRobots() {
            $return = $msg = '';

            if ($_POST['save-robots'] == "Save") {
                file_put_contents("robots.txt", $_POST['robots']);
                $msg = parent::getSuccess('Robots.txt file saved!');
            }

            $robots = file_get_contents("robots.txt");
            $returnForm = ''
                    . ((strlen($msg) > 0) ? $msg : '' )
                    . '<div id="editors" class="update-robots editors">'
                    . '<form class="update-robots" name="update-robots" method="post" action="' . $_SERVER['REQUEST_URI'] . '">'
                    . '<div class="gray-box">'
                    . '<label for="robots">Robots.txt:</label> '
                    . '<textarea class="edit-area html" id="robots" name="robots" rows="20">' . $robots . '</textarea>'
                    . '</div>'
                    . '<div class="gray-box font-right">'
                    . '<input type="submit" name="save-robots" value="Save" >'
                    . '</div>'
                    . '</form>'
                    . '</div>';

            return parent::getFrame("Manage robots.txt", $returnForm, "", true);
        }

        /**
         *
         * 	Show languages.
         * 	C tag.		 
         * 	
         * 	@param		editable		if true, user can delete lang versions
         * 	@return		form		 		 		 
         *
         */
        public function showLanguages($editable) {
            global $dbObject;
            $return = $returnForm = $returnNewForm = $msg = $msgNew = '';

            if ($editable && $_POST['delete-language'] == 'Delete language') {
                $langId = $_POST['language-id'];

                if (count($dbObject->fetchAll('SELECT `page_id` FROM `info` WHERE `language_id` = ' . $langId . ';')) == 0) {
                    $dbObject->execute("DELETE FROM `language` WHERE `id` = " . $langId . ";");
                    $msg = '<h4 class="success">Language deleted!</h4>';
                }
            }

            if ($editable && $_POST['add-new-language'] == "Add") {
                $name = $_POST['langauge-name'];

                if (count($dbObject->fetchAll('SELECT `id` FROM `language` WHERE `language` = "' . $name . '";')) == 0) {
                    $max = $dbObject->fetchAll('SELECT MAX(`id`) as `id` FROM `language`;');
                    $max = $max[0]['id'] + 1;
                    $dbObject->execute('INSERT INTO `language` (`id`, `language`) VALUES (' . $max . ', "' . $name . '");');
                    $msgNew = '<h4 class="success">Language added!</h4>';
                } else {
                    $msgNew = '<h4 class="error">Language with this name already exists!</h4>';
                }
            }

            $langs = $dbObject->fetchAll('SELECT `id`, `language` FROM `language` ORDER BY `id`;');
            $returnForm = ''
                    . ((strlen($msg) > 0) ? $msg : '' )
                    . parent::getWarning('Language version can deleted only when doesn\'t at least one page that uses it!')
                    . '<table class="languages-edit standart">'
                    . '<tr>'
                    . '<th>Id</th>'
                    . '<th>Name</th>'
                    . '<th>Edit</th>'
                    . '</tr>';
            $i = 1;
            foreach ($langs as $lang) {
                $returnForm .= ''
                        . '<tr class="' . ((($i % 2) == 0) ? 'even' : 'idle') . '">'
                        . '<td class="langs-id">' . $lang['id'] . '</td>'
                        . '<td class="langs-language">' . $lang['language'] . '</td>'
                        . (($editable && (count($dbObject->fetchAll('SELECT `page_id` FROM `info` WHERE `language_id` = ' . $lang['id'] . ';')) == 0)) ? ''
                                . '<td class="langs-delete">'
                                . '<form name="delete-lang" method="post" action="' . $_SERVER['REQUEST_URI'] . '">'
                                . '<input type="hidden" name="language-id" value="' . $lang['id'] . '" />'
                                . '<input type="hidden" name="delete-language" value="Delete language" />'
                                . '<input class="confirm" type="image" src="~/images/page_del.png" name="delete-language" value="Delete language" title="Delete language, id(' . $lang['id'] . ')" />'
                                . '</form>'
                                . '</td>' : '<td></td>' )
                        . '</tr>';
                $i++;
            }
            $returnForm .= '</table>';

            if ($editable) {
                $returnNewForm = ''
                        . ((strlen($msgNew) > 0) ? $msgNew : '' )
                        . '<div class="add-new-language">'
                        . '<form name="add-new-language" method="post" action="' . $_SERVER['REQUEST_URI'] . '">'
                        . '<label for="langauge-name">Type new language name:</label> '
                        . '<input type="text" name="langauge-name" /> '
                        . '<input type="submit" name="add-new-language" value="Add" />'
                        . '</form>'
                        . '</div>';
            }

            return $return . parent::getFrame('Languages', $returnForm, '', true) . parent::getFrame('Add Language', $returnNewForm, '', true);
        }

        /**
         *
         * 	Show list of templates
         * 	C tag
         *
         * 	@param	detailPageId				page id with edit template form
         * 	@param	useFrames						use frames in output
         * 	@param	showError						show errors in output
         * 	@return	list of templates
         *
         */
        public function showTemplates($detailPageId = false, $useFrames = false, $showError = false) {
            global $webObject;
            global $loginObject;
            global $dbObject;
            $return = '';
            $actionUrl = $_SERVER['REQUEST_URI'];
            if ($detailPageId != false) {
                $actionUrl = $webObject->composeUrl($detailPageId);
            }

            if ($_POST['template-delete'] == "Delete") {
                $templateId = $_POST['template-id'];

                $rights = $dbObject->fetchAll('SELECT `value` FROM `template` LEFT JOIN `template_right` ON `template`.`id` = `template_right`.`tid` LEFT JOIN `group` ON `template_right`.`gid` = `group`.`gid` WHERE `template_right`.`tid` = ' . $templateId . ' AND `template_right`.`type` = ' . WEB_R_DELETE . ' AND (`group`.`gid` IN (' . $loginObject->getGroupsIdsAsString() . ') OR `group`.`parent_gid` IN (' . $loginObject->getGroupsIdsAsString() . '));');
                if (count($rights) > 0 && $templateId > 0) {
                    $dbObject->execute('DELETE FROM `template_right` WHERE `tid` = ' . $templateId . ';');
                    $dbObject->execute('DELETE FROM `template` WHERE `id` = ' . $templateId . ';');
                    if ($showError != 'false') {
                        $return .= parent::getSuccess('Template deleted!');
                    }
                } else {
                    if ($showError != 'false') {
                        $return .= parent::getError('Permission Denied!');
                    }
                }
            }

            if ($_POST['template-search-submit'] == 'Search') {
                $name = $_POST['template-search-name'];
                $content = $_POST['template-search-content'];
                $identifier = $_POST['template-search-identifier'];
                $group = $_POST['template-search-group'];

                parent::session()->set('name', $name, 'template-search');
                parent::session()->set('content', $content, 'template-search');
                parent::session()->set('identifier', $identifier, 'template-search');
                parent::session()->set('group', $group, 'template-search');
            } elseif ($_POST['template-search-clear'] == 'Clear') {
                parent::session()->delete('name', 'template-search');
                parent::session()->delete('content', 'template-search');
                parent::session()->delete('identifier', 'template-search');
                parent::session()->delete('group', 'template-search');
            }

            if (parent::getUserProperty('Templates.showFilter', 'true') == 'true') {
                $return .= ''
                . '<form name="template-search" method="post" action="' . UrlUtils::removeQueryString($_SERVER['REQUEST_URI']) . '">'
                    . '<div class="gray-box">'
                        . '<label for="template-search-name" class="w100">Name:</label>'
                        . '<input class="w300" type="text" name="template-search-name" id="template-search-name" value="' . parent::session()->get('name', 'template-search') . '" />'
                    . '</div>'
                    . '<div class="gray-box">'
                        . '<label for="template-search-group" class="w100">Group:</label>'
                        . '<input class="w300" type="text" name="template-search-group" id="template-search-group" value="' . parent::session()->get('group', 'template-search') . '" />'
                    . '</div>'
                    . '<div class="gray-box">'
                        . '<label for="template-search-identifier" class="w100">Identifier:</label>'
                        . '<input class="w300" type="text" name="template-search-identifier" id="template-search-identifier" value="' . parent::session()->get('identifier', 'template-search') . '" />'
                    . '</div>'
                    . '<div class="gray-box">'
                        . '<label for="template-search-content" class="w100">Content:</label>'
                        . '<input class="w500" type="text" name="template-search-content" id="template-search-content" value="' . parent::session()->get('content', 'template-search') . '" />'
                    . '</div>'
                    . '<div class="gray-box">'
                        . '<input type="submit" name="template-search-submit" value="Search" /> '
                        . '<input type="submit" name="template-search-clear" value="Clear" />'
                    . '</div>'
                . '</form>'
                . '<hr />';
            }

            // Vyber templatu do kterych smim zapisovat
            $searchPara = '';
            if (parent::session()->exists('name', 'template-search') && parent::session()->get('name', 'template-search') != "") {
                $searchPara .= ' and `template`.`name` like "%' . parent::session()->get('name', 'template-search') . '%"';
            }
            if (parent::session()->exists('group', 'template-search') && parent::session()->get('group', 'template-search') != "") {
                $searchPara .= ' and `template`.`group` like "%' . parent::session()->get('group', 'template-search') . '%"';
            }
            if (parent::session()->exists('identifier', 'template-search') && parent::session()->get('identifier', 'template-search') != "") {
                $searchPara .= ' and `template`.`identifier` like "%' . parent::session()->get('identifier', 'template-search') . '%"';
            }
            if (parent::session()->exists('content', 'template-search') && parent::session()->get('content', 'template-search') != "") {
                $searchPara .= ' and `template`.`content` like "%' . parent::session()->get('content', 'template-search') . '%"';
            }
            $templates = $dbObject->fetchAll('SELECT `template`.`id`, `template`.`name`, `template`.`group`, `template`.`identifier`, `template`.`content` FROM `template` LEFT JOIN `template_right` ON `template`.`id` = `template_right`.`tid` LEFT JOIN `group` ON `template_right`.`gid` = `group`.`gid` WHERE `template_right`.`type` = ' . WEB_R_WRITE . ' AND (`group`.`gid` IN (' . $loginObject->getGroupsIdsAsString() . ') OR `group`.`parent_gid` IN (' . $loginObject->getGroupsIdsAsString() . '))' . $searchPara . ' ORDER BY `template`.`id`;');

            if (count($templates) > 0) {
                $return .= ''
                . '<table class="template-list data-table standart clickable">'
                    . '<thead>'
                        . '<tr>'
                            . '<th class="template-id">Id:</th>'
                            . '<th class="template-name w160">Name:</th>'
                            . '<th class="template-name w80">Group:</th>'
                            . '<th class="template-identifier w120">Identifier:</th>'
                            . '<th class="template-content">Content:</th>'
                            . '<th class="template-edit">Edit:</th>'
                        . '</tr>'
                    . '</thead>'
                    . '<tbody>';

                $i = 1;
                foreach ($templates as $template) {
                    //$template['content'] = str_replace('&amp;web:page', '&web:page', $template['content']);
                    $template['content'] = str_replace('~', '&#126', $template['content']);
                    $template['content'] = str_replace("&", "&amp;", $template['content']);
                    $template['content'] = str_replace(">", "&gt;", $template['content']);
                    $template['content'] = str_replace("<", "&lt;", $template['content']);
                    $return .= ''
                    . '<tr class="' . ((($i % 2) == 0) ? 'even' : 'idle') . '">'
                        . '<td class="template-id">' . $template['id'] . '</td>'
                        . '<td class="template-name">' . $template['name'] . '</td>'
                        . '<td class="template-group">' . $template['group'] . '</td>'
                        . '<td class="template-identifier">' . $template['identifier'] . '</td>'
                        . '<td class="template-content" style="overflow:hidden;">'
                            . '<div class="file-content-in"><div class="foo">' . substr($template['content'], 0, 130) . '</div></div>'
                        . '</td>'
                        . '<td class="template-edit">'
                            . '<a href="' . UrlUtils::addParameter($actionUrl, "id", $template['id']) . '" class="image-button button-edit">'
                                . '<img src="~/images/page_edi.png" title="Edit template" />'
                            . '</a> '
                            . '<form name="template-edit2" method="post" action="' . $_SERVER['REQUEST_URI'] . '">'
                                . '<input type="hidden" name="template-id" value="' . $template['id'] . '" />'
                                . '<input type="hidden" name="template-delete" value="Delete" />'
                                . '<input class="confirm" type="image" src="~/images/page_del.png" name="template-delete" value="Delete" title="Delete template, id(' . $template['id'] . ')" />'
                            . '</form>'
                        . '</td>'
                    . '</tr>';
                    $i++;
                }
                $return .= ''
                        . '</tbody>'
                        . '</table>';
            } else {
                if ($showError != 'false') {
                    $return .= parent::getWarning('No templates to show!');
                }
            }

            $newTemplate = $dbObject->fetchAll('SELECT `value` FROM `template_right` LEFT JOIN `group` ON `template_right`.`gid` = `group`.`gid` WHERE `template_right`.`tid` = 0 AND `template_right`.`type` = ' . WEB_R_WRITE . ' AND (`group`.`gid` IN (' . $loginObject->getGroupsIdsAsString() . ') OR `group`.`parent_gid` IN (' . $loginObject->getGroupsIdsAsString() . ')) ORDER BY `value` DESC;');
            if (count($newTemplate) > 0) {
                $return .= ''
                . '<hr />'
                . '<div class="gray-box">'
                    . '<a href="' . UrlUtils::addParameter($actionUrl, "id", "new") . '" class="button">New Template</a>'
                . '</div>';
            }

            if ($useFrames != "false") {
                return parent::getFrame('Temlates list', $return, '');
            } else {
                return $return;
            }
        }

        /**
         *
         * 	Shows edit template form
         *
         * 	@param	submitPageId		page id submit form to
         * 	@param	useFrames				use frames in output
         * 	@param	showError				show error in output
         * 	@return	html form form editing template
         *
         */
        public function showEditTemplateForm($submitPageId = false, $useFrames = false, $showError = false, $id = "") {
            global $webObject;
            global $loginObject;
            global $dbObject;
            $return = '';
            $actionUrl = $_SERVER['REQUEST_URI'];
            if ($submitPageId != false) {
                $actionUrl = $webObject->composeUrl($submitPageId);
            }
            
            $closeUrl = UrlUtils::removeQueryString($_SERVER["REQUEST_URI"]);

            $entity = null;
            $hasError = false;
            if (array_key_exists('template-submit', $_POST)) {
                $templateId = $_POST['template-id'];
                $entity = [
                    "name" => $_POST['template-name'], 
                    "identifier" => $_POST['template-identifier'], 
                    "group" => $_POST['template-group'], 
                    "content" => str_replace('&#126;', '~', $_POST['template-content'])
                ];
                $templateR = $_POST['template-right-edit-groups-r'];
                $templateW = $_POST['template-right-edit-groups-w'];
                $templateD = $_POST['template-right-edit-groups-d'];

                if ($entity["identifier"] == "") {
                    $entity["identifier"] = null;
                }

                // test na prava
                $tempId = (($_POST['template-id'] != '') ? $_POST['template-id'] : 0);
                $rights = $dbObject->fetchAll('SELECT `value` FROM `template_right` LEFT JOIN `group` ON `template_right`.`gid` = `group`.`gid` WHERE `template_right`.`tid` = ' . $tempId . ' AND `template_right`.`type` = ' . WEB_R_WRITE . ' AND (`group`.`gid` IN (' . $loginObject->getGroupsIdsAsString() . ') OR `group`.`parent_gid` IN (' . $loginObject->getGroupsIdsAsString() . '));');
                if (count($rights) > 0) {
                    $template = $dbObject->fetchAll('SELECT `id` FROM `template` WHERE `id` = ' . $templateId . ';');
                    if (count($template) == 0) {
                        $sql = parent::sql()->insert("template", $entity);
                        try { 
                            $dbObject->execute($sql);
                            $templateId = $dbObject->getLastId();
                            $_POST['template-id'] = $templateId;
                            if ($showError != 'false') {
                                $return .= parent::getSuccess('Template added!');
                            }
                        } catch (DataAccessException $e) {
                            $return .= $this->getError("Error: " . $e->errorMessage . ".");
                            $hasError = true;
                        }
                    } else {
                        $sql = parent::sql()->update("template", $entity, ["id" => $templateId]);
                        try {
                            $dbObject->execute($sql);
                            if ($showError != 'false') {
                                $return .= parent::getSuccess('Template updated!');
                            }

                            $this->deleteParsedTemplate(TemplateCacheKeys::template($templateId));
                        } catch (DataAccessException $e) {
                            $return .= $this->getError("Error: " . $e->errorMessage . ".");
                            $hasError = true;
                        }
                    }

                    if (!$hasError) {
                        if (count($templateR) != 0) {
                            $dbR = $dbObject->fetchAll("SELECT `gid` FROM `template_right` WHERE `template_right`.`tid` = " . $templateId . " AND `type` = " . WEB_R_READ . ";");
                            foreach ($dbR as $right) {
                                if (!in_array($right['gid'], $templateR)) {
                                    $dbObject->execute("DELETE FROM `template_right` WHERE `tid` = " . $templateId . " AND `type` = " . WEB_R_READ . ";");
                                }
                            }
                            foreach ($templateR as $right) {
                                $row = $dbObject->fetchAll("SELECT `gid` FROM `template_right` WHERE `tid` = " . $templateId . " AND `type` = " . WEB_R_READ . " AND `gid` = " . $right . ";");
                                if (count($row) == 0) {
                                    $dbObject->execute("INSERT INTO `template_right`(`tid`, `gid`, `type`) VALUES (" . $templateId . ", " . $right . ", " . WEB_R_READ . ");");
                                }
                            }
                        }
                        if (count($templateW) != 0) {
                            $dbR = $dbObject->fetchAll("SELECT `gid` FROM `template_right` WHERE `template_right`.`tid` = " . $templateId . " AND `type` = " . WEB_R_WRITE . ";");
                            foreach ($dbR as $right) {
                                if (!in_array($right['gid'], $templateW)) {
                                    $dbObject->execute("DELETE FROM `template_right` WHERE `tid` = " . $templateId . " AND `type` = " . WEB_R_WRITE . ";");
                                }
                            }
                            foreach ($templateW as $right) {
                                $row = $dbObject->fetchAll("SELECT `gid` FROM `template_right` WHERE `tid` = " . $templateId . " AND `type` = " . WEB_R_WRITE . " AND `gid` = " . $right . ";");
                                if (count($row) == 0) {
                                    $dbObject->execute("INSERT INTO `template_right`(`tid`, `gid`, `type`) VALUES (" . $templateId . ", " . $right . ", " . WEB_R_WRITE . ");");
                                }
                            }
                        }
                        if (count($templateD) != 0) {
                            $dbR = $dbObject->fetchAll("SELECT `gid` FROM `template_right` WHERE `template_right`.`tid` = " . $templateId . " AND `type` = " . WEB_R_DELETE . ";");
                            foreach ($dbR as $right) {
                                if (!in_array($right['gid'], $templateD)) {
                                    $dbObject->execute("DELETE FROM `template_right` WHERE `tid` = " . $templateId . " AND `type` = " . WEB_R_DELETE . ";");
                                }
                            }
                            foreach ($templateD as $right) {
                                $row = $dbObject->fetchAll("SELECT `gid` FROM `template_right` WHERE `tid` = " . $templateId . " AND `type` = " . WEB_R_DELETE . " AND `gid` = " . $right . ";");
                                if (count($row) == 0) {
                                    $dbObject->execute("INSERT INTO `template_right`(`tid`, `gid`, `type`) VALUES (" . $templateId . ", " . $right . ", " . WEB_R_DELETE . ")");
                                }
                            }
                        }

                        if ($_POST['template-submit'] == "Save and close") {
                            $this->redirectToUrl($closeUrl);
                        } else if ($id == "new") {
                            $redirectUrl = UrlUtils::addParameter($closeUrl, "id", $templateId);
                            $this->redirectToUrl($redirectUrl);
                        }
                    }
                } else {
                    if ($showError != 'false') {
                        $return .= parent::getError('Permission Denied!');
                    }
                }
            }

            if ($_POST['template-edit'] != 'Edit' && $_POST["template-submit"] != "Save" && empty($id)) {
                return '';
            }

            // Pokud je v postu template-id, vyber template, testuj prava, pokud, testuj prava pro template-id 0
            if ($entity == null) {
                $templateId = ((array_key_exists('template-id', $_POST)) ? $_POST['template-id'] : ($id == "new" ? 0 : $id));
                $template = $dbObject->fetchAll('SELECT `template`.`name`, `identifier`, `group`, `content` FROM `template` LEFT JOIN `template_right` ON `template`.`id` = `template_right`.`tid` LEFT JOIN `group` ON `template_right`.`gid` = `group`.`gid` WHERE `template_right`.`tid` = ' . $templateId . ' AND `template_right`.`type` = ' . WEB_R_WRITE . ' AND (`group`.`gid` IN (' . $loginObject->getGroupsIdsAsString() . ') OR `group`.`parent_gid` IN (' . $loginObject->getGroupsIdsAsString() . ')) ORDER BY `value` DESC;');
            } else {
                $template = [0 => $entity];
            }
            if (count($template) > 0 || $templateId == 0) {
                $show = array('read' => true, 'write' => true, 'delete' => false);
                $groupsR = $dbObject->fetchAll("SELECT `gid` FROM `template_right` WHERE `tid` = " . $templateId . " AND `type` = " . WEB_R_READ . ";");
                $groupsW = $dbObject->fetchAll("SELECT `gid` FROM `template_right` WHERE `tid` = " . $templateId . " AND `type` = " . WEB_R_WRITE . ";");
                $groupsD = $dbObject->fetchAll("SELECT `gid` FROM `template_right` WHERE `tid` = " . $templateId . " AND `type` = " . WEB_R_DELETE . ";");
                $allGroups = $dbObject->fetchAll('SELECT `gid`, `name` FROM `group` WHERE (`group`.`gid` IN (' . $loginObject->getGroupsIdsAsString() . ') OR `group`.`parent_gid` IN (' . $loginObject->getGroupsIdsAsString() . ')) ORDER BY `value`;');
                $groupSelectR = '<select id="template-right-edit-groups-r" name="template-right-edit-groups-r[]" multiple="multiple" size="5">';
                $groupSelectW = '<select id="template-right-edit-groups-w" name="template-right-edit-groups-w[]" multiple="multiple" size="5">';
                $groupSelectD = '<select id="template-right-edit-groups-d" name="template-right-edit-groups-d[]" multiple="multiple" size="5">';
                foreach ($allGroups as $group) {
                    $selectedR = false;
                    $selectedW = false;
                    $selectedD = false;
                    foreach ($groupsR as $gp) {
                        if ($gp['gid'] == $group['gid']) {
                            $selectedR = true;
                            $show['read'] = true;
                        }
                    }
                    foreach ($groupsW as $gp) {
                        if ($gp['gid'] == $group['gid']) {
                            $selectedW = true;
                            $show['write'] = true;
                        }
                    }
                    foreach ($groupsD as $gp) {
                        if ($gp['gid'] == $group['gid']) {
                            $selectedD = true;
                            $show['delete'] = true;
                        }
                    }
                    $groupSelectR .= '<option' . (($selectedR) ? ' selected="selected"' : '') . ' value="' . $group['gid'] . '">' . $group['name'] . '</option>';
                    $groupSelectW .= '<option' . (($selectedW) ? ' selected="selected"' : '') . ' value="' . $group['gid'] . '">' . $group['name'] . '</option>';
                    $groupSelectD .= '<option' . (($selectedD) ? ' selected="selected"' : '') . ' value="' . $group['gid'] . '">' . $group['name'] . '</option>';
                }
                $groupSelectR .= '</select>';
                $groupSelectW .= '</select>';
                $groupSelectD .= '</select>';

                $template = $template[0];
                $template['content'] = str_replace("&", "&amp;", $template['content']);
                $template['content'] = str_replace(">", "&gt;", $template['content']);
                $template['content'] = str_replace("<", "&lt;", $template['content']);
                $template['content'] = str_replace('~', '&#126;', $template['content']);
                $return .= ''
                        . '<form name="template-edit-detail" method="post" action="' . $actionUrl . '">'
                        . '<div class="gray-box-float">'
                            . '<label for="template-name" class="w60">Name:</label>'
                            . '<input class="w435" type="text" name="template-name" id="template-name" value="' . $template['name'] . '" />'
                        . '</div>'
                        . '<div class="gray-box-float">'
                            . '<label for="template-group" class="w60">Group:</label>'
                            . '<input class="w180" type="text" name="template-group" id="template-group" value="' . $template['group'] . '" />'
                        . '</div>'
                        . '<div class="gray-box-float">'
                            . '<label for="template-identifier" class="w80" title="Can be used as tag name in template library">Identifier:</label>'
                            . '<input class="w180" type="text" name="template-identifier" id="template-identifier" value="' . $template['identifier'] . '" />'
                        . '</div>'
                        . '<div class="gray-box-float">'
                        . '<details>'
                        . '<summary>Permissions</summary>'
                        . (($show['read']) ? ''
                                . '<div class="float-left mr-1">'
                                . '<label for="template-right-edit-groups-r" class="d-block">Read:</label>'
                                . $groupSelectR
                                . '</div>' : '')
                        . (($show['write']) ? ''
                                . '<div class="float-left mr-1">'
                                . '<label for="template-right-edit-groups-w" class="d-block">Write:</label>'
                                . $groupSelectW
                                . '</div>' : '')
                        . (($show['delete']) ? ''
                                . '<div class="float-left mr-1">'
                                . '<label for="template-right-edit-groups-d" class="d-block">Delete:</label>'
                                . $groupSelectD
                                . '</div>' : '')
                        . '<div class="clear"></div>'
                        . '</details>'
                        . '</div>'
                        . '<div class="clear"></div>';

                $name = 'Page.editors';
                $propertyEditors = parent::system()->getPropertyValue($name);
                $editAreaTextFileRows = parent::system()->getPropertyValue('Page.editAreaTextFileRows');

                if ($propertyEditors == "edit_area") {
                    $return .= ''
                            . '<div id="editors" class="editors edit-area-editors">'
                            . '<div id="template-edit-detail-content-cover">'
                            . '<textarea id="template-edit-detail-content" class="edit-area html" name="template-content" rows="' . ($editAreaTextFileRows > 0 ? $editAreaTextFileRows : 30) . '" wrap="off">' . str_replace('~', '&#126', $template['content']) . '</textarea>'
                            . '</div>'
                            . '</div>';
                } else {
                    $return .= ''
                            . '<div class="template-edit-content">'
                            . '<label for="template-edit-detail-content">Content:</label>'
                            . '<div class="editor-cover">'
                            . '<div class="textarea-cover">'
                            . '<textarea id="template-edit-detail-content" class="editor-textarea" name="template-content" rows="15" wrap="off">' . str_replace('~', '&#126', $template['content']) . '</textarea>'
                            . '</div>'
                            . '</div>'
                            . '</div>';
                }

                $return .= ''
                        . '<div class="template-submit">'
                            . '<input type="hidden" name="template-id" value="' . $templateId . '" />'
                            . '<input type="submit" name="template-submit" value="Save" /> '
                            . '<input type="submit" name="template-submit" value="Save and close" /> '
                            . '<a href="' . $closeUrl . '" class="button">Close</a>'
                        . '</div>'
                        . '</form>';
            } else {
                if ($showError != 'false') {
                    $return .= parent::getError('Permission Denied!');
                }
            }

            if ($useFrames != "false") {
                return parent::getFrame('Template edit :: (' . ($templateId == '' ? 'New' : $templateId) . ') ' . $template['name'], $return, '');
            } else {
                return $return;
            }
        }

        public function showEmbeddedResources($useFrames = false) {
            $rb = $this->rb();
            $return = '';

            if ($_POST['er-delete'] == $rb->get('er.delete')) {
                $erid = $_POST['er-id'];
                EmbeddedResourceManager::delete($erid);
                $return .= parent::getSuccess($rb->get('er.success.deleted'));
            }

            $ers = EmbeddedResourceManager::getAll();
            if (count($ers) > 0) {
                $return .= ''
                        . '<table class="standart clickable">'
                        . '<tr>'
                        . '<th>' . $rb->get('er.id') . ':</th>'
                        . '<th>' . $rb->get('er.type') . ':</th>'
                        . '<th>' . $rb->get('er.url') . ':</th>'
                        . '<th>' . $rb->get('er.rid') . ':</th>'
                        . '<th>' . $rb->get('er.cache') . ':</th>'
                        . '<th></th>'
                        . '</tr>';
                $i = 0;
                foreach ($ers as $er) {
                    $return .= ''
                            . '<tr class="' . ((($i % 2) == 0) ? 'idle' : 'even') . '">'
                            . '<td class="id">' . $er->getId() . '</td>'
                            . '<td>' . $er->getType() . '</td>'
                            . '<td>' . $er->getUrl() . '</td>'
                            . '<td>' . $er->getRid() . '</td>'
                            . '<td>' . $er->getCache() . '</td>'
                            . '<td>'
                            . '<form name="er-edit" method="post" action="' . $_SERVER['REQUEST_URI'] . '">'
                            . '<input type="hidden" name="er-id" value="' . $er->getId() . '" />'
                            . '<input type="hidden" name="er-edit" value="' . $rb->get('er.edit') . '" />'
                            . '<input type="image" src="~/images/page_edi.png" name="er-edit" value="' . $rb->get('er.edit') . '" title="' . $rb->get('er.edittitle') . ', id = ' . $er->getId() . '" />'
                            . '</form>'
                            . '<form name="er-delete" method="post" action="' . $_SERVER['REQUEST_URI'] . '">'
                            . '<input type="hidden" name="er-id" value="' . $er->getId() . '" />'
                            . '<input type="hidden" name="er-delete" value="' . $rb->get('er.delete') . '" />'
                            . '<input class="confirm" type="image" src="~/images/page_del.png" name="er-delete" value="' . $rb->get('er.delete') . '" title="' . $rb->get('er.deletetitle') . ', id = ' . $er->getId() . '" />'
                            . '</form>'
                            . '</td>'
                            . '</tr>';
                    $i++;
                }
                $return .= ''
                        . '</table>';
            } else {
                $return .= parent::getWarning($rb->get('er.nodata'));
            }

            $return .= ''
                    . '<hr />'
                    . '<div class="gray-box">'
                    . '<form name="er-new" method="post" action="' . $_SERVER['REQUEST_URI'] . '">'
                    . '<input type="submit" name="er-new" value="' . $rb->get('er.new') . '" title="' . $rb->get('er.newtitle') . '" />'
                    . '</form>'
                    . '</div>';

            if ($useFrames != "false") {
                return parent::getFrame($rb->get('er.titlelist'), $return, '');
            } else {
                return $return;
            }
        }

        public function showEditEmbeddedResourceFrom($useFrames = false) {
            $rb = $this->rb();
            $return = '';
            $er;
            $fromSave = false;
            $errors = '';

            if ($_POST['er-save'] == $rb->get('er.save')) {
                $er = EmbeddedResourceManager::bindSingleToObject($_POST, 'er-');
                $errors = EmbeddedResourceManager::validate($er, $rb);
                if (strlen($errors) == 0) {
                    if ($er->getId() == '') {
                        $er = EmbeddedResourceManager::create($er);
                        $return .= parent::getSuccess($rb->get('er.success.created'));
                    } else {
                        $er = EmbeddedResourceManager::update($er);
                        $return .= parent::getSuccess($rb->get('er.success.updated'));
                    }
                }
                $fromSave = true;
            }

            if ($_POST['er-edit'] == $rb->get('er.edit') || $_POST['er-new'] == $rb->get('er.new') || $fromSave) {
                if ($_POST['er-edit'] == $rb->get('er.edit')) {
                    $erid = $_POST['er-id'];
                    $er = EmbeddedResourceManager::get($erid);
                } elseif ($_POST['er-new'] == $rb->get('er.new')) {
                    $er = new EmbeddedResource("", "", "", "");
                }
                $return .= ''
                        . '<form name="er-edit" method="post" action="' . $_SERVER['REQUEST_URI'] . '">'
                        . $errors
                        . '<div class="gray-box">'
                        . '<label class="w100" for="er-type">' . $rb->get('er.type') . ':</label>'
                        . '<select class="w200" name="er-type" id="er-type">'
                        . $this->simpleArrayToOptions(EmbeddedResourceManager::types(), $er->getType())
                        . '</select>'
                        . '</div>'
                        . '<div class="gray-box">'
                        . '<label class="w100" for="er-url">' . $rb->get('er.url') . ':</label>'
                        . '<input class="w400" type="text" name="er-url" id="er-url" value="' . $er->getUrl() . '" />'
                        . '</div>'
                        . '<div class="gray-box">'
                        . '<label class="w100" for="er-rid">' . $rb->get('er.rid') . ':</label>'
                        . '<input class="w60" type="text" name="er-rid" id="er-rid" value="' . $er->getRid() . '" />'
                        . '</div>'
                        . '<div class="gray-box">'
                        . '<label class="w100" for="er-cache">' . $rb->get('er.cache') . ':</label>'
                        . '<select class="w200" name="er-cache" id="er-cache">'
                        . $this->simpleArrayToOptions(EmbeddedResourceManager::cache(), $er->getCache())
                        . '</select>'
                        . '</div>'
                        . '<div class="gray-box">'
                        . '<input type="hidden" name="er-id" value="' . $er->getId() . '" />'
                        . '<input type="submit" name="er-save" value="' . $rb->get('er.save') . '" title="' . $rb->get('er.savetitle') . '" /> '
                        . '<input type="submit" name="er-close" value="' . $rb->get('er.close') . '" title="' . $rb->get('er.closetitle') . '" />'
                        . '</div>'
                        . '</form>';

                if ($useFrames != "false") {
                    return parent::getFrame($rb->get('er.titleedit') . ' :: (' . ($er->getId() != '' ? $er->getId() : 'New') . ') ', $return, '');
                } else {
                    return $return;
                }
            }
        }

        public function showWebForwards($useFrames = false) {
            $rb = $this->rb();
            $return = '';

            if ($_POST['wf-delete'] == $rb->get('wf.delete')) {
                $id = $_POST['wf-id'];
                WebForwardManager::delete($id);
                $return .= parent::getSuccess($rb->get('wf.success.deleted'));
            }

            $wfs = WebForwardManager::getAll();
            if (count($wfs) > 0) {
                $return .= ''
                        . '<table class="standart clickable">'
                            . '<tr>'
                                . '<th>' . $rb->get('wf.id') . ':</th>'
                                . '<th>' . $rb->get('wf.type') . ':</th>'
                                . '<th>' . $rb->get('wf.rule') . ':</th>'
                                . '<th>' . $rb->get('wf.condition') . ':</th>'
                                . '<th>' . $rb->get('wf.pageId') . ':</th>'
                                . '<th>' . $rb->get('wf.langId') . ':</th>'
                                . '<th>' . $rb->get('wf.order') . ':</th>'
                                . '<th>' . $rb->get('wf.enabled') . ':</th>'
                                . '<th></th>'
                        . '</tr>';
                $i = 0;
                foreach ($wfs as $wf) {
                    $return .= ''
                        . '<tr class="' . ((($i % 2) == 0) ? 'idle' : 'even') . '">'
                            . '<td class="id">' . $wf->getId() . '</td>'
                            . '<td>' . $wf->getType() . '</td>'
                            . '<td>' . $wf->getRule() . '</td>'
                            . '<td>' . $wf->getCondition() . '</td>'
                            . '<td>' . $wf->getPageId() . '</td>'
                            . '<td>' . $wf->getLangId() . '</td>'
                            . '<td>' . $wf->getOrder() . '</td>'
                            . '<td>' . ($wf->getEnabled() ? $rb->get('wf.enabled') : $rb->get('wf.diabled')) . '</td>'
                            . '<td>'
                                . '<form name="wf-edit" method="post" action="' . $_SERVER['REQUEST_URI'] . '">'
                                    . '<input type="hidden" name="wf-id" value="' . $wf->getId() . '" />'
                                    . '<input type="hidden" name="wf-edit" value="' . $rb->get('wf.edit') . '" />'
                                    . '<input type="image" src="~/images/page_edi.png" name="wf-edit" value="' . $rb->get('wf.edit') . '" title="' . $rb->get('wf.edittitle') . ', id = ' . $wf->getId() . '" />'
                                . '</form> '
                                . '<form name="wf-delete" method="post" action="' . $_SERVER['REQUEST_URI'] . '">'
                                    . '<input type="hidden" name="wf-id" value="' . $wf->getId() . '" />'
                                    . '<input type="hidden" name="wf-delete" value="' . $rb->get('er.delete') . '" />'
                                    . '<input class="confirm" type="image" src="~/images/page_del.png" name="wf-delete" value="' . $rb->get('wf.delete') . '" title="' . $rb->get('wf.deletetitle') . ', id = ' . $wf->getId() . '" />'
                                . '</form>'
                            . '</td>'
                        . '</tr>';
                    $i++;
                }
                $return .= ''
                        . '</table>';
            } else {
                $return .= parent::getWarning($rb->get('wf.nodata'));
            }

            $return .= ''
            . '<hr />'
            . '<div class="gray-box">'
                . '<form name="wf-new" method="post" action="' . $_SERVER['REQUEST_URI'] . '">'
                    . '<input type="submit" name="wf-new" value="' . $rb->get('wf.new') . '" title="' . $rb->get('wf.newtitle') . '" />'
                . '</form>'
            . '</div>';

            if ($useFrames != "false") {
                return parent::getFrame($rb->get('wf.titlelist'), $return, '');
            } else {
                return $return;
            }
        }

        public function showEditWebForwardFrom($useFrames = false) {
            $rb = $this->rb();
            $return = '';
            $wf;
            $fromSave = false;
            $errors = '';

            if ($_POST['wf-save'] == $rb->get('wf.save')) {
                $_POST['wf-enabled'] = $_POST['wf-enabled'] == 'on' ? 1 : 0;
                $wf = WebForwardManager::bindSingleToObject($_POST, 'wf-');
                $errors = WebForwardManager::validate($wf, $rb);
                if (strlen($errors) == 0) {
                    if ($wf->getId() == '' || $wf->getId() == 0) {
                        $wf = WebForwardManager::create($wf);
                        $return .= parent::getSuccess($rb->get('wf.success.created'));
                    } else {
                        $wf = WebForwardManager::update($wf);
                        $return .= parent::getSuccess($rb->get('wf.success.updated'));
                    }
                }
                $fromSave = true;
            }

            if ($_POST['wf-edit'] == $rb->get('wf.edit') || $_POST['wf-new'] == $rb->get('wf.new') || $fromSave) {
                if ($_POST['wf-edit'] == $rb->get('wf.edit')) {
                    $id = $_POST['wf-id'];
                    $wf = WebForwardManager::get($id);
                } elseif ($_POST['wf-new'] == $rb->get('wf.new')) {
                    $wf = new WebForward("", "Forward", "", "", 1, 1, 1, 0);
                }
                $return .= ''
                . '<form name="wf-edit" method="post" action="' . $_SERVER['REQUEST_URI'] . '">'
                    . $errors
                    . '<div class="gray-box">'
                        . '<label class="w100" for="wf-rule" title="' . $rb->get('wf.rulecap') . '">' . $rb->get('wf.rule') . ':</label>'
                        . '<input class="w400" type="text" name="wf-rule" id="wf-rule" value="' . $wf->getRule() . '" />'
                    . '</div>'
                    . '<div class="gray-box">'
                        . '<label class="w100" for="wf-type">' . $rb->get('wf.type') . ':</label>'
                        . '<select class="w200" name="wf-type" id="wf-type">'
                            . $this->simpleArrayToOptions(WebForwardManager::type(), $wf->getType())
                        . '</select>'
                    . '</div>'
                    . '<div class="gray-box">'
                        . '<label class="w100" for="wf-condition">' . $rb->get('wf.condition') . ':</label>'
                        . '<select class="w200" name="wf-condition" id="wf-condition">'
                            . $this->simpleArrayToOptions(WebForwardManager::condition(), $wf->getCondition())
                        . '</select>'
                    . '</div>'
                    . '<div class="gray-box">'
                        . '<label class="w100" for="wf-page-id">' . $rb->get('wf.pageId') . ':</label>'
                        . '<input class="w60" type="text" name="wf-page_id" id="wf-page-id" value="' . $wf->getPageId() . '" />'
                    . '</div>'
                    . '<div class="gray-box">'
                        . '<label class="w100" for="wf-lang-id">' . $rb->get('wf.langId') . ':</label>'
                        . '<input class="w60" type="text" name="wf-lang_id" id="wf-lang-id" value="' . $wf->getLangId() . '" />'
                    . '</div>'
                    . '<div class="gray-box">'
                        . '<label class="w100" for="wf-order">' . $rb->get('wf.order') . ':</label>'
                        . '<input class="w60" type="text" name="wf-order" id="wf-order" value="' . $wf->getOrder() . '" />'
                    . '</div>'
                    . '<div class="gray-box">'
                        . '<label class="w100" for="wf-enabled">' . $rb->get('wf.enabled') . ':</label>'
                        . '<input type="checkbox" name="wf-enabled" id="wf-enabled"' . ($wf->getEnabled() ? ' checked="checked"' : '') . ' />'
                    . '</div>'
                    . '<div class="gray-box">'
                        . '<input type="hidden" name="wf-id" value="' . $wf->getId() . '" />'
                        . '<input type="submit" name="wf-save" value="' . $rb->get('wf.save') . '" title="' . $rb->get('wf.savetitle') . '" /> '
                        . '<input type="submit" name="wf-close" value="' . $rb->get('wf.close') . '" title="' . $rb->get('wf.closetitle') . '" />'
                    . '</div>'
                . '</form>';

                if ($useFrames != "false") {
                    return parent::getFrame($rb->get('wf.titleedit') . ' :: (' . ($wf->getId() != '' ? $wf->getId() : 'New') . ') '.$wf->getRule(), $return, '');
                } else {
                    return $return;
                }
            }
        }

        private function simpleArrayToOptions($arr, $act) {
            $return = '';
            foreach ($arr as $it) {
                $return .= '<option value="' . $it . '"' . ($it == $act ? ' selected="selected"' : '') . '>' . $it . '</option>';
            }
            return $return;
        }

        public function savePage($template, $pageId, $languageId, $params) {
            $return = "";
            if (count($params) == 0) {
                return $return;
            }

            $db = parent::db();
            $whereSql = 'where `page_id` = ' . $db->escape($pageId) . ' and `language_id` = ' . $db->escape($languageId) . '';
            $exists = $db->fetchSingle('select count(*) as `count` from `info` ' . $whereSql . ';');
            if ($exists['count'] == 0) {
                return $return;
            }

            if (!RoleHelper::isInRole(parent::login()->getGroupsIds(), RoleHelper::getRights(Page::$PageRightDesc, $pageId, WEB_R_WRITE))) {
                return $return;
            }

            $updateContentSql = '';
            if (array_key_exists('content', $params)) {
                if (strlen($updateContentSql) > 0) {
                    $updateContentSql .= ', ';
                }

                $content = $params['content'];
                $content = str_replace('&amp;web:page', '&web:page', $content);
                $content = $db->escape(str_replace('&#126', '~', $content));

                $updateContentSql .= '`content` = "' . $db->escape($content) . '"';
            }

            $sql = 'update `content` set ' . $updateContentSql . ' ' . $whereSql . ';';
            $db->execute($sql);
            if ($db->getDataAccess()->getErrorCode() == 0) {
                $return .= $template();
            }

            return $return;
        }

        public function getPageList($template, $webProjectId, $pageId = false, $languageId = false) {
            $return = "";

            $sql = 'select * from `page` p join `info` i on p.`id` = i.`page_id` join `content` c on p.`id` = c.`page_id`';
            $whereSql = ' where ' . RoleHelper::existsSql(Page::$PageRightDesc, 'p.`id`', WEB_R_READ);

            if (is_numeric($webProjectId)) {
                if (strlen($whereSql) == 0) {
                    $whereSql .= ' where ';
                } else {
                    $whereSql .= ' and ';
                }

                $whereSql .= 'p.`wp` = ' . $webProjectId;
            }

            if (is_numeric($pageId)) {
                if (strlen($whereSql) == 0) {
                    $whereSql .= ' where ';
                } else {
                    $whereSql .= ' and ';
                }

                $whereSql .= 'p.`id` = ' . $pageId;
            }

            if (is_numeric($languageId)) {
                if (strlen($whereSql) == 0) {
                    $whereSql .= ' where ';
                } else {
                    $whereSql .= ' and ';
                }

                $whereSql .= 'i.`language_id` = ' . $languageId;
            }

            $sql .= $whereSql . ';';
            $data = parent::db()->fetchAll($sql);
            $oldItem = parent::request()->get('item', 'p:pageList');

            foreach ($data as $item) {
                parent::request()->set('item', $item, 'p:pageList');
                $return .= $template();
            }

            parent::request()->set('item', $oldItem, 'p:pageList');
            return $return;
        }

        public function getPageValue($type) {
            $item = parent::request()->get('item', 'p:pageList');
            if (is_array($item) && array_key_exists($type, $item)) {
                return htmlentities($item[$type]);
            }

            return null;
        }

        private function searchPageProperty($property, $text) {
            $infoJoin = [
                "leftjoin" => [
                    "table" => "info", 
                    "alias" => "i", 
                    "source" => "page_id", 
                    "target" => "page_id"
                ], 
                "select" => [
                    "column" => "name", 
                    "alias" => "name"
                ]
            ];
			$sql = parent::sql()->select(["table" => "content", "alias" => "c"], ["page_id", $property, $infoJoin], "c.`$property` like " . $text . "");
            $data = parent::dataAccess()->fetchAll($sql);

            $items = [];
            foreach ($data as $item) {
                $items[] = [
                    "type" => "Page",
                    "subType" => $property,
                    "id" => $item["page_id"],
                    "name" => $item["name"],
                    "content" => $item[$property]
                ];
            }

            return $items;
        }

        private function searchTemplate($text) {
			$sql = parent::sql()->select("template", ["id", "name", "content"], "`content` like " . $text . "");
            $data = parent::dataAccess()->fetchAll($sql);

            $items = [];
            foreach ($data as $item) {
                $items[] = [
                    "type" => "Template",
                    "subType" => "content",
                    "id" => $item["id"],
                    "name" => $item["name"],
                    "content" => $item["content"]
                ];
            }

            return $items;
        }

        private function searchTextFile($text) {
			$sql = parent::sql()->select("page_file", ["id", "name", "content"], "`content` like " . $text . "");
            $data = parent::dataAccess()->fetchAll($sql);

            $items = [];
            foreach ($data as $item) {
                $items[] = [
                    "type" => "Text File",
                    "subType" => "content",
                    "id" => $item["id"],
                    "name" => $item["name"],
                    "content" => $item["content"]
                ];
            }

            return $items;
        }

        public function search($template, string $text, string $location = "") {
            $model = new ListModel();
            parent::pushListModel($model);

            $text = parent::sql()->escape("%$text%");
            if (empty($location)) {
                $location = "page-tlstart,page-tlend,page-content,page-head,template-content,textfile-content";
            }

            $location = explode(",", $location);

            $items = array_merge(
                in_array("page-tlstart", $location) ? $this->searchPageProperty("tag_lib_start", $text) : [],
                in_array("page-tlend", $location) ? $this->searchPageProperty("tag_lib_end", $text) : [],
                in_array("page-head", $location) ? $this->searchPageProperty("head", $text) : [],
                in_array("page-content", $location) ? $this->searchPageProperty("content", $text) : [],
                in_array("template-content", $location) ? $this->searchTemplate($text) : [],
                in_array("textfile-content", $location) ? $this->searchTextFile($text) : []
            );

			$model->render();
            $model->items($items);
			$result = $template();

			parent::popListModel();
			return $result;
        }

        public function getSearchList() {
            return parent::peekListModel();
        }
        
        public function getSearchName() {
            return parent::peekListModel()->field("name");
        }
        
        public function getSearchType() {
            return parent::peekListModel()->field("type");
        }
        
        public function getSearchSubType() {
            return parent::peekListModel()->field("subType");
        }
        
        public function getSearchContent() {
            return parent::peekListModel()->field("content");
        }
        
        public function getSearchId() {
            return parent::peekListModel()->field("id");
        }
    }

?>
