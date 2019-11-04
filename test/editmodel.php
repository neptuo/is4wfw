<?php

    require_once("../user/instance.inc.php");
	require_once("../app/scripts/php/includes/settings.inc.php");
    require_once(APP_SCRIPTS_PHP_PATH . "classes/EditModel.class.php");

    $model = new EditModel();
    $model["name"] = "Joe";
    $model["surname"] = "Doe";

    $model->prefix("a");
    $model["name"] = "Helen";
    $model["surname"] = "Doe";

    $model->prefix("b");
    $model["name"] = "Jane";
    $model["surname"] = "Doe";

    var_dump(array_key_exists("surname", $model));
    var_dump($model->hasKey("surname"));

    var_dump($model);

    echo "<hr />";
    foreach ($model as $key => $value) {
        echo "Key: $key; Value: $value; <br />";
    }

?>