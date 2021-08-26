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

		$this->applyFileTypeFilter($select, $type);
		$this->applyLimit($select->orderBy([$order], $orderDirection), $pageIndex, $limit);
		return parent::getList($select);
	}

	public function countFromDirectory($dirId, $type = null) {
		$db = $this->dataAccess();
		$select = Select::factory($db)->where('dir_id', '=', $dirId);
		$this->applyFileTypeFilter($select, $type);

		$sql = $this->countSql($this->selectObjectToResult($select));
		return $db->fetchScalar($sql);
	}

	public static function getImageTypeIds() {
		return [WEB_TYPE_JPG, WEB_TYPE_PNG, WEB_TYPE_GIF];
	}

	public static function getImageTypeExtensions() {
		$result = [];
		foreach (self::getImageTypeIds() as $id) {
			$result[] = FileAdmin::$FileExtensions[$id];
		}

		return $result;
	}

	public static function getVideoTypeIds() {
		return [WEB_TYPE_MPEG, WEB_TYPE_MP4];
	}

	public static function getVideoTypeExtensions() {
		$result = [];
		foreach (self::getVideoTypeIds() as $id) {
			$result[] = FileAdmin::$FileExtensions[$id];
		}

		return $result;
	}
	
	public function getImagesFromDirectory($dirId, $oderBy = "name", $pageIndex = false, $limit = false) {
		$select = Select::factory($this->dataAccess())->where('dir_id', '=', $dirId)->conjunctIn('type', self::getImageTypeIds())->orderBy([$oderBy]);
		$select = $this->applyLimit($select, $pageIndex, $limit);
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

	private function applyFileTypeFilter($select, $type) {
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
	}

	public function insert($data) {
		$sql = $this->insertSql($data);

		$this->dataAccess->transaction(function() use ($sql) {
			$this->dataAccess->execute($sql);
			$id = $this->dataAccess->getLastId();
			if (empty($id)) {
				throw new Exception("Missing inserted id while processing new file.");
			}

			$sql = "UPDATE `" . $this->getTableName() . "` SET `order` = `id` WHERE `" . $this->getIdField() . "` = " . $this->dataAccess->escape($id) . ";";
			$this->dataAccess->execute($sql);
		});

		return $this->dataAccess->getErrorCode();
	}
}

?>