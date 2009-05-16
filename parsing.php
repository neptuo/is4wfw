<?php

	require_once("scripts/php/includes/settings.inc.php");
  require_once("scripts/php/libs/DefaultPhp.class.php");
  require_once("scripts/php/libs/DefaultWeb.class.php");
  
  session_start();
  
  $phpObject = new DefaultPhp();
  $webObject = new DefaultWeb();
  
  $Content = '<web:lang />';
  
  require_once("scripts/php/classes/CustomTagParser.class.php");
  
  $Parser = new CustomTagParser();
  $Parser->setContent($Content);
  $Parser->startParsing();
  echo $Parser->getResult();
  
  //$webObject->loadPageContent();
  //$webObject->flush();

?>
