<?php

    class EditModel extends ArrayObject
    {
        private $isRegistration;
        private $isSubmit;
        private $isRender;

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
    }

?>