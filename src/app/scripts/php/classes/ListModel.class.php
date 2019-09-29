<?php

    class ListModel extends ArrayObject
    {
        private $isRegistration;
        private $isRender;

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
    }

?>