<?php

    class FilterModel extends ArrayObject
    {
        public $alias;
        public $joiner;

        public function __toString() {
            return $this->toSql();
        }

        public function toSql() {
            $result = "";
            $joiner = " " . $this->joiner . " ";
            foreach ($this as $value) {
                $result = StringUtils::join($result, $value, $joiner);
            }

            if ($result === "") {
                return null;
            }

            return "($result)";
        }

        public function wrapTableName($tableName) {
            if (!empty($this->alias)) {
                $tableName = array("table" => $tableName, "alias" => $this->alias);
            }

            return $tableName;
        }
    }

?>