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

		private function setModelValue($key, $value, $type) {
			self::peekModel()[$key] = array('value' => $value, 'type' => $type);
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
			if (!self::isGet()) {
				self::setModelValueFromRequest($name, $name, "number");
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
				self::setModelValueFromRequest($name, $name, "string");
			}

			$modelValue = self::getModelValue($name);
			return "<input name='$name' type='text' value='$modelValue' />";
		}

		public function checkbox($name) {
			if (!self::isGet()) {
				$modelValue = $_REQUEST[$name];
				self::setModelValue($name, $modelValue == "on", "bool");
			}

			$modelValue = self::getModelValue($name);
			return "<input name='$name' type='checkbox'" . ($modelValue === TRUE || $modelValue === 1 ? " checked='checked'" : '') . " />";
		}
	}

?>