<?php

    require_once("CodeWriter.class.php");

    class Module {
        private static $modules = null;

        public static function all(): array {
            if (Module::$modules == null) {
                require_once(MODULES_PATH . ModuleGenerator::loaderFileName);
                Module::$modules = __loadModules();
            }

            return Module::$modules;
        }

        public static function findById($id): ?Module {
            $modules = array_filter(Module::all(), function ($module) use ($id) { return $module->id == $id; });
            if (count($modules) == 1) {
                return $modules[0];
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

        public function getLibsPath() {
            return MODULES_PATH . $this->alias . "/libs/";
        }

        public function getViewsPath() {
            return MODULES_PATH . $this->alias . "/views/";
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
            
            $path = USER_PATH . "modules.xml";
            if (file_exists($path)) {
                $xml = new SimpleXMLElement(file_get_contents($path));
                foreach ($xml->module as $module) {
                    $code->addLine("new Module('" . $module->id . "', '" . $module->alias . "', '" . $module->name . "'),", true);
                }
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
                    $code->addLine("require(MODULES_PATH . '$alias' . '/' . '" . ModuleGenerator::postInitFileName . "')");
                }
            }

            $code->writeToFile(MODULES_PATH . ModuleGenerator::postInitFileName);
        }
    }

?>