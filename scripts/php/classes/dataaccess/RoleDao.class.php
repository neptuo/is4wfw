<?php

require_once('DataAccess.class.php');
require_once('AbstractDao.class.php');
require_once('Select.class.php');

class RoleDao extends AbstractDao {
	
	public static function getTableName() {
		return "group";
	}
	
	public static function getTableAlias() {
		return 'r';
	}
	
	public static function getFields() {
		return array("gid", "parent_gid", "name", "value");
	}
	
	public static function getIdField() {
		return "gid";
	}
	
	
	public function getChilds($parentId) {
		return parent::getList(Select::factory(self::dataAccess())->where('parent_gid', '=', $parentId));
	}
}

?>