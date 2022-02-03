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

		public function __construct($prefix, $params = []) {
			if (array_key_exists("path", $params)) {
				$this->directory = $params["path"];
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
				$parsedTemplate = $this->parseTemplate($keys, $templateContent);
			}

			return $this->includeFinal($parsedTemplate, $template, $params);
		}

		public function includeByIdentifierWithBody($template, $fileName, $params) {
			return $this->includeWithBodyByFileName($fileName, $template, $params);
		}
	}

?>
