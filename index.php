<?php

if (!file_exists("scripts/php/includes/instance.inc.php")) {
    header("Location: /setup.php");
    exit;
}

require_once("scripts/php/includes/instance.inc.php");
require_once("scripts/php/includes/settings.inc.php");

if (IS_STOPPED) {
    echo file_get_contents("updating.html");
    exit;
}

require_once("scripts/php/includes/version.inc.php");
require_once("scripts/php/includes/extensions.inc.php");
require_once("scripts/php/libs/DefaultPhp.class.php");
require_once("scripts/php/libs/DefaultWeb.class.php");

session_start();

$phpObject = new DefaultPhp();
$webObject = new DefaultWeb();

require_once("scripts/php/includes/postinit.inc.php");
require_once("scripts/php/includes/autoupdate.inc.php");

$webObject->processRequestNG();

?>