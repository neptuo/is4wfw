<?php

    session_start();

    require_once("../user/instance.inc.php");
    require_once("../app/scripts/php/includes/settings.inc.php");
    require_once(APP_SCRIPTS_PHP_PATH . "includes/settings.inc.php");
    require_once(APP_SCRIPTS_PHP_PATH . "includes/version.inc.php");
    require_once(APP_SCRIPTS_PHP_PATH . "includes/extensions.inc.php");
    require_once(APP_SCRIPTS_PHP_PATH . "libs/DefaultPhp.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "libs/DefaultWeb.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/TemplateParser.class.php");

    // ini_set('pcre.backtrack_limit', 1000000000);

    $phpObject = new DefaultPhp();
    $webObject = new DefaultWeb();
    
    $phpObject->register("cetype", "php.libs.CustomEntity");
    $phpObject->register("template", "php.libs.Template");
    $phpObject->register("ui", "php.libs.Ui");
    $phpObject->register("var", "php.libs.Variable");
    $phpObject->register("view", "php.libs.View");

//     $Content = '<hr />
// <admin:field label="Entity Name" label-class="w90" style="background: red">
//     <input type="text" name="entity-name" />
// </admin:field>
// <hr />';
$Content = '
    <var:declare name="articleId" value="15" />
    <web:condition when="var:articleId" is="15">
        <template:article-detail id="var:articleId"  />
    </web:condition>
';

    function measure($func) {
        $startTime = microtime(true);
        $func();
        $endTime = microtime(true);

        echo '<hr />';
        echo 'Duration: ' . ($endTime - $startTime) . 'ms';
    }

    function parse($parser, $content, $count, $printOutput = false) {
        for ($i=0; $i < $count; $i++) { 
            $result = $parser->parse($content);

            if ($printOutput && $i == 0) {
                echo $result->evaluate();
            }
        }
    }

    measure(function() {
        global $Content;
        $parser = new TemplateParser();
        parse($parser, $Content, 1, true);
    });

    echo '<hr />';
    echo $webObject->PageLog;

?>
