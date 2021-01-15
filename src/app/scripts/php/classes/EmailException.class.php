<?php

    class EmailException extends Exception {

        public function __construct($to, $subject, $errorMessage) {
            $this->message = "Failed to email to '$to' with subject '$subject' with error '$errorMessage'.";
        }
    }

?>