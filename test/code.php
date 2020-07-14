<?php

    require_once("../user/instance.inc.php");
    require_once("../app/scripts/php/includes/settings.inc.php");
    require_once(APP_SCRIPTS_PHP_PATH . "includes/settings.inc.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/CodeWriter.class.php");

    $code = new CodeWriter();
    $code->addClass("Calculator", "Model", ["ArrayAccess", "Iterator"]);
    
    $code->addMethod("add", "public", [["int", "a"], ["int", "b"], ["int", "c", "0"]]);
    $code->addTry();
    $code->addLine("return a + b + c;");
    $code->addCatch(["Exception", "e"]);
    $code->addLine("return 0;");
    $code->addFinally();
    $code->addLine("echo 'test';");
    $code->closeBlock();
    $code->closeBlock();
    
    $code->addMethod("substract", "public", ["a", "b"]);
    $code->addLine("return a - b;");
    $code->closeBlock();

    $code->closeBlock();

    echo "<pre>" . $code . "</pre>";

?>