<?php

    require_once(APP_SCRIPTS_PHP_PATH . "classes/Module.class.php");

    (function(Web $web, PhpRuntime $php, Database $db, Login $login) {
        $module = Module::getById("71b53781-b881-42b3-b39d-14aa18d64d43");
        
        $web->addHook(WebHook::ProcessRequestBeforeCms, function() use ($web, $php, $db, $login, $module) { 
            $virtualUrl = "~/" . $web->getVirtualUrl();
            if (StringUtils::endsWith($virtualUrl, ".view")) {
                processAdministrationRequest($web, $php, $db->getDataAccess(), $login, $module, $virtualUrl);
                return true;
            }
        });
    })($webObject, $phpObject, $dbObject, $loginObject);

    function processAdministrationRequest(Web $web, PhpRuntime $php, DataAccess $db, Login $login, Module $module, string $virtualUrl) {
        if (IS_ADMIN_STOPPED) {
            echo file_get_contents(APP_PATH . "stopped.html");
            exit;
        }

        if (defined("IS_ADMIN_HTTPS") && constant("IS_ADMIN_HTTPS") === true) {
            $web->redirectToHttps();
        }

        $web->LanguageName = 'cs';
        
        $login->initLogin('web-admins');
        
        if ($login->isLogged()) {
            $sql = new SqlBuilder($db);
            $prop = $db->fetchScalar($sql->select("personal_property", ["value"], ["name" => "Admin.Language", "user_id" => $login->getUserId()]));
            if ($prop) {
                $web->LanguageName = $prop;
            }
        }
      
        $viewsPath = $module->getViewsPath();

        $php->lazy("controls", "php.libs.TemplateDirectory", ["path" => $viewsPath . "controls"]);
        $php->lazy("layouts", "php.libs.TemplateDirectory", ["path" => $viewsPath . "layouts"]);
        $php->lazy("views", "php.libs.TemplateDirectory", ["path" => $viewsPath . "pages"]);
        $php->lazy("floorball", "php.libs.TemplateDirectory", ["path" => $viewsPath . "/pages/floorball"]);
        $php->autolib("var")->setValue("virtualUrl", substr($virtualUrl, 2));
      
        $indexContent = file_get_contents($viewsPath . "index.view.php");
        $pageContent = $web->executeTemplateContent(["admin", "index" . sha1($indexContent)], $indexContent);
        $web->setContent($pageContent);
        $web->flushContent(null, null, "/");
    }

?>