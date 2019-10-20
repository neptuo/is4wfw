<?php

    class FilterModel extends ArrayObject
    {
        public $alias;
        public $joiner;

        public function toSql() {
            return "(" . implode($this->joiner, $this) . ")";
        }
    }

?>