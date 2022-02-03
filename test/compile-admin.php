<?php

    require_once("../user/instance.inc.php");
    require_once("../app/scripts/php/includes/settings.inc.php");

    require_once(APP_SCRIPTS_PHP_PATH . "includes/version.inc.php");
    require_once(APP_SCRIPTS_PHP_PATH . "includes/extensions.inc.php");
    require_once(APP_SCRIPTS_PHP_PATH . "libs/PhpRuntime.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "libs/Web.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/ViewHelper.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/utils/FileUtils.class.php");

    $phpObject = new PhpRuntime();
    $webObject = new Web();

    $files = FileUtils::searchDirectoryRecursive(APP_ADMIN_PATH, ".php");
    var_dump($files);
    
    $virtuals = [];
    foreach ($files as $file) {
        $virtual = str_replace(APP_ADMIN_PATH, "~/", $file);
        $virtual = str_replace(".php", "", $virtual);
        $virtuals [] = $virtual;
    }

    var_dump($virtuals);

    foreach ($virtuals as $virtual) {
        $keys = $vObject->getVirtualPathKeys($virtual);
        $template = $vObject->getParsedTemplate($keys);
        if ($template == null) {
            $template = $vObject->parseTemplate($keys, ViewHelper::getViewContent($virtual));
        }
    }

    echo "Done.";

?>