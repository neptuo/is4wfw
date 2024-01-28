<?php

	require_once("BaseTagLib.class.php");

	class FontAwesome extends BaseTagLib {

		private $areResourcesIncluded = false;

		public function resources($customUrl = PhpRuntime::UnusedAttributeValue, $skip = false, $version = "5") {
			if ($skip == true) {
				$this->areResourcesIncluded = true;
			}

			if (!$this->areResourcesIncluded) {
				if ($customUrl === PhpRuntime::UnusedAttributeValue) {
					if ($version == "5") {
						$customUrl = "~/css/fontawesome/all.min.css"; // 5.13.0
					} else if ($version == "6") {
						$customUrl = "~/assets-web/fontawesome/6.5.1/css/all.min.css";
					} else {
						throw new ParameterException("version", "Currently supported versions are 5 and 6. For any other version use 'customUrl' attribute");
					}
				}

				parent::js()->addStyle($customUrl);
				$this->areResourcesIncluded = true;
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