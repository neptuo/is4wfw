<?php

require_once('DataAccess.class.php');
require_once('AbstractDao.class.php');
require_once('Select.class.php');

class DirectoryDao extends AbstractDao {
	
	public static function getTableName() {
		return "directory";
	}
	
	public static function getTableAlias() {
		return 'd';
	}
	
	public static function getFields() {
		return array("id", "parent_id", "name", "url", "timestamp", "order");
	}
	
	public static function getIdField() {
		return "id";
	}
	
	
	public function getFromDirectory($dirId, $order = "name") {
		$orderDirection = "ASC";
		if (is_array($order)) {
			$key = ArrayUtils::firstKey($order);
			if ($key == "") {
				$order = $order[$key];
			} else {
				$orderDirection = $order[$key];
				$order = $key;
			}
		}

		return parent::getList(Select::factory($this->dataAccess())->where('parent_id', '=', $dirId)->orderBy([$order], $orderDirection));
	}
	
	public function getParentId($dirId) {
		$item = parent::get($dirId);
		return $item["parent_id"];
	}

	public function insert($data) {
		$sql = $this->insertSql($data);

		$this->dataAccess->transaction(function() use ($sql) {
			$this->dataAccess->execute($sql);
			$id = $this->dataAccess->getLastId();
			if (empty($id)) {
				throw new Exception("Missing inserted id while processing new directory.");
			}

			$sql = "UPDATE `" . $this->getTableName() . "` SET `order` = `id` WHERE `" . $this->getIdField() . "` = " . $this->dataAccess->escape($id) . ";";
			$this->dataAccess->execute($sql);
		});

		return $this->dataAccess->getErrorCode();
	}
}

?>