<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of WebForward
 *
 * @author Mara
 */
class WebForward {
    private $id;
    private $type;
    private $rule;
    private $condition;
    private $enabled;
    private $pageId;
    private $langId;
    private $order;

    public function  __construct($id, $type, $rule, $condition, $pageId, $langId, $order, $enabled) {
        self::setId($id);
        self::setType($type);
        self::setRule($rule);
        self::setCondition($condition);
        self::setEnabled($enabled);
        self::setPageId($pageId);
        self::setOrder($order);
        self::setLangId($langId);
    }

    public function getId() {
        return $this->id;
    }
    public function setId($id) {
        $this->id = $id;
    }

    public function getType() {
        return $this->type;
    }
    public function setType($type) {
        $this->type = $type;
    }

    public function getRule() {
        return $this->rule;
    }
    public function setRule($rule) {
        $this->rule = $rule;
    }

    public function getCondition() {
        return $this->condition;
    }
    public function setCondition($condition) {
        $this->condition = $condition;
    }

    public function getPageId() {
        return $this->pageId;
    }
    public function setPageId($pageId) {
        $this->pageId = $pageId;
    }

    public function getLangId() {
        return $this->langId;
    }
    public function setLangId($langId) {
        $this->langId = $langId;
    }

    public function getOrder() {
        return $this->order;
    }
    public function setOrder($order) {
        $this->order = $order;
    }

    public function getEnabled() {
        return $this->enabled;
    }
    public function setEnabled($enabled) {
        $this->enabled = $enabled;
    }
    
}

?>
