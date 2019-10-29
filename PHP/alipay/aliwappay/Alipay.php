<?php
namespace app\pay\controller;
use think\Controller;
use think\Config;
use think\Loader;
use think\Cache;
use think\Log;
use think\Db;

/**
* 支付宝wap手机支付
*/
class Alipay extends Controller
{
  //计费点对应的鸟币数组, ext => amount
  private $extAmountData = [
    '25' => 1,
    '18' => 6,
    '19' => 12,
    '20' => 25,
    '21' => 50,
    '22' => 108,
    '23' => 328
  ];

  public function index()
  {
    $this->assign('wappayUrl', $this->request->root(true) . '/paycheck');
    return $this->fetch('wappage');
  }

  public function check()
  {
    $ext = (int)$this->request->post('ext', 0);
    $amount = (int)$this->request->post('amount', 0);
    $account = (string)$this->request->post('account', 0);
    $checkResult = $this->checkParam($ext, $amount, $account);
    Log::write('checkResult: '.json_encode($checkResult), 'pay-check');
    if($checkResult === true) {
      Cache::set('aliwappay_' . $account, 1, 60); //有效期60s
      $url = $this->request->root(true) . "/aliwappage?ext=$ext&amount=$amount&account=$account";
      echo json_encode(['errcode'=>0, 'msg'=>'success', 'data'=>['url'=>$url]]);
      exit();
    }
    echo $checkResult; 
    exit();
  }


  private function checkParam($ext, $amount, $account)
  {
    if(!$ext || !$amount || !$account) {
      return json_encode(['errcode'=>110, 'msg'=>'缺少参数！', 'data'=>[]]);
    }
    $extAmountData = $this->extAmountData;
    if(!isset($extAmountData[$ext]) || $extAmountData[$ext] != $amount) {
      return json_encode(['errcode'=>110, 'msg'=>'非法参数！', 'data'=>[]]);
    }
    $user_id = substr($account, 3);
    $user = Db::connect(Config::get('bird_db'))->table('accounts_info')->where('user_id', $user_id)->find();
    if(!$user) {
      return json_encode(['errcode'=>110, 'msg'=>'用户不存在，请检查输入账号！', 'data'=>[]]);
    }
    return true;
  }


  public function wappage()
  {
    $ext = $this->request->get('ext', 0);
    $amount = $this->request->get('amount', 0);
    $account = $this->request->get('account', 0);
    if(!Cache::get('aliwappay_' . $account)) {
      $this->redirect($this->request->root(true) . '/alipay'); exit();
    }
    $checkResult = $this->checkParam($ext, $amount, $account);
    if($checkResult !== true) {
      $this->redirect($this->request->root(true) . '/alipay'); exit();
    }
    
    define("AOP_SDK_WORK_DIR", APP_PATH . '../runtime/');
    $body = '鸟币充值H5, ext: '.$ext. '; amount: '.$amount;
    $subject = '鸟币充值';
    //wx_10009633 000 182243029210
    $out_trade_no = $account . '0' . (microtime(true) * 10000);
    $total_amount = $amount;
    $passback_params = http_build_query(['ext'=>$ext, 'amount'=>$amount, 'account'=>$account]);

    Loader::import('alipay.wappay.buildermodel.AlipayTradeWapPayContentBuilder', EXTEND_PATH, '.php');
    $payRequestBuilder = new \AlipayTradeWapPayContentBuilder();
    $payRequestBuilder->setBody($body);
    $payRequestBuilder->setSubject($subject);
    $payRequestBuilder->setOutTradeNo($out_trade_no);
    $total_amount = 0.01; //测试
    $payRequestBuilder->setTotalAmount($total_amount);
    $payRequestBuilder->setPassbackParams($passback_params);

    Loader::import('alipay.wappay.service.AlipayTradeService', EXTEND_PATH, '.php');
    $config = Config::get('aliwapPay');
    $payResponse = new \AlipayTradeService($config);
    $result = $payResponse->wapPay($payRequestBuilder, $config['return_url'],$config['notify_url']);

    return;
  }








}
