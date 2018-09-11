<?php
namespace factory\static;
/**
 * 静态工厂模式与抽象工厂模式类似，此模式用于创建一系列相关或相互依赖的对象。
 * 静态工厂模式与抽象工厂模式的区别在于，只使用一个静态方法来创建所有类型对象， 
 * 此方法通常被命名为 factory 或 build
 */

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
		throw new \Exception('Unknown format given');
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
