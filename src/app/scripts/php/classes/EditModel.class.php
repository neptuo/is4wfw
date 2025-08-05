<?php

    class EditModel implements ArrayAccess, Iterator
    {
        private $prefixes = [];
        private $prefix;
        private $metadata = [];
        private $container = [];
        private $validation = [];
        private $index = 0;
        private $editable = true;

        public function prefix($name = "1.1-def") {
            if ($name === "1.1-def") {
                return $this->prefix;
            }

            if ($name === null) {
                $name = "";
            }

            if (!in_array($name, $this->prefixes)) {
                $this->prefixes[] = $name;
            }

            $this->prefix = $name;
        }

        public function prefixes() {
            return $this->prefixes;
        }

        public function editable($value = "0.0-def") {
            if ($value === "0.0-def") {
                return $this->editable;
            }

            $this->editable = $value;
        }

        // ------- ArrayAccess ------------------------------------------------

        public function offsetSet($offset, $value): void {
            if (is_null($offset)) {
                $this->container[$this->prefix][] = $value;
            } else {
                $this->container[$this->prefix][$offset] = $value;
            }
        }
    
        public function offsetExists($offset): bool {
            return isset($this->container[$this->prefix][$offset]);
        }
    
        public function offsetUnset($offset): void {
            unset($this->container[$this->prefix][$offset]);
        }
    
        public function offsetGet($offset, $evaluate = true): mixed {
            if ($this->offsetExists($offset)) {
                $value = $this->container[$this->prefix][$offset];
                if ($evaluate && is_callable($value)) {
                    $value = $value();
                }

                return $value;
            } else {
                return null;
            }
        }

        public function getUnevaluatedValue($offset) {
            return $this->offsetGet($offset, false);
        }

        // ------- Iterator ---------------------------------------------------

        public function rewind(): void {
            $this->index = 0;
        }

        public function current(): mixed {
            $keys = $this->keys();
            if (isset($this[$keys[$this->index]])) {
                $value = $this[$keys[$this->index]];
                return $value;
            } else {
                return null;
            }
        }

        public function key(): mixed{
            $keys = $this->keys();
            $value = $keys[$this->index];
            return $value;
        }

        public function next(): void {
            $this->index++;
        }

        public function valid() : bool {
            $keys = $this->keys();
            $isIndexSet = isset($keys[$this->index]);
            return $isIndexSet;
        }

        // ------- Model phases -----------------------------------------------
        
        private $isRegistration;
        private $isLoad;
        private $isSubmit;
        private $isSave;
        private $isSaved;
        private $isRender;
        
        public function isRegistration() {
            return $this->isRegistration;
        }
        
        public function registration($value = true) {
            $this->isRegistration = $value;
        }

        public function isLoad() {
            return $this->isLoad;
        }

        public function load($value = true) {
            $this->isLoad = $value;
        }

        public function isSubmit() {
            return $this->isSubmit;
        }

        public function submit($value = true) {
            $this->isSubmit = $value;
        }

        public function isSave() {
            return $this->isSave;
        }

        public function save($value = true) {
            $this->isSave = $value;
        }
        
        public function isSaved() {
            return $this->isSaved;
        }

        public function saved($value = true) {
            $this->isSaved = $value;
        }
        
        public function isRender() {
            return $this->isRender;
        }

        public function render($value = true) {
            $this->isRender = $value;
        }

        // ------- Metadata ---------------------------------------------------

        public function metadata($key, $value = "0.0-def") {
            if ($value === "0.0-def") {
                return $this->metadata[$this->prefix][$key];
            } else {
                $this->metadata[$this->prefix][$key] = $value;
            }
        }

        public function hasMetadataKey($key) {
            return array_key_exists($this->prefix, $this->metadata) && array_key_exists($key, $this->metadata[$this->prefix]);
        }

        // ------- Validation -------------------------------------------------

        public function validationMessage($key = "0.0-def", $identifier = "0.0-def") {
            if ($key === "0.0-def") {
                return $this->validation[$this->prefix];
            } else if ($identifier === "0.0-def") {
                return $this->validation[$this->prefix][$key];
            }
            else {
                $this->validation[$this->prefix][$key][] = $identifier;
            }
        }

        public function isValid() {
            return empty($this->validation);
        }

        // ------- Request and model values -----------------------------------

        private $request;

        private function getRequest() {
            if ($this->request == null) {
                return $_REQUEST;
            } 

            return $this->request;
        }

        private function getRequestValue($name, $nameIndex = -1) {
            if ($this->prefix != null) {
                $name = $this->prefix . $name;
            }

            $value = $this->getRequest()[$name];
            if ($this->isNameIndex($nameIndex)) {
                $value = $value[$nameIndex];
            }

            return $value;
        }

        public function request($requestOrName = null, $nameIndex = null) {
            if ($requestOrName == null) {
                return $this->getRequest();
            } else if (is_string($requestOrName)) {
                return $this->getRequestValue($requestOrName, $nameIndex);
            }  else {
                $this->request = $requestOrName;
            }
        }

        public function requestKey($name, $nameIndex = null) {
            if ($this->prefix != null) {
                $name = $this->prefix . $name;
            }

            if ($this->isNameIndex($nameIndex)) {
                $name .= "[]";
            }

            return $name;
        }

        public function set($name, $nameIndex, $value) {
            if (!$this->isRegistration() && !$this->editable()) {
                return;
            }

            if ($this->isNameIndex($nameIndex)) {
                $array = $this[$name];
                if ($array == null) {
                    $array = array();
                }

                $array[$nameIndex] = $value;
                $this[$name] = $array;
            } else {
                $this[$name] = $value;
            }
        }

        public function get($name, $nameIndex) {
            $value = $this[$name];
            if ($this->isNameIndex($nameIndex)) {
                if (!is_array($value)) {
                    $value = explode(",", $value);
                }

                $value = $value[$nameIndex];
            }

            return $value;
        }

        public function isNameIndex($nameIndex) {
            return $nameIndex !== null && $nameIndex != -1;
        }

        public function copyFrom($data) {
            foreach ($data as $key => $value) {
                $this[$key] = $value;
            }
        }

        public function copyTo(&$dataItem) {
            foreach ($this as $key => $value) {
                $dataItem[$key] = $value;
            }
        }

        public function fields($ignoredKeys = null) {
            $keys = array();
            foreach ($this as $key => $value) {
                if ($ignoredKeys == null || !in_array($key, $ignoredKeys)) {
                    $keys[] = $key;
                }
            }
            
            return $keys;
        }

        private function hasPrefix() {
            return array_key_exists($this->prefix, $this->container);
        }

        public function keys() {
            if ($this->hasPrefix()) {
                return array_keys($this->container[$this->prefix]);
            }

            return array();
        }

        public function hasKey($key) {
            return $this->hasPrefix() && array_key_exists($key, $this->container[$this->prefix]);
        }

        public function isEmpty() {
            return empty($this->keys());
        }
    }

?>