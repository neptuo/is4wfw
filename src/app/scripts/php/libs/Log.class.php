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

        public function exception($e, $tagPrefix = null, $tagName = null) {
            $editModel = parent::getEditModel(false);
            if ($editModel != null && $editModel->isSave()) {
                $editModel->exception($e);
            }

            $message = "An exception of type '" . get_class($e) . "' has occured";
            if ($tagPrefix != null && $tagName != null) {
                $message .= " while processing tag '$tagPrefix:$tagName'";
            }
            $message .= ". " . PHP_EOL;

            $message .= $e->getMessage() . PHP_EOL . $e->getTraceAsString() . PHP_EOL . '@ ' . HttpUtils::currentAbsoluteUrl();
            $this->write($message);

            global $webObject;
            if ($webObject->getDebugMode()) {
                return ''
                . '<error style="position: relative;">'
                    . '<error-header style="padding: 4px 8px; color: red; font-weight: bold; display: inline-block; cursor: pointer; background: yellow;" title="Custom tag error" onclick="this.nextSibling.style.display = this.nextSibling.style.display == \'block\' ? \'none\' : \'block\'">!</error-header>'
                    . '<error-body style="position: absolute; top: 24px; left: 0; overflow: auto; width: 800px; max-height: 600px; background: yellow; display: none; padding: 4px 8px; white-space: pre; font-family: monospace;">'
                        . $message
                    . '</error-body>'
                . '</error>';
            } else {
                return "";
            }
        }
    }

?>
