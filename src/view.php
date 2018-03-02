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
	require_once("scripts/php/classes/FullTagParser.class.php");
	require_once("scripts/php/classes/ViewHelper.class.php");
  
	session_start();
  
	$phpObject = new DefaultPhp();
	$webObject = new DefaultWeb();
  
	$phpObject->register('v', 'php.libs.View');
	$phpObject->register('m', 'php.libs.Menu');
  
	require_once("scripts/php/includes/postinitview.inc.php");
  
	$vObject->processView($_REQUEST['WEB_PAGE_PATH']);


?>
