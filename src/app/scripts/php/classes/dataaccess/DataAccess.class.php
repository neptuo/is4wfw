<?php

require_once(APP_SCRIPTS_PHP_PATH . "classes/manager/SystemProperty.class.php");
require_once(APP_SCRIPTS_PHP_PATH . "classes/dataaccess/DataAccessException.class.php");
require_once(APP_SCRIPTS_PHP_PATH . "classes/utils/ArrayUtils.class.php");

class DataAccess {
	public static $CharsetSystemProperty = 'DataAccess.Charset';
	public static $SqlModeSystemProperty = 'DataAccess.SetSqlMode';

	private $isOpened = false;
	private $connection;
	
	private $errorMessage = "";
	private $errorCode = 0;
	private $rowsCount;
    
    private $mockMode = false;
    private $queriesPerRequest = 0;
	private $cacheResults = 'REQUEST';
	private $oldCacheStrategy = '';

	private $saveQueries = false;
	private $measureQueries = false;

	private $queries = array();
	private $measures = array();
	
	private $inTransaction = false;
  
	public function connect($hostname, $user, $passwd, $database, $checkCharset = true, $checkSqlMode = true){
		$this->connection = mysqli_connect($hostname, $user, $passwd);
		$query = "use " . $database;
		mysqli_query($this->connection, $query);
		$this->tryToProcessError($query);
		
		if ($this->connection) {
			$this->isOpened = true;
		} else {
		    $this->isOpened = true;
		}

		if ($checkCharset) {
			$this->checkCharset();
		}

		if ($checkSqlMode) {
			$this->ensureSqlMode();
		}
		
		return $this->isOpened;
	}

	private function checkCharset() {
		if ($this->isOpened) {
			$property = new SystemProperty($this);
			$charset = $property->getValue(DataAccess::$CharsetSystemProperty);
			if ($charset != null && strlen($charset) > 0) {
				mysqli_set_charset($this->connection, $charset);
			}
		}
	}

	private function ensureSqlMode() {
		if ($this->isOpened) {
			$property = new SystemProperty($this);
			$value = $property->getValue(DataAccess::$SqlModeSystemProperty);
			if ($value == true) {
				mysqli_query($this->connection, "SET sql_mode = '';");
			}
		}
	}
	
	public function disconnect(){
		if ($this->inTransaction()) {
			$this->commit();
		}
		if ($this->isOpened()) {
			mysqli_close($this->connection);
		}
	}
	
	private function startTransaction() {
		if (!$this->inTransaction()) {
			$sql = "start transaction";
			$this->inTransaction = true;
			return $this->execute($sql);  	
		}

		return false;
	}

	public function transaction($handler = NULL){
		$isStarted = $this->startTransaction();
		if ($handler == NULL) {
			return $isStarted;
		} else {
			if (!$isStarted) {
				$handler($this);
				return $isStarted;
			} else {
				try {
					$handler($this);
					$this->commit();
				} catch (Exception $e) {
					$this->rollback();
					throw $e;
				}
			}
		}
		
	}
	
	public function commit(){
		if ($this->inTransaction()) {
			$sql = "commit";
			$this->inTransaction = false;
			return $this->execute($sql); 
		}
	}
	
	public function rollback(){
		if ($this->inTransaction()) {
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
			
		if ($this->isOpened) {
			if ($showQuery || $this->mockMode) {
				$pquery = str_replace('<', '&lt;', str_replace('>', '&gt;', $query));
				if ($forceImmediateOutput || $this->mockMode) {
					echo "<div style=\"border: 2px solid gray; padding: 5px; margin: 5px;\"><strong style=\"color: red;\">SQL query:</strong><br /><code>".$pquery."</code></div>";
				} else {
					$webObject->PageLog .= "<div style=\"border: 2px solid gray; padding: 5px; margin: 5px;\"><strong style=\"color: red;\">SQL query:</strong><br /><code>".$pquery."</code></div>";
				}
			} 
			
			$result = false;
			if (!$notExecuteQuery && !$this->mockMode) {
				if ($this->saveQueries) {
					array_push($this->queries, $query);
				}
				
				$startTime = 0;
				if ($this->measureQueries) {
					$startTime = $this->getCurretMillitime();
				}

				// Execute the query.
				$result = @mysqli_query($this->connection, $query);
				
				if ($this->measureQueries) {
					$endTime = $this->getCurretMillitime();
					array_push($this->measures, $endTime - $startTime);
				}

				$this->tryToProcessError($query);
			}
			
			return $result;
		} else {
			trigger_error("Connection is closed, can't execute query!", E_USER_WARNING);
		}
	}

	private function getCurretMillitime() {
		$microtime = microtime(true);
		return floor($microtime * 1000);
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
     *  @return array all rows fetched by database
     *
     */                   
    public function fetchAll($query, $showQuery = false, $printOutput = false, $forceImmediateOutput = false, $notExecuteQuery = false) {
    	global $webObject;
		global $requestStorage;
		
		if ($this->isOpened) {
			if ($showQuery || $this->mockMode) {
				if ($forceImmediateOutput || $this->mockMode) {
					echo "<div style=\"border: 2px solid gray; padding: 5px; margin: 5px;\"><strong style=\"color: red;\">SQL query:</strong><br /><code>".$query."</code></div>";
			  	} else {
					$webObject->PageLog .= "<div style=\"border: 2px solid gray; padding: 5px; margin: 5px;\"><strong style=\"color: red;\">SQL query:</strong><br /><code>".$query."</code></div>";
				}
			} 
			  
			$hashQuery = '';
			if (!$notExecuteQuery) {
				$return = array();
				$result = array();
				$hashQuery = sha1($query);
				if($this->cacheResults == 'REQUEST' && $requestStorage != null) {
					if($requestStorage->exists($hashQuery, 'database-cache')) {
						$return = $requestStorage->get($hashQuery, 'database-cache');
						return $return;
					}
				} 
				
				$this->queriesPerRequest ++;
				if ($this->saveQueries) {
					array_push($this->queries, $query);
				}
				
				$startTime = 0;
				if ($this->measureQueries) {
					$startTime = microtime();
				}

				// Execute the query.
				$result = mysqli_query($this->connection, $query);
  				
				$this->tryToProcessError($query);
  				
				if ($this->errorCode == 0) {
					$this->rowsCount = mysqli_num_rows($result);
					while($row = mysqli_fetch_assoc($result)) {
						$return[] = $row;
					}
				}
				
				if ($this->measureQueries) {
					$endTime = microtime();
					array_push($this->measures, max($endTime - $startTime, 0));
				}
			}
			
			if ($this->errorCode == 0) {
				if ($printOutput || $this->mockMode) {
					if ($forceImmediateOutput || $this->mockMode) {
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
				
				if ($this->cacheResults == 'REQUEST' && $requestStorage != null) {
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
     *  @return array all rows fetched by database
     *
     */                   
    public function fetchSingle($query, $showQuery = false, $printOutput = false, $forceImmediateOutput = false, $notExecuteQuery = false) {
    	$data = $this->fetchAll($query, $showQuery, $printOutput, $forceImmediateOutput, $notExecuteQuery);
    	if (count($data) > 0) {
    		return $data[0];
    	} else {
    		return array();
    	}
	}
	
	public function fetchScalar($query, $showQuery = false, $printOutput = false, $forceImmediateOutput = false, $notExecuteQuery = false) {
		$data = $this->fetchSingle($query, $showQuery, $printOutput, $forceImmediateOutput, $notExecuteQuery);
		$key = ArrayUtils::firstKey($data);
		return $data[$key];
	}
  
	
	private function tryToProcessError($query) {
    	global $logObject;
		
		$this->errorCode = mysqli_errno($this->connection);
		if ($this->errorCode != 0) {
			$this->errorMessage = mysqli_error($this->connection);
			throw new DataAccessException($this->errorCode, $this->errorMessage, $query);
		}
	}
		
	/***
	 *
	 *	Sets mockmode, means that none of calls to execute executes and fetchAll shows query and result.
	 *
	 */		 		 		 		
	public function setMockMode($enabled) {
		if ($enabled == true) {
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
	
	public function getLastId() {
		return mysqli_insert_id($this->connection);
	}
  
	public function getErrorMessage(){
		return $this->errorMessage;
	}
  
	public function getErrorCode(){
		return $this->errorCode;
	}
  
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

	public function getCharset() {
		return mysqli_character_set_name($this->connection);
	}
	
	public function setCharset($value) {
		mysqli_set_charset($this->connection, $value);
	}

	public function escape($value) {
		return mysqli_real_escape_string($this->connection, $value);
	}

	public function saveQueries($value) {
		$this->saveQueries = $value;
	}

	public function getQueries() {
		return $this->queries;
	}

	public function saveProfiles($value) {
		if ($value) {
			$this->execute("SET PROFILING_HISTORY_SIZE=100");
			$this->execute("SET PROFILING=1");
		} else {
			$this->execute("SET PROFILING=0");
		}
	}

	public function saveMeasures($value) {
		$this->measureQueries = $value;
	}

	public function getMeasures() {
		return $this->measures;
	}

	public function getProfiles() {
		$oldSaveQueries = $this->saveQueries;
		$oldMeasureQueries = $this->measureQueries;
		$this->saveQueries(false);
		$this->saveMeasures(false);
		$profiles = $this->fetchAll("SHOW PROFILES");
		$this->saveQueries($oldSaveQueries);
		$this->saveMeasures($oldMeasureQueries);
		return $profiles;
	}
}

?>
