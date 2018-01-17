<?php

// This file is by default restricted by .htaccess!

require_once("scripts/php/includes/instance.inc.php");
require_once("scripts/php/classes/dataaccess/DataAccess.class.php");

function mylog($message, $data = null) {
    echo $message . '<br />';
    if (isset($data)) {
        echo '<pre>';
        echo str_replace('<', '&lt;', str_replace('>', '&gt;', var_export($data))) . '</pre>';
    }
}

function getData($db, $tableName, $structure) {
    $sql = 'SELECT ';
    $isFirst = true;

    foreach ($structure['key'] as $column) {
        if(!$isFirst) {
            $sql .= ', ';
        }

        $sql .= '`' . $column . '`';
        $isFirst = false;
    }

    foreach ($structure['columns'] as $column) {
        if(!$isFirst) {
            $sql .= ', ';
        }

        $sql .= '`' . $column . '`';
        $isFirst = false;
    }

    $sql .= ' FROM `' . $tableName . '`;';
    $data = $db->fetchAll($sql);
    return $data;
}

function updateData($db, $tableName, $structure, $data) {
    foreach ($data as $item) {
        $sql = 'UPDATE `' . $tableName . '` SET ';
        
        $isFirst = true;
        foreach ($structure['columns'] as $column) {
            if(!$isFirst) {
                $sql .= ', ';
            }

            $sql .= '`' . $column . '` = "' . $db->escape($item[$column]) . '"';
            $isFirst = false;
        }

        $sql .= ' WHERE ';

        $isFirst = true;
        foreach ($structure['key'] as $column) {
            if(!$isFirst) {
                $sql .= ' AND ';
            }
    
            $sql .= '`' . $column . '` = ' . $item[$column] . '';
            $isFirst = false;
        }

        $sql .= ';';
        $db->execute($sql);
        
        if ($db->getErrorCode() != 0) {
            break;
        }
    }
}

function convertData($data, $sourceCharacterSet, $targetCharacterSet) {
    foreach ($data as $item) {
        foreach($item as $key => $value) {
            $item[$key] = mb_convert_encoding($value, $targetCharacterSet, $sourceCharacterSet);
        }
    }
}

$db = new DataAccess();
$db->disableCache();
$db->connect(WEB_DB_HOSTNAME, WEB_DB_USER, WEB_DB_PASSWORD, WEB_DB_DATABASE);

$defaultCharacterSet = $db->getCharset();
$targetCharacterSet = 'utf8';
mylog('Default character set is "' . $defaultCharacterSet . '".');
mylog('Target character set is "' . $targetCharacterSet . '".');
if ($defaultCharacterSet == $targetCharacterSet) {
    mylog('Nothing to do.');
    exit;
}

$database = array();

$database['article_content'] = array(
    'key' => array('article_id', 'language_id'),
    'columns' => array('name', 'url', 'keywords', 'head', 'content', 'author', 'datetime')
);
$database['article_label'] = array(
    'key' => array('id'),
    'columns' => array('name', 'url')
);
$database['article_label_language'] = array(
    'key' => array('label_id', 'language_id'),
    'columns' => array('name', 'url')
);
$database['article_line'] = array(
    'key' => array('id'),
    'columns' => array('name', 'url')
);
$database['content'] = array(
    'key' => array('page_id', 'language_id'),
    'columns' => array('tag_lib_start', 'tag_lib_end', 'head', 'content')
);
$database['customform'] = array(
    'key' => array('id'),
    'columns' => array('name', 'fields')
);
$database['db_connection'] = array(
    'key' => array('id'),
    'columns' => array('name', 'hostname', 'user', 'password', 'database', 'fs_root')
);
$database['directory'] = array(
    'key' => array('id'),
    'columns' => array('name', 'url')
);
$database['embedded_resource'] = array(
    'key' => array('id'),
    'columns' => array('type', 'url')
);
$database['file'] = array(
    'key' => array('id'),
    'columns' => array('name', 'title', 'url')
);
$database['group'] = array(
    'key' => array('gid'),
    'columns' => array('name')
);
$database['group_perms'] = array(
    'key' => array('id'),
    'columns' => array('name')
);
$database['guestbook'] = array(
    'key' => array('id'),
    'columns' => array('name', 'content')
);
$database['info'] = array(
    'key' => array('page_id', 'language_id'),
    'columns' => array('name', 'title', 'href', 'keywords')
);
$database['inquiry'] = array(
    'key' => array('id'),
    'columns' => array('question')
);
$database['inquiry_answer'] = array(
    'key' => array('id'),
    'columns' => array('answer')
);
$database['language'] = array(
    'key' => array('id'),
    'columns' => array('language')
);
$database['page_file'] = array(
    'key' => array('id'),
    'columns' => array('name', 'content')
);
$database['page_property_value'] = array(
    'key' => array('id'),
    'columns' => array('name', 'value')
);
$database['personal_note'] = array(
    'key' => array('id'),
    'columns' => array('value')
);
$database['personal_property'] = array(
    'key' => array('id'),
    'columns' => array('name', 'value')
);
$database['system_adminmenu'] = array(
    'key' => array('id'),
    'columns' => array('name', 'icon', 'perm')
);
$database['system_property'] = array(
    'key' => array('id'),
    'columns' => array('key', 'value')
);
$database['template'] = array(
    'key' => array('id'),
    'columns' => array('name', 'content')
);
$database['urlcache'] = array(
    'key' => array('id'),
    'columns' => array('url', 'domain_url', 'root_url', 'virtual_url', 'pages_id')
);
$database['user'] = array(
    'key' => array('uid'),
    'columns' => array('name', 'surname', 'login')
);

$forms = $db->fetchAll('select `id`, `name`, `fields` from `customform` order by `id`;');
foreach ($forms as $form) {
    $name = 'cf_' . $form['name'];
    $database[$name] = array();
    $database[$name]['key'] = array();
    $database[$name]['columns'] = array();

    $primaryKeys = $db->fetchAll('SHOW KEYS FROM `' . $name . '` WHERE Key_name = "PRIMARY";');
    foreach ($primaryKeys as $primaryKey) {
        $database[$name]['key'][] = $primaryKey['Column_name'];
    }

    $fields = explode(";", $form['fields']);
    foreach ($fields as $field) {
        $fieldDefinition = explode(":", $field);
        if (strlen($fieldDefinition[0]) > 0) {
            $database[$name]['columns'][] = $fieldDefinition[0];
        }
    }
}

$isSuccess = true;
$db->transaction();
foreach ($database as $tableName => $structure) {
    mylog('Processing "' . $tableName . '".');

    $db->setCharset($defaultCharacterSet);
    $data = getData($db, $tableName, $structure);

    if ($db->getErrorCode() != 0) {
        mylog('Error running last SQL command.', array('Code' => $db->getErrorCode(), 'Message' => $db->getErrorMessage()));
        $isSuccess = false;
        break;
    }

    mylog('Found "' . count($data) . '" items.');
    convertData($data, $defaultCharacterSet, $targetCharacterSet);

    $db->setCharset($targetCharacterSet);
    updateData($db, $tableName, $structure, $data);
    
    if ($db->getErrorCode() != 0) {
        mylog('Error running last SQL command.', array('Code' => $db->getErrorCode(), 'Message' => $db->getErrorMessage()));
        $isSuccess = false;
        break;
    }
}

if ($isSuccess) {
    $db->execute('INSERT INTO `system_property`(`key`, `value`) VALUES("' . DataAccess::$CharsetSystemProperty . '", "' . $targetCharacterSet . '");');
    $db->commit();
} else {
    $db->rollback();
}

$db->disconnect();

?>