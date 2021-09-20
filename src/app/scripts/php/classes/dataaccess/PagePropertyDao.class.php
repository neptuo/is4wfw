<?php

require_once('DataAccess.class.php');
require_once('AbstractDao.class.php');
require_once('Select.class.php');

class PagePropertyDao extends AbstractDao {
	
	public static function getTableName() {
		return "page_property_value";
	}
	
	public static function getTableAlias() {
		return 'ppv';
	}
	
	public static function getFields() {
		return array("id", "page_id", "name", "value");
	}
	
	public static function getIdField() {
		return "id";
	}
	
	
	public function getPage($pageId) {
		return parent::getList(Select::factory($this->dataAccess())->where('page_id', '=', $pageId));
	}
}

?>