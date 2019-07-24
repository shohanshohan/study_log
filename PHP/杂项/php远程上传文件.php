<?php

/*远程上传图片至资源服务器*/
//远程请求函数
private function curlUpload($url, $data)
{
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  //curl_setopt($ch, CURLOPT_HEADER, false); //设置http头信息，默认false,可不填
  curl_setopt($ch, CURLOPT_POST, 1);
  if(!empty($data) && is_array($data)){
      curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
  }
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  //curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
  $result = curl_exec($ch);
  curl_close($ch);
  return $result;
}

public function uploadImgs()
{
  //...... 这里省略获取前端发送的图片 $_FILES
  $file = $_FILES['filename'];
  $uploadData = [
        'name' => $name,
        'itemid' => $itemid,
        'filename' => $newName
      ]; //上传至远程服务器数据
      if(version_compare(phpversion(),'5.5.0') >= 0 && class_exists('CURLFile')){
          $uploadData['file'] = new \CURLFile(realpath($file['tmp_name']));
      }else{
          $uploadData['file'] = '@'.$file['tmp_name']; //加@符号curl就会把它当成是文件上传处理
      }
      //远程返回的结果
      $uploadRes = $this->curlUpload($this->uploadUrl, $uploadData);
      return $uploadRes;
}

/*以上为请求远程服务器代码*/
/*以下为远程服务器接收请求代码*/
public function uploadGoodsImg()
  {
    if($_FILES)
    {
        $itemid = $this->request->post('itemid');
        $name = $this->request->post('name');
        $filename = $this->request->post('filename');

        $file = $_FILES['file'];

        $upload_path = APP_PATH. '../public/dsmall/goodsImg/' . $itemid . '/' . $name;
        $oneImg = false; //是否为单张图片上传
        if($name === 'img4' || $name === 'img1') {//如果是首页大图或列表图，则只保留一张图片
            $oneImg = true;
        }

        if(!is_dir($upload_path)){ //如果路径不存在则创建（第一次上传图片）
            @mkdir($upload_path, 0777, true);
        } else {
            @chmod($upload_path, 0777); //设置权限
            $fileArr = scandir($upload_path);
            //如果是首页大图或列表图，则只保留一张图片
            if($oneImg === true && count($fileArr) > 2) {
              foreach ($fileArr as $value) {
                if($value != '.' && $value != '..'){
                  @chmod($upload_path . $value, 0777); //设置权限
                  @unlink($upload_path . $value); //删除废弃的图片文件
                }
              }
            }
        }

        $newName = $filename;
        if(move_uploaded_file($file['tmp_name'], $upload_path . '/' . $newName)) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
  }



