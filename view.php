<?php

	require_once("scripts/php/includes/settings.inc.php");
	require_once("scripts/php/includes/database.inc.php");
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
