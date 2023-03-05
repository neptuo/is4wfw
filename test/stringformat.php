<?php

require_once("../app/scripts/php/includes/settings.inc.php");
require_once(APP_SCRIPTS_PHP_PATH . "classes/utils/StringUtils.class.php");

echo StringUtils::format("{greet}, {name}!", function($match) {
    if ($match == "name") {
        return "John";
    } else if ($match == "greet") {
        return "Hi";
    }

    return "XXX";
});

?>