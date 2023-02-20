<?php

	error_reporting(0);
	require_once("scripts/php/includes/settings.inc.php");
	require_once(APP_SCRIPTS_PHP_PATH . "includes/version.inc.php");
	require_once(APP_SCRIPTS_PHP_PATH . "includes/extensions.inc.php");
  	require_once(APP_SCRIPTS_PHP_PATH . "libs/Database.class.php");
  	require_once(APP_SCRIPTS_PHP_PATH . "libs/Web.class.php");
  	require_once(APP_SCRIPTS_PHP_PATH . "libs/PhpRuntime.class.php");

	if (array_key_exists('page-id', $_REQUEST) && array_key_exists('page-id', $_REQUEST)) {
		$dbObject = new Database();
		$webObject = new Web();
		$phpObject = new PhpRuntime();
		
		echo $webObject->composeUrl($_REQUEST['page-id'], $_REQUEST['lang-id'], true);
		exit;
	}

?>
