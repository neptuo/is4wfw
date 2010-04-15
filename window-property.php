<?php

	require_once("scripts/php/includes/settings.inc.php");
  require_once("scripts/php/includes/database.inc.php");
	require_once("scripts/php/includes/version.inc.php");
	require_once("scripts/php/includes/extensions.inc.php");
  require_once("scripts/php/libs/Database.class.php");
	$dbObject = new Database();
	

	if(array_key_exists('user-login', $_REQUEST)) {
		$userId = $dbObject->fetchAll('SELECT `uid` FROM `user` WHERE `login` = "'.$_REQUEST['user-login'].'";');
		if(count($userId) == 1) {
			$_REQUEST['user-id'] = $userId[0]['uid'];
		}
	}

	if(array_key_exists('frame-id', $_REQUEST) && array_key_exists('user-id', $_REQUEST)) {
		if($_REQUEST['request'] == "set") {
			
	  	$sql = "";
	  	$frameId = $_REQUEST['frame-id'];
		  $userId = $_REQUEST['user-id'];
	  
	  	if(array_key_exists('frame-width', $_REQUEST)) {
			  $width = $_REQUEST['frame-width'];
		  	if($width != '') {
					if($sql != "") {
						$sql .= ', ';
					}
					$sql .= '`width` = '.$width;
				} else {
					$width = 0;
				}
			} else {
				$width = 500;
			}
		  if(array_key_exists('frame-height', $_REQUEST)) {
	  		$height = $_REQUEST['frame-height'];
			  if($height != '') {
					if($sql != "") {
						$sql .= ', ';
					}
					$sql .= '`height` = '.$height;
				} else {
					$height = 0;
				}
			} else {
				$height = 300;
			}
	  	if(array_key_exists('frame-left', $_REQUEST)) {
			  $left = $_REQUEST['frame-left'];
		  	if($left != '') {
					if($sql != "") {
						$sql .= ', ';
					}
					$sql .= '`left` = '.$left;
				} else {
					$left = 0;
				}
			} else {
				$left = 0;
			}
		  if(array_key_exists('frame-top', $_REQUEST)) {
	  		$top = $_REQUEST['frame-top'];
			  if($top != '') {
					if($sql != "") {
						$sql .= ', ';
					}
					$sql .= '`top` = '.$top;
				} else {
					$top = 0;
				}
			} else {
				$top = 0;
			}
			if(array_key_exists('frame-maximized', $_REQUEST)) {
			  $maximized = $_REQUEST['frame-maximized'];
			  if($maximized == "true" || $maximized = "false") {
					if($sql != "") {
						$sql .= ', ';
					}
					$sql .= '`maximized` = '.(($maximized == "true") ? 1 : 0);
				} else {
					$maximized = 0;
				}
			} else {
				$maximized = 0;
			}
			
			if(count($dbObject->fetchAll('SELECT `width` FROM `window_properties` WHERE `user_id` = '.$userId.' AND `frame_id` = "'.$frameId.'";', true, true, true)) == 0) {
				$dbObject->execute('INSERT INTO `window_properties`(`user_id`, `frame_id`, `left`, `top`, `width`, `height`, `maximized`) VALUES ('.$userId.', "'.$frameId.'", '.$left.', '.$top.', '.$width.', '.$height.', '.$maximized.');', true);
			} else {
				if($sql != '') {
					$dbObject->execute('UPDATE `window_properties` SET '.$sql.' WHERE `frame_id` = "'.$frameId.'" AND `user_id` = '.$userId.';', true, true);
				}
			}
	  }
	} else {
		echo "No no no!";
	}

?>