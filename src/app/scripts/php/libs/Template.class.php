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

		private $inline = [];

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

		private function includeBy(array $filter, array $keys, callable $contentTemplate, $params) {
			$template = $this->getParsedTemplate($keys);
			if ($template == null) {
				$templateContent = $this->findBy($filter);
				$template = $this->parseTemplate($keys, $templateContent);
			}

			return $this->includeFinal($template, $contentTemplate, $params);
		}

		private function includeFinal(callable $template, callable $contentTemplate, $params) {
			$oldContent = parent::request()->get('content', 'template:include');
			$oldParams = parent::request()->get('params', 'template:include');
			parent::request()->set('params', $params, 'template:include');
			parent::request()->set('content', $contentTemplate, 'template:include');

			$result = $template();
			
			parent::request()->set('params', $oldParams, 'template:include');
			parent::request()->set('content', $oldContent, 'template:include');

			return $result;
		}
		
		public function includeById($id, $params) {
			return $this->includeWithBodyById(null, $id, $params);
		}
		
		public function includeWithBodyById(?ParsedTemplate $template, $id, $params) {
			return $this->includeBy(["id" => $id], ["template", "id", $id], $template, $params);
		}

		public function includeByIdentifier($identifier, $params) {
			if (array_key_exists($identifier, $this->inline)) {
				return $this->includeFinal($this->inline[$identifier], null, $params);
			}

			return $this->includeWithBodyByIdentifier($identifier, null, $params);
		}

		public function includeWithBodyByIdentifier($identifier, $template, $params) {
			return $this->includeBy(["identifier" => $identifier], ["template", "identifier", $identifier], $template, $params);
		}

		public function content() {
			$template = parent::request()->get('content', 'template:include');
			if ($template != null && is_callable($template)) {
				return $template();
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

		public function declareInline(callable $template, string $identifier) {
			$this->inline[$identifier] = $template;
			return "";
		}

		public function provideBodyById($id) {
			$keys = ["template", $id];
			$template = $this->getParsedTemplate($keys);
			if ($template == null) {
				$templateContent = $this->findBy(["id" => $id]);
				$template = $this->parseTemplate($keys, $templateContent);
			}

			$parameters = [DefaultPhp::$FullTagTemplateName => $template];
			return $parameters;
		}
	}

?>