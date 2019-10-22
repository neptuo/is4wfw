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
                $result = self::joinString($result, $value, $joiner);
            }

            return "($result)";
        }

        private function joinString($base, $item, $separator = ", ") {
            if (strlen($base) > 0) {
                $base .= $separator;
            }

            return $base . $item;
        }

        public function wrapTableName($tableName) {
            if (!empty($this->alias)) {
                $tableName = array("table" => $tableName, "alias" => $this->alias);
            }

            return $tableName;
        }
    }

?>