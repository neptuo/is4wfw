<?php

    class TemplateParserException extends Exception {

        public function __construct(string $message) {
            $this->message = $message;
        }
    }

?>