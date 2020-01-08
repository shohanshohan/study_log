<?php
namespace app\duobao\controller;
use think\Controller;
use think\Log;

/**
* 图片上传下载接口
*/
class Upload extends Controller
{
  private $uploadpath = '/data/www/dingz/DB/duobao/';
  
  public function index()
  {
    echo 'hello litter boy';
  }

  //上传或更新图片
  public function uploadimg()
  {
    Log::write('params: ' . json_encode($this->request->post()), 'upload-duobao-goods-img');
    $oneImg = $this->request->post('oneImg', 0);
    $fileUrl = $this->request->post('fileUrl', 0);
    $waitsleep = $this->request->post('waitsleep', 1);
    if(!$fileUrl) {
      return '缺少参数！';
    }
    if($waitsleep) { //小睡一下，防止后台图片还没完整下载，这边访问不到
      usleep(500000);
    }
    $file = @file_get_contents($fileUrl, 0, null, 0, 1);
    if($file) {
      $filename = $this->uploadpath . substr($fileUrl, strpos($fileUrl, 'goodsImg/'));
      $upload_path = substr($filename, 0, strrpos($filename, '/'));

      if(!is_dir($upload_path)){ //如果路径不存在则创建（第一次上传图片）
        @mkdir($upload_path, 0777, true);
      } else {
        @chmod($upload_path, 0777); //设置权限
        $fileArr = scandir($upload_path);
        //如果是首页大图或列表图，则只保留一张图片
        if($oneImg && count($fileArr) > 2) {
          echo 'omeImg <br/>';
          foreach ($fileArr as $value) {
            if($value != '.' && $value != '..'){
              @chmod($upload_path . '/' .$value, 0777); //设置权限
              @unlink($upload_path . '/' . $value); //删除废弃的图片文件
            }
          }
        }
      }

      ob_start(); //打开输出
      readfile($fileUrl); //输出图片文件
      $img = ob_get_contents(); //获得内容
      ob_end_clean(); //清除输出并关闭
      $f = fopen($filename, "a");
      fwrite($f, $img);
      fclose($f);
      if(is_file($filename)) {
        Log::write('filename: ' . $filename, 'upload-duobao-goods-img-filename');
        return true;
      }
      return false;
    } else {
      return false;
    }
  }

  //删除图片
  public function deleteimg()
  {
    Log::write('params: ' . json_encode($this->request->post()), 'delete-duobao-goods-img');
    $oneImg = $this->request->post('oneImg', 0);
    
    $filename = $this->request->post('filename', '');
    if(!$oneImg && $filename) { //$filename 为数据库字段值
      $upload_path = $this->uploadpath . $filename;
      //删除废弃的图片文件
      if(@unlink($upload_path)){ 
        return true;
      }
    }
    return false;
  }
}

/*
请求的时候很简单，只要传访问地址，上面的是两边保存文件的目录都是一致的，所有就直接用url里的地址目录
$imgData = [
  'fileUrl' => $this->imgserver . $imgPath,
  'oneImg' => $oneImg
];
$httpRes = $this->httpRequest($this->uploadUrl, $imgData);

private function httpRequest($url, $data)
{
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  //curl_setopt($ch, CURLOPT_HEADER, false); //设置http头信息，默认false,可不填
  curl_setopt($ch, CURLOPT_POST, 1);
  if(!empty($data) && is_array($data)){
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
  }
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  $result = curl_exec($ch);
  curl_close($ch);
  return $result;
}
*/
