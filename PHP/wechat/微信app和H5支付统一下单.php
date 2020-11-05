<?php
namespace app\fish\controller;
use think\Controller;
use think\Db;
use think\Log;
use think\Config;
/**
 * Class Wepay 微信支付类
 * 对于appid和商户号、密钥等通用信息统一配置在 config.php 文件中
 * 'wxwebPay' => [
    'appId' => 'wx4d99749993e45620',
    'mchId' => '1499446732', //商户号
    'key' => "99c8922d27552af4644b3a6cda15088d", //商户生成的密钥
    'notifyUrl' => 'http://111.230.55.74:9999/fishwxwebnotify', //异步接收微信支付结果通知的回调地址
    'tradeType' => 'APP', //交易类型
    'tradeUrl' => "https://api.mch.weixin.qq.com/pay/unifiedorder", //微信统一下单请求地址
  ]
  * 至于APP支付与H5不同，就在于 交易类型 'tradeType' => 'APP', //交易类型'MWEB'为H5
  * 还有就是一个场景参数：'scene_info' 对于H5支付这个参数是一定要的，而APP支付没有这个参数
  * 请求之后接收的信息中，APP支付是获取 prepay_id 前端支付调用，H5是获取 'mweb' 参数取得页面的一个连接，然后直接请求这个连接
 */
class Wxwebpay extends Controller
{
  /**
  * 统一下单
  * @param WxPayUnifiedOrder $inputObj
  * @param int $timeOut
  * @throws WxPayException
  * @return 成功时返回，其他抛异常
  */
  public function payrequest()
  {
    $postData = $this->request->post();
    if($postData) {
      if(empty($postData['order_sn']) || empty($postData['amount']) || empty($postData['ext'])) {
        return $this->apiInfo(400,'缺少参数！');
      }

      $orderId = $postData['order_sn']; //订单号
      $account = substr($orderId, 0, 11);
      $user_id = substr($account, 3);
      // $user = Db::connect(Config::get('fish7_db'))->table('accounts_info')->where('user_id', $user_id)->find();
      // if(!$user) {
      //   return $this->apiInfo(400,'用户不存在！');
      // }

      $amount = round($postData['amount']); //金额，单位（分）
      $body = isset($postData['body']) ? $postData['body'] : ''; //商品描述
      //$trade_type = 'MWEB'; //交易类型
      $trade_type = 'APP'; //交易类型
      $ext = (int)$postData['ext']; //消费点
      //$scene_info = $postData['extend_param']; //场景信息
      $ext_name = Config::get('ext_name');
      $subject = isset($ext_name[$ext]) ? $ext_name[$ext] : '捕鱼小达人2充值';
      $body = $body ?: $subject;
      $config = Config::get('wxwebPay');

      $attach = '{"orderId":"' .$orderId. '"'. ',"amount":' . $amount . ',"ext":' . $ext .'}';
      $data = [
          'appid' => $config['appId'],
          'mch_id' => $config['mchId'],//微信支付分配的商户号
          'body' => $body,
          'nonce_str' => $this->getNonceStr(), //随机字符串，长度要求在32位以内
          'out_trade_no' => $orderId,
          'total_fee' => $amount,
          'spbill_create_ip' => $this->get_ip(), //终端IP
          'notify_url' => $config['notifyUrl'], //异步接收微信支付结果通知的回调地址
          'trade_type' => $trade_type,
          'attach' => $attach,
          //'scene_info' => $scene_info
      ];

      $data['sign'] = $this->getSign($data);
      $xml = $this->arrayToXml($data);//return $xml;
      $data2 = $this->postXmlCurl($xml, $config['tradeUrl']);
      Log::write(date('Y-m-d H:i:s') . '--微信支付params:' . serialize($data), 'fish7wxwebpayRequest-params-info');
      if (isset($data2['return_code']) && $data2['return_code'] == 'SUCCESS') {
        if (isset($data2['result_code']) && $data2['result_code']=='SUCCESS') {
            Log::write(date('Y-m-d H:i:s') . '--微信支付Data:' . serialize($data), 'fish7wxwebpayRequest-return-info');
            $signData = [
            'appid' => $config['appId'],
            'partnerid' => $config['mchId'],
            'prepayid' => $data2['prepay_id'],
            'package' => 'Sign=WXPay',
            'noncestr' => $this->getNonceStr(),
            'timestamp' => (string)time()
          ];
          $signData['sign'] = $this->getSign($signData);
          return $this->apiInfo(0, '请求成功', $signData);
        } else {
            Log::write(date('Y-m-d H:i:s') . '--微信支付error:' . serialize($data2['err_code_des']), 'fish7wxwebpayRequest-error');
            return $this->apiInfo(400, $data2['err_code_des']);
        }
      }
      Log::write(date('Y-m-d H:i:s') . '--微信支付请求出错！:' . serialize($data2), 'fish7wxwebpayRequest-error');
      return $this->apiInfo(400, '微信支付请求出错！');
    }else{
      return $this->apiInfo(400, '非法请求！');
    }
  }


  /**
   * 支付结果通用通知
   * @param function $callback
   */
  public function wappaynotify()
  {
    libxml_disable_entity_loader(true);
    //获取通知的数据
    $fileContent = file_get_contents("php://input");
    //转换为simplexml对象
    $xmlResult = simplexml_load_string($fileContent, 'SimpleXMLElement', LIBXML_NOCDATA);

    $result = $this->xmlToArray($fileContent);
    Log::write(date('Y-m-d H:i:s') . '--微信支付回调信息:' . json_encode($result), 'fish7wxwebpay-notify-info');
    if(isset($result['return_code']) && $result['return_code']=='SUCCESS'){
      $appid = $result['appid'];
      $mch_id = $result['mch_id'];
      $total_fee = $result['total_fee'];
      $out_trade_no = $result['out_trade_no'];
      $transaction_id = $result['transaction_id']; //微信第三方订单号
      $attach = json_decode($result['attach'], true);

      if(empty($data['attach'])) {
        Log::write('支付验证不通过！attach: empty', 'fish7wxwebpay-notify-error');
        echo "FAIL!"; exit();
      };
      $config = Config::get('wxwebPay');
      if($appid !== $config['appId'] || $mch_id !== $config['mchId']) {
        Log::write('支付验证不通过！appid: ' . $config['appId'] . ';mch_id:' . $config['mchId'], 'fish7wxwebpay-notify-error');
        echo "FAIL!"; exit();
      }
      if($attach['amount'] != $total_fee) {
        Log::write('支付验证不通过！amount: ' . $attach['amount'], 'fish7wxwebpay-notify-error');
        echo "FAIL!"; exit();
      }
      if($attach['orderId'] != $out_trade_no) {
        Log::write('支付验证不通过！account: ' . $attach['account'], 'fish7wxwebpay-notify-error');
        echo "FAIL!"; exit();
      }

      $account = substr($out_trade_no, 0, 11);
      $serverData = [
        'order_id' => $transaction_id,
        'cp_order_id' => $out_trade_no,
        'account' => $account,
        'ext'         => $attach['ext'],
        'sign'        => $data['sign'],
        'amount'   => round($total_fee / 100, 2)
      ];
      //通知游戏服务器，并保存充值数据到数据库
      /*******你的业务逻辑********$notifyServer = $this->notifyServer($serverData);*************/
      $returnData = [
          'return_code' => 'SUCCESS',
          'return_msg' => 'OK'
      ];
      Log::write(date('Y-m-d H:i:s') . '--通知游戏服务器结果:' . serialize($notifyServer), 'fish7wxwebpay-notifyRequest-info');
    }else{
      $returnData = [
        'return_code' => 'FAIL',
        'return_msg' => '数据解析失败'
      ];
    }
    Log::write(date('Y-m-d H:i:s') . '--微信支付回调请求结果:' . serialize($returnData), 'wxwebpay-notifyResult-info');
    return $this->arrayToXml($returnData);
  }

    /**
     * 随机字符串生成，32位内
     * @return string max 32
     */
    protected function getNonceStr()
    {
        $str = time().'myWechatStr'.mt_rand(1,1000000);
        return substr(md5($str),2);
    }

    /**
     * 签名算法
     * @param array $data
     * @return string
     */
    protected function getSign($data=[])
    {
        //签名步骤一：按字典序排序参数
        $String = $this->formatParaMap($data);
        //签名步骤二：在string后加入KEY
        $String .= "&key=". Config::get('wxwebPay')['key'];
        //签名步骤三：MD5加密
        $String = md5($String);
        //签名步骤四：所有字符转为大写
        $signStr = strtoupper($String);
        return $signStr;
    }

    /**
     * 验证签名
     */
    protected function verifySign($data=[])
    {
        $result = false;
        if(!empty($data) && isset($data['sign'])){
            $sign = $data['sign'];
            unset($data['sign']);
            $verifySign = $this->getSign($data);
            if($verifySign==$sign){
                $result = true;
            }
        }
        return $result;
    }

    /**
     * 格式化参数，签名过程需要使用
     * @param array $paraMap
     * @return string
     */
    protected function formatParaMap($paraMap=[])
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
    protected function arrayToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key=>$val)
        {
            $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            //$xml.="<".$key.">".$val."</".$key.">";
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

    /**
     * 接收回调
     */
    protected function getMsg()
    {
    libxml_disable_entity_loader(true);
        //接收传送的数据
        $fileContent = file_get_contents("php://input");
        //转换为simplexml对象
        $xmlResult = simplexml_load_string($fileContent);
        return $this->xmlToArray($xmlResult);
    }

    /**获取用户真实的IP
     * @return array|false|string
     */
    protected function get_ip(){
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
     * 接口回调信息
     * @param int $code 状态码
     * @param string $msg  提示信息
     * @param array $info  返回数据信息
     */
    protected function apiInfo($code=200,$msg='',$info=array())
    {
      return json(['code' => $code, 'message' => $msg, 'info'  => $info]);
    }





}
