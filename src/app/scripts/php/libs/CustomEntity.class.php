<?php

	require_once("BaseTagLib.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/Model.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/Stack.class.php");

	class CustomEntity extends BaseTagLib {

        private $tables;
        private $columns;

		public function __construct() {
            parent::setTagLibXml("CustomEntity.xml");
            
            $this->tables = new Stack();
            $this->columns = new Stack();
        }

        private function getDbType($type) {
            $items = self::getTableColumnTypes();
            foreach ($items as $item) {
                if ($item["key"] == $type) {
                    return $item["db"];
                }
            }
            
            return NULL;
        }

        private function createTable($model) {
            $sql = "CREATE TABLE `ce_" . $model["entity-name"]["value"] . "` (";
            $primary = ', PRIMARY KEY (';

            $columnName = $model["primary-key-1-name"]["value"];
            $sql .= "`" . $columnName . "` " . (self::getDbType($model["primary-key-1-type"]["value"])) . " NOT NULL" . ($model["primary-key-1-identity"]["value"] ? " AUTO_INCREMENT" : "");
            $primary .= "`" . $columnName . "`";

            $columnName = $model["primary-key-2-name"]["value"];
            if ($columnName != "") {
                $sql .= ", `" . $columnName . "` " . (self::getDbType($model["primary-key-2-type"]["value"])) . " NOT NULL";
                $primary .= ", `" . $columnName . "`";
            }

            $columnName = $model["primary-key-3-name"]["value"];
            if ($columnName != "") {
                $sql .= ", `" . $columnName . "` " . (self::getDbType($model["primary-key-3-type"]["value"])) . " NOT NULL";
                $primary .= ", `" . $columnName . "`";
            }

            $sql .= "`)";
            $sql .= $primary . ') ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci ROW_FORMAT=FIXED;';

            self::dataAccess()->execute($sql, true, true, true);
            return true;
        }
        
        public function tableCreator() {
            $model = new Model();
            self::pushModel($model);

            if (array_key_exists("ce-creator-save", $_REQUEST)) {
                $model->submit();
                self::partialView("customentities/tableCreator");
                $model->submit(false);

                if (self::createTable($model)) {
                    self::redirectToSelf();
                    return;
                }
            }

            $model->render();
            $result = self::partialView("customentities/tableCreator");
            self::popModel();
            return $result;
        }

        public function getTableColumnTypes() {
            return array(
                array("key" => "number", "name" => "Number", "db" => "INT"),
                array("key" => "string", "name" => "Text", "db" => "TINYTEXT"),
                array("key" => "bool", "name" => "Boolean", "db" => "BIT")
            );
        }

        public function listTables($template) {
            $tables = self::dataAccess()->fetchAll("show tables");

            $result = "";
            foreach ($tables as $table) {
                $tableName = end($table);
                $this->tables->push($tableName);
                $result .= self::parseContent($template);
                $this->tables->pop();
            }

            return $result;
        }

        public function getTableName() {
            return $this->tables->peek();
        }

        public function listTableColumns($template, $name) {
            $columns = self::dataAccess()->fetchAll("show columns from `$name`;");
            
            $result = "";
            foreach ($columns as $column) {
                $this->columns->push($column);
                $result .= self::parseContent($template);
                $this->columns->pop();
            }

            return $result;
        }

        public function getTableColumnName() {
            return $this->columns->peek()["Field"];
        }

        public function getTableColumnType() {
            return $this->columns->peek()["Type"];
        }
        
		public function form($template, $name, $id = 0, $method = "POST", $submit = "") {
            if ($method == "GET" && $submit == "") {
                trigger_error("Missing required parameter 'submit' for 'GET' custom entity form '$name'", E_USER_ERROR);
            }

            $model = new Model();
            self::pushModel($model);

            if ($id > 0) {
                $model->registration();
                self::parseContent($template);
                $model->registration(false);

                // TODO: Load data.
                print_r($model);
            }

            if (self::isHttpMethod($method) && ($submit == "" || array_key_exists($submit, $_REQUEST))) {
                $model->submit();
                self::parseContent($template);
                $model->submit(false);

                // TODO: Insert/update value.
                print_r($model);
            }

            $model->render();
            $result = self::ui()->form($template, "post");
            self::popModel();
            return $result;
		}
	}

?>