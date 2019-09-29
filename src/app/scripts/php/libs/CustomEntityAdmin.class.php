<?php

	require_once("CustomEntityBase.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/EditModel.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/Stack.class.php");

	class CustomEntityAdmin extends CustomEntityBase {

        private $tables;
        private $columns;

		public function __construct() {
            parent::setTagLibXml("CustomEntityAdmin.xml");
            
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

        private function mapTypeToName($type) {
            return self::mapType("key", $type, "name");
        }

        private function getCreateSql($name, $model) {
            $sql = "CREATE TABLE `$name` (";
            $primary = ', PRIMARY KEY (';

            $columnName = $model["primary-key-1-name"];
            $sql .= "`" . $columnName . "` " . (self::mapTypeToDb($model["primary-key-1-type"])) . " NOT NULL" . ($model["primary-key-1-identity"] ? " AUTO_INCREMENT" : "");
            $primary .= "`" . $columnName . "`";
            
            $columnName = $model["primary-key-2-name"];
            if ($columnName != "") {
                $sql .= ", `" . $columnName . "` " . (self::mapTypeToDb($model["primary-key-2-type"])) . " NOT NULL";
                $primary .= ", `" . $columnName . "`";
            }
            
            $columnName = $model["primary-key-3-name"];
            if ($columnName != "") {
                $sql .= ", `" . $columnName . "` " . (self::mapTypeToDb($model["primary-key-3-type"])) . " NOT NULL";
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
            $name = $model["entity-name"];
            $tableName = self::TablePrefix . $name;

            $columnName = $model["primary-key-1-name"];
            $keyElement = $definitionXml->addChild("column");
            $keyElement->addChild("name", $columnName);
            $keyElement->addChild("type", $model["primary-key-1-type"]);
            $keyElement->addChild("primaryKey", TRUE);
            $keyElement->addChild("required", TRUE);
            
            if ($model["primary-key-1-identity"]) {
                $keyElement->addChild("identity", TRUE);
            }

            $columnName = $model["primary-key-2-name"];
            if ($columnName != "") {
                $keyElement = $definitionXml->addChild("column");
                $keyElement->addChild("name", $columnName);
                $keyElement->addChild("type", $model["primary-key-2-type"]);
                $keyElement->addChild("primaryKey", TRUE);
                $keyElement->addChild("required", TRUE);
            }
            
            $columnName = $model["primary-key-3-name"];
            if ($columnName != "") {
                $keyElement = $definitionXml->addChild("column");
                $keyElement->addChild("name", $columnName);
                $keyElement->addChild("type", $model["primary-key-3-type"]);
                $keyElement->addChild("primaryKey", TRUE);
                $keyElement->addChild("required", TRUE);
            }

            $createSql = self::getCreateSql($tableName, $model);
            $insertSql = self::sql()->insert("custom_entity", array("name" => $name, "description" => $model["entity-description"], "definition" => $definitionXml->asXml()));

            try {
                self::executeSql($insertSql, $createSql);
            } catch(DataAccessException $e) {
                return false;
            }
            
            return true;
        }

        private function createTableColumn($name, $tableName, $model) {
            $modelName = $model["column-name"];
            $modelType = $model["column-type"];
            $modelRequired = $model["column-required"];

            $fkSql = "";
            $alterSql = "ALTER TABLE `$tableName` ADD COLUMN `" . $modelName . "` " . (self::mapTypeToDb($modelType));
            if ($modelRequired) {
                $alterSql .= " NOT NULL";
            } else {
                $alterSql .= " NULL";
            }

            $alterSql .= ";";

            if ($modelType == "singlereference") {
                $referenceTable = $model["column-singlerefence-table"];
                $referenceColumn = $model["column-singlerefence-column"];
                $fkSql = "ALTER TABLE `$tableName` ADD FOREIGN KEY (`$modelName`) REFERENCES `$referenceTable`(`$referenceColumn`);";
            }

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
                self::executeSql($updateSql, $alterSql, $fkSql);
                return true;
            } catch (DataAccessException $e) {
                return false;
            }
        }
        
        public function tableCreator() {
            $model = new EditModel();
            self::pushEditModel($model);

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
            self::popEditModel();
            return $result;
        }

        public function tableDeleter($template, $name) {
            $tableName = self::ensureTableName($name);

            self::executeSql(
                "DROP TABLE `$tableName`;", 
                self::sql()->delete("custom_entity", array("name" => $name))
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

            $model = new EditModel();
            self::pushEditModel($model);

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
            self::popEditModel();
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
	}

?>