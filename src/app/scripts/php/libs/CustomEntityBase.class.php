<?php

	require_once("BaseTagLib.class.php");

    class CustomEntityBase extends BaseTagLib {
        const TablePrefix = "ce_";

        private $types;

        protected function ensureTableName($name) {
            if (!self::startsWith($name, self::TablePrefix)) {
                $tableName = self::TablePrefix . $name;
                $table = self::dataAccess()->fetchAll("SELECT `name` FROM `custom_entity` WHERE `name` = '" . self::dataAccess()->escape($name) . "';");
                if (count($table) == 0) {
                    trigger_error("Table name must be custom entity", E_USER_ERROR);
                }
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

        public function getTableColumnTypes($key = NULL) {
            if ($this->types == NULL) {
                $this->types = array(
                    array(
                        "key" => "number", 
                        "name" => "Number", 
                        "db" => "int(11)", 
                        "hasColumn" => true,
                        "fromUser" => function($value) { return intval($value); }
                    ),
                    array(
                        "key" => "shorttext", 
                        "name" => "Short Text", 
                        "db" => "tinytext", 
                        "hasColumn" => true,
                        "fromUser" => function($value) { return $value; }
                    ),
                    array(
                        "key" => "longtext", 
                        "name" => "Long Text", 
                        "db" => "text", 
                        "hasColumn" => true,
                        "fromUser" => function($value) { return $value; }
                    ),
                    array(
                        "key" => "url", 
                        "name" => "URL path", 
                        "db" => "tinytext", 
                        "hasColumn" => true,
                        "fromUser" => function($value) { return self::convertToValidUrl($value); }
                    ),
                    array(
                        "key" => "bool", 
                        "name" => "Boolean", 
                        "db" => "bit(1)", 
                        "hasColumn" => true,
                        "fromUser" => function($value) { return boolval($value); }
                    ),
                    array(
                        "key" => "singlereference", 
                        "name" => "Single Reference", 
                        "db" => "int(11)", 
                        "hasColumn" => true,
                        "fromUser" => function($value) { return intval($value); }
                    ),
                    array(
                        "key" => "multireference-singlecolumn", 
                        "name" => "Multi Reference in Single Column", 
                        "db" => "tinytext", 
                        "hasColumn" => true,
                        "fromUser" => function($value) { return self::parseMultiReferenceSingleColumn($value); }
                    ),
                    array(
                        "key" => "multireference-jointable", 
                        "name" => "Multi Reference with Join Table", 
                        "db" => "tinytext", 
                        "hasColumn" => false,
                        "fromUser" => function($value) { return self::parseMultiReferenceSingleColumn($value); }
                    ),
                    array(
                        "key" => "directory", 
                        "name" => "Directory in FileSystem", 
                        "db" => "int(11)", 
                        "hasColumn" => true,
                        "fromUser" => function($value) { return intval($value); }
                    )
                );
            }

            if ($key != NULL) {
                foreach ($this->types as $item) {
                    if ($item["key"] == $key) {
                        return $item;
                    }
                }

                return NULL;
            }

            return $this->types;
        }

        private function parseMultiReferenceSingleColumn($value) {
            if (is_array($value)) {
                return implode(",", $value);
            }

            return strval($value);
        }
    }

?>