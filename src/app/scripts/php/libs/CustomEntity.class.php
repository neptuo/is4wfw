<?php

	require_once("CustomEntityBase.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/EditModel.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/FilterModel.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/ListModel.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/Stack.class.php");

	class CustomEntity extends CustomEntityBase {

        private $tagPrefix;

		public function __construct($tagPrefix) {
            parent::setTagLibXml("CustomEntity.xml");
            $this->tagPrefix = $tagPrefix;
        }

        private function parseUserValue($column, $value) {
            $type = self::getTableColumnTypes($column);
            if ($type == NULL) {
                return NULL;
            }

            return $type["fromUser"]($value);
        }

        private function prepareValuesFromModel($xml, $model) {
            $columns = array();
            $extras = array();
            $external = array();
            foreach ($xml->column as $column) {
                $columnName = (string)$column->name;
                $columnType = (string)$column->type;
                if (array_key_exists($columnName, $model)) {
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

        private function prepareLocalizedValuesFromModel($xml, $model, $langIds) {
            $langs = array();
            foreach ($langIds as $langId) {
                $columns = array();
                foreach ($xml->column as $column) {
                    if ($column->localized == true) {
                        $columnName = (string)$column->name;
                        $columnType = (string)$column->type;

                        $key = "$columnName:$langId";
                        if (array_key_exists($key, $model)) {
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
        
        private function insert($tableName, $tableLocalizationName, $xml, $model, $langIds) {
            $values = self::prepareValuesFromModel($xml, $model);
            $sql = self::sql()->insert($tableName, $values["columns"]);

            self::dataAccess()->transaction(function($da) use ($tableName, $tableLocalizationName, $xml, $model, $values, $sql, $langIds) {
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
                        $fa = new FileAdmin();
                        $directoryName = self::formatString($extra['nameFormat'], $model);
                        $directory = $fa->createDirectory($extra['parentDirId'], $directoryName);
                        
                        $sql = self::sql()->update($tableName, array($columnName => $directory['id']), $primaryKeys);
                        $da->execute($sql);
                    }
                }

                // Process external tables.
                self::updateExternals($values, $xml, $model);

                // Process localization.
                self::updateLocalized($tableLocalizationName, $xml, $model, $primaryKeys, $langIds);

                if (self::hasAuditLog($xml)) {
                    self::audit($tableName, "insert", $primaryKeys);
                }
            });
        }
        
        private function update($tableName, $tableLocalizationName, $xml, $keys, $model, $langIds) {
            $values = self::prepareValuesFromModel($xml, $model);
            $primaryKeys = self::prepareValuesFromModel($xml, $keys)["columns"];
            $sql = self::sql()->update($tableName, $values["columns"], $primaryKeys);

            self::dataAccess()->transaction(function($da) use ($tableName, $tableLocalizationName, $xml, $model, $values, $sql, $langIds, $primaryKeys) {
                // Execute update.
                $da->execute($sql);

                // Process external tables.
                self::updateExternals($values, $xml, $model);

                // Process localization.
                self::updateLocalized($tableLocalizationName, $xml, $model, $primaryKeys, $langIds);

                if (self::hasAuditLog($xml)) {
                    self::audit($tableName, "update", $primaryKeys);
                }
            });
        }

        private function loadModel($name, $xml, $keys, $model, $langIds) {
            $columns = "";
            $condition = "";
            $external = array();
            foreach ($xml->column as $column) {
                $columnName = (string)$column->name;
                $columnType = (string)$column->type;
                if (array_key_exists($columnName, $model)) {
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
                if (array_key_exists($columnName, $model)) {
                    $typeDefinition = self::getTableColumnTypes($column);
                    if ($typeDefinition["hasColumn"]) {
                        $columns = self::joinString($columns, "`$columnName`");
                    }
                }
            }

            foreach ($keys as $key => $value) {
                $condition = self::joinString($condition, "$key = $value", " AND");
            }

            // Load main data.
            $data = self::dataAccess()->fetchSingle("SELECT $columns FROM `$name` WHERE $condition");
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
        }

        private function loadLocalizedModel($tableName, $xml, $keys, $model, $langIds) {
            $columns = array();

            // Find model keys.
            foreach ($xml->column as $column) {
                $columnName = (string)$column->name;

                foreach ($langIds as $langId) {
                    $key = "$columnName:$langId";
                    if (array_key_exists($key, $model)) {
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

		public function form($template, $name, $method = "POST", $submit = "", $nextPageId = 0, $langIds = "", $keys = array()) {
            $tableName = self::ensureTableName($name);
            $tableLocalizationName = self::ensureTableLocalizationName($name);
            $xml = self::getDefinition($name);
            $langIds = explode(",", $langIds);
            $keys = parent::removeKeysWithEmptyValues($keys);

            if ($method == "GET" && $submit == "") {
                trigger_error("Missing required parameter 'submit' for 'GET' custom entity form '$name'", E_USER_ERROR);
            }
			
			$template = parent::getParsedTemplate($template);

            $isUpdate = count($keys) > 0;

            $model = new EditModel();
            self::pushEditModel($model);

            if ($isUpdate) {
                $model->registration();
                self::parseContent($template);
                $model->registration(false);

                self::loadModel($tableName, $xml, $keys, $model, $langIds);
                self::loadLocalizedModel($tableLocalizationName, $xml, $keys, $model, $langIds);
            }

            if (self::isHttpMethod($method) && ($submit == "" || array_key_exists($submit, $_REQUEST))) {
                $model->submit();
                self::parseContent($template);
                $model->submit(false);

                if ($isUpdate) {
                    self::update($tableName, $tableLocalizationName, $xml, $keys, $model, $langIds);
                } else {
                    self::insert($tableName, $tableLocalizationName, $xml, $model, $langIds);
                }

                if (!empty($nextPageId)) {
                    self::web()->redirectTo($nextPageId);
                } else {
                    $model->copyFrom($keys);

                    $model->saved(true);
                    self::parseContent($template);
                    $model->saved(false);

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

        public function isFormSaved() {
            return parent::peekEditModel()->isSaved();
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

        public function getListData() {
            return self::peekListModel();
        }

        public function getList($template, $name, $filter = array(), $orderBy = array()) {
            $tableName = self::ensureTableName($name);
            $filter = parent::removeKeysWithEmptyValues($filter);
            $orderBy = parent::removeKeysWithEmptyValues($orderBy);

            $model = new ListModel();
            self::pushListModel($model);

            $model->registration();
            self::parseContent($template, array($this->tagPrefix . ":register"));
            $model->registration(false);
            
            $result = "";

            if (parent::isFilterModel($filter)) {
                $filter = $filter[""];
                $tableName = $filter->wrapTableName($tableName);
                $filter = $filter->toSql();
            }

            $sql = self::sql()->select($tableName, $model->fields(), $filter, $orderBy);
            $data = self::dataAccess()->fetchAll($sql);

            $model->render();
            $model->items($data);
            $result .= self::parseContent($template);

            self::popListModel();
            return $result;
        }

        public function register($name) {
            self::getProperty($name);
        }

		public function getProperty($name) {
            // Inside "ce:list".
			$model = parent::peekListModel(false);
			if ($model != null) {
                if ($name == "_") {
                    return $model->data();
                }
    
                return $model->field($name);
			}

            // Inside "ce:form".
            $model = parent::peekEditModel(false);
            if ($model != null) {
                if ($name == "_") {
                    return $model;
                }
                
                return $model[$name];
            }
            
            return null;
		}
        
        public function deleter($template, $name, $params = array()) {
            $tableName = self::ensureTableName($name);
            $xml = self::getDefinition($name);

            $sql = self::getDeleteSql($tableName, $params);
            self::dataAccess()->execute($sql);
            
            if (self::hasAuditLog($xml)) {
                self::audit($tableName, "delete", $params);
            }

            self::parseContent($template);
        }
	}

?>