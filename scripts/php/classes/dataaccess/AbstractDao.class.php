<?php

require_once('Select.class.php');

/*
 * abstraktní třída pro vytváření dotazů do DB
 */
abstract class AbstractDao {

	protected $dataAccess;
	
	protected $insertSQL = "INSERT INTO `{0}`.`{1}` ({2}) VALUES ({3});"; //INSERT INTO `destinace`.`information` (`infoLanguage`, `destinationId`, `name`, `type`, `description`, `email`, `www`, `phone`, `userId`) VALUES ('en', '7', 'Prvni', 'pokus', 'jkangiroa jaklg jril', NULL, NULL, NULL, '1'); 
	protected $deleteSQL = "DELETE FROM `{0}`.`{1}` WHERE `{1}`.`{2}` = '{3}';";
	protected $updateSQL = "UPDATE `{0}`.`{1}` SET  {2} WHERE  `{1}`.`{3}` = {4};"; //UPDATE  `destinace`.`comment` SET  `text` =  '�iljhgftrgh',`userCommentId` =  '2' WHERE  `comment`.`commentId` =2;
	protected $selectSQL = "SELECT {0} FROM `{1}`.`{2}` as `{3}` ";
	
	abstract public static function getTableName();
	abstract public static function getTableAlias();
	abstract public static function getFields();
	abstract public static function getIdField();
	
	protected function getDatabase() {
		return WEB_DB_DATABASE;
	}
	
	/*
	 * funkce vytváří string pro insert do DB
	 * 
	 * @param object - objekt vkládaný do db
	 */
	protected function insertSql($object){
		$fields = self::arrayToString(array_keys($object));
		$values = self::arrayToString($object, true);
		$pom = array($this->getDatabase(), $this->getTableName(), $fields, $values);
		
		return self::setString($this->insertSQL, $pom);
	}
	
	/*
	 * funkce vytváří string pro update do DB
	 * 
	 * @param object - objekt, který se má updetovat
	 */
	protected function updateSql($object){
		$set = "";
		$count = count($this->getFields());
		$fields = $this->getFields();
		$values = $object;
		for($i = 0; $i < $count; $i++) {
			if($fields[$i] != $this->getIdField()) {
				$set = $set . "`" . $fields[$i] . "` = '" . mysql_real_escape_string($values[$fields[$i]]);
				if ($i == $count - 1){
					$set = $set . "'";
				} else {
					$set = $set . "', ";
				}
			}
		}
		$param = array($this->getDatabase(), $this->getTableName(), $set, $this->getIdField(), mysql_real_escape_string($object[$this->getIdField()]));
		return self::setString($this->updateSQL, $param);
	}
	
	/*
	 * funkce pro vytvoření stringu pro delete
	 * 
	 * @param id - id mazaného objektu
	 */
	protected function deleteSql($id){
		$param = array($this->getDatabase(), $this->getTableName(), $this->getIdField(), mysql_real_escape_string($id));
		return self::setString($this->deleteSQL, $param);		
	}
	
	/*
	 * funkce pro vytvoření stringu pro select
	 * 
	 * @param fields - pole parametru
	 * @param select - pridavany string (where, ...)
	 */
	protected function selectSql($select = null, $distinct = false, $fields = null) {
		if ($fields == null || count($fields) == 0){
			if ($distinct == true){
				$params = array( "DISTINCT *", $this->getDatabase(), $this->getTableName());
			} else {
				$params = array( '`'.$this->getTableAlias().'`.*', $this->getDatabase(), $this->getTableName(), $this->getTableAlias());
			}
			$return = self::setString($this->selectSQL, $params);	
		} else {
			if ($distinct == true){
				$fieldsString = "DISTINCT ";
			}else {
				$fieldsString = "";
			}
			for($i = 0; $i < count($fields); $i++ ){
				if ($i == count($fields)-1){
					$fieldsString .= '`'.$this->getTableAlias().'`.`' . $fields[$i] . '`';
				} else {
					$fieldsString .= '`'.$this->getTableAlias().'`.`' . $fields[$i] . '`, ';
				}
				
				$params = array( $fieldsString, $this->getDatabase(), $this->getTableName(), $this->getTableAlias());
				$return = self::setString($this->selectSQL, $params); 
			}
		}		 
		
		if ($select == null){
			return $return . ";";
		} else {
			return $return . $select . ";";
		}
	}
	
	
	
	
	
	public function insert($data) {
		$sql = self::insertSql($data);
		
		$this->dataAccess->transaction();
		$this->dataAccess->execute($sql);
		$this->dataAccess->commit();
		return $this->dataAccess->getErrorCode();
	}
	
	public function update($data) {
		$sql = self::updateSql($data);
		
		$this->dataAccess->transaction();
		$this->dataAccess->execute($sql);
		$this->dataAccess->commit();
		return $this->dataAccess->getErrorCode();
	}
	
	public function delete($data) {
		if(is_array($data)) {
			$data = $data[$this->getIdField()];
		}
	
		$sql = self::deleteSql($data);
		
		$this->dataAccess->transaction();
		$this->dataAccess->execute($sql);
		$this->dataAccess->commit();
		return $this->dataAccess->getErrorCode();
	}
	
	public function select($select = null, $distinct = false, $fields = null) {
		$sql = self::selectSql($select, $distinct, $fields);
		return $this->dataAccess->fetchAll($sql);
	}
	
	public function selectSingle($select = null, $distinct = false, $fields = null) {
		$sql = self::selectSql($select, $distinct, $fields);
		return $this->dataAccess->fetchSingle($sql);
	}
	
	public function get($id) {
		$select = new Select();
		$select->where($this->getIdField(), '=', $id);
		
		$sql = self::selectSql($select->result(), true);
		return $this->dataAccess->fetchSingle($sql);
	}
	
	public function getList($selectObject = null, $distinct = false) {
		$sql = self::selectSql($selectObject != null ? $selectObject->result() : null, $distinct);
		return $this->dataAccess->fetchAll($sql);
	}
	
	public function setDataAccess($dataAccess) {
		$this->dataAccess = $dataAccess;
	}
	
	protected function dataAccess() {
		return $this->dataAccess;
	}
	
	public function getLastId() {
		$sql = 'select max(`'.$this->getIdField().'`) as `'.$this->getIdField().'` from `'.$this->getTableName().'`;';
		self::dataAccess()->disableCache();
		$result = self::dataAccess()->fetchSingle($sql);
		self::dataAccess()->enableCache();
		return $result[$this->getIdField()];
	}
  
	public function getErrorMessage(){
		return $this->dataAccess->getErrorMessage();
	}
	
	
	/*
	 * funkce pro naharazení {0-9} ve stringu parametry ({0} nahrazana params[0])
	 * 
	 * @param string - string s {}, keteré se mají nahradit
	 * @param params - pole parametru 
	 */
	private static function setString($string, $params){
		$positoin[0] = 0;
		$change = 0;
		for($i = 0; $i < strlen($string) - 2; $i++){
			if (substr($string, $i, 1) == '{' && substr($string, $i + 2, 1) == '}'){
				$change = substr($string, $i + 1, 1);
				$positoin[$change] = "{".$change."}";
			}
		}
		$return = str_replace($positoin, $params, $string);
		return $return;
	}
	
	/*
	 * funkce pro převedení pole na potřebný string
	 * 
	 * @param array - pole (názvů sloupcu v DB)
	 * @param values - pole (hodnot v sloupci v DB)
	 */
	private static function arrayToString($array, $values = false){
		$string = "";
		$count = count($array);
		$i = 0;
		foreach ($array as $feald){
			$i++;
			if (!$values){
				if ($i != $count){
					$string = $string . "`" . $feald . "`, ";
				} else {
					$string = $string . "`" . $feald . "`";
				}
			} else {
			if ($i != $count){
					$string = $string . "'" . mysql_real_escape_string($feald) . "', ";
				} else {
					$string = $string . "'" . mysql_real_escape_string($feald) . "'";
				}
			}
		}
		return $string;
	}
	
}

?>