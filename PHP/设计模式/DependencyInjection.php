<?php
//+--------依赖注入模式----------+
//+------用松散耦合的方式来更好的实现可测试、可维护、可扩展的代码


class DatabaseConfiguration
{
  private $host;
  private $username;
  private $password;
  private $port;

  public function __construct(string $host, string $username, string $password, int $port)
  {
    $this->host = $host;
    $this->username = $username;
    $this->password = $password;
    $this->port = $port;
  }

  public function getHost(): string 
  {
    return $this->host;
  }

  public function getUsername(): string 
  {
    return $this->username;
  }

  public function getPassword(): string 
  {
    return $this->password;
  }

  public function getPort(): int
  {
    return $this->port;
  }
}


class DatabaseConnection
{
  private $configuration;

  public function __construct(DatabaseConfiguration $config)
  {
    $this->configuration = $config;
  }

  public function getDsn(): string
  {
    return sprintf('
      %s:%s@%s:%d', 
      $this->configuration->getUsername(),
      $this->configuration->getPassword(),
      $this->configuration->getHost(),
      $this->configuration->getPort()
    );
  }
}


$config = new DatabaseConfiguration('locahost', 'root', 'root', 3306);
$connection = new DatabaseConnection($config);
echo $connection->getDsn();
