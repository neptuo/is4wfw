<?php

require_once("scripts/php/libs/BaseTagLib.class.php");

class SystemProperty extends BaseTagLib {

    public function getValue($name) {
        $path = SYSTEM_PROPERTY_CACHE_DIR . $name . '.txt';
        if (file_exists($path)) {
            return file_get_contents($path);
        }

        $entity = parent::db()->fetchSingle('select `value` from `system_property` where `key` = "' . parent::db()->escape($name) . '";');
        $value = $entity['value'];
        file_put_contents($path, $value);
        return $value;
    }

    public function setValue($name, $value) {
        if (parent::db()->fetchSingle('select `value` from `system_property` where `key` = "' . parent::db()->escape($name) . '";') == array()) {
            parent::db()->execute('insert into `system_property`(`value`, `key`) values("' . parent::db()->escape($value) . '", "' . parent::db()->escape($name) . '");');
		} else {
            parent::db()->execute('update `system_property` set `value` = "' . parent::db()->escape($value) . '" where `key` = "' . parent::db()->escape($name) . '";');
        }
        
        $path = SYSTEM_PROPERTY_CACHE_DIR . $name . '.txt';
        file_set_contents($path, $value);
    }
}

?>