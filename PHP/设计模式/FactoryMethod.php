<?php
//+---------工厂方法模式---------+
//+---------对比简单工厂模式的优点是，可以将其子类用不同的方法来创建一个对象
//+---------这个抽象类可能只是一个接口，实现了【依赖倒置】原则
//+---------这意味着工厂方法模式取决于抽象类，而不是具体的类。
//+---------这是与简单工厂模式和静态工厂模式相比的优势

require 'vendor/autoload.php';


interface Logger
{
  public function log(string $message);
}

class StdoutLogger implements Logger
{
  public function log(string $message)
  {
    echo $message;
  }
}

class FileLogger implements Logger
{
  private $filePath;

  public function __construct(string $filePath)
  {
    $this->filePath = $filePath;
  }

  public function log(string $message)
  {
    file_put_contents($this->filePath, $message . PHP_EOL, FILE_APPEND);
  }
}

interface LoggerFactory
{
  public function createLogger(): Logger;
}

class StdoutLoggerFactory implements LoggerFactory
{
  public function createLogger(): Logger
  {
    return new StdoutLogger();
  }
}

class FileLoggerFactory implements LoggerFactory
{
  private $filePath;

  public function __construct(string $filePath)
  {
    $this->filePath = $filePath;
  }

  public function createLogger(): Logger
  {
    return new FileLogger($this->filePath);
  }
}


//测试
class FactoryMethodTest extends \PHPUnit\Framework\TestCase
{
  public function testCreateStdoutLogger()
  {
    $loggerFactory = new StdoutLoggerFactory();
    $logger = $loggerFactory->createLogger();
    $logger->log('测试');
    $this->assertInstanceOf(StdoutLogger::class, $logger);
  }
}

$test = new FactoryMethodTest();
$test->testCreateStdoutLogger();
