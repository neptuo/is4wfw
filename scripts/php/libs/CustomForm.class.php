<?php

/**
 *
 *  Require base tag lib class.
 *
 */
require_once("BaseTagLib.class.php");

require_once("scripts/php/classes/ResourceBundle.class.php");
require_once("scripts/php/classes/CustomTagParser.class.php");

/**
 * 
 *  Class CustomForm.
 *      
 *  @author     Marek SMM
 *  @timestamp  2011-01-10
 * 
 */
class CustomForm extends BaseTagLib {

    private $BundleName = 'customform';
    private $BundleLang = 'en';
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

    public function __construct() {
        global $webObject;

        parent::setTagLibXml("xml/CustomForm.xml");

        if ($webObject->LanguageName != '') {
            $rb = new ResourceBundle();
            if ($rb->testBundleExists($this->BundleName, $webObject->LanguageName)) {
                $this->BundleLang = $webObject->LanguageName;
            }
        }
    }

    /* ===================== LIST =========================================== */

    public function listRows($formId, $templateId, $rowId = false, $sortBy = false, $desc = false, $noDataMessage = false) {
        global $webObject;
        $rb = new ResourceBundle();
        $rb->loadBundle($this->BundleName, $this->BundleLang);
        $return = "";
        $rules = "";

        $this->FormId = $formId;

        $templateContent = parent::getTemplateContent($templateId);

        if ($rowId != "") {
            $rules = self::listAddToRules($rules, 'id', $rowId, 'number');
        }

        if (self::listFindFieldsInTemplate($formId, $templateContent)) {
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

            $sortBy = self::listChooseSortBy($sortBy);
            $sql .= ' order by `' . $sortBy . '`';
            $sql .= ( $desc == 'true') ? ' desc' : ' asc';
            $sql .= ';';

            $data = parent::db()->fetchAll($sql);
            if (count($data) > 0) {
                $this->ViewPhase = 2;
                $i = 1;
                foreach ($data as $row) {
                    parent::request()->set('i', $i, 'custom-form');
                    $this->ViewDataRow = $row;
                    self::setRowId($row['id']);

                    $Parser = new CustomTagParser();
                    $Parser->setContent($templateContent);
                    $Parser->startParsing();
                    $return .= $Parser->getResult();
                    $i++;
                }
            } else {
                $return .= $noDataMessage;
            }
        } else {
            $return .= $rb->get('cf.list.error.content');
        }

        return $return;
    }

    private function listAddToRules($rules, $key, $value, $type) {
        switch ($type) {
            case 'string': $value = '"' . $value . '"';
            case 'number': $value = $value;
        }
        if (strlen($rules) == 0) {
            $rules .= '`' . $key . '` = ' . $value;
        } else {
            $rules .= ', `' . $key . '` = ' . $value;
        }
        return $rules;
    }

    private function listFindFieldsInTemplate($formId, $templateContent) {
        $this->ViewPhase = 1;
        $this->ViewFieldsFound = array();
        $this->ViewFieldsFound[] = array('id', 'number');

        $Parser = new CustomTagParser();
        $Parser->setContent($templateContent);
        $Parser->startParsing();
        $Parser->getResult();

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

    /* ===================== FORM =========================================== */

    public function form($formId, $templateId, $type, $pageId, $rowId = false, $emailTemplateId = false, $emailAddresses = false, $emailSubject = false) {
        global $webObject;
        $rb = new ResourceBundle();
        $rb->loadBundle($this->BundleName, $this->BundleLang);
        $return = "";

        $templateContent = parent::getTemplateContent($templateId);

        if (array_key_exists('cf_gen-id', $_POST) && array_key_exists('cf_form-id', $_POST) && $_POST['cf_form-id'] == $formId) {
            // Phase 3
            $this->ValidationError = false;
            self::processForm($formId, $templateContent);
            if (!self::validationError($formId, $templateContent)) {
                if ($type == 'db') {
                    // Save data
                    $names = "";
                    $values = "";
                    $pairs = '';
                    foreach ($this->FormData[$this->FormId] as $name => $value) {
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
                        }

                        if (!array_key_exists('cf_row-id', $_POST)) {
                            if ($names == "") {
                                $names .= '`' . $name . '`';
                                $values .= $value['value'];
                            } else {
                                $names .= ', `' . $name . '`';
                                $values .= ', ' . $value['value'];
                            }
                        } else {
                            if ($pairs == '') {
                                $pairs .= '`' . $name . '` = ' . $value['value'];
                            } else {
                                $pairs .= ', `' . $name . '` = ' . $value['value'];
                            }
                        }
                    }

                    if (!array_key_exists('cf_row-id', $_POST)) {
                        $sql = 'insert into `cf_' . $this->FormId . '`(' . $names . ') values(' . $values . ');';
                        parent::db()->execute($sql);
                    } else {
                        $sql = 'update `cf_' . $this->FormId . '` set ' . $pairs . ' where `id` = ' . $_POST['cf_row-id'] . ';';
                        parent::db()->execute($sql);
                    }
                    $webObject->redirectTo($pageId);
                } elseif ($type == 'email') {
                    $content = '';
                    $templateContent = parent::getTemplateContent($emailTemplateId);
                    $this->ViewDataRow = $this->FormData[$this->FormId];
                    $this->FormPhase = 0;
                    $this->EmailPhase = 1;

                    $Parser = new CustomTagParser();
                    $Parser->setContent($templateContent);
                    $Parser->startParsing();
                    $content .= $Parser->getResult();

                    $this->EmailPhase = 0;

                    $subject = ($emailSubject == '') ? 'Email from CustomForms' : $emailSubject;

                    //echo $content;
                    // Nastaveni SMTP??
                    //ini_set("SMTP", "smtp.google.com");
                    //ini_set('sendmail_from', 'user@example.com');
                    mail($emailAddresses, $subject, $content);

                    $webObject->redirectTo($pageId);
                }

                $this->FormPhase = 0;
                $this->FormId = "";
                $this->GeneratedFormId = "";
                $this->ValidationError = false;
            } else {
                // Show errors
                //echo 'error';
            }
        }

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

                $row = parent::db()->fetchAll('select ' . $names . ' from `cf_' . $formId . '` where `id` = ' . $rowId . ';');
                if (count($row) == 1) {
                    $this->ViewDataRow = $row[0];
                } else {
                    // error
                }
            }

            $return .= self::showForm($formId, $templateContent, $rowId);
        } else {
            if ($type == 'email') {
                $addrs = split(',', $emailAddresses);
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

        return $return;
    }

    private function formValidateAgainstTemplate($formId, $templateContent) {
        $rb = new ResourceBundle();
        $rb->loadBundle($this->BundleName, $this->BundleLang);

        $this->FormPhase = 1;
        $this->FormFieldsFound = array();

        $Parser = new CustomTagParser();
        $Parser->setContent($templateContent);
        $Parser->startParsing();
        $return .= $Parser->getResult();

        $formInfo = parent::db()->fetchAll('select `fields` from `customform` where `name` = "' . $formId . '";');
        if (count($formInfo) == 1) {
            $fields = self::parseFieldsFromString($formInfo[0]['fields']);
            //print_r($fields);
            //print_r($this->FormFieldsFound);
            if (count($this->FormFieldsFound) == count($fields)) {
                $ok = true;
                for ($i = 0; $i < count($fields); $i++) {
                    //echo $this->FormFieldsFound[$i][0].' != '.$fields[$i][0].' || '.$this->FormFieldsFound[$i][1].' != '.$fields[$i][1].' ===> '.(($this->FormFieldsFound[$i][0] != $fields[$i][0] || $this->FormFieldsFound[$i][1] != $fields[$i][1]) ? 'true' : 'false').'<br />';
                    $iok = false;
                    for ($j = 0; $j < count($this->FormFieldsFound); $j++) {
                        //echo $this->FormFieldsFound[$j][0].' == '.$fields[$i][0].' && '.$this->FormFieldsFound[$j][1].' == '.$fields[$i][1].' ===> '.(($this->FormFieldsFound[$j][0] == $fields[$i][0] && $this->FormFieldsFound[$j][1] == $fields[$i][1]) ? 'true' : 'false').'<br />';
                        if ($this->FormFieldsFound[$j][0] == $fields[$i][0] && $this->FormFieldsFound[$j][1] == $fields[$i][1]) {
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
        } else {
            return false;
        }
    }

    private function showForm($formId, $templateContent, $rowId) {
        global $webObject;
        $return = '';
        $this->FormPhase = 2;

        $this->FormId = $formId;
        $this->GeneratedFormId = self::creatorChooseValue($_POST['cf_gen-id'], 'cf_' . rand());
        $this->ResourcesToAdd = '';

        $return .= ''
                . '<form name="cf_' . $formId . '" method="post" action="">'
                . '<input type="hidden" name="cf_gen-id" value="' . $this->GeneratedFormId . '" />'
                . '<input type="hidden" name="cf_form-id" value="' . $this->FormId . '" />';
        if ($rowId != '') {
            $return .= ''
                    . '<input type="hidden" name="cf_row-id" value="' . $rowId . '" />';
        }

        $Parser = new CustomTagParser();
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
        $return .= '</form>';

        return $return;
    }

    private function processForm($formId, $templateContent) {
        $this->FormPhase = 3;

        $this->FormId = $formId;
        $this->GeneratedFormId = $_POST['cf_gen-id'];

        $Parser = new CustomTagParser();
        $Parser->setContent($templateContent);
        $Parser->startParsing();
        $return .= $Parser->getResult();
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
    public function field($name, $viewType = false, $type = false, $required = false, $validation = false, $elementId = false, $transformation = false, $default = false, $errorMessage = false, $requiredValue = false, $transient = false, $data = false) {
        global $webObject;
        $rb = new ResourceBundle();
        $rb->loadBundle($this->BundleName, $this->BundleLang);
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
                        $return .= '<input type="text" name="' . $fname . '" id="' . $id . '" value="' . $value . '" /> ';
                        if ($validation != '') {
                            // parse validation on client side
                        }
                        break;
                    case 'dropdown':
                        $return .= '<select name="' . $fname . '" id="' . $id . '"> ';
                        $items = split(',', $data);
                        foreach ($items as $item) {
                            $return .= '<option value="' . $item . '"' . (($default == $item) ? ' selected="selected"' : '') . '>' . $item . '</option>';
                        }
                        $return .= '</select> ';
                        if ($validation != '') {
                            // parse validation on client side
                        }
                        break;
                    case 'longstring':
                        $return .= '<textarea name="' . $fname . '" id="' . $id . '">' . $value . '</textarea> ';
                        if ($validation != '') {
                            // parse validation on client side
                        }
                        break;
                    case 'number':
                        $return .= ''
                                . '<input type="text" name="' . $fname . '" id="' . $id . '" value="' . $value . '" /> ';
                        //.self::fieldScripts('mask')
                        //.'<script type="text/javascript"> $("#'.$id.'").mask("", {placeholder:" "});</script>';
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
                                . '<input type="text" name="' . $fname . '" id="' . $id . '" value="' . $value . '" /> '
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
                }
                if ($required == 'true') {
                    $return .= '<span class="required">*</span> ';
                }
                if (parent::request()->exists($fname . '_e')) {
                    $return .= '<span class="error">' . parent::request()->get($fname . '_e') . '</span>';
                }
            } elseif ($viewType == 'value') {
                $return .= '<span id="' . $id . '">' . $value . '</span>';
            }
        } elseif ($this->FormPhase == 3) {
            $error = false;
            $fname = $this->GeneratedFormId . '_' . self::creatorChooseValue($elementId, $name);
            $value = $_POST[$fname];
            if ($viewType == 'edit' || $viewType == '') {
                if (($required == 'true' && $value == '') || ($type == 'date' && strtotime($value) == '') || !self::fieldCustomValidation($value, $type, $validation) || ($requiredValue != '' && parent::session()->get($fname.'-req', 'cf') != $value)) {
                    $error = true;
                    parent::request()->set($fname . '_e', $errorMessage == '' ? $rb->get('cf.field.error.required') : $errorMessage);
                } else {
                    parent::session()->clear($fname.'-req', 'cf');
                }
                if (!$error) {
                    if ($transient != 'true') {
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
                $return .= $this->ViewDataRow[$name];
            }
        }

        return $return;
    }

    private function fieldGetValue($name, $type, $fname, $default) {
        if (array_key_exists($fname, $_POST)) {
            return $_POST[$fname];
        } elseif (array_key_exists($name, $this->ViewDataRow)) {
            $val = $this->ViewDataRow[$name];
            if ($type == 'date') {
                $val = date('d.m.Y', $this->ViewDataRow[$name]);
            }
            return $val;
        } else {
            return $default;
        }
    }

    private function fieldCustomValidation($value, $type, $validation) {
        if ($validation == '') {
            return true;
        } else {
            $funcs = split(',', $validation);
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
        $funcs = split(',', $transformation);
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

    /* ===================== BUTTON ========================================= */

    public function button($type = false, $value = false, $elementId = false) {
        $return = "";

        if ($_POST['cf-delete-row-button'] == $value) {
            $id = $_POST['cf-delete-row-id'];
            $sql = 'delete from `cf_' . $this->FormId . '` where `id` = ' . $id . ';';
            parent::db()->execute($sql);
            unset($_POST['cf-delete-row-button']);
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
                            . '<form name="cf-delete-row" method="post" action="">'
                            . '<input type="hidden" name="cf-delete-row-id" value="' . $this->ViewDataRow['id'] . '" />'
                            . '<input type="submit" name="cf-delete-row-button" value="' . $value . '" />'
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

    private function isFormIdFree($name) {
        $forms = parent::db()->fetchAll('select `id` from `customform` where `name` = "' . $name . '";');
        if (count($forms) == 0) {
            return true;
        } else {
            return false;
        }
    }

    private function parseFieldsFromString($fields) {
        $pairs = split(";", $fields);
        $ret = array();
        foreach ($pairs as $pair) {
            if (strlen($pair) != 0) {
                $ret[] = split(":", $pair);
            }
        }
        return $ret;
    }

    /* ===================== LIST =========================================== */

    public function formList($userFrames = false) {
        $rb = new ResourceBundle();
        $rb->loadBundle($this->BundleName, $this->BundleLang);
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
                        . '<form name="list-delete" method="post" action="">'
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
        $rb = new ResourceBundle();
        $rb->loadBundle($this->BundleName, $this->BundleLang);
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
        $rb = new ResourceBundle();
        $rb->loadBundle($this->BundleName, $this->BundleLang);

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
                $url = substr($http, 0, stripos($http, '/')) . '://' . $_SERVER['SERVER_NAME'] . $_SERVER['REDIRECT_URL'];
                header('Location: ' . $url);
                exit;
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
        $rb = new ResourceBundle();
        $rb->loadBundle($this->BundleName, $this->BundleLang);

        $return = ''
                . '<form name="creator-step-0" method="post" action="">'
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
        $rb = new ResourceBundle();
        $rb->loadBundle($this->BundleName, $this->BundleLang);

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
        $rb = new ResourceBundle();
        $rb->loadBundle($this->BundleName, $this->BundleLang);

        $return .= ''
                . '<form name="creator-step-1" method="post" ation="">'
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
        $rb = new ResourceBundle();
        $rb->loadBundle($this->BundleName, $this->BundleLang);

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

            $_SESSION['cf']['creator']['field']['i' . $i]['name'] = $name;
            $_SESSION['cf']['creator']['field']['i' . $i]['type'] = $type;
        }
    }

    private function creatorGetDataTypes($i) {
        $return = ''
                . '<option' . ('number' == self::creatorChooseValue($_SESSION['cf']['creator']['field']['i' . $i]['type'], $_POST['creator-step-1-i' . $i . '-type']) ? ' selected="selected"' : '') . '>number</option>'
                . '<option' . ('string' == self::creatorChooseValue($_SESSION['cf']['creator']['field']['i' . $i]['type'], $_POST['creator-step-1-i' . $i . '-type']) ? ' selected="selected"' : '') . '>string</option>'
                . '<option' . ('dropdown' == self::creatorChooseValue($_SESSION['cf']['creator']['field']['i' . $i]['type'], $_POST['creator-step-1-i' . $i . '-type']) ? ' selected="selected"' : '') . '>dropdown</option>'
                . '<option' . ('longstring' == self::creatorChooseValue($_SESSION['cf']['creator']['field']['i' . $i]['type'], $_POST['creator-step-1-i' . $i . '-type']) ? ' selected="selected"' : '') . '>longstring</option>'
                . '<option' . ('date' == self::creatorChooseValue($_SESSION['cf']['creator']['field']['i' . $i]['type'], $_POST['creator-step-1-i' . $i . '-type']) ? ' selected="selected"' : '') . '>date</option>';

        return $return;
    }

    private function creatorTranslateType($type) {
        switch ($type) {
            case "number": return "INT";
            case "string": return "TINYTEXT";
            case "dropdown": return "TINYTEXT";
            case "longstring": return "TEXT";
            case "date": return "INT";
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
        $rb = new ResourceBundle();
        $rb->loadBundle($this->BundleName, $this->BundleLang);

        $return .= ''
                . '<form name="creator-step-2" method="post" action="">'
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
        $create = 'CREATE TABLE `cf_' . $name . '` (`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY';
        for ($i = 0; $i < $_SESSION['cf']['creator']['fields']; $i++) {
            $fields .= $_SESSION['cf']['creator']['field']['i' . $i]['name'] . ':' . $_SESSION['cf']['creator']['field']['i' . $i]['type'] . ';';

            $create .= ', `' . $_SESSION['cf']['creator']['field']['i' . $i]['name'] . '`' . self::creatorTranslateType($_SESSION['cf']['creator']['field']['i' . $i]['type']) . ' NOT NULL';
        }
        parent::db()->execute('insert into `customform`(`name`, `fields`) values("' . $name . '", "' . $fields . '");');
        $create .= ');';
        parent::db()->execute($create);

        // clear session
        unset($_SESSION['cf']['creator']);

        // go to step0
    }

    private function creatorFormOverview1() {
        $return = '<c:form formId="' . $_SESSION['cf']['creator']['form-id'] . '" templateId="TEMPLATE_ID" type="db" pageId="PAGE_ID_FOR_REDIRECTION" />';
        $return = str_replace('<', '&lt;', $return);
        $return = str_replace('>', '&gt;', $return);
        return $return;
    }

    private function creatorFormOverview2() {
        $return = '';

        for ($i = 0; $i < $_SESSION['cf']['creator']['fields']; $i++) {
            $id = $_SESSION['cf']['creator']['form-id'] . '-' . $_SESSION['cf']['creator']['field']['i' . $i]['name'];
            $return .= ''
                    . '<p>
'
                    . '    <label for="' . $id . '">' . ucfirst($_SESSION['cf']['creator']['field']['i' . $i]['name']) . ':</label>
'
                    . '    <c:field name="' . $_SESSION['cf']['creator']['field']['i' . $i]['name'] . '" type="' . strtolower($_SESSION['cf']['creator']['field']['i' . $i]['type']) . '" elementId="' . $id . '" required="true" />
'
                    . '</p>
';
        }

        $return .= ''
                . '<p>
'
                . '    <c:button type="submit" value="Save" />
'
                . '</p>
';

        $return = str_replace('<', '&lt;', $return);
        $return = str_replace('>', '&gt;', $return);

        return $return;
    }

    /* ================== PROPERTIES ================================================== */

    public function setRowId($id) {
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
		return $value
	}
	
	public function getCustom() {
		return parent::request()->get('custom-property', $value, 'cf');
	}
}

?>