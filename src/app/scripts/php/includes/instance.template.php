<?php

// Database connection.
define("WEB_DB_HOSTNAME", "{database-hostname}");
define("WEB_DB_USER", "{database-username}");
define("WEB_DB_PASSWORD", "{database-password}");
define("WEB_DB_DATABASE", "{database-database}");

// Instance paths and urls.
define("DOCUMENT_ROOT", $_SERVER["DOCUMENT_ROOT"]);
define("INSTANCE_PATH", DOCUMENT_ROOT . "{filesystem-path}" . "/");
define("INSTANCE_URL", "/");

// Other instance config.
define("WEB_USE_URLCACHE", true);

// Stopped instance.
define("IS_ADMIN_STOPPED", false);
define("IS_WEB_STOPPED", false);

define("IS_ADMIN_HTTPS", {admin-https});

?>