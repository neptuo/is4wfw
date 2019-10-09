<?php

	require_once("BaseTagLib.class.php");

    class CustomEntityBase extends BaseTagLib {
        const TablePrefix = "ce_";
        const TableLocalizationPrefix = "celang_";

        private $types;

        protected function ensureTableName($name) {
            if (!self::startsWith($name, self::TablePrefix)) {
                $tableName = self::TablePrefix . $name;
                $table = self::dataAccess()->fetchAll("SELECT `name` FROM `custom_entity` WHERE `name` = '" . self::dataAccess()->escape($name) . "';");
                if (count($table) == 0) {
                    trigger_error("Table name must be custom entity", E_USER_ERROR);
                }
            } else {
                $tableName = $name;
            }
            
            return $tableName;
        }
        
        protected function ensureTableLocalizationName($name) {
            if (!self::startsWith($name, self::TableLocalizationPrefix)) {
                $tableName = self::TableLocalizationPrefix . $name;
            } else {
                $tableName = $name;
            }
            
            return $tableName;
        }

        protected function getDefinition($name) {
            $definition = self::dataAccess()->fetchSingle("SELECT `definition` FROM `custom_entity` WHERE `name` = '" . self::dataAccess()->escape($name) . "';");
            if (empty($definition)) {
                return NULL;
            }

            $xml = new SimpleXMLElement($definition['definition']);
            return $xml;
        }

        protected function getUpdateDefinitionSql($name, $xml) {
            return self::sql()->update("custom_entity", array("definition" => $xml->asXml()), array("name" => $name));
        }

        protected function findIdentityColumn($xml) {
            foreach ($xml->column as $column) {
                if ($column->identity == TRUE) {
                    return $column;
                }
            }

            return NULL;
        }

        protected function getPrimaryKeyColumns($xml) {
            $result = array();
            foreach ($xml->column as $column) {
                if ($column->primaryKey == TRUE) {
                    $result[] = $column;
                }
            }

            return $result;
        }

        protected function findColumn($xml, $columnName) {
            foreach ($xml->column as $column) {
                if ($column->name == $columnName) {
                    return $column;
                }
            }

            return null;
        }

        protected function loadJoinTableData($xml, $model, $columnName) {
            $column = self::findColumn($xml, $columnName);

            $primaryKeyMappings = array();
            foreach ($column->primaryKeyMappings as $mapping) {
                $primaryKeyMappings[] = (string)$mapping->mappedTo;
            }

            $primaryKeyNames = array();
            $primaryKeys = self::getPrimaryKeyColumns($xml);
            foreach ($primaryKeys as $primaryKey) {
                $primaryKeyNames[] = (string)$primaryKey->name;
            }

            $filter = array();
            for ($i=0; $i < count($primaryKeyNames); $i++) { 
                $primaryKeyName = $primaryKeyNames[$i];
                $primaryKeyMapping = $primaryKeyMappings[$i];

                $filter[$primaryKeyMapping] = $model[$primaryKeyName];
            }

            $targetColumn = (string)$column->targetColumn;
            $sql = self::sql()->select((string)$column->joinTable, array($targetColumn), $filter);
            $items = self::dataAccess()->fetchAll($sql);
            $value = array();
            foreach ($items as $item) {
                $value[] = $item[$targetColumn];
            }

            return $value;
        }

        protected function updateJoinTable($xml, $column, $model) {
            $columnName = (string)$column->name;
            $primaryKeys = self::getPrimaryKeyColumns($xml);

            // 1) Select current values;
            $newValues = $model[$columnName];
            $currentValues = self::loadJoinTableData($xml, $model, $columnName);

            // 2) Execute deletes for removed;
            foreach ($currentValues as $item) {
                if (!in_array($item, $newValues)) {
                    $filter = array();
                    for ($i=0; $i < count($primaryKeys); $i++) { 
                        $filter[(string)$column->primaryKeyMappings[$i]->mappedTo] = $model[(string)$primaryKeys[$i]->name];
                    }
                    $filter[(string)$column->targetColumn] = $item;
                    $deleteSql = self::sql()->delete((string)$column->joinTable, $filter);
                    self::dataAccess()->execute($deleteSql);
                }
            }

            // 3) Execute inserts for new.
            foreach ($newValues as $item) {
                if (!in_array($item, $currentValues)) {
                    $itemModel = array();
                    for ($i=0; $i < count($primaryKeys); $i++) { 
                        $itemModel[(string)$column->primaryKeyMappings[$i]->mappedTo] = $model[(string)$primaryKeys[$i]->name];
                    }
                    $itemModel[(string)$column->targetColumn] = $item;
                    $insertItemSql = self::sql()->insert((string)$column->joinTable, $itemModel);
                    self::dataAccess()->execute($insertItemSql);
                }
            }
        }

        protected function registerPrimaryKeysToModel($xml, $model) {
            $primaryKeys = self::getPrimaryKeyColumns($xml);
            foreach ($primaryKeys as $primaryKey) {
                $primaryKeyName = (string)$primaryKey->name;
                $model[$primaryKeyName] = null;
            }
        }

        protected function mapPrimaryKeyValues($xml, $model) {
            $result = array();
            $primaryKeys = self::getPrimaryKeyColumns($xml);
            foreach ($primaryKeys as $primaryKey) {
                $primaryKeyColumnName = (string)$primaryKey->name;
                $result[$primaryKeyColumnName] = $model[$primaryKeyColumnName];
            }

            return $result;
        }

        protected function hasAuditLog($xml) {
            return $xml->audit->log;
        }

        protected function audit(String $tableName, String $operationType, Array $keys, Array $values = array()) {
            if (parent::startsWith($tableName, self::TablePrefix)) {
                $tableName = substr($tableName, strlen(self::TablePrefix));
            }

            $xml = new SimpleXMLElement("<audit/>");
            $xml->timestamp = time();
            $xml->entity = $tableName;
            $xml->operation = $operationType;
            $xml->userId = parent::login()->getUserId();

            if (!empty($keys)) {
                $keysXml = $xml->addChild("keys");
                foreach ($keys as $key => $value) {
                    $keysXml->{$key} = $value;
                }
            }
            
            if (!empty($values)) {
                $valuesXml = $xml->addChild("values");
                foreach ($values as $key => $value) {
                    $valuesXml->{$key} = $value;
                }
            }
            
            $xmlString = $xml->asXML();
            global $logObject;
            $logObject->writeToCustom($tableName, $xmlString);
        }

        public function getTableColumnTypes($column = null, $key = null) {
            if ($this->types == null) {
                $this->types = array(
                    array(
                        "key" => "int", 
                        "name" => "Integer", 
                        "db" => "int",
                        "db.formatter" => function($c) { 
                            $size = (int)$c->size;
                            if ($size != null) {
                                return "int($size)";
                            }

                            return "int";
                        }, 
                        "hasColumn" => true,
                        "fromUser" => function($value) { return intval($value); }
                    ),
                    array(
                        "key" => "float", 
                        "name" => "Float", 
                        "db" => "float",
                        "db.formatter" => function($c) { 
                            $size = (int)$c->size;
                            $decimals = (int)$c->decimals;
                            if ($size != null && $decimals != null) {
                                return "float($size, $decimals)";
                            }

                            return "float";
                        }, 
                        "hasColumn" => true,
                        "isLocalizable" => false,
                        "fromUser" => function($value) { return intval($value); }
                    ),
                    array(
                        "key" => "shorttext", 
                        "name" => "Short Text", 
                        "db" => "tinytext", 
                        "hasColumn" => true,
                        "isLocalizable" => true,
                        "fromUser" => function($value) { return $value; }
                    ),
                    array(
                        "key" => "longtext", 
                        "name" => "Long Text", 
                        "db" => "text", 
                        "hasColumn" => true,
                        "isLocalizable" => true,
                        "fromUser" => function($value) { return $value; }
                    ),
                    array(
                        "key" => "varchar", 
                        "name" => "Fixed-size text", 
                        "db" => "varchar", 
                        "db.formatter" => function($c) { 
                            $size = (int)$c->size;
                            if ($size != null) {
                                return "varchar($size)";
                            }

                            return "varchar";
                        }, 
                        "hasColumn" => true,
                        "isLocalizable" => true,
                        "fromUser" => function($value) { return $value; }
                    ),
                    array(
                        "key" => "url", 
                        "name" => "URL path", 
                        "db" => "varchar", 
                        "db.formatter" => function($c) { 
                            $size = (int)$c->size;
                            if ($size == null) {
                                $size = 50;
                            }
                            
                            return "varchar($size)";
                        }, 
                        "hasColumn" => true,
                        "isLocalizable" => true,
                        "fromUser" => function($value) { return self::convertToValidUrl($value); }
                    ),
                    array(
                        "key" => "bool", 
                        "name" => "Boolean", 
                        "db" => "bit(1)", 
                        "hasColumn" => true,
                        "isLocalizable" => false,
                        "fromUser" => function($value) { return boolval($value); }
                    ),
                    array(
                        "key" => "singlereference", 
                        "name" => "Single Reference", 
                        "db" => "int(11)", 
                        "hasColumn" => true,
                        "isLocalizable" => false,
                        "fromUser" => function($value) { return intval($value); }
                    ),
                    array(
                        "key" => "multireference-singlecolumn", 
                        "name" => "Multi Reference in Single Column", 
                        "db" => "tinytext", 
                        "hasColumn" => true,
                        "isLocalizable" => false,
                        "fromUser" => function($value) { return self::parseMultiReferenceSingleColumn($value); }
                    ),
                    array(
                        "key" => "multireference-jointable", 
                        "name" => "Multi Reference with Join Table", 
                        "db" => "tinytext", 
                        "hasColumn" => false,
                        "isLocalizable" => false,
                        "fromUser" => function($value) { return self::parseMultiReferenceSingleColumn($value); }
                    ),
                    array(
                        "key" => "directory", 
                        "name" => "Directory in FileSystem", 
                        "db" => "int(11)", 
                        "hasColumn" => true,
                        "isLocalizable" => false,
                        "fromUser" => function($value) { return intval($value); }
                    )
                );
            }

            if ($column != null) {
                $columnType = (string)$column->type;
                foreach ($this->types as $item) {
                    if ($item["key"] == $columnType) {
                        if ($key != null) {
                            if ($key == "db") {
                                return self::getTableColumnDbType($item, $column);
                            }

                            return $item[$key];
                        }

                        return $item;
                    }
                }

                return null;
            }

            return $this->types;
        }

        public function getTableColumnDbType($type, $column) {
            $formatter = $type["db.formatter"];
            if ($formatter != null) {
                return $formatter($column);
            }

            return $type["db"];
        }

        private function parseMultiReferenceSingleColumn($value) {
            if (is_array($value)) {
                return implode(",", $value);
            }

            return strval($value);
        }
    }

?>