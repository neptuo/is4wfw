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
		
		public function dropdownlist($name, $entity, $display, $id) {
			if (!self::isGet()) {
				// Process request data.
				$value = $_REQUEST[$name];
				self::setModelValue($name, $value);
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
	}

?>