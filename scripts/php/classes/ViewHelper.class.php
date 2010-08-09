<?php

	class ViewHelper {
		
		public static function getViewContent($path) {
			if(strpos($path, '.view') == '') {
				$path = ViewHelper::resolveViewPath($path);
			} elseif(strpos($path, '.php') == '') {
				$path .= '.php';
			}
			$path = str_replace('~/', VIEW_ROOT.'/', $path);
			if(file_exists($path)) {
				return file_get_contents($path);
			} else {
				throw new Exception('View doesn\'t exist!');
			}
		}
		
		public static function resolveViewPath($path) {
			return ViewHelper::resolveViewRoot($path).'.view.php';
		}
		
		public static function resolveViewRoot($path) {
			return str_replace('~/', VIEW_ROOT.'/', $path);
		}
		
		public static function resolveUrl($url) {
			return str_replace('~/', WEB_ROOT, $url);
		}
		
	}

?>
