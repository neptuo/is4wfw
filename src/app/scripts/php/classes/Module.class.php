<?php

    require_once("CodeWriter.class.php");
    require_once("manager/Version.class.php");
    require_once("utils/FileUtils.class.php");

    class Module {
        private static $all = null;
        private static $active = null;

        public static function reload() {
            Module::$all = null;
            Module::$active = null;
            return Module::all();
        }

        private static function ensure() {
            if (Module::$all == null) {
                $loaderPath = CACHE_MODULES_PATH . ModuleGenerator::loaderFileName;
                if (file_exists($loaderPath)) {
                    include($loaderPath);
                    
                    global $__loadModules;
                    if (is_callable($__loadModules)) {
                        Module::$all = $__loadModules();
                    }
                }
            }

            if (!is_array(Module::$all)) {
                Module::$all = [];
            }

            if (Module::$active == null) {
                Module::$active = array_filter(Module::$all, function($module) { return !$module->is4wfw || Module::isSupportedVersion($module); });
            }
        }

        public static function all($activeOnly = true): array {
            Module::ensure();
            if ($activeOnly) {
                return Module::$active;
            }

            return Module::$all;
        }

        public static function findById($id, $activeOnly = true): ?Module {
            $modules = array_filter(Module::all($activeOnly), function ($module) use ($id) { return $module->id == $id; });
            if (count($modules) == 1) {
                return $modules[ArrayUtils::firstKey($modules)];
            }

            return null;
        }

        public static function findByAlias($alias, $activeOnly = true): ?Module {
            $modules = array_filter(Module::all($activeOnly), function ($module) use ($alias) { return $module->alias == $alias; });
            if (count($modules) == 1) {
                $key = ArrayUtils::firstKey($modules);
                return $modules[$key];
            }

            return null;
        }
        
        public static function getById($id, $activeOnly = true): Module {
            $module = Module::findById($id, $activeOnly);
            if ($module == null) {
                throw new Exception("Missing module '$id'.");
            }

            return $module;
        }
        
        public static function getByAlias($alias, $activeOnly = true): Module {
            $module = Module::findByAlias($alias, $activeOnly);
            if ($module == null) {
                throw new Exception("Missing module with alias '$alias'.");
            }

            return $module;
        }

		public static function isSupportedVersion($moduleOrMinVersion) {
            if ($moduleOrMinVersion instanceof Module) {
                if (!$moduleOrMinVersion->is4wfw) {
                    return true;
                }

                $minVersion = $moduleOrMinVersion->is4wfw->minVersion;
            } else {
                $minVersion = $moduleOrMinVersion;
            }

			$currentVersion = Version::parse(WEB_VERSION);
			$moduleVersion = Version::parse($minVersion);

			return $currentVersion["major"] == $moduleVersion["major"] && $currentVersion["patch"] >= $moduleVersion["patch"];
		}

        // --- Instance members -----------------------------------------------

        public $id;
        public $alias;
        public $name;
        public $version;
        public $gitHub;
        public $is4wfw;

        public function __construct($id, $alias, $name, $version = null, $gitHub = null, $is4wfw = null) {
            // This constructor is also used in AdminModule few lines bellow.
            $this->id = $id;
            $this->alias = $alias;
            $this->name = $name;
            $this->version = $version;
            $this->gitHub = $gitHub;
            $this->is4wfw = $is4wfw;
        }

        public function getRootPath() {
            return MODULES_PATH . $this->alias . "/";
        }

        public function getLibsPath() {
            return $this->getRootPath() . "libs/";
        }

        public function getViewsPath() {
            return $this->getRootPath() . "views/";
        }

        public function getAssetsPath() {
            return "~/assets/" . $this->alias . "/";
        }

        public function getBundlesPath() {
            return $this->getRootPath() . "bundles/";
        }

        public function canEdit() {
            return true;
        }
    }

    class AdminModule extends Module {
        public function __construct() {
            parent::__construct(
                "71b53781-b881-42b3-b39d-14aa18d64d43", 
                "admin", 
                "is4wfw administration", 
                CMS_VERSION, 
                null, 
                new ModuleIs4wfw(WEB_VERSION)
            );
        }

        public function getRootPath() {
            return APP_PATH . "admin/";
        }

        public function canEdit() {
            return false;
        }
    }

    class ModuleIs4wfw {
        public $minVersion;

        public function __construct($minVersion) {
            $this->minVersion = $minVersion;
        }
    }

    class ModuleGitHub {
        public $repositoryName;
        public $isPublic = true;
        public $accessToken;

        public function __construct($repositoryName, $isPublic, $accessToken) {
            $this->repositoryName = $repositoryName;
            $this->isPublic = $isPublic;
            $this->accessToken = $accessToken;
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

        /**
         * Regenerates all cache scripts for modules.
         * Side effect is that it reloads in-memory modules.
         */
        public static function all() {
            ModuleGenerator::loader();
            Module::reload();
            ModuleGenerator::postInit();
        }
        
        public static function loader() {
            $code = new CodeWriter();
            $code->addLine("// Generated content", true);
            $code->addLine("");

            $code->addLine('$' . "GLOBALS['__loadModules'] = function() {");
            $code->addIndent();
            
            $code->addLine("return [");
            $code->addIndent();
            
            $code->addLine("new AdminModule(),", true);

            $xml = ModuleXml::read();
            foreach ($xml->module as $module) {
                $args = [
                    "'" . $module->id . "'",
                    "'" . $module->alias . "'",
                    "'" . $module->name . "'",
                    "'" . $module->version . "'"
                ];

                if ($module->gitHub) {
                    $args[] = "new ModuleGitHub('" . $module->gitHub->repository->name . "', " . ($module->gitHub->repository->private ? 'false' : 'true') . ", '" . $module->gitHub->pat . "')";
                } else {
                    $args[] = "null";
                }

                if ($module->is4wfw) {
                    $args[] = "new ModuleIs4wfw('" . $module->is4wfw->minVersion . "')";
                } else {
                    $args[] = "null";
                }

                $code->addLine("new Module(" . implode(", ", $args) . "),", true);
            }

            $code->removeIndent();
            $code->addLine("];", true);
            
            $code->removeIndent();
            $code->addLine("}", true);
            
            FileUtils::ensureDirectory(CACHE_MODULES_PATH);
            $code->writeToFile(CACHE_MODULES_PATH . ModuleGenerator::loaderFileName);
        }

        public static function postInit() {
            $code = new CodeWriter();
            $code->addLine("// Generated content", true);
            $code->addLine("");

            foreach (Module::all() as $module) {
                $path = $module->getRootPath() . ModuleGenerator::postInitFileName;
                if (file_exists($path)) {
                    $log = $code->var("logObject");

                    $code->addLine("try {");
                    $code->addIndent();
                    $code->addLine("require('{$path}');");
                    $code->removeIndent();
                    $code->addLine("} catch (Exception {$code->var("e")}) {");
                    $code->addIndent();
                    $code->addLine("global $log;");
                    $code->addLine("{$log}->exception(" . '$' . "e);");
                    $code->closeBlock();
                }
            }

            FileUtils::ensureDirectory(CACHE_MODULES_PATH);
            $code->writeToFile(CACHE_MODULES_PATH . ModuleGenerator::postInitFileName);
        }
    }

?>