<?php

    session_start();

    require_once("../user/instance.inc.php");
    require_once("../app/scripts/php/includes/settings.inc.php");
    require_once(APP_SCRIPTS_PHP_PATH . "includes/settings.inc.php");
    require_once(APP_SCRIPTS_PHP_PATH . "includes/version.inc.php");
    require_once(APP_SCRIPTS_PHP_PATH . "includes/extensions.inc.php");
    require_once(APP_SCRIPTS_PHP_PATH . "libs/DefaultPhp.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "libs/Web.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/TemplateParser.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/TemplateCache.class.php");

    // ini_set('pcre.backtrack_limit', 1000000000);

    $phpObject = new DefaultPhp();
    $webObject = new Web();
    
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

$cache = new TemplateCache();

if (array_key_exists("clear", $_GET)) {
    $cache->delete($keys);
    header("Location: " . $_SERVER['PHP_SELF'], true, 302);
}


$content = '
<php:register tagPrefix="ce2" classPath="php.libs.CustomEntity" />
<php:using prefix="test" class="php.libs.test.TestLibrary">
    <web:a pageId="~/index.view" test:a="Hello" test:b="5" />
    <web:a pageId="~/index.view" test:attributeAsBody="Hi" />
    <web:a pageId="~/index.view" test:a="Hello" test:b="5" test:attributeAsBody="Hi" test:if="f" test:if-is="f" />
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

    function parse($parser, $keys, $content, $count, $printOutput = false) {
        for ($i=0; $i < $count; $i++) { 
            $result = $parser->run($keys);
            if ($result == null) {
                $result = $parser->parse($content, $keys);
            }

            if ($printOutput && $i == 0) {
                echo $result();
            }
        }
    }

    measure(function() {
        global $content;
        $parser = new TemplateParser();
        parse($parser, [ "test", "parsing", "1" ], $content, 1, true);
    });

    echo '<hr />';
    echo $webObject->PageLog;

?>
