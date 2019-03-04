<?php
//+--------备忘录模式--------+
//+------提供了在不破坏封装的情况下恢复到之前状态（使用回滚）或者获取对象的内部状态------+
//+------备忘录模式使用3个类来实现：Originator(发起人), Caretaker(守护看管), Mementor(备忘录)------+
//
//+------Memento 它负责存储 Originator 的唯一内部状态，它可以包含string, number, array, 类的实例等等。
//Memento 不是公开的类，任何人都不应该且不能更改它，并防止 Originator 以外的对象访问它------+
//
//+-----Originator 它负责创建 Memento, 并记录外部对象当前时刻的状态，并可使用Memento恢复内部状态
//Originator 可根据需要决定Memento存储Originator的哪些内部状态，但是它们不能更改已保存对象的当前状态------+
//
//+-----Caretaker 负责保存Memento。它可以修改一个对象，决定Originator中对象当前时刻的状态、从Originator获取
//对象的当前状态、或者回滚Originator中对象的状态-------+


class Memento
{
  private $state;

  public function __construct(State $stateToSave)
  {
    $this->state = $stateToSave;
  }

  public function getState()
  {
    return $this->state;
  }
}


class State
{
  const STATE_CREAED = 'created';
  const STATE_OPENED = 'opend';
  const STATE_ASSIGNED = 'assigned';
  const STATE_CLOSED = 'closed';

  private $state;

  private static $valiStates = [
    self::STATE_CREAED,
    self::STATE_OPENED,
    self::STATE_ASSIGNED,
    self::STATE_CLOSED,
  ];


  public function __construct(string $state)
  {
    self::ensureIsValidState($state);
    $this->state = $state;
  }

  private static function ensureIsValidState(string $state)
  {
    if(!in_array($state, self::$valiStates)){
      return die('Invalid state given');
    }
  }

  public function __toString(): string 
  {
    return $this->state;
  }
}


class Ticket
{
  private $currentState;

  public function __construct()
  {
    $this->currentState = new State(State::STATE_CREAED);
  }

  public function open()
  {
    $this->currentState = new State(State::STATE_OPENED);
  }

  public function assign()
  {
    $this->currentState = new State(State::STATE_ASSIGNED);
  }

  public function close()
  {
    $this->currentState = new State(State::STATE_CLOSED);
  }

  public function saveToMemento(): Memento 
  {
    return new Memento(clone $this->currentState);
  }

  public function restoreFromMemento(Memento $memento)
  {
    $this->currentState = $memento->getState();
  }

  public function getState(): State 
  {
    return $this->currentState;
  }
}


$ticket = new Ticket();
$ticket->open();
$openedState = $ticket->getState();
var_dump($openedState);echo '<br>';

$memento = $ticket->saveToMemento(); //保存open之时的状态
$ticket->assign();
var_dump($ticket->getState());echo '<br>';
$ticket->restoreFromMemento($memento); //恢复assign之前的状态即open状态
var_dump($ticket->getState());
