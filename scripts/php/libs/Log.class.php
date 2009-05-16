<?php

  /**
   *
   *  Require base tag lib class.
   *
   */
  require_once("BaseTagLib.class.php");
  
  /**
   * 
   *  Simple log class.
   *  It logs passed string to log file in logs/   
   *  Default object.
   *  
   *  @objectname logObject
   *  
   *  @author     Marek SMM
   *  @timestamp  2008-11-24
   * 
   */              
  class Log extends BaseTagLib {
  
    /**
     *
     *  Object constructor     
     *
     */
    public function __construct() {
      parent::setTagLibXml("xml/Log.xml");
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
      $logFile = "logs/".date("Y-m-d").".log";
      if(is_file($logFile)) {
        $file = fopen($logFile, "a");
      } else {
        $file = fopen($logFile, "w");
      }
      fwrite($file, date("H:i:s")."-".$msg."\r\n");
      fclose($file);
    }
  }

?>
