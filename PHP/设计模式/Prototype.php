<?php
//+--------原型模式---------+
//+--------相比正常创建一个对象，首先创建一个原型，然后克隆它会更节省开销--------+
//+--------例如：通过ORM模型一次性往数据库插入1000000条数据---------------

require 'vendor/autoload.php';

abstract class BookPrototype
{
  protected $title;
  protected $category;

  abstract public function __clone();

  public function getTitle(): string
  {
    return $this->title;
  }

  public function setTitle($title)
  {
    $this->title = $title;
  }
}


class BarBookPrototype extends BookPrototype
{
  protected $category = 'Bar';

  public function __clone()
  {

  }
}

class FooBookPrototype extends BookPrototype
{
  protected $category = 'Foo';

  public function __clone()
  {

  }
}


class PrototypeTest extends \PHPUnit\Framework\TestCase
{
  public function testGetFooBook()
  {
    $fooPrototype = new FooBookPrototype();
    $barPrototype = new BarBookPrototype();

    for ($i=0; $i<1000; $i++){
      $book = clone $fooPrototype;
      $book->setTitle('Foo Book No: ' . $i);
      echo $book->getTitle() . '<br>';
      $this->assertInstanceOf(FooBookPrototype::class, $book);
    }

    for ($i=0; $i<1000; $i++){
      $book = clone $barPrototype;
      $book->setTitle('Bar Book No: ' . $i);
      echo $book->getTitle() . '<br>';
      $this->assertInstanceOf(BarBookPrototype::class, $book);
    }
  }
}

$obj = new PrototypeTest();
$obj->testGetFooBook();


