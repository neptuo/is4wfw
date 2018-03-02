<?php

require_once(APP_SCRIPTS_PHP_PATH . "libs/BaseTagLib.class.php");
require_once(APP_SCRIPTS_PHP_PATH . "classes/model/EmbeddedResource.class.php");

class EmbeddedResourceManager extends BaseTagLib {

    public static function get($erid) {
        return self::bindSingleToObject(parent::db()->fetchSingle('select `id`, `type`, `url`, `rid`, `cache` from `embedded_resource` where `id` = ' . $erid . ';'));
    }

    public static function getAll() {
        return self::bindMultiToObject(parent::db()->fetchAll('select `id`, `type`, `url`, `rid`, `cache` from `embedded_resource` order by `id`;'));
    }

    public static function validate($er, $rb) {
        $return = '';
        if (trim($er->getType()) == '') {
            $return .= parent::getError($rb->get('er.error.type'));
        }
        if (trim($er->getUrl()) == '' && trim($er->getRid()) == '') {
            $return .= parent::getError($rb->get('er.error.urlrid'));
        }
        if (trim($er->getCache()) == '') {
            $return .= parent::getError($rb->get('er.error.cache'));
        }
        return $return;
    }

    public static function update($er) {
        parent::db()->execute('update `embedded_resource` set `type` = "' . $er->getType() . '", `url` = "' . $er->geturl() . '", `rid` = ' . $er->getRid() . ', `cache` = "' . $er->getCache() . '" where `id` = ' . $er->getId() . ';');
        return $er;
    }

    public static function delete($erid) {
        parent::db()->execute('delete from `embedded_resource` where `id` = ' . $erid . ';');
    }

    public static function create($er) {
        parent::db()->execute('insert into `embedded_resource`(`type`, `url`, `rid`, `cache`) values ("' . $er->getType() . '", "' . $er->getUrl() . '", "' . $er->getRid() . '", "' . $er->getCache() . '");');
        $er->setId(parent::db()->getLastId());
        return $er;
    }

    public static function bindSingleToObject($er, $prefix = '') {
        $r = new EmbeddedResource($er[$prefix . 'id'], $er[$prefix . 'type'], $er[$prefix . 'url'], $er[$prefix . 'rid'], $er[$prefix . 'cache']);
        return $r;
    }

    public static function bindMultiToObject($ers, $prefix = '') {
        $return = array();
        foreach ($ers as $er) {
            $return[] = new EmbeddedResource($er[$prefix . 'id'], $er[$prefix . 'type'], $er[$prefix . 'url'], $er[$prefix . 'rid'], $er[$prefix . 'cache']);
        }
        return $return;
    }

    public static function types() {
        return array('TextFile', 'Image', 'Other');
    }

    public static function cache() {
        return array('Always', 'Never');
    }

}

?>