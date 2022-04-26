<?php

	require_once("BaseTagLib.class.php");
	require_once(APP_SCRIPTS_PHP_PATH . "classes/Stack.class.php");

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

		private function ensureStack($key) {
			$storage = parent::request()->get($key, 'template:include');
			if ($storage == null) {
				parent::request()->set($key, $storage = new Stack(), 'template:include');
			}

			return $storage;
		}

		protected function includeFinal(callable $template, ?callable $contentTemplate, $params) {
			$paramsStorage = $this->ensureStack("params");
			$contentStorage = $this->ensureStack("content");

			$paramsStorage->push($params);
			$contentStorage->push($contentTemplate);
			$result = $template();
			$paramsStorage->pop();
			$contentStorage->pop();
			
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
			return $this->contentWithBody(null, $params);
		}

		public function contentWithBody($template, $params) {
			$paramsStorage = $this->ensureStack("params");
			$contentStorage = $this->ensureStack("content");

			$result = "";
			$oldContent = $contentStorage->pop();
			if ($oldContent != null && is_callable($oldContent)) {
				$paramsStorage->push($params);
				if ($template != null) {
					$contentStorage->push($template);
				}
				
				$result = $oldContent();
				
				$paramsStorage->pop();
				if ($template != null) {
					$contentStorage->pop();
				}
			}

			$contentStorage->push($oldContent);
			return $result;
		}

		public function getProperty($name) {
			$params = $this->ensureStack("params")->peek();
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

		public function attribute($name, $type = "", $default = null) {
			$paramsStorage = $this->ensureStack("params");
			$params = $paramsStorage->pop();
			if (array_key_exists($name, $params)) {
				$value = $params[$name];
			} else {
				$value = $default;
			}

			switch ($type) {
				case 'bool':
					$value = !empty($value) && $value !== "false";
					break;
				case 'number':
					$value = is_numeric($value) ? $value : 0;
					break;
			}

			$params[$name] = $value;
			$paramsStorage->push($params);
		}
	}

?>