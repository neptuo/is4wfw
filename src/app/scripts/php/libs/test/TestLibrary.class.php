<?php

	require_once(APP_SCRIPTS_PHP_PATH . "libs/BaseTagLib.class.php");
	require_once(APP_SCRIPTS_PHP_PATH . "libs/DefaultPhp.class.php");

	/**
	 * 
	 *  Class Validation. 
	 *      
	 *  @author     maraf
	 *  @timestamp  2020-07-01
	 * 
	 */
	class TestLibrary extends BaseTagLib {

		public function __construct() {
			parent::setTagLibXml("TestLibrary.xml");
        }

        public function provideAttributes(string $a, int $b = 0, $tagPrefix, $tagName, $tagParameters) {
            return $tagParameters;
        }

        public function provideFullTagBody(string $c) {
            $template = $this->getParsedTemplate($c);
            return [DefaultPhp::$FullTagTemplateName => function() use ($template) { return $template->evaluate(); }];
        }

        public function conditionsExecution($condition, $value) {
            return [DefaultPhp::$DecoratorExecuteName => $condition == $value];
        }

        public function coolDecorator($cool, $tagPrefix, $tagName, $tagParameters) {
            $template = $this->getParsedTemplate($cool);
            $tagParameters[DefaultPhp::$FullTagTemplateName] = function() use ($template) { return $template->evaluate(); };
            $tagParameters[DefaultPhp::$DecoratorExecuteName] = true;
            return $tagParameters;
        }

        public function optionalBody($path) {
            $tagParameters = [];
            if ($path == "test") {
                $tagParameters[DefaultPhp::$FullTagTemplateName] = "Test";
                $tagParameters[DefaultPhp::$DecoratorExecuteName] = true;
            } else {
                $tagParameters[DefaultPhp::$DecoratorExecuteName] = false;
            }

            return $tagParameters;
        }
    }

?>