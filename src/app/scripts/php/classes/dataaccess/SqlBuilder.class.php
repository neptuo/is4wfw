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
            }

            return $value;
        }

        private function condition($filter, $operator = "AND") {
            foreach ($filter as $key => $value) {
                $value = self::escape($value);
                $condition = self::joinString($condition, "`$key` = $value", " " . $operator);
            }

            return $condition;
        }

        private function appendWhere($condition) {
            if (!empty($condition)) {
                $condition = " WHERE $condition";
            }

            return $condition;
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
    }

?>