<?php

    class LibraryLoader {
        private function getFilePath($classPath, $extension) {
            $cpArray = StringUtils::explode($classPath, '.');
            if ($cpArray[0] == "php") {
                $xmlPath = APP_SCRIPTS_PHP_PATH . "libs/";
            } else {
                $xmlPath = Module::getByAlias($cpArray[0])->getLibsPath();
            }

            for ($i = 2; $i < count($cpArray); $i ++) {
                if($i < count($cpArray) - 1) {
                    $xmlPath .= $cpArray[$i] . '/';
                } else {
                    $xmlPath .= $cpArray[$i] . $extension;
                }
            }

            return $xmlPath;
        }

        public function getXmlPath($classPath) {
            return $this->getFilePath($classPath, ".xml");
        }
        
        public function getCodePath($classPath) {
            return $this->getFilePath($classPath, ".class.php");
        }

        public function getClassName($classPath) {
            $parts = StringUtils::explode($classPath, '.');
            return $parts[count($parts) - 1];
        }

        public function all() {
            $libraries = $this->getLibrariesFromDirectory("php", APP_SCRIPTS_PHP_PATH . "libs/");
            foreach (Module::all() as $module) {
                $libraries = array_merge($libraries, $this->getLibrariesFromDirectory($module->alias, $module->getLibsPath()));
            }

            sort($libraries);
            return $libraries;
        }

        private function getLibrariesFromDirectory($alias, $folderPath) {
            $libraries = [];

            $libs = FileUtils::searchDirectoryRecursive($folderPath, ".xml");
            foreach ($libs as $lib) {
                $lib = str_replace($folderPath, $alias . ".libs.", $lib);
                $lib = str_replace("/", ".", $lib);
                $lib = str_replace(".xml", "", $lib);
                $libraries[] = $lib;
            }

            return $libraries;
        }
    }

?>