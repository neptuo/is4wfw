<?php

	require_once("BaseTagLib.class.php");

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

		protected function throwNotFound($filter) {
			throw new Error("Missing template filtered by '" . http_build_query($filter) . "'.");;
		}

		private function getBy($filter) {
			$sql = parent::sql()->select("template", ["id", "content"], $filter);
			$data = parent::db()->fetchSingle($sql);
			if (empty($data)) {
				$this->throwNotFound($filter);
			}

			if (RoleHelper::isInRole(parent::login()->getGroupsIds(), RoleHelper::getRights(Web::$TemplateRightDesc, $data["id"], WEB_R_READ))) {
				return $data["content"];
			}

			throw new Error("Permission denied when reading template id = '" . $data["id"] . "'.");
		}

		protected function includeBy(array $filter, array $keys, ?callable $contentTemplate, $params) {
			$template = $this->getParsedTemplate($keys);
			if ($template == null) {
				$templateContent = $this->getBy($filter);
				$template = $this->parseTemplate($keys, $templateContent);
			}

			return $this->includeFinal($template, $contentTemplate, $params);
		}

		protected function includeFinal(callable $template, ?callable $contentTemplate, $params) {
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
		
		public function includeWithBodyById(?callable $template, $id, $params) {
			return $this->includeBy(["id" => $id], TemplateCacheKeys::template($id), $template, $params);
		}

		public function includeByIdentifier($identifier, $params) {
			return $this->includeWithBodyByIdentifier($identifier, null, $params);
		}

		public function includeWithBodyByIdentifier($identifier, $template, $params) {
			if (array_key_exists($identifier, $this->inline)) {
				return $this->includeFinal($this->inline[$identifier], $template, $params);
			}

			$filter = ["identifier" => $identifier, "group" => ""];
			$sql = $this->sql()->select("template", ["id"], $filter);
			$entityId = $this->dataAccess()->fetchScalar($sql);
			if (empty($entityId)) {
				$this->throwNotFound($filter);
			}
			
			return $this->includeBy(["id" => $entityId], TemplateCacheKeys::template($entityId), $template, $params);
		}

		public function content($params) {
			$template = parent::request()->get('content', 'template:include');
			if ($template != null && is_callable($template)) {
				$oldParams = parent::request()->get('params', 'template:include');
				parent::request()->set('params', $params, 'template:include');
				
				$result = $template();

				parent::request()->set('params', $oldParams, 'template:include');

				return $result;
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
			$keys = TemplateCacheKeys::template($id);
			$template = $this->getParsedTemplate($keys);
			if ($template == null) {
				$templateContent = $this->getBy(["id" => $id]);
				$template = $this->parseTemplate($keys, $templateContent);
			}

			$parameters = [PhpRuntime::$FullTagTemplateName => $template];
			return $parameters;
		}
	}

?>