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




$obj = new Index();
// $test = new \test\Test();
// $test->index();
$obj->test();
echo '<br>';
$obj->test1();
