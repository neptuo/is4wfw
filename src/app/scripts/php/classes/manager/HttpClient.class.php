<?php

    require_once("BaseHttpManager.class.php");

    class HttpClient extends BaseHttpManager {

        public function get($url, $headers = []) {
            return parent::httpGet($url, $headers);
        }
    }

?>