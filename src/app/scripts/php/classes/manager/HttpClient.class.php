<?php

    require_once("BaseHttpManager.class.php");

    class HttpClient extends BaseHttpManager {

        public function get($url, $binary = false) {
            return parent::httpGet($url, $binary);
        }
    }

?>