<?php

	require_once("BaseTagLib.class.php");

	class FontAwesome extends BaseTagLib {

		private $areResourcesIncluded = false;

		public function resources($customUrl = PhpRuntime::UnusedAttributeValue, $skip = false) {
			if ($skip == true) {
				$this->areResourcesIncluded = true;
			}
			
			if (!$this->areResourcesIncluded) {
				if ($customUrl === PhpRuntime::UnusedAttributeValue) {
					$customUrl = "~/css/fontawesome/all.min.css";
				}

				parent::js()->addStyle($customUrl);
			}
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