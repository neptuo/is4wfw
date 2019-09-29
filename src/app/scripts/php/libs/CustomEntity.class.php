<?php

	require_once("CustomEntityBase.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/EditModel.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/Stack.class.php");

	class CustomEntity extends CustomEntityBase {

        const primaryKeyAttributePrefix = "key-";

		public function __construct() {
            parent::setTagLibXml("CustomEntity.xml");
        }

        private function findPrimaryKeysInAttributes($params) {
            $result = array();
            foreach ($params as $key => $value) {
                if (self::startsWith($key, self::primaryKeyAttributePrefix) && !empty($value)) {
                    $result[substr($key, strlen(self::primaryKeyAttributePrefix))] = $value;
                }
            }

            return $result;
        }

        private function parseUserValue($column, $value) {
            switch ($column->type) {
                case 'string':
                    return $value;
                case 'number':
                    return intval($value);
                case 'bool':
                    return boolval($value);
                default:
                    throw new Exception("Not supported field type '$column->name'.");
            }
        }

        private function prepareValuesFromModel($xml, $model) {
            $values = array();
            foreach ($xml->column as $column) {
                if (array_key_exists((string)$column->name, $model)) {
                    $value = self::parseUserValue($column, $model[(string)$column->name]);
                    $values[(string)$column->name] = $value;
                }
            }

            return $values;
        }
        
        private function getInsertSql($name, $xml, $model) {
            $values = self::prepareValuesFromModel($xml, $model);
            return self::sql()->insert($name, $values);
        }
        
        private function getUpdateSql($name, $xml, $keys, $model) {
            $values = self::prepareValuesFromModel($xml, $model);
            $filter = self::prepareValuesFromModel($xml, $keys);
            return self::sql()->update($name, $values, $filter);
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

            $keys = self::findPrimaryKeysInAttributes($params);
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
                } else {
                    $sql = self::getInsertSql($tableName, $xml, $model);
                }

                self::dataAccess()->execute($sql);

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
        
        public function deleter($template, $name, $params = array()) {
            $name = self::ensureTableName($name);

            $sql = self::getDeleteSql($name, $params);
            self::dataAccess()->execute($sql);
            self::parseContent($template);
        }
	}

?>