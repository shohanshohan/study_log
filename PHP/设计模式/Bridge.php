<?php
//+--------桥梁模式---------+
//+-----将抽象与实现分离，这样两者可以独立地改变-------+



interface FormatterInterface
{
  public function format(string $text);
}

class PlainTextFormatter implements FormatterInterface
{
  public function format(string $text)
  {
    return $text;
  }
}

class HtmlFormatter implements FormatterInterface
{
  public function format(string $text)
  {
    return sprintf('<p>%s</p>', $text);
  }
}



abstract class Service 
{
  protected $implementation;

  public function __construct(FormatterInterface $printer)
  {
    $this->implementation = $printer;
  }

  public function setImplementation(FormatterInterface $printer)
  {
    $this->implementation = $printer;
  }

  abstract public function get();
}

class HelloWorldService extends Service 
{
  public function get()
  {
    return $this->implementation->format('Hello World');
  }
}

$service1 = new HelloWorldService(new PlainTextFormatter());
var_dump($service1->get());
echo '<br>';
$service2 = new HelloWorldService(new HtmlFormatter());
var_dump($service2->get());
