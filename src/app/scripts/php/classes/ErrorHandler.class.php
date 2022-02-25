<?php

    /**
     * 
     *  Class for error handling.
     *    
     *  @author     Marek SMM
     *  @timestamp  2008-11-24
     *          
     */
    class ErrorHandler {

        private static $levels = [
            E_ERROR             => true,
            E_USER_ERROR        => true,
            E_CORE_ERROR        => true,
            E_COMPILE_ERROR     => true,
            E_RECOVERABLE_ERROR => true,
            E_WARNING           => false,
            E_USER_WARNING      => false,
            E_CORE_WARNING      => false,
            E_COMPILE_WARNING   => false,
            E_PARSE             => false,
            E_NOTICE            => false,
            E_USER_NOTICE       => false,
            E_STRICT            => false,
            E_DEPRECATED        => false,
            E_USER_DEPRECATED   => false,
            E_ALL               => false,
        ];

        public static function install() {
            set_error_handler("ErrorHandler::onError");
        }

        public static function setLevel(int $level, $enabled) {
            if (array_key_exists($level, ErrorHandler::$levels)) {
                ErrorHandler::$levels[$level] = $enabled;
            }
            // error_reporting(E_ALL ^ E_NOTICE);
        }

        public static function onError($errno, $errstr, $errfile, $errline) {
            if (array_key_exists($errno, ErrorHandler::$levels) && !ErrorHandler::$levels[$errno]) {
                return;
            }

            // Custom tag exceptions are logged using log object.
            // See https://www.php.net/manual/en/errorfunc.constants.php
            switch ($errno) {
                case E_ERROR:
                case E_USER_ERROR:
                    $prefix = "ERROR";
                    break;
                    
                case E_WARNING:
                case E_USER_WARNING:
                    $prefix = "WARNING";
                    break;

                case E_NOTICE:
                case E_USER_NOTICE:
                    $prefix = "NOTICE";
                    break;

                case E_PARSE:
                    $prefix = "PARSE";
                    break;

                case E_DEPRECATED:
                    $prefix = "DEPRECATED ";
                    break;

                default:
                    $prefix = "UNRECOGNIZED";
                    break;
            }

            $message = "$prefix [$errno]: '$errstr' on line $errline in file $errfile.";
            ErrorHandler::write($errno, $message);
            return true;
        }

        private static function write($level, $message) {
            global $logObject;
            if ($logObject) {
                $logObject->write($message);
            } else {
                echo "<hr />" . $message . "<hr />";
            }

            if ($level == E_ERROR || $level == E_USER_ERROR) {
                exit($level);
            }
        }

    }

?>
