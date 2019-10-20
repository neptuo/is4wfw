<?php

    class FilterModel extends ArrayObject
    {
        public $alias;
        public $joiner;

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
    }

?>