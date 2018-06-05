<?php
#--------------------
# 定义Shop接口类

/**
 *  使用接口（interface），可以指定某个类必须实现哪些方法，但不需要定义这些方法的具体内容。

 *  接口是通过 interface 关键字来定义的，就像定义一个标准的类一样，但其中定义所有的方法都是空的。

 *  接口中定义的所有方法都必须是公有，这是接口的特性。
 * 要实现一个接口，使用 implements 操作符。类中必须实现接口中定义的所有方法，否则会报一个致命错误。
 * 类可以实现多个接口，用逗号来分隔多个接口的名称。
 * 实现多个接口时，接口中的方法不能有重名。
*/
#--------------------

interface InterfaceShop
{
    /** 买方法
     * @param $gid 商品id
     * @return mixed
     */
    public function buy($gid);

    /** 卖方法
     * @param $gid 商品id
     * @return mixed
     */
    public function sell($gid);

    /** 商品价格
     * @param $gid 商品id
     * @return mixed
     */
    public function price($gid);
}
