<?php
class Base64post
{
  public function uploadimg()
  {
    header('Content-type:text/html;charset=utf-8');
    $user_id = (int)$this->request->get('user_id', '');
    //$base64_image_content = file_get_contents('php://input');
    $base64_image_content = $this->request->post('portrait', '');

    $user = userModel::get($user_id);
    if(!$user || !$base64_image_content) {
      return api(110, '参数错误');
    }

    if (!preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content)){
        return api(110, 'portrait 参数错误');
    }

    if(preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)) {
      $prefix = $result[2]=='jpeg' ? 'jpg' : $result[2];
      $img = '/portrait/'.$user_id.time().'.'.$prefix;
    }

    $url = Config::get('portrait_url');
    $res = $this->httpsRequest($url, ['old_portrait'=>$user->avatar, 'new_portrait'=>$img, 'base64_content'=>$base64_image_content]);
    Log::write('portrait-res: '.$res, 'portrait-info');
    $user->avatar = $img;
    if($res) {
      $user->save();
      return api(0, 'success', ['result'=>0, 'portrait'=>$img]);
    }
    return api(0, 'fail', ['result'=>1, 'url'=>$url]);
  }
  
  //保存图片
  function uploadportrait()
  {
    header('Content-type:text/html;charset=utf-8');
    $oldPortrait = $this->request->post('old_portrait', '');
    $newPortrait = $this->request->post('new_portrait', '');
    $base64_content = $this->request->post('base64_content', '');
    Log::write('newPortrait: '.$newPortrait, 'uploadportrait');
    if(!$newPortrait || !$base64_content) {
      return false;
    }
    if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_content, $result)) {
      $new_file = $this->uploadpath . $newPortrait;
      if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_content)))){
        @unlink($this->uploadpath . $oldPortrait);
        return true;
      }
    }
    return false;
  }


}



