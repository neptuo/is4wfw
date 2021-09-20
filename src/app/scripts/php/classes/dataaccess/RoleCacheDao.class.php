<?php

require_once('DataAccess.class.php');
require_once('AbstractDao.class.php');
require_once('Select.class.php');

class RoleCacheDao extends AbstractDao {
	
	public static function getTableName() {
		return "rolecache";
	}
	
	public static function getTableAlias() {
		return 'rc';
	}
	
	public static function getFields() {
		return array("id", "source_id", "target_id");
	}
	
	public static function getIdField() {
		return "id";
	}
	
	
	public function truncate() {
		return parent::dataAccess()->execute('truncate `rolecache`;');
	}
	
	public function getBySource($sourceId) {
		parent::dataAccess()->disableCache();
		$result = parent::select(Select::factory($this->dataAccess())->tableAlias($this->getTableAlias())->where('source_id', '=', $sourceId));
		parent::dataAccess()->enableCache();
		return $result;
	}
	
	public function getBySourceTarget($sourceId, $targetId) {
		parent::dataAccess()->disableCache();
		$result = parent::selectSingle(Select::factory($this->dataAccess())->tableAlias($this->getTableAlias())->where('source_id', '=', $sourceId)->conjunct('target_id', '=', $targetId));
		parent::dataAccess()->enableCache();
		return $result;
	}
}

?>