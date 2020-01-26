<?php

    require_once("BaseTagLib.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/ui/BaseGrid.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/ui/BaseForm.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/FullTagParser.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/LocalizationBundle.class.php");

    /**
     * 
     *  Class Sport.
     * 	all about sport	     
     *      
     *  @author     Marek SMM
     *  @timestamp  2012-09-25
     * 
     */
    class Sport extends BaseTagLib {

        private $SubQueriesSQL = '';
        private $ConditionsSQL = '';
        private $UPDisc = 'sport_proj';
        private $UsedFields = array();
        private $ViewPhase = 0;

        public function __construct() {
            self::setTagLibXml("Sport.xml");
            self::setLocalizationBundle("sport");
        }

        /**
         *
         * 	Setups request variables to session.
         *
         */
        public function setFromRequest() {
            // TODO: opravit
            if ($_REQUEST['season-id'] != '') {
                $_SESSION['sport']['season-id'] = $_REQUEST['season-id'];
            }
            if ($_REQUEST['team-id'] != '') {
                $_SESSION['sport']['team-id'] = $_REQUEST['team-id'];
            }
            if ($_REQUEST['player-id'] != '') {
                $_SESSION['sport']['player-id'] = $_REQUEST['player-id'];
            }
            if ($_REQUEST['match-id'] != '') {
                $_SESSION['sport']['match-id'] = $_REQUEST['match-id'];
            }

            echo $_SESSION['player-id'];
        }

        /**
         *
         * 	Show select season form.
         * 		      
         * 	@param		useFrames				use frames in output
         * 	@param		showMsg					show messages in output
         * 	     
         */
        public function selectSeason($useFrames = false, $showMsg = false) {
            global $dbObject;
            $rb = self::rb();
            $return = '';

            if ($_POST['select-season-submit'] == $rb->get('season.select')) {
                $seasonId = $_POST['select-season'];
                if ($seasonId == 0) {
                    self::setSeasonId('-1');
                } else {
                    self::setSeasonId($seasonId);
                }
            }

            if (self::isSetProjectId()) {
                $return .= ''
                . '<div class="select-season">'
                    . '<form name="select-season" method="post" action="' . $_SERVER['REQUEST_URI'] . '" class="auto-submit">'
                        . '<label for="select-season">' . $rb->get('season.selectlab') . ':</label> '
                        . '<select name="select-season" id="select-season">'
                        . '<option value="0">' . $rb->get('season.all') . '</option>'
                            . self::getSeasonsOptions(0, 0, self::getSeasonId())
                        . '</select> '
                        . '<input type="submit" name="select-season-submit" value="' . $rb->get('season.select') . '" />'
                    . '</form>'
                . '</div>';
            } else {
                if ($showMsg != 'false') {
                    $return .= parent::getError($rb->get('project.notset'));
                }
            }

            if ($useFrames == "false") {
                return $return;
            } else {
                return parent::getFrame($rb->get('seasons.title'), $return, "", true);
            }
        }

        /**
         *
         * 	Show select table form.
         * 		      
         * 	@param		useFrames				use frames in output
         * 	@param		showMsg					show messages in output
         * 	     
         */
        public function selectTable($useFrames = false, $showMsg = false) {
            global $dbObject;
            $rb = self::rb();
            $return = '';

            if ($_POST['select-table-submit'] == $rb->get('tables.select')) {
                $tableId = $_POST['select-table'];
                if ($tableId == 0) {
                    self::setTableId('-1');
                } else {
                    self::setTableId($tableId);
                }
            }

            if (self::isSetProjectId()) {
                $return .= ''
                . '<div class="select-table">'
                    . '<form name="select-table" method="post" action="' . $_SERVER['REQUEST_URI'] . '" class="auto-submit">'
                        . '<label for="select-table">' . $rb->get('tables.selectlab') . ':</label> '
                        . '<select name="select-table" id="select-table">'
                        . '<option value="0">' . $rb->get('tables.all') . '</option>'
                            . self::getTablesOptions(self::getTableId())
                        . '</select> '
                        . '<input type="submit" name="select-table-submit" value="' . $rb->get('tables.select') . '" />'
                    . '</form>'
                . '</div>';
            } else {
                if ($showMsg != 'false') {
                    $return .= parent::getError($rb->get('project.notset'));
                }
            }

            if ($useFrames == "false") {
                return $return;
            } else {
                return parent::getFrame($rb->get('tables.title'), $return, "", true);
            }
        }

        /**
         *
         * 	Show select team form.
         * 		      
         * 	@param		useFrames				use frames in output
         * 	@param		showMsg					show messages in output
         * 	     
         */
        public function selectTeam($useFrames = false, $showMsg = false) {
            global $dbObject;
            $rb = self::rb();
            $return = '';

            if ($_POST['select-team-submit'] == $rb->get('team.select')) {
                $teamId = $_POST['select-team'];
                if ($teamId == 0) {
                    self::setTeamId('-1');
                } else {
                    self::setTeamId($teamId);
                }
            }

            if (self::isSetProjectId()) {
                $return .= ''
                . '<div class="select-team">'
                    . '<form name="select-team" method="post" action="' . $_SERVER['REQUEST_URI'] . '" class="auto-submit">'
                        . '<label for="select-team">' . $rb->get('team.selectlab') . ':</label> '
                        . '<select name="select-team" id="select-team">'
                        . '<option value="0">' . $rb->get('team.all') . '</option>'
                            . self::getTeamsOptions(self::getTeamId())
                        . '</select> '
                        . '<input type="submit" name="select-team-submit" value="' . $rb->get('team.select') . '" />'
                    . '</form>'
                . '</div>';
            } else {
                if ($showMsg != 'false') {
                    $return .= parent::getError($rb->get('project.notset'));
                }
            }

            if ($useFrames == "false") {
                return $return;
            } else {
                return parent::getFrame($rb->get('teams.title'), $return, "", true);
            }
        }

        /**
         *
         * 	Show select project form.
         *
         * 	@param		useFrames				use frames in output
         * 	@param		showMsg					show messages in output
         *
         */
        public function selectProject($useFrames = false, $showMsg = false) {
            $rb = self::rb();
            $return = '';

            if ($_POST['select-project-submit'] == $rb->get('project.select')) {
                $projectId = $_POST['select-project'];
                if ($projectId != '') {
                    self::setProjectId($projectId);
                }
            }

            $projects = self::getProjectsOptions(self::getProjectId(), WEB_R_WRITE);

            if ($projects['data'] != array()) {
                if (count($projects['data']) == 1) {
                    self::setProjectId($projects['data'][0]['id']);
                }

                $return .= ''
                . '<div class="gray-box-float">'
                    . '<form name="select-project" method="post" action="' . $_SERVER['REQUEST_URI'] . '"' . (count($projects['data']) > 1 ? ' class="auto-submit"' : '') . '>'
                        . '<label for="select-project">' . $rb->get('project.selectlab') . ':</label> '
                        . '<select name="select-project" id="select-project" class="w160">'
                            . $projects['html']
                        . '</select> '
                        . '<input type="submit" name="select-project-submit" value="' . $rb->get('project.select') . '" />'
                    . '</form>'
                . '</div>';
            } else {
                if ($showMsg != 'false') {
                    $return .= parent::getWarning($rb->get('projects.nodata'));
                }
            }

            if ($useFrames == "false") {
                return $return;
            } else {
                return parent::getFrame($rb->get('projects.title'), $return, "", true);
            }
        }

        /**
         *
         * 	List of projects
         *
         */
        public function showEditProjects($pageId = false, $useFrames = false, $showMsg = false) {
            $rb = self::rb();
            $retrun = '';

            if ($pageId != false) {
                $actionUrl = $webObject->composeUrl($pageId);
            } else {
                $actionUrl = $_SERVER['REQUEST_URI'];
            }

            if ($_POST['project-delete'] == $rb->get('projects.delete')) {
                $projectId = $_POST['project-id'];

                if (UniversalPermission::checkUserPermissions($this->UPDisc, $projectId, WEB_R_DELETE)) {
                    parent::db()->execute(parent::query()->get('deleteProject', array('id' => $projectId), 'sport'));
                    UniversalPermission::deletePermissions($this->UPDisc, $projectId);
                    $return .= parent::getSuccess($rb->get('projects.delete.success'));
                } else {
                    $return .= parent::getError($rb->get('projects.error.permissionsdenied'));
                }
            }

            // test prav!!
            $projects = parent::db()->fetchAll(parent::query()->get('selectProjects', array(), 'sport'));
            if (count($projects) > 0) {
                $return .= ''
                . '<table class="standart">'
                    . '<tr>'
                        . '<th>' . $rb->get('projects.id') . ':</th>'
                        . '<th>' . $rb->get('projects.name') . ':</th>'
                        . '<th>' . $rb->get('projects.url') . ':</th>'
                        . '<th></th>'
                    . '</tr>';

                for ($i = 0; $i < count($projects); $i++) {
                    $can = UniversalPermission::checkUserPermissions($this->UPDisc, $projects[$i]['id'], WEB_R_DELETE);
                    $return .= ''
                        . '<tr class="' . ((($i % 2) == 1) ? 'even' : 'idle') . '">'
                            . '<td>' . $projects[$i]['id'] . '</td>'
                            . '<td>' . $projects[$i]['name'] . '</td>'
                            . '<td>' . $projects[$i]['url'] . '</td>'
                            . '<td>'
                            . ($can ? ''
                                    . '<form name="projects-edit" action="' . $actionUrl . '" method="post">'
                                    . '<input type="hidden" name="project-id" value="' . $projects[$i]['id'] . '" />'
                                    . '<input type="hidden" name="project-edit" value="' . $rb->get('projects.edit') . '" />'
                                    . '<input type="image" src="~/images/page_edi.png" name="project-edit" value="' . $rb->get('projects.edit') . '" title="' . $rb->get('projects.edit.title') . '" />'
                                    . '</form> ' : '')
                            . ($can ? ''
                                    . '<form name="projects-delete" action="" method="post">'
                                    . '<input type="hidden" name="project-id" value="' . $projects[$i]['id'] . '" />'
                                    . '<input type="hidden" name="project-delete" value="' . $rb->get('projects.delete') . '" />'
                                    . '<input class="confirm" type="image" src="~/images/page_del.png" name="project-delete" value="' . $rb->get('projects.delete') . '" title="' . $rb->get('projects.delete.title') . ', id ' . $projects[$i]['id'] . '" />'
                                    . '</form>' : '')
                            . '</td>'
                        . '</tr>';
                }

                $return .= ''
                . '</table>';
            } else {
                $return .= parent::getWarning($rb->get('projects.nodata'));
            }

            $return .= ''
                    . '<hr />'
                    . '<form name="projects-create" action="' . $ationUrl . '" method="post">'
                    . '<input type="submit" name="project-create" value="' . $rb->get('projects.create') . '" />'
                    . '</form>';

            if ($useFrames == "false") {
                return $return;
            } else {
                return parent::getFrame($rb->get('projects.title'), $return, "", true);
            }
        }

        /**
         *
         * 	Edit project
         *
         */
        public function showEditProjectForm($useFrames = false, $showMsg = false) {
            global $dbObject;
            $return = '';
            $rb = self::rb();
            $return = '';

            if ($_POST['project-submit'] == $rb->get('project.submit')) {
                $projectId = $_POST['project-id'];
                $name = $_POST['project-name'];
                $url = parent::convertToUrlValid($_POST['project-url']);


                if ($projectId == '') {
                    $usedName = parent::db()->fetchAll(parent::query()->get('selectProjectByName', array('name' => $name), 'sport'));
                    if (count($usedName) > 0) {
                        $return .= parent::getError($rb->get('project.nameused'));
                    } else {
                        parent::db()->execute(parent::query()->get('insertProject', array('name' => $name, 'url' => $url), 'sport'));
                        $maxId = parent::db()->getLastId();

                        // pridat prava
                        UniversalPermission::savePermissionsFromForm($this->UPDisc, $maxId, WEB_R_READ);
                        UniversalPermission::savePermissionsFromForm($this->UPDisc, $maxId, WEB_R_WRITE);
                        UniversalPermission::savePermissionsFromForm($this->UPDisc, $maxId, WEB_R_DELETE);
                    }
                } else {
                    if (UniversalPermission::checkUserPermissions($this->UPDisc, $projectId, WEB_R_WRITE)) {
                        parent::db()->execute(parent::query()->get('updateProjectById', array('name' => $name, 'url' => $url, 'id' => $projectId), 'sport'));

                        // upravit prava
                        UniversalPermission::savePermissionsFromForm($this->UPDisc, $projectId, WEB_R_READ);
                        UniversalPermission::savePermissionsFromForm($this->UPDisc, $projectId, WEB_R_WRITE);
                        UniversalPermission::savePermissionsFromForm($this->UPDisc, $projectId, WEB_R_DELETE);
                    } else {
                        $return .= parent::getError($rb->get('projects.error.permissionsdenied'));
                    }
                }
            }

            if ($_POST['project-create'] == $rb->get('projects.create') || $_POST['project-edit'] == $rb->get('projects.edit')) {
                $projectId = $_POST['project-id'];
                if ($projectId == '' || UniversalPermission::checkUserPermissions($this->UPDisc, $projectId, WEB_R_DELETE)) {
                    $project = array();
                    if ($projectId != '') {
                        $project = parent::db()->fetchAll(parent::query()->get('selectProjectById', array('id' => $projectId), 'sport'));
                        $project = $project[0];
                    }
                    $return .= ''
                            . '<form name="project-edit" method="post" action="' . $_SERVER['REQUEST_URI'] . '">'
                            . '<div class="gray-box">'
                            . '<label for="project-name" class="w160">' . $rb->get('projects.name') . '</label>'
                            . '<input class="w200" type="text" name="project-name" id="project-name" value="' . $project['name'] . '" />'
                            . '</div>'
                            . '<div class="gray-box">'
                            . '<label for="project-url" class="w160">' . $rb->get('projects.url') . '</label>'
                            . '<input class="w200" type="text" name="project-url" id="project-url" value="' . $project['url'] . '" />'
                            . '</div>'
                            . '<div class="gray-box gray-box-float">'
                            . '<label for="universal-permissions-sport_proj-r" class="block">' . $rb->get('project.permission.r') . '</label>'
                            . UniversalPermission::showPermissionsFormPart($this->UPDisc, ($projectId != '' ? $projectId : 'new'), parent::login()->getGroups(), WEB_R_READ)
                            . '</div>'
                            . '<div class="gray-box gray-box-float">'
                            . '<label for="universal-permissions-sport_proj-w" class="block">' . $rb->get('project.permission.w') . '</label>'
                            . UniversalPermission::showPermissionsFormPart($this->UPDisc, ($projectId != '' ? $projectId : 'new'), parent::login()->getGroups(), WEB_R_WRITE)
                            . '</div>'
                            . '<div class="gray-box gray-box-float">'
                            . '<label for="universal-permissions-sport_proj-d" class="block">' . $rb->get('project.permission.d') . '</label>'
                            . UniversalPermission::showPermissionsFormPart($this->UPDisc, ($projectId != '' ? $projectId : 'new'), parent::login()->getGroups(), WEB_R_DELETE)
                            . '</div>'
                            . '<div class="clear"></div>'
                            . '<div class="gray-box">'
                            . '<input type="hidden" name="project-id" value="' . $project['id'] . '" />'
                            . '<input type="submit" name="project-submit" value="' . $rb->get('project.submit') . '" />'
                            . '</div>'
                            . '</form>';

                    if ($useFrames == "false") {
                        return $return;
                    } else {
                        return parent::getFrame($rb->get('project.title') . ' :: (' . ($projectId == '' ? 'new' : $projectId) . ')', $return, "", true);
                    }
                }
            }
        }

        /**
         *
         * 	List of seasons for editing.
         * 	C tag.
         *
         * 	@param		pageId					next page id
         * 	@param		useFrames				use frames in output
         * 	@param		showMsg					show messages in output
         *
         */
        public function showEditSeasons($pageId = false, $useFrames = false, $showMsg = false) {
            global $dbObject;
            global $webObject;
            $rb = self::rb();
            $retrun = '';

            if (self::isSetProjectId()) {
                if ($pageId != false) {
                    $actionUrl = $webObject->composeUrl($pageId);
                } else {
                    $actionUrl = $_SERVER['REQUEST_URI'];
                }
                if ($_POST['season-delete'] == $rb->get('seasons.delete')) {
                    if (UniversalPermission::checkUserPermissions($this->UPDisc, self::getProjectId(), WEB_R_WRITE)) {
                        $seasonId = $_POST['season-id'];
                        $dbObject->execute(parent::query()->get('deleteSeason', array('id' => $seasonId), 'sport'));
                    } else {
                        $return .= parent::getError($rb->get('projects.error.permissionsdenied'));
                    }
                }

                $seasons = $dbObject->fetchAll(parent::query()->get('selectSeasonsByProjectId', array('projectId' => self::getProjectId()), 'sport'));
                if (count($seasons) > 0) {
                    $return .= ''
                            . '<table class="seasons-list standart">'
                            . '<tr>'
                            . '<th class="seasons-list-id">' . $rb->get('seasons.id') . ':</th>'
                            . '<th class="seasons-list-start">' . $rb->get('seasons.startyear') . ':</th>'
                            . '<th class="seasons-list-end">' . $rb->get('seasons.endyear') . ':</th>'
                            . '<th class="seasons-list-edit">' . $rb->get('seasons.edit') . ':</th>'
                            . '</tr>';
                    for ($i = 0; $i < count($seasons); $i++) {
                        $return .= ''
                                . '<tr class="' . ((($i % 2) == 0 ) ? 'idle' : 'even') . '">'
                                . '<td class="seasons-list-id">' . $seasons[$i]['id'] . '</td>'
                                . '<td class="seasons-list-start">' . $seasons[$i]['start_year'] . '</td>'
                                . '<td class="seasons-list-end">' . $seasons[$i]['end_year'] . '</td>'
                                . '<td class="seasons-list-edit">'
                                . '<form name="seasons-edit" method="post" action="' . $actionUrl . '">'
                                . '<input type="hidden" name="season-id" value="' . $seasons[$i]['id'] . '" />'
                                . '<input type="hidden" name="season-edit" value="' . $rb->get('seasons.edit') . '" />'
                                . '<input type="image" src="~/images/page_edi.png" name="season-edit" value="' . $rb->get('seasons.edit') . '" title="' . $rb->get('seasons.editcap') . '" />'
                                . '</form> '
                                . '<form name="seasons-delete" method="post">'
                                . '<input type="hidden" name="season-id" value="' . $seasons[$i]['id'] . '" />'
                                . '<input type="hidden" name="season-delete" value="' . $rb->get('seasons.delete') . '" />'
                                . '<input class="confirm" type="image" src="~/images/page_del.png" name="season-delete" value="' . $rb->get('seasons.delete') . '" title="' . $rb->get('seasons.deletecap') . ', id (' . $seasons[$i]['id'] . ')" />'
                                . '</form>'
                                . '</td>'
                                . '</tr>';
                    }

                    $return .=''
                            . '</table>';
                } else {
                    $return .= '<h4 class="warning">' . $rb->get('seasons.warning.nodata') . '</h4>';
                }
                $return .= ''
                        . '<hr />'
                        . '<form name="season-new" method="post" action="' . $actionUrl . '">'
                        . '<input type="submit" name="season-new" value="' . $rb->get('seasons.new') . '" title="' . $rb->get('seasons.newcap') . '" />'
                        . '</form>';
            } else {
                $return .= parent::getError($rb->get('project.notset'));
            }

            if ($useFrames == "false") {
                return $return;
            } else {
                return parent::getFrame($rb->get('seasons.title'), $return, "", true);
            }
        }

        /**
         *
         * 	Edit season.
         * 	C tag.
         *
         * 	@param		useFrames				use frames in output
         * 	@param		showMsg					show messages in output
         *
         */
        public function showEditSeasonForm($useFrames = false, $showMsg = false) {
            global $dbObject;
            $return = '';
            $rb = self::rb();
            $season = array();

            if ($_POST['season-save'] == $rb->get('seasons.form.save')) {
                if (UniversalPermission::checkUserPermissions($this->UPDisc, self::getProjectId(), WEB_R_WRITE)) {
                    $seasonId = $_POST['season-id'];
                    $seasonStart = $_POST['season-edit-start'];
                    $seasonEnd = $_POST['season-edit-end'];
                    if ($seasonStart < $seasonEnd) {
                        if ($seasonId != '') {
                            $season = $dbObject->fetchAll(parent::query()->get('selectSeasonById', array('id' => $seasonId), 'sport'));
                        } else {
                            $season = array();
                        }

                        if (count($season) > 0) {
                            $dbObject->execute(parent::query()->get('updateSeasonById', array('startYear' => $seasonStart, 'endYear' => $seasonEnd, 'id' => $seasonId), 'sport'));
                        } else {
                            $dbObject->execute(parent::query()->get('insertSeason', array('startYear' => $seasonStart, 'endYear' => $seasonEnd, 'projectId' => self::getProjectId()), 'sport'), true);
                        }
                    } else {
                        $return .= parent::getError($rb->get('season.error.startgtend'));
                        $season['start_year'] = $seasonStart;
                        $season['end_year'] = $seasonEnd;
                        $season['id'] = $seasonId;
                        $_POST['season-new'] = $rb->get('seasons.new');
                    }
                } else {
                    $return .= parent::getError($rb->get('projects.error.permissionsdenied'));
                }
            }

            if ($_POST['season-new'] == $rb->get('seasons.new') || $_POST['season-edit'] == $rb->get('seasons.edit')) {
                if (self::isSetProjectId()) {
                    if ($_POST['season-edit'] == $rb->get('seasons.edit')) {
                        $seasonId = $_POST['season-id'];
                        $season = $dbObject->fetchSingle(parent::query()->get('selectSeasonById', array('id' => $seasonId), 'sport'));
                    }
                    $return .= ''
                            . '<div class="season-edit-form">'
                            . '<form name="season-edit-form" method="post" action="' . $_SERVER['REQUEST_URI'] . '">'
                            . '<div class="season-edit-start">'
                            . '<label for="season-edit-start">' . $rb->get('seasons.form.startyear') . ':</label> '
                            . '<input type="text" name="season-edit-start" id="season-edit-start" value="' . $season['start_year'] . '" />'
                            . '</div>'
                            . '<div class="season-edit-end">'
                            . '<label for="season-edit-end">' . $rb->get('seasons.form.endyear') . ':</label> '
                            . '<input type="text" name="season-edit-end" id="season-edit-end" value="' . $season['end_year'] . '" />'
                            . '</div>'
                            . '<div class="season-edit-submit">'
                            . '<input type="hidden" name="season-id" value="' . $season['id'] . '" />'
                            . '<input type="submit" name="season-save" value="' . $rb->get('seasons.form.save') . '" />'
                            . '</div>'
                            . '</form>'
                            . '</div>'
                            . '<div class="clear"></div>';
                } else {
                    $return .= parent::getError($rb->get('project.notset'));
                }

                if ($useFrames == "false") {
                    return $return;
                } else {
                    return parent::getFrame($rb->get('seasons.form.title'), $return, "", true);
                }
            }
        }

        /**
         *
         * 	List of teams for editing.
         * 	C tag.
         *
         * 	@param		pageId					next page id
         * 	@param		useFrames				use frames in output
         * 	@param		showMsg					show messages in output
         *
         */
        public function showEditTeams($pageId = false, $useFrames = false, $showMsg = false) {
            global $dbObject;
            global $webObject;
            $rb = self::rb();
            $retrun = '';

            if (self::isSetProjectId()) {
                if ($pageId != false) {
                    $actionUrl = $webObject->composeUrl($pageId);
                } else {
                    $actionUrl = $_SERVER['REQUEST_URI'];
                }

                if ($_POST['team-add-season-submit'] == $rb->get('teams.addseasonsubmit')) {
                    if (UniversalPermission::checkUserPermissions($this->UPDisc, self::getProjectId(), WEB_R_WRITE)) {
                        $addteam = $_POST['teams-add-season-team'];
                        $addseason = $_POST['teams-add-season-season'];

                        $retsql = $dbObject->fetchAll('SELECT `id` FROM `w_sport_team` WHERE `id` = ' . $addteam . ' AND `season` = ' . $addseason . ';');
                        if (count($retsql) == 0) {
                            //parent::db()->setMockMode(true);
                            $reteam = $dbObject->fetchAll('SELECT `name`, `logo`, `url`, `season` FROM `w_sport_team` WHERE `id` = ' . $addteam . ' ORDER BY `season` desc;');
                            $dbObject->execute('INSERT INTO `w_sport_team`(`id`, `name`, `logo`, `url`, `season`, `project_id`) VALUES(' . $addteam . ', "' . $reteam[0]['name'] . '", "' . $reteam[0]['logo'] . '", "' . $reteam[0]['url'] . '", ' . $addseason . ', ' . self::getProjectId() . ');');
                            $tables = parent::db()->fetchAll('select `table_id` from `w_sport_table` where `team` = ' . $addteam . ' and `season` = ' . $reteam[0]['season'] . ';');
                            foreach ($tables as $table) {
                                parent::db()->execute('INSERT INTO `w_sport_table`(`team`, `matches`, `wins`, `draws`, `loses`, `s_score`, `r_score`, `points`, `season`, `positionfix`, `project_id`, `table_id`) VALUES(' . $addteam . ', 0, 0, 0, 0, 0, 0, 0, ' . $addseason . ', 0, ' . self::getProjectId() . ', ' . $table['table_id'] . ');');
                            }
                            //parent::db()->setMockMode(false);
                            //$dbObject->execute('INSERT INTO `w_sport_table`(`team`, `matches`, `wins`, `draws`, `loses`, `s_score`, `r_score`, `points`, `season`, `project_id`) VALUES('.$addteam.', 0, 0, 0, 0, 0, 0, 0, '.$addseason.', '.self::getProjectId().');');
                        } else {
                            if ($showMsg != 'false') {
                                $return .= '<h4 class="error">' . $rb->get('teams.addseasonerr') . '</h4>';
                            }
                        }
                    } else {
                        $return .= parent::getError($rb->get('projects.error.permissionsdenied'));
                    }
                }

                if ($_POST['team-delete'] == $rb->get('teams.delete')) {
                    if (UniversalPermission::checkUserPermissions($this->UPDisc, self::getProjectId(), WEB_R_WRITE)) {
                        $teamId = $_POST['team-id'];
                        $seasonId = $_POST['season-id'];
                        $dbObject->execute('DELETE FROM `w_sport_team` WHERE `id` = ' . $teamId . ' AND `season` = ' . $seasonId . ';');
                        $dbObject->execute('DELETE FROM `w_sport_table` WHERE `team` = ' . $teamId . ' AND `season` = ' . $seasonId . ';');
                    } else {
                        $return .= parent::getError($rb->get('projects.error.permissionsdenied'));
                    }
                }

                $tablesql = '';
                $tablewhere = '';
                if (self::getTableId() != '-1') {
                    $tablesql = ' join `w_sport_table` on `w_sport_team`.`id` = `w_sport_table`.`team`';
                    $tablewhere = ' and `w_sport_table`.`table_id` = ' . self::getTableId();
                }

                if (self::getSeasonId() != '-1') {
                    if (self::getTeamId() != '-1') {
                        $teams = $dbObject->fetchAll('SELECT distinct `w_sport_team`.`id`, `w_sport_team`.`name`, `w_sport_team`.`season`, `w_sport_team`.`logo`, `w_sport_season`.`start_year`, `w_sport_season`.`end_year` FROM `w_sport_team` LEFT JOIN `w_sport_season` ON `w_sport_team`.`season` = `w_sport_season`.`id`' . $tablesql . ' WHERE `w_sport_team`.`season` = ' . self::getSeasonId() . ' AND `w_sport_team`.`id` = ' . self::getTeamId() . ' and `w_sport_team`.`project_id` = ' . self::getProjectId() . $tablewhere . ' ORDER BY `w_sport_season`.`start_year` DESC;');
                    } else {
                        $teams = $dbObject->fetchAll('SELECT distinct `w_sport_team`.`id`, `w_sport_team`.`name`, `w_sport_team`.`season`, `w_sport_team`.`logo`, `w_sport_season`.`start_year`, `w_sport_season`.`end_year` FROM `w_sport_team` LEFT JOIN `w_sport_season` ON `w_sport_team`.`season` = `w_sport_season`.`id`' . $tablesql . '  WHERE `w_sport_team`.`season` = ' . self::getSeasonId() . ' and `w_sport_team`.`project_id` = ' . self::getProjectId() . $tablewhere . ' ORDER BY `w_sport_season`.`start_year` DESC;');
                    }
                } else {
                    if (self::getTeamId() != '-1') {
                        $teams = $dbObject->fetchAll('SELECT distinct `w_sport_team`.`id`, `w_sport_team`.`name`, `w_sport_team`.`season`, `w_sport_team`.`logo`, `w_sport_season`.`start_year`, `w_sport_season`.`end_year` FROM `w_sport_team` LEFT JOIN `w_sport_season` ON `w_sport_team`.`season` = `w_sport_season`.`id`' . $tablesql . '  WHERE `w_sport_team`.`id` = ' . self::getTeamId() . ' and `w_sport_team`.`project_id` = ' . self::getProjectId() . $tablewhere . ' ORDER BY `w_sport_season`.`start_year` DESC;');
                    } else {
                        $teams = $dbObject->fetchAll('SELECT distinct `w_sport_team`.`id`, `w_sport_team`.`name`, `w_sport_team`.`season`, `w_sport_team`.`logo`, `w_sport_season`.`start_year`, `w_sport_season`.`end_year` FROM `w_sport_team` LEFT JOIN `w_sport_season` ON `w_sport_team`.`season` = `w_sport_season`.`id`' . $tablesql . '  where `w_sport_team`.`project_id` = ' . self::getProjectId() . $tablewhere . ' ORDER BY `w_sport_season`.`start_year` DESC;');
                    }
                }
                if (count($teams) > 0) {
                    $return .= ''
                            //.parent::getWarning('Do formuláře pro editaci týmu přidat checkboxy pro výběr v jakých tabulkách bude zařazen!')
                            . '<table class="teams-list standart">'
                            . '<tr>'
                            . '<th class="teams-list-id">' . $rb->get('teams.id') . ':</th>'
                            . '<th class="teams-list-name">' . $rb->get('teams.name') . ':</th>'
                            . '<th class="teams-list-logo">' . $rb->get('teams.logo') . ':</th>'
                            . '<th class="teams-list-season">' . $rb->get('teams.season') . ':</th>'
                            . '<th class="teams-list-edit">' . $rb->get('teams.edit') . ':</th>'
                            . '</tr>';
                    for ($i = 0; $i < count($teams); $i++) {
                        $teams[$i]['logo'] = str_replace('~', '&#126', $teams[$i]['logo']);
                        $return .= ''
                                . '<tr class="' . ((($i % 2) == 0 ) ? 'idle' : 'even') . '">'
                                . '<td class="teams-list-id">' . $teams[$i]['id'] . '</td>'
                                . '<td class="teams-list-name">' . $teams[$i]['name'] . '</td>'
                                . '<td class="teams-list-logo">' . $teams[$i]['logo'] . '</td>'
                                . '<td class="teams-list-season">' . $teams[$i]['start_year'] . ' - ' . $teams[$i]['end_year'] . '</td>'
                                . '<td class="teams-list-edit">'
                                . '<form name="teams-edit" method="post" action="' . $actionUrl . '">'
                                . '<input type="hidden" name="team-id" value="' . $teams[$i]['id'] . '" />'
                                . '<input type="hidden" name="season-id" value="' . $teams[$i]['season'] . '" />'
                                . '<input type="hidden" name="team-edit" value="' . $rb->get('teams.edit') . '" />'
                                . '<input type="image" src="~/images/page_edi.png" name="team-edit" value="' . $rb->get('teams.edit') . '" title="' . $rb->get('teams.editcap') . '" />'
                                . '</form> '
                                . '<form name="teams-delete" method="post">'
                                . '<input type="hidden" name="team-id" value="' . $teams[$i]['id'] . '" />'
                                . '<input type="hidden" name="season-id" value="' . $teams[$i]['season'] . '" />'
                                . '<input type="hidden" name="team-delete" value="' . $rb->get('teams.delete') . '" />'
                                . '<input class="confirm" type="image" src="~/images/page_del.png" name="team-delete" value="' . $rb->get('teams.delete') . '" title="' . $rb->get('teams.deletecap') . ', id (' . $teams[$i]['id'] . '), season (' . $teams[$i]['start_year'] . ' - ' . $teams[$i]['end_year'] . ')" />'
                                . '</form>'
                                . '</td>'
                                . '</tr>';
                    }

                    $return .=''
                            . '</table>';
                } else {
                    $return .= '<h4 class="warning">' . $rb->get('teams.warning.nodata') . '</h4>';
                }
                $return .= ''
                        . '<hr />'
                        . '<form name="team-new" method="post" action="' . $actionUrl . '">'
                        . '<div class="gray-box">'
                        . '<input type="submit" name="team-new" value="' . $rb->get('teams.new') . '" title="' . $rb->get('teams.newcap') . '" /> '
                        . ' | '
                        . '<select name="teams-add-season-team">'
                        . self::getTeamsOptions()
                        . '</select> '
                        . '<select name="teams-add-season-season">'
                        . self::getSeasonsOptions()
                        . '</select> '
                        . '<input type="submit" name="team-add-season-submit" value="' . $rb->get('teams.addseasonsubmit') . '" title="' . $rb->get('teams.addseasonsubmitcap') . '" />'
                        //.'<span class="red">*</span> <span class="small-font">'.$rb->get('teams.copynotenotaddtotable').'</span> '
                        . '</div>'
                        . '</form>';
            } else {
                $return .= parent::getError($rb->get('project.notset'));
            }

            if ($useFrames == "false") {
                return $return;
            } else {
                return parent::getFrame($rb->get('teams.title'), $return, "", true);
            }
        }

        /**
         *
         * 	Edit team.
         * 	C tag.
         *
         * 	@param		useFrames				use frames in output
         * 	@param		showMsg					show messages in output
         *
         */
        public function showEditTeamForm($useFrames = false, $showMsg = false) {
            global $dbObject;
            $rb = self::rb();
            $return = '';
            $team = array();

            if ($_POST['team-save'] == $rb->get('teams.form.save')) {
                if (UniversalPermission::checkUserPermissions($this->UPDisc, self::getProjectId(), WEB_R_WRITE)) {
                    $seasonId = $_POST['season-id'];
                    $teamId = $_POST['team-id'];
                    $name = trim($_POST['team-edit-name']);
                    $logo = $_POST['team-edit-logo'];
                    $season = $_POST['team-edit-season'];
                    $tables = $_POST['tables-list'];
                    $url = trim(parent::convertToUrlValid($_POST['team-edit-url']));
                    if ($url == '') {
                        $url = strtolower(parent::convertToUrlValid($name));
                    }
                    $urls = parent::db()->fetchAll('select `url` from `w_sport_team` where `url` = "' . $url . '" and `project_id` = ' . self::getProjectId() . ($teamId != '' ? ' and `id` != ' . $teamId : '') . ';');
                    if ($name != '' && count($urls) == 0) {
                        //parent::db()->setMockMode(true);
                        if ($teamId == '') {
                            $teamId = 0;
                        }
                        if ($seasonId == '') {
                            $seasonId = 0;
                        }

                        $seasql = $dbObject->fetchAll('SELECT `id` FROM `w_sport_team` WHERE `id` = ' . $teamId . ' AND `season` = ' . $seasonId . ';');
                        if (count($seasql) > 0) {
                            $dbObject->execute('UPDATE `w_sport_team` SET `name` = "' . $name . '", `logo` = "' . $logo . '", `url` = "' . $url . '", `season` = ' . $season . ' WHERE `id` = ' . $teamId . ' AND `season` = ' . $seasonId . ';');
                        } else {
                            $dbObject->execute('INSERT INTO `w_sport_team`(`name`, `logo`, `url`, `season`, `project_id`) VALUES ("' . $name . '", "' . $logo . '", "' . $url . '", ' . $season . ', ' . self::getProjectId() . ');');
                            $tea = $dbObject->fetchAll('SELECT MAX(`id`) AS `id` FROM `w_sport_team`;');
                            $teamId = $tea[0]['id'];
                            //$dbObject->execute('INSERT INTO `w_sport_table`(`team`, `matches`, `wins`, `draws`, `loses`, `s_score`, `r_score`, `points`, `season`, `project_id`) VALUES('.$tea[0]['id'].', 0, 0, 0, 0, 0, 0, 0, '.$season.', '.self::getProjectId().');');
                        }

                        $olduint = parent::db()->fetchAll('select `table_id` from `w_sport_table` where `team` = ' . $teamId . ' and `season` = ' . $season . ';');
                        foreach ($olduint as $oldid) {
                            if (!in_array($oldid['table_id'], $tables)) {
                                parent::db()->execute('delete from `w_sport_table` where `team` = ' . $teamId . ' and `season` = ' . $season . ' and `table_id` = ' . $oldid['table_id'] . ';');
                            } else {
                                foreach ($tables as $key => $newid) {
                                    if ($oldid['table_id'] == $newid) {
                                        unset($tables[$key]);
                                    }
                                }
                            }
                        }

                        if (count($tables) > 0) {
                            $values = '';
                            foreach ($tables as $tbl) {
                                if ($values != '') {
                                    $values .= ', ';
                                }
                                $values .= '(' . $teamId . ', 0, 0, 0, 0, 0, 0, 0, ' . $season . ', 0, ' . $tbl . ', ' . self::getProjectId() . ')';
                            }
                            parent::db()->execute('insert into `w_sport_table`(`team`, `matches`, `wins`, `draws`, `loses`, `s_score`, `r_score`, `points`, `season`, `positionfix`, `table_id`, `project_id`) values ' . $values);
                        }

                        //parent::db()->setMockMode(false);
                    } else {
                        $return .= parent::getError($rb->get('teams.error.nameemptyurlunique'));
                        $team['name'] = $name;
                        $team['logo'] = $logo;
                        $team['url'] = $url;
                        $seasonId = $season;
                        $team['season'] = $season;
                        $team['id'] = $teamId;
                        $_POST['team-new'] = $rb->get('teams.new');
                    }
                } else {
                    $return .= parent::getError($rb->get('projects.error.permissionsdenied'));
                }
            }

            if ($_POST['team-new'] == $rb->get('teams.new') || $_POST['team-edit'] == $rb->get('teams.edit')) {
                if (self::isSetProjectId()) {
                    if ($_POST['team-edit'] == $rb->get('teams.edit')) {
                        $seasonId = $_POST['season-id'];
                        $teamId = $_POST['team-id'];
                        $team = $dbObject->fetchAll('SELECT `id`, `name`, `url`, `logo`, `season` FROM `w_sport_team` WHERE `season` = ' . $seasonId . ' AND `id` = ' . $teamId . ';');
                        $team = $team[0];
                        $team['logo'] = str_replace('~', '&#126', $team['logo']);
                    }

                    $seasons = self::getSeasonsOptions($teamId, $seasonId, $team['season']);

                    $usedTables = array();
                    if ($team['id'] != '') {
                        $usedTables = parent::db()->fetchAll('select `table_id` from `w_sport_table` where `team` = ' . $team['id'] . ' and `season` = ' . $team['season'] . ';');
                    }

                    $tables = '';
                    $tbls = parent::db()->fetchAll('select `id`, `name` from `w_sport_tables` where `project_id` = ' . self::getProjectId() . ' order by `id`;');
                    $i = 1;
                    $tables .= '<div class="ml160">';
                    foreach ($tbls as $table) {
                        $in = false;
                        foreach ($usedTables as $utb) {
                            if ($table['id'] == $utb['table_id']) {
                                $in = true;
                                break;
                            }
                        }

                        $tables .= ''
                                . '<input type="checkbox" id="tables-list-' . $i . '" name="tables-list[]" value="' . $table['id'] . '"' . ($in ? ' checked="checked"' : '') . ' />'
                                . '<label for="tables-list-' . $i . '">' . $table['name'] . '</label>'
                                . '<br />';
                        $i++;
                    }
                    $tables .= '</div>';

                    $return .= ''
                            . '<div class="team-edit-form">'
                            . '<form name="team-edit-form" method="post" action="' . $_SERVER['REQUEST_URI'] . '">'
                            . '<div class="gray-box">'
                            . '<label class="w160" for="team-edit-name">' . $rb->get('teams.name') . ':</label> '
                            . '<input class="w200" type="text" name="team-edit-name" id="team-edit-name" value="' . $team['name'] . '" />'
                            . '</div>'
                            . '<div class="gray-box">'
                            . '<label class="w160" for="team-edit-logo">' . $rb->get('teams.logo') . ':</label> '
                            . '<input class="w200" type="text" name="team-edit-logo" id="team-edit-logo" value="' . $team['logo'] . '" />'
                            . '</div>'
                            . '<div class="gray-box">'
                            . '<label class="w160" for="team-edit-url">' . $rb->get('teams.url') . ':</label> '
                            . '<input class="w200" type="text" name="team-edit-url" id="team-edit-url" value="' . $team['url'] . '" />'
                            . '</div>'
                            . '<div class="gray-box">'
                            . '<label class="floatedl" for="team-edit-tables">' . $rb->get('teams.intables') . ':</label> '
                            . $tables
                            . '</div>'
                            . '<div class="gray-box">'
                            . '<label class="w160" for="team-edit-season">' . $rb->get('teams.season') . '</label> '
                            . '<select class="w200" name="team-edit-season" id="team-edit-season">'
                            . $seasons
                            . '</select>'
                            . '</div>'
                            . '<div class="gray-box">'
                            . '<input type="hidden" name="season-id" value="' . $team['season'] . '" />'
                            . '<input type="hidden" name="team-id" value="' . $team['id'] . '" />'
                            . '<input type="submit" name="team-save" value="' . $rb->get('teams.form.save') . '" />'
                            . '</div>'
                            . '</form>'
                            . '</div>'
                            . '<div class="clear"></div>';
                } else {
                    $return .= parent::getError($rb->get('project.notset'));
                }

                if ($useFrames == "false") {
                    return $return;
                } else {
                    return parent::getFrame($rb->get('teams.form.title'), $return, "", true);
                }
            }
        }

        public function showCopyTeamTableStatsForm($useFrames = false) {
            $return = '';
            $rb = self::rb();

            if (UniversalPermission::checkUserPermissions($this->UPDisc, self::getProjectId(), WEB_R_WRITE)) {
                $ok = true;
                $errors = '';
                $sourceData = array();
                $destData = array();
                $teamId;
                $seasonId;
                $sourceTableId;
                $destTableId;
                $type;
                if ($_POST['tcopy-submit'] == $rb->get('tcopy.submit')) {
                    $teamId = $_POST['tcopy-team'];
                    $seasonId = $_POST['tcopy-season'];
                    $sourceTableId = $_POST['tcopy-fromtable'];
                    $destTableId = $_POST['tcopy-totable'];
                    $type = $_POST['tcopy-type'];

                    if ($sourceTableId == $destTableId) {
                        $errors .= parent::getError($rb->get('tcopy.error.sametables'));
                        $ok = false;
                    }

                    $sourceData = parent::db()->fetchSingle('select `matches`, `wins`, `draws`, `loses`, `s_score`, `r_score`, `points`, `season` from `w_sport_table` where `team` = ' . $teamId . ' and `table_id` = ' . $sourceTableId . ' and `season` = ' . $seasonId . ';');
                    if ($sourceData == array()) {
                        $errors .= parent::getError($rb->get('tcopy.error.teamnotinsourcetable'));
                        $ok = false;
                    }
                    if ($ok) {
                        $destData = parent::db()->fetchSingle('select `matches`, `wins`, `draws`, `loses`, `s_score`, `r_score`, `points`, `season` from `w_sport_table` where `team` = ' . $teamId . ' and `table_id` = ' . $destTableId . ' and `season` = ' . $seasonId . ';');
                        if ($destData != array()) {
                            // Update or delete and insert
                            if ($type == 'append') {
                                // Update
                                $destData['matches'] += $sourceData['matches'];
                                $destData['wins'] += $sourceData['wins'];
                                $destData['draws'] += $sourceData['draws'];
                                $destData['loses'] += $sourceData['loses'];
                                $destData['s_score'] += $sourceData['s_score'];
                                $destData['r_score'] += $sourceData['r_score'];
                                $destData['points'] += $sourceData['points'];

                                parent::db()->execute('update `w_sport_table` set `matches` = ' . $destData['matches'] . ', `wins` = ' . $destData['wins'] . ', `draws` = ' . $destData['draws'] . ', `loses` = ' . $destData['loses'] . ', `s_score` = ' . $destData['s_score'] . ', `r_score` = ' . $destData['r_score'] . ', `points` = ' . $destData['points'] . ' where `team` = ' . $teamId . ' and `season` = ' . $seasonId . ' and `table_id` = ' . $destTableId . ';');
                                $errors .= parent::getSuccess($rb->get('tcopy.success.updated'));
                            } else {
                                // Delete
                                parent::db()->execute('delete from `w_sport_table` where `team` = ' . $teamId . ' and `table_id` = ' . $destTableId . ' and `season` = ' . $seasonId . ';');
                                // Insert
                                parent::db()->execute('insert into `w_sport_table`(`team`, `matches`, `wins`, `draws`, `loses`, `s_score`, `r_score`, `points`, `season`, `positionfix`, `table_id`, `project_id`) values(' . $teamId . ', ' . $sourceData['matches'] . ', ' . $sourceData['wins'] . ', ' . $sourceData['draws'] . ', ' . $sourceData['loses'] . ', ' . $sourceData['s_score'] . ', ' . $sourceData['r_score'] . ', ' . $sourceData['points'] . ', ' . $seasonId . ', 0, ' . $destTableId . ', ' . self::getProjectId() . ');');
                                $errors .= parent::getWarning($rb->get('tcopy.warning.deleted'));
                                $errors .= parent::getSuccess($rb->get('tcopy.success.added'));
                            }
                        } else {
                            // Insert
                            parent::db()->execute('insert into `w_sport_table`(`team`, `matches`, `wins`, `draws`, `loses`, `s_score`, `r_score`, `points`, `season`, `positionfix`, `table_id`, `project_id`) values(' . $teamId . ', ' . $sourceData['matches'] . ', ' . $sourceData['wins'] . ', ' . $sourceData['draws'] . ', ' . $sourceData['loses'] . ', ' . $sourceData['s_score'] . ', ' . $sourceData['r_score'] . ', ' . $sourceData['points'] . ', ' . $seasonId . ', 0, ' . $destTableId . ', ' . self::getProjectId() . ');');
                            $errors .= parent::getSuccess($rb->get('tcopy.success.added'));
                        }
                    }
                }

                $return .= ''
                        . '<form name="tema-copy-stats" method="post" action="' . $_SERVER['REQUEST_URI'] . '">'
                        . $errors
                        . '<div class="gray-box">'
                        . '<label for="tcopy-team">' . $rb->get('tcopy.team') . ':</label> '
                        . '<select name="tcopy-team" id="tcopy-team">'
                        . self::getTeamsOptions($teamId != '' ? $teamId : self::getTeamId())
                        . '</select> '
                        . '<label for="tcopy-fromtable">' . $rb->get('tcopy.fromtable') . ':</label> '
                        . '<select name="tcopy-fromtable" id="tcopy-fromtable">'
                        . self::getTablesOptions($sourceTableId != '' ? $sourceTableId : self::getTableId())
                        . '</select> '
                        . '<label for="tcopy-totable">' . $rb->get('tcopy.totable') . ':</label> '
                        . '<select name="tcopy-totable" id="tcopy-totable">'
                        . self::getTablesOptions($destTableId != '' ? $destTableId : self::getTableId())
                        . '</select> '
                        . '<label for="tcopy-season">' . $rb->get('tcopy.season') . ':</label> '
                        . '<select name="tcopy-season" id="tcopy-season">'
                        . self::getSeasonsOptions($seasonId != '' ? $seasonId : self::getSeasonId())
                        . '</select> '
                        . '<input type="radio" name="tcopy-type" id="tcopy-type1" value="override" title="' . $rb->get('tcopy.overridetitle') . '"' . ($type == 'override' ? ' checked="checked"' : '') . ' />'
                        . '<label for="tcopy-type1" title="' . $rb->get('tcopy.overridetitle') . '">' . $rb->get('tcopy.override') . '</label> '
                        . '<input type="radio" name="tcopy-type" id="tcopy-type2" value="append" title="' . $rb->get('tcopy.appendtitle') . '"' . ($type == 'append' ? ' checked="checked"' : '') . ' />'
                        . '<label for="tcopy-type2" title="' . $rb->get('tcopy.appendtitle') . '">' . $rb->get('tcopy.append') . '</label> '
                        . '<input type="submit" name="tcopy-submit" value="' . $rb->get('tcopy.submit') . '" title="' . $rb->get('tcopy.submittitle') . '" />'
                        . '</div>'
                        . '</form>';

                if ($useFrames == "false") {
                    return $return;
                } else {
                    return parent::getFrame($rb->get('tcopy.title'), $return, "", true);
                }
            }
        }

        /**
         *
         * 	List of players for editing.
         * 	C tag.
         *
         * 	@param		pageId					next page id
         * 	@param		useFrames				use frames in output
         * 	@param		showMsg					show messages in output
         *
         */
        public function showEditPlayers($pageId = false, $useFrames = false, $showMsg = false) {
            global $webObject;
            $rb = self::rb();
            $retrun = '';
            $actionUrl = $_SERVER['REQUEST_URI'];

            if (!self::isSetProjectId()) {
                $return .= parent::getError($rb->get('project.notset'));
            } else {
                if ($pageId != false) {
                    $actionUrl = $webObject->composeUrl($pageId);
                } else {
                    $actionUrl = $_SERVER['REQUEST_URI'];
                }

                if ($_POST['player-delete'] == $rb->get('players.delete')) {
                    if (UniversalPermission::checkUserPermissions($this->UPDisc, self::getProjectId(), WEB_R_WRITE)) {
                        $seasonId = $_POST['season-id'];
                        $playerId = $_POST['player-id'];
                        $position = $_POST['player-position'];
                        $teamId = $_POST['team-id'];

                        parent::db()->execute('DELETE FROM `w_sport_player` WHERE `id` = ' . $playerId . ' AND `team` = ' . $teamId . ' AND `season` = ' . $seasonId . ' AND `position` = ' . $position . ';');
                        parent::db()->execute('delete from `w_sport_stats` where `pid` = ' . $playerId . ' and `season` = ' . $seasonId . ' and `pos` = ' . $position . ';');
                        $return .= '<h4 class="success">' . $rb->get('players.success.delete') . '</h4>';
                    } else {
                        $return .= parent::getError($rb->get('projects.error.permissionsdenied'));
                    }
                } elseif ($_POST['player-deletewhole'] == $rb->get('players.deletewhole')) {
                    if (UniversalPermission::checkUserPermissions($this->UPDisc, self::getProjectId(), WEB_R_WRITE)) {
                        $playerId = $_POST['player-id'];
                        parent::db()->execute('DELETE FROM `w_sport_player` WHERE `id` = ' . $playerId . ';');
                        parent::db()->execute('delete from `w_sport_stats` where `pid` = ' . $playerId . ';');
                        // mazat i statistiky ??
                        $return .= '<h4 class="success">' . $rb->get('players.success.deletewhole') . '</h4>';
                    } else {
                        $return .= parent::getError($rb->get('projects.error.permissionsdenied'));
                    }
                }

                $return .= self::showPlayerSearchForm('', "false");

                $search = self::getPlayerSearchAsPartSql();
                if (self::getSeasonId() != '-1') {
                    if (self::getTeamId() != '-1') {
                        $players = parent::db()->fetchAll('SELECT DISTINCT `w_sport_player`.`id`, `w_sport_player`.`name`, `w_sport_player`.`surname` FROM `w_sport_player` WHERE `w_sport_player`.`season` = ' . self::getSeasonId() . ' AND `w_sport_player`.`team` = ' . self::getTeamId() . ' and `project_id` = ' . self::getProjectId() . ($search != '' ? ' and ' . $search : '') . ' ORDER BY `w_sport_player`.`id` ASC;');
                    } else {
                        $players = parent::db()->fetchAll('SELECT DISTINCT `w_sport_player`.`id`, `w_sport_player`.`name`, `w_sport_player`.`surname` FROM `w_sport_player` WHERE `w_sport_player`.`season` = ' . self::getSeasonId() . ' and `project_id` = ' . self::getProjectId() . ($search != '' ? ' and ' . $search : '') . ' ORDER BY `w_sport_player`.`id` ASC;');
                    }
                } else {
                    if (self::getTeamId() != '-1') {
                        $players = parent::db()->fetchAll('SELECT DISTINCT `w_sport_player`.`id`, `w_sport_player`.`name`, `w_sport_player`.`surname` FROM `w_sport_player` WHERE `w_sport_player`.`team` = ' . self::getTeamId() . ' and `project_id` = ' . self::getProjectId() . ($search != '' ? ' and ' . $search : '') . ' ORDER BY `w_sport_player`.`id` ASC;');
                    } else {
                        $players = parent::db()->fetchAll('SELECT DISTINCT `w_sport_player`.`id`, `w_sport_player`.`name`, `w_sport_player`.`surname` FROM `w_sport_player` where `project_id` = ' . self::getProjectId() . ($search != '' ? ' and ' . $search : '') . ' ORDER BY `w_sport_player`.`id` ASC;');
                    }
                    //$players = $dbObject->fetchAll('SELECT DISTINCT `w_sport_player`.`id`, `w_sport_player`.`name`, `w_sport_player`.`surname` FROM `w_sport_player` ORDER BY `w_sport_player`.`id` ASC;');
                }
                if (count($players) > 0) {
                    $return .= ''
                            . '<table class="players-list standart">'
                            . '<tr>'
                            . '<th class="players-list-id">' . $rb->get('players.id') . '</th>'
                            . '<th class="players-list-name">' . $rb->get('players.name') . '</th>'
                            . '<th class="players-list-surname">' . $rb->get('players.surname') . '</th>'
                            . '<th class="players-list-teasea">' . $rb->get('players.season') . ' / ' . $rb->get('players.team') . '</th>'
                            . '</tr>';

                    $i = 1;
                    foreach ($players as $pl) {
                        if (self::getSeasonId() != '-1') {
                            $seasons = parent::db()->fetchAll('SELECT DISTINCT `w_sport_player`.`position`, `w_sport_player`.`on_loan`, `w_sport_team`.`id` AS `tid`, `w_sport_season`.`id` AS `sid`, `w_sport_season`.`start_year`, `w_sport_season`.`end_year` FROM `w_sport_player` LEFT JOIN `w_sport_season` ON `w_sport_player`.`season` = `w_sport_season`.`id` LEFT JOIN `w_sport_team` ON `w_sport_player`.`team` = `w_sport_team`.`id` WHERE `w_sport_player`.`id` = ' . $pl['id'] . ' AND `w_sport_player`.`season` = ' . self::getSeasonId() . ' and `w_sport_season`.`project_id` = ' . self::getProjectId() . ' ORDER BY `w_sport_season`.`start_year` DESC;');
                        } else {
                            $seasons = parent::db()->fetchAll('SELECT DISTINCT `w_sport_player`.`position`, `w_sport_player`.`on_loan`, `w_sport_team`.`id` AS `tid`, `w_sport_season`.`id` AS `sid`, `w_sport_season`.`start_year`, `w_sport_season`.`end_year` FROM `w_sport_player` LEFT JOIN `w_sport_season` ON `w_sport_player`.`season` = `w_sport_season`.`id` LEFT JOIN `w_sport_team` ON `w_sport_player`.`team` = `w_sport_team`.`id` WHERE `w_sport_player`.`id` = ' . $pl['id'] . ' and `w_sport_season`.`project_id` = ' . self::getProjectId() . ' ORDER BY `w_sport_season`.`start_year` DESC;');
                        }
                        
                        $teaseastr = '';
                        foreach ($seasons as $sea) {
                        
                            $sea['name'] = parent::db()->fetchSingle('select `name` from `w_sport_team` where `id` = ' . $sea['tid'] . ' and `season` = ' . $sea['sid'] . ';');
                            $sea['name'] = $sea['name']['name'];

                            if (strlen($teaseastr) != 0) {
                                $teaseastr .= ', ';
                            }
                            $teaseastr .= '(' . $sea['start_year'] . ' - ' . $sea['end_year'] . ' / ' . $sea['name'] . ' - <span title="' . self::getPlayerPosition($sea['position']) . '" class="blue">' . self::getPlayerPositionShortcut($sea['position']) . '</span> ' . ($sea['on_loan'] == 1 ? ' <span class="red">[' . $rb->get('players.onloanletter') . ']</span>' : '') . ' - '
                                    . '<form name="player-edit" method="post" action="' . $actionUrl . '">'
                                    . '<input type="hidden" name="player-id" value="' . $pl['id'] . '" />'
                                    . '<input type="hidden" name="season-id" value="' . $sea['sid'] . '" />'
                                    . '<input type="hidden" name="team-id" value="' . $sea['tid'] . '" />'
                                    . '<input type="hidden" name="player-edit" value="' . $rb->get('players.edit') . '" />'
                                    . '<input type="image" src="~/images/page_edi.png" name="player-edit" value="' . $rb->get('players.edit') . '" title="' . $rb->get('players.editcap') . '" />'
                                    . '</form> '
                                    . '<form name="player-delete" method="post" action="' . $_SERVER['REQUEST_URI'] . '">'
                                    . '<input type="hidden" name="player-id" value="' . $pl['id'] . '" />'
                                    . '<input type="hidden" name="season-id" value="' . $sea['sid'] . '" />'
                                    . '<input type="hidden" name="team-id" value="' . $sea['tid'] . '" />'
                                    . '<input type="hidden" name="player-position" value="' . $sea['position'] . '" />'
                                    . '<input type="hidden" name="player-delete" value="' . $rb->get('players.delete') . '" />'
                                    . '<input class="confirm" type="image" src="~/images/page_del.png" name="player-delete" value="' . $rb->get('players.delete') . '" title="' . $rb->get('players.deletecap') . ', id (' . $pl['id'] . '), season (' . $sea['sid'] . '), team (' . $sea['tid'] . '), position (' . self::getPlayerPosition($sea['position']) . ')" />'
                                    . '</form> )';
                        }

                        $teaseastr .= ''
                                . ' <form name="player-add" method="post" action="' . $actionUrl . '">'
                                . '<input type="hidden" name="player-id" value="' . $pl['id'] . '" />'
                                . '<input type="hidden" name="player-add" value="' . $rb->get('players.add') . '" />'
                                . '<input type="image" src="~/images/page_add.png" name="player-add" value="' . $rb->get('players.add') . '" title="' . $rb->get('players.addcap') . '" />'
                                . '</form> '
                                . '<form name="player-deletewhole" method="post" action="' . $_SERVER['REQUEST_URI'] . '">'
                                . '<input type="hidden" name="player-id" value="' . $pl['id'] . '" />'
                                . '<input type="hidden" name="player-deletewhole" value="' . $rb->get('players.deletewhole') . '" />'
                                . '<input class="confirm" type="image" src="~/images/page_del.png" name="player-deletewhole" value="' . $rb->get('players.deletewhole') . '" title="' . $rb->get('players.deletewholecap') . ', id (' . $pl['id'] . ')" />'
                                . '</form> ';

                        $return .= ''
                                . '<tr class="' . ((($i % 2) == 1) ? 'idle' : 'even') . '">'
                                . '<td class="players-list-id">' . $pl['id'] . '</td>'
                                . '<td class="players-list-name">' . $pl['name'] . '</td>'
                                . '<td class="players-list-surname">' . $pl['surname'] . '</td>'
                                . '<td class="players-list-teasea">' . $teaseastr . '</td>'
                                . '</tr>';
                        $i++;
                    }

                    $return .= ''
                            . '</table>';
                } else {
                    $return .= '<h4 class="warning">' . $rb->get('players.warning.nodata') . '</h4>';
                }
                $return .= ''
                        . '<hr />'
                        . '<form name="player-deletewhole" method="post" action="' . $actionUrl . '">'
                        . '<input type="submit" name="player-new" value="' . $rb->get('players.new') . '" title="' . $rb->get('players.newcap') . '" />'
                        . '</form>';
            }
            if ($useFrames == "false") {
                return $return;
            } else {
                return parent::getFrame($rb->get('players.title'), $return, "", true);
            }
        }

        /**
         *
         * 	Edit player.
         * 	C tag.
         *
         * 	@param		useFrames				use frames in output
         * 	@param		showMsg					show messages in output
         *
         */
        public function showEditPlayerForm($useFrames = false, $showMsg = false) {
            global $dbObject;
            $rb = self::rb();
            $ok = true;
            $player = array();
            $return = '';
            $updateType = '';

            if ($_POST['player-save'] == $rb->get('player.form.save')) {
                if (UniversalPermission::checkUserPermissions($this->UPDisc, self::getProjectId(), WEB_R_WRITE)) {
                    $seasonId = $_POST['season-id'];
                    $playerId = $_POST['player-id'];
                    $teamId = $_POST['team-id'];

                    $player['name'] = $_POST['player-edit-name'];
                    $player['surname'] = $_POST['player-edit-surname'];
                    $player['url'] = parent::convertToValidUrl($_POST['player-edit-url']);
                    $player['birthyear'] = $_POST['player-edit-birthyear'];
                    $player['number'] = $_POST['player-edit-number'];
                    $player['position'] = $_POST['player-edit-position'];
                    $player['photo'] = $_POST['player-edit-photo'];
                    $player['season'] = $_POST['player-edit-season'];
                    $player['team'] = $_POST['player-edit-team'];
                    $player['on_loan'] = $_POST['player-edit-onloan'] == 'on' ? 1 : 0;
                    $updateType = $_POST['update-type'];

                    if ($player['url'] == '') {
                        $player['url'] = strtolower(parent::convertToValidUrl($player['name'] . '-' . $player['surname']));
                    }

                    if (strlen(trim($player['name'])) == 0) {
                        $return .= '<h4 class="error">' . $rb->get('player.form.error.name') . '</h4>';
                        $ok = false;
                    }
                    if (strlen(trim($player['surname'])) == 0) {
                        $return .= '<h4 class="error">' . $rb->get('player.form.error.surname') . '</h4>';
                        $ok = false;
                    }
                    if (strlen(trim($player['birthyear'])) == 0 || !is_numeric($player['birthyear'])) {
                        $return .= '<h4 class="error">' . $rb->get('player.form.error.birthyear') . '</h4>';
                        $ok = false;
                    }
                    if (strlen(trim($player['number'])) == 0 || !is_numeric($player['number'])) {
                        $return .= '<h4 class="error">' . $rb->get('player.form.error.number') . '</h4>';
                        $ok = false;
                    }
                    $plsu = parent::db()->fetchAll('select `id` from `w_sport_player` where `url` = "' . $player['url'] . '" and `project_id` = ' . self::getProjectId() . '' . ($playerId != '' ? ' and `id` != ' . $playerId : '') . ';');
                    if (count($plsu) != 0) {
                        $return .= parent::getError($rb->get('player.form.error.uniqueurl'));
                        $ok = false;
                    }

                    //parent::db()->setMockMode(true);
                    if ($ok) {
                        //$pl = $dbObject->fetchAll('SELECT `id` FROM `w_sport_player` WHERE `id` = '.$playerId.' AND `season` = '.$seasonId.' AND `team` = '.$teamId.' AND `position` = '.$player['position'].';');
                        if ($updateType == 'edit') {
                            $dbObject->execute('UPDATE `w_sport_player` SET `name` = "' . $player['name'] . '", `surname` = "' . $player['surname'] . '", `url` = "' . $player['url'] . '", `birthyear` = ' . $player['birthyear'] . ', `number` = ' . $player['number'] . ', `photo` = "' . $player['photo'] . '", `position` = ' . $player['position'] . ', `season` = ' . $player['season'] . ', `team` = ' . $player['team'] . ', `on_loan` = ' . $player['on_loan'] . ' WHERE `id` = ' . $playerId . ' AND `season` = ' . $seasonId . ' AND `team` = ' . $teamId . ';');
                        } else {
                            if ($playerId == '') {
                                $dbObject->execute('INSERT INTO `w_sport_player`(`name`, `surname`, `url`, `birthyear`, `number`, `position`, `photo`, `season`, `team`, `on_loan`, `project_id`) VALUES ("' . $player['name'] . '", "' . $player['surname'] . '", "' . $player['url'] . '", ' . $player['birthyear'] . ', ' . $player['number'] . ', ' . $player['position'] . ', "' . $player['photo'] . '", ' . $player['season'] . ', ' . $player['team'] . ', ' . $player['on_loan'] . ', ' . self::getProjectId() . ');');
                            } else {
                                $dbObject->execute('INSERT INTO `w_sport_player`(`id`, `name`, `surname`, `url`, `birthyear`, `number`, `position`, `photo`, `season`, `team`, `on_loan`, `project_id`) VALUES (' . $playerId . ', "' . $player['name'] . '", "' . $player['surname'] . '", "' . $player['url'] . '", ' . $player['birthyear'] . ', ' . $player['number'] . ', ' . $player['position'] . ', "' . $player['photo'] . '", ' . $player['season'] . ', ' . $player['team'] . ', ' . $player['on_loan'] . ', ' . self::getProjectId() . ');');
                            }
                        }
                    }
                    //parent::db()->setMockMode(false);
                } else {
                    $return .= parent::getError($rb->get('projects.error.permissionsdenied'));
                }
            }

            if ($ok == false || $_POST['player-edit'] == $rb->get('players.edit') || $_POST['player-new'] == $rb->get('players.new') || $_POST['player-add'] == $rb->get('players.add')) {
                //$return .= '<h4 class="warning">Coming soon ...</h4>';
                if ($_POST['player-edit'] == $rb->get('players.edit')) {
                    $seasonId = $_POST['season-id'];
                    $playerId = $_POST['player-id'];
                    $teamId = $_POST['team-id'];
                    $player = $dbObject->fetchAll('SELECT `name`, `surname`, `url`, `birthyear`, `number`, `position`, `photo`, `season`, `team`, `on_loan` FROM `w_sport_player` WHERE `id` = ' . $playerId . ' AND `team` = ' . $teamId . ' AND `season` = ' . $seasonId . ';');
                    $player = $player[0];
                    $player['photo'] = str_replace('~', '&#126', $player['photo']);
                    $updateType = 'edit';
                } elseif ($_POST['player-add'] == $rb->get('players.add')) {
                    $playerId = $_POST['player-id'];
                    $player = $dbObject->fetchSingle('SELECT `name`, `surname`, `url`, `birthyear`, `number`, `position`, `photo`, `team`, `on_loan` FROM `w_sport_player` WHERE `id` = ' . $playerId . ' ORDER BY `season` DESC;');
                    $teamId = $player['team'];
                    $updateType = 'add';
                } elseif ($_POST['player-new'] == $rb->get('players.new')) {
                    $teamId = self::getTeamId();
                    $updateType = 'add';
                }

                $return .= ''
                . '<div class="player-edit-form">'
                    . '<form name="player-edit-form" method="post" action="' . $_SERVER['REQUEST_URI'] . '">'
                        . '<div class="player-edit-name">'
                            . '<label for="player-edit-name">' . $rb->get('players.form.name') . ':</label>'
                            . '<input type="text" name="player-edit-name" id="player-edit-name" value="' . $player['name'] . '" />'
                        . '</div>'
                        . '<div class="player-edit-surname">'
                            . '<label for="player-edit-surname">' . $rb->get('players.form.surname') . ':</label>'
                            . '<input type="text" name="player-edit-surname" id="player-edit-surname" value="' . $player['surname'] . '" />'
                        . '</div>'
                        . '<div class="player-edit-surname">'
                            . '<label for="player-edit-url">' . $rb->get('players.form.url') . ':</label>'
                            . '<input type="text" name="player-edit-url" id="player-edit-url" value="' . $player['url'] . '" />'
                        . '</div>'
                        . '<div class="player-edit-birthyear">'
                            . '<label for="player-edit-birthyear">' . $rb->get('players.form.birthyear') . ':</label>'
                            . '<input type="text" name="player-edit-birthyear" id="player-edit-birthyear" value="' . $player['birthyear'] . '" />'
                        . '</div>'
                        . '<div class="player-edit-number">'
                            . '<label for="player-edit-number">' . $rb->get('players.form.number') . ':</label>'
                            . '<input type="text" name="player-edit-number" id="player-edit-number" value="' . $player['number'] . '" />'
                        . '</div>'
                        . '<div class="player-edit-position">'
                            . '<label for="player-edit-position">' . $rb->get('players.form.position') . ':</label>'
                            . '<select name="player-edit-position" id="player-edit-position">'
                                . '<option value="1"' . ($player['position'] == 1 ? ' selected="selected"' : '') . '>' . $rb->get('player.position-goa') . '</option>'
                                . '<option value="2"' . ($player['position'] == 2 ? ' selected="selected"' : '') . '>' . $rb->get('player.position-def') . '</option>'
                                . '<option value="3"' . ($player['position'] == 3 ? ' selected="selected"' : '') . '>' . $rb->get('player.position-att') . '</option>'
                            . '</select>'
                        . '</div>'
                        . '<div class="player-edit-photo">'
                            . '<label for="player-edit-photo">' . $rb->get('players.form.photo') . '</label>'
                            . '<input type="text" name="player-edit-photo" id="player-edit-photo" value="' . $player['photo'] . '" />'
                        . '</div>'
                        . '<div class="player-edit-season">'
                            . '<label for="player-edit-season">' . $rb->get('players.form.season') . '</label>'
                            . '<select name="player-edit-season" id="player-edit-season">'
                            . self::getSeasonsOptions(0, 0, $seasonId)
                        . '</select>'
                        . '</div>'
                        . '<div class="player-edit-team">'
                            . '<label for="player-edit-team">' . $rb->get('players.form.team') . '</label>'
                            . '<select name="player-edit-team" id="player-edit-team">'
                            . self::getTeamsOptions($teamId)
                        . '</select>'
                        . '</div>'
                        . '<div class="gray-box">'
                            . '<label for="player-edit-onloan">' . $rb->get('players.form.onloan') . '</label>'
                            . '<input type="checkbox" name="player-edit-onloan" id="player-edit-onloan" ' . ($player['on_loan'] == 1 ? ' checked="checked"' : '') . '/>'
                        . '</div>'
                        . '<div class="player-edt-submit">'
                            . '<input type="hidden" name="player-id" value="' . $playerId . '" />'
                            . '<input type="hidden" name="season-id" value="' . $seasonId . '" />'
                            . '<input type="hidden" name="team-id" value="' . $teamId . '" />'
                            . '<input type="hidden" name="update-type" value="' . $updateType . '" />'
                            . '<input type="submit" name="player-save" value="' . $rb->get('player.form.save') . '" />'
                        . '</div>'
                    . '</form>'
                . '</div>'
                . '<div class="clear"></div>';

                if ($useFrames == "false") {
                    return $return;
                } else {
                    return parent::getFrame($rb->get('players.form.title'), $return, "", true);
                }
            }
        }

        /**
         *
         * 	Player search filter, applies to sport:editPlayers, sport:players
         *
         */
        public function showPlayerSearchForm($pageId = false, $useFrames = false, $showMsg = false) {
            global $webObject;
            $return = '';
            $player = array();
            $rb = self::rb();

            if ($_POST['player-search-submit'] == $rb->get('player.search.submit')) {
                $name = $_POST['player-search-name'];
                $surname = $_POST['player-search-surname'];
                $position = $_POST['player-search-position'];

                if ($name != '') {
                    parent::session()->set('name', $name, 'player-search');
                } else {
                    parent::session()->delete('name', 'player-search');
                }
                if ($surname != '') {
                    parent::session()->set('surname', $surname, 'player-search');
                } else {
                    parent::session()->delete('surname', 'player-search');
                }
                if ($position != '0') {
                    parent::session()->set('position', $position, 'player-search');
                } else {
                    parent::session()->delete('position', 'player-search');
                }

                if ($pageId != '') {
                    $webObject->redirectTo($pageId);
                }
            } elseif ($_POST['player-search-clear'] == $rb->get('player.search.clear')) {
                parent::session()->clear('player-search');
            }

            $return .= ''
                    . '<form name="player-search" method="post" action="' . $_SEVER['REQUEST_URI'] . '">'
                    . '<div class="player-search-name gray-box">'
                    . '<label for="player-search-name" class="w100">' . $rb->get('players.form.name') . '</label>'
                    . '<input class="w200" type="text" name="player-search-name" id="player-search-name" value="' . parent::session()->get('name', 'player-search') . '" />'
                    . '</div>'
                    . '<div class="player-search-surname gray-box">'
                    . '<label for="player-search-surname" class="w100">' . $rb->get('players.form.surname') . '</label>'
                    . '<input class="w200" type="text" name="player-search-surname" id="player-search-surname" value="' . parent::session()->get('surname', 'player-search') . '" />'
                    . '</div>'
                    . '<div class="player-search-position gray-box">'
                    . '<label for="player-search-position" class="w100">' . $rb->get('players.form.position') . '</label>'
                    . '<select class="w200" name="player-search-position" id="player-search-position">'
                    . '<option value="0">' . $rb->get('player.position-all') . '</option>'
                    . '<option value="1"' . (parent::session()->get('position', 'player-search') == 1 ? ' selected="selected"' : '') . '>' . $rb->get('player.position-goa') . '</option>'
                    . '<option value="2"' . (parent::session()->get('position', 'player-search') == 2 ? ' selected="selected"' : '') . '>' . $rb->get('player.position-def') . '</option>'
                    . '<option value="3"' . (parent::session()->get('position', 'player-search') == 3 ? ' selected="selected"' : '') . '>' . $rb->get('player.position-att') . '</option>'
                    . '</select>'
                    . '</div>'
                    . '<div class="player-search-submit gray-box">'
                    . '<input type="submit" name="player-search-submit" id="player-search-submit" value="' . $rb->get('player.search.submit') . '" /> '
                    . '<input type="submit" name="player-search-clear" id="player-search-clear" value="' . $rb->get('player.search.clear') . '" />'
                    . '</div>'
                    . '</form>';

            if ($useFrames == "false") {
                return $return;
            } else {
                return parent::getFrame($rb->get('players.form.title'), $return, "", true);
            }
        }

        private function getPlayerSearchAsPartSql() {
            $return = '';
            $values = array('name' => 'string', 'surname' => 'string', 'position' => 'number');
            foreach ($values as $key => $value) {
                if (parent::session()->exists($key, 'player-search')) {
                    if ($return != '') {
                        $return .= ' and ';
                    }
                    $return .= '`' . $key . '` like ';
                    switch ($value) {
                        case 'string': $return .= '"%' . parent::session()->get($key, 'player-search') . '%"';
                            break;
                        case 'number': $return .= parent::session()->get($key, 'player-search');
                            break;
                    }
                }
            }
            return $return;
        }

        /**
         *
         * 	Shows current tables in project
         *
         */
        public function showEditTables($pageId = false, $useFrames = false, $showMsg = false) {
            $rb = self::rb();
            $retrun = '';

            if (!self::isSetProjectId()) {
                $return .= parent::getError($rb->get('project.notset'));
            } else {
                if ($_POST['table-delete'] == $rb->get('tables.delete')) {
                    $tableId = $_POST['table-id'];
                    if (UniversalPermission::checkUserPermissions($this->UPDisc, self::getProjectId(), WEB_R_WRITE)) {
                        parent::db()->execute('delete from `w_sport_tables` where `id` = ' . $tableId . ';');
                    } else {
                        $return .= parent::getError($rb->get('projects.error.permissionsdenied'));
                    }
                }

                $tables = parent::db()->fetchAll('select `id`, `name` from `w_sport_tables` where `project_id` = ' . self::getProjectId() . ' order by `id`;');
                if (count($tables) > 0) {
                    $return .= ''
                            . '<table class="standart">'
                            . '<tr>'
                            . '<th>' . $rb->get('tables.id') . ':</th>'
                            . '<th>' . $rb->get('tables.name') . ':</th>'
                            . '<th></th>'
                            . '</tr>';

                    $i = 0;
                    foreach ($tables as $table) {
                        $return .= ''
                                . '<tr class="' . ((($i % 2) == 0) ? 'idle' : 'even') . '">'
                                . '<td>' . $table['id'] . '</td>'
                                . '<td>' . $table['name'] . '</td>'
                                . '<td>'
                                . '<form name="tables-edit" action="' . $_SERVER['REQUEST_URI'] . '" method="post">'
                                . '<input type="hidden" name="table-id" value="' . $table['id'] . '" />'
                                . '<input type="hidden" name="table-edit" value="' . $rb->get('tables.edit') . '" />'
                                . '<input type="image" src="~/images/page_edi.png" name="table-edit" value="' . $rb->get('tables.edit') . '" title="' . $rb->get('tables.edittitle') . '" />'
                                . '</form> '
                                . '<form name="tables-delete" action="' . $_SERVER['REQUEST_URI'] . '" method="post">'
                                . '<input type="hidden" name="table-id" value="' . $table['id'] . '" />'
                                . '<input type="hidden" name="table-delete" value="' . $rb->get('tables.delete') . '" />'
                                . '<input class="confirm" type="image" src="~/images/page_del.png" name="table-delete" value="' . $rb->get('tables.delete') . '" title="' . $rb->get('tables.deletetitle') . ', id(' . $table['id'] . ')" />'
                                . '</form>'
                                . '</td>'
                                . '</tr>';
                        $i++;
                    }
                    $return .= ''
                            . '</table>';
                } else {
                    $return .= parent::getWarning($rb->get('tables.nodata'));
                }

                $return .= ''
                        . '<hr />'
                        . '<form name="tables-add" method="post" action="' . $_SERVER['REQUEST_URI'] . '">'
                            . '<input type="submit" name="tables-add" value="' . $rb->get('tables.add') . '" /> '
                        . '</form>';
            }

            if ($useFrames == "false") {
                return $return;
            } else {
                return parent::getFrame($rb->get('tables.title'), $return, "", true);
            }
        }

        public function showEditTableForm($pageId = false, $useFrames = false, $showMsg = false) {
            $rb = self::rb();
            $retrun = '';
            $table = array();
            $dao = parent::dao('SportTable');
            $isValidationFailed = false;

            if ($_POST['table-submit'] == $rb->get('table.submit')) {
                $table['id'] = $_POST['table-id'];
                $table['name'] = trim($_POST['table-detail-name']);
                $table['points_win'] = trim($_POST['table-detail-points-win']);
                $table['points_win_extratime'] = trim($_POST['table-detail-points-winextratime']);
                $table['points_draw'] = trim($_POST['table-detail-points-draw']);
                $table['points_loose_extratime'] = trim($_POST['table-detail-points-looseextratime']);
                $table['points_loose'] = trim($_POST['table-detail-points-loose']);
                $table['project_id'] = self::getProjectId();

                if (UniversalPermission::checkUserPermissions($this->UPDisc, self::getProjectId(), WEB_R_WRITE)) {
                    $tables = array();
                    if ($table['id'] != '') {
                        $tables = parent::db()->fetchAll('select `id` from `w_sport_table` where `name` = "' . $table['name'] . '" and `project_id` = ' . self::getProjectId() . ' and `table_id` != ' . parent::db()->escape($table['id']) . ';');
                    }
                    if ($table['name'] == '' || ($table['id'] != '' && count($tables) > 0)) {
                        $return .= parent::getError($rb->get('table.error.namemustbeunique'));
                        $isValidationFailed = true;
                    } else {
                        if ($table['id'] != '') {
                            $tables = parent::db()->fetchAll('select `id` from `w_sport_tables` where `id` = ' . $table['id'] . ';');
                        } else {
                            $tables = array();
                            unset($table['id']);
                        }

                        if (count($tables) == 1) {
                            $dao->update($table);
                        } else {
                            $dao->insert($table);
                        }
                    }
                } else {
                    $return .= parent::getError($rb->get('projects.error.permissionsdenied'));
                }
            }

            if ($_POST['table-edit'] == $rb->get('tables.edit') || $_POST['tables-add'] == $rb->get('tables.add') || $isValidationFailed) {
                $tableId = $_POST['table-id'];
                $curTable = $dao->get($tableId);
                if ($curTable != array()) {
                    $table = $curTable;
                }

                $return .= ''
                . '<form name="table-detaill-edit" method="post" action="' . $_SERVER['REQUEST_URI'] . '">'
                    . '<div class="gray-box">'
                        . '<label for="table-detail-name" class="w100">' . $rb->get('tables.name') . ':</label>'
                        . '<input type="text" name="table-detail-name" id="table-detail-name" value="' . $table['name'] . '" />'
                    . '</div>'
                    . '<strong>' . $rb->get('tables.points') . '</strong>'
                    . '<div class="gray-box">'
                        . '<label for="table-detail-points-win" class="w160">' . $rb->get('tables.points.win') . ':</label>'
                        . '<input type="text" name="table-detail-points-win" id="table-detail-points-win" class="w30" value="' . $table['points_win'] . '" />'
                    . '</div>'
                    . '<div class="gray-box">'
                        . '<label for="table-detail-points-winextratime" class="w160">' . $rb->get('tables.points.winextratime') . ':</label>'
                        . '<input type="text" name="table-detail-points-winextratime" id="table-detail-points-winextratime" class="w30" value="' . $table['points_win_extratime'] . '" />'
                    . '</div>'
                    . '<div class="gray-box">'
                        . '<label for="table-detail-points-draw" class="w160">' . $rb->get('tables.points.draw') . ':</label>'
                        . '<input type="text" name="table-detail-points-draw" id="table-detail-points-draw" class="w30" value="' . $table['points_draw'] . '" />'
                    . '</div>'
                    . '<div class="gray-box">'
                        . '<label for="table-detail-points-looseextratime" class="w160">' . $rb->get('tables.points.looseextratime') . ':</label>'
                        . '<input type="text" name="table-detail-points-looseextratime" id="table-detail-points-looseextratime" class="w30" value="' . $table['points_loose_extratime'] . '" />'
                    . '</div>'
                    . '<div class="gray-box">'
                        . '<label for="table-detail-points-loose" class="w160">' . $rb->get('tables.points.loose') . ':</label>'
                        . '<input type="text" name="table-detail-points-loose" id="table-detail-points-loose" class="w30" value="' . $table['points_loose'] . '" />'
                    . '</div>'
                    . '<div class="gray-box">'
                        . '<input type="hidden" name="table-id" value="' . $table['id'] . '" />'
                        . '<input type="submit" name="table-submit" value="' . $rb->get('table.submit') . '" />'
                    . '</div>'
                . '</form>';

                if ($useFrames == "false") {
                    return $return;
                } else {
                    return parent::getFrame($rb->get('table.title'), $return, "", true);
                }
            }
        }

        /**
         *
         * 	List of matches for editing.
         * 	C tag.
         *
         * 	@param		pageId					next page id
         * 	@param		useFrames				use frames in output
         * 	@param		showMsg					show messages in output
         *
         */
        public function showEditMatches($pageId = false, $useFrames = false, $showMsg = false) {
            global $dbObject;
            global $webObject;
            $rb = self::rb();
            $retrun = '';

            if ($pageId != false) {
                $actionUrl = $webObject->composeUrl($pageId);
            } else {
                $actionUrl = $_SERVER['REQUEST_URI'];
            }

            if ($_POST['match-delete'] == $rb->get('matches.delete')) {
                if (UniversalPermission::checkUserPermissions($this->UPDisc, self::getProjectId(), WEB_R_WRITE)) {
                    $match['id'] = $_POST['match-id'];
                    $match['season'] = self::getSeasonId();
                    $tmpma = $dbObject->fetchAll(parent::query()->get('selectMatchByIdSeasonId', array('id' => $match['id'], 'seasonId' => $match['season']), 'sport'));
                    if (count($tmpma) != 0) {
                        $tmpma[0]['season'] = $match['season'];
                        if ($tmpma[0]['in_table'] != 0 && $tmpma[0]['notplayed'] != 1) {
                            if (!self::removeMatchFromTable($tmpma[0])) {
                                $return .= self::getError($rb->get('match.warning.teamsnotintable'));
                            }
                        }
                        $dbObject->execute('DELETE FROM `w_sport_match` WHERE `id` = ' . $match['id'] . ' AND `season` = ' . $match['season'] . ';');
                        $dbObject->execute('DELETE FROM `w_sport_stats` WHERE `mid` = ' . $match['id'] . ' AND `season` = ' . $match['season'] . ';');
                        $return .= '<h4 class="success">' . $rb->get('matches.success.deleted') . '</h4>';
                    } else {
                        $return .= '<h4 class="error">' . $rb->get('matches.error.deletingerror') . '</h4>';
                    }
                } else {
                    $return .= parent::getError($rb->get('projects.error.permissionsdenied'));
                }
            }

            if ($_POST['match-stats-delete'] == $rb->get('matches.stats.delete')) {
                $matchId = $_POST['match-id'];
                $seasonId = self::getSeasonId();
                parent::db()->execute('DELETE FROM `w_sport_stats` WHERE `mid` = ' . $matchId . ' AND `season` = ' . $seasonId . ';');
            }

            if (self::getSeasonId() != '-1') {
                if (self::getTeamId() != '-1') {
                    $matches = parent::db()->fetchAll('SELECT `id`, `h_team`, `a_team`, `h_score`, `a_score`, `h_shoots`, `a_shoots`, `h_penalty`, `a_penalty`, `h_extratime`, `a_extratime`, `round`, `season`, `notplayed` FROM `w_sport_match` WHERE `season` = ' . self::getSeasonId() . ' AND `project_id` = ' . self::getProjectId() . ' and (`w_sport_match`.`h_team` = ' . self::getTeamId() . ' OR `w_sport_match`.`a_team` = ' . self::getTeamId() . ')' . (self::getTableId() != '-1' ? ' and `in_table` = ' . self::getTableId() : '') . ' ORDER BY `round` desc, `id` DESC;');
                } else {
                    $matches = parent::db()->fetchAll('SELECT `id`, `h_team`, `a_team`, `h_score`, `a_score`, `h_shoots`, `a_shoots`, `h_penalty`, `a_penalty`, `h_extratime`, `a_extratime`, `round`, `season`, `notplayed` FROM `w_sport_match` WHERE `season` = ' . self::getSeasonId() . ' and `project_id` = ' . self::getProjectId() . (self::getTableId() != '-1' ? ' and `in_table` = ' . self::getTableId() : '') . ' ORDER BY `round` desc, `id` DESC;');
                }

                if (count($matches) > 0) {
                    $return .= ''
                            . '<table class="matches-table standart">'
                            . '<tr>'
                            . '<th class="matches-table-id">' . $rb->get('matches.id') . ':</th>'
                            . '<th class="matches-table-round">' . $rb->get('matches.round') . ':</th>'
                            . '<th class="matches-table-home">' . $rb->get('matches.hteam') . ':</th>'
                            . '<th class="matches-table-away">' . $rb->get('matches.ateam') . ':</th>'
                            . '<th class="matches-table-score">' . $rb->get('matches.score') . ':</th>'
                            . '<th class="matches-table-shoots">' . $rb->get('matches.shoots') . ':</th>'
                            . '<th class="matches-table-pentalty">' . $rb->get('matches.penalty') . ':</th>'
                            . '<th class="matches-table-extratime">' . $rb->get('matches.extratime') . ':</th>'
                            . '<th class="matches-table-edit">' . $rb->get('matches.edit') . ':</th>'
                            . '</tr>';

                    $i = 1;
                    foreach ($matches as $match) {
                        $home = parent::db()->fetchAll('SELECT `name` FROM `w_sport_team` WHERE `id` = ' . $match['h_team'] . ';');
                        $away = parent::db()->fetchAll('SELECT `name` FROM `w_sport_team` WHERE `id` = ' . $match['a_team'] . ';');
                        $stats = parent::db()->fetchAll('SELECT `pid` FROM `w_sport_stats` WHERE `mid` = ' . $match['id'] . ' AND `season` = ' . $match['season'] . ';');
                        $extime = ($match['h_extratime'] == 1) ? $rb->get('matches.form.homeexwin') : (($match['a_extratime'] == 1) ? $rb->get('matches.form.awayexwin') : '');

                        if ($match['notplayed'] == 1) {
                            $match['h_score'] = '-';
                            $match['h_shoots'] = '-';
                            $match['h_penalty'] = '-';
                            $match['a_score'] = '-';
                            $match['a_shoots'] = '-';
                            $match['a_penalty'] = '-';
                        }

                        $round = parent::db()->fetchSingle(parent::query()->get('roundById', array('id' => $match['round']), 'sport'));

                        $return .= ''
                                . '<tr class="' . ((($i % 2) == 1) ? 'idle' : 'even') . '">'
                                . '<td class="matches-table-id">' . $match['id'] . '</td>'
                                . '<td class="matches-table-round">' . $round['name'] . '</td>'
                                . '<td class="matches-table-home">' . $home[0]['name'] . '</td>'
                                . '<td class="matches-table-away">' . $away[0]['name'] . '</td>'
                                . '<td class="matches-table-score">' . $match['h_score'] . ' : ' . $match['a_score'] . '</td>'
                                . '<td class="matches-table-shoots">' . $match['h_shoots'] . ' : ' . $match['a_shoots'] . '</td>'
                                . '<td class="matches-table-pentalty">' . $match['h_penalty'] . ' : ' . $match['a_penalty'] . '</td>'
                                . '<td class="matches-table-extratime">' . $extime . '</td>'
                                . '<td class="matches-table-edit">'
                                . '<form name="matches-edit" method="post" action="' . $actionUrl . '">'
                                . '<input type="hidden" name="match-id" value="' . $match['id'] . '" />'
                                . '<input type="hidden" name="match-edit" value="' . $rb->get('matches.edit') . '" />'
                                . '<input type="image" src="~/images/page_edi.png" name="match-edit" value="' . $rb->get('matches.edit') . '" title="' . $rb->get('matches.editcap') . '" />'
                                . '</form> -'
                                . ((count($stats) == 0) ? ''
                                        . (($match['notplayed'] != 1) ? ''
                                                . ' <form name="matches-stats-add" method="post" action="' . $actionUrl . '">'
                                                . '<input type="hidden" name="match-id" value="' . $match['id'] . '" />'
                                                . '<input type="hidden" name="match-stats-add" value="' . $rb->get('matches.statsadd') . '" />'
                                                . '<input type="image" src="~/images/page_add.png" name="match-stats-add" value="' . $rb->get('matches.statsadd') . '" title="' . $rb->get('matches.statsaddcap') . '" />'
                                                . '</form> -' : '') : ''
                                        . ' <form name="matches-stats-edit" method="post" action="' . $actionUrl . '">'
                                        . '<input type="hidden" name="match-id" value="' . $match['id'] . '" />'
                                        . '<input type="hidden" name="match-stats-edit" value="' . $rb->get('matches.statsedit') . '" />'
                                        . '<input type="image" src="~/images/page_edi.png" name="match-stats-edit" value="' . $rb->get('matches.statsedit') . '" title="' . $rb->get('matches.statseditcap') . '" />'
                                        . '</form> '
                                        . ' <form name="matches-stats-delete" method="post" action="' . $actionUrl . '">'
                                        . '<input type="hidden" name="match-id" value="' . $match['id'] . '" />'
                                        . '<input type="hidden" name="match-stats-delete" value="' . $rb->get('matches.stats.delete') . '" />'
                                        . '<input class="confirm" type="image" src="~/images/page_del.png" name="match-stats-delete" value="' . $rb->get('matches.stats.delete') . '" title="' . $rb->get('matches.stats.deletecap') . ', id(' . $match['id'] . ')" />'
                                        . '</form> -'
                                )
                                . ' <form name="matches-delete" method="post" action="' . $actionUrl . '">'
                                . '<input type="hidden" name="match-id" value="' . $match['id'] . '" />'
                                . '<input type="hidden" name="match-delete" value="' . $rb->get('matches.delete') . '" />'
                                . '<input class="confirm" type="image" src="~/images/page_del.png" name="match-delete" value="' . $rb->get('matches.delete') . '" title="' . $rb->get('matches.deletecap') . ', id(' . $match['id'] . ')" />'
                                . '</form>'
                                . '</td>'
                                . '</tr>';
                        $i++;
                    }
                } else {
                    $return .= parent::getWarning($rb->get('matches.warning.nodata'));
                }

                $return .= ''
                        . '</table>'
                        . '<hr />'
                        . '<form name="match-new" method="post" action="' . $actionUrl . '">'
                        . '<input type="submit" name="match-new" value="' . $rb->get('matches.new') . '" title="' . $rb->get('matches.newcap') . '" />'
                        . '</form>';
            } else {
                $return .= parent::getError($rb->get('season.error.notset'));
            }

            if ($useFrames == "false") {
                return $return;
            } else {
                return parent::getFrame($rb->get('matches.title'), $return, "", true);
            }
        }

        private function removeMatchFromTable($match) {
            $table = parent::dao('SportTable')->get($match['in_table']);

            $team1 = parent::db()->fetchSingle(parent::query()->get('selectTeamFromTableByTeamIdSeasonIdTableId', array('teamId' => $match['h_team'], 'seasonId' => $match['season'], 'tableId' => $match['in_table']), 'sport'));
            $team2 = parent::db()->fetchSingle(parent::query()->get('selectTeamFromTableByTeamIdSeasonIdTableId', array('teamId' => $match['a_team'], 'seasonId' => $match['season'], 'tableId' => $match['in_table']), 'sport'));
            if ($team1 != array() && $team2 != array()) {
                $team1['matches']--;
                $team2['matches']--;
                if ($match['h_score'] > $match['a_score']) {
                    $team1['wins']--;
                    $team2['loses']--;
                    $team1['points'] -= $table['points_win'];
                    $team2['points'] -= $table['points_loose'];
                } elseif ($match['a_score'] > $match['h_score']) {
                    $team2['wins']--;
                    $team1['loses']--;
                    $team2['points'] -= $table['points_win'];
                    $team1['points'] -= $table['points_loose'];
                } elseif ($match['h_score'] == $match['a_score'] && $match['h_extratime'] == 1) {
                    $team1['draws']--;
                    $team2['draws']--;
                    $team1['points'] -= $table['points_win_extratime'];
                    $team2['points'] -= $table['points_loose_extratime'];
                } elseif ($match['h_score'] == $match['a_score'] && $match['a_extratime'] == 1) {
                    $team1['draws']--;
                    $team2['draws']--;
                    $team2['points'] -= $table['points_win_extratime'];
                    $team1['points'] -= $table['points_loose_extratime'];
                } else {
                    $team1['draws']--;
                    $team2['draws']--;
                    $team1['points'] -= $table['points_draw'];
                    $team2['points'] -= $table['points_draw'];
                }
                $team1['s_score'] -= $match['h_score'];
                $team1['r_score'] -= $match['a_score'];
                $team2['s_score'] -= $match['a_score'];
                $team2['r_score'] -= $match['h_score'];

                parent::db()->execute(parent::query()->get('updateTableByIdTeamIdSeasonId', array('matches' => $team1['matches'], 'wins' => $team1['wins'], 'draws' => $team1['draws'], 'loses' => $team1['loses'], 'sScore' => $team1['s_score'], 'rScore' => $team1['r_score'], 'points' => $team1['points'], 'teamId' => $match['h_team'], 'seasonId' => $match['season'], 'tableId' => $team1['table_id']), 'sport'));
                parent::db()->execute(parent::query()->get('updateTableByIdTeamIdSeasonId', array('matches' => $team2['matches'], 'wins' => $team2['wins'], 'draws' => $team2['draws'], 'loses' => $team2['loses'], 'sScore' => $team2['s_score'], 'rScore' => $team2['r_score'], 'points' => $team2['points'], 'teamId' => $match['a_team'], 'seasonId' => $match['season'], 'tableId' => $team2['table_id']), 'sport'));
                return true;
            }

            return false;
        }

        private function addMatchToTable($match) {
            $table = parent::dao('SportTable')->get($match['in_table']);

            $team1 = parent::db()->fetchSingle(parent::query()->get('selectTeamFromTableByTeamIdSeasonIdTableId', array('teamId' => $match['h_team'], 'seasonId' => $match['season'], 'tableId' => $match['in_table']), 'sport'));
            $team2 = parent::db()->fetchSingle(parent::query()->get('selectTeamFromTableByTeamIdSeasonIdTableId', array('teamId' => $match['a_team'], 'seasonId' => $match['season'], 'tableId' => $match['in_table']), 'sport'));
            $team1['matches']++;
            $team2['matches']++;
            if ($match['h_score'] > $match['a_score']) {
                $team1['wins']++;
                $team2['loses']++;
                $team1['points'] += $table['points_win'];
                $team2['points'] += $table['points_loose'];
            } elseif ($match['a_score'] > $match['h_score']) {
                $team2['wins']++;
                $team1['loses']++;
                $team2['points'] += $table['points_win'];
                $team1['points'] += $table['points_loose'];
            } elseif ($match['h_score'] == $match['a_score'] && $match['h_extratime'] == 1) {
                $team1['draws']++;
                $team2['draws']++;
                $team1['points'] += $table['points_win_extratime'];
                $team2['points'] += $table['points_loose_extratime'];
            } elseif ($match['h_score'] == $match['a_score'] && $match['a_extratime'] == 1) {
                $team1['draws']++;
                $team2['draws']++;
                $team2['points'] += $table['points_win_extratime'];
                $team1['points'] += $table['points_loose_extratime'];
            } else {
                $team1['draws']++;
                $team2['draws']++;
                $team1['points'] += $table['points_draw'];
                $team2['points'] += $table['points_draw'];
            }
            $team1['s_score'] += $match['h_score'];
            $team1['r_score'] += $match['a_score'];
            $team2['s_score'] += $match['a_score'];
            $team2['r_score'] += $match['h_score'];

            parent::db()->execute(parent::query()->get('updateTableByIdTeamIdSeasonId', array('matches' => $team1['matches'], 'wins' => $team1['wins'], 'draws' => $team1['draws'], 'loses' => $team1['loses'], 'sScore' => $team1['s_score'], 'rScore' => $team1['r_score'], 'points' => $team1['points'], 'teamId' => $match['h_team'], 'seasonId' => $match['season'], 'tableId' => $match['in_table']), 'sport'));
            parent::db()->execute(parent::query()->get('updateTableByIdTeamIdSeasonId', array('matches' => $team2['matches'], 'wins' => $team2['wins'], 'draws' => $team2['draws'], 'loses' => $team2['loses'], 'sScore' => $team2['s_score'], 'rScore' => $team2['r_score'], 'points' => $team2['points'], 'teamId' => $match['a_team'], 'seasonId' => $match['season'], 'tableId' => $match['in_table']), 'sport'));
        }

        /**
         *
         * 	Edit match.
         * 	C tag.
         * 	
         * 	@param		useFrames				use frames in output
         * 	@param		showMsg					show messages in output
         *
         */
        public function showEditMatchForm($useFrames = false, $showMsg = false) {
            global $dbObject;
            global $webObject;
            $rb = self::rb();
            $match = array();
            $return = '';
            $isValidationError = false;

            if ($_POST['match-edit-save'] == $rb->get('matches.save')) {
                if (UniversalPermission::checkUserPermissions($this->UPDisc, self::getProjectId(), WEB_R_WRITE)) {
                    $match = array();
                    $match['id'] = $_POST['match-id'];
                    $match['h_team'] = $_POST['match-edit-hteam'];
                    $match['h_score'] = $_POST['match-edit-hscore'];
                    $match['h_shoots'] = $_POST['match-edit-hshoots'];
                    $match['h_penalty'] = $_POST['match-edit-hpenalty'];
                    $match['h_extratime'] = ($_POST['match-edit-hextratime'] == 'on') ? 1 : 0;
                    $match['a_team'] = $_POST['match-edit-ateam'];
                    $match['a_score'] = $_POST['match-edit-ascore'];
                    $match['a_shoots'] = $_POST['match-edit-ashoots'];
                    $match['a_penalty'] = $_POST['match-edit-apenalty'];
                    $match['a_extratime'] = ($_POST['match-edit-aextratime'] == 'on') ? 1 : 0;
                    $match['in_table'] = $_POST['match-edit-in-table'];
                    $match['comment'] = $_POST['match-edit-comment'];
                    $match['date'] = $_POST['match-edit-date'];
                    $match['time'] = $_POST['match-edit-time'];
                    $match['refs1'] = $_POST['match-edit-refs1'];
                    $match['refs2'] = $_POST['match-edit-refs2'];
                    $match['place'] = $_POST['match-edit-place'];
                    $match['main_stuff'] = $_POST['match-edit-mainstuff'];
                    $match['stuff1'] = $_POST['match-edit-stuff1'];
                    $match['stuff2'] = $_POST['match-edit-stuff2'];
                    $match['round'] = $_POST['match-edit-round'];
                    $match['notplayed'] = $_POST['match-edit-notplayed'] == 'on' ? 1 : 0;
                    $match['season'] = self::getSeasonId();

                    if ($match['notplayed'] == 1) {
                        $match['h_score'] = -1;
                        $match['h_shoots'] = -1;
                        $match['h_penalty'] = -1;
                        $match['a_score'] = -1;
                        $match['a_shoots'] = -1;
                        $match['a_penalty'] = -1;
                    }

                    $ok = true;
                    if ($match['h_team'] == $match['a_team']) {
                        $ok = false;
                        $return .= parent::getError($rb->get('match.error.sameteams'));
                    }
                    if ($match['notplayed'] == 0) {
                        if (!is_numeric($match['h_score']) || !is_numeric($match['a_score']) || !is_numeric($match['h_shoots']) || !is_numeric($match['a_shoots']) || !is_numeric($match['h_penalty']) || !is_numeric($match['a_penalty']) || !is_numeric($match['round'])) {
                            $ok = false;
                            $return .= parent::getError($rb->get('match.error.isnotnumber'));
                            if ($match['h_score'] < 1 || $match['a_score'] < 1 || $match['h_shoots'] < 1 || $match['a_shoots'] < 1 || $match['h_penalty'] < 1 || $match['a_penalty'] < 1 || $match['round'] < 1) {
                                $return .= parent::getError($rb->get('match.error.islessthanone'));
                            }
                        }
                        if ($match['h_extratime'] == 1 && $match['a_extratime'] == 1) {
                            $ok = false;
                            $return .= parent::getError($rb->get('match.error.bothexwin'));
                        }
                        if (($match['h_extratime'] == 1 || $match['a_extratime'] == 1) && $match['h_score'] != $match['a_score']) {
                            $ok = false;
                            $return .= parent::getError($rb->get('match.error.exwinsamescore'));
                        }
                    }

                    if ($match['in_table'] != 0) {
                        $team1intable = parent::db()->fetchSingle(parent::query()->get('selectTeamFromTableByTeamIdSeasonIdTableId', array('teamId' => $match['h_team'], 'seasonId' => $match['season'], 'tableId' => $match['in_table']), 'sport'));
                        $team2intable = parent::db()->fetchSingle(parent::query()->get('selectTeamFromTableByTeamIdSeasonIdTableId', array('teamId' => $match['a_team'], 'seasonId' => $match['season'], 'tableId' => $match['in_table']), 'sport'));
                        if ($team1intable == array() || $team2intable == array()) {
                            $ok = false;
                            $return .= parent::getError($rb->get('match.error.teamsnotintable'));
                        }
                    }

                    //parent::db()->setMockMode(true);
                    parent::db()->disableCache();
                    if ($ok) {
                        if ($match['id'] != '') {
                            $tmpma = parent::db()->fetchSingle(parent::query()->get('selectMatchByIdSeasonId', array('id' => $match['id'], 'seasonId' => $match['season']), 'sport'));
                        } else {
                            $tmpma = array();
                        }

                        if ($tmpma != array()) {
                            if ($tmpma['in_table'] != 0 && $tmpma['notplayed'] != 1) {
                                $tmpma['season'] = $match['season'];
                                if (!self::removeMatchFromTable($tmpma)) {
                                    $return .= parent::getError($rb->get('match.error.teamsnotintable'));
                                }
                            }
                            parent::db()->execute(parent::query()->get('updateMatchById', array('hTeamId' => $match['h_team'], 'aTeamId' => $match['a_team'], 'hScore' => $match['h_score'], 'aScore' => $match['a_score'], 'hShoots' => $match['h_shoots'], 'aShoots' => $match['a_shoots'], 'hPenalty' => $match['h_penalty'], 'aPenalty' => $match['a_penalty'], 'hExtratime' => $match['h_extratime'], 'aExtratime' => $match['a_extratime'], 'comment' => $match['comment'], 'round' => $match['round'], 'tableId' => $match['in_table'], 'seasonId' => $match['season'], 'id' => $match['id'], 'date' => $match['date'], 'time' => $match['time'], 'refs1' => $match['refs1'], 'refs2' => $match['refs2'], 'place' => $match['place'], 'mainStuff' => $match['main_stuff'], 'stuff1' => $match['stuff1'], 'stuff2' => $match['stuff2'], 'notplayed' => $match['notplayed']), 'sport'));
                        } else {
                            parent::db()->execute(parent::query()->get('insertMatch', array('hTeamId' => $match['h_team'], 'aTeamId' => $match['a_team'], 'hScore' => $match['h_score'], 'aScore' => $match['a_score'], 'hShoots' => $match['h_shoots'], 'aShoots' => $match['a_shoots'], 'hPenalty' => $match['h_penalty'], 'aPenalty' => $match['a_penalty'], 'hExtratime' => $match['h_extratime'], 'aExtratime' => $match['a_extratime'], 'comment' => $match['comment'], 'round' => $match['round'], 'tableId' => $match['in_table'], 'seasonId' => $match['season'], 'projectId' => self::getProjectId(), 'date' => $match['date'], 'time' => $match['time'], 'refs1' => $match['refs1'], 'refs2' => $match['refs2'], 'place' => $match['place'], 'mainStuff' => $match['main_stuff'], 'stuff1' => $match['stuff1'], 'stuff2' => $match['stuff2'], 'notplayed' => $match['notplayed']), 'sport'));
                        }
                        if ($match['in_table'] != 0 && $match['notplayed'] != 1) {
                            self::addMatchToTable($match);
                            if ($match['id'] != '') {
                                parent::db()->execute(parent::query()->get('updateTableIdInStatsByMid', array('tableId' => $match['in_table'], 'mid' => $match['id']), 'sport'));
                            }
                        }
                    } else {
                        $isValidationError = true;
                    }
                    //parent::db()->setMockMode(false);
                    parent::db()->enableCache();
                } else {
                    $return .= parent::getError($rb->get('projects.error.permissionsdenied'));
                }
            }

            if ($_POST['match-edit'] == $rb->get('matches.edit') || $_POST['match-new'] == $rb->get('matches.new') || $isValidationError) {
                if ($_POST['match-edit'] == $rb->get('matches.edit')) {
                    $matchId = $_POST['match-id'];
                    $match = parent::db()->fetchSingle(parent::query()->get('selectMatchByIdProjectId', array('id' => $matchId, 'projectId' => self::getProjectId()), 'sport'));

                    if ($match['notplayed'] == 1) {
                        $match['h_score'] = '';
                        $match['h_shoots'] = '';
                        $match['h_penalty'] = '';
                        $match['a_score'] = '';
                        $match['a_shoots'] = '';
                        $match['a_penalty'] = '';
                    }
                } else if ($_POST['match-new'] == $rb->get('matches.new')) {
                    $match['in_table'] = self::getTableId();
                    $match['notplayed'] = 1;
                }

                $return .= ''
                        . '<div class="match-edit-form">'
                        . '<form name="match-edit-form" method="post" action="' . $_SERVER['REQUEST_URI'] . '">'
                        . '<table class="match-edit-table">'
                        . '<tr>'
                        . '<th class="match-table-team"> </th>'
                        . '<th class="match-table-name">' . $rb->get('matches.name') . '</th>'
                        . '<th class="match-table-score">' . $rb->get('matches.score') . '</th>'
                        . '<th class="match-table-shoots">' . $rb->get('matches.shoots') . '</th>'
                        . '<th class="match-table-penalty">' . $rb->get('matches.penalty') . '</th>'
                        . '<th class="match-table-extratime">' . $rb->get('matches.extratime') . '</th>'
                        . '</tr>'
                        . '<tr>'
                        . '<td class="match-table-team">'
                        . '<label for="match-edit-hteam">' . $rb->get('matches.hteam') . '</label>'
                        . '</td>'
                        . '<td class="match-table-name">'
                        . '<select name="match-edit-hteam" id="match-edit-hteam">'
                        . self::getTeamsOptions($match['h_team'])
                        . '</select>'
                        . '</td>'
                        . '<td class="match-table-score">'
                        . '<input type="text" name="match-edit-hscore" id="match-edit-hscore" value="' . $match['h_score'] . '" />'
                        . '</td>'
                        . '<td class="match-table-shoots">'
                        . '<input type="text" name="match-edit-hshoots" id="match-edit-hshoots" value="' . $match['h_shoots'] . '" />'
                        . '</td>'
                        . '<td class="match-table-penalty">'
                        . '<input type="text" name="match-edit-hpenalty" id="match-edit-hpenalty" value="' . $match['h_penalty'] . '" />'
                        . '</td>'
                        . '<td class="match-table-extratime">'
                        . '<input type="checkbox" name="match-edit-hextratime" id="match-edit-hextratime"' . (($match['h_extratime'] == 1) ? 'checked="checked"' : '') . ' />'
                        . '</td>'
                        . '</tr>'
                        . '<tr>'
                        . '<td class="match-table-team">'
                        . '<label for="match-edit-ateam">' . $rb->get('matches.ateam') . '</label>'
                        . '</td>'
                        . '<td class="match-table-name">'
                        . '<select name="match-edit-ateam" id="match-edit-ateam">'
                        . self::getTeamsOptions($match['a_team'])
                        . '</select>'
                        . '</td>'
                        . '<td class="match-table-score">'
                        . '<input type="text" name="match-edit-ascore" id="match-edit-ascore" value="' . $match['a_score'] . '" />'
                        . '</td>'
                        . '<td class="match-table-shoots">'
                        . '<input type="text" name="match-edit-ashoots" id="match-edit-ashoots" value="' . $match['a_shoots'] . '" />'
                        . '</td>'
                        . '<td class="match-table-penalty">'
                        . '<input type="text" name="match-edit-apenalty" id="match-edit-apenalty" value="' . $match['a_penalty'] . '" />'
                        . '</td>'
                        . '<td class="match-table-extratime">'
                        . '<input type="checkbox" name="match-edit-aextratime" id="match-edit-aextratime"' . (($match['a_extratime'] == 1) ? 'checked="checked"' : '') . ' />'
                        . '</td>'
                        . '</tr>'
                        . '</table>'
                        . '<div class="gray-box">'
                        . '<label class="w160" for="match-edit-notplayed">' . $rb->get('match.notplayedyet') . ': <span class="red">*</span></label>'
                        . '<input type="checkbox" name="match-edit-notplayed" id="match-edit-notplayed" ' . ($match['notplayed'] == 1 ? 'checked="checked" ' : '') . '/>'
                        . '<script type="text/javascript" src="~/js/sportMatchCheckbox.js"></script>'
                        . '</div>'
                        . '<div class="match-edit-comment">'
                        . '<label form="match-edit-comment">' . $rb->get('match.comment') . ':</label>'
                        . '<textarea name="match-edit-comment" id="match-edit-comment" rows="4">'
                        . $match['comment']
                        . '</textarea>'
                        . '</div>'
                        . '<div class="match-edit-round gray-box">'
                        . '<label for="match-edit-round" class="w160">' . $rb->get('match.round') . ':</label> '
                        . '<select name="match-edit-round" id="match-edit-round">'
                        . self::getRoundsOptions($match['round'])
                        . '</select>'
                        . '</div>'
                        . '<div class="gray-box">'
                        . '<label for="match-edit-in-table" class="w160">' . $rb->get('match.intable') . ':</label> '
                        . '<select name="match-edit-in-table" id="match-edit-in-table">'
                        . '<option value="0">' . $rb->get('matches.nottotable') . '</option>'
                        . self::getTablesOptions($match['in_table'])
                        . '</select>'
                        . '</div>'
                        . '<div class="gray-box">'
                        . '<label class="w160" for="match-edit-date">' . $rb->get('match.datetime') . ':</label>'
                        . '<input type="text" class="w80" name="match-edit-date" id="match-edit-date" value="' . $match['date'] . '" /> '
                        . '<input type="text" class="w80" name="match-edit-time" id="match-edit-time" value="' . $match['time'] . '" />'
                        /* .'<link rel="stylesheet" type="text/css"  href="~/scripts/js/jquery-ui/css/jquery.ui.all.css" />'
                        .'<script type="text/javascript" src="~/scripts/js/jquery/jquery.js"></stript>'
                        .'<script type="text/javascript" src="~/scripts/js/jquery-ui/jquery.ui.core.min.js"></stript>'
                        .'<script type="text/javascript" src="~/scripts/js/jquery-ui/jquery.ui.widget.min.js"></stript>'
                        .'<script type="text/javascript" src="~/scripts/js/jquery-ui/jquery.ui.datepicker.min.js"></stript>'
                        .'<script type="text/javascript">'
                        .'$(function() { '
                        .'var dp = $("#match-edit-datetime");'
                        .'dp.datepicker();'
                        .'dp.datepicker("option", {dateFormat: "dd.mm.yy"});'
                        .($webObject->LanguageName != '' ? '$.datepicker.setDefaults($.datepicker.regional["'.$webObject->LanguageName.'"]);' : '')
                        .' });'
                        .'</script>' */
                        . '</div>'
                        . '<div class="gray-box">'
                        . '<label class="w160" for="match-edit-refs1">' . $rb->get('match.refs') . ':</label>'
                        . '<input class="w200" type="text" name="match-edit-refs1" id="match-edit-refs1" value="' . $match['refs'] . '" /> '
                        . '<input class="w200" type="text" name="match-edit-refs2" id="match-edit-refs2" value="' . $match['refs2'] . '" />'
                        . '</div>'
                        . '<div class="gray-box">'
                        . '<label class="w160" for="match-edit-mainstuff">' . $rb->get('match.mainstuff') . ':</label>'
                        . '<input class="w400" type="text" name="match-edit-mainstuff" id="match-edit-mainstuff" value="' . $match['main_stuff'] . '" />'
                        . '</div>'
                        . '<div class="gray-box">'
                        . '<label class="w160" for="match-edit-mainstuff">' . $rb->get('match.stuff') . ':</label>'
                        . '<input class="w200" type="text" name="match-edit-stuff1" id="match-edit-stuff1" value="' . $match['stuff'] . '" /> '
                        . '<input class="w200" type="text" name="match-edit-stuff2" id="match-edit-stuff2" value="' . $match['stuff2'] . '" />'
                        . '</div>'
                        . '<div class="gray-box">'
                        . '<label class="w160" for="match-edit-place">' . $rb->get('match.place') . ':</label>'
                        . '<input class="w400" type="text" name="match-edit-place" id="match-edit-place" value="' . $match['place'] . '" />'
                        . '</div>'
                        . '<div class="gray-box">'
                        . '<span class="red">*</span> ' . $rb->get('match.notplayedyetcap')
                        . '</div>'
                        . '<div class="match-edit-submit">'
                        . '<input type="hidden" name="match-id" value="' . $match['id'] . '" />'
                        . '<input type="submit" name="match-edit-save" value="' . $rb->get('matches.save') . '" />'
                        . '</div>'
                        . '</form>'
                        . '</div>'
                        . '<div class="clear"></div>';

                if ($useFrames == "false") {
                    return $return;
                } else {
                    return parent::getFrame($rb->get('matches.form.title'), $return, "", true);
                }
            }
        }

        /**
         *
         * 	Edit match.
         * 	C tag.
         *
         * 	@param		useFrames				use frames in output
         * 	@param		showMsg					show messages in output
         *
         */
        public function showEditStatsForm($useFrames = false, $showMsg = false) {
            global $dbObject;
            $rb = self::rb();
            $ok = true;
            $return = '';

            if ($_POST['match-stats-save'] == $rb->get('matches.stats.save')) {
                if (UniversalPermission::checkUserPermissions($this->UPDisc, self::getProjectId(), WEB_R_WRITE)) {
                    //echo '<pre>';
                    //print_r($_POST);
                    //echo '</pre>';

                    $matchId = $_POST['match-id'];
                    $playerId1 = $_POST['match-stats-player-id1'];
                    $inmatch1 = $_POST['match-stats-inmatch1'];
                    $goals1 = $_POST['match-stats-goals1'];
                    $assists1 = $_POST['match-stats-assists1'];
                    $shoots1 = $_POST['match-stats-shoots1'];
                    $penalty1 = $_POST['match-stats-penalty1'];
                    $positions1 = $_POST['match-stats-positions1'];

                    $playerId2 = $_POST['match-stats-player-id2'];
                    $inmatch2 = $_POST['match-stats-inmatch2'];
                    $goals2 = $_POST['match-stats-goals2'];
                    $assists2 = $_POST['match-stats-assists2'];
                    $shoots2 = $_POST['match-stats-shoots2'];
                    $penalty2 = $_POST['match-stats-penalty2'];
                    $positions2 = $_POST['match-stats-positions2'];

                    $match = $dbObject->fetchAll('SELECT `id`, `h_team`, `a_team`, `h_score`, `a_score`, `h_shoots`, `a_shoots`, `h_penalty`, `a_penalty`, `h_extratime`, `a_extratime`, `comment`, `round`, `season`, `in_table` FROM `w_sport_match` WHERE `id` = ' . $matchId . ';');
                    if (count($match) > 0) {
                        $match = $match[0];
                        // Kontrola integrity dat: ---
                        /* $goa1 = 0;
                        foreach($goals1 as $gl) {
                        $goa1 += $gl;
                        }
                        $goa2 = 0;
                        foreach($goals2 as $gl) {
                        $goa2 += $gl;
                        }
                        if($goa1 != $match['h_team']) {
                        $ok = false;
                        $return .= '<h4 class="error">'.$rb->get('matches.stats.error.wronggoalscounth').'</h4>';
                        }
                        if($goa2 != $match['a_team']) {
                        $ok = false;
                        $return .= '<h4 class="error">'.$rb->get('matches.stats.error.wronggoalscounta').'</h4>';
                        }
                        $pen1 = 0;
                        foreach($penalty1 as $pe) {
                        $pen1 += $pe;
                        }
                        $pen2 = 0;
                        foreach($penalty2 as $pe) {
                        $pen2 += $pe;
                        }
                        if($pen1 != $match['h_penalty']) {
                        $ok = false;
                        $return .= '<h4 class="error">'.$rb->get('matches.stats.error.wrongpenaltycounth').'</h4>';
                        }
                        if($goa2 != $match['a_penalty']) {
                        $ok = false;
                        $return .= '<h4 class="error">'.$rb->get('matches.stats.error.wrongpenaltycounta').'</h4>';
                        } */
                        // Konec ---

                        if ($ok) {
                            $dbObject->execute('DELETE FROM `w_sport_stats` WHERE `mid` = ' . $match['id'] . ' AND `season` = ' . self::getSeasonId() . ';');
                            foreach ($playerId1 as $key => $pl) {
                                if ($pl != '' && $inmatch1[$key] == 'on') {
                                    $goals = (($goals1[$key] != '') ? $goals1[$key] : 0);
                                    $assists = (($assists1[$key] != '') ? $assists1[$key] : 0);
                                    $shoots = (($shoots1[$key] != '') ? $shoots1[$key] : 0);
                                    $penalty = (($penalty1[$key] != '') ? $penalty1[$key] : 0);
                                    $pos = (($positions1[$key] != '') ? $positions1[$key] : 0);


                                    $stats = $dbObject->fetchAll('SELECT `pid` FROM `w_sport_stats` WHERE `pid` = ' . $pl . ' AND `mid` = ' . $match['id'] . ' AND `season` = ' . self::getSeasonId() . ';');
                                    if (count($stats) == 0) {
                                        $dbObject->execute('INSERT INTO `w_sport_stats`(`pid`, `pos`, `mid`, `season`, `goals`, `assists`, `shoots`, `penalty`, `table_id`, `project_id`) VALUES (' . $pl . ', ' . $pos . ', ' . $match['id'] . ', ' . self::getSeasonId() . ', ' . $goals . ', ' . $assists . ', ' . $shoots . ', ' . $penalty . ', ' . $match['in_table'] . ', ' . self::getProjectId() . ');');
                                        $return .= '<h4 class="success">Saved!</h4>';
                                    } else {

                                    }
                                }
                            }
                            foreach ($playerId2 as $key => $pl) {
                                if ($pl != '' && $inmatch2[$key] == 'on') {
                                    $goals = (($goals2[$key] != '') ? $goals2[$key] : 0);
                                    $assists = (($assists2[$key] != '') ? $assists2[$key] : 0);
                                    $shoots = (($shoots2[$key] != '') ? $shoots2[$key] : 0);
                                    $penalty = (($penalty2[$key] != '') ? $penalty2[$key] : 0);
                                    $pos = (($positions2[$key] != '') ? $positions2[$key] : 0);

                                    $stats = $dbObject->fetchAll('SELECT `pid` FROM `w_sport_stats` WHERE `pid` = ' . $pl . ' AND `mid` = ' . $match['id'] . ' AND `season` = ' . self::getSeasonId() . ';');
                                    if (count($stats) == 0) {
                                        $dbObject->execute('INSERT INTO `w_sport_stats`(`pid`, `pos`, `mid`, `season`, `goals`, `assists`, `shoots`, `penalty`, `table_id`, `project_id`) VALUES (' . $pl . ', ' . $pos . ', ' . $match['id'] . ', ' . self::getSeasonId() . ', ' . $goals . ', ' . $assists . ', ' . $shoots . ', ' . $penalty . ', ' . $match['in_table'] . ', ' . self::getProjectId() . ');');
                                        $return .= '<h4 class="success">Saved!</h4>';
                                    } else {

                                    }
                                }
                            }
                        } else {
                            $_POST['match-stats-add'] = $rb->get('matches.statsadd');
                        }
                    }
                } else {
                    $return .= parent::getError($rb->get('projects.error.permissionsdenied'));
                }
            }

            if ($_POST['match-stats-edit'] == $rb->get('matches.statsedit') || $_POST['match-stats-add'] == $rb->get('matches.statsadd')) {
                //$return .= '<h4 class="warning">Coming soon ...</h4>';

                $matchId = $_POST['match-id'];
                $seasonId = self::getSeasonId();
                $match = $dbObject->fetchAll('SELECT `id`, `h_team`, `a_team`, `h_score`, `a_score`, `h_shoots`, `a_shoots`, `h_penalty`, `a_penalty`, `h_extratime`, `a_extratime`, `comment`, `round`, `season` FROM `w_sport_match` WHERE `id` = ' . $matchId . ';');
                $match = $match[0];
                $home = $dbObject->fetchAll('SELECT `name` FROM `w_sport_team` WHERE `id` = ' . $match['h_team'] . ';');
                $away = $dbObject->fetchAll('SELECT `name` FROM `w_sport_team` WHERE `id` = ' . $match['a_team'] . ';');

                $players1 = $dbObject->fetchAll('SELECT distinct `id`, `name`, `surname` FROM `w_sport_player` WHERE `team` = ' . $match['h_team'] . ' AND `season` = ' . $seasonId . ';');
                $players2 = $dbObject->fetchAll('SELECT distinct `id`, `name`, `surname` FROM `w_sport_player` WHERE `team` = ' . $match['a_team'] . ' AND `season` = ' . $seasonId . ';');

                $playersStr1 = $playersStr2 = ''
                        . '<table class="match-stats-table">'
                        . '<tr>'
                        . '<th class="match-stats-name">' . $rb->get('matches.stats.name') . ':</th>'
                        . '<th class="match-stats-inmatch">' . $rb->get('matches.stats.inmatch') . ':</th>'
                        . '<th class="match-stats-onposition">' . $rb->get('matches.stats.onposition') . ':</th>'
                        . '<th class="match-stats-goals">' . $rb->get('matches.stats.goals') . ':</th>'
                        . '<th class="match-stats-assists">' . $rb->get('matches.stats.assists') . ':</th>'
                        . '<th class="match-stats-shoots">' . $rb->get('matches.stats.shoots') . ':</th>'
                        . '<th class="match-stats-penalty">' . $rb->get('matches.stats.penalty') . ':</th>'
                        . '</tr>';

                $i = 1;
                foreach ($players1 as $pl) {
                    $tmpstats = $dbObject->fetchAll('SELECT `goals`, `assists`, `penalty`, `shoots`, `pos` FROM `w_sport_stats` WHERE `pid` = ' . $pl['id'] . ' AND `mid` = ' . $match['id'] . ' AND `season` = ' . $seasonId . ';');
                    $stats = $tmpstats[0];
                    $selPos1 = 0;
                    $plpos = parent::db()->fetchSingle('select `position` from `w_sport_player` where `id` = ' . $pl['id'] . ' and `season` = ' . $seasonId . ' and `team` = ' . $match['h_team'] . ';');
                    $pl['position'] = $plpos['position'];
                    if ($stats == array()) {
                        $selPos1 = $pl['position'];
                    } else {
                        $selPos1 = $stats['pos'];
                    }
                    $playersStr1 .= ''
                            . '<tr class="' . ((($i % 2) == 1) ? 'idle' : 'even') . '">'
                            . '<td class="match-stats-name">'
                            . '<label for="match-stats-player' . $pl['id'] . '-checkbox">'
                            . $pl['name'] . ' ' . $pl['surname']
                            //.($pl['position'] == 1 ? ' <strong>[B]</strong>' : '')
                            . '</label>'
                            . '</td>'
                            . '<td class="match-stats-inmatch">'
                            . '<input type="hidden" name="match-stats-player-id1[' . $pl['id'] . ']" value="' . $pl['id'] . '" />'
                            . '<input type="checkbox" id="match-stats-player' . $pl['id'] . '-checkbox" name="match-stats-inmatch1[' . $pl['id'] . ']"' . ((count($tmpstats) == 1) ? ' checked="checked"' : '') . ' />'
                            . '</td>'
                            . '<td class="match-stats-position">'
                            . '<select name="match-stats-positions1[' . $pl['id'] . ']">'
                            . '<option value="1"' . ($selPos1 == 1 ? ' selected="selected"' : '') . '>' . $rb->get('player.position-goa') . '</option>'
                            . '<option value="2"' . ($selPos1 == 2 ? ' selected="selected"' : '') . '>' . $rb->get('player.position-def') . '</option>'
                            . '<option value="3"' . ($selPos1 == 3 ? ' selected="selected"' : '') . '>' . $rb->get('player.position-att') . '</option>'
                            . '</select>'
                            . '</td>'
                            . '<td class="match-stats-goals">'
                            . '<input type="text" name="match-stats-goals1[' . $pl['id'] . ']" value="' . $stats['goals'] . '" />'
                            . '</td>'
                            . '<td class="match-stats-assists">'
                            . '<input type="text" name="match-stats-assists1[' . $pl['id'] . ']" value="' . $stats['assists'] . '" />'
                            . '</td>'
                            . '<td class="match-stats-shoots">'
                            . '<input type="text" name="match-stats-shoots1[' . $pl['id'] . ']" value="' . $stats['shoots'] . '" />'
                            . '</td>'
                            . '<td class="match-stats-penalty">'
                            . '<input type="text" name="match-stats-penalty1[' . $pl['id'] . ']" value="' . $stats['penalty'] . '" />'
                            . '</td>'
                            . '</tr>';
                    $i++;
                }

                $i = 1;
                foreach ($players2 as $pl) {
                    $tmpstats = $dbObject->fetchAll('SELECT `goals`, `assists`, `penalty`, `shoots`, `pos` FROM `w_sport_stats` WHERE `pid` = ' . $pl['id'] . ' AND `mid` = ' . $match['id'] . ' AND `season` = ' . $seasonId . ';');
                    $stats = $tmpstats[0];
                    $selPos1 = 0;
                    $plpos = parent::db()->fetchSingle('select `position` from `w_sport_player` where `id` = ' . $pl['id'] . ' and `season` = ' . $seasonId . ' and `team` = ' . $match['a_team'] . ';');
                    $pl['position'] = $plpos['position'];
                    if ($stats == array()) {
                        $selPos1 = $pl['position'];
                    } else {
                        $selPos1 = $stats['pos'];
                    }
                    $playersStr2 .= ''
                            . '<tr class="' . ((($i % 2) == 1) ? 'idle' : 'even') . '">'
                            . '<td class="match-stats-name">'
                            . '<label for="match-stats-player' . $pl['id'] . '-checkbox">'
                            . $pl['name'] . ' ' . $pl['surname']
                            //.($pl['position'] == 1 ? ' <strong>[B]</strong>' : '')
                            . '</label>'
                            . '</td>'
                            . '<td class="match-stats-inmatch">'
                            . '<input type="hidden" name="match-stats-player-id2[' . $pl['id'] . ']" value="' . $pl['id'] . '" />'
                            . '<input type="checkbox" id="match-stats-player' . $pl['id'] . '-checkbox" name="match-stats-inmatch2[' . $pl['id'] . ']"' . ((count($tmpstats) == 1) ? ' checked="checked"' : '') . ' />'
                            . '</td>'
                            . '<td class="match-stats-position">'
                            . '<select name="match-stats-positions2[' . $pl['id'] . ']">'
                            . '<option value="1"' . ($selPos1 == 1 ? ' selected="selected"' : '') . '>' . $rb->get('player.position-goa') . '</option>'
                            . '<option value="2"' . ($selPos1 == 2 ? ' selected="selected"' : '') . '>' . $rb->get('player.position-def') . '</option>'
                            . '<option value="3"' . ($selPos1 == 3 ? ' selected="selected"' : '') . '>' . $rb->get('player.position-att') . '</option>'
                            . '</select>'
                            . '</td>'
                            . '<td class="match-stats-goals">'
                            . '<input type="text" name="match-stats-goals2[' . $pl['id'] . ']" value="' . $stats['goals'] . '" />'
                            . '</td>'
                            . '<td class="match-stats-assists">'
                            . '<input type="text" name="match-stats-assists2[' . $pl['id'] . ']" value="' . $stats['assists'] . '" />'
                            . '</td>'
                            . '<td class="match-stats-shoots">'
                            . '<input type="text" name="match-stats-shoots2[' . $pl['id'] . ']" value="' . $stats['shoots'] . '" />'
                            . '</td>'
                            . '<td class="match-stats-penalty">'
                            . '<input type="text" name="match-stats-penalty2[' . $pl['id'] . ']" value="' . $stats['penalty'] . '" />'
                            . '</td>'
                            . '</tr>';
                    $i++;
                }

                $playersStr1 .= ''
                        . '</table>';
                $playersStr2 .= ''
                        . '</table>';

                $return .= ''
                        . '<div class="match-edit-form">'
                        . '<form name="match-edit-form" method="post" action="' . $_SERVER['REQUEST_URI'] . '">'
                        . '<table class="match-edit-table">'
                        . '<tr>'
                        . '<th class="match-table-team"> </th>'
                        . '<th class="match-table-name">' . $rb->get('matches.name') . '</th>'
                        . '<th class="match-table-score">' . $rb->get('matches.score') . '</th>'
                        . '<th class="match-table-shoots">' . $rb->get('matches.shoots') . '</th>'
                        . '<th class="match-table-penalty">' . $rb->get('matches.penalty') . '</th>'
                        . '<th class="match-table-extratime">' . $rb->get('matches.extratime') . '</th>'
                        . '</tr>'
                        . '<tr>'
                        . '<td class="match-table-team">'
                        . '<label for="match-edit-hteam">' . $rb->get('matches.hteam') . '</label>'
                        . '</td>'
                        . '<td class="match-table-name">'
                        . $home[0]['name']
                        . '</td>'
                        . '<td class="match-table-score">'
                        . $match['h_score']
                        . '</td>'
                        . '<td class="match-table-shoots">'
                        . $match['h_shoots']
                        . '</td>'
                        . '<td class="match-table-penalty">'
                        . $match['h_penalty']
                        . '</td>'
                        . '<td class="match-table-extratime">'
                        . (($match['h_extratime'] == 1) ? $rb->get('matches.form.homeexwin') : '')
                        . '</td>'
                        . '</tr>'
                        . '<tr>'
                        . '<td class="match-table-team">'
                        . $rb->get('matches.ateam')
                        . '</td>'
                        . '<td class="match-table-name">'
                        . $away[0]['name']
                        . '</td>'
                        . '<td class="match-table-score">'
                        . $match['a_score']
                        . '</td>'
                        . '<td class="match-table-shoots">'
                        . $match['a_shoots']
                        . '</td>'
                        . '<td class="match-table-penalty">'
                        . $match['a_penalty']
                        . '</td>'
                        . '<td class="match-table-extratime">'
                        . (($match['a_extratime'] == 1) ? $rb->get('matches.form.awayexwin') : '')
                        . '</td>'
                        . '</tr>'
                        . '</table>'
                        . $playersStr1
                        . $playersStr2
                        . '<div class="match-edit-submit">'
                        . '<input type="hidden" name="match-id" value="' . $match['id'] . '" />'
                        . '<input type="submit" name="match-stats-save" value="' . $rb->get('matches.stats.save') . '" />'
                        . '</div>'
                        . '</form>'
                        . '</div>'
                        . '<div class="clear"></div>';

                if ($useFrames == "false") {
                    return $return;
                } else {
                    $round = parent::db()->fetchSingle(parent::query()->get('roundById', array('id' => $match['round']), 'sport'));
                    return parent::getFrame($rb->get('matches.stats.title') . ', ' . $rb->get('match.round') . ': ' . $round['name'] . ', ' . $home[0]['name'] . ' : ' . $away[0]['name'], $return, "", true);
                }
            }
        }

        /**
         *
         * 	List of rounds for editing.
         * 	C tag.
         *
         * 	@param		pageId					next page id
         * 	@param		useFrames				use frames in output
         * 	@param		showMsg					show messages in output
         *
         */
        public function showEditRounds($pageId = false, $useFrames = false, $showMsg = false) {
            global $dbObject;
            global $webObject;
            $rb = self::rb();
            $retrun = '';

            $projectId = self::getProjectId();
            $seasonId = self::getSeasonId();

            if ($_POST['round-delete'] == $rb->get('rounds.delete')) {
                $roundId = $_POST['round-id'];

                parent::db()->execute(parent::query()->get('roundDeleteById', array('id' => $roundId), 'sport'));
                $return .= parent::getSuccess($rb->get('rounds.deleted'));
            }

            if ($projectId != '-1' && $seasonId != '-1') {
                $data = parent::db()->fetchAll(parent::query()->get('roundsByProjectIdSeasonId', array('projectId' => $projectId, 'seasonId' => $seasonId), 'sport'));
                if (count($data) > 0) {
                    foreach ($data as $key => $d) {
                        $form = ''
                            . '<form name="rounds-edit" method="post" action="' . $_SERVER['REQUEST_URI'] . '"> '
                                . '<input type="hidden" name="round-id" value="' . $d['id'] . '" /> '
                                . '<input type="hidden" name="round-edit" value="' . $rb->get('rounds.edit') . '" /> '
                                . '<input type="image" src="~/images/page_edi.png" name="round-edit" value="' . $rb->get('rounds.edit') . '" title="' . $rb->get('rounds.editcap') . ', id=' . $d['id'] . '" />'
                            . '</form> '
                            . '<form name="rounds-delete" method="post" action="' . $_SERVER['REQUEST_URI'] . '"> '
                                . '<input type="hidden" name="round-id" value="' . $d['id'] . '" /> '
                                . '<input type="hidden" name="round-delete" value="' . $rb->get('rounds.delete') . '" /> '
                                . '<input class="confirm" type="image" src="~/images/page_del.png" name="round-delete" value="' . $rb->get('rounds.delete') . '" title="' . $rb->get('rounds.deletecap') . ', id=' . $d['id'] . '" /> '
                            . '</form>';
                        $data[$key]['form'] = $form;
                        $data[$key]['visible'] = ($data[$key]['visible'] == 1 ? $rb->get('rounds.yes') : $rb->get('rounds.no'));
                    }

                    $grid = new BaseGrid();
                    $grid->setHeader(array('id' => $rb->get('rounds.id'), 'number' => $rb->get('rounds.number'), 'name' => $rb->get('rounds.name'), 'visible' => $rb->get('rounds.visible'), 'form' => ''));
                    $grid->addRows($data);
                    $return .= $grid->render();
                } else {
                    $return .= parent::getWarning($rb->get('rounds.nodata'));
                }

                $return .= ''
                        . '<hr />'
                        . '<form name="match-new" method="post" action="' . $SERVER['REQUEST_URI'] . '">'
                        . '<input type="submit" name="round-new" value="' . $rb->get('rounds.new') . '" title="' . $rb->get('rounds.newcap') . '" />'
                        . '</form>';
                //$newForm = new BaseForm();
                //$newForm->setFormAttrs('round-new', 'post', $_SERVER['REQUEST_URI'])
                //$newForm->addSubmit('rounds-new');
            } else {
                $return .= parent::getError($rb->get('rounds.seasonprojectnotset'));
            }

            if ($useFrames == "false") {
                return $return;
            } else {
                return parent::getFrame($rb->get('rounds.title'), $return, "", true);
            }
        }

        /**
         *
         * 	Edit round.
         * 	C tag.
         * 	
         * 	@param		useFrames				use frames in output
         * 	@param		showMsg					show messages in output
         *
         */
        public function showEditRoundForm($useFrames = false, $showMsg = false) {
            global $dbObject;
            global $webObject;
            $rb = self::rb();
            $round = array();
            $return = '';
            $ok = true;

            $form = new BaseForm();
            $form->setFormAttrs('edit-round', 'post', $_SERVER['REQUEST_URI']);
            $form->addSubmit('round-save', $rb->get('round.save'));

            if ($form->isSubmited()) {
                if (UniversalPermission::checkUserPermissions($this->UPDisc, self::getProjectId(), WEB_R_WRITE)) {
                    $round['id'] = $form->getValue('round-id');
                    $round['number'] = trim($form->getValue('number'));
                    $round['name'] = trim($form->getValue('name'));
                    $round['visible'] = (trim($form->getValue('visible')) == 'on' ? 1 : 0);
                    $round['season_id'] = self::getSeasonId();
                    $round['project_id'] = self::getProjectId();

                    //parent::db()->setMockMode(true);
                    if ($round['id'] != '') {
                        $roothnum = parent::db()->fetchAll('select `id` from `w_sport_round` where `number` = ' . $round['number'] . ' and `id` != ' . $round['id'] . ' and `season_id` = ' . $round['season_id'] . ';');
                    } else {
                        $roothnum = parent::db()->fetchAll('select `id` from `w_sport_round` where `number` = ' . $round['number'] . ' and `season_id` = ' . $round['season_id'] . ';');
                    }
                    if (count($roothnum) > 0) {
                        $return .= parent::getError($rb->get('rounds.numberunique'));
                        $ok = false;
                    }
                    if ($round['name'] == '') {
                        $return .= parent::getError($rb->get('rounds.namenotempty'));
                        $ok = false;
                    }

                    if ($ok) {
                        if ($round['id'] != '') {
                            parent::db()->execute(parent::query()->get('updateRound', $round, 'sport'));
                        } else {
                            parent::db()->execute(parent::query()->get('insertRound', $round, 'sport'));
                        }
                    }
                    //parent::db()->setMockMode(false);
                } else {
                    $return .= parent::getError($rb->get('projects.error.permissionsdenied'));
                }
            }

            if ($_POST['round-edit'] == $rb->get('rounds.edit') || $_POST['round-new'] == $rb->get('rounds.new') || $ok == false) {
                $round == array();
                if ($_POST['round-edit'] == $rb->get('rounds.edit')) {
                    $roundId = $_POST['round-id'];
                    $round = parent::db()->fetchSingle(parent::query()->get('roundById', array('id' => $roundId), 'sport'));
                } else {
                    $round['visible'] = true;
                }

                $form->addField('text', 'number', $rb->get('rounds.number'), $round['number'], 'w160', 'w80');
                $form->addField('text', 'name', $rb->get('rounds.name'), $round['name'], 'w160', 'w200');
                $form->addField('singlecheckbox', 'visible', $rb->get('rounds.visible'), ($round['visible'] == 1 ? true : false), 'w160');
                $form->addField('hidden', 'round-id', '', $round['id']);

                $return .= $form->render();

                if ($useFrames == "false") {
                    return $return;
                } else {
                    return parent::getFrame($rb->get('round.title'), $return, "", true);
                }
            }
        }

        /**
         *
         * 	Shows seasons.
         * 	C tag.
         *
         * 	@param		templateId			template id
         * 	@param		sorting					ASC or DESC
         *
         */
        public function showSeasons($templateId, $sorting, $noDataMessage) {
            global $dbObject;
            global $loginObject;
            $rb = self::rb();
            $return = '';

            if (strtolower($sorting) == 'asc') {
                $sorting = 'ASC';
            } else {
                $sorting = 'DESC';
            }

            if (!UniversalPermission::checkUserPermissions($this->UPDisc, self::getProjectId(), WEB_R_READ)) {
                return parent::getWarning($noDataMessage);
            }
            $seasons = $dbObject->fetchAll('SELECT `id`, `start_year`, `end_year` FROM `w_sport_season` where `project_id` = ' . self::getProjectId() . ' ORDER BY `start_year` ' . $sorting . ';');
            if (count($seasons) > 0) {
                $content = parent::getTemplateContent($templateId);
                $parser = new FullTagParser();
                $i = 0;
                $prevId = self::getSeasonId();
                foreach ($seasons as $season) {
                    parent::request()->set('season', $season, 'sport-data');
                    parent::request()->set('i', $i, 'sport-data');
                    self::setSeasonId($season['id']);

                    $parser->setContent($content);
                    $parser->startParsing();
                    $return .= $parser->getResult();
                    $i++;
                }
                self::setSeasonId($prevId);
            } else {
                $return .= parent::getWarning($noDataMessage);
            }

            return $return;
        }

        /**
         *
         * 	Shows season.
         * 	C tag.
         *
         * 	@param		field						field name to show
         * 	@param		seasonId				season id
         * 	@param		errMsg					error msg
         *
         */
        public function showSeason($field, $seasonId = false, $errMsg = false) {
            global $dbObject;
            $rb = self::rb();
            $return = '';
            $data = array();

            if (!UniversalPermission::checkUserPermissions($this->UPDisc, self::getProjectId(), WEB_R_READ)) {
                return parent::getWarning($noDataMessage);
            }

            if ($seasonId == '') {
                $seasonId = self::getSeasonId();
            }

            if ($seasonId != '-1') {
                $data = parent::db()->fetchSingle('select `start_year`, `end_year` from `w_sport_season` where `id` = ' . $seasonId . ';');
            } else {
                if (parent::request()->exists('season', 'sport-data')) {
                    $data = parent::request()->get('season', 'sport-data');
                } else {
                    $return .= parent::getError($rb->get('season.error.seasonnotset'));
                }
            }

            if ($data != array()) {
                switch (strtolower($field)) {
                    case 'row': $return .= ( ((self::request()->get('i', 'sport-data') % 2) == 1) ? 'idle' : 'even');
                        break;
                    case 'i': $return .= self::request()->get('i', 'sport-data');
                        break;
                    case 'id': $return .= $data['id'];
                        break;
                    case 'start_year': $return .= $data['start_year'];
                        break;
                    case 'end_year': $return .= $data['end_year'];
                        break;
                    default: $return .= parent::getError($rb->get('season.error.incorrectfield'));
                }
            } else {
                $return .= parent::getError($rb->get('season.error.seasondoesntexist'));
            }

            return $return;
        }

        /**
         *
         * 	Shows table for selected season.
         * 	C tag.
         *
         * 	@param		seasonId				season to show
         * 	@param		useFrames				use frames in output
         * 	@param		showMsg					show messages in output
         *
         */
        public function showTable($seasonId = false, $tableId = false, $editable = false, $useFrames = false, $showMsg = false, $thenByFix = false) {
            global $dbObject;
            global $loginObject;
            $rb = self::rb();
            $return = '';

            if (!UniversalPermission::checkUserPermissions($this->UPDisc, self::getProjectId(), WEB_R_READ)) {
                return parent::getWarning($noDataMessage);
            }

            if (($seasonId != '' || self::getSeasonId() != '-1') && ($tableId != '' || self::getTableId() != '-1')) {
                if ($seasonId == '') {
                    $seasonId = self::getSeasonId();
                }
                if ($tableId == '') {
                    $tableId = self::getTableId();
                }

                if (UniversalPermission::checkUserPermissions($this->UPDisc, self::getProjectId(), WEB_R_WRITE) && $_POST['table-c-save'] == $rb->get('table.submit')) {
                    foreach ($_POST['table-matches'] as $key => $matches) {
                        $team['id'] = $key;
                        $team['matches'] = $matches;
                        $team['wins'] = $_POST['table-wins'][$key];
                        $team['draws'] = $_POST['table-draws'][$key];
                        $team['loses'] = $_POST['table-loses'][$key];
                        $team['s_score'] = $_POST['table-s_score'][$key];
                        $team['r_score'] = $_POST['table-r_score'][$key];
                        $team['points'] = $_POST['table-points'][$key];
                        $team['positionfix'] = $_POST['table-positionfix'][$key];

                        parent::db()->execute('update `w_sport_table` set `matches` = ' . $team['matches'] . ', `wins` = ' . $team['wins'] . ', `draws` = ' . $team['draws'] . ', `loses` = ' . $team['loses'] . ', `s_score` = ' . $team['s_score'] . ', `r_score` = ' . $team['r_score'] . ', `points` = ' . $team['points'] . ', `positionfix` = '.$team['positionfix'].' where `team` = ' . $team['id'] . ' and `season` = ' . $seasonId . ' and `table_id` = ' . $tableId . ';');
                    }
                }

                $orderBy = ' ORDER BY `points` DESC, ';
                if($thenByFix) {
                    $orderBy .= '`w_sport_table`.`positionfix` DESC, ';
                }
                $orderBy .= '(CAST(`w_sport_table`.`s_score` AS SIGNED) - CAST(`w_sport_table`.`r_score` AS SIGNED)) DESC, `w_sport_table`.`s_score` DESC, `w_sport_table`.`wins` DESC';
                
                $table = $dbObject->fetchAll('SELECT `w_sport_team`.`id`, `w_sport_team`.`name`, `w_sport_table`.`matches`, `w_sport_table`.`wins`, `w_sport_table`.`draws`, `w_sport_table`.`loses`, `w_sport_table`.`s_score`, `w_sport_table`.`r_score`, `w_sport_table`.`points`, `w_sport_table`.`positionfix` FROM `w_sport_table` LEFT JOIN `w_sport_team` ON `w_sport_table`.`team` = `w_sport_team`.`id` WHERE `w_sport_table`.`season` = ' . $seasonId . ' AND `w_sport_team`.`season` = ' . $seasonId . ' AND `w_sport_table`.`table_id` = ' . $tableId . ' and `w_sport_table`.`project_id` = ' . self::getProjectId() . $orderBy .';');
                if (count($table) > 0) {
                    if ($editable == 'true') {
                        $return .= '<form name="table-c-edit" method="post" action="' . $_SERVER['REQUEST_URI'] . '">';
                    }

                    $return .= ''
                            . '<table class="standart">'
                            . '<tr>'
                            . '<th class="table-position">' . $rb->get('table.position') . '</th>'
                            . '<th class="table-name">' . $rb->get('table.name') . '</th>'
                            . '<th class="table-matches">' . $rb->get('table.matches') . '</th>'
                            . '<th class="table-wins">' . $rb->get('table.wins') . '</th>'
                            . '<th class="table-draws">' . $rb->get('table.draws') . '</th>'
                            . '<th class="table-loses">' . $rb->get('table.loses') . '</th>'
                            . '<th class="table-s_score">' . $rb->get('table.s_score') . '</th>'
                            . '<th class="table-r_score">' . $rb->get('table.r_score') . '</th>'
                            . '<th class="table-points">' . $rb->get('table.points') . '</th>'
                            . (($editable == 'true') ? ''
                                . '<th class="table-positionfix">' . $rb->get('table.positionfix') . '</th>'
                            : '')
                            . '</tr>';

                    $i = 1;
                    foreach ($table as $team) {
                        if ($editable == 'true') {
                            foreach ($team as $key => $item) {
                                if ($key != 'id' && $key != 'name') {
                                    $team[$key] = '<input class="w30" type="text" name="table-' . $key . '[' . $team['id'] . ']" value="' . $item . '" />';
                                }
                            }
                        }
                        $return .= ''
                                . '<tr class="' . ((($i % 2) == 1) ? 'idle' : 'even') . '">'
                                . '<td class="table-position">' . $i . '</td>'
                                . '<td class="table-name">' . $team['name'] . '</td>'
                                . '<td class="table-matches">' . $team['matches'] . '</td>'
                                . '<td class="table-wins">' . $team['wins'] . '</td>'
                                . '<td class="table-draws">' . $team['draws'] . '</td>'
                                . '<td class="table-loses">' . $team['loses'] . '</td>'
                                . '<td class="table-s_score">' . $team['s_score'] . '</td>'
                                . '<td class="table-r_score">' . $team['r_score'] . '</td>'
                                . '<td class="table-points">' . $team['points'] . '</td>'
                                . (($editable == 'true') ? ''
                                    . '<td class="table-positionfix">' . $team['positionfix'] . '</td>'
                                : '')
                                . '</tr>';
                        $i++;
                    }

                    $return .= ''
                            . '</table>';

                    if ($editable == 'true') {
                        $return .= ''
                                . '<hr />'
                                . '<div class="gray-box">'
                                . '<input type="submit" name="table-c-save" value="' . $rb->get('table.submit') . '" />'
                                . '</div>'
                                . '</form>';
                    }
                } else {
                    $return .= parent::getWarning($rb->get('table.nodata'));
                }
            } else {
                $return .= parent::getError($rb->get('table.error.notset'));
            }

            if ($useFrames == "false") {
                return $return;
            } else {
                return parent::getFrame($rb->get('table.title') . ' :: (' . $tableId . ')', $return, "", true);
            }
        }

        /**
         *
         * 	Shows list of teams.
         * 	C tag.
         * 	
         * 	@param		templateId			template id
         * 	@param		sortBy					field name to sort list by
         * 	@param		sorting					asc|desc
         * 	@param		noDataMessage		...		 		 		 		 		 		 
         *
         */
        public function showTeams($templateId, $seasonId, $noDataMessage, $teamId = false, $sortBy = false, $sorting = false) {
            global $dbObject;
            $rb = self::rb();
            $return = '';

            if (!UniversalPermission::checkUserPermissions($this->UPDisc, self::getProjectId(), WEB_R_READ)) {
                return parent::getWarning($noDataMessage);
            }

            if (!$sorting == 'desc') {
                $sorting = 'asc';
            }
            if ($sortBy == '') {
                $sortBy = 'id';
            }

            $teams = parent::db()->fetchAll('select distinct `id`, `name`, `url`, `logo` from `w_sport_team` where `project_id` = ' . self::getProjectId() . ' and `season` = ' . $seasonId . ($teamId != '' ? ' and `id` = ' . $teamId : '') . ' order by `' . $sortBy . '` ' . $sorting . ';');
            if (count($teams) > 0) {
                $i = 0;
                $content = parent::getTemplateContent($templateId);
                $seasonId = self::getSeasonId();
                $teamId = self::getTeamId();
                foreach ($teams as $team) {
                    parent::request()->set('team', $team, 'sport-data');
                    parent::request()->set('i', $i, 'sport-data');
                    self::setSeasonId($seasonId);
                    self::setTeamId($team['id']);

                    $parser = new FullTagParser();
                    $parser->setContent($content);
                    $parser->startParsing();
                    $return .= $parser->getResult();
                    $i++;
                }
                self::setSeasonId($seasonId);
                self::setTeamId($teamId);
            } else {
                $return .= parent::getWarning($noDataMessage);
            }

            return $return;
        }

        /**
         *
         * 	Shows team.
         * 	C tag.
         *
         * 	@param		field						field name to show
         * 	@param		teamId					team id
         * 	@param		seasonId				season id
         *
         */
        public function showTeam($field, $teamId = false, $seasonId = false) {
            global $dbObject;
            $rb = self::rb();
            $return = '';
            $data = array();

            if ($seasonId == '') {
                $seasonId = self::getSeasonId();
            }

            if ($teamId != '') {
                $data = parent::db()->fetchSingle('select `id`, `name`, `url`, `logo` from `w_sport_team` where `project_id` = ' . self::getProjectId() . ' and `id` = ' . $teamId . ($seasonId != '-1' ? ' and `season` = ' . $seasonId : '') . ';');
            } else {
                if (parent::request()->exists('team', 'sport-data')) {
                    $data = parent::request()->get('team', 'sport-data');
                } else {
                    $return .= parent::getError($rb->get('table.error.seasonorteamnotset'));
                }
            }

            if ($data != array()) {
                switch (strtolower($field)) {
                    case 'row': $return .= ( ((parent::request()->get('i', 'sport-data') % 2) == 1) ? 'idle' : 'even');
                        break;
                    case 'i': $return .= parent::request()->get('i', 'sport-data');
                        break;
                    case 'id': $return .= $data['id'];
                        break;
                    case 'name': $return .= $data['name'];
                        break;
                    case 'logo': $return .= $data['logo'];
                        break;
                    case 'url': $return .= $data['url'];
                        break;
                    default: $return .= parent::getError($rb->get('table.error.incorrectfield'));
                }
            } else {
                $return .= parent::getError($rb->get('table.error.teamdoesntexist'));
            }

            return $return;
        }

        /**
         *
         * 	Shows matches.
         * 	C tag.
         *
         * 	@param		templateId			template id
         * 	@param		sorting					ASC or DESC
         * 	@param		round						matches from passed round
         * 	@param		teamId					team id
         * 	@param		seasonId				season id
         *
         */
        public function showMatches($templateId, $noDataMessage, $sorting = false, $matchId = false, $round = false, $teamId = false, $seasonId = false, $onlyPlayed = true, $state = null) {
            global $dbObject;
            global $loginObject;
            $rb = self::rb();
            $return = '';

            if (!UniversalPermission::checkUserPermissions($this->UPDisc, self::getProjectId(), WEB_R_READ)) {
                return parent::getWarning($noDataMessage);
            }

            if (strtolower($sorting) == 'desc') {
                $sorting = 'DESC';
            } else {
                $sorting = 'ASC';
            }

            $data = array();
            $where = '';

            $where .= ' `project_id` = ' . self::getProjectId();

            if ($teamId != '') {
                $where .= ' and (`h_team` = ' . $teamId . ' OR `a_team` = ' . $teamId . ')';
            }
            if ($round != '') {
                $where .= ' and `round` = ' . $round;
            }
            if ($seasonId != '') {
                $where .= ' and `season` = ' . $seasonId;
            }
            if ($matchId != '') {
                $where .= ' and `id` = ' . $matchId;
            }
            if ($state == '') {
                if ($onlyPlayed == true) {
                    $state = 'played';
                } else {
                    $state = 'all';
                }
            }

            $stateSql = self::mapStateToPlayedSqlWhere($state);
            if ($stateSql === null) {
                return parent::getError('Not valid "state" attribute in s:matches.');
            }

            $sql = 'select `id`, `h_team`, `a_team`, `season`, `h_score`, `a_score`, `h_shoots`, `a_shoots`, `h_penalty`, `a_penalty`, `h_extratime`, `a_extratime`, `comment`, `round`, `in_table`, `place`, `refs`, `refs2`, `date`, `time`, `stuff`, `stuff2`, `main_stuff` from `w_sport_match`' . ($where != '' ? ' where ' . $where : '') . ' order by `round` ' . $sorting . ', `id` ' . $sorting . ';';
            $matches = parent::db()->fetchAll($sql);

            if (count($matches) > 0) {
                $content = parent::getTemplateContent($templateId);
                $round = self::getRoundId();
                $season = self::getSeasonId();
                $table = self::getTableId();
                $matchId = self::getMatchId();
                foreach ($matches as $match) {
                    parent::request()->set('match', $match, 'sport-data');
                    parent::request()->set('i', $i, 'sport-data');
                    self::setSeasonId($match['season']);
                    self::setRoundId($match['round']);
                    self::setMatchId($match['id']);
                    if ($match['in_table'] != 0) {
                        self::setTableId($match['in_table']);
                    }
                    self::setHomeTeamId($match['h_team']);
                    self::setAwayTeamId($match['a_team']);

                    $parser = new FullTagParser();
                    $parser->setContent($content);
                    $parser->startParsing();
                    $return .= $parser->getResult();
                    $i++;
                }
                self::setRoundId($round);
                self::setSeasonId($season);
                self::setTableId($table);
                self::setMatchId($matchId);
            } else {
                $return .= parent::getWarning($noDataMessage);
            }

            return $return;
        }

        private function mapStateToPlayedSqlWhere($state, $prefix = null) {
            $prefix = $prefix != null ? '`' . $prefix . '`.' : '';

            switch ($state) {
                case 'all':
                    return '';

                case 'played':
                    return ' and ' . $prefix . '`notplayed` = 0';
                
                case 'notplayed':
                    return ' and ' . $prefix . '`notplayed` = 1';

                default:
                    return null;
            }
        }

        /**
         *
         * 	Shows match.
         * 	C tag.
         *
         * 	@param		field					field name to show
         * 	@param		matchId					match id
         *
         */
        public function showMatch($field, $matchId = false) {
            global $dbObject;
            $rb = self::rb();
            $return = '';
            $data = array();

            /* if($matchId == '') {
            $matchId = self::getMatchId();
            } */

            if ($matchId != '') {
                $data = parent::db()->fetchSingle('select `id`, `h_team`, `a_team`, `season`, `h_score`, `a_score`, `h_shoots`, `a_shoots`, `h_penalty`, `a_penalty`, `h_extratime`, `a_extratime`, `comment`, `round`, `in_table`, `place`, `refs`, `refs2`, `date`, `time`, `main_stuff`, `stuff`, `stuff2` from `w_sport_match` where `id` = ' . $matchId . ' order by `round` ' . $sorting . ', `id` ' . $sorting . ';');
            } else {
                if (parent::request()->exists('match', 'sport-data')) {
                    $data = parent::request()->get('match', 'sport-data');
                } else {
                    $return .= parent::getError($rb->get('match.error.matchdoesntexist'));
                }
            }

            if ($data != array()) {
                switch (strtolower($field)) {
                    case 'row': $return .= ( ((parent::session()->get('i', 'sport-data') % 2) == 1) ? 'idle' : 'even');
                        break;
                    case 'i': $return .= parent::session()->get('i', 'sport-data');
                        break;
                    case 'id': $return .= $data['id'];
                        break;
                    case 'h_score': $return .= $data['h_score'];
                        break;
                    case 'a_score': $return .= $data['a_score'];
                        break;
                    case 'h_shoots': $return .= $data['h_shoots'];
                        break;
                    case 'a_shoots': $return .= $data['a_shoots'];
                        break;
                    case 'h_penalty': $return .= $data['h_penalty'];
                        break;
                    case 'a_penalty': $return .= $data['a_penalty'];
                        break;
                    case 'h_extratime': $return .= $data['h_extratime'];
                        break;
                    case 'a_extratime': $return .= $data['a_extratime'];
                        break;
                    case 'h_extratime_text': $return .= ( ($data['h_extratime'] == 1) ? $rb->get('matches.form.homeexwin') : '');
                        break;
                    case 'a_extratime_text': $return .= ( ($data['a_extratime']) ? $rb->get('matches.form.awayexwin') : '');
                        break;
                    case 'comment': $return .= $data['comment'];
                        break;
                    case 'place': $return .= $data['place'];
                        break;
                    case 'refs': $return .= $data['refs'];
                        break;
                    case 'refs2': $return .= $data['refs2'];
                        break;
                    case 'date': $return .= $data['date'];
                        break;
                    case 'time': $return .= $data['time'];
                        break;
                    case 'main_stuff': $return .= $data['main_stuff'];
                        break;
                    case 'stuff': $return .= $data['stuff'];
                        break;
                    case 'stuff2': $return .= $data['stuff2'];
                        break;
                    default: $return .= parent::getError($rb->get('match.error.incorrectfield'));
                }
            } else {
                $return .= parent::getError($rb->get('match.error.matchidnotset'));
            }

            return $return;
        }

        /**
         *
         * 	Shows rounds.
         * 	C tag.
         *
         * 	@param		templateId			template id
         * 	@param		sorting					ASC or DESC
         * 	@param		seasonId				season id
         *
         */
        public function showRounds($templateId, $sorting, $noDataMessage, $seasonId = false, $teamId = false, $onlyPlayed = true, $state = '', $startRoundId = false, $maxRoundId = false, $limit = false) {
            global $dbObject;
            global $loginObject;
            $rb = self::rb();
            $return = '';
            $teamSql = '';
            $joinSql = '';

            if ($seasonId == '') {
                $seasonId = self::getSeasonId();
            }
            if ($teamId != '') {
                $teamSql = ' and (`w_sport_match`.`h_team` = ' . $teamId . ' or `w_sport_match`.`a_team` = ' . $teamId . ')';
                $joinSql = ' left join `w_sport_match` on `w_sport_match`.`round` = `w_sport_round`.`id`';
            }

            if (strtolower($sorting) == 'asc') {
                $sorting = 'ASC';
            } else {
                $sorting = 'DESC';
            }

            if ($state == '') {
                if ($onlyPlayed == true) {
                    $state = 'played';
                } else {
                    $state = 'all';
                }
            }

            $stateSql = self::mapStateToPlayedSqlWhere($state, 'w_sport_match');
            if ($stateSql === null) {
                return parent::getError('Not valid "state" attribute in s:matches.');
            }

            if ($seasonId != '-1') {
                if ($state != 'all') {
                    // join pres zapasy pro zobrazeni jen kol s odehranymi zapasy ...
                    $rounds = parent::db()->fetchAll('select distinct `w_sport_round`.`id`, `w_sport_round`.`name`, `w_sport_round`.`number` from `w_sport_match` left join `w_sport_round` on `w_sport_match`.`round` = `w_sport_round`.`id` where `w_sport_round`.`project_id` = ' . self::getProjectId() . ' and `w_sport_round`.`season_id` = ' . $seasonId . $teamSql . $stateSql . ' and `w_sport_round`.`visible` = 1'.($startRoundId != "" ? ' and `w_sport_round`.`id` >= '.$startRoundId : '').($maxRoundId != "" ? ' and `w_sport_round`.`id` <= '.$maxRoundId : '').' order by `w_sport_round`.`number` ' . $sorting . ($limit != "" ? ' limit ' . $limit : "") . ';');
                } else {
                    $rounds = parent::db()->fetchAll('select distinct `w_sport_round`.`id`, `name`, `number` from `w_sport_round`' . $joinSql . ' where `w_sport_round`.`project_id` = ' . self::getProjectId() . ' and `season_id` = ' . $seasonId . $teamSql . ' and `visible` = 1'.($startRoundId != "" ? ' and `w_sport_round`.`id` >= '.$startRoundId : '').($maxRoundId != "" ? ' and `w_sport_round`.`id` <= '.$maxRoundId : '').' order by `number` ' . $sorting . ($limit != "" ? ' limit ' . $limit : "") . ';');
                }
                if (count($rounds) > 0) {
                    $templateContent = parent::getTemplateContent($templateId);
                    $i = 1;
                    $lastround = self::getRoundId();
                    $lastseason = self::getSeasonId();
                    foreach ($rounds as $round) {
                        parent::request()->set('round', $round, 'sport-data');
                        parent::request()->set('i', $i, 'sport-data');
                        self::setRoundId($round['id']);
                        self::setSeasonId($seasonId);
                        $Parser = new FullTagParser();
                        $Parser->setContent($templateContent);
                        $Parser->startParsing();
                        $return .= $Parser->getResult();
                        $i++;
                    }
                    self::setRoundId($lastround);
                    self::setSeasonId($lastseason);
                } else {
                    $return .= parent::getWarning($noDataMessage);
                }
            } else {
                $return .= parent::getWarning($noDataMessage);
            }

            return $return;
        }

        /**
         *
         * 	Shows match.
         * 	C tag.
         * 	
         * 	@param		field						field name to show
         * 	@param		matchId					match id
         *
         */
        public function showRound($field, $roundId = false) {
            $rb = self::rb();
            $return = '';
            $data = array();

            if ($roundId != '') {
                $data = parent::db()->fetchSingle(parent::query()->get('roundById', array('id' => $roundId), 'sport'));
            } else {
                if (parent::request()->exists('round', 'sport-data')) {
                    $data = parent::request()->get('round', 'sport-data');
                } else {
                    $return .= parent::getError($rb->get('round.error.rounddoesntexist'));
                }
            }

            if ($data != array()) {
                switch (strtolower($field)) {
                    case 'row': $return .= ( ((parent::session()->get('i', 'sport-data') % 2) == 1) ? 'idle' : 'even');
                        break;
                    case 'i': $return .= parent::session()->get('i', 'sport-data');
                        break;
                    case 'id': $return .= $data['id'];
                        break;
                    case 'name': $return .= $data['name'];
                        break;
                    case 'number': $return .= $data['number'];
                        break;
                    default: $return .= parent::getError($rb->get('round.error.incorrectfield'));
                }
            } else {
                $return .= parent::getError($rb->get('round.error.roundidnotset'));
            }

            return $return;
        }

        /**
         *
         * 	Shows players.
         * 	C tag.
         *
         * 	@param		templateId			template id
         * 	@param		sorting					ASC or DESC
         * 	@param		sortBy					filed name to sort by
         * 	@param		seasonId				season id
         * 	@param		teamId					team id
         * 	@param		fromMatchId
         * 	@param		only
         * 	@param		scope
         * 	@param		showGolmans
         * 	@param		limit
         *
         */
        public function showPlayers($templateId, $sorting, $sortBy, $playerId = false, $tableId = false, $teamId = false, $seasonId = false, $fromMatchId = false, $only = false, $scope = false, $showGolmans = false, $limit = false, $offset = false, $noDataMessage = false) {
            global $dbObject;
            global $loginObject;
            $rb = self::rb();
            $return = '';

            if ($offset == '') {
                $offset = 0;
            }     
            
            //echo '"' . $only . '"<br />';
            //echo '"' . $scope . '"<br />';
            //echo '"' . $showGolmans . '"<br />';

            
            $templateContent = parent::getTemplateContent($templateId);

            $oldFields = $this->UsedFields;
            $oldPhase = $this->ViewPhase;
            $this->UsedFields = array();
            $this->ViewPhase = 1;

            $parser = new FullTagParser();
            $parser->setContent($templateContent);
            $parser->startParsing();

            //print_r($this->UsedFields);
            //parent::db()->setMockMode(true);  
            $players = self::getPlayersFrom('most', $sorting, $sortBy, $tableId, $teamId, $seasonId, $fromMatchId, $only, $scope, $showGolmans, $limit, $playerId, $offset);
            //echo $_SESSION['sport']['match-id'];
            //unset($_SESSION['sport']['match-id']);

            if (count($players) > 0) {
                $i = $offset + 1;
                $this->ViewPhase = 2;
                $_SESSION['sport']['players']['round'] = $_SESSION['sport']['round'];
                $_SESSION['sport']['players']['team-id'] = $_SESSION['sport']['team-id'];
                $_SESSION['sport']['players']['season-id'] = $_SESSION['sport']['season-id'];
                $_SESSION['sport']['players']['i'] = $_SESSION['sport']['i'];
                $oldteam = self::getTeamId();
                $oldPlayer = self::getPlayerId();
                foreach ($players as $player) {
                    parent::request()->set('player', $player, 'sport-data');
                    $_SESSION['sport']['player-id'] = $player['id'];
                    $_SESSION['sport']['team-id'] = $player['team-id'];
                    $_SESSION['sport']['season-id'] = $seasonId;
                    self::setTeamId($player['team-id']);
                    self::setPlayerId($player['id']);
                    $_SESSION['sport']['i'] = $i;
                    $parser = new FullTagParser();
                    $parser->setContent($templateContent);
                    $parser->startParsing();
                    $return .= $parser->getResult();
                    $i++;
                }
                self::setTeamId($oldteam);
                self::setPlayerId($oldPlayer);
                $_SESSION['sport']['round'] = $_SESSION['sport']['players']['round'];
                $_SESSION['sport']['team-id'] = $_SESSION['sport']['players']['team-id'];
                $_SESSION['sport']['season-id'] = $_SESSION['sport']['players']['season-id'];
                $_SESSION['sport']['i'] = $_SESSION['sport']['players']['i'];
            } else {
                $return .= parent::getWarning($noDataMessage);
            }
            //parent::db()->setMockMode(false);

            $this->ViewPhase = $oldPhase;
            $this->UsedFields = $oldFields;

            return $return;
        }

        /**
         *
         * 	Shows player.
         * 	C tag.
         * 	
         * 	@param		field						field name to show
         * 	@param		playerId				player id
         * 	@param		teamId					team id
         * 	@param		seasonId				season id		 
         * 	@param		errMsg					error message		 
         *
         */
        public function showPlayer($field, $playerId = false, $tableId = false, $teamId = false, $seasonId = false, $errMsg = false) {
            global $dbObject;
            $rb = self::rb();
            $return = '';

            if ($this->ViewPhase == 1) {
                $this->UsedFields[] = $field;
                return;
            }

            if ($playerId == false) {
                $playerId = $_SESSION['sport']['player-id'];
            }
            if ($teamId == false) {
                $teamId = $_SESSION['sport']['team-id'];
            }
            if ($seasonId == false) {
                $seasonId = $_SESSION['sport']['season-id'];
            }

            $player = array();
            if (parent::request()->exists('player', 'sport-data')) {
                $player = parent::request()->get('player', 'sport-data');
            } else {
                $player = self::getPlayersFrom('most', $sorting, $sortBy, $tableId, $teamId, $seasonId, $fromMatchId, $only, $scope, $showGolmans, 1, $playerId);
            }

            foreach ($player as $key => $val) {
                if ($val == '') {
                    $player[$key] = $errMsg;
                }
            }

            if ($player != array()) {
                switch (strtolower($field)) {
                    case 'row': $return .= ( (($_SESSION['sport']['i'] % 2) == 1) ? 'idle' : 'even');
                        break;
                    case 'i': $return .= $_SESSION['sport']['i'];
                        break;
                    case 'id': $return .= $player['id'];
                        break;
                    case 'name': $return .= $player['name'];
                        break;
                    case 'surname': $return .= $player['surname'];
                        break;
                    case 'birthyear': $return .= $player['birthyear'];
                        break;
                    case 'number': $return .= $player['number'];
                        break;
                    case 'position': $return .= self::getPlayerPosition($player['position']);
                        break;
                    case 'photo': $return .= $player['photo'];
                        break;
                    case 'total_matches': $return .= $player['total_matches'];
                        break;
                    case 'total_points': $return .= $player['total_points'];
                        break;
                    case 'total_goals': $return .= $player['total_goals'];
                        break;
                    case 'total_assists': $return .= $player['total_assists'];
                        break;
                    case 'total_shoots': $return .= $player['total_shoots'];
                        break;
                    case 'total_penalty': $return .= $player['total_penalty'];
                        break;
                    case 'total_percentage': $return .= sprintf("%05s", number_format($player['total_percentage'], 2));
                        break;
                    case 'total_average': $return .= sprintf("%05s", round($player['total_average'], 2));
                        break;
                    case 'season_matches': $return .= $player['season_matches'];
                        break;
                    case 'season_points': $return .= $player['season_points'];
                        break;
                    case 'season_goals': $return .= $player['season_goals'];
                        break;
                    case 'season_assists': $return .= $player['season_assists'];
                        break;
                    case 'season_shoots': $return .= $player['season_shoots'];
                        break;
                    case 'season_penalty': $return .= $player['season_penalty'];
                        break;
                    case 'season_percentage': $return .= sprintf("%05s", number_format($player['season_percentage'], 2));
                        break;
                    case 'season_average': $return .= sprintf("%05s", number_format($player['season_average'], 2));
                        break;
                    case 'match_matches': $return .= $player['match_matches'];
                        break;
                    case 'match_points': $return .= $player['match_points'];
                        break;
                    case 'match_goals': $return .= $player['match_goals'];
                        break;
                    case 'match_assists': $return .= $player['match_assists'];
                        break;
                    case 'match_shoots': $return .= $player['match_shoots'];
                        break;
                    case 'match_penalty': $return .= $player['match_penalty'];
                        break;
                    case 'match_percentage': $return .= sprintf("%05s", number_format($player['match_percentage'], 2));
                        break;
                    case 'match_average': $return .= sprintf("%05s", number_format($player['match_average'], 2));
                        break;
                    default: $return .= '<h4 class="error">' . $rb->get('player.error.incorrectfield') . '</h4>';
                }
            } else {
                if ($errMsg != false) {
                    $return .= $errMsg;
                } else {
                    $return .= '<h4 class="error">' . $rb->get('player.error.teamdoesntexist') . '</h4>';
                }
            }

            return $return;
        }

        public function showPlayersNG($templateId, $noDataMessage, $seasonId = false, $tableId = false, $matchId = false, $playerId = false, $teamId = false, $includeOnLoan = false, $positions = false, $sortBy = false, $sorting = false) {
            global $dbObject;
            global $loginObject;
            $rb = self::rb();
            $return = '';

            $where = ' `project_id` = ' . self::getProjectId();
            
            if($includeOnLoan != 'true') {
                $where .= ' and `on_loan` = 0';
            }
            if($seasonId != '') {
                $where .= ' and `season` = ' . $seasonId;
            }
            if ($playerId != '') {
                $where .= ' and `id` = ' . $playerId;
            }
            if ($teamId != '') {
                $where .= ' and `team` = ' . $teamId;
            }
            if ($positions != '') {
                $where .= ' and `position` in (' . $positions . ')';
            }
            if ($sortBy == '') {
                $sortBy = 'id';
            }
            if ($sorting != 'desc') {
                $sorting = 'asc';
            }

            $sql = 'select `id`, `name`, `surname`, `url`, `birthyear`, `number`, `position`, `photo`, `season`, `team` from `w_sport_player` where' . $where . ' order by `' . $sortBy . '` ' . $sorting . ';';
            $players = parent::db()->fetchAll($sql);
            if (count($players) > 0) {
                $templateContent = parent::getTemplateContent($templateId);
                $i = 1;
                $lastseasonId = self::getSeasonId();
                $lastplayerId = self::getPlayerId();
                $lastteamId = self::getTeamId();
                foreach ($players as $player) {
                    parent::request()->set('player', $player, 'sport-data');
                    parent::request()->set('i', $i, 'sport-data');

                    self::setSeasonId($player['season']);
                    self::setTeamId($player['team']);
                    self::setPlayerId($player['id']);

                    $parser = new FullTagParser();
                    $parser->setContent($templateContent);
                    $parser->startParsing();
                    $return .= $parser->getResult();
                    $i++;
                }
                self::setSeasonId($lastseasonId);
                self::setPlayerId($lastplayerId);
                self::setTeamId($lastteamId);
            } else {
                $return .= parent::getWarning($noDataMessage);
            }

            return $return;
        }

        public function showPlayerNG($field, $playerId = false) {
            $rb = self::rb();
            $return = '';
            $data = array();

            if ($playerId != '') {
                $sql = 'select `id`, `name`, `surname`, `url`, `birthyear`, `number`, `position`, `photo`, `season`, `team` from `w_sport_player` where `id` = ' . $playerId . ';';
                $data = parent::db()->fetchSingle($sql);
            } else {
                if (parent::request()->exists('player', 'sport-data')) {
                    $data = parent::request()->get('player', 'sport-data');
                } else {
                    //$return .= parent::getWarning($noDataMessage);
                }
            }

            $positions = array('', $rb->get('player.position-goa'), $rb->get('player.position-def'), $rb->get('player.position-att'));
            if ($data != array()) {
                switch (strtolower($field)) {
                    case 'row': $return .= ( ((parent::session()->get('i', 'sport-data') % 2) == 1) ? 'idle' : 'even');
                        break;
                    case 'i': $return .= parent::session()->get('i', 'sport-data');
                        break;
                    case 'id': $return .= $data['id'];
                        break;
                    case 'name': $return .= $data['name'];
                        break;
                    case 'surname': $return .= $data['surname'];
                        break;
                    case 'number': $return .= $data['number'];
                        break;
                    case 'url': $return .= $data['url'];
                        break;
                    case 'birthyear': $return .= $data['birthyear'];
                        break;
                    case 'position': $return .= $positions[$data['position']];
                        break;
                    case 'photo': $return .= $data['photo'];
                        break;
                    default: $return .= parent::getError($rb->get('player.error.incorrectfield'));
                }
            } else {
                //$return .= parent::getWarning($noDataMessage);
            }

            return $return;
        }

        public function showPlayersStatsNG($templateId, $type, $noDataMessage, $seasonId = false, $tableId = false, $playerId = false, $matchId = false, $sortBy = false, $sorting = false, $positions = false, $partWhere = false) {
            global $dbObject;
            global $loginObject;
            $rb = self::rb();
            $return = '';
            $ok = true;
            $sqlpart = '';

            $where = ' `w_sport_stats`.`project_id` = ' . self::getProjectId();

            if ($seasonId != '') {
                $where .= ' and `w_sport_stats`.`season` = ' . $seasonId;
            }
            if ($tableId != '') {
                $where .= ' and `w_sport_stats`.`table_id` = ' . $tableId;
            }
            if ($playerId != '') {
                $where .= ' and `w_sport_stats`.`pid` = ' . $playerId;
            }
            if ($matchId != '') {
                $where .= ' and `w_sport_stats`.`mid` = ' . $matchId;
            }
            if ($positions != '') {
                $where .= ' and `w_sport_stats`.`pos` in (' . $positions . ')';
            }
            if ($partWhere != '') {
                $where .= ' and (' . $partWhere . ')';
            }
            if ($sorting != 'desc') {
                $sorting = 'asc';
            }
            if ($sortBy == '') {
                $sortBy = 'pid';
            }

            switch ($type) {
                case 'match':
                    if ($matchId == '') {
                        $ok = false;
                    } else {
                        $sqlpart = 'select distinct `pid`, `mid`, `goals`, `assists`, `penalty`, `shoots`, `w_sport_stats`.`season`, `table_id` from `w_sport_stats`';
                    }
                    break;
                case 'season':
                    if ($seasonId == '' || ($playerId == '' && $tableId == '')) {
                        $ok = false;
                    } else {
                        // dotaz na pohledem bud pro jednotliveho hrace nebo pro tabulku
                    }
                    break;
                case 'total':
                    if ($playerId == '' && $tableId == '') {
                        $ok = false;
                    } else {
                        // dotaz na pohledem bud pro jednotliveho hrace nebo pro tabulku
                    }
                    break;
            }

            if ($ok) {
                if ($positions != '') {
                    $sqlpart .= ' left join `w_sport_player` on `w_sport_stats`.`pid` = `w_sport_player`.`id`';
                }
                $sql = $sqlpart . ' where' . $where . ' order by `' . $sortBy . '` ' . $sorting . ';';
                $stats = parent::db()->fetchAll($sql);
                if (count($stats) > 0) {
                    $templateContent = parent::getTemplateContent($templateId);
                    $i = 1;
                    $lastseasonId = self::getSeasonId();
                    $lastplayerId = self::getPlayerId();
                    $lastmatchId = self::getMatchId();
                    $lasttableId = self::getTableId();
                    foreach ($stats as $stat) {
                        parent::request()->set('stat', $stat, 'sport-data');
                        parent::request()->set('i', $i, 'sport-data');

                        self::setSeasonId($stat['season']);
                        self::setMatchId($stat['mid']);
                        self::setPlayerId($stat['pid']);
                        self::setTableId($stat['table_id']);

                        $parser = new FullTagParser();
                        $parser->setContent($templateContent);
                        $parser->startParsing();
                        $return .= $parser->getResult();
                        $i++;
                    }
                    self::setMatchId($lastmatchId);
                    self::setTableId($lasttableId);
                    self::setSeasonId($lastseasonId);
                    self::setPlayerId($lastplayerId);
                } else {
                    $return .= parent::getWarning($noDataMessage);
                }
            } else {
                $return .= parent::getWarning($noDataMessage);
            }

            return $return;
        }

        public function showPlayerStatsNG($field) {
            $rb = self::rb();
            $return = '';
            $data = array();

            if (parent::request()->exists('stat', 'sport-data')) {
                $data = parent::request()->get('stat', 'sport-data');
            } else {
                //$return .= parent::getWarning($noDataMessage);
            }

            if ($data != array()) {
                switch (strtolower($field)) {
                    case 'row': $return .= ( ((parent::session()->get('i', 'sport-data') % 2) == 1) ? 'idle' : 'even');
                        break;
                    case 'i': $return .= parent::session()->get('i', 'sport-data');
                        break;
                    case 'goals': $return .= $data['goals'];
                        break;
                    case 'assists': $return .= $data['assists'];
                        break;
                    case 'shoots': $return .= $data['shoots'];
                        break;
                    case 'penalty': $return .= $data['penalty'];
                        break;
                    case 'season_goals': $return .= $data['season_goals'];
                        break;
                    case 'season_assits': $return .= $data['season_assists'];
                        break;
                    case 'season_shoots': $return .= $data['season_shoots'];
                        break;
                    case 'season_penalty': $return .= $data['season_penalty'];
                        break;
                    case 'total_goals': $return .= $data['total_goals'];
                        break;
                    case 'total_assits': $return .= $data['total_assists'];
                        break;
                    case 'total_shoots': $return .= $data['total_shoots'];
                        break;
                    case 'total_penalty': $return .= $data['total_penalty'];
                        break;
                    default: $return .= parent::getError($rb->get('player.error.incorrectfield'));
                }
            } else {
                //$return .= parent::getWarning($noDataMessage);
            }

            return $return;
        }

        // ------------------------------------------------------------------------------------------------------------------- \\

        public function getPlayerPosition($pos) {
            $rb = self::rb();

            switch ($pos) {
                case 1: return $rb->get('player.position-goa');
                case 2: return $rb->get('player.position-def');
                case 3: return $rb->get('player.position-att');
            }
        }

        public function getPlayerPositionShortcut($pos) {
            $rb = self::rb();

            switch ($pos) {
                case 1: return $rb->get('player.position-goa-short');
                case 2: return $rb->get('player.position-def-short');
                case 3: return $rb->get('player.position-att-short');
            }
        }

        public function getSeasonsOptions($teamId = 0, $seasonId = 0, $seaselId = 0) {
            global $dbObject;
            $return = '';

            if (!UniversalPermission::checkUserPermissions($this->UPDisc, self::getProjectId(), WEB_R_READ)) {
                return '';
            }

            $seasql = $dbObject->fetchAll('SELECT `id`, `start_year`, `end_year` FROM `w_sport_season` WHERE `project_id` = ' . self::getProjectId() . ' ORDER BY `start_year` DESC;');
            foreach ($seasql as $sea) {
                if ($teamId != '') {
                    $tea = $dbObject->fetchAll('SELECT `id` FROM `w_sport_team` WHERE `id` = ' . $teamId . ' AND `season` = ' . $sea['id'] . ';');
                } else {
                    $tea = array();
                }
                if (count($tea) == 0 || $sea['id'] == $seasonId) {
                    $return .= '<option value="' . $sea['id'] . '"' . (($sea['id'] == $seaselId) ? 'selected="selectd"' : '') . '>' . $sea['start_year'] . ' - ' . $sea['end_year'] . '</option>';
                }
            }

            return $return;
        }

        public function getTablesOptions($tabselId) {
            global $dbObject;
            $return = '';

            if (!UniversalPermission::checkUserPermissions($this->UPDisc, self::getProjectId(), WEB_R_READ)) {
                return '';
            }

            $tabsql = $dbObject->fetchAll('select `id`, `name` from `w_sport_tables` where `project_id` = ' . self::getProjectId() . ' ORDER BY `name`;');
            foreach ($tabsql as $tab) {
                $return .= '<option value="' . $tab['id'] . '"' . (($tab['id'] == $tabselId) ? ' selected="selectd"' : '') . '>' . $tab['name'] . '</option>';
            }

            return $return;
        }

        public function getRoundsOptions($tabselId) {
            global $dbObject;
            $return = '';

            if (!UniversalPermission::checkUserPermissions($this->UPDisc, self::getProjectId(), WEB_R_READ)) {
                return '';
            }
            
            $tablewhere = '';
            $seasonId = self::getSeasonId();
            if($seasonId != -1) {
                $tablewhere .= ' and `w_sport_round`.`season_id` = '.$seasonId.'';
            }

            $tabsql = $dbObject->fetchAll('select `id`, `name` from `w_sport_round` where `project_id` = ' . self::getProjectId() . $tablewhere . ' ORDER BY `number`;');
            foreach ($tabsql as $tab) {
                $return .= '<option value="' . $tab['id'] . '"' . (($tab['id'] == $tabselId) ? ' selected="selectd"' : '') . '>' . $tab['name'] . '</option>';
            }

            return $return;
        }

        public function getTeamsOptions($teaselId = 0) {
            global $dbObject;
            $return = '';

            if (!UniversalPermission::checkUserPermissions($this->UPDisc, self::getProjectId(), WEB_R_READ)) {
                return '';
            }

            $tablesql = '';
            $tablewhere = '';
            if (self::getTableId() != '-1') {
                $tablesql = ' join `w_sport_table` on `w_sport_team`.`id` = `w_sport_table`.`team`';
                $tablewhere = ' and `w_sport_table`.`table_id` = ' . self::getTableId();
            }
            
            $seasonId = self::getSeasonId();
            if($seasonId != -1) {
                $tablewhere .= ' and `w_sport_team`.`season` = '.$seasonId.'';
            }

            $teams = $dbObject->fetchAll('select distinct `id`, `name` from `w_sport_team`' . $tablesql . ' where `w_sport_team`.`project_id` = ' . self::getProjectId() . $tablewhere . ' order by `name`;');
            foreach ($teams as $team) {
                $return .= '<option value="' . $team['id'] . '"' . (($team['id'] == $teaselId) ? ' selected="selectd"' : '') . '>' . $team['name'] . '</option>';
            }

            return $return;
        }

        // Vrací jak vygenerované HTML, tak data; formou asociativního pole s klíči 'html' a 'data'.
        public function getProjectsOptions($prselId, $permType) {
            $return = '';

            $data = array();
            $projects = parent::db()->fetchAll('select `id`, `name` from `w_sport_project` order by `name`;');
            foreach ($projects as $project) {
                if (UniversalPermission::checkUserPermissions($this->UPDisc, $project['id'], $permType)) {
                    array_push($data, $project);
                    $return .= '<option value="' . $project['id'] . '"' . (($project['id'] == $prselId) ? ' selected="selected"' : '') . '>' . $project['name'] . '</option>';
                }
            }
            return array('html' => $return, 'data' => $data);
        }

        public function isSetProjectId() {
            if (self::getProjectId() == '-1') {
                return false;
            } else {
                return true;
            }
        }

        // ------------------------------------------------------------------------------------------------------------------- \\

        public function getPlayersFrom($type, $sorting, $sortBy, $tableId = false, $teamId = false, $seasonId = false, $fromMatchId = false, $only = false, $scope = false, $showGolmans = false, $limit = false, $playerId = false, $offset = false) {
            global $dbObject;

            //return parent::db()->fetchAll('select * from `w_sport_stats_list`');
            //return parent::db()->fetchAll('SELECT DISTINCT `player`.`id`, `player`.`name`, `player`.`surname`, `player`.`birthyear`, `player`.`number`, `player`.`position`, `player`.`photo`, (SELECT COUNT(`pid`) AS `matches` FROM `w_sport_stats` WHERE `pid` = `player`.`id` and `w_sport_stats`.`table_id` = 7) AS `total_matches`, (SELECT SUM(`goals`) AS `matches` FROM `w_sport_stats` WHERE `pid` = `player`.`id` and `w_sport_stats`.`table_id` = 7) AS `total_goals`, (SELECT SUM(`assists`) AS `matches` FROM `w_sport_stats` WHERE `pid` = `player`.`id` and `w_sport_stats`.`table_id` = 7) AS `total_assists`, (SELECT SUM(`penalty`) AS `matches` FROM `w_sport_stats` WHERE `pid` = `player`.`id` and `w_sport_stats`.`table_id` = 7) AS `total_penalty`, (SELECT SUM(`shoots`) AS `matches` FROM `w_sport_stats` WHERE `pid` = `player`.`id` and `w_sport_stats`.`table_id` = 7) AS `total_shoots`, (SELECT (SUM(`shoots`) / (SUM(`shoots`) + SUM(`goals`)) * 100) AS `matches` FROM `w_sport_stats` WHERE `pid` = `player`.`id` and `w_sport_stats`.`table_id` = 7) AS `total_percentage`,(SELECT (SUM(`goals`) / COUNT(`pid`)) AS `matches` FROM `w_sport_stats` WHERE `pid` = `player`.`id` and `w_sport_stats`.`table_id` = 7) AS `total_average`, (SELECT (SUM(`goals`) + SUM(`assists`)) AS `matches` FROM `w_sport_stats` WHERE `pid` = `player`.`id` and `w_sport_stats`.`table_id` = 7) AS `total_points`, (SELECT COUNT(`pid`) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`season` = 4 AND `pid` = `player`.`id` and `w_sport_stats`.`table_id` = 7) AS `season_matches`, (SELECT SUM(`goals`) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`season` = 4 AND `pid` = `player`.`id`) AS `season_goals`, (SELECT SUM(`assists`) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`season` = 4 AND `pid` = `player`.`id` and `w_sport_stats`.`table_id` = 7) AS `season_assists`, (SELECT SUM(`penalty`) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`season` = 4 AND `pid` = `player`.`id` and `w_sport_stats`.`table_id` = 7) AS `season_penalty`, (SELECT SUM(`shoots`) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`season` = 4 AND `pid` = `player`.`id` and `w_sport_stats`.`table_id` = 7) AS `season_shoots`, (SELECT (SUM(`shoots`) / (SUM(`shoots`) + SUM(`goals`)) * 100) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`season` = 4 AND `pid` = `player`.`id` and `w_sport_stats`.`table_id` = 7) AS `season_percentage`,(SELECT (SUM(`goals`) / COUNT(`pid`)) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`season` = 4 AND `pid` = `player`.`id` and `w_sport_stats`.`table_id` = 7) AS `season_average`, (SELECT (SUM(`goals`) + SUM(`assists`)) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`season` = 4 AND `pid` = `player`.`id` and `w_sport_stats`.`table_id` = 7) AS `season_points` FROM `w_sport_player` AS `player` LEFT JOIN `w_sport_team` ON `player`.`team` = `w_sport_team`.`id` WHERE `player`.`season` = 4 AND (`player`.`position` = 2 OR `player`.`position` = 3) ORDER BY `season_points` DESC LIMIT 20;');

            $cols = '';
            $matchsql = '';
            $onlysql = '';
            $positionsql = '';
            $limisql = '';
            $joinstatssql = '';
            $subqueriessql = '';
            $conditionssql = '';
            
            //echo $_SESSION['sport']['match-id'];
            //unset($_SESSION['sport']['team-id']);
            if ($seasonId == false) {
                $seasonId = $_SESSION['sport']['season-id'];
            }
            if ($tableId == false) {
                $tableId = $_SESSION['sport']['table-id'];
            }
            if ($teamId == false) {
                $teamId = $_SESSION['sport']['team-id'];
            }
            if ($fromMatchId == false) {
                $fromMatchId = $_SESSION['sport']['match-id'];
            }

            if (strtolower($sorting) == 'asc') {
                $sorting = 'ASC';
            } else {
                $sorting = 'DESC';
            }

            if ($sortBy == '') {
                $sortBy = 'surname';
            }

            if (true) {

                if ($fromMatchId != '') {
                    $matchsql = '`w_sport_stats`.`mid` = ' . $fromMatchId . '';
                    $joinstatssql = 'LEFT JOIN `w_sport_stats` ON `player`.`id` = `w_sport_stats`.`pid`';
                }
                if (strtolower($only) == 'match') {
                    if (strtolower($scope) == 'total') {
                        $onlysql = '`total_matches` > 0';
                    } elseif (strtolower($scope) == 'season') {
                        $onlysql = '`season_matches` > 0';
                    } elseif (strtolower($scope) == 'match') {
                        $onlysql = '`match_matches` > 0';
                    }
                } elseif (strtolower($only) == 'goal') {
                    if (strtolower($scope) == 'total') {
                        $onlysql = '`total_goals` > 0';
                    } elseif (strtolower($scope) == 'season') {
                        $onlysql = '`season_goals` > 0';
                    } elseif (strtolower($scope) == 'match') {
                        $onlysql = '`match_goals` > 0';
                    }
                } elseif (strtolower($only) == 'assist') {
                    if (strtolower($scope) == 'total') {
                        $onlysql = '`total_assists` > 0';
                    } elseif (strtolower($scope) == 'season') {
                        $onlysql = '`season_assists` > 0';
                    } elseif (strtolower($scope) == 'match') {
                        $onlysql = '`match_assists` > 0';
                    }
                } elseif (strtolower($only) == 'shoot') {
                    if (strtolower($scope) == 'total') {
                        $onlysql = '`total_shoots` > 0';
                    } elseif (strtolower($scope) == 'season') {
                        $onlysql = '`season_shoots` > 0';
                    } elseif (strtolower($scope) == 'match') {
                        $onlysql = '`match_shoots` > 0';
                    }
                } elseif (strtolower($only) == 'penalty') {
                    if (strtolower($scope) == 'total') {
                        $onlysql = '`total_penalty` > 0';
                    } elseif (strtolower($scope) == 'season') {
                        $onlysql = '`season_penalty` > 0';
                    } elseif (strtolower($scope) == 'match') {
                        $onlysql = '`match_penalty` > 0';
                    }
                }
                if ($showGolmans == 'true') {
                    //$positionsql = '`player`.`position` = 1';
                    $positionsql = '(select sum(`mid`) from `w_sport_stats` where `pid` = `player`.`id` and `pos` = 1) > 0';
                } elseif ($showGolmans == 'false') {
                    //$positionsql = '(`player`.`position` = 2 OR `player`.`position` = 3)';
                }

                $subqueriessql .= ''
                        . (in_array('total_matches', $this->UsedFields) ? '(SELECT COUNT(`pid`) AS `matches` FROM `w_sport_stats` WHERE `pid` = `player`.`id`' . ($tableId != '' ? ' and `w_sport_stats`.`table_id` ' . self::resolveTableIdPartSql($tableId) : '') . ' and ' . self::resolveGolmanStatsPartSql($showGolmans) . ') AS `total_matches`,' : '')
                        . (in_array('total_goals', $this->UsedFields) ? '(SELECT SUM(`goals`) AS `matches` FROM `w_sport_stats` WHERE `pid` = `player`.`id`' . ($tableId != '' ? ' and `w_sport_stats`.`table_id` ' . self::resolveTableIdPartSql($tableId) : '') . ') AS `total_goals`,' : '')
                        . (in_array('total_assists', $this->UsedFields) ? '(SELECT SUM(`assists`) AS `matches` FROM `w_sport_stats` WHERE `pid` = `player`.`id`' . ($tableId != '' ? ' and `w_sport_stats`.`table_id` ' . self::resolveTableIdPartSql($tableId) : '') . ') AS `total_assists`,' : '')
                        . (in_array('total_penalty', $this->UsedFields) ? '(SELECT SUM(`penalty`) AS `matches` FROM `w_sport_stats` WHERE `pid` = `player`.`id`' . ($tableId != '' ? ' and `w_sport_stats`.`table_id` ' . self::resolveTableIdPartSql($tableId) : '') . ') AS `total_penalty`,' : '')
                        . (in_array('total_shoots', $this->UsedFields) ? '(SELECT SUM(`shoots`) AS `matches` FROM `w_sport_stats` WHERE `pid` = `player`.`id`' . ($tableId != '' ? ' and `w_sport_stats`.`table_id` ' . self::resolveTableIdPartSql($tableId) : '') . ') AS `total_shoots`,' : '')
                        . (in_array('total_percentage', $this->UsedFields) ? '(SELECT (100 / (SUM(`shoots`)) * (SUM(`shoots`) - SUM(`goals`))) AS `matches` FROM `w_sport_stats` WHERE `pid` = `player`.`id`' . ($tableId != '' ? ' and `w_sport_stats`.`table_id` ' . self::resolveTableIdPartSql($tableId) : '') . ') AS `total_percentage`,' : '')
                        . (in_array('total_average', $this->UsedFields) ? '(SELECT (SUM(`goals`) / COUNT(`pid`)) AS `matches` FROM `w_sport_stats` WHERE `pid` = `player`.`id`' . ($tableId != '' ? ' and `w_sport_stats`.`table_id` ' . self::resolveTableIdPartSql($tableId) : '') . ') AS `total_average`,' : '')
                        . (in_array('total_points', $this->UsedFields) ? '(SELECT (SUM(`goals`) + SUM(`assists`)) AS `matches` FROM `w_sport_stats` WHERE `pid` = `player`.`id`' . ($tableId != '' ? ' and `w_sport_stats`.`table_id` ' . self::resolveTableIdPartSql($tableId) : '') . ') AS `total_points`,' : '');

                if ($teamId != '') {
                    if (strlen($conditionssql) != 0) {
                        $conditionssql .= ' AND `player`.`team` = ' . $teamId;
                    } else {
                        $conditionssql .= ' `player`.`team` = ' . $teamId;
                    }
                }
                if ($seasonId != '') {
                    if (strlen($conditionssql) != 0) {
                        $conditionssql .= ' AND `player`.`season` = ' . $seasonId;
                    } else {
                        $conditionssql .= ' `player`.`season` = ' . $seasonId;
                    }

                    $subqueriessql .= /* (strlen($subqueriessql) != 0 ? ', ' : '') */''
                            . (in_array('season_matches', $this->UsedFields) ? '(SELECT COUNT(`pid`) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`season` = ' . $seasonId . ' AND `pid` = `player`.`id`' . ($tableId != '' ? ' and `w_sport_stats`.`table_id` ' . self::resolveTableIdPartSql($tableId) : '') . ' and ' . self::resolveGolmanStatsPartSql($showGolmans) . ') AS `season_matches`,' : '')
                            . (in_array('season_goals', $this->UsedFields) ? '(SELECT SUM(`goals`) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`season` = ' . $seasonId . ' AND `pid` = `player`.`id`' . ($tableId != '' ? ' and `w_sport_stats`.`table_id` ' . self::resolveTableIdPartSql($tableId) : '') . ' and ' . self::resolveGolmanStatsPartSql($showGolmans) . ') AS `season_goals`,' : '')
                            . (in_array('season_assists', $this->UsedFields) ? '(SELECT SUM(`assists`) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`season` = ' . $seasonId . ' AND `pid` = `player`.`id`' . ($tableId != '' ? ' and `w_sport_stats`.`table_id` ' . self::resolveTableIdPartSql($tableId) : '') . ' and ' . self::resolveGolmanStatsPartSql($showGolmans) . ') AS `season_assists`,' : '')
                            . (in_array('season_penalty', $this->UsedFields) ? '(SELECT SUM(`penalty`) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`season` = ' . $seasonId . ' AND `pid` = `player`.`id`' . ($tableId != '' ? ' and `w_sport_stats`.`table_id` ' . self::resolveTableIdPartSql($tableId) : '') . ' and ' . self::resolveGolmanStatsPartSql($showGolmans) . ') AS `season_penalty`,' : '')
                            . (in_array('season_shoots', $this->UsedFields) ? '(SELECT SUM(`shoots`) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`season` = ' . $seasonId . ' AND `pid` = `player`.`id`' . ($tableId != '' ? ' and `w_sport_stats`.`table_id` ' . self::resolveTableIdPartSql($tableId) : '') . ' and ' . self::resolveGolmanStatsPartSql($showGolmans) . ') AS `season_shoots`,' : '')
                            . (in_array('season_percentage', $this->UsedFields) ? '(SELECT (100 / (SUM(`shoots`)) * (SUM(`shoots`) - SUM(`goals`))) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`season` = ' . $seasonId . ' AND `pid` = `player`.`id`' . ($tableId != '' ? ' and `w_sport_stats`.`table_id` ' . self::resolveTableIdPartSql($tableId) : '') . ' and ' . self::resolveGolmanStatsPartSql($showGolmans) . ') AS `season_percentage`,' : '')
                            . (in_array('season_average', $this->UsedFields) ? '(SELECT (SUM(`goals`) / COUNT(`pid`)) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`season` = ' . $seasonId . ' AND `pid` = `player`.`id`' . ($tableId != '' ? ' and `w_sport_stats`.`table_id` ' . self::resolveTableIdPartSql($tableId) : '') . ' and ' . self::resolveGolmanStatsPartSql($showGolmans) . ') AS `season_average`,' : '')
                            . (in_array('season_points', $this->UsedFields) ? '(SELECT (SUM(`goals`) + SUM(`assists`)) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`season` = ' . $seasonId . ' AND `pid` = `player`.`id`' . ($tableId != '' ? ' and `w_sport_stats`.`table_id` ' . self::resolveTableIdPartSql($tableId) : '') . ' and ' . self::resolveGolmanStatsPartSql($showGolmans) . ') AS `season_points`,' : '');
                }
                if (strlen($matchsql) != 0) {
                    if (strlen($conditionssql) != 0) {
                        $conditionssql .= ' AND ' . $matchsql;
                    } else {
                        $conditionssql .= ' ' . $matchsql;
                    }
                    $subqueriessql .= ', '
                            . (in_array('match_goals', $this->UsedFields) ? '(SELECT SUM(`goals`) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`mid` = ' . $fromMatchId . ' AND `pid` = `player`.`id`' . ($tableId != '' ? ' and `w_sport_stats`.`table_id` ' . self::resolveTableIdPartSql($tableId) : '') . ') AS `match_goals`,' : '')
                            . (in_array('match_assists', $this->UsedFields) ? '(SELECT SUM(`assists`) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`mid` = ' . $fromMatchId . ' AND `pid` = `player`.`id`' . ($tableId != '' ? ' and `w_sport_stats`.`table_id` ' . self::resolveTableIdPartSql($tableId) : '') . ') AS `match_assists`,' : '')
                            . (in_array('match_penalty', $this->UsedFields) ? '(SELECT SUM(`penalty`) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`mid` = ' . $fromMatchId . ' AND `pid` = `player`.`id`' . ($tableId != '' ? ' and `w_sport_stats`.`table_id` ' . self::resolveTableIdPartSql($tableId) : '') . ') AS `match_penalty`,' : '')
                            . (in_array('match_shoots', $this->UsedFields) ? '(SELECT SUM(`shoots`) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`mid` = ' . $fromMatchId . ' AND `pid` = `player`.`id`' . ($tableId != '' ? ' and `w_sport_stats`.`table_id` ' . self::resolveTableIdPartSql($tableId) : '') . ') AS `match_shoots`,' : '')
                            . (in_array('match_percentage', $this->UsedFields) ? '(SELECT (100 / (SUM(`shoots`)) * (SUM(`shoots`) - SUM(`goals`))) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`mid` = ' . $fromMatchId . ' AND `pid` = `player`.`id`' . ($tableId != '' ? ' and `w_sport_stats`.`table_id` ' . self::resolveTableIdPartSql($tableId) : '') . ') AS `match_percentage`,' : '')
                            . (in_array('match_average', $this->UsedFields) ? '(SELECT (SUM(`goals`) / COUNT(`pid`)) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`mid` = ' . $fromMatchId . ' AND `pid` = `player`.`id`' . ($tableId != '' ? ' and `w_sport_stats`.`table_id` ' . self::resolveTableIdPartSql($tableId) : '') . ') AS `match_average`,' : '')
                            . (in_array('match_points', $this->UsedFields) ? '(SELECT (SUM(`goals`) + SUM(`assists`)) AS `matches` FROM `w_sport_stats` WHERE `w_sport_stats`.`mid` = ' . $fromMatchId . ' AND `pid` = `player`.`id`' . ($tableId != '' ? ' and `w_sport_stats`.`table_id` ' . self::resolveTableIdPartSql($tableId) : '') . ') AS `match_points`,' : '');
                }
                if (strlen($positionsql) != 0) {
                    if (strlen($conditionssql) != 0) {
                        $conditionssql .= ' AND ' . $positionsql;
                    } else {
                        $conditionssql .= ' ' . $positionsql;
                    }
                }
                if ($playerId != false) {
                    if (strlen($conditionssql) != 0) {
                        $conditionssql .= ' AND `player`.`id` = ' . $playerId;
                    } else {
                        $conditionssql .= ' `player`.`id` = ' . $playerId;
                    }
                }
                if ($limit != false) {
                    $limitsql = 'LIMIT ';
                    if ($offset != false) {
                        $limitsql .= $offset . ', ';
                    }
                    $limitsql .= $limit;
                }

                if ($type == 'most') {
                    $cols = 'distinct `player`.`id`, `player`.`name`, `player`.`surname`, `player`.`birthyear`, `player`.`number`, `player`.`position`, `player`.`photo`, `player`.`team` as `team-id`';
                } else {
                    $cols = 'distinct `player`.`id`, `w_sport_team`.`id` AS `team-id`';
                }

                if ($subqueriessql[strlen($subqueriessql) - 1] == ',') {
                    $subqueriessql = substr($subqueriessql, 0, strlen($subqueriessql) - 1);
                }

                if ($tableId != '') {
                    if (strlen($joinstatssql) != 0) {
                        $joinstatssql .= ', ';
                    }
                    $joinstatssql .= 'join `w_sport_table` on `player`.`team` = `w_sport_table`.`team`';
                    if (strlen($conditionssql) != 0) {
                        $conditionssql .= ' and';
                    }
                    $conditionssql .= ' `w_sport_table`.`table_id` ' . self::resolveTableIdPartSql($tableId);
                }
                
                $sqlQuery = 'SELECT ' . $cols . (strlen($subqueriessql) != 0 ? ', ' : '') . $subqueriessql . ' FROM `w_sport_player` AS `player` JOIN `w_sport_team` ON `player`.`team` = `w_sport_team`.`id`' . ((strlen($joinstatssql) != 0) ? ' ' . $joinstatssql : '') . '' . ((strlen($conditionssql) != 0) ? ' WHERE ' . $conditionssql : '') . ' GROUP BY `player`.`id`' . ' ORDER BY ' . self::sortByStringToSqlParams($sortBy, $sorting) . ((strlen($limitsql) != 0) ? ' ' . $limitsql : '');
                
                if (strlen($onlysql) != 0) {
                    $sqlQuery = 'SELECT * FROM (' . $sqlQuery . ') AS q WHERE ' . $onlysql;
                }
                
                $sqlQuery .= ';';
                $players = $dbObject->fetchAll($sqlQuery);
            }

            return $players;
        }

        private function resolveTableIdPartSql($tableId) {
            if (strpos(',', $tableId) != -1) {
                return ' in (' . $tableId . ')';
            } else {
                return ' = ' . $tableId;
            }
        }

        private function resolveGolmanStatsPartSql($showGolmans) {
            if ($showGolmans == 'true') {
                return '`pos` = 1';
            } elseif ($showGolmans == 'false') {
                return '(`pos` = 2 OR `pos` = 3)';
            } else {
                return '(`pos` = 1 OR `pos` = 2 OR `pos` = 3)';
            }
        }

        private function sortByStringToSqlParams($sortBy, $sorting) {
            $return = '';
            $sorts = explode(',', $sortBy);
            foreach ($sorts as $sort) {
                if ($return != '') {
                    $return .= ', ';
                }
                $return .= '`' . $sort . '` ' . $sorting;
            }
            return $return;
        }

        /* ======================== PROPERTIES ================================= */

        public function setProjectId($projectId) {
            parent::session()->set('project-id', $projectId, 'sport');
            return $projectId;
        }

        public function getProjectId() {
            if (parent::session()->exists('project-id', 'sport')) {
                return parent::session()->get('project-id', 'sport');
            } else {
                return '-1';
            }
        }

        public function setSeasonId($seasonId) {
            parent::session()->set('season-id', $seasonId, 'sport');
            return $seasonId;
        }

        public function getSeasonId() {
            if (parent::session()->exists('season-id', 'sport')) {
                return parent::session()->get('season-id', 'sport');
            } else {
                return '-1';
            }
        }

        public function setTableId($tableId) {
            parent::session()->set('table-id', $tableId, 'sport');
            return $seasonId;
        }

        public function getTableId() {
            if (parent::session()->exists('table-id', 'sport')) {
                return parent::session()->get('table-id', 'sport');
            } else {
                return '-1';
            }
        }

        public function setTeamId($teamId) { 
            parent::session()->set('team-id', $teamId, 'sport');
            return $teamId;
        }

        public function getTeamId() {
            if (parent::session()->exists('team-id', 'sport')) {   
                return parent::session()->get('team-id', 'sport');
            } else {
                return '-1';
            }
        }

        public function setHomeTeamId($teamId) {
            parent::session()->set('home-team-id', $teamId, 'sport');
            return $teamId;
        }

        public function getHomeTeamId() {
            if (parent::session()->exists('home-team-id', 'sport')) {
                return parent::session()->get('home-team-id', 'sport');
            } else {
                return '-1';
            }
        }

        public function setAwayTeamId($teamId) {
            parent::session()->set('away-team-id', $teamId, 'sport');
            return $teamId;
        }

        public function getAwayTeamId() {
            if (parent::session()->exists('away-team-id', 'sport')) {
                return parent::session()->get('away-team-id', 'sport');
            } else {
                return '-1';
            }
        }

        public function setPlayerId($playerId) {
            parent::session()->set('player-id', $playerId, 'sport');
            return $playerId;
        }

        public function getPlayerId() {
            if (parent::session()->exists('player-id', 'sport')) {
                return parent::session()->get('player-id', 'sport');
            } else {
                return '-1';
            }
        }

        public function setMatchId($matchId) {
            parent::session()->set('match-id', $matchId, 'sport');
            return $matchId;
        }

        public function getMatchId() {
            if (parent::session()->exists('match-id', 'sport')) {
                return parent::session()->get('match-id', 'sport');
            } else {
                return '-1';
            }
        }

        public function setRoundId($roundId) {
            parent::session()->set('round-id', $roundId, 'sport');
            return $roundId;
        }

        public function getRoundId() {
            if (parent::session()->exists('round-id', 'sport')) {
                return parent::session()->get('round-id', 'sport');
            } else {
                return '-1';
            }
        }

        // Urls

        public function setProjectUrl($url) {
            $row = parent::db()->fetchAll('select `id` from `w_sport_project` where `url` = "' . $url . '";');
            if (count($row) == 1) {
                self::setProjectId($row[0]['id']);
                return $url;
            } else {
                return '??';
            }
        }

        public function getProjectUrl() {
            if (self::getProjectId() != '-1') {
                $url = parent::db()->fetchSingle('select `url` from `w_sport_project` where `id` = ' . self::getProjectId() . ';');
                return $url['url'];
            } else {
                return '-1';
            }
        }

        public function setSeasonUrl($url) {
            $url1 = explode('-', $url);
            $row = parent::db()->fetchAll('select `id` from `w_sport_season` where `start_year` = ' . $url1[0] . ' and `end_year` = ' . $url1[1] . (self::getProjectId() != '-1' ? ' and `project_id` = ' . self::getProjectId() : '') . ';');
            if (count($row) == 1) {
                self::setSeasonId($row[0]['id']);
                return $url;
            } else {
                return '??';
            }
        }

        public function getSeasonUrl() {
            if (self::getSeasonId() != '-1') {
                $url = parent::db()->fetchSingle('select `start_year`, `end_year` from `w_sport_season` where `id` = ' . self::getSeasonId() . ';');
                return $url['start_year'] . '-' . $url['end_year'];
            } else {
                return '-1';
            }
        }

        public function setTeamUrl($url) {
            $row = parent::db()->fetchAll('select `id` from `w_sport_team` where `url` = "' . $url . '"'.(self::getSeasonId() != '-1' ? ' and `season` = '.self::getSeasonId() : '').'' . (self::getProjectId() != '-1' ? ' and `project_id` = ' . self::getProjectId() : '') . ';');
            if (count($row) == 1) {
                self::setTeamId($row[0]['id']);
                return $url;
            } else {
                return '??';
            }
        }

        public function getTeamUrl() {
            if (self::getTeamId() != '-1') {
                $url = parent::db()->fetchSingle('select `url` from `w_sport_team` where `id` = ' . self::getTeamId() . ';');
                return $url['url'];
            } else {
                return '-1';
            }
        }

        public function setPlayerUrl($url) {
            $row = parent::db()->fetchAll('select `id` from `w_sport_player` where `url` = "' . $url . '"'.(self::getSeasonId() != '-1' ? ' and `season` = '.self::getSeasonId() : '').'' . (self::getProjectId() != '-1' ? ' and `project_id` = ' . self::getProjectId() : '') . ';');
            if (count($row) == 1) {
                self::setPlayerId($row[0]['id']);
                return $url;
            } else {
                return '??';
            }
        }

        public function getPlayerUrl() {
            if (self::getPlayerId() != '-1') {
                $url = parent::db()->fetchSingle('select `url` from `w_sport_player` where `id` = ' . self::getPlayerId() . ';');
                return $url['url'];
            } else {
                return '-1';
            }
        }

    }

?>