<?php

class Order {

    public static function upForm($actionUrl, $prefix, $id, $title) {
        return ''
            . '<form name="' . $prefix . '-move-up" method="post" action="' . $actionUrl . '">'
                . '<input type="hidden" name="' . $prefix . '-id" value="' . $id . '" />'
                . '<input type="hidden" name="' . $prefix . '-move-up" value="move-up" />'
                . '<input type="image" src="~/images/arro_up.png" name="' . $prefix . '-move-up" value="move-up" title="' . $title . '" /> '
            . '</form> ';
    }
    
    public static function downForm($actionUrl, $prefix, $id, $title) {
        return ''
            . '<form name="' . $prefix . '-move-down" method="post" action="' . $actionUrl . '">'
                . '<input type="hidden" name="' . $prefix . '-id" value="' . $id . '" />'
                . '<input type="hidden" name="' . $prefix . '-move-down" value="move-down" />'
                . '<input type="image" src="~/images/arro_do.png" name="' . $prefix . '-move-down" value="move-down" title="' . $title . '" /> '
            . '</form> ';
    }

    public static function tryUpdate($rows, $prefix, $table, $id, $order) {
        global $dbObject;

        if($_POST[$prefix . '-move-up'] == 'move-up') {
            $rowId = $_POST[$prefix . '-id'];

            $current = null;
            $prev = null;
            foreach ($rows as $row) {
                if ($row[$id] == $rowId) {
                    $current = $row;
                    break;
                }

                $prev = $row;
            }

            if ($current != null && $prev != null) {
                $value = $prev[$order];
                $prev[$order] = $current[$order];
                $current[$order] = $value;
                
                $dbObject->execute('UPDATE `' . $table . '` SET `' . $order . '` = '. $prev[$order] .' where `' . $id . '` = ' . $prev[$id] . ';');
                $dbObject->execute('UPDATE `' . $table . '` SET `' . $order . '` = '. $current[$order] .' where `' . $id . '` = ' . $current[$id] . ';');
                return true;
            }
        } else if($_POST[$prefix . '-move-down'] == 'move-down') {
            $rowId = $_POST[$prefix . '-id'];

            $current = null;
            $next = null;
            $isMatched = false;
            foreach ($rows as $row) {
                if($isMatched) {
                    $next = $row;
                    break;
                }

                if($row[$id] == $rowId) {
                    $current = $row;
                    $isMatched = true;
                }
            }

            if ($current != null && $next != null) {
                $value = $next[$order];
                $next[$order] = $current[$order];
                $current[$order] = $value;
                
                $dbObject->execute('UPDATE `' . $table . '` SET `' . $order . '` = '. $next[$order] .' where `' . $id . '` = ' . $next[$id] . ';');
                $dbObject->execute('UPDATE `' . $table . '` SET `' . $order . '` = '. $current[$order] .' where `' . $id . '` = ' . $current[$id] . ';');
                return true;
            }
        }

        return false;
    }

}

?>
