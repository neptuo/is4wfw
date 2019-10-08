<?php

    class EditModel extends ArrayObject
    {
        private $isRegistration;
        private $isSubmit;
        private $isRender;
        private $request;

        public function isRegistration() {
            return $this->isRegistration;
        }
        
        public function registration($value = true) {
            $this->isRegistration = $value;
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

        public function request($request = null) {
            if ($request == null) {
                if ($this->request == null) {
                    return $_REQUEST;
                } 

                return $this->request;
            } else {
                $this->request = $request;
            }
        }
    }

?>