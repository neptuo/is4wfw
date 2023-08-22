<?php

    // Similar code exists in standard migrate.php

    require_once(__DIR__ . "/" . "../app/scripts/php/includes/settings.inc.php");

    // Migrate DB

    require_once(APP_SCRIPTS_PHP_PATH . "libs/Database.class.php");

    $db = new Database(false);
    $db->disableCache();
    $db->getDataAccess()->connect(WEB_DB_HOSTNAME, WEB_DB_USER, WEB_DB_PASSWORD, WEB_DB_DATABASE, false, false);
    $db->getDataAccess()->transaction();

    // Duplicated in setup.php
    if (count($db->fetchAll("SHOW TABLES LIKE 'system_property';")) == 0) {
        echo "Import db schema and default data" . PHP_EOL;

        $importFilePath = APP_PATH . "data/default/default_330.sql";
        $importFile = fopen($importFilePath, 'r') or die('Cannot open file:  ' . $importFilePath);
        $user = [
            "name" => "admin",
            "surname" => "admin",
            "login" => "admin",
            "password" => "admin"
        ];
        $batch = '';
        while (($line = fgets($importFile)) !== false) {
            // Skip it if it's a comment
            if (substr($line, 0, 2) == '--' || $line == '')
                continue;

            // Add this line to the current segment
            $batch .= $line;
            
            // If it has a semicolon at the end, it's the end of the query
            if (substr(trim($line), -1, 1) == ';') {

                $batch = str_replace("{user-name}", $user['name'], $batch);
                $batch = str_replace("{user-surname}", $user['surname'], $batch);
                $batch = str_replace("{user-login}", $user['login'], $batch);
                $batch = str_replace("{user-password}", $user['password'], $batch);

                // Perform the query
                $db->execute($batch);

                // Reset temp variable to empty
                $batch = '';
            }
        }
    }

    
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

?>