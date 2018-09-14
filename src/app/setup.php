<?php

$targetFilePath = '../user/instance.inc.php';
$templateFilePath = 'scripts/php/includes/instance.template.php';

if (file_exists($targetFilePath)) {
    header("Location: /"); 
    exit;
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function ensureDirectory($path) {
    if (!file_exists($path)) {
        mkdir($path);
    }
}

$importFiles = scandir('data/default', SCANDIR_SORT_DESCENDING);
$importFilePath = 'data/default/' . $importFiles[0];

$isDevelopment = array_key_exists('IS4WFW_DEVELOPMENT', $_ENV) && $_ENV['IS4WFW_DEVELOPMENT'] == 'true';

if (count($_POST) == 0) {
    $_POST['database-hostname'] = '127.0.0.1';
    $_POST['database-username'] = '';
    $_POST['database-password'] = '';
    $_POST['database-database'] = '';
    $_POST['filesystem-path'] = '';
    $_POST['user-name'] = 'admin';
    $_POST['user-surname'] = 'admin';
    $_POST['user-login'] = 'admin';
    $_POST['user-password'] = '';

    if ($isDevelopment) {
        $_POST['database-hostname'] = 'db';
        $_POST['database-username'] = 'phpwfw';
        $_POST['database-password'] = '1234';
        $_POST['database-database'] = 'phpwfw';
        $_POST['filesystem-path'] = '';
        $_POST['user-name'] = 'admin';
        $_POST['user-surname'] = 'admin';
        $_POST['user-login'] = 'admin';
        $_POST['user-password'] = '123456';
    }
}

if (isset($_POST['setup-save']) && $_POST['setup-save'] == 'Setup') {
    $database = array(
        'hostname' => $_POST['database-hostname'],
        'username' => $_POST['database-username'],
        'password' => $_POST['database-password'],
        'database' => $_POST['database-database'],
    );
    $filesystem = array(
        'root' => '$_SERVER["DOCUMENT_ROOT"]',
        'path' => $_POST['filesystem-path']
    );
    $user = array(
        'name' => $_POST['user-name'],
        'surname' => $_POST['user-surname'],
        'login' => $_POST['user-login'],
        'password' => sha1($_POST['user-login'] . $_POST['user-password']) // Duplicated in User.class.php
    );

    if (array_key_exists('filesystem-path-override', $_POST) && $_POST['filesystem-path-override'] == 'on') {
        $filesystem['root'] = '"' . $filesystem['path'] . '"';
        $filesystem['path'] = '';
    }

    $templateFile = fopen($templateFilePath, 'r') or die('Cannot open file:  ' . $templateFilePath);
    $templateFileContent = fread($templateFile, filesize($templateFilePath));

    $targetFileContent = $templateFileContent;
    $targetFileContent = str_replace("{database-hostname}", $database['hostname'], $targetFileContent);
    $targetFileContent = str_replace("{database-username}", $database['username'], $targetFileContent);
    $targetFileContent = str_replace("{database-password}", $database['password'], $targetFileContent);
    $targetFileContent = str_replace("{database-database}", $database['database'], $targetFileContent);
    $targetFileContent = str_replace('$_SERVER["DOCUMENT_ROOT"]', $filesystem['root'], $targetFileContent);
    $targetFileContent = str_replace("{filesystem-path}", $filesystem['path'], $targetFileContent);
    
    ensureDirectory("../user");

    $targetFile = fopen($targetFilePath, 'w') or die('Cannot open file:  ' . $targetFilePath);
    fwrite($targetFile, $targetFileContent);

    fclose($templateFile);
    fclose($targetFile);

    require_once($targetFilePath);
    require_once("scripts/php/includes/settings.inc.php");
    require_once(APP_SCRIPTS_PHP_PATH . "libs/Database.class.php");

    $importFile = fopen($importFilePath, 'r') or die('Cannot open file:  ' . $importFilePath);

    $db = new Database(false);
    $db->disableCache();
    $db->getDataAccess()->connect(WEB_DB_HOSTNAME, WEB_DB_USER, WEB_DB_PASSWORD, WEB_DB_DATABASE, false);
    $db->getDataAccess()->setCharset('utf8');
    
    if (count($db->fetchAll("SHOW TABLES LIKE 'system_property';")) == 0) {
        $db->getDataAccess()->transaction();

        $batch = '';
        while (($line = fgets($importFile)) !== false) {
            // Skip it if it's a comment
            if (substr($line, 0, 2) == '--' || $line == '')
                continue;

            // Add this line to the current segment
            $batch .= $line;
            
            // If it has a semicolon at the end, it's the end of the query
            if (substr(trim($line), -1, 1) == ';') {

                $batch = str_replace("{user-name}", $user['name'], $batch);
                $batch = str_replace("{user-surname}", $user['surname'], $batch);
                $batch = str_replace("{user-login}", $user['login'], $batch);
                $batch = str_replace("{user-password}", $user['password'], $batch);

                // Perform the query
                if ($db->execute($batch)) {
                    print("Error performing query '<strong>" . $batch . ": " . mysql_error() . "<br /><br />");
                }

                // Reset temp variable to empty
                $batch = '';
            }
        }

        $dbObject = $db;
        require_once(APP_SCRIPTS_PHP_PATH . "includes/version.inc.php");
        require_once(APP_SCRIPTS_PHP_PATH . "includes/autoupdate.inc.php");

        $db->getDataAccess()->commit();
        $db->close();
    }

    ensureDirectory(CACHE_PATH);
    ensureDirectory(CACHE_IMAGES_PATH);
    ensureDirectory(CACHE_PAGES_PATH);
    ensureDirectory(CACHE_TEMPLATES_PATH);
    ensureDirectory(CACHE_SYSTEMPROPERTY_PATH);
    ensureDirectory(LOGS_PATH);
    ensureDirectory(USER_FILESYSTEM_PATH);
    ensureDirectory(USER_PUBLIC_PATH);

    $readmePath = USER_PATH . 'readme.txt';
    if (!file_exists($readmePath)) {
        file_put_contents($readmePath, '');
    }
    
    if (file_exists($targetFilePath)) {
        header("Location: /login.view"); 
        exit;
    }
}

?>
<!doctype html>
<html>
    <head>
        <title>Setup is4wfw</title>
        <link rel="stylesheet" href="css/admin.css" />
        <link rel="stylesheet" href="css/admin_common.css" />
        <link rel="stylesheet" href="css/admin_design.css" />
        <link rel="stylesheet" href="css/admin_features.css" />
    </head>
    <body>
        <div class="all">
            <div class="header">
                <div class="top">
                    <div class="left">
                        <h1>is4wfw</h1>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
            <div class="body">
                <div class="section">
                    <h2>
                        Setup
                    </h2>
                    <div class="clear"></div>
                </div>
                <div class="content list-folder">

                    <div class="frame frame-cover">
                        <div class="frame frame-head">
                            <div class="frame-label">Instance Settings</div>
                            <div class="frame-close">
                                <a class="click-able click-able-roll" href="#"><span>^</span></a>
                            </div>
                            <div class="clear"></div>
                        </div>
                        <div class="frame frame-body">
                            <form name="setup-edit" method="post" action="setup.php">
                                <h2>Database</h2>
                                <div class="clear"></div>
                                <div class="gray-box">
                                    <label class="w110" for="import-source">Import:</label>
                                    <input type="text" name="import-source" id="import-source" value="<?php echo basename($importFilePath, ".sql") ?>" class="w200" disabled="disabled" />
                                </div>
                                <div class="gray-box">
                                    <label class="w110" for="database-hostname">Hostname:</label>
                                    <input type="text" name="database-hostname" id="database-hostname" value="<?php echo $_POST['database-hostname'] ?>" class="w200" required="required" />
                                </div>
                                <div class="gray-box">
                                    <label class="w110" for="database-username">Username:</label>
                                    <input type="text" name="database-username" id="database-username" value="<?php echo $_POST['database-username'] ?>" class="w200" required="required" />
                                </div>
                                <div class="gray-box">
                                    <label class="w110" for="database-password">Password:</label>
                                    <input type="password" name="database-password" id="database-password" value="<?php echo $_POST['database-password'] ?>" class="w200" />
                                </div>
                                <div class="gray-box">
                                    <label class="w110" for="database-database">Database:</label>
                                    <input type="text" name="database-database" id="database-database" value="<?php echo $_POST['database-database'] ?>" class="w200" required="required" />
                                </div>

                                <h2>FileSystem</h2>
                                <div class="clear"></div>
                                <div class="gray-box">
                                    <label class="w110" for="filesystem-database">Document Root:</label>
                                    <input type="text" name="filesystem-database" id="filesystem-database" value="<?php echo $_SERVER['DOCUMENT_ROOT'] ?>" class="w300" disabled="disabled" />
                                </div>
                                <div class="gray-box">
                                    <label class="w110" for="filesystem-path" title="Start it with '/' only when Document Root doesn't end with slash.">Additional Path:</label>
                                    <input type="text" name="filesystem-path" id="filesystem-path" value="<?php echo $_POST['filesystem-path'] ?>" class="w200" />
                                    <label title="Ignore Document Root and use only this path.">
                                        <input type="checkbox" name="filesystem-path-override" />
                                        Override Document Root
                                    </label>
                                </div>

                                <h2>User</h2>
                                <div class="clear"></div>
                                <h4 class="warning">Will be ignored if database structure already exists.</h4>
                                <div class="gray-box">
                                    <label class="w110" for="user-name">Name:</label>
                                    <input type="text" name="user-name" id="user-name" value="<?php echo $_POST['user-name'] ?>" class="w200" />
                                </div>
                                <div class="gray-box">
                                    <label class="w110" for="user-surname">Surname:</label>
                                    <input type="text" name="user-surname" id="user-surname" value="<?php echo $_POST['user-surname'] ?>" class="w200" />
                                </div>
                                <div class="gray-box">
                                    <label class="w110" for="user-login">Login:</label>
                                    <input type="text" name="user-login" id="user-login" value="<?php echo $_POST['user-login'] ?>" class="w200" />
                                </div>
                                <div class="gray-box">
                                    <label class="w110" for="user-password" title="When creating user, minimal password length is 6 characters.">Password:</label>
                                    <input type="password" name="user-password" id="user-password" value="<?php echo $_POST['user-password'] ?>" class="w200" />
                                </div>

                                <hr />
                                <div class="gray-box">
                                    <input type="submit" name="setup-save" value="Setup" /> 
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </body>
</html>