<?php

require_once(APP_SCRIPTS_PHP_PATH . "libs/BaseTagLib.class.php");

class SystemProperty extends BaseTagLib {

    private $db;

    function __construct($db = null) {
        if ($db == null) {
            $db = parent::db()->getDataAccess();
        }

        $this->db = $db;
    }

    public function getValue($name) {
        $path = CACHE_SYSTEMPROPERTY_PATH . $name . '.txt';
        if (file_exists($path)) {
            return file_get_contents($path);
        }

        $entity = $db->fetchSingle('select `value` from `system_property` where `key` = "' . $db->escape($name) . '";');
        $value = $entity['value'];
        file_put_contents($path, $value);
        return $value;
    }

    public function setValue($name, $value) {
        if ($db->fetchSingle('select `value` from `system_property` where `key` = "' . $db->escape($name) . '";') == array()) {
            $db->execute('insert into `system_property`(`value`, `key`) values("' . $db->escape($value) . '", "' . $db->escape($name) . '");');
		} else {
            $db->execute('update `system_property` set `value` = "' . $db->escape($value) . '" where `key` = "' . $db->escape($name) . '";');
        }
        
        $path = CACHE_SYSTEMPROPERTY_PATH . $name . '.txt';
        file_set_contents($path, $value);
    }
}

?>