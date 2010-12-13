<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 *  Require base tag lib class.
 *
 */
require_once("scripts/php/libs/BaseTagLib.class.php");
require_once("scripts/php/classes/model/WebForward.class.php");

/**
 * Description of WebForwardManager
 *
 * @author Mara
 */
class WebForwardManager extends BaseTagLib {

    public static function get($id) {
        return self::bindSingleToObject(parent::db()->fetchSingle('select `id`, `type`, `rule`, `condition`, `page_id`, `lang_id`, `order`, `enabled` from `web_forward` where `id` = ' . $id . ';'));
    }

    public static function getAll() {
        return self::bindMultiToObject(parent::db()->fetchAll('select `id`, `type`, `rule`, `condition`, `page_id`, `lang_id`, `order`, `enabled` from `web_forward` order by `id`;'));
    }

    public static function getBy($conditions, $order) {
        $conditions = self::prepareConditions($conditions);
        $order = self::prepareOrder($order);
        return self::bindMultiToObject(parent::db()->fetchAll('select `id`, `type`, `rule`, `condition`, `page_id`, `lang_id`, `order`, `enabled` from `web_forward` '.$conditions.' '.$order.';'));
    }

    public static function validate($wf, $rb) {
        $return = '';
        if (trim($wf->getType()) == '') {
            $return .= parent::getError($rb->get('wf.error.type'));
        }
        if (trim($wf->getRule()) == '') {
            $return .= parent::getError($rb->get('wf.error.rule'));
        }
        if (trim($wf->getCondition()) == '') {
            $return .= parent::getError($rb->get('wf.error.condition'));
        }
        if (trim($wf->getPageId()) == '') {
            $return .= parent::getError($rb->get('wf.error.page'));
        }
        if (trim($wf->getLangId()) == '') {
            $return .= parent::getError($rb->get('wf.error.lang'));
        }
        if (trim($wf->getOrder()) == '') {
            $return .= parent::getError($rb->get('wf.error.order'));
        }

        return $return;
    }

    public static function update($wf) {
        parent::db()->execute('update `web_forward` set `type` = "' . $wf->getType() . '", `rule` = "' . $wf->getRule() . '", `condition` = "' . $wf->getCondition() . '", `page_id` = ' . $wf->getPageId() . ', `lang_id` = ' . $wf->getLangId() . ', `order` = ' . $wf->getOrder() . ', `enabled` = ' . $wf->getEnabled() . ' where `id` = ' . $wf->getId() . ';');
        return $wf;
    }

    public static function delete($id) {
        parent::db()->execute('delete from `web_forward` where `id` = ' . $id . ';');
    }

    public static function create($wf) {
        parent::db()->execute('insert into `web_forward`(`type`, `rule`, `condition`, `page_id`, `lang_id`, `order`, `enabled`) values ("' . $wf->getType() . '", "' . $wf->getRule() . '", "' . $wf->getCondition() . '", ' . $wf->getPageId() . ', ' . $wf->getLangId() . ', ' . $wf->getOrder() . ', ' . $wf->getEnabled() . ');');
        $wf->setId(parent::db()->getLastId());
        return $wf;
    }

    public static function bindSingleToObject($wf, $prefix = '') {
        $r = new WebForward($wf[$prefix . 'id'], $wf[$prefix . 'type'], $wf[$prefix . 'rule'], $wf[$prefix . 'condition'], $wf[$prefix . 'page_id'], $wf[$prefix . 'lang_id'], $wf[$prefix . 'order'], $wf[$prefix . 'enabled']);
        return $r;
    }

    public static function bindMultiToObject($wfs, $prefix = '') {
        $return = array();
        foreach ($wfs as $wf) {
            $return[] = self::bindSingleToObject($wf, $prefix);
        }
        return $return;
    }

    public static function type() {
        return array('Forward', 'Substitute');
    }

    public static function condition() {
        return array('Always', '403', '404', 'All Errors');
    }

    private static function prepareConditions($conditions) {
        $return = '';
        foreach ($conditions as $key => $cond) {
            if ($return != '') {
                $return .= 'and ';
            }
            if(is_string($cond)) {
                $cond = '"'.$cond.'"';
            }
            if(is_array($cond)) {
                $con = '';
                foreach($cond as $c) {
                    if($con != '') {
                        $con .= ', ';
                    }
                    if(is_string($c)) {
                        $con .= '"'.$c.'"';
                    } else {
                        $con .= $c;
                    }
                }
                if($con != '') {
                    $cond = 'in ('.$con.')';
                } else {
                    continue;
                }
                $return .= '`'.$key.'` '.$cond.' ';
            } else {
                $return .= '`'.$key.'` = '.$cond.' ';
            }
            
        }
        if($return != '') {
            $return = 'where '.$return;
        }
        return $return;
    }

    private static function prepareOrder($orders) {
        $return = '';
        foreach($orders as $order) {
            if($return != '') {
                $return .= ', ';
            }
            $return .= '`'.$order.'`';
        }
        if($return != '') {
            $return = 'order by '.$return;
        }
        return $return;
    }

}

?>
