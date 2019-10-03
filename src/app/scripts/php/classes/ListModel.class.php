<?php

    class ListModel extends ArrayObject
    {
        private $isRegistration;
        private $isRender;

        private $fields;
        private $items;
        private $data;

        public function __construct() {
            $this->fields = array();
            $this->data = array();
        }

        public function isRegistration() {
            return $this->isRegistration;
        }
        
        public function registration($value = true) {
            $this->isRegistration = $value;
        }

        public function isRender() {
            return $this->isRender;
        }

        public function render() {
            $this->isRender = true;
        }

        public function fields() {
            return $this->fields;
        }

        public function field($name) {
            if ($this->isRegistration) {
                $this->fields[] = $name;
            } else if ($this->isRender) {
                return $this->data[$name];
            }
        }

        public function items($items = "z.z-def") {
            if ($items != "z.z-def") {
                $this->items = $items;
            } else {
                return $this->items;
            }
        }

        public function data($item = "z.z-def") {
            if ($item != "z.z-def") {
                $this->data = $item;
            } else {
                return $this->data;
            }
        }
    }

?>