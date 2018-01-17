<?php

$targetDirectoryPath = 'scripts/php/includes';
$targetFilePath = $targetDirectoryPath . '/database.inc.php';
$templateFilePath = $targetDirectoryPath . '/database.template.php';

if (file_exists($targetFilePath)) {
    header("Location: /"); 
    exit;
}

$importFiles = scandir('data/default', SCANDIR_SORT_DESCENDING);
$importFilePath = 'data/default/' . $importFiles[0];

if (isset($_POST['setup-save']) && $_POST['setup-save'] == 'Setup') {
    $database = array(
        'hostname' => $_POST['database-hostname'],
        'username' => $_POST['database-username'],
        'password' => $_POST['database-password'],
        'database' => $_POST['database-database'],
    );

    $templateFile = fopen($templateFilePath, 'r') or die('Cannot open file:  ' . $templateFilePath);
    $templateFileContent = fread($templateFile, filesize($templateFilePath));

    $targetFileContent = $templateFileContent;
    $targetFileContent = str_replace("{hostname}", $database['hostname'], $targetFileContent);
    $targetFileContent = str_replace("{username}", $database['username'], $targetFileContent);
    $targetFileContent = str_replace("{password}", $database['password'], $targetFileContent);
    $targetFileContent = str_replace("{database}", $database['database'], $targetFileContent);

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
        $templine .= $line;
        
        // If it has a semicolon at the end, it's the end of the query
        if (substr(trim($line), -1, 1) == ';') {
            // Perform the query
            if ($db->execute($templine)) {
                print('Error performing query \'<strong>' . $templine . '\': ' . mysql_error() . '<br /><br />');
            }

            // Reset temp variable to empty
            $templine = '';
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
                                    <label class="w160" for="database-import">Import:</label>
                                    <input type="text" name="database-hostname" id="database-hostname" value="<?php echo basename($importFilePath, ".sql") ?>" class="w200" disabled="disabled" />
                                </div>
                                <div class="gray-box">
                                    <label class="w160" for="database-hostname">Hostname:</label>
                                    <input type="text" name="database-hostname" id="database-hostname" value="127.0.0.1" class="w200" />
                                </div>
                                <div class="gray-box">
                                    <label class="w160" for="database-username">Username:</label>
                                    <input type="text" name="database-username" id="database-username" class="w200" />
                                </div>
                                <div class="gray-box">
                                    <label class="w160" for="database-password">Password:</label>
                                    <input type="password" name="database-password" id="database-password" class="w200" />
                                </div>
                                <div class="gray-box">
                                    <label class="w160" for="database-database">Database:</label>
                                    <input type="text" name="database-database" id="database-database" class="w200" />
                                </div>

                                <h2>FileSystem</h2>
                                <div class="clear"></div>
                                <div class="gray-box">
                                    <label class="w160" for="filesystem-database">Script Document:</label>
                                    <input type="text" name="filesystem-database" id="filesystem-database" value="<?php echo $_SERVER['DOCUMENT_ROOT'] ?>" class="w300" disabled="disabled" />
                                </div>
                                <div class="gray-box">
                                    <label class="w160" for="filesystem-path">Additional Path:</label>
                                    <input type="text" name="filesystem-path" id="filesystem-path" class="w200" />
                                </div>

                                <h2>User</h2>
                                <div class="clear"></div>
                                <div class="gray-box">
                                    <label class="w160" for="user-username">Username:</label>
                                    <input type="text" name="user-username" id="user-username" value="admin" class="w200" />
                                </div>
                                <div class="gray-box">
                                    <label class="w160" for="user-password">Password:</label>
                                    <input type="password" name="user-password" id="user-password" class="w200" />
                                </div>
                                <div class="gray-box">
                                    <label class="w160" for="user-password2">Password Again:</label>
                                    <input type="password" name="user-password2" id="user-password2" class="w200" />
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