<?php

require_once(APP_SCRIPTS_PHP_PATH . "libs/BaseTagLib.class.php");
require_once("Version.class.php");

abstract class BaseHttpManager extends BaseTagLib {
    private $isSessionCookieIncluded = false;

    public function setSessionCookieIncluded($value) {
        $this->isSessionCookieIncluded = $value;
    }

    public function download($url, $filename) {
        if (!file_exists($filename)) {
            $content = self::httpGet($url, true);
            file_put_contents($filename, $content);
            return true;
        }

        return false;
    }

    protected function httpGet($url, $binary = false) {
        if (function_exists('curl_version')) {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_USERAGENT, 'phpwfw-installer'); 
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_MAXREDIRS, 5); 
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); 

            if ($this->isSessionCookieIncluded) {
                curl_setopt($curl, CURLOPT_COOKIE, 'PHPSESSID=' . $_COOKIE['PHPSESSID']); 
            }
            
            if ($binary) { 
                curl_setopt($curl, CURLOPT_BINARYTRANSFER, true); 
            }
    
            $content = curl_exec($curl);
            curl_close($curl);
            return $content;
        } else if (file_get_contents(__FILE__) && ini_get('allow_url_fopen')) {
            $content = file_get_contents($url);
            return $content;
        } else {
            // 'You have neither cUrl installed nor allow_url_fopen activated. Please setup one of those!';
            return null;
        }
    
        return null;
    }

    protected function httpGetJson($url) {
        $content = self::httpGet($url);
        if ($content != null && strlen($content) != 0) {
            $json = json_decode($content);
            return  $json;
        }

        return null;
    }
}

?>