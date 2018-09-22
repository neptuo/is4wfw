<?php

class DataAccessException extends Exception {

    public $errorCode;
    public $errorMessage;
    public $query;

    public function __construct($errorCode, $errorMessage, $query) {
        $this->errorCode = $errorCode;
        $this->errorMessage = $errorMessage;
        $this->query = $query;

        $this->message = "ErrorCode = " . $errorCode . ", " . PHP_EOL . "ErrorMessage = " . $errorMessage . ", " . PHP_EOL . "Sql = " . $query;
    }
}

?>