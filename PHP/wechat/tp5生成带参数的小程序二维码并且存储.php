<?php
namespace app\dsmall\controller;
use think\Controller;
use think\Config;
use think\Log;
use think\Cache;
use app\dsmall\model\User as userModel;

/**
* 获取小程序码
*/
class Qrcode extends Controller
{
  private $wxcodeUrl = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token="; //POST
  /*
    适用于需要的码数量极多的业务场景

    1）、永久有效

    2）、数量无限

    3）、scene是自定义的参数
  */
  private $wxAccessTokenUrl = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential"; //GET
  
  public function index()
  {
    $user_id = $this->request->get('user_id', '');
    $user = userModel::get($user_id);
    if(!$user) {
      return api(110, '用户不存在！');
    }
    list($access_token, $wxResult) = $this->getAccessToken();
    if(!$access_token) {
      Log::write('获取微信凭证access_token失败, wxResult: '.json_encode($wxResult), '10038-wxResult-error');
      return api(120, '获取微信凭证access_token失败！', $wxResult);
    }
    $this->wxcodeUrl .= $access_token;
    //获取结果
    $result = $this->httpsRequest($this->wxcodeUrl, json_encode(["scene"=>"pid=$user_id", "width"=>150]));
    //解析图片
    $base64_img = "data:image/jpeg;base64,".base64_encode( $result );
    $imgPath = 'uploads/' . uniqid() . '.jpg';
    $res = $this->file_put($base64_img, $imgPath); //保存图片至指定路径
    if($base64_img) {
      if($res) {
        return api(0, 'success', ['imgpath'=>'/' . $imgPath]);
      } 
    }else {
      Log::write('获取微信小程序码失败 ', '10038-wxResult-error');
    }
    return api(120, '获取失败！', $result);
  }

  /**
   * 获取微信接口调用凭证
   * @return array [description]
   */
  protected function getAccessToken()
  {
    $access_token = 0;
    $wxResult = '';
    $wx_access_token = Cache::get('wx_access_token');
    if($wx_access_token) {
      $access_token = $wx_access_token;
    } else {
      $appid = Config::get('appid');
      $appkey = Config::get('appKey');
      $this->wxAccessTokenUrl .= "&appid=".$appid."&secret=".$appkey;
      $wxResult = curl_get_https($this->wxAccessTokenUrl);
      if($wxResult && isset($wxResult['access_token'])) {
        $access_token = $wxResult['access_token'];
        $expires_in = $wxResult['expires_in']; //凭证有效时间
        Cache::set('wx_access_token', $access_token, $expires_in - 1);
      }
    }
    return [$access_token, $wxResult];
  }

  //保存图片
  protected function file_put($base64_image_content,$new_file)
  {
      header('Content-type:text/html;charset=utf-8');
      if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)){
          if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_image_content)))){
             return true;
          }else{
              return false;
          }
      }
  }

  //远程请求
  protected function httpsRequest($url, $data, $method='POST')
  {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
    if($method=='POST')
    {
        curl_setopt($curl, CURLOPT_POST, 1);
        if ($data != '')
        {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
    }
 
    curl_setopt($curl, CURLOPT_TIMEOUT, 30);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($curl);
    curl_close($curl);
    return $result;
  }

}
