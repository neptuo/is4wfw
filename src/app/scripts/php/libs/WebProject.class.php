<?php

    require_once("BaseTagLib.class.php");
    require_once("System.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/LocalizationBundle.class.php");

    /**
     * 
     *  Class WebProject.
     *  management of web projects
     *      
     *  @author     Marek SMM
     *  @timestamp  2012-01-29
     * 
     */
    class WebProject extends BaseTagLib {

        public function __construct() {
            self::setTagLibXml("WebProject.xml");
            self::setLocalizationBundle("webproject");
        }

        /**
         *
         * 	Select web project id and save it to session var.
         * 	C tag.
         *
         */
        public function selectProject($label = false, $useFrames = false, $showMsg = false) {
            global $dbObject;
            global $loginObject;
            $rb = self::rb();
            $return = '';
            $projects = array();

            if ($_POST['select-project'] == $rb->get('selectproject.submit')) {
                $projectId = $_POST['project-id'];
                $permission = $dbObject->fetchAll('SELECT `value` FROM `web_project_right` LEFT JOIN `group` ON `web_project_right`.`gid` = `group`.`gid` WHERE `wp` = ' . $projectId . ' AND `type` = ' . WEB_R_WRITE . ' AND (`group`.`gid` IN (' . $loginObject->getGroupsIdsAsString() . ') OR `group`.`parent_gid` IN (' . $loginObject->getGroupsIdsAsString() . ')) ORDER BY `value` DESC;');
                if (count($permission) > 0) {
                    $_SESSION['selected-project'] = $projectId;
                    if ($showMsg != 'false') {
                        $return .= '<h4 class="success">' . $rb->get('selectproject.success') . '</h4>';
                    }
                } else {
                    if ($showMsg != 'false') {
                        $return .= '<h4 class="error">' . $rb->get('selectproject.failed') . '</h4>';
                    }
                }
                $projects = $dbObject->fetchAll('SELECT `web_project`.`id` FROM `web_project` LEFT JOIN `web_project_right` ON `web_project`.`id` = `web_project_right`.`wp` LEFT JOIN `group` ON `web_project_right`.`gid` = `group`.`gid` WHERE `web_project_right`.`type` = ' . WEB_R_WRITE . ' AND (`group`.`gid` IN (' . $loginObject->getGroupsIdsAsString() . ') OR `group`.`parent_gid` IN (' . $loginObject->getGroupsIdsAsString() . '));');
            } else {
                $projects = $dbObject->fetchAll('SELECT `web_project`.`id` FROM `web_project` LEFT JOIN `web_project_right` ON `web_project`.`id` = `web_project_right`.`wp` LEFT JOIN `group` ON `web_project_right`.`gid` = `group`.`gid` WHERE `web_project_right`.`type` = ' . WEB_R_WRITE . ' AND (`group`.`gid` IN (' . $loginObject->getGroupsIdsAsString() . ') OR `group`.`parent_gid` IN (' . $loginObject->getGroupsIdsAsString() . '));');
                $ok = false;
                foreach ($projects as $project) {
                    if ($_SESSION['selected-project'] == $project['id']) {
                        $ok = true;
                    }
                }
                if (!$ok) {
                    $val = parent::system()->getPropertyValue('WebProject.defaultProjectId');
                    $proj = parent::db()->fetchSingle('select `id` from `web_project` where `id` = ' . $val . ';');
                    if ($val > 0 && $proj != array()) {
                        $_SESSION['selected-project'] = $val;
                    } elseif (count($projects) > 0) {
                        $_SESSION['selected-project'] = $projects[0]['id'];
                    } else {
                        $_SESSION['selected-project'] = '';
                    }
                }
            }

            $projectId = $_SESSION['selected-project'];

            if (count($projects) > 0) {
                if($label == false) {
                    $label = $rb->get('selectproject.label');
                }
            
                $return .= ''
                        . '<div class="select-project">'
                        . '<form name="select-project" method="post" action="' . $_SERVER['REQUEST_URI'] . '">'
                        . '<label for="select-project">' . $label . '</label> '
                        . '<select id="select-project" name="project-id">';
                $projects = $dbObject->fetchAll('SELECT DISTINCT `web_project`.`id`, `web_project`.`name` FROM `web_project` LEFT JOIN `web_project_right` ON `web_project`.`id` = `web_project_right`.`wp` LEFT JOIN `group` ON `web_project_right`.`gid` = `group`.`gid` WHERE `web_project_right`.`type` = ' . WEB_R_WRITE . ' AND (`group`.`gid` IN (' . $loginObject->getGroupsIdsAsString() . ') OR `group`.`parent_gid` IN (' . $loginObject->getGroupsIdsAsString() . ')) ORDER BY `web_project`.`name`;');
                foreach ($projects as $project) {
                    $permission = $dbObject->fetchAll('SELECT `value` FROM `web_project_right` LEFT JOIN `group` ON `web_project_right`.`gid` = `group`.`gid` WHERE `wp` = ' . $project['id'] . ' AND `type` = ' . WEB_R_WRITE . ' ORDER BY `value` DESC;');
                    if (count($permission)) {
                        $return .= '<option value="' . $project['id'] . '"' . (($projectId == $project['id']) ? ' selected="selected"' : '') . '>' . $project['name'] . '</option>';
                    }
                }
                $return .= ''
                        . '</select> '
                        . '<input type="submit" name="select-project" value="' . $rb->get('selectproject.submit') . '" />'
                        . '</form>'
                        . '</div>';
            } else {
                if ($showMsg != 'false') {
                    $return .= '<div class="select-project">' . parent::getWarning('No projects') . '</div>';
                }
            }

            if ($useFrames == "false") {
                return $return;
            } else {
                return parent::getFrame($rb->get('selectproject.label'), $return, "", true);
            }
        }

        /**
         *
         * 	Show all web projects which user can see, edit, delete ...
         * 	C tag.		 
         *
         *
         */
        public function showProjects($detailPageId = false, $editable = false) {
            $rb = self::rb();
            global $webObject;
            global $dbObject;
            global $loginObject;
            $return = '';

            if ($_POST['delete'] == $rb->get('project.delete')) {
                $projectId = $_POST['wp'];
                $permission = $dbObject->fetchAll('SELECT `value` FROM `web_project_right` LEFT JOIN `group` ON `web_project_right`.`gid` = `group`.`gid` WHERE `web_project_right`.`wp` = ' . $projectId . ' AND `web_project_right`.`type` = ' . WEB_R_DELETE . ' AND (`group`.`gid` IN (' . $loginObject->getGroupsIdsAsString() . ') OR `group`.`parent_gid` IN (' . $loginObject->getGroupsIdsAsString() . ')) ORDER BY `value` DESC;');
                if (count($permission) > 0) {
                    $pages = $dbObject->fetchAll('SELECT `id` FROM `page` WHERE `wp` = ' . $projectId . ';');
                    if (count($pages) == 0) {
                        $dbObject->execute('DELETE FROM `web_project_right` WHERE `wp` = ' . $projectId . ';');
                        $dbObject->execute('DELETE FROM `web_url` WHERE `project_id` = ' . $projectId . ';');
                        $dbObject->execute('DELETE FROM `web_project` WHERE `id` = ' . $projectId . ';');

                        $return .= parent::getSuccess($rb->get('project.deleted'));
                    } else {
                        $return .= parent::getError($rb->get('project.pagesexist'));
                    }
                } else {
                    $return .= parent::getError($rb->get('project.permdenied'));
                }
            }

            $actionUrl = $_SERVER['REQUEST_URI'];
            if ($editable == "true" && $detailPageId != false) {
                $actionUrl = $webObject->composeUrl($detailPageId);
            }

            $projects = $dbObject->fetchAll('SELECT DISTINCT `web_project`.`id`, `web_project`.`name` FROM `web_project` LEFT JOIN `web_project_right` ON `web_project`.`id` = `web_project_right`.`wp` LEFT JOIN `group` ON `web_project_right`.`gid` = `group`.`gid` WHERE `web_project_right`.`type` = ' . WEB_R_WRITE . ' AND (`group`.`gid` IN (' . $loginObject->getGroupsIdsAsString() . ') OR `group`.`parent_gid` IN (' . $loginObject->getGroupsIdsAsString() . ')) ORDER BY `id`;');

            if (count($projects) == 0) {
                $return .= parent::getWarning($rb->get('project.nodata'));
            } else {
                $return .= ''
                        . '<table class="show-projects data-table standart clickable">'
                        . '<thead>'
                        . '<tr>'
                        . '<th class="th-id">' . $rb->get('project.id') . ':</th>'
                        . '<th class="th-name">' . $rb->get('project.name') . ':</th>'
                        . '<th class="th-url">' . $rb->get('project.url') . ':</th>'
                        //. '<th class="th-url"></th>'
                        . (($editable == "true") ? ''
                                . '<th class="th-edit"></th>' : '')
                        . '</tr>'
                        . '</thead>'
                        . '<tbody>';

                $i = 1;
                foreach ($projects as $project) {
                    $rights = $dbObject->fetchAll("SELECT `group`.`name` FROM `group` LEFT JOIN `web_project_right` ON `group`.`gid` = `web_project_right`.`gid` WHERE `web_project_right`.`wp` = " . $project['id'] . " AND `web_project_right`.`type` = " . WEB_R_WRITE . ";");
                    $ok = true;
                    if (count($rights) > 0) {
                        $ok = false;
                        foreach ($rights as $right) {
                            foreach ($loginObject->getGroups() as $u_gp) {
                                if ($right['name'] == $u_gp['name']) {
                                    $ok = true;
                                }
                            }
                        }
                    }

                    $url = parent::db()->fetchSingle('select `http`, `https`, `domain_url`, `root_url` from `web_url` where `project_id` = ' . $project['id'] . ' order by `default` desc, `id`;');
                    $project['http'] = $url['http'];
                    $project['https'] = $url['https'];
                    if ($url['root_url'] != '') {
                        $project['url'] = $url['domain_url'] . '/' . $url['root_url'];
                    } else {
                        $project['url'] = $url['domain_url'];
                    }
                    $pages = $dbObject->fetchAll('SELECT `id` FROM `page` WHERE `wp` = ' . $project['id'] . ' LIMIT 1;');
                    if ($ok == true) {
                        $return .= ''
                                . '<tr class="' . ((($i % 2) == 0) ? 'even' : 'idle') . '">'
                                . '<td class="td-id">' . $project['id'] . '</td>'
                                . '<td class="td-name">' . $project['name'] . '</td>'
                                . '<td class="td-url">' . $project['url'] . '</td>'
                                //. '<td class="td-url">' . '<a target="_blank" href="' . (($project['http'] == 1) ? 'http://' : 'https://') . $project['url'] . '">view</a>' . '</td>'
                                . '<td class="td-edit">'
                                . (($editable == "true") ? ''
                                        . '<form name="edit-projects1" method="post" action="' . $actionUrl . '"> '
                                        . '<input type="hidden" name="wp" value="' . $project['id'] . '" />'
                                        . '<input type="hidden" name="edit" value="' . $rb->get('project.edit') . '" />'
                                        . '<input type="image" src="~/images/page_edi.png" name="edit" value="' . $rb->get('project.edit') . '" />'
                                        . '</form>'
                                        . ((count($pages) == 0) ? ''
                                                . '<form name="edit-projects2" method="post" action="' . $_SERVER['REQUEST_URI'] . '"> '
                                                . '<input type="hidden" name="wp" value="' . $project['id'] . '" />'
                                                . '<input type="hidden" name="delete" value="' . $rb->get('project.delete') . '" />'
                                                . '<input class="confirm" type="image" src="~/images/page_del.png" name="delete" value="' . $rb->get('project.delete') . '" title="' . $rb->get('project.deletetitle') . ', id(' . $project['id'] . ')" />'
                                                . '</form>' : '') : '')
                                . '</td>'
                                . '</tr>';

                        $i++;
                    }
                }

                $return .= ''
                        . '</tbody>'
                        . '</table>';
            }

            $right = $dbObject->fetchAll('SELECT `web_project_right`.`wp` FROM `web_project_right` LEFT JOIN `group` ON `web_project_right`.`gid` = `group`.`gid` WHERE `web_project_right`.`type` = ' . WEB_R_WRITE . ' AND (`group`.`gid` IN (' . $loginObject->getGroupsIdsAsString() . ') OR `group`.`parent_gid` IN (' . $loginObject->getGroupsIdsAsString() . ')) AND `web_project_right`.`wp` = 0;');
            if (count($right) != 0) {
                $return .= ''
                        . (($editable == "true") ? ''
                                . '<hr />'
                                . '<form name="projects-new" method="post" action="' . $actionUrl . '">'
                                . '<input type="submit" name="new-project" value="' . $rb->get('project.create') . '" title="Create new project" />'
                                . ''
                                . ''
                                . '</form>' : '');
            }

            //$loginObject->getGroups();

            return parent::getFrame($rb->get('projects.title'), $return, "", true);
        }

        /**
         *
         * 	Generates from for editing project.
         * 	C tag.		 
         *
         */
        public function showEditForm($showNotSelectedError = false) {
            $rb = self::rb();
            global $webObject;
            global $dbObject;
            global $loginObject;
            $projectData = null;
            $formSave = false;
            $return = '';

            if ($_POST['save-project'] == $rb->get('project.save') || $_POST['save-project'] == $rb->get('project.saveandclose')) {
                //echo '<pre>';
                //print_r($_POST);
                //echo '</pre>';
                $project = array('id' => $_POST['project-id'], 'name' => $_POST['project-name'], 'read' => $_POST['project-right-edit-groups-r'], 'write' => $_POST['project-right-edit-groups-w'], 'delete' => $_POST['project-right-edit-groups-d'], 'wysiwyg' => $_POST['project-edit-styles-wysiwyg']);
                $urls['domain'] = $_POST['project-urls-domain'];
                $urls['root'] = $_POST['project-urls-root'];
                $urls['virtual'] = $_POST['project-urls-virtual'];
                $urls['http'] = $_POST['project-urls-http'];
                $urls['https'] = $_POST['project-urls-https'];
                $urls['default'] = $_POST['project-urls-default'];
                $urls['defaults'] = array();
                $urls['enabled'] = $_POST['project-urls-enabled'];
                $newUrlId = 0;
                $errors = array();

                $permission = $dbObject->fetchAll('SELECT `value` FROM `web_project_right` LEFT JOIN `group` ON `web_project_right`.`gid` = `group`.`gid` WHERE `web_project_right`.`wp` = ' . $project['id'] . ' AND `web_project_right`.`type` = ' . WEB_R_WRITE . ' AND (`group`.`gid` IN (' . $loginObject->getGroupsIdsAsString() . ') OR `group`.`parent_gid` IN (' . $loginObject->getGroupsIdsAsString() . '));');
                if (count($permission) > 0) {
                    $sameNames = $dbObject->fetchAll('SELECT `id` FROM `web_project` WHERE `name` = "' . $project['name'] . '" AND `id` != ' . $project['id'] . ';');
                    if (count($sameNames) != 0) {
                        $errors[] = $rb->get('project.nameused');
                    }

                    if (count($errors) == 0) {
                        if ($project['id'] == 0) {
                            // vlozit novy projekt
                            parent::db()->execute('INSERT INTO `web_project`(`name`) VALUES ("' . $project['name'] . '");');
                            $projectId = parent::db()->getLastId();
                            $project['id'] = $projectId;

                            // Vlozit url adresy
                            //parent::db()->execute('delete from `web_url` where `project_id` = '.$projectId.';');
                            foreach ($urls['domain'] as $key => $domainUrl) {
                                //$others = parent::db()->fetchAll('select `id` from `web_url` where `domain_url` = "' . $domainUrl . '" and `root_url` = "' . $urls['root'][$key] . '";');
                                //if (count($others) != 0) {
                                //    $warnings .= parent::getWarning($rb->get('project.urlused'));
                                //}
                                if (trim($domainUrl) != '') {
                                    $sql = 'insert into `web_url`(`project_id`, `domain_url`, `root_url`, `virtual_url`, `http`, `https`, `default`, `enabled`) values(' . $projectId . ', "' . $domainUrl . '", "' . $urls['root'][$key] . '", "' . $urls['virtual'][$key] . '", ' . ($urls['http'][$key] ? 1 : 0) . ', ' . ($urls['https'][$key] ? 1 : 0) . ', ' . ($urls['default'][$key] ? 1 : 0) . ', ' . ($urls['enabled'][$key] ? 1 : 0) . ');';
                                    parent::db()->execute($sql);
                                    if ($key == 'new') {
                                        $newUrlId = parent::db()->getLastId();
                                    }
                                }
                            }

                            if (count($project['read']) != 0) {
                                foreach ($project['read'] as $right) {
                                    parent::db()->execute("INSERT INTO `web_project_right`(`wp`, `gid`, `type`) VALUES (" . $projectId . ", " . $right . ", " . WEB_R_READ . ");");
                                }
                            } else {
                                parent::db()->execute('insert into `web_project_right`(`wp`, `gid`, `type`) select ' . $projectId . ', `gid`, ' . WEB_R_READ . ' from `web_project_right` where `wp` = 0;');
                            }
                            if (count($project['write']) != 0) {
                                foreach ($project['write'] as $right) {
                                    $dbObject->execute("INSERT INTO `web_project_right`(`wp`, `gid`, `type`) VALUES (" . $projectId . ", " . $right . ", " . WEB_R_WRITE . ");");
                                }
                            } else {
                                parent::db()->execute('insert into `web_project_right`(`wp`, `gid`, `type`) select ' . $projectId . ', `gid`, ' . WEB_R_WRITE . ' from `web_project_right` where `wp` = 0;');
                            }
                            if (count($project['delete']) != 0) {
                                foreach ($project['delete'] as $right) {
                                    $dbObject->execute("INSERT INTO `web_project_right`(`wp`, `gid`, `type`) VALUES (" . $projectId . ", " . $right . ", " . WEB_R_DELETE . ");");
                                }
                            } else {
                                parent::db()->execute('insert into `web_project_right`(`wp`, `gid`, `type`) select ' . $projectId . ', `gid`, ' . WEB_R_DELETE . ' from `web_project_right` where `wp` = 0;');
                            }
                        } else {
                            // update stavajiciho projektu
                            parent::db()->execute('UPDATE `web_project` SET `name` = "' . $project['name'] . '" WHERE `id` = ' . $project['id'] . ';');

                            parent::db()->execute('delete from `web_url` where `project_id` = ' . $project['id'] . ';');
                            foreach ($urls['domain'] as $key => $domainUrl) {
                                //$others = parent::db()->fetchAll('select `id` from `web_url` where `domain_url` = "' . $domainUrl . '" and `root_url` = "' . $urls['root'][$key] . '";');
                                //if (count($others) != 0) {
                                //    $warnings .= parent::getWarning($rb->get('project.urlused'));
                                //}
                                if (trim($domainUrl) != '') {
                                    $sql = 'insert into `web_url`(`project_id`, `domain_url`, `root_url`, `virtual_url`, `http`, `https`, `default`, `enabled`) values(' . $project['id'] . ', "' . $domainUrl . '", "' . $urls['root'][$key] . '", "' . $urls['virtual'][$key] . '", ' . ($urls['http'][$key] ? 1 : 0) . ', ' . ($urls['https'][$key] ? 1 : 0) . ', ' . ($urls['default'] == $key ? 1 : 0) . ', ' . ($urls['enabled'][$key] ? 1 : 0) . ');';
                                    parent::db()->execute($sql);
                                    if ($key == 'new') {
                                        $newUrlId = parent::db()->getLastId();
                                    }
                                }
                            }

                            if (count($project['read']) != 0) {
                                $dbR = $dbObject->fetchAll("SELECT `gid` FROM `web_project_right` WHERE `web_project_right`.`wp` = " . $project['id'] . " AND `type` = " . WEB_R_READ . ";");
                                foreach ($dbR as $right) {
                                    if (!in_array($right, $project['read'])) {
                                        $dbObject->execute("DELETE FROM `web_project_right` WHERE `wp` = " . $project['id'] . " AND `type` = " . WEB_R_READ . ";");
                                    }
                                }
                                foreach ($project['read'] as $right) {
                                    $row = $dbObject->fetchAll("SELECT `gid` FROM `web_project_right` WHERE `wp` = " . $project['id'] . " AND `type` = " . WEB_R_READ . " AND `gid` = " . $right . ";");
                                    if (count($row) == 0) {
                                        $dbObject->execute("INSERT INTO `web_project_right`(`wp`, `gid`, `type`) VALUES (" . $project['id'] . ", " . $right . ", " . WEB_R_READ . ")");
                                    }
                                }
                            } else {
                                parent::db()->execute('insert into `web_project_right`(`wp`, `gid`, `type`) select ' . $project['id'] . ', `gid`, ' . WEB_R_READ . ' from `web_project_right` where `wp` = 0;');
                            }
                            if (count($project['write']) != 0) {
                                $dbR = $dbObject->fetchAll("SELECT `gid` FROM `web_project_right` WHERE `web_project_right`.`wp` = " . $project['id'] . " AND `type` = " . WEB_R_WRITE . ";");
                                foreach ($dbR as $right) {
                                    if (!in_array($right, $project['write'])) {
                                        $dbObject->execute("DELETE FROM `web_project_right` WHERE `wp` = " . $project['id'] . " AND `type` = " . WEB_R_WRITE . ";");
                                    }
                                }
                                foreach ($project['write'] as $right) {
                                    $row = $dbObject->fetchAll("SELECT `gid` FROM `web_project_right` WHERE `wp` = " . $project['id'] . " AND `type` = " . WEB_R_WRITE . " AND `gid` = " . $right . ";");
                                    if (count($row) == 0) {
                                        $dbObject->execute("INSERT INTO `web_project_right`(`wp`, `gid`, `type`) VALUES (" . $project['id'] . ", " . $right . ", " . WEB_R_WRITE . ")");
                                    }
                                }
                            } else {
                                parent::db()->execute('insert into `web_project_right`(`wp`, `gid`, `type`) select ' . $project['id'] . ', `gid`, ' . WEB_R_WRITE . ' from `web_project_right` where `wp` = 0;');
                            }
                            if (count($project['delete']) != 0) {
                                $dbR = $dbObject->fetchAll("SELECT `gid` FROM `web_project_right` WHERE `web_project_right`.`wp` = " . $project['id'] . " AND `type` = " . WEB_R_DELETE . ";");
                                foreach ($dbR as $right) {
                                    if (!in_array($right, $project['delete'])) {
                                        $dbObject->execute("DELETE FROM `web_project_right` WHERE `wp` = " . $project['id'] . " AND `type` = " . WEB_R_DELETE . ";");
                                    }
                                }
                                foreach ($project['delete'] as $right) {
                                    $row = $dbObject->fetchAll("SELECT `gid` FROM `web_project_right` WHERE `wp` = " . $project['id'] . " AND `type` = " . WEB_R_DELETE . " AND `gid` = " . $right . ";");
                                    if (count($row) == 0) {
                                        $dbObject->execute("INSERT INTO `web_project_right`(`wp`, `gid`, `type`) VALUES (" . $project['id'] . ", " . $right . ", " . WEB_R_DELETE . ");");
                                    }
                                }
                            } else {
                                parent::db()->execute('insert into `web_project_right`(`wp`, `gid`, `type`) select ' . $project['id'] . ', `gid`, ' . WEB_R_DELETE . ' from `web_project_right` where `wp` = 0;');
                            }
                            if (count($project['wysiwyg']) != 0) {
                                $stylesS = $dbObject->fetchAll("SELECT `tf_id` FROM `wp_wysiwyg_file` WHERE `wp` = " . $project['id'] . ";");
                                foreach ($stylesS as $style) {
                                    if (!in_array($style, $project['wysiwyg'])) {
                                        $dbObject->execute("DELETE FROM `wp_wysiwyg_file` WHERE `wp` = " . $project['id'] . " AND `tf_id` = " . $style['tf_id'] . ";");
                                    }
                                }
                                foreach ($project['wysiwyg'] as $style) {
                                    $row = $dbObject->fetchAll("SELECT `tf_id` FROM `wp_wysiwyg_file` WHERE `wp` = " . $project['id'] . " AND `tf_id` = " . $style . ";");
                                    if (count($row) == 0) {
                                        $dbObject->execute("INSERT INTO `wp_wysiwyg_file`(`wp`, `tf_id`) VALUES (" . $project['id'] . ", " . $style . ");");
                                    }
                                }
                            }
                        }
                        $return .= parent::getSuccess($rb->get('project.saved'));
                    } else {
                        foreach ($errors as $error) {
                            $return .= parent::getError($error);
                        }
                    }

                    if ($_POST['save-project'] == $rb->get('project.save') || count($errors) != 0) {
                        $_POST['edit'] = $rb->get('project.edit');
                        $projectData = $project;
                        $i = 0;
                        foreach ($urls['domain'] as $key => $domainUrl) {
                            if (trim($domainUrl) != '') {
                                $projectData['aliases'][$i]['id'] = ($key == 'new') ? $newUrlId : $key;
                                $projectData['aliases'][$i]['domain_url'] = $domainUrl;
                                $projectData['aliases'][$i]['root_url'] = $urls['root'][$key];
                                $projectData['aliases'][$i]['virtual_url'] = $urls['virtual'][$key];
                                $projectData['aliases'][$i]['http'] = $urls['http'][$key];
                                $projectData['aliases'][$i]['https'] = $urls['https'][$key];
                                $projectData['aliases'][$i]['default'] = $urls['default'] == $key ? 1 : 0;
                                $projectData['aliases'][$i]['enabled'] = $urls['enabled'][$key];
                                $i++;
                            }
                        }
                        $fromSave = true;
                    } else {
                        $_POST['edit'] = '';
                        $_POST['new-project'] = '';
                    }
                } else {
                    $return .= parent::getError($rb->get('project.permdenied'));
                }
            }

            if ($_POST['edit'] == $rb->get('project.edit') || $_POST['new-project'] == $rb->get('project.create')) {
                if ($fromSave) {
                    $project = $projectData;
                    $projectId = $project['id'];
                } elseif ($_POST['edit'] == $rb->get('project.edit')) {
                    $projectId = $_POST['wp'];
                    $project = parent::db()->fetchSingle('SELECT `id`, `name` FROM `web_project` WHERE `id` = ' . $projectId . ';');
                    if ($project != array()) {
                        $aliases = parent::db()->fetchAll('select `id`, `domain_url`, `root_url`, `virtual_url`, `http`, `https`, `default`, `enabled` from `web_url` where `project_id` = ' . $projectId . ' order by `id`;');
                        $project['aliases'] = $aliases;
                    } else {
                        parent::getError($rb->get('project.notexist'));
                    }
                } else {
                    $project = array('id' => 0, 'name' => '', 'url' => '', 'http' => 1, 'https' => 1, 'aliases' => array());
                    $projectId = 0;
                }

                // Ziskat prava ....
                $show = array('read' => true, 'write' => true, 'delete' => false);
                $groupsR = $dbObject->fetchAll("SELECT `gid` FROM `web_project_right` WHERE `wp` = " . $projectId . " AND `type` = " . WEB_R_READ . ";");
                $groupsW = $dbObject->fetchAll("SELECT `gid` FROM `web_project_right` WHERE `wp` = " . $projectId . " AND `type` = " . WEB_R_WRITE . ";");
                $groupsD = $dbObject->fetchAll("SELECT `gid` FROM `web_project_right` WHERE `wp` = " . $projectId . " AND `type` = " . WEB_R_DELETE . ";");
                $allGroups = $dbObject->fetchAll('SELECT `gid`, `name` FROM `group` WHERE (`group`.`gid` IN (' . $loginObject->getGroupsIdsAsString() . ') OR `group`.`parent_gid` IN (' . $loginObject->getGroupsIdsAsString() . ')) ORDER BY `value`;');
                $groupSelectR = '<select id="project-right-edit-groups-r-' . $project['id'] . '" name="project-right-edit-groups-r[]" multiple="multiple" size="5">';
                $groupSelectW = '<select id="project-right-edit-groups-w-' . $project['id'] . '" name="project-right-edit-groups-w[]" multiple="multiple" size="5">';
                $groupSelectD = '<select id="project-right-edit-groups-d-' . $project['id'] . '" name="project-right-edit-groups-d[]" multiple="multiple" size="5">';
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

                // Vybrat styly pro wysiwyg!!!!
                $allStyles = $dbObject->fetchAll('SELECT `id`, `name` FROM `page_file` WHERE `wp` = ' . $projectId . ' AND `type` = ' . WEB_TYPE_CSS . ';');
                $selectedStyles = $dbObject->fetchAll('SELECT `tf_id` FROM `wp_wysiwyg_file` WHERE `wp` = ' . $projectId . ';');
                $styleSelect = '<select id="project-edit-styles-wysiwyg-' . $project['id'] . '" name="project-edit-styles-wysiwyg[]" multiple="multiple" size="5">';
                foreach ($allStyles as $style) {
                    $selected = false;
                    foreach ($selectedStyles as $sStyle) {
                        if ($style['id'] == $sStyle['tf_id']) {
                            $selected = true;
                        }
                    }
                    $styleSelect .= '<option' . (($selected) ? ' selected="selected"' : '') . ' value="' . $style['id'] . '">' . $style['name'] . '</option>';
                }
                $styleSelect .= '</select>';

                // Url adresy
                $urls = '';
                $hasUrl = false;
                foreach ($project['aliases'] as $alias) {
                    $hasUrl = true;
                    $urls .= ''
                    . '<tr>'
                        . '<td>'
                            . '<input class="w300" type="text" name="project-urls-domain[' . $alias['id'] . ']" value="' . $alias['domain_url'] . '" />'
                        . '</td>'
                        . '<td>'
                            . '<input class="w300" type="text" name="project-urls-root[' . $alias['id'] . ']" value="' . $alias['root_url'] . '" />'
                        . '</td>'
                        . '<td>'
                            . '<input  class="w300" name="project-urls-virtual[' . $alias['id'] . ']" value="' . $alias['virtual_url'] . '" />'
                        . '</td>'
                        . '<td>'
                            . '<input type="checkbox" name="project-urls-http[' . $alias['id'] . ']"' . ($alias['http'] ? ' checked="checked"' : '') . ' />'
                        . '</td>'
                        . '<td>'
                            . '<input type="checkbox" name="project-urls-https[' . $alias['id'] . ']"' . ($alias['https'] ? ' checked="checked"' : '') . ' />'
                        . '</td>'
                        . '<td>'
                            . '<input type="radio" name="project-urls-default"' . ($alias['default'] ? ' checked="checked"' : '') . ' value="' . $alias['id'] . '" />'
                        . '</td>'
                        . '<td>'
                            . '<input type="checkbox" name="project-urls-enabled[' . $alias['id'] . ']"' . ($alias['enabled'] ? ' checked="checked"' : '') . ' />'
                        . '</td>'
                    . '</tr>';
                }
                $urls .= ''
                . '<tr>'
                    . '<td>'
                        . '<input class="w300" type="text" name="project-urls-domain[new]" value="" />'
                    . '</td>'
                    . '<td>'
                        . '<input class="w300" type="text" name="project-urls-root[new]" value="" />'
                    . '</td>'
                    . '<td>'
                        . '<input class="w300" type="text" name="project-urls-virtual[new]" value="" />'
                    . '</td>'
                    . '<td>'
                        . '<input type="checkbox" name="project-urls-http[new]" checked="checked" />'
                    . '</td>'
                    . '<td>'
                        . '<input type="checkbox" name="project-urls-https[new]" checked="checked" />'
                    . '</td>'
                    . '<td>'
                        . '<input type="radio" name="project-urls-default" value="new"' . (!$hasUrl ? ' checked="checked"' : '') . ' />'
                    . '</td>'
                    . '<td>'
                        . '<input type="checkbox" name="project-urls-enabled[new]" checked="checked" />'
                    . '</td>'
                . '</tr>';

                // Vytvorit formular ....
                $return .= ''
                . '<form name="project-edit-detail" method="post" action="' . $_SERVER['REQUEST_URI'] . '">'
                    . '<div class="gray-box">'
                        . '<label class="w60" for="project-edit-name' . $project['id'] . '">' . $rb->get('project.name') . ':</label> '
                        . '<input class="w400" type="text" id="project-edit-name' . $project['id'] . '" name="project-name" value="' . $project['name'] . '" />'
                    . '</div>'
                    . '<div class="project-edit-rights">'
                        . (($show['read']) ? ''
                        . '<div class="project-edit-right-read">'
                            . '<label for="project-right-edit-groups-r-' . $project['id'] . '">' . $rb->get('project.read') . ':</label>'
                            . $groupSelectR
                        . '</div>' : '')
                        . (($show['write']) ? ''
                        . '<div class="project-edit-right-write">'
                            . '<label for="project-right-edit-groups-w-' . $project['id'] . '">' . $rb->get('project.write') . ':</label>'
                            . $groupSelectW
                        . '</div>' : '')
                        . (($show['delete']) ? ''
                        . '<div class="project-edit-right-delete">'
                            . '<label for="project-right-edit-groups-d-' . $project['id'] . '">' . $rb->get('project.delete') . ':</label>'
                            . $groupSelectD
                        . '</div>' : '')
                        . '<div class="project-edit-styles-wysiwyg">'
                            . '<label for="project-edit-styles-wysiwyg-' . $project['id'] . '">' . $rb->get('project.wysiwyg') . ':</label> '
                            . $styleSelect
                        . '</div>'
                    . '</div>'
                    . '<div class="clear"></div>'
                    . '<div class="gray-box">'
                        . '<span class="red">' . $rb->get('project.urlstitle') . ':</span> '
                        . '<span class="small-note">' . $rb->get('project.urldeletenote') . '</span>'
                    . '</div>'
                    . '<table class="gray-box">'
                        . '<tr>'
                            . '<th>' . $rb->get('project.domainurl') . ':</th>'
                            . '<th>' . $rb->get('project.rooturl') . ':</th>'
                            . '<th>' . $rb->get('project.virtualurl') . ':</th>'
                            . '<th>' . $rb->get('project.http') . ':</th>'
                            . '<th>' . $rb->get('project.https') . ':</th>'
                            . '<th>' . $rb->get('project.default') . ':</th>'
                            . '<th>' . $rb->get('project.enabled') . ':</th>'
                        . '</tr>'
                        . $urls
                    . '</table>'
                    . '<div class="clear"></div>'
                    . '<div class="gray-box">'
                        . '<input type="hidden" name="project-id" value="' . $project['id'] . '" />'
                        . '<input type="submit" name="save-project" value="' . $rb->get('project.save') . '" /> '
                        . '<input type="submit" name="save-project" value="' . $rb->get('project.saveandclose') . '" /> '
                        . '<input type="submit" name="save-project" value="' . $rb->get('project.close') . '" /> '
                    . '</div>'
                . '</form>';

                return parent::getFrame($rb->get('project.edittile') . ' :: (' . ($project['id'] == 0 ? 'New' : $project['id']) . ') ' . $project['name'], $return, '', true);
            }
        }

        public function setSelectedProject($selProject) {
            $_SESSION['selected-project'] = $selProject;
            return $selProject;
        }

        public function getSelectedProject() {
            return $_SESSION['selected-project'];
        }

    }

?>
