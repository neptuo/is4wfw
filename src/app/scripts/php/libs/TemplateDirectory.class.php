<?php

use Mpdf\Tag\P;

require_once("Template.class.php");

	/**
	 * 
	 *  Class TemplateDirectory. 
	 *      
	 *  @author     maraf
	 *  @timestamp  2021-07-09
	 * 
	 */
	class TemplateDirectory extends Template {

		private $directory = "";
		private $relativeClassPathPrefix = null;

		public function __construct($prefix, $params = []) {
			if (array_key_exists("path", $params)) {
				$this->directory = $params["path"];

				if (StringUtils::startsWith($this->directory, INSTANCE_PATH)) {
					$userPath = substr($this->directory, strlen(INSTANCE_PATH));
					$userParts = explode("/", $userPath);
					if ($userParts[0] == "modules") {
						$this->relativeClassPathPrefix = $userParts[1];
					}
				}

			}
		}

		public function includeByFileName($fileName, $params) {
			return $this->includeWithBodyByFileName($fileName, null, $params);
		}

		public function includeWithBodyByFileName($fileName, $template, $params) {
            $filePath = FileUtils::combinePath($this->directory, $fileName) . ".view.php";
            if (!file_exists($filePath)) {
                $this->throwNotFound(["file" => $fileName]);
            }
            
            $templateContent = file_get_contents($filePath);
            $keys = ["templatedirectories", $fileName . "." . sha1($templateContent)];

            $parsedTemplate = $this->getParsedTemplate($keys);
			if ($parsedTemplate == null) {
				$parsedTemplate = $this->parseTemplate($keys, $templateContent, $this->relativeClassPathPrefix);
			}

			return $this->includeFinal($parsedTemplate, $template, $params);
		}

		public function includeByIdentifierWithBody($template, $fileName, $params) {
			return $this->includeWithBodyByFileName($fileName, $template, $params);
		}
	}

?>
