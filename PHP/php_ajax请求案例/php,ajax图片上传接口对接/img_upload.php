<?php
$file = $_FILES['myfile'];//得到传输的数据
//得到文件名称
$file_name = $file['name'];


$type = strtolower(substr($file_name,strrpos($file_name,'.')+1)); //得到文件类型，并且都转化成小写
$allow_type = array('jpg','jpeg','png'); //定义允许上传的类型
//判断文件类型是否被允许上传
if(!in_array($type, $allow_type)){
	$this->ajaxReturn(array('result'=>'error','cause'=>'上传图片类型只能是jpg,jpeg,png'),'json');die;
}
//$account = M('member_list')->where('mid='.$mid)->getField('account');
$length = strrpos($file_name,'.');

//修改图片名称
$name = substr_replace($file_name,date('Y-m-d').round(1-999),0,$length);
$upload_path = "./Uploads/head_img/"; //上传文件的存放路径

//开始移动文件到相应的文件夹
move_uploaded_file($file['tmp_name'],$upload_path.$name);
 	
  //echo "<script language='javascript'>alert('上传头像成功')</script>";
/*}else{
  $this->ajaxReturn(array('result'=>'error','cause'=>'上传头像失败'),'json');die;
   //echo "<script language='javascript'>alert('上传头像失败')</script>";
}*/
var_dump($name);die;  
//保存图片路径
/*$row = M('member_list')->where('mid='.$mid)->setField('image',$upload_path.$name);
 if ($row) {
	$this->ajaxReturn(array('result'=>1,'cause'=>'头像修改成功！'),'json');die;
} */