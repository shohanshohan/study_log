<?php
namespace app\pay\controller;
use think\Controller;
use think\Config;
use think\Log;
use think\Db;

/**
* 支付宝支付回调
*/
class Alinotify extends Controller
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
  
  //游戏服地址
  private $game_server_ip = '47.107.47.43';

  //游戏服端口
  private $game_server_port = '46008';

  public function aliwapnotify()
  {
    $data = $this->request->post();
    /*
    alipay-notify-params:{"gmt_create":"2019-10-28 20:08:42","charset":"UTF-8","seller_email":"admin@45you.cn","subject":"\u9e1f\u5e01\u5145\u503c","sign":"gRSYmmcXnm5\/QMbM92FkF1RN1a\/C+GFFCYkoQE7e0+AaefufIOb\/wpwCpsX62wedM9Kpi63JfffJ8eUTHpc9VRunW67H52HfUhpFr3V6WeNt8EF+6XEWVUzIYoKd5wYJcx1oEg4qRQRIt9wNm+\/SxRPLG1QBlJ12af8i8X4CwdxFg+kJLHOINlq\/9RH2BpT8zSpTgueQ3HycWNi8KajDt+YuWMZ6UoMSfHDIHzQC9X7jbNrHuDkdZCgZXysL3NGzpfiPNd+J8gPdjdc8+umMHprJa2so13UDgiq9ss+y9ccIlXcFaDPqlsgSITt4qkKZ3+\/mY3aXI81Q5Mb2EErKQQ==","body":"\u9e1f\u5e01\u5145\u503cH5, ext: 18; amount: 6","buyer_id":"2088702341409600","invoice_amount":"0.01","notify_id":"2019102800222200843009601412380083","fund_bill_list":"[{\"amount\":\"0.01\",\"fundChannel\":\"ALIPAYACCOUNT\"}]","notify_type":"trade_status_sync","trade_status":"TRADE_SUCCESS","receipt_amount":"0.01","buyer_pay_amount":"0.01","app_id":"2018051460099581","sign_type":"RSA2","seller_id":"2088721198599081","gmt_payment":"2019-10-28 20:08:43","notify_time":"2019-10-28 20:08:43","passback_params":"ext=18&amount=6&account=wx_10000001","version":"1.0","out_trade_no":"wx_10000001015722645172292","total_amount":"0.01","trade_no":"2019102822001409601406044624","auth_app_id":"2018051460099581","buyer_logon_id":"135****1749","point_amount":"0.00"}
     */
    $date = date('Y-m-d H:i:s');
    Log::write($date . '---alipay-notify-params:' . json_encode($data),'aliwappay-notify-param');
    if(isset($data['trade_status']) && $data['trade_status']=='TRADE_SUCCESS'){
      if(!isset($data['passback_params'])) {
        echo "success"; exit();
      };
      $config = Config::get('aliwapPay');
      if($data['app_id'] !== $config['app_id']) {
        Log::write('支付验证不通过！app_id: ' . $config['app_id'], 'aliwappay-notify-error');
        echo "success"; exit();
      }
      $data['total_amount'] = 1;
      parse_str($data['passback_params'], $params);
      if($params['amount'] != $data['total_amount']) {
        Log::write('支付验证不通过！amount: ' . $params['amount'], 'aliwappay-notify-error');
        echo "success"; exit();
      }
      if($params['account'] != substr($data['out_trade_no'], 0, 11)) {
        Log::write('支付验证不通过！account: ' . $params['account'], 'aliwappay-notify-error');
        echo "success"; exit();
      }
      $extData = $this->extAmountData;
      if(!isset($extData[$params['ext']]) || $extData[$params['ext']] != $data['total_amount']) {
        Log::write('支付验证不通过！extAmount: ' . $extData[$params['ext']], 'aliwappay-notify-error');
        echo "success"; exit();
      }

      $serverData = [
        'order_id' => $data['trade_no'],
        'account' => $params['account'],
        'ext' => $params['ext'],
        'sign' => $data['sign'],
        'amount' => $params['amount'],
        'cp_order_id' => $data['out_trade_no']
      ];

      //通知游戏服务器，并保存充值数据到数据库
      $notifyServer = $this->notifyServer($serverData);
      Log::write($date . '---通知游戏服务器结果：' . serialize(['result'=>$notifyServer]), 'aliwappay-notifyServer-result');
      
      echo "success"; 
    }else{
      echo "fail";
    }
  }

  
  




}
