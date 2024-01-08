<?php

    class HttpUtils {

        public static function httpMethod() {
            return $_SERVER['REQUEST_METHOD'];
        }

        public static function isHttpMethod($method) {
            return $_SERVER['REQUEST_METHOD'] == $method;
        }

        public static function isGet() {
            return self::isHttpMethod("GET");
        }

        public static function isPost() {
            return self::isHttpMethod("POST");
        }

        public static function isPut() {
            return self::isHttpMethod("PUT");
        }

        public static function isDelete() {
            return self::isHttpMethod("DELETE");
        }

        public static function currentAbsoluteUrl() {
            return $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        }

    }

?>