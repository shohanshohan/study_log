<?php
/**
* 远程访问接口
* 注意！不适用于 https 请求 
*/
class Remotehttp
{
  /**
   * [http 模拟表单提交远程请求]
   * 对于用 curl 方式来请求，这个方法更简化
   * @param  [type] $url    [请求链接地址]
   * @param  string $method [请求方法，GET或POST]
   * @param  array  $data   [要传递的参数数组]
   * @return [type]         [mixed]
   */
  public static function http($url, $method="GET", $data=[])
  {
    $opts = [
      'http' => [
        'method' => strtoupper($method),
        'header' => 'Content-type: application/x-www-form-urlencoded',
        'content' => http_build_query($data)
      ]
    ];
    //这里用到套接字 stream_context_create()函数，用来创建http访问流
    $context = stream_context_create($opts); 
    return file_get_contents($url, false, $context);
  }

  /**
   * [headerHttp 带Http头信息返回]
   * @param  [type] $url    [请求链接地址]
   * @param  string $method [请求方法，GET或POST]
   * @param  array  $data   [要传递的参数数组]
   * @return [type]         [mixed]
   */
  public static function headerHttp($url, $method="GET", $data=[])
  {
    $opts = [
      'http' => [
        'method' => strtoupper($method),
        'header' => 'Content-type: application/x-www-form-urlencoded',
        'content' => http_build_query($data)
      ]
    ];

    $context = stream_context_create($opts); 
    $fp = @fopen($url, 'r');
    $httpData = stream_get_meta_data($fp);
    return [
      'http_info' => $httpData['wrapper_data'],
      'data' => file_get_contents($url, false, $context)
    ];
  }

}
