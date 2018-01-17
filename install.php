<?php

$targetDirectoryPath = 'scripts/php/includes';
$targetFilePath = $targetDirectoryPath . '/database.inc.php';

$url = 'https://api.github.com/repos/maraf/PHP-WebFramework/releases';

if (file_exists($targetFilePath)) {
    header("Location: /"); 
    exit;
}

function httpGet($url, $binary = false) {
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
        echo 'You have neither cUrl installed nor allow_url_fopen activated. Please setup one of those!';
        exit;
    }

    return null;
}

function httpGetJson($url) {
    $content = httpGet($url);
    if ($content != null && strlen($content) != 0) {
        $json = json_decode($content);
        return  $json;
    }

    return null;
}

if (!file_exists($targetDirectoryPath)) {
    $json = httpGetJson($url);
    if ($json == null) {
        echo 'Unnable to get releases.';
        exit;
    }

    if ($json->message != null) {
        echo $json->message;
        exit;
    }
    
    if (!is_array($json) || $json[0] == null) {
        echo 'No release found.';
        exit;
    }
    
    $json = httpGetJson($json[0]->assets_url);
    if ($json == null) {
        echo 'Unnable to get assets for latest release.';
        exit;
    }
    
    foreach ($json as $item) {
        if ($item->content_type == 'application/x-zip-compressed') {
            $filename = $item->name;
    
            echo $item->browser_download_url;

            // $content = httpGet($item->browser_download_url, true);
            // file_put_contents($filename, $content);
            
            // $zip = new ZipArchive();
            // if ($zip->open($filename) === TRUE) {
            //     $zip->extractTo('.');
            //     $zip->close();
            // }
    
            // unlink($filename);

            $targetUrl = $_SERVER['REQUEST_URI'];
            $targetUrl = str_replace("install.php", "setup.php", $targetUrl);
            header("Location: " . $targetUrl); 
            break;
        }
    }

    exit;
}

?>