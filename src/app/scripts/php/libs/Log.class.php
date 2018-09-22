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

        /**
         *
         *  Object constructor     
         *
         */
        public function __construct() {
            parent::setTagLibXml("Log.xml");
        }

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

        public function exception($e) {
            $message = "An exception of type '" . get_class($e) . "' has occured. " . PHP_EOL . $e->getMessage() . PHP_EOL . $e->getTraceAsString();
            self::write($message);
        }
    }

?>
