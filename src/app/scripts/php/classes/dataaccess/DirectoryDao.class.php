<?php

require_once('DataAccess.class.php');
require_once('AbstractDao.class.php');
require_once('Select.class.php');

class DirectoryDao extends AbstractDao {
	
	public static function getTableName() {
		return "directory";
	}
	
	public static function getTableAlias() {
		return 'd';
	}
	
	public static function getFields() {
		return array("id", "parent_id", "name", "url", "timestamp");
	}
	
	public static function getIdField() {
		return "id";
	}
	
	
	public function getFromDirectory($dirId) {
		return parent::getList(Select::factory(self::dataAccess())->where('parent_id', '=', $dirId)->orderBy('name'));
	}
	
	public function getParentId($dirId) {
		$item = parent::get($dirId);
		return $item["parent_id"];
	}
}

?>