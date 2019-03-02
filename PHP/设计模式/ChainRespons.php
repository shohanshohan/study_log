<?php
//+--------责任链模式---------+
//+------建立一个对象链来按指定顺序调用---------+
//+------如果其中一个对象无法处理命令，它会委托这个调用给它的下一个对象来进行处理，以此类推------+
//+------例如：日志框架，垃圾邮件过滤器------------+

require 'vendor/autoload.php';

abstract class Handler 
{
  private $successor = null;

  public function __construct(Handler $handler=null)
  {
    $this->successor = $handler; //定义处理器
  }

  final public function handle(RequestInterface $request)
  {
    $processed = $this->processing($request);
    if($processed === null){
      //请求尚未被目前的处理器处理则传递到下一个处理器
      if($this->successor !== null){
        $processed = $this->successor->handle($request);
      }
    }
    return $processed;
  }

  //声明处理方法
  abstract protected function processing(RequestInterface $request);
}


/**
 * 创建 http 缓存处理类
 */
class HttpInMemoryCacheHandler extends Handler
{
  private $data;

  public function __construct(array $data, Handler $successor=null)
  {
    parent::__construct($successor);
    $this->data = $data;
  }

  //返回缓存中对应路径存储的数据
  protected function processing(RequestInterface $request)
  {
    $key = sprintf('%s?%s', $request->getPath(), $request->getQuery());
    if($request->getMethod() == 'GET' && isset($this->data[$key])){
      return $this->data[$key];
    }
    return null;
  }
}


/**
 * 创建数据库处理器
 */
class SlowDatabaseHandler extends Handler
{
  protected function processing(RequestInterface $request)
  {
    //模拟输出
    return 'Hello World!';
  }
}


interface RequestInterface
{
  public function getMethod();

  public function getPath();

  public function getQuery();
}

class Request implements RequestInterface
{
  public function getMethod()
  {
    return $_SERVER['REQUEST_METHOD'];
  }

  public function getPath()
  {
    return '/foo/bar';
    //return $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
  }

  public function getQuery()
  {
    //return 'index=1';
    return $_SERVER['QUERY_STRING'];
  }
}

$obj = new HttpInMemoryCacheHandler(['/foo/bar?index=1'=>'Hello in memory'], new SlowDatabaseHandler());
$res = $obj->handle(new Request());
var_dump($res);
