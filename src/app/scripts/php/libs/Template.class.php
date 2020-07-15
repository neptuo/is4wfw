<?php

	require_once("BaseTagLib.class.php");
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

		private function findBy($filter) {
			$sql = parent::sql()->select("template", ["id", "content"], $filter);
			$data = parent::db()->fetchSingle($sql);
			if (empty($data)) {
				throw new Error("Missing template filterd by '" . http_build_query($filter) . "'.");
			}

			if (RoleHelper::isInRole(parent::login()->getGroupsIds(), RoleHelper::getRights(DefaultWeb::$TemplateRightDesc, $data["id"], WEB_R_READ))) {
				return $data["content"];
			}

			throw new Error("Permission denied when reading template id = '" . $data["id"] . "'.");
		}

		private function includeBy($filter, $template, $params) {
			$data = $this->findBy($filter);

			$oldContent = parent::request()->get('content', 'template:include');
			$oldParams = parent::request()->get('params', 'template:include');
			parent::request()->set('params', $params, 'template:include');
			parent::request()->set('content', $template, 'template:include');

			$result = parent::parseContent($data);
			
			parent::request()->set('params', $oldParams, 'template:include');
			parent::request()->set('content', $oldContent, 'template:include');

			return $result;
		}
		
		public function includeById($id, $params) {
			return $this->includeWithBodyById(null, $id, $params);
		}
		
		public function includeWithBodyById($template, $id, $params) {
			return $this->includeBy(["id" => $id], $template, $params);
		}

		public function includeByIdentifier($identifier, $params) {
			return $this->includeWithBodyByIdentifier($identifier, null, $params);
		}

		public function includeWithBodyByIdentifier($identifier, $template, $params) {
			return $this->includeBy(["identifier" => $identifier], $template, $params);
		}

		public function content() {
			$content = parent::request()->get('content', 'template:include');
			if ($content != null) {
				return $this->parseContent($content);
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

		public function provideBodyById($id, $parameters) {
			$template = $this->findBy(["id" => $id]);
			
			// return [DefaultPhp::$FullTagTemplateName => $template];
			$parameters[DefaultPhp::$FullTagTemplateName] = $template;
			$parameters[DefaultPhp::$DecoratorExecuteName] = true;
			return $parameters;
		}
	}

?>