<?php

	require_once("BaseTagLib.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/EditModel.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/FilterModel.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/ListModel.class.php");

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
		
		public function listItems($template, $filter = array(), $orderBy = array()) {
			$tableName = self::TableName;

			$filter = parent::removeKeysWithEmptyValues($filter);
            if (parent::isFilterModel($filter)) {
                $filter = $filter[""];
                $tableName = $filter->wrapTableName($tableName);
                $filter = $filter->toSql();
            }
			
			$model = new ListModel();
			parent::pushListModel($model);

			if (count($orderBy) == 0) {
				$orderBy["id"] = "asc";
			}

			$sql = parent::sql()->select($tableName, array("id", "language", "name", "natural_name"), $filter, $orderBy);
			$data = self::dataAccess()->fetchAll($sql);

			$model->render();
            $model->items($data);
			$result = parent::parseContent($template);

			parent::popListModel();
			return $result;
		}

		public function getListItems() {
			return parent::peekListModel();
		}

		public function getListItemId() {
			return parent::peekListModel()->field("id");
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

		public function form($template, $id) {
			$model = parent::getEditModel();

			if (!$model->hasMetadataKey("isUpdate")) {
				$isUpdate = !empty($id);
				$model->metadata("isUpdate", $isUpdate);
			} else {
				$isUpdate = $model->metadata("isUpdate");
			}

			if ($model->isLoad() && $model->metadata("isUpdate")) {
                $model->registration();
                self::parseContent($template);
				$model->registration(false);
				
				$columns = $model->fields();
				$sql = parent::sql()->select(self::TableName, $columns, array("id" => $id));
				$data = parent::dataAccess()->fetchSingle($sql);
				if (empty($data)) {
					$model->metadata("isError", true);
				}

				$model->copyFrom($data);
			}

			if ($model->isSubmit()) {
                self::parseContent($template);
            }
				
			if ($model->isSave()) {
				if (!$model->metadata("isUpdate")) {
					$sql = parent::sql()->insert(self::TableName, $model);
					parent::dataAccess()->execute($sql);
				} else {
					$sql = parent::sql()->update(self::TableName, $model, array("id" => $id));
					parent::dataAccess()->execute($sql);
				}
            }
			
            if ($model->isRender()) {
				if ($model->hasMetadataKey("isError") && $model->metadata("isError")) {
					return "<h4 class='warning'>Such language doesn't exist.</h4>";
				}

				$result = self::parseContent($template);
				return $result;
			}
		}

		public function deleter($template, $id) {
            $sql = parent::sql()->delete(self::TableName, array("id" => $id));
            self::dataAccess()->execute($sql);
            
            self::parseContent($template);
		}
	}

?>