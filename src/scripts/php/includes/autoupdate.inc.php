<?php

	require_once("scripts/php/classes/manager/SystemProperty.class.php");

	//$dbObject->setMockMode(true);

	$propertyName = "db_version";
	$property = new SystemProperty();

	// Nacti verzi system a verzi db
	$SystemVersion = BUILD_VERSION;
	$DatabaseVersion = $property->getValue($propertyName);

	// Podpora pouze pro zvysovani verze
	if ($SystemVersion > $DatabaseVersion) {
		$xml = new SimpleXMLElement(file_get_contents('data/autoupdate.xml'));
		foreach ($xml->script as $script) {
			$attrs = $script->attributes();
			if ($attrs['build'] >= $DatabaseVersion) {
				$ok = false;
				$sql = '';
				if ($attrs['resource'] != '') {
					// Nacti z souboru
					//echo 'Resource: "'.$attrs['resource'].'"<br />';
					if (file_exists($attrs['resource'])) {
						$ok = true;
						$sql = file_get_contents($attrs['resource']);
					}
				} else {
					// Pouzij obsah elementu
					//echo 'Script: "'.$script.'"<br />';
					$ok = true;
					$sql = trim($script);
				}
				
				if ($ok) {
					$dbObject->execute($sql);
				}
			}
		}

		$property->setValue($propertyName, $SystemVersion);
	}
	
	//$dbObject->setMockMode(false);

?>