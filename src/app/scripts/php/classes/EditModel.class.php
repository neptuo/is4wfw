<?php

    class EditModel implements ArrayAccess, Iterator
    {
        private $prefix;
        private $metadata = array();
        private $container = array();
        private $index = 0;

        public function prefix($name) {
            if ($name == null) {
                $name = "";
            }

            $this->prefix = $name;
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
            return self::offsetExists($offset) ? $this->container[$this->prefix][$offset] : null;
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
        
        private $isPrimary;

        private $isRegistration;
        private $isLoad;
        private $isSubmit;
        private $isSave;
        private $isRender;
        private $isSaved;
        
        public function primary($value) {
            $this->isPrimary = $value;
        }

        public function isRegistration() {
            return $this->isRegistration;
        }
        
        public function registration($value = true) {
            $this->isRegistration = $value;
        }

        public function isLoad() {
            return $this->isLoad;
        }

        public function canLoad() {
            if ($this->isPrimary) {
                return true;
            }

            return self::isLoad();
        }
        
        public function load($value = true) {
            if ($this->isPrimary) {
                $this->isLoad = $value;
            }
        }

        public function isSubmit() {
            return $this->isSubmit;
        }

        public function canSubmit($canSubmit = true) {
            if ($this->isPrimary) {
                return $canSubmit;
            }

            return self::isSubmit();
        }

        public function submit($value = true) {
            if ($this->isPrimary) {
                $this->isSubmit = $value;
            }
        }

        public function isSave() {
            return $this->isSave;
        }

        public function canSave($canSave = true) {
            if ($this->isPrimary) {
                return $canSave;
            }

            return self::isSave();
        }

        public function save($value = true) {
            if ($this->isPrimary) {
                $this->isSave = $value;
            }
        }
        
        public function isRender() {
            return $this->isRender;
        }

        public function canRender() {
            if ($this->isPrimary) {
                return true;
            }

            return self::isRender();
        }

        public function render($value = true) {
            if ($this->isPrimary) {
                $this->isRender = $value;
            }
        }
        
        public function isSaved() {
            return $this->isSaved;
        }

        public function canSaved($canSaved = true) {
            if ($this->isPrimary) {
                return $canSaved;
            }

            return self::isSaved();
        }

        public function saved($value = true) {
            if ($this->isPrimary) {
                $this->isSaved = $value;
            }
        }

        // ------- Metadata ---------------------------------------------------

        public function metadata($key, $value = "0.0-def") {
            if ($value === "0.0-def") {
                // Get.
                return $this->metadata[$this->prefix][$key];
            } else {
                // Set.
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
            if ($nameIndex != null && $nameIndex != -1) {
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

            if ($nameIndex != null && $nameIndex != -1) {
                $name .= "[]";
            }

            return $name;
        }

        public function set($name, $nameIndex, $value) {
            if ($nameIndex != -1) {
                $this[$name][$nameIndex] = $value;
            } else {
                $this[$name] = $value;
            }
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

        public function keys() {
            return array_keys($this->container[$this->prefix]);
        }

        public function hasKey($key) {
            return array_key_exists($key, $this->container[$this->prefix]);
        }
    }

?>