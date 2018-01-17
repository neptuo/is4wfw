<?php

	error_reporting(0);
	require_once("scripts/php/includes/instance.inc.php");
	require_once("scripts/php/includes/settings.inc.php");
	require_once("scripts/php/includes/version.inc.php");
	require_once("scripts/php/includes/extensions.inc.php");
  	require_once("scripts/php/libs/Database.class.php");
  	require_once("scripts/php/libs/DefaultWeb.class.php");
  	require_once("scripts/php/libs/DefaultPhp.class.php");

	if(array_key_exists('page-id', $_REQUEST) && array_key_exists('page-id', $_REQUEST)) {
		$dbObject = new Database();
		$webObject = new DefaultWeb();
		$phpObject = new DefaultPhp();
		
		echo $webObject->composeUrl($_REQUEST['page-id'], $_REQUEST['lang-id'], true);
		exit;
	}

?>
