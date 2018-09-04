<?php
//这里用到套接字 stream_context_create()函数，用来创建http访问流
function remoteHttp($url,$method='GET',$data=[])
{
  $opts = [
    'http' => [
      'method' => $method,
      'header' => 'Content-type: application/x-www-form-urlencoded',
      'content' => $data
    ]
  ];
  $context = steam_context_create($opts);
  return file_get_contents($url,false,$context);
}
比curl方法简便多了
