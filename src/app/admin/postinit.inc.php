<?php

    require_once(APP_SCRIPTS_PHP_PATH . "classes/Module.class.php");

    (function(Web $web, $php) {
        $module = Module::getById("71b53781-b881-42b3-b39d-14aa18d64d43");
        
        $web->addHook(WebHook::ProcessRequestBeforeCms, function($params) use ($web, $php, $module) { 
            $virtualUrl = "~/" . $web->getVirtualUrl();
            if (StringUtils::endsWith($virtualUrl, ".view")) {
                processAdministrationRequest($web, $php, $module, $virtualUrl);
                return true;
            }
        });
    })($webObject, $phpObject);

    function processAdministrationRequest(Web $web, PhpRuntime $php, Module $module, string $virtualUrl) {
        if (IS_ADMIN_STOPPED) {
            echo file_get_contents(APP_PATH . "stopped.html");
            exit;
        }

        if (defined("IS_ADMIN_HTTPS") && constant("IS_ADMIN_HTTPS") === true) {
            $web->redirectToHttps();
        }
      
        // TODO: Rename and move to admin.
        // require_once(APP_SCRIPTS_PHP_PATH . "includes/postinitview.inc.php");

        $viewsPath = $module->getViewsPath();

        $php->lazy("controls", "php.libs.TemplateDirectory", ["path" => $viewsPath . "controls"]);
        $php->lazy("layouts", "php.libs.TemplateDirectory", ["path" => $viewsPath . "layouts"]);
        $php->lazy("views", "php.libs.TemplateDirectory", ["path" => $viewsPath . "pages"]);
        $php->lazy("floorball", "php.libs.TemplateDirectory", ["path" => $viewsPath . "/pages/floorball"]);
        $php->autolib("var")->setValue("virtualUrl", substr($virtualUrl, 2));
      
        $indexContent = file_get_contents($viewsPath . "index.view.php");
        $pageContent = $web->executeTemplateContent(["admin", "views", "index", sha1($indexContent)], $indexContent);
        $web->setContent($pageContent);
        $web->flushContent(null, null, "/");
    }

?>