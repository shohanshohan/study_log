<?php
//+---------规格模式----------+
//+-----构建一个清晰的业务规则规范，其中每条规则都能被针对性地检查------+
//+-----判断给定的规则是否满足规范从而返回true或false-------+

class Item 
{
  private $price;

  public function __construct(float $price)
  {
    $this->price = $price;
  }

  public function getPrice(): float
  {
    return $this->price;
  }
}


interface SpecificationInterface
{
  public function isSatisfiedBy(Item $item): bool;
}

class OrSpecification implements SpecificationInterface
{
  private $specifications;

  public function __construct(SpecificationInterface ...$specifications)
  {
    $this->specifications = $specifications;
  }

  //如果有一条规则符合条件，返回true,否则返回false
  public function isSatisfiedBy(Item $item): bool
  {
    foreach ($this->specifications as $specification) {
      if($specification->isSatisfiedBy($item)){
        return true;
      }
    }
    return false;
  }
}

class AndSpecification implements SpecificationInterface
{
  private $specifications;

  public function __construct(SpecificationInterface ...$specifications)
  {
    $this->specifications = $specifications;
  }

  //如果有一条规则符合条件，返回true,否则返回false
  public function isSatisfiedBy(Item $item): bool
  {
    foreach ($this->specifications as $specification) {
      if($specification->isSatisfiedBy($item)){
        return false;
      }
    }
    return true;
  }
}


class PriceSpecification implements SpecificationInterface
{
  private $maxPrice;
  private $minPrice;

  public function __construct($minPrice, $maxPrice)
  {
    $this->minPrice = $minPrice;
    $this->maxPrice = $maxPrice;
  }

  public function isSatisfiedBy(Item $item): bool 
  {
    if($this->maxPrice !== null && $item->getPrice() > $this->maxPrice){
      return false;
    }

    if($this->minPrice !== null && $item->getPrice() < $this->minPrice){
      return false;
    }
    return true;
  }
}


class NotSpecification implements SpecificationInterface
{
  private $specification;

  public function __construct(SpecificationInterface $specification)
  {
    $this->specification = $specification;
  }

  public function isSatisfiedBy(Item $item): bool
  {
    return !$this->specification->isSatisfiedBy($item);
  }
}



$spec1 = new PriceSpecification(50, 99);
$spec2 = new PriceSpecification(100, 200);
$orSpec = new OrSpecification($spec1, $spec2);
$andSpec = new AndSpecification($spec1, $spec2);
var_dump($orSpec->isSatisfiedBy(new Item(100)));
echo '<br />';
var_dump($andSpec->isSatisfiedBy(new Item(100)));
