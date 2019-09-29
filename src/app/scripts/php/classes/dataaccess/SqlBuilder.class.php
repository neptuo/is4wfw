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

        private function escape($value) {
            if (is_string($value)) {
                $value = "'" . $this->dataAccess->escape($value) . "'";
            } else if (is_bool($value)) {
                $value = $value ? 1 : 0;
            }

            return $value;
        }

        private function condition($filter, $operator = "AND") {
            $result = "";

            foreach ($filter as $key => $value) {
                $value = self::escape($value);
                $result = self::joinString($result, "`$key` = $value", " " . $operator);
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

            foreach ($orderBy as $key => $value) {
                $value = ($value == "desc" || $value == "DESC") ? "DESC" : "ASC";
                $result = self::joinString($result, "`$key` $value", ", ");
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
            $columns = "";
            $values = "";
            foreach ($item as $key => $value) {
                $columns = self::joinString($columns, "`$key`");
                $values = self::joinString($values, self::escape($value));
            }

            $sql = "INSERT INTO `$tableName`($columns) VALUES ($values);";
            return $sql;
        }

        public function update($tableName, $item, $filter) {
            $condition = self::condition($filter, "AND");
            $condition = self::appendWhere($condition);

            $values = "";
            foreach ($item as $key => $value) {
                $value = self::escape($value);
                $values = self::joinString($values, "`$key` = $value");
            }

            $sql = "UPDATE `$tableName` SET $values$condition;";
            return $sql;
        }

        public function delete($tableName, $filter) {
            $condition = self::condition($filter, "AND");
            $condition = self::appendWhere($condition);

            $sql = "DELETE FROM `$tableName`$condition;";
            return $sql;
        }

        public function select($tableName, $fields, $filter = array(), $orderBy = array()) {
            $condition = self::condition($filter, "AND");
            $condition = self::appendWhere($condition);
            $order = self::orderBy($orderBy);
            $order = self::appendOrderBy($order);
            
            $columns = "";
            foreach ($fields as $key) {
                $columns = self::joinString($columns, "`$key`");
            }

            $sql = "SELECT $columns FROM `$tableName`$condition$order";
            return $sql;
        }
    }

?>