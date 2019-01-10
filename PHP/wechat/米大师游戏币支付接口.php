<?php
namespace app\game\controller;
use think\Log;
use think\Db;

/**
* 虚拟支付接口类
*/
class Vrpay extends Base
{
  protected $appid = '***************'; //小程序 appId
  protected $secret = '****************'; //小程序 appSecret
  protected $access_token = null;
  private $currentBurl = 'https://api.weixin.qq.com/cgi-bin/midas/getbalance?access_token='; //正式环境(post)，拼接 ACCESS_TOKEN
  private $currentBurl_sandbox = 'https://api.weixin.qq.com/cgi-bin/midas/sandbox/getbalance?access_token='; //沙箱环境
  private $deductBurl = 'https://api.weixin.qq.com/cgi-bin/midas/pay?access_token='; //正式环境，拼接 ACCESS_TOKEN
  private $deductBurl_sandbox = 'https://api.weixin.qq.com/cgi-bin/midas/sandbox/pay?access_token='; //沙箱环境
  private $presentBurl = 'https://api.weixin.qq.com/cgi-bin/midas/present?access_token='; //正式环境
  private $presentBurl_sandbox = 'https://api.weixin.qq.com/cgi-bin/midas/sandbox/present?access_token='; //沙箱环境
  private $cancelpayurl = 'https://api.weixin.qq.com/cgi-bin/midas/cancelpay?access_token='; //正式环境
  private $cancelpayurl_sandbox = 'https://api.weixin.qq.com/cgi-bin/midas/sandbox/cancelpay?access_token='; //沙箱环境
  private $Mkey = '****************'; //米大师密钥
  private $Mkey_sandbox = '*****************'; //米大师密钥,沙箱
  private $offer_id = '******************'; //米大师分配的offer_id
  private $zone_id = '1'; //游戏服务器大区id


  public function __construct()
  {
    parent::__construct();
    //设置请求头
    header("Access-Control-Allow-Origin:*");
    if( $this->access_token === null ){
      $redis = new \Redis();
      $redis->connect('127.0.0.1', 6379, 10) or die('redis连接失败！');
      $access_token = $redis->get('gameApi_access_token');
      if(empty($access_token)){
        $this->access_token = $this->getAccessToken();
        $redis->set('gameApi_access_token', $this->access_token); 
        $redis->expire('gameApi_access_token', 7199);//保存7199秒，因为小程序是默认7200秒过期
      }else{
        $this->access_token = $access_token;
      }
    }
    
  }
  
  /**
   * 接口回调信息
   * @param int $code 状态码
   * @param string $msg  提示信息
   * @param array $info  返回数据信息
   */
  protected function apiInfo($code=200,$msg='',$data=[])
  {
      return ['code' => $code, 'msg' => $msg, 'data'  => $data];
  }

  /**
   * 获取小程序全局唯一后台接口调用凭据（access_token）
   * @return [type] [description]
   */
  protected function getAccessToken()
  {
    $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appid&secret=$this->secret";
    $result = $this->curl_get_https($url);
    Log::record('获取小程序全局唯一后台接口调用凭据（access_token）：' . json_encode($result), 'getAccessToken-result');
    if($result && isset($result['access_token'])){
      return $result['access_token'];
    }
    return false;
  }


  /**
   * 获取游戏币余额
   * @return [type] [description]
   */
  public function currentB()
  {
    //if($this->request->isPost()){
      $time = time();
      $openid = $this->request->post('openid', 'oDwu15ANa0aKIlB4g4NBrdP_MOKQ');
      if(!$openid){
        Log::record('缺少参数'. json_encode($this->request->post()), 'currentB-error');
        return $this->apiInfo(400, '缺少参数');
      }
      $session_key = $this->session_key($openid); //会话密钥
      if($session_key === false){
        Log::record('找不到session_key'. json_encode($this->request->post()), 'currentB-error');
        return $this->apiInfo(405, '非法访问');
      }
      /*$data = [
        'openid' => $openid, //用户唯一标识符
        'appid'  => $this->appid, //小程序 appId
        'offer_id' => $this->offer_id, //米大师分配的offer_id
        'ts'  => $time, //UNIX 时间戳，单位是秒
        'zone_id' => $this->zone_id, //游戏服务器大区id,游戏不分大区则默认zoneId ="1",String类型
        'pf' => $this->request->post('pf', ''), //平台 如安卓：android
        //'user_ip' => '', //用户外网 IP, 可选
        //'sig' => '', //以上所有参数（含可选最多7个）+uri+米大师密钥，用 HMAC-SHA256签名
        'access_token' => $this->access_token, //接口调用凭证
        //'mp_sig' => '' //以上所有参数（含可选最多9个）+uri+session_key，用 HMAC-SHA256签名
      ];*/
      $data = [
        "openid" => $openid,
        "appid"  => $this->appid,
        "offer_id" => $this->offer_id,
        "ts"  => $time,
        "zone_id" => $this->zone_id,
        "pf" => $this->request->post("pf", "android")
      ];
      
      //生成签名
      $data = $this->signature($data, 'getbalance', $session_key, $env='sandbox');
      
      //请求结果
      $result = $this->curlPost($this->currentBurl_sandbox . $this->access_token, $data);

      Log::record('请求参数：'. json_encode($data). '; 请求结果：'. json_encode($result), 'currentB-info');
      if(isset($result['errcode']) && $result['errcode']===0){
        return $this->apiInfo(200, 'success', $result);
      }
      return $this->apiInfo(400, 'error', $result);
    //}
  }

  /**
   * 扣除游戏币
   * @return [type] [description]
   */
  public function deductB()
  {
    if($this->request->isPost()){
      $time = time();
      $openid = $this->request->post('openid', '');
      if(!$openid){
        Log::record('缺少参数'. json_encode($this->request->post()), 'deductB-error');
        return $this->apiInfo(400, '缺少参数');
      }
      $session_key = $this->session_key($openid); //会话密钥
      if($session_key === false){
        Log::record('找不到session_key'. json_encode($this->request->post()), 'deductB-error');
        //return $this->apiInfo(405, '非法访问');
      }
      $pf = $this->request->post('pf', 'android');


      $amt = (int)$this->request->post('amt', 0); //扣除游戏币数量，不能为 0
      //$amt = 1;
      //获取当前游戏币余额
      $currentB = $amt ? $amt : $this->getCurrrenB($openid, $session_key, $pf);
      
      if( $currentB === false ){
        Log::record('获取游戏币余额出错'. json_encode($currentB) . '--params:' . json_encode($this->request->post()), 'deductB-error');
        return $this->apiInfo(406, '获取游戏币余额出错');
      }
      if( is_array($currentB) ){
        Log::record('无法获取游戏币余额'. json_encode($currentB) . '--params:' . json_encode($this->request->post()), 'deductB-error');
        return $this->apiInfo(406, '无法获取游戏币余额');
      }
      $data = [
        "openid" => $openid, //用户唯一标识符
        "appid"  => $this->appid, //小程序 appId
        "offer_id" => $this->offer_id, //米大师分配的offer_id
        "ts"  => $time, //UNIX 时间戳，单位是秒
        "zone_id" => $this->zone_id, //游戏服务器大区id,游戏不分大区则默认zoneId ="1",String类型
        "pf" => $pf, //平台 如安卓：android
        "amt" => (int)$currentB, //扣除游戏币数量，不能为 0
        "bill_no" => $this->request->post("bill_no", ""), //订单号，业务需要保证全局唯一；相同的订单号不会重复扣款。长度不超过63，只能是数字、大小写字母_-
      ];
      
      //生成签名
      $data = $this->signature($data, 'pay', $session_key, $env='sandbox');

      //return $data;die;
      
      //请求结果
      $result = $this->curlPost($this->deductBurl_sandbox . $this->access_token, $data);

      Log::record('请求参数：'.json_encode($data). '; 请求结果：'. json_encode($result), 'deductB-info');
      
      if(isset($result['errcode'])){
        if($result['errcode'] === -1){
          $result = $this->curlPost($this->deductBurl_sandbox . $this->access_token, $data);//系统繁忙，此时再次请求
        }
        if($result['errcode'] === 0){
          $notifyData = [
            'order_id'  => $data['bill_no'],
            'cp_order_id' => $data['bill_no'],
            'sign'      => 'deduct-B',
            'amount'    => (int)$currentB,
            'ext'       => (int)$this->request->post('ext', '')
          ];
          //通知游戏服
          $notifyServer = $this->notifyServer($notifyData);
          Log::record('通知游戏服结果：'. $notifyServer, 'deductB-notifyServer');
          return $this->apiInfo(200, 'success', $result);
        }
      }
      return $this->apiInfo(400, 'error', $result);
    }
  }


  /**
   * 赠送游戏币
   * @return [type] [description]
   */
  public function presentB()
  {
    if($this->request->isPost()){
      $time = time();
      $openid = $this->request->post('openid', 'oDwu15ANa0aKIlB4g4NBrdP_MOKQ');
      if(!$openid){
        Log::record('缺少参数'. json_encode($this->request->post()), 'presentB-error');
        return $this->apiInfo(400, '缺少参数');
      }
      $session_key = $this->session_key($openid); //会话密钥
      if($session_key === false){
        Log::record('找不到session_key'. json_encode($this->request->post()), 'presentB-error');
        return $this->apiInfo(405, '非法访问');
      }
      
      $data = [
        'openid' => $openid, //用户唯一标识符
        'appid'  => $this->appid, //小程序 appId
        'offer_id' => $this->offer_id, //米大师分配的offer_id
        'ts'  => $time, //UNIX 时间戳，单位是秒
        'zone_id' => $this->zone_id, //游戏服务器大区id,游戏不分大区则默认zoneId ="1",String类型
        'pf' => $this->request->post('pf', ''), //平台 如安卓：android
        'present_counts' => $this->request->post('present_counts', 0), //赠送游戏币的个数，不能为0
        'bill_no' => $this->request->post('bill_no', ''), //订单号，业务需要保证全局唯一；相同的订单号不会重复扣款。长度不超过63，只能是数字、大小写字母_-
      ];

      //生成签名
      $data = $this->signature($data, 'present', $session_key, $env='sandbox');
      
      //请求结果
      $result = $this->curlPost($this->presentBurl_sandbox . $this->access_token, $data);

      Log::record('请求参数：'.json_encode($data). '; 请求结果：'. json_encode($result), 'presentB-info');
      if($result && isset($result['errcode']) && $result['errcode']==0){
        return $this->apiInfo(200, 'success', $result);
      }
      return $this->apiInfo(400, 'error', $result);
    }
  }

  /**
   * 取消定单
   * @return [type] [description]
   */
  public function cancelpay()
  {
    if($this->request->isPost()){
      $time = time();
      $openid = $this->request->post('openid', '');
      if(!$openid){
        Log::record('缺少参数'. json_encode($this->request->post()), 'cancelpay-error');
        return $this->apiInfo(400, '缺少参数');
      }
      $session_key = $this->session_key($openid); //会话密钥
      if($session_key === false){
        Log::record('找不到session_key'. json_encode($this->request->post()), 'cancelpay-error');
        return $this->apiInfo(405, '非法访问');
      }
      
      $data = [
        'openid' => $openid, //用户唯一标识符
        'appid'  => $this->appid, //小程序 appId
        'offer_id' => $this->offer_id, //米大师分配的offer_id
        'ts'  => $time, //UNIX 时间戳，单位是秒
        'zone_id' => $this->zone_id, //游戏服务器大区id,游戏不分大区则默认zoneId ="1",String类型
        'pf' => $this->request->post('pf', ''), //平台 如安卓：android
        'bill_no' => $this->request->post('bill_no', ''), //订单号，业务需要保证全局唯一；相同的订单号不会重复扣款。长度不超过63，只能是数字、大小写字母_-
      ];

      //生成签名
      $data = $this->signature($data, 'cancelpay', $session_key, $env='sandbox');
      
      //请求结果
      $result = $this->curlPost($this->cancelpayurl_sandbox . $this->access_token, $data);

      Log::record('请求参数：'.json_encode($data). '; 请求结果：'. json_encode($result), 'cancelpay-info');
      if($result && isset($result['errcode']) && $result['errcode']==0){
        return $this->apiInfo(200, 'success', $result);
      }
      return $this->apiInfo(400, 'error', $result);
    }
  }

  /**
   * 获取用户session_key(会话密钥)
   * @param  [type] $openid [description]
   * @return [type]         [description]
   */
  protected function session_key($openid)
  {
    $user = Db::table('weixi_user')->where('openid', $openid)->find();
    if($user){
      return $user['session_key'];
    }else{
      return false;
    }
  }

  //获得当前用户的游戏币余额
  protected function getCurrrenB($openid, $session_key, $pf)
  {
    $time = time();
    $data = [
      "openid" => $openid,
      "appid"  => $this->appid,
      "offer_id" => $this->offer_id,
      "ts"  => $time,
      "zone_id" => $this->zone_id,
      "pf" => $pf
    ];

    //生成签名
    $data = $this->signature($data, 'getbalance', $session_key, $env='sandbox');
      
    //请求结果
    $result = $this->curlPost($this->currentBurl_sandbox . $this->access_token, $data);

    Log::record('获取游戏币余额结果：'.json_encode($result), 'getCurrrenB-info');
    if( isset($result['errcode']) ){
      if($result['errcode']==0){
        return $result['balance'];
      }else{
        return $result;
      }
    }
    return false;
  }



  /**
 * 通知游戏服务器
 * @param  $notifyData array [微信支付的回调信息]
 * @param  $orderHash array [保存的订单缓存信息]
 * @return  void 
 */
private function notifyServer($notifyData)
{
  //*************
  return true;
}

/**
 * curl POST请求，发送和接收数据为json对象
 * @param  [type] $url  [description]
 * @param  array  $data [description]
 * @return [type]       [description]
 */
private function curlPost($url, $data=[])
{
  $data_string = json_encode($data);
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
  curl_setopt($ch, CURLOPT_POSTFIELDS,$data_string);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json',
      'Content-Length: ' . strlen($data_string)
  ));
  $result = json_decode(curl_exec($ch), true);
  curl_close($ch);
  return $result;
}

/**
 * 生成米大师支付签名
 * @param  $data 参与签名的数据
 * @param  $action 具体操作方法
 * @param $session_key 会话密钥
 * @param $env 环境（沙箱或正式）
 */
private function signature(array $data, $action, $session_key, $env='sandbox')
{
  $action = ($env==='sandbox') ? 'sandbox/'.$action : $action;

  $Mkey = ($env==='sandbox') ? $this->Mkey_sandbox : $this->Mkey;
  ksort($data);
  $str = "";
  foreach ($data as $key => $value) {
    $str .= $key . "=" . $value . "&";
  }
  $sig = hash_hmac("sha256", $str . "org_loc=/cgi-bin/midas/$action&method=POST&secret=$Mkey", $Mkey);

  $data['sig'] = $sig;
  $data['access_token'] = $this->access_token; //接口调用凭证, 注意：这个参数在第一次生成签名时不调用
  ksort($data);
  $str2 = "";
  foreach ($data as $k => $v) {
    $str2 .= $k . "=" . $v . "&";
  }
  $mp_sig = hash_hmac("sha256", $str2 . "org_loc=/cgi-bin/midas/$action&method=POST&session_key=$session_key", $session_key);

  $data["sig"] = $sig;
  $data["mp_sig"] = $mp_sig;

  return $data;
}


}
