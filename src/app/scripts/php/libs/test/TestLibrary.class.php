<?php

	require_once(APP_SCRIPTS_PHP_PATH . "libs/BaseTagLib.class.php");
	require_once(APP_SCRIPTS_PHP_PATH . "libs/PhpRuntime.class.php");

	/**
	 * 
	 *  Class Validation. 
	 *      
	 *  @author     maraf
	 *  @timestamp  2020-07-01
	 * 
	 */
	class TestLibrary extends BaseTagLib {

        public function provideAttributes(string $a, int $b = 0, $tagPrefix, $tagName, $tagParameters) {
            return $tagParameters;
        }

        public function attributeAsBody(string $c) {
            $keys = ["testlibrary", sha1($c)];
            $template = $this->getParsedTemplate($keys);
            if ($template == null) {
                $template = $this->parseTemplate($keys, $c);
            }

            return [PhpRuntime::$FullTagTemplateName => $template];
        }

        public function conditionsExecution($condition, $value) {
            return [PhpRuntime::$DecoratorExecuteName => $condition == $value];
        }

        public function coolDecorator(string $cool, string $tagPrefix, string $tagName, array $tagParameters) {
            $keys = ["testlibrary", sha1($cool)];
            $template = $this->getParsedTemplate($keys);
            if ($template == null) {
                $template = $this->parseTemplate($keys, $cool);
            }
            
            $tagParameters[PhpRuntime::$FullTagTemplateName] = $template;
            $tagParameters[PhpRuntime::$DecoratorExecuteName] = true;
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

                $tagParameters[PhpRuntime::$FullTagTemplateName] = $template;
                $tagParameters[PhpRuntime::$DecoratorExecuteName] = true;
            } else {
                $tagParameters[PhpRuntime::$DecoratorExecuteName] = false;
            }

            return $tagParameters;
        }
    }

?>