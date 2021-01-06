<?php

    class Stack {

        private $arr = array();

        public function push($s) {
            $this->arr[] = $s;
        }    

        public function pop() {
            return array_pop($this->arr);
        }

        public function peek() {
            return $this->arr[count($this->arr) - 1];
        }

        public function peekWithOffset($offset = 1) {
            $count = count($this->arr);
            $index = $count - $offset;
            if ($index >= 0) {
                return $this->arr[$index];
            }

            return false;
        }

        public function peekNotNull() {
            $result = null;
            $offset = 1;
            do {
                $result = $this->peekWithOffset($offset);
                $offset++;
            }
            while($result === null);

            return $result;
        }

        public function isEmpty() {
            return empty($this->arr);
        }

    }

?>