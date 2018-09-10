<?php
namespace factory;
/**
 * 建造者模式是创建一个复杂对象的一部分接口
 * 有时候，如果建造者对他所创建的东西拥有较好的知识储备，
 * 这个接口就可能成为一个有默认方法的抽象类（又称为适配器）。
 */


/**
 * Director 类是建造者模式的一部分。它可以实现建造者模式的接口
 * 并在构建器的帮助下构建一个复杂的对象
 */
use BuilderInterface;
use Vehicle;

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
	private $struck;

	public function addDoors()
	{
		$this->truck->setPart('rightDoor', new Parts\Door());
		$this->truck->setPart('leftDoor', new Parts\Door());
	}

	public function addEngine()
	{
		$this->struck->setPart('truckEngine', new Parts\Engine());
	}

	public function addWheel()
	{
		$this->truck->setPart('wheel1', new Parts\Wheel());
		$this->truck->setPart('wheel2', new Parts\Wheel());
		$this->truck->setPart('wheel3', new Parts\Wheel());
		$this->truck->setPart('wheel4', new Parts\Wheel());
		$this->truck->setPart('wheel5', new Parts\Wheel());
		$this->truck->setPart('wheel6', new Parts\Wheel());
	}

	public function createVehicle()
	{
		$this->truck = new Parts\Truck();
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
		$this->car->setPart('rightDoor', new Parts\Door());
		$this->car->setPart('leftDoor', new Parts\Door());
		$this->car->setPart('trunkLid', new Parts\Door());
	}

	public function addEngine()
	{
		$this->car->setPart('engine', new Parts\Engine());
	}

	public function addWheel()
	{
		$this->car->setPart('wheelLF', new Parts\Wheel());
		$this->car->setPart('wheelRF', new Parts\Wheel());
		$this->car->setPart('wheelLR', new Parts\Wheel());
		$this->car->setPart('wheelRR', new Parts\Wheel());
	}

	public function createVehicle()
	{
		$this->car = new Parts\Car();
	}

	public function getVehicle(): Vehicle
	{
     	return $this->car;
	}
}


abstract  class Vehicle
{
	private $data = [];

	public function setPart($key, $value)
	{
		$this->data[$key] = $value;
	}
}

class Truck extends Vehicle
{

}

class Car extends Vehicle
{

}

class Engine
{

}

class Wheel
{

}

class Door
{

}
