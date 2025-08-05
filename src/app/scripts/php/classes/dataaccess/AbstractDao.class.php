<?php

require_once('Select.class.php');

/*
 * abstraktní třída pro vytváření dotazů do DB
 */
abstract class AbstractDao {

	protected $dataAccess;
	
	protected $insertSQL = "INSERT INTO `{0}`.`{1}` ({2}) VALUES ({3});"; //INSERT INTO `destinace`.`information` (`infoLanguage`, `destinationId`, `name`, `type`, `description`, `email`, `www`, `phone`, `userId`) VALUES ('en', '7', 'Prvni', 'pokus', 'jkangiroa jaklg jril', NULL, NULL, NULL, '1'); 
	protected $deleteSQL = "DELETE FROM `{0}`.`{1}` WHERE `{1}`.`{2}` = '{3}';";
	protected $updateSQL = "UPDATE `{0}`.`{1}` SET  {2} WHERE  `{1}`.`{3}` = '{4}';"; //UPDATE  `destinace`.`comment` SET  `text` =  '�iljhgftrgh',`userCommentId` =  '2' WHERE  `comment`.`commentId` =2;
	protected $selectSQL = "SELECT {0} FROM `{1}`.`{2}` as `{3}` ";
	protected $countSQL = "SELECT COUNT(*) as `count` FROM `{0}`.`{1}` as `{2}` ";
	
	abstract public static function getTableName();
	abstract public static function getTableAlias();
	abstract public static function getFields();
	abstract public static function getIdField();
	
	protected function getDatabase() {
		return WEB_DB_DATABASE;
	}

	protected function createSelect() {
		return new Select($this->dataAccess);
	}
	
	/*
	 * funkce vytváří string pro insert do DB
	 * 
	 * @param object - objekt vkládaný do db
	 */
	protected function insertSql($object){
		$fields = $this->arrayToString(array_keys($object));
		$values = $this->arrayToString($object, true);
		$pom = array($this->getDatabase(), $this->getTableName(), $fields, $values);
		
		return $this->setString($this->insertSQL, $pom);
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
			if($fields[$i] != $this->getIdField() && array_key_exists($fields[$i], $object)) {
				if ($set != '') {
					$set = $set . ", ";
				}

				$set = $set . "`" . $fields[$i] . "` = '" . $this->dataAccess->escape($values[$fields[$i]]) . "'";
			}
		}
		$param = array($this->getDatabase(), $this->getTableName(), $set, $this->getIdField(), $this->dataAccess->escape($object[$this->getIdField()]));
		return $this->setString($this->updateSQL, $param);
	}
	
	/*
	 * funkce pro vytvoření stringu pro delete
	 * 
	 * @param id - id mazaného objektu
	 */
	protected function deleteSql($id){
		$param = array($this->getDatabase(), $this->getTableName(), $this->getIdField(), $this->dataAccess->escape($id));
		return $this->setString($this->deleteSQL, $param);
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
				$params = array("DISTINCT *", $this->getDatabase(), $this->getTableName(), $this->getTableAlias());
			} else {
				$params = array('`'.$this->getTableAlias().'`.*', $this->getDatabase(), $this->getTableName(), $this->getTableAlias());
			}
			$return = $this->setString($this->selectSQL, $params);	
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
				
				$params = array($fieldsString, $this->getDatabase(), $this->getTableName(), $this->getTableAlias());
				$return = $this->setString($this->selectSQL, $params); 
			}
		}		 
		
		if ($select == null){
			return $return . ";";
		} else {
			return $return . $select . ";";
		}
	}

	protected function selectObjectToResult($selectObject) {
		return $selectObject != null ? $selectObject->tableAlias($this->getTableAlias())->result() : null;
	}

	protected function countSql($select = null) {
		$params = array($this->getDatabase(), $this->getTableName(), $this->getTableAlias());
		$return = $this->setString($this->countSQL, $params); 
		
		if ($select == null){
			return $return . ";";
		} else {
			return $return . $select . ";";
		}
	}
	
	public function insert($data) {
		$sql = $this->insertSql($data);

		$this->dataAccess->transaction(function() use ($sql) {
			$this->dataAccess->execute($sql);
		});

		return $this->dataAccess->getErrorCode();
	}
	
	public function update($data) {
		$sql = $this->updateSql($data);
		
		$this->dataAccess->transaction(function() use ($sql) {
			$this->dataAccess->execute($sql);
		});

		return $this->dataAccess->getErrorCode();
	}
	
	public function delete($data) {
		if (is_array($data)) {
			$data = $data[$this->getIdField()];
		}
	
		$sql = $this->deleteSql($data);

		$this->dataAccess->transaction(function() use ($sql) {
			$this->dataAccess->execute($sql);
		});

		return $this->dataAccess->getErrorCode();
	}
	
	public function select($selectObject = null, $distinct = false, $fields = null) {
		$sql = $this->selectSql($this->selectObjectToResult($selectObject), $distinct, $fields);
		return $this->dataAccess->fetchAll($sql);
	}
	
	public function selectSingle($selectObject = null, $distinct = false, $fields = null) {
		$sql = $this->selectSql($this->selectObjectToResult($selectObject), $distinct, $fields);
		return $this->dataAccess->fetchSingle($sql);
	}
	
	public function get($id) {
		$select = $this->createSelect();
		$select->tableAlias($this->getTableAlias());
		$idField = $this->getIdField();
		if(is_array($idField)) {
			$isFirst = true;
			foreach($idField as $field) {
				if($isFirst) {
					$select->where($field, '=', $id[$field]);
					$isFirst = false;
				} else {
					$select->conjunct($field, '=', $id[$field]);
				}
			}
		} else {
			$select->where($this->getIdField(), '=', $id);
		}
		
		$sql = $this->selectSql($select->result(), true);
		return $this->dataAccess->fetchSingle($sql);
	}
	
	public function getList($selectObject = null, $distinct = false) {
		$sql = $this->selectSql($this->selectObjectToResult($selectObject), $distinct);
		return $this->dataAccess->fetchAll($sql);
	}

	public function count($selectObject = null) {
		$sql = $this->countSql($this->selectObjectToResult($selectObject));
		$data = $this->dataAccess->fetchSingle($sql);
		return $data['count'];
	}

	public function exists($selectObject = null) {
		return $this->count($selectObject) > 0;
	}
	
	public function setDataAccess($dataAccess) {
		$this->dataAccess = $dataAccess;
	}
	
	protected function dataAccess() {
		return $this->dataAccess;
	}

	protected function sql() {
		return new SqlBuilder($this->dataAccess());
	}
	
	public function getLastId() {
		$sql = 'select max(`'.$this->getIdField().'`) as `'.$this->getIdField().'` from `'.$this->getTableName().'`;';
		$this->dataAccess()->disableCache();
		$result = $this->dataAccess()->fetchSingle($sql);
		$this->dataAccess()->enableCache();
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
	private function setString($string, $params){
		$position[0] = 0;
		$change = 0;
		for($i = 0; $i < strlen($string) - 2; $i++){
			if (substr($string, $i, 1) == '{' && substr($string, $i + 2, 1) == '}'){
				$change = substr($string, $i + 1, 1);
				$position[$change] = "{".$change."}";
			}
		}
		$return = str_replace($position, $params, $string);
		return $return;
	}
	
	/*
	 * funkce pro převedení pole na potřebný string
	 * 
	 * @param array - pole (názvů sloupcu v DB)
	 * @param values - pole (hodnot v sloupci v DB)
	 */
	private function arrayToString($array, $values = false){
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
					$string = $string . "'" . $this->dataAccess->escape($feald) . "', ";
				} else {
					$string = $string . "'" . $this->dataAccess->escape($feald) . "'";
				}
			}
		}
		return $string;
	}
	
}

?>