<pre><?php

    print_r($_SERVER);

    if(strpos($_SERVER['REDIRECT_URL'], 'service/') == -1) {
        header("HTTP/1.1 404 Not Found");
        exit;
    }

    $found = false;
    $url = split("/", $_SERVER['REDIRECT_URL']);
    //print_r($url);
    $services = new SimpleXMLElement(file_get_contents(SCRIPTS.'/config/services.xml'));
    foreach($services as $service) {
        $attrs = $service->attributes();
        if($attrs['name'] == $url[2]) {
            echo 'Service found, class: '.$attrs['class'].'<br />';

            require_once ($attrs['file']);
            $class = (string)$attrs['class'];
            $object = new $class;
            $object->handleRawRequest($_SERVER, $_GET, $_POST);
            $found = true;
            break;
        }
    }
    if(!$found) {
        echo 'No such service';
    }

?></pre>