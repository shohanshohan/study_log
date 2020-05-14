<?php
/*
* 文件和文件夹处理工具
*/
class Filedir
{
  //循环删除文件和目录
  public function delDirAndFile($dirName)
  {
    $f = @opendir($dirName);
    if($f) {
      while(false !== ($item = readdir($f))) {
        if($item != '.' && $item != '..') {
          @unlink($dirName.'/'.$item);
        }
      }
    }
    @closedir($f);
    @rmdir($dirName);
  }
  
  
  

}
