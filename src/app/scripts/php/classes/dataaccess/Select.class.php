<?php

/*
 * třída pro vytvoření stringu pro select
 */
class Select{
	private $dataAccess;
	private $result;
	private $tableAlias;
	private $alias;

	public function __construct($dataAccess) {
		$this->dataAccess = $dataAccess;
	}

	public function tableAlias($tableAlias) {
		$this->tableAlias = $tableAlias;
		$this->alias = $this->tableAlias != '' ? '`' . $this->tableAlias . '`' : '';
	}
	
	/*
	 * funkce pro vytvoření klauzule where
	 * 
	 * @param column - jméno sloupce
	 * @param comp - =, >, <, ....
	 * @param value - porovnavaná hodnota
	 * @param ignor - má se ignorovat, nebo ne
	 */
	public function where($column, $comp, $value, $ignore=false){
		if (!$ignore){

			if ($comp == "IN" || $comp == "NOT IN"){
				if(is_array($value) && count($value) > 0) {
					$value = "'".implode("', '", $value)."'";
				} else {
					$value = $this->dataAccess->escape($value);
				}
			
				$this->result .= " WHERE " . $this->alias . ".`" . $column . "` " . $comp . " (" . $value . ")";
			} else {
				$this->result .= " WHERE " . $this->alias . ".`" . $column . "` " . $comp . " '" . $this->dataAccess->escape($value) . "'";
			}
		}
		
		return $this;
	}
	/*
	 * funkce pro vytvoření klauzule or
	 * 
	 * @param column - jméno sloupce
	 * @param comp - =, >, <, ....
	 * @param value - porovnavaná hodnota
	 * @param ignor - má se ignorovat, nebo ne
	 */
	public function disjunct($column, $comp, $value, $ignore=false){
		if (!$ignore){
			if ($this->result != null){
				$this->result .= " OR " . $this->alias . ".`" . $column . "` " . $comp . " " . "'" . $this->dataAccess->escape($value) . "'";
			}
		}
		
		return $this;
	}
	/*
	 * funkce pro vytvoření klauzule and
	 * 
	 * @param column - jméno sloupce
	 * @param comp - =, >, <, ....
	 * @param value - porovnavaná hodnota
	 * @param ignor - má se ignorovat, nebo ne
	 */
	public function conjunct($column, $comp, $value, $ignore=false){
		if (!$ignore){
			if ($this->result != null){
				$this->result .= " AND " . $this->alias . ".`" . $column . "` " . $comp . " " . "'" . $this->dataAccess->escape($value) . "'";
			}
		}
		
		return $this;
	}
	/*
	 * funkce pro vytvoření klauzule and ve formátu $column in ($value).
	 * 
	 * @param column - jméno sloupce
	 * @param values - porovnavaná hodnota
	 * @param ignor - má se ignorovat, nebo ne
	 */
	public function conjunctIn($column, $values, $ignore=false){
		if (!$ignore){
			if ($this->result != null){
				if(count($values) == 0) {
					$values = array(-1);
				}

				$value = implode("','", $values);
				$this->result .= " AND " . $this->alias . ".`" . $column . "` IN ('" .$value."')";
			}
		}
		
		return $this;
	}
	/*
	 * funkce pro vytvoření klauzule order by
	 * 
	 * @param column - jméno sloupce
	 * @param how - ASC nebo DESC
	 * @param ignor - má se ignorovat, nebo ne
	 */
	public function orderBy($column, $how=null, $ignore=false){
		if (!$ignore){			
			$this->result .= " ORDER BY ";
			if (is_array($column)){
				$end = count($column);
				for ($i = 0; $i < $end; $i++){
					if ($i == $end - 1){
						$this->result .= $this->alias . ".`" . $column[$i] . "`";
					} else {
						$this->result .= $this->alias . ".`" . $column[$i] . "`, ";
					}
				}
			} else {
				$this->result .= $column . " ";
			}
			if ($how != null){
				$this->result .= $how;
			}
		}
		
		return $this;		
	}
	/*
	 * funkce pro vytvoření klauzule limit
	 * 
	 * @param start - začátek/počet řádků od začátku
	 * @param end - konec
	 * @param ignore - má se ignorovat, nebo ne
	 */
	public function limit($start, $end = null, $ignore = false){
		if (!$ignore){
			$this->result .= " LIMIT " . $this->dataAccess->escape($start);
			if ($end != null){
				$this->result .= "," . $this->dataAccess->escape($end); 
			}
		}
		
		return $this;
	}
	/*
	 * join
	 * 
	 * @param $dao2 - 2.tabulka (nazev)
	 * @param field2 - nazev sloupecku 2.tabulky  
	 * @param $dao1 -  puvodni tabulka (nazev)
	 * @param field1 - nazev sloupecku puvodni tabulky
	 * @param type - typ joinu (LEFT, ...)
	 * @param ignore - má se ignorovat, nebo ne
	 */
	public function join($dao1, $dao2, $type, $field1 = null, $field2 = null, $ignore = false){
		if (!$ignore){
			if ($field1 == null && $field2 == null){
				$this->result .=  /*mb_convert_case($type, "MB_CASE_UPPER")*/" " . $type . " JOIN `" . WEB_DB_DATABASE . "`.`" . $dao2->getTableName() . "` ON `" . WEB_DB_DATABASE . "`.`" . $dao1->getTableAlias() . "`.`" . $dao1->getIdField() .  "` = `" . WEB_DB_DATABASE . "`.`" . $dao2->getTableName() . "`.`" . $dao2->getIdField() . "`";	
			} else if ($field1 == null){
				$this->result .=  /*mb_convert_case($type, "MB_CASE_UPPER")*/" " . $type . " JOIN `" . WEB_DB_DATABASE . "`.`" . $dao2->getTableName() . "` ON `" . WEB_DB_DATABASE . "`.`" . $dao1->getTableAlias() . "`.`" . $dao1->getIdField() .  "` = `" . WEB_DB_DATABASE . "`.`" . $dao2->getTableName() . "`.`" . $field2 . "`";
			} else if ($field2 == null){
				$this->result .=  /*mb_convert_case($type, "MB_CASE_UPPER")*/" " . $type . " JOIN `" . WEB_DB_DATABASE . "`.`" . $dao2->getTableName() . "` ON `" . WEB_DB_DATABASE . "`.`" . $dao1->getTableAlias() . "`.`" . $field1 .  "` = `" . WEB_DB_DATABASE . "`.`" . $dao2->getTableName() . "`.`" . $dao2->getIdField() . "`";
			} else {
				$this->result .=  /*mb_convert_case($type, "MB_CASE_UPPER")*/" " . $type . " JOIN `" . WEB_DB_DATABASE . "`.`" . $dao2->getTableName() . "` ON `" . WEB_DB_DATABASE . "`.`" . $dao1->getTableAlias() . "`.`" . $field1 .  "` = `" . WEB_DB_DATABASE . "`.`" . $dao2->getTableName() . "`.`" . $field2 . "`";
			}
		}
		
		return $this;
	}
	
	public function groupBy($column, $ignore=false){
		if (!$ignore){
			$this->result .= " GROUP BY " . $column; 
		}
		
		return $this;
	}
	/*
	 * funkce vrátí vytvořený string	 * 
	 */
	public function result(){
		return $this->result;
	}
	/*
	 * funkce smaže nastavený string
	 */
	public function reset(){
		$this->result = "";
		
		return $this;
	}
	
	
	public static function factory($dataAccess) {
		return new Select($dataAccess);
	}
}

?>