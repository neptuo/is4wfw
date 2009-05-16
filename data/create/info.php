<?php

  // Connection variables --------------------------------------------------------------------------
	require_once("../../scripts/php/libs/Database.class.php");
	require("../../scripts/php/includes/settings.inc.php");
	require("connect.inc.php");
  //------------------------------------------------------------------------------------------------

  // Jmeno tabulky ---------------------------------------------------------------------------------
  echo "<h4>TABLE: info</h4>";
  //------------------------------------------------------------------------------------------------

  // Smazani tabulky pro stranky -------------------------------------------------------------------
  $db->execute("DROP TABLE IF EXISTS `info`");
  //------------------------------------------------------------------------------------------------

  // Vytvoreni tabulky pro stranky -----------------------------------------------------------------
 	$db->execute("CREATE TABLE `info` (`page_id` INT NOT NULL, `language_id` INT NOT NULL, `name` TINYTEXT NOT NULL, `href` TINYTEXT NOT NULL, `page_pos` INT NOT NULL, `in_menu` INT NOT NULL, `is_visible` INT NOT NULL, `timestamp` INT NOT NULL, PRIMARY KEY ( `page_id`, `language_id` ));");
  //------------------------------------------------------------------------------------------------

  // Kontrola chyb ---------------------------------------------------------------------------------
  echo "Error code: ".mysql_errno().", error message: ".mysql_error().".<br />";
  //------------------------------------------------------------------------------------------------

  // Add default user ------------------------------------------------------------------------------
	/*$db->execute('INSERT INTO `info` (`page_id`, `language_id`, `name`, `href`, `in_menu`, `is_visible`, `timestamp`) VALUES (1, 1, "Welcome", "", 0, 1, '.time().');');
	$db->execute('INSERT INTO `info` (`page_id`, `language_id`, `name`, `href`, `in_menu`, `is_visible`, `timestamp`) VALUES (2, 1, "RS", "rs", 0, 1, '.time().');');
	$db->execute('INSERT INTO `info` (`page_id`, `language_id`, `name`, `href`, `in_menu`, `is_visible`, `timestamp`) VALUES (3, 1, "Home", "", 0, 1, '.time().');');
	$db->execute('INSERT INTO `info` (`page_id`, `language_id`, `name`, `href`, `in_menu`, `is_visible`, `timestamp`) VALUES (4, 1, "Login", "login", 0, 1, '.time().');');
	$db->execute('INSERT INTO `info` (`page_id`, `language_id`, `name`, `href`, `in_menu`, `is_visible`, `timestamp`) VALUES (5, 1, "in", "in", 0, 1, '.time().');');
	$db->execute('INSERT INTO `info` (`page_id`, `language_id`, `name`, `href`, `in_menu`, `is_visible`, `timestamp`) VALUES (6, 1, "Spravce stranek", "spravce-stranek", 1, 1, '.time().');');
	$db->execute('INSERT INTO `info` (`page_id`, `language_id`, `name`, `href`, `in_menu`, `is_visible`, `timestamp`) VALUES (7, 1, "Spravce css a js", "spravce-css-a-js", 1, 1, '.time().');');
	$db->execute('INSERT INTO `info` (`page_id`, `language_id`, `name`, `href`, `in_menu`, `is_visible`, `timestamp`) VALUES (8, 1, "Spravce souboru", "spravce-souboru", 1, 1, '.time().');');
	*/
  //------------------------------------------------------------------------------------------------

  // Kontrola chyb ---------------------------------------------------------------------------------
  //echo "Error code: ".mysql_errno().", error message: ".mysql_error().".<hr />";
  //------------------------------------------------------------------------------------------------
  
  // Odpojeni od databaze --------------------------------------------------------------------------
  $db->close();
  //------------------------------------------------------------------------------------------------

?>