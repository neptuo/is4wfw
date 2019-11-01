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
		
		public function form($template, $submit) {
            $template = parent::getParsedTemplate($template);
            
            $model = new EditModel();
            parent::setMainEditModel($model);

            // Načtení dat formuláře.
            $model->load();
            self::parseContent($template);
            $model->load(false);

            if (self::isHttpMethod("POST") && array_key_exists($submit, $_REQUEST)) {
                // Submit form.
                $model->submit();
                self::parseContent($template);
                $model->submit(false);

                // Process after save redirects.
                $model->saved(true);
                self::parseContent($template);
                $model->saved(false);
            }

            // Render UI.
            $model->render();
            $result = self::ui()->form($template, "post");
            parent::clearMainEditModel();
            return $result;
        }

        public function prefix($template, $name) {
            parent::setEditModelPrefix($name);
            $result = parent::parseContent($template);
            parent::clearEditModelPrefix();
            return $result;
        }

        public function setValue($name, $value) {
            $model = parent::getMainEditModel();
            $model->set($name, -1, $value);
        }
        
        public function isRegistration() {
            $model = parent::getMainEditModel();
            return $model != null && $model->isRegistration();
        }
        
        public function isLoad() {
            $model = parent::getMainEditModel();
            return $model != null && $model->isLoad();
        }
        
        public function isSubmit() {
            $model = parent::getMainEditModel();
            return $model != null && $model->isSubmit();
        }
        
        public function isSaved() {
            $model = parent::getMainEditModel();
            return $model != null && $model->isSaved();
        }
	}

?>