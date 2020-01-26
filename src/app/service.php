<pre><?php

    print_r($_SERVER);

    if (strpos($_SERVER['REQUEST_URI'], 'service/') == -1) {
        header("HTTP/1.1 404 Not Found");
        exit;
    }

    $found = false;
    $url = explode("/", $_SERVER['REQUEST_URI']);
    // print_r($url);
    $services = new SimpleXMLElement(file_get_contents(APP_SCRIPTS_PATH . '/config/services.xml'));
    foreach ($services as $service) {
        $attrs = $service->attributes();
        if ($attrs['name'] == $url[2]) {
            // echo 'Service found, class: ' . $attrs['class'] . '<br />';

            require_once(APP_SCRIPTS_PHP_PATH . 'classes/service/' . $attrs['file']);
            $class = (string)$attrs['class'];
            $object = new $class;
            $object->handleRawRequest($_SERVER, $_GET, $_POST);
            $found = true;
            break;
        }
    }

    if (!$found) {
        header("HTTP/1.1 404 Not Found");
        echo 'No such service';
    }

?></pre>