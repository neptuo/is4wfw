<?php

	require_once("scripts/php/includes/instance.inc.php");
	require_once("scripts/php/includes/settings.inc.php");
	require_once("scripts/php/classes/RequestStorage.class.php");
	require_once("scripts/php/classes/SessionStorage.class.php");
	require_once("scripts/php/classes/QueryStorage.class.php");
	require_once("scripts/php/libs/Database.class.php");
	require_once("scripts/php/classes/UrlResolver.class.php");
	
	global $requestStorage;
	$requestStorage = new RequestStorage();
	global $sessionStorage;
	$sessionStorage = new SessionStorage();
	global $queryStorage;
	$queryStorage = new QueryStorage();
	
	error_reporting(0);
	session_start();

	if(array_key_exists('selected-project', $_SESSION)) {
		$projectId = $_SESSION['selected-project'];
		
		$dbObject = new Database();
	  
		$allHeaders = getallheaders();
		$userBrowser = $allHeaders['User-Agent'];
		$browser = 'for_all';
		if(preg_match("(Firefox)", $userBrowser)) {
			$browser = 'for_firefox';
		} elseif(preg_match("(MSIE 8.0)", $userBrowser)) {
			$browser = 'for_msie8';
		} elseif(preg_match("(MSIE 7.0)", $userBrowser)) {
			$browser = 'for_msie7';
		} elseif(preg_match("(MSIE 6.0)", $userBrowser)) {
			$browser = 'for_msie6';
		} elseif(preg_match("(Opera)", $userBrowser)) {
			$browser = 'for_opera';
		} elseif(preg_match("(Safari)", $userBrowser)) {
			$browser = 'for_safari';
		}
	  
		$styles = $dbObject->fetchAll('SELECT `page_file`.`content` FROM `wp_wysiwyg_file` LEFT JOIN `page_file` ON `wp_wysiwyg_file`.`tf_id` = `page_file`.`id` WHERE `wp_wysiwyg_file`.`wp` = '.$projectId.' AND `page_file`.`for_all` = 1 OR `page_file`.`'.$browser.'` = 1 ORDER BY `page_file`.`id`;');
		
		$return = '';
		foreach($styles as $css) {
			$return .= $css['content'];
		}
		
		$fileType = "text/css";
		$return = str_replace("~/", UrlResolver::combinePath(WEB_ROOT, UrlResolver::combinePath(UrlResolver::parseScriptRoot($_SERVER['SCRIPT_NAME'], 'file.php'), WEB_ROOT)), $return);
		header('Content-Type: '.$fileType);
		header('Content-Length: '.strlen($return));
		header('Content-Transfer-Encoding: binary');
		echo $return;
		exit;
	}

?>
