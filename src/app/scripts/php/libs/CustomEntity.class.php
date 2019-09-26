<?php

	require_once("BaseTagLib.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/Model.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/Stack.class.php");

	class CustomEntity extends BaseTagLib {

		public function __construct() {
			parent::setTagLibXml("CustomEntity.xml");
        }
        
        public function tableCreator() {
            $model = new Model();
            self::pushModel($model);

            if (array_key_exists("ce-creator-save", $_REQUEST)) {
                $model->submit();
                self::partialView("customentities/tableCreator");
                $model->submit(false);
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