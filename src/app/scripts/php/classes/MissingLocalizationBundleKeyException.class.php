<?php

class MissingLocalizationBundleKeyException extends Exception {

    public $key;
    public $bundleName;
    public $languageName;

    public function __construct($key, $bundleName, $languageName) {
        $this->key = $key;
        $this->bundleName = $bundleName;
        $this->languageName = $languageName;

        $this->message = "Missing key '$key' in localization bundle '$bundleName' for language '$languageName'.";
    }
}

?>