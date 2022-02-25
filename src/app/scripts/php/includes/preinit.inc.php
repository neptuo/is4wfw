<?php

	require_once(APP_SCRIPTS_PHP_PATH . "classes/ErrorHandler.class.php");
	ErrorHandler::install();

	$userPreInitPath = USER_PATH . "preinit.inc.php";
	if (file_exists($userPreInitPath)) {
		require_once($userPreInitPath);
	}

?>