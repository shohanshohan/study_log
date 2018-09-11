<?php
namespace factory\adapter;
/**
 * 适配器模式，将一个类的接口转换成可应用的兼容接口。
 * 适配器使原本由于接口不兼容而不能一起工作的那些类可以一起工作。
 */


interface BookInterface
{
	public function turnPage();

	public function open();

	public function getPage(): int;
}


class Book implements BookInterface
{
	private $page;

	public function open()
	{
		$this->page = 1;
	}

	public function turnPage()
	{
		$this->page++
	}

	public function getPage(): int
	{
		return $this->page;
	}
}


/**
 * 这里是一个适配器，实现了 BookInterface
 * 因此不必去更改客户端代码当使用 Book
 * 这个类使接口进行适当的转换
 */
class EBookAdapter implements BookInterface
{
	private $eBook;

	public function __construct(EBookInterface $eBook)
	{
		$this->eBook = $eBook;
	}

	public function open()
	{
		$this->eBook->unlock();
	}

	public function turnPage()
	{
		$this->eBook->pressNext();
	}

	//注意这里适配器的行为，仅支持获取当前页。EBookInterface::getPage()返回两个整型
	public function getPage(): int
	{
		return $this->eBook->getPage()[0];
	}
}


interface EBookInterface
{
	public function unlock();

	public function pressNext();

	public function getPage(): array; //返回当前页和总页数的一个数组
}



/**
 * 这里是适配过的类，在生产代码中，这可能是来自另一个包的类，一些供应商提供的代码。
 * 它使用了另一种命名方案，并用另一种方式实现了类似的操作
 */
class Kindle implements EBookInterface
{
	private $page = 1;
	private $totalPages = 100;

	public function pressNext()
	{
		$this->page++;
	}

	public function unlock()
	{

	}

	public function getPage(): array
	{
		return [$this->page, $this->totalPages];
	}
}
