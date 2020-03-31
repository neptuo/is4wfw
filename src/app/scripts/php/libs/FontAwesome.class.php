<?php

	require_once("BaseTagLib.class.php");

	class FontAwesome extends BaseTagLib {

		private $areResourcesIncluded = false;
		private $lastId = 0;

		public function __construct() {
			parent::setTagLibXml("FontAwesome.xml");
		}

		private function addStyle($virtualPath) {
			$script = parent::js()->formatStyle($virtualPath);
			if ($script != null) {
				parent::web()->addStyle($script);
			}
		}

		public function resources() {
			if (!$this->areResourcesIncluded) {
				self::addStyle("~/css/fontawesome/all.min.css");
			}
		}

		private function appendClass($attributes, $class) {
			if (array_key_exists("class", $attributes)) {
				$attributes["class"] .= " $class";
			} else {
				$attributes["class"] = $class;
			}

			return $attributes;
		}

		public function span($icon, $params = []) {
			self::resources();

			if (strpos($icon, " ")) {
				$params = self::appendClass($params, "$icon");
			} else {
				$params = self::appendClass($params, "fa");
				$params = self::appendClass($params, "fa-$icon");
			}

			$attributes = parent::joinAttributes($params);
			return "<span$attributes></span>";
		}

		public function getProperty($name) {
			return self::span($name, []);
		}
	}

?>