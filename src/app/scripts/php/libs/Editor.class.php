<?php

	require_once("BaseTagLib.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/EditModel.class.php");

	/**
	 * 
	 *  Class Editor.
	 *      
	 *  @author     maraf
	 *  @timestamp  2019-11-01
	 * 
	 */
	class Editor extends BaseTagLib {

		public function __construct() {
			parent::setTagLibXml("Editor.xml");
		}
		
		public function form($template, $submit, $params = []) {
            $template = parent::getParsedTemplate($template);
            
            $model = new EditModel();
            parent::setEditModel($model);

            // Načtení dat formuláře.
            $model->load();
            parent::parseContent($template);
            $model->load(false);

            if (parent::isHttpMethod("POST") && array_key_exists($submit, $_REQUEST)) {
                // Submit form / bind data into the model.
                $model->submit();
                parent::parseContent($template);
                $model->submit(false);

                if ($model->isValid()) {
                    // Save data in transaction.
                    parent::dataAccess()->transaction(function() use ($model, $template) {
                        $model->save();
                        parent::parseContent($template);
                        $model->save(false);

                        if ($model->hasException()) {
                            throw new Exception("One or more exceptions raised during save phase of EditModel. All of them should be logged.");
                        }
                    });

                    // Process after save redirects.
                    $model->saved(true);
                    parent::parseContent($template);
                    $model->saved(false);
                }
            }

            // Render UI.
            $model->render();
            $result = parent::ui()->form($template, "post", null, $params);
            parent::clearEditModel();
            return $result;
        }

        public function prefix($template, $name) {
            $model = parent::getEditModel();
            $model->prefix($name);
            $result = parent::parseContent($template);
            $model->prefix(null);
            return $result;
        }

        public function setValue($name, $value) {
            $model = parent::getEditModel(true);
            $model->set($name, -1, $value);
        }
        
        public function isRegistration() {
            $model = parent::getEditModel(true);
            return $model != null && $model->isRegistration();
        }
        
        public function isLoad() {
            $model = parent::getEditModel(true);
            return $model != null && $model->isLoad();
        }
        
        public function isSubmit() {
            $model = parent::getEditModel(true);
            return $model != null && $model->isSubmit();
        }
        
        public function isSave() {
            $model = parent::getEditModel(true);
            return $model != null && $model->isSave();
        }
        
        public function isSaved() {
            $model = parent::getEditModel(true);
            return $model != null && $model->isSaved();
        }
        
        public function getProperty($name) {
            $model = parent::getEditModel(true);
            return $model[$name];
        }
	}

?>