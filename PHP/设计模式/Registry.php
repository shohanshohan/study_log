<?php
//+-------注册模式--------+
//+------目的是能够存储在应用程序中经常使用的对象实例，通常会使用只有静态方法的抽象类来实现------+
//+------需要注意的是这里可能会引入全局的状态，我们需要使用依赖注入来避免它-------------+

abstract class Registry
{
  const LOGGER = 'logger';
  private static $storedValues = [];
  private static $allowKeys = [self::LOGGER];

  public static function set(string $key, $value)
  {
    if(!in_array($key, self::$allowKeys)){
      die('Invalid key given');
    }
    self::$storedValues[$key] = $value;
  }


  public static function get(string $key)
  {
    if(!in_array($key, self::$allowKeys) || !isset(self::$storedValues[$key])){
      die('Invalid key given');
    }
    return self::$storedValues[$key];
  }
}

$obj = new stdClass();
$key = Registry::LOGGER;

Registry::set($key, $obj);
$storedValue = Registry::get($key);
var_dump($storedValue);
