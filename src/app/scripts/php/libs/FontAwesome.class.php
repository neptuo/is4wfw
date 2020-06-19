<?php

	require_once("BaseTagLib.class.php");

	class FontAwesome extends BaseTagLib {

		private $areResourcesIncluded = false;

		public function __construct() {
			parent::setTagLibXml("FontAwesome.xml");
		}

		public function resources() {
			if (!$this->areResourcesIncluded) {
				parent::js()->addStyle("~/css/fontawesome/all.min.css");
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
			$this->resources();

			if ($name == "") {
				return "";
			}

			$params = $this->appendClass($params, $prefix);
			$params = $this->appendClass($params, "fa-$name");
			$attributes = parent::joinAttributes($params);
			return "<$tag$attributes></$tag>";
		}

		public function getProperty($name) {
			return $this->icon($name);
		}
	}

?>