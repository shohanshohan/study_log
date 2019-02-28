<?php
//+------------单例模式-------------+
//+------------在应用程序调用的时候，只能获得一个对象实例-----------------


class Singleton
{
  private static $instance;

  //通过懒加载获得实例，在第一次使用的时候创建
  public static function getInstance(): Singleton
  {
    if(null === static::$instance){
      static::$instance = new static();
    }
    return static::$instance;
  }

  /**
   * 不允许从外部调用以防止创建多个实例
   * 要使用单例，必须通过 Singleton::getInstance() 方法获取实例
   */
  private function __construct()
  {
    
  }

  //防止被克隆
  private function __clone()
  {

  }

  //防止反序列化（这将创建它的副本）
  private function __wakeup()
  {

  }

  public static function test()
  {
    echo 'singleton-test';
  }
}

//$obj = new Singleton(); //这样是不行的，会报错，因为 __construct()方法私有化了


$obj = Singleton::getInstance(); //正确的实例化方式

$obj::test();
