<?php

  /**
   *
   *  Require base tag lib class.
   *
   */
  require_once("BaseTagLib.class.php");
  
  /**
   * 
   *  Class System.
   * 	management of web framework.	     
   *      
   *  @author     Marek SMM
   *  @timestamp  2009-10-28
   * 
   */  
  class System extends BaseTagLib {
  
    public function __construct() {
      parent::setTagLibXml("xml/System.xml");
    }
		
		/**
		 *
		 *	Manage user's system properties.
		 *	C tag.
		 *
		 */		 		 		 		 		
		public function manageProperties($useFrames = false, $showMsg = false) {
			global $dbObject;
			global $loginObject;
			$return = '';
			
			$userId = $loginObject->getUserId();
			$typeId = 1;
			
			if($_POST['system-properties-save'] == 'Save') {
				foreach($_POST['system-property-name'] as $id => $name) {
					$value = $_POST['system-property-value'][$id];
					$dbObject->execute('UPDATE `personal_property` SET `name` = "'.$name.'", `value` = "'.$value.'" WHERE `id` = '.$id.';');
				}
				
				if($_POST['system-property-name-new'] != '') {
					$name = $_POST['system-property-name-new'];
					$value = $_POST['system-property-value-new'];
					$dbObject->execute('INSERT INTO `personal_property` (`name`, `value`, `type`, `user_id`) VALUES ("'.$name.'", "'.$value.'", '.$typeId.', '.$userId.');');
				}
			} elseif($_POST['system-properties-delete'] == 'Delete selected') {
				foreach($_POST['system-properties-delete-item'] as $id => $val) {
					$dbObject->execute('DELETE FROM `personal_property` WHERE `id` = '.$id.' AND `user_id` = '.$userId.';');
				}
			}
			
			$properties = $dbObject->fetchAll('SELECT `id`, `name`, `value` FROM `personal_property` WHERE `user_id` = '.$userId.' AND `type` = '.$typeId.' ORDER BY `name`;');
			
			$return .= '' 
			.'<div class="system-properties">'
				.'<form name="system-properties" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
					.'<table>'
						.'<tr>'
							.'<th class="system-properties-name">Name:</th>'
							.'<th class="system-properties-value">Value:</th>'
							.'<th class="system-properties-delete">Delete:</th>';
			$i = 0;
			foreach($properties as $prop) {
				$return .= ''
						.'<tr class="'.(($i % 2) == 1 ? 'even' : 'idle').'">'
							.'<td>'
								.'<input type="text" value="'.$prop['name'].'" name="system-property-name['.$prop['id'].']" id="system-property-name-'.$prop['id'].'" />'
							.'</td>'
							.'<td>'
								.'<input type="text" value="'.$prop['value'].'" name="system-property-value['.$prop['id'].']" id="system-property-value-'.$prop['id'].'" />'
							.'</td>'
							.'<td>'
								.'<input type="checkbox" name="system-properties-delete-item['.$prop['id'].']" />'
							.'</td>'
						.'</tr>';
					$i ++;
			}
			$return .= ''
						.'<tr class="'.(($i % 2) == 1 ? 'even' : 'idle').'">'
							.'<td>'
								.'<input type="text" value="" name="system-property-name-new" id="system-property-name-new" />'
							.'</td>'
							.'<td>'
								.'<input type="text" value="" name="system-property-value-new" id="system-property-value-new" />'
							.'</td>'
							.'<td>&nbsp;</td>'
						.'</tr>'
					.'</table>'
					.'<div class="system-properties-submit">'
						.'<input type="submit" name="system-properties-save" value="Save" /> '
						.'<input type="submit" name="system-properties-delete" value="Delete selected" />'
					.'</div>'
				.'</form>'
			.'</div>';
			
			if($useFrames == "false") {
				return $return;
			} else {
				return parent::getFrame('System properties', $return, "", true);
			}
		}
		
		/**
		 *
		 *	Manage user's system notes.
		 *	C tag.
		 *
		 */		 		 		 		 		
		public function manageNotes($useFrames = false, $showMsg = false) {
			global $dbObject;
			global $loginObject;
			$return = '';
			
			$userId = $loginObject->getUserId();
			$typeId = 1;
			
			if($_POST['system-note-save'] == 'Save') {
				foreach($_POST['system-note-value'] as $id => $name) {
					$dbObject->execute('UPDATE `personal_note` SET `value` = "'.$name.'" WHERE `id` = '.$id.';');
				}
				
				print_r($_POST);
				if($_POST['system-note-value-new'] != '') {
					$value = $_POST['system-note-value-new'];
					$dbObject->execute('INSERT INTO `personal_note` (`value`, `type`, `user_id`) VALUES ("'.$value.'", '.$typeId.', '.$userId.');', true, false);
				}
			} elseif($_POST['system-note-delete'] == 'Delete selected') {
				foreach($_POST['system-note-delete-item'] as $id => $val) {
					$dbObject->execute('DELETE FROM `personal_note` WHERE `id` = '.$id.' AND `user_id` = '.$userId.';');
				}
			}
			
			$properties = $dbObject->fetchAll('SELECT `id`, `value` FROM `personal_note` WHERE `user_id` = '.$userId.' AND `type` = '.$typeId.' ORDER BY `id`;');
			
			$return .= '' 
			.'<div class="system-notes">'
				.'<form name="system-note" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
					.'<table>'
						.'<tr>'
							.'<th class="system-note-value">Value:</th>'
							.'<th class="system-note-delete">Delete:</th>';
			$i = 0;
			foreach($properties as $prop) {
				$return .= ''
						.'<tr class="'.(($i % 2) == 1 ? 'even' : 'idle').'">'
							.'<td>'
								.'<input type="text" value="'.$prop['value'].'" name="system-note-value['.$prop['id'].']" id="system-note-value-'.$prop['id'].'" />'
							.'</td>'
							.'<td>'
								.'<input type="checkbox" name="system-note-delete-item['.$prop['id'].']" />'
							.'</td>'
						.'</tr>';
					$i ++;
			}
			$return .= ''
						.'<tr class="'.(($i % 2) == 1 ? 'even' : 'idle').'">'
							.'<td>'
								.'<input type="text" value="" name="system-note-value-new" id="system-note-value-new" />'
							.'</td>'
							.'<td>&nbsp;</td>'
						.'</tr>'
					.'</table>'
					.'<div class="system-note-submit">'
						.'<input type="submit" name="system-note-save" value="Save" /> '
						.'<input type="submit" name="system-note-delete" value="Delete selected" />'
					.'</div>'
				.'</form>'
			.'</div>';
			
			if($useFrames == "false") {
				return $return;
			} else {
				return parent::getFrame('System notes', $return, "", true);
			}
		}
		
		/**
		 *
		 *	Print user's system notes
		 *	C tag.
		 *
		 */		 		 		 		 		
		public function printNotes($useFrames = false, $showMsg = false) {
			global $dbObject;
			global $loginObject;
			$return = '';
			
			$userId = $loginObject->getUserId();
			$typeId = 1;
			
			$properties = $dbObject->fetchAll('SELECT `value` FROM `personal_note` WHERE `user_id` = '.$userId.' AND `type` = '.$typeId.' ORDER BY `id`;');
			
			$return .= '' 
			.'<div class="system-note-print">'
				.'<ul>';
			$i = 0;
			foreach($properties as $prop) {
				$return .= ''
						.'<li class="system-note-item system-note-item-'.$i.' system-note-item-'.($i % 2 == 0 ? "idle" : "even").'">'.$prop['value'].'</li>';
				$i ++;
			}
			$return .= ''
				.'</ul>'
			.'</div>';
			
			if($useFrames == "false") {
				return $return;
			} else {
				return parent::getFrame('System notes print', $return, "", true);
			}
		}
		
		public function listConnections($useFrames = false, $showMsg = false) {
			global $dbObject;
			$return = '';
			
			if($_POST['db-connection-conn-delete'] == "Delete connection") {
				$id = $_POST['db-connection-conn-id'];
				$dbObject->execute('delete from `db_connection` where `id` = '.$id.';');
				$return .= '<h4 class="success">Connection with id '.$id.' successfuly deleted!</h4>';
			}
			
			$conns = $dbObject->fetchAll('SELECT `id`, `name`, `hostname`, `user`, `password`, `database` FROM `db_connection` ORDER BY `id`;');
			$return .= ''
			.'<div class="db-connection">'
				.'<table>'
					.'<tr>'
						.'<th class="db-connection-id">Id:</th>'
						.'<th class="db-connection-name">Name:</th>'
						.'<th class="db-connection-hostname">Hostname:</th>'
						.'<th class="db-connection-database">DB:</th>'
						.'<th class="db-connection-edit">Edit:</th>'
					.'</tr>';
			$i = 1;
			foreach($conns as $conn) {
				$return .= ''
					.'<tr class="'.((($i % 2) == 0) ? 'even' : 'idle').'">'
						.'<td>'.$conn['id'].'</td>'
						.'<td>'.$conn['name'].'</td>'
						.'<td>'.$conn['hostname'].'</td>'
						.'<td>'.$conn['database'].'</td>'
						.'<td>'
							.'<form name="db-connection-edit" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
								.'<input type="hidden" name="db-connection-conn-id" value="'.$conn['id'].'" />'
								.'<input type="hidden" name="db-connection-conn-edit" value="Edit connection" />'
								.'<input type="image" src="~/images/page_edi.png" name="db-connection-conn-edit" value="Edit connection" title="Edit connection, id '.$conn['id'].'" />'
							.'</form>'
							.'<form name="db-connection-delete" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
								.'<input type="hidden" name="db-connection-conn-id" value="'.$conn['id'].'" />'
								.'<input type="hidden" name="db-connection-conn-delete" value="Delete connection" />'
								.'<input class="confirm" type="image" src="~/images/page_del.png" name="db-connection-conn-delete" value="Delete connection" title="Delete connection, id '.$conn['id'].'" />'
							.'</form>'
						.'</td>'
					.'</tr>';
				$i ++;
			}
			$return .= ''
				.'</table>'
				.'<hr />'
				.'<form name="db-connection-new" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
					.'<span>Create new database connection</span> '
					.'<input type="hidden" name="db-connection-conn-new" value="Create connection" />'
					.'<input type="image" src="~/images/page_add.png" name="db-connection-conn-new" value="Create connection" title="Create connection" />'
				.'</form>'
			.'</div>';
			
		
			if($useFrames == "false") {
				return $return;
			} else {
				if($return != '') {
					return parent::getFrame('List Database Connection', $return, "", true);
				}
			}		
		}
		
		public function editConnection($useFrames = false, $showMsg = false) {
			global $dbObject;
			$return = '';
			
			if($_POST['db-connection-save'] == "Save") {
				$msg = self::editConnectionvalidate();
				
				if(strlen($msg) == 0) {
					$id = $_POST['db-connection-id'];
					$name = $_POST['db-connection-name'];
					$hostname = $_POST['db-connection-hostname'];
					$user = $_POST['db-connection-user'];
					$password = $_POST['db-connection-password'];
					$database = $_POST['db-connection-database'];
					if($id == '') {
						$dbObject->execute('INSERT INTO `db_connection`(`name`, `hostname`, `user`, `password`, `database`) VALUES("'.$name.'", "'.$hostname.'", "'.$user.'", "'.$password.'", "'.$database.'");');
						$connnew = $dbObject->fetchAll('select max(`id`) from `db_connection`;');
						$id = $connew[0]['id'];
						$return .= '<h4 class="success">Connection successfuly added!</h4>';
					} else {
						$dbObject->execute('UPDATE `db_connection` set `name` = "'.$name.'", `hostname` = "'.$hostname.'", `user` = "'.$user.'", `password` = "'.$password.'", `database` = "'.$database.'" where `id` = '.$id.';');
						$return .= '<h4 class="success">Connection successfuly updated!</h4>';
					}
					$return .= self::generateEditConnectionForm($id, $name, $hostname, $user, $password, $database);
				} else {
					if($showMsg == "true") {
						$return .= $msg;
						$return .= self::generateEditConnectionForm("", $_POST['db-connection-name'], $_POST['db-connection-hostname'], $_POST['db-connection-user'], $_POST['db-connection-password'], $_POST['db-connection-database']);
					}
				}
			}
			
			if($_POST['db-connection-conn-new'] == 'Create connection') {
				$return .= self::generateEditConnectionForm("", "", "", "", "", "");
			} else if($_POST['db-connection-conn-edit'] == 'Edit connection') {
				$id = $_POST['db-connection-conn-id'];
				$conn = $dbObject->fetchAll('select `name`, `hostname`, `user`, `password`, `database` from `db_connection` where `id` = '.$id.';', true, true);
				if(count($conn) == 1) {
					$return .= self::generateEditConnectionForm($id, $conn[0]['name'], $conn[0]['hostname'], $conn[0]['user'], $conn[0]['password'], $conn[0]['database']);
				} else {
					if($showMsg == "true") {
						$return .= '<h4 class="error">No such connection!</h4>';
					}
				}
			}
			
		
			if($useFrames == "false") {
				return $return;
			} else {
				if($return != '') {
					return parent::getFrame('Edit Database Connection', $return, "", true);
				}
			}		
		}
		
		private function generateEditConnectionForm($id, $name, $hostname, $user, $password, $database) {
			$return = ''
			.'<div class="db-connection-edit">'
				.'<form name="db-connection-edit" method="post" action="'.$_SERVER['REDIRECT_URL'].'">'
					.'<div class="db-connection-name">'
						.'<label for="db-connection-name">Name:</label>'
						.'<input type="text" id="db-connection-name" name="db-connection-name" value="'.$name.'" />'
					.'</div>'
					.'<div class="db-connection-hostname">'
						.'<label for="db-connection-hostname">Hostname:</label>'
						.'<input type="text" id="db-connection-hostname" name="db-connection-hostname" value="'.$hostname.'" />'
					.'</div>'
					.'<div class="db-connection-user">'
						.'<label for="db-connection-user">Username:</label>'
						.'<input type="text" id="db-connection-user" name="db-connection-user" value="'.$user.'" />'
					.'</div>'
					.'<div class="db-connection-password">'
						.'<label for="db-connection-password">Password:</label>'
						.'<input type="text" id="db-connection-password" name="db-connection-password" value="'.$password.'" />'
					.'</div>'
					.'<div class="db-connection-database">'
						.'<label for="db-connection-database">Database:</label>'
						.'<input type="text" id="db-connection-database" name="db-connection-database" value="'.$database.'" />'
					.'</div>'
					.'<div class="db-connection-submit">'
						.'<input type="hidden" name="db-connection-id" value="'.$id.'" />'
						.'<input type="submit" name="db-connection-save" value="Save" />'
					.'</div>'
				.'</form>'
			.'</div>';
			return $return;
		}
		
		private function editConnectionvalidate() {
			$return = '';
			if(strlen($_POST['db-connection-name']) < 2) {
				$return .= '<h4 class="error">Connection name must have at least 2 chars!</h4>';
			}
			if(strlen($_POST['db-connection-hostname']) == 0) {
				$return .= '<h4 class="error">Connection hostname can\'t be empty!</h4>';
			}
			if(strlen($_POST['db-connection-user']) == 0) {
				$return .= '<h4 class="error">Connection user can\'t be empty!</h4>';
			}
			if(strlen($_POST['db-connection-database']) == 0) {
				$return .= '<h4 class="error">Connection database name can\'t be empty!</h4>';
			}
			
			return $return;
		}
		
		/* ---------- PROPERTIES ---------------------- */
		
		public function setCmsWindowsStyle($value) {
			// not implemented	
		}
		
		public function getCmsWindowsStyle() {
			$val = self::getPropertyValue("System.cms.windowsstyle");
			if($val == "true") {
				return true;
			} else {
				return false;
			}
		}
		
		/* -------------- HELPERS ---------------------- */
		
		/**
		 *
		 *	Return system property value.
		 *	
		 *	@param			name				system property name
		 *	@return			system property value		 		 		 
		 *
		 */		 		 		 		
		public function getPropertyValue($name) {
			global $dbObject;
			global $loginObject;
			
			$userId = $loginObject->getUserId();
			$value = -1;
			$values = $dbObject->fetchAll('SELECT `value` FROM `personal_property` WHERE `user_id` = '.$userId.' AND `name` = "'.$name.'";');
			if(count($values) > 0) {
				$value = $values[0]['value'];
			}
			return $value;
		}
	}

?>
