<?php

    require_once("ViewMissingException.class.php");

    class ViewHelper {

        private static function getServerPath($viewPath) {
            $serverPath = $viewPath;
            if (strpos($serverPath, '.view') == '') {
                $serverPath = ViewHelper::resolveViewPath($serverPath);
                $viewPath .= ".view";
            } elseif (strpos($serverPath, '.php') == '') {
                $serverPath .= '.php';
            }

            $serverPath = str_replace('~/', APP_ADMIN_PATH . '/', $serverPath);
            if (!file_exists($serverPath)) {
                throw new ViewMissingException($viewPath, $serverPath);
            }

            return $serverPath;
        }

        public static function getViewContent($viewPath) {
            $serverPath = self::getServerPath($viewPath);
            return file_get_contents($serverPath);
        }
        
        public static function getViewContentIdentifier($viewPath) {
            $content = self::getViewContent($viewPath);
            return sha1($content);
        }

        public static function resolveViewPath($path) {
            return ViewHelper::resolveViewRoot($path) . '.view.php';
        }

        public static function resolveViewRoot($path) {
            return str_replace('~/', APP_ADMIN_PATH . '/', $path);
        }

        public static function resolveUrl($url) {
            return str_replace('~/', INSTANCE_URL, $url);
        }

    }

?>
