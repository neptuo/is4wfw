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
   *  @timestamp  2010-07-25
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
     *	If true, none of calls to execute executes and fetchAll shows query and result.     
     *
     */		 		     
    private $mockMode = false;
    
    /**
     *
     *	Simply counts db queries per 1 request     
     *
     */		 		     
    private $queriesPerRequest = 0;
	
	private $cacheResults = 'REQUEST';
    
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
    
	public function setCacheResults($val) {
		$this->cacheResults = $val;
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
     *	Closes current opened connection and opens one, defined by name.
     *	
     *	@param	name						database connection name		      
     *  
     *	@return	none     
     *
     */		 		 		     
    public function useConnection($name) {
		if($name == 'default') {
			self::close();
			self::connect(WEB_DB_HOSTNAME, WEB_DB_USER, WEB_DB_PASSWORD, WEB_DB_DATABASE);
		} elseif(strlen($name) != 0) {
			$conn = self::fetchAll('select `hostname`, `user`, `password`, `database`, `fs_root` from `db_connection` where `name` = "'.$name.'"');
			if(count($conn) > 0) {
				$conn = $conn[0];
				self::close();
				self::connect($conn['hostname'], $conn['user'], $conn['password'], $conn['database']);
			} else {
				// bad conn name!
			}
		}
	}
    
    /**
     *
     *  Execute SQL query.
     *  
     *  @param  query           sql query
     *  @param  showQuery       shows input sql query on output
     *  @param  notExecuteQuery if true, doesn't execute query
     *  @param	forceImmediateOutput  if previsou is true, it immediatly output query
     *
     */                   
    public function execute($query, $showQuery = false, $forceImmediateOutput = false, $notExecuteQuery = false) {
    	global $logObject;
    	global $webObject;
    	$this->queriesPerRequest ++;
			
		if($this->IsOpen) {
			if($showQuery || $this->mockMode) {
				$pquery = str_replace('<', '&lt;', str_replace('>', '&gt;', $query));
				if($forceImmediateOutput || $this->mockMode) {
					echo "<div style=\"border: 2px solid gray; padding: 5px; margin: 5px;\"><strong style=\"color: red;\">SQL query:</strong><br /><code>".$pquery."</code></div>";
				} else {
					$webObject->PageLog .= "<div style=\"border: 2px solid gray; padding: 5px; margin: 5px;\"><strong style=\"color: red;\">SQL query:</strong><br /><code>".$pquery."</code></div>";
				}
			}
			  
			if(!$notExecuteQuery && !$this->mockMode) {
				$result = mysql_query($query);
  			
				$errno = mysql_errno();
				if($errno != 0) {
					if(is_object($logObject)) {
						$logObject->write('Mysql query error! ERRNO = '.$errno.', ERRORMSG = '.mysql_error().', QUERY = '.$query.'');
					} else {
						echo 'Mysql query error! ERRNO = '.$errno.', ERRORMSG = '.mysql_error().', QUERY = '.$query.'';
					}
				}
			}
			
			return ;
		} else {
			trigger_error("Connection is closed, cannot fetch data!", E_USER_WARNING);
		}
	}
    
    /**
     *
     *  Returns all rows fetched by database.
     *  
     *  @param  query           			sql query
     *  @param  showQuery       			shows input sql query on output
     *  @param  printOutput     			shows return from database through print_r function
     *  @param  notExecuteQuery 			if true, doesn't execute query
     *  @param	forceImmediateOutput  if previsou 2 are true, it immediatly output query & result
     *  @return returns all rows fetched by database
     *
     */                   
    public function fetchAll($query, $showQuery = false, $printOutput = false, $forceImmediateOutput = false, $notExecuteQuery = false) {
    	global $logObject;
    	global $webObject;
		if($this->IsOpen) {
			if($showQuery || $this->mockMode) {
				if($forceImmediateOutput || $this->mockMode) {
					echo "<div style=\"border: 2px solid gray; padding: 5px; margin: 5px;\"><strong style=\"color: red;\">SQL query:</strong><br /><code>".$query."</code></div>";
			  	} else {
					$webObject->PageLog .= "<div style=\"border: 2px solid gray; padding: 5px; margin: 5px;\"><strong style=\"color: red;\">SQL query:</strong><br /><code>".$query."</code></div>";
				}
			}
			  
			$hashQuery = '';
			if(!$notExecuteQuery) {
				$return = array();
				$result = array();
				$hashQuery = sha1($query);
				if($this->cacheResults == 'REQUEST' && parent::request()->exists($hashQuery, 'database-cache')) {
					$return = parent::request()->get($hashQuery, 'database-cache');
					return $return;
				} else {
					$this->queriesPerRequest ++;
					$result = mysql_query($query);
				}
  				
  				$errno = mysql_errno();
  				if($errno != 0) {
  					if(is_object($logObject)) {
						$logObject->write('Mysql query error! ERRNO = '.$errno.', ERRORMSG = '.mysql_error().', QUERY = '.$query.'');
					} else {
						echo 'Mysql query error! ERRNO = '.$errno.', ERRORMSG = '.mysql_error().', QUERY = '.$query.'';
					}
				}
  				
  				while($row = mysql_fetch_assoc($result)) {
  					$return[] = $row;
  				}
			}
				
			if($printOutput || $this->mockMode) {
				if($forceImmediateOutput || $this->mockMode) {
					echo "<div style=\"border: 2px solid gray; padding: 5px; margin: 5px; overflow: auto;\"><strong style=\"color: red;\">SQL output:</strong><pre>";
					$str = print_r($return, true);
					echo htmlentities($str);
					echo "</pre></div>";
				} else {
					$webObject->PageLog .= "<div style=\"border: 2px solid gray; padding: 5px; margin: 5px; overflow: auto;\"><strong style=\"color: red;\">SQL output:</strong><pre>";
					$str = print_r($return, true);
					$webObject->PageLog .= htmlentities($str);
					$webObject->PageLog .= "</pre></div>";
				}
			}
			
			if($this->cacheResults == 'REQUEST') {
				parent::request()->set($hashQuery, $return, 'database-cache');
			}
			return $return;
		} else {
			trigger_error("Connection is closed, cannot fetch data!", E_USER_WARNING);
		}
	}
		
	/**
     *
     *  Returns single row fetched by database.
     *  
     *  @param  query           			sql query
     *  @param  showQuery       			shows input sql query on output
     *  @param  printOutput     			shows return from database through print_r function
     *  @param  notExecuteQuery 			if true, doesn't execute query
     *  @param	forceImmediateOutput  if previsou 2 are true, it immediatly output query & result
     *  @return returns all rows fetched by database
     *
     */                   
    public function fetchSingle($query, $showQuery = false, $printOutput = false, $forceImmediateOutput = false, $notExecuteQuery = false) {
    	$data = self::fetchAll($query, $showQuery, $printOutput, $forceImmediateOutput, $notExecuteQuery);
    	if(count($data) > 0) {
    		return $data[0];
    	} else {
    		return array();
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
		 *	Returns id of last inserted row
		 *
		 */		 		 		 		
		public function getLastId() {
			return mysql_insert_id();
		}
		
		/***
		 *
		 *	Sets mockmode, means that none of calls to execute executes and fetchAll shows query and result.
		 *
		 */		 		 		 		
		public function setMockMode($enabled) {
			if($enabled == true) {
				$this->mockMode = true;
				echo '<div style="color: white; margin: 5px; padding: 5px; border: 2px solid gray;"><div style="background: red; padding: 2px 5px; font-weight: bold;">Using mock mode ...</div></div>';
			} else {
				$this->mockMode = false;
				echo '<div style="color: white; margin: 5px; padding: 5px; border: 2px solid gray;"><div style="background: red; padding: 2px; font-weight: bold;">Stopped using mock mode ...</div></div>';
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
		
		/**
		 *
		 *	Returns queriesPerRequest.		 
		 *
		 */		 		 		
		public function getQueriesPerRequest() {
			return $this->queriesPerRequest;
		}
    
  }

?>
