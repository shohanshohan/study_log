<?php
//+--------模板方法模式--------+
//+-----这是一种行为型的设计模式。一种让抽象模板的子类（完成）一系列算法的行为策略-----+
//+-----这个类不是由子类调用的，而是以相反的方式-----+
//+-----它是一种非常适合框架库的算法骨架。用户只需要实现子类的一种方法，其父类便可去搞定这项工作了-----+
//+-----这是一种分离具体类的简单办法，且可以减少复制粘贴-----+

abstract class Journey
{
  private $thingsToDo = [];

  /**
   * 这是当前类及其子类提供的公共服务
   * 注意：它冻结了全局的算法行为（private）
   * 如果想重写这个契约，只需要实现一个包含 takeTrip() 方法的接口
   */
  final public function takeTrip()
  {
    $this->thingsToDo[] = $this->buyAFlight();
    $this->thingsToDo[] = $this->takePlane();
    $this->thingsToDo[] = $this->enjoyVacation();
    $buyGift = $this->buyGift();
    if($buyGift !== null  ){
      $this->thingsToDo[] = $buyGift;
    }
    
    $this->thingsToDo[] = $this->takePlane();
  }

  /**
   * 这个方法必须要实现，它是这个模式的关键点
   */
  abstract protected function enjoyVacation(): string;

  /**
   * 这个方法是可选的，也可能作为算法的一部分
   * 如果需要的话可以重写它
   */
  protected function buyGift()
  {
    return null;
  }

  private function buyAFlight(): string 
  {
    return 'Buy a flight ticket';
  }

  private function takePlane(): string 
  {
    return 'Taking the plane';
  }

  public function getThingsToDo(): array 
  {
    return $this->thingsToDo;
  }
}

class BeachJourney extends Journey
{
  protected function enjoyVacation(): string 
  {
    return 'Swimming and sun-bathing';
  }
}

class CityJourney extends Journey
{
  protected function enjoyVacation(): string 
  {
    return 'Eat, drink, take photos and sleep';
  }

  protected function buyGift(): string 
  {
    return 'Buy a gift';
  }
}

$beachJourney = new BeachJourney();
$beachJourney->takeTrip();
print_r($beachJourney->getThingsToDo());
echo '<br />';
$cityJourney = new CityJourney();
$cityJourney->takeTrip();
print_r($cityJourney->getThingsToDo());
