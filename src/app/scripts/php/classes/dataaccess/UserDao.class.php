<?php

require_once('DataAccess.class.php');
require_once('AbstractDao.class.php');
require_once('Select.class.php');

class UserDao extends AbstractDao {
	
	public static function getTableName() {
		return "user";
	}
	
	public static function getTableAlias() {
		return 'u';
	}
	
	public static function getFields(){
		return array("uid", "group_id", "name", "surname", "login", "password", "enable");
	}
	
	public static function getIdField(){
		return "uid";
	}
	
	
	public function getByLogin($login, $password) {
		$select = Select::factory($this->dataAccess())->tableAlias($this->getTableAlias())->where('login', '=', $login)->conjunct('password', '=', $password);
		
		$sql = $this->selectSql($select->result(), true);
		return $this->dataAccess->fetchSingle($sql);
	}
	
	public function getEnabled() {
		return parent::getList(Select::factory($this->dataAccess())->where('enable', '=', 1));
	}
}

?>