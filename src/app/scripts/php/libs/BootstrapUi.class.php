<?php

	require_once("BaseTagLib.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/EditModel.class.php");

	class BootstrapUi extends BaseTagLib {

		private $areResourcesIncluded = false;

		public function __construct() {
			parent::setTagLibXml("BootstrapUi.xml");
			parent::setLocalizationBundle("bootstrapui");
		}

		private function addScript($virtualPath) {
			$script = parent::js()->formatScript($virtualPath);
			if ($script != null) {
				parent::web()->addScript($script);
			}
		}

		private function addStyle($virtualPath) {
			$script = parent::js()->formatStyle($virtualPath);
			if ($script != null) {
				parent::web()->addStyle($script);
			}
		}

		public function resources() {
			if (!$this->areResourcesIncluded) {
				self::addScript("~/js/bootstrap/jquery-3.2.1.slim.min.js");
				self::addScript("~/js/bootstrap/popper.min.js");
				self::addScript("~/js/bootstrap/bootstrap.min.js");
				self::addStyle("~/css/bootstrap/bootstrap.min.css");
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

		public function grid($template, $params = array()) {
			$params = self::appendClass($params, "row");
			$attributes = parent::joinAttributes($params);
			$content = parent::parseContent($template);
			return "<div$attributes>$content</div>";
		}
		
		public function column($template, $default = "", $small = "", $medium = "", $large = "", $extraLarge = "", $params = array()) {
			if ($default != "") {
				$params = self::appendClass($params, "col-$default");
			}
			if ($small != "") {
				$params = self::appendClass($params, "col-sm-$small");
			}
			if ($medium != "") {
				$params = self::appendClass($params, "col-md-$medium");
			}
			if ($large != "") {
				$params = self::appendClass($params, "col-lg-$large");
			}
			if ($extraLarge != "") {
				$params = self::appendClass($params, "col-xl-$extraLarge");
			}
			
			$attributes = parent::joinAttributes($params);
			$content = parent::parseContent($template);
			return "<div$attributes>$content</div>";
		}
	}

?>