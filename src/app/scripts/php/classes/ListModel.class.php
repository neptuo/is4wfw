<?php

    class ListModel extends ArrayObject
    {
        private $isRegistration;
        private $isRender;
        private $isIterate;

        private $fields;
        private $items;
        private $currentIndex = -1;
        
        private $metadata = [];

        public function __construct() {
            $this->fields = array();
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
        
        public function iterate($isIterate) {
            $this->isIterate = $isIterate;
        }
        public function isIterate() {
            return $this->isIterate;
        }

        public function fields() {
            return $this->fields;
        }

        public function field($name, $value = "z.z-def") {
            if ($this->isRegistration) {
                if (!in_array($name, $this->fields)) {
                    $this->fields[] = $name;
                }
            } else if ($this->isRender) {
                if ($value === "z.z-def") {
                    return $this->items[$this->currentIndex][$name];
                } else {
                    $this->items[$this->currentIndex][$name] = $value;
                }
            }
        }

        public function items($items = "z.z-def") {
            if ($items != "z.z-def") {
                $this->items = $items;
                $this->currentIndex = -1;
            } else {
                return $this->items;
            }
        }

        public function itemCount() {
            return count($this->items);
        }

        public function addItem($item) {
            $this->items[] = $item;
        }

        public function currentIndex($index) {
            $this->currentIndex = $index;
        }

        public function currentItem() {
            return $this->itemAtIndex($this->currentIndex);
        }

        public function itemAtIndex($index, $item = "z.z-def") {
            if ($item != "z.z-def") {
                $this->items[$index] = $item;
            } else {
                return $this->items[$index];
            }
        }

        public function hasCurrentItem() {
            return $this->currentIndex != -1;
        }

        // ------- Metadata ---------------------------------------------------
        
        public function metadata($key, $value = "0.0-def") {
            if ($value === "0.0-def") {
                return $this->metadata[$key];
            } else {
                $this->metadata[$key] = $value;
            }
        }
        
        public function hasMetadataKey($key) {
            return array_key_exists($key, $this->metadata);
        }
        
        // ------- Factories --------------------------------------------------

        public static function create($items) : ListModel {
            $model = new ListModel();
            $model->items($items);
            $model->render();
            return $model;
        }

    }

?>