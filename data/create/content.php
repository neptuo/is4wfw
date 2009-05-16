<?php

  // Connection variables --------------------------------------------------------------------------
	require_once("../../scripts/php/libs/Database.class.php");
	require("../../scripts/php/includes/settings.inc.php");
	require("connect.inc.php");
  //------------------------------------------------------------------------------------------------

  // Jmeno tabulky ---------------------------------------------------------------------------------
  echo "<h4>TABLE: content</h4>";
  //------------------------------------------------------------------------------------------------

  // Smazani tabulky pro stranky -------------------------------------------------------------------
  $db->execute("DROP TABLE IF EXISTS `content`");
  //------------------------------------------------------------------------------------------------

  // Vytvoreni tabulky pro stranky -----------------------------------------------------------------
 	$db->execute("CREATE TABLE `content` (`page_id` INT NOT NULL, `language_id` INT NOT NULL, `tag_lib_start` TEXT NOT NULL, `tag_lib_end` TEXT NOT NULL, `head` TEXT, `content` TEXT, PRIMARY KEY ( `page_id`, `language_id` ));");
  //------------------------------------------------------------------------------------------------

  // Kontrola chyb ---------------------------------------------------------------------------------
  echo "Error code: ".mysql_errno().", error message: ".mysql_error().".<br />";
  //------------------------------------------------------------------------------------------------

  // Add default user ------------------------------------------------------------------------------
	/*$db->execute('INSERT INTO `content` (`page_id`, `language_id`, `tag_lib_start`, `tag_lib_end`, `head`, `content`) VALUES (1, 1, "", "", "", "Welcome\");');
	$db->execute('INSERT INTO `content` (`page_id`, `language_id`, `tag_lib_start`, `tag_lib_end`, `head`, `content`) VALUES (2, 1, "", "", "", "<web:content />");');
	$db->execute('INSERT INTO `content` (`page_id`, `language_id`, `tag_lib_start`, `tag_lib_end`, `head`, `content`) VALUES (3, 1, "", "", "", "<login:redirectWhenNotLogged pageId=\"4\" /><login:redirectWhenLogged pageId=\"6\" />");');
	$db->execute('INSERT INTO `content` (`page_id`, `language_id`, `tag_lib_start`, `tag_lib_end`, `head`, `content`) VALUES (4, 1, "", "", "", "<login:form group=\"admins\" pageId=\"6\" />");');
	$db->execute('INSERT INTO `content` (`page_id`, `language_id`, `tag_lib_start`, `tag_lib_end`, `head`, `content`) VALUES (5, 1, "", "", "", "<login:logout pageId=\"4\" /><web:content />");');
	$db->execute('INSERT INTO `content` (`page_id`, `language_id`, `tag_lib_start`, `tag_lib_end`, `head`, `content`) VALUES (6, 1, "<php:register tagPrefix=\"pg\" classPath=\"php.libs.Page\" />", "<php:unregister tagPrefix=\"pg\" />", "", "<pg:showList editable=\"true\" />");');
	$db->execute('INSERT INTO `content` (`page_id`, `language_id`, `tag_lib_start`, `tag_lib_end`, `head`, `content`) VALUES (7, 1, "<php:register tagPrefix=\"pg\" classPath=\"php.libs.Page\" />", "<php:unregister tagPrefix=\"pg\" />", "", "<pg:showFiles editable=\"true\" />");');
	$db->execute('INSERT INTO `content` (`page_id`, `language_id`, `tag_lib_start`, `tag_lib_end`, `head`, `content`) VALUES (8, 1, "<php:register tagPrefix=\"fl\" classPath=\"php.libs.File\" />", "<php:unregister tagPrefix=\"fl\" />", "", "<p><fl:showUploadForm /></p><p><fl:showNewDirectoryForm /></p><p><fl:showDirectory /></p>");');
	*/
  //------------------------------------------------------------------------------------------------

  // Kontrola chyb ---------------------------------------------------------------------------------
  echo "Error code: ".mysql_errno().", error message: ".mysql_error().".<hr />";
  //------------------------------------------------------------------------------------------------
  
  // Odpojeni od databaze --------------------------------------------------------------------------
  $db->close();
  //------------------------------------------------------------------------------------------------

?>