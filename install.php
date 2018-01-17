<?php

$targetDirectoryPath = 'scripts/php/includes';
$targetFilePath = $targetDirectoryPath . '/database.inc.php';

$url = 'https://api.github.com/repos/maraf/PHP-WebFramework/releases';

if (file_exists($targetDirectoryPath)) {
    header("Location: /"); 
    exit;
}

function mylog($message) {
    echo $message . "<br />";
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
        mylog('You have neither cUrl installed nor allow_url_fopen activated. Please setup one of those!');
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

$targetUrl = $_SERVER['REQUEST_URI'];
$targetUrl = str_replace("install.php", "setup.php", $targetUrl);

if (file_exists($targetDirectoryPath)) {
    header("Location: " . $targetUrl); 
    exit;
}

mylog("Getting releases from '" . $url . "'.");

$json = httpGetJson($url);
if ($json == null) {
    mylog("Unnable to get releases.");
    exit;
}

if (!is_array($json)) {
    if (isset($json->message)) {
        mylog("Remote error: " . $json->message);
    } else {
        mylog("No release found.");
    }

    exit;
}

$targetRelease = null;
foreach ($json as $release) {
    if (!$release->draft) {
        $targetRelease = $release;
        break;
    }
}

if ($targetRelease == null) {
    mylog("Unnable to find release which is not draft.");
    exit;
}

mylog("Selected release '" . $targetRelease->tag_name . "' published at '" . $targetRelease->published_at . "' by '" . $targetRelease->author->login . "'.");

foreach ($targetRelease->assets as $asset) {
    if ($asset->content_type == "application/x-zip-compressed") {
        mylog("Selected asset '" . $asset->name . "' of size '" . $asset->size . "B'.");

        $filename = $asset->name;

        mylog("Downloading asset from '" . $asset->browser_download_url . "'.");

        if (!file_exists($filename)) {
            $content = httpGet($asset->browser_download_url, true);
            file_put_contents($filename, $content);
        }
        
        $zip = new ZipArchive();
        if ($zip->open($filename) === TRUE) {
            $zip->extractTo('.');
            $zip->close();
        }

        unlink("install.php");
        unlink($filename);
        header("Location: " . $targetUrl); 
        break;
    } else {
        mylog("Skipping asset '" . $asset->name . "' of type '" . $asset->content_type . "'.");
    }
}

exit;

?>