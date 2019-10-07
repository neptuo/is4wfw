<?php

	require_once("BaseTagLib.class.php");
	require_once(APP_SCRIPTS_PHP_PATH . "classes/FullTagParser.class.php");
	require_once(APP_SCRIPTS_PHP_PATH . "classes/LocalizationBundle.class.php");

	/**
	 * 
	 *  Class Template. 
	 *      
	 *  @author     maraf
	 *  @timestamp  2018-01-24
	 * 
	 */
	class Template extends BaseTagLib {

		public function __construct() {
			parent::setTagLibXml("Template.xml");
		}
		
		public function includeById($id, $params) {
			return self::includeWithBodyById(null, $id, $params);
		}
		
		public function includeWithBodyById($template, $id, $params) {
			$oldContent = parent::request()->get('content', 'template:include');
			$oldParams = parent::request()->get('params', 'template:include');
			parent::request()->set('params', $params, 'template:include');
			parent::request()->set('content', $template, 'template:include');
			
			$return = parent::web()->includeTemplate($id);
			
			parent::request()->set('params', $oldParams, 'template:include');
			parent::request()->set('content', $oldContent, 'template:include');
			
			return $return;
		}

		public function content() {
			$content = parent::request()->get('content', 'template:include');
			if ($content != null) {
				return self::parseContent($content);
			}

			return "";
		}

		public function getProperty($name) {
			$params = parent::request()->get('params', 'template:include');
			if ($params != null && array_key_exists($name, $params)) {
				return $params[$name];
			}

			return "";
		}
	}

?>