<?php

	require_once("CustomEntityBase.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/EditModel.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/FilterModel.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/ListModel.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/Stack.class.php");

	class CustomEntity extends CustomEntityBase {

        private $tagPrefix;
        private $urlProperties;
        private $urlResolvers;

		public function __construct($tagPrefix) {
            $this->tagPrefix = $tagPrefix;
            $this->urlProperties = [];
            $this->urlResolvers = [];
        }

        private function parseUserValue($column, $value) {
            $type = $this->getTableColumnTypes($column);
            if ($type == NULL) {
                return NULL;
            }

            return $type["fromUser"]($value, $column);
        }

        private function parseDbValue($column, $value) {
            $type = $this->getTableColumnTypes($column);
            if ($type == NULL) {
                return NULL;
            }

            return $type["fromDb"]($value, $column);
        }

        private function prepareValuesFromModel($xml, EditModel $model) {
            $columns = array();
            $extras = array();
            $external = array();
            foreach ($xml->column as $column) {
                $columnName = (string)$column->name;
                $columnType = (string)$column->type;
                if ($model->hasKey($columnName)) {
                    $value = $model[$columnName];
                    if (is_array($value) && array_key_exists("type", $value)) {
                        $extras[$columnName] = $value;
                    } else {
                        $typeDefinition = $this->getTableColumnTypes($column);
                        if ($typeDefinition["hasColumn"]) {
                            $value = $this->parseUserValue($column, $value);
                            $columns[$columnName] = $value;
                        } else {
                            $value = $this->parseUserValue($column, $value);
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

        private function prepareLocalizedValuesFromModel($xml, EditModel $model, $langIds) {
            $langs = array();
            foreach ($langIds as $langId) {
                $columns = array();
                foreach ($xml->column as $column) {
                    if ($column->localized == true) {
                        $columnName = (string)$column->name;
                        $columnType = (string)$column->type;

                        $key = "$columnName:$langId";
                        if ($model->hasKey($key)) {
                            $value = $model[$key];
                            $value = $this->parseUserValue($column, $value);
                            $columns[$columnName] = $value;
                        }
                    }
                }

                if (!empty($columns)) {
                    $langs[$langId] = $columns;
                }
            }

            return $langs;
        }

        private function updateExternals($values, $xml, $model) {
            foreach ($values["external"] as $columnName => $external) {
                if ($external["type"] == "multireference-jointable") {
                    $column = $this->findColumn($xml, $columnName);
                    $this->updateJoinTable($xml, $column, $model);
                }
            }
        }

        private function updateLocalized($tableName, $xml, $model, $primaryKeys, $langIds, $isNewRecord = false) {
            $langs = $this->prepareLocalizedValuesFromModel($xml, $model, $langIds);
            foreach ($langs as $langId => $lang) {
                // Check if update is possible.
                if ($isNewRecord) {
                    $count["count"] = 0;
                } else {
                    $count = $this->dataAccess()->fetchSingle($this->sql()->count($tableName, $primaryKeys));
                }

                // Insert or Update.
                if ($count["count"] == 0) {
                    $lang["lang_id"] = $langId;
                    $lang = array_merge($lang, $primaryKeys);
                    $sql = $this->sql()->insert($tableName, $lang);
                } else {
                    $primaryKeys["lang_id"] = $langId;
                    $sql = $this->sql()->update($tableName, $lang, $primaryKeys);
                }

                $this->dataAccess()->execute($sql);
            }
        }

        private function executeEmptyDirectory($model, $tableName, $columnName, $extra, $primaryKeys) {
            $da = parent::dataAccess();

            $fa = new FileAdmin();
            $directoryName = StringUtils::format($extra['nameFormat'], $model);
            $directoryName = StringUtils::format($directoryName, $primaryKeys);
            $directory = $fa->createDirectory($extra['parentDirId'], $directoryName);
            
            $sql = $this->sql()->update($tableName, array($columnName => $directory['id']), $primaryKeys);
            $da->execute($sql);
        }

        private function executeCreateIfEmpty($tableName, $columnName, $extra, $primaryKeys) {
            $da = parent::dataAccess();

            $source = $extra["source"];
            $value = $extra["value"];
            $keyColumn = $extra["keyColumn"];
            $valueColumn = $extra["valueColumn"];
            $values = $extra["values"];

            $newId = 0;
            $sql = parent::sql()->select($source, array($keyColumn), array($valueColumn => $value));
            $item = $da->fetchSingle($sql);
            if (empty($item)) {
                $values[$valueColumn] = $value;
                $sql = parent::sql()->insert($source, $values);
                $da->execute($sql);
                $newId = $da->getLastId($sql);
            } else {
                $newId = $item[$keyColumn];
            }
            
            if ($newId != 0) {
                $sql = $this->sql()->update($tableName, array($columnName => $newId), $primaryKeys);
                $da->execute($sql);
            }
        }
        
        private function insert($tableName, $tableLocalizationName, $xml, $keys, $model, $langIds, $deleteIfEmpty = false) {
            $da = parent::dataAccess();
            $keysModel = new EditModel();
            $keysModel->copyFrom($keys);

            $values = $this->prepareValuesFromModel($xml, $model);
            $primaryKeys = $this->prepareValuesFromModel($xml, $keysModel)["columns"];

            if ($deleteIfEmpty && $this->isEmpty($values["columns"])) {
                return;
            }

            foreach ($keys as $key => $value) {
                $values["columns"][$key] = $value;
            }

            $sql = $this->sql()->insert($tableName, $values["columns"]);

            // Execute insert.
            $da->execute($sql);
            
            // Get last identity value if inserted.
            $identity = $this->findIdentityColumn($xml);
            if ($identity != NULL) {
                $id = $da->getLastId();
                $model[(string)$identity->name] = $id;
            }

            // Prepare filter/keys for additional inserts/updates.
            $primaryKeys = array();
            foreach ($xml->column as $column) {
                if ($column->primaryKey == TRUE) {
                    $primaryKeys[(string)$column->name] = $model[(string)$column->name];
                }
            }
            
            // Process extras.
            foreach ($values["extras"] as $columnName => $extra) {
                if ($extra["type"] == "emptyDirectory") {
                    $this->executeEmptyDirectory($model, $tableName, $columnName, $extra, $primaryKeys);
                } else if ($extra["type"] == "createIfEmpty") {
                    $this->executeCreateIfEmpty($tableName, $columnName, $extra, $primaryKeys);
                }
            }

            // Process external tables.
            $this->updateExternals($values, $xml, $model);

            // Process localization.
            $this->updateLocalized($tableLocalizationName, $xml, $model, $primaryKeys, $langIds);

            if ($this->hasAuditLog($xml)) {
                $this->audit($tableName, "insert", $primaryKeys);
            }
        }

        private function isChanged(EditModel $model, Array $values) {
            $entity = $model->metadata("entity");
            if ($entity == null || count($entity) != count($values)) {
                return true;
            }

            foreach ($values as $key => $value) {
                if (!array_key_exists($key, $entity) || $value !== $entity[$key]) {
                    return true;
                }
            }

            return false;
        }

        private function isEmpty(Array $values) {
            foreach ($values as $key => $value) {
                if (!empty($value)) {
                    return false;
                }
            }

            return true;
        }
        
        private function update($tableName, $tableLocalizationName, $xml, $keys, EditModel $model, $langIds, $deleteIfEmpty = false) {
            $da = parent::dataAccess();
            $keysModel = new EditModel();
            $keysModel->copyFrom($keys);

            $values = $this->prepareValuesFromModel($xml, $model);
            $primaryKeys = $this->prepareValuesFromModel($xml, $keysModel)["columns"];

            // If we have chnaged columns.
            if (count($values["columns"]) > 0 && $this->isChanged($model, $values["columns"])) {
                if ($deleteIfEmpty && $this->isEmpty($values["columns"])) {
                    $sql = $this->sql()->delete($tableName, $primaryKeys);
                    $da->execute($sql);

                    if ($this->hasAuditLog($xml)) {
                        $this->audit($tableName, "delete", $primaryKeys);
                    }

                    return;
                }
                
                // Execute update.
                $sql = $this->sql()->update($tableName, $values["columns"], $primaryKeys);
                $da->execute($sql);
            }
            
            // Process extras.
            foreach ($values["extras"] as $columnName => $extra) {
                if ($extra["type"] == "emptyDirectory") {
                    $directorySql = parent::sql()->select($tableName, [$columnName], $primaryKeys);
                    $directory = $da->fetchSingle($directorySql);
                    if (empty($directory[$columnName])) {
                        $this->executeEmptyDirectory($model, $tableName, $columnName, $extra, $primaryKeys);
                    } else if(array_key_exists("renameOnUpdate", $extra) && $extra["renameOnUpdate"]) {
                        $directoryName = StringUtils::format($extra['nameFormat'], $model);
                        $directoryName = StringUtils::format($directoryName, $primaryKeys);
                        $directoryNameSql = parent::sql()->update("directory", ["name" => $directoryName], ["id" => $directory[$columnName]]);
                        $da->execute($directoryNameSql);
                    }
                } else if ($extra["type"] == "createIfEmpty") {
                    $this->executeCreateIfEmpty($tableName, $columnName, $extra, $primaryKeys);
                }
            }

            // Process external tables.
            $this->updateExternals($values, $xml, $model);

            // Process localization.
            $this->updateLocalized($tableLocalizationName, $xml, $model, $primaryKeys, $langIds);

            if ($this->hasAuditLog($xml)) {
                $this->audit($tableName, "update", $primaryKeys);
            }
        }

        private function loadModel($name, $xml, $keys, EditModel $model) {
            $columns = array();
            $external = array();
            foreach ($xml->column as $column) {
                $columnName = (string)$column->name;
                $columnType = (string)$column->type;
                if ($model->hasKey($columnName)) {
                    $typeDefinition = $this->getTableColumnTypes($column);
                    if (!$typeDefinition["hasColumn"]) {
                        if ($columnType == "multireference-jointable") {
                            $this->registerPrimaryKeysToModel($xml, $model);

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
                if ($model->hasKey($columnName)) {
                    $typeDefinition = $this->getTableColumnTypes($column);
                    if ($typeDefinition["hasColumn"]) {
                        $columns[] = $columnName;
                    }
                }
            }

            // Load main data.
            $sql = parent::sql()->select($name, $columns, $keys);
            $data = parent::dataAccess()->fetchSingle($sql);
            if (empty($data)) {
                // We got keys, but the item doesn't exist.
                return false;
            }

            // Correctly parse all data taken from DB.
            foreach ($data as $key => $value) {
                $column = parent::findColumn($xml, $key);
                if ($column != null) {
                    $data[$key] = $this->parseDbValue($column, $value);
                }
            }
            
            $model->metadata("entity", $data);
            foreach ($model as $key => $item) {
                $model[$key] = $data[$key];
            }

            // Load externals.
            foreach ($external as $columnName => $item) {
                if ($item["type"] == "multireference-jointable") {
                    $value = $this->loadJoinTableData($xml, $model, $columnName);
                    $model[$columnName] = $value;
                }
            }

            return true;
        }

        private function loadLocalizedModel($tableName, $xml, $keys, EditModel $model, $langIds) {
            $columns = array();

            // Find model keys.
            foreach ($xml->column as $column) {
                $columnName = (string)$column->name;

                foreach ($langIds as $langId) {
                    $key = "$columnName:$langId";
                    if ($model->hasKey($key)) {
                        $columns[] = $columnName;
                        break;
                    }
                }
            }

            // Load data.
            if (!empty($columns)) {
                foreach ($langIds as $langId) {
                    $localizableKeys = $keys;
                    $localizableKeys["lang_id"] = $langId;
                    $sql = $this->sql()->select($tableName, $columns, $localizableKeys);
                    $data = $this->dataAccess()->fetchSingle($sql);
                    foreach ($columns as $column) {
                        $model["$column:$langId"] = $data[$column];
                    }
                }
            }
        }

        private function getDeleteSql($name, $params) {
            return $this->sql()->delete($name, $params);
        }

		public function form($template, $name, $deleteIfEmpty = false, $langIds = "", $keys = array()) {
            $model = parent::getEditModel();
            $tableName = $this->ensureTableName($name, $model);
            $tableLocalizationName = $this->ensureTableLocalizationName($name, $model);
            $xml = parent::getDefinition($name, $model);
            $langIds = explode(",", $langIds);
            $keys = ArrayUtils::removeKeysWithEmptyValues($keys);

            if (!$model->hasMetadataKey("isUpdate")) {
                $model->metadata("isUpdate", count($keys) > 0);
            }

            // Load data based on fields in template.
            if ($model->isLoad() && $model->metadata("isUpdate")) {
                $model->copyFrom($keys);
                
                $model->registration();
                $template();
                $model->registration(false);

                $exists = $this->loadModel($tableName, $xml, $keys, $model, $langIds);
                if ($exists) {
                    $this->loadLocalizedModel($tableLocalizationName, $xml, $keys, $model, $langIds);
                } else {
                    $model->metadata("isUpdate", false);
                }
            }

            // Submit if model is leased or isSubmit.
            if ($model->isSubmit()) {
                $template();
            }

            // Save if model is leased or isSubmit.
            if ($model->isSave()) {
                if ($model->metadata("isUpdate")) {
                    $this->update($tableName, $tableLocalizationName, $xml, $keys, $model, $langIds, $deleteIfEmpty);
                } else {
                    $this->insert($tableName, $tableLocalizationName, $xml, $keys, $model, $langIds, $deleteIfEmpty);
                }
            }

            // AfterSave if model is leased or isSubmit.
            if ($model->isSaved()) {
                $model->copyFrom($keys);
                $template();
            }

            if ($model->isRender()) {
                $result = $template();
                return $result;
            }
        }

        public function saveFullTag($template, $name, $langIds = "", $keys = []) {
            $model = $this->getEditModel();
            if ($model->isSave() || $model->isSaved()) {
                $tableName = $this->ensureTableName($name, $model);
                $tableLocalizationName = $this->ensureTableLocalizationName($name, $model);
                $xml = $this->getDefinition($name, $model);
                $langIds = explode(",", $langIds);
                $keys = ArrayUtils::removeKeysWithEmptyValues($keys);
            }
            
            // Save if model is leased or isSubmit.
            if ($model->isSave()) {
                if (!$model->hasMetadataKey("isUpdate")) {
                    $model->metadata("isUpdate", count($keys) > 0);
                }

                $template();

                if ($model->metadata("isUpdate")) {
                    $this->update($tableName, $tableLocalizationName, $xml, $keys, $model, $langIds, false);
                } else {
                    $this->insert($tableName, $tableLocalizationName, $xml, $keys, $model, $langIds, false);
                }
            }

            // AfterSave if model is leased or isSubmit.
            if ($model->isSaved()) {
                $model->copyFrom($keys);
                $template();
            }
        }

        public function emptyDirectory($name, $parentDirId, $nameFormat, $renameOnUpdate = false) {
            $model = parent::getEditModel();
            if ($model->isSubmit()) {
                $model[$name] = array(
                    "type" => "emptyDirectory",
                    "parentDirId" => $parentDirId,
                    "nameFormat" => $nameFormat,
                    "renameOnUpdate" => $renameOnUpdate
                );
            }
        }

        public function createIfEmpty($template, $name, $nameIndex, $source, $keyColumn, $valueColumn, $tableName = "", $values = array()) {
            $model = parent::getEditModel();
            if ($model->isSubmit()) {
                $value = $model->request($name, $nameIndex);
                if ($value != "") {
                    $isEmpty = false;
                    if (is_array($source)) {
                        foreach ($source as $item) {
                            if ($item[$keyColumn] == $value) {
                                $isEmpty = false;
                                break;
                            }
                        }

                        $source = $tableName;
                        $isEmpty = true;
                    } else {
                        $sql = parent::sql()->count($source, array($keyColumn => $value));
                        $count = parent::dataAccess()->fetchSingle($sql);
                        if ($count["count"] == 0) {
                            $isEmpty = true;
                        }
                    }
                    
                    if ($isEmpty) {
                        $modelValue = array(
                            "type" => "createIfEmpty",
                            "source" => $source,
                            "value" => $value,
                            "keyColumn" => $keyColumn,
                            "valueColumn" => $valueColumn,
                            "values" => $values
                        );
                        
                        $model->set($name, $nameIndex, $modelValue);
                        return;
                    }
                }
            }

            return $template();
        }

        public function getListData() {
            return $this->peekListModel();
        }

        private function ensureListLangIds($langIds, $model) {
            if (!is_array($langIds)) {
                $langIds = empty($langIds) ? [] : explode(",", $langIds);
            }

            if (!empty($langIds)) {
                $model->metadata("langIds", $langIds);
            }

            return $langIds;
        }

        private $fieldMetadata = [];

        private function setFieldMetadata($name, $key, $value) {
            $this->fieldMetadata[$name][$key] = $value;
        }

        public function getList($template, $name, $fields = [], $filter = [], $groupBy = "", $having = [], $orderBy = [], $paging = null, $langIds = "") {
            $tableName = $this->ensureTableName($name);

            $oldFieldMetadata = $this->fieldMetadata;
            $this->fieldMetadata = [];

            $model = new ListModel();
            $this->pushListModel($model);

            $model->registration();
            if (empty($fields) || !is_array($fields)) {
                $template(ParsedTemplateConfig::filtered($this->tagPrefix, ["register"], ["*"]));
            } else {
                foreach ($fields as $field) {
                    $this->register($field["name"], $field["alias"], $field["aggregation"], $field["function"]);
                }
            }
            $model->registration(false);

            $langIds = $this->ensureListLangIds($langIds, $model);
            
            if (empty($groupBy)) {
                $groupBy = null;
            } else {
                $groupBy = explode(",", $groupBy);
            }

            $isPagingModel = $paging instanceof PagingModel;
            
            $result = "";

            if (parent::isFilterModel($filter)) {
                $filter = $filter[""];
                $tableName = $filter->wrapTableName($tableName);
                $filter = $filter->toSql();
            } else {
                $filter = ArrayUtils::removeKeysWithEmptyValues($filter);
            }

            if ($this->isFilterModel($having)) {
                $having = $having[""];
                $having = $having->toSql();
            } else {
                $having = ArrayUtils::removeKeysWithEmptyValues($having);
            }
            
            if ($this->isSortModel($orderBy)) {
                $orderBy = $orderBy[""];
            } else {
                $orderBy = ArrayUtils::removeKeysWithEmptyValues($orderBy);
            }

            $fields = $model->fields();
            $xml = null;
            $isAliasRequired = false;
            if (!empty($langIds)) {
                $xml = parent::getDefinition($name);
                $isAliasRequired = true;
                $primaryKeys = $this->getPrimaryKeyColumns($xml);
            }

            foreach ($fields as $key => $value) {
                $isProcessed = false;
                
                $columnName = null;
                $columnAlias = null;
                if (array_key_exists($value, $this->fieldMetadata)) {
                    $metadata = $this->fieldMetadata[$value];
                    if (array_key_exists("alias", $metadata)) {
                        $columnName = $metadata["column"];
                        $columnAlias = $metadata["alias"];
                    }
                }

                if (strpos($columnName ?? $value, ".") !== false) {
                    $parts = explode(".", $columnName ?? $value, 2);
                    if ($xml == null) {
                        $xml = parent::getDefinition($name);
                    }

                    $column = parent::findColumn($xml, $parts[0]);
                    $columnType = (string)$column->type;
                    if ($columnType == "singlereference") {
                        $targetTable = (string)$column->targetTable;
                        $targetColumn = (string)$column->targetColumn;
                        $fields[$key] = array(
                            "select" => array(
                                "column" => $parts[1],
                                "alias" => $columnAlias ?? $value
                            ),
                            "leftjoin" => array(
                                "source" => $parts[0],
                                "target" => $targetColumn,
                                "table" => $targetTable,
                                "alias" => $parts[0]
                            )
                        );
                    }

                    $isAliasRequired = true;
                    $isProcessed = true;
                } else if (!empty($langIds)) {
                    $column = $this->findColumn($xml, $value);
                    if ($column->localized) {
                        foreach ($langIds as $langId) {
                            $keys = [];
                            foreach ($primaryKeys as $primaryKey) {
                                $keys[] = [
                                    "source" => (string)$primaryKey->name,
                                    "target" => (string)$primaryKey->name
                                ];
                            }

                            $keys[] = [
                                "target" => "lang_id",
                                "value" => $langId
                            ];

                            $fields[$key . $langId] = [
                                "select" => [
                                    "column" => $value,
                                    "alias" => $value . ":" . $langId
                                ],
                                "leftjoin" => [
                                    "keys" => $keys,
                                    "table" => $this->ensureTableLocalizationName($name),
                                    "alias" => "lang$langId"
                                ]
                            ];
                        }
                    }
                    
                    $fields[$key] = $value;
                    $isProcessed = true;
                }

                if (array_key_exists($value, $this->fieldMetadata)) {
                    $metadata = $this->fieldMetadata[$value];
                    if (array_key_exists("alias", $metadata)) {
                        if (!$isProcessed) {
                            $fields[$key] = [
                                "select" => [
                                    "column" => $metadata["column"],
                                    "alias" => $metadata["alias"]
                                ]
                            ];
                            $isProcessed = true;
                        }
                    }

                    if (array_key_exists("aggregation", $metadata)) {
                        if ($isProcessed) {
                            $fields[$key]["select"]["aggregation"] = $metadata["aggregation"];
                        } else {
                            $fields[$key] = [
                                "select" => [
                                    "column" => $value,
                                    "aggregation" => $metadata["aggregation"]
                                ]
                            ];
                            $isProcessed = true;
                        }
                    }

                    if (array_key_exists("function", $metadata)) {
                        if ($isProcessed) {
                            $fields[$key]["select"]["function"] = $metadata["function"];
                        } else {
                            $fields[$key] = [
                                "select" => [
                                    "column" => $value,
                                    "function" => $metadata["function"]
                                ]
                            ];
                            $isProcessed = true;
                        }
                    }
                }

                if (!$isProcessed) {
                    $fields[$key] = $value;
                }
            }

            if ($isAliasRequired && !is_array($tableName)) {
                $tableName = array("table" => $tableName, "alias" => "_ce");
            }

            $count = null;
            $offset = null;
            if ($isPagingModel) {
                $count = $paging->getSize();
                $offset = $paging->getOffset();
            }

            if (empty($fields)) {
                $sql = $this->sql()->count($tableName, $filter);
                $totalCount = $this->dataAccess()->fetchScalar($sql);

                if ($count != null) {
                    $dataCount = min($totalCount - $offset, $count);
                } else {
                    $dataCount = $totalCount;
                }

                $data = [];
                for ($i=0; $i < $dataCount; $i++) { 
                    $data[] = [];
                }
            } else {
                $sql = $this->sql()->select2([
                    "table" => $tableName, 
                    "fields" => $fields, 
                    "filter" => $filter, 
                    "groupBy" => $groupBy,
                    "having" => $having,
                    "orderBy" => $orderBy, 
                    "count" => $count, 
                    "offset" => $offset
                ]);

                $data = $this->dataAccess()->fetchAll($sql);

                if ($isPagingModel) {
                    $sql = $this->sql()->select2([
                        "table" => $tableName, 
                        "fields" => $fields, 
                        "filter" => $filter, 
                        "groupBy" => $groupBy,
                        "having" => $having
                    ]);
                    $sql = substr($sql, 0, strlen($sql) - 1);
                    $sql = "SELECT COUNT(*) FROM ($sql) AS q;";
                    $totalCount = $this->dataAccess()->fetchScalar($sql);
                }
            }

            if ($isPagingModel) {
                $paging->setTotalCount($totalCount);
            }

            $model->render();
            $model->items($data);
            $result .= $template();

            $this->popListModel();
            $this->fieldMetadata = $oldFieldMetadata;

            return $result;
        }

        private const Aggregations = ["count", "min", "max", "sum", "avg"];
        private const Functions = ["length", "lower", "upper"];

        public function register($name, $alias = "", $aggregation = "", $function = "") {
            if ($alias != "") {
                $this->getProperty($alias);
                $this->setFieldMetadata($alias, "alias", $alias);
                $this->setFieldMetadata($alias, "column", $name);
            } else {
                $this->getProperty($name);
            }

            if ($alias == "") {
                $alias = $name;
            }
            
            if ($aggregation != "") {
                if (!in_array($aggregation, CustomEntity::Aggregations)) {
                    throw new ParameterException("aggregation", "The '$aggregation' is not supported");
                }

                $this->setFieldMetadata($alias, "aggregation", $aggregation);
            }
            
            if ($function != "") {
                if (!in_array($function, CustomEntity::Functions)) {
                    throw new ParameterException("function", "The '$function' is not supported");
                }

                $this->setFieldMetadata($alias, "function", $function);
            }
        }

		public function getProperty($name) {
            // Inside "ce:list".
			$model = parent::peekListModel(false);
			if ($model != null && (array_key_exists($name, $model->currentItem()) || $name == "_" || !$model->isRender())) {
                if ($name == "_") {
                    return $model->currentItem();
                }
                
                if ($model->isRegistration() && array_key_exists($name, $this->fieldMetadata)) {
                    return;
                }

                if ($model->hasMetadataKey("langIds")) {
                    $langIds = $model->metadata("langIds");
                    foreach ($langIds as $langId) {
                        $value = $model->field("$name:$langId");
                        if (!empty($value)) {
                            return $value;
                        }
                    }
                }
    
                return $model->field($name);
			}

            // Inside "ce:form".
            $model = parent::getEditModel(false);
            if ($model != null && ($model->hasKey($name) || $name == "_")) {
                if ($name == "_") {
                    return $model;
                }
                
                return $model[$name];
            }

            if (array_key_exists($name, $this->urlProperties)) {
                return $this->urlProperties[$name];
            }
            
            return null;
        }
        
        public function setProperty($name, $value) {
            if (array_key_exists($name, $this->urlResolvers)) {
                $resolver = $this->urlResolvers[$name];

                $tableName = $this->ensureTableName($resolver["name"]);

                $filter = $resolver["filter"];
                if (parent::isFilterModel($filter)) {
                    $filter = $filter[""];
                    $sqlName = Filter::formatColumnName($filter, $resolver["columnName"]);
                    $sqlValue = parent::sql()->escape($value);
                    $filter[$resolver["columnName"]] = "$sqlName = $sqlValue";
                    $tableName = $filter->wrapTableName($tableName);
                    $filter = $filter->toSql();
                } else {
                    $filter[$resolver["columnName"]] = $value;
                    $filter = ArrayUtils::removeKeysWithEmptyValues($filter);
                }

                $sql = parent::sql()->count($tableName, $filter);
                $data = parent::db()->fetchSingle($sql);
                if (empty($data) || $data["count"] != 1) {
                    return "x.x---y\\r";
                }
            }

            $this->urlProperties[$name] = $value;
            return $value;
        }
        
        public function deleter($template, $name, $params = array()) {
            $tableName = $this->ensureTableName($name);
            $xml = $this->getDefinition($name);

            $sql = $this->getDeleteSql($tableName, $params);
            $this->dataAccess()->execute($sql);
            
            if ($this->hasAuditLog($xml)) {
                $this->audit($tableName, "delete", $params);
            }

            $template();
        }

        public function urlResolver($propertyName, $name, $columnName, $filter = array()) {
            $this->urlResolvers[$propertyName] = [
                "name" => $name,
                "columnName" => $columnName,
                "filter" => $filter
            ];
            return;
        }

        public function swap($template, $name, $key1, $key2, $fields) {
            $tableName = $this->ensureTableName($name);
            $xml = $this->getDefinition($name);
            $fields = explode(",", $fields);

            if (empty($fields)) {
                throw new ParameterException("fields", "Missing fields to swap.");
            }

            if (empty($key1)) {
                throw new ParameterException("key1", "Missing filter for the first item.");
            }

            if (empty($key2)) {
                throw new ParameterException("key2", "Missing filter for the second item.");
            }

            $db = parent::dataAccess();

            $data1 = $db->fetchSingle(parent::sql()->select($tableName, $fields, $key1));
            $data2 = $db->fetchSingle(parent::sql()->select($tableName, $fields, $key2));

            foreach ($fields as $field) {
                $temp = $data1[$field];
                $data1[$field] = $data2[$field];
                $data2[$field] = $temp;
            }

            $updateSql1 = parent::sql()->update($tableName, $data1, $key1);
            $updateSql2 = parent::sql()->update($tableName, $data2, $key2);
            $db->transaction(function() use ($db, $updateSql1, $updateSql2) {
                $db->execute($updateSql1);
                $db->execute($updateSql2);
            });

            if ($this->hasAuditLog($xml)) {
                $this->audit($tableName, "update", $key1, $data1);
                $this->audit($tableName, "update", $key2, $data1);
            }

            $template();
        }
	}

?>