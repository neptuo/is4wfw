<?php

    require_once(APP_SCRIPTS_PHP_PATH . "classes/utils/StringUtils.class.php");

    class SqlBuilder {
        private $dataAccess;

        public function __construct($dataAccess) {
            $this->dataAccess = $dataAccess;
        }

        private function field($tableAlias, $field) {
            if (empty($tableAlias)) {
                return "`$field`";
            }

            return "$tableAlias.`$field`";
        }

        public function escape($value) {
            if ($value === null) {
                $value = "NULL";
            } else if (is_string($value)) {
                $value = "'" . $this->dataAccess->escape($value) . "'";
            } else if (is_bool($value)) {
                $value = $value ? 1 : 0;
            }

            return $value;
        }

        private function condition($filter, $operator = "AND ", $tableAlias = null) {
            if (is_string($filter)) {
                return $filter;
            }

            $result = "";

            foreach ($filter as $field => $value) {
                $assignValue = null;
                $name = $this->field($tableAlias, $field);
                if (is_array($value)) {
                    $valueString = "";
                    foreach ($value as $item) {
                        if (!empty($item)) {
                            $valueString = StringUtils::join($valueString, $this->escape($item));
                        }
                    }

                    if (!empty($valueString)) {
                        $assignValue = " IN ($valueString)";
                    }
                } else {
                    $value = $this->escape($value);

                    if ($field != "") {
                        $assignValue = " = $value";
                    } else {
                        $assignValue = "$value";
                    }
                }

                if ($assignValue != null) {
                    $result = StringUtils::join($result, "$name$assignValue", " " . $operator);
                }
            }

            return $result;
        }
        
        private function appendWhere($value) {
            if (!empty($value)) {
                $value = " WHERE $value";
            }
            
            return $value;
        }
        
        private function appendHaving($value) {
            if (!empty($value)) {
                $value = " HAVING $value";
            }
            
            return $value;
        }

        private function groupBy($tableAlias, $groupBy) {
            $result = "";
            foreach ($groupBy as $field) {
                $field = $this->field($tableAlias, $field);
                $result = StringUtils::join($result, $field, ", ");
            }

            return $result;
        }

        private function appendGroupBy($value) {
            if (!empty($value)) {
                $value = " GROUP BY $value";
            }

            return $value;
        }

        private function orderBy($orderBy) {
            $result = "";

            foreach ($orderBy as $field => $value) {
                $value = ($value == "desc" || $value == "DESC") ? "DESC" : "ASC";
                $result = StringUtils::join($result, "`$field` $value", ", ");
            }

            return $result;
        }

        private function appendOrderBy($value) {
            if (!empty($value)) {
                $value = " ORDER BY $value";
            }

            return $value;
        }

        public function insert($tableName, $item) {
            return $this->insertOrUpdatePrivate($tableName, $item, null);
        }

        public function insertOrUpdate($tableName, $item, $updateFields) {
            return $this->insertOrUpdatePrivate($tableName, $item, $updateFields);
        }

        private function insertOrUpdatePrivate($tableName, $item, $updateFields) {
            $columns = "";
            $values = "";
            foreach ($item as $field => $value) {
                $columns = StringUtils::join($columns, "`$field`");
                $values = StringUtils::join($values, $this->escape($value));
            }

            $updateString = "";
            if (is_array($updateFields)) {
                foreach ($updateFields as $field) {
                    $value = $this->escape($item[$field]);
                    $updateString = StringUtils::join($updateString, "`$field` = $value");
                }
            }
            if (!empty($updateString)) {
                $updateString = " ON DUPLICATE KEY UPDATE $updateString";
            }

            $sql = "INSERT INTO `$tableName`($columns) VALUES ($values)$updateString;";
            return $sql;
        }

        public function update($tableName, $item, $filter) {
            $condition = $this->condition($filter, "AND ");
            $condition = $this->appendWhere($condition);

            $values = "";
            foreach ($item as $field => $value) {
                $value = $this->escape($value);
                $values = StringUtils::join($values, "`$field` = $value");
            }

            $sql = "UPDATE `$tableName` SET $values$condition;";
            return $sql;
        }

        public function delete($tableName, $filter) {
            $condition = $this->condition($filter, "AND ");
            $condition = $this->appendWhere($condition);

            $sql = "DELETE FROM `$tableName`$condition;";
            return $sql;
        }
        
        public function select($tableName, $fields, $filter = array(), $orderBy = array(), $count = null, $offset = null) {
            return $this->selectInternal($tableName, $fields, $filter, [], [], $orderBy, $count, $offset);
        }

        public function select2($data) {
            return $this->selectInternal(
                $data["table"], 
                $data["fields"], 
                $data["filter"], 
                $data["groupBy"] ?? [], 
                $data["having"] ?? [], 
                $data["orderBy"] ?? [], 
                $data["count"] ?? null, 
                $data["offset"] ?? null
            );
        }

        private function selectInternal($tableName, $fields, $filter = [], $groupBy = [], $having = [], $orderBy = [], $count = null, $offset = null) {
            $order = $this->orderBy($orderBy);
            $order = $this->appendOrderBy($order);

            $alias = "";
            if (is_array($tableName)) {
                $table = $tableName["table"];
                $alias = $tableName["alias"];
                $tableName = "`$table`";
                if (!empty($alias)) {
                    $tableName .= " AS $alias";
                }
            } else {
                $tableName = "`$tableName`";
            }

            $group = $this->groupBy($alias, $groupBy);
            $group = $this->appendGroupBy($group);

            $condition = $this->condition($filter, "AND ", $alias);
            $condition = $this->appendWhere($condition);
            
            $having = $this->condition($having, "AND ");
            $having = $this->appendHaving($having);

            $joins = array();
            $columns = "";

            $wildcardIndex = array_search("*", $fields);
            $hasWildcard = $wildcardIndex !== false;
            if ($hasWildcard) {
                $field = $this->field($alias, "*");
                $columns = StringUtils::join($columns, $field);
                unset($fields[$wildcardIndex]);
            }

            foreach ($fields as $field) {
                $fieldTableAlias = $alias;
                if (is_array($field)) {
                    if (array_key_exists("leftjoin", $field)) {
                        $leftjoin = $field["leftjoin"];

                        $fieldTableAlias = $leftjoin["alias"];
                        if (!array_key_exists($fieldTableAlias, $joins)) {
                            $onClause = "";
                            if (array_key_exists("keys", $leftjoin)) {
                                $onParts = [];
                                foreach ($leftjoin["keys"] as $key) {
                                    if (array_key_exists("source", $key)) {
                                        $source = $this->field($alias, $key["source"]);
                                    } else {
                                        $source = $this->escape($key["value"]);
                                    }

                                    $target = $this->field($fieldTableAlias, $key["target"]);
                                    $onParts[] = "$source = $target";
                                }

                                $onClause = implode(" AND ", $onParts);
                            } else {
                                $source = $this->field($alias, $leftjoin["source"]);
                                $target = $this->field($fieldTableAlias, $leftjoin["target"]);
                                $onClause = "$source = $target";
                            }

                            $table = $leftjoin["table"];
                            $joins[$fieldTableAlias] = "LEFT JOIN `$table` as $fieldTableAlias ON $onClause";
                        }
                    }
                    
                    if (array_key_exists("select", $field)) {
                        $column = $field["select"]["column"];
                        $as = $field["select"]["alias"] ?? $column;
                        $aggregation = $field["select"]["aggregation"] ?? null;
                        $function = $field["select"]["function"] ?? null;

                        $preColumn = '';
                        $postColumn = '';
                        if ($aggregation) {
                            $preColumn .= strtoupper($aggregation) . "(";
                            $postColumn .= ")";
                        }
                        if ($function) {
                            if (is_array($function)) {
                                $functionName = $function[""];
                                switch ($functionName) {
                                    case 'substr':
                                        $preColumn .= strtoupper($functionName) . "(";
                                        $postColumn .= ", " . $this->escape($function["start"]) . ", " . $this->escape($function["length"]) . ")";
                                        break;
                                    
                                    default:
                                        $preColumn .= strtoupper($functionName) . "(";
                                        $postColumn .= ")";
                                        break;
                                }
                            } else {
                                $preColumn .= strtoupper($function) . "(";
                                $postColumn .= ")";
                            }

                        }
                        $field = $preColumn . $this->field($fieldTableAlias, $column) . $postColumn . " AS `$as`";
                    }

                    $columns = StringUtils::join($columns, $field);
                }
                else if (!$hasWildcard) {
                    $field = $this->field($fieldTableAlias, $field);
                    $columns = StringUtils::join($columns, $field);
                }
            }

            if (!empty($joins)) {
                $joins = implode(" ", $joins);
                $joins = " $joins";
            } else {
                $joins = "";
            }

            $limit = "";
            if ($count != null) {
                $limit = " LIMIT ";
                if ($offset !== null) {
                    $limit .= "$offset, ";
                }

                $limit .= "$count";
            }

            $sql = "SELECT $columns FROM $tableName$joins$condition$group$having$order$limit;";
            return $sql;
        }

        public function count($tableName, $filter = array()) {
            $condition = $this->condition($filter, "AND");
            $condition = $this->appendWhere($condition);

            $alias = "";
            if (is_array($tableName)) {
                $table = $tableName["table"];
                $alias = $tableName["alias"];
                $tableName = "`$table`";
                if (!empty($alias)) {
                    $tableName .= " AS $alias";
                }
            } else {
                $tableName = "`$tableName`";
            }

            $sql = "SELECT COUNT(*) as `count` FROM $tableName$condition;";
            return $sql;
        }
    }

?>