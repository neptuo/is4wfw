<?php

require_once(APP_SCRIPTS_PHP_PATH . "libs/BaseTagLib.class.php");
require_once("Version.class.php");

class GitHubReleaseManager extends BaseTagLib {
    private static $Url = 'https://api.github.com/repos/maraf/PHP-WebFramework/releases';

    public function getList() {
        $result = array('result' => false, 'log' => '', 'data' => array());

        $json = self::httpGetJson(GitHubReleaseManager::$Url);
        if ($json == null) {
            $result['log'] .= "Unnable to get releases from '" . $url . "'.";
            return $result;
        }

        if (!is_array($json)) {
            if (isset($json->message)) {
                $result['log'] .= "Remote error: " . $json->message;
            } else {
                $result['log'] .= "No release found.";
            }

            return $result;
        }

        foreach ($json as $release) {
            if (!$release->draft) {
                
                $url = null;
                $size = null;
                foreach ($release->assets as $asset) {
                    if ($asset->content_type == "application/x-zip-compressed") {
                        $url = $asset->browser_download_url;
                        $size = $asset->size;
                    }
                }

                if ($url != null && $size != null) {
                    $result['data'][] = array(
                        'version' => Version::parse($release->tag_name),
                        'published_at' => $release->published_at,
                        'download_url' => $url,
                        'download_size' => $size
                    );
                }
            }
        }

        $result['result'] = true;
        return $result;
    }

    private function httpGet($url, $binary = false) {
        if (function_exists('curl_version')) {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_USERAGENT, 'phpwfw-installer'); 
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_MAXREDIRS, 5); 
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); 
            
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
            mylog('You have neither cUrl installed nor allow_url_fopen activated. Please setup one of those!');
            exit;
        }
    
        return null;
    }

    function httpGetJson($url) {
        $content = self::httpGet($url);
        if ($content != null && strlen($content) != 0) {
            $json = json_decode($content);
            return  $json;
        }

        return null;
    }
}

?>