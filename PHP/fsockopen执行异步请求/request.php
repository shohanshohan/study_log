<?php
date_default_timezone_set('PRC');

$time = time();
echo 'start <br />';

function doRequest($path, $host, $param=array()){
  $query = isset($param)? http_build_query($param) : ''; 

  $port = 80; 
  $errno = 0; 
  $errstr = ''; 
  $timeout = 10; 

  $fp = fsockopen($host, $port, $errno, $errstr, $timeout); 

  $out = "POST ".$path." HTTP/1.1\r\n"; 
  $out .= "host:".$host."\r\n"; 
  $out .= "content-length:".strlen($query)."\r\n"; 
  $out .= "content-type:application/x-www-form-urlencoded\r\n"; 
  $out .= "connection:close\r\n\r\n"; 
  $out .= $query; 

  fputs($fp, $out);
  fclose($fp); 
}


$path = '/test01.php';
$param = ['name'=>'test-name', 'age'=>18];
sleep(1);

ignore_user_abort(true); // 忽略客户端断开 
set_time_limit(0);    // 设置执行不超时

doRequest($path, 'localhost', $param);

echo 'spend ' . (time() - $time) . ' seconds <br />';



