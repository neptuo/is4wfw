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
			parent::setTagLibXml("xml/Template.xml");
		}
		
		public function includeById($id, $params) {
			$oldParams = parent::request()->get('params', 'template:include');
			parent::request()->set('params', $params, 'template:include');
			
			$return = parent::web()->includeTemplate($id);

			if ($oldParams == null) {
				parent::request()->clear('template:include');
			} else {
				parent::request()->set('params', $oldParams, 'template:include');
			}

			return $return;
		}

		public function getProperty($name) {
			$params = parent::request()->get('params', 'template:include');
			if ($params != null && array_key_exists($name, $params)) {
				return $params[$name];
			}

			return '';
		}
	}

?>