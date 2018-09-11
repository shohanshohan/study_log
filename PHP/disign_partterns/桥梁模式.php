<?php
namespace factory\bridge;
/**
 * 桥梁模式，将抽象与实现分离，这样两者可以独立地改变
 */

//创建格式化接口
interface FormatterInterface
{
	public function format(string $text);
}


//创建PlainTextFormatter 文本格式类实现 FormatterInterface
class PlainTextFormatter implements FormatterInterface
{
	//返回字符串格式
	public function format(string $text)
	{
		return $text;
	}
}


//创建 HtmlFormatter html格式类实现 FormatterInterface 接口
class HtmlFormatter implements FormatterInterface
{
	//返回 html 格式
	public function format(string $text)
	{
		return sprintf('<p>%s</p>', $text);
	}
}


//创建抽象类 Service
abstract class Service
{
	protected $implemention;
	public function __construct(FormatterInterface $printer)
	{
		$this->implemention = $printer;
	}

	public function setImplementation(FormatterInterface $printer)
	{
		$this->implemention = $printer;
	}

	abstract public function get();
}


//创建 Service 子类 HelloWorldService
class HelloWorldService extends Service
{
	public function get()
	{
		return $this->implemention->format('Hello world!');
	}
}
