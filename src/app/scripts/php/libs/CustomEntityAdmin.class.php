<?php

	require_once("CustomEntityBase.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/EditModel.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/ListModel.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/Stack.class.php");

	class CustomEntityAdmin extends CustomEntityBase {

        private $tables;
        private $columns;

		public function __construct() {
            parent::setTagLibXml("CustomEntityAdmin.xml");
            
            $this->tables = new Stack();
            $this->columns = new Stack();
        }

        private function getCreateSql($name, $xml) {
            $columns = "";
            $primary = '';

            foreach ($xml->column as $column) {
                if ($column->primaryKey == true) {
                    $columnName = (string)$column->name;
                    $dbType = self::getTableColumnTypes($column, "db");
                    $columns = self::joinString($columns, "`$columnName` $dbType NOT NULL");

                    if ($column->identity == true) {
                        $columns .= " AUTO_INCREMENT";
                    }

                    $primary = self::joinString($primary, "`$columnName`");
                }
            }

            $sql = "CREATE TABLE `$name` ($columns, PRIMARY KEY (" . $primary . ')) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci ROW_FORMAT=FIXED;';
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
            $keyElement->addChild("primaryKey", true);
            $keyElement->addChild("required", true);
            
            if ($model["primary-key-1-identity"]) {
                $keyElement->addChild("identity", true);
            }

            $columnName = $model["primary-key-2-name"];
            if ($columnName != "") {
                $keyElement = $definitionXml->addChild("column");
                $keyElement->addChild("name", $columnName);
                $keyElement->addChild("type", $model["primary-key-2-type"]);
                $keyElement->addChild("primaryKey", true);
                $keyElement->addChild("required", true);
            }
            
            $columnName = $model["primary-key-3-name"];
            if ($columnName != "") {
                $keyElement = $definitionXml->addChild("column");
                $keyElement->addChild("name", $columnName);
                $keyElement->addChild("type", $model["primary-key-3-type"]);
                $keyElement->addChild("primaryKey", true);
                $keyElement->addChild("required", true);
            }

            $createSql = self::getCreateSql($tableName, $definitionXml);
            $insertSql = self::sql()->insert("custom_entity", array("name" => $name, "description" => $model["entity-description"], "definition" => $definitionXml->asXml()));

            try {
                self::executeSql($insertSql, $createSql);
            } catch(DataAccessException $e) {
                return false;
            }
            
            return true;
        }

        private function createTableColumn($name, $tableName, $model) {
            $columnName = $model["column-name"];
            $columnType = $model["column-type"];

            $xml = self::getDefinition($name);
            if ($xml == NULL) {
                return false;
            }

            $column = $xml->addChild("column");
            $column->addChild("name", $columnName);
            $column->addChild("type", $columnType);

            if ($model["column-required"]) {
                $column->addChild("required", true);
            }

            if ($model["column-unique"]) {
                $column->addChild("unique", true);
            }

            // Set precision if needed.
            if ($columnType == "int") {
                $size = $model["column-int-size"];
                if (!empty($size)) {
                    $column->size = $size;
                }
            } else if ($columnType == "float") {
                $size = $model["column-float-size"];
                $decimals = $model["column-float-decimals"];
                if (!empty($size) && !empty($decimals)) {
                    $column->size = $size;
                    $column->decimals = $decimals;
                }
            }

            $typeDefinition = self::getTableColumnTypes($column);
            $dbType = self::getTableColumnDbType($typeDefinition, $column);

            $sql = array();

            if ($typeDefinition["hasColumn"]) {
                $alterSql = "ALTER TABLE `$tableName` ADD COLUMN `" . $columnName . "` $dbType";
                if ($column->required == true) {
                    $alterSql .= " NOT NULL";
                } else {
                    $alterSql .= " NULL";
                }

                if ($column->unique == true) {
                    $alterSql .= " UNIQUE";
                }

                $alterSql .= ";";
                $sql[] = $alterSql;
            }

            if ($columnType == "singlereference") {
                $referenceTable = $model["column-singlerefence-table"];
                $referenceColumn = $model["column-singlerefence-column"];
                $sql[] = "ALTER TABLE `$tableName` ADD FOREIGN KEY (`$columnName`) REFERENCES `$referenceTable`(`$referenceColumn`);";
            } else if ($columnType == "multireference-jointable") {
                $joinTable = $model["column-multireference-table"];
                $targetColumn = $model["column-multireference-targetcolumn"];
                $column->addChild("joinTable", $joinTable);
                $column->addChild("targetColumn", $targetColumn);
                $primaryKeysElements = $column->addChild("primaryKeyMappings");
                
                $primaryKeys = self::getPrimaryKeyColumns($xml);
                for ($i=0; $i < count($primaryKeys); $i++) { 
                    $primaryKey = $primaryKeys[$i];
                    $primaryKeyColumnName = (string)$primaryKey->name;
                    $mappedColumnName = $model["column-multireference-primarykey" . ($i + 1) . "-column"];
                    $primaryKeysElements->addChild("mappedTo", $mappedColumnName);
                    
                    $sql[] = "ALTER TABLE `$joinTable` ADD FOREIGN KEY (`$mappedColumnName`) REFERENCES `$tableName`(`$primaryKeyColumnName`);";
                }
            }

            $sql[] = self::getUpdateDefinitionSql($name, $xml);
            
            try {
                self::dataAccess()->transaction(function($da) use ($sql) {
                    foreach ($sql as $item) {
                        if (!empty($item)) {
                            $da->execute($item);
                        }
                    }
                });

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
            $tables = self::dataAccess()->fetchAll(self::sql()->select("custom_entity", array("name", "description")));
            $model = new ListModel();
            $model->items($tables);
            self::pushListModel($model);

            $model->render();
            $result = self::parseContent($template);
            
            self::popListModel();
            return $result;
        }

        public function getListTables() {
            return self::peekListModel();
        }

        public function getTableName() {
            return self::peekListModel()->field("name");
        }

        public function getTableDescription() {
            return self::peekListModel()->field("description");
        }

        public function listTableColumns($template, $name) {
            $tableName = self::ensureTableName($name);
            $xml = self::getDefinition($name);
            if ($xml == NULL) {
                return "";
            }

            $columns = array();
            foreach ($xml->column as $column) {
                $columns[] = $column;
            }
            // $columns = $xml->column;

            $model = new ListModel();
            $model->items($columns);
            self::pushListModel($model);

            $model->render();
            $result = self::parseContent($template);
            
            self::popListModel();
            return $result;
        }

        public function getListTableColumns() {
            return self::peekListModel();
        }

        public function getTableColumnName() {
            return self::peekListModel()->data()->name;
        }

        public function getTableColumnType() {
            $typeDefinition = self::getTableColumnTypes(self::peekListModel()->data());
            return $typeDefinition["name"];
        }

        public function getTableColumnPrimaryKey() {
            return self::peekListModel()->data()->primaryKey == TRUE;
        }

        public function getTableColumnRequired() {
            return self::peekListModel()->data()->required == TRUE;
        }

        public function getTableColumnUnique() {
            return self::peekListModel()->data()->unique == TRUE;
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
                    $typeDefinition = self::getTableColumnTypes($xml->column[$i]);
                    unset($xml->column[$i]);
                    break;
                }
            }

            $updateSql = self::getUpdateDefinitionSql($entityName, $xml);

            if ($typeDefinition["hasColumn"]) {
                $alterSql = "ALTER TABLE `$tableName` DROP COLUMN `$columnName`;";
            }

            self::executeSql($updateSql, $alterSql);
            self::parseContent($template);
        }

        private $tableLocalizationColumns;

        private function getLocalizableColumns($xml) {
            $columns = array();
            foreach ($xml->column as $column) {
                if ($column->primaryKey == true) {
                    continue;
                }
                
                $typeDefinition = self::getTableColumnTypes($column);
                if (!$typeDefinition["isLocalizable"]) {
                    continue;
                }
                
                $columns[] = array(
                    "name" => (string)$column->name
                );
            }

            return $columns;
        }

        private function updateXmlLocalizedColumns($xml, $columns) {
            foreach ($xml->column as $column) {
                if (in_array((string)$column->name, $columns)) {
                    $column->localized = true;
                } else {
                    unset($column->localized);
                }
            }
        }

        public function tableLocalizationEditor($name) {
            self::ensureTableName($name);
            $tableName = self::ensureTableLocalizationName($name);
            $xml = self::getDefinition($name);
            if ($xml == NULL) {
                return;
            }

            $model = new EditModel();
            self::pushEditModel($model);

            $columns = array();
            foreach ($xml->column as $column) {
                if ($column->localized == true) {
                    $columns[] = (string)$column->name;
                }
            }
            $model["columns"] = $columns;

            if (array_key_exists("ced-localizable-save", $_REQUEST)) {
                $model->submit(true);
                self::partialView("customentities/tableLocalizationEditor");
                $model->submit(false);

                $newColumns = $model["columns"];
                self::updateXmlLocalizedColumns($xml, $newColumns);

                $sql = array(self::getUpdateDefinitionSql($name, $xml));

                if (empty($newColumns)) {
                    $sql[] = "DROP TABLE IF EXISTS `$tableName`;";
                } else {
                    if (empty($columns)) {
                        $sql[] = self::getCreateLocalizationSql($tableName, $xml);
                    }

                    foreach ($columns as $columnName) {
                        if (!in_array($columnName, $newColumns)) {
                            $sql[] = "ALTER TABLE `$tableName` DROP COLUMN `$columnName`;";
                        }
                    }

                    foreach ($newColumns as $columnName) {
                        if (!in_array($columnName, $columns)) {
                            $column = self::findColumn($xml, $columnName);
                            $columnType = self::mapTypeToDb((string)$column->type);
                            $sql[] = "ALTER TABLE `$tableName` ADD COLUMN `" . $columnName . "` $columnType; ";
                        }
                    }
                }

                self::dataAccess()->transaction(function($da) use ($sql) {
                    foreach ($sql as $item) {
                        $da->execute($item);
                    }
                });
            }
            
            $model->render(true);
            
            $this->tableLocalizationColumns = self::getLocalizableColumns($xml);
            $result .= self::partialView("customentities/tableLocalizationEditor");
            $this->tableLocalizableColumns = null;
            
            $model->render(false);
            self::popEditModel();
            
            return $result;
        }

        private function getCreateLocalizationSql($tableName, $xml) {
            $columns = "";
            $primary = '';

            foreach ($xml->column as $column) {
                if ($column->primaryKey == true) {
                    $columnName = (string)$column->name;
                    $columnType = self::getTableColumnTypes($column, "db");
                    $columns = self::joinString($columns, "`$columnName` $columnType NULL");
                    $primary = self::joinString($primary, "`$columnName`");
                }
            }

            $columns .= ", `lang_id` int(11) NOT NULL";
            $primary .= ", `lang_id`";

            $sql = "CREATE TABLE `$tableName` ($columns, PRIMARY KEY ($primary)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci ROW_FORMAT=FIXED;";
            return $sql;
        }

        public function getTableLocalizationColumns() {
            return $this->tableLocalizationColumns;
        }
	}

?>