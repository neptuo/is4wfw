<?php

	class UniversalPermission {
		private static $tableName = 'universal_permission';
		
		/**
		 *
		 *	Returns universal_permissions
		 *	
		 *	@param	disciminator		value for discriminator column (max 10 chars!!)
		 *	@param	objectId				object id
		 *	@param	type						permission type, 0 for all
		 *	@return									array of group names		 
		 *
		 */		 		 		 		
		public static function getPermissions($discriminator, $objectId, $type = 0) {
			$sql = 'select `name` from `'.UniversalPermission::$tableName.'` as `up` left join `group` on `up`.`group_id` = `group`.`gid` where `discriminator` = "'.$discriminator.'" and `object_id` = '.$objectId.''.(($type == WEB_R_READ || $type == WEB_R_WRITE || $type == WEB_R_DELETE) ? ' and `type` = '.$type : '').';';
			$rows = self::db()->fetchAll($sql);
			if(count($rows) > 0) {
				$ret = array();
				foreach($rows as $row) {
					$ret[] = $row['name'];
				}
				return $ret;
			} else {
				return array();
			}
		}
		
		/**
		 *	
		 *	Writes universal_permissions
		 *	
		 *	@param	discriminator		value for discriminator column (max 10 chars!!)
		 *	@param	objectId				object id
		 *	@param	groups					array of group ids
		 *	@param	type						permission type
		 *	@param	override				if true(default), deletes current permission for 'discriminator' and 'objectId'
		 *
		 */		 		 		 		 		
		public static function setPermissions($discriminator, $objectId, $groups, $type, $override = true) {
			if($override == true) {
				self::deletePermissions($discriminator, $objectId, $type);
			}
			/*$ids = self::db()->fetchAll('select distinct `gid` from `group` where `name` in ('.self::intArrayToString($groups).');', true, true, true);
			$values = '';
			for($i = 0; $i < count($ids); $i ++) {
				$id = $ids[$i];
				$group = $groups[$i];
				if($values != '') {
					$values .= ', ';
				}
				$values .= ' ("'.$discriminator.'", '.$objectId.', '.$id['gid'].', '.$group['type'].')';
			}*/
			$values = '';
			foreach($groups as $group) {
				if($values != '') {
					$values .= ',';
				}
				$values .= ' ("'.$discriminator.'", '.$objectId.', '.$group.', '.$type.')';
			}
			self::db()->execute('insert into `'.self::$tableName.'`(`discriminator`, `object_id`, `group_id`, `type`) values '.$values.';');
		}
		
		public static function deletePermissions($discriminator, $objectId, $type = 0) {
			self::db()->execute('delete from `'.UniversalPermission::$tableName.'` where `discriminator` = "'.$discriminator.'" and `object_id` = '.$objectId.''.($type != 0 ? ' and `type` = '.$type : '').';');
		}
		
		/**
		 *
		 *	Shows part of form for setting permission
		 *	
		 *  @param	discriminator		value for discriminator column (max 10 chars!!)
		 *	@param	objectId				object id, 'new' for new row (so searching for selected groups)
		 *	@param	groups					two dimensional array containing at every row pair of 'gid' = group id, 'name' = group name
		 *	@param	type						permission type, 0 for all
		 *
		 */		 		 		 		
		public static function showPermissionsFormPart($discriminator, $objectId, $groups, $type) {
			$return = '';
			$userGroupIdsAsString = self::string2DArrayToStringOfIds($groups);
			$avGroups = self::db()->fetchAll('SELECT `gid`, `name` FROM `group` WHERE (`group`.`gid` IN ('.$userGroupIdsAsString.') OR `group`.`parent_gid` IN ('.$userGroupIdsAsString.')) ORDER BY `value`;');
			
			if($objectId != 'new') { 
				$selGroups = self::getPermissions($discriminator, $objectId, $type);
			} else {
				$selGroups = array();
			}
			
			$name = 'universal-permissions-'.$discriminator.'-';
			switch($type) {
				case WEB_R_READ: $return .= '<select id="'.$name.'r" name="'.$name.'r[]" multiple="multiple" size="5">'; break;
      	case WEB_R_WRITE: $return .= '<select id="'.$name.'w" name="'.$name.'w[]" multiple="multiple" size="5">'; break;
      	case WEB_R_DELETE: $return .= '<select id="'.$name.'d" name="'.$name.'d[]" multiple="multiple" size="5">'; break;
      }
      foreach($avGroups as $group) {
      	$return .= '<option'.((in_array($group['name'], $selGroups)) ? ' selected="selected"' : '').' value="'.$group['gid'].'">'.$group['name'].'</option>';
      }
      $return .= '</select>';
      
      return $return;
		}
		
		/**
		 *
		 *	Saves permissions from submitted form
		 *
		 */		 		 		 		
		public static function savePermissionsFromForm($discriminator, $objectId, $type) {
			$name = 'universal-permissions-'.$discriminator.'-';
			switch($type) {
				case WEB_R_READ: $name .= 'r'; break;
				case WEB_R_WRITE: $name .= 'w'; break;
				case WEB_R_DELETE: $name .= 'd'; break;
      }
			$values = $_POST[$name];
			UniversalPermission::setPermissions($discriminator, $objectId, $values, $type);
		}
		
		public static function checkUserPermissions($discriminator, $objectId, $type) {
			global $loginObject;
			$perms = UniversalPermission::getPermissions($discriminator, $objectId, $type);
			$ok = false;
			//echo 'Type: '.$type.' - ';
			//print_r($perms);
			//print_r($loginObject->getGroups());
			foreach($loginObject->getGroups() as $ugp) {
				if(in_array($ugp['name'], $perms)) {
					$ok = true;
					break;
				}
			}
			return $ok;
		}
		
		private static function db() {
			global $dbObject;
			return $dbObject;
		}
		
		private static function string2DArrayToString($values) {
			$ret = '';
			foreach($values as $value) {
				if($ret == '') {
					$ret = '"'.$value['group'].'"';
				} else {
					$ret .= ', "'.$value['group'].'"';
				}
			}
			return $ret;
		}
		
		private static function string2DArrayToStringOfIds($values) {
			$ret = '';
			foreach($values as $value) {
				if($ret == '') {
					$ret = $value['gid'];
				} else {
					$ret .= ', '.$value['gid'];
				}
			}
			return $ret;
		}
		
		private static function stringArrayToString($values) {
			$ret = '';
			foreach($values as $value) {
				if($ret == '') {
					$ret = '"'.$value.'"';
				} else {
					$ret .= ', "'.$value.'"';
				}
			}
			return $ret;
		}
		
		private static function intArrayToString($values) {
			$ret = '';
			foreach($values as $value) {
				if($ret == '') {
					$ret = $value;
				} else {
					$ret .= ', '.$value;
				}
			}
			return $ret;
		}
		
		private static function composeFormElementName($discriminator, $type) {
			$name = 'universal-permissions-'.$discriminiator.'-';
			switch($type) {
				case WEB_R_READ: $name .= 'r'; break;
      	case WEB_R_WRITE: $name .= 'w'; break;
      	case WEB_R_DELETE: $name .= 'd'; break;
      }
		}
	}

?>
