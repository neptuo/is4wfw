<?php

/*
 * třída pro vytvoření stringu pro select
 */
class Select{
	private $result;
	
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
					$value = mysql_real_escape_string($value);
				}
			
				$this->result .= " WHERE `" . $column . "` " . $comp . " (".$value.")";
			} else {
				$this->result .= " WHERE `" . $column . "` " . $comp . " '".mysql_real_escape_string($value)."'";
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
				$this->result .= " OR `" . $column . "` " . $comp . " " . "'".mysql_real_escape_string($value)."'";
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
				$this->result .= " AND " . $column . " " . $comp . " " . "'".mysql_real_escape_string($value)."'";
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
				$this->result .= " AND " . $column . " IN ('" .$value."')";
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
						$this->result .= $column[$i];
					} else {
						$this->result .= $column[$i] . ", ";
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
			$this->result .= " LIMIT " . mysql_real_escape_string($start);
			if ($end != null){
				$this->result .= "," . mysql_real_escape_string($end); 
			}
		}
		
		return $this;
	}
	/*
	 * join
	 * 
	 * @param $dao2 - 2.tabulka (nazev)
	 * @param field2 - nazev sloupecku 2.tabulky  
	 * @param $dao -  puvodni tabulka (nazev)
	 * @param field1 - nazev sloupecku puvodni tabulky
	 * @param type - typ joinu (LEFT, ...)
	 * @param ignore - má se ignorovat, nebo ne
	 */
	public function join($dao2, $dao, $type, $field1 = null, $field2 = null, $ignore = false){
		if (!$ignore){
			if ($field1 == null && $field2 == null){
				$this->result .=  /*mb_convert_case($type, "MB_CASE_UPPER")*/" " . $type . " JOIN `" . DATABAZE . "`.`" . $dao->getTableName() . "` ON `" . DATABAZE . "`.`" . $dao->getTableName() . "`.`" . $dao->getIdField() .  "` = `" . DATABAZE . "`.`" . $dao2->getTableName() . "`.`" . $dao->getIdField() . "`";	
			} else if ($field1 == null){
				$this->result .=  /*mb_convert_case($type, "MB_CASE_UPPER")*/" " . $type . " JOIN `" . DATABAZE . "`.`" . $dao->getTableName() . "` ON `" . DATABAZE . "`.`" . $dao->getTableName() . "`.`" . $dao->getIdField() .  "` = `" . DATABAZE . "`.`" . $dao2->getTableName() . "`.`" . $field2 . "`";
			} else if ($field2 == null){
				$this->result .=  /*mb_convert_case($type, "MB_CASE_UPPER")*/" " . $type . " JOIN `" . DATABAZE . "`.`" . $dao->getTableName() . "` ON `" . DATABAZE . "`.`" . $dao->getTableName() . "`.`" . $field1 .  "` = `" . DATABAZE . "`.`" . $dao2->getTableName() . "`.`" . $dao2->getIdField() . "`";
			} else {
				$this->result .=  /*mb_convert_case($type, "MB_CASE_UPPER")*/" " . $type . " JOIN `" . DATABAZE . "`.`" . $dao->getTableName() . "` ON `" . DATABAZE . "`.`" . $dao->getTableName() . "`.`" . $field1 .  "` = `" . DATABAZE . "`.`" . $dao2->getTableName() . "`.`" . $field2 . "`";
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
	
	
	public static function factory() {
		return new Select();
	}
}

?>