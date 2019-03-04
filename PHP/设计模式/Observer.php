<?php
//+--------观察者模式---------+
//+-----当对象的状态发生变化时，所有依赖于它的对象都得到通知并被自动更新-----+
//+-----PHP 中已经定义了2个接口用于快速实现观察者模式：SplObserver和SplSubject

class User implements \SplSubject
{
  private $email;
  private $observers;

  public function __construct()
  {
    $this->observers = new \SplObjectStorage();
  }

  public function attach(\SplObserver $observer)
  {
    $this->observers->attach($observer);//添加观察者
  }

  public function detach(\SplObserver $observer)
  {
    $this->observers->detach($observer);//删减观察者
  }

  public function notify()
  {
    foreach ($this->observers as $observer) {
      $observer->update($this);//循环通知每一个观察者
    }
  }

  //触发事件
  public function changeEmail(string $email)
  {
    $this->email = $email;
    $this->notify();//通知观察者
  }
}

//观察者
class UserObserver implements \SplObserver 
{
  private $changedUsers = [];

  public function update(\SplSubject $subject)
  {
    echo '我已接收到通知<br />';
    $this->changedUsers[] = clone $subject;
  }

  public function getChangedUsers(): array 
  {
    return $this->changedUsers;
  }
}


$observer = new UserObserver();
$user = new User();
$user->attach($observer);
var_dump($user);
echo '<br>';
$user->changeEmail('foo@bar.com');
var_dump($observer->getChangedUsers());
