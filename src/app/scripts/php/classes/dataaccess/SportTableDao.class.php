<?php

require_once('DataAccess.class.php');
require_once('AbstractDao.class.php');
require_once('Select.class.php');

class SportTableDao extends AbstractDao {
	
	public static function getTableName() {
		return "w_sport_tables";
	}
	
	public static function getTableAlias() {
		return 'wst';
	}
	
	public static function getFields() {
		return array("id", "name", "points_win", "points_win_extratime", "points_draw", "points_loose_extratime", "points_loose", "project_id");
	}
	
	public static function getIdField() {
		return "id";
    }
}

?>