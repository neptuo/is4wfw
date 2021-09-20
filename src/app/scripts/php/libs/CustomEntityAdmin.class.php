<?php

	require_once("CustomEntityBase.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/EditModel.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/ListModel.class.php");

	class CustomEntityAdmin extends CustomEntityBase {

        private $tables;
        private $columns;

        private $tableEngines = array(
            array(
                "key" => "InnoDB",
                "name" => "InnoDB"
            ),
            array(
                "key" => "MyISAM",
                "name" => "MyISAM"
            )
        );

		public function __construct() {
            $this->tables = new Stack();
            $this->columns = new Stack();
        }

        private function getTableEngineSql($xml) {
            $engine = "ENGINE=" . (string)$xml->engine;
            if ($xml->engine == "MyISAM") {
                $engine .= " ROW_FORMAT=FIXED";
            }

            return $engine;
        }

        private function getCreateSql($name, $xml) {
            $columns = "";
            $primary = '';

            foreach ($xml->column as $column) {
                if ($column->primaryKey == true) {
                    $columnName = (string)$column->name;
                    $dbType = $this->getTableColumnTypes($column, "db");
                    $columns = StringUtils::join($columns, "`$columnName` $dbType NOT NULL");

                    if ($column->identity == true) {
                        $columns .= " AUTO_INCREMENT";
                    }

                    $primary = StringUtils::join($primary, "`$columnName`");
                }
            }

            $engine = $this->getTableEngineSql($xml);
            $sql = "CREATE TABLE `$name` ($columns, PRIMARY KEY ($primary)) $engine DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;";
            return $sql;
        }

        private function executeSql($sql1, $sql2 = "", $sql3 = "") {
            $this->dataAccess()->transaction();

            try {
                $this->dataAccess()->execute($sql1);

                if (!empty($sql2)) {
                    $this->dataAccess()->execute($sql2);
                }

                if (!empty($sql3)) {
                    $this->dataAccess()->execute($sql3);
                }

                $this->dataAccess()->commit();
            } catch(DataAccessException $e) {
                $this->dataAccess()->rollback();
                throw $e;
            }
        }

        private function createTable($model) {
            $xml = new SimpleXMLElement("<definition />");
            $name = $model["entity-name"];
            $engine = $model["entity-engine"];
            $tableName = $this->TablePrefix . $name;

            $xml->engine = $engine;
            if ($model["entity-audit-log"]) {
                $audit = $xml->addChild("audit");
                $audit->log = true;
            }

            $columnName = $model["primary-key-1-name"];
            $keyElement = $xml->addChild("column");
            $keyElement->addChild("name", $columnName);
            $keyElement->addChild("type", $model["primary-key-1-type"]);
            $keyElement->addChild("primaryKey", true);
            $keyElement->addChild("required", true);
            
            if ($model["primary-key-1-identity"]) {
                $keyElement->addChild("identity", true);
            }

            $columnName = $model["primary-key-2-name"];
            if ($columnName != "") {
                $keyElement = $xml->addChild("column");
                $keyElement->addChild("name", $columnName);
                $keyElement->addChild("type", $model["primary-key-2-type"]);
                $keyElement->addChild("primaryKey", true);
                $keyElement->addChild("required", true);
            }
            
            $columnName = $model["primary-key-3-name"];
            if ($columnName != "") {
                $keyElement = $xml->addChild("column");
                $keyElement->addChild("name", $columnName);
                $keyElement->addChild("type", $model["primary-key-3-type"]);
                $keyElement->addChild("primaryKey", true);
                $keyElement->addChild("required", true);
            }

            $createSql = $this->getCreateSql($tableName, $xml);
            $insertSql = $this->sql()->insert("custom_entity", array("name" => $name, "description" => $model["entity-description"], "definition" => $xml->asXml()));

            try {
                $this->executeSql($insertSql, $createSql);
            } catch(DataAccessException $e) {
                return false;
            }
            
            return true;
        }

        private function createTableColumn($name, $tableName, $model) {
            $columnName = $model["column-name"];
            $columnType = $model["column-type"];
            $columnDescription = $model["column-description"];

            $xml = $this->getDefinition($name);
            if ($xml == NULL) {
                return false;
            }

            $column = $xml->addChild("column");
            $column->addChild("name", $columnName);
            $column->addChild("type", $columnType);
            $column->addChild("description", $columnDescription);

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
            } else if ($columnType == "varchar") {
                $size = $model["column-varchar-size"];
                if (!empty($size)) {
                    $column->size = $size;
                }
            } else if ($columnType == "url") {
                $size = $model["column-url-size"];
                if (!empty($size)) {
                    $column->size = $size;
                }
            }

            $typeDefinition = $this->getTableColumnTypes($column);
            $dbType = $this->getTableColumnDbType($typeDefinition, $column);

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
                $column->addChild("targetTable", $referenceTable);
                $column->addChild("targetColumn", $referenceColumn);

                $sql[] = "ALTER TABLE `$tableName` ADD FOREIGN KEY (`$columnName`) REFERENCES `$referenceTable`(`$referenceColumn`);";
            } else if ($columnType == "multireference-jointable") {
                $joinTable = $model["column-multireference-table"];
                $targetColumn = $model["column-multireference-targetcolumn"];
                $column->addChild("joinTable", $joinTable);
                $column->addChild("targetColumn", $targetColumn);
                $primaryKeysElements = $column->addChild("primaryKeyMappings");
                
                $cascade = "";
                if ($xml->engine == "InnoDB") {
                    $cascade = " ON DELETE CASCADE";
                }

                $primaryKeys = $this->getPrimaryKeyColumns($xml);
                for ($i=0; $i < count($primaryKeys); $i++) { 
                    $primaryKey = $primaryKeys[$i];
                    $primaryKeyColumnName = (string)$primaryKey->name;
                    $mappedColumnName = $model["column-multireference-primarykey" . ($i + 1) . "-column"];
                    $primaryKeysElements->addChild("mappedTo", $mappedColumnName);
                    
                    $sql[] = "ALTER TABLE `$joinTable` ADD FOREIGN KEY (`$mappedColumnName`) REFERENCES `$tableName`(`$primaryKeyColumnName`)$cascade;";
                }
            }

            $sql[] = $this->getUpdateDefinitionSql($name, $xml);
            
            try {
                $this->dataAccess()->transaction(function($da) use ($sql) {
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
            $model = $this->getEditModel();

            if ($model->isSubmit()) {
                $this->partialView("customentities/tableCreator");
            }

            if ($model->isSave()) {
                $this->createTable($model);
            }

            if ($model->isRender()) {
                $result = $this->partialView("customentities/tableCreator");
                return $result;
            }
        }
        
        public function tableEditor($template, $name) {
            $model = $this->getEditModel();

            $xml = $this->getDefinition($name);
            if ($xml == NULL) {
                return false;
            }

            if ($model->isLoad()) {
                $sql = $this->sql()->select("custom_entity", ["description", "definition"], ["name" => $name]);

                $entity = $this->dataAccess()->fetchSingle($sql);
                $model->set("entity-description", -1, $entity["description"]);
                
                if (!empty($entity["definition"])) {
                    $xml = new SimpleXMLElement($entity["definition"]);
                    $model->set("entity-audit-log", -1, $this->hasAuditLog($xml) == true);
                }
            }

            if ($model->isSubmit()) {
                $template();
            }
            
            if ($model->isSave()) {
                $entity = [
                    "description" => $model->get("entity-description", -1)
                ];

                if ($model->hasKey("entity-audit-log")) {
                    $xml = $this->getDefinition($name);
                    if ($model["entity-audit-log"]) {
                        if (!$this->hasAuditLog($xml)) {
                            $audit = $xml->addChild("audit");
                            $audit->log = true;
                            $entity["definition"] = $xml->asXml();
                        }
                    } else {
                        if ($this->hasAuditLog($xml)) {
                            unset($xml->audit);
                            $entity["definition"] = $xml->asXml();
                        }
                    }
                }

                $updateSql = $this->sql()->update("custom_entity", $entity, ["name" => $name]);
                $this->dataAccess()->execute($updateSql);
            }
            
            if ($model->isRender()) {
                $result = $template();
                return $result;
            }
        }

        public function tableDeleter($template, $name) {
            $tableName = $this->ensureTableName($name);

            $this->executeSql(
                "DROP TABLE `$tableName`;", 
                $this->sql()->delete("custom_entity", array("name" => $name))
            );
            $template();
        }

        public function listTables($template) {
            $tables = $this->dataAccess()->fetchAll($this->sql()->select("custom_entity", array("name", "description", "definition"), array(), array("name" => "asc")));
            for ($i=0; $i < count($tables); $i++) { 
                $tables[$i]["definition"] = new SimpleXMLElement($tables[$i]["definition"]);
            }

            $model = new ListModel();
            $model->items($tables);
            $this->pushListModel($model);

            $model->render();
            $result = $template();
            
            $this->popListModel();
            return $result;
        }

        public function getListTables() {
            return $this->peekListModel();
        }

        public function getTableName() {
            return $this->peekListModel()->field("name");
        }

        public function getTableDescription() {
            return $this->peekListModel()->field("description");
        }

        public function getTableAuditLog() {
            return $this->hasAuditLog($this->peekListModel()->field("definition"));
        }

        public function listTableColumns($template, $name) {
            $tableName = $this->ensureTableName($name);
            $xml = $this->getDefinition($name);
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
            $this->pushListModel($model);

            $model->render();
            $result = $template();
            
            $this->popListModel();
            return $result;
        }

        public function getListTableColumns() {
            return $this->peekListModel();
        }

        public function getTableColumnName() {
            return $this->peekListModel()->data()->name;
        }

        public function getTableColumnDescription() {
            return $this->peekListModel()->data()->description;
        }

        public function getTableColumnType() {
            $typeDefinition = $this->getTableColumnTypes($this->peekListModel()->data());
            return $typeDefinition["name"];
        }

        public function getTableColumnPrimaryKey() {
            return $this->peekListModel()->data()->primaryKey == TRUE;
        }

        public function getTableColumnRequired() {
            return $this->peekListModel()->data()->required == TRUE;
        }

        public function getTableColumnUnique() {
            return $this->peekListModel()->data()->unique == TRUE;
        }

        public function tableColumnCreator($name) {
            $model = parent::getEditModel();
            $tableName = parent::ensureTableName($name, $model);

            if ($model->isSubmit()) {
                $this->partialView("customentities/tableColumnCreator");
            }

            if ($model->isSave()) {
                $this->createTableColumn($name, $tableName, $model);
            }

            if ($model->isRender()) {
                $result = $this->partialView("customentities/tableColumnCreator");
                return $result;
            }
        }
        
        public function tableColumnEditor($template, $tableName, $columnName) {
            $model = $this->getEditModel();

            $xml = $this->getDefinition($tableName);
            if ($xml == NULL) {
                return false;
            }

            if ($model->isLoad()) {
                for ($i=0; $i < count($xml->column); $i++) { 
                    if ($xml->column[$i]->name == $columnName) {
                        $model->set("column-description", -1, $xml->column[$i]->description);
                        break;
                    }
                }
            }

            if ($model->isSubmit()) {
                $template();
            }
            
            if ($model->isSave()) {
                for ($i=0; $i < count($xml->column); $i++) { 
                    if ($xml->column[$i]->name == $columnName) {
                        $xml->column[$i]->description = $model->get("column-description", -1);
                        break;
                    }
                }
    
                $updateSql = $this->getUpdateDefinitionSql($tableName, $xml);
                $this->dataAccess()->execute($updateSql);
            }
            
            if ($model->isRender()) {
                $result = $template();
                return $result;
            }
        }

        public function tableColumnDeleter($template, $entityName, $columnName) {
            $tableName = $this->ensureTableName($entityName);
            $xml = $this->getDefinition($entityName);
            if ($xml == NULL) {
                return;
            }

            for ($i=0; $i < count($xml->column); $i++) { 
                if ($xml->column[$i]->name == $columnName) {
                    $typeDefinition = $this->getTableColumnTypes($xml->column[$i]);
                    unset($xml->column[$i]);
                    break;
                }
            }

            $updateSql = $this->getUpdateDefinitionSql($entityName, $xml);

            if ($typeDefinition["hasColumn"]) {
                $alterSql = "ALTER TABLE `$tableName` DROP COLUMN `$columnName`;";
            }

            $this->executeSql($updateSql, $alterSql);
            $template();
        }

        private $tableLocalizationColumns;

        private function getLocalizableColumns($xml) {
            $columns = array();
            foreach ($xml->column as $column) {
                if ($column->primaryKey == true) {
                    continue;
                }
                
                $typeDefinition = $this->getTableColumnTypes($column);
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
            $model = parent::getEditModel();
            $tableName = parent::ensureTableLocalizationName($name, $model);
            $xml = $this->getDefinition($name);
            if ($xml == null) {
                return;
            }

            if ($model->isLoad()) {
                $columns = array();
                foreach ($xml->column as $column) {
                    if ($column->localized == true) {
                        $columns[] = (string)$column->name;
                    }
                }
                $model["columns"] = $columns;
            }

            if ($model->isSubmit()) {
                $this->partialView("customentities/tableLocalizationEditor");
            }

            if ($model->isSave()) {
                $newColumns = $model["columns"];
                $this->updateXmlLocalizedColumns($xml, $newColumns);

                $sql = array($this->getUpdateDefinitionSql($name, $xml));

                if (empty($newColumns)) {
                    $sql[] = "DROP TABLE IF EXISTS `$tableName`;";
                } else {
                    if (empty($columns)) {
                        $sql[] = $this->getCreateLocalizationSql($tableName, $xml);
                    }

                    foreach ($columns as $columnName) {
                        if (!in_array($columnName, $newColumns)) {
                            $sql[] = "ALTER TABLE `$tableName` DROP COLUMN `$columnName`;";
                        }
                    }

                    foreach ($newColumns as $columnName) {
                        if (!in_array($columnName, $columns)) {
                            $column = $this->findColumn($xml, $columnName);
                            $columnType = $this->getTableColumnTypes($column, "db");
                            $sql[] = "ALTER TABLE `$tableName` ADD COLUMN `" . $columnName . "` $columnType; ";
                        }
                    }
                }

                $this->dataAccess()->transaction(function($da) use ($sql) {
                    foreach ($sql as $item) {
                        $da->execute($item);
                    }
                });
            }
            
            if ($model->isRender()) {
                $this->tableLocalizationColumns = $this->getLocalizableColumns($xml);
                $result = $this->partialView("customentities/tableLocalizationEditor");
                $this->tableLocalizableColumns = null;
                return $result;
            }
        }

        private function getCreateLocalizationSql($tableName, $xml) {
            $columns = "";
            $primary = '';

            foreach ($xml->column as $column) {
                if ($column->primaryKey == true) {
                    $columnName = (string)$column->name;
                    $columnType = $this->getTableColumnTypes($column, "db");
                    $columns = StringUtils::join($columns, "`$columnName` $columnType NOT NULL");
                    $primary = StringUtils::join($primary, "`$columnName`");
                }
            }

            $columns .= ", `lang_id` int(11) NOT NULL";
            $primary .= ", `lang_id`";

            $engine = $this->getTableEngineSql($xml);
            $sql = "CREATE TABLE `$tableName` ($columns, PRIMARY KEY ($primary)) $engine DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;";
            return $sql;
        }

        public function getTableLocalizationColumns() {
            return $this->tableLocalizationColumns;
        }

        public function getTableEngines() {
            return $this->tableEngines;
        }
	}

?>