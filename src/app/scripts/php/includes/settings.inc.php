<?php

    // Paths and urls.
    define("APP_PATH", INSTANCE_PATH . "app/");
    define("APP_ADMIN_PATH", APP_PATH . "admin/");
    define("APP_SCRIPTS_PATH", APP_PATH . "scripts/");
    define("APP_SCRIPTS_PHP_PATH", APP_SCRIPTS_PATH . "php/");
    
    define("CACHE_PATH", INSTANCE_PATH . "cache/");
    define("CACHE_IMAGES_PATH", CACHE_PATH . "images/");
    define("CACHE_PAGES_PATH", CACHE_PATH . "pages/");
    define("CACHE_TEMPLATES_PATH", CACHE_PATH . "templates/");
    define("CACHE_SYSTEMPROPERTY_PATH", CACHE_PATH . "systemproperty/");

    define("USER_PATH", INSTANCE_PATH . "user/");
    define("USER_FILESYSTEM_PATH", USER_PATH . "filesystem/");
    define("USER_FILESYSTEM_URL", INSTANCE_URL . "files/");
    define("USER_PUBLIC_PATH", USER_PATH . "public/");
    define("USER_PUBLIC_URL", INSTANCE_URL . "public/");

    // Permissions and security.
    define("WEB_R_READ", 101);
    define("WEB_R_WRITE", 102);
    define("WEB_R_DELETE", 103);
    define("WEB_R_ADDCHILD", 104);
    define("WEB_GROUP", "web");

?>