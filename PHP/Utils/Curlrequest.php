<?php
/**
* curl 远程请求类
* http 和 https 
*/
class Curlrequest
{
  /**
   * [curlGet GET请求]
   * @param  [type]  $url     [请求地址]
   * @param  integer $timeout [请求最大时长/秒]
   * @return [type]           [mixed]
   */
  public static function curlGet($url, $timeout=10)
  {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $result = curl_exec($ch);
    curl_close($ch);
    return json_decode($result, true);
  }
  

  /**
   * [curlPost POST请求]
   * @param  [type]  $url      [请求地址]
   * @param  array   $postData [要传递的参数数组]
   * @param  integer $timeout  [请求最大时长/秒]
   * @return [type]            [description]
   */
  public static function curlPost($url, $postData=[], $timeout=10)
  {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_POST, true);
    if(!empty($postData) && is_array($postData)) {
      curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    $result = curl_exec($ch);
    return json_decode($result, true);
  }
  
}
