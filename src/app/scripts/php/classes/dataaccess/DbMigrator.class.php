<?php

    require_once(APP_SCRIPTS_PHP_PATH . "classes/manager/SystemProperty.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/utils/FileUtils.class.php");

    class DbMigrator {
        public static function run($xmlFilePath, $systemPropertyName) {
            global $dbObject;
            $da = $dbObject->getDataAccess();
            $property = new SystemProperty($da);

            $databaseVersion = intval($property->getValue($systemPropertyName));
            $newVersion = $databaseVersion;

            $xml = new SimpleXMLElement(file_get_contents($xmlFilePath));
            foreach ($xml->script as $script) {
                $attrs = $script->attributes();
                $build = intval($attrs['build']);
                
                if ($build > $databaseVersion) {
                    $ok = false;
                    $sql = '';
                    if ($attrs['resource'] != '') {
                        $path = FileUtils::combinePath(dirname($xmlFilePath), $attrs['resource']);
                        if (file_exists($path)) {
                            $ok = true;
                            $sql = file_get_contents($path);
                        }
                    } else {
                        $ok = true;
                        $sql = trim($script);
                    }

                    if ($ok) {
                        $sql = explode(PHP_EOL, $sql);
                        foreach ($sql as $command) {
                            $da->execute($command);
                        }
                        
                        if ($build > $newVersion) {
                            $newVersion = $build;
                        }
                    }
                }
            }

            if ($databaseVersion != $newVersion) {
                $property->setValue($systemPropertyName, $newVersion);
            }
        }
    }

?>