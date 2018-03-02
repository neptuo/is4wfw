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
            parent::setTagLibXml("xml/Log.xml");
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
            $this->LogFile = "logs/" . ($webObject != null ? $webObject->getProjectId() : "_") . '-' . date("Y-m-d") . ".log";
            if ($this->IsOpen == false) {
                if (is_file($this->LogFile)) {
                    $this->File = fopen($this->LogFile, "a");
                } else {
                    $this->File = fopen($this->LogFile, "w");
                }
                $this->IsOpen = true;
            }
            fwrite($this->File, date("H:i:s") . "\t" . $msg . "\r\n");
        }

    }

?>
