<?php
//+--------命令行模式--------+
//+------为了封装调用和解耦，有一个调用程序和一个接收器------+
//+------这种模式将方法调用委托给接收器并且呈现相同的方法------+
//+------调用程序只知道调用方法去处理客户端的命令------+


interface CommandInterface
{
  public function execute();
}

/**
 * 这个具体命令，在接收器上调用 ‘print’
 * 但是外部调用者只知道，这个是否可以执行
 */
class HelloCommand implements CommandInterface
{
  private $output;

  //每个具体的命令都来自于不同的接收者
  //这个可以是一个或者多个接收者，但是参数里必须是可以被执行的命令
  public function __construct(Receiver $console)
  {
    $this->output = $console;
  }

  //执行和输出
  public function execute()
  {
    $this->output->write('Hello World');
  }
}


/**
 * 接收方是特定的服务，有自己的 contrat 只能是具体的实例
 */
class Receiver
{
  private $enableDate = false;
  private $output = [];

  public function write(string $str)
  {
    if($this->enableDate){
      $str .= '[' .date('Y-m-d'). ']';
    }
    $this->output[] = $str;
  }

  public function getOutput(): string 
  {
    return join("\n", $this->output);
  }

  public function enableDate()
  {
    $this->enableDate = true;
  }

  public function disableDate()
  {
    $this->enableDate = false;
  }
}


/**
 * 调用者使用这种命令
 */
class Invoker
{
  private $command;

  public function setCommand(CommandInterface $cmd)
  {
    $this->command = $cmd;
  }

  public function run()
  {
    $this->command->execute();
  }
}

$invoker = new Invoker();
$receiver = new Receiver(); 
$receiver->enableDate();
$invoker->setCommand(new HelloCommand($receiver));
$invoker->run();
var_dump($receiver->getOutput());
