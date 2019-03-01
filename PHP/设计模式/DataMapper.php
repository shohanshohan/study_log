<?php
//+---------数据映射模式-----------+
//+-------数据映射器是一种数据访问层，它执行持久性数据存储（通常是关系数据库）和内存数据表示（域层）之间的数据双向传输-----+
//+-------该模式的目标是保持内存表示和持久数据存储相互独立，并保持数据映射器本身---------+
//+-------该层由一个或多个映射器（或数据访问对象）组成，执行数据传输-------+
//+-------通用映射器将处理许多不同的域实体类型，专用映射器将处理一个或几个--------+
//+-------这种模式的关键点在于，与活动记录模式不同，数据模型遵循单一责任原则--------+


class User
{
  private $username;
  private $email;

  public function __construct(string $username, string $email)
  {
    $this->username = $username;
    $this->email = $email;
  }

  public static function fromState(array $state): User
  {
    //验证状态
    //............ if $state['status'] != 0;
    
    return new self($state['username'], $state['email']);
  }

  public function getUsername()
  {
    return $this->username;
  }

  public function getEmail()
  {
    return $this->email;
  }
}


class UserMapper
{
  private $adapter;

  public function __construct(StorageAdapter $storage)
  {
    $this->adapter = $storage;
  }

  //根据id从存储器中找到用户，并返回一个用户对象
  public function findById(int $id): User 
  {
    $result = $this->adapter->find($id);
    if($result === null){
      die('User #$id not found');
    }
    return $this->mapRowToUser($result);
  }

  private function mapRowToUser(array $row): User
  {
    return User::fromState($row);
  }
}

class StorageAdapter
{
  private $data = [];

  public function __construct(array $data)
  {
    $this->data = $data;
  }

  public function find(int $id)
  {
    if(isset($this->data[$id])){
      return $this->data[$id];
    }
    return null;
  }
}

$storage = new StorageAdapter([1 => ['username'=>'testname', 'email'=>'testemail']]);
$mapper = new UserMapper($storage);//这一步其实就是把上面的对象（用户属性）保存到新的对象属性中
$user = $mapper->findById(1);//这步是通过$storage->find($id)查找到用户属性数组，如果存在则用User对象实例化并返回User对象
var_dump($user); //返回包含用户属性的User对象
