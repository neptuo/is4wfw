<?php

	require_once("BaseTagLib.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/Model.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/Stack.class.php");

	class CustomEntity extends BaseTagLib {

        const tablePrefix = "ce_";
        const primaryKeyAttributePrefix = "key-";

        private $tables;
        private $columns;

		public function __construct() {
            parent::setTagLibXml("CustomEntity.xml");
            
            $this->tables = new Stack();
            $this->columns = new Stack();
        }

        private function mapType($sourceKey, $sourceValue, $targetKey) {
            $items = self::getTableColumnTypes();
            foreach ($items as $item) {
                if ($item[$sourceKey] == $sourceValue) {
                    return $item[$targetKey];
                }
            }
            
            return NULL;
        }

        private function mapTypeToDb($type) {
            return self::mapType("key", $type, "db");
        }

        private function mapTypeToEscapeCharacter($type) {
            return self::mapType("key", $type, "escape");
        }

        private function mapDbTypeToName($type) {
            return self::mapType("db", $type, "name");
        }

        private function createTable($model) {
            $sql = "CREATE TABLE `" . self::tablePrefix . $model["entity-name"]["value"] . "` (";
            $primary = ', PRIMARY KEY (';

            $columnName = $model["primary-key-1-name"]["value"];
            $sql .= "`" . $columnName . "` " . (self::mapTypeToDb($model["primary-key-1-type"]["value"])) . " NOT NULL" . ($model["primary-key-1-identity"]["value"] ? " AUTO_INCREMENT" : "");
            $primary .= "`" . $columnName . "`";
            
            $columnName = $model["primary-key-2-name"]["value"];
            if ($columnName != "") {
                $sql .= ", `" . $columnName . "` " . (self::mapTypeToDb($model["primary-key-2-type"]["value"])) . " NOT NULL";
                $primary .= ", `" . $columnName . "`";
            }
            
            $columnName = $model["primary-key-3-name"]["value"];
            if ($columnName != "") {
                $sql .= ", `" . $columnName . "` " . (self::mapTypeToDb($model["primary-key-3-type"]["value"])) . " NOT NULL";
                $primary .= ", `" . $columnName . "`";
            }

            $primary .= ")";
            $sql .= $primary . ') ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci ROW_FORMAT=FIXED;';
            
            self::dataAccess()->execute($sql);
            return true;
        }

        private function createTableColumn($tableName, $model) {
            $sql = "ALTER TABLE `$tableName` ADD COLUMN `" . $model["column-name"]["value"] . "` " . (self::mapTypeToDb($model["column-type"]["value"]));
            if ($model["column-required"]["value"]) {
                $sql .= " NOT NULL";
            } else {
                $sql .= " NULL";
            }

            $sql .= ";";

            self::dataAccess()->execute($sql);
            return true;
        }
        
        public function tableCreator() {
            $model = new Model();
            self::pushModel($model);

            if (array_key_exists("ce-creator-save", $_REQUEST)) {
                $model->submit();
                self::partialView("customentities/tableCreator");
                $model->submit(false);

                if (self::createTable($model)) {
                    self::redirectToSelf();
                    return;
                }
            }

            $model->render();
            $result = self::partialView("customentities/tableCreator");
            self::popModel();
            return $result;
        }

        public function getTableColumnTypes() {
            return array(
                array("key" => "number", "name" => "Number", "db" => "int(11)", "escape" => ""),
                array("key" => "string", "name" => "Text", "db" => "tinytext", "escape" => "'"),
                array("key" => "bool", "name" => "Boolean", "db" => "bit(1)", "escape" => "")
            );
        }

        public function tableDeleter($template, $name) {
            $name = self::ensureTableName($name);

            $sql = "DROP TABLE `$name`;";
            self::dataAccess()->execute($sql);

            self::parseContent($template);
        }

        public function listTables($template) {
            $tables = self::dataAccess()->fetchAll("SHOW TABLES LIKE '" . self::tablePrefix . "%'");

            $result = "";
            foreach ($tables as $table) {
                $tableName = substr(end($table), strlen(self::tablePrefix));
                $this->tables->push($tableName);
                $result .= self::parseContent($template);
                $this->tables->pop();
            }

            return $result;
        }

        public function getTableName() {
            return $this->tables->peek();
        }

        private function ensureTableName($name) {
            if (!self::startsWith($name, self::tablePrefix)) {
                $name = self::tablePrefix . $name;
                $table = self::dataAccess()->fetchAll("SHOW TABLES LIKE '$name';");
                if (count($table) == 0) {
                    trigger_error("Table name must be custom entity", E_USER_ERROR);
                }
            }

            return $name;
        }

        public function listTableColumns($template, $name) {
            $name = self::ensureTableName($name);

            $columns = self::dataAccess()->fetchAll("show columns from `$name`;");
            
            $result = "";
            foreach ($columns as $column) {
                $this->columns->push($column);
                $result .= self::parseContent($template);
                $this->columns->pop();
            }

            return $result;
        }

        public function getTableColumnName() {
            return $this->columns->peek()["Field"];
        }

        public function getTableColumnType() {
            return self::mapDbTypeToName($this->columns->peek()["Type"]);
        }

        public function getTableColumnPrimaryKey() {
            return $this->columns->peek()["Key"] == "PRI";
        }

        public function getTableColumnRequired() {
            return $this->columns->peek()["Null"] == "NO";
        }

        public function tableColumnCreator($name) {
            $name = self::ensureTableName($name);

            $model = new Model();
            self::pushModel($model);

            if (array_key_exists("ce-column-creator-save", $_REQUEST)) {
                $model->submit();
                self::partialView("customentities/tableColumnCreator");
                $model->submit(false);

                if (self::createTableColumn($name, $model)) {
                    self::redirectToSelf();
                    return;
                }
            }

            $model->render();
            $result = self::partialView("customentities/tableColumnCreator");
            self::popModel();
            return $result;
        }

        public function tableColumnDeleter($template, $tableName, $columnName) {
            $tableName = self::ensureTableName($tableName);

            $sql = "ALTER TABLE `$tableName` DROP COLUMN `$columnName`;";
            self::dataAccess()->execute($sql);

            self::parseContent($template);
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

        private function escapeSqlValue($item) {
            $escape = self::mapTypeToEscapeCharacter($item["type"]);
            if (strlen($escape) > 0) {
                $item["value"] = self::dataAccess()->escape($item["value"]);
            }

            return $escape . $item["value"] . $escape;
        }
        
        private function getInsertSql($name, $model) {
            $columns = "";
            $value = "";
            foreach ($model as $key => $item) {
                $columns = self::joinString($columns, "`$key`");
                $values = self::joinString($values, self::escapeSqlValue($item));
            }

            $sql = "INSERT INTO `$name`($columns) VALUES ($values);";
            return $sql;
        }
        
        private function getUpdateSql($name, $keys, $model) {
            $values = "";
            $condition = "";

            foreach ($model as $key => $item) {
                $values = self::joinString($values, "`$key` = " . self::escapeSqlValue($item));
            }

            foreach ($keys as $key => $value) {
                $condition = self::joinString($condition, "$key = $value", " AND");
            }

            $sql = "UPDATE `$name` SET $values WHERE $condition;";
            return $sql;
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

		public function form($template, $name, $method = "POST", $submit = "", $nextPageId = 0, $params = array()) {
            $name = self::ensureTableName($name);

            if ($method == "GET" && $submit == "") {
                trigger_error("Missing required parameter 'submit' for 'GET' custom entity form '$name'", E_USER_ERROR);
            }

            $keys = self::findPrimaryKeysInAttributes($params);
            $isUpdate = count($keys) > 0;

            $model = new Model();
            self::pushModel($model);

            if ($isUpdate) {
                $model->registration();
                self::parseContent($template);
                $model->registration(false);

                $sql = self::getSelectSql($name, $keys, $model);
                $data = self::dataAccess()->fetchSingle($sql);
                foreach ($model as $key => $item) {
                    $model[$key]["value"] = $data[$key];
                }
            }

            if (self::isHttpMethod($method) && ($submit == "" || array_key_exists($submit, $_REQUEST))) {
                $model->submit();
                self::parseContent($template);
                $model->submit(false);

                if ($isUpdate) {
                    $sql = self::getUpdateSql($name, $keys, $model);
                } else {
                    $sql = self::getInsertSql($name, $model);
                }

                self::dataAccess()->execute($sql);

                if ($nextPageId != 0) {
                    self::web()->redirectTo($nextPageId);
                } else {
                    if (!$isUpdate) {
                        self::popModel();
                        $model = new Model();
                        self::pushModel($model);
                    }
                }
            }

            $model->render();
            $result = self::ui()->form($template, "post");
            self::popModel();
            return $result;
		}
	}

?>