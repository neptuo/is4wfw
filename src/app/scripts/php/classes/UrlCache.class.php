<?php

    require_once(APP_SCRIPTS_PHP_PATH . "libs/BaseTagLib.class.php");

    /**
     *
     *  UrlCache
     *  DB Table: id, http, https, url, domain_url, root_url, virtual_url, project_id, pages_id, language_id, cachetime, lastcache
     *
     */
    class UrlCache extends BaseTagLib {
    
        public function __construct() { }
        
        public function read($url) {
            if (WEB_USE_URLCACHE) {
                return parent::db()->fetchSingle('select `url`, `http`, `https`, `domain_url`, `root_url`, `virtual_url`, `project_id`, `pages_id`, `language_id`, `cachetime`, `lastcache` from `urlcache` where `url` = "'.$url.'";');
            }
            return array();
        }
        
        public function write($fullUrl, $webProject, $pages, $lang, $cachetime) {
            if (WEB_USE_URLCACHE) {
                $sql = 'insert into `urlcache`(`url`, `http`, `https`, `domain_url`, `root_url`, `virtual_url`, `project_id`, `pages_id`, `language_id`, `cachetime`, `lastcache`) values ("'.$fullUrl.'", '.$webProject['alias']['http'].', '.$webProject['alias']['https'].', "'.$webProject['alias']['domain_url'].'", "'.$webProject['alias']['root_url'].'", "'.$webProject['alias']['virtual_url'].'", '.$webProject['id'].', "'.$pages.'", '.$lang['id'].', '.$cachetime.', '.time().');';
                parent::db()->execute($sql);
            }
        }
        
        public function delete($url) {
            if (WEB_USE_URLCACHE) {
                parent::db()->execute('delete from `urlcache` where `url` = "'.$url.'";');
            }
        }
        
        public function updateLastCache($url) {
            if (WEB_USE_URLCACHE) {
                parent::db()->execute('update `urlcache` set `lastcache` = '.time().' where `url` = "'.$url.'";');
            }
        }
    }

?>