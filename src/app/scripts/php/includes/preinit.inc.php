<?php

	$userPreInitPath = USER_PATH . "preinit.inc.php";
	if (file_exists($userPreInitPath)) {
		require_once($userPreInitPath);
	}

?>