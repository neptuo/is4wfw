<?php

	require_once("scripts/php/includes/settings.inc.php");
  require_once("scripts/php/includes/database.inc.php");
  require_once("scripts/php/includes/version.inc.php");
  require_once("scripts/php/includes/extensions.inc.php");
  require_once("scripts/php/libs/DefaultPhp.class.php");
  require_once("scripts/php/libs/DefaultWeb.class.php");
  
  session_start();
  
  $phpObject = new DefaultPhp();
  $webObject = new DefaultWeb();
  
  $Content = '<web:lang />';
  
  require_once("scripts/php/classes/CustomTagParser.class.php");
  
  $parser = new CustomTagParser();
  $parser->setContent($Content);
  $parser->startParsing();
  echo $parser->getResult();
  
  //$webObject->loadPageContent();
  //$webObject->flush();
  
  echo '<br /><br />';
  $pageId = "~/webapp/index";
  
  if(is_numeric($pageId)) {
  	echo $webObject->composeUrl($pageId);
  } else {
  	echo $pageId;
  }

?>
