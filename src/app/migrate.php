<?php

    if (!file_exists("../user/instance.inc.php")) {
        header("Location: /setup.php");
        exit;
    }

    require_once("../user/instance.inc.php");
    require_once("scripts/php/includes/settings.inc.php");

    // Migrate DB

    require_once(APP_SCRIPTS_PHP_PATH . "libs/Database.class.php");

    $db = new Database(false);
    $db->disableCache();
    $db->getDataAccess()->connect(WEB_DB_HOSTNAME, WEB_DB_USER, WEB_DB_PASSWORD, WEB_DB_DATABASE, false);
    $db->getDataAccess()->transaction();
    
    $dbObject = $db;
    
    require_once(APP_SCRIPTS_PHP_PATH . "includes/autoupdate.inc.php");
    
    $db->getDataAccess()->disconnect();

    // Regenerate module scripts

    require_once(APP_SCRIPTS_PHP_PATH . "includes/version.inc.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/Module.class.php");
    
    ModuleGenerator::all();
    
    // Drop system property cache

    require_once(APP_SCRIPTS_PHP_PATH . "classes/manager/SystemProperty.class.php");

    unlink(CACHE_SYSTEMPROPERTY_PATH . SystemPropertyGenerator::loaderFileName);

    if (array_key_exists("return", $_GET)) {
        echo '<a href="' . $_GET["return"] . '">Return back</a>';
    }

?>