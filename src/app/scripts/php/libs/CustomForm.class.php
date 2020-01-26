<?php

    require_once("BaseTagLib.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/LocalizationBundle.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/FullTagParser.class.php");
    require_once(APP_SCRIPTS_PHP_PATH . "libs/FileAdmin.class.php");

    /**
     * 
     *  Class CustomForm.
     *      
     *  @author     Marek SMM
     *  @timestamp  2012-07-18
     * 
     */
    class CustomForm extends BaseTagLib {

        private $FunctionRegex = '#([a-zA-Z0-9]+)\(([^)]*)\)#';
        private $EmailRegex = '/^([a-z0-9])(([-a-z0-9._])*([a-z0-9]))*\@([a-z0-9])(([a-z0-9-])*([a-z0-9]))+(\.([a-z0-9])([-a-z0-9_-])?([a-z0-9])+)+$/i';
        private $CreatorError = "";
        private $FormPhase = 0;
        private $FormFieldsFound = array();
        private $FormId = "";
        private $GeneratedFormId = "";
        private $FormData = array();
        private $ValidationError;
        private $ResourcesToAdd = array();
        private $ViewPhase = 0;
        private $ViewFieldsFound = array();
        private $ViewAllFields = array();
        private $ViewDataRow = array();
        private $EmailPhase = 0;
        private $AdditionalKeys = array();

        public function __construct() {
            global $webObject;

            parent::setTagLibXml("CustomForm.xml");
            parent::setLocalizationBundle("customform");
        }

        /* ===================== LIST =========================================== */

        public function listRows($formId, $templateId, $rowId = false, $filter = false, $sortBy = false, $desc = false, $limit = -1, $noDataMessage = false, $params = false) {
            $templateContent = parent::getTemplateContent($templateId);
            return self::listRowsFullTag($templateContent, $formId, $rowId, $filter, $sortBy, $desc, $limit, $noDataMessage, $params);
        }

        public function listRowsFullTag($templateContent, $formId, $rowId = false, $filter = false, $sortBy = false, $desc = false, $limit = -1, $noDataMessage = false, $params = false) {
            $rb = self::rb();
            $return = "";
            $rules = "";

            $lastFormId = $this->FormId;
            $lastViewPhase = $this->ViewPhase;
            $this->FormId = $formId;

            if ($rowId != "") {
                $rules = self::listAddToRules($rules, 'id', $rowId, 'number');
            }

            foreach($params as $paramName => $paramValue) {
                $rules = self::listAddToRules($rules, $paramName, $paramValue);
            }

            $isRendered = false;
            if (self::listFindFieldsInTemplate($formId, $templateContent)) {
                $rules = self::listParseFilter($rules, $filter);
                //print_r($rules);
            
                $sql = 'select ';
                $fields = '';
                foreach ($this->ViewFieldsFound as $fi) {
                    if ($fields == '') {
                        $fields .= '`' . $fi[0] . '`';
                    } else {
                        $fields .= ', `' . $fi[0] . '`';
                    }
                }
                $sql .= $fields . ' from `cf_' . $formId . '`';
                if ($rules != '') {
                    $sql .= ' where ' . $rules;
                }
                
                $sortByParsed = explode(",", $sortBy);
                foreach($sortByParsed as $sBy) {
                    $sBy = self::listChooseSortBy($sBy);
                    if(strrpos($sql, 'order by') == '') {
                        $sql .= ' order by ';
                    } else {
                        $sql .= ', ';
                    }
                    $sql .= '`' . $sBy . '`';
                    $sql .= ( $desc == 'true') ? ' desc' : ' asc';
                }
                
                if($limit > 0) {
                    $sql .= ' limit ' . parent::db()->escape($limit);
                }
                $sql .= ';';

                $data = parent::db()->fetchAll($sql);
                if (count($data) > 0) {
                    $isRendered = true;
                    $lastIndex = parent::request()->get('i', 'custom-form');
                    $lastRowId = self::getRowId();
                    $lastViewDataRow = $this->ViewDataRow;

                    $this->ViewPhase = 2;
                    $i = 1;

                    foreach ($data as $row) {
                        parent::request()->set('i', $i, 'custom-form');
                        $this->ViewDataRow = $row;
                        self::setRowId($row['id']);

                        $Parser = new FullTagParser();
                        $Parser->setContent($templateContent);
                        $Parser->startParsing();
                        $return .= $Parser->getResult();
                        $i++;
                    }
                    
                    parent::request()->set('i', $lastIndex, 'custom-form');
                    $this->ViewDataRow = $lastViewDataRow;
                    self::setRowId($lastRowId);
                } else {
                    $return .= $noDataMessage;
                }
            } else {
                $return .= $rb->get('cf.list.error.content');
            }

            $this->ViewPhase = $lastViewPhase;
            $this->FormId = $lastFormId;

            return $return;
        }
        
        private function listParseFilter($rules, $filter) {
            $filterParsed = explode(',', $filter);
            foreach($filterParsed as $item) {
                $f = explode(':', $item, 2);
                if(count($f) == 2) {
                    $rules = self::listAddToRules($rules, $f[0], $f[1]);
                }
            }
            return $rules;
        }

        private function listAddToRules($rules, $key, $value, $type = null) {
            $Parser = new FullTagParser();
            $Parser->setUseCaching(false);
            $value = $Parser->parsePropertyExactly($value);
        
            if ($type == null) {
                if (is_numeric($value)) {
                    $type = 'number';
                } else {
                    $type = 'string';
                }
            }
        
            switch ($type) {
                case 'string': $value = '"' . $value . '"';
                case 'number': $value = $value;
            }

            if (strlen($rules) == 0) {
                $rules .= '`' . $key . '` = ' . $value;
            } else {
                $rules .= ' and `' . $key . '` = ' . $value;
            }

            return $rules;
        }

        private function listFindFieldsInTemplate($formId, $templateContent) {
            $this->ViewPhase = 1;
            $this->ViewFieldsFound = array();
            $this->ViewFieldsFound[] = array('id', 'number');

            $Parser = new FullTagParser();
            $Parser->setContent($templateContent);
            $Parser->setTagsToParse(array('cf:field', 'cf:setFieldAsCustomProperty'));
            $Parser->startParsing();
            $Parser->getResult();

            //print_r($this->ViewFieldsFound);
            $formInfo = parent::db()->fetchAll('select `fields` from `customform` where `name` = "' . $formId . '";');
            if (count($formInfo) == 1) {
                $fields = self::parseFieldsFromString($formInfo[0]['fields']);
                $fields[] = array('id', 'number');
                $this->ViewAllFields = $fields;
                $ok = true;
                foreach ($this->ViewFieldsFound as $ff) {
                    $iok = false;
                    foreach ($fields as $fi) {
                        if ($fi[0] == $ff[0]) {
                            $iok = true;
                            break;
                        }
                    }
                    if (!$iok) {
                        //parent::log(''.$ff[0].'-'.$formId.'<br />');
                        $ok = false;
                        break;
                    }
                }
            }
            return $ok;
        }

        private function listChooseSortBy($sortBy) {
            foreach ($this->ViewAllFields as $fi) {
                if ($fi[0] == $sortBy) {
                    return $sortBy;
                }
            }
            return 'id';
        }

        // C-tag.
        public function countRows($formId, $params = false) {
            $return = "";
            $rules = "";

            foreach($params as $paramName => $paramValue) {
                $rules = self::listAddToRules($rules, $paramName, $paramValue);
            }

            $sql = 'select count(`id`) as `count` from `cf_' . $formId . '`';
            if ($rules != '') {
                $sql .= ' where ' . $rules;
            }

            $data = parent::db()->fetchSingle($sql);

            $return .= $data['count'];
            return $return;
        }
        
        public function setFieldAsCustomProperty($fieldName, $type = false) {
            if ($this->ViewPhase == 1) {
                if ($type != '') {
                    $this->ViewFieldsFound[] = array($fieldName, $type);
                }
            } else {
                self::setCustomProperty($this->ViewDataRow[$fieldName]);
            }
        }
        
        public function setupCustomUrl($formId, $fieldName) {
            parent::request()->set('customurl-formid', $formId, 'cf');
            parent::request()->set('customurl-fieldname', $fieldName, 'cf');
        }
        
        /* ===================== FORM =========================================== */

        public function form($formId, $templateId, $type, $pageId, $rowId = false, $emailTemplateId = false, $emailAddresses = false, $emailSubject = false, $emailSender = false, $emailSenderFieldName = false, $emailIsHtml = false) {
            $templateContent = parent::getTemplateContent($templateId);
            return self::formFullTag($templateContent, $formId, $type, $pageId, $rowId, $emailTemplateId, $emailAddresses, $emailSubject, $emailSender, $emailSenderFieldName, $emailIsHtml);
        }

        public function formFullTag($templateContent, $formId, $type, $pageId, $rowId = false, $emailTemplateId = false, $emailAddresses = false, $emailSubject = false, $emailSender = false, $emailSenderFieldName = false, $emailIsHtml = false) {
            global $webObject;
            $rb = self::rb();
            $return = "";
            
            if (is_array($rowId)) {
                $array = $rowId;
                $rowId = $array['id'];
                $this->AdditionalKeys[$formId] = array();
                foreach ($array as $name => $value) {
                    if($name != 'id') {
                        $this->AdditionalKeys[$formId][$name] = $value;
                    }
                }
            }

            if($rowId == '') {
                $this->ViewDataRow = array();
                $rowId = $_POST['cf_row-id'];
            }
            
            $where = '';
            foreach ($this->AdditionalKeys[$formId] as $name => $value) {
                $where .= ' and `' . $name . '` = ' . $value;
            }

            if (array_key_exists('cf_gen-id', $_POST) && array_key_exists('cf_form-id', $_POST) && $_POST['cf_form-id'] == $formId) {
                // Phase 3
                //print_r($_POST);
                $this->ValidationError = false;
                self::processForm($formId, $templateContent);
                if (!self::validationError($formId, $templateContent)) {
                    if ($type == 'db') {
                        // Save data
                        $names = "";
                        $values = "";
                        $pairs = '';
                        foreach ($this->FormData[$this->FormId] as $name => $value) {
                            $editedValue = true;
                            switch ($value['type']) {
                                case 'string':
                                    $value['value'] = '"' . $value['value'] . '"';
                                    break;
                                case 'dropdown':
                                    $value['value'] = '"' . $value['value'] . '"';
                                    break;
                                case 'longstring':
                                    $value['value'] = '"' . $value['value'] . '"';
                                    break;
                                case 'date':
                                    $value['value'] = strtotime($value['value']);
                                    break;
                                case 'number':
                                    $value['value'] = $value['value'];
                                    break;
                                case 'bool':
                                    $value['value'] = self::getBoolValue($value['value']) ? '1' : '0';
                                    break;
                                case 'file':
                                    if($rowId != '' && $value['value'] == array()) {
                                        $editedValue = false;
                                    } else {
                                        $value['value'] = self::formProcessFileUpload($value['value']['file'], $value['value']['dirId']);
                                    }
                            }

                            if ($names == "") {
                                $names .= '`' . $name . '`';
                                $values .= $value['value'];
                            } else {
                                $names .= ', `' . $name . '`';
                                $values .= ', ' . $value['value'];
                            }

                            if($editedValue) {
                                if ($pairs == '') {
                                    $pairs .= '`' . $name . '` = ' . $value['value'];
                                } else {
                                    $pairs .= ', `' . $name . '` = ' . $value['value'];
                                }
                            }
                        }

                        foreach ($this->AdditionalKeys[$formId] as $name => $value) {
                            if ($names == "") {
                                $names .= '`' . $name . '`';
                                $values .= $value['value'];
                            } else {
                                $names .= ', `' . $name . '`';
                                $values .= ', ' . $value['value'];
                            }
                        }

                        if ($rowId == '') {
                            $sql = 'insert into `cf_' . $this->FormId . '`(' . $names . ') values(' . $values . ');';
                            parent::db()->execute($sql);
                        } else {
                            $countResult = parent::db()->fetchSingle('select count(`id`) as `count` from `cf_' . $this->FormId . '` where `id` = ' . $rowId . $where . ';');
                            if ($countResult['count'] == 0) {
                                $sql = 'insert into `cf_' . $this->FormId . '`(`id`, ' . $names . ') values(' . $rowId . ', ' . $values . ');';
                                parent::db()->execute($sql);
                            } else {
                                $sql = 'update `cf_' . $this->FormId . '` set ' . $pairs . ' where `id` = ' . $rowId . $where . ';';
                                parent::db()->execute($sql);
                            }
                        }

                        if ($pageId) {
                            $webObject->redirectTo($pageId);
                        }
                    } elseif ($type == 'email') {
                        $content = '';
                        $templateContent = parent::getTemplateContent($emailTemplateId);
                        $this->ViewDataRow = $this->FormData[$this->FormId];
                        $this->FormPhase = 0;
                        $this->EmailPhase = 1;

                        $Parser = new FullTagParser();
                        $Parser->setContent($templateContent);
                        $Parser->startParsing();
                        $content .= $Parser->getResult();

                        $this->EmailPhase = 0;

                        $subject = ($emailSubject == '') ? $rb->get('cf.form.email.subject') : $emailSubject;

                        $headers = array();
                        $headers[] = 'MIME-Version: 1.0';

                        if ($emailIsHtml) {
                            $headers[] = 'Content-type: text/html; charset=utf8';
                        }

                        if ($emailSenderFieldName && preg_match($this->EmailRegex, $this->ViewDataRow[$emailSenderFieldName]['value'])) {
                            $emailSender = $this->ViewDataRow[$emailSenderFieldName]['value'];
                        }

                        if (preg_match($this->EmailRegex, $emailSender)) {
                            $headers[] = 'From: ' . $emailSender;
                        }

                        $result = mail($emailAddresses, $subject, $content, implode(PHP_EOL, $headers));
                        if ($result) {
                            if($pageId) {
                                $webObject->redirectTo($pageId);
                            }

                            $this->FormPhase = 0;
                            $this->FormId = "";
                            $this->GeneratedFormId = "";
                            $this->ValidationError = false;
                        } else {
                            $this->ValidationError = true;
                        }
                    }
                } else {
                    // Show errors
                    //echo 'error';
                }
            }

            //parent::logVar(self::formValidateAgainstTemplate($formId, $templateContent));
            if ($type == 'db' && self::formValidateAgainstTemplate($formId, $templateContent)) {
                if (is_numeric($rowId)) {
                    $names = "";
                    foreach ($this->FormFieldsFound as $value) {
                        if ($names == "") {
                            $names .= '`' . $value[0] . '`';
                        } else {
                            $names .= ', `' . $value[0] . '`';
                        }
                    }

                    $row = parent::db()->fetchAll('select ' . $names . ' from `cf_' . $formId . '` where `id` = ' . $rowId .  $where . ';');
                    if (count($row) == 1) {
                        $this->ViewDataRow = $row[0];
                    } else {
                        // error
                    }
                }

                $return .= self::showForm($formId, $templateContent, $rowId);
            } else {
                if ($type == 'email') {
                    $addrs = explode(',', $emailAddresses);
                    $ok = true; //print_r($addrs);
                    foreach ($addrs as $adr) {
                        if (!preg_match($this->EmailRegex, $adr)) {
                            $ok = false;
                        }
                    }
                    if ($ok) {
                        // jiny zpusob vyrizovani formulare!!
                        $return .= self::showForm($formId, $templateContent, $rowId);
                        // ...
                    } else {
                        $msg = parent::getError($rb->get('cf.form.error.invalidemailaddress'));
                        echo $msg;
                        trigger_error($msg, E_USER_ERROR);
                    }
                } else {
                    $return .= parent::getError($rb->get('cf.form.error.content'));
                }
            }
            $this->FormPhase = 0;

            return $return;
        }

        private function formValidateAgainstTemplate($formId, $templateContent) {
            $rb = self::rb();

            $this->FormPhase = 1;
            $this->FormFieldsFound = array();

            $Parser = new FullTagParser();
            $Parser->setContent($templateContent);
            $Parser->startParsing();
            $return .= $Parser->getResult();

            $formInfo = parent::db()->fetchAll('select `fields` from `customform` where `name` = "' . $formId . '";');
            if (count($formInfo) == 1) {
                $fields = self::parseFieldsFromString($formInfo[0]['fields']);
                // parent::logVar(array('Found' => $this->FormFieldsFound, 'Additional' => $this->AdditionalKeys[$formId], 'Fields' => $fields));
                if (count($this->FormFieldsFound) + count($this->AdditionalKeys[$formId]) == count($fields)) {
                    $ok = true;
                    for ($i = 0; $i < count($fields); $i++) {
                        // parent::log($this->FormFieldsFound[$i][0].' != '.$fields[$i][0].' || '.$this->FormFieldsFound[$i][1].' != '.$fields[$i][1].' ===> '.(($this->FormFieldsFound[$i][0] != $fields[$i][0] || $this->FormFieldsFound[$i][1] != $fields[$i][1]) ? 'true' : 'false').'<br />');
                        $iok = false;
                        for ($j = 0; $j < count($this->FormFieldsFound); $j++) {
                            //echo $this->FormFieldsFound[$j][0].' == '.$fields[$i][0].' && '.$this->FormFieldsFound[$j][1].' == '.$fields[$i][1].' ===> '.(($this->FormFieldsFound[$j][0] == $fields[$i][0] && $this->FormFieldsFound[$j][1] == $fields[$i][1]) ? 'true' : 'false').'<br />';
                            if ($this->FormFieldsFound[$j][0] == $fields[$i][0] && $this->FormFieldsFound[$j][1] == $fields[$i][1]) {
                                $iok = true;
                                break;
                            }

                            if (array_key_exists($fields[$i][0], $this->AdditionalKeys[$formId])) {
                                $iok = true;
                                break;
                            }
                        }
                        if (!$iok) {
                            $ok = false;
                            break;
                        }
                    }
                    return $ok;
                }
            }

            return false;
        }

        private function showForm($formId, $templateContent, $rowId) {
            global $webObject;
            $return = '';
            $this->FormPhase = 2;

            $this->FormId = $formId;
            $this->GeneratedFormId = self::creatorChooseValue($_POST['cf_gen-id'], 'cf_' . rand());
            $this->ResourcesToAdd = '';

            if (!parent::web()->getIsInsideForm()) {
                $return .= '<form name="cf_' . $formId . '" method="post" enctype="multipart/form-data" action="' . $_SERVER['REQUEST_URI'] . '">';
            }

            $return .= ''
                . '<input type="hidden" name="cf_gen-id" value="' . $this->GeneratedFormId . '" />'
                . '<input type="hidden" name="cf_form-id" value="' . $this->FormId . '" />';

            if ($rowId != '') {
                $return .= '<input type="hidden" name="cf_row-id" value="' . $rowId . '" />';
            }

            $Parser = new FullTagParser();
            $Parser->setContent($templateContent);
            $Parser->startParsing();
            $fcontent = $Parser->getResult();

            foreach ($this->ResourcesToAdd as $res) {
                switch ($res[0]) {
                    case 'style' : $return .= '<link rel="stylesheet" type="text/css" href="' . $res[1] . '" />';
                        break;
                    case 'script': $return .= '<script type="text/javascript" src="' . $res[1] . '"></script>';
                        break;
                }
            }
            $return .= $fcontent;
            
            if (!parent::web()->getIsInsideForm()) {
                $return .= '</form>';
            }

            return $return;
        }

        private function processForm($formId, $templateContent) {
            $this->FormPhase = 3;

            $this->FormId = $formId;
            $this->GeneratedFormId = $_POST['cf_gen-id'];

            $Parser = new FullTagParser();
            $Parser->setContent($templateContent);
            $Parser->startParsing();
            $return .= $Parser->getResult();
        }

        private function formProcessFileUpload($file, $dirId) {
            $fileAdmin = new FileAdmin();
            
            $dataItem = array(
                'id' => '', 
                'url' => '', 
                'name' => time(), 
                'dir_id' => $dirId, 
                'type' => $fileAdmin->getWebFileType($file['name']), 
                'timestamp' => time()
            );
            $err = $fileAdmin->processFileUploadBasic($dataItem, $file['tmp_name']);
            return parent::dao('File')->getLastId();
        }
        
        private function validationError($formId, $templateContent) {
            return $this->ValidationError;
        }

        /* ===================== FORM VALIDATION ================================ */

        public function formValidationTag($errorMessage) {
            if ($this->ValidationError == true) {
                return $errorMessage;
            }
        }

        /* ===================== FIELD ========================================== */

        // pro date -> formatovac!!!
        public function field($name, $viewType = false, $type = false, $required = false, $validation = false, $elementId = false, $transformation = false, $default = false, $errorMessage = false, $requiredValue = false, $transient = false, $data = false, $cssClass = false, $dirId = false, $referenceFormId = false, $referenceCaptionField = false) {
            global $webObject;
            $rb = self::rb();
            $return = "";
            
            if ($this->EmailPhase == 1) {
                $fname = $this->GeneratedFormId . '_' . self::creatorChooseValue($elementId, $name);
                return $_POST[$fname];
            } elseif ($this->FormPhase == 1) {
                if ($transient != 'true') {
                    echo $transient;
                    $this->FormFieldsFound[] = array($name, $type);
                }
            } elseif ($this->FormPhase == 2) {
                $fname = $this->GeneratedFormId . '_' . self::creatorChooseValue($elementId, $name);
                $id = self::creatorChooseValue($elementId, $name);
                $value = self::fieldGetValue($name, $type, $fname, $default);
                if($requiredValue != '') {
                    parent::session()->set($fname.'-req', $requiredValue, 'cf');
                }
                if ($viewType == '' || $viewType == 'edit') {
                    switch ($type) {
                        case 'string':
                            $return .= '<input type="text" name="' . $fname . '" id="' . $id . '" value="' . $value . '" class="'.$cssClass.'" /> ';
                            if ($validation != '') {
                                // parse validation on client side
                            }
                            break;
                        case 'dropdown':
                            $return .= '<select name="' . $fname . '" id="' . $id . '" class="'.$cssClass.'"> ';
                            $items = explode(',', $data);
                            foreach ($items as $item) {
                                $return .= '<option value="' . $item . '"' . (($default == $item) ? ' selected="selected"' : '') . '>' . $item . '</option>';
                            }
                            $return .= '</select> ';
                            if ($validation != '') {
                                // parse validation on client side
                            }
                            break;
                        case 'longstring':
                            $return .= '<textarea name="' . $fname . '" id="' . $id . '" class="'.$cssClass.'">' . $value . '</textarea> ';
                            if ($validation != '') {
                                // parse validation on client side
                            }
                            break;
                        case 'number':
                            $return .= ''
                                    . '<input type="text" name="' . $fname . '" id="' . $id . '" value="' . $value . '" class="'.$cssClass.'" /> ';
                            //.self::fieldScripts('mask')
                            //.'<script type="text/javascript"> $("#'.$id.'").mask("", {placeholder:" "});</script>';
                            if ($validation != '') {
                                // parse validation on client side
                            }
                            break;
                        case 'bool':
                            $return .= ''
                                . '<input type="checkbox" name="' . $fname . '" id="' . $id . '" ' . ($value ? 'checked="checked" ' : '') . 'class="'.$cssClass.'" /> ';
                            if ($validation != '') {
                                // parse validation on client side
                            }
                            break;
                        case 'date':
                            if (is_numeric($value)) {
                                $value = date('d.m.Y', $value);
                            }
                            $return .= ''
                                    //.'<span id="'.$id.'" class="like-input"></span>'
                                    . '<input type="text" name="' . $fname . '" id="' . $id . '" value="' . $value . '" class="'.$cssClass.'" /> '
                                    //.self::fieldScripts('date')
                                    //.self::fieldScripts('mask')
                                    //.'<script type="text/javascript"> $("#'.$id.'").mask("99.99.9999", {placeholder:" "});</script>';
                                    //.'<script type="text/javascript"> $("#'.$id.'").datepicker(); </script>'
                                    //.'<style type="text/css"> .like-input { width: 100px; border: 1px solid #cccccc; } </style>';
                                    . '<script type="text/javascript">'
                                    . '$(function() { '
                                    . 'var dp = $("#' . $id . '");'
                                    . 'dp.datepicker();'
                                    . 'dp.datepicker("option", {dateFormat: "dd.mm.yy"});'
                                    . ($webObject->LanguageName != '' ? '$.datepicker.setDefaults($.datepicker.regional["' . $webObject->LanguageName . '"]);' : '')
                                    . ' });'
                                    . '</script>';
                            self::fieldAddToResource('style', "~/scripts/js/jquery-ui/css/jquery.ui.all.css");
                            self::fieldAddToResource('script', "~/scripts/js/jquery/jquery.js");
                            self::fieldAddToResource('script', "~/scripts/js/jquery-ui/jquery.ui.core.min.js");
                            self::fieldAddToResource('script', "~/scripts/js/jquery-ui/jquery.ui.widget.min.js");
                            self::fieldAddToResource('script', "~/scripts/js/jquery-ui/jquery.ui.datepicker.min.js");
                            if ($webObject->LanguageName != '') {
                                self::fieldAddToResource('script', "~/scripts/js/jquery-ui/i18n/jquery.ui.datepicker-" . $webObject->LanguageName . ".js");
                            }
                            if ($validation != '') {
                                // parse validation on client side
                            }
                            break;
                        case 'file':
                            $return .= '<input type="file" name="' . $fname . '" id="' . $id . '" value="' . $value . '" class="'.$cssClass.'" /> ';
                            break;
                        case 'reference':
                            $return .= self::fieldGenerateReferenceDropDown($referenceFormId, $referenceCaptionField, $fname, $id, $value, $cssClass);
                            break;
                        
                    }
                    if ($required == 'true') {
                        $return .= '<span class="required">*</span> ';
                    }
                    if (parent::request()->exists($fname . '_e')) {
                        $return .= '<span class="error">' . parent::request()->get($fname . '_e') . '</span>';
                    }
                } elseif ($viewType == 'value') {
                    $return .= '<span id="' . $id . '" class="'.$cssClass.'">' . $value . '</span>';
                }
            } elseif ($this->FormPhase == 3) {
                $error = false;
                $fname = $this->GeneratedFormId . '_' . self::creatorChooseValue($elementId, $name);
                $value = $_POST[$fname];
                
                $editingWithoutFile = (array_key_exists($fname, $_FILES) && $type == 'file' && array_key_exists('cf_row-id', $_POST) && $_FILES[$fname]['error'] == 4);
                $fileOk = false;
                if((array_key_exists($fname, $_FILES) && $_FILES[$fname]['error'] == 0)
                    || $editingWithoutFile
                ) {
                    $fileOk = true;
                    $value = $_FILES[$fname]['name'];
                }
                
                if ($viewType == 'edit' || $viewType == '') {
                    if (($type != 'file' && $type != 'bool' && $required == 'true' && $value == '') 
                    || ($type == 'file' && $required == 'true' && (!$fileOk || !self::fieldDirExists($dirId)))
                    || ($type == 'date' && strtotime($value) == '') 
                    || ($type == 'number' && !is_numeric($value)) 
                    || !self::fieldCustomValidation($value, $type, $validation) 
                    || ($requiredValue != '' && parent::session()->get($fname.'-req', 'cf') != $value)
                    ) {
                        $error = true;
                        parent::request()->set($fname . '_e', $errorMessage == '' ? $rb->get('cf.field.error.required') : $errorMessage);
                    } else {
                        parent::session()->clear($fname.'-req', 'cf');
                    }
                    if (!$error) {
                        if ($transient != 'true') {
                            if($fileOk) {
                                if(!$editingWithoutFile) {
                                    $value = array('file' => $_FILES[$fname], 'dirId' => $dirId);
                                } else {
                                    $value = array();
                                }
                            }
                        
                            $this->FormData[$this->FormId][$name]['type'] = $type;
                            $this->FormData[$this->FormId][$name]['value'] = $value;
                        }
                    } else {
                        //echo $name;
                        $this->ValidationError = true;
                    }
                }
            }

            if ($this->ViewPhase == 1) {
                // Add to ViewFieldsFound
                $this->ViewFieldsFound[] = array($name, $type);
            } elseif ($this->ViewPhase == 2) {
                // Return data
                if ($transformation != '') {
                    $return .= self::fieldTransformations($name, $this->ViewDataRow[$name], $transformation);
                } else {
                    if ($type == 'reference') {
                        $sql = 'select `'.$referenceCaptionField.'` from `cf_'.$referenceFormId.'` where `id` = '.$this->ViewDataRow[$name].';';
                        $data = parent::db()->fetchSingle($sql);
                        $return .= $data[$referenceCaptionField];
                    } else {
                        $return .= $this->ViewDataRow[$name];
                    }
                }
            }

            return $return;
        }
        
        private function fieldDirExists($dirId) {
            $fileAdmin = new FileAdmin();
            return $fileAdmin->canWriteDirectory(array('id' => $dirId));
        }

        private function fieldGetValue($name, $type, $fname, $default) {
            if (array_key_exists($fname, $_POST)) {
                return $_POST[$fname];
            } elseif (array_key_exists($name, $this->ViewDataRow)) {
                $val = $this->ViewDataRow[$name];
                if ($type == 'date') {
                    $val = date('d.m.Y', $this->ViewDataRow[$name]);
                } else if($type == 'bool') {
                    $val = self::getBoolValue($this->ViewDataRow[$name]);
                }
                return $val;
            } else {
                return $default;
            }
        }

        private function getBoolValue($value) {
            return $value == 'true' || $value == 'on' || $value;
        }

        private function fieldCustomValidation($value, $type, $validation) {
            if ($validation == '') {
                return true;
            } else {
                $funcs = explode(',', $validation);
                foreach ($funcs as $func) {
                    if ($func != '') {
                        $matches = array();
                        preg_match($this->FunctionRegex, $func, $matches);
                        switch ($matches[1]) {
                            case 'min':
                                if ($type == 'number' && (!is_numeric($matches[2]) || $value < $matches[2])) {
                                    return false;
                                } elseif ($type == 'string' && (!is_numeric($matches[2]) || strlen($value) < $matches[2])) {
                                    return false;
                                } elseif ($type == 'date' && strtotime($value) < strtotime($matches[2])) {
                                    return false;
                                }
                                break;
                            case 'max':
                                if ($type == 'number' && (!is_numeric($matches[2]) || $value > $matches[2])) {
                                    return false;
                                } elseif ($type == 'string' && (!is_numeric($matches[2]) || strlen($value) > $matches[2])) {
                                    return false;
                                } elseif ($type == 'date' && strtotime($value) > strtotime($matches[2])) {
                                    return false;
                                }
                                break;
                            case 'mask':
                                $escapeChars = array("\"" => "", "'" => "", "9" => "[0-9]", "a" => "[a-zA-Z]", "*" => ".", "." => "\.");
                                $regex = '(' . strtr($matches[2], $escapeChars) . ')';
                                if ($type == 'string' && !preg_match($regex, $value)) {
                                    return false;
                                }
                                break;
                        }
                    }
                }
                return true;
            }
        }

        private function fieldTransformations($name, $value, $transformation) {
            $funcs = explode(',', $transformation);
            foreach ($funcs as $func) {
                if ($func != '') {
                    $matches = array();
                    preg_match($this->FunctionRegex, $func, $matches);
                    switch ($matches[1]) {
                        case 'format':
                            $value = date($matches[2], $value);
                            break;
                        case 'toUpper':
                            $value = strtoupper($value);
                            break;
                        case 'toLower':
                            $value = strtolower($value);
                            break;
                        case 'substr':
                            $value = substr($value, 0, $matches[2]);
                            break;
                    }
                }
            }
            return $value;
        }

        private function fieldAddToResource($type, $name) {
            foreach ($this->ResourcesToAdd as $item) {
                if ($item[0] == $type && $item[1] == $name) {
                    return;
                }
            }
            $this->ResourcesToAdd[] = array($type, $name);
        }

        private function fieldGenerateReferenceDropDown($referenceFormId, $referenceCaptionField, $fname, $id, $default, $cssClass) {
            $result = '';
            
            $sql = 'select `id`, `'.$referenceCaptionField.'` from `cf_'.$referenceFormId.'` order by `'.$referenceCaptionField.'`;';
            $data = parent::db()->fetchAll($sql);
            
            $result .= '<select name="' . $fname . '" id="' . $id . '" class="'.$cssClass.'"> ';
            foreach($data as $item) {
                $result .= '<option value="' . $item['id'] . '"' . (($default == $item['id']) ? ' selected="selected"' : '') . '>' . $item[$referenceCaptionField] . '</option>';
            }
            $result .= '</select>';
            
            return $result;
        }
        
        /* ===================== BUTTON ========================================= */

        public function button($type = false, $value = false, $elementId = false) {
            $return = "";

            if ($_POST['cf-delete-row-button'] == $value) {
                $id = $_POST['cf-delete-row-id'];
                self::delete($_POST['cf-delete-form-id'], $id);
                unset($_POST['cf-delete-row-button']);
                parent::web()->redirect($_SERVER['REQUEST_URI']);
            }

            $id = self::creatorChooseValue($elementId, $this->GeneratedFormId . '_' . self::creatorChooseValue($elementId, $type));
            if ($this->FormPhase == 2 || $this->ViewPhase == 2) {
                // Generate form button
                switch ($type) {
                    case "submit":
                        $return .= '<input type="submit" name="form-submit" id="' . $id . '" value="' . $value . '" />';
                        break;
                    case "clear":
                        $return .= '<input type="reset" name="form-reset" id="' . $id . '" value="' . $value . '" />';
                        break;
                    case "delete":
                        $return .= ''
                        . '<form name="cf-delete-row" method="post" action="' . $_SERVER['REQUEST_URI'] . '">'
                            . '<input type="hidden" name="cf-delete-form-id" value="' . $this->FormId . '" />'
                            . '<input type="hidden" name="cf-delete-row-id" value="' . $this->ViewDataRow['id'] . '" />'
                            . '<input type="submit" name="cf-delete-row-button" value="' . $value . '" class="confirm" />'
                        . '</form>';
                        break;
                }
            }

            return $return;
        }

        /* ===================== SPECIAL FIELD ================================== */

        public function specialfield($type) {
            if ($this->ViewPhase == 2) {
                switch ($type) {
                    case 'idleeven': return ((parent::request()->get('i', 'custom-form') % 2) == 0 ? 'even' : 'idle');
                }
            }
        }

        /* ===================== HELPERS ======================================== */

        public function delete($formId, $rowId) {
            $additionalKeys = array();
            if(is_array($rowId)) {
                $array = $rowId;
                $rowId = $array['id'];
                foreach ($array as $name => $value) {
                    if($name != 'id') {
                        $additionalKeys[$name] = $value;
                    }
                }
            }

            if($rowId == '') {
                $this->ViewDataRow = array();
                $rowId = $_POST['cf_row-id'];
            }
            
            $where = '';
            foreach ($additionalKeys as $name => $value) {
                $where .= ' and `' . $name . '` = ' . $value;
            }

            $sql = 'delete from `cf_' . $formId . '` where `id` = ' . $rowId . $where . ';';
            parent::db()->execute($sql);
        }

        private function isFormIdFree($name) {
            $forms = parent::db()->fetchAll('select `id` from `customform` where `name` = "' . $name . '";');
            if (count($forms) == 0) {
                return true;
            } else {
                return false;
            }
        }

        private function parseFieldsFromString($fields) {
            $pairs = explode(";", $fields);
            $ret = array();
            foreach ($pairs as $pair) {
                if (strlen($pair) != 0) {
                    $ret[] = explode(":", $pair);
                }
            }
            return $ret;
        }

        /* ===================== LIST =========================================== */

        public function formList($userFrames = false) {
            $rb = self::rb();
            $return = "";

            if ($_POST['list-delete'] == $rb->get('cf.list.delete')) {
                $name = $_POST['list-id'];

                parent::db()->execute('drop table `cf_' . $name . '`;');
                parent::db()->execute('delete from `customform` where `name` = "' . $name . '";');

                $return .= parent::getSuccess($rb->get('cf.list.delete-success'));
            }

            $forms = parent::db()->fetchAll('select `id`, `name`, `fields` from `customform` order by `id`;');

            $return .= ''
                    . '<div class="gray-box">'
                    . $rb->get('cf.list.label')
                    . '<br />'
                    . $rb->get('cf.list.count-label')
                    . ' : ' . count($forms)
                    . '</div>';
            if (count($forms) > 0) {
                $return .= ''
                        . '<table class="standart">'
                        . '<tr>'
                        . '<th>' . $rb->get('cf.list.id-label') . '</th>'
                        . '<th>' . $rb->get('cf.list.name-label') . '</th>'
                        . '<th>' . $rb->get('cf.list.fields-label') . '</th>'
                        . '<th>' . $rb->get('cf.list.action-label') . '</th>'
                        . '</tr>';

                foreach ($forms as $i => $form) {
                    $return .= ''
                            . '<tr class="' . ((($i % 2) == 0) ? 'idle' : 'even') . '">'
                            . '<td class="id">' . $form['id'] . '</td>'
                            . '<td>' . $form['name'] . '</td>'
                            . '<td>' . self::listFormatFields($form['fields']) . '</td>'
                            . '<td>'
                            . '<form name="list-delete" method="post" action="' . $_SERVER['REQUEST_URI'] . '">'
                            . '<input type="hidden" name="list-id" value="' . $form['name'] . '" />'
                            . '<input class="confirm" type="image" src="~/images/page_del.png" name="list-delete" value="' . $rb->get('cf.list.delete') . '" title="' . $rb->get('cf.list.delete-title') . ', name=' . $form['name'] . '" />'
                            . '</form>'
                            . '</td>'
                            . '</tr>';
                }

                $return .= ''
                        . '</table>';
            } else {
                $return .= parent::getWarning($rb->get('cf.list.nodata'));
            }

            if ($useFrame == "false") {
                return $return;
            } else {
                if ($return != '') {
                    return parent::getFrame('Custom Form List', $return, "", true);
                }
            }
        }

        private function listFormatFields($fields) {
            $fields = str_replace(":", " : ", $fields);
            $fields = str_replace(";", ", ", $fields);
            $fields = substr($fields, 0, strlen($fields) - 2);

            return $fields;
        }
        
        public function setCustomProperty($value) {
            parent::request()->set('custom-property', $value, 'cf');
        }
        
        /* ===================== CREATOR ======================================== */

        public function formCreator($useFrames = false) {
            $rb = self::rb();
            $step = self::creatorSetStep();
            $return = "";

            if ($_POST['creator-clear'] == $rb->get('cf.creator.clear')) {
                unset($_SESSION['cf']['creator']);
                $step = 0;
            }

            if ($_POST['creator-back'] == $rb->get('cf.creator.back')) {
                $step = $_POST['creator-step'] - 1;
            }

            if ($this->CreatorError != "") {
                $return .= parent::getError($this->CreatorError);
            }

            switch ($step) {
                case 0: $return .= self::creatorStep0();
                    break;
                case 1: $return .= self::creatorStep1();
                    break;
                case 2: $return .= self::creatorStep2();
                    break;
                default:
                    $return .= parent::getWarning("Here goes Custom Form Creator.");
            }

            if ($useFrame == "false") {
                return $return;
            } else {
                if ($return != '') {
                    return parent::getFrame('Custom Form Creator :: Step ' . $step, $return, "", true);
                }
            }
        }

        private function creatorSetStep() {
            $rb = self::rb();

            if ($_POST['creator-submit'] == $rb->get('cf.creator.step0.button')) {
                if (self::creatorValidate0()) {
                    self::creatorSaveData0();
                    return 1;
                } else {
                    return 0;
                }
            } elseif ($_POST['creator-submit'] == $rb->get('cf.creator.step1.button')) {
                if (self::creatorValidate1()) {
                    self::creatorSaveData1();
                    return 2;
                } else {
                    return 1;
                }
            } elseif ($_POST['creator-submit'] == $rb->get('cf.creator.step2.button')) {
                if (self::creatorValidate2()) {
                    self::creatorSaveData2();
                    $http = $_SERVER['SERVER_PROTOCOL'];
                    $url = substr($http, 0, stripos($http, '/')) . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                    parent::redirectToUrl($url);
                    return 0;
                } else {
                    return 2;
                }
            } else {
                return 0;
            }
        }

        /*     * ********************* STEP 0 **************************************** */

        private function creatorStep0() {
            $rb = self::rb();

            $return = ''
            . '<form name="creator-step-0" method="post" action="' . $_SERVER['REQUEST_URI'] . '">'
                . '<div class="gray-box">'
                    . $rb->get('cf.creator.step0.label')
                . '</div>'
                . '<div class="gray-box">'
                    . '<label for="creator-id" class="w160">' . $rb->get('cf.creator.step0.id-label') . ':</label>'
                    . '<input type="text" name="creator-id" id="creator-id" value="' . self::creatorChooseValue($_SESSION['cf']['creator']['form-id'], $_POST['creator-id']) . '" class="w200" />'
                . '</div>'
                . '<div class="gray-box">'
                    . '<label for="creator-fields" class="w160">' . $rb->get('cf.creator.step0.fields-label') . ':</label>'
                    . '<select name="creator-fields" id="creator-fields" class="w100">'
                        . self::creatorGetOptionsForFields()
                    . '</select>'
                . '</div>'
                . '<div class="gray-box">'
                    . '<input type="submit" name="creator-submit" value="' . $rb->get('cf.creator.step0.button') . '" /> '
                    . '<input type="submit" name="creator-clear" value="' . $rb->get('cf.creator.clear') . '" />'
                . '</div>'
            . '</form>';

            return $return;
        }

        private function creatorValidate0() {
            $rb = self::rb();

            $formId = $_POST['creator-id'];
            $fields = $_POST['creator-fields'];

            // Taky testovat volnost Id
            if (strlen($formId) > 0 && is_numeric($fields) && $fields > 0 && $fields <= 50 && self::isFormIdFree($formId)) {
                return true;
            } else {
                $this->CreatorError = $rb->get('cf.creator.step0.error');
                return false;
            }
        }

        private function creatorSaveData0() {
            $_SESSION['cf']['creator']['form-id'] = $_POST['creator-id'];
            $_SESSION['cf']['creator']['fields'] = $_POST['creator-fields'];
        }

        private function creatorGetOptionsForFields() {
            $return = '';

            for ($i = 1; $i < 51; $i++) {
                $return .= '<option value="' . $i . '"' . ($i == self::creatorChooseValue($_SESSION['cf']['creator']['fields'], $_POST['creator-fields']) ? ' selected="selected"' : '') . '>' . $i . '</option>';
            }

            return $return;
        }

        /*     * ********************* STEP 1 **************************************** */

        private function creatorStep1() {
            $rb = self::rb();

            $return .= ''
            . '<form name="creator-step-1" method="post" ation="' . $_SERVER['REQUEST_URI'] . '">'
                . '<div class="gray-box">'
                    . $rb->get('cf.creator.step1.label')
                . '</div>'
                . '<div class="gray-box">'
                    . '<label for="creator-id" class="w160">' . $rb->get('cf.creator.step0.id-label') . ':</label> <strong id="creator-id">' . $_SESSION['cf']['creator']['form-id'] . '</strong>'
                    . '<br />'
                    . '<label for="creator-fields" class="w160">' . $rb->get('cf.creator.step0.fields-label') . ':</label> <strong id="creator-fields">' . $_SESSION['cf']['creator']['fields'] . '</strong>'
                . '</div>';
            
                    for ($i = 0; $i < $_SESSION['cf']['creator']['fields']; $i++) {
                $return .= ''
                . '<div class="gray-box">'
                    . '<label for="creator-step-1-i' . $i . '-name" class="w160">' . $rb->get('cf.creator.step1.name-label') . ':</label>'
                    . '<input type="text" name="creator-step-1-i' . $i . '-name" id="creator-step-1-i' . $i . '-name" value="' . self::creatorChooseValue($_SESSION['cf']['creator']['field']['i' . $i]['name'], $_POST['creator-step-1-i' . $i . '-name']) . '" class="w200" />'
                    . '<br />'
                    . '<label for="creator-step-1-i' . $i . '-type" class="w160">' . $rb->get('cf.creator.step1.type-label') . ':</label>'
                    . '<select name="creator-step-1-i' . $i . '-type" id="creator-step-1-i' . $i . '-type">'
                        . self::creatorGetDataTypes($i)
                    . '</select>'
                    . '<input type="checkbox" name="creator-step-1-i' . $i . '-primary" id="creator-step-1-i' . $i . '-primary"' . ($_SESSION['cf']['creator']['field']['i' . $i]['primary'] == 'on' ? ' checked="checked"' : '') . ' />'
                    . '<label for="creator-step-1-i' . $i . '-primary" >' . $rb->get('cf.creator.step1.primary-label') . '</label>'
                . '</div>';
            }
            
            $return .= ''
                . '<div class="gray-box">'
                    . '<input type="submit" name="creator-submit" value="' . $rb->get('cf.creator.step1.button') . '" /> '
                    . '<input type="hidden" name="creator-step" value="1" />'
                    . '<input type="submit" name="creator-back" value="' . $rb->get('cf.creator.back') . '" /> '
                    . '<input type="submit" name="creator-clear" value="' . $rb->get('cf.creator.clear') . '" /> '
                . '</div>'
            . '</form>';

            return $return;
        }

        private function creatorValidate1() {
            $rb = self::rb();

            $ok = true;
            $names = array();
            for ($i = 0; $i < $_SESSION['cf']['creator']['fields']; $i++) {
                $name = $_POST['creator-step-1-i' . $i . '-name'];
                if (strlen($name) > 2 && strtolower($name) != 'id' && !in_array($name, $names)) {
                    $names[] = $name;
                } else {
                    $this->CreatorError = $rb->get('cf.creator.step1.error');
                    $ok = false;
                }
            }

            return $ok;
        }

        private function creatorSaveData1() {
            for ($i = 0; $i < $_SESSION['cf']['creator']['fields']; $i++) {
                $name = $_POST['creator-step-1-i' . $i . '-name'];
                $type = $_POST['creator-step-1-i' . $i . '-type'];
                $primary = array_key_exists('creator-step-1-i' . $i . '-primary', $_POST) ? 'on' : '';

                $_SESSION['cf']['creator']['field']['i' . $i]['name'] = $name;
                $_SESSION['cf']['creator']['field']['i' . $i]['type'] = $type;
                $_SESSION['cf']['creator']['field']['i' . $i]['primary'] = $primary;
            }
        }

        private function creatorGetDataTypes($i) {
            $return = ''
                    . '<option' . ('number' == self::creatorChooseValue($_SESSION['cf']['creator']['field']['i' . $i]['type'], $_POST['creator-step-1-i' . $i . '-type']) ? ' selected="selected"' : '') . '>number</option>'
                    . '<option' . ('string' == self::creatorChooseValue($_SESSION['cf']['creator']['field']['i' . $i]['type'], $_POST['creator-step-1-i' . $i . '-type']) ? ' selected="selected"' : '') . '>string</option>'
                    . '<option' . ('dropdown' == self::creatorChooseValue($_SESSION['cf']['creator']['field']['i' . $i]['type'], $_POST['creator-step-1-i' . $i . '-type']) ? ' selected="selected"' : '') . '>dropdown</option>'
                    . '<option' . ('longstring' == self::creatorChooseValue($_SESSION['cf']['creator']['field']['i' . $i]['type'], $_POST['creator-step-1-i' . $i . '-type']) ? ' selected="selected"' : '') . '>longstring</option>'
                    . '<option' . ('date' == self::creatorChooseValue($_SESSION['cf']['creator']['field']['i' . $i]['type'], $_POST['creator-step-1-i' . $i . '-type']) ? ' selected="selected"' : '') . '>date</option>'
                    . '<option' . ('bool' == self::creatorChooseValue($_SESSION['cf']['creator']['field']['i' . $i]['type'], $_POST['creator-step-1-i' . $i . '-type']) ? ' selected="selected"' : '') . '>bool</option>'
                    . '<option' . ('file' == self::creatorChooseValue($_SESSION['cf']['creator']['field']['i' . $i]['type'], $_POST['creator-step-1-i' . $i . '-type']) ? ' selected="selected"' : '') . '>file</option>'
                    . '<option' . ('reference' == self::creatorChooseValue($_SESSION['cf']['creator']['field']['i' . $i]['type'], $_POST['creator-step-1-i' . $i . '-type']) ? ' selected="selected"' : '') . '>reference</option>';

            return $return;
        }

        private function creatorTranslateType($type) {
            switch ($type) {
                case "number": return "INT";
                case "string": return "TINYTEXT";
                case "dropdown": return "TINYTEXT";
                case "longstring": return "TEXT";
                case "date": return "INT";
                case "bool": return "BIT";
                case "file": return "INT";
                case "reference": return "INT";
            }
        }

        private function creatorChooseValue($val1, $val2) {
            if ($val1 != "") {
                return $val1;
            } else {
                return $val2;
            }
        }

        /*     * ********************* STEP 2 **************************************** */

        private function creatorStep2() {
            $rb = self::rb();

            $return .= ''
            . '<form name="creator-step-2" method="post" action="' . $_SERVER['REQUEST_URI'] . '">'
                . '<div class="gray-box">'
                    . $rb->get('cf.creator.step2.label')
                . '</div>'
                . '<div class="gray-box">'
                    . '<label for="creator-id" class="w160">' . $rb->get('cf.creator.step0.id-label') . ':</label> '
                    . '<strong id="creator-id">' . $_SESSION['cf']['creator']['form-id'] . '</strong>'
                    . '<br />'
                    . '<label for="creator-fields" class="w160">' . $rb->get('cf.creator.step0.fields-label') . ':</label> '
                    . '<strong id="creator-fields">' . $_SESSION['cf']['creator']['fields'] . '</strong>'
                . '</div>';

            for ($i = 0; $i < $_SESSION['cf']['creator']['fields']; $i++) {
                $return .= ''
                . '<div class="gray-box">'
                    . '<label for="creator-step-1-i' . $i . '-name" class="w160">' . $rb->get('cf.creator.step1.name-label') . ':</label> '
                    . '<strong id="creator-step-1-i' . $i . '-name">' . $_SESSION['cf']['creator']['field']['i' . $i]['name'] . '</strong>'
                    . '<br />'
                    . '<label for="creator-step-1-i' . $i . '-type" class="w160">' . $rb->get('cf.creator.step1.type-label') . ':</label> '
                    . '<strong id="creator-step-1-i' . $i . '-type">' . $_SESSION['cf']['creator']['field']['i' . $i]['type'] . '</strong>'
                . '</div>';
            }

            $return .=''
                . '<div class="gray-box">'
                    . '<label for="creator-overview">' . $rb->get('cr.creator.step2.formoverview1-label') . '</label>'
                    . '<textarea id="creator-overview" rows="1">' . self::creatorFormOverview1() . '</textarea>'
                . '</div>'
                . '<div class="gray-box">'
                    . '<label for="creator-overview">' . $rb->get('cr.creator.step2.formoverview2-label') . '</label>'
                    . '<textarea id="creator-overview" rows="15">' . self::creatorFormOverview2() . '</textarea>'
                . '</div>'
                . '<div class="gray-box">'
                    . '<input type="submit" name="creator-submit" value="' . $rb->get('cf.creator.step2.button') . '" /> '
                    . '<input type="hidden" name="creator-step" value="2" />'
                    . '<input type="submit" name="creator-back" value="' . $rb->get('cf.creator.back') . '" /> '
                    . '<input type="submit" name="creator-clear" value="' . $rb->get('cf.creator.clear') . '" /> '
                . '</div>'
            . '</form>';

            return $return;
        }

        private function creatorValidate2() {
            return true;
        }

        private function creatorSaveData2() {
            // create form
            $name = $_SESSION['cf']['creator']['form-id'];
            $fields = "";
            $create = 'CREATE TABLE `cf_' . $name . '` (`id` INT NOT NULL AUTO_INCREMENT';
            $primary = ', PRIMARY KEY (`id`';
            for ($i = 0; $i < $_SESSION['cf']['creator']['fields']; $i++) {
                $fields .= $_SESSION['cf']['creator']['field']['i' . $i]['name'] . ':' . $_SESSION['cf']['creator']['field']['i' . $i]['type'] . ';';
                if ($_SESSION['cf']['creator']['field']['i' . $i]['primary'] == 'on') {
                    $primary .= ', `' . $_SESSION['cf']['creator']['field']['i' . $i]['name'] . '`';
                }
                $create .= ', `' . $_SESSION['cf']['creator']['field']['i' . $i]['name'] . '` ' . self::creatorTranslateType($_SESSION['cf']['creator']['field']['i' . $i]['type']) . ' NOT NULL';
            }
            $primary .= ')';
            parent::db()->execute('insert into `customform`(`name`, `fields`) values("' . $name . '", "' . $fields . '");');
            $create .= $primary . ') ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci ROW_FORMAT=FIXED;';
            parent::db()->execute($create);

            // clear session
            unset($_SESSION['cf']['creator']);

            // go to step0
        }

        private function creatorFormOverview1() {
            $return = '<cf:form formId="' . $_SESSION['cf']['creator']['form-id'] . '" templateId="TEMPLATE_ID" type="db" pageId="PAGE_ID_FOR_REDIRECTION" />';
            $return = str_replace('<', '&lt;', $return);
            $return = str_replace('>', '&gt;', $return);
            return $return;
        }

        private function creatorFormOverview2() {
            $return = '';

            for ($i = 0; $i < $_SESSION['cf']['creator']['fields']; $i++) {
                $id = $_SESSION['cf']['creator']['form-id'] . '-' . $_SESSION['cf']['creator']['field']['i' . $i]['name'];
                $type = strtolower($_SESSION['cf']['creator']['field']['i' . $i]['type']);
                $return .= ''
                        . '<div class="gray-box">
    '
                        . '    <label for="' . $id . '">' . ucfirst($_SESSION['cf']['creator']['field']['i' . $i]['name']) . ':</label>
    '
                        . '    <cf:field name="' . $_SESSION['cf']['creator']['field']['i' . $i]['name'] . '" type="' . $type . '" elementId="' . $id . '" required="true"'.($type == 'file' ? ' dirId="DIRECTORY_ID"' : '').($type == 'reference' ? ' referenceFormId="TARGET_FORM_ID" referenceCaptionField="CAPTION_FIELD"' : '').' />
    '
                        . '</div>
    ';
            }

            $return .= ''
                    . '<div class="gray-box">
    '
                    . '    <cf:button type="submit" value="Save" />
    '
                    . '</div>
    ';

            $return = str_replace('<', '&lt;', $return);
            $return = str_replace('>', '&gt;', $return);

            return $return;
        }

        /* ================== PROPERTIES ================================================== */

        public function setRowId($id) {
            //echo '<br />Setting row id: '.$id;
            if ($id != '') {
                parent::request()->set('custom-form-row-id', $id);
            } else {
                parent::request()->set('custom-form-row-id', -1);
            }
            return $id;
        }

        public function getRowId() {
            if (parent::request()->exists('custom-form-row-id')) {
                return parent::request()->get('custom-form-row-id');
            } else {
                return -1;
            }
        }

        public function setCustom($value) {
            return $value;
        }
        
        public function getCustom() {
            return parent::request()->get('custom-property', 'cf');
        }
        
        public function setCustomUrl($value) {
            $formId = parent::request()->get('customurl-formid', 'cf');
            $fieldName = parent::request()->get('customurl-fieldname', 'cf');
            
            if($formId != '' && $fieldName != '') {
                $sql = 'select `id`, `'.$fieldName.'` from `cf_'.$formId.'`';
                $data = parent::db()->fetchAll($sql);
                foreach($data as $item) {
                    if(parent::convertToUrlValid($item[$fieldName]) == $value) {
                        self::setRowId($item['id']);
                        return $value;
                    }
                }
            }
            
            return "-1===-1";
        }
        
        public function getCustomUrl() {
            $fieldName = parent::request()->get('customurl-fieldname', 'cf');
            return parent::convertToUrlValid($this->ViewDataRow[$fieldName]);
        }
    }

?>