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
   *  @timestamp  2009-05-16
   *
   */              
  class User extends BaseTagLib {
    
    /**
     *
     *  Initialize object.
     *
     */                   
    public function __construct() {
      self::setTagLibXml("xml/User.xml");
    }
    
    public function showUserManagement() {
      global $dbObject;
      global $loginObject;
      $return = '';
      
      if($_POST['user-edit-save'] == "Save") {
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
          $errors[] = 'Login must have at least 5 characters!';
        }
        if(strlen($password) < 6 && $password != '1a1') {
          $errors[] = 'Password must have at least 6 characters!';
        }
        if($password != $passwordAgain) {
          $errors[] = 'Passwords must be same!';
        }
        if(count($groups) == 0) {
          $errors[] = 'User must be in one group at least!';
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
          $errorList = '<ul class="error-list">';
          foreach($errors as $error) {
            $errorList .= '<li class="error-list-item"><span>'.$error.'</span></li>';
          }
          $errorList .= '</ul>';
          $return .= parent::getFrame("Error Message", $errorList, "", true);
          
          $user = array('uid' => "", 'login' => $login, 'name' => $name, 'surname' => $surname);
          $return .= parent::getFrame('Edit User', self::editForm($user, $groups), '');
        }
      }
      
      if($_POST['user-list-edit'] == "Edit") {
        $uid = $_POST['user-list-uid'];
        $permission = $dbObject->fetchAll('SELECT DISTINCT `user`.`uid` AS `this_uid`, `user`.`login`, `user`.`name`,`user`.`surname` FROM `user` LEFT JOIN `user_in_group` ON `user`.`uid` = `user_in_group`.`uid` LEFT JOIN `group` ON `user_in_group`.`gid` = `group`.`gid` WHERE (`group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `user`.`uid` = '.$loginObject->getUserId().') AND `user`.`uid` = '.$uid.' ORDER BY `user`.`uid`;');
        if(count($permission) > 0) {
        	$user = $dbObject->fetchAll("SELECT `uid`, `login`, `name`,`surname`, `enable` FROM `user` WHERE `uid` = ".$uid." ORDER BY `uid`;");
        	$groups = $dbObject->fetchAll("SELECT `group`.`gid` FROM `group` LEFT JOIN `user_in_group` ON `group`.`gid` = `user_in_group`.`gid` WHERE `user_in_group`.`uid` = ".$user[0]['uid'].";");
        	$return .= parent::getFrame('Edit User', self::editForm($user[0], $groups), '');
        } else {
					$return .= parent::getFrame('Edit User', '<h4 class="error">Permission Denied!</h4>', '');
				}
      }
      
      if($_POST['user-list-delete'] == "Delete") {
        $uid = $_POST['user-list-uid'];
				$permission = $dbObject->fetchAll('SELECT DISTINCT `user`.`uid` AS `this_uid`, `user`.`login`, `user`.`name`,`user`.`surname` FROM `user` LEFT JOIN `user_in_group` ON `user`.`uid` = `user_in_group`.`uid` LEFT JOIN `group` ON `user_in_group`.`gid` = `group`.`gid` WHERE (`group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `user`.`uid` = '.$loginObject->getUserId().') AND `user`.`uid` = '.$uid.' ORDER BY `user`.`uid`;');
        if(count($permission) > 0) {
  	      $dbObject->execute("DELETE FROM `user_in_group` WHERE `uid` = ".$uid.";");
	        $dbObject->execute("DELETE FROM `user` WHERE `uid` = ".$uid.";");
        } else {
					$return .= parent::getFrame('Edit User', '<h4 class="error">Permission Denied!</h4>', '');
				}
      }
      
      if($_POST['new-user'] == "New User") {
        $return .= parent::getFrame('Edit User', self::editForm(array(), array()), '');
      }
      
      $n = 1;
      $returnTmp = ''
      .'<div class="user-management">'
        .'<table class="user-list-table">'
          .'<tr>'
            .'<th class="user-list-th user-list-id">Uid</th>'
            .'<th class="user-list-th user-list-login">Login</th>'
            .'<th class="user-list-th user-list-name">Name</th>'
            .'<th class="user-list-th user-list-surname">Surname</th>'
            .'<th class="user-list-th user-list-group">Group</th>'
            .'<th class="user-list-th user-list-edit">Edit</th>'
          .'</tr>';
      //$users = $dbObject->fetchAll("SELECT `user`.`uid` AS `this_uid`, `user`.`login`, `user`.`name`,`user`.`surname`, (SELECT MIN(`value`) FROM `user` LEFT JOIN `user_in_group` ON `user`.`uid` = `user_in_group`.`uid` LEFT JOIN `group` ON `user_in_group`.`gid` = `group`.`gid` WHERE `user`.`uid` = `this_uid`) AS `min_value` FROM `user` ORDER BY `user`.`uid`;");
      $users = $dbObject->fetchAll('SELECT DISTINCT `user`.`uid` AS `this_uid`, `user`.`login`, `user`.`name`,`user`.`surname` FROM `user` LEFT JOIN `user_in_group` ON `user`.`uid` = `user_in_group`.`uid` LEFT JOIN `group` ON `user_in_group`.`gid` = `group`.`gid` WHERE `group`.`parent_gid` IN ('.$loginObject->getGroupsIdsAsString().') OR `user`.`uid` = '.$loginObject->getUserId().' ORDER BY `user`.`uid`;');
      foreach($users as $user) {
        //if($user['min_value'] < $loginObject->getGroupValue()) {
        //  continue;
        //}
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
            .'<form name="user-list-edit1" method="post" action="">'
              .'<input type="hidden" name="user-list-uid" value="'.$user['this_uid'].'" />'
              .'<input type="hidden" name="user-list-edit" value="Edit" />'
              .'<input type="image" src="~/images/page_edi.png" name="user-list-edit" value="Edit" title="Edit user" /> '
            .'</form>'
            .'<form name="user-list-edit2" method="post" action="">'
              .'<input type="hidden" name="user-list-uid" value="'.$user['this_uid'].'" />'
              .'<input type="hidden" name="user-list-delete" value="Delete" />'
              .'<input class="confirm" type="image" src="~/images/page_del.png" name="user-list-delete" value="Delete" title="Delete user" />'
            .'</form>'
          .'</td>'
        .'</tr>';
        $n ++;
      }
      $returnTmp .= ''
        .'</table>'
      .'</div>';
      $return .= parent::getFrame('User List', $returnTmp, '');
      
      $returnTmp = ''
      .'<div class="user-management-new-user">'
        .'<form name="user-new-user" method="post" action="">'
          .'<input type="submit" name="new-user" value="New User" />'
        .'</form>'
      .'</div>';
      $return .= parent::getFrame('New User', $returnTmp, '');
      
      return $return;
    }
    
    private function editForm($user, $groups) {
      global $dbObject;
      global $loginObject;
      
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
        .'<form name="user-edit-form" method="post" action="">'
          .'<div class="user-edit-prop">'
            .'<div class="user-edit-login">'
              .'<label for="user-edit-login">Login: <span>*</span></label> '
              .'<input type="text" id="user-edit-login" name="user-edit-login" value="'.$user['login'].'" />'
            .'</div>'
            .'<div class="user-edit-name">'
              .'<label for="user-edit-name">Name:</label> '
              .'<input type="text" id="user-edit-name" name="user-edit-name" value="'.$user['name'].'" />'
            .'</div>'
            .'<div class="user-edit-surname">'
              .'<label for="user-edit-surname">Surname:</label> '
              .'<input type="text" id="user-edit-surname" name="user-edit-surname" value="'.$user['surname'].'" />'
            .'</div>'
            .'<div class="user-edit-password">'
              .(($generated) ? '<div class="generated-password">Generated password: <strong>'.$user['password'].'</strong></div>' : '')
              .'<label for="user-edit-password">Password: <span>**</span></label> '
              .'<input type="password" id="user-edit-password" name="user-edit-password" value="'.$user['password'].'" />'
            .'</div>'
            .'<div class="user-edit-password-again">'
              .'<label for="user-edit-password-again">Password again: <span>**</span></label> '
              .'<input type="password" id="user-edit-password-again" name="user-edit-password-again" value="'.$user['password-again'].'" />'
            .'</div>'
            .'<div class="user-edit-enable">'
              .'<label for="user-edit-enable">Enable:</label> '
              .'<input type="checkbox" id="user-edit-enable" name="user-edit-enable"'.(($user['enable'] == 0) ? '' : 'checked="checked"').' />'
            .'</div>'
          .'</div>'
          .'<div class="user-edit-groups">'
            .'<label for="user-edit-groups">In groups:</label> '
            .$groupSelect
          .'</div>'
        	.'<div class="clear"></div>'
          .'<div class="user-edit-info">'
            .'<div class="user-edit-1-dot"><span>*</span> Must contain at least 5 characters.</div>'
            .'<div class="user-edit-2-dot"><span>**</span> Must contain at least 6 characters.</div>'
          .'</div>'
          .'<div class="user-edit-submit">'
            .'<input type="hidden" name="user-edit-uid" value="'.$user['uid'].'" />'
            .'<input type="submit" name="user-edit-save" value="Save" />'
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
				
				$groupsForParent = '<select id="new-group-parent" name="new-group-parent">';
				foreach($loginObject->getGroups() as $group) {
					$groupsForParent .= '<option value="'.$group['gid'].'"'.(($parentGid == $group['gid']) ? 'selected="selected"' : '').'>'.$group['name'].'</option>';
				}
				$groupsForParent .= '</select>';
			
				$return .= ''
				.'<div class="add-new-group">'
					.'<form name="add-new-group" method="post" action="">'
						.'<div class="new-group-name">'
							.'<label for="new-group-name">Group name: (<span>at least 2 characters</span>)</label> '
							.'<input type="text" id="new-group-name" name="new-group-name" value="'.$groupName.'" />'
						.'</div>'
						.'<div class="new-group-parent">'
							.'<label for="new-group-parent">Select parent group:</label> '
							.$groupsForParent
						.'</div>'
						.'<hr />'
						.'<div class="new-group-submit">'
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
								.'<form name="group-delete" method="post" action="">'
									.'<input type="hidden" name="group-id" value="'.$group['gid'].'" />'
									.'<input type="hidden" name="delete-group" value="Delete group" />'
									.'<input class="confirm" type="image" src="~/images/page_del.png" name="delete-group" value="Delete group" title="Delete group" />'
								.'</form>'
							: '')
							.'</td>'
						.'</tr>';
						$i ++;
					}
			
					$return .= ''
					.'<div class="group-list">'
							.'<table>'
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
				$logs = $dbObject->fetchAll('SELECT `user_log`.`id`, `user_log`.`user_id`, `user`.`name`, `user`.`surname`, `user`.`login`, `user_log`.`login_timestamp`, `user_log`.`logout_timestamp` FROM `user_log` LEFT JOIN `user` ON `user_log`.`user_id` = `user`.`uid` ORDER BY `user_log`.`login_timestamp`;');
				foreach($logs as $log) {
					$rows .= ''
					.'<tr class="'.((($i % 2) == 1) ? 'even' : 'idle').'">'
						.'<td class="user-log-ud">'.$log['id'].'</td>'
						.'<td class="user-log-uid">'.$log['user_id'].'</td>'
						.'<td class="user-log-name">'.$log['name'].'</td>'
						.'<td class="user-log-surname">'.$log['surname'].'</td>'
						.'<td class="user-log-login">'.$log['login'].'</td>'
						.'<td class="user-log-logon">'.date('H:i:s d:m:Y', $log['login_timestamp']).'</td>'
						.'<td class="user-log-logout">'.(($log['logout_timestamp'] != 0) ? date('H:i:s d:m:Y', $log['logout_timestamp']) : 'Current session').'</td>'
					.'</tr>';
					$i ++;
				}
				
				$return .= ''
				.'<div class="user-log-list">'
					.'<table>'
						.'<tr>'
							.'<th class="user-log-id">Id:</th>'
							.'<th class="user-log-uid">Uid:</th>'
							.'<th class="user-log-name">Name:</th>'
							.'<th class="user-log-surname">Surname:</th>'
							.'<th class="user-log-login">Login:</th>'
							.'<th class="user-log-logon">Logon:</th>'
							.'<th class="user-log-logout">Logout:</th>'
						.'</tr>'
						.$rows
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
					.'<form name="user-log-truncate" method="post" action="">'
						.'<input type="submit" name="user-log-truncate" value="Clear user log!" />'
					.'</form>'
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

  }

?>
