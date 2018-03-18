<?php

	require_once(APP_SCRIPTS_PHP_PATH . "classes/manager/SystemProperty.class.php");

	// $dbObject->setMockMode(true);

	$propertyName = "db_version";
	$property = new SystemProperty($dbObject->getDataAccess());

	// Nacti verzi system a verzi db
	$databaseVersion = intval($property->getValue($propertyName));
	$newVersion = $databaseVersion;

	// Podpora pouze pro zvysovani verze
	$xml = new SimpleXMLElement(file_get_contents('data/autoupdate.xml'));
	foreach ($xml->script as $script) {
		$attrs = $script->attributes();

		$build = intval($attrs['build']);
		
		if ($build > $databaseVersion) {
			$ok = false;
			$sql = '';
			if ($attrs['resource'] != '') {
				// Nacti z souboru
				// echo 'Resource: "'.$attrs['resource'].'"<br />';
				if (file_exists($attrs['resource'])) {
					$ok = true;
					$sql = file_get_contents($attrs['resource']);
				}
			} else {
				// Pouzij obsah elementu
				// echo 'Script: "'.$script.'"<br />';
				$ok = true;
				$sql = trim($script);
			}
			
			if ($ok) {
				$dbObject->execute($sql);
				
				if ($build > $newVersion) {
					$newVersion = $build;
				}
			}
		}
	}

	if ($databaseVersion != $newVersion) {
		$property->setValue($propertyName, $newVersion);
	}
	
	// $dbObject->setMockMode(false);

?>