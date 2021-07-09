<?php

	require_once("BaseTagLib.class.php");
	require_once(APP_SCRIPTS_PHP_PATH . "classes/Stack.class.php");

	/**
	 * 
	 *  Class Route. 
	 *      
	 *  @author     maraf
	 *  @timestamp  2021-07-08
	 * 
	 */
	class Route extends BaseTagLib {

		private $virtualUrlParts;
		private $virtualUrlPartsIndex;
		private $canMatch;
		private $selectedTemplate;

		private $pathBuilder;
		private $routes = [];

		public function __construct() {
			$this->setTagLibXml("Route.xml");
		}

		public function router($template) {
			$virtualUrl = parent::web()->getVirtualUrl();
			$this->virtualUrlParts = StringUtils::explode($virtualUrl, '/');
			$this->virtualUrlPartsIndex = 0;
			$this->canMatch = true;
			$this->pathBuilder = new Stack();
			$this->selectedTemplate = null;
			
			$template();

			if ($this->selectedTemplate != null) {
				$template = $this->selectedTemplate;
				return $template();
			}
		}

		public function directory($template, $path) {
			$this->pathBuilder->push($path);

			if ($this->canMatch) {
				$currentPath = $this->virtualUrlParts[$this->virtualUrlPartsIndex];
				if (strcasecmp($currentPath, $path) == 0) {
					$this->virtualUrlPartsIndex++;
					$template();
					$this->virtualUrlPartsIndex--;
				} else {
					$this->canMatch = false;
					$template();
					$this->canMatch = true;
				}
			} else {
				$template();
			}

			$this->pathBuilder->pop();
		}

		public function file($template, $path, $name = null) {
			if ($this->canMatch) {
				if (empty($path) && count($this->virtualUrlParts) == $this->virtualUrlPartsIndex) {
					$this->selectedTemplate = $template;
				} else {
					$currentPath = $this->virtualUrlParts[$this->virtualUrlPartsIndex];
					if (strcasecmp($currentPath, $path) == 0 && count($this->virtualUrlParts) == $this->virtualUrlPartsIndex + 1) {
						$this->selectedTemplate = $template;
					}
				}
			}

			if (!empty($name)) {
				$parts = $this->pathBuilder->toArray();
				if (!empty($path)) {
					$parts[] = $path;
				}
				$url = "~/" . implode("/", $parts);
				$this->routes[$name] = $url;
			}
		}

		public function setRoute($name, $url) {
			$this->routes[$name] = $url;
		}

		public function getProperty($name) {
			if (array_key_exists($name, $this->routes)) {
				return $this->routes[$name];
			}

			throw new Exception("Named route '$name' not found.");
		}
	}

?>