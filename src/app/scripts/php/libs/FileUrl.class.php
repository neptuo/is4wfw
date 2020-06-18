<?php

	require_once("BaseTagLib.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/ListModel.class.php");

	/**
	 * 
	 *  Class FileUrl. 
	 *      
	 *  @author     maraf
	 *  @timestamp  2020-06-19
	 * 
	 */
	class FileUrl extends BaseTagLib {

		public function __construct() {
			parent::setTagLibXml("FileUrl.xml");
		}

		public function setValue($name, $id) {
			parent::request()->set($name, $id, 'fileUrl');
		}

		public function getProperty($id) {
			$value = intval($id);
			if ($value == 0) {
				$id = parent::request()->get($id, 'fileUrl');
			}

			$url = "~/file.php?rid=$id";
			return $url;
		}
	}

?>