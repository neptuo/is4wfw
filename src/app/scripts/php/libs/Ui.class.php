<?php

	require_once("BaseTagLib.class.php");
	require_once("BaseTagLib.class.php");

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
			return self::peekModel()[$key]['value'];
		}

		private function setModel($key, $value) {
			self::peekModel()[$key] = $value;
		}

		private function setModelType($key, $type) {
			self::setModel($key, array('type' => $type));
		}

		private function setModelValue($key, $value) {
			self::peekModel()[$key]['value'] = $value;
		}

		private function setModelValueFromRequest($modelKey, $requestKey, $type) {
			self::setModelValue($modelKey, $_REQUEST[$requestKey], $type);
		}

		public function form($template, $method = "post", $action = NULL) {
			if ($action == NULL) {
				$action = $_SERVER['REQUEST_URI'];
			}

            return ""
            . "<form method='$method' action='$action'>"
                . self::parseContent($template)
            . "</form>";
		}
		
		public function dropdownlist($name, $entity, $display, $id) {
			if (self::isRegistration() || self::isSubmit()) {
				self::setModelType($name, "reference");
			}

			if (self::isSubmit()) {
				self::setModelValueFromRequest($name, $name);
			}

			if (self::isRender()) {
				$modelValue = self::getModelValue($name);
				
				$result = "<select name='$name'>";

				$data = self::dataAccess()->fetchAll("SELECT `$id`, `$display` FROM `$entity` ORDER BY `$display`;");
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
				self::setModelType($name, "string");
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
				self::setModelType($name, "bool");
			}

			if (self::isSubmit()) {
				$modelValue = $_REQUEST[$name];
				self::setModelValue($name, $modelValue == "on");
			}

			if (self::isRender()) {
				$modelValue = self::getModelValue($name);
				return "<input name='$name' type='checkbox'" . ($modelValue === TRUE || $modelValue === 1 ? " checked='checked'" : '') . " />";
			}
		}
	}

?>