<?php

class Order {

    private static function serializeAdditional($additionalValues) {
        $additional = '';
        if (is_array($additionalValues)) {
            foreach ($additionalValues as $key => $value) {
                $additional .= '<input type="hidden" name="' . $key . '" value="' . $value . '" />';
            }
        }

        return $additional;
    }

    public static function upForm($actionUrl, $prefix, $id, $title, $additionalValues = null) {
        return ''
            . '<form name="' . $prefix . '-move-up" method="post" action="' . $actionUrl . '">'
                . '<input type="hidden" name="' . $prefix . '-id" value="' . $id . '" />'
                . '<input type="hidden" name="' . $prefix . '-move-up" value="move-up" />'
                . Order::serializeAdditional($additionalValues)
                . '<input type="image" src="~/images/arro_up.png" name="' . $prefix . '-move-up" value="move-up" title="' . $title . '" /> '
            . '</form> ';
    }
    
    public static function downForm($actionUrl, $prefix, $id, $title, $additionalValues = null) {
        return ''
            . '<form name="' . $prefix . '-move-down" method="post" action="' . $actionUrl . '">'
                . '<input type="hidden" name="' . $prefix . '-id" value="' . $id . '" />'
                . '<input type="hidden" name="' . $prefix . '-move-down" value="move-down" />'
                . Order::serializeAdditional($additionalValues)
                . '<input type="image" src="~/images/arro_do.png" name="' . $prefix . '-move-down" value="move-down" title="' . $title . '" /> '
            . '</form> ';
    }

    public static function isPost($prefix) {
        return $_POST[$prefix . '-move-up'] == 'move-up' || $_POST[$prefix . '-move-down'] == 'move-down';
    }

    private static function getWhereCondition($row, $columns) {
        if (is_array($columns)) {
            $result = '';
            foreach ($columns as $column) {
                if (strlen($result) == 0) {
                    $result .= ' where';
                } else {
                    $result .= ' and';
                }
                
                $result .= ' `' . $column . '` = ' . $row[$column];
            }

            return $result;
        } else {
            return ' where `' . $columns . '` = ' . $row[$columns];
        }
    }

    public static function tryUpdate($rows, $prefix, $table, $id, $order) {
        global $dbObject;

        if (is_array($id)) {
            $dataId = $id[0];
        } else {
            $dataId = $id;
        }

        if ($_POST[$prefix . '-move-up'] == 'move-up') {
            $rowId = $_POST[$prefix . '-id'];
            
            $current = null;
            $prev = null;
            foreach ($rows as $row) {
                if ($row[$dataId] == $rowId) {
                    $current = $row;
                    break;
                }
                
                $prev = $row;
            }
            
            if ($current != null && $prev != null) {
                $value = $prev[$order];
                $prev[$order] = $current[$order];
                $current[$order] = $value;
                
                $dbObject->execute('UPDATE `' . $table . '` SET `' . $order . '` = '. $prev[$order] . Order::getWhereCondition($prev, $id) . ';');
                $dbObject->execute('UPDATE `' . $table . '` SET `' . $order . '` = '. $current[$order] . Order::getWhereCondition($current, $id) . ';');
                return true;
            }
        } else if ($_POST[$prefix . '-move-down'] == 'move-down') {
            $rowId = $_POST[$prefix . '-id'];

            $current = null;
            $next = null;
            $isMatched = false;
            foreach ($rows as $row) {
                if($isMatched) {
                    $next = $row;
                    break;
                }

                if($row[$dataId] == $rowId) {
                    $current = $row;
                    $isMatched = true;
                }
            }

            if ($current != null && $next != null) {
                $value = $next[$order];
                $next[$order] = $current[$order];
                $current[$order] = $value;
                
                $dbObject->execute('UPDATE `' . $table . '` SET `' . $order . '` = '. $next[$order] . Order::getWhereCondition($next, $id) . ';');
                $dbObject->execute('UPDATE `' . $table . '` SET `' . $order . '` = '. $current[$order] . Order::getWhereCondition($current, $id) . ';');
                return true;
            }
        }

        return false;
    }

}

?>
