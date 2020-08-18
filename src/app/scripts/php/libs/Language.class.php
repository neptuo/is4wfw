<?php

	require_once("BaseTagLib.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/EditModel.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/FilterModel.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/ListModel.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/Validator.class.php");

	/**
	 * 
	 *  Class Language. 
	 *      
	 *  @author     maraf
	 *  @timestamp  2019-10-09
	 * 
	 */
	class Language extends BaseTagLib {

		const TableName = "language";

		public function __construct() {
			parent::setTagLibXml("Language.xml");
		}
		
		public function listItems($template, $filter = [], $orderBy = []) {
			$tableName = self::TableName;

			$filter = ArrayUtils::removeKeysWithEmptyValues($filter);
            if (parent::isFilterModel($filter)) {
                $filter = $filter[""];
                $tableName = $filter->wrapTableName($tableName);
                $filter = $filter->toSql();
			}
			
			$orderBy = ArrayUtils::removeKeysWithEmptyValues($orderBy);
            if ($this->isSortModel($orderBy)) {
                $orderBy = $orderBy[""];
            }
			
			$model = new ListModel();
			parent::pushListModel($model);

			if (count($orderBy) == 0) {
				$orderBy["id"] = "asc";
			}

			$sql = parent::sql()->select($tableName, ["id", "language", "name", "natural_name"], $filter, $orderBy);
			$data = parent::dataAccess()->fetchAll($sql);

			$model->render();
            $model->items($data);
			$result = $template();

			parent::popListModel();
			return $result;
		}

		public function getListItems() {
			return parent::peekListModel();
		}

		public function getListItemId() {
			$model = parent::peekListModel(false);
			if ($model != null) {
				return $model->field("id");
			}
			
            $model = parent::getEditModel(false);
            if ($model != null) {
                return $model["id"];
			}
			
			return null;
		}

		public function getListItemName() {
			return parent::peekListModel()->field("name");
		}

		public function getListItemNaturalName() {
			return parent::peekListModel()->field("natural_name");
		}

		public function getListItemUrl() {
			return parent::peekListModel()->field("language");
		}

		public function form($template, $id = 0) {
			$model = parent::getEditModel();

			if (!$model->hasMetadataKey("isUpdate")) {
				$isUpdate = !empty($id);
				$model->metadata("isUpdate", $isUpdate);
			} else {
				$isUpdate = $model->metadata("isUpdate");
			}

			if ($model->isLoad() && $model->metadata("isUpdate")) {
                $model->registration();
                $template();
				$model->registration(false);
				
				$columns = $model->fields();
				$sql = parent::sql()->select(self::TableName, $columns, ["id" => $id]);
				$data = parent::dataAccess()->fetchSingle($sql);
				if (empty($data)) {
					$model->metadata("isError", true);
				}

				$model->copyFrom($data);
			}

			if ($model->isSubmit()) {
				$template();
				
				Validator::required($model, "name");
				Validator::required($model, "language");

				if (!$this->isUnique($model["language"], $id)) {
					Validator::addUnique($model, "language");
				}
            }
				
			if ($model->isSave()) {
				if (!$model->metadata("isUpdate")) {
					$sql = parent::sql()->insert(self::TableName, $model);
					parent::dataAccess()->execute($sql);
					$model["id"] = parent::dataAccess()->getLastId();
				} else {
					$sql = parent::sql()->update(self::TableName, $model, ["id" => $id]);
					parent::dataAccess()->execute($sql);
					$model["id"] = $id;
				}
			}
			
            if ($model->isSaved()) {
                $template();
            }
			
            if ($model->isRender()) {
				if ($model->hasMetadataKey("isError") && $model->metadata("isError")) {
					return "<h4 class='warning'>Such language doesn't exist.</h4>";
				}

				$result = $template();
				return $result;
			}
		}

		private function isUnique($name, $id) {
			$sql = parent::sql()->select(self::TableName, ["id"], ["language" => $name]);
			$data = parent::dataAccess()->fetchSingle($sql);
			if (empty($data)) {
				return true;
			}

			if (!empty($id)) {
				return $id == $data["id"];
			}

			return false;
		}

		public function deleter($template, $id) {
            $sql = parent::sql()->delete(self::TableName, ["id" => $id]);
            parent::dataAccess()->execute($sql);
            
            $template();
		}
	}

?>