<?php
namespace app\game\controller;
use think\Log;
use think\Db;

/**
* 游戏登录接口
* 获取openid
*/
class Login extends Base
{
  protected $appid = '*********'; //小程序 appId
  protected $secret = '**********'; //小程序 appSecret
  private $grant_type = 'authorization_code'; //授权类型，此处只需填写 authorization_code
  private $wxUrl = 'https://api.weixin.qq.com/sns/jscode2session'; //微信请求地址
  
  public function openid()
  {
    if($this->request->isPost()){
      $js_code = $this->request->post('code', '');
      if($js_code){
        $url = $this->wxUrl . "?appid=".$this->appid."&secret=".$this->secret."&js_code=".$js_code."&grant_type=".$this->grant_type;
        $result = $this->curl_get_https($url);
        Log::record('请求微信接口返回信息：' . json_encode($result), 'login-openid-result');
        if($result && isset($result['openid'])){
          $session_key = $result['session_key'];
          $openid = $result['openid'];
          unset($result['session_key']);

          $con = mysqli_connect('47.107.49.10', "root", "123456");
          if(!$con){
            Log::record('47.107.49.10数据库连接不上！', 'login-openid-mysql');
            return '数据库连接不上！';
          }
          mysqli_select_db($con,"weixi");

          $sql = "select count(*) from weixi_user where openid='".$openid."'";
          $res = mysqli_query($con,$sql);
          $row = mysqli_fetch_assoc($res);

          $token = md5(time().$openid);
          $time = time();
          if ($row["count(*)"] > 0)
          {
            $query = "update weixi_user set token='".$token."',session_key='".$session_key."',update_time=$time where openid='".$openid."'";
          }else{
            $query = "insert into weixi_user (openid,token,session_key,create_time,update_time) values ('$openid','$token','$session_key',$time,$time)";
          }
          mysqli_query($con,$query);
          $insertResult = mysqli_affected_rows($con);
          mysqli_close($con);
          
          Log::record('登录数据保存结果: '. $insertResult, 'openid-weixi-user');
          
          $result['token'] = $token;
          return $this->apiInfo(200, 'success', $result);
        }
        return $this->apiInfo(405, 'error', $result);
      }else{
        Log::record('没有接收到参数code', 'login-openid-error');
        return $this->apiInfo(400, '没有接收到参数code');
      }
    }
  }

  //get请求接口
  public function curl_get_https($url){
    $curl = curl_init(); // 启动一个CURL会话
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $tmpInfo = curl_exec($curl);     //返回api的json对象
    //关闭URL请求
    curl_close($curl);
    return json_decode($tmpInfo,true);   //返回数组 
    //返回json对象
    //return $tmpInfo;
  }
  
  public function postRequest($postUrl='', $post_data = '', $timeout = 15)
  {
    if(empty($postUrl)){
      return false;
    }
    $ch = curl_init();//初始化curl
    curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
    //curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
    curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
    if($post_data != ''){
      curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    }
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_HEADER, false);
    $file_contents = curl_exec($ch);
    curl_close($ch);
    return $file_contents;
  }



}
