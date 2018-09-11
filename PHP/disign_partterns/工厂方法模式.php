<?php
namespace factory\method;
/**
 * 工厂方法模式，可以将其子类用不同的方法来创建一个对象
 * 工厂方法模式取决于抽象类，而不是具体的类。这是与简单工厂模式和静态工厂模式相比的优势
*/

abstract class FactoryMethod
{
	const CHEAP = 'cheap';
	const FAST  = 'fast';

	abstract protected function createVehicle(string $type): VehicleInterface;

	public function create(string $type): VehicleInterface
	{
		$obj = $this->createVehicle($type);
		$obj->setColor('black');
		return $obj;
	}
}


class ItalianFactory extends FactoryMethod
{
	protected function createVehicle(): VehicleInterface
	{
		switch ($type) {
			case parent::CHEAP:
				return new Bicycle();
			case patent::FAST:
				return new CarFerrari();
			default:
				throw new \Exception("$type is not a valid vehicle");
		}
	}
}


class GermanFactory extends FactoryMethod
{
	protected function createVehicle(string $type): VehicleInterface
	{
		switch ($type) {
			case parent::CHEAP:
				return new Bicycle();
			case patent::FAST:
				$carMercedes = new CarMercedes();
				$carMercedes->addAMGTuning();
				return $carMercedes;
			default:
				throw new \Exception("$type is not a valid vehicle");
		}
	}
}


interface VehicleInterface
{
	public function setColor(string $rgb);
}


class CarMercedes implements VehicleInterface
{
	private $color;

	public function setColor(string $rgb)
	{
		$this->color = $rgb;
	}

	public function addAMGTuning()
	{

	}
}

class CarFerrari implements VehicleInterface
{
	private $color;

	public function setColor(string $rgb)
	{
		$this->color = $rgb;
	}
}


class Bicycle implements VehicleInterface
{
	private $color;

	public function setColor(string $rgb)
	{
		$this->color = $rgb;
	}
}
