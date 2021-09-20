<?php

require_once(APP_SCRIPTS_PHP_PATH . "libs/BaseTagLib.class.php");

class SystemProperty extends BaseTagLib {

    private $db;

    function __construct($db) {
        if ($db == null) {
            throw new Exception("Missing required parameter DataAccess.");
        }

        $this->db = $db;
    }

    public function getValue($name) {
        $path = CACHE_SYSTEMPROPERTY_PATH . $name . '.txt';
        if (file_exists($path)) {
            return file_get_contents($path);
        }

        $entity = $this->db->fetchSingle('select `value` from `system_property` where `key` = "' . $this->db->escape($name) . '";');
        $value = $entity['value'];
        if ($this->canCreateFile($path)) {
            file_put_contents($path, $value);
        }

        return $value;
    }

    public function setValue($name, $value) {
        if ($this->db->fetchSingle('select `value` from `system_property` where `key` = "' . $this->db->escape($name) . '";') == array()) {
            $this->db->execute('insert into `system_property`(`value`, `key`) values("' . $this->db->escape($value) . '", "' . $this->db->escape($name) . '");');
		} else {
            $this->db->execute('update `system_property` set `value` = "' . $this->db->escape($value) . '" where `key` = "' . $this->db->escape($name) . '";');
        }
        
        $path = CACHE_SYSTEMPROPERTY_PATH . $name . '.txt';
        if ($this->canCreateFile($path)) {
            file_put_contents($path, $value);
        }
    }

    private function canCreateFile($path) {
        return file_exists(dirname($path));
    }
}

?>