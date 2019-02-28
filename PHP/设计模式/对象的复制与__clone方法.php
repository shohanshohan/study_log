<?php
//+----对象的复制(拷贝)与__clone()方法-----+
//+----在多数情况下，我们并不需要完全复制一个对象来获得其中属性-----
//+----还有一种情况：如果对象 A 中保存着对象 B 的引用，当你复制对象 A 时，你想其中使用的对象不再是对象 B 而是 B 的一个副本，那么你必须得到对象 A 的一个副本. 这个也好理解，就是重写__clone()方法让 引用的属性值实例化新的B对象

class A
{
  public $a=0;
  public $b=1;
  public $c=3;

  public function printItem()
  {
    echo 'a:' . $this->a . ' b:' . $this->b . ' c:' . $this->c;
    echo '<br>';
  }
}

$a1 = new A();
//php的对象是以一个标识符来存储的，所以对对象的直接“赋值”行为相当于“传引用”
$a2 = $a1;
$a1->c = 5;
//$a4是对$a1的浅拷贝，赋值之后的$a4不会再随着$a1变化，但赋值之前$a1的动作产生的结果依然有效
$a4 = clone $a1;
$a3 = new A();

//作为对象标识符的#n，显示$a1和$a2其实是指向同一个对象，而$a3是另一个对象
var_dump($a1);
echo '<br>';
var_dump($a2);
echo '<br>';
var_dump($a3);
echo '<br>';
var_dump($a4);
echo '<br>';

//$a1=$a2, 当$a1对象属性变化时$a2的属性也跟着变化，反之同理
$a1->a = 1;
$a2->b = 2;
echo $a1->printItem() . '<br>';
echo $a2->printItem() . '<br>';
echo $a3->printItem() . '<br>'; //$a3是不同的对象，所以属性值不变
echo $a4->printItem() . '<br>';


//再来看看下面的‘深’拷贝的例子
//使用clone关键字的时候，会自动调用旧对象的__clone()方法(然后返回拷贝的对象)，所以只需要在对应的类中重写__clone()方法
class B
{
  public $a=7;
  public $b=8;
  public $c=9;

  public function printItem()
  {
    echo 'a:' . $this->a . ' b:' . $this->b . ' c:' . $this->c;
    echo '<br>';
  }

  public function __clone()
  {
    $this->b = 8;
  }
}

$b1 = new B();
$b1->b = 6;
$b2 = clone $b1; //会发现即使克隆前$b1让属性b的值变化了，但是$b2克隆$b1后属性b的值会按重写规则定义
echo $b1->printItem() . '<br>';
echo $b2->printItem() . '<br>';
