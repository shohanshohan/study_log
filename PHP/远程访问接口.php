<?php
//这里用到套接字 stream_context_create()函数，用来创建http访问流
function remoteHttp($url,$method='GET',$data=[])
{
  $opts = [
    'http' => [
      'method' => $method,
      'header' => 'Content-type: application/x-www-form-urlencoded',
      'content' => http_build_query($data)
    ]
  ];
  $context = stream_context_create($opts);
  return file_get_contents($url,false,$context);
}
//比curl方法简便多了

//如果要获取请求http头信息,可以如下：
function remoteHttp($url,$method='GET',$data=[])
{
  $opts = [
    'http' => [
      'method' => $method,
      'header' => 'Content-type: application/x-www-form-urlencoded',
      'content' => http_build_query($data)
    ]
  ];
  $context = stream_context_create($opts);
  $fp = @fopen($url,'r');
  $httpData = stream_get_meta_data($fp);
  return [
    'apiContent'  => file_get_contents($url,false,$context),
    'httpInfo'    => $httpData['wrapper_data']
  ]
}
