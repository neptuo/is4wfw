<?php

$targetDirectoryPath = 'scripts/php/includes';
$targetFilePath = $targetDirectoryPath . '/instance.inc.php';
$templateFilePath = $targetDirectoryPath . '/instance.template.php';

if (file_exists($targetFilePath)) {
    header("Location: /"); 
    exit;
}

require_once("scripts/php/libs/User.class.php");

$importFiles = scandir('data/default', SCANDIR_SORT_DESCENDING);
$importFilePath = 'data/default/' . $importFiles[0];

if (isset($_POST['setup-save']) && $_POST['setup-save'] == 'Setup') {
    $database = array(
        'hostname' => $_POST['database-hostname'],
        'username' => $_POST['database-username'],
        'password' => $_POST['database-password'],
        'database' => $_POST['database-database'],
    );
    $filesystem = array(
        'path' => $_POST['filesystem-path']
    );
    $user = array(
        'name' => $_POST['user-name'],
        'surname' => $_POST['user-surname'],
        'login' => $_POST['user-login'],
        'password' => User::hashPassword($_POST['user-name'], $_POST['user-password'])
    );

    print_r($database);
    print_r($filesystem);
    print_r($user);
    print_r($_POST['user-password']);
    exit;

    $templateFile = fopen($templateFilePath, 'r') or die('Cannot open file:  ' . $templateFilePath);
    $templateFileContent = fread($templateFile, filesize($templateFilePath));

    $targetFileContent = $templateFileContent;
    $targetFileContent = str_replace("{database-hostname}", $database['hostname'], $targetFileContent);
    $targetFileContent = str_replace("{database-username}", $database['username'], $targetFileContent);
    $targetFileContent = str_replace("{database-password}", $database['password'], $targetFileContent);
    $targetFileContent = str_replace("{database-database}", $database['database'], $targetFileContent);
    $targetFileContent = str_replace("{filesystem-path}", $filesystem['path'], $targetFileContent);

    $targetFile = fopen($targetFilePath, 'w') or die('Cannot open file:  ' . $targetFilePath);
    fwrite($targetFile, $targetFileContent);

    fclose($templateFile);
    fclose($targetFile);

    require_once($targetFilePath);
    require_once("scripts/php/classes/dataaccess/DataAccess.class.php");

    $importFile = fopen($importFilePath, 'r') or die('Cannot open file:  ' . $importFilePath);

    $db = new DataAccess();
    $db->disableCache();
    $db->connect(WEB_DB_HOSTNAME, WEB_DB_USER, WEB_DB_PASSWORD, WEB_DB_DATABASE, false);
    $db->setCharset('utf8');
    
    $db->transaction();

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
    require_once("scripts/php/includes/autoupdate.inc.php");

    $db->commit();
    $db->disconnect();
    
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
        <link rel="stylesheet" href="scripts/css/admin.css" />
        <link rel="stylesheet" href="scripts/css/admin_common.css" />
        <link rel="stylesheet" href="scripts/css/admin_design.css" />
        <link rel="stylesheet" href="scripts/css/admin_features.css" />
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
                                    <input type="text" name="database-hostname" id="database-hostname" value="127.0.0.1" class="w200" required="required" />
                                </div>
                                <div class="gray-box">
                                    <label class="w110" for="database-username">Username:</label>
                                    <input type="text" name="database-username" id="database-username" class="w200" required="required" />
                                </div>
                                <div class="gray-box">
                                    <label class="w110" for="database-password">Password:</label>
                                    <input type="password" name="database-password" id="database-password" class="w200" />
                                </div>
                                <div class="gray-box">
                                    <label class="w110" for="database-database">Database:</label>
                                    <input type="text" name="database-database" id="database-database" class="w200" required="required" />
                                </div>

                                <h2>FileSystem</h2>
                                <div class="clear"></div>
                                <div class="gray-box">
                                    <label class="w110" for="filesystem-database">Script Document:</label>
                                    <input type="text" name="filesystem-database" id="filesystem-database" value="<?php echo $_SERVER['DOCUMENT_ROOT'] ?>" class="w300" disabled="disabled" />
                                </div>
                                <div class="gray-box">
                                    <label class="w110" for="filesystem-path" title="Must start with '/'">Additional Path:</label>
                                    <input type="text" name="filesystem-path" id="filesystem-path" class="w200" required="/" />
                                </div>

                                <h2>User</h2>
                                <div class="clear"></div>
                                <div class="gray-box">
                                    <label class="w110" for="user-name">Name:</label>
                                    <input type="text" name="user-name" id="user-name" value="admin" class="w200" />
                                </div>
                                <div class="gray-box">
                                    <label class="w110" for="user-surname">Name:</label>
                                    <input type="text" name="user-surname" id="user-surname" value="admin" class="w200" />
                                </div>
                                <div class="gray-box">
                                    <label class="w110" for="user-login">Login:</label>
                                    <input type="text" name="user-login" id="user-login" value="admin" class="w200" required="required" />
                                </div>
                                <div class="gray-box">
                                    <label class="w110" for="user-password">Password:</label>
                                    <input type="password" name="user-password" id="user-password" class="w200" required="required" minlength="6" />
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