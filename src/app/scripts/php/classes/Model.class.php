<?php

    class Model extends ArrayObject
    {
        private $isSubmit;
        private $isRender;

        public function isSubmit() {
            return $this->isSubmit;
        }

        public function submit() {
            $this->isSubmit = true;
        }
        
        public function isRender() {
            return $this->isRender;
        }

        public function render() {
            $this->isRender = true;
        }
    }

?>