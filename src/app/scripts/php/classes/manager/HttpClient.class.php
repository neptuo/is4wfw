<?php

    require_once("BaseHttpManager.class.php");

    class HttpClient extends BaseHttpManager {

        public function get($url, $headers = []) {
            return parent::httpGet($url, $headers);
        }

        public function getJson($url, $headers = []) {
            return $this->httpGetJson($url, $headers);
        }
    }

?>