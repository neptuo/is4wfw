<?php

    echo '<pre>';

function addUrlParameter($url, $name, $value = '') {
    $pair = ($value != '') ? $name . '=' . $value : $name;

    $queryIndex = strpos($url, '?');
    if ($queryIndex == '') {
        $url .= '?' . $pair;
    } else {
        echo 'pos: ' . strpos($url, $name, $queryIndex) . '<br />';
        if (strpos($url, $name, $queryIndex) == '') {
            $url .= '&' . $pair;
        } else {
            $query = substr($url, $queryIndex + 1);
            $query = explode('&', $query);
            $url = substr($url, 0, $queryIndex);
            $isFirst = true;
            foreach ($query as $item) {
                $keyvalue = explode('=', $item);
                if ($keyvalue[0] == $name) {
                    $item = $pair;
                }

                if ($isFirst) {
                    $url .= '?' . $item;
                    $isFirst = false;
                } else {
                    $url .= '&' . $item;
                }
            }
        }
    }
    return $url;
}

    $source = '/webadmin/in/aktuality/detail?article-id=4&language-id=2&editing';
    $target = addUrlParameter($source, 'language-id', 3);

    
    print_r(array('source' => $source, 'target' => $target));

    echo '</pre>';
?>