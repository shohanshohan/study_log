<?php
//+--------中介者模式--------+
//+------本模式提供了一种轻松的多组件之间弱耦合的协同方式------+
//+------所有关联协同的组件仅与中介者建立耦合，这是该模式的重要特性------+


/**
 * MediatorInterface 接口为 Mediator 类建立契约
 */
interface MediatorInterface
{
  public function sendResponse($content);

  public function makeRequest();

  public function queryDb();
}

class Mediator implements MediatorInterface
{
  private $server;
  private $database;
  private $client;

  public function __construct(Database $database, Client $client, Server $server)
  {
    $this->database = $database;
    $this->server = $server;
    $this->client = $client;

    $this->database->setMediator($this);
    $this->server->setMediator($this);
    $this->client->setMediator($this);
  }

  public function makeRequest()
  {
    $this->server->process();
  }

  public function queryDb(): string
  {
    return $this->database->getData();
  }

  public function sendResponse($content)
  {
    $this->client->output($content);
  }
}



/**
 * Colleague 类是个抽象类，该类对象虽彼此协同却不知彼此，只知中介者 Mediator 类
 */
abstract class Colleague
{
  protected $mediator;

  public function setMediator(MediatorInterface $mediator)
  {
    $this->mediator = $mediator;
  }
}

/**
 * Client 类是一个发出请求并获得响应的客户端
 */
class Client extends Colleague
{
  public function request()
  {
    $this->mediator->makeRequest();
  }

  public function output(string $content)
  {
    echo $content;
  }
}

/**
 * 获取数据类
 */
class Database extends Colleague
{
  public function getData(): string
  {
    return 'World';
  }
}

class Server extends Colleague
{
  public function process()
  {
    $data = $this->mediator->queryDb();
    $this->mediator->sendResponse(sprintf("Hello %s", $data));
  }
}



$client = new Client();
$mediator = new Mediator(new Database(), $client, new Server());
$client->request();
