<?php
//+------------静态工厂模式-------------+
//+------------与抽象工厂模式类似，此模式用于创建一系列相关或相互依赖的对象。-----------
//+------------与抽象工厂模式的区别在于，只使用一个静态方法来创建所有类型对象，此方法通常被命名为factory或build
//+----注意点1：静态意味着全局状态，因为它不能被模拟进行测试，所以它是有弊端的------
//+----注意点2：不能被分类或模拟或有多个不同的实例---------

final class StaticFactory
{
  public static function factory(string $type): FormatterInterface
  {
    if($type == 'number'){
      return new FormatNumber();
    }
    if($type == 'string'){
      return new FormatString();
    }
    throw new \InvalidArgumentException('Unknow format given');
  }
}


interface FormatterInterface
{

}

class FormatString implements FormatterInterface
{

}

class FormatNumber implements FormatterInterface
{

}


$obj1 = StaticFactory::factory('number');

$obj2 = StaticFactory::factory('string');

var_dump($obj1);
var_dump($obj2);

$obj3 = StaticFactory::factory('object');
