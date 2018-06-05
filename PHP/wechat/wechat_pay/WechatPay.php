<?php
/**
 * Created by PhpStorm.
 * User: shohan
 * Date: 2018/3/12
 * Time: 18:51
 */
namespace app\game_api\controller;
use wechat\lib\WxPayApi;
use think\Request;

class WechatPay extends WechatBase
{
    private $appId = 'wx8c1dffd1434875df';
    private $mchId = '1495588962';//商户号
    private $notifyUrl = 'http://123.207.109.83:9999/game_api/wechat/notifyResult';//异步接收微信支付结果通知的回调地址
    //private $tradeType = 'APP';//交易类型
    private $tradeUrl = "https://api.mch.weixin.qq.com/pay/unifiedorder"; //微信统一下单请求地址
    private $orderqueryUrl = "https://api.mch.weixin.qq.com/pay/orderquery"; //微信查询订单请求地址
    private $closeorderUrl = "https://api.mch.weixin.qq.com/pay/closeorder"; //微信关闭订单接口
    private $game_server_ip = "120.79.224.160";
    private $game_server_port = 41006;

    /**
     * 发送下单请求；
     * @param  Curl   $curl 请求资源句柄
     * @return array       请求返回数据
     */
    public function payRequest() {
        $request = Request::instance();
        if($request->isPost()) {
            $orderId = $this->p('oid', ''); //订单号
            $amount = (int)$this->p('amount', ''); //金额，单位（分）
            $body = $this->p('body', ''); //商品描述
            $device_info = $this->p('device', ''); //设备号
            $detail = $this->p('detail', ''); //商品详情
            $time_start = $this->p('timeStart', date('YmdHis')); //订单生成时间
            $goods_tag = $this->p('oidTag', ''); //订单优惠标记
            $trade_type = $this->p('type', 'APP'); //交易类型
            $ext = $this->p('ext', ''); //消费点（不是微信请求参数）
            if (empty($orderId) || empty($amount) || empty($body) || empty($ext)) {
                return $this->apiInfo(400, '缺少必要参数值！');
            }
            $data = [
                'appid' => $this->appId,
                'mch_id' => $this->mchId,//微信支付分配的商户号
                'body' => $body,
                'nonce_str' => $this->getNonceStr(), //随机字符串，长度要求在32位以内
                'out_trade_no' => $orderId,
                'total_fee' => $amount,
                'spbill_create_ip' => $this->get_ip(), //终端IP
                //'spbill_create_ip' => '123.12.12.123',
                'notify_url' => $this->notifyUrl, //异步接收微信支付结果通知的回调地址
                'trade_type' => $trade_type,
                'device_info' => $device_info,
                'time_start' => $time_start,
                'detail' => $detail,
                'goods_tag' => $goods_tag
            ];

            $data['sign'] = $this->getSign($data);
            $xml = $this->arrayToXml($data);//return $xml;
            $data2 = $this->postXmlCurl($xml, $this->tradeUrl);
            $data['ext'] = $ext;
            if (isset($data2['return_code'])) {
                if ($data2['return_code'] == 'SUCCESS') {
                    if (isset($data2['prepay_id']) && $this->setHash('orderId:' . $orderId, $data, 86400 * 3)) {
                        //返回给app的调起支付参数
                        $apiData = [
                            'appid' => $this->appId,
                            'partnerid' => $this->mchId,
                            'prepayid' => $data2['prepay_id'], //微信返回的支付交易会话ID
                            'package' => 'Sign=WXPay', //扩展字段
                            'noncestr' => $this->getNonceStr(),
                            'timestamp' => (string)time(),
                        ];
                        $apiData['sign'] = $this->getSign($apiData);
                        return $this->apiInfo(200, '请求成功', $apiData);
                    } else {
                        return $this->apiInfo(400, $data2['err_code_des']);
                    }
                } else {
                    return $this->apiInfo(400, $data2['return_msg']);
                }
            }
            return $this->apiInfo(400, '请求出错！');
        }else{
            return $this->apiInfo(400, '不是post方法请求！');
        }
    }

    /**
     * 支付结果通用通知
     * @param function $callback
     */
    public function notifyResult()
    {
        $f = $this->setRedis('wxNotifytime',date('Y-m-d H:i:s').'微信执行了通知回调地址notifyResult',7200*12);
        //获取通知的数据
        $fileContent = file_get_contents("php://input");
        //转换为simplexml对象
        $xmlResult = simplexml_load_string($fileContent, 'SimpleXMLElement', LIBXML_NOCDATA);

        $data = $this->xmlToArray($fileContent);
        if(!empty($data)){
            $code = isset($data['return_code']) ? $data['return_code'] : '';
            if($code=='SUCCESS'){
                $orderHash = $this->getHash("orderId:".$data['out_trade_no']);
                //签名验证,并校验返回的订单金额是否与商户侧的订单金额一致，防止数据泄漏导致出现“假通知”
                if($this->payResult($data,$orderHash['out_trade_no'],$orderHash['total_fee'])){
                    //通知游戏服务器，并保存充值数据到数据库
                    $notifyServer = $this->notifyServer($data,$orderHash);
                    $data['notifyServerMsg'] = $notifyServer;
                    $re = $this->setHash('wxNotifyResult',$data,3600*24);
                    $returnData = [
                        'return_code' => 'SUCCESS',
                        'return_msg' => 'OK'
                    ];
                }else{
                    $returnData = [
                        'return_code' => 'FAIL',
                        'return_msg' => '校验失败'
                    ];
                }
            }else{
                $returnData = [
                    'return_code' => 'FAIL',
                    'return_msg' => $data['return_msg']
                ];
            }
        }else{
            $re = $this->setHash('wxNotifyResult',['result'=>'resultData--none','xmlResult'=>serialize($xmlResult), 'time'=>date('Y-m-d H:i:s')],7200*12);
            $returnData = [
                'return_code' => 'FAIL',
                'return_msg' => '数据解析失败'
            ];
        }
        return $this->arrayToXml($returnData);
    }

    /**
     * 支付结果验证
     */
    public function payResult($data,$orderId,$amount)
    {
        if($this->verifySign($data)){ //验证签名

            if($data['out_trade_no']==$orderId && $data['total_fee']==$amount){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /**
     * 查询订单状态
     * @param  string $transaction_id 订单号
     * @return array $msgData  订单查询结果
     */
    public function queryOrder($order_id='') {
        $order_id = $this->p('oid','');
        $msgData['erro'] = '订单查询签名失败!';
        $data = [
            'appid'        =>  $this->appId,
            'mch_id'       =>  $this->mchId,
            'out_trade_no' =>  $order_id,
            'nonce_str'    =>  $this->getNonceStr()
        ];
        $sign = $this->getSign($data);
        $data['sign'] = $sign;
        $xml = $this->arrayToXml($data);
        $data2 = $this->postXmlCurl($xml, $this->orderqueryUrl);
        $return_code = isset($data2['return_code']) ? $data2['return_code'] : '';
        $result_code = isset($data2['result_code']) ? $data2['result_code'] : '';
        if($return_code=='SUCCESS'){
            if($result_code=='SUCCESS'){
                $msgData = $data2; //trade_state交易状态，SUCCESS—支付成功，其它状态描述见微信接口文档
            }else{
                $msgData['erro'] = $data2['err_code_des'];
            }
        }
        return $msgData;
        //return $this->apiInfo(200,'查询订单',$msgData);
    }

    /**
     * 关闭订单
     * @return string  结果
     */
    public function closeOrder()
    {
        $out_trade_no = $this->p('oid','gc_10000001');
        $data = [
            'appid' => $this->appId,
            'mch_id' => $this->mchId,
            'nonce_str' => $this->getNonceStr(),
            'out_trade_no' => $out_trade_no
        ];
        $sign = $this->getSign($data);
        $data['sign'] = $sign;
        $xml = $this->arrayToXml($data);
        $data2 = $this->postXmlCurl($xml, $this->closeorderUrl);
        //return $this->apiInfo(200,'msg',$data2);
        $return_code = $data2['return_code'] ? $data2['return_code'] : '';
        if($return_code=='SUCCESS'){
            if($this->verifySign($data2)){ //验证签名
                if(isset($data2['err_code'])){
                    return $this->apiInfo(200,'状态码',['state'=>$data2['err_code']]); //ORDERCLOSED为已关闭
                }else{
                    return $this->apiInfo(200,'状态码',['state'=>'SUCCESS']);
                }
            }
        }
        return $this->apiInfo(400,'签名失败！');
    }

    public function test()
    {
        echo json_encode('wechat test');
    }

    /**
     * 通知游戏服务器
     */
    public function notifyServer($notifyData=[],$orderHash)
    {
        if(isset($notifyData['transaction_id'])){
            $ext = $orderHash['ext'];
            $data = [
                'order_id' => $notifyData['transaction_id'],
                'cp_order_id' => $notifyData['out_trade_no'],
                'ext'         => $ext,
                'sign'        => $notifyData['sign'],
                'amount'   => $notifyData['total_fee'],
            ];
        }
        $order_id = isset($data['order_id']) ? $data['order_id'] : '' ; //微信返回的订单号
        $user_id = $role_id = isset($data['cp_order_id']) ? substr($data['cp_order_id'],0,11) : '';
        $cp_order_id = isset($data['cp_order_id']) ? $data['cp_order_id'] : '';
        $ext = isset($data['ext']) ? $data['ext'] : '';
        $sign = isset($data['sign']) ? $data['sign'] : '';
        $amount = $game_coin = isset($data['amount']) ? $data['amount'] : '';
        if($order_id && $user_id && $cp_order_id && $ext && $amount){
            $success = 1;
        }else{
            $success = 0;
        }

        $con = mysqli_connect("120.79.224.160", "root", "123456");
        if(!$con){
             return '数据库连接不上！';
        }
        mysqli_select_db($con,"jieke");

        //检查重复订单号
        $sql = "select count(*) from t_player_recharge where order_id='".$order_id."' and sdk_id=2";
        $result = mysqli_query($con,$sql);
        $row = mysqli_fetch_assoc($result);
        if ($row["count(*)"] > 0)
        {
            return '订单已存在！';
        }
        //生成充值
        $timeNow = date('Y-m-d H:i:s',time());
        $query = "insert into t_player_recharge(sdk_uid, login_id, user_id, order_id, amount, game_coin, cp_order_id, success, ext, sign_id, create_time, sdk_id) values('$user_id','$role_id','$user_id','$order_id',$amount,'$game_coin', '$cp_order_id',$success,'$ext','$sign','$timeNow',2)";

        mysqli_query($con,$query);
        $insertResult = mysqli_insert_id($con);
        if(!$insertResult){
            return '充值数据保存失败！';
        }
        mysqli_close($con);
        //通知游戏服务器
        $order_id_len 		= strlen($order_id);
        $user_id_len 		= strlen($user_id);
        $role_id_len 		= strlen($role_id);
        $cp_order_id_len 	= strlen($cp_order_id);
        $ext_len 			= strlen($ext);
        $sign_len 			= strlen($sign);

        $struct = pack("s", $order_id_len);
        $struct .= pack("a$order_id_len", $order_id);

        $struct .= pack("s", $user_id_len);
        $struct .= pack("a$user_id_len", $user_id);

        $struct .= pack("s", $role_id_len);
        $struct .= pack("a$role_id_len", $role_id);

        $struct .= pack("f", $amount);
        $struct .= pack("i", $game_coin);

        $struct .= pack("s", $cp_order_id_len);
        $struct .= pack("a$cp_order_id_len", $cp_order_id);

        $struct .= pack("i*", $success);

        $struct .= pack("s", $ext_len);
        $struct .= pack("a$ext_len", $ext);

        $struct .= pack("s", $sign_len);
        $struct .= pack("a$sign_len", $sign);

        $len_body = strlen($struct);
        $opcode = 1095;


        $struct_full = pack("s", $len_body);
        $struct_full .= pack("s", $opcode);
        $struct_full .= pack("a*", $struct);
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        $result = socket_connect($socket, $this->game_server_ip, $this->game_server_port);

        if(!$result){
            socket_close($socket);
        }else{
            return '游戏对话服务连接错误！';
        }

        if(socket_write($socket, $struct_full, strlen($struct_full))){
            return $this->HashAdd('notifyServer',1);
        }
        usleep(50000);

        socket_shutdown($socket);
        socket_close($socket);
    }
}
