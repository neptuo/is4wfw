<?php

	require_once("BaseTagLib.class.php");
	require_once("BaseTagLib.class.php");

	class Ui extends BaseTagLib {

		public function __construct() {
			parent::setTagLibXml("Ui.xml");
		}

		private function getModelValue($key) {
			return self::peekModel()[$key];
		}

		private function setModelValue($key, $value) {
			self::peekModel()[$key] = $value;
		}

		private function setModelValueFromRequest($modelKey, $requestKey) {
			self::peekModel()[$modelKey] = $_REQUEST[$requestKey];
		}
		
		public function dropdownlist($name, $entity, $display, $id) {
			if (!self::isGet()) {
				self::setModelValueFromRequest($name, $name);
			}

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

		public function textbox($name) {
			if (!self::isGet()) {
				self::setModelValueFromRequest($name, $name);
			}

			$modelValue = self::getModelValue($name);
			return "<input name='$name' type='text' value='$modelValue' />";
		}

		public function checkbox($name) {
			if (!self::isGet()) {
				$modelValue = $_REQUEST[$name];
				self::setModelValue($name, $modelValue == "on");
			}

			$modelValue = self::getModelValue($name);
			return "<input name='$name' type='checkbox'" . ($modelValue === TRUE || $modelValue === 1 ? " checked='checked'" : '') . " />";
		}
	}

?>