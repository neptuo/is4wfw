<?php

    class EditModel implements ArrayAccess, Iterator
    {
        private $prefix;
        private $metadata = array();
        private $container = array();
        private $index = 0;
        private $exceptions = array();

        public function prefix($name = "1.1-def") {
            if ($name === "1.1-def") {
                return $this->prefix;
            }

            if ($name == null) {
                $name = "";
            }

            $this->prefix = $name;
        }

        public function exception($e) {
            $this->exceptions[] = $e;
        }

        public function hasException() {
            return count($this->exceptions) > 0;
        }

        // ------- ArrayAccess ------------------------------------------------

        public function offsetSet($offset, $value) {
            if (is_null($offset)) {
                $this->container[$this->prefix][] = $value;
            } else {
                $this->container[$this->prefix][$offset] = $value;
            }
        }
    
        public function offsetExists($offset) {
            return isset($this->container[$this->prefix][$offset]);
        }
    
        public function offsetUnset($offset) {
            unset($this->container[$this->prefix][$offset]);
        }
    
        public function offsetGet($offset) {
            if (self::offsetExists($offset)) {
                return $this->container[$this->prefix][$offset];
            } else {
                return null;
            }
        }

        // ------- Iterator ---------------------------------------------------

        public function rewind(){
            $this->index = 0;
        }

        public function current(){
            $keys = self::keys();
            $value = $this[$keys[$this->index]];
            return $value;
        }

        public function key(){
            $keys = self::keys();
            $value = $keys[$this->index];
            return $value;
        }

        public function next(){
            $keys = self::keys();
            if (isset($keys[++$this->index])) {
                $value = $this[$keys[$this->index]];
                return $value;
            } else {
                return false;
            }
        }

        public function valid() {
            $keys = self::keys();
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

            $value = self::getRequest()[$name];
            if ($nameIndex !== null && $nameIndex != -1) {
                $value = $value[$nameIndex];
            }

            return $value;
        }

        public function request($requestOrName = null, $nameIndex = null) {
            if ($requestOrName == null) {
                return self::getRequest();
            } else if (is_string($requestOrName)) {
                return self::getRequestValue($requestOrName, $nameIndex);
            }  else {
                $this->request = $requestOrName;
            }
        }

        public function requestKey($name, $nameIndex = null) {
            if ($this->prefix != null) {
                $name = $this->prefix . $name;
            }

            if ($nameIndex !== null && $nameIndex != -1) {
                $name .= "[]";
            }

            return $name;
        }

        public function set($name, $nameIndex, $value) {
            if ($nameIndex !== null && $nameIndex != -1) {
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
            if ($nameIndex !== null && $nameIndex != -1) {
                $value = $value[$nameIndex];
            }

            return $value;
        }

        public function copyFrom($data) {
            foreach ($data as $key => $value) {
                $this[$key] = $value;
            }
        }

        public function fields() {
            $keys = array();
            foreach ($this as $key => $value) {
                $keys[] = $key;
            }
            
            return $keys;
        }

        private function hasPrefix() {
            return array_key_exists($this->prefix, $this->container);
        }

        public function keys() {
            if (self::hasPrefix()) {
                return array_keys($this->container[$this->prefix]);
            }

            return array();
        }

        public function hasKey($key) {
            return self::hasPrefix() && array_key_exists($key, $this->container[$this->prefix]);
        }
    }

?>