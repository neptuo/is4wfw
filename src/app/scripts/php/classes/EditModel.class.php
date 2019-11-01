<?php

    class EditModel extends ArrayObject
    {
        private $prefix;

        private $isRegistration;
        private $isLoad;
        private $isSubmit;
        private $isRender;
        private $isSaved;
        private $request;

        public function prefix($name) {
            return $this->prefix = $name;
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
        
        public function load($value = true) {
            $this->isLoad = $value;
        }

        public function isSubmit() {
            return $this->isSubmit;
        }

        public function submit($value = true) {
            $this->isSubmit = $value;
        }
        
        public function isRender() {
            return $this->isRender;
        }

        public function render() {
            $this->isRender = true;
        }
        
        public function isSaved() {
            return $this->isSaved;
        }

        public function saved($value = true) {
            $this->isSaved = $value;
        }

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
    }

?>