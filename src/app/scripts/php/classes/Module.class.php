<?php

    require_once("CodeWriter.class.php");

    class Module {
        private static $modules = null;

        public static function all(): array {
            if (Module::$modules == null) {
                $loaderPath = MODULES_PATH . ModuleGenerator::loaderFileName;
                if (file_exists($loaderPath)) {
                    include($loaderPath);
                    if (function_exists("__loadModules")) {
                        Module::$modules = __loadModules();
                    }
                }
            }

            if (!is_array(Module::$modules)) {
                Module::$modules = [];
            }

            return Module::$modules;
        }

        public static function findById($id): ?Module {
            $modules = array_filter(Module::all(), function ($module) use ($id) { return $module->id == $id; });
            if (count($modules) == 1) {
                return $modules[ArrayUtils::firstKey($modules)];
            }

            return null;
        }

        public static function findByAlias($alias): ?Module {
            $modules = array_filter(Module::all(), function ($module) use ($alias) { return $module->alias == $alias; });
            if (count($modules) == 1) {
                return $modules[0];
            }

            return null;
        }
        
        public static function getById($id): Module {
            $module = Module::findById($id);
            if ($module == null) {
                throw new Exception("Missing module '$id'.");
            }

            return $module;
        }
        
        public static function getByAlias($alias): Module {
            $module = Module::findByAlias($alias);
            if ($module == null) {
                throw new Exception("Missing module with alias '$alias'.");
            }

            return $module;
        }

        // --- Instance members -----------------------------------------------

        public $id;
        public $alias;
        public $name;

        public function __construct($id, $alias, $name)
        {
            $this->id = $id;
            $this->alias = $alias;
            $this->name = $name;
        }

        public function getRootPath() {
            return MODULES_PATH . $this->alias . "/";
        }

        public function getLibsPath() {
            return MODULES_PATH . $this->alias . "/libs/";
        }

        public function getViewsPath() {
            return MODULES_PATH . $this->alias . "/views/";
        }

        public function getAssetsPath() {
            return "~/assets/" . $this->alias . "/";
        }
    }

    class ModuleXml {
        public static function filePath() {
            return USER_PATH . "modules.xml";
        }

        public static function read() {
            $path = ModuleXml::filePath();
            if (file_exists($path)) {
                return new SimpleXMLElement(file_get_contents($path));
            }

            return new SimpleXMLElement("<modules></modules>");
        }

        public static function write(SimpleXMLElement $xml) {
            file_put_contents(ModuleXml::filePath(), $xml->asXML());
        }
    }

    class ModuleGenerator {
        public const loaderFileName = "loader.inc.php";
        public const postInitFileName = "postinit.inc.php";
        
        public static function loader() {
            $code = new CodeWriter();
            $code->addLine("// Generated content", true);
            $code->addLine("");

            $code->addLine("function __loadModules() {");
            $code->addIndent();
            
            $code->addLine("return [");
            $code->addIndent();
            
            $xml = ModuleXml::read();
            foreach ($xml->module as $module) {
                $code->addLine("new Module('" . $module->id . "', '" . $module->alias . "', '" . $module->name . "'),", true);
            }

            $code->removeIndent();
            $code->addLine("];", true);
            
            $code->removeIndent();
            $code->addLine("}", true);
            
            $code->writeToFile(MODULES_PATH . ModuleGenerator::loaderFileName);
        }

        public static function postInit() {
            $code = new CodeWriter();
            $code->addLine("// Generated content", true);
            $code->addLine("");

            foreach (Module::all() as $module) {
                $alias = $module->alias;
                $path = MODULES_PATH . $alias . "/" . ModuleGenerator::postInitFileName;
                if (file_exists($path)) {
                    $code->addLine("require(MODULES_PATH . '$alias' . '/' . '" . ModuleGenerator::postInitFileName . "');");
                }
            }

            $code->writeToFile(MODULES_PATH . ModuleGenerator::postInitFileName);
        }
    }

?>