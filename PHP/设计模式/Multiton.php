<?php
//+--------多例模式是指存在一个类有多个相同实例，而且该实例都是该类本身---------+
//+--------多例类必须自己创建，管理自己的实例，并向外界提供自己的实例-----------+
//+--------多例模式实际上就是单例模式的推广----------------+

final class Multiton
{
  private static $instances = [];

  //私有构造函数阻止用户随意的创建该对象实例
  private function __construct()
  {

  }

  //获取实例
  public static function getInstance(string $instanceName): Multiton
  {
    if(!isset(self::$instances[$instanceName])){
      self::$instances[$instanceName] = new self();
    }
    return self::$instances[$instanceName];
  }

  //譔私有对象阻止实例被克隆
  private function __clone()
  {

  }

  //阻止实例被序列化
  private function __wakeup()
  {

  }


}
//$obj = new Multiton(); //这样不能获取实例，报错

$obj1 = Multiton::getInstance('obj1');
$obj2 = Multiton::getInstance('obj2');
var_dump($obj1);
var_dump($obj2);
var_dump($obj1==$obj2);
