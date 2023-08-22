<?php

	require_once("BaseTagLib.class.php");
	require_once(APP_SCRIPTS_PHP_PATH . "classes/dataaccess/DataAccess.class.php");

	/**
	 *             
	 *  Class for working Database.
	 *  
	 *  @author     Marek SMM
	 *  @timestamp  2010-07-25
	 *   
	 */              
	class Database extends BaseTagLib {

		private $dataAccess = null;

		/**
		 *
		 *  Constructor connects to db using default db values.
		 *  If is set attribute defaultLogin to false, it does'nt connect.
		 *  
		 *  @param  defaultLogin  if true, it connects, if false, it does'nt
		 *
		 */                   
		public function __construct($defaultLogin = true) {
			$this->dataAccess = new DataAccess();

			if ($defaultLogin) {
				$this->dataAccess->connect(WEB_DB_HOSTNAME, WEB_DB_USER, WEB_DB_PASSWORD, WEB_DB_DATABASE);
			}
		}

		public function setCacheResults($val) {
			$this->dataAccess->setCacheResults($val);
		}

		public function disableCache() {
			$this->dataAccess->disableCache();
		}

		public function enableCache() {
			$this->dataAccess->enableCache();
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
			$this->dataAccess->connect($hostname, $user, $password, $dbname);
		}

		/**
		 *
		 *  Closes connection with database.
		 *  
		 *  @return none;          
		 *
		 */                   
		public function close() {
			$this->dataAccess->disconnect();
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
			if ($name == 'default') {
				$this->close();
				$this->connect(WEB_DB_HOSTNAME, WEB_DB_USER, WEB_DB_PASSWORD, WEB_DB_DATABASE);
			} elseif (strlen($name) != 0) {
				$conn = $this->fetchSingle('select `hostname`, `user`, `password`, `database`, `fs_root` from `db_connection` where `name` = "'.$name.'"');
				if($conn != array()) {
					$this->close();
					$this->connect($conn['hostname'], $conn['user'], $conn['password'], $conn['database']);
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
			$this->dataAccess->execute($query, $showQuery, $forceImmediateOutput, $notExecuteQuery);
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
		 *  @return array of all rows fetched by database
		 *
		 */                   
		public function fetchAll($query, $showQuery = false, $printOutput = false, $forceImmediateOutput = false, $notExecuteQuery = false) {
			return $this->dataAccess->fetchAll($query, $showQuery, $printOutput, $forceImmediateOutput, $notExecuteQuery);
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
			return $this->dataAccess->fetchSingle($query, $showQuery, $printOutput, $forceImmediateOutput, $notExecuteQuery);
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
			if ($this->IsOpen) {
				$data = $this->fetchAll($query);
			
				if ($template) {
					if (is_file(APP_SCRIPTS_PHP_PATH . $template)) {
						$cont = file_get_contents(APP_SCRIPTS_PHP_PATH . $template);
				
						$return = "";
						foreach ($data as $tr) {
							foreach ($tr as $tname => $td) {
								$return .= str_replace("<tpl:" . $tname . " />", $td, $cont);
							}
						}
				
						return $return;
					} else {
						trigger_error("Template file doesn't exist!", E_USER_WARNING);
					}
				} else {
					$return = "<table class=\"database-table\">";
					$i = 0;
					foreach ($data as $tr) {
						$return .= "<tr class=\"database-tr tr-".$i."\">";
						$j = 0;
						foreach ($tr as $tname => $td) {
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
			return $this->dataAccess->getLastId();
		}
			
		/***
		 *
		 *	Sets mockmode, means that none of calls to execute executes and fetchAll shows query and result.
			*
			*/		 		 		 		
		public function setMockMode($enabled) {
			$this->dataAccess->setMockMode($enabled);
		}

		/**
		 *
		 *  Returns true if connection with database is opened, false otherwise.
		 *  
		 *  @return returns true if connection with database is opened, false otherwise     		 
		 *
		 */               		
		public function isOpen() {
			return $this->dataAccess->isOpened();
		}

		/**
		 *
		 *	Returns queriesPerRequest.		 
			*
			*/		 		 		
		public function getQueriesPerRequest() {
			return $this->dataAccess->getQueriesPerRequest();
		}

		public function getDataAccess() {
			return $this->dataAccess;
		}

		public function escape($value) {
			return $this->dataAccess->escape($value);
		}
	}

?>
