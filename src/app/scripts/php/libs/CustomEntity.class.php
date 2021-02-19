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
            parent::setTagLibXml("CustomEntity.xml");
            $this->tagPrefix = $tagPrefix;
            $this->urlProperties = [];
            $this->urlResolvers = [];
        }

        private function parseUserValue($column, $value) {
            $type = self::getTableColumnTypes($column);
            if ($type == NULL) {
                return NULL;
            }

            return $type["fromUser"]($value, $column);
        }

        private function parseDbValue($column, $value) {
            $type = self::getTableColumnTypes($column);
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
                            if (is_callable($value)) {
                                $value = $value();
                            }

                            $value = self::parseUserValue($column, $value);
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
                    $column = self::findColumn($xml, $columnName);
                    self::updateJoinTable($xml, $column, $model);
                }
            }
        }

        private function updateLocalized($tableName, $xml, $model, $primaryKeys, $langIds, $isNewRecord = false) {
            $langs = self::prepareLocalizedValuesFromModel($xml, $model, $langIds);
            foreach ($langs as $langId => $lang) {
                // Check if update is possible.
                if ($isNewRecord) {
                    $count["count"] = 0;
                } else {
                    $count = self::dataAccess()->fetchSingle(self::sql()->count($tableName, $primaryKeys));
                }

                // Insert or Update.
                if ($count["count"] == 0) {
                    $lang["lang_id"] = $langId;
                    $lang = array_merge($lang, $primaryKeys);
                    $sql = self::sql()->insert($tableName, $lang);
                } else {
                    $primaryKeys["lang_id"] = $langId;
                    $sql = self::sql()->update($tableName, $lang, $primaryKeys);
                }

                self::dataAccess()->execute($sql);
            }
        }

        private function executeEmptyDirectory($model, $tableName, $columnName, $extra, $primaryKeys) {
            $da = parent::dataAccess();

            $fa = new FileAdmin();
            $directoryName = StringUtils::format($extra['nameFormat'], $model);
            $directoryName = StringUtils::format($directoryName, $primaryKeys);
            $directory = $fa->createDirectory($extra['parentDirId'], $directoryName);
            
            $sql = self::sql()->update($tableName, array($columnName => $directory['id']), $primaryKeys);
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
                $sql = self::sql()->update($tableName, array($columnName => $newId), $primaryKeys);
                $da->execute($sql);
            }
        }
        
        private function insert($tableName, $tableLocalizationName, $xml, $keys, $model, $langIds, $deleteIfEmpty = false) {
            $da = parent::dataAccess();
            $keysModel = new EditModel();
            $keysModel->copyFrom($keys);

            $values = self::prepareValuesFromModel($xml, $model);
            $primaryKeys = self::prepareValuesFromModel($xml, $keysModel)["columns"];

            if ($deleteIfEmpty && self::isEmpty($values["columns"])) {
                return;
            }

            foreach ($keys as $key => $value) {
                $values["columns"][$key] = $value;
            }

            $sql = self::sql()->insert($tableName, $values["columns"]);

            // Execute insert.
            $da->execute($sql);
            
            // Get last identity value if inserted.
            $identity = self::findIdentityColumn($xml);
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
                    self::executeEmptyDirectory($model, $tableName, $columnName, $extra, $primaryKeys);
                } else if ($extra["type"] == "createIfEmpty") {
                    self::executeCreateIfEmpty($tableName, $columnName, $extra, $primaryKeys);
                }
            }

            // Process external tables.
            self::updateExternals($values, $xml, $model);

            // Process localization.
            self::updateLocalized($tableLocalizationName, $xml, $model, $primaryKeys, $langIds);

            if (self::hasAuditLog($xml)) {
                self::audit($tableName, "insert", $primaryKeys);
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

            $values = self::prepareValuesFromModel($xml, $model);
            $primaryKeys = self::prepareValuesFromModel($xml, $keysModel)["columns"];

            // If we have chnaged columns.
            if (count($values["columns"]) > 0 && self::isChanged($model, $values["columns"])) {
                if ($deleteIfEmpty && self::isEmpty($values["columns"])) {
                    $sql = self::sql()->delete($tableName, $primaryKeys);
                    $da->execute($sql);

                    if (self::hasAuditLog($xml)) {
                        self::audit($tableName, "delete", $primaryKeys);
                    }

                    return;
                }
                
                // Execute update.
                $sql = self::sql()->update($tableName, $values["columns"], $primaryKeys);
                $da->execute($sql);
            }
            
            // Process extras.
            foreach ($values["extras"] as $columnName => $extra) {
                if ($extra["type"] == "emptyDirectory") {
                    $directorySql = parent::sql()->select($tableName, [$columnName], $primaryKeys);
                    $directory = $da->fetchSingle($directorySql);
                    if (empty($directory[$columnName])) {
                        self::executeEmptyDirectory($model, $tableName, $columnName, $extra, $primaryKeys);
                    } else if(array_key_exists("renameOnUpdate", $extra) && $extra["renameOnUpdate"]) {
                        $directoryName = StringUtils::format($extra['nameFormat'], $model);
                        $directoryName = StringUtils::format($directoryName, $primaryKeys);
                        $directoryNameSql = parent::sql()->update("directory", ["name" => $directoryName], ["id" => $directory[$columnName]]);
                        $da->execute($directoryNameSql);
                    }
                } else if ($extra["type"] == "createIfEmpty") {
                    self::executeCreateIfEmpty($tableName, $columnName, $extra, $primaryKeys);
                }
            }

            // Process external tables.
            self::updateExternals($values, $xml, $model);

            // Process localization.
            self::updateLocalized($tableLocalizationName, $xml, $model, $primaryKeys, $langIds);

            if (self::hasAuditLog($xml)) {
                self::audit($tableName, "update", $primaryKeys);
            }
        }

        private function loadModel($name, $xml, $keys, EditModel $model) {
            $columns = array();
            $external = array();
            foreach ($xml->column as $column) {
                $columnName = (string)$column->name;
                $columnType = (string)$column->type;
                if ($model->hasKey($columnName)) {
                    $typeDefinition = self::getTableColumnTypes($column);
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
                if ($model->hasKey($columnName)) {
                    $typeDefinition = self::getTableColumnTypes($column);
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
                    $data[$key] = self::parseDbValue($column, $value);
                }
            }
            
            $model->metadata("entity", $data);
            foreach ($model as $key => $item) {
                $model[$key] = $data[$key];
            }

            // Load externals.
            foreach ($external as $columnName => $item) {
                if ($item["type"] == "multireference-jointable") {
                    $value = self::loadJoinTableData($xml, $model, $columnName);
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
                    $sql = self::sql()->select($tableName, $columns, $localizableKeys);
                    $data = self::dataAccess()->fetchSingle($sql);
                    foreach ($columns as $column) {
                        $model["$column:$langId"] = $data[$column];
                    }
                }
            }
        }

        private function getDeleteSql($name, $params) {
            return self::sql()->delete($name, $params);
        }

		public function form($template, $name, $deleteIfEmpty = false, $langIds = "", $keys = array()) {
            $model = parent::getEditModel();
            $tableName = self::ensureTableName($name, $model);
            $tableLocalizationName = self::ensureTableLocalizationName($name, $model);
            $xml = parent::getDefinition($name, $model);
            $langIds = explode(",", $langIds);
            $keys = ArrayUtils::removeKeysWithEmptyValues($keys);

            if (!$model->hasMetadataKey("isUpdate")) {
                $model->metadata("isUpdate", count($keys) > 0);
            }

            // Load data based on fields in template.
            if ($model->isLoad() && $model->metadata("isUpdate")) {
                $model->registration();
                $template();
                $model->registration(false);

                $exists = self::loadModel($tableName, $xml, $keys, $model, $langIds);
                if ($exists) {
                    self::loadLocalizedModel($tableLocalizationName, $xml, $keys, $model, $langIds);
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
                    self::update($tableName, $tableLocalizationName, $xml, $keys, $model, $langIds, $deleteIfEmpty);
                } else {
                    self::insert($tableName, $tableLocalizationName, $xml, $keys, $model, $langIds, $deleteIfEmpty);
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
            return self::peekListModel();
        }

        public function getList($template, $name, $filter = array(), $orderBy = array(), $paging = null) {
            $tableName = self::ensureTableName($name);

            $model = new ListModel();
            self::pushListModel($model);

            $model->registration();
            $template([$this->tagPrefix => "register"]);
            $model->registration(false);
            
            $result = "";

            if (parent::isFilterModel($filter)) {
                $filter = $filter[""];
                $tableName = $filter->wrapTableName($tableName);
                $filter = $filter->toSql();
            } else {
                $filter = ArrayUtils::removeKeysWithEmptyValues($filter);
            }
            
            if ($this->isSortModel($orderBy)) {
                $orderBy = $orderBy[""];
            } else {
                $orderBy = ArrayUtils::removeKeysWithEmptyValues($orderBy);
            }

            $fields = $model->fields();
            $xml = null;
            $isAliasRequired = false;
            foreach ($fields as $key => $value) {
                if (strpos($value, ".") !== false) {
                    $parts = explode(".", $value, 2);
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
                                "alias" => $value
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
                }
            }

            if ($isAliasRequired && !is_array($tableName)) {
                $tableName = array("table" => $tableName, "alias" => "_ce");
            }

            $count = null;
            $offset = null;
            if ($paging instanceof PagingModel) {
                $sql = self::sql()->count($tableName, $filter);
                $totalCount = self::dataAccess()->fetchScalar($sql);
                $paging->setTotalCount($totalCount);

                $count = $paging->getSize();
                $offset = $paging->getOffset();
            }

            $sql = self::sql()->select($tableName, $fields, $filter, $orderBy, $count, $offset);
            $data = self::dataAccess()->fetchAll($sql);

            $model->render();
            $model->items($data);
            $result .= $template();

            self::popListModel();
            return $result;
        }

        public function register($name) {
            self::getProperty($name);
        }

		public function getProperty($name) {
            // Inside "ce:list".
			$model = parent::peekListModel(false);
			if ($model != null && (array_key_exists($name, $model->data()) || $name == "_" || !$model->isRender())) {
                if ($name == "_") {
                    return $model->data();
                }
    
                return $model->field($name);
			}

            // Inside "ce:form".
            $model = parent::getEditModel(false);
            if ($model != null) {
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
                    $filter[] = "$sqlName = $sqlValue";
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
            $tableName = self::ensureTableName($name);
            $xml = self::getDefinition($name);

            $sql = self::getDeleteSql($tableName, $params);
            self::dataAccess()->execute($sql);
            
            if (self::hasAuditLog($xml)) {
                self::audit($tableName, "delete", $params);
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

            $template();
        }
	}

?>