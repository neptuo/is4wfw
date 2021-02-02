<?php

require_once('DataAccess.class.php');
require_once('AbstractDao.class.php');
require_once('Select.class.php');

class FileDao extends AbstractDao {
	
	public static function getTableName() {
		return "file";
	}
	
	public static function getTableAlias() {
		return 'f';
	}
	
	public static function getFields() {
		return array("id", "dir_id", "name", "title", "type", "timestamp", 'url', "order");
	}
	
	public static function getIdField() {
		return "id";
	}
	
	
	public function getFromDirectory($dirId, $order = "name", $type = null, $pageIndex = false, $limit = false) {
		$select = Select::factory(self::dataAccess())->where('dir_id', '=', $dirId);
		
		if (is_array($type)) {
			$types = array();
			foreach	(FileAdmin::$FileExtensions as $id => $extension) {
				if (in_array($extension, $type)) {
					$types[] = $id;
				}
			}

			if (count($types) > 0) {
				$select = $select->conjunctIn('type', $types);
			}
		}
		
		$select = self::applyLimit($select->orderBy([$order]), $pageIndex, $limit);
		return parent::getList($select);
	}
	
	public function getImagesFromDirectory($dirId, $pageIndex = false, $limit = false) {
		$select = Select::factory(self::dataAccess())->where('dir_id', '=', $dirId)->conjunctIn('type', array(WEB_TYPE_JPG, WEB_TYPE_PNG, WEB_TYPE_GIF))->orderBy('name');
		$select = self::applyLimit($select, $pageIndex, $limit);
		return parent::getList($select);
	}

	private function applyLimit($select, $pageIndex = false, $limit = false) {
		if ($limit > 0) {
			$start = 0;
			if ($pageIndex > 0) {
				$start = $pageIndex * $limit;
			}

			$select = $select->limit($start, $limit);
		}

		return $select;
	}

	public function insert($data) {
		$sql = $this->insertSql($data);
		
		$this->dataAccess->transaction();
		
		$this->dataAccess->execute($sql);
		$id = $this->dataAccess->getLastId();
		if (empty($id)) {
			throw new Exception("Missing inserted id while processing new file.");
		}

		$sql = "UPDATE `" . $this->getTableName() . "` SET `order` = `id` WHERE `" . $this->getIdField() . "` = " . $this->dataAccess->escape($id) . ";";
		$this->dataAccess->execute($sql);

		$this->dataAccess->commit();
		return $this->dataAccess->getErrorCode();
	}
}

?>