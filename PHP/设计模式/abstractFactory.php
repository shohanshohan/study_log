<?php
//+---------在不指定具体类的情况下创建一系列相关或依赖对象。---------+
//+---------通常创建的类都实现相同的接口-----------------------------+
//+---------抽象工厂的客户并不关心这些对象是如何创建的，它只是知道它们是如何一起运行的------+
/**
 *                           AbstractFactory (createText(content))
 *                              |        |
 *                              |        |     
 *                        HtmlFactory   JsonFactory                     
 */
require 'vendor/autoload.php';

abstract class AbstractFactory
{
  //输入字符参数，返回一个Text实例
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
  protected $text;

  public function __construct(string $text)
  {
    $this->text = $text;
  }
}

class JsonText extends Text
{
  public function getJson()
  {
    return json_encode($this->text);
  }
}

class HtmlText extends Text
{
  public function getHtml()
  {
    return htmlspecialchars($this->text);
  }
}

//测试类
class AbstractFactoryTest extends \PHPUnit\Framework\TestCase
{
  public function testCreateHtmlText()
  {
    $factory = new HtmlFactory();
    $text = $factory->createText('foobar');
    $this->assertInstanceOf(HtmlText::class, $text);
    var_dump($text->getHtml());
  }

  public function testCreateJsonText()
  {
    $factory = new JsonFactory();
    $text = $factory->createText('foobar-json');
    $this->assertInstanceOf(JsonText::class, $text);
    var_dump($text->getJson());
  }
}

$test = new AbstractFactoryTest();
$test->testCreateHtmlText();
$test->testCreateJsonText();




