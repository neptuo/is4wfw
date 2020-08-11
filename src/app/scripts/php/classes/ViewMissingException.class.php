<?php

class ViewMissingException extends Exception {

    public $ServerPath;
    public $ViewPath;

    public function __construct($viewPath, $serverPath) {
        $this->ViewPath = $viewPath;
        $this->ServerPath = $serverPath;

        $this->message = "View '$viewPath' doesn't exist!";
    }
}

?>