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
	}

?>