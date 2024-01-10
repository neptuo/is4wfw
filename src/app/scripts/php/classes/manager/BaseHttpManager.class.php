<?php

require_once(APP_SCRIPTS_PHP_PATH . "libs/BaseTagLib.class.php");
require_once("Version.class.php");

abstract class BaseHttpManager extends BaseTagLib {
    private $isSessionCookieIncluded = false;
    private $username;
    private $password;

    public function setSessionCookieIncluded($value) {
        $this->isSessionCookieIncluded = $value;
    }

    public function setBasicAuthentication($username, $password) {
        $this->username = $username;
        $this->password = $password;
    }

    public function download($url, $filename, $isExistenceChecked = true) {
        if (!$isExistenceChecked || !file_exists($filename)) {
            $content = $this->httpGet($url);
            file_put_contents($filename, $content);
            return true;
        }

        return false;
    }

    protected function httpGet($url, $headers = []) {
        if (function_exists('curl_version')) {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_USERAGENT, 'is4wfw'); 
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_MAXREDIRS, 5); 
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); 
            
            if ($this->username && $this->password) {
                curl_setopt($curl, CURLOPT_USERPWD, $this->username . ":" . $this->password);
            }

            if ($this->isSessionCookieIncluded) {
                curl_setopt($curl, CURLOPT_COOKIE, 'PHPSESSID=' . $_COOKIE['PHPSESSID']); 
            }

            if (!empty($headers)) {
                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
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

    protected function httpGetJson($url, $headers = []) {
        if (!array_key_exists("Accept", $headers)) {
            $headers["Accept"] = "application/json";
        }
        
        $content = $this->httpGet($url, $headers);
        if ($content != null && strlen($content) != 0) {
            $json = json_decode($content);
            return  $json;
        }

        return null;
    }
}

?>