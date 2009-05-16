<?php

  // Connection variables --------------------------------------------------------------------------
	require_once("../../scripts/php/libs/Database.class.php");
	require("../../scripts/php/includes/settings.inc.php");
	require("connect.inc.php");
  //------------------------------------------------------------------------------------------------

  // Jmeno tabulky ---------------------------------------------------------------------------------
  echo "<h4>TABLE: guestbook</h4>";
  //------------------------------------------------------------------------------------------------

  // Smazani tabulky pro stranky -------------------------------------------------------------------
  $db->execute("DROP TABLE IF EXISTS `guestbook`");
  //------------------------------------------------------------------------------------------------

  // Vytvoreni tabulky pro stranky -----------------------------------------------------------------
 	$db->execute("CREATE TABLE `guestbook` (`id` INT NOT NULL AUTO_INCREMENT, `parent_id` INT NOT NULL, `name` TINYTEXT NOT NULL, `content` TEXT NOT NULL, `timestamp` INT NOT NULL, `guestbook_id` INT NOT NULL, PRIMARY KEY ( `id` ));");
  //------------------------------------------------------------------------------------------------

  // Kontrola chyb ---------------------------------------------------------------------------------
  //echo "Error code: ".mysql_errno().", error message: ".mysql_error().".<br />";
  //------------------------------------------------------------------------------------------------

  // Add default user ------------------------------------------------------------------------------
	//$db->execute("INSERT INTO `pages` (`id`, `name`, `head`, `content`, `visibility`) VALUES (0, \"Homepage\", \"\", \"\", 1);");
  //------------------------------------------------------------------------------------------------

  // Kontrola chyb ---------------------------------------------------------------------------------
  echo "Error code: ".mysql_errno().", error message: ".mysql_error().".<hr />";
  //------------------------------------------------------------------------------------------------
  
  // Odpojeni od databaze --------------------------------------------------------------------------
  $db->close();
  //------------------------------------------------------------------------------------------------

?>