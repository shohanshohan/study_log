<?php

//数组式访问接口
class Obj implements arrayaccess
{
    private $container = [];

    public function __construct()
    {
        $this->container = ['one'=>1, 'two'=>2, 'three'=>3];
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }
}

$obj = new Obj();
$obj->offsetSet('test', 999);

var_dump($obj['test']);
var_dump($obj['two']);
