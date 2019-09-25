<?php

	require_once("BaseTagLib.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/Model.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/Stack.class.php");

	class CustomEntity extends BaseTagLib {

		public function __construct() {
			parent::setTagLibXml("CustomEntity.xml");
		}
        
        private function parseContent($content) {
            $parser = new FullTagParser();
            $parser->setContent($content);
            $parser->startParsing();
            $return = $parser->getResult();
            return $return;
        }
		
		public function form($template, $name) {
            $model = new Model();
            self::pushModel($model);

            if (self::isPost()) {
                self::parseContent($template);

                // TODO: Insert value.
                print_r($model);
            }

            return self::ui()->form($template, "post");
		}
	}

?>