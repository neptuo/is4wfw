<?php

	require_once("BaseTagLib.class.php");
	require_once(APP_SCRIPTS_PHP_PATH . "classes/RoleHelper.class.php");
	require_once(APP_SCRIPTS_PHP_PATH . "classes/LocalizationBundle.class.php");
	require_once(APP_SCRIPTS_PHP_PATH . "classes/manager/GitHubReleaseManager.class.php");
	require_once(APP_SCRIPTS_PHP_PATH . "classes/manager/RecursiveZipArchive.class.php");
	require_once(APP_SCRIPTS_PHP_PATH . "classes/ui/BaseGrid.class.php");
	require_once(APP_SCRIPTS_PHP_PATH . "classes/ui/Formatter.class.php");
  
	/**
	 * 
	 *  Class System.
	 *  Management of web framework.	     
	 *      
	 *  @author     Marek SMM
	 *  @timestamp  2011-08-30
	 * 
	 */  
	class System extends BaseTagLib {

		public function __construct() {
			self::setTagLibXml("System.xml");
			self::setLocalizationBundle("system");
		}

		public $UserSettingsEditors = array(
			'tiny' => 'Wysiwyg',
			'edit_area' => 'Editor kódu',
			'' => 'Obyčejný'
		);

		public $UserSettings = array(
			array(
				'name' => 'Admin.Language',
				'type' => 'dropdown',
				'values' => array('cs' => 'Čeština', 'en' => 'English')
			),
			array(
				'name' => 'Article.editAreaHeadRows',
				'type' => 'number',
				'values' => array(1, 100)
			),
			array(
				'name' => 'Article.editors',
				'type' => 'dropdown',
				'values' => array(
					'tiny' => 'Wysiwyg',
					'edit_area' => 'Editor kódu',
					'' => 'Obyčejný'
				)
			),
			array(
				'name' => 'Article.languageId',
				'type' => 'number'
			),
			array(
				'name' => 'Login.session',
				'type' => 'number',
				'values' => array(10,30)
			),
			array(
				'name' => 'Page.editors',
				'type' => '',
				'values' => array()
			),
			array(
				'name' => 'WebProject.defaultProjectId',
				'type' => 'number'
			),
			array(
				'name' => '',
				'type' => '',
				'values' => array()
			)
		);

		public function manageProperties($userId = false, $useFrames = false, $showMsg = false) {
			parent::setLocalizationBundle('system');

			$title = '';
			if ($userId == '' || $userId == 'current') {
				$userId = parent::login()->getUserId();
				$title = parent::rb()->get('personalproperties.title');
			} else if ($userId == '0' || $userId == 'default') {
				$userId = 0;
				$title = parent::rb()->get('personalproperties.defaulttitle');
			} else {
				$title = parent::rb()->get('personalproperties.title');
			}

			$return = '';
			$typeId = 1;
			
			if($_POST['system-properties-save'] == parent::rb()->get('personalproperties.save')) {
				foreach($_POST['system-property-name'] as $id => $name) {
					$value = $_POST['system-property-value'][$id];
					parent::db()->execute('UPDATE `personal_property` SET `name` = "'.$name.'", `value` = "'.$value.'" WHERE `id` = '.$id.';');
				}
				
				if($_POST['system-property-name-new'] != '') {
					$name = $_POST['system-property-name-new'];
					$value = $_POST['system-property-value-new'];
					parent::db()->execute('INSERT INTO `personal_property` (`name`, `value`, `type`, `user_id`) VALUES ("'.$name.'", "'.$value.'", '.$typeId.', '.$userId.');');
				}
			} elseif($_POST['system-properties-delete'] == parent::rb()->get('personalproperties.delete')) {
				foreach($_POST['system-properties-delete-item'] as $id => $val) {
					parent::db()->execute('DELETE FROM `personal_property` WHERE `id` = '.$id.' AND `user_id` = '.$userId.';');
				}
			}
			
			$properties = parent::db()->fetchAll('SELECT `id`, `name`, `value` FROM `personal_property` WHERE `user_id` = '.$userId.' AND `type` = '.$typeId.' ORDER BY `name`;');
			
			$return .= '' 
			.'<div class="system-properties">'
				.'<form name="system-properties" method="post" action="'.$_SERVER['REQUEST_URI'].'">'
					.'<table>'
						.'<tr>'
							.'<th class="system-properties-name">' . parent::rb()->get('personalproperties.name') . ':</th>'
							.'<th class="system-properties-value">' . parent::rb()->get('personalproperties.value') . ':</th>'
							.'<th class="system-properties-delete">' . parent::rb()->get('personalproperties.select') . ':</th>';
			
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
					.'<div class="system-properties-submit gray-box">'
						.'<input type="submit" name="system-properties-save" value="' . parent::rb()->get('personalproperties.save') . '" /> '
						.'<input type="submit" name="system-properties-delete" value="' . parent::rb()->get('personalproperties.delete') . '" />'
					.'</div>'
				.'</form>'
			.'</div>';
			
			if($useFrames == "false") {
				return $return;
			} else {
				return parent::getFrame($title, $return, "", true);
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
			
			if($_POST['system-note-save'] == 'Save') {
				foreach($_POST['system-note-value'] as $id => $name) {
					$type = $_POST['system-note-type'][$id];
					$dbObject->execute('UPDATE `personal_note` SET `value` = "'.$name.'", `type` = '.$type.' WHERE `id` = '.$id.';');
				}
				
				if($_POST['system-note-value-new'] != '') {
					$value = $_POST['system-note-value-new'];
					$type = $_POST['system-note-type-new'];
					$dbObject->execute('INSERT INTO `personal_note` (`value`, `type`, `user_id`) VALUES ("'.$value.'", '.$type.', '.$userId.');');
				}
			} elseif($_POST['system-note-delete'] == 'Delete selected') {
				foreach($_POST['system-note-delete-item'] as $id => $val) {
					$dbObject->execute('DELETE FROM `personal_note` WHERE `id` = '.$id.' AND `user_id` = '.$userId.';');
				}
			}
			
			$properties = $dbObject->fetchAll('SELECT `id`, `value`, `type` FROM `personal_note` WHERE `user_id` = '.$userId.' ORDER BY `id`;');
			
			$return .= '' 
			.'<div class="system-notes">'
				.'<form name="system-note" method="post" action="'.$_SERVER['REQUEST_URI'].'">'
					.'<table>'
						.'<tr>'
							.'<th class="system-note-value">Value:</th>'
							.'<th class="system-note-delete">Type:</th>'
							.'<th class="system-note-delete">Delete:</th>';
			$i = 0;
			foreach($properties as $prop) {
				$return .= ''
						.'<tr class="'.(($i % 2) == 1 ? 'even' : 'idle').'">'
							.'<td>'
								.'<input type="text" value="'.$prop['value'].'" name="system-note-value['.$prop['id'].']" id="system-note-value-'.$prop['id'].'" />'
							.'</td>'
							.'<td>'
								.'<select name="system-note-type['.$prop['id'].']">'
									.'<option value="1"'.($prop['type'] == 1 ? ' selected="selected"' : '').'>Note</opion>'
									.'<option value="2"'.($prop['type'] == 2 ? ' selected="selected"' : '').'>Warning</opion>'
									.'<option value="3"'.($prop['type'] == 3 ? ' selected="selected"' : '').'>Error</opion>'
									.'<option value="4"'.($prop['type'] == 4 ? ' selected="selected"' : '').'>Fatal</opion>'
									.'<option value="5"'.($prop['type'] == 5 ? ' selected="selected"' : '').'>Success</opion>'
								.'</select>'
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
							.'<td>'
								.'<select name="system-note-type-new">'
									.'<option value="1">Note</opion>'
									.'<option value="2">Warning</opion>'
									.'<option value="3">Error</opion>'
									.'<option value="4">Fatal</opion>'
									.'<option value="5">Success</opion>'
								.'</select>'
							.'</td>'
							.'<td>&nbsp;</td>'
						.'</tr>'
					.'</table>'
					.'<div class="system-note-submit gray-box">'
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
			$types = array('', 'note', 'warning', 'error', 'fatal', 'success');
			
			$properties = $dbObject->fetchAll('SELECT `value`, `type` FROM `personal_note` WHERE `user_id` = '.$userId.' ORDER BY `id`;');
			
			$return .= '' 
			.'<div class="system-note-print">';
			$i = 0;
			foreach($properties as $prop) {
				$return .= ''
						.'<h4 class="system-note-item system-note-item-'.$i.' system-note-item-'.($i % 2 == 0 ? "idle" : "even").' '.$types[$prop['type']].'">'.$prop['value'].'</h4>';
				$i ++;
			}
			$return .= ''
			.'</div>';
			
			if($useFrames == "false") {
				return $return;
			} else {
				return parent::getFrame('System notes print', $return, "", true);
			}
		}
		
		public function adminMenuItem() {
			global $dbObject;
			global $webObject;
			$return  = '';
			
			$id = $_GET['adminId'];
			$data = $dbObject->fetchSingle('select `page_id` from `system_adminmenu` where `id` = '.$id.';');
			if($data != array()) {
				$return .= '<iframe style="border: none;" src="'.$data['page_id'].'"></iframe>';
				$return .= ''
				.'<script type="text/javascript">'
					.'$(function() {'
						.'$("iframe").each(function() {'
							.'$(this).width($(this).parent().width()).load(function() {'
								.'$(this).height($(this).document().height);'
							.'});'
						.'})'
					.'})'
				.'</script>';
			}
			
			return $return;
		}
		
		public function adminMenu($url, $classes = false) {
			global $dbObject;
			global $webObject;
			$inn = 1;
			$data = $dbObject->fetchAll('select `id`, `name`, `icon`, `perm` from `system_adminmenu` order by `id`');
		
			$content .= '<div class="menu menu-' . $inn . ((strlen($classes) > 0) ? ' ' . $classes : '') . '"><ul class="ul-' . $inn . '">';
			$i = 1;
			foreach ($data as $lnk) {
				if ($lnk['perm'] != '') {
					global $loginObject;
					$perm = $loginObject->getGroupPerm($lnk['perm'], $loginObject->getMainGroupId(), false, 'false');
					if($perm['value'] != 'true') {
						continue;
					}
				}
			
				$active = false;
				$href = $url . '?adminId=' . $lnk['id'];
				if ($href == '/' . $_REQUEST['WEB_PAGE_PATH']) {
					$active = true;
				}

				$content .= ''
						. '<li class="menu-item li-' . $i . (($active) ? ' active-item' : '') . ' ' . '">'
						. '<div class="link' . (($parent) ? ' active-parent-link' : '') . (($active) ? ' active-link' : '') . '">'
						. '<a href="' . $href . '"' . (($rel != false) ? ' rel="' . $rel . '"' : '') . ' style="background-image: url(\''.$lnk['icon'].'\') !important;">'
						. '<span>' . $lnk['name'] . '</span>'
						. '</a>'
						. '</div>'
						. '</li>';
				$i++;
			}
			$inner--;
			$content .= "</ul></div>";
			
			return $content;
		}
		
		public function editAdminMenu($useFrames = false, $shpwMsg = false) {
			global $dbObject;
			global $loginObject;
			$return = '';
			
			if($_POST['adminmenu-delete'] == "Delete") {
				$id = $_POST['adminmenu-id'];
				$dbObject->execute('delete from `system_adminmenu` where `id` = '.$id.';');
				$return = parent::getSuccess('Admin menu item with id '.$id.' deleted.');
			}
			
			if($_POST['adminmenu-save'] == "Save") {
				$data = array();
				$data['id'] = $_POST['adminmenu-id'];
				$data['name'] = $_POST['adminmenu-name'];
				$data['page_id'] = $_POST['adminmenu-pageid'];
				$data['icon'] = $_POST['adminmenu-icon'];
				$data['perm'] = $_POST['adminmenu-perm'];
				
				if($data['id'] == "") {
					$dbObject->execute('insert into `system_adminmenu`(`name`, `page_id`, `icon`, `perm`) values ("'.$data['name'].'", "'.$data['page_id'].'", "'.$data['icon'].'", "'.$data['perm'].'");');
					$return = parent::getSuccess('Admin menu item with name '.$name.' save.');
				} else {
					$dbObject->execute('update `system_adminmenu` set `name` = "'.$data['name'].'", `page_id` = "'.$data['page_id'].'", `icon` = "'.$data['icon'].'", `perm` = "'.$data['perm'].'" where `id` = '.$data['id'].';');
					$return = parent::getSuccess('Admin menu item with name '.$name.' updated.');
				}
			}
			
			if($_POST['adminmenu-new'] == "Create" || $_POST['adminmenu-edit'] == "Edit") {
				$data = array();
				if($_POST['adminmenu-edit'] == "Edit") {
					$id = $_POST['adminmenu-id'];
					$data = $dbObject->fetchSingle('select `id`, `name`, `page_id`, `icon`, `perm` from `system_adminmenu` where `id` = '.$id.';');
				}
				
				$return .= ''
				.'<form method="post" action="'.$_SERVER['REQUEST_URI'].'">'
					.'<div class="gray-box">'
						.'<label for="adminmenu-name" class="w160">Name:</label>'
						.'<input type="text" name="adminmenu-name" id="adminmenu-name" value="'.$data['name'].'" class="w160" />'
					.'</div>'
					.'<div class="gray-box">'
						.'<label for="adminmenu-pageid" class="w160">Page id (url):</label>'
						.'<input type="text" name="adminmenu-pageid" id="adminmenu-pageid" value="'.$data['page_id'].'" class="w400" />'
					.'</div>'
					.'<div class="gray-box">'
						.'<label for="adminmenu-icon" class="w160">Icon:</label>'
						.'<input type="text" name="adminmenu-icon" id="adminmenu-icon" value="'.$data['icon'].'" class="w400" />'
					.'</div>'
					.'<div class="gray-box">'
						.'<label for="adminmenu-perm" class="w160">Permission:</label>'
						.'<input type="text" name="adminmenu-perm" id="adminmenu-perm" value="'.$data['perm'].'" class="w200" />'
					.'</div>'
					.'<div class="gray-box">'
						.'<input type="hidden" name="adminmenu-id" value="'.$data['id'].'" />'
						.'<input type="submit" name="adminmenu-save" value="Save" /> '
						.'<input type="submit" name="adminmenu-cancel" value="Cancel" />'
					.'</div>'
				.'</form>'
				.'<hr />';
			}
			
			$data = $dbObject->fetchAll('select `id`, `name`, `page_id`, `icon`, `perm` from `system_adminmenu` order by `id`');
			if(count($data) > 0) {
				$return .= ''
				.'<div class="system-adminmenu">'
					.'<table class="standart clickable">'
						.'<tr>'
							.'<th>Name</th>'
							.'<th>Page id (url)</th>'
							.'<th>Icon</th>'
							.'<th>Permission</th>'
							.'<th></th>'
						.'</tr>';
				$i = 1;
				foreach($data as $item) {
					$return .= ''
					.'<tr class="'.((($i % 2) == 0) ? 'even' : 'idle').'">'
						.'<td>'.$item['name'].'</td>'
						.'<td>'.$item['page_id'].'</td>'
						.'<td>'.$item['icon'].'</td>'
						.'<td>'.$item['perm'].'</td>'
						.'<td>'
							.'<form method="post" action="'.$_SERVER['REQUEST_URI'].'">'
								.'<input type="hidden" name="adminmenu-id" value="'.$item['id'].'" />'
								.'<input type="hidden" name="adminmenu-edit" value="Edit" />'
								.'<input type="image" src="~/images/page_edi.png" name="adminmenu-edit" value="Edit" title="Edit admin menu item, id '.$item['id'].'" />'
							.'</form> '
							.'<form method="post" action="'.$_SERVER['REQUEST_URI'].'">'
								.'<input type="hidden" name="adminmenu-id" value="'.$item['id'].'" />'
								.'<input type="hidden" name="adminmenu-delete" value="Delete" />'
								.'<input type="image" src="~/images/page_del.png" class="confirm" name="adminmenu-delete" value="Delete" title="Delete admin menu item, id '.$item['id'].'" />'
							.'</form>'
						.'</td>'
					.'</tr>';
					$i++;
				}
				$return .= '</table></div>';
			} else {
				$return .= parent::getWarning("No admin menu items.");
			}

			$return .= ''
			.'<hr />'
			.'<div class="gray-box">'
				.'<form method="post" action="'.$_SERVER['REQUEST_URI'].'">'
					.'<input type="submit" name="adminmenu-new" value="Create" title="Create admin menu item" />'
				.'</form>'
			.'</div>';
			
			if($useFrames == "false") {
				return $return;
			} else {
				return parent::getFrame('Admin menu', $return, "", true);
			}
		}

		public function hasAdminMenu() {
			$data = parent::db()->fetchSingle('select count(`id`) as `count` from `system_adminmenu`;');
			return $data['count'] > 0;
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
			if(count($conns)) {
				$return .= ''
				.'<div class="db-connection standart clickable">'
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
								.'<form name="db-connection-edit" method="post" action="'.$_SERVER['REQUEST_URI'].'">'
									.'<input type="hidden" name="db-connection-conn-id" value="'.$conn['id'].'" />'
									.'<input type="hidden" name="db-connection-conn-edit" value="Edit connection" />'
									.'<input type="image" src="~/images/page_edi.png" name="db-connection-conn-edit" value="Edit connection" title="Edit connection, id '.$conn['id'].'" />'
								.'</form> '
								.'<form name="db-connection-delete" method="post" action="'.$_SERVER['REQUEST_URI'].'">'
									.'<input type="hidden" name="db-connection-conn-id" value="'.$conn['id'].'" />'
									.'<input type="hidden" name="db-connection-conn-delete" value="Delete connection" />'
									.'<input class="confirm" type="image" src="~/images/page_del.png" name="db-connection-conn-delete" value="Delete connection" title="Delete connection, id '.$conn['id'].'" />'
								.'</form>'
							.'</td>'
						.'</tr>';
					$i ++;
				}
			} else {
				$return .= parent::getWarning('No connections.');
			}
			$return .= ''
				.'</table>'
				.'<hr />'
				.'<div class="gray-box">'
					.'<form name="db-connection-new" method="post" action="'.$_SERVER['REQUEST_URI'].'">'
						.'<span>Create new database connection</span> '
						.'<input type="hidden" name="db-connection-conn-new" value="Create connection" />'
						.'<input type="image" src="~/images/page_add.png" name="db-connection-conn-new" value="Create connection" title="Create connection" />'
					.'</form>'
				.'</div>'
			.'</div>';
			
		
			if($useFrames == "false") {
				return $return;
			} else {
				if($return != '') {
					return parent::getFrame('List Database Connections', $return, "", true);
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
						$id = $connnew[0]['id'];
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
				.'<form name="db-connection-edit" method="post" action="'.$_SERVER['REQUEST_URI'].'">'
					.parent::getWarning('Passwords are stored and displayed as plain text!')
					.'<div class="gray-box">'
						.'<label for="db-connection-name" class="w160">Name:</label>'
						.'<input type="text" id="db-connection-name" name="db-connection-name" value="'.$name.'" />'
					.'</div>'
					.'<div class="gray-box">'
						.'<label for="db-connection-hostname" class="w160">Hostname:</label>'
						.'<input type="text" id="db-connection-hostname" name="db-connection-hostname" value="'.$hostname.'" class="w200" />'
					.'</div>'
					.'<div class="gray-box">'
						.'<label for="db-connection-user" class="w160">Username:</label>'
						.'<input type="text" id="db-connection-user" name="db-connection-user" value="'.$user.'" />'
					.'</div>'
					.'<div class="gray-box">'
						.'<label for="db-connection-password" class="w160">Password:</label>'
						.'<input type="text" id="db-connection-password" name="db-connection-password" value="'.$password.'" />'
					.'</div>'
					.'<div class="gray-box">'
						.'<label for="db-connection-database" class="w160">Database:</label>'
						.'<input type="text" id="db-connection-database" name="db-connection-database" value="'.$database.'" />'
					.'</div>'
					.'<div class="gray-box">'
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
		
		public function manageRoleCache($buttonOnly = false, $useFrames = true) {
			$return = '';
			
			if($_POST['rolecache-refresh'] == 'Refresh role cache') {
				RoleHelper::refreshCache();
				$return .= parent::getSuccess('Refreshed');
			}
			
			if($buttonOnly) {
				return $return.parent::view('system-rolecache-button', array());
			}
			
			$dataModel = array('items' => parent::dao('RoleCache')->getList());
			
			if($useFrames) {
				return parent::getFrame('Role cache', $return.parent::view('system-rolecache', $dataModel), "", true);
			} else {
				return $return.parent::view('system-rolecache', $dataModel);
			}
		}

		private function getUpdateFormHtml($version) {
			return ''
			. '<form method="post">'
				. '<input type="hidden" name="update-version" value="' . $version . '" />'
				. '<input type="hidden" name="update" value="update" />'
				. '<input type="image" class="confirm" title="' . 'Update application to version ' . $version . '" src="~/images/icons/arrow_right.png" />'
			. '</form>';
		}

		private function mapReleaseToGrid($release, $type) {
			$release['version'] = Version::toString($release['version']);
			$release['published_at'] = str_replace("T", " ", str_replace("Z", "", $release['published_at']));
			$release['form'] = self::getUpdateFormHtml($release['version']);
			$release['version'] = '<a href="' . $release['html_url'] . '" target="_blank">' . $release['version'] . '</a>';

			if ($type == 'patch') {
				if (array_key_exists('patch', $release['download'])) {
					$release['download_size'] = $release['download']['patch']['size'];
					$release['type'] = 'Patch';
				} else {
					$release['download_size'] = $release['download']['full']['size'];
					$release['type'] = 'Full';
				}
			} else {
				$release['download_size'] = $release['download']['full']['size'];
				$release['type'] = 'Full';
			}

			$release['download_size'] = Formatter::toByteString($release['download_size']);
			return $release;
		}

		public function versionList() {
			$return = '';

			$api = new GitHubReleaseManager();
			$result = $api->getList();

			if ($result['result']) {
				$current = Version::parse(WEB_VERSION);
				$data = $result['data'];

				if ($_POST['update'] == 'update') {
					$updated = self::updateToVersion($_POST['update-version'], $data);
					if ($updated['result']) {
						self::redirectToSelf();
					} else {
						$return .= self::getError('Error occured during update: ' . $updated['log']);
					}
				}

				$newerMajors = self::getNewerMajorReleases($data, $current);
				$newerPatches = self::getNewerPatchReleases($data, $current);

				$majorHtml = '';
				$patchHtml = '';

				if (count($newerMajors) == 0) {
					$majorHtml .= self::getSuccess('You are running latest major version.');
				} else {
					$grid = new BaseGrid();
					$grid->addClass('clickable');
					$grid->setHeader(
						array(
							'version' => 'Version', 
							'published_at' => 'Published at', 
							'download_size' => 'Size', 
							'type' => 'Type', 
							'form' => ''
						)
					);

					foreach ($newerMajors as $release) {
						$release = self::mapReleaseToGrid($release, 'major');
						$grid->addRow($release);
					}

					$majorHtml .= $grid->render();
				}

				if (count($newerPatches) == 0) {
					$patchHtml .= self::getSuccess('You are running latest patch for your major version.');
				} else {
					$grid = new BaseGrid();
					$grid->addClass('clickable');
					$grid->setHeader(
						array(
							'version' => 'Version', 
							'published_at' => 'Published at', 
							'download_size' => 'Size', 
							'type' => 'Type', 
							'form' => ''
						)
					);

					foreach ($newerPatches as $release) {
						$release = self::mapReleaseToGrid($release, 'patch');
						$grid->addRow($release);
					}
						
					$patchHtml .= $grid->render();
				}

				$note = '';

				if (IS_DEVELOPMENT_MODE) {
					$note .= self::getWarning('In development mode, app folder is read-only and you can\'t update the application');
				}

				$return .= ''
				. $note
				. '<div class="grid-2">'
					. '<div class="gray-box">'
						. '<strong>' . 'Major Update' . '</strong>'
					. '</div>'
					. '<div class="gray-box">'
						. $majorHtml
					. '</div>'
				. '</div>'
				. '<div class="grid-2">'
					. '<div class="gray-box">'
						. '<strong>' . 'Patch Update' . '</strong>'
					. '</div>'
					. '<div class="gray-box">'
						. $patchHtml
					. '</div>'
				. '</div>'
				. '<div class="clear"></div>';
			} else {
				$return .= self::getError($result['log']);
			}

			return $return;
		}

		public function updateToVersion($target, $releases) {
			$result = array('result' => false, 'log' => '');

			$target = Version::parse($target);
			foreach ($releases as $release) {
				if ($target['major'] == $release['version']['major'] && $target['patch'] == $release['version']['patch']) {
					$api = new GitHubReleaseManager();

					$current = Version::parse(WEB_VERSION);
					
					$type = null;
					$downloadUrl = null;
					if ($target['major'] == $current['major'] && $target['patch'] > $current['patch']) {
						if (array_key_exists('patch', $release['download'])) {
							$type = 'patch';
							$downloadUrl = $release['download']['patch']['url'];
						} else {
							$type = 'full';
							$downloadUrl = $release['download']['full']['url'];
						}
					} else if ($target['major'] > $current['major']) {
						$type = 'full';
						$downloadUrl = $release['download']['full']['url'];
					}

					if ($type == null) {
						$result['log'] .= "Wrong version '" . Version::toString($target) . "'.";
						return $result;
					}

					$targetPath = INSTANCE_PATH;
					$currentFileName = $targetPath . self::getUpdateFileName($current);
					$targetFileName = $targetPath . self::getUpdateFileName($target);
					
					if ($type == 'full') {
						$zip = new RecursiveZipArchive();
						if ($zip->open($currentFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
							$zip->addDir(APP_PATH, basename(APP_PATH));
							$zip->close();
						} else {
							$result['log'] .= 'Unnable to create backup zip.';
							return $result;
						}
					}

					$api->download($downloadUrl, $targetFileName);
					if (!file_exists($targetFileName)) {
						$result['log'] .= 'Unnable to download zip archive with update.';
						return $result;
					}

					if ($type == 'full') {
						self::removeDirectory(APP_PATH);
					}
					
					if (self::zipExtract($targetFileName, $targetPath)) {
						unlink($targetFileName);
						if ($type == 'full') {
							unlink($currentFileName);
						}
						
						$result['result'] = true;
						return $result;
					} else {
						unlink($targetFileName);
						if ($type == 'full') {
							self::removeDirectory(APP_PATH);
							self::zipExtract($currentFileName, $targetPath);
						}

						$result['log'] .= 'Unnable to extract zip archive with update.';
						return $result;
					}
				}
			}

			$result['log'] .= 'Unnable to find release for version ' . Version::toString($target) . '.';
			return $result;
		}

		private function getUpdateFileName($version) {
			return 'phpwfw-' . Version::toString($version) . '.zip';
		}

		private function getNewerMajorReleases($data, $current) {
			$result = array();
			foreach ($data as $release) {
				if ($release['version']['major'] > $current['major']) {
					$isUsed = false;

					foreach ($result as $i => $existing) {
						if ($existing['version']['major'] == $release['version']['major']) {
							if ($existing['version']['patch'] < $release['version']['patch']) {
								$result[$i] = $release;
							}

							$isUsed = true;
							break;
						}
					}

					if (!$isUsed) {
						$result[] = $release;
					}
				}
			}

			return $result;
		}

		private function getNewerPatchReleases($data, $current) {
			$result = array();
			foreach ($data as $release) {
				if ($release['version']['major'] == $current['major'] && $release['version']['patch'] > $current['patch']) {
					$result[] = $release;
				}
			}

			return $result;
		}

		public function repositoryLink($linkText) {
			return '<a target="_blank" href="https://github.com/maraf/PHP-WebFramework">' . $linkText . '</a>';
		}

		public function repositoryIssueCreateLink($linkText) {
			return '<a target="_blank" href="https://github.com/maraf/PHP-WebFramework/issues/new">' . $linkText . '</a>';
		}

		/* ---------- PROPERTIES ---------------------- */
		
		public function setCmsWindowsStyle($value) {
			// not implemented	
		}
		
		public function getCmsWindowsStyle() {
			$val = self::getPropertyValue("System.cms.windowsstyle");
			if ($val == "true") {
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
