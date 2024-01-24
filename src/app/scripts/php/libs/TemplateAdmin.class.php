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
        private $urlProperties;
        private $urlResolvers;

		public function __construct($tagPrefix) {
            $this->tagPrefix = $tagPrefix;
            $this->urlProperties = [];
            $this->urlResolvers = [];
        }

        private function hasAccessToTemplate($data) {
            return RoleHelper::isInRole(parent::login()->getGroupsIds(), RoleHelper::getRights(Web::$TemplateRightDesc, $data["id"], WEB_R_READ));
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
                if ($this->hasAccessToTemplate($item["id"])) {
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
            $model = parent::peekListModel(false);
            if ($model != null) {
                return $model->field("identifier");
            }

            if (array_key_exists("identifier", $this->urlProperties)) {
                return $this->urlProperties["identifier"];
            }

            return null;
		}

		public function getListItemContent() {
			return parent::peekListModel()->field("content");
		}

        public function identifierUrlResolver($filter = array()) {
            $this->urlResolvers["identifier"] = [
                "filter" => $filter
            ];
        }

        public function setIdentifierFromUrl($value) {
            if (array_key_exists("identifier", $this->urlResolvers)) {
                $resolver = $this->urlResolvers["identifier"];

                $tableName = self::TableName;
                $filter = $resolver["filter"];
                if (parent::isFilterModel($filter)) {
                    $filter = $filter[""];
                    $sqlName = Filter::formatColumnName($filter, "identifier");
                    $sqlValue = parent::sql()->escape($value);
                    $filter[] = "$sqlName = $sqlValue";
                    $tableName = $filter->wrapTableName(self::TableName);
                    $filter = $filter->toSql();
                } else {
                    $filter["identifier"] = $value;
                }

                $sql = parent::sql()->select($tableName, ["id"], $filter);
                $data = parent::db()->fetchAll($sql);
                if (empty($data) || count($data) != 1 || !$this->hasAccessToTemplate($data[0])) {
                    return "x.x---y\\r";
                }
            }

            $this->urlProperties["identifier"] = $value;
            return $value;
        }
	}

?>