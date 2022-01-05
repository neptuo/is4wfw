<?php

$a = ["a" => 1, "b" => 2];
$b = ["c" => 3, "b" => 4];
$c = array_merge($a, $b);

var_dump($c);

?>