<?php

use test\Test;
use test\test1\Test1;

class Index
{
  
  function __construct()
  {
    spl_autoload_register([$this, 'autoload']);
  }


  function autoload($className)
  {
    $className = ltrim($className, '\\');
    $fileName = '';
    if( $lastPos = strrpos($className, '\\') ){
      $namespace = substr($className, 0, $lastPos);
      $className = substr($className, $lastPos + 1);
      $fileName = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR; 
    }
    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
    require $fileName;
  }

  public function test()
  {
    (new Test())->index();
  }

  public function test1()
  {
    (new Test1())->index();
  }
}




$obj = new Index(); //这个相当于是入口文件，注册了自动加载后，就可以根据命名空间来引入文件了
// $test = new \test\Test();
// $test->index();
$obj->test();
echo '<br>';
$obj->test1();
