<?php

	require_once('../scripts/php/includes/settings.inc.php');
	require_once('../scripts/php/includes/database.inc.php');
	require_once('../scripts/php/classes/RequestStorage.class.php');
	require_once('../scripts/php/classes/SessionStorage.class.php');
	require_once('../scripts/php/classes/QueryStorage.class.php');
	require_once('../scripts/php/classes/dataaccess/DataAccess.class.php');
	require_once('../scripts/php/classes/dataaccess/UserDao.class.php');
	
	global $requestStorage;
	$requestStorage = new RequestStorage();
	global $sessionStorage;
	$sessionStorage = new SessionStorage();
	global $queryStorage;
	$queryStorage = new QueryStorage();

	$dataAccess = new DataAccess();
	$dataAccess->connect(WEB_DB_HOSTNAME, WEB_DB_USER, WEB_DB_PASSWORD, WEB_DB_DATABASE);

	$dataAccess->fetchSingle('select * from `file` order by `id`;', true, true, true);
	if($dataAccess->getErrorCode() != 0) {
		echo $dataAccess->getErrorMessage();
	}
	
	echo '<hr />';
	
	$userDao = new UserDao();
	$userDao->setDataAccess($dataAccess);
	
	print_r($userDao->getEnabled());

	$dataAccess->disconnect();
	
?>