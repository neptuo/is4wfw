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
		return array("id", "dir_id", "name", "title", "type", "timestamp", 'url');
	}
	
	public static function getIdField() {
		return "id";
	}
	
	
	public function getFromDirectory($dirId) {
		return parent::getList(Select::factory(self::dataAccess())->where('dir_id', '=', $dirId)->orderBy('name'));
	}
	
	public function getImagesFromDirectory($dirId, $limit = false) {
		$select = Select::factory(self::dataAccess())->where('dir_id', '=', $dirId)->conjunctIn('type', array(WEB_TYPE_JPG, WEB_TYPE_PNG, WEB_TYPE_GIF))->orderBy('name');
		if($limit > 0) {
			$select = $select->limit(0, $limit);
		}

		return parent::getList($select);
	}
}

?>