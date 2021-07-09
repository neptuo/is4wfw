<?php

	require_once("Template.class.php");

	/**
	 * 
	 *  Class TemplateGroup. 
	 *      
	 *  @author     maraf
	 *  @timestamp  2021-03-10
	 * 
	 */
	class TemplateGroup extends Template {

		private $group = "";

		public function __construct($prefix, $params = []) {
			if (array_key_exists("group", $params)) {
				$this->group = $params["group"];
			}
		}

		public function includeByIdentifier($identifier, $params) {
			return $this->includeWithBodyByIdentifier($identifier, null, $params);
		}

		public function includeWithBodyByIdentifier($identifier, $template, $params) {
			$filter = ["identifier" => $identifier, "group" => $this->group];
			$sql = $this->sql()->select("template", ["id"], $filter);
			$entityId = $this->dataAccess()->fetchScalar($sql);
			if (empty($entityId)) {
				$this->throwNotFound($filter);
			}
			
			return $this->includeBy(["id" => $entityId], TemplateCacheKeys::template($entityId), $template, $params);
		}
	}

?>