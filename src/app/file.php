<?php

require_once("../user/instance.inc.php");
require_once("scripts/php/includes/settings.inc.php");

require_once(APP_SCRIPTS_PHP_PATH . "includes/version.inc.php");
require_once(APP_SCRIPTS_PHP_PATH . "includes/extensions.inc.php");
require_once(APP_SCRIPTS_PHP_PATH . "libs/Database.class.php");
require_once(APP_SCRIPTS_PHP_PATH . "libs/File.class.php");
require_once(APP_SCRIPTS_PHP_PATH . "libs/FileAdmin.class.php");
require_once(APP_SCRIPTS_PHP_PATH . "classes/UrlResolver.class.php");
require_once(APP_SCRIPTS_PHP_PATH . "classes/manager/EmbeddedResourceManager.class.php");

error_reporting(0);
session_start();

if (array_key_exists('fid', $_REQUEST)) {
    $fileId = $_REQUEST['fid'];

    $dbObject = new Database();
    $dbObject->setCacheResults('NONE');

    $file = $dbObject->fetchAll('SELECT `type`, `content` FROM `page_file` WHERE `id` = ' . $fileId . ';');
    if (count($file) == 1) {
        // Try cached file ...
        $currentEtag = sha1($file[0]['content']);
        $requestEtag = "";
        if (array_key_exists('HTTP_IF_NONE_MATCH', $_SERVER)) {
            $requestEtag = trim($_SERVER['HTTP_IF_NONE_MATCH']);
        }


        header("Cache-Control: private, max-age=10800, pre-check=10800");
        header("Etag: " . $currentEtag);
        if ($currentEtag == $requestEtag) {
            header("HTTP/1.1 304 Not Modified");
            exit;
        }

        $fileType = "text/plain";
        switch ($file[0]['type']) {
            case WEB_TYPE_CSS: 
                $fileType = "text/css";
                break;
            case WEB_TYPE_JS: 
                $fileType = "application/x-javascript";
                break;
        }

        $file[0]['content'] = str_replace("~/", INSTANCE_URL, $file[0]['content']);

        // Zipovani ...
        $encoding = false;
        /* $acceptEnc = $_SERVER['HTTP_ACCEPT_ENCODING'];
        if(headers_sent()) {
          $encoding = false;
        } elseif(strpos($acceptEnc, 'x-gzip') !== false) {
          $encoding = 'x-gzip';
        } elseif(strpos($acceptEnc,'gzip') !== false) {
          $encoding = 'gzip';
        } else {
          $encoding = false;
        } */

        $return = $file[0]['content'];

        $size = strlen($return);
        $return = gzcompress($return, 9);
        $return = substr($return, 0, $size);
        if ($encoding) {
            header('Content-Encoding: ' . $encoding);
            header('Content-Type: ' . $fileType);
            header('Content-Length: ' . strlen($file[0]['content']));
            header('Content-Transfer-Encoding: binary');
            print("\x1f\x8b\x08\x00\x00\x00\x00\x00");
            echo $file[0]['content'];
            exit;
        } else {
            header('Content-Type: ' . $fileType);
            header('Content-Length: ' . strlen($file[0]['content']));
            header('Content-Transfer-Encoding: binary');
            echo $file[0]['content'];
            exit;
        }
    } else {
        header("HTTP/1.1 404 Not Found");
        echo '<h1 class="error">Error 404</h1><p class="error">Requested file doesn\'t exists.</p>';
        exit;
    }
} elseif (array_key_exists('rid', $_REQUEST)) {
    $fileId = $_REQUEST['rid'];
    $dbObject = new Database();
    $dbObject->setCacheResults('NONE');
    $flObject = new File();
    $file = $dbObject->fetchAll("SELECT `id`, `dir_id`, `name`, `url`, `type`, `timestamp` FROM `file` WHERE `id` = " . $fileId . ";");

    if (count($file) == 1) {
        $filePath = $flObject->getPhysicalPathTo($file[0]['dir_id']) . $file[0][FileAdmin::$FileSystemItemPath] . "." . FileAdmin::$FileExtensions[$file[0]['type']];
        //echo $filePath;
        $updTime = filemtime($filePath);

        // Try cached file ...
        header("Cache-Control: private, max-age=10800, pre-check=10800");
        header("Pragma: private");
        header("Expires: " . date(DATE_RFC822, strtotime("+7 day")));
        header("Last-Modified: " . gmdate("D, d M Y H:i:s", $updTime) . " GMT");
        // echo $updTime.' - '.getenv("HTTP_IF_MODIFIED_SINCE").' ; ';
        if (array_key_exists("HTTP_IF_MODIFIED_SINCE", $_SERVER) && $updTime <= strtotime($_SERVER["HTTP_IF_MODIFIED_SINCE"])) {
            //header("HTTP/1.1 304 Not Modified");
            header('Last-Modified: ' . $_SERVER['HTTP_IF_MODIFIED_SINCE'], true, 304);
            exit;
        }

        //$fileExt = ($file[0]['type'] == WEB_TYPE_JPG || $file[0]['type'] == WEB_TYPE_GIF || $file[0]['type'] == WEB_TYPE_PNG) ? "image/".FileAdmin::$FileExtensions[$file[0]['type']] : "document/".$file[0]['type'];
        $fileExt = FileAdmin::$FileMimeTypes[$file[0]['type']];

        if (array_key_exists("width", $_GET) && array_key_exists("height", $_GET)) {
            $width = $_GET['width'];
            $height = $_GET['height'];
            
            $thumbPath = CACHE_IMAGES_PATH . $file[0]['dir_id'] . '-' . $file[0]['id'] . '-' . $file[0]['name'] . '_' . $width . 'x' . $height . '.' . FileAdmin::$FileExtensions[$file[0]['type']];

            if (file_exists($thumbPath) && is_readable($thumbPath)) {
                $filePath = $thumbPath;
            } else {
                $flObject->createThumb($filePath, $thumbPath, $width, $height, $file[0]['type']);
                $filePath = $thumbPath;
            }
        } else if (array_key_exists("width", $_GET)) {
            $width = $_GET['width'];
            list($orWidth, $orHeight, $orType) = getimagesize($filePath);
            $ratio = $width / $orWidth;
            $height = round($ratio * $orHeight);

            $thumbPath = CACHE_IMAGES_PATH . $file[0]['dir_id'] . '-' . $file[0]['id'] . '-' . $file[0]['name'] . '_' . $width . 'x' . $height . '.' . FileAdmin::$FileExtensions[$file[0]['type']];

            if (file_exists($thumbPath) && is_readable($thumbPath)) {
                $filePath = $thumbPath;
            } else {
                $flObject->createThumb($filePath, $thumbPath, $width, $height, $file[0]['type']);
                $filePath = $thumbPath;
            }
        } else if (array_key_exists("height", $_GET)) {
            $height = $_GET['height'];
            list($orWidth, $orHeight, $orType) = getimagesize($filePath);
            $ratio = $height / $orHeight;
            $width = round($ratio * $orWidth);

            $thumbPath = CACHE_IMAGES_PATH . $file[0]['dir_id'] . '-' . $file[0]['id'] . '-' . $file[0]['name'] . '_' . $width . 'x' . $height . '.' . FileAdmin::$FileExtensions[$file[0]['type']];

            if (file_exists($thumbPath) && is_readable($thumbPath)) {
                $filePath = $thumbPath;
            } else {
                $flObject->createThumb($filePath, $thumbPath, $width, $height, $file[0]['type']);
                $filePath = $thumbPath;
            }
        }

        if (file_exists($filePath) && is_readable($filePath)) {
            $fileSize = filesize($filePath);

            header('Content-Type: ' . $fileExt);
            header('Accept-Ranges: bytes');
            header('Content-Length: ' . $fileSize);
            header('Content-Disposition: inline; filename=' . $file[0]['name'] . "." . FileAdmin::$FileExtensions[$file[0]['type']]);
            header('Content-Transfer-Encoding: binary');
            header("Last-Modified: " . gmdate("D, d M Y H:i:s", $updTime) . " GMT");
            $file = @fopen($filePath, 'rb');
            if ($file) {
                fpassthru($file);
                exit;
            } else {
                header("HTTP/1.1 404 Not Found");
                echo '<h1 class="error">Error 404</h1><p class="error">Requested file doesn\'t exists.</p>';
                exit;
            }
        } else {
            header("HTTP/1.1 404 Not Found");
            echo '<h1 class="error">Error 404</h1><p class="error">Requested file doesn\'t exists.</p>';
            exit;
        }
    } else {
        header("HTTP/1.1 404 Not Found");
        echo '<h1 class="error">Error 404</h1><p class="error">Requested file doesn\'t exists.</p>';
        exit;
    }
} elseif (array_key_exists('path', $_REQUEST)) {
    $dbObject = new Database();
    $flObject = new File();

    $filePath = INSTANCE_PATH . $_REQUEST['path'];
    $updTime = filemtime($filePath);
    // Try cached file ...
    // header("Cache-Control: private, max-age=10800, pre-check=10800");
    // header("Pragma: private");
    // header("Expires: " . date(DATE_RFC822, strtotime("+7 day")));
    // header("Last-Modified: " . gmdate("D, d M Y H:i:s", $updTime) . " GMT");

    //echo $updTime.' - '.getenv("HTTP_IF_MODIFIED_SINCE").' ; ';
    // if ($_SERVER["HTTP_IF_MODIFIED_SINCE"] && $updTime <= strtotime($_SERVER["HTTP_IF_MODIFIED_SINCE"])) {
    //     //header("HTTP/1.1 304 Not Modified");
    //     header('Last-Modified: ' . $_SERVER['HTTP_IF_MODIFIED_SINCE'], true, 304);
    //     exit;
    // }

    if (file_exists($filePath) && is_readable($filePath)) {
        $fileSize = filesize($filePath);
        header('Content-Type: ' . $fileExt);
        header('Accept-Ranges: bytes');
        header('Content-Length: ' . $fileSize);
        header('Content-Disposition: inline; filename=' . $file[0]['name'] . "." . FileAdmin::$FileExtensions[$file[0]['type']]);
        header('Content-Transfer-Encoding: binary');
        header("Last-Modified: " . gmdate("D, d M Y H:i:s", $updTime) . " GMT");
        $file = @ fopen($filePath, 'rb');
        if ($file) {
            fpassthru($file);
            exit;
        } else {
            header("HTTP/1.1 404 Not Found");
            echo '<h1 class="error">Error 404</h1><p class="error">Requested file doesn\'t exists.</p>';
            exit;
        }
    } else {
        header("HTTP/1.1 404 Not Found");
        echo '<h1 class="error">Error 404</h1><p class="error">Requested file doesn\'t exists.</p>';
        exit;
    }
} elseif (array_key_exists('erid', $_REQUEST)) {
    global $dbObject;
    $dbObject = new Database();
    $dbObject->setCacheResults('NONE');
    $er = EmbeddedResourceManager::get($_REQUEST['erid']);

    if ($er->getCache() == 'Always') {
        header("Cache-Control: private, max-age=10800, pre-check=10800");
        header("Pragma: private");
        header("Expires: " . date(DATE_RFC822, strtotime("+7 day")));
        header("Last-Modified: " . gmdate("D, d M Y H:i:s", time()) . " GMT");
        // ...
    }

    if ($er->getUrl() != '') {
        if (strpos($er->getUrl(), '://') != -1) {
            //echo 'Location ...';
            //if(!$_SERVER["HTTP_IF_MODIFIED_SINCE"]) {
            $file = @ fopen($er->getUrl(), 'rb');
            if ($file) {
                fpassthru($file);
                exit;
            } else {
                header("HTTP/1.1 404 Not Found");
                echo '<h1 class="error">Error 404</h1><p class="error">Requested file doesn\'t exists.</p>';
                exit;
            }
            //}
        } else {
            echo 'Local ...';
        }
    } else {
        $filePath = ''; // ;-)

        $updTime = filemtime($filePath);
        //echo $updTime.' - '.getenv("HTTP_IF_MODIFIED_SINCE").' ; ';
        if (array_key_exists("HTTP_IF_MODIFIED_SINCE", $_SERVER) && $updTime <= strtotime($_SERVER["HTTP_IF_MODIFIED_SINCE"])) {
            //header("HTTP/1.1 304 Not Modified");
            header('Last-Modified: ' . $_SERVER['HTTP_IF_MODIFIED_SINCE'], true, 304);
            exit;
        }
    }
} else {
    header("HTTP/1.1 404 Not Found");
    echo '<h1 class="error">Error 404</h1><p class="error">Requested file doesn\'t exists.</p>';
    exit;
}

?>
