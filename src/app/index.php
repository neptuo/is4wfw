<?php

    $userInstance = "../user/instance.inc.php";
    if (!file_exists($userInstance)) {
        header("Location: /setup.php");
        exit;
    }

    require_once($userInstance);
    require_once("scripts/php/includes/settings.inc.php");

    require_once(APP_SCRIPTS_PHP_PATH . "includes/version.inc.php");
    require_once(APP_SCRIPTS_PHP_PATH . "includes/extensions.inc.php");

    require_once(APP_SCRIPTS_PHP_PATH . "includes/preinit.inc.php");

    if (array_key_exists("WEB_PAGE_PATH", $_GET)) {
        // Public web.

        if (IS_WEB_STOPPED) {
            echo file_get_contents("stopped.html");
            exit;
        }

        require_once(APP_SCRIPTS_PHP_PATH . "libs/PhpRuntime.class.php");
        require_once(APP_SCRIPTS_PHP_PATH . "libs/Web.class.php");
    
        session_start();
    
        $phpObject = new PhpRuntime();
        $webObject = new Web();
    
        require_once(APP_SCRIPTS_PHP_PATH . "includes/postinit.inc.php");
    
        $webObject->processRequestNG();
    } else if (array_key_exists("VIEW_PAGE_PATH", $_GET)) {
        // Administration.

        if (IS_ADMIN_STOPPED) {
            echo file_get_contents("stopped.html");
            exit;
        }

        require_once(APP_SCRIPTS_PHP_PATH . "libs/PhpRuntime.class.php");
        require_once(APP_SCRIPTS_PHP_PATH . "libs/Web.class.php");
        require_once(APP_SCRIPTS_PHP_PATH . "classes/ViewHelper.class.php");
    
        session_start();
  
        $phpObject = new PhpRuntime();
        $webObject = new Web();
    
        if (defined("IS_ADMIN_HTTPS") && constant("IS_ADMIN_HTTPS") === true) {
            $webObject->redirectToHttps();
        }
      
        $phpObject->register('v', 'php.libs.View');
        $phpObject->register('m', 'php.libs.Menu');
      
        require_once(APP_SCRIPTS_PHP_PATH . "includes/postinitview.inc.php");
    
        $phpObject->register("controls", "php.libs.TemplateDirectory", ["path" => ViewHelper::resolveViewRoot("~/templates/controls")]);
        $phpObject->register("views", "php.libs.TemplateDirectory", ["path" => ViewHelper::resolveViewRoot("~/views")]);
        $phpObject->register("floorball", "php.libs.TemplateDirectory", ["path" => ViewHelper::resolveViewRoot("~/views/floorball")]);
        $phpObject->autolib("var")->setValue("virtualUrl", substr($phpObject->autolib("v")->getCurrentVirtualUrl(), 2));
      
        $indexContent = file_get_contents(APP_ADMIN_PATH . "index.view.php");
        $pageContent = $webObject->executeTemplateContent(["admin", "views", "index", sha1($indexContent)], $indexContent);
        $webObject->setContent($pageContent);
        $webObject->flushContent(null, null, "/");
    } else {
        // Files.

        require("file.inc.php");
    }

?>