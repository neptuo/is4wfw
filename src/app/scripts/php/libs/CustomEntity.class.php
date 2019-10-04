<?php

	require_once("CustomEntityBase.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/EditModel.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/ListModel.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/Stack.class.php");

	class CustomEntity extends CustomEntityBase {

        const primaryKeyAttributePrefix = "key-";
        const filterAttributePrefix = "filter-";
        const orderByAttributePrefix = "orderBy-";

        private $tagPrefix;

		public function __construct($tagPrefix) {
            parent::setTagLibXml("CustomEntity.xml");
            $this->tagPrefix = $tagPrefix;
        }

        private function parseUserValue($column, $value) {
            $type = self::getTableColumnTypes($column);
            if ($type == NULL) {
                return NULL;
            }

            return $type["fromUser"]($value);
        }

        private function prepareValuesFromModel($xml, $model) {
            $columns = array();
            $extras = array();
            $external = array();
            foreach ($xml->column as $column) {
                $columnName = (string)$column->name;
                $columnType = (string)$column->type;
                if (array_key_exists($columnName, $model)) {
                    $value = $model[$columnName];
                    if (is_callable($value)) {
                        $value = $value();
                    }

                    if (is_array($value) && array_key_exists("type", $value)) {
                        $extras[$columnName] = $value;
                    } else {
                        $typeDefinition = self::getTableColumnTypes($column);
                        if ($typeDefinition["hasColumn"]) {
                            $value = self::parseUserValue($column, $value);
                            $columns[$columnName] = $value;
                        } else {
                            $value = self::parseUserValue($column, $value);
                            if ($columnType == "multireference-jointable") {
                                $external[$columnName] = array(
                                    "type" => $columnType,
                                    "value" => $value,
                                    "table" => $column->table,
                                    "targetColumn" => $column->targetColumn,
                                );
                            }
                        }
                    }
                }
            }

            return array(
                "columns" => $columns,
                "extras" => $extras,
                "external" => $external
            );
        }

        private function updateExternals($values, $xml, $model) {
            foreach ($values["external"] as $columnName => $external) {
                if ($external["type"] == "multireference-jointable") {
                    $column = self::findColumn($xml, $columnName);
                    self::updateJoinTable($xml, $column, $model);
                }
            }
        }
        
        private function insert($name, $xml, $model) {
            $values = self::prepareValuesFromModel($xml, $model);
            $sql = self::sql()->insert($name, $values["columns"]);

            if (empty($values["extras"]) && empty($values["external"])) {
                self::dataAccess()->execute($sql);
            } else {
                self::dataAccess()->transaction(function($da) use ($name, $xml, $model, $values, $sql) {
                    // Execute insert.
                    $da->execute($sql);
                    
                    // Get last identity value if inserted.
                    $identity = self::findIdentityColumn($xml);
                    if ($identity != NULL) {
                        $id = $da->getLastId();
                        $model[(string)$identity->name] = $id;
                    }
                    
                    // Process extras.
                    foreach ($values["extras"] as $columnName => $extra) {
                        if ($extra["type"] == "emptyDirectory") {
                            $fa = new FileAdmin();
                            $directoryName = self::formatString($extra['nameFormat'], $model);
                            $directory = $fa->createDirectory($extra['parentDirId'], $directoryName);

                            $filter = array();
                            foreach ($xml->column as $column) {
                                if ($column->primaryKey == TRUE) {
                                    $filter[(string)$column->name] = $model[(string)$column->name];
                                }
                            }
                            
                            $sql = self::sql()->update($name, array($columnName => $directory['id']), $filter);
                            $da->execute($sql);
                        }
                    }

                    // Process external tables.
                    self::updateExternals($values, $xml, $model);
                });
            }
        }
        
        private function update($name, $xml, $keys, $model) {
            $values = self::prepareValuesFromModel($xml, $model);
            $filter = self::prepareValuesFromModel($xml, $keys);
            $sql = self::sql()->update($name, $values["columns"], $filter["columns"]);

            if (empty($values["extras"]) && empty($values["external"])) {
                self::dataAccess()->execute($sql);
            } else {
                self::dataAccess()->transaction(function($da) use ($name, $xml, $model, $values, $sql) {
                    // Execute update.
                    $da->execute($sql);

                    // Process external tables.
                    self::updateExternals($values, $xml, $model);
                });
            }
        }

        private function loadModel($name, $xml, $keys, $model) {
            $columns = "";
            $condition = "";
            $external = array();
            foreach ($xml->column as $column) {
                $columnName = (string)$column->name;
                $columnType = (string)$column->type;
                if (array_key_exists($columnName, $model)) {
                    if (!$typeDefinition["hasColumn"]) {
                        if ($columnType == "multireference-jointable") {
                            self::registerPrimaryKeysToModel($xml, $model);

                            $external[$columnName] = array(
                                "type" => $columnType
                            );
                        }
                    }
                }
            }

            foreach ($xml->column as $column) {
                $columnName = (string)$column->name;
                $columnType = (string)$column->type;
                if (array_key_exists($columnName, $model)) {
                    $typeDefinition = self::getTableColumnTypes($column);
                    if ($typeDefinition["hasColumn"]) {
                        $columns = self::joinString($columns, "`$columnName`");
                    }
                }
            }

            foreach ($keys as $key => $value) {
                $condition = self::joinString($condition, "$key = $value", " AND");
            }

            $data = self::dataAccess()->fetchSingle("SELECT $columns FROM `$name` WHERE $condition");
            foreach ($model as $key => $item) {
                $model[$key] = $data[$key];
            }

            foreach ($external as $columnName => $item) {
                if ($item["type"] == "multireference-jointable") {
                    $value = self::loadJoinTableData($xml, $model, $columnName);
                    $model[$columnName] = $value;
                }
            }

            return $sql;
        }

        private function getDeleteSql($name, $params) {
            return self::sql()->delete($name, $params);
        }

		public function form($template, $name, $method = "POST", $submit = "", $nextPageId = 0, $params = array()) {
            $tableName = self::ensureTableName($name);
            $xml = self::getDefinition($name);

            if ($method == "GET" && $submit == "") {
                trigger_error("Missing required parameter 'submit' for 'GET' custom entity form '$name'", E_USER_ERROR);
            }

            $keys = self::findAttributesByPrefix($params, self::primaryKeyAttributePrefix);
            $isUpdate = count($keys) > 0;

            $model = new EditModel();
            self::pushEditModel($model);

            if ($isUpdate) {
                $model->registration();
                self::parseContent($template);
                $model->registration(false);

                self::loadModel($tableName, $xml, $keys, $model);
            }

            if (self::isHttpMethod($method) && ($submit == "" || array_key_exists($submit, $_REQUEST))) {
                $model->submit();
                self::parseContent($template);
                $model->submit(false);

                if ($isUpdate) {
                    self::update($tableName, $xml, $keys, $model);
                } else {
                    self::insert($tableName, $xml, $model);
                }

                if ($nextPageId != 0) {
                    self::web()->redirectTo($nextPageId);
                } else {
                    if (!$isUpdate) {
                        self::popEditModel();
                        $model = new EditModel();
                        self::pushEditModel($model);
                    }
                }
            }

            $model->render();
            $result = self::ui()->form($template, "post");
            self::popEditModel();
            return $result;
        }

        public function emptyDirectory($name, $parentDirId, $nameFormat) {
            if (self::peekEditModel()->isSubmit()) {
                self::peekEditModel()[$name] = array(
                    "type" => "emptyDirectory",
                    "parentDirId" => $parentDirId,
                    "nameFormat" => $nameFormat
                );
            }
        }

        public function getListData() {
            return self::peekListModel();
        }

        public function getList($template, $name, $params = array()) {
            $tableName = self::ensureTableName($name);
            $filter = self::findAttributesByPrefix($params, self::filterAttributePrefix);
            $orderBy = self::findAttributesByPrefix($params, self::orderByAttributePrefix);

            $model = new ListModel();
            self::pushListModel($model);

            $model->registration();
            self::parseContent($template, array($this->tagPrefix . ":register"));
            $model->registration(false);
            
            $result = "";

            $sql = self::sql()->select($tableName, $model->fields(), $filter, $orderBy);
            $data = self::dataAccess()->fetchAll($sql);

            $model->render();
            $model->items($data);
            $result .= self::parseContent($template);

            self::popListModel();
            return $result;
        }

        public function register($name) {
            self::getProperty($name);
        }

		public function getProperty($name) {
			$model = self::peekListModel();
			if ($model == null) {
				return null;
			}

			if ($name == "_") {
				return $model->data();
			}

			return $model->field($name);
		}
        
        public function deleter($template, $name, $params = array()) {
            $tableName = self::ensureTableName($name);

            $sql = self::getDeleteSql($tableName, $params);
            self::dataAccess()->execute($sql);
            self::parseContent($template);
        }
	}

?>