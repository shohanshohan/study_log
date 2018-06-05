<?php
#---------------------
/**
 * 继承抽象类
 * 继承一个抽象类的时候，子类必须定义父类中的所有抽象方法；另外，这些方法的访问控制必须和父类中一样（或者更为宽松）。
 * 例如某个抽象方法被声明为受保护的，那么子类中实现的方法就应该声明为受保护的或者公有的，而不能定义为私有的。
 */
#---------------------
include_once 'AbstractShop.php';
class MyShop extends AbstractShop
{
    public function today($gid=1)
    {
        echo 'today,'.$this->buy($gid);
    }
}

$myShop = new MyShop;
$myShop->today(2);
