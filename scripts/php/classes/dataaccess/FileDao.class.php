<?php

require_once('DataAccess.class.php');
require_once('AbstractDao.class.php');
require_once('Select.class.php');

class FileDao extends AbstractDao {
	
	public static function getTableName() {
		return "file";
	}
	
	public static function getTableAlias() {
		return 'f';
	}
	
	public static function getFields() {
		return array("id", "dir_id", "name", "title", "type", "timestamp");
	}
	
	public static function getIdField() {
		return "id";
	}
	
	
	public function getFromDirectory($dirId) {
		return parent::getList(Select::factory()->where('dir_id', '=', $dirId)->orderBy('name'));
	}
}

?>