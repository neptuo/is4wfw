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

		public function icon($name, $prefix = "fa", $tag = "span", $params = []) {
			self::resources();

			$params = self::appendClass($params, $prefix);
			$params = self::appendClass($params, "fa-$name");
			$attributes = parent::joinAttributes($params);
			return "<$tag$attributes></$tag>";
		}

		public function getProperty($name) {
			return self::icon($name);
		}
	}

?>