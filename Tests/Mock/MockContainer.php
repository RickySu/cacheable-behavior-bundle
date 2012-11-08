<?php

namespace RickySu\CacheableBehaviorBundle\Tests\Mock;

class MockContainer {

    protected $Object;

    public function __construct($Object) {
        $this->Object = $Object;
    }

    public function __call($name, $arguments) {
        return call_user_func_array(array($this->Object, $name), $arguments);
    }

    public function __set($name, $value) {
        $this->Object->$name = $value;
    }

    public function __get($name) {
        return $this->Object->$name;
    }

    public function __isset($name) {
        return isset($this->Object->$name);
    }

    public function __unset($name) {
        unset($this->Object->$name);
    }

}
