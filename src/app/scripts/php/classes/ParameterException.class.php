<?php

    class ParameterException extends Exception {

        public function __construct($parameter, $message) {
            $this->message = "Parameter '$parameter': $message.";
        }
    }

?>