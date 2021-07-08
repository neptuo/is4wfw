<?php

    require_once("CodeWriter.class.php");

    class Moduler {
        private const postInitFileName = "postinit.inc.php";

        public function getAll() {
            return [
            ];
        }

        public function generatePostInit() {
            $code = new CodeWriter();
            $code->addLine("// Generated content", true);
            $code->addLine("");

            foreach ($this->getAll() as $module) {
                $alias = $module["alias"];
                $path = MODULES_PATH . $alias . "/" . Moduler::postInitFileName;
                if (file_exists($path)) {
                    $code->addLine("require(MODULES_PATH . '$alias' . '/' . '" . Moduler::postInitFileName . "')");
                }
            }

            $code->writeToFile(MODULES_PATH . Moduler::postInitFileName);
        }
    }

?>