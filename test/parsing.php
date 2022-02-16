<?php

    session_start();

    require_once("../user/instance.inc.php");
    require_once("../app/scripts/php/includes/settings.inc.php");
    require_once(APP_SCRIPTS_PHP_PATH . "includes/settings.inc.php");
    require_once(APP_SCRIPTS_PHP_PATH . "includes/version.inc.php");
    require_once(APP_SCRIPTS_PHP_PATH . "includes/extensions.inc.php");
    require_once(APP_SCRIPTS_PHP_PATH . "libs/PhpRuntime.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "libs/Web.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/TemplateParser.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/TemplateCache.class.php");

    // ini_set('pcre.backtrack_limit', 1000000000);

    $phpObject = new PhpRuntime();
    $webObject = new Web();
    
    $phpObject->register("cetype", "php.libs.CustomEntity");
    $phpObject->register("template", "php.libs.Template");
    $phpObject->register("ui", "php.libs.Ui");
    $phpObject->register("var", "php.libs.Variable");
    // $phpObject->register("test", "php.libs.test.TestLibrary");

//     $Content = '<hr />
// <admin:field label="Entity Name" label-class="w90" style="background: red">
//     <input type="text" name="entity-name" />
// </admin:field>
// <hr />';

$keys = ["test", "parsing", "1"];
$cache = new TemplateCache();

if (array_key_exists("clear", $_GET)) {
    $cache->delete($keys);
    header("Location: " . $_SERVER['PHP_SELF'], true, 302);
}


$content = '
<var:declare name="x" value="x" />
<utils:concat output="var:x" value1="A" value2="B" separator="-" />
utils:x <web:out text="utils:x" />
<br />
var:x <web:out text="var:x" />
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

    $cache->delete($keys);
    measure(function() use ($keys) {
        global $content;
        global $phpObject;
        $parser = new TemplateParser($phpObject->getCurrentRegistrations(), []);
        parse($parser, $keys, $content, 1, true);
    });

    echo '<hr />';
    echo $webObject->PageLog;

?>
