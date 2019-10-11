<?php

    require_once("../user/instance.inc.php");
    require_once("../app/scripts/php/includes/settings.inc.php");
    require_once(APP_SCRIPTS_PHP_PATH . "includes/settings.inc.php");
    require_once(APP_SCRIPTS_PHP_PATH . "includes/version.inc.php");
    require_once(APP_SCRIPTS_PHP_PATH . "includes/extensions.inc.php");
    require_once(APP_SCRIPTS_PHP_PATH . "libs/DefaultPhp.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "libs/DefaultWeb.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/CustomTagParser.class.php");

    session_start();

    $phpObject = new DefaultPhp();
    $webObject = new DefaultWeb();

    $Content = '<hr />
<admin:field label="Entity Name" label-class="w90" style="background: red">
    <input type="text" name="entity-name" />
</admin:field>
<hr />';

    $parser = new FullTagParser();
    $parser->setContent($Content);
    $parser->startParsing();
    echo $parser->getResult();

    //$webObject->loadPageContent();
    //$webObject->flush();

    // echo '<br /><br />';
    // $pageId = "~/webapp/index";

    // if(is_numeric($pageId)) {
    //     echo $webObject->composeUrl($pageId);
    // } else {
    //     echo $pageId;
    // }

?>
