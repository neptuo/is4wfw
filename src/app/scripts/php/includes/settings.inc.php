<?php

if (array_key_exists("IS4WFW_INSTANCE_SETTINGS_PATH", $_ENV)) {
    $userInstance = $_ENV["IS4WFW_INSTANCE_SETTINGS_PATH"];
} else {
    $instanceRoot = __DIR__ . "/../../../../";
    $userInstance = $instanceRoot . "user/instance.inc.php";
}

if (!file_exists($userInstance)) {
    header("Location: /setup.php");
    exit;
}

require_once($userInstance);

if (!ini_get('date.timezone')) {
    date_default_timezone_set('GMT');
}

// Use 'INSTANCE PATH constant with colon' just once to allow to override during docker build.
$instancePath = INSTANCE_PATH . "instance/";

// Paths and urls.
define("APP_PATH", INSTANCE_PATH . "app/");
define("APP_SCRIPTS_PATH", APP_PATH . "scripts/");
define("APP_SCRIPTS_PHP_PATH", APP_SCRIPTS_PATH . "php/");
define("APP_SCRIPTS_BUNDLES_PATH", APP_SCRIPTS_PATH . "bundles/");

define("CACHE_PATH", INSTANCE_PATH . "cache/");
define("CACHE_IMAGES_PATH", CACHE_PATH . "images/");
define("CACHE_OUTPUT_PATH", CACHE_PATH . "output/");
define("CACHE_PAGES_PATH", CACHE_PATH . "pages/");
define("CACHE_TEMPLATES_PATH", CACHE_PATH . "templates/");
define("CACHE_SYSTEMPROPERTY_PATH", CACHE_PATH . "systemproperty/");

define("LOGS_PATH", $instancePath . "logs/");

define("MODULES_PATH", $instancePath . "modules/");

define("USER_PATH", $instancePath . "user/");
define("USER_FILESYSTEM_PATH", USER_PATH . "filesystem/");
define("USER_FILESYSTEM_URL", INSTANCE_URL . "files/");
define("USER_BUNDLES_PATH", USER_PATH . "bundles/");
define("USER_PUBLIC_PATH", USER_PATH . "public/");
define("USER_PUBLIC_URL", INSTANCE_URL . "public/");

// Permissions and security.
define("WEB_R_READ", 101);
define("WEB_R_WRITE", 102);
define("WEB_R_DELETE", 103);
define("WEB_R_ADDCHILD", 104);
define("WEB_GROUP", "web");

define("IS_DEVELOPMENT_MODE", array_key_exists('IS4WFW_DEVELOPMENT', $_ENV) && $_ENV['IS4WFW_DEVELOPMENT'] == 'true');
define("IS_UPDATEABLE", !(array_key_exists('IS4WFW_IN_DOCKER', $_ENV) && $_ENV['IS4WFW_IN_DOCKER'] == 'true'));

?>
