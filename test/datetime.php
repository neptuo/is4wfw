<pre><?php

$date = DateTime::createFromFormat('Y-m-d', '2011-01-22');
echo $date->format('d.m.Y H:i:s');
echo PHP_EOL;

$timestamp = $date->getTimestamp();
echo $timestamp;
echo PHP_EOL;

$timestamp = $date->setTime(0, 0)->getTimestamp();
echo $timestamp;
echo PHP_EOL;

$date2 = new DateTime();
$date2->setTimestamp($timestamp);
echo $date->format('d.m.Y H:i:s');
echo PHP_EOL;

?></pre>