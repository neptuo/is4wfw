<?php

    require_once("BaseTagLib.class.php");

    /**
     * 
     *  Simple log class.
     *  It logs passed string to log file in logs   
     *  Default object.
     *  
     *  @objectname logObject
     *  
     *  @author     Marek SMM
     *  @timestamp  2009-06-18
     * 
     */
    class Log extends BaseTagLib {

        private $IsOpen = false;
        private $File = null;
        public static $LogFile = '';

        public function __destruct() {
            if ($this->IsOpen == true) {
                fclose($this->File);
            }
        }

        /**
         *
         *  Writes passed message to log file.
         *  
         *  @param  msg message text to write
         *  @return none                    
         *
         */
        public function write($msg) {
            global $webObject;
            Log::$LogFile = LOGS_PATH . ($webObject != null ? $webObject->getProjectId() : "_") . '-' . date("Y-m-d") . ".log";
            if ($this->IsOpen == false) {
                if (is_file(Log::$LogFile)) {
                    $this->File = fopen(Log::$LogFile, "a");
                } else {
                    $this->File = fopen(Log::$LogFile, "w");
                }
                $this->IsOpen = true;
            }
            fwrite($this->File, date("H:i:s") . "\t" . $msg . "\r\n");
        }

        public function page($msg) {
            parent::log($msg);
        }

        public function writeToCustom($logName, $msg) {
            $path = LOGS_PATH . $logName . "-" . date("Y-m-d") . ".log";
            if (is_file($path)) {
                $file = fopen($path, "a");
            } else {
                $file = fopen($path, "w");
            }
            fwrite($file, date("H:i:s") . PHP_EOL . $msg . PHP_EOL);
            fclose($file);
        }

        private function processException($e, $params, $writeToLog = true) {
            $message = "An exception of type '" . get_class($e) . "' has occured";
            if (array_key_exists("tagPrefix", $params) && array_key_exists("tagName", $params)) {
                $message .= " while processing tag '{$params["tagPrefix"]}:{$params["tagName"]}'";
            }
            if (array_key_exists("boundary", $params)) {
                $message .= " while processing error boundary '{$params["boundary"]}'";
            }
            $message .= ". " . PHP_EOL;
            if (array_key_exists("params", $params) && count($params["params"]) > 0) {
                $strParams = [];
                foreach ($params["params"] as $key => $value) {
                    $strParams[] = "'$key' = '$value'";
                }
                $message .= "Params: " . implode(", ", $strParams) . PHP_EOL;
            }

            $message .= $e->getMessage() . PHP_EOL . $e->getTraceAsString() . PHP_EOL . '@ ' . HttpUtils::currentAbsoluteUrl();

            if ($writeToLog) {
                $this->write($message);
            }

            global $webObject;
            if ($webObject->getDebugMode()) {
                return ''
                . '<error style="position: relative; z-index: 1000">'
                    . '<error-header style="padding: 4px 8px; color: red; font-weight: bold; display: inline-block; cursor: pointer; background: yellow;" title="Custom tag error" onclick="this.nextSibling.style.display = this.nextSibling.style.display == \'block\' ? \'none\' : \'block\'">!</error-header>'
                    . '<error-body style="position: absolute; top: 24px; left: 0; overflow: auto; width: 800px; max-height: 600px; background: yellow; display: none; padding: 4px 8px; white-space: pre; font-family: monospace;">'
                        . $message
                    . '</error-body>'
                . '</error>';
            } else {
                return "";
            }
        }

        public function exception($e, $params = []) {
            return $this->processException($e, $params, true);
        }
        
        public function getDebugExceptionView($e, $params = []) {
            return $this->processException($e, $params, false);
        }
    }

?>
