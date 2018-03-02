<?php

require_once('DataAccess.class.php');
require_once('AbstractDao.class.php');
require_once('Select.class.php');

class LanguageDao extends AbstractDao {
	
	public static function getTableName() {
		return "language";
	}
	
	public static function getTableAlias() {
		return 'l';
	}
	
	public static function getFields() {
		return array("id", "language");
	}
	
	public static function getIdField() {
		return "id";
	}
}

?>