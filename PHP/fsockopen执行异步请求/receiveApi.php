<?php
$start = time();

sleep(5);

$end = time();

$f = fopen('test.txt', 'w+');

fwrite($f, 'test request , this script spend ' . ($end - $start) . ' seconds, params: ' . ($_GET ? json_encode($_GET) : json_encode($_POST)));

fclose($f);
