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

		public function setValue($name, $id, $width = 0, $height = 0, $isAbsolute = false) {
			parent::request()->set($name, ["id" => $id, "width" => $width, "height" => $height, "isAbsolute" => $isAbsolute], 'fileUrl');
		}

		public function getProperty($id) {
			$width = 0;
			$height = 0;
			$value = intval($id);
			$isAbsolute = false;
			if ($value == 0) {
				$file = parent::request()->get($id, 'fileUrl');
				["id" => $id, "width" => $width, "height" => $height, "isAbsolute" => $isAbsolute] = $file;
			}

			$url = "~/file.php?rid=$id";
			if (!empty($width)) {
				$url .= "&width=$width";
			}
			if (!empty($height)) {
				$url .= "&height=$height";
			}

			if ($isAbsolute) {
				$domainUrl = $_SERVER["HTTP_HOST"] . $this->web()->getDevelopmentPortWithSeparator();
				$url = str_replace("~/", $_SERVER["REQUEST_SCHEME"] . "://" . $domainUrl . "/", $url);
			}

			return $url;
		}
	}

?>