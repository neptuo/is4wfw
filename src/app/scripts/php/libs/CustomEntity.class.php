<?php

	require_once("BaseTagLib.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/Model.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/Stack.class.php");

	class CustomEntity extends BaseTagLib {

		public function __construct() {
			parent::setTagLibXml("CustomEntity.xml");
		}
        
		public function form($template, $name, $method = "POST", $submit) {
            if ($method == "GET" && $submit == NULL) {
                trigger_error("Missing required parameter 'submit' for 'GET' custom entity form '$name'", E_USER_ERROR);
            }

            $model = new Model();
            self::pushModel($model);

            if (self::isHttpMethod($method) && ($submit == NULL || array_key_exists($submit, $_REQUEST))) {
                self::parseContent($template);

                // TODO: Insert value.
                print_r($model);
            }

            return self::ui()->form($template, "post");
		}
	}

?>