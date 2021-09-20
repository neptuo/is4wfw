<?php

require_once(APP_SCRIPTS_PHP_PATH . "libs/BaseTagLib.class.php");
require_once(APP_SCRIPTS_PHP_PATH . "classes/utils/ArrayUtils.class.php");

class RoleCacheHelper extends BaseTagLib {
	
	public function refresh() {
		parent::dao('RoleCache')->truncate();
		$this->createFrom(1, 1);
	}
	
	public function is($sourceId, $targetId) {
		if ($sourceId == $targetId) {
			return true;
		}
	
		return parent::dao('RoleCache')->getBySourceTarget($sourceId, $targetId) != array();
	}
	
	public function getRoles($sourceId) {
		return parent::dao('RoleCache')->getBySource($sourceId);
		// $result = array();
		// $loaded = parent::dao('RoleCache')->getBySource($sourceId);
		// foreach($loaded as $item) {
			// $result[count($result)] = $item['target_id'];
		// }
		
		// return $result;
	}
	
	private function createFrom($sourceId, $parentId) {
		$roles = parent::dao('Role')->getChilds($parentId);
		foreach($roles as $role) {
			if(parent::dao('RoleCache')->getBySourceTarget($sourceId, $role['gid']) == array()) {
				parent::dao('RoleCache')->insert(array('source_id' => $sourceId, 'target_id' => $role['gid']));
			}
			$this->createFrom($sourceId, $role['gid']);
			$this->createFrom($role['gid'], $role['gid']);
		}
	}
}

class RoleHelper {
	private static function getInstance() {
		static $roleCacheHelper;
		
		if ($roleCacheHelper === null) {
			$roleCacheHelper = new RoleCacheHelper();
		}

		return $roleCacheHelper;
	}
	
	public static function refreshCache() {
		self::getInstance()->refresh();
	}
	
	public static function isInRole($sourceId, $targetId) {
		$instance = self::getInstance();
		if (is_array($sourceId)) {
			foreach ($sourceId as $sId) {
				if (is_array($targetId)) {
					foreach ($targetId as $tId) {			
						if ($instance->is($sId, $tId)) {
							return true;
						}
					}
				} else {
					if ($instance->is($sId, $targetId)) {
						return true;
					}
				}
			}
			return false;
		} else {
			if (is_array($targetId)) {
				foreach ($targetId as $tId) {			
					if ($instance->is($sourceId, $tId)) {
						return true;
					}
				}
			} else {
				return $instance->is($sourceId, $targetId);
			}
		}
	}
	
	public static function getRoles($groupIds) {
		$instance = self::getInstance();
		$result = array();
	
		if (is_array($groupIds)) {
			foreach ($groupIds as $group) {
				$result = array_merge($result, $instance->getRoles($group));
			}
		}
		
		$return = $groupIds;
		foreach ($result as $g) {
			$return[count($return)] = $g['target_id'];
		}
		
		return array_unique($return);
	}
	
	public static function getCurrentRoles() {
		$instance = self::getInstance();
		return self::getRoles($instance->login()->getGroupsIds());
	}
	
	public static function getUserRoles($uid) {
		$instance = self::getInstance();
		$sql = 'select `gid` from `user_in_group` where `uid` = '.$uid.';';
		$result = $instance->db()->getDataAccess()->fetchAll($sql);
		$return = array();
		foreach($result as $gp) {
			$return[count($return)] = $gp['gid'];
		}
		
		return self::getRoles($return);
	}
	
	public static function getRights($tableDesc, $objectId, $type = 0) {
		return self::getRights2($tableDesc[0], $tableDesc[1], $tableDesc[2], $tableDesc[3], $objectId, $type);
	}
	
	private static function arrayToString($value) {
		if (is_array($value)) {
			$value = implode(', ', $value);
		}

		return $value;
	}

	public static function getRights2($table, $objectColumn, $groupColumn, $typeColumn, $objectId, $type = 0) {
		$instance = self::getInstance();
		$instance->db()->getDataAccess()->disableCache();
		
		$objectId = RoleHelper::arrayToString($objectId);
		$sql = 'select `' . $objectColumn . '`, `' . $groupColumn . '`, `'  .$typeColumn . '` from `' . $table . '` where `' . $objectColumn . '` in (' . $objectId . ')' . ($type != 0 ? ' and `' . $typeColumn .'` = ' . $type : '').';';
		$result = $instance->db()->getDataAccess()->fetchAll($sql);
		
		$return = array();
		foreach ($result as $i => $res) {
			$return[$i] = $res['gid'];
		}
		
		$instance->db()->getDataAccess()->enableCache();
		return $return;
	}
	
	public static function setRights($tableDesc, $objectId, $allowedGroupIds, $newGroupIds, $type) {
		self::setRights2($tableDesc[0], $tableDesc[1], $tableDesc[2], $tableDesc[3], $objectId, $allowedGroupIds, $newGroupIds, $type);
	}
	
	public static function setRights2($table, $objectColumn, $groupColumn, $typeColumn, $objectId, $allowedGroupIds, $newGroupIds, $type) {
		$instance = self::getInstance();
		$sql = 'select `'.$objectColumn.'`, `'.$groupColumn.'`, `'.$typeColumn.'` from `'.$table.'` where `'.$objectColumn.'` = '.$objectId.' and `'.$typeColumn.'` = '.$type.';';
		$result = $instance->db()->getDataAccess()->fetchAll($sql/*, true, true, true*/);
		$perms = array();
		$instance->db()->getDataAccess()->transaction();
		foreach ($result as $perm) {
			$perms[] = $perm[$groupColumn];
			if (RoleHelper::isInRole($allowedGroupIds, $perm[$groupColumn])) {
				if (!in_array($perm[$groupColumn], $newGroupIds)) {
					$deleteSql = 'delete from `'.$table.'` where `'.$objectColumn.'` = '.$objectId.' and `'.$groupColumn.'` = '.$perm[$groupColumn].' and `'.$typeColumn.'` = '.$type.';';
					$instance->db()->getDataAccess()->execute($deleteSql/*, true, true, true*/);
				}
			}
		}
		foreach ($newGroupIds as $newId) {
			if (!in_array($newId, $perms)) {
				$insertSql = 'insert into `'.$table.'`(`'.$objectColumn.'`, `'.$groupColumn.'`, `'.$typeColumn.'`) values('.$objectId.', '.$newId.', '.$type.');';
				$instance->db()->getDataAccess()->execute($insertSql/*, true, true, true*/);
			}
		}
		$instance->db()->getDataAccess()->commit();
	}
	
	public static function deleteRights($tableDesc, $objectId) {
		self::deleteRights2($tableDesc[0], $tableDesc[1], $tableDesc[2], $tableDesc[3], $objectId);
	}
	
	public static function deleteRights2($table, $objectColumn, $groupColumn, $typeColumn, $objectId) {
		$instance = self::getInstance();
		$sql = 'delete from `'.$table.'` where `'.$objectColumn.'` = '.$objectId.';';
		$instance->db()->getDataAccess()->transaction();
		$instance->db()->getDataAccess()->execute($sql);
		$instance->db()->getDataAccess()->commit();
	}
	
	public static function getPermissionsOrDefalt($tableDesc, $objectId, $type, $altTableDesc = array(), $altObjectId = null) {
		return self::getPermissionsOrDefalt2($tableDesc[0], $tableDesc[1], $tableDesc[2], $tableDesc[3], $objectId, $type, $altTableDesc[0], $altTableDesc[1], $altTableDesc[2], $altTableDesc[3], $altObjectId);
	}
	
	public static function getPermissionsOrDefalt2($table, $objectColumn, $groupColumn, $typeColumn, $objectId, $type, 
	       $altTable = null, $altObjectColumn = null, $altGroupColumn = null, $altTypeColumn = null, $altObjectId = null) {
		   
		$instance = self::getInstance();
		$result = array();
		if ($objectId != '' && $objectId != 0 && $objectId > 0) {
			$sql = 'select `'.$objectColumn.'`, `'.$groupColumn.'`, `'.$typeColumn.'` from `'.$table.'` where `'.$objectColumn.'` = '.$objectId.' and `'.$typeColumn.'` = '.$type.';';
			$result = $instance->db()->getDataAccess()->fetchAll($sql);
		} elseif ($altObjectId != null && $altObjectId != '') {
			$sql = 'select `'.$altObjectColumn.'`, `'.$altGroupColumn.'`, `'.$altTypeColumn.'` from `'.$altTable.'` where `'.$altObjectColumn.'` = '.$altObjectId.' and `'.$altTypeColumn.'` = '.$type.';';
			$result = $instance->db()->getDataAccess()->fetchAll($sql);
		}
		
		$return = array();
		foreach ($result as $item) {
			$return[count($return)] = $item[$groupColumn];
		}
		
		return $return;
	}
	
	public static function canCurrentEditUser($uid) {
		$instance = self::getInstance();
		if ($instance->login()->getUserId() == $uid) {
			return true;
		}
	
		$current = self::getCurrentRoles();
		$target = self::getUserRoles($uid);
		
		$currentMax = $instance->db()->getDataAccess()->fetchSingle(self::getCurrentEditUserSql($current));

		if (count($target) > 0) {
			$targetMax = $instance->db()->getDataAccess()->fetchSingle(self::getCurrentEditUserSql($target));
		} else {
			$targetMax = 0;
		}
		
		return $currentMax['value'] < $targetMax['value'];
	}
	
	private static function getCurrentEditUserSql($roles) {
		return 'select min(`value`) as `value` from `group` where `gid` in ('.implode(',', $roles).');';
	}

	public static function existsSql($tableDesc, $joinExpression, $type) {
		return self::existsSql2($tableDesc[0], $tableDesc[1], $tableDesc[2], $tableDesc[3], $joinExpression, $type);
	}

	public static function existsSql2($table, $objectColumn, $groupColumn, $typeColumn, $joinExpression, $type) {
		$instance = self::getInstance();
		return 'exists(select * from `' . $table . '` r where r.`' . $objectColumn . '` = ' . $joinExpression . ' and r.`' . $typeColumn . '` = ' . $type . ' and r.`' . $groupColumn . '` in (' . $instance->login()->getGroupsIdsAsString() . '))';
	}

	public static function canUser($tableDesc, $objectId, $type) {
		return self::canUser2($tableDesc[0], $tableDesc[1], $tableDesc[2], $tableDesc[3], $objectId, $type);
	}

	public static function canUser2($table, $objectColumn, $groupColumn, $typeColumn, $objectId, $type) {
		$instance = self::getInstance();
		if (is_array($objectId)) {
			$ids = $objectId;
		} else {
			$ids = array($objectId);
		}
		
		$groupId = RoleHelper::arrayToString($instance->login()->getGroupsIds());
		$objectId = RoleHelper::arrayToString($objectId);
		$sql = 'select distinct r.`' . $objectColumn . '` as `object_id` from `' . $table . '` r left join `rolecache` rc on r.`' . $groupColumn . '` = rc.`target_id` where r.`' . $objectColumn . '` in (' . $objectId . ') and (rc.`source_id` in (' . $groupId . ') or r.`' . $groupColumn . '` in (' . $groupId . ')) and r.`' . $typeColumn . '` = ' . $type . ';';
		$result = $instance->db()->getDataAccess()->fetchAll($sql);
		$result = array_column($result, 'object_id');

		return ArrayUtils::isEqual($ids, $result);
	}
	
	/* ================== HTML ======================================================== */
	
	public static function getFormPart($tableDesc, $baseName, $objectId, $type, $altTableDesc = array(), $altObjectId = null) {
		return self::getFormPart2($tableDesc[0], $tableDesc[1], $tableDesc[2], $tableDesc[3], $baseName, $objectId, $type, $altTableDesc[0], $altTableDesc[1], $altTableDesc[2], $altTableDesc[3], $altObjectId);
	}
	
	public static function getFormPart2($table, $objectColumn, $groupColumn, $typeColumn, $baseName, $objectId, $type, 
	       $altTable = null, $altObjectColumn = null, $altGroupColumn = null, $altTypeColumn = null, $altObjectId = null) {
		   
		$instance = self::getInstance();
		$result = self::getPermissionsOrDefalt2($table, $objectColumn, $groupColumn, $typeColumn, $objectId, $type, $altTable, $altObjectColumn, $altGroupColumn, $altTypeColumn, $altObjectId);
		
		//$selected = array();
		//foreach($result as $perm) {
		//	$selected[count($selected)] = $perm[$groupColumn];
		//}
		
		return self::getFormPart3($baseName, self::getCurrentRoles(), $result, $type);
	}
	
	public static function getFormPart3($baseName, $availableGroups, $selectedGroups, $type) {
		$instance = self::getInstance();
		$return = '';
		$availableGroups = $instance->dao('Role')->getList(Select::factory($instance->db()->getDataAccess())->where('gid', 'IN', $availableGroups)->orderBy('name'));
		
		switch($type) {
			case WEB_R_READ: $return .= '<select id="'.$baseName.'r" name="'.$baseName.'r[]"'; break;
			case WEB_R_WRITE: $return .= '<select id="'.$baseName.'w" name="'.$baseName.'w[]"'; break;
			case WEB_R_DELETE: $return .= '<select id="'.$baseName.'d" name="'.$baseName.'d[]"'; break;
		}
		$return .= ' multiple="multiple" size="5">';
		
		//print_r($selectedGroups);
		foreach($availableGroups as $group) {
			$return .= '<option'.((in_array($group['gid'], $selectedGroups)) ? ' selected="selected"' : '').' value="'.$group['gid'].'">'.$group['name'].'</option>';
		}
		$return .= '</select>';
		
		return $return;
	}
}


?>