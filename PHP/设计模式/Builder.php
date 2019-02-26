<?php
//+-------------建造者是创建一个复杂对象的一部分接口---------------+
//+----有时候，如果建造者对他所创建的东西有较好的知识储备，这个接口就可能成为一个有默认方法的抽象类（又称为适配器）
//+----建造者通常有一个 流式接口 例如：PHPUnit模拟生成器

require 'vendor/autoload.php';

class Door
{

}

class Wheel
{

}

class Engine
{

}

abstract class Vehicle
{
  private $dat = [];

  public function setPart($key, $value)
  {
    $this->data[$key] = $value;
  }
}

class Car extends Vehicle
{

}

class Truck extends Vehicle
{

}

/**
 * Director 类是建造者模式的一部分。它可以实现建造者模式的接口
 * 并在构建器的帮助下构建一个复杂的对象
 */
class Director
{
  public function build(BuilderInterface $builder): Vehicle
  {
    $builder->createVehicle();
    $builder->addDoors();
    $builder->addEngine();
    $builder->addWheel();

    return $builder->getVehicle();
  }
}

interface BuilderInterface
{
  public function createVehicle();
  public function addWheel();
  public function addEngine();
  public function addDoors();
  public function getVehicle(): Vehicle;
}

class TruckBuilder implements BuilderInterface
{
  private $truck;

  public function addDoors()
  {
    $this->truck->setPart('rightDoor', new Door());
    $this->truck->setPart('leftDoor', new Door());
  }

  public function addEngine()
  {
    $this->truck->setPart('truckEngine', new Engine());
  }

  public function addWheel()
  {
    $this->truck->setPart('wheel1', new Wheel());
    $this->truck->setPart('wheel2', new Wheel());
    $this->truck->setPart('wheel3', new Wheel());
    $this->truck->setPart('wheel4', new Wheel());
    $this->truck->setPart('wheel5', new Wheel());
    $this->truck->setPart('wheel6', new Wheel());
  }

  public function createVehicle()
  {
    $this->truck = new Truck();
  }

  public function getVehicle(): Vehicle
  {
    return $this->truck;
  }
}

class CarBuilder implements BuilderInterface
{
  private $car;

  public function addDoors()
  {
    $this->car->setPart('rightDoor', new Door());
    $this->car->setPart('leftDoor', new Door());
    $this->car->setPart('trunkLid', new Door());
  }

  public function addEngine()
  {
    $this->car->setPart('carEngine', new Engine());
  }

  public function addWheel()
  {
    $this->car->setPart('wheel1', new Wheel());
    $this->car->setPart('wheel2', new Wheel());
    $this->car->setPart('wheel3', new Wheel());
    $this->car->setPart('wheel4', new Wheel());
  }

  public function createVehicle()
  {
    $this->car = new Car();
  }

  public function getVehicle(): Vehicle
  {
    return $this->car;
  }
}




class DirectorTest extends \PHPUnit\Framework\TestCase
{
  public function testBuildTruck()
  {
    $truckBuilder = new TruckBuilder();
    $newVehicle = (new Director())->build($truckBuilder);
    $this->assertInstanceOf(Truck::class, $newVehicle);
  }
}

$test = new DirectorTest();
$test->testBuildTruck();

