<?php

// This file is by default restricted by .htaccess!

require_once("scripts/php/classes/dataaccess/DataAccess.class.php");
require_once("scripts/php/includes/database.inc.php");

function mylog($message, $data = null) {
    echo $message . '<br />';
    if (isset($data)) {
        echo '<pre>' . str_replace('<', '&lt;', str_replace('>', '&gt;', var_export($data))) . '</pre>';
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

            $sql .= '`' . $column . '` = "' . mysql_real_escape_string($item[$column]) . '"';
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
        mylog("SQL", $sql);
        $db->execute($sql);
        
        if ($db->getErrorCode() != 0) {
            break;
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
$database['article_content'] = array();
$database['article_content']['key'] = array('article_id', 'language_id');
$database['article_content']['columns'] = array('name', 'url', 'keywords', 'head', 'content', 'author', 'datetime');

$isSuccess = true;
$db->transaction();
foreach ($database as $tableName => $structure) {
    mylog('Processing "' . $tableName . '".');

    mysql_set_charset($defaultCharacterSet);
    $data = getData($db, $tableName, $structure);

    if ($db->getErrorCode() != 0) {
        mylog('Error running last SQL command.', array('Code' => $db->getErrorCode(), 'Message' => $db->getErrorMessage()));
        $isSuccess = false;
        break;
    }

    mylog('Found "' . count($data) . '" items.');

    mysql_set_charset($targetCharacterSet);
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