<?php

	require_once("CustomEntityBase.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/EditModel.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/ListModel.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/Stack.class.php");

	class CustomEntity extends CustomEntityBase {

        const primaryKeyAttributePrefix = "key-";
        const filterAttributePrefix = "filter-";
        const orderByAttributePrefix = "orderBy-";

		public function __construct() {
            parent::setTagLibXml("CustomEntity.xml");
        }

        private function findAttributesByPrefix($params, $prefix) {
            $result = array();
            foreach ($params as $key => $value) {
                if (self::startsWith($key, $prefix) && !empty($value)) {
                    $result[substr($key, strlen($prefix))] = $value;
                }
            }

            return $result;
        }

        private function parseUserValue($column, $value) {
            $type = self::getTableColumnTypes((string)$column->type);
            if ($type == NULL) {
                return NULL;
            }

            return $type["fromUser"]($value);
        }

        private function prepareValuesFromModel($xml, $model) {
            $columns = array();
            $extras = array();
            foreach ($xml->column as $column) {
                $columnName = (string)$column->name;
                if (array_key_exists($columnName, $model)) {
                    $value = $model[$columnName];
                    if (is_array($value) && array_key_exists("type", $value)) {
                        $extras[$columnName] = $value;
                    } else {
                        $value = self::parseUserValue($column, $value);
                        $columns[$columnName] = $value;
                    }
                }
            }

            return array(
                "columns" => $columns,
                "extras" => $extras
            );
        }
        
        private function insert($name, $xml, $model) {
            $values = self::prepareValuesFromModel($xml, $model);
            $sql = self::sql()->insert($name, $values["columns"]);

            if (empty($values["extras"])) {
                self::dataAccess()->execute($sql);
            } else {
                self::dataAccess()->transaction(function($da) use ($name, $xml, $model, $values, $sql) {
                    $da->execute($sql);
                    
                    $identity = self::findIdentityColumn($xml);
                    if ($identity != NULL) {
                        $id = $da->getLastId();
                        $model[(string)$identity->name] = $id;
                    }
                    
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
                });
            }
        }
        
        private function getUpdateSql($name, $xml, $keys, $model) {
            $values = self::prepareValuesFromModel($xml, $model);
            $filter = self::prepareValuesFromModel($xml, $keys);
            return self::sql()->update($name, $values["columns"], $filter["columns"]);
        }

        private function getSelectSql($name, $keys, $model) {
            $columns = "";
            $condition = "";
            foreach ($model as $key => $item) {
                $columns = self::joinString($columns, "`$key`");
            }

            foreach ($keys as $key => $value) {
                $condition = self::joinString($condition, "$key = $value", " AND");
            }

            $sql = "SELECT $columns FROM `$name` WHERE $condition";
            return $sql;
        }

        private function getDeleteSql($name, $params) {
            return self::sql()->delete($name, $params);
        }

		public function form($template, $name, $method = "POST", $submit = "", $nextPageId = 0, $params = array()) {
            $tableName = self::ensureTableName($name);

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

                $sql = self::getSelectSql($tableName, $keys, $model);
                $data = self::dataAccess()->fetchSingle($sql);
                foreach ($model as $key => $item) {
                    $model[$key] = $data[$key];
                }
            }

            if (self::isHttpMethod($method) && ($submit == "" || array_key_exists($submit, $_REQUEST))) {
                $model->submit();
                self::parseContent($template);
                $model->submit(false);

                $xml = self::getDefinition($name);

                if ($isUpdate) {
                    $sql = self::getUpdateSql($tableName, $xml, $keys, $model);
                    self::dataAccess()->execute($sql);
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

        public function getList($template, $name, $params = array()) {
            $tableName = self::ensureTableName($name);
            $filter = self::findAttributesByPrefix($params, self::filterAttributePrefix);
            $orderBy = self::findAttributesByPrefix($params, self::orderByAttributePrefix);

            $model = new ListModel();
            self::pushListModel($model);

            $model->registration();
            self::parseContent($template);
            $model->registration(false);
            
            $result = "";

            $sql = self::sql()->select($tableName, $model->fields(), $filter, $orderBy);
            $data = self::dataAccess()->fetchAll($sql);

            $model->render();
            foreach ($data as $item) {
                $model->data($item);
                $result .= self::parseContent($template);
            }

            self::popListModel();
            return $result;
        }

		public function getProperty($name) {
            return self::peekListModel()->field($name);
		}
        
        public function deleter($template, $name, $params = array()) {
            $tableName = self::ensureTableName($name);

            $sql = self::getDeleteSql($tableName, $params);
            self::dataAccess()->execute($sql);
            self::parseContent($template);
        }
	}

?>