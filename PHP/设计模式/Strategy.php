<?php
//+--------策略模式--------+
//+------分离（策略）并使他们之间能互相快速切换------+
//+------此外，这种模式是一种不错的继承替代方案（替代使用扩展抽象类的方式）



class Context
{
  private $comparator;

  public function __construct(ComparatorInterface $comparator)
  {
    $this->comparator = $comparator;
  }

  public function executeStrategy(array $elements): array 
  {
    uasort($elements, [$this->comparator, 'compare']);
    return $elements;
  }
}


interface ComparatorInterface
{
  public function compare($a, $b): int;
}

class DateComparator implements ComparatorInterface
{
  public function compare($a, $b): int 
  {
    $aDate = strtotime($a['date']);
    $bDate = strtotime($b['date']);
    return $aDate <=> $bDate;
  }
}

class IdComparator implements ComparatorInterface
{
  public function compare($a, $b): int
  {
    return $a['id'] <=> $b['id'];
  }
}


$idData = [
  ['id'=>2],
  ['id'=>6],
  ['id'=>3],
  ['id'=>8],
  ['id'=>9],
];

$dateData = [
  ['date' => '2019-03-05'],
  ['date' => '2019-03-08'],
  ['date' => '2019-03-03'],
  ['date' => '2019-03-06'],
  ['date' => '2019-03-01'],
];

$context = new Context(new IdComparator());
$data = $context->executeStrategy($idData);
var_dump($data);
$context2 = new Context(new DateComparator());
$data2 = $context2->executeStrategy($dateData);
echo '<br />';
var_dump($data2);
