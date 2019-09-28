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

        private function mapTypeToName($type) {
            return self::mapType("key", $type, "name");
        }

        private function getCreateSql($name, $model) {
            $sql = "CREATE TABLE `$name` (";
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
            return $sql;
        }

        private function executeSql($sql1, $sql2 = "", $sql3 = "") {
            self::dataAccess()->transaction();

            try {
                self::dataAccess()->execute($sql1);

                if (!empty($sql2)) {
                    self::dataAccess()->execute($sql2);
                }

                if (!empty($sql3)) {
                    self::dataAccess()->execute($sql3);
                }

                self::dataAccess()->commit();
            } catch(DataAccessException $e) {
                self::dataAccess()->rollback();
                throw $e;
            }
        }

        private function createTable($model) {
            $definitionXml = new SimpleXMLElement("<definition />");
            $name = $model["entity-name"]["value"];
            $tableName = self::tablePrefix . $name;

            $columnName = $model["primary-key-1-name"]["value"];
            $keyElement = $definitionXml->addChild("column");
            $keyElement->addChild("name", $columnName);
            $keyElement->addChild("type", $model["primary-key-1-type"]["value"]);
            $keyElement->addChild("primaryKey", TRUE);
            $keyElement->addChild("required", TRUE);

            $columnName = $model["primary-key-2-name"]["value"];
            if ($columnName != "") {
                $keyElement = $definitionXml->addChild("column");
                $keyElement->addChild("name", $columnName);
                $keyElement->addChild("primaryKey", TRUE);
                $keyElement->addChild("required", TRUE);
            }
            
            $columnName = $model["primary-key-3-name"]["value"];
            if ($columnName != "") {
                $keyElement = $definitionXml->addChild("column");
                $keyElement->addChild("name", $columnName);
                $keyElement->addChild("primaryKey", TRUE);
                $keyElement->addChild("required", TRUE);
            }

            $createSql = self::getCreateSql($tableName, $model);
            $insertSql = "INSERT INTO `custom_entity`(`name`, `description`, `definition`) VALUES ('" . self::dataAccess()->escape($name) . "', '" . self::dataAccess()->escape($model["entity-description"]["value"]) . "', '" . self::dataAccess()->escape($definitionXml->asXml()) . "');";

            try {
                self::executeSql($insertSql, $createSql);
            } catch(DataAccessException $e) {
                return false;
            }
            
            return true;
        }

        private function createTableColumn($name, $tableName, $model) {
            $modelName = $model["column-name"]["value"];
            $modelType = $model["column-type"]["value"];
            $modelRequired = $model["column-required"]["value"];

            $alterSql = "ALTER TABLE `$tableName` ADD COLUMN `" . $modelName . "` " . (self::mapTypeToDb($modelType));
            if ($modelRequired) {
                $alterSql .= " NOT NULL";
            } else {
                $alterSql .= " NULL";
            }

            $alterSql .= ";";

            $xml = self::getDefinition($name);
            if ($xml == NULL) {
                return false;
            }

            $column = $xml->addChild("column");
            $column->addChild("name", $modelName);
            $column->addChild("type", $modelType);

            if ($modelRequired) {
                $column->addChild("required", TRUE);
            }

            $updateSql = self::getUpdateDefinitionSql($name, $xml);
            
            try {
                self::executeSql($updateSql, $alterSql);
                return true;
            } catch (DataAccessException $e) {
                return false;
            }
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
            $tableName = self::ensureTableName($name);

            self::executeSql(
                "DROP TABLE `$tableName`;", 
                "DELETE FROM `custom_entity` WHERE `name` = '" . self::dataAccess()->escape($name) . "';"
            );
            self::parseContent($template);
        }

        public function listTables($template) {
            $tables = self::dataAccess()->fetchAll("SELECT `name`, `description` FROM `custom_entity`;");

            $result = "";
            foreach ($tables as $table) {
                $this->tables->push($table);
                $result .= self::parseContent($template);
                $this->tables->pop();
            }

            return $result;
        }

        public function getTableName() {
            return $this->tables->peek()["name"];
        }

        public function getTableDescription() {
            return $this->tables->peek()["description"];
        }

        private function ensureTableName($name) {
            if (!self::startsWith($name, self::tablePrefix)) {
                $tableName = self::tablePrefix . $name;
                $table = self::dataAccess()->fetchAll("SELECT `name` FROM `custom_entity` WHERE `name` = '" . self::dataAccess()->escape($name) . "';");
                if (count($table) == 0) {
                    trigger_error("Table name must be custom entity", E_USER_ERROR);
                }
            }

            return $tableName;
        }

        private function getDefinition($name) {
            $definition = self::dataAccess()->fetchSingle("SELECT `definition` FROM `custom_entity` WHERE `name` = '" . self::dataAccess()->escape($name) . "';");
            if (empty($definition)) {
                return NULL;
            }

            $xml = new SimpleXMLElement($definition['definition']);
            return $xml;
        }

        private function getUpdateDefinitionSql($name, $xml) {
            $sql = "UPDATE `custom_entity` SET `definition` = '" . self::dataAccess()->escape($xml->asXml()) . "' WHERE `name` = '" . self::dataAccess()->escape($name) . "';";
            return $sql;
        }

        public function listTableColumns($template, $name) {
            $tableName = self::ensureTableName($name);

            $xml = self::getDefinition($name);
            if ($xml == NULL) {
                return "";
            }

            $result = "";
            foreach ($xml->column as $column) {
                $this->columns->push($column);
                $result .= self::parseContent($template);
                $this->columns->pop();
            }

            return $result;
        }

        public function getTableColumnName() {
            return $this->columns->peek()->name;
        }

        public function getTableColumnType() {
            return self::mapTypeToName($this->columns->peek()->type);
        }

        public function getTableColumnPrimaryKey() {
            return $this->columns->peek()->primaryKey == TRUE;
        }

        public function getTableColumnRequired() {
            return $this->columns->peek()->required == TRUE;
        }

        public function tableColumnCreator($name) {
            $tableName = self::ensureTableName($name);

            $model = new Model();
            self::pushModel($model);

            if (array_key_exists("ce-column-creator-save", $_REQUEST)) {
                $model->submit();
                self::partialView("customentities/tableColumnCreator");
                $model->submit(false);

                if (self::createTableColumn($name, $tableName, $model)) {
                    self::redirectToSelf();
                    return;
                }
            }

            $model->render();
            $result = self::partialView("customentities/tableColumnCreator");
            self::popModel();
            return $result;
        }

        public function tableColumnDeleter($template, $entityName, $columnName) {
            $tableName = self::ensureTableName($entityName);
            
            $xml = self::getDefinition($entityName);
            if ($xml == NULL) {
                return;
            }

            for ($i=0; $i < count($xml->column); $i++) { 
                if ($xml->column[$i]->name == $columnName) {
                    unset($xml->column[$i]);
                    break;
                }
            }

            $updateSql = self::getUpdateDefinitionSql($entityName, $xml);
            $alterSql = "ALTER TABLE `$tableName` DROP COLUMN `$columnName`;";

            self::executeSql($updateSql, $alterSql);
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
                $condition = self::joinString($condition, "`$key` = $value", " AND");
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

        private function getDeleteSql($name, $params) {
            foreach ($params as $key => $value) {
                $condition = self::joinString($condition, "`$key` = $value", " AND");
            }

            if (!empty($condition)) {
                $condition  = " WHERE $condition";
            }

            $sql = "DELETE FROM `$name`$condition;";
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
        
        public function deleter($template, $name, $params = array()) {
            $name = self::ensureTableName($name);

            $sql = self::getDeleteSql($name, $params);
            self::dataAccess()->execute($sql);
            self::parseContent($template);
        }
	}

?>