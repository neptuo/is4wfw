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

        public function attributeAsBody(string $c) {
            $keys = ["testlibrary", sha1($c)];
            $template = $this->getParsedTemplate($keys);
            if ($template == null) {
                $template = $this->parseTemplate($keys, $c);
            }

            return [DefaultPhp::$FullTagTemplateName => $template];
        }

        public function conditionsExecution($condition, $value) {
            return [DefaultPhp::$DecoratorExecuteName => $condition == $value];
        }

        public function coolDecorator(string $cool, string $tagPrefix, string $tagName, array $tagParameters) {
            $keys = ["testlibrary", sha1($cool)];
            $template = $this->getParsedTemplate($keys);
            if ($template == null) {
                $template = $this->parseTemplate($keys, $cool);
            }
            
            $tagParameters[DefaultPhp::$FullTagTemplateName] = $template;
            $tagParameters[DefaultPhp::$DecoratorExecuteName] = true;
            return $tagParameters;
        }

        public function optionalBody($path) {
            $tagParameters = [];
            if ($path == "test") {
                $templateContent = "Test";
                $keys = ["testlibrary", sha1($templateContent)];
                $template = $this->getParsedTemplate($keys);
                if ($template == null) {
                    $template = $this->parseTemplate($keys, $templateContent);
                }

                $tagParameters[DefaultPhp::$FullTagTemplateName] = $template;
                $tagParameters[DefaultPhp::$DecoratorExecuteName] = true;
            } else {
                $tagParameters[DefaultPhp::$DecoratorExecuteName] = false;
            }

            return $tagParameters;
        }
    }

?>