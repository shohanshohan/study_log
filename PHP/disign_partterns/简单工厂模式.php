<?php
namespace factory\simple;
/**
 * 简单工厂模式是一个精简版的工厂模式
 * 它与静态工厂模式最大的区别是它不是『静态』的。
 * 因为非静态，所以你可以拥有多个不同参数的工厂，你可以为其创建子类。
 * 甚至可以模拟（Mock）他，这对编写可测试的代码来讲至关重要。 这也是它比静态工厂模式受欢迎的原因！
 */
class SimpleFactory
{
	public function createBicycle(): Bicycle
	{
		return new Bicycle();
	}
}

class Bicycle
{
	public function driveTo(string $destination)
	{

	}
}

$factory = new SimpleFactory();
$bicycle = $factory->createBicycle();
$bicycle->driveTo('paris');
