<?php

require_once('DataAccess.class.php');
require_once('AbstractDao.class.php');
require_once('Select.class.php');

class ApplicationVariableDao extends AbstractDao {
	
	public static function getTableName() {
		return "application_variable";
	}
	
	public static function getTableAlias() {
		return 'avar';
	}
	
	public static function getFields() {
		return array("name", "value");
	}
	
	public static function getIdField() {
		return "name";
	}

	public function getValue($name) {
		$select = parent::createSelect();
		$idField = $this->getIdField();
		$select->where($this->getIdField(), '=', $name);
		$sql = self::selectSql($select->result(), true, array('value'));
		$data = $this->dataAccess->fetchSingle($sql);
		return $data['value'];
	}

	public function setValue($name, $value) {
		$data = array(
			'name' => $name,
			'value' => $value
		);

		if (parent::exists(parent::createSelect()->where('name', '=', $name))) {
			return parent::update($data);
		} else {
			return parent::insert($data);
		}
	}
}

?>