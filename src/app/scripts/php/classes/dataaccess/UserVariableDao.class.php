<?php

require_once('DataAccess.class.php');
require_once('AbstractDao.class.php');
require_once('Select.class.php');

class UserVariableDao extends AbstractDao {
	
	public static function getTableName() {
		return "personal_property";
	}
	
	public static function getTableAlias() {
		return 'pp';
	}
	
	public static function getFields() {
		return array("name", "value");
	}
	
	public static function getIdField() {
		return "id";
	}

	public function getValue($userId, $name) {
		$select = parent::createSelect();
		$select->where('user_id', '=', $userId);
		$select->conjunct('name', '=', $name);
		$select->conjunct('type', '=', 1);
		$select->tableAlias($this->getTableAlias());
		$sql = $this->selectSql($select->result(), true, array('value'));
		$value = $this->dataAccess->fetchScalar($sql);
		return $value;
	}

	public function setValue($userId, $name, $value) {
		$data = array(
			'user_id' => $userId,
			'name' => $name,
			'value' => $value,
			'type' => 1
		);

		$getIdSql = $this->sql()->select(self::getTableName(), ["id"], ["user_id" => $userId, "name" => $name, "type" => 1]);
		$existingId = $this->dataAccess()->fetchScalar($getIdSql);
		if ($existingId) {
			$updateSql = $this->sql()->update(self::getTableName(), ["value" => $value], ["id" => $existingId]);
			return $this->dataAccess()->execute($updateSql);
		} else {
			return parent::insert($data);
		}
	}

	public function delete($userId, $name) {
		$sql = $this->sql()->delete(UserVariableDao::getTableName(), ["user_id" => $userId, "name" => $name]);
		$this->dataAccess()->execute($sql);
		return $this->dataAccess->getErrorCode();
	}
}

?>