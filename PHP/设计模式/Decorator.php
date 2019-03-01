<?php
//+--------装饰模式---------+
//+-------为类实例动态增加新的方法----------+

interface RenderableInterface
{
  public function renderData(): string;
}


//该类将在后面为装饰者实现数据的输入
class Webservice implements RenderableInterface
{
  private $data;

  public function __construct(string $data)
  {
    $this->data = $data;
  }

  public function renderData(): string
  {
    return $this->data;
  }
}


//装饰者必须实现渲染接口类 RenderableInterface 契约，这是该设计模式的关键点
abstract class RendererDecorator implements RenderableInterface
{
  protected $wrapped;

  public function __construct(RenderableInterface $render)
  {
    $this->wrapped = $render;
  }
}

//创建 XML 修饰者并继承抽象类 RendererDecorator
class XmlRenderer extends RendererDecorator
{
  public function renderData(): string 
  {
    $doc = new \DOMDocument();
    $data = $this->wrapped->renderData();
    $doc->appendChild($doc->createElement('content', $data));

    return $doc->saveXML();
  }
}

$service = new Webservice('test');
$xmlService = new XmlRenderer($service);
echo $xmlService->renderData();

