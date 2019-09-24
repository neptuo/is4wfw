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

        public function isEmpty() {
            return empty($this->arr);
        }

    }

?>