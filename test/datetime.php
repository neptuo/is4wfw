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
echo 'now: ' . $date2->format('d.m.Y H:i:s');
echo PHP_EOL;
$date2->setTimestamp($timestamp);
echo $date2->format('d.m.Y H:i:s');
echo PHP_EOL;

$date->modify("-2 day");
echo $date->format('d.m.Y H:i:s');
echo PHP_EOL;
echo $date->format("Y");
echo PHP_EOL;

$date = DateTime::createFromFormat('Y-m-d', '2021-12-16');
$date->modify("monday this week");
echo $date->format('d.m.Y H:i:s');
echo PHP_EOL;
$date->modify("sunday this week");
echo $date->format('d.m.Y H:i:s');
echo PHP_EOL;

?></pre>