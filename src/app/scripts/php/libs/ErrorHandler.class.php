<?php

    require_once("BaseTagLib.class.php");

    /**
     * 
     * Class handling errors.
     * Default object.
     *    
     * @objectname  errorObject
     *  
     *  @author     Marek SMM
     *  @timestamp  2008-11-24
     *          
     */
    class ErrorHandler extends BaseTagLib {

        /**
         *  
         *  Array to handle error messages
         *
         */                   
        private $Errors;

        /**
         *
         *  Object constructor     
         *
         */              
        public function __construct() {
            parent::setTagLibXml("Error.xml");
            //error_reporting(0);
        }

        /**
         *
         *  Adds error msg.
         *  
         *  @param  msg string error message          
         *  @return none
         *  
         */
        public function add($msg) {
            $this->Errors[] = $msg;
        }

        /**
         *
         *  Show all error messages.
         *  
         *  @return string of error messages          
         *
         */                  
        public function show($atts = false) {
            if (count($this->Errors) > 0) {
                $msg = "<h4 class=\"error\"><ul>";
                foreach ($this->Errors as $tmp) {
                    $msg .= "<li>".$tmp."</li>";
                }

                $msg .= "</ul></h4>";
            } else {
                $msg = "";
            }

            return $msg;
        }

        /**
         *
         *  Handle error from web application and write it to the log file.
         *  
         *  @param  errno   error number
         *  @param  errstr  error message content
         *  @param  errfile file where error occurs
         *  @param  errline line where error occurs
         *  
         *  @return true indicates error where succesfully processed.          
         *
         */                   
        public static function errorHandler($errno, $errstr, $errfile, $errline) {
            // Dodelat zapisovani do logu!!!!
            switch ($errno) {
                case E_USER_ERROR:
                    $str = "ERROR: [$errno] $errstr! Fatal error on line $errline in file $errfile.";

                    global $logObject;
                    if($logObject) {
                        $logObject->write($str);
                    } else {
                        echo "<hr />".$str."<hr />";
                    }

                    exit($errno);
                    break;

                case E_USER_WARNING:
                    $str = "WARNING: [$errno] $errstr. On line $errline in file $errfile.";

                    global $logObject;
                    if($logObject) {
                        $logObject->write($str);
                    } else {
                        echo "<hr />".$str."<hr />";
                    }

                    break;

                case E_USER_NOTICE:
                    $str = "NOTICE: [$errno] $errstr, Fatal error on line $errline in file $errfile";

                    global $logObject;
                    if($logObject) {
                        $logObject->write($str);
                    } else {
                        echo "<hr />".$str."<hr />";
                    }

                    break;

                default:
                    //echo "Unknown error type: [$errno] $errstr<br />$errfile - $errline<br />\n";
                    break;
            }

            return true;
        }
    }

?>
