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
    require_once(APP_SCRIPTS_PHP_PATH . "classes/TemplateCache.class.php");

    // ini_set('pcre.backtrack_limit', 1000000000);

    $phpObject = new DefaultPhp();
    $webObject = new DefaultWeb();
    
    $phpObject->register("cetype", "php.libs.CustomEntity");
    $phpObject->register("template", "php.libs.Template");
    $phpObject->register("ui", "php.libs.Ui");
    $phpObject->register("var", "php.libs.Variable");
    $phpObject->register("view", "php.libs.View");
    // $phpObject->register("test", "php.libs.test.TestLibrary");

//     $Content = '<hr />
// <admin:field label="Entity Name" label-class="w90" style="background: red">
//     <input type="text" name="entity-name" />
// </admin:field>
// <hr />';
$keys = ["test", "parsing"];

$cache = new TemplateCache();

if (array_key_exists("clear", $_GET)) {
    $cache->delete($keys);
    header("Location: " . $_SERVER['PHP_SELF'], true, 302);
}


$content = '
<php:register tagPrefix="ce2" classPath="php.libs.CustomEntity" />
<php:using prefix="test" class="php.libs.test.TestLibrary">
    <web:a pageId="~/index.view" test:a="Hello" test:b="5" />
    <web:a pageId="~/index.view" test:c="Hi" />
    <web:a pageId="~/index.view" test:a="Hello" test:b="5" test:c="Hi" test:if="f" test:if-is="f" />
    <web:a pageId="~/index.view" test:cool="Baf" />
    <web:a pageId="~/index.view" test:optionalBody="test" />
</php:using>
<php:unregister tagPrefix="ce2" />
';

    function measure($func) {
        $startTime = microtime(true);
        $func();
        $endTime = microtime(true);

        echo '<hr />';
        echo 'Duration: ' . ($endTime - $startTime) . 'ms';
        echo '<hr />';
        echo '<a href="?clear">Clear template cache</a>';
    }

    function parse($parser, $cache, $content, $count, $printOutput = false) {
        global $keys;

        for ($i=0; $i < $count; $i++) { 
            if ($cache->exists($keys)) {
                $template = $cache->read($keys);
            }

            $result = $parser->parse($content);

            if ($printOutput && $i == 0) {
                echo $result->evaluate();
            }
        }
    }

    measure(function() {
        global $content;
        $parser = new TemplateParser();
        $cache = new TemplateCache();
        parse($parser, $cache, $content, 1, true);
    });

    echo '<hr />';
    echo $webObject->PageLog;

?>
