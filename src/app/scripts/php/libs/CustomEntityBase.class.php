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

        public function getTableColumnTypes($key = NULL) {
            if ($this->types == NULL) {
                $this->types = array(
                    array("key" => "number", "name" => "Number", "db" => "int(11)", "fromUser" => function($value) { return intval($value); }),
                    array("key" => "shorttext", "name" => "Short Text", "db" => "tinytext", "fromUser" => function($value) { return $value; }),
                    array("key" => "longtext", "name" => "Long Text", "db" => "text", "fromUser" => function($value) { return $value; }),
                    array("key" => "bool", "name" => "Boolean", "db" => "bit(1)", "fromUser" => function($value) { return boolval($value); }),
                    array("key" => "singlereference", "name" => "Single Reference", "db" => "int(11)", "fromUser" => function($value) { return intval($value); }),
                    array("key" => "directory", "name" => "Directory in FileSystem", "db" => "int(11)", "fromUser" => function($value) { return intval($value); })
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
    }

?>