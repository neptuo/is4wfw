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

                    $typeDefinition = $this->getTableColumnTypes($column);
                    $dbType = $this->getTableColumnDbType($typeDefinition, $column);
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
            $tableName = self::TablePrefix . $name;

            $xml->engine = $engine;
            if ($model["entity-audit-log"]) {
                $audit = $xml->addChild("audit");
                $audit->log = true;
            }

            $ddlSql = [];
            $ddlSql[] = "create";

            $columnName = $model["primary-key-1-name"];
            $keyElement = $xml->addChild("column");
            $keyElement->addChild("name", $columnName);
            $keyElement->addChild("type", $model["primary-key-1-type"]);
            $keyElement->addChild("primaryKey", true);
            $keyElement->addChild("required", true);
            $this->setColumnTypeOptions($keyElement, $model, "primary-key-1");
            $this->setColumnAdditionalSqls($xml, $tableName, $keyElement, $model, "primary-key-1", $ddlSql);
            
            if ($model["primary-key-1-type"] == "int") {
                if ($model["primary-key-1-int-identity"]) {
                    $keyElement->addChild("identity", true);
                }
            }

            $columnName = $model["primary-key-2-name"];
            if ($columnName != "") {
                $keyElement = $xml->addChild("column");
                $keyElement->addChild("name", $columnName);
                $keyElement->addChild("type", $model["primary-key-2-type"]);
                $keyElement->addChild("primaryKey", true);
                $keyElement->addChild("required", true);
                $this->setColumnTypeOptions($keyElement, $model, "primary-key-2");
                $this->setColumnAdditionalSqls($xml, $tableName, $keyElement, $model, "primary-key-2", $ddlSql);
            }
            
            $columnName = $model["primary-key-3-name"];
            if ($columnName != "") {
                $keyElement = $xml->addChild("column");
                $keyElement->addChild("name", $columnName);
                $keyElement->addChild("type", $model["primary-key-3-type"]);
                $keyElement->addChild("primaryKey", true);
                $keyElement->addChild("required", true);
                $this->setColumnTypeOptions($keyElement, $model, "primary-key-3");
                $this->setColumnAdditionalSqls($xml, $tableName, $keyElement, $model, "primary-key-3", $ddlSql);
            }

            $ddlSql[0] = $this->getCreateSql($tableName, $xml);
            $dmlSql = $this->sql()->insert("custom_entity", array("name" => $name, "description" => $model["entity-description"], "definition" => $xml->asXml()));

            try {
                $timestamp = time();
                $this->dataAccess()->transaction(function($da) use ($ddlSql, $dmlSql, $name, $timestamp) {
                    foreach ($ddlSql as $item) {
                        if (!empty($item)) {
                            $da->execute($this->getAuditSql($name, $item, $timestamp));
                            $da->execute($item);
                        }
                    }

                    $da->execute($dmlSql);
                });

                return true;
            } catch (DataAccessException $e) {
                return false;
            }
        }

        private function setColumnTypeOptions($column, $model, $prefix) {
            if ($column->type == "int") {
                $size = $model["$prefix-int-size"];
                if (!empty($size)) {
                    $column->size = $size;
                }
            } else if ($column->type == "float") {
                $size = $model["$prefix-float-size"];
                $decimals = $model["$prefix-float-decimals"];
                if (!empty($size) && !empty($decimals)) {
                    $column->size = $size;
                    $column->decimals = $decimals;
                }
            } else if ($column->type == "varchar") {
                $size = $model["$prefix-varchar-size"];
                if (!empty($size)) {
                    $column->size = $size;
                }
            } else if ($column->type == "url") {
                $size = $model["$prefix-url-size"];
                if (!empty($size)) {
                    $column->size = $size;
                }
            }
        }

        private function setColumnAdditionalSqls($xml, $tableName, $column, $model, $prefix, &$ddlSql) {
            if ($column->type == "singlereference") {
                $referenceTable = $model["$prefix-singlerefence-table"];
                $referenceColumn = $model["$prefix-singlerefence-column"];
                $column->addChild("targetTable", $referenceTable);
                $column->addChild("targetColumn", $referenceColumn);

                $ddlSql[] = "ALTER TABLE `$tableName` ADD FOREIGN KEY (`$column->name`) REFERENCES `$referenceTable`(`$referenceColumn`);";
            } else if ($column->type == "multireference-jointable") {
                $joinTable = $model["$prefix-multireference-table"];
                $targetColumn = $model["$prefix-multireference-targetcolumn"];
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
                    $mappedColumnName = $model["$prefix-multireference-primarykey" . ($i + 1) . "-column"];
                    $primaryKeysElements->addChild("mappedTo", $mappedColumnName);
                    
                    $ddlSql[] = "ALTER TABLE `$joinTable` ADD FOREIGN KEY (`$mappedColumnName`) REFERENCES `$tableName`(`$primaryKeyColumnName`)$cascade;";
                }
            }
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
            $this->setColumnTypeOptions($column, $model, "column");

            $typeDefinition = $this->getTableColumnTypes($column);
            $dbType = $this->getTableColumnDbType($typeDefinition, $column);

            $ddlSql = array();

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
                $ddlSql[] = $alterSql;
            }

            $this->setColumnAdditionalSqls($xml, $tableName, $column, $model, "column", $ddlSql);

            $dmlSql = $this->getUpdateDefinitionSql($name, $xml);
            
            try {
                $timestamp = time();
                $this->dataAccess()->transaction(function($da) use ($ddlSql, $dmlSql, $name, $timestamp) {
                    foreach ($ddlSql as $item) {
                        if (!empty($item)) {
                            $da->execute($this->getAuditSql($name, $item, $timestamp));
                            $da->execute($item);
                        }
                    }

                    $da->execute($dmlSql);
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

            $deleteSql = "DROP TABLE `$tableName`;";
            $this->executeSql(
                $deleteSql, 
                $this->getAuditSql($name, $deleteSql),
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
            return $this->peekListModel()->currentItem()->name;
        }

        public function getTableColumnDescription() {
            return $this->peekListModel()->currentItem()->description;
        }

        public function getTableColumnType() {
            $typeDefinition = $this->getTableColumnTypes($this->peekListModel()->currentItem());
            return $typeDefinition["name"];
        }

        public function getTableColumnPrimaryKey() {
            return $this->peekListModel()->currentItem()->primaryKey == TRUE;
        }

        public function getTableColumnRequired() {
            return $this->peekListModel()->currentItem()->required == TRUE;
        }

        public function getTableColumnUnique() {
            return $this->peekListModel()->currentItem()->unique == TRUE;
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

            $this->executeSql($updateSql, $this->getAuditSql($entityName, $alterSql), $alterSql);
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

                $timestamp = time();
                $this->dataAccess()->transaction(function($da) use ($sql, $name, $timestamp) {
                    foreach ($sql as $item) {
                        $da->execute($this->getAuditSql($name, $item, $timestamp));
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

        public function listTableAudit($template, $name) {
            $xml = $this->getDefinition($name);
            if ($xml == null) {
                return;
            }
            
			$model = new ListModel();
			$this->pushListModel($model);

            $sql = $this->sql()->select("custom_entity_audit", ["timestamp", "sql"], ["entity" => $name]);
            $data = $this->dataAccess()->fetchAll($sql);
            
            $model->render();
            $model->items($data);

            $result = $template();
            
			$this->popListModel();
			return $result;
        }

        public function getListTableAudit() {
			return $this->peekListModel();
        }

        public function getTableAuditTimestamp() {
            return $this->peekListModel()->field("timestamp");
        }

        public function getTableAuditSql() {
			if ($this->hasListModel()) {
                return $this->peekListModel()->field("sql");
            }

            return $this->tableAuditSql;
        }

        private $tableAuditSql = "";

        public function tableAuditSql($template, $name, $timestamp) {
            $xml = $this->getDefinition($name);
            if ($xml == null) {
                return;
            }
            
            $da = $this->dataAccess();
            $sql = $this->sql()->select("custom_entity_audit", ["sql", "timestamp"], "`entity` = '" . $da->escape($name) . "'" . (!empty($timestamp) ? " AND `timestamp` >= " . $da->escape($timestamp) : ""));
            $data = $da->fetchAll($sql);

            $result = "";
            foreach ($data as $item) {
                if (!empty($result)) {
                    $result .= PHP_EOL;
                }
                
                $result .= $item["sql"];
                $result .= PHP_EOL;
                $result .= $this->getAuditSql($name, $item["sql"], $item["timestamp"]);
            }
            
            if (!empty($result)) {
                $result .= PHP_EOL;
            }

            $xml = $xml->asXml();
            $result .= $this->sql()->insertOrUpdate("custom_entity", ["name" => $name, "definition" => $xml], ["definition"]);

            $old = $this->tableAuditSql;
            $this->tableAuditSql = $result;

            $result = $template();

            $this->tableAuditSql = $old;
            return $result;
        }
	}

?>