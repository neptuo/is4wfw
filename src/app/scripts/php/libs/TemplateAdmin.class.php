<?php

	require_once("BaseTagLib.class.php");

	/**
	 * 
	 *  Class Template. 
	 *      
	 *  @author     maraf
	 *  @timestamp  2024-01-24
	 * 
	 */
	class TemplateAdmin extends BaseTagLib {

		const TableName = "template";
        
        private $tagPrefix;

		public function __construct($tagPrefix) {
            $this->tagPrefix = $tagPrefix;
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
            
            $model->registration();
            $template(ParsedTemplateConfig::filtered($this->tagPrefix, [], ["*"]));
            $model->registration(false);

            $fields = $model->fields(); // ["id", "name", "group", "identifier", "content"]
            if (!in_array("id", $fields)) {
                $fields[] = "id";
            }
			$sql = parent::sql()->select($tableName, $fields, $filter, $orderBy);
			$data = parent::dataAccess()->fetchAll($sql);

            $items = [];
            foreach ($data as $item) {
                if (RoleHelper::isInRole(parent::login()->getGroupsIds(), RoleHelper::getRights(Web::$TemplateRightDesc, $item["id"], WEB_R_READ))) {
                    $items[] = $item;
                }
            }

			$model->render();
            $model->items($items);
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
			
			return null;
		}

		public function getListItemName() {
			return parent::peekListModel()->field("name");
		}

		public function getListItemGroup() {
			return parent::peekListModel()->field("group");
		}

		public function getListItemIdentifier() {
			return parent::peekListModel()->field("identifier");
		}

		public function getListItemContent() {
			return parent::peekListModel()->field("content");
		}
	}

?>