<?php

require_once('DataAccess.class.php');
require_once('AbstractDao.class.php');
require_once('Select.class.php');

class ArticleLabelLanguageDao extends AbstractDao {
	
	public static function getTableName() {
		return "article_line";
	}
	
	public static function getTableAlias() {
		return 'al';
	}
	
	public static function getFields() {
		return array("id", "name", "url", "parentdirectory_id");
	}
	
	public static function getIdField() {
		return "id";
	}
}

?>