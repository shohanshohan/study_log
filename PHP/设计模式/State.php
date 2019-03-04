<?php
//+---------状态模式--------+
//+------状态模式可以基于一个对象的同种事务而封装出不同的行为-----+
//+------它提供一种简洁的方式使得对象在运行时可以改变自身行为，而不必借助单一庞大的条件判断语句-----+



abstract class StateOrder
{
  private $details;
  protected static $state;

  abstract protected function done();

  protected function setStatus(string $status)
  {
    return $this->details['status'] = $status;
    $this->details['updatedTime'] = time();
  }

  protected function getStatus(): string 
  {
    return $this->details['status'];
  }
}

class ContextOrder extends StateOrder
{
  public function getState(): StateOrder
  {
    return static::$state;
  }

  public function setState(StateOrder $state)
  {
    static::$state = $state;
  }

  public function done()
  {
    static::$state->done();
  }

  public function getStatus(): string 
  {
    return static::$state->getStatus();
  }
}

class ShippingOrder extends StateOrder
{
  public function __construct()
  {
    $this->setStatus('shipping');
  }

  public function done()
  {
    $this->setStatus('completed');
  }
}


class CreateOrder extends StateOrder
{
  public function __construct()
  {
    $this->setStatus('created');
  }

  protected function done()
  {
    static::$state = new ShippingOrder();
  }
}


$order = new CreateOrder();
$contextOrder = new ContextOrder();
$contextOrder->setState($order);
$contextOrder->done();
var_dump($contextOrder->getStatus());
$contextOrder->done();
echo '<br />';
var_dump($contextOrder->getStatus());
