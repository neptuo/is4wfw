<?php

	require_once("BaseTagLib.class.php");
	require_once("Route.class.php");
	require_once(APP_SCRIPTS_PHP_PATH . "classes/Stack.class.php");

	/**
	 * 
	 *  Class Router. 
	 *      
	 *  @author     maraf
	 *  @timestamp  2021-07-08
	 * 
	 */
	class Router extends BaseTagLib {

		private $routerPhase;
		private $template;

		private $virtualUrlParts;
		private $virtualUrlPartsIndex;
		private $canMatch;
		private $selectedName;
		private $selectedIdentifiers;

		private $pathBuilder;

		public function route(): Route {
			return $this->autolib("route");
		}

		private function hasMatch() {
			return $this->selectedName != null;
		}

		public function fromPath($template) {
			$virtualUrl = parent::web()->getVirtualUrl();
			$this->virtualUrlParts = StringUtils::explode($virtualUrl, '/');
			$this->virtualUrlPartsIndex = 0;
			$this->canMatch = true;
			$this->pathBuilder = new Stack();
			$this->selectedName = null;
			$this->selectedIdentifiers = new Stack();
			$this->template = $template;
			
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
						$this->selectedName = $name;
						$this->selectedIdentifiers->push($identifier);
					} else {
						$currentPath = $this->virtualUrlParts[$this->virtualUrlPartsIndex];
						if (strcasecmp($currentPath, $path) == 0 && count($this->virtualUrlParts) == $this->virtualUrlPartsIndex + 1) {
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
					$this->route()->set($name, $url);
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
				$template = $this->template;
				$result = $template();

				$this->routerPhase = null;
				return $result;
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

		public function getSelectedName() {
			return $this->selectedName;
		}
	}

?>