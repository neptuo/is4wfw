<?php

// Database connection.
define("WEB_DB_HOSTNAME", $_ENV['IS4WFW_DB_HOSTNAME']);
define("WEB_DB_USER", $_ENV['IS4WFW_DB_USER']);
define("WEB_DB_PASSWORD", $_ENV['IS4WFW_DB_PASSWORD']);
define("WEB_DB_DATABASE", $_ENV['IS4WFW_DB_DATABASE']);

// Instance paths and urls.
define("DOCUMENT_ROOT", "/var/www/html");
define("INSTANCE_PATH", DOCUMENT_ROOT . "" . "/");
define("INSTANCE_URL", "/");

// Other instance config.
define("WEB_USE_URLCACHE", true);

// Stopped instance.
define("IS_ADMIN_STOPPED", false);
define("IS_WEB_STOPPED", false);

define("IS_ADMIN_HTTPS", false);

?>