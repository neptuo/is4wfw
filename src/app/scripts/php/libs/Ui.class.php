<?php

	require_once("BaseTagLib.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/EditModel.class.php");

	class Ui extends BaseTagLib {

		public function __construct() {
			parent::setTagLibXml("Ui.xml");
		}

		private function isRegistration() {
			return self::peekModel()->isRegistration();
		}

		private function isSubmit() {
			return self::peekModel()->isSubmit();
		}

		private function isRender() {
			return self::peekModel()->isRender();
		}

		private function getModelValue($key) {
			return self::peekModel()[$key];
		}

		private function setModelValue($key, $value) {
			self::peekModel()[$key] = $value;
		}

		private function setModelValueFromRequest($modelKey, $requestKey, $type) {
			self::setModelValue($modelKey, $_REQUEST[$requestKey], $type);
		}

		public function form($template, $method = "post", $action = NULL) {
			if ($action == NULL) {
				$action = $_SERVER['REQUEST_URI'];
			} else {
				$action = self::web()->composeUrl($action);
			}

            return ""
            . "<form method='$method' action='$action'>"
                . self::parseContent($template)
            . "</form>";
		}
		
		public function dropdownlist($name, $source, $display, $id) {
			if (self::isRegistration() || self::isSubmit()) {
				self::setModelValue($name, NULL);
			}

			if (self::isSubmit()) {
				self::setModelValueFromRequest($name, $name);
			}

			if (self::isRender()) {
				$modelValue = self::getModelValue($name);
				
				$result = "<select name='$name'>";

				if (is_array($source)) {
					$data = $source;
				} else {
					$data = self::dataAccess()->fetchAll("SELECT `$id`, `$display` FROM `$source` ORDER BY `$display`;");
				}

				foreach ($data as $item) {
					$itemValue = $item[$id];
					$result .= "<option value='$itemValue'" . ($modelValue == $itemValue ? " selected='selected'" : "") . ">$item[$display]</option>";
				}

				$result .= "</select>";
				return $result;
			}
			
		}

		public function textbox($name) {
			if (self::isRegistration() || self::isSubmit()) {
				self::setModelValue($name, NULL);
			}

			if (self::isSubmit()) {
				self::setModelValueFromRequest($name, $name);
			}

			if (self::isRender()) {
				$modelValue = self::getModelValue($name);
				return "<input name='$name' type='text' value='$modelValue' />";
			}
		}

		public function checkbox($name) {
			if (self::isRegistration() || self::isSubmit()) {
				self::setModelValue($name, NULL);
			}

			if (self::isSubmit()) {
				$modelValue = $_REQUEST[$name];
				self::setModelValue($name, $modelValue == "on" ? 1 : 0);
			}

			if (self::isRender()) {
				$modelValue = self::getModelValue($name);
				return "<input name='$name' type='checkbox'" . ($modelValue === TRUE || $modelValue === 1 || $modelValue === "1" ? " checked='checked'" : '') . " />";
			}
		}
	}

?>