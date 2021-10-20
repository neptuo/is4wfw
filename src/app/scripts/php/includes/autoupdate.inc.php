<?php

	require_once(APP_SCRIPTS_PHP_PATH . "classes/utils/FileUtils.class.php");
	require_once(APP_SCRIPTS_PHP_PATH . "classes/dataaccess/DbMigrator.class.php");

	DbMigrator::run(FileUtils::combinePath(APP_PATH, "data/autoupdate.xml"), "db_version");

?>