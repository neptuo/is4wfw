<?php

  // Connection variables --------------------------------------------------------------------------
	require_once("../../scripts/php/libs/Database.class.php");
	require("../../scripts/php/includes/settings.inc.php");
	require("connect.inc.php");
  //------------------------------------------------------------------------------------------------

  // Jmeno tabulky ---------------------------------------------------------------------------------
  echo "<h4>TABLE: page_file</h4>";
  //------------------------------------------------------------------------------------------------

  // Smazani tabulky pro stranky -------------------------------------------------------------------
  $db->execute("DROP TABLE IF EXISTS `page_file`");
  //------------------------------------------------------------------------------------------------

  // Vytvoreni tabulky pro stranky -----------------------------------------------------------------
 	$db->execute("CREATE TABLE `page_file` (`id` INT NOT NULL AUTO_INCREMENT, `name` TINYTEXT NOT NULL, `content` TEXT NOT NULL, `type` INT NOT NULL, PRIMARY KEY ( `id` ));");
  //------------------------------------------------------------------------------------------------

  // Kontrola chyb ---------------------------------------------------------------------------------
  //echo "Error code: ".mysql_errno().", error message: ".mysql_error().".<br />";
  //------------------------------------------------------------------------------------------------

  // Add default user ------------------------------------------------------------------------------
  //$db->execute("INSERT INTO `page_file` VALUES (1, 'cms', '/* CSS Document */\r\n\r\nbody {\r\n  margin: 0 auto;\r\n}\r\n\r\nform {\r\n  margin: 0;\r\n  display: inline;\r\n}\r\n\r\ntextarea {\r\n  width: 100%;\r\n}\r\n\r\ninput[type=image] {\r\n	margin-bottom: -4px;\r\n}\r\n\r\n.closed .frame-body {\r\n	background: white;\r\n	display: none;\r\n}\r\n\r\n.pages-list td {\r\n  padding: 2px 10px;\r\n}\r\n\r\ntable.pages-table {\r\n  border-collapse: collapse;\r\n}\r\n/*\r\n.inn-1 {\r\n  background: #eeeeee;\r\n}\r\n\r\n.inn-2 {\r\n  background: #dddddd;\r\n}\r\n\r\n.inn-3 {\r\n  background: #cccccc;\r\n}\r\n\r\n.inn-4 {\r\n  background: #bbbbbb\r\n}\r\n*/\r\n.page-item {\r\n  clear: both;\r\n}\r\n\r\n.page-edit, .page-edit form {\r\n  text-align: left;\r\n}\r\n\r\n.float-right {\r\n  width: 400px;\r\n  float: right;\r\n}\r\n\r\n.clear {\r\n  clear: both;\r\n}\r\n\r\n.page-list span {\r\n  padding: 2px 5px;\r\n}\r\n\r\n.page-language-version {\r\n  display: inline;\r\n}\r\n\r\n.page-list li {\r\n  padding: 4px 0 0 0;\r\n}\r\n\r\n.page-id-col {\r\n  color: #888888;\r\n}\r\n\r\n.page-name {\r\n  font-weight: bold;\r\n}\r\n\r\n.page-file-list {\r\n  width: 100%;\r\n  border-top: 1px solid #cccccc;\r\n  border-collapse: collapse;\r\n}\r\n\r\n.file-name {\r\n  width: 130px;\r\n  font-weight: bold;\r\n}\r\n\r\n.file-content {\r\n  color: #777777;\r\n}\r\n\r\n.file-content-in {\r\n  width: 560px;\r\n  overflow: hidden;\r\n}\r\n\r\n.file-content-in .foo {\r\n  width: 1000px;\r\n}\r\n\r\n.file-tr td {\r\n  overflow: hidden;\r\n  padding: 2px 5px;\r\n  border-bottom: 1px solid #cccccc;\r\n}\r\n\r\n.frame-cover {\r\n  margin-top: 15px;\r\n}\r\n\r\ndiv.login {\r\n  width: 256px;\r\n  margin: 100px 0 0 100px;\r\n}\r\n  	\r\ndiv.login-head {\r\n  width: 100%;\r\n  height: 18px;\r\n  background: url(''/file/32-login-head'') no-repeat;\r\n}\r\n  	\r\ndiv.login-in {\r\n  padding-bottom: 12px;\r\n  background: url(''/file/30-login-body'') no-repeat left bottom;\r\n}\r\n\r\ndiv.login-form {\r\n  margin: 0 10px;\r\n  padding: 0 10px;\r\n}\r\n\r\ndiv.login-form form {\r\n  margin: 0\r\n}\r\n\r\ndiv.login-form p.login-head, p.login-message {\r\n  margin-top: 0;\r\n}\r\n\r\n.dir-list .dir-name input[type=submit] {\r\n  width: 100%;\r\n  text-align: left;\r\n  font-weight: bold;\r\n  font-family: Times;\r\n  font-size: 16px;\r\n  padding: 0;\r\n  background: white;\r\n  border: none;\r\n}\r\n\r\n.frame-cover {\r\n	border: 1px solid #04601C;\r\n}\r\n\r\n.frame-head {\r\n	background: #04601C;\r\n}\r\n\r\n.frame-head .frame-label {\r\n	color: white;\r\n	font-weight: bold;\r\n	float: left;\r\n	padding: 1px 0 1px 5px;\r\n}\r\n\r\n.frame-head .frame-close {\r\n	float: right;\r\n}\r\n\r\n.frame-body {\r\n	padding: 5px;\r\n	background: white;\r\n}\r\n\r\n.frames-used .frame-body {\r\n	display: none;\r\n}\r\n\r\nth.file-head-th {\r\n	background: #cccccc;\r\n}\r\n\r\n.dir-list {\r\n	width: 100%;\r\n	border-collapse: collapse;\r\n}\r\n\r\n.dir-list td, .dir-list th {\r\n	text-align: left;\r\n	padding: 2px 4px;\r\n}\r\n\r\n.dir-list .file-edit, .dir-list .dir-edit {\r\n	width: 60px;\r\n}\r\n\r\n.dir-list .file-icon, .dir-list .file-id, .dir-list .file-type, .dir-list .dir-icon, .dir-list .dir-id, .dir-list .dir-type {\r\n	width: 20px;\r\n}\r\n\r\n.dir-list .file-name, .dir-list .dir-name {\r\n	width: 300px;\r\n}\r\n\r\n.dir-list tr.even, .dir-list tr.even .dir-name input {\r\n	background: #eeeeee;\r\n}\r\n\r\n.frame-body .error {\r\n	margin: 2px 0;\r\n	padding: 0 0 1px 18px;\r\n	color: white;\r\n	background: url(''/images/error.png'') #bd2828 no-repeat 1px 3px;\r\n}', 1);");
  //------------------------------------------------------------------------------------------------

  // Kontrola chyb ---------------------------------------------------------------------------------
  echo "Error code: ".mysql_errno().", error message: ".mysql_error().".<hr />";
  //------------------------------------------------------------------------------------------------
  
  // Odpojeni od databaze --------------------------------------------------------------------------
  $db->close();
  //------------------------------------------------------------------------------------------------

?>