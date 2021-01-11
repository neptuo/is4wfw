<?php

    class HttpUtils {

        public static function isHttpMethod($method) {
            return $_SERVER['REQUEST_METHOD'] == $method;
        }

        public static function isPost() {
            return self::isHttpMethod("POST");
        }

        public static function isGet() {
            return self::isHttpMethod("GET");
        }

        public static function currentAbsoluteUrl() {
            return $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        }

    }

?>