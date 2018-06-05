<?php
#---------------------------
/**
 * Shop接口类的实现
 * 类要实现接口，必须使用和接口中所定义的方法完全一致的方式。否则会导致致命错误。
 * 定义为抽象的类不能被实例化。任何一个类，如果它里面至少有一个方法是被声明为抽象的，那么这个类就必须被声明为抽象的。
 * 被定义为抽象的方法只是声明了其调用方式（参数），不能定义其具体的功能实现。
 * 接口类和抽象类的不同点：
    1。抽象类中可以有非抽象的方法而接口中只能够有抽象的方法！
    2。一个类可以继承多个接口，而一个类只能继承一个抽象类！
    3。接口的使用方式通过implements关键字进行，抽象类则是通过继承extends关键字进行！
    4.接口中不可以声明成员变量（包括类静态变量），但是可以声明类常量。抽象类中可以声明各种类型成员变量，实现数据的封装。
    5、接口没有构造函数，抽象类可以有构造函数。
    6、接口中的方法默认都是public类型的，而抽象类中的方法可以使用private,protected,public来修饰。
 *     这里继承了抽象类，所以也只能用public
 */
#---------------------------
include_once 'InterfaceShop.php';
abstract class AbstractShop implements InterfaceShop
{
    public function buy($gid)
    {
        return '你购买了ID为 :'.$gid.'的商品';
    }
    public function sell($gid)
    {
        echo('你卖了ID为 :'.$gid.'的商品');
    }
    public function price($gid)
    {
        echo('定义了ID为 :'.$gid.'的商品价格');
    }

    public function view($gid){
        echo('你查看了ID为 :'.$gid.'的商品');
    }
}
