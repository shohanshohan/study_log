<?php
namespace app\common\util;

/**
* 微信小程序支付统一下单
*/
class Wxpay
{
  private $trade_url = "https://api.mch.weixin.qq.com/pay/unifiedorder"; //微信统一下单请求地址

  private $key = ''; //商户生成的密钥，签名用到

  private $data = []; //统一下单请求参数数组, 可调用方法 data($data) 一次性传入参数

  private $trade_type = 'JSAPI'; //交易类型，默认小程序, 还有 APP

  private $appid = ''; //应用appid

  private $mch_id = ''; //商户号

  private $openid = ''; //用户openid

  private $device_info = ''; //终端设备号(门店号或收银设备ID), 选填

  private $nonce_str = ''; //随机字符串，不长于32位

  private $sign = ''; //签名

  private $sign_type = ''; //签名类型，目前支持HMAC-SHA256和MD5，默认为MD5, 选填

  private $body = '平台充值'; //商品描述

  private $detail = ''; //商品详情，选填

  //附加数据，在查询API和支付通知回调中原样返回，该字段主要用于商户携带订单的自定义数据，选填
  private $attach = ''; 

  private $out_trade_no = ''; //商户订单号

  private $fee_type = ''; //货币类型，选填，默认人民币

  private $total_fee = 0; //订单总金额，单位为分

  private $spbill_create_ip = ''; //终端IP

  private $time_start = ''; //订单生成时间，格式为yyyyMMddHHmmss，选填

  private $time_expire = ''; //交易结束时间，格式为yyyyMMddHHmmss，选填

  private $goods_tag = ''; //订单优惠标记，选填

  //通知服务器的回调地址，接收微信支付异步通知回调地址，通知url必须为直接可访问的url，不能携带参数。
  private $notify_url = ''; 

  private $limit_pay = ''; //指定支付方式 ，选填

  private $receipt = ''; //开发票入口开放标识，选填
  
  public function tradeUrl($url)
  {
    $this->trade_url = (string) $url;
    return $this;
  }

  public function setkey($key)
  {
    $this->key = (string) $key;
    return $this;
  }

  public function tradeType($type)
  {
    $this->trade_type = (string) $type;
    return $this;
  }

  public function appid($appid)
  {
    $this->appid = (string) $appid;
    return $this;
  }

  public function openid($openid)
  {
    $this->openid = (string) $openid;
    return $this;
  }

  public function mchId($mch_id)
  {
    $this->mch_id = (string) $mch_id;
    return $this;
  }

  public function deviceInfo($device)
  {
    $this->device_info = (string) $device;
    return $this;
  }

  public function nonceStr($str='')
  {
    if(empty($str)){
      $str = $this->setNonceStr();
    }
    $this->nonce_str = (string) $str;
    return $this;
  }

  public function signType($type)
  {
    $this->sign_type = (string) $type;
    return $this;
  }

  public function body($body)
  {
    $this->body = (string) $body;
    return $this;
  }

  public function detail($detail)
  {
    $this->detail = (string) $detail;
    return $this;
  }

  public function attach($attach)
  {
    $this->attach = (string) $attach;
    return $this;
  }

  public function outTradeNo($orderid)
  {
    $this->out_trade_no = (string) $orderid;
    return $this;
  }

  public function feeType($type)
  {
    $this->fee_type = (string) $type;
    return $this;
  }

  public function totalFee($amount)
  {
    $this->total_fee = (int) $amount;
    return $this;
  }

  public function spbillCreateIp($ip='')
  {
    if(empty($ip)){
      $ip = $this->getIp();
    }
    $this->spbill_create_ip = (string) $ip;
    return $this;
  }

  public function timeStart($time)
  {
    $this->time_start = (string) $time;
    return $this;
  }

  public function timeExpire($time)
  {
    $this->time_expire = (string) $time;
    return $this;
  }

  public function goodsTag($tag)
  {
    $this->goods_tag = (string) $tag;
    return $this;
  }

  public function notifyUrl($url)
  {
    $this->notify_url = (string) $url;
    return $this;
  }

  public function limitPay($type)
  {
    $this->limit_pay = (string) $type;
    return $this;
  }

  public function receipt($receipt)
  {
    $this->receipt = (string) $receipt;
    return $this;
  }

  //请求参数封装成数组
  public function data(array $data=[]) 
  {
    if(empty($data)){
      $data = [
        'trade_type' => $this->trade_type, //交易类型，默认
        'appid' => $this->appid, //应用appid
        'mch_id' => $this->mch_id, //商户号
        'device_info' => $this->device_info, //终端设备号(门店号或收银设备ID), 选填
        'nonce_str' => $this->nonce_str ? $this->nonce_str : $this->setNonceStr(), //随机字符串，不长于32位
        'sign_type' => $this->sign_type, //签名类型，目前支持HMAC-SHA256和MD5，默认为MD5, 选填
        'body' => $this->body, //商品描述
        'detail' => $this->detail, //商品详情，选填
        //附加数据，在查询API和支付通知回调中原样返回，该字段主要用于商户携带订单的自定义数据，选填
        'attach' => $this->attach,
        'openid' => $this->openid, //小程序支付必填
        'out_trade_no' => $this->out_trade_no, //商户订单号
        'fee_type' => $this->fee_type, //货币类型，选填，默认人民币
        'total_fee' => $this->total_fee, //订单总金额，单位为分
        'spbill_create_ip' => $this->spbill_create_ip ? $this->spbill_create_ip : $this->getIp(), //终端IP
        'time_start' => $this->time_start, //订单生成时间，格式为yyyyMMddHHmmss，选填
        'time_expire' => $this->time_expire, //交易结束时间，格式为yyyyMMddHHmmss，选填
        'goods_tag' => $this->goods_tag, //订单优惠标记，选填
        //通知服务器的回调地址，接收微信支付异步通知回调地址，通知url必须为直接可访问的url，不能携带参数。
        'notify_url' => $this->notify_url, 
        'limit_pay' => $this->limit_pay, //指定支付方式 ，选填
        'receipt' => $this->receipt //开发票入口开放标识，选填
      ];
    } else {
      if(!empty($data['key'])){
        $this->key = $data['key'];
        unset($data['key']);
      }
      if(!empty($data['trade_url'])){
        $this->trade_url = $data['trade_url'];
      }
      unset($data['trade_url']);
      if(empty($data['nonce_str'])){
        $data['nonce_str'] = $this->setNonceStr();
      }
      if(empty($data['body'])){
        $data['body'] = $this->body;
      }
      if(empty($data['trade_type'])){
        $data['trade_type'] = $this->trade_type;
      }
      if(empty($data['spbill_create_ip'])){
        $data['spbill_create_ip'] = $this->getIp();
      }
    }
    $this->data = $data;
    return $this;
  }

  /**
   * 发起统一下单
   * @return array 返回数组信息
   */
  public function orderRequest()
  {
    if(!$this->data) {
      $this->data();
    }
    $data = $this->data;
    
    if(empty($data['appid']) || empty($data['mch_id']) || empty($data['out_trade_no']) || empty($data['total_fee']) || empty($data['spbill_create_ip']) || empty($data['notify_url'])) {
      return false;
    }
    $data['sign'] = $this->setSign();
    $xml = $this->arrayToXml($data);
    return $this->postXmlCurl($xml, $this->trade_url);
  }

  /**
   * 接收微信支付回调信息
   * @return  array [返回数组]
   */
  public function orderResponse()
  {
    //接收传送的数据
    $fileContent = file_get_contents("php://input");
    return $this->xmlToArray($fileContent);
  }


 

  /**
   * 随机字符串，不长于32位
   * @return string max 32
   */
  public function setNonceStr()
  {
    $time = microtime(true) * 10000;
    return substr(md5($this->body . $time), 2);
  }

  /**
   * 签名算法
   * @param array $data
   * @return string
   */
  private function setSign()
  {
    //签名步骤一：按字典序排序参数
    $String = $this->formatparamData($this->data);
    //签名步骤二：在string后加入KEY
    $String .= "&key=".$this->key;
    //签名步骤三：MD5加密
    $String = md5($String);
    //签名步骤四：所有字符转为大写
    $signStr = strtoupper($String);
    return $signStr;
  }

  /**
   * 格式化参数，签名过程需要使用
   * @param array $paramData
   * @return string
   */
  private function formatparamData($paramData=[])
  {
    $string = '';
    if(!empty($paramData)){
      ksort($paramData);
      foreach ($paramData as $key=>$val){
        if(!empty($val)){
            $string .= $key . "=" .$val. "&";
        }
      }
      $string = rtrim($string, '&');
    }
    return $string;
  }

  /**获取用户真实的IP
   * @return array|false|string
   */
  protected function getIp(){
    //判断服务器是否允许$_SERVER
    if(isset($_SERVER)){
      if(isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
          $realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
      }elseif(isset($_SERVER["HTTP_CLIENT_IP"])) {
          $realip = $_SERVER["HTTP_CLIENT_IP"];
      }else{
          $realip = $_SERVER["REMOTE_ADDR"];
      }
    }else{
      //不允许就使用getenv获取
      if(getenv("HTTP_X_FORWARDED_FOR")){
          $realip = getenv( "HTTP_X_FORWARDED_FOR");
      }elseif(getenv("HTTP_CLIENT_IP")) {
          $realip = getenv("HTTP_CLIENT_IP");
      }else{
          $realip = getenv("REMOTE_ADDR");
      }
    }
    return $realip;
  }

  /**
   * array转xml
   * 拼装请求的数据
   * @return  String 拼装完成的数据
   */
  protected function arrayToXml($arr)
  {
      $xml = "<xml>";
      foreach ($arr as $key=>$val)
      {
          $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
      }
      $xml.="</xml>";
      return $xml;
  }

  /**
   *  将xml转为array
   * @return array
   */
  protected function xmlToArray($xml)
  {
      //将XML转为array
      $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
      return $array_data;
  }

  /**
   *  作用：以post方式提交xml到对应的接口url
   */
  protected function postXmlCurl($xml,$url,$second=30,$useCert=false)
  {
      //初始化curl
      $ch = curl_init();
      //设置超时
      // curl_setopt($ch, CURLOP_TIMEOUT, $second);
      //这里设置代理，如果有的话
      //curl_setopt($ch,CURLOPT_PROXY, '8.8.8.8');
      //curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
      curl_setopt($ch,CURLOPT_URL, $url);
      curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
      curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
      //设置header
      curl_setopt($ch, CURLOPT_HEADER, FALSE);
      //要求结果为字符串且输出到屏幕上
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      if($useCert == true){
          //设置证书
          //使用证书：cert 与 key 分别属于两个.pem文件
          curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
          curl_setopt($ch,CURLOPT_SSLCERT, 'path文件');
          curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
          curl_setopt($ch,CURLOPT_SSLKEY, 'path文件');
      }
      //post提交方式
      curl_setopt($ch, CURLOPT_POST, TRUE);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
      //运行curl
      $data = curl_exec($ch);
      //返回结果
      if($data)
      {
          curl_close($ch);
          return $this->xmlToArray($data);
      }
      else
      {
          $error = curl_errno($ch);
          curl_close($ch);
          return $this->apiInfo(400,"curl出错，错误码:$error");
      }
  }

}
