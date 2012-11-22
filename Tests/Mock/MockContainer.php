<?php

namespace RickySu\CacheableBehaviorBundle\Tests\Mock;

class MockContainer implements \arrayaccess
{
    protected $Object;

    public function __construct($Object)
    {
        $this->Object = $Object;
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array(array($this->Object, $name), $arguments);
    }

    public function __set($name, $value)
    {
        $this->Object->$name = $value;
    }

    public function __get($name)
    {
        return $this->Object->$name;
    }

    public function __isset($name)
    {
        return isset($this->Object->$name);
    }

    public function __unset($name)
    {
        unset($this->Object->$name);
    }

    public function __toString()
    {
        return "{$this->Object}";
    }

    public function offsetSet($offset, $value)
    {
        $this->Object[$offset]=$value;
    }

    public function offsetExists($offset)
    {
        return isset($this->Object[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->Object[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->Object[$offset];
    }
}
