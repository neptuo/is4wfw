<?php

    class MissingEditModelException extends Exception {

        public function __construct() {
            $this->message = "Instance of edit model is required for this operation.";
        }
    }

?>