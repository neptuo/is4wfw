<?php

require_once(APP_SCRIPTS_PHP_PATH . "libs/BaseTagLib.class.php");
require_once(APP_SCRIPTS_PHP_PATH . "classes/CodeWriter.class.php");

class SystemProperty extends BaseTagLib {

    private static $storage;
    private $db;

    function __construct($db) {
        if ($db == null) {
            throw new Exception("Missing required parameter DataAccess.");
        }

        $this->db = $db;
    }

    private function ensureCache() {
        if (SystemProperty::$storage == null) {
            $loaderPath = CACHE_SYSTEMPROPERTY_PATH . SystemPropertyGenerator::loaderFileName;
            if (!file_exists($loaderPath)) {
                $sql = new SqlBuilder($this->db);
                $data = $this->db->fetchAll($sql->select("system_property", ["key", "value"]));
                $items = [];
                foreach ($data as $item) {
                    $items[$item["key"]] = $item["value"];
                }

                SystemPropertyGenerator::loader($items);
            }
    
            include($loaderPath);
    
            global $__loadSystemProperties;
            if (is_callable($__loadSystemProperties)) {
                SystemProperty::$storage = $__loadSystemProperties();
            }
        }
    }

    public function getValue($name) {
        $this->ensureCache();

        if (array_key_exists($name, (array)SystemProperty::$storage)) {
            return SystemProperty::$storage[$name];
        }

        return null;
    }

    public function setValue($name, $value) {
        $sql = new SqlBuilder($this->db);
        $this->db->execute($sql->insertOrUpdate("system_property", ["key" => $name, "value" => $value], ["value"]));
        
        SystemProperty::$storage[$name] = $value;
        SystemPropertyGenerator::loader(SystemProperty::$storage);
    }
}

class SystemPropertyGenerator {
    public const loaderFileName = "loader.inc.php"; 

    public static function loader(array $data) {
        $code = new CodeWriter();
        $code->addLine("// Generated content", true);
        $code->addLine("");

        $code->addLine('$' . "GLOBALS['__loadSystemProperties'] = function() {");
        $code->addIndent();
        
        $code->addLine("return [");
        $code->addIndent();
        
        foreach ($data as $key => $value) {
            $code->addLine("\"{$key}\" => \"{$value}\",", true);
        }

        $code->removeIndent();
        $code->addLine("];", true);
        
        $code->removeIndent();
        $code->addLine("}", true);
        
        $code->writeToFile(CACHE_SYSTEMPROPERTY_PATH . SystemPropertyGenerator::loaderFileName);
    }
}

?>