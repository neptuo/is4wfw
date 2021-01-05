<?php

    class SqlBuilder {
        private $dataAccess;

        public function __construct($dataAccess) {
            $this->dataAccess = $dataAccess;
        }
        
        private function joinString($base, $item, $separator = ", ") {
            if (strlen($base) > 0) {
                $base .= $separator;
            }

            return $base . $item;
        }

        private function field($tableAlias, $field) {
            if (empty($tableAlias)) {
                return "`$field`";
            }

            return "$tableAlias.`$field`";
        }

        public function escape($value) {
            if ($value === null) {
                $value = "NULL";
            } else if (is_string($value)) {
                $value = "'" . $this->dataAccess->escape($value) . "'";
            } else if (is_bool($value)) {
                $value = $value ? 1 : 0;
            }

            return $value;
        }

        private function condition($filter, $operator = "AND ") {
            if (is_string($filter)) {
                return $filter;
            }

            $result = "";

            foreach ($filter as $field => $value) {
                $assignValue = null;
                $name = "`$field`";
                if (is_array($value)) {
                    $valueString = "";
                    foreach ($value as $item) {
                        if (!empty($item)) {
                            $valueString = self::joinString($valueString, self::escape($item));
                        }
                    }

                    if (!empty($valueString)) {
                        $assignValue = " IN ($valueString)";
                    }
                } else {
                    $value = self::escape($value);

                    if ($field != "") {
                        $assignValue = " = $value";
                    } else {
                        $assignValue = "$value";
                    }
                }

                if ($assignValue != null) {
                    $result = self::joinString($result, "$name$assignValue", " " . $operator);
                }
            }

            return $result;
        }
        
        private function appendWhere($value) {
            if (!empty($value)) {
                $value = " WHERE $value";
            }
            
            return $value;
        }

        private function orderBy($orderBy) {
            $result = "";

            foreach ($orderBy as $field => $value) {
                $value = ($value == "desc" || $value == "DESC") ? "DESC" : "ASC";
                $result = self::joinString($result, "`$field` $value", ", ");
            }

            return $result;
        }

        private function appendOrderBy($value) {
            if (!empty($value)) {
                $value = " ORDER BY $value";
            }

            return $value;
        }

        public function insert($tableName, $item) {
            return $this->insertOrUpdatePrivate($tableName, $item, null);
        }

        public function insertOrUpdate($tableName, $item, $updateFields) {
            return $this->insertOrUpdatePrivate($tableName, $item, $updateFields);
        }

        private function insertOrUpdatePrivate($tableName, $item, $updateFields) {
            $columns = "";
            $values = "";
            foreach ($item as $field => $value) {
                $columns = self::joinString($columns, "`$field`");
                $values = self::joinString($values, self::escape($value));
            }

            $updateString = "";
            if (is_array($updateFields)) {
                foreach ($updateFields as $field) {
                    $value = $this->escape($item[$field]);
                    $updateString = $this->joinString($updateString, "`$field` = $value");
                }
            }
            if (!empty($updateString)) {
                $updateString = " ON DUPLICATE KEY UPDATE $updateString";
            }

            $sql = "INSERT INTO `$tableName`($columns) VALUES ($values)$updateString;";
            return $sql;
        }

        public function update($tableName, $item, $filter) {
            $condition = self::condition($filter, "AND ");
            $condition = self::appendWhere($condition);

            $values = "";
            foreach ($item as $field => $value) {
                $value = self::escape($value);
                $values = self::joinString($values, "`$field` = $value");
            }

            $sql = "UPDATE `$tableName` SET $values$condition;";
            return $sql;
        }

        public function delete($tableName, $filter) {
            $condition = self::condition($filter, "AND ");
            $condition = self::appendWhere($condition);

            $sql = "DELETE FROM `$tableName`$condition;";
            return $sql;
        }

        public function select($tableName, $fields, $filter = array(), $orderBy = array()) {
            $condition = self::condition($filter, "AND ");
            $condition = self::appendWhere($condition);
            $order = self::orderBy($orderBy);
            $order = self::appendOrderBy($order);

            $alias = "";
            if (is_array($tableName)) {
                $table = $tableName["table"];
                $alias = $tableName["alias"];
                $tableName = "`$table`";
                if (!empty($alias)) {
                    $tableName .= " AS $alias";
                }
            } else {
                $tableName = "`$tableName`";
            }
            
            $joins = array();
            $columns = "";

            $wildcardIndex = array_search("*", $fields);
            $hasWildcard = $wildcardIndex !== false;
            if ($hasWildcard) {
                $field = self::field($alias, "*");
                $columns = self::joinString($columns, $field);
                unset($fields[$wildcardIndex]);
            }

            foreach ($fields as $field) {
                $fieldTableAlias = $alias;
                if (is_array($field)) {
                    if (array_key_exists("leftjoin", $field)) {
                        $leftjoin = $field["leftjoin"];

                        $fieldTableAlias = $leftjoin["alias"];
                        if (!array_key_exists($fieldTableAlias, $joins)) {
                            $source = self::field($alias, $leftjoin["source"]);
                            $table = $leftjoin["table"];
                            $target = self::field($fieldTableAlias, $leftjoin["target"]);
                            $joins[$fieldTableAlias] = "LEFT JOIN `$table` as $fieldTableAlias ON $source = $target";
                        }
                    }
                    
                    if (array_key_exists("select", $field)) {
                        $column = $field["select"]["column"];
                        $as = $field["select"]["alias"];
                        $field = self::field($fieldTableAlias, $column) . " AS `$as`";
                    }

                    $columns = self::joinString($columns, $field);
                }
                else if (!$hasWildcard) {
                    $field = self::field($fieldTableAlias, $field);
                    $columns = self::joinString($columns, $field);
                }
            }

            if (!empty($joins)) {
                $joins = implode(" ", $joins);
                $joins = " $joins";
            } else {
                $joins = "";
            }

            $sql = "SELECT $columns FROM $tableName$joins$condition$order;";
            return $sql;
        }

        public function count($tableName, $filter = array()) {
            $condition = self::condition($filter, "AND");
            $condition = self::appendWhere($condition);

            $sql = "SELECT COUNT(*) as `count` FROM `$tableName`$condition;";
            return $sql;
        }
    }

?>