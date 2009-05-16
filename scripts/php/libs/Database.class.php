<?php

  /**
   *
   *  Require base tag lib class.
   *
   */
  require_once("BaseTagLib.class.php");
  
  /**
   *             
   *  Class for working Database.
   *  
   *  @author     Marek SMM
   *  @timestamp  2008-12-06
   *   
   */              
  class Database extends BaseTagLib {
  
    /**
     *
     *  Holds true if connection is opened, false otherwise.
     *
     */                   
    private $IsOpen;
  
    /**
     *
     *  Holds connection with databse.
     *
     */
    private $conn;
    
    /**
     *
     *  Constructor connects to db using default db values.
     *  If is set attribute defaultLogin to false, it does'nt connect.
     *  
     *  @param  defaultLogin  if true, it connects, if false, it does'nt
     *
     */                   
    public function __construct($defaultLogin = true) {
      self::setTagLibXml("xml/Database.xml");
    
      if($defaultLogin) {
        $this->conn = mysql_connect(WEB_DB_HOSTNAME, WEB_DB_USER, WEB_DB_PASSWORD);
			  mysql_query("use ".WEB_DB_DATABASE);
			  $this->IsOpen = true;
			  mysql_query("SET CHARACTER utf-8;");
      }
    }
    
    /**
     *
     *  Connects to database.
     *  
     *  @param  hostname  hostname to connect
     *  @param  user      username
     *  @param  password  password to user acount in $user
     *  @param  dbname    database name
     *  @return none                              
     *
     */
    public function connect($hostname, $user, $password, $dbname) {
      $this->conn = mysql_connect($hostname, $user, $password);
      mysql_query("use ".$dbname);
    }
    
    /**
     *
     *  Closes connection with database.
     *  
     *  @return none;          
     *
     */                   
    public function close() {
      if($this->IsOpen) {
        mysql_close($this->conn);
      } else {
        trigger_error("Connection already closed!", E_USER_WARNING);
      }
    }
    
    /**
     *
     *  Execute SQL query.
     *  
     *  @param  query           sql query
     *  @param  showQuery       shows input sql query on output
     *  @param  notExecuteQuery if true, doesn't execute query
     *
     */                   
    public function execute($query, $showQuery = false, $notExecuteQuery = false) {
			if($this->IsOpen) {
			  if($showQuery) {
          echo "<div style=\"border: 2px solid gray; padding: 5px; margin: 5px;\"><strong style=\"color: red;\">SQL query:</strong><br />".$query."</div>";
			  }
			  
			  if(!$notExecuteQuery) {
  				$result = mysql_query($query);
				}
				
				return $return;
			} else {
        trigger_error("Connection is closed, cannot fetch data!", E_USER_WARNING);
      }
		}
    
    /**
     *
     *  Returns all rows fetched by database.
     *  
     *  @param  query           sql query
     *  @param  showQuery       shows input sql query on output
     *  @param  printOutput     shows return from database through print_r function
     *  @param  notExecuteQuery if true, doesn't execute query
     *  @return returns all rows fetched by database
     *
     */                   
    public function fetchAll($query, $showQuery = false, $printOutput = false, $notExecuteQuery = false) {
			if($this->IsOpen) {
			  if($showQuery) {
          echo "<div style=\"border: 2px solid gray; padding: 5px; margin: 5px;\"><strong style=\"color: red;\">SQL query:</strong><br />".$query."</div>";
			  }
			  
			  if(!$notExecuteQuery) {
  				$result = mysql_query($query);
  				$return = array();
  				
  				while($row = mysql_fetch_assoc($result)) {
  					$return[] = $row;
  				}
				}
				
				if($printOutput) {
          echo "<div style=\"border: 2px solid gray; padding: 5px; margin: 5px; overflow: auto;\"><strong style=\"color: red;\">SQL output:</strong><pre>";
          $str = print_r($return, true);
          echo htmlentities($str);
          echo "</pre></div>";
        }
				
				return $return;
			} else {
        trigger_error("Connection is closed, cannot fetch data!", E_USER_WARNING);
      }
		}
		
		/**
		 *
		 *  C tag function for fetching all rows.
		 *  
		 *  @param  query SQL query
		 *  @param  template  template for one row of output
		 *  
		 *  @return data from db passed through template or default template                    		 
		 *
		 */                    		
		public function fetch($query, $template = false) {
      if($this->IsOpen) {
        $data = self::fetchAll($query);
        
        if($template) {
          if(is_file(PHP_SCRIPTS.$template)) {
            $cont = file_get_contents(PHP_SCRIPTS.$template);
            
            $return = "";
            foreach($data as $tr) {
              foreach($tr as $tname => $td) {
                $return .= str_replace("<tpl:".$tname." />", $td, $cont);
              }
            }
            
            return $return;
          } else {
            trigger_error("Template file doesn't exist!", E_USER_WARNING);
          }
        } else {
          $return = "<table class=\"database-table\">";
          $i = 0;
          foreach($data as $tr) {
            $return .= "<tr class=\"database-tr tr-".$i."\">";
            $j = 0;
            foreach($tr as $tname => $td) {
              $return .= "<td class=\"database-td td-".$tname." td-".$j."\">".$td."</td>";
              $j ++;
            }
            $return .= "</tr>";
            $i ++;
          }
          $return .= "</table>";
        }
        
        return $return;
      } else {
        trigger_error("Connection is closed, cannot fetch data!", E_USER_WARNING);
      }
    }
		
		/**
		 *
		 *  Returns true if connection with database is opened, false otherwise.
		 *  
		 *  @return returns true if connection with database is opened, false otherwise     		 
		 *
		 */               		
		public function isOpen() {
			return $this->IsOpen;
		}
    
  }

?>
