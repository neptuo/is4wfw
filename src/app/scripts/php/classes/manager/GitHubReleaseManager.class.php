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
            if ($release->draft) {
                continue;
            }
                
            $fullUrl = null;
            $fullSize = null;
            $patchUrl = null;
            $patchSize = null;
            foreach ($release->assets as $asset) {
                if ($asset->content_type == "application/x-zip-compressed") {
                    $assetName = $asset->name;
                    if (strpos($assetName, '-patch') > 0) {
                        $patchUrl = $asset->browser_download_url;
                        $patchSize = $asset->size;
                    } else {
                        $fullUrl = $asset->browser_download_url;
                        $fullSize = $asset->size;
                    }
                }
            }

            if ($fullUrl != null || $patchUrl != null) {
                $release = array(
                    'version' => Version::parse($release->tag_name),
                    'published_at' => $release->published_at,
                    'html_url' => $release->html_url,
                    'download' => array()
                );

                if ($fullUrl != null) {
                    $release['download']['full'] = array(
                        'url' => $fullUrl,
                        'size' => $fullSize
                    );
                }

                if ($patchUrl != null) {
                    $release['download']['patch'] = array(
                        'url' => $patchUrl,
                        'size' => $patchSize
                    );
                }
                
                $result['data'][] = $release;
            }
        }

        $result['result'] = true;
        return $result;
    }

    public function download($url, $filename) {
        if (!file_exists($filename)) {
            $content = self::httpGet($url, true);
            file_put_contents($filename, $content);
            return true;
        }

        return false;
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
            // 'You have neither cUrl installed nor allow_url_fopen activated. Please setup one of those!';
            return null;
        }
    
        return null;
    }

    private function httpGetJson($url) {
        $content = self::httpGet($url);
        if ($content != null && strlen($content) != 0) {
            $json = json_decode($content);
            return  $json;
        }

        return null;
    }
}

?>