<?php

	//$dbObject->setMockMode(true);

	// Nacti verzi system a verzi db
	$SystemVersion = BUILD_VERSION;
	$DatabaseVersion = $dbObject->fetchSingle('select `value` from `system_property` where `key` = "db_version";');
	$DatabaseVersion = $DatabaseVersion['value'];
	
	// Podpora pouze pro zvysovani verze
	if($SystemVersion > $DatabaseVersion) {
		$xml = new SimpleXMLElement(file_get_contents('data/autoupdate.xml'));
		foreach($xml->script as $script) {
			$attrs = $script->attributes();
			if($attrs['build'] >= $DatabaseVersion) {
				$ok = false;
				$sql = '';
				if($attrs['resource'] != '') {
					// Nacti z souboru
					//echo 'Resource: "'.$attrs['resource'].'"<br />';
					if(file_exists($attrs['resource'])) {
						$ok = true;
						$sql = file_get_contents($attrs['resource']);
					}
				} else {
					// Pouzij obsah elementu
					//echo 'Script: "'.$script.'"<br />';
					$ok = true;
					$sql = trim($script);
				}
				if($ok) {
					$dbObject->execute($sql);
				}
			}
		}
		$dbObject->execute('update `system_property` set `value` = "'.$SystemVersion.'" where `key` = "db_version";');
	}
	
	//$dbObject->setMockMode(false);

?>