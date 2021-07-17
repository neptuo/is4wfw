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

		private $routerPhase;
		private $routerTemplate;

		private $virtualUrlParts;
		private $virtualUrlPartsIndex;
		private $canMatch;
		private $selectedName;
		private $selectedTemplate;
		private $selectedIdentifiers;

		private $pathBuilder;
		private $routes = [];

		private $useName;

		private function hasMatch() {
			return $this->selectedTemplate != null;
		}

		public function router($template) {
			$virtualUrl = parent::web()->getVirtualUrl();
			$this->virtualUrlParts = StringUtils::explode($virtualUrl, '/');
			$this->virtualUrlPartsIndex = 0;
			$this->canMatch = true;
			$this->pathBuilder = new Stack();
			$this->selectedTemplate = null;
			$this->selectedName = null;
			$this->selectedIdentifiers = new Stack();

			$this->routerTemplate = $template;
			
			$this->routerPhase = "build";
			$template();
			
			$this->routerPhase = "evaluate";
			$template();

			$this->routerPhase = null;
		}

		public function directory($template, $identifier, $path) {
			if ($this->routerPhase == "build") {
				$this->pathBuilder->push($path);
				
				$wasMatch = $this->hasMatch();
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

				if (!$wasMatch && $this->hasMatch()) {
					$this->selectedIdentifiers->push($identifier);
				}

				$this->pathBuilder->pop();
			} else if ($this->routerPhase == "evaluate") {
				if (in_array($identifier, $this->selectedIdentifiers->toArray())) {
					$template();
				}
			} else if ($this->routerPhase == "render") {
				if (in_array($identifier, $this->selectedIdentifiers->toArray())) {
					return $template();
				}
			}
		}

		public function file($template, $identifier, $path, $name = null) {
			if ($this->routerPhase == "build") {
				if ($this->canMatch && !$this->hasMatch()) {
					if ($path == "*" || (empty($path) && count($this->virtualUrlParts) == $this->virtualUrlPartsIndex)) {
						$this->selectedTemplate = $template;
						$this->selectedName = $name;
						$this->selectedIdentifiers->push($identifier);
					} else {
						$currentPath = $this->virtualUrlParts[$this->virtualUrlPartsIndex];
						if (strcasecmp($currentPath, $path) == 0 && count($this->virtualUrlParts) == $this->virtualUrlPartsIndex + 1) {
							$this->selectedTemplate = $template;
							$this->selectedName = $name;
							$this->selectedIdentifiers->push($identifier);
						}
					}
				}

				if (!empty($name)) {
					$parts = $this->pathBuilder->toArray();
					if (!empty($path)) {
						$parts[] = $path;
					}
					$url = "~/" . implode("/", $parts);
					$this->setRoute($name, $url);
				}
			} else if ($this->routerPhase == "render") {
				if (in_array($identifier, $this->selectedIdentifiers->toArray())) {
					return $template();
				}
			}
		}

		public function render() {
			if ($this->hasMatch()) {
				$this->routerPhase = "render";
				$template = $this->routerTemplate;
				$result = $template();

				$this->routerPhase = null;
				return $result;
				// $template = $this->selectedTemplate;
				// return $template();
			}
		}

		public function getIsBuild() {
			return $this->routerPhase == "build";
		}

		public function getIsEvaluate() {
			return $this->routerPhase == "evaluate";
		}

		public function getIsRender() {
			return $this->routerPhase == "render";
		}

		public function setRoute($name, $url) {
			$this->routes[$name] = $url;
		}

		public function use($template, $name) {
			$lastName = $this->useName;
			$this->useName = $name;

			$result = $template();

			$this->$name = $lastName;

			return $result;
		}

		public function getName() {
			return $this->useName;
		}

		public function getUrl() {
			return $this->getProperty($this->useName);
		}

		public function getIsActive() {
			return $this->useName == $this->selectedName;
		}

		public function getProperty($name) {
			if (array_key_exists($name, $this->routes)) {
				return $this->routes[$name];
			}

			throw new Exception("Named route '$name' not found.");
		}
	}

?>