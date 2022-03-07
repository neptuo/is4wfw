<?php

	/**
	 *
	 *	Script included after app view init
	 *
	 */
	$webObject->LanguageName = 'cs';
	
	$login = new Login();
	$login->initLogin('web-admins');
	
	if ($login->isLogged()) {
		$prop = $dbObject->fetchSingle('select `value` from `personal_property` where `name` = "Admin.Language" and `user_id` = '.$login->getUserId().';');
		if($prop != array()) {
			$webObject->LanguageName = $prop['value'];
		}
	}

?>