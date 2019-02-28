<?php
//+---------适配器模式----------+
//+------将一个类的接口转换成可应用的兼容接口，使原本由于接口不兼容而不能一起工作的类可以一起工作-------+

require 'vendor/autoload.php';

interface BookInterface
{
  public function turnPage();
  public function open();
  public function getPage(): int;
}


class Book implements BookInterface
{
  private $page;

  public function  open()
  {
    $this->page = 1;
  }

  public function turnPage()
  {
    $this->page++;
  }

  public function getPage(): int
  {
    return $this->page;
  }
}

/**
 * 这里是一个适配器，注意它实现了 BookInterface
 * 因此你不必去更改客户端代码当使用 Book
 */
class EBookAdapter implements BookInterface
{
  protected $eBook;

  public function __construct(EBookInterface $eBook)
  {
    $this->eBook = $eBook;
  }

  //进行适当的转换
  public function open()
  {
    $this->eBook->unlock();
  }

  public function turnPage()
  {
    $this->eBook->pressNext();
  }

  public function getPage(): int
  {
    return $this->eBook->getPage()[0];
  }
}



interface EBookInterface
{
  public function unlock();
  public function pressNext();
  public function getPage(): array;
}


class Kindle implements EBookInterface
{
  private $page = 1;

  private $totalPages = 100;

  public function unlock()
  {

  }

  public function pressNext()
  {
    $this->page++;
  }

  public function getPage(): array
  {
    return [$this->page, $this->totalPages];
  }

}


class AdapterTest extends \PHPUnit\Framework\TestCase
{
  public function testTurnPageOnBook()
  {
    $book = new Book();
    $book->open();
    $book->turnPage();
    echo $book->getPage() . '<br>';
    $this->assertEquals(2, $book->getPage());
  }

  public function testTurnPageOnKindleOnBook()
  {
    $kindle = new Kindle();
    $book = new EBookAdapter($kindle);
    $book->open();
    $book->turnPage();
    echo $book->getPage() . '<br>';
    $this->assertEquals(2, $book->getPage());
  }
}

$test = new AdapterTest();
$test->testTurnPageOnBook();
$test->testTurnPageOnKindleOnBook();
