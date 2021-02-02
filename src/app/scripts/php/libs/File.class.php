<?php


    require_once("BaseTagLib.class.php");
    require_once("FileAdmin.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/LocalizationBundle.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/UrlResolver.class.php");

    /**
     * 
     *  FileSystem Class.
     *      
     *  @author     Marek SMM
     *  @timestamp  2012-01-29
     * 
     */  
    class File extends BaseTagLib {

        /**
         *
         *  Holds file web app extensions.
         *
         */                   
        public $FileEx;
        
        /**
         *
         *  Holds file id when dynamic rewrite.
         *
         */                        
        private $CurrentId = 0;

        public function __construct() {
            parent::setTagLibXml("File.xml");
            self::setLocalizationBundle("file");
        }
        
        function __destruct() {
            //unset($_SESSION['dir-id']);
        }

        protected function canUserFile($objectId, $rightType) {
            return RoleHelper::isInRole(parent::login()->getGroupsIds(), RoleHelper::getRights(FileAdmin::$FileRightDesc, $objectId, $rightType));
        }
        
        /**
         *
         *  Generates list of directories and files from FS.
         *  C tag. DEPRECATED: Use FileAdmin
         *  
         *  @param    dirId   				id of parent directory
         *  @param		useFrames		    use frames in output
         *  @return   list of directories and files from FS.
         *
         */                   
        public function showDirectory($dirId = false, $editable = false, $useFrames = false, $showParent = false, $showTitleInsteadOfName = false, $browsable = true, $parentName = false, $nameWithExtension = false, $fileNameHeader = false) {
            global $dbObject;
            global $loginObject;
            $rb = self::rb();
            $return = "";
            $origDirId = $dirId;

            if(!$parentName) {
                $parentName = '..';
            }
            
            if(!$fileNameHeader) {
                $fileNameHeader = $rb->get('file.name').':';
            }

            if($browsable) {
                $dirId = self::setDirId($dirId);
            }
                
            if($_POST['delete-dir'] == $rb->get('dir.delete')) {
                $directoryId = $_POST['directory-id'];
                
                //test na prava mazani z adresare
                $permission = $dbObject->fetchAll('SELECT `value` FROM `directory_right` LEFT JOIN `group` ON `directory_right`.`gid` = `group`.`gid` WHERE `directory_right`.`did` = '.$directoryId.' AND `directory_right`.`type` = '.WEB_R_DELETE.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) ORDER BY `value` DESC;');
                if(count($permission) > 0 && $editable != "false") {
                    $subd = $dbObject->fetchAll("SELECT count(`id`) AS `count` FROM `directory` WHERE `parent_id` = ".$directoryId.";");
                    $subf = $dbObject->fetchAll("SELECT count(`id`) AS `count` FROM `file` WHERE `dir_id` = ".$directoryId.";");
                
                    if($subd[0]['count'] == 0 && $subf[0]['count'] == 0) {
                        $path = self::getPhysicalPathTo($directoryId);
                        $dbObject->execute("DELETE FROM `directory` WHERE `id` = ".$directoryId.";");
                        $dbObject->execute("DELETE FROM `directory_right` WHERE `id` = ".$directoryId.";");
                        rmdir($path);
                    } else {
                        $return .= '<h4 class="error">'.$rb->get('dir.notempty').'</h4>';
                    }
                } else {
                    $return .= '<h4 class="error">'.$rb->get('permissiondenied').'</h4>';
                }
            } elseif($_POST['delete-file'] == $rb->get('file.delete')) {
                $fileId = $_POST['file-id'];
                
                //test na prava mazani z adresare
                $permission = $dbObject->fetchAll('SELECT `value` FROM `file_right` LEFT JOIN `group` ON `file_right`.`gid` = `group`.`gid` WHERE `file_right`.`fid` = '.$fileId.' AND `file_right`.`type` = '.WEB_R_DELETE.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) ORDER BY `value` DESC;');
                if(count($permission) > 0) {
                    $file = $dbObject->fetchAll("SELECT `name`, `dir_id`, `type` FROM `file` WHERE `id` = ".$fileId.";");
                    if(count($file) == 1) {
                        $dbObject->execute("DELETE FROM `file` WHERE `id` = ".$fileId.";");
                        $dbObject->execute("DELETE FROM `file_right` WHERE `id` = ".$fileId.";");
                        $path = self::getPhysicalPathTo($file[0]['dir_id']);
                        $filePath = $path . $file[0]['name'] . "." . FileAdmin::$FileExtensions[$file[0]['type']];
                        //echo $filePath;
                        unlink($filePath);
                    }
                } else {
                    $return .= '<h4 class="error">Permission Denied!</h4>';
                }
            }
                
            if($showParent == "false") {
                $showParent = false;
            } else if($showParent == "root") {
                $showParent = $origDirId != $dirId;
            } else {
                $showParent = true;
            }
        
            if($useFrames != 'false') {
                return parent::getFrame($rb->get('dir.filelist')." :: /" . self::getPhysicalUrlTo($dirId), $return.self::getList($dirId, $editable, $showParent, $showTitleInsteadOfName == "true"), "", true, $parentName, $nameWithExtension, $fileNameHeader);
            } else {
                return $return.self::getList($dirId, $editable, $showParent, $showTitleInsteadOfName == "true", $parentName, $nameWithExtension, $fileNameHeader);
            }
        }
        
        /**
        *
        *  Generates list of files.
        *  
        *  @param    dirId     dir id to load from
        *  @param    editable  if true, it shows editing form
        *  @return   list of files     
        *
        */                        
        private function getList($dirId, $editable, $showParent, $showTitleInsteadOfName, $parentName, $nameWithExtension, $fileNameHeader) {
            global $dbObject;
            global $loginObject;
            $rb = self::rb();
            $return = "";
                
                $dirs = $dbObject->fetchAll('SELECT distinct `directory`.`id`, `directory`.`name`, .`directory`.`timestamp` FROM `directory` LEFT JOIN `directory_right` ON `directory`.`id` = `directory_right`.`did` LEFT JOIN `group` ON `directory_right`.`gid` = `group`.`gid` WHERE `parent_id` = '.$dirId.' AND `directory_right`.`type` = '.WEB_R_READ.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) ORDER BY `name`;');
                $dir = $dbObject->fetchAll("SELECT `parent_id` FROM `directory` WHERE `id` = ".$dirId.";");
                
                $return .= '' 
                .'<table class="dir-list">'
                .'<tr class="dir-header-row">'
                    .'<th class="th-icon"></th>'
                    .'<th class="th-id"><span>'.$rb->get('file.id').':</span></th>'
                    .'<th class="th-name">'.$fileNameHeader.'</th>'
                    .'<th class="th-dir-physical-path">'.$rb->get('file.directlink').':</th>'
                    .'<th class="th-timestamp">'.$rb->get('file.timestamp').'</th>'
                    .'<th class="th-type">'.$rb->get('file.type').'</th>'
                    .(($editable != "false") ? '<th class="th-edit">'.$rb->get('file.action').'</th>' : '' )
                .'</tr>'
            .($showParent ? ''
                .'<tr class="dir-parent-row">'
                    .'<td class="dir-icon"></td>'
                    .'<td class="dir-id"></td>'
                    .'<td class="dir-name">'
                    .'<form name="dir-form" method="post" action="'.$_SERVER['REQUEST_URI'].'">'
                        .'<input type="hidden" name="dir-id" value="'.(($dirId != 0) ? $dir[0]['parent_id'] : $dirId).'" />'
                        .'<input type="submit" name="ch-dir" value="'.$parentName.'" />'
                    .'</form>'
                    .'</td>'
                    .'<td class="dir-physical-path"></td>'
                    .'<td class="dir-timestamp"></td>'
                    .'<td class="dir-type"></td>'
                    .(($editable != "false") ? '<td class="dir-edit"></td>' : '' )
                .'</tr>'
            : '');
                
            $n = 0;
            // sudy   even
            // lichy  idle
            foreach($dirs as $dir) {
                $return .= ''
                .'<tr class="'.((($n % 2) == 0) ? 'even' : 'idle').'">'
                    .'<td class="dir-icon dir"></td>'
                    .'<td class="dir-id dir"><span>'.$dir['id'].'</span></td>'
                    .'<td class="dir-name">'
                    .'<form name="dir-form" method="post" action="'.$_SERVER['REQUEST_URI'].'">'
                        .'<input type="hidden" name="dir-id" value="'.$dir['id'].'" />'
                        .'<input type="submit" name="ch-dir" value="'.$dir['name'].'" />'
                    .'</form>'
                    .'</td>'
                    .'<td class="dir-physical-path"></td>'
                    .'<td class="dir-timestamp">'
                .'<span class="dir-timestamp-date">'.date('d.m.Y', $dir['timestamp']).'</span> '
                .'<span class="dir-timestamp-time">'.date('H:i:s', $dir['timestamp']).'</span> '
                .'</td>'
                    .'<td class="dir-type"></td>'	
                    .(($editable != "false") ? ''
                    .'<td class="dir-edit">'
                    .'<form name="dir-edit" method="post" action="'.$_SERVER['REQUEST_URI'].'">'
                        .'<input type="hidden" name="directory-id" value="'.$dir['id'].'" />'
                        .'<input type="hidden" name="edit-dir" value="'.$rb->get('dir.edit').'" />'
                        .'<input type="image" src="~/images/page_edi.png" name="edit-dir" value="'.$rb->get('dir.edit').'" title="'.$rb->get('dir.edithint').'" />'
                    .'</form> '
                    .'<form name="dir-edit" method="post" action="'.$_SERVER['REQUEST_URI'].'">'
                        .'<input type="hidden" name="directory-id" value="'.$dir['id'].'" />'
                        .'<input type="hidden" name="delete-dir" value="'.$rb->get('dir.delete').'" />'
                        .'<input class="confirm" type="image" src="~/images/page_del.png" name="delete-dir" value="'.$rb->get('dir.delete').'" title="'.$rb->get('dir.deletehint').', id('.$dir['id'].')" />'
                    .'</form>'
                    .'</td>'
                    : '' )
                .'</tr>';
                $n ++;
            }
            
            //$files = $dbObject->fetchAll("SELECT `id`, `name`, `title`, `timestamp`, `type` FROM `file` WHERE `dir_id` = ".$dirId." ORDER BY `name`;");
            $files = $dbObject->fetchAll('SELECT distinct `file`.`id`, `file`.`name`, `file`.`title`, `file`.`timestamp`, `file`.`type` FROM `file` LEFT JOIN `file_right` ON `file`.`id` = `file_right`.`fid` LEFT JOIN `group` ON `file_right`.`gid` = `group`.`gid` WHERE `dir_id` = '.$dirId.' AND `file_right`.`type` = '.WEB_R_READ.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) ORDER BY `name`;');
        
            foreach($files as $file) {
                $return .= ''
                .'<tr class="'.((($n % 2) == 0) ? 'even' : 'idle').'">'
                    .'<td class="file-icon '.FileAdmin::$FileExtensions[$file['type']].'"></td>'
                    .'<td class="file-id"><span>'.$file['id'].'</span></td>'
                    .'<td class="file-name">'
                    .'<a target="_blank" title="'.$file['title'].'" href="~/file.php?rid='.$file['id'].'">'
                .($showTitleInsteadOfName && strlen($file['title']) != 0 ? ''
                .$file['title']
                : ''
                .$file['name']
                )
                .($nameWithExtension ? '.'.FileAdmin::$FileExtensions[$file['type']] : '')
                .'</a>'
                    .'</td>'
                    .'<td class="dir-physical-path">'
                        .'<a href="'.self::getPhysicalUrlTo($dirId).$file['name'].".".FileAdmin::$FileExtensions[$file['type']].'" target="_blank">open</a>'
                    .'</td>'
                    .'<td class="file-timestamp">'
                .'<span class="file-timestamp-date">'.date('d.m.Y', $file['timestamp']).'</span> '
                .'<span class="file-timestamp-time">'.date('H:i:s', $file['timestamp']).'</span> '
                .'</td>'
                    .'<td class="file-type">'.FileAdmin::$FileExtensions[$file['type']].'</td>'
                    .(($editable != "false") ? ''
                    .'<td class="file-edit">'
                    .'<form name="dir-edit" method="post" action="'.$_SERVER['REQUEST_URI'].'">'
                        .'<input type="hidden" name="file-id" value="'.$file['id'].'" />'
                        .'<input type="hidden" name="edit-file" value="'.$rb->get('file.edit').'" />'
                        .'<input type="image" src="~/images/page_edi.png" name="edit-file" value="'.$rb->get('file.edit').'" title="'.$rb->get('file.edithint').'" />'
                    .'</form> '
                    .'<form name="dir-edit" method="post" action="'.$_SERVER['REQUEST_URI'].'">'
                        .'<input type="hidden" name="file-id" value="'.$file['id'].'" />'
                        .'<input type="hidden" name="delete-file" value="'.$rb->get('file.delete').'" />'
                        .'<input class="confirm" type="image" src="~/images/page_del.png" name="delete-file" value="'.$rb->get('file.delete').'" title="'.$rb->get('file.deletehint').', id('.$file['id'].')" />'
                    .'</form>'
                    .'</td>'
                    : '' )
                .'</tr>';
                $n ++;
            }
            
            $return .= ''
            .'</table>';
            
            return $return;
        }
        
        /**
        *
        *  Shows form for add new directory.
        *  C tag. DEPRECATED: Use FileAdmin
        *  
        *  @param    dirId   				parent dir id
        *  @param		useFrames				use frames in output
        *  @return   form for adding directory               
        *
        */                        
        public function showNewDirectoryForm($dirId = false, $useFrames = false, $useRights = false) {
            global $dbObject;
            global $loginObject;
            $rb = self::rb();
            $return = "";
            $dirId = self::setDirId($dirId);
            
            if($_POST['new-directory'] == $rb->get('dir.new') || $_POST['edit-directory'] == $rb->get('dir.edithint')) {
                $directoryParentId = $_POST['directory-parent-id'];
                $directoryName = $_POST['directory-name'];
                $directoryUrl = $_POST['directory-url'];
                if($directoryUrl == "") {
                    $directoryUrl = strtolower(UrlUtils::toValidUrl($directoryName));
                }
                $read = $_POST['directory-right-edit-groups-r'];
                $write = $_POST['directory-right-edit-groups-w'];
                $delete = $_POST['directory-right-edit-groups-d'];
                    
                //test na prava zapisu do adresare
                $permission = $dbObject->fetchAll('SELECT `value` FROM `directory_right` LEFT JOIN `group` ON `directory_right`.`gid` = `group`.`gid` WHERE `directory_right`.`did` = '.$directoryParentId.' AND `directory_right`.`type` = '.WEB_R_WRITE.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) ORDER BY `value` DESC;');
                if(count($permission) > 0) {
                    if(strlen($directoryName) > 0) {
                        if($_POST['new-directory'] == $rb->get('dir.new')) {
                            $sib = $dbObject->fetchAll('SELECT count(`id`) AS `count` FROM `directory` WHERE `name` = "'.$directoryName.'" AND `parent_id` = '.$directoryParentId.';');
                        } elseif($_POST['edit-directory'] == "Edit directory") {
                            $par = $dbObject->fetchAll('SELECT `name`, `parent_id` FROM `directory` WHERE `id` = '.$directoryParentId.';'); 
                            $sib = $dbObject->fetchAll('SELECT count(`id`) AS `count` FROM `directory` WHERE `name` = "'.$directoryName.'" AND `parent_id` = '.$par[0]['parent_id'].';');
                        }
                        
                    if(($_POST['new-directory'] == $rb->get('dir.new') && $sib[0]['count'] == 0) || ($_POST['edit-directory'] == $rb->get('dir.edithint') && (($par[0]['name'] == $directoryName && $sib[0]['count'] == 1) || ($par[0]['name'] != $directoryName && $sib[0]['count'] == 0)))) {
                        if($_POST['new-directory'] == $rb->get('dir.new')) {
                                $dbObject->execute("INSERT INTO `directory`(`parent_id`, `name`, `url`, `timestamp`) VALUES(".$directoryParentId.", \"".$directoryName."\", \"".$directoryUrl."\", ".time().");");
                            $directoryId = $dbObject->fetchAll('SELECT MAX(`id`) AS `id` FROM `directory`;');
                                $directoryId = $directoryId[0]['id'];
                            $path = self::getPhysicalPathTo($directoryParentId).$directoryName;
                            mkdir($path);
                        } elseif($_POST['edit-directory'] == $rb->get('dir.edithint')) {
                            $oldName = $dbObject->fetchAll('SELECT `name`, `parent_id` FROM `directory` WHERE `id` = '.$directoryParentId.';');
                            $path1 = substr(self::getPhysicalPathTo($oldName[0]['parent_id']).$oldName[0]['name'].'/', 1);
                            $path2 = substr(self::getPhysicalPathTo($oldName[0]['parent_id']).$directoryName.'/', 1);
                            rename($path1, $path2);
                            $dbObject->execute('UPDATE `directory` SET `name` = "'.$directoryName.'", `url` = "'.$directoryUrl.'", `timestamp` = "'.time().'" WHERE `id` = '.$directoryParentId.';');
                            $directoryId = $directoryParentId;
                        } else {
                            return;
                        }
                        
                        if(count($read) != 0) {
                            $dbR = $dbObject->fetchAll("SELECT `gid` FROM `directory_right` WHERE `directory_right`.`did` = ".$directoryId." AND `type` = ".WEB_R_READ.";");
                            foreach($dbR as $right) {
                                if(!in_array($right, $read)) {
                                    $dbObject->execute("DELETE FROM `directory_right` WHERE `did` = ".$directoryId." AND `type` = ".WEB_R_READ.";");
                                }
                            }
                            foreach($read as $right) {
                                $row = $dbObject->fetchAll("SELECT `gid` FROM `directory_right` WHERE `did` = ".$directoryId." AND `type` = ".WEB_R_READ." AND `gid` = ".$right.";");
                                if(count($row) == 0) {
                                    $dbObject->execute("INSERT INTO `directory_right`(`did`, `gid`, `type`) VALUES (".$directoryId.", ".$right.", ".WEB_R_READ.");");
                                }
                            }
                        } else {
                            $rights = $dbObject->fetchAll('SELECT `gid` FROM `directory_right` WHERE `did` = '.$directoryParentId.' AND `type` = '.WEB_R_READ.';');
                            foreach($rights as $right) {
                                $dbObject->execute('INSERT INTO `directory_right`(`did`, `gid`, `type`) VALUES ('.$directoryId.', '.$right['gid'].', '.WEB_R_READ.');');
                            }
                        }

                        if(count($write) != 0) {
                            $dbR = $dbObject->fetchAll("SELECT `gid` FROM `directory_right` WHERE `directory_right`.`did` = ".$directoryId." AND `type` = ".WEB_R_WRITE.";");
                            foreach($dbR as $right) {
                                if(!in_array($right, $write)) {
                                    $dbObject->execute("DELETE FROM `directory_right` WHERE `did` = ".$directoryId." AND `type` = ".WEB_R_WRITE.";");
                                }
                            }
                            foreach($write as $right) {
                                $row = $dbObject->fetchAll("SELECT `gid` FROM `directory_right` WHERE `did` = ".$directoryId." AND `type` = ".WEB_R_WRITE." AND `gid` = ".$right.";");
                                if(count($row) == 0) {
                                    $dbObject->execute("INSERT INTO `directory_right`(`did`, `gid`, `type`) VALUES (".$directoryId.", ".$right.", ".WEB_R_WRITE.");");
                                }
                            }
                        } else {
                            $rights = $dbObject->fetchAll('SELECT `gid` FROM `directory_right` WHERE `did` = '.$directoryParentId.' AND `type` = '.WEB_R_WRITE.';');
                            foreach($rights as $right) {
                                $dbObject->execute('INSERT INTO `directory_right`(`did`, `gid`, `type`) VALUES ('.$directoryId.', '.$right['gid'].', '.WEB_R_WRITE.');');
                            }
                        }

                        if(count($delete) != 0) {
                            $dbR = $dbObject->fetchAll('SELECT `gid` FROM `directory_right` WHERE `directory_right`.`did` = '.$directoryId.' AND `type` = '.WEB_R_DELETE.';');
                            foreach($dbR as $right) {
                                if(!in_array($right, $delete)) {
                                    $dbObject->execute("DELETE FROM `directory_right` WHERE `did` = ".$directoryId." AND `type` = ".WEB_R_DELETE.";");
                                }
                            }

                            foreach($delete as $right) {
                                $row = $dbObject->fetchAll('SELECT `gid` FROM `directory_right` WHERE `did` = '.$directoryId.' AND `type` = '.WEB_R_DELETE.' AND `gid` = '.$right.';');
                                if(count($row) == 0) {
                                    $dbObject->execute("INSERT INTO `directory_right`(`did`, `gid`, `type`) VALUES (".$directoryId.", ".$right.", ".WEB_R_DELETE.");");
                                }
                            }
                        } else {
                            $rights = $dbObject->fetchAll('SELECT `gid` FROM `directory_right` WHERE `did` = '.$directoryParentId.' AND `type` = '.WEB_R_DELETE.';');
                            foreach($rights as $right) {
                                $dbObject->execute('INSERT INTO `directory_right`(`did`, `gid`, `type`) VALUES ('.$directoryId.', '.$right['gid'].', '.WEB_R_DELETE.');');
                            }
                        }
                    } else {
                        $message = '<h4 class="error">'.$rb->get('dir.notuniquename').'</h4>';
                        $return .= $message;//parent::getFrame("Error Message", $message, "", true);
                        if($_POST['edit-directory'] == $rb->get('dir.edithint')) {
                            $_POST['edit-dir'] = $rb->get('dir.edit');
                            $_POST['directory-id'] = $directoryParentId;
                        }
                    }
                    } else {
                        $return .= '<h4 class="error">'.$rb->get('dir.namelength').'</h4>';
                    }
                } else {
                    $return .= '<h4 class="error">'.$rb->get('permissiondenied').'</h4>';
                }
            }
            
        $dirName = '';
        if($_POST['edit-dir'] == $rb->get('dir.edit')) {
            $direcId = $_POST['directory-id'];
            $permission = $dbObject->fetchAll('SELECT `directory`.`name`, `directory`.`url`, `group`.`value` FROM `directory` LEFT JOIN `directory_right` ON `directory`.`id` = `directory_right`.`did` LEFT JOIN `group` ON `directory_right`.`gid` = `group`.`gid` WHERE `directory_right`.`did` = '.$direcId.' AND `directory_right`.`type` = '.WEB_R_WRITE.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) ORDER BY `value` DESC;');
            if(count($permission) > 0) {
                $dirId = $direcId;
                $dirName = $permission[0]['name'];
                $dirUrl = $permission[0]['url'];
            }
        }
            
            // Ziskat prava ....
        $show = array('read' => true, 'write' => true, 'delete' => false);
        $groupsR = $dbObject->fetchAll("SELECT `gid` FROM `directory_right` WHERE `did` = ".$dirId." AND `type` = ".WEB_R_READ.";");
        $groupsW = $dbObject->fetchAll("SELECT `gid` FROM `directory_right` WHERE `did` = ".$dirId." AND `type` = ".WEB_R_WRITE.";");
        $groupsD = $dbObject->fetchAll("SELECT `gid` FROM `directory_right` WHERE `did` = ".$dirId." AND `type` = ".WEB_R_DELETE.";");
        $allGroups = $dbObject->fetchAll('SELECT `gid`, `name` FROM `group` WHERE (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) ORDER BY `value`;');
        $groupSelectR = '<select id="directory-right-edit-groups-r" name="directory-right-edit-groups-r[]" multiple="multiple" size="5">';
        $groupSelectW = '<select id="directory-right-edit-groups-w" name="directory-right-edit-groups-w[]" multiple="multiple" size="5">';
        $groupSelectD = '<select id="directory-right-edit-groups-d" name="directory-right-edit-groups-d[]" multiple="multiple" size="5">';
        foreach($allGroups as $group) {
        $selectedR = false;
        $selectedW = false;
        $selectedD = false;
        foreach($groupsR as $gp) {
            if($gp['gid'] == $group['gid']) {
            $selectedR = true;
            $show['read'] = true;
            }
        }
        foreach($groupsW as $gp) {
            if($gp['gid'] == $group['gid']) {
            $selectedW = true;
            $show['write'] = true;
            }
        }
        foreach($groupsD as $gp) {
            if($gp['gid'] == $group['gid']) {
            $selectedD = true;
            $show['delete'] = true;
            }
        }
        $groupSelectR .= '<option'.(($selectedR) ? ' selected="selected"' : '').' value="'.$group['gid'].'">'.$group['name'].'</option>';
        $groupSelectW .= '<option'.(($selectedW) ? ' selected="selected"' : '').' value="'.$group['gid'].'">'.$group['name'].'</option>';
        $groupSelectD .= '<option'.(($selectedD) ? ' selected="selected"' : '').' value="'.$group['gid'].'">'.$group['name'].'</option>';
        }
        $groupSelectR .= '</select>';
        $groupSelectW .= '</select>';
        $groupSelectD .= '</select>';
        
        
            $return .= ''
            .'<form name="new-directory" method="post" action="'.$_SERVER['REQUEST_URI'].'">'
                .'<input type="hidden" name="directory-parent-id" value="'.$dirId.'" />'
                .'<div class="directory-name">'
                    .'<label for="directory-name">'.$rb->get('dir.name').':</label> '
                    .'<input type="text" id="directory-name" name="directory-name" value="'.$dirName.'" />'
                .'</div>'
                .'<div class="directory-name">'
                    .'<label for="directory-name">'.$rb->get('dir.url').':</label> '
                    .'<input type="text" id="directory-url" name="directory-url" value="'.$dirUrl.'" />'
                .'</div>'
            .(($useRights != "false") ? ''
                .'<div class="directory-rights">'
                    .'<div class="directory-right-r">'
                    .'<label for="directory-right-edit-groups-r">'.$rb->get('perm.read').':</label>'
                    .$groupSelectR
                    .'</div>'
                    .'<div class="directory-right-w">'
                    .'<label for="directory-right-edit-groups-w">'.$rb->get('perm.write').':</label>'
                    .$groupSelectW
                    .'</div>'
                    .(($_POST['edit-dir'] == 'Edit' && count($permission) > 0 && !$show['delete']) ? ''
                    : ''
                    .'<div class="directory-right-d">'
                    .'<label for="directory-right-edit-groups-d">'.$rb->get('perm.delete').':</label>'
                    .$groupSelectD
                    .'</div>'
                    )
                    .'<div class="clear"></div>'
                .'</div>'
            : '')
                .(($_POST['edit-dir'] == $rb->get('dir.edit') && count($permission) > 0) ? ''
                .'<div class="directory-submit">'
                    .'<input type="submit" name="edit-directory" value="'.$rb->get('dir.edithint').'" /> '
                    .'<input type="submit" name="back-directory" value="'.$rb->get('dir.back').'" />'
                .'</div>'
                : ''
                .'<div class="directory-submit">'
                    .'<input type="submit" name="new-directory" value="'.$rb->get('dir.new').'" />'
                .'</div>')
                .'</form>';
                
            if($useFrames != 'false') {
                return parent::getFrame((($_POST['edit-dir'] == $rb->get('dir.edit')) ? $rb->get('dir.edithint') : $rb->get('dir.new')), $return, "", true);
            } else {
                return $return;
            }
        }
        
        /**
            *
            *  Shows form for upload new file.
            *  C tag. DEPRECATED: Use FileAdmin
            *  
            *  @param    dirId   		dir id to put in
            *  @param		useRights		use manaul setting rights or use auto setting
            *  @param		useFrames		use frames in output
            *  @return   form for upload file                    
            *
            */                   
        public function showUploadForm($dirId = false, $useRights = false, $useFrames = false) {
            global $dbObject;
            global $loginObject;
            $rb = self::rb();
            $return = "";
            $dirId = self::setDirId($dirId);
            
            if(array_key_exists('file-name', $_POST)) {
            $return .= self::processFileUpload();
            }
            
            // Ziskat prava ....
            $show = array('read' => true, 'write' => true, 'delete' => false);
            if($_POST['edit-file'] != $rb->get('file.edit')) {
            $groupsR = $dbObject->fetchAll("SELECT `gid` FROM `directory_right` WHERE `did` = ".$dirId." AND `type` = ".WEB_R_READ.";");
            $groupsW = $dbObject->fetchAll("SELECT `gid` FROM `directory_right` WHERE `did` = ".$dirId." AND `type` = ".WEB_R_WRITE.";");
            $groupsD = $dbObject->fetchAll("SELECT `gid` FROM `directory_right` WHERE `did` = ".$dirId." AND `type` = ".WEB_R_DELETE.";");
            } else {
            $fileId = $_POST['file-id'];
            $file = $dbObject->fetchAll('SELECT `id`, `name`, `title` FROM `file` WHERE `id` = '.$fileId.';');
            $fileName = $file[0]['name'];
            $fileTitle = $file[0]['title'];
            $fileId = $file[0]['id'];
            $groupsR = $dbObject->fetchAll("SELECT `gid` FROM `file_right` WHERE `fid` = ".$fileId." AND `type` = ".WEB_R_READ.";");
            $groupsW = $dbObject->fetchAll("SELECT `gid` FROM `file_right` WHERE `fid` = ".$fileId." AND `type` = ".WEB_R_WRITE.";");
            $groupsD = $dbObject->fetchAll("SELECT `gid` FROM `file_right` WHERE `fid` = ".$fileId." AND `type` = ".WEB_R_DELETE.";");
            }
            $allGroups = $dbObject->fetchAll('SELECT `gid`, `name` FROM `group` WHERE (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) ORDER BY `value`;');
            $groupSelectR = '<select id="file-right-edit-groups-r" name="file-right-edit-groups-r[]" multiple="multiple" size="5">';
            $groupSelectW = '<select id="file-right-edit-groups-w" name="file-right-edit-groups-w[]" multiple="multiple" size="5">';
            $groupSelectD = '<select id="file-right-edit-groups-d" name="file-right-edit-groups-d[]" multiple="multiple" size="5">';
            foreach($allGroups as $group) {
            $selectedR = false;
            $selectedW = false;
            $selectedD = false;
            foreach($groupsR as $gp) {
                if($gp['gid'] == $group['gid']) {
                $selectedR = true;
                $show['read'] = true;
                }
            }
            foreach($groupsW as $gp) {
                if($gp['gid'] == $group['gid']) {
                $selectedW = true;
                $show['write'] = true;
                }
            }
            foreach($groupsD as $gp) {
                if($gp['gid'] == $group['gid']) {
                $selectedD = true;
                $show['delete'] = true;
                }
            }
            $groupSelectR .= '<option'.(($selectedR) ? ' selected="selected"' : '').' value="'.$group['gid'].'">'.$group['name'].'</option>';
            $groupSelectW .= '<option'.(($selectedW) ? ' selected="selected"' : '').' value="'.$group['gid'].'">'.$group['name'].'</option>';
            $groupSelectD .= '<option'.(($selectedD) ? ' selected="selected"' : '').' value="'.$group['gid'].'">'.$group['name'].'</option>';
            }
            $groupSelectR .= '</select>';
            $groupSelectW .= '</select>';
            $groupSelectD .= '</select>';
            
            $return .= ''
            .'<form name="new-file" method="post" action="'.$_SERVER['REQUEST_URI'].'" enctype="multipart/form-data">'
            .'<!--<a href="http://www.google.cz/" target="ajaxFileUploadIFrame">Google</a>'
            .'<iframe id="ajaxFileUploadIFrame" name="ajaxFileUploadIFrame" src=""></iframe>-->'
            .'<input type="hidden" name="dir-id" value="'.$dirId.'" />'
            .'<div class="up-file-prop">'
                .'<div class="up-file-name">'
                .'<label for="file-name">'.$rb->get('file.name').':</label> '
                .'<input type="text" id="file-name" name="file-name" value="'.((strlen($fileName) != 0) ? $fileName : 'file'.rand(1000, 9999).rand(1000, 9999)).'" /> '
                .'</div>'
                .'<div class="up-file-title">'
                .'<label for="file-title">'.$rb->get('file.title').':</label> '
                .'<input type="text" id="file-title" name="file-title" value="'.$fileTitle.'" /> '
                .'</div>'
                .'<div class="up-file-rs">'
                .'<label for="file-rs">'.$rb->get('file.select').':</label> '
                .'<input type="file" id="file-rs" name="file-rs" /> '
                .'</div>'
            .'</div>'
            .(($useRights != 'false') ? ''
            .'<div class="file-rights">'
                .'<div class="file-right-r">'
                .'<label for="file-right-edit-groups-r">'.$rb->get('perm.read').':</label>'
                .$groupSelectR
                .'</div>'
                .'<div class="file-right-w">'
                .'<label for="file-right-edit-groups-w">'.$rb->get('perm.write').':</label>'
                .$groupSelectW
                .'</div>'
                .'<div class="file-right-d">'
                .'<label for="file-right-edit-groups-d">'.$rb->get('perm.delete').':</label>'
                .$groupSelectD
                .'</div>'
                .'<div class="clear"></div>'
            .'</div>'
            : '')
            .'<div class="clear"></div>'
            .'<div class="up-file-submit">'
                .(($_POST['edit-file'] == $rb->get('file.edit')) ? ''
                .'<input type="hidden" name="file-id" value="'.$fileId.'" />'
                .'<input type="submit" name="edit-upload-file" value="'.$rb->get('file.newver').'" title="'.$rb->get('file.newver').'" />'
                : ''
                .'<input type="submit" name="new-file" value="'.$rb->get('file.upload').'" title="'.$rb->get('file.uploadhint').'" />'
                )
            .'</div>'
            .'<div class="clear"></div>'
            .'</form>';
            
            if($useFrames != 'false') {
            return parent::getFrame((($_POST['edit-file'] == $rb->get('file.edit')) ? $rb->get('file.edithint') : $rb->get('file.new')), $return, "", true);
            } else {
            return $return;
            }
        }
        
        private function processFileUpload() {
            global $dbObject;
            global $loginObject;
            $rb = self::rb();
            $fileName = $_POST['file-name'];
            $dirId = $_POST['dir-id'];
            $fileTitle = $_POST['file-title'];
            $fileId = $_POST['file-id'];
            $read = $_POST['file-right-edit-groups-r'];
            $write = $_POST['file-right-edit-groups-w'];
            $delete = $_POST['file-right-edit-groups-d'];
            $extType = self::getWebFileType($_FILES['file-rs']['name']);
            $oldFile = null;
            
            if(is_uploaded_file($_FILES['file-rs']['tmp_name'])) {
            $original = $_FILES['file-rs']['tmp_name'];
            if(strlen($fileName) == 0) {
                $fileName = "file".rand(1000, 9999).rand(1000, 9999);
            }
            
            if(array_key_exists('file-id', $_POST)) {
                $permission = $dbObject->fetchAll('SELECT `value` FROM `file_right` LEFT JOIN `group` ON `file_right`.`gid` = `group`.`gid` WHERE `file_right`.`fid` = '.$fileId.' AND `file_right`.`type` = '.WEB_R_WRITE.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) ORDER BY `value` DESC;');
            } else {
                $permission = $dbObject->fetchAll('SELECT `value` FROM `directory_right` LEFT JOIN `group` ON `directory_right`.`gid` = `group`.`gid` WHERE `directory_right`.`did` = '.$dirId.' AND `directory_right`.`type` = '.WEB_R_WRITE.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) ORDER BY `value` DESC;');
            }
            if(count($permission) > 0) {
                if(strlen($fileName) > 0) {
                if(array_key_exists('file-id', $_POST)) {
                    $files = $dbObject->fetchAll("SELECT `id` FROM `file` WHERE `dir_id` = ".$dirId." AND `name` = \"".$fileName."\" AND `type` = ".$extType." AND `id` != ".$fileId.";");
                } else {
                    $files = $dbObject->fetchAll("SELECT `id` FROM `file` WHERE `dir_id` = ".$dirId." AND `name` = \"".$fileName."\" AND `type` = ".$extType.";");
                }
                if(count($files) == 0) {
                    if($extType > 0) {
                    $path = self::getPhysicalPathTo($dirId);
                    $moved = move_uploaded_file($original, $path.$fileName.".".FileAdmin::$FileExtensions[$extType]);
                    
                    if($moved) {
                        if(array_key_exists('file-id', $_POST)) {
                        $files = $dbObject->fetchAll("SELECT `id`, `name`, `type`, `dir_id` FROM `file` WHERE `dir_id` = ".$dirId." AND `id` = ".$fileId.";");
                        $oldFile = $files[0];
                        $path = self::getPhysicalPathTo($oldFile['dir_id']);
                        $filePath = $path.$oldFile['name'].".".FileAdmin::$FileExtensions[$oldFile['type']];
                        //echo $filePath;
                        if($fileName != $oldFile['name'] || $extType != $oldFile['type']) {
                            unlink($filePath);
                        }
                        $dbObject->execute('UPDATE `file` SET `name` = "'.$fileName.'", `title` = "'.$fileTitle.'", `timestamp` = '.time().' WHERE `id` = '.$fileId.';');
                        } else {
                            $dbObject->execute("INSERT INTO `file`(`dir_id`, `name`, `title`, `type`, `timestamp`) VALUES (".$dirId.", \"".$fileName."\", \"".$fileTitle."\", ".$extType.", ".time().");");
                            $fileId = $dbObject->fetchAll('SELECT MAX(`id`) AS `id` FROM `file`;');
                            $fileId = $fileId[0]['id'];
                        }
                        
                        
                if(count($read) != 0) {
                    $dbR = $dbObject->fetchAll("SELECT `gid` FROM `file_right` WHERE `file_right`.`fid` = ".$fileId." AND `type` = ".WEB_R_READ.";", true, true);
                    foreach($dbR as $right) {
                    if(!in_array($right, $read)) {
                        $dbObject->execute("DELETE FROM `file_right` WHERE `fid` = ".$fileId." AND `type` = ".WEB_R_READ.";");
                    }
                    }
                    foreach($read as $right) {
                    $row = $dbObject->fetchAll("SELECT `gid` FROM `file_right` WHERE `fid` = ".$fileId." AND `type` = ".WEB_R_READ." AND `gid` = ".$right.";");
                    if(count($row) == 0) {
                        $dbObject->execute("INSERT INTO `file_right`(`fid`, `gid`, `type`) VALUES (".$fileId.", ".$right.", ".WEB_R_READ.");");
                    }
                    }
                } else {
                    $rights = $dbObject->fetchAll('SELECT `gid` FROM `directory_right` WHERE `did` = '.$dirId.' AND `type` = '.WEB_R_READ.';');
                    foreach($rights as $right) {
                    $dbObject->execute('INSERT INTO `file_right`(`fid`, `gid`, `type`) VALUES ('.$fileId.', '.$right['gid'].', '.WEB_R_READ.');');
                    }
                }
                
                if(count($write) != 0) {
                    $dbR = $dbObject->fetchAll("SELECT `gid` FROM `file_right` WHERE `file_right`.`fid` = ".$fileId." AND `type` = ".WEB_R_WRITE.";");
                    foreach($dbR as $right) {
                    if(!in_array($right, $write)) {
                        $dbObject->execute("DELETE FROM `file_right` WHERE `fid` = ".$fileId." AND `type` = ".WEB_R_WRITE.";");
                    }
                    }
                    foreach($write as $right) {
                    $row = $dbObject->fetchAll("SELECT `gid` FROM `file_right` WHERE `fid` = ".$fileId." AND `type` = ".WEB_R_WRITE." AND `gid` = ".$right.";");
                    if(count($row) == 0) {
                        $dbObject->execute("INSERT INTO `file_right`(`fid`, `gid`, `type`) VALUES (".$fileId.", ".$right.", ".WEB_R_WRITE.");");
                    }
                    }
                } else {
                    $rights = $dbObject->fetchAll('SELECT `gid` FROM `directory_right` WHERE `did` = '.$dirId.' AND `type` = '.WEB_R_WRITE.';');
                    foreach($rights as $right) {
                    $dbObject->execute('INSERT INTO `file_right`(`fid`, `gid`, `type`) VALUES ('.$fileId.', '.$right['gid'].', '.WEB_R_WRITE.');');
                    }
                }
                
                if(count($delete) != 0) {
                    $dbR = $dbObject->fetchAll('SELECT `gid` FROM `file_right` WHERE `file_right`.`fid` = '.$fileId.' AND `type` = '.WEB_R_DELETE.';');
                    foreach($dbR as $right) {
                    if(!in_array($right, $delete)) {
                        $dbObject->execute("DELETE FROM `file_right` WHERE `fid` = ".$fileId." AND `type` = ".WEB_R_DELETE.";");
                    }
                    } 	
                    foreach($delete as $right) {
                    $row = $dbObject->fetchAll('SELECT `gid` FROM `file_right` WHERE `fid` = '.$fileId.' AND `type` = '.WEB_R_DELETE.' AND `gid` = '.$right.';');
                    if(count($row) == 0) {
                        $dbObject->execute("INSERT INTO `file_right`(`fid`, `gid`, `type`) VALUES (".$fileId.", ".$right.", ".WEB_R_DELETE.");");
                    }
                    }
                } else {
                    $rights = $dbObject->fetchAll('SELECT `gid` FROM `directory_right` WHERE `did` = '.$dirId.' AND `type` = '.WEB_R_DELETE.';');
                    foreach($rights as $right) {
                    $dbObject->execute('INSERT INTO `file_right`(`fid`, `gid`, `type`) VALUES ('.$fileId.', '.$right['gid'].', '.WEB_R_DELETE.');');
                    }
                }
                }
                    } else {
                    $return .= '<h4 class="error">'.$rb->get('file.unsupportedtype').'</h4>';
                    }
                } else {
                    $return .= '<h4 class="error">'.$rb->get('file.notuniquename').'</h4>';
                }		
                } else {
                $return .= '<h4 class="error">'.$rb->get('file.namelength').'</h4>';
                }
            } else {
                $return .= '<h4 class="error">'.$rb->get('permissiondenied').'</h4>';
            }
        } else if(array_key_exists('file-id', $_POST)) {
            $permission = $dbObject->fetchAll('SELECT `value` FROM `file_right` LEFT JOIN `group` ON `file_right`.`gid` = `group`.`gid` WHERE `file_right`.`fid` = '.$fileId.' AND `file_right`.`type` = '.WEB_R_WRITE.' AND (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) ORDER BY `value` DESC;');
            if(count($permission) > 0) {
            $files = $dbObject->fetchAll("SELECT `id`, `name`, `type`, `dir_id` FROM `file` WHERE `dir_id` = ".$dirId." AND `id` = ".$fileId.";");
            $oldFile = $files[0];
            $path = self::getPhysicalPathTo($oldFile['dir_id']);
            $filePath = $path.$oldFile['name'].".".FileAdmin::$FileExtensions[$oldFile['type']];
            if($fileName != $oldFile['name']) {
                $i = rename($filePath, str_replace($oldFile['name'], $fileName, $filePath));
            }
            
            $dbObject->execute('UPDATE `file` SET `name` = "'.$fileName.'", `title` = "'.$fileTitle.'", `timestamp` = '.time().' WHERE `id` = '.$fileId.';');
            } else {
            $return .= '<h4 class="error">'.$rb->get('permissiondenied').'</h4>';
            }
        } else {
            $return .= '<h4 class="error">'.$rb->get('file.nofile').'</h4>';
        }
            
        return $return;
        }
        
        /**
        *  
        *  Setups right dirId.
        *  
        *  @param    dirId   dir id from c tag
        *  @return   dir id               
        *  
        */
        private function setDirId($dirId) {
            if(array_key_exists("dir-id", $_POST)) {
            $_SESSION['dir-id'] = $_POST['dir-id'];
            return $_SESSION['dir-id'];
            } else if(array_key_exists("dir-id", $_SESSION)) {
            return $_SESSION['dir-id'];
            } else {
            $_SESSION['dir-id'] = $dirId | 0;
            return $_SESSION['dir-id'];
            }
        }
        
        /**
        *
        *  Generates physical path to dir in fs (/hosting/www/user/filesystem/1/2).
        *  
        *  @param    dirId   dir id
        *  @return   physical path to dir in fs
        *
        */                   
        public function getPhysicalPathTo($dirId) {
            return USER_FILESYSTEM_PATH . self::getDirectoryPathIndernal($dirId);
        }

        /**
        *
        *  Generates URL to dir in fs (/files/1/2).
        *  
        *  @param    dirId   dir id
        *  @return   physical path to dir in fs
        *
        */    
        public function getPhysicalUrlTo($dirId) {
            return USER_FILESYSTEM_URL . self::getDirectoryPathIndernal($dirId);
        }

        private function getDirectoryPathIndernal($dirId) {
            $path = "";
            if ($dirId >= 0) {
                while ($dirId != 0) {
                    parent::db()->getDataAccess()->disableCache();
                    $dirInfo = parent::dao('Directory')->select(parent::select()->where('id', '=', $dirId), false, array(FileAdmin::$FileSystemItemPath, 'parent_id'));
                    parent::db()->getDataAccess()->enableCache();
                    if (count($dirInfo) == 1) {
                        $dirId = $dirInfo[0]['parent_id'];
                        $path = $dirInfo[0][FileAdmin::$FileSystemItemPath].'/'.$path;
                    } else {
                        $message = "Directory doesn't exists!";
						echo parent::getError($message);
                        trigger_error($message, E_USER_ERROR);
                    }
                }
            } else {
                $message = "Directory doesn't exists!";
				echo parent::getError($message);
                trigger_error($message, E_USER_ERROR);
            }

            return $path;
        }
        
        /**
        *
        *  Return binary representation of file.
        *  C tag.
        *  
        *  @param    fileId    file id
        *  @return   binary representation of file                    
        *
        */                        
        public function getFile($fileId = false) {
            global $dbObject;
            
            if($fileId == false) {
            if($this->CurrentId != 0) {
                $fileId = $this->CurrentId;
                $this->CurrentId = 0;
            } elseif($_SESSION['file']['current_id'] != '') {
                $fileId = $_SESSION['file']['current_id'];
            } else {
                // vrat image "file not found".
            }
            }
            
            $file = $dbObject->fetchAll("SELECT `id`, `dir_id`, `name`, `type`, `timestamp` FROM `file` WHERE `id` = ".$fileId.";");
            
            if(count($file) == 1) {
            $filePath = self::getPhysicalPathTo($file[0]['dir_id']).$file[0]['name'].".".FileAdmin::$FileExtensions[$file[0]['type']];
            $fileExt = ($file[0]['type'] == WEB_TYPE_JPG || $file[0]['type'] == WEB_TYPE_GIF || $file[0]['type'] == WEB_TYPE_PNG) ? "image/".FileAdmin::$FileExtensions[$file[0]['type']] : "document/".$file[0]['type'];
            
            if(array_key_exists("width", $_GET) && array_key_exists("height", $_GET)) {
                $width = $_GET['width'];
                $height = $_GET['height'];
                $thumbPath = 'cache/images/'.$file[0]['dir_id'].'-'.$file[0]['id'].'-'.$file[0]['name'].'_'.$width.'x'.$height.'.'.FileAdmin::$FileExtensions[$file[0]['type']];
                
                if(file_exists($thumbPath) && is_readable($thumbPath)) {
                $filePath = $thumbPath;
                } else {
                self::createThumb($filePath, $thumbPath, $width, $height, $file[0]['type']);
                $filePath = $thumbPath;
                }
            } else if(array_key_exists("width", $_GET)) {
                $width = $_GET['width'];
                list($orWidth, $orHeight, $orType) = getimagesize($filePath);
                $ratio = $width / $orWidth;
                $height = round($ratio * $orHeight);
                
                $thumbPath = 'cache/images/'.$file[0]['dir_id'].'-'.$file[0]['id'].'-'.$file[0]['name'].'_'.$width.'x'.$height.'.'.FileAdmin::$FileExtensions[$file[0]['type']];
                
                if(file_exists($thumbPath) && is_readable($thumbPath)) {
                $filePath = $thumbPath;
                } else {
                self::createThumb($filePath, $thumbPath, $width, $height, $file[0]['type']);
                $filePath = $thumbPath;
                }
            } else if(array_key_exists("height", $_GET)) {
                $height = $_GET['height'];
                list($orWidth, $orHeight, $orType) = getimagesize($filePath);
                $ratio = $height / $orHeight;
                $width = round($ratio * $orWidth);
                
                $thumbPath = 'cache/images/'.$file[0]['dir_id'].'-'.$file[0]['id'].'-'.$file[0]['name'].'_'.$width.'x'.$height.'.'.FileAdmin::$FileExtensions[$file[0]['type']];
                
                if(file_exists($thumbPath) && is_readable($thumbPath)) {
                $filePath = $thumbPath;
                } else {
                self::createThumb($filePath, $thumbPath, $width, $height, $file[0]['type']);
                $filePath = $thumbPath;
                }
            }
            
            header("Expires: ".gmdate("D, d M Y H:i:s", mktime(0, 0, 0, 12, 21, 2011))." GMT");
            header("Cache-Control: max-age=31536000");
            header("Pragma: cache");
            header("Last-Modified: ".gmdate("D, d M Y H:i:s", $updTime)." GMT");
            
            $updTime = filemtime($filePath);
            //echo $updTime.' - '.getenv("HTTP_IF_MODIFIED_SINCE").' ; ';
            if($_SERVER["HTTP_IF_MODIFIED_SINCE"] && $updTime <= strtotime($_SERVER["HTTP_IF_MODIFIED_SINCE"])) {
                header("HTTP/1.1 304 Not Modified");
                parent::close();
            }

            
            if(file_exists($filePath) && is_readable($filePath)) {
                $fileSize = filesize($filePath);
                
                header('Content-Type: '.$fileExt);
                header('Content-Length: '.$fileSize);
                header('Content-Disposition: attachment; filename='.$file[0]['name'].".".FileAdmin::$FileExtensions[$file[0]['type']]);
                header('Content-Transfer-Encoding: binary');
                $file = @ fopen($filePath, 'rb');
                if ($file) {
                fpassthru($file);
                parent::close();
                } else {
                // vrat image "file not found".
                }
            } else {
                // vrat image "file not found".
            }
            } else {
            // vrat image "file not found".
            }
        }
        
        /**
        *
        *  Creates thumb from originalPath in thumbPath. Thumb has width and height
        *  as passed values.
        *  
        *  @param    originalPath    path to originalImage
        *  @param    thumbPath       path where to locate new image
        *  @param    width           new image width
        *  @param    height          new image height
        *  @param    type            web image type     
        *  @return   true if success, false otherwise                                   
        *
        */                             
        public function createThumb($originalPath, $thumbPath, $width, $height, $type) {
            list($orWidth, $orHeight, $orType) = getimagesize($originalPath);

            switch ($type) {
                case WEB_TYPE_GIF: 
                    $source = @ imagecreatefromgif($originalPath);
                    if (!$source) {
                        $message = 'Cannot process GIF files. Please use JPEG or PNG.';
                        echo "<h4 class=\"error\">".$message."<h4>";
                        trigger_error($message, E_USER_ERROR);
                    }
                    break;
                    
                case WEB_TYPE_JPG: 
                    $source = imagecreatefromjpeg($originalPath); 
                    break;
                    
                case WEB_TYPE_PNG: 
                    $source = imagecreatefrompng($originalPath); 
                    break;

                default:
                    return false;
            }
            
            $thumb = imagecreatetruecolor($width, $height);

            $isTransparent = $type == WEB_TYPE_GIF || $type == WEB_TYPE_PNG;
            if ($isTransparent) {
                imagealphablending($thumb, false);
                imagesavealpha($thumb, true);

                $transparentColor = imagecolorallocatealpha($thumb, 255, 255, 255, 127);
                imagefilledrectangle($thumb, 0, 0, $width, $height, $transparentColor);
            }
                
            imagecopyresampled($thumb, $source, 0, 0, 0, 0, $width, $height, $orWidth, $orHeight);
                
            switch($type) {
                case WEB_TYPE_GIF:
                    if (function_exists('imagegif')) {
                        $success = imagegif($thumb, $thumbPath);
                    } else {
                        $success = imagejpeg($thumb, $thumbPath, 50);
                    }
                    break;
                        
                case WEB_TYPE_JPG:
                    $success = imagejpeg($thumb, $thumbPath, 100);
                    break;

                case WEB_TYPE_PNG:
                    $success = imagepng($thumb, $thumbPath);
                    break;

                default:
                    return false;
            }
                
            imagedestroy($source);
            imagedestroy($thumb);
            
            return true;
        }
        
        /**
        *
        *  Returns right file type to file.
        *  
        *  @param    fileName    file name
        *  @return   int file extension               
        *
        */                   
        public function getWebFileType($fileName) {
            foreach(FileAdmin::$FileExtensions as $key => $ext) {
            $ext = ".".$ext;
            if(strtolower(substr($fileName, strlen($fileName) - strlen($ext))) == $ext) {
                return $key;
            }
            }
            return -1;
        }
        
        /**
        *
        *  Dynamicly rewrite address.
        *  C tag.
        *  
        *  return    if file exists, it returns CurrentDynamicPath     
        *
        */                        
        public function composeUrl() {
            global $webObject;
            global $phpObject;
            global $dbObject;
            $cdp = $webObject->getCurrentDynamicPath();
            
            $id = StringUtils::explode($cdp, "-", 1);
            
            $file = $dbObject->fetchAll("SELECT `name` FROM `file` WHERE `id` = ".$id[0].";");
            if(count($file) == 1 && ($cdp == $id[0] || $cdp == $id[0]."-".$file[0]['name'])) {
            $this->CurrentId = $id[0];
            $_SESSION['file']['current_id'] = $id[0];
            return $cdp;
            } else {
            return 'false.false';
            }
        }
        
        /**
        *
        *  Flush all images in passed dirId directory.
        *
        *  @param    pageId    next page id for display full image
        *  @param    dirId     directory id to show
        *
        */                        
        public function galleryFromDirectory($method = false, $pageId = false, $langId = false, $dirId = false, $defaultDirId = false, $showSubDirs = false, 
            $showNames = false, $showTitles = false, $limit = false, $detailWidth = false, $detailHeight = false, $lightbox = false, $lightWidth = false, 
            $lightHeight = false, $lightTitle = false, $lightId = false, $useDirectLink = false, $recursively = false, $dirDateFormat = false, $orderFilesBy = false, 
            $orderDirsBy = false, $desc = false, $filesBeforeFolders = false, $dirPageSize = false, $filePageSize = false) {

            global $webObject;
            global $dbObject;
            $return = "";
            
            if($dirId == '') {
                if(array_key_exists("dir-id", $_REQUEST)) {
                    $dirId = $_REQUEST['dir-id'];
                } else {
                    if($defaultDirId != false) {
                        $dirId = $defaultDirId;
                    } else {
                        $message = "DirId isn't set! [file:gallery]";
                        echo "<h4 class=\"error\">".$message."</h4>";
                        trigger_error($message, E_USER_ERROR);
                    }
                }
            }
                
            if($method === false || ($method != "static" && $method != "dynamic")) {
                $method = "static";
            }
                
            if($langId == false) {
                $langId = $webObject->LanguageId;
            }
                
            if($lightbox == "true") {
                // Include scripts and styles
                $return .= ''
                .'<link type="text/css" rel="stylesheet" href="~/css/jquery-lightbox.css" />'
                .'<script type="text/javascript" src="~/js/jquery/jquery.js"></script>'
                .'<script type="text/javascript" src="~/js/jquery/jquery-lightbox-pack.js"></script>'
                .'<script type="text/javascript">'."\n"
                .'$(function() {'
                    .'$("#light-gallery-'.$lightId.' a.gallery-link").lightBox({fixedNavigation:true});'
                .'});'
                .'</script>';
            }
                
            $return .= ''
            .'<div '.(($lightbox == "true") ? 'id="light-gallery-'.$lightId.'"' : "").' class="gallery-cover">'
            .'';
                
            if($filesBeforeFolders != "true") {
                if($showSubDirs == "true" || $recursively == "true") {
                $return .= self::galleryShowDirectories($method, $pageId, $langId, $dirId, false, $showSubDirs, $showNames, $showTitles, $limit, $detailWidth, $detailHeight, $lightbox == "true" ? "added" : $lightbox, $lightWidth, $lightHeight, $lightTitle, $lightId, $useDirectLink, $recursively, $dirDateFormat, $orderFilesBy, $orderDirsBy, $desc, $filesBeforeFolders, $dirPageSize, $filePageSize);
                $return .= '<div class="clear"></div>';
                }
            }
                
            $size = '';
            if($detailWidth > 0 && $detailHeight > 0) {
                $size = 'width='.$detailWidth.'&height='.$detailHeight;
            } elseif($detailWidth > 0) {
                $size = 'width='.$detailWidth;
            } elseif($detailHeight > 0) {
                $size = 'height='.$detailHeight;
            } else {
                $size = 'height=100';
            }
                
            $lsize = '';
            if($lightWidth > 0 && $lightHeight > 0) {
                $lsize = 'width='.$lightWidth.'&height='.$lightHeight;
            } elseif($lightWidth > 0) {
                $lsize = 'width='.$lightWidth;
            } elseif($lightHeight > 0) {
                $lsize = 'height='.$lightHeight;
            } else {
                $lsize = '';
            }

            $order = "name";
            switch(strtolower($orderFilesBy)) {
                case "id": $order = "id"; break;
                case "type": $order = "type"; break;
                case "timestamp": $order = "timestamp"; break;
            }
            $images = $dbObject->fetchAll("SELECT `id`, `name`, `title`, `type` FROM `file` WHERE `dir_id` = ".$dirId." AND (`type` = ".WEB_TYPE_JPG." OR `type` = ".WEB_TYPE_GIF." OR `type` = ".WEB_TYPE_PNG.") ORDER BY `".$order."` ".(($desc == true) ? " DESC" : "")."".($limit != "" ? " limit ".$limit : "").";");
                
            foreach($images as $image) {
            if($lightbox == "true" || $lightbox == "added") {
                $link = ''
                .'<a class="gallery-link" title="'.$image['title'].'" href="'.(($useDirectLink != "true") ? '~/file.php?rid='.$image['id'].'&'.$lsize : self::getPhysicalUrlTo($dirId).$image['name'].".".FileAdmin::$FileExtensions[$image['type']]).'"'.(($lightbox == "true") ? ' rel="lightbox'.(($lightId != false) ? '['.$lightId.']' : '').'"' : '').(($lightTitle == "true") ? ' title="'.$image['title'].'"' : '').'>'
                    .'<img src="'.(($useDirectLink != "true") ? '~/file.php?rid='.$image['id'].'&'.$size : self::getPhysicalUrlTo($dirId).$image['name'].".".FileAdmin::$FileExtensions[$image['type']]).'" alt="'.$image['title'].'" />'
                .'</a>';
            } else {
                $link = ''
                .(($pageId != false) ? '<a class="gallery-link" title="'.$image['title'].'" href="'.$webObject->composeUrl($pageId, $langId).(($method == "dynamic") ? '/'.$image['id'].'-'.$image['name'] : '?file-id='.$image['id']).'">' : '')
                    .'<img src="'.(($useDirectLink != "true") ? '~/file.php?rid='.$image['id'].'&'.$size : self::getPhysicalUrlTo($dirId).$image['name'].".".FileAdmin::$FileExtensions[$image['type']]).'" alt="'.$image['title'].'" />'
                .(($pageId != false) ? '</a>' : '');
            }
            
            $return .= ''
                .'<div class="gallery-item gallery-image">'
                .'<div class="gallery-thumb">'
                    .$link
                .'</div>'
                .(($showNames == "true") ? '<div class="gallery-name">'.$image['name'].'</div>' : '')
                .(($showTitles == "true") ? '<div class="gallery-title">'.$image['title'].'</div>' : '')
                .'</div>';
            }
            
            if($filesBeforeFolders == "true") {
                $return .= '<div class="clear"></div>';
                if($showSubDirs == "true" || $recursively == "true") {
                    $return .= self::galleryShowDirectories($method, $pageId, $langId, $dirId, false, $showSubDirs, $showNames, $showTitles, $limit, $detailWidth, $detailHeight, $lightbox == "true" ? "added" : $lightbox, $lightWidth, $lightHeight, $lightTitle, $lightId, $useDirectLink, $recursively, $dirDateFormat, $orderFilesBy, $orderDirsBy, $desc, $filesBeforeFolders, $dirPageSize, $filePageSize);
                }
            }
                
            $return .= ''
            .'<div class="clear"></div>'
            .'</div>';
                
                return $return;
        }

        private function galleryShowDirectories($method = false, $pageId = false, $langId = false, $dirId = false, $defaultDirId = false, $showSubDirs = false, 
            $showNames = false, $showTitles = false, $limit = false, $detailWidth = false, $detailHeight = false, $lightbox = false, $lightWidth = false, 
            $lightHeight = false, $lightTitle = false, $lightId = false, $useDirectLink = false, $recursively = false, $dirDateFormat = false, 
            $orderFilesBy = false, $orderDirsBy = false, $desc = false, $filesBeforeFolders, $dirPageSize = false, $filePageSize = false) {
            global $webObject;
            global $dbObject;
            $return = "";
            
            $order = "name";
            switch(strtolower($orderDirsBy)) {
                case "id": $order = "id"; break;
                case "url": $order = "url"; break;
                case "timestamp": $order = "timestamp"; break;
            }
            
            $sql = "SELECT `id`, `name`, `url`, `timestamp` FROM `directory` WHERE `parent_id` = ".$dirId." ORDER BY `".$order."`".(($desc == true) ? " DESC" : "");
            if($dirPageSize != '') {
                $start = self::getDirOffset();
            
                $sql .= ' limit ' . $start . ',' . $dirPageSize;
            }
            $sql .= ";";
                
            $dirs = $dbObject->fetchAll($sql);
            
            //print_r($_REQUEST);
            
            $tmpDirId = $_REQUEST['dir-id'];
            $tmpDirUrl = $_REQUEST['dir-url'];
            foreach($dirs as $dir) {
                if($pageId != false) {
                    $_REQUEST['dir-id'] = $dir['id'];
                    $_REQUEST['dir-url'] = $dir['url'];
                    $url = $webObject->composeUrl($pageId);
                    if(self::getDirOffset() != 0) {
                        $url .= '?dir-offset=' . self::getDirOffset();
                    }
                }
            
                $return .= ''
                .'<div class="gallery-item gallery-dir">'
                    .'<div class="gallery-thumb">'
                    .'</div>'
                    .'<div class="gallery-name">'
                        .((strlen($url) != 0) ? '<a href="'.$url.'">'.$dir['name'].'</a> ' : $dir['name'])
                        .(($dirDateFormat != "") ? date($dirDateFormat, $dir['timestamp']) : "")
                    .'</div>'
                        .($recursively == "true" ? self::galleryFromDirectory($method, $pageId, $langId, $dir['id'], false, $showSubDirs, $showNames, $showTitles, $limit, $detailWidth, $detailHeight, $lightbox == "true" ? "added" : $lightbox, $lightWidth, $lightHeight, $lightTitle, $lightId, $useDirectLink, $recursively, $dirDateFormat, $orderFilesBy, $orderDirsBy, $desc, $filesBeforeFolders, $dirPageSize, $filePageSize) : "")
                    .'</div>';
            }

            $_REQUEST['dir-id'] = $tmpDirId;
            $_REQUEST['dir-url'] = $tmpDirUrl;
            
            if($dirPageSize != '') {
                $start = self::getDirOffset();
            
                $sql = "SELECT count(`id`) as `count` FROM `directory` WHERE `parent_id` = ".$dirId;
                $result = $dbObject->fetchSingle($sql);
            
                $pages = '';
                for($i = 0; $i < ($result['count'] / $dirPageSize); $i ++) {
                $offset = $i * $dirPageSize;
                $pages .= '<a class="'.($start == $offset ? 'current' : '').'" href="?dir-offset='. $offset .'">'. ($i + 1) .'</a> | ';
                }
                $pages = substr($pages, 0, strlen($pages) - 3);
            
                $return .= ''
                .'<div class="clear"></div>'
                .'<div class="gallery-paging">'
                   .'Strana: ' . $pages
                .'</div>';
            }
            
            return $return;
        }

        private function getDirOffset() {
            $start = 0;
            if(array_key_exists("dir-offset", $_REQUEST)) {
                $start = $_REQUEST["dir-offset"];
            }
            
            return $start;
        }
        
        public function galleryDetail($fileId = false, $showName = false, $showTitle = false) {
            global $dbObject;
            $return = "";
        
            if($fileId == false) {
                if(array_key_exists("file-id", $_REQUEST)) {
                    $fileId = $_REQUEST['file-id'];
                } elseif($this->CurrentId != 0) {
                    $fileId = $this->CurrentId;
                } else {
                    $message = "FileId isn't set! [file:gallery]";
                    //echo "<h4 class=\"error\">".$message."</h4>";
                    trigger_error($message, E_USER_ERROR);
                }
            }
            
            $image = $dbObject->fetchAll("SELECT `id`, `name`, `title` FROM `file` WHERE `id` = ".$fileId.";");
            
            if(count($image) == 1) {
                $return .= ''
                .'<div class="gallery-detail">'
                    .'<div class="gallery-detail-image">'
                    .'<img src="~/file.php?rid='.$image[0]['id'].'" alt="'.(strlen($image[0]['title']) != 0 ? $image[0]['title'] : $image[0]['name']).'" />'
                    .'</div>'
                    .(($showName == "true") ? '<div class="gallery-name">'.$image[0]['name'].'</div>' : '')
                    .(($showTitle == "true") ? '<div class="gallery-title">'.$image[0]['title'].'</div>' : '')
                .'</div>';
            }
            
            return $return;
        }

        public function getFilesFromDirectory($template, $id, $type = null, $pageIndex = false, $limit = false, $noDataMessage = false) {
            if ($type != null) {
                $type = explode(",", $type);
                if (count($type) == 0) {
                    $type = null;
                }
            }

            $return = '';
            
            $files = parent::dao('File')->getFromDirectory($id, "name", $type, $pageIndex, $limit);
            if (count($files) == 0) {
                $return .= $noDataMessage;
            } else {
                foreach($files as $file) {
                    if ($this->canUserFile($file['id'], WEB_R_READ)) {
                        $this->setFileId($file['id']);
                        $this->setFileUrl($file['url']);
                        parent::request()->set('name', $file['name'], 'f:directoryFiles');
                        parent::request()->set('dir_id', $file['dir_id'], 'f:directoryFiles');
                        parent::request()->set('title', $file['title'], 'f:directoryFiles');
                        parent::request()->set('type', $file['type'], 'f:directoryFiles');
                    
                        $return .= $template();
                    }
                }
            }

            return $return;
        }

        //C-Tag
        public function fileName() {
            return parent::request()->get('name', 'f:directoryFiles');
        }

        //C-Tag
        public function fileTitle() {
            return parent::request()->get('title', 'f:directoryFiles');
        }

        //C-Tag
        public function fileType() {
            return parent::request()->get('type', 'f:directoryFiles');
        }

        //C-Tag
        public function fileUrl() {
            return "~/file.php?rid=" . $this->getFileId();
        }
        
        // ----------- PROPERTIES --------------------------- //
        
        public function setFileId($fileId) {
            if(is_numeric($fileId)) {
                $_SESSION['file']['current_id'] = $fileId;
                return $fileId;
            } else {
                return 'wrong-file-id';
            }
        }
        
        public function getFileId() {
            return $_SESSION['file']['current_id'];
        }

        public function setFileUrl($value) {
            parent::request()->set('file-url', $value);
            return $value;
        }

        public function getFileUrl() {
            return parent::request()->get('file-url');
        }
        
        public function setDirectoryId($dirId) {
            if(is_numeric($dirId)) {
                $_REQUEST['dir-id'] = $dirId;
                return $dirId;
            } else {
                return 'wrong-dir-id';
            }
        }
        
        public function getDirectoryId() {
            return $_REQUEST['dir-id'];
        }
        
        public function setDirectoryIdFromUrl($dirUrl) {
            global $dbObject;
            $dirs = $dbObject->fetchAll('SELECT `id` FROM `directory` WHERE `url` = "'.$dirUrl.'";');
            if(count($dirs) > 0) {
                $_REQUEST['dir-id'] = $dirs[0]['id'];
                $_REQUEST['dir-url'] = $dirUrl;
                return $dirUrl;
            } else {
                return 'wrong-dir-url';
            }
        }
        
        public function getDirectoryUrl() {
            return $_REQUEST['dir-url'];
        }

        public function setDirectoryName($name) {
            return $name;
        }
        
        public function getDirectoryName() {
            global $dbObject;
            $sql = 'select `name` from `directory` where `id` = ' . self::getDirectoryId();
            $result = $dbObject->fetchSingle($sql);
            return $result['name'];
        }
    }

?>
