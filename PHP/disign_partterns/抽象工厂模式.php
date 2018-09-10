<?php
//在不指定具体类的情况下创建一系列相关或依赖对象。 通常创建的类都实现相同的接口。
//抽象工厂的客户并不关心这些对象是如何创建的，它只是知道它们是如何一起运行的。
namespace design\factory;
/**
 * 这个抽象工厂是创建一些组件的契约
 * 在 web 中，有两种呈现文本的方式：HTML or JSON
 */
abstract class AbstractFactory
{
	abstract public function createText(string $content): Text;
}


class JsonFactory extends AbstractFactory
{
	public function createText(string $content): Text
	{
		return new JsonText($content);
	}
}


class HtmlFactory extends AbstractFactory
{
	public function createText(string $content): Text
	{
		return new HtmlText($content);
	}
}

abstract class Text
{
	private $text;
	public function __construct(string $text)
	{
		$this->text = $text;
	}
}


class JsonText extends Text
{
	
	
}

class HtmlText extends Text
{
	
}

/**
* 上面 ：Text; 表示返回Text对象实例。是一个类型约束作用。如：:int; 表示返回类型为int类型
*/
