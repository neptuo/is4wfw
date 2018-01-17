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

if ($_POST['install-save'] == 'Install') {
    $data = array(
        'hostname' => $_POST['install-hostname'],
        'username' => $_POST['install-username'],
        'password' => $_POST['install-password'],
        'database' => $_POST['install-database'],
    );

    $templateFile = fopen($templateFilePath, 'r') or die('Cannot open file:  ' . $templateFilePath);
    $templateFileContent = fread($templateFile, filesize($templateFilePath));

    $targetFileContent = $templateFileContent;
    $targetFileContent = str_replace("{hostname}", $data['hostname'], $targetFileContent);
    $targetFileContent = str_replace("{username}", $data['username'], $targetFileContent);
    $targetFileContent = str_replace("{password}", $data['password'], $targetFileContent);
    $targetFileContent = str_replace("{database}", $data['database'], $targetFileContent);

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
        <title>Install is4wfw</title>
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
                        Install
                    </h2>
                    <div class="clear"></div>
                </div>
                <div class="content list-folder">

                    <div class="frame frame-cover">
                        <div class="frame frame-head">
                            <div class="frame-label">Database Settings</div>
                            <div class="frame-close">
                                <a class="click-able click-able-roll" href="#"><span>^</span></a>
                            </div>
                            <div class="clear"></div>
                        </div>
                        <div class="frame frame-body">
                            <form name="install-edit" method="post" action="install.php">
                                <div class="gray-box">
                                    <label class="w160" for="install-import">Import:</label>
                                    <input type="text" name="install-hostname" id="install-hostname" value="<?php echo basename($importFilePath, ".sql") ?>" class="w200" disabled="disabled" />
                                </div>
                                <div class="gray-box">
                                    <label class="w160" for="install-hostname">Hostname:</label>
                                    <input type="text" name="install-hostname" id="install-hostname" value="127.0.0.1" class="w200" />
                                </div>
                                <div class="gray-box">
                                    <label class="w160" for="install-username">Username:</label>
                                    <input type="text" name="install-username" id="install-username" class="w200" />
                                </div>
                                <div class="gray-box">
                                    <label class="w160" for="install-password">Password:</label>
                                    <input type="password" name="install-password" id="install-password" class="w200" />
                                </div>
                                <div class="gray-box">
                                    <label class="w160" for="install-database">Database:</label>
                                    <input type="text" name="install-database" id="install-database" class="w200" />
                                </div>
                                <div class="gray-box">
                                    <input type="submit" name="install-save" value="Install" /> 
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </body>
</html>