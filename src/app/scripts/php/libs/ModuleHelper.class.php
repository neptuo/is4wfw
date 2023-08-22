<?php

	require_once("BaseTagLib.class.php");
	require_once(APP_SCRIPTS_PHP_PATH . "classes/Module.class.php");
	require_once(APP_SCRIPTS_PHP_PATH . "classes/Validator.class.php");
	require_once(APP_SCRIPTS_PHP_PATH . "classes/manager/GitHubReleaseClient.class.php");

	/**
	 * 
	 *  Class ModuleHelper. 
	 *      
	 *  @author     maraf
	 *  @timestamp  2021-07-14
	 * 
	 */
	class ModuleHelper extends BaseTagLib {

		private $current = null;
		private $currentViews = null;

		private function ensureCurrent() {
			if ($this->current == null) {
				throw new Exception("Current module not set.");
			}
		}

		public function use($template, $id, $alias) {
			$lastCurrent = $this->current;

			if (empty($id) && empty($alias)) {
				throw new ParameterException("id", "One for 'id' or 'alias' must be provided.");
			}

			if (!empty($id)) {
				$this->current = Module::getById($id);
			} else if (!empty($alias)) {
				$this->current = Module::getByAlias($alias);
			}
			
			$result = $template();

			$this->current = $lastCurrent;

			return $result;
		}

		public function views($template, $path) {
			$this->ensureCurrent();

			$lastViews = $this->currentViews;

			$this->currentViews = FileUtils::combinePath($this->current->getViewsPath(), $path);
			$result = $template();

			$this->currentViews = $lastViews;

			return $result;
		}

		public function getViewsPath() {
			return $this->currentViews;
		}

		public function assets($template, $path) {
			$this->ensureCurrent();

			$lastAssets = $this->currentAssets;

			$this->currentAssets = FileUtils::combinePath($this->current->getAssetsPath(), $path);
			$result = $template();

			$this->currentViews = $lastAssets;

			return $result;
		}

		public function getAssetsUrl() {
			return $this->currentAssets;
		}

		public function list($template) {
			$modules = Module::all(false);
			
			$model = new ListModel();
			$this->pushListModel($model);
			
			$model->render();
			$model->items($modules);
			$result = $template();
			
			$this->popListModel();
			return $result;
		}

		public function getList() {
			return $this->peekListModel();
		}

		public function getId() {
			if ($this->hasListModel()) {
				return $this->peekListModel()->currentItem()->id;
			}

			if ($this->current) {
				return $this->current->id;
			}
		}

		public function getAlias() {
			if ($this->hasListModel()) {
				return $this->peekListModel()->currentItem()->alias;
			}

			if ($this->current) {
				return $this->current->alias;
			}
		}

		public function getName() {
			if ($this->hasListModel()) {
				return $this->peekListModel()->currentItem()->name;
			}

			if ($this->current) {
				return $this->current->name;
			}
		}

		public function getVersion() {
			if ($this->hasListModel()) {
				return $this->peekListModel()->currentItem()->version;
			}

			if ($this->current) {
				return $this->current->version;
			}
		}

		public function getIsSupported() {
			$version = null;

			if ($this->hasListModel()) {
				$version = $this->peekListModel()->currentItem()->is4wfw->minVersion;
			}

			if ($this->current) {
				$version = $this->current->is4wfw->minVersion;
			}

			if ($version == null) {
				return true;
			}

			return Module::isSupportedVersion($version);
		}

		public function getCanEdit() {
			$module = null;

			if ($this->hasListModel()) {
				$module = $this->peekListModel()->currentItem();
			}

			if ($this->current) {
				$module = $this->current->is4wfw->minVersion;
			}

			if ($module == null) {
				return false;
			}

			return $module->canEdit();
		}

		public function getGitHubRepositoryName() {
			$module = null;
			if ($this->hasListModel()) {
				$module = $this->peekListModel()->currentItem();
			}

			if ($this->current) {
				$module = $this->current;
			}

			if ($module && $module->gitHub) {
				return $module->gitHub->repositoryName;
			}

			return null;
		}

		public function edit($template, $id) {
			$model = parent::getEditModel();

			$isEdit = !empty($id);

			if ($model->isLoad()) {
				if ($isEdit) {
					$module = Module::findById($id);
					if ($module) {
						if (!$module->canEdit()) {
							throw new Exception("Module '$id' is not editable.");
						}

						$model["id"] = $module->id;
						$model["alias"] = $module->alias;
						$model["name"] = $module->name;
					}
				}

				$template();
			}

			if ($model->isSubmit()) {
				$template();
				
				Validator::required($model, "zip");
				if (!$isEdit) {
					Validator::required($model, "alias");

					if ($model->isValid()) {
						if (Module::findByAlias($model["alias"])) {
							Validator::addUnique($model, "alias");
						}

						$xml = ZipUtils::getFileContent($model["zip"]->TempName, "module.xml");
						if ($xml) {
							$xml = new SimpleXMLElement($xml);
							if (isset($xml->id) && isset($xml->name) && isset($xml->is4wfw->minVersion)) {
								if (Module::findById($xml->id)) {
									Validator::addUnique($model, "zip");
								}
								
								if (!Module::isSupportedVersion($xml->is4wfw->minVersion)) {
									Validator::addInvalidValue($model, "zip");
								}
							} else {
								Validator::addInvalidValue($model, "zip");
							}
						} else {
							Validator::addInvalidValue($model, "zip");
						}
					}
				}
            }
				
			if ($model->isSave()) {
				$file = $model["zip"];
				if ($file) {
					$tempName = $file->TempName;
					$this->installFromZip($model, $id, $tempName, $isEdit);
				}
			}
			
            if ($model->isSaved()) {
				if ($model->isValid()) {
                	$template();
				}
            }
			
            if ($model->isRender()) {
				$result = $template();
				return $result;
			}
		}

		private function validateXml(EditModel $model, SimpleXMLElement $xml, ?string $moduleId = null) {
			if (!isset($xml->is4wfw->minVersion)) {
				Validator::addInvalidValue($model, "zip");
			} else {
				if (!Module::isSupportedVersion((string)$xml->is4wfw->minVersion)) {
					Validator::addInvalidValue($model, "zip");
				}
			}
			
			if ($moduleId != null) {
				$module = Module::getById($moduleId);
				if ($module->id != $xml->id) {
					Validator::addInvalidValue($model, "zip");
				}
			}
		}

		private function installFromZip(EditModel $model, string $moduleId, string $fileName, bool $isEdit) {
			$xml = ZipUtils::getFileContent($fileName, "module.xml");
			$xml = new SimpleXMLElement($xml);
			if ($xml) {
				$this->validateXml($model, $xml, $moduleId);
			} else {
				Validator::addInvalidValue($model, "zip");
			}

			if ($model->isValid()) {
				$modules = ModuleXml::read();
				$module = Module::getById($moduleId);

				if ($isEdit) {
					$xml->alias = $module->alias;
					$this->removeModuleXml($modules, $moduleId);
				} else {
					$xml->alias = $model["alias"];
				}

				$toDom = dom_import_simplexml($modules);
				$fromDom = dom_import_simplexml($xml);
				$toDom->appendChild($toDom->ownerDocument->importNode($fromDom, true));

				ModuleXml::write($modules);

				$extractPath = MODULES_PATH . $xml->alias;
				mkdir($extractPath);

				ZipUtils::extract($fileName, $extractPath);
				ModuleGenerator::all();
				Module::reload();
				
				$postScript = FileUtils::combinePath($extractPath, $isEdit ? "postupdate.inc.php" : "postinstall.inc.php");
				$this->runScriptIsolated($postScript);

				return true;
			}

			return false;
		}

		public function rebuildInitializers($template) {
			ModuleGenerator::all();
			$template();
		}

		public function runPostUpdate($template) {
			foreach (Module::all() as $module) {
				if ($module->canEdit()) {
					$extractPath = MODULES_PATH . $module->alias;
					$postScript = FileUtils::combinePath($extractPath, "postupdate.inc.php");
					$this->runScriptIsolated($postScript);
				}
			}
			
			$template();
		}

		public function importExisting($template) {
			if ($handle = opendir(MODULES_PATH)) {
				$items = array();
				while (false !== ($entry = readdir($handle))) {
					if ($entry != "." && $entry != ".." && is_dir(MODULES_PATH . $entry)) {
						$items[] = $entry;
					}
				}
				closedir($handle);

				$newModuleAliases = [];
				foreach ($items as $alias) {
					if (Module::findByAlias($alias, false) == null) {
						$xml = file_get_contents(MODULES_PATH . $alias . "/module.xml");
						if ($xml) {
							$xml = new SimpleXMLElement($xml);
							$validation = new EditModel();
							$this->validateXml($validation, $xml);
							if ($validation->isValid()) {
								$modules = ModuleXml::read();

								$xml->alias = $alias;

								$toDom = dom_import_simplexml($modules);
								$fromDom = dom_import_simplexml($xml);
								$toDom->appendChild($toDom->ownerDocument->importNode($fromDom, true));

								ModuleXml::write($modules);
								$newModuleAliases[] = $alias;
							}
						}
					}
				}

				if (!empty($newModuleAliases)) {
					foreach ($newModuleAliases as $alias) {
						$extractPath = MODULES_PATH . $alias;
						$postScript = FileUtils::combinePath($extractPath, "postinstall.inc.php");
						$this->runScriptIsolated($postScript);
					}

					ModuleGenerator::all();
				}

				$template();
			}
		}

		public function delete($template, $id) {
			$module = Module::findById($id);
			if ($module && $module->canEdit()) {
				$xml = ModuleXml::read();
				$this->removeModuleXml($xml, $id);

				ModuleXml::write($xml);

				$preScript = FileUtils::combinePath($module->getRootPath(), "preuninstall.inc.php");
				$this->runScriptIsolated($preScript);

				FileUtils::removeDirectory($module->getRootPath());

				$this->rebuildInitializers($template);
			}
		}

		private function runScriptIsolated($script) {
			if (file_exists($script)) {
				try {
					include_once($script);
				} catch (Exception $e) {
					global $logObject;
					$logObject->exception($e);
				}
			}
		}

		private function removeModuleXml($xml, $id) {
			for ($i=0; $i < count($xml); $i++) { 
				if ($xml->module[$i]->id == $id) {
					unset($xml->module[$i]);
					return;
				}
			}
		}

		public function entrypoint($id, $params = []) {
			$this->ensureCurrent();
			return $this->web()->renderEntrypoint($this->current->id, $id, $params);
		}

		private function ensureGitHub($module) {
			if (!$module->gitHub || !$module->gitHub->repositoryName) {
				throw new Exception("Missing GitHub registration in module '$module->id'.");
			}
		}

		private function getGitHubClient($module, $userName = "", $accessToken = "") {
			$client = new GitHubReleaseClient();
			if (!$module->gitHub->isPublic) {
				if (!$userName) {
					$userName = explode("/", $module->gitHub->repositoryName)[0];
				}

				if (!$accessToken) {
					$accessToken = $module->gitHub->accessToken;
				}

				$client->setBasicAuthentication($userName, $accessToken);
			}

			return $client;
		}

		public function gitHubUpdateList($template, $moduleId, $userName, $accessToken) {
			$module = Module::getById($moduleId);
			$this->ensureGitHub($module);

			$model = new ListModel();
			$this->pushListModel($model);

			$client = $this->getGitHubClient($module, $userName, $accessToken);

			$data = $client->getList($module->gitHub->repositoryName);
			$model->render();
			if ($data["result"]) {
				$model->items($data["data"]);
			}

			$result = $template();
			
			$this->popListModel();
			return $result;
		}

		public function getGitHubUpdateList() {
			return $this->peekListModel();
		}

		public function getGitHubUpdateId() {
			return $this->peekListModel()->field("id");
		}

		public function getGitHubUpdateName() {
			return $this->peekListModel()->field("name");
		}

		public function getGitHubUpdateVersion() {
			return $this->peekListModel()->field("version");
		}

		public function getGitHubUpdatePublishedAt() {
			return $this->peekListModel()->field("published_at");
		}

		public function getGitHubUpdateHtmlUrl() {
			return $this->peekListModel()->field("html_url");
		}

		public function getGitHubUpdateSize() {
			return $this->peekListModel()->currentItem()["download"]["size"];
		}

		public function gitHubUpdate($template, $moduleId, $updateId, $userName, $accessToken) {
			$module = Module::getById($moduleId);
			$this->ensureGitHub($module);

			$client = $this->getGitHubClient($module, $userName, $accessToken);

			$fileName = tempnam(sys_get_temp_dir(), $updateId);
			$result = $client->downloadReleaseAsset($module->gitHub->repositoryName, $updateId, $fileName);
			if ($result["result"]) {
				$editModel = $this->getEditModel(false);
				if ($editModel == null) {
					$editModel = new EditModel();
				}
				$result["result"] = $this->installFromZip($editModel, $moduleId, $fileName, true);
				if ($result["result"]) {
					$template();
				}
			}
		}
    }

?>