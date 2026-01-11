<?php

	require_once("BaseTagLib.class.php");

	class BootstrapIcons extends BaseTagLib {

		private $areResourcesIncluded = false;
		private $loadedVersion = "1";

		public function resources($customUrl = PhpRuntime::UnusedAttributeValue, $skip = false, $version = "1") {
			if ($skip == true) {
				$this->areResourcesIncluded = true;
			}

			if (!$this->areResourcesIncluded) {
				if ($customUrl === PhpRuntime::UnusedAttributeValue) {
					if ($version == "1") {
						$customUrl = "~/assets-web/bootstrap-icons/1.13.1/bootstrap-icons.min.css";
					} else {
						throw new ParameterException("version", "Currently only supported version is 1. For any other version use 'customUrl' attribute");
					}
				} else {
					$this->loadedVersion = null;
				}

				parent::js()->addStyle($customUrl);
				$this->areResourcesIncluded = true;
			}
		}

		public function icon($name, $tag = "i", $params = []) {
			if ($name == "") {
				return "";
			}

			$isSvg = strtolower($tag) == "svg";
			if ($isSvg) {
				if (!empty($params)) {
					throw new ParameterException("params", "Additional attributes are not supported for SVG icons");
				}

				$path = null;
				if ($this->loadedVersion == "1") {
					$path = APP_SCRIPTS_ASSETS_PATH . "/bootstrap-icons/1.13.1/$name.svg";
				} else {
					throw new ParameterException("tag", "Inline SVG icons are only supported for internal versions");
				}

				if (!file_exists($path)) {
					throw new ParameterException("name", "Icon '$name' does not exist in Bootstrap Icons library");
				}

				return file_get_contents($path);
			} else {
				$this->resources();
				$params = $this->appendClass($params, "bi");
				$params = $this->appendClass($params, "bi-$name");
				$attributes = parent::joinAttributes($params);
				return "<$tag$attributes></$tag>";
			}
		}

		public function getProperty($name) {
			return $this->icon($name);
		}
	}

?>