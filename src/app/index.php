<?php

    require_once("scripts/php/includes/settings.inc.php");

    if (IS_WEB_STOPPED) {
        echo file_get_contents("stopped.html");
        exit;
    }
    
    require_once(APP_SCRIPTS_PHP_PATH . "includes/version.inc.php");
    require_once(APP_SCRIPTS_PHP_PATH . "includes/extensions.inc.php");

    require_once(APP_SCRIPTS_PHP_PATH . "includes/preinit.inc.php");

    if (array_key_exists("WEB_PAGE_PATH", $_GET)) {
        // Web.

        ini_set('pcre.jit', false);

        require_once(APP_SCRIPTS_PHP_PATH . "libs/PhpRuntime.class.php");
        require_once(APP_SCRIPTS_PHP_PATH . "libs/Web.class.php");
    
        session_start();
    
        $phpObject = new PhpRuntime();
        $webObject = new Web();
    
        require_once(APP_SCRIPTS_PHP_PATH . "includes/postinit.inc.php");
    
        $webObject->processRequestNG();
    } else {
        // Files.

        require("file.inc.php");
    }

?>