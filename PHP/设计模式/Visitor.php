<?php
//+--------访问者模式--------+
//+-----访问者模式可以让你将对象操作外包给其它对象------+
//+-----这样做的主要原因就是关注（数据结构和数据操作）分离-----+
//+-----但是被访问的类必须定一个契约接受访问者，契约可以是一个抽象类也可直接是一个接口-----+


/**
 * 注意：访问者不能自行选择调用哪个方法
 */
interface RoleVisitorInterface
{
  public function visitUser(User $role);

  public function visitGroup(Group $role);
}


class RoleVisitor implements RoleVisitorInterface
{
  private $visited = [];

  public function visitGroup(Group $role)
  {
    $this->visited[] = $role;
  }

  public function visitUser(User $role)
  {
    $this->visited[] = $role;
  }

  public function getVisited(): array 
  {
    return $this->visited;
  }
}



interface Role 
{
  public function accept(RoleVisitorInterface $visitor);
}

class User implements Role 
{
  private $name;

  public function __construct(string $name)
  {
    $this->name = $name;
  }

  public function getName(): string 
  {
    return sprintf("User %s", $this->name);
  }

  public function accept(RoleVisitorInterface $visitor)
  {
    $visitor->visitUser($this);
  }
}

class Group implements Role 
{
  private $name;

  public function __construct(string $name)
  {
    $this->name = $name;
  }

  public function getName(): string 
  {
    return sprintf("User %s", $this->name);
  }

  public function accept(RoleVisitorInterface $visitor)
  {
    $visitor->visitGroup($this);
  }
}



$visitor = new RoleVisitor();
$userRole = new User('username');
$userRole->accept($visitor);
$groupRole = new Group('groupName');
$groupRole->accept($visitor);
var_dump($visitor->getVisited());


