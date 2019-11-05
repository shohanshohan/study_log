<?php
namespace app\wx\service;
use think\Config;

/**
* 订单分账服务
*/
class Ordersep
{
  //支付成功后，微信多次分账请求地址（POST）
  private static $sepUrl = 'https://api.mch.weixin.qq.com/secapi/pay/multiprofitsharing';

  // private $appid; //应用appid

  // private $mch_id; //商户号

  // private $nonce_str; //随机字符串

  // private $sign; //签名，签名算法见：https://pay.weixin.qq.com/wiki/doc/api/jsapi_sl.php?chapter=4_3

  // private $transaction_id; //微信支付订单号

  // private $out_order_no; //商户订单号，即我们自己生成的唯一订单号

  /*
  分账接收方列表,多维数组，要转换成json数组
  有四个参数：type, account, amount, description
  [
    {
         "type": "MERCHANT_ID", //MERCHANT_ID：商户IDPERSONAL_WECHATID：个人微信号PERSONAL_OPENID：个人openid
         "account":"190001001", //对应type
         "amount":100,
         "description": "分到商户" //注意,描述信息必须是utf8格式，最好转换一下 utf8_encode()
    },
    ......
  ]
  */
  // private $receivers = []; 


  public static function setexcute($data)
  {
    if(empty($data['appid']) || empty($data['mch_id']) || empty($data['transaction_id']) || empty($data['out_order_no']) || empty($data['receivers'])) {
      throw new \Exception("缺少必要参数!");
    }
    if(empty($data['nonce_str'])) {
      $data['nonce_str'] = self::getNonceStr();
    }
    if(is_array($data['receivers'])) {
      $data['receivers'] = json_encode($data['receivers']);
    }
    
    $sign = self::getSign($data);
    $data['sign'] = $sign;
    $xml = self::arrayToXml($data);
    $result = self::postXmlCurl($xml, self::$sepUrl);
    if($result) {
      if(isset($result['return_code']) && $result['return_code']=='SUCCESS' && isset($result['result_code']) && $result['result_code']=='SUCCESS') {
        $logData = [
          'transaction_id' => $result['transaction_id'], //微信支付平台订单号
          'out_order_no' => $result['out_order_no'], //商户单号，即我们自己生成的唯一订单号
          'wx_order_id' => $result['order_id'], //微信分账单号
        ];
        return $returnData;
      }
      return '订单分账请求出错！resutl：' . serialize($result);
    }
  }


  /**
   * 签名算法
   * @param array $data
   * @return string
   */
  protected static function getSign($data=[])
  {
      $mch_key = Config::get('mch_key'); //注意，这里是你配置的商户密钥
      //签名步骤一：按字典序排序参数
      $String = self::formatParaMap($data);
      //签名步骤二：在string后加入KEY
      $String .= "&key=". $mch_key;
      //签名步骤三：MD5加密
      $String = md5($String);
      //签名步骤四：所有字符转为大写
      $signStr = strtoupper($String);
      return $signStr;
  }

  /**
   * 格式化参数，签名过程需要使用
   * @param array $paraMap
   * @return string
   */
  protected static function formatParaMap($paraMap=[])
  {
    if(!empty($paraMap)){
      ksort($paraMap);
      foreach ($paraMap as $key=>$val){
          if(!empty($val)){
              $data[] = "$key=$val";
          }
      }
      $str = implode('&',$data);
      return $str;
    }
    return '';
  }


  /**
   * array转xml
   * 拼装请求的数据
   * @return  String 拼装完成的数据
   */
  protected static function arrayToXml($arr)
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
  protected static function xmlToArray($xml)
  {
    //将XML转为array
    $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    return $array_data;
  }


  /**
   * 随机字符串生成，32位内
   * @return string max 32
  */
  protected static function getNonceStr()
  {
    $str = time().'mySepStr'.mt_rand(1,1000000);
    return substr(md5($str),2);
  }


  /**
     *  作用：以post方式提交xml到对应的接口url
     */
    protected static function postXmlCurl($xml,$url,$second=30,$useCert=true)
    {
        //初始化curl
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt( $ch, CURLOPT_AUTOREFERER, TRUE );

        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        $certPath = '/usr/local/etc/openssl/'; //注意，这里要使用绝对路径
        //使用证书，证书申请下载见 https://pay.weixin.qq.com/wiki/doc/api/allocation.php?chapter=4_3
        if($useCert == true){ 
          curl_setopt($ch, CURLOPT_VERBOSE, true);
          //使用证书：cert 与 key 分别属于两个.pem文件
          curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
          curl_setopt($ch,CURLOPT_SSLCERT, $certPath . 'apiclient_cert.pem');
          curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
          curl_setopt($ch,CURLOPT_SSLKEY, $certPath . 'apiclient_key.pem');
        }
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        //运行curl
        $data = curl_exec($ch);
        //返回结果
        if($data)
        {
          curl_close($ch);
          return self::xmlToArray($data);
        }
        else
        {
          $curlInfo = curl_getinfo($ch);
          $curlInfo = json_encode($curlInfo);
          $error = curl_error($ch);
          $curl_errno = curl_errno($ch);
          curl_close($ch);
          return "curl出错，info: $curlInfo ; 错误码:$curl_errno; error: $error ; data: " . $data . '; certPath: ' . $certPath;
        }
    }
}
