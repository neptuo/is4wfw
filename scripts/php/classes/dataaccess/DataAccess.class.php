<?php

class DataAccess {
	private $isOpened = false;
	private $connection;
	
	private $errorMessage = "";
	private $errorCode = 0;
	private $rowsCount;
    
    private $mockMode = false;
    private $queriesPerRequest = 0;
	private $cacheResults = 'REQUEST';
	private $oldCacheStrategy = '';
	
	private $inTransaction = false;
  
	public function connect($hostname, $user, $passwd, $database){
		$this->connection = mysql_connect($hostname, $user, $passwd);
		mysql_query("use ".$database);
		echo mysql_error();
		mysql_set_charset("utf8"); 
		
		if ($this->connection) {
			$this->isOpened = true;
		} else {
		    $this->isOpened = true;
		}
		
		return $this->isOpened;
	}
	
	public function disconnect(){
		if(self::inTransaction()) {
			self::commit();
		}
		if(self::isOpened()) {
			mysql_close($this->connection);
		}
	}
	
	public function transaction(){
		if(!self::inTransaction()) {
			$sql = "start transaction";
			$this->inTransaction = true;
			return $this->execute($sql);  	
		}
	}
	
	public function commit(){
		if(self::inTransaction()) {
			$sql = "commit";
			$this->inTransaction = false;
			return $this->execute($sql); 
		}
	}
	
	public function rollback(){
		if(self::inTransaction()) {
			$sql = "rollback";
			$this->inTransaction = false;
			return $this->execute($sql); 
		}
	}
	
	public function inTransaction() {
		return $this->inTransaction;
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
    	global $webObject;
    	$this->queriesPerRequest ++;
		
		$this->errorMessage = '';
		$this->errorCode = 0;
			
		if($this->isOpened) {
			if($showQuery || $this->mockMode) {
				$pquery = str_replace('<', '&lt;', str_replace('>', '&gt;', $query));
				if($forceImmediateOutput || $this->mockMode) {
					echo "<div style=\"border: 2px solid gray; padding: 5px; margin: 5px;\"><strong style=\"color: red;\">SQL query:</strong><br /><code>".$pquery."</code></div>";
				} else {
					$webObject->PageLog .= "<div style=\"border: 2px solid gray; padding: 5px; margin: 5px;\"><strong style=\"color: red;\">SQL query:</strong><br /><code>".$pquery."</code></div>";
				}
			}
			  
			if(!$notExecuteQuery && !$this->mockMode) {
				$result = @mysql_query($query);
				
				self::tryToProcessError($query);
			}
			
			return ;
		} else {
			trigger_error("Connection is closed, can't execute query!", E_USER_WARNING);
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
    	global $webObject;
		global $requestStorage;
		
		if($this->isOpened) {
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
				if($this->cacheResults == 'REQUEST') {
					if($requestStorage->exists($hashQuery, 'database-cache')) {
						$return = $requestStorage->get($hashQuery, 'database-cache');
						return $return;
					}
				} else {
					$this->queriesPerRequest ++;
					$result = mysql_query($query);
				}
  				
  				self::tryToProcessError($query);
  				
				if($this->errorCode == 0) {
					$this->rowsCount = mysql_num_rows($result);
					while($row = mysql_fetch_assoc($result)) {
						$return[] = $row;
					}
				}
			}
			
			if($this->errorCode == 0) {
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
					$requestStorage->set($hashQuery, $return, 'database-cache');
				}
				return $return;
			} else {
				return array();
			}
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
  
	
	private function tryToProcessError($query) {
    	global $logObject;
		
		$this->errorCode = mysql_errno();
		if($this->errorCode != 0) {
			$this->errorMessage = mysql_error();
			
			if(is_object($logObject)) {
				$logObject->write('Mysql query error! ERRNO = '.$this->errorCode.', ERRORMSG = '.$this->errorMesssage.', QUERY = '.$query.'');
			} else {
				echo 'Mysql query error! ERRNO = '.$this->errorCode.', ERRORMSG = '.$this->errorMesssage.', QUERY = '.$query.'';
			}
		}
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
	public function isOpened() {
		return $this->isOpened;
	}
	
	/**
	 *
	 *	Returns queriesPerRequest.		 
	 *
	 */		 		 		
	public function getQueriesPerRequest() {
		return $this->queriesPerRequest;
	}
	
	/*
	* funkce pro získání naposledy vkládaného ID
	*/
	public function getLastId() {
		return mysql_insert_id($this->connection);
	}
  
	/**
	 * funkce pro vrácení erroru
	 */
	public function getErrorMessage(){
		return $this->errorMessage;
	}
  
	/**
	 * funkce pro vrácení erroru
	 */
	public function getErrorCode(){
		return $this->errorCode;
	}
  
	/**
	 * funkce pro získání poctu rádku
	 */
	public function getRowsCount(){
		return $this->rowsCount;
	}
	
	
	
    
	public function setCacheResults($val) {
		$this->cacheResults = $val;
	}
	
	public function disableCache() {
		$this->oldCacheStrategy = $this->cacheResults;
		$this->cacheResults = 'NONE';
	}
	
	public function enableCache() {
		$this->cacheResults = $this->oldCacheStrategy;
	}
}

?>
