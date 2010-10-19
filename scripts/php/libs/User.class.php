<?php

  /**
   *
   *  Require base tag lib class.   
   *
   */
  require_once("BaseTagLib.class.php");
  
	/**
	 *
	 *  user management class.
	 *      
	 *  @author     Marek SMM
	 *  @timestamp  2010-10-09
	 *
	 */              
	class User extends BaseTagLib {
    
  
		private $BundleName = 'user';
  	
		private $BundleLang = 'cs';
		
		public function __construct() {
			global $webObject;
			self::setTagLibXml("xml/User.xml");
			
			if($webObject->LanguageName != '') {
				$rb = new ResourceBundle();
				if($rb->testBundleExists($this->BundleName, $webObject->LanguageName)) {
					$this->BundleLang = $webObject->LanguageName;
				}
			}
		}
    
		public function showUserManagement($attUserId = false) {
			global $dbObject;
			global $loginObject;
			$return = '';
			$rb = new ResourceBundle();
			$rb->loadBundle($this->BundleName, $this->BundleLang);
      
			if($_POST['user-edit-save'] == $rb->get('management.save')) {
				$uid = $_POST['user-edit-uid'];
				$login = $_POST['user-edit-login'];
				$name = $_POST['user-edit-name'];
				$surname = $_POST['user-edit-surname'];
				$password = $_POST['user-edit-password'];
				$passwordAgain = $_POST['user-edit-password-again'];
				$enable = ($_POST['user-edit-enable'] == 'on' ? 1 : 0);
				$groups = $_POST['user-edit-groups'];
        
				$errors = array();
				if(strlen($login) < 5) {
					$errors[] = $rb->get('reg.error.usernameshort');
				}
				if(strlen($password) < 6 && $password != '1a1') {
					$errors[] = $rb->get('reg.error.passwordshort');
				}
				if($password != $passwordAgain) {
					$errors[] = $rb->get('reg.error.passwordnotmatch');
				}
				if(count($groups) == 0) {
					$errors[] = $rb->get('management.error.atleastonegroup');
				}
        
				if(count($errors) == 0) {
					if(is_numeric($uid)) {
						if($password == "1a1") {
							$dbObject->execute("UPDATE `user` SET `login` = \"".$login."\", `name` = \"".$name."\", `surname` = \"".$surname."\", `enable` = ".$enable." WHERE `uid` = ".$uid.";");
						} else {
							$dbObject->execute("UPDATE `user` SET `login` = \"".$login."\", `name` = \"".$name."\", `surname` = \"".$surname."\", `password` = \"".sha1($login.$password)."\", `enable` = ".$enable." WHERE `uid` = ".$uid.";");
						}
						$rGroups = $dbObject->fetchAll("SELECT `gid` FROM `user_in_group` WHERE `uid` = ".$uid.";");
						foreach($rGroups as $group) {
							if(!in_array($group['gid'], $groups)) {
								$dbObject->execute("DELETE FROM `user_in_group` WHERE `gid` = ".$group['gid']." AND `uid` = ".$uid.";");
							}
						} 
						foreach($groups as $group) {
							$row = $dbObject->fetchAll("SELECT `gid` FROM `user_in_group` WHERE `gid` = ".$group." AND `uid` = ".$uid.";");
							if(count($row) == 0) {
								$dbObject->execute("INSERT INTO `user_in_group`(`uid`, `gid`) VALUES (".$uid.", ".$group.");");
							}
						}
					} else {
						$maxUid = $dbObject->fetchAll("SELECT MAX(`uid`) AS `muid` FROM `user`;");
						$uid = $maxUid[0]['muid'] + 1;
						$dbObject->execute("INSERT INTO `user`(`uid`, `login`, `name`, `surname`, `password`, `enable`) VALUES (".$uid.", \"".$login."\", \"".$name."\", \"".$surname."\", \"".sha1($login.$password)."\", ".$enable.");");
						foreach($groups as $group) {
							$dbObject->execute("INSERT INTO `user_in_group`(`uid`, `gid`) VALUES (".$uid.", ".$group.");");
						}
					}
				} else {
					$errorList = '';
					foreach($errors as $error) {
						$errorList .= parent::getError($error);
					}
					$return .= parent::getFrame($rb->get('management.error.title'), $errorList, "", true);
          
					$user = array('uid' => "", 'login' => $login, 'name' => $name, 'surname' => $surname);
					$return .= parent::getFrame($rb->get('management.edit'), self::editForm($user, $groups), '');
				}
			}
      
			if($attUserId != false) {
				$_POST['user-list-edit'] = $rb->get('management.edit');
				$_POST['user-list-uid'] = $attUserId;
			}
      
			if($_POST['user-list-edit'] == $rb->get('management.edit')) {
				$uid = $_POST['user-list-uid'];
				$permission = $dbObject->fetchAll('SELECT DISTINCT `user`.`uid` AS `this_uid`, `user`.`login`, `user`.`name`,`user`.`surname` FROM `user` LEFT JOIN `user_in_group` ON `user`.`uid` = `user_in_group`.`uid` LEFT JOIN `group` ON `user_in_group`.`gid` = `group`.`gid` WHERE (`group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `user`.`uid` = '.$loginObject->getUserId().') AND `user`.`uid` = '.$uid.' ORDER BY `user`.`uid`;');
				if(count($permission) > 0) {
					$user = $dbObject->fetchAll("SELECT `uid`, `login`, `name`,`surname`, `enable` FROM `user` WHERE `uid` = ".$uid." ORDER BY `uid`;");
					$groups = $dbObject->fetchAll("SELECT `group`.`gid` FROM `group` LEFT JOIN `user_in_group` ON `group`.`gid` = `user_in_group`.`gid` WHERE `user_in_group`.`uid` = ".$user[0]['uid'].";");
					$return .= parent::getFrame($rb->get('management.edittitle'), self::editForm($user[0], $groups), '');
				} else {
					$return .= parent::getFrame($rb->get('management.edittitle'), parent::getError($rb->get('management.permdenied')), '');
				}
			}
      
			if($_POST['user-list-delete'] == $rb->get('management.delete')) {
				$uid = $_POST['user-list-uid'];
				$permission = $dbObject->fetchAll('SELECT DISTINCT `user`.`uid` AS `this_uid`, `user`.`login`, `user`.`name`,`user`.`surname` FROM `user` LEFT JOIN `user_in_group` ON `user`.`uid` = `user_in_group`.`uid` LEFT JOIN `group` ON `user_in_group`.`gid` = `group`.`gid` WHERE (`group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `user`.`uid` = '.$loginObject->getUserId().') AND `user`.`uid` = '.$uid.' ORDER BY `user`.`uid`;');
				if(count($permission) > 0) {
					$dbObject->execute("DELETE FROM `user_in_group` WHERE `uid` = ".$uid.";");
					$dbObject->execute("DELETE FROM `user` WHERE `uid` = ".$uid.";");
				} else {
					$return .= parent::getFrame($rb->get('management.edittitle'), parent::getError($rb->get('management.permdenied')), '');
				}
			}
		
			if($_POST['new-user'] == $rb->get('management.new')) {
				$return .= parent::getFrame($rb->get('management.edittitle'), self::editForm(array(), array()), '');
			}
      
			if($attUserId == false) {
				$n = 1;
				$returnTmp = ''
				.'<div class="user-management">'
					.'<table class="user-list-table standart clickable">'
						.'<tr>'
							.'<th class="user-list-th user-list-id">'.$rb->get('management.uid').':</th>'
							.'<th class="user-list-th user-list-login">'.$rb->get('reg.username').':</th>'
							.'<th class="user-list-th user-list-name">'.$rb->get('reg.name').':</th>'
							.'<th class="user-list-th user-list-surname">'.$rb->get('reg.surname').':</th>'
							.'<th class="user-list-th user-list-group">'.$rb->get('management.groups').':</th>'
							.'<th class="user-list-th user-list-edit"></th>'
						.'</tr>';
				$users = $dbObject->fetchAll('SELECT DISTINCT `user`.`uid` AS `this_uid`, `user`.`login`, `user`.`name`,`user`.`surname` FROM `user` LEFT JOIN `user_in_group` ON `user`.`uid` = `user_in_group`.`uid` LEFT JOIN `group` ON `user_in_group`.`gid` = `group`.`gid` WHERE `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `user`.`uid` = '.$loginObject->getUserId().' ORDER BY `user`.`uid`;');
				foreach($users as $user) {
					$groups = $dbObject->fetchAll("SELECT `group`.`name` FROM `group` LEFT JOIN `user_in_group` ON `group`.`gid` = `user_in_group`.`gid` WHERE `user_in_group`.`uid` = ".$user['this_uid'].";");
        
					$groupList = '';
					foreach($groups as $group) {
						$groupList .= $group['name'].', ';
					}
					$groupList = substr($groupList, 0, strlen($groupList) - 2);
					$returnTmp .= ''
					.'<tr class="'.((($n % 2) == 0) ? 'even' : 'idle').'">'
						.'<td class="user-list-td user-list-id">'
							.$user['this_uid']
						.'</td>'
						.'<td class="user-list-td user-list-login">'
							.$user['login']
						.'</td>'
						.'<td class="user-list-td user-list-name">'
							.$user['name']
						.'</td>'
						.'<td class="user-list-td user-list-surname">'
							.$user['surname']
						.'</td>'
						.'<td class="user-list-td user-list-group">'
							.$groupList
						.'</td>'
						.'<td class="user-list-td user-list-edit">'
							.'<form name="user-list-edit1" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
								.'<input type="hidden" name="user-list-uid" value="'.$user['this_uid'].'" />'
								.'<input type="hidden" name="user-list-edit" value="'.$rb->get('management.edit').'" />'
								.'<input type="image" src="~/images/page_edi.png" name="user-list-edit" value="'.$rb->get('management.edit').'" title="'.$rb->get('management.edit').', id('.$user['this_uid'].')" /> '
							.'</form>'
							.'<form name="user-list-edit2" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
								.'<input type="hidden" name="user-list-uid" value="'.$user['this_uid'].'" />'
								.'<input type="hidden" name="user-list-delete" value="'.$rb->get('management.delete').'" />'
								.'<input class="confirm" type="image" src="~/images/page_del.png" name="user-list-delete" value="'.$rb->get('management.delete').'" title="'.$rb->get('management.deletetitle').', id('.$user['this_uid'].')" />'
							.'</form>'
						.'</td>'
					.'</tr>';
					$n ++;
				}
				$returnTmp .= ''
					.'</table>'
				.'</div>';
      
				$returnTmp .= ''
				.'<hr />'
				.'<div class="gray-box">'
					.'<form name="user-new-user" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
						.'<input type="submit" name="new-user" value="'.$rb->get('management.new').'" title="'.$rb->get('management.newtitle').'" />'
					.'</form>'
				.'</div>';
				$return .= parent::getFrame($rb->get('management.listtitle'), $returnTmp, '');
			}
      
			return $return;
		}
    
		private function editForm($user, $groups) {
			global $dbObject;
			global $loginObject;
			$rb = new ResourceBundle();
			$rb->loadBundle($this->BundleName, $this->BundleLang);
      
			$allGroups = $dbObject->fetchAll('SELECT `gid`, `name` FROM `group` WHERE (`group`.`gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().')) ORDER BY `value`;');
			$groupSelect = '<select name="user-edit-groups[]" multiple="multiple" size="6">';
			foreach($allGroups as $group) {
				$selected = false;
				foreach($groups as $gp) {
					if($gp['gid'] == $group['gid']) {
						$selected = true;
					}
				}
				$groupSelect .= '<option'.(($selected) ? ' selected="selected"' : '').' value="'.$group['gid'].'">'.$group['name'].'</option>';
			}
			$groupSelect .= '</select>';
      
			$generated = false;
			if(strlen($user['login']) == 0) {
				$generated = true;
				$chars = array("1","2","3","4","5","6","7","8","9","0","a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z");
				$length = 6;
				$passwd = '';
				for($i = 0; $i < $length; $i ++) {
					$passwd .= $chars[rand(0, count($chars) - 1)];
				}
				$user['password'] = $passwd;
				$user['password-again'] = $passwd;
			} else {
				$user['password'] = '1a1';
				$user['password-again'] = '1a1';
			}
      
			$return .= ''
			.'<div class="user-edit-cover">'
				.'<form name="user-edit-form" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
					.'<div class="user-edit-prop">'
						.'<div class="user-edit-login">'
							.'<label for="user-edit-login">'.$rb->get('reg.username').': <span>*</span></label> '
							.'<input type="text" id="user-edit-login" name="user-edit-login" value="'.$user['login'].'" />'
						.'</div>'
						.'<div class="user-edit-name">'
							.'<label for="user-edit-name">'.$rb->get('reg.name').':</label> '
							.'<input type="text" id="user-edit-name" name="user-edit-name" value="'.$user['name'].'" />'
						.'</div>'
						.'<div class="user-edit-surname">'
							.'<label for="user-edit-surname">'.$rb->get('reg.surname').':</label> '
							.'<input type="text" id="user-edit-surname" name="user-edit-surname" value="'.$user['surname'].'" />'
						.'</div>'
						.'<div class="user-edit-password">'
							.(($generated) ? '<div class="generated-password">'.$rb->get('management.generatedpwd').': <strong>'.$user['password'].'</strong></div>' : '')
							.'<label for="user-edit-password">'.$rb->get('reg.password1').': <span>**</span></label> '
							.'<input type="password" id="user-edit-password" name="user-edit-password" value="'.$user['password'].'" />'
						.'</div>'
						.'<div class="user-edit-password-again">'
							.'<label for="user-edit-password-again">'.$rb->get('reg.password2').': <span>**</span></label> '
							.'<input type="password" id="user-edit-password-again" name="user-edit-password-again" value="'.$user['password-again'].'" />'
						.'</div>'
						.'<div class="user-edit-enable">'
							.'<label for="user-edit-enable">'.$rb->get('management.enabled').':</label> '
							.'<input type="checkbox" id="user-edit-enable" name="user-edit-enable"'.(($user['enable'] == 0) ? '' : 'checked="checked"').' />'
						.'</div>'
					.'</div>'
					.'<div class="user-edit-groups">'
						.'<label for="user-edit-groups">'.$rb->get('management.groups').':</label> '
						.$groupSelect
					.'</div>'
					.'<div class="clear"></div>'
					.'<div class="user-edit-info">'
						.'<div class="user-edit-1-dot"><span>*</span> '.$rb->get('reg.error.usernameshort').'.</div>'
						.'<div class="user-edit-2-dot"><span>**</span> '.$rb->get('reg.error.passwordshort').'.</div>'
					.'</div>'
					.'<div class="user-edit-submit">'
						.'<input type="hidden" name="user-edit-uid" value="'.$user['uid'].'" />'
						.'<input type="submit" name="user-edit-save" value="'.$rb->get('management.save').'" title="'.$rb->get('management.savetitle').'" />'
					.'</div>'
				.'</form>'
			.'</div>';
      
			return $return;
		}
    
    /**
     *
     *	Adds new user group to system.
     *	C tag.
     *	
     *	@param	useFrames				use frames in output
     *
     */		 		 		 		 		     
    public function addNewGroup($useFrames = false) {
			global $dbObject;
			global $loginObject;
			$parentGid = 0;
			$groupName = '';
			$return = '';
			
			$ok = false;
			foreach($loginObject->getGroups() as $group) {
				if($group['name'] == 'admins' || $group['name'] == 'web-projects') {
					$ok = true;
				}
			}
			
			if($ok) {
				if($_POST['new-group-submit']) {
					$parentGid = $_POST['new-group-parent'];
					$groupName = $_POST['new-group-name'];
					
					if(strlen($groupName) > 1 && count($dbObject->fetchAll('SELECT `gid` FROM `group` WHERE `name` = "'.$groupName.'";')) == 0) {
						$parOk = false;
						$parVal = -1;
						foreach($loginObject->getGroups() as $group) {
							if($group['gid'] == $parentGid) {
								$parOk = true;
								$parVal = $group['value'];
							}
						}
						
						if($parOk) {
							$dbObject->execute('INSERT INTO `group`(`parent_gid`, `name`, `value`) VALUES ('.$parentGid.', "'.$groupName.'", '.($parVal + 1).');');
							$return .= '<h4 class="success">Group added!</h4>';
						} else {
							$return .= '<h4 class="error">Permission Denied!</h4>';
						}
					} elseif(strlen($groupName) < 2) {
						$return .= '<h4 class="error">Group name must contain at least 2 characters!</h4>';
					} else {
						$return .= '<h4 class="error">Group with this name already exists!</h4>';
					}
				}
				
				$groupsForParent = '<select id="new-group-parent" name="new-group-parent" class="w200">';
				foreach($loginObject->getGroups() as $group) {
					$groupsForParent .= '<option value="'.$group['gid'].'"'.(($parentGid == $group['gid']) ? 'selected="selected"' : '').'>'.$group['name'].'</option>';
				}
				$groupsForParent .= '</select>';
			
				$return .= ''
				.'<div class="add-new-group">'
					.'<form name="add-new-group" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
						.'<div class="gray-box-float">'
							.'<label for="new-group-name" class="w300">Group name: (<span class="red">at least 2 characters</span>)</label> '
							.'<input type="text" id="new-group-name" name="new-group-name" value="'.$groupName.'" class="w200" />'
						.'</div>'
						.'<div class="clear"></div>'
						.'<div class="gray-box-float">'
							.'<label for="new-group-parent" class="w300">Select parent group:</label> '
							.$groupsForParent
						.'</div>'
						.'<div class="clear"></div>'
						.'<hr />'
						.'<div class="gray-box">'
							.'<input type="submit" name="new-group-submit" value="Save" />'
						.'</div>'
					.'</form>'
				.'</div>';
			
			} else {
				$return .= '<h4 class="error">Permission Denied!</h4>';
			}
			
			if($useFrames == "false") {
				return $return;
			} else {
				return parent::getFrame('Add new Group', $return, "", true);
			}
		}
		
		/**
     *
     *	Deletes user group from system.
     *	C tag.
     *	
     *	@param	useFrames				use frames in output
     *
     */
		public function deleteGroup($useFrames = false) {
			global $dbObject;
			global $loginObject;
			$return = '';
			
			$ok = false;
			foreach($loginObject->getGroups() as $group) {
				if($group['name'] == 'admins' || $group['name'] == 'web-projects') {
					$ok = true;
				}
			}
			
			if($ok) {
				if($_POST['delete-group'] == 'Delete group') {
					$groupId = $_POST['group-id'];
					if(count($dbObject->fetchAll('SELECT `gid` FROM `group` WHERE `gid` = '.$groupId.' AND `parent_gid` IN ('.$loginObject->getGroupsIdsAsString().');')) != 0) {
						if(count($dbObject->fetchAll('SELECT `gid` FROM `user_in_group` WHERE `gid` = '.$groupId.';')) == 0) {
							// Smazat skupinu
							$dbObject->execute('DELETE FROM `group` WHERE `gid` = '.$groupId.';');
							$dbObject->execute('DELETE FROM `user_in_group` WHERE `gid` = '.$groupId.';');
							$dbObject->execute('DELETE FROM `page_right` WHERE `gid` = '.$groupId.';');
							$dbObject->execute('DELETE FROM `article_line_right` WHERE `gid` = '.$groupId.';');
							$dbObject->execute('DELETE FROM `directory_right` WHERE `gid` = '.$groupId.';');
							$dbObject->execute('DELETE FROM `file_right` WHERE `gid` = '.$groupId.';');
							$dbObject->execute('DELETE FROM `template_right` WHERE `gid` = '.$groupId.';');
							$dbObject->execute('DELETE FROM `web_project_right` WHERE `gid` = '.$groupId.';');
							$return .= '<h4 class="success">Group deleted!</h4>';
						} else {
							$return .= '<h4 class="error">There are still users in this [Gid = '.$groupId.'] group!</h4>';
						}
					} else {
						$return .= '<h4 class="error">Permission Denied!</h4>';
					}
				}
			
				$groupsList = '';
				$i = 0;
				$groups = $dbObject->fetchAll('SELECT DISTINCT `group`.`gid`, `group`.`name`, `group`.`parent_gid` FROM `group` LEFT JOIN `user_in_group` ON `group`.`gid` = `user_in_group`.`gid` WHERE `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().');');
				if(count($groups) > 0) {
					foreach($groups as $group) {
						$parentName = $dbObject->fetchAll('SELECT `name` FROM `group` WHERE `gid` = '.$group['parent_gid'].';');
						$parentName = $parentName[0]['name'];
						$groupsList .= ''
						.'<tr class="'.((($i % 2) == 1) ? 'even' : 'idle').'">'
							.'<td class="group-list-gid">'.$group['gid'].'</td>'
							.'<td class="group-list-name">'.$group['name'].'</td>'
							.'<td class="group-list-parent">'.$parentName.'</td>'
							.'<td class="group-list-action">'
							.(((count($dbObject->fetchAll('SELECT `gid` FROM `user_in_group` WHERE `gid` = '.$group['gid'].';')) == 0) && (count($dbObject->fetchAll('SELECT `gid` FROM `group` WHERE `parent_gid` = '.$group['gid'].';')) == 0)) ? ''	
								.'<form name="group-delete" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
									.'<input type="hidden" name="group-id" value="'.$group['gid'].'" />'
									.'<input type="hidden" name="delete-group" value="Delete group" />'
									.'<input class="confirm" type="image" src="~/images/page_del.png" name="delete-group" value="Delete group" title="Delete group, id('.$group['gid'].')" />'
								.'</form>'
							: '')
							.'</td>'
						.'</tr>';
						$i ++;
					}
			
					$return .= ''
					.'<div class="group-list">'
							.'<table class="standart">'
								.'<tr>'
									.'<th>Gid:</th>'
									.'<th>Name:</th>'
									.'<th>Parent name:</th>'
									.'<th>Action:</th>'
								.'</tr>'
								.$groupsList
							.'</table>'
						.'</form>'
					.'</div>';
				} else {
					$return .= '<h4 class="warning">No groups to edit!</h4>';
				}
			} else {
				$return .= '<h4 class="error">Permission Denied!</h4>';
			}
			
			if($useFrames == "false") {
					return $return;
			} else {
					return parent::getFrame('Group list', $return, "", true);
			}
		}
		
		/**
		 *
		 *	Show user log info
		 *	C tag.
		 *	
		 *	@param	useFrames			use frames in output		 		 		 
		 *
		 *
		 */		 		 		 		 		
		public function showUserLog($useFrames = false) {
			global $dbObject;
			global $loginObject;
			$return = '';
			
			$ok = false;
			foreach($loginObject->getGroups() as $group) {
				if($group['name'] == 'admins') {
					$ok = true;
				}
			}
			
			if($ok) {
				$rows = '';
				$i = 0;
				$logs = $dbObject->fetchAll('SELECT `user_log`.`id`, `user_log`.`session_id`, `user_log`.`user_id`, `user_log`.`used_group`, `user`.`name`, `user`.`surname`, `user`.`login`, `user_log`.`login_timestamp`, `user_log`.`logout_timestamp` FROM `user_log` LEFT JOIN `user` ON `user_log`.`user_id` = `user`.`uid` ORDER BY `user_log`.`login_timestamp`;');
				foreach($logs as $log) {
					$rows .= ''
					.'<tr class="'.((($i % 2) == 1) ? 'even' : 'idle').'">'
						.'<td class="user-log-ud">'.$log['id'].'</td>'
						.'<td class="user-log-uid">'.$log['user_id'].'</td>'
						.'<td class="user-log-name">'.$log['name'].'</td>'
						.'<td class="user-log-surname">'.$log['surname'].'</td>'
						.'<td class="user-log-login">'.$log['login'].'</td>'
						.'<td class="user-log-logon">'.date('H:i:s d.m.Y', $log['login_timestamp']).'</td>'
						.'<td class="user-log-logout">'.(($log['session_id'] != $loginObject->getSessionId()) ? (($log['logout_timestamp'] != 0) ? date('H:i:s d:m:Y', $log['logout_timestamp']) : 'No logout time') : 'Current session').'</td>'
						.'<td class="user-log-login">'.$log['used_group'].'</td>'
					.'</tr>';
					$i ++;
				}
				
				$return .= ''
				.'<div class="user-log-list">'
					.'<table class="data-table standart">'
						.'<thead>'
						.'<tr>'
							.'<th class="user-log-id">Id:</th>'
							.'<th class="user-log-uid">Uid:</th>'
							.'<th class="user-log-name">Name:</th>'
							.'<th class="user-log-surname">Surname:</th>'
							.'<th class="user-log-login">Login:</th>'
							.'<th class="user-log-logon">Logon:</th>'
							.'<th class="user-log-logout">Logout:</th>'
							.'<th class="user-log-logout">used group:</th>'
						.'</tr>'
						.'</thead>'
						.'<tbody>'
						.$rows
						.'</tbody>'
					.'</table>'
				.'</div>';
			} else {
				$return .= '<h4 class="error">Permission Denied!</h4>';
			}
			
			if($useFrames == "false") {
					return $return;
			} else {
					return parent::getFrame('User log', $return, "", true);
			}
		}
		
		
		/**
		 *
		 *	Truncate user log info
		 *	C tag.
		 *	
		 *	@param	useFrames			use frames in output		 		 		 
		 *
		 *
		 */	
		public function truncateUserLog($useFrames = false) {
			global $dbObject;
			global $loginObject;
			$return = '';
			
			$ok = false;
			foreach($loginObject->getGroups() as $group) {
				if($group['name'] == 'admins') {
					$ok = true;
				}
			}
			
			if($ok) {
				if($_POST['user-log-truncate'] == 'Clear user log!') {
					$dbObject->execute('DELETE FROM `user_log` WHERE `session_id` != '.$loginObject->getSessionId().';');
					$return .= '<h4 class="success">User log clared!</h4>';
				}
				
				$return .= ''
				.'<div class="user-log-truncate">'
					.'<form name="user-log-truncate" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
						.'<input class="confirm" type="submit" name="user-log-truncate" value="Clear user log!" title="Clear user log" />'
					.'</form>'
				.'</div>';
			} else {
				$return .= '<h4 class="error">Permission Denied!</h4>';
			}
			
			if($useFrames == "false") {
					return $return;
			} else {
					return parent::getFrame('Truncate user log', $return, "", true);
			}
		}
		
		public function registerUser($groups, $disableUser = false, $pageId = false) {
			global $webObject;
			$rb = new ResourceBundle();
			$rb->loadBundle($this->BundleName, $this->BundleLang);
			$user = array();
			$ok = true;
			$messages = array();;
			$msgHtml = '';
			
			if($_POST['user-register-submit'] == $rb->get('reg.submit')) {
				$user['name'] = $_POST['user-register-name'];
				$user['surname'] = $_POST['user-register-surname'];
				$user['username'] = $_POST['user-register-username'];
				$user['password1'] = $_POST['user-register-password1'];
				$user['password2'] = $_POST['user-register-password2'];
				
				if(strlen($user['password1']) < 6) {
					$ok = false;
					$messages[] = parent::getError($rb->get('reg.error.passwordshort'));
				}
				if($user['password1'] != $user['password2']) {
					$ok = false;
					$messages[] = parent::getError($rb->get('reg.error.passwordnotmatch'));
				}
				if(strlen($user['username']) < 4) {
					$ok = false;
					$messages[] = parent::getError($rb->get('reg.error.usernameshort'));
				}
				$userNames = parent::db()->fetchAll('select `uid` from `user` where `login` = "'.$user['username'].'";');
				if(count($userNames) > 0) {
					$ok = false;
					$messages[] = parent::getError($rb->get('reg.error.usernamenotunique'));
				}
				if($ok) {
					if($disableUser == 'true') {
						$user['enable'] = 0;
					} else {
						$user['enable'] = 1;
					}
					$user['password'] = sha1($user['username'].$user['password1']);
					
					//parent::db()->setMockMode(true);
					parent::db()->execute(parent::query()->get('register', $user, 'user'));
					$uid = parent::db()->fetchSingle(parent::query()->get('userByUsername', $user, 'user'));
					$uid = $uid['uid'];
					$groupNames = split(',', $groups);
					foreach($groupNames as $gpName) {
						$gid = parent::db()->fetchSingle(parent::query()->get('groupByName', array('name' => trim($gpName)), 'user'));
						$gid = $gid['gid'];
						parent::db()->execute(parent::query()->get('addUserInGroup', array('uid' => $uid, 'gid' => $gid), 'user'));
					}
					$messages[] = parent::getSuccess($rb->get('reg.saved'));
					$user = array();
					if($pageId != '') {
						$webObject->redirectTo($pageId);
					}
					//parent::db()->setMockMode(false);
				}
				
				foreach($messages as $msg) {
					$msgHtml .= $msg;
				}
			}
			
			$return .= ''
			.'<form name="user-register" action="'.$_SERVER['REDIRECT_URL'].'" method="post">'
				.$msgHtml
				.'<div class="gray-box">'
					.'<label for="user-register-name">'.$rb->get('reg.name').'</label>'
					.'<input type="text" name="user-register-name" id="user-register-name" value="'.$user['name'].'" />'
				.'</div>'
				.'<div class="gray-box">'
					.'<label for="user-register-surname">'.$rb->get('reg.surname').'</label>'
					.'<input type="text" name="user-register-surname" id="user-register-surname" value="'.$user['surname'].'" />'
				.'</div>'
				.'<div class="gray-box">'
					.'<label for="user-register-username">'.$rb->get('reg.username').'</label>'
					.'<input type="text" name="user-register-username" id="user-register-username" value="'.$user['username'].'" />'
				.'</div>'
				.'<div class="gray-box">'
					.'<label for="user-register-password1">'.$rb->get('reg.password1').'</label>'
					.'<input type="password" name="user-register-password1" id="user-register-password1" value="'.$user['password1'].'" />'
				.'</div>'
				.'<div class="gray-box">'
					.'<label for="user-register-password2">'.$rb->get('reg.password2').'</label>'
					.'<input type="password" name="user-register-password2" id="user-register-password2" value="'.$user['password2'].'" />'
				.'</div>'
				.'<div class="gray-box">'
					.'<input type="submit" name="user-register-submit" value="'.$rb->get('reg.submit').'" />'
				.'</div>'
			.'</form>';
			
			return $return;
		}
		
		public function getUserId() {
			global $loginObject;
			
			return $loginObject->getUserId();
		}

  }

?>
