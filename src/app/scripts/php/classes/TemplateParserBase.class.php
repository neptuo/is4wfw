<?php

    require_once(APP_SCRIPTS_PHP_PATH . "classes/CodeWriter.class.php");

    abstract class TemplateParserBase {
        
        // true/false store measures.
        static $Measure = false;
        static $Measures = array();
        
        public static function saveMeasures($value) {
            TemplateParserBase::$Measure = $value;
        }
        
        public static function getMeasures() {
            return TemplateParserBase::$Measures;
        }

        private $startTime;

        protected function startMeasure() {
            $this->startTime = 0;
            if (TemplateParserBase::$Measure) {
                $this->startTime = microtime();
            }
        }

        protected function stopMeasure($content) {
            if (TemplateParserBase::$Measure) {
                $endTime = microtime();
                $elapsed = $endTime - $this->startTime;
                array_push(TemplateParserBase::$Measures, array($elapsed, $content));
            }
        }

        protected function generateRandomString($length = 10) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            return $randomString;
        }

        protected function checkPregError($function, $value = null) {
            $message = null;
            $error = preg_last_error();
            if ($error == PREG_NO_ERROR) {
                // $message = "There is no error.";
            }
            else if ($error == PREG_INTERNAL_ERROR) {
                $message = "There is an internal error";
            }
            else if ($error == PREG_BACKTRACK_LIMIT_ERROR) {
                $message = "Backtrack limit was exhausted";
            }
            else if ($error == PREG_RECURSION_LIMIT_ERROR) {
                $message = "Recursion limit was exhausted";
            }
            else if ($error == PREG_BAD_UTF8_ERROR) {
                $message = "Bad UTF8 error";
            }
            else if ($error == PREG_BAD_UTF8_OFFSET_ERROR) {
                $message = "Bad UTF8 offset error";
            }
            else if ($error == PREG_JIT_STACKLIMIT_ERROR) {
                $message = "JIT stack limit error";
            }

            if ($message != null) {
                $this->log("TemplateParser '$function': $message, '$value'");
            }
        }

        protected function log($var) {
            global $phpObject;
            $phpObject->logVar($var);
        }
    }

?>