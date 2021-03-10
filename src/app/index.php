<?php

    if (!file_exists("../user/instance.inc.php")) {
        header("Location: /setup.php");
        exit;
    }

    require_once("../user/instance.inc.php");
    require_once("scripts/php/includes/settings.inc.php");


    // Allow from any origin
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
        // you want to allow, and if so:
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }
    
    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            // may also be using PUT, PATCH, HEAD etc
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         
        
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    
        exit(0);
    }

    
    if (IS_WEB_STOPPED) {
        echo file_get_contents("stopped.html");
        exit;
    }

    require_once(APP_SCRIPTS_PHP_PATH . "includes/version.inc.php");
    require_once(APP_SCRIPTS_PHP_PATH . "includes/extensions.inc.php");
    require_once(APP_SCRIPTS_PHP_PATH . "libs/PhpRuntime.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "libs/Web.class.php");

    session_start();

    $phpObject = new PhpRuntime();
    $webObject = new Web();

    require_once(APP_SCRIPTS_PHP_PATH . "includes/postinit.inc.php");

    $webObject->processRequestNG();

?>