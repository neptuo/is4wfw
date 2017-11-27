<?php

require_once('DataAccess.class.php');
require_once('AbstractDao.class.php');
require_once('Select.class.php');

class ArticleLabelLanguageDao extends AbstractDao {
	
	public static function getTableName() {
		return "article_label_language";
	}
	
	public static function getTableAlias() {
		return 'all';
	}
	
	public static function getFields() {
		return array("label_id", "language_id", "name", "url");
	}
	
	public static function getIdField() {
		return array("label_id", "language_id");
	}
}

?>