<?php

    require_once("ViewMissingException.class.php");

    class ViewHelper {

        public static function getViewContent($viewPath) {
            $serverPath = $viewPath;
            if (strpos($serverPath, '.view') == '') {
                $serverPath = ViewHelper::resolveViewPath($serverPath);
                $viewPath .= ".view";
            } elseif (strpos($serverPath, '.php') == '') {
                $serverPath .= '.php';
            }

            $serverPath = str_replace('~/', APP_ADMIN_PATH . '/', $serverPath);
            if (file_exists($serverPath)) {
                return file_get_contents($serverPath);
            } else {
                throw new ViewMissingException($viewPath, $serverPath);
            }
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
